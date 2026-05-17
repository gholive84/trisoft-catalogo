<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\OrderRepository;
use App\Services\MailService;

final class OrderController
{
    private View $view;
    private OrderRepository $orders;
    private MailService $mail;
    private \PDO $pdo;

    public function __construct()
    {
        $this->view   = new View(base_path('templates'));
        $this->orders = new OrderRepository();
        $this->mail   = new MailService();
        $this->pdo    = Database::connection();
    }

    public function index(Request $request): string
    {
        $status  = (string) $request->query('status', '');
        $q       = trim((string) $request->query('q', ''));
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = 25;
        $offset  = ($page - 1) * $perPage;

        $where  = "1=1";
        $params = [];
        if ($status !== '') { $where .= " AND o.status = :st"; $params['st'] = $status; }
        if ($q !== '') {
            // MySQL native prepares exigem placeholder unico por ocorrencia
            $where .= " AND (o.order_number LIKE :q1 OR u.name LIKE :q2 OR u.email LIKE :q3)";
            $like = "%{$q}%";
            $params['q1'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
        }

        $sql = "SELECT o.*, u.name AS customer_name, u.email AS customer_email
                  FROM orders o
                  JOIN users u ON u.id = o.user_id
                 WHERE {$where}
              ORDER BY o.created_at DESC
                 LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM orders o JOIN users u ON u.id = o.user_id WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // KPIs por status (para mostrar contadores nos filtros)
        $kpis = [];
        $rows = $this->pdo->query("SELECT status, COUNT(*) AS n FROM orders GROUP BY status")->fetchAll();
        foreach ($rows as $r) {
            $kpis[$r['status']] = (int) $r['n'];
        }

        return $this->view->render('admin/orders/index', [
            'title'    => 'Orçamentos',
            'items'    => $items,
            'total'    => $total,
            'kpis'     => $kpis,
            'status'   => $status,
            'q'        => $q,
            'page'     => $page,
            'lastPage' => max(1, (int) ceil($total / $perPage)),
            'perPage'  => $perPage,
        ]);
    }

    public function show(Request $request): string
    {
        $id = (int) $request->param('id');
        $order = $this->orders->findById($id);
        if ($order === null) Response::abort(404);

        // Dados do cliente
        $cust = $this->pdo->prepare("SELECT id, name, email, phone, document, company_name FROM users WHERE id = ?");
        $cust->execute([$order['user_id']]);
        $customer = $cust->fetch();

        return $this->view->render('admin/orders/show', [
            'title'    => 'Orçamento ' . $order['order_number'],
            'order'    => $order,
            'items'    => $this->orders->itemsOf($id),
            'customer' => $customer ?: ['name' => '—', 'email' => '—'],
        ]);
    }

    /**
     * Vendedor responde com preços ajustados. Calcula totais e envia email.
     */
    public function respond(Request $request): never
    {
        $id = (int) $request->param('id');
        $order = $this->orders->findById($id);
        if ($order === null) Response::abort(404);

        // Atualiza unit_price e quantidade de cada item (e total da linha)
        $itemPrices = (array) $request->post('unit_price', []);
        $itemQty    = (array) $request->post('quantity', []);

        $this->pdo->beginTransaction();
        try {
            $subtotal = 0.0;
            foreach ($itemPrices as $itemId => $priceStr) {
                $unit = (float) str_replace(',', '.', (string) $priceStr);
                $qty  = max(1, (int) ($itemQty[$itemId] ?? 1));
                $line = $unit * $qty;
                $this->pdo->prepare(
                    "UPDATE order_items SET unit_price = :u, quantity = :q, total = :t
                      WHERE id = :id AND order_id = :oid"
                )->execute([
                    'u' => $unit, 'q' => $qty, 't' => $line,
                    'id' => (int) $itemId, 'oid' => $id,
                ]);
                $subtotal += $line;
            }

            $discount = (float) str_replace(',', '.', (string) $request->post('discount', '0'));
            $shipping = (float) str_replace(',', '.', (string) $request->post('shipping_cost', '0'));
            $total    = max(0.0, $subtotal - $discount + $shipping);

            $expDays  = (int) $request->post('expires_in_days', 15);
            $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expDays} days"));

            $internalNotes = trim((string) $request->post('internal_notes', ''));
            $customerNotes = trim((string) $request->post('customer_notes', $order['customer_notes'] ?? ''));

            $newStatus = $request->post('action') === 'save_draft' ? $order['status'] : 'quoted';

            $this->orders->updateStatus($id, $newStatus, [
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'shipping_cost'  => $shipping,
                'total'          => $total,
                'expires_at'     => $expiresAt,
                'internal_notes' => $internalNotes ?: null,
                'customer_notes' => $customerNotes ?: null,
                'quoted_by_user_id' => (int) Auth::id(),
                'quoted_at'      => date('Y-m-d H:i:s'),
            ]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            Session::flash('error', 'Falha ao responder: ' . $e->getMessage());
            Response::redirect(url("admin/orcamentos/{$id}"));
        }

        if ($newStatus === 'quoted') {
            $this->sendQuoteRespondedEmail($id);
            Session::flash('success', 'Orçamento respondido e email enviado ao cliente.');
        } else {
            Session::flash('success', 'Rascunho salvo.');
        }
        Response::redirect(url("admin/orcamentos/{$id}"));
    }

    private function sendQuoteRespondedEmail(int $orderId): void
    {
        $order = $this->orders->findById($orderId);
        if ($order === null) return;
        $items = $this->orders->itemsOf($orderId);
        $cust  = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $cust->execute([$order['user_id']]);
        $user = $cust->fetch();
        if (!$user) return;

        $this->mail->send(
            (string) $user['email'],
            "Seu orçamento {$order['order_number']} está pronto",
            'quote_responded',
            ['order' => $order, 'items' => $items, 'user' => $user]
        );
    }

    public function cancel(Request $request): never
    {
        $id = (int) $request->param('id');
        $this->orders->updateStatus($id, 'canceled', ['canceled_at' => date('Y-m-d H:i:s')]);
        Session::flash('success', 'Orçamento cancelado.');
        Response::redirect(url("admin/orcamentos/{$id}"));
    }
}

<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f4f5; margin:0; padding:24px; color:#111;">
  <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="background:#ffffff; border-radius:12px; overflow:hidden; max-width:600px;">
    <tr>
      <td style="padding:24px; background:#0071A2; color:#ffffff;">
        <h1 style="margin:0; font-size:18px;">Seu orçamento está pronto</h1>
      </td>
    </tr>
    <tr>
      <td style="padding:24px;">
        <h2 style="margin:0 0 12px; font-size:20px;">Olá, <?= e($user['name']) ?>!</h2>
        <p style="margin:0 0 16px; line-height:1.5;">
          Sua proposta personalizada para o orçamento <strong><?= e($order['order_number']) ?></strong> está pronta.
        </p>

        <table cellpadding="8" cellspacing="0" border="0" width="100%" style="border:1px solid #e5e7eb; border-radius:8px; margin:16px 0;">
          <thead>
            <tr style="background:#f9fafb; text-align:left;">
              <th style="font-size:12px; text-transform:uppercase; color:#6b7280; padding:8px;">Item</th>
              <th style="font-size:12px; text-transform:uppercase; color:#6b7280; padding:8px; text-align:right;">Qtd</th>
              <th style="font-size:12px; text-transform:uppercase; color:#6b7280; padding:8px; text-align:right;">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $i):
                $snap = is_array($i['product_snapshot']) ? $i['product_snapshot'] : (json_decode((string) $i['product_snapshot'], true) ?: []);
            ?>
              <tr style="border-top:1px solid #e5e7eb;">
                <td style="padding:8px;">
                  <strong><?= e($snap['name'] ?? '—') ?></strong>
                </td>
                <td style="padding:8px; text-align:right;"><?= e((string) $i['quantity']) ?></td>
                <td style="padding:8px; text-align:right;"><?= e(money_br((float) $i['total'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr style="background:#f9fafb;">
              <td colspan="2" style="padding:8px; text-align:right; font-weight:bold;">Total:</td>
              <td style="padding:8px; text-align:right; font-weight:bold; font-size:16px;"><?= e(money_br((float) $order['total'])) ?></td>
            </tr>
          </tfoot>
        </table>

        <?php if (!empty($order['customer_notes'])): ?>
          <div style="background:#fef3c7; padding:12px; border-radius:8px; margin:16px 0;">
            <strong style="display:block; margin-bottom:4px;">Mensagem do vendedor:</strong>
            <?= nl2br(e($order['customer_notes'])) ?>
          </div>
        <?php endif; ?>

        <?php if ($order['expires_at']): ?>
          <p style="margin:0 0 16px; font-size:14px; color:#6b7280;">
            Válido até: <strong><?= e(date_br($order['expires_at'], 'd/m/Y')) ?></strong>
          </p>
        <?php endif; ?>

        <p style="margin:24px 0; text-align:center;">
          <a href="<?= e(\App\Core\Config::appUrl() . '/minha-conta/orcamentos/' . $order['order_number']) ?>"
             style="background:#0071A2; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:8px; display:inline-block;">
            Ver orçamento completo
          </a>
        </p>
      </td>
    </tr>
  </table>
</body>
</html>

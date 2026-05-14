<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f4f5; margin:0; padding:24px; color:#111827;">
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="background:#ffffff; border-radius:12px; overflow:hidden; max-width:600px;">
    <tr>
      <td style="padding:24px; background:#111827; color:#ffffff;">
        <h1 style="margin:0; font-size:18px; font-weight:bold;">Catálogo Trisoft</h1>
      </td>
    </tr>
    <tr>
      <td style="padding:24px;">
        <h2 style="margin:0 0 12px; font-size:20px;">Olá, <?= e($user['name']) ?>!</h2>
        <p style="margin:0 0 16px; line-height:1.5;">
          Recebemos seu pedido de orçamento <strong><?= e($order['order_number']) ?></strong>.
        </p>
        <p style="margin:0 0 16px; line-height:1.5;">
          Nossa equipe entrará em contato em até 1 dia útil com os preços e condições.
        </p>

        <table cellpadding="8" cellspacing="0" border="0" width="100%" style="border:1px solid #e5e7eb; border-radius:8px; margin:16px 0;">
          <thead>
            <tr style="background:#f9fafb; text-align:left;">
              <th style="font-size:12px; text-transform:uppercase; color:#6b7280; padding:8px;">Item</th>
              <th style="font-size:12px; text-transform:uppercase; color:#6b7280; padding:8px; text-align:right;">Qtd</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $i):
                $snap = is_array($i['product_snapshot']) ? $i['product_snapshot'] : (json_decode((string) $i['product_snapshot'], true) ?: []);
            ?>
              <tr style="border-top:1px solid #e5e7eb;">
                <td style="padding:8px;">
                  <strong style="display:block;"><?= e($snap['name'] ?? '—') ?></strong>
                  <small style="color:#6b7280;">SKU <?= e($snap['sku'] ?? '') ?></small>
                </td>
                <td style="padding:8px; text-align:right;"><?= e((string) $i['quantity']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <p style="margin:24px 0; text-align:center;">
          <a href="<?= e(\App\Core\Config::appUrl() . '/minha-conta/orcamentos/' . $order['order_number']) ?>"
             style="background:#111827; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:8px; display:inline-block;">
            Ver orçamento
          </a>
        </p>

        <p style="margin:0; font-size:12px; color:#6b7280;">
          Se você não solicitou este orçamento, ignore este e-mail.
        </p>
      </td>
    </tr>
    <tr>
      <td style="padding:16px 24px; background:#f9fafb; font-size:12px; color:#6b7280; text-align:center;">
        © <?= date('Y') ?> Trisoft. Todos os direitos reservados.
      </td>
    </tr>
  </table>
</body>
</html>

<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f4f5; margin:0; padding:24px; color:#111827;">
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="background:#ffffff; border-radius:12px; overflow:hidden; max-width:600px;">
    <tr>
      <td style="padding:24px; background:#dc2626; color:#ffffff;">
        <h1 style="margin:0; font-size:18px; font-weight:bold;">Novo orçamento solicitado</h1>
      </td>
    </tr>
    <tr>
      <td style="padding:24px;">
        <p style="margin:0 0 8px; line-height:1.5;">
          <strong><?= e($order['order_number']) ?></strong>
        </p>
        <p style="margin:0 0 16px; line-height:1.5;">
          Cliente: <strong><?= e($user['name']) ?></strong><br>
          E-mail: <?= e($user['email']) ?><br>
          <?php if (!empty($user['phone'])): ?>Telefone: <?= e($user['phone']) ?><br><?php endif; ?>
          <?php if (!empty($user['document'])): ?>CPF/CNPJ: <?= e($user['document']) ?><br><?php endif; ?>
          <?php if (!empty($user['company_name'])): ?>Empresa: <?= e($user['company_name']) ?><?php endif; ?>
        </p>

        <table cellpadding="8" cellspacing="0" border="0" width="100%" style="border:1px solid #e5e7eb; border-radius:8px; margin:16px 0;">
          <thead>
            <tr style="background:#f9fafb; text-align:left;">
              <th style="font-size:12px; text-transform:uppercase; color:#6b7280; padding:8px;">Item</th>
              <th style="font-size:12px; text-transform:uppercase; color:#6b7280; padding:8px; text-align:right;">Qtd</th>
              <th style="font-size:12px; text-transform:uppercase; color:#6b7280; padding:8px; text-align:right;">Preço tabela</th>
              <th style="font-size:12px; text-transform:uppercase; color:#6b7280; padding:8px; text-align:right;">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $i):
                $snap = is_array($i['product_snapshot']) ? $i['product_snapshot'] : json_decode((string) $i['product_snapshot'], true) ?: [];
            ?>
              <tr style="border-top:1px solid #e5e7eb;">
                <td style="padding:8px;">
                  <strong style="display:block;"><?= e($snap['name'] ?? '—') ?></strong>
                  <small style="color:#6b7280;">SKU <?= e($snap['sku'] ?? '') ?></small>
                </td>
                <td style="padding:8px; text-align:right;"><?= e((string) $i['quantity']) ?></td>
                <td style="padding:8px; text-align:right;"><?= e(money_br((float) $i['unit_price'])) ?></td>
                <td style="padding:8px; text-align:right;"><strong><?= e(money_br((float) $i['total'])) ?></strong></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" style="padding:8px; text-align:right; font-weight:bold;">Total tabela:</td>
              <td style="padding:8px; text-align:right; font-weight:bold;"><?= e(money_br((float) $order['total'])) ?></td>
            </tr>
          </tfoot>
        </table>

        <?php if (!empty($order['customer_notes'])): ?>
          <div style="background:#fef3c7; padding:12px; border-radius:8px; margin:16px 0;">
            <strong style="display:block; margin-bottom:4px;">Observações do cliente:</strong>
            <?= nl2br(e($order['customer_notes'])) ?>
          </div>
        <?php endif; ?>

        <p style="margin:24px 0; text-align:center;">
          <a href="<?= e(\App\Core\Config::appUrl() . '/admin/orcamentos/' . $order['id']) ?>"
             style="background:#111827; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:8px; display:inline-block;">
            Responder orçamento
          </a>
        </p>
      </td>
    </tr>
  </table>
</body>
</html>

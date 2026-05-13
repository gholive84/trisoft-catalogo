<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f4f5; margin:0; padding:24px; color:#111827;">
  <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="background:#ffffff; border-radius:12px; overflow:hidden; max-width:600px;">
    <tr>
      <td style="padding:24px; background:#111827; color:#ffffff;">
        <h1 style="margin:0; font-size:18px;">Catálogo Trisoft</h1>
      </td>
    </tr>
    <tr>
      <td style="padding:24px;">
        <h2 style="margin:0 0 12px; font-size:20px;">Olá, <?= e($user['name']) ?>!</h2>
        <p style="margin:0 0 16px; line-height:1.5;">
          Notamos que você deixou alguns itens no seu carrinho de orçamento.
          Que tal finalizar e receber sua proposta?
        </p>

        <ul style="padding-left:18px; margin:16px 0;">
          <?php foreach ($items as $i): ?>
            <li style="margin:6px 0;"><?= e($i['name']) ?> <small style="color:#6b7280;">(<?= e((string) $i['quantity']) ?>x)</small></li>
          <?php endforeach; ?>
        </ul>

        <p style="margin:24px 0; text-align:center;">
          <a href="<?= e(\App\Core\Config::appUrl() . '/carrinho') ?>"
             style="background:#111827; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:8px; display:inline-block;">
            Retomar orçamento
          </a>
        </p>

        <p style="margin:0; font-size:12px; color:#6b7280;">
          Se mudou de ideia, é só ignorar este e-mail.
        </p>
      </td>
    </tr>
  </table>
</body>
</html>

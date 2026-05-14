<?php /** @var App\Core\View $this */ ?>
<!doctype html>
<html lang="pt-BR">
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f4f5; margin:0; padding:24px; color:#111;">
  <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="background:#ffffff; border-radius:12px; overflow:hidden; max-width:600px;">
    <tr>
      <td style="padding:24px; background:#0071A2; color:#ffffff;">
        <h1 style="margin:0; font-size:18px;">Sua conta foi migrada para o novo Catálogo Trisoft</h1>
      </td>
    </tr>
    <tr>
      <td style="padding:24px;">
        <h2 style="margin:0 0 12px; font-size:20px;">Olá, <?= e($user['name']) ?>!</h2>
        <p style="margin:0 0 16px; line-height:1.5;">
          Atualizamos nossa plataforma e migramos sua conta para o novo sistema. Para
          acessá-la, você precisa <strong>definir uma nova senha</strong>.
        </p>
        <p style="margin:0 0 16px; line-height:1.5; color:#6b7280; font-size:14px;">
          (Seus dados, histórico e endereços foram preservados — só a senha precisa ser
          redefinida por questão de segurança.)
        </p>

        <p style="margin:24px 0; text-align:center;">
          <a href="<?= e($reset_url) ?>"
             style="background:#0071A2; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:8px; display:inline-block; font-weight:600;">
            Definir minha senha
          </a>
        </p>

        <p style="margin:0 0 8px; font-size:13px; color:#6b7280;">
          O link é válido até <strong><?= e(date_br($expires_at, 'd/m/Y H:i')) ?></strong>.
        </p>
        <p style="margin:0; font-size:13px; color:#6b7280;">
          Se não funcionar, copie e cole no navegador:
        </p>
        <p style="margin:8px 0 0; font-size:12px; color:#0071A2; word-break:break-all;">
          <a href="<?= e($reset_url) ?>" style="color:#0071A2;"><?= e($reset_url) ?></a>
        </p>

        <hr style="border:none; border-top:1px solid #e5e7eb; margin:24px 0;">
        <p style="margin:0; font-size:12px; color:#6b7280;">
          Se você não esperava este email ou não tem conta na Trisoft, pode ignorá-lo
          com segurança.
        </p>
      </td>
    </tr>
    <tr>
      <td style="padding:16px 24px; background:#f9fafb; font-size:12px; color:#6b7280; text-align:center;">
        © <?= date('Y') ?> Trisoft Revestimentos Funcionais.
      </td>
    </tr>
  </table>
</body>
</html>

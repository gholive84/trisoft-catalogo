-- Dados iniciais do sistema.
-- Carregado via database/seed.php apenas em base limpa (idempotente via INSERT IGNORE / ON DUPLICATE).

-- Settings padrão
INSERT INTO settings (`key`, `value`, `type`) VALUES
  ('site_name', 'Catálogo Trisoft', 'string'),
  ('site_email_contact', 'contato@trisoft.com.br', 'string'),
  ('site_phone', '', 'string'),
  ('quote_expiration_days', '15', 'int'),
  ('abandoned_cart_days', '3', 'int'),
  ('show_prices_to_guests', '1', 'bool'),
  ('require_login_to_add_cart', '1', 'bool')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

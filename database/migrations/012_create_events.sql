CREATE TABLE IF NOT EXISTS events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  session_id VARCHAR(100) NOT NULL,
  event_name VARCHAR(80) NOT NULL COMMENT 'add_to_cart, view_product, request_quote, etc',
  event_data JSON NULL,
  url VARCHAR(500) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_event (event_name),
  INDEX idx_user (user_id),
  INDEX idx_session (session_id),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

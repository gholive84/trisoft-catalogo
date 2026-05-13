CREATE TABLE IF NOT EXISTS page_views (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  session_id VARCHAR(100) NOT NULL,
  url VARCHAR(500) NOT NULL,
  referrer VARCHAR(500) NULL,
  user_agent VARCHAR(500) NULL,
  ip_address VARCHAR(45) NULL,
  product_id BIGINT UNSIGNED NULL COMMENT 'Se for pagina de produto',
  category_id BIGINT UNSIGNED NULL COMMENT 'Se for pagina de categoria',
  duration_seconds INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user (user_id),
  INDEX idx_session (session_id),
  INDEX idx_product (product_id),
  INDEX idx_category (category_id),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

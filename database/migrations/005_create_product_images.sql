CREATE TABLE IF NOT EXISTS product_images (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(200) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_main TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pi_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX idx_product (product_id),
  INDEX idx_main (product_id, is_main)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

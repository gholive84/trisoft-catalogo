-- Imagem técnica de dimensões (desenho com cotas "A"/"B") que aparece em
-- algumas páginas de specs do PDF, acima da tabela. Extraída pelo script
-- extract_pdf_images.php quando presente.

ALTER TABLE products
  ADD COLUMN dimensions_image_path VARCHAR(255) NULL
  COMMENT 'Imagem tecnica de cotas/dimensoes (acima da tabela de specs no PDF)'
  AFTER modulation_image_path;

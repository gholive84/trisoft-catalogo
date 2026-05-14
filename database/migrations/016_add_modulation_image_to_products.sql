-- Imagem das "Sugestões de Modulação" extraída da página de specs do PDF.
-- Renderizada e salva em public/uploads/products/<slug>-modulation.png
-- pelo script scripts/extract_pdf_images.php (via flag --modulations).

ALTER TABLE products
  ADD COLUMN modulation_image_path VARCHAR(255) NULL
  COMMENT 'Imagem das sugestoes de modulacao (extraida da pagina de specs do PDF)'
  AFTER hero_image_path;

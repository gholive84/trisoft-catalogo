-- Adiciona campo `specifications` JSON em products para suportar tabelas de
-- variações/SKUs (ex.: catálogo de baffles, onde cada produto tem várias
-- modulações de tamanho com diferentes códigos, áreas e quantidades).
--
-- Estrutura sugerida (array de objetos):
-- [{"code":"BC-STR-50-0001","thickness":50,"a":200,"b":1200,"pieces_per_box":14,"coverage_area":"3,96m²","pet_bottles":27}, ...]

ALTER TABLE products
  ADD COLUMN specifications JSON NULL
  COMMENT 'Tabela de variações/SKUs do produto (modulações, dimensões, etc.)'
  AFTER description;

-- Subtítulo opcional (usado abaixo do título no card e na página interna do produto)
ALTER TABLE products
  ADD COLUMN subtitle VARCHAR(150) NULL
  COMMENT 'Subtítulo do produto (ex.: SOLID, HIGH RELIEF)'
  AFTER name;

-- Imagem de banner (hero) na página interna do produto
ALTER TABLE products
  ADD COLUMN hero_image_path VARCHAR(255) NULL
  COMMENT 'Imagem hero (banner) na página interna; se NULL usa main image'
  AFTER description;

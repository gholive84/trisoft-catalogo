-- Rótulos customizáveis para as colunas A/B/C/D do layout 'simple'.
-- Formato JSON: {"a":"Diameter","b":"Width",...}
-- Quando NULL, fallback p/ "A", "B", "C", "D".
-- Permite produtos circulares mostrarem "Diameter" em vez de "A", etc.

ALTER TABLE products
  ADD COLUMN spec_column_labels JSON NULL
  COMMENT 'Rotulos customizados das colunas A-D (JSON: {a:"Diameter",...})'
  AFTER spec_layout;

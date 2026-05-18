-- Layout do schema da tabela de specs por produto.
-- 'simple' (default): colunas code, thickness, a, b, c, d, pieces_per_box, coverage_area, pet_bottles
-- 'multi_piece': layout p/ produtos com 2 peças componentes (ex: BAFFLE FORM ARC)
--    colunas: code, thickness, p1_a, p1_b, p1_c, p1_pieces, p1_pet,
--             p2_a, p2_b, p2_c, p2_pieces, pieces_per_box, coverage_area, pet_bottles
-- Admin e public renderizam a tabela conforme este flag.

ALTER TABLE products
  ADD COLUMN spec_layout VARCHAR(30) NOT NULL DEFAULT 'simple'
  COMMENT 'Layout da tabela de specs: simple | multi_piece'
  AFTER specifications;

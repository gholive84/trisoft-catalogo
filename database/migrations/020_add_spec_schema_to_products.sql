-- Layout 'flexible': admin define colunas dinamicamente por produto.
-- spec_schema guarda a estrutura: {columns: [{key, label, unit, color, group}]}
-- specifications continua array de rows; as chaves dos rows usam o `key` das colunas.
-- Cores: blue, amber, emerald, rose, purple, slate, null.

ALTER TABLE products
  ADD COLUMN spec_schema JSON NULL
  COMMENT 'Definicao de colunas do layout flexible (JSON: {columns:[{key,label,unit,color,group}]})'
  AFTER spec_column_labels;

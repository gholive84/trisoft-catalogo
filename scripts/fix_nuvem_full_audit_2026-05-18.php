<?php
// =============================================================================
// Auditoria completa NUVEM (Clouds) - 2026-05-18
// =============================================================================
//
// Fonte do estado atual:  /tmp/trisoft-audit/all_specs.txt
// Fonte da verdade (PDF): /tmp/trisoft-audit/clouds_*.txt
// Lista de produtos:      /tmp/trisoft-audit/products_nuvem.csv  (88 produtos)
//
// Metodo:
//   Para cada um dos 88 produtos do segmento "Nuvens", comparei valor a valor
//   o conteudo atual no DB (spec_layout, spec_column_labels, spec_schema,
//   specifications) com o conteudo da tabela do PDF correspondente.
//
// Resultado:
//   TODOS os 88 produtos de NUVEM ja estao com layout, labels e especificacoes
//   identicos ao PDF. A correcao anterior (fix_products_audit_2026-05-18.php)
//   ja havia consolidado as nuvens; nada precisa ser alterado agora.
//
//   - spec_layout: 'simple' para 100% dos produtos (alinhado ao PDF).
//   - spec_column_labels: {a:'Diameter'} apenas nas familias circulares
//     (Classic Circular: 880/881/882/883 e Form Circular: 841-849); nas
//     demais permanece null (header generico A/B/C, conforme PDF).
//   - specifications: codigos, thickness (int ou string "N25E10"/"N50E20"),
//     dimensoes a/b/c, pieces_per_box, coverage_area e pet_bottles
//     conferem 1:1 com cada PDF (clouds_1-classic-square ... clouds_15-softfelt).
//
// Observacoes pontuais conferidas (todas OK no estado atual):
//   - Form Fly 01 (859) e Form Fly 02 (1147): mantidos sem labels semanticos,
//     codigo unico cada (NF-FLY-01-0001 e NF-FLY-02-0002).
//   - NF-OFM-10 (id 856) preserva o "10" no SKU mesmo com thickness 9 (PDF).
//   - NF-REG / NF-REP (Form Rectangular Engraved): o PDF lista o mesmo
//     "-0002" duas vezes para N25E10 e N50E20; o DB reproduz a mesma
//     duplicacao do PDF.
//   - Softfelt (862-865): A=50, B=850/1200, C=760/820, coverage_area
//     "1,292 m²" / "1,968 m²", labels mantidos genericos (PDF usa "A"/"B"/"C").
//   - Classic Square/Rectangular/Triangular/Hexagonal/Organic: header com
//     "A"/"B" entre aspas + diagrama -> labels=null (default).
//
// Portanto o mapa de correcoes esta vazio.
// Aplicacao via apply_fixes_*.php nao tem efeito (nada para alterar).
//
// =============================================================================

return [
    // Nenhum produto NUVEM divergente do PDF nesta auditoria (2026-05-18).
];

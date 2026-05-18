<?php
// =============================================================================
// Auditoria completa FACHADA (Sunshade) - 2026-05-18
// =============================================================================
//
// Fonte do estado atual:  /tmp/trisoft-audit/all_specs.txt
// Fonte da verdade (PDF): /tmp/trisoft-audit/facade_*.txt
// Lista de produtos:      /tmp/trisoft-audit/products_fachada.csv  (46 produtos)
//
// Metodo:
//   Para cada um dos 46 produtos do segmento "Fachadas", comparei valor a valor
//   o conteudo atual no DB (spec_layout, spec_column_labels, spec_schema,
//   specifications) com o conteudo da tabela do PDF correspondente.
//
// Resultado:
//   TODOS os 46 produtos de FACHADA ja estao com layout, labels e especificacoes
//   identicos ao PDF (apos as auditorias anteriores). Nada precisa ser alterado
//   agora.
//
//   - spec_layout: 'simple' para 100% dos produtos da segmento Fachadas.
//   - spec_column_labels: null em todos os casos. PDF usa cabecalho generico
//     "A"/"B"/"C" entre aspas (sem nome semantico) e a unica unidade rotulada
//     no PDF e' (mm) sob "Thickness". O template `templates/public/product.php`
//     ja renderiza os defaults '"A"' '"B"' '"C"' com unit "mm" appended, o que
//     equivale ao PDF.
//   - specifications: codigos, thickness, dimensoes a/b/c, pieces_per_box,
//     coverage_area e pet_bottles conferem 1:1 com cada PDF.
//
// Mapeamento PDF -> familias (todos com spec_layout=simple, labels=null):
//
//   facade_1-sunshade-board.txt:
//     - 960 SS-BSM-02 sunshade-board-molded         (SS-BSM-02-* + SS-BMD-09-*)
//     - 961 SS-BSVP-02 sunshade-board-visibility-percentage (SS-BSVP-02-* + SS-BVP-09-*)
//     - 962 SS-BSC-02 sunshade-board-cut            (SS-BSC-02-* + SS-BCT-09-*)
//     - 963 SS-BSE-02 sunshade-board-engraved       (SS-BSE-02-* + SS-BEG-09-*)
//     - 964 SS-BSEPBS-02 sunshade-board-engraved-pbs (SS-BSEPBS-02-* + SS-BEPBS-09-*)
//     - 965 SS-BSFM-02 sunshade-board-fire-mark     (SS-BSFM-02-* + SS-BFM-09-*)
//     - 966 SS-BSPBS-02 sunshade-board-solid (PBS)  (SS-BSPBS-02-* + SS-BPBS-09-*)
//     - 967 SS-BVC-09 sunshade-board-v-cut          (SS-BVC-09-*, so' 9mm)
//     - 968 SS-BSPBS-02-47E3 sunshade-board-solid-02 (variant duplicado, mesmos codigos)
//
//   facade_2-sunshade-flaps.txt:
//     - 973 SS-FSO-09  (Solid)            21 linhas (a=100..600 b=2800; 100..600 b=1400; 100..560 b=1200)
//     - 974 SS-FCT-09  (Cut)              mesma estrutura
//     - 975 SS-FEG-09  (Engraved)         "
//     - 976 SS-FEC-09  (Engraved Cut)     "
//     - 977 SS-FFM-09  (Fire Mark)        "
//     - 978 SS-FPBS-09 (PBS)              "
//     - 979 SS-FVP-09  (Visibility %)     "
//
//   facade_3-sunshade-stretch.txt:
//     - 980 SS-SCT-09  (Cut)            21 linhas (a=150..1200; b=2800/1400/1200; ultimo de cada grupo 1200x2800)
//     - 981 SS-SEG-09  (Engraved)       "
//     - 982 SS-SPBS-09 (Engraved PSB)   "
//     - 983 SS-SFM-09  (Fire Mark)      "
//     - 984 SS-SVP-09  (Visibility %)   "
//     - 985 SS-SSO-09  (Solid)          "
//     - 986 SS-SEC-02  (Stretch Slim Engraved Cut, thickness=2, pet=111)
//     - 987 SS-SEC-02-E8B2 (Stretch Slim PBS, mesmos codigos)
//     - 988 SS-SEC-02-5648 (Stretch Slim Solid, mesmos codigos)
//     - 989 SS-SVP-02  (Stretch Slim Visibility %, codigos proprios SS-SVP-02-*)
//
//   facade_4-sunshade-honeycomb.txt:
//     - 990 SS-HFS-09 (Honeycomb Flap Solid)  12 linhas; a=50; b=1400 ou 1200; c variavel
//     - 991 SS-HSO-09 (Honeycomb Solid)       15 linhas; a=50; b=1400 ou 1200; c variavel
//
//   facade_7-sunshade-slatted.txt:
//     - 992 SS-SLEG-09   (Engraved)         21 linhas
//     - 993 SS-SLEC-09   (Engraved Cut)     "
//     - 994 SS-SLCT-09   (Cut)              "
//     - 995 SS-SLEPBS-09 (Engraved PBS)     "
//     - 996 SS-SLFM-09   (Fire Mark)        "
//     - 997 SS-SLPBS-09  (PBS)              "
//     - 998 SS-SLVP-09   (Visibility %)     "
//     - 999 SS-SLO-09    (Solid Perfil L)   "
//
//   facade_8-sunshade-zigzag.txt:
//     - 1000 SS-ZPBS-02 (PBS, thickness=2, pet=111)         21 linhas
//     - 1001 SS-ZSO-02  (Solid)                              "
//     - 1002 SS-ZVP-02  (Visibility %)                       "
//
//   facade_9-sunshade-moving.txt (variantes do BOARD, reutilizam codigos SS-BSC/BSE/BSPBS/BSS):
//     - 1003 SS-BSC-02-9A33  (Cut)        4 linhas (so' a thickness=2 do board)
//     - 1004 SS-BSE-02-AF41  (Engraved)   4 linhas
//     - 1005 SS-BSPBS-02-634C (PBS)       4 linhas
//     - 1006 SS-BSS-02       (Solid)      4 linhas (SS-BSS-02-0001..0004)
//
//   facade_10-sunshade-tube.txt:
//     - 969 SS-TBE-09 (Engraved)  3 linhas; a=120, b=2800/1400/1200
//     - 970 SS-TBS-09 (Solid)     3 linhas idem
//
//   facade_11-sunshade-pivoting.txt:
//     - 971 SS-PVS-09  (Solid)              10 linhas; a=300..600, b=2800/1400/1200, c=4..2/8..4/10..5
//     - 972 SS-PVVP-09 (Visibility %)       10 linhas idem
//
// Observacoes pontuais conferidas (todas OK no estado atual):
//
//   - Os SKUs 1003-1006 (Moving Slim) reutilizam exatamente os codigos do BOARD
//     thickness=2 (SS-BSC-02-*, SS-BSE-02-*, SS-BSPBS-02-*, SS-BSS-02-*); o PDF
//     facade_9 confirma essa reutilizacao (4 linhas cada, sem variante 9mm).
//
//   - SS-SEC-02 (986), SS-SEC-02-5648 (988) e SS-SEC-02-E8B2 (987) compartilham
//     o mesmo dataset de 21 linhas com codigos SS-SEC-02-0001..0021 (cut/PBS/
//     solid). O PDF facade_3 confirma a duplicacao identica.
//
//   - Linhas SS-SCT/SS-SEG/SS-SFM/SS-SLEPBS/SS-SPBS/SS-SSO/SS-SVP-{02,09} #14
//     e #21 mostram "1200 2800 1" no PDF (par a/b invertido vs as outras linhas
//     da serie) -> o DB ja preserva essa peculiaridade do PDF.
//
//   - SS-HFS-09-0009 (c=10), SS-HFS-09-0011 (c=16), SS-HFS-09-0012 (c=23): os
//     valores "C" parecem fora de padrao para a serie de 1200, mas sao os
//     valores impressos no PDF Trisoft. Mantidos exatamente como no PDF (regra:
//     "Deixe labels e valores exatamente iguais aos PDFs").
//
//   - Coluna C (mm) em SS-PVS-09, SS-PVVP-09, SS-HFS-09, SS-HSO-09: renderiza
//     automaticamente porque o template `templates/public/product.php` mostra
//     `c` quando ha valor != '' e o label default '"C"' alinha com o PDF
//     ("A"/"B"/"C" entre aspas, sem nome semantico).
//
//   - PDFs facade_5-sunshade-t50.txt e facade_6-sunshade-t70.txt existem nos
//     extratos mas NAO ha produto correspondente no DB (SKUs SS-T50*, SS-T70*
//     nao constam em products_fachada.csv). Fora do escopo desta auditoria.
//
// Portanto o mapa de correcoes esta vazio.
// Aplicacao via apply_fixes_*.php nao tem efeito (nada para alterar).
//
// =============================================================================

return [
    // Nenhum produto FACHADA divergente do PDF nesta auditoria (2026-05-18).
];

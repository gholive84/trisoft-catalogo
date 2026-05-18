<?php

declare(strict_types=1);

/**
 * Auditoria completa PAREDE — 2026-05-18 (re-pass).
 *
 * Cobre apenas produtos NÃO contemplados (ou onde a revisão indica divergência)
 * na auditoria anterior `fix_walls_audit_2026-05-19.php`.
 *
 * Fonte: PDFs em /tmp/trisoft-audit/wall_*.txt
 *
 * Convenções aplicadas:
 *  - Labels EXATAMENTE como o PDF (A/B genéricos quando entre aspas; semânticos
 *    quando o cabeçalho do PDF traz nome).
 *  - coverage_area sempre com "m²" no final ("3,36 m²", "3,24 m²", ...).
 *  - thickness numérico quando puro; string para "N25E10"/"N50E20".
 *  - Colunas a..h e pieces_per_box/coverage_area/pet_bottles sempre presentes.
 *
 * Produtos NÃO incluídos (sem mudanças necessárias):
 *  - 931 (Revest Ness Bricks)  — já flexible com grupos Cube/Brick.
 *  - 951/952 (Softfelt)        — já têm colunas e..h corretas no audit anterior.
 *  - Todos os demais já fixados em fix_walls_audit_2026-05-19.php.
 */

return [

    // =========================================================================
    // REVEST FORM METALLIC — ENGRAVED  (wall_11)
    // PDF rotula colunas A/B entre aspas → labels null. PDF NÃO traz "Unit"
    // preenchido; manter pieces_per_box=1 por convenção. Coverage em m².
    // =========================================================================
    922 => [ // RF-MEG-09 revest-form-metallic-engraved
        'spec_layout' => 'simple',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>'RF-MEG-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RF-MEG-28-0001','thickness'=>28,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>191],
            ['code'=>'RF-MEG-53-0001','thickness'=>53,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>361],
        ],
    ],

    // =========================================================================
    // REVEST FORM METALLIC — CUT  (wall_11)
    // PDF rotula A/B entre aspas → labels null. SKUs 9 / 25 / 50.
    // =========================================================================
    923 => [ // RF-MEC-09 revest-form-metallic-cut
        'spec_layout' => 'simple',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>'RF-MEC-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RF-MEC-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>191],
            ['code'=>'RF-MEC-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>361],
        ],
    ],

    // =========================================================================
    // REVEST FORM — PRINTED  (wall_4)
    // PDF traz A/B entre aspas + coluna "Arrow profile" (e). 9mm não tem arrow.
    // Espessuras 25/50 trazem arrow=2.  PET 362 / 167 / 334 (do PDF).
    // =========================================================================
    939 => [ // RF-PRI-09 revest-form-printed
        'spec_layout' => 'simple',
        'spec_column_labels' => ['e' => 'Arrow profile'],
        'spec_schema' => null,
        'specifications' => [
            ['code'=>'RF-PRI-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'', 'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>362],
            ['code'=>'RF-PRI-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>167],
            ['code'=>'RF-PRI-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>334],
        ],
    ],

    // =========================================================================
    // REVEST FORM — PRINTED · ENGRAVED  (wall_4)
    // PDF: 4 linhas com thickness 3 / 9 / "N25E10" / "N50E20".
    // Arrow profile = 2 apenas nas duas últimas linhas. PET 138/376/202/404.
    // =========================================================================
    940 => [ // RP-PEG-03 revest-form-printed-engraved
        'spec_layout' => 'simple',
        'spec_column_labels' => ['e' => 'Arrow profile'],
        'spec_schema' => null,
        'specifications' => [
            ['code'=>'RP-PEG-03-0001','thickness'=>3,        'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'', 'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>138],
            ['code'=>'RP-PEG-09-0001','thickness'=>9,        'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'', 'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RP-PEG-10-0001','thickness'=>'N25E10', 'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>202],
            ['code'=>'RP-PEG-20-0001','thickness'=>'N50E20', 'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>404],
        ],
    ],

    // =========================================================================
    // REVEST FORM — PRINTED · CUT  (wall_4)
    // PDF: 9 / 25 / 50. Arrow profile=2 nas duas últimas. PET 376/173/404.
    // =========================================================================
    941 => [ // RP-COB-09 revest-form-printed-cut
        'spec_layout' => 'simple',
        'spec_column_labels' => ['e' => 'Arrow profile'],
        'spec_schema' => null,
        'specifications' => [
            ['code'=>'RP-COB-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'', 'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RP-COB-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>173],
            ['code'=>'RP-COB-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>404],
        ],
    ],

    // =========================================================================
    // REVEST FORM SLATTED — SOLID · V-CUT  (wall_4)
    // PDF traz 3 SKUs com mesma A/B e thickness 9, variando "V-cut spacing"
    // (30 / 50 / 100). PET solid = 376 / 173 / 346.
    // 'd' = V-cut spacing (revert script preserva esse label).
    // =========================================================================
    944 => [ // RP-RFL-09 revest-form-slatted-solid-v-cut
        'spec_layout' => 'simple',
        'spec_column_labels' => ['d' => 'V-cut spacing'],
        'spec_schema' => null,
        'specifications' => [
            ['code'=>'RP-RFL-09-0001','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>30, 'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RP-RFL-09-0002','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>50, 'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>173],
            ['code'=>'RP-RFL-09-0003','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>100,'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>346],
        ],
    ],

    // =========================================================================
    // REVEST FORM SLATTED — PRINTED · V-CUT  (wall_4)
    // Mesmas geometrias; PET printed = 376 / 376 / 376 (do PDF).
    // Códigos no PDF aparecem como "RP-RFL-09-0001/0002/0003" (mesmos da variante
    // solid — provável erro do catálogo). Mantemos fielmente os SKUs do PDF.
    // =========================================================================
    947 => [ // RP-RFL-09-8EFE revest-form-slatted-printed-v-cut
        'spec_layout' => 'simple',
        'spec_column_labels' => ['d' => 'V-cut spacing'],
        'spec_schema' => null,
        'specifications' => [
            ['code'=>'RP-RFL-09-0001','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>30, 'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RP-RFL-09-0002','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>50, 'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RP-RFL-09-0003','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>100,'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
        ],
    ],

    // =========================================================================
    // WIDE PLANKS — PRINTED  (wall_6)
    // PDF rotula "Height 1" / "Length 1" → a=Height, b=Length (semântico).
    // Coverage Area no PDF aparece como "3,36" sem unidade → normalizamos m².
    // =========================================================================
    953 => [ // TL-TBL-09 wide-planks-printed
        'spec_layout' => 'simple',
        'spec_column_labels' => ['a' => 'Height', 'b' => 'Length'],
        'spec_schema' => null,
        'specifications' => [
            ['code'=>'TL-TBL-09-0001','thickness'=>9,'a'=>1200,'b'=>150,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>56,'coverage_area'=>'3,36 m²','pet_bottles'=>20],
        ],
    ],

    // =========================================================================
    // PARAMETRIC FLOW — SOLID  (wall_9) — layout wall_ceiling
    // PDF: Wall A=2800, B=300, length=1130 / Ceiling C=2800, D=60, length=1130.
    // wall_coverage=3,16 m²  ceiling_coverage=3,50 m²  units=16  PET=94.
    // =========================================================================
    958 => [ // PF-PRF-9 parametric-flow-solid
        'spec_layout' => 'wall_ceiling',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            [
                'code'             => 'PF-PRF-9-0001',
                'thickness'        => 9,
                'wall_height'      => 2800,
                'wall_width'       => 300,
                'wall_length'      => 1130,
                'ceiling_height'   => 2800,
                'ceiling_width'    => 60,
                'ceiling_length'   => 1130,
                'pieces_per_box'   => 16,
                'wall_coverage'    => '3,16 m²',
                'ceiling_coverage' => '3,50 m²',
                'pet_bottles'      => 94,
            ],
        ],
    ],

    // =========================================================================
    // PARAMETRIC FLOW — PRINTED  (wall_9) — layout wall_ceiling
    // PDF: Wall A=2800, B=30, length=1130 / Ceiling C=310, D=60, length=1130.
    // Valores estranhos (Wall Width=30, Ceiling Height=310) mantidos fielmente
    // ao PDF — possível erro do catálogo Trisoft.
    // wall_coverage=3,16 m²  ceiling_coverage=3,50 m²  units=16  PET=94.
    // =========================================================================
    959 => [ // PF-PRT-9 parametric-flow-printed
        'spec_layout' => 'wall_ceiling',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            [
                'code'             => 'PF-PRT-9-0001',
                'thickness'        => 9,
                'wall_height'      => 2800,
                'wall_width'       => 30,
                'wall_length'      => 1130,
                'ceiling_height'   => 310,
                'ceiling_width'    => 60,
                'ceiling_length'   => 1130,
                'pieces_per_box'   => 16,
                'wall_coverage'    => '3,16 m²',
                'ceiling_coverage' => '3,50 m²',
                'pet_bottles'      => 94,
            ],
        ],
    ],

];

<?php
// Auditoria PAREDE 2026-05-19 - produtos novos + extensoes para colunas extras (e..h)
// Fonte: /tmp/trisoft-audit/wall_*.txt (texto extraido dos PDFs Trisoft).
// Aplicado via: php scripts/apply_fixes_2026-05-19.php (a criar)
//
// Convencao:
//   a..d -> colunas geometricas principais
//   e..h -> colunas EXTRAS (so renderizadas quando ha label)
//   spec_column_labels traz somente chaves com label nao-padrao
//   Todas as linhas trazem e/f/g/h mesmo vazias para manter o schema consistente
//   thickness: int quando numerico puro; string quando "N25E10" etc.
//   "1 200" do PDF (com espaco) eh interpretado como 1200

return [

    // =========================================================================
    // WALL - REVEST FRAME  (wall_1-revest-frame)
    // PDF rotula somente "A"/"B"/"C"/"D" sem nome semantico (referem-se ao diagrama).
    // Cada SKU tem duas geometrias: A x B (pequena) e C x D (grande, 2700x1200).
    // Mantemos labels genericos (null) e usamos 4 colunas A..D.
    // =========================================================================

    915 => [ // RF-FRA-25 revest-frame-solid
        'spec_column_labels' => null,
        'specifications' => [
            ['code'=>'RF-FRA-25-0001','thickness'=>25,'a'=>500,'b'=>500,'c'=>'',  'd'=>'',  'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'0,25 m²','pet_bottles'=>13],
            ['code'=>'RF-FRA-25-0002','thickness'=>25,'a'=>'', 'b'=>'', 'c'=>2700,'d'=>1200,'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>167],
            ['code'=>'RF-FRA-50-0001','thickness'=>50,'a'=>500,'b'=>500,'c'=>'',  'd'=>'',  'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'0,25 m²','pet_bottles'=>26],
            ['code'=>'RF-FRA-50-0002','thickness'=>50,'a'=>'', 'b'=>'', 'c'=>2700,'d'=>1200,'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>334],
        ],
    ],

    916 => [ // RF-FRP-25 revest-frame-printed
        'spec_column_labels' => null,
        'specifications' => [
            ['code'=>'RF-FRP-25-0001','thickness'=>25,'a'=>500,'b'=>500,'c'=>'',  'd'=>'',  'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'0,25 m²','pet_bottles'=>13],
            ['code'=>'RF-FRP-25-0002','thickness'=>25,'a'=>'', 'b'=>'', 'c'=>2700,'d'=>1200,'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>167],
            ['code'=>'RF-FRP-50-0001','thickness'=>50,'a'=>500,'b'=>500,'c'=>'',  'd'=>'',  'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'0,25 m²','pet_bottles'=>26],
            ['code'=>'RF-FRP-50-0002','thickness'=>50,'a'=>'', 'b'=>'', 'c'=>2700,'d'=>1200,'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>334],
        ],
    ],

    // =========================================================================
    // WALL - REVEST DECOR  (wall_2-revest_decor)
    // Mesma logica do FRAME: A x B pequena, C x D grande. Labels genericos.
    // =========================================================================

    927 => [ // RD-FRP-25 revest-decor-printed
        'spec_column_labels' => null,
        'specifications' => [
            ['code'=>'RD-FRP-25-0001','thickness'=>25,'a'=>800,'b'=>1100,'c'=>'',  'd'=>'',  'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'0,88 m²','pet_bottles'=>45],
            ['code'=>'RD-FRP-25-0002','thickness'=>25,'a'=>'', 'b'=>'',  'c'=>2700,'d'=>1200,'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>167],
            ['code'=>'RD-FRP-50-0001','thickness'=>50,'a'=>800,'b'=>1100,'c'=>'',  'd'=>'',  'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'0,88 m²','pet_bottles'=>91],
            ['code'=>'RD-FRP-50-0002','thickness'=>50,'a'=>'', 'b'=>'',  'c'=>2700,'d'=>1200,'e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>334],
        ],
    ],

    // =========================================================================
    // WALL - REVEST NESS  (wall_3-revest_ness)
    // Geometria dupla por SKU: (A x B) quadrado + (A x C) tijolo.
    // Mantemos labels genericos "A","B","C" (sem nome semantico no PDF).
    // pieces_per_box e coverage_area mudam por linha.
    // =========================================================================

    928 => [ // RN-NSS-25 revest-ness-solid
        'spec_column_labels' => null,
        'specifications' => [
            ['code'=>'RN-NSS-25-0001','thickness'=>25,'a'=>620,'b'=>620,'c'=>'',  'd'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>40,'coverage_area'=>'15,38 m²','pet_bottles'=>20],
            ['code'=>'RN-NSS-25-0002','thickness'=>25,'a'=>620,'b'=>'', 'c'=>1245,'d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>20,'coverage_area'=>'15,44 m²','pet_bottles'=>40],
            ['code'=>'RN-NSS-50-0001','thickness'=>50,'a'=>620,'b'=>620,'c'=>'',  'd'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>20,'coverage_area'=>'7,69 m²', 'pet_bottles'=>40],
            ['code'=>'RN-NSS-50-0002','thickness'=>50,'a'=>620,'b'=>'', 'c'=>1245,'d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>10,'coverage_area'=>'7,72 m²', 'pet_bottles'=>79],
        ],
    ],

    929 => [ // RN-NSC-25 revest-ness-solid-cut
        'spec_column_labels' => null,
        'specifications' => [
            ['code'=>'RN-NSC-25-0001','thickness'=>25,'a'=>620,'b'=>620,'c'=>'',  'd'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>40,'coverage_area'=>'15,38 m²','pet_bottles'=>20],
            ['code'=>'RN-NSC-25-0002','thickness'=>25,'a'=>620,'b'=>'', 'c'=>1245,'d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>20,'coverage_area'=>'15,44 m²','pet_bottles'=>40],
            ['code'=>'RN-NSC-50-0001','thickness'=>50,'a'=>620,'b'=>620,'c'=>'',  'd'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>20,'coverage_area'=>'7,69 m²', 'pet_bottles'=>40],
            ['code'=>'RN-NSC-50-0002','thickness'=>50,'a'=>620,'b'=>'', 'c'=>1245,'d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>10,'coverage_area'=>'7,72 m²', 'pet_bottles'=>79],
        ],
    ],

    930 => [ // RN-SFM-25 revest-ness-solid-fire-mark
        'spec_column_labels' => null,
        'specifications' => [
            ['code'=>'RN-SFM-25-0001','thickness'=>25,'a'=>620,'b'=>620,'c'=>'',  'd'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>40,'coverage_area'=>'15,38 m²','pet_bottles'=>20],
            ['code'=>'RN-SFM-25-0002','thickness'=>25,'a'=>620,'b'=>'', 'c'=>1245,'d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>20,'coverage_area'=>'15,44 m²','pet_bottles'=>40],
            ['code'=>'RN-SFM-50-0001','thickness'=>50,'a'=>620,'b'=>620,'c'=>'',  'd'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>20,'coverage_area'=>'7,69 m²', 'pet_bottles'=>40],
            ['code'=>'RN-SFM-50-0002','thickness'=>50,'a'=>620,'b'=>'', 'c'=>1245,'d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>10,'coverage_area'=>'7,72 m²', 'pet_bottles'=>79],
        ],
    ],

    // =========================================================================
    // WALL - REVEST FORM  (wall_4-revest_form)
    // Geometria A x B. Alguns SKUs tem coluna extra "Arrow profile" (e).
    // Re-emitimos 932/934 com a coluna extra; 935 (Fire Mark) tambem tem.
    // =========================================================================

    932 => [ // RF-FOR-09 revest-form-solid (RE-EMITIDO com 'e'=Arrow profile)
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length', 'e' => 'Arrow profile'],
        'specifications' => [
            ['code'=>'RF-FOR-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'', 'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RF-FOR-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>202],
            ['code'=>'RF-FOR-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>404],
        ],
    ],

    934 => [ // RF-COB-09 revest-form-solid-cut (RE-EMITIDO com 'e'=Arrow profile)
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length', 'e' => 'Arrow profile'],
        'specifications' => [
            ['code'=>'RF-COB-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'', 'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RF-COB-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>202],
            ['code'=>'RF-COB-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>404],
        ],
    ],

    935 => [ // RF-FFM-09 revest-form-solid-fire-mark (RE-EMITIDO com 'e'=Arrow profile)
        // Obs.: linha RF-FFM-09 do PDF traz thickness=3 (provavel erro do catalogo, mantido fielmente).
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length', 'e' => 'Arrow profile'],
        'specifications' => [
            ['code'=>'RF-FFM-09-0001','thickness'=>3, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'', 'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>119],
            ['code'=>'RF-FFM-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>202],
            ['code'=>'RF-FFM-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>2,  'f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>404],
        ],
    ],

    933 => [ // RF-ENG-09 revest-form-solid-engraved
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-ENG-09-0001','thickness'=>9,        'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RF-ENG-10-0001','thickness'=>'N25E10', 'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>202],
            ['code'=>'RF-ENG-20-0001','thickness'=>'N50E20', 'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>404],
        ],
    ],

    936 => [ // RF-MWV-03 revest-form-solid-molded-flute
        // PDF mostra A=2800, B=900 e thicknesses 3 / 55 / 80.
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-MWV-03-0001','thickness'=>3, 'a'=>2800,'b'=>900,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'2,52 m²','pet_bottles'=>89],
            ['code'=>'RF-MWV-55-0002','thickness'=>55,'a'=>2800,'b'=>900,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'2,52 m²','pet_bottles'=>130],
            ['code'=>'RF-MWV-80-0002','thickness'=>80,'a'=>2800,'b'=>900,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'2,52 m²','pet_bottles'=>259],
        ],
    ],

    937 => [ // RT-SMO-03 revest-form-solid-molded-stone
        // PDF: A=2400, B=1200, thickness 3/28/53.
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RT-SMO-03-0001','thickness'=>3, 'a'=>2400,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>114],
            ['code'=>'RT-SMO-28-0001','thickness'=>28,'a'=>2400,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>167],
            ['code'=>'RT-SMO-53-0001','thickness'=>53,'a'=>2400,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>334],
        ],
    ],

    938 => [ // RF-VCT-09 revest-form-solid-v-cut
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-VCT-09-0001','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
        ],
    ],

    942 => [ // RF-PWV-30 revest-form-printed-molded-flute
        // PDF: A=2800, B=900, thickness 30/55/80.
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-PWV-30-0001','thickness'=>30,'a'=>2800,'b'=>900,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'2,52 m²','pet_bottles'=>89],
            ['code'=>'RF-PWV-55-0002','thickness'=>55,'a'=>2800,'b'=>900,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'2,52 m²','pet_bottles'=>130],
            ['code'=>'RF-PWV-80-0002','thickness'=>80,'a'=>2800,'b'=>900,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'2,52 m²','pet_bottles'=>259],
        ],
    ],

    943 => [ // RF-PMO-03 revest-form-printed-molded-stone
        // PDF: A=2400, B=1200, thickness 3/28/53.
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-PMO-03-0001','thickness'=>3, 'a'=>2400,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>89],
            ['code'=>'RF-PMO-28-0001','thickness'=>28,'a'=>2400,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>130],
            ['code'=>'RF-PMO-53-0001','thickness'=>53,'a'=>2400,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>259],
        ],
    ],

    945 => [ // RP-RII-16 revest-form-slatted-solid-i
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RP-RII-16-0001','thickness'=>16,'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
        ],
    ],

    946 => [ // RP-RFL-16 revest-form-slatted-solid-flat
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RP-RFL-16-0001','thickness'=>16,'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
        ],
    ],

    948 => [ // RP-RIP-16 revest-form-slatted-printed-i
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RP-RIP-16-0001','thickness'=>16,'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
        ],
    ],

    949 => [ // RP-RFP-16 revest-form-slatted-printed-flat
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RP-RFP-16-0001','thickness'=>16,'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
        ],
    ],

    950 => [ // RF-MDM-09 revest-form-modular-solid
        // PDF traz 2 linhas: (A=2800, B=600) e (A=700, B=1200), ambas thickness 9.
        // pieces_per_box: 2 e 4 respectivamente; coverage_area constante 3,36 m².
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-MDM-09-0001','thickness'=>9,'a'=>2800,'b'=>600, 'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>2,'coverage_area'=>'3,36 m²','pet_bottles'=>188],
            ['code'=>'RF-MDM-09-0002','thickness'=>9,'a'=>700, 'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>4,'coverage_area'=>'3,36 m²','pet_bottles'=>94],
        ],
    ],

    // =========================================================================
    // WALL - SOFTFELT TRANSLUCENT  (wall_5-softfelt)
    // RE-EMITIDO com colunas extras:
    //   e = Slat thickness (mm)
    //   f = Unfolded dimensions (ex: 170x2400)
    //   g = Support thickness (mm)
    //   h = Support dimensions (ex: 35x840)
    // a = A (65), b = B (840). pieces_per_box = "Number of units arranged per box" = 19.
    // =========================================================================

    951 => [ // SF-FIB-01 softfelt-translucent-solid (RE-EMITIDO com e..h)
        'spec_column_labels' => [
            'a' => 'A',
            'b' => 'B',
            'e' => 'Slat thickness',
            'f' => 'Unfolded dimensions',
            'g' => 'Support thickness',
            'h' => 'Support dimensions',
        ],
        'specifications' => [
            ['code'=>'SF-FIB-01-0001','thickness'=>3,'a'=>65,'b'=>840,'c'=>'','d'=>'','e'=>3,'f'=>'170x2400','g'=>9,'h'=>'35x840','pieces_per_box'=>19,'coverage_area'=>'20,2 m²','pet_bottles'=>71],
        ],
    ],

    952 => [ // SF-PRN-01 softfelt-translucent-printed (RE-EMITIDO com e..h)
        'spec_column_labels' => [
            'a' => 'A',
            'b' => 'B',
            'e' => 'Slat thickness',
            'f' => 'Unfolded dimensions',
            'g' => 'Support thickness',
            'h' => 'Support dimensions',
        ],
        'specifications' => [
            ['code'=>'SF-PRN-01-0001','thickness'=>3,'a'=>65,'b'=>840,'c'=>'','d'=>'','e'=>3,'f'=>'170x2400','g'=>9,'h'=>'35x840','pieces_per_box'=>19,'coverage_area'=>'20,2 m²','pet_bottles'=>71],
        ],
    ],

    // =========================================================================
    // WALL - PARQUET BLOCKS  (wall_7-parquet-blocks)
    // PDF rotula "Height 1" e "Length 1": a = Height, b = Length.
    // =========================================================================

    954 => [ // TC-TCS-09 parquet-blocks-printed
        'spec_column_labels' => ['a' => 'Height', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'TC-TCS-09-0001','thickness'=>9,'a'=>60,'b'=>310,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>180,'coverage_area'=>'3,348 m²','pet_bottles'=>2],
        ],
    ],

    // =========================================================================
    // WALL - GRANILITE  (wall_8-granilite)
    // PDF rotula "Height A" e "Length B": a = Height, b = Length.
    // Duas espessuras (25 e 50 mm).
    // =========================================================================

    955 => [ // GR-GRN-25 granilite
        'spec_column_labels' => ['a' => 'Height', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'GR-GRN-25-0001','thickness'=>25,'a'=>60,'b'=>300,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>40,'coverage_area'=>'0,72 m²','pet_bottles'=>1],
            ['code'=>'GR-GRN-50-0001','thickness'=>50,'a'=>60,'b'=>300,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>40,'coverage_area'=>'0,72 m²','pet_bottles'=>2],
        ],
    ],

    // =========================================================================
    // WALL - PARAMETRIC  (wall_9-parametric)
    // PDF rotula "Wall Height" / "Wall Width": a = Wall Height, b = Wall Width.
    // Coverage Area no PDF aparece como "4,40" (sem unidade) - assumimos m².
    // =========================================================================

    956 => [ // PR-PRM-09 parametric-solid
        'spec_column_labels' => ['a' => 'Wall Height', 'b' => 'Wall Width'],
        'specifications' => [
            ['code'=>'PR-PRM-09-0001','thickness'=>9,'a'=>2800,'b'=>300,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>40,'coverage_area'=>'4,40 m²','pet_bottles'=>94],
        ],
    ],

    957 => [ // PR-PRT-09 parametric-printed
        'spec_column_labels' => ['a' => 'Wall Height', 'b' => 'Wall Width'],
        'specifications' => [
            ['code'=>'PR-PRT-09-0001','thickness'=>9,'a'=>2800,'b'=>300,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>40,'coverage_area'=>'4,40 m²','pet_bottles'=>94],
        ],
    ],

    // =========================================================================
    // WALL - MASHRABIYA  (wall_10-mashrabiya)
    // PDF rotula "Height A" / "Length B": a = Height, b = Length.
    // CB-SOL (solid cut) NAO tem PET no PDF -> pet_bottles=''.
    // Demais variantes (PRI/MOL/MPI) trazem PET 376 (9mm) e 404 (50mm).
    // =========================================================================

    917 => [ // CB-SOL-09 mashrabiya-solid-cut
        'spec_column_labels' => ['a' => 'Height', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'CB-SOL-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>''],
            ['code'=>'CB-SOL-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>''],
        ],
    ],

    918 => [ // CB-PRI-09 mashrabiya-printed
        'spec_column_labels' => ['a' => 'Height', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'CB-PRI-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>376],
            ['code'=>'CB-PRI-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>404],
        ],
    ],

    919 => [ // CB-MOL-09 mashrabiya-molded-solid-cut
        'spec_column_labels' => ['a' => 'Height', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'CB-MOL-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>376],
            ['code'=>'CB-MOL-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>404],
        ],
    ],

    920 => [ // CB-MPI-09 mashrabiya-molded-printed-cut
        'spec_column_labels' => ['a' => 'Height', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'CB-MPI-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>376],
            ['code'=>'CB-MPI-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>404],
        ],
    ],

    // =========================================================================
    // WALL - REVEST FORM METALLIC  (wall_11-revest_form_metallic)
    // =========================================================================

    921 => [ // RF-MEL-03 revest-form-metallic (variante "base" - 4 thicknesses)
        // PDF: A=2800. B=1400 (espessuras 3/28/53) e B=1200 (espessura 9).
        // PDF nao traz coluna UNIT preenchida; assumimos 1.
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-MEL-03-0001','thickness'=>3, 'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>438],
            ['code'=>'RF-MEL-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>355],
            ['code'=>'RF-MEL-28-0001','thickness'=>28,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>181],
            ['code'=>'RF-MEL-53-0001','thickness'=>53,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>343],
        ],
    ],

    924 => [ // RF-MTC-09 revest-form-metallic-slatted-v-cut
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-MTC-09-0001','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
        ],
    ],

    925 => [ // RF-MMO-03 revest-form-metallic-molded-stone
        // PDF: A=2700, B=1200, thickness 3/28/53. Coverage area no PDF eh "3,24" sem unidade -> normalizamos para m².
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-MMO-03-0001','thickness'=>3, 'a'=>2700,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>114],
            ['code'=>'RF-MMO-28-0001','thickness'=>28,'a'=>2700,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>167],
            ['code'=>'RF-MMO-53-0001','thickness'=>53,'a'=>2700,'b'=>1200,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>1,'coverage_area'=>'3,24 m²','pet_bottles'=>334],
        ],
    ],

    // =========================================================================
    // WALL - GREEN WALL  (wall_12-green-wall)
    // PDF rotula "Height 1" / "Length 1": a = Height, b = Length.
    // Espessura 2 mm. Dimensoes 2000 x 1000.
    // =========================================================================

    926 => [ // GW-GRW-01 green-wall
        'spec_column_labels' => ['a' => 'Height', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'GW-GRW-01-0001','thickness'=>2,'a'=>2000,'b'=>1000,'c'=>'','d'=>'','e'=>'','f'=>'','g'=>'','h'=>'','pieces_per_box'=>114,'coverage_area'=>'2 m²','pet_bottles'=>71],
        ],
    ],

];

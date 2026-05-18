<?php
// Mapa de correcoes gerado pela auditoria 2026-05-18.
// Fonte: /tmp/trisoft-audit/*.txt (texto extraido dos PDFs Trisoft).
// Aplicado via: php scripts/apply_fixes_2026-05-18.php
//
// Convencao: chaves geometricas usam 'a','b','c','d' para compatibilidade.
// 'spec_column_labels' so traz chaves com label semantico nao-padrao.
// 'thickness' eh int quando puramente numerico; string quando vier "N25E10" etc.
// Strings vazias representam colunas nao aplicaveis a este produto.

return [

    // =========================================================================
    // CLOUDS - CLASSIC CIRCULAR  (clouds_5-classic-circular)
    // =========================================================================

    880 => [ // NC-CIR-50 cloud-classic-circular-solid
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NC-CIR-50-0001','thickness'=>50,'a'=>500, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>26],
            ['code'=>'NC-CIR-50-0002','thickness'=>50,'a'=>800, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>66],
            ['code'=>'NC-CIR-50-0003','thickness'=>50,'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>148],
        ],
    ],

    881 => [ // NC-CHR-50 cloud-classic-circular-solid-high-relief
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NC-CHR-50-0001','thickness'=>50,'a'=>500, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>26],
            ['code'=>'NC-CHR-50-0002','thickness'=>50,'a'=>800, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>66],
            ['code'=>'NC-CHR-50-0003','thickness'=>50,'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>148],
        ],
    ],

    882 => [ // ND-CIR-50 cloud-classic-circular-decor-printed
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'ND-CIR-50-0001','thickness'=>50,'a'=>500, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>26],
            ['code'=>'ND-CIR-50-0002','thickness'=>50,'a'=>800, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>66],
            ['code'=>'ND-CIR-50-0003','thickness'=>50,'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>148],
        ],
    ],

    883 => [ // ND-CHR-50 cloud-classic-circular-decor-printed-high-relief
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'ND-CHR-50-0001','thickness'=>50,'a'=>500, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>26],
            ['code'=>'ND-CHR-50-0002','thickness'=>50,'a'=>800, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>66],
            ['code'=>'ND-CHR-50-0003','thickness'=>50,'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'','pet_bottles'=>148],
        ],
    ],

    // =========================================================================
    // CLOUDS - FORM CIRCULAR  (clouds_11-form-circular)
    // Obs.: o catalogo agrupa multiplas espessuras (09/25/50) no mesmo "SKU prefix".
    // =========================================================================

    841 => [ // NF-CIR-09 cloud-form-circular-solid
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NF-CIR-09-0001','thickness'=>9, 'a'=>620, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>43],
            ['code'=>'NF-CIR-09-0003','thickness'=>9, 'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>161],
            ['code'=>'NF-CIR-25-0001','thickness'=>25,'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>25],
            ['code'=>'NF-CIR-25-0003','thickness'=>25,'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>101],
            ['code'=>'NF-CIR-50-0001','thickness'=>50,'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>50],
            ['code'=>'NF-CIR-50-0003','thickness'=>50,'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>202],
        ],
    ],

    842 => [ // NF-CPR-09 cloud-form-circular-printed
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NF-CPR-09-0001','thickness'=>9, 'a'=>620, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>43],
            ['code'=>'NF-CPR-09-0003','thickness'=>9, 'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>161],
            ['code'=>'NF-CPR-25-0001','thickness'=>25,'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>25],
            ['code'=>'NF-CPR-25-0003','thickness'=>25,'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>101],
            ['code'=>'NF-CPR-50-0001','thickness'=>50,'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>50],
            ['code'=>'NF-CPR-50-0003','thickness'=>50,'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>202],
        ],
    ],

    843 => [ // NF-CEG-09 cloud-form-circular-solid-engraved
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NF-CEG-09-0001','thickness'=>9,        'a'=>620, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>43],
            ['code'=>'NF-CEG-09-0003','thickness'=>9,        'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>161],
            ['code'=>'NF-CEG-10-0001','thickness'=>'N25E10', 'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>25],
            ['code'=>'NF-CEG-10-0003','thickness'=>'N25E10', 'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>101],
            ['code'=>'NF-CEG-20-0001','thickness'=>'N50E20', 'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>50],
            ['code'=>'NF-CEG-20-0003','thickness'=>'N50E20', 'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>202],
        ],
    ],

    844 => [ // NF-CPE-09 cloud-form-circular-printed-engraved
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NF-CPE-09-0001','thickness'=>9,        'a'=>620, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>43],
            ['code'=>'NF-CPE-09-0003','thickness'=>9,        'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>161],
            ['code'=>'NF-CPE-10-0001','thickness'=>'N25E10', 'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>25],
            ['code'=>'NF-CPE-10-0003','thickness'=>'N25E10', 'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>101],
            ['code'=>'NF-CPE-20-0001','thickness'=>'N50E20', 'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>50],
            ['code'=>'NF-CPE-20-0003','thickness'=>'N50E20', 'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>202],
        ],
    ],

    845 => [ // NF-CCT-09 cloud-form-circular-solid-cut
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NF-CCT-09-0001','thickness'=>9, 'a'=>620, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>43],
            ['code'=>'NF-CCT-09-0003','thickness'=>9, 'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>161],
            ['code'=>'NF-CCT-25-0001','thickness'=>25,'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>25],
            ['code'=>'NF-CCT-25-0003','thickness'=>25,'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>101],
            ['code'=>'NF-CCT-50-0001','thickness'=>50,'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>50],
            ['code'=>'NF-CCT-50-0003','thickness'=>50,'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>202],
        ],
    ],

    846 => [ // NF-CPC-09 cloud-form-circular-printed-cut
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NF-CPC-09-0001','thickness'=>9, 'a'=>620,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>43],
            ['code'=>'NF-CPC-25-0001','thickness'=>25,'a'=>700,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>25],
            ['code'=>'NF-CPC-50-0001','thickness'=>50,'a'=>700,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>50],
        ],
    ],

    847 => [ // NF-CFM-09 cloud-form-circular-solid-fire-mark
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NF-CFM-09-0001','thickness'=>9, 'a'=>620, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>43],
            ['code'=>'NF-CFM-09-0003','thickness'=>9, 'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>161],
            ['code'=>'NF-CFM-25-0001','thickness'=>25,'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>25],
            ['code'=>'NF-CFM-25-0003','thickness'=>25,'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>101],
            ['code'=>'NF-CFM-50-0001','thickness'=>50,'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>50],
            ['code'=>'NF-CFM-50-0003','thickness'=>50,'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>202],
        ],
    ],

    848 => [ // NF-CMO-09 cloud-form-circular-solid-molded
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NF-CMO-09-0001','thickness'=>9, 'a'=>620, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>43],
            ['code'=>'NF-CMO-09-0003','thickness'=>9, 'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>161],
            ['code'=>'NF-CMO-25-0001','thickness'=>25,'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>25],
            ['code'=>'NF-CMO-25-0003','thickness'=>25,'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>101],
            ['code'=>'NF-CMO-50-0001','thickness'=>50,'a'=>700, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>50],
            ['code'=>'NF-CMO-50-0003','thickness'=>50,'a'=>1400,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>202],
        ],
    ],

    849 => [ // NF-CVC-09 cloud-form-circular-solid-v-cut
        'spec_column_labels' => ['a' => 'Diameter'],
        'specifications' => [
            ['code'=>'NF-CVC-09-0001','thickness'=>9,'a'=>620, 'b'=>'','c'=>'','d'=>'','pieces_per_box'=>8,'coverage_area'=>'','pet_bottles'=>43],
            ['code'=>'NF-CVC-09-0003','thickness'=>9,'a'=>1200,'b'=>'','c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>'','pet_bottles'=>161],
        ],
    ],

    // =========================================================================
    // CLOUDS - FORM FLY  (clouds_13-form-fly)
    // PDF rotula somente "A" e "B" (sem nome semantico); diagrama no PDF mostra
    // A como dimensao principal e B perpendicular. Mantemos labels genericos.
    // =========================================================================

    859 => [ // NF-FLY-02 cloud-form-fly-solid
        // Sem spec_column_labels: PDF usa apenas "A" / "B" sem nome semantico.
        'specifications' => [
            ['code'=>'NF-FLY-02-0002','thickness'=>9,'a'=>1200,'b'=>540,'c'=>'','d'=>'','pieces_per_box'=>5,'coverage_area'=>'','pet_bottles'=>72],
        ],
    ],

    // =========================================================================
    // CLOUDS - SOFTFELT  (clouds_15-softfelt)
    // PDF rotula "A" / "B" / "C" sem nome semantico (referem-se ao diagrama).
    // Inclui coluna Coverage Area em m^2.
    // =========================================================================

    862 => [ // NS-SFC-03 cloud-softfelt-circular-solid
        'specifications' => [
            ['code'=>'NS-SFC-03-0001','thickness'=>3,'a'=>50,'b'=>850,'c'=>760,'d'=>'','pieces_per_box'=>34,'coverage_area'=>'1,292 m²','pet_bottles'=>51],
        ],
    ],

    863 => [ // NS-CPR-03 cloud-softfelt-circular-printed
        'specifications' => [
            ['code'=>'NS-CPR-03-0001','thickness'=>3,'a'=>50,'b'=>850,'c'=>760,'d'=>'','pieces_per_box'=>34,'coverage_area'=>'1,292 m²','pet_bottles'=>51],
        ],
    ],

    864 => [ // NS-RPR-03 cloud-softfelt-rectangular-solid
        // Atencao: no PDF este codigo aparece sob "RECTANGULAR / SOLID".
        'specifications' => [
            ['code'=>'NS-RPR-03-0001','thickness'=>3,'a'=>50,'b'=>1200,'c'=>820,'d'=>'','pieces_per_box'=>18,'coverage_area'=>'1,968 m²','pet_bottles'=>39],
        ],
    ],

    865 => [ // NS-SFR-03 cloud-softfelt-rectangular-printed
        // Atencao: no PDF este codigo aparece sob "RECTANGULAR / PRINTED".
        'specifications' => [
            ['code'=>'NS-SFR-03-0001','thickness'=>3,'a'=>50,'b'=>1200,'c'=>820,'d'=>'','pieces_per_box'=>18,'coverage_area'=>'1,968 m²','pet_bottles'=>39],
        ],
    ],

    // =========================================================================
    // WALL - REVEST FORM  (wall_4-revest_form)
    // Geometria padrao: A x B (Largura x Comprimento de placa).
    // =========================================================================

    932 => [ // RF-FOR-09 revest-form-solid
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-FOR-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RF-FOR-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>202],
            ['code'=>'RF-FOR-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>404],
        ],
    ],

    934 => [ // RF-COB-09 revest-form-solid-cut
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-COB-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RF-COB-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>202],
            ['code'=>'RF-COB-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>404],
        ],
    ],

    935 => [ // RF-FFM-09 revest-form-solid-fire-mark
        // Obs.: linha RF-FFM-09 do PDF traz thickness=3 (provavel erro do catalogo, mantido fielmente).
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-FFM-09-0001','thickness'=>3, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>119],
            ['code'=>'RF-FFM-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>202],
            ['code'=>'RF-FFM-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>404],
        ],
    ],

    939 => [ // RF-PRI-09 revest-form-printed
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-PRI-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>362],
            ['code'=>'RF-PRI-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>167],
            ['code'=>'RF-PRI-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>334],
        ],
    ],

    941 => [ // RP-COB-09 revest-form-printed-cut
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RP-COB-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RP-COB-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>173],
            ['code'=>'RP-COB-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>404],
        ],
    ],

    940 => [ // RP-PEG-03 revest-form-printed-engraved
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RP-PEG-03-0001','thickness'=>3,        'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>138],
            ['code'=>'RP-PEG-09-0001','thickness'=>9,        'a'=>2800,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RP-PEG-10-0001','thickness'=>'N25E10', 'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>202],
            ['code'=>'RP-PEG-20-0001','thickness'=>'N50E20', 'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>404],
        ],
    ],

    944 => [ // RP-RFL-09 revest-form-slatted-solid-v-cut
        // PDF inclui coluna extra "V-cut spacing" (30 / 50 / 100 mm) -> mapeada em 'd'.
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length', 'd' => 'V-cut spacing'],
        'specifications' => [
            ['code'=>'RP-RFL-09-0001','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>30, 'pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RP-RFL-09-0002','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>50, 'pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>173],
            ['code'=>'RP-RFL-09-0003','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>100,'pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>346],
        ],
    ],

    947 => [ // RP-RFL-09-8EFE revest-form-slatted-printed-v-cut
        // PDF (pagina 45): "REVEST FORM SLATTED / PRINTED + V-CUT".
        // Os codigos no PDF reusam RP-RFL-09-XXXX; no DB ficaram com sufixo "-8EFE"
        // para diferenciar do solid (id 944). Mantemos os codigos originais do PDF.
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length', 'd' => 'V-cut spacing'],
        'specifications' => [
            ['code'=>'RP-RFL-09-0001','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>30, 'pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RP-RFL-09-0002','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>50, 'pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RP-RFL-09-0003','thickness'=>9,'a'=>2800,'b'=>1200,'c'=>'','d'=>100,'pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
        ],
    ],

    // =========================================================================
    // WALL - REVEST FORM METALLIC  (wall_11-revest_form_metallic)
    // =========================================================================

    923 => [ // RF-MEC-09 revest-form-metallic-cut
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-MEC-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RF-MEC-25-0001','thickness'=>25,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>191],
            ['code'=>'RF-MEC-50-0001','thickness'=>50,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>361],
        ],
    ],

    922 => [ // RF-MEG-09 revest-form-metallic-engraved
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'RF-MEG-09-0001','thickness'=>9, 'a'=>2800,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,36 m²','pet_bottles'=>376],
            ['code'=>'RF-MEG-28-0001','thickness'=>28,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>191],
            ['code'=>'RF-MEG-53-0001','thickness'=>53,'a'=>2800,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>1,'coverage_area'=>'3,92 m²','pet_bottles'=>361],
        ],
    ],

    // =========================================================================
    // WALL - REVEST NESS BRICKS  (wall_3-revest_ness)
    // BRICK: o catalogo combina duas geometrias (cubica A x B, e tijolo A x C)
    // sob o mesmo prefixo SKU. Mapeamos como duas linhas (-0001 cubo, -0002 tijolo).
    // a = Width, b = Length (cubo), c = Length (formato tijolo).
    // =========================================================================

    931 => [ // BR-BCK-25 revest-ness-bricks-solid
        'spec_column_labels' => ['a' => 'Width', 'b' => 'Length (cube)', 'c' => 'Length (brick)'],
        'specifications' => [
            ['code'=>'BR-BCK-25-0001','thickness'=>25,'a'=>200,'b'=>200,'c'=>'',   'd'=>'','pieces_per_box'=>72,'coverage_area'=>'7,68 m²','pet_bottles'=>2],
            ['code'=>'BR-BCK-25-0002','thickness'=>25,'a'=>200,'b'=>'', 'c'=>400, 'd'=>'','pieces_per_box'=>60,'coverage_area'=>'7,68 m²','pet_bottles'=>2],
        ],
    ],

    // =========================================================================
    // WALL - SOFTFELT TRANSLUCENT  (wall_5-softfelt)
    // Tabela com colunas extras (slat thickness, unfolded dims, support thickness,
    // support dims). Mapeamos as dimensoes geometricas reais: a=A, b=B.
    // Demais metadados ficam descritos em coverage_area / labels.
    // =========================================================================

    951 => [ // SF-FIB-01 softfelt-translucent-solid
        'spec_column_labels' => ['a' => 'A', 'b' => 'B'],
        'specifications' => [
            ['code'=>'SF-FIB-01-0001','thickness'=>3,'a'=>65,'b'=>840,'c'=>'','d'=>'','pieces_per_box'=>19,'coverage_area'=>'20,2 m²','pet_bottles'=>71],
        ],
    ],

    952 => [ // SF-PRN-01 softfelt-translucent-printed
        'spec_column_labels' => ['a' => 'A', 'b' => 'B'],
        'specifications' => [
            ['code'=>'SF-PRN-01-0001','thickness'=>3,'a'=>65,'b'=>840,'c'=>'','d'=>'','pieces_per_box'=>19,'coverage_area'=>'20,2 m²','pet_bottles'=>71],
        ],
    ],

    // =========================================================================
    // WALL - WIDE PLANKS  (wall_6-wide-planks)
    // PDF rotula "Height 1" e "Length 1": a = Height, b = Length.
    // =========================================================================

    953 => [ // TL-TBL-09 wide-planks-printed
        'spec_column_labels' => ['a' => 'Height', 'b' => 'Length'],
        'specifications' => [
            ['code'=>'TL-TBL-09-0001','thickness'=>9,'a'=>1200,'b'=>150,'c'=>'','d'=>'','pieces_per_box'=>56,'coverage_area'=>'3,36 m²','pet_bottles'=>20],
        ],
    ],

];

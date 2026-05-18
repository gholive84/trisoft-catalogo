<?php
// =============================================================================
// Auditoria completa BAFFLE - 2026-05-18
// =============================================================================
//
// Fontes:
//   Estado atual (DB):  /tmp/trisoft-audit/all_specs.txt
//   Fonte da verdade:   /tmp/trisoft-audit/baffles_*.txt (texto extraido dos PDFs)
//   Lista de produtos:  /tmp/trisoft-audit/products_baffle.csv (70 produtos)
//
// Metodo:
//   Para cada um dos 70 produtos de Baffles, comparei valor a valor o estado do
//   DB (spec_layout, spec_column_labels, spec_schema, specifications) com a
//   tabela do PDF correspondente.
//
// Resultado:
//   58 produtos ja estao identicos ao PDF (numericos + layout + labels).
//   10 produtos divergem APENAS em thickness "N25 E 10"/"N50 E 20" (com espacos)
//   vs forma canonica "N25E10"/"N50E20" (sem espacos), conforme precedente do
//   fix_products_audit_2026-05-18.php (Nuvens).
//   2 anomalias preservadas (descritas abaixo).
//
// Layouts (mantidos como ja estao no DB):
//   - 'multi_piece' para todas as variantes de ARC (Classic e Form).
//   - 'simple' para Straight/Trapezium/Wave/Trapezium-Wave (Classic, Ness, Form).
//   - spec_column_labels = null em todos: PDFs usam "A"/"B"/"C" entre aspas como
//     header generico + diagrama, igual ao default da view simple.
//   - spec_schema = null em todos.
//
// Anomalias documentadas (mantidas no DB, sem fix porque ja refletem o PDF tal qual):
//   - id 1090 (BF-ARVC-09) linha "BF-ARVC-9-0003": typo do PDF (faltou o "0" da espessura
//     09 no codigo). Demais linhas usam "BF-ARVC-09-XXXX". DB preservou o typo do PDF.
//   - id 1092 (BF-ARPE-09) linha "BF-ARPE-9-0003": idem (typo do PDF preservado).
//   - id 1128 (BF-WFM-09) linha "BF-WFM-09-0007": o PDF erroneamente mostra
//     "BF-WCT-09-0007" no meio da tabela do BF-WFM-09 (typo). O DB ja
//     normalizou para "BF-WFM-09-0007". Mantido assim por ser obviamente o
//     codigo correto (todas as outras linhas do produto seguem BF-WFM-).
//
// IDs 1134 (BC-TRW-50), 1135 (BC-THR-50-9706 = trap.wave Solid HR), 1136 (BD-TRW-50)
// e 1137 (BD-THR-50-1BB9 = trap.wave Decor HR) foram conferidos contra o PDF
// baffles_8-classic-trapezium-wave.txt: todos OK, sem alteracao.
//
// =============================================================================

return [

    1086 => [ // BF-ARE-09 baffle-form-arc-solid-engraved
        'spec_layout' => 'multi_piece',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>"BF-ARE-09-0001",'thickness'=>9,'p1_a'=>232,'p1_b'=>1200,'p1_c'=>112,'p1_pieces'=>6,'p1_pet'=>37,'p2_a'=>232,'p2_b'=>1200,'p2_c'=>352,'p2_pieces'=>6,'pieces_per_box'=>12,'coverage_area'=>"3,20 m²",'pet_bottles'=>37],
            ['code'=>"BF-ARE-09-0002",'thickness'=>9,'p1_a'=>348,'p1_b'=>1200,'p1_c'=>228,'p1_pieces'=>4,'p1_pet'=>55,'p2_a'=>348,'p2_b'=>1200,'p2_c'=>468,'p2_pieces'=>4,'pieces_per_box'=>8,'coverage_area'=>"3,02 m²",'pet_bottles'=>55],
            ['code'=>"BF-ARE-09-0003",'thickness'=>9,'p1_a'=>464,'p1_b'=>1200,'p1_c'=>343,'p1_pieces'=>3,'p1_pet'=>72,'p2_a'=>464,'p2_b'=>1200,'p2_c'=>585,'p2_pieces'=>3,'pieces_per_box'=>6,'coverage_area'=>"2,86 m²",'pet_bottles'=>72],
            ['code'=>"BF-ARE-09-0004",'thickness'=>9,'p1_a'=>560,'p1_b'=>1200,'p1_c'=>437,'p1_pieces'=>3,'p1_pet'=>87,'p2_a'=>560,'p2_b'=>1200,'p2_c'=>680,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"2,75 m²",'pet_bottles'=>87],
            ['code'=>"BF-ARE-09-0005",'thickness'=>9,'p1_a'=>240,'p1_b'=>2800,'p1_c'=>120,'p1_pieces'=>3,'p1_pet'=>76,'p2_a'=>240,'p2_b'=>2800,'p2_c'=>364,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"2,83 m²",'pet_bottles'=>76],
            ['code'=>"BF-ARE-09-0006",'thickness'=>9,'p1_a'=>300,'p1_b'=>2800,'p1_c'=>197,'p1_pieces'=>2,'p1_pet'=>94,'p2_a'=>300,'p2_b'=>2800,'p2_c'=>400,'p2_pieces'=>2,'pieces_per_box'=>4,'coverage_area'=>"2,63 m²",'pet_bottles'=>94],
            ['code'=>"BF-ARE-09-0007",'thickness'=>9,'p1_a'=>400,'p1_b'=>2800,'p1_c'=>296,'p1_pieces'=>2,'p1_pet'=>126,'p2_a'=>400,'p2_b'=>2800,'p2_c'=>501,'p2_pieces'=>1,'pieces_per_box'=>3,'coverage_area'=>"2,32 m²",'pet_bottles'=>126],
            ['code'=>"BF-ARE-09-0008",'thickness'=>9,'p1_a'=>600,'p1_b'=>2800,'p1_c'=>470,'p1_pieces'=>1,'p1_pet'=>188,'p2_a'=>600,'p2_b'=>2800,'p2_c'=>730,'p2_pieces'=>1,'pieces_per_box'=>2,'coverage_area'=>"1,74 m²",'pet_bottles'=>188],
            ['code'=>"BF-ARE-10-0001",'thickness'=>"N25E10",'p1_a'=>232,'p1_b'=>1400,'p1_c'=>112,'p1_pieces'=>6,'p1_pet'=>17,'p2_a'=>232,'p2_b'=>1400,'p2_c'=>352,'p2_pieces'=>6,'pieces_per_box'=>12,'coverage_area'=>"3,99 m²",'pet_bottles'=>17],
            ['code'=>"BF-ARE-10-0002",'thickness'=>"N25E10",'p1_a'=>348,'p1_b'=>1400,'p1_c'=>228,'p1_pieces'=>4,'p1_pet'=>26,'p2_a'=>348,'p2_b'=>1400,'p2_c'=>468,'p2_pieces'=>4,'pieces_per_box'=>8,'coverage_area'=>"3,68 m²",'pet_bottles'=>26],
            ['code'=>"BF-ARE-10-0003",'thickness'=>"N25E10",'p1_a'=>464,'p1_b'=>1400,'p1_c'=>343,'p1_pieces'=>3,'p1_pet'=>34,'p2_a'=>464,'p2_b'=>1400,'p2_c'=>585,'p2_pieces'=>3,'pieces_per_box'=>6,'coverage_area'=>"3,46 m²",'pet_bottles'=>34],
            ['code'=>"BF-ARE-10-0004",'thickness'=>"N25E10",'p1_a'=>560,'p1_b'=>1400,'p1_c'=>437,'p1_pieces'=>3,'p1_pet'=>41,'p2_a'=>560,'p2_b'=>1400,'p2_c'=>680,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"3,30 m²",'pet_bottles'=>41],
            ['code'=>"BF-ARE-10-0005",'thickness'=>"N25E10",'p1_a'=>230,'p1_b'=>2800,'p1_c'=>115,'p1_pieces'=>3,'p1_pet'=>34,'p2_a'=>230,'p2_b'=>2800,'p2_c'=>350,'p2_pieces'=>3,'pieces_per_box'=>6,'coverage_area'=>"3,64 m²",'pet_bottles'=>34],
            ['code'=>"BF-ARE-10-0006",'thickness'=>"N25E10",'p1_a'=>280,'p1_b'=>2800,'p1_c'=>184,'p1_pieces'=>3,'p1_pet'=>41,'p2_a'=>280,'p2_b'=>2800,'p2_c'=>375,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"3,47 m²",'pet_bottles'=>41],
            ['code'=>"BF-ARE-10-0007",'thickness'=>"N25E10",'p1_a'=>350,'p1_b'=>2800,'p1_c'=>260,'p1_pieces'=>2,'p1_pet'=>51,'p2_a'=>350,'p2_b'=>2800,'p2_c'=>440,'p2_pieces'=>2,'pieces_per_box'=>4,'coverage_area'=>"3,22 m²",'pet_bottles'=>51],
            ['code'=>"BF-ARE-10-0008",'thickness'=>"N25E10",'p1_a'=>465,'p1_b'=>2800,'p1_c'=>363,'p1_pieces'=>2,'p1_pet'=>68,'p2_a'=>465,'p2_b'=>2800,'p2_c'=>570,'p2_pieces'=>1,'pieces_per_box'=>3,'coverage_area'=>"2,80 m²",'pet_bottles'=>68],
            ['code'=>"BF-ARE-20-0001",'thickness'=>"N50E20",'p1_a'=>232,'p1_b'=>1400,'p1_c'=>112,'p1_pieces'=>6,'p1_pet'=>17,'p2_a'=>232,'p2_b'=>1400,'p2_c'=>352,'p2_pieces'=>6,'pieces_per_box'=>12,'coverage_area'=>"4,41 m²",'pet_bottles'=>17],
            ['code'=>"BF-ARE-20-0002",'thickness'=>"N50E20",'p1_a'=>348,'p1_b'=>1400,'p1_c'=>228,'p1_pieces'=>4,'p1_pet'=>26,'p2_a'=>348,'p2_b'=>1400,'p2_c'=>468,'p2_pieces'=>4,'pieces_per_box'=>8,'coverage_area'=>"3,98 m²",'pet_bottles'=>26],
            ['code'=>"BF-ARE-20-0003",'thickness'=>"N50E20",'p1_a'=>464,'p1_b'=>1400,'p1_c'=>343,'p1_pieces'=>3,'p1_pet'=>34,'p2_a'=>464,'p2_b'=>1400,'p2_c'=>585,'p2_pieces'=>3,'pieces_per_box'=>6,'coverage_area'=>"3,67 m²",'pet_bottles'=>34],
            ['code'=>"BF-ARE-20-0004",'thickness'=>"N50E20",'p1_a'=>560,'p1_b'=>1400,'p1_c'=>437,'p1_pieces'=>3,'p1_pet'=>41,'p2_a'=>560,'p2_b'=>1400,'p2_c'=>680,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"3,49 m²",'pet_bottles'=>41],
            ['code'=>"BF-ARE-20-0005",'thickness'=>"N50E20",'p1_a'=>230,'p1_b'=>2800,'p1_c'=>115,'p1_pieces'=>3,'p1_pet'=>34,'p2_a'=>230,'p2_b'=>2800,'p2_c'=>350,'p2_pieces'=>3,'pieces_per_box'=>6,'coverage_area'=>"4,06 m²",'pet_bottles'=>34],
            ['code'=>"BF-ARE-20-0006",'thickness'=>"N50E20",'p1_a'=>280,'p1_b'=>2800,'p1_c'=>184,'p1_pieces'=>3,'p1_pet'=>41,'p2_a'=>280,'p2_b'=>2800,'p2_c'=>375,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"3,84 m²",'pet_bottles'=>41],
            ['code'=>"BF-ARE-20-0007",'thickness'=>"N50E20",'p1_a'=>350,'p1_b'=>2800,'p1_c'=>260,'p1_pieces'=>2,'p1_pet'=>51,'p2_a'=>350,'p2_b'=>2800,'p2_c'=>440,'p2_pieces'=>2,'pieces_per_box'=>4,'coverage_area'=>"3,50 m²",'pet_bottles'=>51],
            ['code'=>"BF-ARE-20-0008",'thickness'=>"N50E20",'p1_a'=>465,'p1_b'=>2800,'p1_c'=>363,'p1_pieces'=>2,'p1_pet'=>68,'p2_a'=>465,'p2_b'=>2800,'p2_c'=>570,'p2_pieces'=>1,'pieces_per_box'=>3,'coverage_area'=>"3,02 m²",'pet_bottles'=>68],
        ],
    ],

    1092 => [ // BF-ARPE-09 baffle-form-arc-printed-engraved
        'spec_layout' => 'multi_piece',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>"BF-ARPE-09-0001",'thickness'=>9,'p1_a'=>232,'p1_b'=>1200,'p1_c'=>112,'p1_pieces'=>6,'p1_pet'=>37,'p2_a'=>232,'p2_b'=>1200,'p2_c'=>352,'p2_pieces'=>6,'pieces_per_box'=>12,'coverage_area'=>"3,20 m²",'pet_bottles'=>28],
            ['code'=>"BF-ARPE-09-0002",'thickness'=>9,'p1_a'=>348,'p1_b'=>1200,'p1_c'=>228,'p1_pieces'=>4,'p1_pet'=>55,'p2_a'=>348,'p2_b'=>1200,'p2_c'=>468,'p2_pieces'=>4,'pieces_per_box'=>8,'coverage_area'=>"3,02 m²",'pet_bottles'=>37],
            ['code'=>"BF-ARPE-9-0003",'thickness'=>9,'p1_a'=>464,'p1_b'=>1200,'p1_c'=>343,'p1_pieces'=>3,'p1_pet'=>73,'p2_a'=>464,'p2_b'=>1200,'p2_c'=>585,'p2_pieces'=>3,'pieces_per_box'=>6,'coverage_area'=>"2,86 m²",'pet_bottles'=>46],
            ['code'=>"BF-ARPE-09-0004",'thickness'=>9,'p1_a'=>560,'p1_b'=>1200,'p1_c'=>437,'p1_pieces'=>3,'p1_pet'=>88,'p2_a'=>560,'p2_b'=>1200,'p2_c'=>680,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"2,75 m²",'pet_bottles'=>54],
            ['code'=>"BF-ARPE-09-0005",'thickness'=>9,'p1_a'=>240,'p1_b'=>2800,'p1_c'=>120,'p1_pieces'=>3,'p1_pet'=>72,'p2_a'=>240,'p2_b'=>2800,'p2_c'=>364,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"2,83 m²",'pet_bottles'=>55],
            ['code'=>"BF-ARPE-09-0006",'thickness'=>9,'p1_a'=>300,'p1_b'=>2800,'p1_c'=>197,'p1_pieces'=>2,'p1_pet'=>88,'p2_a'=>300,'p2_b'=>2800,'p2_c'=>400,'p2_pieces'=>2,'pieces_per_box'=>4,'coverage_area'=>"2,63 m²",'pet_bottles'=>59],
            ['code'=>"BF-ARPE-09-0007",'thickness'=>9,'p1_a'=>400,'p1_b'=>2800,'p1_c'=>296,'p1_pieces'=>2,'p1_pet'=>110,'p2_a'=>400,'p2_b'=>2800,'p2_c'=>501,'p2_pieces'=>1,'pieces_per_box'=>3,'coverage_area'=>"2,32 m²",'pet_bottles'=>69],
            ['code'=>"BF-ARPE-09-0008",'thickness'=>9,'p1_a'=>600,'p1_b'=>2800,'p1_c'=>470,'p1_pieces'=>1,'p1_pet'=>146,'p2_a'=>600,'p2_b'=>2800,'p2_c'=>730,'p2_pieces'=>1,'pieces_per_box'=>2,'coverage_area'=>"1,74 m²",'pet_bottles'=>90],
            ['code'=>"BF-ARPE-10-0001",'thickness'=>"N25E10",'p1_a'=>232,'p1_b'=>1400,'p1_c'=>112,'p1_pieces'=>6,'p1_pet'=>17,'p2_a'=>232,'p2_b'=>1400,'p2_c'=>352,'p2_pieces'=>6,'pieces_per_box'=>12,'coverage_area'=>"3,99 m²",'pet_bottles'=>13],
            ['code'=>"BF-ARPE-10-0002",'thickness'=>"N25E10",'p1_a'=>348,'p1_b'=>1400,'p1_c'=>228,'p1_pieces'=>4,'p1_pet'=>25,'p2_a'=>348,'p2_b'=>1400,'p2_c'=>468,'p2_pieces'=>4,'pieces_per_box'=>8,'coverage_area'=>"3,68 m²",'pet_bottles'=>17],
            ['code'=>"BF-ARPE-10-0003",'thickness'=>"N25E10",'p1_a'=>464,'p1_b'=>1400,'p1_c'=>343,'p1_pieces'=>3,'p1_pet'=>34,'p2_a'=>464,'p2_b'=>1400,'p2_c'=>585,'p2_pieces'=>3,'pieces_per_box'=>6,'coverage_area'=>"3,46 m²",'pet_bottles'=>22],
            ['code'=>"BF-ARPE-10-0004",'thickness'=>"N25E10",'p1_a'=>560,'p1_b'=>1400,'p1_c'=>437,'p1_pieces'=>3,'p1_pet'=>41,'p2_a'=>560,'p2_b'=>1400,'p2_c'=>680,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"3,30 m²",'pet_bottles'=>25],
            ['code'=>"BF-ARPE-10-0005",'thickness'=>"N25E10",'p1_a'=>230,'p1_b'=>2800,'p1_c'=>115,'p1_pieces'=>3,'p1_pet'=>34,'p2_a'=>230,'p2_b'=>2800,'p2_c'=>350,'p2_pieces'=>3,'pieces_per_box'=>6,'coverage_area'=>"3,64 m²",'pet_bottles'=>26],
            ['code'=>"BF-ARPE-10-0006",'thickness'=>"N25E10",'p1_a'=>280,'p1_b'=>2800,'p1_c'=>184,'p1_pieces'=>3,'p1_pet'=>41,'p2_a'=>280,'p2_b'=>2800,'p2_c'=>375,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"3,47 m²",'pet_bottles'=>28],
            ['code'=>"BF-ARPE-10-0007",'thickness'=>"N25E10",'p1_a'=>350,'p1_b'=>2800,'p1_c'=>260,'p1_pieces'=>2,'p1_pet'=>51,'p2_a'=>350,'p2_b'=>2800,'p2_c'=>440,'p2_pieces'=>2,'pieces_per_box'=>4,'coverage_area'=>"3,22 m²",'pet_bottles'=>32],
            ['code'=>"BF-ARPE-10-0008",'thickness'=>"N25E10",'p1_a'=>465,'p1_b'=>2800,'p1_c'=>363,'p1_pieces'=>2,'p1_pet'=>68,'p2_a'=>465,'p2_b'=>2800,'p2_c'=>570,'p2_pieces'=>1,'pieces_per_box'=>3,'coverage_area'=>"2,80 m²",'pet_bottles'=>42],
            ['code'=>"BF-ARPE-20-0001",'thickness'=>"N50E20",'p1_a'=>232,'p1_b'=>1400,'p1_c'=>112,'p1_pieces'=>6,'p1_pet'=>32,'p2_a'=>232,'p2_b'=>1400,'p2_c'=>352,'p2_pieces'=>6,'pieces_per_box'=>12,'coverage_area'=>"4,41 m²",'pet_bottles'=>24],
            ['code'=>"BF-ARPE-20-0002",'thickness'=>"N50E20",'p1_a'=>348,'p1_b'=>1400,'p1_c'=>228,'p1_pieces'=>4,'p1_pet'=>47,'p2_a'=>348,'p2_b'=>1400,'p2_c'=>468,'p2_pieces'=>4,'pieces_per_box'=>8,'coverage_area'=>"3,98 m²",'pet_bottles'=>32],
            ['code'=>"BF-ARPE-20-0003",'thickness'=>"N50E20",'p1_a'=>464,'p1_b'=>1400,'p1_c'=>343,'p1_pieces'=>3,'p1_pet'=>63,'p2_a'=>464,'p2_b'=>1400,'p2_c'=>585,'p2_pieces'=>3,'pieces_per_box'=>6,'coverage_area'=>"3,67 m²",'pet_bottles'=>40],
            ['code'=>"BF-ARPE-20-0004",'thickness'=>"N50E20",'p1_a'=>560,'p1_b'=>1400,'p1_c'=>437,'p1_pieces'=>3,'p1_pet'=>76,'p2_a'=>560,'p2_b'=>1400,'p2_c'=>680,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"3,49 m²",'pet_bottles'=>46],
            ['code'=>"BF-ARPE-20-0005",'thickness'=>"N50E20",'p1_a'=>230,'p1_b'=>2800,'p1_c'=>115,'p1_pieces'=>3,'p1_pet'=>76,'p2_a'=>230,'p2_b'=>2800,'p2_c'=>350,'p2_pieces'=>3,'pieces_per_box'=>6,'coverage_area'=>"4,06 m²",'pet_bottles'=>57],
            ['code'=>"BF-ARPE-20-0006",'thickness'=>"N50E20",'p1_a'=>280,'p1_b'=>2800,'p1_c'=>184,'p1_pieces'=>3,'p1_pet'=>94,'p2_a'=>280,'p2_b'=>2800,'p2_c'=>375,'p2_pieces'=>2,'pieces_per_box'=>5,'coverage_area'=>"3,84 m²",'pet_bottles'=>63],
            ['code'=>"BF-ARPE-20-0007",'thickness'=>"N50E20",'p1_a'=>350,'p1_b'=>2800,'p1_c'=>260,'p1_pieces'=>2,'p1_pet'=>126,'p2_a'=>350,'p2_b'=>2800,'p2_c'=>440,'p2_pieces'=>2,'pieces_per_box'=>4,'coverage_area'=>"3,50 m²",'pet_bottles'=>79],
            ['code'=>"BF-ARPE-20-0008",'thickness'=>"N50E20",'p1_a'=>465,'p1_b'=>2800,'p1_c'=>363,'p1_pieces'=>2,'p1_pet'=>188,'p2_a'=>465,'p2_b'=>2800,'p2_c'=>570,'p2_pieces'=>1,'pieces_per_box'=>3,'coverage_area'=>"3,02 m²",'pet_bottles'=>115],
        ],
    ],

    1100 => [ // BF-STEG-09 baffle-form-straight-solid-engraved
        'spec_layout' => 'simple',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>"BF-STEG-09-0001",'thickness'=>9,'a'=>200,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>14,'coverage_area'=>"3,29 m²",'pet_bottles'=>27],
            ['code'=>"BF-STEG-09-0002",'thickness'=>9,'a'=>310,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>9,'coverage_area'=>"3,08 m²",'pet_bottles'=>42],
            ['code'=>"BF-STEG-09-0003",'thickness'=>9,'a'=>400,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>7,'coverage_area'=>"2,96 m²",'pet_bottles'=>54],
            ['code'=>"BF-STEG-09-0004",'thickness'=>9,'a'=>560,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>5,'coverage_area'=>"2,75 m²",'pet_bottles'=>76],
            ['code'=>"BF-STEG-09-0005",'thickness'=>9,'a'=>200,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>6,'coverage_area'=>"2,97 m²",'pet_bottles'=>63],
            ['code'=>"BF-STEG-09-0006",'thickness'=>9,'a'=>300,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>4,'coverage_area'=>"2,63 m²",'pet_bottles'=>94],
            ['code'=>"BF-STEG-09-0007",'thickness'=>9,'a'=>400,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,32 m²",'pet_bottles'=>126],
            ['code'=>"BF-STEG-09-0008",'thickness'=>9,'a'=>600,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>"1,74 m²",'pet_bottles'=>188],
            ['code'=>"BF-STEG-10-0001",'thickness'=>"N25E10",'a'=>200,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>14,'coverage_area'=>"4,13 m²",'pet_bottles'=>15],
            ['code'=>"BF-STEG-10-0002",'thickness'=>"N25E10",'a'=>310,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>9,'coverage_area'=>"3,79 m²",'pet_bottles'=>23],
            ['code'=>"BF-STEG-10-0003",'thickness'=>"N25E10",'a'=>400,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>7,'coverage_area'=>"3,61 m²",'pet_bottles'=>29],
            ['code'=>"BF-STEG-10-0004",'thickness'=>"N25E10",'a'=>560,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,54 m²",'pet_bottles'=>41],
            ['code'=>"BF-STEG-10-0005",'thickness'=>"N25E10",'a'=>200,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>7,'coverage_area'=>"3,85 m²",'pet_bottles'=>29],
            ['code'=>"BF-STEG-10-0006",'thickness'=>"N25E10",'a'=>350,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,22 m²",'pet_bottles'=>51],
            ['code'=>"BF-STEG-10-0007",'thickness'=>"N25E10",'a'=>465,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,81 m²",'pet_bottles'=>68],
            ['code'=>"BF-STEG-10-0008",'thickness'=>"N25E10",'a'=>700,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>"2,10 m²",'pet_bottles'=>101],
            ['code'=>"BF-STEG-20-0001",'thickness'=>"N50E20",'a'=>200,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>14,'coverage_area'=>"4,62 m²",'pet_bottles'=>29],
            ['code'=>"BF-STEG-20-0002",'thickness'=>"N50E20",'a'=>310,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>9,'coverage_area'=>"4,10 m²",'pet_bottles'=>45],
            ['code'=>"BF-STEG-20-0003",'thickness'=>"N50E20",'a'=>400,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>7,'coverage_area'=>"3,85 m²",'pet_bottles'=>58],
            ['code'=>"BF-STEG-20-0004",'thickness'=>"N50E20",'a'=>560,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,49 m²",'pet_bottles'=>81],
            ['code'=>"BF-STEG-20-0005",'thickness'=>"N50E20",'a'=>200,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>7,'coverage_area'=>"4,34 m²",'pet_bottles'=>58],
            ['code'=>"BF-STEG-20-0006",'thickness'=>"N50E20",'a'=>350,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,50 m²",'pet_bottles'=>101],
            ['code'=>"BF-STEG-20-0007",'thickness'=>"N50E20",'a'=>465,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>3,'coverage_area'=>"3,02 m²",'pet_bottles'=>135],
            ['code'=>"BF-STEG-20-0008",'thickness'=>"N50E20",'a'=>700,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>"2,24 m²",'pet_bottles'=>202],
        ],
    ],

    1106 => [ // BF-STPE-09 baffle-form-straight-printed-engraved
        'spec_layout' => 'simple',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>"BF-STPE-09-0001",'thickness'=>9,'a'=>200,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>14,'coverage_area'=>"3,29 m²",'pet_bottles'=>27],
            ['code'=>"BF-STPE-09-0002",'thickness'=>9,'a'=>310,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>9,'coverage_area'=>"3,08 m²",'pet_bottles'=>42],
            ['code'=>"BF-STPE-09-0003",'thickness'=>9,'a'=>400,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>7,'coverage_area'=>"2,96 m²",'pet_bottles'=>54],
            ['code'=>"BF-STPE-09-0004",'thickness'=>9,'a'=>560,'b'=>1200,'c'=>'','d'=>'','pieces_per_box'=>5,'coverage_area'=>"2,75 m²",'pet_bottles'=>76],
            ['code'=>"BF-STPE-09-0005",'thickness'=>9,'a'=>200,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>6,'coverage_area'=>"2,97 m²",'pet_bottles'=>63],
            ['code'=>"BF-STPE-09-0006",'thickness'=>9,'a'=>300,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>4,'coverage_area'=>"2,63 m²",'pet_bottles'=>94],
            ['code'=>"BF-STPE-09-0007",'thickness'=>9,'a'=>400,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,32 m²",'pet_bottles'=>126],
            ['code'=>"BF-STPE-09-0008",'thickness'=>9,'a'=>600,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>"1,74 m²",'pet_bottles'=>188],
            ['code'=>"BF-STPE-10-0001",'thickness'=>"N25E10",'a'=>200,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>14,'coverage_area'=>"4,13 m²",'pet_bottles'=>15],
            ['code'=>"BF-STPE-10-0002",'thickness'=>"N25E10",'a'=>310,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>9,'coverage_area'=>"3,79 m²",'pet_bottles'=>23],
            ['code'=>"BF-STPE-10-0003",'thickness'=>"N25E10",'a'=>400,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>7,'coverage_area'=>"3,61 m²",'pet_bottles'=>29],
            ['code'=>"BF-STPE-10-0004",'thickness'=>"N25E10",'a'=>560,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,54 m²",'pet_bottles'=>41],
            ['code'=>"BF-STPE-10-0005",'thickness'=>"N25E10",'a'=>200,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>7,'coverage_area'=>"3,85 m²",'pet_bottles'=>29],
            ['code'=>"BF-STPE-10-0006",'thickness'=>"N25E10",'a'=>350,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,22 m²",'pet_bottles'=>51],
            ['code'=>"BF-STPE-10-0007",'thickness'=>"N25E10",'a'=>465,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,81 m²",'pet_bottles'=>68],
            ['code'=>"BF-STPE-10-0008",'thickness'=>"N25E10",'a'=>700,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>"2,10 m²",'pet_bottles'=>101],
            ['code'=>"BF-STPE-20-0001",'thickness'=>"N50E20",'a'=>200,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>14,'coverage_area'=>"4,62 m²",'pet_bottles'=>29],
            ['code'=>"BF-STPE-20-0002",'thickness'=>"N50E20",'a'=>310,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>9,'coverage_area'=>"4,10 m²",'pet_bottles'=>45],
            ['code'=>"BF-STPE-20-0003",'thickness'=>"N50E20",'a'=>400,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>7,'coverage_area'=>"3,85 m²",'pet_bottles'=>58],
            ['code'=>"BF-STPE-20-0004",'thickness'=>"N50E20",'a'=>560,'b'=>1400,'c'=>'','d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,49 m²",'pet_bottles'=>81],
            ['code'=>"BF-STPE-20-0005",'thickness'=>"N50E20",'a'=>200,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>7,'coverage_area'=>"4,34 m²",'pet_bottles'=>58],
            ['code'=>"BF-STPE-20-0006",'thickness'=>"N50E20",'a'=>350,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,50 m²",'pet_bottles'=>101],
            ['code'=>"BF-STPE-20-0007",'thickness'=>"N50E20",'a'=>465,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>3,'coverage_area'=>"3,02 m²",'pet_bottles'=>135],
            ['code'=>"BF-STPE-20-0008",'thickness'=>"N50E20",'a'=>700,'b'=>2800,'c'=>'','d'=>'','pieces_per_box'=>2,'coverage_area'=>"2,24 m²",'pet_bottles'=>202],
        ],
    ],

    1113 => [ // BF-TRE-09 baffle-form-trapezium-solid-engraved
        'spec_layout' => 'simple',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>"BF-TRE-09-0001",'thickness'=>9,'a'=>110,'b'=>1200,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"4,02 m²",'pet_bottles'=>15],
            ['code'=>"BF-TRE-09-0002",'thickness'=>9,'a'=>200,'b'=>1200,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"3,73 m²",'pet_bottles'=>27],
            ['code'=>"BF-TRE-09-0003",'thickness'=>9,'a'=>300,'b'=>1200,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"3,46 m²",'pet_bottles'=>41],
            ['code'=>"BF-TRE-09-0004",'thickness'=>9,'a'=>500,'b'=>1200,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"2,94 m²",'pet_bottles'=>68],
            ['code'=>"BF-TRE-09-0005",'thickness'=>9,'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,14 m²",'pet_bottles'=>32],
            ['code'=>"BF-TRE-09-0006",'thickness'=>9,'a'=>250,'b'=>2800,'c'=>350,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,05 m²",'pet_bottles'=>79],
            ['code'=>"BF-TRE-09-0007",'thickness'=>9,'a'=>330,'b'=>2800,'c'=>430,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,49 m²",'pet_bottles'=>104],
            ['code'=>"BF-TRE-09-0008",'thickness'=>9,'a'=>550,'b'=>2800,'c'=>650,'d'=>'','pieces_per_box'=>2,'coverage_area'=>"1,88 m²",'pet_bottles'=>173],
            ['code'=>"BF-TRE-10-0001",'thickness'=>"N25E10",'a'=>110,'b'=>1400,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"5,30 m²",'pet_bottles'=>8],
            ['code'=>"BF-TRE-10-0002",'thickness'=>"N25E10",'a'=>200,'b'=>1400,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"4,59 m²",'pet_bottles'=>15],
            ['code'=>"BF-TRE-10-0003",'thickness'=>"N25E10",'a'=>300,'b'=>1400,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,20 m²",'pet_bottles'=>22],
            ['code'=>"BF-TRE-10-0004",'thickness'=>"N25E10",'a'=>500,'b'=>1400,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,54 m²",'pet_bottles'=>37],
            ['code'=>"BF-TRE-10-0005",'thickness'=>"N25E10",'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>9,'coverage_area'=>"5,11 m²",'pet_bottles'=>15],
            ['code'=>"BF-TRE-10-0006",'thickness'=>"N25E10",'a'=>220,'b'=>2800,'c'=>320,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,93 m²",'pet_bottles'=>32],
            ['code'=>"BF-TRE-10-0007",'thickness'=>"N25E10",'a'=>300,'b'=>2800,'c'=>400,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,64 m²",'pet_bottles'=>44],
            ['code'=>"BF-TRE-10-0008",'thickness'=>"N25E10",'a'=>400,'b'=>2800,'c'=>500,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"3,01 m²",'pet_bottles'=>58],
            ['code'=>"BF-TRE-20-0001",'thickness'=>"N50E20",'a'=>110,'b'=>1400,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"5,89 m²",'pet_bottles'=>16],
            ['code'=>"BF-TRE-20-0002",'thickness'=>"N50E20",'a'=>200,'b'=>1400,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"4,97 m²",'pet_bottles'=>29],
            ['code'=>"BF-TRE-20-0003",'thickness'=>"N50E20",'a'=>300,'b'=>1400,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,48 m²",'pet_bottles'=>44],
            ['code'=>"BF-TRE-20-0004",'thickness'=>"N50E20",'a'=>500,'b'=>1400,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,71 m²",'pet_bottles'=>73],
            ['code'=>"BF-TRE-20-0005",'thickness'=>"N50E20",'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>9,'coverage_area'=>"5,74 m²",'pet_bottles'=>29],
            ['code'=>"BF-TRE-20-0006",'thickness'=>"N50E20",'a'=>220,'b'=>2800,'c'=>320,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"4,28 m²",'pet_bottles'=>64],
            ['code'=>"BF-TRE-20-0007",'thickness'=>"N50E20",'a'=>300,'b'=>2800,'c'=>400,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,92 m²",'pet_bottles'=>87],
            ['code'=>"BF-TRE-20-0008",'thickness'=>"N50E20",'a'=>400,'b'=>2800,'c'=>500,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"3,22 m²",'pet_bottles'=>116],
        ],
    ],

    1119 => [ // BF-TEP-09 baffle-form-trapezium-printed-engraved
        'spec_layout' => 'simple',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>"BF-TEP-09-0001",'thickness'=>9,'a'=>110,'b'=>1200,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"4,02 m²",'pet_bottles'=>15],
            ['code'=>"BF-TEP-09-0002",'thickness'=>9,'a'=>200,'b'=>1200,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"3,73 m²",'pet_bottles'=>27],
            ['code'=>"BF-TEP-09-0003",'thickness'=>9,'a'=>300,'b'=>1200,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"3,46 m²",'pet_bottles'=>41],
            ['code'=>"BF-TEP-09-0004",'thickness'=>9,'a'=>500,'b'=>1200,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"2,94 m²",'pet_bottles'=>68],
            ['code'=>"BF-TEP-09-0005",'thickness'=>9,'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,14 m²",'pet_bottles'=>32],
            ['code'=>"BF-TEP-09-0006",'thickness'=>9,'a'=>250,'b'=>2800,'c'=>350,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,05 m²",'pet_bottles'=>79],
            ['code'=>"BF-TEP-09-0007",'thickness'=>9,'a'=>330,'b'=>2800,'c'=>430,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,49 m²",'pet_bottles'=>104],
            ['code'=>"BF-TEP-09-0008",'thickness'=>9,'a'=>550,'b'=>2800,'c'=>650,'d'=>'','pieces_per_box'=>2,'coverage_area'=>"1,88 m²",'pet_bottles'=>173],
            ['code'=>"BF-TEP-10-0001",'thickness'=>"N25E10",'a'=>110,'b'=>1400,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"5,30 m²",'pet_bottles'=>8],
            ['code'=>"BF-TEP-10-0002",'thickness'=>"N25E10",'a'=>200,'b'=>1400,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"4,59 m²",'pet_bottles'=>15],
            ['code'=>"BF-TEP-10-0003",'thickness'=>"N25E10",'a'=>300,'b'=>1400,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,20 m²",'pet_bottles'=>22],
            ['code'=>"BF-TEP-10-0004",'thickness'=>"N25E10",'a'=>500,'b'=>1400,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,54 m²",'pet_bottles'=>37],
            ['code'=>"BF-TEP-10-0005",'thickness'=>"N25E10",'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>9,'coverage_area'=>"5,11 m²",'pet_bottles'=>15],
            ['code'=>"BF-TEP-10-0006",'thickness'=>"N25E10",'a'=>220,'b'=>2800,'c'=>320,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,93 m²",'pet_bottles'=>32],
            ['code'=>"BF-TEP-10-0007",'thickness'=>"N25E10",'a'=>300,'b'=>2800,'c'=>400,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,64 m²",'pet_bottles'=>44],
            ['code'=>"BF-TEP-10-0008",'thickness'=>"N25E10",'a'=>400,'b'=>2800,'c'=>500,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"3,01 m²",'pet_bottles'=>58],
            ['code'=>"BF-TEP-20-0001",'thickness'=>"N50E20",'a'=>110,'b'=>1400,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"5,89 m²",'pet_bottles'=>16],
            ['code'=>"BF-TEP-20-0002",'thickness'=>"N50E20",'a'=>200,'b'=>1400,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"4,97 m²",'pet_bottles'=>29],
            ['code'=>"BF-TEP-20-0003",'thickness'=>"N50E20",'a'=>300,'b'=>1400,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,48 m²",'pet_bottles'=>44],
            ['code'=>"BF-TEP-20-0004",'thickness'=>"N50E20",'a'=>500,'b'=>1400,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,71 m²",'pet_bottles'=>73],
            ['code'=>"BF-TEP-20-0005",'thickness'=>"N50E20",'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>9,'coverage_area'=>"5,74 m²",'pet_bottles'=>29],
            ['code'=>"BF-TEP-20-0006",'thickness'=>"N50E20",'a'=>220,'b'=>2800,'c'=>320,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"4,28 m²",'pet_bottles'=>64],
            ['code'=>"BF-TEP-20-0007",'thickness'=>"N50E20",'a'=>300,'b'=>2800,'c'=>400,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,92 m²",'pet_bottles'=>87],
            ['code'=>"BF-TEP-20-0008",'thickness'=>"N50E20",'a'=>400,'b'=>2800,'c'=>500,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"3,22 m²",'pet_bottles'=>116],
        ],
    ],

    1126 => [ // BF-WEG-09 baffle-form-wave-solid-engraved
        'spec_layout' => 'simple',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>"BF-WEG-09-0001",'thickness'=>9,'a'=>110,'b'=>1200,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"4,02 m²",'pet_bottles'=>15],
            ['code'=>"BF-WEG-09-0002",'thickness'=>9,'a'=>200,'b'=>1200,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"3,73 m²",'pet_bottles'=>27],
            ['code'=>"BF-WEG-09-0003",'thickness'=>9,'a'=>300,'b'=>1200,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"3,46 m²",'pet_bottles'=>41],
            ['code'=>"BF-WEG-09-0004",'thickness'=>9,'a'=>500,'b'=>1200,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"2,94 m²",'pet_bottles'=>68],
            ['code'=>"BF-WEG-09-0005",'thickness'=>9,'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,14 m²",'pet_bottles'=>32],
            ['code'=>"BF-WEG-09-0006",'thickness'=>9,'a'=>250,'b'=>2800,'c'=>350,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,05 m²",'pet_bottles'=>79],
            ['code'=>"BF-WEG-09-0007",'thickness'=>9,'a'=>330,'b'=>2800,'c'=>430,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,49 m²",'pet_bottles'=>104],
            ['code'=>"BF-WEG-09-0008",'thickness'=>9,'a'=>550,'b'=>2800,'c'=>650,'d'=>'','pieces_per_box'=>2,'coverage_area'=>"1,88 m²",'pet_bottles'=>173],
            ['code'=>"BF-WEG-10-0001",'thickness'=>"N25E10",'a'=>110,'b'=>1400,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"5,30 m²",'pet_bottles'=>8],
            ['code'=>"BF-WEG-10-0002",'thickness'=>"N25E10",'a'=>200,'b'=>1400,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"4,59 m²",'pet_bottles'=>15],
            ['code'=>"BF-WEG-10-0003",'thickness'=>"N25E10",'a'=>300,'b'=>1400,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,20 m²",'pet_bottles'=>22],
            ['code'=>"BF-WEG-10-0004",'thickness'=>"N25E10",'a'=>500,'b'=>1400,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,54 m²",'pet_bottles'=>37],
            ['code'=>"BF-WEG-10-0005",'thickness'=>"N25E10",'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>9,'coverage_area'=>"5,11 m²",'pet_bottles'=>15],
            ['code'=>"BF-WEG-10-0006",'thickness'=>"N25E10",'a'=>220,'b'=>2800,'c'=>320,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,93 m²",'pet_bottles'=>32],
            ['code'=>"BF-WEG-10-0007",'thickness'=>"N25E10",'a'=>300,'b'=>2800,'c'=>400,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,64 m²",'pet_bottles'=>44],
            ['code'=>"BF-WEG-10-0008",'thickness'=>"N25E10",'a'=>400,'b'=>2800,'c'=>500,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"3,01 m²",'pet_bottles'=>58],
            ['code'=>"BF-WEG-20-0001",'thickness'=>"N50E20",'a'=>110,'b'=>1400,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"5,89 m²",'pet_bottles'=>16],
            ['code'=>"BF-WEG-20-0002",'thickness'=>"N50E20",'a'=>200,'b'=>1400,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"4,97 m²",'pet_bottles'=>29],
            ['code'=>"BF-WEG-20-0003",'thickness'=>"N50E20",'a'=>300,'b'=>1400,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,48 m²",'pet_bottles'=>44],
            ['code'=>"BF-WEG-20-0004",'thickness'=>"N50E20",'a'=>500,'b'=>1400,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,71 m²",'pet_bottles'=>73],
            ['code'=>"BF-WEG-20-0005",'thickness'=>"N50E20",'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>9,'coverage_area'=>"5,74 m²",'pet_bottles'=>29],
            ['code'=>"BF-WEG-20-0006",'thickness'=>"N50E20",'a'=>220,'b'=>2800,'c'=>320,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"4,28 m²",'pet_bottles'=>64],
            ['code'=>"BF-WEG-20-0007",'thickness'=>"N50E20",'a'=>300,'b'=>2800,'c'=>400,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,92 m²",'pet_bottles'=>87],
            ['code'=>"BF-WEG-20-0008",'thickness'=>"N50E20",'a'=>400,'b'=>2800,'c'=>500,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"3,22 m²",'pet_bottles'=>116],
        ],
    ],

    1132 => [ // BF-WPE-09 baffle-form-wave-printed-engraved
        'spec_layout' => 'simple',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>"BF-WPE-09-0001",'thickness'=>9,'a'=>110,'b'=>1200,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"4,02 m²",'pet_bottles'=>15],
            ['code'=>"BF-WPE-09-0002",'thickness'=>9,'a'=>200,'b'=>1200,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"3,73 m²",'pet_bottles'=>27],
            ['code'=>"BF-WPE-09-0003",'thickness'=>9,'a'=>300,'b'=>1200,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"3,46 m²",'pet_bottles'=>41],
            ['code'=>"BF-WPE-09-0004",'thickness'=>9,'a'=>500,'b'=>1200,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"2,94 m²",'pet_bottles'=>68],
            ['code'=>"BF-WPE-09-0005",'thickness'=>9,'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,14 m²",'pet_bottles'=>32],
            ['code'=>"BF-WPE-09-0006",'thickness'=>9,'a'=>250,'b'=>2800,'c'=>350,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,05 m²",'pet_bottles'=>79],
            ['code'=>"BF-WPE-09-0007",'thickness'=>9,'a'=>330,'b'=>2800,'c'=>430,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,49 m²",'pet_bottles'=>104],
            ['code'=>"BF-WPE-09-0008",'thickness'=>9,'a'=>550,'b'=>2800,'c'=>650,'d'=>'','pieces_per_box'=>2,'coverage_area'=>"1,88 m²",'pet_bottles'=>173],
            ['code'=>"BF-WPE-10-0001",'thickness'=>"N25E10",'a'=>110,'b'=>1400,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"5,30 m²",'pet_bottles'=>8],
            ['code'=>"BF-WPE-10-0002",'thickness'=>"N25E10",'a'=>200,'b'=>1400,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"4,59 m²",'pet_bottles'=>15],
            ['code'=>"BF-WPE-10-0003",'thickness'=>"N25E10",'a'=>300,'b'=>1400,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,20 m²",'pet_bottles'=>22],
            ['code'=>"BF-WPE-10-0004",'thickness'=>"N25E10",'a'=>500,'b'=>1400,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,54 m²",'pet_bottles'=>37],
            ['code'=>"BF-WPE-10-0005",'thickness'=>"N25E10",'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>9,'coverage_area'=>"5,11 m²",'pet_bottles'=>15],
            ['code'=>"BF-WPE-10-0006",'thickness'=>"N25E10",'a'=>220,'b'=>2800,'c'=>320,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,93 m²",'pet_bottles'=>32],
            ['code'=>"BF-WPE-10-0007",'thickness'=>"N25E10",'a'=>300,'b'=>2800,'c'=>400,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,64 m²",'pet_bottles'=>44],
            ['code'=>"BF-WPE-10-0008",'thickness'=>"N25E10",'a'=>400,'b'=>2800,'c'=>500,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"3,01 m²",'pet_bottles'=>58],
            ['code'=>"BF-WPE-20-0001",'thickness'=>"N50E20",'a'=>110,'b'=>1400,'c'=>210,'d'=>'','pieces_per_box'=>17,'coverage_area'=>"5,89 m²",'pet_bottles'=>16],
            ['code'=>"BF-WPE-20-0002",'thickness'=>"N50E20",'a'=>200,'b'=>1400,'c'=>300,'d'=>'','pieces_per_box'=>11,'coverage_area'=>"4,97 m²",'pet_bottles'=>29],
            ['code'=>"BF-WPE-20-0003",'thickness'=>"N50E20",'a'=>300,'b'=>1400,'c'=>400,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,48 m²",'pet_bottles'=>44],
            ['code'=>"BF-WPE-20-0004",'thickness'=>"N50E20",'a'=>500,'b'=>1400,'c'=>600,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,71 m²",'pet_bottles'=>73],
            ['code'=>"BF-WPE-20-0005",'thickness'=>"N50E20",'a'=>100,'b'=>2800,'c'=>200,'d'=>'','pieces_per_box'=>9,'coverage_area'=>"5,74 m²",'pet_bottles'=>29],
            ['code'=>"BF-WPE-20-0006",'thickness'=>"N50E20",'a'=>220,'b'=>2800,'c'=>320,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"4,28 m²",'pet_bottles'=>64],
            ['code'=>"BF-WPE-20-0007",'thickness'=>"N50E20",'a'=>300,'b'=>2800,'c'=>400,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,92 m²",'pet_bottles'=>87],
            ['code'=>"BF-WPE-20-0008",'thickness'=>"N50E20",'a'=>400,'b'=>2800,'c'=>500,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"3,22 m²",'pet_bottles'=>116],
        ],
    ],

    1139 => [ // BF-TWE-09 baffle-form-trapezium-wave-solid-engraved
        'spec_layout' => 'simple',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>"BF-TWE-09-0001",'thickness'=>9,'a'=>140,'b'=>1200,'c'=>235,'d'=>'','pieces_per_box'=>14,'coverage_area'=>"3,83 m²",'pet_bottles'=>15],
            ['code'=>"BF-TWE-09-0002",'thickness'=>9,'a'=>230,'b'=>1200,'c'=>320,'d'=>'','pieces_per_box'=>10,'coverage_area'=>"3,58 m²",'pet_bottles'=>27],
            ['code'=>"BF-TWE-09-0003",'thickness'=>9,'a'=>300,'b'=>1200,'c'=>390,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"3,37 m²",'pet_bottles'=>41],
            ['code'=>"BF-TWE-09-0004",'thickness'=>9,'a'=>500,'b'=>1200,'c'=>590,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"2,89 m²",'pet_bottles'=>68],
            ['code'=>"BF-TWE-09-0005",'thickness'=>9,'a'=>150,'b'=>2800,'c'=>240,'d'=>'','pieces_per_box'=>6,'coverage_area'=>"3,53 m²",'pet_bottles'=>32],
            ['code'=>"BF-TWE-09-0006",'thickness'=>9,'a'=>250,'b'=>2800,'c'=>340,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"2,97 m²",'pet_bottles'=>79],
            ['code'=>"BF-TWE-09-0007",'thickness'=>9,'a'=>320,'b'=>2800,'c'=>415,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,41 m²",'pet_bottles'=>104],
            ['code'=>"BF-TWE-09-0008",'thickness'=>9,'a'=>530,'b'=>2800,'c'=>620,'d'=>'','pieces_per_box'=>2,'coverage_area'=>"1,79 m²",'pet_bottles'=>173],
            ['code'=>"BF-TWE-10-0001",'thickness'=>"N25E10",'a'=>140,'b'=>1400,'c'=>235,'d'=>'','pieces_per_box'=>14,'coverage_area'=>"4,77 m²",'pet_bottles'=>8],
            ['code'=>"BF-TWE-10-0002",'thickness'=>"N25E10",'a'=>230,'b'=>1400,'c'=>320,'d'=>'','pieces_per_box'=>10,'coverage_area'=>"4,38 m²",'pet_bottles'=>15],
            ['code'=>"BF-TWE-10-0003",'thickness'=>"N25E10",'a'=>300,'b'=>1400,'c'=>390,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,10 m²",'pet_bottles'=>22],
            ['code'=>"BF-TWE-10-0004",'thickness'=>"N25E10",'a'=>500,'b'=>1400,'c'=>590,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,48 m²",'pet_bottles'=>37],
            ['code'=>"BF-TWE-10-0005",'thickness'=>"N25E10",'a'=>180,'b'=>2800,'c'=>270,'d'=>'','pieces_per_box'=>6,'coverage_area'=>"4,20 m²",'pet_bottles'=>15],
            ['code'=>"BF-TWE-10-0006",'thickness'=>"N25E10",'a'=>290,'b'=>2800,'c'=>380,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,47 m²",'pet_bottles'=>32],
            ['code'=>"BF-TWE-10-0007",'thickness'=>"N25E10",'a'=>400,'b'=>2800,'c'=>490,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,95 m²",'pet_bottles'=>44],
            ['code'=>"BF-TWE-10-0008",'thickness'=>"N25E10",'a'=>630,'b'=>2800,'c'=>720,'d'=>'','pieces_per_box'=>2,'coverage_area'=>"2,16 m²",'pet_bottles'=>58],
            ['code'=>"BF-TWE-20-0001",'thickness'=>"N50E20",'a'=>140,'b'=>1400,'c'=>235,'d'=>'','pieces_per_box'=>14,'coverage_area'=>"5,26 m²",'pet_bottles'=>16],
            ['code'=>"BF-TWE-20-0002",'thickness'=>"N50E20",'a'=>230,'b'=>1400,'c'=>320,'d'=>'','pieces_per_box'=>10,'coverage_area'=>"4,73 m²",'pet_bottles'=>29],
            ['code'=>"BF-TWE-20-0003",'thickness'=>"N50E20",'a'=>300,'b'=>1400,'c'=>390,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,38 m²",'pet_bottles'=>44],
            ['code'=>"BF-TWE-20-0004",'thickness'=>"N50E20",'a'=>500,'b'=>1400,'c'=>590,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,65 m²",'pet_bottles'=>73],
            ['code'=>"BF-TWE-20-0005",'thickness'=>"N50E20",'a'=>180,'b'=>2800,'c'=>270,'d'=>'','pieces_per_box'=>6,'coverage_area'=>"3,78 m²",'pet_bottles'=>29],
            ['code'=>"BF-TWE-20-0006",'thickness'=>"N50E20",'a'=>290,'b'=>2800,'c'=>380,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,33 m²",'pet_bottles'=>64],
            ['code'=>"BF-TWE-20-0007",'thickness'=>"N50E20",'a'=>400,'b'=>2800,'c'=>490,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,72 m²",'pet_bottles'=>87],
            ['code'=>"BF-TWE-20-0008",'thickness'=>"N50E20",'a'=>630,'b'=>2800,'c'=>720,'d'=>'','pieces_per_box'=>2,'coverage_area'=>"1,68 m²",'pet_bottles'=>116],
        ],
    ],

    1145 => [ // BF-TWEP-09 baffle-form-trapezium-wave-printed-engraved
        'spec_layout' => 'simple',
        'spec_column_labels' => null,
        'spec_schema' => null,
        'specifications' => [
            ['code'=>"BF-TWEP-09-0001",'thickness'=>9,'a'=>140,'b'=>1200,'c'=>235,'d'=>'','pieces_per_box'=>14,'coverage_area'=>"3,83 m²",'pet_bottles'=>15],
            ['code'=>"BF-TWEP-09-0002",'thickness'=>9,'a'=>230,'b'=>1200,'c'=>320,'d'=>'','pieces_per_box'=>10,'coverage_area'=>"3,58 m²",'pet_bottles'=>27],
            ['code'=>"BF-TWEP-09-0003",'thickness'=>9,'a'=>300,'b'=>1200,'c'=>390,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"3,37 m²",'pet_bottles'=>41],
            ['code'=>"BF-TWEP-09-0004",'thickness'=>9,'a'=>500,'b'=>1200,'c'=>590,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"2,89 m²",'pet_bottles'=>68],
            ['code'=>"BF-TWEP-09-0005",'thickness'=>9,'a'=>150,'b'=>2800,'c'=>240,'d'=>'','pieces_per_box'=>6,'coverage_area'=>"3,53 m²",'pet_bottles'=>32],
            ['code'=>"BF-TWEP-09-0006",'thickness'=>9,'a'=>250,'b'=>2800,'c'=>340,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"2,97 m²",'pet_bottles'=>79],
            ['code'=>"BF-TWEP-09-0007",'thickness'=>9,'a'=>320,'b'=>2800,'c'=>415,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,41 m²",'pet_bottles'=>104],
            ['code'=>"BF-TWEP-09-0008",'thickness'=>9,'a'=>530,'b'=>2800,'c'=>620,'d'=>'','pieces_per_box'=>2,'coverage_area'=>"1,79 m²",'pet_bottles'=>173],
            ['code'=>"BF-TWEP-10-0001",'thickness'=>"N25E10",'a'=>140,'b'=>1400,'c'=>235,'d'=>'','pieces_per_box'=>14,'coverage_area'=>"4,77 m²",'pet_bottles'=>8],
            ['code'=>"BF-TWEP-10-0002",'thickness'=>"N25E10",'a'=>230,'b'=>1400,'c'=>320,'d'=>'','pieces_per_box'=>10,'coverage_area'=>"4,38 m²",'pet_bottles'=>15],
            ['code'=>"BF-TWEP-10-0003",'thickness'=>"N25E10",'a'=>300,'b'=>1400,'c'=>390,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,10 m²",'pet_bottles'=>22],
            ['code'=>"BF-TWEP-10-0004",'thickness'=>"N25E10",'a'=>500,'b'=>1400,'c'=>590,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,48 m²",'pet_bottles'=>37],
            ['code'=>"BF-TWEP-10-0005",'thickness'=>"N25E10",'a'=>180,'b'=>2800,'c'=>270,'d'=>'','pieces_per_box'=>6,'coverage_area'=>"4,20 m²",'pet_bottles'=>15],
            ['code'=>"BF-TWEP-10-0006",'thickness'=>"N25E10",'a'=>290,'b'=>2800,'c'=>380,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,47 m²",'pet_bottles'=>32],
            ['code'=>"BF-TWEP-10-0007",'thickness'=>"N25E10",'a'=>400,'b'=>2800,'c'=>490,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,95 m²",'pet_bottles'=>44],
            ['code'=>"BF-TWEP-10-0008",'thickness'=>"N25E10",'a'=>630,'b'=>2800,'c'=>720,'d'=>'','pieces_per_box'=>2,'coverage_area'=>"2,16 m²",'pet_bottles'=>58],
            ['code'=>"BF-TWEP-20-0001",'thickness'=>"N50E20",'a'=>140,'b'=>1400,'c'=>235,'d'=>'','pieces_per_box'=>14,'coverage_area'=>"5,26 m²",'pet_bottles'=>16],
            ['code'=>"BF-TWEP-20-0002",'thickness'=>"N50E20",'a'=>230,'b'=>1400,'c'=>320,'d'=>'','pieces_per_box'=>10,'coverage_area'=>"4,73 m²",'pet_bottles'=>29],
            ['code'=>"BF-TWEP-20-0003",'thickness'=>"N50E20",'a'=>300,'b'=>1400,'c'=>390,'d'=>'','pieces_per_box'=>8,'coverage_area'=>"4,38 m²",'pet_bottles'=>44],
            ['code'=>"BF-TWEP-20-0004",'thickness'=>"N50E20",'a'=>500,'b'=>1400,'c'=>590,'d'=>'','pieces_per_box'=>5,'coverage_area'=>"3,65 m²",'pet_bottles'=>73],
            ['code'=>"BF-TWEP-20-0005",'thickness'=>"N50E20",'a'=>180,'b'=>2800,'c'=>270,'d'=>'','pieces_per_box'=>6,'coverage_area'=>"3,78 m²",'pet_bottles'=>29],
            ['code'=>"BF-TWEP-20-0006",'thickness'=>"N50E20",'a'=>290,'b'=>2800,'c'=>380,'d'=>'','pieces_per_box'=>4,'coverage_area'=>"3,33 m²",'pet_bottles'=>64],
            ['code'=>"BF-TWEP-20-0007",'thickness'=>"N50E20",'a'=>400,'b'=>2800,'c'=>490,'d'=>'','pieces_per_box'=>3,'coverage_area'=>"2,72 m²",'pet_bottles'=>87],
            ['code'=>"BF-TWEP-20-0008",'thickness'=>"N50E20",'a'=>630,'b'=>2800,'c'=>720,'d'=>'','pieces_per_box'=>2,'coverage_area'=>"1,68 m²",'pet_bottles'=>116],
        ],
    ],

];

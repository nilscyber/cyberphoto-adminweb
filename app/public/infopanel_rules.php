<?php
// infopanel_rules.php
// ------------------------------------------------------------
// ENDA STÄLLET du behöver ändra när ni vill finjustera nivåer.
// Matchar Excel-matrisen: TOTALA BOXEN + INSTABOX RUTAN.
// ------------------------------------------------------------

return [

  // TOTAL-boxen (baserad på volym att hantera)
  // value = (utskrivna + ej_utskrivna) + (instabox ev. med beroende på mode)
  'total_rules' => [
    // 00:00 - 10:30  => Grönt (inga trösklar)
    ['from' => '00:00', 'to' => '10:30', 'mode' => 'all',         'orange_gt' => null, 'red_gt' => null],

    // 10:31 - 12:00  => Orange >50, Röd >100 (Instabox räknas in)
    ['from' => '10:31', 'to' => '12:00', 'mode' => 'all',         'orange_gt' => 50,   'red_gt' => 100],

    // 12:01 - 14:30  => Grönt (Instabox räknas in)
    ['from' => '12:01', 'to' => '14:30', 'mode' => 'all',         'orange_gt' => null, 'red_gt' => null],

    // 14:31 - 15:30  => Orange >65, Röd >100 (Instabox INTE inräknad)
    ['from' => '14:31', 'to' => '15:30', 'mode' => 'no_instabox', 'orange_gt' => 65,   'red_gt' => 100],

    // 15:31 - 16:30  => Orange >50, Röd >75  (Instabox INTE inräknad)
    ['from' => '15:31', 'to' => '16:30', 'mode' => 'no_instabox', 'orange_gt' => 50,   'red_gt' => 75],

    // 16:31 - 17:05  => Orange >20, Röd >40  (Instabox INTE inräknad)
    ['from' => '16:31', 'to' => '17:05', 'mode' => 'no_instabox', 'orange_gt' => 20,   'red_gt' => 40],

    // 17:06 - 23:59  => Grönt (Instabox räknas in igen  enligt Excel: Grönt/blankt)
    ['from' => '17:06', 'to' => '23:59', 'mode' => 'all',         'orange_gt' => null, 'red_gt' => null],
  ],

  // Instabox-varning (på EJ UTSKRIVNA INSTABOX)
  // value = ej_utskrivna_instabox
  'instabox_rules' => [
    // 00:00 - 13:30 => Grönt
    ['from' => '00:00', 'to' => '13:30', 'orange_gt' => null, 'red_gt' => null],

    // 13:30 - 14:30 => Orange >20, Röd >30
    ['from' => '13:30', 'to' => '14:30', 'orange_gt' => 20,   'red_gt' => 30],

    // 14:31 - 15:10 => Orange >10, Röd >20
    ['from' => '14:31', 'to' => '15:10', 'orange_gt' => 10,   'red_gt' => 20],

    // 15:11 - 23:59 => Grönt
    ['from' => '15:11', 'to' => '23:59', 'orange_gt' => null, 'red_gt' => null],
  ],

  // Visuell fill i TOTAL-boxen:
  // - Fill börjar vid orange-tröskeln och är 100% vid red-tröskeln (om båda finns).
  // - Om bara orange finns: fill går mot 100% vid orange*2 (rimlig default).
  'visual' => [
    'enable_total_fill' => true,
    'enable_total_tint' => true,
    'enable_instabox_badge' => true,
  ],
];

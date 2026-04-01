<?php
/**
 * cron_dropship_top10.php
 * Skickar mail med topp-10 levererade dropshipment-produkter för vald period.
 *
 * Mode:
 *  - prev_month : hela föregående månad
 *  - first_half : 114 i innevarande månad (levererat under perioden)
 *
 * Funkar både via HTTP (GET ?mode=...) och CLI (php fil.php prev_month / --mode=prev_month).
 */

include_once("Db.php");
include_once("CDropship.php");
include_once(__DIR__ . "/../../lib/SmtpMail.php");

$u8 = function ($s) {
    return (string)$s;
};

// --------------------
// Läs mode: HTTP + CLI
// --------------------
$mode = 'first_half';

// HTTP
if (isset($_GET['mode']) && $_GET['mode'] !== '') {
    $mode = (string)$_GET['mode'];
}

// CLI
if (PHP_SAPI === 'cli' && isset($argv) && count($argv) > 1) {
    foreach ($argv as $arg) {
        if (strpos($arg, '--mode=') === 0) {
            $mode = substr($arg, 7);
        }
    }
    if (isset($argv[1]) && $argv[1] !== '' && $argv[1][0] !== '-') {
        $mode = $argv[1];
    }
}

if ($mode !== 'prev_month' && $mode !== 'first_half') {
    $mode = 'first_half';
}

$limit = 10;

// --------------------
// Beräkna intervall [from, to) baserat på LEVERANSDATUM
// --------------------
if ($mode === 'prev_month') {
    $from = date('Y-m-01 00:00:00', strtotime('first day of last month'));
    $to   = date('Y-m-01 00:00:00', strtotime('first day of this month'));
    $periodLabel = date('Y-m', strtotime('first day of last month'));
    $title = "Topp 10  Levererade dropshipment-produkter (föregående månad: ".$periodLabel.")";
} else {
    // 114 (to = 15:e 00:00)
    $from = date('Y-m-01 00:00:00');
    $to   = date('Y-m-15 00:00:00');
    $periodLabel = date('Y-m')." (114)";
    $title = "Topp 10  Levererade dropshipment-produkter (första halvan: ".$periodLabel.")";
}

// För rubrik: visa to-1 sekund så det ser inkluderande ut i text
$toInclusive = date('Y-m-d H:i', strtotime($to . ' -1 second'));

// --------------------
// Hämta rapport (CDropship-funktionen måste filtrera på ol.datedelivered)
// --------------------
$ds = new CDropship();

// Bygg HTML från klass (OBS: funktionen ska använda ol.datedelivered som periodfilter!)
$bodyInner = $ds->getTopSoldDropshipDeliveredArticlesHtml($from, $to, $limit);

// --------------------
// Bygg mail
// --------------------
$body  = "<html><head>";
$body .= "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
$body .= "<style>
            body{font-family:Arial,Helvetica,sans-serif;}
            table.stat-table{border-collapse:collapse;width:100%;font-size:12px;}
            table.stat-table th,table.stat-table td{border:1px solid #ccc;padding:4px 6px;vertical-align:top;}
            table.stat-table th{background:#e5e7eb;font-weight:bold;text-align:left;}
            a{color:#111;}
          </style>";
$body .= "</head><body>";
$body .= "<h1>".$u8($title)."</h1>";
$body .= "<div style='color:#6b7280;font-size:12px;margin-bottom:10px;'>".$u8("Levererat under perioden: ".$from." ? ".$toInclusive)."</div>";
$body .= $bodyInner;
$body .= "</body></html>";

// --------------------
// Mottagare (testa på dig själv)
// --------------------
$toMail = "stefan@cyberphoto.se, emil.lindberg@cyberphoto.se";
// $toMail = "stefan@cyberphoto.se";

// --------------------
// Subject: CP1252 -> UTF-8 + MIME
// --------------------
$rawSubject = $title . " | Levererat: " . substr($from, 0, 10) . "" . substr($toInclusive, 0, 10);
$subject = '=?UTF-8?B?' . base64_encode($rawSubject) . '?=';

SmtpMail::send($toMail, $subject, $body, "Content-Type: text/html; charset=UTF-8");
?>

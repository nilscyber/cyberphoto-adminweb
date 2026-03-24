<?php
// =========================
// EXPORT (MÅSTE LIGGA FÖRST)
// =========================
require_once("CCheckIpNumber.php");
require_once("Db.php");
require_once("CDropship.php");

$days  = isset($_GET['days'])  ? (int)$_GET['days']  : 30;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 200;
if ($days <= 0)  { $days = 30; }
if ($limit <= 0) { $limit = 200; }
if ($limit > 5000) { $limit = 5000; }

$view = isset($_GET['view']) ? (string)$_GET['view'] : 'orders';
if ($view !== 'orders' && $view !== 'lines') { $view = 'orders'; }

$lineStatus = isset($_GET['status']) ? (string)$_GET['status'] : 'undelivered';
if ($lineStatus !== 'undelivered' && $lineStatus !== 'delivered') { $lineStatus = 'undelivered'; }

$ds = new CDropship();

// ---- Export: Orders ----
if (isset($_GET['export']) && (int)$_GET['export'] === 1) {
    $data = $ds->getDropshipOrders($days, $limit);

    if (!empty($data['error'])) {
        header("Content-Type: text/plain; charset=UTF-8");
        echo $data['error'];
        exit;
    }

    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=dropship_orders.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "\xEF\xBB\xBF";

    $out = fopen('php://output', 'w');
    fputcsv($out, array('Datum', 'Ordernr', 'Kund', 'Lagd av', 'Ordervarde_SEK', 'TB_SEK', 'TG_ratio'), ';');

    foreach ($data['rows'] as $r) {
        $created = date('Y-m-d H:i', strtotime($r['created']));

        $ordervarde = (float)$r['totallines'];
        $tb         = (float)$r['marginamt'];

        $tg_ratio = 0.0;
        if ($ordervarde != 0.0) { $tg_ratio = $tb / $ordervarde; }

        $tg_ratio_str = number_format($tg_ratio, 7, ',', '');

        fputcsv($out, array(
            $created,
            (string)$r['documentno'],
            (string)$r['customer_name'],
            (string)$r['salesrep_name'],
            (string)round($ordervarde, 0),
            (string)round($tb, 0),
            $tg_ratio_str
        ), ';');
    }

    fclose($out);
    exit;
}

// ---- Export: Lines (undelivered/delivered) ----
if (isset($_GET['export_lines']) && (int)$_GET['export_lines'] === 1) {
    $data = $ds->getDropshipLinesByDeliveryStatus($lineStatus, $days, $limit);

    if (!empty($data['error'])) {
        header("Content-Type: text/plain; charset=UTF-8");
        echo $data['error'];
        exit;
    }

    $fn = ($lineStatus === 'delivered') ? 'dropship_lines_delivered.csv' : 'dropship_lines_undelivered.csv';

    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=".$fn);
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "\xEF\xBB\xBF";

    $out = fopen('php://output', 'w');

    // Ingen URL-kolumn. ASCII headers.
    fputcsv($out, array(
        'Orderdatum',
        'Ordernr',
        'Artikelnummer',
        'Tillverkare',
        'Produkt',
        'Antal',
        'Limitpris_SEK',
        'Prislista_SEK',
        'Orderpris_SEK',
        'Radrabatt_ratio',
        'TB_SEK'
    ), ';');

    foreach ($data['rows'] as $r) {
        $orderDate = (string)$r['order_date'];
        $ordno     = (string)$r['orderno'];
        $artnr     = (string)$r['artnr'];
        $manu      = (string)$r['manufacturer_name'];
        $pname     = (string)$r['product_name'];

        $qty       = (float)$r['qtyordered'];
        $limitPris = (float)$r['pricelimit'];
        $prislista = (float)$r['pricelist'];
        $orderpris = (float)$r['linenetamt'];

        // Radrabatt ratio: 1 - (linenetamt / (pricelist * qty))
        $rabattRatio = 0.0;
        $den = $prislista * $qty;
        if ($den > 0.0) {
            $rabattRatio = 1.0 - ($orderpris / $den);
        }
        $rabattRatioStr = number_format($rabattRatio, 7, ',', '');

        // TB: orderpris - (limitpris * qty)
        $tbLine = $orderpris - ($limitPris * $qty);

        fputcsv($out, array(
            $orderDate,
            $ordno,
            $artnr,
            $manu,
            $pname,
            (string)$qty,
            (string)round($limitPris, 0),
            (string)round($prislista, 0),
            (string)round($orderpris, 0),
            $rabattRatioStr,
            (string)round($tbLine, 0)
        ), ';');
    }

    fclose($out);
    exit;
}

// =========================
// NORMAL SIDA
// =========================
include_once("top.php");
include_once("header.php");

$h  = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
$toLatin1 = function ($s) { return (string)$s; };
$nf = function ($n, $d = 0) { return number_format((float)$n, $d, ',', ' '); };

echo "<h1>Dropshipment  uppföljning</h1>";

// Hämta data beroende på vy
if ($view === 'lines') {
    $data = $ds->getDropshipLinesByDeliveryStatus($lineStatus, $days, $limit);
} else {
    $data = $ds->getDropshipOrders($days, $limit);
}

echo '<style>
.with-drawer-gutter{margin-right:8px}
.table-list{table-layout:fixed;width:100%;border-collapse:collapse;font-size:13px}
.table-list th,.table-list td{padding:8px 10px;border-bottom:1px solid #e5e7eb;vertical-align:middle}
.table-list thead th{background:#e5e7eb;color:#111;font-weight:700;text-align:left}
.table-list tbody tr:nth-child(even){background:#f3f4f6}
.table-list tbody tr:hover{background:#e5e7eb}
.table-list .text-right{text-align:right}
.table-list .text-center{text-align:center}
.table-list td.prod-col{white-space:normal;word-break:normal}
.small-muted{color:#6b7280;font-size:12px;margin:8px 0 12px}
.filterbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin:10px 0 14px}
.filterbar label{font-size:12px;color:#374151}
.filterbar input{padding:6px 8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px}
.filterbar button,.btn{padding:7px 10px;border:0;border-radius:6px;background:#111827;color:#fff;cursor:pointer;font-size:13px;text-decoration:none;display:inline-block}
.btn.secondary{background:#95979b}
.cat-head{background:#d1d5db;font-weight:700}
.icon-link{display:inline-flex;align-items:center;text-decoration:none}
.icon{width:16px;height:16px;vertical-align:middle;fill:#111}
.neg{color:#b91c1c;font-weight:700}
</style>';

// Filter + vyval + export
echo '<div class="filterbar">';
echo '  <form method="get" action="" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">';
echo '    <input type="hidden" name="view" value="'.$h($view).'">';
if ($view === 'lines') {
    echo '    <input type="hidden" name="status" value="'.$h($lineStatus).'">';
}
echo '    <label>Visa senaste <input type="number" name="days" value="'.$h($days).'" min="1" max="3650" style="width:90px;"> dagar</label>';
echo '    <label>Max rader <input type="number" name="limit" value="'.$h($limit).'" min="10" max="5000" style="width:90px;"></label>';
echo '    <button type="submit">Uppdatera</button>';
echo '  </form>';

$base = '?days='.$h($days).'&limit='.$h($limit);

echo '  <a class="btn '.($view==='orders' ? '' : 'secondary').'" href="'.$base.'&view=orders">Orderöversikt</a>';
echo '  <a class="btn '.($view==='lines'  ? '' : 'secondary').'" href="'.$base.'&view=lines&status='.$h($lineStatus).'">Produktlista</a>';

if ($view === 'orders') {
    $exportUrl = $base.'&view=orders&export=1';
    echo '  <a class="btn secondary" href="'.$exportUrl.'">Exportera Excel</a>';
} else {
    $uUrl = $base.'&view=lines&status=undelivered';
    $dUrl = $base.'&view=lines&status=delivered';

    echo '  <a class="btn '.($lineStatus==='undelivered' ? '' : 'secondary').'" href="'.$uUrl.'">Ej levererade</a>';
    echo '  <a class="btn '.($lineStatus==='delivered' ? '' : 'secondary').'" href="'.$dUrl.'">Levererade</a>';

    $exportLinesUrl = $base.'&view=lines&status='.$h($lineStatus).'&export_lines=1';
    echo '  <a class="btn secondary" href="'.$exportLinesUrl.'">Exportera Excel</a>';
}

echo '</div>';

if (!empty($data['error'])) {
    echo '<div style="padding:10px;border:1px solid #fecaca;background:#fef2f2;color:#991b1b;border-radius:8px;">'
       . $h($data['error'])
       . '</div>';
    include_once("footer.php");
    exit;
}

$rows = $data['rows'];

if ($view === 'lines') {
    // =========================
    // VY: PRODUKTLISTA
    // =========================
    $label = ($lineStatus === 'delivered') ? 'Levererade rader' : 'Ej levererade rader';
    echo '<div class="small-muted">'.$h($label).': <b>'.$h(count($rows)).'</b> rader.</div>';

    echo '<div class="with-drawer-gutter">';
    echo '<table class="table-list">';
	echo '  <thead>';
	echo '    <tr>';
	echo '      <th style="width:90px;">Orderdatum</th>';
	echo '      <th style="width:80px;">Ordernr</th>';
	echo '      <th style="width:100px;">Artikelnr</th>';
	echo '      <th style="width:380px;">Produkt</th>';
	echo '      <th class="text-right" style="width:50px;">Antal</th>';
	echo '      <th class="text-right" style="width:90px;">Limitpris</th>';
	echo '      <th class="text-right" style="width:90px;">Prislista</th>';
	echo '      <th class="text-right" style="width:90px;">Orderpris</th>';
	echo '      <th class="text-right" style="width:80px;">Radrabatt</th>';
	echo '      <th class="text-right" style="width:70px;">TB</th>';
	echo '    </tr>';
	echo '  </thead>';
    echo '  <tbody>';

    if (empty($rows)) {
        echo '<tr><td colspan="10" style="padding:14px;color:#6b7280;">Inget att visa för vald period.</td></tr>';
    } else {
        foreach ($rows as $r) {
            $orderDate = (string)$r['order_date'];
            $ordno     = (string)$r['orderno'];
            $artnr     = (string)$r['artnr'];

            $manu  = trim($toLatin1($r['manufacturer_name']));
            $pname = trim($toLatin1($r['product_name']));
            $prodDisplay = ($manu !== '') ? ($manu.' '.$pname) : $pname;

            $pid       = (int)$r['m_product_id'];

            $qty       = (float)$r['qtyordered'];
            $limitPris = (float)$r['pricelimit'];
            $prislista = (float)$r['pricelist'];
            $orderpris = (float)$r['linenetamt'];

            // Rabatt % = (1 - (linenetamt / (pricelist*qty))) * 100
            $rabattPct = 0.0;
            $den = $prislista * $qty;
            if ($den > 0.0) {
                $rabattPct = (1.0 - ($orderpris / $den)) * 100.0;
            }

            // TB = orderpris - (limitpris * qty)
            $tbLine = $orderpris - ($limitPris * $qty);

            $prodUrl  = 'https://admin.cyberphoto.se/search_dispatch.php?mode=product&q='
                        . rawurlencode($artnr)
                        . '&open=product&id=' . $pid;

            $orderUrl = 'https://admin.cyberphoto.se/search_dispatch.php?mode=order&page=1&q='
                        . rawurlencode($ordno);

            echo '<tr>';
            echo '  <td>'.$h($orderDate).'</td>';
            echo '  <td><a href="'.$h($orderUrl).'" target="_blank">'.$h($ordno).'</a></td>';
            echo '  <td><a href="'.$h($prodUrl).'" target="_blank">'.$h($artnr).'</a></td>';
            echo '  <td class="prod-col">'.$h($prodDisplay).'</td>';
            echo '  <td class="text-right">'.$h($qty).'</td>';
            echo '  <td class="text-right">'.$nf($limitPris, 0).' SEK</td>';
            echo '  <td class="text-right">'.$nf($prislista, 0).' SEK</td>';
            echo '  <td class="text-right">'.$nf($orderpris, 0).' SEK</td>';
            echo '  <td class="text-right">'.$nf($rabattPct, 2).'%</td>';

            $tbClass = ($tbLine < 0) ? 'neg' : '';
            echo '  <td class="text-right '.$tbClass.'">'.$nf($tbLine, 0).' SEK</td>';
            echo '</tr>';
        }
    }

    echo '  </tbody>';
    echo '</table>';
    echo '</div>';

} else {
    // =========================
    // VY: ORDERÖVERSIKT
    // =========================
    $counts = array();
    foreach ($rows as $r) {
        $day = date('Y-m-d', strtotime($r['created']));
        if (!isset($counts[$day])) $counts[$day] = 0;
        $counts[$day]++;
    }

    echo '<div class="small-muted">Hittade <b>'.$h(count($rows)).'</b> dropship-order (warehouse 1000003).</div>';

    echo '<div class="with-drawer-gutter">';
    echo '<table class="table-list">';
    echo '  <thead>';
    echo '    <tr>';
    echo '      <th style="width:135px;">Datum</th>';
    echo '      <th style="width:120px;">Ordernr</th>';
    echo '      <th>Kund</th>';
    echo '      <th style="width:190px;">Lagd av</th>';
    echo '      <th class="text-right" style="width:130px;">Ordervarde</th>';
    echo '      <th class="text-right" style="width:120px;">TB</th>';
    echo '      <th class="text-right" style="width:90px;">TG</th>';
    echo '      <th class="text-center" style="width:60px;">URL</th>';
    echo '    </tr>';
    echo '  </thead>';
    echo '  <tbody>';

    if (empty($rows)) {
        echo '<tr><td colspan="8" style="padding:14px;color:#6b7280;">Inget att visa f&ouml;r vald period.</td></tr>';
    } else {
        $lastDay = '';
        $today   = date('Y-m-d');

        foreach ($rows as $r) {
            $ts  = strtotime($r['created']);
            $day = date('Y-m-d', $ts);

            if ($day !== $lastDay) {
                $label = ($day === $today) ? 'Idag' : $day;
                $cnt   = isset($counts[$day]) ? (int)$counts[$day] : 0;

                echo '<tr class="cat-head"><th colspan="8">'.$h($label).' <span class="small-muted">('.$h($cnt).' st)</span></th></tr>';
                $lastDay = $day;
            }

            $created = date('Y-m-d H:i', $ts);
            $ordno   = (string)$r['documentno'];

            //$cust = $toLatin1($r['customer_name']);
            $rep  = $toLatin1($r['salesrep_name']);

            $val = (float)$r['totallines'];
            $tg  = (float)$r['margin'];

            $orderUrl = 'https://admin.cyberphoto.se/search_dispatch.php?mode=order&page=1&q=' . rawurlencode($ordno);

            $ext = trim((string)$r['external_reference']);
            $extUrl = ($ext !== '') ? ('https://www.cyberphoto.se/orderstatus?order=' . rawurlencode($ext)) : '';

            echo '<tr>';
            echo '  <td>'.$h($created).'</td>';
            echo '  <td><a href="'.$h($orderUrl).'" target="_blank">'.$h($ordno).'</a></td>';
            echo '  <td>'.$h($cust).'</td>';
            echo '  <td>'.$h($rep).'</td>';
            echo '  <td class="text-right">'.$nf($val, 0).' SEK</td>';
            echo '  <td class="text-right">'.$nf($tb, 0).' SEK</td>';
            echo '  <td class="text-right">'.$nf($tg, 2).'%</td>';
            echo '  <td class="text-center">';
            if ($extUrl !== '') {
                echo '<a class="icon-link" href="'.$h($extUrl).'" target="_blank" title="Oppna kundens orderstatus">';
                echo '  <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z"></path><path d="M5 5h6v2H7v10h10v-4h2v6H5V5z"></path></svg>';
                echo '</a>';
            }
            echo '  </td>';
            echo '</tr>';
        }
    }

    echo '  </tbody>';
    echo '</table>';
    echo '</div>';
}

include_once("footer.php");
?>

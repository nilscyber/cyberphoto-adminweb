<?php
// /sold_article.php
// Visar alla sålda orderrader för en specifik artikel
// Normalt läge:  ?product_id=12345
// Bundle-läge:   ?product_id=12345&show_salesbundle=yes

include_once("top.php");
include_once("header.php");
require_once "Db.php";

@header('Content-Type: text/html; charset=UTF-8');

$h = function($s){
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
};

$product_id = isset($_GET['product_id'])      ? (int)$_GET['product_id']                    : 0;
$showBundle = isset($_GET['show_salesbundle']) && $_GET['show_salesbundle'] === 'yes';
$page       = isset($_GET['page'])             ? (int)$_GET['page']                          : 1;
if ($page < 1) $page = 1;

$limit  = 2000;
$offset = ($page - 1) * $limit;

$pg = Db::getConnectionAD(false);
if (!$pg) {
    echo '<div style="padding:14px"><h1>S&aring;lda ordrar</h1><p>Kunde inte ansluta till ADempiere-databasen.</p></div>';
    include_once("footer.php");
    exit;
}

// Kör UTF8 mot databasen - konverterar i PHP med iconv
if ($pg) { @pg_set_client_encoding($pg, "UTF8"); }

$statusText = function($s){
    $s = strtoupper(trim((string)$s));
    if ($s === 'DR') return 'Utkast';
    if ($s === 'IP') return 'Bearbetas';
    if ($s === 'CO') return 'Slutf&ouml;rd';
    if ($s === 'IN') return 'Felaktig';
    if ($s === 'WP') return 'V&auml;ntar';
    if ($s === 'CL') return 'St&auml;ngd';
    return $s;
};

// --- Hämta produktnamn + artikelnummer ---
$produktNamn = '';
$artikelNr   = '';
if ($product_id > 0) {
    $sqlProd = "SELECT p.name, p.value FROM m_product p WHERE p.m_product_id = $1 LIMIT 1";
    $rsProd  = ($pg) ? @pg_query_params($pg, $sqlProd, array($product_id)) : false;
    if ($rsProd && ($rp = pg_fetch_assoc($rsProd))) {
        $produktNamn = (string)$rp['name'];
        $artikelNr   = (string)$rp['value'];
    }
    if ($rsProd) pg_free_result($rsProd);
}

// ============================================================
// BUNDLE-LÄGE: söker på col.packey = artikelnumret
// ============================================================
if ($showBundle && $artikelNr !== '') {

    $sqlCount = "
        SELECT COUNT(DISTINCT ol.c_order_id) AS n
        FROM c_orderline ol
        INNER JOIN c_order o ON o.c_order_id = ol.c_order_id
        WHERE ol.packey         = $1
          AND o.c_doctype_id    = 1000030
          AND o.docstatus       = 'CO'
          AND ol.qtyordered     = ol.qtydelivered
          AND ol.qtyordered     > 0
    ";
    $rsC = ($pg) ? @pg_query_params($pg, $sqlCount, array($artikelNr)) : false;
    $total = 0;
    if ($rsC) {
        $rc    = $rsC ? pg_fetch_assoc($rsC) : null;
        $total = (int)$rc['n'];
        pg_free_result($rsC);
    }
    $totalSkickat = $total;

    $sqlData = "
        SELECT
            o.created::date             AS orderdatum,
            o.documentno                AS ordernummer,
            o.c_order_id                AS order_id,
            bp.name                     AS kundnamn,
            o.c_bpartner_id             AS kund_id,
            o.docstatus                 AS orderstatus
        FROM (
            SELECT DISTINCT c_order_id
            FROM c_orderline
            WHERE packey      = $1
              AND qtyordered  = qtydelivered
              AND qtyordered  > 0
        ) unika
        INNER JOIN c_order    o  ON o.c_order_id     = unika.c_order_id
        INNER JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id
        WHERE o.c_doctype_id = 1000030
          AND o.docstatus    = 'CO'
        ORDER BY o.created DESC
        LIMIT " . (int)$limit . " OFFSET " . (int)$offset . "
    ";
    $rsD = ($pg) ? @pg_query_params($pg, $sqlData, array($artikelNr)) : false;

    $rowsHtml = '';
    if ($rsD) {
        while ($rsD && $r = pg_fetch_assoc($rsD)) {
            $datum    = (string)$r['orderdatum'];
            $ordNr    = (string)$r['ordernummer'];
            $kundNamn = (string)$r['kundnamn'];
            $kundId   = (int)$r['kund_id'];
            $status   = $statusText($r['orderstatus']);

            $ordLink  = '<a href="/search_dispatch.php?mode=order&page=1&q=' . rawurlencode($ordNr) . '" target="_blank" rel="noopener">' . $h($ordNr) . '</a>';
            $kundLink = '<a href="/customer_orders.php?bp_id=' . $kundId . '" target="_blank" rel="noopener">' . $h($kundNamn) . '</a>';

            $rowsHtml .= '<tr>'
                      .  '<td class="nowrap">'.$h($datum).'</td>'
                      .  '<td class="nowrap">'.$ordLink.'</td>'
                      .  '<td>'.$kundLink.'</td>'
                      .  '<td class="text-center"></td>'
                      .  '<td class="text-center"></td>'
                      .  '<td>'.$status.'</td>'
                      .  '</tr>';
        }
        pg_free_result($rsD);
    }

// ============================================================
// NORMALT LÄGE: söker på m_product_id
// ============================================================
} else {

    $sqlCount = "
        SELECT
            COUNT(*)                          AS n,
            COALESCE(SUM(ol.qtydelivered), 0) AS tot_skickat
        FROM c_orderline ol
        INNER JOIN c_order o ON o.c_order_id = ol.c_order_id
        WHERE ol.m_product_id = $1
          AND o.issotrx = 'Y'
          AND o.docstatus NOT IN ('VO','RE')
          AND o.c_doctypetarget_id NOT IN (1000027, 1000026)
          AND ol.qtydelivered > 0
    ";
    $rsC = ($pg) ? @pg_query_params($pg, $sqlCount, array($product_id)) : false;
    $total        = 0;
    $totalSkickat = 0;
    if ($rsC) {
        $rc           = $rsC ? pg_fetch_assoc($rsC) : null;
        $total        = (int)$rc['n'];
        $totalSkickat = (int)$rc['tot_skickat'];
        pg_free_result($rsC);
    }

    $sqlData = "
        SELECT
            ol.created::date            AS orderdatum,
            o.documentno                AS ordernummer,
            o.c_order_id                AS order_id,
            bp.name                     AS kundnamn,
            o.c_bpartner_id             AS kund_id,
            ol.description              AS notering,
            ol.qtyordered               AS bestallt,
            ol.qtydelivered             AS skickat,
            o.docstatus                 AS orderstatus
        FROM c_orderline ol
        INNER JOIN c_order    o  ON o.c_order_id     = ol.c_order_id
        INNER JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id
        WHERE ol.m_product_id = $1
          AND o.issotrx = 'Y'
          AND o.docstatus NOT IN ('VO','RE')
          AND o.c_doctypetarget_id NOT IN (1000027, 1000026)
          AND ol.qtydelivered > 0
        ORDER BY ol.created DESC, ol.c_orderline_id DESC
        LIMIT " . (int)$limit . " OFFSET " . (int)$offset . "
    ";
    $rsD = ($pg) ? @pg_query_params($pg, $sqlData, array($product_id)) : false;

    $rowsHtml = '';
    if ($rsD) {
        while ($rsD && $r = pg_fetch_assoc($rsD)) {
            $datum    = (string)$r['orderdatum'];
            $ordNr    = (string)$r['ordernummer'];
            $kundNamn = (string)$r['kundnamn'];
            $kundId   = (int)$r['kund_id'];
            $notering = (string)$r['notering'];
            $bestallt = (int)$r['bestallt'];
            $skickat  = (int)$r['skickat'];
            $status   = $statusText($r['orderstatus']);

            $ordLink  = '<a href="/search_dispatch.php?mode=order&page=1&q=' . rawurlencode($ordNr) . '" target="_blank" rel="noopener">' . $h($ordNr) . '</a>';
            $kundLink = '<a href="/customer_orders.php?bp_id=' . $kundId . '" target="_blank" rel="noopener">' . $h($kundNamn) . '</a>';

            $rowsHtml .= '<tr>'
                      .  '<td class="nowrap">'.$h($datum).'</td>'
                      .  '<td class="nowrap">'.$ordLink.'</td>'
                      .  '<td>'.$kundLink.'</td>'
                      .  '<td class="notering-col">'.$h($notering).'</td>'
                      .  '<td class="text-center">'.$bestallt.'</td>'
                      .  '<td class="text-center">'.$skickat.'</td>'
                      .  '<td>'.$status.'</td>'
                      .  '</tr>';
        }
        pg_free_result($rsD);
    }
}

// --- Pagination ---
$pages = ($limit > 0) ? (int)ceil($total / $limit) : 1;
if ($pages < 1) $pages = 1;

$baseUrl = '/sold_article.php?product_id=' . (int)$product_id . ($showBundle ? '&show_salesbundle=yes' : '');
$mkUrl = function($p) use ($baseUrl){ return $baseUrl . '&page=' . (int)$p; };

$pagerHtml = '';
if ($pages > 1) {
    $prev = max(1, $page - 1);
    $next = min($pages, $page + 1);
    $pagerHtml .= '<div style="margin-top:12px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">';
    $pagerHtml .= '<a class="btn-filter" href="'.$mkUrl(1).'">&laquo; F&ouml;rsta</a>';
    $pagerHtml .= '<a class="btn-filter" href="'.$mkUrl($prev).'">&lsaquo; F&ouml;reg</a>';
    $pagerHtml .= '<span style="font-weight:700;">Sida '.(int)$page.' / '.(int)$pages.'</span>';
    $pagerHtml .= '<a class="btn-filter" href="'.$mkUrl($next).'">N&auml;sta &rsaquo;</a>';
    $pagerHtml .= '<a class="btn-filter" href="'.$mkUrl($pages).'">Sista &raquo;</a>';
    $pagerHtml .= '</div>';
}

echo '<style>
.nowrap      { white-space: nowrap; }
.text-center { text-align: center; }
.text-right  { text-align: right; }
.notering-col{ color: #555; font-style: italic; max-width: 260px; }
.btn-filter  { display:inline-block;padding:6px 10px;border-radius:999px;border:1px solid #d1d5db;background:#fff;text-decoration:none;color:#111827;font-weight:700;font-size:13px; }
.btn-filter:hover{ background:#f3f4f6; }
.so-wrap { padding: 14px 16px; }
.so-sub  { color: #6b7280; margin: 0 0 14px; font-size: 14px; }
</style>';

echo '<div class="so-wrap">';

$visaNamn = $produktNamn !== '' ? $produktNamn : ('Produkt #' . (int)$product_id);
echo '<h1>S&aring;lda: ' . $h($visaNamn) . '</h1>';

if ($showBundle) {
    echo '<div class="so-sub">' . (int)$total . ' ordrar s&aring;lda med v&auml;rdepaket</div>';
} else {
    echo '<div class="so-sub">'
       . (int)$totalSkickat . ' st skickade totalt'
       . ' &nbsp;&middot;&nbsp; '
       . (int)$total . ' orderrader'
       . '</div>';
}

if ($rowsHtml === '') {
    echo '<p style="margin-top:12px;">Inga s&aring;lda ordrar hittades f&ouml;r denna produkt.</p>';
} else {
    if ($showBundle) {
        echo '<table class="table-list">'
           . '<colgroup><col style="width:11ch"/><col style="width:12ch"/><col/><col style="width:9ch"/><col style="width:9ch"/><col style="width:10ch"/></colgroup>'
           . '<thead><tr><th>Orderdatum</th><th>Ordernummer</th><th>Kunden</th><th class="text-center">Best&auml;llda</th><th class="text-center">Skickade</th><th>Orderstatus</th></tr></thead>'
           . '<tbody>' . $rowsHtml . '</tbody>'
           . '</table>';
    } else {
        echo '<table class="table-list">'
           . '<colgroup><col style="width:11ch"/><col style="width:12ch"/><col/><col style="width:24ch"/><col style="width:9ch"/><col style="width:9ch"/><col style="width:10ch"/></colgroup>'
           . '<thead><tr><th>Orderdatum</th><th>Ordernummer</th><th>Kunden</th><th>Notering</th><th class="text-center">Best&auml;llda</th><th class="text-center">Skickade</th><th>Orderstatus</th></tr></thead>'
           . '<tbody>' . $rowsHtml . '</tbody>'
           . '</table>';
    }
    echo $pagerHtml;
}

echo '</div>';
include_once("footer.php");

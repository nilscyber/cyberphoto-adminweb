<?php
// /customer_orders.php

include_once("top.php");
include_once("header.php");
require_once "Db.php";

// Sidan kör LATIN-1 (admin)
@header('Content-Type: text/html; charset=UTF-8');

$h = function($s){
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
};

// --- Input ---
$bp_id = isset($_GET['bp_id']) ? (int)$_GET['bp_id'] : 0;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$type = isset($_GET['type']) ? strtolower(trim((string)$_GET['type'])) : 'all';
if (!in_array($type, array('all','sale','purchase','quote'), true)) $type = 'all';

$limit  = 100; // justera om du vill
$offset = ($page - 1) * $limit;

// --- DB ---
$pg = Db::getConnectionAD(false);
if (!$pg) {
    echo '<div style="padding:14px"><h2>Alla ordrar</h2><p>Kunde inte ansluta till ADempiere-databasen.</p></div>';
    include_once("footer.php");
    exit;
}
if ($pg) { @pg_set_client_encoding($pg, "UTF8"); }

// --- Helpers: status + typ ---
$statusText = function($s){
    $s = strtoupper(trim((string)$s));
    if ($s === 'DR') return 'Utkast';
    if ($s === 'IP') return 'Bearbetas';
    if ($s === 'CO') return 'Slutförd';
    if ($s === 'IN') return 'Felaktig';
    return $s;
};

$typeText = function($issotrx, $dt){
    $issotrx = strtoupper(trim((string)$issotrx));
    $dt = (int)$dt;

    if ($issotrx === 'N') return 'Inköp';

    // issotrx=Y => Försäljning eller Offert
    if ($dt === 1000027 || $dt === 1000026) return 'Offert';
    return 'Försäljning';
};

// --- Hämta kundnamn ---
$custName = '';
$custValue = '';
if ($bp_id > 0) {
    $sqlBp = "SELECT bp.name, bp.value FROM c_bpartner bp WHERE bp.c_bpartner_id=$1 LIMIT 1";
    $rsBp = ($pg) ? @pg_query_params($pg, $sqlBp, array($bp_id)) : false;
    if ($rsBp && ($rbp = pg_fetch_assoc($rsBp))) {
        $custName  = (string)$rbp['name'];
        $custValue = (string)$rbp['value'];
    }
    if ($rsBp) pg_free_result($rsBp);
}

// --- WHERE: alltid kund + bort med VO/RE ---
$where = "o.c_bpartner_id = $1 AND o.docstatus NOT IN ('VO','RE')";

// Typfilter (knappar)
switch ($type) {
    case 'quote':
        $where .= " AND o.issotrx='Y' AND o.c_doctypetarget_id IN (1000027,1000026)";
        break;
    case 'sale':
        $where .= " AND o.issotrx='Y' AND o.c_doctypetarget_id NOT IN (1000027,1000026)";
        break;
    case 'purchase':
        $where .= " AND o.issotrx='N'";
        break;
    default:
        break;
}

// --- Count ---
$total = 0;
$sqlCount = "SELECT COUNT(*) AS n FROM c_order o WHERE $where";
$rsC = ($pg) ? @pg_query_params($pg, $sqlCount, array($bp_id)) : false;
if ($rsC) {
    $rr = $rsC ? pg_fetch_assoc($rsC) : null;
    $total = (int)$rr['n'];
    pg_free_result($rsC);
}

// --- Data ---
$sqlData = "
    SELECT
        o.dateordered::date AS order_date,
        o.documentno,
        o.issotrx,
        o.c_doctypetarget_id,
        o.docstatus,
        o.totallines
    FROM c_order o
    WHERE $where
    ORDER BY o.dateordered DESC, o.c_order_id DESC
    LIMIT $2 OFFSET $3
";
$rsD = ($pg) ? @pg_query_params($pg, $sqlData, array($bp_id, $limit, $offset)) : false;

$rowsHtml = '';
if ($rsD) {
    while ($rsD && $r = pg_fetch_assoc($rsD)) {
        $date = (string)$r['order_date']; // YYYY-MM-DD
        $ord  = (string)$r['documentno'];
        $typ  = $typeText($r['issotrx'], $r['c_doctypetarget_id']);
        $st   = $statusText($r['docstatus']);
        $val  = number_format((float)$r['totallines'], 0, ',', ' ') . ' kr';

        // Orderlänk: öppna order-sök (du kan byta till något annat om du vill)
        $ordLink = '<a href="/search_dispatch.php?mode=order&page=1&q=' . rawurlencode($ord) . '" target="_blank" rel="noopener">' . $h($ord) . '</a>';

        $rowsHtml .= '<tr>'
                  .  '<td class="nowrap">'.$h($date).'</td>'
                  .  '<td>'.$ordLink.'</td>'
                  .  '<td>'.$h($typ).'</td>'
                  .  '<td>'.$h($st).'</td>'
                  .  '<td class="text-right nowrap">'.$h($val).'</td>'
                  .  '</tr>';
    }
    pg_free_result($rsD);
}

// --- UI: filterknappar ---
$baseUrl = '/customer_orders.php?bp_id='.(int)$bp_id;

$btnClass = function($t) use ($type){
    return 'btn-filter' . (($type === $t) ? ' is-active' : '');
};

// --- Pagination ---
$pages = ($limit > 0) ? (int)ceil($total / $limit) : 1;
if ($pages < 1) $pages = 1;

$mkUrl = function($p) use ($baseUrl, $type){
    return $baseUrl . '&type=' . rawurlencode($type) . '&page=' . (int)$p;
};

$pagerHtml = '';
if ($pages > 1) {
    $p = $page;
    $prev = max(1, $p - 1);
    $next = min($pages, $p + 1);

    $pagerHtml .= '<div style="margin-top:12px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">';
    $pagerHtml .= '<a class="btn-filter" href="'.$mkUrl(1).'">&laquo; Första</a>';
    $pagerHtml .= '<a class="btn-filter" href="'.$mkUrl($prev).'">&lsaquo; Föreg</a>';
    $pagerHtml .= '<span style="font-weight:700;">Sida '.$h($p).' / '.$h($pages).'</span>';
    $pagerHtml .= '<a class="btn-filter" href="'.$mkUrl($next).'">Nästa &rsaquo;</a>';
    $pagerHtml .= '<a class="btn-filter" href="'.$mkUrl($pages).'">Sista &raquo;</a>';
    $pagerHtml .= '</div>';
}

// --- Render ---
echo '<style>
/* små utilities */
.nowrap{white-space:nowrap}

/* filterknappar */
.btn-filter{
  display:inline-block;padding:6px 10px;border-radius:999px;
  border:1px solid #d1d5db;background:#fff;text-decoration:none;
  color:#111827;font-weight:700;font-size:13px;
}
.btn-filter:hover{background:#f3f4f6}
.btn-filter.is-active{background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8}

/* layout */
.co-wrap{padding:14px 16px}
.co-title{font-size:22px;font-weight:800;margin:6px 0 4px}
.co-sub{color:#6b7280;margin:0 0 10px}
</style>';

echo '<div class="co-wrap">';

$titleName = $custName !== '' ? $custName : ('Kund #' . (int)$bp_id);
echo '<div class="co-title">Alla ordrar: '.$h($titleName);
if ($custValue !== '') echo ' <span style="font-weight:500;">('.$h($custValue).')</span>';
echo '</div>';

echo '<div class="co-sub">Totalt: '.$h($total).' st</div>';

echo '<div style="margin:10px 0 12px;display:flex;gap:8px;flex-wrap:wrap;">'
   . '<a class="'.$btnClass('all').'"      href="'.$baseUrl.'&type=all&page=1">Alla</a>'
   . '<a class="'.$btnClass('sale').'"     href="'.$baseUrl.'&type=sale&page=1">Försäljning</a>'
   . '<a class="'.$btnClass('purchase').'" href="'.$baseUrl.'&type=purchase&page=1">Inköp</a>'
   . '<a class="'.$btnClass('quote').'"    href="'.$baseUrl.'&type=quote&page=1">Offert</a>'
   . '</div>';

if ($rowsHtml === '') {
    echo '<p style="margin-top:12px">Inga ordrar hittades för valt filter.</p>';
} else {
    echo '<table class="table-list">'
       . '<colgroup>'
       .   '<col style="width:12ch"/>'
       .   '<col style="width:12ch"/>'
       .   '<col style="width:14ch"/>'
       .   '<col style="width:14ch"/>'
       .   '<col style="width:14ch"/>'
       . '</colgroup>'
       . '<thead><tr>'
       .   '<th>Datum</th>'
       .   '<th>Order</th>'
       .   '<th>Typ</th>'
       .   '<th>Status</th>'
       .   '<th class="text-right">Värde</th>'
       . '</tr></thead>'
       . '<tbody>'.$rowsHtml.'</tbody>'
       . '</table>';

    echo $pagerHtml;
}

echo '</div>';

include_once("footer.php");

<?php
// bundle_overview.php
// Oversikt over alla egna paket (issalesbundle=Y) som är aktiva på webben.
// Syfte: identifiera paket som inte säljer och kan tas bort.

include_once("top.php");
include_once("header.php");
require_once "Db.php";

$h = function($s){
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
};

$pg = Db::getConnectionAD(false);
if (!$pg) {
    echo '<div style="padding:14px"><h1>Egna paket</h1><p>Kunde inte ansluta till databasen.</p></div>';
    include_once("footer.php");
    exit;
}
@pg_set_client_encoding($pg, "UTF8");

// Hämta alla aktiva egna paket som är publicerade på webben,
// med säljsiffror räknade via packey (bundle-logiken).
$sql = "
    SELECT
        p.m_product_id,
        p.value                                                         AS article,
        TRIM(COALESCE(manu.name,'') || ' ' || COALESCE(p.name,''))     AS product_full,
        pc.m_product_category_id                                        AS category_id,
        COALESCE(pc.name,'')                                            AS category_name,
        COALESCE(pc.value,'')                                           AS category_value,
        COALESCE(pc.sort_priority, 999999)                              AS category_priority,

        -- Sålda via packey (bundle-räkning): 30 dagar
        COALESCE((
            SELECT COUNT(DISTINCT ol.c_order_id)
            FROM c_orderline ol
            INNER JOIN c_order o ON o.c_order_id = ol.c_order_id
            WHERE ol.packey         = p.value
              AND o.c_doctype_id    = 1000030
              AND o.docstatus       = 'CO'
              AND ol.qtyordered     = ol.qtydelivered
              AND ol.qtyordered     > 0
              AND o.created        >= NOW() - INTERVAL '30 days'
        ), 0) AS sold_30d,

        -- 90 dagar
        COALESCE((
            SELECT COUNT(DISTINCT ol.c_order_id)
            FROM c_orderline ol
            INNER JOIN c_order o ON o.c_order_id = ol.c_order_id
            WHERE ol.packey         = p.value
              AND o.c_doctype_id    = 1000030
              AND o.docstatus       = 'CO'
              AND ol.qtyordered     = ol.qtydelivered
              AND ol.qtyordered     > 0
              AND o.created        >= NOW() - INTERVAL '90 days'
        ), 0) AS sold_90d,

        -- Totalt
        COALESCE((
            SELECT COUNT(DISTINCT ol.c_order_id)
            FROM c_orderline ol
            INNER JOIN c_order o ON o.c_order_id = ol.c_order_id
            WHERE ol.packey         = p.value
              AND o.c_doctype_id    = 1000030
              AND o.docstatus       = 'CO'
              AND ol.qtyordered     = ol.qtydelivered
              AND ol.qtyordered     > 0
        ), 0) AS sold_total

    FROM m_product p
    LEFT JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id
    LEFT JOIN m_product_category pc ON pc.m_product_category_id = p.m_product_category_id
    LEFT JOIN (
        SELECT m_product_id, SUM(qtyavailable) AS qtyavailable
        FROM m_product_cache
        WHERE m_warehouse_id = 1000000
        GROUP BY m_product_id
    ) ps ON ps.m_product_id = p.m_product_id
    WHERE p.isactive      = 'Y'
      AND p.issalesbundle = 'Y'
      AND p.isselfservice = 'Y'
      AND (p.launchdate IS NULL OR p.launchdate <= NOW())
      AND (p.discontinued  = 'N' OR COALESCE(ps.qtyavailable, 0) > 0)
      AND (p.demo_product  = 'N' OR COALESCE(ps.qtyavailable, 0) > 0)
    ORDER BY
        COALESCE(pc.sort_priority, 999999) DESC,
        pc.name NULLS LAST,
        manu.name NULLS LAST,
        p.name
";

$rs   = @pg_query($pg, $sql);
$rows = array();
if ($rs) {
    while ($r = pg_fetch_assoc($rs)) {
        $rows[] = $r;
    }
    pg_free_result($rs);
}

$total = count($rows);
?>
<style>
.bo-wrap        { padding: 14px 16px; }
.bo-sub         { color: #6b7280; margin: 0 0 14px; font-size: 14px; }

.table-list     { table-layout: fixed; width: 100%; border-collapse: collapse; font-size: 13px; }
.table-list th,
.table-list td  { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
.table-list thead th { background: #d1f2f0; color: #111; font-weight: 700; text-align: left; }

.col-art        { width: 13ch; }
.col-prod       { width: auto; }
.col-sold       { width: 7ch; }

.cat-head       { background: #f3f4f6; }
.cat-head th    { padding: 7px 10px; font-size: 13px; font-weight: 700; color: #111; text-align: left; }
.cat-head th a  { color: #000; text-decoration: none; }
.cat-head th a:hover { text-decoration: underline; }

.copy-art       { cursor: pointer; }
.copy-art.copied{ outline: 2px solid #a7f3d0; outline-offset: 2px; }

/* Sälj-badge – samma klasser som sold-count-link i search_dispatch */
.sold-badge {
    display: inline-block;
    min-width: 26px;
    padding: 2px 7px;
    border-radius: 10px;
    text-align: center;
    font-weight: 700;
    font-size: 12px;
    text-decoration: none;
    border: 1px solid #d1d5db;
}
.sold-badge:hover { opacity: .8; }
.sold-badge.none  { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }  /* aldrig sålt */
.sold-badge.low   { background: #fef9c3; color: #854d0e; border-color: #fde047; }  /* sålts totalt men inte senaste perioden */
.sold-badge.ok    { background: #dcfce7; color: #166534; border-color: #86efac; }  /* sålts i perioden */
.sold-badge.plain { background: #f3f4f6; color: #374151; border-color: #d1d5db; }  /* neutral (totalt) */

.text-center { text-align: center; }
</style>

<div class="bo-wrap">
<h1>Egna paket på webben</h1>
<p class="bo-sub"><?php echo (int)$total; ?> aktiva egna paket &mdash; klicka på säljtalen för att se orderlistan</p>

<?php if (empty($rows)): ?>
<p>Inga egna paket hittades.</p>
<?php else: ?>

<table class="table-list">
<colgroup>
  <col class="col-art" />
  <col class="col-prod" />
  <col class="col-sold" />
  <col class="col-sold" />
  <col class="col-sold" />
</colgroup>
<thead>
  <tr>
    <th>Artikel</th>
    <th>Produkt</th>
    <th class="text-center" title="Sålda ordrar senaste 30 dagarna">30d</th>
    <th class="text-center" title="Sålda ordrar senaste 90 dagarna">90d</th>
    <th class="text-center" title="Totalt antal sålda ordrar">Totalt</th>
  </tr>
</thead>
<tbody>
<?php
$lastCat = null;
foreach ($rows as $r) {
    $catId   = (int)$r['category_id'];
    $catName = $h($r['category_name']);
    $catVal  = $r['category_value'];

    if ($catId !== $lastCat) {
        $catTitle = ($catName !== '') ? $catName : 'Övrigt';
        if ($catVal !== '' && $catName !== '') {
            $catUrl   = 'https://admin.cyberphoto.se/lagerstatus.php?katID=' . rawurlencode($catVal);
            $catTitle = '<a href="'.$catUrl.'" target="_blank" rel="noopener">'.$catName.'</a>';
        }
        echo '<tr class="cat-head"><th colspan="5">'.$catTitle.'</th></tr>';
        $lastCat = $catId;
    }

    $pid      = (int)$r['m_product_id'];
    $article  = $h($r['article']);
    $prodFull = $h($r['product_full']);
    $sold30   = (int)$r['sold_30d'];
    $sold90   = (int)$r['sold_90d'];
    $soldTot  = (int)$r['sold_total'];

    $soldUrl = '/sold_article.php?product_id='.$pid.'&show_salesbundle=yes';

    // Färgklass för 30d-badgen: avgör om paketet är relevant
    $badge30Class  = ($sold30  > 0) ? 'ok'  : (($soldTot > 0) ? 'low' : 'none');
    $badge90Class  = ($sold90  > 0) ? 'ok'  : (($soldTot > 0) ? 'low' : 'none');
    $badgeTotClass = ($soldTot > 0) ? 'plain' : 'none';

    // Drawer-länk på produktnamnen
    $qs = array('mode'=>'product','q'=>$r['article'],'open'=>'product','id'=>$pid);
    $shareUrl = '/search_dispatch.php?' . http_build_query($qs);
    $prodLink = '<a href="'.$shareUrl.'" class="drawer-link btn-more"'
              . ' data-type="product" data-pid="'.$pid.'" data-id="'.$pid.'" data-article="'.$article.'">'
              . $prodFull . '</a>';

    echo '<tr>'
       . '<td><span class="copy-art" data-article="'.$article.'" title="Kopiera artikelnummer">'.$article.'</span></td>'
       . '<td>'.$prodLink.'</td>'
       . '<td class="text-center"><a href="'.$soldUrl.'" class="sold-badge '.$badge30Class.'" target="_blank" rel="noopener" title="30 dagars försäljning">'.$sold30.'</a></td>'
       . '<td class="text-center"><a href="'.$soldUrl.'" class="sold-badge '.$badge90Class.'" target="_blank" rel="noopener" title="90 dagars försäljning">'.$sold90.'</a></td>'
       . '<td class="text-center"><a href="'.$soldUrl.'" class="sold-badge '.$badgeTotClass.'" target="_blank" rel="noopener" title="Totalt sålda ordrar">'.$soldTot.'</a></td>'
       . '</tr>';
}
?>
</tbody>
</table>

<?php endif; ?>
</div>

<script>
(function(){
  function copyText(t, cb){
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(String(t||'')).then(function(){ cb && cb(true); }, function(){ cb && cb(false); });
    } else {
      try{
        var ta=document.createElement('textarea'); ta.value=String(t||'');
        ta.style.position='fixed'; ta.style.opacity='0'; document.body.appendChild(ta);
        ta.select(); var ok=document.execCommand('copy'); document.body.removeChild(ta);
        cb && cb(ok);
      }catch(e){ cb && cb(false); }
    }
  }
  document.addEventListener('click', function(e){
    var el = e.target && e.target.closest ? e.target.closest('.copy-art') : null;
    if (!el) return;
    var art = el.getAttribute('data-article') || (el.textContent||'').trim();
    if (!art) return;
    copyText(art, function(ok){
      if (!ok) return;
      el.classList.add('copied');
      setTimeout(function(){ el.classList.remove('copied'); }, 1200);
    });
  }, false);
})();
</script>

<?php
include_once("footer.php");
?>

<?php
include_once "top.php";

// hämtar vilken sökning det handlar om
$mode = isset($_GET['mode']) ? strtolower(trim((string)$_GET['mode'])) : 'product';

// Kör endast produkt-fastlane när användaren är i produktmode
$isProductMode = ($mode === 'product' || $mode === '');

// Fast lane: exakt artikelnummer -> gå direkt till drawer
$qRaw = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$isWide = isset($_GET['wide']) ? trim((string)$_GET['wide']) : '';
$hasOpen = !empty($_GET['open']) || !empty($_GET['id']);

// OM filter så ska direktvisning inte ske
$hasFilters = false;
$filterKeys = ['in_stock','discontinued','old_tradeins','used_web','used_offweb','hide_tradein','hide_demo','not_web']; // byt till dina faktiska GET-namn

foreach ($filterKeys as $k) {
  if (!empty($_GET[$k])) { $hasFilters = true; break; }
}

$isSingleToken = ($qRaw !== '' && !preg_match('/\s/', $qRaw));
$looksSafe     = ($isSingleToken && !preg_match('/%/', $qRaw)); // blocka bara % (LIKE-wildcard)

// if (!$hasOpen && $looksSafe && !$isWide && !$hasFilters) {
if ($isProductMode && !$hasOpen && $looksSafe && !$isWide && !$hasFilters) {

    $q = $qRaw;
    $qDigits = preg_replace('/\D+/', '', $q);

    $conn = Db::getConnectionAD(false);
    if ($conn) { @pg_set_client_encoding($conn, "UTF8"); } // AD brukar vara UTF8; byt till LATIN1 om ni kör så här i produktlistan

    // ------------------------------------------------------------
    // 1) Fastlane #1: internt artnr (p.value)
    //    - här tillåter vi dubletter men väljer "bästa" kandidaten
    //      med en prioriterad ORDER BY
    // ------------------------------------------------------------
    $sqlValue = "
        SELECT p.m_product_id
        FROM m_product p
        WHERE p.isactive = 'Y'
          AND upper(p.value) = upper($1)
        ORDER BY
          CASE WHEN COALESCE(p.isselfservice,'N') = 'Y' THEN 0 ELSE 1 END,
          p.updated DESC NULLS LAST,
          p.m_product_id DESC
        LIMIT 1
    ";
    $r1 = ($conn) ? @pg_query_params($conn, $sqlValue, array($q)) : false;
    if ($r1 && pg_num_rows($r1) >= 1) {
        $hit = $r1 ? pg_fetch_assoc($r1) : null;
        pg_free_result($r1);

        if (!empty($hit['m_product_id'])) {
            $pid = (int)$hit['m_product_id'];
            header("Location: /search_dispatch.php?mode=product&q=" . rawurlencode($q) . "&open=product&id=" . $pid);
            exit;
        }
    }
    if ($r1) pg_free_result($r1);

    // ------------------------------------------------------------
    // 2) Fastlane #2: tillverkarens artnr (manufacturerproductno)
    //    - redirect ENDAST om unik
    // ------------------------------------------------------------
    $sqlMpn = "
        SELECT p.m_product_id
        FROM m_product p
        WHERE p.isactive = 'Y'
          AND upper(COALESCE(p.manufacturerproductno,'')) = upper($1)
        LIMIT 2
    ";
    $r2 = ($conn) ? @pg_query_params($conn, $sqlMpn, array($q)) : false;
    $hits = array();
    if ($r2) {
        while ($r2 && $row = pg_fetch_assoc($r2)) $hits[] = (int)$row['m_product_id'];
        pg_free_result($r2);
    }
    if (count($hits) === 1 && $hits[0] > 0) {
        $pid = $hits[0];
        header("Location: /search_dispatch.php?mode=product&q=" . rawurlencode($q) . "&open=product&id=" . $pid);
        exit;
    }

    // ------------------------------------------------------------
    // 3) Fastlane #3: EAN/UPC (p.upc), digits-only
    //    - redirect ENDAST om unik
    // ------------------------------------------------------------
    if ($qDigits !== '') {
        $sqlUpc = "
            SELECT p.m_product_id
            FROM m_product p
            WHERE p.isactive = 'Y'
              AND regexp_replace(COALESCE(p.upc,''), '[^0-9]', '', 'g') = $1
            LIMIT 2
        ";
        $r3 = ($conn) ? @pg_query_params($conn, $sqlUpc, array($qDigits)) : false;
        $hits = array();
        if ($r3) {
            while ($r3 && $row = pg_fetch_assoc($r3)) $hits[] = (int)$row['m_product_id'];
            pg_free_result($r3);
        }
        if (count($hits) === 1 && $hits[0] > 0) {
            $pid = $hits[0];
            header("Location: /search_dispatch.php?mode=product&q=" . rawurlencode($q) . "&open=product&id=" . $pid);
            exit;
        }
    }
}

include_once "header.php";

// ---- CSS ----
echo '<style>
.with-drawer-gutter{margin-right:8px}
.table-list{table-layout:fixed;width:100%;border-collapse:collapse;font-size:13px}
.table-list th,.table-list td{padding:8px 10px;border-bottom:1px solid #e5e7eb;vertical-align:middle}
.table-list thead th{background:#d1f2f0;color:#111;font-weight:700;text-align:left}
.table-list .text-right{text-align:right}
.table-list .text-center{text-align:center}
.cat-head{background:#f3f4f6;font-weight:700}
.cat-head th{padding:8px 10px;font-size:13px;color:#111;text-align:left}

/* col widths (tajtare på siffror, mer plats för produkt) */
.table-list col.col-art{width:10ch}
.table-list col.col-link{width:28px}
.table-list col.col-prod{width:auto}
.table-list col.col-price{width:11ch}  /* var 14ch */
.table-list col.col-tg{width:7ch}      /* var 8ch */
.table-list col.col-num{width:6ch}     /* var 8ch */
.table-list col.col-act{width:60px}    /* var 72px */
.table-list td:last-child{white-space:nowrap;text-align:center;padding-right:8px}
.table-list td.text-right,
.table-list td.text-center { padding-left:6px; padding-right:6px; }
.table-list td.text-right,
.table-list td.text-center { font-variant-numeric: tabular-nums; }
.copy-link svg,
.popup-link svg{vertical-align:middle}

/* webblänk-ikon */
.web-link svg{display:inline-block;vertical-align:middle}


/* behåll dina färger; lägger bara in spacing fallback om du redan har .b* klasserna */
.b { display:inline-block; padding:2px 8px; border-radius:999px; border:1px solid transparent;
     font-weight:700; font-size:12px; line-height:20px; }



/* TG-chip */
.tg-chip{display:inline-block;padding:2px 6px;border-radius:6px;font-weight:700}
.tg-chip.pos{background:#e6ffed;color:#065f46}     /* grön */
.tg-chip.neg{background:#ffe6e6;color:#991b1b}     /* röd  */
.tg-chip.zero{background:#fef3c7;color:#92400e}     /* orange vid 0,0% */

/* kopiera-ikon */
.copy-link svg{vertical-align:middle}
.popup-link svg{vertical-align:middle}

/* summeringsrad */
.summary-row td{background:#f9fafb;font-weight:700}

.search-filters{display:flex;gap:18px;align-items:center;margin:6px 0 10px 0}
.search-filters label{display:inline-flex;gap:6px;align-items:center;font-size:13px;color:#111}
.search-filters input[type="checkbox"]{transform:translateY(1px)}

/* ===== Filterpanel ===== */
.filter-bar{ display:flex; gap:12px; align-items:flex-start; margin:6px 0 12px 0; }
.filter-box{ display:inline-flex; align-items:center; gap:14px; flex-wrap:wrap; padding:8px 12px; border:1px solid #e5e7eb; background:#f9fafb; border-radius:10px; }
.filter-box label{ display:inline-flex; gap:6px; align-items:center; font-size:13px; color:#111; white-space:nowrap; }
.filter-box input[type="checkbox"]{ transform:translateY(1px); }

/* Stacka panelerna på smal vy */
@media (max-width: 900px){ .filter-bar{ flex-direction:column; } }

/* Resultatrad */
.result-info{ display:inline-block; margin:6px 0 8px 0; padding:8px 12px; border:1px solid #e5e7eb; background:#f9fafb; border-radius:10px; font-size:15px; color:#111; }
.result-info strong{ font-size:16px; font-weight:800; }

/* Rubriker */
.table-list thead th{ font-size:13px; font-weight:700; }
.cat-head{ background:#f3f4f6; }
.cat-head th{ padding:8px 10px; font-size:13px; font-weight:700; color:#111; text-align:left; }
.cat-head th a{ font-size:13px; font-weight:700; color:#000000; text-decoration:none; }
.cat-head th a:hover{ text-decoration:underline; }

.copy-art{cursor:pointer}
.copy-art.copied{outline:2px solid #a7f3d0; outline-offset:2px}

/* Filterpills */
.filter-box label.filter{
  display:inline-flex; align-items:center; gap:6px;
}
.filter-box .tag{
  display:inline-block;
  padding:4px 8px;
  border:1px solid #e5e7eb;
  border-radius:999px;
  font-size:12px;
  background:#fff;
  color:#111;
  user-select:none;
}
.filter-box input[type="checkbox"]{ /* liten vertikal align-fix */
  transform:translateY(1px);
}

/* Aktivt utseende  specifika färger per filter */
.filter-box input[name="in_stock"]:checked + .tag{
  background:#ecfdf5; border-color:#a7f3d0; color:#065f46; /* grön */
}
.filter-box input[name="discontinued"]:checked + .tag{
  background:#e3e3e3; border-color:#d1d5db; color:#111;    /* neutral/grå */
}
.filter-box input[name="not_web"]:checked + .tag{
  background:#e3e3e3; border-color:#d1d5db; color:#111;    /* neutral/grå */
}
.filter-box input[name="used_web"]:checked + .tag{
  background:#ecfdf5; border-color:#a7f3d0; color:#065f46; /* grön */
}
.filter-box input[name="used_offweb"]:checked + .tag{
  background:#f7d5aa; border-color:#fed7aa; color:#9a3412; /* orange */
}
.filter-box input[name="old_tradeins"]:checked + .tag{
  background:#cad5f9; border-color:#c7d2fe; color:#3730a3; /* blå/lila */
}

/* (valfritt) hover-feedback på alla */
.filter-box label.filter:hover .tag{
  border-color:#cfd6e0;
}
.table-list td.col-contacts,
.table-list td.col-emails,
.table-list td.col-phones{
  white-space: normal;
  word-break: break-word;
  vertical-align: top;
}

/* valfritt så inte mail/telefon-kolumnerna blir för breda */
.table-list td.col-emails,
.table-list td.col-phones{
  max-width: 420px;
}
.pref-modal{position:fixed;inset:0;background:rgba(0,0,0,.35);display:none;align-items:center;justify-content:center;z-index:9999;}
.pref-modal.show{display:flex;}
.pref-dialog{background:#fff;border:1px solid #e5e7eb;border-radius:10px;min-width:320px;max-width:420px;padding:12px;}
.pref-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;}
.pref-close{border:1px solid #cfd6e0;background:#fff;border-radius:6px;width:28px;height:28px;cursor:pointer;}
.pref-body{padding:6px 2px;}
.pref-row{display:flex;align-items:center;gap:8px;font-size:14px;}
.pref-foot{display:flex;justify-content:flex-end;margin-top:10px;gap:8px;}
.pref-btn{padding:6px 10px;border:1px solid #cfd6e0;border-radius:6px;background:#fff;cursor:pointer;}
.pref-btn:hover{background:#f2f6ff;border-color:#b7c4da;}

/* standardknapp har du redan; lägg bara en "active"-stil */
.dw-icon-btn.active{
  border-color:#ef4444;              /* röd kant */
  background:#fff5f5;                /* ljusröd bakgrund */
}
.dw-icon-btn.active svg{ stroke:#b91c1c; } /* röd ikon */
/* Neutral baseline (som du har) */
.active-prefs{
  display:none; margin:6px 0 10px 0; padding:6px 8px;
  border:1px solid #e5e7eb; background:#fafafa; border-radius:8px;
}

/* Aktiv state: bärnstensgul warning */
.active-prefs.on{
  background:#fff5cf;           /* amber-100 */
  border-color:#fddda7;         /* amber-500 */
  box-shadow:0 0 0 2px rgba(245,158,11,.12) inset;
  color:#7C2D12;                /* amber-900 */
}

/* Pillerna  poppa lite men funka i båda bakgrunderna */
.active-prefs .apill{
  display:inline-block; margin:0 6px 4px 0; padding:2px 10px;
  border-radius:999px; font-weight:700; font-size:12px;
  border:1px solid #cfd6e0; background:#fff; color:#111;
  cursor:pointer; user-select:none;
}
.active-prefs .apill b{ font-weight:900; margin-left:6px; }
.active-prefs .apill:hover{ background:#f2f6ff; border-color:#b7c4da; }

.has-tip{position:relative}
.has-tip::after {
  transition: opacity .15s ease, transform .15s ease;
  transform: translateY(6px);
}
.has-tip:hover::after {
  transform: translateY(0);
}
.has-tip:hover::after,.has-tip:hover::before{display:block}


/* Om rader har overflow:hidden någonstans: släpp igenom tooltipen */
.table-list tbody tr{ overflow:visible; }

.dw-table-orderlines .ol-sub td{
    background:#fafafa;
    border-top:0;
    font-size:11px;
}
.dw-table-orderlines .ol-main td{
    border-bottom:0;
}
.product-summary-row td{
  border-top: 2px solid #e5e7eb;
  font-weight: 600;
  background:#f9fafb;
}

.result-info--direct{
  align-items:center;
  gap:10px;
  justify-content:flex-start;   /* viktig: håll allt till vänster */
}

.result-pill-link{
  margin-left:8px;
  display:inline-flex;
  align-items:center;
  height:24px;
  padding:0 10px;
  border-radius:999px;
  border:1px solid #cfd6e0;
  background:#fff;
  color:#111;
  text-decoration:none;
  font-weight:700;
  font-size:12px;
}

.result-pill-link:hover{
  background:#f2f6ff;
  border-color:#b7c4da;
}
.sold-count-link {
    display: inline-block;
    min-width: 22px;
    padding: 1px 6px;
    border-radius: 10px;
    text-align: center;
    font-weight: 700;
    font-size: 12px;
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    text-decoration: none;
}
.sold-count-link:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
    color: #111827;
}

</style>';

// ---- GEMENSAM INPUT & ESCAPE ----

// HTML-escape för ISO-8859-1, med auto-konvertering från UTF-8 vid behov
$h = function($s){
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
};

$mode = isset($_GET['mode']) ? strtolower(trim($_GET['mode'])) : 'product';
$q    = isset($_GET['q'])    ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$id   = isset($_GET['id'])   ? (int)$_GET['id'] : 0;


// ======================= ORDER-LÄGE =======================
if ($mode === 'order') {

    $orderNo = trim($q);

    echo '<div class="with-drawer-gutter">';

    // 1) Tomt fält
    if ($orderNo === '') {
        echo '<div class="result-info" style="background:#fff7ed;border:1px solid #fed7aa;">';
        echo 'Ange ett ordernummer i sökfältet.';
        echo '</div>';
        echo '</div>';
        include_once "footer.php";
        return;
    }

    // 2) Vi tillåter bara rena siffror (ingen fuzzy-sökning här)
    if (!ctype_digit($orderNo)) {
        echo '<div class="result-info" style="background:#fee2e2;border:1px solid #fecaca;">';
        echo 'Ordernummer ska bestå av enbart siffror &ndash; du angav <strong>&quot;'.$h($orderNo).'&quot;</strong>.';
        echo '</div>';
        echo '</div>';
        include_once "footer.php";
        return;
    }

    // 3) Lite minimal feedback om vad vi försöker öppna
    echo '<div class="result-info">';
    echo 'Visar detaljer för order <strong>'.$h($orderNo).'</strong>.';
    echo '</div>';

    // 4) Auto-öppna drawer_details med type=order + id = ordernummer
    $orderNoJs = json_encode($orderNo); // säker JS-literal

    echo '<script>
    (function(){
        function openOrderDrawer(){
            var el = document.createElement("a");
            el.href = "#";
            el.className = "drawer-link btn-more";
            el.setAttribute("data-type", "order");
            el.setAttribute("data-id", '.$orderNoJs.');
            document.body.appendChild(el);
            el.click();
            setTimeout(function(){ el.remove(); }, 0);
        }
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", openOrderDrawer);
        } else {
            openOrderDrawer();
        }
    })();
    </script>';

    echo '</div>'; // .with-drawer-gutter

    include_once "footer.php";
    return;
}


// ======================= CUSTOMER-LÄGE =======================
if ($mode === 'customer') {

    // Primär sökning via klass
    $res = CSearch::searchCustomersAD($q, 50, $page);

    // Frontend fallback till sida 1 om total>0 men rows saknas
    if ((int)$res['total'] > 0 && (empty($res['rows']) || !is_array($res['rows']))) {
        $res2 = CSearch::searchCustomersAD($q, 50, 1);
        if (!empty($res2['rows']) && is_array($res2['rows'])) {
            $res  = $res2;
            $page = 1;
        }
    }

    // ===== SUPER-FALLBACK: egen enkel fråga om det fortfarande saknas rader =====
    if ((int)$res['total'] > 0 && (empty($res['rows']) || !is_array($res['rows']))) {
        $pg = Db::getConnectionAD(false);
        if ($pg) {
            if ($pg) { @pg_set_client_encoding($pg, "UTF8"); }

            $q_like_src = $q;
            $like       = '%'.$q_like_src.'%';
            $hasAt      = (strpos($q, '@') !== false);
            $digits     = preg_replace('/\D+/', '', $q);
            $hasDigits5 = (strlen($digits) >= 5);

            // Basdel (namn, namn2, kundnr)
            $sql  = "
                SELECT bp.c_bpartner_id,
                       bp.value AS bp_value,
                       TRIM(COALESCE(bp.name,'') || CASE WHEN COALESCE(bp.name2,'')<>'' THEN '  '||bp.name2 ELSE '' END) AS fullname
                FROM c_bpartner bp
                WHERE bp.isactive='Y'
                  AND (
                       bp.name  ILIKE $1
                    OR bp.name2 ILIKE $2
                    OR bp.value ILIKE $3
            ";
            $params = array($like, $like, $like);
            $pi = 4;

            // Mail (om @ finns i söksträngen)
            if ($hasAt) {
                $sql .= " OR EXISTS (
                            SELECT 1 FROM ad_user u
                            WHERE u.c_bpartner_id = bp.c_bpartner_id
                              AND u.isactive='Y'
                              AND u.email ILIKE $".$pi."
                          )";
                $params[] = $like; $pi++;
            }

            // Telefon (om vi har minst 5 siffror att matcha)
            if ($hasDigits5) {
                $dLike = '%'.$digits.'%';
                $sql .= " OR EXISTS (
                            SELECT 1 FROM ad_user u
                            WHERE u.c_bpartner_id = bp.c_bpartner_id
                              AND u.isactive='Y'
                              AND (
                                   regexp_replace(COALESCE(u.phone,''),  '[^0-9]', '', 'g') LIKE $".$pi."
                                OR regexp_replace(COALESCE(u.phone2,''), '[^0-9]', '', 'g') LIKE $".($pi+1)."
                              )
                          )";
                $params[] = $dLike;
                $params[] = $dLike;
                $pi += 2;
            }

            // Namn på kontakt (alltid med i fallbacken för att vara snäll)
            $sql .= " OR EXISTS (
                        SELECT 1 FROM ad_user u
                        WHERE u.c_bpartner_id = bp.c_bpartner_id
                          AND u.isactive='Y'
                          AND ( (COALESCE(u.firstname,'') || ' ' || COALESCE(u.lastname,'')) ILIKE $".$pi." )
                     )";
            $params[] = $like; $pi++;

            // Stäng WHERE + sort/limit
            $sql .= ")
                ORDER BY UPPER(bp.name) ASC, bp.c_bpartner_id
                LIMIT 50";

            $rs = ($pg) ? @pg_query_params($pg, $sql, $params) : false;

            $fallback_rows = array();
            $ids = array();
            if ($rs) {
                while ($rs && $r = pg_fetch_assoc($rs)) {
                    $ids[] = (int)$r['c_bpartner_id'];
                    $fallback_rows[] = array(
                        'bp_id'    => (int)$r['c_bpartner_id'],
                        'bp_value' => (string)$r['bp_value'],
                        'name'     => (string)$r['fullname'],
                        'contacts' => array(),
                        'emails'   => array(),
                        'phones'   => array(),
                    );
                }
                pg_free_result($rs);
            }

            // Gruppnamn för badge
            $grpMap = array();
            if ($ids) {
                $in = implode(',', $ids);
                $sqlG = "
                    SELECT bp.c_bpartner_id, COALESCE(g.name,'') AS group_name
                    FROM c_bpartner bp
                    LEFT JOIN c_bp_group g ON g.c_bp_group_id = bp.c_bp_group_id
                    WHERE bp.c_bpartner_id IN ($in)
                ";
                $rg = ($pg) ? @pg_query($pg, $sqlG) : false;
                if ($rg) {
                    while ($rg && $g = pg_fetch_assoc($rg)) {
                        $grpMap[(int)$g['c_bpartner_id']] = (string)$g['group_name'];
                    }
                    pg_free_result($rg);
                }
            }

            // Kontakter i batch (för att rendera kolumnerna)
            $contactMap = array();
            if ($ids) {
                $in = implode(',', $ids);
                $sqlU = "
                    SELECT u.c_bpartner_id,
                           TRIM(COALESCE(u.firstname,'') || ' ' || COALESCE(u.lastname,'')) AS fullname,
                           NULLIF(u.email,'')  AS email,
                           NULLIF(u.phone,'')  AS phone,
                           NULLIF(u.phone2,'') AS mobile
                    FROM ad_user u
                    WHERE u.isactive='Y' AND u.c_bpartner_id IN ($in)
                    ORDER BY UPPER(u.lastname), UPPER(u.firstname)
                ";
                $ru = ($pg) ? @pg_query($pg, $sqlU) : false;
                if ($ru) {
                    while ($ru && $u = pg_fetch_assoc($ru)) {
                        $bid = (int)$u['c_bpartner_id'];
                        if (!isset($contactMap[$bid])) $contactMap[$bid] = array('names'=>array(), 'emails'=>array(), 'phones'=>array());
                        if (!empty($u['fullname'])) $contactMap[$bid]['names'][]  = (string)$u['fullname'];
                        if (!empty($u['email']))    $contactMap[$bid]['emails'][] = (string)$u['email'];
                        if (!empty($u['phone']))    $contactMap[$bid]['phones'][] = (string)$u['phone'];
                        if (!empty($u['mobile']))   $contactMap[$bid]['phones'][] = (string)$u['mobile'];
                    }
                    pg_free_result($ru);
                }
            }

            // Mappa in kontakter + group_name
            if ($fallback_rows) {
                foreach ($fallback_rows as &$fr) {
                    $bid = (int)$fr['bp_id'];
                    if (isset($contactMap[$bid])) {
                        $fr['contacts'] = $contactMap[$bid]['names'];
                        $fr['emails']   = $contactMap[$bid]['emails'];
                        $fr['phones']   = $contactMap[$bid]['phones'];
                    }
                    $fr['group_name'] = isset($grpMap[$bid]) ? $grpMap[$bid] : '';
                }
                unset($fr);
                $res['rows'] = $fallback_rows;
            }
        }
    }
    // ===== SLUT super-fallback =====


    // Auto-open vid exakt värde + 1 träff
    if (!empty($res['exact_value_match']) && (int)$res['total'] === 1 && !empty($res['rows'][0]['bp_id'])) {
        echo '<script>(function(){function go(){if(window.triggerCustomer){window.triggerCustomer('.(int)$res['rows'][0]['bp_id'].');return;}setTimeout(go,100);}go();})();</script>';
    }

    echo '<div class="with-drawer-gutter search-customer">';
    echo '<div class="result-info"><strong>' . (int)$res['total'] . ' st</strong> träffar på <strong>&quot;' . $h($q) . '&quot;</strong></div>';

	// CSS (badge + kopiering + omsättning)
	echo '<style>
	  .badge-group{display:inline-block;padding:2px 8px;border-radius:999px;border:1px solid #e5e7eb;background:#eef2ff;font-size:12px;font-weight:700;color:#1f2937;vertical-align:middle;margin-left:8px}
	  .badge-group--leverantor{background:#fff7ed;border-color:#fed7aa}
	  .badge-group--foretagskund{background:#ecfeff;border-color:#a5f3fc}
	  .badge-group--aterforsaljare{background:#f0fdf4;border-color:#bbf7d0}
	  .badge-group--konsult{background:#fef2f2;border-color:#fecaca}
	  .badge-group--anstalld{background:#f5f5f5;border-color:#e5e7eb}

	  .copy-num{cursor:pointer;user-select:none;padding:0 2px;border-radius:4px}
	  .copy-num:hover{text-decoration:underline dotted}
	  .copy-num.copied{background:#ecfdf5;box-shadow:inset 0 0 0 1px #34d399}

	  .col-turnover{white-space:nowrap;text-align:right}
	  .turnover{font-weight:700;white-space:nowrap}
	  .turnover--debt{color:#b91c1c}
	  .turnover[data-tooltip]{position:relative;cursor:help}
	  .turnover[data-tooltip]:hover:after{
		content: attr(data-tooltip);
		position:absolute;
		right:0;
		top:100%;
		margin-top:6px;
		background:#111827;
		color:#fff;
		padding:6px 8px;
		border-radius:8px;
		font-size:12px;
		font-weight:700;
		white-space:nowrap;
		z-index:50;
		box-shadow:0 6px 20px rgba(0,0,0,.25);
	  }
		.th-omsdatum,
		.td-omsdatum {
		  width: 110px;          /* justera vid behov: 100120px är sweet spot */
		  white-space: nowrap;
		  text-align: center;
		}
	</style>';

	// ? Viktigt: öppna tabellen FÖRE colgroup/thead
	echo '<table class="table-list">';

	echo '<colgroup>
			<col />
			<col style="width:16ch" />
			<col style="width:24ch" />
			<col style="width:24ch" />
			<col style="width:18ch" />
		  </colgroup>';

	echo '<thead><tr>
			<th>Namn</th>
			<th class="th-omsdatum">Oms/Datum</th>
			<th>Kontakter</th>
			<th>E-post</th>
			<th>Telefon</th>
		  </tr></thead><tbody>';

	// Lokal helper: badge (ingen för "Privatkund")
	$renderBpGroupBadge = function($name) use ($h) {
	  $g = trim((string)$name);
	  if ($g === '') return '';
	  if (stripos($g, 'privat') === 0) return '';
	  $map  = array('Å'=>'A','Ä'=>'A','Ö'=>'O','å'=>'a','ä'=>'a','ö'=>'o');
	  $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/','-', strtr($g,$map)),'-'));
	  return '<span class="badge-group badge-group--'.$slug.'">'.$h($g).'</span>';
	};

	if (!empty($res['rows']) && is_array($res['rows'])) {

	  // Engångs-anslutning för on-demand-gruppläsning
	  $__pg_group = Db::getConnectionAD(false);
	  if ($__pg_group) { if ($__pg_group) { @pg_set_client_encoding($__pg_group, "UTF8"); } }
	  $__grpCache = array();

	  $grpMap = array(); // kan fyllas ovan i super-fallback, men säkra att den finns

	  foreach ($res['rows'] as $r) {
		$idBp   = (int)$r['bp_id'];
		$name   = $h($r['name']);

		// Omsättning + öppen balans
		$turnover = isset($r['actuallifetimevalue']) ? (float)$r['actuallifetimevalue'] : 0.0;
		$openBal  = isset($r['totalopenbalance'])    ? (float)$r['totalopenbalance']    : 0.0;

		$fmtKr = function($n){
		  return number_format((float)$n, 0, ',', ' ') . ' kr';
		};

		$hasTurnover = (abs($turnover) > 0.00001);

		// Öppen balans visas med 2 decimaler (se punkt 2)
		$openBalTxt  = number_format((float)$openBal, 2, ',', ' ') . ' kr';

		$hasDebt = (abs($openBal) > 0.00001);

		// Uppdaterad (YYYY-MM-DD)
		$upd = isset($r['upddate']) ? trim((string)$r['upddate']) : '';
		$updDate = ($upd !== '') ? substr($upd, 0, 10) : '';
		$updHtml = ($updDate !== '') ? '<div class="oms-date">'.$h($updDate).'</div>' : '';

		$turnoverHtml = '';
		if ($hasTurnover || $updHtml !== '') {

		  $turnoverTxt = $hasTurnover
			? number_format((float)$turnover, 0, ',', ' ') . ' kr'
			: '';

		  $turnoverClass = 'turnover' . ($hasDebt ? ' turnover--debt' : '');
		  $tooltip = $hasDebt ? ' data-tooltip="Öppen balans: '.$h($openBalTxt).'"' : '';

		  $turnSpan = $hasTurnover
			? '<div class="oms-val"><span class="'.$turnoverClass.'"'.$tooltip.'>'.$h($turnoverTxt).'</span></div>'
			: '<div class="oms-val"></div>';

		  $turnoverHtml = '<div class="oms-cell">'.$turnSpan.$updHtml.'</div>';
		}

		// Länk som öppnar drawer
		$nameLink = '<a class="drawer-link" data-type="customer" data-id="'.$idBp.'" '.
					'href="/search_dispatch.php?mode=customer&q='.rawurlencode($q).'&drawer='.$idBp.'">'.
					$name.'</a>';

		// Gruppnamn => badge
		$gname = '';
		if (!empty($r['group_name'])) {
		  $gname = (string)$r['group_name'];
		} elseif (isset($grpMap[$idBp]) && $grpMap[$idBp] !== '') {
		  $gname = (string)$grpMap[$idBp];
		} elseif ($__pg_group) {
		  if (!isset($__grpCache[$idBp])) {
			$rsG = pg_query_params($__pg_group,
			  "SELECT COALESCE(g.name,'') FROM c_bpartner bp
			   LEFT JOIN c_bp_group g ON g.c_bp_group_id = bp.c_bp_group_id
			   WHERE bp.c_bpartner_id = $1 LIMIT 1", array($idBp));
			$__grpCache[$idBp] = ($rsG && pg_num_rows($rsG)) ? (string)pg_fetch_result($rsG, 0, 0) : '';
			if ($rsG) pg_free_result($rsG);
		  }
		  $gname = $__grpCache[$idBp];
		}

		$badgeHtml = ($gname !== '' && stripos($gname, 'privat') !== 0) ? $renderBpGroupBadge($gname) : '';

		// Kopierbart kund-/lev.nr
		$copyNum = '';
		if (!empty($r['bp_value'])) {
		  $no = $h($r['bp_value']);
		  $copyNum = ' <span class="copy-num" data-copy="'.$no.'" title="Kopiera kundnummer">('.$no.')</span>';
		}

		// NYTT: Uppdaterad
		$upd = isset($row['updated']) ? trim((string)$row['updated']) : '';
		$updDate = '';
		if ($upd !== '') {
		  // AD/PG brukar ge "YYYY-MM-DD HH:MM:SS" eller med .ms  vi tar första 10 tecken
		  $updDate = substr($upd, 0, 10);
		}

		// Kontakter / E-post / Telefon
		$contactsHtml = !empty($r['contacts']) ? implode('<br>', array_map($h, (array)$r['contacts'])) : '';
		$emailsHtml   = !empty($r['emails'])   ? implode('<br>', array_map($h, (array)$r['emails']))   : '';
		$phonesHtml   = !empty($r['phones'])   ? implode('<br>', array_map($h, (array)$r['phones']))   : '';

		echo '<tr>';
		echo   '<td class="col-name">'.$nameLink.' '.$badgeHtml.$copyNum.'</td>';
		echo   '<td class="col-turnover td-omsdatum">'.$turnoverHtml.'</td>';
		echo   '<td class="col-contacts">'.$contactsHtml.'</td>';
		echo   '<td class="col-emails">'.$emailsHtml.'</td>';
		echo   '<td class="col-phones">'.$phonesHtml.'</td>';
		echo '</tr>';
	  }

	} else {
	  echo '<tr><td colspan="5">Inga träffar.</td></tr>';
	}

	echo '</tbody></table>';

    echo '</div>'; // with-drawer-gutter

    // JS: kopiera kundnummer
    ?>
    <script>
    (function(){
      if (window.__copyNumInit) return; window.__copyNumInit = true;
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
        var el = e.target && e.target.closest ? e.target.closest('.copy-num') : null;
        if (!el) return;
        var txt = el.getAttribute('data-copy') || '';
        if (!txt) return;
        copyText(txt, function(ok){
          if (!ok) return;
          var old = el.getAttribute('title') || 'Kopiera kundnummer';
          el.setAttribute('title','Kopierat!');
          el.classList.add('copied');
          setTimeout(function(){
            el.classList.remove('copied');
            el.setAttribute('title', old);
          }, 1200);
        });
      }, false);
    })();
    </script>
    <?php

    include_once "footer.php";
    return;
}


// ======================= PRODUKT-LÄGE =======================

// Läs filter från GET (gäller bara produktläget)
$onlyInStock      = !empty($_GET['in_stock']);
$discontinuedOnly = !empty($_GET['discontinued']);
$notWeb           = !empty($_GET['not_web']);
$oldTradeIns      = !empty($_GET['old_tradeins']);
$usedWeb          = !empty($_GET['used_web']);
$usedOffWeb       = !empty($_GET['used_offweb']);

// hjälpare
function getCookieBool($name, $default = false){
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] === '1' : $default;
}

$prefHideTradeIn = getCookieBool('pref_hide_tradein', false);
$prefHideDemo    = getCookieBool('pref_hide_demo', false);

// Direktläge: om vi har id + open=product => behandla som direkt träff
$directProductId = 0;
if ($id > 0 && isset($_GET['open']) && $_GET['open'] === 'product') {
    $directProductId = $id;
}
$isDirectProductView = ($directProductId > 0);

// Hämta data (skicka vidare filtret + direct-product-id)
$res = CSearch::searchProductsAD(
    $q,
    50,
    $page,
    array(
        'product_id'    => $directProductId,
        'in_stock'      => $onlyInStock,
        'discontinued'  => $discontinuedOnly,
        'old_tradeins'  => $oldTradeIns,
        'used_web'      => $usedWeb,
        'used_offweb'   => $usedOffWeb,
        'hide_tradein'  => $prefHideTradeIn,
        'hide_demo'     => $prefHideDemo,
        'not_web'       => $notWeb,
    )
);

if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    error_log('[search_dispatch] total='.(int)$res['total'].' rows='. (isset($res['rows'])?count($res['rows']):-1));
}

// ---- UI ----
echo '<div class="with-drawer-gutter">';

// Filterpanel
echo '<form id="search-filters" class="filter-bar" method="get" action="">';
  // behåll söksträng & sida
  echo '<input type="hidden" name="q" value="'.$h($q).'">';
  echo '<input type="hidden" name="page" id="pageField" value="'.(int)$page.'">';
  // behåll mode
  echo '<input type="hidden" name="mode" value="product">';

  // Vänstra boxen  tar bara plats den behöver
  echo '<div class="filter-box">';
    echo '<label class="filter"><input type="checkbox" name="in_stock" value="1"'.($onlyInStock?' checked':'').'><span class="tag">Endast i lager</span></label>';
    echo '<label class="filter"><input type="checkbox" name="discontinued" value="1"'.($discontinuedOnly?' checked':'').'><span class="tag">Utgångna</span></label>';
    echo '<label class="filter"><input type="checkbox" name="not_web" value="1"'.($notWeb?' checked':'').'><span class="tag">Ej med</span></label>';
  echo '</div>';

  // Högra boxen  visas ENDAST för trade-in
  if (!empty($res['isTradeIn'])) {
    echo '<div class="filter-box">';
      echo '<label class="filter"><input type="checkbox" name="used_web" value="1"'.($usedWeb?' checked':'').'><span class="tag">Ute webb</span></label>';
      echo '<label class="filter"><input type="checkbox" name="used_offweb" value="1"'.($usedOffWeb?' checked':'').'><span class="tag">Ej webb</span></label>';
      echo '<label class="filter"><input type="checkbox" name="old_tradeins" value="1"'.($oldTradeIns?' checked':'').'><span class="tag">Gamla inbyten</span></label>';
    echo '</div>';
  }

echo '</form>';

echo '<div id="activePrefs" class="active-prefs" style="display:none"></div>';

if ($isDirectProductView) {
    $wideUrl = '/search_dispatch.php?mode=product&q=' . rawurlencode($q) . '&wide=1';

    echo '<div class="result-info result-info--direct">'
       .   '<strong>Direktvisning</strong> av <strong>&quot;' . $h($q) . '&quot;</strong>'
       .   '<a class="result-pill-link" href="'.$wideUrl.'" title="Visa bred sökning istället">Bred sökning</a>'
       . '</div>';
} else {
    echo '<div class="result-info"><strong>' . (int)$res['total'] . ' st</strong> träffar på <strong>&quot;' . $h($q) . '&quot;</strong></div>';
}

echo '<table class="table-list">';

echo <<<HTML
<colgroup>
  <col class="col-art" />
  <col class="col-link" />
  <col class="col-prod" />
  <col class="col-price" />
  <col class="col-price" />
  <col class="col-tg" />
  <col class="col-num" />
  <col class="col-num" />
  <col class="col-num" />
  <col class="col-act" />
</colgroup>
<thead>
  <tr>
    <th>Artikel</th>
    <th class="text-center"></th>
    <th>Produkt</th>
    <th class="text-right">Netto</th>
    <th class="text-right">Pris inkl</th>
    <th class="text-right">TG</th>
    <th class="text-center">Sålda</th>
    <th class="text-center">Hylla</th>
    <th class="text-center">Lager</th>
    <th></th>
  </tr>
</thead>
<tbody>
HTML;

// ---- render rows ----
$lastCat = null;

// summeringar
$sumValueNetto = 0.0;    // qtyonhand * netto (orundat för korrekt summa)
$sumSold       = 0;
$sumOnShelf    = 0;
$sumStock      = 0;

foreach ($res['rows'] as $r) {
    // Kategori-rubrik
    $catId   = isset($r['category_id']) ? (int)$r['category_id'] : 0;
    $catName = isset($r['category_name']) ? $h($r['category_name']) : '';
    $catVal  = isset($r['category_value']) ? $r['category_value'] : '';

    if ($catId !== $lastCat) {
        if ($catName === '') {
            $catTitle = 'Övrigt';
        } else {
            if ($catVal !== '') {
                $url     = 'https://admin.cyberphoto.se/lagerstatus.php?katID=' . rawurlencode($catVal);
                $catTitle = '<a href="'.$url.'" target="_blank" rel="noopener">'.$catName.'</a>';
            } else {
                $catTitle = $catName;
            }
        }
        echo '<tr class="cat-head"><th colspan="10">'.$catTitle.'</th></tr>';
        $lastCat = $catId;
    }

    $article = $h($r['article']);
    $produkt = $h($r['product_full']);
    $pid     = (int)$r['m_product_id'];
    $title   = $h($r['product_full']);

    // Bygg länk som öppnar drawer i samma sökning (behåll nuvarande query + drawer=pid)
    $qs = $_GET;
    $qs['drawer'] = $pid;
    $shareUrl = $_SERVER['PHP_SELF'] . '?' . http_build_query($qs);

    // Priser / netto / TG
    $priceExRound  = isset($r['price_ex_round'])  ? (int)$r['price_ex_round']  : (int)round((float)$r['price_ex']);
    $priceIncRound = isset($r['price_inc_round']) ? (int)$r['price_inc_round'] : (int)round((float)$r['price_inc']);

    $nettoRaw      = isset($r['net_price']) ? (float)$r['net_price'] : (float)$r['price_ex']; // fallback
    $nettoRound    = (int)round($nettoRaw);

    $nettoDisp  = number_format($nettoRound, 0, ',', ' ') . ' kr';
    $prisInDisp = number_format($priceIncRound, 0, ',', ' ') . ' kr';

    $rate = isset($r['tax_rate']) ? (float)$r['tax_rate'] : 0.0;
    $netMinInc = $nettoRound * (1.0 + $rate/100.0);
    $netMinIncFmt = number_format((float)round($netMinInc), 0, ',', ' ');

    $priceExBase = isset($r['price_ex']) ? (float)$r['price_ex'] : 0.0;
    $nettoRaw    = isset($r['net_price']) ? (float)$r['net_price'] : $priceExBase;

    $tbRaw = $priceExBase - $nettoRaw;
    $tgPct = ($priceExBase > 0) ? ($tbRaw / $priceExBase) * 100.0 : 0;
    $tbDisp = number_format((float)round($tbRaw), 0, ',', ' ') . ' kr';

    $priceExTitle = $h('Exkl moms: ' . number_format($priceExRound, 0, ',', ' ') . ' kr');
    $tgTitle      = $h('TB: ' . $tbDisp);

    // Säljstatistik
    $sold30    = isset($r['sold_30d']) ? (int)$r['sold_30d'] : 0;
    $soldHover = !empty($r['sold_hover']) ? $h($r['sold_hover']) : ($sold30 . ' / - / -');

    // Lager
    $qtyOnHand = isset($r['stock_qty']) ? (int)$r['stock_qty'] : 0;
    $qtyAvail  = isset($r['available_qty']) ? (int)$r['available_qty'] : 0;
    $stockBadgeClass = ($qtyAvail > 0) ? 'badge-stock ok' : 'badge-stock bad';
    $stockBadge = '<span class="'.$stockBadgeClass.'">'.$qtyAvail.'</span>';

    // Summeringar
    $nettoForSum    = $nettoRaw;
    $sumValueNetto += max(0, $qtyOnHand) * $nettoForSum;

    $sumSold    += $sold30;
    $sumOnShelf += $qtyOnHand;
    $sumStock   += $qtyAvail;

    // Badges
    $badgeHtml = CSearch::buildListBadges($r);

    // Tooltip från description (för begagnat/fyndvara)
    $descRaw = isset($r['description_text']) ? trim($r['description_text']) : '';
    $tooltip = ($descRaw !== '' && (!empty($r['is_used']) || !empty($r['is_deal'])))
             ? ' title="'.$h($descRaw).'"'
             : '';

    // Webblänk
    $web = isset($r['web_url']) ? $r['web_url'] : ('https://www.cyberphoto.se/sok?q=' . rawurlencode($article));

    echo '<tr>';

      // Artikel
      echo '<td><span class="copy-art" data-article="'.$article.'" title="Kopiera artikelnummer">'.$article.'</span></td>';

      // Webblänk-ikon
      echo '<td class="text-center"><a class="web-link" href="'.$h($web).'" target="_blank" title="Öppna på webben">'
         . '<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">'
         . '<path d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3zM5 5h5V3H3v7h2V5zm0 14h5v2H3v-7h2v5zm14-5h2v7h-7v-2h5v-5z"/>'
         . '</svg></a></td>';

      // Produkt + badges + drawer-trigger
      echo '<td><a href="'.$shareUrl.'" class="drawer-link btn-more"'
         . ' data-type="product" data-pid="'.$pid.'" data-id="'.$pid.'" data-article="'.$article.'"'.$tooltip.'>'
         . $produkt . '</a>' . $badgeHtml . '</td>';

      // Netto / Pris inkl
      echo '<td class="text-right" style="white-space:nowrap" title="Nettopris inkl moms: '.$netMinIncFmt.' kr">'.$nettoDisp.'</td>';
      echo '<td class="text-right num" title="'.$priceExTitle.'">'.$prisInDisp.'</td>';

      // TG-chip
      echo '<td class="text-right">';
      if ($priceExBase <= 0) {
          echo '<span class="tg-chip" title="'.$h('TB: 0 kr').'"></span>';
      } else {
          $tbKr   = $priceExBase - $nettoRaw;
          $tgRaw  = ($tbKr / $priceExBase) * 100.0;
          $tgDisp = round($tgRaw, 1);
          $thr    = 0.1;

          if ($tgRaw >  $thr)      $tgClass = 'pos';
          elseif ($tgRaw < -$thr)  $tgClass = 'neg';
          else                     $tgClass = 'zero';

          $tgText  = number_format($tgDisp, 1, ',', '');
          $tbTitle = $h('TB: ' . number_format((float)round($tbKr), 0, ',', ' ') . ' kr');

          echo '<span class="tg-chip '.$tgClass.'" title="'.$tbTitle.'">'.$tgText.'%</span>';
      }
      echo '</td>';

      // Sålda (30d) + tooltip 7/30/90
      // echo '<td class="text-center"><span title="'.$soldHover.'">'.$sold30.'</span></td>';
	  echo '<td class="text-center">'
		 . '<a href="https://admin.cyberphoto.se/sold_article.php?product_id='.$pid.'"'
		 . ' target="_blank" rel="noopener"'
		 . ' title="'.$soldHover.'"'
		 . ' class="sold-count-link">'
		 . $sold30
		 . '</a>'
		 . '</td>';

      // Hylla / Tillgång
      echo '<td class="text-center">'.(int)$qtyOnHand.'</td>';
      echo '<td class="text-center">'.$stockBadge.'</td>';

      // actions
      echo '<td style="white-space:nowrap">';
        echo '<a href="#" class="copy-link" data-pid="'.$pid.'" data-article="'.$article.'" title="Kopiera delningslänk" style="margin-right:6px;display:inline-block">'
           . '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
           . '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>'
           . '<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>'
           . '</svg></a>';

        echo '<a href="product_update.php?artnr='.$article.'&m_product_id='.$pid.'" class="edit-btn popup-link" title="Redigera" data-popup="product_update_'.$pid.'">'
           . '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
           . '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>'
           . '</a>';
      echo '</td>';

    echo '</tr>';
}

// ---- summeringsrad ----
$fmt = function($n){
    return number_format((int)round((float)$n), 0, ',', ' ');
};

echo '<tr class="product-summary-row">';
  echo '<td colspan="3"><strong>Summeringar:</strong></td>';              // Artikel + ikon + Produkt
  echo '<td class="text-right" style="white-space:nowrap"><strong>'.$fmt($sumValueNetto).' kr</strong></td>'; // Netto (lagervärde)
  echo '<td class="text-right"><strong></strong></td>';                   // Pris inkl
  echo '<td class="text-right"><strong></strong></td>';                   // TG (tom)
  echo '<td class="text-center"><strong>'.$sumSold.'</strong></td>';      // Sålda
  echo '<td class="text-center"><strong>'.$sumOnShelf.'</strong></td>';   // Hylla
  echo '<td class="text-center"><strong>'.$sumStock.'</strong></td>';     // Tillgång
  echo '<td></td>';                                                       // actions
echo '</tr>';

echo '</tbody></table>';

// ---- JS: kopiera ren länk + autoöppna drawer ----
?>
<script>
(function () {
  function buildShareURL(pid, article) {
    var base = window.location.origin + window.location.pathname;
    var url = new URL(base);
    url.searchParams.set('mode', 'product');
    url.searchParams.set('q', article || '');
    url.searchParams.set('open', 'product');
    url.searchParams.set('id', String(pid));
    return url.toString();
  }

  // Robust copy: Clipboard API om möjligt, annars textarea + execCommand
  function copyToClipboard(text) {
    if (text == null) text = '';
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(String(text));
    }
    return new Promise(function (resolve) {
      var ta = document.createElement('textarea');
      ta.value = String(text);
      ta.setAttribute('readonly', '');
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      try { document.execCommand('copy'); } catch (e) {}
      document.body.removeChild(ta);
      resolve();
    });
  }

  // EN gemensam click-hanterare för alla kopieringar
  document.addEventListener('click', function (e) {
    // 1) Chips i drawern (Vårt artnr / Tillv. artnr / EAN)
    var chip = e.target.closest && e.target.closest('.copy-chip');
    if (chip) {
      e.preventDefault();
      var val = chip.getAttribute('data-copy') || '';
      if (!val || val === '') return;
      copyToClipboard(val).then(function(){
        var old = chip.getAttribute('title') || '';
        chip.classList.add('copied');
        chip.setAttribute('title','Kopierat!');
        setTimeout(function(){
          chip.classList.remove('copied');
          chip.setAttribute('title', old || 'Kopiera');
        }, 900);
      });
      return;
    }

    // 2) Klick på artikelnumret i listan
    var artEl = e.target.closest && e.target.closest('.copy-art');
    if (artEl) {
      e.preventDefault();
      var art = artEl.getAttribute('data-article') || (artEl.textContent || '').trim();
      copyToClipboard(art);
      return;
    }

    // 3) Klick på kopiera delningslänk-ikonen
    var a = e.target.closest && e.target.closest('.copy-link');
    if (a) {
      e.preventDefault();
      var pid = a.getAttribute('data-pid');
      var art = a.getAttribute('data-article') || '';
      var link = buildShareURL(pid, art);
      copyToClipboard(link);
      return;
    }
  }, false);

  function triggerDrawer(pid) {
    var a = document.createElement('a');
    a.href = '#';
    a.className = 'drawer-link btn-more';
    a.setAttribute('data-type', 'product');
    a.setAttribute('data-pid', String(pid));
    a.setAttribute('data-id', String(pid));
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    setTimeout(function(){ a.remove(); }, 0);
  }

  function autoOpenFromURL() {
    try {
      var url = new URL(window.location.href);
      if (url.searchParams.get('open') !== 'product') return;
      var pid = url.searchParams.get('id');
      if (!pid) return;

      var tries = 0;
      var t = setInterval(function(){
        tries++;
        var hasTable = !!document.querySelector('.table-list');
        if (hasTable || tries > 20) {
          clearInterval(t);
          triggerDrawer(pid);
        }
      }, 100);
    } catch (e) {}
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', autoOpenFromURL);
  } else {
    autoOpenFromURL();
  }
})();
</script>

<script>
(function(){
  var f = document.getElementById('search-filters');
  if (!f) return;
  f.addEventListener('change', function(){
    // när man ändrar filter, gå alltid till sida 1
    var page = document.getElementById('pageField');
    if (page) page.value = '1';
    f.submit();
  });
})();
</script>

<script>
(function(){
  var f = document.getElementById('search-filters');
  if (!f) return;

  // När man ändrar ett filter -> hoppa till sida 1 + submit
  f.addEventListener('change', function(){
    var page = document.getElementById('pageField');
    if (page) page.value = '1';
    f.submit();
  });

  // ===== Keyboard shortcuts för filter =====
  // I = toggla "Gamla inbyten" (old_tradeins)
  document.addEventListener('keydown', function(e){
    // ignorera om man skriver i inputs osv
    var t = e.target || e.srcElement;
    if (!t) return;
    var tag = (t.tagName || '').toLowerCase();
    if (tag === 'input' || tag === 'textarea' || tag === 'select' || t.isContentEditable) {
      return;
    }

    // inga ctrl/alt/meta-kombos, bara ren bokstav
    if (e.altKey || e.ctrlKey || e.metaKey) return;

    var key = e.key || '';
    if (key.toLowerCase() === 'i') {
      var cb = f.querySelector('input[name="old_tradeins"]');
      if (!cb) return;

      e.preventDefault(); // så inget annat fångar I

      // toggla checkboxen
      cb.checked = !cb.checked;

      // trigga change så vår form-logik körs (sida=1 + submit)
      if (typeof Event === 'function') {
        var evt = new Event('change', { bubbles: true });
        cb.dispatchEvent(evt);
      } else if (document.createEvent) {
        var ev2 = document.createEvent('HTMLEvents');
        ev2.initEvent('change', true, false);
        cb.dispatchEvent(ev2);
      } else {
        // supergammalt fallback
        cb.fireEvent && cb.fireEvent('onchange');
      }
    }
  }, false);

})();
</script>

<?php
echo '</div>';

include_once "footer.php";
?>

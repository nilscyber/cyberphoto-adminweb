<?php
header('Content-Type: text/html; charset=UTF-8');
include_once 'CCheckIpNumber.php';
spl_autoload_register(function ($class) {
	include $class . '.php';
});

$h    = function($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); };
$type = isset($_GET['type']) ? strtolower($_GET['type']) : 'product';
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$debug = !empty($_GET['debug']) && $_GET['debug'] == '1';
// Skriv endast ut debug-panelen om $debug === true


if ($debug) {
    echo '<div style="background:#fff3cd;border:1px solid #ffeeba;padding:6px;margin-bottom:8px">';
    echo 'DEBUG type='.$h($type)
       .' | raw id='.$h(isset($_GET['id'])?$_GET['id']:'(saknas)')
       .' | trimmed id='.$h($id)
       .' | ctype_digit='.(ctype_digit($id)?'true':'false');
    echo '</div>';
}

if ($type === 'customer') {
	echo CSearch::renderCustomerDetailsAD($id);
	exit;
} elseif ($type === 'order') {
    // $id = ordernummer (documentno)
    echo CSearch::renderOrderDetailsAD($id);
    exit;
} elseif ($type === 'product') {

    $conn = Db::getConnectionAD(false); // Postgres-RESOURCE
    if (!$conn) { echo '<p style="color:#b00">PG-anslutning saknas.</p>'; exit; }
    if ($conn) { @pg_set_client_encoding($conn, "UTF8"); }

    $rawId = isset($_GET['id']) ? $_GET['id'] : '';
    $id    = trim($rawId);
    $debug = !empty($_GET['debug']);

    if ($debug) {
        echo '<div style="background:#FEF3C7;border:1px solid #FDE68A;padding:8px;margin-bottom:10px;">
                <div><strong>DEBUG type=product</strong> | raw id=' . $h($rawId) . ' | trimmed id=' . $h($id) . ' | ctype_digit=' . (ctype_digit($id) ? 'true' : 'false') . '</div>
              </div>';
    }

    if ($id === '' || !ctype_digit($id)) { echo '<p>Ogiltigt produkt-id.</p>'; exit; }
    $pid = (int)$id;

	// ====== Hämta huvudinfo (inkl. nya fält för begagnat) ======
	$sql = "
		SELECT
			p.m_product_id,
			p.created,
			u_created.name AS created_by_name,
			p.value AS article,
			TRIM(COALESCE(manu.name,'') || ' ' || COALESCE(p.name,'')) AS product_full,

			COALESCE(pp.pricestd, pp.pricelist, 0) AS price_ex,

			-- Lager fraan vyn (för huvudlager 1000000) - aliasa till slutliga fältnamn
			COALESCE(psv.qtyonhand,             0) AS stock_qty,
			COALESCE(psv.qty_allocated_lines,   0) AS allocated_storage_qty,
			COALESCE(psv.qtyavailable,          0) AS available_qty,
			COALESCE(psv.qtyordered,            0) AS ordered_qty,

			COALESCE(pp.pricelimit, c.currentcostprice, 0) AS net_price,

			COALESCE((
				SELECT t.rate
				  FROM c_tax t
				 WHERE t.c_taxcategory_id = p.c_taxcategory_id
				   AND t.c_country_id = 313
				 ORDER BY t.isdefault DESC NULLS LAST
				 LIMIT 1
			), (
				SELECT t.rate
				  FROM c_tax t
				 WHERE t.c_taxcategory_id = p.c_taxcategory_id
				   AND t.isdefault = 'Y'
				 ORDER BY t.c_tax_id
				 LIMIT 1
			), 0) AS tax_rate,

			p.salestart,
			p.launchdate,
			p.isselfservice,
			p.discontinued,
			p.is_spec13 AS spec_13,
			p.description AS description_text,
			p.issalesbundle,

			-- badges
			p.istradein,
			p.demo_product,
			p.isdropship,
			CASE
			  WHEN p.isdropship = 'Y' THEN (
				SELECT CASE WHEN COUNT(*) > 0
							THEN COALESCE(SUM(v.qtyonhand),0)
							ELSE NULL
					   END
				FROM xc_storage_vendor v
				WHERE v.m_product_id   = p.m_product_id
				  AND v.isactive       = 'Y'
				  AND v.isdiscontinued = 'N'
			  )
			  ELSE NULL
			END AS vendor_stock,

			-- meta
			p.manufacturerproductno,
			p.upc,

			mloc.value AS shelf_value,
			
			-- Extra fält för begagnat/fyndvara
			p.serialno,
			p.included_accessories,
			p.qtyexposures,
			p.usedcomment

		FROM m_product p
		LEFT JOIN xc_manufacturer manu
			   ON manu.xc_manufacturer_id = p.xc_manufacturer_id
		LEFT JOIN m_product_stock_summary_v psv
			   ON psv.m_product_id = p.m_product_id
			  AND psv.m_warehouse_id = 1000000
		LEFT JOIN m_productprice pp
			   ON pp.m_product_id = p.m_product_id
			  AND pp.m_pricelist_version_id = 1000000
		LEFT JOIN (
			SELECT mc2.m_product_id, mc2.currentcostprice
			  FROM m_cost mc2
			 WHERE mc2.updated = (
				   SELECT MAX(mc3.updated)
					 FROM m_cost mc3
					WHERE mc3.m_product_id = mc2.m_product_id
			 )
		) c ON c.m_product_id = p.m_product_id
		LEFT JOIN m_locator mloc
			   ON mloc.m_locator_id = p.m_locator_id
		LEFT JOIN ad_user u_created
			   ON u_created.ad_user_id = p.createdby
		WHERE p.m_product_id = $1
		LIMIT 1
	";

    $res = ($conn) ? @pg_query_params($conn, $sql, array($pid)) : false;
    $row = $res ? pg_fetch_assoc($res) : null;
    if ($res) pg_free_result($res);

	// === Behoorighetsflaggor (tidigt) ===
	$isTradeIn  = false;
	$isPriority = false;
	if (class_exists('CCheckIP')) {
		if (method_exists('CCheckIP','checkIfLoginIsTradeIn'))  { try { $isTradeIn  = (bool) CCheckIP::checkIfLoginIsTradeIn(); }  catch (Exception $e) {} }
		if (method_exists('CCheckIP','checkIfLoginIsPriority')) { try { $isPriority = (bool) CCheckIP::checkIfLoginIsPriority(); } catch (Exception $e) {} }
	}

	if ($row) {
		// Begagnad?
		$isUsedProd = (isset($row['istradein']) && $row['istradein'] === 'Y');

		// Raa datumsträngar
		$ldRaw = isset($row['launchdate']) ? trim($row['launchdate']) : '';
		$sdRaw = isset($row['salestart'])  ? trim($row['salestart'])  : '';

		// --- Datum/tids-spärr för ej-priority ---
		if (!$isPriority) {
			$tz  = new DateTimeZone('Europe/Stockholm');
			$now = new DateTime('now', $tz);   // exakt nu, inkl tid

			// Hjälpfunktion: TRUE om s ligger i framtiden (jämfört med nu)
			$isFuture = function($s) use ($tz, $now) {
				if ($s === '' || $s === null) return false;
				try {
					$d = new DateTime($s, $tz);
				} catch (Exception $e) {
					return false;
				}
				return $d > $now;
			};

			// BUGGFIX v31: Spärr baseras ENBART paa launchdate.
			$blockedForDate = ($ldRaw !== '') ? $isFuture($ldRaw) : false;

			// Undantag: trade-in-användare + begagnad produkt
			$allowedByTradeIn = ($isTradeIn && $isUsedProd);

			if ($blockedForDate && !$allowedByTradeIn) {
				if (!empty($_GET['debug'])) {
					echo '<div style="background:#FEF3C7;border:1px solid #FDE68A;'.
						 'padding:6px;margin:8px 0;color:#7C2D12;font-size:12px">';
					echo 'Ej lanserad ännu<br>';
					echo 'now='        . htmlspecialchars($now->format('Y-m-d H:i:s'), ENT_QUOTES, 'UTF-8') . '<br>';
					echo 'salestart='  . htmlspecialchars($sdRaw, ENT_QUOTES, 'UTF-8') . '<br>';
					echo 'launchdate=' . htmlspecialchars($ldRaw, ENT_QUOTES, 'UTF-8') . '<br>';
					echo 'isTradeIn='  . ($isTradeIn ? '1' : '0') . ', isUsedProd=' . ($isUsedProd ? '1' : '0');
					echo '</div>';
				}

				echo '<div style="padding:10px;border:1px solid #FDE68A;background:#FEF3C7;'.
					 'border-radius:8px;color:#7C2D12;font-size:13px">';
				echo 'Produkten är inte lanserad ännu.';
				echo '</div>';
				exit;
			}
		}
	}

    if (!$row) {
        if ($debug) {
            echo '<div style="color:#b00">DEBUG: heavy SELECT gav 0 rader. '.htmlspecialchars(pg_last_error($conn)).'</div>';
        }
        echo '<p>Produkten hittades inte.</p>';
        exit;
    }

	// $row kommer fraan din SELECT ovan
	$isDropShip = false;
	if (isset($row['isdropship'])) {
		$v = strtoupper(trim((string)$row['isdropship']));
		$isDropShip = ($v === 'Y' || $v === '1' || $v === 'T' || $v === 'TRUE');
	}

	// Plocka ut vendor_stock fraan raden
	$vendorStock = null;
	if (array_key_exists('vendor_stock', $row) && $row['vendor_stock'] !== null && $row['vendor_stock'] !== '') {
		$vendorStock = (int)$row['vendor_stock'];
	}

    // ====== Smaa hjälpare ======
    $fmt = function($n){ return number_format((float)$n, 2, ',', ' '); };

    // ====== Beräkningar ======
    $priceEx = (float)$row['price_ex'];
    $rate    = (float)$row['tax_rate'];
    $priceIn = $priceEx * (1.0 + $rate/100.0);
    $net     = (float)$row['net_price'];
    $tb      = $priceEx - $net;
    $tg      = ($priceEx > 0) ? ($tb / $priceEx) * 100.0 : 0.0;

    $netMinInc     = $net * (1.0 + $rate/100.0);
    $netMinIncFmt  = number_format((float)round($netMinInc), 0, ',', ' ');

    $shelf = (string)$row['shelf_value'];
    if ($shelf === '') $shelf = 'Standard';

    // Min-/maxlager fraan m_replenish
    $minLevel = null; 
    $maxLevel = null;
    $sqlLvl = "SELECT level_min, level_max FROM m_replenish WHERE m_product_id = $1 LIMIT 1";
    if ($resLvl = ($conn) ? @pg_query_params($conn, $sqlLvl, array($pid)) : false) {
        if ($resLvl && $rowLvl = pg_fetch_assoc($resLvl)) {
            $minLevel = isset($rowLvl['level_min']) ? (int)$rowLvl['level_min'] : null;
            $maxLevel = isset($rowLvl['level_max']) ? (int)$rowLvl['level_max'] : null;
        }
        pg_free_result($resLvl);
    }

    // Försäljningsstatistik - hämta EN gaang
    $st = null;
    $sqlStat = "
        SELECT qtyweek, qtymonth, qty3month, qty6month, qty12month, qty36month, qtytotal
          FROM xc_product_statistics
         WHERE m_product_id = $1 AND c_country_id = 313
         ORDER BY updated DESC
         LIMIT 1
    ";
    if ($resS = ($conn) ? @pg_query_params($conn, $sqlStat, array($row['m_product_id'])) : false) {
        $st = $resS ? pg_fetch_assoc($resS) : null;
        pg_free_result($resS);
    }

    // ====== Render start ======
	echo '<style>
	  .dw-wrap{font-size:14px;max-height:calc(90vh - 28px);overflow-y:auto}
	  .dw-h2{margin:0 0 10px 0;font-size:18px;font-weight:700}
	  .dw-label{font-weight:400;color:#374151}
	  .dw-val{font-weight:700}
	  .dw-val.good{color:#166534}
	  .dw-val.bad{color:#b91c1c}
	  .dw-muted{color:#6b7280}
	  .nowrap{white-space:nowrap}

	  .dw-section{margin:10px 0 0 0}

	  .dw-table{width:100%;border-collapse:collapse;font-size:13px}
	  .dw-table th,.dw-table td{padding:4px 6px;border-bottom:1px solid #ddd;text-align:left}
	  .dw-country{display:inline-block;padding:1px 5px;border-radius:4px;font-size:11px;font-weight:700;background:#e5e7eb;color:#374151}
	  .text-center{text-align:center}

	  .dw-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
	  .dw-btn{display:inline-block;padding:6px 10px;border:1px solid #cfd6e0;border-radius:6px;background:#fff;text-decoration:none;color:#111}
	  .dw-btn:hover{background:#f2f6ff;border-color:#b7c4da}

	  .dw-meta{margin:0 0 10px 0;color:#374151;font-size:13px}
	  .dw-meta strong{font-weight:700}

	  .dw-card{border:1px solid #e5e7eb;background:#fff;border-radius:10px;padding:10px 12px;margin:10px 0 0 0;}
	  .dw-card h3{margin:0 0 8px 0;font-size:14px;font-weight:700;color:#111}
	  .dw-card-title{font-weight:700;margin:0 0 6px 0;}
	  .dw-card .row{display:grid;grid-template-columns:1fr 1fr;grid-gap:6px 16px}
	  .dw-card .row > div{white-space:nowrap}

	  .dw-url{color:#1d4ed8;text-decoration:underline;font-weight:600;font-size:14px}

	  .dw-charts{grid-column:1 / -1; margin:8px 0 12px 0;}
	  .dw-charts svg{width:100%; height:auto; display:block;}
	  .dw-spark-wrap{margin:0;}
	  .dw-spark-title{display:flex; align-items:baseline; gap:8px; margin-bottom:4px;}
	  .dw-spark-title strong{font-size:13px;}
	  .dw-spark-meta{color:#6b7280; font-size:12px;}

	  .dw-card-vendor .row{display:block}
	  .dw-card-vendor .row > div{margin:2px 0}

	  .dw-title-link{font-size:14px;font-weight:800;color:blue}
	  .dw-title-link:hover{color:#1d4ed8;text-decoration:underline}

	  .dw-pill{display:inline-block;padding:2px 8px;border-radius:999px;font-weight:700;font-size:12px;border:1px solid transparent}
	  .dw-pill-ok{background:#ecfdf5;border-color:#a7f3d0;color:#065f46}
	  .dw-pill-none{background:#f3f4f6;border-color:#e5e7eb;color:#6b7280}

	  .dw-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border:1px solid #cfd6e0;border-radius:6px;margin-left:6px;color:#374151;background:#fff;text-decoration:none}
	  .dw-icon-btn:hover{background:#f2f6ff;border-color:#b7c4da}
	  .dw-title-actions{display:inline-flex;vertical-align:middle;margin-left:6px}

	  .dw-inline-link{color:inherit;text-decoration:none;font-size:inherit;line-height:inherit;font-weight:inherit}
	  .dw-inline-link.dw-val{font-weight:700;}
	  .dw-inline-link .u{text-decoration:underline;}
	  .dw-inline-link:hover{color:inherit;}

	  .dw-cond{margin:6px 0 10px 0;padding:8px 10px;border:1px solid #e5e7eb;background:#f9fafb;border-radius:8px;color:#111;font-size:14px;}
	  .dw-cond .dw-cond-label{font-weight:700;color:#374151;margin-right:6px}

	  .dw-span-2{ grid-column: 1 / -1; }

	  .dw-supp a{color:#0b57d0;text-decoration:underline;font-size:inherit;line-height:1.4}

	  .dw-card.dw-supp{text-align:left;}
	  .dw-card.dw-supp .row{display:block;margin:2px 0;}
	  .dw-card.dw-supp a{color:#0b57d0;text-decoration:underline;font-size:inherit;line-height:1.4;}
	  .dw-card.dw-supp .kv{display:grid;grid-template-columns:max-content 1fr;column-gap:8px;row-gap:4px;}
	  .dw-card.dw-supp .kv-key{color:#374151;font-weight:400;white-space:nowrap;}
	  .dw-card.dw-supp .kv-val{font-weight:700;overflow-wrap:anywhere;}
	  .dw-card.dw-supp .copy-chip{ margin-left:0; }

	  .kv-row .kv-key{ min-width:92px; }
	  .kv-row .kv-val{ font-size:12px; line-height:1.25; }

	  /* Fjärrlager-badge */
	  .dw-row-fjarrlager .badge-drop{
		padding:2px 10px;
		font-size:12px;
	  }

	  .dw-supp .lbl{font-weight:400;color:#374151;margin-right:6px}
	  .dw-supp .dw-val{font-weight:700}

	  .copy-chip{ cursor:pointer; padding:0 4px; border-radius:6px; }
	  .copy-chip.copied{ outline:2px solid #a7f3d0; outline-offset:2px; }
	  .dw-supp .copy-chip.dw-val { margin-left:4px; }

	  /* === Lagerkort - gridlayout & pill-värden === */
	  .dw-stock-grid{ display:grid; grid-template-columns:1fr 1fr; gap:14px 18px; }
	  .dw-stock-col{ display:grid; row-gap:8px; }
	  .dw-row{ display:grid; grid-template-columns:110px 1fr; align-items:center; }
	  .dw-row .dw-label{ color:#555; font-weight:400; }
	  .dw-row .dw-val{ font-weight:700; }

	  .dw-badge-pill{
		display:inline-block; padding:0 10px; line-height:22px;
		border-radius:999px; border:1px solid transparent; font-weight:700; font-size:12px;
	  }
	  .dw-badge-green{  background:#ecfdf5; border-color:#a7f3d0; color:#065f46; }
	  .dw-badge-red{    background:#fee2e2; border-color:#fecaca; color:#991b1b; }
	  .dw-badge-amber{  background:#fef3c7; border-color:#fde68a; color:#92400e; }
	  .dw-badge-indigo{ background:#eef2ff; border-color:#c7d2fe; color:#3730a3; }
	  .dw-badge-blue{   background:#2563eb; border-color:#1e40af; color:#fff; }
	  .dw-badge-grey{   background:#757575; border-color:#434343; color:#fff; }
	  .dw-badge-fuchsia{ background:#fä8ff; border-color:#f5d0fe; color:#86198f; }

	  /* osynlig rad som tar plats för att jämna ut höjden */
	  .dw-row-spacer .dw-label,
	  .dw-row-spacer .dw-val{ visibility:hidden; height:22px; }

	  /* Länkarna (waitinglist/purchaselist) ska inte ändra färg paa badge */
	  .dw-vallink{ color:inherit; text-decoration:none; }
	  .dw-vallink .u{ text-decoration:underline; }

	  /* fullbreddsrad längst ner, label + värde direkt efter varandra */
	  .dw-stock-grid .dw-row-full {
		grid-column: 1 / -1;
		display: flex;
		align-items: center;
		gap: 8px;
	  }
	  .dw-stock-grid .dw-nowrap { white-space: nowrap; }
	  .dw-stock-grid .dw-row-full .dw-label { margin: 0; }
	  .dw-stock-grid .dw-row-full .dw-val   { white-space: nowrap; }

	  .dw-card-stock { margin-bottom: 0; }
	  #dw-queue-block, #dw-purch-block { background:#f0f9ff; border-color:#bae6fd; margin-bottom: 16px; }

	  .dw-card-meta{
		font-size:13px;
		margin-top:10px;
	  }
	  .dw-card-meta .badge-age{
		margin-left:4px;
	  }

	  /* === v32: Prisjakt-logotyp i priskortets rubrikrad === */
	  .dw-card-priser{ position:relative; }
	  .dw-prisjakt-link{
		position:absolute; top:7px; right:14px;
		display:inline-flex; align-items:center;
		padding:2px 7px;
		background:#e8173c;
		border-radius:4px;
		opacity:0.85; transition:opacity .15s;
	  }
	  .dw-prisjakt-link:hover{ opacity:1; }
	  .dw-prisjakt-link img{ height:14px; width:auto; display:block; }
	</style>';

    $artRaw = !empty($row['article']) ? $row['article'] : (!empty($row['value']) ? $row['value'] : '');
    $art_h  = $h($artRaw);
    $title  = $h($row['product_full']);
    $shopUrl = 'https://www.cyberphoto.se/sok?q=' . rawurlencode($row['article']);

    echo '<div class="dw-wrap">';
    echo '<h2 class="dw-h2"><a href="'.$shopUrl.'" target="_blank" rel="noopener" class="dw-title-link">'.$title.'</a>'
       . '<span class="dw-title-actions">'
       .   '<a href="#" class="dw-icon-btn copy-link" data-pid="'.$pid.'" data-article="'.$art_h.'" title="Kopiera delningslänk">'
       .     '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
       .       '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>'
       .       '<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>'
       .     '</svg>'
       .   '</a>'
       . '</span>'
       . '</h2>';

	// === Bygg flaggar saa de matchar buildern ===
	$_flagTz  = new DateTimeZone('Europe/Stockholm');
	$_flagNow = new DateTime('now', $_flagTz);

	$flags = array(
		'is_used'         => (!empty($row['istradein'])    && $row['istradein']    === 'Y') ? 1 : 0,
		'is_deal'         => (!empty($row['demo_product']) && $row['demo_product'] === 'Y') ? 1 : 0,
		'is_dropship'     => isset($row['isdropship']) ? $row['isdropship'] : '',
		'is_unpublished'  => (isset($row['isselfservice']) && $row['isselfservice'] === 'N'
							  && (isset($row['available_qty']) ? (float)$row['available_qty'] : 0) > 0) ? 1 : 0,
		'is_launch_future'=> (!empty($row['launchdate']) && (new DateTime($row['launchdate'], $_flagTz)) > $_flagNow) ? 1 : 0,
		'salestart'       => (!empty($row['salestart']) && (new DateTime($row['salestart'], $_flagTz)) > $_flagNow)
								? $row['salestart']
								: '',
	);

	// === Render: samma badges som i listan ===
	echo '<div class="badges-wrap">'.CSearch::buildListBadges(array_merge($row, $flags)).'</div>';

	// === Skick / kommentar (visas endast för begagnat/fyndvara) ===
	$desc = '';
	if (!empty($row['description_text']))      $desc = (string)$row['description_text'];
	elseif (!empty($row['description']))       $desc = (string)$row['description'];
	elseif (!empty($row['p_description']))     $desc = (string)$row['p_description'];

	$desc = trim($desc);

	if ($desc !== '' && (!empty($flags['is_used']) || !empty($flags['is_deal']))) {
		echo '<div class="dw-cond"><span class="dw-cond-label">Skick / kommentar:</span>'
		   . nl2br($h($desc))
		   . '</div>';
	}

	// Extra fält (bara om de har värde)
	$extraFields = array();

	if (!empty($row['serialno'])) {
		$extraFields[] = '<span class="dw-cond-label">Serienummer:</span> ' . $h($row['serialno']);
	}
	if (!empty($row['included_accessories'])) {
		$extraFields[] = '<span class="dw-cond-label">Medföljande tillbehör:</span> ' . nl2br($h($row['included_accessories']));
	}
	if (!empty($row['qtyexposures']) && (int)$row['qtyexposures'] > 0) {
		$extraFields[] = '<span class="dw-cond-label">Antal exponeringar:</span> ' . $h($row['qtyexposures']);
	}
	if (!empty($row['usedcomment'])) {
		$extraFields[] = '<span class="dw-cond-label">Begagnat notering:</span> ' . nl2br($h($row['usedcomment']));
	}

	if (!empty($extraFields)) {
		echo '<div class="dw-cond">' . implode('<br>', $extraFields) . '</div>';
	}

    // Nummerserie (kopierbara chips)
    $ourArt = $artRaw;
    $mfgNo  = !empty($row['manufacturerproductno']) ? trim($row['manufacturerproductno']) : '';
    $ean    = !empty($row['upc']) ? trim($row['upc']) : '';
    ?>
    <div class="kv-row">
      <div class="kv-val">
        <span class="copy-chip" data-copy="<?php echo $h($ourArt); ?>" title="Kopiera vårt artikelnummer">
          Vårt: <strong><?php echo $h($ourArt) !== '' ? $h($ourArt) : '-'; ?></strong>
        </span>
        &nbsp;&ndash;&nbsp;
        <span class="copy-chip" data-copy="<?php echo $h($mfgNo); ?>" title="Kopiera tillv. artnr">
          Tillv: <strong><?php echo $h($mfgNo) !== '' ? $h($mfgNo) : '-'; ?></strong>
        </span>
        &nbsp;&ndash;&nbsp;
        <span class="copy-chip" data-copy="<?php echo $h($ean); ?>" title="Kopiera EAN">
          EAN: <strong><?php echo $h($ean) !== '' ? $h($ean) : '-'; ?></strong>
        </span>
      </div>
    </div>
    <?php

    // Datumrader (endast framtid/idag)
    $lines = array();
    $tz = new DateTimeZone('Europe/Stockholm');
    $today = new DateTime('now', $tz); $today->setTime(0,0,0);
    if (!empty($row['launchdate'])) {
        $ld = new DateTime($row['launchdate'], $tz); $ld->setTime(0,0,0);
        $d  = (int)$today->diff($ld)->format('%r%a');
        if ($d >= 0) { $suffix = $d === 0 ? '(idag)' : '(om '.$d.' '.($d===1?'dag':'dagar').')';
            $lines[] = 'Lansering: <strong class="dw-val">'.$ld->format('Y-m-d').'</strong> <span class="dw-muted">'.$suffix.'</span>'; }
    }
    if (!empty($row['salestart'])) {
        $sd = new DateTime($row['salestart'], $tz); $sd->setTime(0,0,0);
        $d  = (int)$today->diff($sd)->format('%r%a');
        if ($d >= 0) { $suffix = $d === 0 ? '(idag)' : '(om '.$d.' '.($d===1?'dag':'dagar').')';
            $lines[] = 'Säljstart: <strong class="dw-val">'.$sd->format('Y-m-d').'</strong> <span class="dw-muted">'.$suffix.'</span>'; }
    }
    if ($lines) echo '<div style="margin:0 0 10px 0">'.implode(' &ndash; ', $lines).'</div>';

    // ===== Kort: PRISER =====
    $tbClass = ($tb < 0) ? 'dw-val bad' : 'dw-val good';
    $tgClass = ($tg < 0) ? 'dw-val bad' : 'dw-val good';

    // === v32: Prisjakt-länk (visas endast om EAN finns) ===
    $prisjakHtml = '';
    if ($ean !== '') {
        $prisjakUrl  = 'https://www.prisjakt.nu/search?query=' . rawurlencode($ean);
        $prisjakHtml = '<a href="' . $prisjakUrl . '" target="_blank" rel="noopener" class="dw-prisjakt-link"'
                     . ' title="Sök paa Prisjakt: ' . $h($ean) . '">'
                     . '<img src="https://cdn.pji.nu/g/rfe/logos/logo_se_v2_light.svg" alt="Prisjakt">'
                     . '</a>';
    }

    echo '<div class="dw-card dw-card-priser">';
      echo '<h3>Priser</h3>' . $prisjakHtml;
      echo '<div class="row">';
        echo '<div><span class="dw-label">Pris exkl: </span><strong class="dw-val nowrap">'.$fmt($priceEx).' kr</strong></div>';
        echo '<div><span class="dw-label">Pris inkl: </span><strong class="dw-val nowrap">'.$fmt($priceIn).' kr</strong></div>';
        echo '<div><span class="dw-label">Nettopris: </span><strong class="dw-val nowrap" title="Nettopris inkl moms: '.$netMinIncFmt.' kr">'.$fmt($net).' kr</strong></div>';
        echo '<div><span class="dw-label">Moms: </span><strong class="dw-val">'.number_format($rate, 1, ',', ' ').'%</strong></div>';
        echo '<div><span class="dw-label">TG: </span><strong class="'.$tgClass.'">'.number_format($tg, 1, ',', ' ').'%</strong></div>';
        echo '<div><span class="dw-label">TB: </span><strong class="'.$tbClass.' nowrap">'.$fmt($tb).' kr</strong></div>';
      echo '</div>';
    echo '</div>';

	/* ===== Kort: LAGER (Kö visas alltid) ===== */
	$availableRaw = isset($row['available_qty']) ? (int)$row['available_qty'] : 0;
	$allocated    = isset($row['allocated_storage_qty']) ? (int)$row['allocated_storage_qty'] : 0;
	$ordered      = isset($row['ordered_qty']) ? (int)$row['ordered_qty'] : 0;

	$availDisplay = max(0, $availableRaw);
	$queue        = ($availableRaw < 0) ? abs($availableRaw) : 0;
	$hasQueueLink = ($queue > 0);

	$artWait  = $h($row['article']);
	$artValue = isset($row['article']) ? $row['article'] : '';
	$waitUrl  = 'https://admin.cyberphoto.se/waitinglist.php?artnr='.$artWait;
	$purchUrl = 'https://admin.cyberphoto.se/purchaselist.php?artnr='.$artWait;

	// === Kö-rader (inline i drawern) ===
	$queueRows = array();
	$sqlQueue = "
		SELECT col.created, o.documentno, bp.name, col.qtyordered, col.qtyallocated,
		       col.description, xc.name AS status_name, us.name AS locked_user,
		       loc.c_country_id, o.priorityrule
		FROM c_orderline col
		JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id
		JOIN c_order o ON col.c_order_id = o.c_order_id
		JOIN m_product p ON col.m_product_id = p.m_product_id
		JOIN c_bpartner_location bpl ON bpl.c_bpartner_location_id = o.c_bpartner_location_id
		JOIN c_location loc ON loc.c_location_id = bpl.c_location_id
		JOIN c_country con ON con.c_country_id = loc.c_country_id
		LEFT JOIN xc_sales_order_status xc ON xc.xc_sales_order_status_id = o.xc_sales_order_status_id
		LEFT JOIN AD_User us ON us.AD_User_ID = o.locked_to_id
		WHERE o.c_doctype_id = 1000030
		  AND o.docstatus NOT IN ('VO')
		  AND col.qtyordered > col.qtydelivered
		  AND p.value = \$1
		  AND c_doctypetarget_id NOT IN (1000027)
		ORDER BY o.priorityrule ASC, col.created ASC
	";
	if ($rsQ = ($conn) ? @pg_query_params($conn, $sqlQueue, array($artValue)) : false) {
		while ($rsQ && $qr = pg_fetch_assoc($rsQ)) $queueRows[] = $qr;
		pg_free_result($rsQ);
	}

	// === Inköpsorder-rader (inline i drawern) ===
	$purchRows = array();
	$sqlPurch = "
		SELECT o.created, o.documentno, bp.name, col.qtyordered, col.qtydelivered,
		       col.datepromisedprecision, col.datepromised, col.description
		FROM c_orderline col
		JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id
		JOIN c_order o ON col.c_order_id = o.c_order_id
		JOIN m_product p ON col.m_product_id = p.m_product_id
		WHERE o.c_doctype_id = 1000016
		  AND o.docstatus NOT IN ('VO')
		  AND col.qtyordered > col.qtydelivered
		  AND p.value = \$1
		ORDER BY o.created ASC
	";
	if ($rsP = ($conn) ? @pg_query_params($conn, $sqlPurch, array($artValue)) : false) {
		while ($rsP && $pr = pg_fetch_assoc($rsP)) $purchRows[] = $pr;
		pg_free_result($rsP);
	}

	$shelfTxt = (isset($shelf) && $shelf!=='') ? $shelf : 'Standard';

	/* Badges */
	$availBadge   = '<span class="dw-badge-pill '.($availDisplay>0?'dw-badge-green':'dw-badge-red').'">'.$availDisplay.' st</span>';
	$allocBadge   = '<span class="dw-badge-pill dw-badge-indigo">'.$allocated.' st</span>';
	$queueBadge   = '<span class="dw-badge-pill dw-badge-fuchsia">'.$queue.' st</span>';
	$orderedBadge = '<span class="dw-badge-pill dw-badge-amber">'.$ordered.' st</span>';
	$shelfBadge   = '<span class="dw-badge-pill dw-badge-grey">'.$h($shelfTxt).'</span>';


	echo '<div class="dw-card dw-card-stock">';
	  echo '<h3>Lager</h3>';
	  echo '<div class="dw-stock-grid">';

		/* ===== VAENSTER KOLUMN ===== */
		echo '<div class="dw-stock-col">';

		  echo '<div class="dw-row"><span class="dw-label">I lager:</span><span class="dw-val">'.$availBadge.'</span></div>';

		  echo '<div class="dw-row"><span class="dw-label">Allokerade:</span><span class="dw-val">';
		  if ($allocated > 0) {
			echo '<a href="#" class="dw-vallink" onclick="dwToggleBlock(\'dw-queue-block\');return false;">'.$allocBadge.'</a>';
		  } else {
			echo $allocBadge;
		  }
		  echo '</span></div>';

		  echo '<div class="dw-row"><span class="dw-label">Kö:</span><span class="dw-val">';
		  if ($hasQueueLink) {
			echo '<a href="#" class="dw-vallink" onclick="dwToggleBlock(\'dw-queue-block\');return false;">'.$queueBadge.'</a>';
		  } else {
			echo $queueBadge;
		  }
		  echo '</span></div>';

		  echo '<div class="dw-row"><span class="dw-label">Beställda:</span><span class="dw-val">';
		  if ($ordered > 0) {
			echo '<a href="#" class="dw-vallink" onclick="dwToggleBlock(\'dw-purch-block\');return false;">'.$orderedBadge.'</a>';
		  } else {
			echo $orderedBadge;
		  }
		  echo '</span></div>';

		echo '</div>'; // vänster

		// Höger kolumn
		echo '<div class="dw-stock-col">';
		echo '<div class="dw-row"><span class="dw-label">Hyllplats:</span><span class="dw-val">'.$shelfBadge.'</span></div>';
		echo '<div class="dw-row"><span class="dw-label">Min:</span><span class="dw-val">'.($minLevel !== null ? (int)$minLevel : '-').'</span></div>';
		echo '<div class="dw-row"><span class="dw-label">Max:</span><span class="dw-val">'.($maxLevel !== null ? (int)$maxLevel : '-').'</span></div>';

		if ($isDropShip && $vendorStock !== null) {
			echo '<div class="dw-row dw-row-fjarrlager">'
			   .   '<span class="dw-label">Fjärrlager:</span>'
			   .   '<span class="dw-val"><span class="badge badge-drop">'.$vendorStock.' st</span></span>'
			   . '</div>';
		} else {
			echo '<div class="dw-row dw-row-spacer">'
			   .   '<span class="dw-label">&nbsp;</span>'
			   .   '<span class="dw-val">&nbsp;</span>'
			   . '</div>';
		}

		echo '</div>'; // dw-stock-col

		// === Fullbreddsrad längst ner i boxen ===
		$next = '';
		if ((int)$availDisplay === 0 && (int)$ordered > 0) {
			$next = CWebAdempiere::getNextDeliveryLabelForBox($artValue, 1000000, 1);
		}
		if ($next !== '') {
			echo '<div class="dw-row dw-row-full">'
			   . '<span class="dw-label">Nästa leverans:</span>'
			   . '<span class="dw-val">'.htmlspecialchars($next).'</span>'
			   . '</div>';
		}

	  echo '</div>'; // grid
	echo '</div>'; // dw-card

	// ===== Inline: Kö =====
	if (!empty($queueRows)) {
		$countryLabel = function($cid) {
			$cid = (int)$cid;
			if ($cid === 181 || $cid === 50000) return '<span class="dw-country">FI</span>';
			if ($cid === 167) return '<span class="dw-country">DK</span>';
			if ($cid === 269) return '<span class="dw-country">NO</span>';
			return '<span class="dw-country">SE</span>';
		};

		echo '<div id="dw-queue-block" class="dw-card" style="display:none">';
		  echo '<div class="dw-card-title">Kö <span class="dw-muted">('.count($queueRows).' st)</span></div>';
		  echo '<table class="dw-table" style="font-size:12px">';
		    echo '<thead><tr>'
		       . '<th>#</th><th>Skapad</th><th>Order</th><th></th><th>Namn</th>'
		       . '<th class="text-center">Best</th><th class="text-center">Allok</th><th>Status</th>'
		       . '</tr></thead><tbody>';
		  $i = 1;
		  foreach ($queueRows as $qr) {
			$created  = $h(substr((string)$qr['created'], 0, 10));
			$docno    = $h($qr['documentno']);
			$name     = $h($qr['name']);
			$desc     = $qr['description'] !== '' ? ' <b>('.$h($qr['description']).')</b>' : '';
			$best     = (int)$qr['qtyordered'];
			$allok    = (int)$qr['qtyallocated'];
			$prio     = (int)$qr['priorityrule'];
			$locked   = trim((string)$qr['locked_user']);
			$status   = trim((string)$qr['status_name']);
			$country  = $countryLabel($qr['c_country_id']);

			$statusTxt = '';
			if ($best === $allok) $statusTxt = '<i>Väntar på att skickas</i>';
			elseif ($locked !== '') $statusTxt = '<span title="'.$h($status).'">&#128274; '.$h($locked).'</span>';

			$prioTxt = '';
			if ($prio === 1) $prioTxt = '<span title="Högsta prio" style="color:#b91c1c;font-weight:700">&#9650;&#9650;</span>';
			elseif ($prio === 3) $prioTxt = '<span title="Hög prio" style="color:#d97706;font-weight:700">&#9650;</span>';

			$orderLink = '<a href="/search_dispatch.php?mode=order&page=1&q='.$docno.'" target="_blank" rel="noopener">'.$docno.'</a>';

			echo '<tr>';
			echo '<td>'.$i.'</td>';
			echo '<td style="white-space:nowrap">'.$created.'</td>';
			echo '<td style="white-space:nowrap">'.$orderLink.'</td>';
			echo '<td>'.$country.'</td>';
			echo '<td>'.$name.$desc.'</td>';
			echo '<td class="text-center">'.$best.'</td>';
			echo '<td class="text-center">'.$allok.'</td>';
			echo '<td style="white-space:nowrap">'.$prioTxt.' '.$statusTxt.'</td>';
			echo '</tr>';
			$i++;
		  }
		  echo '</tbody></table>';
		echo '</div>';
	}

	// ===== Inline: Inköpsordrar =====
	if (!empty($purchRows)) {
		$fmtDelivDate = function($dat, $prec) {
			$dat = trim((string)$dat);
			if ($dat === '') return 'Okänt datum';
			$d  = substr($dat, 0, 10);
			$ts = strtotime($d);
			$months = array('','jan','feb','mar','apr','maj','jun','jul','aug','sep','okt','nov','dec');
			if ($prec === 'D') return date('Y-m-d', $ts);
			if ($prec === 'W') return 'Vecka '.date('W', $ts);
			if ($prec === 'U') return 'Leveransdatum okänt';
			if ($prec === 'M') return $months[(int)date('n', $ts)].' '.date('Y', $ts);
			if ($prec === 'P') {
				$day = (int)date('j', $ts); $m = $months[(int)date('n', $ts)];
				if ($day <= 10) return 'Tidigt i '.$m;
				if ($day <= 20) return 'Mitten av '.$m;
				return 'Sent i '.$m;
			}
			return $d;
		};

		echo '<div id="dw-purch-block" class="dw-card" style="display:none">';
		  echo '<div class="dw-card-title">Inköpsordrar <span class="dw-muted">('.count($purchRows).' st)</span></div>';
		  echo '<table class="dw-table" style="font-size:12px">';
		    echo '<thead><tr>'
		       . '<th>#</th><th>Skapad</th><th>Order</th><th>Namn</th>'
		       . '<th class="text-center">Antal</th><th class="text-center">Lev</th>'
		       . '<th>Datum</th><th>Notering</th>'
		       . '</tr></thead><tbody>';
		  $i = 1;
		  foreach ($purchRows as $pr) {
			$created  = $h(substr((string)$pr['created'], 0, 10));
			$docno    = $h($pr['documentno']);
			$name     = $h($pr['name']);
			$antal    = (int)$pr['qtyordered'];
			$lev      = (int)$pr['qtydelivered'];
			$datum    = $h($fmtDelivDate($pr['datepromised'], $pr['datepromisedprecision']));
			$note     = $h(trim((string)$pr['description']));

			echo '<tr>';
			echo '<td>'.$i.'</td>';
			echo '<td style="white-space:nowrap">'.$created.'</td>';
			echo '<td><a href="/search_dispatch.php?mode=order&page=1&q='.$docno.'" target="_blank" rel="noopener">'.$docno.'</a></td>';
			echo '<td>'.$name.'</td>';
			echo '<td class="text-center">'.$antal.'</td>';
			echo '<td class="text-center">'.$lev.'</td>';
			echo '<td style="white-space:nowrap">'.$datum.'</td>';
			echo '<td>'.($note !== '' ? $note : '').'</td>';
			echo '</tr>';
			$i++;
		  }
		  echo '</tbody></table>';
		echo '</div>';
	}


    // ===== Sparkline + Sålt över tid =====
    echo '<div style="height:12px"></div>';
    if ($st) {
        $qtyweek    = (int)$st['qtyweek'];
        $qtymonth   = (int)$st['qtymonth'];
        $qty3month  = (int)$st['qty3month'];
        $qty6month  = (int)$st['qty6month'];
        $qty12month = (int)$st['qty12month'];
        $qty36month = (int)$st['qty36month'];
        $qtytotal   = (int)$st['qtytotal'];

        $hasTrend = ($qtyweek + $qtymonth + $qty3month + $qty6month + $qty12month + $qty36month) > 0;

        if ($hasTrend) {
            $perWeek = array(
              array('label'=>'1 v',    'v'=> $qtyweek / 1.0),
              array('label'=>'1 man',  'v'=> ($qtymonth   * 7.0) / 30.0),
              array('label'=>'3 man',  'v'=> ($qty3month  * 7.0) / 90.0),
              array('label'=>'6 man',  'v'=> ($qty6month  * 7.0) / 180.0),
              array('label'=>'12 man', 'v'=> ($qty12month * 7.0) / 365.0),
              array('label'=>'36 man', 'v'=> ($qty36month * 7.0) / 1095.0),
            );
            $values = array($perWeek[0]['v'],$perWeek[1]['v'],$perWeek[2]['v'],$perWeek[3]['v'],$perWeek[4]['v'],$perWeek[5]['v']);
            $max = 0.0; foreach ($values as $vv) { if ($vv > $max) $max = $vv; }
            if ($max < 1) $max = 1;

            $W = 560; $H = 150; $pad = 8; $innerW = $W - 2*$pad; $innerH = $H - 2*$pad;
            $n = count($perWeek); $dx = ($n>1) ? $innerW/($n-1) : 0;

            $coords = array();
            for ($i=0; $i<$n; $i++){
                $x = $pad + $i*$dx;
                $v = $perWeek[$i]['v'];
                $norm = $v / $max; if ($norm<0) $norm=0; if ($norm>1) $norm=1;
                $y = $pad + (1.0 - $norm) * $innerH;
                $coords[] = array($x,$y);
            }
            $path = '';
            for ($k=0; $k<count($coords); $k++){
                $c = $coords[$k];
                $path .= (($k==0)?'M':'L') . round($c[0],1) . ',' . round($c[1],1) . ' ';
            }

            $lastIdx = $n-1;
            $lastLbl = $perWeek[$lastIdx]['label'];
            $lastValTxt = number_format($perWeek[$lastIdx]['v'], 1, ',', ' ');

            echo '<div class="dw-charts dw-spark-wrap">';
              echo '<div class="dw-spark-title"><strong>Försäljning (trend)</strong>'
                 . '<span class="dw-spark-meta">Senaste: '.$lastLbl.' '.$lastValTxt.' st/vecka &middot; Totalt: '.$qtytotal.' st</span></div>';

              echo '<svg width="100%" height="'.$H.'" viewBox="0 0 '.$W.' '.$H.'" role="img" aria-label="Försäljning sparkline (per vecka)">';
                for ($g = 1; $g <= 3; $g++) {
                    $gy = $pad + ($innerH * ($g / 4.0));
                    echo '<line x1="'.$pad.'" y1="'.round($gy,1).'" x2="'.($W-$pad).'" y2="'.round($gy,1).'" stroke="#eef2f7" stroke-width="1"/>';
                }
                echo '<line x1="'.$pad.'" y1="'.($H-$pad).'" x2="'.($W-$pad).'" y2="'.($H-$pad).'" stroke="#e5e7eb" stroke-width="1"/>';
                $area = $path.' L '.($W-$pad).',' . ($H-$pad) . ' L '.$pad.',' . ($H-$pad) . ' Z';
                echo '<path d="'.$area.'" fill="#e6f6ea"/>';
                echo '<path d="'.$path.'" fill="none" stroke="#065f46" stroke-width="2.5"/>';
                for ($i=0; $i<count($coords); $i++){
                    $c = $coords[$i];
                    echo '<circle cx="'.round($c[0],1).'" cy="'.round($c[1],1).'" r="3" fill="#065f46"/>';
                }
              echo '</svg>';
            echo '</div>';
        }

        echo '<div class="dw-section">';
        echo '<div class="dw-label" style="margin-bottom:6px">Sålt över tid</div>';
        echo '<table class="dw-table"><thead><tr>
              <th>1 v</th><th>1 man</th><th>3 man</th><th>6 man</th><th>12 man</th><th>36 man</th><th>Totalt</th>
              </tr></thead><tbody><tr>';
        echo '<td>'.$qtyweek.'</td>';
        echo '<td>'.$qtymonth.'</td>';
        echo '<td>'.$qty3month.'</td>';
        echo '<td>'.$qty6month.'</td>';
        echo '<td>'.$qty12month.'</td>';
        echo '<td>'.$qty36month.'</td>';
        echo '<td>'.$qtytotal.'</td>';
        echo '</tr></tbody></table>';
        echo '</div>';

    } else {
        echo '<div class="dw-section dw-muted" style="margin-top:6px">Inga säljsiffror i statistiken</div>';
    }

    // ===== RMA: antal kopplade till artikeln =====
    $rmaQty = 0;
    $sqlRma = "
        SELECT COALESCE(SUM(rmal.qty),0) AS rma_qty
          FROM m_rma rma
          JOIN m_rmaline rmal ON rmal.m_rma_id = rma.m_rma_id
          JOIN m_inoutline iol ON iol.m_inoutline_id = rmal.m_inoutline_id
          JOIN m_product p2 ON p2.m_product_id = iol.m_product_id
         WHERE rma.docstatus = 'CO'
           AND rma.m_rmatype_id IN (1000001,1000005)
           AND rma.c_doctype_id = 1000029
           AND p2.value = $1
    ";
    if ($rsR = ($conn) ? @pg_query_params($conn, $sqlRma, array($row['article'])) : false) {
        if ($rsR && $rr = pg_fetch_assoc($rsR)) $rmaQty = (int)$rr['rma_qty'];
        pg_free_result($rsR);
    }
    $artParam = rawurlencode($row['article']);
    $rmaUrl   = 'https://admin.cyberphoto.se/rma_summary.php?article='.$artParam;

    if ($rmaQty > 0) {
        echo '<div class="dw-card">';
            echo '<div class="dw-section" style="margin-top:6px">';
            echo '  <span class="dw-label">RMA: </span>';
            echo '  <a href="'.$rmaUrl.'" target="_blank" rel="noopener" class="dw-inline-link dw-val"><span class="u">'.$rmaQty.' st</span></a>';
            echo '</div>';
        echo '</div>';
    }

    // ===== Snabbknappar =====
    $art     = $h($row['article']);
    $pidOut  = (int)$row['m_product_id'];
    $suppUrl = 'https://admin.cyberphoto.se/suplier.php?artnr='.$art;
    $soldUrl = 'https://admin.cyberphoto.se/sold_article.php?product_id='.$pidOut;
    $moniUrl = 'https://admin.cyberphoto.se/monitor_articles.php?add=yes&addArtnr='.$art;
    $feedUrl = 'https://admin.cyberphoto.se/product_feedback.php?popup=1&artnr='.$art.'&ordernr=';
    $editUrl = 'https://admin.cyberphoto.se/product_update.php?artnr='.$art.'&m_product_id='.$pidOut;

	$isBundle = (isset($row['issalesbundle']) && strtoupper((string)$row['issalesbundle']) === 'Y');
	if ($isBundle) {
		$soldUrl .= '&show_salesbundle=yes';
	}

    echo '<div class="dw-actions">';
      echo '<a href="#" class="dw-btn" onclick="window.open(\''.$suppUrl.'\',\'supplier_'.$pidOut.'\',\'width=550,height=500,menubar=0,toolbar=0,location=0,status=0,resizable=1,scrollbars=1\');return false;">Leverantör</a>';
      echo '<a href="'.$soldUrl.'" class="dw-btn" target="_blank" rel="noopener">S&aring;lda</a>';
      echo '<a href="#" class="dw-btn" onclick="window.open(\''.$moniUrl.'\',\'monitor_'.$pidOut.'\',\'width=720,height=700,menubar=0,toolbar=0,location=0,status=0,resizable=1,scrollbars=1\');return false;">Bevaka</a>';
      echo '<a href="#" class="dw-btn" onclick="window.open(\''.$feedUrl.'\',\'report_'.$pidOut.'\',\'width=900,height=800,menubar=0,toolbar=0,location=0,status=0,resizable=1,scrollbars=1\');return false;">Rapportera</a>';
      echo '<a href="#" class="dw-btn" onclick="window.open(\''.$editUrl.'\',\'product_update_'.$pidOut.'\',\'width=800,height=900,menubar=0,toolbar=0,location=0,status=0,resizable=1,scrollbars=1\');return false;">Editera</a>';
    echo '</div>';

    // ===== Aktuell leverantör =====
    $sqlSupp = "
        SELECT
            bp.name                 AS supplier_name,
            bp.value                AS supplier_code,
            bp.url                  AS supplier_url,
            mpo.vendorproductno     AS vendor_product_no,
            rep.level_min           AS min_level,
            rep.level_max           AS max_level,
            bp.usernamevendor       AS vendor_user,
            bp.passwordvendor       AS vendor_pass,
            au_buyer.name           AS buyer_name
        FROM m_product_po mpo
        JOIN c_bpartner bp
          ON bp.c_bpartner_id = mpo.c_bpartner_id
        LEFT JOIN m_replenish rep
          ON rep.m_product_id = mpo.m_product_id
        LEFT JOIN Ad_User au_buyer
          ON au_buyer.Ad_User_id = bp.salesrep_id
        WHERE mpo.isactive = 'Y'
          AND mpo.iscurrentvendor = 'Y'
          AND mpo.m_product_id = $1
        LIMIT 1
    ";
    $supp = null;
    if ($rs = ($conn) ? @pg_query_params($conn, $sqlSupp, array($pid)) : false) {
        $supp = $rs ? pg_fetch_assoc($rs) : null;
        pg_free_result($rs);
    }

    if ($supp) {
        $sName   = $h($supp['supplier_name']);
        $sCode   = isset($supp['supplier_code']) ? trim($supp['supplier_code']) : '';
        $sUrl    = trim((string)$supp['supplier_url']);
		$sVnoRaw = isset($supp['vendor_product_no']) ? trim($supp['vendor_product_no']) : '';
		$sVno    = $h($sVnoRaw);
        $sUser   = $h($supp['vendor_user']);
        $sPass   = $h($supp['vendor_pass']);
        $sBuyer  = isset($supp['buyer_name']) ? trim($supp['buyer_name']) : '';

        echo '<div class="dw-card dw-supp">';
          echo '<div class="dw-card-title">Aktuell leverantör</div>';
          echo '<div class="kv">';

            echo '<div class="kv-key">Leverantör:</div><div class="kv-val">';
            if ($sName !== '') {
                if ($sCode !== '') {
                    $supLink = 'https://admin.cyberphoto.se/supplier.php?supID='.rawurlencode($sCode);
                    echo '<a href="'.$supLink.'" target="_blank" rel="noopener">'.$sName.'</a> ';
                    echo '<span class="copy-chip" data-copy="'.$h($sCode).'" title="Kopiera kundnummer">('.$h($sCode).')</span>';
                } else {
                    echo $sName;
                }
            } else {
                echo '-';
            }
            echo '</div>';

            if (!in_array($sCode, array('5555', '4444'), true)) {
                echo '<div class="kv-key">Lev. art.nr:</div><div class="kv-val">'
                   . ($sVnoRaw !== ''
                        ? '<span class="copy-chip" data-copy="'.$h($sVnoRaw).'" title="Kopiera lev. art.nr">'.$sVno.'</span>'
                        : '-' )
                   . '</div>';
                echo '<div class="kv-key">Användare:</div><div class="kv-val">'.($sUser !== '' ? '<span class="copy-chip" data-copy="'.$sUser.'" title="Kopiera användar-namn">'.$sUser.'</span>' : '-').'</div>';
                echo '<div class="kv-key">Lösenord:</div><div class="kv-val">'.($sPass !== '' ? '<span class="copy-chip" data-copy="'.$sPass.'" title="Kopiera lösenord">'.$sPass.'</span>' : '-').'</div>';
                echo '<div class="kv-key">Hemsida:</div><div class="kv-val">'.($sUrl !== '' ? '<a href="'.$h($sUrl).'" target="_blank" rel="noopener">'.$h($sUrl).'</a>' : '-').'</div>';
                echo '<div class="kv-key">Inköpar:</div><div class="kv-val">'.($sBuyer !== '' ? $h($sBuyer) : '-').'</div>';
            }

          echo '</div>';
        echo '</div>';
    }

    /* ===== Rapporterad missad försäljning (MySQL) - summering per orsak ===== */
    $grpRows    = array();
    $totalCount = 0;
    $lastOverallTsTxt = '';

    $mysql = Db::getConnection(false); // ext/mysql-länk
    if ($mysql && !empty($row['article'])) {
        $artEsc = mysqli_real_escape_string($mysql, $row['article']);
        $sqlG = "
            SELECT
                pf.reason_id,
                COALESCE(r.label, '(okänd)') AS label,
                COUNT(*) AS cnt,
                MAX(pf.`timestamp`) AS last_ts
            FROM cyberphoto.product_feedback pf
            LEFT JOIN cyberphoto.product_feedback_reasons r ON r.id = pf.reason_id
            WHERE pf.artnr = '".$artEsc."'
            GROUP BY pf.reason_id, r.label
            ORDER BY cnt DESC, last_ts DESC
        ";
        if ($rsG = @mysqli_query($mysql, $sqlG)) {
            $lastOverallTs = null;
            while ($rg = @mysqli_fetch_assoc($rsG)) {
                $cnt = (int)$rg['cnt'];
                $totalCount += $cnt;
                $tsTxt = '';
                if (!empty($rg['last_ts'])) {
                    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $rg['last_ts']);
                    if ($dt) $tsTxt = $dt->format('Y-m-d H:i');
                    if ($dt && (!$lastOverallTs || $dt > $lastOverallTs)) { $lastOverallTs = $dt; }
                }
                $grpRows[] = array('label'=>$rg['label'], 'cnt'=>$cnt, 'ts'=>$tsTxt);
            }
            @mysqli_free_result($rsG);
            if ($lastOverallTs) $lastOverallTsTxt = $lastOverallTs->format('Y-m-d H:i');
        }
    }

    echo '<div class="dw-card">';
      echo '<h3>Rapporterad missad försäljning</h3>';

      if ($totalCount > 0) {
          echo '<div class="dw-pill dw-pill-ok">'.$totalCount.' rapport'.($totalCount===1?'':'er').'</div>';
          if ($lastOverallTsTxt !== '') { echo ' <span class="dw-muted">&ndash; Senast: '.$lastOverallTsTxt.'</span>'; }

          echo '<div style="margin-top:8px">';
          echo '<table class="dw-table" style="font-size:12px"><thead><tr>';
          echo '<th>Anledning</th><th style="width:1%;white-space:nowrap">Antal</th><th style="width:1%;white-space:nowrap">Senast</th>';
          echo '</tr></thead><tbody>';
          foreach ($grpRows as $g) {
              echo '<tr>';
              echo '<td>'.htmlspecialchars($g['label'], ENT_QUOTES, 'UTF-8').'</td>';
              echo '<td style="text-align:right">'.(int)$g['cnt'].'</td>';
              echo '<td style="white-space:nowrap">'.($g['ts']!=='' ? $g['ts'] : '-').'</td>';
              echo '</tr>';
          }
          echo '</tbody></table>';
          echo '</div>';
      } else {
          echo '<div class="dw-pill dw-pill-none">Inga rapporter</div>';
      }

	$feedDetailsUrl = 'https://admin.cyberphoto.se/product_feedback.php?artnr='.$art.'&details=1';
	echo ' <a href="'.$feedDetailsUrl.'" class="dw-btn" target="_blank">Öppna rapporter</a>';
    echo '</div>';
	
    // Kolla senaste inköopsordrar
	$poRows = array();
	$pid    = (int)$row['m_product_id'];
	
	$sqlPo = "
		SELECT
			o.c_order_id,
			o.documentno,
			o.dateordered,
			COALESCE(bp.name, '') AS vendor_name,
			SUM(ol.qtyordered)    AS qty
		FROM c_orderline ol
		JOIN c_order o
		  ON o.c_order_id = ol.c_order_id
		LEFT JOIN c_bpartner bp
		  ON bp.c_bpartner_id = o.c_bpartner_id
		WHERE ol.m_product_id = $1
		  AND o.issotrx = 'N'
		  AND o.docstatus NOT IN ('VO','RE')
		GROUP BY o.c_order_id, o.documentno, o.dateordered, bp.name
		ORDER BY o.dateordered DESC NULLS LAST, o.c_order_id DESC
		LIMIT 20
	";

	$rPo = ($conn) ? @pg_query_params($conn, $sqlPo, array($pid)) : false;
	if ($rPo) {
		while ($rPo && $rowPo = pg_fetch_assoc($rPo)) {
			$poRows[] = $rowPo;
		}
		pg_free_result($rPo);
	}

	$purchaseOrders = $poRows;

	if (!empty($purchaseOrders)) {
		echo '<div class="dw-card">';
		echo '  <div class="dw-card-h2">Inköpsordrar</div>';

		echo '  <table class="table-list" style="margin-top:4px;">';
		echo '    <colgroup>
					 <col style="width:16ch" />
					 <col style="width:14ch" />
					 <col />
					 <col style="width:8ch" />
				   </colgroup>';
		echo '    <thead>
					 <tr>
					   <th>Datum</th>
					   <th>Order</th>
					   <th>Leverantör</th>
					   <th class="text-right">Antal</th>
					 </tr>
				   </thead>';
		echo '    <tbody>';

		foreach ($purchaseOrders as $po) {
			$od  = $po['dateordered'] ? $h(date('Y-m-d', strtotime($po['dateordered']))) : '-';
			$doc = $h($po['documentno']);
			$ven = $po['vendor_name'] !== '' ? $h($po['vendor_name']) : '-';
			$qty = is_numeric($po['qty']) ? (int)$po['qty'] : 0;

			$url  = '/search_dispatch.php?mode=order&page=1&q=' . rawurlencode($po['documentno']);
			$link = '<a href="'.$url.'" target="_blank" rel="noopener">'.$doc.'</a>';

			echo '<tr>';
			echo '  <td>'.$od.'</td>';
			echo '  <td>'.$link.'</td>';
			echo '  <td>'.$ven.'</td>';
			echo '  <td class="text-right">'.$qty.'</td>';
			echo '</tr>';
		}

		echo '    </tbody>';
		echo '  </table>';

		echo '</div>'; // dw-card
	}

	// UTF-8-safe escape
	$h = function($s){
		return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
	};

	// --- Metadata: upplagd av / skapad / aalder i dagar ---
	$createdBy = isset($row['created_by_name']) ? trim((string)$row['created_by_name']) : '';
	$createdTs = isset($row['created']) ? trim((string)$row['created']) : '';

	$ageBadgeHtml = '-';
	$createdLabel = '-';

	if ($createdTs !== '') {
		try {
			$tz       = new DateTimeZone('Europe/Stockholm');
			$dt       = new DateTime($createdTs, $tz);
			$createdLabel = $dt->format('Y-m-d H:i:s');

			$today    = new DateTime('today', $tz);
			$diff     = $today->diff($dt);
			$days     = (int)$diff->days;

			if ($days < 0) $days = 0;

			if ($days <= 60) {
				$ageClass = 'green';
			} elseif ($days <= 90) {
				$ageClass = 'orange';
			} else {
				$ageClass = 'red';
			}

			$ageBadgeHtml = '<span class="badge-age ' . $ageClass . '" title="Produkten skapades ' . $h($createdLabel) . '">' 
						  . $days . 'd</span>';
		} catch (Exception $e) {
			// lämna default
		}
	}

	$createdByLabel = $createdBy !== '' ? $h($createdBy) : 'Okänd';
	$createdLabel   = $createdLabel !== '' ? $h($createdLabel) : '-';

	echo '<div class="dw-card dw-card-meta" style="margin-top:10px;">';
    echo '<h3>Metadata</h3>';
	echo '  <div style="font-size:13px;">';
	echo '    <div>Upplagd av: <strong>' . $createdByLabel . '</strong></div>';
	echo '    <div>Skapades: <strong>' . $createdLabel . '</strong>&nbsp; ' . $ageBadgeHtml . '</div>';
	echo '  </div>';
	echo '</div>';

    echo '</div>'; // dw-wrap

	exit;
}

// default
echo '<p>AJAX-routen OK. Ange ?type=order&id=ORDERNUMMER.</p>';

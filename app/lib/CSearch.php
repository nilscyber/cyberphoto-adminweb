<?php
require_once("CCheckIpNumber.php");
require_once("Db.php");

Class CSearch {

public static function debugSql($sql, $params, $conn) {
    $out = $sql;
    
    // Ersätt parametrar i OMVÄND ordning för att undvika överlappningar
    // $13 före $3, $12 före $2, osv.
    for ($i = count($params); $i >= 1; $i--) {
        $placeholder = '$' . $i;
        $val = $params[$i - 1]; // array är 0-indexerad
        
        if ($val === null) {
            $escaped = 'NULL';
        } elseif (is_numeric($val)) {
            $escaped = $val;
        } else {
            $escaped = "'" . pg_escape_string($conn, (string)$val) . "'";
        }
        
        $out = str_replace($placeholder, $escaped, $out);
    }
    
    return $out;
}

public function redirectLegacyInfoToAdmin()
{
    if (empty($_GET['article'])) {
        // Ingen artikel -> låt ev. gammal info.php fortsätta
        return;
    }

    $article = trim($_GET['article']);

    // ADempiere-anslutning (PostgreSQL) som resource
    $conn = Db::getConnectionAD(false); // read-only

    if (!$conn) {
        // Om något strular, skippa redirect och låt sidan leva vidare som tidigare
        return;
    }

    // Använd pg_query_params så slipper vi manuell escapning
    $sql = "
        SELECT m_product_id
        FROM m_product
        WHERE value = $1
        ORDER BY m_product_id
        LIMIT 1
    ";

    $result = ($conn) ? @pg_query_params($conn, $sql, array($article)) : false;

    $row = $result ? pg_fetch_assoc($result) : false;

    // Parametrar mot nya admin-sidan
    $params = array(
        'q'    => $article,
        'open' => 'product',
    );

    if ($row && !empty($row['m_product_id'])) {
        $params['id'] = (int)$row['m_product_id'];
    }

	if (!$row) {
		// Ingen träff i m_product  skicka användaren till nya söken ändå
		$params = array(
			'q'    => $article,
			'open' => 'product',
		);
	}

    $baseUrl  = 'https://admin.cyberphoto.se/search_dispatch.php';
    $location = $baseUrl . '?' . http_build_query($params, '', '&');

    header('Location: ' . $location);
    exit;
}

public static function searchProductsAD($q, $limit = 50, $page = 1, $opts = array()) {
    $conn = Db::getConnectionAD(false);
    if ($conn) { @pg_set_client_encoding($conn, "UTF8"); }

    // Direktträff via m_product_id (delningslänk / info.php-redirect)
    $directProductId = 0;
    if (!empty($opts['product_id'])) {
        $directProductId = (int)$opts['product_id'];
    }

    // Är användaren ett trade-in-konto?
    $isTradeIn = false;
    if (class_exists('CCheckIP') && method_exists('CCheckIP', 'checkIfLoginIsTradeIn')) {
        try { $isTradeIn = (bool) CCheckIP::checkIfLoginIsTradeIn(); } catch (Exception $e) {}
    }

    $isPriority = false;
    if (class_exists('CCheckIP') && method_exists('CCheckIP', 'checkIfLoginIsPriority')) {
        try { $isPriority = (bool) CCheckIP::checkIfLoginIsPriority(); } catch (Exception $e) {}
    }

    // Söksträng  trim + säker UTF-8
    $q = trim((string)$q);
    if ($q !== '' && function_exists('mb_detect_encoding') && function_exists('mb_convert_encoding')) {
        if (mb_detect_encoding($q, 'UTF-8', true) === false) {
            $q = mb_convert_encoding($q, 'UTF-8', 'ISO-8859-1, Windows-1252');
        }
    }
    if ($q !== '' && function_exists('iconv')) {
        $q = @iconv('UTF-8', 'UTF-8//IGNORE', $q);
    }

    $limit  = max(1, (int)$limit);
    $page   = max(1, (int)$page);
    $offset = ($page - 1) * $limit;

    // Läs filter
    $inStock      = !empty($opts['in_stock'])     || !empty($_GET['in_stock']);
    $discontinued = !empty($opts['discontinued']) || !empty($_GET['discontinued']);
    $oldTradeIns  = !empty($opts['old_tradeins']) || !empty($_GET['old_tradeins']);
    $usedWeb      = !empty($opts['used_web'])     || !empty($_GET['used_web']);
    $usedOffWeb   = !empty($opts['used_offweb'])  || !empty($_GET['used_offweb']);
    $hideTradeIn  = !empty($opts['hide_tradein']);
    $hideDemo     = !empty($opts['hide_demo']);
    $notWeb       = !empty($opts['not_web']);

    // Om varken text eller id -> tomt resultat
    if ($q === '' && $directProductId <= 0) {
        return array(
            'total'     => 0,
            'page'      => $page,
            'limit'     => $limit,
            'rows'      => array(),
            'isTradeIn' => $isTradeIn
        );
    }

    // -------- Textvillkor / Direktträff --------
    $params = array();
    $wheres = array();
    $i      = 1;

    if ($directProductId > 0) {
        // DIREKTLÄGE: slå direkt på primärnyckeln
        $wheres[] = "p.m_product_id = $" . $i;
        $params[] = $directProductId;
        $i++;
    } else {
        // ========================================
        // FIX v44b: Striktare matchning för siffror + korrekt $i-räkning
        // ========================================
        $tokens = preg_split('/\s+/', $q);

        foreach ($tokens as $t) {
            if ($t === '') continue;

            $tEscaped  = str_replace(array('%','_'), array('\\%','\\_'), $t);
            $isNumeric = preg_match('/^\d+$/', $t);

            $or = array();

            if ($isNumeric) {
                // Rent tal: regex med word boundary så "40" inte matchar "400" eller "1040"
                $numPattern = '(^|[^0-9])' . $t . '([^0-9]|$)';

                // Tydlig $i-räkning: öka manuellt för varje fält
                $or[] = "p.value ~ $"                                 . $i; $params[] = $numPattern; $i++;
                $or[] = "p.name ~ $"                                  . $i; $params[] = $numPattern; $i++;
                $or[] = "p.description ~ $"                           . $i; $params[] = $numPattern; $i++;
                $or[] = "COALESCE(manu.name,'') ~ $"                  . $i; $params[] = $numPattern; $i++;
                $or[] = "COALESCE(pc.name,'') ~ $"                    . $i; $params[] = $numPattern; $i++;
                $or[] = "COALESCE(p.manufacturerproductno,'') ~ $"    . $i; $params[] = $numPattern; $i++;
                $or[] = "COALESCE(p.upc,'') ~ $"                      . $i; $params[] = $numPattern; $i++;

            } else {
                // Text/blandat: vanlig ILIKE
                $like = '%' . $tEscaped . '%';

                $or[] = "p.value ILIKE $"                                 . $i; $params[] = $like; $i++;
                $or[] = "p.name ILIKE $"                                  . $i; $params[] = $like; $i++;
                $or[] = "p.description ILIKE $"                           . $i; $params[] = $like; $i++;
                $or[] = "COALESCE(manu.name,'') ILIKE $"                  . $i; $params[] = $like; $i++;
                $or[] = "COALESCE(pc.name,'') ILIKE $"                    . $i; $params[] = $like; $i++;
                $or[] = "COALESCE(p.manufacturerproductno,'') ILIKE $"    . $i; $params[] = $like; $i++;
                $or[] = "COALESCE(p.upc,'') ILIKE $"                      . $i; $params[] = $like; $i++;
            }

            // Varje ord MÅSTE finnas någonstans (AND-logik mellan tokens)
            $wheres[] = '(' . implode(' OR ', $or) . ')';
        }
    }

    // -------- Synlighet --------
    $visibility = "("
                . " p.isselfservice = 'Y'"
                . " AND (p.launchdate IS NULL OR p.launchdate <= NOW())"
                . ")";

    if ($isTradeIn) {
        $visibility = "(" . $visibility . " OR ("
                    . "   p.istradein = 'Y'"
                    . "   AND (p.isselfservice = 'N' OR (p.salestart IS NOT NULL AND p.salestart > NOW()))"
                    . "   AND EXISTS (SELECT 1 FROM m_product_cache c0"
                    . "               WHERE c0.m_product_id = p.m_product_id"
                    . "                 AND c0.m_warehouse_id = 1000000"
                    . "                 AND c0.qtyonhand > 0)"
                    . "))";
    }

    // Efter att $isTradeIn och $isPriority satts
    if (!$isPriority) {
        if ($isTradeIn) {
            // Inbyteskonton: samma tidsregel, men begagnat släpps igenom även före lansering
            $wheres[] =
                "("
              . " (p.launchdate IS NULL OR p.launchdate <= NOW())"
              . " OR p.istradein = 'Y'"
              . ")";
        } else {
            // Vanliga användare: bara lanserade (eller utan datum)
            $wheres[] =
                "("
              . " p.launchdate IS NULL"
              . " OR p.launchdate <= NOW()"
              . ")";
        }
    }

    // -------- Lager (endast 1000000) --------
    $stockSub = "
        SELECT m_product_id,
               SUM(qtyonhand)    AS qtyonhand,
               SUM(qtyreserved)  AS qtyreserved,
               SUM(qtyordered)   AS qtyordered,
               SUM(qtyavailable) AS qtyavailable
          FROM m_product_cache
         WHERE m_warehouse_id = 1000000
         GROUP BY m_product_id
    ";

    // -------- Filterlogik --------
    if ($notWeb) {  // "Ej med"
        $wheres[] = "p.isselfservice = 'N'";
        $wheres[] = "p.demo_product = 'N'";
        $wheres[] = "COALESCE(p.issalesbundle,'N') <> 'Y'";
    } elseif ($discontinued) {
        $wheres[] = "p.isselfservice = 'Y'";
        $wheres[] = "p.discontinued = 'Y'";
        $wheres[] = "COALESCE(ps.qtyavailable,0) < 1";
        $wheres[] = "p.demo_product = 'N'";
        $wheres[] = "p.istradein = 'N'";
        $wheres[] = "COALESCE(p.issalesbundle,'N') <> 'Y'";
    } elseif ($oldTradeIns) {
        $wheres[] = "p.istradein = 'Y'";
        $wheres[] = "COALESCE(ps.qtyavailable,0) < 1";
    } else {
        $wheres[] = $visibility;
        $wheres[] = "(p.demo_product = 'N' OR COALESCE(ps.qtyavailable,0) > 0)";
        $wheres[] = "(p.discontinued = 'N' OR COALESCE(ps.qtyavailable,0) > 0)";
        if ($hideTradeIn) { $wheres[] = "p.istradein = 'N'"; }
        if ($hideDemo)    { $wheres[] = "(p.demo_product <> 'Y' OR p.istradein = 'Y')"; }
    }

    /* === Trade-in del-filter: Ute webb / Ej webb (adderande) ===
       Gäller bara när man INTE valt 'Utgångna' eller 'Gamla inbyten'.
       - Ute webb   => begagnat + tillgängligt + isselfservice = 'Y'
       - Ej webb    => begagnat + tillgängligt + isselfservice = 'N'
       - Båda       => begagnat + tillgängligt (oavsett isselfservice)
    */
    if (!$discontinued && !$oldTradeIns && ($usedWeb || $usedOffWeb)) {
        $wheres[] = "p.istradein = 'Y'";
        $wheres[] = "COALESCE(ps.qtyavailable,0) > 0";
        if ($usedWeb xor $usedOffWeb) {
            $wheres[] = "p.isselfservice = '" . ($usedWeb ? 'Y' : 'N') . "'";
        }
    }

    // Endast i lager?
    if ($inStock) {
        $wheres[] = "EXISTS (SELECT 1 FROM m_product_cache mc"
                  . "          WHERE mc.m_product_id = p.m_product_id"
                  . "            AND mc.m_warehouse_id = 1000000"
                  . "            AND mc.qtyavailable > 0)";
    }

    // ====== Exkludera artiklar / lagerplatser via separat konfig ======
    $EXCL_ARTICLES = array();
    $EXCL_LOCATORS = array();
    $EXCL_LOCATOR_REQUIRE_QTY = true;  // obsolete i v44c men behålls för bakåtkompatibilitet

    $cfgPath = __DIR__ . '/search_exclude.php';
    if (is_file($cfgPath)) { include $cfgPath; }

    // 2a) Artiklar (p.value NOT IN (...))
    if (!empty($EXCL_ARTICLES) && is_array($EXCL_ARTICLES)) {
        $ph = array();
        foreach ($EXCL_ARTICLES as $v) {
            $params[] = (string)$v;
            $ph[] = '$' . count($params);
        }
        if (!empty($ph)) {
            $wheres[] = 'p.value NOT IN (' . implode(',', $ph) . ')';
        }
    }

    // ========================================
    // FIX v44d: Tillåt produkter utan lagerplats (NULL) + exkludera svartlistade
    // ========================================
    if (!empty($EXCL_LOCATORS) && is_array($EXCL_LOCATORS)) {
        $ph = array();
        foreach ($EXCL_LOCATORS as $id) {
            $params[] = (int)$id;
            $ph[] = '$' . count($params);
        }
        if (!empty($ph)) {
            // Visa produkter utan plats (NULL) + alla utom svartlistade
            $wheres[] = '(p.m_locator_id IS NULL OR p.m_locator_id NOT IN (' . implode(',', $ph) . '))';
        }
    }
    // ====== slut exkludering ======

    // Safety: om inga where-villkor skapats alls (borde inte hända)
    if (empty($wheres)) {
        $wheres[] = '1=1';
    }

    // -------- COUNT --------
    $baseCount = "
        FROM m_product p
        LEFT JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id
        LEFT JOIN m_product_category pc ON pc.m_product_category_id = p.m_product_category_id
        LEFT JOIN ($stockSub) ps ON ps.m_product_id = p.m_product_id
        WHERE " . implode(' AND ', $wheres);

    $sqlCount = "SELECT COUNT(*) AS cnt " . $baseCount;

    // ========================================
    // DEBUG: COUNT QUERY
    // ========================================
    if (!empty($_GET['debug_sql'])) {
        echo "<div style='background:#f0f0f0;border:2px solid #333;padding:12px;margin:10px;font-family:monospace;font-size:11px;overflow:auto'>";
        echo "<strong style='color:#c00'>DEBUG: COUNT QUERY</strong><br><br>";
        echo "<pre style='white-space:pre-wrap;word-wrap:break-word'>" . htmlspecialchars(CSearch::debugSql($sqlCount, $params, $conn), ENT_QUOTES, 'UTF-8') . "</pre>";
        echo "</div>";
    }
    // error_log("===== SEARCH COUNT QUERY =====");
    // error_log(CSearch::debugSql($sqlCount, $params, $conn));
    // error_log("==============================");
    // ========================================

    $total = 0;
    if ($rc = ($conn) ? @pg_query_params($conn, $sqlCount, $params) : false) {
        if ($rc && $rowc = pg_fetch_assoc($rc)) $total = (int)$rowc['cnt'];
        pg_free_result($rc);
    }

    // Clamp page/offset efter COUNT (fixar "träffar men inga rader")
    $maxPage = max(1, (int)ceil($total / $limit));
    if ($page > $maxPage) {
        $page   = $maxPage;
        $offset = ($page - 1) * $limit;
    }

    // -------- LIST-SELECT --------
    $sql = "
SELECT
    p.m_product_id,
    p.value AS article,
    p.istradein,
    p.demo_product,
    p.discontinued,
    p.issalesbundle,
    p.is_spec13 AS spec_13,
    p.description AS description_text,
    p.isdropship AS is_dropship,
    p.salestart AS salestart,
    p.launchdate,
    p.isselfservice,
    TRIM(COALESCE(manu.name,'') || ' ' || COALESCE(p.name,'')) AS product_full,

    COALESCE(pp.pricestd, pp.pricelist, 0) AS price_ex,

    COALESCE(
      pp.pricelimit,
      (
        SELECT mc.currentcostprice
          FROM m_cost mc
         WHERE mc.m_product_id = p.m_product_id
         ORDER BY mc.updated DESC
         LIMIT 1
      ),
      0
    ) AS net_price,

    COALESCE(ps.qtyonhand, 0)    AS stock_qty,
    COALESCE(ps.qtyreserved, 0)  AS queue_qty,
    COALESCE(ps.qtyordered, 0)   AS ordered_qty,
    COALESCE(ps.qtyavailable, 0) AS available_qty,

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

    pc.m_product_category_id          AS category_id,
    COALESCE(pc.name,'')              AS category_name,
    pc.value                          AS category_value,
    COALESCE(pc.sort_priority,999999) AS category_priority,

    CASE WHEN p.isselfservice = 'N' AND COALESCE(ps.qtyavailable,0) > 0 THEN 1 ELSE 0 END AS is_unpublished,
    CASE WHEN p.salestart   IS NOT NULL AND p.salestart   > NOW() THEN 1 ELSE 0 END AS is_sales_future,
    CASE WHEN p.launchdate IS NOT NULL AND p.launchdate > NOW() THEN 1 ELSE 0 END AS is_launch_future,
    CASE WHEN p.istradein   = 'Y' THEN 1 ELSE 0 END AS is_used,
    CASE WHEN p.demo_product = 'Y' THEN 1 ELSE 0 END AS is_deal,

    COALESCE((
      SELECT xs.qtyweek
        FROM xc_product_statistics xs
       WHERE xs.m_product_id = p.m_product_id
         AND xs.c_country_id = 313
       ORDER BY xs.updated DESC
       LIMIT 1
    ), 0) AS sold_7d,

    COALESCE((
      SELECT xs.qtymonth
        FROM xc_product_statistics xs
       WHERE xs.m_product_id = p.m_product_id
         AND xs.c_country_id = 313
       ORDER BY xs.updated DESC
       LIMIT 1
    ), 0) AS sold_30d,

    COALESCE((
      SELECT xs.qty3month
        FROM xc_product_statistics xs
       WHERE xs.m_product_id = p.m_product_id
         AND xs.c_country_id = 313
       ORDER BY xs.updated DESC
       LIMIT 1
    ), 0) AS sold_90d

FROM m_product p
LEFT JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id
LEFT JOIN m_product_category pc ON pc.m_product_category_id = p.m_product_category_id
LEFT JOIN ($stockSub) ps ON ps.m_product_id = p.m_product_id
LEFT JOIN m_productprice pp
       ON pp.m_product_id = p.m_product_id
      AND pp.m_pricelist_version_id = 1000000
WHERE " . implode(' AND ', $wheres) . "
" . (
    $oldTradeIns
    ? "ORDER BY COALESCE(pc.sort_priority,999999) DESC, p.salestart DESC NULLS LAST, pc.name NULLS LAST, manu.name NULLS LAST, p.name"
    : "ORDER BY COALESCE(pc.sort_priority,999999) DESC, pc.name NULLS LAST, manu.name NULLS LAST, p.name"
) . "
LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

    // ========================================
    // DEBUG: SELECT QUERY
    // ========================================
    if (!empty($_GET['debug_sql'])) {
        echo "<div style='background:#f0f0f0;border:2px solid #333;padding:12px;margin:10px;font-family:monospace;font-size:11px;overflow:auto'>";
        echo "<strong style='color:#c00'>DEBUG: SELECT QUERY</strong><br><br>";
        echo "<pre style='white-space:pre-wrap;word-wrap:break-word'>" . htmlspecialchars(CSearch::debugSql($sql, $params, $conn), ENT_QUOTES, 'UTF-8') . "</pre>";
        echo "</div>";
    }
    // error_log("===== SEARCH SELECT QUERY =====");
    // error_log(CSearch::debugSql($sql, $params, $conn));
    // error_log("===============================");
    // ========================================

    $rows = array();
    if ($res = ($conn) ? @pg_query_params($conn, $sql, $params) : false) {
        while ($res && $r = pg_fetch_assoc($res)) {
            $rate = isset($r['tax_rate']) ? (float)$r['tax_rate'] : 0.0;
            $ex   = (float)$r['price_ex'];                 // exkl moms
            $inc  = $ex * (1.0 + $rate / 100.0);           // inkl moms
            $cost = (float)$r['net_price'];                // kostnad (pricelimit / m_cost fallback)

            // TG i % (heltal); null om ex=0
            $r['tg_percent']      = ($ex > 0) ? (int)round((($ex - $cost) / $ex) * 100.0) : null;

            // Priser (heltal för visning)
            $r['price_inc']       = $inc;
            $r['price_ex_round']  = (int)round($ex);
            $r['price_inc_round'] = (int)round($inc);

            // Sålda (säkerställ int) + tooltip 7/30/90
            $r['sold_7d']   = isset($r['sold_7d'])  ? (int)$r['sold_7d']  : 0;
            $r['sold_30d']  = isset($r['sold_30d']) ? (int)$r['sold_30d'] : 0;
            $r['sold_90d']  = isset($r['sold_90d']) ? (int)$r['sold_90d'] : 0;
            $r['sold_hover'] = $r['sold_7d'].' / '.$r['sold_30d'].' / '.$r['sold_90d'];

            // Lager som int
            $r['stock_qty']     = (int)$r['stock_qty'];
            $r['available_qty'] = (int)$r['available_qty'];
            $r['ordered_qty']   = (int)$r['ordered_qty'];
            $r['queue_qty']     = (int)$r['queue_qty'];

            // Webblänk (ikonkolumn)
            $r['web_url'] = 'https://www.cyberphoto.se/sok?q=' . rawurlencode($r['article']);

            $rows[] = $r;
        }
        pg_free_result($res);

        // Om total>0 men rows=0 på grund av gammal page/offset, kör om på sida 1.
        if ($total > 0 && count($rows) === 0 && $offset > 0) {
            $page   = 1;
            $offset = 0;
            $sql_fallback = preg_replace('/OFFSET\s+\d+/i', 'OFFSET 0', $sql);
            if ($res2 = ($conn) ? @pg_query_params($conn, $sql_fallback, $params) : false) {
                while ($res2 && $r = pg_fetch_assoc($res2)) {
                    $rate = isset($r['tax_rate']) ? (float)$r['tax_rate'] : 0.0;
                    $ex   = (float)$r['price_ex'];
                    $inc  = $ex * (1.0 + $rate / 100.0);
                    $cost = (float)$r['net_price'];

                    $r['tg_percent']      = ($ex > 0) ? (int)round((($ex - $cost) / $ex) * 100.0) : null;
                    $r['price_inc']       = $inc;
                    $r['price_ex_round']  = (int)round($ex);
                    $r['price_inc_round'] = (int)round($inc);

                    $r['sold_7d']   = isset($r['sold_7d'])  ? (int)$r['sold_7d']  : 0;
                    $r['sold_30d']  = isset($r['sold_30d']) ? (int)$r['sold_30d'] : 0;
                    $r['sold_90d']  = isset($r['sold_90d']) ? (int)$r['sold_90d'] : 0;
                    $r['sold_hover'] = $r['sold_7d'].' / '.$r['sold_30d'].' / '.$r['sold_90d'];

                    $r['stock_qty']     = (int)$r['stock_qty'];
                    $r['available_qty'] = (int)$r['available_qty'];
                    $r['ordered_qty']   = (int)$r['ordered_qty'];
                    $r['queue_qty']     = (int)$r['queue_qty'];

                    $r['web_url'] = 'https://www.cyberphoto.se/sok?q=' . rawurlencode($r['article']);
                    $rows[] = $r;
                }
                pg_free_result($res2);
            }
        }
    }

    return array(
        'total'     => $total,
        'page'      => $page,
        'limit'     => $limit,
        'rows'      => $rows,
        'isTradeIn' => $isTradeIn
    );
}


// Resten av klassen följer (buildListBadges, searchCustomersAD, etc.) - OFÖRÄNDRAD från v43
// Kopierar in hela resten nedan för komplett fil:

public static function buildListBadges(array $r)
{
    $badges = array();

    // ===== 1) Normaliserade flaggor =====
    $isUsedFlag = !empty($r['is_used']) && (int)$r['is_used'] === 1;
    $isDealFlag = !empty($r['is_deal']) && (int)$r['is_deal'] === 1;

    // --- Momssats (0 % = VMB) ---
    $vatRate = null;
    if (isset($r['tax_rate'])) {
        $vatRate = (float)$r['tax_rate'];
    }
    $isVatZero = ($vatRate !== null && abs($vatRate) < 0.001);

    // Begagnad trumfar Fyndvara
    if ($isUsedFlag) {
        $badges[] = '<span class="badge badge-used">Begagnad</span>';

        // VMB-badge direkt efter Begagnad
        if ($isVatZero) {
            $badges[] = '<span class="badge badge-vmb">VMB</span>';
        }
    } elseif ($isDealFlag) {
        $badges[] = '<span class="badge badge-deal">Fyndvara</span>';
    }

    // ===== 2) Ålders-/säljstart-badge =====
    $salestartTs = 0;
    if (!empty($r['salestart'])) {
        $tmp = @strtotime($r['salestart']);
        if ($tmp !== false) {
            $salestartTs = (int)$tmp;
        }
    }
    $nowTs = time();

    if ($salestartTs) {
        if ($salestartTs > $nowTs) {
            // Framtida säljstart  "Säljstart Nd"
            $daysUntil = (int)ceil(($salestartTs - $nowTs) / 86400);
            $badges[] = '<span class="badge badge-soon">Säljstart ' . $daysUntil . 'd</span>';
        } else {
            // Historisk säljstart  visa ålder ENDAST om begagnad/fyndvara
            if ($isUsedFlag || $isDealFlag) {
                $daysSince = (int)floor(($nowTs - $salestartTs) / 86400);
                $ageClass  = ($daysSince <= 60) ? 'green' : (($daysSince <= 90) ? 'orange' : 'red');
                $title     = 'Säljstart ' . date('Y-m-d', $salestartTs);
                $badges[]  = '<span class="badge-age ' . $ageClass . '" title="' . $title . '">' . $daysSince . 'd</span>';
            }
        }
    }

    // ===== 2b) Nyhet-badge (baserad på launchdate i första hand) =====
    $maxNewDays = 60; // Ändra här om ni vill ha längre/kortare nyhetsperiod

    $launchRaw = isset($r['launchdate']) ? trim((string)$r['launchdate']) : '';
    $saleRaw   = isset($r['salestart'])  ? trim((string)$r['salestart'])  : '';

    $launchBase = null;
    if ($launchRaw !== '') {
        // PRIORITERA launchdate om den finns
        $launchBase = $launchRaw;
    } elseif ($saleRaw !== '') {
        // fallback för äldre produkter som saknar launchdate
        $launchBase = $saleRaw;
    }

    if (!$isUsedFlag && !$isDealFlag && $launchBase !== null) {
        $launchTs = @strtotime($launchBase);
        if ($launchTs !== false) {
            $daysSinceLaunch = (int)floor(($nowTs - $launchTs) / 86400);

            // bara 0..$maxNewDays, inga framtida datum
            if ($daysSinceLaunch >= 0 && $daysSinceLaunch <= $maxNewDays) {
                $badges[] = '<span class="badge badge-new">Nyhet</span>';
            }
        }
    }

    // ===== 3) Dropship =====
    $dropRaw = isset($r['is_dropship']) ? $r['is_dropship'] : (isset($r['isdropship']) ? $r['isdropship'] : '');
    $v       = strtoupper((string)$dropRaw);
    $isDrop  = ($v === '1' || $v === 'Y' || $v === 'T' || $v === 'TRUE');
    if (!$isUsedFlag && !$isDealFlag && $isDrop) {
        $badges[] = '<span class="badge badge-drop">Dropship</span>';
    }

    // ===== 4) Rå produktflaggor =====
    $isUsedRaw = (isset($r['istradein'])    && $r['istradein']    === 'Y'); // begagnad
    $isDealRaw = (isset($r['demo_product']) && $r['demo_product'] === 'Y'); // fyndvara
    $isDisc    = (isset($r['discontinued']) && $r['discontinued'] === 'Y');
    $isBundle  = (isset($r['issalesbundle']) && $r['issalesbundle'] === 'Y');

    // Utgången visas ej på begagnad/fyndvara
    if ($isDisc && !$isUsedRaw && !$isDealRaw) {
        $badges[] = '<span class="badge-expired">Utgången</span>';
    }

    // ===== 5) Övriga statusar =====
    if (!empty($r['is_unpublished'])   && (int)$r['is_unpublished']   === 1) {
        $badges[] = '<span class="badge badge-unpub">Ej publicerad</span>';
    }
    if (!empty($r['is_launch_future']) && (int)$r['is_launch_future'] === 1) {
        $badges[] = '<span class="badge badge-launch">Lanseras snart</span>';
    }

    if ($isBundle) {
        $badges[] = '<span class="badge-bundle">Eget paket</span>';
    }

    // ===== Priority (spec_13 = 'Y') =====
    $prioRaw     = isset($r['spec_13']) ? strtoupper(trim((string)$r['spec_13'])) : '';
    $hasPriority = ($prioRaw === 'Y');

    if ($hasPriority) {
        $badges[] = '<span class="badge badge-priority">Priority</span>';
    }

	// ===== NEW: EJ WEB (isselfservice = 'N') =====
	$isNotWeb = (isset($r['isselfservice']) && strtoupper((string)$r['isselfservice']) === 'N' && !$isUsedRaw);
	if ($isNotWeb) {
		$badges[] = '<span class="badge badge-notweb">EJ WEB</span>';
	}

    return $badges ? '<span class="badges">' . implode(' ', $badges) . '</span>' : '';
}

/**
 * ERSÄTT den befintliga searchCustomersAD() i CSearch_v52.php med denna.
 *
 * Vad som fixats:
 *  - taxid-sökning var inlåst bakom $hasLetters-villkoret ? missade "556741-5772"
 *  - Taxid söks nu ALLTID, separat från namnblocket
 *  - Normalisering: sökning utan bindestreck/mellanslag jämförs mot
 *    REPLACE(bp.taxid,'-','') så att "5567415772" hittar "556741-5772" och vice versa
 *  - Safety net-frågan kompletterad med taxid också
 */
public static function searchCustomersAD($q, $limit = 50, $page = 1){
    $pg = Db::getConnectionAD(false);
    if (!$pg) return array('ok'=>false,'total'=>0,'rows'=>array());
    if ($pg) { @pg_set_client_encoding($pg, "UTF8"); }

    $toL1 = function($s){
        return (string)$s;
    };

    $q_raw  = trim((string)$q);
    $q_l1   = $toL1($q_raw);
    $like   = '%'.$q_l1.'%';

    // --- Klassificera söktermen ---
    $hasAt      = (strpos($q_raw,'@') !== false);
    $digits     = preg_replace('/\D+/', '', $q_raw);       // bara siffror ur söktermen
    $hasDigits5 = (strlen($digits) >= 5);
    $hasLetters = (bool)preg_match('/[A-Za-zÅÄÖåäö]/', $q_l1);

    // Normaliserat sökvärde för taxid (tar bort bindestreck och mellanslag)
    // Gör att "556741-5772", "5567415772" och "556741 5772" alla matchar varandra
    $taxidClean     = preg_replace('/[-\s]/', '', $q_l1);
    $taxidCleanLike = '%'.$taxidClean.'%';

    $limit  = max(1, (int)$limit);
    $page   = max(1, (int)$page);
    $offset = ($page-1)*$limit;

    $orderSql = "bp.updated DESC, UPPER(bp.name) ASC, bp.c_bpartner_id";

    // 1) Direktträff på kund-/lev.nr (bp.value) om bara siffror (exakt)
    if ($digits !== '' && ctype_digit($q_raw)) {
        $sql1 = "
            SELECT bp.c_bpartner_id,
                   bp.value AS bp_value,
                   TRIM(
                     COALESCE(bp.name,'') ||
                     CASE
                       WHEN COALESCE(bp.name2,'')<>'' AND lower(trim(bp.name2)) <> lower(trim(bp.name))
                       THEN '  '||bp.name2
                       ELSE ''
                     END
                   ) AS fullname,
                   bp.isvendor, bp.iscustomer, bp.taxid,
                   COALESCE(g.name,'') AS group_name,
                   bp.actuallifetimevalue,
                   bp.totalopenbalance,
                   bp.updated AS bp_updated
            FROM c_bpartner bp
            LEFT JOIN c_bp_group g ON g.c_bp_group_id = bp.c_bp_group_id
            WHERE bp.isactive='Y' AND bp.value = $1
            LIMIT 1
        ";
        $rs1 = ($pg) ? @pg_query_params($pg, $sql1, array($q_l1)) : false;
        if ($rs1 && ($r = pg_fetch_assoc($rs1))) {
            pg_free_result($rs1);
            $ids      = array((int)$r['c_bpartner_id']);
            $map      = self::_fetchActiveContactsFor($pg, $ids);
            $bpId     = (int)$r['c_bpartner_id'];
            $contacts = isset($map[$bpId]) ? $map[$bpId] : array();
            $gName    = isset($r['group_name']) ? (string)$r['group_name'] : '';
            $outRow   = self::_formatCustomerRow($r, $contacts, $gName);
            return array(
                'ok'                => true,
                'total'             => 1,
                'exact_value_match' => 1,
                'rows'              => array($outRow)
            );
        }
        if ($rs1) pg_free_result($rs1);
        // Ramla vidare till normal sökning om ingen exakt value-träff
    }

    // 2) Bygg OR-delar
    $orParts = array();
    $params  = array();
    $pi = 1;

    // Namn och kontaktpersonsnamn (bara när söktermen innehåller bokstäver,
    // eller är kort/ej telefon/email)
    if ($hasLetters || (!$hasAt && !$hasDigits5)) {
        $orParts[] = "(bp.name ILIKE $".$pi." OR bp.name2 ILIKE $".($pi+1).")";
        $params[] = $like; $params[] = $like; $pi += 2;

        // Kontaktpersoners fullnamn
        $orParts[] = "EXISTS (
            SELECT 1 FROM ad_user u
            WHERE u.c_bpartner_id = bp.c_bpartner_id
              AND u.isactive='Y'
              AND ( (COALESCE(u.firstname,'') || ' ' || COALESCE(u.lastname,'')) ILIKE $".$pi." )
        )";
        $params[] = $like; $pi++;
    }

    // Taxid / org.nr / personnummer - ALLTID söka på detta, oavsett format
    // FIX: var tidigare inlåst i hasLetters-blocket ovan ? missade "556741-5772"
    // Söker på tre sätt:
    //   a) Originalterm mot taxid som den är         ? "556741-5772" hittar "556741-5772"
    //   b) Normaliserad term mot normaliserat taxid  ? "5567415772"  hittar "556741-5772"
    //   c) Normaliserad term mot normaliserat taxid  ? "556741-5772" hittar "5567415772"
    if ($taxidClean !== '') {
        $orParts[] = "(
            bp.taxid ILIKE $".$pi."
            OR REPLACE(REPLACE(bp.taxid, '-', ''), ' ', '') ILIKE $".($pi+1)."
        )";
        $params[] = $like;          // $pi    originalterm mot taxid direkt
        $params[] = $taxidCleanLike; // $pi+1  normaliserad term mot normaliserat taxid
        $pi += 2;
    }

    // E-post
    if ($hasAt) {
        $orParts[] = "EXISTS (
            SELECT 1 FROM ad_user u
            WHERE u.c_bpartner_id = bp.c_bpartner_id
              AND u.isactive='Y'
              AND u.email ILIKE $".$pi."
        )";
        $params[] = $like; $pi++;
    }

    // Telefon (jämför siffror mot siffror)
    if ($hasDigits5) {
        $dLike = '%'.$digits.'%';
        $orParts[] = "EXISTS (
            SELECT 1 FROM ad_user u
            WHERE u.c_bpartner_id = bp.c_bpartner_id
              AND u.isactive='Y'\r\n              AND (
                   regexp_replace(COALESCE(u.phone,''),  '[^0-9]', '', 'g') LIKE $".$pi."
                OR regexp_replace(COALESCE(u.phone2,''), '[^0-9]', '', 'g') LIKE $".($pi+1)."
              )
        )";
        $params[] = $dLike; $params[] = $dLike; $pi += 2;
    }

    // Säkerhetsventil: om ingenting aktiverades, returnera tomt
    if (!$orParts) return array('ok'=>true,'total'=>0,'rows'=>array());

    // Bygg WHERE
    $whereSql = "bp.isactive='Y' AND (".implode(' OR ', $orParts).")";

    // Räkna totalt antal träffar
    $sqlCount = "SELECT COUNT(*) AS n FROM c_bpartner bp WHERE $whereSql";
    $rsC = ($pg) ? @pg_query_params($pg, $sqlCount, $params) : false;
    $total = 0;
    if ($rsC) { $rsC && $rr = pg_fetch_assoc($rsC); $total = (int)$rr['n']; pg_free_result($rsC); }

    // Hämta data
    $sqlData = "
        SELECT bp.c_bpartner_id,
               bp.value AS bp_value,
               bp.taxid,
               TRIM(
                 COALESCE(bp.name,'') ||
                 CASE
                   WHEN COALESCE(bp.name2,'')<>'' AND lower(trim(bp.name2)) <> lower(trim(bp.name))
                   THEN '  '||bp.name2
                   ELSE ''
                 END
               ) AS fullname,
               bp.isvendor, bp.iscustomer,
               COALESCE(g.name,'') AS group_name,
               bp.actuallifetimevalue,
               bp.totalopenbalance,
               bp.updated AS bp_updated
        FROM c_bpartner bp
        LEFT JOIN c_bp_group g ON g.c_bp_group_id = bp.c_bp_group_id
        WHERE $whereSql
        ORDER BY $orderSql
        LIMIT $limit OFFSET $offset
    ";

    $rsD = ($pg) ? @pg_query_params($pg, $sqlData, $params) : false;
    $rows = array();
    $ids  = array();
    if ($rsD) {
        while ($rsD && $r = pg_fetch_assoc($rsD)) {
            $rows[] = $r;
            $ids[]  = (int)$r['c_bpartner_id'];
        }
        pg_free_result($rsD);
    }
    if (!$rows) return array('ok'=>true,'total'=>$total,'rows'=>array());

    // Hämta aktiva kontakter för alla träffar i EN laddning
    $map = self::_fetchActiveContactsFor($pg, $ids);

    // Formatera till UI-vänliga rader
    $out = array();
    foreach ($rows as $row) {
        $bpId     = (int)$row['c_bpartner_id'];
        $contacts = isset($map[$bpId]) ? $map[$bpId] : array();
        $gName    = isset($row['group_name']) ? (string)$row['group_name'] : '';
        $out[]    = self::_formatCustomerRow($row, $contacts, $gName);
    }

    // --- SAFETY NET: om total > 0 men rows blev tomt, enkel reservfråga ---
    if ($total > 0 && empty($out)) {
        $sqlSimple = "
            SELECT bp.c_bpartner_id,
                   bp.value AS bp_value,
                   TRIM(
                     COALESCE(bp.name,'') ||
                     CASE
                       WHEN COALESCE(bp.name2,'')<>'' AND lower(trim(bp.name2)) <> lower(trim(bp.name))
                       THEN '  '||bp.name2
                       ELSE ''
                     END
                   ) AS fullname,
                   bp.isvendor, bp.iscustomer,
                   bp.taxid,
                   COALESCE(g.name,'') AS group_name,
                   bp.actuallifetimevalue,
                   bp.totalopenbalance,
                   bp.updated AS bp_updated
            FROM c_bpartner bp
            LEFT JOIN c_bp_group g ON g.c_bp_group_id = bp.c_bp_group_id
            WHERE bp.isactive='Y'
              AND (
                   bp.name  ILIKE $1
                OR bp.name2 ILIKE $2
                OR bp.value ILIKE $3
                OR bp.taxid ILIKE $4
                OR REPLACE(REPLACE(bp.taxid,'-',''),' ','') ILIKE $5
                OR EXISTS (
                     SELECT 1 FROM ad_user u
                     WHERE u.c_bpartner_id = bp.c_bpartner_id
                       AND u.isactive='Y'
                       AND (
                            ( (COALESCE(u.firstname,'') || ' ' || COALESCE(u.lastname,'')) ILIKE $6 )
                         OR u.email ILIKE $7
                       )
                   )
              )
            ORDER BY bp.updated DESC, UPPER(bp.name) ASC, bp.c_bpartner_id
            LIMIT 50
        ";

        $rsS = ($pg) ? @pg_query_params($pg, $sqlSimple, array(
            $like,           // $1 namn
            $like,           // $2 namn2
            $like,           // $3 value
            $like,           // $4 taxid original
            $taxidCleanLike, // $5 taxid normaliserat
            $like,           // $6 kontaktnamn
            $like            // $7 email
        )) : false;

        $rowsS = array(); $idsS = array();
        if ($rsS) {
            while ($rsS && $r = pg_fetch_assoc($rsS)) {
                $rowsS[] = $r; $idsS[] = (int)$r['c_bpartner_id'];
            }
            pg_free_result($rsS);
        }

        if ($rowsS) {
            $mapS = self::_fetchActiveContactsFor($pg, $idsS);
            $out = array();
            foreach ($rowsS as $rowS) {
                $bpId     = (int)$rowS['c_bpartner_id'];
                $contacts = isset($mapS[$bpId]) ? $mapS[$bpId] : array();
                $gName    = isset($rowS['group_name']) ? (string)$rowS['group_name'] : '';
                $out[]    = self::_formatCustomerRow($rowS, $contacts, $gName);
            }
        }
    }

    return array('ok'=>true,'total'=>$total,'rows'=>$out);
}

/* ===== Helpers ===== */

// Hämtar aktiva kontakter för en lista av BP-ID:n och returnerar map: bp_id => [ {fullname,email,phone,mobile}, ... ]
private static function _fetchActiveContactsFor($pg, $ids){
    $ids = array_values(array_unique(array_map('intval', $ids)));
    if (!$ids) return array();

    // Bygg en säker IN-lista (endast heltal)
    $in = implode(',', $ids);

    $sql = "
        SELECT u.c_bpartner_id,
               TRIM(COALESCE(u.firstname,'') || ' ' || COALESCE(u.lastname,'')) AS fullname,
               u.email, u.phone, u.phone2
        FROM ad_user u
        WHERE u.isactive='Y' AND u.c_bpartner_id IN ($in)
        ORDER BY UPPER(u.lastname), UPPER(u.firstname)
    ";
    $rs = ($pg) ? @pg_query($pg, $sql) : false;
    $map = array();
    if ($rs) {
        while ($rs && $r = pg_fetch_assoc($rs)) {
            $bpId = (int)$r['c_bpartner_id'];
            if (!isset($map[$bpId])) $map[$bpId] = array();
            $map[$bpId][] = array(
                'fullname' => $r['fullname'],
                'email'    => $r['email'],
                'phone'    => $r['phone'],
                'mobile'   => $r['phone2'],
            );
        }
        pg_free_result($rs);
    }
    return $map;
}

// Formaterar en rad till ditt UI (namn + tre kolumner: Kontakter / E-post / Telefon)
public static function _formatCustomerRow($bpRow, $contacts, $groupName = ''){
    $name  = isset($bpRow['fullname']) ? $bpRow['fullname'] : '';
    $value = isset($bpRow['bp_value']) ? $bpRow['bp_value'] : '';
    $bpId  = (int)$bpRow['c_bpartner_id'];
	$upd = isset($bpRow['bp_updated']) ? trim((string)$bpRow['bp_updated']) : '';

    $names = array(); $emails = array(); $phones = array();
    foreach ($contacts as $u) {
        if (!empty($u['fullname'])) $names[] = $u['fullname'];
        if (!empty($u['email']))    $emails[] = $u['email'];
        if (!empty($u['phone']))    $phones[] = $u['phone'];
        if (!empty($u['mobile']))   $phones[] = $u['mobile'];
    }

    $badgeHtml = self::_renderBpGroupBadge($groupName); // tom sträng för Privatkund

    // Numeric-fält (för omsättning + öppen balans)
    $ltv  = isset($bpRow['actuallifetimevalue']) ? $bpRow['actuallifetimevalue'] : '0';
    $obal = isset($bpRow['totalopenbalance'])    ? $bpRow['totalopenbalance']    : '0';

    return array(
        'bp_id'               => $bpId,
        'bp_value'            => $value,
        'name'                => $name,
        'group_name'          => (string)$groupName,
        'group_badge'         => $badgeHtml,
        'upddate'             => $upd,

        'actuallifetimevalue' => (string)$ltv,
        'totalopenbalance'    => (string)$obal,

        'isvendor'            => (isset($bpRow['isvendor'])   && strtoupper($bpRow['isvendor'])   === 'Y') ? 1 : 0,
        'iscustomer'          => (isset($bpRow['iscustomer']) && strtoupper($bpRow['iscustomer']) === 'Y') ? 1 : 0,
        'contacts'            => array_values(array_unique($names)),
        'emails'              => array_values(array_unique($emails)),
        'phones'              => array_values(array_unique($phones)),
    );
}



public static function getCustomerDetailsAD($bp_id){
    $cn = Db::getConnectionAD(false);
    if (function_exists('pg_set_client_encoding')) { @pg_set_client_encoding($cn, 'UTF8'); }
    @pg_query($cn, "SET client_encoding TO UTF8");

    $bp_id = (int)$bp_id;
    if ($bp_id <= 0) return array('ok'=>false,'msg'=>'bad_id');

    // Basinfo
    $r0 = ($cn) ? @pg_query_params($cn, "
        SELECT
          bp.c_bpartner_id AS bp_id,
          bp.name,
          bp.name2,
          bp.value              AS bp_value,
          bp.taxid,
          bp.iscustomer,
          bp.isvendor,
          bp.salesrep_id,
          bp.url                AS supplier_url,
          bp.usernamevendor     AS supplier_user,
          bp.passwordvendor     AS supplier_pass,
          bp.actuallifetimevalue,
          bp.totalopenbalance,
          COALESCE(grp.name,'')  AS group_name,
          COALESCE(grp.value,'') AS group_value
        FROM c_bpartner bp
        LEFT JOIN c_bp_group grp ON grp.c_bp_group_id = bp.c_bp_group_id
        WHERE bp.c_bpartner_id = $1
        LIMIT 1
    ", array($bp_id)) : false;
    if (!$r0 || !pg_num_rows($r0)) return array('ok'=>false,'msg'=>'not_found');
    $bp = $r0 ? pg_fetch_assoc($r0) : null;

    // Adress  enkel/robust
    $rA = ($cn) ? @pg_query_params($cn, "
        SELECT
          bl.c_bpartner_location_id,
          l.address1, l.address2, l.postal, l.city,
          COALESCE(co.name,'') AS country
        FROM c_bpartner_location bl
        JOIN c_location l ON l.c_location_id = bl.c_location_id
        LEFT JOIN c_country co ON co.c_country_id = l.c_country_id
        WHERE bl.c_bpartner_id = $1
        ORDER BY bl.created DESC
        LIMIT 1
    ", array($bp_id)) : false;
    $addr = ($rA && pg_num_rows($rA)) ? pg_fetch_assoc($rA) : null;

    // Kontakter  ENDAST aktiva
    $rU = ($cn) ? @pg_query_params($cn, "
        SELECT
          u.ad_user_id,
          NULLIF(trim(COALESCE(u.firstname,'')||' '||COALESCE(u.lastname,'')),'') AS fullname,
          NULLIF(u.email,'' ) AS email,
          NULLIF(u.phone,'' ) AS phone,
          NULLIF(u.phone2,'' ) AS mobile
        FROM ad_user u
        WHERE u.c_bpartner_id = $1
          AND u.isactive = 'Y'
        ORDER BY COALESCE(u.firstname,''), COALESCE(u.lastname,'')
        LIMIT 200
    ", array($bp_id)) : false;
    $contacts = array();
    if ($rU) while ($rU && $u = pg_fetch_assoc($rU)) $contacts[] = $u;
    $primary = $contacts ? $contacts[0] : null;

    // Ansvarig
    $owner = null;
    if (!empty($bp['salesrep_id'])) {
        $rO = ($cn) ? @pg_query_params($cn, "
            SELECT ad_user_id,
                   NULLIF(trim(COALESCE(firstname,'')||' '||COALESCE(lastname,'')),'') AS fullname,
                   NULLIF(email,'') AS email
            FROM ad_user
            WHERE ad_user_id = $1
            LIMIT 1
        ", array((int)$bp['salesrep_id'])) : false;
        if ($rO && pg_num_rows($rO)) $rO && $owner = pg_fetch_assoc($rO);
    }

    // --- Senaste ordrar (nyaste först, 8 st) ---
    $orders = array();
	$rOrd = ($cn) ? @pg_query_params($cn, "
		SELECT
		  o.c_order_id,
		  o.documentno,
		  o.dateordered,
		  o.totallines,
		  o.issotrx,
		  o.c_doctypetarget_id
		FROM c_order o
		WHERE (o.c_bpartner_id = $1 OR COALESCE(o.bill_bpartner_id,0) = $1)
		  AND o.docstatus NOT IN ('VO','RE')
		ORDER BY o.dateordered DESC NULLS LAST, o.c_order_id DESC
		LIMIT 8
	", array($bp_id)) : false;
    if ($rOrd) while ($rOrd && $o = pg_fetch_assoc($rOrd)) $orders[] = $o;

    return array(
        'ok'       => true,
        'bp'       => $bp,
        'address'  => $addr,
        'primary'  => $primary,
        'contacts' => $contacts,
        'owner'    => $owner,
        'orders'   => $orders
    );
}

public static function renderCustomerDetailsAD($bp_id){
    // UTF-8-safe escape
    $h = function($s){
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    };

    $dash = '&ndash;';

    $d = self::getCustomerDetailsAD($bp_id);
    if (!$d['ok']) return '<div style="padding:12px;color:#b00;">Kunde inte h&auml;mta kund/leverant&ouml;r.</div>';

    $bp   = $d['bp'];
    $addr = $d['address'];
    $prim = $d['primary'];
    $own  = $d['owner'];

    // ---------- Namn (undvik "A  A") ----------
    $nameRaw  = isset($bp['name'])  ? (string)$bp['name']  : '';
    $name2Raw = isset($bp['name2']) ? (string)$bp['name2'] : '';

    $name = $h($nameRaw);
    $showName2 = false;
    if (trim($name2Raw) !== '') {
        $showName2 = (strcasecmp(trim($name2Raw), trim($nameRaw)) !== 0);
    }
    $name2 = $showName2 ? ' &ndash; '.$h($name2Raw) : '';

    $taxid = (!empty($bp['taxid'])) ? ' ('.$h($bp['taxid']).')' : '';
    $num   = (!empty($bp['bp_value'])) ? $h($bp['bp_value']) : '';

    // ---------- Badge som i sökresultatet: baserad på group_name (ej Privatkund) ----------
    $groupName = isset($bp['group_name']) ? trim((string)$bp['group_name']) : '';
    $badgeHtml = '';
    if ($groupName !== '' && stripos($groupName, 'privat') !== 0) {
        // ÅÄÖ -> AAO för slug
        $map  = array('Å'=>'A','Ä'=>'A','Ö'=>'O','å'=>'a','ä'=>'a','ö'=>'o');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/','-', strtr($groupName,$map)),'-'));
        $badgeHtml = '<span class="badge-group badge-group--'.$slug.'">'.$h($groupName).'</span>';
    }

    // ---------- Leverantör? (styr INTE på isvendor, utan på gruppnamn som listan) ----------
    $isSupplierGroup = ($groupName !== '' && stripos($groupName, 'leverant') === 0);

    // ---------- Omsättning + öppen balans ----------
    $turnover = isset($bp['actuallifetimevalue']) ? (float)$bp['actuallifetimevalue'] : 0.0;
    $openBal  = isset($bp['totalopenbalance'])    ? (float)$bp['totalopenbalance']    : 0.0;

    $hasDebt     = (abs($openBal) > 0.00001);
    $hasTurnover = (abs($turnover) > 0.00001);

	$turnoverTxt = number_format($turnover, 0, ',', ' ') . ' kr';
	$openBalTxt  = number_format($openBal, 2, ',', ' ') . ' kr';

    // ---------- Chips ----------
    $chips = array();
    if ($num !== '')       $chips[] = '<span class="copy-chip" data-copy="'.$num.'">Kund-/lev.nr: '.$num.'</span>';
    if (!empty($bp['taxid'])) $chips[] = '<span class="copy-chip" data-copy="'.$h($bp['taxid']).'">Org.nr: '.$h($bp['taxid']).'</span>';
    $chipsHtml = $chips ? '<div class="chips">'.implode('', $chips).'</div>' : '';

    // ---------- Adress ----------
    $addrHtml = $dash;
    if ($addr) {
        $line1 = !empty($addr['address1']) ? $h($addr['address1']) : '';
        $line2 = !empty($addr['address2']) ? '<br>'.$h($addr['address2']) : '';
        $line3 = (!empty($addr['postal']) || !empty($addr['city'])) ? '<br>'.$h(trim($addr['postal'].' '.$addr['city'])) : '';
        $line4 = !empty($addr['country']) ? '<br>'.$h($addr['country']) : '';
        $addrHtml = $line1.$line2.$line3.$line4;
        if (trim(strip_tags($addrHtml)) === '') $addrHtml = $dash;
    }

    // ---------- Primär kontakt ----------
    $primHtml = $dash;
    if ($prim) {
        $nm = !empty($prim['fullname']) ? $h($prim['fullname']) : 'Kontakt';
        $em = !empty($prim['email']) ? ' &lt;'.$h($prim['email']).'&gt;' : '';
        $ph = !empty($prim['phone']) ? '<br>'.$h($prim['phone']) : '';
        $mb = !empty($prim['mobile'])? '<br>'.$h($prim['mobile']) : '';
        $primHtml = $nm.$em.$ph.$mb;
    }

    // ---------- Ansvarig ----------
    $ownerHtml = $dash;
    if ($own && !empty($own['fullname'])) {
        $ownerHtml = $h($own['fullname']) . (!empty($own['email']) ? ' &lt;'.$h($own['email']).'&gt;' : '');
    }

    // ---------- Kontakter ----------
    $rowsContacts = '';
    foreach ($d['contacts'] as $u) {
        $nm = !empty($u['fullname']) ? $h($u['fullname']) : $dash;

        $lines = array();
        if (!empty($u['email']))  $lines[] = $h($u['email']).','; // kommatecken efter e-post
        if (!empty($u['phone']))  $lines[] = $h($u['phone']);
        if (!empty($u['mobile'])) $lines[] = $h($u['mobile']);

        $kontakt = $lines ? implode('<br>', $lines) : $dash;
        $rowsContacts .= '<tr><td>'.$nm.'</td><td class="contact-col">'.$kontakt.'</td></tr>';
    }
    if ($rowsContacts === '') $rowsContacts = '<tr><td colspan="2">Inga kontakter.</td></tr>';

    // ---------- Ordrar (med Typ) ----------
    $rowsOrders = '';
    if (!empty($d['orders'])) {
        foreach ($d['orders'] as $o) {
            $od = (!empty($o['dateordered']))
                ? $h(date('Y-m-d', strtotime($o['dateordered'])))
                : $dash;

            $docRaw = isset($o['documentno']) ? (string)$o['documentno'] : '';
            $doc    = ($docRaw !== '') ? $h($docRaw) : $dash;

			$dt  = isset($o['c_doctypetarget_id']) ? (int)$o['c_doctypetarget_id'] : 0;
			$so  = isset($o['issotrx']) ? strtoupper(trim((string)$o['issotrx'])) : '';

			$typLabel = $dash;
			$typClass = 'order-type';

			// 1) Inköp identifieras alltid på issotrx
			if ($so !== 'Y') {
				$typLabel = 'Ink&ouml;p';
			} else {
				// 2) Sales: styr på doctype-target enligt din policy
				if ($dt === 1000030) {
					$typLabel = 'F&ouml;rs&auml;ljning';
				} elseif ($dt === 1000027) {
					$typLabel = 'Offert';
					$typClass .= ' order-type--quote';
				} elseif ($dt === 1000026) {
					$typLabel = 'Offert, binder lager';
					$typClass .= ' order-type--quote-res';
				} else {
					$typLabel = 'F&ouml;rs&auml;ljning';
				}
			}

			$typHtml = '<span class="'.$typClass.'">'.$typLabel.'</span>';

            $val = $dash;
            if (isset($o['totallines']) && $o['totallines'] !== '' && is_numeric($o['totallines'])) {
                $val = number_format((float)$o['totallines'], 0, ',', ' ') . ' kr';
            }

            $link = $dash;
            if ($docRaw !== '') {
                $url  = '/search_dispatch.php?mode=order&page=1&q='.rawurlencode($docRaw);
                $link = '<a href="'.$url.'" target="_blank" rel="noopener">'.$doc.'</a>';
            }

            $rowsOrders .= '<tr>'
                         .   '<td>'.$od.'</td>'
                         .   '<td>'.$link.'</td>'
                         .   '<td>'.$typHtml.'</td>'
                         .   '<td class="text-right">'.$val.'</td>'
                         . '</tr>';
        }
    }
    if ($rowsOrders === '') $rowsOrders = '<tr><td colspan="4">Inga ordrar funna.</td></tr>';

    // ---------- CSS ----------
    $css = '<style>
      .drawer-panel{font-size:13px;line-height:1.4;color:#111}
      .drawer-panel h2{font-size:18px;margin:0;line-height:1.2}
      .chips{display:flex;gap:8px;flex-wrap:wrap;margin:8px 0}
      .copy-chip{cursor:pointer}
      .section-title{font-weight:700;font-size:13px;margin:12px 0 6px}

      /* Tabeller */
      .drawer-panel table.table-list{width:100%;border-collapse:collapse;font-size:13px}
      .drawer-panel table.table-list th,
      .drawer-panel table.table-list td{padding:8px 10px;border-bottom:1px solid #e5e7eb;vertical-align:middle}
      .drawer-panel table.table-list thead th{background:#d1f2f0;text-align:left;font-weight:700}
      .drawer-panel .table-list .text-right{text-align:right}

      table.drawer-contacts td.contact-col,
      table.drawer-contacts td:nth-child(2){
        white-space: normal !important;
        text-align: left !important;
        overflow: visible !important;
        text-overflow: clip !important;
        word-break: break-word;
        overflow-wrap: anywhere;
      }
      table.drawer-contacts td.contact-col { line-height: 1.35; }

      /* Badge exakt som listan */
      .badge-group{display:inline-block;padding:2px 8px;border-radius:999px;border:1px solid #e5e7eb;background:#eef2ff;font-size:12px;font-weight:700;color:#1f2937;vertical-align:middle}
      .badge-group--leverantor{background:#fff7ed;border-color:#fed7aa}
      .badge-group--foretagskund{background:#ecfeff;border-color:#a5f3fc}
      .badge-group--aterforsaljare{background:#f0fdf4;border-color:#bbf7d0}
      .badge-group--konsult{background:#fef2f2;border-color:#fecaca}
      .badge-group--anstalld{background:#f5f5f5;border-color:#e5e7eb}

		/* Omsättning / balans (tight, sida-vid-sida) */
		.kpi-row{
		  display:grid;
		  grid-template-columns: 1fr 1fr;
		  gap:10px;
		  margin-top:10px;
		}
		.kpi{
		  border:1px solid #e5e7eb;
		  border-radius:10px;
		  background:#fff;
		  padding:10px 12px;
		}
		.kpi .lbl{font-size:12px;color:#6b7280;font-weight:700;margin-bottom:2px}
		.kpi .val{font-size:16px;font-weight:800;letter-spacing:.2px}
		.kpi .val.debt{color:#b91c1c}

      /* Leverantörsbox */
      .supplier-box{border:1px solid #facc15;border-radius:8px;padding:8px 10px;background:#fffbeb;font-size:13px}
      .supplier-box .row{display:flex;gap:8px;margin:2px 0}
      .supplier-box .label{width:110px;font-weight:600;color:#4b5563;text-align:left}
      .supplier-box .value{flex:1;text-align:left;font-size:13px}
      .supplier-box .value a{color:#0b66d6;text-decoration:none}
      .supplier-box .value a:hover{text-decoration:underline}
      .supplier-box .value .copy-chip{font-size:13px}

		.order-type{white-space:nowrap}
		.order-type--quote{color:#2563eb}       /* blå */
		.order-type--quote-res{color:#b91c1c}   /* röd */

    </style>';

    // ---------- HTML ----------
    $html  = $css;
    $html .= '<div class="drawer-panel" style="padding:14px">';

    // Header: titel + badge till höger (som listan)
    $html .= '<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:6px">';
    $html .=   '<div><h2>'.$name.'</h2></div>';
    $html .=   '<div style="margin-top:2px">'.($badgeHtml !== '' ? $badgeHtml : '').'</div>';
    $html .= '</div>';

    $html .= $chipsHtml;

	// KPI: visa bara om det finns data
	if ($hasTurnover || $hasDebt) {

		// Om bara en finns -> 1 kolumn, annars 2 kolumner
		$cols = ($hasTurnover && $hasDebt) ? '1fr 1fr' : '1fr';

		$html .= '<div class="kpi-row" style="grid-template-columns:'.$cols.'">';

		if ($hasTurnover) {
			$html .=   '<div class="kpi">';
			$html .=     '<div class="lbl">Faktiskt livstidsvärde</div>';
			$html .=     '<div class="val'.($hasDebt ? ' debt' : '').'">'.$h($turnoverTxt).'</div>';
			$html .=   '</div>';
		}

		if ($hasDebt) {
			$html .=   '<div class="kpi">';
			$html .=     '<div class="lbl">&Ouml;ppen balans</div>';
			$html .=     '<div class="val debt">'.$h($openBalTxt).'</div>';
			$html .=   '</div>';
		}

		$html .= '</div>';
	}

    // Adress + Primär kontakt
    $html .= '<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:12px">';
    $html .=   '<div><div class="section-title">Adress</div><div>'.$addrHtml.'</div></div>';
    $html .=   '<div><div class="section-title">Prim&auml;r kontakt</div><div>'.$primHtml.'</div></div>';
    $html .= '</div>';

    $html .= '<div style="margin-top:10px"><div class="section-title">Ansvarig</div><div>'.$ownerHtml.'</div></div>';

    // Kontakter
    $html .= '<div style="margin-top:14px"><div class="section-title">Kontakter</div>'
           . '<table class="table-list drawer-contacts">'
           .   '<colgroup><col/><col/></colgroup>'
           .   '<thead><tr><th>Namn</th><th>Kontakt</th></tr></thead>'
           .   '<tbody>'.$rowsContacts.'</tbody>'
           . '</table>'
           . '</div>';

    // Leverantörsinfo  BARA om gruppen är Leverantör (som listan)
    if ($isSupplierGroup) {
        $supName = !empty($bp['name']) ? $bp['name'] : '';
        $supCode = !empty($bp['bp_value']) ? $bp['bp_value'] : '';
        $supUser = !empty($bp['supplier_user']) ? $bp['supplier_user'] : '';
        $supPass = !empty($bp['supplier_pass']) ? $bp['supplier_pass'] : '';
        $supUrl  = !empty($bp['supplier_url'])  ? $bp['supplier_url']  : '';

        $leverantorHtml = $h($supName);
        if ($supCode !== '') {
            $supLink = 'https://admin.cyberphoto.se/supplier.php?supID='.rawurlencode($supCode);
            $leverantorHtml =
                '<a href="'.$supLink.'" target="_blank">'.$h($supName).'</a> '
              . '<span class="copy-chip" data-copy="'.$supCode.'" title="Kopiera leverant&ouml;rens kundnummer">('
              . $h($supCode)
              . ')</span>';
        }

        $userHtml = ($supUser !== '') ? '<span class="copy-chip" data-copy="'.$supUser.'" title="Kopiera anv&auml;ndare">'.$h($supUser).'</span>' : $dash;
        $passHtml = ($supPass !== '') ? '<span class="copy-chip" data-copy="'.$supPass.'" title="Kopiera l&ouml;senord">'.$h($supPass).'</span>' : $dash;
        $urlHtml  = ($supUrl  !== '') ? '<a href="'.$h($supUrl).'" target="_blank">'.$h($supUrl).'</a>' : $dash;
        $buyerName = ($own && !empty($own['fullname'])) ? $h($own['fullname']) : $dash;

        $html .= '<div style="margin-top:14px">'
               .   '<div class="section-title">Leverant&ouml;rsinfo</div>'
               .   '<div class="supplier-box">'
               .     '<div class="row"><div class="label">Leverant&ouml;r:</div><div class="value">'.$leverantorHtml.'</div></div>'
               .     '<div class="row"><div class="label">Anv&auml;ndare:</div><div class="value">'.$userHtml.'</div></div>'
               .     '<div class="row"><div class="label">L&ouml;senord:</div><div class="value">'.$passHtml.'</div></div>'
               .     '<div class="row"><div class="label">Hemsida:</div><div class="value">'.$urlHtml.'</div></div>'
               .     '<div class="row"><div class="label">Ink&ouml;pare:</div><div class="value">'.$buyerName.'</div></div>'
               .   '</div>'
               . '</div>';
    }

    // Ordrar
    $html .= '<div style="margin-top:14px"><div class="section-title">Senaste ordrar</div>'
           . '<table class="table-list">'
           .   '<colgroup>'
           .     '<col style="width:16ch"/>'
           .     '<col/>'
           .     '<col style="width:14ch"/>'
           .     '<col style="width:14ch"/>'
           .   '</colgroup>'
           .   '<thead><tr><th>Datum</th><th>Order</th><th>Typ</th><th class="text-right">V&auml;rde</th></tr></thead>'
           .   '<tbody>'.$rowsOrders.'</tbody>'
           . '</table>'
			. '<div style="margin-top:8px;text-align:right;">'
			.   '<a href="/customer_orders.php?bp_id='.(int)$bp_id.'" target="_blank" rel="noopener">'
			.     'Se kundens alla ordrar'
			.   '</a>'
			. '</div>';

    $html .= '</div>'; // drawer-panel
    return $html;
}

// Normaliserar till slug: "återförsäljare" -> "aterforsaljare"
private static function _slug($s){
    $s = (string)$s;
    $map = array('Å'=>'A','Ä'=>'A','Ö'=>'O','å'=>'a','ä'=>'a','ö'=>'o');
    $s2 = strtr($s, $map);
    $s2 = preg_replace('/[^A-Za-z0-9]+/', '-', $s2);
    $s2 = trim($s2, '-');
    return strtolower($s2);
}

// Renderar en badge för affärspartnergrupp (tom sträng för Privatkund / tomt namn)
private static function _renderBpGroupBadge($groupName){
    $g = trim((string)$groupName);
    if ($g === '') return '';
    // Filtrera bort Privatkund (visa ingen badge)
    if (stripos($g, 'privat') === 0) return '';
    $slug = self::_slug($g);
    return '<span class="badge badge-group badge-group--'.$slug.'">'.$g.'</span>';
}

// Hämtar gruppnamn för mängd BP-ID (returnerar map bp_id => group_name)
private static function _fetchGroupsFor($pg, $ids){
    $ids = array_values(array_unique(array_map('intval', (array)$ids)));
    if (!$ids) return array();
    $in = implode(',', $ids);
    $sql = "
        SELECT bp.c_bpartner_id, COALESCE(g.name,'') AS group_name
        FROM c_bpartner bp
        LEFT JOIN c_bp_group g ON g.c_bp_group_id = bp.c_bp_group_id
        WHERE bp.c_bpartner_id IN ($in)
    ";
    $rs = ($pg) ? @pg_query($pg, $sql) : false;
    $out = array();
    if ($rs) {
        while ($rs && $r = pg_fetch_assoc($rs)) {
            $out[(int)$r['c_bpartner_id']] = (string)$r['group_name'];
        }
        pg_free_result($rs);
    }
    return $out;
}

// renderOrderDetailsAD - OFÖRÄNDRAD från v43 (kopierar in hela funktionen nedan)
public static function renderOrderDetailsAD($orderNo)
{
    $orderNo = trim($orderNo);
    if ($orderNo === '') {
        return '<div class="dw-wrap"><p>Ingen order angiven.</p></div>';
    }

    $pg = Db::getConnectionAD(false);
    if (!$pg) {
        return '<div class="dw-wrap"><p>Kunde inte ansluta till ADempiere-databasen.</p></div>';
    }

    if ($pg) { @pg_set_client_encoding($pg, "UTF8"); }

    $eh = function($s){
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    };

    // ----- SQL: Hämta huvudinfo - INKLUDERAR c_location_id för adressjämförelse -----
    $sqlOrder = "
        SELECT 
            o.c_order_id,
            o.issotrx,
            o.c_doctypetarget_id,
            o.created,
            o.documentno,
            o.customercomment,
            o.ip_address,
            o.totallines,
            o.deliveryviarule,
            o.order_url,
            o.external_reference,
            o.docstatus,

            TRIM(COALESCE(ad.firstname,'') || ' ' || COALESCE(ad.lastname,'')) AS salesrep_name,
            ad.value AS salesrep_value,

            -- Leveransadress
            bp.c_bpartner_id AS ship_bp_id,
            bp.name  AS ship_name,
            bp.name2 AS ship_name2,
            bp.value AS ship_bp_value,
            loc.c_location_id AS ship_location_id,
            loc.address1 AS ship_address1,
            loc.address2 AS ship_address2,
            loc.postal   AS ship_postal,
            loc.city     AS ship_city,
            contrl.name  AS ship_country,

            -- Fakturaadress
            bp2.c_bpartner_id AS bill_bp_id,
            bp2.name  AS bill_name,
            bp2.name2 AS bill_name2,
            bp2.value AS bill_bp_value,
            loc2.c_location_id AS bill_location_id,
            loc2.address1 AS bill_address1,
            loc2.address2 AS bill_address2,
            loc2.postal   AS bill_postal,
            loc2.city     AS bill_city,
            contrl2.name  AS bill_country,
            con2.c_country_id AS bill_country_id,

            ad2.email  AS customer_email,
            ad2.phone2 AS customer_phone,

            pay.name AS paymentterm,
            sh.name  AS shipper,

            xc.name  AS pclass,
            xc.name2 AS pclass2

        FROM c_order o
            JOIN c_paymentterm pay ON pay.c_paymentterm_id = o.c_paymentterm_id
            LEFT JOIN m_shipper sh ON sh.m_shipper_id = o.m_shipper_id

            JOIN ad_user ad ON ad.ad_user_id = o.salesrep_id

            JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id
            JOIN c_bpartner_location bpl ON bpl.c_bpartner_location_id = o.c_bpartner_location_id
            JOIN c_location loc ON loc.c_location_id = bpl.c_location_id
            JOIN c_country con ON con.c_country_id = loc.c_country_id
            JOIN c_country_trl contrl 
                ON contrl.c_country_id = con.c_country_id 
               AND contrl.ad_language = 'sv_SE'

            JOIN c_bpartner bp2 ON bp2.c_bpartner_id = o.bill_bpartner_id
            JOIN c_bpartner_location bpl2 ON bpl2.c_bpartner_location_id = o.bill_location_id
            JOIN c_location loc2 ON loc2.c_location_id = bpl2.c_location_id
            JOIN c_country con2 ON con2.c_country_id = loc2.c_country_id
            JOIN c_country_trl contrl2 
                ON contrl2.c_country_id = con2.c_country_id 
               AND contrl2.ad_language = 'sv_SE'

            LEFT JOIN ad_user ad2 ON ad2.c_bpartner_id = o.bill_bpartner_id

            LEFT JOIN xc_kreditor_pclass xc ON xc.xc_kreditor_pclass_id = o.xc_kreditor_pclass_id

        WHERE o.documentno = $1
        LIMIT 1
    ";

    $resOrder = ($pg) ? @pg_query_params($pg, $sqlOrder, array($orderNo)) : false;
    if (!$resOrder || pg_num_rows($resOrder) === 0) {
        if ($resOrder) { pg_free_result($resOrder); }
        return '<div class="dw-wrap"><p>Ingen order hittades för ordernummer ' . $eh($orderNo) . '.</p></div>';
    }

    $order = $resOrder ? pg_fetch_assoc($resOrder) : null;
    pg_free_result($resOrder);

    $salesrep = trim((string)$order['salesrep_name']);
    if ($salesrep === '') {
        $salesrep = (string)$order['salesrep_value'];
    }

    // ----- Hämta orderrader -----
    $sqlLines = "
        SELECT 
            p.value                       AS product_value,
            p.m_product_id                AS m_product_id,
            COALESCE(pt.name, p.name)     AS product_name,
            m.name                        AS manufacturer_name,
            col.qtyordered,
            col.qtyreserved,
            col.qtyallocated,
            col.qtydelivered,
            col.qtyinvoiced,
            col.description               AS note,
            col.packey,
            col.linenetamt,
            col.discount,
            col.line,
            col.datepromised,
            col.datepromisedprecision
        FROM c_orderline col
            LEFT JOIN m_product p            ON col.m_product_id = p.m_product_id
            LEFT JOIN m_product_trl pt       ON pt.m_product_id = p.m_product_id
                                             AND pt.ad_language = 'sv_SE'
            LEFT JOIN xc_manufacturer m      ON m.xc_manufacturer_id = p.xc_manufacturer_id
        WHERE col.c_order_id = $1
        ORDER BY col.line ASC
    ";

    $resLines = ($pg) ? @pg_query_params($pg, $sqlLines, array((int)$order['c_order_id'])) : false;
    $lines = array();
    if ($resLines) {
        while ($resLines && $row = pg_fetch_assoc($resLines)) {
            $lines[] = $row;
        }
        pg_free_result($resLines);
    }

    $created = $order['created'];
    if (strlen($created) > 19) {
        $created = substr($created, 0, 19);
    }

    $totalNet = number_format((float)$order['totallines'], 1, ',', ' ');

    $pclassLabel = '';
    if (!empty($order['pclass']) || !empty($order['pclass2'])) {
        $pclassLabel = trim($order['pclass'] . ' ' . $order['pclass2']);
    }

    // ----- Orderstatus -----
    $docStatus = strtoupper(trim((string)$order['docstatus']));
    $statusLabel = 'Bearbetas';
    $statusClass = 'badge-status badge-status-process';
    $isCancelled = in_array($docStatus, array('VO', 'CL'));

    $allDelivered      = false;
    $anyDelivered      = false;
    $anyNotDelivered   = false;

    if ($lines) {
        $allDelivered = true;
        foreach ($lines as $ln) {
            $art = trim((string)$ln['product_value']);
            if ($art === '') continue;

            $qo = (float)$ln['qtyordered'];
            $qd = (float)$ln['qtydelivered'];

            if ($qo <= 0) continue;

            if ($qd + 0.0001 >= $qo) {
                $anyDelivered = true;
            } else {
                $allDelivered    = false;
                $anyNotDelivered = true;
                if ($qd > 0) {
                    $anyDelivered = true;
                }
            }
        }
    }

    if ($isCancelled) {
        $statusLabel = 'Annullerad';
        $statusClass = 'badge-status badge-status-cancel';
    } elseif ($allDelivered && $anyDelivered) {
        $statusLabel = 'Skickad';
        $statusClass = 'badge-status badge-status-sent';
    } elseif ($anyDelivered && $anyNotDelivered) {
        $statusLabel = 'Dellevererad';
        $statusClass = 'badge-status badge-status-partial';
    }

    $offerBadge = '';
    $issotrx = isset($order['issotrx']) ? strtoupper(trim((string)$order['issotrx'])) : '';
    $dt      = isset($order['c_doctypetarget_id']) ? (int)$order['c_doctypetarget_id'] : 0;
	$isQuote = ($issotrx === 'Y' && ($dt === 1000027 || $dt === 1000026));

    if ($issotrx === 'Y') {
        if ($dt === 1000027) {
            $offerBadge = '<span class="badge badge--quote">Offert</span>';
        } elseif ($dt === 1000026) {
            $offerBadge = '<span class="badge badge--quote-res">Offert, binder lager</span>';
        }
    }

    // === KRITISK: Jämför c_location_id (fysiska adresser) ===
    $shipLocId = isset($order['ship_location_id']) ? (int)$order['ship_location_id'] : 0;
    $billLocId = isset($order['bill_location_id']) ? (int)$order['bill_location_id'] : 0;
    $sameAddress = ($shipLocId > 0 && $shipLocId === $billLocId);

    // Interna kommentarer (hämtas tidigt för att kunna visa ikon i titelraden)
    $chatRows = array();
    $orderDocNoInt = (int)$order['c_order_id'];
    $sqlChat = "
        SELECT
            ch.created,
            ad.name,
            ch.characterdata
        FROM cm_chat c
            JOIN cm_chatentry ch ON ch.cm_chat_id = c.cm_chat_id
            JOIN ad_user ad      ON ad.ad_user_id = ch.updatedby
        WHERE c.ad_table_id = 259
          AND c.record_id   = \$1
        ORDER BY ch.created ASC
    ";
    $resChat = ($pg) ? @pg_query_params($pg, $sqlChat, array($orderDocNoInt)) : false;
    if ($resChat) {
        while ($resChat && $row = pg_fetch_assoc($resChat)) {
            $chatRows[] = $row;
        }
        pg_free_result($resChat);
    }

    // ----- CSS -----
    $html  = '<style>
    .dw-table-orderlines{width:100%;border-collapse:separate;border-spacing:0;table-layout:auto}
    .dw-table-orderlines th,.dw-table-orderlines td{padding:6px 10px}
    .dw-table-orderlines thead th{background:#d1f2f0;font-weight:700}
    .dw-table-orderlines tr.ol-main,.dw-table-orderlines tr.ol-sub{background:#ffffff !important}
    .dw-table-orderlines tr.ol-main.is-delivered{background:#bbf7d0 !important}
    .dw-table-orderlines tr.ol-sub.is-delivered{background:#dcfce7 !important}
    .dw-table-orderlines tr.ol-sub td{font-size:11px;color:#444;border-top:0;padding-top:0;padding-bottom:8px}
    .ol-eta{margin-top:6px;padding-top:6px;font-size:12px;font-weight:700;color:#111827;border-top:1px solid #e5e7eb}
    .ol-eta span{display:inline-block;padding:2px 10px;border-radius:999px;font-weight:800;letter-spacing:.2px;background:#e0f2fe;border:1px solid #38bdf8;color:#075985}
    .dw-table-orderlines tr.ol-sep td{padding:0;height:12px;background:#ffffff;border-bottom:1px solid #cbcbcb}
    .dw-table-orderlines tr.ol-main:hover,.dw-table-orderlines tr.ol-sub:hover{background:#f3f4f6 !important}
    .dw-table-orderlines tr.ol-main.is-delivered:hover{background:#a7f3d0 !important}
    .dw-table-orderlines tr.ol-sub.is-delivered:hover{background:#d1fae5 !important}
    .order-chat-header{font-weight:700;margin-bottom:4px}
    .order-chat-entry{margin-bottom:10px;padding:8px 10px;background:#f9fafb;border-radius:6px;border:1px solid #e5e7eb}
    .dw-chat-btn{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border:1px solid #fed7aa;border-radius:6px;margin-left:6px;color:#ea580c;background:#fff7ed;text-decoration:none;vertical-align:middle}
    .dw-chat-btn:hover{background:#ffedd5;border-color:#fb923c}
    .order-chat-meta{font-size:12px;color:#6b7280;margin-bottom:4px}
    .order-chat-meta i{font-style:italic}
    .order-chat-body{font-size:13px;color:#111;line-height:1.5}
    .order-customer-link{margin-top:10px;font-size:13px}
    .order-customer-link a{display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;border:1px solid #1d4ed8;background:#eff6ff;color:#1d4ed8;text-decoration:none;font-weight:700}
    .order-customer-link a:hover{background:#dbeafe;border-color:#1d4ed8}
    .dw-address-cols{display:block;margin-top:8px}
    .dw-address-card{border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;padding:8px 10px;margin-bottom:10px}
    .dw-address-title{font-weight:700;margin-bottom:4px}
    .dw-section-compact{margin-bottom:14px}
    .order-status-wrap{display:flex;gap:8px;align-items:center;justify-content:flex-end;flex-wrap:wrap;margin-left:8px;margin-top:4px;margin-bottom:2px}
    .dw-title-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:2px}
    .dw-title{margin:0}
    .badge-status{display:inline-block;padding:2px 10px;border-radius:999px;border:1px solid #d1d5db;font-size:12px;font-weight:700}
    .badge-status-process{background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8}
    .badge-status-sent{background:#ecfdf5;border-color:#6ee7b7;color:#065f46}
    .badge-status-partial{background:#fff7ed;border-color:#fed7aa;color:#9a3412}
    .badge-status-cancel{background:#fee2e2;border-color:#fecaca;color:#991b1b}
    .copy-chip{cursor:pointer}
    .copy-chip:hover{background:#f3f4f6;border-radius:3px}
    .copy-chip.copied{background:#ecfdf5;box-shadow:inset 0 0 0 1px #34d399;border-radius:3px}
    .discount-badge{display:inline-block;padding:0 4px;border-radius:999px;background:#f97316;color:#ffffff;font-weight:bold}
    .ord-web-link{display:inline-block;margin-right:4px}
    .ord-web-link svg{vertical-align:middle}
    .badge--quote{background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:700}
    .badge--quote-res{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:700}
    .dw-table-orderlines tr.ol-promised td{padding-top:6px;padding-bottom:6px;background:#ffffff}
    .promised-pill{display:inline-block;padding:3px 10px;border-radius:999px;border:1px solid #1d4ed8;background:#eff6ff;color:#1d4ed8;font-weight:700;font-size:12px}
    .dw-table-orderlines td.text-right{white-space: nowrap}
    .metadata-section{margin-top:16px;padding-top:12px;border-top:1px solid #e5e7eb}
    .metadata-title{font-weight:700;font-size:14px;margin-bottom:6px}
    .customer-contacts{margin-top:8px;padding-top:8px;border-top:1px solid #e5e7eb;font-size:13px}
    .order-action-links{margin-left:10px}
    .order-action-links a{color:#0b57d0;text-decoration:none;font-weight:600;margin-left:10px}
    .order-action-links a:hover{text-decoration:underline}
    </style>';

    $html .= '<div class="dw-wrap">';
    $html .= '<div class="dw-header">';
    $html .= '<div class="dw-title-row">';
    $orderNoDisp = (string)$order['documentno'];
    $html .= '<h2 class="dw-title">Säljorder '
           . '<span class="copy-chip" data-copy="'.$eh($orderNoDisp).'"'
           . ' title="Kopiera ordernummer">'.$eh($orderNoDisp).'</span>'
           . (!empty($chatRows)
               ? '<a href="#order-chat-section" class="dw-chat-btn"'
               . ' onclick="document.getElementById(\'order-chat-section\').scrollIntoView({behavior:\'smooth\'});return false;"'
               . ' title="Interna kommentarer ('.count($chatRows).' st)">'
               .   '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
               .     '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>'
               .   '</svg>'
               . '</a>'
               : '')
           . '</h2>';
    $html .= '<div class="order-status-wrap">'
          .  '<span class="'.$eh($statusClass).'">'.$eh($statusLabel).'</span>'
          .  $offerBadge
          .  '</div>';
    $html .= '</div>';

    // === Meta-rader UTAN IP-adress (flyttas till metadata-sektion längst ner) ===
    $html .= '<div class="dw-section dw-section-compact">';
    $html .= '  <div class="dw-row"><span class="dw-label">Order skapad:</span> <span class="dw-value"><strong>' . $eh($created) . '</strong></span></div>';
    $html .= '  <div class="dw-row"><span class="dw-label">Skapad av:</span> <span class="dw-value"><strong>' . $eh($salesrep) . '</strong></span></div>';
    $html .= '</div>';
    $html .= '</div>';

    // Betalning / frakt / kredit
    $html .= '<div class="dw-section dw-section-compact">';
    $html .= '  <div class="dw-row"><span class="dw-label">Betalningsvillkor:</span> <span class="dw-value"><strong>' . $eh($order['paymentterm']) . '</strong></span></div>';
    if (!empty($order['shipper'])) {
        $html .= '  <div class="dw-row"><span class="dw-label">Leveranssätt:</span> <span class="dw-value"><strong>' . $eh($order['shipper']) . '</strong></span></div>';
    }
    if ($pclassLabel !== '') {
        $html .= '  <div class="dw-row"><span class="dw-label">Kreditklass:</span> <span class="dw-value"><strong>' . $eh($pclassLabel) . '</strong></span></div>';
    }
    $html .= '</div>';

    // === ADRESSBLOCK: EN eller TVÅ boxar beroende på c_location_id ===
    $html .= '<div class="dw-section"><div class="dw-address-cols">';

    if ($sameAddress) {
        // === SAMMA ADRESS ? EN box ===
        $html .= '<div class="dw-address-card">';
        $html .= '<div class="dw-address-title">Kunden</div><p>';
        $custName = trim((string)$order['ship_name']); 
        $custBp   = trim((string)$order['ship_bp_value']);
        if ($custBp !== '') {
            $custName .= ' (<span class="copy-chip" data-copy="'.$eh($custBp).'" title="Kopiera kundnummer">'.$eh($custBp).'</span>)';
        }
        $html .= $custName . '<br>';
        if (!empty($order['ship_address1'])) $html .= $eh($order['ship_address1']) . '<br>';
        if (!empty($order['ship_address2'])) $html .= $eh($order['ship_address2']) . '<br>';
        $html .= $eh($order['ship_postal'] . ' ' . $order['ship_city']) . '<br>';
        $html .= $eh($order['ship_country']);
        $html .= '</p>';

        // Kontaktuppgifter
        $custEmail = trim((string)$order['customer_email']);
        $custPhone = trim((string)$order['customer_phone']);
        if ($custEmail !== '' || $custPhone !== '') {
            $html .= '<div class="customer-contacts">';
            if ($custEmail !== '') $html .= '<div><strong>E-post:</strong> ' . $eh($custEmail) . '</div>';
            if ($custPhone !== '') $html .= '<div><strong>Telefon:</strong> ' . $eh($custPhone) . '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';

    } else {
        // === OLIKA ADRESSER ? TVÅ boxar ===
        
        // Box 1: Leverans till
        $html .= '<div class="dw-address-card">';
        $html .= '<div class="dw-address-title">Leverans till</div><p>';
        $shipName = trim((string)$order['ship_name']);
        $shipBp   = trim((string)$order['ship_bp_value']);
        if ($shipBp !== '') {
            $shipName .= ' (<span class="copy-chip" data-copy="'.$eh($shipBp).'" title="Kopiera kundnummer">'.$eh($shipBp).'</span>)';
        }
        $html .= $shipName . '<br>';
        if (!empty($order['ship_address1'])) $html .= $eh($order['ship_address1']) . '<br>';
        if (!empty($order['ship_address2'])) $html .= $eh($order['ship_address2']) . '<br>';
        $html .= $eh($order['ship_postal'] . ' ' . $order['ship_city']) . '<br>';
        $html .= $eh($order['ship_country']);
        $html .= '</p></div>';

        // Box 2: Faktura till
        $html .= '<div class="dw-address-card">';
        $html .= '<div class="dw-address-title">Faktura till</div><p>';
        $billName = trim((string)$order['bill_name']);
        $billBp   = trim((string)$order['bill_bp_value']);
        if ($billBp !== '') {
            $billName .= ' (<span class="copy-chip" data-copy="'.$eh($billBp).'" title="Kopiera kundnummer">'.$eh($billBp).'</span>)';
        }
        $html .= $billName . '<br>';
        if (!empty($order['bill_address1'])) $html .= $eh($order['bill_address1']) . '<br>';
        if (!empty($order['bill_address2'])) $html .= $eh($order['bill_address2']) . '<br>';
        $html .= $eh($order['bill_postal'] . ' ' . $order['bill_city']) . '<br>';
        $html .= $eh($order['bill_country']);
        $html .= '</p></div>';

        // Kontaktuppgifter under båda boxarna
        $custEmail = trim((string)$order['customer_email']);
        $custPhone = trim((string)$order['customer_phone']);
        if ($custEmail !== '' || $custPhone !== '') {
            $html .= '<div class="dw-address-card" style="padding-top:6px">';
            $html .= '<div class="customer-contacts" style="margin-top:0;padding-top:0;border-top:0">';
            if ($custEmail !== '') $html .= '<div><strong>E-post:</strong> ' . $eh($custEmail) . '</div>';
            if ($custPhone !== '') $html .= '<div><strong>Telefon:</strong> ' . $eh($custPhone) . '</div>';
            $html .= '</div></div>';
        }
    }

    $html .= '</div></div>';

    // Kundens kommentar
    if (!empty($order['customercomment'])) {
        $html .= '<div class="dw-section">';
        $html .= '<h3 class="dw-h3">Kundens kommentar</h3>';
        $html .= '<p>' . nl2br($eh($order['customercomment'])) . '</p>';
        $html .= '</div>';
    }

    // === ORDERRADER MED BEVAKA/RAPPORTERA-LÄNKAR ===
    $html .= '<div class="dw-section">';
    $html .= '<h3 class="dw-h3">Orderrader</h3>';

    if (!$lines) {
        $html .= '<p>Inga orderrader hittades.</p>';
    } else {
        $html .= '<table class="dw-table dw-table-compact dw-table-orderlines">';
        $html .= '<thead><tr>';
        $html .= '<th>Artnr</th>';
        $html .= '<th>Benämning</th>';
        $html .= '<th class="text-center">Best</th>';
        $html .= '<th class="text-center">Lev</th>';
        $html .= '<th class="text-center">Fakt</th>';
        $html .= '<th class="text-right">Netto</th>';
        $html .= '</tr></thead>';

		foreach ($lines as $line) {
			$qtyOrdered   = (float)$line['qtyordered'];
			$qtyReserved  = (float)$line['qtyreserved'];
			$qtyAllocated = (float)$line['qtyallocated'];
			$qtyDelivered = (float)$line['qtydelivered'];
			$qtyInvoiced  = (float)$line['qtyinvoiced'];

			$isFullyDelivered = ($qtyOrdered > 0 && $qtyDelivered + 0.0001 >= $qtyOrdered);
			$isFullyAllocated = ($qtyOrdered > 0 && $qtyAllocated + 0.0001 >= $qtyOrdered);

			$clsMain = 'ol-main' . ($isFullyDelivered ? ' is-delivered' : '');
			$clsSub  = 'ol-sub'  . ($isFullyDelivered ? ' is-delivered' : '');

			$article = trim((string)$line['product_value']);
			$pid     = isset($line['m_product_id']) ? (int)$line['m_product_id'] : 0;
			$note    = trim((string)$line['note']);
			$isPkg   = (!empty($line['packey'])) ? 'Ja' : 'Nej';

			$allowArticle =
				($article !== '' &&
				 $article !== 'rab' &&
				 $article !== 'invoicefee' &&
				 $article !== 'friforsakring' &&
				 substr($article, 0, 5) !== 'frakt');

			$isRealProduct = ($pid > 0 && $allowArticle);

			$productNameRaw = (string)$line['product_name'];
			$manuName       = trim((string)$line['manufacturer_name']);
			$productDisplay = ($manuName !== '') ? $manuName . ' ' . $productNameRaw : $productNameRaw;

			$textLabel = $productDisplay;
			if ($pid > 0 && $article !== '') {
				$urlAdmin = '/search_dispatch.php?mode=product&page=1&q='
						  . rawurlencode($article) . '&drawer=' . $pid;
				$textLabel = '<a href="'.$eh($urlAdmin).'" target="_blank" rel="noopener">'.$productDisplay.'</a>';
			}

			$iconHtml = '';
			if ($article !== '') {
				$urlWeb = 'https://www.cyberphoto.se/sok?q=' . rawurlencode($article);
				$iconHtml =
					'<a href="'.$eh($urlWeb).'" class="ord-web-link" target="_blank" rel="noopener">'
				  . '<svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true">'
				  . '<circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1.5"></circle>'
				  . '<path d="M2 12h20" fill="none" stroke="currentColor" stroke-width="1.5"></path>'
				  . '<path d="M12 2a15 15 0 0 1 0 20" fill="none" stroke="currentColor" stroke-width="1.5"></path>'
				  . '<path d="M12 2a15 15 0 0 0 0 20" fill="none" stroke="currentColor" stroke-width="1.5"></path>'
				  . '</svg></a>';
			}

			$productLabel = $iconHtml . ' ' . $textLabel;

			$allocDisplay = $qtyAllocated;
			if ($qtyAllocated > 0 && !$isFullyAllocated) {
				$allocDisplay =
					'<span style="padding:0 6px;border-radius:999px;
						   background:#1d95e5;color:#fff;font-weight:700;">'
				  . $qtyAllocated . '</span>';
			} elseif ($isFullyAllocated) {
				$allocDisplay =
					'<span style="padding:0 6px;border-radius:999px;
						   background:#139f18;color:#fff;font-weight:700;">'
				  . $qtyAllocated . '</span>';
			}

			$net = number_format((float)$line['linenetamt'], 1, ',', ' ');
			$rawDisc = ($line['discount'] !== '' && $line['discount'] !== null)
				? (float)$line['discount'] : 0.0;

			if (abs($rawDisc) < 0.001) {
				$disc = '0,0 %';
			} elseif ($rawDisc > 0) {
				$disc = '<span class="discount-badge">'
					  . number_format($rawDisc, 1, ',', ' ') . ' %</span>';
			} else {
				$disc = number_format($rawDisc, 1, ',', ' ') . ' %';
			}

			// === Rad 1 ===
			$html .= '<tr class="'.$clsMain.'">';
			$html .= '<td><span class="copy-chip" data-copy="'.$eh($article).'">'.$eh($article).'</span></td>';
			$html .= '<td>'.$productLabel.'</td>';
			$html .= '<td class="text-center">'.$qtyOrdered.'</td>';
			$html .= '<td class="text-center">'.$qtyDelivered.'</td>';
			$html .= '<td class="text-center">'.$qtyInvoiced.'</td>';
			$html .= '<td class="text-right">'.$net.'</td>';
			$html .= '</tr>';

			// === Bevaka/Rapportera-länkar ===
			$canMonitor = ($allowArticle &&
						   $qtyAllocated < $qtyOrdered &&
						   $qtyDelivered < $qtyOrdered);
			$canFeedback = ($allowArticle &&
							$qtyDelivered < $qtyOrdered);

			$links = array();
			if ($canMonitor) {
				$monUrl = 'https://admin.cyberphoto.se/monitor_articles.php'
						. '?add=yes'
						. '&addArtnr=' . rawurlencode($article)
						. '&addType=3'
						. '&addStoreValue=' . rawurlencode($order['documentno']);
				$links[] =
					'<a href="'.$eh($monUrl).'"'
				  . ' class="popup-link order-monitor-link"'
				  . ' data-popup="monitor_'.$eh($article).'">'
				  . 'Bevaka</a>';
			}
			if ($canFeedback) {
				$fbUrl = 'https://admin.cyberphoto.se/product_feedback.php'
					   . '?popup=1'
					   . '&artnr=' . rawurlencode($article)
					   . '&ordernr=' . rawurlencode($order['documentno']);
				$links[] =
					'<a href="'.$eh($fbUrl).'"'
				  . ' class="popup-link order-feedback-link"'
				  . ' data-popup="feedback_'.$eh($article).'">'
				  . 'Rapportera</a>';
			}

			// === Rad 2 (detaljer + länkar) ===
			$html .= '<tr class="'.$clsSub.'"><td colspan="6">';
			$parts = array(
				'Reserverat: '.$qtyReserved,
				'Allokerat: '.$allocDisplay,
				'Paket: '.$eh($isPkg),
				'Rabatt: '.$disc
			);
			if ($note !== '') $parts[] = 'Notering: '.$eh($note);
			$html .= implode(' &middot; ', $parts);

			if (!empty($links)) {
				$html .= '<span class="order-action-links">' . implode(' ', $links) . '</span>';
			}

			$html .= '</td></tr>';

			// === Beräknad leverans ===
			if (!$isQuote && $isRealProduct && !$isFullyDelivered && !$isFullyAllocated) {
				$prec = strtoupper(trim((string)$line['datepromisedprecision']));
				$dp   = trim((string)$line['datepromised']);
				if (strlen($dp) > 10) $dp = substr($dp, 0, 10);

				$txt = '';
				if ($prec === 'U') {
					$txt = 'Okänt leveransdatum';
				} elseif ($dp !== '') {
					$m = (int)substr($dp,5,2);
					$d = (int)substr($dp,8,2);
					$months = [1=>'januari',2=>'februari',3=>'mars',4=>'april',5=>'maj',6=>'juni',
							   7=>'juli',8=>'augusti',9=>'september',10=>'oktober',11=>'november',12=>'december'];

					if ($prec === 'D') {
						$txt = 'Beräknas in '.$dp;
					} elseif ($prec === 'M') {
						$txt = 'Beräknas in i '.$months[$m];
					} elseif ($prec === 'P') {
						$part = ($d <= 10) ? 'i början av' : (($d <= 20) ? 'i mitten av' : 'i slutet av');
						$txt = 'Beräknas '.$part.' '.$months[$m];
					} elseif ($prec === 'W') {
						$ts = strtotime($dp);
						if ($ts) $txt = 'Beräknas in vecka '.date('W',$ts);
					}
				}

				if ($txt !== '') {
					$html .= '<tr class="ol-promised"><td colspan="6">'
						   . '<span class="promised-pill">'.$eh($txt).'</span>'
						   . '</td></tr>';
				}
			}

			$html .= '<tr class="ol-sep"><td colspan="6"></td></tr>';
		}

        $html .= '</table>';
    }

    $html .= '</div>';

    // Total
    $html .= '<div class="dw-section dw-section-compact">';
    $html .= '<div class="dw-row">';
    $html .= '<span class="dw-label"><strong>Totalt ex moms:</strong></span> ';
    $html .= '<span class="dw-value"><strong>' . $totalNet . ' SEK</strong></span>';
    $html .= '</div>';
    $html .= '</div>';

    // Interna kommentarer
    if (!empty($chatRows)) {
        $html .= '<div id="order-chat-section" class="dw-section">';
        $html .= '<div class="order-chat-header">Interna kommentarer <svg style="vertical-align:middle;color:#ea580c" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></div>';

        foreach ($chatRows as $c) {
            $cCreated = $c['created'];
            if (strlen($cCreated) > 19) {
                $cCreated = substr($cCreated, 0, 19);
            }
            $cUser  = $eh($c['name']);
            $cText  = nl2br($eh($c['characterdata']));

            $html .= '<div class="order-chat-entry">';
            $html .= '  <div class="order-chat-meta">'.$eh($cCreated).' &nbsp;&nbsp; <i>' . $cUser . '</i></div>';
            $html .= '  <div class="order-chat-body">'.$cText.'</div>';
            $html .= '</div>';
        }

        $html .= '</div>';
    }

    // Extern kundlänk
    $extRef = '';
    if (isset($order['external_reference'])) {
        $extRef = trim((string)$order['external_reference']);
    }

    if ($extRef !== '') {
        $urlCustomer = 'https://www.cyberphoto.se/orderstatus?order=' . rawurlencode($extRef);
        $html .= '<div class="dw-section">';
        $html .= '  <div class="order-customer-link">';
        $html .= '    <a href="'.$eh($urlCustomer).'" target="_blank" rel="noopener">Kundlänk</a>';
        $html .= '  </div>';
        $html .= '</div>';
    }

    // === METADATA-SEKTION LÄNGST NER MED IP-ADRESS ===
    if (!empty($order['ip_address'])) {
        $html .= '<div class="metadata-section">';
        $html .= '<div class="metadata-title">Meta-data</div>';
        $html .= '<div>IP-adress: <strong>' . $eh($order['ip_address']) . '</strong></div>';
        $html .= '</div>';
    }

    $html .= '</div>';

    // JavaScript
    $html .= '<script type="text/javascript">
    (function(){
        function copyText(txt, cb){
            try{
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(String(txt||"")).then(function(){ cb && cb(true); }, function(){ cb && cb(false); });
                } else {
                    var ta=document.createElement("textarea");
                    ta.value=String(txt||"");
                    ta.style.position="fixed";
                    ta.style.opacity="0";
                    document.body.appendChild(ta);
                    ta.select();
                    var ok=document.execCommand("copy");
                    document.body.removeChild(ta);
                    cb && cb(ok);
                }
            }catch(e){ cb && cb(false); }
        }

        document.addEventListener("click", function(e){
            var t = e.target;
            if (!t || !t.closest) return;

            var chip = t.closest(".copy-chip");
            if (chip) {
                var txt = chip.getAttribute("data-copy") || "";
                if (!txt) return;
                copyText(txt, function(ok){
                    if (!ok) return;
                    chip.classList.add("copied");
                    setTimeout(function(){ chip.classList.remove("copied"); }, 900);
                });
                return;
            }

            var link = t.closest(".order-monitor-link, .order-feedback-link");
            if (!link) return;

            e.preventDefault();
            var url = link.getAttribute("href") || "";
            if (!url) return;

            var name  = link.classList.contains("order-monitor-link") ? "monitorPopup" : "feedbackPopup";
            var specs = (name === "monitorPopup")
                ? "width=950,height=700,scrollbars=yes,resizable=yes"
                : "width=900,height=650,scrollbars=yes,resizable=yes";

            try { window.open(url, name, specs); } catch(ex) {}
        }, false);

    })();
    </script>';

    return $html;
}


}
?>

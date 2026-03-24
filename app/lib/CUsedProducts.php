<?php
// ============================================================
// CUsedProducts_v1.php
// Klass för att hämta begagnade och demo-produkter
// som är äldre än 90 dagar, har lager > 0 och vars salestart
// har passerats. Sorteras på salestart ASC (äldst överst).
// Exkluderar hyllplatser/artiklar via search_exclude.php
// ============================================================

class CUsedProducts {

    public function getOldProducts($type = 'used', $maxDays = 90) {
    // $type = 'used'  => begagnade produkter (istradein = 'Y')
    // $type = 'demo'  => fyndprodukter/demo (demo_product = 'Y')
    // $maxDays = 90   => produkter som passerat salestart med max 90 dagar (dvs: salestart <= NOW() - 90 dagar)

    $conn = Db::getConnectionAD(false);
    if ($conn) { @pg_set_client_encoding($conn, 'UTF8'); }

    // --- Lagersub (samma mönster som CSearch) ---
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

    // --- Bygg WHERE-villkor ---
    $params = array();
    $i      = 1;
    $wheres = array();

    // Produkttyp-villkor
    if ($type === 'used') {
        $wheres[] = "p.istradein = 'Y'";
    } else {
        // demo_product = 'Y', men INTE istradein (annars hamnar begagnade hit också)
        $wheres[] = "p.demo_product = 'Y'";
        $wheres[] = "p.istradein = 'N'";
    }

    // Endast produkter som ÄR webbutiksprodukter
    $wheres[] = "p.iswebstoreproduct = 'Y'";

    // Måste ha lager > 0
    $wheres[] = "COALESCE(ps.qtyonhand, 0) > 0";

    // salestart måste finnas och ha passerats
    $wheres[] = "p.salestart IS NOT NULL";
    $wheres[] = "p.salestart <= NOW()";

    // salestart passerades för MINST maxDays dagar sedan (skippas i Visa alla-läge)
    if ((int)$maxDays > 0) {
        $wheres[] = "p.salestart <= (NOW() - INTERVAL '1 day' * $" . $i . ")";
        $params[] = (int)$maxDays;
        $i++;
    }

    // ====== Exkludera artiklar / hyllplatser via search_exclude.php ======
    $EXCL_ARTICLES = array();
    $EXCL_LOCATORS = array();
    $EXCL_LOCATOR_REQUIRE_QTY = true;

    $cfgPath = __DIR__ . '/search_exclude.php';
    if (is_file($cfgPath)) {
        include $cfgPath;
    }

    // Exkludera specifika artikelnummer
    if (!empty($EXCL_ARTICLES) && is_array($EXCL_ARTICLES)) {
        $ph = array();
        foreach ($EXCL_ARTICLES as $v) {
            $params[] = (string)$v;
            $ph[]     = '$' . $i;
            $i++;
        }
        if (!empty($ph)) {
            $wheres[] = 'p.value NOT IN (' . implode(',', $ph) . ')';
        }
    }

    // Exkludera specifika hyllplatser
    if (!empty($EXCL_LOCATORS) && is_array($EXCL_LOCATORS)) {
        $ph = array();
        foreach ($EXCL_LOCATORS as $id) {
            $params[] = (int)$id;
            $ph[]     = '$' . $i;
            $i++;
        }
        if (!empty($ph)) {
            // NULL-locator tillåts (produkt utan hyllplats), men svartlistade exkluderas
            $wheres[] = '(p.m_locator_id IS NULL OR p.m_locator_id NOT IN (' . implode(',', $ph) . '))';
        }
    }

    // --- Bygg SQL ---
    $sql = "
        SELECT
            p.m_product_id,
            p.value                                                     AS article,
            TRIM(COALESCE(manu.name, '') || ' ' || COALESCE(p.name, '')) AS product_full,
            p.salestart,
            (NOW()::date - p.salestart::date)                           AS age_days,

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
            )                                                           AS net_price,

            COALESCE(ps.qtyonhand, 0)                                   AS stock_qty,

            COALESCE((
                SELECT t.rate
                  FROM c_tax t
                 WHERE t.c_taxcategory_id = p.c_taxcategory_id
                   AND t.c_country_id = 313
                 ORDER BY t.isdefault DESC NULLS LAST
                 LIMIT 1
            ), 0)                                                        AS tax_rate

        FROM m_product p
        LEFT JOIN xc_manufacturer manu  ON manu.xc_manufacturer_id = p.xc_manufacturer_id
        LEFT JOIN m_product_category pc ON pc.m_product_category_id = p.m_product_category_id
        LEFT JOIN ($stockSub) ps        ON ps.m_product_id = p.m_product_id
        LEFT JOIN m_productprice pp
               ON pp.m_product_id = p.m_product_id
              AND pp.m_pricelist_version_id = 1000000
        WHERE " . implode(' AND ', $wheres) . "
        ORDER BY p.salestart ASC NULLS LAST
    ";

    $rows = array();
    if ($res = ($conn) ? @pg_query_params($conn, $sql, $params) : false) {
        while ($res && $r = pg_fetch_assoc($res)) {
            // Avrunda nettopris
            $r['net_price']  = (float)$r['net_price'];
            $r['stock_qty']  = (int)$r['stock_qty'];
            $r['age_days']   = (int)$r['age_days'];
            $r['tax_rate']   = (float)$r['tax_rate'];
            $r['salestart']  = substr((string)$r['salestart'], 0, 10); // Bara datum-delen
            $rows[] = $r;
        }
        pg_free_result($res);
    }

        return $rows;
    }

    // --------------------------------------------------------
    // getStandardLocatorProducts()
    // Begagnade produkter (istradein='Y') med lagerplats Standard
    // (m_locator_id = 1000000) vars salestart har passerats.
    // Ingen 90-dagarsgrans - alla produkter som passerats visas.
    // --------------------------------------------------------
    public function getStandardLocatorProducts() {

        $conn = Db::getConnectionAD(false);
        if ($conn) { @pg_set_client_encoding($conn, 'UTF8'); }

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

        $sql = "
            SELECT
                p.m_product_id,
                p.value                                                      AS article,
                TRIM(COALESCE(manu.name, '') || ' ' || COALESCE(p.name, '')) AS product_full,
                p.salestart,
                (NOW()::date - p.salestart::date)                            AS age_days,

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
                )                                                            AS net_price,

                COALESCE(ps.qtyonhand, 0)                                    AS stock_qty,

                COALESCE((
                    SELECT t.rate
                      FROM c_tax t
                     WHERE t.c_taxcategory_id = p.c_taxcategory_id
                       AND t.c_country_id = 313
                     ORDER BY t.isdefault DESC NULLS LAST
                     LIMIT 1
                ), 0)                                                         AS tax_rate

            FROM m_product p
            LEFT JOIN xc_manufacturer manu  ON manu.xc_manufacturer_id = p.xc_manufacturer_id
            LEFT JOIN m_product_category pc ON pc.m_product_category_id = p.m_product_category_id
            LEFT JOIN ($stockSub) ps        ON ps.m_product_id = p.m_product_id
            LEFT JOIN m_productprice pp
                   ON pp.m_product_id = p.m_product_id
                  AND pp.m_pricelist_version_id = 1000000
            WHERE p.istradein = 'Y'
              AND p.iswebstoreproduct = 'Y'
              AND p.m_locator_id = 1000000
              AND COALESCE(ps.qtyonhand, 0) > 0
              AND p.salestart IS NOT NULL
              AND p.salestart <= NOW()
            ORDER BY p.salestart ASC NULLS LAST
        ";

        $rows = array();
        if ($res = ($conn) ? @pg_query($conn, $sql) : false) {
            while ($res && $r = pg_fetch_assoc($res)) {
                $r['net_price'] = (float)$r['net_price'];
                $r['stock_qty'] = (int)$r['stock_qty'];
                $r['age_days']  = (int)$r['age_days'];
                $r['tax_rate']  = (float)$r['tax_rate'];
                $r['salestart'] = substr((string)$r['salestart'], 0, 10);
                $rows[] = $r;
            }
            pg_free_result($res);
        }

        return $rows;
    }

}

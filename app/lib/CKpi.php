<?php
require_once("CCheckIpNumber.php");
require_once("Db.php");

Class CKpi {


public function getSalesTodayTotalsFromInout($dagensdatum)
{
    // Vi kör [>= dagens 00:00, < nästa dags 00:00] för bättre indexanvändning
    $start = $dagensdatum . " 00:00:00";
    $end   = date('Y-m-d', strtotime($dagensdatum . ' +1 day')) . " 00:00:00";

    $sql = "
        SELECT
            COUNT(*) AS cnt,
            COALESCE(SUM(inv.totallines), 0) AS sum_total
        FROM m_inout io
        LEFT JOIN c_invoice inv ON inv.c_invoice_id = io.c_invoice_id
        WHERE
            io.docstatus IN ('CO')
            AND io.deliveryviarule IN ('S','P')
            AND io.issotrx = 'Y'
            AND io.isindispute != 'Y'
            AND io.isactive = 'Y'
            AND io.ad_client_id = 1000000
            AND io.m_rma_id IS NULL
            AND io.updated >= $1
            AND io.updated <  $2
    ";

    $res = (Db::getConnectionAD(false)) ? @pg_query_params(Db::getConnectionAD(false), $sql, array($start, $end)) : false;

    $count = 0;
    $sum   = 0;

    if ($res && pg_num_rows($res) > 0) {
        $r = $res ? pg_fetch_row($res) : null;
        $count = (int)$r[0];
        $sum   = (float)$r[1];
    }

    return array(
        'date'  => $dagensdatum,
        'count' => $count,
        'sum'   => $sum
    );
}

private function mariaEscape($db, $value)
{
    $value = (string)$value;

    if (is_object($db) && method_exists($db, 'real_escape_string')) {
        return $db->real_escape_string($value);
    }

    if (is_resource($db) && function_exists('mysql_real_escape_string')) {
        return mysqli_real_escape_string($db, $value);
    }

    return addslashes($value);
}

private function mariaUseDb($db, $dbName)
{
    // mysqli
    if (is_object($db) && method_exists($db, 'select_db')) {
        @$db->select_db($dbName);
        return;
    }

    // mysql_* resource
    if (is_resource($db) && function_exists('mysql_select_db')) {
        @mysqli_select_db($db, $dbName);
    }
}

private function mariaQueryOrDie($db, $sql)
{
    // mysqli
    if (is_object($db) && method_exists($db, 'query')) {
        $res = $db->query($sql);
        if ($res === false) {
            error_log("CKpi MariaDB SQL ERROR (mysqli): " . $db->error . " | SQL=" . $sql);
        }
        return $res;
    }

    // mysql_* resource
    if (is_resource($db) && function_exists('mysql_query')) {
        $res = mysqli_query($db, $sql);
        if ($res === false) {
            $err = function_exists('mysql_error') ? mysqli_error($db) : 'unknown mysql error';
            error_log("CKpi MariaDB SQL ERROR (mysql_*): " . $err . " | SQL=" . $sql);
        }
        return $res;
    }

    error_log("CKpi MariaDB ERROR: Unknown DB handle type");
    return false;
}


public function upsertKpiCache($kpiKey, $kpiDate, $countValue, $sumValue, $jsonValue = '', $source = 'pg')
{
    $db = Db::getConnection(true); // MariaDB write

    // Tvinga databasen där tabellen faktiskt finns
    $this->mariaUseDb($db, 'cyberadmin');

    $kpiKey = $this->mariaEscape($db, $kpiKey);
    $source = $this->mariaEscape($db, $source);

    $kpiDateSql = ($kpiDate === null || $kpiDate === '')
        ? "NULL"
        : "'" . $this->mariaEscape($db, $kpiDate) . "'";

    $countSql = ($countValue === null) ? "NULL" : (int)$countValue;

    $sumSql = ($sumValue === null)
        ? "NULL"
        : "'" . $this->mariaEscape($db, number_format((float)$sumValue, 2, '.', '')) . "'";

    $jsonSql = ($jsonValue === null)
        ? "NULL"
        : "'" . $this->mariaEscape($db, $jsonValue) . "'";

    $sql = "
        INSERT INTO kpi_cache (kpi_key, kpi_date, count_value, sum_value, json_value, source)
        VALUES ('$kpiKey', $kpiDateSql, $countSql, $sumSql, $jsonSql, '$source')
        ON DUPLICATE KEY UPDATE
            count_value = VALUES(count_value),
            sum_value   = VALUES(sum_value),
            json_value  = VALUES(json_value),
            source      = VALUES(source),
            updated_at  = CURRENT_TIMESTAMP
    ";

    return $this->mariaQueryOrDie($db, $sql);
}


public function collectSalesToday($dagensdatum)
{
    // 1) Räkna (PostgreSQL/AD)
    $totals = $this->getSalesTodayTotalsFromInout($dagensdatum);

    // 2) Skriv till MariaDB cache
    $this->upsertKpiCache(
        'sales.today.inout',
        $dagensdatum,
        $totals['count'],
        $totals['sum'],
        '',
        'pg'
    );

    // 3) Returnera (cron får sitt JSON ändå)
    return $totals;
}

public function getOrdersPrintedTotals()
{
    $sql = "
        SELECT
            COUNT(*) AS cnt,
            COALESCE(SUM(x.totallines), 0) AS sum_total
        FROM (
            SELECT
                m.documentno,
                SUM((l.qtyordered - l.qtydelivered) * l.priceactual) AS totallines
            FROM m_inout m
            JOIN c_orderline l ON m.c_order_id = l.c_order_id
            JOIN c_order o ON o.c_order_id = l.c_order_id
            WHERE
                m.docstatus IN ('IP', 'IN')
                AND m.deliveryviarule IN ('S')
                AND m.issotrx = 'Y'
                AND m.isindispute != 'Y'
                AND m.isactive = 'Y'
                AND m.ad_client_id = 1000000
                AND m.m_rma_id IS NULL
            GROUP BY m.documentno
        ) x
    ";

    $res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $sql) : false;

    $count = 0;
    $sum   = 0;

    if ($res && pg_num_rows($res) > 0) {
        $r = $res ? pg_fetch_row($res) : null;
        $count = (int)$r[0];
        $sum   = (float)$r[1];
    }

    return array(
        'count' => $count,
        'sum'   => $sum
    );
}

public function collectOrdersPrinted()
{
    $totals = $this->getOrdersPrintedTotals();

    $this->upsertKpiCache(
        'orders.printed', // backlog KPI (ej datum)
        null,
        $totals['count'],
        $totals['sum'],
        '',
        'pg'
    );

    return $totals;
}

public function getOrdersNotPrintedTotals()
{
    $sql = "
        SELECT
            COUNT(*) AS cnt,
            COALESCE(SUM(totallines), 0) AS sum_total
        FROM M_InOut_Candidate_v ic
        WHERE
            ic.deliveryviarule = 'S'
            AND ic.ad_client_id = 1000000
    ";

    $res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $sql) : false;

    $count = 0;
    $sum   = 0;

    if ($res && pg_num_rows($res) > 0) {
        $r = $res ? pg_fetch_row($res) : null;
        $count = (int)$r[0];
        $sum   = (float)$r[1];
    }

    return array(
        'count' => $count,
        'sum'   => $sum
    );
}

public function collectOrdersNotPrinted()
{
    $totals = $this->getOrdersNotPrintedTotals();

    $this->upsertKpiCache(
        'orders.not_printed', // backlog KPI (ej datum)
        null,
        $totals['count'],
        $totals['sum'],
        '',
        'pg'
    );

    return $totals;
}

public function collectProductsCreatedLast12h()
{
    $sql  = "SELECT prod.launchdate, prod.created, manu.name AS manu_name, prod.name AS produktnamn, prod.value AS artnr, cat.name AS kategori, ";
    $sql .= "u.name AS upplagdav, prod.isselfservice, prod.manufacturerproductno, prod.iswebstoreproduct ";
    $sql .= "FROM m_product prod ";
    $sql .= "JOIN ad_user u ON u.ad_user_id = prod.createdby ";
    $sql .= "JOIN xc_manufacturer manu ON prod.xc_manufacturer_id = manu.xc_manufacturer_id ";
    $sql .= "JOIN m_product_category cat ON prod.m_product_category_id = cat.m_product_category_id ";
    $sql .= "WHERE NOT prod.demo_product = 'Y' AND NOT prod.discontinued = 'Y' ";
    $sql .= "AND prod.created > CURRENT_TIMESTAMP - INTERVAL '12 hours' ";
    $sql .= "AND prod.launchdate <= CURRENT_TIMESTAMP ";
    $sql .= "AND prod.iswebstoreproduct = 'Y' ";
    $sql .= "ORDER BY prod.created DESC ";

    $res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $sql) : false;

    $rows = array();

    if ($res && pg_num_rows($res) > 0) {
        while ($res && $r = pg_fetch_assoc($res)) {
            $rows[] = array(
                'artnr'    => (string)$r['artnr'],
                'name'     => (string)$r['produktnamn'],
                'manu'     => (string)$r['manu_name'],
                'cat'      => (string)$r['kategori'],
                'by'       => (string)$r['upplagdav'],
                'created'  => (string)$r['created'],
            );
        }
    }

    // Begränsa ifall någon dag sticker (UI blir ändå scroll, men vi vill inte lagra 5000 rader)
    if (count($rows) > 100) {
        $rows = array_slice($rows, 0, 100);
    }

    return array(
        'count' => count($rows),
        'rows'  => $rows,
    );
}

public function collectProductsDiscontinuedLast12h()
{
    $sql  = "SELECT prod.discontinueddate, manu.name AS manu_name, prod.name AS produktnamn, prod.value AS artnr, cat.name AS kategori, ";
    $sql .= "u.name AS upplagdav, prod.isselfservice, prod.manufacturerproductno ";
    $sql .= "FROM m_product prod ";
    $sql .= "JOIN ad_user u ON u.ad_user_id = prod.createdby ";
    $sql .= "JOIN xc_manufacturer manu ON prod.xc_manufacturer_id = manu.xc_manufacturer_id ";
    $sql .= "JOIN m_product_category cat ON prod.m_product_category_id = cat.m_product_category_id ";
    $sql .= "WHERE NOT prod.demo_product = 'Y' ";
    $sql .= "AND prod.discontinueddate > CURRENT_TIMESTAMP - INTERVAL '12 hours' ";
    $sql .= "ORDER BY prod.discontinueddate DESC ";

    $res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $sql) : false;

    $rows = array();

    if ($res && pg_num_rows($res) > 0) {
        while ($res && $r = pg_fetch_assoc($res)) {
            $rows[] = array(
                'artnr'        => (string)$r['artnr'],
                'name'         => (string)$r['produktnamn'],
                'manu'         => (string)$r['manu_name'],
                'cat'          => (string)$r['kategori'],
                'by'           => (string)$r['upplagdav'],
                'discontinued' => (string)$r['discontinueddate'],
            );
        }
    }

    if (count($rows) > 100) {
        $rows = array_slice($rows, 0, 100);
    }

    return array(
        'count' => count($rows),
        'rows'  => $rows,
    );
}

/**
 * Spara list-feed i kpi_cache.json_value
 * $kpiDate = null för feed (inte dagssumma)
 */
public function upsertKpiJson($kpiKey, $kpiDate, $rows, $source)
{
    $db = Db::getConnectionDb('cyberadmin');

    // Escape
    if (is_object($db) && method_exists($db, 'real_escape_string')) {
        $kpiKeyEsc = $db->real_escape_string((string)$kpiKey);
        $srcEsc    = $db->real_escape_string((string)$source);
        $jsonEsc   = $db->real_escape_string(json_encode($rows, JSON_UNESCAPED_UNICODE));
        $dateSql   = ($kpiDate === null || $kpiDate === '') ? "NULL" : "'" . $db->real_escape_string((string)$kpiDate) . "'";
    } else {
        // legacy mysql_* fallback
        $kpiKeyEsc = mysqli_real_escape_string($db, (string)$kpiKey);
        $srcEsc    = mysqli_real_escape_string($db, (string)$source);
        $jsonEsc   = mysqli_real_escape_string($db, json_encode($rows, JSON_UNESCAPED_UNICODE));
        $dateSql   = ($kpiDate === null || $kpiDate === '') ? "NULL" : "'" . mysqli_real_escape_string($db, (string)$kpiDate) . "'";
    }

    // count_value/sum_value kan stå 0 för feed  det är json_value som används
    $sql  = "INSERT INTO kpi_cache (kpi_key, kpi_date, count_value, sum_value, json_value, updated_at, source) VALUES (";
    $sql .= "'" . $kpiKeyEsc . "', " . $dateSql . ", 0, 0, '" . $jsonEsc . "', NOW(), '" . $srcEsc . "')";
    $sql .= " ON DUPLICATE KEY UPDATE ";
    $sql .= "count_value=VALUES(count_value), sum_value=VALUES(sum_value), json_value=VALUES(json_value), updated_at=NOW(), source=VALUES(source)";

    if (is_object($db) && method_exists($db, 'query')) {
        $db->query($sql);
    } else {
        mysqli_query($db, $sql);
    }

    return true;
}


}

?>
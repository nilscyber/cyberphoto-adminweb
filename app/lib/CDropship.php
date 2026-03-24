<?php
require_once("CCheckIpNumber.php");
require_once("Db.php");

Class CDropship
{
    public function getDropshipOrders($daysBack = 30, $limit = 200)
    {
        $conn = Db::getConnectionAD(false);

        $daysBack = (int)$daysBack;
        if ($daysBack <= 0) { $daysBack = 30; }

        $limit = (int)$limit;
        if ($limit <= 0) { $limit = 200; }
        if ($limit > 5000) { $limit = 5000; }

        $sql = "
            SELECT
                o.created::timestamp   AS created,
                o.documentno           AS documentno,
                bp.name                AS customer_name,
                o.totallines           AS totallines,
                o.margin               AS margin,
                o.marginamt            AS marginamt,
                u.name                 AS salesrep_name,
                o.external_reference   AS external_reference
            FROM c_order o
            JOIN c_bpartner bp
              ON bp.c_bpartner_id = o.c_bpartner_id
            LEFT JOIN ad_user u
              ON u.ad_user_id = o.salesrep_id
            WHERE
                o.issotrx = 'Y'
                AND o.c_doctype_id = 1000030
                AND o.docstatus <> 'VO'
                AND o.created >= (CURRENT_TIMESTAMP - ($1::int * INTERVAL '1 day'))
                AND EXISTS (
                    SELECT 1
                    FROM c_orderline ol
                    WHERE ol.c_order_id = o.c_order_id
                      AND ol.m_warehouse_id = 1000003
                )
            ORDER BY o.created DESC
            LIMIT $2
        ";

        $res = ($conn) ? @pg_query_params($conn, $sql, array($daysBack, $limit)) : false;
        if ($res === false) {
            return array('error' => 'DB error: ' . pg_last_error($conn), 'rows' => array());
        }

        $rows = array();
        while ($res && $row = pg_fetch_assoc($res)) {
            $row['totallines'] = (float)$row['totallines'];
            $row['margin']     = (float)$row['margin'];
            $row['marginamt']  = (float)$row['marginamt'];
            $rows[] = $row;
        }

        return array('error' => '', 'rows' => $rows);
    }

    // ============================================
    // PRODUKTLISTA  DROPSHIP (LEVERERAD / EJ)
    // ============================================
    public function getDropshipLinesByDeliveryStatus($status = 'undelivered', $daysBack = 365, $limit = 1000)
    {
        $conn = Db::getConnectionAD(false);

        $status = ($status === 'delivered') ? 'delivered' : 'undelivered';

        $daysBack = (int)$daysBack;
        if ($daysBack <= 0) { $daysBack = 365; }

        $limit = (int)$limit;
        if ($limit <= 0) { $limit = 1000; }
        if ($limit > 5000) { $limit = 5000; }

        if ($status === 'delivered') {
            $deliveryWhere = "
                COALESCE(ol.qtydelivered, 0) >= COALESCE(ol.qtyordered, 0)
                AND COALESCE(ol.qtyordered, 0) > 0
            ";
        } else {
            $deliveryWhere = "COALESCE(ol.qtydelivered, 0) < COALESCE(ol.qtyordered, 0)";
        }

		$orderBy = "o.created ASC, o.documentno ASC, p.value ASC";
		if ($status === 'delivered') {
			$orderBy = "o.created DESC, o.documentno DESC, p.value ASC";
		}

        $sql = "
            SELECT
                o.created::date            AS order_date,
                o.documentno               AS orderno,
                p.value                    AS artnr,
                p.name                     AS product_name,
                p.m_product_id             AS m_product_id,

                xm.name                    AS manufacturer_name,

                COALESCE(ol.qtyordered, 0) AS qtyordered,
                COALESCE(ol.pricelimit, 0) AS pricelimit,
                COALESCE(ol.pricelist, 0)  AS pricelist,
                COALESCE(ol.linenetamt, 0) AS linenetamt
            FROM c_order o
            JOIN c_orderline ol
              ON ol.c_order_id = o.c_order_id
            JOIN m_product p
              ON p.m_product_id = ol.m_product_id
            LEFT JOIN xc_manufacturer xm
              ON xm.xc_manufacturer_id = p.xc_manufacturer_id
            WHERE
                o.issotrx = 'Y'
                AND o.c_doctype_id = 1000030
                AND o.docstatus <> 'VO'
                AND ol.m_warehouse_id = 1000003
                AND $deliveryWhere
                AND o.created >= (CURRENT_DATE - ($1::int * INTERVAL '1 day'))
			ORDER BY
				$orderBy
            LIMIT $2
        ";

        $res = ($conn) ? @pg_query_params($conn, $sql, array($daysBack, $limit)) : false;
        if ($res === false) {
            return array('error' => 'DB error: ' . pg_last_error($conn), 'rows' => array());
        }

        $rows = array();
        while ($res && $row = pg_fetch_assoc($res)) {
            $row['qtyordered'] = (float)$row['qtyordered'];
            $row['pricelimit'] = (float)$row['pricelimit'];
            $row['pricelist']  = (float)$row['pricelist'];
            $row['linenetamt'] = (float)$row['linenetamt'];
            $rows[] = $row;
        }

        return array('error' => '', 'rows' => $rows);
    }


public function getTopSoldDropshipArticlesHtml($daysBack = 14, $limit = 10)
{
    $conn = Db::getConnectionAD(false);

    $daysBack = (int)$daysBack;
    if ($daysBack <= 0) { $daysBack = 14; }

    $limit = (int)$limit;
    if ($limit <= 0) { $limit = 10; }
    if ($limit > 100) { $limit = 100; }

    // Konvertera inkommande DB-strängar till UTF-8 (admin/DB kan vara latin1/cp1252)
    $toUtf8 = function ($s) {
        $s = (string)$s;
        if ($s === '') return $s;

        if (preg_match('//u', $s)) {
            return $s; // redan utf-8
        }

        // CP1252 först (hanterar  m.m.)
        return $s;
    };

    $sql = "
        SELECT
            p.value                 AS artnr,
            p.m_product_id          AS m_product_id,
            p.name                  AS product_name,
            COALESCE(xm.name, '')   AS manufacturer_name,
            SUM(COALESCE(ol.qtyordered, 0)) AS qty_sold,
            SUM(COALESCE(ol.linenetamt, 0)) AS sales_sek
        FROM c_order o
        JOIN c_orderline ol
          ON ol.c_order_id = o.c_order_id
        JOIN m_product p
          ON p.m_product_id = ol.m_product_id
        LEFT JOIN xc_manufacturer xm
          ON xm.xc_manufacturer_id = p.xc_manufacturer_id
        WHERE
            o.issotrx = 'Y'
            AND o.docstatus IN ('CO','CL')
            AND o.created >= (CURRENT_DATE - ($1::int * INTERVAL '1 day'))

            AND ol.m_warehouse_id = 1000003
            AND COALESCE(ol.qtyordered, 0) > 0
			AND COALESCE(ol.qtydelivered, 0) >= COALESCE(ol.qtyordered, 0)
            AND ol.c_charge_id IS NULL
            AND COALESCE(p.producttype, '') = 'I'
        GROUP BY
            p.value, p.m_product_id, p.name, xm.name
        ORDER BY
            qty_sold DESC, sales_sek DESC
        LIMIT $2
    ";

    $res = ($conn) ? @pg_query_params($conn, $sql, array($daysBack, $limit)) : false;
    if ($res === false) {
        return '<div style="color:#b91c1c;">DB error: '.htmlspecialchars(pg_last_error($conn)).'</div>';
    }

    $rows = array();
    while ($res && $row = pg_fetch_assoc($res)) {
        $rows[] = $row;
    }

    if (empty($rows)) {
        return '<div>Inga rader hittades för perioden.</div>';
    }

    $html  = '<table class="stat-table">';
    $html .= '<thead><tr>';
    $html .= '<th style="width:40px;">#</th>';
    $html .= '<th style="width:120px;">Artikelnr</th>';
    $html .= '<th>Produkt</th>';
    $html .= '<th style="width:90px;text-align:right;">Antal</th>';
    $html .= '<th style="width:130px;text-align:right;">'.htmlspecialchars($toUtf8("Omsättning"), ENT_QUOTES, 'UTF-8').'</th>';
    $html .= '</tr></thead><tbody>';

    $i = 0;
    foreach ($rows as $r) {
        $i++;

        $artnr = $toUtf8($r['artnr']);
        $pid   = (int)$r['m_product_id'];

        $manu  = trim($toUtf8($r['manufacturer_name']));
        $pname = trim($toUtf8($r['product_name']));

        // Tillverkare + produktnamn
        $displayName = ($manu !== '') ? ($manu.' '.$pname) : $pname;

        $qty   = (float)$r['qty_sold'];
        $sales = (float)$r['sales_sek'];

        // Länk till produktsök med öppet produktläge
        $prodUrl = 'https://admin.cyberphoto.se/search_dispatch.php?mode=product'
                 . '&q=' . rawurlencode($artnr)
                 . '&open=product'
                 . '&id=' . $pid;

        $html .= '<tr>';
        $html .= '<td>'.(int)$i.'</td>';
        $html .= '<td>'.htmlspecialchars($artnr, ENT_QUOTES, 'UTF-8').'</td>';

        // Klickbart produktnamn
        $html .= '<td><a href="'.htmlspecialchars($prodUrl, ENT_QUOTES, 'UTF-8').'" target="_blank" style="color:#111;text-decoration:underline;">'
              .  htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8')
              .  '</a></td>';

        $html .= '<td style="text-align:right;">'.number_format($qty, 0, ',', ' ').'</td>';
        $html .= '<td style="text-align:right;">'.number_format($sales, 0, ',', ' ').' SEK</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return $html;
}

public function getTopSoldDropshipDeliveredArticlesHtml($dateFrom, $dateTo, $limit = 10)
{
    $conn = Db::getConnectionAD(false);

    $limit = (int)$limit;
    if ($limit <= 0) { $limit = 10; }
    if ($limit > 100) { $limit = 100; }

    $toUtf8 = function ($s) {
        return (string)$s;
    };

    // Viktigt: använd intervall [from, to) (to är exklusiv)
    $sql = "
        SELECT
            p.value                 AS artnr,
            p.m_product_id          AS m_product_id,
            p.name                  AS product_name,
            COALESCE(xm.name, '')   AS manufacturer_name,
            SUM(COALESCE(ol.qtyordered, 0)) AS qty_sold,
            SUM(COALESCE(ol.linenetamt, 0)) AS sales_sek
        FROM c_order o
        JOIN c_orderline ol
          ON ol.c_order_id = o.c_order_id
        JOIN m_product p
          ON p.m_product_id = ol.m_product_id
        LEFT JOIN xc_manufacturer xm
          ON xm.xc_manufacturer_id = p.xc_manufacturer_id
        WHERE
            o.issotrx = 'Y'
            AND o.docstatus IN ('CO','CL')

			AND ol.datedelivered IS NOT NULL
			AND ol.datedelivered >= $1::timestamp
			AND ol.datedelivered <  $2::timestamp

            AND ol.m_warehouse_id = 1000003

            AND COALESCE(ol.qtyordered, 0) > 0
            AND COALESCE(ol.qtydelivered, 0) >= COALESCE(ol.qtyordered, 0)

            AND ol.c_charge_id IS NULL
            AND COALESCE(p.producttype, '') = 'I'
        GROUP BY
            p.value, p.m_product_id, p.name, xm.name
        ORDER BY
            qty_sold DESC, sales_sek DESC
        LIMIT $3
    ";

    $res = ($conn) ? @pg_query_params($conn, $sql, array($dateFrom, $dateTo, $limit)) : false;
    if ($res === false) {
        return '<div style="color:#b91c1c;">DB error: '.htmlspecialchars(pg_last_error($conn)).'</div>';
    }

    $rows = array();
    while ($res && $row = pg_fetch_assoc($res)) {
        $rows[] = $row;
    }

    if (empty($rows)) {
        return '<div>Inga rader hittades för perioden.</div>';
    }

    $html  = '<table class="stat-table">';
    $html .= '<thead><tr>';
    $html .= '<th style="width:40px;">#</th>';
    $html .= '<th style="width:120px;">Artikelnr</th>';
    $html .= '<th>Produkt</th>';
    $html .= '<th style="width:90px;text-align:right;">Antal</th>';
    $html .= '<th style="width:130px;text-align:right;">'.htmlspecialchars($toUtf8("Omsättning"), ENT_QUOTES, 'UTF-8').'</th>';
    $html .= '</tr></thead><tbody>';

    $i = 0;
    foreach ($rows as $r) {
        $i++;

        $artnr = $toUtf8($r['artnr']);
        $pid   = (int)$r['m_product_id'];

        $manu  = trim($toUtf8($r['manufacturer_name']));
        $pname = trim($toUtf8($r['product_name']));
        $displayName = ($manu !== '') ? ($manu.' '.$pname) : $pname;

        $qty   = (float)$r['qty_sold'];
        $sales = (float)$r['sales_sek'];

        $prodUrl = 'https://admin.cyberphoto.se/search_dispatch.php?mode=product'
                 . '&q=' . rawurlencode($artnr)
                 . '&open=product'
                 . '&id=' . $pid;

        $html .= '<tr>';
        $html .= '<td>'.(int)$i.'</td>';
        $html .= '<td>'.htmlspecialchars($artnr, ENT_QUOTES, 'UTF-8').'</td>';
        $html .= '<td><a href="'.htmlspecialchars($prodUrl, ENT_QUOTES, 'UTF-8').'" target="_blank">'
              .  htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8')
              .  '</a></td>';
        $html .= '<td style="text-align:right;">'.number_format($qty, 0, ',', ' ').'</td>';
        $html .= '<td style="text-align:right;">'.number_format($sales, 0, ',', ' ').' SEK</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return $html;
}


}
?>

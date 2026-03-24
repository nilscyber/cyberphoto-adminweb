<?php

Class CStatistics {

	function getLatestPayMethods($sv,$fi,$no) {
		global $history, $netto_noll, $group;
		
		$count = 1;

		$select  = "SELECT pay.name AS betalning, COUNT(o.documentno) AS antal, AVG(o.totallines) AS netto ";
		$select .= "FROM c_order o ";
		$select .= "JOIN c_paymentterm pay ON pay.c_paymentterm_id = o.c_paymentterm_id ";
		$select .= "WHERE o.c_doctype_id = 1000030 AND o.docstatus NOT IN ('VO', 'RE') ";
		if ($history == "day") {
			$select .= "AND o.created > '" . date("Y-m-d H:i:s",strtotime("-1 days")) . "' ";
		} elseif ($history == "week") {
			$select .= "AND o.created > '" . date("Y-m-d H:i:s",strtotime("-1 week")) . "' ";
		} elseif ($history == "month3") {
			$select .= "AND o.created > '" . date("Y-m-d H:i:s",strtotime("-3 MONTH")) . "' ";
		} elseif ($history == "month6") {
			$select .= "AND o.created > '" . date("Y-m-d H:i:s",strtotime("-6 MONTH")) . "' ";
		} else {
			$select .= "AND o.created > '" . date("Y-m-d H:i:s",strtotime("-1 MONTH")) . "' ";
		}
		$select .= "GROUP BY betalning ";
		$select .= "ORDER BY Antal DESC ";
		
		// echo $select;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $counts = pg_num_rows($res);
		
		while ($res && $row = pg_fetch_object($res)) {
			
			$shownetto = number_format($row->netto, 0, ',', ' ') . " " . $valuta;
			
			if ($count == $counts) {
				echo "['$row->betalning ($row->antal) $shownetto', $row->antal]\n";
			} else  {
				echo "['$row->betalning ($row->antal) $shownetto', $row->antal],\n";
			}
			
			$count++;
				
		}

	}

	function getLatestSveaPayMethods($sv,$fi,$no) {
		global $history, $netto_noll, $group;
		
		$count = 1;

		$select  = "SELECT COUNT(o.documentno) AS antal, xc.name, AVG(o.totallines) AS netto, xc.name2 ";
		$select .= "FROM c_order o  ";
		$select .= "LEFT JOIN xc_kreditor_pclass xc ON xc.xc_kreditor_pclass_id = o.xc_kreditor_pclass_id ";
		$select .= "WHERE o.c_doctype_id = 1000030 AND o.docstatus NOT IN ('VO', 'RE') AND o.c_paymentterm_id = 1000027 ";
		$select .= "AND xc.c_paymentterm_id IN(1000025,1000027,1000032) ";
		if ($history == "day") {
			$select .= "AND o.created > current_date - integer '1'  ";
		} elseif ($history == "week") {
			$select .= "AND o.created > current_date - integer '7'  ";
		} elseif ($history == "month3") {
			$select .= "AND o.created > current_date - integer '90'  ";
		} elseif ($history == "month6") {
			$select .= "AND o.created > current_date - integer '180'  ";
		} else {
			$select .= "AND o.created > current_date - integer '30'  ";
		}
		if ($fi) {
			$select .= " AND o.c_currency_id = 102 ";
			$valuta = "EUR";
		} elseif ($no) {
			$select .= " AND o.c_currency_id = 287 ";
			$valuta = "NOK";
		} else {
			$select .= " AND o.c_currency_id = 311 ";
			$valuta = "SEK";
		}

		if ($netto_noll == "no") {
			$select .= "AND o.totallines > 0 ";
		}
		$select .= "GROUP BY xc.name, xc.name2 ";
		$select .= "ORDER BY antal DESC ";
		
		if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nuX') {
			echo $select;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$counts = ($res ? pg_num_rows($res) : 0);
		
		while ($res && $row = pg_fetch_object($res)) {
			
			$shownetto = number_format($row->netto, 0, ',', ' ') . " " . $valuta;
			if ($row->name2 != "") {
				$betnamn = $row->name2;
			} else {
				$betnamn = $row->name;
			}
			
			if ($count == $counts) {
				echo "['$betnamn ($row->antal) $shownetto', $row->antal]\n";
			} else  {
				echo "['$betnamn ($row->antal) $shownetto', $row->antal	],\n";
			}
			
			$count++;
				
		}

	}

	function getAverageValue($sv,$fi,$no) {
		global $history, $thismonth;
		
		$this_mont = date("Y-m",time());
		
		$count = 1;

		$select  = "SELECT to_char(o.created, 'YYYY-MM') AS  month, COUNT(o.documentno) AS antal, AVG(o.totallines) AS snittet ";
		$select .= "FROM c_order o  ";
		$select .= "WHERE o.c_doctype_id = 1000030 AND o.docstatus NOT IN ('VO', 'RE') ";
		if ($thismonth == "yes") {
			$select .= "AND to_char(o.created, 'YYYY-MM') = '$this_mont' ";
		} else {
			$select .= "AND to_char(o.created, 'YYYY') = '$history' ";
			$select .= "AND NOT to_char(o.created, 'YYYY-MM') = '$this_mont' ";
		}

		if ($fi) {
			$select .= " AND o.c_currency_id = 102 ";
			$valuta = "EUR";
		} elseif ($no) {
			$select .= " AND o.c_currency_id = 287 ";
			$valuta = "NOK";
		} else {
			$select .= " AND o.c_currency_id = 311 ";
			$valuta = "SEK";
		}

		$select .= "GROUP BY 1 ";
		$select .= "ORDER BY 1 ASC ";
		
		// echo $select;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$counts = ($res ? pg_num_rows($res) : 0);
		
		while ($res && $row = pg_fetch_object($res)) {
			
			$snittet = round($row->snittet, 0);
			
			if ($count == $counts) {
				echo "['$row->month', $row->antal, $snittet]\n";
			} else  {
				echo "['$row->month',$row->antal,$snittet],\n";
			}
			
			$count++;
				
		}

	}

	function showSalesValueGraph() {
	
		$startrad = 1;
		?>
			<script type='text/javascript' src='http://www.google.com/jsapi'></script>
			<script type='text/javascript'>
			  google.load('visualization', '1', {'packages':['annotatedtimeline']});
			  google.setOnLoadCallback(drawChart);
			  function drawChart() {
				var data = new google.visualization.DataTable();
				data.addColumn('date', 'Date');
				data.addColumn('number', 'Försäljning');
				data.addRows([
		<?php
	
			$select  = "SELECT DATE_FORMAT(sales_Date, '%Y-%m-%d') AS Datum, sales_Value ";
			$select .= "FROM cyberadmin.salesvalue ";
			$select .= "WHERE sales_Type = 0 AND sales_Value > 100000 ";
			$select .= "ORDER BY Datum DESC ";
			
			$res = mysqli_query(Db::getConnection(), $select);
			
				$num_rows = mysqli_num_rows($res);
				// echo "$num_rows Rows\n";
				// echo mysqli_num_rows($res);
				if (mysqli_num_rows($res) > 0) {
				
					while ($row = mysqli_fetch_array($res)):
				
					extract($row);
					
						$pyear = date("Y", strtotime($Datum));
						$pmonth = date("n", strtotime($Datum))-1;
						$pday = date("j", strtotime($Datum));
						
						if ($num_rows == $startrad) {
							echo "\t[new Date($pyear, $pmonth ,$pday), $sales_Value]\n";
						} else {
							echo "\t[new Date($pyear, $pmonth ,$pday), $sales_Value],\n";
						}
					
					$startrad++;
	
					endwhile;
	
				} else {
				
					echo "";
				}
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		?>
				]);
				
				var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('sales_div'));
				chart.draw(data, {'colors': ['green'],displayAnnotations: true, thickness: 1, fill: 50, displayExactValues: true, 'scaleType': 'maximized'} );
			  }
			</script>
		<?php
			} else {
		?>
				]);
				
				var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('sales_div'));
				chart.draw(data, {'colors': ['green'],displayAnnotations: true, thickness: 1, fill: 50, displayExactValues: true, 'scaleType': 'maximized'} );
			  }
			</script>
		<?php
			}
	
	}
	
	function getSalesThisMonth($lastyear) {
	
		$startrad = 1;

		$select  = "SELECT SUM(sales_Value) AS total_sales ";
		$select .= "FROM cyberadmin.salesvalue ";
		$select .= "WHERE MONTH(sales_Date) = MONTH(NOW()) ";
		if ($lastyear) {
			$select .= "AND YEAR(sales_Date) = YEAR(NOW()) -1 ";
		} else {
			$select .= "AND YEAR(sales_Date) = YEAR(NOW()) ";
		}
		$select .= "AND sales_Type = 0 ";
			
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_array($res);
		extract($row);
		
		return $total_sales;
	
	}

	function getSalesThisMonthNew() {
	
		$startrad = 1;

		$select  = "SELECT ";
		$select .= "SUM(CASE WHEN sales_Type = 0 THEN sales_Value ELSE 0 END) AS total_sales, ";
		$select .= "MAX(CASE WHEN sales_Type = 1 AND DATE(sales_Date) = CURDATE() THEN sales_Value ELSE NULL END) AS max_sales_today, ";
		$select .= "SUM(CASE WHEN sales_Type = 0 THEN sales_Value ELSE 0 END) + ";
		$select .= "MAX(CASE WHEN sales_Type = 1 AND DATE(sales_Date) = CURDATE() THEN sales_Value ELSE 0 END) AS total_with_max ";
		$select .= "FROM cyberadmin.salesvalue ";
		$select .= "WHERE MONTH(sales_Date) = MONTH(NOW()) ";
		$select .= "AND YEAR(sales_Date) = YEAR(NOW()) ";
		$select .= "AND (sales_Type = 0 OR sales_Type = 1) ";
			
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_array($res);
		extract($row);
		
		return $total_with_max;
	
	}

	function getSalesThisMonthLastYear() {
	
		$startrad = 1;

		$select  = "SELECT SUM(sales_Value) AS total_sales ";
		$select .= "FROM cyberadmin.salesvalue ";
		$select .= "WHERE MONTH(sales_Date) = MONTH(NOW()) ";
		$select .= "AND YEAR(sales_Date) = YEAR(NOW()) -1 ";
		$select .= "AND sales_Type = 0 ";
			
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_array($res);
		extract($row);
		
		return $total_sales;
	
	}

    public function getReportByReason()
    {
        $conn = Db::getConnection(false);
        $sql = "SELECT r.id AS reason_id, r.label AS reason, COUNT(*) AS antal
                FROM product_feedback pf
                JOIN product_feedback_reasons r ON pf.reason_id = r.id
                GROUP BY pf.reason_id
                ORDER BY antal DESC";

        $result = mysqli_query($conn, $sql);
        $total = 0;

        $html = "<h2>Rapportering per orsak</h2>";
        $html .= "<table class='stat-table'>";
        $html .= "<tr><th>Orsak</th><th style='text-align: center; width: 80px;'>Antal</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            $reason = htmlspecialchars($row['reason']);
            $count = (int)$row['antal'];
            $total += $count;

            $html .= "<tr>";
            $html .= "<td><a href='?reason_id=" . $row['reason_id'] . "'>" . $reason . "</a></td>";
            $html .= "<td style='text-align: center;'>" . $count . "</td>";
            $html .= "</tr>";
        }

        $html .= "<tr><th style='text-align: right;'>Totalt:</th><th style='text-align: center;'>$total</th></tr>";
        $html .= "</table>";

        return $html;
    }

public function getArticlesForReason($reason_id)
{
    $conn      = Db::getConnection(false);
    $reason_id = (int)$reason_id;

    // Hämta orsakens label
    $sqlReason = "
        SELECT label
        FROM product_feedback_reasons
        WHERE id = $reason_id
        LIMIT 1
    ";
    $resReason = mysqli_query($conn, $sqlReason);
    $reasonLabel = '';
    if ($resReason && mysqli_num_rows($resReason) > 0) {
        $rowReason   = mysqli_fetch_assoc($resReason);
        $reasonLabel = $rowReason['label'];
    }

    $reasonLabelH = htmlspecialchars($reasonLabel);

    // Hämta artiklar för vald orsak
    $sql = "
        SELECT
            pf.artnr,
            COUNT(*) AS antal,
            CONCAT_WS(' ', t.tillverkare, a.beskrivning) AS produkt
        FROM product_feedback pf
        LEFT JOIN Artiklar a   ON pf.artnr = a.artnr
        LEFT JOIN Tillverkare t ON t.tillverkar_id = a.tillverkar_id
        WHERE pf.reason_id = $reason_id
        GROUP BY pf.artnr
        ORDER BY antal DESC
    ";

    $result = mysqli_query($conn, $sql);

    $html  = "<h2>Produkter rapporterade för orsak: <em>$reasonLabelH</em></h2>";
    $html .= "<table class='stat-table'>";
    $html .= "<tr>"
           . "<th>Artikelnummer</th>"
           . "<th>Produkt</th>"
           . "<th style='text-align:center;width:80px;'>Antal</th>"
           . "</tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $artnr   = htmlspecialchars($row['artnr']);
        $produkt = htmlspecialchars($row['produkt']);
        $antal   = (int)$row['antal'];

        // Länk till detaljvy för artikeln i samma admin-sida
        $urlArt = "product_feedback.php?artnr=" . urlencode($row['artnr']) . "&details=1";
        $artnrLink = "<a href=\"$urlArt\">$artnr</a>";

        // (Valfritt) produktnamn som länk till www2/drawer
        $urlInfo = "https://www2.cyberphoto.se/info.php?article=" . urlencode($row['artnr']);
        $produktLink = "<a href=\"$urlInfo\" target=\"_blank\">$produkt</a>";

        $html .= "<tr>";
        $html .= "<td>$artnrLink</td>";
        $html .= "<td>$produktLink</td>";
        $html .= "<td style='text-align:center;'>$antal</td>";
        $html .= "</tr>";
    }

    $html .= "</table>";

    return $html;
}


	public function getReportByUser()
	{
		$conn = Db::getConnection(false);

		$sql = "SELECT user_email, COUNT(*) AS antal
				FROM product_feedback
				GROUP BY user_email
				ORDER BY antal DESC";

		$result = mysqli_query($conn, $sql);

		$html = "<h2>Rapportörer</h2>";
		$html .= "<table class='stat-table'>";
		$html .= "<tr><th>Användare</th><th style='text-align: center; width: 80px;'>Antal</th></tr>";

		while ($row = mysqli_fetch_assoc($result)) {
			$email = htmlspecialchars($row['user_email']);
			$antal = (int)$row['antal'];

			$html .= "<tr>";
			$html .= "<td>$email</td>";
			$html .= "<td style='text-align: center;'>$antal</td>";
			$html .= "</tr>";
		}

		$html .= "</table>";

		return $html;
	}

public function getReportsForArticleInReason($reason_id, $artnr)
{
    $conn = Db::getConnection(false);

    // Orsakslabel för rubriken
    $sql_reason = "SELECT label FROM product_feedback_reasons WHERE id = ".(int)$reason_id." LIMIT 1";
    $res_reason = mysqli_query($conn, $sql_reason);
    $row_reason = $res_reason ? mysqli_fetch_assoc($res_reason) : null;
    $reason_label = $row_reason ? htmlspecialchars($row_reason['label']) : '';

    // Escapa artnr för SQL
    $artnr_esc = mysqli_real_escape_string($conn, $artnr);

    // Hämta rapporter (nyast först)
    $sql = "
        SELECT
            id,
            ordernr,
            user_email,
            `timestamp`,
            notes
        FROM product_feedback
        WHERE reason_id = ".(int)$reason_id."
          AND artnr = '".$artnr_esc."'
        ORDER BY `timestamp` DESC, id DESC
    ";
    $result = mysqli_query($conn, $sql);

    $artnr_h = htmlspecialchars($artnr);

    $html  = "<h2>Rapporter för artikel: <em>{$artnr_h}</em>  orsak: <em>{$reason_label}</em></h2>";
    $html .= "<table class='stat-table'>";
    $html .= "<tr>"
          .  "<th>Rapporttid</th>"
          .  "<th>Ordernr</th>"
          .  "<th>Rapportör</th>"
          .  "<th>Notering</th>"
          .  "</tr>";

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $ts    = htmlspecialchars($row['timestamp']);
            $user  = htmlspecialchars($row['user_email']);
            $notes = ($row['notes'] !== null && $row['notes'] !== '')
                       ? nl2br(htmlspecialchars($row['notes']))
                       : '';

            // Bygg cell för ordernummer:
            // - Saknas (NULL) => streck
            // - Finns => klickbar länk som öppnar popup 1100x700
            if (is_null($row['ordernr'])) {
                $ord_cell = '';
            } else {
                $ord_int  = (int)$row['ordernr'];
                $ord_txt  = (string)$ord_int;
                $ord_cell = '<a href="order_info.php?order='.$ord_int.'" '
                          . 'onclick="return openOrderPopup('.$ord_int.');" '
                          . 'title="Öppna order '.$ord_txt.' i popup">'.$ord_txt.'</a>';
            }

            $html .= "<tr>"
                  .  "<td>{$ts}</td>"
                  .  "<td>{$ord_cell}</td>"
                  .  "<td>{$user}</td>"
                  .  "<td>{$notes}</td>"
                  .  "</tr>";
        }
    } else {
        $html .= "<tr><td colspan='4'>Inga rapporter hittades.</td></tr>";
    }

    $html .= "</table>";

    // Tillbaka-länk (egen sida, inte hårdkodat namn)
    $self    = htmlspecialchars($_SERVER['PHP_SELF']);
    $backUrl = $self . "?reason_id=".(int)$reason_id;
    $html   .= "<p style='margin-top:8px'><a href='{$backUrl}'>&larr; Tillbaka till produkter för orsaken</a></p>";

    return $html;
}

/**
 * Grund-KPI: Units per order (endast säljordrar, ej annullerade).
 * - $mode: 'total' | 'web' | 'manual'
 * - $webId: t.ex. 1652736
 * - $excludedCategoryIds: t.ex. array(1000362,1000441)
 * Returnerar EN rad med: orders, units, avg_units_per_order, orders_1_item, orders_2_items, orders_3plus_items
 */
public function getUnitsPerOrderBasic($from, $to, $mode = 'total', $webId = null, array $excludedCategoryIds = array(1000362,1000441))
{
    $conn = Db::getConnectionAD(false); // pg resource

    if ($conn) { @pg_set_client_encoding($conn, 'UTF8'); }

    if ($mode === 'web' && $webId === null) {
        return array(
            'orders'=>0,'units'=>0,'avg_units_per_order'=>'0.00',
            'orders_1_item'=>0,'orders_2_items'=>0,'orders_3plus_items'=>0
        );
    }

    // Postgres array-literals
    $webIdArr = ($webId === null) ? '{}' : '{'.(int)$webId.'}';
    $exclArr  = '{'.implode(',', array_map('intval', $excludedCategoryIds)).'}';

    $sql = "
WITH
params AS (
  SELECT
    $1::timestamp AS from_se,
    $2::timestamp AS to_se,
    $3::text      AS mode,
    $4::int[]     AS web_ids,
    $5::int[]     AS excl_cats,
    TIMESTAMP '2018-10-31 00:00:00' AS cutoff_ts,
    1000121::int  AS legacy_web_id
),
orders_base AS (
  SELECT o.c_order_id, o.created, o.salesrep_id
  FROM c_order o
  CROSS JOIN params p
  WHERE o.created >= p.from_se
    AND o.created <  p.to_se
    AND o.c_doctype_id = 1000030
    AND o.docstatus NOT IN ('VO','RE')
),
orders_tagged AS (
  SELECT
    ob.c_order_id,
    CASE
      WHEN ob.created <  (SELECT cutoff_ts FROM params)
           AND ob.salesrep_id IS NOT NULL
           AND ob.salesrep_id::int = (SELECT legacy_web_id FROM params)
        THEN TRUE
      WHEN ob.created >= (SELECT cutoff_ts FROM params)
           AND ob.salesrep_id IS NOT NULL
           AND ob.salesrep_id::int = ANY((SELECT web_ids FROM params)::int[])
        THEN TRUE
      ELSE FALSE
    END AS is_web
  FROM orders_base ob
),
eo AS (
  SELECT ot.c_order_id
  FROM orders_tagged ot
  CROSS JOIN params p
  WHERE
        p.mode = 'total'
     OR (p.mode = 'web'    AND ot.is_web = TRUE)
     OR (p.mode = 'manual' AND ot.is_web = FALSE)
),
units_per_order AS (
  SELECT
    ol.c_order_id,
    SUM(ol.qtyordered)::numeric AS units
  FROM c_orderline ol
  JOIN eo           ON eo.c_order_id = ol.c_order_id
  JOIN m_product pr ON pr.m_product_id = ol.m_product_id
  WHERE NOT (
    COALESCE(pr.m_product_category_id::int, -1)
    = ANY ((SELECT excl_cats FROM params)::int[])
  )
  GROUP BY ol.c_order_id
),
stats AS (
  SELECT
    COALESCE(COUNT(*), 0)                  AS orders,
    COALESCE(SUM(units)::bigint, 0)        AS units,
    COALESCE(AVG(units)::numeric(10,2), 0) AS avg_units_per_order
  FROM units_per_order
),
dist AS (
  SELECT
    COALESCE(SUM(CASE WHEN units = 1 THEN 1 ELSE 0 END), 0)  AS cnt_1,
    COALESCE(SUM(CASE WHEN units = 2 THEN 1 ELSE 0 END), 0)  AS cnt_2,
    COALESCE(SUM(CASE WHEN units >= 3 THEN 1 ELSE 0 END), 0) AS cnt_3p
  FROM units_per_order
)
SELECT
  s.orders,
  s.units,
  s.avg_units_per_order,
  d.cnt_1::int  AS orders_1_item,
  d.cnt_2::int  AS orders_2_items,
  d.cnt_3p::int AS orders_3plus_items
FROM stats s
CROSS JOIN dist d;
";

    $params = array($from, $to, $mode, $webIdArr, $exclArr);

    $res = ($conn) ? @pg_query_params($conn, $sql, $params) : false;
    if ($res === false) {
        error_log('getUnitsPerOrderBasic: '.pg_last_error($conn));
        return array(
            'orders'=>0,'units'=>0,'avg_units_per_order'=>'0.00',
            'orders_1_item'=>0,'orders_2_items'=>0,'orders_3plus_items'=>0
        );
    }

    $row = $res ? pg_fetch_assoc($res) : null;
    pg_free_result($res);
    if (!$row) {
        return array(
            'orders'=>0,'units'=>0,'avg_units_per_order'=>'0.00',
            'orders_1_item'=>0,'orders_2_items'=>0,'orders_3plus_items'=>0
        );
    }
    return $row;
}



/**
 * Hämtar KPI per säljare för valfritt datumintervall (inklusive båda ändar).
 * $from, $to i format YYYY-MM-DD
 */
public function getPurchaseStats($from = null, $to = null, $debug = false) {
    // Default = gårdagen om inget skickas in
    if (empty($from) || empty($to)) {
        $y = date('Y-m-d', strtotime('-1 day'));
        $from = $from ?: $y;
        $to   = $to   ?: $y;
    }
    // Omvända datum? Byt plats.
    if (strtotime($from) > strtotime($to)) { $t=$from; $from=$to; $to=$t; }

    // Postgres-anslutning (kan vara PDO eller pg-resource)
    $conn = Db::getConnectionAD(false);

    // IDENTISK struktur som i DBeaver (period-CTE + exklusivt slut)
    $sql_pg = "
WITH period AS (
  SELECT
    $1::date                                  AS start_dt,
    ($2::date + INTERVAL '1 day')             AS end_dt
),
orders AS (
  SELECT
    o.c_order_id,
    o.salesrep_id                 AS user_id,
    COALESCE(u.name, '(saknas)')  AS user_name,
    o.dateordered,
    o.totallines
  FROM c_order o
  JOIN period p
    ON o.dateordered >= p.start_dt
   AND o.dateordered <  p.end_dt
  LEFT JOIN ad_user u
    ON u.ad_user_id = o.salesrep_id
  WHERE o.c_doctype_id = 1000016
    AND o.docstatus NOT IN ('VO','RE')
),
line_counts AS (
  SELECT
    ol.c_order_id,
    COUNT(*) AS line_count
  FROM c_orderline ol
  GROUP BY ol.c_order_id
),
orders_with_lines AS (
  SELECT
    o.*,
    COALESCE(lc.line_count, 0) AS line_count
  FROM orders o
  LEFT JOIN line_counts lc
    ON lc.c_order_id = o.c_order_id
)
SELECT
  owl.user_name                           AS salesrep,
  COUNT(*)                                AS antal_inkopsordrar,
  SUM(owl.line_count)                     AS antal_orderrader_totalt,
  ROUND(SUM(owl.totallines)::numeric, 2)  AS totallines_sum
FROM orders_with_lines owl
GROUP BY owl.user_id, owl.user_name
ORDER BY antal_inkopsordrar DESC, salesrep;
";

    if ($conn instanceof PDO) {
        // Byt till namngivna parametrar för PDO
        $sql_pdo = str_replace(array('$1','$2'), array(':from',':to'), $sql_pg);
        $stmt = $conn->prepare($sql_pdo);
        $stmt->bindValue(':from', $from);
        $stmt->bindValue(':to',   $to);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (is_resource($conn)) {
        $res = ($conn) ? @pg_query_params($conn, $sql_pg, array($from, $to)) : false;
        if ($res === false) {
            error_log('getPurchaseStats: '.($conn ? pg_last_error($conn) : 'no connection'));
            return array();
        }
        $rows = pg_fetch_all($res);
        if (!$rows) $rows = array();
    } else {
        error_log('getPurchaseStats: Unknown connection type');
        return array();
    }

    if ($debug) {
        error_log('getPurchaseStats OK: rows='.count($rows).' from='.$from.' to='.$to.' (doctype=1000016)');
    }

    return $rows;
}

public function getSalesStats($from = null, $to = null, $debug = false) {

    if (empty($from) || empty($to)) {
        $y = date('Y-m-d', strtotime('-1 day'));
        $from = $from ?: $y;
        $to   = $to   ?: $y;
    }
    if (strtotime($from) > strtotime($to)) { $t=$from; $from=$to; $to=$t; }

    $conn = Db::getConnectionAD(false);
    if (!is_resource($conn)) {
        error_log('getSalesStats: invalid AD connection');
        return array();
    }

    // ===== Variant 1: Line-level TB (uses pricelimit/cost) =====
    // FIX: removed mc.m_warehouse_id (doesn't exist in your m_cost)
    $sql_pg_line = "
WITH period AS (
  SELECT $1::date AS start_dt, ($2::date + INTERVAL '1 day') AS end_dt
),
orders AS (
  SELECT
    o.c_order_id,
    o.salesrep_id AS user_id,
    COALESCE(u.name, '') AS user_name,
    o.dateordered,
    o.totallines,
    o.grandtotal,
    o.marginamt
  FROM c_order o
  JOIN period p ON o.dateordered >= p.start_dt AND o.dateordered < p.end_dt
  LEFT JOIN ad_user u ON u.ad_user_id = o.salesrep_id
  WHERE o.c_doctype_id = 1000030
    AND o.docstatus NOT IN ('VO','RE')
),
costs AS (
  SELECT
    mc.m_product_id,
    mc.currentcostprice
  FROM m_cost mc
  WHERE mc.m_costelement_id = 1000005
    AND mc.m_costtype_id    = 1000000
    AND mc.ad_client_id     = 1000000
    AND mc.isactive         = 'Y'
),
product_lines AS (
  SELECT
    ol.c_order_id,
    SUM(ol.linenetamt) AS prod_net_sum,
    SUM(ol.linenetamt - (ol.qtyordered * COALESCE(ol.pricelimit, c.currentcostprice, 0))) AS prod_tb_sum
  FROM c_orderline ol
  JOIN orders o ON o.c_order_id = ol.c_order_id
  LEFT JOIN costs c ON c.m_product_id = ol.m_product_id
  WHERE ol.m_product_id IS NOT NULL
    AND (ol.c_charge_id IS NULL OR ol.c_charge_id = 0)
  GROUP BY ol.c_order_id
),
line_counts AS (
  SELECT ol.c_order_id, COUNT(*) AS line_count
  FROM c_orderline ol
  JOIN orders o ON o.c_order_id = ol.c_order_id
  WHERE ol.m_product_id IS NOT NULL
    AND (ol.c_charge_id IS NULL OR ol.c_charge_id = 0)
  GROUP BY ol.c_order_id
),
orders_with_lines AS (
  SELECT
    o.*,
    COALESCE(lc.line_count, 0) AS line_count,
    COALESCE(pl.prod_net_sum, 0) AS prod_net_sum,
    COALESCE(pl.prod_tb_sum, 0)  AS prod_tb_sum
  FROM orders o
  LEFT JOIN line_counts   lc ON lc.c_order_id = o.c_order_id
  LEFT JOIN product_lines pl ON pl.c_order_id = o.c_order_id
)
SELECT
  NULLIF(BTRIM(owl.user_name), '') AS salesrep,
  COUNT(*) AS antal_saljordrar,
  SUM(owl.line_count) AS antal_orderrader_totalt,
  ROUND(AVG(owl.line_count)::numeric, 2) AS snitt_rader_per_order,
  ROUND(SUM(owl.totallines)::numeric, 2) AS totallines_sum,
  ROUND(SUM(owl.prod_tb_sum)::numeric, 2) AS tb_summa,
  ROUND(100 * SUM(owl.prod_tb_sum) / NULLIF(SUM(owl.prod_net_sum), 0), 2) AS tb_marginal_pct,
  SUM(owl.prod_net_sum) AS prod_net_sum
FROM orders_with_lines owl
GROUP BY owl.user_id, owl.user_name
ORDER BY antal_saljordrar DESC, salesrep
";

    // ===== Variant 2: Fallback TB from order header marginamt =====
    $sql_pg_fallback = "
WITH period AS (
  SELECT $1::date AS start_dt, ($2::date + INTERVAL '1 day') AS end_dt
),
orders AS (
  SELECT
    o.c_order_id,
    o.salesrep_id AS user_id,
    COALESCE(u.name, '') AS user_name,
    o.dateordered,
    o.totallines,
    o.grandtotal,
    o.marginamt
  FROM c_order o
  JOIN period p ON o.dateordered >= p.start_dt AND o.dateordered < p.end_dt
  LEFT JOIN ad_user u ON u.ad_user_id = o.salesrep_id
  WHERE o.c_doctype_id = 1000030
    AND o.docstatus NOT IN ('VO','RE')
),
product_lines AS (
  SELECT
    ol.c_order_id,
    SUM(ol.linenetamt) AS prod_net_sum
  FROM c_orderline ol
  JOIN orders o ON o.c_order_id = ol.c_order_id
  WHERE ol.m_product_id IS NOT NULL
    AND (ol.c_charge_id IS NULL OR ol.c_charge_id = 0)
  GROUP BY ol.c_order_id
),
line_counts AS (
  SELECT ol.c_order_id, COUNT(*) AS line_count
  FROM c_orderline ol
  JOIN orders o ON o.c_order_id = ol.c_order_id
  WHERE ol.m_product_id IS NOT NULL
    AND (ol.c_charge_id IS NULL OR ol.c_charge_id = 0)
  GROUP BY ol.c_order_id
),
orders_with_lines AS (
  SELECT
    o.*,
    COALESCE(lc.line_count, 0) AS line_count,
    COALESCE(pl.prod_net_sum, 0)::numeric(18,2) AS prod_net_sum
  FROM orders o
  LEFT JOIN line_counts   lc ON lc.c_order_id = o.c_order_id
  LEFT JOIN product_lines pl ON pl.c_order_id = o.c_order_id
)
SELECT
  NULLIF(BTRIM(owl.user_name), '') AS salesrep,
  COUNT(*) AS antal_saljordrar,
  SUM(owl.line_count) AS antal_orderrader_totalt,
  ROUND(AVG(owl.line_count)::numeric, 2) AS snitt_rader_per_order,
  ROUND(SUM(owl.totallines)::numeric, 2) AS totallines_sum,
  ROUND(SUM(owl.marginamt)::numeric, 2) AS tb_summa,
  ROUND(100 * SUM(owl.marginamt) / NULLIF(SUM(owl.prod_net_sum), 0), 2) AS tb_marginal_pct,
  SUM(owl.prod_net_sum) AS prod_net_sum
FROM orders_with_lines owl
GROUP BY owl.user_id, owl.user_name
ORDER BY antal_saljordrar DESC, salesrep
";

    // ===== Execute: try line variant first, fallback on SQL error =====
    $rows = array();
    $res = ($conn) ? @pg_query_params($conn, $sql_pg_line, array($from, $to)) : false;

    if ($res === false) {
        $err = $conn ? pg_last_error($conn) : 'no connection';
        error_log('getSalesStats: falling back to header margin. Reason: '.$err);

        $res2 = ($conn) ? @pg_query_params($conn, $sql_pg_fallback, array($from, $to)) : false;
        if ($res2 === false) {
            error_log('getSalesStats fallback error: '.($conn ? pg_last_error($conn) : 'no connection'));
            return array();
        }
        $rows = pg_fetch_all($res2);
        if (!$rows) $rows = array();
    } else {
        $rows = pg_fetch_all($res);
        if (!$rows) $rows = array();
    }

    // ===== Optional debug: find why salesrep is blank =====
    if ($debug) {
        $blank = 0;
        foreach ($rows as $r) {
            $sr = isset($r['salesrep']) ? trim((string)$r['salesrep']) : '';
            if ($sr === '') $blank++;
        }
        error_log('getSalesStats OK rows='.count($rows).' blanksalesrep='.$blank.' from='.$from.' to='.$to);

        // Sample problematic orders (where salesrep_id missing or user has empty name)
        $sql_dbg = "
WITH period AS (
  SELECT $1::date AS start_dt, ($2::date + INTERVAL '1 day') AS end_dt
)
SELECT
  o.c_order_id,
  o.dateordered,
  o.salesrep_id,
  u.ad_user_id AS user_hit,
  u.name       AS user_name
FROM c_order o
JOIN period p ON o.dateordered >= p.start_dt AND o.dateordered < p.end_dt
LEFT JOIN ad_user u ON u.ad_user_id = o.salesrep_id
WHERE o.c_doctype_id = 1000030
  AND o.docstatus NOT IN ('VO','RE')
  AND (
    o.salesrep_id IS NULL OR o.salesrep_id = 0
    OR u.ad_user_id IS NULL
    OR BTRIM(COALESCE(u.name,'')) = ''
  )
ORDER BY o.dateordered DESC
LIMIT 25
";
        $res_dbg = ($conn) ? @pg_query_params($conn, $sql_dbg, array($from, $to)) : false;
        if ($res_dbg !== false) {
            $dbg_rows = pg_fetch_all($res_dbg);
            if ($dbg_rows) {
                foreach ($dbg_rows as $dr) {
                    error_log(
                        'getSalesStats DBG order='.$dr['c_order_id'].
                        ' date='.$dr['dateordered'].
                        ' salesrep_id='.$dr['salesrep_id'].
                        ' user_hit='.$dr['user_hit'].
                        ' user_name="'.(isset($dr['user_name']) ? $dr['user_name'] : '').'"'
                    );
                }
            } else {
                error_log('getSalesStats DBG: no problematic orders in sample');
            }
        } else {
            error_log('getSalesStats DBG query error: '.pg_last_error($conn));
        }
    }

    return $rows;
}


public function getDailyReportByArticle($hours = 24)
{
    $conn = Db::getConnection(false);

    $hours = (int)$hours;
    if ($hours <= 0) {
        $hours = 24;
    }

    // Beräkna period (nu och nu - $hours)
    $now   = time();
    $end   = date('Y-m-d H:i', $now);
    $start = date('Y-m-d H:i', $now - ($hours * 3600));

    $sql = "
        SELECT
            pf.artnr,
            COUNT(*) AS antal,
            MAX(pf.timestamp) AS senast,
            GROUP_CONCAT(DISTINCT r.label ORDER BY r.label SEPARATOR ', ') AS orsaker,
            CONCAT_WS(' ', t.tillverkare, a.beskrivning) AS produkt
        FROM product_feedback pf
        LEFT JOIN product_feedback_reasons r ON pf.reason_id = r.id
        LEFT JOIN Artiklar a ON pf.artnr = a.artnr
        LEFT JOIN Tillverkare t ON t.tillverkar_id = a.tillverkar_id
        WHERE pf.timestamp >= (NOW() - INTERVAL $hours HOUR)
        GROUP BY pf.artnr
        ORDER BY antal DESC
    ";

    $result = mysqli_query($conn, $sql);

    $html  = "<h2>Senaste $hours timmar  per artikel</h2>";
    $html .= "<p>Period: $start till $end</p>";
    $html .= "<table class='stat-table'>";
    $html .= "<tr>"
           . "<th>Artikelnummer</th>"
           . "<th>Produkt</th>"
           . "<th>Orsaker</th>"
           . "<th>Senast</th>"
           . "<th style='text-align: center; width: 80px;'>Antal</th>"
           . "</tr>";

			while ($row = mysqli_fetch_assoc($result)) {
				$artnr   = htmlspecialchars($row['artnr']);
				$produkt = htmlspecialchars($row['produkt']);
				$orsaker = htmlspecialchars($row['orsaker']);
				$senast  = htmlspecialchars($row['senast']);
				$antal   = (int)$row['antal'];

				// Bygg länk till produktsidan (www2)
				$url = 'https://www2.cyberphoto.se/info.php?article=' . urlencode($row['artnr']);
				$produktLink = '<a href="' . $url . '" target="_blank">' . $produkt . '</a>';

				$html .= "<tr>";
				$html .= "<td>$artnr</td>";
				$html .= "<td>$produktLink</td>";
				$html .= "<td>$orsaker</td>";
				$html .= "<td>$senast</td>";
				$html .= "<td style='text-align: center;'>$antal</td>";
				$html .= "</tr>";
			}

    $html .= "</table>";

    return $html;
}

public function getReportForArticle($artnr)
{
    $conn = Db::getConnection(false);

    // Skydda mot SQL-injektion (mysql_* style)
    $artnr_db = mysqli_real_escape_string($conn, $artnr);

    // Hämta produktnamn
    $sqlProd = "
        SELECT CONCAT_WS(' ', t.tillverkare, a.beskrivning) AS produkt
        FROM Artiklar a
        LEFT JOIN Tillverkare t ON t.tillverkar_id = a.tillverkar_id
        WHERE a.artnr = '$artnr_db'
        LIMIT 1
    ";
    $resProd = mysqli_query($conn, $sqlProd);
    $prodName = '';
    if ($resProd && mysqli_num_rows($resProd) > 0) {
        $rowProd = mysqli_fetch_assoc($resProd);
        $prodName = $rowProd['produkt'];
    }

    $artnr_h   = htmlspecialchars($artnr);
    $prodNameH = $prodName !== '' ? htmlspecialchars($prodName) : 'Okänd produkt';

    // Hämta alla rapporter för artikeln
    $sql = "
        SELECT
            pf.timestamp,
            pf.ordernr,
            pf.notes,
            pf.user_email,
            r.label AS reason
        FROM product_feedback pf
        LEFT JOIN product_feedback_reasons r ON pf.reason_id = r.id
        WHERE pf.artnr = '$artnr_db'
        ORDER BY pf.timestamp DESC
    ";

    $result = mysqli_query($conn, $sql);

    $html  = "<h2>Rapporter för artikel $artnr_h  $prodNameH</h2>";

    if (!$result || mysqli_num_rows($result) === 0) {
        $html .= "<p>Inga rapporter registrerade för denna produkt.</p>";
        return $html;
    }

    $html .= "<table class='stat-table'>";
    $html .= "<tr>"
           . "<th>Tidpunkt</th>"
           . "<th>Orsak</th>"
           . "<th>Ordernr</th>"
           . "<th>Notering</th>"
           . "<th>Rapportör</th>"
           . "</tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $ts     = htmlspecialchars($row['timestamp']);
        $reason = htmlspecialchars($row['reason']);

        // Ordernr ? länk till search_dispatch om det finns
        $ord    = $row['ordernr'];
        if ($ord === null || $ord == 0) {
            $ordCell = "";
        } else {
            $ord_h = (int)$ord;
            $url   = 'https://admin.cyberphoto.se/search_dispatch.php'
                   . '?mode=order&page=1&q=' . urlencode($ord_h);
            $ordCell = '<a href="' . $url . '" target="_blank">' . $ord_h . '</a>';
        }

        $notes = htmlspecialchars($row['notes']);
        $notes = nl2br($notes);
        $user  = htmlspecialchars($row['user_email']);

        $html .= "<tr>";
        $html .= "<td>$ts</td>";
        $html .= "<td>$reason</td>";
        $html .= "<td>$ordCell</td>";
        $html .= "<td>$notes</td>";
        $html .= "<td>$user</td>";
        $html .= "</tr>";
    }

    $html .= "</table>";

    return $html;
}


	
}
?>
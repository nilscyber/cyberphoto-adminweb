<?php
require_once("CCheckIpNumber.php");
require_once("Db.php");

Class CSales {

	function __construct() {
	
	}

	function salesAddValue($value,$value_type,$date_insert = "") {

		if ($date_insert != "") {
			$updt  = "INSERT INTO cyberadmin.salesvalue ";
			$updt .= "(sales_Value,sales_Type,sales_IP,sales_Date) ";
			$updt .= "VALUES ";
			$updt .= "('$value','$value_type','" . $_SERVER['REMOTE_ADDR'] . "','" . $date_insert . " 19:00:01') ";
		} else {
			$updt  = "INSERT INTO cyberadmin.salesvalue ";
			$updt .= "(sales_Value,sales_Type,sales_IP) ";
			$updt .= "VALUES ";
			$updt .= "('$value','$value_type','" . $_SERVER['REMOTE_ADDR'] . "') ";
		}

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $updt;
			exit;
		}
		
		$res = mysqli_query(Db::getConnection(true), $updt);

	}

/**
 * Returnerar jämförelsedatum från föregående år, justerat till samma veckodag
 * och förbi eventuella röda dagar eller helg.
 *
 * @param string $date Dagens datum (YYYY-MM-DD)
 * @param array $holidays Lista med helgdagar (format: YYYY-MM-DD)
 * @return string Jämförbart datum föregående år
 */
public function getComparableDate($date, $holidays = []) {
	$year = date('Y', strtotime($date));
	$week = date('W', strtotime($date));
	$weekday = date('N', strtotime($date)); // 1 = måndag, 7 = söndag

	// Bygg datum från föregående år med samma veckonummer och veckodag
	$base = strtotime(($year - 1) . '-01-01');
	$target = date('Y-m-d', strtotime("+".($week - 1)." weeks +".($weekday - 1)." days", $base));

	// Justera framåt om datumet är röd dag eller helg
	while (in_array($target, $holidays) || date('N', strtotime($target)) >= 6) {
		$target = date('Y-m-d', strtotime('+1 day', strtotime($target)));
	}

	return $target;
}


function displaySalesPerUser() {
	global $history, $group_by_seller;

	if ($history == 'custom') {
		$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
		$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

		if (empty($date_from) || empty($date_to)) {
			echo "<p class=\"italic span_blue\">Vänligen välj både från- och tilldatum för att visa resultat.</p>";
			return;
		}
	}

	$dagensdatum = date("Y-m-d");
	$start = $end = "";

	if ($history == "this_week") {
		$start = date("Y-m-d", strtotime("monday this week"));
		$end = date("Y-m-d", strtotime("sunday this week"));
	} elseif ($history == "last_week") {
		$start = date("Y-m-d", strtotime("monday last week"));
		$end = date("Y-m-d", strtotime("sunday last week"));
	} elseif ($history == "this_month") {
		$start = date("Y-m-01");
		$end = date("Y-m-d");
	} elseif ($history == "last_month") {
		$start = date("Y-m-01", strtotime("first day of last month"));
		$end = date("Y-m-t", strtotime("last day of last month"));
	} elseif ($history == "custom") {
		$start = isset($_GET['date_from']) ? $_GET['date_from'] : '';
		$end = isset($_GET['date_to']) ? $_GET['date_to'] : '';
	} else {
		$start = $end = $dagensdatum;
	}
	
	$current_month_key = date('Y-m', strtotime($start));
	$budget_file = __DIR__ . '/budget.json';
	$budget_data = [];
	$budget_value = null;

	// Läs befintlig budgetfil
	if (file_exists($budget_file)) {
		$json = file_get_contents($budget_file);
		$budget_data = json_decode($json, true);
		if (!is_array($budget_data)) {
			$budget_data = [];
		}
		if (isset($budget_data[$current_month_key])) {
			$budget_value = (int)$budget_data[$current_month_key];
		}
	}

	// Hantera inskickning av ny budget (endast tillåten för Stefan & Albin)
	if (
		(isset($_COOKIE['login_mail']) && $_COOKIE['login_mail'] === 'stefan@cyberphoto.se') || 
		(isset($_COOKIE['login_mail']) && $_COOKIE['login_mail'] === 'albin@cyberphoto.se')
	) {
		if (isset($_POST['budget']) && is_numeric($_POST['budget'])) {
			$new_budget = (int)$_POST['budget'];
			$budget_data[$current_month_key] = $new_budget;
			file_put_contents($budget_file, json_encode($budget_data, JSON_PRETTY_PRINT));
			$budget_value = $new_budget;
			echo "<div style='color: green; margin-top: 10px;'>Budget sparad för $current_month_key!</div>";
		}
	}

	if ($history == "this_week" || $history == "last_week") {
		$week = date('W', strtotime($start));
		$year = date('Y', strtotime($start));
		$prev_year = $year - 1;
		$start_last_year = date('Y-m-d', strtotime($prev_year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
		$end_last_year = date('Y-m-d', strtotime($start_last_year . ' +6 days'));
	} elseif ($start == $end) {
		$helgdagar2024 = array(
			'2024-01-01','2024-01-06','2024-03-29','2024-03-31','2024-04-01',
			'2024-05-01','2024-05-09','2024-06-06','2024-06-21','2024-11-02',
			'2024-12-24','2024-12-25','2024-12-26','2024-12-31',
			'2025-01-01','2025-01-06','2025-04-18','2025-04-20','2025-04-21',
			'2025-05-01','2025-05-29','2025-06-06','2025-06-20','2025-11-01',
			'2025-12-24','2025-12-25','2025-12-26','2025-12-31',
			'2026-01-01','2026-01-06','2026-04-03','2026-04-05','2026-04-06',
			'2026-05-01','2026-05-14','2026-06-06','2026-06-19','2026-11-07',
			'2026-12-24','2026-12-25','2026-12-26','2026-12-31'
		);
		$start_last_year = $this->getComparableDate($start, $helgdagar2024);
		$end_last_year = $start_last_year;
	} else {
		if ($history == "this_month") {
			$start_last_year = date("Y-m-01", strtotime("-1 year", strtotime($start)));
			$end_last_year = date("Y-m-t", strtotime("-1 year", strtotime($start)));
		} else {
			$start_last_year = date("Y-m-d", strtotime("-1 year", strtotime($start)));
			$end_last_year = date("Y-m-d", strtotime("-1 year", strtotime($end)));
		}
	}

	$week_text = ($history == "this_week" || $history == "last_week") ? " (vecka " . date('W', strtotime($start)) . ")" : "";
	$week_last_text = ($history == "this_week" || $history == "last_week") ? " (vecka " . date('W', strtotime($start_last_year)) . ")" : "";

	$period_text = ($start == $end) ? "Visar idag: $start" : "Visar perioden: $start till $end$week_text";
	echo "<div class=\"top20 bold span_blue\">$period_text</div>";

	if ($start_last_year === $end_last_year) {
		echo "<div class=\"top10 bottom10 italic\">Jämför med: $start_last_year</div>";
	} else {
		echo "<div class=\"top10 bottom10 italic\">Jämför med: $start_last_year till $end_last_year$week_last_text</div>";
	}
	
	$date_filter = "AND mio.updated >= '$start 00:00:00' AND mio.updated <= '$end 23:59:59'";
	$date_filter_last_year = "AND mio.updated >= '$start_last_year 00:00:00' AND mio.updated <= '$end_last_year 23:59:59'";

	function fetch_sales_data($date_filter) {
		$select  = "SELECT COUNT(o.c_order_id) AS antal, SUM(i.totallines) AS summa, ad.value, ad.firstname, ad.lastname ";
		$select .= "FROM m_inout mio ";
		$select .= "JOIN c_order o ON o.c_order_id=mio.c_order_id ";
		$select .= "JOIN ad_user ad ON ad.ad_user_id = o.salesrep_id ";
		$select .= "JOIN c_invoice i ON i.c_invoice_id=mio.c_invoice_id ";
		$select .= "WHERE mio.docstatus IN ('CO') AND mio.deliveryViaRule IN ('S','P') AND mio.isSOTrx = 'Y' ";
		$select .= "AND mio.isInDispute!='Y' and mio.isActive='Y' AND mio.AD_Client_ID=1000000 AND mio.M_rma_ID is null ";
		$select .= $date_filter . " GROUP BY ad.value, ad.firstname, ad.lastname ORDER BY summa DESC, ad.lastname ASC, ad.firstname ASC";
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$data = [];
		while ($res && $row = pg_fetch_object($res)) {
			$key = strtoupper(trim($row->value));
			$fullname = trim($row->firstname . ' ' . $row->lastname);

			$data[$key] = [
				'antal' => $row->antal,
				'summa' => $row->summa,
				'namn' => $row->firstname . ' ' . $row->lastname
			];
		}
		return $data;
	}

	$sales_now = fetch_sales_data($date_filter);
	$sales_last = fetch_sales_data($date_filter_last_year);
	$all_sellers = array_unique(array_merge(array_keys($sales_now), array_keys($sales_last)));

	$total_antal = $total_summa = $total_last_summa = $total_diff = 0;
	$webb_summa = $webb_summa_last = 0;
	$rowcolor = true;

	echo "<style>
	tr.firstrow:hover, tr.secondrow:hover { background-color: #eef; cursor: pointer; }
	.bar-container { width: 100%; background-color: #f1f1f1; border-radius: 4px; overflow: hidden; height: 20px; }
	.bar-fill { height: 100%; text-align: center; color: white; line-height: 20px; }
	</style>";

	echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">
	<tr>
		<td class=\"align_left\"><b>Säljare</b></td>
		<td class=\"align_center\"><b>Antal</b></td>
		<td class=\"align_center\"><b>Summa</b></td>
		<td class=\"align_center\"><b>Fjolår</b></td>
		<td class=\"align_center\"><b>Diff (kr)</b></td>
		<td class=\"align_center\"><b>Diff (%)</b></td>
	</tr>";

	foreach ($all_sellers as $seller) {
		$antal = isset($sales_now[$seller]) ? $sales_now[$seller]['antal'] : 0;
		$summa = isset($sales_now[$seller]) ? $sales_now[$seller]['summa'] : 0;
		$last_summa = isset($sales_last[$seller]) ? $sales_last[$seller]['summa'] : 0;
		$diff = $summa - $last_summa;
		if ($last_summa > 0) {
			$diff_pct = ($diff / $last_summa) * 100;
		} elseif ($summa > 0) {
			$diff_pct = 100;
		} else {
			$diff_pct = 0;
		}

		$total_antal += $antal;
		$total_summa += $summa;
		$total_last_summa += $last_summa;
		$total_diff += $diff;

		if (strtoupper(trim($seller)) === 'LITIUM') {
			$webb_summa = $summa;
			$webb_summa_last = $last_summa;
		}

		$backcolor = $rowcolor ? "firstrow" : "secondrow";
		$rowcolor = !$rowcolor;

		$diff_color = ($diff >= 0) ? 'style="color: green;"' : 'style="color: red;"';
		$diff_pct_color = ($diff_pct >= 0) ? 'style="color: green;"' : 'style="color: red;"';

		echo "<tr class=\"$backcolor\">
			<td align=\"left\">" . isset($sales_now[$seller]['namn']) ? $sales_now[$seller]['namn'] :
					(isset($sales_last[$seller]['namn']) ? $sales_last[$seller]['namn'] : $seller) . "</td>
			<td align=\"center\">$antal</td>
			<td align=\"right\">" . number_format($summa, 0, ',', ' ') . " SEK</td>
			<td align=\"right\">" . number_format($last_summa, 0, ',', ' ') . " SEK</td>
			<td align=\"right\" $diff_color>" . number_format($diff, 0, ',', ' ') . " SEK</td>
			<td align=\"right\" $diff_pct_color>" . number_format($diff_pct, 1, ',', ' ') . "%</td>
		</tr>";
	}

	$total_diff_pct = ($total_last_summa > 0) ? ($total_diff / $total_last_summa * 100) : 0;
	$total_diff_color = ($total_diff >= 0) ? 'style="color: green;"' : 'style="color: red;"';
	$total_diff_pct_color = ($total_diff_pct >= 0) ? 'style="color: green;"' : 'style="color: red;"';

	echo "<tr style=\"font-weight: bold; border-top: 2px solid #000;\">
		<td align=\"left\">Totalt</td>
		<td align=\"center\">$total_antal</td>
		<td align=\"right\">" . number_format($total_summa, 0, ',', ' ') . " SEK</td>
		<td align=\"right\">" . number_format($total_last_summa, 0, ',', ' ') . " SEK</td>
		<td align=\"right\" $total_diff_color>" . number_format($total_diff, 0, ',', ' ') . " SEK</td>
		<td align=\"right\" $total_diff_pct_color>" . number_format($total_diff_pct, 1, ',', ' ') . "%</td>
	</tr>";
	echo "</table><div style=\"clear: both;\"></div>";

	if (
		$history === 'this_month' &&
		(isset($_COOKIE['login_mail']) && (
			$_COOKIE['login_mail'] === 'stefan@cyberphoto.se' ||
			$_COOKIE['login_mail'] === 'albin@cyberphoto.se'
		))
	) {
		echo "<form method=\"post\" style=\"margin-top: 30px; padding: 15px; background: #f7f7f7; border: 1px solid #ccc; border-radius: 8px;\">
			<h3>Sätt månadens budget för $current_month_key</h3>
			<label for=\"budget\">Budget (SEK):</label>
			<input type=\"number\" name=\"budget\" id=\"budget\" value=\"" . htmlspecialchars(isset($budget_value) ? $budget_value : '') . "\" required>
			<button type=\"submit\">Spara</button>
		</form>";
	}

	// Summering för butikssäljare exklusive 'LITIUM'
	$butik_summa = $total_summa - $webb_summa;
	$butik_summa_last = $total_last_summa - $webb_summa_last;
	$butik_diff = $butik_summa - $butik_summa_last;
	$butik_diff_pct = ($butik_summa_last > 0) ? ($butik_diff / $butik_summa_last * 100) : 0;

	$butik_diff_color = ($butik_diff >= 0) ? 'style="color: green;"' : 'style="color: red;"';
	$butik_diff_pct_color = ($butik_diff_pct >= 0) ? 'style="color: green;"' : 'style="color: red;"';

	// Visuell ruta
	echo "<div class=\"top20 bottom10\" style=\"padding: 15px; border: 2px solid #ccc; border-radius: 8px; background-color: #f9f9f9;\">
		<h3 style=\"margin-top: 0;\">Försäljning (exkl. webb)</h3>
		<div><b>Totalt:</b> " . number_format($butik_summa, 0, ',', ' ') . " SEK</div>
		<div><b>Fjolår:</b> " . number_format($butik_summa_last, 0, ',', ' ') . " SEK</div>
		<div><b>Diff:</b> <span $butik_diff_color>" . number_format($butik_diff, 0, ',', ' ') . " SEK</span></div>
		<div><b>Diff (%):</b> <span $butik_diff_pct_color>" . number_format($butik_diff_pct, 1, ',', ' ') . "%</span></div>
	</div>";


	if ($history === 'this_month' && $budget_value !== null) {
		$budget_diff = $butik_summa - $budget_value;
		$budget_diff_pct = ($budget_value > 0) ? ($budget_diff / $budget_value * 100) : 0;
		$budget_color = ($budget_diff >= 0) ? 'style="color: green;"' : 'style="color: red;"';
		$budget_pct_color = ($budget_diff_pct >= 0) ? 'style="color: green;"' : 'style="color: red;"';

		echo "<div class=\"top20 bottom30\" style=\"padding: 15px; border: 2px solid #007acc; border-radius: 8px; background-color: #eef7ff;\">
			<h3 style=\"margin-top: 0;\">Måluppföljning  Säljare ($current_month_key)</h3>
			<div><b>Budget:</b> " . number_format($budget_value, 0, ',', ' ') . " SEK</div>
			<div><b>Utfall:</b> " . number_format($butik_summa, 0, ',', ' ') . " SEK</div>
			<div><b>Diff:</b> <span $budget_color>" . number_format($budget_diff, 0, ',', ' ') . " SEK</span></div>
			<div><b>Diff (%):</b> <span $budget_pct_color>" . number_format($budget_diff_pct, 1, ',', ' ') . " %</span></div>
		</div>";
	}


	// Visualisering för webb/säljare båda år
foreach ([
	['år' => date('Y', strtotime($start)), 'webb' => $webb_summa, 'total' => $total_summa],
	['år' => date('Y', strtotime($start_last_year)), 'webb' => $webb_summa_last, 'total' => $total_last_summa]
] as $data) {
	$webb_pct = ($data['total'] > 0) ? ($data['webb'] / $data['total']) * 100 : 0;
	$säljare_pct = 100 - $webb_pct;
	echo "<div class=\"top30\"><h2>{$data['år']}</h2>
	<div><b>Webb:</b> " . number_format($webb_pct, 2) . "%</div>
	<div class=\"bar-container\"><div class=\"bar-fill\" style=\"width: {$webb_pct}%; background-color: #007acc;\">&nbsp;</div></div>
	<div><b>Säljare:</b> " . number_format($säljare_pct, 2) . "%</div>
	<div class=\"bar-container\"><div class=\"bar-fill\" style=\"width: {$säljare_pct}%; background-color: #339933;\">&nbsp;</div></div></div>";
}

	echo "<form method=\"GET\" action=\"export_sales_report.php\" target=\"_blank\" style=\"margin-top: 20px;\">";
	echo "<input type=\"hidden\" name=\"history\" value=\"" . htmlspecialchars($history) . "\">";
	echo "<input type=\"hidden\" name=\"date_from\" value=\"" . htmlspecialchars($start) . "\">";
	echo "<input type=\"hidden\" name=\"date_to\" value=\"" . htmlspecialchars($end) . "\">";
	echo "<button type=\"submit\">Exportera till Excel</button>";
	echo "</form>";
}

	function getEURrate($idag_date = "",$NOK = false) {

		if ($idag_date == "") {
			$idag_date = date("Y-m-d", time());
		}

		$select = "SELECT multiplyrate  ";
		$select .= "FROM c_conversion_rate ";
		if ($NOK) {
			$select .= "WHERE c_currency_id = 287 AND c_currency_id_to = 311 AND validto = '$idag_date' ";
		} else {
			$select .= "WHERE c_currency_id = 102 AND c_currency_id_to = 311 AND validto = '$idag_date' ";
		}
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
			while ($res && $row = pg_fetch_row($res)) {

				return round($row[0],3);
			
			}

	}

// Hämtar summerad omsättning för ett visst datum (i SEK) baserat på pickdate
function getDailyTurnoverSummary($date)
{
    if ($date == '') {
        $date = date('Y-m-d');
    }

    // Postgres-connection (ADempiere)
    $db = Db::getConnectionAD(false);

    $dateSql = pg_escape_string($db, $date);

    // Växelkurser för dagen
    $eurRate = $this->getEURrate($date);
    $nokRate = $this->getEURrate($date, true);

	$sql  = "SELECT ";
	$sql .= "  x.deliveryviarule, ";
	$sql .= "  x.c_currency_id, ";
	$sql .= "  x.total ";
	$sql .= "FROM ( ";
	$sql .= "  SELECT DISTINCT inv.c_invoice_id, ";
	$sql .= "         mi.deliveryviarule, ";
	$sql .= "         inv.c_currency_id, ";
	$sql .= "         inv.totallines AS total ";
	$sql .= "  FROM m_inout mi ";
	$sql .= "  JOIN c_invoice inv ON inv.c_invoice_id = mi.c_invoice_id ";
	$sql .= "  WHERE mi.docstatus IN ('CO') ";
	$sql .= "    AND mi.deliveryviarule IN ('S','P') ";
	$sql .= "    AND mi.isSOTrx = 'Y' ";
	$sql .= "    AND mi.isInDispute != 'Y' ";
	$sql .= "    AND mi.isActive = 'Y' ";
	$sql .= "    AND mi.ad_client_id = 1000000 ";
	$sql .= "    AND mi.m_rma_id IS NULL ";
	$sql .= "    AND date(mi.pickdate) = '" . pg_escape_string($dateSql) . "' ";
	$sql .= ") x ";
	$sql .= "ORDER BY x.total DESC";
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $sql;
		exit;
	}

    $res = ($db) ? @pg_query($db, $sql) : false;

	$data = array(
		'date'               => $date,
		'total_orders'       => 0,
		'total_amount_sek'   => 0.0,

		'warehouse_orders'   => 0,
		'warehouse_amount'   => 0.0,

		// NYTT: speditör
		'ship_orders'        => 0,
		'ship_amount'        => 0.0,

		'se_orders'          => 0,
		'se_amount'          => 0.0,
		'fi_orders'          => 0,
		'fi_amount'          => 0.0,
		'no_orders'          => 0,
		'no_amount'          => 0.0,

		'avg_order_value'    => 0.0,
	);

    if ($res) {
        while ($res && $row = pg_fetch_assoc($res)) {
            $amount     = (float)$row['total'];
            $currencyId = (int)$row['c_currency_id'];
            $via        = $row['deliveryviarule'];

            // Omräkning till SEK
            if ($currencyId == 102) {          // EUR
                $amountSek = $amount * $eurRate;
            } elseif ($currencyId == 287) {    // NOK
                $amountSek = $amount * $nokRate;
            } else {                           // SEK
                $amountSek = $amount;
            }

            $data['total_orders']++;
            $data['total_amount_sek'] += $amountSek;

			if ($via === 'P') {
				// Lagershop
				$data['warehouse_orders']++;
				$data['warehouse_amount'] += $amountSek;
			} else {
				// Speditör
				$data['ship_orders']++;
				$data['ship_amount'] += $amountSek;
			}

            if ($currencyId == 102) {
                $data['fi_orders']++;
                $data['fi_amount'] += $amountSek;
            } elseif ($currencyId == 287) {
                $data['no_orders']++;
                $data['no_amount'] += $amountSek;
            } else {
                $data['se_orders']++;
                $data['se_amount'] += $amountSek;
            }
        }
    }

    if ($data['total_orders'] > 0) {
        $data['avg_order_value'] = $data['total_amount_sek'] / $data['total_orders'];
    }

    return $data;
}


// Räknar fram "motsvarande dag i fjol" givet ett datum
function getComparableDateLastYear($date)
{
    if ($date == '') {
        $date = date('Y-m-d');
    }

    // Manuell override-tabell
    $overrides = array(
        '2025-04-17' => '2024-03-28',
        '2025-04-22' => '2024-04-02',
        '2025-12-29' => '2024-12-27',
        '2026-01-02' => '2025-01-01',
    );

    // Om dagens datum finns i override-tabellen ? använd det direkt
    if (isset($overrides[$date])) {
        return $overrides[$date];
    }

    // Annars: ordinarie veckodagslogik
    $todayDay      = date('N', strtotime($date)); // 17
    $dayYearBefore = date('Y-m-d', strtotime('-1 year', strtotime($date)));

    switch ($todayDay) {
        case 1: $display = date('Y-m-d', strtotime('next monday',    strtotime($dayYearBefore))); break;
        case 2: $display = date('Y-m-d', strtotime('next tuesday',   strtotime($dayYearBefore))); break;
        case 3: $display = date('Y-m-d', strtotime('next wednesday', strtotime($dayYearBefore))); break;
        case 4: $display = date('Y-m-d', strtotime('next thursday',  strtotime($dayYearBefore))); break;
        case 5: $display = date('Y-m-d', strtotime('next friday',    strtotime($dayYearBefore))); break;
        case 6: $display = date('Y-m-d', strtotime('next saturday',  strtotime($dayYearBefore))); break;
        case 7:
        default:
            $display = date('Y-m-d', strtotime('next sunday', strtotime($dayYearBefore)));
            break;
    }

    return $display;
}


// Returnerar allt som behövs för mätaren: dagens total, fjolårets, diff osv.
function getDailyTurnoverComparison($date)
{
    // Dagens dag
    $todaySummary = $this->getDailyTurnoverSummary($date);
    $actualDate   = $todaySummary['date'];

    // Motsvarande dag i fjol
    $compareDate     = $this->getComparableDateLastYear($actualDate);
    $lastYearSummary = $this->getDailyTurnoverSummary($compareDate);

    $valuenow  = (float)$todaySummary['total_amount_sek'];
    $valuethen = (float)$lastYearSummary['total_amount_sek'];

    if ($valuethen == 0 && $valuenow == 0) {
        $diffAmount  = 0;
        $diffPercent = 0;
    } elseif ($valuethen == 0) {
        // Inget att jämföra med  betrakta allt som +100 %
        $diffAmount  = $valuenow;
        $diffPercent = 100;
    } else {
        $diffAmount  = $valuenow - $valuethen;
        $diffPercent = ($diffAmount / $valuethen) * 100;
    }

    return array(
        'date'            => $actualDate,
        'compare_date'    => $compareDate,

        'today_total'     => $valuenow,
        'last_year_total' => $valuethen,

        'diff_amount'     => $diffAmount,
        'diff_percent'    => $diffPercent,

        'today'           => $todaySummary,
        'last_year'       => $lastYearSummary,
    );
}

// Hämtar detaljerade utleveranser för ett visst datum
function getDailyDeliveriesDetails($date)
{
    $result = array();

    if ($date == '') {
        $date = date('Y-m-d');
    }
    $dateSql = pg_escape_string($date);

    // Växelkurser
    $EURrate = $this->getEURrate($date);
    $NOKrate = $this->getEURrate($date, true);

    // Bygger på din gamla SELECT, men:
    // - tar med c_invoice_id
    // - lev.nr plockar vi bort (ointressant här)
    $sql  = "SELECT ";
    $sql .= "  m_inout.c_invoice_id, ";
    $sql .= "  (SELECT documentno FROM c_order ";
    $sql .= "     WHERE c_order_id = m_inout.c_order_id) AS order_no, ";
    $sql .= "  m_inout.updated AS updated, ";
    $sql .= "  (SELECT value FROM ad_user ";
    $sql .= "     WHERE ad_user_id = m_inout.updatedby) AS user_code, ";
    $sql .= "  (SELECT totallines FROM c_invoice ";
    $sql .= "     WHERE c_invoice_id = m_inout.c_invoice_id) AS total, ";
    $sql .= "  (SELECT c_currency_id FROM c_invoice ";
    $sql .= "     WHERE c_invoice_id = m_inout.c_invoice_id) AS currency_id, ";
    $sql .= "  m_inout.deliveryviarule AS via ";
    $sql .= "FROM m_inout ";
    $sql .= "WHERE docstatus IN ('CO') ";
    $sql .= "  AND deliveryviarule IN ('S','P') ";
    $sql .= "  AND isSOTrx = 'Y' ";
    $sql .= "  AND isInDispute != 'Y' ";
    $sql .= "  AND isActive = 'Y' ";
    $sql .= "  AND AD_Client_ID = 1000000 ";
    $sql .= "  AND M_rma_ID IS NULL ";
    $sql .= "  AND date(pickdate) = '" . $dateSql . "' ";
    $sql .= "ORDER BY updated DESC";

    // Debug om du vill:
    // error_log($sql);

    $res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $sql) : false;
    if (!$res) {
        return $result;
    }
	
    $seenInvoices = array();

    while ($res && $row = pg_fetch_assoc($res)) {
        $invoiceId = (int)$row['c_invoice_id'];

        // Har vi redan behandlat den här fakturan? Hoppa över i så fall.
        if ($invoiceId > 0 && isset($seenInvoices[$invoiceId])) {
            continue;
        }
        $seenInvoices[$invoiceId] = true;

        $amount     = (float)$row['total'];
        $currencyId = (int)$row['currency_id'];

        $amountSek = $amount;
        $currency  = 'SEK';

        if ($currencyId == 102) {
            $amountSek = $amount * $EURrate;
            $currency  = 'EUR';
        } elseif ($currencyId == 287) {
            $amountSek = $amount * $NOKrate;
            $currency  = 'NOK';
        }

        $result[] = array(
            'order_no'   => $row['order_no'],
            'timestamp'  => $row['updated'],
            'user_code'  => $row['user_code'],
            'amount'     => $amount,
            'currency'   => $currency,
            'amount_sek' => $amountSek,
            'via'        => $row['via'],
        );
    }

    return $result;
}

    /**
     * Fokusprodukter:
     * - AD (PostgreSQL): produkter där p.is_spec13 = 'Y' + tillverkare + namn
     * - MariaDB: lager från m_product_cache för warehouse 1000000, SUM(qtyonhand)
     * - AD (PostgreSQL): sålda senaste X dagar från order (CO/CL)
     */
    public function getFocusProductsSimple($days = 30)
    {
        $days = (int)$days;
        if ($days <= 0) { $days = 30; }

        $dbAD = Db::getConnectionAD(false);

		// 1) Produkter + tillverkare + kategori
		$sqlProducts = "
			SELECT
				p.m_product_id,
				p.value AS artnr,
				p.name  AS product_name,
				COALESCE(mf.name, '') AS manufacturer_name,

				COALESCE(pc.name, 'Okategoriserat') AS category_name,
				COALESCE(pc.value, '') AS category_value,
				COALESCE(pc.sort_priority, 999999) AS category_sort


			FROM m_product p
			LEFT JOIN xc_manufacturer mf
				   ON mf.xc_manufacturer_id = p.xc_manufacturer_id

			LEFT JOIN m_product_category pc
				   ON pc.m_product_category_id = p.m_product_category_id

			WHERE p.isactive = 'Y'
			  AND p.is_spec13 = 'Y'

			ORDER BY COALESCE(pc.sort_priority,999999) DESC, COALESCE(pc.name,'Okategoriserat') ASC, p.value
		";

        $res = ($dbAD) ? @pg_query($dbAD, $sqlProducts) : false;
        if (!$res) {
            return array();
        }

        $rows = array();
        $productIds = array();

        while ($res && $r = pg_fetch_assoc($res)) {
            $pid = (int)$r['m_product_id'];
            $productIds[] = $pid;

            $man  = trim((string)$r['manufacturer_name']);
            $name = trim((string)$r['product_name']);
            $label = ($man !== '') ? ($man . ' ' . $name) : $name;

			$rows[$pid] = array(
				'm_product_id'   => $pid,
				'artnr'          => (string)$r['artnr'],
				'manufacturer'   => $man,
				'product_name'   => $name,
				'product_label'  => $label,

				'category_name'  => (string)$r['category_name'],
				'category_value' => (string)$r['category_value'],
				'category_sort'  => (int)$r['category_sort'],

				'onhand_qty'     => 0,
				'sold_30d'       => 0
			);

        }

        if (count($productIds) === 0) {
            return array();
        }

        // För pg_query_params ANY(int[]) använder vi int-array literal
        $in = array();
        foreach ($productIds as $pid) { $in[] = (int)$pid; }
        $arrLiteral = '{' . implode(',', $in) . '}';

        // 2) Lager från AD: SUM(qtyonhand) i warehouse 1000000
        $warehouseId = 1000000;

        $sqlStock = "
            SELECT
                s.m_product_id,
                COALESCE(SUM(s.qtyonhand), 0) AS qtyonhand
            FROM m_storage s
            JOIN m_locator l ON l.m_locator_id = s.m_locator_id
            WHERE l.m_warehouse_id = $1
              AND s.m_product_id = ANY($2::int[])
            GROUP BY s.m_product_id
        ";

        $resStock = ($dbAD) ? @pg_query_params($dbAD, $sqlStock, array($warehouseId, $arrLiteral)) : false;
        if ($resStock) {
            while ($resStock && $s = pg_fetch_assoc($resStock)) {
                $pid = (int)$s['m_product_id'];
                if (isset($rows[$pid])) {
                    $rows[$pid]['onhand_qty'] = (int)round((float)$s['qtyonhand']);
                }
            }
        }

        // 3) Sålda senaste X dagar (order CO/CL)
        $sqlSales = "
            SELECT
                ol.m_product_id,
                COALESCE(SUM(ol.qtyordered), 0) AS sold_qty
            FROM c_orderline ol
            JOIN c_order o ON o.c_order_id = ol.c_order_id
            WHERE o.issotrx = 'Y'
              AND o.docstatus IN ('CO','CL')
              AND o.dateordered >= (CURRENT_DATE - ($1::int * INTERVAL '1 day'))
              AND ol.m_product_id = ANY($2::int[])
            GROUP BY ol.m_product_id
        ";

        $resSales = ($dbAD) ? @pg_query_params($dbAD, $sqlSales, array($days, $arrLiteral)) : false;
        if ($resSales) {
            while ($resSales && $x = pg_fetch_assoc($resSales)) {
                $pid = (int)$x['m_product_id'];
                if (isset($rows[$pid])) {
                    $rows[$pid]['sold_30d'] = (int)round((float)$x['sold_qty']);
                }
            }
        }

        return array_values($rows);
    }

	public function exportFocusProductsSimpleCsv($rows)
	{
		$filename = 'fokusprodukter_' . date('YmdHi') . '.csv';

		if (ob_get_level()) {
			@ob_end_clean();
		}

		// Excel i Windows gillar detta bäst när den "gissar" ANSI
		header('Content-Type: text/csv; charset=ISO-8859-1');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		$out = fopen('php://output', 'w');

		// Semikolon i svensk Excel + tydlig hint
		fwrite($out, "sep=;\r\n");

		// Rubriker (ASCII-safe för att slippa encoding-bök i filen)
		fputcsv($out, array('Artnr', 'Produkt', 'Kategori', 'Lagersaldo', 'Solda 30d'), ';');

		// Helper: konvertera text till latin1 om den råkar vara UTF-8
		$to_latin1 = function ($s) {
			$s = (string)$s;
			if (preg_match('//u', $s)) {
				return $s;
			}
			return $s;
		};

		foreach ($rows as $r) {
			$artnr = (string)$r['artnr'];

			$prod = $to_latin1(isset($r['product_label']) ? $r['product_label'] : '');
			$cat  = $to_latin1(isset($r['category_name']) ? $r['category_name'] : '');

			// sanera radbryt
			$prod = str_replace(array("\r", "\n"), ' ', $prod);
			$cat  = str_replace(array("\r", "\n"), ' ', $cat);

			fputcsv($out, array(
				$artnr,
				$prod,
				$cat,
				(int)$r['onhand_qty'],
				(int)$r['sold_30d']
			), ';');
		}

		fclose($out);
		exit;
	}

public function getNewProductsForPage($daysBack = 14)
{
    $daysBack = (int)$daysBack;
    if ($daysBack <= 0) { $daysBack = 14; }

    $dbAD = Db::getConnectionAD(false);

    // Leverantör + min/max + inköpare enligt drawer_details-logiken
    // Viktigt krav: sidan visar INTE produkter där launchdate inte passerat.
    $sql = "
        SELECT
            p.m_product_id,
            p.value AS artnr,
            p.name  AS description,
            COALESCE(mf.name, '') AS manufacturer,
            p.launchdate AS launch_ts,

            COALESCE(bp.name, '')      AS supplier_name,
            COALESCE(rep.level_min, 0) AS min_level,
            COALESCE(rep.level_max, 0) AS max_level,
            COALESCE(au_buyer.name, '') AS buyer_name

        FROM m_product p
        LEFT JOIN xc_manufacturer mf
               ON mf.xc_manufacturer_id = p.xc_manufacturer_id

        LEFT JOIN m_product_po mpo
               ON mpo.m_product_id = p.m_product_id
              AND mpo.isactive = 'Y'
              AND mpo.iscurrentvendor = 'Y'

        LEFT JOIN c_bpartner bp
               ON bp.c_bpartner_id = mpo.c_bpartner_id

        LEFT JOIN m_replenish rep
               ON rep.m_product_id = mpo.m_product_id

        LEFT JOIN ad_user au_buyer
               ON au_buyer.ad_user_id = bp.salesrep_id

        WHERE p.isactive = 'Y'
          AND p.launchdate IS NOT NULL
          AND p.launchdate <= NOW()
          AND p.launchdate >= (NOW() - ($1::int * INTERVAL '1 day'))

		  AND p.iswebstoreproduct = 'Y'
		  AND COALESCE(p.istradein,'N') <> 'Y'
		  AND COALESCE(p.demo_product,'N') <> 'Y'

        ORDER BY p.launchdate DESC, p.value
    ";

    $res = ($dbAD) ? @pg_query_params($dbAD, $sql, array($daysBack)) : false;
    if (!$res) { return array(); }

    $out = array();
    while ($res && $r = pg_fetch_assoc($res)) {
        $ts = (string)$r['launch_ts'];

        $out[] = array(
            'm_product_id' => (int)$r['m_product_id'],
            'launch_date'  => $ts ? date('Y-m-d H:i', strtotime($ts)) : '',
            'artnr'        => (string)$r['artnr'],
            'manufacturer' => (string)$r['manufacturer'],
            'description'  => (string)$r['description'],
            'supplier'     => (string)$r['supplier_name'],
            'buyer'        => (string)$r['buyer_name'],
            'min_stock'    => (int)$r['min_level'],
            'max_stock'    => (int)$r['max_level'],
        );
    }

    return $out;
}

public function getNewProductsForMail($hoursBack = 24)
{
    $hoursBack = (int)$hoursBack;
    if ($hoursBack <= 0) { $hoursBack = 24; }

    $dbAD = Db::getConnectionAD(false);

    $sql = "
        SELECT
            p.m_product_id,
            p.value AS artnr,
            p.name  AS description,
            COALESCE(mf.name, '') AS manufacturer,
            p.launchdate AS launch_ts,

            COALESCE(bp.name, '')       AS supplier_name,
            COALESCE(rep.level_min, 0)  AS min_level,
            COALESCE(rep.level_max, 0)  AS max_level,
            COALESCE(au_buyer.name, '') AS buyer_name

        FROM m_product p
        LEFT JOIN xc_manufacturer mf
               ON mf.xc_manufacturer_id = p.xc_manufacturer_id

        LEFT JOIN m_product_po mpo
               ON mpo.m_product_id = p.m_product_id
              AND mpo.isactive = 'Y'
              AND mpo.iscurrentvendor = 'Y'

        LEFT JOIN c_bpartner bp
               ON bp.c_bpartner_id = mpo.c_bpartner_id

        LEFT JOIN m_replenish rep
               ON rep.m_product_id = mpo.m_product_id

        LEFT JOIN ad_user au_buyer
               ON au_buyer.ad_user_id = bp.salesrep_id

        WHERE p.isactive = 'Y'
          AND p.iswebstoreproduct = 'Y'
          AND COALESCE(p.istradein,'N') <> 'Y'
          AND COALESCE(p.demo_product,'N') <> 'Y'
          AND p.launchdate IS NOT NULL

          -- Viktigt: mail ska ta nylanserade senaste X timmar
          AND p.launchdate <= NOW()
          AND p.launchdate > (NOW() - ($1::int * INTERVAL '1 hour'))

		ORDER BY
		  COALESCE(bp.name,'') ASC,
		  p.launchdate DESC,
		  COALESCE(mf.name,'') ASC,
		  p.name ASC,
		  p.value ASC
    ";

    $res = ($dbAD) ? @pg_query_params($dbAD, $sql, array($hoursBack)) : false;
    if (!$res) { return array(); }

    $out = array();
    while ($res && $r = pg_fetch_assoc($res)) {
        $ts = (string)$r['launch_ts'];
        $out[] = array(
            'm_product_id'  => (int)$r['m_product_id'],
            'launch_date'   => $ts ? date('Y-m-d H:i', strtotime($ts)) : '',
            'artnr'         => (string)$r['artnr'],
            'manufacturer'  => (string)$r['manufacturer'],
            'description'   => (string)$r['description'],
            'supplier'      => (string)$r['supplier_name'],
            'buyer'         => (string)$r['buyer_name'],
            'min_stock'     => (int)$r['min_level'],
            'max_stock'     => (int)$r['max_level'],
        );
    }

    return $out;
}

public function exportNewProductsCsvLatin1($rows, $filename = null)
{
    if ($filename === null || $filename === '') {
        $filename = 'nya_produkter_' . date('YmdHi') . '.csv';
    }

    if (ob_get_level()) { @ob_end_clean(); }

    header('Content-Type: text/csv; charset=ISO-8859-1');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $out = fopen('php://output', 'w');
    fwrite($out, "sep=;\r\n");

    // ASCII-safe rubriker (minimerar encoding-strul)
    fputcsv($out, array(
        'Datum','Artnr','Tillverkare','Beskrivning','Leverantor','Inkopare','Minlagersaldo','Maxlagersaldo'
    ), ';');

    $to_latin1 = function ($s) {
        $s = (string)$s;
        if (preg_match('//u', $s)) { return $s; }
        return $s;
    };

    foreach ($rows as $r) {
        $mf   = $to_latin1($r['manufacturer']);
        $desc = $to_latin1($r['description']);
        $sup  = $to_latin1($r['supplier']);
        $buy  = $to_latin1($r['buyer']);

        $mf   = str_replace(array("\r","\n"), ' ', $mf);
        $desc = str_replace(array("\r","\n"), ' ', $desc);
        $sup  = str_replace(array("\r","\n"), ' ', $sup);
        $buy  = str_replace(array("\r","\n"), ' ', $buy);

        fputcsv($out, array(
            (string)$r['launch_date'],
            (string)$r['artnr'],
            $mf,
            $desc,
            $sup,
            $buy,
            (int)$r['min_stock'],
            (int)$r['max_stock'],
        ), ';');
    }

    fclose($out);
    exit;
}

public function buildNewProductsCsvLatin1String($rows)
{
    $to_latin1 = function ($s) {
        $s = (string)$s;
        if (preg_match('//u', $s)) { return $s; }
        return $s;
    };

    $csv  = "sep=;\r\n";
    $csv .= "Datum;Artnr;Tillverkare;Beskrivning;Leverantor;Inkopare;Minlagersaldo;Maxlagersaldo\r\n";

    foreach ($rows as $r) {
        $mf   = $to_latin1($r['manufacturer']);
        $desc = $to_latin1($r['description']);
        $sup  = $to_latin1($r['supplier']);
        $buy  = $to_latin1($r['buyer']);

        $mf   = str_replace(array("\r","\n"), ' ', $mf);
        $desc = str_replace(array("\r","\n"), ' ', $desc);
        $sup  = str_replace(array("\r","\n"), ' ', $sup);
        $buy  = str_replace(array("\r","\n"), ' ', $buy);

        $csv .= (string)$r['launch_date'] . ';'
             . (string)$r['artnr'] . ';'
             . $mf . ';'
             . $desc . ';'
             . $sup . ';'
             . $buy . ';'
             . (int)$r['min_stock'] . ';'
             . (int)$r['max_stock']
             . "\r\n";
    }

    return $csv;
}

public function getDemoProductsMissingSupplier($missingSupplierCode = '1004141', $warehouseId = 1000000)
{
    $warehouseId = (int)$warehouseId;
    $missingSupplierCode = (string)$missingSupplierCode;

    $dbAD = Db::getConnectionAD(false);

    // Hittar DEMO-produkter som har "Saknar leverantör" som current vendor
    // och som har saldo på lager.
    $sql = "
        SELECT
            p.m_product_id,
            p.value AS artnr,
            COALESCE(mf.name, '') AS manufacturer,
            p.name AS description,
            COALESCE(bp.name, '') AS supplier_name,
            COALESCE(bp.value, '') AS supplier_code,
            COALESCE(pc.qtyonhand, 0) AS qty_onhand
        FROM m_product_cache pc
        JOIN m_product p
          ON p.m_product_id = pc.m_product_id

        LEFT JOIN xc_manufacturer mf
          ON mf.xc_manufacturer_id = p.xc_manufacturer_id

        LEFT JOIN m_product_po mpo
          ON mpo.m_product_id = p.m_product_id
         AND mpo.isactive = 'Y'
         AND mpo.iscurrentvendor = 'Y'

        LEFT JOIN c_bpartner bp
          ON bp.c_bpartner_id = mpo.c_bpartner_id

        WHERE pc.m_warehouse_id = $1
          AND COALESCE(pc.qtyonhand,0) > 0

          AND p.isactive = 'Y'
          AND COALESCE(p.demo_product,'N') = 'Y'
          AND COALESCE(p.istradein,'N') <> 'Y'

          AND bp.value = $2

        ORDER BY mf.name, p.name, p.value
    ";

    $res = ($dbAD) ? @pg_query_params($dbAD, $sql, array($warehouseId, $missingSupplierCode)) : false;
    if (!$res) {
        return array();
    }

    $out = array();
    while ($res && $r = pg_fetch_assoc($res)) {
        $out[] = array(
            'm_product_id'   => (int)$r['m_product_id'],
            'artnr'          => (string)$r['artnr'],
            'manufacturer'   => (string)$r['manufacturer'],
            'description'    => (string)$r['description'],
            'supplier_name'  => (string)$r['supplier_name'],
            'supplier_code'  => (string)$r['supplier_code'],
            'qty_onhand'     => (int)$r['qty_onhand'],
        );
    }

    return $out;
}


}
?>
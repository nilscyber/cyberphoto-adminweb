<?php
// export_sales_report.php

$start = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-d');
$end = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

$filename = "forsaljningsrapport_{$start}_{$end}.csv";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"$filename\"");

$history = isset($_GET['history']) ? $_GET['history'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

require_once('Db.php'); // Anpassa till din faktiska anslutningsfil

function fetch_sales_data_export($date_filter) {
	$select  = "SELECT COUNT(o.c_order_id) AS antal, SUM(i.totallines) AS summa, ad.value, ad.firstname, ad.lastname ";
	$select .= "FROM m_inout mio ";
	$select .= "JOIN c_order o ON o.c_order_id=mio.c_order_id ";
	$select .= "JOIN ad_user ad ON ad.ad_user_id = o.salesrep_id ";
	$select .= "JOIN c_invoice i ON i.c_invoice_id=mio.c_invoice_id ";
	$select .= "WHERE mio.docstatus IN ('CO') AND mio.deliveryViaRule IN ('S','P') AND mio.isSOTrx = 'Y' ";
	$select .= "AND mio.isInDispute!='Y' and mio.isActive='Y' AND mio.AD_Client_ID=1000000 AND mio.M_rma_ID is null ";
	$select .= $date_filter . " GROUP BY ad.value, ad.firstname, ad.lastname ORDER BY summa DESC, ad.value DESC";
	$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
	$data = [];
	while ($res && $row = pg_fetch_object($res)) {
		$key = $row->value;
		$namn = trim($row->firstname . ' ' . $row->lastname);
		$data[$key] = [
			'antal' => $row->antal,
			'summa' => $row->summa,
			'namn'  => $namn
		];
	}
	return $data;
}

// Bestäm datumfilter
$date_filter = "AND mio.updated >= '$date_from 00:00:00' AND mio.updated <= '$date_to 23:59:59'";

if ($date_from == $date_to) {
	// Enkel -1 år för samma datum (ingen helgdagsjustering här)
	$start_last_year = date("Y-m-d", strtotime("-1 year", strtotime($date_from)));
	$end_last_year = $start_last_year;
} elseif ($history === 'this_month') {
	// Hela månaden året innan
	$start_last_year = date("Y-m-01", strtotime("-1 year", strtotime($date_from)));
	$end_last_year   = date("Y-m-t", strtotime("-1 year", strtotime($date_from)));
} else {
	// Standardfall: -1 år för start och slut
	$start_last_year = date("Y-m-d", strtotime("-1 year", strtotime($date_from)));
	$end_last_year   = date("Y-m-d", strtotime("-1 year", strtotime($date_to)));
}

$date_filter_last_year = "AND mio.updated >= '$start_last_year 00:00:00' AND mio.updated <= '$end_last_year 23:59:59'";

$sales_now = fetch_sales_data_export($date_filter);
$sales_last = fetch_sales_data_export($date_filter_last_year);
$all_sellers = array_unique(array_merge(array_keys($sales_now), array_keys($sales_last)));

$output = fopen('php://output', 'w');

// Rubriker
fputcsv($output, ['Säljare', 'Antal', 'Summa', 'Fjolår', 'Diff (kr)', 'Diff (%)']);

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

	// Fullständigt namn från nu eller fjolår, fallback till seller-id
	$namn = isset($sales_now[$seller]['namn']) ? $sales_now[$seller]['namn'] :
	        (isset($sales_last[$seller]['namn']) ? $sales_last[$seller]['namn'] : strtoupper($seller));

	fputcsv($output, [
		$namn,
		$antal,
		round($summa),
		round($last_summa),
		round($diff),
		round($diff_pct, 1)
	]);
}

fclose($output);
exit;
?>

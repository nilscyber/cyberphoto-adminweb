<?php

require_once("CCheckIpNumber.php");
require_once("Db.php");
// require_once("CBasket.php");
// $bask = new CBasket();

Class CCPto {

	/*
	var $conn_my;
	var $conn_my2;
	var $conn_ad;
	*/

	function __construct() {

		/*
		$this->conn_my = Db::getConnection();
		$this->conn_my2 = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		@mysqli_select_db($this->conn_my2, "cyberadmin");
		$this->conn_ad = Db::getConnectionAD();
		*/

	}

	function getIncommingOrders() {

		$totalsum = 0;
		$totalcount = 0;

		$select  = "SELECT count(*) AS qty, COALESCE(round(sum(o.totallines_sek),0), 0) AS sumGrandTotalSEK, isWebOrder ";
		$select .= "FROM v_c_order_report o ";
		$select .= "WHERE o.issotrx='Y' AND o.created > current_date - integer '0' ";
		$select .= "GROUP BY 3 order by 3 DESC ";

		// echo $select;

		// $res = pg_query($this->conn_ad, $select);
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

		while ($res && $row = pg_fetch_array($res)) {

			$totalsum = $totalsum + $row['sumgrandtotalsek'];
			$totalcount = $totalcount + $row['qty'];

		}

		if ($totalcount > 0) {
			$snittordervardet = $totalsum / $totalcount;
		}

		echo "<div id=\"incontainer\">" . number_format($totalcount, 0, ',', ' ') . "</div>\n";
		echo "<div id=\"incontainer\">" . number_format($totalsum, 0, ',', ' ') . "</div>\n";
		echo "<div id=\"incontainer\">" . number_format($snittordervardet, 0, ',', ' ') . "</div>\n";

	}

	function getOutgoingOrders($only_out_value = false, $simple = false) {
		// global $dagensdatum;

		$desiderow = true;
		$countrows = 0;
		$totsum = 0;
		$totsumLagershop = 0;
		$count_lagershop = 0;
		$count_SE = 0;
		$totsumSE = 0;
		$totsumFI = 0;
		$count_FI = 0;
		$totsumNO = 0;
		$count_NO = 0;
		$dagensdatum = date("Y-m-d", time());
		/*
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		*/
		$EURrate = $this->getEURrate($dagensdatum);
		$NOKrate = $this->getEURrate($dagensdatum,true);

		$select = "SELECT (select documentno from c_order where c_order_id=m_inout.c_order_id) as ordernr, updated, (select value from ad_user where ad_user_id=m_inout.updatedby) as user, (select totallines from c_invoice where c_invoice_id=m_inout.c_invoice_id) as total, (select c_currency_id from c_invoice where c_invoice_id=m_inout.c_invoice_id) as currency, deliveryViaRule, documentno ";
		$select .= "FROM m_inout WHERE docstatus IN ('CO') AND deliveryViaRule IN ('S','P') ";
		$select .= "AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select .= "AND AD_Client_ID=1000000 AND M_rma_ID is null ";
		$select .= "AND date(updated)>='" . $dagensdatum . " 00:00:00' AND date(updated)<='" . $dagensdatum . " 23:59:59' ";
		$select .= "ORDER BY updated DESC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.44x") {
			echo $select;
			exit;
		}

		// $res = pg_query($this->conn_ad, $select);
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

			while ($res && $row = pg_fetch_row($res)) {

				if ($row[4] == 102) {
					$omraknat = $EURrate * $row[3];
				}
				if ($row[4] == 287) {
					$omraknatNO = $NOKrate * $row[3];
				}
				if ($row[5] == "P") {
					$count_lagershop++;
					$totsumLagershop = $totsumLagershop + $row[3];
				}
				if ($row[4] == 102) {
					$count_FI++;
					$totsumFI = $totsumFI + $omraknat;
					$totsum = $totsum + $omraknat;
				} elseif ($row[4] == 287) {
					$count_NO++;
					$totsumNO = $totsumNO + $omraknatNO;
					$totsum = $totsum + $omraknatNO;
				} else {
					$totsum = $totsum + $row[3];
				}
				$omraknat = 0;
				$countrows++;
			}

		if ($countrows > 0) {
			$snittordervardet = $totsum / $countrows;
		}

		if ($simple) {
			return number_format($totsum, 0, ',', ' ');
		} elseif ($only_out_value) {
			return round($totsum,0);
		} else {
			echo "<div class=\"top10\"></div>\n";
			echo "<div id=\"outcontainer\">" . number_format($countrows, 0, ',', ' ') . "</div>\n";
			echo "<div id=\"outcontainer\">" . number_format($totsum, 0, ',', ' ') . "</div>\n";
			echo "<div id=\"outcontainer\">" . number_format($snittordervardet, 0, ',', ' ') . "</div>\n";
		}

	}

	function getEURrate($idag_date, $NOK = false) {

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

		// $res = pg_query($this->conn_ad, $select);
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

		while ($res && $row = pg_fetch_row($res)) {

			return round($row[0],3);

		}

	}

	function getStoreValue($ongoing, $simple = false) {

		$select = "SELECT stockvalue_Value ";
		$select .= "FROM cyberadmin.stockvalue ";
		if ($ongoing || $simple) {
			$select .= "WHERE stockvalue_Type = 2 "; // l�pande
		} else {
			$select .= "WHERE stockvalue_Type = 0 "; // fast
		}
		$select .= "ORDER BY stockvalue_Date DESC ";
		$select .= "LIMIT 1 ";

		// $res = mysqli_query($this->conn_my2, $select);
		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)) {

					extract($row);

					if ($simple) {
						return number_format($stockvalue_Value, 0, ',', ' ');
					} elseif ($ongoing) {

						$getYesterdayStoreValue = $this->getYesterdayStoreValue();
						$storeValueDiff = $stockvalue_Value - $getYesterdayStoreValue;
						if ($storeValueDiff > 100000) {
							$flag = 'red';
						} elseif ($storeValueDiff < -100000) {
							$flag = 'green';
						} else {
							$flag = 'neutral';
						}

						if ($flag == 'red') {
							echo "<div class=\"top10\"></div>\n";
							echo "<div id=\"redcontainer\">" . number_format($stockvalue_Value, 0, ',', ' ') . "</div>\n";
						} elseif ($flag == 'green') {
							echo "<div class=\"top10\"></div>\n";
							echo "<div id=\"greencontainer\">" . number_format($stockvalue_Value, 0, ',', ' ') . "</div>\n";
						} else {
							echo "<div class=\"top10\"></div>\n";
							echo "<div class=\"plaincontainer\">" . number_format($stockvalue_Value, 0, ',', ' ') . "</div>\n";
						}

					} else {

						echo "<div class=\"top10\"></div>\n";
						echo "<div class=\"plaincontainer\">" . number_format($stockvalue_Value, 0, ',', ' ') . "</div>\n";

					}

				}

			} else {

				echo "";

			}

	}

	function getYesterdayStoreValue() {

		$select = "SELECT stockvalue_Value ";
		$select .= "FROM cyberadmin.stockvalue ";
		$select .= "WHERE stockvalue_Type = 0 "; // fast
		$select .= "ORDER BY stockvalue_Date DESC ";
		$select .= "LIMIT 1 ";

		// $res = mysqli_query($this->conn_my2, $select);
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
		return $row->stockvalue_Value;

	}

	function getPrintedOrders() {

		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL ";

		// $res = pg_query($this->conn_ad, $select);
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

		echo "<div class=\"top10\"></div>\n";
		echo "<div class=\"plaincontainer\">$row->antal</div>\n";

	}

	function getNotPrintedOrders() {

		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";

		// $res = pg_query($this->conn_ad, $select);
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

		echo "<div class=\"plaincontainer\">$row->antal</div>\n";

	}

}

?>

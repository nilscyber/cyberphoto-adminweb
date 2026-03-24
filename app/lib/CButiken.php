<?php

include_once 'Db.php'; 

Class CButiken {


	function __construct() {

	}

	function getDaysLeft($dateto) {

		$now = time();
		$timeto = strtotime($dateto);
		$diff = $timeto - $now;
		$sek = $diff % 60;
		$min = ($diff / 60) % 60;
		$hour = ($diff / 3600);
		$days = ($diff / 86400);
		$days = floor($days);
		$days = round($days, 0);
		return $days;
	}

	function getKoSystemInfo() {
		global $fi, $sv, $no;
	
		$select  = "SELECT ks.koNR ";
		$select .= "FROM cyberadmin.kosystem ks ";
		$select .= "ORDER BY ks.koID DESC ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
	
		$rows = mysqli_fetch_object($res);

		return $rows;
	
	}

	function addKoNumber($konumber,$kassa) {
	
		$todaydate = date("Y-m-d H:i:s", time());
		
		if ($kassa != "") {
			$kassnr = $kassa;
		} elseif ($_SERVER['REMOTE_ADDR'] == "192.168.1.240") {
			$kassnr = 1;
		} elseif ($_SERVER['REMOTE_ADDR'] == "192.168.1.241") {
			$kassnr = 2;
		} elseif ($_SERVER['REMOTE_ADDR'] == "192.168.1.242") {
			$kassnr = 3;
		} else {
			$kassnr = 0;
		}
		/*
		if ($kassa != "") {
			$kassnr = $kassa;
		} else {
			$kassnr = 0;
		}
		*/
		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.kosystem ";
		$select .= "ORDER BY koID DESC ";
		
		$updt  = "INSERT INTO cyberadmin.kosystem ";
		$updt .= "(koNR, koTIME, koKASSA) ";
		$updt .= "VALUES ";
		if ($konumber != "") {
			$updt .= "('$konumber','$todaydate','$kassnr') ";
		} else {
			$updt .= "((SELECT koNR+1 FROM cyberadmin.kosystem ALIASTABLE ORDER BY koID DESC LIMIT 1),'$todaydate','$kassnr') ";
		}

		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);
	
	}

	function getLastKoInput($bigscreen = false) {
	
		$todaydate = date("Y-m-d", time());
		
		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.kosystem ";
		$select .= "ORDER BY koID DESC ";
		
		// echo $select;
		// exit;
	
		$res = @mysqli_query(Db::getConnection(false), $select);
		$rows = mysqli_fetch_object($res);
	
		$shownumber = intval(substr($rows->koNR, -2));
		if ($shownumber == 00) {
			$shownumber = 0;
		} elseif ($shownumber == 01) {
			$shownumber = 1;
		} elseif ($shownumber == 02) {
			$shownumber = 2;
		} elseif ($shownumber == 03) {
			$shownumber = 3;
		} elseif ($shownumber == 04) {
			$shownumber = 4;
		} elseif ($shownumber == 05) {
			$shownumber = 5;
		} elseif ($shownumber == 06) {
			$shownumber = 6;
		} elseif ($shownumber == 7) {
			$shownumber = 7;
		} elseif ($shownumber == 8) {
			$shownumber = 8;
		} elseif ($shownumber == 9) {
			$shownumber = 9;
		}
		// if ($bigscreen && $not == "now") {
		if ($bigscreen) {
				if ($rows->koKASSA > 0) {
					if ((time() - strtotime($rows->koTIME)) < 4) {
						return "<div class=\"ko_nr\">" . $shownumber . "</div><div class=\"ko_kassa blink_text_white\">Kassa " . $rows->koKASSA . "</div>";
					} elseif ((time() - strtotime($rows->koTIME)) < 15) {
						return "<div class=\"ko_nr\">" . $shownumber . "</div><div class=\"ko_kassa\">Kassa " . $rows->koKASSA . "</div>";
					} else {
						return "<div class=\"ko_nr\">" . $shownumber . "</div>";
					}
				} else {
					return "<div class=\"ko_nr\">" . $shownumber . "</div>";
				}
		} else {
			// return "<div class=\"ko_nr\">" . $shownumber . "</div>";
			return $shownumber;
		}
	
	}
	
	function addDorrPling() {
	
		$todaydate = date("Y-m-d H:i:s", time());
		
		$updt  = "INSERT INTO cyberadmin.dorrpling ";
		$updt .= "(plingTime) ";
		$updt .= "VALUES ";
		$updt .= "('$todaydate') ";

		$res = mysqli_query(Db::getConnection(true), $updt);
	
	}

	function butikenHelp() {

		// echo $_SERVER['PHP_SELF'];
		if (preg_match("/help_call/", $_SERVER['PHP_SELF'])) {
			$updt  = "UPDATE cyberadmin.deskhelp ";
			$updt .= "SET dhValue = 1 ";
			$updt .= "WHERE dhID = 1 ";
			$res = mysqli_query(Db::getConnection(true), $updt);
		} else {
			$updt  = "UPDATE cyberadmin.deskhelp ";
			$updt .= "SET dhValue = 0 ";
			$updt .= "WHERE dhID = 1 ";
			$res = mysqli_query(Db::getConnection(true), $updt);
		}
	
	}

	function getPlingButiken() {
		global $fi, $sv, $no;
	
		$select  = "SELECT DATE_FORMAT(plingTime,'%Y-%m') AS Datum, COUNT(plingID) AS Antal ";
		$select .= "FROM cyberadmin.dorrpling ";
		$select .= "WHERE plingTime >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 12 MONTH), '%Y-%m-01') ";
		$select .= "GROUP BY DATE_FORMAT(plingTime,'%Y-%m') ";
		$select .= "ORDER BY Datum DESC ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo mysqli_num_rows($res);
			// exit;
		}
	
		// $row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
		
			echo "<div class=\"count_data bold italic\">Pling i butiken, per månad</div>\n";
			echo "<table id=\"begg_saleready\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($row = mysqli_fetch_object($res)) {

				echo "\t<tr>";
				echo "\t\t<td width=\"80\" class=\"mark_black\">$row->Datum</td>\n";
				echo "\t\t<td class=\"mark_black\">$row->Antal</td>\n";
				echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			/*
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
			*/
		
		}

	}

	function getTurnoverButiken($year=false) {
		global $dagensdatum;
		
		$countrow = 0;

		if ($year) {
			$select = "SELECT to_char(io.updated,'YYYY') AS DateFrom, COUNT(io.deliveryViaRule) AS Antal, SUM((select totallines from c_invoice where c_invoice_id=io.c_invoice_id)) as total ";
		} else {
			$select = "SELECT to_char(io.updated,'YYYY-MM') AS DateFrom, COUNT(io.deliveryViaRule) AS Antal, SUM((select totallines from c_invoice where c_invoice_id=io.c_invoice_id)) as total ";
		}
		$select .= "FROM m_inout io ";
		$select .= "JOIN c_order o ON o.c_order_id = io.c_order_id ";
		$select .= "WHERE io.docstatus IN ('CO') AND io.deliveryViaRule IN ('S','P') AND io.isSOTrx = 'Y' AND io.isInDispute!='Y' and io.isActive='Y' AND io.AD_Client_ID=1000000 AND io.M_rma_ID is null AND io.deliveryViaRule = 'P' ";
		// $select .= "AND io.updated > CURRENT_TIMESTAMP - INTERVAL '90 days' ";
		// $select .= "AND io.updated < '2023-05-22 00:00:00' ";
		// $select .= "AND NOT o.salesrep_id = 1000051 ";
		$select .= "AND NOT (o.salesrep_id = 1000051 AND io.updated > '2023-05-20 00:00:00' AND io.updated < '2024-11-13 23:59:59') ";
		$select .= "AND io.updated >= date_trunc('month', CURRENT_DATE) - INTERVAL '12 months' ";
		$select .= "GROUP BY DateFrom ";
		$select .= "ORDER BY DateFrom DESC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}
		
		
		if ($res && pg_num_rows($res) > 0) {
		
			if ($year) {
				echo "<div class=\"count_data bold italic\">Omsättning i butiken, per år</div>\n";
			} else {
				echo "<div class=\"count_data bold italic\">Omsättning i butiken, per månad</div>\n";
			}
			echo "<table id=\"begg_saleready\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($res && $row = pg_fetch_object($res)) {

				echo "\t<tr>";
				echo "\t\t<td width=\"80\" class=\"mark_black\">$row->datefrom</td>\n";
				echo "\t\t<td width=\"80\" class=\"mark_black\">$row->antal</td>\n";
				echo "\t\t<td class=\"mark_black\">" . round($row->total,0) . "</td>\n";
				echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
		
		}
			
	}

}

?>

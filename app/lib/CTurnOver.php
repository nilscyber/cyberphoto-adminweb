<?php

Class CTurnOver {
	var $conn_ms; 
	var $conn_my; 
	var $conn_fi;
	var $conn_ad;

	function __construct() {
		global $fi;
			
	}

	function getOrdersFromAD() {

		// $select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL ";
		//echo $select;
		// exit;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				echo $row->antal;
				
			} else {
			
				echo "0";
			
			}

	}

	function driveOutExists() {

		$select = "SELECT * FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL AND 
				   M_FreightCategory_ID=1000023 ";

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				return true;
				
			} else {
				return false;
			
			}

	}
	function getNotPrintedDriveOut() {

		// $select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 AND ic.M_FreightCategory_ID = 1000023 ";
		//echo $select;
		// exit;

		$res = (Db::getConnection()) ? @pg_query(Db::getConnection(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				return $row->antal;
				
			} else {
			
				return 0;
			
			}

	}
	function getNotPrintedExpress() {
		// $select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.M_FreightCategory_ID = 1000012  AND ic.AD_Client_ID=1000000 ";
		//echo $select;
		// exit;

		$res = (Db::getConnection()) ? @pg_query(Db::getConnection(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				return $row->antal;
				
			} else {
			
				return 0;
			
			}	
	}
	function getOrdersFromADNew() {

		// $select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL ";
		//echo $select;
		// exit;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;
		
			if ($res && pg_num_rows($res) > 0) {
				
				return $row->antal;
				
			} else {
			
				return 0;
			
			}

	}
	function getOrdersFromADNB() {

		// $select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL ";
		//echo $select;
		// exit;

		$res = (Db::getConnectionNB()) ? @pg_query(Db::getConnectionNB(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;
		
			if ($res && pg_num_rows($res) > 0) {
				
				return $row->antal;
				
			} else {
			
				return 0;
			
			}

	}
	function getOrdersFromADDriveOut() {

		// $select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL AND M_FreightCategory_ID = 1000023  ";
		//echo $select;
		// exit;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;
		
			if ($res && pg_num_rows($res) > 0) {
				
				return $row->antal;
				
			} else {
			
				return 0;
			
			}

	}	
	function getOrdersFromADExpress() {

		// $select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL AND M_FreightCategory_ID = 1000012  ";
		//echo $select;
		// exit;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;
		
			if ($res && pg_num_rows($res) > 0) {
				
				return $row->antal;
				
			} else {
			
				return 0;
			
			}

	}	
	function printPrintedOrdersFromAD() {

		$countrows = 0;
		
		echo "<div><h1>Utskrivna plocksedlar</h1></div>";
		echo "<div>";
		echo "<table>";
		echo "<tr>";
		echo "<td width=\"100\">Dokument nr (följesedelsnummer)</td>";
		echo "<td width=\"100\">Ordernummer</td>";
		echo "<td>Utskriven</td>";
		echo "</tr>";
		
		$select = "SELECT io.documentno, io.movementdate, io.docstatus, c.documentno ";
		$select .= "FROM m_inout io  LEFT JOIN C_Order c ON io.c_order_id = c.c_order_id   ";
		$select .= "WHERE io.docstatus IN ('IP', 'IN') AND io.deliveryViaRule IN ('S') ";
		$select .= "AND io.isSOTrx = 'Y' AND io.isInDispute!='Y' and io.isActive='Y' ";
		$select .= "AND io.AD_Client_ID=1000000 AND io.M_rma_ID is null ";
		$select .= "ORDER BY io.movementdate ";
		// echo $select;
		// exit;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_array($res);

			while ($res && $row = pg_fetch_row($res)) {
				echo "<tr>";
				echo "<td>$row[0]</td>";
				echo "<td>$row[3]</td>";
				echo "<td>" . date("Y-m-d", strtotime($row[1])) . "</td>";
				echo "</tr>";
				$countrows++;
			}

		echo "</table>";
		echo "</div>";
		echo "<div><h3>Totalt: " . $countrows . "<h3></div>";

	}

	function getNotPrintedOrdersFromAD() {

		// $select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		//echo $select;
		// exit;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;
		

			if ($res && pg_num_rows($res) > 0) {
				
				echo $row->antal;
				
			} else {
			
				echo "0";
			
			}

	}

	function getNotPrintedOrdersFromADNew() {

		// $select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		//echo $select;
		// exit;

		$res = (Db::getConnectionNB()) ? @pg_query(Db::getConnectionNB(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				return $row->antal;
				
			} else {
			
				return 0;
			
			}

	}
	
	function getNotPrintedOrdersFromADNB() {

		// $select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		//echo $select;
		// exit;

		$res = (Db::getConnectionNB()) ? @pg_query(Db::getConnectionNB(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				return $row->antal;
				
			} else {
			
				return 0;
			
			}

	}	

	function getNotPrintedOrdersInGroup($ej_utskrivna) {
	
		if ($ej_utskrivna == 0) {
		
			$output .= "<img class=\"flagimage\" src=\"/order/sv_flag.png\">\n";
			$output .= "<span class=\"flagnumber lightnen\">0</span>\n";
			$output .= "<img class=\"flagimage\" src=\"/order/fi_flag.png\">\n";
			$output .= "<span class=\"flagnumber lightnen\">0</span>\n";
			$output .= "<img class=\"flagimage\" src=\"/order/no_flag.png\">\n";
			$output .= "<span class=\"flagnumber lightnen\">0</span>\n";
		
		} else {
		
			$country_id = 1;
			$sv_flag = false;
			$fi_flag = false;
			$no_flag = false;

			$select = "SELECT ic.m_pricelist_id, count(*) AS antal ";
			$select .= "FROM M_InOut_Candidate_v ic ";
			$select .= "WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
			$select .= "GROUP BY ic.m_pricelist_id ";
			$select .= "ORDER BY ic.m_pricelist_id ASC ";

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);
			
			while ($res && $row = pg_fetch_object($res)) {
			
				if ($country_id == 1) {
					if ($row->m_pricelist_id == 1000280) {
						$sv_flag = true;
						$fi_flag = true;
						$no_flag = true;
						$output .= "<img class=\"flagimage\" src=\"/order/sv_flag.png\">\n";
						$output .= "<span class=\"flagnumber lightnen\">0</span>\n";
						$output .= "<img class=\"flagimage\" src=\"/order/fi_flag.png\">\n";
						$output .= "<span class=\"flagnumber lightnen\">0</span>\n";
						$output .= "<img class=\"flagimage\" src=\"/order/no_flag.png\">\n";
						$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
					} elseif ($row->m_pricelist_id == 1000018) {
						$sv_flag = true;
						$fi_flag = true;
						$output .= "<img class=\"flagimage\" src=\"/order/sv_flag.png\">\n";
						$output .= "<span class=\"flagnumber lightnen\">0</span>\n";
						$output .= "<img class=\"flagimage\" src=\"/order/fi_flag.png\">\n";
						$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
					} else {
						$sv_flag = true;
						$output .= "<img class=\"flagimage\" src=\"/order/sv_flag.png\">\n";
						$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
					}
				}
				if ($country_id == 2) {
					if ($row->m_pricelist_id == 1000280) {
						if (!$fi_flag) {
							$fi_flag = true;
							$no_flag = true;
							$output .= "<img class=\"flagimage\" src=\"/order/fi_flag.png\">\n";
							$output .= "<span class=\"flagnumber lightnen\">0</span>\n";
							$output .= "<img class=\"flagimage\" src=\"/order/no_flag.png\">\n";
							$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
						} else {
							$output .= "<img class=\"flagimage\" src=\"/order/no_flag.png\">\n";
							$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
						}
					} else {
						$fi_flag = true;
						$output .= "<img class=\"flagimage\" src=\"/order/fi_flag.png\">\n";
						$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
					}
				}
				if ($country_id == 3) {
					$no_flag = true;
					$output .= "<img class=\"flagimage\" src=\"/order/no_flag.png\">\n";
					$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
				}

				/*
				if ($row->m_pricelist_id == 1000018) {
					$output .= "<img class=\"flagimage\" src=\"/order/fi_flag.png\">\n";
					$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
				} elseif ($row->m_pricelist_id == 1000280) {
					$output .= "<img class=\"flagimage\" src=\"/order/no_flag.png\">\n";
					$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
				} elseif ($row->m_pricelist_id == 1000000) {
					$output .= "<img class=\"flagimage\" src=\"/order/sv_flag.png\">\n";
					$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
				}
				*/
				
				$country_id++;
			
			}
			
			if (!$fi_flag) {
				$output .= "<img class=\"flagimage\" src=\"/order/fi_flag.png\">\n";
				$output .= "<span class=\"flagnumber lightnen\">0</span>\n";
			}
			if (!$no_flag) {
				$output .= "<img class=\"flagimage\" src=\"/order/no_flag.png\">\n";
				$output .= "<span class=\"flagnumber lightnen\">0</span>\n";
			}
		
		}
		
		return $output;

	}

	function getNotPrintedOrdersInGroupOld() {
	
		$select = "SELECT ic.m_pricelist_id, count(*) AS antal ";
		$select .= "FROM M_InOut_Candidate_v ic ";
		$select .= "WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select .= "GROUP BY ic.m_pricelist_id ";
		$select .= "ORDER BY ic.m_pricelist_id ASC ";

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_object($res);
		
		while ($res && $row = pg_fetch_object($res)) {

			if ($row->m_pricelist_id == 1000018) {
				$output .= "<img class=\"flagimage\" src=\"/order/fi_flag.png\">\n";
				$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
			} elseif ($row->m_pricelist_id == 1000280) {
				$output .= "<img class=\"flagimage\" src=\"/order/no_flag.png\">\n";
				$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
			} elseif ($row->m_pricelist_id == 1000000) {
				$output .= "<img class=\"flagimage\" src=\"/order/sv_flag.png\">\n";
				$output .= "<span class=\"flagnumber\">" . $row->antal . "</span>\n";
			}
		}
		
		return $output;

	}
	
	function printNotPrintedOrdersFromAD() {

		$countrows = 0;
		
		echo "<div><h1>EJ utskrivna plocksedlar</h1></div>";
		echo "<div>";
		echo "<table>";
		echo "<tr>";
		echo "<td width=\"100\">Dokument nr</td>";
		echo "<td width=\"100\">Summa</td>";
		echo "<td width=\"100\">Datum</td>";
		echo "</tr>";
		
		$select = "SELECT documentno, totallines, dateordered ";
		$select .= "FROM M_InOut_Candidate_v ic ";
		$select .= "WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select .= "ORDER BY dateordered ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_array($res);

			while ($res && $row = pg_fetch_row($res)) {
				echo "<tr>";
				echo "<td>$row[0]</td>";
				echo "<td>" . round($row[1],2) . "</td>";
				echo "<td>" . date("Y-m-d", strtotime($row[2])) . "</td>";
				echo "</tr>";
				$countrows++;
			}

		echo "</table>";
		echo "</div>";
		echo "<div><h3>Totalt: " . $countrows . "<h3></div>";

	}
	function printPicked($dateFrom, $dateTo) {

		$select = "SELECT u.name, count(*) as picked,  date_trunc('day', pickdate) as date
					FROM m_inout io   
					JOIN m_package p ON p.m_inout_id = io.m_inout_id 
					JOIN ad_user u ON p.createdby = u.ad_user_id 
					WHERE pickdate >'" . $dateFrom . "' AND pickdate <'" . $dateTo . "' AND 
					io.isSOTrx = 'Y' AND io.isInDispute!='Y' and io.isActive='Y' AND io.docstatus IN ('CO') 
					GROUP BY p.createdby, u.name, u.value, date_trunc('day', pickdate)
					ORDER BY count(*) DESC";
		
		// if you just wanted todays date, this is the way: 			
		//WHERE date_trunc('day', pickdate) = date_trunc ('day', localtimestamp) AND 				
		echo "<div><h1>Packade paket</h1></div>";
		echo "<div>";
		echo "<table>";
		echo "<tr>";
		echo "<td width=\"230\">Packare</td>";
		echo "<td width=\"150\">Antal</td>";
		echo "<td width=\"100\">Datum</td>";
		echo "</tr>";		

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_array($res);

		while ($res && $row = pg_fetch_row($res)) {
				echo "<tr>";
				echo "<td>$row[0]</td>";
				echo "<td>$row[1]</td>";
				echo "<td>" . date("Y-m-d", strtotime($row[2])) . "</td>";
				
				echo "</tr>";
		}

		echo "</table>";
		echo "</div>";
	}


	function printSentOrdersFromAD() {

		$countrows = 0;
		$totsum = 0;
		$idag_date = date("Y-m-d", time());
		
		echo "<div><h1>Skickade plocksedlar</h1></div>";
		echo "<div>";
		echo "<table>";
		echo "<tr>";
		echo "<td width=\"100\">Dokument nr</td>";
		echo "<td width=\"150\">Datum</td>";
		echo "<td width=\"100\">Av</td>";
		echo "<td width=\"100\">Summa</td>";
		echo "<td width=\"100\">Valuta</td>";
		echo "</tr>";
		
		$select = "SELECT documentno, updated, (select value from ad_user where ad_user_id=m_inout.updatedby) as user, (select totallines from c_invoice where c_invoice_id=m_inout.c_invoice_id) as total, (select c_currency_id from c_invoice where c_invoice_id=m_inout.c_invoice_id) as currency ";
		$select .= "FROM m_inout WHERE docstatus IN ('CO','CL') AND deliveryViaRule IN ('S') ";
		$select .= "AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select .= "AND AD_Client_ID=1000000 AND M_rma_ID is null ";
		// ändrat till movementdate som nog är mer korrekt
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			$select .= "AND date(movementdate)>='2011-12-27 00:00:00' AND date(movementdate)<='2011-12-27 23:59:59' ";
		} else {
			$select .= "AND date(movementdate)>='" . $idag_date . " 00:00:00' AND date(movementdate)<='" . $idag_date . " 23:59:59' ";
		}
		// movementtype = customershipment 
		$select .= " AND movementType='C-' ";
		$select .= "ORDER BY updated DESC ";
		// $select .= " ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
			echo $select;
		// exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_array($res);

			while ($res && $row = pg_fetch_row($res)) {
				echo "<tr>";
				echo "<td>$row[0]</td>";
				echo "<td>" . date("Y-m-d H:i:s", strtotime($row[1])) . "</td>";
				echo "<td>" . strtoupper($row[2]) . "</td>";
				echo "<td>" . round($row[3],0) . "</td>";
				if ($row[4] == 102) {
					echo "<td>EUR</td>";
				} else {
					echo "<td>SEK</td>";
				}
				echo "</tr>";
				$countrows++;
				$totsum = $totsum + $row[3];
			}

		echo "</table>";
		echo "</div>";
		echo "<div><h3>Totalt: " . $countrows . "<h3></div>";
		// echo "<div><h3>Total summa: " . round($totsum,0) . "<h3></div>";

	}

	function printLagershopOrdersFromAD() {

		$countrows = 0;
		$totsum = 0;
		$idag_date = date("Y-m-d", time());
		
		echo "<div><h1>Skickade plocksedlar - Lagershop</h1></div>";
		echo "<div>";
		echo "<table>";
		echo "<tr>";
		echo "<td width=\"100\">Dokument nr</td>";
		echo "<td width=\"150\">Datum</td>";
		echo "<td width=\"100\">Av</td>";
		echo "<td width=\"100\">Summa</td>";
		echo "<td width=\"100\">Valuta</td>";
		echo "</tr>";
		
		$select = "SELECT documentno, updated, (select value from ad_user where ad_user_id=m_inout.updatedby) as user, (select totallines from c_invoice where c_invoice_id=m_inout.c_invoice_id) as total, (select c_currency_id from c_invoice where c_invoice_id=m_inout.c_invoice_id) as currency ";
		$select .= "FROM m_inout WHERE docstatus IN ('CO') AND deliveryViaRule IN ('P') ";
		$select .= "AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select .= "AND AD_Client_ID=1000000 AND M_rma_ID is null ";
		$select .= "AND date(updated)>='" . $idag_date . " 00:00:00' AND date(updated)<='" . $idag_date . " 23:59:59' ";
		$select .= "ORDER BY updated DESC ";
		// $select .= " ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_array($res);

			while ($res && $row = pg_fetch_row($res)) {
				echo "<tr>";
				echo "<td>$row[0]</td>";
				echo "<td>" . date("Y-m-d H:i:s", strtotime($row[1])) . "</td>";
				echo "<td>" . strtoupper($row[2]) . "</td>";
				echo "<td>" . round($row[3],0) . "</td>";
				if ($row[4] == 102) {
					echo "<td>EUR</td>";
				} else {
					echo "<td>SEK</td>";
				}
				echo "</tr>";
				$countrows++;
				$totsum = $totsum + $row[3];
			}

		echo "</table>";
		echo "</div>";
		echo "<div><h3>Totalt: " . $countrows . "<h3></div>";
		// echo "<div><h3>Total summa: " . round($totsum,0) . "<h3></div>";

	}

	function printOverallDeliveriesFromAD() {
		global $ref_dagensdatum, $dagensdatum, $details;

		$desiderow = true;
		$countrows = 0;
		$totsum = 0;
		$totsumLagershop = 0;
		$totsumFI = 0;
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		$EURrate = $this->getEURrate($dagensdatum);
		
		echo "<div class=\"salespanel\">"; // första divven till vänster
		echo "<div><h2>Leveranser från CyberPhoto - $dagensdatum</h2></div>";
		?>
		<div>
		<form name="sampleform" method="POST">
		<input type="hidden" name="show" value="overall">
		<span class="abbrubrik">Annat datum:</span><br>
		<input type="text" name="firstinput" size=12 value="<?php echo $dagensdatum; ?>" style="font-family: Verdana; font-size: 10px"> <span class="abbrubrik"><a href="javascript:showCal('Calendar1')">Välj datum</a></span> <?php if ($ref_dagensdatum != $dagensdatum) { ?><span class="abbrubrik"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=overall"> Idag</a></span><?php } ?>&nbsp;
		<input type="checkbox" name="details" value="yes"<?php if ($details == "yes"): echo " checked"; endif; ?>>&nbsp;Visa detaljer<br>
		<hr noshade color="#C0C0C0" align="left" width="150" size="1">
		<input type="submit" value="Rapport" style="font-family: Verdana; font-size: 10px">
		</form>
		</div>
		<?php
		echo "<div><br>EUR kurs: $EURrate<br><br></div>";
		if ($details == "yes") {
			echo "<div>";
			echo "<table>";
			echo "<tr>";
			echo "<td width=\"70\" align=\"center\"><b>Lev nr</td>";
			echo "<td width=\"70\" align=\"center\"><b>Order nr</td>";
			echo "<td width=\"140\" align=\"center\"><b>Tidpunkt</td>";
			echo "<td width=\"50\" align=\"center\"><b>Av</td>";
			echo "<td width=\"60\"><b>Totalt</td>";
			echo "<td width=\"45\" align=\"center\"><b>Valuta</td>";
			echo "<td width=\"60\"><b>SEK</td>";
			echo "<td width=\"25\">&nbsp;</td>";
			echo "</tr>";
		}
		
		// $select = "SELECT documentno, updated, (select value from ad_user where ad_user_id=m_inout.updatedby) as user, (select totallines from c_invoice where c_invoice_id=m_inout.c_invoice_id) as total, (select c_currency_id from c_invoice where c_invoice_id=m_inout.c_invoice_id) as currency, deliveryViaRule ";
		$select = "SELECT (select documentno from c_order where c_order_id=m_inout.c_order_id) as ordernr, updated, (select value from ad_user where ad_user_id=m_inout.updatedby) as user, (select totallines from c_invoice where c_invoice_id=m_inout.c_invoice_id) as total, (select c_currency_id from c_invoice where c_invoice_id=m_inout.c_invoice_id) as currency, deliveryViaRule, documentno ";
		$select .= "FROM m_inout WHERE docstatus IN ('CO') AND deliveryViaRule IN ('S','P') ";
		$select .= "AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select .= "AND AD_Client_ID=1000000 AND M_rma_ID is null ";
		$select .= "AND date(updated)>='" . $dagensdatum . " 00:00:00' AND date(updated)<='" . $dagensdatum . " 23:59:59' ";
		$select .= "ORDER BY updated DESC ";
		// $select .= " ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_array($res);

			while ($res && $row = pg_fetch_row($res)) {
			
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}

				if ($row[4] == 102) {
					$omraknat = $EURrate * $row[3];
				}
			
				if ($details == "yes") {
					echo "<tr>";
					echo "<td class=\"$rowcolor\" align=\"center\">$row[6]</td>";
					echo "<td class=\"$rowcolor\" align=\"center\"><a href=\"javascript:winPopupCenter(500, 1000, '/order/order_info.php?order=$row[0]');\">$row[0]</a></td>";
					echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d H:i:s", strtotime($row[1])) . "</td>";
					echo "<td class=\"$rowcolor\" align=\"center\">" . strtoupper($row[2]) . "</td>";
					echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($row[3], 0, ',', ' ') . "&nbsp;</td>";
					if ($row[4] == 102) {
						echo "<td class=\"$rowcolor\" align=\"center\">EUR</td>";
					} else {
						echo "<td class=\"$rowcolor\" align=\"center\">SEK</td>";
					}
					if ($row[4] == 102) {
						// echo "<td>" . round($omraknat,0) . "</td>";
						echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($omraknat, 0, ',', ' ') . "</td>";
					} else {
						echo "<td class=\"$rowcolor\">&nbsp;</td>";
					}
					if ($row[5] == "P") {
						echo "<td class=\"$rowcolor\">*</td>";
					} else {
						echo "<td class=\"$rowcolor\">&nbsp;</td>";
					}
					echo "</tr>";
				}
				
				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				$countrows++;
				if ($row[5] == "P") {
					$totsumLagershop = $totsumLagershop + $row[3];
				}
				if ($row[4] == 102) {
					$totsumFI = $totsumFI + $omraknat;
					$totsum = $totsum + $omraknat;
				} else {
					$totsum = $totsum + $row[3];
				}
				$omraknat = 0;
			}

		if ($details == "yes") {
			echo "</table>";
			echo "</div>";
		}
		echo "<div><h2>Totalt: " . $countrows . " st - " . number_format($totsum, 0, ',', ' ') . " SEK<h2></div>";
		if ($countrows > 0) {
			echo "<div><h3>Varav lagershop: " . number_format($totsumLagershop, 0, ',', ' ') . " SEK<h3></div>";
			echo "<div><h3>Varav Finland: " . number_format($totsumFI, 0, ',', ' ') . " SEK<h3></div>";
			// echo "<div><h3>Total summa: " . number_format($totsum, 0, ',', ' ') . " SEK<h3></div>";
			echo "<div><h3>Snittordervärdet: " . number_format($totsum/$countrows, 0, ',', ' ') . " SEK<h3></div>";
		}
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			// $this->printOverallDeliveriesFromADOneYearBack($totsum);
			// echo "<div><h3>Snittordervärdet: " . number_format($totsum/$countrows, 0, ',', ' ') . " SEK<h3></div>";
		}
		if ($details != "yes") {
			$this->printOverallDeliveriesFromADOneYearBack($totsum);
		} else {
			echo "</div>";
		}

	}

	function printOverallDeliveriesFromADOneYearBack($totsumNow) {
		global $dagensdatum;
		
		// echo 'Current PHP version: ' . phpversion();

		$today_day = date("N", strtotime($dagensdatum)); // tar fram vilken veckodag det är idag
		$day_year_before = date('Y-m-d', strtotime('-1 year', strtotime($dagensdatum))); // tar fram motsvarande dag ett år tillbaka
		
		if ($today_day == 1) {
			$display_day_before = date('Y-m-d', strtotime('next monday', strtotime($day_year_before)));
		} elseif ($today_day == 2) {
			$display_day_before = date('Y-m-d', strtotime('next tuesday', strtotime($day_year_before)));
		} elseif ($today_day == 3) {
			$display_day_before = date('Y-m-d', strtotime('next wednesday', strtotime($day_year_before)));
		} elseif ($today_day == 4) {
			$display_day_before = date('Y-m-d', strtotime('next thursday', strtotime($day_year_before)));
		} elseif ($today_day == 5) {
			$display_day_before = date('Y-m-d', strtotime('next friday', strtotime($day_year_before)));
		} elseif ($today_day == 6) {
			$display_day_before = date('Y-m-d', strtotime('next saturday', strtotime($day_year_before)));
		} elseif ($today_day == 7) {
			$display_day_before = date('Y-m-d', strtotime('next sunday', strtotime($day_year_before)));
		}
		
		// echo $display_day_before;
		
		$desiderow = true;
		$countrows = 0;
		$totsum = 0;
		$totsumLagershop = 0;
		$totsumFI = 0;
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}

		$EURrate = $this->getEURrate($display_day_before);
		
		echo "<hr noshade color=\"#C0C0C0\" align=\"left\" width=\"630\" size=\"1\">";
		echo "<div><h1>Leveranser från CyberPhoto motsvarande dag i fjol - $display_day_before</h1></div>";
		if (strtotime($display_day_before) < strtotime("2011-03-14")) {
			echo "<div><h3><i>Ingen historik visas här förrän vi passerat 12 mars 2012</i></h3></div>";
		}

		echo "<div><br>EUR kurs: $EURrate<br><br></div>";
		
		$select = "SELECT documentno, updated, (select value from ad_user where ad_user_id=m_inout.updatedby) as user, (select totallines from c_invoice where c_invoice_id=m_inout.c_invoice_id) as total, (select c_currency_id from c_invoice where c_invoice_id=m_inout.c_invoice_id) as currency, deliveryViaRule ";
		$select .= "FROM m_inout WHERE docstatus IN ('CO') AND deliveryViaRule IN ('S','P') ";
		$select .= "AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select .= "AND AD_Client_ID=1000000 AND M_rma_ID is null ";
		$select .= "AND date(updated)>='" . $display_day_before . " 00:00:00' AND date(updated)<='" . $display_day_before . " 23:59:59' ";
		$select .= "ORDER BY updated DESC ";
		// $select .= " ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_array($res);

			while ($res && $row = pg_fetch_row($res)) {
			
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}

				if ($row[4] == 102) {
					$omraknat = $EURrate * $row[3];
				}
			
				$countrows++;
				if ($row[5] == "P") {
					$totsumLagershop = $totsumLagershop + $row[3];
				}
				if ($row[4] == 102) {
					$totsumFI = $totsumFI + $omraknat;
					$totsum = $totsum + $omraknat;
				} else {
					$totsum = $totsum + $row[3];
				}
				$omraknat = 0;
			}

		echo "<div><h2>Totalt: " . $countrows . " st - " . number_format($totsum, 0, ',', ' ') . " SEK<h2></div>";
		if ($countrows > 0) {
			echo "<div><h3>Varav lagershop: " . number_format($totsumLagershop, 0, ',', ' ') . " SEK<h3></div>";
			echo "<div><h3>Varav Finland: " . number_format($totsumFI, 0, ',', ' ') . " SEK<h3></div>";
			// echo "<div><h3>Total summa: " . number_format($totsum, 0, ',', ' ') . " SEK<h3></div>";
			echo "<div><h3>Snittordervärdet: " . number_format($totsum/$countrows, 0, ',', ' ') . " SEK<h3></div>";
		}

		$valuenow = $totsumNow;
		$valuethen = $totsum;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			
			// echo $valuenow . "<br>";
			// echo $valuethen . "<br>";
			
			if ($valuethen == 0 && $valuenow == 0) {
				$valuediff = $valuenow;
				// $diffinprocent = $valuediff / 1;
				$diffinprocent = 0;
			} elseif ($valuethen == 0) {
				$valuediff = $valuenow;
				// $diffinprocent = $valuediff / 1;
				$diffinprocent = 1;
			} else {
				$valuediff = $valuenow - $valuethen;
				$diffinprocent =  $valuenow / $valuethen;
			}
			$diffinprocent = round(($diffinprocent * 100), 1);
			
			if ($valuethen == 0 && $valuenow == 0) {
				$class = "rubrikblack";
			} elseif ($valuediff < 0 && $valuediff > -11) {
				$class = "rubrikorange";
			} elseif ($valuediff > 0) {
				$class = "rubrikgreen";
			} else {
				$class = "rubrikred";
			}
			if ($diffinprocent < 200) {
				$showMax = 200;
			} else {
				$showMax = $diffinprocent;
			}
		
		} else {
		
			/*
			if ($valuethen == 0 && $valuenow == 0) {
				$valuediff = $valuenow;
				// $diffinprocent = $valuediff / 1;
				$diffinprocent = 0;
			} elseif ($valuethen == 0) {
				$valuediff = $valuenow;
				// $diffinprocent = $valuediff / 1;
				$diffinprocent = 1;
			} else {
				$valuediff = $valuenow - $valuethen;
				$diffinprocent = $valuediff / $valuethen;
			}
			$diffinprocent = round(($diffinprocent * 100), 1);
			if ($valuethen == 0 && $valuenow == 0) {
				$class = "rubrikblack";
			} elseif ($valuediff < 0 && $valuediff > -11) {
				$class = "rubrikorange";
			} elseif ($valuediff > 0) {
				$class = "rubrikgreen";
			} else {
				$class = "rubrikred";
			}
			if ($diffinprocent < 100) {
				$showMax = 100;
			} else {
				$showMax = $diffinprocent;
			}
			*/
			if ($valuethen == 0 && $valuenow == 0) {
				$valuediff = $valuenow;
				// $diffinprocent = $valuediff / 1;
				$diffinprocent = 0;
			} elseif ($valuethen == 0) {
				// $valuediff = $valuethen;
				$valuediff = $valuenow - $valuethen;
				// $diffinprocent = $valuediff / 1;
				$diffinprocent = 1;
			} else {
				$valuediff = $valuenow - $valuethen;
				$diffinprocent =  $valuenow / $valuethen;
			}
			$diffinprocent = round(($diffinprocent * 100), 1);
			
			// if ($valuethen == 0 && $valuenow == 0) {
			if ($diffinprocent == 0) {
				$class = "rubrikblack";
			} elseif ($diffinprocent < 100 && $diffinprocent > 90) {
				$class = "rubrikorange";
			} elseif ($diffinprocent >= 100) {
				$class = "rubrikgreen";
			} else {
				$class = "rubrikred";
			}
			if ($diffinprocent < 200) {
				$showMax = 200;
			} else {
				$showMax = $diffinprocent;
			}
			
		}
		// echo "<hr noshade color=\"#C0C0C0\" align=\"left\" width=\"630\" size=\"1\">";
		echo "</div>"; // första divven till vänster slutar här
		echo "<div class=\"salespanelsum\">";
		echo "<div><h1>Skillnad motsvarande dag i fjol</h1></div>";
		if (strtotime($display_day_before) < strtotime("2011-03-14")) {
			echo "<div><h3><i>Korrekt differens visas här när vi passerat 12 mars 2012</i></h3></div>";
		}
		// echo "<div><span class=\"$class\">" . number_format($valuediff, 0, ',', ' ') . " kr (" . $diffinprocent . "%)</span></div>";
		echo "<div><span class=\"$class\">";
		if ($valuediff > 0) {
			echo "+";
		}
		echo number_format($valuediff, 0, ',', ' ');
		echo " kr (" . $diffinprocent . "%)</span></div>";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			// echo "<div><h3>Snittordervärdet: " . number_format($totsum/$countrows, 0, ',', ' ') . " SEK<h3></div>";
		}

		?>
		<script type='text/javascript' src='https://www.google.com/jsapi'></script>
		<script type='text/javascript'>
		  google.load('visualization', '1', {packages:['gauge']});
		  google.setOnLoadCallback(drawChart);
		  function drawChart() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Label');
			data.addColumn('number', 'Value');
			data.addRows([
			  ['Differens', <?php echo $diffinprocent; ?>]
			]);

			var options = {
			  width: 300, height: 300,
			  redFrom: 0, redTo: 90,
			  yellowFrom: 90, yellowTo: 100,
			  greenFrom:100, greenTo: <?php echo $showMax; ?>,
			  minorTicks: 5, min: 0, max: <?php echo $showMax; ?>
			};

			var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
			chart.draw(data, options);
		  }
		</script>
		<div id='chart_div'></div>

		<?php
		echo "</div>";
	}

	function getEURrate($idag_date) {

		if ($idag_date == "") {
			$idag_date = date("Y-m-d", time());
		}

		$select = "SELECT multiplyrate  ";
		$select .= "FROM c_conversion_rate ";
		$select .= "WHERE c_currency_id = 102 AND c_currency_id_to = 311 AND validto = '$idag_date' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			while ($res && $row = pg_fetch_row($res)) {

				return round($row[0],3);
			
			}

	}

	function getStat($dagensdatum) {

	?>
		<div id="systemkamcontainer">
		<div class="roundtop">
		<div class="sykam1"></div>
		<div class="sykam2"></div>
		<div class="sykam3"></div>
		<div class="sykam4"></div>
		</div>
		
		<div class="content22">

		<table border="0" cellpadding="5" cellspacing="0">
		  <tr>
			<td width="50"><font face="Verdana" size="1"><b>Land</td>
			<td width="100" align="center"><font face="Verdana" size="1"><b>Antal paket</td>
			<td width="100" align="right"><font face="Verdana" size="1"><b>Omsättning SEK</td>
			<td width="100" align="right"><font face="Verdana" size="1"><b>Omsättning EUR</td>
			<td width="150"><font face="Verdana" size="1">&nbsp;</td>
		  </tr>

	<?php
		$select  = "SELECT land_id, count(ordernr) as Antal, sum(netto) AS Totalsumma, currencyRate  ";
		$select .= "FROM ordertabell_alla  ";
		$select .= "WHERE (land_id = 45 or land_id = 46 or land_id = 358 or land_id = 999) and skickat = '$dagensdatum' ";
		$select .= "GROUP BY land_id, currencyRate ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		$num_rows = mssql_num_rows($res);
		
		if ($num_rows > 0) {

		// extract(mssql_fetch_array($res));
		while ($row = @mssql_fetch_object($res)):

	?>
		  <tr>
			<td width="50"><font face="Verdana" size="1">
			<?php
			if ($row->land_id == 45) {
				echo "Danmark";
			} elseif ($row->land_id == 46) {
				echo "Sverige";
			} elseif ($row->land_id == 358) {
				echo "Finland";
			} elseif ($row->land_id == 999) {
				echo "Åland";
			}
			?>
			</td>
			<td width="100" align="center"><font face="Verdana" size="1">
			<?php
			
			if ($row->land_id == 45) {
				$AntalDk = $row->Antal;
				echo $AntalDk;
			} elseif ($row->land_id == 46) {
				$AntalSv = $row->Antal;
				echo $AntalSv;
			} elseif ($row->land_id == 358) {
				if ($AntalFi == "") {
					$AntalFi = $row->Antal;
					echo $AntalFi;
				} else {
					$AntalFi2 = $row->Antal;
					echo $AntalFi2;
				}
			} elseif ($row->land_id == 999) {
				$AntalAland = $row->Antal;
				echo $AntalAland;
			}
			
			echo "&nbsp;st";
			?>
			</td>
			<td width="100" align="right"><font face="Verdana" size="1">
			<?php
			if ($row->land_id == 358) {
		
			$FiTotalsummaEUR = $row->Totalsumma;
				if ($FiTotalsumma == "") {
				$FiTotalsumma = $row->Totalsumma * $row->currencyRate;
				echo number_format($FiTotalsumma, 0, ',', ' ') . " SEK";
				} else {
				$FiTotalsumma2 = $row->Totalsumma * $row->currencyRate;
				echo number_format($FiTotalsumma2, 0, ',', ' ') . " SEK";
				}
			
			} elseif ($row->land_id == 999) {
		
			$FiTotalsummaEUR = $row->Totalsumma;
			$FiTotalsummaAland = $row->Totalsumma * $row->currencyRate;
			
			echo number_format($FiTotalsummaAland, 0, ',', ' ') . " SEK";
			// echo "  (" . number_format($FiTotalsummaEUR, 0, ',', ' ') . " EUR)";
			
			} elseif ($row->land_id == 45) {
		
			$DkTotalsumma = $row->Totalsumma * $row->currencyRate;
			
			echo number_format($DkTotalsumma, 0, ',', ' ') . " SEK";
		
			} else {
		
			$SvTotalsumma = $row->Totalsumma * $row->currencyRate;
			
			echo number_format($SvTotalsumma, 0, ',', ' ') . " SEK";
		
			}
			?>
			</td>
			<td width="100" align="right"><font face="Verdana" size="1"><?php if ($FiTotalsummaEUR != 0) { echo number_format($FiTotalsummaEUR, 0, ',', ' ') . " EUR"; } ?>&nbsp;</td>
			<td width="150"><font face="Verdana" size="1">&nbsp;</td>
		  </tr>
	<?php
		endwhile;

			$SummaTotalPaket = ($AntalDk + $AntalSv + $AntalFi + $AntalFi2 + $AntalAland);
			$SummaTotal = ($DkTotalsumma + $SvTotalsumma + $FiTotalsumma + $FiTotalsumma2 + $FiTotalsummaAland);
			$SummaTotalEUR = ($FiTotalsummaEUR + $FiTotalsummaEURAland);

	?>
		  <tr>
			<td width="50"><font face="Verdana" size="1"><b>Totalt:</td>
			<td width="100" align="center"><font face="Verdana" size="1"><b><?php echo number_format($SummaTotalPaket, 0, ',', ' ') . "</b>&nbsp;st"; ?></td>
			<td width="100" align="right"><font face="Verdana" size="1"><b><?php echo number_format($SummaTotal, 0, ',', ' '); ?></b>&nbsp;SEK</td>
			<td width="100" align="right"><font face="Verdana" size="1">&nbsp;</td>
			<td width="150"><font face="Verdana" size="1">&nbsp;</td>
		  </tr>

	<?php
		
		} else {
		

		}

	?>
		</table>
		</div>
		
		<div class="roundbottom">
		<div class="sykam4"></div>
		<div class="sykam3"></div>
		<div class="sykam2"></div>
		<div class="sykam1"></div>
		</div>
		</div>


	<?php
	}

	function getCountryValue($id) {
		global $dagensdatum;
		
		// $Totalsumma = 0;

		$select  = "SELECT COUNT(ordernr) AS Antal, ROUND(SUM(netto * currencyRate),0) AS Totalsumma ";
		$select .= "FROM ordertabell_alla  ";
		$select .= "WHERE land_id IN ($id) and skickat = '$dagensdatum' ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			if ($Totalsumma == Null) {
				echo "0";
			} else {
				echo $Totalsumma;
			}
			
			endwhile;
		} else {
		
			echo "0";
		}

	}

	function getCountryValueNew($id) {
		global $dagensdatum;
		
		// $Totalsumma = 0;

		$select  = "SELECT COUNT(ordernr) AS Antal, ROUND(SUM(netto * currencyRate),0) AS Totalsumma ";
		$select .= "FROM ordertabell_alla  ";
		$select .= "WHERE land_id IN ($id) and skickat = '$dagensdatum' ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			if ($Totalsumma == Null) {
				return 0;
			} else {
				return $Totalsumma;
			}
			
			endwhile;
		} else {
		
			return 0;
		}

	}

	function getTotalValue() {
		global $dagensdatum;
		
		$Totalsumma = 0;

		$select  = "SELECT COUNT(ordernr) AS Antal, ROUND(SUM(netto * currencyRate),0) AS Totalsumma ";
		$select .= "FROM ordertabell_alla  ";
		$select .= "WHERE skickat = '$dagensdatum' ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			if ($Totalsumma == Null) {
				echo "0";
			} else {
				echo $Totalsumma;
			}
			
			endwhile;
		
		} else {
		
			echo $Totalsumma;
		
		}

	}

	function getTotalValueNew() {
		global $dagensdatum;
		
		$Totalsumma = 0;

		$select  = "SELECT COUNT(ordernr) AS Antal, ROUND(SUM(netto * currencyRate),0) AS Totalsumma ";
		$select .= "FROM ordertabell_alla  ";
		$select .= "WHERE skickat = '$dagensdatum' ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			if ($Totalsumma == Null) {
				return 0;
			} else {
				return $Totalsumma;
			}
			
			endwhile;
		
		} else {
		
			return 0;
		
		}

	}

	function getTotalValueNewOneYearBack() {
		global $compare_date;
		
		$Totalsumma = 0;

		$select  = "SELECT COUNT(ordernr) AS Antal, ROUND(SUM(netto * currencyRate),0) AS Totalsumma ";
		$select .= "FROM ordertabell_alla  ";
		$select .= "WHERE skickat = '$compare_date' ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			if ($Totalsumma == Null) {
				return 0;
			} else {
				return $Totalsumma;
			}
			
			endwhile;
		
		} else {
		
			return 0;
		
		}

	}

	function getTotalValueFormated() {
		global $dagensdatum;
		
		$Totalsumma = 0;

		$select  = "SELECT COUNT(ordernr) AS Antal, ROUND(SUM(netto * currencyRate),0) AS Totalsumma ";
		$select .= "FROM ordertabell_alla  ";
		$select .= "WHERE skickat = '$dagensdatum' ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			echo number_format($Totalsumma, 0, ',', ' ');
			// echo $Totalsumma;
			
			endwhile;
		
		} else {
		
			echo $Totalsumma;
		
		}

	}

	function getPrintedOrders() {

		$select  = "SELECT COUNT(ordernr) as AntalOpackade ";
		$select .= "FROM ordertabell  ";
		// $select .= "WHERE plockUtskriven = -1 AND skickat is null ";
		$select .= "WHERE plockUtskriven = -1 AND skickat is null AND NOT (betalsatt_id = 8) AND NOT (leveranssatt_id = 19) AND NOT (skum = -1) AND skickad_av is null AND NOT (leveranssatt_id = 8) ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			echo $AntalOpackade;
			
			endwhile;
		}

	}

	function getPrintedOrdersLight() {

		$select  = "SELECT COUNT(ordernr) as AntalOpackade ";
		$select .= "FROM ordertabell  ";
		$select .= "WHERE plockUtskriven = -1 AND skickat is null ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			return $AntalOpackade;
			
			endwhile;
		} else {
		
			return 0;
		}

	}

	function getPrintedOrdersLight_v2() {

		$select  = "SELECT COUNT(ordernr) as AntalOpackade ";
		$select .= "FROM ordertabell  ";
		$select .= "WHERE plockUtskriven = -1 AND skickat is null AND NOT (betalsatt_id = 8) AND NOT (leveranssatt_id = 19) AND NOT (skum = -1) AND skickad_av is null AND NOT (leveranssatt_id = 8) ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			return $AntalOpackade;
			
			endwhile;
		} else {
		
			return 0;
		}

	}

	function getNotPrintedOrders() {

		$select  = "SELECT COUNT(ordernr) as AntalOpackade ";
		$select .= "FROM ordertabell  ";
		$select .= "WHERE plockUtskriven = 0 AND (skickad_av is Null or skickad_av = 'XX' ) AND köplats <> -1 AND skum = 0 and kundnr <> 1010 AND plockOK = -1 AND NOT (fakturanr IS Null) ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			echo $AntalOpackade;
			
			endwhile;
		}

	}

	function getNotPrintedOrdersView() {
		global $dagensdatum;
		
		$printok = 0;

		$select  = "SELECT ordernr, samPackasMedOrdernr, kundnr, behandlat, readyStatus ";
		$select .= "FROM ordertabell  ";
		$select .= "WHERE behandlat is not Null AND (skickad_av is Null) AND skickat is null ";
		$select .= "AND (leveranssatt_id <> 8 OR leveranssatt_id is Null) AND ((Ordertabell.levklar = 3) OR Ordertabell.betalsatt_id <> 7) ";
		$select .= "AND ((Ordertabell.forskottBelopp > 0 OR Ordertabell.netto = 0 OR Ordertabell.netto is Null ) OR Ordertabell.betalsatt_id <> 1) ";
		$select .= "AND Ordertabell.plockUtskriven = 0 AND Ordertabell.plockOk = -1 ";
		$select .= "AND (skickasTidigast <= '$dagensdatum' OR skickasTidigast is Null) ";
		$select .= "AND ( betalsatt_id <> 11 AND betalsatt_id <> 12 OR ( Ordertabell.levklar = 3 and Ordertabell.erordernr is not null ) ";
		$select .= "AND ( readyStatus is null or readyStatus = 3 or readyStatus = 0 )) ";
		$select .= "ORDER BY inkommet DESC ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			if ($samPackasMedOrdernr != "") {
				if ($this->getNotPrintedOrdersView2($samPackasMedOrdernr)) {
					$printok++;
				}
			 } else {
					$printok++;
			 }
			
			endwhile;
		}
		
		echo $printok;

	}

	function getNotPrintedOrdersViewLight() {
		global $dagensdatum;
		
		$printok = 0;

		$select  = "SELECT ordernr, samPackasMedOrdernr, kundnr, behandlat, readyStatus ";
		$select .= "FROM ordertabell  ";
		$select .= "WHERE behandlat is not Null AND (skickad_av is Null) AND skickat is null ";
		$select .= "AND (leveranssatt_id <> 8 OR leveranssatt_id is Null) AND ((Ordertabell.levklar = 3) OR Ordertabell.betalsatt_id <> 7) ";
		$select .= "AND ((Ordertabell.forskottBelopp > 0 OR Ordertabell.netto = 0 OR Ordertabell.netto is Null ) OR Ordertabell.betalsatt_id <> 1) ";
		$select .= "AND Ordertabell.plockUtskriven = 0 AND Ordertabell.plockOk = -1 ";
		$select .= "AND (skickasTidigast <= '$dagensdatum' OR skickasTidigast is Null) ";
		$select .= "AND ( betalsatt_id <> 11 AND betalsatt_id <> 12 OR ( Ordertabell.levklar = 3 and Ordertabell.erordernr is not null ) ";
		$select .= "AND ( readyStatus is null or readyStatus = 3 or readyStatus = 0 )) ";
		$select .= "ORDER BY inkommet DESC ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			if ($samPackasMedOrdernr != "") {
				if ($this->getNotPrintedOrdersView2($samPackasMedOrdernr)) {
					$printok++;
				}
			 } else {
					$printok++;
			 }
			
			endwhile;
		}
		
		return $printok;

	}

	function getNotPrintedOrdersView2($ordernr) {
		global $dagensdatum;

		$select  = "SELECT ordernr, samPackasMedOrdernr, kundnr, behandlat, readyStatus ";
		$select .= "FROM ordertabell  ";
		$select .= "WHERE behandlat is not Null AND (skickad_av is Null) AND skickat is null ";
		$select .= "AND (leveranssatt_id <> 8 OR leveranssatt_id is Null) AND ((Ordertabell.levklar = 3) OR Ordertabell.betalsatt_id <> 7) ";
		$select .= "AND ((Ordertabell.forskottBelopp > 0 OR Ordertabell.netto = 0 OR Ordertabell.netto is Null ) OR Ordertabell.betalsatt_id <> 1) ";
		$select .= "AND Ordertabell.plockUtskriven = 0 AND Ordertabell.plockOk = -1 ";
		$select .= "AND (skickasTidigast <= '$dagensdatum' OR skickasTidigast is Null) ";
		$select .= "AND ( betalsatt_id <> 11 AND betalsatt_id <> 12 OR ( Ordertabell.levklar = 3 and Ordertabell.erordernr is not null ) ";
		$select .= "AND ( readyStatus is null or readyStatus = 3 or readyStatus = 0 )) ";
		$select .= "AND ordernr = '$ordernr' ";
		$select .= "ORDER BY inkommet DESC ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			return true;
			
			endwhile;
			
		} else {
		
			return false;
		
		}

	}

	function getCheckOrders($id) {

		$select  = "SELECT COUNT(ordernr) as AntalKollaOrdrar ";
		$select .= "FROM ordertabell  ";
		$select .= "WHERE plockOK <> -1 AND skickat is Null AND ( skickad_av is Null or skickad_av = 'XX' ) AND köplats <> -1 AND skum = 0 and kundnr <> 1010 AND land_id IN ($id) ";
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			echo $AntalKollaOrdrar;
			
			endwhile;
		}

	}

	function getSnittValue($id,$telefon) {
		global $dagensdatum;

		$select  = "SELECT COUNT(ordernr) AS Antal, SUM(netto * currencyRate) AS Totalsumma ";
		$select .= "FROM ordertabell_alla ";
		$select .= "WHERE ordertabell_alla.land_id IN($id) AND skickat = '$dagensdatum' ";
		if ($telefon == 1) {
			$select .= "AND inlagd_av = 'TO' ";
		}
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
			$antalordrar = $Antal;
			$ordersumma = $Totalsumma;
			if ($Totalsumma == "") {
				$snittotal = 0;
			} else {
				$snittotal = $ordersumma / $antalordrar;
			}
			
			echo round($snittotal,0);
			
			endwhile;
		}

	}

	function sendMessPack($summapac) {

		$recipient = "pa";
		$recipient .= " rk";
		
		$subj = "Upplysning!";

		$extra = "From: webmaster";
		
		$text1 = "Klockan är nu efter 16:00 och det finns " . $summapac . " st ordrar för packning!\n\nVänligen vidta lämplig åtgärd.\n\n// SysAdmin";
		
		mail($recipient, $subj, $text1, $extra);

	}
	function sendMessOrder($summakolla) {

		$recipient = "order";
		// $recipient .= " ss";
		
		$subj = "Upplysning!";

		$extra = "From: webmaster";
		
		$text1 = "Klockan är nu efter 15:45 och det finns " . $summakolla . " st ordrar som måste kollas!\n\nVänligen vidta lämplig åtgärd.\n\n// SysAdmin";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendMessOrderTotal($land) {
		global $dagensdatum;

		$friseradate = date("Y-m-d", time());
		// $recipient .= " po";
		$recipient .= " ss";
		
		if ($land == 358) {
			$subj = "Summering Finland " . $friseradate;
		} else {
			$subj = "Summering Sverige " . $friseradate;
		}

		$extra = "From: sysadmin";
		
		$text1 = $this->getTotalValueToday($land);
		
		mail($recipient, $subj, $text1, $extra);

	}

	function mailAboutHelp($numbers) {
		global $dagensdatum;

		$date = date("Y-m-d H:i:s", time());
		$recipient .= " grupp_alla@cyberphoto.nu";
		$subj = $date . " - Plockhjälp behövs - " . $numbers . " st ordrar just nu";
		$extra = "From: noreply";
		$text1  = "Just nu finns det " . $numbers . " st ordrar att plocka. Har ni några minuter över så kom gärna ner och plocka ett par ordrar.\n\n";
		$text1 .= "// Logistikgruppen\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function getEUR() {
		
	$select  = "SELECT kurs FROM ValutaNew WHERE id = 1 ";

	// echo $select;
	// exit;

	$res = mssql_query ($select, $this->conn_ms);

		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			extract($row);
		
				return $kurs;
			
			endwhile;
		
		} else {
		
			return 0;
		
		}

	}

	function getTotalValueToday($land) {
		global $dagensdatum;
		
		if ($land == 358) {
			// $valuta = " EUR"; // evetuellt kommer vi köra EUR också
			$valuta = " SEK";
		} else {
			$valuta = " SEK";
		}

		$select  = "SELECT COUNT(ordernr) AS Antal, SUM(netto) AS SummaNetto ";
		$select .= "FROM Ordertabell ";
		$select .= "WHERE DateDiff(day, inkommet,'$dagensdatum') = 0 ";
		if ($land == 358) {
			$select .= "AND land_id = '358' ";
		} else {
			$select .= "AND NOT (land_id = '358') ";
		}
		
		// echo $select;
		
		$res = mssql_query($select, $this->conn_ms);
		
		if (mssql_num_rows($res) > 0) {
		
			while ($row = mssql_fetch_array($res)):
			
			extract($row);
			
			if ($land == 358) {
				$SummaNetto = ($SummaNetto * $this->getEUR());
			}
			$snittordervardet = ($SummaNetto / $Antal);
		
			return "Antal: " . round($Antal,0) . " st\nVärde: " . number_format($SummaNetto, 0, ',', ' ') . $valuta . "\nSnittordervärde: " . number_format($snittordervardet, 0, ',', ' ') . $valuta;
			
			endwhile;
		}

	}

	function getIfWorkingday() {
	
		$todaydate = date("Y-m-d", time());
		
		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.departure ";
		$select .= "WHERE departure_Time LIKE '" . $todaydate . "%' AND departure_Active = 1 ";
	
		$res = @mysqli_query(Db::getConnection(false), $select);
	
		if (@mysqli_num_rows($res) > 0) {
			return true;
		} else {
			return false;
		}
	
	}

}
?>

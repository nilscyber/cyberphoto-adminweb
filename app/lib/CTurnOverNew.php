<?php

//include("connections.php");

Class CTurnOverNew {
	var $conn_ms; 
	var $conn_my; 
	var $conn_fi;
	var $conn_ad;

	function __construct() {
		global $fi;
			
		/*
		$this->conn_my = Db::getConnection();
		*/
//		$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKat�ms#1");
//		@mssql_select_db ("cyberphoto", $this->conn_ms);
		$this->conn_fi = $this->conn_ms;
		$this->conn_ad = Db::getConnectionAD();
	}

	function getOrdersFromAD() {

		// $select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL ";
		//echo $select;
		// exit;

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				echo $row->antal;
				
			} else {
			
				echo "0";
			
			}

	}

	function printPrintedOrdersFromAD() {

		$countrows = 0;
		$countsumma = 0;
		
		echo "<div><h1>Utskrivna plocksedlar</h1></div>";
		echo "<div>";
		echo "<table>";
		echo "<tr>";
		echo "<td width=\"100\">Leverans nr</td>";
		echo "<td width=\"100\">Order nr</td>";
		echo "<td width=\"100\">Utskriven</td>";
		echo "<td width=\"80\" class=\"align_center\">Summa</td>";
		echo "<td width=\"150\" class=\"align_center\">Fraktsätt</td>";
		echo "</tr>";
		
		/*
		$select = "SELECT documentno, movementdate, docstatus, m_freightCategory_id ";
		$select .= "FROM m_inout ";
		$select .= "WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S') ";
		$select .= "AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select .= "AND AD_Client_ID=1000000 AND M_rma_ID is null ";
		$select .= "ORDER BY movementdate ";
		*/
		$select = "SELECT DISTINCT m.documentno, m.movementdate, m.docstatus, m.m_freightCategory_id, sum((l.qtyordered - l.qtydelivered) * l.priceactual) AS totallines, o.documentno ";
		$select .= "FROM m_inout m ";
		$select .= "JOIN c_orderline l ON m.c_order_id = l.c_order_id ";
		$select .= "JOIN c_order o ON o.c_order_id = l.c_order_id ";
		$select .= "WHERE m.docstatus IN ('IP', 'IN') AND m.deliveryViaRule IN ('S')  ";
		$select .= "AND m.isSOTrx = 'Y' AND m.isInDispute!='Y' and m.isActive='Y' ";
		$select .= "AND m.AD_Client_ID=1000000 AND M_rma_ID is null ";
		$select .= "GROUP BY m.documentno, m.movementdate, m.docstatus, m.m_freightCategory_id, o.documentno ";
		$select .= "ORDER BY m.movementdate ";
		// echo $select;
		// exit;

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
		// $row = pg_fetch_array($res);

			while ($res && $row = pg_fetch_row($res)) {
				echo "<tr>";
				echo "<td>$row[0]</td>";
				echo "<td><a href=\"javascript:winPopupCenter(500, 1000, 'order_info.php?order=$row[5]');\">$row[5]</a></td>";
				echo "<td>" . date("Y-m-d", strtotime($row[1])) . "</td>";
				echo "<td class=\"align_right\">" . number_format($row[4], 0, ',', ' ') . " SEK</td>";
				if ($row[3] == 1000063) {
					echo "<td class=\"align_center\">Instabox</td>";
				} else {
					echo "<td class=\"align_center\">Postnord</td>";
				}
				echo "</tr>";
				$countrows++;
				$countsumma = $countsumma + $row[4];
			}

		echo "</table>";
		echo "</div>";
		echo "<div><h3>Totalt: " . $countrows . "<h3></div>";
		echo "<div><h3>Totalsumma: " . number_format($countsumma, 0, ',', ' ') . " SEK<h3></div>";

	}

	function getNotPrintedOrdersFromAD() {

		// $select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		//echo $select;
		// exit;

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				echo $row->antal;
				
			} else {
			
				echo "0";
			
			}

	}

	function printNotPrintedOrdersFromAD() {

		$countrows = 0;
		$countsumma = 0;
		
		echo "<div><h1>EJ utskrivna plocksedlar</h1></div>";
		echo "<div>";
		echo "<table>";
		echo "<tr>";
		echo "<td width=\"100\">Order nr</td>";
		echo "<td width=\"100\">Datum</td>";
		echo "<td width=\"80\" class=\"align_center\">Summa</td>";
		echo "<td width=\"150\" class=\"align_center\">Fraktsätt</td>";
		echo "</tr>";
		
		$select = "SELECT documentno, totallines, dateordered, m_freightCategory_id ";
		$select .= "FROM M_InOut_Candidate_v ic ";
		$select .= "WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select .= "ORDER BY dateordered ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
		// $row = pg_fetch_array($res);

			while ($res && $row = pg_fetch_row($res)) {
				echo "<tr>";
				echo "<td><a href=\"javascript:winPopupCenter(500, 1000, 'order_info.php?order=$row[0]');\">$row[0]</a></td>";
				echo "<td>" . date("Y-m-d", strtotime($row[2])) . "</td>";
				echo "<td class=\"align_right\">" . number_format($row[1], 0, ',', ' ') . " SEK</td>";
				if ($row[3] == 1000063) {
					echo "<td class=\"align_center\">Instabox</td>";
				} else {
					echo "<td class=\"align_center\">Postnord</td>";
				}
				echo "</tr>";
				$countrows++;
				$countsumma = $countsumma + $row[1];
			}

		echo "</table>";
		echo "</div>";
		echo "<div><h3>Totalt: " . $countrows . "<h3></div>";
		echo "<div><h3>Totalsumma: " . number_format($countsumma, 0, ',', ' ') . " SEK<h3></div>";

	}
	function printPicked($dateFrom, $dateTo) {

		$select = "SELECT u.name, count(*) as picked,  date_trunc('day', pickdate) as date,
					SUM((select totallines from c_invoice where c_invoice_id=io.c_invoice_id)) as total
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
		echo "<td width=\"50\" align=\"center\">Antal</td>";
		echo "<td width=\"125\" align=\"center\">Värde</td>";
		echo "<td width=\"100\" align=\"center\">Datum</td>";
		echo "</tr>";		

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
		// $row = pg_fetch_array($res);

		while ($res && $row = pg_fetch_row($res)) {
				echo "<tr>";
				echo "<td>$row[0]</td>";
				echo "<td align=\"center\">$row[1]</td>";
					echo "<td class=\"$rowcolor\" align=\"center\">" . number_format($row[3], 0, ',', ' ') . "&nbsp;</td>";
				echo "<td align=\"center\">" . date("Y-m-d", strtotime($row[2])) . "</td>";
				
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
		// �ndrat till movementdate som nog �r mer korrekt
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

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
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

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
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
	function printReceivedOrders() {
		global $ref_dagensdatum, $dagensdatum, $details, $daysBehind;

		$desiderow = true;
		$countrows = 0;
		$totsum = 0;
		$totsumLagershop = 0;
		$totsumFI = 0;
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		if ($daysBehind == "")
				$daysBehind = 7;
			
		// echo "<div><h2>Inkomna ordrar till cyberphoto - $dagensdatum</h2></div>";
		?>
		<div>
		<form name="sampleform" method="POST">
		<input type="hidden" name="show" value="overall">
		<span class="abbrubrik">Antal dagar bakåt:</span>
		<input type="text" name="daysBehind" size=1 value="<?php echo $daysBehind; ?>" style="font-family: Verdana; font-size: 10px; text-align: center;"> 
		<input type="submit" value="Rapport" style="font-family: Verdana; font-size: 10px">
		</form>
		</div>
		   
			<script type="text/javascript" src="https://www.google.com/jsapi"></script>

			<script type="text/javascript">
				google.load('visualization', '1', {packages: ['corechart']});
				google.setOnLoadCallback(drawBoth);
				
				function drawBoth() {
					drawVisualization();
					drawVisualization2();
					drawVisualization3();
				}
				
				function drawVisualization() {
					var data = google.visualization.arrayToDataTable([
						['Dag', 'Inkommet via webben i kr', 'Lagda manuellt i kr', 'Totalt i kr'],
					<?php
					
					echo "\n";

					// $select = "select count(*) as qty, COALESCE(round(sum(o.grandTotalSEK),0), 0) as sumGrandTotalSEK, DATE (created) as date, isWebOrder ";
					/*
					$select = "select count(*) as qty, COALESCE(round(sum(o.totallines_sek),0), 0) as sumGrandTotalSEK, DATE (created) as date, isWebOrder ";
					$select .= " FROM v_c_order_report o WHERE ";
					$select .= " o.issotrx='Y' AND o.created > current_date - integer '" . $daysBehind . "' ";
					$select .= " group by 3,4 order by 3 ASC,4 DESC";
					*/
					$select = "select DATE (created) as date, COALESCE(round(sum(o.totallines_sek),0), 0) as sumGrandTotalSEK, isweborder as isweborder ";
					$select .= "FROM v_c_order_report o WHERE ";
					$select .= "o.issotrx='Y' AND o.created > current_date - integer '" . $daysBehind . "' ";
					$select .= "AND o.created <= current_date + integer '1' "; // denna rad bara f�r att s�kra upp konstiga datum fram�t i tiden
					$select .= "group by 1,3 order by 1 ASC,3 DESC ";
					//echo $select;
					$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
					$cnt = ($res ? pg_num_rows($res) : 0);
					$i = 0;
					$checkrow = true;

					while ($res && $row = pg_fetch_array($res)) {

						$i++;
						
						if ($row['isweborder'] == "Y" && $checkrow) {
						
							$total = 0;
							echo "['" . $row['date'] . "', ";
							echo (int)$row['sumgrandtotalsek'] . ", ";
							$total += $row['sumgrandtotalsek'];
							$checkrow = false;
						
						} elseif ($row['date'] != $current_pubdate && $row['isweborder'] == "Y" && !$checkrow && $checkrow2) {
						
							echo 0;
							echo ", " . $total;
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
							$total = 0;
							echo "['" . $row['date'] . "', ";
							echo (int)$row['sumgrandtotalsek'] . ", ";
							$total += $row['sumgrandtotalsek'];
							$checkrow = false;
							$checkrow2 = false;
						
						} elseif ($row['date'] != $current_pubdate && $row['isweborder'] == "Y" && !$checkrow && !$checkrow2) {
							echo 0;
							echo ", " . $total;
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
							$total = 0;
							echo "['" . $row['date'] . "', ";
							echo (int)$row['sumgrandtotalsek'] . ", ";
							$total += $row['sumgrandtotalsek'];
							$checkrow = false;
							$checkrow2 = true;
						}

						if ($row['isweborder'] == "N") {
							echo (int)$row['sumgrandtotalsek'];
							$total += $row['sumgrandtotalsek'];
							echo ", " . $total;
							// echo ",";
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
							$checkrow = true;
						
						}

						$current_pubdate = $row['date'];

						/*
						if ($row['date'] != $current_pubdate && $row['isweborder'] == "Y" && $checkrow) {

							echo 0;
							echo ", " . $total;
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
							echo "['" . $row['date'] . "', ";
							echo (int)$row['sumgrandtotalsek'] . ", ";
							$total += $row['sumgrandtotalsek'];
							$checkrow = false;

						}
						*/
						
						/*
						if ($res && $row = pg_fetch_array($res)) {
							$i++;
							if ($row['isweborder'] == "N") {
								echo (int)$row['sumgrandtotalsek'];
								$total += $row['sumgrandtotalsek'];

								echo ", " . $total;
								// echo ",";
								echo "]";
								if (($i)<$cnt)
									echo ",";
								echo "\n";

							} else {
								echo 0;
								echo ", " . $total;
								// echo ",";
								echo "]";
								if (($i)<$cnt)
									echo ",";
								echo "\n";
								$total = $row['sumgrandtotalsek'];
								echo "['" . $row['date'] . "', ";
								echo (int)$row['sumgrandtotalsek'] . ", ";
								$sjabo = true;
							}
						} else {
							echo 0;
							echo ", " . $total;
							// echo ",";
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
						}
						*/
						// sdfsdfsdfsdf
					}
					
					?>
					]);
					var options = {
					  title : 'Inkomna ordrar i SEK',
					  seriesType: "bars",
					  series: {0: {color: '#009900', type: "bar"}, 1: {color: '#0066FF', type: "bar"}, 2: {color: '#FF9900', type: "line"}},
					  legend: {position: 'top', textStyle: {fontSize: 14}},
					  chartArea:{left:100,top:80,width:1000,height:400}
					};

					var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
					chart.draw(data, options);
			  }

				function drawVisualization2() {
					var data = google.visualization.arrayToDataTable([
						['Dag', 'Inkommet via webben antal', 'Lagda manuellt antal', 'Totalt antal'],
					<?php

					echo "\n";

					// $select = "select count(*) as qty, COALESCE(round(sum(o.grandTotalSEK),0), 0) as sumGrandTotalSEK, DATE (created) as date, isWebOrder ";
					/*
					$select = "select count(*) as qty, COALESCE(round(sum(o.totallines_sek),0), 0) as sumGrandTotalSEK, DATE (created) as date, isWebOrder ";
					$select .= " FROM v_c_order_report o WHERE ";
					$select .= " o.issotrx='Y' AND o.created > current_date - integer '" . $daysBehind . "' ";
					$select .= " group by 3,4 order by 3 ASC,4 DESC";
					*/
					$select = "select  DATE (created) as date, count(*) as qty, isWebOrder ";
					$select .= "FROM v_c_order_report o WHERE ";
					$select .= "o.issotrx='Y' AND o.created > current_date - integer '" . $daysBehind . "' ";
					$select .= "AND o.created <= current_date + integer '1' "; // denna rad bara f�r att s�kra upp konstiga datum fram�t i tiden
					$select .= "group by 1,3 order by 1 ASC,3 DESC ";
					// echo $select;
					$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
					$cnt = ($res ? pg_num_rows($res) : 0);
					$i = 0;
					$checkrow = true;
					
					while ($res && $row = pg_fetch_array($res)) {
						
						$i++;
						
						if ($row['isweborder'] == "Y" && $checkrow) {
						
							$total = 0;
							// $total = $row['qty'];
							echo "['" . $row['date'] . "', ";
							echo $row['qty'] . ", ";
							$total += $row['qty'];
							$checkrow = false;
						
						} elseif ($row['date'] != $current_pubdate && $row['isweborder'] == "Y" && !$checkrow && $checkrow2) {
						
							echo 0;
							echo ", " . $total;
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
							$total = 0;
							echo "['" . $row['date'] . "', ";
							echo $row['qty'] . ", ";
							$total += $row['qty'];
							$checkrow = false;
							$checkrow2 = false;
						
						} elseif ($row['date'] != $current_pubdate && $row['isweborder'] == "Y" && !$checkrow && !$checkrow2) {
							echo 0;
							echo ", " . $total;
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
							$total = 0;
							echo "['" . $row['date'] . "', ";
							echo $row['qty'] . ", ";
							$total += $row['qty'];
							$checkrow = false;
							$checkrow2 = true;
						}

						if ($row['isweborder'] == "N") {
							echo $row['qty'];
							$total += $row['qty'];
							echo ", " . $total;
							// echo ",";
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
							$checkrow = true;
						
						}
						
						$current_pubdate = $row['date'];

						/*
						$compare_date = $row['date'];
						$i++;
						$total = $row['sumgrandtotalsek'];
						$tot = $row['qty'];
						echo "['" . $row['date'] . "', ";
						echo $row['qty'] . ", ";
						$ant = "" . $row['qty'] . ",";                    
						
						if ($res && $row = pg_fetch_array($res)) {
							if ($compare_date != $row['date']) {

								$i++;
								echo 0;
								echo ", " . $tot;
								echo "],";
								echo "\n";
								
								$tot = $row['qty'];
								echo "['" . $row['date'] . "', ";
								echo $row['qty'] . ", ";
								$ant = "" . $row['qty'] . ",";                    
								echo ", " . $tot;
								echo "]";
								if (($i)<$cnt)
									echo ",";
								echo "\n";
							
							
							} else {
								$i++;
								echo $row['qty'];
								$ant .= $row['qty'];
								$tot += $row['qty'];
							
								echo ", " . $tot;
								echo "]";
								if (($i)<$cnt)
									echo ",";
								echo "\n";
							}
						
						
						} else {
							echo 0;
							echo ", " . $tot;
							echo "]";
							echo "\n";
						}
						*/
						/*
						$i++;
						$total = $row['sumgrandtotalsek'];
						$tot = $row['qty'];
						echo "['" . $row['date'] . "', ";                    
						echo $row['qty'] . ",";
						$ant = "" . $row['qty'] . ",";                    
						
						if ($res && $row = pg_fetch_array($res)) {
							$i++;
							echo $row['qty'];
							$ant .= $row['qty'];
							$total += $row['sumgrandtotalsek'];
							$tot += $row['qty'];
						} else {
							echo 0;
							$ant .= "0";
						}
						echo ", " . $tot;
						echo ",";// . $ant . "," . $tot;
						echo "]";
						if (($i)<$cnt)
							echo ",";
						echo "\n";
						*/
	 
					}
					?>
					]);
					var options = {
					  title : 'Inkomna ordrar antal',
					  seriesType: "bars",
					  series: {0: {color: '#009900', type: "bar"}, 1: {color: '#0066FF', type: "bar"}, 2: {color: '#FF9900', type: "line"}},
					  legend: {position: 'top', textStyle: {fontSize: 14}},
					  chartArea:{left:100,top:80,width:1000,height:400}
					};

					var chart = new google.visualization.ComboChart(document.getElementById('chart_div2'));
					chart.draw(data, options);
			  }
				function drawVisualization3() {
					var data = google.visualization.arrayToDataTable([
						['Dag', 'Inkommet via webben snitt', 'Lagda manuellt snitt', 'Totalt snitt'],
					<?php

					echo "\n";

					// $select = "select count(*) as qty, COALESCE(round(sum(o.grandTotalSEK),0), 0) as sumGrandTotalSEK, DATE (created) as date, isWebOrder ";
					/*
					$select = "select count(*) as qty, COALESCE(round(sum(o.totallines_sek),0), 0) as sumGrandTotalSEK, DATE (created) as date, isWebOrder ";
					$select .= " FROM v_c_order_report o WHERE ";
					$select .= " o.issotrx='Y' AND o.created > current_date - integer '" . $daysBehind . "' ";
					$select .= " group by 3,4 order by 3 ASC,4 DESC";
					*/
					$select = "select count(*) as qty, COALESCE(round(sum(o.totallines_sek),0), 0) as sumGrandTotalSEK, DATE (created) as date, isWebOrder ";
					$select .= "FROM v_c_order_report o WHERE ";
					$select .= "o.issotrx='Y' AND o.created > current_date - integer '" . $daysBehind . "' ";
					$select .= "AND o.created <= current_date + integer '1' "; // denna rad bara f�r att s�kra upp konstiga datum fram�t i tiden
					$select .= "group by 3,4 order by 3 ASC,4 DESC ";
					// echo $select;
					$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
					$cnt = ($res ? pg_num_rows($res) : 0);
					$i = 0;
					$checkrow = true;
					
					while ($res && $row = pg_fetch_array($res)) {
						
						$i++;
						
						$snittvardet = round($row['sumgrandtotalsek'] / $row['qty']);
						
						if ($row['isweborder'] == "Y" && $checkrow) {
						
							$total = 0;
							$total_qty = 0;
							$total_sek = 0;
							// $total = $row['qty'];
							echo "['" . $row['date'] . "', ";
							echo $snittvardet . ", ";
							$total_qty += $row['qty'];
							$total_sek += $row['sumgrandtotalsek'];
							$checkrow = false;
						
						} elseif ($row['date'] != $current_pubdate && $row['isweborder'] == "Y" && !$checkrow && $checkrow2) {
						
							echo 0;
							// echo ", " . $total;
							$total_snitt = round($total_sek / $total_qty);
							echo ", " . $total_snitt;
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
							$total = 0;
							$total_qty = 0;
							$total_sek = 0;
							echo "['" . $row['date'] . "', ";
							echo $snittvardet . ", ";
							$total_qty += $row['qty'];
							$total_sek += $row['sumgrandtotalsek'];
							$checkrow = false;
							$checkrow2 = false;
						
						} elseif ($row['date'] != $current_pubdate && $row['isweborder'] == "Y" && !$checkrow && !$checkrow2) {
							echo 0;
							// echo ", " . $total;
							$total_snitt = round($total_sek / $total_qty);
							echo ", " . $total_snitt;
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
							$total = 0;
							$total_qty = 0;
							$total_sek = 0;
							echo "['" . $row['date'] . "', ";
							echo $snittvardet . ", ";
							$total_qty += $row['qty'];
							$total_sek += $row['sumgrandtotalsek'];
							$checkrow = false;
							$checkrow2 = true;
						}

						if ($row['isweborder'] == "N") {
							echo $snittvardet;
							$total_qty += $row['qty'];
							$total_sek += $row['sumgrandtotalsek'];
							// echo ", " . $total;
							$total_snitt = round($total_sek / $total_qty);
							echo ", " . $total_snitt;
							// echo ",";
							echo "]";
							if (($i)<$cnt)
								echo ",";
							echo "\n";
							$checkrow = true;
						
						}
						
						$current_pubdate = $row['date'];

						/*
						$compare_date = $row['date'];
						$i++;
						$total = $row['sumgrandtotalsek'];
						$tot = $row['qty'];
						echo "['" . $row['date'] . "', ";
						echo $row['qty'] . ", ";
						$ant = "" . $row['qty'] . ",";                    
						
						if ($res && $row = pg_fetch_array($res)) {
							if ($compare_date != $row['date']) {

								$i++;
								echo 0;
								echo ", " . $tot;
								echo "],";
								echo "\n";
								
								$tot = $row['qty'];
								echo "['" . $row['date'] . "', ";
								echo $row['qty'] . ", ";
								$ant = "" . $row['qty'] . ",";                    
								echo ", " . $tot;
								echo "]";
								if (($i)<$cnt)
									echo ",";
								echo "\n";
							
							
							} else {
								$i++;
								echo $row['qty'];
								$ant .= $row['qty'];
								$tot += $row['qty'];
							
								echo ", " . $tot;
								echo "]";
								if (($i)<$cnt)
									echo ",";
								echo "\n";
							}
						
						
						} else {
							echo 0;
							echo ", " . $tot;
							echo "]";
							echo "\n";
						}
						*/
						/*
						$i++;
						$total = $row['sumgrandtotalsek'];
						$tot = $row['qty'];
						echo "['" . $row['date'] . "', ";                    
						echo $row['qty'] . ",";
						$ant = "" . $row['qty'] . ",";                    
						
						if ($res && $row = pg_fetch_array($res)) {
							$i++;
							echo $row['qty'];
							$ant .= $row['qty'];
							$total += $row['sumgrandtotalsek'];
							$tot += $row['qty'];
						} else {
							echo 0;
							$ant .= "0";
						}
						echo ", " . $tot;
						echo ",";// . $ant . "," . $tot;
						echo "]";
						if (($i)<$cnt)
							echo ",";
						echo "\n";
						*/
	 
					}
					?>
					]);
					var options = {
					  title : 'Snittordervärdet',
					  seriesType: "bars",
					  series: {0: {color: '#009900', type: "bar"}, 1: {color: '#0066FF', type: "bar"}, 2: {color: '#FF9900', type: "line"}},
					  legend: {position: 'top', textStyle: {fontSize: 14}},
					  chartArea:{left:100,top:80,width:1000,height:400}
					};

					var chart = new google.visualization.ComboChart(document.getElementById('chart_div3'));
					chart.draw(data, options);
			  }

		  </script>
		   
		  <div id="chart_div" style="width: 1300px; height: 550px;"></div>
		  <div id="chart_div2" style="width: 1300px; height: 550px;"></div>
		  <div id="chart_div3" style="width: 1300px; height: 550px;"></div>


			<?php
			
	}
	/* */
	function printReceivedOrdersNew() {
		global $ref_dagensdatum, $dagensdatum, $details, $daysBehind;

		$desiderow = true;
		$countrows = 0;
		$totsum = 0;
		$totsumLagershop = 0;
		$totsumFI = 0;
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		if ($daysBehind == "")
				$daysBehind = 14;
			
		echo "<div class=\"salespanel\">"; // f�rsta divven till v�nster
		echo "<div><h2>Inkomna ordrar till cyberphoto - $dagensdatum</h2></div>";
		?>
		<div>
		<form name="sampleform" method="POST">
		<input type="hidden" name="show" value="overall">
		<span class="abbrubrik">Antal dagar bakåt:</span><br>
		<input type="text" name="daysBehind" size=12 value="<?php echo $daysBehind; ?>" style="font-family: Verdana; font-size: 10px"> 
			
		<input type="submit" value="Rapport" style="font-family: Verdana; font-size: 10px">
		</form>
		</div>
		   
			<script type="text/javascript" src="https://www.google.com/jsapi"></script>

			<script type="text/javascript">
				google.load('visualization', '1', {packages: ['corechart']});
				google.setOnLoadCallback(drawBoth);
				
				function drawBoth() {
					drawVisualization();
					drawVisualization2();
				}
				
				function drawVisualization() {
					var data = google.visualization.arrayToDataTable([
						['Dag', 'Lagda manuellt i kr', 'Inkommet via webben i kr', 'Totalt i kr'],
					<?php

					$select = "select count(*) as qty, COALESCE(round(sum(o.grandTotalSEK),0), 0) as sumGrandTotalSEK, DATE (created) as date, isWebOrder ";
					$select .= " FROM v_c_order_report o WHERE ";
					$select .= " o.issotrx='Y' AND o.created > current_date - integer '" . $daysBehind . "' ";
					$select .= " group by 3,4 order by 3 DESC,4";
					//echo $select;
					$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
					$cnt = ($res ? pg_num_rows($res) : 0);
					$i = 0;
					
					while ($res && $row = pg_fetch_array($res)) {
						$i++;
						$total = $row['sumgrandtotalsek'];
						$tot = $row['qty'];
						echo "['" . $row['date'] . "', ";                    
						echo $row['sumgrandtotalsek'] . ",";
						$ant = "" . $row['qty'] . ",";                    
						
						if ($res && $row = pg_fetch_array($res)) {
							$i++;
							echo (int)$row['sumgrandtotalsek'];
							$ant .= $row['qty'];
							$total += $row['sumgrandtotalsek'];
							$tot += $row['qty'];
						} else {
							echo 0;
							$ant .= "0";
						}
						echo ", " . $total;
						echo ",";// . $ant . "," . $tot;
						echo "]";
						if (($i)<$cnt)
							echo ",";
						echo "\n";
	 
					}
					?>
					]);
					var options = {
					  title : 'Inkomna ordrar i SEK',
					  vAxis: {title: "Inkommet i SEK"},
					  hAxis: {title: "Datum"},
					  seriesType: "bars",
					  series: {5: {type: "line"}}
					};

					var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
					chart.draw(data, options);
			  }

				function drawVisualization2() {
					var data = google.visualization.arrayToDataTable([
						['Dag', 'Lagda manuellt antal', 'Inkommet via webben antal', 'Totalt antal'],
					<?php

					$select = "select count(*) as qty, COALESCE(round(sum(o.grandTotalSEK),0), 0) as sumGrandTotalSEK, DATE (created) as date, isWebOrder ";
					$select .= " FROM v_c_order_report o WHERE ";
					$select .= " o.issotrx='Y' AND o.created > current_date - integer '" . $daysBehind . "' ";
					$select .= " group by 3,4 order by 3 DESC,4";
					//echo $select;
					$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
					$cnt = ($res ? pg_num_rows($res) : 0);
					$i = 0;
					
					while ($res && $row = pg_fetch_array($res)) {
						$i++;
						$total = $row['sumgrandtotalsek'];
						$tot = $row['qty'];
						echo "['" . $row['date'] . "', ";                    
						echo $row['qty'] . ",";
						$ant = "" . $row['qty'] . ",";                    
						
						if ($res && $row = pg_fetch_array($res)) {
							$i++;
							echo $row['qty'];
							$ant .= $row['qty'];
							$total += $row['sumgrandtotalsek'];
							$tot += $row['qty'];
						} else {
							echo 0;
							$ant .= "0";
						}
						echo ", " . $tot;
						echo ",";// . $ant . "," . $tot;
						echo "]";
						if (($i)<$cnt)
							echo ",";
						echo "\n";
	 
					}
					?>
					]);
					var options = {
					  title : 'Inkomna ordrar antal',
					  vAxis: {title: "Inkommet i antal ordrar"},
					  hAxis: {title: "Datum"},
					  seriesType: "bars",
					  series: {5: {type: "line"}}
					};

					var chart = new google.visualization.ComboChart(document.getElementById('chart_div2'));
					chart.draw(data, options);
			  }
			</script>
		   
		  <div id="chart_div" style="width: 900px; height: 500px;"></div>
		  <div id="chart_div2" style="width: 900px; height: 500px;"></div>


			<?php
			
	}
	
	function printOverallDeliveriesFromAD() {
		global $ref_dagensdatum, $dagensdatum, $details;

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
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		$EURrate = $this->getEURrate($dagensdatum);
		$NOKrate = $this->getEURrate($dagensdatum,true);
		// echo $NOKrate;
		
		echo "<div class=\"salespanel\">"; // f�rsta divven till v�nster
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
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			echo "<div class=\"top5\">EUR kurs: $EURrate</div>";
			echo "<div class=\"bottom5\">NOK kurs: $NOKrate</div>";
		}
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
			echo "<td width=\"80\" align=\"center\"><b>Omräknat</td>";
			echo "<td width=\"75\">&nbsp;</td>";
			echo "</tr>";
		}
		
		// $select = "SELECT documentno, updated, (select value from ad_user where ad_user_id=m_inout.updatedby) as user, (select totallines from c_invoice where c_invoice_id=m_inout.c_invoice_id) as total, (select c_currency_id from c_invoice where c_invoice_id=m_inout.c_invoice_id) as currency, deliveryViaRule ";
		$select = "SELECT (select documentno from c_order where c_order_id=m_inout.c_order_id) as ordernr, updated, (select value from ad_user where ad_user_id=m_inout.updatedby) as user, (select totallines from c_invoice where c_invoice_id=m_inout.c_invoice_id) as total, (select c_currency_id from c_invoice where c_invoice_id=m_inout.c_invoice_id) as currency, deliveryViaRule, documentno ";
		$select .= "FROM m_inout WHERE docstatus IN ('CO') AND deliveryViaRule IN ('S','P') ";
		$select .= "AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' ";
		$select .= "AND AD_Client_ID=1000000 AND M_rma_ID is null ";
		// $select .= "AND date(updated)>='" . $dagensdatum . " 00:00:00' AND date(updated)<='" . $dagensdatum . " 23:59:59' ";
		$select .= "AND date(pickdate)>='" . $dagensdatum . " 00:00:00' AND date(pickdate)<='" . $dagensdatum . " 23:59:59' ";
		$select .= "ORDER BY updated DESC ";
		// $select .= " ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
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
				if ($row[4] == 287) {
					$omraknatNO = $NOKrate * $row[3];
				}
			
				if ($details == "yes") {
					echo "<tr>";
					echo "<td class=\"$rowcolor\" align=\"center\">$row[6]</td>";
					echo "<td class=\"$rowcolor\" align=\"center\"><a href=\"javascript:winPopupCenter(500, 1000, 'order_info.php?order=$row[0]');\">$row[0]</a></td>";
					echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d H:i:s", strtotime($row[1])) . "</td>";
					echo "<td class=\"$rowcolor\" align=\"center\">" . strtoupper($row[2]) . "</td>";
					echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($row[3], 0, ',', ' ') . "&nbsp;</td>";
					if ($row[4] == 102) {
						echo "<td class=\"$rowcolor\" align=\"center\">EUR</td>";
					} elseif ($row[4] == 287) {
						echo "<td class=\"$rowcolor\" align=\"center\">NOK</td>";
					} else {
						echo "<td class=\"$rowcolor\" align=\"center\">SEK</td>";
					}
					if ($row[4] == 102) {
						// echo "<td>" . round($omraknat,0) . "</td>";
						echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($omraknat, 0, ',', ' ') . " SEK</td>";
					} elseif ($row[4] == 287) {
						// echo "<td>" . round($omraknat,0) . "</td>";
						echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($omraknatNO, 0, ',', ' ') . " SEK</td>";
					} else {
						echo "<td class=\"$rowcolor\">&nbsp;</td>";
					}
					if ($row[5] == "P") {
						echo "<td align=\"center\" class=\"$rowcolor\">Lagershop</td>";
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
					$count_SE++;
					$totsumSE = $totsumSE + $row[3];
					$totsum = $totsum + $row[3];
				}
				$omraknat = 0;
				$omraknatNO = 0;
			}

		if ($details == "yes") {
			echo "</table>";
			echo "</div>";
		}
		$sent_posten = $countrows - $count_lagershop;
		// echo "<div><h2>Totalt: " . $countrows . " st - " . number_format($totsum, 0, ',', ' ') . " SEK<h2></div>";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo "<div class=\"align_left\"><a href=\"javascript:winPopupCenter(900, 800, '/order/sales_report_manuel.php?value=" . round($totsum,0) . "&date=$dagensdatum');\">Rapportera detta</a> <a href=\"?firstinput=" . date('Y-m-d',strtotime($dagensdatum . "+1 days")) . "\">Nästa dag</a></div>\n";
		}
		if ($countrows > 0) {
			echo "<table>\n";
			echo "\t<tr>\n";
			echo "\t\t<td class=\"turnover_head_totti align_left\"><img border=\"0\" src=\"img/turnover_totalt.png\"></td>\n";
			echo "\t\t<td class=\"turnover_head align_right\">" . $countrows . " st</td>\n";
			echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsum, 0, ',', ' ') . " SEK</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td><img border=\"0\" src=\"img/turnover_truck.png\" title=\"Det som skickas med speditör från oss\"></td>\n";
			echo "\t\t<td class=\"turnover_head align_right\">" . $sent_posten . " st</td>\n";
			echo "\t\t<td class=\"turnover_head_amount align_right\"></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td><img border=\"0\" src=\"img/turnover_warehouse.png\" title=\"Det som hämtas i lagershopen\"></td>\n";
			echo "\t\t<td class=\"turnover_head align_right\">" . $count_lagershop . " st</td>\n";
			echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsumLagershop, 0, ',', ' ') . " SEK</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td><img border=\"0\" src=\"img/turnover_sverige.png\" title=\"Det som skickas till Sverige\"></td>\n";
			echo "\t\t<td class=\"turnover_head align_right\">" . $count_SE . " st</td>\n";
			echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsumSE, 0, ',', ' ') . " SEK</td>\n";
			echo "\t</tr>\n";
			if (strtotime("2016-06-15") > strtotime($dagensdatum)) {
				echo "\t<tr>\n";
				echo "\t\t<td><img border=\"0\" src=\"img/turnover_finland.png\" title=\"Det som skickas till Finland\"></td>\n";
				echo "\t\t<td class=\"turnover_head align_right\">" . $count_FI . " st</td>\n";
				echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsumFI, 0, ',', ' ') . " SEK</td>\n";
				echo "\t</tr>\n";
				echo "\t<tr>\n";
				echo "\t\t<td><img border=\"0\" src=\"img/turnover_norge.png\" title=\"Det som skickas till Norge\"></td>\n";
				echo "\t\t<td class=\"turnover_head align_right\">" . $count_NO . " st</td>\n";
				echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsumNO, 0, ',', ' ') . " SEK</td>\n";
				echo "\t</tr>\n";
			}
			echo "\t<tr>\n";
			echo "\t\t<td class=\"turnover_head_totti align_left\"><img border=\"0\" src=\"img/turnover_snitt.png\"></td>\n";
			echo "\t\t<td class=\"turnover_head_totti align_left\"><img border=\"0\" src=\"img/turnover_empty.png\"></td>\n";
			echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsum/$countrows, 0, ',', ' ') . " SEK</td>\n";
			echo "\t</tr>\n";
			echo "</table>\n";
	
			// echo "<div><h3>Varav Finland: " . $count_FI . " st - " . number_format($totsumFI, 0, ',', ' ') . " SEK<h3></div>";
			// echo "<div><h3>Varav Norge: " . $count_NO . " st - " . number_format($totsumNO, 0, ',', ' ') . " SEK<h3></div>";
			// echo "<div><h3>Total summa: " . number_format($totsum, 0, ',', ' ') . " SEK<h3></div>";
			// echo "<div><h3>Snittorderv�rdet: " . number_format($totsum/$countrows, 0, ',', ' ') . " SEK<h3></div>";
		}
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			// $this->printOverallDeliveriesFromADOneYearBack($totsum);
			// echo "<div><h3>Snittorderv�rdet: " . number_format($totsum/$countrows, 0, ',', ' ') . " SEK<h3></div>";
		}
		if ($details != "yes") {
			$this->printOverallDeliveriesFromADOneYearBack($totsum);
		} else {
			echo "</div>";
		}

	}


	function printOverallDeliveriesFromADOneYearBack($totsumNow) {
		global $dagensdatum;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98") {
			echo "här: " . $dagensdatum;
		}
		// echo 'Current PHP version: ' . phpversion();

		$today_day = date("N", strtotime($dagensdatum)); // tar fram vilken veckodag det �r idag
		$day_year_before = date('Y-m-d', strtotime('-1 year', strtotime($dagensdatum))); // tar fram motsvarande dag ett �r tillbaka
		
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

		if ($dagensdatum == "2025-04-17") // f�r att det ska bli r�ttvis j�mf�relse
			$display_day_before = "2024-03-28";

		if ($dagensdatum == "2025-04-22") // f�r att det ska bli r�ttvis j�mf�relse
			$display_day_before = "2024-04-02";

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
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		
		
		$EURrate = $this->getEURrate($display_day_before);
		$NOKrate = $this->getEURrate($display_day_before,true);
		
		echo "<hr noshade color=\"#C0C0C0\" align=\"left\" width=\"630\" size=\"1\">";
		echo "<div><h1>Leveranser från CyberPhoto motsvarande dag i fjol - $display_day_before</h1></div>";
		if (strtotime($display_day_before) < strtotime("2011-03-14")) {
			echo "<div><h3><i>Ingen historik visas här färrän vi passerat 12 mars 2012</i></h3></div>";
		}

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			echo "<div class=\"top5\">EUR kurs: $EURrate</div>";
			echo "<div class=\"bottom5\">NOK kurs: $NOKrate</div>";
		}
		
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

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
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
				if ($row[4] == 287) {
					$omraknatNO = $NOKrate * $row[3];
				}
				
				$countrows++;
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
					$count_SE++;
					$totsumSE = $totsumSE + $row[3];
					$totsum = $totsum + $row[3];
				}
				$omraknat = 0;
			}

		$sent_posten = $countrows - $count_lagershop;
		// echo "<div><h2>Totalt: " . $countrows . " st - " . number_format($totsum, 0, ',', ' ') . " SEK<h2></div>";
		if ($countrows > 0) {
			echo "<table>\n";
			echo "\t<tr>\n";
			echo "\t\t<td class=\"turnover_head_totti align_left\"><img border=\"0\" src=\"img/turnover_totalt.png\"></td>\n";
			echo "\t\t<td class=\"turnover_head align_right\">" . $countrows . " st</td>\n";
			echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsum, 0, ',', ' ') . " SEK</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td><img border=\"0\" src=\"img/turnover_truck.png\" title=\"Det som skickas med speditör från oss\"></td>\n";
			echo "\t\t<td class=\"turnover_head align_right\">" . $sent_posten . " st</td>\n";
			echo "\t\t<td class=\"turnover_head_amount align_right\"></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td><img border=\"0\" src=\"img/turnover_warehouse.png\" title=\"Det som hämtas i lagershopen\"></td>\n";
			echo "\t\t<td class=\"turnover_head align_right\">" . $count_lagershop . " st</td>\n";
			echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsumLagershop, 0, ',', ' ') . " SEK</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td><img border=\"0\" src=\"img/turnover_sverige.png\" title=\"Det som skickas till Sverige\"></td>\n";
			echo "\t\t<td class=\"turnover_head align_right\">" . $count_SE . " st</td>\n";
			echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsumSE, 0, ',', ' ') . " SEK</td>\n";
			echo "\t</tr>\n";
			if (strtotime("2016-06-15") > strtotime($dagensdatum)) {
				echo "\t<tr>\n";
				echo "\t\t<td><img border=\"0\" src=\"img/turnover_finland.png\" title=\"Det som skickas till Finland\"></td>\n";
				echo "\t\t<td class=\"turnover_head align_right\">" . $count_FI . " st</td>\n";
				echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsumFI, 0, ',', ' ') . " SEK</td>\n";
				echo "\t</tr>\n";
				echo "\t<tr>\n";
				echo "\t\t<td><img border=\"0\" src=\"img/turnover_norge.png\" title=\"Det som skickas till Norge\"></td>\n";
				echo "\t\t<td class=\"turnover_head align_right\">" . $count_NO . " st</td>\n";
				echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsumNO, 0, ',', ' ') . " SEK</td>\n";
				echo "\t</tr>\n";
			}
			echo "\t<tr>\n";
			echo "\t\t<td class=\"turnover_head_totti align_left\"><img border=\"0\" src=\"img/turnover_snitt.png\"></td>\n";
			echo "\t\t<td class=\"turnover_head_totti align_left\"><img border=\"0\" src=\"img/turnover_empty.png\"></td>\n";
			echo "\t\t<td class=\"turnover_head_amount align_right\">" . number_format($totsum/$countrows, 0, ',', ' ') . " SEK</td>\n";
			echo "\t</tr>\n";
			echo "</table>\n";
			
			// echo "<div><h3>Varav spedit�r: " . $sent_posten . " st<h3></div>";
			// echo "<div><h3>Varav lagershop: " . $count_lagershop . " st - " . number_format($totsumLagershop, 0, ',', ' ') . " SEK<h3></div>";
			// echo "<div><h3>Varav Finland: " . $count_FI . " st - " . number_format($totsumFI, 0, ',', ' ') . " SEK<h3></div>";
			// echo "<div><h3>Total summa: " . number_format($totsum, 0, ',', ' ') . " SEK<h3></div>";
			// echo "<div><h3>Snittorderv�rdet: " . number_format($totsum/$countrows, 0, ',', ' ') . " SEK<h3></div>";
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
			} elseif ($diffinprocent > 200 && $diffinprocent < 300) {
				$showMax = 300;
			} elseif ($diffinprocent > 300 && $diffinprocent < 400) {
				$showMax = 400;
			} elseif ($diffinprocent > 400 && $diffinprocent < 500) {
				$showMax = 500;
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
			} elseif ($diffinprocent > 200 && $diffinprocent < 300) {
				$showMax = 300;
			} elseif ($diffinprocent > 300 && $diffinprocent < 400) {
				$showMax = 400;
			} elseif ($diffinprocent > 400 && $diffinprocent < 500) {
				$showMax = 500;
			} else {
				$showMax = $diffinprocent;
			}
			
		}
		// echo "<hr noshade color=\"#C0C0C0\" align=\"left\" width=\"630\" size=\"1\">";
		echo "</div>"; // f�rsta divven till v�nster slutar h�r
		echo "<div class=\"salespanelsum\">";
		echo "<div><h1>Skillnad motsvarande dag i fjol</h1></div>";
		if (strtotime($display_day_before) < strtotime("2011-03-14")) {
			echo "<div><h3><i>Korrekt differens visas h�r n�r vi passerat 12 mars 2012</i></h3></div>";
		}
		// echo "<div><span class=\"$class\">" . number_format($valuediff, 0, ',', ' ') . " kr (" . $diffinprocent . "%)</span></div>";
		echo "<div><span class=\"$class\">";
		if ($valuediff > 0) {
			echo "+";
		}
		echo number_format($valuediff, 0, ',', ' ');
		echo " kr (" . $diffinprocent . "%)</span></div>";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			// echo "<div><h3>Snittorderv�rdet: " . number_format($totsum/$countrows, 0, ',', ' ') . " SEK<h3></div>";
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
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}
	function sendMessOrder($summakolla) {

		$recipient = "order";
		// $recipient .= " ss";
		
		$subj = "Upplysning!";

		$extra = "From: webmaster";
		
		$text1 = "Klockan är nu efter 15:45 och det finns " . $summakolla . " st ordrar som måste kollas!\n\nVänligen vidta lämplig åtgärd.\n\n// SysAdmin";
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}

	function sendMessOrderTotal($land) {
		global $dagensdatum;

		$friseradate = date("Y-m-d", time());
		// $recipient = "order";
		$recipient .= " po";
		$recipient .= " ss";
		
		if ($land == 358) {
			$subj = "Summering Finland " . $friseradate;
		} else {
			$subj = "Summering Sverige " . $friseradate;
		}

		$extra = "From: sysadmin";
		
		$text1 = $this->getTotalValueToday($land);
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

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
			// $valuta = " EUR"; // evetuellt kommer vi k�ra EUR ocks�
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
	
	function displayIncommingOrders($sv,$fi,$no) {
		global $delivered, $svea, $part_delivered, $one_week, $sales_by_seller, $group_by_seller, $group_by_litium, $only_shop;
	
		$countrow = 0;
		$countrow_by_day = 0;
		$ordervarde = 0;
		$margin = 0;
		unset($row_note);
	
		echo "<div class=\"floatleft top20\">\n";
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		echo "\t<tr>\n";
		if ($group_by_seller == "yes") {
			echo "\t\t<td width=\"50\" class=\"align_center\"><b>Antal</b></td>\n";
			echo "\t\t<td width=\"50\" class=\"align_center\"><b>Säljare</b></td>\n";
			echo "\t\t<td width=\"100\" class=\"align_center\"><b>Ordersumma</b></td>\n";
		} else {
			// echo "\t\t<td width=\"25\">&nbsp;</td>\n";
			echo "\t\t<td width=\"110\"><b>Datum</b></td>\n";
			echo "\t\t<td width=\"70\" class=\"align_center\"><b>Order nr</b></td>\n";
			echo "\t\t<td width=\"25\">&nbsp;</td>\n";
			echo "\t\t<td width=\"320\"><b>Affärspartner</b></td>\n";
			echo "\t\t<td width=\"190\"><b>Betalsätt</b></td>\n";
			echo "\t\t<td width=\"100\" class=\"align_center\"><b>Ordersumma</b></td>\n";
			echo "\t\t<td width=\"90\" class=\"align_center\"><b>TB</b></td>\n";
			echo "\t\t<td width=\"70\" class=\"align_center\"><b>TG</b></td>\n";
			/*
			if ($sales_by_seller == "yes") {
				echo "\t\t<td width=\"50\" class=\"align_center\"><b>Lagd av</b></td>\n";
			}
			*/
			echo "\t\t<td width=\"50\" class=\"align_center\"><b>Lagd av</b></td>\n";
			echo "\t\t<td width=\"80\" class=\"align_center\"><b>&nbsp;</b></td>\n";
			echo "\t\t<td width=\"150\" class=\"align_left\">&nbsp;<b>Notering</b></td>\n";
		}
		echo "\t</tr>\n";
		
		if ($group_by_seller == "yes") {
			$select  = "SELECT count(o.documentno) AS antal, SUM(o.totallines) AS summa, ad.value, curr.iso_code  ";
		} else {
			$select  = "SELECT o.created, o.documentno, bp.name, o.marginamt, o.totallines, curr.iso_code, o.c_currency_id,  ";
			$select .= "o.margin, o.order_url, o.isdelivered, o.ispartdelivered, pay.name AS betalning, loc.c_country_id, ad.value, ";
			$select .= "o.deliveryviarule ";
		}
		$select .= "FROM c_order o ";
		$select .= "JOIN c_bpartner bp ON o.c_bpartner_id = bp.c_bpartner_id ";
		$select .= "JOIN ad_user ad ON o.salesrep_id = ad.ad_user_id ";
		$select .= "JOIN c_currency curr ON curr.c_currency_id = o.c_currency_id ";
		$select .= "JOIN c_paymentterm pay ON pay.c_paymentterm_id = o.c_paymentterm_id ";
		$select .= "JOIN c_bpartner_location bpl ON bpl.c_bpartner_location_id = o.c_bpartner_location_id ";
		$select .= "JOIN c_location loc ON loc.c_location_id = bpl.c_location_id ";
		// $select .= "WHERE o.c_doctype_id = 1000030 AND o.docstatus IN ('CO','IP') ";
		$select .= "WHERE o.c_doctype_id = 1000030 AND o.docstatus NOT IN ('VO', 'RE') ";
		if ($svea == "yes") {
			if ($fi) {
				$select .= " AND o.c_paymentterm_id IN (1000025,1000027) AND o.totallines > 500 ";
			} else {
				$select .= " AND o.c_paymentterm_id IN (1000025,1000027) AND o.totallines > 4000 ";
			}
		}
		if ($delivered == "no" && $part_delivered == "no") {
			// $select .= "AND o.created > current_date - integer '730' ";
			$select .= "AND o.created > '" . date("Y-m-d H:i:s",strtotime("-2 years")) . "' ";
		} elseif ($delivered == "no") {
			// $select .= "AND o.created > current_date - integer '180' ";
			$select .= "AND o.created > '" . date("Y-m-d H:i:s",strtotime("-180 days")) . "' ";
		} elseif ($one_week == "yes") {
			// $select .= "AND o.created > current_date - integer '7' ";
			$select .= "AND o.created > '" . date("Y-m-d H:i:s",strtotime("-1 week")) . "' ";
		} else {
			// $select .= "AND o.created > current_date - integer '3' ";
			$select .= "AND o.created > '" . date("Y-m-d H:i:s",strtotime("-3 days")) . "' ";
		}
		if ($part_delivered == "no") {
			$select .= "AND o.ispartdelivered = 'N' ";
		}
		if ($fi) {
			$select .= "AND o.c_currency_id = 102 ";
		} elseif ($no) {
			$select .= "AND o.c_currency_id = 287 ";
		} else {
			$select .= "AND o.c_currency_id = 311 ";
		}
		if ($delivered == "no") {
			$select .= "AND isdelivered = 'N' ";
		}
		if ($group_by_litium == "yes") {
			$select .= "AND o.salesrep_id = '1652736' ";
		}
		if ($sales_by_seller == "yes") {
			// $select .= "AND NOT o.salesrep_id = '1000121' ";
			$select .= "AND NOT o.salesrep_id IN ('1000121','1652736') ";
		}
		if ($only_shop == "yes") {
			$select .= "AND o.deliveryviarule = 'P' ";
		}
		if ($group_by_seller == "yes") {
			$select .= "GROUP BY ad.value, curr.iso_code ";
			$select .= "ORDER BY antal DESC ";
		} else {
			$select .= "ORDER BY o.created DESC ";
		}
		// $select .= "LIMIT 500 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
	
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo "h�r: " . $res;
		}
	
	
		if ($res && pg_num_rows($res) > 0) {
		
			while ($res && $row = pg_fetch_object($res)) {
	
				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}
				
				if ($group_by_seller == "yes") {
					
					echo "\t<tr>";
					// echo "\t\t<td class=\"$backcolor\" align=\"center\">$countrow</td>\n";
					echo "\t\t<td align=\"center\" class=\"$backcolor\">" . $row->antal . "</td>\n";
					echo "\t\t<td align=\"center\" class=\"$backcolor\">" . strtoupper($row->value) . "</td>\n";
					echo "\t\t<td align=\"right\" class=\"$backcolor\">" . number_format($row->summa, 0, ',', ' ') . " " . $row->iso_code . "</td>\n";
					echo "\t</tr>\n";
					
					$countrow++;
					$countrow_by_day++;
					$ordervarde = $ordervarde + $row->summa;
					$valuta_kod = $row->iso_code;

				} else {
					
					if ($row->isdelivered == "N" && $row->ispartdelivered == "Y") {
						$row_note .= "är dellevererad";
					}
					
					$this_day = date("Y-m-d", strtotime($row->created));
					// echo "f�rsta: " . $this_day . "<br>";
					
					if ($this_day != $actual_day) {
						
						if ($countrow_by_day != 0) {
							echo "<td colspan=\"25\" class=\"align_left bold\">" . $countrow_by_day . " st</td>";
							echo "\t<tr>\n";
							echo "<td colspan=\"25\" class=\"align_left bold\">&nbsp;</td>";
							echo "\t</tr>\n";
							$countrow_by_day = 0;
						}
							
						echo "<tr>";
						if ($this_day == date("Y-m-d", time())) {
							echo "<td colspan=\"25\" class=\"align_left bold\">" . CDeparture::replace_days(date("l", strtotime($row->created))) . " (idag)</td>";
						} else {
							echo "<td colspan=\"25\" class=\"align_left bold\">" . CDeparture::replace_days(date("l", strtotime($row->created))) . "</td>";
						}
						echo "</tr>";
							
					}
					
					
					$disp_day = date('l', strtotime("$row->created"));
					$disp_day = CDeparture::replace_days($disp_day);
					
					
					echo "\t<tr>";
					// echo "\t\t<td class=\"$backcolor\" align=\"center\">$countrow</td>\n";
					echo "\t\t<td class=\"$backcolor\">" . date("Y-m-d H:i", strtotime($row->created)) . "</td>\n";
					echo "\t\t<td class=\"$backcolor\" align=\"center\"><a href=\"javascript:winPopupCenter(500, 1000, 'order_info.php?order=$row->documentno');\">$row->documentno</a></td>\n";
					if ($row->iso_code == "EUR") {
						echo "\t\t<td class=\"align_center\"><img border=\"0\" src=\"fi_mini.jpg\"></td>\n";
					} elseif ($row->iso_code == "NOK") {
						echo "\t\t<td class=\"align_center\"><img border=\"0\" src=\"no_mini.jpg\"></td>\n";
					} elseif ($row->c_country_id == 167) {
						echo "\t\t<td class=\"align_center\"><img border=\"0\" src=\"dk_mini.jpg\"></td>\n";
					} else {
						echo "\t\t<td class=\"align_center\"><img border=\"0\" src=\"sv_mini.jpg\"></td>\n";
					}
					echo "\t\t<td class=\"$backcolor\">" . $row->name . "</td>\n";
					echo "\t\t<td class=\"$backcolor\">" . $row->betalning . "</td>\n";
					echo "\t\t<td align=\"right\" class=\"$backcolor\">" . number_format($row->totallines, 0, ',', ' ') . " " . $row->iso_code . "</td>\n";
					echo "\t\t<td align=\"right\" class=\"$backcolor\">" . number_format($row->marginamt, 0, ',', ' ') . " " . $row->iso_code . "</td>\n";
					echo "\t\t<td align=\"right\" class=\"$backcolor\">" . $row->margin . " %</td>\n";
					/*
					if ($sales_by_seller == "yes") {
						echo "\t\t<td align=\"center\" class=\"$backcolor\">" . strtoupper($row->value) . "</td>\n";
					}
					*/
					echo "\t\t<td align=\"center\" class=\"$backcolor\">" . strtoupper($row->value) . "</td>\n";
					if ($fi) {
						echo "\t\t<td align=\"center\" class=\"$backcolor\"><a target=\"_blank\" href=\"https://www.cyberphoto.fi/kundvagn/tilaukseni-tila?orderref=" . $row->order_url . "&order_check=" . $row->documentno . "\">Kundlänk</td>\n";
					} elseif ($no) {
						echo "\t\t<td align=\"center\" class=\"$backcolor\"><a target=\"_blank\" href=\"https://www.cyberphoto.no/kundvagn/min-ordrestatus?orderref=" . $row->order_url . "&order_check=" . $row->documentno . "\">Kundlänk</td>\n";
					} else {
						echo "\t\t<td align=\"center\" class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/kundvagn/min-orderstatus?orderref=" . $row->order_url . "&order_check=" . $row->documentno . "\">Kundlänk</td>\n";
					}
					echo "\t\t<td class=\"align_left italic\">&nbsp; " . $row_note . "</td>\n";
					echo "\t</tr>\n";

					$countrow++;
					$countrow_by_day++;
					$ordervarde = $ordervarde + $row->totallines;
					$margin = $margin + $row->marginamt;
					$valuta_kod = $row->iso_code;
					
				}

				if ($rowcolor == true) {
					// $row = true;
					$rowcolor = false;
				} else {
					// $row = false;
					$rowcolor = true;
				}
				
				unset($row_note);
				$actual_day = date("Y-m-d", strtotime($row->created));
				// echo "andra: " . $actual_day . "<br>";
				
			}
					
		} else {

				echo "\t<tr>\n";
				echo "\t\t<td colspan=\"5\"><i>Inga träffar. Detta verkar inte rimligt.</i></td>\n";
				echo "\t</tr>\n";
	
		}
		
			if ($group_by_seller == "yes") {
				echo "\t<tr>\n";
				echo "<td colspan=\"2\" class=\"align_left bold\">&nbsp;</td>";
				echo "\t\t<td align=\"right\"><b>" . number_format($ordervarde, 0, ',', ' ') . " " . $valuta_kod . "</b></td>\n";
				echo "\t</tr>\n";
			} else {
				echo "\t<tr>\n";
				echo "<td colspan=\"25\" class=\"align_left bold\">" . $countrow_by_day . " st</td>";
				echo "\t</tr>\n";
				echo "\t<tr>\n";
				echo "<td colspan=\"25\" class=\"align_left bold\">&nbsp;</td>";
				echo "\t</tr>\n";
				echo "\t<tr>\n";
				echo "\t\t<td colspan=\"5\"><b>Totalt: $countrow st ordrar i denna lista</b></td>\n";
				echo "\t\t<td align=\"right\"><b>" . number_format($ordervarde, 0, ',', ' ') . " " . $valuta_kod . "</b></td>\n";
				echo "\t\t<td align=\"right\"><b>" . number_format($margin, 0, ',', ' ') . " " . $valuta_kod . "</b></td>\n";
				echo "\t</tr>\n";
			}

			echo "</table>\n";
			echo "</div>\n";
	}

	function displaySalesPerUser() {
		global $history, $group_by_seller;
	
		$countrow = 0;
		$countrow_by_day = 0;
		$ordervarde = 0;
		$margin = 0;
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		unset($row_note);
	
		echo "<div class=\"floatleft top20\">\n";
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td width=\"50\" class=\"align_center\"><b>Antal</b></td>\n";
		echo "\t\t<td width=\"50\" class=\"align_center\"><b>Säljare</b></td>\n";
		echo "\t\t<td width=\"100\" class=\"align_center\"><b>Ordersumma</b></td>\n";
		echo "\t</tr>\n";
		
		$select  = "SELECT COUNT(o.c_order_id) AS antal, SUM(i.totallines) AS summa, ad.value  ";
		$select .= "FROM m_inout mio ";
		$select .= "JOIN c_order o ON o.c_order_id=mio.c_order_id ";
		$select .= "JOIN ad_user ad ON ad.ad_user_id = o.salesrep_id ";
		$select .= "JOIN c_invoice i ON i.c_invoice_id=mio.c_invoice_id ";
		$select .= "WHERE mio.docstatus IN ('CO') AND mio.deliveryViaRule IN ('S','P') AND mio.isSOTrx = 'Y'  ";
		$select .= "AND mio.isInDispute!='Y' and mio.isActive='Y' AND mio.AD_Client_ID=1000000 AND mio.M_rma_ID is null ";
		
		if ($history == "month") {
			// $select .= "AND o.created > current_date - integer '730' ";
			$select .= "AND mio.updated > '" . date("Y-m-d H:i:s",strtotime("-1 month")) . "' ";
		} elseif ($history == "week") {
			// $select .= "AND o.created > current_date - integer '7' ";
			$select .= "AND mio.updated > '" . date("Y-m-d H:i:s",strtotime("-1 week")) . "' ";
		} else {
			$select .= "AND date(mio.updated)>='" . $dagensdatum . " 00:00:00' AND date(mio.updated)<='" . $dagensdatum . " 23:59:59' ";
		}
		$select .= "GROUP BY ad.value ";
		$select .= "ORDER BY summa DESC, ad.value DESC ";
		// $select .= "LIMIT 500 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
	
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo "här: " . $res;
		}
	
	
		if ($res && pg_num_rows($res) > 0) {
		
			while ($res && $row = pg_fetch_object($res)) {
	
				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}
				
					
				echo "\t<tr>";
				// echo "\t\t<td class=\"$backcolor\" align=\"center\">$countrow</td>\n";
				echo "\t\t<td align=\"center\" class=\"$backcolor\">" . $row->antal . "</td>\n";
				echo "\t\t<td align=\"center\" class=\"$backcolor\">" . strtoupper($row->value) . "</td>\n";
				echo "\t\t<td align=\"right\" class=\"$backcolor\">" . number_format($row->summa, 0, ',', ' ') . " SEK</td>\n";
				echo "\t</tr>\n";
				
				$countrow++;
				$countrow_by_day++;
				$ordervarde = $ordervarde + $row->summa;
				$valuta_kod = $row->iso_code;
				// if (strtoupper($row->value) == "WEBORDER") {
				if (strtoupper($row->value) == "LITIUM") {
					$userweb = $row->summa;
				}


				if ($rowcolor == true) {
					// $row = true;
					$rowcolor = false;
				} else {
					// $row = false;
					$rowcolor = true;
				}
				
				unset($row_note);
				$actual_day = date("Y-m-d", strtotime($row->created));
				// echo "andra: " . $actual_day . "<br>";
				
			}
					
		} else {

				echo "\t<tr>\n";
				echo "\t\t<td colspan=\"3\"><span class=\"italic span_blue\">Ingen försäljning finns registrerad för perioden</span></td>\n";
				echo "\t</tr>\n";
	
		}
		
			echo "\t<tr>\n";
			echo "<td colspan=\"2\" class=\"align_left bold\">&nbsp;</td>";
			echo "\t\t<td align=\"right\"><b>" . number_format($ordervarde, 0, ',', ' ') . " SEK</b></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "<td colspan=\"3\" class=\"align_left bold\">&nbsp;</td>";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "<td colspan=\"2\" class=\"align_left bold\">Webb:&nbsp;</td>";
			echo "\t\t<td align=\"right\"><b>" . number_format(($userweb/$ordervarde*100), 2) . "%</b></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "<td colspan=\"2\" class=\"align_left bold span_blue\">Säljare:&nbsp;</td>";
			echo "\t\t<td align=\"right\"><span class=\"bold span_blue\">" . number_format((($ordervarde-$userweb)/$ordervarde*100), 2) . "%</b></td>\n";
			echo "\t</tr>\n";
			echo "</table>\n";
			echo "</div>\n";
			echo "<div style=\"clear: both;\"></div>";
	}
	
}
?>

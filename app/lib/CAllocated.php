<?php

/*

PHP login object
author		Stefan Sjöberg
version		1.0 2012-04-25

*/
include_once 'Db.php';

// include("connections.php");
// include("connection_ad.php");

Class CAllocated {
	var $conn_my;
	var $conn_ad;

	function __construct() {
		// global $conn_ad;
		
		$this->conn_my = Db::getConnection();
		$this->conn_ad = Db::getConnectionAD();

	}

	function showActualMonitorAllocated() {

		$rowcolor = true;

		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
		echo "<tr>\n";
		echo "<td width=\"110\"><b>Artikel nr</b></td>\n";
		echo "<td width=\"380\"><b>Produkt</b></td>\n";
		echo "<td width=\"55\" align=\"center\"><b>&nbsp;</b></td>\n";
		echo "</tr>\n";

		$select  = "SELECT allID, allArtnr, Tillverkare, beskrivning ";
		$select .= "FROM MonitorAllocated ";
		$select .= "JOIN Artiklar ON Artiklar.artnr = MonitorAllocated.allArtnr ";
		$select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		// $select .= "WHERE allActive = 1 AND allArtnr = '$addArtnr' ";
		$select .= "WHERE allActive = 1 ";
			// echo $select;
			// exit;

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)):
			
				extract($row);

				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}
				if (strlen($beskrivning) > 55) {
					$beskrivning = substr ($beskrivning, 0, 55) . "....";
				}


				echo "<tr>";
				echo "<td class=\"$backcolor\">$allArtnr</td>\n";
				echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=" . $allArtnr . "\">$tillverkare $beskrivning</a></td>\n";
				echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?delete=" . $allID . "\">avsluta</a></td>\n";
				echo "</tr>\n";

				if ($rowcolor == true) {
					$row = true;
					$rowcolor = false;
				} else {
					$row = false;
					$rowcolor = true;
				}
			
				endwhile;
				
			} else {
			
				echo "<tr>\n";
				echo "<td colspan=\"3\"><font color=\"#000000\"><b>Inga enskilda bevakningar finns upplagda i systemet</b></td>\n";
				echo "</tr>\n";
			
			}
				echo "</table>\n";

	}

	function checkAllocatedDuplicate($addArtnr) {

		$select  = "SELECT allArtnr ";
		$select .= "FROM MonitorAllocated ";
		$select .= "WHERE allActive = 1 AND allArtnr = '$addArtnr' ";
			// echo $select;
			// exit;

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				return true;
				
			} else {
			
				return false;
			
			}

	}
	
	function doMonitorAllocatedAdd($addArtnr) {

		$conn_my = Db::getConnection(true);
		
		$allIP = $_SERVER['REMOTE_ADDR'];

		$updt = "INSERT INTO MonitorAllocated (allArtnr,allIP) VALUES ('$addArtnr','$allIP')";

		$res = mysqli_query($conn_my, $updt);

		header("Location: allokerat.php");

	}

	function doMonitorAllocatedDelete($allID) {

		$conn_my = Db::getConnection(true);
		
		$allIP = $_SERVER['REMOTE_ADDR'];
		$aktuelltdatum = date("Y-m-d H:i:s");

		$updt = "UPDATE MonitorAllocated SET allEnd = '$aktuelltdatum', allEndIP = '$allIP', allActive = 0 WHERE allID = '$allID' ";
		
		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

		header("Location: allokerat.php");

	}
	
	function displayAllocatedButReady($type) {
		global $istradein, $nopricelimit;

		$countrow = 0;
		$totsum = 0;
		// echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\">\n";
		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
		echo "\t<tr>\n";
		// echo "\t\t<td width=\"25\">&nbsp;</td>\n";
		echo "\t\t<td align=\"left\" width=\"100\"><b>Order datum</b></td>\n";
		echo "\t\t<td align=\"left\" width=\"110\"><b>Artnr</b></td>\n";
		echo "\t\t<td align=\"left\" width=\"230\"><b>Produkt</b></td>\n";
		echo "\t\t<td align=\"center\" width=\"80\"><b>Order nr</b></td>\n";
		echo "\t\t<td align=\"left\" width=\"250\"><b>Kund</b></td>\n";
		echo "\t\t<td align=\"center\" width=\"55\"><b>Best.</b></td>\n";
		echo "\t\t<td align=\"center\" width=\"55\"><b>Allo.</b></td>\n";
		echo "\t\t<td align=\"center\" width=\"55\"><b>Lev.</b></td>\n";
		echo "\t\t<td align=\"left\" width=\"200\"><b>Låst pga</b></td>\n";
		echo "\t\t<td align=\"center\" width=\"50\"><b>Låst på</b></td>\n";
		echo "\t\t<td align=\"center\" width=\"85\"><b>Kostnad</b></td>\n";
		echo "\t</tr>\n";
			
			$select = "SELECT o.created, p.value, manu.name, p.name, o.documentno, bp.name, col.qtyordered, col.qtyallocated, col.qtydelivered, xc.name, us.value, po.currentcostprice, o.xc_sales_order_status_id ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = p.m_product_id "; // ny
			$select .= "LEFT JOIN xc_sales_order_status xc ON xc.xc_sales_order_status_id = o.xc_sales_order_status_id ";
			$select .= "LEFT JOIN AD_User us ON us.AD_User_ID = o.locked_to_id ";
			// $select .= "WHERE o.c_doctype_id = 1000030 AND NOT o.docstatus IN ('VO') AND col.qtyordered = col.qtyallocated AND col.qtyallocated > col.qtydelivered AND (o.xc_sales_order_status_id = 1000004 OR NOT (o.locked_to_id IS NULL)) ";
			$select .= "WHERE o.c_doctype_id = 1000030 AND NOT o.docstatus IN ('VO') AND col.qtyordered = col.qtyallocated AND col.qtyallocated > col.qtydelivered ";
			
			if ($type == 2) {
				if ($nopricelimit == "yes") {
					$select .= "AND NOT p.m_product_category_ID IN(1000221) ";
				} else {
					$select .= "AND NOT p.m_product_category_ID IN(1000221) AND col.pricelimit > 5000 ";
				}
			} else {
				$select .= "AND p.m_product_category_ID IN(1000221) ";
			}
			if ($istradein == "no") {
				// $select .= "AND NOT o.xc_sales_order_status_id IN (1000015) "; // tar bort ordrar låsta på inbytesaffärer
			}
			// $select .= "AND NOT o.xc_sales_order_status_ID=1000015 "; // tar bort ordrar låsta på inbytesaffärer
			
			$select .= "AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.currentcostprice > 0 "; // ny
			$select .= "AND NOT o.c_order_id IN (1889920,2224736,1080606,1446823,2258062) "; // tar borta interna ordrar såsom mats test, inbyte osv.
			// $select .= "AND NOT o.xc_sales_order_status_id IS NULL "; 
			$select .= "ORDER BY o.created DESC, manu.name ASC, p.name ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				// exit;
			}

			// $res = pg_query($this->conn_ad, $select);
			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
						
						if (!($istradein == "no" && $row[12] == 1000015)) { // tar bort inbytesposter
				
							$beskrivning = $row[2] . " " . $row[3];
							if (strlen($beskrivning) > 30) {
								$beskrivning = substr ($beskrivning, 0, 30) . "....";
							}
							$customer = $row[5];
							if (strlen($customer) > 35) {
								$customer = substr ($customer, 0, 35) . "....";
							}
							$pcost = $row[11] * $row[7];
							$pcostF = number_format($pcost, 0, ',', ' ');

							if ($rowcolor == true) {
								$backcolor = "firstrow";
							} else {
								$backcolor = "secondrow";
							}

							echo "\t<tr>";
							// echo "\t\t<td>$countrow</td>\n";
							// echo "\t\t<td align=\"left\">" . date("Y-m-d H:i",strtotime($row[0])) . "</td>\n";
							echo "\t\t<td class=\"$backcolor\" align=\"left\">" . date("Y-m-d",strtotime($row[0])) . "</td>\n";
							echo "\t\t<td class=\"$backcolor\" class=\"$backcolor\" align=\"left\">$row[1]</td>\n";
							// echo "\t\t<td class=\"$backcolor\" align=\"left\">" . $row[2] . " " . $row[3] . "</td>\n";
							echo "\t\t<td class=\"$backcolor\" align=\"left\">$beskrivning</td>\n";
							// echo "\t\t<td class=\"$backcolor\" align=\"center\"><a href=\"javascript:winPopupCenter(500, 1000, '/order/order_info.php?order=$row[4]');\">$row[4]</a></td>\n";
							echo "\t\t<td class=\"$backcolor\" align=\"center\"><a href=\"javascript:winPopupCenter(500, 1000, '/order_info.php?order=$row[4]');\">$row[4]</a></td>\n";
							echo "\t\t<td class=\"$backcolor\" align=\"left\">$customer</td>\n";
							echo "\t\t<td class=\"$backcolor\" align=\"center\">$row[6]</td>\n";
							echo "\t\t<td class=\"$backcolor\" align=\"center\">$row[7]</td>\n";
							echo "\t\t<td class=\"$backcolor\" align=\"center\">$row[8]</td>\n";
							echo "\t\t<td class=\"$backcolor\" align=\"left\">$row[9]</td>\n";
							echo "\t\t<td class=\"$backcolor\" align=\"center\">". strtoupper($row[10]) . "</td>\n";
							echo "\t\t<td class=\"$backcolor\" align=\"right\">$pcostF SEK</td>\n";
							echo "\t</tr>\n";
							
							if ($rowcolor == true) {
								$row = true;
								$rowcolor = false;
							} else {
								$row = false;
								$rowcolor = true;
							}
							$totsum = $pcost + $totsum;
							$countrow++;
						}

					}
					
				} else {
				
						echo "\t<tr>\n";
						// echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"9\"><i>Inga produkter i listan. Utmärkt!</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		$totsumF = number_format($totsum, 0, ',', ' ');
		echo "\t<tr>\n";
		echo "\t\t<td align=\"left\"><b>Totalt: $countrow st</b></td>\n";
		echo "\t\t<td colspan=\"9\"></td>\n";
		echo "\t\t<td align=\"right\"><b>$totsumF SEK</b></td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
	}

	function fetchAllocatedAndLocked($artnr) {

			$select = "SELECT p.value, o.documentno, bp.name, col.qtyordered, col.qtyallocated, col.qtydelivered, xc.name, us.name ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id ";
			$select .= "LEFT JOIN xc_sales_order_status xc ON xc.xc_sales_order_status_id = o.xc_sales_order_status_id ";
			$select .= "LEFT JOIN AD_User us ON us.AD_User_ID = o.locked_to_id ";
			// $select .= "WHERE o.c_doctype_id = 1000030 AND NOT o.docstatus IN ('VO') AND col.qtyordered = col.qtyallocated AND col.qtyallocated > col.qtydelivered AND (o.xc_sales_order_status_id = 1000004 OR NOT (o.locked_to_id IS NULL)) ";
			$select .= "WHERE o.c_doctype_id = 1000030 AND NOT o.docstatus IN ('VO') AND col.qtyordered = col.qtyallocated AND col.qtyallocated > col.qtydelivered ";
			$select .= "AND p.value = '$artnr' ";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			// $res = pg_query($this->conn_ad, $select);
			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						$artnr = $row[0];
						$ordernr = $row[1];
						$kund = $row[2];
						$last1 = $row[6];
						$last2 = $row[7];
					
						$this->sendMess($artnr,$ordernr,$kund,$last1,$last2);

					}
					
				} else {
				
					return;
				
				}
			
	}

	function getActualMonitorAllocated() {

		$select  = "SELECT allID, allArtnr, Tillverkare, beskrivning ";
		$select .= "FROM MonitorAllocated ";
		$select .= "JOIN Artiklar ON Artiklar.artnr = MonitorAllocated.allArtnr ";
		$select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE allActive = 1 ";

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)) {
				extract($row);
				
					$this->fetchAllocatedAndLocked($allArtnr);
			
				}
				
			} else {
			
				return;
			
			}

	}
	
	function sendMess($artnr,$ordernr,$kund,$last1,$last2) {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		// $recipient .= " salj";
		$recipient .= " sjabo";
		// $recipient .= " rolf";
		// $recipient .= " tobias";
		
		$subj = $orderdatum . " Allokerad produkt är låst!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "Vänligen kontrollera detta omgående.\n\n";
		$text1 .= "Artikel: $artnr\n";
		$text1 .= "Order nr: $ordernr\n";
		$text1 .= "Kund: $kund\n";
		if ($last1 != "") {
			$text1 .= "Låst pga: $last1\n";
		}
		if ($last2 != "") {
			$text1 .= "Låst till: $last2\n";
		}
		$text1 .= "\nMer info här\n";
		$text1 .= "http://www.cyberphoto.se/order/admin/allokerat.php\n\n";
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}
	
}
?>

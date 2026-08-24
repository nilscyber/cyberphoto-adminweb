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

		echo "<table class=\"table-list\">\n";
		echo "<thead><tr>\n";
		echo "<th>Artikel nr</th>\n";
		echo "<th>Produkt</th>\n";
		echo "<th class=\"c\">&nbsp;</th>\n";
		echo "</tr></thead>\n<tbody>\n";

		$select  = "SELECT allID, allArtnr, Tillverkare, beskrivning ";
		$select .= "FROM MonitorAllocated ";
		$select .= "JOIN Artiklar ON Artiklar.artnr = MonitorAllocated.allArtnr ";
		$select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE allActive = 1 ";

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)):

				extract($row);

				if (strlen($beskrivning) > 55) {
					$beskrivning = substr ($beskrivning, 0, 55) . "....";
				}

				echo "<tr>";
				echo "<td>" . htmlspecialchars($allArtnr) . "</td>\n";
				echo "<td><a target=\"_blank\" href=\"/?info.php?article=" . urlencode($allArtnr) . "\">" . htmlspecialchars($tillverkare . " " . $beskrivning) . "</a></td>\n";
				echo "<td class=\"c\"><a href=\"" . $_SERVER['PHP_SELF'] . "?delete=" . $allID . "\">avsluta</a></td>\n";
				echo "</tr>\n";

				endwhile;

			} else {

				echo "<tr>\n";
				echo "<td colspan=\"3\"><i>Inga enskilda bevakningar finns upplagda i systemet</i></td>\n";
				echo "</tr>\n";

			}
				echo "</tbody>\n</table>\n";

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
	
	// Hämtar allokerade men ej levererade orderrader grupperade per order, så att flera produkter
	// på samma order hamnar tillsammans.
	// $mode = "locked" (standard): endast ordrar som faktiskt är låsta (har en låsanledning eller
	//   är låst till en säljare). $showtradein == "yes" visar då ENDAST ordrar låsta på
	//   inbytesaffärer, annars döljs de.
	// $mode = "incomplete": ordrar som INTE är låsta men ändå inte kan skickas eftersom minst en
	//   annan fysisk produkt på samma order fortfarande väntar på att komma in.
	// "complete" = alla FYSISKA produkter (producttype='I', dvs ej tjänster som frakt/försäkring)
	// på ordern är allokerade. Sådana tjänsterader blir aldrig allokerade och ska därför inte
	// räknas med, annars flaggas ordern felaktigt som ofullständig.
	function getOrderGroups($mode, $showtradein) {

		$select  = "SELECT o.c_order_id, o.created, o.documentno, p.value, manu.name, p.name, bp.name, ";
		$select .= "col.qtyordered, col.qtyallocated, col.qtydelivered, xc.name, us.name, po.currentcostprice, p.m_product_id, ";
		$select .= "o.xc_sales_order_status_id, ";
		$select .= "(SELECT COUNT(*) FROM c_orderline col2 ";
		$select .= "JOIN m_product p2 ON col2.m_product_id = p2.m_product_id ";
		$select .= "WHERE col2.c_order_id = o.c_order_id AND p2.producttype = 'I' AND col2.qtyordered <> col2.qtyallocated) AS missing_lines ";
		$select .= "FROM c_orderline col ";
		$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
		$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
		$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
		$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN m_cost po ON po.m_product_id = p.m_product_id ";
		$select .= "LEFT JOIN xc_sales_order_status xc ON xc.xc_sales_order_status_id = o.xc_sales_order_status_id ";
		$select .= "LEFT JOIN AD_User us ON us.AD_User_ID = o.locked_to_id ";
		$select .= "WHERE o.c_doctype_id = 1000030 AND NOT o.docstatus IN ('VO') AND col.qtyordered = col.qtyallocated AND col.qtyallocated > col.qtydelivered ";
		if ($mode == "incomplete") {
			$select .= "AND o.xc_sales_order_status_id IS NULL AND o.locked_to_id IS NULL "; // ej administrativt låst
			$select .= "AND EXISTS (SELECT 1 FROM c_orderline col3 ";
			$select .= "JOIN m_product p3 ON col3.m_product_id = p3.m_product_id ";
			$select .= "WHERE col3.c_order_id = o.c_order_id AND p3.producttype = 'I' AND col3.qtyordered <> col3.qtyallocated) "; // väntar på minst en annan fysisk produkt
		} else {
			$select .= "AND (o.xc_sales_order_status_id IS NOT NULL OR o.locked_to_id IS NOT NULL) "; // endast ordrar som faktiskt är låsta (anledning eller säljare)
			if ($showtradein == "yes") {
				$select .= "AND o.xc_sales_order_status_id = 1000015 "; // visar ENDAST ordrar låsta på inbytesaffärer
			} else {
				// döljer ordrar låsta på inbytesaffärer som standard. Måste hantera NULL explicit,
				// annars faller ordrar utan xc_sales_order_status_id (t.ex. låsta enbart på säljare) bort.
				$select .= "AND (o.xc_sales_order_status_id IS NULL OR o.xc_sales_order_status_id <> 1000015) ";
			}
		}
		$select .= "AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.currentcostprice > 0 ";
		$select .= "AND NOT o.c_order_id IN (1889920,2224736,1080606,1446823,2258062) "; // tar borta interna ordrar såsom mats test, inbyte osv.
		$select .= "ORDER BY o.created DESC, o.documentno ASC, manu.name ASC, p.name ASC ";

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

		$groups = array();

		if ($res && pg_num_rows($res) > 0) {

			while ($res && $row = pg_fetch_row($res)) {

				$orderId = $row[0];

				if (!isset($groups[$orderId])) {
					$groups[$orderId] = array(
						'created'      => $row[1],
						'documentno'   => $row[2],
						'customer'     => $row[6],
						'lockedreason' => $row[10],
						'lockedto'     => $row[11],
						'statusid'     => $row[14],
						'complete'     => ($row[15] == 0),
						'lines'        => array(),
						'sum'          => 0,
					);
				}

				$beskrivning = $row[4] . " " . $row[5];
				$pcost = $row[12] * $row[8];

				$groups[$orderId]['lines'][] = array(
					'artnr'        => $row[3],
					'productid'    => $row[13],
					'beskrivning'  => $beskrivning,
					'qtyordered'   => $row[7],
					'qtyallocated' => $row[8],
					'cost'         => $pcost,
					'missing'      => false,
				);

				$groups[$orderId]['sum'] += $pcost;

			}

		}

		// Lägg till de fysiska produkter på samma ordrar som INTE är allokerade ännu, så man ser
		// vad som faktiskt saknas och orsakar att ordern inte kan skickas.
		if (count($groups) > 0) {
			$missingByOrder = $this->getMissingLinesForOrders(array_keys($groups));
			foreach ($missingByOrder as $orderId => $missingLines) {
				foreach ($missingLines as $ml) {
					$groups[$orderId]['lines'][] = $ml;
				}
			}
		}

		return $groups;

	}

	// Hämtar fysiska produkter (producttype='I') på angivna ordrar som ännu inte är allokerade
	// (qtyordered <> qtyallocated), grupperat per order. Används för att visa vad som saknas på
	// en order utöver de produkter som redan är låsta/allokerade.
	function getMissingLinesForOrders($orderIds) {

		$ids = array_map('intval', $orderIds);
		if (count($ids) == 0) {
			return array();
		}

		$select  = "SELECT col.c_order_id, p.value, manu.name, p.name, p.m_product_id, col.qtyordered, col.qtyallocated ";
		$select .= "FROM c_orderline col ";
		$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
		$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "WHERE col.c_order_id IN (" . implode(",", $ids) . ") ";
		$select .= "AND p.producttype = 'I' AND col.qtyordered <> col.qtyallocated ";
		$select .= "ORDER BY col.c_order_id, manu.name ASC, p.name ASC ";

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

		$missing = array();

		if ($res && pg_num_rows($res) > 0) {

			while ($res && $row = pg_fetch_row($res)) {

				$orderId = $row[0];
				$beskrivning = $row[2] . " " . $row[3];

				$missing[$orderId][] = array(
					'artnr'        => $row[1],
					'productid'    => $row[4],
					'beskrivning'  => $beskrivning,
					'qtyordered'   => $row[5],
					'qtyallocated' => $row[6],
					'cost'         => 0,
					'missing'      => true,
				);

			}

		}

		return $missing;

	}

	function displayOrderGroups($mode, $showtradein) {

		$groups = $this->getOrderGroups($mode, $showtradein);

		$totsum = 0;
		$countrow = 0;
		$completecount = 0;

		echo "<table class=\"table-list\">\n";
		echo "<thead><tr>\n";
		echo "<th>Order nr</th>\n";
		echo "<th>Order datum</th>\n";
		echo "<th>Kund</th>\n";
		echo "<th>Artnr</th>\n";
		echo "<th>Produkt</th>\n";
		echo "<th class=\"c\">Best.</th>\n";
		echo "<th class=\"c\">Allo.</th>\n";
		echo "<th>Låst pga</th>\n";
		echo "<th class=\"c\">Låst på</th>\n";
		echo "<th class=\"r\">Kostnad</th>\n";
		echo "</tr></thead>\n<tbody>\n";

		if (count($groups) > 0) {

			foreach ($groups as $orderId => $group) {

				$sumF = number_format($group['sum'], 0, ',', ' ');
				$orderUrl = "/search_dispatch.php?mode=order&page=1&q=" . urlencode($group['documentno']);

				$orderDate = date("Y-m-d", strtotime($group['created']));
				$ageDays = (int)floor((time() - strtotime($group['created'])) / 86400);
				if ($ageDays <= 3) {
					$ageClass = "green";
				} elseif ($ageDays <= 14) {
					$ageClass = "orange";
				} else {
					$ageClass = "red";
				}

				echo "<tr class=\"order-group-head order-group-box" . ($group['complete'] ? " order-complete-alert" : "") . "\">\n";
				echo "<td colspan=\"10\">";
				echo "<div class=\"order-group-row\">";
				echo "<span class=\"order-group-left\">";
				echo "<a class=\"badge\" href=\"" . $orderUrl . "\" target=\"_blank\" rel=\"noopener\">" . htmlspecialchars($group['documentno']) . "</a>";
				echo "<span class=\"badge\">" . $orderDate . "</span>";
				echo "<span class=\"badge-age " . $ageClass . "\" title=\"" . $ageDays . " dagar sedan orderdatum\">" . $ageDays . "d</span>";
				echo "<span class=\"order-group-customer\">" . htmlspecialchars($group['customer']) . "</span>";
				echo "</span>";
				echo "<span class=\"order-group-right\">";
				echo "<span class=\"badge order-sum-badge\">" . $sumF . " kr</span>";
				if ($group['complete']) {
					echo "<span class=\"badge badge-ready\">KLAR ATT SKICKAS</span>";
					$completecount++;
				} else {
					echo "<span class=\"badge badge-priority\">EJ KOMPLETT</span>";
				}
				echo "</span>";
				echo "</div>";
				echo "</td>\n</tr>\n";

				$linenr = 0;
				$linecount = count($group['lines']);

				foreach ($group['lines'] as $line) {

					$linenr++;
					$beskrivning = $line['beskrivning'];
					if (strlen($beskrivning) > 55) {
						$beskrivning = substr($beskrivning, 0, 55) . "....";
					}
					$productUrl = "/search_dispatch.php?mode=product&q=" . urlencode($line['artnr']) . "&open=product&id=" . (int)$line['productid'];

					$rowclass = "order-group-box" . ($linenr == $linecount ? " order-group-box-end" : "");
					$rowclass .= $line['missing'] ? " order-line-missing" : ($group['complete'] ? " order-complete-alert" : "");

					echo "<tr class=\"" . $rowclass . "\">\n";
					echo "<td></td>\n";
					echo "<td></td>\n";
					echo "<td></td>\n";
					echo "<td>" . htmlspecialchars($line['artnr']) . "</td>\n";
					echo "<td><a href=\"" . $productUrl . "\" target=\"_blank\" rel=\"noopener\">" . htmlspecialchars($beskrivning) . "</a></td>\n";
					echo "<td class=\"c\">" . $line['qtyordered'] . "</td>\n";
					echo "<td class=\"c\">" . $line['qtyallocated'] . "</td>\n";
					if ($line['missing']) {
						echo "<td><i>Ej allokerad &ndash; väntar på leverans</i></td>\n";
						echo "<td class=\"c\">&ndash;</td>\n";
						echo "<td class=\"r\">&ndash;</td>\n";
					} else {
						$costF = number_format($line['cost'], 0, ',', ' ');
						echo "<td>" . htmlspecialchars($group['lockedreason']) . "</td>\n";
						echo "<td class=\"c\">" . strtoupper($group['lockedto']) . "</td>\n";
						echo "<td class=\"r\">" . $costF . " kr</td>\n";
						$totsum += $line['cost'];
						$countrow++;
					}
					echo "</tr>\n";

				}

				echo "<tr class=\"order-group-spacer\"><td colspan=\"10\"></td></tr>\n";

			}

		} else {

			echo "<tr>\n<td colspan=\"10\"><i>Inga produkter i listan. Utmärkt!</i></td>\n</tr>\n";

		}

		$totsumF = number_format($totsum, 0, ',', ' ');
		$totalLabel = "Totalt: " . $countrow . " produkt(er) på " . count($groups) . " order(rar)";
		if ($mode != "incomplete") {
			$totalLabel .= ", varav " . $completecount . " helt allokerade";
		}
		echo "</tbody>\n<tfoot>\n";
		echo "<tr class=\"total-row\">\n";
		echo "<td colspan=\"9\">" . $totalLabel . "</td>\n";
		echo "<td class=\"r\">" . $totsumF . " kr</td>\n";
		echo "</tr>\n";
		echo "</tfoot>\n</table>\n";

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

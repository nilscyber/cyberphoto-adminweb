<?php

/*

PHP login object
author		Stefan Sjöberg
version		1.0 2011-03-15

*/

// include("connections.php");
// include("connection_ad.php");
// include("Db.php");
include_once 'Db.php';

Class CADOrderInfo {

	var $conn_my;
	var $conn_ad;

	function __construct() {
		global $conn_ad;
		
		$this->conn_my = Db::getConnection();
		$this->conn_ad = Db::getConnectionAD();

	}

	function displayOrderInfo($ordernr) {
		global $artnr;

		if ($ordernr == "") {
			return;	// returnera nada

		} else {
		
			$select = "SELECT o.c_order_id, o.created, ad.value, bp.name, bp.name2, loc.address1, loc.address2, loc.postal, loc.city, contrl.name, ";
			$select .= "bp2.name, bp2.name2, loc2.address1, loc2.address2, loc2.postal, loc2.city, contrl2.name, ";
			$select .= "pay.name, sh.name, o.customercomment, o.ip_address, ad2.email, ad2.phone2, o.totallines, o.deliveryviarule, bp.value, ";
			$select .= "xc.name, o.order_url, con2.c_country_id, xc.name2 ";
			$select .= "FROM c_order o ";
			$select .= "JOIN c_paymentterm pay ON pay.c_paymentterm_id = o.c_paymentterm_id ";
			$select .= "LEFT JOIN m_shipper sh ON sh.m_shipper_id = o.m_shipper_id ";
			$select .= "JOIN ad_user ad ON ad.ad_user_id = o.salesrep_id ";
			$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id ";
			$select .= "LEFT JOIN ad_user ad2 ON ad2.c_bpartner_id = o.c_bpartner_id ";
			$select .= "JOIN c_bpartner bp2 ON bp2.c_bpartner_id = o.bill_bpartner_id ";
			$select .= "JOIN c_bpartner_location bpl ON bpl.c_bpartner_location_id = o.c_bpartner_location_id ";
			$select .= "JOIN c_bpartner_location bpl2 ON bpl2.c_bpartner_location_id = o.bill_location_id ";
			$select .= "JOIN c_location loc ON loc.c_location_id = bpl.c_location_id ";
			$select .= "JOIN c_location loc2 ON loc2.c_location_id = bpl2.c_location_id ";
			$select .= "JOIN c_country con ON con.c_country_id = loc.c_country_id ";
			$select .= "JOIN c_country con2 ON con2.c_country_id = loc2.c_country_id ";
			$select .= "JOIN c_country_trl contrl ON contrl.c_country_id = con.c_country_id ";
			$select .= "JOIN c_country_trl contrl2 ON contrl2.c_country_id = con2.c_country_id ";
			$select .= "LEFT JOIN xc_kreditor_pclass xc ON xc.xc_kreditor_pclass_id = o.xc_kreditor_pclass_id ";
			$select .= "WHERE contrl.ad_language = 'sv_SE' AND contrl2.ad_language = 'sv_SE' AND o.documentno = '$ordernr' ";
			// $select .= "AND o.c_doctype_id = 1000030 ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
				echo $select;
				exit;
			}
			
			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					 while ($res && $row = pg_fetch_row($res)) {
						$row = $row;
						
						echo "<div style=\"padding: 5px; float: left; margin-right: 25px;\">Order skapad: <b>$row[1]</b></div>\n";
						echo "<div style=\"padding: 5px; float: left; margin-right: 25px;\">Skapad av: <b>". strtoupper($row[2]) . "</b></div>\n";
						if ($row[20] != "") {
							echo "<div style=\"padding: 5px;\">IP-adress: <b>$row[20]</b></div>\n";
						} else {
							echo "<div style=\"clear: both;\"></div>\n";
						}
						echo "<div style=\"padding: 5px; float: left; margin-right: 25px;\">Betalningsvillkor: <b>" . $row[17] . " </b></div>\n";
						if ($row[26] != "") {
							echo "<div style=\"padding: 5px; float: left; margin-right: 25px;\">Betalplan: <b>"  . ($row[29] != '' ? $row[29] : $row[26]) . "</b></div>\n";
						}
						if ($row[24] != "P") {
							echo "<div style=\"padding: 5px;\">Leveranssätt: <b>$row[18]</b></div>\n";
						} else {
							echo "<div style=\"padding: 5px;\">Leveranssätt: <b>Hämtas lagershop</b></div>\n";
						}
						if ($row[19] != "") {
							echo "<div style=\"padding: 5px;\">Kommentar från kund: <b>$row[19]</b></div>\n";
						}
						echo "<div style=\"padding: 5px;\">Kundnummer affärspartner: <b>$row[25]</b></div>\n";
						echo "<div style=\"margin-top: 10px; margin-right: 10px; padding: 10px; float: left; width: 400px; min-height: 130px; background: #eeeeee; border-bottom: 1px solid #d0d1d5; border-right: 1px solid #d0d1d5;\">Leverans till:<br>\n";
						if ($row[4] != "") {
							echo "<br><b>$row[4]</b>\n";
						} else {
							echo "<br><b>$row[3]</b>\n";
						}
						if ($row[5] != "") {
							echo "<br><b>$row[5]</b>\n";
						}
						if ($row[6] != "") {
							echo "<br><b>$row[6]</b>\n";
						}
						echo "<br><b>$row[7] $row[8]</b>\n";
						echo "<br><b>". strtoupper($row[9]) . "</b>\n";
						if ($row[21] != "") {
							echo "<br><br><b>$row[21]</b>\n";
						}
						if ($row[22] != "") {
							echo "<br><b>$row[22]</b>\n";
						}
						echo "</div>\n";
						echo "<div style=\"margin-top: 10px; margin-right: 10px; padding: 10px; float: left; width: 400px; min-height: 130px; background: #eeeeee; border-bottom: 1px solid #d0d1d5; border-right: 1px solid #d0d1d5;\">Faktura till:<br>\n";
						if ($row[11] != "") {
							echo "<br><b>$row[11]</b>\n";
						} else {
							echo "<br><b>$row[10]</b>\n";
						}
						if ($row[12] != "") {
							echo "<br><b>$row[12]</b>\n";
						}
						if ($row[13] != "") {
							echo "<br><b>$row[13]</b>\n";
						}
						echo "<br><b>$row[14] $row[15]</b>\n";
						echo "<br><b>". strtoupper($row[16]) . "</b>\n";
						echo "</div>\n";
						
						$this->displayProductList($row[0],$row[23],$ordernr);
						
						echo "<div class=\"top20\">\n";
						if ($row[28] == 313) {
							echo "<img border=\"0\" src=\"sv_mini.jpg\"> <a target=\"_blank\" href=\"https://www2.cyberphoto.se/kundvagn/min-ordrestatus?orderref=" . $row[27] . "&order_check=" . $ordernr . "\">Kundlänk</a>\n";
						} elseif ($row[28] == 269) {
							echo "<img border=\"0\" src=\"no_mini.jpg\"> <a target=\"_blank\" href=\"https://www.cyberphoto.no/kundvagn/min-ordrestatus?orderref=" . $row[27] . "&order_check=" . $ordernr . "\">Kundlänk</a>\n";
						} elseif ($row[28] == 167) {
							echo "<img border=\"0\" src=\"dk_mini.jpg\"> <a target=\"_blank\" href=\"https://www.cyberphoto.se/kundvagn/min-ordrestatus?orderref=" . $row[27] . "&order_check=" . $ordernr . "\">Kundlänk</a>\n";
						} else {
							echo "<img border=\"0\" src=\"fi_mini.jpg\"> <a target=\"_blank\" href=\"https://www.cyberphoto.fi/kundvagn/min-ordrestatus?orderref=" . $row[27] . "&order_check=" . $ordernr . "\">Kundlänk</a>\n";
						}
						echo "</div>\n";

						exit; // vi hämtar bara ett resultat
						
					}
					
				} else {
				
					echo "Nu blev något väldigt fel!\n";
				
				}
		}
	}
	
	function displayProductList($c_order_id,$totallines,$ordernr) {
		global $artnr, $salda, $order, $show_salesbundle;

		$countrow = 1;
		echo "<div style=\"clear: both;\"></div>\n";
		echo "<div style=\"margin-top: 25px; clear: both;\">\n";
		echo "\n<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td width=\"20\">&nbsp;</td>\n";
		echo "\t\t<td><b>Artikel nr</b></td>\n";
		echo "\t\t<td><b>Benämning</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Beställt</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Reserverat</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Allokerat</b></td>\n";
		echo "\t\t<td>&nbsp;</td>\n";
		echo "\t\t<td align=\"center\"><b>Levererat</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Fakturerat</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Notering</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Paket</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Rabatt</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Netto</b></td>\n";
		echo "\t\t<td>&nbsp;</td>\n";
		echo "\t</tr>\n";
			
			$select = "SELECT p.value, p.name, col.qtyordered, col.qtyreserved, col.qtyallocated, col.qtydelivered, col.description, col.packey, col.linenetamt, col.discount, col.line, col.qtyinvoiced ";
			$select .= "FROM c_orderline col ";
			$select .= "LEFT JOIN m_product p ON col.m_product_id = p.m_product_id ";
			// $select .= "WHERE col.qtyordered > col.qtydelivered AND col.c_order_id = $c_order_id ";
			$select .= "WHERE col.c_order_id = $c_order_id ";
			$select .= "ORDER BY col.line ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
				
						echo "\t<tr onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='normal'\">\n";
						// echo "\t\t<td align=\"left\">$countrow</td>\n";
						echo "\t\t<td align=\"left\">$row[10]</td>\n";
						echo "\t\t<td>$row[0]</td>\n";
						echo "\t\t<td>$row[1]</td>\n";
						echo "\t\t<td align=\"center\">$row[2]</td>\n";
						echo "\t\t<td align=\"center\">$row[3]</td>\n";
						echo "\t\t<td align=\"center\">$row[4]</td>\n";
						if ($row[4] < $row[2] && ($row[5] < $row[2])
							&& ($row[0] != "rab" && $row[0] != "invoicefee" && $row[0] != "friforsakring" && $row[0] != "" && substr($row[0], 0, 5) != "frakt")
							) {
							echo "\t\t<td><a target=\"_blank\" style=\"text-decoration: none;\" href=\"https://admin.cyberphoto.se/monitor_articles.php?add=yes&addArtnr=$row[0]&addType=3&addStoreValue=$order\">Bevaka</a></td>\n";
						} else {
							echo "\t\t<td>&nbsp;</td>\n";
						}
						echo "\t\t<td align=\"center\">$row[5]</td>\n";
						echo "\t\t<td align=\"center\">$row[11]</td>\n";
						echo "\t\t<td align=\"center\">$row[6]</td>\n";
						echo "\t\t<td align=\"center\">$row[7]</td>\n";
						echo "\t\t<td align=\"center\">" . round($row[9], 1) . "%</td>\n";
						echo "\t\t<td align=\"right\">" . round($row[8], 1) . "</td>\n";
						if (($row[5] < $row[2])
							&& ($row[0] != "rab" && $row[0] != "invoicefee" && $row[0] != "friforsakring" && $row[0] != "" && substr($row[0], 0, 5) != "frakt")
							) {
							echo "\t\t<td><a href=\"#\" onclick=\"window.open('https://admin.cyberphoto.se/product_feedback.php?popup=1&artnr=$row[0]&ordernr=$ordernr', 'feedbackPopup', 'width=750,height=600'); return false;\">Rapportera</a></td>\n";
						} else {
							echo "\t\t<td>&nbsp;</td>\n";
						}
						echo "\t</tr>\n";
						
						$countrow++;

					}
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Inga rader finns</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "</table>\n";
		echo "</div>\n";
		echo "<div style=\"text-align: right; margin-top: 10px; margin-right: 3px;\">\n";
		echo "<b>Totalt ex moms: ". round($totallines, 2) . "</b>\n";
		echo "</div>\n";
		$this->displayInternalChat($c_order_id);
		if ($artnr != "") {
			if ($show_salesbundle == "yes") {
				echo "<div style=\"padding: 5px; margin-top: 15px;\"><a style=\"text-decoration: none;\" href=\"antalsalda.php?artnr=$artnr&show_salesbundle=yes\">Tillbaka till listan</a></div>\n";
			} elseif ($salda == "yes") {
				echo "<div style=\"padding: 5px; margin-top: 15px;\"><a style=\"text-decoration: none;\" href=\"antalsalda.php?artnr=$artnr\">Tillbaka till listan</a></div>\n";
			} else {
				echo "<div style=\"padding: 5px; margin-top: 15px;\"><a style=\"text-decoration: none;\" href=\"waitinglist.php?artnr=$artnr\">Tillbaka till listan</a></div>\n";
			}
		}
	}

	function displayInternalChat($ordernr) {

		$countrow = 1;

			$select = "SELECT ch.created, ad.name, ch.characterdata ";
			$select .= "FROM cm_chat c ";
			$select .= "JOIN cm_chatentry ch ON ch.cm_chat_id = c.cm_chat_id ";
			$select .= "JOIN ad_user ad ON ad.ad_user_id = ch.updatedby ";
			$select .= "WHERE ad_table_id = 259 AND record_id = $ordernr ";
			$select .= "ORDER BY ch.created ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $res = pg_query($this->conn_ad, $select);
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						$beskrivning = eregi_replace("\n", "<br>", $row[2]);
						
						if ($countrow == 1) {
							echo "<div class=\"chat\">Interna kommentarer om denna order</div>\n";
						}
						echo "<div style=\"padding: 5px; margin-top: 10px; background: #FFEFDF; border-bottom: 1px solid #d0d1d5; border-right: 1px solid #d0d1d5;\">\n";
						echo "<div style=\"float: left; margin-right: 25px;\">$row[0]</div>\n";
						echo "<div><i>$row[1]</i></div>\n";
						// echo "<div style=\"margin-top: 5px;\">$row[2]</div>\n";
						echo "<div style=\"margin-top: 5px;\">" . $beskrivning . "</div>\n";
						echo "</div>\n";
						$countrow++;

					}
					
				} else {
					
					return;
					
				}
			
	}

	function displayInternalChatProduct($m_product_id) {

		$countrow = 1;

			$select = "SELECT ch.created, ad.name, ch.characterdata ";
			$select .= "FROM cm_chat c ";
			$select .= "JOIN cm_chatentry ch ON ch.cm_chat_id = c.cm_chat_id ";
			$select .= "JOIN ad_user ad ON ad.ad_user_id = ch.updatedby ";
			$select .= "WHERE ad_table_id = 208 AND record_id = $m_product_id ";
			$select .= "ORDER BY ch.created ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $res = pg_query($this->conn_ad, $select);
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
				echo "<div class=\"internalcomment_container\">\n";
				echo "<div class=\"bold italic bottom5\">Info angående produkten</div>\n";
				
					while ($res && $row = pg_fetch_row($res)) {
					
						$beskrivning = eregi_replace("\n", "<br>", $row[2]);
						
						echo "<div class=\"bottom5\">\n";
						echo "<div style=\"float: left; margin-right: 5px;\">" . date("Y-m-d", strtotime($row[0])) . "</div>\n";
						echo "<div><i>$row[1]</i></div>\n";
						echo "<div style=\"margin-top: 5px;\">" . $beskrivning . "</div>\n";
						echo "</div>\n";

						/*
						echo "<div style=\"padding: 5px; margin-top: 10px; background: #FFEFDF; border-bottom: 1px solid #d0d1d5; border-right: 1px solid #d0d1d5;\">\n";
						echo "<div style=\"float: left; margin-right: 25px;\">$row[0]</div>\n";
						echo "<div><i>$row[1]</i></div>\n";
						echo "<div style=\"margin-top: 5px;\">$row[2]</div>\n";
						echo "</div>\n";
						*/
						$countrow++;

					}
				
				echo "</div>\n";

				} else {
					
					return;
					
				}
			
	}
	
	function displayInternalChatNew($ordernr) {

		$countrow = 1;

			$select = "SELECT ch.created, ad.name, ch.characterdata ";
			$select .= "FROM cm_chat c ";
			$select .= "JOIN cm_chatentry ch ON ch.cm_chat_id = c.cm_chat_id ";
			$select .= "JOIN ad_user ad ON ad.ad_user_id = ch.updatedby ";
			$select .= "JOIN c_order o ON o.c_order_id = c.record_id ";
			$select .= "WHERE ad_table_id = 259 ";
			$select .= "AND o.documentno = '$ordernr' ";
			$select .= "ORDER BY ch.created ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $res = pg_query($this->conn_ad, $select);
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						if ($countrow == 1) {
							echo "<hr noshade color=\"#0000FF\" align=\"left\" size=\"1\">\n";
							echo "<div style=\"background-image: url(/order/chat.png); background-repeat: no-repeat; background-position: top left; padding-left: 20px; font-weight: bold; margin-top: 20px;\">Interna kommentarer om denna order</div>\n";
						}
						echo "<div style=\"padding: 5px; margin-top: 10px; background: #FFEFDF; border-bottom: 1px solid #d0d1d5; border-right: 1px solid #d0d1d5;\">\n";
						echo "<div style=\"float: left; margin-right: 25px;\">$row[0]</div>\n";
						echo "<div><i>$row[1]</i></div>\n";
						echo "<div style=\"margin-top: 5px;\">$row[2]</div>\n";
						echo "</div>\n";
						$countrow++;

					}
					
				} else {
					
					return;
					
				}
			
	}
	
}
?>

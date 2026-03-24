<?php

/*

PHP login object
author		Stefan Sjöberg
version		1.0 2011-03-15

*/
include_once 'Db.php';
include("connections.php");
// include("connection_ad.php");

Class CWebADIntern {

	var $conn_my;
	var $conn_ad;

	function __construct() {
		global $conn_ad;
		
		$this->conn_my = Db::getConnection();
		$this->conn_ad = Db::getConnectionAD();

	}

	function checkOnQueue($artnr) {
		
		$select = "SELECT m_storage.qtyreserved AS queue ";
		$select .= "FROM m_product ";
		$select .= "JOIN m_storage ON m_storage.m_product_id = m_product.m_product_id ";
		$select .= "WHERE m_product.value = '" . $artnr . "' ";
		// echo $select;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($row->queue > 0) {
				
				return true;
				
			} else {
			
				return false;
			
			}
		
	}

	function displayOnQueue($artnr) {
		
		$select = "SELECT m_storage.qtyreserved AS queue ";
		$select .= "FROM m_product ";
		$select .= "JOIN m_storage ON m_storage.m_product_id = m_product.m_product_id ";
		$select .= "WHERE m_product.value = '" . $artnr . "' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $select;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				echo $row->queue . "&nbsp;st";
				
			} else {
			
				echo "&nbsp;";
			
			}
		
	}

	function displayOnQueueNew($artnr) {
		
		$select = "SELECT qtyavailable AS queue ";
		$select .= "FROM m_product_stock_summary_v ";
		$select .= "WHERE m_product_stock_summary_v.value = '" . $artnr . "' AND m_warehouse_id = 1000000 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($row->queue < 0) {
				// $showqueue = 0 - $row->queue;
				echo "<a href=\"javascript:winPopupCenter(600, 1000, 'https://admin.cyberphoto.se/waitinglist.php?artnr=$artnr');\" style=\"text-decoration: none;  color: #009933;\">" . abs($row->queue) . "&nbsp;st</a>";
			} else {
				echo "--";
			}
		
	}

	function displayOnQueuePricelist($artnr) {
		
		$select = "SELECT qtyavailable AS queue ";
		$select .= "FROM m_product_stock_summary_v ";
		$select .= "WHERE m_product_stock_summary_v.value = '" . $artnr . "' AND m_warehouse_id = 1000000 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($row->queue < 0) {
				// $showqueue = 0 - $row->queue;
				// echo "<a href=\"javascript:winPopupCenter(600, 1000, '/order/waitinglist.php?artnr=$artnr');\" style=\"text-decoration: none;  color: #009933;\">" . abs($row->queue) . "&nbsp;st</a>";
				// return "<a class=\"mark_blue\" href=\"javascript:winPopupCenter(600, 1000, '/order/waitinglist.php?artnr=$artnr');\">K: " . abs($row->queue) . "</a>";
				return abs($row->queue);
			} else {
				return "&nbsp;";
			}
		
	}
	
	function displayProductInventory($artnr) {
		
		$select = "SELECT qtyavailable AS available, qtyonhand AS onhand, qtyallocated_storage AS allocated ";
		$select .= "FROM m_product_stock_summary_v ";
		$select .= "WHERE m_product_stock_summary_v.value = '" . $artnr . "' AND m_warehouse_id = 1000000 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($row->available > 0) {
				echo round($row->available,0) . "&nbsp;st</b></font>";
			} else {
				echo "0 st</b></font>";
			}
			if ($row->available > 0 && $row->allocated > 0) {
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
					echo "&nbsp;<a href=\"javascript:winPopupCenter(600, 1000, 'https://admin.cyberphoto.se/waitinglist.php?artnr=$artnr');\">(" . round($row->allocated,0) . "&nbsp;st)</a>";
				} else {
					echo "&nbsp;<a href=\"javascript:winPopupCenter(600, 1000, 'https://admin.cyberphoto.se/waitinglist.php?artnr=$artnr');\">(" . round($row->allocated,0) . "&nbsp;st)</a>";
					// echo "&nbsp;(" . $row->allocated . "&nbsp;st)";
				}
			} else {
				echo "&nbsp;";
			}
			if ($row->available < 1) {
				echo "&nbsp;<a href=\"javascript:winPopupCenter(600, 1000, 'https://admin.cyberphoto.se/waitinglist.php?artnr=$artnr');\">(" . round($row->onhand,0) . "&nbsp;st)</a>";
				// echo "&nbsp;(" . $row->onhand . "&nbsp;st)";
			} else {
				echo "&nbsp;";
			}
		
	}

	function displayProductInventoryOnlyAllocated($artnr) {
		
		$select = "SELECT qtyavailable AS available, qtyonhand AS onhand, qtyallocated_storage AS allocated ";
		$select .= "FROM m_product_stock_summary_v ";
		$select .= "WHERE m_product_stock_summary_v.value = '" . $artnr . "' AND m_warehouse_id = 1000000 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

		if ($row->available == 0 && $row->allocated > 0) {
			return "<img title=\"Är fortfarande allokerad på kund\" border=\"0\" src=\"status_yellow.jpg\">";
		} else {
			return "&nbsp;";
		}
		
	}

	function displaySoldLastMonth($artnr) {
		
		$select = "SELECT SUM(pstat.qtymonth) AS numbersold ";
		$select .= "FROM xc_product_statistics pstat ";
		$select .= "JOIN m_product mp ON mp.m_product_id = pstat.m_product_id ";
		$select .= "WHERE mp.value = '" . $artnr . "' ";
		// echo $select;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				if ($row->numbersold > 0) {
					echo $row->numbersold . "&nbsp;st/mån";
				} else {
					echo $row->numbersold . "0&nbsp;st/mån";
				}
				
			} else {
			
				echo "";
			
			}
		
	}

	function displayStoreLocation($artnr) {
		
		// $select = "SELECT x, y, z ";
		$select = "SELECT mloc.value AS hyllplats ";
		$select .= "FROM m_locator mloc ";
		$select .= "JOIN m_product mp ON mp.m_locator_id = mloc.m_locator_id ";
		$select .= "WHERE mp.value = '" . $artnr . "' ";
		// echo $select;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				if ($row->hyllplats != "") {
					echo $row->hyllplats;
				} else {
					echo "";
				}
				
			} else {
			
				echo "saknas";
			
			}
		
	}

	function returnStoreLocation($artnr) {
		
		// $select = "SELECT x, y, z ";
		$select = "SELECT mloc.value AS hyllplats ";
		$select .= "FROM m_locator mloc ";
		$select .= "JOIN m_product mp ON mp.m_locator_id = mloc.m_locator_id ";
		$select .= "WHERE mp.value = '" . $artnr . "' ";
		// echo $select;

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				if ($row->hyllplats != "") {
					return $row->hyllplats;
				} else {
					return "";
				}
				
			} else {
			
				return "saknas";
			
			}
		
	}

	function displayQtyOrdered($AD_m_product_id) {

		if ($AD_m_product_id == "") {
			return "";	// returnera nada

		} else {
		
			$select = "SELECT adempiere.bomqtyordered($AD_m_product_id, 1000000, 0) AS qtyOrdered ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			$row = $res ? pg_fetch_object($res) : null;

				if ($res && pg_num_rows($res) > 0) {
					
					if ($row->qtyordered > 0) {
						echo "Beställda: <b><font color=\"#996600\">" . round($row->qtyordered,0) . " st";
					} else {
						echo "";
					}
					
				} else {
				
					return "";
				
				}
		}
	}

	function printMissingProductsFromAD() {

		$rowcolor = true;
		$countrows = 0;
		
		echo "<div>";
		// echo "<table width=\"1200\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">";
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">";
		echo "<tr>";
		echo "<td width=\"130\" class=\"rubrik\">Artnr</td>";
		echo "<td width=\"550\" class=\"rubrik\">Benämning</td>";
		echo "<td width=\"250\" class=\"rubrik\">Kategori</td>";
		// echo "<td class=\"rubrik\">Benämning</td>";
		echo "<td width=\"40\" class=\"rubrik\">Kö</td>";
		echo "<td width=\"200\" class=\"rubrik\">Leverantör</td>";
		echo "</tr>";
		
		// $select = "SELECT prod.value, cat.name AS cat_name, manu.name AS manu_name, prod.name AS prod_name, pstock.qtyavailable ";
		$select = "SELECT prod.value, cat.name AS cat_name, manu.name AS manu_name, prod.name AS prod_name, pstock.qtyavailable, cbp.name AS lev_name ";
		// $select .= "FROM m_product_stock_summary_v pstock ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "LEFT JOIN m_product prod ON pstock.m_product_id = prod.m_product_id ";
		$select .= "LEFT JOIN xc_manufacturer manu ON prod.xc_manufacturer_id = manu.xc_manufacturer_id ";
		$select .= "LEFT JOIN m_product_category cat ON prod.m_product_category_id = cat.m_product_category_id ";
		$select .= "LEFT JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
		$select .= "LEFT JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
		// $select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyavailable < -1 ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyavailable < -1 "; // detta ändrades 111031 på PO:s begäran
		$select .= "AND NOT cat.value IN('314','12','700','486','1000517') ";
		$select .= "AND prod_po.iscurrentvendor = 'Y' ";
		$select .= "ORDER BY pstock.qtyavailable ASC, manu.name ASC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// echo $this->conn_ad;
		// $row = pg_fetch_array($res);
		// echo pg_num_rows($res);

			while ($res && $row = pg_fetch_object($res)) {

				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}


					echo "<tr>";
					echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=" . $row->value . "\">" . $row->value . "</a></td>";
					echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=" . $row->value . "\">" . $row->manu_name . " ". $row->prod_name . "</a></td>";
					echo "<td class=\"$backcolor\">" . $row->cat_name . "</td>";
					// echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"/info.php?article=" . $row->value . "\">" . $row->manu_name . " " . $row->prod_name . "</a></td>";
					echo "<td class=\"$backcolor\">" . $row->qtyavailable . "</td>";
					echo "<td class=\"$backcolor\">" . $row->lev_name . "</td>";
					echo "</tr>";
					$countrows++;
				
				if ($rowcolor == true) {
					$row = true;
					$rowcolor = false;
				} else {
					$row = false;
					$rowcolor = true;
				}
			
			}

		echo "</table>";
		echo "</div>";
		echo "<div class=\"top5\"></div>\n";
		echo "<div><b>Totalt: " . $countrows . " st</b></div>\n";

	}

	function printProductsADOffice($butiken = false) {

		$rowcolor = true;
		$countrows = 0;
		$totsum = 0;
		// echo "<div><h1>Skickade plocksedlar - Lagershop</h1></div>";
		echo "<div>";
		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\">";
		echo "<tr>";
		echo "<td width=\"150\" class=\"rubrik\">Artikel nr</td>";
		echo "<td width=\"200\" class=\"rubrik\">Kategori</td>";
		echo "<td width=\"400\" class=\"rubrik\">Benämning</td>";
		echo "<td width=\"50\" class=\"rubrik\">I lager</td>";
		echo "<td width=\"100\" class=\"rubrik\">Plats</td>";
		echo "</tr>";
		
		$select = "SELECT DISTINCT pstock.value, cat.name, manu.name, prod.name, pstock.qtyavailable, mloc.value ";
		$select .= "FROM m_product_stock_summary_v pstock  ";
		$select .= "LEFT JOIN m_product prod ON pstock.m_product_id=prod.m_product_id ";
		$select .= "LEFT JOIN m_storage store ON store.m_product_id = prod.m_product_id ";
		$select .= "LEFT JOIN m_locator mloc ON  store.m_locator_id = mloc.m_locator_id ";
		$select .= "LEFT JOIN xc_manufacturer manu ON prod.xc_manufacturer_id=manu.xc_manufacturer_id ";
		$select .= "LEFT JOIN m_product_category cat ON prod.m_product_category_id=cat.m_product_category_id ";
		if ($butiken) {
			$select .= "WHERE mloc.m_warehouse_id = 1000006 AND pstock.m_warehouse_id = 1000006 AND pstock.qtyavailable > 0 AND store.qtyonhand > 0 ";
		} else {
			$select .= "WHERE mloc.m_warehouse_id = 1000002 AND pstock.m_warehouse_id = 1000002 AND pstock.qtyavailable > 0 AND store.qtyonhand > 0 ";
		}
		$select .= "ORDER BY mloc.value ASC ";
		// $select .= " ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_array($res);

			while ($res && $row = pg_fetch_row($res)) {

			if ($rowcolor == true) {
				$backcolor = "firstrow";
			} else {
				$backcolor = "secondrow";
			}


				echo "<tr>";
				echo "<td class=\"$backcolor\">$row[0]</td>";
				echo "<td class=\"$backcolor\">".$row[1] . "</td>";
				echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row[0]\">" . $row[2] . " " . $row[3] . "</a></td>";
				echo "<td align=\"center\" class=\"$backcolor\">$row[4]</td>";
				echo "<td class=\"$backcolor\">$row[5]</td>";
				echo "</tr>";
				$countrows++;
				$totsum = $totsum + (int)$row[3];
			
			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
			
			}

		echo "</table>";
		echo "</div>";
		echo "<div class=\"top5\"></div>\n";
		echo "<div><b>Totalt: " . $countrows . " st</b></div>\n";
		// echo "<div><h3>Total summa: " . round($totsum,0) . "<h3></div>";

	}

	function printNotShownWebProductsFromAD() {

		$rowcolor = true;
		$countarticles = 0;
		
		// echo "<div><h1>Skickade plocksedlar - Lagershop</h1></div>";
		echo "<div>";
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">";
		echo "<tr>";
		echo "<td width=\"150\" class=\"rubrik\">Artikel nr</td>";
		echo "<td width=\"200\" class=\"rubrik\">Kategori</td>";
		echo "<td width=\"300\" class=\"rubrik\">Benämning</td>";
		echo "<td width=\"100\" class=\"rubrik\">Lagersaldo</td>";
		echo "</tr>";
		
		$select = "SELECT pstock.value, cat.name, manu.name, prod.name, pstock.qtyonhand  ";
		$select .= "FROM m_product_stock_summary_v pstock  ";
		$select .= "LEFT JOIN m_product prod ON pstock.m_product_id=prod.m_product_id ";
		$select .= "LEFT JOIN xc_manufacturer manu ON prod.xc_manufacturer_id=manu.xc_manufacturer_id ";
		$select .= "LEFT JOIN m_product_category cat ON prod.m_product_category_id=cat.m_product_category_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND prod.isselfservice = 'N' ";
		$select .= "ORDER BY pstock.qtyonhand DESC ";
		// $select .= " ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_array($res);

			while ($res && $row = pg_fetch_row($res)) {

				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}


					echo "<tr>";
					echo "<td class=\"$backcolor\">$row[0]</td>";
					echo "<td class=\"$backcolor\">$row[1]</td>";
					echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=$row[0]\">$row[2] $row[3]</a></td>";
					echo "<td class=\"$backcolor\">$row[4]</td>";
					echo "</tr>";
					$countrows++;
					$totsum = $totsum + $row[3];
				
				if ($rowcolor == true) {
					$row = true;
					$rowcolor = false;
				} else {
					$row = false;
					$rowcolor = true;
				}
				$countarticles++;
			
			}

		echo "</table>";
		echo "<p><b>Totalt: " . $countarticles . " st</b></p>";
		echo "</div>";
		// echo "<div><h3>Totalt: " . $countrows . "<h3></div>";
		// echo "<div><h3>Total summa: " . round($totsum,0) . "<h3></div>";

	}

	function printNotShownWebProductsNew() {
		global $allproducts;

		$countrow = 0;
		$current_catcount = 0;
		$rowcolor = true;
		
		$select  = "SELECT a.artnr, t.tillverkare, a.beskrivning, a.utpris, a.lagersaldo, k.kategori ";
		$select .= "FROM Artiklar a ";
		$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
		$select .= "WHERE a.ej_med = -1 AND a.lagersaldo > 0 ";
		if ($allproducts != "yes") {
			$select .= "AND a.demo = -1 ";
		}
		$select .= "AND NOT a.isTradeIn = -1 "; // ta bort alla inbytesprodukter
		$select .= "AND NOT a.artnr IN('filter','invoicefee','delbet','reklamNbL','kickback3','kickback0','taxfree','undersökningsavg','bonus','mellanskillnad','split') "; // ta bort alla solklara produkter
		$select .= "AND NOT a.artnr IN('126397','128943','128023','128010','126613','128030') "; // ta bort abonnemang
		$select .= "AND NOT a.artnr LIKE 'frakt%' AND NOT a.artnr LIKE 'brev%' AND NOT a.artnr LIKE 'paket%' "; // ta bort alla frakter
		$select .= "AND NOT a.artnr LIKE 'frakt%' AND NOT a.artnr LIKE 'reklam%' AND NOT a.artnr LIKE 'marknadsbidrag%' "; // ta bort lite till 
		// $select .= "AND a.date_add < NOW() - INTERVAL 2 WEEK ";
		$select .= "ORDER BY k.kategori ASC, t.tillverkare ASC, a.beskrivning ASC ";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

		$res = mysqli_query($this->conn_my, $select);

			echo "<table width=\"1200\">";
			echo "<tr>";
			echo "<td width=\"150\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"900\" align=\"left\"><b>Artikel</b></td>";
			echo "<td width=\"90\" align=\"center\"><b>Utpris</b></td>";
			echo "<td width=\"90\" align=\"center\"><b>Lagersaldo</b></td>";
			echo "</tr>";

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
						if ($kategori != $current_kategori) {
							if ($current_catcount != 0) {
								echo "<tr>\n";
								echo "<td colspan=\"3\" align=\"left\">&nbsp;</td>\n";
								echo "</tr>\n";
								$current_catcount = 0;
								$rowcolor = true;
							}
							echo "<tr>\n";
							echo "<td colspan=\"3\" align=\"left\"><b>$kategori</b></td>\n";
							echo "</tr>\n";
						}
						$current_kategori = $kategori;
						
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						$beskrivning = $tillverkare . " " . $beskrivning;
						
						echo "<tr>";
						echo "<td class=\"$backcolor\" align=\"left\">$artnr</td>";
						echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$artnr\">$beskrivning</a></td>";
						echo "<td class=\"$backcolor\" align=\"right\">" . round($utpris,0) . " SEK</td>";
						echo "<td class=\"$backcolor\" align=\"right\">$lagersaldo st</td>";
						echo "</tr>";
					
						$countrow++;
						$current_catcount++;
						if ($rowcolor == true) {
							$rowcolor = false;
						} else {
							$rowcolor = true;
						}
					
				
				endwhile;
				
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"4\"><font color=\"#00D900\"><b><i>Detta är lysande, listan är tom!</i></b></font></td>";
				echo "</tr>";
			
			}
		if ($countrow == 0) {
		}
		echo "</table>";
		echo "<p>Antal artiklar: <b>$countrow st</b></p>";

	}

	function checkProductUpdate($artnr,$show_history = false) {
		global $fi, $no;
	
		$select  = "SELECT pl.name, pl.m_pricelist_id, u.value, pu.updatetime, pu.pricestd, pu.isupdtselfservice, pu.isselfservice, pu.isupdtpricestd, pu.isupdtdiscontinued, pu.discontinued, ";
		$select .= "pu.isupdtname, pu.isupdtdescription, pu.m_product_update_id, p.value AS artnr, pu.m_product_id ";
		$select .= "FROM m_product_update pu ";
		$select .= "JOIN m_product p ON p.m_product_id = pu.m_product_id ";
		$select .= "JOIN m_pricelist pl ON pl.m_pricelist_id = pu.m_pricelist_id ";
		$select .= "JOIN ad_user u ON u.ad_user_id = pu.salesrep_id ";
		if ($show_history) {
			$select .= "WHERE pu.isupdated = 'Y' AND p.value = '" . $artnr . "' ";
			$select .= "ORDER BY pu.updatetime DESC, pl.m_pricelist_id  ";
		} else {
			$select .= "WHERE pu.isactive = 'Y' AND pu.isupdated = 'N' AND p.value = '" . $artnr . "' ";
			$select .= "ORDER BY pu.updatetime ASC, pl.m_pricelist_id  ";
		}
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
			
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_object($res);
		// echo pg_num_rows($res);
	
		if ($res && pg_num_rows($res) > 0) {

			echo "<div class=\"top5 bottom10 floatleft\">\n";
			echo "<div class=\"bold italic\">Automatiska prisjusteringar</div>\n";
			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\" width=\"310\">\n";
				
			// while ($row2 = pg_fetch_row($res)) {
			while ($res && $row = pg_fetch_object($res)) {
				
				if ($row->m_pricelist_id == 1000018) {
					$utpris_moms = $row->pricestd * 1.24;
					$valuta = "EUR";
				} elseif ($row->m_pricelist_id == 1000280) {
					$utpris_moms = $row->pricestd * 1.25;
					$valuta = "NOK";
				} else {
					$utpris_moms = $row->pricestd * 1.25;
					$valuta = "SEK";
				}
	
				echo "\t<tr>\n";
				
				echo "\t\t<td>";
				// echo "<a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?edit=yes&artnr=$row->artnr&m_product_id=$row->m_product_id&ID=$row->m_product_update_id');\">";
				echo "<a href=\"javascript:winPopupCenter(900, 800, 'https://admin.cyberphoto.se/product_update.php?edit=yes&artnr=$row->artnr&m_product_id=$row->m_product_id&ID=$row->m_product_update_id');\">";
				
				if ($row->m_pricelist_id == 1000018) {
					// echo "<img border=\"0\" src=\"/order/admin/fi_mini.jpg\">";
					echo "<img border=\"0\" src=\"fi_mini.jpg\">";
				} elseif ($row->m_pricelist_id == 1000280) {
					// echo "<img border=\"0\" src=\"/order/admin/no_mini.jpg\">";
					echo "<img border=\"0\" src=\"no_mini.jpg\">";
				} else {
					// echo "<img border=\"0\" src=\"/order/admin/sv_mini.jpg\">";
					echo "<img border=\"0\" src=\"https://admin.cyberphoto.se/sv_mini.jpg\">";
				}
				echo "</a></td>\n";
				// echo "\t\t<td>" . $row->m_pricelist_id . "</td>\n";
				echo "\t\t<td class=\"align_center\">" . date("Y-m-d H:i", strtotime($row->updatetime)) . "</td>\n";
				echo "\t\t<td class=\"align_center\">" . strtoupper($row->value) . "</td>\n";
				if ($row->isupdtselfservice == "Y") {
					if ($row->isselfservice == "Y") {
						echo "\t\t<td class=\"align_right\">Visa=Ja</td>\n";
					} else {
						echo "\t\t<td class=\"align_right\">Visa=Nej</td>\n";
					}
				}
				if ($row->isupdtdiscontinued == "Y") {
					if ($row->discontinued == "Y") {
						echo "\t\t<td class=\"align_right\">Utg=Ja</td>\n";
					} else {
						echo "\t\t<td class=\"align_right\">Utg=Nej</td>\n";
					}
				}
				if ($row->isupdtname == "Y") {
					echo "\t\t<td class=\"align_right\">Ny beskrivning</td>\n";
				}
				if ($row->isupdtdescription == "Y") {
					echo "\t\t<td class=\"align_right\">Ny kommentar</td>\n";
				}
				if ($row->isupdtpricestd == "Y") {
					echo "\t\t<td class=\"align_right\">" . number_format($utpris_moms, 0, ',', ' ') . " " . $valuta . "</td>\n";
				}
				echo "\t</tr>\n";
				
			}

			echo "</table>\n";
			echo "</div>\n";
			echo "<div class=\"clear\"></div>\n";
				
		}
	
	}

	function findUserId($email) {
	
		$select  = "SELECT * ";
		$select .= "FROM ad_user ";
		$select .= "WHERE emailuser = '" . $email . "' ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
			
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;
	
		if ($res && pg_num_rows($res) > 0) {
			return $row->ad_user_id;
		} else {
			return 99;
		}
	
	}
	
}
?>

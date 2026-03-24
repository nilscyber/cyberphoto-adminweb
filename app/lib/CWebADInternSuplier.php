<?php

/*

PHP login object
author		Stefan Sjöberg
version		1.0 2011-03-15

*/

include_once 'Db.php';
include_once 'CCheckIpNumber.php';

// require_once("AxTool2020.php");

Class CWebADInternSuplier {

	function __construct() {
		
	}

	function displaySuplierInfo($artnr) {

		if ($artnr == "") {
			return "";	// returnera nada

		} else {
		
			// $select = "SELECT DISTINCT c_bpartner.name, c_bpartner.url, m_product_po.vendorproductno, m_product_po.pricepo, m_product_po.c_bpartner_id, c_currency.iso_code, Ad_User.name ";
			$select = "SELECT DISTINCT c_bpartner.name, c_bpartner.url, m_product_po.vendorproductno, m_productprice.pricelimit, m_product_po.c_bpartner_id, 
					   Ad_User.name, m_productprice.pricestd, c_bpartner.value, c_bpartner.UserNameVendor, c_bpartner.PasswordVendor, c_bpartner.vendorproutines, c_bpartner.ReferenceNo, au2.name,
					   rep.level_min, rep.level_max, au3.name, m_product.created, m_product.manufacturerproductno, m_product.upc ";
			$select .= "FROM m_product_po ";
			$select .= "JOIN c_bpartner ON c_bpartner.c_bpartner_id = m_product_po.c_bpartner_id ";
			$select .= "LEFT JOIN c_bpartner_location ON c_bpartner.c_bpartner_id = c_bpartner_location.c_bpartner_id ";
			// $select .= "JOIN c_currency ON m_product_po.c_currency_id = c_currency.c_currency_id ";
			$select .= "JOIN m_product ON m_product.m_product_id = m_product_po.m_product_id ";
			$select .= "JOIN Ad_User ON Ad_User.Ad_User_id = c_bpartner.salesrep_id ";
			$select .= "JOIN m_productprice ON m_productprice.m_product_id = m_product.m_product_id ";
			$select .= "JOIN m_product_category cat ON m_product.m_product_category_id = cat.m_product_category_id ";
			$select .= "JOIN m_replenish rep ON m_product.m_product_id = rep.m_product_id ";
			$select .= "LEFT JOIN Ad_User au2 ON au2.Ad_User_id = cat.salesrep_id ";
			$select .= "LEFT JOIN Ad_User au3 ON au3.Ad_User_id = m_product.createdby  ";
			$select .= "WHERE m_product_po.isactive = 'Y' AND m_product_po.iscurrentvendor = 'Y' AND m_product.value = '" . $artnr. "' AND m_productprice.m_pricelist_version_id=1000000 ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
				// @ini_set('display_errors', 'on');
	//@error_reporting(E_ALL | E_STRICT);
				// @error_reporting(E_ALL);
				//echo $select;
				//exit;
			}
			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
				echo $select . "<br>";
			}
			
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					 while ($res && $row = pg_fetch_row($res)) {
						//$row = $row;
					 
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
							echo $res;
							/*
							echo $row[6] . "<br>";
							echo $row[3] . "<br>";
							*/
						}
						$tbvalue = $row[6] - $row[3];
						if ($row[6] > 0) {
							$tgvalue = (($tbvalue/$row[6])*100);
						} else {
							$tgvalue = -100;
						}
						$skapades = $this->getDaysLeft($row[16]);
				
						echo "Leverantör: $row[0] ($row[7])<br>";
						echo "Hemsida: $row[1]<br>";
						echo "Lev. artnr: <b>$row[2]</b><br>";
						echo "Tillverkarens artikelnummer: <b>" . $row[17] . "</b><br>";
						echo "EAN-kod: <b>" . $row[18] . "</b><br>";
						// if ($row[7] == 177 && $_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
						if ($row[7] == 177) {
							// require_once("AxTool2020.php");
							// echo utf8_encode ("Status leverantör: <b><font color=\"#FF00FF\">") . AxTool::getAvailableStock($row[2]) . "</font></b><br>";
						}
						// echo "Nettopris: " . round($row[3], 2) . " $row[5]<br>";
						echo "Limitpris: " . round($row[3], 2) . " SEK (" . round(($row[3]*1.25), 2) . " inkl. moms)<br>";
						if ($tbvalue > 0) {
							echo "TB: <span class=\"tbtgcolorgreen\">" . round($tbvalue, 2) . " SEK</span><br>";
							echo "TG: <span class=\"tbtgcolorgreen\">" . round($tgvalue, 2) . " %</span><br>";
						} else {
							echo "TB: <span class=\"tbtgcolorred\">" . round($tbvalue, 2) . " SEK</span><br>";
							echo "TG: <span class=\"tbtgcolorred\">" . round($tgvalue, 2) . " %</span><br>";
						}
						// echo "Telefon: $row[3] <br>";
						echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"350\" size=\"1\">";
						echo "Minlagersaldo: <b>" . round($row[13], 0) . "</b><br>";
						echo "Maxlagersaldo: <b>" . round($row[14], 0) . "</b><br>";
						$this->displaySuplierPhone($row[4]);
						echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"350\" size=\"1\">";
						echo "Ansvarig: $row[12]<br>";
						echo "Inköpare: $row[5]<br>";
						echo "Upplagd av: $row[15]<br>";
						echo "Skapades: $row[16] (" . abs($skapades) . " dagar sedan)<br>";
						echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"350\" size=\"1\">";
						echo "Vårt kundnummer: $row[11]<br>";
						if (CCheckIP::checkIfPurchaseValid()) {
							echo "Användarnamn: $row[8]<br>";
							echo "Lösenord: $row[9]<br>";
							echo "Hemsida: <a target=\"_blank\" href=\"$row[1]\">$row[1]<a/><br>";
						}
						echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"350\" size=\"1\">";
						echo "<u>Rutiner vid beställning:</u><br>$row[10]<br>";
						/*
						echo $row->name . "<br>";
						echo $row->url . "<br>";
						echo $row->phone . "<br><br>";
						echo "Leverantörens artikel nummer: <b>" . $row->vendorproductno . "</b><br>";
						*/
						$this->displaySuplierContacts($row[4]);
					}
					
				} else {
				
					// return "";
					echo "Ingen nuvarande leverantör finns för denna produkt";
				
				}
		}
	}

	function displaySuplierPhone($supID) {

			$select = "SELECT DISTINCT c_bpartner_location.phone ";
			$select .= "FROM c_bpartner_location ";
			$select .= "WHERE NOT (c_bpartner_location.phone IS NULL) AND c_bpartner_location.c_bpartner_id = $supID ";
			// echo $select;
			// exit;

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"350\" size=\"1\">";
					
					while ($res && $row = pg_fetch_row($res)) {
				
						echo "Telefon: $row[0]<br>";

					}
					
				} else {
				
					return "";
				
				}
	}

	function displaySuplierContacts($supID) {

			$select = "SELECT au.firstname, au.lastname, au.email, au.phone2, au.description ";
			$select .= "FROM ad_user au ";
			$select .= "WHERE au.isactive = 'Y' AND au.c_bpartner_id = $supID ";
			// echo $select;
			// exit;

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"350\" size=\"1\">";
					
					while ($res && $row = pg_fetch_row($res)) {
				
						echo "Kontakt: $row[0] $row[1]<br>";
						echo "E-post: $row[2]<br>";
						echo "Mobil: $row[3]<br>";
						echo "Roll: $row[4]<br><br>";

					}
					
				} else {
				
					return "";
				
				}
	}

	function getDaysLeft($frontDateTo) {

		$now = time();
		$timeto = strtotime($frontDateTo);
		$diff = $timeto - $now;
		$sek = $diff % 60;
		$min = ($diff / 60) % 60;
		$hour = ($diff / 3600);
		$days = ($diff / 86400);
		$days = floor($days);
		$days = round($days, 0);
		return $days;
	}

	function displayParcelInfo($artnr) {

		if ($artnr == "") {
			return "";	// returnera nada

		} else {
		
			$select = "SELECT m_productprice.pricelimit, m_productprice.pricestd, au3.name, m_product.created, au2.name ";
			$select .= "FROM m_productprice ";
			$select .= "JOIN m_product ON m_product.m_product_id = m_productprice.m_product_id ";
			$select .= "JOIN m_product_category cat ON m_product.m_product_category_id = cat.m_product_category_id ";
			$select .= "LEFT JOIN Ad_User au2 ON au2.Ad_User_id = cat.salesrep_id ";
			$select .= "LEFT JOIN Ad_User au3 ON au3.Ad_User_id = m_product.createdby  ";
			$select .= "WHERE m_product.value = '$artnr' AND m_productprice.m_pricelist_version_id=1000000 ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					 while ($res && $row = pg_fetch_row($res)) {
					 
						$tbvalue = $row[1] - $row[0];
						$tgvalue = (($tbvalue/$row[1])*100);
						$skapades = $this->getDaysLeft($row[3]);
				
						echo "Limitpris: " . round($row[0], 2) . " SEK (" . round(($row[0]*1.25), 2) . " inkl. moms)<br>";
						if ($tbvalue > 0) {
							echo "TB: <span class=\"tbtgcolorgreen\">" . round($tbvalue, 2) . " SEK</span><br>";
							echo "TG: <span class=\"tbtgcolorgreen\">" . round($tgvalue, 2) . " %</span><br>";
						} else {
							echo "TB: <span class=\"tbtgcolorred\">" . round($tbvalue, 2) . " SEK</span><br>";
							echo "TG: <span class=\"tbtgcolorred\">" . round($tgvalue, 2) . " %</span><br>";
						}
						echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"350\" size=\"1\">";
						echo "Ansvarig: $row[4]<br>";
						echo "Upplagd av: $row[2]<br>";
						echo "Skapades: $row[3] (" . abs($skapades) . " dagar sedan)<br>";
						echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"350\" size=\"1\">";
					}
					
				} else {
				
					return "";
				
				}
		}
	}

	function displayStoreValueLimitPrice() {

		$select = "SELECT SUM(pstock.qtyonhand*m_productprice.pricelimit) AS lagervarde ";
		$select .= "FROM m_product_stock_summary_v pstock ";
		$select .= "JOIN m_productprice ON m_productprice.m_product_id = pstock.m_product_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND m_productprice.pricelimit > 1 AND m_productprice.m_pricelist_version_id=1000000 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

		return $row->lagervarde;
				
	}

	function displayStoreValueAveragePrice() {

		$select = "SELECT SUM(pstock.qtyonhand*po.pricepo) AS lagervarde ";
		$select .= "FROM m_product_stock_summary_v pstock ";
		$select .= "JOIN m_product_po po ON po.m_product_id = pstock.m_product_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.pricepo > 1 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

		return $row->lagervarde;
				
	}

	function displayStoreValueAveragePriceAndDiff() {

		// $select = "SELECT SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, SUM(pstock.qtyavailable*po.currentcostprice) AS avalible, SUM((pstock.qtyonhand*po.currentcostprice)-(pstock.qtyavailable*po.currentcostprice)) AS different ";
		// $select = "SELECT SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, SUM(pstock.qtyavailable*po.currentcostprice) AS avalible, SUM(pstock.qtyallocated_storage*po.currentcostprice) AS different ";
		$select = "SELECT SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue ";
		$select .= "FROM m_product_stock_summary_v pstock ";
		$select .= "LEFT JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005  AND po.m_costtype_id=1000000 AND ad_client_id=1000000 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

		// return $row->lagervarde;
		echo "<div><span class=\"lagerrubrik\">" . number_format($row->storevalue, 0, ',', ' ') . " kr</span></div>\n";
		// echo "<div><span class=\"lagerrubrik2\">** " . number_format($row->avalible, 0, ',', ' ') . " kr (tillgängligt)</span></div>\n";
		// echo "<div><span class=\"lagerrubrik2\">** " . number_format($row->different, 0, ',', ' ') . " kr (allokerat)</span></div>\n";
		// echo "<div class='top10'></div>\n";
		// echo "<p>&nbsp;</p>\n";
				
	}

	function displayStoreValueSimple() {

		$select = "SELECT SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue ";
		// $select .= "FROM m_product_stock_summary_v pstock ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "LEFT JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005  AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

		return $row->storevalue;
				
	}

	function getStoreValueMysql() {

		$select = "SELECT stockvalue_Value, stockvalue_Date ";
		$select .= "FROM cyberadmin.stockvalue ";
		$select .= "WHERE stockvalue_Type = 2 "; // löpande
		$select .= "ORDER BY stockvalue_Date DESC ";
		$select .= "LIMIT 1 ";

		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					$timediff = time() - strtotime($stockvalue_Date);
					
					echo "<div><span class=\"lagerrubrik\">" . number_format($stockvalue_Value, 0, ',', ' ') . " kr</span></div>\n";
					echo "<div class=\"bottom10\" style=\"float: left; margin-left: 3px;\">Lagerv&auml;rdet uppdaterades f&ouml;r <i>" . $this->displayTimeFromReport($stockvalue_Date) . "</i>.</div>\n";
					if (!(date('N') == 6 || date('N') == 7 || date('G') < 8 || date('G') > 18 || (date('G') == 8 && date('i') < 16))) {
						// if (date('i') == 59 || date('i') == 14 || date('i') == 29 || date('i') == 44) {
						if (date('i') < 14) {
							$present = 14 - date('i');
							echo "<div class=\"bottom10\" style=\"float: left; margin-left: 3px;\">Uppdateras igen om <i>$present minuter</i>.</div>\n";
						} elseif (date('i') < 29) {
							$present = 29 - date('i');
							echo "<div class=\"bottom10\" style=\"float: left; margin-left: 3px;\">Uppdateras igen om <i>$present minuter</i>.</div>\n";
						} elseif (date('i') < 44) {
							$present = 44 - date('i');
							echo "<div class=\"bottom10\" style=\"float: left; margin-left: 3px;\">Uppdateras igen om <i>$present minuter</i>.</div>\n";
						} elseif (date('i') < 59) {
							$present = 59 - date('i');
							echo "<div class=\"bottom10\" style=\"float: left; margin-left: 3px;\">Uppdateras igen om <i>$present minuter</i>.</div>\n";
						}
						echo "<div class=\"clear\"></div>\n";
					}
				
				endwhile;
				
			} else {
			
				echo "";
			
			}

	}

	function displayTimeFromReport($plankTid) {

		$justnu = strtotime("now");
		$inlagg = strtotime($plankTid);
		$sekdiff = $justnu - $inlagg;
		
		if ($sekdiff < 60) {
			$displayfrom = round($sekdiff,0) . " sekunder sedan";
		} elseif ($sekdiff > 59 && $sekdiff < 5401) {
			$displayfrom = round($sekdiff/60,0) . " minuter sedan";
		} elseif ($sekdiff > 5400 && $sekdiff < 172800) {
			$displayfrom = round($sekdiff/60/60,0) . " timmar sedan";
		} else {
			$displayfrom = round($sekdiff/60/60/24,0) . " dagar sedan";
		}
		
		return $displayfrom;
		
	}
	
	function displayStoreDiffSimple() {

		// $select = "SELECT SUM((pstock.qtyonhand*po.currentcostprice)-(pstock.qtyavailable*po.currentcostprice)) AS different ";
		$select = "SELECT SUM(pstock.qtyallocated_storage*po.currentcostprice) AS different ";
		$select .= "FROM m_product_stock_summary_v pstock ";
		$select .= "LEFT JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005  AND po.m_costtype_id=1000000 AND ad_client_id=1000000 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

		return $row->different;
				
	}

	function displaySuplierValue() {
			global $demo, $begagnat;

			$totalvarde = 0;

			// $select = "SELECT cbp.value, cbp.name, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, SUM(pstock.qtyonhand) ";
			// $select = "SELECT cbp.value, cbp.name, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, SUM(pstock.qtyonhand) ";
			$select = "SELECT cbp.c_bpartner_id, cbp.name, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, cbp.value ";
			// $select .= "FROM m_product_stock_summary_v pstock ";
			$select .= "FROM m_product_cache pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
			$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND 
			po.ad_client_id=1000000 AND prod_po.iscurrentvendor = 'Y' AND po.isactive = 'Y' ";
			if ($demo == "yes" && $begagnat != "yes") {
				$select .= "AND prod.demo_product = 'Y' AND prod.istradein = 'N' ";
			} elseif ($demo != "yes" && $begagnat == "yes") {
				$select .= "AND prod.demo_product = 'Y' AND prod.istradein = 'Y' ";
			} elseif ($demo == "yes" && $begagnat == "yes") {
				$select .= "AND ((prod.demo_product = 'Y' AND prod.istradein = 'N') OR (prod.demo_product = 'Y' AND prod.istradein = 'Y')) ";
			} else {
				$select .= "AND prod.demo_product = 'N' ";
			}
			$select .= "GROUP BY cbp.c_bpartner_id, cbp.name ";
			$select .= "ORDER BY storevalue DESC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"650\"><b>Leverantör</b></td>";
			// echo "<td width=\"75\" align=\"center\"><b>Antal</b></td>";
			echo "<td width=\"120\" align=\"center\"><b>Lagervärde</b></td>";
			echo "</tr>";

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						
						$totalvarde += $row[2];
						$SummaLager = number_format($row[2], 0, ',', ' ');

						echo "<tr>";
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?supID=$row[3]&demo=" . $demo . "&begagnat=" . $begagnat . "\">$row[1] ($row[3])</a></td>";
						} else {
							echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?supID=$row[3]&demo=" . $demo . "&begagnat=" . $begagnat . "\">$row[1] ($row[3])</a></td>";
						}
						// echo "<td class=\"$backcolor\" align=\"center\">$row[3]</td>";
						echo "<td class=\"$backcolor\" align=\"right\">$SummaLager SEK&nbsp;</td>";
						echo "</tr>";

						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
						
					}

				}

			$totalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>";
			echo "<td align=\"left\"><b>Totalt:</b></td>";
			// echo "<td align=\"right\"><b></b></td>";
			echo "<td align=\"right\"><b>$totalvarde SEK&nbsp;</b></td>";
			echo "</tr>";
			echo "</table>";

	}

	function displaySuplierValueDetail($supID) {
			global $export, $demo, $begagnat;
			
			if ($export){
				$setdate = date("Ymd");
				$settitle = $supID . "_" . $setdate;
				/*
				header("Content-type: application/octet-stream"); 
				// header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=$settitle.xls"); 
				header("Pragma: no-cache"); 
				header("Expires: 0");
				*/
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");;
				header("Content-Disposition: attachment; filename=$settitle.xls");
				header("Content-Transfer-Encoding: binary ");
				
			}

			$antal_produkter = 0;
			$totalvarde = 0;
			$totalallokerat = 0;

			// $select = "SELECT prod.value, SUM(pstock.qtyonhand), SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, SUM((pstock.qtyonhand*po.currentcostprice)-(pstock.qtyavailable*po.currentcostprice)) AS different, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			// $select = "SELECT prod.value,  pstock.qtyonhand, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, SUM(pstock.qtyallocated_storage*po.currentcostprice) AS different, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			$select = "SELECT prod.value,  pstock.qtyonhand, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, SUM(pstock.qtyreserved*po.currentcostprice) AS different, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			// $select .= "FROM m_product_stock_summary_v pstock ";
			$select .= "FROM m_product_cache pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id  ";
			$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id  ";
			$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
			$select .= "LEFT JOIN xc_product_statistics stat ON stat.m_product_id = pstock.m_product_id ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND 
						po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
			// $select .= "AND cbp.c_bpartner_id = $supID AND stat.c_country_id IS Null AND prod_po.iscurrentvendor = 'Y' ";
			$select .= "AND cbp.value = '$supID' AND stat.c_country_id IS Null AND prod_po.iscurrentvendor = 'Y' ";
			if ($demo == "yes" && $begagnat != "yes") {
				$select .= "AND prod.demo_product = 'Y' AND prod.istradein = 'N' ";
			} elseif ($demo != "yes" && $begagnat == "yes") {
				$select .= "AND prod.demo_product = 'Y' AND prod.istradein = 'Y' ";
			} elseif ($demo == "yes" && $begagnat == "yes") {
				$select .= "AND ((prod.demo_product = 'Y' AND prod.istradein = 'N') OR (prod.demo_product = 'Y' AND prod.istradein = 'Y')) ";
			} else {
				$select .= "AND prod.demo_product = 'N' ";
			}
			$select .= "GROUP BY prod.value, pstock.qtyonhand, prod.name, manu.name, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			if ($supID == 5555) {
				$select .= "ORDER BY manu.name ASC, prod.name ASC ";
			} else {
				$select .= "ORDER BY storevalue DESC ";
			}
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.144xx") {
				echo $select;
				exit;
			}
			
			if ($export) {

				/*
				echo "<table>";
				echo "<tr>";
				echo "<td><b>Artikel</b></td>";
				echo "<td><b>Antal</b></td>";
				echo "<td><b>Lagervärde</b></td>";
				echo "<td><b>Allokerat</b></td>";
				echo "<td><b>Räcker</b></td>";
				echo "<td><b>Sålda 30</b></td>";
				echo "<td><b>Sålda 60</b></td>";
				echo "</tr>";
				*/

				echo "Artikel\t";
				echo "Tillverkare\t";
				echo "Antal\t";
				echo "Lagervärde\t";
				echo "Allokerat\t";
				echo "Räcker\t";
				echo "Sålda 30\t";
				echo "Sålda 60\t\n";
			
			} else {

				echo "<table>";
				echo "<tr>";
				echo "<td width=\"550\"><b>Artikel</b></td>";
				// echo "<td width=\"100\"><b>Tillverkare</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Antal</b></td>";
				echo "<td width=\"100\" align=\"center\"><b>Lagervärde</b></td>";
				echo "<td width=\"100\" align=\"center\"><b>Allokerat</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Räcker</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
				echo "</tr>";
			
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						if ($export) {

							$antalManad1 = $row[6];
							$antalManad2 = $row[7];
						
							if ($antalManad1 == null) {
								$antalManad1 = 0;
							}
							if ($antalManad2 == null) {
								$antalManad2 = 0;
							}
							if ($antalManad1 != 0) {
								$Takt = round((($row[1]/$antalManad1)*30),0);
							}

							if ($antalManad1 == 0 && $antalManad2 == 0) {
								$avvikelse = 999;
							} else {
								$avvikelse = round($antalManad1 / $antalManad2, 2);
							}

							if ($rowcolor == true) {
								$backcolor = "firstrow";
							} else {
								$backcolor = "secondrow";
							}
							
							$beskrivning = $row[4] . " " . $row[3];
							if (strlen($beskrivning) > 75) {
								$beskrivning = substr ($beskrivning, 0, 75) . "....";
							}
							if ($row[8] == "N") {
								$beskrivning .= " - VISAS EJ PÅ WEBBEN!";
							}
							$totalvarde += $row[2];
							$totalallokerat+= $row[5];
							$SummaLager = number_format($row[2], 0, ',', ' ');
							$allokerat = number_format($row[5], 0, ',', ' ');

							/*
							echo "<tr>";
							echo "<td nowrap>$beskrivning</td>";
							echo "<td nowrap>$row[1]</td>";
							echo "<td nowrap>$SummaLager</td>";
							echo "<td nowrap>$allokerat</td>";
							if ($antalManad1 == 0) {
								echo "<td nowrap>9999</td>";
							} else {
								echo "<td nowrap>$Takt</td>";
							}
							echo "<td nowrap>$antalManad1</td>";
							echo "<td nowrap>$antalManad2</td>";
							echo "</tr>";
							*/

							echo "$beskrivning\t";
							echo $row[4] . "\t";
							echo "$row[1]\t";
							echo "$SummaLager\t";
							echo "$allokerat\t";
							if ($antalManad1 == 0) {
								echo "9999\t";
							} else {
								echo "$Takt\t";
							}
							echo "$antalManad1\t";
							echo "$antalManad2\t\n";
						
						} else {
					
							$antalManad1 = $row[6];
							$antalManad2 = $row[7];
						
							if ($antalManad1 == null) {
								$antalManad1 = 0;
							}
							if ($antalManad2 == null) {
								$antalManad2 = 0;
							}
							if ($antalManad1 != 0) {
								$Takt = round((($row[1]/$antalManad1)*30),0);
							}

							if ($antalManad1 == 0 && $antalManad2 == 0) {
								$avvikelse = 999;
							} else {
								$avvikelse = round($antalManad1 / $antalManad2, 2);
							}

							if ($rowcolor == true) {
								$backcolor = "firstrow";
							} else {
								$backcolor = "secondrow";
							}
							$row[3] = $row[3];
							$row[4] = $row[4];
							if ($supID == 5555) {
								$beskrivning = $row[4] . " " . $row[3];
							} else {
								$beskrivning = $row[4] . " " . $row[3];
							}
							if (strlen($beskrivning) > 75) {
								$beskrivning = substr ($beskrivning, 0, 75) . "....";
							}
							if ($row[8] == "N") {
								$beskrivning .= " -<span class=\"notshowweb\"> VISAS EJ PÅ WEBBEN!</span>";
							}
							$totalvarde += $row[2];
							$totalallokerat+= $row[5];
							$SummaLager = number_format($row[2], 0, ',', ' ');
							$allokerat = number_format($row[5], 0, ',', ' ');
							// $antal_produkter++;
							$antal_produkter += $row[1];

							echo "<tr>";
							if ($supID == 5555) {
								echo "<td class=\"$backcolor\">(" . $row[0] . ") <a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row[0]\">$beskrivning</a></td>";
							} else {
								echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row[0]\">$beskrivning...</a></td>";
							}
							// echo "<td class=\"$backcolor\" align=\"center\">$row[4]</td>";
							echo "<td class=\"$backcolor\" align=\"center\">$row[1]</td>";
							echo "<td class=\"$backcolor\" align=\"right\">$SummaLager SEK&nbsp;</td>";
							echo "<td class=\"$backcolor\" align=\"right\">$allokerat SEK&nbsp;</td>";
							if ($antalManad1 == 0) {
								echo "<td class=\"$backcolor\" align=\"center\"><span class=\"lagerred\">&#8734;</span></td>";
							} else {
								if ($Takt < 15) {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagergreen\">$Takt dagar&nbsp;</span></td>";
								} elseif ($Takt > 14 && $Takt < 31)  {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerblack\">$Takt dagar&nbsp;</span></td>";
								} elseif ($Takt > 30 && $Takt < 100)  {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerred\">$Takt dagar&nbsp;</span></td>";
								} else {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerredbold\">$Takt dagar&nbsp;</span></td>";
								}
							}
							echo "<td class=\"$backcolor\" align=\"center\">$antalManad1</td>";
							echo "<td class=\"$backcolor\" align=\"center\">$antalManad2</td>";
							echo $this->displayTrend($avvikelse);
							echo "</tr>";

							if ($rowcolor == true) {
								$row = true;
								$rowcolor = false;
							} else {
								$row = false;
								$rowcolor = true;
							}
							
						}
						
					}

				}

			if (!$export) {
				
				$totalvarde = number_format($totalvarde, 0, ',', ' ');
				$totalallokerat = number_format($totalallokerat, 0, ',', ' ');
				echo "<tr>";
				echo "<td align=\"left\"><b>Totalt:</b></td>";
				if ($supID == 5555) {
					echo "<td align=\"center\"><b>$antal_produkter st</b></td>";
				} else {
					echo "<td align=\"center\"><b>$antal_produkter st</b></td>";
				}
				echo "<td align=\"right\"><b>$totalvarde SEK&nbsp;</b></td>";
				echo "<td align=\"right\"><b>$totalallokerat SEK&nbsp;</b></td>";
				echo "</tr>";
				echo "</table>";
				
			}
				
	}

	function displayManufacturerValue() {
			global $demo, $begagnat;

			$totalvarde = 0;

			$select = "SELECT manu.xc_manufacturer_id, manu.name, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue ";
			$select .= "FROM m_product_cache pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
			$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND 
			po.ad_client_id=1000000 AND prod_po.iscurrentvendor = 'Y' AND po.isactive = 'Y' ";
			if ($demo == "yes" && $begagnat != "yes") {
				$select .= "AND demo_product = 'Y' AND istradein = 'N' ";
			} elseif ($demo != "yes" && $begagnat == "yes") {
				$select .= "AND demo_product = 'Y' AND istradein = 'Y' ";
			} elseif ($demo == "yes" && $begagnat == "yes") {
				$select .= "AND ((demo_product = 'Y' AND istradein = 'N') OR (demo_product = 'Y' AND istradein = 'Y')) ";
			} else {
				$select .= "AND demo_product = 'N' ";
			}
			$select .= "GROUP BY manu.xc_manufacturer_id ";
			$select .= "ORDER BY storevalue DESC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}
			/*
			echo "demo: ". $demo . "<br>";
			echo "begagnat: ". $begagnat . "<br>";
			*/

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"650\"><b>Tillverkare</b></td>";
			// echo "<td width=\"75\" align=\"center\"><b>Antal</b></td>";
			echo "<td width=\"120\" align=\"center\"><b>Lagervärde</b></td>";
			echo "</tr>";

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						
						$totalvarde += $row[2];
						$SummaLager = number_format($row[2], 0, ',', ' ');

						echo "<tr>";
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							// echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?manID=$row[0]\">$row[1]</a></td>";
							echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?manID=$row[0]&demo=" . $demo . "&begagnat=" . $begagnat . "\">" . $row[1] . "</a></td>";
						} else {
							echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?manID=$row[0]&demo=" . $demo . "&begagnat=" . $begagnat . "\">" . $row[1] . "</a></td>";
						}
						// echo "<td class=\"$backcolor\" align=\"center\">$row[3]</td>";
						echo "<td class=\"$backcolor\" align=\"right\">$SummaLager SEK&nbsp;</td>";
						echo "</tr>";

						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
						
					}

				}

			$totalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>";
			echo "<td align=\"left\"><b>Totalt:</b></td>";
			// echo "<td align=\"right\"><b></b></td>";
			echo "<td align=\"right\"><b>$totalvarde SEK&nbsp;</b></td>";
			echo "</tr>";
			echo "</table>";

	}

	function displayManufacturerValueDetail($manID) {
			global $export, $demo, $begagnat;
			
			if ($export){
				$setdate = date("Ymd");
				$settitle = $supID . "_" . $setdate;
				/*
				header("Content-type: application/octet-stream"); 
				// header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=$settitle.xls"); 
				header("Pragma: no-cache"); 
				header("Expires: 0");
				*/
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");;
				header("Content-Disposition: attachment; filename=$settitle.xls");
				header("Content-Transfer-Encoding: binary ");
				
			}

			$antal_produkter = 0;
			$totalvarde = 0;
			$totalallokerat = 0;

			// $select = "SELECT prod.value, SUM(pstock.qtyonhand), SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, SUM((pstock.qtyonhand*po.currentcostprice)-(pstock.qtyavailable*po.currentcostprice)) AS different, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			// $select = "SELECT prod.value,  pstock.qtyonhand, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, SUM(pstock.qtyallocated_storage*po.currentcostprice) AS different, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			$select = "SELECT prod.value,  pstock.qtyonhand, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, SUM(pstock.qtyreserved*po.currentcostprice) AS different, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			// $select .= "FROM m_product_stock_summary_v pstock ";
			$select .= "FROM m_product_cache pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id  ";
			$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id  ";
			$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
			$select .= "LEFT JOIN xc_product_statistics stat ON stat.m_product_id = pstock.m_product_id ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND 
						po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
			// $select .= "AND cbp.c_bpartner_id = $supID AND stat.c_country_id IS Null AND prod_po.iscurrentvendor = 'Y' ";
			$select .= "AND manu.xc_manufacturer_id = '$manID' AND stat.c_country_id IS Null AND prod_po.iscurrentvendor = 'Y' ";
			if ($demo == "yes" && $begagnat != "yes") {
				$select .= "AND prod.demo_product = 'Y' AND prod.istradein = 'N' ";
			} elseif ($demo != "yes" && $begagnat == "yes") {
				$select .= "AND prod.demo_product = 'Y' AND prod.istradein = 'Y' ";
			} elseif ($demo == "yes" && $begagnat == "yes") {
				$select .= "AND ((prod.demo_product = 'Y' AND prod.istradein = 'N') OR (prod.demo_product = 'Y' AND prod.istradein = 'Y')) ";
			} else {
				$select .= "AND demo_product = 'N' ";
			}
			$select .= "GROUP BY prod.value, pstock.qtyonhand, prod.name, manu.name, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			if ($supID == 5555) {
				$select .= "ORDER BY manu.name ASC, prod.name ASC ";
			} else {
				$select .= "ORDER BY storevalue DESC ";
			}
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}
			
			if ($export) {

				/*
				echo "<table>";
				echo "<tr>";
				echo "<td><b>Artikel</b></td>";
				echo "<td><b>Antal</b></td>";
				echo "<td><b>Lagervärde</b></td>";
				echo "<td><b>Allokerat</b></td>";
				echo "<td><b>Räcker</b></td>";
				echo "<td><b>Sålda 30</b></td>";
				echo "<td><b>Sålda 60</b></td>";
				echo "</tr>";
				*/

				echo "Artikel\t";
				echo "Tillverkare\t";
				echo "Antal\t";
				echo "Lagervärde\t";
				echo "Allokerat\t";
				echo "Räcker\t";
				echo "Sålda 30\t";
				echo "Sålda 60\t\n";
			
			} else {

				echo "<table>";
				echo "<tr>";
				echo "<td><b>Artnr</b></td>";
				echo "<td width=\"550\"><b>Artikel</b></td>";
				// echo "<td width=\"100\"><b>Tillverkare</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Antal</b></td>";
				echo "<td width=\"100\" align=\"center\"><b>Lagervärde</b></td>";
				echo "<td width=\"100\" align=\"center\"><b>Allokerat</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Räcker</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
				echo "</tr>";
			
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						if ($export) {

							$antalManad1 = $row[6];
							$antalManad2 = $row[7];
						
							if ($antalManad1 == null) {
								$antalManad1 = 0;
							}
							if ($antalManad2 == null) {
								$antalManad2 = 0;
							}
							if ($antalManad1 != 0) {
								$Takt = round((($row[1]/$antalManad1)*30),0);
							}

							if ($antalManad1 == 0 && $antalManad2 == 0) {
								$avvikelse = 999;
							} else {
								$avvikelse = round($antalManad1 / $antalManad2, 2);
							}

							if ($rowcolor == true) {
								$backcolor = "firstrow";
							} else {
								$backcolor = "secondrow";
							}
							
							$beskrivning = $row[4] . " " . $row[3] . " ...";
							if (strlen($beskrivning) > 75) {
								$beskrivning = substr ($beskrivning, 0, 75);
							}
							if ($row[8] == "N") {
								$beskrivning .= " - VISAS EJ PÅ WEBBEN!";
							}
							$totalvarde += $row[2];
							$totalallokerat+= $row[5];
							$SummaLager = number_format($row[2], 0, ',', ' ');
							$allokerat = number_format($row[5], 0, ',', ' ');

							/*
							echo "<tr>";
							echo "<td nowrap>$beskrivning</td>";
							echo "<td nowrap>$row[1]</td>";
							echo "<td nowrap>$SummaLager</td>";
							echo "<td nowrap>$allokerat</td>";
							if ($antalManad1 == 0) {
								echo "<td nowrap>9999</td>";
							} else {
								echo "<td nowrap>$Takt</td>";
							}
							echo "<td nowrap>$antalManad1</td>";
							echo "<td nowrap>$antalManad2</td>";
							echo "</tr>";
							*/

							echo "$beskrivning\t";
							echo $row[4] . "\t";
							echo "$row[1]\t";
							echo "$SummaLager\t";
							echo "$allokerat\t";
							if ($antalManad1 == 0) {
								echo "9999\t";
							} else {
								echo "$Takt\t";
							}
							echo "$antalManad1\t";
							echo "$antalManad2\t\n";
						
						} else {
					
							$antalManad1 = $row[6];
							$antalManad2 = $row[7];
						
							if ($antalManad1 == null) {
								$antalManad1 = 0;
							}
							if ($antalManad2 == null) {
								$antalManad2 = 0;
							}
							if ($antalManad1 != 0) {
								$Takt = round((($row[1]/$antalManad1)*30),0);
							}

							if ($antalManad1 == 0 && $antalManad2 == 0) {
								$avvikelse = 999;
							} else {
								$avvikelse = round($antalManad1 / $antalManad2, 2);
							}

							if ($rowcolor == true) {
								$backcolor = "firstrow";
							} else {
								$backcolor = "secondrow";
							}
							
							if ($supID == 5555) {
								$beskrivning = $row[4] . " " . $row[3];
							} else {
								$beskrivning = $row[4] . " " . $row[3];
							}
							$beskrivning = $beskrivning;
							if (strlen($beskrivning) > 75) {
								$beskrivning = substr ($beskrivning, 0, 75) . "....";
							}
							if ($row[8] == "N") {
								$beskrivning .= " -<span class=\"notshowweb\"> VISAS EJ PÅ WEBBEN!</span>";
							}
							$totalvarde += $row[2];
							$totalallokerat+= $row[5];
							$SummaLager = number_format($row[2], 0, ',', ' ');
							$allokerat = number_format($row[5], 0, ',', ' ');
							// $antal_produkter++;
							$antal_produkter += $row[1];

							echo "<tr>";
							echo "<td class=\"$backcolor\" align=\"center\">$row[0]</td>";
							if ($supID == 5555) {
								echo "<td class=\"$backcolor\">(" . $row[0] . ") <a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row[0]\">$beskrivning</a></td>";
							} else {
								echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row[0]\">$beskrivning</a></td>";
							}
							// echo "<td class=\"$backcolor\" align=\"center\">$row[4]</td>";
							echo "<td class=\"$backcolor\" align=\"center\">$row[1]</td>";
							echo "<td class=\"$backcolor\" align=\"right\">$SummaLager SEK&nbsp;</td>";
							echo "<td class=\"$backcolor\" align=\"right\">$allokerat SEK&nbsp;</td>";
							if ($antalManad1 == 0) {
								echo "<td class=\"$backcolor\" align=\"center\"><span class=\"lagerred\">&#8734;</span></td>";
							} else {
								if ($Takt < 15) {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagergreen\">$Takt dagar&nbsp;</span></td>";
								} elseif ($Takt > 14 && $Takt < 31)  {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerblack\">$Takt dagar&nbsp;</span></td>";
								} elseif ($Takt > 30 && $Takt < 100)  {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerred\">$Takt dagar&nbsp;</span></td>";
								} else {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerredbold\">$Takt dagar&nbsp;</span></td>";
								}
							}
							echo "<td class=\"$backcolor\" align=\"center\">$antalManad1</td>";
							echo "<td class=\"$backcolor\" align=\"center\">$antalManad2</td>";
							echo $this->displayTrend($avvikelse);
							echo "</tr>";

							if ($rowcolor == true) {
								$row = true;
								$rowcolor = false;
							} else {
								$row = false;
								$rowcolor = true;
							}
							
						}
						
					}

				}

			if (!$export) {
				
				$totalvarde = number_format($totalvarde, 0, ',', ' ');
				$totalallokerat = number_format($totalallokerat, 0, ',', ' ');
				echo "<tr>";
				echo "<td align=\"left\"><b>Totalt:</b></td>";
				if ($supID == 5555) {
					echo "<td align=\"center\"><b>$antal_produkter st</b></td>";
				} else {
					echo "<td align=\"center\"><b>$antal_produkter st</b></td>";
				}
				echo "<td align=\"right\"><b>$totalvarde SEK&nbsp;</b></td>";
				echo "<td align=\"right\"><b>$totalallokerat SEK&nbsp;</b></td>";
				echo "</tr>";
				echo "</table>";
				
			}
				
	}

	function displayProductsValue() {

			$totalvarde = 0;
			$countrow = 1;

			$select = "SELECT cat.name, SUM(pstock.qtyonhand), SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, cat.value, u.value ";
			// $select .= "FROM m_product_stock_summary_v pstock ";
			$select .= "FROM m_product_cache pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_category cat ON cat.m_product_category_id = prod.m_product_category_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "LEFT JOIN ad_user u ON cat.salesrep_id = u.ad_user_id ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
			$select .= "GROUP BY cat.name, cat.value, u.value ";
			$select .= "ORDER BY storevalue DESC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}
	
			echo "<table>";
			echo "<tr>";
			// echo "<td width=\"10\">&nbsp;</td>";
			echo "<td width=\"550\"><b>Kategori</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Antal</b></td>";
			echo "<td width=\"120\" align=\"center\"><b>Lagervärde</b></td>";
			echo "<td width=\"70\" align=\"center\"><b>Ansvarig</b></td>";
			echo "</tr>";

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						
						$totalvarde += $row[2];
						$SummaLager = number_format($row[2], 0, ',', ' ');

						echo "<tr>";
						// echo "<td class=\"$backcolor\" align=\"center\">$countrow</td>";
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?katID=$row[3]\">$row[0]</a></td>";
						} else {
							echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?katID=$row[3]\">$row[0]</a></td>";
						}
						echo "<td class=\"$backcolor\" align=\"center\">$row[1]</td>";
						echo "<td class=\"$backcolor\" align=\"right\">$SummaLager SEK&nbsp;</td>";
						echo "<td class=\"$backcolor\" align=\"center\">" . strtoupper($row[4]) . "</td>";
						echo "</tr>";

						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
						
						$countrow++;
						
					}

				}

			$totalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>";
			echo "<td align=\"left\"><b>Totalt:</b></td>";
			echo "<td align=\"right\"><b></b></td>";
			echo "<td align=\"right\"><b>$totalvarde SEK&nbsp;</b></td>";
			echo "</tr>";
			echo "</table>";

	}

	function displayProductsValueDetail($katID) {

			$totalvarde = 0;
			$totalallokerat = 0;

			// $select = "SELECT prod.value, SUM(pstock.qtyonhand), SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, SUM((pstock.qtyonhand*po.currentcostprice)-(pstock.qtyavailable*po.currentcostprice)) AS different, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			// $select = "SELECT prod.value, SUM(pstock.qtyonhand), SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, SUM(pstock.qtyallocated_storage*po.currentcostprice) AS different, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			// $select = "SELECT prod.value, pstock.qtyonhand, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, SUM(pstock.qtyallocated_storage*po.currentcostprice) AS different, stat.qtymonth, stat.qty2month, prod.IsSelfService, pstock.qtyallocated_storage ";
			$select = "SELECT prod.value, pstock.qtyonhand, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, po.currentcostprice, stat.qtymonth, stat.qty2month, prod.IsSelfService, pstock.qtyreserved ";
			// $select .= "FROM m_product_stock_summary_v pstock ";
			$select .= "FROM m_product_cache pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_category cat ON cat.m_product_category_id = prod.m_product_category_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
			$select .= "LEFT JOIN xc_product_statistics stat ON stat.m_product_id = pstock.m_product_id  AND (stat.c_country_id IS Null OR stat.c_country_id = 0) ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND 
						po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
			$select .= "AND cat.value = '$katID' ";
			// $select .= "GROUP BY prod.value, pstock.qtyonhand, prod.name, manu.name, stat.qtymonth, stat.qty2month, prod.IsSelfService, pstock.qtyallocated_storage ";
			$select .= "GROUP BY prod.value, pstock.qtyonhand, prod.name, manu.name, po.currentcostprice, stat.qtymonth, stat.qty2month, prod.IsSelfService, pstock.qtyreserved ";
			$select .= "ORDER BY storevalue DESC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			echo "<table width=\"1250\">";
			echo "<tr>";
			echo "<td><b>Artikel</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Antal</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Lagervärde</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Allokerat</b></td>";
			echo "<td width=\"55\" align=\"center\"><b>Antal</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Räcker</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
			echo "</tr>";

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						$antalManad1 = $row[6];
						$antalManad2 = $row[7];
					
						if ($antalManad1 == null) {
							$antalManad1 = 0;
						}
						if ($antalManad2 == null) {
							$antalManad2 = 0;
						}
						if ($antalManad1 != 0) {
							$Takt = round((($row[1]/$antalManad1)*30),0);
						}

						if ($antalManad1 == 0 && $antalManad2 == 0) {
							$avvikelse = 999;
						} else {
							$avvikelse = round($antalManad1 / $antalManad2, 2);
						}

						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						
						$beskrivning = $row[4] . " " . $row[3];
						if (strlen($beskrivning) > 75) {
							$beskrivning = substr ($beskrivning, 0, 75) . "....";
						}
						if ($row[8] == "N") {
							$beskrivning .= " -<span class=\"notshowweb\"> VISAS EJ PÅ WEBBEN!</span>";
						}
						$totalvarde += $row[2];
						$SummaLager = number_format($row[2], 0, ',', ' ');
						if ($row[9] > $row[1]) {
							$allokerat_antal = $row[1];
						} else {
							$allokerat_antal = $row[9];
						}
						$allokerat = number_format(($row[5]*$allokerat_antal), 0, ',', ' ');
						$totalallokerat+= $row[5]*$allokerat_antal;

						echo "<tr>";
						echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row[0]\">$beskrivning</a></td>";
						echo "<td class=\"$backcolor\" align=\"center\">$row[1]</td>";
						echo "<td class=\"$backcolor\" align=\"right\">$SummaLager SEK&nbsp;</td>";
						echo "<td class=\"$backcolor\" align=\"right\">$allokerat SEK&nbsp;</td>";
						echo "<td class=\"$backcolor\" align=\"center\">$allokerat_antal</td>";
						if ($antalManad1 == 0) {
							echo "<td class=\"$backcolor\" align=\"center\"><span class=\"lagerred\">&#8734;</span></td>";
						} else {
							if ($Takt < 15) {
								echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagergreen\">$Takt dagar&nbsp;</span></td>";
							} elseif ($Takt > 14 && $Takt < 31)  {
								echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerblack\">$Takt dagar&nbsp;</span></td>";
							} elseif ($Takt > 30 && $Takt < 100)  {
								echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerred\">$Takt dagar&nbsp;</span></td>";
							} else {
								echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerredbold\">$Takt dagar&nbsp;</span></td>";
							}
						}
						echo "<td class=\"$backcolor\" align=\"center\">$antalManad1</td>";
						echo "<td class=\"$backcolor\" align=\"center\">$antalManad2</td>";
						echo $this->displayTrend($avvikelse);
						echo "</tr>";

						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
						
					}

				}

			$totalvarde = number_format($totalvarde, 0, ',', ' ');
			$totalallokerat = number_format($totalallokerat, 0, ',', ' ');
			echo "<tr>";
			echo "<td align=\"left\"><b>Totalt:</b></td>";
			echo "<td align=\"right\"><b></b></td>";
			echo "<td align=\"right\"><b>$totalvarde SEK&nbsp;</b></td>";
			echo "<td align=\"right\"><b>$totalallokerat SEK&nbsp;</b></td>";
			echo "<td align=\"right\"><b>&nbsp;</b></td>";
			echo "</tr>";
			echo "</table>";

	}

	function displayProductsValueDetailAll($sortBy="storevalue", $showLastInvoiced=true) {
			global $export;
			
			if ($export){
				$setdate = date("YmdHi");
				$settitle = "ALL_" . $setdate;
				/*
				header("Content-type: application/octet-stream"); 
				// header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=$settitle.xls"); 
				header("Pragma: no-cache"); 
				header("Expires: 0");
				*/
				/*
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");;
				header('Content-type: application/ms-excel');
				header("Content-Disposition: attachment; filename=$settitle.xls");
				header("Content-Transfer-Encoding: binary ");
				*/
				
			}
			$totalvarde = 0;
			$totalallokerat = 0;

			// $select = "SELECT prod.value, SUM(pstock.qtyonhand), SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, manu.name, SUM((pstock.qtyonhand*po.currentcostprice)-(pstock.qtyavailable*po.currentcostprice)) AS different, stat.qtymonth, stat.qty2month, prod.IsSelfService ";
			$select = "SELECT prod.value, pstock.qtyonhand, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, prod.name, 
			manu.name, SUM(pstock.qtyallocated_storage*po.currentcostprice) AS different, stat.qtymonth, stat.qty2month, 
			prod.IsSelfService, prod.m_product_id ";
			if ($showLastInvoiced)
				$select .= ",  (SELECT MAX(i.dateinvoiced) FROM C_Invoice i, C_InvoiceLine il WHERE i.c_invoice_id = il.c_invoice_id AND il.m_product_id = prod.m_product_id) as last_invoiced "; 
			$select .= ",cbp.name   ";
			$select .= "FROM m_product_stock_summary_v pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_category cat ON cat.m_product_category_id = prod.m_product_category_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
			$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id  "; // ss
			$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id  ";// ss
			$select .= "LEFT JOIN xc_product_statistics stat ON stat.m_product_id = pstock.m_product_id AND (stat.c_country_id is null OR stat.c_country_id = 0) ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND 
						po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND prod_po.iscurrentvendor = 'Y' ";
			//$select .= " stat.c_country_id = 0 ";
			$select .= "GROUP BY prod.m_product_id, pstock.qtyonhand, prod.value, prod.name, manu.name, stat.qtymonth, stat.qty2month, prod.IsSelfService, cbp.name ";
			$select .= "ORDER BY $sortBy DESC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			if ($export) {
				$contents = "Leverantör \t Artikel \t Antal \t Lagervärde \t Allokerat \t Räcker \t Sålda 30 \t Sålda 60 \t Senast såld \n";
				/*
				echo "Leverantör\t";
				echo "Artikel\t";
				echo "Antal\t";
				echo "Lagervärde\t";
				echo "Allokerat\t";
				echo "Räcker\t";
				echo "Sålda 30\t";
				echo "Sålda 60\t";	
				if ($showLastInvoiced)
					echo "Senast såld\t";
				echo "\n";		
				*/
			} else {
				echo "<table>";
				echo "<tr>";
				echo "<td width=\"150\"><b>Leverantör</b></td>";
				echo "<td width=\"550\"><b>Artikel</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Antal</b></td>";
				echo "<td width=\"100\" align=\"center\"><b>Lagervärde</b></td>";
				echo "<td width=\"100\" align=\"center\"><b>Allokerat</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Räcker</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
				if ($showLastInvoiced)
					echo "<td width=\"75\" align=\"center\"><b>Senast såld</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
				echo "</tr>";			
			}
			

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
						echo ($res ? pg_num_rows($res) : 0);
						exit;
						if ($export){ 
							$antalManad1 = $row[6];
							$antalManad2 = $row[7];
							$supplier = $row[11];
							if ($showLastInvoiced) {
								$lastInvoiced = substr($row[10], 0, 10) ;//date("YYYY-mm-dd", strtotime($row[10]));
							}
							if ($antalManad1 == null) {
								$antalManad1 = 0;
							}
							if ($antalManad2 == null) {
								$antalManad2 = 0;
							}
							if ($antalManad1 != 0) {
								$Takt = round((($row[1]/$antalManad1)*30),0);
							}
		
							if ($antalManad1 == 0 && $antalManad2 == 0) {
								$avvikelse = 999;
							} else {
								$avvikelse = round($antalManad1 / $antalManad2, 2);
							}
		
							$beskrivning = $row[4] . " " . $row[3];
							if (strlen($beskrivning) > 75) {
								$beskrivning = substr ($beskrivning, 0, 75) . "....";
							}
							if ($row[8] == "N") {
								$beskrivning .= " -<span class=\"notshowweb\"> VISAS EJ PÅ WEBBEN!</span>";
							}
							$SummaLager = round($row[2],0);
							$allokerat = number_format($row[5], 0, ',', ' ');
							
							// $contents .= utf8_encode("$supplier \t $beskrivning \t $row[1] \t $SummaLager \t $allokerat \t $Takt \t $antalManad1 \t $antalManad2 \t $lastInvoiced \n");
							$contents .= "1 \t 2 \t 3 \t 4 \t 5 \t 6 \t 7 \t 8 \t 9 \n";
							/*
							echo "$supplier\t";
							echo "$beskrivning\t";
							echo "$row[1]\t";
							echo "$SummaLager\t";
							echo "$allokerat\t";
							if ($antalManad1 == 0) {
								echo "9999\t";
							} else {
								echo "$Takt\t";
							}
							echo "$antalManad1\t";
							echo "$antalManad2\t";
							if ($showLastInvoiced)
								echo "$lastInvoiced\t";
							echo "\n";
							*/
		
						} else {
							$antalManad1 = $row[6];
							$antalManad2 = $row[7];
							if ($showLastInvoiced) {
								$lastInvoiced = substr($row[10], 0, 10) ;//date("YYYY-mm-dd", strtotime($row[10]));
							}
							if ($antalManad1 == null) {
								$antalManad1 = 0;
							}
							if ($antalManad2 == null) {
								$antalManad2 = 0;
							}
							if ($antalManad1 != 0) {
								$Takt = round((($row[1]/$antalManad1)*30),0);
							}
		
							if ($antalManad1 == 0 && $antalManad2 == 0) {
								$avvikelse = 999;
							} else {
								$avvikelse = round($antalManad1 / $antalManad2, 2);
							}
		
							if ($rowcolor == true) {
								$backcolor = "firstrow";
							} else {
								$backcolor = "secondrow";
							}
							
							$beskrivning = $row[4] . " " . $row[3];
							if (strlen($beskrivning) > 75) {
								$beskrivning = substr ($beskrivning, 0, 75) . "....";
							}
							if ($row[8] == "N") {
								$beskrivning .= " -<span class=\"notshowweb\"> VISAS EJ PÅ WEBBEN!</span>";
							}
							$totalvarde += $row[2];
							$totalallokerat+= $row[5];
							$SummaLager = number_format($row[2], 0, ',', ' ');
							$allokerat = number_format($row[5], 0, ',', ' ');
		
							echo "<tr>";
							echo "<td class=\"$backcolor\" align=\"left\">$row[11]</td>";
							echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row[0]\">$beskrivning</a></td>";
							echo "<td class=\"$backcolor\" align=\"center\">$row[1]</td>";
							echo "<td class=\"$backcolor\" align=\"right\">$SummaLager SEK&nbsp;</td>";
							echo "<td class=\"$backcolor\" align=\"right\">$allokerat &nbsp;</td>";
							if ($antalManad1 == 0) {
								echo "<td class=\"$backcolor\" align=\"center\"><span class=\"lagerred\">&#8734;</span></td>";
							} else {
								if ($Takt < 15) {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagergreen\">$Takt dagar&nbsp;</span></td>";
								} elseif ($Takt > 14 && $Takt < 31)  {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerblack\">$Takt dagar&nbsp;</span></td>";
								} elseif ($Takt > 30 && $Takt < 100)  {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerred\">$Takt dagar&nbsp;</span></td>";
								} else {
									echo "<td class=\"$backcolor\" align=\"right\"><span class=\"lagerredbold\">$Takt dagar&nbsp;</span></td>";
								}
							}
							echo "<td class=\"$backcolor\" align=\"center\">$antalManad1</td>";
							echo "<td class=\"$backcolor\" align=\"center\">$antalManad2</td>";
							if ($showLastInvoiced) 
								echo "<td class=\"$backcolor\" align=\"center\">$lastInvoiced</td>";
							echo $this->displayTrend($avvikelse);
							echo "</tr>";
		
							if ($rowcolor == true) {
								$row = true;
								$rowcolor = false;
							} else {
								$row = false;
								$rowcolor = true;
							}						
						}
					}

				}
			if (!$export) {
				$totalvarde = number_format($totalvarde, 0, ',', ' ');
				$totalallokerat = number_format($totalallokerat, 0, ',', ' ');
				echo "<tr>";
				echo "<td align=\"left\"><b>Totalt:</b></td>";
				echo "<td align=\"right\"><b></b></td>";
				echo "<td align=\"right\"><b>$totalvarde SEK&nbsp;</b></td>";
				echo "<td align=\"right\"><b>$totalallokerat SEK&nbsp;</b></td>";
				echo "</tr>";
				echo "</table>";	
			} else {
				header('Content-type: application/ms-excel');
				header("Content-Disposition: attachment; filename=$settitle.xls");
				echo $contents;
			}
			

	}

	function exportCategoryToExcel($showLastInvoiced=false) {
		
			include_once 'CCategories.php';
			
			$setdate = date("YmdHi");
			$settitle = "ALL_" . $setdate;
			if ($showLastInvoiced) {
				$contents = "Leverantör\tTillverkare\tBenämning\tKategori\tArtikel\tAntal\tLagervärde\tAllokerat\tRäcker\tSålda 30\tSålda 60\tHuvudkategori\tSenast såld\n";
			} else {
				$contents = "Leverantör\tTillverkare\tBenämning\tKategori\tArtikel\tAntal\tLagervärde\tAllokerat\tRäcker\tSålda 30\tSålda 60\tHuvudkategori\n";
			}

			$select = "SELECT prod.value AS artnr, pstock.qtyonhand, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, cat.name AS kategori, manu.name AS tillverkare, cbp.name AS leverantor, ";
			// $select .= "SUM(pstock.qtyreserved*po.currentcostprice) AS allokerat, po.currentcostprice, stat.qtymonth, stat.qty2month, pstock.qtyreserved ";
			$select .= "po.currentcostprice, stat.qtymonth, stat.qty2month, pstock.qtyreserved, prod.name AS prodnamn, cat.value AS catname ";
			if ($showLastInvoiced) {
				$select .= ", (SELECT MAX(i.dateinvoiced) FROM C_Invoice i, C_InvoiceLine il WHERE i.issotrx='Y' AND i.docstatus IN ('CO', 'CL') AND  i.c_invoice_id = il.c_invoice_id AND il.m_product_id = prod.m_product_id) AS last_invoiced "; 
			}
			$select .= "FROM m_product_cache pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_category cat ON cat.m_product_category_id = prod.m_product_category_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
			$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id  "; // ss
			$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id  ";// ss
			$select .= "LEFT JOIN xc_product_statistics stat ON stat.m_product_id = pstock.m_product_id AND (stat.c_country_id is null OR stat.c_country_id = 0) ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 ";
			$select .= "AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' AND prod_po.iscurrentvendor = 'Y' ";
			// $select .= "AND cat.value = '395' ";
			if ($showLastInvoiced) {
				$select .= "GROUP BY pstock.qtyonhand, prod.value, cat.name, manu.name, cbp.name, po.currentcostprice, stat.qtymonth, stat.qty2month, pstock.qtyreserved, prod.name, cat.value, last_invoiced ";
			} else {
				$select .= "GROUP BY pstock.qtyonhand, prod.value, cat.name, manu.name, cbp.name, po.currentcostprice, stat.qtymonth, stat.qty2month, pstock.qtyreserved, prod.name, cat.value ";
			}
			$select .= "ORDER BY storevalue DESC ";
			// $select .= "LIMIT 5 ";
			// echo $select;exit;
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

				while ($res && $row = pg_fetch_object($res)) {
					$summalager = (int) round($row->storevalue,0);
					// $allokerat = (int) round($row->allokerat,0);
					$antalManad1 = $row->qtymonth;
					$antalManad2 = $row->qty2month;
					if ($antalManad1 == null) {
						$antalManad1 = 0;
					}
					if ($antalManad2 == null) {
						$antalManad2 = 0;
					}
					if ($antalManad1 != 0) {
						$takt = round((($row->qtyonhand/$antalManad1)*30),0);
					} else {
						$takt = "";
					}
					if ($row->qtyreserved > $row->qtyonhand) {
						$allokerat_antal = $row->qtyonhand;
					} else {
						$allokerat_antal = $row->qtyreserved;
					}
					// $allokerat = number_format(($row->rowcurrentcostprice*$allokerat_antal), 0, ',', ' ');
					$allokerat = (int) round($row->currentcostprice*$allokerat_antal,0);
					$main_category_id = CCategories::getMainCategory($row->catname);
					if ($main_category_id == 583) {
						$main_category = "Foto - Video";
					} elseif ($main_category_id == 584) {
						$main_category = "Outdoor";
					} elseif ($main_category_id == 585) {
						$main_category = "Mobiltelefoni";
					} elseif ($main_category_id == 1000082) {
						$main_category = "Hushåll";
					} elseif ($main_category_id == 1000147) {
						$main_category = "Cybairgun";
					} else {
						$main_category = "Foto - Video";
					}
					// $main_category = $row->m_product_category_id;

					if ($showLastInvoiced) {
						// $lastInvoiced = substr($row->last_invoiced, 0, 10);
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
							// $lastInvoiced = "1970-01-01";
							$lastInvoiced = "hej";
						} elseif ($row->last_invoiced == null) {
							$lastInvoiced = "1970-01-01";
						} elseif ($row->last_invoiced == "") {
							$lastInvoiced = "1970-01-01";
						} else {
							$lastInvoiced = date("Y-m-d", strtotime($row->last_invoiced));
						}
						/*
						if ($row->last_invoiced == NULL) {
							$lastInvoiced = "2000-01-01";
						} else {
							$lastInvoiced = date("Y-m-d", strtotime($row->last_invoiced));
						}
						*/
						// $lastInvoiced = 2015;
						// $contents .= utf8_encode("$row->leverantor\t$row->tillverkare\t$row->prodnamn\t$row->kategori\t$row->artnr\t$row->qtyonhand\t$summalager\t$allokerat\t$takt\t$antalManad1\t$antalManad2\t$main_category\t$lastInvoiced\n");
						$contents .= "$row->leverantor\t$row->tillverkare\t$row->prodnamn\t$row->kategori\t$row->artnr\t$row->qtyonhand\t$summalager\t$allokerat\t$takt\t$antalManad1\t$antalManad2\t$main_category\t$lastInvoiced\n";
					} else {
						// $contents .= utf8_encode("$row->leverantor\t$row->tillverkare\t$row->prodnamn\t$row->kategori\t$row->artnr\t$row->qtyonhand\t$summalager\t$allokerat\t$takt\t$antalManad1\t$antalManad2\t$main_category\n");
						$contents .= "$row->leverantor\t$row->tillverkare\t$row->prodnamn\t$row->kategori\t$row->artnr\t$row->qtyonhand\t$summalager\t$allokerat\t$takt\t$antalManad1\t$antalManad2\t$main_category\n";
					}
				}

			header('Content-type: application/ms-excel');
			header("Content-Disposition: attachment; filename=$settitle.xls");
			echo $contents;

	}
	
	function displayTrend($avvikelse) {
		
		if ($avvikelse == 999) {
			return "<td align=\"center\"><img border=\"0\" src=\"ring_blink.gif\"></td>";
		} elseif ($avvikelse > 0.55) {
			return "<td align=\"center\"><img border=\"0\" src=\"arrow_green.gif\"></td>";
		} elseif ($avvikelse < 0.45) {
			return "<td align=\"center\"><img border=\"0\" src=\"arrow_red.gif\"></td>";
		} else {
			return "<td align=\"center\"><img border=\"0\" src=\"arrow_grey.gif\"></td>";
		}

	}

	function displayQueueList($artnr) {

		$countrow = 1;
		echo "\n<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td width=\"20\">&nbsp;</td>\n";
		echo "\t\t<td><b>Skapad</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Order nr</b></td>\n";
		echo "\t\t<td width=\"30\">&nbsp;</td>\n";
		echo "\t\t<td><b>Namn</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Best</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Allok</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Prioritet</b></td>\n";
		echo "\t\t<td>&nbsp;</td>\n";
		echo "\t\t<td width=\"20\">&nbsp;</td>\n";
		echo "\t\t<td><b>Låst pga</b></td>\n";
		echo "\t\t<td><b>Låst på säljare</b></td>\n";
		echo "\t</tr>\n";
			
			$select = "SELECT col.created, o.documentno, bp.name, col.qtyordered, col.qtyallocated, col.description, xc.name, us.name, loc.c_country_id, o.priorityrule ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "JOIN c_bpartner_location bpl ON bpl.c_bpartner_location_id = o.c_bpartner_location_id ";
			$select .= "JOIN c_location loc ON loc.c_location_id = bpl.c_location_id ";
			$select .= "JOIN c_country con ON con.c_country_id = loc.c_country_id ";
			$select .= "LEFT JOIN xc_sales_order_status xc ON xc.xc_sales_order_status_id = o.xc_sales_order_status_id ";
			$select .= "LEFT JOIN AD_User us ON us.AD_User_ID = o.locked_to_id ";
			$select .= "WHERE o.c_doctype_id = 1000030 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered AND p.value = '$artnr' ";
			$select .= "AND NOT c_doctypetarget_id IN (1000027) ";
			$select .= "ORDER BY o.priorityrule ASC, col.created ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
						
						$row = $row;
				
						echo "\t<tr onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='normal'\">\n";
						echo "\t\t<td align=\"left\">$countrow</td>\n";
						echo "\t\t<td>" . date("Y-m-d H:i",strtotime($row[0])) . "</td>\n";
						echo "\t\t<td align=\"center\"><a style=\"text-decoration: none;\" href=\"order_info.php?order=" . $row[1] . "&artnr=$artnr\">$row[1]</a></td>\n";
						if ($row[8] == 181 || $row[8] == 50000) {
							echo "\t\t<td align=\"center\"><img border=\"\" src=\"fi_mini.jpg\"></td>\n";
						} elseif ($row[8] == 167) {
							echo "\t\t<td align=\"center\"><img border=\"\" src=\"dk_mini.jpg\"></td>\n";
						} elseif ($row[8] == 269) {
							echo "\t\t<td align=\"center\"><img border=\"\" src=\"no_mini.jpg\"></td>\n";
						} else {
							echo "\t\t<td align=\"center\"><img border=\"\" src=\"sv_mini.jpg\"></td>\n";
						}
						if ($row[5] != "") {
							echo "\t\t<td>$row[2] <b>($row[5])</b></td>\n";
						} else {
							echo "\t\t<td>$row[2]</td>\n";
						}
						echo "\t\t<td align=\"center\">$row[3]</td>\n";
						echo "\t\t<td align=\"center\">$row[4]</td>\n";
						if ($row[9] == 1) {
							echo "\t\t<td align=\"center\"><img title=\"HÖGSTA\" border=\"0\" src=\"/img/priority_1.jpg\"></td>\n";
						} elseif ($row[9] == 3) {
							echo "\t\t<td align=\"center\"><img title=\"HÖG\" border=\"0\" src=\"/img/priority_3.jpg\"></td>\n";
						} else {
							echo "\t\t<td align=\"center\">&nbsp;</td>\n";
						}
						if ($row[3] == $row[4]) {
							echo "\t\t<td align=\"center\"><i>Väntar på att skickas</i></td>\n";
						} else {
							echo "\t\t<td align=\"center\">&nbsp;</td>\n";
						}
						if ($row[6] != "" || $row[7] != "") {
							echo "\t\t<td align=\"center\"><img border=\"0\" src=\"/img/lock.png\"></td>\n";
						} else {
							echo "\t\t<td align=\"left\">&nbsp;</td>\n";
						}
						if ($row[6] != "") {
							echo "\t\t<td align=\"left\">$row[6]</td>\n";
						} else {
							echo "\t\t<td align=\"center\">&nbsp;</td>\n";
						}
						if ($row[7] != "") {
							echo "\t\t<td align=\"left\">$row[7]</td>\n";
						} else {
							echo "\t\t<td align=\"left\">&nbsp;</td>\n";
						}
						echo "\t</tr>\n";
						
						$countrow++;

					}
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Ingen kö på denna produkt</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "</table>\n";
	}

	function displayPurchaseList($artnr) {

		$countrow = 1;
		echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td width=\"25\">&nbsp;</td>\n";
		echo "\t\t<td width=\"75\"><b>Skapad</b></td>\n";
		echo "\t\t<td width=\"60\"><b>Order nr</b></td>\n";
		echo "\t\t<td><b>Namn</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Antal</b></td>\n";
		echo "\t\t<td align=\"center\"><b>Lev</b></td>\n";
		echo "\t\t<td align=\"left\"><b>Datum</b></td>\n";
		echo "\t\t<td align=\"left\"><b>Notering</b></td>\n";
		echo "\t</tr>\n";
			
			$select = "SELECT o.created, o.documentno, bp.name, col.qtyordered, col.qtydelivered, col.datepromisedprecision, col.datepromised, col.description ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered AND p.value = '$artnr' ";
			$select .= "ORDER BY o.created ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
				
						echo "\t<tr>";
						echo "\t\t<td>$countrow</td>\n";
						// echo "\t\t<td>" . date("Y-m-d H:i",strtotime($row[0])) . "</td>\n";
						echo "\t\t<td>" . date("Y-m-d",strtotime($row[0])) . "</td>\n";
						echo "\t\t<td>$row[1]</td>\n";
						echo "\t\t<td>$row[2]</td>\n";
						echo "\t\t<td align=\"center\">$row[3]</td>\n";
						echo "\t\t<td align=\"center\">$row[4]</td>\n";
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							echo "\t\t<td align=\"left\">" . $this->showDeliveryDate($row[6], $row[5], false, true) . "</td>\n";
						} else {
							echo "\t\t<td align=\"left\">" . $this->showDeliveryDate($row[6], $row[5], false, true) . "</td>\n";
							// echo "\t\t<td align=\"center\">$row[5]</td>\n";
						}
						echo "\t\t<td align=\"left\">$row[7]</td>\n";
						echo "\t</tr>\n";
						
						$countrow++;

					}
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Ingen kö på denna produkt</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "</table>\n";
	}

	function showDeliveryDate($dat, $prec, $fi, $sv) {
		global $bestallningsgrans, $mobilsite, $pricesite;
		
		if ($dat != "")
			$dat = substr($dat, 0, 10);
		
		$timestmp = strtotime($dat);		
		if ($prec == "D") { // exakt datum
			if ($fi) {								
				return date("d-m-Y", $timestmp);
			} else {
				return date("Y-m-d", $timestmp);
			}
			return $dat;
		} elseif ($prec == "W") {	// visas som vecka
			if ($fi && !$sv)
				return "Oletettu saapumisaika viikko " .  strtolower (date("W", strtotime($dat)));		
			else 	
				return "Beräknas in vecka " .  strtolower (date("W", strtotime($dat)));		
				
		} elseif ($prec == "P") {	// del av månad
			$day = date("j", strtotime($dat));
			$month = date("n", strtotime($dat));

			if ($day > 0 && $day <= 10 ) {
				if ($fi && !$sv)
					return "Oletettu saapumisaika " . $this->getMonthFi($month) . " alussa";
				else 
					return "Beräknas in i början av " . $this->getMonthSv($month);
					
			} elseif ($day > 10 && $day <= 20) {
				if ($fi && !$sv)
					return "Oletettu saapumisaika " . $this->getMonthFi($month) . " puolivälissä";		
				else 
					return "Beräknas in i mitten av " . $this->getMonthSv($month);
			} elseif ($day > 20) {
				if ($fi && !$sv)
					return "Oletettu saapumisaika " . $this->getMonthFi($month) . " loppupuolella";
				else
					return "Beräknas in i slutet av " . $this->getMonthSv($month);
					
			} else { // tja, när är den något annat? Tomt blir nog bra
				return "";
			}
			
		} elseif ($prec == "M") { // månad
			$month = date("n", strtotime($dat));
			if ($fi && !$sv)
				return "Oletettu saapumisaika " . $this->getMonthFi($month);		
			else 
				return "Beräknas in i " . $this->getMonthSv($month);

		} elseif ($prec == "U") { // detta om det är ett okänt leveransbesked
			if ($fi && !$sv)
				return "<a onMouseOver=\"return escape('Tuote on tilattu mutta toimitusaika ei ole tiedossa. Emme ole saaneet tilausvahvistusta toimittajalta')\" style=\"text-decoration: none\"> toimituspäivämäärä ei ole määritelty</a>";
			else 
				if ($mobilsite || $pricesite) {
					return "leveransdatum okänt";
				} else {
					// return "<a onMouseOver=\"return escape('Leveransdatum okänt innebär att vår leverantör i dagsläget inte har något beräknat leveransdatum för när de får in varan till sig. Så fort vi får ett nytt leveransbesked uppdateras detta.')\" style=\"text-decoration: underline\"> leveransdatum okänt</a>";
					return "leveransdatum okänt";
				}

		} elseif ($prec == "QU" && $bestallningsgrans != 0) { // om det inte finns tillräckligt med beställda antal för att täck de som finns på kö
			if ($fi && !$sv)
				return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilapäisesti lopussa. <br>Normaali toimitusaika on  <b>$lev_datum_norm_fi </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilapäisesti loppu</font></a>";
			else 
				if ($mobilsite || $pricesite) {
					return "tillfälligt slut";
				} else {
					return "tillfälligt slut";
					// return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> tillfälligt slut</font></a>";
				}

		} elseif ($bestallningsgrans == 0)  {
			if (!$sv) // TODO: finska nästa rad
				return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
			else
				if ($mobilsite || $pricesite) {
					return "beställningsvara";
				} else {
					return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> beställningsvara</></font></a>";
				}
				
		} else {
			return $dat;
		}
		
	}

	function getMonthFi($month) {
		if ($month == 1)
			return "tammikuu";
		elseif ($month == 2)
			return "helmikuu";
		elseif ($month == 3)
			return "maaliskuu";
		elseif ($month == 4)
			return "huhtikuu";
		elseif ($month == 5)
			return "toukokuu";
		elseif ($month == 6)
			return "kesäkuu";
		elseif ($month == 7)
			return "heinäkuu";
		elseif ($month == 8)
			return "elokuu";
		elseif ($month == 9)
			return "syyskuu";
		elseif ($month == 10)
			return "lokakuu";
		elseif ($month == 11)
			return "marraskuu";
		elseif ($month == 12)
			return "joulukuu";
		else 
			return "";
	}
	function getMonthSv($month) {
		if ($month == 1)
			return "januari";
		elseif ($month == 2)
			return "februari";
		elseif ($month == 3)
			return "mars";
		elseif ($month == 4)
			return "april";
		elseif ($month == 5)
			return "maj";
		elseif ($month == 6)
			return "juni";
		elseif ($month == 7)
			return "juli";
		elseif ($month == 8)
			return "augusti";
		elseif ($month == 9)
			return "september";
		elseif ($month == 10)
			return "oktober";
		elseif ($month == 11)
			return "november";
		elseif ($month == 12)
			return "december";
		else 
			return "";		
	}

	function displayDeliveryList($artnr,$displaypac) {
	

			
		if ($displaypac) {
			$countrow = 0;
		}
		echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\">\n";
		if ($displaypac) {
			echo "\t<tr>\n";
			echo "\t\t<td width=\"125\"><b>Orderdatum</b></td>\n";
			echo "\t\t<td align=\"center\" width=\"80\"><b>Order nr</b></td>\n";
			echo "\t\t<td align=\"center\" width=\"30\">&nbsp;</td>\n";
			echo "\t\t<td><b>Namn</b></td>\n";
			echo "\t\t<td width=\"125\">&nbsp;</td>\n";
			echo "\t</tr>\n";
		} else {
			echo "\t<tr>\n";
			// echo "\t\t<td width=\"25\">&nbsp;</td>\n";
			echo "\t\t<td width=\"125\"><b>Skickad</b></td>\n";
			echo "\t\t<td align=\"center\" width=\"80\"><b>Order nr</b></td>\n";
			echo "\t\t<td align=\"center\" width=\"30\">&nbsp;</td>\n";
			echo "\t\t<td width=\"300\"><b>Namn</b></td>\n";
			echo "\t\t<td align=\"center\"><b>Best</b></td>\n";
			echo "\t\t<td align=\"center\"><b>Lev</b></td>\n";
			echo "\t\t<td width=\"125\">&nbsp;</td>\n";
			echo "\t</tr>\n";
		}
			
			// $select = "SELECT o.updated, o.documentno, bp.name, col.qtyordered, col.qtydelivered, col.description ";
			if ($displaypac) {
				$select = "SELECT DISTINCT o.created, o.documentno, bp.name, loc.c_country_id ";
			} else {
				$select = "SELECT col.datedelivered, o.documentno, bp.name, col.qtyordered, col.qtydelivered, col.description, loc.c_country_id ";
			}
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "JOIN c_bpartner_location bpl ON bpl.c_bpartner_location_id = o.c_bpartner_location_id ";
			$select .= "JOIN c_location loc ON loc.c_location_id = bpl.c_location_id ";
			$select .= "JOIN c_country con ON con.c_country_id = loc.c_country_id ";
			if ($displaypac) {
				$select .= "WHERE o.c_doctype_id = 1000030 AND o.docstatus IN ('CO') AND col.qtyordered = col.qtydelivered AND col.qtyordered > 0 AND col.packey = '$artnr' ";
				$select .= "ORDER BY o.created DESC ";
			} else {
				$select .= "WHERE o.c_doctype_id = 1000030 AND o.docstatus IN ('CO') AND col.qtyordered = col.qtydelivered AND col.qtyordered > 0 AND p.value = '$artnr' ";
				$select .= "ORDER BY col.datedelivered DESC ";
			}
			
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				// exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
						$row = $row;
						echo "\t<tr>";
						// echo "\t\t<td>$countrow</td>\n";
						// echo "\t\t<td>" . date("Y-m-d H:i",strtotime($row[0])) . "</td>\n";
						if ($displaypac) {
							echo "\t\t<td>" . date("Y-m-d",strtotime($row[0])) . "</td>\n";
							echo "\t\t<td align=\"center\"><a style=\"text-decoration: none;\" href=\"order_info.php?order=" . $row[1] . "&artnr=$artnr&salda=yes&show_salesbundle=yes\">$row[1]</a></td>\n";
							if ($row[3] == 181 || $row[3] == 50000) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"fi_mini.jpg\"></td>\n";
							} elseif ($row[3] == 167) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"dk_mini.jpg\"></td>\n";
							} elseif ($row[3] == 269) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"no_mini.jpg\"></td>\n";
							} else {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"sv_mini.jpg\"></td>\n";
							}
							if ($row[5] != "") {
								echo "\t\t<td>$row[2] <b>($row[5])</b></td>\n";
							} else {
								echo "\t\t<td>$row[2]</td>\n";
							}
							echo "\t\t<td align=\"center\">Skickad</td>\n";
						} else {
							echo "\t\t<td>" . date("Y-m-d",strtotime($row[0])) . "</td>\n";
							echo "\t\t<td align=\"center\"><a style=\"text-decoration: none;\" href=\"order_info.php?order=" . $row[1] . "&artnr=$artnr&salda=yes\">$row[1]</a></td>\n";
							if ($row[6] == 181 || $row[6] == 50000) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"fi_mini.jpg\"></td>\n";
							} elseif ($row[6] == 167) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"dk_mini.jpg\"></td>\n";
							} elseif ($row[6] == 269) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"no_mini.jpg\"></td>\n";
							} else {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"sv_mini.jpg\"></td>\n";
							}
							if ($row[5] != "") {
								echo "\t\t<td>$row[2] <b>($row[5])</b></td>\n";
							} else {
								echo "\t\t<td>$row[2]</td>\n";
							}
							echo "\t\t<td align=\"center\">$row[3]</td>\n";
							echo "\t\t<td align=\"center\">$row[4]</td>\n";
							echo "\t\t<td align=\"center\">Skickad</td>\n";
						}
						echo "\t</tr>\n";
						
						if ($displaypac) {
							$countrow++;
						}

					}
					
				} else {
				
						echo "\t<tr>\n";
						// echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Ingen leverans på denna produkt</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "</table>\n";
		
		if ($displaypac && $countrow > 0) {
			echo "<p>$countrow st ordrar sålda med värdepaket</p>\n";
		}
	}

	function displayNegativeProducts() {

			$countrows = 0;
			$current_catcount = 0;
			$rowcolor = true;

			// $select = "SELECT prod.value, m.name, prod.name, price.updated, aduser.value, price.pricelist, price.pricelimit, cat5.name, cat4.name, cat3.name, cat2.name, cat.name ";
			$select = "SELECT prod.value, m.name, prod.name, price.updated, aduser.value, price.pricelist, price.pricelimit, array_to_string(ARRAY[cat5.name, cat4.name, cat3.name, cat2.name, cat.name], '/') AS catgroup, u.value ";
			$select .= "FROM m_productprice price ";
			$select .= "JOIN m_product prod ON prod.m_product_id = price.m_product_id ";
			$select .= "JOIN xc_manufacturer m ON m.xc_manufacturer_id = prod.xc_manufacturer_id ";
			$select .= "JOIN ad_user aduser ON aduser.ad_user_id = price.updatedby ";
			$select .= "JOIN m_product_category cat ON cat.m_product_category_id = prod.m_product_category_id ";
			$select .= "LEFT JOIN m_product_category cat2 ON cat2.m_product_category_id = cat.m_product_category_parent_id ";
			$select .= "LEFT JOIN m_product_category cat3 ON cat3.m_product_category_id = cat2.m_product_category_parent_id ";
			$select .= "LEFT JOIN m_product_category cat4 ON cat4.m_product_category_id = cat3.m_product_category_parent_id ";
			$select .= "LEFT JOIN m_product_category cat5 ON cat5.m_product_category_id = cat4.m_product_category_parent_id ";
			$select .= "LEFT JOIN ad_user u ON cat.salesrep_id = u.ad_user_id ";
			$select .= "WHERE price.m_pricelist_version_id = 1000000 AND (price.pricelimit > price.pricelist) AND prod.discontinued = 'N' AND prod.demo_product = 'N' AND prod.IsSelfService = 'Y' ";
			$select .= "AND prod.m_product_category_id != 1000441 ";
			// $select .= "ORDER BY cat5.name ASC, cat4.name ASC, cat3.name ASC, cat2.name ASC, cat.name ASC, m.name ASC, price.updated DESC ";
			$select .= "ORDER BY catgroup ASC, cat.name ASC, m.name ASC, price.updated DESC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			echo "<table>\n";
			echo "<tr>\n";
			echo "<td width=\"130\"><b>Artikel nr</b></td>\n";
			echo "<td width=\"470\" align=\"left\"><b>Benämning</b></td>\n";
			echo "<td width=\"150\" align=\"center\"><b>Uppdaterad</b></td>\n";
			echo "<td width=\"50\" align=\"center\"><b>Av</b></td>\n";
			echo "<td width=\"70\" align=\"center\"><b>Prislista</b></td>\n";
			echo "<td width=\"70\" align=\"center\"><b>Limitpris</b></td>\n";
			echo "<td width=\"70\" align=\"center\"><b>Differens</b></td>\n";
			echo "</tr>";

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						unset($catname);
					
						/*
						if ($row[7] != "") {
							$catname .= $row[7] . "/";
						}
						if ($row[8] != "") {
							$catname .= $row[8] . "/";
						}
						if ($row[9] != "") {
							$catname .= $row[9] . "/";
						}
						if ($row[10] != "") {
							$catname .= $row[10] . "/";
						}
						*/
						$catname .= $row[7];
							
						// $catname = $row[7] . " / " . $row[8];
						
						if ($catname != $current_cat) {
							if ($current_catcount != 0) {
								echo "<tr>\n";
								echo "<td colspan=\"7\" align=\"left\">&nbsp;</td>\n";
								echo "</tr>\n";
								$current_catcount = 0;
								$rowcolor = true;
							}
							echo "<tr>\n";
							echo "<td colspan=\"7\" align=\"left\"><b>$catname (ansvarig: " . strtoupper($row[8]) . ")</b></td>\n";
							echo "</tr>\n";
						}
						$current_cat = $catname;

						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						
						$beskrivning = $row[1] . " " . $row[2];
						if (strlen($beskrivning) > 70) {
							$beskrivning = substr ($beskrivning, 0, 70) . "....";
						}

						echo "<tr>";
						echo "<td class=\"$backcolor\">$row[0]</td>\n";
						echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row[0]\">$beskrivning</a></td>\n";
						echo "<td class=\"$backcolor\" align=\"center\">$row[3]</td>\n";
						echo "<td class=\"$backcolor\" align=\"center\">" . strtoupper($row[4]) . "</td>\n";
						echo "<td class=\"$backcolor\" align=\"center\">" . round($row[5],0) . "</td>\n";
						echo "<td class=\"$backcolor\" align=\"center\">" . round($row[6],0) . "</td>\n";
						echo "<td class=\"$backcolor\" align=\"center\">" . round($row[5]-$row[6],0) . "</td>\n";
						echo "</tr>";

						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
						$countrows++;
						$current_catcount++;
						
					}

				}

			echo "</table>\n";
			echo "<p>&nbsp;<b>$countrows st artiklar</b></p>\n";

	}
	
	function displaySupliers() {
		global $detail;
		
		$countrows = 0;
		$rowcolor = true;
		
		$select  = "SELECT DISTINCT bp.name, bp.url, Ad_User.name, bp.value, bp.UserNameVendor, bp.PasswordVendor, bp.vendorproutines, bp.ReferenceNo ";
		$select .= "FROM c_bpartner bp ";
		$select .= "JOIN c_bpartner_location ON bp.c_bpartner_id = c_bpartner_location.c_bpartner_id ";
		$select .= "JOIN Ad_User ON Ad_User.Ad_User_id = bp.salesrep_id ";
		$select .= "WHERE bp.isactive = 'Y' AND bp.isVendor = 'Y' ";
		$select .= "ORDER BY bp.name ASC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_object($res);

			if ($res && pg_num_rows($res) > 0) {
			
				echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"1250\">\n";
				echo "<tr>\n";
				echo "<th>Kundnummer</th>\n";
				echo "<th>Namn</th>\n";
				echo "<th>URL</th>\n";
				if (CCheckIP::checkIfPurchaseValid()) {
					echo "<th>Användarnamn</th>\n";
					echo "<th>Lösenord</th>\n";
				}
				echo "<th></th>\n";
				echo "</tr>\n";
			
				while ($res && $row = pg_fetch_row($res)) {
				
					if ($rowcolor == true) {
						$backcolor = "firstrow";
					} else {
						$backcolor = "secondrow";
					}
					
					echo "<tr>\n";
					echo "<td class=\"$backcolor\"><a style=\"text-decoration: none\" name=\"$row[3]\">" . $row[3] . "</a></td>\n";
					echo "<td class=\"$backcolor\">" . $row[0] . "</td>\n";
					echo "<td class=\"$backcolor\">" . $row[1] . "</td>\n";
					if (CCheckIP::checkIfPurchaseValid()) {
						echo "<td class=\"$backcolor\">" . $row[4] . "</td>\n";
						echo "<td class=\"$backcolor\">" . $row[5] . "</td>\n";
					}
					if ($row[6] != "") {
						echo "<td><a href=\"?detail=$row[3]#$row[3]\"><img border=\"0\" src=\"plus.jpg\"></a></td>\n";
					} else {
						echo "<td></td>\n";
					}
					echo "</tr>\n";
					if ($detail == $row[3]) {
						echo "<td colspan=\"5\" class=\"tdsuplier\">" . $row[6] . "<br><br>Inköpare: <b>" . $row[2] . "</b><br><br></td>\n";
					}

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
					$countrows++;
				 
				}

				echo "</table>\n";
				
			} else {
			
				return;
			
			}
			
			echo "<p>Totalt <b>$countrows st</b> leverantörer</p>\n";
	}

	function goodsExpectation() {
		global $dagensdatum, $firstinput, $only_today, $not_only_today, $economy;

		$countrow = 1;
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		// $dagensdatum = date("Y-m-d", time());
		$look_forward = date('Y-m-d', strtotime("$dagensdatum +3 day"));
		$look_backward = date('Y-m-d', strtotime("$dagensdatum -3 day"));
		$totalvikt = 0;
		$antaltotal = 0;
		$totsumma = 0;

		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td width=\"25\">&nbsp;</td>\n";
		echo "\t\t<td width=\"200\"><b>Leverantör</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Antal</b></td>\n";
		if ($economy == "yes") {
			echo "\t\t<td width=\"75\" align=\"center\"><b>Nettosumma</b></td>\n";
		}
		echo "\t\t<td width=\"75\" align=\"center\"><b>Nettovikt</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Inköpare</b></td>\n";
		echo "\t</tr>\n";
			/*
			$select = "SELECT bp.name, SUM(col.qtyordered - col.qtydelivered) AS totantal, SUM((col.qtyordered - col.qtydelivered) * p.weight_net) AS totvikt, bp.value, au.value ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "JOIN Ad_User au ON au.Ad_User_id = bp.salesrep_id ";
			$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered ";
			$select .= "AND ((col.datepromised >= '$dagensdatum 00:00:00' AND col.datepromised <= '$dagensdatum 23:59:59') ";
			$select .= "OR (col.datepromised > current_date - integer '3' AND col.datepromised < '$dagensdatum 00:00:00') ";
			$select .= "OR (col.datepromised < current_date + integer '3' AND col.datepromised > '$dagensdatum 23:59:59' AND NOT col.datepromisedprecision = 'D')) ";
			$select .= "AND NOT col.datepromisedprecision = 'U' ";
			$select .= "GROUP BY bp.name, bp.value, au.value ";
			$select .= "ORDER BY bp.name ";
			*/
			if ($economy == "yes") {
				$select = "SELECT bp.name, SUM(col.qtyordered - col.qtydelivered) AS totantal, SUM((col.qtyordered - col.qtydelivered) * p.weight_net) AS totvikt, bp.value, au.value ";
				$select .= ", SUM((col.qtyordered - col.qtydelivered) * po.currentcostprice) AS totsumma ";
				$select .= "FROM c_orderline col ";
				$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
				$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
				$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
				$select .= "JOIN Ad_User au ON au.Ad_User_id = bp.salesrep_id ";
				$select .= "JOIN m_cost po ON po.m_product_id = p.m_product_id ";
				$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered ";
				if ($not_only_today == "yes") {
					$select .= "AND ( ";
					$select .= "(col.datepromised = '$dagensdatum') ";
					$select .= "OR (col.datepromised > '$look_backward' AND col.datepromised < '$dagensdatum') ";
					$select .= "OR (col.datepromised < '$look_forward' AND col.datepromised > '$dagensdatum' AND NOT col.datepromisedprecision = 'D') ";
					$select .= ") ";
				} else {
					$select .= "AND col.datepromised = '$dagensdatum' AND col.datepromisedprecision = 'D' ";
				}
				$select .= "AND NOT col.datepromisedprecision = 'U' ";
				$select .= "AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
				$select .= "GROUP BY bp.name, bp.value, au.value ";
				$select .= "ORDER BY bp.name ";
			} else {
				$select = "SELECT bp.name, SUM(col.qtyordered - col.qtydelivered) AS totantal, SUM((col.qtyordered - col.qtydelivered) * p.weight_net) AS totvikt, bp.value, au.value ";
				$select .= "FROM c_orderline col ";
				$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
				$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
				$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
				$select .= "JOIN Ad_User au ON au.Ad_User_id = bp.salesrep_id ";
				$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered ";
				if ($not_only_today == "yes") {
					$select .= "AND ( ";
					$select .= "(col.datepromised = '$dagensdatum') ";
					$select .= "OR (col.datepromised > '$look_backward' AND col.datepromised < '$dagensdatum') ";
					$select .= "OR (col.datepromised < '$look_forward' AND col.datepromised > '$dagensdatum' AND NOT col.datepromisedprecision = 'D') ";
					$select .= ") ";
				} else {
					$select .= "AND col.datepromised = '$dagensdatum' AND col.datepromisedprecision = 'D' ";
				}
				$select .= "AND NOT col.datepromisedprecision = 'U' ";
				$select .= "GROUP BY bp.name, bp.value, au.value ";
				$select .= "ORDER BY bp.name ";
			}
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {

						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
				
						echo "\t<tr>";
						echo "\t\t<td class=\"$backcolor\">$countrow</td>\n";
						// echo "\t\t<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?supID=$row[3]&firstinput=$firstinput&only_today=$only_today\">$row[0]</a></td>\n";
						echo "\t\t<td class=\"$backcolor\"><a href=\"#\" onclick=\"document.sampleform.supID.value='$row[3]'; document.sampleform.submit();\">$row[0]</a></td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . round($row[1],0) . "</td>\n";
						if ($economy == "yes") {
							echo "\t\t<td class=\"$backcolor\" align=\"right\">" . number_format($row[5], 0, ',', ' ') . " SEK</td>\n";
						}
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . round($row[2],2) . "</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . strtoupper($row[4]) . "</td>\n";
						echo "\t</tr>\n";
						
						$countrow++;
						$totalvikt = $totalvikt + $row[2];
						$antaltotal = $antaltotal + $row[1];
						$totsumma = $totsumma + $row[5];
						
						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}

					}
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Inga produkter beräknas in denna dag (period)</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\">&nbsp;</td>\n";
		echo "\t\t<td align=\"center\"><b>$antaltotal st</td>\n";
		if ($economy == "yes") {
			echo "\t\t<td align=\"right\"><b>" . number_format($totsumma, 0, ',', ' ') . " SEK</td>\n";
		}
		echo "\t\t<td align=\"center\"><b>" . round($totalvikt,2) . " kg</td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
	}

	function goodsExpectationDetail() {
		global $supID, $dagensdatum, $firstinput, $only_today, $not_only_today;

		$countrow = 1;
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		// $dagensdatum = date("Y-m-d", time());
		$look_forward = date('Y-m-d', strtotime("$dagensdatum +3 day"));
		$look_backward = date('Y-m-d', strtotime("$dagensdatum -3 day"));
		$totalvikt = 0;
		$antaltotal = 0;
		
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td width=\"25\">&nbsp;</td>\n";
		echo "\t\t<td width=\"200\"><b>Leverantör</b></td>\n";
		echo "\t\t<td width=\"150\"><b>Artikel</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Antal</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Nettovikt</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Ordernr</b></td>\n";
		echo "\t\t<td width=\"25\" align=\"center\"><b>&nbsp;</b></td>\n";
		echo "\t</tr>\n";
			
			/*
			$select = "SELECT bp.name, p.value, col.qtyordered, col.qtydelivered, p.weight_net, o.documentno, col.datepromised, col.datepromisedprecision ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered ";
			$select .= "AND ((col.datepromised >= '$dagensdatum 00:00:00' AND col.datepromised <= '$dagensdatum 23:59:59') ";
			$select .= "OR (col.datepromised > current_date - integer '3' AND col.datepromised < '$dagensdatum 00:00:00') ";
			$select .= "OR (col.datepromised < current_date + integer '3' AND col.datepromised > '$dagensdatum 23:59:59' AND NOT col.datepromisedprecision = 'D')) ";
			$select .= "AND NOT col.datepromisedprecision = 'U' AND bp.value = '$supID' ";
			$select .= "ORDER BY bp.name ASC ";
			*/
			
			$select = "SELECT bp.name, p.value, col.qtyordered, col.qtydelivered, p.weight_net, o.documentno, col.datepromised, col.datepromisedprecision ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered ";
			if ($not_only_today == "yes") {
				$select .= "AND ( ";
				$select .= "(col.datepromised = '$dagensdatum') ";
				$select .= "OR (col.datepromised > '$look_backward' AND col.datepromised < '$dagensdatum') ";
				$select .= "OR (col.datepromised < '$look_forward' AND col.datepromised > '$dagensdatum' AND NOT col.datepromisedprecision = 'D') ";
				$select .= ") ";
			} else {
				$select .= "AND col.datepromised = '$dagensdatum' AND col.datepromisedprecision = 'D' ";
			}
			$select .= "AND NOT col.datepromisedprecision = 'U' AND bp.value = '$supID' ";
			$select .= "ORDER BY bp.name ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {

						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
					
						$antalin = $row[2] - $row[3];
						$nettototal = $antalin * round($row[4],2);
						
						if ($row[7] == 'M') {
							$statuscolor = "#F26F0D";
						} elseif ($row[7] == 'P') {
							$statuscolor = "#FFCC00";
						} elseif ($row[7] == 'W') {
							$statuscolor = "#CCFF99";
						} else {
							$statuscolor = "#00FF00";
						}
						
						echo "\t<tr>";
						echo "\t\t<td class=\"$backcolor\">$countrow</td>\n";
						// echo "\t\t<td>" . date("Y-m-d H:i",strtotime($row[0])) . "</td>\n";
						// echo "\t\t<td>" . date("Y-m-d",strtotime($row[0])) . "</td>\n";
						echo "\t\t<td class=\"$backcolor\">$row[0]</td>\n";
						echo "\t\t<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row[1]\">$row[1]</a></td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . $antalin . "</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . $nettototal . "</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">$row[5]</td>\n";
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							// echo "\t\t<td align=\"left\">" . $this->showDeliveryDate($row[6], $row[7], false, true) . "</td>\n";
							echo "\t\t<td bgcolor=\"$statuscolor\"></td>\n";
						} else {
							// echo "\t\t<td align=\"left\">" . $this->showDeliveryDate($row[6], $row[7], false, true) . "</td>\n";
							// echo "\t\t<td align=\"center\">$row[5]</td>\n";
							echo "\t\t<td bgcolor=\"$statuscolor\"></td>\n";
						}
						echo "\t</tr>\n";
						
						$countrow++;
						$totalvikt = $totalvikt + $nettototal;
						$antaltotal = $antaltotal + $antalin;

						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
						
					}
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Ingen kö på denna produkt</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"3\">&nbsp;</td>\n";
		echo "\t\t<td align=\"center\"><b>$antaltotal st</td>\n";
		echo "\t\t<td align=\"center\"><b>$totalvikt kg</td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
	}

	function goodsExpectationDelay() {

		$countrow = 1;
		$dagensdatum = date("Y-m-d", strtotime('yesterday'));
		$totalvikt = 0;
		$antaltotal = 0;
		
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td width=\"25\">&nbsp;</td>\n";
		echo "\t\t<td width=\"200\"><b>Leverantör</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Antal</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Nettovikt</b></td>\n";
		// echo "\t\t<td width=\"75\" align=\"center\"><b>Inköpare</b></td>\n";
		echo "\t</tr>\n";
			
			$select = "SELECT bp.name, SUM(col.qtyordered - col.qtydelivered) AS totantal, SUM((col.qtyordered - col.qtydelivered) * p.weight_net) AS totvikt, bp.value, au.value ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "JOIN Ad_User au ON au.Ad_User_id = bp.salesrep_id ";
			$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered ";
			$select .= "AND col.datepromised < '$dagensdatum 23:59:59' ";
			$select .= "AND NOT col.datepromisedprecision = 'U' ";
			$select .= "GROUP BY bp.name, bp.value, au.value ";
			$select .= "ORDER BY au.value ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {

						if ($row[4] != $current_purchace) {
							if ($countrow > 1) {
								echo "<tr>\n";
								echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline\"></td>\n";
								echo "</tr>\n";
							}
						echo "<tr>\n";
						echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline\">Inköpare: " . strtoupper($row[4]) . "</td>\n";
						echo "</tr>\n";
						}
						$current_purchace = $row[4];
						
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
				
						echo "\t<tr>";
						echo "\t\t<td class=\"$backcolor\">$countrow</td>\n";
						echo "\t\t<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?supID=$row[3]\">$row[0]</a></td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . round($row[1],0) . "</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . round($row[2],2) . "</td>\n";
						// echo "\t\t<td class=\"$backcolor\" align=\"center\">" . strtoupper($row[4]) . "</td>\n";
						echo "\t</tr>\n";
						
						$countrow++;
						$totalvikt = $totalvikt + $row[2];
						$antaltotal = $antaltotal + $row[1];
						
						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}

					}
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Ingen kö på denna produkt</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\">&nbsp;</td>\n";
		echo "\t\t<td align=\"center\"><b>$antaltotal st</td>\n";
		echo "\t\t<td align=\"center\"><b>" . round($totalvikt,2) . " kg</td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
	}

	function goodsExpectationDelayDetail() {
		global $supID;

		$countrow = 1;
		$dagensdatum = date("Y-m-d", strtotime(yesterday));
		$totalvikt = 0;
		$antaltotal = 0;
		
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td width=\"25\">&nbsp;</td>\n";
		echo "\t\t<td width=\"200\"><b>Leverantör</b></td>\n";
		echo "\t\t<td width=\"150\"><b>Lev artikel nr</b></td>\n";
		echo "\t\t<td width=\"150\"><b>Vårt artikel nr</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Antal</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Nettovikt</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Ordernr</b></td>\n";
		echo "\t\t<td width=\"25\" align=\"center\"><b>&nbsp;</b></td>\n";
		echo "\t</tr>\n";
			
			$select = "SELECT bp.name, p.value, col.qtyordered, col.qtydelivered, p.weight_net, o.documentno, col.datepromised, col.datepromisedprecision, ppo.vendorproductno ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "JOIN m_product_po ppo ON p.m_product_id = ppo.m_product_id ";
			$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered ";
			$select .= "AND col.datepromised < '$dagensdatum 23:59:59' ";
			$select .= "AND NOT col.datepromisedprecision = 'U' AND ppo.iscurrentvendor = 'Y' AND bp.value = '$supID' ";
			$select .= "ORDER BY bp.name ASC, col.datepromised ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {

						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
					
						$antalin = $row[2] - $row[3];
						$nettototal = $antalin * round($row[4],2);
						$statuscolor = "#F26F0D";
						
						echo "\t<tr>";
						echo "\t\t<td class=\"$backcolor\">$countrow</td>\n";
						// echo "\t\t<td>" . date("Y-m-d H:i",strtotime($row[0])) . "</td>\n";
						// echo "\t\t<td>" . date("Y-m-d",strtotime($row[0])) . "</td>\n";
						echo "\t\t<td class=\"$backcolor\">$row[0]</td>\n";
						echo "\t\t<td class=\"$backcolor\">$row[8]</td>\n";
						echo "\t\t<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row[1]\">$row[1]</a></td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . $antalin . "</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . $nettototal . "</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">$row[5]</td>\n";
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							// echo "\t\t<td align=\"left\">" . $this->showDeliveryDate($row[6], $row[7], false, true) . "</td>\n";
							echo "\t\t<td bgcolor=\"$statuscolor\"></td>\n";
						} else {
							// echo "\t\t<td align=\"left\">" . $this->showDeliveryDate($row[6], $row[7], false, true) . "</td>\n";
							// echo "\t\t<td align=\"center\">$row[5]</td>\n";
							echo "\t\t<td bgcolor=\"$statuscolor\"></td>\n";
						}
						echo "\t</tr>\n";
						
						$countrow++;
						$totalvikt = $totalvikt + $nettototal;
						$antaltotal = $antaltotal + $antalin;

						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
						
					}
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Ingen kö på denna produkt</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"4\">&nbsp;</td>\n";
		echo "\t\t<td align=\"center\"><b>$antaltotal st</td>\n";
		echo "\t\t<td align=\"center\"><b>$totalvikt kg</td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
	}

	function purschasedGoodsWithNoCustomers() {

		$countrow = 1;
		$dagensdatum = date("Y-m-d", strtotime('yesterday'));
		$totalvikt = 0;
		$antaltotal = 0;
		
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		echo "\t<tr>\n";
		// echo "\t\t<td width=\"25\">&nbsp;</td>\n";
		echo "\t\t<td width=\"120\"><b>Artikel nr</b></td>\n";
		echo "\t\t<td width=\"150\"><b>Leverantör</b></td>\n";
		echo "\t\t<td width=\"500\"><b>Produkt</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Kostnad</b></td>\n";
		echo "\t</tr>\n";
			
			// $select = "SELECT prod.value AS artnr, manu.name AS tillverkare, cbp.name AS leverantor, au.value as inkopare, prod.name AS beskrivning, po.currentcostprice AS productprice ";
			$select = "SELECT prod.value AS artnr, manu.name AS tillverkare, cbp.name AS leverantor, au.value as inkopare, prod.name AS beskrivning, price.pricelimit AS productprice ";
			$select .= "FROM m_product_cache pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_productprice price ON price.m_product_id = pstock.m_product_id ";
			$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
			$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
			$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
			$select .= "JOIN m_replenish repl ON repl.m_product_id = pstock.m_product_id ";
			$select .= "JOIN Ad_User au ON au.Ad_User_id = cbp.salesrep_id ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND prod_po.iscurrentvendor = 'Y' AND prod.discontinued = 'N' ";
			$select .= "AND repl.level_min = 0 AND repl.level_max = 0 ";
			$select .= "AND pstock.qtyordered > pstock.qtyreserved ";
			$select .= "AND m_pricelist_version_id = 1000000 ";
			// $select .= "AND (pstock.qtyordered+pstock.qtyonhand) > pstock.qtyreserved AND pstock.qtyreserved > 0 ";
			$select .= "ORDER BY au.value ASC, cbp.name ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_object($res)) {

						if ($row->inkopare != $current_purchace) {
							if ($countrow > 1) {
								echo "<tr>\n";
								echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline\"></td>\n";
								echo "</tr>\n";
							}
						echo "<tr>\n";
						echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline\">Inköpare: " . strtoupper($row->inkopare) . "</td>\n";
						echo "</tr>\n";
						}
						$current_purchace = $row->inkopare;
						
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
				
						echo "\t<tr>";
						// echo "\t\t<td class=\"$backcolor\">$countrow</td>\n";
						echo "\t\t<td class=\"$backcolor\">$row->artnr</td>\n";
						echo "\t\t<td class=\"$backcolor\">$row->leverantor</td>\n";
						echo "\t\t<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=" . $row->artnr . "\">$row->tillverkare $row->beskrivning</a></td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"right\">" . number_format($row->productprice, 0, ',', ' ') . " SEK</td>\n";
						echo "\t</tr>\n";
						
						$countrow++;
						
						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}

					}
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Ingen produkter som är överbeställda, klockrent!</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "\t<tr>\n";
		// echo "\t\t<td colspan=\"2\">&nbsp;</td>\n";
		echo "\t\t<td align=\"left\"><b>Totalt: $countrow st</td>\n";
		echo "\t\t<td align=\"center\">&nbsp;</td>\n";
		echo "\t\t<td align=\"center\">&nbsp;</td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
	}

	function displayMainCategorysValue() {
	
			$data = array();
			$index = array();
			$select  = "SELECT m_product_category_id, m_product_category_parent_id, name ";
			$select .= "FROM m_product_category ";
			$select .= "WHERE ad_client_id=1000000 ";
			$select .= "ORDER BY m_product_category_id ";
			
			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

			while ($res && $row = pg_fetch_assoc($res)) {
				$id = $row["m_product_category_id"];
				$kategori_id_parent = $row["m_product_category_parent_id"] === 0 ? 0 : $row["m_product_category_parent_id"];
				$data[$id] = $row;
				$index[$kategori_id_parent][] = $id;
			}
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				print_r($data);
				print_r($index);
				exit;
			}

			$totalvarde = 0;
			$rowcolor = true;
			$i = 0;
			$sjabo = array();

			$select  = "SELECT cat.m_product_category_id, cat.m_product_category_parent_id, cat.name AS category_name, cat.value, u.value AS responsible_name ";
			$select .= "FROM m_product_category cat ";
			$select .= "LEFT JOIN ad_user u ON cat.salesrep_id = u.ad_user_id ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				// $select .= "WHERE cat.m_product_category_id IN (1000528,1000774,1000530,1000529) AND cat.ad_client_id=1000000 ";
				$select .= "WHERE cat.m_product_category_id IN (1000528,1000535,1000529,1001183) AND cat.ad_client_id=1000000 ";
			} else {
				// $select .= "WHERE cat.m_product_category_parent_id is null AND cat.ad_client_id=1000000 ";
				// $select .= "WHERE cat.m_product_category_id IN (1000528,1000774,1000530,1000529) AND cat.ad_client_id=1000000 ";
				$select .= "WHERE cat.m_product_category_id IN (1000528,1000535,1000529,1001183) AND cat.ad_client_id=1000000 ";
			}
			// $select .= "AND m_product_category_id = 1000528 ";
			$select .= "ORDER BY cat.name ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			echo "<h2>Värde</h2>\n";
			echo "<div>\n";
			echo "<table>\n";
			echo "<tr>\n";
			// echo "<td width=\"100\"><b>ID</b></td>\n";
			echo "<td width=\"550\"><b>Kategori</b></td>\n";
			echo "<td width=\"120\" align=\"center\"><b>Lagervärde</b></td>\n";
			echo "<td width=\"65\" align=\"center\"><b>Ansvarig</b></td>\n";
			echo "</tr>";

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_object($res)) {
					
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						
						// $summaLager = $this->display_child_nodes(1000802, 0);
						$treeCat = $this->display_child_nodes($row->m_product_category_id, NULL, $data, $index);
						$trimTreeCat = rtrim($treeCat,",");
						$SummaLager = $this->displayStoreCategorys($trimTreeCat);
						$totalvarde = $totalvarde + $SummaLager;
						$sjabo[$i]['catvalue'] = $row->value;
						$sjabo[$i]['catid'] = $row->m_product_category_id;
						$sjabo[$i]['catname'] = $row->category_name;
						$sjabo[$i]['lagervarde'] = round($SummaLager,0);
						$sjabo[$i]['ansvarig'] = strtoupper($row->responsible_name);
						
						echo "<tr>\n";
						// echo "<td class=\"$backcolor\">$row->value</td>\n";
						if ($SummaLager > 0) {
							echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?catID=$row->m_product_category_id\"><b>$row->category_name</b></a></td>\n";
						} else {
							echo "<td class=\"$backcolor\">$row->category_name</td>\n";
						}
						echo "\t\t<td class=\"$backcolor\" align=\"right\">" . number_format($SummaLager, 0, ',', ' ') . " SEK&nbsp;</td>\n";
						echo "<td class=\"$backcolor\" align=\"center\">" . strtoupper($row->responsible_name) . "</td>\n";
						echo "</tr>\n";

						$i++;
						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
						
					}

				}

			$totalvardeclean = round($totalvarde,0);
			$totalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>\n";
			// echo "<td align=\"right\"></td>\n";
			echo "<td align=\"right\"><b>Totalt:</b></td>\n";
			echo "<td align=\"right\"><b>$totalvarde SEK&nbsp;</b></td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</div>\n";
			if ($_SERVER['REMOTE_ADDR'] != "192.168.1.89x") {
				// print_r($sjabo);
				// exit;
				$rowcolor = true;
				echo "<div class=\"top10\">\n";

				echo "<h2>Andel</h2>\n";
				echo "<div>\n";
				echo "<table>\n";
				echo "<tr>\n";
				// echo "<td width=\"100\"><b>ID</b></td>\n";
				echo "<td width=\"550\"><b>Kategori</b></td>\n";
				echo "<td width=\"120\" align=\"center\"><b>Lagervärde</b></td>\n";
				echo "<td width=\"65\" align=\"center\"><b>Ansvarig</b></td>\n";
				echo "</tr>\n";

				// $antal = count($sjabo); 
				 
				foreach ($sjabo as $a => $b) {
					// echo $b[catid] . " " . $b[catname] . "\n"; 

						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						$andelvarde = ($b['lagervarde'] / $totalvardeclean * 100);

						echo "<tr>\n";
						// echo "<td class=\"$backcolor\">$b[catvalue]</td>\n";
						if ($SummaLager > 0) {
							echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?catID=$b[catid]\"><b>$b[catname]</b></a></td>\n";
						} else {
							echo "<td class=\"$backcolor\">$row->category_name</td>\n";
						}
						echo "\t\t<td class=\"$backcolor\" align=\"right\">" . number_format(round($andelvarde, 2), 2, ',', ' ') . " %&nbsp;</td>\n";
						echo "<td class=\"$backcolor\" align=\"center\">" . strtoupper($b['ansvarig']) . "</td>\n";
						echo "</tr>\n";
						// echo $i . ' : ' . $sjabo[$i] . '<br />'; 
						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
					

				}

				echo "<tr>\n";
				// echo "<td align=\"right\"></td>\n";
				echo "<td align=\"right\"><b>Totalt:</b></td>\n";
				echo "<td align=\"right\"><b>100 %&nbsp;</b></td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo "</div>\n";



				echo "</div>\n";
			
			}

	}
	
	function displayMainCategorysValueDetail($catID) {

			$data = array();
			$index = array();
			$select = "SELECT m_product_category_id, m_product_category_parent_id, name FROM m_product_category WHERE ad_client_id=1000000 ORDER BY m_product_category_id ";
			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

			while ($res && $row = pg_fetch_assoc($res)) {
				$id = $row["m_product_category_id"];
				$kategori_id_parent = $row["m_product_category_parent_id"] === 0 ? 0 : $row["m_product_category_parent_id"];
				$data[$id] = $row;
				$index[$kategori_id_parent][] = $id;
			}
			// print_r($data);
			// print_r($index);
			// exit;
			$treeCat = $this->display_child_nodes($catID, NULL, $data, $index);
			$trimTreeCat = rtrim($treeCat,",");
			
			$totalvarde = 0;

			$select = "SELECT cat.name, SUM(pstock.qtyonhand), SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue, cat.value, u.value AS responsible_name ";
			// $select .= "FROM m_product_stock_summary_v pstock ";
			$select .= "FROM m_product_cache pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_category cat ON cat.m_product_category_id = prod.m_product_category_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "LEFT JOIN ad_user u ON cat.salesrep_id = u.ad_user_id ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
			$select .= "AND cat.m_product_category_id IN ($trimTreeCat) ";
			$select .= "GROUP BY cat.name, cat.value, u.value ";
			$select .= "ORDER BY storevalue DESC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"100\"><b>ID</b></td>";
			echo "<td width=\"550\"><b>Kategori</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Antal</b></td>";
			echo "<td width=\"120\" align=\"center\"><b>Lagervärde</b></td>";
			echo "<td width=\"70\" align=\"center\"><b>Ansvarig</b></td>";
			echo "</tr>";

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_row($res)) {
					
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						
						$totalvarde += $row[2];
						$SummaLager = number_format($row[2], 0, ',', ' ');

						echo "<tr>";
						echo "<td class=\"$backcolor\">$row[3]</td>";
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							echo "<td class=\"$backcolor\"><a href=\"lagerstatus.php?katID=$row[3]\">$row[0]</a></td>";
						} else {
							echo "<td class=\"$backcolor\"><a href=\"lagerstatus.php?katID=$row[3]\">$row[0]</a></td>";
							// echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?katID=$row[3]\">$row[0]</a></td>";
						}
						echo "<td class=\"$backcolor\" align=\"center\">$row[1]</td>";
						echo "<td class=\"$backcolor\" align=\"right\">$SummaLager SEK&nbsp;</td>";
						echo "<td class=\"$backcolor\" align=\"center\">" . strtoupper($row[4]) . "</td>";
						echo "</tr>";

						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
						
					}

				}

			$totalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>";
			echo "<td align=\"left\"><b>Totalt:</b></td>";
			echo "<td align=\"right\"><b></b></td>";
			echo "<td align=\"right\"><b></b></td>";
			echo "<td align=\"right\"><b>$totalvarde SEK&nbsp;</b></td>";
			echo "</tr>";
			echo "</table>";

	}
	
	function display_child_nodes($kategori_id_parent, $level, $data, $index, &$a = null) {

		$kategori_id_parent = $kategori_id_parent === NULL ? NULL : $kategori_id_parent;
		if (isset($index[$kategori_id_parent])) {
			foreach ($index[$kategori_id_parent] as $id) {
				$a .= $data[$id]["m_product_category_id"] . ",";
				$this->display_child_nodes($id, $level + 1, $data, $index, $a);
			}
		}
		
		return $a;
	}

	function displayStoreCategorys($category) {

		$select = "SELECT SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue ";
		// $select .= "FROM m_product_stock_summary_v pstock ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "JOIN m_product p ON p.m_product_id = pstock.m_product_id ";
		$select .= "LEFT JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005  AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
		$select .= "AND p.m_product_category_id IN ($category) ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select . "<br><br>";
			// exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

		return $row->storevalue;
				
	}

	function goodsExpectationValue() {
		global $dagensdatum, $firstinput, $only_today;

		$countrow = 1;
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		// $dagensdatum = date("Y-m-d", time());
		$look_forward = date('Y-m-d', strtotime("$dagensdatum +3 day"));
		$look_backward = date('Y-m-d', strtotime("$dagensdatum -3 day"));
		$totalvikt = 0;
		$antaltotal = 0;

		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td width=\"25\">&nbsp;</td>\n";
		echo "\t\t<td width=\"300\"><b>Leverantör</b></td>\n";
		// echo "\t\t<td width=\"75\" align=\"center\"><b>Antal</b></td>\n";
		echo "\t\t<td width=\"110\" align=\"center\"><b>Summa</b></td>\n";
		echo "\t\t<td width=\"75\" align=\"center\"><b>Inköpare</b></td>\n";
		echo "\t</tr>\n";

			// $select = "SELECT bp.name, SUM(col.qtyordered - col.qtydelivered) AS totantal, SUM((col.qtyordered - col.qtydelivered) * po.currentcostprice) AS totsumma, SUM((col.qtyordered - col.qtydelivered) * p.weight_net) AS totvikt, bp.value AS bpartner, au.value AS inkopare ";
			$select = "SELECT bp.name, bp.value AS bpartner, au.value AS inkopare, ";
			// $select .= "SUM((col.qtyordered - col.qtydelivered) * po.currentcostprice) AS totsumma ";
			$select .= "SUM((col.qtyordered - col.qtydelivered) * price.pricelimit) AS totsumma ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			$select .= "JOIN Ad_User au ON au.Ad_User_id = bp.salesrep_id ";
			// $select .= "JOIN m_cost po ON po.m_product_id = p.m_product_id ";
			$select .= "JOIN m_productprice price ON price.m_product_id = p.m_product_id ";
			$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered ";
			/*
			if ($only_today == "yes") {
				$select .= "AND col.datepromised = '$dagensdatum' AND col.datepromisedprecision = 'D' ";
			} else {
				$select .= "AND ( ";
				$select .= "(col.datepromised = '$dagensdatum') ";
				$select .= "OR (col.datepromised > '$look_backward' AND col.datepromised < '$dagensdatum') ";
				$select .= "OR (col.datepromised < '$look_forward' AND col.datepromised > '$dagensdatum' AND NOT col.datepromisedprecision = 'D') ";
				$select .= ") ";
			}
			$select .= "AND NOT col.datepromisedprecision = 'U' ";
			*/
			// $select .= "AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
			$select .= "AND price.m_pricelist_version_id=1000000 ";
			$select .= "GROUP BY bp.name, bp.value, au.value ";
			// $select .= "ORDER BY bp.name ";
			$select .= "ORDER BY totsumma DESC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_object($res)) {

						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
				
						echo "\t<tr>";
						echo "\t\t<td class=\"$backcolor\">$countrow</td>\n";
						// echo "\t\t<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?supID=$row[3]&firstinput=$firstinput&only_today=$only_today\">$row[0]</a></td>\n";
						echo "\t\t<td class=\"$backcolor\"><a href=\"#\" onclick=\"document.sampleform.supID.value='$row->bpartner'; document.sampleform.submit();\">$row->name</a></td>\n";
						// echo "\t\t<td class=\"$backcolor\" align=\"center\">" . round($row->totantal,0) . "</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"right\">" . number_format($row->totsumma, 0, ',', ' ') . " SEK&nbsp;</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . strtoupper($row->inkopare) . "</td>\n";
						//echo "\t\t<td class=\"$backcolor\" align=\"center\">" . round($row[2],2) . "</td>\n";
						echo "\t</tr>\n";
						
						$countrow++;
						// $antaltotal = $antaltotal + $row->totantal;
						$totalsumma = $totalsumma + $row->totsumma;
						
						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}

					}
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Inga produkter beräknas in denna dag (period)</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\">&nbsp;</td>\n";
		// echo "\t\t<td align=\"center\"><b>$antaltotal st</td>\n";
		echo "\t\t<td align=\"right\"><b>" . number_format($totalsumma, 0, ',', ' ') . " SEK&nbsp;</td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
	}

	function goodsExpectationValueDetail() {
		global $supID, $dagensdatum, $firstinput, $only_today;

		$countrow = 1;
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		// $dagensdatum = date("Y-m-d", time());
		$look_forward = date('Y-m-d', strtotime("$dagensdatum +3 day"));
		$look_backward = date('Y-m-d', strtotime("$dagensdatum -3 day"));
		$totalvikt = 0;
		$antaltotal = 0;
		
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td width=\"25\">&nbsp;</td>\n";
		echo "\t\t<td width=\"300\"><b>Leverantör</b></td>\n";
		echo "\t\t<td width=\"150\"><b>Artikel</b></td>\n";
		echo "\t\t<td width=\"55\" align=\"center\"><b>Antal</b></td>\n";
		// echo "\t\t<td width=\"75\" align=\"center\"><b>Nettovikt</b></td>\n";
		echo "\t\t<td width=\"100\" align=\"center\"><b>Summa</b></td>\n";
		echo "\t\t<td width=\"100\" align=\"center\"><b>Inköpsorder</b></td>\n";
		// echo "\t\t<td width=\"25\" align=\"center\"><b>&nbsp;</b></td>\n";
		echo "\t</tr>\n";
			
			$select = "SELECT bp.name, p.value, col.qtyordered, col.qtydelivered, p.weight_net, o.documentno, col.datepromised, col.datepromisedprecision, ";
			// $select .= "SUM((col.qtyordered - col.qtydelivered) * po.currentcostprice) AS totsumma ";
			$select .= "SUM((col.qtyordered - col.qtydelivered) * price.pricelimit) AS totsumma ";
			$select .= "FROM c_orderline col ";
			$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
			$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
			// $select .= "JOIN m_cost po ON po.m_product_id = p.m_product_id ";
			$select .= "JOIN m_productprice price ON price.m_product_id = p.m_product_id ";
			$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered ";
			/*
			if ($only_today == "yes") {
				$select .= "AND col.datepromised = '$dagensdatum' AND col.datepromisedprecision = 'D' ";
			} else {
				$select .= "AND ( ";
				$select .= "(col.datepromised = '$dagensdatum') ";
				$select .= "OR (col.datepromised > '$look_backward' AND col.datepromised < '$dagensdatum') ";
				$select .= "OR (col.datepromised < '$look_forward' AND col.datepromised > '$dagensdatum' AND NOT col.datepromisedprecision = 'D') ";
				$select .= ") ";
			}
			$select .= "AND NOT col.datepromisedprecision = 'U' ";
			*/
			$select .= "AND bp.value = '$supID' ";
			// $select .= "AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
			$select .= "AND price.m_pricelist_version_id=1000000 ";
			$select .= "GROUP BY bp.name, p.value, col.qtyordered, col.qtydelivered, p.weight_net, o.documentno, col.datepromised, col.datepromisedprecision ";
			// $select .= "ORDER BY bp.name ASC ";
			$select .= "ORDER BY totsumma DESC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_object($res)) {

						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
					
						$antalin = $row->qtyordered - $row->qtydelivered;
						// $summa2 = round($row->totsumma,0);
						
						if ($row->datepromisedprecision == 'M') {
							$statuscolor = "#F26F0D";
						} elseif ($row->datepromisedprecision == 'P') {
							$statuscolor = "#FFCC00";
						} elseif ($row->datepromisedprecision == 'W') {
							$statuscolor = "#CCFF99";
						} else {
							$statuscolor = "#00FF00";
						}
						
						echo "\t<tr>";
						echo "\t\t<td class=\"$backcolor\">$countrow</td>\n";
						// echo "\t\t<td>" . date("Y-m-d H:i",strtotime($row[0])) . "</td>\n";
						// echo "\t\t<td>" . date("Y-m-d",strtotime($row[0])) . "</td>\n";
						echo "\t\t<td class=\"$backcolor\">$row->name</td>\n";
						echo "\t\t<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row->value\">$row->value</a></td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">" . $antalin . "</td>\n";
						// echo "\t\t<td class=\"$backcolor\" align=\"center\">" . $nettototal . "</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"right\">" . number_format($row->totsumma, 0, ',', ' ') . " SEK&nbsp;</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\">$row->documentno</td>\n";
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							// echo "\t\t<td align=\"left\">" . $this->showDeliveryDate($row[6], $row[7], false, true) . "</td>\n";
							// echo "\t\t<td bgcolor=\"$statuscolor\"></td>\n";
						} else {
							// echo "\t\t<td align=\"left\">" . $this->showDeliveryDate($row[6], $row[7], false, true) . "</td>\n";
							// echo "\t\t<td align=\"center\">$row[5]</td>\n";
							// echo "\t\t<td bgcolor=\"$statuscolor\"></td>\n";
						}
						echo "\t</tr>\n";
						
						$countrow++;
						$totalsumma = $totalsumma + $row->totsumma;
						$antaltotal = $antaltotal + $antalin;

						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}
						
					}
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Ingen kö på denna produkt</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"3\">&nbsp;</td>\n";
		echo "\t\t<td align=\"center\"><b>$antaltotal st</td>\n";
		echo "\t\t<td align=\"right\"><b>" . number_format($totalsumma, 0, ',', ' ') . " SEK&nbsp;</td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
	}
	
	
}
?>

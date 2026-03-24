<?php

/*

PHP login object
author		Stefan Sjöberg
version		1.0 2012-06-26

*/
include_once 'Db.php';

Class CRma {

	var $conn_my;
	var $conn_ad;

	function __construct() {
		global $conn_ad;
		
		$this->conn_my = Db::getConnection();

		$this->conn_ad = Db::getConnectionAD();

	}

	function displayRMACategory() {
		global $doa, $rma_year, $rma_month, $rma_days, $return_rma;

		$rowcolor = true;
		$sumrma = 0;

		echo "<div>\n";
		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"800\">\n";
		echo "<tr>\n";
		echo "<td width=\"50\" align=\"center\"><b>Antal</b></td>\n";
		echo "<td align=\"left\"><b>Kategori</b></td>\n";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		}
		echo "</tr>\n";
		
		$select = "SELECT SUM(rmal.qty) AS antal, cat.name, cat.value ";
		$select .= "FROM m_rma rma ";
		$select .= "JOIN m_rmaline rmal ON rmal.m_rma_id = rma.m_rma_id ";
		$select .= "JOIN m_inoutline iol ON iol.m_inoutline_id = rmal.m_inoutline_id ";
		$select .= "JOIN m_product p ON p.m_product_id = iol.m_product_id ";
		$select .= "JOIN xc_manufacturer m ON m.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN m_product_category cat ON cat.m_product_category_id = p.m_product_category_id ";
		if ($doa) {
			$select .= "WHERE rma.m_rmatype_id = 1000001 AND rma.docstatus = 'CO' ";
		} elseif ($return_rma) {
			$select .= "WHERE rma.m_rmatype_id = 1000000 AND rma.docstatus = 'CO' ";
		} else {
			$select .= "WHERE rma.m_rmatype_id = 1000005 AND rma.docstatus = 'CO' ";
		}
		if ($rma_year > 0) {
			$select .= "AND to_char(rma.created, 'yyyy')  = '$rma_year' ";
		}
		if ($rma_month > 0) {
			$select .= "AND to_char(rma.created, 'MM')  = '$rma_month' ";
		}
		if ($rma_days > 0) {
			$select .= "AND to_char(rma.created, 'DD')  = '$rma_days' ";
		}
		$select .= "GROUP BY cat.name, cat.value ";
		$select .= "ORDER BY antal DESC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_object($res);

			if ($res && pg_num_rows($res) > 0) {
			
				while ($res && $row = pg_fetch_row($res)) {
				
					$sumrma = $sumrma + round($row[0],0);

					if ($rowcolor == true) {
						$backcolor = "firstrow";
					} else {
						$backcolor = "secondrow";
					}
				
						echo "<tr>\n";
						echo "<td class=\"$backcolor\" align=\"center\">" . round($row[0],0) . "</td>\n";
						echo "<td class=\"$backcolor\" align=\"left\"><a href=\"" . $_SERVER['PHP_SELF'] . "?rma_year=$rma_year&rma_month=$rma_month&rma_days=$rma_days&rma_cat=$row[2]\">$row[1]</a></td>\n";
						echo "</tr>\n";

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
				
				}
				
			} else {
			
				echo "<tr>\n";
				echo "<td colspan=\"2\"><i>Inget registrerat denna period</i></td>\n";
				echo "</tr>\n";
			
			}

		echo "<tr>\n";
		echo "<td colspan=\"2\"><b>Totalt $sumrma st denna period</b></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";

	}

	function displayRMACategoryDetail($rma_cat) {
		global $doa, $rma_year, $rma_month, $rma_days, $return_rma;

		$rowcolor = true;
		$sumrma = 0;

		echo "<div>\n";
		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"800\">\n";
		echo "<tr>\n";
		echo "<td width=\"50\" align=\"center\"><b>Antal</b></td>\n";
		echo "<td width=\"100\" align=\"left\"><b>Artikel nr</b></td>\n";
		echo "<td width=\"450\" align=\"left\"><b>Benämning</b></td>\n";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		}
		echo "</tr>\n";
		
		$select = "SELECT SUM(rmal.qty) AS antal, p.value, m.name, p.name ";
		$select .= "FROM m_rma rma ";
		$select .= "JOIN m_rmaline rmal ON rmal.m_rma_id = rma.m_rma_id ";
		$select .= "JOIN m_inoutline iol ON iol.m_inoutline_id = rmal.m_inoutline_id ";
		$select .= "JOIN m_product p ON p.m_product_id = iol.m_product_id ";
		$select .= "JOIN xc_manufacturer m ON m.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN m_product_category cat ON cat.m_product_category_id = p.m_product_category_id ";
		if ($doa) {
			$select .= "WHERE rma.m_rmatype_id = 1000001 AND rma.docstatus = 'CO' ";
		} elseif ($return_rma) {
			$select .= "WHERE rma.m_rmatype_id = 1000000 AND rma.docstatus = 'CO' ";
		} else {
			$select .= "WHERE rma.m_rmatype_id = 1000005 AND rma.docstatus = 'CO' ";
		}
		$select .= "AND cat.value ='$rma_cat' ";
		if ($rma_year > 0) {
			$select .= "AND to_char(rma.created, 'yyyy')  = '$rma_year' ";
		}
		if ($rma_month > 0) {
			$select .= "AND to_char(rma.created, 'MM')  = '$rma_month' ";
		}
		if ($rma_days > 0) {
			$select .= "AND to_char(rma.created, 'DD')  = '$rma_days' ";
		}
		$select .= "GROUP BY p.value, m.name, p.name ";
		$select .= "ORDER BY antal DESC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_object($res);

			if ($res && pg_num_rows($res) > 0) {
			
				while ($res && $row = pg_fetch_row($res)) {
				
					$sumrma = $sumrma + round($row[0],0);

					if ($rowcolor == true) {
						$backcolor = "firstrow";
					} else {
						$backcolor = "secondrow";
					}
				
						echo "<tr>\n";
						echo "<td class=\"$backcolor\" align=\"center\">" . round($row[0],0) . "</td>\n";
						echo "<td class=\"$backcolor\" align=\"left\"><a href=\"" . $_SERVER['PHP_SELF'] . "?rma_year=$rma_year&rma_month=$rma_month&rma_days=$rma_days&rma_cat=$rma_cat&rma_artnr=$row[1]\">$row[1]</a></td>\n";
						// echo "<td class=\"$backcolor\" align=\"left\">$row[1]</td>\n";
						echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\"href=\"/info.php?article=$row[1]\">$row[2] $row[3]</a></td>\n";
						echo "</tr>\n";

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
				
				}
				
			} else {
			
				echo "<tr>\n";
				echo "<td colspan=\"3\"><i>Inget registrerat denna period</i></td>\n";
				echo "</tr>\n";
			
			}

		echo "<tr>\n";
		echo "<td colspan=\"3\"><b>Totalt $sumrma st reparationer denna period</b></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";

	}

	function displayRMAProductDetail($artnr) {
		global $doa, $rma_year, $rma_month, $rma_days, $return_rma;

		$rowcolor = true;
		$sumrma = 0;

		echo "<div>\n";
		echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"2\" width=\"1200\">\n";
		echo "<tr>\n";
		echo "<td width=\"80\" align=\"center\"><b>Datum</b></td>\n";
		echo "<td width=\"50\" align=\"center\"><b>RMA</b></td>\n";
		echo "<td width=\"1000\" align=\"left\"><b>Felbeskrivning</b></td>\n";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		}
		echo "</tr>\n";
		
		$select = "SELECT rma.name, rma.error_descripton, rma.customercomment, rma.m_rmatype_id, rma.created ";
		$select .= "FROM m_rma rma ";
		$select .= "JOIN m_rmaline rmal ON rmal.m_rma_id = rma.m_rma_id ";
		$select .= "JOIN m_inoutline iol ON iol.m_inoutline_id = rmal.m_inoutline_id ";
		$select .= "JOIN m_product p ON p.m_product_id = iol.m_product_id ";
		if ($doa) {
			$select .= "WHERE rma.m_rmatype_id = 1000001 AND rma.docstatus = 'CO' ";
		} elseif ($return_rma) {
			$select .= "WHERE rma.m_rmatype_id = 1000000 AND rma.docstatus = 'CO' ";
		} else {
			// $select .= "WHERE rma.m_rmatype_id = 1000005 AND rma.docstatus = 'CO' ";
			$select .= "WHERE rma.m_rmatype_id IN (1000001,1000005) AND rma.c_doctype_id = 1000029 AND rma.docstatus = 'CO' ";
		}
		// $select .= "AND cat.value ='$rma_cat' ";
		if ($rma_year > 0) {
			$select .= "AND to_char(rma.created, 'yyyy')  = '$rma_year' ";
		}
		if ($rma_month > 0) {
			$select .= "AND to_char(rma.created, 'MM')  = '$rma_month' ";
		}
		if ($rma_days > 0) {
			$select .= "AND to_char(rma.created, 'DD')  = '$rma_days' ";
		}
		$select .= "AND p.value='$artnr' ";
		$select .= "ORDER BY rma.name ASC ";
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
						$row[2] = $row[2];
						echo "<tr>\n";
						echo "<td class=\"$backcolor\" align=\"center\">" . date("Y-m-d", strtotime($row[4])) . "</td>\n";
						echo "<td class=\"$backcolor\" align=\"center\">" . $row[0] . "</td>\n";
						if ($row[3] == 1000001) {
							echo "<td class=\"$backcolor\" align=\"left\">DOA - $row[2]</td>\n";
						} else {
							echo "<td class=\"$backcolor\" align=\"left\">REP - $row[1]</td>\n";
						}
						echo "</tr>\n";

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
					$sumrma++;
				
				}
				
			} else {
			
				echo "<tr>\n";
				echo "<td colspan=\"3\"><i>Inget registrerat denna period</i></td>\n";
				echo "</tr>\n";
			
			}

		echo "<tr>\n";
		echo "<td colspan=\"3\"><b>Totalt $sumrma st RMA</b></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";

	}

	function summaryRMAProduct($artnr) {

		$rowcolor = true;
		$sumrma = 0;

		echo "<div>\n";
		echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"2\" width=\"1200\">\n";
		echo "<tr>\n";
		echo "<td width=\"60\" align=\"center\"><b>Antal</b></td>\n";
		echo "<td width=\"150\" align=\"left\"><b>Orsak</b></td>\n";
		echo "<td align=\"left\"><b>Produkt</b></td>\n";
		echo "<td width=\"75\" align=\"center\"><b>Andel</b></td>\n";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		}
		echo "</tr>\n";
		
		$select .= "SELECT SUM(rmal.qty) AS antal, rmat.name, p.value, m.name, p.name, stat.qtytotal, cat.value, cat.name ";
		$select .= "FROM m_rma rma ";
		$select .= "JOIN m_rmaline rmal ON rmal.m_rma_id = rma.m_rma_id ";
		$select .= "JOIN m_rmatype rmat ON rmat.m_rmatype_id = rma.m_rmatype_id ";
		$select .= "JOIN m_inoutline iol ON iol.m_inoutline_id = rmal.m_inoutline_id ";
		$select .= "JOIN m_product p ON p.m_product_id = iol.m_product_id ";
		$select .= "JOIN xc_manufacturer m ON m.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN m_product_category cat ON cat.m_product_category_id = p.m_product_category_id ";
		$select .= "LEFT JOIN xc_product_statistics stat ON p.m_product_id = stat.m_product_id ";
		$select .= "WHERE rma.docstatus = 'CO' AND rma.c_doctype_id = 1000029 AND stat.c_country_id IS NULL ";
		$select .= "AND p.value='$artnr' ";
		$select .= "GROUP BY p.value, m.name, p.name, rmat.name, stat.qtytotal, cat.value, cat.name ";
		$select .= "ORDER BY antal DESC ";
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
					$getcategory = $row[6];
					$getcategoryname = $row[7];
					$antalsalda = round($row[5],0);
					$cause = round($row[0],0);
					$thisandel = $cause / $antalsalda * 100;
					$row[1] = $row[1];
					$row[3] = $row[3];
					echo "<tr>\n";
					echo "<td class=\"$backcolor\" align=\"center\">" . $cause . "</td>\n";
					echo "<td class=\"$backcolor\" align=\"left\">$row[1]</td>\n";
					echo "<td class=\"$backcolor\" align=\"left\">$row[2] $row[3]</td>\n";
					echo "<td class=\"$backcolor\" align=\"center\">" . number_format(round($thisandel, 2), 2, ',', ' ') . "%</td>\n";
					echo "</tr>\n";

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
					$sumrma++;
				
				}
				
			} else {
			
				echo "<tr>\n";
				echo "<td colspan=\"3\"><i>Inget registrerat denna period</i></td>\n";
				echo "</tr>\n";
			
			}

		echo "<tr>\n";
		echo "<td colspan=\"4\" align=\"left\">Antal sålda: <b>" . $antalsalda . " st</b></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td colspan=\"4\" align=\"left\"><a href=\"" . $_SERVER['PHP_SELF'] . "?category=$getcategory\">--> Tillbaka till $getcategoryname</a></td>\n";
		echo "</tr>\n";
		/*
		echo "<tr>\n";
		echo "<td colspan=\"3\"><b>Totalt $sumrma st reparationer denna period</b></td>\n";
		echo "</tr>\n";
		*/
		echo "</table>\n";
		echo "</div>\n";

	}

	function summaryRMACat($category,$discontinued) {

		$rowcolor = true;
		$countrows = 0;

		echo "<div>\n";
		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"1200\">\n";
		echo "<tr>\n";
		echo "<td width=\"50\" align=\"center\"><b>RMA</b></td>\n";
		echo "<td width=\"50\" align=\"center\"><b>Sålda</b></td>\n";
		echo "<td width=\"135\" align=\"left\"><b>Artikel nr</b></td>\n";
		echo "<td align=\"left\"><b>Produkt</b></td>\n";
		echo "<td width=\"75\" align=\"center\"><b>Andel</b></td>\n";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		}
		echo "</tr>\n";
		
		$select .= "SELECT SUM(rmal.qty) AS rma, stat.qtytotal AS salda, SUM((rmal.qty/stat.qtytotal)*100) AS andel, p.value, m.name, p.name, p.discontinued ";
		$select .= "FROM m_rma rma ";
		$select .= "JOIN m_rmaline rmal ON rmal.m_rma_id = rma.m_rma_id ";
		$select .= "JOIN m_rmatype rmat ON rmat.m_rmatype_id = rma.m_rmatype_id ";
		$select .= "JOIN m_inoutline iol ON iol.m_inoutline_id = rmal.m_inoutline_id ";
		$select .= "JOIN m_product p ON p.m_product_id = iol.m_product_id ";
		$select .= "JOIN xc_manufacturer m ON m.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN m_product_category cat ON cat.m_product_category_id = p.m_product_category_id ";
		$select .= "LEFT JOIN xc_product_statistics stat ON p.m_product_id = stat.m_product_id ";
		$select .= "WHERE rma.docstatus = 'CO' AND stat.c_country_id IS NULL ";
		$select .= "AND cat.value ='$category' AND rma.m_rmatype_id IN (1000001,1000005) AND rma.c_doctype_id = 1000029 AND p.isselfservice = 'Y' AND p.demo_product = 'N' AND NOT stat.qtytotal IS NULL ";
		if ($discontinued != "yes") {
			$select .= "AND p.discontinued = 'N' ";
		}
		$select .= "GROUP BY p.value, m.name, p.name, stat.qtytotal, p.discontinued ";
		$select .= "ORDER BY andel DESC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_object($res);

			if ($res && pg_num_rows($res) > 0) {
			
				while ($res && $row = pg_fetch_row($res)) {
					
					if ($countrows == 8724210) {
						echo "<tr>\n";
						echo "<td class=\"$backcolor\" align=\"left\"><a href=\"" . $_SERVER['PHP_SELF'] . "?category=$category\">Tillbaka till kategori</a></td>\n";
						echo "</tr>\n";
					}

					if ($rowcolor == true) {
						$backcolor = "firstrow";
					} else {
						$backcolor = "secondrow";
					}
					if ($row[2] > 20) {
						$andelcolor = "#FF0000";
					} elseif ($row[2] > 10) {
						$andelcolor = "#FF6600";
					} elseif ($row[2] > 5) {
						$andelcolor = "#666666";
					} else {
						$andelcolor = "#33CC33";
					}
					$beskrivning = $row[4] . " " . $row[5];
					$beskrivning = $beskrivning;
					if ($row[6] == "Y") {
						$beskrivning .= " <i><span style=\"color: red\">- Utgången</span></i>";
					}

					echo "<tr>\n";
					echo "<td class=\"$backcolor\" align=\"center\">" . round($row[0],0) . "</td>\n";
					echo "<td class=\"$backcolor\" align=\"center\">$row[1]</td>\n";
					echo "<td class=\"$backcolor\" align=\"left\"><a href=\"" . $_SERVER['PHP_SELF'] . "?article=$row[3]\">$row[3]</a></td>\n";
					echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"/info.php?article=$row[3]\">$beskrivning</a></td>\n";
					echo "<td class=\"$backcolor\" align=\"center\"><b><span style=\"color: $andelcolor\">" . number_format(round($row[2], 2), 2, ',', ' ') . "%</span></b></td>\n";
					echo "</tr>\n";

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
					$countrows++;
				
				}
				
			} else {
			
				echo "<tr>\n";
				echo "<td colspan=\"3\"><i>Inget registrerat denna period</i></td>\n";
				echo "</tr>\n";
			
			}

		/*
		echo "<tr>\n";
		echo "<td colspan=\"3\"><b>Totalt $sumrma st reparationer denna period</b></td>\n";
		echo "</tr>\n";
		*/
		echo "</table>\n";
		echo "</div>\n";
		echo "<p>Antal: <b>" . $countrows . " st</b></p>";

	}

	function summaryRMACatManufacturer($category,$discontinued) {

		$rowcolor = true;
		$countrows = 0;

		echo "<div>\n";
		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"1200\">\n";
		echo "<tr>\n";
		echo "<td align=\"left\"><b>Tillverkare</b></td>\n";
		echo "<td width=\"50\" align=\"center\"><b>Sålda</b></td>\n";
		echo "<td width=\"75\" align=\"center\"><b>RMA</b></td>\n";
		echo "<td width=\"75\" align=\"center\"><b>Andel</b></td>\n";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		}
		echo "</tr>\n";
		
		$select .= "SELECT SUM(rmal.qty) AS rma, SUM(stat.qtytotal) AS salda, m.name, m.value ";
		$select .= "FROM m_rma rma ";
		$select .= "JOIN m_rmaline rmal ON rmal.m_rma_id = rma.m_rma_id ";
		$select .= "JOIN m_rmatype rmat ON rmat.m_rmatype_id = rma.m_rmatype_id ";
		$select .= "JOIN m_inoutline iol ON iol.m_inoutline_id = rmal.m_inoutline_id ";
		$select .= "JOIN m_product p ON p.m_product_id = iol.m_product_id ";
		$select .= "JOIN xc_manufacturer m ON m.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN m_product_category cat ON cat.m_product_category_id = p.m_product_category_id ";
		$select .= "LEFT JOIN xc_product_statistics stat ON p.m_product_id = stat.m_product_id ";
		$select .= "WHERE rma.docstatus = 'CO' AND stat.c_country_id IS NULL ";
		$select .= "AND cat.value ='$category' AND rma.m_rmatype_id IN (1000001,1000005) AND rma.c_doctype_id = 1000029 AND p.isselfservice = 'Y' AND p.demo_product = 'N' AND NOT stat.qtytotal IS NULL ";
		if ($discontinued != "yes") {
			$select .= "AND p.discontinued = 'N' ";
		}
		$select .= "GROUP BY m.name, m.value ";
		$select .= "ORDER BY m.name ASC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_object($res);

			if ($res && pg_num_rows($res) > 0) {
			
				while ($res && $row = pg_fetch_object($res)) {
					
					if ($rowcolor == true) {
						$backcolor = "firstrow";
					} else {
						$backcolor = "secondrow";
					}
					$andelfel = (($row->rma / $row->salda)*100);
					if ($andelfel > 20) {
						$andelcolor = "#FF0000";
					} elseif ($andelfel > 10) {
						$andelcolor = "#FF6600";
					} elseif ($andelfel > 5) {
						$andelcolor = "#666666";
					} else {
						$andelcolor = "#33CC33";
					}
					$beskrivning = $row->name;

					echo "<tr>\n";
					echo "<td class=\"$backcolor\" align=\"left\"><b>$beskrivning</b></td>\n";
					echo "<td class=\"$backcolor\" align=\"center\">" . $row->salda . "</td>\n";
					echo "<td class=\"$backcolor\" align=\"center\">" . round($row->rma, 0) . "</td>\n";
					echo "<td class=\"$backcolor\" align=\"center\"><b><span style=\"color: $andelcolor\">" . number_format(round($andelfel, 2), 2, ',', ' ') . "%</span></b></td>\n";
					echo "</tr>\n";

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
					$countrows++;
				
				}
				
			} else {
			
				echo "<tr>\n";
				echo "<td colspan=\"3\"><i>Inget registrerat denna period</i></td>\n";
				echo "</tr>\n";
			
			}

		/*
		echo "<tr>\n";
		echo "<td colspan=\"3\"><b>Totalt $sumrma st reparationer denna period</b></td>\n";
		echo "</tr>\n";
		*/
		echo "</table>\n";
		echo "</div>\n";
		echo "<p>Antal: <b>" . $countrows . " st</b></p>";

	}
	
	function getIfRMAExist($artnr) {
		return false;
		$select .= "1 ";
		$select .= "FROM m_rma rma ";
		$select .= "JOIN m_rmaline rmal ON rmal.m_rma_id = rma.m_rma_id ";
		//$select .= "JOIN m_rmatype rmat ON rmat.m_rmatype_id = rma.m_rmatype_id ";
		$select .= "JOIN m_inoutline iol ON iol.m_inoutline_id = rmal.m_inoutline_id ";
		$select .= "JOIN m_product p ON p.m_product_id = iol.m_product_id ";
		$select .= "WHERE rma.docstatus = 'CO' AND rma.m_rmatype_id IN (1000001,1000005) AND rma.c_doctype_id = 1000029 AND rmal.qty > 0";
		$select .= "AND p.value='$artnr' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($res && pg_num_rows($res) > 0) 
			return true;
		else
			return false;
		/**
		$row = $res ? pg_fetch_object($res) : null;
		
		if ($row->rma > 0) {
			return true;
		} else {
			return false;
		}
		*/

	}
	
	function getRMACount($artnr) {
		
		$select .= "SELECT SUM(rmal.qty) AS rma ";
		$select .= "FROM m_rma rma ";
		$select .= "JOIN m_rmaline rmal ON rmal.m_rma_id = rma.m_rma_id ";
		$select .= "JOIN m_inoutline iol ON iol.m_inoutline_id = rmal.m_inoutline_id ";
		$select .= "JOIN m_product p ON p.m_product_id = iol.m_product_id ";
		$select .= "WHERE rma.docstatus = 'CO' AND rma.m_rmatype_id IN (1000001,1000005) AND rma.c_doctype_id = 1000029 ";
		$select .= "AND p.value='$artnr' ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;
		return $row->rma;
		//echo "<a target=\"_blank\" href=\"/order/admin/rma_summary.php?article=$artnr\"><b>" . round($row->rma,0) . " st</b></a>\n";

	}
	
}
?>

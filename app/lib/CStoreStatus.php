<?php
require_once("Db.php");


Class CStoreStatus {

	var $conn_my;
	var $conn_my2;

	function __construct() {

		$this->conn_my = Db::getConnection();
		$this->conn_my2 = Db::getConnectionDb('cyberadmin');

	}

	function getEUR() {
		
	$select  = "SELECT kurs FROM cyberphoto.ValutaNew WHERE id = 1 ";

	// echo $select;
	// exit;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
			extract($row);
		
				return $kurs;
			
			endwhile;
		
		} else {
		
			return 0;
		
		}

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

	function getTotalStoreValue($sortby) {

	$rowcolor = true;

	$select  = "SELECT Kategori.kategori, Kategori.kategori_id, SUM(Artiklar.lagersaldo+Artiklar.till_kund) AS 'lager', SUM(Artiklar.art_id*(Artiklar.lagersaldo+Artiklar.till_kund)) AS varde, SUM(mostSoldArticles.antalManad1) AS 'tretti', SUM(mostSoldArticles.antalManad2) AS 'sexti' ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "JOIN cyberphoto.Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
	$select .= "JOIN cyberphoto.mostSoldArticles ON Artiklar.artnr = mostSoldArticles.artnr ";
	$select .= "JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "WHERE (Artiklar.lagersaldo > 0 || Artiklar.till_kund > 0) AND Artiklar.art_id > 1 AND NOT (Artiklar.kategori_id = 314) ";
	$select .= "GROUP BY Kategori.kategori ";
	if ($sortby == "category") {
		$select .= "ORDER BY Kategori.kategori ASC ";
	} elseif ($sortby == "articles") {
		$select .= "ORDER BY lager DESC ";
	} elseif ($sortby == "storevalue") {
		$select .= "ORDER BY varde DESC ";
	} else {
		$select .= "ORDER BY varde DESC ";
	}

	// echo $select;
	// exit;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"550\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=category\">Kategori</u></a></b></td>";
			echo "<td width=\"100\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=articles\">Antal i lager</u></a></b></td>";
			echo "<td width=\"100\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=storevalue\">Lagervärde</u></a></b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

				if ($tretti == 0 && $sexti == 0) {
					
					$avvikelse = 999;
					
				} else {
				
					$avvikelse = round($tretti / $sexti, 2);
				}
			
			if ($rowcolor == true) {
				$backcolor = "firstrow";
			} else {
				$backcolor = "secondrow";
			}
			$totalvarde += $varde;
			$SummaLager = number_format($varde, 0, ',', ' ');

			echo "<tr>";
			echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?katid=" . $kategori_id . "\">$kategori</a></td>";
			echo "<td class=\"$backcolor\" align=\"center\">$lager</td>";
			echo "<td class=\"$backcolor\" align=\"right\">$SummaLager kr</td>";
			echo "<td class=\"$backcolor\" align=\"center\">$tretti</td>";
			echo "<td class=\"$backcolor\" align=\"center\">$sexti</td>";
			echo $this->displayTrend($avvikelse);
			echo "</tr>";

			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
		
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"5\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
		echo "</tr>";
		
		}
			$formattotalvarde = number_format($totalvarde, 0, ',', ' ');
			// $formattotalvarde = number_format($this->displayOverallStoreStatus(), 0, ',', ' ');
			echo "<tr>";
			echo "<td colspan=\"5\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"5\"><b>Totalt lagervärde just nu: $formattotalvarde kr</td>";
			echo "</tr>";
			echo "</table>";
			// echo number_format($totalvarde, 0, ',', ' ');
	}

	function getTotalStoreValueNew($sortby) {

	$rowcolor = true;

	// $select  = "SELECT Kategori.kategori, Kategori.kategori_id, SUM(Artiklar.lagersaldo+Artiklar.till_kund) AS 'lager', SUM(Artiklar.art_id*(Artiklar.lagersaldo+Artiklar.till_kund)) AS varde, SUM(mostSoldArticles.antalManad1) AS 'tretti', SUM(mostSoldArticles.antalManad2) AS 'sexti' ";
	// $select  = "SELECT Kategori.kategori, Kategori.kategori_id, SUM(Artiklar.lagersaldo+Artiklar.till_kund) AS 'lager', SUM(Artiklar.art_id*(Artiklar.lagersaldo+Artiklar.till_kund)) AS varde ";
	$select  = "SELECT Kategori.kategori, Kategori.kategori_id, SUM(Artiklar.lagersaldo) AS 'lager', SUM(Artiklar.art_id*Artiklar.lagersaldo) AS varde ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "JOIN cyberphoto.Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
	// $select .= "JOIN mostSoldArticles ON Artiklar.artnr = mostSoldArticles.artnr ";
	$select .= "JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	// $select .= "WHERE (Artiklar.lagersaldo > 0 || Artiklar.till_kund > 0) AND Artiklar.art_id > 1 AND NOT (Artiklar.kategori_id = 314) ";
	$select .= "WHERE Artiklar.lagersaldo > 0 AND Artiklar.art_id > 1 AND NOT (Artiklar.kategori_id = 314) ";
	$select .= "GROUP BY Kategori.kategori ";
	if ($sortby == "category") {
		$select .= "ORDER BY Kategori.kategori ASC ";
	} elseif ($sortby == "articles") {
		$select .= "ORDER BY lager DESC ";
	} elseif ($sortby == "storevalue") {
		$select .= "ORDER BY varde DESC ";
	} else {
		$select .= "ORDER BY varde DESC ";
	}

	// echo $select;
	// exit;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"550\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=category\">Kategori</u></a></b></td>";
			echo "<td width=\"100\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=articles\">Antal i lager</u></a></b></td>";
			echo "<td width=\"100\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=storevalue\">Lagervärde</u></a></b></td>";
			/*
			echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
			*/
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

			/*
			$antalManad1 = $this->displayMostSoldArticleManad1Kat($kategori_id);
			$antalManad2 = $this->displayMostSoldArticleManad2Kat($kategori_id);

				if ($antalManad1 == 0 && $antalManad2 == 0) {
					
					$avvikelse = 999;
					
				} else {
				
					$avvikelse = round($antalManad1 / $antalManad2, 2);
				}
			*/
			
			if ($rowcolor == true) {
				$backcolor = "firstrow";
			} else {
				$backcolor = "secondrow";
			}
			$totalvarde += $varde;
			$SummaLager = number_format($varde, 0, ',', ' ');

			echo "<tr>";
			echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?katid=" . $kategori_id . "\">$kategori</a></td>";
			echo "<td class=\"$backcolor\" align=\"center\">$lager</td>";
			echo "<td class=\"$backcolor\" align=\"right\">$SummaLager kr&nbsp;</td>";
			/*
			echo "<td class=\"$backcolor\" align=\"center\">$antalManad1</td>";
			echo "<td class=\"$backcolor\" align=\"center\">$antalManad2</td>";
			echo $this->displayTrend($avvikelse);
			*/
			echo "</tr>";

			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
		
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"5\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
		echo "</tr>";
		
		}
			$formattotalvarde = number_format($totalvarde, 0, ',', ' ');
			// $formattotalvarde = number_format($this->displayOverallStoreStatus(), 0, ',', ' ');
			echo "<tr>";
			echo "<td colspan=\"5\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"5\"><b>Totalt lagervärde just nu: $formattotalvarde kr</td>";
			echo "</tr>";
			echo "</table>";
			// echo number_format($totalvarde, 0, ',', ' ');
	}

	function getCategoryStoreValue($katid) {

	$rowcolor = true;

	$select  = "SELECT Artiklar.artnr, Tillverkare.tillverkare, Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.till_kund, Artiklar.art_id, mostSoldArticles.antalManad1, mostSoldArticles.antalManad2, SUM(Artiklar.art_id*(Artiklar.lagersaldo+Artiklar.till_kund)) AS varde ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "JOIN cyberphoto.Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
	$select .= "JOIN cyberphoto.mostSoldArticles ON Artiklar.artnr = mostSoldArticles.artnr ";
	$select .= "JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "WHERE (Artiklar.lagersaldo > 0 || Artiklar.till_kund > 0) AND Artiklar.art_id > 1 ";
	$select .= "AND Artiklar.kategori_id = '" . $katid . "' ";
	$select .= "GROUP BY Artiklar.artnr ";
	if ($sortby == "") {
		$select .= "ORDER BY varde DESC ";
	} else {
		$select .= "ORDER BY Kategori.kategori ";
	}

	// echo $select;
	// exit;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\"><b>Artikel nr</b></td>";
			echo "<td width=\"550\"><b>Beskrivning</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Antal i lager</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Lagervärde</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
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
			$lagersaldo = ($lagersaldo+$till_kund);
			$totalvarde += $varde;
			$SummaLager = number_format($varde, 0, ',', ' ');

			echo "<tr>";
			echo "<td class=\"$backcolor\">$artnr</td>";
			echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=$artnr\">$tillverkare $beskrivning</a></td>";
			echo "<td class=\"$backcolor\" align=\"center\">$lagersaldo</td>";
			echo "<td class=\"$backcolor\" align=\"right\">$SummaLager kr</td>";
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
		
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"6\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
		echo "</tr>";
		
		}
			$formattotalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>";
			echo "<td colspan=\"6\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"6\"><b>Totalt lagervärde just nu: $formattotalvarde kr</td>";
			echo "</tr>";
			echo "</table>";
			// echo number_format($totalvarde, 0, ',', ' ');
	}

	function getCategoryStoreValueNew($katid) {

	$rowcolor = true;

	// $select  = "SELECT Artiklar.artnr, Tillverkare.tillverkare, Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.till_kund, Artiklar.art_id, mostSoldArticles.antalManad1, mostSoldArticles.antalManad2, SUM(Artiklar.art_id*(Artiklar.lagersaldo+Artiklar.till_kund)) AS varde ";
	// $select  = "SELECT Artiklar.artnr, Tillverkare.tillverkare, Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.till_kund, Artiklar.art_id, SUM(Artiklar.art_id*(Artiklar.lagersaldo+Artiklar.till_kund)) AS varde ";
	$select  = "SELECT Artiklar.artnr, Tillverkare.tillverkare, Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.till_kund, Artiklar.art_id, SUM(Artiklar.art_id*Artiklar.lagersaldo) AS varde ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "JOIN cyberphoto.Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
	// $select .= "JOIN mostSoldArticles ON Artiklar.artnr = mostSoldArticles.artnr ";
	$select .= "JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	// $select .= "WHERE (Artiklar.lagersaldo > 0 || Artiklar.till_kund > 0) AND Artiklar.art_id > 1 ";
	$select .= "WHERE Artiklar.lagersaldo > 0 AND Artiklar.art_id > 1 ";
	$select .= "AND Artiklar.kategori_id = '" . $katid . "' ";
	$select .= "GROUP BY Artiklar.artnr ";
	if ($sortby == "") {
		$select .= "ORDER BY varde DESC ";
	} else {
		$select .= "ORDER BY Kategori.kategori ";
	}

	// echo $select;
	// exit;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\"><b>Artikel nr</b></td>";
			echo "<td width=\"550\"><b>Beskrivning</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Antal i lager</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Lagervärde</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			$antalManad1 = $this->displayMostSoldArticleManad1($artnr);
			$antalManad2 = $this->displayMostSoldArticleManad2($artnr);

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
			$lagersaldo = ($lagersaldo+$till_kund);
			$totalvarde += $varde;
			$SummaLager = number_format($varde, 0, ',', ' ');

			echo "<tr>";
			echo "<td class=\"$backcolor\">$artnr</td>";
			echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=$artnr\">$tillverkare $beskrivning</a></td>";
			echo "<td class=\"$backcolor\" align=\"center\">$lagersaldo</td>";
			echo "<td class=\"$backcolor\" align=\"right\">$SummaLager kr</td>";
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
		
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"6\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
		echo "</tr>";
		
		}
			$formattotalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>";
			echo "<td colspan=\"6\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"6\"><b>Totalt lagervärde just nu: $formattotalvarde kr</td>";
			echo "</tr>";
			echo "</table>";
			// echo number_format($totalvarde, 0, ',', ' ');
	}

	function getTotalSupplierValue($sortby) {
		global $totalstore;

	// echo $lev_id_3;
	$rowcolor = true;

	// $select  = "SELECT Leverantor.grossist, Artiklar.grossist_id, SUM(Artiklar.lagersaldo+Artiklar.till_kund) AS 'lager', SUM(Artiklar.art_id*(Artiklar.lagersaldo+Artiklar.till_kund)) AS varde ";
	$select  = "SELECT Leverantor.grossist, Artiklar.grossist_id, SUM(Artiklar.lagersaldo) AS 'lager', SUM(Artiklar.art_id*Artiklar.lagersaldo) AS varde ";
	// $select  = "SELECT Leverantor.grossist, Artiklar.grossist_id, SUM(Artiklar.lagersaldo+Artiklar.till_kund) AS 'lager', SUM(Artiklar.art_id*(Artiklar.lagersaldo+Artiklar.till_kund)) AS varde, SUM(mostSoldArticles.antalManad1) AS 'tretti', SUM(mostSoldArticles.antalManad2) AS 'sexti' ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "JOIN cyberphoto.Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
	$select .= "JOIN cyberphoto.Leverantor ON Artiklar.grossist_id = Leverantor.grossist_id ";
	// $select .= "JOIN mostSoldArticles ON Artiklar.artnr = mostSoldArticles.artnr ";
	$select .= "JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "WHERE Artiklar.lagersaldo > 0 AND Artiklar.art_id > 1 AND NOT (Artiklar.kategori_id = 314) ";
	// $select .= "GROUP BY Leverantor.grossist ";
	$select .= "GROUP BY Artiklar.grossist_id ";
	if ($sortby == "supplier") {
		$select .= "ORDER BY Leverantor.grossist ASC ";
	} elseif ($sortby == "articles") {
		$select .= "ORDER BY lager DESC ";
	} elseif ($sortby == "storevalue") {
		$select .= "ORDER BY varde DESC ";
	} else {
		$select .= "ORDER BY varde DESC ";
	}

	// echo $select;
	// exit;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"550\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=supplier\">Leverantör</a></u></b></td>";
			echo "<td width=\"100\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=articles\">Antal i lager</a></u></b></td>";
			echo "<td width=\"100\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=storevalue\">Lagervärde</a></u></b></td>";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				echo "<td width=\"100\" align=\"center\"></td>";
			}
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "firstrow";
			} else {
				$backcolor = "secondrow";
			}
			
			$totalvarde += $varde;
			$totallager += $lager;
			$SummaLager = number_format($varde, 0, ',', ' ');

			echo "<tr>";
			echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?grossid=" . $grossist_id . "\">$grossist</a></td>";
			echo "<td class=\"$backcolor\" align=\"center\">$lager</td>";
			echo "<td class=\"$backcolor\" align=\"right\">$SummaLager kr&nbsp;</td>";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				if ($grossist_id == 3) {
					echo "<td align=\"center\"></td>";
				} else {
					echo "<td align=\"center\"></td>";
				}
			}
			echo "</tr>";

			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
		
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"5\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
		echo "</tr>";
		
		}
			$totaldiff = ($totalstore-$totalvarde);
			if ($totaldiff < 0) {
				$goodresult = false;
			} else {
				$goodresult = true;
			}
			$totaldiff = number_format($totaldiff, 0, ',', ' ');
			$formattotalvarde = number_format($totalvarde, 0, ',', ' ');
			$totalstore = number_format($totalstore, 0, ',', ' ');
			// $formattotalvarde = number_format($this->displayOverallStoreStatus(), 0, ',', ' ');
			echo "<td colspan=\"3\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</table>";
			echo "<table>";
			echo "<tr>";
			echo "<td>Totalt: </td>";
			echo "<td><b>$formattotalvarde kr</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>Normal: </td>";
			echo "<td><b>$totalstore kr</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>Diff: </td>";
			if ($goodresult) {
				echo "<td><b><font color=\"green\">$totaldiff kr</font></b></td>";
			} else {
				echo "<td><b><font color=\"red\">$totaldiff kr</font></b></td>";
			}
			echo "</tr>";
			echo "</table>";
			// echo number_format($totalvarde, 0, ',', ' ');
			// echo number_format($this->displayOverallStoreStatus(), 0, ',', ' ');
	}

	function getSupplierStoreValue($grossid,$sortby) {

	$rowcolor = true;

	$select  = "SELECT Artiklar.artnr, Tillverkare.tillverkare, Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.till_kund, Artiklar.art_id, mostSoldArticles.antalManad1, mostSoldArticles.antalManad2, SUM((Artiklar.lagersaldo+Artiklar.till_kund)*Artiklar.art_id) AS varde ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "JOIN cyberphoto.Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
	$select .= "JOIN cyberphoto.mostSoldArticles ON Artiklar.artnr = mostSoldArticles.artnr ";
	$select .= "JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "WHERE (Artiklar.lagersaldo > 0 || Artiklar.till_kund > 0) AND Artiklar.art_id > 1 AND NOT (Artiklar.kategori_id = 314) ";
	$select .= "AND Artiklar.grossist_id = '" . $grossid . "' ";
	$select .= "GROUP BY Artiklar.artnr ";
	if ($sortby == "") {
		$select .= "ORDER BY varde DESC ";
	} else {
		$select .= "ORDER BY Kategori.kategori ";
	}

	// echo $select;
	// exit;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\"><b>Artikel nr</b></td>";
			echo "<td width=\"450\"><b>Beskrivning</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Antal i lager</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Lagervärde</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

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
			$lagersaldo = ($lagersaldo+$till_kund);
			$totalvarde += $varde;
			$SummaLager = number_format($varde, 0, ',', ' ');

			echo "<tr>";
			echo "<td class=\"$backcolor\">$artnr</td>";
			echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=$artnr\">$tillverkare $beskrivning</a></td>";
			echo "<td class=\"$backcolor\" align=\"center\">$lagersaldo</td>";
			echo "<td class=\"$backcolor\" align=\"right\">$SummaLager kr</td>";
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
		
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"6\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
		echo "</tr>";
		
		}
			$formattotalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>";
			echo "<td colspan=\"6\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"6\"><b>Totalt lagervärde just nu: $formattotalvarde kr</td>";
			echo "</tr>";
			echo "</table>";
			// echo number_format($totalvarde, 0, ',', ' ');
			// echo number_format($this->displayOverallStoreStatus(), 0, ',', ' ');
	}

	function getSupplierStoreValueNew($grossid,$sortby) {

	$rowcolor = true;

	// $select  = "SELECT Artiklar.artnr, Tillverkare.tillverkare, Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.till_kund, Artiklar.art_id, mostSoldArticles.antalManad1, mostSoldArticles.antalManad2, SUM((Artiklar.lagersaldo+Artiklar.till_kund)*Artiklar.art_id) AS varde ";
	$select  = "SELECT Artiklar.artnr, Tillverkare.tillverkare, Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.till_kund, Artiklar.art_id, SUM((Artiklar.lagersaldo+Artiklar.till_kund)*Artiklar.art_id) AS varde ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "JOIN cyberphoto.Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
	// $select .= "JOIN mostSoldArticles ON Artiklar.artnr = mostSoldArticles.artnr ";
	$select .= "JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "WHERE (Artiklar.lagersaldo > 0 || Artiklar.till_kund > 0) AND Artiklar.art_id > 1 AND NOT (Artiklar.kategori_id = 314) ";
	$select .= "AND Artiklar.grossist_id = '" . $grossid . "' ";
	$select .= "GROUP BY Artiklar.artnr ";
	if ($sortby == "") {
		$select .= "ORDER BY varde DESC ";
	} else {
		$select .= "ORDER BY Kategori.kategori ";
	}
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		// echo $select;
		// exit;
	}

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\"><b>Artikel nr</b></td>";
			echo "<td width=\"450\"><b>Beskrivning</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Antal i lager</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Lagervärde</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			$antalManad1 = $this->displayMostSoldArticleManad1($artnr);
			$antalManad2 = $this->displayMostSoldArticleManad2($artnr);

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
			$lagersaldo = ($lagersaldo+$till_kund);
			$totalvarde += $varde;
			$SummaLager = number_format($varde, 0, ',', ' ');

			echo "<tr>";
			echo "<td class=\"$backcolor\">$artnr</td>";
			echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=$artnr\">$tillverkare $beskrivning</a></td>";
			echo "<td class=\"$backcolor\" align=\"center\">$lagersaldo</td>";
			echo "<td class=\"$backcolor\" align=\"right\">$SummaLager kr</td>";
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
		
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"6\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
		echo "</tr>";
		
		}
			$formattotalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>";
			echo "<td colspan=\"6\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"6\"><b>Totalt lagervärde just nu: $formattotalvarde kr</td>";
			echo "</tr>";
			echo "</table>";
			// echo number_format($totalvarde, 0, ',', ' ');
			// echo number_format($this->displayOverallStoreStatus(), 0, ',', ' ');
	}

	function getSupplierStoreValueNew2($grossid,$sortby) {

	$rowcolor = true;

	// $select  = "SELECT Artiklar.artnr, Tillverkare.tillverkare, Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.till_kund, Artiklar.art_id, mostSoldArticles.antalManad1, mostSoldArticles.antalManad2, SUM((Artiklar.lagersaldo+Artiklar.till_kund)*Artiklar.art_id) AS varde ";
	// $select  = "SELECT Artiklar.artnr, Tillverkare.tillverkare, Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.till_kund, Artiklar.art_id, SUM((Artiklar.lagersaldo+Artiklar.till_kund)*Artiklar.art_id) AS varde, antalManad1, antalManad2, SUM((Artiklar.lagersaldo/antalManad1)*30) AS Takt ";
	$select  = "SELECT Artiklar.artnr, Tillverkare.tillverkare, Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.till_kund, Artiklar.art_id, SUM(Artiklar.lagersaldo*Artiklar.art_id) AS varde, antalManad1, antalManad2, SUM((Artiklar.lagersaldo/antalManad1)*30) AS Takt ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "JOIN cyberphoto.Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
	$select .= "JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN cyberphoto.mostSoldArticles ON Artiklar.artnr = mostSoldArticles.artnr ";
	$select .= "WHERE Artiklar.lagersaldo > 0 AND Artiklar.art_id > 1 AND NOT (Artiklar.kategori_id = 314) ";
	$select .= "AND Artiklar.grossist_id = '" . $grossid . "' ";
	$select .= "GROUP BY Artiklar.artnr ";
	if ($sortby == "") {
		$select .= "ORDER BY varde DESC ";
	} elseif ($sortby == "varde") {
		$select .= "ORDER BY varde DESC ";
	} elseif ($sortby == "takt") {
		$select .= "ORDER BY Takt DESC ";
	} else {
		$select .= "ORDER BY Kategori.kategori ";
	}
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		// echo $select;
		// exit;
	}

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\"><b>Artikel nr</b></td>";
			echo "<td width=\"350\"><b>Beskrivning</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Antal i lager</b></td>";
			echo "<td width=\"100\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?grossid=" . $grossid . "&sortby=varde\">Lagervärde</a></u></b></td>";
			echo "<td width=\"100\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?grossid=" . $grossid . "&sortby=takt\">Räcker dagar</a></u></b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 30</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Sålda 60</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Trend</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			// $antalManad1 = $this->displayMostSoldArticleManad1($artnr);
			// $antalManad2 = $this->displayMostSoldArticleManad2($artnr);
			if ($antalManad1 == null) {
				$antalManad1 = 0;
			}
			if ($antalManad2 == null) {
				$antalManad2 = 0;
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
			$lagersaldo = ($lagersaldo+$till_kund);
			$totalvarde += $varde;
			$SummaLager = number_format($varde, 0, ',', ' ');

			echo "<tr>";
			echo "<td class=\"$backcolor\">$artnr</td>";
			echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=$artnr\">$tillverkare $beskrivning</a></td>";
			echo "<td class=\"$backcolor\" align=\"center\">$lagersaldo</td>";
			echo "<td class=\"$backcolor\" align=\"right\">$SummaLager kr</td>";
			if ($antalManad1 == 0) {
				echo "<td class=\"$backcolor\" align=\"center\">&#8734;</td>";
			} else {
				echo "<td class=\"$backcolor\" align=\"center\">" . round($Takt,0) . "</td>";
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
		
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"6\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
		echo "</tr>";
		
		}
			$formattotalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>";
			echo "<td colspan=\"6\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"6\"><b>Totalt lagervärde just nu: $formattotalvarde kr</td>";
			echo "</tr>";
			echo "</table>";
			// echo number_format($totalvarde, 0, ',', ' ');
			// echo number_format($this->displayOverallStoreStatus(), 0, ',', ' ');
	}

	function displayOverallStoreStatus() {

	$select = "SELECT SUM(Artiklar.lagersaldo*Artiklar.art_id) AS Pengar ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "WHERE Artiklar.lagersaldo > 0 AND Artiklar.art_id > 1 AND NOT (Artiklar.kategori_id = 314) ";

	// echo $select;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			return $Pengar;
			
			endwhile;
			
		} else {
		
			return 0;
		
		}

	}

	function displayMostSoldArticleManad1($artnr) {

	$select = "SELECT antalManad1 ";
	$select .= "FROM cyberphoto.mostSoldArticles ";
	$select .= "WHERE artnr = '" . $artnr . "' ";

	// echo $select;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			return $antalManad1;
			
			endwhile;
			
		} else {
		
			return 0;
		
		}

	}

	function displayMostSoldArticleManad1Kat($katid) {

	$select = "SELECT SUM(antalManad1) AS Antal ";
	$select .= "FROM cyberphoto.mostSoldArticles ";
	$select .= "JOIN cyberphoto.Artiklar ON mostSoldArticles.artnr = Artiklar.artnr ";
	$select .= "WHERE kategori_id = '" . $katid . "' ";

	// echo $select . "<br>";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			return $Antal;
			
			endwhile;
			
		} else {
		
			return 0;
		
		}

	}

	function displayMostSoldArticleManad2($artnr) {

	$select = "SELECT antalManad2 ";
	$select .= "FROM cyberphoto.mostSoldArticles ";
	$select .= "WHERE artnr = '" . $artnr . "' ";

	// echo $select;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			return $antalManad2;
			
			endwhile;
			
		} else {
		
			return 0;
		
		}

	}

	function displayMostSoldArticleManad2Kat($katid) {

	$select = "SELECT SUM(antalManad2) AS Antal ";
	$select .= "FROM cyberphoto.mostSoldArticles ";
	$select .= "JOIN cyberphoto.Artiklar ON mostSoldArticles.artnr = Artiklar.artnr ";
	$select .= "WHERE kategori_id = '" . $katid . "' ";

	// echo $select . "<br>";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			return $Antal;
			
			endwhile;
			
		} else {
		
			return 0;
		
		}

	}

	// här plockar vi fram beställda produkter på väg in
	function getIncomingFromSupplierValueNew($sortby,$type) {
		global $frandatum, $tilldatum, $tilldatumweek;

	$eurokurs = $this->getEUR();
	$rowcolor = true;

	/*
	$select  = "SELECT Leverantor.grossist, Leverantor.grossist_id, SUM((Inkopsposter.antal - Inkopsposter.levererat) * Artiklar.art_id) AS Summa ";
	$select .= "FROM Artiklar ";
	$select .= "JOIN Inkopsposter ON Inkopsposter.artnr = Artiklar.artnr ";
	$select .= "JOIN Leverantor ON Artiklar.grossist_id = Leverantor.grossist_id ";
	$select .= "WHERE Inkopsposter.levererat < Inkopsposter.antal AND Inkopsposter.antal > 0 ";
	$select .= "GROUP BY Leverantor.grossist ";
	*/
	$select  = "SELECT Leverantor.grossist, Leverantor.grossist_id, SUM((Inkopsposter.antal - Inkopsposter.levererat) * Inkopsposter.pris) AS Summa ";
	$select .= "FROM cyberphoto.Inkopsposter ";
	$select .= "JOIN cyberphoto.Leverantor ON Inkopsposter.leverantor_id = Leverantor.grossist_id ";
	$select .= "WHERE Inkopsposter.levererat < Inkopsposter.antal AND Inkopsposter.antal > 0 ";
	if ($type == "month") {
		$select .= "AND Inkopsposter.ber_lev_datum BETWEEN '" . $frandatum . "' AND '" . $tilldatum . "' ";
	} elseif ($type == "twoweek") {
		$select .= "AND Inkopsposter.ber_lev_datum BETWEEN '" . $frandatum . "' AND '" . $tilldatumweek . "' ";
	} elseif ($type == "late") {
		$select .= "AND Inkopsposter.ber_lev_datum <= '" . $frandatum . "' ";
	} elseif ($type == "today") {
		$select .= "AND Inkopsposter.ber_lev_datum LIKE '" . $frandatum . "%' ";
	} elseif ($type == "nodate") {
		$select .= "AND Inkopsposter.ber_lev_datum LIKE '2100-01-01%' ";
	}
	$select .= "GROUP BY Leverantor.grossist ";

	if ($sortby == "supplier") {
		$select .= "ORDER BY Leverantor.grossist ASC ";
	} elseif ($sortby == "articles") {
		$select .= "ORDER BY lager DESC ";
	} elseif ($sortby == "storevalue") {
		$select .= "ORDER BY Summa DESC ";
	} else {
		$select .= "ORDER BY Summa DESC ";
	}

	// echo $select;
	// exit;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"350\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=supplier\">Leverantör</a></u></b></td>";
			// echo "<td width=\"100\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=articles\">Antal i lager</a></u></b></td>";
			echo "<td width=\"100\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=storevalue\">Värde SEK</a></u></b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Värde EUR</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "firstrow";
			} else {
				$backcolor = "secondrow";
			}
			
			if ($grossist_id == 12 || $grossist_id == 48 || $grossist_id == 146 || $grossist_id == 190 || $grossist_id == 276 || $grossist_id == 282) {
				$totalvardeEUR += ($Summa/$eurokurs);
			} else {
				$totalvarde += $Summa;
			}
			// $totallager += $lager;
			$SummaLager = number_format($Summa, 0, ',', ' ');
			$SummaLagerEuro = number_format(($Summa/$eurokurs), 0, ',', ' ');

			echo "<tr>";
			if ($type == "month") {
				echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?period=month&grossid=" . $grossist_id . "\">$grossist</a></td>";
			} elseif ($type == "twoweek") {
				echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?period=twoweek&grossid=" . $grossist_id . "\">$grossist</a></td>";
			} elseif ($type == "late") {
				echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?period=late&grossid=" . $grossist_id . "\">$grossist</a></td>";
			} elseif ($type == "today") {
				echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?period=today&grossid=" . $grossist_id . "\">$grossist</a></td>";
			} elseif ($type == "nodate") {
				echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?period=nodate&grossid=" . $grossist_id . "\">$grossist</a></td>";
			} else {
				echo "<td class=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?period=total&grossid=" . $grossist_id . "\">$grossist</a></td>";
			}
			// echo "<td class=\"$backcolor\" align=\"center\">$lager</td>";
			if ($grossist_id == 12 || $grossist_id == 48 || $grossist_id == 146 || $grossist_id == 190 || $grossist_id == 276 || $grossist_id == 282) {
				echo "<td class=\"$backcolor\" align=\"right\">&nbsp;</td>";
			} else {
				echo "<td class=\"$backcolor\" align=\"right\">$SummaLager SEK&nbsp;</td>";
			}
			
			if ($grossist_id == 12 || $grossist_id == 48 || $grossist_id == 146 || $grossist_id == 190 || $grossist_id == 276 || $grossist_id == 282) {
				echo "<td class=\"$backcolor\" align=\"right\">$SummaLagerEuro EUR&nbsp;</td>";
			} else {
				echo "<td class=\"$backcolor\" align=\"right\">&nbsp;</td>";
			}
			echo "</tr>";

			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
		
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"5\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
		echo "</tr>";
		
		}
			$totaldiff = ($totalstore-$totalvarde);
			if ($totaldiff < 0) {
				$goodresult = false;
			} else {
				$goodresult = true;
			}
			$totaldiff = number_format($totaldiff, 0, ',', ' ');
			$formattotalvarde = number_format($totalvarde, 0, ',', ' ');
			$formattotalvardeEUR = number_format($totalvardeEUR, 0, ',', ' ');
			$totalstore = number_format($totalstore, 0, ',', ' ');

			echo "<td colspan=\"3\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			// echo "</table>";
			// echo "<table>";
			echo "<tr>";
			echo "<td align=\"right\"><b>Totalt:</b></td>";
			echo "<td align=\"right\"><b>$formattotalvarde SEK</b></td>";
			echo "<td align=\"right\"><b>$formattotalvardeEUR EUR&nbsp;</b></td>";
			echo "</tr>";
			echo "</table>";

	}

	// här kollar vi upp i detal vilka produkter som är på väg in i lager
	function getIncomingFromSupplierValueDetail($grossid,$sortby) {
		global $frandatum, $tilldatum, $tilldatumweek, $period;

	$eurokurs = $this->getEUR();
	$rowcolor = true;

	/*
	$select  = "SELECT Artiklar.artnr, Artiklar.beskrivning, Inkopsposter.antal, Artiklar.art_id, SUM((Inkopsposter.antal - Inkopsposter.levererat) * Artiklar.art_id) AS Summa, SUM(Inkopsposter.antal - Inkopsposter.levererat) AS SummaAntal ";
	$select .= "FROM Artiklar ";
	$select .= "JOIN Inkopsposter ON Inkopsposter.artnr = Artiklar.artnr ";
	$select .= "WHERE Inkopsposter.levererat < Inkopsposter.antal AND Inkopsposter.antal > 0 ";
	$select .= "AND Artiklar.grossist_id = '" . $grossid . "' ";
	$select .= "GROUP BY Artiklar.artnr ";
	*/

	$select  = "SELECT Artiklar.artnr, Artiklar.beskrivning, Inkopsposter.antal, Artiklar.art_id, SUM((Inkopsposter.antal - Inkopsposter.levererat) * Inkopsposter.pris) AS Summa, SUM(Inkopsposter.antal - Inkopsposter.levererat) AS SummaAntal ";
	$select .= "FROM cyberphoto.Inkopsposter ";
	$select .= "JOIN cyberphoto.Leverantor ON Inkopsposter.leverantor_id = Leverantor.grossist_id ";
	$select .= "JOIN cyberphoto.Artiklar ON Inkopsposter.artnr = Artiklar.artnr ";
	$select .= "WHERE Inkopsposter.levererat < Inkopsposter.antal AND Inkopsposter.antal > 0 ";
	$select .= "AND Inkopsposter.leverantor_id = '" . $grossid . "' ";
	if ($period == "month") {
		$select .= "AND Inkopsposter.ber_lev_datum BETWEEN '" . $frandatum . "' AND '" . $tilldatum . "' ";
	} elseif ($period == "twoweek") {
		$select .= "AND Inkopsposter.ber_lev_datum BETWEEN '" . $frandatum . "' AND '" . $tilldatumweek . "' ";
	} elseif ($period == "late") {
		$select .= "AND Inkopsposter.ber_lev_datum <= '" . $frandatum . "' ";
	} elseif ($period == "today") {
		$select .= "AND Inkopsposter.ber_lev_datum LIKE '" . $frandatum . "%' ";
	} elseif ($period == "nodate") {
		$select .= "AND Inkopsposter.ber_lev_datum LIKE '2100-01-01%' ";
	}
	$select .= "GROUP BY Artiklar.artnr ";

	if ($sortby == "") {
		$select .= "ORDER BY Summa DESC ";
	} else {
		$select .= "ORDER BY Kategori.kategori ";
	}

	// echo $select;
	// exit;

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\"><b>Artikel nr</b></td>";
			echo "<td width=\"450\"><b>Beskrivning</b></td>";
			echo "<td width=\"75\"><b>Antal best.</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Inpris</b></td>";
			echo "<td width=\"100\" align=\"center\"><b>Värde SEK</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "firstrow";
			} else {
				$backcolor = "secondrow";
			}

			$totalvarde += $Summa;
			$SummaLager = number_format($Summa, 0, ',', ' ');

			echo "<tr>";
			echo "<td class=\"$backcolor\">$artnr</td>";
			echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=$artnr\">$tillverkare $beskrivning</a></td>";
			echo "<td class=\"$backcolor\" align=\"center\">$SummaAntal</td>";
			echo "<td class=\"$backcolor\" align=\"right\">" . number_format($art_id, 0, ',', ' ') . " SEK</td>";
			echo "<td class=\"$backcolor\" align=\"right\">$SummaLager SEK</td>";
			echo "</tr>";

			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
		
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"6\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
		echo "</tr>";
		
		}
			$formattotalvarde = number_format($totalvarde, 0, ',', ' ');
			echo "<tr>";
			echo "<td colspan=\"6\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"6\"><b>Totalt: $formattotalvarde kr</td>";
			echo "</tr>";
			echo "</table>";

	}

	function addStockValue($value) {

		$insert = "INSERT INTO cyberadmin.stockvalue (stockvalue_Value,stockvalue_IP) values ('" . $value . "','" . $_SERVER['REMOTE_ADDR'] . "')";
		// echo $insert;
		mysqli_query(Db::getConnection(true), $insert);

	}

	function addStockAllocatedValue($value) {

		$insert = "INSERT INTO cyberadmin.stockvalue (stockvalue_Value,stockvalue_IP,stockvalue_Type) values ('" . $value . "','" . $_SERVER['REMOTE_ADDR'] . "','1')";
		// mysqli_query($this->conn_my2, $insert);
		mysqli_query(Db::getConnection(true), $insert);

	}

	function addStockValueOngoing($value) {

		$insert = "INSERT INTO cyberadmin.stockvalue (stockvalue_Value,stockvalue_IP,stockvalue_Type) values ('" . $value . "','" . $_SERVER['REMOTE_ADDR'] . "','2')";
		// mysqli_query($this->conn_my2, $insert);
		mysqli_query(Db::getConnection(true), $insert);

	}
	
	function showStockValueGraph() {

	$startrad = 1;
	?>
		<script type='text/javascript' src='https://www.google.com/jsapi'></script>
		<script type='text/javascript'>
		  google.load('visualization', '1', {'packages':['annotatedtimeline']});
		  google.setOnLoadCallback(drawChart);
		  function drawChart() {
			var data = new google.visualization.DataTable();
			data.addColumn('date', 'Date');
			data.addColumn('number', 'Lagervärde');
			data.addRows([
	<?php

		// $select = "SELECT DATE_FORMAT(stockvalue_Date, '%Y-%m-%d') AS Datum, stockvalue_Value FROM stockvalue ORDER BY Datum DESC ";
		$select = "SELECT DATE_FORMAT(stockvalue_Date, '%Y-%m-%d') AS Datum, stockvalue_Value FROM cyberadmin.stockvalue WHERE stockvalue_Type = 0 ORDER BY Datum DESC ";
		
		// $res = mysqli_query($this->conn_my2, $select);
		$res = mysqli_query(Db::getConnection(), $select);
		
			$num_rows = mysqli_num_rows($res);
			// echo "$num_rows Rows\n";
			// echo mysqli_num_rows($res);
			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
					$pyear = date("Y", strtotime($Datum));
					$pmonth = date("n", strtotime($Datum))-1;
					$pday = date("j", strtotime($Datum));
					
					if ($num_rows == $startrad) {
						echo "\t[new Date($pyear, $pmonth ,$pday), $stockvalue_Value]\n";
					} else {
						echo "\t[new Date($pyear, $pmonth ,$pday), $stockvalue_Value],\n";
					}
				
				$startrad++;

				endwhile;

			} else {
			
				echo "";
			}
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
	?>
			]);
			
			var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
			chart.draw(data, {'colors': ['green'],displayAnnotations: true, thickness: 1, fill: 50, displayExactValues: true, 'scaleType': 'maximized'} );
		  }
		</script>
	<?php
		} else {
	?>
			]);
			
			var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
			chart.draw(data, {'colors': ['green'],displayAnnotations: true, thickness: 1, fill: 50, displayExactValues: true, 'scaleType': 'maximized'} );
		  }
		</script>
	<?php
		}

	}

	function showStockDiffGraph() {

	$startrad = 1;
	?>
		<script type='text/javascript' src='https://www.google.com/jsapi'></script>
		<script type='text/javascript'>
		  google.load('visualization', '1', {'packages':['annotatedtimeline']});
		  google.setOnLoadCallback(drawChart);
		  function drawChart() {
			var data = new google.visualization.DataTable();
			data.addColumn('date', 'Date');
			data.addColumn('number', 'Allokerat värde');
			data.addRows([
	<?php

		// $select = "SELECT DATE_FORMAT(stockvalue_Date, '%Y-%m-%d') AS Datum, stockvalue_Value FROM stockvalue ORDER BY Datum DESC ";
		$select = "SELECT DATE_FORMAT(stockvalue_Date, '%Y-%m-%d') AS Datum, stockvalue_Value FROM cyberadmin.stockvalue WHERE stockvalue_Type = 1 ORDER BY Datum DESC ";
		
		// $res = mysqli_query($this->conn_my2, $select);
		$res = mysqli_query(Db::getConnection(), $select);
		
			$num_rows = mysqli_num_rows($res);
			// echo "$num_rows Rows\n";
			// echo mysqli_num_rows($res);
			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
					$pyear = date("Y", strtotime($Datum));
					$pmonth = date("n", strtotime($Datum))-1;
					$pday = date("j", strtotime($Datum));
					
					if ($num_rows == $startrad) {
						echo "\t[new Date($pyear, $pmonth ,$pday), $stockvalue_Value]\n";
					} else {
						echo "\t[new Date($pyear, $pmonth ,$pday), $stockvalue_Value],\n";
					}
				
				$startrad++;

				endwhile;

			} else {
			
				echo "";
			}

	?>
			]);

			var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div_2'));
			chart.draw(data, {displayAnnotations: true, thickness: 1, fill: 50, displayExactValues: true, 'scaleType': 'maximized'} );
		  }
		</script>
		
	<?php

	}

	function displayHistoryStore() {

	$select = "SELECT stockvalue_Date, stockvalue_Value ";
	$select .= "FROM cyberadmin.stockvalue ";
	$select .= "WHERE stockvalue_Type = 0 "; // ta bara med lagervärde, ej allokerat
	$select .= "ORDER BY stockvalue_Date DESC ";
	$select .= "LIMIT 20 ";

	// echo $select;

	echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n";
	echo "\t<tr>\n";
	echo "\t\t<td width=\"85\"><b>Datum</b></td>\n";
	echo "\t\t<td><b>Lagervärde</b></td>\n";
	echo "\t</tr>\n";

	// $res = mysqli_query($this->conn_my2, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			$stockvalue_Date = date("Y-m-d", strtotime($stockvalue_Date));
			$stockvalue_Value = number_format($stockvalue_Value, 0, ',', ' ');
			
			echo "\t<tr>\n";
			echo "\t\t<td>$stockvalue_Date</td>\n";
			echo "\t\t<td>$stockvalue_Value</td>\n";
			echo "\t</tr>\n";
			
			endwhile;
			
		} else {
		
			echo "";
		
		}

	echo "</table>";
	}

}

?>

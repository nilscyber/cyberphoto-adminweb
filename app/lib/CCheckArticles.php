<?php


Class CCheckArticles {

	var $conn_my;

	function __construct() {

		$this->conn_my = Db::getConnection();
	}

	function getProductsWithNoFilter($grupp) {

	$rowcolor = true;
	$countrow = 0;

	$select  = "SELECT artnr, kategori, Tillverkare, beskrivning, lagersaldo ";
	$select .= "FROM Artiklar ";
	$select .= "JOIN Tillverkare ON Tillverkare.tillverkar_id = Artiklar.tillverkar_id ";
	$select .= "JOIN Kategori ON Kategori.kategori_id = Artiklar.kategori_id ";
	if ($grupp == "digikam") {
		$select .= "WHERE Artiklar.kategori_id IN(392,393,394) ";
	} elseif ($grupp == "system") {
		$select .= "WHERE Artiklar.kategori_id IN(395) ";
	} elseif ($grupp == "objektiv") {
		$select .= "WHERE Artiklar.kategori_id IN(42,43,45,50,373,374,375,376) AND NOT (Artiklar.beskrivning LIKE '%objektivmodul%') ";
	} elseif ($grupp == "mobil") {
		$select .= "WHERE Artiklar.kategori_id IN(336) ";
	} elseif ($grupp == "video") {
		$select .= "WHERE Artiklar.kategori_id IN(7,213,329,346,402) ";
	} elseif ($grupp == "padda") {
		$select .= "WHERE Artiklar.kategori_id IN(748) ";
	} else {
		$select .= "WHERE Artiklar.kategori_id IN(100000) ";
	}
	$select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
	$select .= "AND ((spec1 IS null AND spec2 IS null AND  spec3 IS null AND spec4 IS null AND spec5 IS null AND spec6 IS null AND spec7 IS null AND spec8 IS null ";
	$select .= "AND spec9 IS null AND spec10 IS null AND spec11 IS null AND spec12 IS null) OR ";
	$select .= "(spec1 =0 AND spec2 =0 AND  spec3 =0 AND spec4 =0 AND spec5 =0 AND spec6 =0 AND spec7 =0 AND spec8 =0 ";
	$select .= "AND spec9 =0 AND spec10 =0 AND spec11 =0 AND spec12 =0)) ";
	$select .= "ORDER BY Kategori ASC, Tillverkare ASC, beskrivning ASC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $select;
		}

	$res = mysqli_query($this->conn_my, $select);

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"210\" align=\"left\"><b>Kategori</b></td>";
			echo "<td width=\"610\" align=\"left\"><b>Produkt</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Lagersaldo</b></td>";
			echo "</tr>";

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "#E8E8E8";
			} else {
				$backcolor = "#FFCF9F";
			}
			
			$lagersaldosum += $lagersaldo;

			echo "<tr>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\">$artnr</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\">$kategori</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"/info.php?article=$artnr\">$Tillverkare $beskrivning</a></td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$lagersaldo</td>";
			echo "</tr>";

			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
		
			$countrow ++;
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"4\"><font color=\"#33CC33\"><b>Det finns inga produkter i listan = UTMÄRKT!</b></td>";
		echo "</tr>";
		
		}
			echo "<tr>";
			echo "<td colspan=\"4\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"3\"><b>Antal produkter: $countrow st</td>";
			echo "<td align=\"center\"><b>$lagersaldosum</b></td>";
			echo "</tr>";
			echo "</table>";
	}

	function getSystemkamWithNoHighLight() {

	$rowcolor = true;
	$countrow = 0;

	$select  = "SELECT artnr, Tillverkare, beskrivning, motljsk, ccd, zoom_digikam ";
	$select .= "FROM Artiklar ";
	$select .= "JOIN Tillverkare ON Tillverkare.tillverkar_id = Artiklar.tillverkar_id ";
	$select .= "WHERE Artiklar.kategori_id IN(395) ";
	$select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
	$select .= "AND (motljsk IS null OR ccd IS null OR zoom_digikam IS null OR motljsk = '' OR ccd = '' OR zoom_digikam = '') ";
	$select .= "ORDER BY Tillverkare ASC, beskrivning ASC ";

	// echo $select;
	// exit;

	$res = mysqli_query($this->conn_my, $select);

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"610\" align=\"left\"><b>Produkt</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Skärm</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Kamera</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Zoom</b></td>";
			echo "</tr>";

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "#E8E8E8";
			} else {
				$backcolor = "#FFCF9F";
			}
			
			echo "<tr>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\">$artnr</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"/info.php?article=$artnr\">$Tillverkare $beskrivning</a></td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$motljsk</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$ccd</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$zoom_digikam</td>";
			echo "</tr>";

			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
		
			$countrow ++;
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"5\"><font color=\"#33CC33\"><b>Det finns inga produkter i listan = UTMÄRKT!</b></td>";
		echo "</tr>";
		
		}
			echo "<tr>";
			echo "<td colspan=\"5\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"5\"><b>Antal produkter: $countrow st</td>";
			echo "</tr>";
			echo "</table>";
	}

	function getDigikamWithNoHighLight() {

	$rowcolor = true;
	$countrow = 0;

	$select  = "SELECT artnr, Tillverkare, beskrivning, motljsk, ccd, zoom_digikam ";
	$select .= "FROM Artiklar ";
	$select .= "JOIN Tillverkare ON Tillverkare.tillverkar_id = Artiklar.tillverkar_id ";
	$select .= "WHERE Artiklar.kategori_id IN(392,393,394) ";
	$select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
	// $select .= "AND (motljsk IS null OR ccd IS null OR zoom_digikam IS null) ";
	// $select .= "AND (motljsk = '' OR ccd = '' OR zoom_digikam = '') ";
	$select .= "AND (motljsk IS null OR ccd IS null OR zoom_digikam IS null OR motljsk = '' OR ccd = '' OR zoom_digikam = '') ";
	$select .= "ORDER BY Tillverkare ASC, beskrivning ASC ";

	// echo $select;
	// exit;

	$res = mysqli_query($this->conn_my, $select);

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"610\" align=\"left\"><b>Produkt</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Skärm</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Kamera</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Zoom</b></td>";
			echo "</tr>";

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "#E8E8E8";
			} else {
				$backcolor = "#FFCF9F";
			}
			
			echo "<tr>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\">$artnr</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"/info.php?article=$artnr\">$Tillverkare $beskrivning</a></td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$motljsk</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$ccd</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$zoom_digikam</td>";
			echo "</tr>";

			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
		
			$countrow ++;
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"5\"><font color=\"#33CC33\"><b>Det finns inga produkter i listan = UTMÄRKT!</b></td>";
		echo "</tr>";
		
		}
			echo "<tr>";
			echo "<td colspan=\"5\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"5\"><b>Antal produkter: $countrow st</td>";
			echo "</tr>";
			echo "</table>";
	}

	function getLensWithNoHighLight() {

	$rowcolor = true;
	$countrow = 0;

	$select  = "SELECT artnr, Tillverkare, beskrivning, motljsk, ccd ";
	$select .= "FROM Artiklar ";
	$select .= "JOIN Tillverkare ON Tillverkare.tillverkar_id = Artiklar.tillverkar_id ";
	$select .= "WHERE Artiklar.kategori_id IN(42,43,45,50,373,374,375,376) ";
	$select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
	$select .= "AND (filterd IS null OR motljsk IS null OR filterd = '' OR motljsk = '') ";
	$select .= "ORDER BY Tillverkare ASC, beskrivning ASC ";

	// echo $select;
	// exit;

	$res = mysqli_query($this->conn_my, $select);

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"610\" align=\"left\"><b>Produkt</b></td>";
			echo "<td width=\"95\" align=\"center\"><b>Filterdiameter</b></td>";
			echo "<td width=\"95\" align=\"center\"><b>Motljusskydd</b></td>";
			echo "</tr>";

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "#E8E8E8";
			} else {
				$backcolor = "#FFCF9F";
			}
			
			echo "<tr>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\">$artnr</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"/info.php?article=$artnr\">$Tillverkare $beskrivning</a></td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$filterd</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$motljsk</td>";
			echo "</tr>";

			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
		
			$countrow ++;
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"4\"><font color=\"#33CC33\"><b>Det finns inga produkter i listan = UTMÄRKT!</b></td>";
		echo "</tr>";
		
		}
			echo "<tr>";
			echo "<td colspan=\"4\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"4\"><b>Antal produkter: $countrow st</td>";
			echo "</tr>";
			echo "</table>";
	}

	function getMobileWithNoHighLight() {

	$rowcolor = true;
	$countrow = 0;

	$select  = "SELECT artnr, Tillverkare, beskrivning, motljsk, ccd ";
	$select .= "FROM Artiklar ";
	$select .= "JOIN Tillverkare ON Tillverkare.tillverkar_id = Artiklar.tillverkar_id ";
	$select .= "WHERE Artiklar.kategori_id IN(336) ";
	$select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
	$select .= "AND (motljsk IS null OR ccd IS null OR motljsk = '' OR ccd = '') ";
	$select .= "ORDER BY Tillverkare ASC, beskrivning ASC ";

	// echo $select;
	// exit;

	$res = mysqli_query($this->conn_my, $select);

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"610\" align=\"left\"><b>Produkt</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Skärm</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Kamera</b></td>";
			echo "</tr>";

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "#E8E8E8";
			} else {
				$backcolor = "#FFCF9F";
			}
			
			echo "<tr>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\">$artnr</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"/info.php?article=$artnr\">$Tillverkare $beskrivning</a></td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$motljsk</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$ccd</td>";
			echo "</tr>";

			if ($rowcolor == true) {
				$row = true;
				$rowcolor = false;
			} else {
				$row = false;
				$rowcolor = true;
			}
		
			$countrow ++;
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"4\"><font color=\"#33CC33\"><b>Det finns inga produkter i listan = UTMÄRKT!</b></td>";
		echo "</tr>";
		
		}
			echo "<tr>";
			echo "<td colspan=\"4\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"4\"><b>Antal produkter: $countrow st</td>";
			echo "</tr>";
			echo "</table>";
	}

	function findIfPac($artnr) {

	$select = "SELECT artnr FROM Artiklar WHERE artnr = '" . $artnr . "' AND ej_med = 0 AND utgangen = 0 ";

	$res = mysqli_query($this->conn_my, $select);

	$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
		
			return true;
			
		} else {
		
			return false;
		
		}

	}

	function findIfPacIsInactive($artnr) {

	$select  = "SELECT artnr ";
	$select .= "FROM Artiklar ";
	$select .= "WHERE artnr = '" . $artnr . "' AND (ej_med = -1 OR utgangen = -1) ";

	$res = mysqli_query($this->conn_my, $select);

	$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
		
			return true;
			
		} else {
		
			return false;
		
		}

	}

	function displayArtWithNoPac($grupp) {

	$rowcolor = true;
	$countrow = 0;

	$select  = "SELECT artnr, Tillverkare, beskrivning, demo ";
	$select .= "FROM Artiklar ";
	$select .= "JOIN Tillverkare ON Tillverkare.tillverkar_id = Artiklar.tillverkar_id ";
	if ($grupp == "digikam") {
		$select .= "WHERE Artiklar.kategori_id IN(392,393,394) ";
	} elseif ($grupp == "system") {
		$select .= "WHERE Artiklar.kategori_id IN(395) ";
	} elseif ($grupp == "objektiv") {
		$select .= "WHERE Artiklar.kategori_id IN(42,43,45,50,373,374,375,376) ";
	} elseif ($grupp == "mobil") {
		$select .= "WHERE Artiklar.kategori_id IN(336) ";
	} elseif ($grupp == "video") {
		$select .= "WHERE Artiklar.kategori_id IN(7,213,329,346,402) ";
	} elseif ($grupp == "padda") {
		$select .= "WHERE Artiklar.kategori_id IN(748) ";
	} else {
		$select .= "WHERE Artiklar.kategori_id IN(100000) ";
	}
	// $select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
	$select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) AND utpris > 1 ";
	if ($grupp == "system") {
		$select .= "AND NOT artnr = '1100d1855kit' ";
	}
	if ($grupp == "objektiv") {
		$select .= "AND NOT filterd = 99 ";
	}
	$select .= "AND NOT Artiklar.tillverkar_id = 5 "; // vi ska inte ha värdepaket på leica prylar, därför denna rad.....
	$select .= "ORDER BY Tillverkare ASC, beskrivning ASC ";

	// echo $select;
	// exit;

	$res = mysqli_query($this->conn_my, $select);

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"130\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"610\" align=\"left\"><b>Produkt</b></td>";
			echo "<td width=\"35\" align=\"center\"><b>&nbsp;</b></td>";
			echo "<td width=\"250\" align=\"center\"><b>&nbsp;</b></td>";
			echo "</tr>";

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			$artnrpac = $artnr . "pac";
			$kollaifpac = $this->findIfPac($artnrpac);
			$kollaifIsInactive = $this->findIfPacIsInactive($artnrpac);
			
			if (!$kollaifpac && $demo != -1) {
			
				if ($rowcolor == true) {
					$backcolor = "#E8E8E8";
				} else {
					$backcolor = "#FFCF9F";
				}
			
				echo "<tr>";
				echo "<td bgcolor=\"$backcolor\" align=\"left\">$artnr</td>";
				echo "<td bgcolor=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"/info.php?article=$artnr\">$Tillverkare $beskrivning</a></td>";
				echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>";
				if ($kollaifIsInactive) {
					echo "<td align=\"center\">Paket finns men är \"Ej med\" eller utgånget</td>";
				} else {
					echo "<td align=\"center\"></td>";
				}
				echo "</tr>";

				if ($rowcolor == true) {
					$row = true;
					$rowcolor = false;
				} else {
					$row = false;
					$rowcolor = true;
				}
		
				$countrow ++;
			}
			
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"5\"><font color=\"#33CC33\"><b>Det finns inga produkter i listan = UTMÄRKT!</b></td>";
		echo "</tr>";
		
		}
			echo "<tr>";
			echo "<td colspan=\"5\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"5\"><b>Antal produkter: $countrow st</td>";
			echo "</tr>";
			echo "</table>";
	}

	function displayArtWithNoTekData($grupp) {

		$rowcolor = true;
		$countrow = 0;

		// $select  = "SELECT Artiklar.artnr as dsfsdf, Tillverkare, beskrivning, demo, Tekniska_data.artnr, Info_page.artnr_tekniska_data ";
		$select  = "SELECT Artiklar.artnr as dsfsdf, Tillverkare, beskrivning, demo ";
		$select .= "FROM Artiklar ";
		$select .= "JOIN Tillverkare ON Tillverkare.tillverkar_id = Artiklar.tillverkar_id ";
		if ($grupp == "digikam" || $grupp == "system") {
			// $select .= "LEFT JOIN Tekniska_data ON Artiklar.artnr = Tekniska_data.artnr ";
			$select .= "LEFT JOIN Tekn_cameras ON Artiklar.artnr = Tekn_cameras.artnr ";
		} elseif ($grupp == "video") {
			// $select .= "LEFT JOIN Videokameror ON Artiklar.artnr = Videokameror.artnr ";
			$select .= "LEFT JOIN Tekn_video ON Artiklar.artnr = Tekn_video.artnr ";
		} elseif ($grupp == "objektiv") {
			// $select .= "LEFT JOIN Tekn_objektiv ON Artiklar.artnr = Tekn_objektiv.artnr ";
			$select .= "LEFT JOIN Tekn_lenses ON Artiklar.artnr = Tekn_lenses.artnr ";
		} elseif ($grupp == "mobil") {
			// $select .= "LEFT JOIN Tekn_objektiv ON Artiklar.artnr = Tekn_objektiv.artnr ";
			$select .= "LEFT JOIN Tekn_mobile ON Artiklar.artnr = Tekn_mobile.artnr ";
		}
		$select .= "LEFT JOIN Info_page ON Artiklar.artnr = Info_page.artnr  ";
		if ($grupp == "digikam") {
			$select .= "WHERE Artiklar.kategori_id IN(392,393,394) ";
		} elseif ($grupp == "system") {
			$select .= "WHERE Artiklar.kategori_id IN(395) ";
		} elseif ($grupp == "objektiv") {
			$select .= "WHERE Artiklar.kategori_id IN(42,43,45,50,373,374,375,376) ";
			$select .= "AND NOT Artiklar.tillverkar_id = 425 "; // visa INTE Lensbay enligt RK
		} elseif ($grupp == "mobil") {
			$select .= "WHERE Artiklar.kategori_id IN(336) ";
		} elseif ($grupp == "video") {
			$select .= "WHERE Artiklar.kategori_id IN(7,213,329,346,402) ";
		} else {
			$select .= "WHERE Artiklar.kategori_id IN(100000) ";
		}
		// $select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
		// $select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
		$select .= "AND ej_med=0 AND demo=0 AND (utgangen=0 OR lagersaldo > 0) ";
		if ($grupp == "digikam" || $grupp == "system") {
			// $select .= "AND (Tekniska_data.artnr IS NULL AND Info_page.artnr_tekniska_data IS NULL) ";
			$select .= "AND (Tekn_cameras.artnr IS NULL AND Info_page.artnr_tekniska_data IS NULL) ";
		} elseif ($grupp == "video") {
			// $select .= "AND (Videokameror.artnr IS NULL AND Info_page.artnr_tekniska_data IS NULL) ";
			$select .= "AND (Tekn_video.artnr IS NULL AND Info_page.artnr_tekniska_data IS NULL) ";
		} elseif ($grupp == "objektiv") {
			// $select .= "AND (Tekn_objektiv.artnr IS NULL AND Info_page.artnr_tekniska_data IS NULL) ";
			$select .= "AND (Tekn_lenses.artnr IS NULL AND Info_page.artnr_tekniska_data IS NULL) ";
		} elseif ($grupp == "mobil") {
			// $select .= "AND (Tekn_objektiv.artnr IS NULL AND Info_page.artnr_tekniska_data IS NULL) ";
			$select .= "AND (Tekn_mobile.artnr IS NULL AND Info_page.artnr_tekniska_data IS NULL) ";
		}
		$select .= "ORDER BY Tillverkare ASC, beskrivning ASC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = mysqli_query($this->conn_my, $select);

				echo "<table>";
				echo "<tr>";
				echo "<td width=\"130\" align=\"left\"><b>Artnr</b></td>";
				echo "<td width=\"610\" align=\"left\"><b>Produkt</b></td>";
				echo "<td width=\"35\" align=\"center\"><b>&nbsp;</b></td>";
				echo "</tr>";

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
				// $artnrpac = $artnr . "pac";
				// $kollaifpac = $this->findIfPac($artnrpac);
				
				// if (!$kollaifpac && $demo != -1) {
				
					if ($rowcolor == true) {
						$backcolor = "#E8E8E8";
					} else {
						$backcolor = "#FFCF9F";
					}
				
						echo "<tr>";
						echo "<td bgcolor=\"$backcolor\" align=\"left\">$dsfsdf</td>";
						echo "<td bgcolor=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"/info.php?article=$dsfsdf\">$Tillverkare $beskrivning</a></td>";
						echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>";
						echo "</tr>";

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
			
					$countrow ++;
				// }
				
				endwhile;
				
			} else {
			
			echo "<tr>";
			echo "<td colspan=\"3\"><font color=\"#33CC33\"><b>Det finns inga produkter i listan = UTMÄRKT!</b></td>";
			echo "</tr>";
			
			}
				echo "<tr>";
				echo "<td colspan=\"3\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=\"3\"><b>Antal produkter: $countrow st</td>";
				echo "</tr>";
				echo "</table>";
	}

	function notInFinland($norway = false) {

		$rowcolor = true;
		$countrow = 0;
		$current_katcount = 0;

		$select  = "SELECT Artiklar.artnr, Artiklar.m_product_id, Kategori.Kategori, CONCAT(tillv.tillverkare,' ',Artiklar.beskrivning) AS Benamning, Kategori.kategori_id ";
		$select .= "FROM Artiklar ";
		if (!$norway) {
			$select .= "JOIN Artiklar_fi art_fi ON art_fi.artnr_fi = Artiklar.artnr ";
		}
		$select .= "JOIN Tillverkare tillv ON tillv.tillverkar_id = Artiklar.tillverkar_id ";
		$select .= "JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id  ";
		if ($norway) {
			$select .= "WHERE (Artiklar.demo = 0 AND Artiklar.ej_med = 0 AND Artiklar.utgangen = 0) AND Artiklar.ej_med_no = -1 ";
			include ("std_instore_special_no.php");
		} else {
			$select .= "WHERE (Artiklar.demo = 0 AND Artiklar.ej_med = 0 AND Artiklar.utgangen = 0) AND art_fi.ej_med_fi = -1 ";
			include ("std_instore_special_fi.php");
		}
		$select .= $criteria;
		$select .= "ORDER BY Kategori.Kategori ASC, Benamning ASC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);

				echo "<table>\n";
				echo "<tr>\n";
				echo "<td width=\"130\" align=\"left\"><b>Artnr</b></td>\n";
				echo "<td width=\"750\" align=\"left\"><b>Produkt</b></td>\n";
				// echo "<td width=\"35\" align=\"center\"><b>&nbsp;</b></td>";
				echo "</tr>\n";

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
				
					if ($rowcolor == true) {
						$backcolor = "firstrow";
					} else {
						$backcolor = "secondrow";
					}
						if ($Kategori != $current_Kategori) {
							if ($current_katcount != 0) {
								echo "<tr>\n";
								echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">Totalt: $current_katcount st</td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">&nbsp;</td>\n";
								echo "</tr>\n";
								$current_katcount = 0;
							}
							echo "<tr>\n";
							echo "<td colspan=\"2\" align=\"left\" class=\"dateheadline\">$Kategori ($kategori_id)</td>\n";
							echo "</tr>\n";
						}
						$current_Kategori = $Kategori;
				
						echo "<tr>\n";
						echo "<td class=\"$backcolor\" align=\"left\">$artnr</td>\n";
						if ($norway) {
							echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"http://www.cyberphoto.no/info.php?article=$artnr\">$Benamning</a></td>\n";
						} else {
							echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"http://www.cyberphoto.fi/info.php?article=$artnr\">$Benamning</a></td>\n";
						}
						echo "</tr>\n";
						$current_katcount ++;

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
			
					$countrow ++;

				endwhile;
				
			} else {
			
				echo "<tr>\n";
				echo "<td colspan=\"2\"><font color=\"#33CC33\"><b>Känns som mission impossible, men GRATTIS, listan är TOM!</b></td>\n";
				echo "</tr>\n";
			
			}
				// echo "<tr>\n";
				// echo "<td colspan=\"3\"><hr noshade color=\"#85000D\" size=\"1\"></td>\n";
				// echo "</tr>\n";
				echo "<tr>\n";
				echo "<td colspan=\"2\"><b>Antal produkter: $countrow st</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
	}
	
}

?>

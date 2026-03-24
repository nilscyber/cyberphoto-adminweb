<?php

Class CTechnicalData {

	function getActualProducts($category) {
		global $show_oldproduct;
	
		$rowcolor = true;
		$count_products = 0;

		$select  = "SELECT a.artnr, t.tillverkare, a.beskrivning, tm.techAddBy, tm.techAddDate, tm.techUpdateBy, tm.techUpdateDate ";
		$select .= "FROM cyberphoto." . $category . " tm ";
		$select .= "JOIN Artiklar a ON a.artnr = tm.artnr ";
		$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
		$select .= "JOIN Tillverkare t ON t.tillverkar_id = a.tillverkar_id ";
		if ($show_oldproduct == "yes") {
			$select .= "WHERE a.ej_med = 0 AND a.utgangen = -1 AND a.lagersaldo < 1 ";
		} else {
			$select .= "WHERE a.ej_med = 0 AND (a.utgangen = 0 OR a.lagersaldo > 0) ";
		}
		if (preg_match("/Tekn_tablets\.php/i", $_SERVER['PHP_SELF'])) {
			$select .= "AND a.kategori_id = 748 ";
		} elseif (preg_match("/Tekn_mobile\.php/i", $_SERVER['PHP_SELF'])) {
			$select .= "AND a.kategori_id = 336 ";
		} elseif (preg_match("/Tekn_dslr\.php/i", $_SERVER['PHP_SELF'])) {
			$select .= "AND a.kategori_id = 395 ";
		} elseif (preg_match("/Tekn_cameras\.php/i", $_SERVER['PHP_SELF'])) {
			$select .= "AND a.kategori_id IN (392,393,394) ";
		}
		$select .= "ORDER BY tillverkare ASC, beskrivning ASC ";
		// echo $select;
		$res = mysqli_query(Db::getConnection(), $select);
		$check = mysqli_num_rows($res);

		if (mysqli_num_rows($res) > 0) {

			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			echo "<tr>\n";
			echo "<td class=\"bold\" width=\"600\">Produkt</td>\n";
			echo "<td class=\"bold\" width=\"250\">Upplagd av</td>\n";
			echo "<td class=\"bold\" width=\"250\">Redigerad av</td>\n";
			echo "<td class=\"bold\" width=\"60\" align=\"center\"></td>\n";
			echo "<td class=\"bold\" width=\"60\" align=\"center\"></td>\n";
			echo "</tr>";
		
			while ($row = mysqli_fetch_object($res)) {
			
				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}
				
				echo "<tr>\n";
				echo "\t<td class=\"$backcolor tech_row\"><a target=\"_blank\" href=\"/info.php?article=" . $row->artnr . "\">" . $row->tillverkare . " " . $row->beskrivning . "</a></td>";
				echo "\t<td class=\"$backcolor tech_row\"><a onMouseOver=\"this.T_WIDTH=150;this.T_PADDING=10;this.T_BGCOLOR='#FFFFFF';this.T_FONTCOLOR='#000000';return escape('</b>$row->techAddDate</b>')\">" . $row->techAddBy . "</a></td>";
				echo "\t<td class=\"$backcolor tech_row\"><a onMouseOver=\"this.T_WIDTH=150;this.T_PADDING=10;this.T_BGCOLOR='#FFFFFF';this.T_FONTCOLOR='#000000';return escape('</b>$row->techUpdateDate</b>')\">" . $row->techUpdateBy . "</a></td>";
				echo "\t<td class=\"\" align=\"center\"><a href=\"?change=" . $row->artnr . "\">Ändra</td>";
				echo "\t<td class=\"\" align=\"center\"><a href=\"?copypost=" . $row->artnr . "\">Kopiera</td>";
				echo "</tr>\n";
			
				if ($rowcolor == true) {
					$row = true;
					$rowcolor = false;
				} else {
					$row = false;
					$rowcolor = true;
				}
				$count_products++;
			
			}
		
			echo "</table>\n";
			echo "<p>Totalt: $count_products st<p>\n";

		} else {
			echo "<p><i>Inga produkter är upplagda</i><p>\n";
		}
	
	}
	
	function getSpecTech($artnr) {
	
		if (preg_match("/mobile/", $_SERVER['PHP_SELF']) || preg_match("/tablets/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_mobile";
		}
		if (preg_match("/lenses/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_lenses";
		}
		if (preg_match("/printer/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_printer";
		}
		if (preg_match("/cameras/", $_SERVER['PHP_SELF']) || preg_match("/Tekn_dslr/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_cameras";
		}
		if (preg_match("/video/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_video";
		}

		$select  = "SELECT * FROM cyberphoto." . $Tekn_table . " WHERE artnr = '" . $artnr . "' ";
		// echo $select;
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		return $rows;

	}

	function getMenuKategori() {
		global $addByCat;

		$select  = "SELECT kategori, kategori_id FROM cyberphoto.Kategori WHERE visas = -1 ORDER BY kategori ASC ";

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_object($res)) {
		
			echo "<option value=\"" . $row->kategori_id . "\"";
				
			if ($addByCat == $row->kategori_id) {
				echo " selected";
			}
				
			echo ">" . $row->kategori . " (" . $row->kategori_id . ")</option>\n";
				
		}

	}

	function getActualProductsInCategory($category) {
		global $addArtnr, $addidc, $show_ej_med;

		if (preg_match("/mobile/", $_SERVER['PHP_SELF']) || preg_match("/tablets/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_mobile";
		}
		if (preg_match("/lenses/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_lenses";
		}
		if (preg_match("/printer/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_printer";
		}
		if (preg_match("/cameras/", $_SERVER['PHP_SELF']) || preg_match("/dslr/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_cameras";
		}
		if (preg_match("/video/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_video";
		}
		
		$select  = "SELECT " . $Tekn_table . ".artnr as artnrTekn, Info_page.artnr_tekniska_data as artnrInfo, Artiklar.artnr, tillverkare, beskrivning, ej_med ";
		$select .= "FROM cyberphoto.Artiklar ";
		$select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "LEFT JOIN " . $Tekn_table . " ON " . $Tekn_table . ".artnr = Artiklar.artnr ";
		$select .= "LEFT JOIN Info_page ON Info_page.artnr = Artiklar.artnr ";
		if ($show_ej_med == "yes") {
			$select .= "WHERE (utgangen = 0 OR lagersaldo > 0) AND demo = 0 AND NOT Artiklar.artnr = '$addidc' ";
		} else {
			$select .= "WHERE (utgangen = 0 OR lagersaldo > 0) AND ej_med = 0 AND demo = 0 AND NOT Artiklar.artnr = '$addidc' ";
		}
		if (preg_match("/lenses/", $_SERVER['PHP_SELF'])) {
			$select .= "AND NOT Artiklar.tillverkar_id IN(425) ";
		}
		$select .= "AND kategori_id IN(" . $category . ") ";
		$select .= "ORDER BY tillverkare ASC, beskrivning ASC ";
		
		// echo $select;
		// exit;

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_object($res)) {
		
			if ($row->artnrTekn == "" && $row->artnrInfo == "") {
				
				$product_text = $row->tillverkare . " " . $row->beskrivning;
				if ($row->ej_med == -1) {
					$product_text .= " (produkten EJ MED, artnr: " . $row->artnr . ")";
				}
				echo "<option value=\"" . $row->artnr . "\"";
					
				if ($addArtnr == $row->artnr) {
					echo " selected";
				}
				
				echo ">" . $product_text . " (" . $row->artnr . ")</option>\n";
				
			}
		}

	}

	function techAdminChange($addid) {
		global $params1, $params2, $params3,$params4,$params5,$params6,$params7,$params8,$params9,$params10,$params11,$params12,$params13,$params14,$params15,$params16,$params17,$params18,$params19,$params20,$params21,$params22,$params23,$params24,$params25,$params26,$params27,$params28,$params29,$params30,$params31,$params32,$params33,$params34,$params35,$params36,$params37,$addcreatedby;
		
		if ($_COOKIE['login_mail'] != "") {
			$addcreatedby = $_COOKIE['login_mail'];
		} else {
			exit;
		}

		if (preg_match("/mobile/", $_SERVER['PHP_SELF']) || preg_match("/tablets/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_mobile";
		}
		if (preg_match("/lenses/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_lenses";
		}
		if (preg_match("/printer/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_printer";
		}
		if (preg_match("/cameras/", $_SERVER['PHP_SELF']) || preg_match("/Tekn_dslr/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_cameras";
		}
		if (preg_match("/video/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_video";
		}
		
		$updt  = "UPDATE cyberphoto." . $Tekn_table . " ";
		$updt .= "SET ";
		if (preg_match("/mobile/", $_SERVER['PHP_SELF']) || preg_match("/tablets/", $_SERVER['PHP_SELF'])) {
			$updt .= "params1 = '$params1', ";
			$updt .= "params2 = '$params2', ";
			$updt .= "params3 = '$params3', ";
			$updt .= "params4 = '$params4', ";
			$updt .= "params5 = '$params5', ";
			$updt .= "params6 = '$params6', ";
			$updt .= "params7 = '$params7', ";
			$updt .= "params8 = '$params8', ";
			$updt .= "params9 = '$params9', ";
			$updt .= "params10 = '$params10', ";
			$updt .= "params11 = '$params11', ";
			$updt .= "params12 = '$params12', ";
			$updt .= "params13 = '$params13', ";
			$updt .= "params14 = '$params14', ";
			$updt .= "params15 = '$params15', ";
			$updt .= "params16 = '$params16', ";
			$updt .= "params17 = '$params17', ";
			$updt .= "params18 = '$params18', ";
			$updt .= "params19 = '$params19', ";
			$updt .= "params20 = '$params20', ";
			$updt .= "params21 = '$params21', ";
			$updt .= "params22 = '$params22', ";
		}
		if (preg_match("/lenses/", $_SERVER['PHP_SELF'])) {
			$updt .= "params1 = '$params1', ";
			$updt .= "params2 = '$params2', ";
			$updt .= "params3 = '$params3', ";
			$updt .= "params4 = '$params4', ";
			$updt .= "params5 = '$params5', ";
			$updt .= "params6 = '$params6', ";
			$updt .= "params7 = '$params7', ";
			$updt .= "params8 = '$params8', ";
			$updt .= "params9 = '$params9', ";
			$updt .= "params10 = '$params10', ";
			$updt .= "params11 = '$params11', ";
			$updt .= "params12 = '$params12', ";
			$updt .= "params13 = '$params13', ";
			$updt .= "params14 = '$params14', ";
		}
		if (preg_match("/printer/", $_SERVER['PHP_SELF'])) {
			$updt .= "params1 = '$params1', ";
			$updt .= "params2 = '$params2', ";
			$updt .= "params3 = '$params3', ";
			$updt .= "params4 = '$params4', ";
			$updt .= "params5 = '$params5', ";
			$updt .= "params6 = '$params6', ";
			$updt .= "params7 = '$params7', ";
			$updt .= "params8 = '$params8', ";
			$updt .= "params9 = '$params9', ";
			$updt .= "params10 = '$params10', ";
			$updt .= "params11 = '$params11', ";
			$updt .= "params12 = '$params12', ";
			$updt .= "params13 = '$params13', ";
			$updt .= "params14 = '$params14', ";
		}
		if (preg_match("/cameras/", $_SERVER['PHP_SELF']) || preg_match("/Tekn_dslr/", $_SERVER['PHP_SELF'])) {
			$updt .= "params1 = '$params1', ";
			$updt .= "params2 = '$params2', ";
			$updt .= "params3 = '$params3', ";
			$updt .= "params4 = '$params4', ";
			$updt .= "params5 = '$params5', ";
			$updt .= "params6 = '$params6', ";
			$updt .= "params7 = '$params7', ";
			$updt .= "params8 = '$params8', ";
			$updt .= "params9 = '$params9', ";
			$updt .= "params10 = '$params10', ";
			$updt .= "params11 = '$params11', ";
			$updt .= "params12 = '$params12', ";
			$updt .= "params13 = '$params13', ";
			$updt .= "params14 = '$params14', ";
			$updt .= "params15 = '$params15', ";
			$updt .= "params16 = '$params16', ";
			$updt .= "params17 = '$params17', ";
			$updt .= "params18 = '$params18', ";
			$updt .= "params19 = '$params19', ";
			$updt .= "params20 = '$params20', ";
			$updt .= "params21 = '$params21', ";
			$updt .= "params22 = '$params22', ";
			$updt .= "params23 = '$params23', ";
			$updt .= "params24 = '$params24', ";
			$updt .= "params25 = '$params25', ";
			$updt .= "params26 = '$params26', ";
			$updt .= "params27 = '$params27', ";
			$updt .= "params28 = '$params28', ";
			$updt .= "params29 = '$params29', ";
			$updt .= "params30 = '$params30', ";
			$updt .= "params31 = '$params31', ";
			$updt .= "params32 = '$params32', ";
			$updt .= "params33 = '$params33', ";
			$updt .= "params34 = '$params34', ";
			$updt .= "params35 = '$params35', ";
		}
		if (preg_match("/video/", $_SERVER['PHP_SELF'])) {
			$updt .= "params1 = '$params1', ";
			$updt .= "params2 = '$params2', ";
			$updt .= "params3 = '$params3', ";
			$updt .= "params4 = '$params4', ";
			$updt .= "params5 = '$params5', ";
			$updt .= "params6 = '$params6', ";
			$updt .= "params7 = '$params7', ";
			$updt .= "params8 = '$params8', ";
			$updt .= "params9 = '$params9', ";
			$updt .= "params10 = '$params10', ";
			$updt .= "params11 = '$params11', ";
			$updt .= "params12 = '$params12', ";
			$updt .= "params13 = '$params13', ";
			$updt .= "params14 = '$params14', ";
			$updt .= "params15 = '$params15', ";
			$updt .= "params16 = '$params16', ";
			$updt .= "params17 = '$params17', ";
			$updt .= "params18 = '$params18', ";
			$updt .= "params19 = '$params19', ";
			$updt .= "params20 = '$params20', ";
			$updt .= "params21 = '$params21', ";
			$updt .= "params22 = '$params22', ";
			$updt .= "params23 = '$params23', ";
			$updt .= "params24 = '$params24', ";
			$updt .= "params25 = '$params25', ";
			$updt .= "params26 = '$params26', ";
			$updt .= "params27 = '$params27', ";
			$updt .= "params28 = '$params28', ";
			$updt .= "params29 = '$params29', ";
			$updt .= "params30 = '$params30', ";
			$updt .= "params31 = '$params31', ";
			$updt .= "params32 = '$params32', ";
			$updt .= "params33 = '$params33', ";
			$updt .= "params34 = '$params34', ";
			$updt .= "params35 = '$params35', ";
			$updt .= "params36 = '$params36', ";
			$updt .= "params37 = '$params37', ";
		}
		$updt .= "techUpdateBy = '$addcreatedby', techUpdateDate = now(), techUpdateIP = '" . $_SERVER['REMOTE_ADDR'] . "' ";
		$updt .= "WHERE artnr = '$addid'";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $updt;
			exit;
		}
		
		$res = mysqli_query(Db::getConnection(true), $updt);

		if (preg_match("/mobile/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_mobile.php");
		}
		if (preg_match("/tablets/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_tablets.php");
		}
		if (preg_match("/lenses/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_lenses.php");
		}
		if (preg_match("/printer/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_printer.php");
		}
		if (preg_match("/cameras/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_cameras.php");
		}
		if (preg_match("/dslr/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_dslr.php");
		}
		if (preg_match("/video/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_video.php");
		}

	}

	function techAdminAdd($addArtnr) {
		global $params1, $params2, $params3,$params4,$params5,$params6,$params7,$params8,$params9,$params10,$params11,$params12,$params13,$params14,$params15,$params16,$params17,$params18,$params19,$params20,$params21,$params22,$params23,$params24,$params25,$params26,$params27,$params28,$params29,$params30,$params31,$params32,$params33,$params34,$params35,$params36,$params37,$addcreatedby;

		if ($_COOKIE['login_mail'] != "") {
			$addcreatedby = $_COOKIE['login_mail'];
		} else {
			exit;
		}
		
		if (preg_match("/mobile/", $_SERVER['PHP_SELF']) || preg_match("/tablets/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_mobile";
		}
		if (preg_match("/lenses/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_lenses";
		}
		if (preg_match("/printer/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_printer";
		}
		if (preg_match("/cameras/", $_SERVER['PHP_SELF']) || preg_match("/Tekn_dslr/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_cameras";
		}
		if (preg_match("/video/", $_SERVER['PHP_SELF'])) {
			$Tekn_table = "Tekn_video";
		}
		
		$updt  = "INSERT INTO cyberphoto." . $Tekn_table . " ";
		$updt .= "("; 
		$updt .= "artnr,"; 
		if (preg_match("/mobile/", $_SERVER['PHP_SELF']) || preg_match("/tablets/", $_SERVER['PHP_SELF'])) {
			$updt .= "params1,"; 
			$updt .= "params2,"; 
			$updt .= "params3,"; 
			$updt .= "params4,"; 
			$updt .= "params5,"; 
			$updt .= "params6,"; 
			$updt .= "params7,"; 
			$updt .= "params8,"; 
			$updt .= "params9,"; 
			$updt .= "params10,"; 
			$updt .= "params11,"; 
			$updt .= "params12,"; 
			$updt .= "params13,"; 
			$updt .= "params14,"; 
			$updt .= "params15,"; 
			$updt .= "params16,"; 
			$updt .= "params17,"; 
			$updt .= "params18,"; 
			$updt .= "params19,"; 
			$updt .= "params20,"; 
			$updt .= "params21,";
			$updt .= "params22,";
		}
		if (preg_match("/lenses/", $_SERVER['PHP_SELF'])) {
			$updt .= "params1,"; 
			$updt .= "params2,"; 
			$updt .= "params3,"; 
			$updt .= "params4,"; 
			$updt .= "params5,"; 
			$updt .= "params6,"; 
			$updt .= "params7,"; 
			$updt .= "params8,"; 
			$updt .= "params9,"; 
			$updt .= "params10,"; 
			$updt .= "params11,"; 
			$updt .= "params12,"; 
			$updt .= "params13,"; 
			$updt .= "params14,"; 
		}
		if (preg_match("/printer/", $_SERVER['PHP_SELF'])) {
			$updt .= "params1,"; 
			$updt .= "params2,"; 
			$updt .= "params3,"; 
			$updt .= "params4,"; 
			$updt .= "params5,"; 
			$updt .= "params6,"; 
			$updt .= "params7,"; 
			$updt .= "params8,"; 
			$updt .= "params9,"; 
			$updt .= "params10,"; 
			$updt .= "params11,"; 
			$updt .= "params12,"; 
			$updt .= "params13,"; 
			$updt .= "params14,"; 
		}
		if (preg_match("/cameras/", $_SERVER['PHP_SELF']) || preg_match("/Tekn_dslr/", $_SERVER['PHP_SELF'])) {
			$updt .= "params1,"; 
			$updt .= "params2,"; 
			$updt .= "params3,"; 
			$updt .= "params4,"; 
			$updt .= "params5,"; 
			$updt .= "params6,"; 
			$updt .= "params7,"; 
			$updt .= "params8,"; 
			$updt .= "params9,"; 
			$updt .= "params10,"; 
			$updt .= "params11,"; 
			$updt .= "params12,"; 
			$updt .= "params13,"; 
			$updt .= "params14,"; 
			$updt .= "params15,"; 
			$updt .= "params16,"; 
			$updt .= "params17,"; 
			$updt .= "params18,"; 
			$updt .= "params19,"; 
			$updt .= "params20,"; 
			$updt .= "params21,";
			$updt .= "params22,";
			$updt .= "params23,";
			$updt .= "params24,";
			$updt .= "params25,";
			$updt .= "params26,";
			$updt .= "params27,";
			$updt .= "params28,";
			$updt .= "params29,";
			$updt .= "params30,";
			$updt .= "params31,";
			$updt .= "params32,";
			$updt .= "params33,";
			$updt .= "params34,";
			$updt .= "params35,";
		}
		if (preg_match("/video/", $_SERVER['PHP_SELF'])) {
			$updt .= "params1,"; 
			$updt .= "params2,"; 
			$updt .= "params3,"; 
			$updt .= "params4,"; 
			$updt .= "params5,"; 
			$updt .= "params6,"; 
			$updt .= "params7,"; 
			$updt .= "params8,"; 
			$updt .= "params9,"; 
			$updt .= "params10,"; 
			$updt .= "params11,"; 
			$updt .= "params12,"; 
			$updt .= "params13,"; 
			$updt .= "params14,"; 
			$updt .= "params15,"; 
			$updt .= "params16,"; 
			$updt .= "params17,"; 
			$updt .= "params18,"; 
			$updt .= "params19,"; 
			$updt .= "params20,"; 
			$updt .= "params21,";
			$updt .= "params22,";
			$updt .= "params23,";
			$updt .= "params24,";
			$updt .= "params25,";
			$updt .= "params26,";
			$updt .= "params27,";
			$updt .= "params28,";
			$updt .= "params29,";
			$updt .= "params30,";
			$updt .= "params31,";
			$updt .= "params32,";
			$updt .= "params33,";
			$updt .= "params34,";
			$updt .= "params35,";
			$updt .= "params36,";
			$updt .= "params37,";
		}
		$updt .= "techAddBy,techAddDate,techAddIP) ";
		$updt .= "VALUES ";
		$updt .= "(";
		if (preg_match("/mobile/", $_SERVER['PHP_SELF']) || preg_match("/tablets/", $_SERVER['PHP_SELF'])) {
			$updt .= "'$addArtnr',";
			$updt .= "'$params1',";
			$updt .= "'$params2',";
			$updt .= "'$params3',";
			$updt .= "'$params4',";
			$updt .= "'$params5',";
			$updt .= "'$params6',";
			$updt .= "'$params7',";
			$updt .= "'$params8',";
			$updt .= "'$params9',";
			$updt .= "'$params10',";
			$updt .= "'$params11',";
			$updt .= "'$params12',";
			$updt .= "'$params13',";
			$updt .= "'$params14',";
			$updt .= "'$params15',";
			$updt .= "'$params16',";
			$updt .= "'$params17',";
			$updt .= "'$params18',";
			$updt .= "'$params19',";
			$updt .= "'$params20',";
			$updt .= "'$params21',";
			$updt .= "'$params22',";
		}
		if (preg_match("/lenses/", $_SERVER['PHP_SELF'])) {
			$updt .= "'$addArtnr',";
			$updt .= "'$params1',";
			$updt .= "'$params2',";
			$updt .= "'$params3',";
			$updt .= "'$params4',";
			$updt .= "'$params5',";
			$updt .= "'$params6',";
			$updt .= "'$params7',";
			$updt .= "'$params8',";
			$updt .= "'$params9',";
			$updt .= "'$params10',";
			$updt .= "'$params11',";
			$updt .= "'$params12',";
			$updt .= "'$params13',";
			$updt .= "'$params14',";
		}
		if (preg_match("/printer/", $_SERVER['PHP_SELF'])) {
			$updt .= "'$addArtnr',";
			$updt .= "'$params1',";
			$updt .= "'$params2',";
			$updt .= "'$params3',";
			$updt .= "'$params4',";
			$updt .= "'$params5',";
			$updt .= "'$params6',";
			$updt .= "'$params7',";
			$updt .= "'$params8',";
			$updt .= "'$params9',";
			$updt .= "'$params10',";
			$updt .= "'$params11',";
			$updt .= "'$params12',";
			$updt .= "'$params13',";
			$updt .= "'$params14',";
		}
		if (preg_match("/cameras/", $_SERVER['PHP_SELF']) || preg_match("/Tekn_dslr/", $_SERVER['PHP_SELF'])) {
			$updt .= "'$addArtnr',";
			$updt .= "'$params1',";
			$updt .= "'$params2',";
			$updt .= "'$params3',";
			$updt .= "'$params4',";
			$updt .= "'$params5',";
			$updt .= "'$params6',";
			$updt .= "'$params7',";
			$updt .= "'$params8',";
			$updt .= "'$params9',";
			$updt .= "'$params10',";
			$updt .= "'$params11',";
			$updt .= "'$params12',";
			$updt .= "'$params13',";
			$updt .= "'$params14',";
			$updt .= "'$params15',";
			$updt .= "'$params16',";
			$updt .= "'$params17',";
			$updt .= "'$params18',";
			$updt .= "'$params19',";
			$updt .= "'$params20',";
			$updt .= "'$params21',";
			$updt .= "'$params22',";
			$updt .= "'$params23',";
			$updt .= "'$params24',";
			$updt .= "'$params25',";
			$updt .= "'$params26',";
			$updt .= "'$params27',";
			$updt .= "'$params28',";
			$updt .= "'$params29',";
			$updt .= "'$params30',";
			$updt .= "'$params31',";
			$updt .= "'$params32',";
			$updt .= "'$params33',";
			$updt .= "'$params34',";
			$updt .= "'$params35',";
		}
		if (preg_match("/video/", $_SERVER['PHP_SELF'])) {
			$updt .= "'$addArtnr',";
			$updt .= "'$params1',";
			$updt .= "'$params2',";
			$updt .= "'$params3',";
			$updt .= "'$params4',";
			$updt .= "'$params5',";
			$updt .= "'$params6',";
			$updt .= "'$params7',";
			$updt .= "'$params8',";
			$updt .= "'$params9',";
			$updt .= "'$params10',";
			$updt .= "'$params11',";
			$updt .= "'$params12',";
			$updt .= "'$params13',";
			$updt .= "'$params14',";
			$updt .= "'$params15',";
			$updt .= "'$params16',";
			$updt .= "'$params17',";
			$updt .= "'$params18',";
			$updt .= "'$params19',";
			$updt .= "'$params20',";
			$updt .= "'$params21',";
			$updt .= "'$params22',";
			$updt .= "'$params23',";
			$updt .= "'$params24',";
			$updt .= "'$params25',";
			$updt .= "'$params26',";
			$updt .= "'$params27',";
			$updt .= "'$params28',";
			$updt .= "'$params29',";
			$updt .= "'$params30',";
			$updt .= "'$params31',";
			$updt .= "'$params32',";
			$updt .= "'$params33',";
			$updt .= "'$params34',";
			$updt .= "'$params35',";
			$updt .= "'$params36',";
			$updt .= "'$params37',";
		}
		$updt .= "'$addcreatedby',now(),'" . $_SERVER['REMOTE_ADDR'] . "') ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $updt;
			exit;
		}
		
		$res = mysqli_query(Db::getConnection(true), $updt);

		if (preg_match("/mobile/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_mobile.php");
		}
		if (preg_match("/tablets/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_tablets.php");
		}
		if (preg_match("/lenses/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_lenses.php");
		}
		if (preg_match("/printer/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_printer.php");
		}
		if (preg_match("/cameras/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_cameras.php");
		}
		if (preg_match("/dslr/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_dslr.php");
		}
		if (preg_match("/video/", $_SERVER['PHP_SELF'])) {
			header("Location: Tekn_video.php");
		}

	}

	function getProductName($artnr) {
		global $sv, $no, $fi;

		$select  = "SELECT artnr, tillverkare, beskrivning, beskrivning_fi  ";
		$select .= "FROM cyberphoto.Artiklar ";
		$select .= "JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
		$select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		
		// echo $select;

		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
		
		if ($fi && !$sv) {
			if ($row->beskrivning_fi != "") {
				$productname = $row->tillverkare . " " . $row->beskrivning_fi;
			} else {
				$productname = $row->tillverkare . " " . $row->beskrivning;
			}
		} else {
			$productname = $row->tillverkare . " " . $row->beskrivning;
		}
		
		return $productname;
				
	}
	
}
?>
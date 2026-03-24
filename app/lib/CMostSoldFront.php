<?php
require_once("Locs.php");

Class CMostSoldFront {
	var $conn_my;

	function __construct() {
			
		// $this->conn_my = @mysqli_connect(getenv('DB_HOST') ?: 'db', getenv('DB_USER') ?: 'appuser', getenv('DB_PASS') ?: 'apppass');
		// @mysqli_select_db($this->conn_my, getenv('DB_NAME') ?: 'cyberphoto');
		
	}

	function mostSoldFrontNew($kategorier_id,$limit,$sitelink,$catname,$kategori_id_parents) {
		global $fi, $sv, $no, $headlinc, $frameless;

		$int = 1;
		$select = "";
		$output = "";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") { // om man testar att köra sålda senaste veckan samt måste finnas i lager
		
			$select  = "SELECT mostSoldArticles.artnr, Artiklar.link, Artiklar.beskrivning, Artiklar.beskrivningKort, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Artiklar.utpris_no, Tillverkare.tillverkare, ";
			$select .= "Artiklar_fi.utpris_fi, Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi, Moms.momssats, Moms.momssats_fi, Moms.momssats_no ";
			$select .= "FROM cyberphoto.Artiklar ";
			$select .= "JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
			$select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
			$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
			$select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
			$select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";

			if ($sitelink == 7) {
				$select .= "WHERE Kategori.kategori_id_parent IN(1000082,1000083,1000088,1000100,1000101,1000116,1000118) ";
			} else {
				$select .= "WHERE Artiklar.kategori_id IN ($kategorier_id) ";
			}

			if ($fi)
				$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
			else
				// $select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
				$select .= " AND Artiklar.lagersaldo > 0 AND ej_med != -1 ";
			
			if ($fi) {
				include ("std_instore_special_fi.php");
				$select .= "AND ej_med = 0 AND ej_med_fi = 0 AND demo = 0 ";
				$select .= $criteria;
			} elseif ($no) {
				include ("std_instore_special_no.php");
				$select .= "AND ej_med = 0 AND ej_med_no=0 AND demo = 0 ";
				$select .= $criteria;
			}

			// $select .= " ORDER BY antalManad1 DESC";
			// $select .= " ORDER BY antalVecka DESC";
			$select .= " ORDER BY antalManad1 DESC";

			$select .= " LIMIT " . $limit;
		
		} else {

			$select  = "SELECT mostSoldArticles.artnr, Artiklar.link, Artiklar.beskrivning, Artiklar.beskrivningKort, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Artiklar.utpris_no, Tillverkare.tillverkare, ";
			$select .= "Artiklar_fi.utpris_fi, Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi, Moms.momssats, Moms.momssats_fi, Moms.momssats_no ";
			$select .= "FROM cyberphoto.Artiklar ";
			$select .= "JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
			$select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
			$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
			$select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
			$select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
			if ($kategori_id_parents != '') {
				$select .= "WHERE Kategori.kategori_id_parent IN($kategori_id_parents) ";
		    } else if ($sitelink == 7) {
				$select .= "WHERE Kategori.kategori_id_parent IN(1000082,1000083,1000088,1000100,1000101,1000116,1000118) ";
			} else {
				$select .= "WHERE Artiklar.kategori_id IN ($kategorier_id) ";
			}

			if ($fi)
				$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
			else
				$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";

			if ($fi) {
				include ("std_instore_special_fi.php");
				$select .= "AND ej_med = 0 AND ej_med_fi = 0 AND demo = 0 ";
				$select .= $criteria;
			} elseif ($no) {
				include ("std_instore_special_no.php");
				$select .= "AND ej_med = 0 AND ej_med_no=0 AND demo = 0 ";
				$select .= $criteria;
			}

			
			$select .= " ORDER BY antalManad1 DESC, Tillverkare.tillverkare ASC";

			$select .= " LIMIT " . $limit;
		
		}

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98") {
			//echo $select;
		}
		
		if ($catname != "") {
			$kategorier_id = $catname;
		}

		$res = mysqli_query(Db::getConnection(), $select);
		
		if ($sitelink == 1) { // kompaktkameror
			if ($fi && !$sv) {
				echo "<a target=\"_parent\" href=\"/foto-video/kompaktikamerat\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(393) . "</span></span></div></a>";
			} elseif ($fi && $sv) {
				echo "<a target=\"_parent\" href=\"/foto-video/kompaktkameror\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(393) . "</span></span></div></a>";
			} else {
				echo "<a target=\"_parent\" href=\"/foto-video/kompaktkameror\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(393) . "</span></span></div></a>";
			}
		} elseif ($sitelink == 2) { // videokameror
			if ($fi && !$sv) {
				echo "<a target=\"_parent\" href=\"/foto-video/videokamerat\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(1000028) . "</span></span></div></a>";
			} elseif ($fi && $sv) {
				echo "<a target=\"_parent\" href=\"/foto-video/videokameror\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(1000028) . "</span></span></div></a>";
			} else {
				echo "<a target=\"_parent\" href=\"/foto-video/videokameror\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(1000028) . "</span></span></div></a>";
			}
		} elseif ($sitelink == 3) { // skrivare
			if ($fi && !$sv) {
				echo "<a target=\"_parent\" href=\"/foto-video/skrivare\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(1000022) . "</span></span></div></a>";
			} elseif ($fi && $sv) {
				echo "<a target=\"_parent\" href=\"/foto-video/skrivare\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(1000022) . "</span></span></div></a>";
			} else {
				echo "<a target=\"_parent\" href=\"/foto-video/skrivare\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(1000022) . "</span></span></div></a>";
			}
		} elseif ($sitelink == 4) { // mobiltelefoner
			if ($fi && !$sv) {
				echo "<a target=\"_parent\" href=\"/mobiili\"><div class=\"front_blue_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			} elseif ($fi && $sv) {
				echo "<a target=\"_parent\" href=\"/mobiltelefoni\"><div class=\"front_blue_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			} else {
				echo "<a target=\"_parent\" href=\"/mobiltelefoni\"><div class=\"front_blue_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			}
		} elseif ($sitelink == 5) { // hobby
			if ($fi && !$sv) {
				echo "<a target=\"_parent\" href=\"/outdoor\"><div class=\"front_green_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(584) . "</span></span></div></a>";
			} elseif ($fi && $sv) {
				echo "<a target=\"_parent\" href=\"/outdoor\"><div class=\"front_green_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(584) . "</span></span></div></a>";
			} else {
				echo "<a target=\"_parent\" href=\"/outdoor\"><div class=\"front_green_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName(584) . "</span></span></div></a>";
			}
		} elseif ($sitelink == 55) { // hobby där kategori avgör
			if ($fi && !$sv) {
				echo "<a target=\"_parent\" href=\"/outdoor\"><div class=\"front_green_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			} elseif ($fi && $sv) {
				echo "<a target=\"_parent\" href=\"/outdoor\"><div class=\"front_green_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			} else {
				echo "<a target=\"_parent\" href=\"/outdoor\"><div class=\"front_green_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			}
		} elseif ($sitelink == 6) { // systemkameror
			if ($fi && !$sv) {
				echo "<a target=\"_parent\" href=\"/foto-video/jarjestelma\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			} elseif ($fi && $sv) {
				echo "<a target=\"_parent\" href=\"/foto-video/systemkameror\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			} else {
				echo "<a target=\"_parent\" href=\"/foto-video/systemkameror\"><div class=\"front_red_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			}
		} elseif ($sitelink == 8) { // batterier där kategori avgör
			if ($fi && !$sv) {
				echo "<a target=\"_parent\" href=\"/akut\"><div class=\"front_orange_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			} elseif ($fi && $sv) {
				echo "<a target=\"_parent\" href=\"/batterier\"><div class=\"front_orange_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			} else {
				echo "<a target=\"_parent\" href=\"/batterier\"><div class=\"front_orange_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			}
		} elseif ($sitelink == 9) { // cybairgun där kategori avgör
			if ($fi && !$sv) {
				echo "<a target=\"_parent\" href=\"/cybairgun\"><div class=\"front_darkgreen_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			} elseif ($fi && $sv) {
				echo "<a target=\"_parent\" href=\"/cybairgun\"><div class=\"front_darkgreen_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($kategorier_id) . "</span></span></div></a>";
			} else {
				echo "<a target=\"_parent\" href=\"/cybairgun\"><div class=\"front_darkgreen_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\">" . $this->getCategoryName($catname) . "</span></span></div></a>";
			}
	
		} elseif ($sitelink == 10) { // manuell för outdoor

			echo "<a target=\"_parent\" href=\"/outdoor\"><div class=\"front_darkgreen_bar\"><span class=\"mostsoldheadcontainer\">" . l("Most sold") . " <span class=\"lowercase\"> produkter</span></span></div></a>";

		}		
		echo "<div class=\"clear\"></div>\n";
		echo "<div class=\"container_mostsold\">\n";

		while ($row = mysqli_fetch_array($res)) {
			extract ($row);
			
			if ($beskrivningKort != "") {
				$beskrivning = $beskrivningKort;
			}

			if ($tillverkare != ".") {
				if ($fi && !$sv) {
					$produktbeskrivning = $tillverkare ." " . $beskrivning_fi;
				} else {
					$produktbeskrivning = $tillverkare ." " . $beskrivning;
				}
			} else {
				if ($fi && !$sv) {
					$produktbeskrivning = $beskrivning_fi;
				} else {
					$produktbeskrivning = $beskrivning;
				}
			}

			if ($fi && !$sv) {
				if ($beskrivning_fi != "")

					$beskrivning = $beskrivning_fi;

				if ($link2_fi != "")

					$link = $link2_fi;
				else
					$link = "/info_fi.php?article=".$artnr;
			} elseif ($fi && $sv) {

				if ($link2_fi != "")
					$link = $link2_fi;
				else
					$link = "/info_fi_se.php?article=".$artnr;
			} else {
				if ($link != "")
					$link = $link;
				else
					$link = "/info.php?article=".$artnr;
			}
			
			if ($frameless) {
				$link = preg_replace("/info\_fi\_se\.php/", "info.php", $link);
				$link = preg_replace("/info\_fi\.php/", "info.php", $link);
			}

			if ($bild != "XxX") {
				if ($fi && !$sv) {
					$bild = "/pic/noimage_fi.jpg";
				} else {
					$bild = "/pic/noimage.jpg";
				}
			}

			if ($fi) {
				$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
			} elseif ($no) {
				$utprismoms = number_format(($utpris_no + $utpris_no * $momssats_no), 0, ',', ' ') . "&nbsp;NOK";
			} else {
				$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;SEK";
			}

			if ($int == 2 || $int == 4) {
				echo "<div class=\"mostsold_bars\">\n";
			} else {
				echo "<div class=\"mostsold_clean\">\n";
			}
			echo "<a href=\"$link\"><div class=\"mostsoldheadline\">$produktbeskrivning</div></a>";
			echo "<a href=\"$link\"><div style=\"width: 170px; height: 15px; background-image: url('/thumbs/xlarge/bilder/$bild'); background-repeat: no-repeat; background-position: center\"></div></a>";
			echo "<div class=\"pris floatleft\">$utprismoms</div>";
			echo "<div style=\"float: right; margin-right: 5px;\">";
			echo "<a href=\"javascript:modifyItems('$artnr')\">";
			echo "<div class=\"product_buy_butt\">" . l('Buy') . "</div>";
			/*
			if ($fi && !$sv) {
				echo "<img title=\"Klikkaa tästä laittaaksesi tuote ostoskoriin\" src=\"/pic/11_fi.gif\" border=0>";
			} else {
				echo "<img title=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/11.gif\" border=0>";
			}
			*/
			echo "</a>";
			echo "</div>\n";
			echo "</div>\n";

			$int ++;

		}

		echo "<div class=\"clear\"></div>\n";
		echo "</div>\n";

		return $output;
	}

	function getCategoryName($category) {
		global $sv, $fi, $no;
		
		if ($fi && !$sv) {
			$select = "SELECT kategori_fi AS kategori FROM cyberphoto.Kategori WHERE kategori_id = $category ";
		} elseif ($no) {
			$select = "SELECT kategori_no AS kategori FROM cyberphoto.Kategori WHERE kategori_id = $category ";
		} else {
			$select = "SELECT kategori FROM cyberphoto.Kategori WHERE kategori_id = $category ";
		}
		
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			return $row->kategori;
		} else {
			return;
		}
	}	

}

?>

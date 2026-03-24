<?php
require_once("CCheckIpNumber.php");
require_once("Locs.php");
include_once 'Db.php';

Class CPricelist {

	// var $conn_my;

	function __construct() {

		// $this->conn_my = @mysqli_connect(getenv('DB_HOST') ?: 'db', getenv('DB_USER') ?: 'appuser', getenv('DB_PASS') ?: 'apppass');
		// @mysqli_select_db($this->conn_my, getenv('DB_NAME') ?: 'cyberphoto');
		// $this->conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($this->conn_my, getenv('DB_NAME') ?: 'cyberphoto');

	}

	function displayPageTitle() {
		global $sv, $fi, $no, $pagetitle, $pagetitle_name, $seo_title;
		
		// if (preg_match("/systemkameror\.php/i", $_SERVER['PHP_SELF'])) {
		if ($seo_title != "") {
			return "<title>" . $seo_title . " | CyberPhoto</title>\n";
		} elseif ($pagetitle > 0) {
			return "<title>CyberPhoto - " . $this->getCategoryName($pagetitle) . "</title>\n";
		} elseif ($pagetitle_name != "") {
			return "<title>CyberPhoto - " . $pagetitle_name . "</title>\n";
		} else {
			if ($fi && !$sv) {
				return "<title>CyberPhoto - Henkilökohtainen palvelu netissä</title>\n";
			} elseif ($no) {
				return "<title>CyberPhoto - Digitalkameror, videokameror och fototillbehör. Personlig service på nettet</title>\n";
			} else {
				return "<title>CyberPhoto - Digitalkameror, videokameror och fototillbehör. Personlig service på nätet!</title>\n";
			}
		}
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
		
		// $res = mysqli_query($this->conn_my, $select);
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			return $row->kategori;
		} else {
			return;
		}
	}	

	function getCategoryButton($category_name,$linc,$newlinc) {
		global $sv, $fi, $no, $sortera, $linc_page, $headlinc;
		
		unset($pageLinc);
		
		$pageLinc = CSeo::getLincIfSeoReplacePage($linc);

		if ($pageLinc != "") {
			$buttlinc = $pageLinc;
		} elseif ($newlinc) {
			$buttlinc = $headlinc . "/category/" . $linc . "/" . strtolower(Tools::replace_special_char(trim($this->getCategoryName($linc))));
		} elseif ($linc_page != "") {
			$buttlinc = $linc_page . "?show=" . $linc . "&sortera=" . $sortera;
		} elseif ($linc == "") {
			$buttlinc = $_SERVER['PHP_SELF'] . "?show=" . $category_name . "&sortera=" . $sortera;
		} else {
			$buttlinc = $_SERVER['PHP_SELF'] . "?show=" . $linc . "&sortera=" . $sortera;
		}

		if ($fi && !$sv) {
			$select = "SELECT kategori_fi AS kategori FROM cyberphoto.Kategori WHERE kategori_id = $category_name ";
		} elseif ($no) {
			$select = "SELECT kategori_no AS kategori FROM cyberphoto.Kategori WHERE kategori_id = $category_name ";
		} else {
			$select = "SELECT kategori FROM cyberphoto.Kategori WHERE kategori_id = $category_name ";
		}
		
		// $res = mysqli_query($this->conn_my, $select);
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			if ($category_name == 410) {
				$catname = "Cokin";
			} else {
				$catname = $row->kategori;
			}
			
			$catname = preg_replace("/Polarisaatiosuodatin/", "Polarisaatio<br>suodatin", $catname);
			$catname = preg_replace("/erikoissuodattimet/", "erikois<br>suodattimet", $catname);
			$catname = preg_replace("/Infrapunasuodatin/", "Infrapuna<br>suodatin", $catname);
			$catname = preg_replace("/Vidvinkelkonverter/", "Vidvinkel-konverter", $catname);
			
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				echo "<a href=\"" . $buttlinc . "\">\n";
				echo "<div class=\"head_button\">\n";
				if (!preg_match("/price_category\.php/i", $_SERVER['PHP_SELF'])) {
					$filename = '/pricelist/button/' . $category_name . '.png';
					$fileN = "/web/www" . $filename;
				}
				if (file_exists($fileN)) {
					echo "<div class=\"floatleft\">\n";
					echo "<img src=\"/pricelist/button/$category_name.png\">\n";
					echo "</div>\n";
					echo "<div class=\"head_button_cell_picture\">\n";
					echo $catname;
					echo "</div>\n";
				} else {
					echo "<div class=\"head_button_cell\">\n";
					echo $catname;
					echo "</div>\n";
				}
				echo "</div></a>\n";
			} else {
				echo "<a href=\"" . $buttlinc . "\">\n";
				echo "<div class=\"head_button\">\n";
				if (!preg_match("/price_category\.php/i", $_SERVER['PHP_SELF'])) {
					$filename = '/pricelist/button/' . $category_name . '.png';
					$fileN = "/web/www" . $filename;
				}
				if (file_exists($fileN)) {
					echo "<div class=\"floatleft\">\n";
					echo "<img src=\"/pricelist/button/$category_name.png\">\n";
					echo "</div>\n";
					echo "<div class=\"head_button_cell_picture\">\n";
					echo $catname;
					echo "</div>\n";
				} else {
					echo "<div class=\"head_button_cell\">\n";
					echo $catname;
					echo "</div>\n";
				}
				echo "</div></a>\n";
			}
		} else {
			return;
		}
	}

	function getPlainButton($menuID,$category_name) {
		global $sv, $fi, $no;
		
		// $category_name = 236;
		
		$select  = "SELECT menuID, kategori, kategori_fi, kategori_no, menuByCat, menuNameSE, menuLincSE, menuNameNO, menuLincNO, menuNameFI, menuLincFI, menuShowPublic, menuIsParent, menuIsSpacing ";
		$select .= "FROM cyberphoto.menu_web ";
		$select .= "JOIN Kategori ON menuByCat = kategori_id ";
		$select .= "WHERE menuID = $menuID AND  menuSection = 'plainbutton' ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND menuShowPublic = -1 ";
		}
		if ($fi) {
			$select .= "AND menuActiveFI = -1 ";
		} elseif ($no) {
			$select .= "AND menuActiveNO = -1 ";
		} else {
			$select .= "AND menuActiveSE = -1 ";
		}
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		
		// $res = mysqli_query($this->conn_my, $select);
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {

			if ($fi && !$sv) {
				if ($row->menuLincFI != "") {
					$buttlinc = $row->menuLincFI;
				} else {
					$buttlinc = $row->menuLincSE;
				}
			} elseif ($no) {
				if ($row->menuLincNO != "") {
					$buttlinc = $row->menuLincNO;
				} else {
					$buttlinc = $row->menuLincSE;
				}
			} else {
				$buttlinc = $row->menuLincSE;
			}
			if ($fi && !$sv) {
				if ($row->menuNameFI != "") {
					$catname = $row->menuNameFI;
				} elseif ($row->kategori_fi != "") {
					$catname = $row->kategori_fi;
				} else {
					$catname = $row->menuNameSE;
				}
			} elseif ($no) {
				if ($row->menuNameNO != "") {
					$catname = $row->menuNameNO;
				} elseif ($row->kategori_no != "") {
					$catname = $row->kategori_no;
				} else {
					$catname = $row->menuNameSE;
				}
			} else {
				if ($row->menuNameSE != "") {
					$catname = $row->menuNameSE;
				} else {
					$catname = $row->kategori;
				}
			}
			
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				echo "<a href=\"/" . $buttlinc . "\">\n";
				echo "<div class=\"head_button\">\n";
				$filename = '/pricelist/button/' . $category_name . '.png';
				$fileN = "/web/www" . $filename;
				if (file_exists($fileN)) {
					echo "<div class=\"floatleft\">\n";
					echo "<img src=\"/pricelist/button/$category_name.png\">\n";
					echo "</div>\n";
					echo "<div class=\"head_button_cell_picture\">\n";
					echo $catname;
					echo "</div>\n";
				} else {
					echo "<div class=\"head_button_cell_textonly\">\n";
					echo $catname;
					echo "</div>\n";
				}
				echo "</div></a>\n";
			} else {
				echo "<a href=\"/" . $buttlinc . "\">\n";
				echo "<div class=\"head_button\">\n";
				$filename = '/pricelist/button/' . $category_name . '.png';
				$fileN = "/web/www" . $filename;
				if (file_exists($fileN)) {
					echo "<div class=\"floatleft\">\n";
					echo "<img src=\"/pricelist/button/$category_name.png\">\n";
					echo "</div>\n";
					echo "<div class=\"head_button_cell_picture\">\n";
					echo $catname;
					echo "</div>\n";
				} else {
					echo "<div class=\"head_button_cell_textonly\">\n";
					echo $catname;
					echo "</div>\n";
				}
				echo "</div></a>\n";
			}
		} else {
			return;
		}
	}
	
	function mostSoldProducts($kategorier_id, $limit, $pagetitle) {
		global $fi, $sv, $no, $frameless;
		
		echo "<div id=\"mostsold_box\">\n";
		echo "<div class=\"mostsold_header\"><span class=\"mostsold_heading\">" . l('Most sold') . " <span class=\"lowercase\">" . $this->getCategoryName($pagetitle) . "</span></span></div>\n";

		$int = 0;
		
		$select  = "SELECT mostSoldArticles.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Tillverkare.tillverkare ";
		$select .= "FROM cyberphoto.Artiklar ";
		$select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
		$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
		$select .= " WHERE Artiklar.kategori_id IN ($kategorier_id)";
		
		if ($fi) {
			$select .= " AND ej_med_fi=0 AND demo=0 AND (utgangen_fi=0 OR lagersaldo > 0 ) AND NOT (demo = -1 AND demo_fi != -1) ";
		} else {
			$select .= " AND ej_med=0 AND demo = 0 AND (utgangen=0 OR lagersaldo > 0) ";
		}
		
		if ($fi) {
			include ("std_instore_special_fi.php");
			$select .= "AND ej_med = 0 AND ej_med_fi = 0 AND demo = 0 ";
			$select .= $criteria;
		} elseif ($no) {
			include ("std_instore_special_no.php");
			$select .= "AND ej_med = 0 AND ej_med_no=0 AND demo = 0 ";
			$select .= $criteria;
		}
		
		$select .= " ORDER BY antalManad1 DESC";	
		
		$select .= " LIMIT " . $limit;
		
		// echo $select;
		// exit;
		
		// $res = mysqli_query($this->conn_my, $select);
		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_array($res)) {
			$int += 1;
			extract ($row);
			
			if ($fi && !$sv) {
				if ($beskrivning_fi != "")
					$beskrivning = $beskrivning_fi;
					$link = "/info_fi.php?article=".$artnr;
			} elseif ($fi && $sv) {
					$link = "/info_fi_se.php?article=".$artnr;
			} else {
					$link = "/info.php?article=".$artnr;
			}
			if ($frameless) {
				$link = preg_replace("/info\_fi\_se\.php/", "info.php", $link);
				$link = preg_replace("/info\_fi\.php/", "info.php", $link);
			}
			
			$tillverkare = preg_replace("/\./", "", $tillverkare);
			$beskrivning = preg_replace("/\<b\>/", "", $beskrivning);
			$beskrivning = preg_replace("/\<\/b\>/", "", $beskrivning);
			$trimmaheader = $tillverkare . " " . $beskrivning;
			
			if (strlen($trimmaheader) >= 40)
				$trimmaheader = substr ($trimmaheader, 0, 40) . "...";

			echo "<a href=\"$link\">";
			if ($int == 10) {
				echo "<div class=\"mostsold_row mostsold_last_row\">$int. ". $trimmaheader . "</div>";
			} else {
				echo "<div class=\"mostsold_row\">$int. ". $trimmaheader . "</div>";
			}
			echo "</a>\n";

		}

		echo "</div>\n";

	}

	function displayCategories($kategori_id) {
		global $fi, $sv, $no, $headlinc, $product, $nombc;
		
		$select  = "SELECT kategori_id, kategori, kategori_fi, kategori_no ";
		$select .= "FROM Kategori ";
		$select .= "WHERE kategori_id_parent = $kategori_id ";
		$select .= "AND visas = -1 ";	
		$select .= "AND NOT (kategori_id IN (396,486,501,509,513,1000260,1000265,1000267)) ";	
		$select .= "ORDER BY sortPriority DESC, kategori ASC ";
		
		// echo $select;
		// exit;
		
		$res = mysqli_query(Db::getConnection(), $select);
		
		if (!$nombc) {
			$product->getBredcrumbs($kategori_id);
		}
		echo "<h1>" . $this->getCategoryName($kategori_id) . "</h1>\n";
		echo "<div class=\"top20\">\n";
		echo "<div class=\"head_box\">\n";

		while ($row = mysqli_fetch_object($res)) {
		
			// echo "<div class=\"box_category_linc\"><a class=\"category_linc\" href=\"\">" . $row->kategori . "</a></div>";
			$this->getCategoryButton($row->kategori_id,$row->kategori_id,true);

		}

		echo "<div class=\"clear\"></div>";
		echo "</div>\n";
		echo "</div>\n";

	}

	function getMargin2($article,$shownetto = false) {
		global $sv, $fi, $no;
				
		$marginal = round((($article->utpris - $article->art_id) / $article->utpris) * 100,2);
		
		if ($marginal < 0) {
			$color = "text_red";
		} else {
			$color = "text_green";
		}


		if ($shownetto) {
			return "<span class=\"mark_grey\">" . number_format(round($article->art_id, 2), 2, ',', ' ') . "</span>";
		} elseif ($marginal > 0) {
			// return "<span class=\"$color\">" . $marginal . "</span>";
			return "<span class=\"$color\">" . number_format(round($marginal, 2), 2, ',', ' ') . "</span>";
		} else {
			return "<span class=\"$color\">" . number_format(round($marginal, 2), 2, ',', ' ') . "</span>";
			// return;
		}
	}	
	
	function getMargin($artnr,$shownetto = false) {
		global $sv, $fi, $no;
		
		//$kurs_EUR = $this->getRate($idag_date, false);
		//$kurs_NOK = $this->getRate($idag_date, true);
		
		$select  = "SELECT a.utpris, a.art_id, a.utpris_no, afi.utpris_fi ";
		$select .= "FROM cyberphoto.Artiklar a ";
		$select .= "LEFT JOIN Artiklar_fi afi ON afi.artnr_fi = a.artnr ";
		$select .= "WHERE a.artnr = '" . $artnr . "' ";
		
		// echo $select;
		
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $select;
			// echo $kurs_NOK;
			// exit;
		}
		
		if ($fi) {
			$netto_fi = round($row->art_id / $kurs_EUR, 2);
			$marginal = (($row->utpris_fi - $netto_fi) / $row->utpris_fi) * 100;
		} elseif ($no) {
			$netto_no = round($row->art_id / $kurs_NOK, 2);
			$marginal = (($row->utpris_no - $netto_no) / $row->utpris_no) * 100;
		} else {
			$marginal = round((($row->utpris - $row->art_id) / $row->utpris) * 100,2);
		}
		if ($marginal < 0) {
			$color = "text_red";
		} else {
			$color = "text_green";
		}
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $kurs_EUR;
			// exit;
		}

		if ($shownetto) {
			if (preg_match("/dos_product\.php/i", $_SERVER['PHP_SELF'])) {
				return "<span class=\"mark_grey\">" . round($row->art_id, 0) . "</span>";
			} else {
				return "<span class=\"mark_grey\">" . number_format(round($row->art_id, 2), 2, ',', ' ') . "</span>";
			}
		} elseif (mysqli_num_rows($res) > 0) {
			// return "<span class=\"$color\">" . $marginal . "</span>";
			return "<span class=\"$color\">" . number_format(round($marginal, 2), 2, ',', ' ') . "</span>";
		} else {
			return;
		}
	}	

	function getRate($idag_date,$NOK) {

		if ($idag_date == "") {
			$idag_date = date("Y-m-d", time());
		}

		$select = "SELECT multiplyrate  ";
		$select .= "FROM c_conversion_rate ";
		if ($NOK) {
			// $select .= "WHERE c_currency_id = 287 AND c_currency_id_to = 311 AND validto = '$idag_date' ";
			$select .= "WHERE c_currency_id = 287 AND c_currency_id_to = 311 AND validto = '$idag_date' ";
			$select .= "ORDER BY validto DESC ";
		} else {
			$select .= "WHERE c_currency_id = 102 AND c_currency_id_to = 311 AND validto = '$idag_date' ";
		}
		$select .= "LIMIT 1 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			while ($res && $row = pg_fetch_row($res)) {

				return round($row[0],3);
			
			}

	}

	function getManufacturerList($criteria) {
		global $sv, $fi, $no, $tillverkare;
		
		$count = 0;
		
		if ($tillverkare != "") {
			$criteria = substr($criteria, 0, strpos($criteria, "AND (Tillverkare.tillverkare"));
		} else {
			$criteria = substr($criteria, 0, strpos($criteria, "ORDER"));
		}

		$select  = "SELECT DISTINCT tillverkare ";
		$select .= "FROM cyberphoto.Artiklar ";
		if ($fi) {
			$select .= "LEFT JOIN cyberphoto.Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
		}
		$select .= "LEFT JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "LEFT JOIN cyberphoto.Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
		$select .= $criteria;
		$select .= "ORDER BY tillverkare ASC ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = mysqli_query(Db::getConnection(), $select);
		
		if (mysqli_num_rows($res) > 1) {

			// echo "<label for=\"tillverkare\">" . l('Manufacturer') . ": </label>";
			echo "<select class=\"sort_manufacture_box\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\" name=\"tillverkare\" id=\"tillverkare\">\n";
			// echo "<option value=\"\"></option>\n";
			// echo "<option value=\"\" disabled selected>" . l('Select manufacturer') . "</option>\n";
			echo "<option value=\"\">" . l('Select manufacturer') . "</option>\n";
			
			while ($row = mysqli_fetch_object($res)) {
			
				if (trim($row->tillverkare) != "") {
			
					echo "<option value=\"" . $row->tillverkare . "\"";
						
					if ($tillverkare == $row->tillverkare) {
						echo " selected";
					}
					
					echo ">" . $row->tillverkare . "</option>\n";
				
				}
					
			}
			
			echo "</select>\n";
		
		}

	}
	
}

?>

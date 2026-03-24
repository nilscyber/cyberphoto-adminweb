<?php

require_once("CCheckIpNumber.php");
require_once("CBasket.php");
$bask = new CBasket();

Class CMobileSite {

	var $conn_my;
	var $conn_ms;
	var $conn_my2;

	function __construct() {

		$this->conn_my = Db::getConnection();
		$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
		@mssql_select_db ("cyberphoto", $this->conn_ms);
		$this->conn_my2 = Db::getConnectionDb('cyberadmin');

	}

	function getMainCategories() {
		global $katID;

		$select  = "SELECT kategori_id, kategori ";
		$select  .= "FROM Kategori ";
		$select  .= "WHERE kategori_id_huvud = -1 ";
		$select  .= "AND visas = -1 ";
		$select  .= "ORDER BY sortPriority DESC, kategori ASC ";
		
		$res = mysqli_query($this->conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
				while ($row = mysqli_fetch_array($res)) {
				extract($row);
				
				echo "\t<li>\n";
				echo "\t\t<a class=\"item_title\" href=\"products.php?katID=$kategori_id\">\n";
				if ($kategori_id == 584) {
					echo "\t\t<div class=\"middle expandable\"><div class=\"inner icon_hobby\">$kategori</div></div></a>\n";
				} elseif ($kategori_id == 585) {
					echo "\t\t<div class=\"middle expandable\"><div class=\"inner icon_mobile\">$kategori</div></div></a>\n";
				} elseif ($kategori_id == 586) {
					echo "\t\t<div class=\"middle expandable\"><div class=\"inner icon_sound\">$kategori</div></div></a>\n";
				} elseif ($kategori_id == 1000082) {
					echo "\t\t<div class=\"middle expandable\"><div class=\"inner icon_house\">$kategori</div></div></a>\n";
				} elseif ($kategori_id == 1000147) {
					echo "\t\t<div class=\"middle expandable\"><div class=\"inner icon_cybairgun\">$kategori</div></div></a>\n";
				} elseif ($kategori_id == 1000045) {
					echo "\t\t<div class=\"middle expandable\"><div class=\"inner icon_battery\">$kategori</div></div></a>\n";
				} else {
					echo "\t\t<div class=\"middle expandable\"><div class=\"inner icon_box\">$kategori</div></div></a>\n";
				}
				echo "\t</li>\n";
				
				}

	}

	function getCategories() {
		global $katID;

		$select  = "SELECT kategori_id, kategori ";
		$select  .= "FROM Kategori ";
		if ($katID != "") {
			$select  .= "WHERE kategori_id_parent = '$katID' ";
			$select  .= "AND visas = -1 AND NOT (kategori_id = 0) ";
			$select  .= "AND NOT (kategori_id = 1000010 OR kategori_id = 1000011 OR kategori_id = 1000012) "; // tar bort abonnemang
		} else {
			$select  .= "WHERE kategori_id_huvud = -1 ";
		}
		$select  .= "ORDER BY sortPriority DESC, kategori ASC ";
		
		$res = mysqli_query($this->conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {
			
				$countrow = 1;
				while ($row = mysqli_fetch_array($res)) {
				extract($row);
				
				echo "<ul class=\"item_list\">\n";
				if ($countrow == $num_rows) {
					echo "\t<li class=\"last\">\n";
				} else {
					echo "\t<li>\n";
				}
				echo "\t\t<a class=\"item_title\" href=\"" . $_SERVER['PHP_SELF'] . "?katID=$kategori_id\">\n";
				echo "\t\t<div class=\"middle expandable\"><div class=\"inner noicon\">$kategori</div></div></a>\n";
				echo "\t</li>\n";
				echo "</ul>\n";
				
				$countrow++;
				
				}
				
			} else {
				/*
				echo "<ul class=\"item_list\">\n";
				echo "\t<li class=\"last\">\n";
				echo "\t\t<a class=\"item_title\" href=\"" . $_SERVER['PHP_SELF'] . "?katID=$katID\">\n";
				echo "\t\t<div class=\"middle expandable\"><div class=\"inner noicon\">Jag har inte byggt längre än så :)</div></div>\n";
				echo "\t</li>\n";
				echo "</ul>\n";
				*/
				$this->getProducts($katID);
			}

	}

	function getProducts($katID) {

		$select = "SELECT art.artnr, art.beskrivning, tillv.Tillverkare ";
		$select .= "FROM Artiklar art ";
		$select .= "JOIN Tillverkare tillv ON tillv.tillverkar_id = art.tillverkar_id ";
		$select .= "WHERE art.ej_med=0 AND (art.demo=0 OR art.lagersaldo > 0) AND (art.utgangen=0 OR art.lagersaldo > 0) ";
		$select .= "AND art.kategori_id = '$katID' ";
		$select .= "AND NOT demo=-1 " ; // initialt visar vi inga demoprodukter på mobilsidan
		$select .= "ORDER BY art.sortPriority DESC, tillv.Tillverkare ASC, art.beskrivning ASC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($this->conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {
			
				$countrow = 1;
				while ($row = mysqli_fetch_array($res)) {
				extract($row);

				if ($Tillverkare == ".") {
					$Tillverkare = "";
				}
				
				echo "<ul class=\"item_list\">\n";
				if ($countrow == $num_rows) {
					echo "\t<li class=\"last\">\n";
				} else {
					echo "\t<li>\n";
				}
				echo "\t\t<a class=\"item_title\" href=\"info.php?article=$artnr\">\n";
				echo "\t\t<div class=\"middle expandable\"><div class=\"inner noicon\">$Tillverkare $beskrivning</div></div></a>\n";
				// echo "\t\t<div class=\"middle expandable\"><div class=\"inner noicon\">$Tillverkare $beskrivning</div></div></a>\n";
				echo "\t</li>\n";
				echo "</ul>\n";
				
				$countrow++;
				
				}
				
			} else {
				echo "<ul class=\"item_list\">\n";
				echo "\t<li class=\"last\">\n";
				echo "\t\t<span class=\"item_title\">\n";
				echo "\t\t<div class=\"middle\"><div class=\"inner noicon\">Inga produkter finns i denna kategori</div></div>\n";
				echo "\t</li>\n";
				echo "</ul>\n";
			}

	}

	function getProductsThroughSearch($searchprod) {

		// nedan en massa småfix för att rätta till solklara fall
		$searchprod = preg_replace("/Sök här/", "", $searchprod);
		$searchprod = preg_replace("/nikkor/i", "nikon objektiv", $searchprod);
		$searchprod = preg_replace("/nikor/i", "nikon objektiv", $searchprod);
		$searchprod = preg_replace("/1\.4/", "1,4", $searchprod);
		$searchprod = preg_replace("/1\.8/", "1,8", $searchprod);
		$searchprod = preg_replace("/2\.0/", "2,0", $searchprod);
		$searchprod = preg_replace("/2\.8/", "2,8", $searchprod);
		$searchprod = preg_replace("/3\.5/", "3,5", $searchprod);
		$searchprod = preg_replace("/4\.0/", "4,0", $searchprod);
		$searchprod = preg_replace("/4\.5/", "4,5", $searchprod);
		$searchprod = preg_replace("/5\.6/", "5,6", $searchprod);
		$searchprod = preg_replace("/6\.3/", "6,3", $searchprod);
		$searchprod = preg_replace("/f\//", "", $searchprod);
		$searchprod = preg_replace("/8mm/", "8", $searchprod);
		$searchprod = preg_replace("/18mm/", "18", $searchprod);
		$searchprod = preg_replace("/20mm/", "20", $searchprod);
		$searchprod = preg_replace("/22mm/", "22", $searchprod);
		$searchprod = preg_replace("/28mm/", "28", $searchprod);
		$searchprod = preg_replace("/35mm/", "35", $searchprod);
		$searchprod = preg_replace("/40mm/", "40", $searchprod);
		$searchprod = preg_replace("/50mm/", "50", $searchprod);
		$searchprod = preg_replace("/52mm/", "52", $searchprod);
		$searchprod = preg_replace("/55mm/", "55", $searchprod);
		$searchprod = preg_replace("/60mm/", "60", $searchprod);
		$searchprod = preg_replace("/70mm/", "70", $searchprod);
		$searchprod = preg_replace("/75mm/", "75", $searchprod);
		$searchprod = preg_replace("/77mm/", "77", $searchprod);
		$searchprod = preg_replace("/82mm/", "82", $searchprod);
		$searchprod = preg_replace("/85mm/", "85", $searchprod);
		$searchprod = preg_replace("/90mm/", "90", $searchprod);
		$searchprod = preg_replace("/100mm/", "100", $searchprod);
		$searchprod = preg_replace("/105mm/", "105", $searchprod);
		$searchprod = preg_replace("/125mm/", "125", $searchprod);
		$searchprod = preg_replace("/135mm/", "135", $searchprod);
		$searchprod = preg_replace("/150mm/", "150", $searchprod);
		$searchprod = preg_replace("/180mm/", "180", $searchprod);
		$searchprod = preg_replace("/200mm/", "200", $searchprod);
		$searchprod = preg_replace("/250mm/", "250", $searchprod);
		$searchprod = preg_replace("/300mm/", "300", $searchprod);
		$searchprod = preg_replace("/400mm/", "400", $searchprod);
		$searchprod = preg_replace("/500mm/", "500", $searchprod);
		$searchprod = preg_replace("/600mm/", "600", $searchprod);
		$searchprod = preg_replace("/ mm/", "", $searchprod);
		$searchprod = preg_replace("/mobiler/", "mobiltelefoner", $searchprod);
		$searchprod = preg_replace("/scanner/", "skanner", $searchprod);
		$searchprod = preg_replace("/diaskanner/", "skanner", $searchprod);
		$searchprod = preg_replace("/cannon/", "canon", $searchprod);
		$searchprod = preg_replace("/nikkon/", "nikon", $searchprod);
		$searchprod = preg_replace("/elinchrome/", "elinchrom", $searchprod);
		$searchprod = preg_replace("/kamerastativ/", "stativ huvud", $searchprod);
		$searchprod = preg_replace("/teleconverter/", "telekonverter", $searchprod);
		$searchprod = preg_replace("/surfplatta/", "surfplattor", $searchprod);

		// $firstSearch = $searchprod;
		$searchprod = trim($searchprod);

		// echo $searchprod;
		// exit;
		

		$searchwords = preg_split("/[\s]+/", $searchprod);

		$select = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.kommentar, Artiklar.utpris, Artiklar.kategori_id, Tillverkare, link, kategori, no_buy, lagersaldo, bestallningsgrans, bild, momssats, utgangen, link, Kategori.sortPriority, abb_data, Artiklar.campaignLink, Artiklar.tillverkar_id ";
		$select .= "FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "LEFT JOIN Moms on Artiklar.momskod = Moms.moms_id ";
		$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id WHERE ";
		$select .= "( ";

		for ($i = 0; $i < count($searchwords);$i++) {

				if ($i == 0) {
					// $select .= "tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
					$select .= "kategori like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%') ";
				} else {
					// $select .= "AND (tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
					$select .= "AND (kategori like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%') ";
					$morethenoneword = true;
				}
		}

		$select .= "AND ej_med=0 AND (utgangen=0 OR lagersaldo > 0) AND (demo=0 OR lagersaldo > 0) AND NOT (Artiklar.kategori_id = 486 OR Artiklar.kategori_id = 509 OR Artiklar.kategori_id = 511 OR Artiklar.kategori_id = 512 OR Artiklar.kategori_id = 513) " ;
		$select .= "AND NOT (Artiklar.kategori_id = 1000010 OR Artiklar.kategori_id = 1000011 OR Artiklar.kategori_id = 1000012) "; // tar bort abonnemang
		$select .= "AND NOT demo=-1 " ; // initialt visar vi inga demoprodukter på mobilsidan

		$select .= "ORDER BY sortPriority DESC, kategori ASC, tillverkare ASC, beskrivning ASC, skapad_datum DESC, lagersaldo DESC, bestallningsgrans DESC ";
		
		$res = mysqli_query($this->conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0 && $searchprod != "") {
			
				$countrow = 1;
				while ($row = mysqli_fetch_array($res)) {
				extract($row);
				
				if ($Tillverkare == ".") {
					$Tillverkare = "";
				}
				
				echo "<ul class=\"item_list\">\n";
				if ($countrow == $num_rows) {
					echo "\t<li class=\"last\">\n";
				} else {
					echo "\t<li>\n";
				}
				echo "\t\t<a class=\"item_title\" href=\"info.php?article=$artnr\">\n";
				echo "\t\t<div class=\"middle expandable\"><div class=\"inner noicon\">$Tillverkare $beskrivning</div></div></a>\n";
				echo "\t</li>\n";
				echo "</ul>\n";
				
				$countrow++;
				
				}
				
			} else {
				echo "<ul class=\"item_list\">\n";
				echo "\t<li class=\"last\">\n";
				echo "\t\t<span class=\"item_title\">\n";
				echo "\t\t<div class=\"middle\"><div class=\"inner noicon\">Inget resultat</div></div>\n";
				echo "\t</li>\n";
				echo "</ul>\n";
			}

	}

	function displayProductName($artnr) {
		
		$select = "SELECT TILLV.tillverkare, ART.beskrivning ";
		$select .= "FROM Artiklar ART ";
		$select .= "LEFT JOIN Tillverkare TILLV ON ART.tillverkar_id = TILLV.tillverkar_id ";
		$select .= "WHERE ART.artnr = '$artnr' ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($this->conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)) {
				extract($row);
				
					if ($tillverkare != ".") {
						$showbeskrivning = $tillverkare . " " . $beskrivning;
					} else {
						$showbeskrivning = $beskrivning;
					}
					
					echo $showbeskrivning;
				}
			
			} else {
			
				// om det inte blir träff av någon anledning
			
			}

	}

	function displayProduct($artnr) {
		global $bask, $accessories;
		
		$select = "SELECT ART.artnr, TILLV.tillverkare, ART.beskrivning, ART.utpris, ART.bild, MO.momssats, ART.betyg ";
		$select .= "FROM Artiklar ART ";
		$select .= "LEFT JOIN Tillverkare TILLV ON ART.tillverkar_id = TILLV.tillverkar_id ";
		$select .= "LEFT JOIN Moms MO on ART.momskod = MO.moms_id ";
		$select .= "WHERE ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
		$select .= "AND NOT demo=-1 " ; // initialt visar vi inga demoprodukter på mobilsidan
		$select .= "AND ART.artnr = '$artnr' ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($this->conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)) {
				extract($row);
				
					if ($tillverkare != ".") {
						$showbeskrivning = $tillverkare . " " . $beskrivning;
					} else {
						$showbeskrivning = $beskrivning;
					}
					$valuta = ' SEK';
					if ($utpris == 0) {
						$utpris_moms = "ej prissatt ännu";
					} else {
						$utpris_moms = $utpris + ($utpris * $momssats);
						$utpris_moms = number_format($utpris_moms, 0, ',', ' ') . $valuta;
					}
				
					echo "<h1><div class=\"middle\"><div class=\"inner\">$showbeskrivning</div></div></h1>";
					if ($bild == "") {
						echo "<img align=\"right\" hspace=\"5\" vspace=\"5\" src=\"img/nopicture.png\">";
					} else {
						echo "<img align=\"right\" hspace=\"5\" vspace=\"5\" src=\"/thumbs/large/bilder/$bild\">";
					}
					if ($betyg == 1) {
						echo "<img align=\"right\" hspace=\"5\" vspace=\"5\" src=\"img/stamp_toppklass_new.png\">";
					}
					if ($betyg == 20) {
						echo "<img align=\"right\" hspace=\"5\" vspace=\"5\" src=\"img/stamp_brakop_new.png\">";
					}
					if ($betyg == 30) {
						echo "<img align=\"right\" hspace=\"5\" vspace=\"5\" src=\"img/stamp_prisvard_new.png\">";
					}
					echo "<br />";
					echo "<h3>Pris: $utpris_moms</h3>";
					echo "<a href=\"purchase.php?addproduct=true&article=$artnr\">";
					echo "<br />";
					echo "<img src=\"img/buy.png\">";
					echo "</a>";
					echo "<br /><br />";
					echo "<h3>Lagerstatus:&nbsp;";
					if ($artnr == "forsakring") {
						echo "<span class=\"instore\">Finns i lager</span>";
					} else {
						echo $bask->check_lager($artnr);
					}
					echo "</h3>";
					echo "<br />";
					if ($this->getIfAnyPac($artnr)) { // kolla om det finns värdpaket
						echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?article=$artnr" . "pac" . "\">";
						echo "<img src=\"img/plus.png\"> <span class=\"accessories\">Visa värdepaket</span>";
						echo "</a>";
						echo "<br />";
					}
					if ($this->getIfAnyAccessories() && !$accessories) { // kolla om det finns tillbehör till produkten
						echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?article=$artnr&accessories=true#access\">";
						echo "<img src=\"img/plus.png\"> <span class=\"accessories\">Visa tillbehör</span>";
						echo "</a>";
						echo "<br />";
					}
					if ($accessories && $this->confirmInfoPage($artnr)) {
						echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?article=$artnr&accessories=#access\">";
						echo "<img src=\"img/plus.png\"> <span class=\"accessories\">Visa produktinfo</span>";
						echo "</a>";
						echo "<br />";
					}
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89xx") {
						echo "<br />";
						$this->getInfoPage($artnr);
					}
				
				}
			
			} else {
			
				// om det inte blir träff av någon anledning
			
			}

	}

	function confirmInfoPage($artnr) {

		$confirm_page = false;
		
		$select  = "SELECT * ";
		$select .= "FROM Info_page ";
		$select .= "WHERE artnr = '$artnr' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);
		
		if (mysqli_num_rows($res) > 0) {
			
			while ($row = mysqli_fetch_array($res)) {
				extract($row);
				
				if ($test_text != "") {
					$confirm_page = true;
				} elseif ($produktinfo_text != "") {
					$confirm_page = true;
				} else {
					$confirm_page = false;
				}
			}
			
			if ($confirm_page) {
				return true;
			} else {
				return false;
			}
			
		} else {
			return false;
		}

		
	}

	function getInfoPage($artnr) {

		$select  = "SELECT test_text, produktinfo_text ";
		$select .= "FROM Info_page ";
		$select .= "WHERE artnr = '$artnr' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);
		
		if (mysqli_num_rows($res) > 0) {
			
			while ($row = mysqli_fetch_array($res)) {
				extract($row);
				if ($test_text != "") {
					// $test_text = preg_replace('/\n/', '<br>', $test_text);
					$test_text = $this->replaceRelativeLinks($test_text);
					echo "<br />";
					echo "<b>CyberPhoto testar</b>";
					echo "<br />";
					echo "<br />";
					echo $test_text;
				} else {
					// $produktinfo_text = preg_replace('/\n/', '<br>', $produktinfo_text);
					$produktinfo_text = $this->replaceRelativeLinks($produktinfo_text);
					echo "<br />";
					echo "<b>Produktbeskrivning</b>";
					echo "<br />";
					echo "<br />";
					echo $produktinfo_text;
				}
			}
		} else {
			return;
		}

		
	}

	function replaceRelativeLinks($var) {

		$relative = false;
		if (eregi("^/", $var)) {
			$relative = true;

			if ($rel_link == "") { // om relativ länk inte är angett, leta reda på den själv
				// tag reda på vart sidan ligger
				$path_parts = pathinfo($var);
				$rel_link = $path_parts['dirname'] . "/";
			}

			ob_start();
			include("/web/www" . $var);
			$var = ob_get_contents();
			ob_end_clean();
		}
		// felaktiga tecken pga replikeringen
		// $var = eregi_replace ('`', '"', $var);
		$var = preg_replace ('/\`/', '"', $var);
		$var = preg_replace('/http\:\/\/www\.cyberphoto\.se/', '', $var);
		$var = preg_replace('/\<h1\>/', '<b>', $var);
		$var = preg_replace('/\<\/h1\>/', '</b>', $var);
		$var = preg_replace('/<img src=\'/', '<img src=\'/', $var);
		// $var = preg_replace('/<a href=\'bildexempel/', '<a href=\'/bildexempel', $var);
		$var = preg_replace('/bildexempel\//', '/bildexempel/', $var);
		$var = preg_replace('/\/\/bildexempel\//', '/bildexempel/', $var);
		$var = preg_replace('/info\.php/', 'm/info.php', $var);
		
		if ($rel_link != "" AND $var != "")  {

			$var = eregi_replace("<img src=\"", "<img src=\"" . $rel_link, $var);
			$var = eregi_replace("<img border=\"0\" src=\"", "<img border=\"0\" src=\"" . $rel_link, $var);
			//$var = eregi_replace("<a href=\"", "<a href=\"" . $rel_link, $var);
			//$var = preg_replace("/<a href=\"(?!javascript)/i", "<a href=\"" . $rel_link, $var);
			$var = preg_replace("/<a href=\"(?!javascript|http:)/i", "<a href=\"" . $rel_link, $var);

		}
		return $var;
	}

	function getIfAnyPac($artnr) {

		$artnr2 = $artnr . "pac";

		$select  = "SELECT Artiklar.artnr ";
		$select .= "FROM Artiklar ";
		$select .= "WHERE Artiklar.artnr = '$artnr2' ";
		$select .= "AND ej_med = 0 AND utgangen = 0 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);
		
		if (mysqli_num_rows($res) > 0) {
			return true; // värdepaket Finns till denna produkt
		} else {
			return false; // Inget värdepaket finns till denna produkt
		}

		
	}

	function displayPurchase($artnr) {
		global $bask, $remove;
		
		$select = "SELECT ART.artnr, TILLV.tillverkare, ART.beskrivning, ART.utpris, ART.bild, MO.momssats ";
		$select .= "FROM Artiklar ART ";
		$select .= "LEFT JOIN Tillverkare TILLV ON ART.tillverkar_id = TILLV.tillverkar_id ";
		$select .= "LEFT JOIN Moms MO on ART.momskod = MO.moms_id ";
		$select .= "WHERE ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
		$select .= "AND ART.artnr = '$artnr' ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($this->conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)) {
				extract($row);
				
					$valuta = ' SEK';
					$utpris_moms = $utpris + ($utpris * $momssats);
					$utpris_moms = number_format($utpris_moms, 0, ',', ' ') . $valuta;
				
					echo "<h1><div class=\"middle\"><div class=\"inner\">$tillverkare $beskrivning</div></div></h1>";
					echo "<img src=\"img/loader.gif\">";
					echo "<br />";
					echo "<br />";
					if ($remove) {
						echo "<h3>Produkten tas nu bort från kundvagnen</h3>";
					} else {
						echo "<h3>Produkten läggs nu i kundvagnen</h3>";
					}
					echo "<br />";
				
				}
			
			} else {
			
				// om det inte blir träff av någon anledning
			
			}

	}

	function getIfAnyAccessories() {
		global $article;

		$select  = "SELECT Artiklar.passartill ";
		$select .= "FROM Artiklar ";
		$select .= "WHERE Artiklar.artnr = '$article' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);
		
		if (mysqli_num_rows($res) > 0) {
		
			extract(mysqli_fetch_array($res));

			if ($passartill != "") {
				$newargument = split (" ", $passartill);
				$n = count($newargument);
				if ($n > 7)
					$n = 7;
				for ($i=0; $i<$n; $i+=1) {
					if ($i == 0)
						$passartill1 = trim($newargument[$i]);
					elseif ($i == 1)
						$passartill2 = trim($newargument[$i]);
					elseif ($i == 2)
						$passartill3 = trim($newargument[$i]);
					elseif ($i == 3)
						$passartill4 = trim($newargument[$i]);
					elseif ($i == 4)
						$passartill5 = trim($newargument[$i]);
					elseif ($i == 5)
						$passartill6 = trim($newargument[$i]);
					elseif ($i == 6)
						$passartill7 = trim($newargument[$i]);
				}

			} else {
				$passartill1 = $article;
			}
		}

		$passar = "WHERE (Passartill.passartill='$passartill1' ";
		$passar .= "OR Passartill.passartill='$passartill2' OR Passartill.passartill='$passartill3' OR Passartill.passartill='$passartill4' OR ";
		$passar .= "Passartill.passartill='$passartill5' OR Passartill.passartill='$passartill6' OR Passartill.passartill='$passartill7' OR ";
		$passar .= "Passartill.passartill='" . $article . "') ";

		$select = "SELECT DISTINCT Artiklar.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, ";
		$select .= "Artiklar.lagersaldo, Artiklar_fi.lagersaldo_fi, Artiklar.bestallningsgrans, Artiklar_fi.bestallningsgrans_fi, utpris, utpris_fi, ";
		$select .= "tillverkare, link, link2_fi, kategori, Passartill.kommentar as comment, Moms.momssats, Moms.momssats_fi, Kategori.kategori_fi, Artiklar.bild as showbild, ";
		$select .= "Artiklar.no_buy ";
		$select .= "FROM Artiklar ";
		$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
		$select .= "INNER JOIN Passartill ON Artiklar.artnr=Passartill.artnr ";
		$select .= "INNER JOIN Kategori ON Artiklar.kategori_id=Kategori.kategori_id ";
		$select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
		$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
		$select .= "$passar";
		if ($fi) {
		$select .= "AND Artiklar_fi.ej_med_fi != -1 AND (utgangen=0 OR lagersaldo > 0 OR lagersaldo_fi > 0) ";
		} else {
		$select .= "AND Artiklar.ej_med=0 AND (utgangen=0 OR lagersaldo > 0) ";
		$select .= "AND NOT demo=-1 " ; // initialt visar vi inga demoprodukter på mobilsidan
		}
		$select .= "AND (Passartill.recommended >= 0 AND Passartill.recommended < 90) AND NOT (Artiklar.kategori_id IN(42,43,45,50,373,374,375,376,476)) ";
		$select .= "AND NOT (Artiklar.artnr IN(SELECT Passartill.artnr FROM Passartill $passar AND Passartill.recommended = 99)) ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = @mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
			return true; // tillbehör Finns till denna produkt
		} else {
			return false; // Inga tillbehör finns till denna produkt
		}

		
	}

	function displayAcessories($article) {

		$select  = "SELECT Artiklar.passartill ";
		$select .= "FROM Artiklar ";
		$select .= "WHERE Artiklar.artnr = '$article' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);
		
		if (mysqli_num_rows($res) > 0) {
		
			extract(mysqli_fetch_array($res));

			if ($passartill != "") {
				$newargument = split (" ", $passartill);
				$n = count($newargument);
				if ($n > 7)
					$n = 7;
				for ($i=0; $i<$n; $i+=1) {
					if ($i == 0)
						$passartill1 = trim($newargument[$i]);
					elseif ($i == 1)
						$passartill2 = trim($newargument[$i]);
					elseif ($i == 2)
						$passartill3 = trim($newargument[$i]);
					elseif ($i == 3)
						$passartill4 = trim($newargument[$i]);
					elseif ($i == 4)
						$passartill5 = trim($newargument[$i]);
					elseif ($i == 5)
						$passartill6 = trim($newargument[$i]);
					elseif ($i == 6)
						$passartill7 = trim($newargument[$i]);
				}

			} else {
				$passartill1 = $article;
			}
		}

		$passar = "WHERE (Passartill.passartill='$passartill1' ";
		$passar .= "OR Passartill.passartill='$passartill2' OR Passartill.passartill='$passartill3' OR Passartill.passartill='$passartill4' OR ";
		$passar .= "Passartill.passartill='$passartill5' OR Passartill.passartill='$passartill6' OR Passartill.passartill='$passartill7' OR ";
		$passar .= "Passartill.passartill='" . $article . "') ";

		$select = "SELECT DISTINCT Artiklar.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, ";
		$select .= "Artiklar.lagersaldo, Artiklar_fi.lagersaldo_fi, Artiklar.bestallningsgrans, Artiklar_fi.bestallningsgrans_fi, utpris, utpris_fi, ";
		$select .= "tillverkare, link, link2_fi, kategori, Passartill.kommentar as comment, Moms.momssats, Moms.momssats_fi, Kategori.kategori_fi, Artiklar.bild as showbild, ";
		$select .= "Artiklar.no_buy ";
		$select .= "FROM Artiklar ";
		$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
		$select .= "INNER JOIN Passartill ON Artiklar.artnr=Passartill.artnr ";
		$select .= "INNER JOIN Kategori ON Artiklar.kategori_id=Kategori.kategori_id ";
		$select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
		$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
		$select .= "$passar";
		if ($fi) {
		$select .= "AND Artiklar_fi.ej_med_fi != -1 AND (utgangen=0 OR lagersaldo > 0 OR lagersaldo_fi > 0) ";
		} else {
		$select .= "AND Artiklar.ej_med=0 AND (utgangen=0 OR lagersaldo > 0) ";
		$select .= "AND NOT demo=-1 " ; // initialt visar vi inga demoprodukter på mobilsidan
		}
		$select .= "AND (Passartill.recommended >= 0 AND Passartill.recommended < 90) AND NOT (Artiklar.kategori_id IN(42,43,45,50,373,374,375,376,476)) ";
		$select .= "AND NOT (Artiklar.artnr IN(SELECT Passartill.artnr FROM Passartill $passar AND Passartill.recommended = 99)) ";
		$select .= "ORDER BY kategori ASC, tillverkare ASC, Artiklar.beskrivning ASC, utpris ASC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {
			
				$countrow = 1;
				while ($row = mysqli_fetch_array($res)) {
				extract($row);

				if ($tillverkare != ".") {
					$showbeskrivning = $tillverkare . " " . $beskrivning;
				} else {
					$showbeskrivning = $beskrivning;
				}
				
				echo "<ul class=\"item_list\">\n";
				if ($countrow == $num_rows) {
					echo "\t<li class=\"last\">\n";
				} else {
					echo "\t<li>\n";
				}
				echo "\t\t<a class=\"item_title\" href=\"info.php?article=$artnr\">\n";
				echo "\t\t<div class=\"middle expandable\"><div class=\"inner noicon\">$showbeskrivning</div></div></a>\n";
				echo "\t</li>\n";
				echo "</ul>\n";
				
				$countrow++;
				
				}
				
			} else { // detta skall inte behöva inträffa men ändå
				echo "<ul class=\"item_list\">\n";
				echo "\t<li class=\"last\">\n";
				echo "\t\t<span class=\"item_title\">\n";
				echo "\t\t<div class=\"middle\"><div class=\"inner noicon\">Inget tillbehör finns för denna produkt</div></div>\n";
				echo "\t</li>\n";
				echo "</ul>\n";
			}

	}

	function viewBasketItems($kundvagn) {

		$output = "";
		if (ereg ("(grejor:)(.*)", $kundvagn,$matches)) {
			# Split the number of items and article id s into a list
			$orderlista = $matches[2];
			$argument = split ("\|", $orderlista);
		}

		$goodscounter=0;

		$n = count($argument);

		for ($i=$n-2; ($i > -1); $i+=-2) {
			$arg = $argument[$i];        # Article id
			$count = $argument[$i+1];    # Keeps track of the number of the same article

			$select  = "SELECT artnr, beskrivning, kommentar, utpris, tillverkare, frakt, lagersaldo, bestallt, ";
			$select .= "bestallningsgrans, lev_datum_normal, frakt FROM Artiklar ";
			$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
			$select .= "WHERE artnr='$arg'";

			# Alla värden försvinner inte i varje loop, så därför måste vi göra enligt nedan
			$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $bestallningsgrans = $lev_datum_normal = "";

			$row = mysqli_fetch_array(mysqli_query($this->conn_my, $select));
			extract($row);

			if (!eregi("frakt", $artnr)) {
				$output .= $goodscounter++;
			}
		}

		if ($goodscounter == 1) {
			echo "1 artikel";
		} else {
			echo $goodscounter . " artiklar";
		}

	}

	function viewBasket($kundvagn) {
		global $pay, $freight;

		echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\" width=\"100%\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td class=\"baskettdtop\">Produkt</td>\n";
		echo "\t\t<td class=\"baskettdtop\" align=\"right\">Pris SEK</td>\n";
		echo "\t\t<td class=\"baskettditems\" align=\"right\">&nbsp;</td>\n";
		echo "\t</tr>\n";
		
		$output = "";
		if (ereg ("(grejor:)(.*)", $kundvagn,$matches)) {
			# Split the number of items and article id s into a list
			$orderlista = $matches[2];
			$argument = split ("\|", $orderlista);
		}

		$valuta  = 'SEK';
		$goodscounter=0;
		$goodsvalue=0;

		$n = count($argument);
		//for ($i=0; ($i < $n);  $i+=2) {
		for ($i=$n-2; ($i > -1); $i+=-2) {
			$arg = $argument[$i];        # Article id
			$count = $argument[$i+1];    # Keeps track of the number of the same article

			$select  = "SELECT ART.artnr, ART.beskrivning, ART.kommentar, ART.utpris, TILLV.tillverkare, MOM.momssats, ART.frakt ";
			$select .= "FROM Artiklar ART ";
			$select .= "LEFT JOIN Tillverkare TILLV ON ART.tillverkar_id = TILLV.tillverkar_id ";
			$select .= "LEFT JOIN Moms MOM ON ART.momskod = MOM.moms_id ";
			$select .= "WHERE ART.artnr='$arg'";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			# Alla värden försvinner inte i varje loop, så därför måste vi göra enligt nedan
			$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $bestallningsgrans = $lev_datum_normal = "";

			$row = mysqli_fetch_array(mysqli_query($this->conn_my, $select));
			extract($row);

			$goodsvalue += ($utpris*$count);
			$goodsvalueMoms += ($utpris + $utpris * $momssats)*$count;
			$price_vat_noticluded = ($utpris*$count);
			$price_vat_icluded = ($utpris + $utpris * $momssats)*$count;
			$showprice = number_format($price_vat_icluded, 0, ',', ' ') . "&nbsp;" . $valuta;

			$description = $count . " st ";
			if ($tillverkare != '.')
				$description .= $tillverkare . " ";
			$description .= $beskrivning;

			if (strlen($description) >= 27)
				$description = substr ($description, 0, 27) . "..";

			// if ($frakt > 0 && $freight != 'fraktbutik' && !$this->freeFreight )  {
			if ($frakt > 0 && $freight != 'fraktbutik')  {
				if ($extra_freight < $frakt) {
					$extra_freight = $frakt; 
				}
			}
			
			if ($artnr != "fraktbutik") {
				echo "\t<tr>\n";
				if (!eregi("^frakt", $artnr)) {
					echo "\t\t<td class=\"baskettditems\"><a href=\"info.php?article=$artnr\">$description</a></td>\n";
				} else {
					echo "\t\t<td class=\"baskettditems\">$description</td>\n";
				}
				echo "\t\t<td class=\"baskettditems\" align=\"right\">$showprice</td>\n";
				echo "\t\t<td class=\"baskettditems\" align=\"center\">";
				if (!eregi("^frakt", $artnr)) {
					echo "<a href=\"purchase.php?remove=true&article=$artnr\">";
					echo "<img border=\"0\" src=\"img/remove.png\"></a>";
				} else {
					echo "&nbsp;";
				}
				echo "</td>\n";
				echo "\t</tr>\n";
			}
		}

		// if ($firstbasket != 'nooutput' && $pay == "invoiceme" && ((!$fi && $this->basketValue < 3200) || ($fi && $this->basketValue < 327))) {
		if ($firstbasket != 'nooutput' && $pay == "sveainvoice" && $goodsvalueMoms < 400000) {

			# Alla värden försvinner inte i varje loop, så därför måste vi göra enligt nedan
			$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $bestallningsgrans = $lev_datum_normal = "";

			$select = "SELECT Artiklar.beskrivning, Artiklar.kommentar, utpris, Artiklar_fi.beskrivning_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi from Artiklar, Artiklar_fi, Moms  ";
			$select .= "WHERE Artiklar.momskod = Moms.moms_id AND Artiklar.artnr = Artiklar_fi.artnr_fi ";
			$select .= "AND artnr='invoicefee'";

			$res = mysqli_query($this->conn_my, $select);
			$row = mysqli_fetch_object($res);
			
			$description = $row->beskrivning;
			if (strlen($description) >= 25)
				$description = substr ($description, 0, 25) . "..";

			$goodsvalue += $row->utpris;
			$goodsvalueMoms += ($row->utpris + ($row->utpris * $row->momssats));
			$price_vat_noticluded = $row->utpris;
			$price_vat_icluded = ($row->utpris + ($row->utpris * $row->momssats));
			$showprice = number_format($price_vat_icluded, 0, ',', ' ') . "&nbsp;" . $valuta;
		
				echo "\t<tr>\n";
				echo "\t\t<td class=\"baskettditems\">$description</td>\n";
				echo "\t\t<td class=\"baskettditems\" align=\"right\">$showprice</td>\n";
				echo "\t\t<td class=\"baskettditems\" align=\"center\">";
				echo "&nbsp;";
				echo "</td>\n";
				echo "\t</tr>\n";

		}

		// if ($extra_freight && $firstbasket == 'yes' && $firstbasket != 'nooutput' && $extra_freight != 999 && $extra_freight != 3 && $freight != Null && !$this->freeFreight) {
		// echo $extra_freight;
		if ($extra_freight && $firstbasket != 'nooutput' && $extra_freight != 999 && $extra_freight != 3 && $freight != Null && !$this->freeFreight) {

			if ($extra_freight == 1) $extra_freight_artnr = 'frakt+';
			elseif ($extra_freight == 2) $extra_freight_artnr = 'frakt+2';
			//elseif ($extra_freight == 3 && $old_foretag == 0) $extra_freight_artnr = 'frakthempall';	    
			//elseif ($extra_freight == 3 && $old_foretag == -1) $extra_freight_artnr = 'fraktpall';	    
			else $extra_freight_artnr = 'frakt+'; // för säkerhets skull
			$select  = "SELECT Artiklar.beskrivning, Artiklar.kommentar, utpris, Artiklar_fi.beskrivning_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi from Artiklar, Artiklar_fi, Moms ";
			$select  .= "WHERE Artiklar.momskod = Moms.moms_id AND Artiklar.artnr = Artiklar_fi.artnr_fi AND ";
			$select .= " artnr='$extra_freight_artnr'";
		
			$res = mysqli_query($this->conn_my, $select);
			$row = mysqli_fetch_object($res);
			if ($fi && !$sv) {
				$name = $row->beskrivning_fi;
				$comment = $row->kommentar_fi;			
			} else {
				$name = $row->beskrivning;
				$comment = $row->kommentar;			
			}
			if ($fi) {
				$val = "EUR";
				$outprice = $row->utpris_fi;
				$momsts = $row->momssats_fi;			
			} else {
				$val = "SEK";
				$outprice = $row->utpris;
				$momsts = $row->momssats;						
			}
			
			$exfreight = number_format(($outprice + $outprice * $momsts) * $count, 0, ',', ' ') . " " . $val;
		
			$manufacturer = "";
			$goodsvalue += $outprice;
			$goodsvalueMoms += ($outprice + $outprice * $momsts);
			$goodsvalueMomsGC += ($outprice + $outprice * $momsts);
		
				echo "\t<tr>\n";
				echo "\t\t<td class=\"baskettditems\"><i>$name</i></td>\n";
				echo "\t\t<td class=\"baskettditems\" align=\"right\">$exfreight</td>\n";
				echo "\t\t<td class=\"baskettditems\" align=\"center\">";
				echo "&nbsp;";
				echo "</td>\n";
				echo "\t</tr>\n";

		}
		
		$showpricetotal = number_format($goodsvalueMoms, 0, ',', ' ') . "&nbsp;" . $valuta;
		echo "\t<tr>\n";
		echo "\t\t<td class=\"baskettdbottom \" align=\"left\">Totalt:</td>\n";
		echo "\t\t<td class=\"baskettdbottom\" align=\"right\">$showpricetotal</td>\n";
		echo "\t\t<td class=\"baskettdbottom\" align=\"left\">&nbsp;</td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
		
		echo "<h3><a href=\"javascript:removeAllItems();\">Töm kundvagnen</a></h3>";

	}

	function getNews() {
		global $type;

		$select  = "SELECT cnt, titel, blogType ";
		$select  .= "FROM blog ";
		if ($type == "blogg") {
			$select  .= "WHERE blogType IN (19) ";
		} elseif ($type == "mobil") {
			$select  .= "WHERE blogType IN (23) ";
		} elseif ($type == "test") {
			$select  .= "WHERE blogType IN (1,5,9) ";
		} elseif ($type == "news") {
			$select  .= "WHERE blogType IN (2,6,10) ";
		} else {
			$select  .= "WHERE blogType IN (1,2,3,5,6,7,9,10,11,19,23) ";
		}
		$select  .= "AND skapad < now() AND offentlig = -1 ";
		$select  .= "ORDER BY skapad DESC ";
		$select  .= "LIMIT 50 ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		$res = mysqli_query($this->conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {
			
				$countrow = 1;
				while ($row = mysqli_fetch_array($res)) {
				extract($row);
				
				/*
				if ($blogType == 19) {
					$maintitel = $titel . " - Blogg";
				} elseif ($blogType == 1 || $blogType == 6 || $blogType == 9) {
					$maintitel = $titel . " - Testad";
				} else {
					$maintitel = $titel . " - Nyhet";
				}
				*/
				echo "<ul class=\"item_list\">\n";
				if ($countrow == $num_rows) {
					echo "\t<li class=\"last\">\n";
				} else {
					echo "\t<li>\n";
				}
				echo "\t\t<a class=\"item_title\" href=\"" . $_SERVER['PHP_SELF'] . "?ID=$cnt\">\n";
				echo "\t\t<div class=\"middle expandable\"><div class=\"inner noicon\">$titel</div></div></a>\n";
				echo "\t</li>\n";
				echo "</ul>\n";
				
				$countrow++;
				
				}
				
			} else {
				/*
				echo "<ul class=\"item_list\">\n";
				echo "\t<li class=\"last\">\n";
				echo "\t\t<a class=\"item_title\" href=\"" . $_SERVER['PHP_SELF'] . "?katID=$katID\">\n";
				echo "\t\t<div class=\"middle expandable\"><div class=\"inner noicon\">Jag har inte byggt längre än så :)</div></div>\n";
				echo "\t</li>\n";
				echo "</ul>\n";
				*/
				// $this->getProducts($katID);
			}

	}

	function displayNews($ID) {
		global $comment;
		
		$select  = "SELECT cnt, titel, beskrivning, link, link_pic, blogType ";
		$select .= "FROM blog ";
		$select .= "WHERE cnt = '$ID' ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($this->conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)) {
				extract($row);
				
					if (!preg_match("/blank/i", $beskrivning)) {
						$beskrivning = preg_replace("/http/i", "https", $beskrivning);
					}
					$beskrivning = preg_replace("/info\.php/i", "m\/info.php", $beskrivning);
					$beskrivning = preg_replace("/\n/i", "<br>", $beskrivning);

					echo "<h1><div class=\"middle\"><div class=\"inner\">$titel</div></div></h1>";
					// if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
					if ($blogType == 19 || $blogType == 23 || ($blogType == 1 && $cnt > 8674) || ($blogType == 2 && $cnt > 8674)) {
						$picture = "/blogg/thumbs/xxxlarge/blogg/" . $link_pic;
						echo "<img src=\"$picture\">";
						// echo $link_pic;
					} else {
					// if ($blogType != 19 && $blogType != 23) {
						$picture = preg_replace("/http/i", "https", $link_pic);
						$picture = preg_replace("/large/i", "xxlarge", $picture);
						echo "<img src=\"$picture\">";
					}
					echo "<br />";
					echo $beskrivning;
					if ($blogType != 19 && $blogType != 23) {
						echo "<br />";
						echo "<br />";
						$productlinc = preg_replace("/http/i", "https", $link);
						$productlinc = preg_replace("/\?info/i", "info", $productlinc);
						$productlinc = preg_replace("/info\.php/i", "m\/info.php", $productlinc);
						if (($blogType == 1 && $cnt < 8674) || ($blogType == 2 && $cnt < 8674)) {
							echo "<a href=\"$productlinc\">Till produkten</a>";
						}
						echo "<br />";
					}
					if ($blogType == 19 || $blogType == 23) {
						if ($this->getTotalComments($cnt) == 1) {
							echo "<br />";
							echo "<br />";
							echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?ID=$cnt&comment=true#$cnt\" name=\"$cnt\">Det finns " . $this->getTotalComments($cnt) . " kommentar</a>";
						} elseif ($this->getTotalComments($cnt) > 0) {
							echo "<br />";
							echo "<br />";
							echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?ID=$cnt&comment=true#$cnt\" name=\"$cnt\">Det finns " . $this->getTotalComments($cnt) . " kommentarer</a>";
						}
							if ($comment) {
								$this->showComments($cnt);
							}
							echo "<br />";
							echo "<br />";
							echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?ID=$cnt&add_comment=true\">";
							echo "<img src=\"img/plus.png\"> <span class=\"accessories\">Lägg till kommentar</span>";
							echo "</a>";
					}
					echo "<br />";
					echo "<br />";
				
				}
			
			} else {
			
				// om det inte blir träff av någon anledning
			
			}

		if ($blogType == 19) {
			echo "<h3><a href=\"" . $_SERVER['PHP_SELF'] . "?type=blogg\">Tillbaka till listan</a></h3>";
		} elseif ($blogType == 23) {
			echo "<h3><a href=\"" . $_SERVER['PHP_SELF'] . "?type=mobil\">Tillbaka till listan</a></h3>";
		} elseif ($blogType == 1 || $blogType == 5 || $blogType == 9) {
			echo "<h3><a href=\"" . $_SERVER['PHP_SELF'] . "?type=test\">Tillbaka till listan</a></h3>";
		} elseif ($blogType == 2 || $blogType == 6 || $blogType == 10) {
			echo "<h3><a href=\"" . $_SERVER['PHP_SELF'] . "?type=news\">Tillbaka till listan</a></h3>";
		} else {
			echo "<h3><a href=\"" . $_SERVER['PHP_SELF'] . "\">Tillbaka till listan</a></h3>";
		}

	}

	function getTotalComments($bloggID) {

	$select  = "SELECT COUNT(bcID) AS Antal FROM bloggComment WHERE bcActive = 1 AND bcBID = '" . $bloggID . "' ";

	$res = mysqli_query($this->conn_my2, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);
		
		return $Antal;

		}

	}

	function showComments($cnt) {

		echo "<br />";
		echo "<br />";

		$select  = "SELECT bcName, bcComment, bcTime, bcIP FROM bloggComment WHERE bcBID = '" . $cnt . "' AND bcActive = 1 ORDER BY bcTime DESC ";

		$res = mysqli_query($this->conn_my2, $select);

		while ($row = mysqli_fetch_array($res)) {
		
			extract($row);

			$bcComment = eregi_replace("\n", "<br>", $bcComment);
			
			echo "<hr noshade color=\"#808080\" align=\"left\" size=\"1\">";
			echo "<br />";
			if (CCheckIP::checkIpAdress($bcIP)) {
				echo "<p align=\"left\"><img border=\"0\" src=\"/blogg/cyberphoto_stamp.jpg\"></p>";
			} else {
				echo "<p align=\"left\" class=\"commentname\"><b>$bcName</b></p>";
			}
			echo "<p align=\"left\" class=\"commenttexten\">$bcComment</p>";
			echo "<p align=\"left\" class=\"commentttiden\">$bcTime</p>";

		}
		echo "<hr noshade color=\"#808080\" align=\"left\" size=\"1\">";

	}

}

?>

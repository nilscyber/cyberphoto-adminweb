<?php
require_once("CCheckIpNumber.php");
require_once("Db.php");

Class CSearchLogg {

	var $conn_my;
	var $conn_my2;

	function __construct() {

		$this->conn_my = Db::getConnectionDb('cyberadmin');
		$this->conn_my2 = Db::getConnection();

	}
	
	function trimSearchString($beskrivning) {

		$beskrivning = preg_replace("/Sök här/", "", $beskrivning);
		$beskrivning = preg_replace("/Søk her/", "", $beskrivning);
		$beskrivning = preg_replace("/Etsi/", "", $beskrivning);
		$beskrivning = preg_replace("/röde/", "røde", $beskrivning);
		// $beskrivning = preg_replace("/ø/", "ö", $beskrivning);
		// $beskrivning = preg_replace("/Ø/", "Ö", $beskrivning);
		$beskrivning = preg_replace("/1\.4/", "1,4", $beskrivning);
		$beskrivning = preg_replace("/1\.8/", "1,8", $beskrivning);
		$beskrivning = preg_replace("/2\.0/", "2,0", $beskrivning);
		$beskrivning = preg_replace("/2\.8/", "2,8", $beskrivning);
		$beskrivning = preg_replace("/3\.5/", "3,5", $beskrivning);
		$beskrivning = preg_replace("/4\.0/", "4,0", $beskrivning);
		$beskrivning = preg_replace("/4\.5/", "4,5", $beskrivning);
		$beskrivning = preg_replace("/5\.6/", "5,6", $beskrivning);
		$beskrivning = preg_replace("/6\.3/", "6,3", $beskrivning);
		$beskrivning = preg_replace("/f\//", "", $beskrivning);
		$beskrivning = preg_replace("/8mm/", "8", $beskrivning);
		$beskrivning = preg_replace("/15mm/", "15", $beskrivning);
		$beskrivning = preg_replace("/18mm/", "18", $beskrivning);
		$beskrivning = preg_replace("/20mm/", "20", $beskrivning);
		$beskrivning = preg_replace("/22mm/", "22", $beskrivning);
		$beskrivning = preg_replace("/24mm/", "24", $beskrivning);
		$beskrivning = preg_replace("/28mm/", "28", $beskrivning);
		$beskrivning = preg_replace("/30mm/", "30", $beskrivning);
		$beskrivning = preg_replace("/35mm/", "35", $beskrivning);
		$beskrivning = preg_replace("/40mm/", "40", $beskrivning);
		$beskrivning = preg_replace("/50mm/", "50", $beskrivning);
		$beskrivning = preg_replace("/52mm/", "52", $beskrivning);
		$beskrivning = preg_replace("/55mm/", "55", $beskrivning);
		$beskrivning = preg_replace("/60mm/", "60", $beskrivning);
		$beskrivning = preg_replace("/70mm/", "70", $beskrivning);
		$beskrivning = preg_replace("/75mm/", "75", $beskrivning);
		$beskrivning = preg_replace("/77mm/", "77", $beskrivning);
		$beskrivning = preg_replace("/82mm/", "82", $beskrivning);
		$beskrivning = preg_replace("/85mm/", "85", $beskrivning);
		$beskrivning = preg_replace("/90mm/", "90", $beskrivning);
		$beskrivning = preg_replace("/100mm/", "100", $beskrivning);
		$beskrivning = preg_replace("/105mm/", "105", $beskrivning);
		$beskrivning = preg_replace("/125mm/", "125", $beskrivning);
		$beskrivning = preg_replace("/135mm/", "135", $beskrivning);
		$beskrivning = preg_replace("/150mm/", "150", $beskrivning);
		$beskrivning = preg_replace("/180mm/", "180", $beskrivning);
		$beskrivning = preg_replace("/200mm/", "200", $beskrivning);
		$beskrivning = preg_replace("/250mm/", "250", $beskrivning);
		$beskrivning = preg_replace("/300mm/", "300", $beskrivning);
		$beskrivning = preg_replace("/400mm/", "400", $beskrivning);
		$beskrivning = preg_replace("/500mm/", "500", $beskrivning);
		$beskrivning = preg_replace("/600mm/", "600", $beskrivning);
		$beskrivning = preg_replace("/ mm/", "", $beskrivning);
		$beskrivning = preg_replace("/mobiler/", "mobiltelefoner", $beskrivning);
		$beskrivning = preg_replace("/scanner/", "skanner", $beskrivning);
		$beskrivning = preg_replace("/diaskanner/", "skanner", $beskrivning);
		$beskrivning = preg_replace("/cannon/", "canon", $beskrivning);
		$beskrivning = preg_replace("/nikkor/", "nikon objektiv", $beskrivning);
		$beskrivning = preg_replace("/nikkon/", "nikon", $beskrivning);
		$beskrivning = preg_replace("/elinchrome/", "elinchrom", $beskrivning);
		$beskrivning = preg_replace("/kamerastativ/", "stativ huvud", $beskrivning);
		$beskrivning = preg_replace("/teleconverter/", "telekonverter", $beskrivning);
		$beskrivning = preg_replace("/surfplatta/", "surfplattor", $beskrivning);
		$beskrivning = preg_replace("/linsskydd/", "objektivlock", $beskrivning);
		// $beskrivning = preg_replace("/uv\-filter/", "UV / Skylight-filter", $beskrivning);
		$beskrivning = preg_replace("/uv filter/", "UV-filter", $beskrivning);
		if (!preg_match("/stativväska/", $beskrivning) && !preg_match("/objektivväska/", $beskrivning) && !preg_match("/plånboksväska/", $beskrivning) && !preg_match("/kikarväska/", $beskrivning)) {
			$beskrivning = preg_replace("/väska/", "väskor", $beskrivning);
		}
		$beskrivning = preg_replace("/microfiberduk/", "putsduk", $beskrivning);
		$beskrivning = preg_replace("/microfiber/", "putsduk", $beskrivning);
		$beskrivning = preg_replace("/polfilter/", "polarisation", $beskrivning);
		$beskrivning = preg_replace("/nd\-filter/", "gråfilter", $beskrivning);
		$beskrivning = preg_replace("/sölv/", "silver", $beskrivning);
		$beskrivning = preg_replace("/step up/", "stepring", $beskrivning);
		$beskrivning = preg_replace("/zodiak/", "zodiac", $beskrivning);
		$beskrivning = preg_replace("/omd/", "om-d", $beskrivning);
		$beskrivning = preg_replace("/exponeringsmätare/", "ljusmätare", $beskrivning);
		$beskrivning = preg_replace("/objektivadapter/", "objektivadaptrar", $beskrivning);
		/*
		if ($beskrivning != "5dmk3") {
			$beskrivning = preg_replace("/mk3/", "mark iii", $beskrivning);
		}
		*/
		$beskrivning = preg_replace("/gull/", "guld", $beskrivning);
		$beskrivning = preg_replace("/rx\-100/", "rx100", $beskrivning);
		$beskrivning = preg_replace("/peakdesign/", "peak design", $beskrivning);
		$beskrivning = preg_replace("/handkikare/", "kikare", $beskrivning);
		$beskrivning = preg_replace("/usb c/", "usb-c", $beskrivning);
		if ($beskrivning == "eos r") {
			$beskrivning = preg_replace("/eos r/i", "eosr", $beskrivning);
		}
		$beskrivning = preg_replace("/\//", " ", $beskrivning);
		
		return $beskrivning;
	
	}

	function loggSearch($IP,$beskrivning,$place,$domain,$beskrivningTrim) {

		// $conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($conn_my, "cyberadmin");
		
		$insert  = "INSERT INTO cyberadmin.SearchLogg ";
		$insert .= "(searchIP,searchString,searchPlace,searchDomain,searchStringTrim) ";
		$insert .= "VALUES ";
		$insert .= "('$IP','$beskrivning','$place','$domain','$beskrivningTrim') ";
		// echo $insert;
		// exit;
		// $res = mysqli_query($conn_my, $insert);
		$res = mysqli_query(Db::getConnection(true), $insert);

	}
	
	function getSearchLogg($country,$feed) {
		global $sortby;

		$desiderow = true;
		$internal = "192.168.1";

		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.SearchLogg ";
		// $select .= "AND NOT searchIP LIKE '" . $internal . "%' ";
		if ($country == 1) {
			$select .= "WHERE searchDomain = 'www.cyberphoto.se' ";
		}
		if ($country == 2) {
			$select .= "WHERE searchDomain = 'www.cyberphoto.fi' ";
		}
		if ($country == 3) {
			$select .= "WHERE searchDomain = 'www.cyberphoto.no' ";
		}
		// $select .= "AND searchPlace = 0 ";
		$select .= "AND searchTime > DATE_SUB(now(), INTERVAL 1 day) ";
		$select .= "ORDER BY searchTime DESC ";
		if ($feed) {
			$select .= "LIMIT 50 ";
		} else {
			$select .= "LIMIT 350 ";
		}

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		// $res = mysqli_query($this->conn_my, $select);
		$res = mysqli_query(Db::getConnection(), $select);

		echo "<div style=\"float: left; margin-right: 20px; margin-bottom: 20px;\">";
		echo "<table cellspacing=\"1\" cellpading=\"2\" width=\"400\">";
		echo "<tr>";
		echo "<td width=\"25\" align=\"center\">&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		// echo "<td width=\"75\"><b>&nbsp;</b></td>";
		echo "</tr>";
		
			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)) {
			
					extract($row);
					
					if ($desiderow == true) {
						$rowcolor = "firstrow";
					} else {
						$rowcolor = "secondrow";
					}
					if (preg_match("/192\.168\.1\./", $searchIP)) {
						$searchString = $searchString . " (intern)";
					}

					echo "<tr>";
					echo "<td align=\"right\">";
					if ($searchDomain == "www.cyberphoto.fi") {
						echo "<img style=\"cursor: pointer;\" title=\"" . $searchIP . " - " . $this->displayTimeFrom($searchTime) . "\" border=\"0\" src=\"fi_mini.jpg\">&nbsp;";
					} elseif ($searchDomain == "www.cyberphoto.no") {
						echo "<img style=\"cursor: pointer;\" title=\"" . $searchIP . " - " . $this->displayTimeFrom($searchTime) . "\" border=\"0\" src=\"no_mini.jpg\">&nbsp;";
					} else {
						echo "<img style=\"cursor: pointer;\" title=\"" . $searchIP . " - " . $this->displayTimeFrom($searchTime) . "\" border=\"0\" src=\"sv_mini.jpg\">&nbsp;";
					}
					echo "</td>";
					if ($searchPlace == 0) {
						echo "<td class=\"$rowcolor bold\">";
					} else {
						echo "<td class=\"$rowcolor\">";
					}
					echo $searchString;
					echo "</td>";
					// echo "<td>&nbsp;&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?show=yes&searchstring=" . $searchString . "\">Detaljer</a></td>";
					echo "</tr>";

					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}
			
				}
				
			}
			
			echo "</table>";
			echo "</div>";
			
	}
	
	function displayTimeFrom($ttime) {

		$justnu = strtotime("now");
		$inlagg = strtotime($ttime);
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

	function getEveryWord($eachWord) {

		$select = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.kommentar, Artiklar.utpris, tillverkare, link, kategori, no_buy, lagersaldo, bestallningsgrans, bild, momssats, utgangen, link, Kategori.sortPriority ";
		$select .= "FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "LEFT JOIN Moms on Artiklar.momskod = Moms.moms_id ";
		$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id WHERE ";
		$select .= "( ";
		$select .= "kategori like '%" . $eachWord . "%' OR tillverkare like '%" . $eachWord . "%' OR Artiklar.beskrivning like '%" . $eachWord . "%' OR Artiklar.searchTerms like '%" . $eachWord . "%') ";
		$select .= "AND ej_med=0 AND (utgangen=0 OR lagersaldo > 0) AND (demo=0 OR lagersaldo > 0) AND NOT (Artiklar.kategori_id = 486 OR Artiklar.kategori_id = 509 OR Artiklar.kategori_id = 511 OR Artiklar.kategori_id = 512 OR Artiklar.kategori_id = 513) " ;

		$res = mysqli_query($this->conn_my2, $select);
		
		if (mysqli_num_rows($res) > 0) {
		
			return true;
		
		} else {
		
			return false;
		
		}
	}

	function countEveryWord($eachWord) {

		$select = "SELECT COUNT(Artiklar.artnr) AS Antal ";
		$select .= "FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "LEFT JOIN Moms on Artiklar.momskod = Moms.moms_id ";
		$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id WHERE ";
		$select .= "( ";
		$select .= "kategori like '%" . $eachWord . "%' OR tillverkare like '%" . $eachWord . "%' OR Artiklar.beskrivning like '%" . $eachWord . "%' OR Artiklar.searchTerms like '%" . $eachWord . "%') ";
		$select .= "AND ej_med=0 AND (utgangen=0 OR lagersaldo > 0) AND (demo=0 OR lagersaldo > 0) AND NOT (Artiklar.kategori_id = 486 OR Artiklar.kategori_id = 509 OR Artiklar.kategori_id = 511 OR Artiklar.kategori_id = 512 OR Artiklar.kategori_id = 513) " ;
		
		// echo $select;
		// exit;

		$res = mysqli_query($this->conn_my2, $select);
		
		if (mysqli_num_rows($res) > 0) {
		
				while ($row = mysqli_fetch_array($res)):
				
				extract($row);
				
					return $Antal;
				
				endwhile;
		
		} else {
		
			return 0;
		
		}
	}

	function getSearchWords() {

	$rowcolor = true;

	$select  = "SELECT searchTime, searchIP, searchString ";
	$select .= "FROM SearchLogg ";
	$select .= "ORDER BY searchTime ";

	// echo $select;

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"150\"><b>Tidpunkt</b></td>";
			echo "<td width=\"150\"><b>IP-nummer</b></td>";
			echo "<td width=\"550\"><b>Söksträng</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "#E8E8E8";
			} else {
				$backcolor = "#FFCF9F";
			}

			echo "<tr>";
			echo "<td bgcolor=\"$backcolor\">$searchTime</td>";
			if (CCheckIP::checkIpAdress($searchIP)) {
				echo "<td bgcolor=\"$backcolor\"><i>internt</i></td>";
			} else {
				echo "<td bgcolor=\"$backcolor\">$searchIP</td>";
			}
			echo "<td bgcolor=\"$backcolor\">$searchString</td>";
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
			echo "</table>";
	}

	function getSearchGroup($place) {

	$rowcolor = true;

	$select  = "SELECT DATE_FORMAT(searchTime, '%Y') AS DateYear, DATE_FORMAT(searchTime, '%v') AS DateWeek, COUNT(searchID) AS Antal ";
	$select .= "FROM SearchLogg ";
	$select .= "WHERE searchPlace = $place ";
	$select .= "GROUP BY DATE_FORMAT(searchTime, '%Y'), DATE_FORMAT(searchTime, '%v') ";
	$select .= "ORDER BY DateYear DESC, DateWeek DESC ";

	// echo $select;

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"50\"><b>År</b></td>";
			echo "<td width=\"50\"><b>Vecka</b></td>";
			echo "<td width=\"50\"><b>Antal</b></td>";
			echo "<td width=\"150\"><b>&nbsp;</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "#E8E8E8";
			} else {
				$backcolor = "#FFCF9F";
			}

			echo "<tr>";
			echo "<td bgcolor=\"$backcolor\">$DateYear</td>";
			echo "<td bgcolor=\"$backcolor\">$DateWeek</td>";
			echo "<td bgcolor=\"$backcolor\">$Antal</td>";
			echo "<td>&nbsp;&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?show=yes&year=" . $DateYear . "&week=" . $DateWeek . "\">Se detaljer</a></td>";
			/*
			if (CCheckIP::checkIpAdress($searchIP)) {
				echo "<td bgcolor=\"$backcolor\"><i>internt</i></td>";
			} else {
				echo "<td bgcolor=\"$backcolor\">$searchIP</td>";
			}
			*/
			// echo "<td bgcolor=\"$backcolor\">$searchString</td>";
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
			echo "</table>";
	}

	function getSearchWords_v2($year,$week,$place) {

	$rowcolor = true;

	$select  = "SELECT searchTime, searchIP, searchString ";
	$select .= "FROM SearchLogg ";
	$select .= "WHERE DATE_FORMAT(searchTime, '%Y') = $year AND DATE_FORMAT(searchTime, '%v') = $week ";
	$select .= "AND searchPlace = $place ";
	$select .= "ORDER BY searchTime ASC ";

	// echo $select;

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"150\"><b>Tidpunkt</b></td>";
			echo "<td width=\"150\"><b>IP-nummer</b></td>";
			echo "<td width=\"550\"><b>Söksträng</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "#E8E8E8";
			} else {
				$backcolor = "#FFCF9F";
			}

			echo "<tr>";
			echo "<td bgcolor=\"$backcolor\">$searchTime</td>";
			if (CCheckIP::checkIpAdress($searchIP)) {
				echo "<td bgcolor=\"$backcolor\">$searchIP <i>(internt)</i></td>";
			} else {
				echo "<td bgcolor=\"$backcolor\">$searchIP</td>";
			}
			echo "<td bgcolor=\"$backcolor\">$searchString</td>";
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
			echo "</table>";
	}

	function getSearchWordsGroup_v1() {
		global $sortby;

		$Antaltot = 0;
		$rowcolor = true;

		$select  = "SELECT count(searchString) AS Antal, searchString ";
		$select .= "FROM cyberadmin.SearchLogg ";
		$select .= "WHERE searchTime > DATE_SUB(now(), INTERVAL 1 month) ";
		$select .= "AND searchPlace = 0 ";
		$select .= "GROUP BY searchString ";
		if ($sortby == "" || $sortby == "count") {
			$select .= "ORDER BY Antal DESC, searchString ASC ";
		} else {
			$select .= "ORDER BY searchString ASC, Antal DESC ";
		}
		
		// $select .= "LIMIT 250 ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {

				echo "<table cellspacing=\"1\" cellpading=\"2\" width=\"675\">";
				echo "<tr>";
				echo "<td width=\"50\" align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=count\"><b><u>Antal</u></b></a></td>";
				echo "<td width=\"550\"><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=searchstring\"><b><u>Söksträng</u></b></a></td>";
				// echo "<td width=\"75\"><b>&nbsp;</b></td>";
				echo "</tr>";
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
				if ($Antal < 5) {
					break;
				}
				if ($rowcolor == true) {
					$backcolor = "#E8E8E8";
				} else {
					$backcolor = "#FFCF9F";
				}
				$Antaltot = $Antaltot + $Antal;

				echo "<tr>";
				echo "<td bgcolor=\"$backcolor\" align=\"center\">$Antal</td>";
				echo "<td bgcolor=\"$backcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?show=yes&searchstring=" . $searchString . "\">$searchString</a></td>";
				// echo "<td>&nbsp;&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?show=yes&searchstring=" . $searchString . "\">Detaljer</a></td>";
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
				echo "<td colspan=\"3\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
				echo "</tr>";
			
			}
			
			echo "<tr>";
			echo "<td colspan=\"3\"><font color=\"#000000\"><b>Totalt: $Antaltot st sökningar</b></td>";
			echo "</tr>";
			echo "</table>";
	}

	function getSearchWordsGroupDetail_v1($searchstring,$place) {

	$frandatum = date('Y-m-d', strtotime("$frandatum -1 month"));

	$rowcolor = true;

	$select  = "SELECT COUNT(searchString) AS Antal, searchString, searchIP ";
	$select .= "FROM SearchLogg ";
	$select .= "WHERE searchTime BETWEEN '" . $frandatum . "' AND Now() AND searchString = '" . $searchstring . "' ";
	$select .= "AND searchPlace = $place ";
	$select .= "GROUP BY searchIP ";
	$select .= "ORDER BY Antal DESC, searchString ASC ";

	// echo $select;

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table cellspacing=\"1\" cellpading=\"0\" width=\"750\">";
			echo "<tr>";
			echo "<td width=\"50\" align=\"center\"><b>Antal</b></td>";
			echo "<td width=\"150\"><b>IP-nummer</b></td>";
			echo "<td width=\"550\"><b>Söksträng</b></td>";
			echo "</tr>";
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "#E8E8E8";
			} else {
				$backcolor = "#FFCF9F";
			}

			echo "<tr>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\">$Antal</td>";
			if (CCheckIP::checkIpAdress($searchIP)) {
				echo "<td bgcolor=\"$backcolor\">$searchIP <i>(internt)</i></td>";
			} else {
				echo "<td bgcolor=\"$backcolor\">$searchIP</td>";
			}
			echo "<td bgcolor=\"$backcolor\">$searchString</td>";
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
			echo "</table>";
	}

}

?>

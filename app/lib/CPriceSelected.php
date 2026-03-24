<?php

include_once 'Db.php';
// include_once("connections.php");
require_once("CCheckIpNumber.php");

Class CPriceSelected {

	// var $conn_my;

	function __construct() {

		// $this->conn_my = @mysqli_connect(getenv('DB_HOST') ?: 'db', getenv('DB_USER') ?: 'appuser', getenv('DB_PASS') ?: 'apppass');
		// @mysqli_select_db($this->conn_my, getenv('DB_NAME') ?: 'cyberphoto');

	}

	function isValidDateTime($dateTime)
	{
		if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)) {
			if (checkdate($matches[2], $matches[3], $matches[1])) {
				return true;
			}
		}

		return false;
	}

	function getValidPriceList($ID) {

	$select  = "SELECT * FROM cyberphoto.pricelist WHERE ";
	if (!(CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']))) {
	// $select  .= "priceDateFrom < now() AND priceDateTo > now() AND priceActive = -1 AND ";
	$select  .= "priceDateFrom < now() AND priceDateTo > now() AND ";
	}
	$select  .= "priceID = '" . $ID . "' ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
		return true;

		} else {

		return false;

		}

	}

	function getValidPriceListCheckVisual($ID) {

	$select  = "SELECT * FROM cyberphoto.pricelist WHERE ";
	// $select  .= "priceDateFrom < now() AND priceDateTo > now() AND priceActive = -1 AND ";
	$select  .= "priceDateFrom < now() AND priceDateTo > now() AND ";
	$select  .= "priceID = '" . $ID . "' ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
		return true;

		} else {

		return false;

		}

	}

	function getPriceListDetail($ID) {

	$select  = "SELECT * FROM cyberphoto.pricelist WHERE priceID = '" . $ID . "' ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
		$rows = mysqli_fetch_object($res);

		return $rows;

		} else {

		return "";

		}

	}

	function getPriceListArt($ID) {

	$select  = "SELECT artArtnr FROM cyberphoto.pricelistArtnr WHERE pricelist = '" . $ID . "' ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {
		
			extract($row);

			$maparticles .= "Artiklar.artnr='$artArtnr' OR ";
			
			}
		
		}
		
		return $maparticles;

	}

	function getPriceListKat($ID) {

	$select  = "SELECT artArtnr FROM cyberphoto.pricelistArtnr WHERE pricelist = '" . $ID . "' ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {
		
			extract($row);

			if (eregi("-kat-", $artArtnr)) {
				$artArtnr = (eregi_replace("-kat-","",$artArtnr));
			}

			$mapkategori .= "Artiklar.kategori_id = '$artArtnr' OR ";
			
			}
		
		}
		
		return $mapkategori;

	}

	function checkIfKat($ID) {

	$select  = "SELECT priceActive FROM cyberphoto.pricelist WHERE priceID = '" . $ID . "' ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {

			extract($row);

				if ($priceActive == -1) {
					return true;
				} else {
					return false;
				}

			}

		}

	}

	function printDaysLeft($ID) {

	$select  = "SELECT priceDateTo FROM cyberphoto.pricelist WHERE priceID = '" . $ID . "' ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {

			extract($row);
			
			$priceDateTo = preg_replace('/:[0-9][0-9][0-9]/','', $priceDateTo);
			$aterstar = $this->getDaysLeft($priceDateTo);
			
			if ($aterstar < 7) {
				// echo "Prislistan upphör att visas om <font color=\"#CC0000\"><b>$aterstar</b></font> dagar.&nbsp;<a target=\"_blank\" href=\"/order/admin/pricelist.php?show=$ID\">&nbsp;Ändra</a>";
				echo "Prislistan upphör att visas om <font color=\"#CC0000\"><b>$aterstar</b></font> dagar.&nbsp;<a target=\"_blank\" href=\"https://admin.cyberphoto.se/pricelist.php?show=$ID\">&nbsp;Ändra</a>";
			} else {
				// echo "Prislistan upphör att visas om <b>$aterstar</b> dagar.&nbsp;<a target=\"_blank\" href=\"/order/admin/pricelist.php?show=$ID\">&nbsp;Ändra</a>";
				echo "Prislistan upphör att visas om <b>$aterstar</b> dagar.&nbsp;<a target=\"_blank\" href=\"https://admin.cyberphoto.se/pricelist.php?show=$ID\">&nbsp;Ändra</a>";
			}

			}

		}

	}

	// ******* NEDAN BÖRJAR ALL ADMINISTRATIV KOD *******

	function replace_char($string) {
        $from = array("å", "ä", "ö", "Å", "Ä", "Ö"," - ",".","-","?"," ","ø","(",")","!");
        $to = array("a", "a", "o", "A", "A", "O","-","-","-","-","-","o","","","");
        return str_replace($from, $to, $string);
	}
	
	function getPriceListActual($plan = false,$history = false) {

		$desiderow = true;

		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
		echo "<tr>\n";
		echo "<th width=\"100\" colspan=\"4\">Länkar</th>\n";
		echo "<th width=\"100\">Gäller till</th>\n";
		echo "<th width=\"90\" align=\"center\">Återstår</th>\n";
		echo "<th width=\"550\">Rubrik</th>\n";
		echo "<th width=\"200\">Skapad av</th>\n";
		echo "<th width=\"120\">&nbsp;</th>";
		echo "</tr>\n";
		
		$select  = "SELECT * FROM cyberphoto.pricelist ";
		// $select .= "WHERE priceDateFrom < getdate() AND priceDateTo > getdate() ";
		if ($plan) {
			$select .= "WHERE priceDateFrom > now() ";
		} elseif ($history) {
			$select .= "WHERE priceDateTo < now() ";
		} else {
			$select .= "WHERE priceDateFrom < now() AND priceDateTo > now() ";
		}
		if (!$_SERVER['REMOTE_ADDR'] == "81.8.240.115") {
		$select .= "AND NOT (priceID = 72)";
		}
		$select .= "ORDER BY priceDateTo ASC ";
		
		// echo $select;

		// $res = mysqli_query($this->conn_my, $select);
		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
				if ($priceType == 5) {
					$linc_fi = "cybairgun/pricelist/";
					$linc_no = "cybairgun/pricelist/";
					$linc = "foto-video/pricelist/";
				} elseif ($priceType == 4) {
					$linc_fi = "outdoor/pricelist/";
					$linc_no = "outdoor/pricelist/";
					$linc = "outdoor/pricelist/";
				} elseif ($priceType == 3) {
					$linc_fi = "akut/pricelist/";
					$linc_no = "batterier/pricelist/";
					$linc = "batterier/pricelist/";
				} elseif ($priceType == 2) {
					$linc_fi = "mobiili/pricelist/";
					$linc_no = "mobiltelefoni/pricelist/";
					$linc = "mobiltelefoni/pricelist/";
				} elseif ($priceType == 1) {
				} else {
					$linc_fi = "foto-video/pricelist/";
					$linc_no = "foto-video/pricelist/";
					$linc = "foto-video/pricelist/";
				}

				if ($priceHeader_fi != "") {
					$linc_fi .= $priceID . "/" . strtolower(Tools::replace_special_char(trim($priceHeader_fi)));
				} else {
					$linc_fi .= $priceID . "/" . strtolower(Tools::replace_special_char(trim($priceHeader)));
				}
				if ($priceHeader_no != "") {
					$linc_no .= $priceID . "/" . strtolower(Tools::replace_special_char(trim($priceHeader_no)));
				} else {
					$linc_no .= $priceID . "/" . strtolower(Tools::replace_special_char(trim($priceHeader)));
				}
					
				$linc .= $priceID . "/" . strtolower(Tools::replace_special_char(trim($priceHeader)));
				
				if ($plan) {
					$priceDateTo = preg_replace('/:[0-9][0-9][0-9]/','', $priceDateFrom);
					$aterstar = $this->getDaysLeft($priceDateFrom);
				} elseif ($history) {
					$priceDateTo = preg_replace('/:[0-9][0-9][0-9]/','', $priceDateTo);
					$aterstar = $this->getDaysLeft($priceDateTo);
				} else {
					$priceDateTo = preg_replace('/:[0-9][0-9][0-9]/','', $priceDateTo);
					$aterstar = $this->getDaysLeft($priceDateTo);
				}

				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				
				if ((time() - strtotime($priceAddDate)) < 259200) {
					$priceHeader .= " <i><b>New</b></i>";
				}

				echo "<tr>";
				echo "<td bgcolor=\"#FFFFFF\"><a target=\"_blank\" href=\"http://www.cyberphoto.fi/$linc_fi\"><img border=\"0\" src=\"fi_mini.jpg\"></a></td>";
				echo "<td bgcolor=\"#FFFFFF\"><a target=\"_blank\" href=\"http://www.cyberphoto.fi/$linc\"><img border=\"0\" src=\"fisv_mini.jpg\"></a></td>";
				echo "<td bgcolor=\"#FFFFFF\"><a target=\"_blank\" href=\"http://www.cyberphoto.no/$linc_no\"><img border=\"0\" src=\"no_mini.jpg\"></a></td>";
				echo "<td bgcolor=\"#FFFFFF\"><a target=\"_blank\" href=\"http://www.cyberphoto.se/$linc\"><img border=\"0\" src=\"sv_mini.jpg\"></a></td>";
				echo "<td class=\"$rowcolor\">" . date("j M Y", strtotime($priceDateTo)) . "</td>";
				if ($aterstar < 7) {
				echo "<td class=\"$rowcolor\" align=\"right\"><font color=\"red\"><b>" . $aterstar . " dagar&nbsp;&nbsp;</b></td>";
				} else {
				echo "<td class=\"$rowcolor\" align=\"right\">" . $aterstar . " dagar&nbsp;&nbsp;</td>";
				}
				echo "<td class=\"$rowcolor\">" . $priceHeader . "</td>";
				echo "<td class=\"$rowcolor\">" . $priceAddBy . "</td>";
				echo "<td class=\"#FFFFFF\" align=\"center\"><b><a href=\"pricelist.php?show=" . $priceID . "\">Visa detaljer</a></b></td>";
				echo "</tr>";

				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
			
				endwhile;
				
			} else {
			
			echo "<tr>";
			if ($plan) {
				echo "<td colspan=\"11\"><span style=\"background-color: #FF0000\"><font color=\"#FFFFFF\"><b>Inga planerade prislistor finns registrerade</b></span></td>";
			} elseif ($hitory) {
				echo "<td colspan=\"11\"><span style=\"background-color: #FF0000\"><font color=\"#FFFFFF\"><b>Inga utgångna prislistor finns registrerade</b></span></td>";
			} else {
				echo "<td colspan=\"11\"><span style=\"background-color: #FF0000\"><font color=\"#FFFFFF\"><b>Inga aktiva prislistor finns registrerade</b></span></td>";
			}
			echo "</tr>";
			
			}
			
		echo "</table>\n";

	}

	function getPriceListPlan() {

	$desiderow = true;

	echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
	echo "<tr>\n";
	echo "<th width=\"90\">Länkar</th>\n";
	echo "<th width=\"90\">Gäller till</th>\n";
	echo "<th width=\"90\" align=\"center\">Börjar</th>\n";
	echo "<th width=\"550\">Rubrik</th>\n";
	// echo "<th width=\"350\">Rubrik FI</th>\n";
	echo "<th width=\"120\">&nbsp;</th>";
	echo "</tr>\n";

	$select  = "SELECT * FROM cyberphoto.pricelist ";
	// $select .= "WHERE priceDateFrom > getdate() ";
	$select .= "WHERE priceDateFrom > now() ";
	$select .= "ORDER BY priceDateFrom ASC ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			$priceDateFrom = preg_replace('/:[0-9][0-9][0-9]/','', $priceDateFrom);
			$aterstar = $this->getDaysLeft($priceDateFrom);

			if ($desiderow == true) {
				$rowcolor = "firstrow";
			} else {
				$rowcolor = "secondrow";
			}

			echo "<tr>";
			echo "<td bgcolor=\"#FFFFFF\"><a target=\"_blank\" href=\"/pri_selected.php?ID=$priceID\"><img border=\"0\" src=\"sv_mini.jpg\"></a>&nbsp;&nbsp;<a target=\"_blank\" href=\"/pri_selected_fi.php?ID=$priceID\"><img border=\"0\" src=\"fi_mini.jpg\"></a>&nbsp;&nbsp;<a target=\"_blank\" href=\"/pri_selected_fi_se.php?ID=$priceID\"><img border=\"0\" src=\"fisv_mini.jpg\"></a></td>";
			echo "<td class=\"$rowcolor\">" . date("j M Y", strtotime($priceDateFrom)) . "</td>";
			if ($aterstar < 4) {
			echo "<td class=\"$rowcolor\" align=\"right\"><font color=\"red\"><b>" . $aterstar . " dagar&nbsp;&nbsp;</b></td>";
			} else {
			echo "<td class=\"$rowcolor\" align=\"right\">" . $aterstar . " dagar&nbsp;&nbsp;</td>";
			}
			echo "<td class=\"$rowcolor\">" . $priceHeader . "</td>";
			// echo "<td class=\"$rowcolor\">" . $priceHeader_fi . "</td>";
			echo "<td class=\"$rowcolor\" align=\"center\"><b><a href=\"pricelist.php?show=" . $priceID . "\">Visa detaljer</a></b></td>";
			echo "</tr>";
		
			if ($desiderow == true) {
				$desiderow = false;
			} else {
				$desiderow = true;
			}
			
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"6\"><span style=\"background-color: #FF0000\"><font color=\"#FFFFFF\"><b>Inga planerade prislistor finns registrerade</b></span></td>";
		echo "</tr>";
		
		}

	echo "</table>\n";

	}

	function getPriceListHistory() {

	$desiderow = true;

	echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
	echo "<tr>\n";
	echo "<th width=\"90\">Länkar</th>\n";
	echo "<th width=\"90\">Gällde till</th>\n";
	echo "<th width=\"90\" align=\"center\">Slutade</th>\n";
	echo "<th width=\"550\">Rubrik</th>\n";
	// echo "<th width=\"350\">Rubrik FI</th>\n";
	echo "<th width=\"120\">&nbsp;</th>";
	echo "</tr>\n";

	$select  = "SELECT * FROM cyberphoto.pricelist ";
	// $select .= "WHERE priceDateFrom > getdate() ";
	$select .= "WHERE priceDateTo < now() ";
	$select .= "ORDER BY priceDateTo DESC ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			$priceDateTo = preg_replace('/:[0-9][0-9][0-9]/','', $priceDateTo);
			$aterstar = $this->getDaysLeft($priceDateTo);

			if ($desiderow == true) {
				$rowcolor = "firstrow";
			} else {
				$rowcolor = "secondrow";
			}

			echo "<tr>";
			echo "<td bgcolor=\"#FFFFFF\"><a target=\"_blank\" href=\"/pri_selected.php?ID=$priceID\"><img border=\"0\" src=\"sv_mini.jpg\"></a>&nbsp;&nbsp;<a target=\"_blank\" href=\"/pri_selected_fi.php?ID=$priceID\"><img border=\"0\" src=\"fi_mini.jpg\"></a>&nbsp;&nbsp;<a target=\"_blank\" href=\"/pri_selected_fi_se.php?ID=$priceID\"><img border=\"0\" src=\"fisv_mini.jpg\"></a></td>";
			echo "<td class=\"$rowcolor\">" . date("j M Y", strtotime($priceDateTo )) . "</td>";
			if ($aterstar < 4) {
			echo "<td class=\"$rowcolor\" align=\"right\"><font color=\"red\"><b>" . $aterstar . " dagar&nbsp;&nbsp;</b></td>";
			} else {
			echo "<td class=\"$rowcolor\" align=\"right\">" . $aterstar . " dagar&nbsp;&nbsp;</td>";
			}
			echo "<td class=\"$rowcolor\">" . $priceHeader . "</td>";
			// echo "<td class=\"$rowcolor\">" . $priceHeader_fi . "</td>";
			echo "<td class=\"#FFFFFF\" align=\"center\"><b><a href=\"pricelist.php?show=" . $priceID . "\">Visa detaljer</a></b></td>";
			echo "</tr>";
		
			if ($desiderow == true) {
				$desiderow = false;
			} else {
				$desiderow = true;
			}
			
			endwhile;
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"6\"><span style=\"background-color: #FF0000\"><font color=\"#FFFFFF\"><b>Inga planerade prislistor finns registrerade</b></span></td>";
		echo "</tr>";
		
		}

	echo "</table>\n";

	}
	
	function getPriceListActualDetail($show) {

		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
		echo "<tr>\n";
		echo "<th width=\"80\">Gäller från</th>\n";
		echo "<th width=\"80\">Gäller till</th>\n";
		echo "<th width=\"75\">Återstår</th>\n";
		echo "<th width=\"140\">Rubrik</th>\n";
		echo "<th width=\"140\">Rubrik FI</th>\n";
		echo "<th width=\"140\">Payoff text</th>\n";
		echo "<th width=\"140\">Payoff text FI</th>\n";
		echo "<th width=\"50\" align=\"center\">Typ</th>\n";
		echo "<th width=\"100\" align=\"center\">Kommentar</th>\n";
		echo "<th width=\"25\">Av</th>\n";
		echo "<th width=\"100\">Bild</th>\n";
		echo "<th>&nbsp;</th>";
		echo "<th>&nbsp;</th>";
		echo "</tr>\n";
		
		$select  = "SELECT * FROM cyberphoto.pricelist WHERE priceID = '" . $show . "' ";

		// $res = mysqli_query($this->conn_my, $select);
		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
				if ($priceType == 5) {
					$linc_fi = "cybairgun/pricelist/";
					$linc_no = "cybairgun/pricelist/";
					$linc = "foto-video/pricelist/";
				} elseif ($priceType == 4) {
					$linc_fi = "outdoor/pricelist/";
					$linc_no = "outdoor/pricelist/";
					$linc = "outdoor/pricelist/";
				} elseif ($priceType == 3) {
					$linc_fi = "akut/pricelist/";
					$linc_no = "batterier/pricelist/";
					$linc = "batterier/pricelist/";
				} elseif ($priceType == 2) {
					$linc_fi = "mobiili/pricelist/";
					$linc_no = "mobiltelefoni/pricelist/";
					$linc = "mobiltelefoni/pricelist/";
				} elseif ($priceType == 1) {
				} else {
					$linc_fi = "foto-video/pricelist/";
					$linc_no = "foto-video/pricelist/";
					$linc = "foto-video/pricelist/";
				}

				if ($priceHeader_fi != "") {
					$linc_fi .= $priceID . "/" . strtolower(Tools::replace_special_char(trim($priceHeader_fi)));
				} else {
					$linc_fi .= $priceID . "/" . strtolower(Tools::replace_special_char(trim($priceHeader)));
				}
				if ($priceHeader_no != "") {
					$linc_no .= $priceID . "/" . strtolower(Tools::replace_special_char(trim($priceHeader_no)));
				} else {
					$linc_no .= $priceID . "/" . strtolower(Tools::replace_special_char(trim($priceHeader)));
				}
					
				$linc .= $priceID . "/" . strtolower(Tools::replace_special_char(trim($priceHeader)));

				$priceHeader2 = $priceHeader;
				$priceHeader_fi2 = $priceHeader_fi;
				$priceUnderHeader2 = $priceUnderHeader;
				$priceUnderHeader_fi2 = $priceUnderHeader_fi;
				
				if (strlen($priceHeader) >= 17)
					$priceHeader2 = substr ($priceHeader, 0, 17) . "...";
				if (strlen($priceHeader_fi) >= 17)
					$priceHeader_fi2 = substr ($priceHeader_fi, 0, 17) . "...";
				if (strlen($priceUnderHeader) >= 17)
					$priceUnderHeader2 = substr ($priceUnderHeader, 0, 17) . "...";
				if (strlen($priceUnderHeader_fi) >= 17)
					$priceUnderHeader_fi2 = substr ($priceUnderHeader_fi, 0, 17) . "...";
				if (strlen($priceComment) >= 10)
					$priceComment2 = substr ($priceComment, 0, 10) . "...";

				$priceDateFrom = preg_replace('/:[0-9][0-9][0-9]/','', $priceDateFrom);
				$priceDateTo = preg_replace('/:[0-9][0-9][0-9]/','', $priceDateTo);
				
				$aterstar = $this->getDaysLeft($priceDateTo);

				echo "<tr>";
				echo "<td bgcolor=\"#CCCC00\">" . date("j M Y", strtotime($priceDateFrom)) . "</td>";
				echo "<td bgcolor=\"#CCCC00\">" . date("j M Y", strtotime($priceDateTo)) . "</td>";
				if ($aterstar < 4) {
				echo "<td bgcolor=\"#CCCC00\" align=\"right\"><fontcolor=\"red\"><b>" . $aterstar . " dagar&nbsp;&nbsp;</b></td>";
				} else {
				echo "<td bgcolor=\"#CCCC00\" align=\"right\">" . $aterstar . " dagar&nbsp;&nbsp;</td>";
				}
				echo "<td bgcolor=\"#CCCC00\"><a onMouseOver=\"this.T_WIDTH=450;return escape('<b>$priceHeader</b>')\">" . $priceHeader2 . "</a></td>";
				echo "<td bgcolor=\"#CCCC00\"><a onMouseOver=\"this.T_WIDTH=450;return escape('<b>$priceHeader_fi</b>')\">" . $priceHeader_fi2 . "</a></td>";
				echo "<td bgcolor=\"#CCCC00\"><a onMouseOver=\"this.T_WIDTH=450;return escape('<b>$priceUnderHeader</b>')\">" . $priceUnderHeader2 . "</a></td>";
				echo "<td bgcolor=\"#CCCC00\"><a onMouseOver=\"this.T_WIDTH=450;return escape('<b>$priceUnderHeader_fi</b>')\">" . $priceUnderHeader_fi2 . "</a></td>";
				if ($priceType == 1) {
				echo "<td bgcolor=\"#CCCC00\" align=\"center\">Pro</td>";
				} elseif ($priceType == 2) {
				echo "<td bgcolor=\"#CCCC00\" align=\"center\">Mobil</td>";
				} else {
				echo "<td bgcolor=\"#CCCC00\" align=\"center\">Normal</td>";
				}
				echo "<td bgcolor=\"#CCCC00\"><a onMouseOver=\"this.T_WIDTH=550;return escape('<b>$priceComment</b>')\">" . $priceComment2 . "</a></td>";
				echo "<td bgcolor=\"#CCCC00\" align=\"center\">" . $priceCreatedBy . "</td>";
				if (strlen($pricePicture) > 2) {
				echo "<td bgcolor=\"#CCCC00\" align=\"center\"><a onMouseOver=\"this.T_WIDTH=250;return escape('<img border=\'0\' src=\'$pricePicture\'>')\">Ja</a></td>";
				} else {
				echo "<td bgcolor=\"#CCCC00\" align=\"center\">&nbsp;</td>";
				}
				echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><b><a href=\"pricelist.php?change=" . $priceID . "\">Ändra</a></b></td>";
				if (!$this->getPriceListArtnrValid($priceID)) {
				echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><b><a href=\"pricelist.php?deletepricelist=" . $priceID . "\">Ta bort</a></b></td>";
				} else {
				echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><b>&nbsp;</b></td>";
				}		
				echo "</tr>";
			
				endwhile;

			} else {
			
				echo "<tr>";
				echo "<td colspan=\"14\"><span style=\"background-color: #FF0000\"><fontcolor=\"#FFFFFF\"><b>Inga poster aktiva</b></span></td>";
				echo "</tr>";
			
			}

		echo "</table>\n";

		echo "<div class=top10><b>Länken till denna prislista blir</b></div>\n";
		echo "<div class=top10><a target=\"_blank\" href=\"http://www.cyberphoto.fi/$linc_fi\"><img border=\"0\" src=\"fi_mini.jpg\"></a>&nbsp;&nbsp;$linc_fi</div>\n";
		echo "<div class=top5><a target=\"_blank\" href=\"http://www.cyberphoto.fi/$linc\"><img border=\"0\" src=\"fisv_mini.jpg\"></a>&nbsp;&nbsp;$linc</a></div>\n";
		echo "<div class=top5><a target=\"_blank\" href=\"http://www.cyberphoto.no/$linc_no\"><img border=\"0\" src=\"no_mini.jpg\"></a>&nbsp;&nbsp;$linc_no</a></div>\n";
		echo "<div class=top5><a target=\"_blank\" href=\"http://www.cyberphoto.se/$linc\"><img border=\"0\" src=\"sv_mini.jpg\"></a>&nbsp;&nbsp;$linc</a></div>\n";
		
	}

	function getPriceListArtnr($show) {
		global $clean_check;

	require_once("CWebADIntern.php");
	$adintern = new CWebADIntern();
	$show_this = true;
	
	if ($show == 1066 || $show == 1067 || $show == 1068) {
		echo "<form>\n";
		echo "<input type=\"hidden\" value=\"$show\" name=\"show\">\n";
		if ($clean_check == "yes") {
			echo "<input type=\"checkbox\" name=\"clean_check\" value=\"yes\" onClick=\"submit()\" checked>&nbsp;&nbsp;Visa färdiga produkter";
		} else {
			echo "<input type=\"checkbox\" name=\"clean_check\" value=\"yes\" onClick=\"submit()\">&nbsp;&nbsp;Visa färdiga produkter";
		}
		echo "</form>\n";
	}
	
	echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"1250\">\n";
	echo "<tr>\n";
	if ($this->checkIfKat($show)) {
		echo "<th width=\"110\">Kategori</th>\n";
	} else {
		echo "<th width=\"110\">Artikel</th>\n";
	}
	echo "<th width=\"520\">Benämning</th>\n";
	echo "<th width=\"60\" class=\"align_center\">I lager</th>\n";
	if ($show == 1066 || $show == 1067 || $show == 1068) {
		echo "<th width=\"70\" class=\"align_center\">Hyllplats</th>\n";
		echo "<th width=\"20\" class=\"align_center\">VMB</th>\n";
	}
	echo "<th width=\"50\" class=\"align_center\">Visas</th>\n";
	echo "<th width=\"50\">&nbsp;</th>\n";
	echo "<th width=\"110\" class=\"align_center\">Produkt skapad</th>\n";
	echo "<th width=\"50\" class=\"align_center\">Ålder</th>\n";
	echo "<th>&nbsp;</th>";
	echo "<th>&nbsp;</th>";
	echo "</tr>\n";

	if ($this->checkIfKat($show)) {
		$select  = "SELECT *  ";
		$select .= "FROM cyberphoto.pricelistArtnr ";
		$select .= "WHERE pricelist = '" . $show . "' ";
	} else {
		$select  = "SELECT pla.*, a.lagersaldo, a.utgangen, a.ej_med, t.Tillverkare, a.beskrivning, a.date_add, a.m_product_id, ";
		$select .= "DATEDIFF(now(),a.date_add) AS DiffDate, a.isTradeIn ";
		$select .= "FROM cyberphoto.pricelistArtnr pla ";
		$select .= "JOIN Artiklar a ON a.artnr = pla.artArtnr ";
		$select .= "JOIN Tillverkare t on a.tillverkar_id = t.tillverkar_id ";
		$select .= "WHERE pla.pricelist = '" . $show . "' ";
		// $select .= "ORDER BY a.date_add ASC, a.lagersaldo DESC, t.Tillverkare, a.beskrivning ";
		$select .= "ORDER BY a.lagersaldo DESC, t.Tillverkare, a.beskrivning ";
	}

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);
	// echo $select;

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($clean_check == "yes") {
				// echo "paj";
				$hyllplats = $adintern->returnStoreLocation($artArtnr);
				
				// if ($ej_med == 0 && ($hyllplats == "Standard" || preg_match("/ibk/i",$hyllplats) || preg_match("/ibs/i",$hyllplats))) {
				// if ($ej_med == -1 && $hyllplats == "Standard") {
				// if ($ej_med == 0 || $lagersaldo < 1 || ($hyllplats == "Standard" || preg_match("/ibk/i",$hyllplats) || preg_match("/ibs/i",$hyllplats))) {
				if ($ej_med == 0 || $lagersaldo < 1 || $hyllplats == "Standard" || preg_match("/ibk/i",$hyllplats) || preg_match("/ibs/i",$hyllplats)) {
					$show_this = false;
				}
			}
			
			if ($show_this) {
			
					if ($this->checkIfKat($show)) {
					// if (eregi("-kat-", $artArtnr)) {
						$artArtnr = (eregi_replace("-kat-","",$artArtnr));
						$beskrivning = $this->getbeskrivningKat($artArtnr);
					} else {
						// $beskrivning = $this->getbeskrivningArt($artArtnr);
						$beskrivning = $Tillverkare . " " . $beskrivning;
						// $lagersaldo = $this->getlagersaldoArt($artArtnr);
					}
					
					echo "<tr>";
					// echo "<td bgcolor=\"#FFFF00\"><font face=\"Verdana\" size=\"1\"><a target=\"_blank\" href=\"/info.php?article=$artArtnr\">" . $artArtnr . "</a></td>";
					echo "<td bgcolor=\"#FFFF00\">" . $artArtnr . "</td>";
					echo "<td bgcolor=\"#FFFF00\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$artArtnr\">" . $beskrivning . "</a></td>";
					echo "<td bgcolor=\"#FFFF00\" align=\"center\">" . $lagersaldo . "</td>";
					if ($show == 1066 || $show == 1067 || $show == 1068) {
					echo "<td bgcolor=\"#FFFF00\" align=\"center\">" . $adintern->returnStoreLocation($artArtnr) . "</td>";
						if ($isTradeIn == -1) {
							echo "<td align=\"center\"><img title=\"VMB produkt\" border=\"0\" src=\"recycle.jpg\"></td>";
						} else {
							echo "<td bgcolor=\"#FFFFFF\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
						}
					}
					if ($ej_med == -1 || ($utgangen == -1 && $lagersaldo < 1)) {
						echo "<td align=\"center\"><img border=\"0\" src=\"status_red.jpg\"></td>";
					} else {
						echo "<td align=\"center\"><img border=\"0\" src=\"status_green.jpg\"></td>";
					}
					echo "<td align=\"center\">" . $adintern->displayProductInventoryOnlyAllocated($artArtnr) . "</td>";
					echo "<td bgcolor=\"#FFFF00\" align=\"center\">" . date("Y-m-d", strtotime($date_add)) . "</td>";
					echo "<td bgcolor=\"#FFFF00\" align=\"center\">" . $DiffDate . "</td>";
					echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><a href=\"pricelist.php?deletearticle=" . $artID . "&show=" . $pricelist . "\">Ta bort</a></td>";
					echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$artArtnr&m_product_id=$m_product_id');\">Uppdatera</a></td>";
					echo "</tr>";
					
			}
			$show_this = true;
		
			endwhile;

			echo "<tr>";
			echo "<td bgcolor=\"#FFFFFF\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
			echo "<td bgcolor=\"#FFFFFF\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
			echo "<td bgcolor=\"#FFFFFF\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"3\" bgcolor=\"#FFFFFF\"><font face=\"Verdana\" size=\"1\"><b><a href=\"" . $_SERVER['PHP_SELF'] . "?confirmdelete=" . $pricelist . "&show=" . $pricelist . "\"><img border=\"0\" src=\"recycle.jpg\">&nbsp;&nbsp;Ta bort alla poster kopplade till prislistan</a></td>";
			echo "</tr>";
			
		} else {
		
		echo "<tr>";
		echo "<td colspan=\"14\"><span style=\"background-color: #FF0000\"><font face=\"Verdana\" size=\"1\" color=\"#FFFFFF\"><b>Inga produkter kopplade till prislistan ännu.</b></span></td>";
		echo "</tr>";
		
		}

	echo "</table>\n";

	}

	function getPriceListArtnrValid($priceID) {

	$select  = "SELECT * FROM cyberphoto.pricelistArtnr WHERE pricelist = '" . $priceID . "' ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
		return true;
			
		} else {
		
		return false;
		
		}

	}

	function getbeskrivningArt($artArtnr) {
		
	$select  = "SELECT lagersaldo, beskrivning, Tillverkare.tillverkare ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "WHERE artnr = '" . $artArtnr . "' ";
	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

		extract(mysqli_fetch_array($res));

		// return $tillverkare . " ". $beskrivning . " (" . $lagersaldo . ")";
		return $tillverkare . " ". $beskrivning;
		
		}

	}	

	function getbeskrivningKat($artArtnr) {
		
	$select  = "SELECT kategori FROM cyberphoto.Kategori WHERE kategori_id = '" . $artArtnr . "' ";
	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

		extract(mysqli_fetch_array($res));

		// return $tillverkare . " ". $beskrivning . " (" . $lagersaldo . ")";
		return $kategori;
		
		}

	}	

	function getlagersaldoArt($artArtnr) {
		
	$select  = "SELECT lagersaldo FROM cyberphoto.Artiklar WHERE artnr = '" . $artArtnr . "' ";
	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

		extract(mysqli_fetch_array($res));

		return $lagersaldo;
		
		}

	}	

	function check_artikel_status($addartnr) {
		
	$select  = "SELECT artnr FROM cyberphoto.Artiklar WHERE binary artnr = '" . $addartnr . "' ";
	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);
	// echo $select;

		if (mysqli_num_rows($res) > 0) {

			return true;
		
		} else {
		
			return false;
		
		}

	}	
	
	function getAnstalldaNewOld() {

	global $addcreatedby;

	$select  = "SELECT sign, namn FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";

	$res = mssql_query ($select);

		while ($row = mssql_fetch_array($res)) {
		
		extract($row);

		echo "<option value=\"$sign\"";
			
		if ($addcreatedby == $sign) {
			echo " selected";
		}
			
		echo ">" . $namn . "</option>";
			
		
		// endwhile;

		}

	}

	function getAnstalldaNew() {

		global $addcreatedby;

		$select  = "SELECT sign, namn FROM cyberphoto.Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";

		// $res = mysqli_query($select);
		$res = mysqli_query(Db::getConnection(), $select);

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			echo "<option value=\"$sign\"";
				
			if ($addcreatedby == $sign) {
				echo " selected";
			}
				
			echo ">" . $namn . "</option>";
				
			
			// endwhile;

			}

	}
	
	function getAnstallda() {
		global $addcreatedby;
		
		$select  = "SELECT sign, namn FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";
		// echo $select;

		$res = mysqli_query($conn_my, $select);

			while ($row = mysqli_fetch_array($res, $this->conn_my)) {
			
			extract($row);

			echo "<option value=\"$sign\"";
				
			if ($addcreatedby == $sign) {
				echo " selected";
			}
				
			echo ">" . $namn . "</option>";
				
			
			// endwhile;

			}

	}

	function getSpecPricelist($priceID) {

	$select  = "SELECT * FROM cyberphoto.pricelist WHERE priceID = '" . $priceID . "' ";

	// $res = mysqli_query($this->conn_my, $select);
	$res = mysqli_query(Db::getConnection(), $select);

	$rows = mysqli_fetch_object($res);

	return $rows;

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

	function articleDelete($deletearticle,$show) {

		// $conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($conn_my, getenv('DB_NAME') ?: 'cyberphoto');

		// mssql_query ("DELETE FROM pricelistArtnr WHERE artID = '" . $deletearticle . "'");

		$updt = "DELETE FROM cyberphoto.pricelistArtnr WHERE artID = '" . $deletearticle . "'";

		// $res = mysqli_query($conn_my, $updt);
		$res = mysqli_query(Db::getConnection(true), $updt);

		header("Location: pricelist.php?show=$show");

	}

	function AllArticleDelete($alldeletearticle) {

		// $conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($conn_my, getenv('DB_NAME') ?: 'cyberphoto');

		// mssql_query ("DELETE FROM pricelistArtnr WHERE pricelist = '" . $alldeletearticle . "'");

		$updt = "DELETE FROM cyberphoto.pricelistArtnr WHERE pricelist = '" . $alldeletearticle . "'";

		// $res = mysqli_query($conn_my, $updt);
		$res = mysqli_query(Db::getConnection(true), $updt);

		header("Location: pricelist.php?show=$alldeletearticle");

	}

	function pricelistDelete($deletepricelist) {

		// $conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($conn_my, getenv('DB_NAME') ?: 'cyberphoto');

		// mssql_query ("DELETE FROM pricelist WHERE priceID = '" . $deletepricelist . "'");

		$updt = "DELETE FROM cyberphoto.pricelist WHERE priceID = '" . $deletepricelist . "'";

		// $res = mysqli_query($conn_my, $updt);
		$res = mysqli_query(Db::getConnection(true), $updt);

		header("Location: pricelist.php");

	}

	function AddPriceList($addrubrik,$addrubrik_fi,$addpayoff,$addpayoff_fi,$addtype,$addcomment,$addcreatedby,$addfrom,$addto,$addpicture,$addactive,$addrubrik_no = "",$addpayoff_no = "",$addgallerylist = null) {

		// $conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($conn_my, getenv('DB_NAME') ?: 'cyberphoto');

		// mssql_query ("INSERT INTO pricelist (priceHeader,priceHeader_fi,priceUnderHeader,priceUnderHeader_fi,priceType,priceComment,priceCreatedBy,priceDateFrom,priceDateTo,pricePicture,priceActive) VALUES ('$addrubrik','$addrubrik_fi','$addpayoff','$addpayoff_fi','$addtype','$addcomment','$addcreatedby','$addfrom','$addto','$addpicture','$addactive') ");

		$updt = "INSERT INTO cyberphoto.pricelist (priceHeader,priceHeader_fi,priceHeader_no,priceUnderHeader,priceUnderHeader_fi,priceUnderHeader_no,priceType,priceComment,priceCreatedBy,priceDateFrom,priceDateTo,pricePicture,priceActive,priceListType,priceCreated,priceAddBy,priceAddDate,priceAddIP) VALUES ('$addrubrik','$addrubrik_fi','$addrubrik_no','$addpayoff','$addpayoff_fi','$addpayoff_no','$addtype','$addcomment','$addcreatedby','$addfrom','$addto','$addpicture','$addactive','$addgallerylist',now(),'$addcreatedby',now(),'" . $_SERVER['REMOTE_ADDR'] . "')";

		// $res = mysqli_query($conn_my, $updt);
		$res = mysqli_query(Db::getConnection(true), $updt);
		$backto = mysqli_insert_id();
		// echo $backto;
		// exit;

		// header("Location: pricelist.php");
		header("Location: pricelist.php?show=$backto");

	}

	function ChangePriceList($addid,$addrubrik,$addrubrik_fi,$addpayoff,$addpayoff_fi,$addtype,$addcomment,$addcreatedby,$addfrom,$addto,$addpicture,$addactive,$addrubrik_no = "",$addpayoff_no = "",$addgallerylist = null) {

		// $conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($conn_my, getenv('DB_NAME') ?: 'cyberphoto');

		// mssql_query ("UPDATE pricelist SET priceHeader = '$addrubrik', priceHeader_fi = '$addrubrik_fi',priceUnderHeader = '$addpayoff',priceUnderHeader_fi = '$addpayoff_fi',priceType = '$addtype',priceComment = '$addcomment',priceCreatedBy = '$addcreatedby',priceDateFrom = '$addfrom',priceDateTo = '$addto',pricePicture = '$addpicture',priceActive = '$addactive' WHERE priceID = '$addid' ");

		$updt = "UPDATE cyberphoto.pricelist SET priceHeader = '$addrubrik', priceHeader_fi = '$addrubrik_fi', priceHeader_no = '$addrubrik_no', priceUnderHeader = '$addpayoff', priceUnderHeader_fi = '$addpayoff_fi', priceUnderHeader_no = '$addpayoff_no', priceType = '$addtype',priceComment = '$addcomment',priceCreatedBy = '$addcreatedby',priceDateFrom = '$addfrom',priceDateTo = '$addto',pricePicture = '$addpicture',priceActive = '$addactive',priceListType = '$addgallerylist' WHERE priceID = '$addid'";

		// $res = mysqli_query($conn_my, $updt);
		$res = mysqli_query(Db::getConnection(true), $updt);

		header("Location: pricelist.php?show=$addid");

	}

	function addPriceListArticle($addpricelist,$addartnr) {

		// $conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($conn_my, getenv('DB_NAME') ?: 'cyberphoto');

		// mssql_query ("INSERT INTO pricelistArtnr (pricelist,artArtnr) VALUES ('$addpricelist','$addartnr') ");

		$updt = "INSERT INTO cyberphoto.pricelistArtnr (pricelist,artArtnr) VALUES ('$addpricelist','$addartnr')";

		// $res = mysqli_query($conn_my, $updt);
		$res = mysqli_query(Db::getConnection(true), $updt);

		header("Location: pricelist.php?addart=yes&show=$addpricelist");

	}

	function check_artikel_status_in_pricelist($pricelist,$addartnr) {
		
		$select  = "SELECT *  ";
		$select .= "FROM cyberphoto.pricelistArtnr ";
		$select .= "WHERE pricelist = '" . $pricelist . "' AND binary artArtnr = '" . $addartnr . "' ";
		// $res = mysqli_query($this->conn_my, $select);
		$res = mysqli_query(Db::getConnection(), $select);
		// echo $select;
		// exit;

		if (mysqli_num_rows($res) > 0) {
			return true;
		} else {
			return false;
		}

	}
	
	
}

?>

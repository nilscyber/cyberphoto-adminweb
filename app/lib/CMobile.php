<?php
require_once("CCheckIpNumber.php");
require_once("Db.php");

include("connections.php");

Class CMobile {
	var $conn_my;
	var $conn_my2;

	function __construct() {
		global $fi;
			
		$this->conn_my = Db::getConnection();
		$this->conn_my2 = Db::getConnection(true);

		
	}

	function getMobilePhone($artnr) {
		
		$select = "SELECT tillverkare, beskrivning FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		$select .= "AND (kategori_id = 336 OR kategori_id = 462) ";
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
		echo "<tr>";
		echo "<td width=\"10\">&nbsp;</td>\n";
		echo "<td>";

		if (mysqli_num_rows($res) > 0) {
			
			echo "<a href=\"info.php?article=$row->artnr\"><b>" . $row->tillverkare . " " . $row->beskrivning . "</b></a>\n";
		
		} else {
		
			echo "<b>V�LJ TELEFON I LISTAN</b>";
		
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}	

	function getMobilePhoneList($artnr2) {
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			echo $artnr2;
		}
		// echo $artnr2;
		
		$select = "SELECT artnr, tillverkare, beskrivning FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		// $select .= "WHERE (kategori_id = 336 OR kategori_id = 462) AND utpris > 0 ";
		$select .= "WHERE kategori_id = 336 AND utpris > 0 ";
		$select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) AND (demo=0 OR lagersaldo > 0) ";
		$select .= "ORDER BY tillverkare, beskrivning ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>\n";
			// echo "<td width=\"10\"><img border=\"0\" src=\"pic/bord_1.jpg\"></td>\n";
			echo "<td>";
			
			// echo "<select name=\"artnr\" onchange=\"this.form.submit();\">\n";
			echo "<select name=\"article\" onchange=\"this.form.submit();\">\n";
			// echo "<option value=\"\">V�lj</option>\n";

			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
				
				$displayname = $tillverkare . " " . $beskrivning;

				if (strlen($displayname) >= 60)
					$displayname = substr ($displayname, 0, 60) . "...";

				echo "<option value=\"$artnr\"";
				
				if ($artnr == $artnr2) {
					echo " selected";
				}
				
				// echo ">" . $tillverkare . " " . $beskrivning . "</option> \n";
				echo ">" . $displayname . "</option> \n";
			
			}

			echo "</select>\n";
			echo "</td>\n";
			echo "<td width=\"150\" align=\"right\"><img border=\"0\" src=\"/pic/stamp_frakt.gif\"></td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			
			// echo "<br><br>";
			
		}

	}	

	function getMobilePhoneList_v2($artnr2,$type) {
		
		// echo $artnr2;
		
		$select = "SELECT artnr, tillverkare, beskrivning, bild FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		// $select .= "WHERE (kategori_id = 336 OR kategori_id = 462) AND utpris > 0 ";
		if ($type == 2) { // om mobilt bredband
			$select .= "WHERE abb_data = -1 AND utpris > 0 ";
		} else {
			$select .= "WHERE kategori_id = 336 AND utpris > 0 ";
		}
		$select .= "AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) AND (demo=0 OR lagersaldo > 0) ";
		$select .= "ORDER BY tillverkare, beskrivning ";
		if ($type == 2) {
			// echo $select;
		}
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>\n";
			// echo "<td width=\"10\"><img border=\"0\" src=\"pic/bord_1.jpg\"></td>\n";
			echo "<td>";
			
			// echo "<select name=\"artnr\" onchange=\"this.form.submit();\">\n";
			echo "<select name=\"article\" onchange=\"this.form.submit();\">\n";
			// echo "<option value=\"\">V�lj</option>\n";

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);
			
			$displaypicture = $this->getMobilePhonePicture($artnr2);
			
			$displayname = $tillverkare . " " . $beskrivning;

			if (strlen($displayname) >= 60)
				$displayname = substr ($displayname, 0, 60) . "...";

			echo "<option value=\"$artnr\"";
			
			if ($artnr == $artnr2) {
				echo " selected";
			}
			
			// echo ">" . $tillverkare . " " . $beskrivning . "</option> \n";
			echo ">" . $displayname . "</option> \n";
			
			}

			echo "</select>\n";
			echo "</td>\n";
			echo "<td width=\"120\" align=\"center\"><img title=\"Vi bjuder p� frakten n�r du best�ller mobiltelefon med abonnemang (g�ller postpaket).\"border=\"0\" src=\"/pic/stamp_frakt_v3.gif\"></td>\n";
			echo "<td width=\"70\" align=\"center\">$displaypicture</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			
			// echo "<br><br>";
			
		}

	}	

	function getMobileOperator($type) {
		global $op, $artnr, $ftg;
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $artnr;
		}
		
		$select = "SELECT DISTINCT mobile_plans.operator_id, mobile_operator.name ";
		$select .= "FROM mobile_plans ";
		$select .= "LEFT JOIN mobile_operator ON mobile_plans.operator_id = mobile_operator.operator_id ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND isactive = -1 ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND isactive = -1 ";
		if ($ftg == 1) {
			$select .= "AND foretag = 1 ";
		} else {
			$select .= "AND foretag = 0 ";
		}
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND iswebstorefeatured = -1 ";
			// $select .= "AND NOT mobile_plans.operator_id IN (2,3) "; // 2013-04-30, idag som begr�nsar vi k�p av telia och halebop, dock endast externt
			// 2013-08-01, idag �r det tillbaka igen
		}
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			$select .= "AND NOT mobile_plans.operator_id IN (2,3) ";
		}
		if ($artnr == "p7500" || $artnr == "p7500w") {
			// $select .= "AND mobile_plans.operator_id = 2 ";
		}
		$select .= "AND NOT (mobile_operator.name IS NULL) ";
		if ($type == 2) { // om mobilt bredband
			$select .= "AND abb_type = 2 ";
		} else {
			$select .= "AND abb_type = 1 ";
		}
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>";
			// echo "<td width=\"10\"><img border=\"0\" src=\"pic/bord_2.jpg\"></td>\n";
			// echo "<td width=\"100\"><img border=\"0\" src=\"pic/choose_op.png\"></td>\n";
			echo "<td width=\"100\" class=\"mob_head_dep\">V�lj operat�r:</td>\n";
			echo "<td>";
			
			// echo "<select name=\"artnr\" onchange=\"this.form.submit();\">\n";
			echo "<select class=\"abb_input_operator\" name=\"op\" onchange=\"this.form.submit();\">\n";
		
			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);

				echo "<option value=\"$operator_id\"";
				
				if ($operator_id == $op) {
					echo " selected";
				}
				
				echo ">" . $name . "</option> \n";
				
				/*
				echo "<td width=\"18\"><input type=\"radio\" value=\"$operator_id\" name=\"op\" ";
				// echo "<td width=\"25\"><input type=\"radio\" value=\"$operator_id\" name=\"op\" onClick=\"submit()\"></td>";
				if ($operator_id == $op) {
					echo " checked ";
				}
				echo "onClick=\"submit()\"></td>\n";
				echo "<td width=\"50\">";
				if ($operator_id == $op) {
					echo "<b>";
				}
				echo "$name</b></td>\n";
				*/
			
			}
			
			echo "</select>\n";
			echo "</td>\n";
			echo "</table>\n";
			
		
		}
	
	}	

	function getTypeAbb($type) {
		global $typeabb, $abb_data;
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $abb_data;
		}
		
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
		echo "<tr>\n";
		// echo "<td width=\"105\"><a onMouseOver=\"this.T_WIDTH=600;return escape('<b>Teckningstyp</b><br>V�lj om du vill teckna ett nytt abonnemang, f�rl�nga befintligt abonnemang, byta operat�r eller byta fr�n kontantkort till abonnemang.<ul><li>Nytt abonnemang = Nytt nummer</li><li>Portering = Beh�lla sitt nummer men byta operat�r fr�n Tex Telia till Tele2</li><li>F�rl�ngning = F�rl�ng det nummer du redan har</li><li>Konvertering = Beh�lla sitt nummer men byta till abonnemang fr�n kontantkort.</li></ul><p>T�nk p� att vid konvertering och portering fr�n kontantkort s� <u><b>m�ste</b></u> kontantkortet vara registrerat.</p>')\"><img border=\"0\" src=\"pic/choose_type.png\"></a></td>\n";
		echo "<td width=\"100\" class=\"mob_head_dep underline\"><span onclick=\"show_hide('info_abb_teckning');\" style=\"cursor:pointer;\">Teckningstyp:</span></td>\n";
		echo "<td>\n";
			
		echo "<select class=\"abb_input_select\" name=\"typeabb\" onchange=\"this.form.submit();\">\n";
		if ($typeabb == 1) {
			echo "<option value=\"1\" selected>Nytt abonnemang</option>\n";
		} else {
			echo "<option value=\"1\">Nytt abonnemang</option>\n";
		}
		if ($abb_data != -1) {
			if ($typeabb == 2) {
				echo "<option value=\"2\" selected>Portering (byta operat�r)</option>\n";
			} else {
				echo "<option value=\"2\">Portering (byta operat�r)</option>\n";
			}
		}
		if ($typeabb == 3 || $typeabb == "") {
			echo "<option value=\"3\" selected>F�rl�ngning</option>\n";
		} else {
			echo "<option value=\"3\">F�rl�ngning</option>\n";
		}
		if ($abb_data != -1) {
			if ($typeabb == 4) {
				echo "<option value=\"4\" selected>Konvertering (kontantkort till abonnemang)</option>\n";
			} else {
				echo "<option value=\"4\">Konvertering (kontantkort till abonnemang)</option>\n";
			}
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
			
	}	

	function getMobilePhonePicture($artnr) {
		
		
		$select = "SELECT artnr, bild FROM Artiklar ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		// $select .= "AND (kategori_id = 336 OR kategori_id = 462) "; // varf�r jag la dit denna i f�rsta l�get kan man ju �ngsligt undra!!!
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			// return "<a href=\"info.php?article=$row->artnr\"><img border=\"0\" style=\"border: 1px solid #C0C0C0\" src=\"/thumbs/medium/bilder/".$row->bild ."\"</a>" ;
			return "<img border=\"0\" style=\"border: 1px solid #C0C0C0\" src=\"/thumbs/medium/bilder/".$row->bild ."\"" ;
		
		} else {
		
			return "";
		
		}
	}	

	function getOperatorAbbList($op,$type) {
		global $abb, $op, $ftg;
		
		// $abb = urldecode($abb);
		// $abb = urlencode($abb);
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $abb;
		}

		
		// $select = "SELECT DISTINCT mobile_plans.name AS AbbNamn, mobile_plans.iswebstorefeatured AS VisaKund, mobile_operator.name ";
		$select = "SELECT DISTINCT mobile_plans.name AS AbbNamn, mobile_plans.iswebstorefeatured AS VisaKund, mobile_operator.name, mobile_plans.foretag ";
		$select .= "FROM mobile_plans ";
		$select .= "LEFT JOIN mobile_operator ON mobile_plans.operator_id = mobile_operator.operator_id ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND isactive = -1 ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND isactive = -1 ";
		if ($ftg == 1) {
			$select .= "AND foretag = 1 ";
		} else {
			$select .= "AND foretag = 0 ";
		}
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND mobile_plans.plan_length < 100 AND mobile_plans.operator_id = '" . $op . "' ";
		if ($type == 2) { // om mobilt bredband
			// $select .= "AND abb_type = 2 ";
			$select .= "AND abb_type IN (1,2) ";
		} else {
			$select .= "AND abb_type = 1 ";
		}
		$select .= "ORDER BY mobile_plans.foretag ASC, mobile_plans.name ASC ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>\n";
			// echo "<td width=\"10\"><img border=\"0\" src=\"pic/bord_3.jpg\"></td>\n";
			// echo "<td width=\"105\"><img border=\"0\" src=\"pic/choose_abb.png\"></td>\n";
			echo "<td width=\"100\" class=\"mob_head_dep\">Abonnemang:</td>\n";
			echo "<td>";

		if (mysqli_num_rows($res) > 0) {
			
			echo "<select class=\"abb_input_select\" name=\"abb\" onchange=\"this.form.submit();\" >\n";
			echo "<option value=\"\">V�lj</option>\n";

			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
				
				$AbbNamn2 = $AbbNamn;
				
				$AbbNamn2 = preg_replace("/01GB/i", "1GB", $AbbNamn2);
				$AbbNamn2 = preg_replace("/05GB/i", "5GB", $AbbNamn2);
	
				echo "<option value=\"" . $AbbNamn . "\"";
				
				if ($AbbNamn == $abb) {
					echo " selected";
				}
				
				if ($VisaKund == -1) {
					if ($foretag == 1) {
						echo ">" . $name . " " . $AbbNamn2 . " - F�retagsabonnemang</option> \n";
					} else {
						echo ">" . $name . " " . $AbbNamn2 . "</option> \n";
					}
				} else {
					if ($foretag == 1) {
						echo ">" . $name . " " . $AbbNamn2 . " - F�retagsabonnemang - Visas EJ</option> \n";
					} else {
						echo ">" . $name . " " . $AbbNamn2 . " - Visas EJ</option> \n";
					}
				}
			
			}

			echo "</select>\n";
			
			// echo "<br><br>";

		}

			echo "</td>\n";
			/*
			if ($op == 1) {
				if ($type == 2) {
					echo "<td align=\"center\" width=\"75\"><a class=\"linku\" href=\"javascript:winPopupCenter(500, 870, 'http://www.cyberphoto.se/abonnemang/" . $name . "_data_abonnemang.php');\">Se �versikt</a></td>\n";
				} else {
					echo "<td align=\"center\" width=\"75\"><a class=\"linku\" href=\"javascript:winPopupCenter(450, 1000, 'http://www.cyberphoto.se/abonnemang/" . $name . "_abonnemang.php');\">Se �versikt</a></td>\n";
				}
			} elseif ($op == 2) {
				if ($type == 2) {
					echo "<td align=\"center\" width=\"75\"><a class=\"linku\" href=\"javascript:winPopupCenter(780, 870, 'http://www.cyberphoto.se/abonnemang/" . $name . "_data_abonnemang.php');\">Se �versikt</a></td>\n";
				} else {
					echo "<td align=\"center\" width=\"75\"><a class=\"linku\" href=\"javascript:winPopupCenter(780, 870, 'http://www.cyberphoto.se/abonnemang/" . $name . "_abonnemang.php');\">Se �versikt</a></td>\n";
				}
			} elseif ($op == 3) {
				echo "<td align=\"center\" width=\"75\"><a class=\"linku\" href=\"javascript:winPopupCenter(530, 610, 'http://www.cyberphoto.se/abonnemang/" . $name . "_abonnemang.php');\">Se �versikt</a></td>\n";
			}
			*/
			echo "</tr>\n";
			echo "</table>\n";
	}	

	function getAbbPlanLenght($abb) {
		global $bt;
		
		$select = "SELECT artnr, plan_length ";
		$select .= "FROM mobile_plans ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() ";
		$select .= "WHERE validFrom < now() AND validTo > now() ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND isactive = -1 AND name = '" . $abb . "' ";
		$select .= "ORDER BY plan_length ASC ";
		
		/*
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			echo $abb;
		}
		*/
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">";
			echo "<tr>";
			// echo "<td width=\"10\"><img border=\"0\" src=\"pic/bord_4.jpg\"></td>\n";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				// echo "<td width=\"100\"><a onMouseOver=\"return escape('<b>Bindningstid</b><br>Detta �r den tid (i m�nader) som du binder upp dig till operat�ren. Under den tiden kan du inte v�lja ett annat abonnemang. Med l�ng bindningstid blir telefonen billigare.')\"><img border=\"0\" src=\"pic/choose_bind.png\"></a></td>\n";
				echo "<td width=\"100\" class=\"mob_head_dep\">Bindningstid:</td>\n";
			} else {
				// echo "<td width=\"100\"><a onMouseOver=\"return escape('<b>Bindningstid</b><br>Detta �r den tid (i m�nader) som du binder upp dig till operat�ren. Under den tiden kan du inte v�lja ett annat abonnemang. Med l�ng bindningstid blir telefonen billigare.')\"><img border=\"0\" src=\"pic/choose_bind.png\"></a></td>\n";
				echo "<td width=\"100\" class=\"mob_head_dep\">Bindningstid:</td>\n";
				// echo "<td width=\"90\"><img border=\"0\" src=\"pic/choose_bind.gif\"></td>\n";
			}
			echo "<td>\n";
			echo "<select class=\"abb_input_select\" name=\"bt\" onchange=\"this.form.submit();\">\n";
		
			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);

				echo "<option value=\"$artnr\"";
				
				if ($artnr == $bt) {
					echo " selected";
				}
				
				// echo ">" . $tillverkare . " " . $beskrivning . "</option> \n";
				echo ">" . $plan_length . " m�nader</option> \n";

				/*
				echo "<td width=\"18\"><input type=\"radio\" value=\"$artnr\" name=\"bt\" ";
				if ($artnr == $bt) {
					echo " checked ";
				}
				echo "onClick=\"submit()\"></td>";
				echo "<td width=\"48\">";
				if ($artnr == $bt) {
					echo "<b>";
				}
				echo "$plan_length m�n</b></td>";
				*/
			
			}
			
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
		
		}

	}	
	
	function getDataSize($op) {
		global $ds;
		
		$select = "SELECT artnr, name, month_fee ";
		$select .= "FROM cyberphoto.mobile_plans ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() ";
		$select .= "WHERE validFrom < now() AND validTo > now() ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND isactive = -1 AND operator_id = '" . $op . "' AND abb_type = 3 ";
		$select .= "ORDER BY artnr ASC ";
		
		/*
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			echo $abb;
		}
		*/
		$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">";
			echo "<tr>";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				echo "<td width=\"100\" class=\"mob_head_dep\">V�lj datam�ngd:</td>\n";
			} else {
				echo "<td width=\"100\" class=\"mob_head_dep\">V�lj datam�ngd:</td>\n";
			}
			echo "<td>\n";
			echo "<select class=\"abb_input_select\" name=\"ds\" onchange=\"this.form.submit();\">\n";
		
			while ($row = mysqli_fetch_object($res)) {
			
				echo "<option value=\"$row->artnr\"";
				
				if ($ds == $row->artnr) {
					echo " selected";
				}
				
				echo ">" . $row->name . " - " . $row->month_fee . " SEK/m�n</option> \n";

			}
			
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
		
		}

	}	

	function getPaymentPlanID($db) {
	
		if ($db != "") {
		
			if ($db == 6) {
				return 333318;
			} elseif ($db == 12) {
				return 333319;
			} else {
				return 333320;
			}
		
		}
		

	}	
	
	function getPlanLength($artnr) {
		
		
		$select = "SELECT plan_length ";
		$select .= "FROM mobile_plans ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			if($row->plan_length > 0) {
			
				return true;
			
			} else {
			
				return false;
			
			}
		
		} else {
		
			return false;
		
		}
	}	

	function returnPlanLength($artnr) {
		
		$select = "SELECT plan_length ";
		$select .= "FROM mobile_plans ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
				return $row->plan_length;
			
		} else {
		
				return $row->plan_length;
		
		}
	}	

	function getMobilePriceWithTax($artnr) {
		
		
		$select = "SELECT utpris, momssats FROM Artiklar ";
		$select .= "JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			$utpris_moms = ($row->utpris + ($row->utpris * $row->momssats));
			
			// echo number_format($utpris_moms, 0, ',', ' ') . " kr";
			return $utpris_moms;
		
		} else {
		
			return "";
		
		}
	}	

	function getPlanDescription($artnr) {
		
		
		$select = "SELECT description, description_new ";
		$select .= "FROM mobile_plans ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND artnr = '" . $artnr . "' ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND artnr = '" . $artnr . "' ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND artnr = '" . $artnr . "' ";
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			/*
			if ($row->description_new != "") {
				return $row->description_new;
			} else {
				return $row->description;
			}
			*/
			return $row->description_new;
		
		} else {
		
			return "";
		
		}
	}	

	static function getPlanDescriptionPublicRight($artnr) {
		
		$select = "SELECT mobile_plans_id, description_new ";
		$select .= "FROM cyberphoto.mobile_plans ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND artnr = '" . $artnr . "' ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND artnr = '" . $artnr . "' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		// $res = mysqli_query($this->conn_my, $select);
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			echo $row->description_new;
			if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_COOKIE['login_mail'] == 'peder@cyberphoto.nu' || $_COOKIE['login_mail'] == 'mathias@cyberphoto.nu') {
				echo "<div><a href=\"javascript:winPopupCenter(650, 1300, '/order/admin/edit_abonnemang.php?edit=1&artid=$row->mobile_plans_id');\">�ndra</a></div>\n";
			}
		
		} else {
		
			echo "";
		
		}
	}	
	
	function getAmtDiscount($bt) {
		global $artnr;
		
		$select = "SELECT discountCustomerAmt ";
		$select .= "FROM mobile_plans ";
		$select .= "WHERE artnr = '" . $bt . "' ";
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				if ($artnr == "i9000" && $bt == "128011") {
					return 475;
				} else {
					return $row->discountCustomerAmt;
				}
			} else {
				return $row->discountCustomerAmt;
			}
		
		} else {
		
			return "";
		
		}
	}	

	function getMobilePriceWithoutTax($artnr) {
		
		
		$select = "SELECT utpris FROM Artiklar ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->utpris;
		
		} else {
		
			return "";
		
		}
	}	

	function getMonthPlan($bt) {
		
		
		$select = "SELECT plan_length ";
		$select .= "FROM mobile_plans ";
		$select .= "WHERE artnr = '" . $bt . "' ";
		
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->plan_length;
		
		} else {
		
			return "";
		
		}
	}	

	function displayNewPrice($bt,$artnr,$fma) {
		global $ftg;

		$mobileprice = $this->getMobilePriceWithoutTax($artnr);
		$giveawayamt = $this->getAmtDiscount($bt);
		$newcustomerprice = round(($mobileprice - $giveawayamt) * 1.25);
		
			if ($fma != 0) {
			
				$periodiccostdraw = round(($this->getMonthPlan($bt) * $fma) * 1.25);
				if ($ftg == 1) {
					$newcustomerprice = ($newcustomerprice - $periodiccostdraw) * 0.8;
				} else {
					$newcustomerprice = $newcustomerprice - $periodiccostdraw;
				}
				
					if ($newcustomerprice < 1) {
					
						return 1;
					
					} else {
					
						return number_format($newcustomerprice, 0, ',', ' ');

					}
			
			} else {
		
				if ($ftg == 1) {
					$newcustomerprice = $newcustomerprice * 0.8;
				}
				if ($newcustomerprice < 1) {

					return 1;
				
				} else {
				
					return number_format($newcustomerprice, 0, ',', ' ');
				
				}
			
			}

	}

	function displayNewPriceTOT($bt,$artnr,$fma) {

		$mobileprice = $this->getMobilePriceWithoutTax($artnr);
		$giveawayamt = $this->getAmtDiscount($bt);
		$newcustomerprice = round(($mobileprice - $giveawayamt) * 1.25);
		
			if ($fma != 0) {
			
				$periodiccostdraw = round(($this->getMonthPlan($bt) * $fma) * 1.25);
				$newcustomerprice = $newcustomerprice - $periodiccostdraw;
				
					if ($newcustomerprice < 1) {
					
						return 1;
					
					} else {
					
						return $newcustomerprice;

					}
			
			} else {
		
				if ($newcustomerprice < 1) {

					return 1;
				
				} else {
				
					return $newcustomerprice;
				
				}
			
			}

	}

	function displayNewPriceNotFormated($bt,$artnr,$fma) {

		$mobileprice = $this->getMobilePriceWithoutTax($artnr);
		$giveawayamt = $this->getAmtDiscount($bt);
		$newcustomerprice = round(($mobileprice - $giveawayamt) * 1.25);
		
			if ($fma != 0) {
			
				$periodiccostdraw = round(($this->getMonthPlan($bt) * $fma) * 1.25);
				$newcustomerprice = $newcustomerprice - $periodiccostdraw;
				
					if ($newcustomerprice < 1) {
					
						return 1;
					
					} else {
					
						return $newcustomerprice;

					}
			
			} else {
		
				if ($newcustomerprice < 1) {

					return 1;
				
				} else {
				
					return $newcustomerprice;
				
				}
			
			}

	}

	function getOperatorAddons($op) {
		
		$select = "SELECT mobile_plans.name AS AbbNamn, description, artnr ";
		$select .= "FROM mobile_plans ";
		// $select .= "LEFT JOIN operator ON mobile_plans.operator_id = operator.operator_id ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND isactive = -1 ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND isactive = -1 ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND mobile_plans.plan_length = 100 AND mobile_plans.operator_id = '" . $op . "' ";
		$select .= "ORDER BY mobile_plans_id ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

			echo "<table border=\"0\" cellpadding=\"7\" cellspacing=\"0\">\n";
			echo "<tr>\n";
			// echo "<td width=\"10\"><img border=\"0\" src=\"pic/bord_3.jpg\"></td>\n";
			echo "<td width=\"400\"><img border=\"0\" src=\"pic/choose_tj.gif\"></td>\n";
			echo "<td>&nbsp;</td>\n";
			echo "</tr>\n";

		if (mysqli_num_rows($res) > 0) {
			
			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			$ttjanster = "";

			ob_start();
			include ("/web/www/abonnemang/$description");
			$ttjanster = ob_get_contents();
			ob_end_clean();
			
			// echo $ttjanster;
			
			// $ttjanster = include ("abonnemang/$description");

			echo "<tr>\n";
			// echo "<td width=\"290\" class=\"addon\"><a onMouseOver=\"return escape(' include (\"abonnemang/description.php\"); ')\">$AbbNamn</a></td>";
			echo "<td width=\"400\" class=\"addon\"><a onMouseOver=\"return escape('$ttjanster')\">$AbbNamn</a></td>";
			echo "<td><a href=\"javascript:modifyItems('$artnr')\"><img border=\"0\" src=\"pic/01.gif\"></a></td>";
			echo "</tr>\n";
			
			
			}

		} else {
		
			echo "<tr>\n";
			echo "<td colspan=\"2\">Det finns inga till�ggstj�nster</td>";
			echo "</tr>\n";

		}

			echo "</table>\n";
	}	

	function getOperatorAddonsNew($op,$type) {
		
		$select = "SELECT mobile_plans.name AS AbbNamn, mobile_plans_id, artnr ";
		$select .= "FROM mobile_plans ";
		// $select .= "LEFT JOIN operator ON mobile_plans.operator_id = operator.operator_id ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND isactive = -1 ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND isactive = -1 ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND mobile_plans.plan_length = 100 AND mobile_plans.operator_id = '" . $op . "' ";
		if ($type == 2) { // om mobilt bredband
			$select .= "AND abb_type = 2 ";
		} else {
			$select .= "AND abb_type = 1 ";
		}
		$select .= "ORDER BY mobile_plans_id ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

			echo "<table border=\"0\" cellpadding=\"7\" cellspacing=\"0\">\n";
			echo "<tr>\n";
			// echo "<td width=\"10\"><img border=\"0\" src=\"pic/bord_3.jpg\"></td>\n";
			echo "<td width=\"400\"><img border=\"0\" src=\"pic/choose_tj.gif\"></td>\n";
			echo "<td>&nbsp;</td>\n";
			echo "</tr>\n";

		if (mysqli_num_rows($res) > 0) {
			
			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			$ttjanster = "";

			/*
			ob_start();
			include ("/web/www/abonnemang/$description");
			$ttjanster = ob_get_contents();
			ob_end_clean();
			*/
			
			// echo $ttjanster;
			
			// $ttjanster = include ("abonnemang/$description");

			echo "<tr>\n";
			// echo "<td width=\"400\" class=\"addon\"><a onMouseOver=\"return escape('$ttjanster')\">$AbbNamn</a></td>";
			echo "<td width=\"400\" class=\"addon\"><a href=\"javascript:winPopupCenter(400, 650, '/abonnemang/tjanster.php?show=$mobile_plans_id');\">$AbbNamn</a></td>";
			echo "<td><a href=\"javascript:modifyItems('$artnr')\"><img border=\"0\" src=\"pic/01.gif\"></a></td>";
			echo "</tr>\n";
			
			
			}

		} else {
		
			echo "<tr>\n";
			echo "<td colspan=\"2\">Det finns inga till�ggstj�nster</td>";
			echo "</tr>\n";

		}

			echo "</table>\n";
	}	

	function displayFMA($bt,$artnr,$fma) {

		$monthplan = $this->getMonthPlan($bt);
		$mobileprice = $this->getMobilePriceWithoutTax($artnr);
		$giveawayamt = $this->getAmtDiscount($bt);
		$newcustomerprice = round(($mobileprice - $giveawayamt) * 1.25);
		
		if ((($monthplan * $fma) * 1.25) < $newcustomerprice) {
		
			return true;
		
		} else {
		
			return false;
			
		}
		

	}

	function getDescriptionAbonnemang($artnr) {
		
		
		$select = "SELECT tillverkare, beskrivning, Artiklar.tillverkar_id ";
		$select .= "FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		// $select .= "AND (kategori_id = 336 OR kategori_id = 462) ";
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			if ($row->tillverkar_id == 80) {
				return $row->beskrivning;
			} else {
				return $row->tillverkare . " " . $row->beskrivning;
			}
		
		} else {
		
			return "&nbsp;";
		
		}

	}	

	function getMaxGiveAwayAmt() {
		
		
		$select = "SELECT MAX(discountCustomerAmt) AS maxpris ";
		$select .= "FROM mobile_plans ";
		// $select .= "WHERE validfrom < GETDATE() and validto > GETDATE() ";
		$select .= "WHERE validfrom < now() and validto > now() ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND abb_type = 1 ";
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->maxpris;
		
		} else {
		
			return 0;
		
		}

	}	

	function getMaxGiveAwayAmtData() {
		
		
		$select = "SELECT MAX(discountCustomerAmt) AS maxpris ";
		$select .= "FROM mobile_plans ";
		// $select .= "WHERE validfrom < GETDATE() and validto > GETDATE() ";
		$select .= "WHERE validfrom < now() and validto > now() ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND abb_type = 2 ";
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->maxpris;
		
		} else {
		
			return 0;
		
		}

	}	

	function getMaxGiveAwayAmtOperator($op) {
		
		
		$select = "SELECT MAX(discountCustomerAmt) AS maxpris ";
		$select .= "FROM mobile_plans ";
		// $select .= "WHERE validfrom < GETDATE() and validto > GETDATE() ";
		$select .= "WHERE validfrom < now() and validto > now() ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND operator_id = '" . $op . "' ";
		$select .= "AND abb_type = 1 ";
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->maxpris;
		
		} else {
		
			return 0;
		
		}

	}	

	function getMaxGiveAwayAmtOperatorData($op) {
		
		
		$select = "SELECT MAX(discountCustomerAmt) AS maxpris ";
		$select .= "FROM mobile_plans ";
		// $select .= "WHERE validfrom < GETDATE() and validto > GETDATE() ";
		$select .= "WHERE validfrom < now() and validto > now() ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND operator_id = '" . $op . "' ";
		$select .= "AND abb_type = 2 ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->maxpris;
		
		} else {
		
			return 0;
		
		}

	}	

	function displayLowestPrice($artnr,$type) {

		$mobileprice = $this->getMobilePriceWithoutTax($artnr);
		if ($type == 2) {
			$maxgiveawayamt = $this->getMaxGiveAwayAmtData();
		} else {
			$maxgiveawayamt = $this->getMaxGiveAwayAmt();
		}
		// echo $maxgiveawayamt;
		// $customerprice = round(($mobileprice - $maxgiveawayamt - 3840) * 1.25);
		if ($type == 2) {
			$customerprice = round(($mobileprice - $maxgiveawayamt - 5760) * 1.25);
		} else {
			$customerprice = round(($mobileprice - $maxgiveawayamt - 4800) * 1.25);
		}
		
				
			if ($customerprice < 1) {
				
				return 1;
					
			} else {
					
				return number_format($customerprice, 0, ',', ' ');

			}
			

	}

	function displayLowestPriceOperator($artnr,$op) {

		$mobileprice = $this->getMobilePriceWithoutTax($artnr);
		$maxgiveawayamt = $this->getMaxGiveAwayAmtOperator($op);
		// echo $maxgiveawayamt;
		$customerprice = round(($mobileprice - $maxgiveawayamt - 3840) * 1.25);
		
				
			if ($customerprice < 1) {
				
				return 1;
					
			} else {
					
				return number_format($customerprice, 0, ',', ' ');

			}
			

	}

	function displayLowestPriceOperatorNew($artnr,$op) {

		$mobileprice = $this->getMobilePriceWithoutTax($artnr);
		$maxgiveawayamt = $this->getMaxGiveAwayAmtOperator($op);
		// echo $maxgiveawayamt;
		if ($op == 2) {
			$customerprice = round(($mobileprice - $maxgiveawayamt - 4800) * 1.25);
		} else {
			$customerprice = round(($mobileprice - $maxgiveawayamt - 3840) * 1.25);
		}
		
				
			if ($customerprice < 1) {
				
				return 1;
					
			} else {
					
				return number_format($customerprice, 0, ',', ' ');

			}
			

	}

	function displayLowestPriceOperatorData($artnr,$op) {

		$mobileprice = $this->getMobilePriceWithoutTax($artnr);
		$maxgiveawayamt = $this->getMaxGiveAwayAmtOperatorData($op);
		// echo $maxgiveawayamt;
		if ($op == 2) {
			$customerprice = round(($mobileprice - $maxgiveawayamt - 4800) * 1.25);
			// $customerprice = round(($mobileprice - $maxgiveawayamt - 5760) * 1.25); // detta kom till n�r vi la dit 300 kr alternativet, 121012, tagit bort denna
		} else {
			$customerprice = round(($mobileprice - $maxgiveawayamt - 3840) * 1.25);
		}
		
				
			if ($customerprice < 1) {
				
				return 1;
					
			} else {
					
				return number_format($customerprice, 0, ',', ' ');

			}
			

	}
	
	function displayMonthPriceTele2($month,$articlemob,$db,$bt,$ds) {
	
		$monthprice = 0;
		$price_phone = $this->getMobilePriceWithTax($articlemob);
		$month_fee = $this->getMonthFee($bt);
		$month_fee_data = $this->getMonthFee($ds);
		// echo "h�r: " . $month_fee . " stopp";
		
		if ($db > 0 && $month == 1) {
			$monthprice = $price_phone / $db;
			$monthprice = $monthprice + 250;
			$monthprice = $monthprice + $month_fee;
			$monthprice = $monthprice + $month_fee_data;
		} elseif ($db > 0 && $month == 2) {
			$monthprice = $price_phone / $db;
			$monthprice = $monthprice + $month_fee;
			$monthprice = $monthprice + $month_fee_data;
		} else {
			$monthprice = $monthprice + $month_fee;
			$monthprice = $monthprice + $month_fee_data;
		}
		
		return number_format($monthprice, 0, ',', ' ');
	
	}

	function getPlanID($bt) {
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $bt;
		}
		$select = "SELECT mobile_plans_id ";
		$select .= "FROM mobile_plans ";
		$select .= "WHERE artnr = '" . $bt . "' ";
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->mobile_plans_id;
		
		} else {
		
			return "";
		
		}
	}	

	function displayAbBask($bt) {
		
		
		$select = "SELECT mobile_operator.name AS OPname, mobile_plans.name AS ABname, mobile_plans.plan_length ";
		$select .= "FROM mobile_plans ";
		$select .= "JOIN mobile_operator ON mobile_plans.operator_id = mobile_operator.operator_id ";
		$select .= "WHERE mobile_plans_id = '" . $bt . "' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			echo $row->OPname . " ". $row->ABname . " " . $row->plan_length . " m�n";
		
		} else {
		
			echo "";
		
		}
	}	

	function getMaxPlanLength($abb) {
		
		// echo $abb;
		
		$select = "SELECT MAX(plan_length) AS MaxLangd ";
		$select .= "FROM mobile_plans ";
		$select .= "WHERE name = '" . $abb . "' ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND isactive = -1 ";
		
		// echo $select;
		
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->MaxLangd;
		
		} else {
		
			return "";
		
		}
	}	

	function getMaxAbbShow($abb) {
		
		// echo $abb;
		$maxperiod = $this->getMaxPlanLength($abb);
		
		$select = "SELECT artnr ";
		$select .= "FROM mobile_plans ";
		$select .= "WHERE name = '" . $abb . "' AND plan_length = $maxperiod ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND isactive = -1 ";
		
		// echo $select;
		
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->artnr;
		
		} else {
		
			return "";
		
		}
	}	

	function displayMonthFee($fma,$bt) {
		global $op, $ftg;

		$fmainclvat = $fma * 1.25;
		$abb_month_fee = $this->getMonthFee($bt);
		if ($ftg == 1) {
			$totalmonthfee = ($fmainclvat + $abb_month_fee) * 0.8;
		} else {
			$totalmonthfee = $fmainclvat + $abb_month_fee;
		}
		
		if ($sjabo == "full" && $op == 5 && $fma > 0 && ($bt == 300001 || $bt == 300002 || $bt == 300003 || $bt == 300004 || $bt == 300020)) {
			
			if ($bt == 300001 && $fma > 0) {
				$totalmonthfeeNew = $totalmonthfee - 50;
			} elseif ($bt == 300002 && $fma > 0) {
				$totalmonthfeeNew = $totalmonthfee - 50;
			} elseif ($bt == 300003 && $fma == 40) {
				$totalmonthfeeNew = $totalmonthfee - 50;
			} elseif ($bt == 300003 && $fma > 40) {
				$totalmonthfeeNew = $totalmonthfee - 100;
			} elseif ($bt == 300004 && $fma == 40) {
				$totalmonthfeeNew = $totalmonthfee - 50;
			} elseif ($bt == 300004 && $fma == 80) {
				$totalmonthfeeNew = $totalmonthfee - 100;
			} elseif ($bt == 300004 && $fma == 120) {
				$totalmonthfeeNew = $totalmonthfee - 150;
			} elseif ($bt == 300004 && $fma == 160) {
				$totalmonthfeeNew = $totalmonthfee - 200;
			} elseif ($bt == 300004 && $fma == 200) {
				$totalmonthfeeNew = $totalmonthfee - 250;
			} elseif ($bt == 300020 && $fma == 40) {
				$totalmonthfeeNew = $totalmonthfee - 50;
			} elseif ($bt == 300020 && $fma == 80) {
				$totalmonthfeeNew = $totalmonthfee - 100;
			} elseif ($bt == 300020 && $fma > 80) {
				$totalmonthfeeNew = $totalmonthfee - 150;
			}

			return "<strike>" . $totalmonthfee . " kr</strike> <font color=\"#009933\">Nu endast " . $totalmonthfeeNew . " kr</font>";
		
		} else {
		
			return round($totalmonthfee,0) . " kr";
		
		}

	}

	function returnMonthFee($fma,$bt) {
		global $op;

		$fmainclvat = $fma * 1.25;
		$abb_month_fee = $this->getMonthFee($bt);
		$totalmonthfee = $fmainclvat + $abb_month_fee;
		
		if ($op == 5 && $fma > 0 && ($bt == 300002 || $bt == 300003 || $bt == 300004)) {
			
			if ($bt == 300002 && $fma > 0) {
				$totalmonthfeeNew = $totalmonthfee - 50;
			} elseif ($bt == 300003 && $fma == 40) {
				$totalmonthfeeNew = $totalmonthfee - 50;
			} elseif ($bt == 300003 && $fma > 40) {
				$totalmonthfeeNew = $totalmonthfee - 100;
			} elseif ($bt == 300004 && $fma == 40) {
				$totalmonthfeeNew = $totalmonthfee - 50;
			} elseif ($bt == 300004 && $fma == 80) {
				$totalmonthfeeNew = $totalmonthfee - 100;
			} elseif ($bt == 300004 && $fma == 120) {
				$totalmonthfeeNew = $totalmonthfee - 150;
			} elseif ($bt == 300004 && $fma == 160) {
				$totalmonthfeeNew = $totalmonthfee - 200;
			} elseif ($bt == 300004 && $fma == 200) {
				$totalmonthfeeNew = $totalmonthfee - 250;
			}

			return $totalmonthfeeNew;
		
		} else {
		
			return $totalmonthfee;
		
		}

	}

	function getMonthFee($bt) {

		$select = "SELECT month_fee ";
		$select .= "FROM mobile_plans ";
		$select .= "WHERE isactive = -1 AND validFrom < now() AND validTo > now() ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND artnr = '" . $bt . "' ";
		
		// echo $select;
		
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->month_fee;
		
		} else {
		
			return "";
		
		}

	}

	function displayTotalFee($monthfee,$month,$cash) {
		global $ftg;

			// echo "<br>" . $monthfee . "<br>";
			// echo $month . "<br>";
			// echo $cash . "<br>";

			if ($ftg == 1) {
				$totalFee = ($monthfee * $month + $cash) * 0.8;
			} else {
				$totalFee = $monthfee * $month + $cash;
			}
		
			return number_format($totalFee, 0, ',', ' ') . " kr";
			// return $totalFee . " kr";

	}

	function insertAbbType($ordernr,$sim,$phonenr,$abb,$abbtype) {

		mysqli_query($this->conn_my2, "INSERT INTO mobilephones (ordernr,sim,phonenr,mobile_plans_id,abb_type_id) VALUES ('$ordernr','$sim','$phonenr','$abb','$abbtype') ");

	}

	function sendAbbMess() {

		$addcreatedby = "webmaster";

		// $recipient .= " peder";
		$recipient .= " borje";
		$recipient .= " sjabo";
		
		$subj = "Nytt abonnemang upplagt!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendAbbMess_v2($op,$desc) {

		$orderdatum = date("j/n-Y H:i", time());
		
		$addcreatedby = "webmaster";

		// $recipient .= " peder";
		$recipient .= " borje";
		$recipient .= " sjabo";
		
		if ($op == "1") {
			$subj = $orderdatum . " Nytt Tele2 abonnemang upplagt!";
		} elseif ($op == "2") {
			$subj = $orderdatum . " Nytt Telia abonnemang upplagt!";
		} elseif ($op == "3") {
			$subj = $orderdatum . " Nytt Halebop abonnemang upplagt!";
		} else {
			$subj = $orderdatum . " Nytt abonnemang upplagt!";
		}

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= $desc;
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendAbbMess_v3($op,$desc,$freight) {

		$orderdatum = date("j/n-Y H:i", time());
		
		$addcreatedby = "webmaster";

		// $recipient .= " peder";
		$recipient .= " borje";
		$recipient .= " sjabo";
		$recipient .= " vinterflod@gmail.com";
		
		if ($op == "1") {
			$subj = $orderdatum . " Nytt Tele2 abonnemang upplagt!";
		} elseif ($op == "2") {
			$subj = $orderdatum . " Nytt Telia abonnemang upplagt!";
		} elseif ($op == "3") {
			$subj = $orderdatum . " Nytt Halebop abonnemang upplagt!";
		} else {
			$subj = $orderdatum . " Nytt abonnemang upplagt!";
		}

		if ($freight == "fraktbutik") {
			$subj .= " *** LAGERSHOP ***";
		}
		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= $desc;
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendAbbMess_v4($op,$desc,$freight,$old_lnamn) {

		$orderdatum = date("j/n-Y H:i", time());
		
		$addcreatedby = "webmaster";

		// $recipient .= " peder";
		$recipient .= " borje";
		$recipient .= " sjabo";
		// $recipient .= " mathias";
		// $recipient .= " vinterflod@gmail.com";
		
		if ($op == "1") {
			$subj = $orderdatum . " Nytt Tele2 abonnemang upplagt!";
		} elseif ($op == "2") {
			$subj = $orderdatum . " Nytt Telia abonnemang upplagt!";
		} elseif ($op == "3") {
			$subj = $orderdatum . " Nytt Halebop abonnemang upplagt!";
		} else {
			$subj = $orderdatum . " Nytt abonnemang upplagt!";
		}

		if ($freight == "fraktbutik") {
			$subj .= " *** LAGERSHOP ***";
		}
		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "K�pare: " . $old_lnamn . "\n\n";
		$text1 .= $desc;
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendAbbMess_v5($op,$desc,$freight,$old_lnamn,$newordernr,$old_simnumber,$old_abbnumber,$old_abbtype,$old_operator,$old_personnr,$old_lpostadr) {

		$orderdatum = date("j/n-Y H:i", time());
		
		$addcreatedby = "webmaster";

		// $recipient .= " peder";
		$recipient .= " borje";
		$recipient .= " sjabo";
		// $recipient .= " mathias";
		// $recipient .= " jonas";
		
		if ($old_abbtype == "1") {
			$abbtype = "Nyteckning";
		} elseif ($old_abbtype == "2") {
			$abbtype = "Beh�lla nummer - flytt fr�n annan operat�r";
		} elseif ($old_abbtype == "3") {
			$abbtype = "Beh�lla nummer hos operat�r";
		} else {
			$abbtype = "Beh�lla nummer - har kontantkort";
		}
		if ($op == "1") {
			$subj = $orderdatum . " Nytt Tele2 abonnemang upplagt!";
		} elseif ($op == "2") {
			$subj = $orderdatum . " Nytt Telia abonnemang upplagt!";
		} elseif ($op == "3") {
			$subj = $orderdatum . " Nytt Halebop abonnemang upplagt!";
		} elseif ($op == "5") {
			$subj = $orderdatum . " Nytt Telenor abonnemang upplagt!";
		} else {
			$subj = $orderdatum . " Nytt abonnemang upplagt!";
		}

		if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR']) || $_SERVER['REMOTE_ADDR'] == "81.8.240.95") {
			$subj .= " *** SKAPAD I HUSET ***";
		}
		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $newordernr . "\n\n";
		$text1 .= "K�pare: " . $old_lnamn . ", " . $old_personnr . "\n\n";
		$text1 .= "Produkt: " . $desc . "\n\n";
		$text1 .= "Abonnemangsform: " . $abbtype . "\n\n";
		// if ($old_abbtype == "2" || $old_abbtype == "3") {
		if ($old_abbtype != "1") {
			$text1 .= "Nuvarande abonnemang: " . $old_abbnumber . "\n\n";
		}
		if ($old_abbtype == "2") {
			$text1 .= "Nuvarande operat�r: " . $old_operator . "\n\n";
		}
		if ($old_abbtype == "4") {
			$text1 .= "Simkortsnummer: " . $old_simnumber . "\n\n";
		}
		if ($freight == "fraktbutik") {
			$text1 .= "Produkten skall h�mtas i Lagershopen\n\n";
		} else {
			$text1 .= "Produkten skall skickas till: " . $old_lpostadr . "\n\n";
		}
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendAbbMess_v6($op,$desc) {
		global $freight, $old_lnamn, $newordernr, $old_simnumber, $old_abbnumber, $old_abbtype, $old_operator, $old_personnr, $old_lco, $old_lpostnr, $old_lpostadr, $old_lemail, $old_mobilnr, $old_abbpersonnumber;

		$orderdatum = date("j/n-Y H:i", time());
		
		$addcreatedby = "webmaster";

		// $recipient .= " peder@cyberphoto.nu";
		$recipient .= " borje@cyberphoto.nu";
		// $recipient .= " mathias@cyberphoto.nu";
		// $recipient .= " maria@cyberphoto.nu";
		$recipient .= " sjabo@cyberphoto.nu";
		$recipient .= " jonas@cyberphoto.nu";
		$recipient .= " albin.larsson@cyberphoto.nu";
		$recipient .= " johan.eriksson@cyberphoto.nu";
		
		if ($old_abbtype == "1") {
			$abbtype = "Nyteckning";
		} elseif ($old_abbtype == "2") {
			$abbtype = "Beh�lla nummer - flytt fr�n annan operat�r";
		} elseif ($old_abbtype == "3") {
			$abbtype = "Beh�lla nummer hos operat�r";
		} else {
			$abbtype = "Beh�lla nummer - har kontantkort";
		}
		if ($op == "1") {
			$subj = $orderdatum . " Nytt Tele2 abonnemang upplagt!";
		} elseif ($op == "2") {
			$subj = $orderdatum . " Nytt Telia abonnemang upplagt!";
		} elseif ($op == "3") {
			$subj = $orderdatum . " Nytt Halebop abonnemang upplagt!";
		} elseif ($op == "5") {
			$subj = $orderdatum . " Nytt Telenor abonnemang upplagt!";
		} else {
			$subj = $orderdatum . " Nytt abonnemang upplagt!";
		}

		if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR']) || $_SERVER['REMOTE_ADDR'] == "81.8.240.95") {
			$subj .= " *** SKAPAD I HUSET ***";
		}
		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $newordernr . "\n\n";
		$text1 .= "K�pare: " . $old_lnamn . ", " . $old_personnr . "\n\n";
		/*
		if (($op == "2" || $op == "3") && $old_abbpersonnumber != "") {
			$text1 .= "�gare till abonnemanget: " . $old_abbpersonnumber . "\n\n";
		}
		*/
		if ($old_abbpersonnumber != "") {
			$text1 .= "�gare till abonnemanget: " . $old_abbpersonnumber . "\n\n";
		}
		$text1 .= "Produkt: " . $desc . "\n\n";
		$text1 .= "Abonnemangsform: " . $abbtype . "\n\n";
		// if ($old_abbtype == "2" || $old_abbtype == "3") {
		if ($old_abbtype != "1") {
			$text1 .= "Nuvarande abonnemang: " . $old_abbnumber . "\n\n";
		}
		if ($old_abbtype == "2") {
			$text1 .= "Nuvarande operat�r: " . $old_operator . "\n\n";
		}
		if ($old_abbtype == "4") {
			$text1 .= "Simkortsnummer: " . $old_simnumber . "\n\n";
		}
		if ($freight == "fraktbutik") {
			$text1 .= "Produkten skall h�mtas i Lagershopen\n\n";
		} else {
			$text1 .= "Produkten skall skickas till: " . $old_lpostadr . "\n\n";
		}
		$text1 .= "Adress: " . $old_lco . ", " . $old_lpostnr . " " . $old_lpostadr . "\n\n";
		$text1 .= "E-post: " . $old_lemail . "\n\n";
		$text1 .= "SMS-avis: " . $old_mobilnr . "\n\n";
		if ($old_mobilnr != "") {
			$text1 .= "http://www.hitta.se/ExternalSearch/Default.aspx?SearchType=4&TextBoxWho=" . $old_mobilnr . "\n\n";
			$text1 .= "http://personer.eniro.se/resultat/" . $old_mobilnr . "\n\n";
		}
		$text1 .= "http://www.birthday.se/sok/?a=" . urlencode($old_lco) . "&z=" . urlencode($old_lpostnr) . "&c=" . urlencode($old_lpostadr) . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendForsakringMess_v1($ordernr) {

		$orderdatum = date("j/n-Y H:i", time());
		
		$addcreatedby = "webmaster";

		// $recipient .= " peder";
		// $recipient .= " borje";
		// $recipient .= " sjabo";
		// $recipient .= " mathias";
		// $recipient .= " vinterflod@gmail.com";
		$recipient .= " jonas@cyberphoto.nu";
		$recipient .= " albin.larsson@cyberphoto.nu";
		$recipient .= " johan.eriksson@cyberphoto.nu";
		
		$subj = $orderdatum . " Order med f�rs�kring upplagt!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendSparrMess($ordernr,$old_namn) {
	
		$orderdatum = date("Y-m-d H:i", time());
	
		$addcreatedby = "noreply";
	
		// $recipient .= " sjabo";
		$recipient .= " kundtjanst";
	
		$subj = $orderdatum . " Order som M�ste kontrolleras �r upplagd!";
	
		$extra = "From: " . $addcreatedby;
	
		if (preg_match("/levenpahoutte/i", $old_namn)) {
			$text1 .= "Denna person best�ller men h�mtar aldrig ut varorna. Torbj�rn och Malin vet mera!\n\n";
		}
		$text1 .= "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
	
		mail($recipient, $subj, $text1, $extra);
	
	}
	
	static function sendAbbContactMeMess($number,$article) {
		global $mobil, $fi;
	
		$orderdatum = date("Y-m-d H:i", time());
	
		$addcreatedby = "noreply";
	
		// $recipient .= " sjabo";
		if ($fi) {
			$recipient .= " info@cyberphoto.fi";
		} elseif ($mobil) {
			$recipient .= " ringupp_mobil";
		} else {
			$recipient .= " ringupp";
		}
	
		$subj = $orderdatum . " Ring upp kund!";
	
		$extra = "From: " . $addcreatedby;
	
		$text1 .= "Kund har fr�gor om en produkt och �nskar bli uppringd.\n\n";
		$text1 .= "Telefon: " . $number . "\n\n";
		$text1 .= "Produkten d�r kunden skickade fr�gan ifr�n: http://www.cyberphoto.se/info.php?article=" . $article . "\n\n";
		if (!$fi) {
			$text1 .= "S�k numret p� HITTA:\n";
			$text1 .= "http://www.hitta.se/ExternalSearch/Default.aspx?SearchType=4&TextBoxWho=" . $number . "\n\n";
			$text1 .= "S�k numret p� ENIRO:\n";
			$text1 .= "http://personer.eniro.se/resultat/" . $number . "\n\n";
		}
		$text1 .= "Registrerat fr�n IP-adress: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
	
		mail($recipient, $subj, $text1, $extra);
	
	}
	
	function sendSnabbKassaMess_v1($ordernr) {

		$orderdatum = date("j/n-Y H:i:s", time());
		
		$addcreatedby = "noreply";

		$recipient .= " sjabo";
		// $recipient .= " tobias";
		
		$subj = $orderdatum . " Order fr�n snabbkassa upplagt!";

		$extra = "From: " . $addcreatedby;
		
		// $text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendMessMobileOrder_v1($ordernr,$goodsvalue) {

		$orderdatum = date("j/n-Y H:i:s", time());
		
		$addcreatedby = "noreply";

		$recipient .= " sjabo";
		// $recipient .= " tobias";
		
		$subj = $orderdatum . " Order fr�n mobilsajt upplagd!";

		$extra = "From: " . $addcreatedby;
		
		$text1 .= "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		$text1 .= "Netto: " . $goodsvalue . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendMessNorwayOrder($ordernr,$goodsvalue) {
		global $kundvagn;

		$orderdatum = date("Y-m-d H:i", time());
		
		$addcreatedby = "noreply";

		$recipient .= " sjabo";
		
		$subj = $orderdatum . " Order fr�n Norge upplagd!";

		$extra = "From: " . $addcreatedby;
		
		$text1 .= "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		$text1 .= "Netto: " . $goodsvalue . "\n\n";
		$text1 .= "Kundvagnen:\n\n";
		$text1 .= $this->viewBasketShort($kundvagn);
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendMessNorwayFinlandOrder($ordernr,$goodsvalue,$country,$betalsatt_id) {
		global $kundvagn;
	
		$orderdatum = date("Y-m-d H:i:s", time());
	
		$addcreatedby = "noreply";
	
		if ($country == 47) {
			$subj = $orderdatum . " Order fr�n Norge upplagd!";
			$recipient .= " sjabo";
		} elseif ($country == 45) {
			$subj = $orderdatum . " Order fr�n Danmark upplagd!";
			$recipient .= " sjabo";
		} else {
			$subj = $orderdatum . " Order fr�n Finland upplagd!";
			$recipient .= " sjabo";
			$recipient .= " borje";
		}
	
		$extra = "From: " . $addcreatedby;
	
		$text1 .= "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		$text1 .= "Netto: " . $goodsvalue . "\n\n";
		$text1 .= "Betals�tt: " . $this->getBetalsattet($betalsatt_id) . "\n\n";
		$text1 .= "Kundvagnen:\n\n";
		$text1 .= $this->viewBasketShort($kundvagn);
	
		mail($recipient, $subj, $text1, $extra);
	
	}
	
	function getBetalsattet($betalsatt_id) {
		
		$select  = "SELECT betalsatt ";
		$select .= "FROM cyberphoto.Betalsatt ";
		$select .= "WHERE betalsatt_id = " . $betalsatt_id;
		
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
		
		return $row->betalsatt;
	
	}
	
	function sendMessTenThousandOrder_v1($ordernr,$goodsvalue) {

		$orderdatum = date("j/n-Y H:i:s", time());
		
		$addcreatedby = "noreply";

		$recipient .= " sjabo@cyberphoto.nu";
		$recipient .= " patrick@cyberphoto.nu";
		// $recipient .= " tobias";
		
		$subj = $orderdatum . " Order med v�rde �ver tio tusen upplagd!";

		$extra = "From: " . $addcreatedby;
		
		$text1 .= "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		$text1 .= "Netto: " . $goodsvalue . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendMessTenThousandOrder_v2($ordernr,$goodsvalue) {
		global $pay, $freight, $old_lnamn, $newordernr, $old_personnr, $old_lco, $old_lpostnr, $old_lpostadr, $old_lemail, $old_mobilnr;

		$orderdatum = date("j/n-Y H:i", time());
		
		$addcreatedby = "noreply";

		$recipient .= " patrick@cyberphoto.nu";
		$recipient .= " sjabo@cyberphoto.nu";
		
		$subj = $orderdatum . " Order med v�rde �ver 10.000 upplagd!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $newordernr . "\n\n";
		if ($old_personnr != "") {
			$text1 .= "Kunden: " . $old_lnamn . ", " . $old_personnr . "\n\n";
		} else {
			$text1 .= "Kunden: " . $old_lnamn . "\n\n";
		}

		$text1 .= "Betals�tt: " . $pay . "\n\n";
		if ($freight == "fraktbutik") {
			$text1 .= "Produkten skall h�mtas i Lagershopen\n\n";
		} else {
			$text1 .= "Produkten skall skickas till: " . $old_lpostadr . "\n\n";
		}
		$text1 .= "Adress: " . $old_lco . ", " . $old_lpostnr . " " . $old_lpostadr . "\n\n";
		if ($old_lemail != "") {
			$text1 .= "E-post: " . $old_lemail . "\n\n";
		}
		if ($old_mobilnr != "") {
			$text1 .= "Mobil: " . $old_mobilnr . "\n\n";
		}
		
		mail($recipient, $subj, $text1, $extra);

	}
	
	function sendMessTenThousandOrder_v3($ordernr,$goodsvalue) {
		global $pay, $freight, $old_lnamn, $newordernr, $old_personnr, $old_lco, $old_lpostnr, $old_lpostadr, $old_lemail, $old_mobilnr;

		$orderdatum = date("j/n-Y H:i", time());
		
		$addcreatedby = "noreply";

		$recipient .= " patrick@cyberphoto.nu";
		// $recipient .= " sjabo@cyberphoto.nu";
		// $recipient .= " torbjorn@cyberphoto.nu";
		// $recipient .= " eva-maria@cyberphoto.nu";
		$recipient .= " kolla_ordrar@cyberphoto.se";
		
		$subj = $orderdatum . " Order med v�rde �ver 10.000 upplagd!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $newordernr . "\n\n";
		if ($old_personnr != "") {
			$text1 .= "Kunden: " . $old_lnamn . ", " . $old_personnr . "\n\n";
		} else {
			$text1 .= "Kunden: " . $old_lnamn . "\n\n";
		}

		$text1 .= "Betals�tt: " . $pay . "\n\n";
		if ($freight == "fraktbutik") {
			$text1 .= "H�mtas lagershopen\n\n";
		} else {
			$text1 .= "Frakts�tt: " . $freight . "\n\n";
			$text1 .= "Skickas till: " . $old_lpostadr . "\n\n";
		}
		$text1 .= "Adress: " . $old_lco . ", " . $old_lpostnr . " " . $old_lpostadr . "\n\n";
		if ($old_lemail != "") {
			$text1 .= "E-post: " . $old_lemail . "\n\n";
		}
		if ($old_mobilnr != "") {
			$text1 .= "Mobil: " . $old_mobilnr . "\n\n";
			$text1 .= "http://www.hitta.se/ExternalSearch/Default.aspx?SearchType=4&TextBoxWho=" . $old_mobilnr . "\n\n";
			$text1 .= "http://personer.eniro.se/resultat/" . $old_mobilnr . "\n\n";
		}
		$text1 .= "http://www.birthday.se/sok/?a=" . urlencode($old_lco) . "&z=" . urlencode($old_lpostnr) . "&c=" . urlencode($old_lpostadr) . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}
	
	function sendSiemensMess_v1($ordernr) {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		// $recipient .= " rolf";
		$recipient .= " produkt";
		// $recipient .= " sjabo";
		// $recipient .= " tobias";
		
		$subj = $orderdatum . " Order med f�retagshyra/leasing �r upplagt!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendCustomerNumberMail_v1($ordernr) {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		// $recipient .= " peder";
		$recipient .= " sjabo";
		
		$subj = $orderdatum . " Order fr�n Norra Skogs�garna �r upplagt!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendCybairgunMail_v1($ordernr) {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		$recipient .= " jonas@cyberphoto.nu";
		$recipient .= " sjabo@cyberphoto.nu";
		
		$subj = $orderdatum . " Kampanjorder Microsoft xBox 360 �r upplagt!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendCybairgunMail_v2($ordernr) {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		$recipient .= " info@cybairgun.se";
		// $recipient .= " sjabo@cyberphoto.nu";
		
		$subj = $orderdatum . " Kampanjorder �r upplagt!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}
	
	function sendCashMail_v1($ordernr) {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		$recipient .= " sjabo";
		
		$subj = $orderdatum . " Order med kontant betalning �r upplagt!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function sendPayPalMail_v1($ordernr) {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		$recipient .= " sjabo";
		
		$subj = $orderdatum . " Order med PayPal betalning �r upplagt!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "V�nligen kontrollera detta i aff�rssystemet.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}
	
	function sendMessAboutCustomerComment_v1($ordernr,$order_kommentar,$fi) {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		// $recipient .= " kundtjanst";
		if ($fi) {
			$recipient .= " kundtjanst@cyberphoto.fi";
		} else {
			// $recipient .= " kundtjanst";
			$recipient .= " kolla_ordrar@cyberphoto.se";
			// $recipient .= " sjabo";
		}
		
		$subj = $orderdatum . " Order med kundkommentar �r upplagd!";

		$extra = "From: " . $addcreatedby;
		
		$text1 .= "Kundens kommentar: " . $order_kommentar . "\n\n";
		$text1 .= "V�nligen vidta l�mpliga �tg�rder.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		
		mail($recipient, $subj, $text1, $extra);

	}
	
	function viewBasketShort($kundvagn) {
		global $fi, $sv, $no;
	
		$output = "";
		if (ereg ("(grejor:)(.*)", $kundvagn,$matches)) {
			# Split the number of items and article id s into a list
			$orderlista = $matches[2];
			$argument = split ("\|", $orderlista);
		}
		//reverse array to show last article first
		$argument = array_reverse($argument, true);
	
		$goodscounter=0;
		$goodsvalue=0;
	
		$n = count($argument);
		//for ($i=0; ($i < $n);  $i+=2) {
		for ($i=$n-2; ($i > -1); $i+=-2) {
			$arg = $argument[$i];        # Article id
			$count = $argument[$i+1];    # Keeps track of the number of the same article
	
			$select  = "SELECT artnr, beskrivning, tillverkare  ";
			$select .= "FROM Artiklar ";
			$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
			$select .= "WHERE artnr='$arg'";
	
			# Alla v�rden f�rsvinner inte i varje loop, s� d�rf�r m�ste vi g�ra enligt nedan
			$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = "";
	
			$row = mysqli_fetch_array(mysqli_query(Db::getConnection(), $select));

			extract($row);
	
			$goodscounter += 1;
			$goodsvalue += ($utpris*$count);
	
			$description = $count . " st ";
	
			if ($tillverkare != '.') {
				$description .= $tillverkare . " ";
			}
				$description .= $beskrivning;
	
			$output .= $description . "\n";
		
		}
	
		return $output;
	
	}
	
	function getOperatorAbbListFull($op) {

		// $op = 1;
		
		$select = "SELECT DISTINCT mobile_plans.name AS AbbNamn, mobile_plans.iswebstorefeatured AS VisaKund, mobile_operator.name ";
		$select .= "FROM mobile_plans ";
		$select .= "LEFT JOIN mobile_operator ON mobile_plans.operator_id = mobile_operator.operator_id ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND isactive = -1 ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND isactive = -1 ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND mobile_plans.plan_length < 100 AND mobile_plans.operator_id = '" . $op . "' ";
		$select .= "ORDER BY mobile_plans.name ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

			echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n";

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			echo "<tr>\n";
			echo "<td class=\"abbheader\" colspan=\"2\"><img border=\"0\" src=\"/pic/1pix_cccc99.png\" width=\"800\" height=\"1\"></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td class=\"abbheader\" colspan=\"2\">$name $AbbNamn</td>\n";
			echo "</tr>\n";
			
			$this->getOperatorAbbListFullDescription($AbbNamn);
			
			}

		}

			echo "</table>\n";
	}	

	function getOperatorAbbListFullDescription($AbbNamn) {

		$select = "SELECT mobile_plans.description_new ";
		$select .= "FROM mobile_plans ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND isactive = -1 ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND isactive = -1 ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND mobile_plans.plan_length < 100 AND mobile_plans.name = '" . $AbbNamn . "' ";
		// $select .= "ORDER BY mobile_plans.description ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);
			
			echo "<tr>\n";
			echo "<td width=\"50\"></td>\n";
			echo "<td>";
			?>

			<div id="infocontainerdesc">
			<div class="roundtop">
			<div class="infor1"></div>
			<div class="infor2"></div>
			<div class="infor3"></div>
			<div class="infor4"></div>
			</div>
			
			<div class="content">

			<?php
			// include("$description");
			echo $description_new;
			?>
			
			</div>
			
			<div class="roundbottom">
			<div class="infor4"></div>
			<div class="infor3"></div>
			<div class="infor2"></div>
			<div class="infor1"></div>
			</div>
			
			<?php

			echo "</td>\n";
			echo "</tr>\n";
			
			}

		}
			echo "<tr>\n";
			echo "<td class=\"abbheader\" colspan=\"2\"><img border=\"0\" src=\"/pic/1pix.png\" width=\"1\" height=\"15\"></td>\n";
			echo "</tr>\n";

	}	

	function getExtraServicesForAbb($ID) {

		$select = "SELECT mobile_plans.name, mobile_plans.description_new ";
		$select .= "FROM mobile_plans ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND isactive = -1 ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND isactive = -1 ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND mobile_plans.plan_length > 99 AND mobile_plans.mobile_plans_id = '" . $ID . "' ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n";

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);
			
			echo "<tr>\n";
			echo "<td>\n";
			?>

			<div id="infocontainerdesc">
			<div class="roundtop">
			<div class="infor1"></div>
			<div class="infor2"></div>
			<div class="infor3"></div>
			<div class="infor4"></div>
			</div>
			
			<div class="content">

			<?php
			echo $description_new;
			?>
			
			</div>
			
			<div class="roundbottom">
			<div class="infor4"></div>
			<div class="infor3"></div>
			<div class="infor2"></div>
			<div class="infor1"></div>
			</div>
			
			<?php

			echo "</td>\n";
			echo "</tr>\n";
			
			}

			echo "</table>\n";

		}

	}	

	function returnFmaArticleNr($FMA,$ABB) {

				if ($FMA == 40) {
				
					if ($this->getMonthPlan($ABB) == 12) {
						return 126602;
					} elseif ($this->getMonthPlan($ABB) == 18) {
						return 126603;
					} elseif ($this->getMonthPlan($ABB) == 24) {
						return 126604;
					}
				
				} elseif ($FMA == 80) {
				
					if ($this->getMonthPlan($ABB) == 12) {
						return 126605;
					} elseif ($this->getMonthPlan($ABB) == 18) {
						return 126606;
					} elseif ($this->getMonthPlan($ABB) == 24) {
						return 126607;
					}
				
				} elseif ($FMA == 120) {
				
					if ($this->getMonthPlan($ABB) == 12) {
						return 126608;
					} elseif ($this->getMonthPlan($ABB) == 18) {
						return 126609;
					} elseif ($this->getMonthPlan($ABB) == 24) {
						return 126610;
					}
				
				} elseif ($FMA == 160) {
				
					if ($this->getMonthPlan($ABB) == 12) {
						return 126611;
					} elseif ($this->getMonthPlan($ABB) == 18) {
						return 126612;
					} elseif ($this->getMonthPlan($ABB) == 24) {
						return 126613;
					}
				
				} elseif ($FMA == 200) {
				
					if ($this->getMonthPlan($ABB) == 12) {
						return 128779;
					} elseif ($this->getMonthPlan($ABB) == 18) {
						return 128780;
					} elseif ($this->getMonthPlan($ABB) == 24) {
						return 128781;
					}
				
				} elseif ($FMA == 216) {
				
					if ($this->getMonthPlan($ABB) == 12) {
						return 1289958;
					} elseif ($this->getMonthPlan($ABB) == 18) {
						return 1289957;
					} elseif ($this->getMonthPlan($ABB) == 24) {
						return 1288997;
					}
				
				} elseif ($FMA == 240) {
				
					if ($this->getMonthPlan($ABB) == 12) {
						return 128946;
					} elseif ($this->getMonthPlan($ABB) == 18) {
						return 128947;
					} elseif ($this->getMonthPlan($ABB) == 24) {
						return 128948;
					}
				
				} elseif ($FMA == 248) {
				
					if ($this->getMonthPlan($ABB) == 12) {
						return 1248948;
					} elseif ($this->getMonthPlan($ABB) == 18) {
						return 1238948;
					} elseif ($this->getMonthPlan($ABB) == 24) {
						return 1228948;
					}
				
				} elseif ($FMA == 264) {
				
					if ($this->getMonthPlan($ABB) == 12) {
						return 1288946;
					} elseif ($this->getMonthPlan($ABB) == 18) {
						return 1288947;
					} elseif ($this->getMonthPlan($ABB) == 24) {
						return 1000127;
					}
				
				}

	}
	// ************************************ NEDAN TILLH�R ADMIN ******************************************

	function getAdminOperatorAbbList($op,$ID,$type) {
		global $expand, $operator_choose, $inactivated;

		// $op = 1;
		$desiderow = true;
		
		$select = "SELECT mobile_plans.mobile_plans_id, mobile_plans.name AS AbbNamn, mobile_plans.plan_length, mobile_plans.validto, mobile_plans.commissionAmt,  ";
		$select .= "mobile_plans.discountCustomerAmt, mobile_plans.artnr, mobile_plans.iswebstorefeatured, mobile_plans.operator_id, mobile_operator.name,   ";
		$select .= "mobile_plans.month_fee, mobile_plans.isactive, mobile_plans.foretag, Artiklar.utpris  ";
		if ($ID != "" || $expand == "yes") {
			$select .= ",mobile_plans.description, mobile_plans.description_new ";
		}
		$select .= "FROM mobile_plans ";
		// $select .= "LEFT JOIN operator ON mobile_plans.operator_id = operator.operator_id ";
		$select .= "LEFT JOIN mobile_operator ON mobile_plans.operator_id = mobile_operator.operator_id ";
		$select .= "INNER JOIN Artiklar ON mobile_plans.artnr = Artiklar.artnr ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND isactive = -1 ";
		$select .= "WHERE mobile_plans.abb_type = '" . $type . "' ";
		if ($inactivated != "yes") {
			$select .= "AND isactive = -1 ";
			$select .= "AND validFrom < now() AND validTo > now() ";
		}
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND iswebstorefeatured = -1 ";
		}
		if ($operator_choose != "") {
			$select .= "AND mobile_plans.operator_id = '" . $operator_choose . "' ";
		}
		$select .= "AND mobile_plans.plan_length < 100 AND mobile_plans.operator_id = '" . $op . "' ";
		$select .= "ORDER BY mobile_plans.foretag ASC, mobile_plans.name ASC, mobile_plans.plan_length ASC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// exit;
		}
		$res = mysqli_query($this->conn_my, $select);

			// echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"93%\">\n";
			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			echo "<tr>\n";
			echo "<td width=\"20\"><b><font face=\"Verdana\" size=\"1\">&nbsp;</font></b></td>\n";
			if ($op == 1) {
				echo "<td colspan=\"11\"><a name=\"$op\"><img border=\"0\" src=\"tele2.png\"></a></td>\n";
			} elseif ($op == 2) {
				echo "<td colspan=\"11\"><a name=\"$op\"><img border=\"0\" src=\"telia.png\"></a></td>\n";
			} elseif ($op == 3) {
				echo "<td colspan=\"11\"><a name=\"$op\"><img border=\"0\" src=\"halebop.png\"></a></td>\n";
			} elseif ($op == 5) {
				echo "<td colspan=\"11\"><a name=\"$op\"><img border=\"0\" src=\"telenor.png\"></a></td>\n";
			}
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td width=\"20\"><b>&nbsp;</b></td>\n";
			echo "<td width=\"300\"><b>Abonnemangsnamn</b></td>\n";
			echo "<td width=\"60\" align=\"center\"><b>Bindning</b></td>\n";
			echo "<td width=\"100\" align=\"center\"><b>G�ller till</b></td>\n";
			echo "<td width=\"85\" align=\"center\"><b>Antal dagar</b></td>\n";
			echo "<td width=\"75\" align=\"center\"><b>Provision</b></td>\n";
			echo "<td width=\"75\" align=\"center\"><b>Till kund</b></td>\n";
			echo "<td width=\"75\" align=\"center\"><b>TB</b></td>\n";
			echo "<td width=\"75\" align=\"center\"><b>SEK/M�n</b></td>\n";
			echo "<td width=\"75\" align=\"center\"><b>Artikel nr</b></td>\n";
			echo "<td width=\"50\" align=\"center\"><b>Kundpris</b></td>\n";
			echo "<td width=\"50\" align=\"center\"><b>Visas</b></td>\n";
			// echo "<td width=\"75\"><b>&nbsp;</b></td>\n";
			// echo "<td width=\"75\"><b>&nbsp;</b></td>\n";
			echo "</tr>\n";

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			if ($ID == $mobile_plans_id) {
					$rowcolor = "chooserow";
			} else {
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
			}

			$validto = preg_replace('/:[0-9][0-9][0-9]/','', $validto);
			$aterstar = $this->getDaysLeft($validto);
			$tb = $commissionAmt - $discountCustomerAmt;

			echo "<tr>\n";
			echo "<td>&nbsp;</a></td>\n";
			if ($isactive != -1) {
				echo "<td class=\"$rowcolor\"><strike>$name $AbbNamn</strike></td>\n";
			} else {
				if ($foretag == 1) {
					// echo "<td class=\"$rowcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?ID=$mobile_plans_id#$operator_id" . "\">$name $AbbNamn - <b>F�retagsabonnemang</b></a></td>\n";
					// echo "<td class=\"$rowcolor\">$name $AbbNamn - <b>F�retagsabonnemang</b></td>\n";
					echo "<td class=\"$rowcolor\" align=\"left\"><a href=\"javascript:winPopupCenter(650, 1300, 'edit_abonnemang.php?edit=1&artid=$mobile_plans_id');\">$name $AbbNamn - <b>F�retagsabonnemang</b></a></td>\n";
				} else {
					// echo "<td class=\"$rowcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?ID=$mobile_plans_id#$operator_id" . "\">$name $AbbNamn</a></td>\n";
					// echo "<td class=\"$rowcolor\">$name $AbbNamn</td>\n";
					echo "<td class=\"$rowcolor\" align=\"left\"><a href=\"javascript:winPopupCenter(650, 1300, 'edit_abonnemang.php?edit=1&artid=$mobile_plans_id');\">$name $AbbNamn</a></td>\n";
				}
			}
			echo "<td class=\"$rowcolor\" align=\"center\">$plan_length</td>\n";
			echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d", strtotime($validto)) . "</td>\n";
			echo "<td class=\"$rowcolor\" align=\"center\">$aterstar</td>\n";
			echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($commissionAmt, 0, ',', ' ') . " kr</td>\n";
			echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($discountCustomerAmt, 0, ',', ' ') . " kr</td>\n";
			echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($tb, 0, ',', ' ') . " kr</td>\n";
			echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($month_fee, 0, ',', ' ') . " kr</td>\n";
			echo "<td class=\"$rowcolor\" align=\"center\">$artnr</td>\n";
			echo "<td align=\"center\">$utpris</td>\n";
			if ($iswebstorefeatured == -1) {
				echo "<td align=\"center\"><img border=\"0\" src=\"status_green.gif\"></td>\n";
			} else {
				echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>\n";
			}
			/*
			if ($isactive != -1) {
				echo "<td align=\"center\">L�st</td>\n";
			} else {
				// echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?ID=$mobile_plans_id#$operator_id" . "\">Detaljer</td>\n";
				echo "<td align=\"center\"><a href=\"javascript:winPopupCenter(210, 550, 'add_abonnemang.php?ID=$mobile_plans_id');\">Kopiera</td>\n";
			}
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" || $_SERVER['REMOTE_ADDR'] == "192.168.1.62" || $_SERVER['REMOTE_ADDR'] == "192.168.1.53") {
				echo "<td align=\"center\"><a href=\"javascript:winPopupCenter(650, 1300, 'edit_abonnemang.php?edit=1&artid=$mobile_plans_id');\">�ndra</a></td>\n";
			} else {
				echo "<td align=\"center\">�ndra</td>\n";
			}
			*/
			echo "</tr>\n";
			
			if ($ID == $mobile_plans_id || $expand == "yes") {

			echo "<tr>\n";
			echo "<td colspan=\"12\">&nbsp;</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td>&nbsp;</td>\n";
			echo "<td class=\"chooserow2\"><a href=\"javascript:winPopupCenter(650, 1300, 'edit_abonnemang.php?edit=1&artid=$mobile_plans_id');\">�ndra</a></td>\n";
			// echo "<td colspan=\"10\" align=\"left\" class=\"chooserow2\"><a href=\"" . $_SERVER['PHP_SELF'] . "?ID=$mobile_plans_id&copypost=yes" . "\">Kopiera</td>\n";
			echo "<td colspan=\"10\" align=\"left\" class=\"chooserow2\"><a href=\"javascript:winPopupCenter(210, 550, 'add_abonnemang.php?ID=$mobile_plans_id');\">Kopiera</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan=\"12\">&nbsp;</td>\n";
			echo "</tr>\n";

			if ($description_new != "") {
			
				echo "<tr>\n";
				echo "<td>&nbsp;</td>\n";
				echo "<td colspan=\"11\" style=\"border: 1px solid #C0C0C0\">$description_new</td>\n";
				echo "</tr>\n";

			} else {

				/*
				echo "<tr>\n";
				echo "<td>&nbsp;</td>\n";
				echo "<td colspan=\"11\" style=\"border: 1px solid #FF0000; background-color: #FFFF00\">";
					include("/web/www/abonnemang/$description");
				echo "</td>\n";
				echo "</tr>\n";
				*/
			
			}

			echo "<tr>\n";
			echo "<td colspan=\"12\">&nbsp;</td>\n";
			echo "</tr>\n";
			
			}

				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
			
			}
			
		}

		$this->getAdminOperatorTjanster($op,$ID,$type);
		echo "</table>\n";
	}	

	function getAdminOperatorTjanster($op,$ID,$type) {
		global $expand, $operator_choose;

		// $op = 1;
		$desiderow = true;
		
		$select = "SELECT mobile_plans.mobile_plans_id, mobile_plans.name AS AbbNamn, mobile_plans.plan_length, mobile_plans.validto, mobile_plans.commissionAmt,  ";
		$select .= "mobile_plans.discountCustomerAmt, mobile_plans.artnr, mobile_plans.iswebstorefeatured, mobile_plans.operator_id, mobile_operator.name,   ";
		$select .= "mobile_plans.month_fee, mobile_plans.isactive, mobile_plans.foretag, Artiklar.utpris  ";
		if ($ID != "" || $expand == "yes") {
		$select .= ",mobile_plans.description, mobile_plans.description_new ";
		}
		$select .= "FROM mobile_plans ";
		// $select .= "LEFT JOIN operator ON mobile_plans.operator_id = operator.operator_id ";
		$select .= "LEFT JOIN mobile_operator ON mobile_plans.operator_id = mobile_operator.operator_id ";
		$select .= "INNER JOIN Artiklar ON mobile_plans.artnr = Artiklar.artnr ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND isactive = -1 ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND isactive = -1 ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		if ($operator_choose != "") {
			$select .= "AND mobile_plans.operator_id = '" . $operator_choose . "' ";
		}
		$select .= "AND mobile_plans.plan_length > 99 AND mobile_plans.operator_id = '" . $op . "' ";
		$select .= "AND mobile_plans.abb_type = '" . $type . "' ";
		$select .= "ORDER BY mobile_plans.name ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

			// echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"93%\">\n";
			/*
			echo "<tr>\n";
			echo "<td width=\"20\"><b><font face=\"Verdana\" size=\"1\">&nbsp;</font></b></td>\n";
			if ($op == 1) {
				echo "<td colspan=\"11\"><a name=\"$op\"><img border=\"0\" src=\"tele2.png\"></a></td>\n";
			} elseif ($op == 2) {
				echo "<td colspan=\"11\"><a name=\"$op\"><img border=\"0\" src=\"telia.png\"></a></td>\n";
			} elseif ($op == 3) {
				echo "<td colspan=\"11\"><a name=\"$op\"><img border=\"0\" src=\"halebop.png\"></a></td>\n";
			}
			echo "</tr>\n";
			*/
		if (mysqli_num_rows($res) > 0) {

			echo "<tr>\n";
			echo "<td><b>&nbsp;</b></td>\n";
			echo "<td><b>Till�ggstj�nster</b></td>\n";
			echo "<td align=\"center\"><b>Bindning</b></td>\n";
			echo "<td align=\"center\"><b>G�ller till</b></td>\n";
			echo "<td align=\"center\"><b>Antal dagar</b></td>\n";
			echo "<td align=\"center\"><b>Provision</b></td>\n";
			echo "<td align=\"center\"><b>Till kund</b></td>\n";
			echo "<td align=\"center\"><b>TB</b></td>\n";
			echo "<td width=\"75\" align=\"center\"><b>SEK/M�n</b></td>\n";
			echo "<td align=\"center\"><b>Artikel nr</b></td>\n";
			echo "<td align=\"center\"><b>Kundpris</b></td>\n";
			echo "<td align=\"center\"><b>Visas</b></td>\n";
			echo "<td><b>&nbsp;</b></td>\n";
			echo "<td><b>&nbsp;</b></td>\n";
			echo "</tr>\n";

		
			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			if ($ID == $mobile_plans_id) {
					$rowcolor = "chooserow";
			} else {
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
			}

			$validto = preg_replace('/:[0-9][0-9][0-9]/','', $validto);
			$aterstar = $this->getDaysLeft($validto);
			$tb = $commissionAmt - $discountCustomerAmt;

			echo "<tr>\n";
			echo "<td>&nbsp;</a></td>\n";
			echo "<td class=\"$rowcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?ID=$mobile_plans_id#$operator_id" . "\">$name $AbbNamn</a></td>\n";
			echo "<td class=\"$rowcolor\" align=\"center\">$plan_length</td>\n";
			echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d", strtotime($validto)) . "</td>\n";
			echo "<td class=\"$rowcolor\" align=\"center\">$aterstar</td>\n";
			echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($commissionAmt, 0, ',', ' ') . " kr</td>\n";
			echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($discountCustomerAmt, 0, ',', ' ') . " kr</td>\n";
			echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($tb, 0, ',', ' ') . " kr</td>\n";
			echo "<td class=\"$rowcolor\" align=\"right\">" . number_format($month_fee, 0, ',', ' ') . " kr</td>\n";
			echo "<td class=\"$rowcolor\" align=\"center\">$artnr</td>\n";
			echo "<td align=\"center\">$utpris</td>\n";
			if ($iswebstorefeatured == -1) {
				echo "<td align=\"center\"><img border=\"0\" src=\"status_green.gif\"></td>\n";
			} else {
				echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>\n";
			}
			echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?ID=$mobile_plans_id#$operator_id" . "\">Detaljer</td>\n";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" || $_SERVER['REMOTE_ADDR'] == "192.168.1.62") {
				echo "<td align=\"center\"><a href=\"javascript:winPopupCenter(550, 550, 'edit_abonnemang.php?edit=1&artid=$mobile_plans_id');\">�ndra</a></td>\n";
			} else {
				echo "<td align=\"center\">�ndra</td>\n";
			}
			echo "</tr>\n";
			
			if ($ID == $mobile_plans_id) {

			echo "<tr>\n";
			echo "<td colspan=\"12\">&nbsp;</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td>&nbsp;</td>\n";
			echo "<td colspan=\"11\" class=\"chooserow2\"><a href=\"javascript:winPopupCenter(600, 850, '/order/edit_abonnemang.php?edit=1&artid=$mobile_plans_id');\">Redigera</a></b></td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan=\"12\">&nbsp;</td>\n";
			echo "</tr>\n";

			if ($description_new != "" || $expand == "yes") {
			
				echo "<tr>\n";
				echo "<td>&nbsp;</td>\n";
				echo "<td colspan=\"11\" style=\"border: 1px solid #C0C0C0\">$description_new</td>\n";
				echo "</tr>\n";

			} else {

				echo "<tr>\n";
				echo "<td>&nbsp;</td>\n";
				echo "<td colspan=\"11\" style=\"border: 1px solid #FF0000; background-color: #FFFF00\">";
					include("/web/www/abonnemang/$description");
				echo "</td>\n";
				echo "</tr>\n";
			
			}

			echo "<tr>\n";
			echo "<td colspan=\"12\">&nbsp;</td>\n";
			echo "</tr>\n";
			
			}

				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
			
			}
			
		}

		// echo "</table>\n";
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

	function getSpecAbb($mobileID) {

		$select  = "SELECT * FROM mobile_plans WHERE mobile_plans_id = '" . $mobileID . "' ";

		$res = mysqli_query($select);

		$rows = mysqli_fetch_object($res);

		return $rows;

	}

	function getSpecAbbNew($mobileID) {

		$select  = "SELECT mp.*, mo.name AS operator ";
		$select .= "FROM mobile_plans mp ";
		$select .= "JOIN mobile_operator mo ON mp.operator_id = mo.operator_id ";
		$select .= "WHERE mp.mobile_plans_id = '" . $mobileID . "' ";

		$res = mysqli_query($select);

		$rows = mysqli_fetch_object($res);

		return $rows;

	}
	
	function getMobileOperatorInt($abb_operator_id) {
		
		$select = "SELECT operator_id, name FROM mobile_operator ";
		$select .= "WHERE operator_id IN (1,2,3,5) ";
		$select .= "ORDER BY name ";
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			echo "<select size=\"1\" name=\"abb_operator_id\">\n";
			echo "<option></option>\n";
		
			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

				echo "<option value=\"$operator_id\" ";
				if ($operator_id == $abb_operator_id) {
					echo " selected ";
				}
				echo ">$name</option>\n";
			
			}

				echo "</select>\n";
		
		} else {
		
			echo "";
		
		}
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

	function getAnstallda() {

	global $abb_createdby;

	$select  = "SELECT sign, namn FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";

	$res = mysqli_query($select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		echo "<option value=\"$sign\"";
			
		if ($abb_createdby == $sign) {
			echo " selected";
		}
			
		echo ">" . $namn . "</option>";
			
		
		// endwhile;

		}

	}

	function check_artikel_status($abb_artnr) {
		
	$select  = "SELECT artnr FROM Artiklar WHERE artnr = '" . $abb_artnr . "' ";
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			extract(mysqli_fetch_array($res));
			
			return true;
		
		} else {
		
			return false;
		
		}

	}	

	function check_artikel_status_mobile_plans($abb_artnr,$abb_id) {
		
	$select  = "SELECT artnr FROM mobile_plans WHERE artnr = '" . $abb_artnr . "' AND NOT (mobile_plans_id = '" . $abb_id . "') AND iswebstorefeatured = -1 AND isactive = -1 ";
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			// extract(mssql_fetch_array($res));
			
			return true;
		
		} else {
		
			return false;
		
		}

	}	

	function AbbAdminChange($abb_id,$abb_name,$abb_iswebstorefeatured,$abb_isactive,$abb_from,$abb_to,$abb_artnr,$abb_plan_length,$abb_commissionAmt,$abb_discountCustomerAmt,$abb_createdby,$abb_operator_id,$abb_description,$abb_month_fee,$abb_foretag,$abb_description_new) {

	// mssql_query ("UPDATE mobile_plans  SET name = '$abb_name', iswebstorefeatured = '$abb_iswebstorefeatured', isactive = '$abb_isactive', validfrom = '$abb_from', validto = '$abb_to', artnr = '$abb_artnr', plan_length = '$abb_plan_length', commissionAmt = '$abb_commissionAmt', discountCustomerAmt = '$abb_discountCustomerAmt', createdby = '$abb_createdby', operator_id = '$abb_operator_id', description = '$abb_description' WHERE mobile_plans_id = '$abb_id' ");
	// mssql_query ("INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontCreatedBy) VALUES ('$addsection','$addfrom','$addto','$addpicture','$addartnr','$addlinc','$addstore','$addcomment','$addcreatedby') ");

	// header("Location: update_front.php?section=$addsection");
		$insert = "UPDATE mobile_plans ";
		$insert .= "SET name = '$abb_name', iswebstorefeatured = '$abb_iswebstorefeatured', isactive = '$abb_isactive', validfrom = '$abb_from', validto = '$abb_to', artnr = '$abb_artnr', plan_length = '$abb_plan_length', commissionAmt = '$abb_commissionAmt', discountCustomerAmt = '$abb_discountCustomerAmt', createdby = '$abb_createdby', operator_id = '$abb_operator_id', description = '$abb_description', month_fee = '$abb_month_fee', foretag = '$abb_foretag', description_new = '$abb_description_new' ";
		$insert .= "WHERE mobile_plans_id = '$abb_id' ";
		// echo $insert;
		// exit;
		$res = mysqli_query($this->conn_my2, $insert);


	}

	function AbbAdminCopy($abb_id) {

		$conn_my = Db::getConnection(true);
	
		// mssql_query ("INSERT INTO mobile_plans (name,description_new,iswebstorefeatured,artnr,validfrom,validto,plan_length,isactive,commissionAmt,discountCustomerAmt,operator_id,abb_type)
				 // SELECT name, description_new, '0', '0', getDate(), validto, plan_length, isactive, '0', '0', operator_id, '1' FROM mobile_plans WHERE mobile_plans_id = '" . $abb_id . "' ");

		$insert  = "INSERT INTO mobile_plans (artnr,name,description_new,iswebstorefeatured,validfrom,validto,plan_length,isactive,commissionAmt,discountCustomerAmt,operator_id,abb_type,month_fee) ";
		$insert .= "SELECT '51156761', '*** NYSKAPAD ***', description_new, '0', now(), validto, plan_length, isactive, '0', '0', operator_id, '1', month_fee FROM mobile_plans WHERE mobile_plans_id = '" . $abb_id . "' ";
		// echo $insert;
		// exit;
		// $res = mysqli_query($this->conn_my2, $insert);
		$res = mysqli_query($conn_my, $insert);
				 
		header("Location: abonnemang_mobil.php");

	}

	function AbbDataAdminCopy($abb_id) {

	// mssql_query ("INSERT INTO mobile_plans (name,description_new,iswebstorefeatured,artnr,validfrom,validto,plan_length,isactive,commissionAmt,discountCustomerAmt,operator_id,abb_type)
				 // SELECT name, description_new, '0', '0', getDate(), validto, plan_length, isactive, '0', '0', operator_id, '2' FROM mobile_plans WHERE mobile_plans_id = '" . $abb_id . "' ");

		$insert = "INSERT INTO mobile_plans (name,description_new,iswebstorefeatured,artnr,validfrom,validto,plan_length,isactive,commissionAmt,discountCustomerAmt,operator_id,abb_type,month_fee) ";
		$insert .= "SELECT name, description_new, '0', '0', now(), validto, plan_length, isactive, '0', '0', operator_id, '2', month_fee FROM mobile_plans WHERE mobile_plans_id = '" . $abb_id . "' ";
		// echo $insert;
		// exit;
		$res = mysqli_query($this->conn_my2, $insert);

	// header("Location: update_front.php?section=$addsection");

	}

	function AbbCopy($abb_id,$count) {

		$conn_my = Db::getConnection(true);
	
		for ($i = 1; $i <= $count; $i++) {
		
			$insert  = "INSERT INTO mobile_plans (artnr,name,description_new,iswebstorefeatured,validfrom,validto,plan_length,isactive,commissionAmt,discountCustomerAmt,operator_id,abb_type,month_fee) ";
			$insert .= "SELECT '51156761', '*** NYSKAPAD ***', description_new, '0', now(), validto, plan_length, isactive, '0', '0', operator_id, abb_type, month_fee FROM mobile_plans WHERE mobile_plans_id = '" . $abb_id . "' ";
			// echo $insert . "<br>\n";
			// exit;
			$res = mysqli_query($conn_my, $insert);
		
		}
				 
		sleep(2);
		// header("Location: abonnemang_mobil.php");

	}
	
	function getOperatorAbbListKalkyl($op,$type) {
		global $abb, $op;
		
		$select = "SELECT DISTINCT mobile_plans.name AS AbbNamn, mobile_plans.iswebstorefeatured AS VisaKund, mobile_operator.name ";
		$select .= "FROM mobile_plans ";
		// $select .= "LEFT JOIN operator ON mobile_plans.operator_id = operator.operator_id ";
		$select .= "LEFT JOIN mobile_operator ON mobile_plans.operator_id = mobile_operator.operator_id ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() AND isactive = -1 ";
		$select .= "WHERE validFrom < now() AND validTo > now() AND isactive = -1 ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		// $select .= "AND mobile_plans.plan_length < 100 AND mobile_plans.operator_id = '" . $op . "' ";
		$select .= "AND mobile_plans.plan_length < 100 ";
		if ($type == 2) { // om mobilt bredband
			$select .= "AND abb_type = 2 ";
		} else {
			$select .= "AND abb_type = 1 ";
		}
		$select .= "ORDER BY mobile_operator.name, mobile_plans.name ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

			// echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			// echo "<tr>\n";
			// echo "<td width=\"10\"><img border=\"0\" src=\"pic/bord_3.jpg\"></td>\n";
			// echo "<td width=\"105\"><img border=\"0\" src=\"pic/choose_abb.gif\"></td>\n";
			// echo "<td>";

		if (mysqli_num_rows($res) > 0) {
			
			echo "<select name=\"abb\" onchange=\"this.form.submit();\" >\n";
			echo "<option value=\"\">V�lj</option>\n";

			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);

				echo "<option value=\"$AbbNamn\"";
				
				if ($AbbNamn == $abb) {
					echo " selected";
				}
				
				if ($VisaKund == -1) {
					echo ">" . $name . " " . $AbbNamn . "</option> \n";
				} else {
					echo ">" . $name . " " . $AbbNamn . " - Visas EJ</option> \n";
				}
			
			}

			echo "</select>\n";
			
			// echo "<br><br>";

		}

			/*
			echo "</td>\n";
			if ($op == 1) {
				if ($type == 2) {
					echo "<td align=\"center\" width=\"75\"><a class=\"linku\" href=\"javascript:winPopupCenter(500, 870, 'http://www.cyberphoto.se/abonnemang/" . $name . "_data_abonnemang.php');\">Se �versikt</a></td>\n";
				} else {
					echo "<td align=\"center\" width=\"75\"><a class=\"linku\" href=\"javascript:winPopupCenter(450, 1000, 'http://www.cyberphoto.se/abonnemang/" . $name . "_abonnemang.php');\">Se �versikt</a></td>\n";
				}
			} elseif ($op == 2) {
				if ($type == 2) {
					echo "<td align=\"center\" width=\"75\"><a class=\"linku\" href=\"javascript:winPopupCenter(780, 870, 'http://www.cyberphoto.se/abonnemang/" . $name . "_data_abonnemang.php');\">Se �versikt</a></td>\n";
				} else {
					echo "<td align=\"center\" width=\"75\"><a class=\"linku\" href=\"javascript:winPopupCenter(780, 870, 'http://www.cyberphoto.se/abonnemang/" . $name . "_abonnemang.php');\">Se �versikt</a></td>\n";
				}
			} elseif ($op == 3) {
				echo "<td align=\"center\" width=\"75\"><a class=\"linku\" href=\"javascript:winPopupCenter(530, 610, 'http://www.cyberphoto.se/abonnemang/" . $name . "_abonnemang.php');\">Se �versikt</a></td>\n";
			}
			echo "</tr>\n";
			echo "</table>\n";
			*/

	}	

	function getMobilePhoneListKalkyl($artnr2,$type) {
		
		// echo $artnr2;
		
		$select = "SELECT artnr, tillverkare, beskrivning, bild FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		// $select .= "WHERE (kategori_id = 336 OR kategori_id = 462) AND utpris > 0 ";
		if ($type == 2) { // om mobilt bredband
			$select .= "WHERE abb_data = -1 AND utpris > 0 ";
		} else {
			$select .= "WHERE kategori_id = 336 AND utpris > 0 ";
		}
		$select .= "AND ej_med=0 AND (utgangen=0 OR lagersaldo > 0) AND demo=0 "; // vi skiter i demolurar
		$select .= "ORDER BY tillverkare, beskrivning ";
		if ($type == 2) {
			// echo $select;
		}
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			// echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			// echo "<tr>\n";
			// echo "<td width=\"10\"><img border=\"0\" src=\"pic/bord_1.jpg\"></td>\n";
			// echo "<td>";
			
			// echo "<select name=\"artnr\" onchange=\"this.form.submit();\">\n";
			echo "<select name=\"article\" onchange=\"this.form.submit();\">\n";
			echo "<option value=\"\">V�lj</option>\n";

			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
				
				$displaypicture = $this->getMobilePhonePicture($artnr2);
				
				$displayname = $tillverkare . " " . $beskrivning;

				if (strlen($displayname) >= 60)
					$displayname = substr ($displayname, 0, 60) . "...";

				echo "<option value=\"$artnr\"";
				
				if ($artnr == $artnr2) {
					echo " selected";
				}
				
				// echo ">" . $tillverkare . " " . $beskrivning . "</option> \n";
				echo ">" . $displayname . "</option> \n";
			
			}

			echo "</select>\n";
			/*
			echo "</td>\n";
			echo "<td width=\"120\" align=\"center\"><img title=\"Vi bjuder p� frakten n�r du best�ller mobiltelefon med abonnemang (g�ller postpaket).\"border=\"0\" src=\"/pic/stamp_frakt_v3.gif\"></td>\n";
			echo "<td width=\"70\" align=\"center\">$displaypicture</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			*/
			
			// echo "<br><br>";
			
		}

	}	

	function displayPriceAbbInternal() {
		global $article, $abb;
		
		$bruttopris = round($this->getMobilePriceWithoutTax($article),0);
		$nettopris = round($this->getMobileNettoPrice($article),0);
		$marginal = $bruttopris - $nettopris;
		
		echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
		echo "<tr>";
		echo "<td class=\"rubrik\">";
		echo "Brutto telefon";
		echo "</td>\n";
		echo "<td class=\"rubrik\" align=\"right\">";
		echo $bruttopris . " SEK";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>";
		echo "<td class=\"rubrik\">";
		echo "Netto telefon";
		echo "</td>\n";
		echo "<td class=\"rubrik\" align=\"right\">";
		echo $nettopris . " SEK";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>";
		echo "<td class=\"rubrik\">";
		echo "Marginal telefon";
		echo "</td>\n";
		echo "<td class=\"rubrik\" align=\"right\">";
		echo $marginal . " SEK";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>";
		echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"750\" size=\"1\">";
			
		$select = "SELECT artnr, plan_length, commissionAmt, operator_id FROM mobile_plans ";
		// $select .= "WHERE validFrom < GETDATE() AND validTo > GETDATE() ";
		$select .= "WHERE validFrom < now() AND validTo > now() ";
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
		$select .= "AND iswebstorefeatured = -1 ";
		}
		$select .= "AND isactive = -1 AND name = '" . $abb . "' ";
		$select .= "ORDER BY plan_length ASC ";
		
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);

				echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
				echo "<tr>";
				echo "<td class=\"rubrik\">";
				echo "Bindningstid";
				echo "</td>\n";
				echo "<td class=\"rubrik\">";
				echo $plan_length . " m�n";
				echo "</td>\n";
				echo "<td class=\"rubrik\">";
				echo "Kick";
				echo "</td>\n";
				echo "<td class=\"rubrik\" align=\"right\">";
				echo $commissionAmt . " SEK";
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>";
				if ($plan_length > 0) {
					$this->displayKalkyl($operator_id,$plan_length,$commissionAmt,$nettopris);
				}
				echo "<hr noshade color=\"#85000D\" align=\"left\" width=\"350\" size=\"1\">";
			
			}
			
		} else {
		
			echo "";
		
		}

			// echo "</table>\n";
			
	}

	function displayKalkyl($operator_id,$plan_length,$commissionAmt,$nettopris) {
		
		echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
		echo "<tr>";
		echo "<td class=\"rubriklight\" width=\"100\">&nbsp;</td>";
		echo "<td class=\"rubriklight\" align=\"right\">";
		echo "40 kr f�rh�jd m�nadsavgift";
		echo "</td>\n";
		echo "<td class=\"rubriklight\" align=\"right\">";
			$this->calculateProfit($plan_length,$commissionAmt,$nettopris,40);
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>";
		echo "<td class=\"rubriklight\" width=\"100\">&nbsp;</td>";
		echo "<td class=\"rubriklight\" align=\"right\">";
		echo "80 kr f�rh�jd m�nadsavgift";
		echo "</td>\n";
		echo "<td class=\"rubriklight\" align=\"right\">";
			$this->calculateProfit($plan_length,$commissionAmt,$nettopris,80);
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>";
		echo "<td class=\"rubriklight\" width=\"100\">&nbsp;</td>";
		echo "<td class=\"rubriklight\" align=\"right\">";
		echo "120 kr f�rh�jd m�nadsavgift";
		echo "</td>\n";
		echo "<td class=\"rubriklight\" align=\"right\">";
			$this->calculateProfit($plan_length,$commissionAmt,$nettopris,120);
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>";
		echo "<td class=\"rubriklight\" width=\"100\">&nbsp;</td>";
		echo "<td class=\"rubriklight\" align=\"right\">";
		echo "160 kr f�rh�jd m�nadsavgift";
		echo "</td>\n";
		echo "<td class=\"rubriklight\" align=\"right\">";
			$this->calculateProfit($plan_length,$commissionAmt,$nettopris,160);
		echo "</td>\n";
		echo "</tr>\n";
		if ($operator_id == 2) {
			echo "<tr>";
			echo "<td class=\"rubriklight\" width=\"100\">&nbsp;</td>";
			echo "<td class=\"rubriklight\" align=\"right\">";
			echo "200 kr f�rh�jd m�nadsavgift";
			echo "</td>\n";
			echo "<td class=\"rubriklight\" align=\"right\">";
				$this->calculateProfit($plan_length,$commissionAmt,$nettopris,200);
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>";
		// echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"750\" size=\"1\">";
			
			
	}

	function getMobileNettoPrice($artnr) {
		
		
		$select = "SELECT art_id FROM Artiklar ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			return $row->art_id;
		
		} else {
		
			return "";
		
		}
	}	

	function calculateProfit($plan_length,$commissionAmt,$nettopris,$amountMonth) {

		/*
		echo $plan_length . "<br>";
		echo $commissionAmt . "<br>";
		echo $nettopris . "<br>";
		echo $amountMonth . "<br>";
		*/

		$avbetalning = ($plan_length * $amountMonth);
		$tillgang = $avbetalning + $commissionAmt;
		$overskott = $tillgang - $nettopris;
		// echo $tillgang . "<br>";
		
		// return $overskott;
		if ($overskott > 0) {
			echo "<span class=\"rubriklightgreen\">$overskott</span>";
		} else {
			echo "<span class=\"rubriklightred\">$overskott</span>";
		}
		
	}

}
?>

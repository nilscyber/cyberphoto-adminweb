<?php

require_once("Locs.php");
require_once("Log.php");

Class CDeparture {

	var $conn_my;
	var $conn_my2; 
	var $conn_ms; 
        var $conn_my3;

	function __construct() {

		// $this->conn_my = @mysqli_connect(getenv('DB_HOST') ?: 'db', getenv('DB_USER') ?: 'appuser', getenv('DB_PASS') ?: 'apppass');
		// @mysqli_select_db($this->conn_my, getenv('DB_NAME') ?: 'cyberphoto');
		$this->conn_my = Db::getConnectionDb('cyberadmin');
		$this->conn_my2 = Db::getConnectionDb('cyberadmin');
		//$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
		//@mssql_select_db ("cyberphoto", $this->conn_ms);
        $this->conn_my3 = Db::getConnection(true);

	}

	// ****************************** NEDAN ÄR FÖR ADMINISTRATION ********************************* //

	function getDaysLeft($dateto) {

		$now = time();
		$timeto = strtotime($dateto);
		$diff = $timeto - $now;
		$sek = $diff % 60;
		$min = ($diff / 60) % 60;
		$hour = ($diff / 3600);
		$days = ($diff / 86400);
		$days = floor($days);
		$days = round($days, 0);
		return $days;
	}

	function makeInsertDeparture($dateto) {

		if ($this->getLastDeparture() != "") {
			$lastdate = $this->getLastDeparture();
		} else {
			$lastdate = date("Y-m-d", mktime(0,0,0,date("m"),date("d"),date("Y")));
		}
		$lastdate = strtotime($lastdate);
		$dateto = strtotime($dateto);

		if ($dateto < $lastdate) {
			echo "det fungerar inte";
			exit;
		}

		$i=1;
		do
		{
		
		// $makedate = date("Y-m-d H:i:s", mktime(17,0,0,date("m",$lastdate),date("d",$lastdate)+$i,date("Y")));
		// $makedate = date("Y-m-d H:i:s", mktime(16,30,0,date("m",$lastdate),date("d",$lastdate)+$i,date("Y")));
		$makedate = date("Y-m-d H:i:s", mktime(16,30,0,date("m",$lastdate),date("d",$lastdate)+$i,date('Y', $lastdate)));
		$makedateOpenMorning = date("Y-m-d H:i:s", mktime(9,30,0,date("m",$lastdate),date("d",$lastdate)+$i,date('Y', $lastdate)));
		$makedateOpenMorning_FI = date("Y-m-d H:i:s", mktime(10,00,0,date("m",$lastdate),date("d",$lastdate)+$i,date('Y', $lastdate)));
		$makedateCloseLunch = date("Y-m-d H:i:s", mktime(12,00,0,date("m",$lastdate),date("d",$lastdate)+$i,date('Y', $lastdate)));
		$makedateCloseLunch_FI = date("Y-m-d H:i:s", mktime(12,00,0,date("m",$lastdate),date("d",$lastdate)+$i,date('Y', $lastdate)));
		$makedateOpenLunch = date("Y-m-d H:i:s", mktime(13,00,0,date("m",$lastdate),date("d",$lastdate)+$i,date('Y', $lastdate)));
		$makedateOpenLunch_FI = date("Y-m-d H:i:s", mktime(13,00,0,date("m",$lastdate),date("d",$lastdate)+$i,date('Y', $lastdate)));
		$makedateCloseEvening = date("Y-m-d H:i:s", mktime(17,00,0,date("m",$lastdate),date("d",$lastdate)+$i,date('Y', $lastdate)));
		$makedateCloseEvening_FI = date("Y-m-d H:i:s", mktime(16,00,0,date("m",$lastdate),date("d",$lastdate)+$i,date('Y', $lastdate)));
		$makedateto = strtotime($makedate);
		$startday = date('l', strtotime("$makedate"));
		$getdayofweek = date('w', strtotime("$makedate"));
		$getyear = date('Y', strtotime("$makedate"));
		if ($getdayofweek != 0 && $getdayofweek != 6) {
			$this->addDeparture($makedate,$makedateOpenMorning,$makedateCloseLunch,$makedateOpenLunch,$makedateCloseEvening,1,1,0,0,$makedateOpenMorning_FI,$makedateCloseLunch_FI,$makedateOpenLunch_FI,$makedateCloseEvening_FI);
			// echo $makedate . " " . $startday . " " . $getdayofweek . " 1<br>";
		} else {
			$this->addDeparture($makedate,$makedateOpenMorning,$makedateCloseLunch,$makedateOpenLunch,$makedateCloseEvening,0,0,0,0,$makedateOpenMorning_FI,$makedateCloseLunch_FI,$makedateOpenLunch_FI,$makedateCloseEvening_FI);
			// echo $makedate . " " . $startday . " " . $getdayofweek . " 0<br>";
		}
		$i++;
		
		}

		while ($makedateto < $dateto);

	}

	function addDeparture($makedate,$makedateOpenMorning = null,$makedateCloseLunch = null,$makedateOpenLunch = null,$makedateCloseEvening = null,$active = null,$departure_Phone_SE = null,$departure_Phone_FI = null,$departure_Phone_NO = null,$makedateOpenMorning_FI = null,$makedateCloseLunch_FI = null,$makedateOpenLunch_FI = null,$makedateCloseEvening_FI = null) {

		$insrt = "INSERT INTO cyberadmin.departure (departure_time,departure_Active,departure_OpenMorning,departure_CloseLunch,departure_OpenLunch,departure_CloseEvening,departure_Phone_SE,departure_Phone_FI,departure_Phone_NO,departure_OpenMorning_FI,departure_CloseLunch_FI,departure_OpenLunch_FI,departure_CloseEvening_FI)	VALUES ('$makedate','$active','$makedateOpenMorning','$makedateCloseLunch','$makedateOpenLunch','$makedateCloseEvening','$departure_Phone_SE','$departure_Phone_FI','$departure_Phone_NO','$makedateOpenMorning_FI','$makedateCloseLunch_FI','$makedateOpenLunch_FI','$makedateCloseEvening_FI') ";
		// echo $insrt . "<br>";
		

		mysqli_query($this->conn_my2, $insrt);

	}

	function getDepartureFuture() {
		
	$select  = "SELECT * FROM cyberadmin.departure ";
	$select .= "WHERE departure_time > now() ";
	$select .= "ORDER BY departure_time ASC ";
	$res = mysqli_query($this->conn_my, $select);

	// echo $select;
		$desiderow = true;
		echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
		echo "<tr>";
		echo "<td width=\"65\"><b>Dag</b></td>";
		echo "<td width=\"120\" align=\"center\"><b>Sista avgångstid</b></td>";
		echo "<td width=\"35\"><b>Aktiv</b></td>";
		echo "<td width=\"120\" align=\"center\"><b>Öppnar morgon</b></td>";
		echo "<td width=\"120\" align=\"center\"><b>Stänger lunch</b></td>";
		echo "<td width=\"120\" align=\"center\"><b>Öppnar lunch</b></td>";
		echo "<td width=\"120\" align=\"center\"><b>Stänger kväll</b></td>";
		echo "<td width=\"15\"><img border=\"0\" src=\"sv_mini.jpg\"></td>";
		echo "<td width=\"15\"><img border=\"0\" src=\"fi_mini.jpg\"></td>";
		echo "<td width=\"15\"><img border=\"0\" src=\"no_mini.jpg\"></td>";
		echo "<td width=\"15\" align=\"center\"><img border=\"0\" src=\"recycle.png\"></td>";
		echo "<td width=\"120\" align=\"center\"><b>Ändrad</b></td>";
		echo "<td width=\"30\" align=\"center\"><b>Av</b></td>";
		echo "<td width=\"180\"><b>Orsak</b></td>";
		echo "<td width=\"50\"><b></b></td>";
		echo "</tr>";

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
	
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				
				$this_month = date("F", strtotime($departure_Time));
				
				if ($this_month != $actual_month) {
					
					echo "<tr>";
					echo "<td colspan=\"25\" class=\"align_left bold\">" . $this->replace_month(date("F", strtotime($departure_Time))) . "</td>";
					echo "</tr>";
					
				}
				
				
				$disp_day = date('l', strtotime("$departure_Time"));
				$disp_day = $this->replace_days($disp_day);
				
				echo "<tr>";
				// echo "<td class=\"$rowcolor\">" . date('l', strtotime("$departure_Time")) . "</td>";
				echo "<td class=\"$rowcolor\">" . $disp_day . "</td>";
				echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d H:i", strtotime($departure_Time)) . "</td>";
				if ($departure_Active == 0) {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>";
				} else {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_green.gif\"></td>";
				}
				echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d H:i", strtotime($departure_OpenMorning)) . "</td>";
				echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d H:i", strtotime($departure_CloseLunch)) . "</td>";
				echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d H:i", strtotime($departure_OpenLunch)) . "</td>";
				echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d H:i", strtotime($departure_CloseEvening)) . "</td>";
				if ($departure_Phone_SE == 0) {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>";
				} else {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_green.gif\"></td>";
				}
				if ($departure_Phone_FI == 0) {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>";
				} else {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_green.gif\"></td>";
				}
				if ($departure_Phone_NO == 0) {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>";
				} else {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_green.gif\"></td>";
				}
				if ($departure_Recycle == 0) {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>";
				} else {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_green.gif\"></td>";
				}
				if ($departure_ChangeTime != null) {
					echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d H:i", strtotime($departure_ChangeTime)) . "</td>";
				} else {
					echo "<td class=\"$rowcolor\" align=\"center\"></td>";
				}
				echo "<td class=\"$rowcolor\" align=\"center\">$departure_ChangeBy</td>";
				echo "<td class=\"$rowcolor\">$departure_ChangeWhy</td>";
				echo "<td align=\"center\"><a href=\"javascript:winPopupCenter(450, 500, 'edit_departure.php?edit=1&depid=$departure_ID');\">Ändra</a></td>\n";
				echo "</tr>";
	
				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				
				$actual_month = date("F", strtotime($departure_Time));
				
			}
		
		} else {
		
			echo "<tr>";
			echo "<td colspan=\"6\" class=\"$rowcolor\"><b>Inga avgångar finns inlagda just nu</b></td>";
			echo "</tr>";
		
		}

		echo "</table>";

	}

	function getLastDeparture() {
		
	$select  = "SELECT * FROM cyberadmin.departure ";
	$select .= "WHERE departure_time > now() ";
	$select .= "ORDER BY departure_Time DESC ";
	$select .= "LIMIT 1 ";
	$res = mysqli_query($this->conn_my2, $select);

	// echo $select;
		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

			return $departure_Time;
		
			endwhile;

		} else {
			
			return "";
		
		}

	}

	function getFirstDeparture() {
		
	$select  = "SELECT departure_Time FROM cyberadmin.departure ";
	$select .= "WHERE departure_time > now() AND departure_Active = 1 ";
	$select .= "ORDER BY departure_Time ASC ";
	$select .= "LIMIT 1 ";
	$res = @mysqli_query($this->conn_my, $select);

	// echo $select;
		if (@mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

			return $departure_Time;
		
			endwhile;

		} else {
			
			return "";
		
		}

	}

	function getSecondDeparture() {

	$todaydate = date("Y-m-d", time());
	// echo $todaydate;
	// exit;
		
	$select  = "SELECT departure_Time FROM cyberadmin.departure ";
	$select .= "WHERE departure_time > now() AND departure_Active = 1 ";
	$select .= "AND NOT (departure_Time LIKE '%" . $todaydate . "%') ";
	$select .= "ORDER BY departure_Time ASC ";
	$select .= "LIMIT 1 ";
	$res = @mysqli_query($this->conn_my, $select);

	// echo $select;
		if (@mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

			return $departure_Time;
		
			endwhile;

		} else {
			
			return "";
		
		}

	}

	function getSpecDeparture($depid) {

		$select  = "SELECT * FROM cyberadmin.departure WHERE departure_id = '" . $depid . "' ";
		// echo $select;

		$res = mysqli_query($this->conn_my, $select);
		$rows = mysqli_fetch_object($res);

		return $rows;

	}

	function getAnstallda() {
		global $dep_createdby;

		$select  = "SELECT sign, namn FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";

		$res = mysqli_query($this->conn_my3, $select);

			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);

				echo "<option value=\"$sign\"";
					
				if ($dep_createdby == $sign) {
					echo " selected";
				}
					
				echo ">" . $namn . "</option>\n";
				
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

	function departureChange($dep_time,$dep_active,$dep_changewhy,$dep_createdby,$dep_ip,$dep_id,$departure_OpenMorning,$departure_CloseLunch,$departure_OpenLunch,$departure_CloseEvening,$dep_phone_se,$dep_phone_fi,$dep_phone_no,$departure_OpenMorning_FI,$departure_CloseLunch_FI,$departure_OpenLunch_FI,$departure_CloseEvening_FI,$dep_recycle) {

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			mysqli_query("UPDATE cyberadmin.departure SET departure_Time = '$dep_time', departure_Active = '$dep_active', departure_OpenMorning = '$departure_OpenMorning', departure_CloseLunch = '$departure_CloseLunch', departure_OpenLunch = '$departure_OpenLunch', departure_CloseEvening = '$departure_CloseEvening', departure_Phone_SE = '$dep_phone_se', departure_Phone_FI = '$dep_phone_fi', departure_Phone_NO = '$dep_phone_no', departure_OpenMorning_FI = '$departure_OpenMorning_FI', departure_CloseLunch_FI = '$departure_CloseLunch_FI', departure_OpenLunch_FI = '$departure_OpenLunch_FI', departure_CloseEvening_FI = '$departure_CloseEvening_FI', departure_Recycle = '$dep_recycle' WHERE departure_ID = '$dep_id' ");
		} else {
			mysqli_query("UPDATE cyberadmin.departure SET departure_Time = '$dep_time', departure_ChangeBy = '$dep_createdby', departure_ChangeWhy = '$dep_changewhy', departure_Active = '$dep_active', departure_ChangeTime = now(), departure_ChangeIP = '$dep_ip', departure_OpenMorning = '$departure_OpenMorning', departure_CloseLunch = '$departure_CloseLunch', departure_OpenLunch = '$departure_OpenLunch', departure_CloseEvening = '$departure_CloseEvening', departure_Phone_SE = '$dep_phone_se', departure_Phone_FI = '$dep_phone_fi', departure_Phone_NO = '$dep_phone_no', departure_OpenMorning_FI = '$departure_OpenMorning_FI', departure_CloseLunch_FI = '$departure_CloseLunch_FI', departure_OpenLunch_FI = '$departure_OpenLunch_FI', departure_CloseEvening_FI = '$departure_CloseEvening_FI', departure_Recycle = '$dep_recycle' WHERE departure_ID = '$dep_id' ");
		}

	// header("Location: update_front.php?section=$addsection");

	}
	
	static function replace_days($string) {
			$from = array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
			$to = array("Måndag","Tisdag","Onsdag","Torsdag","Fredag","Lördag","Söndag");
			return str_replace($from, $to, $string);
	}

	static function replace_month($string) {
			$from = array("January","February","March","April","May","June","July","August","September","October","November","December");
			$to = array("Januari","Februari","Mars","April","Maj","Juni","Juli","Augusti","September","Oktober","November","December");
			return str_replace($from, $to, $string);
	}
	
	function OnlineStatus() {
		global $currentUrl, $sv, $fi, $no;

		echo "<div class=\"online_container\">\n";

		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.departure ";
		if ($fi) {
			$select .= "WHERE ((departure_OpenMorning_FI < now() AND departure_CloseLunch_FI > now()) OR (departure_OpenLunch_FI < now() AND departure_CloseEvening_FI > now())) ";
		} else {
			$select .= "WHERE ((departure_OpenMorning < now() AND departure_CloseLunch > now()) OR (departure_OpenLunch < now() AND departure_CloseEvening > now())) ";
		}
		if ($no || $fi) {
			$select .= "AND departure_Active = 8 ";
		} else {
			$select .= "AND departure_Active = 1 ";
		}
		
		$res = @mysqli_query($this->conn_my, $select);

		if (@mysqli_num_rows($res) > 0) {

            echo "<a class=\"online_linc\" href=\"https://" . $_SERVER['HTTP_HOST'] . "/" . strtolower(l('Customer service')) . "\"><div class=\"\">" . l('We are') . " <span class=\"online_text\">" . l('ONLINE') . "</span></div></a>\n";
            echo "<a class=\"online_linc\" href=\"https://" . $_SERVER['HTTP_HOST'] . "/" . strtolower(l('Customer service')) . "\"><div class=\"online_phone\">" . l('showphonenr') . "</div></a>\n";

		} else {
			
            echo "<a class=\"online_linc\" href=\"https://" . $_SERVER['HTTP_HOST'] . "/" . strtolower(l('Customer service')) . "\"><div class=\"\">" . l('Contact us') . "</div></a>\n";
			if (preg_match("/cybairgun/i", $currentUrl)) {
				echo "<a href=\"https://" . $_SERVER['HTTP_HOST'] . "/" . strtolower(l('Customer service')) . "\"><div class=\"online_mail\">" . l('contactmail_cybairgun') . "</div></a>\n";
			} else {
				echo "<a href=\"https://" . $_SERVER['HTTP_HOST'] . "/" . strtolower(l('Customer service')) . "\"><div class=\"online_mail\">" . l('contactmail') . "</div></a>\n";
			}
		
		}
		
		if (!$fi && !$no) {
			$this->ParcelStatus();
		}
	
		echo "</div>\n";

		echo "<div id=\"info_container_posten\" style=\"display: none;\">\n";
		echo "<div class=\"container_posten\">\n";
		echo "<b>Information</b><br><br>";
		echo "På CyberPhoto jobbar vi alltid med inställningen att dina varor skall skickas samma dag. För att detta skall fungera gäller följande:<br><br>- Att din order läggs innan angiven tid.<br><br>- Att alla produkter på din ordern finns i lager.<br><br>- Att din betalning är fullgjord.<br><br>Hälsningar<br>CyberPhoto";
		echo "</div>\n";
		echo "</div>\n";

	}

	function ParcelStatus() {

		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.departure ";
		$select .= "WHERE departure_Time < DATE_ADD(NOW(), INTERVAL 10 HOUR) AND departure_Time >= NOW() ";
		$select .= "AND departure_Active = 1 ";
		
		$res = @mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (@mysqli_num_rows($res) > 0) {
		
			$departureTime = date('H:i', strtotime($row->departure_Time));

            // echo "<div class=\"\">" . l('We are') . "</div>\n";
			echo "<span onclick=\"show_hide('info_container_posten');\" style=\"cursor:pointer;\">\n";
            echo "<div class=\"online_posten_head top10\">Handla före " . $departureTime . "</div>\n";
            // echo "<div class=\"online_posten\">skickas idag " . $departureTime . "</div>\n";
            echo "<div class=\"online_posten\">så skickar vi idag</div>\n";
			echo "</span>\n";

		}
	
	}
	
	function getOnlineDayTime() {
		global $fi, $no;

		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.departure ";
		if ($fi) {
			$select .= "WHERE departure_OpenLunch < now() AND departure_CloseEvening > now() ";
			$select .= "AND DATE_SUB(departure_Time, INTERVAL 1 HOUR) > now() ";
			$select .= "AND departure_Phone_FI = 1 ";
		} elseif ($no) {
			$select .= "WHERE departure_OpenMorning < now() AND departure_CloseEvening > now() ";
			$select .= "AND DATE_SUB(departure_Time, INTERVAL 1 HOUR) > now() ";
			$select .= "AND departure_Phone_NO = 1 ";
		} else {
			$select .= "WHERE departure_OpenMorning < now() AND departure_CloseEvening > now() ";
			$select .= "AND DATE_SUB(departure_Time, INTERVAL 1 HOUR) > now() ";
			$select .= "AND departure_Phone_SE = 1 ";
		}
		if ($no || $fi) {
			$select .= "AND departure_Active = 8 ";
		} else {
			$select .= "AND departure_Active = 1 ";
		}
		
		$res = @mysqli_query(Db::getConnection(false), $select);
		
		if (@mysqli_num_rows($res) > 0) {
			return true;
		} else {
			return false;
		}
		
	}

	function showPhoneToday() {
		global $sv, $no, $fi;

		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.departure ";
		if ($fi) {
			$select .= "WHERE departure_OpenMorning_FI < DATE_ADD(NOW(), INTERVAL 3 HOUR) AND departure_CloseEvening_FI >= NOW() ";
		} else {
			$select .= "WHERE departure_OpenMorning < DATE_ADD(NOW(), INTERVAL 3 HOUR) AND departure_CloseEvening >= NOW() ";
		}
		if ($no || $fi) {
			$select .= "AND departure_Active = 8 ";
		} else {
			$select .= "AND departure_Active = 1 ";
		}
		
		$res = @mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);

		if (@mysqli_num_rows($res) > 0) {
		
			if ($fi) { // lägger på en timme på finska tiderna så att de visas korrekt i deras tidszon
				$OpenMorning = date('H:i', strtotime($row->departure_OpenMorning_FI)+3600);
				$CloseLunch = date('H:i', strtotime($row->departure_CloseLunch_FI)+3600);
				$OpenLunch = date('H:i', strtotime($row->departure_OpenLunch_FI)+3600);
				$CloseEvening = date('H:i', strtotime($row->departure_CloseEvening_FI)+3600);
			} else {
				$OpenMorning = date('H:i', strtotime($row->departure_OpenMorning));
				$CloseLunch = date('H:i', strtotime($row->departure_CloseLunch));
				$OpenLunch = date('H:i', strtotime($row->departure_OpenLunch));
				$CloseEvening = date('H:i', strtotime($row->departure_CloseEvening));
			}

            echo "<div class=\"bold bottom5 left5\">" . l('Phone hours today') . "</div>\n";
            echo "<div class=\"bottom5 left5\">" . $OpenMorning . " - " . $CloseLunch . "</div>\n";
            echo "<div class=\"bottom5 left5\">" . $OpenLunch . " - " . $CloseEvening . "</div>\n";

		}
	
	}

	function getIfRecycle() {
	
		$todaydate = date("Y-m-d", time());
		
		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.departure ";
		$select .= "WHERE departure_Time LIKE '" . $todaydate . "%' AND departure_Recycle = 1 ";
	
		$res = @mysqli_query(Db::getConnection(false), $select);
	
		if (@mysqli_num_rows($res) > 0) {
			return true;
		} else {
			return false;
		}
	
	}

	function OnlineStatusSimple() {
		global $sv, $fi, $no;

		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.departure ";
		if ($fi) {
			$select .= "WHERE ((departure_OpenMorning_FI < now() AND departure_CloseLunch_FI > now()) OR (departure_OpenLunch_FI < now() AND departure_CloseEvening_FI > now())) ";
		} else {
			$select .= "WHERE ((departure_OpenMorning < now() AND departure_CloseLunch > now()) OR (departure_OpenLunch < now() AND departure_CloseEvening > now())) ";
		}
		if ($no || $fi) {
			$select .= "AND departure_Active = 8 ";
		} else {
			$select .= "AND departure_Active = 1 ";
		}
		
		$res = @mysqli_query($this->conn_my, $select);

		if (@mysqli_num_rows($res) > 0) {
			
			return true;

		} else {
			
			return false;
	
		}
		
	}
	
}

?>

<?php

include_once 'Db.php'; 

Class CTemp {


	function __construct() {

	}

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

	function getLatestTimeStamp() {
		$select = "SELECT DATE_FORMAT(t.tTime,'%i') AS minute ";
		$select .= "FROM cyberadmin.temp t ";
		// $select .= "WHERE t.tSensor = '" . $sensor . "' ";
		$select .= "ORDER BY t.tTime DESC ";
		$select .= "LIMIT 1 ";
		
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		
		return $rows->minute;
	}
	
	function addTemp($temperature,$humidity,$sensor = null) {
	
		$todaydate = date("Y-m-d H:i:s", time());
		if ($sensor == "") {
			$sensor = 1;
		}
		
		$updt  = "INSERT INTO cyberadmin.temp ";
		$updt .= "(tTime, tTemperature, tHumidity, tSensor) ";
		$updt .= "VALUES ";
		$updt .= "('$todaydate','$temperature','$humidity','$sensor') ";

		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);
		
		if ($temperature > 19) {
			// $this->sendTempMess($temperature,$humidity);
		}
	
	}

	function sendTempMess($temperature,$humidity) {

		$orderdatum = date("j/n-Y H:i", time());
		
		$addcreatedby = "noreply";

		$recipient .= " sjabo@cyberphoto.nu";
		$recipient .= " nils@cyberphoto.nu";
		$recipient .= " patrick@cyberphoto.nu";
		$recipient .= " thomas@cyberphoto.nu";
		
		$subj = $orderdatum . " Förhöjd temperatur (" . $temperature . ") i serverrummet!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "Vänligen kontrollera detta omgående.\n\n";
		// $text1 .= "Just nu:\n";
		$text1 .= "Temperatur: " . $temperature . "\n";
		$text1 .= "Luftfuktighet: " . $humidity . "%\n\n";
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}
	
	function showTempList($sensor) {
	
		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.temp ";
		$select .= "WHERE tSensor = $sensor ";
		$select .= "ORDER BY tTime DESC ";
		$select .= "LIMIT 1000 ";
		
		// echo $select;
		// exit;
		
		$res = @mysqli_query(Db::getConnection(false), $select);
		if (mysqli_num_rows($res) > 0) {

			if ($sensor == 1) {
				echo "<h1>Temperaturlogg - Serverrummet</h1>\n";
			} elseif ($sensor == 2) {
				echo "<h1>Temperaturlogg - Ute</h1>\n";
			} elseif ($sensor == 3) {
				echo "<h1>Temperaturlogg - Nya delen - Norra</h1>\n";
			} elseif ($sensor == 4) {
				echo "<h1>Temperaturlogg - Nya delen - Södra</h1>\n";
			} elseif ($sensor == 5) {
				echo "<h1>Temperaturlogg - Köket</h1>\n";
			} elseif ($sensor == 6) {
				echo "<h1>Temperaturlogg - Inbyte</h1>\n";
			} elseif ($sensor == 7) {
				echo "<h1>Temperaturlogg - Service</h1>\n";
			} elseif ($sensor == 8) {
				echo "<h1>Temperaturlogg - Packsal</h1>\n";
			} elseif ($sensor == 9) {
				echo "<h1>Temperaturlogg - Flyttbar</h1>\n";
			}
			echo "<table>\n";
			echo "\t<tr>\n";
			echo "\t\t<th>Datum</th>\n";
			echo "\t\t<th>Tid</th>\n";
			echo "\t\t<th>Temperatur</th>\n";
			echo "\t\t<th></th>\n";
			echo "\t\t<th>Luftfuktighet</th>\n";
			echo "\t</tr>\n";
		
			while ($row = mysqli_fetch_object($res)) {
				
				echo "\t<tr>\n";
				echo "\t<td>\n" . date("Y-m-d", strtotime($row->tTime)) . "</td>\n";
				echo "\t<td>\n" . date("H:i", strtotime($row->tTime)) . "</td>\n";
				echo "\t<td>\n" . number_format($row->tTemperature, 1) . "&deg;</td>\n";
				if ($row->tTemperature > 22 && $sensor == 1) {
					echo "\t<td><img border=\"0\" src=\"/status_red.jpg\"></td>\n";
				} else {
					echo "\t<td><img border=\"0\" src=\"/status_green.jpg\"></td>\n";
				}
				echo "\t<td>\n" . $row->tHumidity . "%</td>\n";
				echo "\t<tr>\n";
				
			}
			
			echo "</table>\n";
		}
		
	}

	function showLastTemp($sensor) {
	
		$select  = "SELECT tTemperature ";
		$select .= "FROM cyberadmin.temp ";
		$select .= "WHERE tSensor = $sensor ";
		$select .= "ORDER BY tTime DESC ";
		$select .= "LIMIT 1 ";
		
		$res = @mysqli_query(Db::getConnection(false), $select);
		$row = mysqli_fetch_object($res);
		
		if ($row->tTemperature < 0) {
			$actualtemp = "<span class=\"span_blue\">" . $row->tTemperature . "</span>";
		} else {
			$actualtemp = "<span class=\"span_green2\">" . $row->tTemperature . "</span>";
		}
		return $actualtemp;

	}

	function showLastTempInfopanel($sensor) {
	
		$select  = "SELECT tTemperature ";
		$select .= "FROM cyberadmin.temp ";
		$select .= "WHERE tSensor = $sensor ";
		$select .= "ORDER BY tTime DESC ";
		$select .= "LIMIT 1 ";
		
		$res = @mysqli_query(Db::getConnection(false), $select);
		$row = mysqli_fetch_object($res);
		
		$actualtemp = $row->tTemperature;
		
		return $actualtemp;

	}

}

?>

<?php


Class CDeparture {

var $conn_my;

function __construct() {

	$this->conn_my = Db::getConnectionDb('cyberadmin');
	// $this->conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
	// @mysqli_select_db($this->conn_my, "cyberadmin");
	$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
	@mssql_select_db ("cyberphoto", $this->conn_ms);

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
	$makedate = date("Y-m-d H:i:s", mktime(16,30,0,date("m",$lastdate),date("d",$lastdate)+$i,date("Y")));
	$makedateto = strtotime($makedate);
	$startday = date('l', strtotime("$makedate"));
	$getdayofweek = date('w', strtotime("$makedate"));
	$getyear = date('Y', strtotime("$makedate"));
	if ($getdayofweek != 0 && $getdayofweek != 6) {
		$this->addDeparture($makedate,1);
		// echo $makedate . " " . $startday . " " . $getdayofweek . " 1<br>";
	} else {
		$this->addDeparture($makedate,0);
		// echo $makedate . " " . $startday . " " . $getdayofweek . " 0<br>";
	}
	$i++;
	
	}

	while ($makedateto < $dateto);

}

function addDeparture($time,$active) {

$insrt = "INSERT INTO departure (departure_time,departure_Active) VALUES ('$time','$active') ";

mysqli_query($this->conn_my, $insrt);

}

function getDepartureFuture() {
	
$select  = "SELECT * FROM departure ";
$select .= "WHERE departure_time > now() ";
$select .= "ORDER BY departure_time ASC ";
$res = mysqli_query($this->conn_my, $select);

// echo $select;
	$desiderow = true;
	echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr>";
	echo "<td width=\"75\"><b>Dag</b></td>";
	echo "<td width=\"120\"><b>Sista betsällningstid</b></td>";
	echo "<td width=\"35\"><b>Aktiv</b></td>";
	echo "<td width=\"130\" align=\"center\"><b>Manuellt ändrad</b></td>";
	echo "<td width=\"30\" align=\"center\"><b>Av</b></td>";
	echo "<td width=\"300\"><b>Anledning</b></td>";
	echo "<td width=\"75\"><b></b></td>";
	echo "</tr>";

	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_array($res)):
	
		extract($row);

		if ($desiderow == true) {
			$rowcolor = "firstrow";
		} else {
			$rowcolor = "secondrow";
		}
		
		echo "<tr>";
		echo "<td class=\"$rowcolor\">" . date('l', strtotime("$departure_Time")) . "</td>";
		echo "<td class=\"$rowcolor\">" . date("Y-m-d H:i", strtotime($departure_Time)) . "</td>";
		if ($departure_Active == 0) {
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
		echo "<td align=\"center\"><a href=\"javascript:winPopupCenter(350, 500, 'edit_departure.php?edit=1&depid=$departure_ID');\">Ändra</a></td>\n";
		echo "</tr>";

		if ($desiderow == true) {
			$desiderow = false;
		} else {
			$desiderow = true;
		}
		
		endwhile;
	
	} else {
	
		echo "<tr>";
		echo "<td colspan=\"6\" class=\"$rowcolor\"><b>Inga avgångar finns inlagda just nu</b></td>";
		echo "</tr>";
	
	}

	echo "</table>";

}

function getLastDeparture() {
	
$select  = "SELECT * FROM departure ";
$select .= "WHERE departure_time > now() ";
$select .= "ORDER BY departure_Time DESC ";
$select .= "LIMIT 1 ";
$res = mysqli_query($this->conn_my, $select);

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
	
$select  = "SELECT departure_Time FROM departure ";
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
	
$select  = "SELECT departure_Time FROM departure ";
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

$select  = "SELECT * FROM departure WHERE departure_id = '" . $depid . "' ";

$res = mysqli_query($this->conn_my, $select);

$rows = mysqli_fetch_object($res);

return $rows;

}

function getAnstallda() {

global $dep_createdby;

$select  = "SELECT sign, namn FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";

$res = mssql_query ($select, $this->conn_ms);

	while ($row = mssql_fetch_array($res)) {
	
	extract($row);

	echo "<option value=\"$sign\"";
		
	if ($dep_createdby == $sign) {
		echo " selected";
	}
		
	echo ">" . $namn . "</option>\n";
		
	
	// endwhile;

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

function departureChange($dep_time,$dep_active,$dep_changewhy,$dep_createdby,$dep_ip,$dep_id) {

mysqli_query("UPDATE departure SET departure_Time = '$dep_time', departure_ChangeBy = '$dep_createdby', departure_ChangeWhy = '$dep_changewhy', departure_Active = '$dep_active', departure_ChangeTime = now(), departure_ChangeIP = '$dep_ip' WHERE departure_ID = '$dep_id' ");

// header("Location: update_front.php?section=$addsection");

}
}

?>

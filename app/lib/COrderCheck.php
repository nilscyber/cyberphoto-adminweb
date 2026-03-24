<?php

include("connections.php");
require_once("CCheckIpNumber.php");


Class COrderCheck {

	var $conn_my; 
	var $conn_my2; 
        var $conn_my3;

function __construct() {

	global $fi;

	$this->conn_my = Db::getConnection();
	$this->conn_my2 = Db::getConnectionDb('cyberadmin');
        $this->conn_my3 = Db::getConnection(true);

}

function getActualAlerts($timespan) {
	global $sortby;

$rowcolor = true;

		echo "<table>";
		echo "<tr>";
		echo "<td width=\"100\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=artnr\">Artikel nr</u></a></b></td>";
		echo "<td width=\"300\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=product\">Benämning</u></a></b></td>";
		echo "<td width=\"150\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=time\">Bevakas till</u></a></b></td>";
		echo "<td width=\"80\" align=\"center\"><b><u><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=user\">Bevakas av</u></a></b></td>";
		echo "<td width=\"75\" align=\"center\"><b>&nbsp;</b></td>";
		echo "</tr>";

$select  = "SELECT checkID, checkTo, checkRecipient, checkArtnr, tillverkare, beskrivning ";
$select .= "FROM OrderCheck ";
$select .= "JOIN Artiklar ON Artiklar.artnr = OrderCheck.checkArtnr ";
$select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
if ($timespan) {
	$select .= "WHERE checkFrom < NOW() AND CheckTo > NOW() ";
} else {
	$select .= "WHERE checkFrom > NOW() ";
}
if ($sortby == "artnr") {
	$select .= "ORDER BY checkArtnr ASC ";
} elseif ($sortby == "product") {
	$select .= "ORDER BY tillverkare ASC, beskrivning ASC ";
} elseif ($sortby == "user") {
	$select .= "ORDER BY checkRecipient ASC ";
} else {
	$select .= "ORDER BY checkTo ASC ";
}
	// echo $select;
	// exit;

$res = mysqli_query($this->conn_my, $select);

	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_array($res)):
	
		extract($row);

		if ($rowcolor == true) {
			$backcolor = "firstrow";
		} else {
			$backcolor = "secondrow";
		}

		echo "<tr>";
		echo "<td class=\"$backcolor\">$checkArtnr</td>";
		echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=" . $checkArtnr . "\">$tillverkare $beskrivning</a></td>";
		echo "<td class=\"$backcolor\" align=\"center\">$checkTo</td>";
		echo "<td class=\"$backcolor\" align=\"center\">$checkRecipient</td>";
		echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $checkID . "\">ändra</a></td>";
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
		echo "<td colspan=\"5\"><font color=\"#000000\"><b>Inga artiklar kommer bevakas</b></td>";
		echo "</tr>";
	
	}
		echo "</table>";

}

function getSpecAlerts($ID) {

$select  = "SELECT * FROM OrderCheck WHERE checkID = '" . $ID . "' ";

$res = mysqli_query($this->conn_my, $select);

$rows = mysqli_fetch_object($res);

return $rows;

}

function getAnstallda() {

global $addRecipient;

$select  = "SELECT sign, namn, mail FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";

$res = mysqli_query($this->conn_my3, $select);

	while ($row = mysqli_fetch_array($res)) {
	
	extract($row);

	echo "<option value=\"$mail\"";
		
	if ($addRecipient == $mail) {
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

function check_artikel_status($addArtnr) {
	
$select  = "SELECT artnr FROM Artiklar WHERE artnr = '" . $addArtnr . "' ";
$res = mysqli_query($this->conn_my, $select);

	if (mysqli_num_rows($res) > 0) {

		extract(mysqli_fetch_array($res));
		
		return $artnr;
		// return true;
	
	} else {
	
		return false;
	
	}

}	

function doAlertChange($addID,$addFrom,$addTo,$addArtnr,$addRecipient) {

	$conn_my = Db::getConnection(true);

	$updt = "UPDATE OrderCheck SET checkFrom = '$addFrom', checkTo = '$addTo', checkArtnr = '$addArtnr', checkRecipient = '$addRecipient' WHERE checkID = '$addID'";

	$res = mysqli_query($conn_my, $updt);

	header("Location: webordercheck.php");

}

function doAlertAdd($addFrom,$addTo,$addArtnr,$addRecipient) {

	$conn_my = Db::getConnection(true);

	$updt = "INSERT INTO OrderCheck (checkFrom,checkTo,checkArtnr,checkRecipient) VALUES ('$addFrom','$addTo','$addArtnr','$addRecipient')";

	$res = mysqli_query($conn_my, $updt);

	header("Location: webordercheck.php");

}

}
?>

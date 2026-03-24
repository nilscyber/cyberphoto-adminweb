<?php


Class CTomteverkstan {

var $conn_my;

function __construct() {

	$this->conn_my = Db::getConnectionDb('cyberadmin');


}

// function addTomteOnskan($fraga,$onskan,$motiv,$namn,$epost,$tele,$IP) {
function addTomteOnskan($fraga,$onskan,$motiv,$namn,$epost,$IP) {

// $insrt = "INSERT INTO tomteverkstan2010 (tomte_Datum,tomte_Svar,tomte_Onskan,tomte_Motiv,tomte_Namn,tomte_Epost,tomte_Telefon,tomte_IP) VALUES (now(),'$fraga','$onskan','$motiv','$namn','$epost','$tele','$IP') ";
$insrt = "INSERT INTO tomteverkstan2010 (tomte_Datum,tomte_Svar,tomte_Onskan,tomte_Motiv,tomte_Namn,tomte_Epost,tomte_IP) VALUES (now(),'$fraga','$onskan','$motiv','$namn','$epost','$IP') ";

mysqli_query($this->conn_my, $insrt);

}

// ****************************** NEDAN ÄR FÖR ADMINISTRATION ********************************* //

function getTomteOnskan() {
	
$select  = "SELECT DATE_FORMAT(tomte_Datum, '%Y-%m-%d') AS PubDate, COUNT(tomte_ID) AS Antal FROM tomteverkstan2010 ";
// $select .= "WHERE departure_time > now() ";
$select .= "GROUP BY PubDate ";
$select .= "ORDER BY tomte_Datum ASC ";
$res = mysqli_query($this->conn_my, $select);

// echo $select;
	$desiderow = true;
	echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr>";
	echo "<td width=\"75\" align=\"left\"><b>Datum</b></td>";
	echo "<td width=\"55\" align=\"center\"><b>Antal</b></td>";
	echo "<td width=\"75\" align=\"center\"><b></b></td>";
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
		echo "<td class=\"$rowcolor\" align=\"left\">$PubDate</td>";
		echo "<td class=\"$rowcolor\" align=\"center\">$Antal</td>";
		echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] ."?date=$PubDate\">Detaljer</a></td>";
		echo "</tr>";

		if ($desiderow == true) {
			$desiderow = false;
		} else {
			$desiderow = true;
		}
		
		endwhile;
	
	} else {
	
		echo "<tr>";
		echo "<td colspan=\"3\" class=\"$rowcolor\"><b>Inga önskningar finns inlagda!</b></td>";
		echo "</tr>";
	
	}

	echo "</table>";

}

function getTomteOnskanDetail($date) {
	
$select  = "SELECT * FROM tomteverkstan2010 ";
$select .= "WHERE tomte_Datum LIKE '%" . $date . "%' ";
$select .= "ORDER BY tomte_Datum ASC ";
$res = mysqli_query($this->conn_my, $select);

// echo $select;
	$desiderow = true;
	echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr>";
	// echo "<td width=\"150\" align=\"left\"><b>Namn</b></td>";
	echo "<td width=\"20\" align=\"left\"><b>Tid</b></td>";
	echo "<td width=\"165\" align=\"left\"><b>Namn</b></td>";
	echo "<td width=\"100\" align=\"left\"><b>Svar</b></td>";
	echo "<td width=\"200\" align=\"left\"><b>Önskan</b></td>";
	echo "<td align=\"left\"><b>Motiv</b></td>";
	echo "<td width=\"200\" align=\"left\"><b>E-post</b></td>";
	// echo "<td width=\"200\" align=\"left\"><b>E-post</b></td>";
	// echo "<td width=\"100\" align=\"left\"><b>Telefon</b></td>";
	echo "</tr>";

	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
		
		$tidpunkt = date("H:i", strtotime($tomte_Datum));

		if ($desiderow == true) {
			$rowcolor = "firstrow";
		} else {
			$rowcolor = "secondrow";
		}
		
		echo "<tr>";
		echo "<td class=\"$rowcolor\" align=\"left\">$tidpunkt&nbsp;&nbsp;</td>";
		echo "<td class=\"$rowcolor\" align=\"left\">$tomte_Namn&nbsp;&nbsp;</td>";
		echo "<td class=\"$rowcolor\" align=\"left\">$tomte_Svar</td>";
		echo "<td class=\"$rowcolor\" align=\"left\">$tomte_Onskan</td>";
		echo "<td class=\"$rowcolor\" align=\"left\">$tomte_Motiv</td>";
		echo "<td class=\"$rowcolor\" align=\"left\">$tomte_Epost&nbsp;&nbsp;</td>";
		// echo "<td class=\"$rowcolor\" align=\"left\">$tomte_Telefon</td>";
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

}

?>

<?php

require_once ("CWebKollinr.php");

Class CPosten {

	function __construct() {
		
	}

	function getPostalInfo($ID) {
		global $sv, $fi, $no;

		if ($fi && !$sv) {
			$url = "http://server.logistik.posten.se/servlet/PacTrack?lang=FI&kolliid=" . $ID;
		} elseif ($no) {
			$url = "http://server.logistik.posten.se/servlet/PacTrack?lang=NO&kolliid=" . $ID;
		} else {
			$url = "http://server.logistik.posten.se/servlet/PacTrack?lang=SE&kolliid=" . $ID;
		}
		$xml = simplexml_load_file($url);
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			print_r($xml);
		}

		$from=$xml->body->parcel[0]->customername;
		$to=$xml->body->parcel[0]->receivercity;
		$kollinummer=$xml->body->parcel[0][id];
		$status=$xml->body->parcel[0]->statusdescription;
		$receiverzipcode=$xml->body->parcel[0]->receiverzipcode;
		$datesent=$xml->body->parcel[0]->datesent;
		$datesent= date("Y-m-d",strtotime($datesent));
		
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td>" . l('Package ID') . ": </td>";
		echo "<td><b>" . $kollinummer . "</b><td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td>" . l('From') . ": </td>";
		echo "<td>" . $from . "</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td>" . l('To') . ": </td>";
		echo "<td>" . $to . " (" . $receiverzipcode . ")</td>\n";
		echo "\t</tr>\n";
		if ($xml->body->parcel[0]->datesent != "") {
			echo "\t<tr>\n";
			echo "\t\t<td>" . l('Sent') . ": </td>";
			if ($fi) {
				echo "<td>" . date("d-m-Y",strtotime($datesent)) . "</td>\n";
			} else {
				echo "<td>" . date("Y-m-d",strtotime($datesent)) . "</td>\n";
			}
			echo "\t</tr>\n";
		}
		echo "\t<tr>\n";
		echo "<td colspan=\"2\"><hr noshade color=\"#cccccc\" align=\"left\" size=\"1\"></td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		// echo "<td colspan=\"2\"><b><i>" . l('Events') . "</i></b></td>\n";
		echo "<td colspan=\"2\"><b><i>" . l('Click for info') . "</i></b></td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "<td colspan=\"2\">" . CWebKollinr::getKollinr($kollinummer) . "</td>\n";
		echo "\t</tr>\n";
		
		/*
		foreach($xml->body->parcel->event as $event) {

			echo "\t<tr>\n";
			if ($fi) {
				echo "<td colspan=\"2\">" . date("d-m-Y",strtotime($event->date)) . " " . date("H:i",strtotime($event->time)) . "</td>\n";
			} else {
				echo "<td colspan=\"2\">" . date("Y-m-d",strtotime($event->date)) . " " . date("H:i",strtotime($event->time)) . "</td>\n";
			}
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "<td colspan=\"2\">" . $event->location . ", " . $event->description . "</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "<td colspan=\"2\"><hr noshade color=\"#cccccc\" align=\"left\" size=\"1\"></td>\n";
			echo "\t</tr>\n";
		
		}
		*/

		echo "</table>\n";

	}

}

?>

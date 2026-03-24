<?php

/*

PHP login object
author		Stefan Sjöberg
version		1.0 2011-12-19

*/

Class CWebKollinr {


	function __construct() {

	}

	static function getKollinr($ordernr) {
		global $sv, $fi, $no;
		
		unset($output);

		$select = "SELECT pa.trackinginfo, pa.shipdate ";
		$select .= "FROM m_package pa ";
		$select .= "JOIN m_inout io ON io.m_inout_id = pa.m_inout_id ";
		$select .= "JOIN c_order o ON o.c_order_id = io.c_order_id ";
		if (preg_match("/cms_search_parcel\.php/i", $_SERVER['PHP_SELF'])) {
			$select .= "WHERE pa.trackinginfo = '$ordernr' ";
		} else {
			$select .= "WHERE o.documentno = '$ordernr' ";
		}
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_object($res);
		// echo "antal: " . pg_num_rows($res);

			if ($res && pg_num_rows($res) > 0) {
				
				if ($res && pg_num_rows($res) > 1) {
					$newline = "<br>";
				} else {
					$newline = "";
				}
			
				 while ($res && $row = pg_fetch_row($res)) {
				 
					if ($fi && !$sv) {
						// $output .=  "<a class=\"mark_blue kollipostlink\" href=\"javascript:winPopupCenter(550, 700, 'http://server.logistik.posten.se/servlet/PacTrack?kolliid=$row[0]&xslURL=/xsl/pactrack/standard.xsl&lang=FI&cssURL=http://www.cyberphoto.fi/css/pacsoft.css');\">" . $row[0] . "</a>" . $newline;
						$output .= "<a class=\"mark_blue kollipostlink\" href=\"javascript:winPopupCenter(600, 650, 'http://www.cyberphoto.fi/kundvagn/etsi-kollinumero?searchId=$row[0]');\">" . $row[0] . "</a>" . $newline;
					} elseif ($no) {
						// $output .= "<a class=\"mark_blue kollipostlink\" href=\"javascript:winPopupCenter(550, 700, 'http://server.logistik.posten.se/servlet/PacTrack?kolliid=$row[0]&xslURL=/xsl/pactrack/standard.xsl&lang=NO&cssURL=http://www.cyberphoto.no/css/pacsoft.css');\">" . $row[0] . "</a>" . $newline;
						$output .= "<a class=\"mark_blue kollipostlink\" href=\"javascript:winPopupCenter(600, 650, 'http://www.cyberphoto.no/kundvagn/sok-kollinummer?searchId=$row[0]');\">" . $row[0] . "</a>" . $newline;
						$day_sent = date("Y-m-d 17:00:00", strtotime($row[1]));
						// echo $day_sent;
						$time_sent = strtotime($day_sent);
						$time_now = time();
						$time_diff = $time_now - $time_sent;
						if ($time_diff < 216000) { // om det gått mindre än 2,5 dygn från vi skickat paketet lägger vi till denna info
							$output .= "<br><span class=\"italic\"><b>Vær oppmerksom!</b><br>Pakken er i transitt til tollstasjonen i Norge. Dette tar vanligvis ca 2,5 dager. Inntil da er det ingen sporingsinformasjon tilgjengelig.</span>" . $newline;
						}
						
					} else {
						// $output .= "<a class=\"mark_blue kollipostlink\" href=\"javascript:winPopupCenter(550, 700, 'http://server.logistik.posten.se/servlet/PacTrack?kolliid=$row[0]&xslURL=/xsl/pactrack/standard.xsl&lang=SE&cssURL=http://www.cyberphoto.se/css/pacsoft.css');\">" . $row[0] . "</a>" . $newline;
						$output .= "<a class=\"mark_blue kollipostlink\" href=\"javascript:winPopupCenter(600, 650, 'https://www2.cyberphoto.se/kundvagn/sok-kollinummer?searchId=$row[0]');\">" . $row[0] . "</a>" . $newline;
					}
				
				}
				
			} else {
			
				if ($fi && !$sv) {
					$output .= "<i>Kollinumero puuttuu</i>";
				} elseif ($no) {
					$output .= "<i>Kollinummer mangler</i>";
				} else {
					$output .= "<i>Kollinummer saknas</i>";
				}
			
			}
			
		return $output;

	}


}
?>

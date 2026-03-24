<?php
require_once("CCheckIpNumber.php");

Class CDevSjabo extends CCheckIP {
	var $conn_ms; var $conn_my;

function __construct() {

	$this->conn_my = Db::getConnection();
	$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
	@mssql_select_db ("cyberphoto", $this->conn_ms);

}

	function mostHushallFront() {
		global $fi, $sv;

		$countrow = 0;

		echo "<div align=\"center\">";
		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" width=\"135\" height=\"358\">";
		
		$select  = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.utpris, Tillverkare.tillverkare, ";
		$select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi ";
		$select .= "FROM Artiklar ";
		$select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
		$select .= "LEFT JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
		$select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
		$select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
		// $select .= "WHERE Kategori.kategori_id_parent IN(1000082,1000083,1000088,1000100,1000101) ";
		$select .= "WHERE Artiklar.artnr = 'TKA8011' ";
		$select .= "OR Artiklar.artnr = '033561' ";
		$select .= "OR Artiklar.artnr = 'TAS2001' ";
		$select .= "OR Artiklar.artnr = 'TWK8613' ";
		$select .= "OR Artiklar.artnr = '657217' ";
		if ($fi)
			$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
		else
			$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
		$select .= " ORDER BY antalManad1 DESC";	
		$select .= " LIMIT 5 ";	

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($select);

		while ($row = mysqli_fetch_array($res)) {
			$int += 1;
			extract ($row);
			
			if ($fi && !$sv) {
				if ($tillverkare != ".") {
					$showtitle = $tillverkare . " " . $beskrivning;
				} else {
					$showtitle = $beskrivning;
				}
			} else {
				if ($tillverkare != ".") {
					$showtitle = $tillverkare . " " . $beskrivning;
				} else {
					$showtitle = $beskrivning;
				}
			}
			if ($fi) {
				$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ');
			} else {
				$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ');
			}

			echo "<tr>";
			if ($countrow > 0) {
				if ($fi && !$sv) {
					echo "<td class=\"mostsoldtdleft\"><a title=\"$showtitle\" href=\"/info_fi.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
					echo "<td class=\"mostsoldtd\"><span class=\"mostsoldprice\">$utprismoms EUR</td>\n";
				} else {
					echo "<td class=\"mostsoldtdleft\"><a title=\"$showtitle\" href=\"/info.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
					echo "<td class=\"mostsoldtd\"><span class=\"mostsoldprice\">$utprismoms kr</td>\n";
				}
			} else {
				if ($fi && !$sv) {
					echo "<td class=\"mostsoldtdleft_no\"><a title=\"$showtitle\" href=\"/info_fi.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
					echo "<td class=\"mostsoldtdright_no\"><span class=\"mostsoldprice\">$utprismoms EUR</td>\n";
				} else {
					echo "<td class=\"mostsoldtdleft_no\"><a title=\"$showtitle\" href=\"/info.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
					echo "<td class=\"mostsoldtdright_no\"><span class=\"mostsoldprice\">$utprismoms kr</span></td>\n";
				}
			}
			echo "</tr>";
			
			$countrow ++;
		}
		echo "</table>";
		echo "</div>";
	}

}

?>

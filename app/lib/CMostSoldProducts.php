<?php

// include("connections.php");

Class CMostSoldProducts {
	var $conn_my; 

	function __construct() {
		global $fi, $sv, $no;
			
		/*
		$this->conn_my = Db::getConnection();
		*/

	}

	function mostSold($kategorier_id, $limit) {
		global $fi, $sv, $no;

			echo "<div class=\"container_white\">";

			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">";
			echo "<tr>";
			if ($fi && !$sv) {
				echo "<td></td><td class=\"toplistheading\">Myydyimmät, kuukauden aikana</td>";
			} else {
				echo "<td></td><td class=\"toplistheading\">Mest sålda senaste månaden</td>";
			}
			echo "</tr>";
			
			$select  = "SELECT mostSoldArticles.artnr, mostSoldArticles.antalManad1, Artiklar.link, Artiklar.beskrivning, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, ";
			$select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi ";
			$select .= "FROM Artiklar ";
			$select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
			$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
			$select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
			$select .= "WHERE Artiklar.kategori_id IN ($kategorier_id) ";

			if ($fi)
				$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
			else
				$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
			
			if ($fi) {
				include ("std_instore_special_fi.php");
				$select .= "AND ej_med = 0 AND ej_med_fi = 0 AND demo = 0 ";
				$select .= $criteria;
			} elseif ($no) {
				include ("std_instore_special_no.php");
				$select .= "AND ej_med = 0 AND ej_med_no=0 AND demo = 0 ";
				$select .= $criteria;
			}

			$select .= " ORDER BY antalManad1 DESC";	
			
			$select .= " LIMIT " . $limit;	
			
			//$select .= "AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND Artiklar.ej_med=0 ORDER BY antal DESC";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}
			$res = mysqli_query(Db::getConnection(), $select);

			while ($row = mysqli_fetch_array($res)) {
				$int += 1;
				extract ($row);
				if ($fi && !$sv) {
					
					if ($beskrivning_fi != "")
						$beskrivning = $beskrivning_fi;
				}
				
				$link = "/info.php?article=".$artnr;

				if ($bild == "") {

					if ($fi && !$sv) {
						$bild = "../../../pic/noimage_fi.jpg";
					} else {
						$bild = "../../../pic/noimage.jpg";
					}
				}
				
				$beskrivning = htmlspecialchars($beskrivning);
				$trimmaheader = $tillverkare . " " . $beskrivning;
				
				if (strlen($trimmaheader) >= 38)
					$trimmaheader = substr ($trimmaheader, 0, 38) . "...";

				echo "<tr>";
				echo "<td width=\"15\" valign=\"top\" align=\"left\">" . $int . ".</td>\n";
				
				if ($fi && !$sv) {
				echo "<td><a onmouseover=\"return escape('<b>". $tillverkare . " ". $beskrivning . "</b><br><br>&nbsp;&nbsp;&nbsp;&nbsp;<img src=\'/thumbs/large/bilder/". $bild . "\'><br><br><i>Klikkaamalla linkkiä saat lisätietoja.&nbsp;')\" href=\"" . $link . "\">"  . $trimmaheader . "</a></td>\n";
				} else {
				echo "<td><a onmouseover=\"return escape('<b>". $tillverkare . " ". $beskrivning . "</b><br><br>&nbsp;&nbsp;&nbsp;&nbsp;<img src=\'/thumbs/large/bilder/". $bild . "\'><br><br><i>Klicka på länken för mer information.&nbsp;')\" href=\"" . $link . "\">"  . $trimmaheader . "</a></td>\n";
				}
				echo "</tr>";

			}

		echo "</table>";
		echo "</div>";
		
	}

}
?>

<?php

include("connections.php");
require_once("CCheckIpNumber.php");


Class CSoldArticles {

	var $conn_my; 
	var $conn_my2; 
	var $conn_fi;

	function __construct() {

		global $fi;

		$this->conn_my = Db::getConnection();

		$this->conn_my2 = Db::getConnectionDb('cyberadmin');

	}

	function getKategori() {

		global $kategori_nr, $category;
		
		if ($kategori_nr == "") {
			$kategori_nr = $category;
		}

		// $select  = "SELECT kategori, kategori_id FROM Kategori WHERE visas = -1 ORDER BY kategori ";
		$select  = "SELECT kategori, kategori_id FROM Kategori WHERE visas = -1 ORDER BY kategori ";

		$res = mysqli_query($this->conn_my, $select);

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			echo "<option value=\"$kategori_id\"";
				
			if ($kategori_nr == $kategori_id) {
				echo " selected";
			}
				
			echo ">" . $kategori . "(" . $kategori_id . ")</option>\n";

			}

	}

	function displaySoldArticles($kategorier_id,$alltimehigh) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
		global $fi, $sv;
		// $limit = 100;
		$rowcolor = true;
		$countprod = 1;
		$totsalda = 0;
		
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">";
		echo "<tr>";
		echo "<td width=\"30\" align=\"center\">&nbsp;</td>";
		echo "<td width=\"350\" align=\"left\"><b><u>Produkt</u></b></td>";
		echo "<td width=\"30\" align=\"center\"><b><u>Antal</b></u></td>";
		echo "</tr>";

		if ($alltimehigh == "yes") {
			$select  = "SELECT mostSoldArticles.artnr, mostSoldArticles.antal AS antalManad1, Artiklar.link, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, Moms.momssats ";
		} else {
			$select  = "SELECT mostSoldArticles.artnr, mostSoldArticles.antalManad1, Artiklar.link, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, Moms.momssats ";
		}
		$select .= "FROM Artiklar ";
		$select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
		$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
		$select .= " WHERE Artiklar.kategori_id IN ($kategorier_id) ";
		if ($alltimehigh == "yes") {
			// $select .= " AND NOT (Artiklar.demo = -1 OR ej_med = -1) AND mostSoldArticles.antal > 0 ";
			$select .= " AND NOT Artiklar.demo = -1 AND mostSoldArticles.antal > 0 ";
		} else {
			// $select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) AND mostSoldArticles.antalManad1 > 0 ";
			$select .= " AND NOT Artiklar.demo = -1 AND mostSoldArticles.antalManad1 > 0 ";
		}
		$select .= " ORDER BY antalManad1 DESC";
		if ($alltimehigh == "yes") {
			$select .= " LIMIT 200";
		}

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($this->conn_my, $select);
		
		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)) {
				extract ($row);

				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}
				
				if ($tillverkare != ".") {
					$produktbeskrivning = $tillverkare ." " . $beskrivning;
				} else {
					$produktbeskrivning = $beskrivning;
				}

				if ($link != "")
					$link = $link;
				else
					// $link = "/info.php?article=".$artnr;
					$link = "https://www2.cyberphoto.se/info.php?article=".$artnr;

				if ($bild == "") {

					if ($fi && !$sv) {
						$bild = "/pic/noimage_fi.jpg";
					} else {
						$bild = "/pic/noimage.jpg";
					}
				}

				$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;kr";
				
					echo "<tr>";
					echo "<td class=\"$backcolor\" align=\"center\">" . $countprod . "</td>";
					echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"" . $link . "\">" . $produktbeskrivning . "</a></td>";
					echo "<td class=\"$backcolor\" align=\"center\">" . $antalManad1 . "</td>";
					echo "</tr>";
				$countprod++;
				$totsalda += $antalManad1;
				if ($rowcolor == true) {
					$row = true;
					$rowcolor = false;
				} else {
					$row = false;
					$rowcolor = true;
				}
			}
			echo "<tr>";
			echo "<td align=\"center\">&nbsp;</td>";
			echo "<td align=\"left\">&nbsp;</td>";
			echo "<td align=\"center\"><b><u>" . $totsalda . "</b></u></td>";
			echo "</tr>";

		} else {

			echo "<tr>";
			echo "<td width=\"30\" align=\"center\">&nbsp;</td>";
			echo "<td colspan=\"2\" align=\"left\"><b><i>Inga produkter sålda i denna kategori senaste månaden</i></b></td>";
			echo "</tr>";
		
		}
		
		echo "</table>";
	}

	function displaySoldArticlesCategories() {
		global $fi, $sv;
		// $limit = 100;
		$rowcolor = true;
		$countprod = 1;
		$totsalda = 0;
		
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">";
		echo "<tr>";
		echo "<td width=\"30\" align=\"center\">&nbsp;</td>";
		echo "<td width=\"350\" align=\"left\"><b><u>Kategori</u></b></td>";
		echo "<td width=\"30\" align=\"center\"><b><u>Antal</b></u></td>";
		echo "</tr>";

		$select  = "SELECT Kategori.kategori, Kategori.kategori_id, SUM(mostSoldArticles.antalManad1) AS Antal ";
		$select .= "FROM Artiklar ";
		$select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
		$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "INNER JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
		$select .= "WHERE NOT Artiklar.demo = -1 AND mostSoldArticles.antalManad1 > 0 ";
		$select .= "GROUP BY Kategori.kategori, Kategori.kategori_id ";
		$select .= "ORDER BY Antal DESC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($this->conn_my, $select);
		
		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)) {
				extract ($row);

				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}
				
				if ($tillverkare != ".") {
					$produktbeskrivning = $tillverkare ." " . $beskrivning;
				} else {
					$produktbeskrivning = $beskrivning;
				}

				if ($link != "")
					$link = $link;
				else
					$link = "/?info.php?article=".$artnr;

				if ($bild == "") {

					if ($fi && !$sv) {
						$bild = "/pic/noimage_fi.jpg";
					} else {
						$bild = "/pic/noimage.jpg";
					}
				}

				$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;kr";
				
					echo "<tr>";
					echo "<td class=\"$backcolor\" align=\"center\">" . $countprod . "</td>";
					echo "<td class=\"$backcolor\" align=\"left\"><a href=\"mestsalda.php?kategori_nr=" . $kategori_id . "\">" . $kategori . "</a></td>";
					echo "<td class=\"$backcolor\" align=\"center\">" . $Antal . "</td>";
					echo "</tr>";
				$countprod++;
				$totsalda += $Antal;
				if ($rowcolor == true) {
					$row = true;
					$rowcolor = false;
				} else {
					$row = false;
					$rowcolor = true;
				}
			}
			echo "<tr>";
			echo "<td align=\"center\">&nbsp;</td>";
			echo "<td align=\"left\">&nbsp;</td>";
			echo "<td align=\"center\"><b><u>" . $totsalda . "</b></u></td>";
			echo "</tr>";

		} else {

			echo "<tr>";
			echo "<td width=\"30\" align=\"center\">&nbsp;</td>";
			echo "<td colspan=\"2\" align=\"left\"><b><i>Inga produkter sålda i denna kategori senaste månaden</i></b></td>";
			echo "</tr>";
		
		}
		
		echo "</table>";
	}

}
?>

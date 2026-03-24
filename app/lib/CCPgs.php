<?php

// include("connections.php");
require_once("CCheckIpNumber.php");
require_once("Db.php");


Class CCPgs {
	
	/*
	var $conn_my; 
	*/

	function __construct() {

		/*
		global $fi;
			
		$this->conn_my = Db::getConnection();
		*/
		
	}

	function getResultSearch($q,$o = null) {
		
		$rowcolor = true;

		echo "<div align=\"center\">\n";
		echo "<center>\n";
		echo "<table class=\"searchtable\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"80%\">\n";
		
		$searchwords = preg_split("/[\s]+/", $q);

		$select = "SELECT Artiklar.artnr, Artiklar.lagersaldo ";
		$select .= "FROM cyberphoto.Artiklar ";
		$select .= "LEFT JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		// $select .= "LEFT JOIN Moms on Artiklar.momskod = Moms.moms_id ";
		$select .= "LEFT JOIN cyberphoto.Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
		$select .= "WHERE ";

		$select .= "( ";
		$select .= "( ";

		for ($i = 0; $i < count($searchwords);$i++) {

				if ($i == 0) {
					$select .= "kategori like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
				} else {
					$select .= "AND (kategori like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
				}
		}
		
		$select .= "OR artnr = '" . $q . "') " ;

		// $select .= ") ";

		$select .= "AND ej_med=0 AND (utgangen=0 OR lagersaldo > 0) AND (demo=0 OR lagersaldo > 0) " ;
		$select .= "AND IsSalesBundle = 0 " ;

		// $select .= "ORDER BY Artiklar.artnr ASC ";
		$select .= "ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";

		$select .= "LIMIT 50 ";

		// echo $select;
		
		// $res = mysqli_query($this->conn_my, $select);
		$res = mysqli_query(Db::getConnection(), $select);
		
		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_object($res)) {
			

			if ($rowcolor) {
				$backcolor = "line1";
			} else {
				$backcolor = "line2";
			}
			
			echo "<tr>";
			echo "<td class=\"$backcolor table_artnr\">" . $row->artnr . "</td>\n";
			echo "<td class=\"$backcolor table_lagersaldo\">" . $row->lagersaldo . "</td>\n";
			echo "</tr>";

			if ($rowcolor) {
				$rowcolor = false;
			} else {
				$rowcolor = true;
			}

			}


		} else {
		
		echo "<tr>\n";
		echo "<td class=\"line1 table_lagersaldo\">Tyvärr gav sökningen inga träffar</td>\n";
		echo "</tr>\n";

		}
		
		echo "</table>\n";
		echo "</center>\n";
		echo "</div>\n";
		
	}

	function getResultKategory($KatID) {
		
		$rowcolor = true;

		echo "<div align=\"center\">\n";
		echo "<center>\n";
		echo "<table class=\"searchtable\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"80%\">\n";
		
		$select  = "SELECT SUM(lagersaldo) AS Antal ";
		$select .= "FROM cyberphoto.Artiklar ";
		$select .= "WHERE lagersaldo > 0 AND demo = 0 ";
		$select .= "AND kategori_id IN ('" . $KatID . "') ";

		// echo $select;
		
		$res = mysqli_query(Db::getConnection(), $select);
		
		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_object($res)) {
			

			if ($rowcolor) {
				$backcolor = "line_cat";
			} else {
				$backcolor = "line1";
			}
			
			echo "<tr>";
			echo "<td class=\"$backcolor table_lagersaldo\">" . $row->Antal . "</td>\n";
			echo "</tr>";

			if ($rowcolor) {
				$rowcolor = false;
			} else {
				$rowcolor = true;
			}

			}


		} else {
		
			echo "<tr>\n";
			echo "<td class=\"line1 table_lagersaldo\">Tyvärr gav sökningen inga träffar</td>\n";
			echo "</tr>\n";

		}
		
		echo "</table>\n";
		echo "</center>\n";
		echo "</div>\n";
		
	}
	
}
?>

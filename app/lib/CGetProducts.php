<?php

// include("connections.php");

Class CGetProducts {
	var $conn_my; 

	function __construct() {
		global $fi;

		$this->conn_my = Db::getConnection();
	}

	function getPrinters($tillv) {

		$select = "SELECT DISTINCT Tillverkare.tillverkare, Artiklar.tillverkar_id ";
		$select .= "FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE Artiklar.kategori_id IN(2,215,236,414,415,449,450,451,706) AND ej_med = 0 ";
		// ej olympus (9)
		$select .= " AND Artiklar.tillverkar_id != 9 ";
		$select .= "GROUP BY Tillverkare.tillverkare, Artiklar.tillverkar_id ";
		$select .= "ORDER BY Tillverkare.tillverkare ASC ";

		$res = mysqli_query($select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		echo "\t<option value=\"$tillverkar_id\"";

		if ($tillv == $tillverkar_id) {
			echo " selected";
		}
			
		echo ">" . $tillverkare . "</option>\n";
			
		}

	}

	function getPrinterModell($tillv,$modell) {

		$select = "SELECT CONCAT(Tillverkare.tillverkare,' ',Artiklar.beskrivning) AS PrinterModell, Artiklar.artnr ";
		$select .= "FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE Artiklar.kategori_id IN (2,23,215,236,414,415,449,450,451) AND ej_med = 0 AND demo != -1 AND Artiklar.tillverkar_id = '" . $tillv . "' ";
		$select .= "ORDER BY Artiklar.beskrivning ASC ";
		
		// echo $select;

		$res = mysqli_query($select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		echo "\t<option value=\"$artnr\"";
			
		if ($modell == $artnr) {
			echo " selected";
		}

		echo ">" . $PrinterModell . "</option>\n";
			
		

		}

	}

	function getSamsungMobile($modell) {

		$select = "SELECT CONCAT(Tillverkare.tillverkare,' ',Artiklar.beskrivning) AS PrinterModell, Artiklar.artnr ";
		$select .= "FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE Artiklar.kategori_id IN (336) AND Artiklar.tillverkar_id = 29 ";
		$select .= "AND ej_med = 0 AND demo != -1 AND (utgangen=0 OR lagersaldo > 0) ";
		$select .= "ORDER BY Artiklar.beskrivning ASC ";
		
		// echo $select;

		$res = mysqli_query($select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		echo "\t<option value=\"$artnr\"";
			
		if ($modell == $artnr) {
			echo " selected";
		}

		echo ">" . $PrinterModell . "</option>\n";
			
		

		}

	}

	function getSamsungTablet($modell) {

		$select = "SELECT CONCAT(Tillverkare.tillverkare,' ',Artiklar.beskrivning) AS PrinterModell, Artiklar.artnr ";
		$select .= "FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE Artiklar.kategori_id IN (748) AND Artiklar.tillverkar_id = 29 ";
		$select .= "AND ej_med = 0 AND demo != -1 AND (utgangen=0 OR lagersaldo > 0) ";
		$select .= "ORDER BY Artiklar.beskrivning ASC ";
		
		// echo $select;

		$res = mysqli_query($select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		echo "\t<option value=\"$artnr\"";
			
		if ($modell == $artnr) {
			echo " selected";
		}

		echo ">" . $PrinterModell . "</option>\n";
			
		

		}

	}
	

}
?>

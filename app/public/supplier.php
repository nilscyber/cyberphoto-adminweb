<?php 
	if (!$export) {
		include_once("top.php");
		include_once("header.php");
		
		echo "<h1>Aktuell leverantörsstatus</h1>\n";
		include_once("filter_jonas.php");
		if ($supID != "") {
			echo "<div>\n";
			$adintern->displaySuplierValueDetail($supID);
			echo "</div>\n";
			// include("supplier_excel.php");
		} else {
			echo "<div>\n";
			$adintern->displaySuplierValue();		
			echo "</div>\n";
		}

		include_once("footer.php");

	} else {
		include_once("top.php");
		$adintern->displaySuplierValueDetail($supID);
	}
	
?>
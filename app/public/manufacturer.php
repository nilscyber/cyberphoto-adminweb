<?php 
	if (!$export) {
		include_once("top.php");
		include_once("header.php");
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

		echo "<h1>Aktuell tillverkarstatus</h1>\n";
		include_once("filter_jonas.php");
		if ($manID != "") {
			echo "<div>\n";
			$adintern->displayManufacturerValueDetail($manID);
			echo "</div>\n";
			// include("supplier_excel.php");
		} else {
			echo "<div>\n";
			$adintern->displayManufacturerValue();		
			echo "</div>\n";
		}

		include_once("footer.php");

	} else {
		include_once("top.php");
		$adintern->displaySuplierValueDetail($supID);
	}
?>
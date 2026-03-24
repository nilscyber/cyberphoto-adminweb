<?php 
	if (!$export) {
		include_once("top.php");
		include_once("header.php");
		
		echo "<h1>Aktuell lagerstatus grupperat på våra huvudkategorier</h1>\n";
		if ($catID != "") {
			$adintern->displayMainCategorysValueDetail($catID);
		} else {
			$adintern->displayMainCategorysValue();
		// include("lagerstatus_excel.php");
		}

		include_once("footer.php");

	} else {
		include_once("top.php");
		$adintern->exportCategoryToExcel($showLastInvoiced);
	}
?>
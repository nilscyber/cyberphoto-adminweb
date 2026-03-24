<?php 
	if ($export) {
		include_once("top.php");
		$adintern->exportCategoryToExcel($showLastInvoiced);
	} else {
		include_once("top.php");
		include_once("header.php");
		
		echo "<h1>Aktuell lagerstatus</h1>\n";
		include_once("filter_jonas.php");
		if ($katID != "") {
			$adintern->displayProductsValueDetail($katID);
		} elseif ($showAll) {
			$adintern->displayProductsValueDetailAll();
		} else {
			$adintern->displayProductsValue();
			include("lagerstatus_excel.php");
		}
		if ($katID != "") {
			// include("lagerstatus_excel.php");
		}

		include_once("footer.php");
	}
?>
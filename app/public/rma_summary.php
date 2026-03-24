<?php 
	include_once("top.php");
	include_once("header.php");
	
	if ($discontinued == "yes") {
		echo "<h1>RMA ärenden totalt</h1>\n";
	} else {
		echo "<h1>RMA ärenden</h1>\n";
	}
	if ($category > 0) {
		unset($article);
	}
		include("rma_choose.php");
	if ($category > 0) {
		unset($article);
		$rma->summaryRMACat($category,$discontinued);
		echo "<h2>Grupperat per tillverkare</h2>\n";
		$rma->summaryRMACatManufacturer($category,$discontinued);
	} elseif ($article != "") {
		echo "<h2>Detaljer för produkt</h2>\n";
		$rma->displayRMAProductDetail($article);
		echo "<h2>Total summering för produkt ovan</h2>\n";
		$rma->summaryRMAProduct($article);
	}
	
	include_once("footer.php");
?>
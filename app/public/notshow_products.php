<?php 
	include_once("top.php");
	include_once("header.php");
	
	if ($allproducts == "yes") {
		echo "<h1>ALLA produkter som finns i lager men visas EJ på webben</h1>\n";
	} else {
		echo "<h1>DEMO-produkter som finns i lager men visas EJ på webben</h1>\n";
	}
	include("notshow_products_choose.php");

	$intern->printNotShownWebProductsNew();
	
	include_once("footer.php");
?>
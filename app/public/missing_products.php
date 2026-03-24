<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Produkter som har kö</h1>\n";
	$intern->printMissingProductsFromAD();
	
	include_once("footer.php");
?>
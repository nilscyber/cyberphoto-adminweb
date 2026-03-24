<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Produkter som ligger på lager - Butiken</h1>\n";
	$intern->printProductsADOffice(true);
	
	include_once("footer.php");
?>
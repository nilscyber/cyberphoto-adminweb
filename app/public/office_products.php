<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Produkter som ligger på lager - Kontor</h1>\n";
	$intern->printProductsADOffice();
	
	include_once("footer.php");
?>
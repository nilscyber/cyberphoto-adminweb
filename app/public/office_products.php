<?php 
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	echo "<h1>Produkter som ligger på lager - Kontor</h1>\n";
	$intern->printProductsADOffice();
	
	include_once("footer.php");
?>
<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Produkter med negativ marginal</h1>\n";
	$adintern->displayNegativeProducts();
	
	include_once("footer.php");
?>
<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Namnjämförelse över produkter upplagda de senaste två månaderna</h1>\n";
	$product->listLastAddedProducts();
	
	include_once("footer.php");
?>
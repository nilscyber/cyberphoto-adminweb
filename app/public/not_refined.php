<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>EJ förädlade produkter</h1>\n";
	echo "<div class='top10'>";
	$adminstat->listNotRefinedProducts();
	echo "</div>\n";
	
	include_once("footer.php");
?>
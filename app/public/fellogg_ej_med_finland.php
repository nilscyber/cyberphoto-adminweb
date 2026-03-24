<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Produkter som Ej visas i Finland</h1>\n";
	echo "<div class='top10'>";
	$check->notInFinland();
	echo "</div>\n";
	
	echo "<h1>Produkter som Ej visas i Norge</h1>\n";
	echo "<div class='top10'>";
	$check->notInFinland(true);
	echo "</div>\n";
	
	include_once("footer.php");
?>
<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Sålda de senaste 30 dagarna (kategorier)</h1>\n";
	$sold->displaySoldArticlesCategories();
	
	include_once("footer.php");
?>
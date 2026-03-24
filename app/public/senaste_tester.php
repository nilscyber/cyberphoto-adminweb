<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Senaste tester</h1>\n";
	
	echo "<div class=top10>";
	$blogg->getLatestProductBlogg("1,5,9,21,25");
	echo "</div>\n";
	
	include_once("footer.php");
?>
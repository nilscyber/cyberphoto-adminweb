<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Senaste nyheterna</h1>\n";
	
	echo "<div class=top10>";
	$blogg->getLatestProductBlogg("2,6,10,22,26");
	echo "</div>\n";
	
	include_once("footer.php");
?>
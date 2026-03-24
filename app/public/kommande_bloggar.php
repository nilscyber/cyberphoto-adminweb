<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Kommande bloggar</h1>";
	$blogg->getUpCommingBlogg();
	
	include_once("footer.php");
?>
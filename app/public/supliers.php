<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Lista över aktuella leverantörer</h1>\n";
	$adintern->displaySupliers();
	
	include_once("footer.php");
?>
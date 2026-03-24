<?php 
	include_once("top.php");
	include_once("header.php");
	
	// echo "<h1>Sista beställningstid från oss</h1>\n";
	$butiken->getTurnoverButiken();
	// $butiken->getTurnoverButiken(true);
	echo "<p></p>\n";
	$butiken->getPlingButiken();

	include_once("footer.php");
?>
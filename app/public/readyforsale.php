<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Begagnade produkter som är klara att säljas</h1>\n";
	
	$tradein->getReadyToSell();

	include_once("footer.php");
?>
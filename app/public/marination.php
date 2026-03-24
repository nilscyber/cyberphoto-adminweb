<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Begagnade produkter som marinerar just nu</h1>\n";
	
	$tradein->getMarination();

	include_once("footer.php");
?>
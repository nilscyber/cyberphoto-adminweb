<?php 
	include_once("top.php");
	include_once("header.php");
	
	// echo "<h1>Sista beställningstid från oss</h1>\n";
	$tradein->findDoubleTradeInBooking();
	$tradein->bookedNotShipped();
	$tradein->findReadyForSale(true); // skickade på reparation
	$tradein->findMissingPersnr(); // inget personummer med space invaders

	include_once("footer.php");
?>
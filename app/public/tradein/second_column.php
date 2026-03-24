<?php
session_start();
require_once("CTradeIn.php");
require_once("CAdminStat.php");
$tradein = new CTradeIn();
$adminstat = new CAdminStat();
$number = date('i');
if ($number % 2 == 0) {
	$tradein->findDoubleTradeInBooking();
	$tradein->lastBookedTradeIn();
	// $tradein->findReadyForSale(false);
	$tradein->findReadyForSale(true); // skickade på reparation
	// $tradein->getTradeInWishlist(false); // önsklista
	$tradein->findMissingPersnr(); // inget personummer med space invaders
	
	$adminstat->listNewProducts(true);
} else {
	$tradein->findDoubleTradeInBooking();
	$tradein->lastBookedTradeIn();
	// $tradein->findReadyForSale(false);
	$tradein->findReadyForSale(true); // skickade på reparation
	
	// $tradein->findReadyForSale(false,false,true,true); // möjliga att sälja
	
	// $tradein->getTradeInWishlist(false); // önsklista
	$tradein->findMissingPersnr(); // inget personummer med space invaders
	
	$adminstat->listNewProducts(true);
}
?>
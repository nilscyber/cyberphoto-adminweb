<?php
session_start();
date_default_timezone_set('Europe/Stockholm');
require_once("CTradeIn.php");
$tradein = new CTradeIn();
$number = date('i');
if ($number % 2 == 0) {
	// $tradein->tradeInValue(true,true);
	if (date("G", time()) < 14) {
		// echo file_get_contents("VMB.svg");
		// echo file_get_contents("Totalt.svg");
		echo file_get_contents(__DIR__ . "/../banner_images/Begagnatlogg_Diagram_chart1.svg");
	} else {
		$tradein->findDoublets(false);
		// echo file_get_contents("Begagnatlogg_Diagram_chart1.svg");
	}
} else {
	// $tradein->tradeInValue(false,true);
	$tradein->findDoublets(false);
	// echo file_get_contents("Begagnatlogg_Diagram_chart1.svg");
}

$tradein->getTimeLeft("2024-07-19 17:00:00");
?>
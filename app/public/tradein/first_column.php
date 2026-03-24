<?php
session_start();
require_once("CTradeIn.php");
$tradein = new CTradeIn();
$tradein->lastSoldTradeIn(false);
// $tradein->findMissing5555();
// $tradein->findReadyForSale(false,true);
/*
$number = date('i');
if (date("G", time()) < 10) {
	if ($number % 2 == 0) {
		echo file_get_contents("Antal.svg");
		echo file_get_contents("Omsättning.svg");
	} else {
		echo file_get_contents("Pling.svg");
	}
}
*/
?>

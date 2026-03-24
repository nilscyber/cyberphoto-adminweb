<?php
session_start();
require_once("CTradeIn.php");
$tradein = new CTradeIn();
$number = date('i');
if ($number % 2 == 0) {
	$tradein->turnAroundButiken(true);
} else {
	$tradein->getPlingButiksdorren();
	$tradein->turnAroundButiken();
}
?>

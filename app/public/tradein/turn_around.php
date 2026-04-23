<?php
session_start();
date_default_timezone_set('Europe/Stockholm');
require_once("CTradeIn.php");
$tradein = new CTradeIn();
$number = date('i');
if ($number % 2 == 0 && $tradein->checkLastPingIncomming()) {
	$tradein->getLastPingIncomming(true);
} else {
	$tradein->getTradeInValue();
}
?>

<?php
session_start();
require_once("CTradeIn.php");
require_once("CAdminStat.php");
$tradein = new CTradeIn();
$adminstat = new CAdminStat();
$number = date('i');

$adminstat->listNewProducts(true);

$adminstat->listDiscontinuedProducts(true);
?>
<!-- <div align="center"><img border="0" src="bila.gif"></div> -->
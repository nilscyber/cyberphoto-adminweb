<?php
session_start();
require_once ("CCPto.php");
$cpto = new CCPto();

if ($_GET['server'] == "yes") {
	echo $_SERVER['SERVER_ADDR'];
}
if ($_GET['store'] == "yes") {
	echo $cpto->getStoreValue(false,true);
}
if ($_GET['sales'] == "yes") {
	echo $cpto->getOutgoingOrders(false,true);
}

?>
<?php
include_once("incl_class.php");

$criteria = "WHERE ej_med=0 AND demo = 0 ";

include ("std_instore.php");

if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
	include ("PricelistSpec.php");
} else {
	include ("PricelistSpec.php");
}

?>
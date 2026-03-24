<?php

// $sv = true;
// $fi = false;

require_once("CCheckIpNumber.php");

if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {

	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98") {

		include ("CBasket_ver2.php");

	} else {

		include ("CBasket_ver2.php");

	}

} else {

	include ("CBasket_ver2.php");

}

exit;

?>
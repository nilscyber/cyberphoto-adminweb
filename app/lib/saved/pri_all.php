<?php
require_once("CCheckIpNumber.php");

if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {

	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {

		include ("pri_all_v3.php");

	} else {

		include ("pri_all_v3.php");
	}

} else {

	include ("pri_all_v3.php");

}

exit;

?>
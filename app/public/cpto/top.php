<?php
$HTTPS=$_SERVER['HTTPS']; // beh�vs f�r php5.4, $HTTPS funkar inte l�ngre
session_start();
if (false && $_SERVER['REMOTE_ADDR'] != "192.168.1.89") {
	require_once('mobile_device_detect.php');
	mobile_device_detect(true,false,true,true,true,true,true,false,'http://www.cyberphoto.se');
}

require_once ("CCPto.php");
$cpto = new CCPto();

if (!($HTTPS == "on")) {
	echo "<script>";
	echo "parent.location=\"https://www.cyberphoto.se/cpto/\"";
	echo "</script>";
exit;
}

?>
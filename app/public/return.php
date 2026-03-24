<?php
$return_rma = true;

if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
	include ("rma.php");
} else {
	include ("rma.php");
}

exit;

?>
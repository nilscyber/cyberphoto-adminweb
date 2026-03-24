<?php
// denna är den som är aktiv när vi flyttat till cyberorder (mysql)
require_once("Stomp.php");
///**
try {
	$con = new Stomp("tcp://devir.cyberphoto.se:61613");
	$res = $con->connect();	
	if (!$con->send("neworder", $newordernr, array('persistent'=>'true'))) {
		orderNotQued();
	}
	$con->disconnect();
} catch (StompException $e) {
	//echo "error caught<br>" . $e;
	orderNotQued();
}
	
function orderNotQued() {
	global $newordernr, $conn_standard;
	$update = "UPDATE cyberorder.Ordertabell SET isNotQueud=-1 WHERE ordernr = " . $newordernr;
	mysqli_query($conn_standard, $update);
	//$conn_ms;
	//echo $update;
}
//*/

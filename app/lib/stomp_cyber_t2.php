<?php

require_once("Stomp.php");

try {
	$con = new Stomp("tcp://devir.cyberphoto.se:61613");
	$res = $con->connect();	
	if (!$con->send("/queue/neworder", $newordernr, array('persistent'=>'true'))) {
		orderNotQued();
	}
	$con->disconnect();
} catch (StompException $e) {
	echo "error caught<br>" . $e;
	orderNotQued();
}
	
function orderNotQued() {
	global $newordernr, $conn_ms;
	$update = "UPDATE Ordertabell SET isNotQueud=-1 WHERE ordernr = " . $newordernr;
	//mssql_query($update, $conn_ms);
	//$conn_ms;
	//echo $update;
}

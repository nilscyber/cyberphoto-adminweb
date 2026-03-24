<?php 
	include_once("top.php");
	// include_once("header.php");
	
	$cpto->getIncommingOrders();
	$cpto->getOutgoingOrders();
	if (date('N') == 6 || date('N') == 7 || date('G') < 8 || date('G') > 18 || (date('G') == 8 && date('i') < 16)) {
		$cpto->getStoreValue(false);
	} else {
		$cpto->getStoreValue(true);
	}
	$cpto->getPrintedOrders();
	$cpto->getNotPrintedOrders();
	
	/*
	echo utf8_encode("<h1>S�kloggar</h1>\n");
	echo utf8_encode("<p>De fetmarkerade indikerar att s�kningen inte givit bes�karen n�gon tr�ff</p>\n");
	echo "<div class='top10'>";
	$csearch->getSearchLogg(1,true);
	$csearch->getSearchLogg(2,true);
	$csearch->getSearchLogg(3,true);
	*/
	echo "</div>\n";
	
	// include_once("footer.php");
	
?>

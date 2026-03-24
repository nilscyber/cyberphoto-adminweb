<?php 
	include_once("top.php");
	$manual_pagetitle = "Diverse pryttlar";
	include_once("header.php");
	
	// $sales->salesAddValue($cpto->getOutgoingOrders(true),1);
	// echo $cpto->getOutgoingOrders(true);
	// $filter->getWordsToCheck();
	// $product->listLastAddedProducts();
	echo "<h1>Sjabo diverse specialare</h1>";
	
	if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu') {
		print_r($_COOKIE);
	}
	
	echo "<h2>Logg vid orderläggning med kommentar - visar upplagda de senaste 5 dygnen</h2>";
	$filter->getIncommingComment("/kundvagn/placeOrder.php");
	/*
	if ($show == "yes") {
		$csearch->getSearchWordsGroupDetail_v1($searchstring,0);
	} else {
		$csearch->getSearchWordsGroup_v1();
	}
	*/
	
	include_once("footer.php");
?>
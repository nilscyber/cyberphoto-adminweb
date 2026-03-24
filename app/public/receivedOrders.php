<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Inkommande ordrar</h1>";
	$turnover->printReceivedOrders();
	
	include_once("footer.php");
?>
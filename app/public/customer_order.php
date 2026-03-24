<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Visa kundorder</h1>\n";

	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		include("customer_order_add_v1.php");
	} else {
		include("customer_order_add_v1.php");
	}
	
	if ($ordernr > 0 && is_numeric($ordernr)) {
		
		$orderrow = $status->getOrderinfo ($ordernr, $old);
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			include("customer_order_weblook_v4.php");
		} else {
			include("customer_order_weblook_v4.php");
		}
		if ($old != "yes") {
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				echo "<h3>Orderposter vid beställningen (innan importen till ADempiere), dessa ser kunden inte alls</h3>\n";
				$status->viewOrderLinesOriginal($ordernr);
			} else {
				echo "<h3>Orderposter vid beställningen (innan importen till ADempiere), dessa ser kunden inte alls</h3>\n";
				$status->viewOrderLinesOriginal($ordernr);
			}
			$orderi->displayInternalChatNew($ordernr);
		}
		$status->viewCustomerOrdersTotal($orderrow->kundnr);
	}
	
	include_once("footer.php");
?>
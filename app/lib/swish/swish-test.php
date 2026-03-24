<?php
include_once("incl_class.php");
require("Swish.php");
require("PaymentInterface.php");
$swish = new Swish();
$swish->finalOrdernr = 293233;
$swish->external_reference = "";
$swish->totalSum = 1;
$swish->currency = "SEK";
$swish->triggerImport();




    	$xmlMess = "<swishMsg>";
    	$xmlMess .= "\t<OrderID>" . $this->finalOrdernr . "</OrderID>\n";
    	$xmlMess .= "\t\t<Order_ID_ref>" . $this->external_reference . "</Order_ID_ref>\n";
		$xmlMess .= "\t\t<AuthCode>" . $this->external_reference . "</AuthCode>\n";
    	$xmlMess .= "\t\t<AuthAmount>" . $this->totalSum . "</AuthAmount>\n";
    	$xmlMess .= "\t\t<AuthTime>" . date("Y-m-d H:i:s") . "</AuthTime>\n";
    	$xmlMess .= "\t\t<ccName>" . "" . "</ccName>\n";
    	$xmlMess .= "\t\t<Currency>" . $this->currency . "</Currency>\n";
    	$xmlMess .= "\t</swishMsg>";
    	// $xmlMess is already UTF-8
		
echo $xmlMess;
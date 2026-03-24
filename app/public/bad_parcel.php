<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Aktiva värdepaket med negativ paketrabatt</h1>\n";
	echo "<h2>Dessa artiklar måste åtgärdas (Sverige)</h2>\n";
	$parcel->getActivePac();
	$parcel->getActivePacCheck(false,false);
	echo "<h2>Dessa artiklar måste åtgärdas (Finland)</h2>\n";
	$parcel->getActivePacFI();
	$parcel->getActivePacCheck(true,false);
	echo "<h2>Dessa artiklar måste åtgärdas (Norge)</h2>\n";
	$parcel->getActivePac(true);
	$parcel->getActivePacCheck(false,true);
	
	include_once("footer.php");
?>
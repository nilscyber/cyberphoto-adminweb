<?php 
	include_once("top.php");
	include_once("header.php");
	
	// $tradein->addNotify(time(),"SALE! SALE! SALE! SALE!",100);

	if ($possible == "")
		$possible = "all";
	
	// echo "<h1>Sista beställningstid från oss</h1>\n";
	echo "<div>\n";
	echo "<form method=\"GET\">\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($possible == "all") {
		echo "Samtliga <input type=\"radio\" name=\"possible\" value=\"all\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Samtliga <input type=\"radio\" name=\"possible\" value=\"all\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 200px;\">\n";
	if ($possible == "sale") {
		echo "Möjliga att lägga ut <input type=\"radio\" name=\"possible\" value=\"sale\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Möjliga att lägga ut <input type=\"radio\" name=\"possible\" value=\"sale\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	
	echo "</form>\n";
	echo "</div>\n";
	echo "<div class=\"clear\"></div>\n";
	echo "<div class=\"top20\"></div>\n";

	
	// echo "<h1>Sista beställningstid från oss</h1>\n";
	if ($possible == "sale") {
		$tradein->findReadyForSale(false,false,true,true);
		echo "<p></p>\n";
	} else {
		$tradein->findReadyForSale(false,false,true);
		echo "<p></p>\n";
		$tradein->findReadyForSale(true,false,true); // visa reparationer
	}

	include_once("footer.php");
?>
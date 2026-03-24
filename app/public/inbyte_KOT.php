<?php 
	include_once("top.php");
	include_once("header.php");
	if ($products == "")
		$products = "all";
	
	// echo "<h1>Sista beställningstid från oss</h1>\n";
	echo "<div>\n";
	echo "<form method=\"GET\">\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($products == "all") {
		echo "Samtliga <input type=\"radio\" name=\"products\" value=\"all\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Samtliga <input type=\"radio\" name=\"products\" value=\"all\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($products == "cameras") {
		echo "Kameror <input type=\"radio\" name=\"products\" value=\"cameras\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Kameror <input type=\"radio\" name=\"products\" value=\"cameras\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($products == "lenses") {
		echo "Objektiv <input type=\"radio\" name=\"products\" value=\"lenses\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Objektiv <input type=\"radio\" name=\"products\" value=\"lenses\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($products == "accessories") {
		echo "Tillbehör <input type=\"radio\" name=\"products\" value=\"accessories\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Tillbehör <input type=\"radio\" name=\"products\" value=\"accessories\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 140px;\">\n";
	if ($moms == "yes") {
		echo "Endast momsade <input type=\"checkbox\" name=\"moms\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Endast momsade <input type=\"checkbox\" name=\"moms\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 140px;\">\n";
	if ($show_webb == "yes") {
		echo "Endast på webb <input type=\"checkbox\" name=\"show_webb\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Endast på webb <input type=\"checkbox\" name=\"show_webb\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	
	echo "</form>\n";
	echo "</div>\n";
	echo "<div class=\"clear\"></div>\n";
	echo "<div class=\"top20\"></div>\n";

	$tradein->KOTlist(false,false,false);

	include_once("footer.php");
?>
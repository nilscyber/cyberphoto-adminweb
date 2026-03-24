<?php 
	include_once("top.php");
	include_once("header.php");
	
	/*
	echo "<div>\n";
	echo "<form method=\"GET\">\n";
	echo "<div style=\"float: left; width: 140px;\">\n";
	if ($show_webb == "yes") {
		echo "Endast ute på webb <input type=\"checkbox\" name=\"show_webb\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Endast ute på webb <input type=\"checkbox\" name=\"show_webb\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	
	echo "</form>\n";
	echo "</div>\n";
	echo "<div class=\"clear\"></div>\n";
	echo "<div class=\"top20\"></div>\n";
	*/

	if ($show_webb == "yes") {
		$tradein->findDoublets(true,true);
	} else {
		$tradein->findDoublets(true,true);
	}

	include_once("footer.php");
?>
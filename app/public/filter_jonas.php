<?php

	echo "<div>\n";
	echo "<form method=\"GET\">\n";
	echo "<input type=\"hidden\" name=\"manID\" value=\"$manID\">\n";
	/*
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($products == "new") {
		echo "Endast nya <input type=\"radio\" name=\"products\" value=\"new\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Endast nya <input type=\"radio\" name=\"products\" value=\"new\" onClick=\"submit()\">\n";
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
	*/
	echo "<div style=\"float: left; width: 90px;\">\n";
	if ($demo == "yes") {
		echo "Visa demo <input type=\"checkbox\" name=\"demo\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Visa demo <input type=\"checkbox\" name=\"demo\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 120px;\">\n";
	if ($begagnat == "yes") {
		echo "Visa begagnat <input type=\"checkbox\" name=\"begagnat\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Visa begagnat <input type=\"checkbox\" name=\"begagnat\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	
	// echo $demo;
	
	echo "</form>\n";
	echo "</div>\n";
	echo "<div class=\"clear\"></div>\n";
	echo "<div class=\"top20\"></div>\n";

?>
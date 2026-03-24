<?php
	echo "<div>\n";
	echo "<select class=\"select_frontsection\" name=\"rma_year\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
	echo "<option value=\"0\">Alla år</option>\n";
	echo "<option value=\"2011\"";
	if ($rma_year == "2011") {
		echo " selected";
	}
	echo ">2011</option>\n";
	echo "<option value=\"2012\"";
	if ($rma_year == "2012") {
		echo " selected";
	}
	echo ">2012</option>\n";
	echo "<option value=\"2013\"";
	if ($rma_year == "2013") {
		echo " selected";
	}
	echo ">2013</option>\n";
	if ($rma_year > 2013) { // förbereder för 2014
		echo "<option value=\"2014\"";
		if ($rma_year == "2014") {
			echo " selected";
		}
		echo ">2014</option>\n";
	}
	if ($rma_year > 2014) { // förbereder för 2015
		echo "<option value=\"2015\"";
		if ($rma_year == "2015") {
			echo " selected";
		}
		echo ">2015</option>\n";
	}
	echo "</select>\n";
	echo "</div>\n";
?>
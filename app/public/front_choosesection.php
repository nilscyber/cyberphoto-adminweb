<?php
	echo "<div>\n";
	
	echo "<select class=\"select_frontsection\" name=\"choose_section\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
	echo "<option value=\"0\">-- Välj sektion --</option>\n";
	$banners->getActiveSections($_SESSION['bannerdepartment']);
	/*
	echo "<option value=\"1\"";
	if ($_SESSION['bannersection'] == 1) {
		echo " selected";
	}
	echo ">Sektion 1</option>\n";
	echo "<option value=\"2\"";
	if ($_SESSION['bannersection'] == 2) {
		echo " selected";
	}
	echo ">Sektion 2</option>\n";
	echo "<option value=\"3\"";
	if ($_SESSION['bannersection'] == 3) {
		echo " selected";
	}
	echo ">Sektion 3</option>\n";
	echo "<option value=\"4\"";
	if ($_SESSION['bannersection'] == 4) {
		echo " selected";
	}
	echo ">Sektion 4</option>\n";
	echo "<option value=\"5\"";
	if ($_SESSION['bannersection'] == 5) {
		echo " selected";
	}
	echo ">Sektion 5</option>\n";
	echo "<option value=\"22\"";
	if ($_SESSION['bannersection'] == 22) {
		echo " selected";
	}
	echo ">Personligt</option>\n";
	echo "<option value=\"23\"";
	if ($_SESSION['bannersection'] == 23) {
		echo " selected";
	}
	echo ">Vänster</option>\n";
	echo "<option value=\"24\"";
	if ($_SESSION['bannersection'] == 24) {
		echo " selected";
	}
	echo ">Höger</option>\n";
	echo "<option value=\"25\"";
	if ($_SESSION['bannersection'] == 25) {
		echo " selected";
	}
	echo ">Prislistor</option>\n";
	*/
	echo "</select>\n";
	
	echo "</div>\n";
?>
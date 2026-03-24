<?php

	echo "<div>\n";
	
		echo "<select class=\"select_frontsite\" name=\"choose_department\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
		echo "<option value=\"0\">-- Välj avdelning --</option>\n";
		echo "<option value=\"1\"";
		if ($_SESSION['menudepartment'] == 1) {
			echo " selected";
		}
		echo ">Foto-video</option>\n";
		echo "<option value=\"2\"";
		if ($_SESSION['menudepartment'] == 2) {
			echo " selected";
		}
		echo ">Mobiltelefoni</option>\n";
		echo "<option value=\"3\"";
		if ($_SESSION['menudepartment'] == 3) {
			echo " selected";
		}
		echo ">Batterier</option>\n";
		echo "<option value=\"4\"";
		if ($_SESSION['menudepartment'] == 4) {
			echo " selected";
		}
		echo ">Outdoor</option>\n";
		/*
		echo "<option value=\"5\"";
		if ($_SESSION['menudepartment'] == 5) {
			echo " selected";
		}
		echo ">Cybairgun</option>\n";
		*/
		echo "</select>\n";

	echo "</div>\n";

?>
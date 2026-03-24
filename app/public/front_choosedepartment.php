<?php

	echo "<div>\n";
	
	if ($_SESSION['bannersite'] == 1) {
		echo "<select class=\"select_frontsite\" name=\"choose_department\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
		echo "<option value=\"0\">-- Välj avdelning --</option>\n";
		echo "<option value=\"1\"";
		if ($_SESSION['bannerdepartment'] == 1) {
			echo " selected";
		}
		echo ">Foto-video</option>\n";
		/*
		echo "<option value=\"2\"";
		if ($_SESSION['bannerdepartment'] == 2) {
			echo " selected";
		}
		echo ">Mobiltelefoni</option>\n";
		echo "<option value=\"3\"";
		if ($_SESSION['bannerdepartment'] == 3) {
			echo " selected";
		}
		echo ">Batterier</option>\n";
		echo "<option value=\"4\"";
		if ($_SESSION['bannerdepartment'] == 4) {
			echo " selected";
		}
		echo ">Outdoor</option>\n";
		if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nuX' || $_COOKIE['login_mail'] == 'tobias@cyberphoto.nuX') {
			echo "<option value=\"1440\"";
			if ($_SESSION['bannerdepartment'] == 1440) {
				echo " selected";
			}
			echo ">development_environment_1440</option>\n";
		}
		*/
		echo "</select>\n";
	} elseif ($_SESSION['bannersite'] == 2) {
		echo "<select class=\"select_frontsite\" name=\"choose_department\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
		echo "<option value=\"0\">-- Välj avdelning --</option>\n";
		echo "<option value=\"101\"";
		if ($_SESSION['bannerdepartment'] == 101) {
			echo " selected";
		}
		echo ">Foto-video</option>\n";
		echo "<option value=\"102\"";
		if ($_SESSION['bannerdepartment'] == 102) {
			echo " selected";
		}
		echo ">Mobiltelefoni</option>\n";
		echo "<option value=\"103\"";
		if ($_SESSION['bannerdepartment'] == 103) {
			echo " selected";
		}
		echo ">Batterier</option>\n";
		echo "<option value=\"104\"";
		if ($_SESSION['bannerdepartment'] == 104) {
			echo " selected";
		}
		echo ">Outdoor</option>\n";
		echo "</select>\n";
	} elseif ($_SESSION['bannersite'] == 3) {
		echo "<select class=\"select_frontsite\" name=\"choose_department\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
		echo "<option value=\"0\">-- Välj avdelning --</option>\n";
		echo "<option value=\"201\"";
		if ($_SESSION['bannerdepartment'] == 201) {
			echo " selected";
		}
		echo ">Foto-video</option>\n";
		echo "<option value=\"202\"";
		if ($_SESSION['bannerdepartment'] == 202) {
			echo " selected";
		}
		echo ">Mobiltelefoni</option>\n";
		echo "<option value=\"203\"";
		if ($_SESSION['bannerdepartment'] == 203) {
			echo " selected";
		}
		echo ">Batterier</option>\n";
		echo "<option value=\"204\"";
		if ($_SESSION['bannerdepartment'] == 204) {
			echo " selected";
		}
		echo ">Outdoor</option>\n";
		echo "</select>\n";
	} elseif ($_SESSION['bannersite'] == 4) {
		echo "<select class=\"select_frontsite\" name=\"choose_department\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
		echo "<option value=\"0\">-- Välj avdelning --</option>\n";
		echo "<option value=\"301\"";
		if ($_SESSION['bannerdepartment'] == 301) {
			echo " selected";
		}
		echo ">Foto-video</option>\n";
		echo "<option value=\"302\"";
		if ($_SESSION['bannerdepartment'] == 302) {
			echo " selected";
		}
		echo ">Mobiltelefoni</option>\n";
		echo "<option value=\"303\"";
		if ($_SESSION['bannerdepartment'] == 303) {
			echo " selected";
		}
		echo ">Batterier</option>\n";
		echo "<option value=\"304\"";
		if ($_SESSION['bannerdepartment'] == 304) {
			echo " selected";
		}
		echo ">Outdoor</option>\n";
		echo "</select>\n";
	}

	echo "</div>\n";

?>
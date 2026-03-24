<?php

	echo "<hr>\n";
	echo "<div id=\"sort_container\">\n";
	
	/*
	echo "<div class=\"floatleft left5\">";
	$pricelist->getManufacturerList($criteria);
	echo "</div>\n";
	*/
	
	/*
	if ($countarticles > 30 && ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_COOKIE['login_mail'] == 'mathias@cyberphoto.nu' || 
	$_COOKIE['login_mail'] == 'peder@cyberphoto.nu' || $_COOKIE['login_mail'] == 'patrick@cyberphoto.nu' ||
	$_COOKIE['login_mail'] == 'tobias@cyberphoto.nu' || $_COOKIE['login_mail'] == 'maria@cyberphoto.nu' ||
	$_COOKIE['login_mail'] == 'borje@cyberphoto.nu')) {
	
	}
	*/
	
	
	/*
	if ($show_soldarticles == "yes") {
		echo "<div class=\"floatleft left5\"><label for=\"show_soldarticles\">Sålda</label><input type=\"checkbox\" id=\"show_soldarticles\" name=\"show_soldarticles\" value=\"yes\" onclick=\"submit()\" checked></div>\n";
	} else {
		echo "<div class=\"floatleft left5\"><label for=\"show_soldarticles\">Sålda</label><input type=\"checkbox\" id=\"show_soldarticles\" name=\"show_soldarticles\" value=\"yes\" onclick=\"submit()\"></div>\n";
	}
	if ($show_queue == "yes") {
		echo "<div class=\"floatleft left20\"><label for=\"show_queue\">Kö</label><input type=\"checkbox\" id=\"show_queue\" name=\"show_queue\" value=\"yes\" onclick=\"submit()\" checked></div>\n";
	} else {
		echo "<div class=\"floatleft left20\"><label for=\"show_queue\">Kö</label><input type=\"checkbox\" id=\"show_queue\" name=\"show_queue\" value=\"yes\" onclick=\"submit()\"></div>\n";
	}
	*/
	if ($sortera == "tillverkare" || $sortera == "") {
		echo "<div class=\"floatleft left20\"><label for=\"tillverkare\">Aktuella</label><input type=\"radio\" id=\"tillverkare\" name=\"sortera\" value=\"tillverkare\" onclick=\"submit()\" checked></div>\n";
	} else {
		echo "<div class=\"floatleft left20\"><label for=\"tillverkare\">Aktuella</label><input type=\"radio\" id=\"tillverkare\" name=\"sortera\" value=\"tillverkare\" onclick=\"submit()\"></div>\n";
	}
	if ($sortera == "discontinued") {
		echo "<div class=\"floatleft left20\"><label for=\"discontinued\">Utgångna</label><input type=\"radio\" id=\"discontinued\" name=\"sortera\" value=\"discontinued\" onclick=\"submit()\" checked></div>\n";
	} else {
		echo "<div class=\"floatleft left20\"><label for=\"discontinued\">Utgångna</label><input type=\"radio\" id=\"discontinued\" name=\"sortera\" value=\"discontinued\" onclick=\"submit()\"></div>\n";
	}
	if (CCheckIP::checkIfLoginIsTradeIn($_SERVER['REMOTE_ADDR'])) {
		if ($sortera == "old_tradein") {
			echo "<div class=\"floatleft left20\"><label for=\"old_tradein\">Gamla inbyten</label><input type=\"radio\" id=\"old_tradein\" name=\"sortera\" value=\"old_tradein\" onclick=\"submit()\" checked></div>\n";
		} else {
			echo "<div class=\"floatleft left20\"><label for=\"old_tradein\">Gamla inbyten</label><input type=\"radio\" id=\"old_tradein\" name=\"sortera\" value=\"old_tradein\" onclick=\"submit()\"></div>\n";
		}
	}

	/*
	echo "<div class=\"sort_row\">\n";
	echo "<label for=\"sortera\">" . l('Sort by') . ": </label>";
	echo "<select onchange=\"this.form.submit(this.options[this.selectedIndex].value)\" name=\"sortera\" id=\"sortera\">\n";
	if ($sortera == "tillverkare" || ($sortera == "" && CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']))) {
		echo "<option value=\"tillverkare\" selected>" . l('Manufacturer') . "</option>\n";
	} else {
		echo "<option value=\"tillverkare\">" . l('Manufacturer') . "</option>\n";
	}
	if ($sortera == "utpris" || ($sortera == "" && !CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']))) {
		echo "<option value=\"utpris\" selected>" . l('Price ascending') . "</option>\n";
	} else {
		echo "<option value=\"utpris\">" . l('Price ascending') . "</option>\n";
	}
	if ($sortera == "utpris_fall") {
		echo "<option value=\"utpris_fall\" selected>" . l('Price descending') . "</option>\n";
	} else {
		echo "<option value=\"utpris_fall\">" . l('Price descending') . "</option>\n";
	}
	if ($sortera == "testresultat") {
		echo "<option value=\"testresultat\" selected>" . l('Test results') . "</option>\n";
	} else {
		echo "<option value=\"testresultat\">" . l('Test results') . "</option>\n";
	}
	if ($sortera == "instore") {
		echo "<option value=\"instore\" selected>" . l('Only in stock') . "</option>\n";
	} else {
		echo "<option value=\"instore\">" . l('Only in stock') . "</option>\n";
	}
	if (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
		echo "<option value=\"\"></option>\n";
		if ($sortera == "discontinued") {
			echo "<option value=\"discontinued\" selected>" . l('Utgångna produkter') . "</option>\n";
		} else {
			echo "<option value=\"discontinued\">" . l('Utgångna produkter') . "</option>\n";
		}
	}
	if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
		echo "<option value=\"\"></option>\n";
		if ($sortera == "instore_falling") {
			echo "<option value=\"instore_falling\" selected>" . l('I lager, fallande') . "</option>\n";
		} else {
			echo "<option value=\"instore_falling\">" . l('I lager, fallande') . "</option>\n";
		}
		if ($sortera == "notinstore") {
			echo "<option value=\"notinstore\" selected>" . l('Endast EJ i lager') . "</option>\n";
		} else {
			echo "<option value=\"notinstore\">" . l('Endast EJ i lager') . "</option>\n";
		}
		if ($sortera == "discontinued") {
			echo "<option value=\"discontinued\" selected>" . l('Utgångna produkter') . "</option>\n";
		} else {
			echo "<option value=\"discontinued\">" . l('Utgångna produkter') . "</option>\n";
		}
		if ($sortera == "notshown") {
			echo "<option value=\"notshown\" selected>" . l('Visa EJ på webb') . "</option>\n";
		} else {
			echo "<option value=\"notshown\">" . l('Visa EJ på webb') . "</option>\n";
		}
		if ($sortera == "have_parent") {
			echo "<option value=\"have_parent\" selected>" . l('Visa underliggande') . "</option>\n";
		} else {
			echo "<option value=\"have_parent\">" . l('Visa underliggande') . "</option>\n";
		}
		if (CCheckIP::checkIfLoginIsTradeIn($_SERVER['REMOTE_ADDR'])) {
			if ($sortera == "noweb_tradein") {
				echo "<option value=\"noweb_tradein\" selected>" . l('Inbyten, EJ på webb') . "</option>\n";
			} else {
				echo "<option value=\"noweb_tradein\">" . l('Inbyten, EJ på webb') . "</option>\n";
			}
		}
		if ($sortera == "old_tradein") {
			echo "<option value=\"old_tradein\" selected>" . l('Gamla inbyten') . "</option>\n";
		} else {
			echo "<option value=\"old_tradein\">" . l('Gamla inbyten') . "</option>\n";
		}
	}
	echo "</select>\n";
	echo "</div>\n";
	*/
	
	echo "<div class=\"clear\"></div>\n";
	echo "</div>\n";

	echo "</form>\n";
	echo "<hr>\n";

?>
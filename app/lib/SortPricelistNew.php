<?php
	// include_once("translate.php");
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $plist;
	}
	
	if ($plist == "plain_row") {
		$_SESSION['plain_row'] = true;
		unset($_SESSION['admin_row']);
		unset($_SESSION['gallery_row']);
	}
	if ($plist == "gallery_row") {
		$_SESSION['gallery_row'] = true;
		unset($_SESSION['admin_row']);
		unset($_SESSION['plain_row']);
	}
	if ($plist == "admin_row") {
		$_SESSION['admin_row'] = true;
		unset($_SESSION['plain_row']);
		unset($_SESSION['gallery_row']);
	}
	if ($plist == "picture_row") {
		unset($_SESSION['plain_row']);
		unset($_SESSION['admin_row']);
		unset($_SESSION['gallery_row']);
	}

	echo "<div id=\"sort_container\">\n";
	
	if (preg_match("/begagnade\-produkter/i", $_SERVER['REQUEST_URI']) || 
		preg_match("/search/i", $_SERVER['PHP_SELF']) || preg_match("/nya\-produkter/i", $_SERVER['REQUEST_URI']) || 
		preg_match("/price\_kikare/i", $_SERVER['PHP_SELF']) || preg_match("/price\_digital\_cameras/i", $_SERVER['PHP_SELF']) || 
		preg_match("/uudet\-tuotteet/i", $_SERVER['REQUEST_URI'])) {
		echo "<div class=\"filter_sort_row\">";
		$pricelist->getManufacturerList($criteria);
		echo "</div>\n";
	}
	
	if ($countarticles > 30 && (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']))) {
		if ($show_soldarticles == "yes") {
			echo "<div class=\"floatleft left5\"><label for=\"show_soldarticles\">Visa antalet sålda</label><input type=\"checkbox\" id=\"show_soldarticles\" name=\"show_soldarticles\" value=\"yes\" onclick=\"submit()\" checked></div>\n";
		} else {
			echo "<div class=\"floatleft left5\"><label for=\"show_soldarticles\">Visa antalet sålda</label><input type=\"checkbox\" id=\"show_soldarticles\" name=\"show_soldarticles\" value=\"yes\" onclick=\"submit()\"></div>\n";
		}
		if ($show_queue == "yes") {
			echo "<div class=\"floatleft left20\"><label for=\"show_queue\">Visa kösituationen</label><input type=\"checkbox\" id=\"show_queue\" name=\"show_queue\" value=\"yes\" onclick=\"submit()\" checked></div>\n";
		} else {
			echo "<div class=\"floatleft left20\"><label for=\"show_queue\">Visa kösituationen</label><input type=\"checkbox\" id=\"show_queue\" name=\"show_queue\" value=\"yes\" onclick=\"submit()\"></div>\n";
		}
	}
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
		if ($sortera == "temporarynotinstore") {
			echo "<option value=\"temporarynotinstore\" selected>" . l('Endast tillfälligt slut') . "</option>\n";
		} else {
			echo "<option value=\"temporarynotinstore\">" . l('Endast tillfälligt slut') . "</option>\n";
		}
		if ($sortera == "notplaninstore") {
			echo "<option value=\"notplaninstore\" selected>" . l('Endast beställningsvaror') . "</option>\n";
		} else {
			echo "<option value=\"notplaninstore\">" . l('Endast beställningsvaror') . "</option>\n";
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
			if ($sortera == "onlyweb_tradein") {
				echo "<option value=\"onlyweb_tradein\" selected>" . l('Inbyten, Endast ute på webb') . "</option>\n";
			} else {
				echo "<option value=\"onlyweb_tradein\">" . l('Inbyten, Endast ute på webb') . "</option>\n";
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
	
	if ($show_pricelist_type != -1) {

		if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			echo "<div class=\"plist_type\">\n";
			if ($plist == "admin_row" || $_SESSION['admin_row']) {
				echo "<img border=\"0\" src=\"/pricelist/admin_row.png\">";
			} else {
				echo "<a onclick=\"setCookie('plist','admin_row',365)\" title=\"" . l('View supercompact pricelist without buy button') . "\" href=\"" . $currentUrl . $firstvariable . "plist=admin_row\">";
				echo "<img class=\"plist_img\" border=\"0\" src=\"/pricelist/admin_row.png\"></a>";
			}
			echo "</div>\n";
		}
		
		echo "<div class=\"plist_type\">\n";
		if ($plist == "plain_row" || $_SESSION['plain_row']) {
			echo "<img border=\"0\" src=\"/pricelist/plain_row.png\">";
		} else {
			echo "<a onclick=\"setCookie('plist','plain_row',365)\" title=\"" . l('View compact list without images') . "\" href=\"" . $currentUrl . $firstvariable . "plist=plain_row\">";
			echo "<img class=\"plist_img\" border=\"0\" src=\"/pricelist/plain_row.png\"></a>";
		}
		echo "</div>\n";
		echo "<div class=\"plist_type\">\n";
		if ($plist == "" || $plist == "picture_row") {
			echo "<img border=\"0\" src=\"/pricelist/picture_row.png\">";
		} else {
			echo "<a onclick=\"setCookie('plist','picture_row',365)\" title=\"" . l('View pricelist with pictures') . "\" href=\"" . $currentUrl . $firstvariable . "plist=picture_row\">";
			echo "<img class=\"plist_img\" border=\"0\" src=\"/pricelist/picture_row.png\"></a>";
		}
		echo "</div>\n";

		echo "<div class=\"plist_type\">\n";
		if ($plist == "gallery_row" || $_SESSION['gallery_row']) {
			echo "<img border=\"0\" src=\"/pricelist/gallery_row.png\">";
		} else {
			echo "<a onclick=\"setCookie('plist','gallery_row',365)\" title=\"" . l('View pricelist in gallery mode') . "\" href=\"" . $currentUrl . $firstvariable . "plist=gallery_row\">";
			echo "<img class=\"plist_img\" border=\"0\" src=\"/pricelist/gallery_row.png\"></a>";
		}
		echo "</div>\n";
	
	}
	
	if (preg_match("/filter/i", $_SERVER['PHP_SELF'])) { // om filtersidan, då visar vi storlekar
		echo "<div class=\"sort_row right10\">\n";
		echo "<label for=\"storlek\">" . l('Filter size') . ": </label>";
		echo "<select onchange=\"this.form.submit(this.options[this.selectedIndex].value)\" name=\"storlek\" id=\"storlek\">\n";
		if ($storlek == "") {
			echo "<option value=\"\" selected>" . l('View all') . "</option>\n";
		} else {
			echo "<option value=\"\">" . l('View all') . "</option>\n";
		}
		if ($storlek == "27") {
			echo "<option value=\"27\" selected>27mm</option>\n";
		} else {
			echo "<option value=\"27\">27mm</option>\n";
		}
		if ($storlek == "28") {
			echo "<option value=\"28\" selected>28mm</option>\n";
		} else {
			echo "<option value=\"28\">28mm</option>\n";
		}
		if ($storlek == "30") {
			echo "<option value=\"30\" selected>30mm</option>\n";
		} else {
			echo "<option value=\"30\">30mm</option>\n";
		}
		if ($storlek == "30,5") {
			echo "<option value=\"30,5\" selected>30,5mm</option>\n";
		} else {
			echo "<option value=\"30,5\">30,5mm</option>\n";
		}
		if ($storlek == "37") {
			echo "<option value=\"37\" selected>37mm</option>\n";
		} else {
			echo "<option value=\"37\">37mm</option>\n";
		}
		if ($storlek == "40,5") {
			echo "<option value=\"40,5\" selected>40,5mm</option>\n";
		} else {
			echo "<option value=\"40,5\">40,5mm</option>\n";
		}
		if ($storlek == "43") {
			echo "<option value=\"43\" selected>43mm</option>\n";
		} else {
			echo "<option value=\"43\">43mm</option>\n";
		}
		if ($storlek == "46") {
			echo "<option value=\"46\" selected>46mm</option>\n";
		} else {
			echo "<option value=\"46\">46mm</option>\n";
		}
		if ($storlek == "49") {
			echo "<option value=\"49\" selected>49mm</option>\n";
		} else {
			echo "<option value=\"49\">49mm</option>\n";
		}
		if ($storlek == "52") {
			echo "<option value=\"52\" selected>52mm</option>\n";
		} else {
			echo "<option value=\"52\">52mm</option>\n";
		}
		if ($storlek == "55") {
			echo "<option value=\"55\" selected>55mm</option>\n";
		} else {
			echo "<option value=\"55\">55mm</option>\n";
		}
		if ($storlek == "58") {
			echo "<option value=\"58\" selected>58mm</option>\n";
		} else {
			echo "<option value=\"58\">58mm</option>\n";
		}
		if ($storlek == "62") {
			echo "<option value=\"62\" selected>62mm</option>\n";
		} else {
			echo "<option value=\"62\">62mm</option>\n";
		}
		if ($storlek == "67") {
			echo "<option value=\"67\" selected>67mm</option>\n";
		} else {
			echo "<option value=\"67\">67mm</option>\n";
		}
		if ($storlek == "72") {
			echo "<option value=\"72\" selected>72mm</option>\n";
		} else {
			echo "<option value=\"72\">72mm</option>\n";
		}
		if ($storlek == "77") {
			echo "<option value=\"77\" selected>77mm</option>\n";
		} else {
			echo "<option value=\"77\">77mm</option>\n";
		}
		if ($storlek == "82") {
			echo "<option value=\"82\" selected>82mm</option>\n";
		} else {
			echo "<option value=\"82\">82mm</option>\n";
		}
		if ($storlek == "86") {
			echo "<option value=\"86\" selected>86mm</option>\n";
		} else {
			echo "<option value=\"86\">86mm</option>\n";
		}
		if ($storlek == "95") {
			echo "<option value=\"95\" selected>95mm</option>\n";
		} else {
			echo "<option value=\"95\">95mm</option>\n";
		}
		if ($storlek == "105") {
			echo "<option value=\"105\" selected>105mm</option>\n";
		} else {
			echo "<option value=\"105\">105mm</option>\n";
		}
		echo "</select>\n";
		echo "</div>\n";
	}

	if ((preg_match("/minneskort/i", $_SERVER['PHP_SELF']) && ($show == 4001 ||$show == 4002 ||$show == 4004 ||$show == 4006)) || ($catID == 109 || $catID == 201 || $catID == 651 || $catID == 701 || $catID == 191 || $catID == 1000123 || $catID == 1000247 || $catID == 1000287)) { // om minneskort
		echo "<div class=\"sort_row right10\">\n";
		echo "<label for=\"storlek\">" . l('Memory size') . ": </label>";
		echo "<select class=\"mem_size\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\" name=\"storlek\" id=\"storlek\">\n";
		if ($storlek == "") {
			echo "<option value=\"\" selected>" . l('View all') . "</option>\n";
		} else {
			echo "<option value=\"\">" . l('View all') . "</option>\n";
		}
		if ($storlek == "2") {
			echo "<option value=\"2\" selected>2GB</option>\n";
		} else {
			echo "<option value=\"2\">2GB</option>\n";
		}
		if ($storlek == "8") {
			echo "<option value=\"8\" selected>8GB</option>\n";
		} else {
			echo "<option value=\"8\">8GB</option>\n";
		}
		if ($storlek == "16") {
			echo "<option value=\"16\" selected>16GB</option>\n";
		} else {
			echo "<option value=\"16\">16GB</option>\n";
		}
		if ($storlek == "32") {
			echo "<option value=\"32\" selected>32GB</option>\n";
		} else {
			echo "<option value=\"32\">32GB</option>\n";
		}
		if ($storlek == "64") {
			echo "<option value=\"64\" selected>64GB</option>\n";
		} else {
			echo "<option value=\"64\">64GB</option>\n";
		}
		if ($storlek == "128") {
			echo "<option value=\"128\" selected>128GB</option>\n";
		} else {
			echo "<option value=\"128\">128GB</option>\n";
		}
		if ($storlek == "200") {
			echo "<option value=\"200\" selected>200GB</option>\n";
		} else {
			echo "<option value=\"200\">200GB</option>\n";
		}
		if ($storlek == "256") {
			echo "<option value=\"256\" selected>256GB</option>\n";
		} else {
			echo "<option value=\"256\">256GB</option>\n";
		}
		echo "</select>\n";
		echo "</div>\n";
	}
	
	echo "<div class=\"clear\"></div>\n";
	echo "</div>\n";

	if (preg_match("/pri_/i", $_SERVER['PHP_SELF']) || preg_match("/price_/i", $_SERVER['PHP_SELF']) || preg_match("/search\.php/i", $_SERVER['PHP_SELF'])) {
		echo "</form>\n";
	} elseif (preg_match("/\/gopro\//i", $_SERVER['REQUEST_URI']) || preg_match("/\/nikon\//i", $_SERVER['REQUEST_URI']) || preg_match("/\/canon\//i", $_SERVER['REQUEST_URI']) || preg_match("/\/peakdesign\//i", $_SERVER['REQUEST_URI']) || preg_match("/\/fujifilm/i", $_SERVER['REQUEST_URI'])|| preg_match("/\/outdoor\/nikon/i", $_SERVER['REQUEST_URI'])) {
		echo "</form>\n";
	}

?>
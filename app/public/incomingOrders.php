<?php 
	include_once("top.php");
	include_once("header.php");
	if ($country == "")
		$country = "sv";
	
	echo "<h1>Marginalstruktur inkommande ordrar</h1>";
	echo "<div>\n";
	echo "<form method=\"GET\">\n";
	/*
	echo "<div style=\"float: left; width: 60px;\">\n";
	if ($country == "sv") {
		echo "<img border=\"0\" src=\"sv_mini.jpg\"><input type=\"radio\" name=\"country\" value=\"sv\" onClick=\"submit()\" checked>\n";
	} else {
		echo "<img border=\"0\" src=\"sv_mini.jpg\"><input type=\"radio\" name=\"country\" value=\"sv\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 60px;\">\n";
	if ($country == "fi") {
		echo "<img border=\"0\" src=\"fi_mini.jpg\"><input type=\"radio\" name=\"country\" value=\"fi\" onClick=\"submit()\" checked>\n";
	} else {
		echo "<img border=\"0\" src=\"fi_mini.jpg\"><input type=\"radio\" name=\"country\" value=\"fi\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 60px;\">\n";
	if ($country == "no") {
		echo "<img border=\"0\" src=\"no_mini.jpg\"><input type=\"radio\" name=\"country\" value=\"no\" onClick=\"submit()\" checked>\n";
	} else {
		echo "<img border=\"0\" src=\"no_mini.jpg\"><input type=\"radio\" name=\"country\" value=\"no\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	*/
	echo "<div style=\"float: left; width: 190px;\">\n";
	if ($delivered == "no") {
		echo "Visa endast Ej levererade<input type=\"checkbox\" name=\"delivered\" value=\"no\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Visa endast Ej levererade<input type=\"checkbox\" name=\"delivered\" value=\"no\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	/*
	echo "<div style=\"float: left; width: 230px;\">\n";
	if ($svea == "yes") {
		echo "Visa endast Svea ordrar > 5000<input type=\"checkbox\" name=\"svea\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Visa endast Svea ordrar > 5000<input type=\"checkbox\" name=\"svea\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	*/
	if ($delivered == "no") {
		echo "<div style=\"float: left; width: 190px;\">\n";
		if ($part_delivered == "no") {
			echo "Visa Ej dellevererade<input type=\"checkbox\" name=\"part_delivered\" value=\"no\" onClick=\"submit()\" checked>\n";
		} else {
			echo "Visa Ej dellevererade<input type=\"checkbox\" name=\"part_delivered\" value=\"no\" onClick=\"submit()\">\n";
		}
		echo "</div>\n";
	}
	if ($delivered != "no") {
		echo "<div style=\"float: left; width: 160px;\">\n";
		if ($one_week == "yes") {
			echo "Visa en vecka bakåt <input type=\"checkbox\" name=\"one_week\" value=\"yes\" onClick=\"submit()\" checked>\n";
		} else {
			echo "Visa en vecka bakåt <input type=\"checkbox\" name=\"one_week\" value=\"yes\" onClick=\"submit()\">\n";
		}
		echo "</div>\n";
	}

	echo "<div style=\"float: left; width: 140px;\">\n";
	if ($sales_by_seller == "yes") {
		echo "EJ webborder <input type=\"checkbox\" name=\"sales_by_seller\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "EJ webborder <input type=\"checkbox\" name=\"sales_by_seller\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	
	echo "<div style=\"float: left; width: 160px;\">\n";
	if ($group_by_litium == "yes") {
		echo "Endast webborder <input type=\"checkbox\" name=\"group_by_litium\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Endast webborder <input type=\"checkbox\" name=\"group_by_litium\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	
	echo "<div style=\"float: left; width: 160px;\">\n";
	if ($only_shop == "yes") {
		echo "Endast butiken <input type=\"checkbox\" name=\"only_shop\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Endast butiken <input type=\"checkbox\" name=\"only_shop\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	
	echo "</div>\n";
	echo "</form>\n";
	echo "<div class=\"clear\"></div>\n";
	
	if ($country != "") {
		if ($country == "fi") {
			$turnover->displayIncommingOrders(true, true, false);
		} elseif ($country == "no") {
			$turnover->displayIncommingOrders(false, false, true);
		} else {
			$turnover->displayIncommingOrders(true, false, false);
		}
	} else {
		echo "<p span class=\"top20 bold\">Välj vilket land du vill kika på genom att klicka ovan</p>";
	}
	
	include_once("footer.php");
?>
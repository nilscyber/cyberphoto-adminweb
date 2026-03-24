<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Nyligt utgångna produkter</h1>\n";
	echo "<div>\n";
	echo "<form method=\"GET\">\n";
	echo "<div style=\"float: left; width: 180px;\">\n";
	echo "<select name=\"history\" onchange=\"this.form.submit();\">\n";
	if ($history == "today") {
		echo "<option value=\"today\" selected>Idag</option>\n";
	} else {
		echo "<option value=\"today\">Idag</option>\n";
	}
	if ($history == "week" || $history == "") {
		echo "<option value=\"week\" selected>Senaste veckan</option>\n";
	} else {
		echo "<option value=\"week\">Senaste veckan</option>\n";
	}
	if ($history == "month") {
		echo "<option value=\"month\" selected>Senaste månaden</option>\n";
	} else {
		echo "<option value=\"month\">Senaste månaden</option>\n";
	}
	if ($history == "halfyear") {
		echo "<option value=\"halfyear\" selected>Senaste halvåret</option>\n";
	} else {
		echo "<option value=\"halfyear\">Senaste halvåret</option>\n";
	}
	if ($history == "year") {
		echo "<option value=\"year\" selected>Senaste året</option>\n";
	} else {
		echo "<option value=\"year\">Senaste året</option>\n";
	}
	echo "</select>\n";
	echo "</div>\n";

	/*
	echo "<div style=\"float: left; width: 190px;\">\n";
	if ($delivered == "no") {
		echo "Visa endast Ej levererade<input type=\"checkbox\" name=\"delivered\" value=\"no\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Visa endast Ej levererade<input type=\"checkbox\" name=\"delivered\" value=\"no\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 230px;\">\n";
	if ($svea == "yes") {
		echo "Visa endast Svea ordrar > 5000<input type=\"checkbox\" name=\"svea\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Visa endast Svea ordrar > 5000<input type=\"checkbox\" name=\"svea\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
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
	*/
	
	echo "</div>\n";
	echo "</form>\n";
	echo "<div class=\"clear\"></div>\n";	
	echo "<div class='top10'>";
	$adminstat->listDiscontinuedProducts();
	echo "</div>\n";
	
	include_once("footer.php");
?>
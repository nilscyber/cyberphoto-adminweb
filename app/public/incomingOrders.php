<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	if ($country == "")
		$country = "sv";
?>
<?php
	echo "<h1>Marginalstruktur inkommande ordrar</h1>\n";
	echo "<form method=\"GET\">\n";
	echo "<div class=\"filter-bar\">\n";

	$chk = ($delivered == "no") ? " checked" : "";
	echo "<label><input type=\"checkbox\" name=\"delivered\" value=\"no\" onClick=\"submit()\"$chk> Visa endast Ej levererade</label>\n";

	if ($delivered == "no") {
		$chk = ($part_delivered == "no") ? " checked" : "";
		echo "<label><input type=\"checkbox\" name=\"part_delivered\" value=\"no\" onClick=\"submit()\"$chk> Visa Ej dellevererade</label>\n";
	}

	if ($delivered != "no") {
		$chk = ($one_week == "yes") ? " checked" : "";
		echo "<label><input type=\"checkbox\" name=\"one_week\" value=\"yes\" onClick=\"submit()\"$chk> Visa en vecka bakåt</label>\n";
	}

	$chk = ($only_delivered == "yes") ? " checked" : "";
	echo "<label><input type=\"checkbox\" name=\"only_delivered\" value=\"yes\" onClick=\"submit()\"$chk> Endast levererade</label>\n";

	$chk = ($sales_by_seller == "yes") ? " checked" : "";
	echo "<label><input type=\"checkbox\" name=\"sales_by_seller\" value=\"yes\" onClick=\"submit()\"$chk> EJ webborder</label>\n";

	$chk = ($group_by_litium == "yes") ? " checked" : "";
	echo "<label><input type=\"checkbox\" name=\"group_by_litium\" value=\"yes\" onClick=\"submit()\"$chk> Endast webborder</label>\n";

	$chk = ($only_shop == "yes") ? " checked" : "";
	echo "<label><input type=\"checkbox\" name=\"only_shop\" value=\"yes\" onClick=\"submit()\"$chk> Endast butiken</label>\n";

	echo "</div>\n";
	echo "</form>\n";

	$turnover->displayIncommingOrders(true, false, false);

	include_once("footer.php");
?>

<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	if ($products == "")
		$products = "all";

	echo "<h1>Inbytt lager</h1>\n";

	echo "<form method=\"GET\" class=\"filter-bar\">\n";
	echo "<label>";
	if ($show_webb == "yes") {
		echo "<input type=\"checkbox\" name=\"show_webb\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "<input type=\"checkbox\" name=\"show_webb\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "Endast ute på webb</label>\n";
	echo "</form>\n";

	if ($show_webb == "yes") {
		$tradein->tradeInValue(true,false);
	} else {
		$tradein->tradeInValue(false,false);
	}

	include_once("footer.php");
?>
<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	if ($products == "")
		$products = "all";

	echo "<h1>Tillgängliga begagnade produkter</h1>\n";

	echo "<form method=\"GET\">\n";
	echo "<div class=\"filter-bar\">\n";

	$radioOptions = [
		"all"         => "Samtliga",
		"cameras"     => "Kameror",
		"lenses"      => "Objektiv",
		"accessories" => "Tillbehör",
	];
	foreach ($radioOptions as $val => $label) {
		$checked = ($products == $val) ? " checked" : "";
		echo "<label><input type=\"radio\" name=\"products\" value=\"$val\" onClick=\"submit()\"$checked> $label</label>\n";
	}

	$momsChecked = ($moms == "yes") ? " checked" : "";
	echo "<label><input type=\"checkbox\" name=\"moms\" value=\"yes\" onClick=\"submit()\"$momsChecked> Endast momsade</label>\n";

	$webbChecked = ($show_webb == "yes") ? " checked" : "";
	echo "<label><input type=\"checkbox\" name=\"show_webb\" value=\"yes\" onClick=\"submit()\"$webbChecked> Endast på webb</label>\n";

	echo "</div>\n";
	echo "</form>\n";

	$tradein->KOTlist(false,false,false);

	include_once("footer.php");
?>

<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	// Standard: dölj ordrar låsta på inbytesaffärer, tills man aktivt bockar i annat
	$showtradein = isset($showtradein) ? $showtradein : "no";

	echo "<h1>Låsta ordrar</h1>\n";

	echo "<form method=\"GET\">\n";
	echo "<div class=\"filter-bar\">\n";
	echo "<label>";
	echo "<input type=\"checkbox\" name=\"showtradein\" value=\"yes\" onClick=\"submit()\"" . ($showtradein == "yes" ? " checked" : "") . ">";
	echo "Visa inbytesaffärer";
	echo "</label>\n";
	echo "</div>\n";
	echo "</form>\n";

	echo "<div class=\"result-info\">Produkter som väntar på leverans grupperas per order. Ordrar där alla produkter redan är allokerade markeras röda &ndash; de bör skickas omgående.</div>\n";
	echo "<div>";
	$allocated->displayLockedOrderGroups($showtradein);
	echo "</div>\n";

	include_once("footer.php");
?>

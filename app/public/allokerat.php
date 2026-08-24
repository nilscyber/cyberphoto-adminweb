<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	// Standard: dölj ordrar låsta på inbytesaffärer samt använd prislimit, tills man aktivt bockar i annat
	$showtradein = isset($showtradein) ? $showtradein : "no";
	$nopricelimit = isset($nopricelimit) ? $nopricelimit : "no";

	echo "<h1>Låsta ordrar (DSLR &amp; värde &gt; 5000 SEK)</h1>\n";

	echo "<form method=\"GET\">\n";
	echo "<div class=\"filter-bar\">\n";
	echo "<label>";
	echo "<input type=\"checkbox\" name=\"showtradein\" value=\"yes\" onClick=\"submit()\"" . ($showtradein == "yes" ? " checked" : "") . ">";
	echo "Visa ordrar låsta på inbytesaffärer";
	echo "</label>\n";
	echo "<label>";
	echo "<input type=\"checkbox\" name=\"nopricelimit\" value=\"yes\" onClick=\"submit()\"" . ($nopricelimit == "yes" ? " checked" : "") . ">";
	echo "Ingen prislimit";
	echo "</label>\n";
	echo "</div>\n";
	echo "</form>\n";

	echo "<div class=\"result-info\">Saknade produkter på samma order visas tillsammans. Ordrar där allt redan är allokerat markeras röda &ndash; de bör skickas omgående.</div>\n";
	echo "<div>";
	$allocated->displayLockedOrderGroups($nopricelimit, $showtradein);
	echo "</div>\n";

	include_once("footer.php");
?>

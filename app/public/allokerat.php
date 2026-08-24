<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	// Standard: dölj ordrar låsta på inbytesaffärer samt ej kompletta ordrar, tills man aktivt bockar i annat
	$showtradein = isset($showtradein) ? $showtradein : "no";
	$showincomplete = isset($showincomplete) ? $showincomplete : "no";

	echo "<h1>Låsta produkter</h1>\n";

	echo "<form method=\"GET\">\n";
	echo "<div class=\"filter-bar\">\n";
	echo "<label>";
	echo "<input type=\"checkbox\" name=\"showtradein\" value=\"yes\" onClick=\"submit()\"" . ($showtradein == "yes" ? " checked" : "") . ">";
	echo "Visa inbytesaffärer";
	echo "</label>\n";
	echo "<label>";
	echo "<input type=\"checkbox\" name=\"showincomplete\" value=\"yes\" onClick=\"submit()\"" . ($showincomplete == "yes" ? " checked" : "") . ">";
	echo "Visa ej kompletta ordrar";
	echo "</label>\n";
	echo "</div>\n";
	echo "</form>\n";

	echo "<div class=\"result-info\">Produkter som väntar på leverans grupperas per order och markeras blå. Ordrar där allt redan är allokerat får en grön \"KLAR ATT SKICKAS\"-flagga &ndash; de bör skickas omgående.</div>\n";
	echo "<div>";
	$allocated->displayOrderGroups("locked", $showtradein);
	echo "</div>\n";

	if ($showincomplete == "yes") {
		echo "<h1>Ej kompletta ordrar</h1>\n";
		echo "<div class=\"result-info\">Ordrar som inte är låsta men ändå inte kan skickas eftersom minst en annan produkt på ordern fortfarande väntar på att komma in.</div>\n";
		echo "<div>";
		$allocated->displayOrderGroups("incomplete", $showtradein);
		echo "</div>\n";
	}

	include_once("footer.php");
?>

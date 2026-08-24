<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	// Tre olika vyer, styrda av $view: "locked" (standard), "incomplete" eller "tradein"
	$view = isset($view) ? $view : "locked";
	if (!in_array($view, array("locked", "incomplete", "tradein"))) {
		$view = "locked";
	}

	echo "<h1>Låsta produkter</h1>\n";

	echo "<form method=\"GET\">\n";
	echo "<div class=\"filter-bar\">\n";
	echo "<label>";
	echo "<input type=\"radio\" name=\"view\" value=\"locked\" onClick=\"submit()\"" . ($view == "locked" ? " checked" : "") . ">";
	echo "Låsta ordrar";
	echo "</label>\n";
	echo "<label>";
	echo "<input type=\"radio\" name=\"view\" value=\"incomplete\" onClick=\"submit()\"" . ($view == "incomplete" ? " checked" : "") . ">";
	echo "Ej låsta ordrar";
	echo "</label>\n";
	echo "<label>";
	echo "<input type=\"radio\" name=\"view\" value=\"tradein\" onClick=\"submit()\"" . ($view == "tradein" ? " checked" : "") . ">";
	echo "Inbytesaffärer";
	echo "</label>\n";
	echo "</div>\n";
	echo "</form>\n";

	if ($view == "incomplete") {
		echo "<div class=\"result-info\">Ordrar som inte är låsta men ändå inte kan skickas eftersom minst en annan produkt på ordern fortfarande väntar på att komma in.</div>\n";
		echo "<div>";
		$allocated->displayOrderGroups("incomplete", "no");
		echo "</div>\n";
	} elseif ($view == "tradein") {
		echo "<div class=\"result-info\">Ordrar som är låsta på inbytesaffärer.</div>\n";
		echo "<div>";
		$allocated->displayOrderGroups("locked", "yes");
		echo "</div>\n";
	} else {
		echo "<div class=\"result-info\">Produkter som väntar på leverans grupperas per order och markeras blå. Ordrar där allt redan är allokerat får en grön \"KLAR ATT SKICKAS\"-flagga &ndash; de bör skickas omgående.</div>\n";
		echo "<div>";
		$allocated->displayOrderGroups("locked", "no");
		echo "</div>\n";
	}

	include_once("footer.php");
?>

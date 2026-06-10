<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	echo "<h1>Filter för inkommande ordrar</h1>\n";
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($add == "yes" || $addID != "") {
		include("check_incoming_add.php");
	}
	if ($add != "yes") {
		echo "<div style=\"margin-bottom:16px;\">\n";
		echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\" style=\"display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#0d9488;color:#fff;border-radius:5px;text-decoration:none;font-size:13px;font-weight:600;\">&#43; Lägg till filter</a>\n";
		echo "</div>\n";
		echo "<h2>Aktuella filter</h2>\n";
		$filter->getActualFilters();
		echo "<h2 style=\"margin-top:28px;\">Avaktiverade bevakningar</h2>\n";
		$filter->getActualFilters(true);
	}

	echo "<div style=\"margin-top:28px;padding:16px 20px;background:#f0fdfa;border:1px solid #99f6e4;border-radius:8px;font-size:13px;line-height:1.6;\">\n";
	echo "<div style=\"font-weight:700;margin-bottom:8px;\">LÄS DETTA!</div>\n";
	echo "<p style=\"margin:0 0 6px;\">Observera att denna funktion skall användas med eftertänksamhet. Den finns till för att stoppa buset och inte för att bevaka när grannen handlar varor av oss.</p>\n";
	echo "<p style=\"margin:0 0 6px;\">Alla aviseringar skickas in i OTRS-kön och hanteras efter upplagda rutiner.</p>\n";
	echo "<p style=\"margin:0 0 10px;\">Har du frågor om detta? Prata med Patrick Ohlsson (Stefan Sjöberg om det tekniska)</p>\n";
	echo "<div style=\"font-weight:600;margin-bottom:4px;\">Fält som skannas</div>\n";
	echo "<ul style=\"margin:0 0 10px;padding-left:20px;\">\n";
	echo "<li>Namn</li>\n";
	echo "<li>Adressrad 1</li>\n";
	echo "<li>Adressrad 2</li>\n";
	echo "<li>Postort</li>\n";
	echo "<li>Namn (leverans)</li>\n";
	echo "<li>Adressrad 1 (leverans)</li>\n";
	echo "<li>Adressrad 2 (leverans)</li>\n";
	echo "<li>Postort (leverans)</li>\n";
	echo "<li>E-postadress</li>\n";
	echo "<li>Telefon</li>\n";
	echo "<li>Personnummer / organisationsnummer</li>\n";
	echo "</ul>\n";
	echo "<p style=\"margin:0 0 4px;\">Tänk på att inte göra för detaljerade sökningar. Ange istället delar av namnen du vill söka på.</p>\n";
	echo "<p style=\"margin:0 0 4px;\">Exempel: Om du vill bevaka en buse som brukar lägga ordrar som Benjamin Karlsson i Växjö. Lägg ett filter på: <em>benj karl väx</em></p>\n";
	echo "<p style=\"margin:0;\">Processen körs var 15:e minut och skickas in till OTRS kön \"Brådskande\".</p>\n";
	echo "</div>\n";

	include_once("footer.php");
?>

<?php 
	include_once("top.php");
	include_once("header.php");
	
	// $sales->salesAddValue($cpto->getOutgoingOrders(true),1);
	// echo $cpto->getOutgoingOrders(true);
	// $filter->getWordsToCheck();

	echo "<h1>Filter för inkommande ordrar</h1>\n";
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($add == "yes" || $addID != "") {
		include("check_incoming_add.php");
	}
	if ($add != "yes") {
		echo "<div><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<b><a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">Lägg till filter</b></a></div>\n";
		echo "<h2>Aktuella filter</h2>\n";
		$filter->getActualFilters();
		echo "<h2>Avaktiverade bevakningar</h2>\n";
		$filter->getActualFilters(true);
	}
	echo "<div class=\"top20\"></div>\n";
	echo "<hr noshade=\"\" color=\"#999999\" align=\"left\" width=\"100%\" size=\"1\">\n";
	echo "<div class=\"top20\"></div>\n";
	echo "<div class=\"container_grey\">\n";
	echo "<div><b>LÄS DETTA!</b></div>\n";
	echo "<div>Observera att denna funktion skall användas med eftertänksamhet. Den finns till för att stoppa buset och inte för att bevaka när grannen handlar varor av oss.</div>\n";
	echo "<div>Alla aviseringar skickas in i OTRS-kön och hanteras efter upplagda rutiner.</div>\n";
	echo "<div class=\"top20\">Har du frågor om detta? Prata med Patrick Ohlsson (Stefan Sjöberg om det tekniska)</div>\n";
	echo "<div class=\"top20\">\n";
	echo "<b>Fält som skannas</b>\n";
	echo "<ul>\n";
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
	echo "</ul>\n";
	echo "</div>\n";
	echo "<div class=\"top20\">Tänk på att inte göra för detaljerade sökningar. Ange istället delar av namnen du vill söka på.</div>\n";
	echo "<div class=\"\">Exempel: Om du vill bevaka en buse som brukar lägga ordrar som Benjamin Karlsson i Växjö. Lägg ett filter på: <i>benj karl väx</i></div>\n";
	echo "<div class=\"top20\">Processen körs var 15:e minut och skickas in till OTRS kön \"Brådskande\".</div>\n";
	echo "</div>\n";
	
	include_once("footer.php");
?>
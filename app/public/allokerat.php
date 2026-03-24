<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Låsta produkter (enskilda bevakningar)</h1>\n";
	echo "<div>\n";
	echo "<form method=\"GET\">\n";

	echo "<div style=\"float: left; width: 190px;\">\n";
	if ($istradein == "no") {
		echo "Visa Ej inbytesaffärer<input type=\"checkbox\" name=\"istradein\" value=\"no\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Visa Ej inbytesaffärer<input type=\"checkbox\" name=\"istradein\" value=\"no\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 190px;\">\n";
	if ($nopricelimit == "yes") {
		echo "Ingen prislimit<input type=\"checkbox\" name=\"nopricelimit\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Ingen prislimit<input type=\"checkbox\" name=\"nopricelimit\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	
	echo "</div>\n";
	echo "</form>\n";
	echo "<div class=\"clear\"></div>\n";

	echo "<div class=\"top10\">";
	$allocated->showActualMonitorAllocated();
	// $allocated->getActualMonitorAllocated();
	echo "</div>\n";
	if ($add != "yes") {
		// echo "<p>&nbsp;</p>\n";
		echo "<div class=\"top10\"><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<b><a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">Lägg till artikel för enskild bevakning</b></a></div>\n";
	}
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($add == "yes" || $addID != "") {
		include("add_allocated.php");
	}
	echo "<h1>Låsta produkter (DSLR)</h1>\n";
	echo "<div>";
	$allocated->displayAllocatedButReady(1);
	echo "</div>\n";
	echo "<h1>Låsta produkter (VÄRDE > 5000 SEK)</h1>\n";
	echo "<div>";
	$allocated->displayAllocatedButReady(2);
	echo "</div>\n";
	
	include_once("footer.php");
?>
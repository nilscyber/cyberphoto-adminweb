<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Abonnemang data - Kalkyl</h1>\n";
	
	echo "<form>\n";
	echo "<div class=top10>";
	echo "Välj abonnemang: ";
	$mobile->getOperatorAbbListKalkyl($op,2);
	echo "&nbsp;&nbsp;&nbsp;Välj telefon: ";
	$mobile->getMobilePhoneListKalkyl($article,2);
	echo "</div>\n";
	if ($abb != "" && $article != "") {
		echo "<div class=top10>";
		$mobile->displayPriceAbbInternal();
		echo "</div>\n";
	}
	echo "</form>\n";
	
	echo "<div class=top10>OBS! Alla priser ovan presenteras exklusive moms.";
	echo "</div>\n";
	
	include_once("footer.php");
?>
<?php 
	include_once("top.php");
	include_once("header.php");
	
	if ($sortby == "TB") {
		echo "<h1>Produkter med bäst täckningsbidrag (de som finns i lager)</h1>\n";
	} else {
		echo "<h1>Produkter med bäst täckningsgrad (de som finns i lager)</h1>\n";
	}
	echo "<div class='top10'>";
	$parcel->getBestTG();
	echo "</div>\n";
	
	include_once("footer.php");
?>
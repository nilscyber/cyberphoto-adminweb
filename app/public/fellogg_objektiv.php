<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Fellogg - Objektiv</h1>\n";
	echo "<h2>Ej ifyllda filterfunktioner</h2>\n";
	echo "<div class='top10'>";
	$check->getProductsWithNoFilter($grupp);
	echo "</div>\n";
	echo "<h2>Produkter filterdiameter eller motljusskydd Ej är ifyllda</h2>\n";
	echo "<div class='top10'>";
	$check->getLensWithNoHighLight();
	echo "</div>\n";
	echo "<h2>Produkter där det saknas värdepaket</h2>\n";
	echo "<div class='top10'>";
	$check->displayArtWithNoPac($grupp);
	echo "</div>\n";
	echo "<h2>Produkter där det saknas tekniska data</h2>\n";
	echo "<div class='top10'>";
	$check->displayArtWithNoTekData($grupp);
	echo "</div>\n";
	
	include_once("footer.php");
?>
<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Kommande produktuppdateringar</h1>\n";
	echo "<div class='top10'>";
	echo "<h2>Kommande</h2>\n";
	$product->listCommingUpdates();
	echo "<div class='top20'></div>\n";
	echo "<hr class=\"hr_blue\">\n";
	echo "<h2>Redan uppdaterade</h2>\n";
	$product->listCommingUpdates(true);
	echo "</div>\n";
	echo "<div class='top20'></div>\n";
	echo "<h2>Fördelning av utförda uppdateringar</h2>\n";
	$product->listProductUpdateBy();
	echo "</div>\n";
	
	include_once("footer.php");
?>
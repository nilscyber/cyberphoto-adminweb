<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Hantera tekniska data objektiv</h1>\n";
	
	if ($add == "yes" || $addid != "" || $addidc != "") {
		if ($wrongmess) {
			echo $wrongmess;
		}
		include("Tekn_lenses_add_v3.php");
	} else {
	
		echo "\n<div class=\"top10\"></div>\n";
		echo "<div><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">Lägg till tekniska data</a></div>\n";
		
		echo "<h5>Befintliga produkter med tekniska data</h5>\n";
		include("tekn_show_old_products.php");
		$tech->getActualProducts("Tekn_lenses");
	
	}
	
	include_once("footer.php");
?>
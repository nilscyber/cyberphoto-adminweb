<?php 
	include_once("top.php");
	include_once("header.php");
	
	/*
	echo "<h1>Söklogg</h1>\n";
	echo "<p>De fetmarkerade indikerar att sökningen inte givit någon träff</p>\n";
	echo "<div class='top10'>";
	#$csearch->getSearchLogg(1);
	#$csearch->getSearchLogg(2);
	#$csearch->getSearchLogg(3);
	echo "</div>\n";
	*/
	echo "<h1>Sökloggar, sidan uppdatera automatiskt var 30 sek</h1>\n";
	echo "<p>De fetmarkerade indikerar att sökningen inte givit besökaren någon träff</p>\n";
	echo "<div id=\"searchArea\">Laddar sidan......<br><img border=\"0\" src=\"ajax-loader.gif\"></div>\n";

	
	include_once("footer.php");
?>

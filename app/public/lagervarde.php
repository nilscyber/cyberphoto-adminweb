<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Aktuell lagervärde</h1>\n";
	echo "<div id=\"txtArea\"><p>&nbsp;Laddar aktuellt lagervärde, vänligen vänta medan jag summerar...<br><br><img border=\"0\" src=\"ajax-loader.gif\"><br><br></p></div>\n";
	echo "<div id='chart_div' style='width: 1000px; height: 380px;'></div>\n";
	echo "<div class='top10'>\n";
	$store->displayHistoryStore();
	echo "</div>\n";
	
	include_once("footer.php");
?>
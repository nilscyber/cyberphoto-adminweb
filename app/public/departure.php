<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Sista beställningstid från oss</h1>\n";
	echo "<p><img border=\"0\" src=\"/pic/help.gif\"><a href=\"javascript:winPopupCenter(250, 400, 'add_departure.php');\"> Lägg till avgångar</a></p>\n";
	if ($_COOKIE['login_ok'] != "true") {
		echo "<div class=\"container_loggin\">\n";
		echo "<span class=\"not_loggin\">Du är Ej inloggad och kommer därför inte kunna utföra åtgärden!</span>\n";
		echo "</div>\n";
		echo "<div class=\"clear\"></div>\n";
	}
	echo "<h2>Kommande tider</h2>\n";
	echo "<div>";
	$departure->getDepartureFuture();
	echo "</div>\n";
	
	include_once("footer.php");
?>
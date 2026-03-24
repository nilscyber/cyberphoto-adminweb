<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Senaste systeminformationen</h1>\n";
	
	echo "<div class=top10>";
	echo "&nbsp;<img border=\"0\" src=\"/pic/help.gif\">&nbsp;<a class=\"greylink\" href=\"javascript:winPopupCenter(550, 650, 'http://www.cyberphoto.se/order/admin/systemblogg.php?addsys=yes');\">Lägg till ny information</a>";
	echo "</div>\n";
	echo "<div class=top10>";
	$blogg->getLatestSystemBlogg("13,14,16,17,32");
	echo "</div>\n";
	
	include_once("footer.php");
?>
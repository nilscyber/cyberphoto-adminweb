<?php 
// error_reporting(E_ALL & ~E_NOTICE);
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Bevaka nivåer på enskilda artiklar</h1>\n";
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($add == "yes" || $addID != "") {
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			include("monitor_articles_add.php");
		} else {
			include("monitor_articles_add.php");
		}
	}
	echo "<div><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<b><a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">Lägg till bevakning</b></a></div>\n";
	if ($add != "yes") {
		echo "<h2>Aktuella bevakningar</h2>\n";
		$monitor->getActualMonitors();
		// if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
			echo "<h2>Redan aviserade bevakningar</h2>\n";
			$monitor->getNotActualMonitors();
		}
	}
	if ($addArtnr != "") {
		echo "<h2>Aktuella bevakningar på denna produkt</h2>\n";
		$monitor->getActualMonitorsArticle($addArtnr);
	}
	
	include_once("footer.php");
?>

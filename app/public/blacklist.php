<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Svartlistade IP-adresser</h1>\n";
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($add == "yes" || $addID != "") {
		include("blacklist_add.php");
	}
	if ($add != "yes") {
		echo "<div><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<b><a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">Lägg till bevakning</b></a></div>\n";
		echo "<h2>Aktuella bevakningar</h2>\n";
		$blacklist->getActualBlacklist();
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			echo "<h2>Avaktiverade bevakningar</h2>\n";
			$blacklist->getNotActualBlacklist();
		}
	}
	
	include_once("footer.php");
?>
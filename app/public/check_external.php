<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Ändra om du vill visa vår hemsida med externa eller interna \"ögon\".</h1>";
	if ($_SESSION['EXTERNAL_SWITCH'] == 1) {
		echo "<p>Du är just nu <b><font color=\"blue\">extern</b></font> betraktare</p>";
	} else {
		echo "<p>Du är just nu <b><font color=\"green\">intern</b></font> betraktare</p>";
	}
	if ($_SESSION['EXTERNAL_SWITCH'] == 1) {
		echo "<p><a href=\"" . $_SERVER['PHP_SELF'] . "?switch_external=no\">Ändra</a></p>";
	} else {
		echo "<p><a href=\"" . $_SERVER['PHP_SELF'] . "?switch_external=yes\">Ändra</a></p>";
	}
	
	include_once("footer.php");
?>
<?php
include_once("top.php");
include_once("header.php");

if ($_COOKIE['login_ok'] == "true") {
	echo "<h1>In- samt utgående ordrar, lagerstatus samt plock-situationen</h1>";
	echo "<h5>Sidan uppdateras automatiskt var 60:e sekund</h5>";
	echo "<div id=\"txtArea\">Laddar sidan......<br><img border=\"0\" src=\"ajax-loader.gif\"></div>\n";
} else {
	echo "<h1>Välkommen, logga in för att fortsätta.</h1>";
}

include_once("footer.php");
?>
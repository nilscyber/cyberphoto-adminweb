<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	echo "<h1>Dubbletter</h1>\n";

	$tradein->findDoublets(true,true);

	include_once("footer.php");
?>

<?php
	include_once("top.php");
	include_once("header.php");

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	echo "<h1>Godsvolymer som kanske borde avbokas</h1>\n";
	echo "<div class='top10'>";
	$adintern->purschasedGoodsWithNoCustomers();
	echo "</div>\n";

	include_once("footer.php");
?>
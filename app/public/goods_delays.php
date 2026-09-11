<?php
	include_once("top.php");
	include_once("header.php");

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	echo "<h1>Försenade godsvolymer</h1>\n";
	echo "<div class='top10'>";
	$adintern->goodsExpectationDelay();
	echo "</div>\n";
	if ($supID > 0) {
		echo "<div class='top10'>";
		$adintern->goodsExpectationDelayDetail();
		echo "</div>\n";
	}

	include_once("footer.php");
?>
<?php 
	include_once("top.php");
	include_once("header.php");
	
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
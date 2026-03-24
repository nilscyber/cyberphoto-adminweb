<?php

	echo "<div class=\"container_grey floatleft\">\n";
	if ($_SESSION['menusite'] == 1) {
		echo "<div class=\"floatleft marked_container\"><a href=\"?choose_site=1\"><img src=\"sv.jpg\"></a></div>\n";
	} else {
		echo "<div class=\"floatleft notmarked_container\"><a href=\"?choose_site=1\"><img src=\"sv.jpg\"></a></div>\n";
	}
	if ($_SESSION['menusite'] == 2) {
		echo "<div class=\"floatleft left10 marked_container\"><a href=\"?choose_site=2\"><img src=\"no.jpg\"></a></div>\n";
	} else {
		echo "<div class=\"floatleft left10 notmarked_container\"><a href=\"?choose_site=2\"><img src=\"no.jpg\"></a></div>\n";
	}
	if ($_SESSION['menusite'] == 3) {
		echo "<div class=\"floatleft left10 marked_container\"><a href=\"?choose_site=3\"><img src=\"fi.jpg\"></a></div>\n";
	} else {
		echo "<div class=\"floatleft left10 notmarked_container\"><a href=\"?choose_site=3\"><img src=\"fi.jpg\"></a></div>\n";
	}
	echo "<div class=\"clear\"></div>\n";
	echo "</div>\n";

	echo "<div class=\"clear\"></div>\n";
?>
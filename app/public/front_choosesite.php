<?php

	echo "<div class=\"container_grey floatleft\">\n";
	if ($_SESSION['bannersite'] == 1) {
		echo "<div class=\"floatleft marked_container\"><a href=\"?choose_site=1\"><img src=\"sv.jpg\"></a></div>\n";
	} else {
		echo "<div class=\"floatleft notmarked_container\"><a href=\"?choose_site=1\"><img src=\"sv.jpg\"></a></div>\n";
	}
	if ($_SESSION['bannersite'] == 2) {
		echo "<div class=\"floatleft left10 marked_container\"><a href=\"?choose_site=2\"><img src=\"no.jpg\"></a></div>\n";
	} else {
		echo "<div class=\"floatleft left10 notmarked_container\"><a href=\"?choose_site=2\"><img src=\"no.jpg\"></a></div>\n";
	}
	if ($_SESSION['bannersite'] == 3) {
		echo "<div class=\"floatleft left10 marked_container\"><a href=\"?choose_site=3\"><img src=\"fi.jpg\"></a></div>\n";
	} else {
		echo "<div class=\"floatleft left10 notmarked_container\"><a href=\"?choose_site=3\"><img src=\"fi.jpg\"></a></div>\n";
	}
	if ($_SESSION['bannersite'] == 4) {
		echo "<div class=\"floatleft left10 marked_container\"><a href=\"?choose_site=4\"><img src=\"fisv.jpg\"></a></div>\n";
	} else {
		echo "<div class=\"floatleft left10 notmarked_container\"><a href=\"?choose_site=4\"><img src=\"fisv.jpg\"></a></div>\n";
	}
	echo "<div class=\"clear\"></div>\n";
	echo "</div>\n";

	echo "<div class=\"clear\"></div>\n";
?>
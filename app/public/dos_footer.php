<?php
	if ($emptysearch) {
		// echo "\t\t<div class=\"center\"><img width=\"100%\" border=\"\" src=\"https://admin.cyberphoto.se/invader.gif\"></div>\n";
		// echo "\t\t<div data-role=\"page\" style=\"background:url('invader.gif'); background-repeat: no-repeat; background-size: 100% 100%;\" >\n";
	}
	echo "\n</div>\n"; // slut content
	// if ((!$emptysearch && !$clearsearch) && $countarticles == 0 && ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_SERVER['REMOTE_ADDR'] == "192.168.1.89")) {
	if ((!$emptysearch && !$clearsearch) && $countarticles == 0 && preg_match("/dos_product\.php/i", $_SERVER['PHP_SELF'])) {
		echo "\n<div id=\"invaders\"><img style=\"-webkit-user-select: none;\" src=\"https://admin.cyberphoto.se/invaders.webp\"></div>\n";
	}
	echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"wz_tooltip_front.js\"></script>\n";
	echo "</body>\n";
	echo "</html>\n";
?>
<?php
	/* Installerat på vår server 2013-10-04 */
	if (!$mobil && !$hobby && !$batterier && !$cybairgun && !$fi && !$no && $_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu') {
		echo "<script type=\"text/javascript\" id=\"la_x2s6df8d\" src=\"/chat/scripts/track.js\"></script>\n";
		echo "<img src=\"/chat/scripts/pix.gif\" onLoad=\"LiveAgentTracker.createButton('e21aed92', this);\"/>\n";
	}
	if ($mobil && !$fi && !$no) {
		echo "<script type=\"text/javascript\" id=\"la_x2s6df8d\" src=\"/chat/scripts/track.js\"></script>\n";
		echo "<img src=\"/chat/scripts/pix.gif\" onLoad=\"LiveAgentTracker.createButton('69a6dc4f', this);\"/>\n";
	}
?>
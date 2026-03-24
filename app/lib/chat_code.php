<?php
	//if ($departure->OnlineStatusSimple() && false) {
	if (true) {
		/* Installerat på vår server 2013-10-04 */
		// if (!$mobil && !$hobby && !$batterier && !$cybairgun && !$fi && !$no && ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_SERVER['REMOTE_ADDR'] == "192.168.1.89")) {
		if (!$mobil && !$hobby && !$batterier && !$cybairgun && !$fi && !$no) {
			echo "<script type=\"text/javascript\" id=\"la_x2s6df8d\" src=\"/chat/scripts/track.js\"></script>\n";
			echo "<img src=\"/chat/scripts/pix.gif\" onLoad=\"LiveAgentTracker.createButton('89c81824', this);\"/>\n";
		}
		if ($mobil && !$fi) {
			echo "<script type=\"text/javascript\" id=\"la_x2s6df8d\" src=\"/chat/scripts/track.js\"></script>\n";
			echo "<img src=\"/chat/scripts/pix.gif\" onLoad=\"LiveAgentTracker.createButton('69a6dc4f', this);\"/>\n";
		}
		// if ($hobby && !$fi && !$no && $_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu') {
		if ($hobby && !$fi) {
			echo "<script type=\"text/javascript\" id=\"la_x2s6df8d\" src=\"/chat/scripts/track.js\"></script>\n";
			echo "<img src=\"/chat/scripts/pix.gif\" onLoad=\"LiveAgentTracker.createButton('85ee8f28', this);\"/>\n";
		}
	}
?>

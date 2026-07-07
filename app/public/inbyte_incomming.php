<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	echo "<h1>Inkommande paket</h1>\n";

	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($add == "yes" || $addID != "") {
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			include("inbyte_incomming_add_v2.php");
		} else {
			include("inbyte_incomming_add_v2.php");
		}
	} else {
		echo "<div class=\"bottom10\"><a href=\"ping_incomming.php?add=yes\" target=\"_blank\" class=\"btn-link-sm\">+ Lägg till post</a></div>\n";
	}

	$tradein->getPingIncomming(false);
	if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
		$tradein->getPingIncomming(true);
	}
	// $tradein->getTradeInWishlist(true,true);

	include_once("footer.php");
?>
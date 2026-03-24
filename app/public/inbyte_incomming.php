<?php 
	include_once("top.php");
	include_once("header.php");

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
		echo "<div class=\"bottom10\"><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<b><a href=\"ping_incomming.php?add=yes\" target=\"_blank\">Lägg till post</b></a></div>\n";
	}
	
	$tradein->getPingIncomming(false);
	if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
		$tradein->getPingIncomming(true);
	}
	// $tradein->getTradeInWishlist(true,true);

	include_once("footer.php");
?>
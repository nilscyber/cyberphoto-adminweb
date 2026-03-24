<?php 
	include_once("top.php");
	include_once("header.php");

	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($add == "yes" || $addID != "") {
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			include("inbyte_wishlist_add_v1.php");
		} else {
			include("inbyte_wishlist_add_v1.php");
		}
	} else {
		echo "<div class=\"bottom10\"><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<b><a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">Lägg till post</b></a></div>\n";
	}
	
	$tradein->getTradeInWishlist(true,false);
	$tradein->getTradeInWishlist(true,true);

	include_once("footer.php");
?>
<?php
	include_once("top.php");
	include_once("dos_header.php");

	echo "<div id=\"search\">\n";
	echo "<form method=\"GET\">\n";
	// echo "<input class=\"searchbar\" type=\"text\" name=\"q\" value=\"$beskrivning\" autofocus> <a href=\"/p\">rensa</a>\n";
	// if ($beskrivning == "") {
	if ($clearsearch) {
		echo "<input id=\"searchbar\" class=\"searchbar\" type=\"text\" name=\"q\" value=\"$beskrivning\" autofocus>\n";
	} else {
		echo "<input id=\"searchbar\" class=\"searchbar\" type=\"text\" name=\"q\" value=\"$beskrivning\">\n";
	}
	// echo "</form>\n";
	echo "</div>\n";
	if (!$emptysearch) {
		include ("dos_pricelist.php");
	}
	include_once("dos_footer.php");
	
	// echo $currentUrl;
	// phpinfo();
?>
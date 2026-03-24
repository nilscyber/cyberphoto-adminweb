<?php
	echo "<form>\n";
	if ($show_oldproduct == "yes") {
		echo "<input type=\"checkbox\" name=\"show_oldproduct\" value=\"yes\" onclick=\"submit()\" checked>\n";
	} else {
		echo "<input type=\"checkbox\" name=\"show_oldproduct\" value=\"yes\" onclick=\"submit()\">\n";
	}
	echo " Visa utgångna produkter istället";
	echo "</form>\n";
	echo "<div class=\"top20\"></div>\n";
?>
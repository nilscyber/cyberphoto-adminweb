<?php


Class CFond {

function getStore($artlev) {

	$string = file_get_contents("http://92.33.9.114/s.aspx?pid=$artlev");
	
	$page = preg_replace("/document.write(\()(\")/", "", $string);
	$page = preg_replace("/(\")(\))(\;)/", "", $page);
	$page = preg_replace("/Ã¤/", "ä", $page);
		
	echo "<b><font color=\"#FF3300\">" . $page . "</font></b>";

}

}

?>

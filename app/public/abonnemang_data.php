<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Abonnemang - Data</h1>\n";

	echo "<div class=top10>";
	$mobile->getAdminOperatorAbbList(1,$ID,2);
	echo "</div>\n";
	echo "<div class=top10>";
	$mobile->getAdminOperatorAbbList(2,$ID,2);
	echo "</div>\n";
	echo "<div class=top10>";
	$mobile->getAdminOperatorAbbList(3,$ID,2);
	echo "</div>\n";
	
	echo "<div>";
	echo "</div>\n";
	
	include_once("footer.php");
?>
<?php 
	include_once("top.php");
	
	echo "<div class='top10'>";
	$csearch->getSearchLogg(1,false);
	$csearch->getSearchLogg(2,false);
	$csearch->getSearchLogg(3,false);
	echo "</div>\n";
	
?>
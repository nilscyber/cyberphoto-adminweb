<?php 
	include_once("top.php");
	include_once("header.php");
	
	if ($alltimehigh == "yes") {
		echo "<h1>Sålda \"All Time High\"</h1>\n";
	} else {
		echo "<h1>Sålda de senaste 30 dagarna</h1>\n";
	}
	include("mestsalda_choose.php");
	if ($kategori_nr > 0) {
		$sold->displaySoldArticles($kategori_nr,$alltimehigh);
	}
	
	include_once("footer.php");
?>
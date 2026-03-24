<?php 
	include_once("top.php");
	$manual_pagetitle = "Söksträngar som inte ger träffar på hemsidan";
	include_once("header.php");
	
	echo "<h1>Söksträngar som inte ger träffar på hemsidan - 1 månad bakåt</h1>\n";

	if ($show == "yes") {
		$csearch->getSearchWordsGroupDetail_v1($searchstring,0);
	} else {
		$csearch->getSearchWordsGroup_v1();
	}
	
	include_once("footer.php");
?>
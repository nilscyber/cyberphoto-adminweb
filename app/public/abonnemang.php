<?php 
	include_once("top.php");
	include_once("header.php");
	
	if (preg_match("/abonnemang_data\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<h1>Abonnemang - Data</h1>\n";
	} else {
		echo "<h1>Abonnemang - Mobil</h1>\n";
	}
	// echo "<p><img border=\"0\" src=\"/pic/help.gif\"><a href=\"javascript:winPopupCenter(250, 600, 'add_abonnemang.php');\"> Lägg till abonnemmang</a></p>\n";
	echo "<form>\n";
	include("mobil_choose.php");
	
	if (preg_match("/abonnemang_data\.php/i", $_SERVER['PHP_SELF'])) {

		echo "<div class=top10>";
		$mobile->getAdminOperatorAbbList(1,$ID,2);
		echo "</div>\n";
		echo "<div class=top10>";
		$mobile->getAdminOperatorAbbList(2,$ID,2);
		echo "</div>\n";
		echo "<div class=top10>";
		$mobile->getAdminOperatorAbbList(3,$ID,2);
		echo "</div>\n";
		
	} else {
		if ($operator_choose == "" || $operator_choose == "1") {
			echo "<div class=top10>";
			$mobile->getAdminOperatorAbbList(1,$ID,1);
			$mobile->getAdminOperatorAbbList(1,$ID,3);
			echo "</div>\n";
		}
		if ($operator_choose == "" || $operator_choose == "2") {
			echo "<div class=top10>";
			$mobile->getAdminOperatorAbbList(2,$ID,1);
			echo "</div>\n";
		}
		if ($operator_choose == "" || $operator_choose == "3") {
			echo "<div class=top10>";
			$mobile->getAdminOperatorAbbList(3,$ID,1);
			echo "</div>\n";
		}
		if ($operator_choose == "" || $operator_choose == "5") {
			echo "<div class=top10>";
			$mobile->getAdminOperatorAbbList(5,$ID,1);
			echo "</div>\n";
		}
	}
	echo "</form>\n";
	
	echo "<div>";
	echo "</div>\n";
	
	include_once("footer.php");
?>
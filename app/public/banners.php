<?php 
// error_reporting(E_ALL);
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Hantera banners</h1>\n";
	echo "<form method=\"GET\">\n";
	echo "<input type=\"hidden\" value=\"" . $_SESSION['bannersite'] . "\" name=\"choose_site\">\n";
	include("front_choosesite.php");
	if ($_SESSION['bannersite'] > 0) {
		include("front_choosedepartment.php");
	}
	if ($_SESSION['bannerdepartment'] > 0) {
		include("front_choosesection.php");
	}
	echo "</form>\n";
	
	if ($add == "yes" || $addid != "" || $addidc != "") {
		if ($wrongmess) {
			echo $wrongmess;
		}
		include("front_addform.php");
	}
	
	if ($confirmdelete != "") {
		echo "\n<div class=\"top10\"></div>\n";
		echo "<div><h2>Bekr�fta borttagning</h2></div>\n";
		echo "<div class=\"left5 floatleft\"><a href=\"" . $_SERVER['PHP_SELF'] . "?delete=$confirmdelete\"><img border=\"0\" src=\"confirm.gif\"></a></div>\n";
		echo "<div class=\"left10 floatleft\"><a href=\"" . $_SERVER['PHP_SELF'] . "\"><img border=\"0\" src=\"cancel.gif\"></a></div>\n";
		echo "<div class=\"clear\"></div>";
	
	}
	
	if ($_SESSION['bannersection'] > 0) {
		echo "\n<div class=\"top10\"></div>\n";
		echo "<div><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">L�gg till post p� denna sektion</a></div>\n";
	}
	
	if ($_SESSION['bannerdepartment'] > 0 && $_SESSION['bannersection'] > 0) {
		$banners->getBannerAdminNow($_SESSION['bannerdepartment'],$_SESSION['bannersection']);
		$banners->getBannerAdminNow($_SESSION['bannerdepartment'],$_SESSION['bannersection'],true);
	}
	// if ($choose_department > 0 && $choose_section < 1) {
	if ($choose_department > 0) {
		$banners->displayWarning($_SESSION['bannerdepartment']);
	}
	
	include_once("footer.php");
?>

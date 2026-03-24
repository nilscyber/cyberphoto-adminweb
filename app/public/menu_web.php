<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Hantera meny för webbshop</h1>\n";
	echo "<form method=\"GET\">\n";
	// echo "<input type=\"hidden\" value=\"" . $_SESSION['menudepartment'] . "\" name=\"choose_department\">\n";
	// include("menu_choosesite.php");
	include("menu_choosedepartment.php");
	echo "</form>\n";
	
	if ($add == "yes" || $addid != "" || $addidc != "") {
		if ($wrongmess) {
			echo $wrongmess;
		}
		include("menu_addform_v2.php");
	}
	
	if ($confirmdelete != "") {
		echo "\n<div class=\"top10\"></div>\n";
		echo "<div><h2>Bekräfta borttagning</h2></div>\n";
		echo "<div class=\"left5 floatleft\"><a href=\"" . $_SERVER['PHP_SELF'] . "?delete=$confirmdelete\"><img border=\"0\" src=\"confirm.gif\"></a></div>\n";
		echo "<div class=\"left10 floatleft\"><a href=\"" . $_SERVER['PHP_SELF'] . "\"><img border=\"0\" src=\"cancel.gif\"></a></div>\n";
		echo "<div class=\"clear\"></div>";
	
	}
	
	if ($_SESSION['menudepartment'] > 0) {
		echo "\n<div class=\"top10\"></div>\n";
		echo "<div><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">Lägg till post på denna meny</a></div>\n";
	}
	
	if ($_SESSION['menudepartment'] > 0) {
		echo "\n<div class=\"top20\"></div>\n";
		echo "\n<div id=\"menu_web\">\n";
		echo "\n<div class=\"left20\">\n";
		if ($_SESSION['menudepartment'] == 5) {
			$menu->getMenuAdmin('cybairgun');
		} elseif ($_SESSION['menudepartment'] == 4) {
			$menu->getMenuAdmin('jakt-fritid');
		} elseif ($_SESSION['menudepartment'] == 3) {
			$menu->getMenuAdmin('batterier');
		} elseif ($_SESSION['menudepartment'] == 2) {
			$menu->getMenuAdmin('mobiltelefoni');
		} else {
			$menu->getMenuAdmin('foto-video');
		}
		echo "\n</div>\n";
		echo "\n</div>\n";
	}
	
	include_once("footer.php");
?>
<?php 
	include_once("top.php");
	$manual_pagetitle = "Hantera alias och tillbehör";
	if ($origin == "yes") {
		echo "<head>\n";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
		$admin->displayPageTitle();
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"global.css?ver=ad" . date("ynjGi") . "\">\n";
		?>
		<style>

		body {
			background-color: #FFFFFF; 
			padding-left: 15px;
			background-image: none;
		}
		</style>
		<?php
		echo "</head>\n";
		if ($uppdate_ok) {
			echo "<body onload=\"top.opener.location.reload(true);window.close()\">\n";
		} else {
		echo "<body>\n";
		}
		include("add_accessories.php");
		echo "</body>\n";
		exit;
	} elseif (preg_match("/accessories_popup\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<head>\n";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
		$admin->displayPageTitle();
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"global.css?ver=ad" . date("ynjGi") . "\">\n";
		?>
		<style>

		body {
			background-color: #FFFFFF; 
			padding-left: 15px;
			background-image: none;
		}
		</style>
		<?php
		echo "<script language=\"javascript\">\n";
		echo "\tfunction sf()\n";
		echo "\t{\n";
		echo "\tdocument.log.addartnr.focus();\n";
		echo "\t}\n";
		echo "</script>\n";
		echo "</head>\n";
		if ($uppdate_ok) {
			echo "<body onload=\"top.opener.location.reload(true);window.close()\">\n";
		} elseif ($addart == "yes") {
			echo "<body onLoad=sf()>\n\n";
		} else {
		echo "<body>\n";
		}
	} else {
		include_once("header.php");
	}
	
	echo "<h1>" . $manual_pagetitle . "</h1>\n";
	
	if ($success == "yes") {
		echo "<h2 class=\"span_green\">Allt uppdaterades enligt önskemål och du är nu flyttad till ersättningsprodukten</h2>\n";
	}
	
	if ($addart == "" && $change == "") {	

		echo "<div class=\"\">\n";
		// echo "<div class=\"floatright right20\">\n";
		// echo "<div style=\"float: left; width: 210px;\">\n";
		echo "<form method=\"GET\">\n";
		echo "<input type=\"hidden\" value=\"yes\" name=\"search\">\n";
		// echo "</div>\n";
		echo "<input style=\"background-color: #FFFDD3; width: 420px;\" placeholder=\"Sök på ett tillbehör och visa vilka artiklar/alias det finns på\" type=\"text\" name=\"article\" value=\"" . $article . "\">\n";
		echo "\n";
		echo "\n";
		echo "</form>\n";
		if ($wrongmess) {
			echo $wrongmess;
		}
		echo "</div>\n";
		
	}

	if ($_GET['alias'] == "yes") {
		$namealias = $_GET['change'];
	}
	
	if ($addart == "yes" && $change != "") {	
		include("add_accessories.php");
	} elseif ($_GET['alias'] == "yes") {
		echo "<div>\n";
		echo "<img border=\"0\" src=\"/pic/help.gif\">&nbsp;\n";
		echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=" . $namealias . "&addart=yes\">Lägg till artikel i detta alias / tillbehör</a>\n";
		echo "</div>\n";
	}
	
	if ($_GET['alias'] == "yes") {
		$product->listProductsInAlias($_GET['change']);
		$product->listProductsWithAlias($_GET['change']);
	} elseif ($search == "yes" && $article != "") {
		$product->listAccessoriesOnProduct($_GET['article']);
	} else {
		$product->listAllAlias();
	}
	
	if (preg_match("/accessories_popup\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<p><span class=\"span_link\" onclick=\"top.opener.location.reload(true);window.close()\">Stäng fönster</span></p>\n";
		echo "</body>\n";
	} else {
		include_once("footer.php");
	}
?>
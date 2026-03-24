<?php 
	include_once("top.php");
	$manual_pagetitle = "Aktiva paket för " . $_GET['article'];
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
	} elseif (preg_match("/active_parcel\.php/i", $_SERVER['PHP_SELF'])) {
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
	

	
	$product->listAllPackages($_GET['article']);
	
	if (preg_match("/active_parcel\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<p><span class=\"span_link\" onclick=\"top.opener.location.reload(true);window.close()\">Stäng fönster</span></p>\n";
		echo "</body>\n";
	} else {
		include_once("footer.php");
	}
?>
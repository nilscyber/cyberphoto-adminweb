<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Innehållshanteringssystem</h1>\n";
	// echo "här: " . $addactive; 
	// echo $department;
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	
	if ($add == "yes" || $addid != "") {
		if ($_COOKIE['login_ok'] != "true") {
			echo "<div class=\"container_loggin\">\n";
			echo "<span class=\"not_loggin\">Du är Ej inloggad och kommer därför inte kunna utföra åtgärden!</span>\n";
			echo "</div>\n";
			echo "<div class=\"clear\"></div>\n";
		}
		echo "<form method=\"POST\" action=\"cms.php\">\n";
		if ($addid !="") {
			echo "<input type=\"hidden\" value=\"$addid\" name=\"addid\">\n";
			echo "<input type=\"hidden\" value=true name=\"submC\">\n";
		} else {
			echo "<input type=\"hidden\" value=\"yes\" name=\"add\">\n";
			echo "<input type=\"hidden\" value=true name=\"subm\">\n";
		}
		echo "<div><input type=\"text\" name=\"headline\" placeholder=\"Skriv in rubriken här\" size=\"60\" value=\"$headline\"></div>";
		echo "<div class=\"top10\">";
		echo "<select name=\"department\">\n";
		echo "<option value=\"\">Välj vilken ram som skall laddas</option>\n";
		if ($department == "alla") {
			echo "<option value=\"alla\" selected>Laddas i default ram (ej specifiserat)</option>\n";
		} else {
			echo "<option value=\"alla\">Laddas i default ram (ej specifiserat)</option>\n";
		}
		if ($department == "foto-video") {
			echo "<option value=\"foto-video\" selected>foto-video (röd)</option>\n";
		} else {
			echo "<option value=\"foto-video\">foto-video (röd)</option>\n";
		}
		
		if ($department == "mobiltelefoni") {
			echo "<option value=\"mobiltelefoni\" selected>mobiltelefoni (blå)</option>\n";
		} else {
			echo "<option value=\"mobiltelefoni\">mobiltelefoni (blå)</option>\n";
		}
		if ($department == "batterier") {
			echo "<option value=\"batterier\" selected>batterier (orange)</option>\n";
		} else {
			echo "<option value=\"batterier\">batterier (orange)</option>\n";
		}
		if ($department == "outdoor") {
			echo "<option value=\"outdoor\" selected>outdoor (grön)</option>\n";
		} else {
			echo "<option value=\"outdoor\">outdoor (grön)</option>\n";
		}
		/*
		if ($department == "cybairgun") {
			echo "<option value=\"cybairgun\" selected>cybairgun (cammo)</option>\n";
		} else {
			echo "<option value=\"cybairgun\">cybairgun (cammo)</option>\n";
		}
		*/
		echo "</select>\n";
		echo "</div>";
		if ($addactive != 0) {
			echo "<div class=\"top10\"><input type=\"checkbox\" name=\"addactive\" value=\"yes\" checked> Bocka i om sidan skall visas externt</div>\n";
		} else {
			echo "<div class=\"top10\"><input type=\"checkbox\" name=\"addactive\" value=\"yes\"> Bocka i om sidan skall visas externt</div>\n";
		}
		echo "<div class=\"top10\"><textarea id=\"elm1\" name=\"area\">$area</textarea></div>\n";
		echo "<div class=\"top10\"><textarea style=\"display: none;\" name=\"area_backup\">$area</textarea></div>\n";
		echo "</form>\n";
	}
	if ($add != "yes" && $addid == "") {

		echo "<div><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<b><a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">Skapa en ny sida</b></a></div>\n";
		
		$cms->getCmsListAdmin("alla");
		$cms->getCmsListAdmin("foto-video");
		$cms->getCmsListAdmin("mobiltelefoni");
		$cms->getCmsListAdmin("batterier");
		$cms->getCmsListAdmin("outdoor");
		// $cms->getCmsListAdmin("cybairgun");
	
	}
	
	include_once("footer.php");
?>
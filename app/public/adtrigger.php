<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Kontroll av länkar för annonser</h1>\n";
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($confirmdelete != "") {
		echo "<div id=\"layerconfirm\">\n";
		include ("confirm_pricedelete.php");
		echo "</div>\n";
	}

	if ($add == "yes" || $addid != "") {
		include("adtrigger_add.php");
	}
	if ($group != "") {
		echo "<h2>Detaljer av annonsgrupp</h2>\n";
		echo "<div class=top10>\n";
		echo "Detaljer av annonsgrupp: \n";
		echo "<b><font color=\"#0000FF\">" . $group . "</font>: " . $adtrigger->getLoggTotGroup($group) . " st, avslut " . $adtrigger->getBuyTotGroup($group) . "\n";
		echo "st = " . round(($adtrigger->getBuyTotGroup($group) / $adtrigger->getLoggTotGroup($group) * 100),2) . "%</b></font>\n";
		echo "</div>\n";
	}
	if ($add != "yes") {
		if ($show == "") {
			echo "<div class=top10><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<b><a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">Lägg till länk</b></a></div>\n";
			echo "<h2>Aktuella länkar</h2>\n";
			$adtrigger->getAd();
			echo "<h2>Planerade länkar</h2>\n";
			$adtrigger->getAdPlan();
			echo "<h2>Tidigare länkar</h2>\n";
			$adtrigger->getAdHistory();
		}
		if ($show != "") {
			echo "<h2>Detaljer av vald länk</h2>\n";
			$adtrigger->getAdDetail($show);
			if ($adtrigger->getLoggTot($show) > 0) {
				echo "<div class=top10>\n";
				$adtrigger->getLoggWiz($show);
				echo "</div>\n";
				echo "<div class=top10>\n";
				echo "Klick på denna annons/banner: \n";
				echo "<b>" . $adtrigger->getLoggTot($show) . " ggr, antal avslut " . $adtrigger->getBuyTot($show) . " st = \n";
				echo round(($adtrigger->getBuyTot($show) / $adtrigger->getLoggTot($show) * 100),2) . "%</b>\n";
				echo "</div>\n";
				echo "<div class=top10>\n";
				$adtrigger->getLogg($show);
				echo "</div>\n";
				if ($detail != "") {
					echo "<div class=top10>\n";
					$adtrigger->getLoggDetail($show,$detail);
					echo "</div>\n";
				}
			}
		}
	}
	
	include_once("footer.php");
?>
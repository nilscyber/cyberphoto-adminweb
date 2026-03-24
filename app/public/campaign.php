<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Hantera kampanjer</h1>\n";
	/*
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($confirmdelete != "") {
		echo "<div id=\"layerconfirm\">\n";
		include ("confirm_pricedelete.php");
		echo "</div>\n";
	}
	*/

	if ($add == "yes" || $addid != "" || $copyid != "") {
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo "erer";
			include("campaign_add.php");
		} else {
			include("campaign_add.php");
		}
	}
	
	
	
	if ($add != "yes" && $addid == "" && $copyid == "") {
		
		if ($show == "") {
			$campaign->getLastCampaigns(true);
		}
		
		// if ($show == "" && $discountCode == "") {
		if ($show == "") {
			echo "<div class=\"floatright right20\">\n";
			// echo "<div style=\"float: left; width: 210px;\">\n";
			echo "<form method=\"GET\">\n";
			if ($personal_discount == "yes") {
				echo "Visa personliga rabattkoder<input type=\"checkbox\" name=\"personal_discount\" value=\"yes\" onclick=\"submit()\" checked>\n";
			} else {
				echo "Visa personliga rabattkoder<input type=\"checkbox\" name=\"personal_discount\" value=\"yes\" onclick=\"submit()\">\n";
			}
			// echo "</div>\n";
			echo "<input placeholder=\"S�k artikelnr\" type=\"text\" name=\"article\" size=\"20\" value=\"" . $article . "\">\n";
			echo "\n";
			echo "\n";
			echo "</form>\n";
			if ($wrongmess) {
				echo $wrongmess;
			}
			echo "</div>\n";
			echo "<div><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<b><a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">Skapa en ny kampanj</b></a></div>\n";
			echo "<h2>Aktuella kampanjer</h2>\n";
			$campaign->getCampaign(1,$personal_discount);
			echo "<hr class=\"hr_blue\">\n";
			echo "<h2>Planerade kampanjer</h2>\n";
			$campaign->getCampaign(2,$personal_discount);
			echo "<hr class=\"hr_blue\">\n";
			if ($article != "") {
				echo "<h2>Utg�ngna kampanjer (upp till 12 m�nader gamla)</h2>\n";
			} else {
				echo "<h2>Utg�ngna kampanjer (upp till 12 m�nader gamla)</h2>\n";
			}
			$campaign->getCampaign(3,$personal_discount);
		}
		// if ($show != "" || $discountCode != "") {
		if ($show != "") {
			// echo "<h2>Detaljer f�r kampanjen <span style=\"color:blue\">$discountCode</span></h2>\n";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.78x") {
				echo "<h5>show: $show discountcode: $discountCode</h5>\n";
			}
			// $campaign->getCampaignDetailAdmin($show,$discountCode);
			$campaign->getCampaignDetailAdmin($show);
		}
	}
	
	include_once("footer.php");
?>
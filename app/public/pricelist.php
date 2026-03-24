<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Prislistor</h1>\n";
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($confirmdelete != "") {
		echo "<div id=\"layerconfirm\">\n";
		include ("confirm_pricedelete.php");
		echo "</div>\n";
	}

	if ($add == "yes" || $addid != "") {
		include("pricelist_add.php");
	}
	if ($add != "yes") {
		if ($show == "") {
			echo "<div><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<b><a href=\"" . $_SERVER['PHP_SELF'] . "?add=yes\">L�gg till prislista</b></a></div>\n";
			echo "<h2>Aktuella prislistor</h2>\n";
			$price->getPriceListActual();
			echo "<h2>Planerade prislistor</h2>\n";
			$price->getPriceListActual(true,false);
			// $price->getPriceListPlan();
			echo "<h2>Utg�ngna prislistor</h2>\n";
			$price->getPriceListActual(false,true);
			// $price->getPriceListHistory();
		}
		if ($show != "") {
			echo "<h2>Detaljer av vald prislista</h2>\n";
			$price->getPriceListActualDetail($show);

			echo "<div class=top10>\n";
			$price->getPriceListArtnr($show);
			echo "</div>\n";
			if ($price->checkIfKat($show)) {
				echo "<div class=top10>&nbsp;<img border=\"0\" src=\"plus.jpg\">&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?addart=yes&show=$show\">L�gg till <u><b>kategori</b></u> till prislistan</font></a></div>\n";
			} else {
				echo "<div class=top10>&nbsp;<img border=\"0\" src=\"plus.jpg\">&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?addart=yes&show=$show\">L�gg till <u><b>artikel</b></u> till prislistan</font></a></div>\n";
			}
			if ($addart == "yes") {
				include("pricelistarticle_add.php");
			}
		}
	}
	
	include_once("footer.php");
?>

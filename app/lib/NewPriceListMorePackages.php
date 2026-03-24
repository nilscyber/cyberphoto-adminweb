<?php
	include_once("translate.php");
	require_once("CCheckIpNumber.php");
	require_once("CStyleCode.php");
	require_once("CBasket.php");
	$style = new CStyleCode();
	$bask = new CBasket();

	# PHP Pricelist script
	# author	Stefan Sjöberg
	# version	2013-10-21

	unset($current_category);
	$countproducts = 0;
	echo "<div id=\"price_container\">\n";
	
	while ($row = @mysqli_fetch_object($res)) {

		$artnr3 = $row->artnr;
		// echo $row->artnr;
		// echo $artnr3;
		
		echo "<div id=\"". $artnr3 . "\" style=\"border:0px solid #FFFFFF; display:none;\">\n";
		include ("package_detail.php");
		echo "</div>\n";
		// echo $artnr;
		
		if ($row->kategori != $current_category) {
			$current_category = $row->kategori;
			if ($fi && !$sv) {
				echo "<h3>$row->kategori_fi</h3>";
			} else {
				echo "<h3>$row->kategori</h3>";
			}
		}

		if ($row->IsSalesBundle == -1) {
			$prodlinc = "<span onclick=\"show_hide('$artnr3');\" style=\"cursor:pointer;\">\n";
		} else {
			$prodlinc = "/info.php?article=" . $artnr3 . "&infArtnr=" . $artnr;
			$prodlinc = "<a href=\"javascript:goTo('" . $prodlinc . "', '" . $artnr . "', true)\">";
		}
		$buylinc = "<a href=\"javascript:modifyItems('" . $artnr3 . "')\">";
		if ($row->showbild != "") {
			$picture = "<img border =\"0\" src=\"/thumbs/medium/bilder/" . $row->showbild . "\">";
		}
		if ($row->tillverkare <> ".") {
           $header = $row->tillverkare . " ";
		}
		if ($row->beskrivningKort) {
			$header .= $row->beskrivningKort;
		} else {
			if ($fi) {
				if ($row->beskrivning_fi != "") {
					$header .= $row->beskrivning_fi;
				} else {
					$header .= $row->beskrivning;
				}
			} else {
				$header .= $row->beskrivning;
			}
		}
		if ($row->comment != "" && $sv) {
				$header .= " (<i>" . $row->comment . "</i>)";
		}

		$header .= $style->StyleNewProduct_v2($row->artnr,2,$row->demo);
		if ($fi) {
			if ($row->kommentar_fi && !$sv) {
				$header .= " " . $style->StyleText_v2($row->kommentar_fi);
			}
		} else {
			if ($row->kommentar) {
				$header .= " " . $style->StyleText_v2($row->kommentar);
			}
		}
		if ($row->campaignLink == 1) {
			$campaigntext = $bask->getDiscountInfo($row->artnr,1,$row->tillverkar_id,$row->kategori_id, '', true); // artnr, showstyle (1=text, 2=bild), tillverkare, kategori, nyaprislistan
		}
		
		if ($fi) {
			$currency = "EUR";
		} elseif ($no) {
			$currency = "NOK";
		} else {
			$currency = "SEK";
		}
		if ($fi) {
			if ($row->utpris_fi > 0) {
				$price = ($row->utpris_fi + $row->utpris_fi * $row->momssats_fi);
				$price = number_format($price, 0, ',', ' ') . " " . $currency;
			} else {
				if ($sv) {
					$price = "ej prissatt";
				} else {
					$price = "ei hinnoiteltu";
				}
			}
		} elseif ($no) {
			// echo $row->momssats_no;
			if ($row->utpris_no > 0) {
				$price = ($row->utpris_no + $row->utpris_no * $row->momssats_no);
				$price = number_format($price, 0, ',', ' ') . " " . $currency;
			} else {
					$price = "ikke priset";
			}
		} else {
			if ($row->utpris > 0) {
				$price = ($row->utpris + $row->utpris * $row->momssats);
				$price = number_format($price, 0, ',', ' ') . " " . $currency;
			} else {
					$price = "ej prissatt";
			}
		}
	
		echo "<div class=\"price_row\">\n";
		if ($row->showbild != "") {
			echo $prodlinc . "<div class=\"box_img\">" . $picture . "</div></span>\n";
		} else {
			echo $prodlinc . "<div class=\"box_img\">";
			echo "<div class=\"butt_picture\">" . l('Picture missing') . "</div>";
			echo "</div></span>\n";
		}
		echo $prodlinc . "<div class=\"box_header\">\n";
		echo "<div class=\"product_name\">" . $header . "</div>";
		if ($campaigntext != "") {
			echo "<div class=\"product_name\">" . $campaigntext . "</div>";
		}
		if ($row->IsSalesBundle == -1) {
			echo "<div class=\"mark_cyber\">(". l('show details on the package') . ")</div>";
		}
		echo "</div></span></a>\n";
		echo "<div class=\"box_test\">\n";
		echo "</div>\n";
		echo "<div class=\"box_price\">\n";
		echo "<div class=\"product_price\">" . $price . "</div>";
		if ($row->IsSalesBundle == -1) {
			echo "<div class=\"product_status\"></div>";
		} elseif ($row->lagersaldo > 0) {
			echo "<div class=\"product_status\">" . l('In stock') . "</div>";
		} elseif ($row->lagersaldo <= 0 && $row->bestallningsgrans > 0) {
			echo "<div class=\"product_status\">" . l('Out of stock') . "</div>";
		} else {
			echo "<div class=\"product_status\">" . l('Non stock item') . "</div>";
		}
		echo "</div>\n";
		echo "<div class=\"box_buy\">\n";
		if ($row->no_buy == 0) {
			echo $buylinc . "<div class=\"buy_butt_green\">" . l('Buy') . "</div></a>\n";
		} else {
			echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
		}
		echo "</div>\n";
		echo "<div class=\"clear\"></div>\n";
		echo "</div>\n";
		
		unset($campaigntext);
		unset($header);
		unset($artnr3);
		$countproducts++;
		if ($countproducts == 200 && preg_match("/searchsimple/i", $_SERVER['PHP_SELF'])) {
			break;
		}
	}
	echo "<div class=\"box_count\">" . l('Quantity') . ": " . $countproducts . " " . l('pcs') . "</div>\n";	
	echo "</div>\n";	

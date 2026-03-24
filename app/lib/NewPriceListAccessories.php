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
	if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
		if (strlen($passartill1) > 1) {
			echo "<div id=\"accessories_container\">\n";
			echo "<p class=\"bold italic bottom5\">Följande alias finns kopplade till produkten:</span></p>\n";
			// echo "<a target=\"_blank\" href=\"/order/admin/accessories.php?alias=yes&change=" . $passartill1 . "\">" . $passartill1 . "</a><br>";
			// echo "<a  href=\"javascript:winPopupCenter(850, 1400, '/order/admin/accessories_popup.php?alias=yes&change=" . $passartill1 . "');\">" . $passartill1 . "</a><br>";
			echo "<a  href=\"javascript:winPopupCenter(850, 1400, 'https://admin.cyberphoto.se/accessories_popup.php?alias=yes&change=" . $passartill1 . "');\">" . $passartill1 . "</a><br>";
			if (strlen(passartill2) > 1) {
				// echo "<a target=\"_blank\" href=\"/order/admin/accessories.php?alias=yes&change=" . $passartill2 . "\">" . $passartill2 . "</a><br>";
				// echo "<a  href=\"javascript:winPopupCenter(850, 1400, '/order/admin/accessories_popup.php?alias=yes&change=" . $passartill2 . "');\">" . $passartill2 . "</a><br>";
				echo "<a  href=\"javascript:winPopupCenter(850, 1400, 'https://admin.cyberphoto.se/accessories_popup.php?alias=yes&change=" . $passartill2 . "');\">" . $passartill2 . "</a><br>";
			}
			if (strlen($passartill3) > 1) {
				// echo "<a target=\"_blank\" href=\"/order/admin/accessories.php?alias=yes&change=" . $passartill3 . "\">" . $passartill3 . "</a><br>";
				// echo "<a  href=\"javascript:winPopupCenter(850, 1400, '/order/admin/accessories_popup.php?alias=yes&change=" . $passartill3 . "');\">" . $passartill3 . "</a><br>";
				echo "<a  href=\"javascript:winPopupCenter(850, 1400, 'https://admin.cyberphoto.se/accessories_popup.php?alias=yes&change=" . $passartill3 . "');\">" . $passartill3 . "</a><br>";
			}
			if (strlen($passartill4) > 1) {
				// echo "<a target=\"_blank\" href=\"/order/admin/accessories.php?alias=yes&change=" . $passartill4 . "\">" . $passartill4 . "</a><br>";
				// echo "<a  href=\"javascript:winPopupCenter(850, 1400, '/order/admin/accessories_popup.php?alias=yes&change=" . $passartill4 . "');\">" . $passartill4 . "</a><br>";
				echo "<a  href=\"javascript:winPopupCenter(850, 1400, 'https://admin.cyberphoto.se/accessories_popup.php?alias=yes&change=" . $passartill4 . "');\">" . $passartill4 . "</a><br>";
			}
			if (strlen($passartill5) > 1) {
				// echo "<a target=\"_blank\" href=\"/order/admin/accessories.php?alias=yes&change=" . $passartill5 . "\">" . $passartill5 . "</a><br>";
				// echo "<a  href=\"javascript:winPopupCenter(850, 1400, '/order/admin/accessories_popup.php?alias=yes&change=" . $passartill5 . "');\">" . $passartill5 . "</a><br>";
				echo "<a  href=\"javascript:winPopupCenter(850, 1400, 'https://admin.cyberphoto.se/accessories_popup.php?alias=yes&change=" . $passartill5 . "');\">" . $passartill5 . "</a><br>";
			}
			if (strlen($passartill6) > 1) {
				// echo "<a target=\"_blank\" href=\"/order/admin/accessories.php?alias=yes&change=" . $passartill6 . "\">" . $passartill6 . "</a><br>";
				// echo "<a  href=\"javascript:winPopupCenter(850, 1400, '/order/admin/accessories_popup.php?alias=yes&change=" . $passartill6 . "');\">" . $passartill6 . "</a><br>";
				echo "<a  href=\"javascript:winPopupCenter(850, 1400, 'https://admin.cyberphoto.se/accessories_popup.php?alias=yes&change=" . $passartill6 . "');\">" . $passartill6 . "</a><br>";
			}
			if (strlen($passartill7) > 1) {
				// echo "<a target=\"_blank\" href=\"/order/admin/accessories.php?alias=yes&change=" . $passartill7 . "\">" . $passartill7 . "</a><br>";
				// echo "<a  href=\"javascript:winPopupCenter(850, 1400, '/order/admin/accessories_popup.php?alias=yes&change=" . $passartill7 . "');\">" . $passartill7 . "</a><br>";
				echo "<a  href=\"javascript:winPopupCenter(850, 1400, 'https://admin.cyberphoto.se/accessories_popup.php?alias=yes&change=" . $passartill7 . "');\">" . $passartill7 . "</a><br>";
			}
			echo "</div>\n";
			// echo "<div class=\"left5 top5\"><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<a target=\"_blank\" href=\"/order/admin/accessories.php?alias=yes&change=" . $artnr . "&addart=yes\">Lägg till/ta bort tillbehör till denna produkt</a></div>\n";
			// echo "<div class=\"left5 top5\"><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<a href=\"javascript:winPopupCenter(850, 1400, '/order/admin/accessories_popup.php?alias=yes&change=" . $artnr . "&addart=yes');\">Lägg till/ta bort tillbehör till denna produkt</a></div>\n";
			echo "<div class=\"left5 top5\"><img border=\"0\" src=\"/pic/help.gif\">&nbsp;<a href=\"javascript:winPopupCenter(850, 1400, 'https://admin.cyberphoto.se/accessories_popup.php?alias=yes&change=" . $artnr . "&addart=yes');\">Lägg till/ta bort tillbehör till denna produkt</a></div>\n";
		}
	}
	echo "<div id=\"price_container\">\n";
	
	while ($row = @mysqli_fetch_object($res)) {
		
		$artnr2 = $row->artnr;
		if ($row->kategori != $current_category) {
			$current_category = $row->kategori;
			if ($fi && !$sv) {
				echo "<h3>$row->kategori_fi</h3>";
			} else {
				echo "<h3>$row->kategori</h3>";
			}
		}

        if (preg_match("/pri_blackpatroner/i", $_SERVER['PHP_SELF'])) {
			if ($fi && !$sv) {
				$prodlinc = "/info_fi.php?article=" . $artnr2;
			} elseif ($fi && $sv) {
				$prodlinc = "/info_fi_se.php?article=" . $artnr2;
			} else {
				$prodlinc = "/info.php?article=" . $artnr2;
			}
			$prodlinc = "<a href=\"$prodlinc\">";
		
		} else {
			if ($fi && !$sv) {
				$prodlinc = "/info_fi.php?article=" . $artnr2 . "&infArtnr=" . $artnr;
			} elseif ($fi && $sv) {
				$prodlinc = "/info_fi_se.php?article=" . $artnr2 . "&infArtnr=" . $artnr;
			} else {
				$prodlinc = "/info.php?article=" . $artnr2 . "&infArtnr=" . $artnr;
			}
			$prodlinc = "<a href=\"javascript:goTo('" . $prodlinc . "', '" . $artnr . "', true)\">";
		}
		
		if ($frameless) {
			$prodlinc = preg_replace("/info\_fi\_se\.php/", "info.php", $prodlinc);
			$prodlinc = preg_replace("/info\_fi\.php/", "info.php", $prodlinc);
		}
		
		$buylinc = "<a href=\"javascript:modifyItems('$artnr2')\">";
		if ($row->showbild != "") {
			$picture = "<img border =\"0\" src=\"/thumbs/medium/bilder/" . $row->showbild . "\">";
		}
		if ($row->tillverkare <> ".") {
           $header = $row->tillverkare . " ";
		}
		if ($row->beskrivningKort) {
			$header .= $row->beskrivningKort;
		} else {
			if ($fi && !$sv) {
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
			if ($row->isParent == -1) {
				$price = number_format($product->getChildProductPrice($row->artnr), 0, ',', ' ') . " " . $currency;
			} elseif ($row->utpris_fi > 0) {
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
			if ($row->isParent == -1) {
				$price = number_format($product->getChildProductPrice($row->artnr), 0, ',', ' ') . " " . $currency;
			} elseif ($row->utpris_no > 0) {
				$price = ($row->utpris_no + $row->utpris_no * $row->momssats_no);
				$price = number_format($price, 0, ',', ' ') . " " . $currency;
			} else {
					$price = "ikke priset";
			}
		} else {
			if ($row->isParent == -1) {
				$price = number_format($product->getChildProductPrice($row->artnr), 0, ',', ' ') . " " . $currency;
			} elseif ($row->utpris > 0) {
				$price = ($row->utpris + $row->utpris * $row->momssats);
				$price = number_format($price, 0, ',', ' ') . " " . $currency;
			} else {
					$price = "ej prissatt";
			}
		}
	
		echo "<div class=\"price_row\">\n";
		if ($row->showbild != "") {
			echo $prodlinc . "<div class=\"box_img\">" . $picture . "</div></a>\n";
		} else {
			echo $prodlinc . "<div class=\"box_img\">";
			echo "<div class=\"butt_picture\">" . l('Picture missing') . "</div>";
			echo "</div></a>\n";
		}
		echo $prodlinc . "<div class=\"box_header\">\n";
		echo "<div class=\"product_name\">" . $header . "</div>";
		if ($campaigntext != "") {
			echo "<div class=\"product_name\">" . $campaigntext . "</div>";
		}
		$product->getExtraComment($row->artnr,false); // visa extra kommentar
		$product->getExtraComment($row->artnr,false,true); // visa kommentar helgkampanj
		echo "</div></a>\n";
		echo "<div class=\"box_test\">\n";
		echo "</div>\n";
		echo "<div class=\"box_price\">\n";
		if ($row->isParent == -1) {
			echo "<div class=\"product_price\"><span class=\"from_price\">" . strtolower(l('From price')). "</span> " . $price . "</div>";
		} else {
			echo "<div class=\"product_price\">" . $price . "</div>";
		}
		if ($row->artnr == "forsakring") { // ta bort lagerstatus för försäkringen
			echo $prodlinc . "<div class=\"product_status\">&nbsp;</div></a>";
		} elseif ($row->isParent == -1) {
			echo $prodlinc . "<div class=\"product_status\">" . l('Click for info') . "</div></a>";
		} elseif ($row->IsSalesBundle == -1) {
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
		if ($row->isParent == -1) {
			echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Buy') . "</div></a>\n";
		} elseif ($row->no_buy == 0) {
			echo $buylinc . "<div class=\"buy_butt_green\">" . l('Buy') . "</div></a>\n";
		} else {
			echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
		}
		echo "</div>\n";
		echo "<div class=\"clear\"></div>\n";
		if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			echo "<div class=\"box_artnr\"><div>" . $row->artnr . "</div></div>";
			echo "<div class=\"box_update_product\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$row->artnr&m_product_id=$row->m_product_id');\">Uppdatera produkten</a></div>";
			if ($row->lagersaldo > 0) {
				echo "<div class=\"box_lagersaldo\">" . $row->lagersaldo . "</div>";
			} else {
				echo "<div class=\"box_lagersaldo\">&nbsp;</div>";
			}
			if ($row->isParent == -1) {
				echo "<div class=\"box_margin\"></div>";
			} else {
				echo "<div class=\"box_margin\">" . $pricelist->getMargin($row->artnr) . "</div>";
			}
			if ($row->utgangen == -1) {
				echo "<div class=\"mark_red italic\">Utgången produkt</div>";
			}
			echo "<div class=\"clear\"></div>\n";
			if ($row->ej_med == -1) {
				echo "<div class=\"mark_red italic bold uppercase\">OBS! Denna produkt ligger \"Ej Med\" i vårt system och visas därför INTE ut mot kund!</div>";
			}
		}
		echo "</div>\n";
		
		unset($campaigntext);
		unset($header);
		$countproducts++;
		if ($countproducts == 200 && preg_match("/searchsimple/i", $_SERVER['PHP_SELF'])) {
			break;
		}
	}
	echo "<div class=\"box_count\">" . l('Quantity') . ": " . $countproducts . " " . l('pcs') . "</div>\n";	
	echo "</div>\n";	

<?php
	# PHP Pricelist script
	# author	Stefan Sj?berg
	# version	2013-10-21

	$internview = false;
	$arts = new CArticleFunctions();

	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.78x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.85x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.86x"
		|| $_SERVER['REMOTE_ADDR'] == "192.168.1.88x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.93x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.94x"
		|| $_SERVER['REMOTE_ADDR'] == "192.168.1.89x") { // om internt
		
		$internview = true;
		
	}
	
	// echo $criteria;

	# Read articles using criteria
	# if $criteria is not defined, all Articles will be selected therefore:
	if ($critiera = "") {
		$criteria = "WHERE Artiklar.kategori_id=0"; 
	}

	$articles = $arts->readArticlesWithCriteria($criteria);
	$countarticles = count($articles);
	$sum_articles_instore = 0;
	$sum_count_on_queue = 0;

	# Reset category
	unset($current_category);
	unset($current_betyg);
	$countproducts = 0;
	if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) { // om inloggad höjer vi max träffar till 400. Ej inloggade ser 200 som max
		$max_search = 400;
	} else {
		$max_search = 200;
	}
	
	// H?r l?gger vi in alla sorteringsverktyg
	include ("SortPricelistNew.php");
	
	echo "<div id=\"price_container\">\n";
	if ($headline) {
		echo "<h2>$headline</h2>";
	}
	if ($countarticles == 0 && preg_match("/search/i", $_SERVER['PHP_SELF']) && !$fi && !$no) {
		if (strlen($showSearch) <= 50) {
			$csearch->loggSearch($_SERVER['REMOTE_ADDR'],$showSearch,0,$_SERVER["HTTP_HOST"],$beskrivning);
		}
		echo "<p>" . l('Your search for') . " $showSearch " . l('yielded no results.') . "</p>\n";
	}
	if ($countarticles > 0 && preg_match("/search/i", $_SERVER['PHP_SELF'])) {
		$csearch->loggSearch($_SERVER['REMOTE_ADDR'],$showSearch,1,$_SERVER["HTTP_HOST"],$beskrivning);
	}
	if ($countarticles > $max_search && preg_match("/search/i", $_SERVER['PHP_SELF'])) {
		if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			echo "<p>" . l('Your search resulted in') . " <b>$countarticles</b> " . l('hits. Below are the 400 most relevant products.') . "</p>\n";
		} else {
			echo "<p>" . l('Your search resulted in') . " <b>$countarticles</b> " . l('hits. Below are the 200 most relevant products.') . "</p>\n";
		}
	}
	while (list($key, $article) = each($articles)) {
		
		if (($article->kategori != $current_category) && !$headline && $article->kategori_id != 396 && $article->kategori_id != 486 && $article->kategori_id != 513) {
			if ($plist == "gallery_row" || $_SESSION['gallery_row']) { // om anv?ndaren valt galleril?ge m?ste vi k?ra denna f?r att bryta rubriken till ny rad
				echo "<div class=\"clear\"></div>\n";
			}
			$current_category = $article->kategori;
			if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) && (preg_match("/search/i", $_SERVER['PHP_SELF']) || preg_match("/price_mobil/i", $_SERVER['PHP_SELF'])) && ($article->kategori_id == 1000260 || $article->kategori_id == 1000267 || $article->kategori_id == 1000270)) {
				if ($fi && !$sv) {
					echo "<h2>$article->kategori_fi ($article->kategori_id) - <span class=\"mark_blue\">Denna kategori hittar inte kunderna vid s?kning</span></h2>";
				} elseif ($no && $article->kategori_no != "") {
					echo "<h2>$article->kategori_no ($article->kategori_id) - <span class=\"mark_blue\">Denna kategori hittar inte kunderna vid s?kning</span></h2>";
				} else {
					echo "<h2>$article->kategori ($article->kategori_id) - <span class=\"mark_blue\">Denna kategori hittar inte kunderna vid s?kning</span></h2>";
				}
			} elseif (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
				if ($fi && !$sv) {
					echo "<h2>$article->kategori_fi ($article->kategori_id)</h2>";
				} elseif ($no && $article->kategori_no != "") {
					echo "<h2>$article->kategori_no ($article->kategori_id)</h2>";
				} else {
					echo "<h2>$article->kategori ($article->kategori_id)</h2>";
				}
			} else {
				if ($fi && !$sv) {
					echo "<h2>$article->kategori_fi</h2>";
				} elseif ($no && $article->kategori_no != "") {
					echo "<h2>$article->kategori_no</h2>";
				} else {
					echo "<h2>$article->kategori</h2>";
				}
			}
		}

		if ($article->tillverkare <> ".") {
           $header = $article->tillverkare . " ";
           $headerFU = $article->tillverkare . " ";
		}
		if ($article->beskrivningKort) {
			$header .= $article->beskrivningKort;
			$headerFU .= $article->beskrivningKort;
		} else {
			if ($fi && !$sv) {
				if ($article->beskrivning_fi != "") {
					$header .= $article->beskrivning_fi;
					$headerFU .= $article->beskrivning_fi;
				} else {
					$header .= $article->beskrivning;
					$headerFU .= $article->beskrivning;
				}
			} elseif ($no) {
				if ($article->beskrivning_no != "") {
					$header .= $article->beskrivning_no;
					$headerFU .= $article->beskrivning_no;
				} else {
					$header .= $article->beskrivning;
					$headerFU .= $article->beskrivning;
				}
			} else {
				$header .= $article->beskrivning;
				$headerFU .= $article->beskrivning;
			}
		}

		$header = $style->StyleText_v2($header);
		$header .= $style->StyleNewProduct_v2($article->artnr,2,$article->demo);
		if ($fi && !$sv) {
			if ($article->kommentar_fi != "") {
				$header .= " " . $style->StyleText_v2($article->kommentar_fi);
			}
		} elseif ($no) {
			if ($article->kommentar_no != "") {
				$header .= " " . $style->StyleText_v2($article->kommentar_no);
			} elseif ($article->kommentar) {
				$header .= " " . $style->StyleText_v2($article->kommentar);
			}
		} else {
			if ($article->kommentar) {
				$header .= " " . $style->StyleText_v2($article->kommentar);
			}
		}
		
		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.seX') {
			
			// $prodlinc = "<a href=\"" . $prodlinc . "\">";
			$prodlinc = CProduct::getProductFriendlyURL($article->artnr);
			$prodlinc = "<a href=\"" . $prodlinc . "\">";
			$prodlincPRINT = "http://" . $_SERVER["HTTP_HOST"] . "" . $prodlinc;
			$prodlincOLD = "<a target=\"_blank\" href=\"/info.php?article=" . $article->artnr . "\">http://" . $_SERVER["HTTP_HOST"] . "/info.php?article=" . $article->artnr . "</a>";
			
		} else {
			$prodlinc = "<a href=\"/info.php?article=" . $article->artnr . "\">";
		}
		$buylinc = "<a href=\"javascript:modifyItems('$article->artnr')\">";
		if ($article->bild != "") {
			if ($plist == "gallery_row" || $_SESSION['gallery_row']) { // om anv?ndaren valt galleril?ge
				$picture = "<img border =\"0\" src=\"/thumbs/xxlarge/bilder/" . $article->bild . "\">";
			} else {
				$picture = "<img border =\"0\" src=\"/thumbs/medium/bilder/" . $article->bild . "\">";
			}
		}
		/*
		if ($article->campaignLink == 1 || preg_match("/pri_selected/i", $_SERVER['PHP_SELF']) || preg_match("/pri_campaign/i", $_SERVER['PHP_SELF'])) {
			$campaigntext = $bask->getDiscountInfo($article->artnr,1,$article->tillverkar_id,$article->kategori_id, '', true); // artnr, showstyle (1=text, 2=bild), tillverkare, kategori, nyaprislistan
		}
		*/
		$campaigntext = $bask->getDiscountInfo($article->artnr,1,$article->tillverkar_id,$article->kategori_id, '', true); // artnr, showstyle (1=text, 2=bild), tillverkare, kategori, nyaprislistan
		
		if ($fi) {
			$currency = "EUR";
		} elseif ($no) {
			$currency = "NOK";
		} else {
			$currency = "SEK";
		}
		

		
		if ($fi) {
			if ($article->isParent == -1) {
				$price = number_format($product->getChildProductPrice($article->artnr), 0, ',', ' ') . " " . $currency;
			} elseif ($article->utpris_fi > 0) {
				$price = ($article->utpris_fi + $article->utpris_fi * $article->momssats_fi);
				$price = number_format($price, 0, ',', ' ') . " " . $currency;
			} else {
				if ($sv) {
					$price = "ej prissatt";
				} else {
					$price = "ei hinnoiteltu";
				}
			}
		} elseif ($no) {
			if ($article->isParent == -1) {
				$price = number_format($product->getChildProductPrice($article->artnr), 0, ',', ' ') . " " . $currency;
			} elseif ($article->utpris_no > 0) {
				$price = ($article->utpris_no + $article->utpris_no * $article->momssats_no);
				$price = number_format($price, 0, ',', ' ') . " " . $currency;
			} else {
					$price = "ikke priset";
			}
		} else {
			if ($article->isParent == -1) {
				$price = number_format($product->getChildProductPrice($article->artnr), 0, ',', ' ') . " " . $currency;
			} elseif ($article->utpris > 0) {
				$price = ($article->utpris + $article->utpris * $article->momssats);
				$price = number_format($price, 0, ',', ' ') . " " . $currency;
			} else {
					$price = "ej prissatt";
			}
		}
		
		if (preg_match("/price_archive/i", $_SERVER['PHP_SELF']) || preg_match("/pri_archive/i", $_SERVER['PHP_SELF'])) { // om arkivet

			echo "<div class=\"price_row\">\n";
			if ($article->bild != "") {
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
			echo "</div></a>\n";
			echo "<div class=\"box_test\">\n";
			if ($article->betyg > 0 && ($testresult == "yes" || $tested == "yes" || $sortera == "testresultat")) {
				$style->displayOurTestReviewPricelist($article->betyg);
			}
			echo "</div>\n";
			echo "<div class=\"box_price\">\n";
			echo "</div>\n";
			echo "<div class=\"box_buy\">\n";
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
			echo "</div>\n";
			echo "<div class=\"clear\"></div>\n";
			echo "</div>\n";

		} elseif ($internview || $plist == "admin_row" || $_SESSION['admin_row']) { // om internt

			echo "<div class=\"internal_price_row\">\n";
			
			echo $prodlinc . "<div class=\"internal_box_header\">\n";
			echo "<div class=\"product_name\">" . $header . "</div>";
			if ($campaigntext != "") {
				echo "<div class=\"product_name\">" . $campaigntext . "</div>";
			}
			$product->getExtraComment($article->artnr,false); // visa extra kommentar
			echo "</div></a>\n";

			echo "<div class=\"internal_box_test\">\n";
			if (preg_match("/pri_case/i", $_SERVER['PHP_SELF'])) {
				if ($article->filterd != NULL) {
					echo $article->filterd . " cm";
				} else {
					echo "&nbsp;\n";
				}
			} elseif (preg_match("/pri_objektiv/i", $_SERVER['PHP_SELF'])) {
				if ($article->filterd != NULL) {
					if ($article->filterd == "99") {
						echo "--";
					} else {
						echo $article->filterd;
					}
				} else {
					echo "&nbsp;\n";
				}
				echo "&nbsp;/&nbsp;";
				if ($article->motljsk != NULL) {
					echo $article->motljsk;
				} else {
					echo "&nbsp;\n";
				}
			} elseif ($article->betyg > 0 && ($testresult == "yes" || $tested == "yes" || $sortera == "testresultat")) {
				$style->displayOurTestReviewPricelist($article->betyg);
			} else {
				echo "&nbsp;\n";
			}
			echo "</div>\n";
			echo "<div class=\"internal_box_price\">\n";
			echo "<div class=\"internal_product_price\">" . $price . "</div>";
			echo "</div>\n";
			
			echo "<div class=\"internal_box_comment\">\n";
			$product->getExtraComment($article->artnr,true); // visa extra kommentar
			$product->getExtraComment($article->artnr,false,true); // visa kommentar helgkampanj
			echo "</div>\n";

			echo "<div class=\"internal_box_status\">\n";
			if ($article->isSalesBundle == -1) {
				echo "<div class=\"internal_product_status\"></div>";
			} elseif ($article->lagersaldo > 0) {
				// echo "<div class=\"internal_product_status\">" . l('In stock') . "</div>";
				echo "<div class=\"internal_product_status\"><img border=\"0\" src=\"/images/store_green.png\"></div>";
			} elseif ($article->lagersaldo <= 0 && $article->bestallningsgrans > 0) {
				// echo "<div class=\"internal_product_status\">" . l('Out of stock') . "</div>";
				echo "<div class=\"internal_product_status\"><img border=\"0\" src=\"/images/store_orange.png\"></div>";
			} else {
				// echo "<div class=\"internal_product_status\">" . l('Non stock item') . "</div>";
				echo "<div class=\"internal_product_status\"><img border=\"0\" src=\"/images/store_grey.png\"></div>";
			}
			echo "</div></a>\n";
			
			echo "<div class=\"clear\"></div>\n";
			echo "</div>\n";
		
		} elseif ($plist == "plain_row" || $_SESSION['plain_row']) { // om anv?ndaren valt kompakt prislistan

			echo "<div class=\"price_row\">\n";
			echo $prodlinc . "<div class=\"plain_box_header\">\n";
			echo "<div class=\"product_name\">" . $header . "</div>";
			if ($campaigntext != "") {
				echo "<div class=\"product_name\">" . $campaigntext . "</div>";
			}
			$product->getExtraComment($article->artnr,false); // visa extra kommentar
			$product->getExtraComment($article->artnr,false,true); // visa kommentar helgkampanj
			echo "</div></a>\n";
			echo "<div class=\"plain_box_test\">\n";
			if ($article->betyg > 0 && ($testresult == "yes" || $tested == "yes" || $sortera == "testresultat")) {
				$style->displayOurTestReviewPricelist($article->betyg);
			}
			echo "</div>\n";
			echo "<div class=\"plain_box_price\">\n";
			if ($article->isParent == -1) {
				echo "<div class=\"plain_product_price\"><span class=\"from_price\">" . strtolower(l('From price')). "</span> " . $price . "</div>";
			} else {
				if ($article->kategori_id == 1000010 && $article->utpris < 1) {
					echo "<div class=\"plain_product_price\">0 SEK</div>";
				} else {
					echo "<div class=\"plain_product_price\">" . $price . "</div>";
				}
			}
			echo "</div>\n";
			echo "<div class=\"plain_box_status\">\n";
			if ($article->isSalesBundle == -1) {
				echo "<div class=\"plain_product_status\">";
				$bask->check_lager($article->artnr);
				echo "</div>";
				// echo $prodlinc . "<div class=\"plain_product_status\">" . l('Click for info') . "</div></a>";
			} elseif ($article->isParent == -1) {
				echo $prodlinc . "<div class=\"plain_product_status\">" . l('Click for info') . "</div></a>";
			} elseif ($article->lagersaldo > 0) {
				echo "<div class=\"plain_product_status\">" . l('In stock') . "</div>";
			} elseif ($article->lagersaldo <= 0 && $article->bestallningsgrans > 0) {
				echo "<div class=\"plain_product_status\">" . l('Out of stock') . "</div>";
			} else {
				// echo "<div class=\"plain_product_status\">" . l('Non stock item') . "</div>";
				echo "<div class=\"plain_product_status\"><a onMouseOver=\"this.T_FONTSIZE='12px';this.T_PADDING=5;this.T_STATIC=true;this.T_SHADOWWIDTH=2;this.T_WIDTH=400;this.T_BGCOLOR='#FFF8BA';this.T_TEXTALIGN='left';return escape('" . l('Delivery info onmouseover') . "')\">" . l('Delivery info') . "</a></div>";
			}
			echo "</div>\n";
			echo "<div class=\"plain_box_buy\">\n";
			if ($article->isParent == -1) {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Buy') . "</div></a>\n";
			} elseif ($article->isSalesBundle == -1 && (($article->utpris == 0 && $sv) || ($article->utpris_no == 0 && $no))) {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
			} elseif (($article->utpris_fi == 0 && $fi) || ($article->utpris_no == 0 && $no)) {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
			// } elseif (($article->no_buy == 0 && $sv) || ($article->no_buy_fi == 0 && $fi) || ($article->no_buy_no == 0 && $no)) {
			} elseif ($article->no_buy == 0) {
				echo $buylinc . "<div class=\"buy_butt_green\">" . l('Buy') . "</div></a>\n";
			} else {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
			}
			echo "</div>\n";
			echo "<div class=\"clear\"></div>\n";
			echo "</div>\n";
		
		} elseif ($plist == "gallery_row" || $_SESSION['gallery_row']) { // om anv?ndaren valt galleril?ge
			echo "<div class=\"gallery_row\">";

			echo "<div class=\"gallery_box_header\">\n";
			echo $prodlinc . "<div class=\"gallery_product_name\">" . $header . "</div></a>";
			if ($campaigntext != "") {
				echo $prodlinc . "<div class=\"gallery_product_name\">" . $campaigntext . "</div></a>";
			}
			$product->getExtraComment($article->artnr,false); // visa extra kommentar
			$product->getExtraComment($article->artnr,false,true); // visa kommentar helgkampanj
			echo "</div>\n";
			// if ($article->bild != "") {
			if ($article->bild == "XxX") {
				if ($article->betyg > 0 && ($testresult == "yes" || $tested == "yes" || $sortera == "testresultat")) {
					echo $prodlinc . "<div class=\"gallery_box_img_test\">\n";
					echo $picture;
					$style->displayOurTestReviewPricelist($article->betyg);
					echo "</div></a>\n";
				} else {
					echo $prodlinc . "<div class=\"gallery_box_img\">\n";
					echo $picture;
					echo "</div></a>\n";
				}
			} else {
				echo $prodlinc . "<div class=\"gallery_box_img\">";
				echo "<div class=\"butt_picture_gallery\">" . l('Picture missing') . "</div>";
				echo "</div></a>\n";
			}
			echo "<div class=\"gallery_box_price\">\n";
			if ($article->isParent == -1) {
				echo "<div class=\"product_price\"><span class=\"from_price\">" . strtolower(l('From price')). "</span> " . $price . "</div>";
			} else {
				if ($article->kategori_id == 1000010 && $article->utpris < 1) {
					echo "<div class=\"product_price\">0 SEK</div>";
				} else {
					echo "<div class=\"product_price\">" . $price . "</div>";
				}
			}
			if (preg_match("/price_newproducts/i", $_SERVER['PHP_SELF'])) { // om sidan ?ver nya produkter, ta bort lagerstatus
				echo "<div class=\"product_status\">&nbsp;</div>";
			} elseif ($article->isParent == -1) {
				echo $prodlinc . "<div class=\"product_status\">" . l('Click for info') . "</div></a>";
			} elseif ($article->isSalesBundle == -1) {
				echo "<div class=\"product_status\">";
				$bask->check_lager($article->artnr);
				echo "</div>";
				// echo $prodlinc . "<div class=\"product_status\">" . l('Click for info') . "</div></a>";
			} elseif ($article->lagersaldo > 0) {
				echo "<div class=\"product_status\">" . l('In stock') . "</div>";
			} elseif ($article->lagersaldo <= 0 && $article->bestallningsgrans > 0) {
				echo "<div class=\"product_status\">" . l('Out of stock') . "</div>";
			} else {
				// echo "<div class=\"product_status\">" . l('Non stock item') . "</div>";
				echo "<div class=\"product_status\"><a onMouseOver=\"this.T_FONTSIZE='12px';this.T_PADDING=5;this.T_STATIC=true;this.T_SHADOWWIDTH=2;this.T_WIDTH=400;this.T_BGCOLOR='#FFF8BA';this.T_TEXTALIGN='left';return escape('" . l('Delivery info onmouseover') . "')\">" . l('Delivery info') . "</a></div>";
			}
			echo "</div>\n";
			echo "<div class=\"gallery_box_buy\">\n";
			if ($article->isParent == -1) {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Buy') . "</div></a>\n";
			} elseif ($article->isSalesBundle == -1 && (($article->utpris == 0 && $sv) || ($article->utpris_no == 0 && $no))) {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
			} elseif (($article->utpris_fi == 0 && $fi) || ($article->utpris_no == 0 && $no)) {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
			// } elseif (($article->no_buy == 0 && $sv) || ($article->no_buy_fi == 0 && $fi) || ($article->no_buy_no == 0 && $no)) {
			} elseif ($article->no_buy == 0) {
				echo $buylinc . "<div class=\"buy_butt_green\">" . l('Buy') . "</div></a>\n";
			} else {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
			}
			echo "</div>\n";
			echo "<div class=\"clear\"></div>\n";
			
			
			echo "</div>\n";
			
		} else { // annars visas den "vanliga" prislistan
	
			echo "<div class=\"price_row\">\n";
			// if ($article->bild != "") {
			if ($article->bild == "XxX") {
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
			$product->getExtraComment($article->artnr,false); // visa extra kommentar
			$product->getExtraComment($article->artnr,false,true); // visa kommentar helgkampanj
			echo "</div></a>\n";
			
			if ($article->spec13 == -1 && $sortera != "testresultat") {
				echo "<div class=\"box_braval\">\n";
				$style->displayRecommended();
				echo "</div>\n";
			} else {
				echo "<div class=\"box_test\">\n";
				if ($article->betyg > 0 && ($testresult == "yes" || $tested == "yes" || $sortera == "testresultat")) {
					$style->displayOurTestReviewPricelist($article->betyg);
				}
				echo "</div>\n";
			}
			echo "<div class=\"box_price\">\n";
			if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
				if ($article->isParent == -1) {
					// echo "";
					echo "<div class=\"product_price\"><span class=\"from_price\">" . strtolower(l('From price')). "</span> " . $price . "</div>";
				} else {
					if ($article->kategori_id == 1000010 && $article->utpris < 1) {
						echo "<a class=\"product_price\" href=\"javascript:winPopupCenter(750, 800, '/order/product_update_history.php?artnr=$article->artnr&m_product_id=$article->m_product_id');\">0 SEK</a>";
					} else {
						if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.sex') {
							// echo "<a class=\"product_price\" href=\"javascript:winPopupCenter(750, 800, '/order/product_update_history.php?artnr=$article->artnr&m_product_id=$article->m_product_id');\">" . $price . "</a>";
							echo "<a class=\"product_price\" href=\"javascript:winPopupCenter(750, 800, '/order/product_update_history.php?artnr=$article->artnr&m_product_id=$article->m_product_id');\">" . $article->momssats . "</a>";
						} else {
							echo "<a class=\"product_price\" href=\"javascript:winPopupCenter(750, 800, '/order/product_update_history.php?artnr=$article->artnr&m_product_id=$article->m_product_id');\">" . $price . "</a>";
						}
					}
				}
			} else {
				if ($article->isParent == -1) {
					echo "<div class=\"product_price\"><span class=\"from_price\">" . strtolower(l('From price')). "</span> " . $price . "</div>";
				} else {
					if ($article->kategori_id == 1000010 && $article->utpris < 1) {
						echo "<div class=\"product_price\">0 SEK</div>";
					} else {
						echo "<div class=\"product_price\">" . $price . "</div>";
					}
				}
			}
			
			if ($article->isParent == -1) {
				echo $prodlinc . "<div class=\"product_status\">" . l('Click for info') . "</div></a>";
			} elseif ($article->isSalesBundle == -1) {
				echo "<div class=\"product_status\">";
				$bask->check_lager($article->artnr);
				echo "</div>";
				// echo $prodlinc . "<div class=\"product_status\">" . l('Click for info') . "</div></a>";
			} elseif ($article->lagersaldo > 0) {
				echo "<div class=\"product_status\">" . l('In stock') . "</div>";
			} elseif ($article->lagersaldo <= 0 && $article->bestallningsgrans > 0) {
				echo "<div class=\"product_status\">" . l('Out of stock') . "</div>";
			} else {
				// echo "<div class=\"product_status\">" . l('Non stock item') . "</div>";
				echo "<div class=\"product_status\"><a onMouseOver=\"this.T_FONTSIZE='12px';this.T_PADDING=5;this.T_STATIC=true;this.T_SHADOWWIDTH=2;this.T_WIDTH=400;this.T_BGCOLOR='#FFF8BA';this.T_TEXTALIGN='left';return escape('" . l('Delivery info onmouseover') . "')\">" . l('Delivery info') . "</a></div>";
			}
			echo "</div>\n";
			echo "<div class=\"box_buy\">\n";
			if ($article->isParent == -1) {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Buy') . "</div></a>\n";
			} elseif ($article->isSalesBundle == -1 && (($article->utpris == 0 && $sv) || ($article->utpris_no == 0 && $no))) {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
			} elseif (($article->utpris_fi == 0 && $fi) || ($article->utpris_no == 0 && $no)) {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
			// } elseif (($article->no_buy == 0 && $sv) || ($article->no_buy_fi == 0 && $fi) || ($article->no_buy_no == 0 && $no)) {
			} elseif ($article->no_buy == 0) {
				echo $buylinc . "<div class=\"buy_butt_green\">" . l('Buy') . "</div></a>\n";
			} else {
				echo $prodlinc . "<div class=\"buy_butt_green\">" . l('Info') . "</div></a>\n";
			}
			echo "</div>\n";
			echo "<div class=\"clear\"></div>\n";
			if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
				echo "<div id=\"internal_panel_pricelist\">\n";
				echo "<div class=\"box_artnr\"><div>" . $article->artnr . "</div></div>";
				// echo "<div class=\"box_update_product\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$article->artnr&m_product_id=$article->m_product_id');\">Uppdatera</a></div>";
				echo "<div class=\"box_update_product\"><a href=\"javascript:winPopupCenter(900, 800, 'https://admin.cyberphoto.se/product_update.php?artnr=$article->artnr&m_product_id=$article->m_product_id');\">Uppdatera</a></div>";
				if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se' || CCheckIP::checkIfLoginIsPriority()) {
					// echo "<div class=\"box_sold_articles\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$article->artnr&m_product_id=$article->m_product_id&hcampaign=yes');\">HELG</a> -</div>";
					echo "<div class=\"box_sold_articles\"><a href=\"#\" onclick=\"window.open('https://admin.cyberphoto.se/product_feedback.php?popup=1&artnr=$article->artnr&ordernr=', 'feedbackPopup', 'width=750,height=600'); return false;\">Rapportera</a> -</div>";
				}
				if (CCheckIP::checkIfLoginIsTradeIn() || $_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
					// echo "<div class=\"box_sold_articles\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$article->artnr&m_product_id=$article->m_product_id&hcampaign=yes');\">HELG</a> -</div>";
					// echo "<div class=\"box_sold_articles\"><a href=\"javascript:winPopupCenter(150, 400, '/order/admin/add_campaign_article.php?article=$article->artnr');\">KID</a> -</div>";
					echo "<div class=\"box_sold_articles\"><a target=\"_blank\" href=\"https://admin.cyberphoto.se/monitor_articles.php?add=yes&addArtnr=$article->artnr\">Bevaka</a> -</div>";
				}
				if ($article->isSalesBundle == -1) {
					// echo "<div class=\"box_sold_articles\"><a href=\"javascript:winPopupCenter(600, 1000, '/order/antalsalda.php?artnr=$article->artnr&show_salesbundle=yes');\">S?lda</a> -</div>";
					echo "<div class=\"box_sold_articles\"><a href=\"javascript:winPopupCenter(600, 1000, 'https://admin.cyberphoto.se/antalsalda.php?artnr=$article->artnr&show_salesbundle=yes');\">Sålda</a> -</div>";
				} else {
					echo "<div class=\"box_sold_articles\"><a href=\"javascript:winPopupCenter(600, 1000, 'https://admin.cyberphoto.se/antalsalda.php?artnr=$article->artnr');\">Sålda</a> -</div>";
				}
				// echo "<div class=\"box_suplier_info\">- <a href=\"javascript:winPopupCenter(550, 500, '/order/suplier.php?artnr=$article->artnr');\">Leverant?r</a> -</div>";
				echo "<div class=\"box_suplier_info\"><a href=\"javascript:winPopupCenter(550, 500, 'https://admin.cyberphoto.se/suplier.php?artnr=$article->artnr');\">Leverantör</a> -</div>";
				echo "<div class=\"extra_info_box\">&nbsp;\n";
				// $product->getExtraComment($article->artnr,true); // visa extra kommentar
				echo "</div>\n";
				if ($article->lagersaldo > 0) {
					echo "<div class=\"box_lagersaldo\">L: " . $article->lagersaldo . "</div>";
				} else {
					echo "<div class=\"box_lagersaldo\">&nbsp;</div>";
				}
				if ($article->isParent == -1) {
					echo "<div class=\"box_margin\">&nbsp;</div>";
				} else {
					// tagit bort marginal d? det ?r tungt att visa
					echo "<div class=\"box_margin\">" . $pricelist->getMargin2($article) . "</div>";
					//echo "<div class=\"box_margin\">&nbsp;</div>";
				}
				if ($article->utgangen == -1) {
					echo "<div class=\"box_discontinued mark_red italic bold\">Utgången</div>";
				}
				echo "<div class=\"clear\"></div>\n";
				if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.seX') {
					echo "<div class=\"box_update_product\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>";
					echo "<div class=\"box_sold_articles\"><a href=\"javascript:winPopupCenter(150, 400, '/order/admin/add_campaign_article.php?article=$article->artnr');\">+Kampanj</a></div>";
				}
				if ($article->isSalesBundle != -1 && (CCheckIP::checkIfLoginIsTradeIn() || CCheckIP::checkIfLoginIsPriority() || $_COOKIE['login_mail'] == 'stefan@cyberphoto.se')) {
					if ($article->demo == -1) {
						$now = time(); // or your date as well
						$your_date = strtotime($article->date_add);
						$datediff = $now - $your_date;
						$datediff2 = floor($datediff / (60 * 60 * 24));
						if ($datediff2 > 89) {
							 if ($sortera == "old_tradein") {
								echo "<div class=\"box_artnr\"><span class=\"mark_red bold\">" . $datediff2 . " dagar</span></div>";
							 } else {
								echo "<div class=\"box_artnr\"><span class=\"mark_red bold blink_text_red\">" . $datediff2 . " dagar</span></div>";
							 }
						} elseif ($datediff2 > 59) {	
							 if ($sortera == "old_tradein") {
								echo "<div class=\"box_artnr\"><span class=\"mark_blue bold\">" . $datediff2 . " dagar</span></div>";
							 } else {
								echo "<div class=\"box_artnr\"><span class=\"mark_blue bold blink_text_blue\">" . $datediff2 . " dagar</span></div>";
							 }
						} else {
							echo "<div class=\"box_artnr mark_green\">" . $datediff2 . " dagar</div>";
						}
						
					} else {
						echo "<div class=\"box_artnr\">&nbsp;</div>";
					}
					if ($show_soldarticles == "yes" || $countarticles <= 30) {
						echo "<div class=\"box_lagersaldo\"><a href=\"javascript:winPopupCenter(600, 1000, 'https://admin.cyberphoto.se/antalsalda.php?artnr=" . $article->artnr . "');\" onMouseOver=\"this.T_WIDTH=300;this.T_BGCOLOR='#FFFF00';this.T_TEXTALIGN='center';return escape('<b>S?lda senaste 30 dagarna, klicka f?r mer detaljer</b>')\" style=\"text-decoration: none\">" . $bask->soldLastMonthPricelist($article->artnr,$article->lagersaldo) . "</a></div>";
					} else {
						echo "<div class=\"box_lagersaldo\">&nbsp;</div>";
					}
					if ($show_queue == "yes" || $countarticles <= 30) {
						$count_on_queue = $adintern->displayOnQueuePricelist($article->artnr);
						if ($count_on_queue > 0 ) {
							// echo "<div class=\"box_margin\"><a href=\"javascript:winPopupCenter(600, 1000, '/order/antalsalda.php?artnr=" . $article->artnr . "');\" onMouseOver=\"this.T_WIDTH=300;this.T_BGCOLOR='#FFFF00';this.T_TEXTALIGN='center';return escape('<b>S?lda senaste 30 dagarna, klicka f?r mer detaljer</b>')\" style=\"text-decoration: none\">" . $adintern->displayOnQueuePricelist($article->artnr) . "</a></div>";
							// echo "<div class=\"box_margin\">" . $adintern->displayOnQueuePricelist($article->artnr) . "</div>";
							echo "<div class=\"box_margin\"><a class=\"mark_blue\" href=\"javascript:winPopupCenter(600, 1000, 'https://admin.cyberphoto.se/waitinglist.php?artnr=$article->artnr');\">K: " . $count_on_queue . "</a></div>";
						} else {
							echo "<div class=\"box_margin\">&nbsp;</div>";
						}
					} else {
						echo "<div class=\"box_margin\">&nbsp;</div>";
					}
					if (CCheckIP::checkIfLoginIsTradeIn() || $_COOKIE['login_mail'] == 'stefan@cyberphoto.se' || CCheckIP::checkIfLoginIsPriority()) {
						echo "<div class=\"box_netto\">" . $pricelist->getMargin($article->artnr,true) . "</div>";
						// echo "<div class=\"box_accessories\"><a class=\"mark_purple\" href=\"javascript:winPopupCenter(850, 1400, '/order/admin/accessories_popup.php?alias=yes&change=" . $article->artnr . "&addart=yes');\">Tillbeh?r</a></div>";
						if ($article->momssats == 0.00) {
							echo "<div class=\"box_accessories\">VMB</div>";
						}
						if ($article->upc != "" && ($article->kategori_id == 336 || $article->kategori_id == 748)) {
							echo "<div class=\"box_accessories\">- <a target=\"_blank\" class=\"\" href=\"http://www.prisjakt.nu/#rparams=ss=" . $article->upc . "\">Prisjakt</a></div>";
						}
					}
					echo "<div class=\"clear\"></div>\n";
				} else {
					echo "<div class=\"clear\"></div>\n";
				}

				if (CCheckIP::checkIfLoginIsTradeIn() && $article->ej_med == -1 && $article->isTradeIn == -1 && $article->lagersaldo > 0) {
					if ($article->momssats != 0.00) {
						echo "<div class=\"italic align_left top5 mark_green\">Klar att sälja</div>";
					}elseif ($datediff2 < 30 && $article->spec12 == -1) {
						echo "<div class=\"italic align_left top5 mark_red\">Långmarinering</div>";
					} elseif ($datediff2 < 7 && $article->spec12 == 0) {
						echo "<div class=\"italic align_left top5 mark_red\">Kortmarinering</div>";
					} else {
						echo "<div class=\"italic align_left top5 mark_green\">Klar att sälja</div>";
					}
				}
				if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
					// echo "<div class=\"italic align_left top5\">" . $prodlincOLD . " / " . $product->getProductFriendlyURL($article->artnr) . "</div>";
					echo "<div class=\"italic align_left top5\">" . $prodlincOLD . "</div>";
				}
				
				if ($article->ej_med == -1 && !$fi && !$no) {
					echo "<div class=\"mark_red italic bold uppercase align_center top5\">OBS! Denna produkt ligger \"Ej Med\" i vårt system och visas därför INTE ut mot kund!</div>";
				}
				if (($fi && $article->ej_med_fi == -1) || ($no && $article->ej_med_no == -1)) {
					echo "<div class=\"mark_red italic bold uppercase align_center top5\">OBS! Denna produkt ligger \"Ej Med\" i vårt system och visas därför INTE ut mot kund!</div>";
				}
				if ($article->ej_med == 0 && $article->demo == -1 && $article->utpris < 1) {
					echo "<div class=\"mark_blue italic bold uppercase align_center top5\">OBS! Denna produkt är en demoprodukt som inte är prissatt. Däremed visas INTE denna ut mot kund!</div>";
				}
				echo "</div>\n"; // END DIV internal_panel_pricelist
			}
			echo "</div>\n";
			
		}
		
		unset($campaigntext);
		unset($header);
		$countproducts++;
		if ($article->lagersaldo > 0) {
			$sum_articles_instore = $sum_articles_instore + $article->lagersaldo;
		}
		if ($count_on_queue > 0) {
			$sum_count_on_queue = $sum_count_on_queue + $count_on_queue;
			unset($count_on_queue);
		}
		if ($countproducts == $max_search && preg_match("/search/i", $_SERVER['PHP_SELF'])) {
			break;
		}
	}
	if ($plist == "gallery_row" || $_SESSION['gallery_row']) { // om anv?ndaren valt galleril?ge
		echo "<div class=\"clear\"></div>\n";
	}
	if ($countarticles > 0) {
		echo "<div class=\"box_count\">" . l('Quantity') . ": " . $countproducts . " " . l('pcs') . "</div>\n";	
		if ($sum_articles_instore > 0 && CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			echo "<div class=\"box_count\">Tillg?ngliga p? hyllan: <b>" . $sum_articles_instore . " " . l('pcs') . "</b> (syns endast om inloggad)</div>\n";	
		}
		if ($sum_count_on_queue > 0 && CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			echo "<div class=\"box_count\">Antal p? k?: <span class=\"mark_blue bold\">" . $sum_count_on_queue . " " . l('pcs') . "</span> (syns endast om inloggad)</div>\n";	
		}
	} elseif ($countarticles < 1 && preg_match("/search/i", $_SERVER['PHP_SELF'])) {
		echo "<div class=\"box_count_empty\"></div>\n";	
	} elseif ($countarticles < 1 && $show_mobile_filter) {
		echo "<div class=\"box_count_empty_mobile italic mark_blue\">" . l('Sorry, there are no mobile phones with this combination.') . "</div>\n";	
	} elseif ($countarticles < 1 && $show_kikare_filter) {
		echo "<div class=\"box_count_empty_mobile italic mark_blue\">" . l('Sorry, there are no binocular with this combination.') . "</div>\n";	
	} else {
		/*
		if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			$pricelist->displayCategories($show);
		} else {
			echo "<div class=\"box_count_empty\"></div>\n";	
		}
		*/
		$pricelist->displayCategories($show);
	}
	echo "</div>\n";	

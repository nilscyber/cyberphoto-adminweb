<?php

include_once 'Db.php';

Class CProduct {

	function getArticleName($artnr) {
		global $sv, $fi, $no;
		
		$select  = "SELECT tillverkare, beskrivning ";
		$select .= "FROM Artiklar ";
		$select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			return $row->tillverkare . " " . $row->beskrivning;
		} else {
			return;
		}
	}

	function getMorePackages($artnr,$detail) {
	
		$select  = "SELECT artnr ";
		$select .= "FROM cyberphoto.Passartill ";
		$select .= "WHERE recommended = 200 AND passartill = '" . $artnr . "'";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
	
		if (mysqli_num_rows($res) > 0) {
			while ($row = mysqli_fetch_object($res)) {
				$this->showMorePackages($row->artnr,$detail);
			}
		}
		
	}
	
	function showMorePackages($artnr,$detail) {
	
		global $sv, $fi, $no, $artnr2, $ip_adress, $count, $lagersaldo, $bask, $products, $product, $style;
		$numberpac = 2;
		
		if ($detail) {
			echo "<div id=\"". $artnr . "\" style=\"display:none; WIDTH:450px; POSITION:absolute; TOP:125px;\">\n";
			include ("package_detail.php");
			echo "</div>";
		} else {
			include ("/web/www/cgi-bin/buybutton.php");
		}
	
	}
	
	function makeProductUpdate($m_product_id,$addfrom,$add_country,$check_addprice,$addprice_VAT,$check_showweb,$showweb,$check_utgangen,$utgangen,$check_name,$addname,$check_comment,$addcomment,$check_priceshape = null,$priceshape = null) {
		// global $m_product_id,$addfrom,$add_country,$check_addprice,$addprice_VAT,$check_showweb,$showweb,$check_utgangen,$utgangen,$check_name,$addname,$check_comment,$addcomment;
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			// echo $addname;
			echo $_COOKIE['login_name'] . "<br>";
			echo $check_priceshape;
			exit;
		}
		include_once 'ADempiere/ProductUpdate_v2.php';
		try {
			$pr = new ProductUpdate();
		} catch (Exception $e) {
			return false;
		}
		
		if ($_COOKIE['login_ok'] != "true" && false) {
			echo "Du är inte längre inloggad och därför ej behörig att utföra denna åtgärd...";
			exit;
		} elseif ($_COOKIE['login_userid'] == 99) {
			echo "Du har inte behörighet att utföra denna åtgärd...";
			exit;
		} else {
			$salesrep_id = $_COOKIE['login_userid'];
		}
		
		$inserttime = date("Y-m-d H:i:s", time());
		
		if ($check_utgangen == "yes") {
			if ($utgangen == "yes") {
				$pr->setIsDiscontinued(true);
			} else {
				$pr->setIsDiscontinued(false);
			}
		}
		if ($check_showweb == "yes") {
			if ($showweb == "yes") {
				$pr->setIsSelfService(true);
			} else {
				$pr->setIsSelfService(false);
			}
		}
		if ($check_priceshape == "yes") {
			if ($priceshape == "yes") {
				$pr->setIsExclAutoPricing(true);
			} else {
				$pr->setIsExclAutoPricing(false);
			}
		}
		// $pr->setName('Nytt namn2');
		// $pr->setDescription("beskrivning2");
		// $pr->setNameShort('Kort namn');
		if ($check_addprice == "yes") {
			// $pr->setPriceLimit($addprice_VAT);
			$pr->setPriceList($addprice_VAT);
			$pr->setPriceStd($addprice_VAT);
		}
		if ($add_country == "3") {
			$pr->setPriceListId(ProductUpdate::PRICELIST_NORWAY);
		} elseif ($add_country == "2") {
			$pr->setPriceListId(ProductUpdate::PRICELIST_FINLAND);
		} else {
			$pr->setPriceListId(ProductUpdate::PRICELIST_SWEDEN);
		}
		$pr->setProductId($m_product_id);
		$pr->setUpdatedBy($salesrep_id);
		$pr->setSalesRepId($salesrep_id);
		$pr->setUpdateTime($addfrom);
		if ($check_name == "yes") {
			$pr->setName($addname);
		}
		if ($check_comment == "yes") {
			$pr->setDescription($addcomment);
		}
		if (($check_utgangen == "yes" || $check_showweb == "yes" || $check_name == "yes" || $check_comment == "yes") && ($add_country == "2" || $add_country == "3")) {
			if ($add_country == "3") {
				$pr->setAdLanguage(ProductUpdate::AD_LANG_NO);
			} else {
				$pr->setAdLanguage(ProductUpdate::AD_LANG_FI);
			}
		}
		
		try {
			$res = $pr->add();
		} catch (Exception $e) {
			return false;
		}
		return ($res === true);
	}

	function changeProductUpdate($m_product_update_id,$m_product_id,$addfrom,$add_country,$check_addprice,$addprice_VAT,$check_showweb,$showweb,$check_utgangen,$utgangen,$check_name,$addname,$check_comment,$addcomment,$check_priceshape,$priceshape) {
		// global $m_product_update_id,$m_product_id,$addfrom,$add_country,$check_addprice,$addprice_VAT,$check_showweb,$showweb,$check_utgangen,$utgangen,$check_name,$addname,$check_comment,$addcomment;
	
		if ($_COOKIE['login_ok'] != "true") {
			echo "Du är inte längre inloggad och därför ej behörig att utföra denna åtgärd...";
			exit;
		} elseif ($_COOKIE['login_userid'] == 99) {
			echo "Du har inte behörighet att utföra denna åtgärd...";
			exit;
		} else {
			$salesrep_id = $_COOKIE['login_userid'];
		}

		$updt  = "UPDATE adempiere.m_product_update ";
		$updt .= "SET ";
		if ($add_country == "3") {
			$updt .= "m_pricelist_id = '1000280', ";
		} elseif ($add_country == "2") {
			$updt .= "m_pricelist_id = '1000018', ";
		} else {
			$updt .= "m_pricelist_id = '1000000', ";
		}
		if ($check_addprice == "yes") {
			$updt .= "pricelist = '" . $addprice_VAT . "', ";
			$updt .= "pricestd = '" . $addprice_VAT . "', ";
		}
		if ($check_name == "yes") {
			$updt .= "isupdtname = 'Y', ";
			$updt .= "name = '" . $addname . "', ";
		}
		if ($check_comment == "yes") {
			if ($addcomment == "") {
				$updt .= "isupdtdescription = 'Y', ";
				$updt .= "description = NULL, ";
			} else {
				$updt .= "isupdtdescription = 'Y', ";
				$updt .= "description = '" . $addcomment . "', ";
			}
		}
		if ($check_showweb == "yes") {
			if ($showweb == "yes") {
				$updt .= "isupdtselfservice = 'Y', ";
				$updt .= "isselfservice = 'Y', ";
			} else {
				$updt .= "isupdtselfservice = 'Y', ";
				$updt .= "isselfservice = 'N', ";
			}
		} else {
			$updt .= "isupdtselfservice = 'N', ";
			$updt .= "isselfservice = 'N', ";
		}
		if ($check_utgangen == "yes") {
			if ($utgangen == "yes") {
				$updt .= "isupdtdiscontinued = 'Y', ";
				$updt .= "discontinued = 'Y', ";
			} else {
				$updt .= "isupdtdiscontinued = 'Y', ";
				$updt .= "discontinued = 'N', ";
			}
		} else {
			$updt .= "isupdtdiscontinued = 'N', ";
			$updt .= "discontinued = 'N', ";
		}
		if ($check_priceshape == "yes") {
			if ($priceshape == "yes") {
				$updt .= "isupdtexclautopricing = 'Y', ";
				$updt .= "isexclautopricing = 'Y', ";
			} else {
				$updt .= "isupdtexclautopricing = 'Y', ";
				$updt .= "isexclautopricing = 'N', ";
			}
		} else {
			$updt .= "isupdtexclautopricing = 'N', ";
			$updt .= "isexclautopricing = 'N', ";
		}
		$updt .= "updatetime = '" . $addfrom . "' ";
		$updt .= "WHERE isactive = 'Y' AND isupdated = 'N' AND m_product_update_id = '" . $m_product_update_id . "' ";
	
		// echo $updt;
		// exit;
		
		if ((Db::getConnectionAD(true)) && @pg_query(Db::getConnectionAD(true), $updt)) {
			return true;
		} else {
			return false;
		}
	
	}

	function getProductUpdateInfo($m_product_update_id) {
	
		$select  = "SELECT * ";
		$select .= "FROM adempiere.m_product_update ";
		$select .= "WHERE m_product_update_id= '" . $m_product_update_id . "' ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
	
		$rows = $res ? pg_fetch_object($res) : null;

		return $rows;
	
	}
	
	function getArticleInfo ($artnr) {
		global $fi, $sv, $no;
	
		$select  = "SELECT Artiklar.artnr, Artiklar_fi.artnr_fi, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.utpris, Artiklar_fi.utpris_fi, Tillverkare.tillverkare, ";
		$select .= "Artiklar.utpris_no, Artiklar.art_id, Artiklar.art_id_no, Artiklar_fi.art_id_fi, ";
		$select .= "Artiklar.no_buy, Artiklar.lagersaldo, Artiklar.kommentar, Artiklar_fi.kommentar_fi, Moms.momssats, Moms.momssats_fi, Moms.momssats_no, Artiklar.utgangen, Artiklar_fi.utgangen_fi, ";
		$select .= "Artiklar.artnr_lev, Artiklar.m_product_id, Artiklar.isSalesBundle, Artiklar.spec20, Artiklar.bild, Artiklar.tillverkar_id, ";
		$select .= "Artiklar.searchTerms, Artiklar.passartill, Artiklar.isTradeIn, Artiklar.beskrivning_no, Artiklar.kommentar_no, ";
		$select .= "Artiklar.upc ";
		$select .= "FROM Artiklar ";
		$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "LEFT JOIN Leverantor ON Artiklar.grossist_id = Leverantor.grossist_id ";
		$select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
		$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
		$select .= "WHERE Artiklar.tillverkar_id=Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id ";
		$select .= "AND Artiklar.artnr='$artnr' ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
	
		$rows = mysqli_fetch_object($res);

		return $rows;
	
	}

	function getIfArticleExist($artnr) {
	
		$select  = "SELECT Artiklar.artnr ";
		$select .= "FROM Artiklar ";
		$select .= "WHERE Artiklar.artnr='$artnr' AND ej_med = 0 AND utgangen = 0 AND utpris > 10 ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		
		$res = mysqli_query(Db::getConnection(), $select);
		// $rows = mysqli_fetch_object($res);
		
		if (mysqli_num_rows($res) > 0) {
			return true;
		} else {
			return false;
		}

	}
	
	function getMProductID ($artnr) {
		global $fi, $sv, $no;
	
		$select  = "SELECT Artiklar.m_product_id ";
		$select .= "FROM Artiklar ";
		$select .= "WHERE Artiklar.artnr='$artnr' ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
	
		}
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
	
		return $rows->m_product_id;
	
	}

	function getArtnr ($MproductID) {
		global $fi, $sv, $no;
	
		$select  = "SELECT Artiklar.artnr ";
		$select .= "FROM Artiklar ";
		$select .= "WHERE Artiklar.m_product_id='$MproductID' ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
	
		return $rows->artnr;
	
	}

	function verifyMProductID ($artnr,$m_product_id) {
		global $fi, $sv, $no;
	
		$select  = "SELECT Artiklar.m_product_id ";
		$select .= "FROM Artiklar ";
		$select .= "WHERE Artiklar.artnr='$artnr' AND Artiklar.m_product_id='$m_product_id' ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
	
		}
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		
		if (mysqli_num_rows($res) > 0) {
			return true;
		} else {
			return false;
		}
	
	}
	
	function getExtraComment($artnr,$show_add,$show_campaign = false) {
		global $sv, $fi, $no, $style, $showutgangen, $showlagersaldo, $ej_med;
		
		$language = $_SESSION['currentLocale'];
	
		$select  = "SELECT eiID, eiArtnr, eiComment, eiValidFrom, eiValidTo ";
		$select .= "FROM cyberphoto.Extrainfo ";
		$select .= "WHERE eiArtnr = '" . $artnr . "' ";
		if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND ((eiDependOnDate = 1 AND eiValidFrom < now() AND eiValidTo > now()) OR (eiDependOnDate = 1 AND eiValidFrom > now()) OR (eiDependOnDate = 0)) ";
		} else {
			$select .= "AND ((eiDependOnDate = 1 AND eiValidFrom < now() AND eiValidTo > now()) OR (eiDependOnDate = 0)) ";
		}
		$select .= "AND eiIsActive = 1 AND eiLanguage = '" . $language . "' ";
		$select .= "AND eiIsInternal = 0 ";
		if ($show_campaign) {
			$select .= "AND eiIsCampaign = 1 ";
		} else {
			$select .= "AND eiIsCampaign = 0 ";
		}
		
		// echo $select;
		
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
	
		if (mysqli_num_rows($res) > 0) {
			if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) && strtotime($row->eiValidFrom) > time()) {
				$linc = "edit=1&eiID=" . $row->eiID . "&eiArtnr=" . $row->eiArtnr; 
				if ((preg_match("/price_/i", $_SERVER['PHP_SELF']) || preg_match("/pri_/i", $_SERVER['PHP_SELF']) || preg_match("/search/i", $_SERVER['PHP_SELF'])) && $show_add) {
					echo "<div class=\"internal_product_comment\"><a class=\"italic mark_blue\" href=\"javascript:winPopupCenter(450, 800, '/order/comment_update.php?$linc');\">** Kommande **</a></div>\n";
				} else {
					echo "<div class=\"comment_area\"><a class=\"italic mark_blue\" href=\"javascript:winPopupCenter(450, 800, '/order/comment_update.php?$linc');\">** Kommande **</a></div>\n";
				}
			} elseif (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR']) && (preg_match("/price_/i", $_SERVER['PHP_SELF']) || preg_match("/pri_/i", $_SERVER['PHP_SELF']) || preg_match("/search/i", $_SERVER['PHP_SELF'])) && $show_add) {
				$linc = "edit=1&eiID=" . $row->eiID . "&eiArtnr=" . $row->eiArtnr; 
				echo "<div class=\"internal_product_comment\"><a href=\"javascript:winPopupCenter(450, 800, '/order/comment_update.php?$linc');\">ändra info</a></div>\n";
			} elseif (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
				$linc = "edit=1&eiID=" . $row->eiID . "&eiArtnr=" . $row->eiArtnr; 
				if ($showutgangen == -1 && $showlagersaldo < 1) {
					echo "<div class=\"comment_area bold\"><a href=\"javascript:winPopupCenter(450, 800, '/order/comment_update.php?$linc');\">" . $style->StyleText_v2($row->eiComment) . "</a></div>\n";
					echo "<div class=\"vpadding italic mark_blue\">Kommentaren ovan visas ej externt</div>\n";
				} else {
					echo "<div class=\"comment_area bold\"><a href=\"javascript:winPopupCenter(450, 800, '/order/comment_update.php?$linc');\">" . $style->StyleText_v2($row->eiComment) . "</a></div>\n";
				}
			} else {
				echo "<div class=\"comment_area bold\">" . $style->StyleText_v2($row->eiComment) . "</div>\n";
			}
		} elseif (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) && $show_add) {
			if (preg_match("/price_/i", $_SERVER['PHP_SELF']) || preg_match("/pri_/i", $_SERVER['PHP_SELF']) || preg_match("/search/i", $_SERVER['PHP_SELF'])) {
				// echo "<div class=\"internal_product_comment\"><img border=\"0\" src=\"/blogg/help.gif\">&nbsp;<a href=\"javascript:winPopupCenter(450, 800, '/order/comment_update.php?eiArtnr=$artnr');\">L�gg till info</a></div>\n";
				echo "<div class=\"internal_product_comment\"><a href=\"javascript:winPopupCenter(450, 800, '/order/comment_update.php?eiArtnr=$artnr');\">Lägg till info</a></div>\n";
			} else {
				echo "<div class=\"align_left bottom5\"><img border=\"0\" src=\"/blogg/help.gif\">&nbsp;<a href=\"javascript:winPopupCenter(450, 800, '/order/comment_update.php?eiArtnr=$artnr');\">Lägg till info</a></div>\n";
			}
		} else {
			return "";
		}
	}

	function getExtraText($artnr = null,$show_campaign = false) {
		global $sv, $fi, $no, $style;
	
		$language = $_SESSION['currentLocale'];
	
		$select  = "SELECT eiID, eiArtnr, eiText, eiValidFrom ";
		$select .= "FROM cyberphoto.Extrainfo ";
		$select .= "WHERE eiArtnr = '" . $artnr . "' ";
		if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND ((eiDependOnDate = 1 AND eiValidFrom < now() AND eiValidTo > now()) OR (eiDependOnDate = 1 AND eiValidFrom > now()) OR (eiDependOnDate = 0)) ";
		} else {
			$select .= "AND ((eiDependOnDate = 1 AND eiValidFrom < now() AND eiValidTo > now()) OR (eiDependOnDate = 0)) ";
		}
		$select .= "AND eiIsActive = 1 AND eiLanguage = '" . $language . "' AND NOT (eiText IS NULL) ";
		$select .= "AND eiIsInternal = 0 ";
		if ($show_campaign) {
			$select .= "AND eiIsCampaign = 1 ";
		} else {
			$select .= "AND eiIsCampaign = 0 ";
		}
	
		// echo $select;
	
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
	
		if (mysqli_num_rows($res) > 0) {
			if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
				echo "<div class=\"align_left top5 bottom5\">" . $row->eiText . "</div>\n";
				if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) && strtotime($row->eiValidFrom) > time()) {
					echo "<div class=\"upcoming_camapign\">** Kommande **</div>";
					echo "<div class=\"clear\"></div>";
				}
				$linc = "edit=1&eiID=" . $row->eiID . "&eiArtnr=" . $row->eiArtnr; 
				echo "<div class=\"comment_area\"><a href=\"javascript:winPopupCenter(450, 800, '/order/comment_update.php?$linc');\">Uppdatera extrainfo</a></div>\n";
			} else {
				echo "<div class=\"align_left top5 bottom5\">" . $row->eiText . "</div>\n";
			}
		}
	}
	
	function getExtrainfo ($ID,$artnr) {
		global $fi, $sv, $no;
	
		// $select  = "SELECT eiID, eiArtnr, eiComment, eiText, eiDependOnDate, eiValidFrom, eiValidTo, eiHandleLikeCampaign ";
		$select  = "SELECT * ";
		$select .= "FROM cyberphoto.Extrainfo ";
		$select .= "WHERE eiID='$ID' AND eiArtnr='$artnr' ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
	
		}
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		
		return $rows;
	
	}
	
	function getExtraInternalComment($artnr) {
		global $sv, $fi, $no;
		
		$language = $_SESSION['currentLocale'];
	
		$select  = "SELECT eiID, eiArtnr, eiComment, eiValidFrom, eiValidTo, eiAddDate ";
		$select .= "FROM cyberphoto.Extrainfo ";
		$select .= "WHERE eiArtnr = '" . $artnr . "' ";
		$select .= "AND ((eiDependOnDate = 1 AND eiValidFrom < now() AND eiValidTo > now()) OR (eiDependOnDate = 0)) ";
		$select .= "AND eiIsActive = 1 ";
		$select .= "AND eiIsInternal = 1 ";
		$select .= "ORDER BY eiAddDate DESC  ";
		
		// echo $select;
		
		$res = mysqli_query(Db::getConnection(), $select);
		// $row = mysqli_fetch_object($res);
		// echo $row->eiAddDate;
	
		if (mysqli_num_rows($res) > 0) {
			
			echo "<div class=\"internalcomment_container\">\n";
			echo "<div class=\"bold italic bottom5\">Info angående produkten</div>\n";
			
			while ($row = mysqli_fetch_object($res)) {
				
				$linc = "edit=1&eiID=" . $row->eiID . "&eiArtnr=" . $row->eiArtnr; 
				echo "<div class=\"bottom5\">\n";
				echo "<a href=\"javascript:winPopupCenter(450, 800, '/order/internal_comment_update.php?$linc');\">\n";
				echo "<span class=\"italic\">" . date("Y-m-d", strtotime($row->eiAddDate)) . "</span><br>";
				echo $row->eiComment;
				echo "</a>\n";
				echo "</div>\n";
				
			}
			
			echo "</div>\n";
			
		}
	
	}

	function addExtrainfo($eiArtnr,$add_country,$addactive,$addfrom,$addto,$addcomment,$addhtml,$check_dependdate,$addascampaign,$addcreatedby,$internal_comment,$campaign_comment,$language=null) {
		// global ;

		if ($language == null) {
			$language = $_SESSION['currentLocale'];
		}
		
		$addcomment = trim($addcomment);
		$addhtml = trim($addhtml);

		$updt  = "INSERT INTO cyberphoto.Extrainfo ";
		$updt .= "( ";
		$updt .= "eiArtnr, ";
		if ($addcomment != "") {
			$updt .= "eiComment, ";
		}
		if ($addhtml != "") {
			$updt .= "eiText, ";
		}
		if ($addascampaign == "yes") {
			$updt .= "eiHandleLikeCampaign, ";
		}
		if ($check_dependdate == "yes") {
			$updt .= "eiDependOnDate, ";
			$updt .= "eiValidFrom, ";
			$updt .= "eiValidTo, ";
		}
		$updt .= "eiLanguage, ";
		$updt .= "eiAddDate, eiAddIP, ";
		$updt .= "eiAddby ";
		if ($internal_comment) {
			$updt .= ",eiIsInternal ";
		}
		if ($campaign_comment) {
			$updt .= ",eiIsCampaign ";
		}
		$updt .= ") ";
		$updt .= "VALUES ";
		$updt .= "( ";
		$updt .= "'$eiArtnr', ";
		if ($addcomment != "") {
			$updt .= "'$addcomment', ";
		}
		if ($addhtml != "") {
			$updt .= "'$addhtml', ";
		}
		if ($addascampaign == "yes") {
			$updt .= "'1', ";
		}
		if ($check_dependdate == "yes") {
			$updt .= "'1', ";
			$updt .= "'$addfrom', ";
			$updt .= "'$addto', ";
		}
		/*
		if ($add_country == "3") {
			$updt .= "'no_NO',";
		} elseif ($add_country == "2") {
			$updt .= "'fi_FI',";
		} else {
			$updt .= "'sv_SE',";
		}
		*/
		$updt .= "'" . $language . "',";
		$updt .= "now(),'" . $_SERVER['REMOTE_ADDR'] . "', ";
		$updt .= "'" . $addcreatedby . "' ";
		if ($internal_comment) {
			$updt .= ",'1' ";
		}
		if ($campaign_comment) {
			$updt .= ",'1' ";
		}
		$updt .= ") ";
		
		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);
		
	}

	function updateExtrainfo() {
		global $eiID, $addactive, $addfrom, $addto, $addcomment, $addhtml, $check_dependdate, $addascampaign, $addcreatedby;
	
		$addcomment = trim($addcomment);
		$addhtml = trim($addhtml);
	
		$updt  = "UPDATE cyberphoto.Extrainfo ";
		$updt .= "SET ";
		if ($addactive == "yes") {
			$updt .= "eiIsActive = '1', ";
		} else {
			$updt .= "eiIsActive = '0', ";
		}
		
		if ($addcomment != "") {
			$updt .= "eiComment = '$addcomment', ";
		} else {
			$updt .= "eiComment = NULL, ";
		}
		if ($addhtml != "") {
			$updt .= "eiText = '$addhtml', ";
		} else {
			$updt .= "eiText = NULL, ";
		}
		if ($addascampaign == "yes") {
			$updt .= "eiHandleLikeCampaign = '1', ";
		} else {
			$updt .= "eiHandleLikeCampaign = '0', ";
		}
		if ($check_dependdate == "yes") {
			$updt .= "eiDependOnDate = '1', ";
			$updt .= "eiValidFrom = '$addfrom', ";
			$updt .= "eiValidTo = '$addto', ";
		} else {
			$updt .= "eiDependOnDate = '0', ";
			$updt .= "eiValidFrom = NULL, ";
			$updt .= "eiValidTo = NULL, ";
		}
		$updt .= "eiUpdateBy = '$addcreatedby', ";
		$updt .= "eiUpdateDate = now(), ";
		$updt .= "eiUpdateIP = '" . $_SERVER['REMOTE_ADDR'] . "' ";
		$updt .= "WHERE eiID = '" . $eiID . "' ";
	
		// echo $updt;
		// exit;
	
		$res = mysqli_query(Db::getConnection(true), $updt);
	
	}

	function listCommingUpdates($old = false) {
	
		$desiderow = true;
		unset($groupday);
		unset($groupday2);
	
		$select  = "SELECT p.value as artnr, u.value as user, pu.*, p.name ";
		$select .= "FROM m_product_update pu ";
		$select .= "JOIN m_product p ON p.m_product_id = pu.m_product_id ";
		$select .= "JOIN ad_user u ON u.ad_user_id = pu.salesrep_id ";
		if ($old) {
			$select .= "WHERE isupdated = 'Y' ";
		} else {
			$select .= "WHERE isupdated = 'N' ";
		}
		if ($old) {
			$select .= "AND updatetime > current_date - integer '13' ";
			$select .= "ORDER BY updatetime DESC, p.value ASC, m_product_update_id DESC ";
			// $select .= "LIMIT 300 ";
		} else {
			$select .= "ORDER BY updatetime ASC, p.value ASC, m_product_update_id DESC ";
		}
		
		// $res = mysqli_query($this->getConnectionDb(false), $select);
		$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $select) : false;
		// $res = $res;
		// $check = mysqli_num_rows($res);
	
		echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
		echo "<tr>";
		echo "<td class=\"bold align_left\" width=\"20\"></td>";
		echo "<td class=\"bold align_left\" width=\"40\">Datum</td>";
		echo "<td class=\"bold align_center\" width=\"100\">återstår</td>";
		echo "<td class=\"bold align_left\" width=\"150\">Artikel</td>";
		echo "<td class=\"bold align_left\" width=\"400\">Namn</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Nytt pris</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Visas</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Utgången</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Beskrivning</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Kommentar</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Ansvarig</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
	
		if ($res && pg_num_rows($res) > 0) {
			
			while ($res && $row = pg_fetch_object($res)) {
				
				// $row = $row;
					
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
	
				if ($row->m_pricelist_id == 1000018) {
					$utpris_moms = $row->pricestd * 1.24;
					$valuta = "EUR";
					$prod_linc = "http://www.cyberphoto.fi/info.php?article=";
				} elseif ($row->m_pricelist_id == 1000280) {
					$utpris_moms = $row->pricestd * 1.25;
					$valuta = "NOK";
					$prod_linc = "http://www.cyberphoto.no/info.php?article=";
				} else {
					$utpris_moms = $row->pricestd * 1.25;
					$valuta = "SEK";
					$prod_linc = "http://www2.cyberphoto.se/info.php?article=";
				}
				
				$groupday = date("Y-m-d",strtotime($row->updatetime));

				// if (date("Y-m-d",strtotime($row->updatetime)) == date("Y-m-d", time())) {
				if ($groupday != $groupday2) {
					if (date("Y-m-d",strtotime($row->updatetime)) == date("Y-m-d", time())) {
						echo "<tr>";
						echo "<td colspan=\"12\" class=\"bold\">Idag</td>";
						echo "</tr>";
					} else {
						echo "<tr>";
						echo "<td colspan=\"12\" class=\"bold\">&nbsp;</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td colspan=\"12\" class=\"bold\">" . CDeparture::replace_days(date("l",strtotime($row->updatetime))) . " " . date("Y-m-d",strtotime($row->updatetime)) . "</td>";
						echo "</tr>";
					}
				}
				
				// echo date("Y-m-d",strtotime($row->updatetime)) . "-";
				// echo date("Y-m-d", strtotime(time()));
				
				echo "<tr>";
				echo "\t\t<td>\n";
				// echo "<a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?edit=yes&artnr=$row->artnr&m_product_id=$row->m_product_id&ID=$row->m_product_update_id');\">";
				echo "<a href=\"javascript:winPopupCenter(900, 800, '/product_update.php?edit=yes&artnr=$row->artnr&m_product_id=$row->m_product_id&ID=$row->m_product_update_id');\">";
				if ($row->m_pricelist_id == 1000018) {
					echo "<img border=\"0\" src=\"fi_mini.jpg\">";
				} elseif ($row->m_pricelist_id == 1000280) {
					echo "<img border=\"0\" src=\"no_mini.jpg\">";
				} else {
					echo "<img border=\"0\" src=\"sv_mini.jpg\">";
				}
				echo "</a>\n";
				echo "</td>\n";
				echo "\t\t<td class=\"$rowcolor align_center\">" . date("H:i",strtotime($row->updatetime)) . "</td>";
				if (!$old) {
					echo "\t\t<td class=\"$rowcolor align_center\">" . CCampaignCheck::getTimeLeftNew($row->updatetime) . "</td>";
				} else {
					echo "\t\t<td class=\"$rowcolor align_center\"><i>Utförd</i></td>";
				}
				echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr . "</td>";
				// echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc . $row->artnr . "\">" . $row->name . "</a></td>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc . $row->artnr . "\">" . $row->name . "</a></td>";
				if ($row->isupdtpricestd == "Y") {
					echo "\t\t<td class=\"$rowcolor align_right\">" . number_format($utpris_moms, 0, ',', ' ') . " " . $valuta . "</td>";
				} else {
					echo "<td class=\"$rowcolor\">&nbsp;</td>";
				}
				if ($row->isupdtselfservice == "Y") {
					if ($row->isselfservice == "Y") {
						echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_green.png\"></td>\n";
					} else {
						echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_red.png\"></td>\n";
					}
				} else {
					echo "<td class=\"$rowcolor\">&nbsp;</td>";
				}
				if ($row->isupdtdiscontinued == "Y") {
					if ($row->discontinued == "Y") {
						echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_green.png\"></td>\n";
					} else {
						echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_red.png\"></td>\n";
					}
				} else {
					echo "<td class=\"$rowcolor\">&nbsp;</td>";
				}
				if ($row->isupdtname == "Y") {
					echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_green.png\"></td>\n";
				} else {
					echo "<td class=\"$rowcolor\">&nbsp;</td>";
				}
				if ($row->isupdtdescription == "Y") {
					echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_green.png\"></td>\n";
				} else {
					echo "<td class=\"$rowcolor\">&nbsp;</td>";
				}
				echo "\t\t<td class=\"$rowcolor align_center\">" . strtoupper($row->user) . "</td>";
				// echo "<td class=\"$rowcolor\">&nbsp;</td>";
				echo "<td>&nbsp;</td>";
				echo "</tr>";
	
				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				
				$groupday2 = date("Y-m-d",strtotime($row->updatetime));
				
	
			}
	
		} else {
			echo "<tr>";
			echo "<td colspan=\"12\" class=\"italic\">Inga produktuppdateringar finns registrerade&nbsp;</td>";
			echo "</tr>";
		}
	
		echo "</table>";
	
	}

	function listArticleUpdates($artnr,$old) {
	
		$select  = "SELECT pl.name, pl.m_pricelist_id, u.value, pu.updatetime, pu.pricestd, pu.isupdtselfservice, pu.isselfservice, pu.isupdtpricestd, pu.isupdtdiscontinued, pu.discontinued, ";
		$select .= "pu.isupdtname, pu.isupdtdescription ";
		$select .= "FROM m_product_update pu ";
		$select .= "JOIN m_product p ON p.m_product_id = pu.m_product_id ";
		$select .= "JOIN m_pricelist pl ON pl.m_pricelist_id = pu.m_pricelist_id ";
		$select .= "JOIN ad_user u ON u.ad_user_id = pu.salesrep_id ";
		if ($old) {
			$select .= "WHERE isupdated = 'Y' AND p.value = '" . $artnr . "' ";
		} else {
			$select .= "WHERE isupdated = 'N' AND p.value = '" . $artnr . "' ";
		}
		$select .= "ORDER BY pu.updatetime DESC, pl.m_pricelist_id  ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
	
		$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $select) : false;
		// $row = pg_fetch_object($res);
		// echo pg_num_rows($res);
	
		if ($res && pg_num_rows($res) > 0) {

			echo "<div class=\"top5 bottom10\">\n";
			if ($old) {
				echo "<div class=\"bold italic\">Gjorda prisjusteringar</div>\n";
			} else {
				echo "<div class=\"bold italic\">Kommande prisjusteringar</div>\n";
			}
			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\" width=\"100%\">\n";
				
			// while ($row2 = pg_fetch_row($res)) {
			while ($res && $row = pg_fetch_object($res)) {
				
				if ($row->m_pricelist_id == 1000018) {
					$utpris_moms = $row->pricestd * 1.24;
					$valuta = "EUR";
				} elseif ($row->m_pricelist_id == 1000280) {
					$utpris_moms = $row->pricestd * 1.25;
					$valuta = "NOK";
				} else {
					$utpris_moms = $row->pricestd * 1.25;
					$valuta = "SEK";
				}
	
				echo "\t<tr>\n";
				if ($row->m_pricelist_id == 1000018) {
					echo "\t\t<td><img border=\"0\" src=\"/order/admin/fi_mini.jpg\"></td>\n";
				} elseif ($row->m_pricelist_id == 1000280) {
					echo "\t\t<td><img border=\"0\" src=\"/order/admin/no_mini.jpg\"></td>\n";
				} else {
					echo "\t\t<td><img border=\"0\" src=\"/order/admin/sv_mini.jpg\"></td>\n";
				}
				// echo "\t\t<td>" . $row->m_pricelist_id . "</td>\n";
				echo "\t\t<td class=\"align_center\">" . date("Y-m-d H:i", strtotime($row->updatetime)) . "</td>\n";
				echo "\t\t<td class=\"align_center\">" . strtoupper($row->value) . "</td>\n";
				if ($row->isupdtselfservice == "Y") {
					if ($row->isselfservice == "Y") {
						echo "\t\t<td class=\"align_right\">Visa=Ja</td>\n";
					} else {
						echo "\t\t<td class=\"align_right\">Visa=Nej</td>\n";
					}
				}
				if ($row->isupdtdiscontinued == "Y") {
					if ($row->discontinued == "Y") {
						echo "\t\t<td class=\"align_right\">Utg=Ja</td>\n";
					} else {
						echo "\t\t<td class=\"align_right\">Utg=Nej</td>\n";
					}
				}
				if ($row->isupdtname == "Y") {
					echo "\t\t<td class=\"align_right\">Ny beskrivning</td>\n";
				}
				if ($row->isupdtdescription == "Y") {
					echo "\t\t<td class=\"align_right\">Ny kommentar</td>\n";
				}
				if ($row->isupdtpricestd == "Y") {
					echo "\t\t<td class=\"align_right\">" . number_format($utpris_moms, 0, ',', ' ') . " " . $valuta . "</td>\n";
				}
				echo "\t</tr>\n";
				
			}

			echo "</table>\n";
			echo "</div>\n";
				
		}
			
	}

	function listArticleManualPrice($artnr) {
	
		$select  = "SELECT pp.m_pricelist_version_id ";
		$select .= "FROM M_ProductPrice pp ";
		$select .= "JOIN m_product p ON p.m_product_id = pp.m_product_id ";
		$select .= "WHERE pp.ismanual = 'Y' ";
		$select .= "AND p.value = '" . $artnr . "' ";
		$select .= "ORDER BY pp.m_pricelist_version_id ASC  ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
	
		$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $select) : false;
		// $row = pg_fetch_object($res);
		// echo pg_num_rows($res);
	
		if ($res && pg_num_rows($res) > 0) {

			echo "<div class=\"top5 bottom10\">\n";
			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\" width=\"100%\">\n";
				
			// while ($row2 = pg_fetch_row($res)) {
			while ($res && $row = pg_fetch_object($res)) {
				
				echo "\t<tr>\n";
				if ($row->m_pricelist_version_id == 1000016) {
					echo "\t\t<td><img border=\"0\" src=\"/order/admin/fi_mini.jpg\"></td>\n";
				} elseif ($row->m_pricelist_version_id == 1000268) {
					echo "\t\t<td><img border=\"0\" src=\"/order/admin/no_mini.jpg\"></td>\n";
				} else {
					echo "\t\t<td><img border=\"0\" src=\"/order/admin/sv_mini.jpg\"></td>\n";
				}
				echo "\t\t<td class=\"align_left\">Denna artikel sätts manuellt pris</td>\n";
				echo "\t</tr>\n";
				
			}

			echo "</table>\n";
			echo "</div>\n";
				
		}
			
	}
	
	function getLastInvoiceProductWarning($m_product_id) {
		return null;
		$select  = "SELECT MAX(i.dateacct) AS last_invoiced  ";
		$select .= "FROM c_invoice i ";
		$select .= "JOIN c_InvoiceLine il ON i.c_invoice_id = il.c_invoice_id ";
		$select .= "JOIN m_product prod ON il.m_product_id = prod.m_product_id ";
		$select .= "WHERE i.isSOTrx='Y' AND il.m_product_id = " . $m_product_id . " ";
		//echo $select;
		
		$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $select) : false;
		$row = $res ? pg_fetch_object($res) : null;
		
		if ($row->last_invoiced != NULL) {
			
			$now = time(); // or your date as well
			$your_date = strtotime($row->last_invoiced);
			$datediff = $now - $your_date;
			$showdays = floor($datediff/(60*60*24));
			// echo $showdays;
			
			if ($showdays > 180) {
				
				if (preg_match("/pri_/i", $_SERVER['PHP_SELF']) || preg_match("/price_/i", $_SERVER['PHP_SELF']) || preg_match("/search\.php/i", $_SERVER['PHP_SELF'])) {
					
					echo "<div class=\"\">Senast såld: " . date("Y-m-d", strtotime($row->last_invoiced)) . " (<span class=\"bold mark_red\">" . $showdays . " dagar</span>)</div>\n";
						
				} else {
					/*
					echo "<tr>\n";
					echo "<td colspan=\"3\">&nbsp;</td>\n";
					echo "</tr>\n";
					*/
					echo "<tr>\n";
					echo "<td colspan=\"3\" class=\"align_left\">Senast såld: " . date("Y-m-d", strtotime($row->last_invoiced)) . " (<span class=\"bold mark_red\">" . $showdays . " dagar</span>)</td>\n";
					echo "</tr>\n";
				}
			}
		
		}
		
	}
	
	function listEarlierComments($eiArtnr) {
		global $sv, $fi, $no, $style;
	
		$language = $_SESSION['currentLocale'];
	
		$select  = "SELECT eiID, eiArtnr, eiComment, eiText, eiValidFrom, eiValidTo, eiAddDate, eiUpdateDate, eiDependOnDate  ";
		$select .= "FROM cyberphoto.Extrainfo ";
		$select .= "WHERE eiArtnr = '" . $eiArtnr . "' ";
		$select .= "AND ((eiDependOnDate = 1 AND eiValidTo < now()) OR (eiDependOnDate = 0 AND eiIsActive = 0)) ";
		$select .= "AND eiLanguage = 'sv_SE' AND NOT (eiComment IS NULL OR eiComment IS NULL) ";
		$select .= "ORDER BY eiAddDate DESC ";
	
		// echo $select;
	
		$res = mysqli_query(Db::getConnection(), $select);
		// $row = mysqli_fetch_object($res);
	
		if (mysqli_num_rows($res) > 0) {
			
			echo "<h2>Tidigare kommentarer på produkten</h2>\n";
			echo "<table cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>\n";
			echo "<th width=\"300\">Kommentar</th>\n";
			echo "<th width=\"150\">Från</th>\n";
			echo "<th width=\"120\">Till</th>\n";
			echo "</tr>\n";
		
			while ($row = mysqli_fetch_object($res)) {
			
				echo "<tr>\n";
				echo "<td class=\"align_left\">" . $row->eiComment . "</td>\n";
				if ($row->eiDependOnDate == 1) {
					echo "<td class=\"align_left\">" . $row->eiValidFrom . "</td>\n";
					echo "<td class=\"align_left\">" . $row->eiValidTo . "</td>\n";
				} else {
					echo "<td class=\"align_left\">" . $row->eiAddDate . "</td>\n";
					echo "<td class=\"align_left\">" . $row->eiUpdateDate . "</td>\n";
				}
				echo "</tr>\n";
			
			}
			
			echo "</table>\n";
		}
	
	}

	function getBredcrumbs($kategori,$array = null) {
		global $fi, $sv, $no;

		if (!$array) {
			$array = array();
		}

		$select  = "SELECT kategori, kategori_fi, kategori_no, kategori_id, kategori_id_parent ";
		$select .= "FROM cyberphoto.Kategori ";
        $select .= "WHERE kategori_id = $kategori ";
        $select .= "AND NOT kategori_id IN (1000260) ";
		
		// echo $select;
		
		$res = mysqli_query(Db::getConnection(), $select);
		
		while ($row = mysqli_fetch_object($res)) {
			
			if ($fi && !$sv) {
				$cat_name = $row->kategori_fi;
			} elseif ($no) {
				$cat_name = $row->kategori_no;
			} else {
				$cat_name = $row->kategori;
			}
			
			if ($row->kategori_id_parent == 0) {
				$content['bread_id'] = $row->kategori_id;
				// $content['bread_name'] = $row->kategori;
				$content['bread_name'] = $cat_name;
				array_push($array, $content);
				$reversed = array_reverse($array);
				/*
				print "<pre>";
				// print_r($array);
				print_r($reversed);
				print "</pre>";
				*/
			} else {
				$content['bread_id'] = $row->kategori_id;
				// $content['bread_name'] = $row->kategori;
				$content['bread_name'] = $cat_name;
				array_push($array, $content);
				$this->getBredcrumbs($row->kategori_id_parent,$array);
			}
		}
		
		if (count($reversed) > 0) {
			$this->displayBreadCrumbs($reversed);
		}
		
	}
	
	function displayBreadCrumbs($reversed) {
		global $sv, $fi, $no, $headlinc;
		
		$last_array = 1;
		$arrlength = count($reversed);
	
		echo "<div id=\"breadcrumb_area\">\n";
		
		foreach($reversed AS $x) {
			
			unset($pageLinc);
			
			$pageLinc = CSeo::getLincIfSeoReplacePage($x['bread_id']);
			
			if ($pageLinc == "") {
				$xlinc = $headlinc . "/category/" . $x['bread_id'] . "/" . strtolower(Tools::replace_special_char(trim($x['bread_name'])));
			} else {
				$xlinc = $pageLinc;
			}
			echo "<a href=\"$xlinc\">" . $x['bread_name'], "</a>";
			if ($last_array < $arrlength) {
				echo " - ";
			}
			$last_array++;
		}
		
		echo "</div>\n";
		
	}

	function listProductUpdateBy() {
		
		$tot_count = 0;
	
		$select  = "SELECT COUNT(pu.m_product_update_id) AS antal, u.value AS user ";
		$select .= "FROM m_product_update pu ";
		$select .= "JOIN m_product p ON p.m_product_id = pu.m_product_id ";
		$select .= "JOIN m_pricelist pl ON pl.m_pricelist_id = pu.m_pricelist_id ";
		$select .= "JOIN ad_user u ON u.ad_user_id = pu.salesrep_id ";
		$select .= "WHERE isupdated = 'Y' ";
		$select .= "GROUP BY u.value ";
		$select .= "ORDER BY Antal DESC ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
	
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		// $row = pg_fetch_object($res);
		// echo pg_num_rows($res);
	
		if ($res && pg_num_rows($res) > 0) {

			echo "<div class=\"bottom10\">\n";
			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\">\n";
			echo "\t<tr>\n";
			echo "\t\t<td class=\"bold\" width=\"100\">Användare</td>\n";
			echo "\t\t<td class=\"align_center bold\">Antal</td>\n";
			echo "\t</tr>\n";
				
			// while ($row2 = pg_fetch_row($res)) {
			while ($res && $row = pg_fetch_object($res)) {
				
				echo "\t<tr>\n";
				echo "\t\t<td>" . strtoupper($row->user) . "</td>\n";
				echo "\t\t<td class=\"align_right\">" . $row->antal . " st</td>\n";
				echo "\t</tr>\n";
				
				$tot_count = $tot_count + $row->antal;
				
			}

			echo "\t<tr>\n";
			echo "\t\t<td class=\"bold\">Totalt:</td>\n";
			echo "\t\t<td class=\"align_right bold\">" . $tot_count ." st</td>\n";
			echo "\t</tr>\n";
			echo "</table>\n";
			echo "</div>\n";
				
		}
			
	}

	function listLastAddedProducts() {
		global $category;
	
		$desiderow = true;
		$countrow = 0;
	
		$select  = "SELECT a.artnr, a.beskrivning, afi.beskrivning_fi, a.m_product_id ";
		$select .= "FROM cyberphoto.Artiklar a ";
		$select .= "JOIN Artiklar_fi afi ON a.artnr = afi.artnr_fi ";
		$select .= "WHERE date_add > DATE_SUB(NOW(), INTERVAL 3 MONTH) ";
		$select .= "AND ej_med=0 AND ej_med_fi=0 AND demo = 0 AND (utgangen=0 OR lagersaldo > 0 ) ";
		if ($category != "") {
			$select .= "AND a.kategori_id IN (395) ";
		} else {
			$select .= "AND a.kategori_id IN (392,393,394,395,336,748,329,402,517,235,445,462,1000182) ";
		}
		include ("std_instore_special_fi.php");
		$criteria = preg_replace("/Artiklar./", "a.", $criteria);
		$select .= $criteria;
		$select .= "ORDER BY date_add DESC ";
		
		$res = mysqli_query(Db::getConnection(), $select);
		// $row = mysqli_fetch_object($res);
	
		echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"1250\">";
		echo "<tr>";
		echo "<td class=\"bold align_left\">Artikel nr</td>";
		echo "<td class=\"bold align_left\" width=\"500\">Svenska beskrivningen</td>";
		echo "<td class=\"bold align_left\" width=\"500\">Finska beskrivningen</td>";
		echo "<td class=\"bold align_left\" width=\"60\"></td>";
		echo "</tr>";
	
		if (mysqli_num_rows($res) > 0) {
	
			while ($row = mysqli_fetch_object($res)) {
					
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				
				$mark = "<span style=\"color: red;\">***</span>";
				
				if (trim($row->beskrivning) === trim($row->beskrivning_fi)) {
					$mark = "";
				}
	
				echo "<tr>";
				echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr . "</td>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.se/info.php?article=" . $row->artnr . "\">" . $row->beskrivning . "</a></td>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.fi/info.php?article=" . $row->artnr . "\">" . $row->beskrivning_fi . "</a> " . $mark . "</td>";
				echo "\t\t<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=" . $row->artnr . "&m_product_id=" . $row->m_product_id . "&force_lang=fi');\">åtgärda</a></td>";
				echo "</tr>";
	
				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				$countrow++;
				
	
			}
	
		}
	
		echo "<tr>";
		echo "\t\t<td colspan=\"10\" class=\"bold align_left\">Totalt: " . $countrow . " st</td>";
		echo "</tr>";
		echo "</table>";
	
	}

	function getChildParentProducts($artnr,$parent) {
		global $sv, $fi, $no;
		
		if (!$parent) {
			$artnr_check = $this->getMainProduct($artnr);
			$style = "bottom5";
		} else {
			$artnr_check = $artnr;
			$style = "bottom5";
		}

		$select  = "SELECT a.artnr, a.beskrivning ";
		$select .= "FROM cyberphoto.Artiklar a ";
		$select .= "WHERE a.artnr_parent = '$artnr_check' ";
		$select .= "AND a.ej_med = 0 AND a.demo = 0 AND (a.utgangen = 0 OR a.lagersaldo > 0) ";
		$select .= "ORDER BY a.SortNo DESC, a.beskrivning ASC ";

		$res = mysqli_query(Db::getConnection(), $select);

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		
		echo "<div class=\"$style\">\n";
		echo "<form class=\"parent_child\" action=\"/info.php\" method=\"GET\" name=\"parentchild\">\n";
		echo "<select name=\"article\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
		if ($parent) {
			echo "<option value=\"$artnr\">*** " . l('Choose size') . " ***</option>\n";
		}

		while ($row = mysqli_fetch_object($res)) {
		
			echo "<option value=\"" . $row->artnr . "\"";
				
			if ($artnr == $row->artnr) {
				echo " selected";
			}
				
			echo ">" . $row->beskrivning . "</option>\n";
				
		}
		
		echo "</select>\n";
		echo "</form>\n";
		echo "</div>\n";

	}
	
	function getMainProduct($artnr) {
		
		$select  = "SELECT a.artnr_parent ";
		$select .= "FROM cyberphoto.Artiklar a ";
		$select .= "WHERE a.artnr = '$artnr' ";

		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		return $rows->artnr_parent;
		
	}

	function getChildProductPrice($artnr) {
		global $sv, $fi, $no;
		
		$select  = "SELECT MIN(a.utpris) AS utpris, MIN(afi.utpris_fi) AS utpris_fi, MIN(a.utpris_no) AS utpris_no, m.momssats, m.momssats_fi, m.momssats_no ";
		$select .= "FROM cyberphoto.Artiklar a ";
		$select .= "LEFT JOIN Moms m ON a.momskod = m.moms_id ";
		$select .= "LEFT JOIN Artiklar_fi afi ON a.artnr = afi.artnr_fi ";
		$select .= "WHERE a.artnr_parent = '$artnr' ";
		$select .= "AND a.ej_med = 0 AND a.demo = 0 AND (a.utgangen = 0 OR a.lagersaldo > 0) ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		
		if ($fi) {
			$utpris_moms = $rows->utpris_fi + $rows->utpris_fi * $rows->momssats_fi;
		} elseif ($no) {
			$utpris_moms = $rows->utpris_no + $rows->utpris_no * $rows->momssats_no;
		} else {
			$utpris_moms = $rows->utpris + $rows->utpris * $rows->momssats;
		}
		
		return $utpris_moms;
		
	}

	function listProductsInAlias($alias) {
		global $delete;
	
		$desiderow = true;
		$countrow = 0;
	
		$select  = "SELECT pt.artnr, t.tillverkare, a.beskrivning, a.lagersaldo, a.utgangen, a.ej_med, pt.recommended, pt.addBy, pt.kommentar ";
		$select .= "FROM cyberphoto.Passartill pt ";
		$select .= "JOIN Artiklar a ON a.artnr = pt.artnr ";
		$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "WHERE pt.passartill = '" . $alias . "' ";
		$select .= "ORDER BY pt.recommended ASC, a.utpris ASC, t.tillverkare, a.beskrivning ";
		
		/*
		$select .= "WHERE date_add > DATE_SUB(NOW(), INTERVAL 3 MONTH) ";
		$select .= "AND ej_med=0 AND ej_med_fi=0 AND demo = 0 AND (utgangen=0 OR lagersaldo > 0 ) ";
		*/
		// echo $select;
		
		$res = mysqli_query(Db::getConnection(), $select);
		// $row = mysqli_fetch_object($res);
		
		echo "<h2>Alias $alias (<span class=\"span_blue\">" . $this->getArticleName($alias) . "</span>) innehåller följande produkter</h2>\n";
		if ($delete != "") {
			echo "<h2><span class=\"span_red\">Visst vore det fint att kunna ta bort artikel $delete direkt här? Tyvärr måste Du fortfarande göra det via Adempiere.</span></h2>\n";
		}
	
		echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
		echo "<tr>";
		echo "<td class=\"bold align_left\" width=\"160\">Artikel nr</td>";
		echo "<td class=\"bold align_left\" width=\"420\">Artikel</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Lagersaldo</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Utgången</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Ej med</td>";
		echo "<td class=\"bold align_center\">Rekommenderat</td>";
		echo "<td class=\"bold align_center\">Upplagt av</td>";
		if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nuX') {
			echo "<td class=\"bold align_center\">&nbsp;</td>";
		}
		echo "<td class=\"bold align_center\">&nbsp;</td>";
		echo "</tr>";
	
		if (mysqli_num_rows($res) > 0) {
	
			while ($row = mysqli_fetch_object($res)) {
					
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				
				echo "<tr>";
				echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr . "</td>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.se/info.php?article=" . $row->artnr . "\">" . $row->tillverkare . " " . $row->beskrivning . "</a></td>";
				echo "\t\t<td class=\"$rowcolor align_center\">" . $row->lagersaldo . "</td>";
				if ($row->utgangen == -1) {
					echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_red.png\"></td>";
				} else {
					echo "\t\t<td class=\"$rowcolor align_center\">&nbsp;</td>";
				}
				if ($row->ej_med == -1) {
					echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_red.png\"></td>";
				} else {
					echo "\t\t<td class=\"$rowcolor align_center\">&nbsp;</td>";
				}
				echo "\t\t<td class=\"$rowcolor align_center\">" . $row->recommended . "</td>";
				echo "\t\t<td class=\"$rowcolor align_center\">" . $row->addBy . "</td>";
				if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nuX') {
					echo "\t\t<td class=\"align_center\"><a href=\"javascript:winPopupCenter(300, 700, 'http://www.cyberphoto.se/order/admin/accessories.php?origin=yes&change=$alias&ptproduct=" . $row->artnr . "');\">ändra</a></td>";
				}
				echo "\t\t<td class=\"align_center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=$alias&delete=" . $row->artnr . "\">Ta bort</a></td>";
				/*
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.se/info.php?article=" . $row->artnr . "\">" . $row->beskrivning . "</a></td>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.fi/info.php?article=" . $row->artnr . "\">" . $row->beskrivning_fi . "</a> " . $mark . "</td>";
				echo "\t\t<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=" . $row->artnr . "&m_product_id=" . $row->m_product_id . "&force_lang=fi');\">�tg�rda</a></td>";
				*/
				echo "</tr>";
	
				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				$countrow++;
				
	
			}
	
		}
	
		echo "<tr>";
		echo "\t\t<td colspan=\"10\" class=\"bold align_left\">Totalt: " . $countrow . " st</td>";
		echo "</tr>";
		echo "</table>";
	
	}

	function listProductsWithAlias($alias) {
	
		$desiderow = true;
		$countrow = 0;
	
		$select  = "SELECT a.artnr, t.tillverkare, a.beskrivning, a.lagersaldo, a.utgangen, a.ej_med, a.passartill ";
		$select .= "FROM cyberphoto.Artiklar a ";
		$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "WHERE a.passartill LIKE '%" . $alias . "%' ";
		$select .= "AND ej_med = 0 AND demo = 0 AND (utgangen = 0 OR lagersaldo > 0 ) ";
		$select .= "ORDER BY t.tillverkare, a.beskrivning ";
		
		/*
		$select .= "WHERE date_add > DATE_SUB(NOW(), INTERVAL 3 MONTH) ";
		*/
		// echo $select;
		
		$res = mysqli_query(Db::getConnection(), $select);
		// $row = mysqli_fetch_object($res);
		
		echo "<h2>Produkter där alias $alias (<span class=\"span_blue\">" . $this->getArticleName($alias) . "</span>) är angivet</h2>\n";
	
		echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"1220\">";
		echo "<tr>";
		echo "<td class=\"bold align_left\" width=\"160\">Artikel nr</td>";
		echo "<td class=\"bold align_left\" width=\"500\">Artikel</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Lagersaldo</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Utgången</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Ej med</td>";
		echo "<td class=\"bold align_center\" width=\"300\">Alias på produkten</td>";
		// echo "<td class=\"bold align_center\">&nbsp;</td>";
		echo "</tr>";
	
		if (mysqli_num_rows($res) > 0) {
	
			while ($row = mysqli_fetch_object($res)) {
					
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				$passartill = $row->passartill;

				$newargument = split (" ", $passartill);
				$n = count($newargument);
				if ($n > 7)
					$n = 7;
				for ($i=0; $i<$n; $i+=1) {
					if ($i == 0)
						$passartill1 = trim($newargument[$i]);
					elseif ($i == 1)
						$passartill2 = trim($newargument[$i]);
					elseif ($i == 2)
						$passartill3 = trim($newargument[$i]);
					elseif ($i == 3)
						$passartill4 = trim($newargument[$i]);
					elseif ($i == 4)
						$passartill5 = trim($newargument[$i]);
					elseif ($i == 5)
						$passartill6 = trim($newargument[$i]);
					elseif ($i == 6)
						$passartill7 = trim($newargument[$i]);
				}
				/*
				if ($passartill2 == $alias) {
					echo "1: " . $passartill1 . "<br>"; 
					echo "2: " . $passartill2 . "<br>"; 
				}
				*/
				if (($passartill1 === $alias) || ($passartill2 === $alias) || ($passartill3 === $alias) || ($passartill4 === $alias) || ($passartill5 === $alias) || ($passartill6 === $alias) || ($passartill7 === $alias)) {
					echo "<tr>";
					echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr . "</td>";
					echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.se/info.php?article=" . $row->artnr . "\">" . $row->tillverkare . " " . $row->beskrivning . "</a></td>";
					echo "\t\t<td class=\"$rowcolor align_center\">" . $row->lagersaldo . "</td>";
					if ($row->utgangen == -1) {
						echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_red.png\"></td>";
					} else {
						echo "\t\t<td class=\"$rowcolor align_center\">&nbsp;</td>";
					}
					if ($row->ej_med == -1) {
						echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_red.png\"></td>";
					} else {
						echo "\t\t<td class=\"$rowcolor align_center\">&nbsp;</td>";
					}
					// echo "\t\t<td class=\"$rowcolor align_left\">" . $row->passartill . "</td>";
					echo "\t\t<td class=\"$rowcolor align_left\">";
					echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=" . $passartill1 . "\">" . $passartill1 . "</a> ";
					if (strlen(passartill2) > 1) {
						echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=" . $passartill2 . "\">" . $passartill2 . "</a> ";
					}
					if (strlen($passartill3) > 1) {
						echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=" . $passartill3 . "\">" . $passartill3 . "</a> ";
					}
					if (strlen($passartill4) > 1) {
						echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=" . $passartill4 . "\">" . $passartill4 . "</a> ";
					}
					if (strlen($passartill5) > 1) {
						echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=" . $passartill5 . "\">" . $passartill5 . "</a> ";
					}
					if (strlen($passartill6) > 1) {
						echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=" . $passartill6 . "\">" . $passartill6 . "</a> ";
					}
					if (strlen($passartill7) > 1) {
						echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=" . $passartill7 . "\">" . $passartill7 . "</a> ";
					}
					echo "</td>\n";
					// echo "\t\t<td class=\"align_center\">Ta bort</td>";
					echo "</tr>";
					
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}
					unset($passartill1,$passartill2,$passartill3,$passartill4,$passartill5,$passartill6,$passartill7);
					$countrow++;
				
				}
	
	
			}
	
		}
	
		echo "<tr>";
		echo "\t\t<td colspan=\"10\" class=\"bold align_left\">Totalt: " . $countrow . " st</td>";
		echo "</tr>";
		echo "</table>";
	
	}

	function listAllAlias() {
	
		$desiderow = true;
		$countrow = 0;
	
		$select  = "SELECT a.artnr, k.kategori, (SELECT COUNT(*) FROM Passartill pt WHERE pt.passartill = a.artnr ) AS Antalartiklar ";
		$select .= "FROM cyberphoto.Artiklar a ";
		$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
		$select .= "WHERE a.kategori_id IN (325,416) ";
		$select .= "ORDER BY a.kategori_id DESC, a.artnr ";
		
		// echo $select;
		$res = mysqli_query(Db::getConnection(), $select);
		// $row = mysqli_fetch_object($res);
		
		echo "<h2>Lista över alla alias</h2>\n";
	
		echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"450\">";
		echo "<tr>";
		echo "<td class=\"bold align_left\" width=\"200\">Artikel nr</td>";
		echo "<td class=\"bold align_left\" width=\"150\">Kategori</td>";
		echo "<td class=\"bold align_center\">Antal</td>";
		echo "<td class=\"bold align_center\">&nbsp;</td>";
		echo "</tr>";
	
		if (mysqli_num_rows($res) > 0) {
	
			while ($row = mysqli_fetch_object($res)) {
					
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				
				echo "<tr>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=" . $row->artnr . "\">" . $row->artnr . "</a></td>";
				echo "\t\t<td class=\"$rowcolor align_left\">" . $row->kategori . "</td>";
				echo "\t\t<td class=\"$rowcolor align_center\">" . $row->Antalartiklar . "</td>";
				echo "\t\t<td class=\"align_center\">&nbsp;</td>";
				echo "</tr>";
				
				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				$countrow++;
	
			}
	
		}
	
		echo "<tr>";
		echo "\t\t<td colspan=\"10\" class=\"bold align_left\">Totalt: " . $countrow . " st</td>";
		echo "</tr>";
		echo "</table>";
	
	}
	
	function addAccessories($addartnr,$change,$addcomment,$addrecommended,$addcreatedby) {

		$addcomment = trim($addcomment);

		$updt  = "INSERT INTO cyberphoto.Passartill ";
		$updt .= "( ";
		$updt .= "artnr, ";
		$updt .= "passartill, ";
		if ($addcomment != "") {
			$updt .= "kommentar, ";
		}
		$updt .= "recommended, ";
		$updt .= "addTime, ";
		$updt .= "addIP, ";
		$updt .= "addBy ";
		$updt .= ") ";
		$updt .= "VALUES ";
		$updt .= "( ";
		$updt .= "'$addartnr', ";
		$updt .= "'$change', ";
		if ($addcomment != "") {
			$updt .= "'$addcomment', ";
		}
		$updt .= "'$addrecommended', ";
		$updt .= "now(), ";
		$updt .= "'" . $_SERVER['REMOTE_ADDR'] . "', ";
		$updt .= "'" . $addcreatedby . "' ";
		$updt .= ") ";
		
		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);
		
	}
	
	function deleteAccessories($addartnr,$change) {

		$updt = "DELETE FROM cyberphoto.Passartill WHERE artnr = '" . $addartnr . "' AND passartill = '" . $change . "' ";		
		
		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);
		
	}
	
	function listAccessoriesOnProduct($article) {
		global $success, $wrongmess, $replace_artnr;
	
		$desiderow = true;
		$countrow = 0;
	
		$select  = "SELECT pt.artnr, pt.passartill, pt.recommended, k.kategori, t.tillverkare, a.beskrivning, a.lagersaldo, a.utgangen, a.ej_med ";
		$select .= "FROM cyberphoto.Passartill pt ";
		$select .= "JOIN Artiklar a ON a.artnr = pt.passartill ";
		$select .= "JOIN Kategori k ON k.kategori_id = a.kategori_id ";
		$select .= "JOIN Tillverkare t ON t.tillverkar_id = a.tillverkar_id ";
		$select .= "WHERE pt.artnr = '" . $article . "' ";
		$select .= "ORDER BY k.kategori ";
		
		$res = mysqli_query(Db::getConnection(), $select);
		
		echo "<h2>Artikel $article (<span class=\"span_blue\">" . $this->getArticleName($article) . "</span>) finns som tillbehör till följande produkter/alias</h2>\n";
	
		echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
		echo "<tr>";
		echo "<td class=\"bold align_left\" width=\"120\">Kategori</td>";
		echo "<td class=\"bold align_left\" width=\"600\">Artikel</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Lagersaldo</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Utgången</td>";
		echo "<td class=\"bold align_center\" width=\"75\">Ej med</td>";
		echo "<td class=\"bold align_center\" width=\"120\">Rekommenderat</td>";
		echo "<td class=\"bold align_center\" width=\"90\">&nbsp;</td>";
		echo "<td class=\"bold align_center\">&nbsp;</td>";
		echo "</tr>";
	
		if (mysqli_num_rows($res) > 0) {
	
			while ($row = mysqli_fetch_object($res)) {
					
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				
				$alias = $row->passartill;
				
				echo "<tr>";
				echo "\t\t<td class=\"$rowcolor align_left\">" . $row->kategori . "</td>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.se/info.php?article=" . $row->passartill . "\">" . $row->tillverkare . " " . $row->beskrivning . "</a></td>";
				echo "\t\t<td class=\"$rowcolor align_center\">" . $row->lagersaldo . "</td>";
				if ($row->utgangen == -1) {
					echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_red.png\"></td>";
				} else {
					echo "\t\t<td class=\"$rowcolor align_center\">&nbsp;</td>";
				}
				if ($row->ej_med == -1) {
					echo "\t\t<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_red.png\"></td>";
				} else {
					echo "\t\t<td class=\"$rowcolor align_center\">&nbsp;</td>";
				}
				echo "\t\t<td class=\"$rowcolor align_center\">" . $row->recommended . "</td>";
				echo "\t\t<td class=\"align_center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?alias=yes&change=$alias\">ändra</a></td>";
				echo "<td class=\"bold align_center\">&nbsp;</td>";
				/*
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.se/info.php?article=" . $row->artnr . "\">" . $row->beskrivning . "</a></td>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.fi/info.php?article=" . $row->artnr . "\">" . $row->beskrivning_fi . "</a> " . $mark . "</td>";
				echo "\t\t<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=" . $row->artnr . "&m_product_id=" . $row->m_product_id . "&force_lang=fi');\">�tg�rda</a></td>";
				*/
				echo "</tr>";
	
				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				$countrow++;
				
	
			}
	
		}
	
		echo "<tr>";
		echo "\t\t<td colspan=\"10\" class=\"bold align_left\">Totalt: " . $countrow . " st</td>";
		echo "</tr>";
		echo "</table>";
		
		if ($countrow > 1) {
		
			echo "<h2>Ersätt med nytt artikel nummer (ingen ångerrätt finns...) Tänk efter före!</h2>\n";
	
			echo "<div class=\"\">\n";
			// echo "<div class=\"floatright right20\">\n";
			// echo "<div style=\"float: left; width: 210px;\">\n";
			echo "<form method=\"GET\">\n";
			echo "<input type=\"hidden\" value=\"yes\" name=\"replace_article\">\n";
			echo "<input type=\"hidden\" value=\"" . $article . "\" name=\"origin_article\">\n";
			// echo "</div>\n";
			echo "<input style=\"background-color: #FFFDD3; width: 420px;\" placeholder=\"Skriv in det artikelnummer du vill ha istället\" type=\"text\" name=\"replace_artnr\" value=\"" . $replace_artnr . "\">\n";
			echo "\n";
			echo "\n";
			echo "</form>\n";
			if ($success == "no") {
				echo "<p class=\"boldit_red\">- Artikelnummer <span class=\"span_blue\">$replace_artnr</span> finns inte. Vänligen kolla upp detta!</p>";
			}
			echo "</div>\n";
			
		}
	
	}
	
	function replaceAccessories($origin_article,$replace_artnr) {

		$updt = "UPDATE cyberphoto.Passartill pt SET pt.artnr = '" . $replace_artnr . "' WHERE pt.artnr = '" . $origin_article . "' ";		
		
		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);
		
	}
	
	function makeProductDataUpdate($addalias,$addsearchterms,$artnr) {

		$updt  = "UPDATE cyberphoto.Artiklar ";		
		$updt .= "SET ";
		if ($addalias != "") {
			$updt .= "passartill = '" . trim($addalias) . "', ";
		} else {
			$updt .= "passartill = NULL, ";
		}
		if ($addsearchterms != "") {
			$updt .= "searchTerms = '" . trim($addsearchterms) . "' ";
		} else {
			$updt .= "searchTerms = NULL ";
		}
		$updt .= "WHERE artnr = '" . $artnr . "' ";		
		
		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);
		
	}

	static function getProductFriendlyURL($artnr) {
		global $sv, $fi, $no;
		
		$select  = "SELECT t.Tillverkare, a.beskrivning, afi.beskrivning_fi, a.m_product_id, k.kategori_id_parent, k.kategori, k.kategori_fi, k.kategori_no ";
		$select .= "FROM cyberphoto.Artiklar a ";
		$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
		$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "LEFT JOIN Artiklar_fi afi ON a.artnr = afi.artnr_fi ";
		$select .= "WHERE a.artnr = '$artnr' ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		
		if ($rows->tillverkare <> ".") {
           $headerFU = $rows->tillverkare . " ";
		}
		if ($fi && !$sv) {
			if ($rows->beskrivning_fi != "") {
				$headerFU .= $rows->beskrivning_fi;
			} else {
				$headerFU .= $rows->beskrivning;
			}
		} else {
			$headerFU .= $rows->beskrivning;
		}

		if (CCategories::getMainCategory($rows->kategori_id_parent) == 585) {
			$FURL = "/mobiltelefoni/";
		} elseif (CCategories::getMainCategory($rows->kategori_id_parent) == 1000045) {
			$FURL = "/batterier/";
		} elseif (CCategories::getMainCategory($rows->kategori_id_parent) == 584) {
			$FURL = "/outdoor/";
		} elseif (CCategories::getMainCategory($rows->kategori_id_parent) == 1000147) {
			$FURL = "/outdoor/";
		} else {
			$FURL = "/foto-video/";
		}
		
		if ($fi && !$sv) {
			$FURL = preg_replace("/jakt\-fritid/", "outdoor", $FURL);
			$FURL = preg_replace("/mobiltelefoni/", "mobiili", $FURL);
			$FURL = preg_replace("/mobiltelefoner/", "matkapuhelimet", $FURL);
			$FURL = preg_replace("/alla/", "kaikki", $FURL);
			$FURL = preg_replace("/batterier/", "akut", $FURL);
		} else {
			$FURL = preg_replace("/jakt\-fritid/", "outdoor", $FURL);
		}
		
		if ($fi && !$sv) {
			if ($rows->kategori_fi != "") {
				$categoryFU = $rows->kategori_fi;
			} else {
				$categoryFU = $rows->kategori;
			}
		} elseif ($no) {
			if ($rows->kategori_no != "") {
				$categoryFU = $rows->kategori_no;
			} else {
				$categoryFU = $rows->kategori_no;
			}
		} else {
			$categoryFU = $rows->kategori;
		}

		$FURL = $FURL . "product/" . $rows->m_product_id . "/";
		$FURL .= strtolower(Tools::replace_special_char(trim($categoryFU))) . "/";
		$FURL .= strtolower(Tools::replace_special_char(trim($headerFU)));
		
		return $FURL;
		
	}

	function getIfInPackages($artnr) {
	
		$select  = "SELECT COUNT(pp.artnr_paket) AS antal ";
		$select .= "FROM cyberphoto.Artiklar a ";
		$select .= "JOIN cyberphoto.Paketpriser pp ON a.Artnr = pp.artnr_paket ";
		$select .= "WHERE NOT (a.utgangen = -1) AND NOT (a.ej_med = -1) AND pp.artnr_del = '" . $artnr . "'";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
		
		if ($row->antal > 0) {
			echo "<div class=\"packagecomment_container align_center\">\n";
			echo "<a href=\"javascript:winPopupCenter(450, 800, '/order/admin/active_parcel.php?article=$artnr');\">\n";
			echo "Ingår i <b>" . $row->antal . " st</b> aktiva paket";
			echo "</a>\n";
			echo "</div>\n";
		}
	
		/*
		if (mysqli_num_rows($res) > 0) {
			while ($row = mysqli_fetch_object($res)) {
				$this->showMorePackages($row->artnr,$detail);
			}
		}
		*/
		
		
	}

	function listAllPackages($artnr) {
		
		$countrow = 0;
	
		$select  = "SELECT pp.artnr_paket, t.Tillverkare, a.beskrivning ";
		$select .= "FROM cyberphoto.Artiklar a ";
		$select .= "JOIN cyberphoto.Paketpriser pp ON a.Artnr = pp.artnr_paket ";
		$select .= "JOIN cyberphoto.Tillverkare t ON t.tillverkar_id = a.tillverkar_id ";
		$select .= "WHERE NOT (a.utgangen = -1) AND NOT (a.ej_med = -1) AND pp.artnr_del = '" . $artnr . "'";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
	
		echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"99%\">";
		echo "<tr>";
		echo "<td class=\"bold align_left\" width=\"120\">Artikel nr</td>";
		echo "<td class=\"bold align_left\">Paket</td>";
		// echo "<td class=\"bold align_center\">&nbsp;</td>";
		echo "</tr>";
	
		if (mysqli_num_rows($res) > 0) {
	
			while ($row = mysqli_fetch_object($res)) {
					
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				
				echo "<tr>";
				echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr_paket . "</td>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"/info.php?article=$row->artnr_paket\">" . $row->Tillverkare . " " . $row->beskrivning . "</a></td>";
				// echo "\t\t<td class=\"align_center\">&nbsp;</td>";
				echo "</tr>";
				
				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				$countrow++;
	
			}
	
		}
	
		echo "<tr>";
		echo "\t\t<td colspan=\"10\" class=\"bold align_left\">Totalt: " . $countrow . " st</td>";
		echo "</tr>";
		echo "</table>";

		
	}

	function displayTradeInInfo($m_product_id) {

		$countrow = 1;

			$select  = "SELECT p.qtyexposures, p.included_accessories, p.usedcomment, p.description , p.value ";
			$select .= "FROM m_product p ";
			$select .= "WHERE (NOT p.qtyexposures IS NULL OR NOT p.included_accessories IS NULL OR NOT p.usedcomment IS NULL) ";
			$select .= "AND p.m_product_id = $m_product_id ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $select) : false;
			// $res = pg_query($this->conn_ad, $select);
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					echo "<div class=\"tradein_container\">\n";
				
					while ($res && $row = pg_fetch_object($res)) {
					
						// $beskrivning = eregi_replace("\n", "<br>", $row[2]);
						
						if ($row->value != "") {
							echo "<div class=\"bold italic bottom5\">Vårt artikelnummer</div>\n";
							echo "<div class=\"bottom5\">\n";
							// echo "<div style=\"float: left; margin-right: 5px;\">" . date("Y-m-d", strtotime($row[0])) . "</div>\n";
							echo "<div>" . $row->value . "</i></div>\n";
							// echo "<div style=\"margin-top: 5px;\">" . $row->p.qtyexposures . "</div>\n";
							echo "</div>\n";
						}
						if ($row->description != "") {
							echo "<div class=\"bold italic bottom5\">Skick samt garanti</div>\n";
							echo "<div class=\"bottom5\">\n";
							// echo "<div style=\"float: left; margin-right: 5px;\">" . date("Y-m-d", strtotime($row[0])) . "</div>\n";
							echo "<div>" . $row->description . "</i></div>\n";
							// echo "<div style=\"margin-top: 5px;\">" . $row->p.qtyexposures . "</div>\n";
							echo "</div>\n";
						}
						if ($row->qtyexposures != "") {
							echo "<div class=\"bold italic bottom5\">Antal exponeringar</div>\n";
							echo "<div class=\"bottom5\">\n";
							// echo "<div style=\"float: left; margin-right: 5px;\">" . date("Y-m-d", strtotime($row[0])) . "</div>\n";
							echo "<div>" . $row->qtyexposures . "</i></div>\n";
							// echo "<div style=\"margin-top: 5px;\">" . $row->p.qtyexposures . "</div>\n";
							echo "</div>\n";
						}
						if ($row->included_accessories != "") {
							echo "<div class=\"bold italic bottom5\">Medföljande tillbehör</div>\n";
							echo "<div class=\"bottom5\">\n";
							// echo "<div style=\"float: left; margin-right: 5px;\">" . date("Y-m-d", strtotime($row[0])) . "</div>\n";
							echo "<div>" . $row->included_accessories . "</i></div>\n";
							// echo "<div style=\"margin-top: 5px;\">" . $row->p.qtyexposures . "</div>\n";
							echo "</div>\n";
						}
						if ($row->usedcomment != "") {
							echo "<div class=\"bold italic bottom5\">Begagnat notering</div>\n";
							echo "<div class=\"bottom5\">\n";
							// echo "<div style=\"float: left; margin-right: 5px;\">" . date("Y-m-d", strtotime($row[0])) . "</div>\n";
							echo "<div>" . $row->usedcomment . "</i></div>\n";
							// echo "<div style=\"margin-top: 5px;\">" . $row->p.qtyexposures . "</div>\n";
							echo "</div>\n";
						}

						/*
						echo "<div style=\"padding: 5px; margin-top: 10px; background: #FFEFDF; border-bottom: 1px solid #d0d1d5; border-right: 1px solid #d0d1d5;\">\n";
						echo "<div style=\"float: left; margin-right: 25px;\">$row[0]</div>\n";
						echo "<div><i>$row[1]</i></div>\n";
						echo "<div style=\"margin-top: 5px;\">$row[2]</div>\n";
						echo "</div>\n";
						*/
						$countrow++;

					}
				
					echo "</div>\n";

				} else {
					
					return;
					
				}
			
	}

	public function getSaleStartDate($m_product_id)
	{
		$db = Db::getConnectionAD(false);

		$sql = "SELECT launchdate FROM m_product WHERE m_product_id = $1";
		$res = ($db) ? @pg_query_params($db, $sql, array((int)$m_product_id)) : false;

		if (!$res) {
			return null;
		}

		$row = $res ? pg_fetch_assoc($res) : null;
		if (!$row) {
			return null;
		}

		$v = trim((string)$row['launchdate']);
		if ($v === '') {
			return null;
		}

		return $v;
	}

	
}
?>
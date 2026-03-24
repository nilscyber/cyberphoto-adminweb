<?php
require_once("CCheckIpNumber.php");
/*
PHP
author		Stefan Sjöberg
*/

include("connections.php");

Class CCampaignCheck {
	var $conn_my;
	var $conn_ad;

	function __construct() {
		global $conn_ad;
			
		$this->conn_my = Db::getConnection();
		$this->conn_ad = Db::getConnectionAD();
		
	}

	function displayPictureIfValid() {
		global $sv, $fi, $site_foto, $site_mobil, $no, $currentUrl;

		$select = "SELECT cnt, campaign_link, campaign_link_no, campaign_link_fi, campaign_link_fi_sv, link, link_no, link_fi, link_fi_sv, artnr, kategori_id, tillverkar_id, ";	
		$select .= "descrptn, descrptn_fi, descrptn_fi_sv, descrptn_no, site ";	
		$select .= "FROM discountCodes ";	

		if ($fi && !$sv) {
			$select  .= "WHERE NOT campaign_link_fi IS Null AND validDate > now() AND validFrom < now() ";	
		} elseif ($fi && $sv) {
			$select  .= "WHERE NOT campaign_link_fi_sv IS Null AND validDate > now() AND validFrom < now() ";	
		} elseif ($no) {
			$select  .= "WHERE NOT campaign_link_no IS Null AND validDate > now() AND validFrom < now() ";	
		} else {
			$select  .= "WHERE NOT campaign_link IS Null AND validDate > now() AND validFrom < now() ";	
		}

		if ($fi) {
			$select  .= "AND active_fi = -1 ";
		} elseif ($no) {
			$select  .= "AND active_no = -1 ";
		} else {
			$select  .= "AND active_se = -1 ";
		}
		if ($fi || $no) {
			$select  .= "AND (site = 0 OR site = 1 OR site = 2 OR site = 3) ";
		} elseif (preg_match("/outdoor/i", $currentUrl)) {
			$select  .= "AND (site = 0 OR site = 3) ";
		} elseif (preg_match("/mobiltelefon/i", $currentUrl) || preg_match("/mobiili/i", $currentUrl) || $site_mobil) {
			$select  .= "AND (site = 0 OR site = 2) ";
		} else {
			$select  .= "AND (site = 0 OR site = 1) ";
		}
		/*
		if ($site_foto) {
			$select  .= "AND (site = 0 OR site = 1) ";
		} elseif ($site_mobil) {
			$select  .= "AND (site = 0 OR site = 2) ";
		} elseif ($site_hobby) {
			$select  .= "AND (site = 0 OR site = 3) ";
		}
		*/
		// specialare under vår maj-kampanj
		// $select  .= "AND NOT discountCode like '%maj_%' ";

		$select  .= "AND isPicture_show = -1 ";

		$select  .= "ORDER BY cnt DESC ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);

			if ($this->getValidCampaign($cnt)) {

				if ($fi && !$sv) {
					
					if ($link_fi == "") {
						if ($artnr != "" && $kategori_id == "" && $tillverkar_id == "") {
							$link_fi = "/info.php?article=$artnr";
						}
						if ($this->checkPaKopet($cnt)) {
							// $link_fi = "/pri_campaign.php?ID=$cnt";
							if ($site == 2) {
								$link_fi = "/mobiltelefoni/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn_fi));
							} elseif ($site == 3) {
								$link_fi = "/outdoor/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn_fi));
							} else {
								$link_fi = "/foto-video/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn_fi));
							}
						}
					}
				
					echo "<a href=\"$link_fi\"><img border=\"0\" src=\"/../../$campaign_link_fi\"></a><br>";
				
				} elseif ($fi && $sv) {

					if ($link_fi_sv == "") {
						if ($artnr != "" && $kategori_id == "" && $tillverkar_id == "") {
							$link_fi_sv = "/info.php?article=$artnr";
						}
						if ($this->checkPaKopet($cnt)) {
							// $link_fi_sv = "/pri_campaign.php?ID=$cnt";
							if ($site == 2) {
								$link_fi_sv = "/mobiltelefoni/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn_fi_sv));
							} elseif ($site == 3) {
								$link_fi_sv = "/outdoor/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn_fi_sv));
							} else {
								$link_fi_sv = "/foto-video/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn_fi_sv));
							}
						}
					}
				
					echo "<a href=\"$link_fi_sv\"><img border=\"0\" src=\"/../../$campaign_link_fi_sv\"></a><br>";
				
				} elseif ($no) {

					if ($link_no == "") {
						if ($artnr != "" && $kategori_id == "" && $tillverkar_id == "") {
							$link_no = "/info.php?article=$artnr";
						}
						if ($this->checkPaKopet($cnt)) {
							// $link_no = "/pri_campaign.php?ID=$cnt";
							if ($site == 2) {
								$link_no = "/mobiltelefoni/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn_no));
							} elseif ($site == 3) {
								$link_no = "/outdoor/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn_no));
							} else {
								$link_no = "/foto-video/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn_no));
							}
						}
					}
				
					echo "<a href=\"$link_no\"><img border=\"0\" src=\"/../../$campaign_link_no\"></a><br>";
				
				} else {
					
					if ($link == "") {
						if ($artnr != "" && $kategori_id == "" && $tillverkar_id == "") {
							$link = "/info.php?article=$artnr";
						}
						if ($this->checkPaKopet($cnt)) {
							// $link = "/pri_campaign.php?ID=$cnt";
							if ($site == 2) {
								$link = "/mobiltelefoni/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn));
							} elseif ($site == 3) {
								$link = "/outdoor/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn));
							} else {
								$link = "/foto-video/kampanjer/$cnt/" . strtolower(Tools::replace_special_char($descrptn));
							}
						}
					}
				
					echo "<a href=\"$link\"><img border=\"0\" src=\"/../../$campaign_link\"></a><br>";

				}

					if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
						// echo "- <a href=\"javascript:winPopupCenter(500, 700, '/order/campaign_detail.php?ID=$cnt');\" style=\"text-decoration: none\">Se detaljer<br>";
						// echo "- <a target=\"_blank\" href=\"/order/admin/campaign.php?show=$cnt\" style=\"text-decoration: none\">Se detaljer<br>";
						echo "- <a target=\"_blank\" href=\"http://admin.cyberphoto.se/campaign.php?show=$cnt\" style=\"text-decoration: none\">Se detaljer<br>";
						// echo "<hr noshade color=\"#C0C0C0\" align=\"left\" width=\"525\" size=\"1\">";
					} else {
						echo "<br>";
					}
			}

		}
		
		$this->getExtraText();

	}

	function getExtraText() {
		global $sv, $fi, $no, $style;
	
		$language = $_SESSION['currentLocale'];
	
		$select  = "SELECT eiID, eiArtnr, eiText ";
		$select .= "FROM cyberphoto.Extrainfo ";
		$select .= "WHERE ((eiDependOnDate = 1 AND eiValidFrom < now() AND eiValidTo > now()) OR (eiDependOnDate = 0)) ";
		$select .= "AND eiIsActive = 1 AND eiHandleLikeCampaign = 1 AND eiLanguage = '" . $language . "' ";
	
		// echo $select;
	
		$res = mysqli_query(Db::getConnection(), $select);
		// $row = mysqli_fetch_object($res);
	
		if (mysqli_num_rows($res) > 0) {
			
			while ($row = mysqli_fetch_object($res)) {
				
				if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
					$linc = "/info.php?article=" . $row->eiArtnr;
					$lincadmin = "edit=1&eiID=" . $row->eiID . "&eiArtnr=" . $row->eiArtnr;
					echo "<a href=\"$linc\"><div class=\"align_left top5 bottom5\">" . $row->eiText . "</div></a>\n";
					echo "<div class=\"comment_area\"><a href=\"javascript:winPopupCenter(450, 800, '/order/comment_update.php?$lincadmin');\">- Se detaljer</a></div>\n";
				} else {
					$linc = "/info.php?article=" . $row->eiArtnr;
					echo "<a href=\"$linc\"><div class=\"align_left top5 bottom5\">" . $row->eiText . "</div></a>\n";
				}
			
			}
		}
	}
	
	
	function displayPictureIfNotValid() {
		global $sv, $fi, $site_foto, $no;

		$select  = "SELECT cnt, campaign_link, campaign_link_fi, campaign_link_fi_sv, link, link_fi, link_fi_sv ";	
		$select  .= "FROM discountCodes ";	

		if ($fi && !$sv) {
			$select  .= "WHERE NOT campaign_link_fi IS Null AND validDate > now() AND validFrom < now() ";	
		} elseif ($fi && $sv) {
			$select  .= "WHERE NOT campaign_link_fi_sv IS Null AND validDate > now() AND validFrom < now() ";	
		} elseif ($no) {
			$select  .= "WHERE NOT campaign_link_no IS Null AND validDate > now() AND validFrom < now() ";	
		} else {
			$select  .= "WHERE NOT campaign_link IS Null AND validDate > now() AND validFrom < now() ";	
		}

		if ($fi) {
			$select  .= "AND active_fi = -1 ";
		} elseif ($no) {
			$select  .= "AND active_no = -1 ";
		} else {
			$select  .= "AND active_se = -1 ";
		}
		if ($site_foto) {
			$select  .= "AND (site = 0 OR site = 1) ";
		} elseif ($site_mobil) {
			$select  .= "AND (site = 0 OR site = 2) ";
		} elseif ($site_hobby) {
			$select  .= "AND (site = 0 OR site = 3) ";
		}

		$select  .= "AND NOT discountCode like '%maj_%' ";

		$select  .= "AND isPicture_show = -1 ";

		$select  .= "ORDER BY cnt DESC ";

		$res = mysqli_query($this->conn_my, $select);

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);

			if (!$this->getValidCampaign($cnt)) {

				if ($fi && !$sv) {
				
					echo "<a href=\"$link_fi\"><img border=\"0\" src=\"/../../$campaign_link_fi\"></a><br>";
				
				} elseif ($fi && $sv) {
				
					echo "<a href=\"$link_fi_sv\"><img border=\"0\" src=\"/../../$campaign_link_fi_sv\"></a><br>";
				
				} else {
				
					echo "<a href=\"$link\"><img border=\"0\" src=\"/../../$campaign_link\"></a><img border=\"0\" src=\"/kampanj/not_valid.gif\"><br>";
					if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
						// echo "- <a href=\"javascript:winPopupCenter(500, 700, '/order/campaign_detail.php?ID=$cnt');\" style=\"text-decoration: none\">Se detaljer";
						// echo "- <a target=\"_blank\" href=\"/order/admin/campaign.php?show=$cnt\" style=\"text-decoration: none\">Se detaljer<br>";
						echo "- <a target=\"_blank\" href=\"http://admin.cyberphoto.se/campaign.php?show=$cnt\" style=\"text-decoration: none\">Se detaljer<br>";
						echo "<hr noshade color=\"#C0C0C0\" align=\"left\" width=\"525\" size=\"1\">";
					}

				}

			}

		}

	}

	function displayPictureIfNotDate() {
		global $sv, $fi, $site_foto, $no;
		
		$nnnn = date('Y-m-d H:i:s', time());
		$mmmm = date('Y-m-d H:i:s', strtotime("$nnnn -3 month")); // tar fram datumet för tre månader sedan
		
		$select  = "SELECT cnt, campaign_link, campaign_link_fi, campaign_link_fi_sv, link, link_fi, link_fi_sv ";	
		$select  .= "FROM discountCodes ";	

		if ($fi && !$sv) {
			$select  .= "WHERE NOT campaign_link_fi IS Null AND validDate > now() AND validFrom < now() ";	
		} elseif ($fi && $sv) {
			$select  .= "WHERE NOT campaign_link_fi_sv IS Null AND validDate > now() AND validFrom < now() ";	
		} elseif ($no) {
			$select  .= "WHERE NOT campaign_link_no IS Null AND validDate > now() AND validFrom < now() ";	
		} else {
			$select  .= "WHERE NOT campaign_link IS Null AND validDate < now() AND validFrom > '$mmmm' ";	
		}

		if ($fi) {
			$select  .= "AND active_fi = -1 ";
		} elseif ($no) {
			$select  .= "AND active_fi = -1 ";
		} else {
			$select  .= "AND active_se = -1 ";
		}
		if ($site_foto) {
			$select  .= "AND (site = 0 OR site = 1) ";
		} elseif ($site_mobil) {
			$select  .= "AND (site = 0 OR site = 2) ";
		} elseif ($site_hobby) {
			$select  .= "AND (site = 0 OR site = 3) ";
		}

		// $select  .= "AND NOT discountCode like '%maj_%' ";

		$select  .= "AND isPicture_show = -1 ";

		$select  .= "ORDER BY cnt DESC ";
		
		// echo $select;

		$res = mysqli_query($this->conn_my, $select);

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);

			// if ($this->getValidCampaign($cnt)) {

				if ($fi && !$sv) {
				
					echo "<a href=\"$link_fi\"><img border=\"0\" src=\"/../../$campaign_link_fi\"></a><br>";
				
				} elseif ($fi && $sv) {
				
					echo "<a href=\"$link_fi_sv\"><img border=\"0\" src=\"/../../$campaign_link_fi_sv\"></a><br>";
				
				} else {
				
					echo "<a href=\"$link\"><img border=\"0\" src=\"/../../$campaign_link\"></a><br>";
					if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
						echo "- <a href=\"javascript:winPopupCenter(500, 700, '/order/campaign_detail.php?ID=$cnt');\" style=\"text-decoration: none\">Se detaljer";
						echo "<hr noshade color=\"#C0C0C0\" align=\"left\" width=\"525\" size=\"1\">";
					}

				}

			// }

		}

	}

	function getValidCampaign($cnt) {

		global $fi, $sv;

		$select = "SELECT * FROM discountCodeArticle WHERE dscntId = " . $cnt;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $select;
		}
		$res = mysqli_query($this->conn_my, $select);
		
		$row = mysqli_fetch_object($res);
		
		if (mysqli_num_rows($res) == 0) {				

			return true;
		
		} else {
		
			if ($this->getValidCampaignArticle($row->includedArticles_id)) {
			
				return true;
			
			} else {
			
				return false;
			
			}
		}
	}

	function getValidCampaignArticle($cnt) {

		global $fi, $sv;
		
		unset ($allCheck);
		
		$select =  "SELECT discountCodeIncludedArticles.*, Artiklar.lagersaldo, Artiklar.utgangen, Artiklar.ej_med  ";
		$select .=  "FROM discountCodeIncludedArticles, Artiklar ";
		$select .=  "WHERE discountCodeIncludedArticles.includedArticles = Artiklar.artnr ";
		$select .=  "AND discountCodeIncludedArticles.includedArticles_id = " . $cnt . ""; 
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $select;
		}
		
		$res = (mysqli_query($this->conn_my, $select));
		
		if (mysqli_num_rows($res) > 0) {
		
			while ($rw = mysqli_fetch_object($res)) {
							
				if (($rw->lagersaldo >= $rw->qty || $rw->utgangen == 0 ) && ($rw->lagersaldo > 0 || $rw->allowNoStock == -1) && $allCheck = 1) {
			
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
					
					// echo $select;
					// echo $rw->lagersaldo;
					// echo $rw->qty;
					// echo $rw->utgangen;
					// echo $rw->allowNoStock;
					}
				
					$allCheck = 1;
		
				} else {
			
					unset ($allCheck);
					return false;
					break;
			
				}
			
			}
		} else { // om ingen produkt finns med "på köpet" så skall bild och text visas
			return true;
		}
		
		if ($allCheck == 1) {
			return true;
		}
	}

	function showCampaignOldStyle() {
		global $sv, $fi, $site_foto;

		if (!$fi) {

			// $select  = "SELECT DISTINCT campaignLink FROM Artiklar WHERE NOT campaignLink IS Null AND NOT campaignLink like '%maj_%' AND NOT campaignLink like '%mamma%' AND NOT campaignLink like '%fotoskola%' AND NOT campaignLink = '1' AND NOT kategori_ID = 486 AND NOT kategori_ID = 511 AND NOT kategori_ID = 512 AND NOT Artiklar.kategori_id = 513 AND NOT ej_med = -1 ";	
			$select  = "SELECT DISTINCT campaignLink ";	
			$select  .= "FROM Artiklar ";	
			$select  .= "WHERE NOT (campaignLink = '') ";	
			$select  .= "AND NOT (campaignLink = '1') ";	
			$select  .= "AND NOT (campaignLink like '%maj_%') ";	
			$select  .= "AND NOT (campaignLink like '%mamma%') ";	
			$select  .= "AND NOT (campaignLink like '%fotoskola%') ";	
			$select  .= "AND NOT (kategori_ID = 486)  ";	
			$select  .= "AND NOT (kategori_ID = 511) ";	
			$select  .= "AND NOT (kategori_ID = 512) ";	
			$select  .= "AND NOT (kategori_id = 513) ";	
			$select  .= "AND NOT ej_med = -1 ";	
			
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				// echo $select;
			}
			
			$res = mysqli_query($this->conn_my, $select);

			while ($row = mysqli_fetch_array($res)) {

				extract ($row);

				// echo $campaignLink . "<br>";
				include ("kampanj/$campaignLink");
				// echo "<br>";
			}

		}

		if ($fi) {

			$select  = "SELECT DISTINCT campaignLink_fi ";	
			$select  .= "FROM Artiklar ";	
			$select  .= "WHERE NOT campaignLink_fi IS Null AND NOT campaignLink_fi = '1' AND NOT kategori_ID = 486 AND NOT kategori_ID = 511 AND NOT kategori_ID = 512 AND NOT Artiklar.kategori_id = 513 ";	

			$res = mysqli_query($this->conn_my, $select);
			
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				// echo $select;
			}

			while ($row = mysqli_fetch_array($res)) {

				extract ($row);

				// echo $campaignLink . "<br>";
				include ("kampanj/$campaignLink_fi");
				// echo "<br>";
			}

		}
		
	}

	function getCampaignDetail($ID) {

		$select  = "SELECT * ";	
		$select  .= "FROM discountCodes ";	
		$select  .= "WHERE cnt = '$ID' ";	

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $select;
		}

		$res = mysqli_query($this->conn_my, $select);
		
		echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);
			
			// $aterstar = $this->getDaysLeft($validDate);
			$aterstar = $this->getTimeLeftNew($validDate);
			
			echo "<tr>";
			echo "<td>Rabattkod</td>";
			// echo "<td class=\"boldit\">" . $discountCode . "</td>";
			// echo "<td class=\"boldit\"><a class=\"boldit_underline\" target=\"_blank\" href=\"/order/admin/campaign.php?show=" . $cnt . "\">" . $discountCode . "</a></td>";
			echo "<td class=\"boldit\"><a class=\"boldit_underline\" target=\"_blank\" href=\"campaign.php?show=" . $cnt . "\">" . $discountCode . "</a></td>";
			echo "<td class=\"boldit\">&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>Giltig från:</td>";
			echo "<td class=\"boldit\">" . date("Y-m-d H:i:s", strtotime($validFrom)) . "</td>";
			echo "<td class=\"boldit\">&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>Giltig till:</td>";
			echo "<td class=\"boldit\">" . date("Y-m-d H:i:s", strtotime($validDate)) . "</td>";
			// echo "<td>&nbsp;&nbsp;(" . $aterstar . "&nbsp;dagar kvar)</td>";
			echo "<td>&nbsp;&nbsp;(" . $aterstar . " kvar)</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>Ansvarig:</td>";
			echo "<td class=\"boldit\">" . $by_user . "</td>";
			echo "<td class=\"boldit\">&nbsp;</td>";
			echo "</tr>";
			echo "<td>Kommentar:</td>";
			echo "<td colspan=\"2\" class=\"boldit\">" . $notes . "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>Aktiv i:</td>";
			echo "<td class=\"boldit\">";
			if ($active_se == -1) {
				// echo "<img border=\"0\" src=\"/order/admin/sv_mini.jpg\">&nbsp;";
				echo "<img border=\"0\" src=\"sv_mini.jpg\">&nbsp;";
			}
			if ($active_no == -1) {
				// echo "<img border=\"0\" src=\"/order/admin/no_mini.jpg\">&nbsp;";
				echo "<img border=\"0\" src=\"no_mini.jpg\">&nbsp;";
			}
			if ($active_fi == -1) {
				// echo "<img border=\"0\" src=\"/order/admin/fi_mini.jpg\">&nbsp;";
				echo "<img border=\"0\" src=\"fi_mini.jpg\">&nbsp;";
			}
			echo "</td>";
			echo "<td class=\"boldit\">&nbsp;</td>";
			echo "</tr>";
			echo "</tr>";
			echo "<td colspan=\"3\"><hr noshade color=\"#000000\" align=\"left\" size=\"1\"></td>";
			echo "</tr>";
			if ($artnr != "" || $kategori_id != "" || $tillverkar_id != "") {
				echo "</tr>";
				echo "<td class=\"boldit\" colspan=\"3\">Om följande kriterier uppfylls</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>Artnr:</td>";
				echo "<td class=\"boldit\">" . $artnr . "</td>";
				echo "<td class=\"boldit\">&nbsp;</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>Kategori:</td>";
				echo "<td class=\"boldit\">" . $kategori_id . "</td>";
				echo "<td class=\"boldit\">&nbsp;</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>Tillverkare:</td>";
				echo "<td class=\"boldit\">" . $tillverkar_id . "</td>";
				echo "<td class=\"boldit\">&nbsp;</td>";
				echo "</tr>";
				echo "</tr>";
				echo "<td class=\"boldit\" colspan=\"3\">Ges följande rabatter</td>";
				echo "</tr>";
				if ($discountPercent != "") {
					echo "<tr>";
					echo "<td>Avdrag:</td>";
					echo "<td class=\"boldit\">" . round($discountPercent*100,0) . " %</td>";
					echo "<td class=\"boldit\">&nbsp;</td>";
					echo "</tr>";
				} elseif ($discountAmount != "") {
					echo "<tr>";
					echo "<td>Avdrag:</td>";
					echo "<td class=\"boldit\">" . $discountAmount . " + moms</td>";
					echo "<td class=\"boldit\">&nbsp;</td>";
					echo "</tr>";
				} elseif ($discountOutprice != "") {
					echo "<tr>";
					echo "<td>Nytt utpris:</td>";
					echo "<td class=\"boldit\">" . $discountOutprice . " + moms</td>";
					echo "<td class=\"boldit\">&nbsp;</td>";
					echo "</tr>";
				} else {
					echo "</tr>";
					echo "<td class=\"boldit_italic\" colspan=\"3\">Inga rabatter verkar finnas på denna kampanj</td>";
					echo "</tr>";
				}
				echo "</tr>";
				echo "<td colspan=\"3\"><hr noshade color=\"#000000\" align=\"left\" size=\"1\"></td>";
				echo "</tr>";
			} else {
				echo "</tr>";
				echo "<td class=\"boldit_italic\" colspan=\"3\">Följande artiklar omfattas</td>";
				echo "</tr>";
				$this->getCampaignArticles($cnt);
			}

		}

		echo "</table>";

	}

	function getCampaignArticles($cnt) {

		$showarticlesincluded = false;

		$select  = "SELECT discountCodeArticle.*, Artiklar.beskrivning, Tillverkare.tillverkare ";	
		$select  .= "FROM discountCodeArticle ";	
		$select  .= "JOIN Artiklar ON discountCodeArticle.artnr = Artiklar.artnr ";	
		$select  .= "JOIN Tillverkare ON Tillverkare.tillverkar_id = Artiklar.tillverkar_id ";	
		$select  .= "WHERE discountCodeArticle.dscntId = '$cnt' ";	
		$select  .= "ORDER BY Tillverkare.tillverkare, Artiklar.beskrivning ";	

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = mysqli_query($this->conn_my, $select);
		
		while ($row = mysqli_fetch_array($res)) {

			extract ($row);
			
			echo "<tr>";
			// echo "<td>Artikel</td>";
			echo "<td class=\"boldit\">" . $artnr . "</td>";
			echo "<td colspan=\"2\"><a target=\"_blank\" href=\"/info.php?article=$artnr\">" . $tillverkare . "&nbsp;" . $beskrivning . "</a></td>";
			echo "</tr>";
			
			$showarticlesincluded = true;

		}

			echo "</tr>";
			echo "<td colspan=\"3\"><hr noshade color=\"#000000\" align=\"left\" size=\"1\"></td>";
			echo "</tr>";
			echo "</tr>";
			echo "<td class=\"boldit_italic\" colspan=\"3\">Följande artiklar skickas med produkterna ovan</td>";
			echo "</tr>";

			if ($showarticlesincluded) {
				$this->getIncludesArticlesDetail($includedArticles_id);
			}
		
	}

	function getIncludesArticlesDetail($includedArticles_id) {

		$select  = "SELECT discountCodeIncludedArticles.*, Artiklar.lagersaldo ";	
		$select  .= "FROM discountCodeIncludedArticles ";	
		$select  .= "JOIN Artiklar ON discountCodeIncludedArticles.includedArticles = Artiklar.artnr ";	
		$select  .= "WHERE discountCodeIncludedArticles.includedArticles_id = '$includedArticles_id' ";	

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $select;
		}

		$res = mysqli_query($this->conn_my, $select);
		
		while ($row = mysqli_fetch_array($res)) {

			extract ($row);
			
			echo "<tr>";
			echo "<td>" . $qty . " st</td>";
			echo "<td><a class=\"boldit\" target=\"_blank\" href=\"/info.php?article=$includedArticles\">" . $includedArticles . "</a></td>";
			if ($allowNoStock == -1) {
				echo "<td class=\"boldit_green\">&nbsp;tillåt slut i lager (" . $lagersaldo . " st)</td>";
			} else {
				echo "<td class=\"boldit_red\">&nbsp;tillåt EJ slut i lager (" . $lagersaldo . " st)</td>";
			}
			echo "</tr>";

		}
		
			echo "<tr>";
			echo "<td colspan=\"3\"><hr noshade color=\"#000000\" align=\"left\" size=\"1\"></td>";
			echo "</tr>";

	}

	function getDaysLeft($frontDateTo) {

		$now = time();
		$timeto = strtotime($frontDateTo);
		$diff = $timeto - $now;
		$sek = $diff % 60;
		$min = ($diff / 60) % 60;
		$hour = ($diff / 3600);
		$days = ($diff / 86400);
		$days = floor($days);
		$days = round($days, 0);
		return $days;
	}

	function getTimeLeft($frontDateTo,$revers=null) {

		$justnu = strtotime("now");
		$slutar = strtotime($frontDateTo);
		if ($revers) {
			$sekdiff = $justnu - $slutar;
		} else {
			$sekdiff = $slutar - $justnu;
		}
		
		if ($sekdiff < 60) {
			$displayfrom = round($sekdiff,0) . " sekunder";
		} elseif ($sekdiff > 59 && $sekdiff < 5401) {
			$displayfrom = round($sekdiff/60,0) . " minuter";
		} elseif ($sekdiff > 5400 && $sekdiff < 172800) {
			$displayfrom = round($sekdiff/60/60,0) . " timmar";
		} else {
			$displayfrom = round($sekdiff/60/60/24,0) . " dagar";
		}
		
		return $displayfrom;
		
	}

	static function getTimeLeftNew($frontDateTo,$revers=null) {
		global $frameless;

		$justnu = strtotime("now");
		$slutar = strtotime($frontDateTo);
		if ($revers) {
			$sekdiff = $justnu - $slutar;
		} else {
			$sekdiff = $slutar - $justnu;
		}
		
		// echo $sekdiff;

		$now = new DateTime();
		$future_date = new DateTime($frontDateTo);

		$interval = $future_date->diff($now);

		if ($slutar < $justnu && preg_match("/product_updates\.php/i", $_SERVER['PHP_SELF'])) { // passerat
			return "<i>Körs inom kort</i>";
		} elseif ($sekdiff < 60) { // mindre än 1 minut
			return $interval->format("%s sek");
		} elseif ($sekdiff < 3600) { // mindre än en timma
			return $interval->format("%i min %s sek");
		} elseif ($sekdiff < 86400) { // mindre än ett dygn
			return $interval->format("%h tim %i min");
		// } elseif ($sekdiff < 2419200) { // mindre än 28 dygn
		// } elseif ($sekdiff < 2592000) { // mindre än 30 dygn
		} elseif ($sekdiff < 2678400) { // mindre än 31 dygn
			if ($frameless) {
				return $interval->format("%d dag %h tim %i min");
			} else {
				return $interval->format("%d dag %h tim");
			}
		} elseif ($sekdiff < 31536000) { // mindre än ett år
			return $interval->format("%m mån %d dag");
		} else {
			// return $interval->format("%y år, %m månad, %d dagar, %h timmar, %i minuter, %s sekunder");
			return $interval->format("%y år %m mån");
		}
		
		// return $displayfrom;
		
	}

	function getCampaign($type,$personal_discount) {
		global $article, $search_tillverkar_id, $search_kategori_id;

		$desiderow = true;
		$countrow = 0;

		if (preg_match("/campaign\.php/i", $_SERVER['PHP_SELF'])) {
			// echo "jopp";
		}
		echo "<div>\n";
		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"100%\">\n";
		echo "<tr>\n";
		// echo "<th width=\"50\">&nbsp;</th>\n";
		echo "<th width=\"70\">Avd</th>\n";
		echo "<th width=\"65\">Aktiv</th>\n";
		echo "<th width=\"130\">Rabattkod</th>\n";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		}
		echo "<th>&nbsp;</th>";
		if ($type == 3) {
			// echo "<th class=\"center\" width=\"130\">Gällde från</th>\n";
			echo "<th class=\"center\" width=\"130\">Gällde till</th>\n";
		} elseif ($type == 2) {
			// echo "<th class=\"center\" width=\"130\">Gäller från</th>\n";
			echo "<th class=\"center\" width=\"130\">Gäller till</th>\n";
		} else {
			// echo "<th class=\"center\" width=\"130\">Gäller från</th>\n";
			echo "<th class=\"center\" width=\"130\">Gäller till</th>\n";
		}
		if ($type == 3) {
			echo "<th class=\"center\" width=\"100\">Tid sedan</th>\n";
		} else {
			echo "<th class=\"center\" width=\"100\">Tid kvar</th>\n";
		}
		echo "<th class=\"center\" width=\"75\">Ansvarig</th>";
		echo "<th>Kampanjtext (SE)</th>";
		echo "<th>Intern notering</th>";
		echo "<th>&nbsp;</th>";
		echo "</tr>\n";
		
		if ($article != "") {

			$select = "
			SELECT c.*
			FROM cyberphoto.discountCodes c
			LEFT JOIN discountCodeArticle ca ON c.cnt = ca.dscntId
			WHERE
				CASE WHEN ca.artnr IS NOT NULL THEN
					ca.artnr = '" . $article . "'
				ELSE
					CASE 
						WHEN c.artnr IS NOT NULL THEN c.artnr = '" . $article . "'
						WHEN c.kategori_id IS NOT NULL AND c.tillverkar_id IS NOT NULL THEN (c.kategori_id = " . $search_kategori_id . " AND c.tillverkar_id = " . $search_tillverkar_id . ")
						WHEN c.kategori_id IS NULL THEN c.tillverkar_id = " . $search_tillverkar_id . "
						ELSE c.kategori_id = " . $search_kategori_id . "
					END
				END
			";										
			if ($type == 3) {
				$select .= "AND c.validDate < now() AND c.validDate >= DATE_SUB(NOW(),INTERVAL 12 MONTH) ";
			} elseif ($type == 2) {
				$select .= "AND c.validFrom > now() ";
			} else {
				$select .= "AND c.validFrom < now() AND c.validDate > now() ";
			}
				
		} else {
		
			$select  = "SELECT * ";
			$select .= "FROM discountCodes ";
			if ($type == 3) {
				$select .= "WHERE validDate < now() AND validDate >= DATE_SUB(NOW(),INTERVAL 1 YEAR) ";
			} elseif ($type == 2) {
				$select .= "WHERE validFrom > now() ";
			} else {
				$select .= "WHERE validFrom < now() AND validDate > now() ";
			}
				
		}

		$select .= "AND (active_se = -1 OR active_fi = -1 OR active_no = -1) ";
		if ($personal_discount == "yes") {
			$select .= "AND personal_discount = -1 ";
		} else {
			$select .= "AND personal_discount = 0 ";
		}
		if ($type == 3) {
			$select .= "ORDER BY validDate DESC, discountCode ASC ";
		} elseif ($type == 2) {
			$select .= "ORDER BY validDate ASC, discountCode ASC ";
		} else {
			$select .= "ORDER BY validDate ASC, discountCode ASC ";
		}
		
		if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nuW') {
			echo $select;
		}

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
				$priceDateTo = preg_replace('/:[0-9][0-9][0-9]/','', $validDate);
				$priceDateFrom = preg_replace('/:[0-9][0-9][0-9]/','', $validFrom);
				if ($type == 3) {
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
						$aterstar = $this->getTimeLeftNew($priceDateTo,true);
					} else {
						$aterstar = $this->getTimeLeftNew($priceDateTo,true);
					}
				} elseif ($type == 2) {
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
						$aterstar = $this->getTimeLeftNew($priceDateFrom);
					} else {
						$aterstar = $this->getTimeLeftNew($priceDateFrom);
					}
				} else {
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
						$aterstar = $this->getTimeLeftNew($priceDateTo);
					} else {
						$aterstar = $this->getTimeLeftNew($priceDateTo);
					}
				}
				$aterstardagar = $this->getDaysLeft($priceDateTo);
				if ((time()-strtotime($dateCreated)) < 86400) {
					$isNew = "<span class=\"sup\">New</span>";
				} else {
					$isNew = "";
				}
				if (strlen($notes) > 80) {
					$notes = substr ($notes, 0, 80) . "....";
				}

				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}

				echo "<tr>";
				if ($site == 3) {
					echo "<td class=\"$rowcolor\">Outdoor</td>\n";
				} elseif ($site == 2) {
					echo "<td class=\"$rowcolor\">Mobil</td>\n";
				} elseif ($site == 1) {
					echo "<td class=\"$rowcolor\">Foto</td>\n";
				} else {
					echo "<td class=\"$rowcolor\">Alla</td>\n";
				}
				echo "<td>";
				if ($active_se == -1) {
					echo "<img border=\"0\" src=\"sv_mini.jpg\">&nbsp;";
				}
				if ($active_fi == -1) {
					echo "<img border=\"0\" src=\"fi_mini.jpg\">&nbsp;";
				}
				if ($active_no == -1) {
					echo "<img border=\"0\" src=\"no_mini.jpg\">&nbsp;";
				}
				if ($personal_discount == -1) {
					echo "<img border=\"0\" src=\"personal_mini.png\">&nbsp;";
				}
				echo "</td>";
				
				// echo "<td class=\"$rowcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?show=" . $cnt . "&discountCode=" . $discountCode . "\"><b>" . strtoupper($discountCode) . "</b></a>$isNew</td>";
				echo "<td class=\"$rowcolor\"><a href=\"" . $_SERVER['PHP_SELF'] . "?show=" . $cnt . "\"><b>" . strtoupper($discountCode) . "</b></a>$isNew</td>";

				if ($this->getCampaignArticleInclID($cnt) > 0) {
					echo "<td><img border=\"0\" src=\"status_green.jpg\" title=\"På köpet kampanj\"></td>";
				} else {
					echo "<td>&nbsp;</td>";
				}
			
				// echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d H:i:s", strtotime($priceDateFrom)) . "</td>";
				echo "<td class=\"$rowcolor\" align=\"center\">" . date("Y-m-d H:i:s", strtotime($priceDateTo)) . "</td>";
				
				
				if ($aterstardagar < 7 && $type != 3) {
					echo "<td class=\"$rowcolor\" align=\"center\"><font color=\"red\"><b>" . $aterstar . "</b></td>";
				} else {
					echo "<td class=\"$rowcolor\" align=\"center\">" . $aterstar . "</td>";
				}
				echo "<td class=\"$rowcolor\" align=\"center\">" . $by_user . "</td>";
				echo "<td class=\"$rowcolor\" align=\"left\">" . $descrptn . "</td>";
				echo "<td class=\"$rowcolor\" align=\"left\">" . $notes . "</td>";
				if ($notify == -1) {
					if ($isNotify == -1) {
						echo "<td><img title=\"Redan aviserad\" border=\"0\" src=\"/order/admin/mailto_g.png\"></td>";
					} else {
						echo "<td><img title=\"Skall aviseras\" border=\"0\" src=\"/order/admin/mailto_y.png\"></td>";
					}
				} else {
					echo "<td>&nbsp;</td>";
				}
				echo "</tr>";

				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				$countrow++;
			
				endwhile;
				
			} else {
			
				echo "<tr>";
				echo "<td width=\"50\">&nbsp;</td>\n";
				echo "<td colspan=\"11\"><font color=\"#33CC33\"><b>Inga planerade kampanjer finns registrerade</b></td>";
				echo "</tr>";
			
			}
			
		if ($countrow > 0) {
			echo "<tr>";
			echo "<td width=\"50\">&nbsp;</td>\n";
			echo "<td colspan=\"11\"><b>Totalt: $countrow st</b></td>";
			echo "</tr>";
		}
		echo "</table>\n";
		echo "</div>\n";

	}

	function getCampaignDetailAdmin($ID=null,$discountCode=null) {

		$give_discount = false;
		$vat = 1.25;
		$vat_fi = 1.24;
		$vat_no = 1.25;
		
		$select  = "SELECT * ";	
		$select  .= "FROM discountCodes ";
		if ($discountCode != null) {
			$select  .= "WHERE discountCode = '$discountCode' ";
		} else {
			$select  .= "WHERE cnt = '$ID' ";
		}
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);
		

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);
			
			if (!preg_match("/campaign_detail\.php/i", $_SERVER['PHP_SELF'])) {
				echo "<h2>Detaljer för kampanjen <span style=\"color:blue\">$discountCode</span></h2>\n";
				$this->getLastCampaigns();
			}
			echo "<div class=\"framebox\">\n";
			echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";

			// $aterstar = $this->getDaysLeft($validDate);
			$aterstar = $this->getTimeLeftNew($validDate);
			
			if (preg_match("/campaign_detail\.php/i", $_SERVER['PHP_SELF'])) {
				echo "<tr>";
				echo "<td>Rabattkod</td>";
				// echo "<td class=\"boldit\"><a target=\"_blank\" href=\"/order/admin/campaign.php?show=$cnt\">" . $discountCode . "</a></td>";
				echo "<td class=\"boldit\"><a target=\"_blank\" href=\"campaign.php?show=$cnt\">" . $discountCode . "</a></td>";
				echo "<td class=\"boldit\">&nbsp;</td>";
				echo "</tr>";
			} else {
				echo "<tr>";
				echo "<td>Rabattkod</td>";
				echo "<td class=\"boldit\">" . $discountCode . "</td>";
				echo "<td class=\"boldit\">&nbsp;</td>";
				echo "</tr>";
			}
			echo "<tr>";
			echo "<td>Giltig från:</td>";
			echo "<td class=\"boldit\">" . date("Y-m-d H:i:s", strtotime($validFrom)) . "</td>";
			echo "<td class=\"boldit\">&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>Giltig till:</td>";
			echo "<td class=\"boldit\">" . date("Y-m-d H:i:s", strtotime($validDate)) . "</td>";
			// echo "<td>&nbsp;&nbsp;(" . $aterstar . "&nbsp;dagar kvar)</td>";
			echo "<td>&nbsp;&nbsp;(" . $aterstar . " kvar)</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>Ansvarig:</td>";
			echo "<td class=\"boldit\">" . $by_user . "</td>";
			echo "<td class=\"boldit\">&nbsp;</td>";
			echo "</tr>";
			if ($notes != "") {
				echo "<tr>";
				echo "<td>Kommentar:</td>";
				echo "<td colspan=\"2\" class=\"boldit\">" . $notes . "</td>";
				echo "</tr>";
			}
			echo "<tr>";
			echo "<td>Aktiv i:</td>";
			echo "<td class=\"boldit\">";
			if ($active_se == -1) {
				// echo "<img border=\"0\" src=\"/order/admin/sv_mini.jpg\">&nbsp;";
				echo "<img border=\"0\" src=\"sv_mini.jpg\">&nbsp;";
			}
			if ($active_fi == -1) {
				// echo "<img border=\"0\" src=\"/order/admin/fi_mini.jpg\">&nbsp;";
				echo "<img border=\"0\" src=\"fi_mini.jpg\">&nbsp;";
			}
			if ($active_no == -1) {
				// echo "<img border=\"0\" src=\"/order/admin/no_mini.jpg\">&nbsp;";
				echo "<img border=\"0\" src=\"no_mini.jpg\">&nbsp;";
			}
			if ($personal_discount == -1) {
				// echo "<img border=\"0\" src=\"/order/admin/personal_mini.png\">&nbsp;";
				echo "<img border=\"0\" src=\"personal_mini.png\">&nbsp;";
			}
			echo "</td>";
			echo "<td class=\"boldit\">&nbsp;</td>";
			echo "</tr>";
			echo "</tr>";
			echo "<td colspan=\"3\"><hr noshade color=\"#000000\" align=\"left\" size=\"1\"></td>";
			echo "</tr>";
			if ($artnr != "" || $kategori_id != "" || $tillverkar_id != "") {
				echo "</tr>";
				echo "<td class=\"boldit\" colspan=\"3\">Om följande kriterier uppfylls</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>Artnr:</td>";
				if ($artnr != "") {
					echo "<td class=\"boldit\">" . $artnr . "</td>";
					echo "<td><a target=\"_blank\" href=\"/info.php?article=$artnr\">" . $this->getArticleName($artnr) . "</a></td>";
				} else {
					echo "<td class=\"boldit\">" . $artnr . "</td>";
					echo "<td class=\"boldit\">&nbsp;</td>";
				}
				echo "</tr>";
				echo "<tr>";
				echo "<td>Kategori:</td>";
				if ($kategori_id != "") {
					echo "<td class=\"boldit\">" . $this->getKategoriName($kategori_id) . "</td>";
				} else {
					echo "<td class=\"boldit\">&nbsp;</td>";
				}
				echo "<td class=\"boldit\">&nbsp;</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>Tillverkare:</td>";
				if ($tillverkar_id != "") {
					echo "<td class=\"boldit\">" . $this->getManufacturerName($tillverkar_id) . "</td>";
				} else {
					echo "<td class=\"boldit\">&nbsp;</td>";
				}
				echo "<td class=\"boldit\">&nbsp;</td>";
				echo "</tr>";
				if ($discountPercent != "" || $discountPercent_fi != "" || $discountPercent_no != "" || $discountAmount != "" || $discountAmount_fi != "" || $discountAmount_no != "" || $discountOutprice != "" || $discountOutprice_fi != "" || $discountOutprice_no != "") {
					$give_discount = true;
					echo "<tr>";
					echo "<td class=\"boldit\" colspan=\"3\">Ges följande rabatter</td>";
					echo "</tr>";
					if ($discountPercent != "") {
						echo "<tr>";
						// echo "<td><img border=\"0\" src=\"/order/admin/sv_mini.jpg\"></td>";
						echo "<td><img border=\"0\" src=\"sv_mini.jpg\"></td>";
						echo "<td class=\"boldit\">" . round($discountPercent*100,0) . " %</td>";
						echo "<td class=\"boldit\">&nbsp;</td>";
						echo "</tr>";
					}
					if ($discountPercent_fi != "") {
						echo "<tr>";
						// echo "<td><img border=\"0\" src=\"/order/admin/fi_mini.jpg\"></td>";
						echo "<td><img border=\"0\" src=\"fi_mini.jpg\"></td>";
						echo "<td class=\"boldit\">" . round($discountPercent_fi*100,0) . " %</td>";
						echo "<td class=\"boldit\">&nbsp;</td>";
						echo "</tr>";
					}
					if ($discountPercent_no != "") {
						echo "<tr>";
						// echo "<td><img border=\"0\" src=\"/order/admin/no_mini.jpg\"></td>";
						echo "<td><img border=\"0\" src=\"no_mini.jpg\"></td>";
						echo "<td class=\"boldit\">" . round($discountPercent_no*100,0) . " %</td>";
						echo "<td class=\"boldit\">&nbsp;</td>";
						echo "</tr>";
					}
					if ($discountAmount != "") {
						echo "<tr>";
						// echo "<td><img border=\"0\" src=\"/order/admin/sv_mini.jpg\"></td>";
						echo "<td><img border=\"0\" src=\"sv_mini.jpg\"></td>";
						echo "<td colspan=\"2\" class=\"boldit\">" . round($discountAmount,0) . " SEK + moms (" . round(($discountAmount*$vat),0) . " SEK inkl moms)</td>";
						// echo "<td class=\"boldit\">&nbsp;</td>";
						echo "</tr>";
					}
					if ($discountAmount_fi != "") {
						echo "<tr>";
						// echo "<td><img border=\"0\" src=\"/order/admin/fi_mini.jpg\"></td>";
						echo "<td><img border=\"0\" src=\"fi_mini.jpg\"></td>";
						echo "<td colspan=\"2\" class=\"boldit\">" . round($discountAmount_fi,0) . " EUR + moms (" . round(($discountAmount_fi*$vat_fi),0) . " EUR inkl moms)</td>";
						// echo "<td class=\"boldit\">&nbsp;</td>";
						echo "</tr>";
					}
					if ($discountAmount_no != "") {
						echo "<tr>";
						// echo "<td><img border=\"0\" src=\"/order/admin/no_mini.jpg\"></td>";
						echo "<td><img border=\"0\" src=\"no_mini.jpg\"></td>";
						echo "<td colspan=\"2\" class=\"boldit\">" . round($discountAmount_no,0) . " NOK + moms (" . round(($discountAmount_no*$vat_no),0) . " NOK inkl moms)</td>";
						// echo "<td class=\"boldit\">&nbsp;</td>";
						echo "</tr>";
					}
					if ($discountOutprice != "") {
						echo "<tr>";
						echo "<td class=\"boldit\" colspan=\"3\">Nytt utpris</td>";
						echo "</tr>";
						echo "<tr>";
						// echo "<td><img border=\"0\" src=\"/order/admin/sv_mini.jpg\"></td>";
						echo "<td><img border=\"0\" src=\"sv_mini.jpg\"></td>";
						echo "<td colspan=\"2\" class=\"boldit\">" . round($discountOutprice,0) . " SEK + moms (" . round(($discountOutprice*$vat),0) . " SEK inkl moms)</td>";
						// echo "<td class=\"boldit\">&nbsp;</td>";
						echo "</tr>";
					}
					if ($discountOutprice_fi != "") {
						echo "<tr>";
						echo "<td class=\"boldit\" colspan=\"3\">Nytt utpris</td>";
						echo "</tr>";
						echo "<tr>";
						// echo "<td><img border=\"0\" src=\"/order/admin/fi_mini.jpg\"></td>";
						echo "<td><img border=\"0\" src=\"fi_mini.jpg\"></td>";
						echo "<td colspan=\"2\" class=\"boldit\">" . round($discountOutprice_fi,0) . " EUR + moms (" . round(($discountOutprice_fi*$vat_fi),0) . " EUR inkl moms)</td>";
						// echo "<td class=\"boldit\">&nbsp;</td>";
						echo "</tr>";
					}
					if ($discountOutprice_no != "") {
						echo "<tr>";
						echo "<td class=\"boldit\" colspan=\"3\">Nytt utpris</td>";
						echo "</tr>";
						echo "<tr>";
						// echo "<td><img border=\"0\" src=\"/order/admin/no_mini.jpg\"></td>";
						echo "<td><img border=\"0\" src=\"no_mini.jpg\"></td>";
						echo "<td colspan=\"2\" class=\"boldit\">" . round($discountOutprice_no,0) . " NOK + moms (" . round(($discountOutprice_no*$vat_no),0) . " NOK inkl moms)</td>";
						// echo "<td class=\"boldit\">&nbsp;</td>";
						echo "</tr>";
					}
				} else {
					echo "</tr>";
					echo "<td class=\"boldit_italic\" colspan=\"3\">Inga rabatter verkar finnas på denna kampanj</td>";
					echo "</tr>";
				}
				echo "</tr>";
				echo "<td colspan=\"3\"><hr noshade color=\"#000000\" align=\"left\" size=\"1\"></td>";
				echo "</tr>";
			} else {
				echo "</tr>";
				echo "<td class=\"boldit_italic\" colspan=\"3\">Följande artiklar omfattas</td>";
				echo "</tr>";
				$this->getCampaignArticles($cnt);
			}

		}

		echo "</table>";
		if (!preg_match("/campaign_detail\.php/i", $_SERVER['PHP_SELF'])) {
			if (strtotime($validDate) > time()) {
				// echo "<div class=\"left5\"><p><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $cnt . "&discountCode=" . $discountCode . "\">- Uppdatera denna kampanj</a></p></div>\n";
				echo "<div class=\"left5\"><p>- <a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $cnt . "\">Uppdatera denna kampanj</a></p></div>\n";
			}
			echo "<div class=\"left5\"><p>- <a href=\"" . $_SERVER['PHP_SELF'] . "?copy=" . $cnt . "\">Kopiera denna kampanj</a></p></div>\n";
		}
		
		if ($artnr != "" || $kategori_id != "" || $tillverkar_id != "") {
			// echo "<div class=\"left5\"><p>- <a href=\"" . $_SERVER['PHP_SELF'] . "?copy=" . $cnt . "\">Kopiera denna kampanj</a></p></div>\n";
		}
		echo "</div>\n";

		echo "<div class=\"clear\"></div>\n";
		echo "<div class=\"top10\"></div>\n";
		if ($descrptn != "") {
			echo "<div class=\"left5\"><b><font color=\"#85000D\">Kampanj!</font> $descrptn</b></div>\n";
		} elseif ($descrptn == "" && $title_se != "") {
			$descrptn = $title_se;
		}
		if ($campaign_link != "") {
			echo "<div><img border=\"0\" src=\"http://www.cyberphoto.se/$campaign_link\"></div>";
		}
		if ($link != "") {
			echo "<div class=\"left5\">Länkas till: http://www.cyberphoto.se$link</div>";
		} elseif ($this->checkPaKopet($ID)) {
			// echo "<div class=\"top10\"></div>\n";
			// echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.se/pri_campaign.php?ID=$ID\">http://www.cyberphoto.se/pri_campaign.php?ID=$ID</a></div>";
			if ($site == 3)	{
				echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.se/outdoor/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn)) . "\">http://www.cyberphoto.se/outdoor/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn)) . "</a></div>";
			} elseif ($site == 2)	{
				echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.se/mobiltelefoni/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn)) . "\">http://www.cyberphoto.se/mobiltelefoni/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn)) . "</a></div>";
			} else {
				echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.se/foto-video/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn)) . "\">http://www.cyberphoto.se/foto-video/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn)) . "</a></div>";
			}
		}
		if ($link_ext != "") {
			echo "<div class=\"left5\">Länkas externt till: $link_ext</div>";
		}
		
		if ($active_fi == -1) {
			echo "<hr noshade color=\"#000000\" align=\"left\" size=\"1\">\n";
			echo "<div class=\"clear\"></div>\n";
			echo "<div class=\"top10\"></div>\n";
			if ($descrptn_fi != "") {
				echo "<div class=\"left5\"><b><font color=\"#85000D\">Kampanja!</font> $descrptn_fi</b></div>\n";
			} elseif ($descrptn_fi == "" && $title_fi != "") {
				$descrptn_fi = $title_fi;
			}
			if ($campaign_link_fi != "") {
				echo "<div><img border=\"0\" src=\"http://www.cyberphoto.fi/$campaign_link_fi\"></div>";
			}
			if ($link_fi != "") {
				echo "<div class=\"left5\">Länkas till: http://www.cyberphoto.fi$link_fi</div>";
			} elseif ($this->checkPaKopet($ID)) {
				// echo "<div class=\"top10\"></div>\n";
				// echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.fi/pri_campaign_fi.php?ID=$ID\">http://www.cyberphoto.fi/pri_campaign_fi.php?ID=$ID</a></div>";
				if ($site == 3)	{
					echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.fi/outdoor/kampanjat/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi)) . "\">http://www.cyberphoto.fi/outdoor/kampanjat/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi)) . "</a></div>";
				} elseif ($site == 2)	{
					echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.fi/mobiili/kampanjat/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi)) . "\">http://www.cyberphoto.fi/mobiili/kampanjat/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi)) . "</a></div>";
				} else {
					echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.fi/foto-video/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi)) . "\">http://www.cyberphoto.fi/foto-video/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi)) . "</a></div>";
				}
			}
			if ($link_ext_fi != "") {
				echo "<div class=\"left5\">Länkas externt till: $link_ext_fi</div>";
			}
		
			echo "<hr noshade color=\"#000000\" align=\"left\" size=\"1\">\n";
			echo "<div class=\"clear\"></div>\n";
			echo "<div class=\"top10\"></div>\n";
			if ($descrptn_fi_sv != "") {
				echo "<div class=\"left5\"><b><font color=\"#85000D\">Kampanj!</font> $descrptn_fi_sv</b></div>\n";
			} elseif ($descrptn_fi_sv == "" && $title_se != "") {
				$descrptn_fi_sv = $title_se;
			}
			if ($campaign_link_fi_sv != "") {
				echo "<div><img border=\"0\" src=\"http://www.cyberphoto.fi/$campaign_link_fi_sv\"></div>";
			}
			if ($link_fi_sv != "") {
				echo "<div class=\"left5\">Länkas till: http://www.cyberphoto.fi$link_fi_sv</div>";
			} elseif ($this->checkPaKopet($ID)) {
				// echo "<div class=\"top10\"></div>\n";
				// echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.fi/pri_campaign_fi_se.php?ID=$ID\">http://www.cyberphoto.fi/pri_campaign_fi_se.php?ID=$ID</a></div>";
				if ($site == 3)	{
					echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.fi/outdoor/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi_sv)) . "\">http://www.cyberphoto.fi/outdoor/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi_sv)) . "</a></div>";
				} elseif ($site == 2)	{
					echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.fi/mobiltelefoni/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi_sv)) . "\">http://www.cyberphoto.fi/mobiltelefoni/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi_sv)) . "</a></div>";
				} else {
					echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.fi/foto-video/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi_sv)) . "\">http://www.cyberphoto.fi/foto-video/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_fi_sv)) . "</a></div>";
				}
			}
			if ($link_ext_fi_sv != "") {
				echo "<div class=\"left5\">Länkas externt till: $link_ext_fi_sv</div>";
			}
		}
		
		if ($active_no == -1) {
			echo "<hr noshade color=\"#000000\" align=\"left\" size=\"1\">\n";
			echo "<div class=\"clear\"></div>\n";
			echo "<div class=\"top10\"></div>\n";
			if ($descrptn_no != "") {
				echo "<div class=\"left5\"><b><font color=\"#85000D\">Kampanj!</font> $descrptn_no</b></div>\n";
			} elseif ($descrptn_no == "" && $title_no != "") {
				$descrptn_no = $title_no;
			}
			if ($campaign_link_no != "") {
				echo "<div><img border=\"0\" src=\"http://www.cyberphoto.se/$campaign_link_no\"></div>";
			}
			if ($link_no != "") {
				echo "<div class=\"left5\">Länkas till: http://www.cyberphoto.no$link_no</div>";
			} elseif ($this->checkPaKopet($ID)) {
				// echo "<div class=\"top10\"></div>\n";
				// echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.no/pri_campaign.php?ID=$ID\">http://www.cyberphoto.no/pri_campaign.php?ID=$ID</a></div>";
				if ($site == 3)	{
					echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.no/outdoor/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_no)) . "\">http://www.cyberphoto.no/outdoor/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_no)) . "</a></div>";
				} elseif ($site == 2)	{
					echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.no/mobiltelefoni/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_no)) . "\">http://www.cyberphoto.no/mobiltelefoni/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_no)) . "</a></div>";
				} else {
					echo "<div class=\"left5\">Automatgenererad prislista: <a target=\"_blank\" href=\"http://www.cyberphoto.no/foto-video/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_no)) . "\">http://www.cyberphoto.no/foto-video/kampanjer/$ID/" . strtolower(Tools::replace_special_char($descrptn_no)) . "</a></div>";
				}
			}
			if ($link_ext_no != "") {
				echo "<div class=\"left5\">Länkas externt till: $link_ext_no</div>";
			}
		}
		
		/*
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" || $discountPercent != "" || $discountPercent_fi != "" || $discountPercent_no != "" || $discountAmount != "" || $discountAmount_fi != "" || $discountAmount_no != "" || $discountOutprice != "" || $discountOutprice_fi != "" || $discountOutprice_no != "") {
			echo "<h2>Utnyttjade rabattkoder</h2>\n";
			$this->displayOrdersWithPromotionCode($discountCode,$give_discount);
		}
		*/
		$this->displayOrdersWithPromotionCode($discountCode,$give_discount);

	}
	
	function getLastCampaigns($only_list = false) {
		global $personal_discount;
	
		$upcoming = 0;
		
		$select  = "SELECT * ";
		$select .= "FROM cyberphoto.discountCodes ";
		$select .= "WHERE  ";
		if ($personal_discount == "yes") {
			$select .= "personal_discount = -1 ";
		} else {
			$select .= "personal_discount = 0 ";
		}
		$select .= "AND (dateCreated > DATE_SUB(NOW(), INTERVAL 48 HOUR) AND validFrom < now() AND validDate > now()) OR validFrom > now() ";
		$select .= "ORDER BY validFrom ASC ";
	
		// echo $select;
	
		$res = mysqli_query($this->conn_my, $select);
		// $res = mysqli_query($conn_my, $select);
	
		if (mysqli_num_rows($res) > 0) {
				
			if ($only_list) {
				echo "<div class=\"framebox4\">\n";
			} else {
				echo "<div class=\"framebox4\">\n";
			}
			echo "<h2>Kampanjer upplagda under de senaste 48 timmarna</h2>\n";
			echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"100%\">\n";
			echo "<tr>\n";
			echo "<th width=\"75\">Aktiv</th>\n";
			echo "<th>Kampanjkod&nbsp;</th>";
			echo "<th class=\"align_center\">Ansvarig&nbsp;</th>";
			echo "<th>Gäller från&nbsp;</th>";
			echo "<th>Gäller till&nbsp;</th>";
			echo "</tr>\n";
	
			while ($row = mysqli_fetch_object($res)) {
					
				if ($upcoming == 0 && strtotime($row->validFrom) > time()) {
					echo "</table>\n";
					echo "<h2>Kommande kampanjer</h2>\n";
					echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"100%\">\n";
					echo "<tr>\n";
					echo "<th width=\"75\">Aktiv</th>\n";
					echo "<th>Kampanjkod&nbsp;</th>";
					echo "<th class=\"align_center\">Ansvarig&nbsp;</th>";
					echo "<th>Gäller från&nbsp;</th>";
					echo "<th>Gäller till&nbsp;</th>";
					echo "</tr>\n";
					/*
					echo "<tr>";
					echo "<td colspan=\"4\" align=\"left\">&nbsp;</td>";
					echo "</tr>";
					*/
					$upcoming++;
				}
				echo "<tr>";
				echo "<td class=\"align_left\">";
				if ($row->active_se == -1) {
					echo "<img border=\"0\" src=\"sv_mini.jpg\">&nbsp;";
				}
				if ($row->active_fi == -1) {
					echo "<img border=\"0\" src=\"fi_mini.jpg\">&nbsp;";
				}
				if ($row->active_no == -1) {
					echo "<img border=\"0\" src=\"no_mini.jpg\">&nbsp;";
				}
				if ($row->personal_discount == -1) {
					echo "<img border=\"0\" src=\"personal_mini.png\">&nbsp;";
				}
				echo "</td>";
				// echo "<td class=\"align_left\"><a href=\"/order/admin/campaign.php?show=$row->cnt\">" . $row->discountCode . "</a></td>";
				echo "<td class=\"align_left\"><a href=\"campaign.php?show=$row->cnt\">" . $row->discountCode . "</a></td>";
				echo "<td class=\"align_center\">" . $row->by_user . "</td>";
				echo "<td class=\"align_left\">" . date("Y-m-d H:i", strtotime($row->validFrom)) . "</td>";
				echo "<td class=\"align_left\">" . date("Y-m-d H:i", strtotime($row->validDate)) . "</td>";
				echo "</tr>";
						
			}
	
			echo "</table>\n";
			if ($only_list) {
				echo "</div>\n";
				echo "<div class=\"clear top20\"></div>\n";
			} else {
				echo "</div>\n";
			}
		
		}
	
	}
	
	function displayOrdersWithPromotionCode($promotioncode,$give_discount) {

		$countrow = 0;
		$ordervarde = 0;
		$margin = 0;
		
			$select = "SELECT o.created, o.documentno, bp.name, o.marginamt, o.totallines, curr.iso_code ";
			$select .= "FROM c_order o ";
			$select .= "JOIN c_bpartner bp ON o.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_currency curr ON curr.c_currency_id = o.c_currency_id ";
			// $select .= "WHERE o.c_doctype_id = 1000030 AND o.docstatus IN ('CO','IP') AND o.PromotionCode = '$promotioncode' ";
			$select .= "WHERE o.c_doctype_id = 1000030 AND o.docstatus IN ('CO','IP') AND o.PromotionCode LIKE '$promotioncode%' ";
			$select .= "ORDER BY o.created ASC ";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;

				if ($res && pg_num_rows($res) > 0) {
					
					echo "<h2>Utnyttjade rabattkoder</h2>\n";

					if (!$give_discount) {
						echo "<h6>Detta verkar inte rimligt. Inga rabatter finns angivna i kampanjen!</h6>\n";
					}
					
					echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
					echo "\t<tr>\n";
					// echo "\t\t<td width=\"25\">&nbsp;</td>\n";
					echo "\t\t<td width=\"110\"><b>Datum</b></td>\n";
					echo "\t\t<td width=\"60\"><b>Order nr</b></td>\n";
					echo "\t\t<td>&nbsp;</td>\n";
					echo "\t\t<td width=\"250\"><b>Affärspartner</b></td>\n";
					echo "\t\t<td width=\"90\"><b>Ordersumma</b></td>\n";
					echo "\t\t<td width=\"90\"><b>Marginal</b></td>\n";
					echo "\t</tr>\n";
				
					while ($res && $row = pg_fetch_object($res)) {

						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
				
						echo "\t<tr>";
						// echo "\t\t<td class=\"$backcolor\" align=\"center\">$countrow</td>\n";
						echo "\t\t<td class=\"$backcolor\">" . date("Y-m-d H:i", strtotime($row->created)) . "</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"center\"><a href=\"javascript:winPopupCenter(500, 1000, '/order/order_info.php?order=$row->documentno');\">$row->documentno</a></td>\n";
						if ($row->iso_code == "EUR") {
							echo "\t\t<td><img border=\"0\" src=\"fi_mini.jpg\"></td>\n";
						} elseif ($row->iso_code == "NOK") {
							echo "\t\t<td><img border=\"0\" src=\"no_mini.jpg\"></td>\n";
						} else {
							echo "\t\t<td><img border=\"0\" src=\"sv_mini.jpg\"></td>\n";
						}
						echo "\t\t<td class=\"$backcolor\">$row->name</td>\n";
						echo "\t\t<td align=\"right\" class=\"$backcolor\">" . number_format($row->totallines, 0, ',', ' ') . " " . $row->iso_code . "</td>\n";
						echo "\t\t<td align=\"right\" class=\"$backcolor\">" . number_format($row->marginamt, 0, ',', ' ') . " " . $row->iso_code . "</td>\n";
						echo "\t</tr>\n";
						
						$countrow++;
						$ordervarde = $ordervarde + $row->totallines;
						$margin = $margin + $row->marginamt;
						
						if ($rowcolor == true) {
							$row = true;
							$rowcolor = false;
						} else {
							$row = false;
							$rowcolor = true;
						}

					}
					
					echo "\t<tr>\n";
					echo "\t\t<td colspan=\"4\"><b>Totalt: $countrow st ordrar</b></td>\n";
					echo "\t\t<td align=\"right\"><b>" . number_format($ordervarde, 0, ',', ' ') . " SEK</b></td>\n";
					echo "\t\t<td align=\"right\"><b>" . number_format($margin, 0, ',', ' ') . " SEK</b></td>\n";
					echo "\t</tr>\n";
					
				} else {
				
						echo "\t<tr>\n";
						echo "\t\t<td colspan=\"5\"><i>Inga ordrar lagda med denna rabattkod.</i></td>\n";
						echo "\t</tr>\n";
				
				}
		
				echo "</table>\n";
				
	}
	
	function getSpecDiscountCode($ID) {

		$select  = "SELECT * FROM discountCodes WHERE cnt = '" . $ID . "' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query($this->conn_my, $select);
		$rows = mysqli_fetch_object($res);
		return $rows;

	}
	
	function getAnstalldaNew() {
		global $addcreatedby;

		$select  = "SELECT sign, namn FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";

		$res = mysqli_query($this->conn_my, $select);

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			echo "<option value=\"$sign\"";
				
			if ($addcreatedby == $sign) {
				echo " selected";
			}
				
			echo ">" . $namn . "</option>";
				
			
			// endwhile;

			}

	}

	function getSite() {
		global $addsite;

		$select  = "SELECT site_id, site FROM site ORDER BY site_id ASC ";

		$res = mysqli_query($this->conn_my, $select);

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			echo "<option value=\"$site_id\"";
				
			if ($addsite == $site_id) {
				echo " selected";
			}
				
			echo ">" . $site . "</option>";
				
			
			// endwhile;

			}

	}
	
	function getKategori() {
		global $addkategori;

		$select  = "SELECT kategori_id, kategori FROM Kategori WHERE visas = -1 ORDER BY kategori ASC ";

		$res = mysqli_query($this->conn_my, $select);

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			echo "<option value=\"$kategori_id\"";
				
			if ($addkategori == $kategori_id) {
				echo " selected";
			}
				
			echo ">$kategori ($kategori_id)</option>\n";
				
			
			// endwhile;

			}

	}

	function getArticleName($artnr) {

		$select  = "SELECT t.tillverkare, a.beskrivning ";
		$select .= "FROM Artiklar a ";
		$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "WHERE a.artnr = '$artnr' ";

		$res = mysqli_query($this->conn_my, $select);

			while ($row = mysqli_fetch_array($res)) {
			extract($row);

				if (strlen($beskrivning) > 35) {
					$beskrivning = substr ($beskrivning, 0, 35) . "....";
				}
			
				return $tillverkare . " " . $beskrivning;

			}

	}

	function getKategoriName($kategori_id) {

		$select  = "SELECT kategori FROM Kategori WHERE kategori_id = $kategori_id ";

		$res = mysqli_query($this->conn_my, $select);

			while ($row = mysqli_fetch_array($res)) {
			extract($row);
			
				return $kategori;

			}

	}
	
	function getManufacturer() {
		global $addmanufacturer;

		$select  = "SELECT tillverkar_id, tillverkare FROM Tillverkare ORDER BY tillverkare ASC ";

		$res = mysqli_query($this->conn_my, $select);

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			echo "<option value=\"$tillverkar_id\"";
				
			if ($addmanufacturer == $tillverkar_id) {
				echo " selected";
			}
				
			echo ">$tillverkare</option>\n";
				
			
			// endwhile;

			}

	}

	function getManufacturerName($tillverkar_id) {

		$select  = "SELECT tillverkare FROM Tillverkare WHERE tillverkar_id = $tillverkar_id ";
		
		$res = mysqli_query($this->conn_my, $select);

			while ($row = mysqli_fetch_array($res)) {
			extract($row);
			
				return $tillverkare;
			

			}

	}

	function getCampaignArticle($show) {

		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"100%\">\n";
		echo "<tr>\n";
		echo "<th>Artikel</th>\n";
		echo "<th>Benämning</th>\n";
		echo "<th>Lager</th>\n";
		echo "<th>&nbsp;</th>";
		echo "</tr>\n";

		$select  = "SELECT * ";
		$select .= "FROM discountCodeArticle ";
		$select .= "JOIN Artiklar ON discountCodeArticle.artnr = Artiklar.artnr ";
		$select .= "JOIN Tillverkare ON Tillverkare.tillverkar_id = Artiklar.tillverkar_id ";	
		$select .= "WHERE dscntId = '" . $show . "' ";
		// $select .= "ORDER BY Artiklar.beskrivning ";
		$select  .= "ORDER BY Tillverkare.tillverkare, Artiklar.beskrivning ";	

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
				
				$beskrivning = $this->getbeskrivningArt($artnr);
				$lagersaldo = $this->getlagersaldoArt($artnr);
				
				echo "<tr>";
				echo "<td>$artnr</td>";
				echo "<td><a target=\"_blank\" href=\"/info.php?article=$artnr\">" . $beskrivning . "</a></td>";
				echo "<td align=\"center\">" . $lagersaldo . "</td>";
				echo "<td align=\"center\"><b><a href=\"" . $_SERVER['PHP_SELF'] . "?deletearticle=" . $cnt . "&change=" . $show . "\">Ta&nbsp;bort</a></b></td>";
				echo "</tr>";
			
				}

			} else {
			
				echo "<tr>";
				echo "<td colspan=\"4\"><b><i>Det finns inga produkter kopplade till kampanjen ännu!</i></b></td>";
				echo "</tr>";
			
			}

		echo "</table>\n";

	}
	
	function getCampaignArticleInclID($show) {

		$select  = "SELECT includedArticles_id FROM discountCodeArticle WHERE dscntId = '" . $show . "' ";
		
		// echo $select;

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
				
				return $includedArticles_id;
			
				}

			} else {
			
				return;
			
			}

	}

	function getCampaignArticleIncluded($show) {
		global $change;

		// $conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($conn_my, getenv('DB_NAME') ?: 'cyberphoto');
		
		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"100%\">\n";
		echo "<tr>\n";
		echo "<th>Antal</th>\n";
		echo "<th>Artikel</th>\n";
		echo "<th>Benämning</th>\n";
		echo "<th>Lagersaldo</th>\n";
		echo "<th>Tillåt slut</th>\n";
		echo "<th>&nbsp;</th>";
		echo "</tr>\n";

		$select  = "SELECT * ";
		$select .= "FROM discountCodeIncludedArticles ";
		$select .= "JOIN Artiklar ON discountCodeIncludedArticles.includedArticles = Artiklar.artnr ";
		$select .= "WHERE includedArticles_id = '" . $show . "' ";
		$select .= "ORDER BY Artiklar.beskrivning ";
		
		// echo $select;
		
		$res = mysqli_query($this->conn_my, $select);
		// $res = mysqli_query($conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
				
				$beskrivning = $this->getbeskrivningArt($artnr);
				$lagersaldo = $this->getlagersaldoArt($artnr);
				
				echo "<tr>";
				echo "<td align=\"center\">" . $qty . " st</td>";
				echo "<td>$artnr</td>";
				echo "<td><a target=\"_blank\" href=\"/info.php?article=$artnr\">" . $beskrivning . "</a></td>";
				echo "<td align=\"center\">" . $lagersaldo . "</td>";
				if ($allowNoStock == 0) {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_red.jpg\"></td>";
				} else {
					echo "<td align=\"center\"><img border=\"0\" src=\"status_green.jpg\"></td>";
				}
				echo "<td align=\"center\"><b><a href=\"" . $_SERVER['PHP_SELF'] . "?delArtIncl=" . $cnt . "&change=" . $change . "\">Ta&nbsp;bort</a></b></td>";
				/*
				if ($addedBy != "127.0.0.1") {
					echo "<td align=\"center\"><b><a href=\"" . $_SERVER['PHP_SELF'] . "?delArtIncl=" . $cnt . "&change=" . $change . "\">Ta&nbsp;bort</a></b></td>";
				} else {
					echo "<td align=\"center\"><b><a href=\"" . $_SERVER['PHP_SELF'] . "?manualDelete=YES&manualID=" . $cnt . "&change=" . $change . "\">Ta&nbsp;bort</a></b></td>";
					// echo "<td align=\"center\"><b><a href=\"" . $_SERVER['PHP_SELF'] . "?manualDelete=YES&change=" . $change . "\">Ta&nbsp;bort</a></b></td>";
				}
				*/
				echo "</tr>";
			
				}

			} else {
			
				echo "<tr>";
				echo "<td colspan=\"4\"><b><i>Det finns inga produkter kopplade till kampanjen ännu!</i></b></td>";
				echo "</tr>";
			
			}

		echo "</table>\n";

	}

	function getbeskrivningArt($artArtnr) {
		
		$select  = "SELECT lagersaldo, beskrivning, Tillverkare.tillverkare FROM Artiklar LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id WHERE artnr = '" . $artArtnr . "' ";
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

		extract(mysqli_fetch_array($res));

		// return $tillverkare . " ". $beskrivning . " (" . $lagersaldo . ")";
		return $tillverkare . " ". $beskrivning;
		
		}

	}	

	function getlagersaldoArt($artArtnr) {
		
		$select  = "SELECT lagersaldo FROM Artiklar WHERE artnr = '" . $artArtnr . "' ";
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

		extract(mysqli_fetch_array($res));

		return $lagersaldo;
		
		}

	}	

	function deleteCampaignArticle($deletearticle,$change) {

		$conn_my = Db::getConnection(true);

		$updt = "DELETE FROM discountCodeArticle WHERE cnt = '" . $deletearticle . "'";
		
		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

		header("Location: campaign.php?change=$change");

	}
	
	function deleteIncludedArticle($deletearticle,$change) {

		$conn_my = Db::getConnection(true);

		$updt = "DELETE FROM discountCodeIncludedArticles WHERE cnt = '" . $deletearticle . "'";
		
		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

		header("Location: campaign.php?change=$change");

	}
	
	function addCampaignArticle($addartnr,$change,$oldIncID=null) {
	
		if ($oldIncID != null) {
			$includedArticles_id = $oldIncID;
		} else {
			$includedArticles_id = $change;
		}

		$conn_my = Db::getConnection(true);

		$updt = "INSERT INTO discountCodeArticle (dscntId,artnr,includedArticles_id,addedBy) VALUES ('$change','$addartnr','$includedArticles_id','" . $_SERVER['REMOTE_ADDR'] . "')";

		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

		if (preg_match("/campaign\.php/i", $_SERVER['PHP_SELF'])) {
			header("Location: campaign.php?change=$change");
		}

	}
	
	function addIncludedArticle($addartnrincludedcount,$addartnrincluded,$addno_store,$change,$oldIncID=null) {

		$conn_my = Db::getConnection(true);
		
		if ($oldIncID != null) {
			$includedArticles_id = $oldIncID;
		} else {
			$includedArticles_id = $change;
		}
		

		// $updt = "INSERT INTO discountCodeIncludedArticles (includedArticles_id,includedArticles,allowNoStock,qty) VALUES ('$change','$addartnrincluded','$addno_store','$addartnrincludedcount')";
		$updt = "INSERT INTO discountCodeIncludedArticles (includedArticles_id,includedArticles,allowNoStock,qty,addedBy) VALUES ('$includedArticles_id','$addartnrincluded','$addno_store','$addartnrincludedcount','" . $_SERVER['REMOTE_ADDR'] . "')";

		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

		header("Location: campaign.php?change=$change");

	}

	function addCampaign($addkampanjkod,$addfrom,$addto,$addactive_se,$addactive_fi,
						$addartikelnr=null,$addkategori=null,$addmanufacturer=null,$adddiscountpercent=null,$adddiscountpercent_fi=null,
						$adddiscountamount=null,$adddiscountamount_fi=null,$adddiscountoutprice=null,$adddiscountoutprice_fi=null,
						$addcampaigntext=null,$addcampaigntext_fi=null,$addcampaigntext_fi_sv=null,
						$addlinc=null,$addlinc_fi=null,$addlinc_fi_sv=null,
						$addpicturelinc=null,$addpicturelinc_fi=null,$addpicturelinc_fi_sv=null,
						$addexternallinc=null,$addexternallinc_fi=null,$addexternallinc_fi_sv=null,
						$addshowpicture,$addsite,$addcomment=null,$addcreatedby) {

		$conn_my = Db::getConnection(true);
		
		if ($adddiscountpercent != null) {
			$adddiscountpercent = $adddiscountpercent/100;
		}
		if ($adddiscountpercent_fi != null) {
			$adddiscountpercent_fi = $adddiscountpercent_fi/100;
		}

		$updt  = "INSERT INTO discountCodes ";
		$updt .= "(discountCode,validFrom,validDate,active_se,active_fi, ";
		if ($addartikelnr != null)
			$updt .= "artnr, ";
		if ($addkategori != null)
			$updt .= "kategori_id, ";
		if ($addmanufacturer != null)
			$updt .= "tillverkar_id, ";
		if ($adddiscountpercent != null)
			$updt .= "discountPercent, ";
		if ($adddiscountpercent_fi != null)
			$updt .= "discountPercent_fi, ";
		if ($adddiscountamount != null)
			$updt .= "discountAmount, ";
		if ($adddiscountamount_fi != null)
			$updt .= "discountAmount_fi, ";
		if ($adddiscountoutprice != null)
			$updt .= "discountOutprice, ";
		if ($adddiscountoutprice_fi != null)
			$updt .= "discountOutprice_fi, ";
		$updt .= "descrptn,descrptn_fi,descrptn_fi_sv, ";
		$updt .= "campaign_link,campaign_link_fi,campaign_link_fi_sv, ";
		$updt .= "link,link_fi,link_fi_sv, ";
		$updt .= "link_ext,link_ext_fi,link_ext_fi_sv, ";
		$updt .= "isPicture_show,site,notes,by_user,addedBy) ";
		$updt .= "VALUES ";
		$updt .= "('$addkampanjkod','$addfrom','$addto','$addactive_se','$addactive_fi', ";
		if ($addartikelnr != null)
			$updt .= "'$addartikelnr', ";
		if ($addkategori != null)
			$updt .= "'$addkategori', ";
		if ($addmanufacturer != null)
			$updt .= "'$addmanufacturer', ";
		if ($adddiscountpercent != null)
			$updt .= "'$adddiscountpercent', ";
		if ($adddiscountpercent_fi != null)
			$updt .= "'$adddiscountpercent_fi', ";
		if ($adddiscountamount != null)
			$updt .= "'$adddiscountamount', ";
		if ($adddiscountamount_fi != null)
			$updt .= "'$adddiscountamount_fi', ";
		if ($adddiscountoutprice != null)
			$updt .= "'$adddiscountoutprice', ";
		if ($adddiscountoutprice_fi != null)
			$updt .= "'$adddiscountoutprice_fi', ";
		$updt .= "'$addcampaigntext','$addcampaigntext_fi','$addcampaigntext_fi_sv', ";
		$updt .= "'$addpicturelinc','$addpicturelinc_fi','$addpicturelinc_fi_sv', ";
		$updt .= "'$addlinc','$addlinc_fi','$addlinc_fi_sv', ";
		$updt .= "'$addexternallinc','$addexternallinc_fi','$addexternallinc_fi_sv', ";
		$updt .= "'$addshowpicture','$addsite','$addcomment','$addcreatedby','" . $_SERVER['REMOTE_ADDR'] . "') ";

		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);
		$backto = mysqli_insert_id();

		header("Location: campaign.php?show=$backto");

	}

	function addCampaignNew($addkampanjkod,$addfrom,$addto,$addactive_se,$addactive_fi,$addactive_no,
						$addartikelnr=null,$addkategori=null,$addmanufacturer=null,$adddiscountpercent=null,$adddiscountpercent_fi=null,$adddiscountpercent_no=null,
						$adddiscountamount=null,$adddiscountamount_fi=null,$adddiscountamount_no=null,$adddiscountoutprice=null,$adddiscountoutprice_fi=null,$adddiscountoutprice_no=null,
						$addcampaigntext=null,$addcampaigntext_fi=null,$addcampaigntext_fi_sv=null,$addcampaigntext_no=null,
						$addlinc=null,$addlinc_fi=null,$addlinc_fi_sv=null,$addlinc_no=null,
						$addpicturelinc=null,$addpicturelinc_fi=null,$addpicturelinc_fi_sv=null,$addpicturelinc_no=null,
						$addexternallinc=null,$addexternallinc_fi=null,$addexternallinc_fi_sv=null,$addexternallinc_no=null,
						$addshowpicture,$addsite,$addcomment=null,$addcreatedby,$addnotify,$addnotifyfrom,$addpersonal_discount,
						$addtitle_se=null,$addtitle_fi=null,$addtitle_no=null) {

		$conn_my = Db::getConnection(true);
		
		if ($adddiscountpercent != null) {
			$adddiscountpercent = $adddiscountpercent/100;
		}
		if ($adddiscountpercent_fi != null) {
			$adddiscountpercent_fi = $adddiscountpercent_fi/100;
		}
		if ($adddiscountpercent_no != null) {
			$adddiscountpercent_no = $adddiscountpercent_no/100;
		}

		$updt  = "INSERT INTO discountCodes ";
		$updt .= "(discountCode,validFrom,validDate,active_se,active_fi,active_no, ";
		$updt .= "artnr,kategori_id,tillverkar_id, ";
		$updt .= "discountPercent,discountPercent_fi,discountPercent_no, ";
		$updt .= "discountAmount,discountAmount_fi,discountAmount_no, ";
		$updt .= "discountOutprice,discountOutprice_fi,discountOutprice_no, ";
		$updt .= "descrptn,descrptn_fi,descrptn_fi_sv,descrptn_no, ";
		$updt .= "campaign_link,campaign_link_fi,campaign_link_fi_sv,campaign_link_no, ";
		$updt .= "link,link_fi,link_fi_sv,link_no, ";
		$updt .= "link_ext,link_ext_fi,link_ext_fi_sv,link_ext_no, ";
		$updt .= "isPicture_show,site,notes,by_user,addedBy,notify,notifyTime,personal_discount, ";
		$updt .= "title_se,title_fi,title_no) ";
		$updt .= "VALUES ";
		$updt .= "('$addkampanjkod','$addfrom','$addto','$addactive_se','$addactive_fi','$addactive_no', ";
		if ($addartikelnr != null) {
			$updt .= "'$addartikelnr', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addkategori != null) {
			$updt .= "'$addkategori', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addmanufacturer != null) {
			$updt .= "'$addmanufacturer', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($adddiscountpercent != null) {
			$updt .= "'$adddiscountpercent', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($adddiscountpercent_fi != null) {
			$updt .= "'$adddiscountpercent_fi', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($adddiscountpercent_no != null) {
			$updt .= "'$adddiscountpercent_no', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($adddiscountamount != null) {
			$updt .= "'$adddiscountamount', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($adddiscountamount_fi != null) {
			$updt .= "'$adddiscountamount_fi', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($adddiscountamount_no != null) {
			$updt .= "'$adddiscountamount_no', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($adddiscountoutprice != null) {
			$updt .= "'$adddiscountoutprice', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($adddiscountoutprice_fi != null) {
			$updt .= "'$adddiscountoutprice_fi', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($adddiscountoutprice_no != null) {
			$updt .= "'$adddiscountoutprice_no', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addcampaigntext != null) {
			$updt .= "'$addcampaigntext', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addcampaigntext_fi != null) {
			$updt .= "'$addcampaigntext_fi', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addcampaigntext_fi_sv != null) {
			$updt .= "'$addcampaigntext_fi_sv', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addcampaigntext_no != null) {
			$updt .= "'$addcampaigntext_no', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addpicturelinc != null) {
			$updt .= "'$addpicturelinc', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addpicturelinc_fi != null) {
			$updt .= "'$addpicturelinc_fi', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addpicturelinc_fi_sv != null) {
			$updt .= "'$addpicturelinc_fi_sv', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addpicturelinc_no != null) {
			$updt .= "'$addpicturelinc_no', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addlinc != null) {
			$updt .= "'$addlinc', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addlinc_fi != null) {
			$updt .= "'$addlinc_fi', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addlinc_fi_sv != null) {
			$updt .= "'$addlinc_fi_sv', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addlinc_no != null) {
			$updt .= "'$addlinc_no', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addexternallinc != null) {
			$updt .= "'$addexternallinc', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addexternallinc_fi != null) {
			$updt .= "'$addexternallinc_fi', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addexternallinc_fi_sv != null) {
			$updt .= "'$addexternallinc_fi_sv', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addexternallinc_no != null) {
			$updt .= "'$addexternallinc_no', ";
		} else {
			$updt .= "NULL, ";
		}
		$updt .= "'$addshowpicture','$addsite', ";
		if ($addcomment != null) {
			$updt .= "'$addcomment', ";
		} else {
			$updt .= "NULL, ";
		}
		$updt .= "'$addcreatedby','" . $_SERVER['REMOTE_ADDR'] . "','$addnotify', ";
		if ($addnotifyfrom != null) {
			$updt .= "'$addnotifyfrom', ";
		} else {
			$updt .= "NULL, ";
		}
		$updt .= "'$addpersonal_discount', ";
		if ($addtitle_se != null) {
			$updt .= "'$addtitle_se', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addtitle_fi != null) {
			$updt .= "'$addtitle_fi', ";
		} else {
			$updt .= "NULL, ";
		}
		if ($addtitle_no != null) {
			$updt .= "'$addtitle_no' ";
		} else {
			$updt .= "NULL ";
		}
		$updt .= ") ";

		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);
		$backto = mysqli_insert_id();

		header("Location: campaign.php?show=$backto");

	}
	
	function updateCampaign($addkampanjkod,$addfrom,$addto,$addactive_se,$addactive_fi,$addactive_no,
						$addartikelnr=null,$addkategori=null,$addmanufacturer=null,$adddiscountpercent=null,$adddiscountpercent_fi=null,$adddiscountpercent_no=null,
						$adddiscountamount=null,$adddiscountamount_fi=null,$adddiscountamount_no=null,$adddiscountoutprice=null,$adddiscountoutprice_fi=null,$adddiscountoutprice_no=null,
						$addcampaigntext=null,$addcampaigntext_fi=null,$addcampaigntext_fi_sv=null,$addcampaigntext_no=null,
						$addlinc=null,$addlinc_fi=null,$addlinc_fi_sv=null,$addlinc_no=null,
						$addpicturelinc=null,$addpicturelinc_fi=null,$addpicturelinc_fi_sv=null,$addpicturelinc_no=null,
						$addexternallinc=null,$addexternallinc_fi=null,$addexternallinc_fi_sv=null,$addexternallinc_no=null,
						$addshowpicture,$addsite,$addcomment=null,$addcreatedby,$addnotify,$addnotifyfrom=null,$addpersonal_discount,
						$addtitle_se=null,$addtitle_fi=null,$addtitle_no=null,$addid) {

		$conn_my = Db::getConnection(true);
		
		if ($adddiscountpercent != null) {
			$adddiscountpercent = $adddiscountpercent/100;
		}
		if ($adddiscountpercent_fi != null) {
			$adddiscountpercent_fi = $adddiscountpercent_fi/100;
		}
		if ($adddiscountpercent_no != null) {
			$adddiscountpercent_no = $adddiscountpercent_no/100;
		}

		// $updt = "UPDATE pricelist SET priceHeader = '$addrubrik', priceHeader_fi = '$addrubrik_fi',priceUnderHeader = '$addpayoff',priceUnderHeader_fi = '$addpayoff_fi',priceType = '$addtype',priceComment = '$addcomment',priceCreatedBy = '$addcreatedby',priceDateFrom = '$addfrom',priceDateTo = '$addto',pricePicture = '$addpicture',priceActive = '$addactive' WHERE priceID = '$addid'";

		$updt  = "UPDATE discountCodes SET ";
		// $updt .= "discountCode = '$addkampanjkod', ";
		$updt .= "validFrom = '$addfrom', ";
		$updt .= "validDate = '$addto', ";
		$updt .= "active_se = '$addactive_se', ";
		$updt .= "active_fi = '$addactive_fi', ";
		$updt .= "active_no = '$addactive_no', ";
		if ($addartikelnr != null) {
			$updt .= "artnr = '$addartikelnr', ";
		} else {
			$updt .= "artnr = NULL, ";
		}
		if ($addkategori != null) {
			$updt .= "kategori_id = '$addkategori', ";
		} else {
			$updt .= "kategori_id = NULL, ";
		}
		if ($addmanufacturer != null) {
			$updt .= "tillverkar_id = '$addmanufacturer', ";
		} else {
			$updt .= "tillverkar_id = NULL, ";
		}
		if ($adddiscountpercent != null) {
			$updt .= "discountPercent = '$adddiscountpercent', ";
		} else {
			$updt .= "discountPercent = NULL, ";
		}
		if ($adddiscountpercent_fi != null) {
			$updt .= "discountPercent_fi = '$adddiscountpercent_fi', ";
		} else {
			$updt .= "discountPercent_fi = NULL, ";
		}
		if ($adddiscountpercent_no != null) {
			$updt .= "discountPercent_no = '$adddiscountpercent_no', ";
		} else {
			$updt .= "discountPercent_no = NULL, ";
		}
		if ($adddiscountamount != null) {
			$updt .= "discountAmount = '$adddiscountamount', ";
		} else {
			$updt .= "discountAmount = NULL, ";
		}
		if ($adddiscountamount_fi != null) {
			$updt .= "discountAmount_fi = '$adddiscountamount_fi', ";
		} else {
			$updt .= "discountAmount_fi = NULL, ";
		}
		if ($adddiscountamount_no != null) {
			$updt .= "discountAmount_no = '$adddiscountamount_no', ";
		} else {
			$updt .= "discountAmount_no = NULL, ";
		}
		if ($adddiscountoutprice != null) {
			$updt .= "discountOutprice = '$adddiscountoutprice', ";
		} else {
			$updt .= "discountOutprice = NULL, ";
		}
		if ($adddiscountoutprice_fi != null) {
			$updt .= "discountOutprice_fi = '$adddiscountoutprice_fi', ";
		} else {
			$updt .= "discountOutprice_fi = NULL, ";
		}
		if ($adddiscountoutprice_no != null) {
			$updt .= "discountOutprice_no = '$adddiscountoutprice_no', ";
		} else {
			$updt .= "discountOutprice_no = NULL, ";
		}
		if ($addcampaigntext != null) {
			$updt .= "descrptn = '$addcampaigntext', ";
		} else {
			$updt .= "descrptn = NULL, ";
		}
		if ($addcampaigntext_fi != null) {
			$updt .= "descrptn_fi = '$addcampaigntext_fi', ";
		} else {
			$updt .= "descrptn_fi = NULL, ";
		}
		if ($addcampaigntext_fi_sv != null) {
			$updt .= "descrptn_fi_sv = '$addcampaigntext_fi_sv', ";
		} else {
			$updt .= "descrptn_fi_sv = NULL, ";
		}
		if ($addcampaigntext_no != null) {
			$updt .= "descrptn_no = '$addcampaigntext_no', ";
		} else {
			$updt .= "descrptn_no = NULL, ";
		}
		if ($addpicturelinc != null) {
			$updt .= "campaign_link = '$addpicturelinc', ";
		} else {
			$updt .= "campaign_link = NULL, ";
		}
		if ($addpicturelinc_fi != null) {
			$updt .= "campaign_link_fi = '$addpicturelinc_fi', ";
		} else {
			$updt .= "campaign_link_fi = NULL, ";
		}
		if ($addpicturelinc_fi_sv != null) {
			$updt .= "campaign_link_fi_sv = '$addpicturelinc_fi_sv', ";
		} else {
			$updt .= "campaign_link_fi_sv = NULL, ";
		}
		if ($addpicturelinc_no != null) {
			$updt .= "campaign_link_no = '$addpicturelinc_no', ";
		} else {
			$updt .= "campaign_link_no = NULL, ";
		}
		if ($addlinc != null) {
			$updt .= "link = '$addlinc', ";
		} else {
			$updt .= "link = NULL, ";
		}
		if ($addlinc_fi != null) {
			$updt .= "link_fi = '$addlinc_fi', ";
		} else {
			$updt .= "link_fi = NULL, ";
		}
		if ($addlinc_fi_sv != null) {
			$updt .= "link_fi_sv = '$addlinc_fi_sv', ";
		} else {
			$updt .= "link_fi_sv = NULL, ";
		}
		if ($addlinc_no != null) {
			$updt .= "link_no = '$addlinc_no', ";
		} else {
			$updt .= "link_no = NULL, ";
		}
		if ($addexternallinc != null) {
			$updt .= "link_ext = '$addexternallinc', ";
		} else {
			$updt .= "link_ext = NULL, ";
		}
		if ($addexternallinc_fi != null) {
			$updt .= "link_ext_fi = '$addexternallinc_fi', ";
		} else {
			$updt .= "link_ext_fi = NULL, ";
		}
		if ($addexternallinc_fi_sv != null) {
			$updt .= "link_ext_fi_sv = '$addexternallinc_fi_sv', ";
		} else {
			$updt .= "link_ext_fi_sv = NULL, ";
		}
		if ($addexternallinc_no != null) {
			$updt .= "link_ext_no = '$addexternallinc_no', ";
		} else {
			$updt .= "link_ext_no = NULL, ";
		}
		$updt .= "isPicture_show = '$addshowpicture', ";
		$updt .= "site = '$addsite', ";
		if ($addcomment != null) {
			$updt .= "notes = '$addcomment', ";
		} else {
			$updt .= "notes = NULL, ";
		}
		// $updt .= "by_user = '$addcreatedby', ";
		// $updt .= "updatedBy = '" . $_SERVER['REMOTE_ADDR'] . "', ";
		$updt .= "updatedBy = '$addcreatedby', ";
		$updt .= "updatedTime = '" . date("Y-m-d H:i:s", time()) . "', ";
		$updt .= "notify = '$addnotify', ";
		if ($addnotifyfrom != null) {
			$updt .= "notifyTime = '$addnotifyfrom', ";
		} else {
			$updt .= "notifyTime = NULL, ";
		}
		$updt .= "personal_discount = '$addpersonal_discount', ";
		if ($addtitle_se != null) {
			$updt .= "title_se = '$addtitle_se', ";
		} else {
			$updt .= "title_se = NULL, ";
		}
		if ($addtitle_fi != null) {
			$updt .= "title_fi = '$addtitle_fi', ";
		} else {
			$updt .= "title_fi = NULL, ";
		}
		if ($addtitle_no != null) {
			$updt .= "title_no = '$addtitle_no' ";
		} else {
			$updt .= "title_no = NULL ";
		}
		$updt .= "WHERE cnt = '$addid' ";

		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

		header("Location: campaign.php?show=$addid");

	}
	
	function checkCampaignCode($addkampanjkod) {
		
	$select  = "SELECT discountCode FROM discountCodes WHERE binary discountCode = '" . $addkampanjkod . "' ";
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			return true;
		
		} else {
		
			return false;
		
		}

	}

	function checkPaKopet($change) {
		
		if ($change == "") {
			return false;
		}
		
		$select  = "SELECT artnr FROM discountCodeArticle WHERE dscntId = '" . $change . "' ";
		$res = mysqli_query($this->conn_my, $select);
	
		if (mysqli_num_rows($res) > 0) {

			return true;
		
		} else {
		
			return false;
		
		}

	}

	function checkCampaignCodeArticleInUse($addartnr,$addactive_se,$addactive_fi,$addactive_no) {
		
		$select  = "SELECT * ";
		$select .= "FROM discountCodes ";
		$select .= "WHERE ((validFrom < now() AND validDate > now()) OR validFrom > now()) ";
		$select .= "AND binary artnr = '" . $addartnr . "' ";
		if ($addactive_se == -1 && $addactive_fi == -1 && $addactive_no == -1) {
			$select .= "AND (active_se = -1 OR active_fi = -1 OR active_no = -1) ";
		}
		if ($addactive_se == -1 && $addactive_fi == 0 && $addactive_fi == 0) {
			$select .= "AND active_se = -1 ";
		}
		if ($addactive_se == -1 && $addactive_fi == -1 && $addactive_fi == 0) {
			$select .= "AND (active_se = -1 OR active_fi = -1) ";
		}
			if ($addactive_se == -1 && $addactive_fi == 0 && $addactive_no == -1) {
			$select .= "AND (active_se = -1 OR active_no = -1) ";
		}
		if ($addactive_se == 0 && $addactive_fi == -1 && $addactive_no == -1) {
			$select .= "AND (active_fi = -1 OR active_no = -1) ";
		}
		if ($addactive_se == 0 && $addactive_fi == -1 && $addactive_no == 0) {
			$select .= "AND (active_fi = -1) ";
		}
		if ($addactive_se == 0 && $addactive_fi == 0 && $addactive_no == -1) {
			$select .= "AND (active_no = -1) ";
		}
		$select .= "AND personal_discount = 0 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
			// echo $addactive_no . "<br>";
			echo $select;
		}
	
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			return true;
		
		} else {
		
			return false;
		
		}

	}

	function checkNotifyCampaign() {

		$select  = "SELECT cnt, discountCode, mail ";
		$select .= "FROM discountCodes ";
		$select .= "JOIN Anstallda ON discountCodes.by_user = Anstallda.sign ";
		// $select .= "WHERE validFrom < now() AND validDate > now() AND DATEDIFF(ValidDate, NOW()) < 4 ";
		$select .= "WHERE validFrom < now() AND validDate > now() AND notifyTime < now() ";
		$select .= "AND notify = -1 AND isNotify = 0 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
				$this->doCorrectNotify($cnt);
				$this->sendNotifyMess_v1($cnt,$discountCode,$mail);

				endwhile;
				
			}

	}

	function sendNotifyMess_v1($cnt,$discountCode,$mail) {

		$bevdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";
		$recipient .= " " . $mail;
		// $recipient .= " sjabo@cyberphoto.nu";
		$subj = $bevdatum . " Bevakning av kampanj " . $discountCode;
		$extra = "From: " . $addcreatedby;
		
		$text1 = "Kampanj med bevakning går ut inom kort\n\n";
		$text1 .= "Vänligen vidta lämplig åtgärd!\n\n";
		// $text1 .= "http://www.cyberphoto.se/order/admin/campaign.php?show=" . $cnt . "\n";
		$text1 .= "http://admin.cyberphoto.se/campaign.php?show=" . $cnt . "\n";
		
		mail($recipient, $subj, $text1, $extra);

	}

	function doCorrectNotify($cnt) {

		$aktuelltdatum = date("Y-m-d H:i:s");
		
		$conn_my = Db::getConnection(true);

		$updt = "UPDATE discountCodes SET isNotify = '-1', isNotifyTime = '$aktuelltdatum' WHERE cnt = '$cnt'";

		$res = mysqli_query($conn_my, $updt);

		// header("Location: monitor_articles.php");

	}

	function getValidCampaignPriceList($ID) {

		$select  = "SELECT * ";
		$select .= "FROM discountCodes ";
		$select .= "WHERE ";
		if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			// $select  .= "validFrom < now() AND  validDate > now() ";
			$select  .= "cnt = '" . $ID . "' ";
		} else {
			$select  .= "validFrom < now() AND  validDate > now() ";
			$select  .= "AND cnt = '" . $ID . "' ";
		}
		// echo $select;

		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
			return true;
		} else {
			return false;
		}
	
	}
	
	function getCampaignPriceListArt($ID) {

		$select  = "SELECT artnr FROM discountCodeArticle WHERE dscntId = '" . $ID . "' ";

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)) {
			
					extract($row);

					$maparticles .= "Artiklar.artnr='$artnr' OR ";
				
				}
			
			}
			
			return $maparticles;

	}

	function createRandomPassword($number) {
	
		$chars = "abcdefghjkmnpqrstuvwxyz23456789";
		// srand((double)microtime()*1000000);
		$i = 0;
		// $pass = '' ;
		// $number = 6;
	
		while ($i < $number) {
			/*
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			*/
			$randomChar = $chars[rand(0, strlen($chars)-1)];
			$pass = $pass . $randomChar;
			$i++;
		}
	
		return $pass;
	
	}	

	function closeIfPersonalDiscount($discountcode) {

		$conn_my = Db::getConnection(true);

		$updt  = "UPDATE discountCodes SET validDate = now() ";
		$updt .= "WHERE discountCode = '$discountcode' AND validDate > now() AND validFrom < now() AND personal_discount = -1 ";

		$res = mysqli_query($conn_my, $updt);

	}
	
}

?>

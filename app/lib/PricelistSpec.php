<?php

$pricesite = true;

$web_adempiere = new CWebAdempiere();
$count = 1;

function getFreightPriceWithVat($artnr) {
	global $fi, $no;
	
	$select  = "SELECT utpris, utpris_no, utpris_fi, Moms.momssats, Moms.momssats_fi, Moms.momssats_no ";
	$select .= "FROM Artiklar ";
	$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
    $select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
    $select .= "WHERE artnr = '$artnr' ";
	$res = mysqli_query($select);
	$row = mysqli_fetch_object($res);
	
	// echo $select;
	// exit;
	
	if ($fi) {
		$utpris_moms = $row->utpris_fi + $row->utpris_fi * $row->momssats_fi;
	} elseif ($no) {
		$utpris_moms = $row->utpris_no + $row->utpris_no * $row->momssats_no;
	} else {
		$utpris_moms = $row->utpris + $row->utpris * $row->momssats;
	}	

	
	// return number_format($utpris_moms, 0, ',', ' ') . " " . $valuta;
	return number_format($utpris_moms, 0, ',', ' ');

}
function checkSkapad($artnr) {

	$select  = "SELECT skapad_datum ";
	$select .= "FROM cyberphoto.Artiklar ";
	$select .= "WHERE artnr = '" . $artnr . "' ";

	$res = mysqli_query(Db::getConnection(), $select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_object($res)) {
	
			$newproducttime = (time() - strtotime($row->skapad_datum));
			$newproduct = round($newproducttime / 60 / 60 / 24);
			
			if ($newproduct < 30) {
				return true;
			} else {
				return false;
			}
		
		}
		
	} else {
	
		return false;
	
	}

}

# PHP Pricelist script
# author	Nils Kohlström
# version	2000-11-20


# Read articles using criteria
# if $criteria is not defined, all Articles will be selected therefore:
if ($critiera = "") { 
	$criteria = "WHERE Artiklar.kategori_id=0"; 
}

$arts = new CArticleFunctions();
$articles = $arts->readArticlesWithCriteria($criteria);

// $articles = readArticlesWithCriteria($criteria);

# Reset category
$current_category = "";
$i = 0;
while (list($key, $article) = each($articles)) {
	$i++;
	//print_r($article);
	
	$bestallningsgrans = $article->bestallningsgrans;
	
	/*
	if ($article->isSalesBundle == -1) {
		echo "paketartikel";
	}
	*/
	
	// $bask->check_package_sv($article->artnr);
	
	if ($article->lagersaldo > 0) {
		$lager = "yes";
		if ($fi) {
			$showotherstatus = "varastossa";
		} elseif ($no) {
			$showotherstatus = "finnes på lager";
		} else {
			$showotherstatus = "finns i lager";
		}
	// } elseif ($article->isSalesBundle == -1 && ($article->kategori_id == 395 || $article->kategori_id == 445)) {
	} elseif ($article->isSalesBundle == -1) {
		if ($bask->check_package_sv($article->artnr)) {
			$lager = "yes";
			if ($fi) {
				$showotherstatus = "varastossa";
			} elseif ($no) {
				$showotherstatus = "finnes på lager";
			} else {
				$showotherstatus = "finns i lager";
			}
		} else {
			$lager = "no";
			if ($fi) {
				$showotherstatus = "ei varastossa";
			} elseif ($no) {
				$showotherstatus = "finnes ej på lager";
			} else {
				$showotherstatus = "finns ej i lager";
			}
		}
	} else {
		$lager = "no";
		if ($fi) {
			$showotherstatus = "ei varastossa";
		} elseif ($no) {
			$showotherstatus = "finnes ej på lager";
		} else {
			$showotherstatus = "finns ej i lager";
		}
	}
	
	if ($fi) {
		if ($article->frakt == 1) {
			$fraktpris = (getFreightPriceWithVat('fraktmypack') + getFreightPriceWithVat('frakt+'));
		} elseif ($article->frakt == 2) {
			$fraktpris = (getFreightPriceWithVat('fraktmypack') + getFreightPriceWithVat('frakt+2'));
		} else {
			if ($article->ej_brev == 0 && (round(($article->utpris * $article->momssats)+ $article->utpris) < 50 )) {
				$fraktpris = getFreightPriceWithVat('frakt16');
			} else {
				$fraktpris = getFreightPriceWithVat('fraktmypack');
			}
		}
	} elseif ($no) {
		$fraktpris = getFreightPriceWithVat('fraktmypack_no');
	} else {
		if ($article->frakt == 1) {
			$fraktpris = (getFreightPriceWithVat('fraktpost') + getFreightPriceWithVat('frakt+'));
		} elseif ($article->frakt == 2) {
			$fraktpris = (getFreightPriceWithVat('fraktpost') + getFreightPriceWithVat('frakt+2'));
		} else {
			if ($article->ej_brev == 0 && (round(($article->utpris * $article->momssats)+ $article->utpris) < 500 )) {
				$fraktpris = getFreightPriceWithVat('frakt16');
			} else {
				$fraktpris = getFreightPriceWithVat('fraktpost');
			}
		}
	}
	
	if (preg_match("/prisjakt\.php/i", $_SERVER['PHP_SELF'])) {
		if ($fi) {
			$cpc_linc = "utm_source=prisjakt&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=prisjakt_fi";
		} elseif ($no) {
			$cpc_linc = "utm_source=prisjakt&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=prisjakt_no";
		} else {
			$cpc_linc = "utm_source=prisjakt&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=prisjakt_se";
		}
	}

	else if (preg_match("/prisguide\.php/i", $_SERVER['PHP_SELF'])) {
		if ($fi) {
			$cpc_linc = "utm_source=prisguide&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=prisguide_fi";
		} elseif ($no) {
			$cpc_linc = "utm_source=prisguide&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=prisguide_no";
		} else {
			$cpc_linc = "utm_source=prisguide&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=prisguide_se";
		}
	}

	else if (preg_match("/kelkoo\.php/i", $_SERVER['PHP_SELF'])) {
		if ($fi) {
			$cpc_linc = "utm_source=kelkoo&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=kelkoo_fi";
		} elseif ($no) {
			$cpc_linc = "utm_source=kelkoo&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=kelkoo_no";
		} else {
			$cpc_linc = "utm_source=kelkoo&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=kelkoo_se";
		}
	}

	if (preg_match("/airsoftnu\.php/i", $_SERVER['PHP_SELF'])) {
		if ($fi) {
			$cpc_linc = "utm_source=airsoftnu&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=airsoftnu_fi";
		} elseif ($no) {
			$cpc_linc = "utm_source=airsoftnu&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=airsoftnu_no";
		} else {
			$cpc_linc = "utm_source=airsoftnu&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=airsoftnu_se";
		}
	}
	
	if ($fi) {
		$link = "http://www.cyberphoto.fi/info.php?article=".$article->artnr . $cpc_linc;
	} elseif ($no) {
		$link = "http://www.cyberphoto.no/info.php?article=".$article->artnr . $cpc_linc;
	} else {
		//$link = "http://www.cyberphoto.se/info.php?article=" . $article->artnr . $cpc_linc;
		//$newurl = parse_url( $article->WebshopURL );
		//$produrl = $newurl['scheme'] . "://" . $newurl['host'] . $newurl['path'];
		$produrl = 'https://www.cyberphoto.se' . "" . $article->WebshopURL;
		$link = $produrl . "?" . $cpc_linc;
	}

	if ($article->tillverkare <> ".")
		$tillverkare = $article->tillverkare;
	else
		$tillverkare = "";
	if (true) {
		$bild = $article->WebshopImageURL;
	} else if ($article->bild != "") {
		if (substr($article->bild, 0, 3) != "../") {
			
			if ($fi) {
				$bild = "http://www.cyberphoto.fi/" . $article->bild;
			} elseif ($no) {
				$bild = "http://www.cyberphoto.no/" . $article->bild;
			} else {
				$bild = "https://www2.cyberphoto.se/" . $article->bild;
			}

		} else {
			
			if ($fi) {
				$bild = "http://www.cyberphoto.fi" . eregi_replace("^(\.\.)", "",  $article->bild);
			} elseif ($no) {
				$bild = "http://www.cyberphoto.no" . eregi_replace("^(\.\.)", "",  $article->bild);
			} else {
				$bild = "https://www2.cyberphoto.se" . eregi_replace("^(\.\.)", "",  $article->bild);
			}
			
		}
	} else {
		$bild = "";
	}
	
	if ((
		$article->kategori_id == 336 || $article->kategori_id == 392 || $article->kategori_id == 393 || $article->kategori_id == 394 || 
		$article->kategori_id == 395 || $article->kategori_id == 748 || $article->kategori_id == 426 || $article->kategori_id == 522 || 
		$article->kategori_id == 517 || $article->kategori_id == 1000217 || $article->kategori_id == 1000282 || 
		$article->kategori_id == 1000248 || $article->kategori_id == 1000018
		) && $article->bestallningsgrans > 0 && $lager == "no") { // om huvudkategorier så skickar vi med förväntat leveransdatum
		
		$getIncomingStatus = $web_adempiere->check_lager($article->artnr,1000000,$count);
		
		if ($getIncomingStatus == "tillfälligt slut" && checkSkapad($article->artnr)) {
			$getIncomingStatus = "Förhandsboka";
		}
		
		if ($fi) {
			echo $article->artnr . "|".$article->beskrivning_fi."|".$article->WebshopCategory."|".round(($article->utpris_fi * $article->momssats_fi)+ $article->utpris_fi)."|".round((($article->utpris_fi * $article->momssats_fi)+ $article->utpris_fi)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "|" . $getIncomingStatus . "|".$article->manufacturerproductno. "\n";
		} elseif ($no) {
			echo $article->artnr . "|".$article->beskrivning."|".$article->WebshopCategory."|".round(($article->utpris_no * $article->momssats_no)+ $article->utpris_no)."|".round((($article->utpris_no * $article->momssats_no)+ $article->utpris_no)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "|" . $getIncomingStatus . "|".$article->manufacturerproductno. "\n";
		} else {
			echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round(($article->utpris * $article->momssats)+ $article->utpris)."|".round((($article->utpris * $article->momssats)+ $article->utpris)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "|" . $getIncomingStatus . "|".$article->manufacturerproductno."|".$article->WebshopCategory."|".$article->upc. "\n";
		}
	} else {
		if ($fi) {
			echo $article->artnr . "|".$article->beskrivning_fi."|".$article->WebshopCategory."|".round(($article->utpris_fi * $article->momssats_fi)+ $article->utpris_fi)."|".round((($article->utpris_fi * $article->momssats_fi)+ $article->utpris_fi)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "| |".$article->manufacturerproductno. "\n";
		} elseif ($no) {
			echo $article->artnr . "|".$article->beskrivning."|".$article->WebshopCategory."|".round(($article->utpris_no * $article->momssats_no)+ $article->utpris_no)."|".round((($article->utpris_no * $article->momssats_no)+ $article->utpris_no)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "| |".$article->manufacturerproductno. "\n";
		} else {
			echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round(($article->utpris * $article->momssats)+ $article->utpris)."|".round((($article->utpris * $article->momssats)+ $article->utpris)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "| |".$article->manufacturerproductno."|".$article->WebshopCategory."|".$article->upc. "\n";
		}
	}
	
}

?>
<?php

$pricesite = true;

$web_adempiere = new CWebAdempiere();
$count = 1;

function getFreightPriceWithVat($artnr) {
	global $fi, $no;
	
	$select  = "SELECT utpris, utpris_no, Moms.momssats, Moms.momssats_fi, Moms.momssats_no ";
	$select .= "FROM Artiklar ";
    $select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
    $select .= "WHERE artnr = '$artnr' ";
	$res = mysqli_query($select);
	$row = mysqli_fetch_object($res);
	
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

while (list($key, $article) = each($articles)) {
	if ($article->lagersaldo > 0) {
		$lager = "yes";
		if ($no) {
			$showotherstatus = "finnes på lager";
		} else {
			$showotherstatus = "finns i lager";
		}
	} else {
		$lager = "no";
		if ($no) {
			$showotherstatus = "finnes ej på lager";
		} else {
			$showotherstatus = "finns ej i lager";
		}
	}
	
	if ($no) {
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
	
	$link = "http://www.cyberphoto.se/info.php?article=" . $article->artnr . "&utm_source=airsoftnu&utm_medium=cpc&utm_content=" . $article->artnr . "&utm_campaign=airsoftnu_se";

	if ($article->tillverkare <> ".")
		$tillverkare = $article->tillverkare;
	else
		$tillverkare = "";
	
	if ($article->bild != "") {
		if ($no) {
			$bild = "http://www.cyberphoto.no" . eregi_replace("^(\.\.)", "",  $article->bild);
		} else {
			$bild = "http://www.cyberphoto.se" . eregi_replace("^(\.\.)", "",  $article->bild);
		}
	} else {
		$bild = "";
	}
	
	if (($article->kategori_id == 336 || $article->kategori_id == 392 || $article->kategori_id == 393 || $article->kategori_id == 394 || $article->kategori_id == 395 || $article->kategori_id == 748 || $article->kategori_id == 426) && $article->bestallningsgrans > 0 && $lager == "no") { // om huvudkategorier så skickar vi med förväntat leveransdatum
		if ($no) {
			echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round(($article->utpris_no * $article->momssats_no)+ $article->utpris_no)."|".round((($article->utpris_no * $article->momssats_no)+ $article->utpris_no)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "|".$web_adempiere->check_lager($article->artnr,1000000,$count). "|".$article->manufacturerproductno. "\n";
		} else {
			echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round(($article->utpris * $article->momssats)+ $article->utpris)."|".round((($article->utpris * $article->momssats)+ $article->utpris)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "|".$web_adempiere->check_lager($article->artnr,1000000,$count). "|".$article->manufacturerproductno. "\n";
		}
	} else {
		if ($no) {
			echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round(($article->utpris_no * $article->momssats_no)+ $article->utpris_no)."|".round((($article->utpris_no * $article->momssats_no)+ $article->utpris_no)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "| |".$article->manufacturerproductno. "\n";
		} else {
			echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round(($article->utpris * $article->momssats)+ $article->utpris)."|".round((($article->utpris * $article->momssats)+ $article->utpris)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "| |".$article->manufacturerproductno. "\n";
		}
	}
	
}

?>
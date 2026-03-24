<?php
//mb_internal_encoding("ISO-8859-1");
?><?php
/**
<head>
  <meta charset="UTF-8">
</head>
*/
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
$conn = Db::getConnection();
mysqli_character_set_name("UTF-8");
//echo mysqli_character_set_name(conn); 

//echo mb_internal_encoding();
//exit;
if ($_SERVER['SERVER_ADDR'] == '192.168.100.80')
	$utf8 = false;
else
	$utf8 = true;

$utf8 = true;
//AddDefaultCharset Off
//echo html_entity_decode('har funnits p&aring;');exit;
header('Content-Type: text/xml; charset=utf-8', true); //set document header content type to be XML

$pricesite = true;
$web_adempiere = new CWebAdempiere();
$count = 1;




# PHP Pricelist script
# author	Nils Kohlstr?m
# version	2000-11-20


# Read articles using criteria
# if $criteria is not defined, all Articles will be selected therefore:
if ($critiera = "") { 
	$criteria = "WHERE Artiklar.kategori_id=0 "; 
}

$arts = new CArticleFunctions();
$articles = $arts->readArticlesWithCriteria($criteria);

# Reset category
$current_category = "";

$rss = new SimpleXMLElementExtended('<rss xmlns:g="http://base.google.com/ns/1.0"></rss>');
$rss->addAttribute('version', '2.0');


$title = $rss->addChild('title','CyberPhoto AB'); //title of the feed
$description = $rss->addChild('description','CyberPhoto AB google shopping rss feed'); 
$link = $rss->addChild('link','https://www.cyberphoto.se/database/google.php'); //feed site
$channel = $rss->addChild('channel'); 


        $xml = '<?xml version="1.0"?>' . "\n";
        $xml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">' . "\n";
        $xml .= '<channel>' . "\n";
        // Shop name
        $xml .= '<title><![CDATA[CyberPhoto AB]]></title>' . "\n";
        // Shop description
        $xml .= '<description><![CDATA[CyberPhoto AB google shopping rss feed]]></description>' . "\n";
        $xml .= '<link>https://www.cyberphoto.se/database/google.php</link>' . "\n";
        


$i = 0;
while (list($key, $article) = each($articles)) {
	$i++;
	/**
	$i = 0;
	if ($_COOKIE['login_mail'] != 'nils@cyberphoto.nu') {
		if ($i>200) break;
	} else {
		if ($i>200) break;
	}
	*/

	$bestallningsgrans = $article->bestallningsgrans;
	$showotherstatus = 'i lager';
	if ($article->lagersaldo > 0) {
		$lager = "yes";
		if ($fi) {
			$showotherstatus = "varastossa";
		} elseif ($no) {
			$showotherstatus = "finnes på lager";
		} else {
			$showotherstatus = "i lager";
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
				$showotherstatus = "i lager";
			}
		} else {
			$lager = "no";
			if ($fi) {
				$showotherstatus = "ei varastossa";
			} elseif ($no) {
				$showotherstatus = "finnes ej på lager";
			} else {
				$showotherstatus = "ej i lager";
			}
		}
	} else {
		if ($bestallningsgrans == 0) {
			$showotherstatus = 'förbeställning';
		} else {
			if ($fi) {
				$showotherstatus = "ei varastossa";
			} elseif ($no) {
				$showotherstatus = "finnes ej på lager";
			} else {
				$showotherstatus = "ej i lager";
			}			
		}
		$lager = "no";
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
	
	if ($fi) {
		$link = "https://www.cyberphoto.fi/info.php?article=".$article->artnr . $cpc_linc;
	} elseif ($no) {
		$link = "https://www.cyberphoto.no/info.php?article=".$article->artnr . $cpc_linc;
	} else {
		$link = "https://www.cyberphoto.se/info.php?article=" . $article->artnr . $cpc_linc;
	}

	if ($article->tillverkare <> ".")
		$tillverkare = $article->tillverkare;
	else
		$tillverkare = "";
	
	if ($article->bild != "") {
		if (substr($article->bild, 0, 3) != "../") {
			
			if ($fi) {
				$bild = "https://www.cyberphoto.fi/" . $article->bild;
			} elseif ($no) {
				$bild = "https://www.cyberphoto.no/" . $article->bild;
			} else {
				$bild = "https://www.cyberphoto.se/" . $article->bild;
			}

		} else {
			
			if ($fi) {
				$bild = "https://www.cyberphoto.fi" . eregi_replace("^(\.\.)", "",  $article->bild);
			} elseif ($no) {
				$bild = "https://www.cyberphoto.no" . eregi_replace("^(\.\.)", "",  $article->bild);
			} else {
				$bild = "https://www.cyberphoto.se" . eregi_replace("^(\.\.)", "",  $article->bild);
			}
			
		}
	} else {
		$bild = "";
	}
	/**
	if ((
		$article->kategori_id == 336 || $article->kategori_id == 392 || $article->kategori_id == 393 || $article->kategori_id == 394 || 
		$article->kategori_id == 395 || $article->kategori_id == 748 || $article->kategori_id == 426 || $article->kategori_id == 522 || 
		$article->kategori_id == 517 || $article->kategori_id == 1000217 || $article->kategori_id == 1000282 || 
		$article->kategori_id == 1000248
		) && $article->bestallningsgrans > 0 && $lager == "no") { // om huvudkategorier s? skickar vi med f?rv?ntat leveransdatum
		
		$getIncomingStatus = $web_adempiere->check_lager($article->artnr,1000000,$count);
		
		if ($getIncomingStatus == "tillf?lligt slut" && checkSkapad($article->artnr)) {
			$getIncomingStatus = "F?rhandsboka";
		}
		
		if ($fi) {
			echo $article->artnr . "|".$article->beskrivning_fi."|".$article->kategori."|".round(($article->utpris_fi * $article->momssats_fi)+ $article->utpris_fi)."|".round((($article->utpris_fi * $article->momssats_fi)+ $article->utpris_fi)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "|" . $getIncomingStatus . "|".$article->manufacturerproductno. "";
		} elseif ($no) {
			echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round(($article->utpris_no * $article->momssats_no)+ $article->utpris_no)."|".round((($article->utpris_no * $article->momssats_no)+ $article->utpris_no)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "|" . $getIncomingStatus . "|".$article->manufacturerproductno. "";
		} else {
			echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round(($article->utpris * $article->momssats)+ $article->utpris)."|".round((($article->utpris * $article->momssats)+ $article->utpris)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "|" . $getIncomingStatus . "|".$article->manufacturerproductno. "";
		}
	} else {
		if ($fi) {
			echo $article->artnr . "|".$article->beskrivning_fi."|".$article->kategori."|".round(($article->utpris_fi * $article->momssats_fi)+ $article->utpris_fi)."|".round((($article->utpris_fi * $article->momssats_fi)+ $article->utpris_fi)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "| |".$article->manufacturerproductno. "";
		} elseif ($no) {
			echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round(($article->utpris_no * $article->momssats_no)+ $article->utpris_no)."|".round((($article->utpris_no * $article->momssats_no)+ $article->utpris_no)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "| |".$article->manufacturerproductno. "";
		} else {
			echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round(($article->utpris * $article->momssats)+ $article->utpris)."|".round((($article->utpris * $article->momssats)+ $article->utpris)+$fraktpris)."|".$link."|".$tillverkare."|" . $lager ."|".$bild. "| |".$article->manufacturerproductno. "";
		}
	}*/ 
		$upc = preg_replace("/[^0-9]/","",$article->upc);
		if (strlen($upc) > 11 && strlen($upc) < 15) {
			$upc = $upc;
		} else {
			continue;
		}
		if ((int)$article->utpris == 0)
			continue;
		if (trim($bild) == '')
			continue;
		
        $xml .= '<item>' . "\n";
        $item = $channel->addChild('item'); //add item node
         
        $xml .= '<g:id>'.$i.$article->artnr.'</g:id>'."" . "\n";
		$id = $item->addChild('xmlns:g:id', $article->artnr); //upc om det finns, annars tillverkas artnr, annars v?rt artnr
		
        $title = $item->addChildWithCDATA('xmlns:g:title', '' . trim($article->beskrivning). ''); //add title node under item
		
		$titl = html_entity_decode((trim($article->beskrivning)), ENT_COMPAT | ENT_HTML5);
		$titl = htmlspecialchars_decode($titl);
		$titl =  trim(strip_tags( $titl ));		
        //$xml .= '<g:title><![CDATA['.utf8encode(($article->beskrivning)).']]></g:title>'."" . "\n";
		//if ($_SERVER['SERVER_ADDR'] == '192.168.100.80')
		$titl = utf8encode($titl);
		$xml .= '<g:title><![CDATA['.$titl.']]></g:title>'."" . "\n";
        $desc = getDescription($article->artnr);
		
		$desc = str_replace('&ndash;', '-', $desc);
		$desc = str_replace(chr(153), '', $desc);
		$desc = str_replace(chr(150), '', $desc);
		$desc = str_replace(chr(151), '', $desc);
		$desc = str_replace('&reg;', '', $desc);
		$desc = str_replace('&trade;', '', $desc);
		$desc = str_replace(chr(153), '', $desc);
		//
		$desc = str_replace('', '"', $desc);
		$desc = str_replace('', '-', $desc);
		
		
		$desc = html_entity_decode((trim($desc)), ENT_COMPAT | ENT_HTML5);
		
		//$desc = decode_entities_full($desc, ENT_COMPAT);
	    if ($_SERVER['SERVER_ADDR'] != '192.168.100.80')			
			$desc = utf8encode($desc);
		//$desc = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $desc);
		//if (trim($desc) == '')			$desc = utf8encode(($article->beskrivning));
		
		$description = $item->addChildWithCDATA('xmlns:g:description','' . $description . '');
		$xml .= '<g:description><![CDATA['.trim($desc).']]></g:description>'."" . "\n";
		
		$child = $item->addChildWithCDATA('xmlns:g:link','' . $link . '');
        $xml .= '<g:link><![CDATA['.$link.']]></g:link>'."" . "\n";
        
		if (trim($bild) != '')
			$child = $item->addChildWithCDATA('xmlns:g:image_link','' . trim($bild) . ''); // bildl?nk, f?rsta bildl?nken
		$xml .= '<g:image_link><![CDATA['.trim($bild).']]></g:image_link>'."" . "\n";
        
		//$child = $item->addChildWithCDATA('xmlns:g:additional_image_link','' . . ''); // resten av bilderna
		//$child = $item->addChildWithCDATA('xmlns:g:condition','' . . ''); // begagnad etc
		$child = $item->addChildWithCDATA('xmlns:g:condition','ny'); // in stock etc
		$xml .= '<g:condition>ny</g:condition>'."" . "\n";
		
		$child = $item->addChildWithCDATA('xmlns:g:availability','' . $showotherstatus. ''); // in stock etc
		$xml .= '<g:availability>'.utf8encode($showotherstatus).'</g:availability>'."" . "\n";
        
		$child = $item->addChild('xmlns:g:price', (round(($article->utpris * $article->momssats)+ $article->utpris)) );
		$xml .= '<g:price>'.(round(($article->utpris * $article->momssats)+ $article->utpris)).'</g:price>'."" . "\n";
        
		$shipping = $item->addChild('xmlns:g:shipping');
        $xml .= '<g:shipping>'."" . "\n";
        
			$child = $shipping->addChild('xmlns:g:country','SE'); // e.g SE
			$xml .= '<g:country>SE</g:country>'."" . "\n";
            
			$child = $shipping->addChild('xmlns:g:service', 'MyPack'); // e.g. MyPack collect eller vad det nu heter
			$xml .= '<g:service>MyPack</g:service>'."" . "\n";
			
			$child = $shipping->addChild('xmlns:g:price', $fraktpris); // fraktkostnad
			$xml .= '<g:price>'. $fraktpris .'</g:price>'."" . "\n";
            
	    $xml .= '</g:shipping>'."" . "\n";
        
		
		$child = $item->addChild('xmlns:g:gtin','' . $artId . ''); // Produktens GTIN (Global Trade Item Number), EAN i v?rt fall (eller kolumn upc)
		$xml .= '<g:gtin>'.$artId.'</g:gtin>' . "" . "\n";
        
		if (trim($article->tillverkare!='')) {
			//$tillverkare = str_replace('Sea&Sea' , 'Sea and Sea', $article->tillverkare);
			$child = $item->addChild('xmlns:g:brand','' . utf8encode($tillverkare) . ''); // tillverkare
			$xml .= '<g:brand><![CDATA['.utf8encode(trim($tillverkare)).']]></g:brand>'."" . "\n";
        }
		if (trim($article->manufacturerproductno)!='') {
			$child = $item->addChildWithCDATA('xmlns:g:mpn','' . $article->manufacturerproductno . ''); // tillverkarens artnr
			$xml .= '<g:mpn><![CDATA['.trim($article->manufacturerproductno).']]></g:mpn>'."" . "\n";
        }
		//$child = $item->addChildWithCDATA('xmlns:g:google_product_category','' . $article->kategori . ''); // googles kategorisering, vi har inget st?d f?r det
		if (trim($article->kategori!='')) {
			$child = $item->addChildWithCDATA('xmlns:g:product_type','' . $article->kategori  . ''); // v?rt kategorinamn'
			$xml .= '<g:product_type><![CDATA['.utf8encode($article->kategori).']]></g:product_type>'."" . "\n";
	    }
		//$child = $item->addChildWithCDATA('xmlns:g:sale_price','' . . ''); // om rabatterat
		//$child = $item->addChildWithCDATA('xmlns:g:sale_price_effective_date','' .  . ''); // n?r det slutar
		
		$child = $item->addChild('xmlns:g:is_bundle',($article->isSalesBundle == -1 ?  'ja' :   'nej')); // ja / nej
		$xml .= '<g:is_bundle>'.($article->isSalesBundle == -1 ?  'ja' :   'nej').'</g:is_bundle>'."" . "\n";
        $xml .= '</item>' . "\n";

}
        $xml .= '</channel>' . "\n";  	
        $xml .= '</rss>' . "";  
echo $xml;
//echo $rss->asXML();	

function getFreightPriceWithVat($artnr) {
	global $fi, $no;
	
	$select  = "SELECT utpris, utpris_no, utpris_fi, Moms.momssats, Moms.momssats_fi, Moms.momssats_no ";
	$select .= "FROM Artiklar ";
	$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
    $select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
    $select .= "WHERE artnr = '$artnr' ";
	$res = mysqli_query(Db::getConnection(), $select);
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
function getDescription($artnr) {
	global $conn;
	$select  = "SELECT produktinfo_text, test_text  ";
	$select .= "FROM Info_page  ";
    $select .= "WHERE artnr = '" . $artnr. "' ";
	//$conn = Db::getConnection();
	//echo mysqli_character_set_name($conn); exit;
	//mysqli_set_charset("UTF-8", $conn);
	//mysqli_set_charset("latin1", $conn);

	$res = mysqli_query($conn, $select);
	$row = mysqli_fetch_object($res);
	
	if ($row->test_text != '')
		$res =  $row->test_text;
	else
		$res = $row->produktinfo_text;
	
	$res = mb_substr($res, 0, 4995);

	if (mb_strlen($res)==4995) {
		$res .= '...';
	}
	//$res = utf8_encode($res);
	//$res = $res;
	//return $res;
	$res = htmlspecialchars_decode($res);
	//mysqli_set_charset("latin1", $conn);
	return trim(strip_tags( $res ));
	
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
function utf8encode($string) {
	return $string;
}
Class SimpleXMLElementExtended extends SimpleXMLElement {

  /**
   * Adds a child with $value inside CDATA
   * @param unknown $name
   * @param unknown $value
   */
  public function addChildWithCDATA($name, $value = NULL) {
    $new_child = $this->addChild($name);

    if ($new_child !== NULL) {
      $node = dom_import_simplexml($new_child);
      $no   = $node->ownerDocument;
      $node->appendChild($no->createCDATASection($value));
    }

    return $new_child;
  }
  
}

?>
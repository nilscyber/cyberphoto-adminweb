<?php

# PHP Pricelist script
# author	Nils Kohlström
# version	2000-11-20


# Read articles using criteria
# if $criteria is not defined, all Articles will be selected therefore:
if ($critiera = "")
 { $criteria = "WHERE Artiklar.kategori_id=0"; }

 $articles = readArticlesWithCriteria($criteria);

# Reset category
$current_category = "";

while (list($key, $article) = each($articles)) :
	if ($article->lagersaldo > 0)
		$lager = "yes";
	else
		$lager = "no";
	
	if ($article->link) {
		$link = "http://www.cyberphoto.se/?".$article->link;
	}
	else {
		$link = "http://www.cyberphoto.se/?info.php?article=".$article->artnr;
	}
	if ($article->tillverkare <> ".")
		$tillverkare = $article->tillverkare;
	else
		$tillverkare = "";
	
	echo $tillverkare . " " . $article->beskrivning.";" . 
		"".$link  . "\n";
	
	/*
	echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".
		round( $article->utpris + $article->utpris * $article->momssats )."|".$article->kategori.round(($article->utpris + $article->utpris * $article->momssats + 99)).
		"|".$link."|".$tillverkare."\n";
		
	*/
	/*
	(en produkt per rad)
	pris_id|produkt_namn|kategori|pris ink
	moms|pris_inkfrakt*|url_till_produkten*|tillverkare*
	*/
endwhile;

$headline = "";

?>
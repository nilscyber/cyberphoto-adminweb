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
	
	if ($article->ej_brev == 0 && (round($article->utpris * 1.25) < 500 )) {
		echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round($article->utpris*1.25)."|".round(($article->utpris+28)*1.25)."|".$link."|".$tillverkare."|" . $lager . "\n";
	} else {
		echo $article->artnr . "|".$article->beskrivning."|".$article->kategori."|".round($article->utpris*1.25)."|".round(($article->utpris+55.2)*1.25)."|".$link."|".$tillverkare."|" . $lager . "\n";
	}
	
endwhile;

$headline = "";

?>
<?php

/*

Två funktioner som returnerar de mest sålda artiklarna inom en viss kategori. 
Den ena är begränsad till de senaste 30 dagarna för att få mer dagsaktuella
uppgifter. 

*/
// kolla om finska sidor. 
if (eregi("_fi.php", $GLOBALS['PHP_SELF'])) {
	$fi = true;
	$sv = false;

} elseif (eregi("_fi_se.php", $GLOBALS['PHP_SELF'])) {
	$fi = true;
	$sv = true;	
} else {
	$fi = false;
	$sv = true;

}
function mostSold($kategori_id, $limit) {
	global $fi, $sv;
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticles.artnr, mostSoldArticles.antal, Artiklar.link, 
	Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi FROM mostSoldArticles, Artiklar LEFT JOIN 
	Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi 
	WHERE mostSoldArticles.artnr = Artiklar.artnr AND 
	kategori_id = $kategori_id ";
	if ($fi)
		$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) ";
	else
		$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) ";
	
	
	$select .= " ORDER BY antal DESC";
	
	$res = mysqli_query($select);
	if ($limit >= 10) 
		$space = "&nbsp;&nbsp;";
	
	while ($row = mysqli_fetch_array($res)) {
		$int += 1;
		extract ($row);
		if ($fi && !$sv) {
			
			if ($beskrivning_fi != "")
				$beskrivning = $beskrivning_fi;
			
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi.php?article=".$artnr;
		} elseif ($fi && $sv) {
		
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi_se.php?article=".$artnr;
			
		} else {
			if ($link != "")
				$link = $link;
			else
				$link = "info.php?article=".$artnr;
			
		}
		

		$output .= $int . ".";
		if ($int < 10) 
			$output .= $space;
		$output .= "<a href=\"" . $link . "\"> "  . $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;
}

function mostSoldLimited2($kategori_id, $limit) {
	global $fi, $sv;
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi FROM mostSoldArticlesLimited, Artiklar 
	LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND 
	kategori_id = $kategori_id  ";
	if ($fi)
		$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) ";
	else
		$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1) ";
	
	
	$select .= " ORDER BY antal DESC";	

	$res = mysqli_query($select);

	if ($limit >= 10) 
		$space = "&nbsp;&nbsp;";
	
	while ($row = mysqli_fetch_array($res)) {
		$int += 1;
		extract ($row);
		if ($fi && !$sv) {
			
			if ($beskrivning_fi != "")
				$beskrivning = $beskrivning_fi;
			
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi.php?article=".$artnr;
		} elseif ($fi && $sv) {
		
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi_se.php?article=".$artnr;
			
		} else {
			if ($link != "")
				$link = $link;
			else
				$link = "info.php?article=".$artnr;
			
		}
		

		$output .= $int . ".";
		if ($int < 10) 
			$output .= $space;
		$output .= "<a href=\"" . $link . "\"> "  . $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;}

function mostSoldLimited($kategori_id, $limit) {
	global $fi, $sv;
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning, Tillverkare.tillverkare, Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi FROM mostSoldArticlesLimited, Artiklar, Tillverkare 
	LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND
	kategori_id = $kategori_id ";
	if ($fi)
		$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) ";
	else
		$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) ";
	
	
	$select .= " ORDER BY antal DESC";	
	
	$res = mysqli_query($select);

	if ($limit >= 10) 
		$space = "&nbsp;&nbsp;";
	
	while ($row = mysqli_fetch_array($res)) {
		$int += 1;
		extract ($row);
		if ($fi && !$sv) {
			
			if ($beskrivning_fi != "")
				$beskrivning = $beskrivning_fi;
			
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi.php?article=".$artnr;
		} elseif ($fi && $sv) {
		
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi_se.php?article=".$artnr;
			
		} else {
			if ($link != "")
				$link = $link;
			else
				$link = "info.php?article=".$artnr;
			
		}
		

		$output .= $int . ".";
		if ($int < 10) 
			$output .= $space;
		$output .= "<a href=\"" . $link . "\"> "  . $tillverkare . " " . $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;
}


function mostSoldLimitedNew($kategorier_id, $limit) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
	global $fi, $sv;
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	
	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning, Tillverkare.tillverkare, Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi FROM mostSoldArticlesLimited, Artiklar, Tillverkare 
	LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND (";

        $select  = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, Artiklar.beskrivning, Tillverkare.tillverkare, ";
        $select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi ";
        $select .= "FROM Artiklar ";
        $select .= "INNER JOIN mostSoldArticlesLimited ON mostSoldArticlesLimited.artnr = Artiklar.artnr ";
        $select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
        $select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
        $select .= " WHERE (";


	$kategori_id = split (" ", $kategorier_id);
	$n = count($kategori_id);
	
	for ($i=0; $i<$n; $i+=1) {
		
		$select .= "Artiklar.kategori_id = " . trim($kategori_id[$i]);
		if ($i<>$n-1)
			$select .= " OR \n";
	}
	$select .= ")";
	
	if ($fi)
		$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) ";
	else
		$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1) ";
	
	
	$select .= " ORDER BY antal DESC";	
	
	//$select .= "AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND Artiklar.ej_med=0 ORDER BY antal DESC";
	//echo $select;
	$res = mysqli_query($select);

	if ($limit >= 10) 
		$space = "&nbsp;&nbsp;";
	
	while ($row = mysqli_fetch_array($res)) {
		$int += 1;
		extract ($row);
		if ($fi && !$sv) {
			
			if ($beskrivning_fi != "")
				$beskrivning = $beskrivning_fi;
			
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi.php?article=".$artnr;
		} elseif ($fi && $sv) {
		
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi_se.php?article=".$artnr;
			
		} else {

			if ($link != "")
				$link = $link;
			else
				$link = "info.php?article=".$artnr;
			
		}
		

		$output .= $int . ".";
		if ($int < 10) 
			$output .= $space;
		$output .= "<a href=\"" . $link . "\"> "  . $tillverkare . " " .  $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;
}


function splitToArr($arg) {


	
	$newargument = split (" ", $arg);
	$n = count($newargument);

	for ($i=0; $i<$n; $i+=2) {

		}


}

function mostSoldLimitedHörlur($limit) {
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning, Tillverkare.tillverkare FROM mostSoldArticlesLimited, Artiklar, Tillverkare 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND 
	(Artiklar.kategori_id=154 OR Artiklar.kategori_id=321 
	OR Artiklar.kategori_id=322 OR Artiklar.kategori_id=323 OR Artiklar.kategori_id=324) 	

	AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND Artiklar.ej_med=0 ORDER BY antal DESC";
	//echo $select;
	$res = mysqli_query($select);

	if ($limit >= 10) 
		$space = "&nbsp;&nbsp;";
	
	while ($row = mysqli_fetch_array($res)) {
		$int += 1;
		extract ($row);
		if (strlen($link) == 0) {
			$link = "info.php?article=".$artnr;
		}

		$output .= $int . ".";
		if ($int < 10) 
			$output .= $space;
		$output .= "<a href=\"" . $link . "\"> " . $tillverkare . " " . $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;
}

// exakt samma som "utan" _fi, bara med för bakåtkompatibelt i prislistor. 
function mostSoldLimitedNew_fi($kategorier_id, $limit) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
	global $fi, $sv;
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning, Tillverkare.tillverkare, Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi FROM mostSoldArticlesLimited, Artiklar, Tillverkare 
	LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND (";

	$kategori_id = split (" ", $kategorier_id);
	$n = count($kategori_id);
	
	for ($i=0; $i<$n; $i+=1) {
		
		$select .= "Artiklar.kategori_id = " . trim($kategori_id[$i]);
		if ($i<>$n-1)
			$select .= " OR \n";
	}
	$select .= ")";
	
	if ($fi)
		$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0)  AND NOT (Artiklar.demo = -1) ";
	else
		$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0)  AND NOT (Artiklar.demo = -1) ";
	
	
	$select .= " ORDER BY antal DESC";	
	//echo $select;
	$res = mysqli_query($select);

	if ($limit >= 10) 
		$space = "&nbsp;&nbsp;";
	
	while ($row = mysqli_fetch_array($res)) {
		$int += 1;
		extract ($row);
		if ($fi && !$sv) {
			
			if ($beskrivning_fi != "")
				$beskrivning = $beskrivning_fi;
			
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi.php?article=".$artnr;
		} elseif ($fi && $sv) {
		
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi_se.php?article=".$artnr;
			
		} else {

			if ($link != "")
				$link = $link;
			else
				$link = "info.php?article=".$artnr;
			
		}
		

		$output .= $int . ".";
		if ($int < 10) 
			$output .= $space;
		$output .= "<a href=\"" . $link . "\"> "  . $tillverkare . " " . $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;
}
// exakt samma som "utan" _fi, bara med för bakåtkompatibelt i prislistor. 
function mostSoldLimitedNew_fi_se($kategorier_id, $limit) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
	global $fi, $sv;
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning, Tillverkare.tillverkare, Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi FROM mostSoldArticlesLimited, Artiklar, Tillverkare 
	LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND (";

	$kategori_id = split (" ", $kategorier_id);
	$n = count($kategori_id);
	
	for ($i=0; $i<$n; $i+=1) {
		
		$select .= "Artiklar.kategori_id = " . trim($kategori_id[$i]);
		if ($i<>$n-1)
			$select .= " OR \n";
	}
	$select .= ")";
	
	if ($fi)
		$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) ";
	else
		$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) ";
	
	
	$select .= " ORDER BY antal DESC";	

	$res = mysqli_query($select);

	if ($limit >= 10) 
		$space = "&nbsp;&nbsp;";
	
	while ($row = mysqli_fetch_array($res)) {
		$int += 1;
		extract ($row);
		if ($fi && !$sv) {
			
			if ($beskrivning_fi != "")
				$beskrivning = $beskrivning_fi;
			
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi.php?article=".$artnr;
		} elseif ($fi && $sv) {
		
			if ($link2_fi != "")
				$link = $link2_fi;
			else
				$link = "info_fi_se.php?article=".$artnr;
			
		} else {

			if ($link != "")
				$link = $link;
			else
				$link = "info.php?article=".$artnr;
			
		}
		

		$output .= $int . ".";
		if ($int < 10) 
			$output .= $space;
		$output .= "<a href=\"" . $link . "\"> "  . $tillverkare . " " . $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;
}

function mostSoldLimitedNew_obj($kategorier_id, $limit) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning, Tillverkare.tillverkare, Artiklar.tillverkar_id FROM mostSoldArticlesLimited, Artiklar, Tillverkare 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.tillverkar_id = 3 AND (";

	$kategori_id = split (" ", $kategorier_id);
	$n = count($kategori_id);

	for ($i=0; $i<$n; $i+=1) {
		
		$select .= "Artiklar.kategori_id = " . trim($kategori_id[$i]);
		if ($i<>$n-1)
			$select .= " OR \n";
	}
	$select .= ")";
	
	
	
	$select .= "AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND Artiklar.ej_med=0 ORDER BY antal DESC";
	//echo $select;
	$res = mysqli_query($select);

	if ($limit >= 10) 
		$space = "&nbsp;&nbsp;";
	
	while ($row = mysqli_fetch_array($res)) {
		$int += 1;
		extract ($row);
		if (strlen($link) == 0) {
			$link = "info_fi_se.php?article=".$artnr;
		}

		$output .= $int . ".";
		if ($int < 10) 
			$output .= $space;
		$output .= "<a href=\"" . $link . "\"> " . $tillverkare . " " . $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;
}

?>
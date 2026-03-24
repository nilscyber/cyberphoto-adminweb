<%

/*

Två funktioner som returnerar de mest sålda artiklarna inom en viss kategori. 
Den ena är begränsad till de senaste 30 dagarna för att få mer dagsaktuella
uppgifter. 

*/
function mostSold($kategori_id, $limit) {
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticles.artnr, mostSoldArticles.antal, Artiklar.link, 
	Artiklar.beskrivning FROM mostSoldArticles, Artiklar 
	WHERE mostSoldArticles.artnr = Artiklar.artnr AND 
	kategori_id = $kategori_id ORDER BY antal DESC";
	
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
		$output .= "<a href=\"" . $link . "\"> "  . $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;
}

function mostSoldLimited2($kategori_id, $limit) {
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning FROM mostSoldArticlesLimited, Artiklar 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND 
	kategori_id = $kategori_id  AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) ORDER BY antal DESC";

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
		$output .= "<a href=\"" . $link . "\"> "  . $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;
}

function mostSoldLimited($kategori_id, $limit) {
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
	kategori_id = $kategori_id AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND Artiklar.ej_med=0 ORDER BY antal DESC";
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


function mostSoldLimitedNew($kategorier_id, $limit) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning, Tillverkare.tillverkare FROM mostSoldArticlesLimited, Artiklar, Tillverkare 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND (";

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

function mostSoldLimitedNew_fi($kategorier_id, $limit) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning, Tillverkare.tillverkare FROM mostSoldArticlesLimited, Artiklar, Tillverkare 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND (";

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
			$link = "info_fi.php?article=".$artnr;
		}

		$output .= $int . ".";
		if ($int < 10) 
			$output .= $space;
		$output .= "<a href=\"" . $link . "\"> " . $tillverkare . " " . $beskrivning . "</a><br>\n";

		if ($int >= $limit) break;
	}
	return $output;
}

function mostSoldLimitedNew_fi_se($kategorier_id, $limit) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

	$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
	Artiklar.beskrivning, Tillverkare.tillverkare FROM mostSoldArticlesLimited, Artiklar, Tillverkare 
	WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND (";

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

%>
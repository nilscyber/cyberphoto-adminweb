<%

/*

Två funktioner som returnerar de mest sålda artiklarna inom en viss kategori. 
Den ena är begränsad till de senaste 30 dagarna för att få mer dagsaktuella
uppgifter. 

*/
function mostSoldLimitedNew($kategorier_id, $limit) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
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
%>
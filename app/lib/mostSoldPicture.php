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

        $select  = "SELECT mostSoldArticles.artnr, mostSoldArticles.antalManad1, Artiklar.link, Artiklar.beskrivning, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, ";
        $select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi ";
        $select .= "FROM Artiklar ";
        $select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
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
		$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
	else
		$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
	
	$select .= " ORDER BY antalManad1 DESC";	
	
	$select .= " LIMIT " . $limit;	
	
	//$select .= "AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND Artiklar.ej_med=0 ORDER BY antal DESC";
	//echo $select;
	$res = mysqli_query($select);

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

		if ($bild == "") {

			if ($fi && !$sv) {
				$bild = "../../../pic/noimage_fi.jpg";
			} else {
				$bild = "../../../pic/noimage.jpg";
			}
		}

		$trimmaheader = $tillverkare . " " . $beskrivning;
		
		if (strlen($trimmaheader) >= 40)
			$trimmaheader = substr ($trimmaheader, 0, 40) . "...";

		print "<tr>";
		print "<td width=\"15\" valign=\"top\"><font size=\"1\" face=\"Verdana\">" . $int . ".</td>\n";
		if ($fi && !$sv) {
		print "<td><a onmouseover=\"return escape('<b>". $tillverkare . " ". $beskrivning . "</b><br><br>&nbsp;&nbsp;&nbsp;&nbsp;<img src=\'/thumbs/large/bilder/". $bild . "\'><br><br><i>Klikkaamalla linkkiä saat lisätietoja.&nbsp;')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $trimmaheader . "</a></td>\n";
		} else {
		print "<td><a onmouseover=\"return escape('<b>". $tillverkare . " ". $beskrivning . "</b><br><br>&nbsp;&nbsp;&nbsp;&nbsp;<img src=\'/thumbs/large/bilder/". $bild . "\'><br><br><i>Klicka på länken för mer information.&nbsp;')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $trimmaheader . "</a></td>\n";
		}
		print "</tr>";

	}
	return $output;
}

function mostSoldLimitedNewLevID($kategorier_id, $limit, $levID) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
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

        $select  = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, Artiklar.beskrivning, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, ";
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
		$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
	else
		$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
	
	$select .= " AND Artiklar.tillverkar_id =" . $levID;	

	$select .= " ORDER BY antal DESC";	
	
	$select .= " LIMIT " . $limit;	
	
	//$select .= "AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND Artiklar.ej_med=0 ORDER BY antal DESC";
	//echo $select;
	$res = mysqli_query($select);

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

		if ($bild == "") {

			if ($fi && !$sv) {
				$bild = "../../../pic/noimage_fi.jpg";
			} else {
				$bild = "../../../pic/noimage.jpg";
			}
		}

		print "<tr>";
		print "<td width=\"15\" valign=\"top\"><font size=\"1\" face=\"Verdana\">" . $int . ".</td>\n";
		if ($fi && !$sv) {
		print "<td><a onmouseover=\"return escape('<b>". $tillverkare . " ". $beskrivning . "</b><br><br>&nbsp;&nbsp;&nbsp;&nbsp;<img src=\'/thumbs/large/bilder/". $bild . "\'><br><br><i>Klikkaamalla linkkiä saat lisätietoja.&nbsp;')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $tillverkare . " " .  $beskrivning . "</a></td>\n";
		} else {
		print "<td><a onmouseover=\"return escape('<b>". $tillverkare . " ". $beskrivning . "</b><br><br>&nbsp;&nbsp;&nbsp;&nbsp;<img src=\'/thumbs/large/bilder/". $bild . "\'><br><br><i>Klicka på länken för mer information.&nbsp;')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $tillverkare . " " .  $beskrivning . "</a></td>\n";
		}
		print "</tr>";

	}
	return $output;
}

function mostSoldLimitedNewWithPic($kategorier_id, $limit) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
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

        $select  = "SELECT mostSoldArticles.artnr, mostSoldArticles.antal, Artiklar.link, Artiklar.beskrivning, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, ";
        $select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi ";
        $select .= "FROM Artiklar ";
        $select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
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
		$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
	else
		$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
	
	$select .= " ORDER BY antal DESC";	
	
	$select .= " LIMIT " . $limit;	
	
	//$select .= "AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND Artiklar.ej_med=0 ORDER BY antal DESC";
	// echo $select;
	// exit;
	$res = mysqli_query($select);

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

		if ($bild == "") {

			if ($fi && !$sv) {
				$bild = "../../../pic/noimage_fi.jpg";
			} else {
				$bild = "../../../pic/noimage.jpg";
			}
		}

		print "<tr>";
		// print "<td width=\"15\" valign=\"top\"><font size=\"1\" face=\"Verdana\">" . $int . ".</td>\n";
		print "<td height=\"64\" width=\"64\"><a href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		print "<td height=\"64\"><a href=\"" . $link . "\"><b><font size=\"1\" face=\"Verdana\"> "  . $tillverkare . " " .  $beskrivning . "</b></a></td>\n";
		print "</tr>";

	}
	return $output;
}

function mostSoldLimitedNewWithPicMobil($kategorier_id, $limit) { // kategorier_id = kategorier som ska vara med, uppdelade av mellanslag
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

        $select  = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, ";
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
		$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
	else
		$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
	
	$select .= " ORDER BY antal DESC";	
	
	$select .= " LIMIT " . $limit;	
	
	//$select .= "AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND Artiklar.ej_med=0 ORDER BY antal DESC";
	//echo $select;
	$res = mysqli_query($select);

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
				$link = "../info_mobil.php?article=".$artnr;
			
		}

		if ($bild == "") {

			if ($fi && !$sv) {
				$bild = "../../../pic/noimage_fi.jpg";
			} else {
				$bild = "../../../pic/noimage.jpg";
			}
		}

		$utprismoms = number_format(($utpris + $utpris * 0.25), 0, ',', ' ');

		print "<tr>";
		// print "<td width=\"15\" valign=\"top\"><font size=\"1\" face=\"Verdana\">" . $int . ".</td>\n";
		if ($kortinfo != "") {
		print "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>$kortinfo<br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		} else {
		print "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		}
		if ($kortinfo != "") {
		print "<td height=\"64\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>$kortinfo<br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $tillverkare . " " .  $beskrivning . "</b></a></td>\n";
		} else {
		print "<td height=\"64\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $tillverkare . " " .  $beskrivning . "</b></a></td>\n";
		}
		// print "<td width=\"15\" valign=\"center\"><font size=\"1\" face=\"Verdana\"><a href=\"javascript:modifyItems('$artnr')\"><img border=\"0\" src=\"/pic/01.gif\"></a></td>\n";
		print "</tr>";

	}
	return $output;
}

?>
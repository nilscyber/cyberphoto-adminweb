<%
Class CMostSold {
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
			$output .= "<a href=\"" . $link . "\"> "  . $beskrivning . " " . $antal . "</a><br>\n";

			if ($int >= $limit) break;
		}
		return $output;
	}

	function mostSoldLimited($kategori_id, $limit) {
		/*
		Returnerar en lista på $limit sålda produkter på kategori $kategori_id
		(just nu 30 senaste dagarna)

		*/
		$int = 0;
		$select = "";
		$output = "";

		$select = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, 
		Artiklar.beskrivning FROM mostSoldArticlesLimited, Artiklar 
		WHERE mostSoldArticlesLimited.artnr = Artiklar.artnr AND 
		kategori_id = $kategori_id ORDER BY antal DESC";
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
			$output .= "<a href=\"" . $link . "\"> "  . $beskrivning . " " . $antal . "</a><br>\n";

			if ($int >= $limit) break;
		}
		return $output;
	}
}
%>
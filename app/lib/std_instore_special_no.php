<?php
		include ("std_remove_spec_product_no_fi.php"); // i denna hittas produkter som gäller både Norge & Finland
		
		// Övriga produkter som vi inte får sälja
		// $criteria .= " AND NOT Artiklar.artnr LIKE 'edges6%' "; // samsung egde

		// ta bort alla kategorier som INTE skall visas/hittas i Norge
		$criteria .= " AND NOT Artiklar.kategori_id = 327 "; // bildskärmar
		$criteria .= " AND NOT Artiklar.kategori_id = 387 "; // Bakgrund - papper
		// $criteria .= " AND NOT Artiklar.kategori_id = 741 "; // Bakgrund - tyg
		// $criteria .= " AND NOT Artiklar.kategori_id = 743 "; // Bakgrund - hopfällbara
		// $criteria .= " AND NOT Artiklar.kategori_id = 742 "; // Bakgrund - vinyl
		// $criteria .= " AND NOT Artiklar.kategori_id = 382 "; // Bakgrundsstativ (382)
		// $criteria .= " AND NOT Artiklar.kategori_id = 362 "; // Belysningsstativ (362)
		$criteria .= " AND NOT Artiklar.kategori_id = 1000078 "; // Kläder
		$criteria .= " AND NOT Artiklar.kategori_id IN(1000128,1000129,1000131,1000130,727,1000214) "; // Kläder Cybairgun
		// $criteria .= " AND NOT Kategori.kategori_id_parent IN (23,1000022,1000023) "; // Skrivare, skanner
		$criteria .= " AND NOT Artiklar.kategori_id = 103 "; // Fotopåsar (362)
		$criteria .= " AND NOT Artiklar.kategori_id IN (1000010,1000011,1000012) "; // Färdiga abonnemangslösningar, telia,tele2 samt halebop
		// $criteria .= " AND NOT Artiklar.kategori_id IN(1000187,1000190,1000336,543,1000189,1000186) "; // Knivar
		$criteria .= " AND NOT Kategori.kategori_id_parent IN (538) "; // Knivar och allt under
		
		// sedan tar vi bort alla produkter som går som tyngre, skrymmande frakt
		$criteria .= " AND Artiklar.frakt < 1 ";
?>
<?php
		include ("std_remove_spec_product_no_fi.php"); // i denna hittas produkter som gäller både Norge & Finland
		
		// ta bort alla kategorier som INTE skall visas/hittas i Finland
		$criteria .= " AND NOT Artiklar.kategori_id = 387 "; // Bakgrund - papper
		$criteria .= " AND NOT Artiklar.kategori_id = 742 "; // Bakgrund - vinyl
		$criteria .= " AND NOT Artiklar.kategori_id = 103 "; // Fotopåsar (362)
		$criteria .= " AND NOT Artiklar.kategori_id IN (1000010,1000011,1000012) "; // Färdiga abonnemangslösningar, telia,tele2 samt halebop
		$criteria .= " AND NOT Artiklar.kategori_id IN (634,696,1000218) "; // Kommunikationsradio
?>
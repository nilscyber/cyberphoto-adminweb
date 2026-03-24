<?php
		// Nikon som inte får visas
		$criteria .= " AND NOT Artiklar.artnr LIKE 'nid4%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'niDF%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni45pc%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni85pc%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni24pc%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '200VR2%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '300VRII%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni400vr%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni500vr%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni600vr%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni800vr%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'nid800e%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'nid810%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni58_14%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'nid3X%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '200-400VRII%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni300afs%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'Ni50_18nikkor%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'tc14e3%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'JAA910DA%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'TC-17%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'TC20EIII%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'WT-5%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni500f4efl%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ni600f4efl%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'SB5000%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'K360%' ";
		$criteria .= " AND NOT Artiklar.artnr = 'nid5' ";
		$criteria .= " AND NOT Artiklar.artnr = 'nid5pac' ";
		$criteria .= " AND NOT Artiklar.artnr = 'nid5cf' ";
		$criteria .= " AND NOT Artiklar.artnr = 'nid5cfpac' ";
		$criteria .= " AND NOT Artiklar.artnr = 'nid500' ";
		$criteria .= " AND NOT Artiklar.artnr = 'nid500pac' ";

		// Canon som inte får visas
		$criteria .= " AND NOT Artiklar.artnr LIKE 'EF1124%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '129065%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'tse17%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'tse24II%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '232045%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '232100%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '232180%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '232103%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'EF200IS%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '128679%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '128680%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '299199%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'ef400do2%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '128681%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '128683%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'EF800IS%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '1dx%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '7Dmk2%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '5dmk3%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '5ds%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE '760d%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'XA25%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'XF105%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'XF200%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'XF205%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'XF300%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'XF305%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'XC10%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'EOSC100%' ";
		$criteria .= " AND NOT Artiklar.artnr LIKE 'EOSC300%' ";
		
		// Övriga produkter som inte får visas
		$criteria .= " AND NOT Artiklar.artnr = 'forsakring' ";
		// $criteria .= " AND NOT Artiklar.artnr LIKE 'dmc-cm1%' ";

?>
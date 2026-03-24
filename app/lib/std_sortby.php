<?php
if ($sortera == "latest_products") {
	$criteria .= " ORDER BY Artiklar.date_add DESC ";
	$criteria .= " LIMIT 200 ";
} elseif ($sortera == "instore_falling") {
	$criteria .= " AND lagersaldo > 0 ";
	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		$criteria .= " ORDER BY lagersaldo DESC, Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	} else {
		$criteria .= " ORDER BY lagersaldo DESC, Kategori.sortPriority DESC, kategori ASC, Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	}
} elseif ($sortera == "instore") {
	$criteria .= " AND lagersaldo > 0 ";
	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		$criteria .= " ORDER BY Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	} else {
		$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	}
} elseif ($sortera == "discontinued") {
	$criteria .= " AND utgangen = -1 AND lagersaldo < 1 ";
	// $criteria .= " AND NOT Artiklar.kommentar LIKE '%garanti%' ";
	$criteria .= " AND NOT Artiklar.demo = -1 ";
	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		$criteria .= " ORDER BY lagersaldo DESC, Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	} else {
		$criteria .= " ORDER BY lagersaldo DESC, Kategori.sortPriority DESC, kategori ASC, Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	}
} elseif ($sortera == "old_tradein") {
	$criteria .= " AND utgangen = -1 AND lagersaldo < 1 ";
	$criteria .= " AND Artiklar.kommentar LIKE '%garanti%' ";
	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		$criteria .= " ORDER BY Artiklar.sortPriority DESC, Artiklar.date_add DESC, tillverkare ASC, beskrivning ASC ";
	} else {
		$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.date_add DESC, Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	}
} elseif ($sortera == "noweb_tradein") {
	$criteria .= " AND ej_med = -1 AND utgangen = -1 AND lagersaldo > 0 AND isTradeIn = -1 ";
	// $criteria .= " AND Artiklar.kommentar LIKE '%garanti%' ";
	// $criteria .= " ORDER BY Artiklar.date_add ASC, tillverkare ASC, beskrivning ASC ";
	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		$criteria .= " ORDER BY Artiklar.sortPriority DESC, Artiklar.date_add DESC, tillverkare ASC, beskrivning ASC ";
	} else {
		$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.date_add DESC, Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	}
} elseif ($sortera == "onlyweb_tradein") {
	$criteria .= " AND ej_med = 0 AND utgangen = -1 AND lagersaldo > 0 AND isTradeIn = -1 ";
	// $criteria .= " AND Artiklar.kommentar LIKE '%garanti%' ";
	// $criteria .= " ORDER BY Artiklar.date_add ASC, tillverkare ASC, beskrivning ASC ";
	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		$criteria .= " ORDER BY Artiklar.sortPriority DESC, Artiklar.date_add DESC, tillverkare ASC, beskrivning ASC ";
	} else {
		$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.date_add DESC, Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	}
} elseif ($sortera == "notinstore") {
	$criteria .= " AND lagersaldo < 1 ";
	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		$criteria .= " ORDER BY Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	} else {
		$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.sortPriority DESC, tillverkare ASC, beskrivning ASC ";
	}
} elseif ($sortera == "utpris") {

	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		if ($fi) {
			$criteria .= " ORDER BY Artiklar.spec13 ASC, utpris ASC, tillverkare DESC, beskrivning ASC ";
		} elseif ($no) {
			$criteria .= " ORDER BY Artiklar.spec13 ASC, utpris_no ASC, tillverkare DESC, beskrivning ASC ";
		} else {
			$criteria .= " ORDER BY Artiklar.spec13 ASC, utpris ASC, tillverkare DESC, beskrivning ASC ";
		}
	} else {
		if ($fi) {
			$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.spec13 ASC, utpris ASC, tillverkare DESC, beskrivning ASC ";
		} elseif ($no) {
			$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.spec13 ASC, utpris_no ASC, tillverkare DESC, beskrivning ASC ";
		} else {
			$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.spec13 ASC, utpris ASC, tillverkare DESC, beskrivning ASC ";
		}
	}

} elseif ($sortera == "utpris_fall") {

	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		if ($fi) {
			$criteria .= " ORDER BY Artiklar.spec13 ASC, utpris DESC, tillverkare DESC, beskrivning ASC ";
		} elseif ($no) {
			$criteria .= " ORDER BY Artiklar.spec13 ASC, utpris_no DESC, tillverkare DESC, beskrivning ASC ";
		} else {
			$criteria .= " ORDER BY Artiklar.spec13 ASC, utpris DESC, tillverkare DESC, beskrivning ASC ";
		}
	} else {
		if ($fi) {
			$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.spec13 ASC, utpris DESC, tillverkare DESC, beskrivning ASC ";
		} elseif ($no) {
			$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.spec13 ASC, utpris_no DESC, tillverkare DESC, beskrivning ASC ";
		} else {
			$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.spec13 ASC, utpris DESC, tillverkare DESC, beskrivning ASC ";
		}
	}

} elseif ($sortera == "testresultat") {

	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		$criteria .= " ORDER BY betyg ASC, Artiklar.spec13 ASC, tillverkare ASC, beskrivning ASC ";
	} else {
		$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, betyg ASC, Artiklar.spec13 ASC, tillverkare DESC, beskrivning ASC ";
	}

// } else {
} elseif ($sortera == "tillverkare" || ($sortera == "" && CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']))) {

	if (preg_match("/pri_filter/i", $_SERVER['PHP_SELF'])) {
		$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.sortPriority DESC, beskrivning ASC ";
		// $criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.sortPriority DESC, LEFT(Artiklar.beskrivning, 5) ASC, utpris ASC ";
	} elseif (preg_match("/pri_digitalkameror/i", $_SERVER['PHP_SELF']) || preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF'])) {
		$criteria .= " ORDER BY Artiklar.sortPriority DESC, Artiklar.spec13 ASC, tillverkare ASC, beskrivning ASC ";
	} else {
		$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.sortPriority DESC, Artiklar.spec13 ASC, tillverkare ASC, beskrivning ASC ";
	}

} else {
	
	if (preg_match("/price_digital_cameras/i", $_SERVER['PHP_SELF']) || preg_match("/price_slr_cameras/i", $_SERVER['PHP_SELF'])) {
		if ($fi) {
			$criteria .= " ORDER BY Artiklar.spec13 ASC, utpris ASC, tillverkare DESC, beskrivning ASC ";
		} elseif ($no) {
			$criteria .= " ORDER BY Artiklar.spec13 ASC, utpris_no ASC, tillverkare DESC, beskrivning ASC ";
		} else {
			$criteria .= " ORDER BY Artiklar.spec13 ASC, utpris ASC, tillverkare DESC, beskrivning ASC ";
		}
	} else {
		if ($fi) {
			$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.spec13 ASC, utpris ASC, tillverkare DESC, beskrivning ASC ";
		} elseif ($no) {
			$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.spec13 ASC, utpris_no ASC, tillverkare DESC, beskrivning ASC ";
		} else {
			$criteria .= " ORDER BY Kategori.sortPriority DESC, kategori ASC, Artiklar.spec13 ASC, utpris ASC, tillverkare DESC, beskrivning ASC ";
		}
	}
	
}
if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nuX') {
	echo $criteria;
}
?>
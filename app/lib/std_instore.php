<?php
	if ($fi) {
		include ("std_instore_special_fi.php"); // här tar vi bort alla specialprodukter samt kategorier som Inte skall visas/säljas i Finland
		if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			$criteria .= " AND ((ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0)) OR (ej_med = -1 AND lagersaldo > 0)) AND demo = 0 ";
			/*
			if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_COOKIE['login_mail'] == 'borje@cyberphoto.nu') {
				$criteria .= " AND ((ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0)) OR (ej_med = -1 AND lagersaldo > 0)) AND demo = 0 ";
			} elseif ($sortera == "notshown") {
				// $criteria .= " AND ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH) ";
				$criteria .= " AND ej_med = -1 ";
			} else {
				$criteria .= " AND ej_med=0 AND ej_med_fi=0 AND demo = 0 AND (utgangen=0 OR lagersaldo > 0 ) ";
			}
			*/
		} else {
			$criteria .= " AND ej_med=0 AND ej_med_fi=0 AND demo = 0 AND (utgangen=0 OR lagersaldo > 0 ) ";
		}
	} elseif ($no) {
		include ("std_instore_special_no.php"); // här tar vi bort alla specialprodukter samt kategorier som Inte skall visas/säljas i Norge
		if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			$criteria .= " AND ((ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0)) OR (ej_med = -1 AND lagersaldo > 0)) AND demo = 0 ";
			/*
			if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_COOKIE['login_mail'] == 'borje@cyberphoto.nu') {
				$criteria .= " AND ((ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0)) OR (ej_med = -1 AND lagersaldo > 0)) AND demo = 0 ";
			} elseif ($sortera == "notshown") {
				// $criteria .= " AND ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH) ";
				$criteria .= " AND ej_med = -1 ";
			} else {
				$criteria .= " AND ej_med=0 AND ej_med_no=0 AND demo=0 AND (utgangen=0 OR lagersaldo > 0) ";
			}
			*/
		} else {
			$criteria .= " AND ej_med=0 AND ej_med_no=0 AND demo=0 AND (utgangen=0 OR lagersaldo > 0) ";
		}
	} else {
		if (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
			if ($sortera == "discontinued") {
				// $criteria .= " AND ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH) "; // Till 2015-10-05
				// $criteria .= " AND ((ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH)) OR (ej_med = -1 AND demo = -1 AND lagersaldo > 0)) ";
				$criteria .= " AND ej_med = 0 AND utgangen = -1 ";
			} else {
				$criteria .= " AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
			}
		} elseif (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			if ($sortera == "notshown") {
				// $criteria .= " AND ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH) "; // Till 2015-10-05
				// $criteria .= " AND ((ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH)) OR (ej_med = -1 AND demo = -1 AND lagersaldo > 0)) ";
				$criteria .= " AND ej_med = -1 AND utgangen = 0 ";
			} elseif ($sortera == "temporarynotinstore") {
				// $criteria .= " AND ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH) "; // Till 2015-10-05
				// $criteria .= " AND ((ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH)) OR (ej_med = -1 AND demo = -1 AND lagersaldo > 0)) ";
				$criteria .= " AND ej_med = 0 AND utgangen = 0 AND bestallningsgrans > 0 AND lagersaldo < 1 AND demo = 0 ";
			} elseif ($sortera == "notplaninstore") {
				// $criteria .= " AND ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH) "; // Till 2015-10-05
				// $criteria .= " AND ((ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH)) OR (ej_med = -1 AND demo = -1 AND lagersaldo > 0)) ";
				$criteria .= " AND ej_med = 0 AND utgangen = 0 AND bestallningsgrans < 1 AND demo = 0 ";
			} elseif ($sortera == "discontinued") {
				// $criteria .= " AND ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH) "; // Till 2015-10-05
				// $criteria .= " AND ((ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH)) OR (ej_med = -1 AND demo = -1 AND lagersaldo > 0)) ";
				$criteria .= " AND ej_med = 0 AND utgangen = -1 ";
			} elseif ($sortera == "old_tradein") {
				// $criteria .= " AND ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH) "; // Till 2015-10-05
				// $criteria .= " AND ((ej_med = -1 AND utgangen = 0 AND  Artiklar.date_add > DATE_SUB(NOW(), INTERVAL 2 MONTH)) OR (ej_med = -1 AND demo = -1 AND lagersaldo > 0)) ";
				$criteria .= " AND ej_med = 0 AND utgangen = -1 ";
			} else {
				// $criteria .= " AND ((ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0)) OR (ej_med = -1 AND utgangen = 0 AND Artiklar.kategori_id IN (1000290,1000292))) ";
				// $criteria .= " AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) "; // Till 2015-10-05
				if (CCheckIP::checkIfLoginIsTradeIn()) {
					$criteria .= " AND ((ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0)) OR (ej_med = -1 AND lagersaldo > 0)) ";
				} else {
					$criteria .= " AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
				}
			}
		} else {
			$criteria .= " AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
		}
	}
	
	$criteria .= "AND NOT (Artiklar.kategori_id IN (0,314,396,486,501,509,513)) ";
	
	/*
	if (preg_match("/prisjakt\.php/i", $_SERVER['PHP_SELF'])) {
		$criteria .= "AND NOT (Artiklar.isParent = -1) ";
	} elseif (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) && $sortera == "have_parent") {
		$criteria .= "AND NOT (Artiklar.isParent = -1) ";
	} else {
		////$criteria .= "AND (Artiklar.artnr_parent IS NULL OR Artiklar.artnr_parent = '') ";
		
		//$criteria .= " AND  (Artiklar.kategori_id IN (395, 1000265, 336, 393, 394) OR (Artiklar.artnr_parent IS NULL OR Artiklar.artnr_parent = '') ) ";
		// de som har varianter: 1000128, 1000129, 1000292, 1000398, 1000290, dvs kläder och skor
		$criteria .= " AND  (Artiklar.kategori_id NOT IN (1000128, 1000129, 1000292, 1000398, 1000290) OR (Artiklar.artnr_parent IS NULL OR Artiklar.artnr_parent = '') ) ";
	}
	*/


	if ($tillverkare != "") {
		$criteria .= "AND (Tillverkare.tillverkare = '$tillverkare' OR Artiklar.beskrivning LIKE '%$tillverkare%') ";
	}
	
	if (!CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) && ($fi || $no)) {
		$criteria .= "AND Artiklar.artnr = 'nomore' ";
	}
	if (!CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) { // säkra upp så att Ej prissatta demoprodukter inte syns på sidan.
		$criteria .= "AND NOT (Artiklar.demo = -1 AND Artiklar.utpris < 1) ";
	}

?>
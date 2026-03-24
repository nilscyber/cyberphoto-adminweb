<?php

/*

PHP login object
author		Stefan Sjöberg
version		1.0 2022-01-21

*/
include_once 'Db.php';
include("connections.php");
// include("connection_ad.php");

Class CAdminStat {

	var $conn_my;
	var $conn_ad;

	function __construct() {
		global $conn_ad;
		
		$this->conn_my = Db::getConnection();
		$this->conn_ad = Db::getConnectionAD();

	}

	function listNotRefinedProducts() {
		global $c_id, $qtyavailable;
	
		$desiderow = true;
		unset($groupday);
		unset($groupday2);
		$prod_linc = "http://www2.cyberphoto.se/info.php?article=";
		$countrow = 0;
	
		$select  = "SELECT p.value AS artnr, p.created, manu.name AS manu_name, p.name AS produktnamn, pstock.qtyavailable,  ";
		$select .= "pstock.qtyonhand, ad2.name AS kategoriansvarige, ad.name AS upplagdav, cat.salesrep_id, cat.name AS kategori, ";
		$select .= "cat.salesrep_id AS kat_ansvarig, p.iswebstoreproduct ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "JOIN m_product p ON pstock.m_product_id = p.m_product_id ";
		$select .= "JOIN xc_manufacturer manu ON p.xc_manufacturer_id = manu.xc_manufacturer_id ";
		$select .= "JOIN m_product_category cat ON p.m_product_category_id = cat.m_product_category_id ";
		$select .= "JOIN ad_user ad ON ad.ad_user_id = p.createdby ";
		$select .= "LEFT JOIN ad_user ad2 ON ad2.ad_user_id = cat.salesrep_id ";
		if ($old) {
			$select .= "WHERE p.isselfservice = 'N' AND p.discontinued = 'N' AND NOT p.demo_product = 'Y' ";
		} else {
			$select .= "WHERE p.isselfservice = 'N' AND p.discontinued = 'N' AND NOT p.demo_product = 'Y' ";
		}
		if ($qtyavailable != "yes") {
			$select .= "AND p.created > CURRENT_TIMESTAMP - INTERVAL '12 month' ";
		}
		if ($c_id != "") {
			$select .= "AND cat.salesrep_id = '" . $c_id . "' ";
		}
		if ($qtyavailable == "yes") {
			$select .= "AND pstock.qtyavailable > 0 ";
		}
		$select .= "ORDER BY p.created DESC ";
		
		// echo $select;
		

		// $res = mysqli_query($this->getConnectionDb(false), $select);
		$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $select) : false;
		// $res = $res;
		// $check = mysqli_num_rows($res);
	
		echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
		echo "<tr>";
		echo "<td class=\"bold align_left\" width=\"100\">Art nr</td>";
		echo "<td class=\"bold align_left\">Produkt</td>";
		echo "<td class=\"bold align_left\" width=\"150\">Kategori</td>";
		echo "<td class=\"bold align_left\" width=\"150\">Kategoriansvarig</td>";
		echo "<td class=\"bold align_left\" width=\"150\">Upplagd av</td>";
		echo "<td class=\"bold align_center\" width=\"80\">Tillgängligt</td>";
		echo "<td class=\"bold align_center\" width=\"80\">På hyllan</td>";
		echo "<td>&nbsp;</td>";
		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
			echo "<td>&nbsp;</td>";
		}
		echo "</tr>";
	
		if ($res !== false && pg_num_rows($res) > 0) {
			
			while ($res && $row = pg_fetch_object($res)) {
				
				// $row = $row;
					
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				$groupday = date("Y-m-d",strtotime($row->created));

				// if (date("Y-m-d",strtotime($row->updatetime)) == date("Y-m-d", time())) {
				if ($groupday != $groupday2) {
					if (date("Y-m-d",strtotime($row->created)) == date("Y-m-d", time())) {
						echo "<tr>";
						echo "<td colspan=\"12\" class=\"bold\">Idag</td>";
						echo "</tr>";
					} else {
						echo "<tr>";
						echo "<td colspan=\"12\" class=\"bold\">&nbsp;</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td colspan=\"12\" class=\"bold\">" . CDeparture::replace_days(date("l",strtotime($row->created))) . " " . date("Y-m-d",strtotime($row->created)) . "</td>";
						echo "</tr>";
					}
				}
				
				// echo date("Y-m-d",strtotime($row->updatetime)) . "-";
				// echo date("Y-m-d", strtotime(time()));
				
				echo "<tr>";
				echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr . "</td>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
				echo "\t\t<td class=\"$rowcolor align_left\">" . $row->kategori . "</td>";
				echo "\t\t<td class=\"$rowcolor align_left\"><a href=\"?c_id=$row->kat_ansvarig\">" . $row->kategoriansvarige . "</a></td>";
				echo "\t\t<td class=\"$rowcolor align_left\">" . $row->upplagdav . "</td>";
				echo "<td class=\"$rowcolor align_center\">" . $row->qtyavailable . "</td>";
				echo "<td class=\"$rowcolor align_center\">" . $row->qtyonhand . "</td>";
				if ($row->qtyavailable > 0) {
					echo "<td><a href=\"?qtyavailable=yes\"><img border=\"0\" src=\"star.png\"></a></td>";
				} else {
					echo "<td>&nbsp;</td>";
				}
					// echo "<td>&nbsp;</td>";
				if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
					if ($row->iswebstoreproduct == 'Y') {
						echo "<td><img border=\"0\" src=\"status_green.png\"></td>";
					} else {
						echo "<td><img border=\"0\" src=\"status_yellow.jpg\"></td>";
					}
				}
				echo "</tr>";
	
				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				
				$groupday2 = date("Y-m-d",strtotime($row->created));
				$countrow++;
				
	
			}
	
		} else {
		
			echo "<tr>";
			echo "<td colspan=\"12\" class=\"italic\">Inga produkter finns enligt filtreringen, TOPPEN!&nbsp;</td>";
			echo "</tr>";
		
		}
		if ($countrow > 0) {
			echo "<tr>";
			echo "<td colspan=\"12\">&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=\"12\">Totalt: <span class=\"bold\">" . $countrow . "&nbsp;</td>";
			echo "</tr>";
		}
	
		echo "</table>";
	
	}

	function listCommingProducts() {
		global $history;
	
		$desiderow = true;
		unset($groupday);
		unset($groupday2);
		$prod_linc = "https://www.cyberphoto.se/sok?q=";
		$prod_linc_www2 = "https://www2.cyberphoto.se/info.php?article=";
		$countrow = 0;
	
		$select  = "SELECT prod.launchdate, prod.created, manu.name AS manu_name, prod.name AS produktnamn, prod.value AS artnr, cat.name AS kategori,  ";
		$select .= "u.name AS upplagdav, prod.isselfservice, prod.manufacturerproductno ";
		$select .= "FROM m_product prod ";
		$select .= "JOIN ad_user u ON u.ad_user_id = prod.createdby ";
		$select .= "JOIN xc_manufacturer manu ON prod.xc_manufacturer_id = manu.xc_manufacturer_id ";
		$select .= "JOIN m_product_category cat ON prod.m_product_category_id = cat.m_product_category_id ";
		$select .= "WHERE NOT prod.demo_product = 'Y' AND NOT prod.discontinued = 'Y' ";

		// $select .= "AND NOT prod.launchdate > CURRENT_TIMESTAMP ";
		$select .= "AND prod.launchdate > CURRENT_TIMESTAMP ";

		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
			$select .= "ORDER BY prod.launchdate ASC ";
		} else {
			$select .= "ORDER BY prod.launchdate ASC ";
		}
		
		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.seX') {
			echo $select;
		}

		// $res = mysqli_query($this->getConnectionDb(false), $select);
		$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $select) : false;
		// $res = $res;
		// $check = mysqli_num_rows($res);
		
		if ($today && $res && pg_num_rows($res) < 1) {

			// echo "<div class=\"count_data bold italic\">Skapade produkterna idag</div>";
			echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
			
		} elseif ($today && $res && pg_num_rows($res) > 0) {

			echo "<div class=\"count_data bold italic\">Skapade produkterna idag</div>";
			echo "<table id=\"nytt_senaste\" cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
			
		} else {
	
			echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
			echo "<tr>";
			echo "<td class=\"bold align_left\" width=\"100\">Vårt artnr</td>";
			echo "<td class=\"bold align_left\" width=\"100\">Tillv artnr</td>";
			echo "<td class=\"bold align_left\">Produkt</td>";
			echo "<td class=\"bold align_left\" width=\"350\">Kategori</td>";
			// echo "<td class=\"bold align_left\" width=\"150\">Kategoriansvarig</td>";
			echo "<td class=\"bold align_left\" width=\"150\">Upplagd av</td>";
			// echo "<td class=\"bold align_center\" width=\"25\">&nbsp;</td>";
			// echo "<td class=\"bold align_center\" width=\"80\">På hyllan</td>";
			echo "<td>&nbsp;</td>";
			echo "</tr>";
			
		}
		
			if ($res && pg_num_rows($res) > 0) {
				
				while ($res && $row = pg_fetch_object($res)) {
					
					// $row = $row;
						
					if ($desiderow == true) {
						$rowcolor = "firstrow";
					} else {
						$rowcolor = "secondrow";
					}
					$groupday = date("Y-m-d",strtotime($row->launchdate));

					// if (date("Y-m-d",strtotime($row->updatetime)) == date("Y-m-d", time())) {
					if ($groupday != $groupday2 && !$today) {
						if (date("Y-m-d",strtotime($row->launchdate)) == date("Y-m-d", time())) {
							echo "<tr>";
							echo "<td colspan=\"12\" class=\"bold\">Idag</td>";
							echo "</tr>";
						} else {
							echo "<tr>";
							echo "<td colspan=\"12\" class=\"bold\">&nbsp;</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td colspan=\"12\" class=\"bold\">" . CDeparture::replace_days(date("l",strtotime($row->launchdate))) . " " . date("Y-m-d",strtotime($row->launchdate)) . "</td>";
							echo "</tr>";
						}
					}
					
					// echo date("Y-m-d",strtotime($row->updatetime)) . "-";
					// echo date("Y-m-d", strtotime(time()));

					echo "<tr>";
					echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr . "</td>";
					if ($row->manufacturerproductno == '') {
						echo "<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_red.gif\"></td>";
					} else {
						echo "\t\t<td class=\"$rowcolor align_left\">" . $row->manufacturerproductno . "</td>";
					}
					if ($row->isselfservice == 'Y') {
						echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
					} else {
						echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc_www2 . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
					}
					echo "\t\t<td class=\"$rowcolor align_left\">" . $row->kategori . "</td>";
					// echo "\t\t<td class=\"$rowcolor align_left\"><a href=\"?c_id=$row->kat_ansvarig\">" . $row->kategoriansvarige . "</a></td>";
					echo "\t\t<td class=\"$rowcolor align_left\">" . $row->upplagdav . "</td>";
					// echo "<td class=\"$rowcolor align_center\">" . $row->qtyavailable . "</td>";
					// echo "<td class=\"$rowcolor align_center\">" . $row->qtyonhand . "</td>";
					if ($row->isselfservice == 'Y') {
						echo "<td><img border=\"0\" src=\"status_green.png\"></td>";
					} else {
						echo "<td><img border=\"0\" src=\"status_red.png\"></td>";
					}
						// echo "<td>&nbsp;</td>";
					echo "</tr>";
		
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}
					
					$groupday2 = date("Y-m-d",strtotime($row->launchdate));
					$countrow++;
		
				}
		
			} else {
			
				if ($today) {
					echo "<tr>";
					echo "<td colspan=\"12\" class=\"italic\">&nbsp;</td>";
					echo "</tr>";
				} else {
					echo "<tr>";
					echo "<td colspan=\"12\" class=\"italic\">Inga produkter skapade idag!&nbsp;</td>";
					echo "</tr>";
				}
			
			}
			if ($countrow > 0 && !$today) {
				echo "<tr>";
				echo "<td colspan=\"12\">&nbsp;</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=\"12\">Totalt: <span class=\"bold\">" . $countrow . "&nbsp;</td>";
				echo "</tr>";
			}
		
			echo "</table>";

			if ($countrow > 0 && $today) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
	
	}

	function listNewProducts($today) {
		global $history;
	
		$desiderow = true;
		unset($groupday);
		unset($groupday2);
		$prod_linc = "https://www.cyberphoto.se/sok?q=";
		$prod_linc_www2 = "https://www2.cyberphoto.se/info.php?article=";
		$countrow = 0;
	
		$select  = "SELECT prod.launchdate, prod.created, manu.name AS manu_name, prod.name AS produktnamn, prod.value AS artnr, cat.name AS kategori,  ";
		$select .= "u.name AS upplagdav, prod.isselfservice, prod.manufacturerproductno, prod.iswebstoreproduct ";
		$select .= "FROM m_product prod ";
		$select .= "JOIN ad_user u ON u.ad_user_id = prod.createdby ";
		$select .= "JOIN xc_manufacturer manu ON prod.xc_manufacturer_id = manu.xc_manufacturer_id ";
		$select .= "JOIN m_product_category cat ON prod.m_product_category_id = cat.m_product_category_id ";
		$select .= "WHERE NOT prod.demo_product = 'Y' AND NOT prod.discontinued = 'Y' ";
		if ($history == "year") {
			$select .= "AND prod.created > CURRENT_TIMESTAMP - INTERVAL '12 month' ";
		} elseif ($history == "halfyear") {
			$select .= "AND prod.created > CURRENT_TIMESTAMP - INTERVAL '6 month' ";
		} elseif ($history == "month") {
			$select .= "AND prod.created > CURRENT_TIMESTAMP - INTERVAL '1 month' ";
		} elseif ($history == "today") {
			$select .= "AND prod.created > CURRENT_TIMESTAMP - INTERVAL '12 hours' ";
		} elseif ($today) {
			$select .= "AND prod.created > CURRENT_TIMESTAMP - INTERVAL '12 hours' ";
		} else {
			$select .= "AND prod.created > CURRENT_TIMESTAMP - INTERVAL '1 week' ";
		}

		// $select .= "AND NOT prod.launchdate > CURRENT_TIMESTAMP ";
		$select .= "AND prod.launchdate <= CURRENT_TIMESTAMP ";

		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
			$select .= "ORDER BY prod.created DESC ";
		} else {
			$select .= "AND prod.iswebstoreproduct = 'Y' ";
			$select .= "ORDER BY prod.created DESC ";
		}
		
		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.seX') {
			echo $select;
		}

		// $res = mysqli_query($this->getConnectionDb(false), $select);
		$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $select) : false;
		// $res = $res;
		// $check = mysqli_num_rows($res);
		
		if ($today && $res && pg_num_rows($res) < 1) {

			// echo "<div class=\"count_data bold italic\">Skapade produkterna idag</div>";
			echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
			
		} elseif ($today && $res && pg_num_rows($res) > 0) {

			echo "<div class=\"count_data bold italic\">Skapade produkterna idag</div>";
			echo "<table id=\"nytt_senaste\" cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
			
		} else {
	
			echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
			echo "<tr>";
			echo "<td class=\"bold align_left\" width=\"100\">Vårt artnr</td>";
			echo "<td class=\"bold align_left\" width=\"100\">Tillv artnr</td>";
			echo "<td class=\"bold align_left\">Produkt</td>";
			echo "<td class=\"bold align_left\" width=\"350\">Kategori</td>";
			// echo "<td class=\"bold align_left\" width=\"150\">Kategoriansvarig</td>";
			echo "<td class=\"bold align_left\" width=\"150\">Upplagd av</td>";
			// echo "<td class=\"bold align_center\" width=\"25\">&nbsp;</td>";
			// echo "<td class=\"bold align_center\" width=\"80\">På hyllan</td>";
			echo "<td>&nbsp;</td>";
			if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
				echo "<td>&nbsp;</td>";
			}
			echo "</tr>";
			
		}
		
			if ($res && pg_num_rows($res) > 0) {
				
				while ($res && $row = pg_fetch_object($res)) {
					
					// $row = $row;
						
					if ($desiderow == true) {
						$rowcolor = "firstrow";
					} else {
						$rowcolor = "secondrow";
					}
					$groupday = date("Y-m-d",strtotime($row->created));

					// if (date("Y-m-d",strtotime($row->updatetime)) == date("Y-m-d", time())) {
					if ($groupday != $groupday2 && !$today) {
						if (date("Y-m-d",strtotime($row->created)) == date("Y-m-d", time())) {
							echo "<tr>";
							echo "<td colspan=\"12\" class=\"bold\">Idag</td>";
							echo "</tr>";
						} else {
							echo "<tr>";
							echo "<td colspan=\"12\" class=\"bold\">&nbsp;</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td colspan=\"12\" class=\"bold\">" . CDeparture::replace_days(date("l",strtotime($row->created))) . " " . date("Y-m-d",strtotime($row->created)) . "</td>";
							echo "</tr>";
						}
					}
					
					// echo date("Y-m-d",strtotime($row->updatetime)) . "-";
					// echo date("Y-m-d", strtotime(time()));

				if ($today) {
					
					echo "<tr>";
					// echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr . "</td>";
					if ($row->isselfservice == 'Y') {
						echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
					} else {
						echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc_www2 . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
					}
					// echo "\t\t<td class=\"$rowcolor align_left\">" . $row->kategori . "</td>";
					// echo "\t\t<td class=\"$rowcolor align_left\"><a href=\"?c_id=$row->kat_ansvarig\">" . $row->kategoriansvarige . "</a></td>";
					// echo "\t\t<td class=\"$rowcolor align_left\">" . $row->upplagdav . "</td>";
					// echo "<td class=\"$rowcolor align_center\">" . $row->qtyavailable . "</td>";
					// echo "<td class=\"$rowcolor align_center\">" . $row->qtyonhand . "</td>";
					/*
					if ($row->isselfservice == 'Y') {
						echo "<td><img border=\"0\" src=\"status_green.png\"></td>";
					} else {
						echo "<td><img border=\"0\" src=\"status_red.png\"></td>";
					}
					*/
						// echo "<td>&nbsp;</td>";
					echo "</tr>";
		
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}
					
					$groupday2 = date("Y-m-d",strtotime($row->created));
					$countrow++;

				} else {

					echo "<tr>";
					echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr . "</td>";
					if ($row->manufacturerproductno == '') {
						echo "<td class=\"$rowcolor align_center\"><img border=\"0\" src=\"status_red.gif\"></td>";
					} else {
						echo "\t\t<td class=\"$rowcolor align_left\">" . $row->manufacturerproductno . "</td>";
					}
					if ($row->isselfservice == 'Y') {
						echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
					} else {
						echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc_www2 . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
					}
					echo "\t\t<td class=\"$rowcolor align_left\">" . $row->kategori . "</td>";
					// echo "\t\t<td class=\"$rowcolor align_left\"><a href=\"?c_id=$row->kat_ansvarig\">" . $row->kategoriansvarige . "</a></td>";
					echo "\t\t<td class=\"$rowcolor align_left\">" . $row->upplagdav . "</td>";
					// echo "<td class=\"$rowcolor align_center\">" . $row->qtyavailable . "</td>";
					// echo "<td class=\"$rowcolor align_center\">" . $row->qtyonhand . "</td>";
					if ($row->isselfservice == 'Y') {
						echo "<td><img border=\"0\" src=\"status_green.png\"></td>";
					} else {
						echo "<td><img border=\"0\" src=\"status_red.png\"></td>";
					}
					if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
						if ($row->iswebstoreproduct == 'Y') {
							echo "<td><img border=\"0\" src=\"status_green.png\"></td>";
						} else {
							echo "<td><img border=\"0\" src=\"status_yellow.jpg\"></td>";
						}
					}
						// echo "<td>&nbsp;</td>";
					echo "</tr>";
		
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}
					
					$groupday2 = date("Y-m-d",strtotime($row->created));
					$countrow++;
					
				}
					
		
				}
		
			} else {
			
				if ($today) {
					echo "<tr>";
					echo "<td colspan=\"12\" class=\"italic\">&nbsp;</td>";
					echo "</tr>";
				} else {
					echo "<tr>";
					echo "<td colspan=\"12\" class=\"italic\">Inga produkter skapade idag!&nbsp;</td>";
					echo "</tr>";
				}
			
			}
			if ($countrow > 0 && !$today) {
				echo "<tr>";
				echo "<td colspan=\"12\">&nbsp;</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=\"12\">Totalt: <span class=\"bold\">" . $countrow . "&nbsp;</td>";
				echo "</tr>";
			}
		
			echo "</table>";

			if ($countrow > 0 && $today) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
	
	}

	function listDiscontinuedProducts($today = null) {
		global $history;
	
		$desiderow = true;
		unset($groupday);
		unset($groupday2);
		$prod_linc = "https://www.cyberphoto.se/sok?q=";
		$prod_linc_www2 = "https://www2.cyberphoto.se/info.php?article=";
		$countrow = 0;
	
		$select  = "SELECT prod.discontinueddate, manu.name AS manu_name, prod.name AS produktnamn, prod.value AS artnr, cat.name AS kategori,  ";
		$select .= "u.name AS upplagdav, prod.isselfservice, prod.manufacturerproductno ";
		$select .= "FROM m_product prod ";
		$select .= "JOIN ad_user u ON u.ad_user_id = prod.createdby ";
		$select .= "JOIN xc_manufacturer manu ON prod.xc_manufacturer_id = manu.xc_manufacturer_id ";
		$select .= "JOIN m_product_category cat ON prod.m_product_category_id = cat.m_product_category_id ";
		$select .= "WHERE NOT prod.demo_product = 'Y' ";
		if ($history == "year") {
			$select .= "AND prod.discontinueddate > CURRENT_TIMESTAMP - INTERVAL '12 month' ";
		} elseif ($history == "halfyear") {
			$select .= "AND prod.discontinueddate > CURRENT_TIMESTAMP - INTERVAL '6 month' ";
		} elseif ($history == "month") {
			$select .= "AND prod.discontinueddate > CURRENT_TIMESTAMP - INTERVAL '1 month' ";
		} elseif ($history == "today") {
			$select .= "AND prod.discontinueddate > CURRENT_TIMESTAMP - INTERVAL '12 hours' ";
		} elseif ($today) {
			$select .= "AND prod.discontinueddate > CURRENT_TIMESTAMP - INTERVAL '12 hours' ";
		} else {
			$select .= "AND prod.discontinueddate > CURRENT_TIMESTAMP - INTERVAL '1 week' ";
		}
		$select .= "ORDER BY prod.discontinueddate DESC ";
		
		// echo $select;

		// $res = mysqli_query($this->getConnectionDb(false), $select);
		$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $select) : false;
		// $res = $res;
		// $check = mysqli_num_rows($res);
		
		if ($today && $res && pg_num_rows($res) < 1) {

			// echo "<div class=\"count_data bold italic\">Skapade produkterna idag</div>";
			echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
			
		} elseif ($today && $res && pg_num_rows($res) > 0) {

			echo "<div class=\"count_data bold italic\">Utgångna produkterna idag</div>";
			echo "<table id=\"begg_senaste\" cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
			
		} else {
	
			echo "<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">";
			echo "<tr>";
			echo "<td class=\"bold align_left\" width=\"100\">Vårt artnr</td>";
			echo "<td class=\"bold align_left\">Produkt</td>";
			echo "<td class=\"bold align_left\" width=\"350\">Kategori</td>";
			// echo "<td class=\"bold align_left\" width=\"150\">Kategoriansvarig</td>";
			// echo "<td class=\"bold align_left\" width=\"150\">Upplagd av</td>";
			// echo "<td class=\"bold align_center\" width=\"25\">&nbsp;</td>";
			// echo "<td class=\"bold align_center\" width=\"80\">På hyllan</td>";
			// echo "<td>&nbsp;</td>";
			echo "</tr>";
			
		}
		
			if ($res && pg_num_rows($res) > 0) {
				
				while ($res && $row = pg_fetch_object($res)) {
					
					// $row = $row;
						
					if ($desiderow == true) {
						$rowcolor = "firstrow";
					} else {
						$rowcolor = "secondrow";
					}
					$groupday = date("Y-m-d",strtotime($row->discontinueddate));

					// if (date("Y-m-d",strtotime($row->updatetime)) == date("Y-m-d", time())) {
					if ($groupday != $groupday2 && !$today) {
						if (date("Y-m-d",strtotime($row->discontinueddate)) == date("Y-m-d", time())) {
							echo "<tr>";
							echo "<td colspan=\"12\" class=\"bold\">Idag</td>";
							echo "</tr>";
						} else {
							echo "<tr>";
							echo "<td colspan=\"12\" class=\"bold\">&nbsp;</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td colspan=\"12\" class=\"bold\">" . CDeparture::replace_days(date("l",strtotime($row->discontinueddate))) . " " . date("Y-m-d",strtotime($row->discontinueddate)) . "</td>";
							echo "</tr>";
						}
					}
					
					// echo date("Y-m-d",strtotime($row->updatetime)) . "-";
					// echo date("Y-m-d", strtotime(time()));

				if ($today) {
					
					echo "<tr>";
					// echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr . "</td>";
					if ($row->isselfservice == 'Y') {
						echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
					} else {
						echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc_www2 . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
					}
					// echo "\t\t<td class=\"$rowcolor align_left\">" . $row->kategori . "</td>";
					// echo "\t\t<td class=\"$rowcolor align_left\"><a href=\"?c_id=$row->kat_ansvarig\">" . $row->kategoriansvarige . "</a></td>";
					// echo "\t\t<td class=\"$rowcolor align_left\">" . $row->upplagdav . "</td>";
					// echo "<td class=\"$rowcolor align_center\">" . $row->qtyavailable . "</td>";
					// echo "<td class=\"$rowcolor align_center\">" . $row->qtyonhand . "</td>";
					/*
					if ($row->isselfservice == 'Y') {
						echo "<td><img border=\"0\" src=\"status_green.png\"></td>";
					} else {
						echo "<td><img border=\"0\" src=\"status_red.png\"></td>";
					}
					*/
						// echo "<td>&nbsp;</td>";
					echo "</tr>";
		
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}
					
					$groupday2 = date("Y-m-d",strtotime($row->discontinueddate));
					$countrow++;

				} else {

					echo "<tr>";
					echo "\t\t<td class=\"$rowcolor align_left\">" . $row->artnr . "</td>";
					if ($row->isselfservice == 'Y') {
						echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
					} else {
						echo "\t\t<td class=\"$rowcolor align_left\"><a target=\"_blank\" href=\"" . $prod_linc_www2 . $row->artnr . "\">" . $row->manu_name . " " . $row->produktnamn ."</a></td>";
					}
					echo "\t\t<td class=\"$rowcolor align_left\">" . $row->kategori . "</td>";
					// echo "\t\t<td class=\"$rowcolor align_left\"><a href=\"?c_id=$row->kat_ansvarig\">" . $row->kategoriansvarige . "</a></td>";
					// echo "\t\t<td class=\"$rowcolor align_left\">" . $row->upplagdav . "</td>";
					// echo "<td class=\"$rowcolor align_center\">" . $row->qtyavailable . "</td>";
					// echo "<td class=\"$rowcolor align_center\">" . $row->qtyonhand . "</td>";
					/*
					if ($row->isselfservice == 'Y') {
						echo "<td><img border=\"0\" src=\"status_green.png\"></td>";
					} else {
						echo "<td><img border=\"0\" src=\"status_red.png\"></td>";
					}
					*/
						// echo "<td>&nbsp;</td>";
					echo "</tr>";
		
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}
					
					$groupday2 = date("Y-m-d",strtotime($row->discontinueddate));
					$countrow++;
					
				}
					
		
				}
		
			} else {
			
				if ($today) {
					echo "<tr>";
					echo "<td colspan=\"12\" class=\"italic\">&nbsp;</td>";
					echo "</tr>";
				} else {
					echo "<tr>";
					echo "<td colspan=\"12\" class=\"italic\">Inga produkter skapade idag!&nbsp;</td>";
					echo "</tr>";
				}
			
			}
			if ($countrow > 0 && !$today) {
				echo "<tr>";
				echo "<td colspan=\"12\">&nbsp;</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=\"12\">Totalt: <span class=\"bold\">" . $countrow . "&nbsp;</td>";
				echo "</tr>";
			}
		
			echo "</table>";

			if ($countrow > 0 && $today) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
	
	}

	
}
?>

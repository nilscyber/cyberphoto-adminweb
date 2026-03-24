<?php
require_once("CCheckIpNumber.php");
require_once("Db.php");


Class CBusinessPartner {
	// var $conn_my; 

	function __construct() {
	
		// $this->conn_my = @mysqli_connect(getenv('DB_HOST') ?: 'db', getenv('DB_USER') ?: 'appuser', getenv('DB_PASS') ?: 'apppass');
		// @mysqli_select_db($this->conn_my, getenv('DB_NAME') ?: 'cyberphoto');
	
	}

	function getResultSearch($q,$o = null) {
		
		$rowcolor = true;
		$start_count = 0;

		echo "<div class=\"left10 top20\">\n";
		// echo "<left>\n";
		echo "<table class=\"searchtable\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"100%\">\n";
		
		$searchwords = preg_split("/[\s]+/", $q);

		$select = "SELECT k.kundnr, k.namn, k.postadr, k.email, k.lland_id ";
		$select .= "FROM cyberorder.Kund k ";
		// $select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		// $select .= "LEFT JOIN Moms on Artiklar.momskod = Moms.moms_id ";
		// $select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
		$select .= "WHERE ";

		$select .= "(";

		for ($i = 0; $i < count($searchwords);$i++) {

				if ($i == 0) {
					$select .= "kundnr like '%" . $searchwords[$i] . "%' OR namn like '%" . $searchwords[$i] . "%' OR co like '%" . $searchwords[$i] . "%' OR adress like '%" . $searchwords[$i] . "%' OR postnr like '%" . $searchwords[$i] . "%' OR postadr like '%" . $searchwords[$i] . "%' OR email like '%" . $searchwords[$i] . "%' OR mobilnr like '%" . $searchwords[$i] . "%' OR telnr like '%" . $searchwords[$i] . "%' OR userName like '%" . $searchwords[$i] . "%' OR orgnr like '%" . $searchwords[$i] . "%') ";
				} else {
					$select .= "AND (kundnr like '%" . $searchwords[$i] . "%' OR namn like '%" . $searchwords[$i] . "%' OR co like '%" . $searchwords[$i] . "%' OR adress like '%" . $searchwords[$i] . "%' OR postnr like '%" . $searchwords[$i] . "%' OR postadr like '%" . $searchwords[$i] . "%' OR email like '%" . $searchwords[$i] . "%' OR mobilnr like '%" . $searchwords[$i] . "%' OR telnr like '%" . $searchwords[$i] . "%' OR userName like '%" . $searchwords[$i] . "%' OR orgnr like '%" . $searchwords[$i] . "%') ";
				}
		}
		

		$select .= "ORDER BY date_upd DESC, k.kundnr ASC ";

		$select .= "LIMIT 500 ";

		if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $select;
		}
		
		$res = mysqli_query(Db::getConnection(), $select);
		/*
		mysqli_query('SET character_set_results=utf8');
		mysqli_query('SET names=utf8');  
		mysqli_query('SET character_set_client=utf8');
		mysqli_query('SET character_set_connection=utf8');   
		mysqli_query('SET character_set_results=utf8');   
		mysqli_query('SET collation_connection=utf8_general_ci'); 
		*/
		
		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_object($res)) {
			

				if ($rowcolor) {
					$backcolor = "search_line1";
				} else {
					$backcolor = "search_line2";
				}
				
				if ($start_count == 0) {
					echo "<tr>";
					echo "<td width=\"80\" class=\"table_artnr bold underline\">Kundnr</td>\n";
					echo "<td class=\"table_lagersaldo\">&nbsp;</td>\n";
					echo "<td class=\"table_lagersaldo bold underline\">Namn</td>\n";
					echo "<td class=\"table_lagersaldo bold underline\">Ort</td>\n";
					echo "<td class=\"table_lagersaldo bold underline\">E-post</td>\n";
					echo "<td width=\"100\" class=\"table_lagersaldo bold underline align_center\">Senaste order</td>\n";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td class=\"$backcolor table_artnr\">" . ($row->kundnr) . "</td>\n";
				if ($row->lland_id == 358 || $row->lland_id == 999) {
					echo "<td class=\"align_center\"><img border=\"\" src=\"https://admin.cyberphoto.se/fi_mini.jpg\"></td>\n";
				} elseif ($row->lland_id == 47) {
					echo "<td class=\"align_center\"><img border=\"\" src=\"https://admin.cyberphoto.se/no_mini.jpg\"></td>\n";
				} else {
					echo "<td class=\"align_center\"><img border=\"\" src=\"https://admin.cyberphoto.se/sv_mini.jpg\"></td>\n";
				}
				// echo "<td class=\"$backcolor table_lagersaldo\"><a class=\"$bold_order\" href=\"javascript:winPopupCenter(600, 350, '/order/customer_info.php?knr=" . $row->kundnr . "');\">" . ($row->namn) . "</a></td>\n";
				echo "<td class=\"$backcolor table_lagersaldo\"><a class=\"$bold_order\" href=\"javascript:winPopupCenter(600, 350, 'https://admin.cyberphoto.se/customer_info.php?knr=" . $row->kundnr . "');\">" . ($row->namn) . "</a></td>\n";
				echo "<td class=\"$backcolor table_lagersaldo\">" . ($row->postadr) . "</td>\n";
				echo "<td class=\"$backcolor table_lagersaldo\">" . ($row->email) . "</td>\n";
				echo "<td class=\"$backcolor table_lagersaldo align_center\">" . ($this->getLatestOrder($row->kundnr)) . "</td>\n";
				echo "</tr>";

				if ($rowcolor) {
					$rowcolor = false;
				} else {
					$rowcolor = true;
				}
				$start_count++;

			}


		} else {
		
			echo "<tr>\n";
			echo ("<td colspan=\"10\" class=\"line1 table_lagersaldo\">Tyvärr gav sökningen inga träffar</td>\n");
			echo "</tr>\n";

		}
		
		/*
		echo "<tr>\n";
		echo "<td colspan=\"10\" class=\"line1 table_lagersaldo\">Sökningen gav $start_count träffar</td>\n";
		echo "</tr>\n";
		*/
		echo "</table>\n";
		echo "<div class=\"left5 top20 bottom10\">Sökningen gav<b> $start_count st</b> träffar</div>\n";
		
		echo "</div>\n";
		
	}

	function getAllCustomerInfo($knr) {
		
		$rowcolor = true;
		$start_count = 0;

		echo "<div class=\"left10 top20\">\n";
		echo "<table class=\"searchtable\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"100%\">\n";
		
		$select  = "SELECT k.* ";
		$select .= "FROM cyberorder.Kund k ";
		$select .= "WHERE kundnr = '" . $knr . "' ";

		// echo $select;
		
		$res = mysqli_query(Db::getConnection(), $select);
		
		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_object($res)) {
				// var_dump($row);
			
				/*
				if ($rowcolor) {
					$backcolor = "search_line1";
				} else {
					$backcolor = "search_line2";
				}
				*/
				
				echo "<tr>";
				// echo "<td width=\"100\" class=\"table_lagersaldo\">&nbsp;</td>\n";
				echo "<td class=\"table_lagersaldo\">&nbsp;</td>\n";
				echo "</tr>";
				
				echo "<tr>";
				// echo "<td class=\"table_lagersaldo\">Land:</td>\n";
				if ($row->lland_id == 358 || $row->lland_id == 999) {
					echo "<td class=\"align_center\"><img border=\"\" src=\"/order/admin/fi_mini.jpg\"></td>\n";
				} elseif ($row->lland_id == 47) {
					echo "<td class=\"align_center\"><img border=\"\" src=\"/order/admin/no_mini.jpg\"></td>\n";
				} else {
					echo "<td class=\"align_center\"><img border=\"\" src=\"sv_mini.jpg\"></td>\n";
				}
				echo "</tr>";
				if ($row->foretag == -1) {
					echo "<tr>";
					echo "<td class=\"bold table_artnr\">" . ($row->kundnr) . " - <b><i>Företag</i></b></td>\n";
					echo "</tr>";
				} else {
					echo "<tr>";
					echo "<td class=\"bold table_artnr\">" . ($row->kundnr) . " - <i>Privat</i></td>\n";
					echo "</tr>";
				}
				echo "<tr>";
				// echo "<td class=\"table_lagersaldo\">Namn:</td>\n";
				echo "<td class=\"table_artnr\">" . $row->namn . "</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"table_artnr\">" . $row->email . "</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"table_artnr\">" . $row->telnr . "</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"table_artnr\">" . $row->mobilnr . "</td>\n";
				echo "</tr>";
				if ($row->orgnr != "") {
					echo "<tr>";
					echo "<td class=\"table_artnr\">" . $row->orgnr . " (personnummer)</td>\n";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td class=\"table_artnr\">&nbsp;</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"bold table_artnr\">Adress</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"table_artnr\">" . $row->co . "</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"table_artnr\">" . $row->adress . "</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"table_artnr\">" . $row->postnr . " " . $row->postadr . "</td>\n";
				echo "</tr>";
				echo "<td class=\"table_artnr\">&nbsp;</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"bold table_artnr\">Leveransadress</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"table_artnr\">" . $row->lco . "</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"table_artnr\">" . $row->ladress . "</td>\n";
				echo "</tr>";
				echo "<tr>";
				echo "<td class=\"table_artnr\">" . $row->lpostnr . " " . $row->lpostadr . "</td>\n";
				echo "</tr>";
				echo "</tr>";
				echo "<td class=\"table_artnr\">&nbsp;</td>\n";
				echo "</tr>";
				if ($row->userName != "") {
					echo "</tr>";
					echo "<td class=\"bold table_artnr\">Användarnamn (web):</td>\n";
					echo "</tr>";
					echo "<tr>";
					echo "<td class=\"table_artnr\">" . $row->userName . "</td>\n";
					echo "</tr>";
				}

				if ($rowcolor) {
					$rowcolor = false;
				} else {
					$rowcolor = true;
				}
				$start_count++;

			}


		} else {
		
			echo "<tr>\n";
			echo ("<td colspan=\"10\" class=\"line1 table_lagersaldo\">Tyvärr gav sökningen inga träffar</td>\n");
			echo "</tr>\n";

		}
		
		echo "</table>\n";
		// echo "<div class=\"left5 top20 bottom10\">Sökningen gav<b> $start_count st</b> träffar</div>\n";
		
		echo "</div>\n";
		
	}
	
	function getLatestOrder($bp) {
		

		$select = "SELECT MAX(inkommet) AS senast_handlat ";
		$select .= "FROM cyberphoto.Ordertabell ";
		$select .= "WHERE (docstatus = 'CO' OR docstatus = 'IP') AND c_doctype_id = 1000030 AND kundnr = '" . $bp . "' ";
		// echo $select . "<br>";
		
		$res = mysqli_query(Db::getConnection(), $select);
		
		if (mysqli_num_rows($res) > 0) {
		$row = mysqli_fetch_object($res);
			
			if ($row->senast_handlat != NULL) {
				return date("Y-m-d", strtotime($row->senast_handlat));
			} else {
				return $this->getLatestOrder_gamla($bp);
			}

		}
		
	}

	function getLatestOrder_gamla($bp) {

		$select = "SELECT MAX(inkommet) AS senast_handlat ";
		$select .= "FROM cyberorder.Ordertabell_gamla ";
		$select .= "WHERE NOT (skickat IS NULL) AND kundnr = '" . $bp . "' ";
		// echo $select . "<br>";
		
		$res = mysqli_query(Db::getConnection(), $select);
		
		if (mysqli_num_rows($res) > 0) {
		$row = mysqli_fetch_object($res);
			
			if ($row->senast_handlat != NULL) {
				return date("Y-m-d", strtotime($row->senast_handlat));
			} else {
				return "-";
			}

		}
		
	}

	function showCustomerOrderLight($ordernr) {
	
		echo "<div class=\"left10 top20 bottom20\">\n";
		echo "<table class=\"searchtable\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"100%\">\n";
			echo "\t<tr>\n";
			// echo "\t\t<td width=\"25\">&nbsp;</td>\n";
			echo "\t\t<td width=\"120\"><b>Skapad</b></td>\n";
			echo "\t\t<td align=\"center\" width=\"80\"><b>Order nr</b></td>\n";
			echo "\t\t<td align=\"center\" width=\"80\"><b>Kund nr</b></td>\n";
			echo "\t\t<td align=\"center\" width=\"50\">&nbsp;</td>\n";
			echo "\t\t<td width=\"300\"><b>Namn</b></td>\n";
			echo "\t\t<td align=\"center\"><b>Total ordersumma</b></td>\n";
			echo "\t\t<td align=\"center\">&nbsp;</td>\n";
			echo "\t\t<td align=\"center\">&nbsp;</td>\n";
			// echo "\t\t<td width=\"125\">&nbsp;</td>\n";
			echo "\t</tr>\n";
			
			$select  = "SELECT o.created, o.documentno, bp.name, loc.c_country_id, o.grandtotal, o.order_url, bp.value ";
			$select .= "FROM c_order o ";
			$select .= "JOIN c_bpartner bp ON o.c_bpartner_id = bp.c_bpartner_id ";
			$select .= "JOIN c_bpartner_location bpl ON bpl.c_bpartner_location_id = o.c_bpartner_location_id ";
			$select .= "JOIN c_location loc ON loc.c_location_id = bpl.c_location_id ";
			$select .= "JOIN c_country con ON con.c_country_id = loc.c_country_id ";
			// $select .= "JOIN c_country con ON con.c_country_id = loc.c_country_id ";
			$select .= "WHERE o.c_doctype_id = 1000030 AND o.docstatus NOT IN ('VO', 'RE') ";
			$select .= "AND (o.documentno = '$ordernr' OR bp.value = '$ordernr') ";
			$select .= "ORDER BY o.created DESC ";
			
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				// exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			// $row = pg_fetch_object($res);

				if ($res && pg_num_rows($res) > 0) {
				
					while ($res && $row = pg_fetch_object($res)) {
						
						if ($ordernr == $row->documentno) {
							$bold_order = "mark_blue span_link";
						} else {
							$bold_order = "span_link";
						}
						if ($ordernr == $row->value) {
							$bold_knr = "mark_blue";
						} else {
							$bold_knr = "";
						}
						// $row = $row;
						echo "\t<tr>";
						// echo "\t\t<td>$countrow</td>\n";
						// echo "\t\t<td>" . date("Y-m-d H:i",strtotime($row[0])) . "</td>\n";
							echo "\t\t<td>" . date("Y-m-d",strtotime($row->created)) . "</td>\n";
							// echo "\t\t<td align=\"center\"><a style=\"text-decoration: none;\" href=\"order_info.php?order=" . $row->documentno . "&artnr=$artnr&salda=yes\"> " . $row->documentno . "</a></td>\n";
							// echo "\t\t<td align=\"center\"><a class=\"$bold_order\" href=\"javascript:winPopupCenter(500, 1000, '/order/order_info.php?order=" . $row->documentno . "');\">" . $row->documentno . "</a></td>\n";
							echo "\t\t<td align=\"center\"><a class=\"$bold_order\" href=\"javascript:winPopupCenter(500, 1000, 'https://admin.cyberphoto.se/order_info.php?order=" . $row->documentno . "');\">" . $row->documentno . "</a></td>\n";
							echo "\t\t<td align=\"center\" class=\"$bold_knr\">" . $row->value . "</td>\n";
							if ($row->c_country_id == 181 || $row->c_country_id == 50000) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"https://admin.cyberphoto.se/fi_mini.jpg\"></td>\n";
								$land = "fi";
								$currency = "EUR";
							} elseif ($row->c_country_id == 167) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"https://admin.cyberphoto.se/dk_mini.jpg\"></td>\n";
								$land = "se";
								$currency = "SEK";
							} elseif ($row->c_country_id == 269) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"https://admin.cyberphoto.se/no_mini.jpg\"></td>\n";
								$land = "no";
								$currency = "NOK";
							} else {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"https://admin.cyberphoto.se/sv_mini.jpg\"></td>\n";
								$land = "se";
								$currency = "SEK";
							}
							echo "\t\t<td>" . $row->name . "</td>\n";
							echo "\t\t<td align=\"center\">" . number_format($row->grandtotal, 0, ',', ' ') . " ". $currency . "</td>\n";
							if ($row->c_country_id == 181 || $row->c_country_id == 50000) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"https://admin.cyberphoto.se/fi_mini.jpg\"></td>\n";
							} elseif ($row->c_country_id == 167) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"https://admin.cyberphoto.se/dk_mini.jpg\"></td>\n";
							} elseif ($row->c_country_id == 269) {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"https://admin.cyberphoto.se/no_mini.jpg\"></td>\n";
							} else {
								echo "\t\t<td align=\"center\"><img border=\"\" src=\"https://admin.cyberphoto.se/sv_mini.jpg\"></td>\n";
							}
							if ($row->order_url != "") {
								// echo utf8_encode("\t\t<td align=\"center\"><a target=\"_blank\" href=\"https://www.cyberphoto." . $land . "/kundvagn/min-ordrestatus?orderref=" . $row->order_url . "&order_check=" . $row->documentno . "\">Kundlänk</a></td>\n");
								echo "\t\t<td align=\"center\"><a target=\"_blank\" href=\"https://www2.cyberphoto." . $land . "/kundvagn/min-ordrestatus?orderref=" . $row->order_url . "&order_check=" . $row->documentno . "\">Kundlänk</a></td>\n";
							} else {
								echo "\t\t<td align=\"center\">&nbsp;</td>\n";
							}
							// echo "\t\t<td align=\"center\">Skickad</td>\n";
						echo "\t</tr>\n";
						
						if ($displaypac) {
							$countrow++;
						}

					}
					
				} else {
				
						echo "\t<tr>\n";
						// echo "\t\t<td width=\"25\">&nbsp;</td>\n";
						echo "\t\t<td colspan=\"4\"><i>Ingen order hittade med detta ordernr...</i></td>\n";
						echo "\t</tr>\n";
				
				}
			
		echo "</table>\n";
		echo "</div>\n";
		
	}

	
}
?>

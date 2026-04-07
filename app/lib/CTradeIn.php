<?php

/*

PHP login object
author		Stefan Sj�berg
version		1.0 2018-11-09

*/

include_once 'Db.php';
include_once 'CWebADInternSuplier.php';

Class CTradeIn {

	function __construct() {
		
	}

	function replace_char($string) {
			$from = array("Mon", "Tue", "Wen", "Thu", "Fri", "Sat", "Sun");
			$to = array("Mån", "Tis", "Ons", "Tor", "Fre", "Lör", "Sön");
			// return str_replace($from, $to, $string);
			return str_replace($from, $to, $string);
	}
	
	function dayFromCreated($datefrom) {
		$now = time(); // or your date as well
		$your_date = strtotime($datefrom);
		$datediff = $now - $your_date;
		$datediff2 = floor($datediff / (60 * 60 * 24));
		
		return $datediff2;
	}

	function lastBookedTradeIn() {
		global $dagensdatum;
		
		// $dagensdatum = "2018-11-05";
		$specialdatum = date("Y-m-d", time());
		$dagensdatum_notify = date("Y-m-d", time());

		$countrow = 0;

		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d H:i:s", time());
		}
		// $dagensdatum = date("Y-m-d", time());
		// $look_forward = date('Y-m-d', strtotime("$dagensdatum +3 day"));
		$look_backward = date('Y-m-d H:i:s', strtotime("$dagensdatum -1 day"));
		
			
		$select = "SELECT DISTINCT col.created, t.name as tillverkare, p.name as beskrivning, p.value as artnr, col.qtyallocated, col.qtyordered, col.qtyreserved, col.qtydelivered ";
		$select .= ", col.c_tax_id, o.salesrep_id ";
		$select .= "FROM c_orderline col ";
		$select .= "JOIN c_order o ON o.c_order_id = col.c_order_id ";
		$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
		$select .= "JOIN m_product_po po ON p.m_product_id = po.m_product_id ";
		$select .= "JOIN xc_manufacturer t ON t.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = po.c_bpartner_id ";
		$select .= "JOIN m_locator mloc ON mloc.m_locator_id = p.m_locator_id ";
		$select .= "WHERE o.docstatus IN ('CO','IP') AND o.deliveryViaRule IN ('S','P') AND o.isSOTrx = 'Y' AND o.isActive='Y' AND o.AD_Client_ID=1000000 ";
		// $select .= "AND date(col.updated)>='$dagensdatum 00:00:00' AND date(col.updated)<='$dagensdatum 23:59:59' ";
		// $select .= "AND date(col.created) >= '$look_backward' ";
		if ($specialdatum == "2018-12-27") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '6 days' ";
		} elseif ($specialdatum == "2022-12-27") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '4 days' ";
		} elseif ($specialdatum == "2023-12-27") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '5 days' ";
		} elseif ($specialdatum == "2023-04-11") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '5 days' ";
		} elseif ($specialdatum == "2023-05-19") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '2 days' ";
		} elseif ($specialdatum == "2024-01-02") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '3 days' ";
		} elseif ($specialdatum == "2024-04-02") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '5 days' ";
		} elseif ($specialdatum == "2024-05-02") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '2 days' ";
		} elseif ($specialdatum == "2024-05-10") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '2 days' ";
		} elseif ($specialdatum == "2024-06-07") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '2 days' ";
		} elseif ($specialdatum == "2024-06-24") {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '4 days' ";
		} elseif (date('N') == 1) {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '3 days' ";
		} else {
			$select .= "AND col.created > CURRENT_TIMESTAMP - INTERVAL '1 days' ";
		}
		// $select .= "AND bp.value = '5555' AND (col.qtyallocated > 0 OR col.qtydelivered > 0) ";
		$select .= "AND bp.value = '5555' AND NOT (col.qtyordered <> col.qtyallocated) AND col.qtydelivered = 0 AND col.qtyordered > 0 ";
		$select .= "AND NOT mloc.m_locator_id IN (1004179) ";  // tar bor om vi bokat upp som testkamera = IBT
		$select .= "AND NOT p.value IN ('sensorcleaning','services','filter','valuation','updmjukvara','service123') ";
		$select .= "AND NOT p.value IN ('dagshyra','dagshyra_objektiv','Dagshyra_system','veckohyra','veckohyra_objektiv') ";  // tar bort hyra
		// $select .= "AND NOT p.value IN ('filter') ";  // tar bort diverse
		// $select .= "AND NOT p.value IN ('dagshyra','dagshyra_objektiv','Dagshyra_system','veckohyra','veckohyra_objektiv') ";  // tar bort hyra
		$select .= "AND NOT o.bill_bpartner_id IN (1013455,1013492) ";  // tar bort ordrar inbytesg�nget
		$select .= "ORDER BY col.created DESC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// echo "dag: " . date('N');
			// exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}
		
		
		if ($res && pg_num_rows($res) > 0) {
		
			if ($specialdatum == "2018-12-27") {
				echo "<div class=\"count_data bold italic\">Uppbokade senaste 6 dygnen, ej skickat</div>\n";
			} elseif ($specialdatum == "2023-04-11") {
				echo "<div class=\"count_data bold italic\">Uppbokade senaste 5 dygnen, ej skickat</div>\n";
			} elseif ($specialdatum == "2023-12-27") {
				echo "<div class=\"count_data bold italic\">Uppbokade senaste 5 dygnen, ej skickat</div>\n";
			} elseif ($specialdatum == "2023-05-19") {
				echo "<div class=\"count_data bold italic\">Uppbokade senaste 2 dygnen, ej skickat</div>\n";
			} elseif ($specialdatum == "2024-01-02") {
				echo "<div class=\"count_data bold italic\">Uppbokade senaste 3 dygnen, ej skickat</div>\n";
			} elseif ($specialdatum == "2024-04-02") {
				echo "<div class=\"count_data bold italic\">Uppbokade senaste 5 dygnen, ej skickat</div>\n";
			} elseif ($specialdatum == "2024-06-24") {
				echo "<div class=\"count_data bold italic\">Uppbokade senaste 4 dygnen, ej skickat</div>\n";
			} elseif (date('N') == 1) {
				echo "<div class=\"count_data bold italic\">Uppbokade senaste 3 dygnen, ej skickat</div>\n";
			} else {
				echo "<div class=\"count_data bold italic\">Uppbokade senaste dygnet, ej skickat</div>\n";
			}
			echo "<table id=\"begg_booked\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($res && $row = pg_fetch_object($res)) {

				$trimmaprodukten = $row->tillverkare . " " . $row->beskrivning;
				
				if (strlen($trimmaprodukten) >= 42)
					$trimmaprodukten = substr ($trimmaprodukten, 0, 42) . "...";
				
				$time_since = (time() - strtotime($row->created));
				$booked_date_short = date("Y-m-d", strtotime($row->created));
				
				if ($dagensdatum_notify == $booked_date_short && $_SERVER['REMOTE_ADDR'] != "192.168.1.89") {
					$this->addNotify($row->artnr,$trimmaprodukten,2);
				}
				
				// if ($time_since < 30 && $countrow == 0) {
				if ($time_since < 30 && $countrow == 0 && $no == "more") {
					echo "<audio autoplay>\n";
					// echo "\t<source src=\"pump_shotgun.mp3\" type=\"audio/mpeg\">\n";
					if ($row->c_tax_id == 1000000) {
						echo "\t<source src=\"cash_register2.mp3\" type=\"audio/mpeg\">\n";
					} else {
						echo "\t<source src=\"woohoo.mp3\" type=\"audio/mpeg\">\n";
					}
					echo "</audio>\n";
				}
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
					// echo $time_since;
					// echo $this->replace_char("Fri") . "<br>";
				}
				
				if ($time_since > 60 && $time_since < 1200) {
					
					echo "\t<tr>";
					if ($row->salesrep_id == 1652736) {
						echo "\t\t<td width=\"14\" class=\"mark_black bold blink_text_black\"><img border=\"\" src=\"ne9.png\"></td>\n";
					} else {
						echo "\t\t<td width=\"14\" class=\"mark_black bold blink_text_black\"><img border=\"\" src=\"factory.png\"></td>\n";
					}
					echo "\t\t<td width=\"35\" class=\"mark_black bold blink_text_black\">" . $this->replace_char(date("D", strtotime($row->created))) . "</td>\n";
					// echo "\t\t<td width=\"35\" class=\"mark_black bold blink_text_black\">" . $this->replace_char(date("D", strtotime($row->created))) . "</td>\n";
					echo "\t\t<td width=\"45\" class=\"mark_black bold blink_text_black\">" . date("H:i", strtotime($row->created)) . "</td>\n";
					echo "\t\t<td width=\"80\" class=\"mark_black bold blink_text_black\">$row->artnr</td>\n";
					echo "\t\t<td class=\"mark_black bold blink_text_black\">$trimmaprodukten</td>\n";
					if ($row->c_tax_id == 1000000) {
						echo "\t\t<td width=\"35\" class=\"mark_black bold blink_text_black\">VMB</td>\n";
					} else {
						echo "\t\t<td width=\"35\" class=\"mark_black bold blink_text_black\">&nbsp;</td>\n";
					}
					echo "\t</tr>\n";
				} elseif ($time_since > 60 && $countrow < 38) {
					echo "\t<tr>";
					if ($row->salesrep_id == 1652736) {
						echo "\t\t<td width=\"14\" class=\"\"><img border=\"\" src=\"ne9.png\"></td>\n";
					} else {
						echo "\t\t<td width=\"14\" class=\"\"><img border=\"\" src=\"factory.png\"></td>\n";
					}
					echo "\t\t<td width=\"35\" class=\"\">" . $this->replace_char(date("D", strtotime($row->created))) . "</td>\n";
					// echo "\t\t<td width=\"35\" class=\"\">" . $this->replace_char(date("D", strtotime($row->created))) . "</td>\n";
					// echo "\t\t<td width=\"35\" class=\"\">" . $booked_date_short . "</td>\n";
					echo "\t\t<td width=\"45\" class=\"\">" . date("H:i", strtotime($row->created)) . "</td>\n";
					echo "\t\t<td width=\"80\" class=\"\">$row->artnr</td>\n";
					echo "\t\t<td class=\"\">$trimmaprodukten</td>\n";
					if ($row->c_tax_id == 1000000) {
						echo "\t\t<td width=\"35\" class=\"align_center\">VMB</td>\n";
					} else {
						echo "\t\t<td width=\"35\" class=\"align_center\">&nbsp;</td>\n";
					}
					echo "\t</tr>\n";
				}
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
		
		} else {
		
			echo "<div class=\"count_data italic bold\">V�ntar p� att f�rsta produkten skall bokas</div>\n";
		
		}
			
	}

	function lastSoldTradeIn($lastweek) {
		global $dagensdatum;
		
		// $dagensdatum = "2018-11-05";

		$countrow = 0;
		$number = date('i');

		if ($_SESSION['notify_latest'] == "") {
			$_SESSION['notify_latest'] = 0;
		}

		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		// $dagensdatum = date("Y-m-d", time());
		$look_forward = date('Y-m-d', strtotime("$dagensdatum +3 day"));
		$look_backward = date('Y-m-d', strtotime("$dagensdatum -3 day"));
		$dagensdatum_notify = date("Y-m-d", time());
		
			
			/*
			$select = "SELECT il.updated, t.name as tillverkare, p.name as beskrivning, p.value as artnr ";
			$select .= "FROM m_inoutline miol ";
			$select .= "JOIN m_inout mio ON mio.m_inout_id = miol.m_inout_id ";
			$select .= "JOIN m_product p ON miol.m_product_id = p.m_product_id ";
			$select .= "JOIN m_product_po po ON p.m_product_id = po.m_product_id ";
			$select .= "JOIN xc_manufacturer t ON t.xc_manufacturer_id = p.xc_manufacturer_id ";
			$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = po.c_bpartner_id ";
			$select .= "JOIN c_invoiceline il ON il.m_inoutline_id = miol.m_inoutline_id ";
			$select .= "WHERE mio.docstatus IN ('CO') AND mio.deliveryViaRule IN ('S','P') AND mio.isSOTrx = 'Y' AND mio.isInDispute!='Y' and mio.isActive='Y' AND mio.AD_Client_ID=1000000 AND mio.M_rma_ID is null ";
			$select .= "AND date(il.updated)>='$dagensdatum 00:00:00' AND date(il.updated)<='$dagensdatum 23:59:59' AND bp.value = '5555' AND il.qtyinvoiced = 1 ";
			$select .= "ORDER BY il.updated DESC ";
			*/

			/*
			$select = "SELECT DISTINCT i.updated, t.name as tillverkare, p.name as beskrivning, p.value as artnr ";
			$select .= ", p.c_taxcategory_id ";
			$select .= "FROM m_inoutline miol ";
			$select .= "JOIN m_inout mio ON mio.m_inout_id = miol.m_inout_id ";
			$select .= "JOIN m_product p ON miol.m_product_id = p.m_product_id ";
			$select .= "JOIN m_product_po po ON p.m_product_id = po.m_product_id ";
			$select .= "JOIN xc_manufacturer t ON t.xc_manufacturer_id = p.xc_manufacturer_id ";
			$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = po.c_bpartner_id ";
			$select .= "JOIN c_invoice i ON i.c_invoice_id = mio.c_invoice_id ";
			$select .= "JOIN c_invoiceline il ON il.c_invoice_id = i.c_invoice_id ";
			$select .= "WHERE mio.docstatus IN ('CO') AND mio.deliveryViaRule IN ('S','P') AND mio.isSOTrx = 'Y' AND mio.isInDispute!='Y' and mio.isActive='Y' AND mio.AD_Client_ID=1000000 AND mio.M_rma_ID is null ";
			if ($lastweek) {
				$select .= "AND il.updated > CURRENT_TIMESTAMP - INTERVAL '7 days' AND bp.value = '5555' AND il.qtyinvoiced = 1 ";
			} else {
				$select .= "AND date(il.updated)>='$dagensdatum 00:00:00' AND date(il.updated)<='$dagensdatum 23:59:59' AND bp.value = '5555' AND il.qtyinvoiced = 1 ";
			}
			$select .= "AND NOT p.value IN ('sensorcleaning','services') ";
			$select .= "ORDER BY i.updated DESC ";
			*/

			$select = "SELECT DISTINCT i.created, t.name as tillverkare, p.name as beskrivning, p.value as artnr, p.c_taxcategory_id, price.pricelimit, price.pricestd ";
			$select .= "FROM m_inoutline miol ";
			$select .= "JOIN m_inout mio ON mio.m_inout_id = miol.m_inout_id ";
			$select .= "JOIN m_product p ON miol.m_product_id = p.m_product_id ";
			$select .= "JOIN m_product_po po ON p.m_product_id = po.m_product_id ";
			$select .= "JOIN xc_manufacturer t ON t.xc_manufacturer_id = p.xc_manufacturer_id ";
			$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = po.c_bpartner_id ";
			$select .= "JOIN c_invoice i ON i.c_invoice_id = mio.c_invoice_id ";
			$select .= "JOIN c_invoiceline il ON il.c_invoice_id = i.c_invoice_id ";
			$select .= "JOIN m_productprice price ON p.m_product_id = price.m_product_id ";
			$select .= "WHERE mio.docstatus IN ('CO') AND mio.deliveryViaRule IN ('S','P') AND mio.isSOTrx = 'Y' AND mio.isInDispute!='Y' and mio.isActive='Y' AND mio.AD_Client_ID=1000000 AND mio.M_rma_ID is null ";
			if ($lastweek) {
				$select .= "AND il.created > CURRENT_TIMESTAMP - INTERVAL '7 days' AND bp.value = '5555' AND il.qtyinvoiced = 1 ";
			} else {
				$select .= "AND date(il.created)>='$dagensdatum 00:00:00' AND date(il.created)<='$dagensdatum 23:59:59' AND bp.value = '5555' AND il.qtyinvoiced = 1 ";
			}
			$select .= "AND NOT p.value IN ('sensorcleaning','services','filter','valuation','updmjukvara','service123') ";
			$select .= "AND NOT p.value IN ('dagshyra','dagshyra_objektiv','Dagshyra_system','veckohyra','veckohyra_objektiv') ";  // tar bort hyra
			$select .= "AND NOT i.c_bpartner_id IN (1013455,1013492) ";  // tar bort ordrar inbytesg�nget
			$select .= "AND price.m_pricelist_version_ID=1000000 ";
			$select .= "ORDER BY i.created DESC ";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo ($res ? pg_num_rows($res) : 0);
				// exit;
			}
			
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo "Var session: " . $_SESSION['notify_latest'] . "<br>";
				echo "Var pg_num_rows h�r: " . ($res ? pg_num_rows($res) : 0) . "<br>";
			}
			
			if ($_SESSION['notify_latest'] < ($res ? pg_num_rows($res) : 0)) {
			// if (($_SESSION['notify_latest'] < pg_num_rows($res)) && $_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.125x") {
					echo "den k�rs<br>";
				}
				// echo "<audio autoplay>\n";
				// echo "\t<source src=\"party_horn.mp3\" type=\"audio/mpeg\">\n";
				// echo "</audio>\n";
			}
			$_SESSION['notify_latest'] = ($res ? pg_num_rows($res) : 0); // h�r s�tter vi v�rdet som h�ller reda p� ljuduppspelningen
			
			$this->getMarinatingTime(); // ta fram de olika marineringsdatumena
			
				if ($res && pg_num_rows($res) > 0) {
				
					if ($lastweek) {
						echo "<div class=\"count_data bold italic\">Sålda begagnade produkter senaste veckan</div>\n";
					} else {
						echo "<div class=\"count_data bold italic\">Senast sålda begagnade produkter</div>\n";
					}
					echo "<table id=\"begg_senaste\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
				
					while ($res && $row = pg_fetch_object($res)) {

						$trimmaprodukten = $row->tillverkare . " " . $row->beskrivning;
						
						if (strlen($trimmaprodukten) >= 50)
							$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
						
						// $time_since = (time() - strtotime($row->updated));
						$sold_date_short = date("Y-m-d", strtotime($row->created));
						
						$time_since = (time() - strtotime($row->created));
						
						if ($dagensdatum_notify == $sold_date_short && $_SERVER['REMOTE_ADDR'] != "192.168.1.89") {
							$this->addNotify($row->artnr,$trimmaprodukten,1);
						}
						
						// if ($time_since < 30 && $countrow == 0) {
						if ($time_since < 30 && $countrow == 0 && $no == "more") {
							echo "<audio autoplay>\n";
							if ($row->c_taxcategory_id != 1000000) {
								// echo "\t<source src=\"boo3.mp3\" type=\"audio/mpeg\">\n";
								echo "\t<source src=\"ticking_bomb.mp3\" type=\"audio/mpeg\">\n";
							} elseif ($number % 2 == 0) {
								// echo "\t<source src=\"explosion_1.mp3\" type=\"audio/mpeg\">\n";
								echo "\t<source src=\"ticking_bomb.mp3\" type=\"audio/mpeg\">\n";
							} else {
								// echo "\t<source src=\"cannon.mp3\" type=\"audio/mpeg\">\n";
								echo "\t<source src=\"ticking_bomb.mp3\" type=\"audio/mpeg\">\n";
							}
							echo "</audio>\n";
						}
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
							echo $time_since;
						}
						
						if ($lastweek) {
							echo "\t<tr>";
							// echo "\t\t<td width=\"130\" class=\"$backcolor\">" . date("Y-m-d H:i", strtotime($row->updated)) . "</td>\n";
							echo "\t\t<td width=\"130\" class=\"$backcolor\">" . date("Y-m-d H:i", strtotime($row->created)) . "</td>\n";
							echo "\t\t<td width=\"80\" class=\"$backcolor\">$row->artnr</td>\n";
							echo "\t\t<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row->artnr\">$trimmaprodukten</a></td>\n";
							echo "\t\t<td width=\"35\" class=\"align_center\">" . round(($row->pricelimit),0) . "</td>\n";
							echo "\t\t<td width=\"35\" class=\"align_center\">" . round(($row->pricestd),0) . "</td>\n";
							if ($row->c_taxcategory_id == 1000000) {
								echo "\t\t<td width=\"35\" class=\"align_center\">VMB</td>\n";
							} else {
								echo "\t\t<td width=\"35\" class=\"align_center\">&nbsp;</td>\n";
							}
							echo "\t</tr>\n";
						} elseif ($time_since > 60 && $time_since < 1200) {
							
							echo "\t<tr>";
							echo "\t\t<td width=\"45\" class=\"mark_black bold blink_text_black\">" . date("H:i", strtotime($row->created)) . "</td>\n";
							echo "\t\t<td width=\"80\" class=\"mark_black bold blink_text_black\">$row->artnr</td>\n";
							echo "\t\t<td class=\"mark_black bold blink_text_black\">$trimmaprodukten</td>\n";
							if ($row->c_taxcategory_id == 1000000) {
								echo "\t\t<td width=\"35\" class=\"mark_black bold blink_text_black\">VMB</td>\n";
							} else {
								echo "\t\t<td width=\"35\" class=\"mark_black bold blink_text_black\">&nbsp;</td>\n";
							}
							echo "\t</tr>\n";
						} elseif ($time_since > 60 && $countrow < 26) {
							echo "\t<tr>";
							echo "\t\t<td width=\"45\" class=\"$backcolor\">" . date("H:i", strtotime($row->created)) . "</td>\n";
							echo "\t\t<td width=\"80\" class=\"$backcolor\">$row->artnr</td>\n";
							echo "\t\t<td class=\"$backcolor\">$trimmaprodukten</td>\n";
							if ($row->c_taxcategory_id == 1000000) {
								echo "\t\t<td width=\"35\" class=\"align_center\">VMB</td>\n";
							} else {
								echo "\t\t<td width=\"35\" class=\"align_center\">&nbsp;</td>\n";
							}
							echo "\t</tr>\n";
						}
						
						$countrow++;
						
					}
					
					echo "</table>\n";
					if ($countrow > 0) {
						echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
					}
				
				} else {
				
					echo "<div class=\"count_data italic\">V�ntar p� att f�rsta produkten skall s�ljas</div>\n";
				
				}
				
				$this->findMissing5555();
				$this->findReadyForSale(false,true,false);
				$this->findMissingPrice();
				$this->findMissingManuProductno();
				$this->findMissingSaleStart();
				
				if ((date("G", time()) < 15) && $countrow < 25) {
					/*
					if ($number % 2 == 0) {
						echo file_get_contents("Antal.svg");
						echo file_get_contents("Omsattning.svg");
					} else {
						echo file_get_contents("Pling.svg");
					}
					*/
				} elseif ($countrow < 25) {
					echo "<div class=\"align_center clock mark_red\">#www</div>";
					echo "<div class=\"clear\"></div>";
				}
				
				
				
	}

	function getMarinatingTime() {
	
		if (date('Y') == 2020 && date('W') == 45) {
			// $tradein_days_planning_forward_short = date('j/n', strtotime("+11 days"));
			$tradein_days_planning_forward_long = date('j/n', strtotime("+16 days"));
			$tradein_days_planning_forward_long_korr = date('Y-m-d', strtotime("+16 days"));
			$tradein_days_planning_forward_short = date('j/n', strtotime("+7 days"));
		} elseif (date('Y') == 2020 && date('W') == 46) {
			// $tradein_days_planning_forward_short = date('j/n', strtotime("+11 days"));
			$tradein_days_planning_forward_long = date('j/n', strtotime("+18 days"));
			$tradein_days_planning_forward_long_korr = date('Y-m-d', strtotime("+18 days"));
			$tradein_days_planning_forward_short = date('j/n', strtotime("+7 days"));
		} elseif (date('Y') == 2020 && date('W') == 47) {
			// $tradein_days_planning_forward_short = date('j/n', strtotime("+11 days"));
			$tradein_days_planning_forward_long = date('j/n', strtotime("+20 days"));
			$tradein_days_planning_forward_long_korr = date('Y-m-d', strtotime("+20 days"));
			$tradein_days_planning_forward_short = date('j/n', strtotime("+7 days"));
		} elseif (date('Y') == 2020 && date('W') == 48) {
			// $tradein_days_planning_forward_short = date('j/n', strtotime("+11 days"));
			$tradein_days_planning_forward_long = date('j/n', strtotime("+22 days"));
			$tradein_days_planning_forward_long_korr = date('Y-m-d', strtotime("+22 days"));
			$tradein_days_planning_forward_short = date('j/n', strtotime("+7 days"));
		} elseif (date('Y') == 2020 && date('W') == 49) {
			// $tradein_days_planning_forward_short = date('j/n', strtotime("+11 days"));
			$tradein_days_planning_forward_long = date('j/n', strtotime("+24 days"));
			$tradein_days_planning_forward_long_korr = date('Y-m-d', strtotime("+24 days"));
			$tradein_days_planning_forward_short = date('j/n', strtotime("+7 days"));
		} elseif (date('Y') == 2020 && date('W') == 50) {
			// $tradein_days_planning_forward_short = date('j/n', strtotime("+11 days"));
			$tradein_days_planning_forward_long = date('j/n', strtotime("+26 days"));
			$tradein_days_planning_forward_long_korr = date('Y-m-d', strtotime("+26 days"));
			$tradein_days_planning_forward_short = date('j/n', strtotime("+7 days"));
		} elseif (date('Y') == 2020 && date('W') == 51) {
			// $tradein_days_planning_forward_short = date('j/n', strtotime("+11 days"));
			$tradein_days_planning_forward_long = date('j/n', strtotime("+28 days"));
			$tradein_days_planning_forward_long_korr = date('Y-m-d', strtotime("+28 days"));
			$tradein_days_planning_forward_short = date('j/n', strtotime("+7 days"));
		} else {
			// $tradein_days_planning_forward_short = date('j/n', strtotime("+13 days"));
			$tradein_days_planning_forward_long = date('j/n', strtotime("+30 days"));
			$tradein_days_planning_forward_long_korr = date('Y-m-d', strtotime("+30 days"));
			$tradein_days_planning_forward_short = date('j/n', strtotime("+7 days"));
		}
		
		if (date('N', strtotime($tradein_days_planning_forward_long_korr)) == 6) {
			$tradein_days_planning_forward_long = date('j/n', strtotime("$tradein_days_planning_forward_long_korr -1 day"));
		} elseif (date('N', strtotime($tradein_days_planning_forward_long_korr)) == 7) {
			$tradein_days_planning_forward_long = date('j/n', strtotime("$tradein_days_planning_forward_long_korr -2 day"));
		}
		
		/*
		echo "<div class=\"container_white align_center clock\">";
		echo "<div class=\"clock\">" . date('N', strtotime($tradein_days_planning_forward_long)) . "</div>";
		echo "<div class=\"clear\"></div>";
		echo "</div>";
		*/
		echo "<div class=\"container_white align_center clock\">";
		echo "<div class=\"floatleft clock\">&nbsp;&nbsp;K:&nbsp;</div><div class=\"floatleft clock mark_green\">" . $tradein_days_planning_forward_short . "</div>";
		echo "<div class=\"floatleft clock\">&nbsp;&nbsp;&nbsp;L:&nbsp;</div><div class=\"floatleft clock mark_green\">" . $tradein_days_planning_forward_long . "</div>";
		echo "<div class=\"clear\"></div>";
		echo "</div>";
	
	}

	function getTimeLeft($time) {

				// if (strtotime('2020-07-17 17:00:00') > time()) {
				if (strtotime($time) > time()) {

					/*
					//Convert to date
					// $datestr= "2020-07-17 17:00:00";//Your date
					$datestr=$time;//Your date
					$date=strtotime($datestr);//Converted to a PHP date (a second count)

					//Calculate difference
					$diff=$date-time();//time returns current time in seconds
					$days=floor($diff/(60*60*24));//seconds/minute*minutes/hour*hours/day)
					$hours=round(($diff-$days*60*60*24)/(60*60));

					if ($days > 19) {
						echo "<div class=\"floatleft clock mark_red\">" . $days .  "</div><div class=\"floatleft clock\">&nbsp;dagar&nbsp;</div><div class=\"floatleft clock mark_red\">" . $hours . "</div><div class=\"floatleft clock\">&nbsp;tim</div>";
					} elseif ($days > 9) {
						echo "<div class=\"floatleft clock mark_blue\">" . $days .  "</div><div class=\"floatleft clock\">&nbsp;dagar&nbsp;</div><div class=\"floatleft clock mark_blue\">" . $hours . "</div><div class=\"floatleft clock\">&nbsp;tim</div>";
					} else {
						echo "<div class=\"floatleft clock mark_green\">" . $days .  "</div><div class=\"floatleft clock\">&nbsp;dagar&nbsp;</div><div class=\"floatleft clock mark_green\">" . $hours . "</div><div class=\"floatleft clock\">&nbsp;tim</div>";
					}
					echo "<div class=\"clear\"></div>";
					*/

					$seconds = strtotime($time) - time();

					$days = floor($seconds / 86400);
					$seconds %= 86400;

					$hours = floor($seconds / 3600);
					$seconds %= 3600;

					$minutes = floor($seconds / 60);
					$seconds %= 60;

					if ($days > 19) {
						echo "<div class=\"floatleft clock mark_red\">" . $days .  "</div><div class=\"floatleft clock\">&nbsp;dagar&nbsp;</div><div class=\"floatleft clock mark_red\">" . $hours . "</div><div class=\"floatleft clock\">&nbsp;tim&nbsp;</div><div class=\"floatleft clock mark_red\">" . $minutes . "</div><div class=\"floatleft clock\">&nbsp;min</div>";
					} elseif ($days > 4) {
						echo "<div class=\"floatleft clock mark_blue\">" . $days .  "</div><div class=\"floatleft clock\">&nbsp;dagar&nbsp;</div><div class=\"floatleft clock mark_blue\">" . $hours . "</div><div class=\"floatleft clock\">&nbsp;tim&nbsp;</div><div class=\"floatleft clock mark_blue\">" . $minutes . "</div><div class=\"floatleft clock\">&nbsp;min</div>";
					} else {
						if ($days == 1) {
							echo "<div class=\"floatleft clock mark_green\">" . $days .  "</div><div class=\"floatleft clock\">&nbsp;dag&nbsp;</div>";
						} elseif ($days > 0) {
							echo "<div class=\"floatleft clock mark_green\">" . $days .  "</div><div class=\"floatleft clock\">&nbsp;dagar&nbsp;</div>";
						}
						if ($hours > 0) {
							echo "<div class=\"floatleft clock mark_green\">" . $hours . "</div><div class=\"floatleft clock\">&nbsp;tim&nbsp;</div>";
						}
						echo "<div class=\"floatleft clock mark_green\">" . $minutes . "</div><div class=\"floatleft clock\">&nbsp;min</div>";
					}
					echo "<div class=\"clear\"></div>";

				}

	}
	function lastSoldTradeInSingle() {
		global $dagensdatum;
		
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		/*
		$select = "SELECT DISTINCT i.updated, t.name as tillverkare, p.name as beskrivning, p.value as artnr ";
		$select .= "FROM m_inoutline miol ";
		$select .= "JOIN m_inout mio ON mio.m_inout_id = miol.m_inout_id ";
		$select .= "JOIN m_product p ON miol.m_product_id = p.m_product_id ";
		$select .= "JOIN m_product_po po ON p.m_product_id = po.m_product_id ";
		$select .= "JOIN xc_manufacturer t ON t.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = po.c_bpartner_id ";
		$select .= "JOIN c_invoice i ON i.c_invoice_id = mio.c_invoice_id ";
		$select .= "JOIN c_invoiceline il ON il.c_invoice_id = i.c_invoice_id ";
		$select .= "WHERE mio.docstatus IN ('CO') AND mio.deliveryViaRule IN ('S','P') AND mio.isSOTrx = 'Y' AND mio.isInDispute!='Y' and mio.isActive='Y' AND mio.AD_Client_ID=1000000 AND mio.M_rma_ID is null ";
		$select .= "AND date(il.updated)>='$dagensdatum 00:00:00' AND date(il.updated)<='$dagensdatum 23:59:59' AND bp.value = '5555' AND il.qtyinvoiced = 1 ";
		$select .= "ORDER BY i.updated DESC ";
		$select .= "LIMIT 1 ";
		*/

		$select = "SELECT DISTINCT i.created, t.name as tillverkare, p.name as beskrivning, p.value as artnr ";
		$select .= "FROM m_inoutline miol ";
		$select .= "JOIN m_inout mio ON mio.m_inout_id = miol.m_inout_id ";
		$select .= "JOIN m_product p ON miol.m_product_id = p.m_product_id ";
		$select .= "JOIN m_product_po po ON p.m_product_id = po.m_product_id ";
		$select .= "JOIN xc_manufacturer t ON t.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = po.c_bpartner_id ";
		$select .= "JOIN c_invoice i ON i.c_invoice_id = mio.c_invoice_id ";
		$select .= "JOIN c_invoiceline il ON il.c_invoice_id = i.c_invoice_id ";
		$select .= "WHERE mio.docstatus IN ('CO') AND mio.deliveryViaRule IN ('S','P') AND mio.isSOTrx = 'Y' AND mio.isInDispute!='Y' and mio.isActive='Y' AND mio.AD_Client_ID=1000000 AND mio.M_rma_ID is null ";
		$select .= "AND date(il.created)>='$dagensdatum 00:00:00' AND date(il.created)<='$dagensdatum 23:59:59' AND bp.value = '5555' AND il.qtyinvoiced = 1 ";
		$select .= "AND NOT p.value IN ('sensorcleaning','services','filter','valuation') ";
		$select .= "AND NOT p.value IN ('dagshyra','dagshyra_objektiv','Dagshyra_system','veckohyra','veckohyra_objektiv') ";  // tar bort hyra
		$select .= "ORDER BY i.created DESC ";
		$select .= "LIMIT 1 ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		
		if ($res && pg_num_rows($res) > 0) {
		
			while ($res && $row = pg_fetch_object($res)) {

				$trimmaprodukten = $row->tillverkare . " " . $row->beskrivning;
				
				if (strlen($trimmaprodukten) >= 50)
					$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
				
				// $time_since = (time() - strtotime($row->updated));
				$time_since = (time() - strtotime($row->created));
				
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
					echo "<div class=\"container_orange_plain align_center latest_p mark_white blink_text_white\">$trimmaprodukten</div>";
				} elseif ($time_since < 60) {
					echo "<div class=\"container_orange align_center clock mark_white blink_text_white\">$trimmaprodukten</div>";
					// echo "\t\t<span class=\"mark_black blink_text_black\">$trimmaprodukten</span>\n";
					// echo "</div>\n";
				}
				
			}
			
		}
			
	}

	function notifyBorder() {
		global $dagensdatum;
		
		// TESTA LJUDET NEDAN
		/*
		echo "<audio autoplay>\n";
		echo "\t<source src=\"ticking_bomb.mp3\" type=\"audio/mpeg\">\n";
		echo "</audio>\n";
		*/
		
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}

		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.tradeinnotify ";
		$select .= "WHERE notActive = 1 ";
		$select .= "ORDER BY notID ASC ";
		$select .= "LIMIT 1 ";
		
		// echo $select;
		// exit;
	
		$res = @mysqli_query(Db::getConnection(false), $select);
		
		if (mysqli_num_rows($res) > 0) {
			
			while ($row = mysqli_fetch_object($res)) {

				echo "<audio autoplay>\n";
				if ($row->notType == 3) {
					// echo "\t<source src=\"ticking_bomb.mp3\" type=\"audio/mpeg\">\n";
					echo "\t<source src=\"firecracker.mp3\" type=\"audio/mpeg\">\n";
				} elseif ($row->notType == 100) {
					// echo "\t<source src=\"mtrex.wav\" type=\"audio/mpeg\">\n";
					echo "\t<source src=\"boooo.mp3\" type=\"audio/mpeg\">\n";
				} elseif ($row->notType == 2) {
					// echo "\t<source src=\"cash_register2.mp3\" type=\"audio/mpeg\">\n";
					// echo "\t<source src=\"firecracker.mp3\" type=\"audio/mpeg\">\n";
					// echo "\t<source src=\"squeal3.mp3\" type=\"audio/mpeg\">\n"; // PIGGY PIGGY
					echo "\t<source src=\"easy-peasy-lemon-squeezy.mp3\" type=\"audio/mpeg\">\n";
				} else {
					// echo "\t<source src=\"ticking_bomb.mp3\" type=\"audio/mpeg\">\n";
					// echo "\t<source src=\"claps.mp3\" type=\"audio/mpeg\">\n";
					// echo "\t<source src=\"Basic_Rock_FillintoCrash_135.mp3\" type=\"audio/mpeg\">\n";
					// echo "\t<source src=\"Boom_Chuk_130.mp3\" type=\"audio/mpeg\">\n";
					echo "\t<source src=\"yell4yeeha.mp3\" type=\"audio/mpeg\">\n";
				}
				echo "</audio>\n";
				
				echo "<div class=\"container_flip\">";
				echo "<div class=\"resized-splitflap\">" . $row->notShow . "</div>";
				echo "</div>";

				$this->resetNotifyBorder($row->notID);
				
			}

		}


		// echo "<div class=\"container_flip\">";
		// echo "<div class=\"resized-splitflap\">Tamron AF SP 24-70/2,8 Di VC USD G2 till Canon</div>";
		// echo "</div>";
		/*
		echo "<div class=\"container_flip\">";
		echo "<div class=\"do-splitflap\">hej hej</div>";
		echo "</div>";
		*/
		
		?>

		<script src="https://code.jquery.com/jquery-1.10.1.min.js"></script>
		<script src="js/jquery/jquery.splitflap.js"></script>
		<script>
			(function ($) {
				$(document).ready(function () {
					$('.do-splitflap')
							.splitFlap();

					$('.click-splitflap')
							.splitFlap({
								textInit:   'Click me ',
								autoplay:   false,
								onComplete: function () {
									console.log('Done !');
								}
							})
							.click(function () {
								$(this).splitFlap('splitflap').animate();
							});

					$('.empty-splitflap')
							.splitFlap({
								text: 'This is JS'
							});

					var ratio = 0.7;
					$('.resized-splitflap')
							.splitFlap({
								charWidth:  50 * ratio,
								charHeight: 100 * ratio,
								imageSize:  (2500 * ratio) + 'px ' + (100 * ratio) + 'px'
							});
				});
			})(jQuery);
		</script>
	
	<?php
		
	}

	function resetNotifyBorder($notID) {

		$aktuelltdatum = date("Y-m-d H:i:s");
		
		$updt  = "UPDATE cyberadmin.tradeinnotify ";
		$updt .= "SET ";
		$updt .= "notActive = '0' ";
		$updt .= "WHERE notID = '" . $notID . "' ";
		
		// echo $updt;
		// exit;

		$res = mysqli_query(Db::getConnection(true), $updt);

	}

	function checkLastPingIncomming() {
	
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}

		$select  = "SELECT ping_Parcels, ping_Time ";
		$select .= "FROM cyberadmin.ping_incomming ";
		$select .= "WHERE ping_Time >= '$dagensdatum 00:00:00' AND ping_Time <='$dagensdatum 23:59:59' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			$select .= "AND ping_Reset = 0 ";
		} else {
			$select .= "AND ping_Reset = 0 ";
		}
		$select .= "AND ping_deleted = 0 ";
		$select .= "ORDER BY ping_ID DESC ";
		$select .= "LIMIT 1 ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo mysqli_num_rows($res);
			// exit;
		}
	
		// $row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			
			while ($row = mysqli_fetch_object($res)) {
				
				$time_since = (time() - strtotime($row->ping_Time));
				
				if ($time_since > 1800) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}

	}

	function getLastPingIncomming($small = false) {
	
		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}

		$select  = "SELECT ping_Parcels, ping_Time ";
		$select .= "FROM cyberadmin.ping_incomming ";
		$select .= "WHERE ping_Time >= '$dagensdatum 00:00:00' AND ping_Time <='$dagensdatum 23:59:59' ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			$select .= "AND ping_Reset = 0 ";
		} else {
			$select .= "AND ping_Reset = 0 ";
		}
		$select .= "AND ping_deleted = 0 ";
		$select .= "ORDER BY ping_ID DESC ";
		$select .= "LIMIT 1 ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo mysqli_num_rows($res);
			// exit;
		}
	
		// $row = mysqli_fetch_object($res);
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo "<audio autoplay>\n";
			echo "\t<source src=\"cantina.mp3\" type=\"audio/mpeg\">\n";
			echo "</audio>\n";
		}

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_object($res)) {

				$time_since = (time() - strtotime($row->ping_Time));
				
				if ($time_since < 30) {
					echo "<audio autoplay>\n";
					if (date("N", time()) == 1 || date("N", time()) == 3 || date("N", time()) == 5) {
						echo "\t<source src=\"cantina.mp3\" type=\"audio/mpeg\">\n";
					} else {
						// echo "\t<source src=\"nuclear_alarm.mp3\" type=\"audio/mpeg\">\n";
						echo "\t<source src=\"cantina.mp3\" type=\"audio/mpeg\">\n";
					}
					echo "</audio>\n";
				}
				
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
					echo "<div class=\"container_blue align_center clock mark_white blink_text_white\">" . $row->ping_Parcels . " paket har ankommit logistik</div>";
				} elseif ($time_since > 1800 && $small) {
					echo "<div class=\"container_blue align_center clock mark_white blink_text_white\">" . $row->ping_Parcels . " paket</div>";
				} elseif ($time_since < 1800 && !$small) {
					echo "<div class=\"container_blue align_center clock mark_white blink_text_white\">" . $row->ping_Parcels . " paket har ankommit logistik</div>";
				}
				
			}
		
		}

	}

	function pingIncommingReset() {

		// echo $_SERVER['PHP_SELF'];
		// exit;

		$updt  = "UPDATE cyberadmin.ping_incomming ";
		$updt .= "SET ping_Reset = 1 ";
		$updt .= "WHERE ping_Reset = 0 ";
		$res = mysqli_query(Db::getConnection(true), $updt);

	}
	
	function tradeInValue($showweb,$onlysum) {
		global $dagensdatum;
		
		// $dagensdatum = "2018-11-05";

		$countrow = 0;

		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}
		// $dagensdatum = date("Y-m-d", time());
		$look_forward = date('Y-m-d', strtotime("$dagensdatum +3 day"));
		$look_backward = date('Y-m-d', strtotime("$dagensdatum -3 day"));
		
			
			$select = "SELECT cat.name, SUM(pstock.qtyonhand) AS antallager, SUM(pstock.qtyonhand*po.currentcostprice) AS storevalue ";
			$select .= "FROM m_product_cache pstock ";
			$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_category cat ON cat.m_product_category_id = prod.m_product_category_id ";
			$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
			$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
			$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
			$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
			$select .= "AND cbp.value = '5555' AND prod_po.iscurrentvendor = 'Y' ";
			$select .= "AND NOT cat.m_product_category_id=1000000 ";
			if ($showweb == true) {
				$select .= "AND prod.IsSelfService = 'Y' AND pstock.qtyavailable > 0 ";
			}
			$select .= "GROUP BY cat.name, pstock.qtyonhand ";
			$select .= "ORDER BY storevalue DESC ";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

			$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo ($res ? pg_num_rows($res) : 0);
				// exit;
			}
			
			
				if ($res && pg_num_rows($res) > 0) {
					if (!$onlysum) {
						if ($showweb == true) {
							echo "<div class=\"count_data bold italic\">Begagnade produkter ute p� webben just nu</div>\n";
							// echo "<table id=\"begg_nu\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
							echo "<table id=\"begg_tot\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
						} else {
							echo "<div class=\"count_data bold italic\">Begagnade produkter i lager totalt</div>\n";
							echo "<table id=\"begg_tot\" width=\"95%\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
						}
					}
				
					while ($res && $row = pg_fetch_object($res)) {

						$trimmaprodukten = $row->name;
						
						if (strlen($trimmaprodukten) >= 50)
							$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
						
						$totalvarde += $row->storevalue;
						$totalantal += $row->antallager;
						$summalager = number_format($row->storevalue, 0, ',', ' ');
						
						if (!$onlysum) {
							echo "\t<tr>";
							echo "\t\t<td class=\"$backcolor\">$trimmaprodukten</td>\n";
							echo "\t\t<td width=\"40\" class=\"align_center\">$row->antallager</td>\n";
							echo "\t\t<td width=\"130\" class=\"align_right\">$summalager SEK</td>\n";
							echo "\t</tr>\n";
						}
						
						$countrow++;
						
						
					}
					
					if (!$onlysum) {
						echo "\t<tr>";
						echo "\t\t<td class=\"$backcolor\">&nbsp;</td>\n";
						echo "\t\t<td width=\"40\" class=\"align_center bold\">$totalantal</td>\n";
						echo "\t\t<td width=\"130\" class=\"align_right bold\">" . number_format($totalvarde, 0, ',', ' ') . " SEK</td>\n";
						echo "\t</tr>\n";
						echo "</table>\n";
					} else {

						echo "<div class=\"\">";

						echo "<span class=\"clock\">" . $totalantal . "</span>";
						echo "<span class=\"clock\"> / </span>";
						// echo "<span class=\"clock\">" . number_format($totalvarde, 0, ',', ' ') . " SEK</span>";
						if ($totalvarde > 3000000) {
							echo "<span class=\"clock mark_green\">" . round(($totalvarde / 1000), 0) . "</span>";
						} else {
							echo "<span class=\"clock\">" . round(($totalvarde / 1000), 0) . "</span>";
						}
						echo "</div>";


							
					}
				
				}
			
	}

	function getPlingButiksdorren() {
		
		// $dagensdatum = "2018-11-14";

		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}

		$select  = "SELECT COUNT(plingID) AS Antal ";
		$select .= "FROM cyberadmin.dorrpling ";
		$select .= "WHERE DATE_FORMAT(plingTime,'%Y-%m-%d') = '$dagensdatum' ";

		$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
			$row = mysqli_fetch_object($res);
			echo $row->Antal . "p";
		} else {
			echo "0p";
		}
			
	}
	
	function turnAroundButiken($showsum = false) {
		
		// $dagensdatum = "2018-11-14";

		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}

		$select  = "SELECT COUNT(deliveryViaRule) AS antal, SUM((select totallines from c_invoice where c_invoice_id=m_inout.c_invoice_id)) as total ";
		$select .= "FROM m_inout ";
		$select .= "WHERE docstatus IN ('CO') AND deliveryViaRule IN ('S','P') AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND AD_Client_ID=1000000 AND M_rma_ID is null AND deliveryViaRule = 'P' ";
		// $select .= "AND date(updated)>='$dagensdatum 00:00:00' AND date(updated)<='$dagensdatum 23:59:59' ";
		$select .= "AND date(pickdate)>='$dagensdatum 00:00:00' AND date(pickdate)<='$dagensdatum 23:59:59' ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

		if ($res && pg_num_rows($res) > 0) {
			$row = $res ? pg_fetch_object($res) : null;
			if ($showsum) {
				echo number_format($row->total, 0, ',', ' ') . " SEK";
			} else {
				echo " - " . $row->antal . "o";
			}
		} else {
			if ($showsum) {
				echo "0 SEK";
			} else {
				echo "-&nbsp;o";
			}
		}
			
	}

	function getTradeInInfo() {
		global $fi, $sv, $no;
	
		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.tradeinvalue ";
		$select .= "WHERE tiID = 1 ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
	
		$rows = mysqli_fetch_object($res);

		return $rows;
	
	}

	function getTradeInValue($change = false) {
	
		$todaydate = date("Y-m-d", time());
		
		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.tradeinvalue ";
		$select .= "WHERE tiID = 1 ";
		
		// echo $select;
		// exit;
	
		$res = @mysqli_query(Db::getConnection(false), $select);
		$rows = mysqli_fetch_object($res);
		
		if ($change) {
			echo "<a href=\"javascript:winPopupCenter(190, 800, '/tradein_update.php?edit=yes');\">";
			echo "<div class=\"\">";
		}
		if ($rows->tiGREEN_TO != NULL) {
			if ($rows->tiGREEN_TO == 0) {
				echo "<span class=\"clock\">" . $rows->tiGREEN_TO . "</span>";
			} else {
				echo "<span class=\"mark_green clock\">" . $rows->tiGREEN_TO . "</span>";
			}
		}
		if ($rows->tiRED_TO != NULL) {
			echo "<span class=\"mark_red clock\">" . $rows->tiRED_TO . "</span>";
		}
		echo "<span class=\"clock\"> / </span>";
		if ($rows->tiGREEN_TG != NULL) {
			if ($rows->tiGREEN_TG == 0) {
				echo "<span class=\"clock\">" . round($rows->tiGREEN_TG,0) . "</span>";
			} else {
				echo "<span class=\"mark_green clock\">" . round($rows->tiGREEN_TG,0) . "</span>";
			}
		}
		if ($rows->tiRED_TG != NULL) {
			echo "<span class=\"mark_red clock\">" . round($rows->tiRED_TG,0) . "</span>";
		}
		if ($change) {
			echo "</div>";
			echo "</a>";
		}
			
	}

	function updateTradeInValue() {
		global $tiID, $tiGREEN_TO, $tiGREEN_TB, $tiGREEN_TG, $tiRED_TO, $tiRED_TB, $tiRED_TG, $addcreatedby;
		
		$tiGREEN_TG = str_replace(',', '.', $tiGREEN_TG);
		$tiRED_TG = str_replace(',', '.', $tiRED_TG);
	
		$updt  = "UPDATE cyberadmin.tradeinvalue ";
		$updt .= "SET ";
		if ($tiGREEN_TO != "") {
			$updt .= "tiGREEN_TO = $tiGREEN_TO, ";
		} else {
			$updt .= "tiGREEN_TO = NULL, ";
		}
		if ($tiGREEN_TB != "") {
			$updt .= "tiGREEN_TB = $tiGREEN_TB, ";
		} else {
			$updt .= "tiGREEN_TB = NULL, ";
		}
		if ($tiGREEN_TG != "") {
			$updt .= "tiGREEN_TG = '$tiGREEN_TG', ";
		} else {
			$updt .= "tiGREEN_TG = NULL, ";
		}
		if ($tiRED_TO != "") {
			$updt .= "tiRED_TO = $tiRED_TO, ";
		} else {
			$updt .= "tiRED_TO = NULL, ";
		}
		if ($tiRED_TB != "") {
			$updt .= "tiRED_TB = $tiRED_TB, ";
		} else {
			$updt .= "tiRED_TB = NULL, ";
		}
		if ($tiRED_TG != "") {
			$updt .= "tiRED_TG = '$tiRED_TG' ";
		} else {
			$updt .= "tiRED_TG = NULL ";
		}
		$updt .= "WHERE tiID = 1 ";
	
		// echo $updt;
		// exit;
	
		$res = mysqli_query(Db::getConnection(true), $updt);
	
	}

	function findDoubleTradeInBooking() {
		global $dagensdatum;
		
		$countrow = 0;

		$select = "SELECT DISTINCT col.created, o.documentno, t.name as tillverkare, p.name as beskrivning, p.value as artnr, col.qtyallocated, col.qtyordered, col.qtyreserved, col.qtydelivered ";
		$select .= "FROM c_orderline col ";
		$select .= "JOIN c_order o ON o.c_order_id = col.c_order_id ";
		$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
		$select .= "JOIN m_product_po po ON p.m_product_id = po.m_product_id ";
		$select .= "JOIN xc_manufacturer t ON t.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = po.c_bpartner_id ";
		$select .= "WHERE o.docstatus IN ('CO','IP') AND o.deliveryViaRule IN ('S','P') AND o.isSOTrx = 'Y' AND o.isActive='Y' AND o.AD_Client_ID=1000000 ";
		$select .= "AND bp.value = '5555' AND col.qtyallocated < col.qtyordered AND col.qtydelivered = 0 ";
		$select .= "AND NOT o.c_doctypetarget_id IN(1000027) ";  // tar bort om offert
		$select .= "AND p.producttype = 'I' ";  // endast produkter, EJ tj�nster
		$select .= "ORDER BY col.created DESC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}
		
		
		if ($res && pg_num_rows($res) > 0) {
		
			echo "<div class=\"count_data bold italic\">Dubbelbokade produkter som m�ste �tg�rdas omedelbart!</div>\n";
			echo "<table id=\"begg_double\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($res && $row = pg_fetch_object($res)) {

				$trimmaprodukten = $row->tillverkare . " " . $row->beskrivning;
				
				if (strlen($trimmaprodukten) >= 50)
					$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
				
				echo "\t<tr>";
				// echo "\t\t<td width=\"45\" class=\"mark_black bold blink_text_black\">" . date("H:i", strtotime($row->created)) . "</td>\n";
				echo "\t\t<td width=\"80\" class=\"mark_black bold blink_text_black\">$row->artnr</td>\n";
				echo "\t\t<td class=\"mark_black bold blink_text_black\">$trimmaprodukten</td>\n";
				echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
		
		}
			
	}

	function findReadyForSale($reparationer = false, $nostoreshelf = false, $sok = false, $possible = false) {
		global $dagensdatum, $crontab;
		
		$countrow = 0;

		$select = "SELECT prod.value AS artnr, pstock.qtyonhand, manu.name AS tillverkare, prod.name, prod.IsSelfService, mloc.value, mloc.m_locator_id,  ";
		$select .= "prod.c_taxcategory_id, prod.m_product_parent_id, prod.m_product_id ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
		$select .= "JOIN m_locator mloc ON mloc.m_locator_id = prod.m_locator_id ";
		$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
		$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
		if ($possible == "sale") {
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyavailable > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
		} else {
			$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
		}
		$select .= "AND cbp.value = '5555' AND prod_po.iscurrentvendor = 'Y' ";
		if ($nostoreshelf) {
			$select .= "AND mloc.m_locator_id IN (1000000,1004125,1003683,1003684,1003685,1004170) ";
		} elseif ($reparationer) {
			$select .= "AND mloc.m_locator_id IN (1004170) ";
		} elseif ($testprodukter) {
			$select .= "AND mloc.m_locator_id IN (1004179) ";
		} else {
			$select .= "AND NOT mloc.m_locator_id IN (1000000,1004125,1003683,1003684,1003685,1004170,1004179,1004183,1004184,1004185,1004216,1004217,1004255,1004256) ";
		}
		if ($nostoreshelf) {
			$select .= "AND prod.IsSelfService = 'Y' ";
		} else {
			$select .= "AND prod.IsSelfService = 'N' ";
		}

		$select .= "AND prod.created < DATE '2025-09-02' ";
		
		$select .= "GROUP BY prod.value, pstock.qtyonhand, prod.name, manu.name, prod.IsSelfService, mloc.value, mloc.m_locator_id, prod.c_taxcategory_id, prod.m_product_parent_id, prod.m_product_id ";
		$select .= "ORDER BY manu.name ASC, prod.name ASC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}
		
		
		if ($res && pg_num_rows($res) > 0) {
		
			if ($nostoreshelf) {
				echo "<div class=\"count_data bold italic\">Publika produkter som saknar hyllplats, �tg�rdas snarast</div>\n";
				if ((date("i", time()) == 00 || date("i", time()) == 15 || date("i", time()) == 30 || date("i", time()) == 45) && date("s", time()) < 30) {
					echo "<audio autoplay>\n";
					// echo "\t<source src=\"not_priced.mp3\" type=\"audio/mpeg\">\n";
					echo "\t<source src=\"screaming-goat.mp3\" type=\"audio/mpeg\">\n";
					echo "</audio>\n";
				}
			} elseif ($reparationer) {
				echo "<div class=\"count_data bold italic\">Produkter skickade f�r reparation</div>\n";
			} elseif ($possible == "sale") {
				echo "<div class=\"count_data bold italic\">M�jliga produkter att l�gga ut</div>\n";
			} else {
				echo "<div class=\"count_data bold italic\">Klara produkter med hyllplats</div>\n";
			}
			if ($nostoreshelf) {
				echo "<table id=\"begg_misshelf\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			} elseif ($possible == "sale") {
				echo "<table id=\"begg_misshelf\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			} else {
				echo "<table id=\"begg_saleready\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			}
		
			while ($res && $row = pg_fetch_object($res)) {
				
				if ($possible == "sale") {
					
					if ($this->findReadyForSaleCheck($row->m_product_parent_id)) {

						$trimmaprodukten = $row->tillverkare . " " . $row->name;
						$trimmaproduktenSearch = $row->tillverkare . " " . $row->name;
						
						if (strlen($trimmaprodukten) >= 50)
							$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
						
						echo "\t<tr>";
						echo "\t\t<td width=\"80\" class=\"mark_black\">$row->artnr</td>\n";
						if ($sok) {
							// echo "\t\t<td class=\"mark_black\"><a target=\"_blank\" href=\"https://cyberphoto.se/sok?q=$trimmaproduktenSearch\">NYA</a></td>\n";
							echo "\t\t<td width=\"60\" class=\"mark_black\"><a href=\"javascript:winPopupCenter(900, 800, 'https://admin.cyberphoto.se/product_update.php?artnr=$row->artnr&m_product_id=$row->m_product_id');\">L�GG UT</a></td>\n";
							echo "\t\t<td width=\"60\" class=\"mark_black\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/search?q=$trimmaproduktenSearch\">GAMLA</a></td>\n";
						}
						echo "\t\t<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row->artnr\">$trimmaprodukten</a></td>\n";
						// echo "\t\t<td class=\"mark_black\">$trimmaprodukten</td>\n";
						echo "\t\t<td class=\"mark_black\">$row->value</td>\n";
						if ($row->c_taxcategory_id == 1000000) {
							echo "\t\t<td class=\"mark_black center\" width=\"30\" class=\"align_center\">VMB</td>\n";
						} else {
							echo "\t\t<td class=\"mark_black\" width=\"30\" class=\"align_center\">&nbsp;</td>\n";
						}
						echo "\t</tr>\n";
						
						$countrow++;
					}

				} else {

					$trimmaprodukten = $row->tillverkare . " " . $row->name;
					
					if (strlen($trimmaprodukten) >= 50)
						$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
					
					echo "\t<tr>";
					echo "\t\t<td width=\"80\" class=\"mark_black\">$row->artnr</td>\n";
					if ($sok) {
						echo "\t\t<td class=\"mark_black\"><a target=\"_blank\" href=\"https://cyberphoto.se/sok?q=$trimmaprodukten\">NYA</a></td>\n";
						echo "\t\t<td class=\"mark_black\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/search?q=$trimmaprodukten\">GAMLA</a></td>\n";
					}
					echo "\t\t<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row->artnr\">$trimmaprodukten</a></td>\n";
					// echo "\t\t<td class=\"mark_black\">$trimmaprodukten</td>\n";
					echo "\t\t<td class=\"mark_black\">$row->value</td>\n";
					if ($row->c_taxcategory_id == 1000000) {
						echo "\t\t<td class=\"mark_black center\" width=\"30\" class=\"align_center\">VMB</td>\n";
					} else {
						echo "\t\t<td class=\"mark_black\" width=\"30\" class=\"align_center\">&nbsp;</td>\n";
					}
					echo "\t</tr>\n";
					
					$countrow++;

				}
				
			}
			
			echo "</table>\n";
			if ($countrow > 0 && $crontab == "yes") {
				$this->addNotify(time(),"SALE! SALE! SALE! SALE!",100);
			} elseif ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			} else {
				echo "<div class=\"count_data italic top20 bold span_green2\">Inga produkter finns att l�gga ut just nu!</div>\n";
			}
		
		}
			
	}

	function findReadyForSaleCheck($m_product_parent_id) {
		global $dagensdatum;
		
		$countrow = 0;

		/*
		$select = "SELECT prod.value AS artnr, pstock.qtyonhand, manu.name AS tillverkare, prod.name, prod.IsSelfService, mloc.value, mloc.m_locator_id, prod.c_taxcategory_id, prod.m_product_parent_id ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
		$select .= "JOIN m_locator mloc ON mloc.m_locator_id = prod.m_locator_id ";
		$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
		$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
		// $select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND pstock.qtyreserved = 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyavailable > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
		$select .= "AND cbp.value = '5555' AND prod_po.iscurrentvendor = 'Y' ";
		$select .= "AND NOT mloc.m_locator_id IN (1000000,1004125,1003683,1003684,1003685,1004170,1004179) ";
		$select .= "AND prod.IsSelfService = 'Y' ";
		$select .= "AND prod.m_product_parent_id = '" . $m_product_parent_id . "' ";
		$select .= "GROUP BY prod.value, pstock.qtyonhand, prod.name, manu.name, prod.IsSelfService, mloc.value, mloc.m_locator_id, prod.c_taxcategory_id, prod.m_product_parent_id ";
		$select .= "ORDER BY manu.name ASC, prod.name ASC ";
		*/

		$select = "SELECT prod.value AS artnr ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
		$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyavailable > 0 ";
		$select .= "AND cbp.value = '5555' AND prod_po.iscurrentvendor = 'Y' ";
		$select .= "AND prod.IsSelfService = 'Y' ";
		$select .= "AND prod.m_product_parent_id = '" . $m_product_parent_id . "' ";

		// if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" && $m_product_parent_id == '1092776') {
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}
		
		
		if ($res && pg_num_rows($res) > 0) {
			return false;
		} else {
			return true;
		}
			
	}

	function bookedNotShipped() {
		global $dagensdatum;
		
		$countrow = 0;

		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d H:i:s", time());
		}
			
		$select = "SELECT DISTINCT date(col.created), col.created, o.documentno, t.name as tillverkare, p.name as beskrivning, p.value as artnr, ";
		$select .= "col.qtyallocated, col.qtyordered, col.qtyreserved, col.qtydelivered, col.c_tax_id, col.pricelimit, col.priceactual, col.priceentered ";
		$select .= "FROM c_orderline col ";
		$select .= "JOIN c_order o ON o.c_order_id = col.c_order_id ";
		$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
		$select .= "JOIN m_product_po po ON p.m_product_id = po.m_product_id ";
		$select .= "JOIN xc_manufacturer t ON t.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = po.c_bpartner_id ";
		$select .= "JOIN m_locator mloc ON mloc.m_locator_id = p.m_locator_id ";
		$select .= "WHERE o.docstatus IN ('CO','IP') AND o.deliveryViaRule IN ('S','P') AND o.isSOTrx = 'Y' AND o.isActive='Y' AND o.AD_Client_ID=1000000 ";
		$select .= "AND bp.value = '5555' AND NOT (col.qtyordered <> col.qtyallocated) AND col.qtydelivered = 0 AND col.qtyordered > 0 ";
		$select .= "AND NOT mloc.m_locator_id IN (1004179) ";  // tar bor om vi bokat upp som testkamera = IBT
		$select .= "AND NOT o.bill_bpartner_id IN (1013455,1013492) ";  // tar bort ordrar inbytesg�nget
		$select .= "ORDER BY col.created DESC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			// echo $select;
			echo "dag: " . date('N');
			// exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}
		
		
		if ($res && pg_num_rows($res) > 0) {
		
			echo "<div class=\"count_data bold italic\">Uppbokade, EJ skickade</div>\n";
			echo "<table id=\"begg_booked\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($res && $row = pg_fetch_object($res)) {

				$trimmaprodukten = $row->tillverkare . " " . $row->beskrivning;
				
				if (strlen($trimmaprodukten) >= 50)
					$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
				
				$time_since = (time() - strtotime($row->created));
				
				if ($time_since < 30 && $countrow == 0) {
					echo "<audio autoplay>\n";
					echo "\t<source src=\"pump_shotgun.mp3\" type=\"audio/mpeg\">\n";
					echo "</audio>\n";
				}
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
					// echo $time_since;
					// echo $this->replace_char("Fri") . "<br>";
				}
				
					echo "\t<tr>";
					echo "\t\t<td width=\"110\" class=\"\">" . $this->replace_char(date("Y-m-d H:i", strtotime($row->created))) . "</td>\n";
					echo "\t\t<td width=\"75\" class=\"align_center\"><a href=\"javascript:winPopupCenter(500, 1000, 'order_info.php?order=$row->documentno');\">$row->documentno</a></td>\n";
					// echo "\t\t<td width=\"45\" class=\"$backcolor\">" . date("H:i", strtotime($row->created)) . "</td>\n";
					echo "\t\t<td width=\"80\" class=\"align_center\">$row->artnr</td>\n";
					echo "\t\t<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row->artnr\">$trimmaprodukten</a></td>\n";
					echo "\t\t<td width=\"80\" class=\"align_center\">" . round($row->pricelimit) . "</td>\n";
					if ($row->priceentered < $row->pricelimit) {
						echo "\t\t<td width=\"80\" class=\"align_center span_red bold\">" . round($row->priceentered) . "</td>\n";
					} else {
						echo "\t\t<td width=\"80\" class=\"align_center\">" . round($row->priceentered) . "</td>\n";
					}
					if ($row->c_tax_id == 1000000) {
						echo "\t\t<td width=\"80\" class=\"align_center\">VMB</td>\n";
					} else {
						echo "\t\t<td width=\"80\" class=\"align_center\">&nbsp;</td>\n";
					}
					echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
		
		} else {
		
			echo "<div class=\"count_data italic\">V�ntar p� att f�rsta produkten skall bokas</div>\n";
		
		}
			
	}

	function findMissing5555() {
		global $dagensdatum;
		
		$countrow = 0;

		$select = "SELECT prod.value, CONCAT (manu.name, ' ', prod.name) AS beskrivning, cbp.name ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
		$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
		$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 ";
		$select .= "AND po.ad_client_id=1000000 AND po.isactive = 'Y' AND cbp.value = '1004141' AND prod_po.iscurrentvendor = 'Y' AND prod.demo_product = 'Y' AND prod.istradein = 'Y' ";
		// $select .= "AND (prod.createdby = 1000059 OR prod.createdby = 1000076 OR prod.createdby = 1507150 OR prod.createdby = 1000051 OR prod.createdby = 1563773 OR prod.createdby = 1730654) ";
		$select .= "AND NOT (prod.value LIKE '%d1' OR prod.value LIKE '%D1') ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}

		if ($res && pg_num_rows($res) > 0) {

			if ((date("i", time()) == 00 || date("i", time()) == 15 || date("i", time()) == 30 || date("i", time()) == 45) && date("s", time()) < 30) {
				echo "<audio autoplay>\n";
				echo "\t<source src=\"screaming-goat.mp3\" type=\"audio/mpeg\">\n";
				echo "</audio>\n";
			}
		
			echo "<div class=\"count_data bold italic\">Produkter som saknar ink�p Inbyte, �tg�rdas omedelbart!</div>\n";
			echo "<table id=\"begg_miss5555\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($res && $row = pg_fetch_object($res)) {

				$trimmaprodukten = $row->beskrivning;
				
				if (strlen($trimmaprodukten) >= 50)
					$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
				
				echo "\t<tr>";
				// echo "\t\t<td width=\"45\" class=\"mark_black bold blink_text_black\">" . date("H:i", strtotime($row->created)) . "</td>\n";
				echo "\t\t<td width=\"80\" class=\"mark_black bold blink_text_black\">$row->value</td>\n";
				echo "\t\t<td class=\"mark_black bold blink_text_black\">$trimmaprodukten</td>\n";
				echo "\t\t<td class=\"mark_black bold blink_text_black\">$row->name</td>\n";
				echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
		
		}
			
	}

	function findDoublets($all,$sok = false) {
		global $dagensdatum;
		
		$countrow = 0;

		// $select .= "SELECT count(prod.value) AS antaldubletter, prod.m_product_parent_id, manu.name AS tillverkare, prod2.name AS beskrivning ";
		$select .= "SELECT count(prod.value) AS antaldubletter, prod.m_product_parent_id, manu.name AS tillverkare, prod.name AS beskrivning ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_product prod2 ON prod2.m_product_id = prod.m_product_id ";
		$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
		$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
		$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
		$select .= "AND cbp.value = '5555' AND prod_po.iscurrentvendor = 'Y' ";
		$select .= "AND NOT prod.value IN('filter','IB_FV_1','IB_FV_2','IB_FV_3') ";
		// $select .= "GROUP BY prod.m_product_parent_id, pstock.qtyonhand, prod2.name, manu.name ";
		$select .= "GROUP BY prod.m_product_parent_id, pstock.qtyonhand, prod.name, manu.name ";
		if ($all) {
			$select .= "HAVING count(prod.value) > 1";
		} else {
			$select .= "HAVING count(prod.value) > 2";
		}
		// $select .= "ORDER BY antaldubletter DESC, manu.name ASC, prod2.name ASC";
		$select .= "ORDER BY antaldubletter DESC, manu.name ASC, prod.name ASC";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}

		if (date("d", time()) == 07 && date("H", time()) == 14 && date("i", time()) == 24 && date("s", time()) < 30) {
			echo "<audio autoplay>\n";
			// echo "\t<source src=\"nuclear_alarm.mp3\" type=\"audio/mpeg\">\n";
			echo "</audio>\n";
		}
		
		
		if ($res && pg_num_rows($res) > 0) {
		
			if ($all) {
				echo "<div class=\"count_data bold italic\">Samtliga produkter fler än 1st</div>\n";
			} else {
				echo "<div class=\"count_data bold italic\">Produkter fler än 2st</div>\n";
			}
			echo "<table id=\"begg_miss5555\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($res && $row = pg_fetch_object($res)) {

				$trimmaprodukten = $row->beskrivning;
				
				if (strlen($trimmaprodukten) >= 50)
					$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
				
				echo "\t<tr>";
				echo "\t\t<td width=\"40\" class=\"align_center mark_black\">$row->antaldubletter</td>\n";
				if ($sok) {
					echo "\t\t<td class=\"mark_black\"><a target=\"_blank\" href=\"https://cyberphoto.se/sok?q=$trimmaprodukten\">NYA</a></td>\n";
					echo "\t\t<td class=\"mark_black\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/search?q=$trimmaprodukten\">GAMLA</a></td>\n";
				}
				echo "\t\t<td class=\"mark_black\">$row->tillverkare $trimmaprodukten</td>\n";
				echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			/*
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
			*/
		
		}
			
	}

	function KOTlist() {
		global $dagensdatum, $products, $moms, $show_webb;
		
		$countrow = 0;

		$select = "SELECT prod.value AS artnr, pstock.qtyonhand, CONCAT (manu.name, ' ', prod.name) AS beskrivning, prod.IsSelfService, mloc.value, mloc.m_locator_id, ";
		$select .= "prod.c_taxcategory_id, price.pricelimit, prod.created, prod.m_product_id ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_productprice price ON prod.m_product_id = price.m_product_id ";
		$select .= "JOIN m_cost po ON po.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
		$select .= "JOIN m_locator mloc ON mloc.m_locator_id = prod.m_locator_id ";
		$select .= "JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
		$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
		// $select .= "WHERE prod.value = '863443_2' ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 AND pstock.qtyavailable > 0 AND po.m_costelement_id=1000005 AND po.m_costtype_id=1000000 AND po.ad_client_id=1000000 AND po.isactive = 'Y' ";
		$select .= "AND cbp.value = '5555' AND prod_po.iscurrentvendor = 'Y' AND price.m_pricelist_version_id = 1000000 ";
		if ($products == "cameras") {
			$select .= "AND prod.m_product_category_id IN (1000387,1000221,1000248,1000344,1000936,1000154,1001336,1001351,1001123,1000988) ";
		} elseif ($products == "lenses") {
			$select .= "AND prod.m_product_category_id IN (1000078,1000084,1000211,1000213,1000077,1000210,1000080,1000212) ";
		} elseif ($products == "accessories") {
			$select .= "AND NOT prod.m_product_category_id IN (1000387,1000221,1000248,1000344,1000936,1000154,1001336,1001351,1001123,1000988,1000078,1000084,1000211,1000213,1000077,1000210,1000080,1000212) ";
		}
		if ($moms == "yes") {
			$select .= "AND prod.c_taxcategory_id = 1000004 ";
		}
		if ($show_webb == "yes") {
			$select .= "AND prod.IsSelfService = 'Y' ";
		} else {
			$select .= "AND prod.IsSelfService = 'N' ";
		}
		$select .= "GROUP BY prod.value, pstock.qtyonhand, prod.name, manu.name, prod.IsSelfService, mloc.value, mloc.m_locator_id, prod.c_taxcategory_id, price.pricelimit, ";
		$select .= "prod.created, prod.m_product_id ";
		$select .= "ORDER BY manu.name ASC, prod.name ASC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}
		
		
		if ($res && pg_num_rows($res) > 0) {
		
			if ($nostoreshelf) {
				echo "<div class=\"count_data bold italic\">Publika produkter som saknar hyllplats, �tg�rdas snarast</div>\n";
			} elseif ($reparationer) {
				echo "<div class=\"count_data bold italic\">Produkter skickade f�r reparation</div>\n";
			} else {
				echo "<div class=\"count_data bold italic\">Tillg�ngliga begagnade produkter</div>\n";
			}
			if ($nostoreshelf) {
				echo "<table id=\"begg_miss5555\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			} else {
				echo "<table id=\"begg_saleready\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			}
		
			while ($res && $row = pg_fetch_object($res)) {

				// $trimmaprodukten = $row->tillverkare . " " . $row->name;
				$trimmaprodukten = $row->beskrivning;
				
				if (strlen($trimmaprodukten) >= 50)
					$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";

				$datecreated = $this->dayFromCreated($row->created);
				
				echo "\t<tr>";
				echo "\t\t<td width=\"80\" class=\"mark_black\">$row->artnr</td>\n";
				echo "\t\t<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=$row->artnr\">$trimmaprodukten</a></td>\n";
				// echo "\t\t<td class=\"mark_black\">$trimmaprodukten</td>\n";
				if ($datecreated > 13) {
					echo "\t\t<td class=\"align_center span_green bold\">$datecreated</td>\n";
				} elseif ($datecreated < 8) {
					echo "\t\t<td class=\"align_center span_red\">$datecreated</td>\n";
				} else {
					echo "\t\t<td class=\"align_center\">$datecreated</td>\n";
				}
				if ($row->c_taxcategory_id == 1000000) {
					echo "\t\t<td class=\"mark_black center\" width=\"30\" class=\"align_center\">VMB</td>\n";
				} else {
					echo "\t\t<td class=\"mark_black\" width=\"30\" class=\"align_center\">&nbsp;</td>\n";
				}
				echo "\t\t<td width=\"65\" class=\"align_center\">" . round($row->pricelimit) . "</td>\n";
				if ($row->isselfservice == 'Y') {
					echo "\t\t<td class=\"align_center\" width=\"15\"><img border=\"\" src=\"status_green.jpg\"></td>\n";
				} else {
					echo "\t\t<td class=\"align_center\" width=\"15\"><img border=\"\" src=\"status_red.jpg\"></td>\n";
				}
				echo "\t\t<td class=\"align_center\" width=\"15\"><a href=\"javascript:winPopupCenter(900, 800, 'https://admin.cyberphoto.se/product_update.php?artnr=$row->artnr&m_product_id=$row->m_product_id');\">Uppdatera</a></td>\n";
				echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
		
		}
			
	}

	function findMissingPrice() {
		global $dagensdatum;
		
		$countrow = 0;

		$select = "SELECT p.value as artnr, CONCAT (t.name, ' ', p.name) AS beskrivning, p.m_product_id ";
		$select .= ", p.c_taxcategory_id, p.created ";
		$select .= "FROM m_product p ";
		$select .= "JOIN m_product_po po ON p.m_product_id = po.m_product_id  ";
		$select .= "JOIN xc_manufacturer t ON t.xc_manufacturer_id = p.xc_manufacturer_id ";
		$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = po.c_bpartner_id ";
		$select .= "JOIN m_product_cache pstock ON pstock.m_product_id = p.m_product_id ";
		$select .= "JOIN m_productprice price ON price.m_product_id = p.m_product_id ";
		$select .= "WHERE pstock.qtyonhand > 0 AND bp.value = '5555' AND price.m_pricelist_version_id = 1000000 AND pstock.m_warehouse_id = 1000000 ";
		$select .= "AND (price.pricestd < 1 OR price.pricelist < 1) ";
		$select .= "ORDER BY beskrivning ASC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}
		
		
		if ($res && pg_num_rows($res) > 0) {
		
			if ((date("i", time()) == 00 || date("i", time()) == 15 || date("i", time()) == 30 || date("i", time()) == 45) && date("s", time()) < 30) {
				echo "<audio autoplay>\n";
				// echo "\t<source src=\"not_priced.mp3\" type=\"audio/mpeg\">\n";
				echo "\t<source src=\"screaming-goat.mp3\" type=\"audio/mpeg\">\n";
				echo "</audio>\n";
			}

			echo "<div class=\"count_data bold italic\">EJ prissatta produkter, �tg�rdas asap!</div>\n";
			echo "<table id=\"begg_missprice\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($res && $row = pg_fetch_object($res)) {

				$trimmaprodukten = $row->beskrivning;
				
				if (strlen($trimmaprodukten) >= 50)
					$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";

				$datecreated = $this->dayFromCreated($row->created);
				
				echo "\t<tr>";
				// echo "\t\t<td width=\"45\" class=\"mark_black\">" . date("H:i", strtotime($row->created)) . "</td>\n";
				echo "\t\t<td width=\"80\" class=\"mark_black\">$row->artnr</td>\n";
				echo "\t\t<td class=\"mark_black\">$trimmaprodukten</td>\n";
				echo "\t\t<td class=\"align_center\">$datecreated</td>\n";
				if ($row->c_taxcategory_id == 1000000) {
					echo "\t\t<td class=\"mark_black align_center\">VMB</td>\n";
				} else {
					echo "\t\t<td class=\"mark_black align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/product_update.php?artnr=$row->artnr&m_product_id=$row->m_product_id');\">Uppdatera</a></td>\n";
				}
				echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
		
		}
			
	}

	function getWishlistSpec($ID) {

		$select  = "SELECT * FROM cyberadmin.tradeinwishlist WHERE tiID = '" . $ID . "' ";
		$res = @mysqli_query(Db::getConnection(false), $select);
		$rows = mysqli_fetch_object($res);
		return $rows;

	}

	function doWishlistAdd($addProduct,$addNote,$addLinc,$addBy) {

		$aktuelltdatum = date("Y-m-d H:i:s");

		$addProduct = trim($addProduct);
		$addNote = trim($addNote);

		$updt  = "INSERT INTO cyberadmin.tradeinwishlist ";
		$updt .= "( ";
		$updt .= "tiProduct, ";
		if ($addNote != "") {
			$updt .= "tiNote, ";
		}
		if ($addLinc != "") {
			$updt .= "tiLinc, ";
		}
		$updt .= "tiBy, ";
		$updt .= "tiDate ";
		$updt .= ") ";
		$updt .= "VALUES ";
		$updt .= "( ";
		$updt .= "'$addProduct', ";
		if ($addNote != "") {
			$updt .= "'$addNote', ";
		}
		if ($addLinc != "") {
			$updt .= "'$addLinc', ";
		}
		$updt .= "'$addBy', ";
		$updt .= "now() ";
		$updt .= ") ";
		
		// echo $updt;
		// exit;

		
		$res = mysqli_query(Db::getConnection(true), $updt);

	}

	function doWishlistChange($addID,$addActive,$addProduct,$addNote,$addLinc) {

		$aktuelltdatum = date("Y-m-d H:i:s");
		
		$updt  = "UPDATE cyberadmin.tradeinwishlist ";
		$updt .= "SET ";
		if ($addActive == 1) {
			$updt .= "tiActive = '1', ";
		} else {
			$updt .= "tiActive = '0', ";
		}
		
		if ($addProduct != "") {
			$updt .= "tiProduct = '$addProduct', ";
		} else {
			$updt .= "tiProduct = NULL, ";
		}
		if ($addNote != "") {
			$updt .= "tiNote = '$addNote', ";
		} else {
			$updt .= "tiNote = NULL, ";
		}
		if ($addLinc != "") {
			$updt .= "tiLinc = '$addLinc' ";
		} else {
			$updt .= "tiLinc = NULL ";
		}
		$updt .= "WHERE tiID = '" . $addID . "' ";
		
		// echo $updt;
		// exit;

		$res = mysqli_query(Db::getConnection(true), $updt);

	}

	function getTradeInWishlist($show_note,$oldies) {
	
		$countrow = 0;
		$todaydate = date("Y-m-d", time());
		
		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.tradeinwishlist ";
		if ($oldies) {
			$select .= "WHERE tiActive = 0 ";
		} else {
			$select .= "WHERE tiActive = 1 ";
		}
		$select .= "ORDER BY tiDate DESC ";
		
		// echo $select;
		// exit;
	
		$res = @mysqli_query(Db::getConnection(false), $select);
		
		if (mysqli_num_rows($res) > 0) {

			if ($oldies) {
				echo "<div class=\"count_data bold italic\">Gamla �nskem�l</div>\n";
			} elseif ($show_note) {
				echo "<div class=\"count_data bold italic\">�nskem�l fr�n utvalda kunder</div>\n";
			} else {
				echo "<div class=\"count_data bold italic\">�nskem�l fr�n utvalda kunder</div>\n";
			}
			if ($oldies) {
				echo "<table id=\"begg_wishlist_old\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			} else {
				echo "<table id=\"begg_wishlist\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			}

			while ($row = mysqli_fetch_object($res)) {
				
				$datecreated = $this->dayFromCreated($row->tiDate);
				
				echo "\t<tr>";
				// echo "\t\t<td width=\"45\" class=\"mark_black\">" . date("H:i", strtotime($row->created)) . "</td>\n";
				if ($show_note) {
					echo "\t\t<td width=\"320\" class=\"mark_black\">$row->tiProduct</td>\n";
				} else {
					echo "\t\t<td width=\"320\" class=\"mark_black\">$row->tiProduct</td>\n";
				}
				if ($show_note) {
					echo "\t\t<td class=\"mark_black\">$row->tiNote</td>\n";
				} else {
					echo "\t\t<td class=\"mark_black\">&nbsp;</td>\n";
				}
				echo "\t\t<td width=\"200\" class=\"mark_black\">$row->tiBy</td>\n";
				echo "\t\t<td width=\"60\" class=\"align_right\">$datecreated dagar</td>\n";
				if ($show_note || $_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu') {
					echo "<td width=\"50\" align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $row->tiID . "\">�ndra</a></td>\n";
				}
				echo "\t</tr>\n";
				
				$countrow++;

			}
		
		echo "</table>\n";
		if ($countrow > 0) {
			echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
		}

		}
	}

	function findMissingPersnr() {
		global $dagensdatum;
		
		$countrow = 0;
		$show_spaceinvaders = false;

		$select = "SELECT o.documentno, o.created, ad.firstname, bp.name ";
		$select .= "FROM c_order o ";
		$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id ";
		$select .= "JOIN ad_user ad ON ad.ad_user_id = o.salesrep_id ";
		$select .= "WHERE o.docstatus IN ('DR','CO','IP') AND o.deliveryViaRule IN ('S','P') AND o.isSOTrx = 'Y' AND o.isActive='Y' AND o.AD_Client_ID=1000000  ";
		$select .= "AND o.xc_sales_order_status_id = 1000015 AND NOT o.c_doctypetarget_id IN(1000027) ";
		$select .= "AND bp.taxid IS NULL ";
		$select .= "AND o.created > CURRENT_TIMESTAMP - INTERVAL '365 days' ";
		$select .= "ORDER BY o.documentno DESC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}
		
		
		if ($res && pg_num_rows($res) > 0) {
		
			echo "<div class=\"count_data bold italic\">Inbytesaffärer där vi saknar personnummer</div>\n";
			echo "<table id=\"begg_missprice\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($res && $row = pg_fetch_object($res)) {

				// $datecreated = $this->dayFromCreated($row->created);
				$datecreated = time() - strtotime($row->created);
				
				// if ($datecreated < 86400) { // visas 1 dygn
				if ($datecreated < 10800) { // vissa 3 timmar
					$show_spaceinvaders = true;
				}

				if ((date("i", time()) == 00 || date("i", time()) == 15 || date("i", time()) == 30 || date("i", time()) == 45) && date("s", time()) < 30 && $datecreated < 900) {
					echo "<audio autoplay>\n";
					echo "\t<source src=\"not_priced.mp3\" type=\"audio/mpeg\">\n";
					echo "</audio>\n";
				}
				
				echo "\t<tr>";
				// echo "\t\t<td width=\"70\" class=\"mark_black\">$datecreated</td>\n";
				echo "\t\t<td width=\"70\" class=\"mark_black\">$row->documentno</td>\n";
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
					echo "\t\t<td class=\"mark_black\">$datecreated</td>\n";
				} else {
					echo "\t\t<td class=\"mark_black\">$row->name</td>\n";
				}
				echo "\t\t<td width=\"70\" class=\"align_center\">$row->firstname</td>\n";
				echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
		
			if ($show_spaceinvaders) {
				echo "<img border=\"0\" src=\"space_invaders_error.gif\">\n";
			}
		}
			
	}

	function getPingInfo() {
		global $fi, $sv, $no;
	
		$select  = "SELECT ks.koNR ";
		$select .= "FROM cyberadmin.kosystem ks ";
		$select .= "ORDER BY ks.koID DESC ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
	
		$rows = mysqli_fetch_object($res);

		return $rows;
	
	}

	function addPingNumber($konumber) {
	
		$todaydate = date("Y-m-d H:i:s", time());
		
		$updt  = "INSERT INTO cyberadmin.ping_incomming ";
		$updt .= "(ping_Parcels, ping_Time) ";
		$updt .= "VALUES ";
		$updt .= "('$konumber','$todaydate') ";

		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);
	
	}

	function getLastPing($bigscreen = false) {
	
		$todaydate = date("Y-m-d", time());
		
		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.ping_incomming ";
		$select .= "WHERE ping_deleted = 0 ";
		$select .= "ORDER BY ping_ID DESC ";
		
		// echo $select;
		// exit;
	
		$res = @mysqli_query(Db::getConnection(false), $select);
		$rows = mysqli_fetch_object($res);
	
		$shownumber = intval(substr($rows->ping_Parcels, -2));
		if ($shownumber == 00) {
			$shownumber = 0;
		} elseif ($shownumber == 01) {
			$shownumber = 1;
		} elseif ($shownumber == 02) {
			$shownumber = 2;
		} elseif ($shownumber == 03) {
			$shownumber = 3;
		} elseif ($shownumber == 04) {
			$shownumber = 4;
		} elseif ($shownumber == 05) {
			$shownumber = 5;
		} elseif ($shownumber == 06) {
			$shownumber = 6;
		} elseif ($shownumber == 7) {
			$shownumber = 7;
		} elseif ($shownumber == 8) {
			$shownumber = 8;
		} elseif ($shownumber == 9) {
			$shownumber = 9;
		}
		// if ($bigscreen && $not == "now") {
		if ($bigscreen) {
				if ($rows->koKASSA > 0) {
					if ((time() - strtotime($rows->koTIME)) < 4) {
						return "<div class=\"ko_nr\">" . $shownumber . "</div><div class=\"ko_kassa blink_text_white\">Kassa " . $rows->koKASSA . "</div>";
					} elseif ((time() - strtotime($rows->koTIME)) < 15) {
						return "<div class=\"ko_nr\">" . $shownumber . "</div><div class=\"ko_kassa\">Kassa " . $rows->koKASSA . "</div>";
					} else {
						return "<div class=\"ko_nr\">" . $shownumber . "</div>";
					}
				} else {
					return "<div class=\"ko_nr\">" . $shownumber . "</div>";
				}
		} else {
			// return "<div class=\"ko_nr\">" . $shownumber . "</div>";
			return $shownumber;
		}
	
	}

	function addNotify($artnr,$showname,$type) {

		$showname = substr($showname, 0, 50);

		$todaydate = date("Y-m-d H:i:s", time());
		$todaydate_short = date("Y-m-d", time());

		/*
		$updt  = "INSERT INTO cyberadmin.tradeinnotify ";
		$updt .= "(notArtnr, notShow, notType, notTime) ";
		$updt .= "VALUES ";
		$updt .= "('$artnr','$showname','$type','$todaydate') ";
		*/

		$updt  = "INSERT INTO cyberadmin.tradeinnotify (notArtnr, notShow, notType, notTime) ";
		$updt .= "SELECT * FROM (SELECT '$artnr', '$showname', '$type', '$todaydate') AS tmp ";
		$updt .= "WHERE NOT EXISTS (SELECT * FROM cyberadmin.tradeinnotify WHERE notArtnr = '$artnr'AND notType = '$type' AND DATE_FORMAT(notTime, '%Y-%m-%d') = '$todaydate_short') ";
		$updt .= "LIMIT 1; ";

		// echo $updt;
		// exit;

		try {
			$res = mysqli_query(Db::getConnection(true), $updt);
		} catch (\Exception $e) {
			// Notify insert failed silently — non-critical
		}

	}

	function getPingIncomming($oldies) {
	
		$countrow = 0;
		$todaydate = date("Y-m-d", time());
		
		$select  = "SELECT * ";
		$select .= "FROM cyberadmin.ping_incomming ";
		if ($oldies) {
			$select .= "WHERE ping_deleted = 1 ";
		} else {
			$select .= "WHERE ping_deleted = 0 ";
		}
		$select .= "ORDER BY ping_Time DESC ";
		$select .= "LIMIT 7 ";
		
		// echo $select;
		// exit;
	
		$res = @mysqli_query(Db::getConnection(false), $select);
		
		if (mysqli_num_rows($res) > 0) {

			if ($oldies) {
				echo "<div class=\"count_data bold italic\">Inkommande paket - Raderade poster</div>\n";
			} else {
				echo "<div class=\"count_data bold italic\">Inkommande paket</div>\n";
			}
			if ($oldies) {
				echo "<table id=\"begg_wishlist_old\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			} else {
				echo "<table id=\"begg_wishlist\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			}

			while ($row = mysqli_fetch_object($res)) {
				
				$datecreated = $this->dayFromCreated($row->ping_Time);
				
				echo "\t<tr>";
				echo "\t\t<td width=\"120\" class=\"mark_black\">" . date("Y-m-d H:i", strtotime($row->ping_Time)) . "</td>\n";
				echo "\t\t<td width=\"45\" class=\"mark_black align_center\">$row->ping_Parcels</td>\n";
				echo "\t\t<td width=\"45\" class=\"mark_black align_center\">$row->ping_ParcelsBuy</td>\n";
				if ($row->ping_changeby != "") {
					echo "\t\t<td width=\"300\" class=\"align_left\">" . $row->ping_changeby . "</td>\n";
				} else {
					echo "\t\t<td width=\"300\" class=\"mark_black\">&nbsp;</td>\n";
				}
				echo "<td width=\"50\" align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $row->ping_ID . "\">�ndra</a></td>\n";
				if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu') {
				}
				echo "\t</tr>\n";
				
				$countrow++;

			}
		
		echo "</table>\n";
		if ($countrow > 0) {
			echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
		}

		}
	}

	function getIncommingSpec($ID) {

		$select  = "SELECT * FROM cyberadmin.ping_incomming WHERE ping_ID = '" . $ID . "' ";
		$res = @mysqli_query(Db::getConnection(false), $select);
		$rows = mysqli_fetch_object($res);
		return $rows;

	}

	function doIncommingChange($addID,$addActive,$addNumber,$addBy,$addNumberBuy) {

		$aktuelltdatum = date("Y-m-d H:i:s");
		
		$updt  = "UPDATE cyberadmin.ping_incomming ";
		$updt .= "SET ";
		if ($addActive == 0) {
			$updt .= "ping_deleted = '0', ";
		} else {
			$updt .= "ping_deleted = '1', ";
		}
		$updt .= "ping_Parcels = '$addNumber', ";
		$updt .= "ping_changeby = '$addBy', ";
		$updt .= "ping_ParcelsBuy = '$addNumberBuy' ";
		// $updt .= "WHERE ping_ID = '" . $addID . "' ";
		$updt .= "WHERE ping_ID = $addID ";
		
		// echo $updt;
		// exit;

		$res = mysqli_query(Db::getConnection(true), $updt);

	}

	function findMissingManuProductno() {
		global $dagensdatum;
		
		$countrow = 0;

		$select = "SELECT prod.value,  CONCAT (manu.name, ' ', prod.name) AS beskrivning, prod.manufacturerproductno, prod.created ";
		$select .= "FROM m_product_cache pstock ";
		$select .= "JOIN m_product prod ON prod.m_product_id = pstock.m_product_id ";
		$select .= "JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
		$select .= "JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = prod.xc_manufacturer_id ";
		$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyonhand > 0 ";
		$select .= "AND prod.manufacturerproductno IS NULL AND prod_po.iscurrentvendor = 'Y' AND prod.demo_product = 'Y' AND prod.istradein = 'Y' ";
		$select .= "AND prod.created >= '2024-07-04' ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}

		if ($res && pg_num_rows($res) > 0) {

			if ((date("i", time()) == 00 || date("i", time()) == 15 || date("i", time()) == 30 || date("i", time()) == 45) && date("s", time()) < 30) {
				echo "<audio autoplay>\n";
				echo "\t<source src=\"screaming-goat.mp3\" type=\"audio/mpeg\">\n";
				echo "</audio>\n";
			}
		
			echo "<div class=\"count_data bold italic\">Produkter som saknar ink�psordernummer, �tg�rdas omedelbart!</div>\n";
			echo "<table id=\"begg_miss5555\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($res && $row = pg_fetch_object($res)) {

				$trimmaprodukten = $row->beskrivning;
				
				if (strlen($trimmaprodukten) >= 50)
					$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
				
				echo "\t<tr>";
				// echo "\t\t<td width=\"45\" class=\"mark_black bold blink_text_black\">" . date("H:i", strtotime($row->created)) . "</td>\n";
				echo "\t\t<td width=\"80\" class=\"mark_black bold blink_text_black\">$row->value</td>\n";
				echo "\t\t<td class=\"mark_black bold blink_text_black\">$trimmaprodukten</td>\n";
				// echo "\t\t<td class=\"mark_black bold blink_text_black\">$row->name</td>\n";
				echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
		
		}
			
	}


public function getMarination() {
    // Helpers
    $h = function($s){
        if ($s === null) return '';
        $s = (string)$s;
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    };

    $relative = function($ymd){
        try {
            $tz = new DateTimeZone('Europe/Stockholm');
            $today = new DateTime('today', $tz);
            $that  = new DateTime($ymd, $tz);
            $diff  = (int)$today->diff($that)->format('%r%a');
            if ($diff === 0)  return "$ymd (idag)";
            if ($diff === 1)  return "$ymd (imorgon)";
            if ($diff > 1)    return "$ymd (om $diff dagar)";
            if ($diff === -1) return "$ymd (ig�r)";
            return "$ymd (f�r ".(0-$diff)." dagar sedan)";
        } catch (Exception $e) { return $ymd; }
    };

    // Skriv modern, men stilla, tabell-CSS (eng�ngs)
    static $cssPrinted = false;
    if (!$cssPrinted) {
        echo '<style>
        .marination-table{width:100%;border-collapse:separate;border-spacing:0;margin-top:10px;font-size:14px}
        .marination-table thead th{background:#f9fafb;border-bottom:2px solid #e5e7eb;padding:8px;text-align:left}
        .marination-table td{border-bottom:1px solid #eee;padding:8px}
        .marination-group{background:#e5e7eb;font-weight:700}
        .marination-meta{color:#6b7280;font-size:12px}
        .nowrap{white-space:nowrap}
        .marination-table tbody tr.data-row:nth-child(even){background:#f9fafb}
        .marination-table tbody tr.data-row:hover{background:#eef2ff}
        </style>';
        $cssPrinted = true;
    }

    // SQL � Postgres, grupperar per datum och filtrerar framtid/idag
    $select = "
SELECT
  prod.value                AS artnr,
  pstock.qtyonhand          AS qtyonhand,
  manu.name                 AS tillverkare,
  prod.name                 AS product_name,
  mloc.value                AS locator_value,
  mloc.m_locator_id         AS m_locator_id,
  prod.m_product_id         AS m_product_id,
  DATE(prod.salestart)      AS salestart
FROM m_product_cache pstock
JOIN m_product     prod    ON prod.m_product_id = pstock.m_product_id
JOIN m_cost        po      ON po.m_product_id   = pstock.m_product_id
JOIN m_product_po  prod_po ON pstock.m_product_id = prod_po.m_product_id
JOIN m_locator     mloc    ON mloc.m_locator_id = prod.m_locator_id
JOIN c_bpartner    cbp     ON cbp.c_bpartner_id = prod_po.c_bpartner_id
JOIN xc_manufacturer manu   ON manu.xc_manufacturer_id = prod.xc_manufacturer_id
WHERE pstock.m_warehouse_id = 1000000
  AND pstock.qtyonhand > 0
  AND po.m_costelement_id = 1000005
  AND po.m_costtype_id    = 1000000
  AND po.ad_client_id     = 1000000
  AND po.isactive         = 'Y'
  AND cbp.value           = '5555'
  AND prod_po.iscurrentvendor = 'Y'
  AND mloc.m_locator_id NOT IN (1000000,1004125,1003683,1003684,1003685,1004170,1004179,1004183,1004184,1004185,1004216,1004217,1004255,1004256)
  AND prod.IsSelfService = 'N'
  AND prod.salestart IS NOT NULL
  AND DATE(prod.salestart) >= CURRENT_DATE
GROUP BY
  prod.value, pstock.qtyonhand, manu.name, prod.name,
  mloc.value, mloc.m_locator_id, prod.m_product_id, DATE(prod.salestart)
ORDER BY DATE(prod.salestart) ASC, manu.name ASC, prod.name ASC
    ";

    $res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
    if (!$res) { echo '<div class="marination-meta">Kunde inte h�mta data.</div>'; return; }

    // Gruppera p� salestart (YYYY-MM-DD)
    $groups = array();
    while ($res && $row = pg_fetch_assoc($res)) {
        $date = substr($row['salestart'], 0, 10);
        if (!isset($groups[$date])) $groups[$date] = array();
        $groups[$date][] = $row;
    }

    // Render � ENDAST: Artikel | Produkt | Plats
    echo '<table class="marination-table">';
    echo '<thead><tr>
            <th>Artikel</th>
            <th>Produkt</th>
            <th>Plats</th>
          </tr></thead><tbody>';

    if (!count($groups)) {
        echo '<tr><td colspan="3">Inga produkter hittades.</td></tr></tbody></table>';
        return;
    }

    foreach ($groups as $ymd => $rows) {
        $count = count($rows);
        echo '<tr class="marination-group"><td colspan="3">'
           . $h($relative($ymd)) . ' � ' . $count . ' produkt' . ($count!==1?'er':'')
           . '</td></tr>';

        foreach ($rows as $r) {
            $fullName = trim($r['tillverkare'].' '.$r['product_name']);
            echo '<tr class="data-row">';
            echo '<td class="nowrap">'.$h($r['artnr']).'</td>';
            echo '<td>'.$h($fullName).'</td>';
            echo '<td>'.$h($r['locator_value']).'</td>';
            echo '</tr>';
        }
    }

    echo '</tbody></table>';
}

	function findMissingSaleStart() {
		global $dagensdatum;
		
		$countrow = 0;

    $select = "
SELECT
  prod.value                AS artnr,
  pstock.qtyonhand          AS qtyonhand,
  manu.name                 AS tillverkare,
  prod.name                 AS product_name,
  CONCAT (manu.name, ' ', prod.name) AS beskrivning,
  mloc.value                AS locator_value,
  mloc.m_locator_id         AS m_locator_id,
  prod.m_product_id         AS m_product_id,
  DATE(prod.salestart)      AS salestart,
  prod.created,
  prod.c_taxcategory_id
FROM m_product_cache pstock
JOIN m_product     prod    ON prod.m_product_id = pstock.m_product_id
JOIN m_cost        po      ON po.m_product_id   = pstock.m_product_id
JOIN m_product_po  prod_po ON pstock.m_product_id = prod_po.m_product_id
JOIN m_locator     mloc    ON mloc.m_locator_id = prod.m_locator_id
JOIN c_bpartner    cbp     ON cbp.c_bpartner_id = prod_po.c_bpartner_id
JOIN xc_manufacturer manu   ON manu.xc_manufacturer_id = prod.xc_manufacturer_id
WHERE pstock.m_warehouse_id = 1000000
  AND pstock.qtyonhand > 0
  AND po.m_costelement_id = 1000005
  AND po.m_costtype_id    = 1000000
  AND po.ad_client_id     = 1000000
  AND NOT prod.c_taxcategory_id     = 1000004
  AND po.isactive         = 'Y'
  AND cbp.value           = '5555'
  AND prod_po.iscurrentvendor = 'Y'
  AND mloc.m_locator_id NOT IN (1000000,1004125,1003683,1003684,1003685,1004170,1004179,1004183,1004184,1004185,1004216,1004217,1004255,1004256)
  AND prod.IsSelfService = 'N'
  AND prod.salestart IS NOT NULL
  AND prod.salestart - prod.created < INTERVAL '1 day' AND prod.created >= DATE '2025-09-01'
GROUP BY
  prod.value, pstock.qtyonhand, manu.name, prod.name,
  mloc.value, mloc.m_locator_id, prod.m_product_id, DATE(prod.salestart)
ORDER BY DATE(prod.salestart) ASC, manu.name ASC, prod.name ASC
    ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo ($res ? pg_num_rows($res) : 0);
			// exit;
		}

		if ($res && pg_num_rows($res) > 0) {

			if ((date("i", time()) == 00 || date("i", time()) == 15 || date("i", time()) == 30 || date("i", time()) == 45) && date("s", time()) < 30) {
				echo "<audio autoplay>\n";
				echo "\t<source src=\"screaming-goat.mp3\" type=\"audio/mpeg\">\n";
				echo "</audio>\n";
			}
		
			echo "<div class=\"count_data bold italic\">Produkter som saknar s�ljstart, �tg�rdas omedelbart!</div>\n";
			echo "<table id=\"begg_miss5555\" width=\"95%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
		
			while ($res && $row = pg_fetch_object($res)) {

				$trimmaprodukten = $row->beskrivning;
				
				if (strlen($trimmaprodukten) >= 50)
					$trimmaprodukten = substr ($trimmaprodukten, 0, 50) . "...";
				
				echo "\t<tr>";
				// echo "\t\t<td width=\"45\" class=\"mark_black bold blink_text_black\">" . date("H:i", strtotime($row->created)) . "</td>\n";
				echo "\t\t<td width=\"80\" class=\"mark_black bold blink_text_black\">$row->artnr</td>\n";
				echo "\t\t<td class=\"mark_black bold blink_text_black\">$trimmaprodukten</td>\n";
				// echo "\t\t<td class=\"mark_black bold blink_text_black\">$row->name</td>\n";
				echo "\t</tr>\n";
				
				$countrow++;
				
			}
			
			echo "</table>\n";
			if ($countrow > 0) {
				echo "<div class=\"count_data bold\">" . $countrow . " st</div>\n";
			}
		
		}
			
	}

public function getReadyToSell() {

    $h = function($s){
        if ($s === null) return '';
        $s = (string)$s;
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    };

    // ===== Eng�ngs-CSS =====
    static $cssPrinted = false;
    if (!$cssPrinted) {
        echo '<style>
        .notready-table{width:100%;border-collapse:separate;border-spacing:0;margin-top:10px;font-size:14px}
        .notready-table thead th{background:#f9fafb;border-bottom:2px solid #e5e7eb;padding:8px;text-align:left}
        .notready-table td{border-bottom:1px solid #eee;padding:8px}
        .notready-table tbody tr:nth-child(even){background:#f9fafb}
        .notready-table tbody tr:hover{background:#eef2ff}
        .nowrap{white-space:nowrap}
        .muted{color:#6b7280}
        .cta{display:inline-block;padding:6px 10px;border-radius:6px;background:#111827;color:#fff;text-decoration:none}
        .cta:hover{background:#374151}
        .dup-row{border-left:4px solid #dc2626}
        .dup-badge{display:inline-block;margin-left:8px;padding:2px 6px;border-radius:9999px;background:#dc2626;color:#fff;font-size:12px;vertical-align:middle}
        </style>';
        $cssPrinted = true;
    }

    // ===== Eng�ngs-JS =====
    static $jsPrinted = false;
    if (!$jsPrinted) {
        echo '<script>
        function openProductPopup(url){
            var w = 900, h = 800;
            var y = window.top.outerHeight ? Math.max(0, (window.top.outerHeight - h) / 2) : 50;
            var x = window.top.outerWidth  ? Math.max(0, (window.top.outerWidth  - w) / 2) : 50;
            window.open(url, "product_update",
                "toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width="+w+",height="+h+",left="+x+",top="+y);
            return false;
        }
        </script>';
        $jsPrinted = true;
    }

    // ===== SQL =====
    $select = "
SELECT
  prod.value               AS artnr,
  manu.name                AS tillverkare,
  prod.name                AS product_name,
  mloc.value               AS locator_value,
  prod.salestart           AS salestart,
  prod.m_product_id        AS m_product_id,
  prod.m_product_parent_id AS m_product_parent_id
FROM m_product_cache pstock
JOIN m_product     prod    ON prod.m_product_id = pstock.m_product_id
JOIN m_cost        po      ON po.m_product_id   = pstock.m_product_id
JOIN m_product_po  prod_po ON pstock.m_product_id = prod_po.m_product_id
JOIN m_locator     mloc    ON mloc.m_locator_id = prod.m_locator_id
JOIN c_bpartner    cbp     ON cbp.c_bpartner_id = prod_po.c_bpartner_id
JOIN xc_manufacturer manu   ON manu.xc_manufacturer_id = prod.xc_manufacturer_id
WHERE pstock.m_warehouse_id = 1000000
  AND pstock.qtyonhand > 0
  AND po.m_costelement_id = 1000005
  AND po.m_costtype_id    = 1000000
  AND po.ad_client_id     = 1000000
  AND po.isactive         = 'Y'
  AND cbp.value           = '5555'
  AND prod_po.iscurrentvendor = 'Y'
  AND mloc.m_locator_id NOT IN (1000000,1004125,1003683,1003684,1003685,1004170,1004179,1004183,1004184,1004185,1004216,1004217,1004255,1004256)
  AND prod.IsSelfService = 'N'
  AND prod.salestart IS NOT NULL
  AND prod.salestart < CURRENT_DATE
  AND prod.created >= TIMESTAMP '2025-09-01 00:00:00'
ORDER BY manu.name ASC, prod.name ASC
    ";

    $res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
    if (!$res) {
        echo '<div class="muted">Kunde inte h�mta data.</div>';
        return;
    }

    echo '<table class="notready-table">';
    echo '<thead><tr>
            <th>Artikel</th>
            <th>Produkt</th>
            <th class="nowrap">Säljstart</th>
            <th>Plats</th>
            <th style="text-align:right">Åtgärd</th>
          </tr></thead><tbody>';

    $count = 0;
    while ($res && $r = pg_fetch_assoc($res)) {
        $count++;

        $fullName = trim($r['tillverkare'].' '.$r['product_name']);

        // Dubblettkontroll
        $parentId = (int)$r['m_product_parent_id'];
        $raw = null;

        if (method_exists($this, 'findReadyForSaleCheck')) {
            $raw = $this->findReadyForSaleCheck($parentId);
        } elseif (function_exists('findReadyForSaleCheck')) {
            $raw = findReadyForSaleCheck($parentId);
        }

        $isDup = !$raw;

        $urlAdmin = '/product_update.php'
            . '?artnr=' . rawurlencode($r['artnr'])
            . '&m_product_id=' . rawurlencode($r['m_product_id']);

        $urlPublic = 'https://www2.cyberphoto.se/info.php?article='
            . rawurlencode($r['artnr']);

        $trClass = $isDup ? ' class="dup-row"' : '';

        echo '<tr'.$trClass.'>';
        echo '<td class="nowrap">'.$h($r['artnr']).'</td>';
        echo '<td>';
        echo '<a href="'.$h($urlPublic).'" target="_blank" rel="noopener">'.$h($fullName).'</a>';
        if ($isDup) {
            echo '<span class="dup-badge" title="Likadan produkt kan finnas ute redan">Möjlig dubblett</span>';
        }
        echo '</td>';
        echo '<td class="nowrap">'.$h(substr($r['salestart'],0,10)).'</td>';
        echo '<td>'.$h($r['locator_value']).'</td>';
        echo '<td style="text-align:right">'
           . '<a class="cta" href="'.$h($urlAdmin).'" onclick="return openProductPopup(this.href);" rel="noopener">Lägg ut</a>'
           . '</td>';
        echo '</tr>';
    }

    if ($count === 0) {
        echo '<tr><td colspan="5">Inga produkter hittades.</td></tr>';
    }

    echo '</tbody></table>';
}




}
?>

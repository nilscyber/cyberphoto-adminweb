<?php

// require_once("CWebAdempiere.php");
// $web_adempiere = new CWebAdempiere();

Class CStatus {
	
	function __construct() {
          
	}
	
	function show_old_order($kundnr, $show) {
		global $fi, $sv, $no, $REMOTE_ADDR;
		
		$desiderow = true;
		$count_orders = 0;
		$sum_my_order = 0;
	
		// för att strtotime skall fungera måste queryn vara engelsk så den blir rätt tolkad. Därav följande rad: 		
		setlocale (LC_ALL, 'en_US');

		$select  = "SELECT ordernr, inkommet, skickat, leveranssatt_id, levklar, betalsatt_id, totalsumma, currency, gift_card_deduction  ";
		$select .= "FROM cyberphoto.Ordertabell ";
		$select .= "WHERE kundnr = " . $kundnr . " and docstatus IN ('IP', 'CO', 'CL') ";
		$select .= "AND c_doctype_id IN (0,1000030) AND isActive=-1 ";
		$select .= "AND NOT xc_sales_order_status_ID IN (1000002) ";
		$select .= "AND NOT ordernr IN (68966) "; // 151202, specialare för att dölja en order för kundnummer 1003430 som nu innehas av två personer
		$select .= "ORDER BY inkommet DESC";
		$res = mysqli_query(Db::getConnection(), $select);
		
		// echo $select;

		if (mysqli_num_rows($res) > 0 ) {
	
			while ($row = mysqli_fetch_array($res)) {
			
				// om en variable innehåller värdet NULL i nästkommande loop så ändras inte värdet från 
				// loopen före, därför: 
				$inkommet = $skickat = $behandlat = $levklar = "";
				$ordernr = $row['ordernr'];
				extract ($row);	
				$inkommet = preg_replace('/:[0-9][0-9][0-9]/','', $inkommet);
				$skickat = preg_replace('/:[0-9][0-9][0-9]/','', $skickat);

				if (($inkommet != "")) {
					//echo "här:";
					if ($fi)
						$inkommet = strftime ("%m-%d-%Y %R", strtotime($inkommet));
					else
						// $inkommet = strftime ("%Y-%m-%d %R", strtotime($inkommet));
						$inkommet = strftime ("%Y-%m-%d", strtotime($inkommet));
				}
				if (!($skickat == "")) {
					if ($fi)
						$skickat = strftime ("%m-%d-%Y", strtotime($skickat));
					else
						$skickat = strftime ("%Y-%m-%d", strtotime($skickat));
				}

				// $link = "show_order.php?ordernr=" . $ordernr . "&old=no&fi=" . $fi . "&sv=" . $sv;
				// $link = "?show=" . $ordernr . "&old=no&fi=" . $fi . "&sv=" . $sv . "#showorder";
				$link = "?show=" . $ordernr . "&old=no#showorder";
				if ($show == $ordernr) {
					$rowcolor = "orderrow";
				} elseif ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				if ($currency == "") {
					if ($fi) {
						$currency = "EUR";
					} elseif ($no) {
						$currency = "NOK";
					} else {
						$currency = "SEK";
					}
				}
				echo "<tr>\n";
				echo "\t<td class=\"align_center $rowcolor\"><a href=\"" . $link . "\">$ordernr</a></td>\n";
				echo "\t<td class=\"align_center $rowcolor\">" . $inkommet . "</td>\n";
				echo "\t<td class=\"align_right order_list_padding $rowcolor\">" . number_format($totalsumma, 0, ',', ' ') . " " . $currency . "</td>\n";
				echo "\t<td class=\"align_left order_list_padding $rowcolor\">" . $this->levStatusAD($skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok, $ordernr, $skickad_av) . "</td>\n";
				echo "</tr>\n";

				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				
				$count_orders++;
				$sum_my_order = $sum_my_order + $totalsumma;
				
			}

		} else {
			$orderfromAD = true;
		}

		$select = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id, levklar, betalsatt_id, faktura_ok, totalsumma, currency FROM cyberorder.Ordertabell_gamla WHERE kundnr = " . $kundnr . " order by ordernr DESC";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
			echo $select . "<br>". Db::getConnection();
		}
		$res2 = mysqli_query(Db::getConnection(), $select);
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
					echo "yes: " . mysqli_get_server_info(Db::getConnection());
				}
		if (mysqli_num_rows($res2) > 0 ) {


			while ($row = mysqli_fetch_array($res2)) {

			// om en variable innehåller värdet NULL i nästkommande loop så ändras inte värdet från 
				// loopen före, därför: 
				$inkommet = $skickat = $behandlat = $levklar = $betalsatt_id = $leveranssatt_id = $faktura_ok =  NULL;
				unset($skickat);
				//$skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok
				extract ($row);
				$inkommet = preg_replace('/:[0-9][0-9][0-9]/','', $inkommet);
				$skickat = preg_replace('/:[0-9][0-9][0-9]/','', $skickat);			

				if (!($skickat == "")) {
					if ($fi)
						$skickat = strftime ("%m-%d-%Y", strtotime($skickat));
					else
						$skickat = strftime ("%Y-%m-%d", strtotime($skickat));
					//echo ".";
				}
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
					echo $inkommet . "<br>";
				}
				if ($inkommet != "") {
					//echo "här:";
					if ($fi)
						$inkommet = strftime ("%m-%d-%Y %R", strtotime($inkommet));
					else
						// $inkommet = strftime ("%Y-%m-%d %R", strtotime($inkommet));
						// $inkommet = strftime ("%Y-%m-%d", strtotime($inkommet));
						$inkommet = date("Y-m-d", strtotime($behandlat)); // 20160229, ändrat till behandlat för inkommet funkar inte. Tror ingen bryr sig heller...
						// $inkommet = $skickat;
				}

				$ordernr = sprintf("%d", $ordernr);			
				/**
				if ($fi)
					$link = "show_order_fi_n.php?ordernr=$ordernr&old=yes&fi=yes";
				else
					$link = "show_order_n.php?ordernr=$ordernr&old=yes";
				*/
				// $link = "show_order.php?ordernr=" . $ordernr . "&old=yes&fi=" . $fi . "&sv=" . $sv;
				// $link = "?show=" . $ordernr . "&old=yes&fi=" . $fi . "&sv=" . $sv . "#showorder";
				$link = "?show=" . $ordernr . "&old=yes#showorder";
				if ($show == $ordernr) {
					$rowcolor = "orderrow";
				} elseif ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}

				echo "<tr>\n";
				echo "\t<td class=\"align_center $rowcolor\"><a href=\"" . $link . "\">$ordernr</a></td>\n";
				echo "\t<td class=\"align_center $rowcolor\">" . $inkommet . "</td>\n";
				echo "\t<td class=\"align_right order_list_padding $rowcolor\">" . number_format($totalsumma, 0, ',', ' ') . " " . $currency . "</td>\n";
				echo "\t<td class=\"align_left order_list_padding $rowcolor\">" . strtolower($this->levStatus($skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok, $ordernr, $skickad_av)) . "</td>\n";
				echo "</tr>\n";

				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				
				$count_orders++;
				$sum_my_order = $sum_my_order + $totalsumma;
				

				}

			} else {
				$orderfromOLD = true;
			}

			if ($sum_my_order > 0) {
				echo "<tr>\n";
				echo "\t<td colspan=\"2\" class=\"align_left bold\">" . l('Total') . ": " . $count_orders . " " . l('pcs') . "</td>\n";
				echo "\t<td class=\"align_right bold order_list_padding\">" . number_format($sum_my_order, 0, ',', ' ') . " " . $currency . "</td>\n";
				echo "\t<td class=\"align_left bold\"></td>\n";
				echo "</tr>\n";
			}
		
			// för säkerhets skull, om inga ordrar finns lagda. 
			if ($orderfromAD && $orderfromOLD) {

				echo "<tr>\n";
				echo "\t<td colspan=\"3\" class=\"align_left bold italic\">" . l('No orders are registered') . "</td>\n";
				echo "</tr>\n";

				setlocale (LC_ALL, 'sv_SE');
			
			}

	}

	//-------------------------------------------------------------------------------

	function levStatus ($skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok, $ordernr, $skickad_av) {
		global $fi, $sv, $no;

		if ($skickat != "") {
			/*
			// 20/1-10 det känns som att denna funktion är överflödig eftersom vi redan formaterat datumet tidigare / SS
			$skickat = preg_replace('/:[0-9][0-9][0-9]/','', $skickat);
			
			if ($fi)
				$skickat = strftime ("%m-%d-%Y", strtotime($skickat));
			else
				$skickat = strftime ("%Y-%m-%d", strtotime($skickat));
			*/
			if ($fi && !$sv) {
				$output .= "valmis";
			} elseif ($no) {
				$output .= "skickad";
			} else {
				// $output .= "skickad $skickat";
				$output .= "skickad";
			}
		} elseif ($behandlat != "") {
			
			if ($fi)
				if ($skickad_av == 'XB' ) { 
					if ($sv)
						$output .= "Under behandling";
					else
						$output .= "Käsittelyssä";

				} elseif ($betalsatt_id == 5 && $skickad_av == 'XX' ) { 
					if ($fi && !$sv) {
						// $output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
						$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					} elseif ($fi && $sv) {
						// $output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\">klicka här om du vill pröva igen</a>";
						$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\">klicka här om du vill pröva igen</a>";
					} else {
						$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
					}
				} elseif ($betalsatt_id == 10 && $skickad_av == 'XX') {	
					if ($sv)
						$output .= "<b>Obs!</b> Vi väntar på direktbetalning från er <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
					else
						$output .= "<b>Huom !</b> Odotamme suoramaksusuoritustanne <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klikkaamalla tästä voitte tehdä uuden yrityksen</a>";
					
				} else {
					if ($sv)
						$output .= "klar att skicka, skickas inom kort";
					else
						$output .= "valmis lähetettäväksi, lähetetään pikapuolin";
					
				}
			else { 
				
				if ($betalsatt_id == 4) {
					
					if ($faktura_ok != "") {
						if ($leveranssatt_id == 8)
							$output .= "klar för avhämtning";
						else
							$output .= "klar att skicka, skickas inom kort";
					} else {
						$output .= "ännu ej godkänd för fakturering";
					}


				//} elseif ($leveranssatt_id == 8 AND $betalsatt_id == 7) { 
				} elseif ($betalsatt_id == 7) { 
					if ($leveranssatt_id == 8) {
						if ($levklar == 3) 
							$output .= "klar för avhämtning";
						else 
							$output .= "inväntar klartecken från Klarna AB";						
					} else {
						if ($levklar == 3) 
							$output .= "klar att skicka, skickas inom kort";
						elseif ($levklar == 12) 
							$output .= "ordern är makulerad hos Klarna AB";
						else
							$output .= "inväntar klartecken från Klarna AB";						
					}
				}
				elseif ($betalsatt_id == 5 && $skickad_av == 'XX' ) { 
					if ($fi && !$sv) {
						// $output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
						$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					} elseif ($fi && $sv) {
						// $output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
						$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
					} else {
						$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
					}

				
				} elseif ($leveranssatt_id == 8) {
					$output .= "klar för avhämtning";
				} else {
					$output .= "klar att skicka, skickas inom kort";
				}
			}
		} else {


				
			if ($betalsatt_id == 4) {

				if ($faktura_ok != "") {
					if ($leveranssatt_id == 8)
						$output .= "ej fullständig";
					else
						$output .= "ej leveransklar";
				} elseif ($skickad_av == 'ZZ') {
					$output .= "kreditstoppad, vänligen kontakta ekonomiavdelningen, <a href=\"mailto:ekonomi@cyberphoto.se?subject=kreditstoppad order" .  $ordernr . "\">ekonomi@cyberphoto.se</a> eller 090-71 10 97";
				} else {
					$output .= "ännu ej godkänd för fakturering";
				}
			
			} elseif ($betalsatt_id == 7) { 
				if ($leveranssatt_id == 8) {
					if ($levklar == 3) 
						// $output .= "klar för avhämtning"; *** kan denna verkligen stämma?
						$output .= "ej fullständig";
					else 
						$output .= "ej fullständig samt inväntar klartecken från Klarna AB";						
				} else {
					if ($levklar == 3) 
						$output .= "ej fullständig";
					else 
						$output .= "ej fullständig samt inväntar klartecken från Klarna AB";						
				}
			}
			elseif ($betalsatt_id == 5 ) { 
				
				if ($fi && !$sv) {
				// $output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
				$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
				} elseif ($fi && $sv) {
				// $output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
				$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
				} else {
				$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
				}	
			} elseif ($betalsatt_id == 10 && $skickad_av == 'XX') {	
				if ($sv)
					$output .= "<b>Obs!</b> Vi väntar på direktbetalning från er <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
				else
					$output .= "<b>Huom !</b> Odotamme suoramaksusuoritustanne <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klikkaamalla tästä voitte tehdä uuden yrityksen</a>";
				
			} else {
				if (!$sv)
					$output .= "ei toimitusvalmis, katso kyseisen tuotteen yksilötietoja";
				else
					$output .= "ej leveransklar";
			}
		}


		return $output;
		
	}
	

	//--------------------------------------------------------------------------------

	function getKollinr ($ordernr, $land_id = null) {

		global $fi, $sv;
		$ret = "";


		if ($land_id == 46) {

			include ("CConnect_pacsoft.php");	

			$ordernr = sprintf("%d", $ordernr);

			$select  = "SELECT prc.ParcelNo FROM pacsoft.prc, pacsoft.psl ";
			$select .= "WHERE psl.idPSL=prc.idPSL ";
			$select .= "AND codePSS <> 'D' ";
			$select .= "AND CustNo like '$ordernr' ";
			$select .= "ORDER BY prc.ParcelNo ASC ";

			$res = mysqli_query(Db::getConnection(), $select);
			if (mysqli_num_rows($res) > 0) {
				//while ($row = mysqli_fetch_object($res)):		
		#		return "<a href=\"/kollinr.php?kollinr=" . $row->ParcelNo . "\">" . $row->ParcelNo . "</a>" . 
		#			" (klicka för att följa paketet)";

					if ($fi) {
						$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "\">Klikkaamalla tästä voit seurata pakettia</a>";
					}
					else {
						$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "\">klicka för att följa paketet</a>";
					}
					$ret .= "<br>";
				//endwhile;
				return $ret;
			}

			else {

				if ($fi == 'yes')
					return "ei tulostettu";
				else
					return "ej utskrivet";
			}
		}
		else {
			//include ("CConnect.php");	

			$ordernr = trim($ordernr);

			$select  = "SELECT cyberphoto.kollinr FROM Kollinummer WHERE ordern = " . $ordernr;
			$res = mysqli_query(Db::getConnection(), $select);
			if (mysqli_num_rows($res) > 0) {
				//while ($row = mysqli_fetch_object($res)):		
		#		return "<a href=\"/kollinr.php?kollinr=" . $row->ParcelNo . "\">" . $row->ParcelNo . "</a>" . 
		#			" (klicka för att följa paketet)";

					if ($fi == 'yes') {
						$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "\">Klikkaamalla tästä voit seurata pakettia</a>";
					}
					else {
						$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "\">klicka för att följa paketet</a>";
					}
					$ret .= "<br>";

				//endwhile;
				return $ret;
			}

			else {

				if ($fi == 'yes')
					return "ei tulostettu";
				else
					return "ej utskrivet";
			}


		}


		mysqli_close();
		include_once ("CConnect_ms.php");

	}

	function getKollinrOnline ($ordernr, $land_id, $ant_kolli, $returnLink) {

		global $fi, $sv, $old;
		$ret = "";

		//echo "." . $ant_kolli;
		//if ($land_id == 46) {

				

			$ordernr = sprintf("%d", $ordernr);

			$select  = "SELECT prc.ParcelNo FROM pacsoft.prc ";
			$select .= "INNER JOIN psl ON psl.idPSL=prc.idPSL ";
			$select .= "WHERE codePSS <> 'D' ";
			$select .= "AND CustNo like '$ordernr' ";
			$select .= "ORDER BY prc.ParcelNo ASC ";

			/* 2007-09-14 i väntan på att nils kommer tillbaka
			$select  = "SELECT prc.ParcelNo FROM prc, psl ";
			$select .= "WHERE psl.idPSL=prc.idPSL ";
			$select .= "AND codePSS <> 'D' ";
			$select .= "AND CustNo like '$ordernr' ";
			$select .= "ORDER BY prc.ParcelNo ASC ";
			*/
			
			$select  = "SELECT * FROM pacsoft.parcelNoCheck WHERE ordern = " . $ordernr . " AND cancelled = 0 ORDER BY kollinr DESC ";

			$res = mysqli_query(Db::getConnection(), $select);
			$i = 0;
			if (mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_object($res)) :
					//echo $row->kollinr;
					if ($i < $ant_kolli) {
						if ($returnLink) {
							if (!$sv) 
								$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "&old=" . $old . "\">" . $row->kollinr . "</a>" . " (Klikkaamalla tästä voit seurata pakettia)";
							else
								// nödhack tills nils är tillbaka!! $ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "&old=" . $old . "&kollinr=" . $row->kollinr . "\">" . $row->kollinr . "</a>" . " (klicka för att följa paketet)";
								$ret .= "<a target='_blank' href=\"../?kollinr=" . $row->kollinr . "\">" . $row->kollinr . "</a>" . " (klicka för att följa paketet)";
							$ret .= "<br>";														
						} else { // TODO: utan 
							
						}
					}
					/*			
					if ($fi == 'yes') {
						$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "\">Klikkaamalla tästä voit seurata pakettia</a>";
					}
					else {
						$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "\">klicka för att följa paketet</a>";
					}
					*/
						
					$i += 1;
				endwhile;
				return $ret;
			}

			else {

				if ($fi == 'yes')
					return "ei tulostettu";
				else
					return "ej utskrivet";
			}
		/**
		}
		else {
			include ("CConnect.php");	

			$ordernr = trim($ordernr);

			$select  = "SELECT kollinr FROM Kollinummer WHERE ordern = " . $ordernr;
			$res = mysqli_query($select);
			if (mysqli_num_rows($res) > 0) {
				//while ($row = mysqli_fetch_object($res)):		
		#		return "<a href=\"/kollinr.php?kollinr=" . $row->ParcelNo . "\">" . $row->ParcelNo . "</a>" . 
		#			" (klicka för att följa paketet)";

					if ($fi == 'yes') {
						$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "\">Klikkaamalla tästä voit seurata pakettia</a>";
					}
					else {
						$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "\">klicka för att följa paketet</a>";
					}
					$ret .= "<br>";

				//endwhile;
				return $ret;
			}

			else {

				if ($fi == 'yes')
					return "ei tulostettu";
				else
					return "ej utskrivet";
			}


		}
		*/


		mysqli_close();
		include_once ("CConnect_ms.php");

	}
	function getKollinrOnline2 ($ordernr) {
		
		global $fi;

		$ordernr = sprintf("%d", $ordernr);

		$select  = "SELECT prc.ParcelNo FROM pacsoft.prc, pacsoft.psl ";
		$select .= "WHERE psl.idPSL=prc.idPSL ";
		$select .= "AND codePSS <> 'D' ";
		$select .= "AND CustNo like '$ordernr' ";
		$select .= "ORDER BY prc.ParcelNo ASC ";

		$select  = "SELECT kollinr FROM parcelNoCheck WHERE ordern = " . $ordernr;

		$res = mysqli_query(Db::getConnection(), $select);
		if (mysqli_num_rows($res) > 0) {
			$row = mysqli_fetch_object($res);
			return $row->kollinr;
		}	
		else {
			return Null;	
		}
		//mysqli_close();
		//include_once ("CConnect_ms.php");

	}

	//-------------------------------------------------------------------------------
	function getKollinr2 ($ordernr) {

		global $fi;

		$ordernr = sprintf("%d", $ordernr);

		$select  = "SELECT prc.ParcelNo FROM pacsoft.prc, pacsoft.psl ";
		$select .= "WHERE psl.idPSL=prc.idPSL ";
		$select .= "AND codePSS <> 'D' ";
		$select .= "AND CustNo like '$ordernr' ";
		$select .= "ORDER BY prc.ParcelNo ASC ";

		$res = mysqli_query(Db::getConnection(), $select);
		if (mysqli_num_rows($res) > 0) {
			$row = mysqli_fetch_object($res);
			return $row->ParcelNo;
		}	
		else {
			return Null;	
		}
		mysqli_close();
		include_once ("CConnect_ms.php");

	}
	//---------------------------------------------
	function getKollinr3 ($ordernr) {

		global $fi;

		$ordernr = trim($ordernr);

		$select  = "SELECT cyberphoto.kollinr FROM Kollinummer WHERE ordern = " . $ordernr;

		$res = mysqli_query(Db::getConnection(), $select);
		if (mysqli_num_rows($res) > 0) {
			$row = mysqli_fetch_object($res);
			return $row->kollinr;
		}	
		else {
			return Null;	
		}
		
		

	}
	//---------------------------------------------
	function getOrderinfo ($ordernr, $old) {
		//setlocale (LC_ALL, 'en_US');

		//include_once ("CConnect_ms.php");
		global $fi;
		
		if ($old == 'yes') {
			/**
			if ($fi) {
				$select = "SELECT * FROM Ordertabell_gamla_fi WHERE ordernr = " . $ordernr;
				$res = mssql_query ($select, $this->conn_fi);
			} else {
			*/
				
				$select = "SELECT * FROM cyberorder.Ordertabell_gamla WHERE ordernr = " . $ordernr ;
				//echo $select;
				$res = mysqli_query(Db::getConnection(), $select);
			//}
			$orderrow = mysqli_fetch_object($res);
		}

		else {
			/**
			if ($fi) {
				
				$select = "SELECT * FROM Ordertabell_fi WHERE ordernr = " . $ordernr;
				//echo $select;
				$res = mssql_query ($select, $this->conn_fi);
				//echo $res;
			} else {
				*/
				$select = "SELECT * FROM cyberphoto.Ordertabell WHERE ordernr = " . $ordernr ;
				
				//echo $select;
				//echo $this->conn_ms;
				$res = mysqli_query(Db::getConnection(), $select);
			//}
			
			$orderrow = mysqli_fetch_object($res);
		}
	
	return $orderrow;

	}

	//-------------------------------------------------------------------------------
	function getCustomer ($kundnr) {
		$select = "SELECT * FROM cyberorder.Kund WHERE kundnr = " . $kundnr ;
		$res = mysqli_query(Db::getConnection(), $select);

		$kund = mysqli_fetch_object($res);
		
		return $kund;

	}
	function getCountry ($land_id) {
		global $fi, $sv, $no;
		
		$select = "SELECT land, land_fi FROM cyberphoto.Land WHERE land_id = " . $land_id;
		$res = @mysqli_query(Db::getConnection(), $select);
		$row = @mysqli_fetch_object($res);
		if ($fi && !$sv)
			return $row->land_fi;
		else
			return $row->land;
	} 

	//-------------------------------------------------------------------------------

	function getBetalsatt ($betalsatt_id) {
		//include_once ("CConnect_ms.php");
		global $fi, $sv, $no;		
		if (!empty ($betalsatt_id)) {
			$select = "SELECT betalsatt, betalsatt_fi, betalsatt_no FROM cyberphoto.Betalsatt WHERE betalsatt_id = $betalsatt_id";
			$res = mysqli_query(Db::getConnection(), $select);
			$row = mysqli_fetch_object($res);
			
			if ($fi && !$sv) {
				return $row->betalsatt_fi;
			} else {
				
				if ($row->betalsatt_no != "" && $no) {
					return $row->betalsatt_no;
				} else {
					return $row->betalsatt;
				}
			}
			
		} else {
			
			if ($fi && !$sv) {
				return "ei ilmoitettu";
			} elseif ($no) {
				return "ikke oppført";
			} else {
				return "ej angett";
			}
		
		}

	}

	//-------------------------------------------------------------------------------

	function getLeveranssatt ($leveranssatt_id) {
		global $fi, $sv, $no;		
		if (!empty ($leveranssatt_id)) {
			$select = "SELECT leveranssatt, leveranssatt_fi, leveranssatt_no FROM cyberphoto.Leveranssatt WHERE leveranssatt_id = $leveranssatt_id";

			$res = mysqli_query(Db::getConnection(), $select);
			$row = mysqli_fetch_object($res);
			if ($fi && !$sv) {
				return $row->leveranssatt_fi;
			} else {
				if ($row->leveranssatt_no != "" && $no) {
					return $row->leveranssatt_no;
				} else {
					return $row->leveranssatt;
				}
			}
		
		} else {
			
			if ($fi && !$sv) {
				return "ei ilmoitettu";
			} elseif ($no) {
				return "ikke oppført";
			} else {
				return "ej angett";
			}
		
		}
	}

	//-------------------------------------------------------------------------------


	function displayPacName($artnr) {
			global $fi, $sv;
			
				$select = "SELECT CONCAT(Tillverkare.tillverkare, ' ', Artiklar.beskrivning) AS PacBeskrivning, CONCAT(Tillverkare.tillverkare, ' ', Artiklar_fi.beskrivning_fi) AS PacBeskrivning_fi ";
				$select .= "FROM cyberphoto.Artiklar ";
				$select .= "JOIN cyberphoto.Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
				$select .= "JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
				$select .= "WHERE artnr = '" . $artnr . "' ";
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
					echo $select;
					// exit;
					// echo "japp, träff";
				}
				$res = mysqli_query(Db::getConnection(), $select);
				
				if (mysqli_num_rows($res) > 0 ) {

					while ($row = mysqli_fetch_object($res)):
					
						if ($fi && !$sv) {
							return $row->PacBeskrivning_fi;
						} else {
							return $row->PacBeskrivning;
						}
					
					endwhile;
				}
				

	}
	
	function displayPricePac($ordernr,$artnrpac,$tax) {
		global $fi, $sv, $no;
		
		$totalsumpac = 0;
		if ($fi) {
			$valuta = " EUR";
		} elseif ($no) {
			$valuta = " NOK";
		} else {
			$valuta = " SEK";
		}

		$select = "SELECT Orderposter.artnr, Orderposter.antal, Orderposter.utpris, Moms.momssats, Moms.momssats_fi "; 
		$select .= "FROM cyberphoto.Orderposter ";
        $select .= "JOIN cyberphoto.Moms ON Orderposter.momskod = Moms.moms_id ";
        $select .= "WHERE ordernr = '" . $ordernr . "' AND paketArtnr = '" . $artnrpac . "'";
		// echo $select;
		// exit;
			$res = mysqli_query(Db::getConnection(), $select);		
					
			while ($row = mysqli_fetch_object($res)) {
			
				if ($tax) {
					if ($fi) {
						$totalsumpac += ($row->antal*$row->utpris) + (($row->antal*$row->utpris)*$row->momssats_fi);
					} else {
						$totalsumpac += ($row->antal*$row->utpris) + (($row->antal*$row->utpris)*$row->momssats);
					}
				} else {
					$totalsumpac += $row->antal*$row->utpris;
				}
			
			}
			
		// return round($totalsumpac,0) . $valuta;
		return number_format($totalsumpac, 0, ',', ' ') . " " . $valuta;
	
	}
	
	function viewOrderLinesInPac($ordernr,$artnrpac) {
		global $fi, $sv, $no, $web_adempiere, $bask, $doctype;
		
		// echo $fi;
		// exit;
	
		$select = "SELECT Orderposter.artnr, Orderposter.antal, Orderposter.utpris, Orderposter.QtyDelivered, Orderposter.QtyAllocated, Orderposter.DatePromised, Orderposter.DatePromisedPrecision, "; 
		$select .= "Orderposter.beskrivning, Tillverkare.tillverkare, Moms.momssats as momssats, Moms.momssats_fi as momssats_fi, ordernr, Artiklar.kategori_id, Orderposter.Line ";
		$select .= "FROM Artiklar, Orderposter, Moms, Tillverkare  ";
		$select .= "WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND  Artiklar.artnr = Orderposter.artnr AND ";
        $select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND ";
        $select .= "ordernr = '" . $ordernr . "' AND paketArtnr = '" . $artnrpac . "'";
        $select .= "ORDER BY Orderposter.Line ASC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// exit;
			// echo "japp, träff";
		}
		// echo $select;
		// exit;

			$res = mysqli_query(Db::getConnection(), $select);		
					
			while ($row = mysqli_fetch_object($res)) {

				$description = "";

				if ($fi) {
					$val = "EUR";
				} elseif ($no) {
					$val = " NOK";
				} else {
					$val = "SEK";
				}
				if ($orderrow->currency != "")
					$val = $orderrow->currency;
					
				if (eregi("^frakt", $row->artnr)) {
					$fraktkostnad += $row->utpris;
					$fraktkostnadMoms += $row->utpris + ($row->utpris * $row->momssats);
				}

				$goodscounter += 1;

				if ($row->tillverkare != '.')
					$description = $row->tillverkare . " ";

				$description .= $row->beskrivning . " " . $row->kommentar;

				$rowcolor = "p_inpac";

				echo "<tr>\n";
				echo "\t<td class=\"align_left $rowcolor\">&nbsp;&nbsp;-&nbsp;<a href=\"/info.php?article=" . $row->artnr . "\">" . $description . "</a></td>\n";
				echo "\t<td class=\"align_center $rowcolor\">" . $row->antal . "</td>\n";
				echo "\t<td class=\"align_left $rowcolor\">&nbsp;</td>\n";
				echo "\t<td class=\"align_left $rowcolor\">";
				if (preg_match("/^frakt/", $row->artnr)) { // om frakt är inbakad i paketet, göm lagerstatus
					echo "&nbsp;\n";
				} elseif ($row->artnr == "friforsakring") { // om fri försäkring
					echo "&nbsp;";
				} elseif ($row->QtyAllocated == $row->antal) {
					if ($fi && !$sv) { 
						echo "&nbsp;Valmis";
					} elseif ($no) {
						echo "&nbsp;Klar";
					} else {
						echo "&nbsp;Klar";
					}

				} elseif ($row->kategori_id == 595 || $row->kategori_id == 629 || $row->kategori_id == 630) {
					if (!$sv) //TODO: 
						echo "&nbsp;";
					else
						echo "&nbsp;";
				
				} elseif ($row->QtyDelivered == $row->antal) {
					if ($fi && !$sv) { 
						echo "&nbsp;Valmis";
					} elseif ($no) {
						echo "&nbsp;Klar";
					} else {
						echo "&nbsp;Klar";
					}
									
				} else {
					// echo $bask->showDeliveryDate($row->DatePromised, $row->DatePromisedPrecision, $fi, $sv);
					if ($doctype == 1000027) { // om offert, visa ingen status
						echo "&nbsp;";
					} else {
						echo "&nbsp;" . $web_adempiere->showDeliveryDate($row->DatePromised, $row->DatePromisedPrecision, $fi, $sv);
					}
				}
				echo "</td>\n";
				echo "</tr>\n";

			
			}
		
				/*
				echo "<tr>\n";
				echo "\t<td colspan=\"5\" bgcolor=\"#ffffff\" height=\"1\"><img border=\"0\" src=\"/1pix.gif\" height=\"1\"></td>\n";
				echo "</tr>\n";
				*/
	
	}
	
	
	function viewOrderLines($orderrow,$old,$bask) {
			//include_once ("CConnect_ms.php");
			global $fi, $sv, $no, $old_foretag, $web_adempiere, $doctype;

			$desiderow = true;
			$is_freight = false;

				$stack = array();
				// kolla här om det finns några värdepaket
				// $select = "SELECT DISTINCT Orderposter.pacKey ";
				$select = "SELECT DISTINCT Orderposter.paketArtnr ";
				$select .= "FROM cyberphoto.Orderposter ";
				// $select .= "WHERE ordernr = " . $orderrow->ordernr . " AND NOT (Orderposter.pacKey = '') ";
				$select .= "WHERE ordernr = " . $orderrow->ordernr . " AND NOT (Orderposter.paketArtnr = '') ";
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
					echo $select;
					// exit;
				}
				$res = mysqli_query(Db::getConnection(), $select);
				
				if (mysqli_num_rows($res) > 0 ) {
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
						echo "japp, träff";
					}

					while ($row = mysqli_fetch_object($res)):
					
						array_push($stack, $row->paketArtnr);
					
					endwhile;
				}
				
				foreach ($stack as $value) {
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
						// echo "japp, träff";
						echo $value . "<br />";
					}
					
					if ($desiderow == true) {
						$rowcolor = "secondrow";
					} else {
						$rowcolor = "secondrow";
					}
					echo "<tr>\n";
					echo "\t<td class=\"align_left $rowcolor\" colspan=\"2\">" . $this->displayPacName($value) . "</td>\n";
					if ($old_foretag == -1) {
						echo "\t<td class=\"align_right $rowcolor\">" . $this->displayPricePac($orderrow->ordernr,$value,false) . "</td>\n";
					} else {
						echo "\t<td class=\"align_right $rowcolor\">" . $this->displayPricePac($orderrow->ordernr,$value,true) . "</td>\n";
					}
					echo "\t<td class=\"align_left $rowcolor\">&nbsp;</td>\n";
					echo "</tr>\n";
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}
					$this->viewOrderLinesInPac($orderrow->ordernr,$value);
				}


				if ($old == "yes") {
					$select = "SELECT Orderposter_gamla.artnr, Orderposter_gamla.antal, Orderposter_gamla.utpris, Orderposter_gamla.bokad, Orderposter_gamla.beskrivning_alt, Orderposter_gamla.visualPrice, "; 
					$select .= "Orderposter_gamla.rest, Orderposter_gamla.beskrivning, Tillverkare.tillverkare, Moms.momssats as momssats, lagersaldo, bestallt, lev_datum, lev_datum_norm, bestallningsgrans, queue, ordernr ";
					$select .= "FROM cyberphoto.Artiklar, cyberorder.Orderposter_gamla, cyberphoto.Moms, cyberphoto.Tillverkare ";
					$select .= "WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND  Artiklar.artnr = Orderposter_gamla.artnr AND ";
					$select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND (isHidden = 0 OR isHidden IS NULL) AND ";
					$select .= "ordernr = " . $orderrow->ordernr;
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
						echo $select;
						exit;
					}
				} else {

					$select = "SELECT Orderposter.artnr, Orderposter.antal, Orderposter.utpris, Orderposter.QtyDelivered,Orderposter.QtyAllocated,
                                            Orderposter.DatePromised, Orderposter.DatePromisedPrecision,   "; 
                    $select .= "Orderposter.beskrivning, Tillverkare.tillverkare, Moms.momssats as momssats, Moms.momssats_fi as momssats_fi, Moms.momssats_no as momssats_no, ordernr, Artiklar.kategori_id, Artiklar.lagersaldo ";
                    $select .= "FROM Artiklar, Orderposter, Moms, Tillverkare WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND  Artiklar.artnr = Orderposter.artnr AND ";
                    // $select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND NOT (Artiklar.artnr = 'rab') AND ";
                    $select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND ";
                    $select .= "ordernr = " . $orderrow->ordernr;
	
					foreach ($stack as $value) {
						$select .= " AND NOT paketArtnr = '$value'";
					}
					$select .= " ORDER BY line ASC ";
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
						echo $select;
						// exit;
					}
				
				}			
			//}
	
			$res = mysqli_query(Db::getConnection(), $select);		
                        
			$fraktkostnad = 0;
			if ($old == "yes") {
				$w = mysql_fetch_object;
			} else {
				$w = mysql_fetch_object;
		        }
					
			while ($row = $w($res)) {
			$description = "";

			if ($fi) {
				$val = "EUR";
			} elseif ($no) {
				$val = "NOK";
			} else {
				$val = "SEK";
			}
			if ($orderrow->currency != "")
				$val = $orderrow->currency;
				
			if (eregi("^frakt", $row->artnr)) {
				$fraktkostnad += $row->utpris;
				$fraktkostnadMoms += $row->utpris + ($row->utpris * $row->momssats);
				$is_freight = true;
			}

			$goodscounter += 1;

			if ($row->tillverkare != '.')
				$description = $row->tillverkare . " ";

			$description .= $row->beskrivning . " " . $row->kommentar;

			if (strlen($row->beskrivning_alt) > 2) {
				$description = $row->beskrivning_alt;
			}


			// visa bara info om det inte är frakt
			if (!(eregi("^frakt", $row->artnr))):
			if ($desiderow == true) {
				$rowcolor = "secondrow";
			} else {
				$rowcolor = "secondrow";
			}
	?>
			<tr>
			  <?php if (preg_match("/UNHCR/i", $row->artnr) || preg_match("/CFFN10/i", $row->artnr)) { ?>
				<td class="align_left <?php echo $rowcolor; ?>"><a href="/stod-jul"><?php echo $description; ?></a></td>
			  <?php } else { ?>
				<?php if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR']) || CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) { ?>
				<td class="align_left <?php echo $rowcolor; ?>"><a href="/info.php?article=<?php echo $row->artnr; ?>"><?php echo $description; ?></a></td>
				<?php } else { ?>
				<td class="align_left <?php echo $rowcolor; ?>"><?php echo $description; ?></td>
				<?php } ?>
			  <?php } ?>
			  <td class="align_center <?php echo $rowcolor; ?>"><?php echo $row->antal; ?></td>
			  <?php	if ($old_foretag == -1) { ?>

				<td class="align_right <?php echo $rowcolor; ?>">
				<?php
				if ($row->visualPrice > 0) {
					echo number_format($row->visualPrice * $row->antal, 0, ',', ' ') . " " . $val;
				} else {
					echo number_format($row->utpris*$row->antal, 0, ',', ' ') . " " . $val;
				}
				?>
				</td>
			  <?php	} else { ?>
				<td class="align_right <?php echo $rowcolor; ?>">
				<?php
				if ($row->visualPrice > 0) {
					echo number_format(($row->visualPrice * $row->antal) + (($row->visualPrice * $row->antal) * $row->momssats), 0, ',', ' ') . " " . $val; 
				} else {
					if ($fi) {
						echo number_format(($row->utpris*$row->antal) + (($row->utpris*$row->antal) * $row->momssats_fi), 0, ',', ' ') . " " . $val; 
					} else {
						echo number_format(($row->utpris*$row->antal) + (($row->utpris*$row->antal) * $row->momssats), 0, ',', ' ') . " " . $val; 
					}
				}
				?>
				
				</td>
			  <?php	} ?>
			<td class="align_left <?php echo $rowcolor; ?>">

	<?php		if (!(eregi("^frakt", $row->artnr))):

				if (preg_match("/UNHCR/i", $row->artnr) || preg_match("/CFF10/i", $row->artnr)) { // om stöd-jul 2014, visa ingen status
					echo "&nbsp;";
				} elseif ($row->artnr == "friforsakring") { // om fri försäkring
					echo "&nbsp;";
				} elseif ($doctype == 1000027) { // om offert, visa ingen status
					echo "&nbsp;";
				} elseif ($row->bokad == 1) {
					if ($row->rest == 1) {
						if (!$sv)
							echo "Jälkitoimitus, lähetetään myöhemmin";
						else
							echo "Restnoterad, skickas senare";
					}
					else {
						if ($fi && !$sv) {
							echo "&nbsp;Valmis";
						} elseif ($no) {
							echo "&nbsp;Klar";
						} else {
							echo "&nbsp;Klar";
						}
					}
				}

				elseif ($row->bokad == 2) {
					if (!$sv)
						echo "Valmis (lähetetään suoraan meidän toimittajaltamme";
					else
						echo "Klar (skickas direkt från vår leverantör)";
				}

				elseif ($bokad == 4) {
					if (!$sv) //TODO: 
						echo "";
					else
						echo "<font color=\"#85000D\">Produkten är utgången! Vänligen <a href=\"mailto:produkt@cyberphoto.se?subject=Utgången produkt ($artnr) $beskrivning på order $ordernr\"><u><font color=\"#85000D\">kontakta oss</font></u></a> för alterantiv</font>";

				} elseif ($row->kategori_id == 595 || $row->kategori_id == 629 || $row->kategori_id == 630) {
					if (!$sv) //TODO: 
						echo "&nbsp;";
					else
						echo "&nbsp;";
						
				} elseif ($row->artnr == "invoicefee") {
					if (!$sv) // TODO:
						echo "&nbsp;";
					else
						echo "&nbsp;";
						
				}
				
				elseif ($row->QtyAllocated == $row->antal) {
					if ($fi && !$sv) {
						echo "&nbsp;Valmis";
					} elseif ($no) {
						echo "&nbsp;Klar";
					} else {
						echo "&nbsp;Klar";
					}
				}

				elseif ($row->QtyDelivered == $row->antal) {
					if ($fi && !$sv) {
						echo "&nbsp;Valmis";
					} elseif ($no) {
						echo "&nbsp;Klar";
					} else {
						echo "&nbsp;Klar";
					}
				}

				elseif (($row->QtyAllocated+$row->QtyDelivered) == $row->antal) {  // 2019-05-24, la till
					if ($fi && !$sv) {
						echo "&nbsp;Valmis";
					} elseif ($no) {
						echo "&nbsp;Klar";
					} else {
						echo "&nbsp;Klar";
					}
				}

				elseif ($row->artnr == "rab" || $row->lagersaldo == "99999") { // hårdkodat 120827, visar ingen status på artikel rab
					if ($fi && !$sv) {
						echo "&nbsp;Valmis";
					} elseif ($no) {
						echo "&nbsp;Klar";
					} else {
						echo "&nbsp;Klar";
					}
				}
				else {
					// echo "&nbsp;" . $bask->showDeliveryDate($row->DatePromised, $row->DatePromisedPrecision, $fi, $sv);
					echo "&nbsp;" . $web_adempiere->showDeliveryDate($row->DatePromised, $row->DatePromisedPrecision, $fi, $sv);
					// echo $row->DatePromisedPrecision;
					// TODO: 
					//echo kollaLevtid($artnr, $antal, $ordernr, $fi); 
					//if ($fi)
					//	echo $bask->check_lager_bask_fi($row);
					//else 
						//echo $bask->check_lager_bask_se($row);
					

				} ?>
	<?php 		endif; ?>
			</td>
			</tr>

			<?php  endif; // kostnadfri frakt 
			?>
	<?php	
				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
	
			} // slut på endwhile
					
			// Fraktkostnaden sist (om det finns någon)
			if ($is_freight && $fraktkostnad >= 0) {
	?>
			<tr>
			  <td class="align_left <?php echo $rowcolor; ?>"><?php if ($fi && !$sv): ?>Rahti<?php else: ?>Fraktkostnad<?php endif; ?></td>
			  <td class="align_center <?php echo $rowcolor; ?>">1</td>
			<td class="align_right <?php echo $rowcolor; ?>">
			<?php if ($old_foretag == -1) { ?>
				<?php echo number_format($fraktkostnad, 0, ',', ' ') . " " . $val; ?>
			<?php } else { ?>
				<?php echo number_format($fraktkostnadMoms, 0, ',', ' ') . " " . $val; ?>
			<?php } ?>
			</td>
			<td class="align_left <?php echo $rowcolor; ?>">&nbsp;</td>
			</tr>

	<?php		}
	
/*			if ($orderrow->paketRabatt > 0) {
	?>
			<tr>
			  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><b>Paketrabatt</b></font></td>
			  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1">1</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
	<?php 		echo number_format($orderrow->paketRabatt, 0, ',', ' ') . " " . $val; ?>
			</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
	<?php 		echo number_format($orderrow->paketRabatt * 1.25, 0, ',', ' ') . " " . $val; ?>
			</font></td>
			<td bgcolor="#ECECE6" align="left"><font size="1" face="Verdana, Arial">
			<?php if ($fi == 'yes'): ?>
			&nbsp;&nbsp;
			<?php else: ?>
			&nbsp;&nbsp;
			<?php endif; ?>
			</font></td>
			</tr>

	<?php		}	
			if ($orderrow->avtalsRabatt > 0) {
	?>
			<tr>
			  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><b>Avtalsrabatt</b></font></td>
			  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1">1</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
	<?php 		echo number_format($orderrow->avtalsRabatt, 0, ',', ' ') . " " . $val; ?>
			</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
	<?php 		echo number_format($orderrow->avtalsRabatt * 1.25, 0, ',', ' ') . " " . $val; ?>
			</font></td>
			<td bgcolor="#ECECE6" align="left"><font size="1" face="Verdana, Arial">
			<?php if ($fi == 'yes'): ?>
			&nbsp;&nbsp;
			<?php else: ?>
			&nbsp;&nbsp;
			<?php endif; ?>
			</font></td>
			</tr>

	<?php		}	*/
	}
	
	function getIfWebOrder ($ordernr) {

		$select = "SELECT salesrep_id FROM c_order o WHERE o.documentno = '$ordernr' ";
		$res = pg_query (Db::getConnectionAD(), $select);
		$row = pg_fetch_object ($res);
		
		// echo $select;
		// echo Db::getConnectionAD();
		// echo "här: " . $row->salesrep_id;
			
		if ($row->salesrep_id == 1000121) {
			return true;
		} else {
			return false;
		}
				
	}

	function getOrderTime ($ordernr) {
	
		$select = "SELECT inkommet FROM cyberphoto.Ordertabell WHERE ordernr = '$ordernr' ";
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
		return $row->inkommet;
		
	}
	
	function levStatusAD ($skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok, $ordernr, $skickad_av) {
		global $fi, $sv, $no, $old_mobilnr, $old_swishnumber, $directUrl, $orderrow, $add_swish_payment;
		// TODO: new function to find deliveries
		
		
		$select = "SELECT Orderposter.* ";
		$select .= "FROM cyberphoto.Orderposter ";
		$select .= "JOIN cyberphoto.Artiklar ON cyberphoto.Orderposter.artnr = cyberphoto.Artiklar.artnr ";
		$select .= "LEFT JOIN cyberphoto.Kategori ON cyberphoto.Artiklar.kategori_id = cyberphoto.Kategori.kategori_id ";
		// $select .= "WHERE ordernr = " . $ordernr . " AND NOT (Artiklar.kategori_id IN(314)) AND NOT (Artiklar.artnr = 'rab')";
		$select .= "WHERE antal > 0 AND ordernr = " . $ordernr . " AND NOT (cyberphoto.Artiklar.kategori_id IN(314)) ";
		$select .= " AND NOT (cyberphoto.Artiklar.artnr IN  ('rab', 'friforsakring', 'forsakring') )   ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			 echo $select;
		}

		$res = mysqli_query(Db::getConnection(), $select);
		$isDelivered = true;
		$isAllocated = true;
		$isDelivered2 = false; // behövs för att presentera ev. delleveranser
		$isWebOrder = false;
		// $output .= $old_mobilnr;
		if ($this->getIfWebOrder($ordernr)) {
			$isWebOrder = true;
		}
		while ($row = mysqli_fetch_object($res)) {
			if ($row->antal > 0 && $row->artnr != '' ) { // make sure it is an article
				if ($row->QtyDelivered < $row->antal) 
					$isDelivered = false;
			}
			// $output .= $row->antal . ":" . $row->artnr . ":" . $row->QtyAllocated . ":" . $row->QtyDelivered .  "<br>";
			// echo $row->antal . ":" . $row->artnr . ":" . $row->QtyAllocated . ":" . $row->QtyDelivered .  "<br>";
			if ($row->QtyAllocated < $row->antal) {
				$isAllocated = false;
			}
			if ($row->QtyDelivered == $row->antal) {
				$isDelivered2 = true;
			}
			
		}
		// specialare om swish från kassan
		if ($add_swish_payment == 'yes')
			$betalsatt_id = 24;
		
		$ordertime = $this->getOrderTime($ordernr);
		$ordertime = strtotime($ordertime) + 7500; // inom 2 timmar och 5 minuter från orderläggning
		//$output .= "betalsatt: " . $betalsatt_id  . ", levklar: " . $levklar . ":".$isDelivered2 . ":".$isDelivered;
		//if ($skickat != "") {
		if ($isDelivered2 && !$isDelivered) { // om det finns både levererade poster samt icke levererade poster så visas detta alternativ - 120830
		
			if ($fi && !$sv) {
				//$output .= "valmis $skickat";
				$output .= "osatoimitettu ";
			} elseif ($no) {
				$output .= "dellevererad ";
			} else {
				//$output .= "skickad $skickat";
				$output .= "dellevererad ";
			}

			return $output;
		
		}
		//} else if ($betalsatt_id == 24 && ($levklar != 3 || $add_swish_payment=='yes'))  {
		elseif ($isDelivered && $add_swish_payment!='yes') {
			/*
			// 20/1-10 det känns som att denna funktion är överflödig eftersom vi redan formaterat datumet tidigare / SS
			$skickat = preg_replace('/:[0-9][0-9][0-9]/','', $skickat);
			
			if ($fi)
				$skickat = strftime ("%m-%d-%Y", strtotime($skickat));
			else
				$skickat = strftime ("%Y-%m-%d", strtotime($skickat));
			*/
			if ($fi && !$sv) {
				//$output .= "valmis $skickat";
				$output .= "valmis ";
			} elseif ($no) {
				//$output .= "skickad $skickat";
				$output .= "sendt ";
			} else {
				//$output .= "skickad $skickat";
				$output .= "skickad ";
			}
			
			return $output;
		} elseif ($isAllocated) {
				
			if ($fi) {
				if ($skickad_av == 'XB' ) { // has no function but will be replaced (maybe) by locked_to or something similar, kept for reference 
					if ($sv || $no)
						$output .= "Under behandling";
					else
						$output .= "Käsittelyssä";

				} elseif ($betalsatt_id == 5 && $ordertime > time() && $isWebOrder && $levklar == 0 ) { 
					if ($fi && !$sv) {
						$output .= "<b><font color=\"blue\">Korttitapahtuma käsitellään</font></b>";
					} else {
						$output .= "<b><font color=\"blue\">Korttransaktionen behandlas</font></b>";
					}
				} elseif ($betalsatt_id == 5 && $levklar == 0 ) { 
					if ($fi && !$sv) {
						$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan.php?ordernr_check=" . $ordernr . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					} elseif ($fi && $sv) {
						$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
					} else {
						$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
					}
				} elseif ($betalsatt_id == 10 && $levklar == 0) {	
					if ($sv)
						$output .= "<b>Obs!</b> Vi väntar på direktbetalning från er <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
					else
						$output .= "<b>Huom !</b> Odotamme suoramaksusuoritustanne <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klikkaamalla tästä voitte tehdä uuden yrityksen</a>";
					
				} else {
					if ($fi && !$sv) {
						$output .= "valmis lähetettäväksi, lähetetään pikapuolin";
					} else {
						$output .= "klar att skicka, skickas inom kort";
					}
					
				}
				
			} else { 
			
				if ($betalsatt_id == 4) {
					if ($levklar == 3) {
						if ($leveranssatt_id == 8)
							$output .= "klar för avhämtning";
						else
							$output .= "klar att skicka, skickas inom kort";
					} else {
						$output .= "ännu ej godkänd för fakturering";
					}


				//} elseif ($leveranssatt_id == 8 AND $betalsatt_id == 7) { 
				} elseif ($betalsatt_id == 7) { 
					if ($leveranssatt_id == 8) {
						if ($levklar == 3) 
							$output .= "klar för avhämtning";
						else 
							$output .= "inväntar klartecken från Klarna AB";						
					} else {
						if ($levklar == 3) 
							$output .= "klar att skicka, skickas inom kort";
						elseif ($levklar == 12) 
							$output .= "ordern är makulerad hos Klarna AB";
						else
							$output .= "inväntar klartecken från Klarna AB";						
					}
				} elseif ($betalsatt_id == 5 && $ordertime > time() && $isWebOrder && $levklar == 0 ) { 
					if ($fi && !$sv) {
						$output .= "<b><font color=\"blue\">Korttitapahtuma käsitellään</font></b>";
					} elseif ($no) {
						$output .= "<b><font color=\"blue\">Korttransaksjoner behandlet</font></b>";
					} else {
						$output .= "<b><font color=\"blue\">Korttransaktionen behandlas</font></b>";
					}
				} elseif ($betalsatt_id == 5  && $levklar != 3) { 
					if ($fi && !$sv) {
						$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan.php?ordernr_check=" . $ordernr . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					} elseif ($fi && $sv) {
						$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
					} elseif ($no) {
						$output .= "<b>NB!</b><br>Vi venter på at kortbetaling fra deg.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "\"><font color=\"blue\">Klikk her for å registrere kortbetaling</a><br><br>";
					} else {
						$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
					} 
				// ---- Swish (fullständig order = samma som nedan när de är ej fullständiga) -----------
				} else if ($betalsatt_id == 24 && ($levklar != 3 || $add_swish_payment=='yes'))  {
					if (!is_object( $_SESSION['swish'])  ) {
						$swish = new Swish(true);	
						
						$_SESSION['swish'] = $swish; 

					} else  {
						$swish = $_SESSION['swish'];
					} 				
						
					if ($swish->returningToCart) { // = error från swish
						
						// vad vi vill visa för kunderna
						$extra = $swish->CUSTOMER_MESSAGE[$swish->errorCode];

						$extra = $extra . "<br><br>";
						// visas bara en gång (om sidan laddas om) så därför sätta värdet till false efter
						$swish->returningToCart = false;
						$swish = new Swish(true);
					}

					$swish->retry = true;
					$swish->retry_url = "https://" . $_SERVER["HTTP_HOST"] . "" . $_SERVER["REQUEST_URI"];
					$swish->url_waiting_page = "https://" . $_SERVER["HTTP_HOST"] . "" . "/kundvagn/hantera-din-swish-betalning";
					$swish->return_url = "https://" . $_SERVER["HTTP_HOST"] . "" . "/kundvagn/swish_return.php";
					//return_url = 'https://www.cyberphoto.se/kundvagn/swish_return.php';
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx"  ) {

						$swish->setTestRequestError(Swish::ERROR_REQUEST_ACMT03);
					} 
					$output .= "<form action=\"swishMan.php\" method=\"POST\"><input type=\"hidden\" name=\"ordernr_check\" value=\"" . $ordernr . "\" ><table width=\"100%\">
						<tr>
							<td colspan=\"3\" class=\"\"><hr noshade color=\"#ABABAB\" width=\"95%\" size=\"1\"></td>
						</tr>";

					$output .= "
						<tr>
							<td colspan=\"3\" ><b>OBS!</b><br>" . $extra . "Vi väntar på swishbetalningen från er. Ange ett mobilnummer som är kopplat mot Swish</td>
						</tr>
						  <tr>
							<td colspan=\"3\"><input placeholder=\"Exempel: 0701234567\" class=\"inputaddress\" onKeyPress=\"return numbersonly(this, event)\" onfocus=\"this.select()\" maxLength=\"10\" name=\"new_swishnumber\" size=\"18\" value=\"" ;
					 if ($old_swishnumber != "") {
						$old_swishnumber = preg_replace('/[^0-9]/','', $old_swishnumber);
						$output .=  ereg_replace ("[\]", "", $old_swishnumber); 

					 } else if ($directUrl && (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR']) || CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) )  {
						$customer = $this->getCustomer($orderrow->kundnr);

						$mobil = preg_replace('/[^0-9]/','', $customer->mobilnr);

						$output .= $mobil;
					}
						$output .= "\"></td>
						  </tr>
						  <tr>
							<td colspan=\"3\"><input type=\"submit\" name=\"skicka\"/></td>
						  </tr>

					</table></form>";			
				// ---- Slut Swish (fullständig order = samma som nedan när de är ej fullständiga) -----------
				} elseif ($betalsatt_id == 20 || $betalsatt_id == 21) { 
					if ($leveranssatt_id == 8) {
						if ($levklar == 3) 
							$output .= "klar för avhämtning";
						else 
							$output .= "inväntar klartecken från Svea Ekonomi AB";						
					} else {
						if ($levklar == 3) 
							$output .= "klar att skicka, skickas inom kort";
						elseif ($levklar == 12) 
							$output .= "ordern är makulerad hos Svea Ekonomi AB";
						else
							$output .= "inväntar klartecken från Svea Ekonomi AB";						
					}
				} elseif ($leveranssatt_id == 8) {
					$output .= "klar för avhämtning";
				} else {
					if ($fi && !$sv) {
						$output .= "valmis lähetettäväksi, lähetetään pikapuolin";
					} elseif ($no) {
						$output .= "klar til å sende, sendes snart";
					} else {
						$output .= "klar att skicka, skickas inom kort";
					}
				}
			}
		//ej fullt allokerade (ej fullständig)
		} else {				
			if ($betalsatt_id == 4) {

				if ($levklar == 3) {
					if ($leveranssatt_id == 8)
						$output .= "ej fullständig";
					else
						if ($fi && !$sv) {
							$output .= "ei toimitusvalmis";
						} else {
							$output .= "ej leveransklar";
						}
				} elseif ($levklar == 4) {
					$output .= "kreditstoppad, vänligen kontakta ekonomiavdelningen, <a href=\"mailto:ekonomi@cyberphoto.se?subject=kreditstoppad order" .  $ordernr . "\">ekonomi@cyberphoto.se</a> eller 090-200 70 00";
				} else {
					$output .= "ännu ej godkänd för fakturering";
				} 
			} elseif ($betalsatt_id == 7) { 
				if ($leveranssatt_id == 8) {
					if ($levklar == 3) 
						// $output .= "klar för avhämtning"; *** kan denna verkligen stämma?
						$output .= "ej fullständig";
					else 
						$output .= "ej fullständig samt inväntar klartecken från Klarna AB";						
				} else {
					if ($levklar == 3) 
						$output .= "ej fullständig";
					else 
						$output .= "ej fullständig samt inväntar klartecken från Klarna AB";						
				}
			} elseif ($betalsatt_id == 20 || $betalsatt_id == 21) { 
				if ($leveranssatt_id == 8) {
					if ($levklar == 3) 
						$output .= "ej fullständig";
					else 
						$output .= "ej fullständig samt inväntar klartecken från Svea Ekonomi AB";						
				} else {
					if ($levklar == 3) 
						$output .= "ej fullständig";
					else 
						$output .= "ej fullständig samt inväntar klartecken från Svea Ekonomi AB";						
				}
			} elseif ($betalsatt_id == 5 && $ordertime > time() && $isWebOrder && $levklar == 0 ) { 
				if ($fi && !$sv) {
					$output .= "<b><font color=\"blue\">Korttitapahtuma käsitellään</font></b>";
				} elseif ($no) {
					$output .= "<b><font color=\"blue\">Korttransaksjoner behandlet</font></b>";
				} else {
					$output .= "<b><font color=\"blue\">Korttransaktionen behandlas</font></b>";
				}
			} elseif ($betalsatt_id == 5 && $levklar != 3 ) {
				if ($fi && !$sv) {
					// $output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan.php?ordernr_check=" . $ordernr . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
				} elseif ($fi && $sv) {
					// $output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
					$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
				} elseif ($no) {
					$output .= "<b>NB!</b><br>Vi venter på at kortbetaling fra deg.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "\"><font color=\"blue\">Klikk her for å registrere kortbetaling</a><br><br>";
				} else {
					$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
				}	
			// ---- Swish (ej fullständig order = samma som ovan när de är fullständiga) -----------
			} else if ($betalsatt_id == 24 && ($levklar != 3 || $add_swish_payment=='yes'))  {
				//$_SESSION['swish'] = new Swish(true);
				if (!is_object( $_SESSION['swish'])  ) {
					$swish = new Swish(true);	
					
					$_SESSION['swish'] = $swish; 

				} else  {
					$swish = $_SESSION['swish'];
				} 				
					
				if ($swish->returningToCart) { // = error från swish
					
					// vad vi vill visa för kunderna
					$extra = $swish->CUSTOMER_MESSAGE[$swish->errorCode];

					$extra = $extra . "<br><br>";
					// visas bara en gång (om sidan laddas om) så därför sätta värdet till false efter
					$swish->returningToCart = false;
					$swish = new Swish(true);
				}

				$swish->retry = true;
				$swish->retry_url = "https://" . $_SERVER["HTTP_HOST"] . "" . $_SERVER["REQUEST_URI"];
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx"  ) {

					$swish->setTestRequestError(Swish::ERROR_REQUEST_ACMT03);
				} 
				$output .= "<form name=\"swish_send\" action=\"swishMan.php\" method=\"POST\"><input type=\"hidden\" name=\"ordernr_check\" value=\"" . $ordernr . "\" ><table width=\"100%\">
					<tr>
						<td colspan=\"3\" class=\"\"><hr noshade color=\"#ABABAB\" width=\"95%\" size=\"1\"></td>
					</tr>";

				$output .= "
					<tr>
						<td colspan=\"3\" ><b>OBS!</b><br>" . $extra . "Vi väntar på swishbetalningen från er. Ange ett mobilnummer som är kopplat mot Swish</td>
					</tr>
					  <tr>
						<td colspan=\"3\"><input placeholder=\"Exempel: 0701234567\" class=\"inputaddress\" onKeyPress=\"return numbersonly(this, event)\" onfocus=\"this.select()\" maxLength=\"10\" name=\"new_swishnumber\" size=\"18\" value=\"" ;
				 if ($old_swishnumber != "") {
					$old_swishnumber = preg_replace('/[^0-9]/','', $old_swishnumber);
					$output .=  ereg_replace ("[\]", "", $old_swishnumber); 

				 } else if ($directUrl && (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR']) || CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) )  {
					$customer = $this->getCustomer($orderrow->kundnr);

					$mobil = preg_replace('/[^0-9]/','', $customer->mobilnr);

					$output .= $mobil;
				}
					$output .= "\"></td>
					  </tr>
					  <tr>
						<td colspan=\"3\"><input type=\"submit\" name=\"skicka\"/></td>
					  </tr>

				</table></form>";
				if ($add_swish_payment == 'yess') {
					$output .= "
				 <script language=\"JavaScript\">
					document.forms['swish_send'].submit();
					function goTo() {
						document.forms['swish_send'].submit();
					}
				 window.setTimeout (\"goTo()\", 3500);
				 </script>					
					";
				}
			// ---- Slut Swish (ej fullständig order = samma som ovan när de är fullständiga) -----------
			} elseif ($betalsatt_id == 10 && $levklar == 0 ) {	
				if ($sv || $no)
					$output .= "<b>Obs!</b> Vi väntar på direktbetalning från er <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
				else
					$output .= "<b>Huom !</b> Odotamme suoramaksusuoritustanne <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klikkaamalla tästä voitte tehdä uuden yrityksen</a>";
				
			} else {
				if ($fi && !$sv) {
					$output .= "ei toimitusvalmis, katso kyseisen tuotteen yksilötietoja";
				} elseif ($no) {
					$output .= "ikke klar til å sende";
				} else {
					$output .= "ej leveransklar";
				}
			}
		}
		return $output;		
	}

	function viewOrderLinesOriginal($ordernr) {

		$countrow = 1;
		
		$select  = "SELECT CONCAT(Tillverkare.tillverkare, ' ', Orderposter.beskrivning) AS ArtBeskrivning, "; 
		$select .= "Orderposter.paketArtnr, Orderposter.pacKey, Orderposter.artnr, Orderposter.antal, Orderposter.utpris, Orderposter.paketRabatt "; 
		$select .= "FROM cyberorder.Orderposter ";
		$select .= "JOIN cyberphoto.Artiklar ON Artiklar.artnr = Orderposter.artnr ";
		$select .= "JOIN cyberphoto.Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
        $select .= "WHERE Orderposter.ordernr = '" . $ordernr . "' ";
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// exit;
		}

		$res = mysqli_query(Db::getConnection(), $select);		
		
		if (mysqli_num_rows($res) > 0 ) {
					
			while ($row = mysqli_fetch_object($res)) {

				if ($countrow == 1) {
					echo "<div>\n";
					echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"830\">\n";
					echo "<tr>\n";
					echo "\t<th>Antal</th>\n";
					echo "\t<th>Produkt</th>\n";
					echo "\t<th>Paket</th>\n";
					echo "\t<th>Artikel nr</th>\n";
					echo "\t<th>Utpris</th>\n";
					echo "\t<th>Paketrabatt</th>\n";
					echo "</tr>\n";
				}

				echo "<tr>\n";
				echo "\t<td bgcolor=\"#ECECE6\">" . $row->antal . " st</td>\n";
				// echo "\t<td bgcolor=\"#ECECE6\">" . $row->ArtBeskrivning . "</td>\n";
				echo "\t<td bgcolor=\"#ECECE6\"><a target=\"_blank\" href=\"/info.php?article=" . $row->artnr . "\">" . $row->ArtBeskrivning . "</a></td>\n";
				// echo "\t<td bgcolor=\"#ECECE6\">" . $row->paketArtnr . "</td>\n";
				echo "\t<td bgcolor=\"#ECECE6\">" . $row->pacKey . "</td>\n";
				echo "\t<td bgcolor=\"#ECECE6\">" . $row->artnr . "</td>\n";
				echo "\t<td bgcolor=\"#ECECE6\">" . $row->utpris . "</td>\n";
				echo "\t<td bgcolor=\"#ECECE6\">" . round($row->paketRabatt,4) . "</td>\n";
				echo "</tr>\n";
				$countrow++;
			
			}
			
					echo "</table>\n";
					echo "</div>\n";
					$this->viewOrderTotalOriginal($ordernr);
			
		} else {
				echo "<h5>Denna order las inte på webben eller så har ett fel uppstått.</h5>\n";
		}
	
	
	}

	function viewOrderTotalOriginal($ordernr) {

		$select  = "SELECT netto, moms, currency, totalsumma "; 
		$select .= "FROM cyberorder.Ordertabell ";
        $select .= "WHERE ordernr = '" . $ordernr . "' ";
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// exit;
		}

		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
		
		if (mysqli_num_rows($res) > 0 ) {
		
			echo "<div class=\"top10\">Netto: " . $row->netto . "</div>";
			echo "<div class=\"\">Moms: " . $row->moms . "</div>";
			echo "<div class=\"\">Totalt: " . round ($row->netto + $row->moms) . "</div>";
					
			
		} else {
				echo "<h5>Denna order las inte på webben eller så har ett fel uppstått.</h5>\n";
		}
	
	
	}
	
	function viewCustomerOrdersTotal($kundnr) {
		global $ordernr;

		$countrow = 1;
		
		$select  = "SELECT ordernr, inkommet, skickat, leveranssatt_id, levklar, betalsatt_id "; 
		$select .= "FROM cyberphoto.Ordertabell  "; 
		$select .= "WHERE kundnr = " . $kundnr . " and docstatus IN ('IP', 'CO', 'CL') AND c_doctype_id IN (0,1000030) AND isActive=-1 "; 
		$select .= "ORDER BY inkommet DESC "; 

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// exit;
		}

		$res = mysqli_query(Db::getConnection(), $select);		
		
		if (mysqli_num_rows($res) > 0 ) {
					
			while ($row = mysqli_fetch_object($res)) {

				if ($countrow == 1) {
					echo "<hr noshade color=\"#0000FF\" align=\"left\" width=\"850\" size=\"1\">\n";
					echo "<h3>Alla kundens ordrar</h3>\n";
					echo "<div>\n";
					echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"250\">\n";
					echo "<tr>\n";
					echo "\t<th class=\"center\">Order nr</th>\n";
					echo "\t<th class=\"center\">Ordern lagd</th>\n";
					echo "</tr>\n";
				}

				echo "<tr>\n";
				if ($ordernr == $row->ordernr) {
					echo "\t<td align=\"center\" bgcolor=\"#ECECE6\"><b><a href=\"" . $_SERVER['PHP_SELF'] . "?ordernr=" . $row->ordernr . "\">" . $row->ordernr . "</a></b></td>\n";
				} else {
					echo "\t<td align=\"center\" bgcolor=\"#ECECE6\"><a href=\"" . $_SERVER['PHP_SELF'] . "?ordernr=" . $row->ordernr . "\">" . $row->ordernr . "</a></td>\n";
				}
				echo "\t<td align=\"center\" bgcolor=\"#ECECE6\"><a href=\"" . $_SERVER['PHP_SELF'] . "?ordernr=" . $row->ordernr . "\">" . $row->inkommet . "</a></td>\n";
				echo "</tr>\n";
				$countrow++;
			
			}
			
					$this->viewCustomerOrdersTotalOldSys($kundnr);
					echo "</table>\n";
					echo "</div>\n";
			
		} else {
				echo "<h5>Inga övriga ordrar finns lagda på denna kund.</h5>\n";
		}
	
	
	}

	function viewCustomerOrdersTotalOldSys($kundnr) {
		global $ordernr;

		$select  = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id, levklar, betalsatt_id, faktura_ok "; 
		$select .= "FROM cyberorder.Ordertabell_gamla  "; 
		$select .= "WHERE kundnr = " . $kundnr . " "; 
		$select .= "ORDER BY ordernr DESC "; 

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// exit;
		}

		$res = mysqli_query(Db::getConnection(), $select);		
		
		if (mysqli_num_rows($res) > 0 ) {
					
			while ($row = mysqli_fetch_object($res)) {

				echo "<tr>\n";
				if ($ordernr == $row->ordernr) {
					echo "\t<td align=\"center\" bgcolor=\"#ECECE6\"><b><a href=\"" . $_SERVER['PHP_SELF'] . "?ordernr=" . $row->ordernr . "&old=yes\">" . $row->ordernr . "</a></b></td>\n";
				} else {
					echo "\t<td align=\"center\" bgcolor=\"#ECECE6\"><a href=\"" . $_SERVER['PHP_SELF'] . "?ordernr=" . $row->ordernr . "&old=yes\">" . $row->ordernr . "</a></td>\n";
				}
				echo "\t<td align=\"center\" bgcolor=\"#ECECE6\"><a href=\"" . $_SERVER['PHP_SELF'] . "?ordernr=" . $row->ordernr . "&old=yes\">" . $row->inkommet . "</a></td>\n";
				echo "</tr>\n";
			
			}
			
		}
	
	}
	function check_direct_url($order_url, $ordernr) {
	
		if (strlen($order_url) > 10 && is_numeric(trim($ordernr))) {
			// if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			if (!(CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) || CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR']))) {
				// to prevent hacking
				sleep(3);
			}
			$order_url = Tools::sql_inject_clean(trim($order_url));
			$order_check = Tools::sql_inject_clean(trim($ordernr));
			$sel = "SELECT * FROM cyberphoto.Ordertabell WHERE binary order_url = '" . $order_url . "' AND ordernr = " . $order_check;
			$res = mysqli_query(Db::getConnection(), $sel);
			if (mysqli_num_rows($res) > 0) {  
							$row = mysqli_fetch_array($res);
							// check if active
							$sel = 'SELECT borttagen from cyberorder.Kund WHERE kundnr = ' . $row['kundnr'];
							$res = mysqli_query(Db::getConnection(), $sel);
							$row = mysqli_fetch_array($res);
							if ($row['borttagen'] == -1) {
								unset($_SESSION['order_check']);
								unset($_SESSION['orderref']);
								sleep(10);
								return false;					
							}
			
                            $_SESSION['order_check'] == $order_check;
                            $_SESSION['orderref'] == $order_url;  
                            return $order_check;
			} else {
                            unset($_SESSION['order_check']);
                            unset($_SESSION['orderref']);
				// sleep again to mess with hackers
                            sleep(10);
                            return false;
			}
			// TODO: check if order matches domain (.fi, .no, se) otherwise redirect to correct domain
			// TODO: set locale according to order locale
		}            
	
	}

}
?>

<?php

//-------------------------------------------------------------------------------
Class CStatus {
	var $conn_my;
	var $conn_Order;
	
	function __construct() {
		global $fi;

		$this->conn_Order = Db::getConnectionDb('cyberorder');
                
		$this->conn_my = Db::getConnection();
          
	}
	
	function show_old_order($kundnr) {
		global $fi, $sv, $no, $REMOTE_ADDR;
	
		// för att strtotime skall fungera måste queryn vara engelsk så den blir rätt tolkad. Därav följande rad: 		
		setlocale (LC_ALL, 'en_US');
		//setlocale (LC_ALL, 'sv_SE');
		/**
		if ($fi) {
			$select = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id, levklar, betalsatt_id, faktura_ok, skickad_av FROM Ordertabell_fi WHERE kundnr = " . $kundnr . " AND show_order = 0 order by ordernr DESC";
			$res = mssql_query ($select, $this->conn_fi);
		} else {
		*/
			//if ($REMOTE_ADDR == "81.8.240.100")
			//$select = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id, levklar, betalsatt_id, faktura_ok, skickad_av FROM Ordertabell WHERE kundnr = " . $kundnr . " AND show_order = 0 order by ordernr DESC";
			//else
			// $select = "SELECT ordernr, inkommet, skickat, leveranssatt_id, levklar, betalsatt_id FROM Ordertabell WHERE kundnr = " . $kundnr . " and docstatus NOT IN ('DR', 'IN', 'VO') AND isActive=-1 order by inkommet DESC";
			$select = "SELECT ordernr, inkommet, skickat, leveranssatt_id, levklar, betalsatt_id FROM Ordertabell WHERE kundnr = " . $kundnr . " and docstatus IN ('IP', 'CO', 'CL') AND c_doctype_id IN (0,1000030) AND isActive=-1 order by inkommet DESC";

			$res = mysqli_query($this->conn_my, $select);   
		//}
				//$skickat, $behandlat, $betalsatt_id, $leveranssatt_id, $faktura_ok)
	        #echo $select;	
		if (mysqli_num_rows($res) > 0 ) {
		//UNIX_TIMESTAMP('2007-11-30 10:30:19');
	
		while ($row = mysqli_fetch_array($res)):
		
			// om en variable innehåller värdet NULL i nästkommande loop så ändras inte värdet från 
			// loopen före, därför: 
			$inkommet = $skickat = $behandlat = $levklar = "";
			$ordernr = $row['ordernr'];
			extract ($row);	
			//if ($REMOTE_ADDR == "81.8.240.100") {
			//	echo "<br>" . $inkommet;
			//}				
			$inkommet = preg_replace('/:[0-9][0-9][0-9]/','', $inkommet);
			$skickat = preg_replace('/:[0-9][0-9][0-9]/','', $skickat);
			//echo $row->behandlat;
			//setlocale (LC_ALL, 'pl_PL');
			//$skickat;
			//setlocale (LC_ALL, 'en_US');
			//echo $inkommet;
			//echo strtotime($inkommet);
			//if (eregi("81.8.240.100", $REMOTE_ADDR)) {

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

			//$ordernr = sprintf("%d", $ordernr);
			/**
			if ($fi)
				$link = "show_order_fi_n.php?ordernr=$ordernr&old=no&fi=yes";
			else
				$link = "show_order_.php?ordernr=$ordernr&old=no";
			*/
			$link = "show_order.php?ordernr=" . $ordernr . "&old=no&fi=" . $fi . "&sv=" . $sv;
			
$output .= <<<eof
   <tr>
     <td valign="top" width="40"><font size="1" face="Verdana, Arial"><a href="$link">$ordernr</a></font>
     </td>
     <td valign="top" width="110"><font face="Verdana, Arial" size="1">$inkommet</font>
     </td>
     <td valign="top" width="170"><font face="Verdana, Arial" size="1">
eof;
	// levStatus funktionen, ändra betalsatt_id till att hantera betalsatt istället. 
	// skillnad mot tidigare är egentligen bara att faktura_ok inte gäller utan istället samma som på klarna status. Samma sak även för betalkort, är inte 
	// betalkortet aktiverat så är levklar inte satt som leveransklar. 
	$output .= $this->levStatusAD($skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok, $ordernr, $skickad_av);


$output .= <<<eof
	</font>

     </td>
   </tr>
eof;

		endwhile;

		}
	/**
	if ($fi)
		$select = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id, levklar, betalsatt_id, faktura_ok, skickad_av FROM Ordertabell_gamla_fi WHERE kundnr = " . $kundnr . " order by ordernr DESC";
	else
	*/
		$select = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id, levklar, betalsatt_id, faktura_ok FROM Ordertabell_gamla WHERE kundnr = " . $kundnr . " order by ordernr DESC";
		
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98") {
		//echo $select . "<br>". $this->conn_Order;
	}
	$res2 = /* TODO: mysql_db_query replaced - needs manual review (was selecting db + querying) */ mysqli_query("cyberorder",$select, $this->conn_Order);
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98") {
                //echo "yes: " . mysqli_get_server_info($this->conn_Order);
            }
	if (mysqli_num_rows($res2) > 0 ) {


		while ($row = mysqli_fetch_array($res2)):
			// om en variable innehåller värdet NULL i nästkommande loop så ändras inte värdet från 
			// loopen före, därför: 
			$inkommet = $skickat = $behandlat = $levklar = $betalsatt_id = $leveranssatt_id = $faktura_ok =  NULL;
			unset($skickat);
			//$skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok
			extract ($row);
			$inkommet = preg_replace('/:[0-9][0-9][0-9]/','', $inkommet);
			$skickat = preg_replace('/:[0-9][0-9][0-9]/','', $skickat);			
			//$skickat = substr($skickat, 0, 10);
			//echo $skickat;
			//$skickat = $skickat;
			//$inkommet = $inkommet;
			//setlocale (LC_ALL, 'pl_PL');
			//$skickat = cstr($skickat);
			//$output .= ".";
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
				//echo ".";
			}
			if ($_SERVER['REMOTE_ADDR'] == "81.8.240.115") {
				// echo $skickat . "<br>";
			}

			$ordernr = sprintf("%d", $ordernr);			
			/**
			if ($fi)
				$link = "show_order_fi_n.php?ordernr=$ordernr&old=yes&fi=yes";
			else
				$link = "show_order_n.php?ordernr=$ordernr&old=yes";
			*/
			$link = "show_order.php?ordernr=" . $ordernr . "&old=yes&fi=" . $fi . "&sv=" . $sv;
$output .= <<<eof
   <tr>
     <td valign="top" width="40"><font size="1" face="Verdana, Arial"><a href="$link">$ordernr</a></font>
     </td>
     <td valign="top" width="110"><font face="Verdana, Arial" size="1">$inkommet</font>
     </td>
     <td valign="top" width="170"><font face="Verdana, Arial" size="1">
eof;

	$output .= $this->levStatus($skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok, $ordernr, $skickad_av);	
	//$output .= $skickat;
	//$output .= ".";
/*			if ($skickat != "") {
				if (!$sv)
					$output .= "lähetetty $skickat";
				else
					$output .= "skickad $skickat";
			}
			elseif ($behandlat != "") {
				if (!$sv)
					$output .= "valmis lähetettäväksi, lähetetään pikauolin";
				else {
					if ($leveranssatt_id == 8 AND $betalsatt_id == 7) { 
						if ($levklar == -1) 
							$output .= "klar för avhämtning";
						else 
							$output .= "inväntar klartecken från handelsbanken finans";
					}
					elseif ($leveranssatt_id == 8)
						$output .= "klar för avhämtning";
					else 
						$output .= "klar att skicka, skickas inom kort";
				}
			}
			else {
				if (!$fi)
					$output .= "ei lähetysvalmis";
				else
					$output .= "ej leveransklar";
			}
*/

$output .= <<<eof
	</font>

     </td>
   </tr>
eof;


		endwhile;

		}


	// för säkerhets skull, om inga ordrar finns lagda. 
		if (empty($output)) {

			$output = "<tr><td align=\"left\" colspan=\"3\"><font face=\"Verdana\" size=\"1\">";
			if (!$sv)
				$output .= "Ei tilauksia";
			else
				$output .= "Inga ordrar finns registrerade";

			$output .= "</font></td></tr>";

			}
		setlocale (LC_ALL, 'sv_SE');
		return $output;	

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
				$output .= "skickad $skickat";
			} elseif ($no) {
				$output .= "valmis $skickat";
			} else {
				$output .= "valmis $skickat";
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
						$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					} elseif ($fi && $sv) {
						// $output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\">klicka här om du vill pröva igen</a>";
						$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\">klicka här om du vill pröva igen</a>";
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
						$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					} elseif ($fi && $sv) {
						// $output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
						$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
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
				$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
				} elseif ($fi && $sv) {
				// $output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
				$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
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

			$select  = "SELECT prc.ParcelNo FROM prc, psl ";
			$select .= "WHERE psl.idPSL=prc.idPSL ";
			$select .= "AND codePSS <> 'D' ";
			$select .= "AND CustNo like '$ordernr' ";
			$select .= "ORDER BY prc.ParcelNo ASC ";

			$res = mysqli_query($select);
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


		mysqli_close();
		include_once ("CConnect_ms.php");

	}

	function getKollinrOnline ($ordernr, $land_id, $ant_kolli, $returnLink) {

		global $fi, $sv, $old;
		$ret = "";

		//echo "." . $ant_kolli;
		//if ($land_id == 46) {

			include ("CConnect_pacsoft.php");	

			$ordernr = sprintf("%d", $ordernr);

			$select  = "SELECT prc.ParcelNo FROM prc ";
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
			
			$select  = "SELECT * FROM parcelNoCheck WHERE ordern = " . $ordernr . " AND cancelled = 0 ORDER BY kollinr DESC ";

			$res = mysqli_query($select);
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
		include ("CConnect_pacsoft.php");
		global $fi;

		$ordernr = sprintf("%d", $ordernr);

		$select  = "SELECT prc.ParcelNo FROM prc, psl ";
		$select .= "WHERE psl.idPSL=prc.idPSL ";
		$select .= "AND codePSS <> 'D' ";
		$select .= "AND CustNo like '$ordernr' ";
		$select .= "ORDER BY prc.ParcelNo ASC ";

		$select  = "SELECT kollinr FROM parcelNoCheck WHERE ordern = " . $ordernr;

		$res = mysqli_query($select);
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
		include ("CConnect_pacsoft.php");
		global $fi;

		$ordernr = sprintf("%d", $ordernr);

		$select  = "SELECT prc.ParcelNo FROM prc, psl ";
		$select .= "WHERE psl.idPSL=prc.idPSL ";
		$select .= "AND codePSS <> 'D' ";
		$select .= "AND CustNo like '$ordernr' ";
		$select .= "ORDER BY prc.ParcelNo ASC ";

		$res = mysqli_query($select);
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
		//include ("CConnect.php");
		global $fi;

		$ordernr = trim($ordernr);

		$select  = "SELECT kollinr FROM Kollinummer WHERE ordern = " . $ordernr;

		$res = mysqli_query($this->conn_my, $select);
		if (mysqli_num_rows($res) > 0) {
			$row = mysqli_fetch_object($res);
			return $row->kollinr;
		}	
		else {
			return Null;	
		}
		mysqli_close();
		include_once ("CConnect_ms.php");

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
				
				$select = "SELECT * FROM Ordertabell_gamla WHERE ordernr = " . $ordernr ;
				//echo $select;
				$res = /* TODO: mysql_db_query replaced - needs manual review (was selecting db + querying) */ mysqli_query("cyberorder",$select, $this->conn_Order);
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
				$select = "SELECT * FROM Ordertabell WHERE ordernr = " . $ordernr ;
				
				//echo $select;
				//echo $this->conn_ms;
				$res = /* TODO: mysql_db_query replaced - needs manual review (was selecting db + querying) */ mysqli_query("cyberphoto",$select, $this->conn_my);
			//}
			
			$orderrow = mysqli_fetch_object($res);
		}
	
	return $orderrow;

	}

	//-------------------------------------------------------------------------------

	function getCountry ($land_id) {
		global $fi, $sv;
		
		$select = "SELECT land, land_fi FROM Land WHERE land_id = " . $land_id;
		$res = @mysqli_query($this->conn_my, $select);
		$row = @mysqli_fetch_object($res);
		if (!$sv)
			return $row->land_fi;
		else
			return $row->land;
	} 

	//-------------------------------------------------------------------------------

	function getBetalsatt ($betalsatt_id) {
		//include_once ("CConnect_ms.php");
		global $fi, $sv;		
		if (!empty ($betalsatt_id)) {
			$select = "SELECT betalsatt, betalsatt_fi FROM Betalsatt WHERE betalsatt_id = $betalsatt_id";
			$res = mysqli_query($this->conn_my, $select);
			$row = mysqli_fetch_object($res);
			if (!$sv)
				return $row->betalsatt_fi;
			else
				return $row->betalsatt;
		}
		else {
			if (!$sv)
				return "ei ilmoitettu";
			else
				return "ej angett";
		}

	}

	//-------------------------------------------------------------------------------

	function getLeveranssatt ($leveranssatt_id) {
		//include_once ("CConnect_ms.php");
		global $fi, $sv;		
		if (!empty ($leveranssatt_id)) {
			$select = "SELECT leveranssatt, leveranssatt_fi FROM Leveranssatt WHERE leveranssatt_id = $leveranssatt_id";

			$res = mysqli_query($this->conn_my, $select);
			$row = mysqli_fetch_object($res);
			if (!$sv)	
				return $row->leveranssatt_fi;
			else
				return $row->leveranssatt;
		}
		else {
			if (!$sv)
				return "ei ilmoitettu";
			else
				return "ej angett";
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
				$res = mysqli_query($this->conn_my, $select);
				
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
			$res = mysqli_query($this->conn_my, $select);		
					
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
			
		return round($totalsumpac,0) . $valuta;
	
	}
	
	function viewOrderLinesInPac($ordernr,$artnrpac) {
		global $fi, $sv, $no, $web_adempiere, $bask;
		
		// echo $fi;
		// exit;
	
		$select = "SELECT Orderposter.artnr, Orderposter.antal, Orderposter.utpris, Orderposter.QtyDelivered, Orderposter.QtyAllocated, Orderposter.DatePromised, Orderposter.DatePromisedPrecision, "; 
		$select .= "Orderposter.beskrivning, Tillverkare.tillverkare, Moms.momssats as momssats, Moms.momssats_fi as momssats_fi, ordernr, Artiklar.kategori_id ";
		$select .= "FROM Artiklar, Orderposter, Moms, Tillverkare  ";
		$select .= "WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND  Artiklar.artnr = Orderposter.artnr AND ";
        $select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND ";
        $select .= "ordernr = '" . $ordernr . "' AND paketArtnr = '" . $artnrpac . "'";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// exit;
			// echo "japp, träff";
		}
		// echo $select;
		// exit;

			$res = mysqli_query($this->conn_my, $select);		
					
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

				echo "<tr>\n";
				echo "\t<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;-&nbsp;" . $description . "</font></td>\n";
				echo "\t<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">" . $row->antal . "</font></td>\n";
				echo "\t<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;</font></td>\n";
				echo "\t<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;</font></td>\n";
				echo "\t<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">";
				if ($row->QtyAllocated == $row->antal) {
					if (!$sv) //TODO: 
						echo "&nbsp;Valmis";
					else
						echo "&nbsp;Klar";

				} elseif ($row->kategori_id == 595 || $row->kategori_id == 629 || $row->kategori_id == 630) {
					if (!$sv) //TODO: 
						echo "&nbsp;";
					else
						echo "&nbsp;";
				
				} elseif ($row->QtyDelivered == $row->antal) {
					if (!$sv) //TODO: 
						echo "&nbsp;Valmis";
					else
						echo "&nbsp;Klar";
				
				} else {
					echo $bask->showDeliveryDate($row->DatePromised, $row->DatePromisedPrecision, $fi, $sv);
					// echo $web_adempiere->showDeliveryDate($row->DatePromised, $row->DatePromisedPrecision, $fi, $sv);
				}
				echo "</font></td>\n";
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
			global $fi, $sv, $no;

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
				$res = mysqli_query($this->conn_my, $select);
				
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
					echo "<tr>\n";
					echo "\t<td colspan=\"2\" bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\"><b><i>" . $this->displayPacName($value) . "</i></b></font></td>\n";
					echo "\t<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">" . $this->displayPricePac($orderrow->ordernr,$value,false) . "</font></td>\n";
					echo "\t<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">" . $this->displayPricePac($orderrow->ordernr,$value,true) . "</font></td>\n";
					echo "\t<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;</font></td>\n";
					echo "</tr>\n";
					$this->viewOrderLinesInPac($orderrow->ordernr,$value);
				}


			//	global	$count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue, 
			//	$est_delivery, $fi, $sv, $release, $fi, $sv;
			/**
			if ($fi) {
				if ($old == "yes") {
					$select = "SELECT Orderposter_gamla_fi.artnr, Orderposter_gamla_fi.antal, Orderposter_gamla_fi.utpris, Orderposter_gamla_fi.bokad, "; 
					$select .= "Orderposter_gamla_fi.rest, Orderposter_gamla_fi.beskrivning, Tillverkare.tillverkare, Moms.momssats_fi as momssats, lagersaldo_fi as lagersaldo, bestallt_fi as bestallt, lev_datum_fi as lev_datum, lev_datum_norm_fi as lev_datum_norm, bestallningsgrans_fi as bestallningsgrans, queue_fi as queue, release, ordernr ";
					$select .= "FROM Artiklar, Artiklar_fi, Orderposter_gamla_fi, Moms, Tillverkare WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND  Artiklar.artnr = Orderposter_gamla_fi.artnr AND Artiklar_fi.artnr_fi = Artiklar.artnr AND ";
					$select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND ";
					$select .= "ordernr = " . $orderrow->ordernr;
				}
				else {		
					$select = "SELECT Orderposter_fi.artnr, Orderposter_fi.antal, Orderposter_fi.utpris, Orderposter_fi.bokad, "; 
					$select .= "Orderposter_fi.rest, Orderposter_fi.beskrivning, Tillverkare.tillverkare, Moms.momssats_fi as momssats, lagersaldo, bestallt, lev_datum, lev_datum_norm, bestallningsgrans, queue, release, ordernr ";
					$select .= "FROM Artiklar, Artiklar_fi, Orderposter_fi, Moms, Tillverkare WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.artnr = Orderposter_fi.artnr AND Artiklar_fi.artnr_fi = Artiklar.artnr AND ";
					$select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND ";
					$select .= "ordernr = " . $orderrow->ordernr;
				}
			} else {*/
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
					/*
					$select = "SELECT Orderposter.artnr, Orderposter.antal, Orderposter.utpris, Orderposter.bokad, Orderposter.beskrivning_alt, Orderposter.visualPrice, "; 
					$select .= "Orderposter.rest, Orderposter.beskrivning, Tillverkare.tillverkare, Moms.momssats as momssats, Moms.momssats_fi as momssats_fi, lagersaldo, bestallt, lev_datum, lev_datum_norm, bestallningsgrans, queue, release, ordernr ";
					$select .= "FROM Artiklar, Orderposter, Moms, Tillverkare WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND  Artiklar.artnr = Orderposter.artnr AND ";
					// $select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND NOT (isHidden = -1) AND ";
					$select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND (isHidden = 0 OR isHidden IS NULL) AND ";
					$select .= "ordernr = " . $orderrow->ordernr;
					*/

					$select = "SELECT Orderposter.artnr, Orderposter.antal, Orderposter.utpris, Orderposter.QtyDelivered,Orderposter.QtyAllocated,
                                            Orderposter.DatePromised, Orderposter.DatePromisedPrecision,   "; 
                    $select .= "Orderposter.beskrivning, Tillverkare.tillverkare, Moms.momssats as momssats, Moms.momssats_fi as momssats_fi, Moms.momssats_no as momssats_no, ordernr, Artiklar.kategori_id ";
                    $select .= "FROM Artiklar, Orderposter, Moms, Tillverkare WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND  Artiklar.artnr = Orderposter.artnr AND ";
                    // $select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND NOT (Artiklar.artnr = 'rab') AND ";
                    $select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND ";
                    $select .= "ordernr = " . $orderrow->ordernr;
	
					foreach ($stack as $value) {
						$select .= " AND NOT paketArtnr = '$value'";
					}
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
						echo $select;
						// exit;
					}
				
				}			
			//}
		        if ($old == "yes")	
				$res = mysqli_query($this->conn_Order, $select);
		        else
				$res = mysqli_query($this->conn_my, $select);		
			$fraktkostnad = 0;
			if ($old == "yes") {
				$w = mysql_fetch_object;
			} else {
				$w = mysql_fetch_object;
		        }
					
			while ($row = $w($res)) {
			$description = "";
			// Om en variable innehåller NULL i en loop så skrivs inte NULL värdet in i variabeln utan det gamla sparas
			// därför: 
			//$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = "";
			//echo $row['artnr'];
			//extract($row);
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
	?>
			<tr>
			  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php echo $description; ?></font></td>
			  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1"><?php echo $row->antal; ?></font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
			<?php
			if ($row->visualPrice > 0) {
				echo number_format($row->visualPrice * $row->antal, 0, ',', ' ') . " " . $val;
			} else {
				echo number_format($row->utpris*$row->antal, 0, ',', ' ') . " " . $val;
			}
			?>
			</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
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
			
			</font></td>
			<td bgcolor="#ECECE6" align="left"><font size="1" face="Verdana, Arial">

	<?php		if (!(eregi("^frakt", $row->artnr))):

				if ($row->bokad == 1) {
					if ($row->rest == 1) {
						if (!$sv)
							echo "Jälkitoimitus, lähetetään myöhemmin";
						else
							echo "Restnoterad, skickas senare";
					}
					else {
						if (!$sv)
							echo "Valmis";
						else
							echo "Klar";
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
					if (!$sv) //TODO: 
						echo "&nbsp;";
					else
						echo "&nbsp;";
						
				}
				
				elseif ($row->QtyAllocated == $row->antal) {
					if (!$sv) //TODO: 
						echo "&nbsp;Valmis";
					else
						echo "&nbsp;Klar";
				}

				elseif ($row->QtyDelivered == $row->antal) {
					if (!$sv) //TODO: 
						echo "&nbsp;Valmis";
					else
						echo "&nbsp;Klar";
				}

				elseif ($row->artnr == "rab") { // hårdkodat 120827, visar ingen status på artikel rab
					if (!$sv) //TODO: 
						echo "&nbsp;Valmis";
					else
						echo "&nbsp;Klar";
				}
				else {
					echo $bask->showDeliveryDate($row->DatePromised, $row->DatePromisedPrecision, $fi, $sv);
					// TODO: 
					//echo kollaLevtid($artnr, $antal, $ordernr, $fi); 
					//if ($fi)
					//	echo $bask->check_lager_bask_fi($row);
					//else 
						//echo $bask->check_lager_bask_se($row);
					

				} ?>
	<?php 		endif; ?>
			</font></td>
			</tr>

			<?php  endif; // kostnadfri frakt 
			?>
	<?php	} // slut på endwhile
					
			// Fraktkostnaden sist (om det finns någon)
			if ($fraktkostnad > 0) {
	?>
			<tr>
			  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% if (!$sv): %>Rahti<% else: %>Fraktkostnad<% endif; %></font></td>
			  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1">1</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
	<?php 		echo number_format($fraktkostnad, 0, ',', ' ') . " " . $val; ?>
			</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
	<?php 		echo number_format($fraktkostnadMoms, 0, ',', ' ') . " " . $val; ?>
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
		
	function levStatusAD ($skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok, $ordernr, $skickad_av) {
		global $fi, $sv, $no;
		// TODO: new function to find deliveries
		$select = "SELECT * ";
		$select .= "FROM Orderposter ";
		$select .= "JOIN Artiklar ON Orderposter.artnr = Artiklar.artnr ";
		$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
		// $select .= "WHERE ordernr = " . $ordernr . " AND NOT (Artiklar.kategori_id IN(314)) AND NOT (Artiklar.artnr = 'rab')";
		$select .= "WHERE antal > 0 AND ordernr = " . $ordernr . " AND NOT (Artiklar.kategori_id IN(314)) AND NOT (Artiklar.artnr = 'rab')";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			 echo $select;
		}
	 		
		$res = mysqli_query($this->conn_my, $select);
		$isDelivered = true;
		$isAllocated = true;
		$isDelivered2 = false; // behövs för att presentera ev. delleveranser
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
		elseif ($isDelivered) {
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
				$output .= "skickad ";
			} else {
				//$output .= "skickad $skickat";
				$output .= "skickad ";
			}

			return $output;
		} elseif ($isAllocated) {
				
			if ($fi)
				if ($skickad_av == 'XB' ) { // has no function but will be replaced (maybe) by locked_to or something similar, kept for reference 
					if ($sv || $no)
						$output .= "Under behandling";
					else
						$output .= "Käsittelyssä";

				} elseif ($betalsatt_id == 5 && $levklar == 0 ) { 
					if ($fi && !$sv) {
						$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					} elseif ($fi && $sv) {
						$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan_fi_se.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\">klicka här om du vill pröva igen</a>";
					} else {
						$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
					}
				} elseif ($betalsatt_id == 10 && $levklar == 0) {	
					if ($sv)
						$output .= "<b>Obs!</b> Vi väntar på direktbetalning från er <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
					else
						$output .= "<b>Huom !</b> Odotamme suoramaksusuoritustanne <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klikkaamalla tästä voitte tehdä uuden yrityksen</a>";
					
				} else {
					if ($sv || $no)
						$output .= "klar att skicka, skickas inom kort";
					else
						$output .= "valmis lähetettäväksi, lähetetään pikapuolin";
					
				}
			else { 
			
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
				}
				elseif ($betalsatt_id == 5  && $levklar == 0) { 
					if ($fi && !$sv) {
						$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					} elseif ($fi && $sv) {
						$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan_fi_se.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
					} else {
						$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
					} 
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
					$output .= "klar att skicka, skickas inom kort";
				}
			}
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
			}
			elseif ($betalsatt_id == 5 && $levklar == 0 ) {
				if ($fi && !$sv) {
					// $output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
				} elseif ($fi && $sv) {
					// $output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan_fi_f.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
					$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
				} else {
					$output .= "<b>OBS!</b><br>Vi väntar på kortbetalningen från er.<br><a href=\"kortMan.php?ordernr_check=" . $ordernr . "\"><font color=\"blue\">Klicka här för att registrera kortbetalning</a><br><br>";
				}	
			} elseif ($betalsatt_id == 10 && $levklar == 0 ) {	
				if ($sv || $no)
					$output .= "<b>Obs!</b> Vi väntar på direktbetalning från er <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
				else
					$output .= "<b>Huom !</b> Odotamme suoramaksusuoritustanne <a href=\"direktRetryVerkko.php?ordernr_check=" . $ordernr . "\">klikkaamalla tästä voitte tehdä uuden yrityksen</a>";
				
			} else {
				if ($fi && !$sv)
					$output .= "ei toimitusvalmis, katso kyseisen tuotteen yksilötietoja";
				else
					$output .= "ej leveransklar";
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

		$res = mysqli_query($this->conn_my, $select);		
		
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

		$res = mysqli_query($this->conn_my, $select);		
		
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

		$res = mysqli_query($this->conn_my, $select);		
		
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
	
}
?>

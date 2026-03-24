<?php

//-------------------------------------------------------------------------------
Class CStatus {
	var $conn_ms; var $conn_my; var $conn_fi;
	
	function CStatus() {
		global $fi;
		include_once ("CConnect_ms.php");
		$this->conn_ms = $conn_ms;
		include_once ("CConnect.php");
		$this->conn_my = $conn_my;
	}
	
	function show_old_order($kundnr) {
		global $fi, $sv;
		// för att strtotime skall fungera måste queryn vara engelsk så den blir rätt tolkad. Därav följande rad: 		
		setlocale (LC_ALL, 'en_US');
		
		if ($fi) {
			$select = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id, levklar, betalsatt_id, faktura_ok, skickad_av FROM Ordertabell_fi WHERE kundnr = " . $kundnr . " AND show_order = 0 order by ordernr DESC";
			$res = mssql_query ($select, $this->conn_fi);
		} else {
			$select = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id, levklar, betalsatt_id, faktura_ok, skickad_av FROM Ordertabell WHERE kundnr = " . $kundnr . " AND show_order = 0 order by ordernr DESC";
			$res = mssql_query ($select, $this->conn_ms);
		}
				//$skickat, $behandlat, $betalsatt_id, $leveranssatt_id, $faktura_ok)
		if (mssql_num_rows ($res) > 0 ) {
//		echo $select;
		while ($row = mssql_fetch_array($res)):
			// om en variable innehåller värdet NULL i nästkommande loop så ändras inte värdet från 
			// loopen före, därför: 
			$inkommet = $skickat = $behandlat = $levklar = "";
			$ordernr = $row['ordernr'];
			extract ($row);	
			//echo $row->behandlat;
			//setlocale (LC_ALL, 'pl_PL');
			//$skickat;
			//setlocale (LC_ALL, 'en_US');
			//echo $row['inkommet'];
			//echo strtotime($row['inkommet']);
			if (($row['inkommet'] != "")) {
				//echo "här:";
				if ($fi)
					$inkommet = strftime ("%m-%d-%Y %R", strtotime($row['inkommet']));
				else
					$inkommet = strftime ("%Y-%m-%d %R", strtotime($row['inkommet']));
			}
			if (!($row['skickat'] == "")) {
				if ($fi)
					$skickat = strftime ("%m-%d-%Y", strtotime($row['skickat']));
				else
					$skickat = strftime ("%Y-%m-%d", strtotime($row['skickat']));
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
			

	$output .= $this->levStatus($skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok, $ordernr, $skickad_av);


$output .= <<<eof
	</font>

     </td>
   </tr>
eof;

		endwhile;

		}

	if ($fi)
		$select = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id, levklar, betalsatt_id, faktura_ok, skickad_av FROM Ordertabell_gamla_fi WHERE kundnr = " . $kundnr . " order by ordernr DESC";
	else
		$select = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id, levklar, betalsatt_id, faktura_ok, skickad_av FROM Ordertabell_gamla WHERE kundnr = " . $kundnr . " order by ordernr DESC";
		
	//echo $select;
	$res = mssql_query ($select, $this->conn_ms);
	if (mssql_num_rows ($res) > 0 ) {

		while ($row = mssql_fetch_array($res)):
			// om en variable innehåller värdet NULL i nästkommande loop så ändras inte värdet från 
			// loopen före, därför: 
			$inkommet = $skickat = $behandlat = $levklar = $betalsatt_id = $leveranssatt_id = $faktura_ok =  NULL;
			unset($skickat);
			//$skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok
			extract ($row);
			//$skickat = substr($skickat, 0, 10);
			//echo $skickat;
			//$skickat = $row['skickat'];
			//$inkommet = $row['inkommet'];
			//setlocale (LC_ALL, 'pl_PL');
			//$skickat = cstr($skickat);
			//$output .= ".";
			if (($row['inkommet'] != "")) {
				//echo "här:";
				if ($fi)
					$inkommet = strftime ("%m-%d-%Y %R", strtotime($row['inkommet']));
				else
					$inkommet = strftime ("%Y-%m-%d %R", strtotime($row['inkommet']));
			}
			if (!($row['skickat'] == "")) {
				if ($fi)
					$skickat = strftime ("%m-%d-%Y", strtotime($row['skickat']));
				else
					$skickat = strftime ("%Y-%m-%d", strtotime($row['skickat']));
				//echo ".";
			}

			$ordernr = sprintf("%d", $ordernr);			
			if ($fi)
				$link = "show_order_fi_n.php?ordernr=$ordernr&old=yes&fi=yes";
			else
				$link = "show_order_n.php?ordernr=$ordernr&old=yes";
				
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
				$output .= "Inga ordrar finns";
			else
				$output .= "Inga ordrar finns";

			$output .= "</font></td></tr>";

			}
		setlocale (LC_ALL, 'sv_SE');
		return $output;	

	}

	//-------------------------------------------------------------------------------

	function levStatus ($skickat, $behandlat, $levklar, $betalsatt_id, $leveranssatt_id, $faktura_ok, $ordernr, $skickad_av) {
		global $fi, $sv;

		if ($skickat != "") {
		
			if ($fi)
				$skickat = strftime ("%m-%d-%Y", strtotime($skickat));
			else
				$skickat = strftime ("%Y-%m-%d", strtotime($skickat));
			if ($sv)
				$output .= "skickad $skickat";
			else
				$output .= "valmis $skickat";
		}
		elseif ($behandlat != "") {
			
			if (!$sv)
				$output .= "valmis lähetettäväksi, lähetetään pikapuolin";
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
							$output .= "inväntar klartecken från handelsbanken finans";						
					} else {
						if ($levklar == 3) 
							$output .= "klar att skicka, skickas inom kort";
						else 
							$output .= "inväntar klartecken från handelsbanken finans";						
					}
				}
				elseif ($betalsatt_id == 5 && $skickad_av == 'XX' ) { 
					if ($fi && !$sv) {
						$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
					} elseif ($fi && $sv) {
						$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\">klicka här om du vill pröva igen</a>";
					} else {
						$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan_n.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
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
						$output .= "klar för avhämtning";
					else 
						$output .= "ej fullständig samt inväntar klartecken från handelsbanken finans";						
				} else {
					if ($levklar == 3) 
						$output .= "ej fullständig";
					else 
						$output .= "ej fullständig samt inväntar klartecken från handelsbanken finans";						
				}
			}
			elseif ($betalsatt_id == 5 && $skickad_av == 'XX' ) { 
				
				if ($fi && !$sv) {
				$output .= "<b>Huom!</b> Odotamme korttimaksuanne <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv . "\">klikkaa tästä mikäli haluat yrittää uudelleen</a>";
				} elseif ($fi && $sv) {
				$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan_fi.php?ordernr_check=" . $ordernr . "&sv=" . $sv .  "\">klicka här om du vill pröva igen</a>";
				} else {
				$output .= "<b>Obs!</b> Vi väntar på kortbetalningen från er <a href=\"kortMan.php?ordernr_check=" . $ordernr . "\">klicka här om du vill pröva igen</a>";
				}					
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

			if ($fi) {
				$select = "SELECT * FROM Ordertabell_gamla_fi WHERE ordernr = " . $ordernr;
				$res = mssql_query ($select, $this->conn_fi);
			} else {
				$select = "SELECT * FROM Ordertabell_gamla WHERE ordernr = " . $ordernr ;
				$res = mssql_query ($select, $this->conn_ms);
			}
			$orderrow = mssql_fetch_object ($res);
		}

		else {
			
			if ($fi) {
				
				$select = "SELECT * FROM Ordertabell_fi WHERE ordernr = " . $ordernr;
				//echo $select;
				$res = mssql_query ($select, $this->conn_fi);
				//echo $res;
			} else {
				$select = "SELECT * FROM Ordertabell WHERE ordernr = " . $ordernr ;
				//echo $select;
				$res = mssql_query ($select, $this->conn_ms);
			}
			$orderrow = mssql_fetch_object ($res);
		}
	
	return $orderrow;

	}

	//-------------------------------------------------------------------------------

	function getCountry ($land_id) {
		global $fi, $sv;
		
		$select = "SELECT land, land_fi FROM Land WHERE land_id = " . $land_id;
		$res = mysqli_query($this->conn_my, $select);
		$row = mysqli_fetch_object($res);
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
			$res = mssql_query ($select, $this->conn_ms);
			$row = mssql_fetch_object ($res);
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

			$res = mssql_query ($select, $this->conn_ms);
			$row = mssql_fetch_object ($res);
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

	function viewOrderLines($orderrow, $old, $bask) {
			//include_once ("CConnect_ms.php");
			global $fi, $sv;
			//	global	$count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue, 
			//	$est_delivery, $fi, $sv, $release, $fi, $sv;

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
			} else {
				if ($old == "yes") {
					$select = "SELECT Orderposter_gamla.artnr, Orderposter_gamla.antal, Orderposter_gamla.utpris, Orderposter_gamla.bokad, "; 
					$select .= "Orderposter_gamla.rest, Orderposter_gamla.beskrivning, Tillverkare.tillverkare, Moms.momssats as momssats, lagersaldo, bestallt, lev_datum, lev_datum_norm, bestallningsgrans, queue, release, ordernr ";
					$select .= "FROM Artiklar, Orderposter_gamla, Moms, Tillverkare WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND  Artiklar.artnr = Orderposter_gamla.artnr AND ";
					$select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND ";
					$select .= "ordernr = " . $orderrow->ordernr;
				}
				else {		
					$select = "SELECT Orderposter.artnr, Orderposter.antal, Orderposter.utpris, Orderposter.bokad, "; 
					$select .= "Orderposter.rest, Orderposter.beskrivning, Tillverkare.tillverkare, Moms.momssats as momssats, lagersaldo, bestallt, lev_datum, lev_datum_norm, bestallningsgrans, queue, release, ordernr ";
					$select .= "FROM Artiklar, Orderposter, Moms, Tillverkare WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND  Artiklar.artnr = Orderposter.artnr AND ";
					$select .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod AND ";
					$select .= "ordernr = " . $orderrow->ordernr;
				
				}			
			}
			//echo $select;
			$res = mssql_query ($select, $this->conn_ms);

			$fraktkostnad = 0;
			
			while ($row = mssql_fetch_object($res)) {
			$description = "";
			// Om en variable innehåller NULL i en loop så skrivs inte NULL värdet in i variabeln utan det gamla sparas
			// därför: 
			//$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = "";
			//echo $row['artnr'];
			//extract($row);
			if ($fi)
				$val = "EUR";
			else
				$val = "SEK";
				
			if (eregi("^frakt", $row->artnr)) {
				$fraktkostnad += $row->utpris;
				$fraktkostnadMoms += $row->utpris + ($row->utpris * $row->momssats);
			}

			$goodscounter += 1;

			if ($row->tillverkare != '.')
				$description = $row->tillverkare . " ";

			$description .= $row->beskrivning . " " . $row->kommentar;

			// visa bara info om det inte är frakt
			if (!(eregi("^frakt", $row->artnr))):
	?>
			<tr>
			  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php echo $description; ?></font></td>
			  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1"><?php echo $row->antal; ?></font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
	<?php 		echo number_format($row->utpris, 0, ',', ' ') . " " . $val; ?>
			</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
	<?php 		echo number_format($row->utpris + ($row->utpris * $row->momssats), 0, ',', ' ') . " " . $val; ?>
			
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
				}

				else {
					// TODO: 
					//echo kollaLevtid($artnr, $antal, $ordernr, $fi); 
					if ($fi)
						echo $bask->check_lager_bask_fi($row);
					else 
						echo $bask->check_lager_bask_se($row);

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

}
?>

<%

//-------------------------------------------------------------------------------

function show_old_order($kundnr) {
	global $fi;

	include_once ("CConnect_ms.php");
	
	// tvungen att ändra språket till engelska för att 
	// strtotime skulle fungera korrekt
	setlocale (LC_ALL, 'en_US');
	$select = "SELECT ordernr, inkommet, skickat, behandlat, leveranssatt_id FROM Ordertabell WHERE kundnr = '$kundnr' AND show_order = '0' order by ordernr DESC";
	$res = mssql_query ($select);
	if (mssql_num_rows ($res) > '0' ) {
	
	while ($row = mssql_fetch_array($res)):
		// om en variable innehåller värdet NULL i nästkommande loop så ändras inte värdet från 
		// loopen före, därför: 
		$inkommet = $skickat = $behandlat = NULL;
		
		extract ($row);
		//setlocale (LC_ALL, 'pl_PL');
		//$skickat = cstr($skickat);
		if (!($inkommet == ""))
			$inkommet = strftime ("%Y-%m-%d %R", strtotime($inkommet));
		if (!($skickat == ""))
			$skickat = strftime ("%Y-%m-%d", strtotime($skickat));

		$ordernr = sprintf("%d", $ordernr);
		if ($fi == 'yes')
			$link = "show_order_fi.php?ordernr=$ordernr&old=no&fi=yes";
		else
			$link = "show_order.php?ordernr=$ordernr&old=no";
$output .= <<<eof
   <tr>
     <td valign="top" width="40"><font size="1" face="Verdana, Arial"><a href="$link">$ordernr</a></font>
     </td>
     <td valign="top" width="90"><font face="Verdana, Arial" size="1">$inkommet</font>
     </td>
     <td valign="top" width="70%"><font face="Verdana, Arial" size="1">
eof;


		if ($skickat != "") {
			if ($fi == 'yes')
				$output .= "lähetetty $skickat";
			else
				$output .= "skickad $skickat";
		}
		elseif ($behandlat != "") {
			if ($fi == 'yes')
				$output .= "valmis lähetettäväksi, lähetetään pikauolin";
			else {
				if ($leveranssatt_id == 8) 
					$output .= "klar för avhämtning";
				else 
					$output .= "klar att skicka, skickas inom kort";
			}
		}
		else {
			if ($fi == 'yes')
				$output .= "ei lähetysvalmis";
			else
				$output .= "ej leveransklar";
		}


$output .= <<<eof
	</font>
		    
     </td>
   </tr>
eof;

	endwhile;
	
	}

$select = "SELECT ordernr, inkommet, skickat, behandlat FROM Ordertabell_gamla WHERE kundnr = '$kundnr' order by ordernr DESC";
//echo $select;
$res = mssql_query ($select);
if (mssql_num_rows ($res) > '0' ) {
	
	//setlocale (LC_TIME, "sv_SE");
	

	while ($row = mssql_fetch_array($res)):
		// om en variable innehåller värdet NULL i nästkommande loop så ändras inte värdet från 
		// loopen före, därför: 
		$inkommet = $skickat = $behandlat = NULL;
		
		extract ($row);
		//$ordernr = $row->ordernr;
		
		$inkommet = strftime ("%Y-%m-%d %R", strtotime($inkommet));
		$skickat = strftime ("%Y-%m-%d", strtotime($skickat));
		//$behandlat = $row->behandlat;
		
		$ordernr = sprintf("%d", $ordernr);
		if ($fi == 'yes')
			$link_old = "show_order_fi.php?ordernr=$ordernr&old=yes&fi=yes";
		else
			$link_old = "show_order.php?ordernr=$ordernr&old=yes";
		
$output .= <<<eof
   <tr>
     <td valign="top" width="40"><font size="1" face="Verdana, Arial"><a href="$link_old">$ordernr</a></font>
     </td>
     <td valign="top" width="90"><font face="Verdana, Arial" size="1">$inkommet</font>
     </td>
     <td valign="top" width="70%"><font face="Verdana, Arial" size="1">
eof;
		

		if ($skickat != "") {
			if ($fi == 'yes')
				$output .= "lähetetty $skickat";
			else
				$output .= "skickad $skickat";
		}
		elseif ($behandlat != "") {
			if ($fi == 'yes')
				$output .= "valmis lähetettäväksi, lähetetään pikauolin";
			else
				$output .= "klar att skicka, skickas inom kort";
		}
		else {
			if ($fi == 'yes')
				$output .= "ei lähetysvalmis";
			else
				$output .= "ej leveransklar";
		}

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
		if ($fi == 'yes')
			$output .= "Inga ordrar finns";
		else
			$output .= "Inga ordrar finns";
			
		$output .= "</font></td></tr>";

		}

	return $output;	

}

//-------------------------------------------------------------------------------

function levStatus ($skickat, $behandlat) {
	global $fi;
	setlocale (LC_ALL, 'en_US');

	if ($skickat != "") {
		$skickat = strftime ("%Y-%m-%d", strtotime($skickat));
		$output .= "skickad $skickat";
	}
	elseif ($behandlat != "") {
		if ($fi == 'yes')
			$output .= "valmis lähetettäväksi, lähetetään pikauolin";
		else
			$output .= "klar att skicka, skickas inom kort";
	}
	else {
		if ($fi == 'yes')
			$output .= "ei toimitusvalmis, katso kyseisen tuotteen yksilötietoja";
		else
			$output .= "ej leveransklar, se respektive produkt för detaljer";
	}
	
	return $output;
}

//--------------------------------------------------------------------------------

function getKollinr ($ordernr) {
	include ("CConnect_pacsoft.php");
	global $fi;
	$ret = "";

	$ordernr = sprintf("%d", $ordernr);
	
	$select  = "SELECT prc.ParcelNo FROM prc, psl ";
	$select .= "WHERE psl.idPSL=prc.idPSL ";
	$select .= "AND codePSS <> 'D' ";
	$select .= "AND CustNo like '$ordernr' ";
	$select .= "ORDER BY prc.ParcelNo ASC ";
	
	$res = mysqli_query($select);
	if (mysqli_num_rows($res) > 0) {
		while ($row = mysqli_fetch_object($res)):		
#		return "<a href=\"/kollinr.php?kollinr=" . $row->ParcelNo . "\">" . $row->ParcelNo . "</a>" . 
#			" (klicka för att följa paketet)";
		
			if ($fi == 'yes') {
				$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "\">Klikkaamalla tästä voit seurata pakettia</a>";
			}
			else {
				$ret .= "<a href=\"kollinr.php?ordernr_check=" . $ordernr . "\">klicka för att följa paketet</a>";
			}
			$ret .= "<br>";
		endwhile;
		return $ret;
	}
	
	else {
	
		if ($fi == 'yes')
			return "ei tulostettu";
		else
			return "ej utskrivet";
	}
	mysqli_close();
	include_once ("CConnect_ms.php");
	
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
function getOrderinfo ($ordernr, $old) {
	setlocale (LC_ALL, 'en_US');

	include_once ("CConnect_ms.php");
	global $fi;
	if ($old == 'yes') {
		$select = "SELECT * FROM Ordertabell_gamla WHERE ordernr = '$ordernr' ";
		$res = mssql_query ($select);
		$orderrow = mssql_fetch_object ($res);
	}

	else {
		$select = "SELECT * FROM Ordertabell WHERE ordernr = '$ordernr' ";
		$res = mssql_query ($select);
		$orderrow = mssql_fetch_object ($res);
	}

return $orderrow;

}

//-------------------------------------------------------------------------------

function getCountry ($land_id) {
	include_once ("CConnect_ms.php");

	global $fi;
	$select = "SELECT land, land_fi FROM Land WHERE land_id = '$land_id'";
	$res = mssql_query ($select);
	$row = mssql_fetch_object ($res);
	if ($fi == 'yes')
		return $row->land_fi;
	else
		return $row->land;
} 

//-------------------------------------------------------------------------------

function getBetalsatt ($betalsatt_id) {
	include_once ("CConnect_ms.php");
	global $fi;		
	if (!empty ($betalsatt_id)) {
		$select = "SELECT betalsatt, betalsatt_fi FROM Betalsatt WHERE betalsatt_id = $betalsatt_id";
		$res = mssql_query ($select);
		$row = mssql_fetch_object ($res);
		if ($fi == 'yes')
			return $row->betalsatt_fi;
		else
			return $row->betalsatt;
	}
	else {
		if ($fi == 'yes')
			return "ei ilmoitettu";
		else
			return "ej angett";
	}
	
}

//-------------------------------------------------------------------------------

function getLeveranssatt ($leveranssatt_id) {
	include_once ("CConnect_ms.php");
	global $fi;		
	if (!empty ($leveranssatt_id)) {
		$select = "SELECT leveranssatt, leveranssatt_fi FROM Leveranssatt WHERE leveranssatt_id = $leveranssatt_id";
		
		$res = mssql_query ($select);
		$row = mssql_fetch_object ($res);
		if ($fi == 'yes')	
			return $row->leveranssatt_fi;
		else
			return $row->leveranssatt;
	}
	else {
		if ($fi == 'yes')
			return "ei ilmoitettu";
		else
			return "ej angett";
	}
}

//-------------------------------------------------------------------------------

function viewOrderLines($ordernr, $old, $fi) {
		include_once ("CConnect_ms.php");
		global $fi;
		
		if ($old == "yes") {
			$select = "SELECT Orderposter_gamla.artnr, Orderposter_gamla.antal, Orderposter_gamla.utpris, Orderposter_gamla.bokad, "; 
			$select .= "Orderposter_gamla.rest, Orderposter_gamla.beskrivning, Tillverkare.tillverkare ";
			$select .= "FROM (Artiklar INNER JOIN Orderposter_gamla ON Artiklar.artnr = Orderposter_gamla.artnr) ";
			$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
			$select .= "WHERE ordernr = '$ordernr' ";
		}
		else {		
			$select = "SELECT Orderposter.artnr, Orderposter.antal, Orderposter.utpris, Orderposter.bokad, "; 
			$select .= "Orderposter.rest, Orderposter.beskrivning, Tillverkare.tillverkare ";
			$select .= "FROM (Artiklar INNER JOIN Orderposter ON Artiklar.artnr = Orderposter.artnr) ";
			$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
			$select .= "WHERE ordernr = '$ordernr' ";	
		}
		//echo $select;
		$res = mssql_query ($select);
		
		$fraktkostnad = 0;
		
		while ($row = mssql_fetch_array($res)) {

 		// Om en variable innehåller NULL i en loop så skrivs inte NULL värdet in i variabeln utan det gamla sparas
 		// därför: 
		$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = "";

		extract($row);
		if (eregi("^frakt", $artnr))
			$fraktkostnad += $utpris;
		
		$goodscounter += '1';
		
		if ($tillverkare != '.')
			$description = $tillverkare . " ";
		
		$description .= $beskrivning . " " . $kommentar;

		// visa bara info om det inte är frakt
		if (!(eregi("^frakt", $artnr))):
%>
		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% echo $description; %></font></td>
		  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1"><% echo $antal; %></font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<%		printf("%10.0f SEK", $utpris);  %>
		</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<%		printf("%10.0f SEK", $utpris*1.25);
		%>
		</font></td>
		<td bgcolor="#ECECE6" align="left"><font size="1" face="Verdana, Arial">

<%		if (!(eregi("^frakt", $artnr))):
		
			if ($bokad == 1) {
				if ($rest == 1) {
					if ($fi == 'yes')
						echo "Jälkitoimitus, lähetetään myöhemmin";
					else
						echo "Restnoterad, skickas senare";
				}
				else {
					if ($fi == 'yes')
						echo "Valmis";
					else
						echo "Klar";
				}
			}
			
			elseif ($bokad == 2) {
				if ($fi == 'yes')
					echo "Valmis (lähetetään suoraan meidän toimittajaltamme";
				else
					echo "Klar (skickas direkt från vår leverantör)";
			}

			elseif ($bokad == 4) {
				if ($fi == 'yes')
					echo "";
				else
					echo "<font color=\"#85000D\">Produkten är utgången! Vänligen <a href=\"mailto:produkt@cyberphoto.se?subject=Utgången produkt ($artnr) $beskrivning på order $ordernr\"><u><font color=\"#85000D\">kontakta oss</font></u></a> för alterantiv</font>";
			}

			else {
						
				echo kollaLevtid($artnr, $antal, $ordernr, $fi); 
				
			} %>
<% 		endif; %>
		</font></td>
		</tr>

		<%  endif; // kostnadfri frakt 
		%>
<%	} // slut på endwhile
		
		// Fraktkostnaden sist (om det finns någon)
		if ($fraktkostnad > '0') : 
%>
		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Fraktkostnad</font></td>
		  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1">1</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<%		printf("%10.0f kr", $fraktkostnad);  %>
		</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<%		printf("%10.0f kr", $fraktkostnad*1.25); %>
		</font></td>
		<td bgcolor="#ECECE6" align="left"><font size="1" face="Verdana, Arial">
		<% if ($fi == 'yes'): %>
		Valmis
		<% else: %>
		Klar
		<% endif; %>
		</font></td>
		</tr>
		
<%		endif; 


}

%>

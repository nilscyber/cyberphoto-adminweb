<?php

function session_clear() {
  
	 // if session exists, unregister all variables that exist and destroy session 
	$exists = "no"; 
	 $session_array = explode(";",session_encode()); 
	 for ($x = 0; $x < count($session_array); $x++) { 
		$name  = substr($session_array[$x], 0, strpos($session_array[$x], "|")); 
		if (session_is_registered($name)) { 
			session_unregister('$name'); 
			$exists = "yes"; 
		} 
	 } 

	if ($exists != "no") { 
		session_destroy(); 
	} 
} 


function debiCrypt($strUrl) {
	return `/web/phplib/veencrypt '$strUrl'`;
}
function getCountry($land_id) {
	include ("CConnect.php");
	$select = "SELECT land_kod FROM Land WHERE land_id like '$land_id'";
	$res = mssql_query($select);
	$row = mssql_fetch_object ($res);

	return $row->land_kod;
	include ("CConnect_ms.php");

}

function cryptoData($orderrow) {
	global $REMOTE_ADDR, $fi;

	//$ordersumma = number_format($orderrow->netto + $orderrow->moms, 0, "", "");
	$ordersumma = $orderrow->totalsumma;
	$ordersumma = $ordersumma * 100;

	$datat = 'data=';
	$datat .= rawurlencode('1:vara:1:' . $ordersumma . ':');
	if ($fi)
		$datat .= '&currency=EUR';
	else 
		$datat .= '&currency=SEK';
	$datat .= '&shipment=0';
	$datat .= "&kundnr=" . $orderrow->kundnr;
	$datat .= "&ordernr=" . $orderrow->ordernr;
	if (strlen($orderrow->email == 0)) {
		$datat .= "&eMail=" . rawurlencode($orderrow->email);
	}
	else {
		$datat .= "&eMail=" . rawurlencode('null@cyberphoto.se');
	}
	$datat .= "&transID=" . $orderrow->ordernr;
	$datat .= "&namn=" . rawurlencode($orderrow->namn);
	if (strlen($orderrow->co) == 0) {
		$datat .= "&billingAddress=" . rawurlencode ($orderrow->ladress);
	}
	else {
		$datat .= "&billingAddress=" . rawurlencode ($orderrow->co);
	}
	$datat .= "&billingCity=" . rawurlencode ($orderrow->postadress);
	$datat .= "&billingCountry=" . rawurlencode (getCountry($orderrow->fland_id));
	$datat .= "&billingZipCode=" . rawurlencode ($orderrow->postnr);
	$datat .= "&billingFirstName=" . rawurlencode ($orderrow->namn);
	$datat .= "&billingLastName=" . rawurlencode ($orderrow->namn);
	$datat .= "&ip=". rawurlencode ($REMOTE_ADDR);
	if (!$fi)
		$datat .= "&uses3dsecure=true";
	$datat .= "&referenceNo=" . $orderrow->ordernr;
	$datat .= "&metod=order";

	return rtrim(debiCrypt($datat));

}




function show_summary() {

	global $orderrow, $messageText, $messageHtml, $fi, $sv;
		

	if ($fi) 
		$val = " EUR";
	else
		$val = " SEK";
	$summaexmoms = number_format($orderrow->netto, 0, ',', ' ') . " " . $val;	

	if ($orderrow->land_id == 47 || $orderrow->land_id == 999) {
		$moms = sprintf ("%10.0f SEK", 0);
		$summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val;
		}
	else {
		$moms = number_format($orderrow->moms, 0, ',', ' ') . " " . $val;
		$summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val;
		}
	if ($fi && !$sv) {
	?>
	</center></div>
	<div align="center"><center><table border=0 cellspacing=1 cellpadding=2>
	<tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">Alv 0%:</font></small></small></td>
	<td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
	<?php echo number_format($orderrow->netto, 0, ',', ' ') . " " . $val; ?>
	</font></small></small></td></tr>

	<tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">Alv:</font></small></small></td>
	<td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
	<?php echo number_format($orderrow->moms, 0, ',', ' ') . " " . $val; ?>
	</font></small></small></td></tr>

	<tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">Sis Alv:</font></small></small></td>
	<td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
	<?php echo number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val; ?>
	</font></small></small></td></tr>

	</table></center></div>
	<?php		
		
	} else {
	?>
	</center></div>
	<div align="center"><center><table border=0 cellspacing=1 cellpadding=2>
	<tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">exkl moms:</font></small></small></td>
	<td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
	<?php echo number_format($orderrow->netto, 0, ',', ' ') . " " . $val; ?>
	</font></small></small></td></tr>

	<tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">moms:</font></small></small></td>
	<td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
	<?php echo number_format($orderrow->moms, 0, ',', ' ') . " " . $val; ?>
	</font></small></small></td></tr>

	<tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">total summa:</font></small></small></td>
	<td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
	<?php echo number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val; ?>
	</font></small></small></td></tr>

	</table></center></div>
	<?php
	}
	
if ($fi && !$sv) {
$messageText .= <<<EOF

  Alv 0%:  $summaexmoms
  Alv:     $moms
  Sis Alv: $summaMedMoms

EOF;
$messageHtml .= <<<EOF
<tr>
  <td height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;
    </font></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Alv 0%:</font></p>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$summaexmoms</font>
</font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Alv:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$moms</font></font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Sis Alv:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><b><font size="1">$summaMedMoms</font></b> 
</font></td>
</tr>
EOF;

} else {
$messageText .= <<<EOF

  exkl. moms:       $summaexmoms
  Moms:             $moms
  Totalt:           $summaMedMoms

EOF;
$messageHtml .= <<<EOF
<tr>
  <td height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;
    </font></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">exkl moms:</font></p>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$summaexmoms</font>
</font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">moms:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$moms</font></font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1"> inkl moms:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><b><font size="1">$summaMedMoms</font></b> 
</font></td>
</tr>
EOF;

}

}


function write_creditcard() {
	global $summaMedMoms;
	

	?>
	<p><font face="Verdana" size="1">Ni har valt att betala med kontokort. 
    Betalningen är preliminärt godkänd. Betalningen kommer att belastas ert kort 
    när varorna skickas från CyberPhoto.</font></p>
	<?php

}


function netpay()  {
	global $orderrow, $ordernrladdaom;

	# Lägg på moms
	#$totalsumma = $orderrow->netto*1.25;
	$totalsumma = $orderrow->totalsumma;
	# Avrunda beloppet
	$totalsumma = number_format ($totalsumma, 0, "", "");

	$output = `/usr/java/jdk1.3.1_07/bin/java -cp /usr/java/lib/HFAffar.jar se.hbfinans.netpay.store.HFStoreModule 900080 $ordernrladdaom $totalsumma SEK "" SV http://www.cyberphoto.se/?ordernr=$ordernrladdaom&` ;

	?>
	<form action="https://www.netpay.saljfinans.com/reservation" target="_parent" method="get">
	<input type="hidden" name="reservation" value="<?php echo $output; ?>">
	<input type="image" src="vidare.gif" border="0" value="Klicka här för att gå vidare till Netpay">
	</form>
	<script language="JavaScript">
		document.forms[0].submit();
	</script>

	<?php

	}
function write_toOrderposter($artnr, $newordernr, $count) {
	global $goodsvalue, $moms1, $moms2, $moms3, $moms4, $fi, $sv, $bask, $inkommet, $frakt, $bask, $conn_standard, $extra_frakt, $butiksfrakt, 
	$discountCode, $discountCodeStatus, $rowDiscount, $freight, $pay;

	// Nollställ variabler
 	$totalpacutpris = "";
  	$rabatt = "";
	$check = "";
	$inserted = "";
	$bokad = "";
	$levDatum = "";

	$select  = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.momskod, utpris, utpris_fi, frakt, lagersaldo, lagersaldo_fi, till_kund, till_kund_fi, ";
	$select .= " Moms.momssats, Moms.momssats_fi, Artiklar.tillverkar_id, Artiklar.kategori_id, "; 
	$select .= "bestallt, bestallt_fi, lev_datum, lev_datum_fi, lev_datum_norm, lev_datum_norm_fi, bestallningsgrans, bestallningsgrans_fi, release  ";
	$select .= "FROM Artiklar, Artiklar_fi, Moms WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.momskod = Moms.moms_id AND artnr='$artnr'";
	//echo $select;
	// Get the data from the DB
	$res = mssql_query($select);
	$row = mssql_fetch_object($res);

	$frakt = $row->frakt;

	if ($fi) {	
		$outprice = $row->utpris_fi;	
	} else  {
		$outprice = $row->utpris;
	}
	// ja, jag vet, den behövs inte, men jag orkar inte leta igenom alla rader där det blir fel annars. 
	$utpris = $outprice;
	
	$discount = false;
	$utprisNormal = $outprice;
	$newUtpris = null;
	$extraBeskrivningText = "";

	if ($discountCode != "" && $discountCodeStatus == 1 && $rowDiscount->totalSum == "" && !$fi) {							
			
			$newUtpris = $bask->getDiscountPrice($rowDiscount, $row->artnr, $row->kategori_id, $row->tillverkar_id, $utpris);
			if ($newUtpris != $utpris) {
				$extraBeskrivningText = $bask->getDiscountText($rowDiscount, $momssats, $utpris, false);					
				$discount = true;						
				$utpris = $newUtpris;
				$outprice = $newUtpris;
				$count = 1;
			}
	}									
			
	$goodsvalue+=($outprice*$count);
	
	if (eregi("pac$", $row->artnr)) {
			$art = $row->artnr;
			$select  = "SELECT Artiklar.artnr, Artiklar.lagersaldo, Artiklar.beskrivning, Artiklar.bestallt, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, tillverkare, ";
			$select .= "frakt, lagersaldo_fi, bestallt_fi, Artiklar.release, Artiklar.utpris, Artiklar.till_kund, Artiklar_fi.till_kund_fi, ";
			$select .= "lev_datum_fi, bestallningsgrans, lev_datum_norm, lev_datum_norm_fi, Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, Artiklar.momskod, ";
			$select .= "Moms.momssats_fi, Moms.momssats, Artiklar.link, link2_fi, Paketpriser.antal, art_id, art_id_fi ";
			$select .= " FROM Artiklar, Artiklar_fi, Tillverkare, Kategori, Moms, Paketpriser ";
			$select .= "WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id ";
			$select .= "AND Paketpriser.artnr_del=Artiklar.artnr ";
			$select .= " AND Paketpriser.artnr_paket = '$art'";
			//echo $select;
		/**					
		if ($fi) {
			$select = "SELECT artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.art_id, Artiklar_fi.lagersaldo_fi as lagersaldo, antal, till_kund_fi as till_kund, utpris_fi as utpris, ";
			$select = "Moms.momssats_fi as momssats, Artiklar.momskod ";
			$select .= "FROM Artiklar, Artiklar_fi, Paketpriser, Moms WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.artnr=Paketpriser.artnr_del AND Artiklar.momskod = Moms.moms_id ";
			$select .= "AND Paketpriser.artnr_paket = '$artnr' ";
		} else {
			$select = "SELECT artnr, Artiklar.beskrivning, Artiklar.art_id, lagersaldo, antal, till_kund, Artiklar.utpris, Moms.momssats, Artiklar.momskod ";
			$select .= "FROM Artiklar, Paketpriser, Moms WHERE Artiklar.artnr=Paketpriser.artnr_del AND Artiklar.momskod = Moms.moms_id ";
			$select .= "AND Paketpriser.artnr_paket = '$artnr' ";		
		}
		*/
		
		$res = mssql_query ($select);


		$marg = 0; $paketRabatt = 0; $pris = 0; $marg2 = 0;

		while ($row = mssql_fetch_object($res)):
			$frakt = $row->frakt;

			if ($frakt > 0 && !($butiksfrakt)) {				
				// lägg bara på extra frakt om ingen annan produkt har större
				if ($frakt > $extra_frakt) {
					$extra_frakt = $frakt;
				}
			}  	

			
			if ($fi)
				$del_utpris = $row->utpris_fi;
			else 	
				$del_utpris = $row->utpris;
			
			if ($fi) {				
				$utpris = $row->utpris_fi;
				$momssats = $row->momssats_fi;
				$art_id = $row->art_id_fi;
				$till_kund = $row->till_kund_fi;
				if ($row->beskrivning_fi != "" && !$sv)
					$beskrivning = $row->beskrivning_fi;
				else
					$beskrivning = $row->beskrivning;					
			} else {
				$art_id = $row->art_id;
				$utpris = $row->utpris;
				$momssats = $row->momssats;
				$till_kund = $row->till_kund;
				$beskrivning = $row->beskrivning;
			}
					
			//$momskod = $row->momskod;
			//$levDatum = "";
			//unset($levDatum);

			// används för att räkna ut rabatten för varje enskild artikel
			//$marg = $marg + rsPaketDelInfo!utpris - rsPaketDelInfo!art_id
			$marg += $utpris - $art_id;
			//$pris = $pris + rsPaketDelInfo!utpris * rsPaketDelar!antal * rsOrderposter!antal
			$pris = $pris + $utpris * $row->antal * $count;


			// Plussa på totala paketpriset
			$totalpacutpris += $del_utpris*$row->antal;

			// Titta hur många av paketdelen vi behöver
			$newCount = $row->antal*$count;

			// Titta om det finns tillräckligt på lager och boka upp i så fall
			//if ($lagersaldo >= $newCount) {

			if ($fi  && $row->lagersaldo_fi >= $newCount) {			
				$bokad = 1;
				$lagersaldo_new = $row->lagersaldo_fi - $newCount;
				$till_kund_new = $row->till_kund_fi + $newCount;

				$update = "UPDATE Artiklar_fi set lagersaldo_fi = $lagersaldo_new, till_kund_fi = $till_kund_new WHERE artnr_fi='$row->artnr'";	
							
				mssql_query($update, $conn_standard);

			} elseif (!$fi && $row->lagersaldo >= $newCount) {
				$bokad = 1;
				$lagersaldo_new = $row->lagersaldo - $newCount;
				$till_kund_new = $row->till_kund + $newCount;
				if ($fi)
					$update = "UPDATE Artiklar_fi set lagersaldo_fi = $lagersaldo_new, till_kund_fi = $till_kund_new WHERE artnr_fi='$row->artnr'";
				else 	
					$update = "UPDATE Artiklar set lagersaldo = $lagersaldo_new, till_kund = $till_kund_new WHERE artnr='$row->artnr'";
					//echo "updaterar lagersaldo";					

				mssql_query ($update, $conn_standard);

	
				# Om det saknas på lager
			} else {
				$bokad = 0;
				$complete_order = 'no';
				//Hoppade helt över detta här och visar istället bara i process_orderrows. MEN TODO: lägga in aktuellt datum i levDatum. 
				//$levDatum = $bask->check_lager($artnr2);
				//$levDatum = ereg_replace('"', '\\"', $bask->check_lager_bask_fi($row)) ; 
				//$levDatum = $bask->
				//getLevTid ($artnr, $count, $ordernr)
			}

			// Titta om artikeln redan finns inlagd.
			if ($fi) 
				$checkifexist = "SELECT antal FROM Orderposter_fi WHERE ordernr = $newordernr AND artnr = '$row->artnr' ";
			else 	
				$checkifexist = "SELECT antal FROM Orderposter WHERE ordernr = $newordernr AND artnr = '$row->artnr' ";
				
			$res2 = mssql_query($checkifexist, $conn_standard);
			$row2 = mssql_fetch_object($res2);
			$inserted = $row2->antal;
			//echo $artnr2;
			// Om den existerade, plussa ihop och uppdatera antalet till det nya
			if (mssql_num_rows ($res2) > 0) {
				  $insertCount = $newCount + $inserted;
			if ($fi)
				$update = "update Orderposter_fi set antal = '$insertCount' WHERE ordernr = $newordernr AND artnr = '$row->artnr' ";
			else 
				$update = "update Orderposter set antal = '$insertCount' WHERE ordernr = $newordernr AND artnr = '$row->artnr' ";
			mssql_query ($update, $conn_standard);
			 }

			// Stoppa annars in som vanligt
			else {
			if ($fi) {
				$insert  = "insert into Orderposter_fi (ordernr, artnr, antal, utpris, bokad, beskrivning, levDatum, levDatumDatumInlagt, momssats, momskod, paketArtnr) ";
				$insert .= "values ($newordernr, '$row->artnr', $newCount, $utpris, $bokad, '$beskrivning', '$levDatum', '$inkommet', $momssats, $row->momskod, '$art') ";				
			} else {
				$insert  = "insert into Orderposter (ordernr, artnr, antal, utpris, bokad, beskrivning, levDatum, levDatumDatumInlagt, momssats, momskod, paketArtnr) ";
				$insert .= "values ($newordernr, '$row->artnr', $newCount, $utpris, $bokad, '$beskrivning', '$levDatum', '$inkommet', $momssats, $row->momskod, '$art') ";				
			}
			
			mssql_query ($insert, $conn_standard);
			}

		endwhile;
		$rabattDel = ($pris - $outprice * $count); //delrabatt för de olika paketen

		$res = mssql_query ($select);	
		$antalRader = mssql_num_rows($res);
		$i = 0;
		$Totalt = $rabattDel; //används för att räkna ut restvärdet av rabatten till den sista artikeln
		while ($row = mssql_fetch_object($res)):
		    //$marg2 = rsPaketDelInfo!utpris - rsPaketDelInfo!art_id
		    $i += 1;
		    if ($fi) {
		    	$marg2 = $row->utpris_fi - $row->art_id_fi;
		    	//echo "<p>marg2: " . $marg2 . ",utpris_fi," . $row->utpris_fi . ",art_id_fi," . $row->art_id_fi."<p>";
		    	$momssats = $row->momssats_fi;
		    	//echo $momssats;
		    	$utpris = $row->utpris_fi; 
		    } else {
		    	$marg2 = $row->utpris - $row->art_id;
		    	$momssats = $row->momssats;
		    	$utpris = $row->utpris;
		    }
		    $andel = $marg2 / $marg;
		    $andelKr = $andel * $rabattDel;
		    $andelKrTot = andelKrTot + $andelKr;
		    // resten hamnar på sista posten så att det inte blir något avrundningsproblem
		    if ($i == $antalRader) {
				if ($fi) 
					$s = "UPDATE Orderposter_fi set paketRabatt = " . $Totalt . " + paketRabatt WHERE ordernr = " . $newordernr . " AND artnr = '" . $row->artnr . "'";								
				 else 
					$s = "UPDATE Orderposter set paketRabatt = " . $Totalt . " + paketRabatt WHERE ordernr = " . $newordernr . " AND artnr = '" . $row->artnr . "'";																			    	
				//echo "här: " . $utpris . "," . $row->antal . "," . $Totalt . "," . $momssats;
				if ($row->momskod == 1)					
					$moms1 +=  round((($utpris * $row->antal  * $count - $Totalt) * $momssats),2); 
				if ($row->momskod == 2)
					$moms2 +=  round((($utpris * $row->antal * $count - $Totalt) * $momssats),2); 
				if ($row->momskod == 3)
					$moms3 +=  round((($utpris * $row->antal * $count - $Totalt) * $momssats),2); 
				if ($row->momskod == 4)
					$moms4 +=  round((($utpris * $row->antal * $count - $Totalt) * $momssats),2); 
				

		    } else {
				if ($fi)
		    		$s = "UPDATE Orderposter_fi set paketRabatt = " . $andelKr . " + paketRabatt WHERE ordernr = " . $newordernr . " AND artnr = '" . $row->artnr . "'";
		    	else 
		    		$s = "UPDATE Orderposter set paketRabatt = " . $andelKr . " + paketRabatt WHERE ordernr = " . $newordernr . " AND artnr = '" . $row->artnr . "'";
				if ($row->momskod == 1)
					$moms1 +=  round((($utpris * $row->antal * $count - $andelKr) * $momssats),2);
				if ($row->momskod == 2)
					$moms2 +=  round((($utpris * $row->antal * $count - $andelKr) * $momssats),2); 
				if ($row->momskod == 3)
					$moms3 +=  round((($utpris * $row->antal * $count - $andelKr) * $momssats),2);
				if ($row->momskod == 4)
					$moms4 +=  round((($utpris * $row->antal * $count - $andelKr) * $momssats),2); 
				
		    }
		    mssql_query($s, $conn_standard);
		    $Totalt = $Totalt - $andelKr;	
		endwhile;

		// Titta efter hur mycket rabatten blir i paketet mot enskilda delar samt
		// gångra med antal paket
		//echo "här: " . $totalpacutpris . "," . $outprice . "," . $count;
		$ny_rabatt = ($totalpacutpris - $outprice) * $count;
		//echo "här: " . $ny_rabatt;
		// Titta om det finns någon rabatt och kommentar redan inlagd
		if ($fi)
			$rabattkoll = "SELECT rabatt, kommentar FROM Ordertabell_fi WHERE ordernr = $newordernr";
		else 	
			$rabattkoll = "SELECT rabatt, kommentar FROM Ordertabell WHERE ordernr = $newordernr";
		$res = mssql_query ($rabattkoll, $conn_standard);
		$row3 = mssql_fetch_object($res);
		$old_rabatt = $row3->rabatt;
		$oldkommentar = $row3->kommentar;
		
		/**
		// Titta om "rabatt ord" redan är inlagda, och lägg till text utefter det.
		if (eregi("Rabatten avser", $oldkommentar)) {

			$newkommentar = $oldkommentar . ", $name: $ny_rabatt kr";
		}
		else 	{

			$newkommentar = "" . $oldkommentar . "Rabatten avser paketpris på $name: ";
			$newkommentar .= sprintf("%.2f", $ny_rabatt) . " kr";
		}
		*/
		if ($discount) {
			if ($oldkommentar != "")
				$newkommentar = $oldkommentar . "\n\r" . $extraBeskrivningText;
			else 
				$newkommentar = $oldkommentar . "\n\r" . $extraBeskrivningText;
		}
		$discount = false;
		

		// Lägg ihop existerande rabatt med nya paketrabatten och uppdatera tabellen.
		$tot_rabatt = $old_rabatt + $ny_rabatt;
		//$insert_rabatt = "UPDATE Ordertabell SET kommentar = '$newkommentar', paketRabatt = paketRabatt + " . $ny_rabatt . " WHERE ordernr = '$newordernr'";
		if ($fi) {
			$insert_rabatt = "UPDATE Ordertabell_fi SET paketRabatt = paketRabatt + " . $ny_rabatt . " WHERE ordernr = $newordernr";			
		} else {
			if ($newkommentar != "")
				$insert_rabatt = "UPDATE Ordertabell SET paketRabatt = paketRabatt + " . $ny_rabatt . ", kommentar = '" . $newkommentar . "'" . " WHERE ordernr = $newordernr";
			else 
				$insert_rabatt = "UPDATE Ordertabell SET paketRabatt = paketRabatt + " . $ny_rabatt . " WHERE ordernr = $newordernr";
		}
		mssql_query ($insert_rabatt, $conn_standard);

	   }
	   else { // om inte paket
			if ($frakt > 0 && !($butiksfrakt)) {				
				// lägg bara på extra frakt om ingen annan produkt har större
				if ($frakt > $extra_frakt) {
					$extra_frakt = $frakt;
				}
			}  	
	   	
			if ($fi) {
				//echo $sv . $row->beskrivning_fi;
				$utpris = $row->utpris_fi;
				$momssats = $row->momssats_fi;
				if ($sv || $row->beskrivning_fi == "")
					$beskrivning = $row->beskrivning;
				else 
					$beskrivning = $row->beskrivning_fi;
			} else {
				$momssats = $row->momssats;
				$utpris = $row->utpris;
				$beskrivning = $row->beskrivning;
			}

				   	
		// skapa presentort om det är ett sådant
		if (eregi("presentkort", $artnr)) {
			$date = date("Y-m-d H:i:s");
			$receiver = "";
			ereg ("(presentkort)([0-9]+)", $artnr,$matchess);		
			$i = $matchess[2];
			//echo "här: " . $i;
			$outprice = $GLOBALS['giftCard'][$i];
			$receiver = $GLOBALS['giftCardReceiver'][$i];
			newGiftCard($newordernr, $outprice, $date, $artnr, $receiver); 
			//$i += 1;	
		}   
			
	

		  // Om produkten är någon form av frakt så bokas den automatiskt upp direkt.
		  if (eregi("frakt", $artnr)  || (eregi("presentkort", $artnr))) {
			$bokad = 1;
		  } else {

			 // Titta om det finns tillräckligt på lager och boka upp i så fall
			if ($fi  && $row->lagersaldo_fi >= $count) {

				$bokad = 1;
				$lagersaldo_new = $row->lagersaldo_fi - $count;
				$till_kund_new = $row->till_kund_fi + $count;

				$update = "UPDATE Artiklar_fi set lagersaldo_fi = $lagersaldo_new, till_kund_fi = $till_kund_new WHERE artnr_fi='$artnr'";
		

				mssql_query ($update, $conn_standard);
			} elseif (!$fi && $row->lagersaldo >= $count) {
				$bokad = 1;
				$lagersaldo_new = $row->lagersaldo - $count;
				$till_kund_new = $row->till_kund + $count;
				if ($fi)
					$update = "UPDATE Artiklar_fi set lagersaldo_fi = $lagersaldo_new, till_kund_fi = $till_kund_new WHERE artnr_fi='$artnr'";
				else 	
					$update = "UPDATE Artiklar set lagersaldo = $lagersaldo_new, till_kund = $till_kund_new WHERE artnr='$artnr'";					

				mssql_query ($update, $conn_standard);

		  } else {
				$bokad = 0;
				$complete_order = 'no';
				// TODO: fixa detta. 
				//$levDatum = $bask->check_lager($artnr);
		  }
		  
		}

		// Titta om artikeln redan finns inlagd.
		if ($fi)
			$checkifexist2 = "SELECT antal FROM Orderposter_fi WHERE ordernr = $newordernr AND artnr = '$artnr' ";
		else 
			$checkifexist2 = "SELECT antal FROM Orderposter WHERE ordernr = $newordernr AND artnr = '$artnr' ";	
		
		//echo $checkifexist2;
		$res3 = mssql_query($checkifexist2, $conn_standard);
		//if ($res3) 
		$row3 = mssql_fetch_object($res3);
		$inserted2 = $row3->antal;

		// Om den existerade, plussa ihop och uppdatera antalet till det nya
		if (mssql_num_rows ($res3) > 0) {
			  $insertCount2 = $count + $inserted2;
			if ($fi)
				$update2 = "UPDATE Orderposter_fi set antal = $insertCount2 WHERE ordernr = $newordernr AND artnr = '$artnr' ";
			else 
				$update2 = "UPDATE Orderposter set antal = $insertCount2 WHERE ordernr = $newordernr AND artnr = '$artnr' ";
		//echo $update2;
		mssql_query ($update2, $conn_standard);
		 }

		// Stoppa annars in som vanligt
		else {
		  // Lägg till artikeln till ordern
		  if ($fi) {
			  $insert  = "insert into Orderposter_fi (ordernr, artnr, antal, utpris, bokad, beskrivning, levDatum, levDatumDatumInlagt, momssats, momskod) ";
			  $insert .= "values ($newordernr, '$artnr', $count, $utpris, $bokad, '$beskrivning', '$levDatum', '$inkommet', $row->momssats_fi, $row->momskod) ";		  	
		  } else {
			  	/**
		  		$insert  = "insert into Orderposter (ordernr, artnr, antal, utpris, bokad, beskrivning, levDatum, levDatumDatumInlagt, momssats, momskod) ";
			  	$insert .= "values ($newordernr, '$artnr', $count, $utpris, $bokad, '$beskrivning $extraBeskrivningText', '$levDatum', '$inkommet', $row->momssats, $row->momskod) ";		  						
				*/
			  	  	
		  		if ($discount) {
					  $insert  = "insert into Orderposter (ordernr, artnr, antal, utpris, bokad, beskrivning, levDatum, levDatumDatumInlagt, momssats, momskod, discountCode, utprisNormal) ";
					  $insert .= "values ($newordernr, '$artnr', $count, $utpris, $bokad, '$beskrivning $extraBeskrivningText', '$levDatum', '$inkommet', $row->momssats, $row->momskod, '$discountCode', $utprisNormal) ";		  			  			
				} else {
					  $insert  = "insert into Orderposter (ordernr, artnr, antal, utpris, bokad, beskrivning, levDatum, levDatumDatumInlagt, momssats, momskod, utprisNormal) ";
					  $insert .= "values ($newordernr, '$artnr', $count, $utpris, $bokad, '$beskrivning $extraBeskrivningText', '$levDatum', '$inkommet', $row->momssats, $row->momskod, $utprisNormal) ";
				}
				
		  }
		//echo $insert;
			if ($artnr != "")
				mssql_query ($insert, $conn_standard);
			//echo "här: " . $row->momskod . "," . $momssats . "," . $count . "," . $utpris;
			if ($row->momskod == 1)
				$moms1 += ($momssats*$utpris*$count);
			elseif ($row->momskod == 2)
				$moms2 += ($momssats*$utpris*$count);
			elseif ($row->momskod == 3)
				$moms3 += ($momssats*$utpris*$count);
			elseif ($row->momskod == 4)
				$moms4 += ($momssats*$utpris*$count);
			
		}
  	}
	
}

function mailaSlutet() {

	global $messageHtml, $messageText, $message, $headers, $subject, $to, $orderrow, $orderrecipient;
// Avslut i mail meddelandet
$messageHtml .= <<<EOF

</center>
<tr>
  <td height="15" colspan="5"><font face="Verdana, Arial" size="1">Övrig information<p>Som 
  privatperson har du 14 dagar på dig att ångra ditt köp efter du mottagit 
  varan. Varan ska vara i oförändrat skick och förpackningen ska vara oskadad 
  och obruten.</p>
  <p>Vid eventuell retur, så måste du kontakta oss först för att få ett 
  returnummer. Det är ditt ansvar att varan och dess originalförpackning kommer 
  tillbaka till oss i oskadat skick. Se därför till att varan emballeras väl. Om 
  något skulle hända med varan trots att den emballerats på ett 
  tillfredställande sätt, så är det posten som är ersättningsskyldig. Returen är 
  godkänd först när vi kontrollerat varan. Vid godkänd retur så återbetalas 
  summan inom kort. Vid ej godkänd retur så returneras varan och frakt 
  debiteras.</p>
  <p>Vill du åberopa garantireparation gäller följande:<br>
  Kontakta oss så kan vi hänvisa till lokal auktoriserad verkstad och om det 
  inte finns, så sänder vi en paketavi så att ni kostnadsfritt kan sända kameran 
  till auktoriserad verkstad. Kostnadsfri paketavi gäller inom Sverige. Om du 
  måste sända din kamera så tänk på att kameran har skyddspåse eller liknande i 
  sin kartong så att kameran inte repas och att du har en ytterkartong runt 
  kamerans egen kartong.</font></td>
</tr>
<tr>
  <td height="15" colspan="5">
  <p></p>
  <p></p>
  <p><br>
  <a href="http://www.cyberphoto.se">
  <img border="0" src="cid:logo.gif" width="450" height="95"></a></td>
</tr>
</table>

</td>
      </tr>
      <tr>
        <td></td>
        <td align="right"></td>
      </tr>
      <tr>
        <td></td>
        <td align="right"></td>
      </tr>
    </table>
    </td>
  </tr>
</table>
</div>

</body>
</html>

EOF;

$messageText .= <<<EOF


---------------------------------------------------------------
Information

Som privatperson har du 14 dagar på dig att ångra ditt köp efter du
mottagit varan. Varan ska vara i oförändrat skick och förpackningen
ska vara oskadad och obruten.

Vid eventuell retur, så måste du kontakta oss först för att få ett
returnummer. Det är ditt ansvar att varan och dess originalförpackning
kommer tillbaka till oss i oskadat skick. Se därför till att varan
emballeras väl. Om något skulle hända med varan trots att den emballerats
på ett tillfredställande sätt, så är det posten som är ersättningsskyldig.
Returen är godkänd först när vi kontrollerat varan. Vid godkänd retur så
återbetalas summan inom kort. Vid ej godkänd retur så returneras varan
och frakt debiteras.

Vill du åberopa garantireparation gäller följande:
Kontakta oss så kan vi hänvisa till lokal auktoriserad verkstad och om
det inte finns, så sänder vi en paketavi så att ni kostnadsfritt kan
sända kameran till auktoriserad verkstad. Kostnadsfri paketavi gäller
inom Sverige. Om du måste sända din kamera så tänk på att kameran har
skyddspåse eller liknande i sin kartong så att kameran inte repas och
att du har en ytterkartong runt kamerans egen kartong.

EOF;


	$message .= "------=MIME_BOUNDRY_main_message\n"; 
	$message .= "Content-Type: text/plain;\n\tcharset=\"iso-8859-1\"\n"; 
	$message .= "Content-Transfer-Encoding: quoted-printable\n\n"; 
	$message .= $messageText . "\n\n";
	
	$message .= "------=MIME_BOUNDRY_main_message\n"; 
	//$message .= "Content-Type: text/html;\n\tcharset=\"iso-8859-1\"\n"; 
	//$message .= "Content-Transfer-Encoding: quoted-printable\n\n"; 
	$message .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
	$message .= "Content-Transfer-Encoding: 8bit\n\n";
	
	$message .= $messageHtml;


//echo $messageText;

//$message  .= $messageText . "\n\n" . $messageHtml . "";
/**
$message .= "\n------=MIME_BOUNDRY_main_message\n";
$message .= "Content-Type: image/gif; \n name=\"logo.gif\"\n"; 
$message .= "Content-Transfer-Encoding: base64\n"; 
//$message .= "Content-Disposition: inline; filename=\"logo.gif\"\n";
$message .= "Content-ID: <loggan>\n\n"; 


	$file = "/home/www/10.gif";
	$fp = fopen($file, "rb");
	$content = fread($fp, filesize($file));

$message .= chunk_split(base64_encode($content));

$message .= "\n"; 

*/
$message .= "\n------=MIME_BOUNDRY_main_message--\n\n"; 





if (isset($to)) {
	require_once("class.phpmailer.php");
	//echo "här: " . $orderrow->email;
	

	$mail = new phpmailer();

	$mail->From     = "order@cyberphoto.se";
	$mail->FromName = "CyberPhoto";
	$mail->Host     = "81.8.240.27";
	//$mail->Mailer   = "smtp";

	$mail->AddAddress ($orderrow->email, $orderrow->namn);
	$mail->Body = $messageHtml;
	$mail->AltBody = $messageText;
	
	$mail->Subject = "Preliminär orderbekräftelse för " . $orderrow->namn;
	$subject = "Preliminär orderbekräftelse för " . $orderrow->namn;
	
	$mail->IsHTML(true);
	$mail->IsSMTP(true);
	$mail->AddEmbeddedImage ("/home/www/logo.gif", "logo.gif", "logo.gif", "base64", "image/gif");
	
	$mail->AddEmbeddedImage ("/home/www/vertline.gif", "vertline.gif", "vertline.gif", "base64", "image/gif");
	$mail->AddEmbeddedImage ("/home/www/dotv.gif", "dotv.gif", "dotv.gif", "base64", "image/gif");
	
	$mail->AddEmbeddedImage ("/home/www/dotsv.gif", "dotsv.gif", "dotsv.gif", "base64", "image/gif");

	
	if(!$mail->Send())
		echo "Orderbekräftelse kunden inte mailas iväg";
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();

	// kopia till oss
	$ordermessage = "Kommentar: \n" . $orderrow->kommentarKund . "\n\n" . $messageText;
	$headers = preg_replace ("/(To:){1}(.)*(\n){1}/", "", $headers); // används inte men lät den stå kvar. Tar bort raden med "To" från headers

	$headers2 = "From: CyberPhoto <order@cyberphoto.se>\n";

	mail("$orderrecipient", "$subject", "$ordermessage", "$headers2");

        }
# ordna så att mailen inte skickas igen om någon laddar om sidan
unset($to, $messageHtml, $headers, $subject);
}
function process_orderrows($show) {
	global $ordernrladdaom, $message, $orderrow, $messageText, $messageHtml, $orderrow, $fi, 
	$bestallningsgrans, $sv, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $release, $bask, 
	$count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue, 
	$est_delivery, $fi, $sv, $release, $fi, $sv, $bask, $conn_standard;

	
	$betalsatt_id = $orderrow->betalsatt_id;
	
	// Artikel rubrik i mail meddelande
	if ($fi)
		$messageText .= "\nlukumäärä\t  Tuote  \t\t\t\t\t   sisällä \n\n";
	else 	
		$messageText .= "\nAntal\t  Vara  \t\t\t\t\t   Pris/st\n\n";

	// Först en fråga för att ta fram allt utom frakten (så att det hamnar först)
	if ($fi)
		$itemsselect  = "SELECT Orderposter_fi.artnr, Orderposter_fi.utpris, Orderposter_fi.levDatum, Orderposter_fi.bokad, Orderposter_fi.antal, Moms.momssats_fi as momssats, Orderposter_fi.beskrivning,   ";
	else 
		$itemsselect  = "SELECT Orderposter.artnr, Orderposter.utpris, Orderposter.levDatum, Orderposter.bokad, Orderposter.antal, Moms.momssats, Orderposter.beskrivning, ";
	
	$itemsselect .= "tillverkare, lagersaldo, lagersaldo_fi, bestallt, bestallt_fi, release, lev_datum_fi, lev_datum, bestallningsgrans, bestallningsgrans_fi, lev_datum_norm, lev_datum_norm_fi  ";
	if ($fi) {	
		$itemsselect .= " FROM Artiklar, Artiklar_fi, Orderposter_fi, Tillverkare, Moms WHERE Orderposter_fi.artnr = Artiklar.artnr AND Orderposter_fi.ordernr = $ordernrladdaom AND (Orderposter_fi.artnr not like 'frakt%') ";
	} else {
		$itemsselect .= " FROM Artiklar, Artiklar_fi, Orderposter, Tillverkare, Moms WHERE Orderposter.artnr = Artiklar.artnr AND Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr not like 'frakt%') ";
	}
	$itemsselect .= " AND Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod ";
	//echo $itemsselect;
	/**
	if ($fi) {
		$itemsselect  = "SELECT Orderposter_fi.artnr, Artiklar.beskrivning,Artiklar_fi.beskrivning_fi, Orderposter_fi.utpris, tillverkare, antal, Moms.momssats_fi as momssats, Artiklar.lev_datum, Artiklar.lev_datum_fi,  ";
		$itemsselect .= "Orderposter_fi.levDatum, Orderposter_fi.bokad, Artiklar_fi.bestallningsgrans_fi as bestallningsgrans, Artiklar_fi.lagersaldo_fi as lagersaldo, Artiklar_fi.lev_datum_norm_fi ";
		$itemsselect .= "FROM Artiklar, Artiklar_fi, Orderposter_fi, Tillverkare, Moms WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.momskod = Moms.moms_id AND Orderposter_fi.artnr = Artiklar.artnr AND ";
		$itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND ";
		$itemsselect .= "Orderposter_fi.ordernr = $ordernrladdaom AND (Orderposter_fi.artnr not like 'frakt%') ";		
		
	} else {
		$itemsselect  = "SELECT Orderposter.artnr, Artiklar.beskrivning, Orderposter.utpris, tillverkare, antal, Moms.momssats, Artiklar.lev_datum, Artiklar.lev_datum_norm,  ";
		$itemsselect .= "Orderposter.levDatum, Orderposter.bokad, Artiklar.bestallningsgrans, Artiklar.lagersaldo, Artiklar.lev_datum_normal ";
		$itemsselect .= "FROM Artiklar, Orderposter, Tillverkare, Moms WHERE Artiklar.momskod = Moms.moms_id AND Orderposter.artnr = Artiklar.artnr AND ";
		$itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND ";
		$itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr not like 'frakt%') ";		
	}
	*/
	
	$itemres = mssql_query($itemsselect, $conn_standard);
	
	while ($row = mssql_fetch_object($itemres)) :		
		if ($fi) {
			$val = "EUR";
			$momssats = $row->momssats_fi;
			
			if ($sv || $row->beskrivning_fi == "")
				$beskrivning = $row->beskrivning;
			else 
				$beskrivning = $row->beskrivning_fi;
		} else {
			$val = "SEK";
			$momssats = $row->momssats;
			$beskrivning = $row->beskrivning;
		}	
		
		$count = $row->antal;	
		if ($fi)
			$levDatum = $bask->check_lager_bask_fi($row);
		else 
			$levDatum = $bask->check_lager_bask_se($row);
		# If manufacturer is a single dot, set it to ''
		$benamning = $beskrivning;
		if ($row->tillverkare != '.') 
			$benamning = $row->tillverkare . " " . $benamning;
		
		if ($show) {
		?>
			<tr><td bgcolor="#ECECE6"><small><font face="Verdana, Arial" size="1">
	 		<?php echo $benamning; ?>
			</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php echo $count; ?> st</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
			<?php echo number_format($row->utpris*$row->antal, 0, ',', ' ') . " " . $val;		?></font></td>
			<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
			<?php echo number_format(($row->utpris + $row->utpris * $row->momssats) * $row->antal, 0, ',', ' ') . " " . $val; ?>
			</font></td>
			<?php
			print "</tr>\n";
		}			
		
		$messageHtml .= "\n<tr>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$benamning</td>\n";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">$count</td>\n";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= number_format($row->utpris*$row->antal, 0, ',', ' ') . " " . $val;
		$messagehtml .= "</td>\n";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= number_format(($row->utpris + $row->utpris * $row->momssats) * $row->antal, 0, ',', ' ') . " " . $val;
		$messagehtml .= "</td>\n";
		
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"left\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#385F39\">" . $levDatum . "</td>\n";
		$messageHtml .= "</tr>";

		if (strlen($benamning) >= '30')
			$benamning = substr ($benamning, 0, 30) . "...";		
		
		$messageText .= "  $count   ";

		$messageText .= sprintf("%-35s", $benamning);
		$messageText .= number_format(($row->utpris + $row->utpris * $row->momssats) * $row->antal, 0, ',', ' ') . " " . $val;
		$messageText .= "\n";

	endwhile;

	// Sen plocka fram frakten (så att det hamnar sist)

	if ($fi)
		$itemsselect  = "SELECT Orderposter_fi.artnr, Orderposter_fi.utpris, Orderposter_fi.levDatum, Orderposter_fi.bokad, Orderposter_fi.antal, Moms.momssats_fi as momssats, Orderposter_fi.beskrivning ";
	else 
		$itemsselect  = "SELECT Orderposter.artnr, Orderposter.utpris, Orderposter.levDatum, Orderposter.bokad, Orderposter.antal, Moms.momssats, Orderposter.beskrivning ";
	
	//$itemsselect .= "Artiklar.beskrivning, Artiklar_fi.beskrivning_fi  ";
	if ($fi) {	
		$itemsselect .= " FROM Artiklar, Artiklar_fi, Orderposter_fi, Tillverkare, Moms WHERE Orderposter_fi.artnr = Artiklar.artnr AND Orderposter_fi.ordernr = $ordernrladdaom AND (Orderposter_fi.artnr like 'frakt%') ";
	} else {
		$itemsselect .= " FROM Artiklar, Artiklar_fi, Orderposter, Tillverkare, Moms WHERE Orderposter.artnr = Artiklar.artnr AND Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr like 'frakt%') ";
	}
	$itemsselect .= " AND Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod ";	
	$itemres = mssql_query($itemsselect, $conn_standard);

	while ($row = mssql_fetch_object($itemres)) :
		if ($fi) {
			$val = "EUR";
			//$momssats = $row->momssats_fi;
			
			if ($sv || $row->beskrivning_fi == "")
				$beskrivning = $row->beskrivning;
			else 
				$beskrivning = $row->beskrivning_fi;
		} else {
			$val = "SEK";
			//$momssats = $row->momssats;
			$beskrivning = $row->beskrivning;
		}		
		
		# If manufacturer is a single dot, set it to ''
		$benamning = $beskrivning;
		if ($row->tillverkare != '.') 
			$benamning = $row->tillverkare . " " . $benamning;
		
		if ($show) {
		?>
	
			<tr><td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
			<?php echo $benamning; ?></font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">1 st</font></td>
			<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
			<?php  
			echo number_format(($row->utpris), 0, ',', ' ') . " " . $val;
			?>
			</font></td>
			<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
			<?php  
			echo number_format(($row->utpris + $row->utpris * $row->momssats), 0, ',', ' ') . " " . $val;
			?>
			</font></td>
			</tr>
		<?php
		}
		
		$price = $row->utpris + $row->utpris * $row->momssats;
		$messageText .= "  1   ";

		$messageText .= sprintf("%-35s", $benamning);

		$messageText .= number_format($price, 0, ',', ' ') . " " . $val;
			
		$messageText .= "\n";

		$messageHtml .= "<tr>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$benamning</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">$count</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= number_format(($row->utpris ), 0, ',', ' ') . " " . $val;
		$messagehtml .= "</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= number_format(($price ), 0, ',', ' ') . " " . $val;
		$messagehtml .= "</td>";
		
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"left\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
		$messageHtml .= "</tr>";
		

	endwhile;
		
		if ($orderrow->paketRabatt > 0) { 
			if ($sv)	{				
				$Paketrabatt = "Paketrabatt";							
			} else 	 {
				$Paketrabatt = "Pakettialennus";
			}
			
			if ($fi) {
				$momssats = 0.22;
				$val = "EUR";				
			} else {
				$momssats = 0.25;
				$val = "SEK";
			}					
					
		?>
		<?php if ($show) {?>
		<tr>
		  <td bgcolor="#ECECE6" align="left"><font face="Verdana, Arial" size="1"><% echo $Paketrabatt; %></font></td>
		  <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">1 st</font></td>
		  <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php echo number_format($orderrow->paketRabatt, 0, ',', ' ') . " " . $val; ?></font></td>
		  <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php echo number_format(($orderrow->paketRabatt * $momssats) + $orderrow->paketRabatt, 0, ',', ' ') . " " . $val; ?></font></td>
		</tr>
		<?php
		}
		$messageText .= "  1   ";
		$messageText .= sprintf("%-35s", $Paketrabatt);
		$messageText .= number_format(($orderrow->paketRabatt * $momssats) + $orderrow->paketRabatt, 0, ',', ' ') . " " . $val;
		$messageText .= "\n";

		$messageHtml .= "<tr>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$Paketrabatt</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">1</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= number_format($orderrow->paketRabatt, 0, ',', ' ') . " " . $val;
		$messagehtml .= "</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\" align=\"right\">";
		$messageText .= number_format(($orderrow->paketRabatt * $momssats) + $orderrow->paketRabatt, 0, ',', ' ') . " " . $val;
		$messagehtml .= "</td>";
		
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
		$messageHtml .= "</tr>";


		}
$messageHtml .= <<<EOF
<tr><td bgcolor="#ECECE6" colspan="5" height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;


    </font></td></tr>
EOF;

}

function process_orderrowsMail() { // används inte längre. 
	global $ordernrladdaom, $message, $orderrow, $messageText, $messageHtml, $orderrow, $fi, 
	$lev_datum, $lev_datum_normal, $bestallningsgrans, $sv, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $release;	

	
	$betalsatt_id = $orderrow->betalsatt_id;
	
	// Artikel rubrik i mail meddelande
	$messageText .= "\nAntal\t  Vara  \t\t\t\t\t   Pris/st\n\n";

	// Först en fråga för att ta fram allt utom frakten (så att det hamnar först)
	if ($fi) {
		$itemsselect  = "SELECT Orderposter_fi.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Orderposter_fi.utpris as utpris, tillverkare, antal, ";
		$itemsselect .= "Orderposter_fi.levDatum, Orderposter_fi.bokad, Artiklar_fi.bestallningsgrans_fi as bestallningsgrans, Artiklar.lagersaldo, Artiklar_fi.lagersaldo_fi, ";
		$itemsselect .= "Artiklar.lev_datum_normal, Artiklar_fi.lev_datum_norm_fi, Moms.momssats_fi as momssats_fi ";
		$itemsselect .= "FROM Artiklar, Orderposter_fi, Tillverkare, Moms, Artiklar_fi WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Orderposter_fi.artnr = Artiklar.artnr AND ";
		$itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id AND ";
		$itemsselect .= "Orderposter_fi.ordernr = $ordernrladdaom AND (Orderposter_fi.artnr not like 'frakt%') ";
	} else {
		$itemsselect  = "SELECT Orderposter.artnr, Artiklar.beskrivning, Orderposter.utpris, tillverkare, antal, ";
		$itemsselect .= "Orderposter.levDatum, Orderposter.bokad, Artiklar.bestallningsgrans, Artiklar.lagersaldo, Artiklar.lev_datum_normal, Moms.momssats ";
		$itemsselect .= "FROM Artiklar, Orderposter, Tillverkare, Moms WHERE Orderposter.artnr = Artiklar.artnr AND ";
		$itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id AND ";
		$itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr not like 'frakt%') ";	
	}
	//echo $itemsselect;
	$itemres = mssql_query($itemsselect, $conn_standard);

	while ($row = mssql_fetch_object($itemres)) :
		$artnr = $manufacurer = $name = $count = $outprice = $levDatum = $bokad = $bestallningsgrans = $lagersaldo = $normal_leverasntid = "";
		$artnr = $row->artnr;
		$manufacturer = $row->tillverkare;
		if ($fi) {
			if ($row->beskrivning_fi != "")
				$name = $row->beskrivning_fi;
			else
				$name = $row->beskrivning;
		} else {
			$name = $row->beskrivning;
		}
		$count = $row->antal;
		$outprice = $row->utpris;
		$levDatum = $row->levDatum;
		$bokad = $row->bokad;
		$bestallningsgrans = $row->bestallningsgrans;
		$lagersaldo = $row->$lagersaldo;
		$normal_leveranstid = $row->lev_datum_normal;
		$momssats = $row->momssats;		

		//$price = $outprice * 1.25;
		if ($manufacturer == ".") 
			$benamning = $name;
		else
			$benamning = $manufacturer ." " . $name;
		$status = "";
		// TODO:  här skall nya saker ske, hmm..
		if ($bokad == 0) {
			if ($levDatum == "-" || $levDatum == "" || $levDatum == " ")
				$status .= "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">leveransdatum okänt</font>";
			elseif ($levDatum == "0") {
				if ($bestallningsgrans == "0")
					$status .= "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">beställningsvara, normal leveranstid $normal_leveranstid</font>";
				else {
					$status .= "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">leveransdatum okänt</font>";
					//echo "<p>$levDatum  <br>  $status<p>";
				}
			}
			else
				$status .= "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">beräknas in i lager $levDatum</font>";
		}
		else {
			$status .= "\t\t<font size=\"1\" face=\"Verdana, Arial\" color=\"#385F39\">finns i lager</font>";
		
		}
		

		$messageHtml .= "\n<tr>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$benamning</td>\n";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">$count</td>\n";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= sprintf("%10.0f SEK", $outprice*$count);
		$messagehtml .= "</td>\n";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= sprintf("%10.0f SEK", ($outprice + $outprice * $momssats) *$count);
		$messagehtml .= "</td>\n";
		
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"left\"><font face=\"Verdana, Arial\" size=\"1\">$status</td>\n";
		$messageHtml .= "</tr>";

		if (strlen($benamning) >= '30')
			$benamning = substr ($benamning, 0, 30) . "...";		
		
		$messageText .= "  $count   ";

		$messageText .= sprintf("%-35s", $benamning);
		$messageText .= sprintf("%10.0f SEK", ($outprice + $outprice * $momssats) *$count);
		$messageText .= "\n";
		

	endwhile;

	// Sen plocka fram frakten (så att det hamnar sist)
	if ($fi) {
		$itemsselect  = "SELECT Orderposter_fi.artnr, Artiklar_fi.beskrivning_fi as beskrivning, Orderposter_fi.utpris, Orderposter_fi.antal, Moms.momssats_fi as momssats ";
		$itemsselect .= "FROM Artiklar, Artiklar_fi, Orderposter_fi, Moms WHERE Orderposter_fi.artnr = Artiklar.artnr AND Artiklar.artnr = Artiklar_fi.artnr_fi AND Moms.moms_id = Artiklar.momskod AND ";
		$itemsselect .= "Orderposter_fi.ordernr = $ordernrladdaom AND (Orderposter_fi.artnr like 'frakt%') ";
	} else {
		$itemsselect  = "SELECT Orderposter.artnr, Artiklar.beskrivning, Orderposter.utpris, Orderposter.antal, Moms.momssats ";
		$itemsselect .= "FROM Artiklar, Orderposter, Moms WHERE Orderposter.artnr = Artiklar.artnr AND Moms.moms_id = Artiklar.momskod AND ";
		$itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr like 'frakt%') ";	
	}

	$itemres = mssql_query($itemsselect, $conn_standard);

	while ($row = mssql_fetch_object($itemres)) :
		$name = $row->beskrivning;
		$count = $row->antal;
		$outprice = $row->utpris;
		$count = $row->antal;
		  $price = $outprice + $outprice * $row->momssats;
		  $messageText .= "  1   ";

		$messageText .= sprintf("%-35s", $name);
		if ($fi) 
			$messageText .= sprintf("%10.0f SEK", $price);
		else
			$messageText .= sprintf("%10.0f EUR", $price);
		$messageText .= "\n";

		$messageHtml .= "<tr>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$name</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">$count</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= sprintf("%10.0f SEK", $outprice); 
		$messagehtml .= "</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= sprintf("%10.0f SEK", $price); 
		$messagehtml .= "</td>";
		
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"left\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
		$messageHtml .= "</tr>";
		

	endwhile;

		if ($orderrow->paketRabatt > '0') { 

		$messageText .= "  1   ";
		$messageText .= sprintf("%-35s", Paketrabatt);
		$messageText .= sprintf("%10.0f SEK", ($orderrow->paketRabatt * 1.25));
		$messageText .= "\n";

		$messageHtml .= "<tr>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">Paketrabatt</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">1</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= sprintf("%10.0f SEK", $orderrow->paketRabatt); 
		$messagehtml .= "</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\" align=\"right\">";
		$messageHtml .= sprintf("%10.0f SEK", $orderrow->paketRabatt * 1.25); 
		$messagehtml .= "</td>";
		
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
		$messageHtml .= "</tr>";


		}
$messageHtml .= <<<EOF
<tr><td bgcolor="#ECECE6" colspan="5" height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;


    </font></td></tr>
EOF;

}

function show_summaryMail() {

	global $orderrow, $messageText, $messageHtml, $fi, $sv;



if ($fi) 
	$val = " EUR";
else
	$val = " SEK";
$summaexmoms = number_format($orderrow->netto, 0, ',', ' ') . " " . $val;	
if ($orderrow->land_id == 47 || $orderrow->land_id == '999') {
	$moms = sprintf ("%10.0f SEK", 0);
	$summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val;
	}
else {
	$moms = number_format($orderrow->moms, 0, ',', ' ') . " " . $val;
	$summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val;
	}

if ($fi && !$sv) {
$messageText .= <<<EOF

  Alv 0%:  $summaexmoms
  Alv:     $moms
  Sis Alv: $summaMedMoms

EOF;
$messageHtml .= <<<EOF
<tr>
  <td height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;
    </font></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Alv 0%:</font></p>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$summaexmoms</font>
</font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Alv:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$moms</font></font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Sis Alv:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><b><font size="1">$summaMedMoms</font></b> 
</font></td>
</tr>
EOF;

} else {
$messageText .= <<<EOF

  exkl. moms:       $summaexmoms
  Moms:             $moms
  Totalt:           $summaMedMoms

EOF;
$messageHtml .= <<<EOF
<tr>
  <td height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;
    </font></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">exkl moms:</font></p>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$summaexmoms</font>
</font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">moms:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$moms</font></font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1"> inkl moms:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><b><font size="1">$summaMedMoms</font></b> 
</font></td>
</tr>
EOF;

}

}



function write_invoiceinfo() {
	global $orderrow, $kundrow;

	?>

	<table width="100%" border="0" cellspacing="1" cellpadding="2">
	<tr>
	<td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial"><b>
	<?php if ($fi & !$sv) { ?>
	<?php if ($kundrow->foretag == -1) { ?>Laskutusosoite<?php } else { ?>Osoite: <?php } ?>
	<?php } else { ?>
	<?php if ($kundrow->foretag == -1) { ?>Fakturaadress<?php } else { ?>Adress: <?php } ?>
	<?php } ?>
	</b></font></small></small></td>
	</tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->namn; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->co; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->adress; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->postnr . " " . $orderrow->postadr; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->land; ?></font></small></small></td></tr>
	</table>

<?php

} # End of write_invoiceinfo


// Create a table with delivery address
function write_deliveryinfo() {
	global $orderrow, $sv;
	?>
	<table width="100%" border="0" cellspacing="1" cellpadding="2">
	<tr>
	<td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial"><b>
	<?php if ($sv) { ?>Leveransadress<?php } else { ?>Toimitusosoite<?php } ?>
	</b></font></small></small></td>
	</tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->lnamn; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->lco; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->ladress; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->lpostnr . " " . $orderrow->lpostadr; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->land; ?></font></small></small></td></tr>
	</table>

	<?php

} # End of write_deliveryinfo
?>

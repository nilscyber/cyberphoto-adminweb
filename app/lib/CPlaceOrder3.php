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
	global $REMOTE_ADDR;

	//$ordersumma = number_format($orderrow->netto + $orderrow->moms, 0, "", "");
	$ordersumma = $orderrow->totalsumma;
	$ordersumma = $ordersumma * 100;

	$datat = 'data=';
	$datat .= rawurlencode('1:vara:1:' . $ordersumma . ':');

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
	$datat .= "&uses3dsecure=true";
	$datat .= "&metod=order";

	return rtrim(debiCrypt($datat));

}


function write_invoiceinfo() {
	global $orderrow, $kundrow;

	?>

	<table width="100%" border="0" cellspacing="1" cellpadding="2">
	<tr>
	<td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial"><b><?php if ($kundrow->foretag == -1):?>Fakturaadress<?php else: ?>Adress: <?php endif; ?></b></font></small></small></td>
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
	global $orderrow;

	?>

	<table width="100%" border="0" cellspacing="1" cellpadding="2">
	<tr>
	<td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial"><b>Leveransadress</b></font></small></small></td>
	</tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->lnamn; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->lco; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->ladress; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->lpostnr . " " . $orderrow->lpostadr; ?></font></small></small></td></tr>
	<tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->land; ?></font></small></small></td></tr>
	</table>

	<?php

} # End of write_deliveryinfo

function process_orderrows() {
	global $ordernrladdaom, $message, $orderrow, $messageText, $messageHtml, $orderrow;
	
	$betalsatt_id = $orderrow->betalsatt_id;
	
	// Artikel rubrik i mail meddelande
	$messageText .= "\nAntal\t  Vara  \t\t\t\t\t   Pris/st\n\n";

	// Först en fråga för att ta fram allt utom frakten (så att det hamnar först)
	$itemsselect  = "SELECT Orderposter.artnr, Artiklar.beskrivning, Orderposter.utpris, tillverkare, antal, Moms.momssats, ";
	$itemsselect .= "Orderposter.levDatum, Orderposter.bokad, Artiklar.bestallningsgrans, Artiklar.lagersaldo, Artiklar.lev_datum_normal ";
	$itemsselect .= "FROM Artiklar, Orderposter, Tillverkare, Moms WHERE Artiklar.momskod = Moms.moms_id AND Orderposter.artnr = Artiklar.artnr AND ";
	$itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND ";
	$itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr not like 'frakt%') ";
	
	$itemres = mssql_query($itemsselect);
	
	while ($row = mssql_fetch_object($itemres)) :
		$artnr = $manufacurer = $name = $count = $outprice = $levDatum = $bokad = $bestallningsgrans = $lagersaldo = $normal_leverasntid = "";
		$artnr = $row->artnr;
		$manufacturer = $row->tillverkare;
		$name = $row->beskrivning;
		$count = $row->antal;
		$outprice = $row->utpris;
		$levDatum = $row->levDatum;
		$bokad = $row->bokad;
		$bestallningsgrans = $row->bestallningsgrans;
		$lagersaldo = $row->$lagersaldo;
		$normal_leveranstid = $row->lev_datum_normal;
		$momssats = $row->momssats;
		?>
		<tr><td bgcolor="#ECECE6"><small><font face="Verdana, Arial" size="1">
		<?php
		# If manufacturer is a single dot, set it to ''
		if ($manufacturer == '.') { $manufacturer = ''; };
		echo "$manufacturer $name";
		?>
		</font></td>
		<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php echo $count; ?> st</font></td>
		<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
		<?php printf("%10.0f SEK", $outprice*$count); ?></font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
		<?php printf("%10.0f SEK", ($outprice + $outprice * $momssats) *$count); ?>
		 </font></td>
		<?php
		print "</tr>\n";

		//$price = $outprice * 1.25;
		$benamning = $manufacturer ." " . $name;
		$status = "";
		//echo $levDatum;
		//echo "tjosan: $artnr ;$levDatum;";
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
		/**
		if ($bokad == 0) {
			if ($levDatum == "-" || $levDatum == "") {
				$messageText .= "\t\tleveransdatum okänt";
			}
			elseif ($levDatum == 0) {
				if ($bestallningsgrans == 0)
					$messageText .= "\t\tbeställningsvara, normal leveranstid $normal_leveranstid";
				else
					$messageText .= "\t\tleveransdatum okänt";
			}
			else
				$messageText .= "\t\tberäknas in i lager: $levDatum";
				
			
		
		
		
		}
		else {
			$messageText .= "\t\tfinns i lager";
		
		}
		$messageText .= "\n";
		*/
		

	endwhile;

	// Sen plocka fram frakten (så att det hamnar sist)
	$itemsselect  = "SELECT Orderposter.artnr, Artiklar.beskrivning, Orderposter.utpris, Orderposter.antal, Moms.momssats ";
	$itemsselect .= "FROM Artiklar, Orderposter, Moms WHERE Orderposter.artnr = Artiklar.artnr AND Artiklar.momskod = Moms.moms_id AND ";
	$itemsselect .= "Orderposter.ordernr = '$ordernrladdaom' AND (Orderposter.artnr like 'frakt%') ";

	$itemres = mssql_query($itemsselect);

	while ($row = mssql_fetch_object($itemres)) :
		$name = $row->beskrivning;
		$count = $row->antal;
		$outprice = $row->utpris;
		$count = $row->antal;
		$momsts = $row->momssats;
		?>

		<tr><td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
		<?php echo $name; ?></font></td>
		<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">1 st</font></td>
		<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
		<?php  printf("%10.0f SEK", $outprice); ?>
		</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
		<?php  printf("%10.0f SEK", $outprice + $outprice * $momsts); ?>
		</font></td>
		</tr>
		<?php
		  $price = $outprice + $outprice * $momsts;
		  $messageText .= "  1   ";

		$messageText .= sprintf("%-35s", $name);
		$messageText .= sprintf("%10.0f SEK", $price);
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

		if ($orderrow->rabatt > '0') { ?>
		<tr>
		  <td bgcolor="#ECECE6" align="left"><font face="Verdana, Arial" size="1">Rabatt</font></td>
		  <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">1 st</font></td>
		  <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php printf("%10.0f SEK", $orderrow->rabatt); ?></font></td>
		  <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php printf("%10.0f SEK", $orderrow->rabatt); ?></font></td>
		</tr>
		<?php
		$messageText .= "  1   ";
		$messageText .= sprintf("%-35s", Rabatt);
		$messageText .= sprintf("%10.0f SEK", ($orderrow->rabatt * 1.25));
		$messageText .= "\n";

		$messageHtml .= "<tr>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">Rabatt</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">1</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= sprintf("%10.0f SEK", $orderrow->rabatt); 
		$messagehtml .= "</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\" align=\"right\">";
		$messageHtml .= sprintf("%10.0f SEK", $orderrow->rabatt * 1.25); 
		$messagehtml .= "</td>";
		
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
		$messageHtml .= "</tr>";


		}
$messageHtml .= <<<EOF
<tr><td bgcolor="#ECECE6" colspan="5" height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;


    </font></td></tr>
EOF;

}

function show_summary() {

	global $orderrow, $messageText, $messageHtml;

	?>
	</center></div>
	<div align="center"><center><table border=0 cellspacing=1 cellpadding=2>
	<tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">exkl moms:</font></small></small></td>
	<td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
	<?php echo number_format($orderrow->netto, 0, ',', ' ') . " SEK"; ?>
	</font></small></small></td></tr>

	<tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">moms:</font></small></small></td>
	<td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
	<?php echo number_format($orderrow->moms, 0, ',', ' ') . " SEK"; ?>
	</font></small></small></td></tr>

	<tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">total summa:</font></small></small></td>
	<td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
	<?php echo number_format($orderrow->totalsumma, 0, ',', ' ') . " SEK"; ?>
	</font></small></small></td></tr>

	</table></center></div>
	<?php

$summaexmoms = number_format($orderrow->netto, 0, ',', ' ') . " SEK";
if ($orderrow->land_id == '47' || $orderrow->land_id == '999') {
	$moms = sprintf ("%10.0f SEK", 0);
	$summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " SEK";
	}
else {
	$moms = number_format($orderrow->moms, 0, ',', ' ') . " SEK";
	$summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " SEK";
	}


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

function write_formline($artnr, $momssats, $momskod) {

	global $newordernr, $count, $outprice, $name, $lagersaldo, $complete_order, $till_kund, $inkommet;
	static $i = 0;

 	// Nollställ variabler
 	$totalpacutpris = "";
  	$rabatt = "";
	$check = "";
	$inserted = "";
	$bokad = "";
	$levDatum = "";

if (eregi("pac$", $artnr)) {

	$select = "SELECT artnr, Artiklar.beskrivning, lagersaldo, antal, till_kund, Artiklar.utpris, Moms.momssats, Artiklar.momskod ";
	$select .= "FROM Artiklar, Paketpriser, Moms WHERE Artiklar.artnr=Paketpriser.artnr_del AND Artiklar.momskod = Moms.moms_id ";
	$select .= "AND Paketpriser.artnr_paket = '$artnr' ";

	$res = mssql_query ($select);
	while ($row = mssql_fetch_object($res)):

		$artnr2 = $row->artnr;
		$lagersaldo2 = $row->lagersaldo;
		$antal = $row->antal;
		$till_kund2 = $row->till_kund;
		$del_utpris = $row->utpris;
		$name2 = $row->beskrivning;
		$momssats = $row->momssats;
		$momskod = $row->momskod;
		//$levDatum = "";
		unset($levDatum);

		// Plussa på totala paketpriset
		$totalpacutpris += $del_utpris*$antal;



		// Titta hur många av paketdelen vi behöver
		$newCount = $antal*$count;

		// Titta om det finns tillräckligt på lager och boka upp i så fall
		if ($lagersaldo2 >= $newCount) {

			$bokad = 1;
			$lagersaldo_new = $lagersaldo2 - $newCount;
			$till_kund_new = $till_kund2 + $newCount;

			$update = "UPDATE Artiklar set lagersaldo = $lagersaldo_new, till_kund = '$till_kund_new' WHERE artnr='$artnr2'";

			mssql_query($update);
		}

			# Om det saknas på lager
		else {
			$bokad = 0;
			$complete_order = 'no';
			$levDatum = getLevTid($artnr2, $newCount, $newordernr);
			//getLevTid ($artnr, $count, $ordernr)
		}

		// Titta om artikeln redan finns inlagd.
		$checkifexist = "SELECT antal FROM Orderposter WHERE ordernr = $newordernr AND artnr = '$artnr2' ";
		$res2 = mssql_query($checkifexist);
		$row2 = mssql_fetch_object($res2);
		$inserted = $row2->antal;
		//echo $artnr2;
		// Om den existerade, plussa ihop och uppdatera antalet till det nya
		if (mssql_num_rows ($res2) > 0) {
			  $insertCount = $newCount + $inserted;
		$update = "update Orderposter set antal = '$insertCount' WHERE ordernr = $newordernr AND artnr = '$artnr2' ";
		mssql_query ($update);
		 }

		// Stoppa annars in som vanligt
		else {

		$insert  = "insert into Orderposter (ordernr, artnr, antal, utpris, bokad, beskrivning, levDatum, levDatumDatumInlagt, momssats, momskod, paketArtnr) ";
		$insert .= "values ($newordernr, '$artnr2', $newCount, $del_utpris, '$bokad', '$name2', '$levDatum', '$inkommet', $momssats, $momskod, '$artnr') ";

		mssql_query ($insert);
		}


	endwhile;

	// Titta efter hur mycket rabatten blir i paketet mot enskilda delar samt
	// gångra med antal paket

	$ny_rabatt = ($totalpacutpris - $outprice) * $count;

	// Titta om det finns någon rabatt och kommentar redan inlagd
	$rabattkoll = "SELECT rabatt, kommentar FROM Ordertabell WHERE ordernr = '$newordernr'";
	$res = mssql_query ($rabattkoll);
	$row3 = mssql_fetch_object($res);
	$old_rabatt = $row3->rabatt;
	$oldkommentar = $row3->kommentar;

	// Titta om "rabatt ord" redan är inlagda, och lägg till text utefter det.
	if (eregi("Rabatten avser", $oldkommentar)) {

		$newkommentar = $oldkommentar . ", $name: $ny_rabatt kr";
	}
	else 	{

		$newkommentar = "" . $oldkommentar . "Rabatten avser paketpris på $name: ";
		$newkommentar .= sprintf("%.2f", $ny_rabatt) . " kr";
	}


	// Lägg ihop existerande rabatt med nya paketrabatten och uppdatera tabellen.
	$tot_rabatt = $old_rabatt + $ny_rabatt;
	$insert_rabatt = "UPDATE Ordertabell SET kommentar = '$newkommentar', paketRabatt = paketRabatt + " . $ny_rabatt . " WHERE ordernr = '$newordernr'";
	mssql_query ($insert_rabatt);


   }
   else {
   	$receiver = "";
	if (eregi("presentkort", $artnr)) {
		$date = date("Y-m-d H:i:s");
		
		$outprice = $GLOBALS['giftCard'][$i];
		$receiver = $GLOBALS['giftCardReceiver'][$i];
		newGiftCard($newordernr, $outprice, $date, $artnr, $receiver); 
		$i += 1;	
	}
	  // Om produkten är någon form av frakt så bokas den automatiskt upp direkt.
	  if (eregi("frakt", $artnr)) {
	  	$bokad = '1';
	  }
	  else {

		 // Titta om det finns tillräckligt på lager och boka upp i så fall
		if ($lagersaldo >= $count) {

			$bokad = 1;
			$lagersaldo_new = $lagersaldo - $count;
			$till_kund_new = $till_kund + $count;

			$update = "UPDATE Artiklar set lagersaldo = $lagersaldo_new, till_kund = $till_kund_new WHERE artnr='$artnr'";
			mssql_query ($update);

			}
			else {
				$bokad = '0';
				$complete_order = 'no';
				$levDatum = getLevTid($artnr, $count, $newordernr);
			}


	  }

	// Titta om artikeln redan finns inlagd.
	$checkifexist2 = "SELECT antal FROM Orderposter WHERE ordernr = $newordernr AND artnr = '$artnr' ";
	//echo $checkifexist2;
	$res3 = mssql_query($checkifexist2);
	//if ($res3) 
	$row3 = mssql_fetch_object($res3);
	$inserted2 = $row3->antal;

	// Om den existerade, plussa ihop och uppdatera antalet till det nya
	if (mssql_num_rows ($res3) > '0') {
		  $insertCount2 = $count + $inserted2;
	$update2 = "update Orderposter set antal = $insertCount2 WHERE ordernr = $newordernr AND artnr = '$artnr' ";
	//echo $update2;
	mssql_query ($update2);
	 }

	// Stoppa annars in som vanligt
	else {
	  // Lägg till artikeln till ordern
	  if ($receiver != "")
	  	$name .= " (till " . $receiver . ")";
	  $insert  = "insert into Orderposter (ordernr, artnr, antal, utpris, bokad, beskrivning, levDatum, levDatumDatumInlagt, momssats, momskod) ";
	  $insert .= "values ($newordernr, '$artnr', $count, $outprice, $bokad, '$name', '$levDatum', '$inkommet', $momssats, $momskod) ";
	  // ändring tillfällig
	  //echo $insert;
	  if ($artnr != "")
	  	mssql_query ($insert);
  	}

  	}
}
function countValueInBasketMs($kundvagn) {

	global $kundvagn, $goodsvalue, $artnr, $count, $lagersaldo, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans;
	
	//include ("CConnect.php");

	
	$freight_check = NULL;

	# Get the cookie kundvagn
	$answers = $kundvagn;
		
	if (ereg ("(grejor:)(.*)", $answers,$matches)) {
		# Split the number of items and article id s into a list
		$orderlista = $matches[2];
		$argument = split ("\|", $orderlista);
	}

	$goodscounter=0;
	$goodsvalue=0;
	 
	$n = count($argument);
	for ($i=0; ($i < $n);  $i+=2) {

		$arg = $argument[$i];        # Article id
		$count = $argument[$i+1];    # Keeps track of the number of the same article

		$select  = "SELECT artnr, utpris, frakt From Artiklar WHERE artnr='$arg'";
		
		# Alla värden försvinner inte, så därför måste vi göra enligt nedan
		$artnr = $frakt = "";
		$utpris = $extra_freight = 0;

		$row = mssql_fetch_array(mssql_query ($select));
		extract($row);
		
		// Lägg på extra frakt om det behövs
		if ($frakt) {
		
			if ($extra_freight < '2') {
				
				$extra_freight = $frakt; 
				
				}
		}
		
		$goodscounter += '1';
		$goodsvalue += ($utpris*$count);
		
		$description .= $beskrivning . " " . $kommentar;
		if ($extra_freight): 
			    if ($extra_freight == "1") $extra_freight_artnr = 'frakt+';
		    elseif ($extra_freight == "2") $extra_freight_artnr = 'frakt+2';
		    else $extra_freight_artnr = 'frakt+'; // för säkerhets skull
		    $select = "select utpris from Artiklar where artnr='$extra_freight_artnr'";

		    $res = mssql_query ($select);
		    $row = mssql_fetch_object ($res);

		    $outprice = $row->utpris;

		    $manufacturer = "";
		    $goodsvalue += $outprice;
		endif;
	}
	
	return $goodsvalue;
}

function getLevTid ($artnr, $count, $ordernr)  { // artnr som skall kollas, antal samt inkommet datum på ordern
	global $fi;
	$articleinfo = "";
	setlocale (LC_ALL, 'en_US');
	//setlocale (LC_ALL, "sv_SE");

	// kolla först inkommet för att få fram köplats. 
	// ange dagens datum om koll för kundvagnen 
	
	if ($ordernr == NULL) {
		$inkommetOur = date("Y-m-d H:i:s");
	}
	else {
		$select = "SELECT inkommet from Ordertabell WHERE ordernr = $ordernr ";

		$res = mssql_query ($select);

		if ($res)  {
			$row = mssql_fetch_object ($res);
			$inkommetOur = $row->inkommet;
		}
	}
	
	$inkommetOur = strtotime($inkommetOur); // omvandla till timestamp för enkelhet. 
	
	$totalNeed = 0;
	$ordernr = NULL; // tag bort värdet eftersom samma variable namn används nedan
	//echo $inkommetOur;	
	// räkna först ut hur många som står före
	$select =  "SELECT Orderposter.ordernr, Orderposter.antal, Ordertabell.inkommet FROM Orderposter, Ordertabell ";
	$select .= "WHERE Orderposter.ordernr = Ordertabell.ordernr AND ";
	$select .= "Orderposter.artnr = '$artnr' AND Orderposter.bokad = 0 ";
	$select .= "ORDER BY Ordertabell.inkommet ASC";
	
	//echo $select;
	
	$res = mssql_query ($select);
	
	//if (mssql_num_rows ($res) == 1) { // Det fungerar inte att jämföra datum riktigt. 
					  // sätter därför = 0 om det bara finns en order. 
	//	$totalNeed = 0;
	//}
	if (mssql_num_rows ($res) > 0) {
		
		while ($row = mssql_fetch_array ($res)):
			extract ($row);
			$inkommet = strtotime($inkommet); //till timestamp för jämförelse
			
			//echo "här: $inkommet, $inkommetOur<br>";
			if ($inkommet < $inkommetOur) { // jämför om vårt ordernr ligger efter i kön
							    // om den gör det, stopppa, då har vi alla poster
							    // före i kön. Observera snuttarna runt datumet. 
							    // de behövs för att det skall fungera
				//echo "<br>yes<br>";				
				$totalNeed += $antal; // antalet som står före.
				//echo "$totalNeed, $ordernr";
			}
			else
				break;
				
		endwhile;
		//echo "<br>$totalNeed<br>";
		
	}
	
	$totalNeed += $count; // lägg på de vi behöver till totala behovet (för att det skall 
			      // nå fram till "vår" köplats)
	
	$antal_sum = 0;
	$select = "SELECT inkopsnr, antal, levdatum, levererat FROM Inkopsposter WHERE artnr = '$artnr' "; 
	$select .= "AND antal != levererat ";
	$select .= "ORDER BY inkopsnr ASC ";
	$res = mssql_query ($select);
	
	//echo $select;
	if (mssql_num_rows ($res) > '0') {
		
		while ($row = mssql_fetch_array($res)): 
			extract ($row);
			$antal_sum += ($antal - $levererat); // den som inte är levererad summeras ihop
			
			if ($antal_sum >= $totalNeed) {      // tills vi fått de vi behöver då stoppar vi. 
				$articleinfo = $levdatum;    // det datum som finns på den leveransen. 
				break;
			}
		//echo "$antal_sum $totalNeed.$articleinfo<br>";
		//$antal = $levererat = 0;
		endwhile;	
	
	}
	else
	
	{
		$articleinfo = 0; // kod om inköpsposter saknas
		/**	
		if ($fi == 'yes')
			$articleinfo = "toimituspäivämäärä ei ole määritelty";
		else
			$articleinfo = "leveransdatum okänt";
		*/	
	}
/**
if ($articleinfo == "" || $articleinfo == "-") { // om det inte var beställt så... (levdatum okänt tills vidare)
	if ($fi == 'yes')
		$articleinfo = "toimituspäivämäärä ei ole määritelty";
	else
		$articleinfo = "leveransdatum okänt";
}
*/

return $articleinfo;
//setlocale (LC_TIME, "sv_SE");
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
	require("class.phpmailer.php");
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

function process_orderrowsMail() {
	global $ordernrladdaom, $message, $orderrow, $messageText, $messageHtml, $orderrow;
	
	$betalsatt_id = $orderrow->betalsatt_id;
	
	// Artikel rubrik i mail meddelande
	$messageText .= "\nAntal\t  Vara  \t\t\t\t\t   Pris/st\n\n";

	// Först en fråga för att ta fram allt utom frakten (så att det hamnar först)
	$itemsselect  = "SELECT Orderposter.artnr, Artiklar.beskrivning, Orderposter.utpris, tillverkare, antal, ";
	$itemsselect .= "Orderposter.levDatum, Orderposter.bokad, Artiklar.bestallningsgrans, Artiklar.lagersaldo, Artiklar.lev_datum_normal, Moms.momssats ";
	$itemsselect .= "FROM Artiklar, Orderposter, Tillverkare, Moms WHERE Orderposter.artnr = Artiklar.artnr AND ";
	$itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id AND ";
	$itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr not like 'frakt%') ";

	$itemres = mssql_query($itemsselect);

	while ($row = mssql_fetch_object($itemres)) :
		$artnr = $manufacurer = $name = $count = $outprice = $levDatum = $bokad = $bestallningsgrans = $lagersaldo = $normal_leverasntid = "";
		$artnr = $row->artnr;
		$manufacturer = $row->tillverkare;
		$name = $row->beskrivning;
		$count = $row->antal;
		$outprice = $row->utpris;
		$levDatum = $row->levDatum;
		$bokad = $row->bokad;
		$bestallningsgrans = $row->bestallningsgrans;
		$lagersaldo = $row->$lagersaldo;
		$normal_leveranstid = $row->lev_datum_normal;
		$momssats = $row->momssats;
		# If manufacturer is a single dot, set it to ''

		//$price = $outprice * 1.25;
		$benamning = $manufacturer ." " . $name;
		$status = "";
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
	$itemsselect  = "SELECT Orderposter.artnr, Artiklar.beskrivning, Orderposter.utpris, Orderposter.antal, Moms.momssats ";
	$itemsselect .= "FROM Artiklar, Orderposter, Moms WHERE Orderposter.artnr = Artiklar.artnr AND Moms.moms_id = Artiklar.momskod AND ";
	$itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr like 'frakt%') ";

	$itemres = mssql_query($itemsselect);

	while ($row = mssql_fetch_object($itemres)) :
		$name = $row->beskrivning;
		$count = $row->antal;
		$outprice = $row->utpris;
		$count = $row->antal;
		  $price = $outprice + $outprice * $row->momssats;
		  $messageText .= "  1   ";

		$messageText .= sprintf("%-35s", $name);
		$messageText .= sprintf("%10.0f SEK", $price);
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

		if ($orderrow->rabatt > '0') { 

		$messageText .= "  1   ";
		$messageText .= sprintf("%-35s", Rabatt);
		$messageText .= sprintf("%10.0f SEK", ($orderrow->rabatt * 1.25));
		$messageText .= "\n";

		$messageHtml .= "<tr>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">Rabatt</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">1</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
		$messageHtml .= sprintf("%10.0f SEK", $orderrow->rabatt); 
		$messagehtml .= "</td>";
		$messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\" align=\"right\">";
		$messageHtml .= sprintf("%10.0f SEK", $orderrow->rabatt * 1.25); 
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

	global $orderrow, $messageText, $messageHtml;


$summaexmoms = number_format($orderrow->netto, 0, ',', ' ') . " SEK";
if ($orderrow->land_id == '47' || $orderrow->land_id == '999') {
	$moms = sprintf ("%10.0f SEK", 0);
	$summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " SEK";
	}
else {
	$moms = number_format($orderrow->moms, 0, ',', ' ') . " SEK";
	$summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " SEK";
	}


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



?>

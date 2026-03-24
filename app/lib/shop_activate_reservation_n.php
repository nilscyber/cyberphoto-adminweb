<?php

include "kreditor.php";
include "shop_info.php";
global $eid;
global $secret;
$tstArray = array();
array_push($tstArray, "tjosan");

$goodslist = array();
include("connections.php");

$s = "SELECT Ordertabell.*, kreditor_reference.*, Kund.salary, Kund.electronic_invoice FROM Ordertabell, kreditor_reference, Kund ";
$s .=" WHERE Ordertabell.ordernr = kreditor_reference.ordernr AND Kund.kundnr = Ordertabell.kundnr AND (betalsatt_id = 7 OR betalsatt_id = 13 OR betalsatt_id = 14) ";

$s .= " AND skickat is not null and Ordertabell.netto > 0 ";
$s .=" AND (levklar = 3 OR levklar = 11) ";

// testrad för enskilda ordrar
//$s .= " AND Ordertabell.ordernr = 506314 ";
//$s .= " AND Ordertabell.skickat < '2009-11-30' ";
// för ordrar som saknar reservation sätt reservationsnumret till 0!!!

// andra tester som gjorts
//$s .= " AND Ordertabell.ordernr >= 317481 ";
//$s .= " AND (Ordertabell.restOrderMain > 317481 OR restOrderMain is null) ";
//$s .= " AND skickat < '2007-12-22'";

$s .= " ORDER BY Ordertabell.ordernr ";
//echo  $s; exit;
$res = mssql_query ($s, $conn_standard);
if (mssql_num_rows($res) == 0) {
	echo "inga ordrar finns";
	echo "<br>" . $s;
	//exit;
}

$i = 0;
$mess = "";
$messAlt = "";
$restOrder = 0;

while ($row = mssql_fetch_object($res)) {

	if ($row->restfaktura == 1) 
		$invoiceAll = true;
	else 
		$invoiceAll = false;
	// detta används inte här utan delas upp i orderhanteringen istället. 
	if ($row->restorder != "" && !$invoiceAll) { // finns det en restorder och är det några pengar på den? 
		if (is_numeric($row->restorder)) {
			if ($row->restorder > 9000) {
			
				//gör en split_reservation på reservationen som hör till denna order och uppdatera den nya med nytt reservationsnummer. 
				
				// först split_reservation. 
			//split_reservation($rno, $splitAmount, $orderid1, $orderid2, $flags, $eid, $secret,
			//	&$result)
			
				
			}
		}
	
	}
		
		
	$i +=1;
	echo "räknare: " . $i . "<p>";
	//if ($i > 10) exit;

	echo "ordernr: " . $row->ordernr . "<br>";
	if ($row->fland_id == 358 || $row->fland_id == 999)  {
		$country = $KRED_ISO3166_FI;	
		if ($row->lang == "sv")
			$language = $KRED_ISO639_SV;			
		else
			$language = $KRED_ISO639_FI; 			
		$pno_encoding = $KRED_FI_PNO;		
		$cntry = "fi";
	} else {
		$country = $KRED_ISO3166_SE;		
		$language = $KRED_ISO639_SV;
		$pno_encoding = $KRED_SE_PNO;
		
		$cntry = "se";
	}

	if ($row->currency == "EUR")
		$currency = $KRED_EUR;
	else
		$currency = $KRED_SEK;
		
	echo " \n<br>pno_encoding: " . $pno_encoding;
	echo " \n<br>lang: " . $language;
	echo " \n<br>curr: " . $currency;
	echo " \n<br>country: " . $cntry;
	echo " \n<br>country: " . $country;
	//exit;
	
	$fadr = mk_address($row->firstName, $row->lastName, $row->co, $row->postnr, $row->postadress, $cntry);	
	if ($row->leveranssatt_id == 26)
		$ladr = $fadr;
	else
		$ladr = mk_address($row->firstNameDel, $row->lastNameDel, $row->lco, $row->lpostnr, $row->lpostadr, $cntry);		
	
	if ( (     ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$", $row->email )) && ( $row->electronic_invoice == -1) ) 
	//if ( (     ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$", $row->email ))) 
		$flags = $KRED_SEND_BE_EMAIL;
	else
		$flags = $KRED_SEND_BY_MAIL;
	
	// Om testmode, avbocka denna rad: 
	//$flags = $KRED_TEST_MODE;
	echo "flagga: " . $flags;
	
	
	//email = 8, mail = 4, test = 2;
/** översättningar

"Avtalsrabatt" Sopimusalennus
"Paketrabatt"  Kokonaisalennus
"Övrig rabatt" Muu alennus
Öresavrundning: Pyöristys


*/
	
	$goodslist = mkGoodslist($row->ordernr, $row);
	//echo "rabatt: " . $row->paketRabatt;
	// om vi inte har någon annan moms än 25% (eller 23%) gör vi med vanliga 
	//echo "här, ja" . $row->rabatt_moms3 . ":" .$row->rabatt_moms0;

	if ($row->fland_id == 358)  {
		$momssats = 23;
	} elseif ($row->fland_id == 999) {
		$momssats = 0;
	} else {
		$momssats = 25;
	}

	if ($row->rabatt_moms3 == 0 AND $row->rabatt_moms0 == 0) {
		
		if ($row->avtalsRabatt > 0) {
			if ($row->fland_id == 358 || $row->fland_id == 999)  {
				if ($row->lang == "fi") {
					array_push($goodslist, mk_goods(1, "rabatt", "Sopimusalennus", -$row->avtalsRabatt*100, $momssats, 0));
				} else {
					array_push($goodslist, mk_goods(1, "rabatt", "Avtalsrabatt", -$row->avtalsRabatt*100, $momssats, 0));
				}			
			} else {
				array_push($goodslist, mk_goods(1, "rabatt", "Avtalsrabatt", -$row->avtalsRabatt*100, $momssats, 0));
			}
		}
		if ($row->paketRabatt > 0) {
			if ($row->fland_id == 358 || $row->fland_id == 999)  {
				if ($row->lang == "fi") {
					array_push($goodslist, mk_goods(1, "rabatt", "Kokonaisalennus", -$row->paketRabatt*100, $momssats, 0));
				} else {		
					array_push($goodslist, mk_goods(1, "rabatt", "Paketrabatt", -$row->paketRabatt*100, $momssats, 0));
				}				
			
			} else {
				array_push($goodslist, mk_goods(1, "rabatt", "Paketrabatt", -$row->paketRabatt*100, $momssats, 0));
			}		
		}
		if ($row->rabatt > 0) {
			if ($row->fland_id == 358 || $row->fland_id == 999)  {
				if ($row->lang == "fi") {
					array_push($goodslist, mk_goods(1, "rabatt", "Muu alennus", -$row->rabatt*100, $momssats, 0));
				} else {
					array_push($goodslist, mk_goods(1, "rabatt", "Övrig rabatt", -$row->rabatt*100, $momssats, 0));
				}			
			} else {
				array_push($goodslist, mk_goods(1, "rabatt", "Övrig rabatt", -$row->rabatt*100, $momssats, 0));
			}		
		}
	} else {	
		if ($row->rabatt_moms1 > 0 ) {
			if ($row->fland_id == 358 || $row->fland_id == 999)  {
				if ($row->lang == "fi") {
					array_push($goodslist, mk_goods(1, "rabatt", "Alennus (Alv 23%)", -$row->rabatt_moms1*100, $momssats, 0));
				} else {			
					array_push($goodslist, mk_goods(1, "rabatt", "Rabatt (23% moms)", -$row->rabatt_moms1*100, $momssats, 0));
				}			
			} else {
				array_push($goodslist, mk_goods(1, "rabatt", "Rabatt (25% moms)", -$row->rabatt_moms1*100, $momssats, 0));
			}		
		}
		
		if ($row->rabatt_moms3 > 0 ) {
			if ($row->fland_id == 358 || $row->fland_id == 999)  {
				if ($row->lang == "fi") {			
					array_push($goodslist, mk_goods(1, "rabatt", "Alennus (Alv 8%)", -$row->rabatt_moms3*100, 8, 0));	
				} else {			
					array_push($goodslist, mk_goods(1, "rabatt", "Rabatt (8% moms)", -$row->rabatt_moms3*100, 8, 0));	
				}			
			} else {
				array_push($goodslist, mk_goods(1, "rabatt", "Rabatt (6% moms)", -$row->rabatt_moms3*100, 6, 0));	
			}		
		}
		if ($row->rabatt_moms0 > 0 ) {
			if ($row->fland_id == 358 || $row->fland_id == 999)  {
				if ($row->lang == "fi") {			
					array_push($goodslist, mk_goods(1, "rabatt", "Alennus (Alv 0%)", -$row->rabatt_moms0*100, 0, 0));	
				} else {								
					array_push($goodslist, mk_goods(1, "rabatt", "Rabatt (0% moms)", -$row->rabatt_moms0*100, 0, 0));	
				}			
			} else {				
				array_push($goodslist, mk_goods(1, "rabatt", "Rabatt (0% moms)", -$row->rabatt_moms0*100, 0, 0));	
			}		
		}		
	}

	//if ($row->öresutjämning > 0)
	//	array_push($goodslist, mk_goods(1, "", "Öresavrundning", $row->öresutjämning*100, 0, 0));	
		
	//	
	
	//Lägg till presentkortsavdrag. 
	
	//echo "här: " . $row->presentkortAvdrag ;
	
	if (is_numeric($row->presentkortAvdrag)) {
		if ($row->presentkortAvdrag > 0) {
			if ($row->fland_id == 358 || $row->fland_id == 999)  {
				if ($row->lang == "fi") {
					array_push($goodslist, mk_goods(1, "presentkort", "Lahjakorttihyvitys", -$row->presentkortAvdrag*100, 0, 0)); 
				} else {		
					array_push($goodslist, mk_goods(1, "presentkort", "Avgår presentkort", -$row->presentkortAvdrag*100, 0, 0)); 
				}			
			} else {
				array_push($goodslist, mk_goods(1, "presentkort", "Avgår presentkort", -$row->presentkortAvdrag*100, 0, 0)); 
			}
		
		}		
	}
	
	print_r($goodslist);
	$kommentar = "";
	if ($row->kommentar != "")
		$kommentar = "==== Initial notering\n" . $row->kommentar;
	if ($row->salary == null) 
		$salary = 0;
	else 
		$salary = $row->salary;
	

	print_r($fadr);
	//print_r($ladr);
	//if ($flags != $KRED_TEST_MODE) 
		$kred_ref = $row->kreditor_reference;
		
	// om kreditreferens saknas och det sker manuellt aktivera denna rad: 
	//$kred_ref = 0;
	//else 
		//$kred_ref = "11111";
		
	//echo "<p>ref: " . $kred_ref;
	//echo "<p>här: " . $row->orgnr;
	//echo "<p>plcass: " . $row->pclass;
	//echo "<p>kommentar: " . $kommentar;

	
	//$status = activate_reservation($rno, $pno, $goodslist, $reference, 
	//				$reference_code, $orderid1, $orderid2, $laddr, 
	//				$faddr, $shipmenttype, $email, 
	//				$phone, $cell, $clientip, $flags,  
	//				$currency, $country, $language,
	//				$eid, $secret, $pnoencoding, $pclass, $ysalary,
	//                                $result);
	//	
	if ($row->ipaddr != "")
		$ip = $row->ipaddr;
	else 
		$ip = "0.0.0.0";
	
//	exit;
	$status = activate_reservation($kred_ref, trim($row->orgnr), $goodslist, "", 
					$kommentar, $row->ordernr, $row->fakturanr, $ladr, 
					$fadr, 1, $row->email, 
					$row->telefon, $row->mobilnr, $ip, $flags,  
					$currency, $country, $language,
					$eid, $secret, $pno_encoding, $row->pclass, $salary,
	                                $result);
	
	                         
	switch ($status) {
	 case 0:
	 	if ($flags != $KRED_TEST_MODE) {
		 	$s = "UPDATE Ordertabell set levklar = 9 WHERE ordernr = " . $row->ordernr;
		 	
		 	if (mssql_query($s, $conn_standard)) {
		 		echo "Uppdaterade ordertabell till debiterad<p>";
		 	} else {
		 		$messAlt .= "order " . $row->ordernr . ". Kunde EJ uppdatera ordertabell. InsertQuery: " . $s;
		 		echo "Obs! Kunde EJ uppdatera ordertabell. Processen avbryts<p>";
		 		echo $s;		 		
		 	}
		 	$s = "UPDATE kreditor_reference set invoiceNo = " . $result[1] . ", status = -1 WHERE ordernr = " . $row->ordernr;
		 	
		 	if (mssql_query($s, $conn_standard)) {
		 		
		 		echo "Uppdaterade kreditor_reference med fakturanummer och status<p>";
		 	} else  {
		 		echo "Obs! Kunde EJ uppdatera kreditor_reference. Processen avbryts<p>";	 	
		 		$messAlt .= "order " . $row->ordernr . ". Kunde EJ uppdatera kreditorreference. InsertQuery: " . $s;
		 		echo $s;		 		
		 	}
	 	}
	    echo "<p>Status and ocr</p>";
	      foreach($result as $line){
		echo $line . "<br/>\n";	
	      }
	      echo "<p><a href=\"javascript:back();\">Back</a></p>";      
	      break;
	 case -99:
	 	  $mess .= "" . $row->ordernr . ". Felmeddelande: " . $result . "\n";
	      echo "<p>Internal error: <pre>" . $result . "</pre></p>";
	      echo "<p><a href=\"javascript:back();\">Back</a>";

	      break;
	 default:
	 	  $mess .= "" . $row->ordernr . ". Felmeddelande: " . $result . "\n";
	      echo "<p>Error code: <em>" . $status . "</em></p>";
	      echo "<p>Reason: <em>" . $result . "</em></p>";	      
	      echo "<p><a href=\"javascript:back();\">Back</a>";

	}
	sleep(2);
}

if ($mess != "" || $messAlt != "") {
	echo "hej!"; 
	if ($mess != "")
		$mess = "Följande ordrar kunde inte debiteras: \n" . $mess;
	if ($messAlt != "")
		$mess .= "Andra problem som uppkommit, detta kanske du måste meddela till systemadministratören: \n";
		
	mail("nils@cyberphoto.se", "Ej debiterade beställningar kreditor", $mess . "\n" . $messAlt, "From: admin@cyberphoto.se");	
	//mail("ekonomi@cyberphoto.se", "Ej debiterade beställningar kreditor", $mess . "\n" . $messAlt, "From: admin@cyberphoto.se");	
	
}

mail("admin@cyberphoto.se", "Kreditor uppdatering", "Uppdaterade " . $i . "st ordrar idag", "From: admin@cyberphoto.se");

//-----------------
$s = "SELECT Ordertabell.*, kreditor_reference.*, Kund.salary FROM Ordertabell, kreditor_reference, Kund ";
$s .=" WHERE Ordertabell.ordernr = kreditor_reference.ordernr AND Kund.kundnr = Ordertabell.kundnr AND (betalsatt_id = 7 OR betalsatt_id = 13) ";

$s .= " AND skickat is not null and Ordertabell.netto > 0 ";
$s .=" AND levklar = 3";

$s = "SELECT Ordertabell_alla.* FROM Ordertabell_alla WHERE Ordertabell_alla.skickat is not null AND (betalsatt_id = 7 OR betalsatt_id = 13) ";
$s .= " AND ordernr > 317481 and (restOrderMain > 37481 or restorderMain is null) AND netto > 0 and not levklar in(9,13,14,15) ";

//    If (Form_Settings.landId = 46 And Me.levklar <> 0 And (Me.betalsatt_id = 7)) And (Me.ordernr < 317481 And (Me.restOrderMain < 37481 Or IsNull(Me.restOrderMain) Or Me.restOrderMain = "")) Then
$s .= " ORDER BY Ordertabell_alla.ordernr ";

//echo  $s; //exit;
$res = mssql_query ($s, $conn_standard);
if (mssql_num_rows($res) > 0) {
	$ordernrlist = ""; $i = 0;
	while ($row = mssql_fetch_object($res)) {
		if ($i > 0)
			$ordernrlist .= "\n";
		$ordernrlist .= $row->ordernr;
		$i++;
	}
	$mess = "Varning! Följande ordernummer har inte debiterats trots att det borde ha skett: \n\n" . $ordernrlist;
	mail("admin@cyberphoto.se", "Kreditor varning! ", $mess, "From: admin@cyberphoto.se");
	//mail("ekonomi@cyberphoto.se", "Kreditor varning! ", $mess, "From: admin@cyberphoto.se");

}
//echo "här: " . $ordernrlist;

//---------------


function mkGoodslist($ordernr, $orderrow)  {
	global $conn_standard, $KRED_IS_SHIPMENT, $invoiceAll;
	$goodslist = array();
	$freightCost = 0;
/**
 * $goodslist[] = mk_goods($_GET["qty1"], $_GET["artno1"], $_GET["title1"],
			$_GET["price1"], $_GET["vat1"],0);
 * 
 */

	$s = "SELECT Orderposter.ordernr, Orderposter.artnr, Orderposter.antal, Orderposter.rest, ";
	$s .= "Orderposter.bokad, Orderposter.serienr, Tillverkare.tillverkare, Moms.momssats, ";
	$s .= "(CASE Orderposter.beskrivning_alt WHEN is null  Orderposter.beskrivning WHEN '' Orderposter.beskrivning ELSE Orderposter.beskrivning_alt)";
	//Orderposter.beskrivning, Orderposter.utpris
	$s .= "FROM Orderposter, ";
	$s .= "Artiklar, Tillverkare, Moms WHERE Orderposter.artnr = Artiklar.artnr AND Orderposter.momskod = Moms.moms_id AND ";
	$s .="Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND ";
	$s .=" ordernr = " . $ordernr;
	
	$s = "SELECT ";
	$s .= "CASE WHEN (Orderposter.beskrivning_alt is not null AND Orderposter.beskrivning_alt != '') THEN Orderposter.beskrivning_alt ELSE Orderposter.beskrivning END AS beskrivning, ";
	$s .= "CASE WHEN Orderposter.visualPrice > 0 THEN Orderposter.visualPrice ELSE Orderposter.utpris  END as utpris, ";
	$s .= "Orderposter.ordernr, Orderposter.artnr, Orderposter.antal, Orderposter.rest, Orderposter.bokad, Orderposter.serienr, Tillverkare.tillverkare, Moms.momssats ";
	$s .= "FROM Orderposter, Artiklar, Tillverkare, Moms ";
	$s .= "WHERE Orderposter.artnr = Artiklar.artnr AND Orderposter.momskod = Moms.moms_id AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND ";
	$s .= "(Orderposter.isHidden is null OR Orderposter.isHidden != -1) AND ";
	$s .= "ordernr = " . $ordernr;
	
	//echo $s; exit;
	$res = mssql_query($s, $conn_standard);
	if (mssql_num_rows($res) == 0) return;
	while ($row=mssql_fetch_object(($res))) {
		//echo "\n<br>momssats: " . $row->momssats;
		//echo "\n<br>momssats: " . $row->fland_id;
		if ($orderrow->fland_id == 358 || $orderrow->fland_id == 999)  {
			if ($orderrow->fland_id == 999) {
				$momssats = 0;
			} else {
				if ($row->momssats == 0.25) {
					$momssats = 0.23;				
				} else if ($row->momssats == 0.06) {			
					$momssats = 0.08;
				} else {
					$momssats = 0.23;				
				}
			}
		} else {
			$momssats = $row->momssats;
		}
		
		if (eregi("^frakt", $row->artnr)) {
			$freightCost += $row->utpris * $row->antal;
		} else {
			$beskrivning = "";
			//echo "här: " . $row->tillverkare;
			if ($row->tillverkare != ".")
				$beskrivning = $row->tillverkare . " ";
			$beskrivning .= $row->beskrivning;				
			$beskrivning = trim($beskrivning);
			if ($row->serienr != "") 
				$beskrivning .= " (serienr: " . $row->serienr . ")";
			
			$antal = $row->antal;
			if ($antal == "")	
				$antal = 0;
			$rest = $row->rest;
			if ($rest == "")
				$rest = 0;			
			if ($rest > 0) {
				if ($invoiceAll) {
					array_push($goodslist, mk_goods($antal + $rest, $row->artnr, $beskrivning, $row->utpris*100, $momssats*100, 0));
					array_push($goodslist, mk_goods("", "", "Obs! " . $rest . "st av ovanstående är restnoterade", "", "", ""));
					
				} else {
					array_push($goodslist, mk_goods($antal, $row->artnr, $beskrivning, $row->utpris*100, $momssats*100, 0));				
					array_push($goodslist, mk_goods("", "", "" . $rest . "st är restnoterade", "", "", ""));
				}				
			} else {
				array_push($goodslist, mk_goods($antal, $row->artnr, $beskrivning, $row->utpris*100, $momssats*100, 0));				
			}
		}
	}
	// Rahti
	if ($orderrow->fland_id == 358) {
		if ($orderrow->lang == "sv")
			array_push($goodslist, mk_goods_flags(1, "frakt", "Fraktkostnad", $freightCost*100, 23, 0, $KRED_IS_SHIPMENT));
		else
			array_push($goodslist, mk_goods_flags(1, "frakt", "Rahti", $freightCost*100, 23, 0, $KRED_IS_SHIPMENT));
			
	} else if ($orderrow->fland_id == 999)  {
		if ($orderrow->lang == "sv")
			array_push($goodslist, mk_goods_flags(1, "frakt", "Fraktkostnad", $freightCost*100, 0, 0, $KRED_IS_SHIPMENT));
		else
			array_push($goodslist, mk_goods_flags(1, "frakt", "Rahti", $freightCost*100, 0, 0, $KRED_IS_SHIPMENT));
	} else {
		array_push($goodslist, mk_goods_flags(1, "frakt", "Fraktkostnad", $freightCost*100, 25, 0, $KRED_IS_SHIPMENT));
	}
	return $goodslist;
}	
?>
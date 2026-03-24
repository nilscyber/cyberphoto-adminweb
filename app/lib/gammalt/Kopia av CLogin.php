<%
/*

PHP login "object"
author		Nils Kohlström
version		2001-05-16

Inkluderade funktioner:
login(), login funktion
add_customer(), lägger till ny kund i databas
customer_info(), plockar fram info om tidigare kund
viewItemsInBasket(), skriver ut tabellrader med innehållet i kundvagnen
	check_lager(), visar lagerstatus på produkt, tillhör viewItemsInbasket()
	check_package(), kontrollerar lagerstatus på paket (artnr som slutar på pac)
	check_queue(), kontrollerar antal på kö på produkten. 
generate_pass(), genererar slumpmässiga lösenord

*/


// Ta bort gamla uppgifter från ev. tidigare lagd order 
# registrera först de gamla variablerna så att vi kan...
session_register("kundnrladdaom", "ordernrladdaom");
# ... förstöra dom: 
unset ($kundnrladdaom);
unset ($ordernrladdaom);

//Registrera login variabler
session_register("kundnrsave", "confirm", "old_namn", "old_co", "old_adress", "old_postnr", "old_postnr", "old_postadr", "old_land_id", "old_email", "old_telnr", "old_orgnr",
"old_lnamn", "old_lco", "old_ladress", "old_lpostnr", "old_lpostadr", "old_lland_id", "old_ltelnr", "old_lemail", 
"old_levadress", "old_faktadress", "old_land", "old_lland", "old_faktura", "order_erref", "order_erordernr", "order_kommentar", 
"paketref", "betalsatt", "spara_uppgifter", "old_faktlev");


function login($kundnr, $passwd) {

	/*
	Gives variable "confirm" five possible
	values. Input needed from variable "kundr" and 
	variable "passwd". 
	1 = login ok
	2 = login incorrect
	3 = too many trials (maximum of three)
	4 = password missing
	5 = password doesn't exist
	also, if $confirm = '1' then $kundnrsave gets the value of $kundnr
	*/

	global $confirm, $salt, $kundnrsave;
	if ($passwd == ""):
		$confirm = '4';
	else:
	
	include ("CConnect.php");
	$select = "SELECT kundid, trials from Kund ";
	$select .= "WHERE kundnr=$kundnr ";
	$res = mysqli_query($select);
	if ($res)
		extract(mysqli_fetch_array($res));

	if ($kundid == "" || $kundid == "nkN9RbBQ19sUs") {
		$confirm = 5;
	
	}		
	else {	
		
		// kontrollera så att kunden inte har missat sitt löseord för många gånger.
		if ($trials <= 5)  {

		// kontrollera om lösenordet är rätt

			if ($passwd == $kundid ) {
				if ($trials > '0') {
					$update = "update Kund set trials=0";
					/* TODO: mysql_db_query replaced - needs manual review (was selecting db + querying) */ mysqli_query("cyberphoto", "$update");
					}
				$confirm='1';
				$kundnrsave = $kundnr; 
				}

			else  {
				$newtrials = $trials+1;
				$update = "update Kund set trials=$newtrials";
				/* TODO: mysql_db_query replaced - needs manual review (was selecting db + querying) */ mysqli_query("cyberphoto", "$update");
				$confirm='2';
				}

		}
		else {
			$confirm='3';
			}
		}
	
	endif;
	
	
}

function add_customer($uppdatera) {
	global $new_namn, $new_co, $new_adress, $new_postnr, $new_postadr, $new_land, $new_telnr, 
	$new_orgnr, $new_email, $new_erref, $new_erordernr, $new_kommentar, $new_lco, $new_ladress, 
	$new_lpostadr, $new_lpostnr, $new_lland, $kundnrsave, $confirm, $new_passw, $newcustomerset, 
	$kundnr, $newcust, $uppdatera, $kundnrsave, $order_erref, $order_erordernr, $order_kommentar, 
	$wrongpassword, $change_passw, $new_faktlev, $new_erordernr, $new_erref, $new_kommentar;
	
	include_once ("CConnect.php");

	$select = "SELECT land_id FROM Land WHERE land = '$new_land'";
	$row = mysqli_fetch_object(mysqli_query($select));
	$land_id = $row->land_id;
	
	$select = "SELECT land_id FROM Land WHERE land = '$new_lland'";
	$row = mysqli_fetch_object(mysqli_query($select));
	$lland_id = $row->land_id;
	
	// Om leveransadress inte angivits, stoppa in fakturaadressen
	if ($new_lco == "" || $new_faktlev == '1') { $new_lco = $new_co; }
	if ($new_ladress == "" || $new_faktlev == '1') { $new_ladress = $new_adress; }
	if ($new_lpostadr == "" || $new_faktlev == '1') { $new_lpostadr = $new_postadr; }
	if ($new_lpostnr == "" || $new_faktlev == '1') { $new_lpostnr = $new_postnr; }
	if ( (($new_lco == "") && (new_ladress == "")) || ($new_faktlev == '1') ) { $lland_id = $land_id; }
	
	// Om ny kund
	if ($uppdatera != 'yes') {
	
		$select = "SELECT max(kundnr) as kundnr FROM Kund ";
		$row = (mysqli_fetch_object(mysqli_query("$select")));
		$newkundnr = "$row->kundnr";
		$newkundnr++;

		$insert  = "INSERT INTO Kund (kundnr, namn, co, adress, postnr, postadr, land_id, email, telnr, orgnr, erref, erordernr, ";
		$insert .= "lnamn, lco, ladress, lpostadr, lpostnr, lland_id, kundid, lemail, ltelnr, faktlev, savelogin) values ('$newkundnr', '$new_namn', '$new_co', '$new_adress', ";
		$insert .= "'$new_postnr', '$new_postadr', '$land_id', '$new_email', '$new_telnr', '$new_orgnr', '$new_erref', '$new_erordernr', ";
		$insert .= "'$new_namn', '$new_lco', '$new_ladress', '$new_lpostadr', '$new_lpostnr', '$lland_id', '$new_passw', '$new_email', '$new_telnr', '$new_faktlev', '$spara')";
		
		$res = mysqli_query($insert);
				
		if ($res) {
			$kundnrsave = $newkundnr;
			$confirm = '1';		

			$order_erordernr = $new_erordernr;
			$order_erref = $new_erref;
			$order_kommentar = $new_kommentar;

		}
	}
	
	// Om uppdatera gamla uppgifter
	else {	
	
		// Testa det angivna lösenordet
		login($kundnrsave, $new_passw);
	
		if ($confirm == '1') {
			if ($change_passw != "")
			$passw = $change_passw;
			else
			$passw = $new_passw;
		
		$update  = "UPDATE Kund set namn = '$new_namn', co = '$new_co', adress = '$new_adress', postnr = '$new_postnr', ";
		$update .= "postadr = '$new_postadr', land_id = '$land_id', email = '$new_email', telnr = '$new_telnr', ";
		$update .= "orgnr = '$new_orgnr', faktlev = '$new_faktlev', ";
		$update .= "lnamn = '$new_namn', lco = '$new_lco', ladress = '$new_ladress', lpostadr = '$new_lpostadr', ";
		$update .= "lpostnr = '$new_lpostnr', lland_id = '$lland_id', kundid = '$new_passw', lemail = '$new_email', ";
		$update .= "ltelnr = '$new_telnr', kundid = '$passw' WHERE kundnr = '$kundnrsave'";
		$res = mysqli_query($update);
		
		$order_erordernr = $new_erordernr;
		$order_erref = $new_erref;
		$order_kommentar = $new_kommentar;

		}
		else {
		
		$confirm = '1';
		$wrongpassword = 'yes';
		}
	
	}
	
}

function customer_info() {

	/*
	Input:	$kundnrsave, $confirm, requires user to be loged in (i.e. $confirm == '1')
	Output:	Customer information in variables below. 	 
	*/
	global $kundnrsave, $confirm, $old_namn, $old_co, $old_adress, $old_postnr, $old_postnr, $old_postadr, $old_land_id, $old_email, $old_telnr,
	$old_orgnr, $old_lnamn, $old_lco, $old_ladress, $old_lpostnr, $old_lpostadr, $old_lland_id, $old_ltelnr, $old_lemail,
	$old_levadress, $old_faktadress, $old_land, $old_lland, $old_faktura, $old_erref, $old_erordernr, $old_faktlev;
	include_once ("CConnect.php");
	
	if ($confirm == '1') {

		// Plocka först fram kunduppgifterna
				
		$select  = "SELECT namn, co, adress, postnr, postadr, Kund.land_id, email, telnr, orgnr, mail_send, faktura, ";
		$select .= "lnamn, lco, ladress, lpostnr, lpostadr, lland_id, lemail, ltelnr, land, erordernr, erref, faktlev, ";
		$select .= "savelogin ";
		$select .= "FROM Kund LEFT JOIN Land ON Kund.land_id = Land.land_id ";
		$select .= "WHERE kundnr = '$kundnrsave'";
		
		$res = mysqli_query($select);
		$row = mysqli_fetch_array($res);
			
		// returns selected variabls prefixed $old_, e.g. $old_namn
		if ((mysqli_num_rows($res)) > '0')
		extract($row, EXTR_PREFIX_ALL, "old");
		
		// Vet inte riktigt hur man skriver en helt effektiv fråga, därför, detta extra
		$select2 = "SELECT land from Land WHERE land_id = '$old_lland_id'";
		$res2 = /* TODO: mysql_db_query replaced - needs manual review (was selecting db + querying) */ mysqli_query("cyberphoto", "$select2");
		$row2 = mysqli_fetch_object($res2);
		$old_lland = $row2->land;
		
		// Gör snabb variabel för adresser
		$old_faktadress  = "$old_namn<br>";
		$old_faktadress .= "$old_co<br>";
		$old_faktadress .= "$old_adress<br>";
		$old_faktadress .= "$old_postnr $old_postadr<br>";
		$old_faktadress .= "$old_land";

		$old_levadress  = "$old_lnamn<br>";
		$old_levadress .= "$old_lco<br>";
		$old_levadress .= "$old_ladress<br>";
		$old_levadress .= "$old_lpostnr $old_lpostadr<br>";
		$old_levadress .= "$old_lland";

	}
} # end customer_info

function viewItemsInBasket($firstbasket) {
	global $kundvagn, $goodsvalue, $artnr, $count, $lagersaldo, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans;
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

		$select  = "SELECT artnr, beskrivning, kommentar, utpris, tillverkare, frakt, lagersaldo, bestallt, ";
		$select .= "lev_datum, bestallningsgrans, lev_datum_normal, frakt FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
		$select .= "WHERE artnr='$arg'";
		
		# Alla värden försvinner inte, så därför måste vi göra enligt nedan
		$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = "";

		$row = mysqli_fetch_array(mysqli_query($select));
		extract($row);
		
		// Lägg på extra frakt om det behövs
		if ($frakt) 
			$extra_freight = $frakt; 
		
		$goodscounter += '1';
		$goodsvalue += ($utpris*$count);
		
		if ($tillverkare != '.')
			$description = $tillverkare . " ";
		
		$description .= $beskrivning . " " . $kommentar;
// visa bara info om det inte är kostnadsfri frakt
if (!(eregi("fraktbutik", $artnr)) && $firstbasket != 'nooutput'):

%>

		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% echo $description; %></font></td>
		  <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><% echo $count; %></font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<%		printf("%10d", $utpris);  %>

		</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<%		printf("%10d", $utpris*1.25); %>
		</font></td>
		<td bgcolor="#ECECE6" align="left"><font size="1" face="Verdana, Arial">

<%		if (!(eregi("^frakt", $artnr))): %>
<%		check_lager(); %>
<%		else: %>
		&nbsp;&nbsp;
<% 		endif; %>

		</font></td>
		<td bgcolor="#ECECE6">
<%		if (!(eregi("^frakt", $artnr))): %>

		<A HREF="javascript:modifyItemsInBasket('<% echo $artnr; %>', '1')">
		<font face="Verdana, Arial" size="1"><img src="../pic/besantal.gif" width=62 height=8 border=0 alt="tryck för att ändra antal"></font></A>
		<% else: %>
		&nbsp;&nbsp;
		<% endif; %>
		</td>
		</tr>
		
<%  endif; %>
<%	}

	
	if ($extra_freight && $firstbasket == 'yes' && $firstbasket != 'nooutput'): 
	
	    $select = "select beskrivning, kommentar, utpris from Artiklar where artnr='frakt+'";
	
	    $res = /* TODO: mysql_db_query replaced - needs manual review (was selecting db + querying) */ mysqli_query("cyberphoto", "$select");
	    $row = mysqli_fetch_object($res);
	
	    $name = $row->beskrivning;
	    $comment = $row->kommentar;
	    $outprice = $row->utpris;
	
	    # Set variables
	    $artnr = "frakt+";
	    $manufacturer = "";
	    $goodsvalue += $outprice;
%>	    
		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% echo $name; %></font></td>
		  <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">1</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<%		printf("%10d", $outprice);  %>
		</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<%		printf("%10d", $outprice*1.25); %>
		</font></td>
		<td bgcolor="#ECECE6" align="left"><font size="1" face="Verdana, Arial">&nbsp;&nbsp;
		</font></td>
		<td bgcolor="#ECECE6">&nbsp;&nbsp;</td>
		</tr>
	<% endif; 

	
}

function check_lager() {
	global $artnr, $count, $lagersaldo, $bestallt, $lev_datum, $lev_datum_normal, $package_stock, $bestallningsgrans, $queue, $est_delivery;

	$package_stock = NULL;  # clear package check
	# Make a check if freigt is already selected. 
	if (ereg("^frakt", $artnr))
		{ $freight_check = "1"; }

	if (ereg("pac$", $artnr)) # kollar tillgången om det är ett paket
		{   check_package();  }
	if (!(ereg("frakt",$artnr))) {

		if ($lagersaldo >= $count || ($package_stock == '1') )  { 
			print "finns i lager"; 
		}
		else {

			# Kolla hur många det finns på kö
			# antal på köp visas i $queue, nollställes först. 
			$queue = NULL;
			check_queue();
			$neededStock = $queue + $count;
			if ($bestallt >= $neededStock) {
				if ($lev_datum == '-' || ($lev_datum == ""))
					{ print "ej fastställt"; }
				else
					{ print "beräknas in $lev_datum"; }
			}

			else  { 

				if (ereg("pac$",$artnr))
					{  print "produkten består av ett paket med flera artiklar där en eller flera av artiklarna är tillfälligt slut, <a href=\"/e-mail.htm\">kontakta oss för mer info</a>"; }        
				elseif ($bestallningsgrans == '0') 
					{  print "beställningsvara, normal leveranstid $lev_datum_normal"; }
				else
					{ print "tillfälligt slut, normal leveranstid $lev_datum_normal" ; }
				}
		}         
	}
}

function check_package() {

	global $artnr, $package_stock, $count;

	$select = "SELECT lagersaldo, antal ";
	$select .= "FROM Artiklar, Paketpriser WHERE Artiklar.artnr=Paketpriser.artnr_del ";
	$select .= "AND Paketpriser.artnr_paket = '$artnr' ";

	$res = mysqli_query($select);
	
	unset ($check);

	while ($row = mysqli_fetch_array($res))
	{
	extract($row);

	$check = $antal*$count;



	  if ($check > $lagersaldo) {
	    $package_stock = "";
	    break;
	  }
	  else {
	    $package_stock = "1";
	  }
	}

}


function check_queue() {
	global $artnr, $queue;
	
	$select = "SELECT antal FROM Orderposter WHERE bokad = '0' && artnr = '$artnr'";
	$res = mysqli_query($select);
	while ($row = mysqli_fetch_array($res) )   {
	extract ($row);
		$queue += $antal;

	}
}

function generate_pass ($pass_len)  { 
	/*
	Input: $pass_len, Längd på lösenord
	Output: $nps, lösenord bestående av stora och små bokstäver och siffror
	
	*/
$nps = ""; 

// Seed the random number generator
mt_srand ((double) microtime() * 1000000); 

while (strlen($nps)<$pass_len) { 
	
	// Ge $c ett värde från slumpmässigt valt ASCII värde
	$c = chr(mt_rand (0,255)); 
	
	// Lägg till på $nps om det är i rätt format
	if (eregi("^[a-z0-9]$", $c)) {
		$nps = $nps.$c;
		$nps = strtolower($nps);
	}
	
}
 	return ($nps); 
}
%>
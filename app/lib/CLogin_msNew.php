<%

/*

PHP login "object"
author		Nils Kohlström
version		2001-05-16
revised continually

Inkluderade funktioner:
	login(), login funktion
	add_customer(), lägger till ny kund i databas
	customer_info(), plockar fram info om tidigare kund
	function maila_kundinfo(), mailar kundinfo
	
*/

// Ta bort gamla uppgifter från ev. tidigare lagd order 
# registrera först de gamla variablerna så att vi kan...
session_register("kundnrladdaom", "ordernrladdaom");
# ... förstöra dom: 
unset ($kundnrladdaom);
unset ($ordernrladdaom);


session_register("kundnrsave", "confirm", "old_namn", "old_co", "old_adress", "old_postnr", "old_postnr", "old_postadr", "old_land_id", "old_email", "old_telnr", "old_orgnr",
"old_lnamn", "old_lco", "old_ladress", "old_lpostnr", "old_lpostadr", "old_lland_id", "old_ltelnr", "old_lemail", 
"old_levadress", "old_faktadress", "old_land", "old_land_fi", "old_lland", "old_lland_fi", "old_faktura", "order_erref", "order_erordernr", "order_kommentar", 
"paketref", "betalsatt", "spara_uppgifter", "old_faktlev", "intern", "old_foretag", "old_userName", "SesCc", "SesExpM", "SesExpY", "SesCcCode", "kortfax", 
"old_nyhetsbrev", "CcName", "old_avtalskund", "old_mobilnr", "old_sms", "old_nyhetsbrev", "old_forsakringserbjudande");

function login($kundnrtest, $passwd) {

	include ("CConnect_ms.php");
	
	/*
	Gives variable "confirm" five possible
	values. Input needed from variable "kundr" and 
	variable "passwd". 
	1 = login ok
	2 = login incorrect
	3 = too many trials (maximum of 5)
	4 = passwordfield empty
	5 = password not set, standard is used
	6 = customer missing
	also, if $confirm = '1' then $kundnrsave gets the value of $kundnr
	*/

	global $confirm, $salt, $kundnrsave;
	$passwd = trim($passwd);
	$kundnrtest = trim($kundnrtest);
	if ($passwd == "") {
		$confirm = '4';
	}
	else {
		
		$select = "SELECT kundnr, kundid, userName, trials from Kund ";
		$select .= "WHERE kundnr like '$kundnrtest' AND borttagen = 0";
		$res = mssql_query($select);
		if (@mssql_num_rows ($res) > 0) { // @ så att det inte syns om det blir fel. 
		
			extract(mssql_fetch_array($res));

			if ($kundid == "" || $kundid == "nkN9RbBQ19sUs") {
				$confirm = 5;
			}		
			else {

				// kontrollera så att kunden inte har missat sitt löseord för många gånger.
				if ($trials <= 5)  {

				// kontrollera om lösenordet är rätt

					if ($passwd == $kundid ) {
						if ($trials > '0') {
							$update = "update Kund set trials=0 where kundnr=$kundnr";
							mssql_query($update);
							}
						$confirm='1';
						$kundnrsave = $kundnr; 
					}

					else  {
						$newtrials = $trials+1;
						$update = "update Kund set trials=$newtrials where kundnr=$kundnr";
						mssql_query($update);
						$confirm='2';
						}
				}
				else {
					$confirm='3';
					}
			}
		}
		else {
		
			$select = "SELECT kundnr, kundid, userName, trials FROM Kund ";
			$select .= "WHERE userName = '$kundnrtest' AND borttagen = 0";

			$res = mssql_query($select);
			if (mssql_num_rows ($res) > 0) {

				extract(mssql_fetch_array($res));

				if ($kundid == "" || $kundid == "nkN9RbBQ19sUs") {
					$confirm = 5;

				}		
				else {

					// kontrollera så att kunden inte har missat sitt löseord för många gånger.
					if ($trials <= 5)  {

					// kontrollera om lösenordet är rätt

						if ($passwd == $kundid ) {
							if ($trials > '0') {
								$update = "update Kund set trials=0 where kundnr=$kundnr";
								mssql_query($update);
								}
							$confirm='1';
							$kundnrsave = $kundnr; 
							}

						else  {
							$newtrials = $trials+1;
							$update = "update Kund set trials=$newtrials where kundnr=$kundnr";
							mssql_query($update);
							$confirm='2';
							}

					}
					else {
						$confirm='3';
						}
				}
			}
			else {

				$confirm = 6;
			}
		}
	}
	
}

function customer_info() {

	include ("CConnect_ms.php");
	
	/*
	Input:	$kundnrsave, $confirm, requires user to be loged in (i.e. $confirm == '1')
	Output:	Customer information in variables below. 	 
	*/
	global $kundnrsave, $confirm, $old_namn, $old_co, $old_adress, $old_postnr, $old_postnr, $old_postadr, $old_land_id, $old_email, $old_telnr,
	$old_orgnr, $old_lnamn, $old_lco, $old_ladress, $old_lpostnr, $old_lpostadr, $old_lland_id, $old_ltelnr, $old_lemail,
	$old_levadress, $old_faktadress, $old_land, $old_lland, $old_faktura, $old_erref, $old_erordernr, $old_faktlev, $old_land_fi, $old_lland_fi, 
	$old_foretag, $old_userName, $old_avtalskund, $old_mobilnr, $old_sms, $old_nyhetsbrev, $old_forsakringserbjudande;

	
	if ($confirm == '1') {

		// Plocka först fram kunduppgifterna
				
		$select  = "SELECT namn, co, adress, postnr, postadr, Kund.land_id, email, telnr, orgnr, mail_send, faktura, ";
		$select .= "lnamn, lco, ladress, lpostnr, lpostadr, lland_id, lemail, ltelnr, land, erordernr, erref, faktlev, ";
		$select .= "savelogin, land_fi, foretag, userName, avtalskund, mobilnr, sms, nyhetsbrev, forsakringserbjudande ";
		$select .= "FROM Kund LEFT JOIN Land ON Kund.land_id = Land.land_id ";
		$select .= "WHERE kundnr = '$kundnrsave'";
		
		$res = mssql_query($select);
		$row = mssql_fetch_array($res);
			
		// returns selected variabls prefixed $old_, e.g. $old_namn
		if ((mssql_num_rows($res)) > 0)
		extract($row, EXTR_PREFIX_ALL, "old");
		
		// För gamla kunder som saknar leveransadress på sina ordrar. 	
		if (($kundrsave < 105138) AND ($old_lnamn == "" AND $old_lco == "" AND $old_ladress == "" AND $old_lpostnr == "" AND $old_lpostadr == "") )

		{
			check_lev_adress($kundnrsave);

		}	
		
		// Vet inte riktigt hur man skriver en helt effektiv fråga, därför, detta extra
		$select2 = "SELECT land, land_fi from Land WHERE land_id = '$old_lland_id'";
		$res2 = mssql_query($select2);
		$row2 = mssql_fetch_object($res2);
		$old_lland = $row2->land;
		$old_lland_fi = $row2->land_fi;
		

	}
} # end customer_info



function add_customer($uppdatera, $mailpassw) {

	include ("CConnect_ms.php");
	
	global $new_namn, $new_co, $new_adress, $new_postnr, $new_postadr, $new_land, $new_telnr, 
	$new_orgnr, $new_email, $new_erref, $new_erordernr, $new_kommentar, $new_lco, $new_ladress, 
	$new_lpostadr, $new_lpostnr, $new_lland, $kundnrsave, $confirm, $new_passw, $newcustomerset, 
	$kundnr, $newcust, $uppdatera, $kundnrsave, $order_erref, $order_erordernr, $order_kommentar, 
	$wrongpassword, $change_passw, $new_faktlev, $new_erordernr, $new_erref, $new_kommentar, $new_lnamn, 
	$userName, $userTaken, $foretag, $new_userName, $new_nyhetsbrev, $new_mobilnr, $new_sms;
	
	$userTaken = "";
	
	// tag bort mellanslag före och efter på lösenord och användarnamn
	$userName = trim($userName);
	$new_userName = trim($new_userName);
	$new_passw = trim($new_passw);
	$change_passw = trim($change_passw);
	$new_namn = trim($new_namn);
	$new_lnamn = trim($new_lnamn);
	$new_mobilnr = trim($new_mobilnr);
	
	$select = "SELECT land_id FROM Land WHERE land = '$new_land'";
	$row = mssql_fetch_object (mssql_query ($select));
	$land_id = $row->land_id;
	
	$select = "SELECT land_id FROM Land WHERE land = '$new_lland'";
	$row = mssql_fetch_object (mssql_query ($select));
	$lland_id = $row->land_id;
	
	// Om leveransadress inte angivits, stoppa in fakturaadressen
	if (($new_lco == "" && $new_lnamn == "") || $new_faktlev == 1 || $foretag == 'no') {
		$new_lnamn = $new_namn;
		$new_lco = $new_co;
		$new_ladress = $new_adress;
		$new_lpostnr = $new_postnr;
		$new_lpostadr = $new_postadr;
		$lland_id = $land_id;
	}
	/**
	if ($new_lco == "" || $new_faktlev == '1') { $new_lco = $new_co; }
	if ($new_ladress == "" || $new_faktlev == '1') { $new_ladress = $new_adress; }
	if ($new_lpostadr == "" || $new_faktlev == '1') { $new_lpostadr = $new_postadr; }
	if ($new_lpostnr == "" || $new_faktlev == '1') { $new_lpostnr = $new_postnr; }
	//if ( (($new_lco == "") && (new_ladress == "")) || ($new_faktlev == '1') ) { $lland_id = $land_id; }
	if ( (($new_lco == "") && (new_ladress == "")) || ($new_faktlev == '1') ) { $lland_id = $land_id; }
	if ( $new_lnamn == "" ) { $new_lnamn = $new_namn; }
	*/
	
	// Om ny kund
	if ($uppdatera != 'yes') {
		
		if (testUsername ($userName)) {
			$userTaken = true;
		}
		else {

			if ($new_lnamn != "")
				$levnamnet = $new_lnamn;
			else
				$levnamnet = $new_namn;
			if ($foretag == "yes") 
				$foretaget = -1;
			else
				$foretaget = 0;
			
			if ($new_nyhetsbrev)
				$new_nyhetsbrev = -1;
			else
				$new_nyhetsbrev = 0;

			if ($new_sms)
				$sms = -1;
			else
				$sms = 0;
			
			$select = "SELECT max(kundnr) as kundnr FROM Kund ";
			$row = (mssql_fetch_object(mssql_query("$select")));
			$newkundnr = "$row->kundnr";
			$newkundnr++;
			$datumet = date("Y-m-d H:i:s");


			$insert  = "INSERT INTO Kund (kundnr, namn, co, adress, postnr, postadr, land_id, email, telnr, orgnr, erref, erordernr, ";
			$insert .= "lnamn, lco, ladress, lpostadr, lpostnr, lland_id, kundid, lemail, ltelnr, faktlev, savelogin, userName, foretag, skapad, nyhetsbrev, mobilnr, sms) values ";
			$insert .= " ('$newkundnr', '$new_namn', '$new_co', '$new_adress', ";
			$insert .= "'$new_postnr', '$new_postadr', '$land_id', '$new_email', '$new_telnr', '$new_orgnr', '$new_erref', '$new_erordernr', ";
			$insert .= "'$new_lnamn', '$new_lco', '$new_ladress', '$new_lpostadr', '$new_lpostnr', '$lland_id', '$new_passw', '$new_email', ";
			$insert .= "'$new_telnr', '$new_faktlev', '$spara', '$userName', '$foretaget', '$datumet', $new_nyhetsbrev, '$new_mobilnr', $sms)";
			
			$res = mssql_query ($insert);

			if ($res) {
				$kundnrsave = $newkundnr;
				$confirm = '1';		

				$order_erordernr = $new_erordernr;
				$order_erref = $new_erref;
				$order_kommentar = $new_kommentar;
				maila_kundinfo($newkundnr, $new_passw, $mailpassw);
			}
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

		if ($new_nyhetsbrev)
			$new_nyhetsbrev =-1;
		else
			$new_nyhetsbrev = 0;
		if ($new_sms)
			$sms = -1;
		else
			$sms = 0;
		
		$update  = "UPDATE Kund set namn = '$new_namn', co = '$new_co', adress = '$new_adress', postnr = '$new_postnr', ";
		$update .= "postadr = '$new_postadr', land_id = '$land_id', email = '$new_email', telnr = '$new_telnr', ";
		$update .= "orgnr = '$new_orgnr', faktlev = '$new_faktlev', ";
		$update .= "lnamn = '$new_lnamn', lco = '$new_lco', ladress = '$new_ladress', lpostadr = '$new_lpostadr', ";
		$update .= "lpostnr = '$new_lpostnr', lland_id = '$lland_id', lemail = '$new_email', ";
		$update .= "ltelnr = '$new_telnr', ";
		$update .= "kundid = '$passw', ";
		$update .= "userName = '$new_userName', ";
		$update .= "nyhetsbrev = $new_nyhetsbrev, ";
		$update .= "mobilnr = '$new_mobilnr', ";
		$update .= "sms = $sms ";
		$update .= "WHERE kundnr = '$kundnrsave'";
		//echo $update;
		$res = mssql_query ($update);
		
		// maila ut den nya kundinfon
		
		maila_kundinfo($newkundnr, $passw, $mailpassw);
		
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

function maila_kundinfo($kundnr, $passw, $mailpassw) {
	
	include ("CConnect_ms.php");
	

	$select = "SELECT * from Kund WHERE kundnr = '$kundnr'";
	$res = mssql_query ($select);
	$row = mssql_fetch_object ($res);
if ($row->email != ''): 	

$selland1 = "SELECT land FROM Land where land_id = '$row->land_id'";
$res1 = mssql_query ($selland1);
$row1 = mssql_fetch_object ($res1);
$land = $row1->land;

$selland2 = "SELECT land FROM Land where land_id = '$row->land_id'";
$res2 = mssql_query ($selland2);
$row2 = mssql_fetch_object ($res2);
$lland = $row2->land;

$message = <<<eof
Hej!

Här kommer era kunduppgifter

Kundnummer: $kundnr
Användarnamn: $row->userName
Lösenord:   
eof;
if ($mailpassw == 'yes' && $row->kundid != '')
	{ $message .= $row->kundid . "\n"; }
elseif ($row->kundid == '')
	{ $message .= "ej angett\n"; }
elseif ($mailpassw == 'no')
	{ $message .= "visas ej enl. önskemål\n"; }
	
$message .= <<<eof

Adress:
$row->namn
$row->co
$row->adress
$row->postnr $row->postadr
$land

eof;
if ($row->foretag == -1) {
$message .= <<<eof

Leveransuppgifter:
$row->lnamn
$row->lco
$row->ladress
$row->lpostnr $row->lpostadr
$lland

Övriga uppgifter:
$row->email
$row->telnr

eof;
}

$subject = "Kundinfo från CyberPhoto";
$extra = "From: order@cyberphoto.se\n";

mail($row->email, $subject, $message, $extra);
endif; 
}

//-------------------------------------------------------------------------------
/*
function kollaLevtid ($artnr, $count, $ordernr, $fi)  { // artnr som skall kollas, antal samt inkommet datum på ordern
	
	include ("CConnect_ms.php");
	
	global $fi;

	// kolla först inkommet för att få fram köplats. 
	// ange dagens datum om koll för kundvagnen 
	
	if ($ordernr == NULL) {
		$inkommetOur = date("Y-m-d H:i:s");
	}
	else {
		$select = "SELECT inkommet from Ordertabell WHERE ordernr = '$ordernr' ";

		$res = mssql_query ($select);

		if ($res)  {
			$row = mssql_fetch_object ($res);
			$inkommetOur = $row->inkommet;
		}
	}
	
	
	
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
	
	if (mssql_num_rows ($res) > '0') {
		
		while ($row = mssql_fetch_array ($res)):
			extract ($row);
			if ($inkommet < $inkommetOur) {
				
				$totalNeed += $antal; // antalet som står före.
				//echo "$totalNeed, $ordernr";
			}
			else
				break;
				
		endwhile;
		
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
			$antal_sum += ($antal - $levererat);
			
			if ($antal_sum >= $totalNeed) {
				$articleinfo = $levdatum;
				break;
			}
		//echo "$antal_sum $totalNeed.$articleinfo<br>";
		$antal = $levererat = 0;
		endwhile;	
	
	}
	else
	
	{
	
		if ($fi == 'yes')
			$articleinfo = "toimituspäivämäärä ei ole määritelty";
		else
			$articleinfo = "leveransdatum okänt";
	
	}

if ($articleinfo == "" || $articleinfo == "-") {
	if ($fi == 'yes')
		$articleinfo = "toimituspäivämäärä ei ole määritelty";
	else
		$articleinfo = "leveransdatum okänt";
}
return $articleinfo;
}
*/
function check_lev_adress($kundnrsave) {

global $old_lnamn, $old_lco, $old_lpostnr, $old_lpostadr, $old_lland_id, $old_ladress, $old_email, $old_telnr, 
$old_namn, $old_co, $old_adress, $old_postnr, $old_postadr, $old_land_id; 

// Kontrollera först om det finns en order i "nya" ordertabellen 
$select = "SELECT max(ordernr) as ordernr from Ordertabell WHERE kundnr = '$kundnrsave'";
$res = mssql_query($select);
$row = mssql_fetch_object($res);

if (($row->ordernr) > '0') {
	$ordernr = $row->ordernr;
   	$oldorders = 'yes'; # konstaterar att det finns gammal order
	}
// Annars se om det finns en order i "gamla" ordertabellen
else {

	$select = "SELECT max(ordernr) as ordernr from Ordertabell_gamla WHERE kundnr = '$kundnrsave'";
	$res = mssql_query($select);
    	$row = mssql_fetch_object($res);
    	if (($row->ordernr) > '0') {
		$ordernr = $row->ordernr;
		$oldorders = 'yes'; // konstaterar att det finns gammal order
		$old = 'yes';	    // konstaterar att det är en order ur "gamla" ordertabellen
		}

	}

if (($old) && ($oldorders)) { // om det finns order och det är ur gamla tabellen

	$select = "SELECT lnamn, lco, ladress, lpostnr, lpostadr, land_id FROM Ordertabell_gamla WHERE ordernr = '$ordernr'";
	}

elseif ($oldorders) { // om senaste order är ur nya listan
	$select = "SELECT lnamn, lco, ladress, lpostnr, lpostadr, land_id FROM Ordertabell WHERE ordernr = '$ordernr'";
	}

# Om inget av ovanstående gäller så finns det tydligen inga gamla ordrar
# då använder vi helt enkelt faktura adressen som leveransadress
else {

$old_lnamn = $old_namn;
$old_lco = $old_co;
$old_ladress = $old_adress;
$old_lpostnr = $old_postnr;
$old_lpostadr = $old_postadr;
$old_lland_id = $old_land_id;

$stop = "yes";

}

if ($stop != 'yes'):  	
	// Plocka fram data för ovanstående fråga 
	$res = mssql_query($select);
	#echo $select;
	$row = mssql_fetch_object($res);

	$old_lnamn = $row->lnamn;
	$old_lco = $row->lco;
	$old_ladress = $row->ladress;
	$old_lpostnr = $row->lpostnr;
	$old_lpostadr = $row->lpostadr;
	$old_lland_id = $row->land_id;

endif; 

$update = "UPDATE Kund set lnamn = '$old_lnamn', lco = '$old_lco', ladress = '$old_ladress', ";
$update .= "lpostnr = '$old_lpostnr', lpostadr = '$old_lpostadr', lland_id = '$old_lland_id', ";
$update .= "ltelnr = '$old_telnr', lemail = '$old_email' ";
$update .= "WHERE kundnr = '$kundnrsave' ";

mssql_query ($update);

}

function testUsername ($userName) {

	include ("CConnect_ms.php");
	// Testa om användarnamnet redan är taget samt att användarnamnet inte är samma som någon annans kundnummer
	
	if ($userName == "")
		$returnvalue = false;
	else {
	
		$test = "SELECT userName FROM Kund WHERE userName = '$userName' ";
		$resTest = mssql_query ($test);

		if (mssql_num_rows($resTest) > 0) {
			$test2 = "SELECT kundnr FROM Kund WHERE kundnr like '$userName' ";
			$resTest2 = mssql_query ($test2);
			
			if (mssql_num_rows($resTest2) > 0) 
				$returnvalue = false;
			else
				$returnvalue = true;
		}
		else {
			$returnvalue =  false;
		}
	}	
	return $returnvalue;
}


function getBetalsattId ($betalsatt) {
	include ("CConnect_ms.php");

	if ($betalsatt == 'pf') { $betalsatt_id = 3; }
	else
	{
	  if ($betalsatt == 'faktura') { $betalsatt_id = 4; }
	    else
	    {
	      if ($betalsatt == 'kort') { $betalsatt_id = 5; }
	      elseif ($betalsatt == 'netpay')
	      { $betalsatt_id = 7; }
	      elseif ($betalsatt == 'giro')
	      { $betalsatt_id = 1; }
	      elseif ($betalsatt == 'butik')
	      { $betalsatt_id = 8; }
	      else
	      { $betalsatt_id = 0; }
	    }
	}

	return $betalsatt_id;
}

function getBetalsattet ($betalsatt_id) {
	include ("CConnect_ms.php");
	$select = "SELECT betalsatt FROM Betalsatt WHERE betalsatt_id = " . $betalsatt_id;
	$res = mssql_query ($select);
	$row = mssql_fetch_object ($res);
	return $row->betalsatt;
	
}

function getArticleInfo($artnr) {
	include ("CConnect.php");
	$select = "SELECT * FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query($select);
	$row = mysqli_fetch_object($res);
	return $row;
}
function getArticleSpec($artnr, $variable) {
	include ("CConnect.php");
	$select = "SELECT $variable FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query($select);
	$row = mysqli_fetch_object($res);
	return round($row->$variable);
}

%>

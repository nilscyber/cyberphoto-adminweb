<?php

/*

PHP login object
author		Nils Kohlström
version		0.9 2006-07-14

*/

Class CBasket {
	var $conn_ms; var $conn_my; var $conn_fi;
	var $confirm; var $kundnrsave;

function CBasket() {
	//include ("CConnect_ms.php");
	//include ("CConnect.php");
	global $fi;
	if ($fi) {
		$this->conn_fi = @mssql_pconnect ("62.80.138.170", "apache", "aKatöms#1");
		@mssql_select_db ("cyberphoto", $this->conn_fi);
	}
	
	$this->conn_my = Db::getConnection();
	$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
	@mssql_select_db ("cyberphoto", $this->conn_ms);

}
function login($kundnrtest, $passwd) {

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
	if ($passwd == ""  || $kundnrtest == "") {
		$this->confirm = 2;
		$confirm = 2;
	}
	else {
		
		$select = "SELECT kundnr, kundid, userName, trials from Kund ";
		$select .= "WHERE kundnr like '$kundnrtest' AND borttagen = 0";

		$res = mssql_query($select, $this->conn_ms);
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
						if ($trials > 0) {
							$update = "update Kund set trials=0 where kundnr=$kundnr";
							mssql_query($update, $this->conn_ms);
							}
						$confirm = 1;
						$kundnrsave = $kundnr; 
					}

					else  {
						$newtrials = $trials+1;
						$update = "update Kund set trials=$newtrials where kundnr=$kundnr";
						mssql_query($update, $this->conn_ms);
						$confirm='2';
						}
				}
				else {
					$confirm=3;
					}
			}
		}
		else {
		
			$select = "SELECT kundnr, kundid, userName, trials FROM Kund ";
			$select .= "WHERE userName = '$kundnrtest' AND borttagen = 0";

			$res = mssql_query($select, $this->conn_ms);
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
							if ($trials > 0) {
								$update = "update Kund set trials=0 where kundnr=$kundnr";
								mssql_query($update, $this->conn_ms);
								}
							$confirm=1;
							$kundnrsave = $kundnr; 
							}

						else  {
							$newtrials = $trials+1;
							$update = "update Kund set trials=$newtrials where kundnr=$kundnr";
							mssql_query($update, $this->conn_ms);
							$confirm=2;
							}

					}
					else {
						$confirm=3;
						}
				}
			}
			else {

				$confirm = 6;
			}
		}
	}
	
}
/**
 * plockare fram kundinfo från databasen
 *
 * @param boolean $lastInfo om senaste leverans och betalsätt skall plockas fram. 
 */
function customer_info($lastInfo) {
	


	/*
	Input:	$kundnrsave, $confirm, requires user to be loged in (i.e. $confirm == '1')
	Output:	Customer information in variables below.
	*/
	global $kundnrsave, $confirm, $old_namn, $old_co, $old_adress, $old_postnr, $old_postnr, $old_postadr, $old_land_id, $old_email, $old_telnr,
	$old_orgnr, $old_lnamn, $old_lco, $old_ladress, $old_lpostnr, $old_lpostadr, $old_lland_id, $old_ltelnr, $old_lemail,
	$old_levadress, $old_faktadress, $old_land, $old_lland, $old_faktura, $old_erref, $old_erordernr, $old_faktlev, $old_land_fi, $old_lland_fi,
	$old_foretag, $old_userName, $old_avtalskund, $old_mobilnr, $old_sms, $old_nyhetsbrev, $old_forsakringserbjudande, $senasteBetalsatt, $senasteLevsatt,
	$freight, $pay, $fi, $sv;

	$senasteLevsatt = 0; $senasteBetalsatt = 0;
	if ($confirm == 1) {

		// Plocka först fram kunduppgifterna

		$select  = "SELECT namn, co, adress, postnr, postadr, Kund.land_id, email, telnr, orgnr, mail_send, faktura, ";
		$select .= "lnamn, lco, ladress, lpostnr, lpostadr, lland_id, lemail, ltelnr, land, land_fi, erordernr, erref, faktlev, ";
		$select .= "savelogin, land_fi, foretag, userName, avtalskund, mobilnr, sms, nyhetsbrev, forsakringserbjudande ";
		$select .= "FROM Kund LEFT JOIN Land ON Kund.land_id = Land.land_id ";
		$select .= "WHERE kundnr = $kundnrsave ";

		$res = mssql_query($select, $this->conn_ms);
		$row = mssql_fetch_array($res);

		// returns selected variabls prefixed $old_, e.g. $old_namn
		if ((mssql_num_rows($res)) > 0)
		extract($row, EXTR_PREFIX_ALL, "old");
		if (!$sv)
			$old_land = $old_land_fi;
		// För gamla kunder som saknar leveransadress på sina ordrar.
		if (($kundrsave < 105138) AND ($old_lnamn == "" AND $old_lco == "" AND $old_ladress == "" AND $old_lpostnr == "" AND $old_lpostadr == "") )

		{
			check_lev_adress($kundnrsave);

		}

		// Vet inte riktigt hur man skriver en helt effektiv fråga, därför, detta extra
		$select2 = "SELECT land, land_fi from Land WHERE land_id = $old_lland_id";
		$res2 = mssql_query($select2, $this->conn_ms);
		$row2 = mssql_fetch_object($res2);
		$old_lland = $row2->land;
		$old_lland_fi = $row2->land_fi;
		if (!$sv)
			$old_lland = $old_lland_fi;
		// leta reda på eventuellt senaste orderuppgifterna
		$select = "SELECT betalsatt_id, leveranssatt_id, ordernr FROM Ordertabell_alla WHERE kundnr = " . $kundnrsave . " ORDER BY ordernr DESC";

		$res = mssql_query ($select, $this->conn_ms);
		$row = mssql_fetch_object($res);

		if (mssql_num_rows($res) > 0) {
			if ($lastInfo) {
				$senasteBetalsatt = $row->betalsatt_id;
				$senasteLevsatt = $row->leveranssatt_id;

				$pay = $this->getBets($senasteBetalsatt);
				$freight = $this->getLevSatt($senasteLevsatt);
			}

		}


	}
} # end customer_info



function add_customer($uppdatera, $mailpassw) {

	global $new_namn, $new_co, $new_adress, $new_postnr, $new_postadr, $new_land, $new_telnr,
	$new_orgnr, $new_email, $new_erref, $new_erordernr, $new_kommentar, $new_lco, $new_ladress,
	$new_lpostadr, $new_lpostnr, $new_lland, $kundnrsave, $confirm, $new_passw, $newcustomerset,
	$kundnr, $newcust, $kundnrsave, 
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
	$row = mssql_fetch_object (mssql_query ($select, $this->conn_ms));
	$land_id = $row->land_id;

	$select = "SELECT land_id FROM Land WHERE land = '$new_lland'";
	$row = mssql_fetch_object (mssql_query ($select, $this->conn_ms));
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
	if (!$uppdatera) {
		if ($this->testUsername ($userName)) {
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

			if ($new_nyhetsbrev == "yes")
				$new_nyhetsbrev = -1;
			else
				$new_nyhetsbrev = 0;

			if ($new_sms == "yes")
				$sms = -1;
			else
				$sms = 0;
			if ($new_passw == "")
				$new_passw = $this->generate_pass(5);
			$select = "SELECT max(kundnr) as kundnr FROM Kund ";
			$row = (mssql_fetch_object(mssql_query($select, $this->conn_ms)));
			$newkundnr = $row->kundnr;
			$newkundnr++;
			$datumet = date("Y-m-d H:i:s");


			$insert  = "INSERT INTO Kund (kundnr, namn, co, adress, postnr, postadr, land_id, email, telnr, orgnr, erref, erordernr, ";
			$insert .= "lnamn, lco, ladress, lpostadr, lpostnr, lland_id, kundid, lemail, ltelnr, faktlev, savelogin, userName, foretag, skapad, nyhetsbrev, mobilnr, sms) values ";
			$insert .= " ('$newkundnr', '$new_namn', '$new_co', '$new_adress', ";
			$insert .= "'$new_postnr', '$new_postadr', '$land_id', '$new_email', '$new_telnr', '$new_orgnr', '$new_erref', '$new_erordernr', ";
			$insert .= "'$new_lnamn', '$new_lco', '$new_ladress', '$new_lpostadr', '$new_lpostnr', '$lland_id', '$new_passw', '$new_email', ";
			$insert .= "'$new_telnr', '$new_faktlev', '$spara', '$userName', '$foretaget', '$datumet', $new_nyhetsbrev, '$new_mobilnr', $sms)";

			$res = mssql_query ($insert, $this->conn_ms);

			if ($res) {
				$kundnrsave = $newkundnr;
				$confirm = 1;

				//$old_erordernr = $new_erordernr;
				//$old_erref = $new_erref;
				//$old_kommentar = $new_kommentar;
				$this->maila_kundinfo($newkundnr, $new_passw, $mailpassw);
			}
		}
	}

	// Om uppdatera gamla uppgifter
	else {

		// kolla först om inloggad, annars
		if ((ereg("[0-9]{4,}", $kundnrsave)) && ($confirm == 1)) {
			;
		} else {
			// Testa det angivna lösenordet

			$this->login($kundnrsave, $new_passw);
		}

		if ($confirm == 1) {

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
			if ($change_passw != "" && $change_passw != " ")
				$update .= "kundid = '$change_passw', ";
			//$update .= "userName = '$new_userName', ";
			$update .= "nyhetsbrev = $new_nyhetsbrev, ";
			$update .= "mobilnr = '$new_mobilnr', ";
			$update .= "sms = $sms ";
			$update .= "WHERE kundnr = '$kundnrsave'";
			//echo $update;
			$res = mssql_query ($update, $this->conn_ms);

			// maila ut den nya kundinfon

			//$this->maila_kundinfo($newkundnr, $passw, $mailpassw);

			$order_erordernr = $new_erordernr;
			$order_erref = $new_erref;
			$order_kommentar = $new_kommentar;

		}
		else {

			$confirm = 1;
			$wrongpassword = 'yes';
		}

	}

}

function maila_kundinfo($kundnr, $passw, $mailpassw) {

	$select = "SELECT * from Kund WHERE kundnr = '$kundnr'";
	$res = mssql_query ($select, $this->conn_ms);
	$row = mssql_fetch_object ($res);
	if ($row->email != ''): 	
	
$selland1 = "SELECT land FROM Land where land_id = '$row->land_id'";
$res1 = mssql_query ($selland1, $this->conn_ms);
$row1 = mssql_fetch_object ($res1);
$land = $row1->land;

$selland2 = "SELECT land FROM Land where land_id = '$row->land_id'";
$res2 = mssql_query ($selland2, $this->conn_ms);
$row2 = mssql_fetch_object ($res2);
$lland = $row2->land;

$message = <<<eof
Hej!

Här kommer era kunduppgifter

Kundnummer: $kundnr
Användarnamn: $row->userName
Lösenord:   
eof;
$mailpassw = true;
if ($mailpassw && $row->kundid != '')
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
$extra = "From: order@cyberphoto.se \n ";

mail($row->email, $subject, $message, $extra);
	endif; 
}

function check_lev_adress($kundnrsave) {

global $old_lnamn, $old_lco, $old_lpostnr, $old_lpostadr, $old_lland_id, $old_ladress, $old_email, $old_telnr, 
$old_namn, $old_co, $old_adress, $old_postnr, $old_postadr, $old_land_id; 

// Kontrollera först om det finns en order i "nya" ordertabellen 
$select = "SELECT max(ordernr) as ordernr from Ordertabell WHERE kundnr = '$kundnrsave'";
$res = mssql_query($select, $this->conn_ms);
$row = mssql_fetch_object($res);

if (($row->ordernr) > '0') {
	$ordernr = $row->ordernr;
   	$oldorders = 'yes'; # konstaterar att det finns gammal order
	}
// Annars se om det finns en order i "gamla" ordertabellen
else {

	$select = "SELECT max(ordernr) as ordernr from Ordertabell_gamla WHERE kundnr = '$kundnrsave'";
	$res = mssql_query($select, $this->conn_ms);
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
	$res = mssql_query($select, $this->conn_ms);
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

mssql_query ($update, $this->conn_ms);

}

function testUsername ($userName) {

	
	// Testa om användarnamnet redan är taget samt att användarnamnet inte är samma som någon annans kundnummer
	
	if ($userName == "")
		$returnvalue = false;
	else {
	
		$test = "SELECT userName FROM Kund WHERE userName = '$userName' ";
		$resTest = mssql_query ($test, $this->conn_ms);

		if (mssql_num_rows($resTest) > 0) {
			$test2 = "SELECT kundnr FROM Kund WHERE kundnr like '$userName' ";
			$resTest2 = mssql_query ($test2, $this->conn_ms);
			
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
function getLevSatt($leveranssatt_id) {

	$select = "SELECT artnr FROM Leveranssatt WHERE leveranssatt_id = " . $leveranssatt_id;
	$res = mssql_query ($select, $this->conn_ms);
	$row = mssql_fetch_object ($res);
	//echo $select;
	if (mssql_num_rows($res) > 0)
		return $row->artnr;
	else
		return "";		
}
function getLeveranssatt_id ($fraktartnr, $fi) {

	if ($fi) {
		//TODO: lägga in de finska leveranssätten. 
		if (ereg("^fraktutl", $fraktartnr))
			$leveranssatt_id = 10;
		elseif (ereg("^fraktpost", $fraktartnr))
			$leveranssatt_id = 3;
		elseif (ereg("^frakthem", $fraktartnr))
			$leveranssatt_id = 4;
		elseif (ereg("^frakt1600", $fraktartnr))
			$leveranssatt_id = 5;
		elseif (ereg("^frakt16", $fraktartnr) OR ereg("^fraktbrevUtl", $fraktartnr))
			$leveranssatt_id = 1;
		elseif (ereg("^frakt0900", $fraktartnr))
			$leveranssatt_id = 6;
		elseif (ereg("^fraktjetpaktoday", $fraktartnr))
			$leveranssatt_id = 7;
		elseif (ereg("^fraktbutik", $fraktartnr))
			$leveranssatt_id = 8;	
		elseif (ereg("^fraktrek", $fraktartnr))
			$leveranssatt_id = 15;
		elseif (ereg("^fraktfritt", $fraktartnr))
			$leveranssatt_id = 3;
		elseif (ereg("^frakt0700", $fraktartnr))
			$leveranssatt_id = 14;
		else
			$leveranssatt_id = 3;	
		
	
	} else  {
	
		if (ereg("^fraktutl", $fraktartnr))
			$leveranssatt_id = 10;
		elseif (ereg("^fraktpost", $fraktartnr))
			$leveranssatt_id = 3;
		elseif (ereg("^frakthem", $fraktartnr))
			$leveranssatt_id = 4;
		elseif (ereg("^frakt1600", $fraktartnr))
			$leveranssatt_id = 5;
		elseif (ereg("^frakt16", $fraktartnr) OR ereg("^fraktbrevUtl", $fraktartnr))
			$leveranssatt_id = 1;
		elseif (ereg("^frakt0900", $fraktartnr))
			$leveranssatt_id = 6;
		elseif (ereg("^fraktjetpaktoday", $fraktartnr))
			$leveranssatt_id = 7;
		elseif (ereg("^fraktbutik", $fraktartnr))
			$leveranssatt_id = 8;	
		elseif (ereg("^fraktrek", $fraktartnr))
			$leveranssatt_id = 15;
		elseif (ereg("^fraktfritt", $fraktartnr))
			$leveranssatt_id = 3;
		elseif (ereg("^frakt0700", $fraktartnr))
			$leveranssatt_id = 14;			
		else
			$leveranssatt_id = 3;	
	}	
	return $leveranssatt_id;

}
function getBets($betalsatt_id) {
	$pay = "";
	if ($betalsatt_id == 3)
		$pay = "pf";
	elseif ($betalsatt_id == 4)
		$pay = "faktura";
	elseif ($betalsatt_id == 5)
		$pay = "kort";
	elseif ($betalsatt_id == 7)
		$pay = "netpay";
	elseif ($betalsatt_id == 1)
		$pay = "giro";
	elseif ($betalsatt_id == 8)
		$pay = "butik";
	else
		$pay = "";
    
	return $pay;
	
}
function getBetalsattet ($betalsatt_id) {
	
	$select = "SELECT betalsatt FROM Betalsatt WHERE betalsatt_id = " . $betalsatt_id;
	$res = mssql_query ($select, $this->conn_ms);
	$row = mssql_fetch_object ($res);
	return $row->betalsatt;
	
}

function getArticleInfo($artnr) {	
	$select = "SELECT * FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query($this->conn_my, $select);
	$row = mysqli_fetch_object($res);
	return $row;
}
function getArticleSpec($artnr, $variable) {
	
	$select = "SELECT $variable FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query($this->conn_my, $select);
	$row = mysqli_fetch_object($res);
	return round($row->$variable);
}

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


function viewPacketDelivery($artnr, $mangd, $fi) {
	global $count, $lagersaldo, $bestallt, $lev_datum, $lev_datum_norm, $lev_datum_norm_fi, $bestallningsgrans, $bestallningsgrans_fi, $fi, $sv, $bestallt_fi;		
	
	//echo $artnr;
	$select =  "SELECT Paketpriser.artnr_paket, Paketpriser.artnr_del, Paketpriser.antal, Artiklar.artnr, ";
	$select .= "Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar_fi.lagersaldo_fi, Artiklar.lagersaldo, Artiklar.beskrivning, ";
	$select .= "tillverkare, Artiklar.lagersaldo, Artiklar.bestallt, Artiklar.lev_datum, ";
	$select .= "lev_datum_fi, bestallningsgrans, bestallningsgrans_fi, lev_datum_norm, lev_datum_norm_fi, lagersaldo_fi, bestallt_fi, bestallt, ";
	$select .= "Artiklar.bestallningsgrans, Artiklar.lev_datum_normal ";
	$select .= "FROM Artiklar, Paketpriser, Tillverkare LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi WHERE Artiklar.artnr = Paketpriser.artnr_del ";
	$select .= "AND Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "AND Paketpriser.artnr_paket = '$artnr' ";
	/*
	if ($fi) {
		$select  = "SELECT Paketpriser.artnr_paket, Paketpriser.artnr_del, Paketpriser.antal,  artnr_fi as artnr, Artiklar.lagersaldo, Artiklar.beskrivning, Artiklar.bestallt, ";
		$select .= "beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, tillverkare, frakt, lagersaldo_fi, bestallt_fi, ";
		$select .= "lev_datum_fi, bestallningsgrans, lev_datum_norm, lev_datum_norm_fi, Artiklar.ej_brev as ej_brev, ";
		$select .= "Artiklar.link, link2_fi ";
		$select .= " FROM Artiklar, Artiklar_fi, Tillverkare, Paketpriser ";
		$select .= "WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Paketpriser.artnr_paket = Artiklar.artnr   ";
		$select .= "AND Paketpriser.artnr_paket = '$artnr' ";
	} else {
		$select  = "SELECT Paketpriser.artnr_paket, Paketpriser.artnr_del, Paketpriser.antal, Artiklar.artnr, Artiklar.beskrivning, Artiklar.kommentar, utpris, lagersaldo, bestallt, ";
		$select .= "lev_datum, bestallningsgrans, lev_datum_norm, lev_datum_norm_fi, frakt, ";
		$select .= "Artiklar.link ";
		$select .= " FROM Artiklar, Tillverkare ";
		$select .= "WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id  ";
		$select .= "AND Paketpriser.artnr_paket = '$artnr' ";
		
	}
	*/
	//echo $select;
	/* 	while ($row = mysqli_fetch_array($res) )   {
	extract ($row); */
	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > '0') {
		
		while ($row = mysqli_fetch_array($res)):
		extract($row);
		$count = $antal*$mangd;
		if ($tillverkare != '.')
			$description = $tillverkare . " ";

		if (!$sv && $beskrivning_fi != "")
			$description .= $beskrivning_fi;
		else 	
			$description .= $beskrivning;


		?>
		
		<tr>
		  <td bgcolor="#ECECE6"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php echo $description; ?></font></td>
		  <td bgcolor="#ECECE6" align="center"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php echo $antal*$mangd; ?></font></td>
		  <td bgcolor="#ECECE6"><font color="#2B2B2B" face="Verdana, Arial" size="1">
		<?php	
		//($artnr, $count, NULL);
		$this->check_lager($artnr, $fi); 
		?>
		</font></td>
		</tr>
		<?php
		endwhile;

	}
	else { ?>

		  </font></td>
		</tr>
		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
		  <a href="mailto:order@cyberphoto.se"><?php if ($fi == 'yes'): ?>Information saknas, kontakta cyberphoto för mer info<?php else: ?>Information saknas, kontakta cyberphoto för mer info<?php endif; ?></a>
		  </font></td>
		</tr>
		<?php
	}
	?>
	  </font></td>
	</tr>
		


<?php
	
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
	// bara A-Z, a-z och siffror för att inte få några
	// skumma tecken
	$randvalue = mt_rand (48, 122);
	$c = chr($randvalue); 
	
	// Lägg till på $nps om det är i rätt format

	if (eregi("[a-z0-9]", $c)) {
		$nps = $nps.$c;
		// förenkla för kunden genom att bara använda små bokstäver
		$nps = strtolower($nps);
	}
	
}
 	return ($nps); 
}

function viewBasketShort($kundvagn) {
	
	$output = "";
	if (ereg ("(grejor:)(.*)", $kundvagn,$matches)) {
		# Split the number of items and article id s into a list
		$orderlista = $matches[2];
		$argument = split ("\|", $orderlista);
	}

	$goodscounter=0;
	$goodsvalue=0;
	 
	$n = count($argument);
	//for ($i=0; ($i < $n);  $i+=2) {
	for ($i=$n-2; ($i > -1); $i+=-2) {
		$arg = $argument[$i];        # Article id
		$count = $argument[$i+1];    # Keeps track of the number of the same article

		$select  = "SELECT artnr, beskrivning, kommentar, utpris, tillverkare, frakt, lagersaldo, bestallt, ";
		$select .= "lev_datum, bestallningsgrans, lev_datum_normal, frakt FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
		$select .= "WHERE artnr='$arg'";
		
		# Alla värden försvinner inte i varje loop, så därför måste vi göra enligt nedan
		$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = "";

		$row = mysqli_fetch_array(mysqli_query($this->conn_my, $select));
		extract($row);
		
		
		$goodscounter += '1';
		$goodsvalue += ($utpris*$count);
		
		$description = $count . "st ";
		if ($tillverkare != '.')
			$description .= $tillverkare . " ";
		$description .= $beskrivning;
		
		if (strlen($description) >= '24')
			$description = substr ($description, 0, 24) . "...";

		if (!eregi("frakt", $artnr)) {
		$output .= "<option value=\"\">$description</option>";
	
	
		}
	}

	return $output;

}
function getArticleInfoBuy ($artnr) {
	global $kundvagn, $utpris, $moms, $lagersaldo, $lagersaldo_fi, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans, $betalsatt, 
	$PHP_SELF, $bestallningsgrans, $pack, $fi, $sv, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $artnr2, $count, $select;	
	$count = 1;
	if ($fi) {
		// Get currency for EURO
		$select_valuta = "SELECT kurs FROM ValutaNew WHERE valutakod = 'EUR'";
		$res_valuta = mysqli_query($select_valuta);
		$valuta = mysqli_fetch_object($res_valuta);
	}
	
	//echo $artnr; 
	$select = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.utpris, Artiklar_fi.utpris_fi, Tillverkare.tillverkare, ";
	$select .= "Artiklar.no_buy, Artiklar.lagersaldo, Artiklar_fi.lagersaldo_fi, Artiklar.bestallt, Artiklar_fi.bestallt_fi, Artiklar.kommentar,  ";
	$select .= "Artiklar_fi.kommentar_fi, Artiklar.lev_datum, Artiklar_fi.lev_datum_fi, Artiklar.lev_datum_norm, Artiklar_fi.lev_datum_norm_fi, ";
	$select .= "Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.lev_datum_normal, Moms.momssats, Moms.momssats_fi, Artiklar.utgangen, Artiklar_fi.utgangen_fi ";
	$select .= "FROM Artiklar, Artiklar_fi, Tillverkare, Moms ";
	$select .= "WHERE Artiklar.tillverkar_id=Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id AND Artiklar_fi.artnr_fi = Artiklar.artnr ";
	$select .= "AND Artiklar.ej_med=0 && Artiklar.artnr='$artnr' && (utgangen=0 OR lagersaldo > 0) ";
	
	$select = "SELECT Artiklar.artnr, Artiklar_fi.artnr_fi, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.utpris, Artiklar_fi.utpris_fi, Tillverkare.tillverkare, ";
	$select .= "Artiklar.no_buy, Artiklar.lagersaldo, Artiklar_fi.lagersaldo_fi, Artiklar.bestallt, Artiklar_fi.bestallt_fi, Artiklar.kommentar,  ";
	$select .= "Artiklar_fi.kommentar_fi, Artiklar.lev_datum, Artiklar_fi.lev_datum_fi, Artiklar.lev_datum_norm, Artiklar_fi.lev_datum_norm_fi, ";
	$select .= "Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.lev_datum_normal, Moms.momssats, Moms.momssats_fi, Artiklar.utgangen, Artiklar_fi.utgangen_fi ";
	$select .= "FROM Artiklar, Tillverkare, Moms LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
	$select .= "WHERE Artiklar.tillverkar_id=Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id ";
	$select .= "AND Artiklar.ej_med=0 && Artiklar.artnr='$artnr' && (utgangen=0 OR lagersaldo > 0) ";
	//echo $select;
	/*
			$select  = "SELECT artnr_fi as artnr, Artiklar.lagersaldo, Artiklar.beskrivning, Artiklar.bestallt, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, tillverkare, ";
			$select .= "frakt, lagersaldo_fi, bestallt_fi, ";
			$select .= "lev_datum_fi, bestallningsgrans, lev_datum_norm, lev_datum_norm_fi, Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, ";
			$select .= "Moms.momssats_fi as momssats, Artiklar.link, link2_fi ";
			$select .= " FROM Artiklar, Artiklar_fi, Tillverkare, Kategori, Moms ";
			$select .= "WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id ";
			$select .= "AND artnr='$arg'";	
	*/
	$res = mysqli_query($this->conn_my, $select);
	
	$rows = mysqli_fetch_object($res);
	$lev_datum_norm = $rows->lev_datum_norm;
	$lev_datum_norm_fi = $rows->lev_datum_norm_fi;
	
	$artnr2 = $rows->artnr;
	return $rows;
	
}

function viewItemsInBasket($firstbasket, $fi) {
	global $kundvagn, $goodsvalue, $utpris, $goodsvalueMoms, $moms, $artnr, $count, $lagersaldo, $lagersaldo_fi, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans, $betalsatt, 
	$PHP_SELF, $brev, $bestallningsgrans, $pack, $alltidBrev, $pallDelivery, $fi, $sv, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $release;	
	
	$freight_check = NULL;
	$pallDelivery = false; // visar om någon produkt innehåller frakttillägg. Används bl.a. till att tyngre produkter inte skall skickas som hempkaet
	$brev = true;
	$alltidBrev = false;	
	# Get the cookie kundvagn
	$answers = $kundvagn;
		
	if (ereg ("(grejor:)(.*)", $answers,$matches)) {
		# Split the number of items and article id s into a list
		$orderlista = $matches[2];
		$argument = split ("\|", $orderlista);
	}

	$goodscounter=0;
	$goodsvalue=0;
	$goodsvalueMoms=0;
	$moms = 0;
	
	 
	$n = count($argument);
	$j = 0;
	for ($i=0; ($i < $n);  $i+=2) {

		$arg = $argument[$i];        # Article id
		$count = $argument[$i+1];    # Keeps track of the number of the same article
		//if ($fi) {
			$select  = "SELECT artnr_fi as artnr, Artiklar.lagersaldo, Artiklar.beskrivning, Artiklar.bestallt, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, utpris, tillverkare, ";
			$select .= "frakt, lagersaldo_fi, bestallt_fi, Artiklar.release ";
			$select .= "lev_datum_fi, bestallningsgrans, lev_datum_norm, lev_datum_norm_fi, Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, ";
			$select .= "Moms.momssats_fi, Moms.momssats, Artiklar.link, link2_fi ";
			$select .= " FROM Artiklar, Tillverkare, Kategori, Moms LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
			$select .= "WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id ";
			$select .= "AND artnr='$arg'";
		/**
		} else {
			$select  = "SELECT artnr, Artiklar.beskrivning, Artiklar.kommentar, utpris, tillverkare, frakt, lagersaldo, bestallt, ";
			$select .= "lev_datum, bestallningsgrans, lev_datum_normal, frakt, Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, lev_datum, lev_datum_norm, Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, ";
			$select .= "Moms.momssats, Artiklar.link, Artiklar.release  ";
			$select .= " FROM Artiklar, Tillverkare, Kategori, Moms ";
			$select .= "WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id ";
			$select .= "AND artnr='$arg'";
			
		}
		*/
		//echo $select;
		# Alla värden försvinner inte, så därför måste vi göra enligt nedan
		$ej_brev = $artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $lagersaldo_fi = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = $lev_datum_norm_fi = $lev_datum_norm = "";
		$momssats = 0;
		//echo $select;
		$row = mysqli_fetch_array(mysqli_query($select));
		extract($row);
		if ($fi) {
			$link = $link_fi;
			$utpris = $utpris_fi;
			$momssats = $momssats_fi;
		} else {
			$momssats = $momssats;
		}
		
		// kolla om det är ett paket
		if (eregi("pac$", $artnr)) 
			$pack = true;
		
		// kolla om det inte går som brev
		if (($ej_brev == -1 || $kat_ej_brev == -1)) {
			$brev = false;
			
		}
		if ((eregi("presentkort", $artnr)))
			$alltidBrev = true;
			
			
		// Lägg på extra frakt om det behövs
		if ($frakt > 0) {
		//$extraFrakt = true;
			if ($extra_freight < $frakt) {
				
				$extra_freight = $frakt; 
				
				}
		}
		if ($extra_freight == 3)
			$pallDelivery = true;	
		
		if (eregi("presentkort", $artnr)) {	
			if ($firstbasket == "nooutput")
				wiewGiftCardInBasket($j, false);
			else
				wiewGiftCardInBasket($j, true);
			$j +=1;
		}		
		
		$goodscounter += 1;
		$goodsvalue += ($utpris*$count);
		$goodsvalueMoms += ($utpris + $utpris * $momssats)*$count;
		$moms += $utpris*$count*$momssats;
		
		if ($tillverkare != '.')
			$description = $tillverkare . " ";
		if (!$sv && $beskrivning_fi != "") {			
			$description .= $beskrivning_fi . " " . $kommentar_fi;
		} else {
			$description .= $beskrivning . " " . $kommentar;
		}

			
		
		// visa bara info om det inte är kostnadsfri frakt
		if (!(eregi("fraktbutik", $artnr)) && $firstbasket != 'nooutput' && !(eregi("presentkort", $artnr)) ) {

?>

			<tr>
				
			  <td bgcolor="#ECECE6" width="262"><font size="1" face="Verdana">
			  <?php 
	  			//if ((eregi("frakt", $artnr))) echo "<a name=\"beforeFreight\"></a>";
				//if ($i+4 == $n || $n < 3) echo "<a name=\"beforeFreight\"></a>"; // lägg till bokmärke så att vi kan visa nedre delen av kundvagnen, men bara om kundvagnen är lång, mer än fyra artiklar. 
				if ($i+4 == $n && $n > 12) echo "<a name=\"beforeFreight\"></a>"; // lägg till bokmärke så att vi kan visa nedre delen av kundvagnen, men bara om kundvagnen är lång, mer än fyra artiklar. 
				if ($link <> "")
					{
					 if (ereg("http:", $link))						
					 	print "<A href=\"".$link."\">";
					 else
					 	print "<A href=\"/".$link."\">";
		
					}
					
				 else
					{
						if ($fi && !$sv)
							print "<A HREF=\"/info_fi.php?article=".$artnr."\">";
						elseif ($fi && $sv)
							print "<A HREF=\"/info_fi_se.php?article=".$artnr."\">";
						else 
							print "<A HREF=\"/info.php?article=".$artnr."\">";
					}
								
				
	  			echo $description; ?></font>
	  			
	  			</A>
	  			</td>
			  <td align="center" bgcolor="#ECECE6" width="30"><font size="1" face="Verdana"><?php echo $count; ?></font></td>				  
			<td align="center" bgcolor="#ECECE6" width="66"><font size="1" face="Verdana">	
				<?php if (!(eregi("^frakt", $artnr))) { ?>
		
				<A HREF="javascript:modifyItemsInBasket('<?php echo $artnr; ?>', '<% echo $fi; %>', '<% echo $sv; %>')">
				<font face="Verdana, Arial" size="1">
				<?php if ($fi && !$sv) { ?>
				muuta lukumäärä</font>
				</A>
				<?php } else { ?>
				ändra antal</font></A>
				<?php } ?>
				<?php } else { ?>
				&nbsp;&nbsp;
				<?php }; ?>							
			</font></td>
			<td align="left" bgcolor="#ECECE6" width="100"><font size="1" color="#008000" face="Verdana">
				<?php		if (!(eregi("^frakt", $artnr))) { 
						
							$this->check_lager($artnr, $fi); ?> 
				<?php		
							} else { ?>
						&nbsp;&nbsp;
				<?php 		} ?>
		
			</font></td>

			<td align="right" bgcolor="#ECECE6" width="79"><font size="1" face="Verdana">
			
			<?php 
			if ($fi)	
				echo number_format($utpris*$count, 0, ',', ' ') . " EUR";  
			else 	
				echo number_format($utpris*$count, 0, ',', ' ') . " SEK";  
			?>
			</font></td>
			<td align="right" bgcolor="#ECECE6" width="82"><font size="1" face="Verdana">
			
			<?php 
			if ($fi)
				echo number_format(($utpris + $utpris * $momssats) * $count, 0, ',', ' ') . " EUR"; 
			else 	
				echo number_format(($utpris + $utpris * $momssats) * $count, 0, ',', ' ') . " SEK"; 
			?>
			
			</font></td>
			
			</tr>

			<?php  	
			
		} 
	}

	
	if ($extra_freight && $firstbasket == 'yes' && $firstbasket != 'nooutput' && $extra_freight != 999 && $extra_freight != 3) {
	
	    if ($extra_freight == 1) $extra_freight_artnr = 'frakt+';
	    elseif ($extra_freight == 2) $extra_freight_artnr = 'frakt+2';
	    //elseif ($extra_freight == 3 && $old_foretag == 0) $extra_freight_artnr = 'frakthempall';	    
		//elseif ($extra_freight == 3 && $old_foretag == -1) $extra_freight_artnr = 'fraktpall';	    
	    else $extra_freight_artnr = 'frakt+'; // för säkerhets skull
	    $select  = "select Artiklar.beskrivning, Artiklar.kommentar, utpris, Moms.momssats from Artiklar, Moms where Artiklar.momskod = Moms.moms_id AND ";
	    $select .= " artnr='$extra_freight_artnr'";
	
	    $res = mysqli_query($this->conn_my, $select);
	    $row = mysqli_fetch_object($res);
	
	    $name = $row->beskrivning;
	    $comment = $row->kommentar;
	    $outprice = $row->utpris;
	    $momsts = $row->momssats;
	
	    $manufacturer = "";
	    $goodsvalue += $outprice;
	    $goodsvalueMoms += ($outprice + $outprice * $momsts);
?>	    
	    

	<tr>
		<td bgcolor="#ECECE6" width="262"><font size="1" face="Verdana"><?php echo $name; ?></font></td>
		<td align="center" bgcolor="#ECECE6" width="30"><font size="1" face="Verdana">1</font></td>

		<td bgcolor="#ECECE6">&nbsp;&nbsp;</td>
		<td bgcolor="#ECECE6">&nbsp;&nbsp;</td>

		<td align="right" bgcolor="#ECECE6" width="79"><font size="1" face="Verdana">

		<?php echo number_format($outprice, 0, ',', ' ') . " SEK";  ?>
		</font></td>
		<td align="right" bgcolor="#ECECE6" width="82"><font size="1" face="Verdana">

		<?php echo number_format(($outprice + $outprice * $momsts) * $count, 0, ',', ' ') . " SEK"; ?>

		</font></td>


	</tr>
	<?php  
	    
	}
	?>
		
	<?php

}

/**
 * kontrollerar lagersaldo
 *
 * @param string $artnr 
 */
function check_lager($artnr) {
	
	global $fi, $sv;
	
	if ($fi)
		$this->check_lager_fi($artnr);
	else 	
		$this->check_lager_se($artnr);			
}
/**
 * kontrollerar lagersaldo
 *
 * @param string $artnr
 */
function check_lager_se($artnr) {
	global $count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue, 
	$est_delivery, $fi, $sv, $release, $fi, $sv;

		?>
		<font size="1" face="Verdana, Arial" color="#385F39">
		<?php
		
	//$ret = "";
	$release = substr($release, 0, 10);
	$package_stock_sv = false;

	# Make a check if freigt is already selected. 
	if (ereg("^frakt", $artnr))
		{ $freight_check = 1; }

	if (ereg("pac$", $artnr)) {    // kollar tillgången om det är ett paket
		$package_stock_sv = $this->check_package_sv(); 	
	}
	//echo "här: " . $lagersaldo . $count;
	if ($lagersaldo >= $count || $package_stock_sv) {
		if (!$sv)
			print "finns i lager"; 
		else 
			print "<a onMouseOver=\"return escape('Varan finns på lager och skickas normalt samma dag som ni beställer')\" style=\"text-decoration: none\"> finns i lager</a>";
	
	/*		

	*/
	} else {

		# Kolla hur många det finns på kö
		# antal på köp visas i $queue, nollställes först. 
		$queue = 0;
		
		$queue = $this->check_queue($artnr, $fi, null);
		
		$neededStock = $queue + $count;
		
		if ($bestallt >= $neededStock )  { 	
			
			echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($artnr, $count, null, false, $fi, $sv )	;
			
		} 


		else  { 
		//<a onMouseOver=\"return escape('')\" style=\"text-decoration: none\"> 
			if (ereg("pac$",$artnr)) {				  				
				print "<a href=\"javascript:levStatusPaket('$artnr', $count, false, true) \" onMouseOver=\"return escape('Varan består av ett paket med flera delar. En eller flera av delarna är slut i lager. Klicka för att se lagerstatus på varje enskild vara. Naturligtvis går paketet bra att beställa ändå. ')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\" >klicka för info</a>";						
			} elseif ($bestallningsgrans == 0)  {
			  
				// kolla om den är släppt ännu eller inte. 
				if ($release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($release) >= mktime()) {
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har ännu inte kommit ut på marknaden. Beräknat första leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) ."<br>Produkten är en beställningsvara')\" style=\"text-decoration: none\">Släpps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
					} else { // då blir det vanlig beställningsvara. 
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> beställningsvara</></font></a>";													
					}
				} else {
					print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> beställningsvara</></font></a>";													
				}
				
				
			} else {
				 
				if ($release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($release) >= mktime()) {					
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har ännu inte kommit ut på marknaden. Beräknat första leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Släpps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
					} else {
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager. <br>Normal leveranstid är <b>$lev_datum_norm </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> tillfälligt slut</font></a>" ;														
					}
				} else {
					print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager. <br>Normal leveranstid är <b>$lev_datum_norm </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> tillfälligt slut</font></a>" ;														
				}
			}	
		}
	}         
}
/**
 * kontrollerar lagersaldo
 *
 * @param unknown_type $artnr
 * @param unknown_type $fi
 */
function check_lager_fi($artnr) {
	global	$count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue, 
	$est_delivery, $fi, $sv, $release, $fi, $sv;

		?>
		<font size="1" face="Verdana, Arial" color="#385F39">
		<?php
		
	//$ret = "";

	$package_stock_sv = false;
	$package_stock_fi = false;
	$release = substr($release, 0, 10);
	# Make a check if freigt is already selected. 
	if (ereg("^frakt", $artnr))
		{ $freight_check = 1; }

	if (ereg("pac$", $artnr)) {    // kollar tillgången om det är ett paket
		$package_stock_fi = $this->check_package_fi(); 
		if (!$package_stock_fi) $package_stock_sv = $this->check_package_sv();		
	}
	
	if ($lagersaldo_fi >= $count || $package_stock_fi) {
		if (!$sv)
			print "<a onMouseOver=\"return escape('Tuote löytyy varastosta ja lähetetään normaalisti samana päivänä kuin teet tilauksesi')\" style=\"text-decoration: none\"> arastossa</a>"; 
		else 
			print "<a onMouseOver=\"return escape('Varan finns på lager och skickas normalt samma dag som ni beställer')\" style=\"text-decoration: none\"> finns i lager</a>";	
	} else {

		# Kolla hur många det finns på kö
		# antal på köp visas i $queue, nollställes först. 
		$queue = 0;
		
		$queue = $this->check_queue($artnr, true, null);
		
		$neededStock = $queue + $count;
		
		if ($bestallt_fi >= $neededStock )  { 			
			echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($artnr, $count, null, true, $fi, $sv )	;			
		} 
		elseif ( ($package_stock_sv || $lagersaldo >= $count) )  { 				
			if (!$sv)
				print "<a onMouseOver=\"return escape('Tuote löytyy keskusvarastosta. Toimitusaika pitenee kahdella päivällä')\" style=\"text-decoration: none\"> keskusvarastossa</a>"; 
			else 
				print "<a onMouseOver=\"return escape('Varan finns på centrallagret. Två dagar tillkommer på leveranstiden')\" style=\"text-decoration: none\"> finns på centrallager</a>";							
		} 
		elseif ($bestallt >= $neededStock) {
			echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($artnr, $count, null, false, true, $sv );
		} else  { 
		//<a onMouseOver=\"return escape('')\" style=\"text-decoration: none\"> 
			if (ereg("pac$",$artnr)) {							
				if (!$sv)
					print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \"><a onMouseOver=\"return escape('Tuote on paketti joka koostuu eri osista. Yksi tai useampi osa on loppunut varastosta. Klikkaa nähdääksesi joka tuotteen varastotilaa. Luonnollisesti voit tilata paketin kuitenkin.')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">lisää tietoja tästä</a>";
				else
					print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \"><a onMouseOver=\"return escape('Varan består av ett paket med flera delar. En eller flera av delarna är slut i lager. Klicka för att se lagerstatus på varje enskild vara. Naturligtvis går paketet bra att beställa ändå. ')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\" >klicka för info</a>";
					
			} elseif ($bestallningsgrans == 0)  {
			  
				// kolla om den är släppt ännu eller inte. 
				if ($release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($release) >= mktime()) {
						// TODO: få in att varan är en beställningsvara också. 
						if (!$sv) 
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote ei ole vielä markkinoilla, arvioitu ensimmäinen toimitusaika " . $this->showDeliveryDate($release, 1, $fi, $sv) . "<br>Ainoastaan tilauksesta')\" style=\"text-decoration: none\">Tulee markkinoille " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har ännu inte kommit ut på marknaden. Beräknat första leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) ."<br>Produkten är en beställningsvara')\" style=\"text-decoration: none\">Släpps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
					} else { // då blir det vanlig beställningsvara. 
						if (!$sv) // TODO: finska nästa rad
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> beställningsvara</></font></a>";													
					}
				} else {
					if (!$sv) // TODO: finska nästa rad
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
					else
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> beställningsvara</></font></a>";													
				}
				
				
			} else {
				 
				if ($release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($release) >= mktime()) {					
						if (!$sv) // TODO: släpps på finska
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote ei ole vielä markkinoilla, arvioitu ensimmäinen toimitusaika " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Släpps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har ännu inte kommit ut på marknaden. Beräknat första leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Släpps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
					} else {
						if (!$sv) // TODO: finska på nästa rad
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> tillfälligt slut</font></a>" ;														
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> tillfälligt slut</font></a>" ;														
					}
				} else {
					if (!$sv) // TODO: finska på nästa rad
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> tillfälligt slut</font></a>" ;														
					else
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> tillfälligt slut</font></a>" ;														
				}
			}	
		}
	}         
	
}
function check_lager_bask_fi($row) {
//	global	$count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue, 
//	$est_delivery, $fi, $sv, $release, $fi, $sv;
	global $count, $fi, $sv;
	$release = substr($release, 0, 10);
	

	if (ereg("^frakt", $row->artnr))
		{ return null; }
	
	if ($row->bokad ==1) {
		if (!$sv)
			return "arastossa"; 
		else 
			return "finns i lager";	
	} else {

		# Kolla hur många det finns på kö
		# antal på köp visas i $queue, nollställes först. 
		$queue = 0;
		
		$queue = $this->check_queue($row->artnr, true, $row->ordernr);
		
		$neededStock = $queue + $count;
		//echo "här: " . $row->lagersaldo . "." . $count . "";
		if ($row->bestallt_fi >= $neededStock )  { 			
			return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($row->artnr, $count, $row->ordernr, true, $fi, $sv )	. "</font>";			
		} 
		elseif ( ($row->lagersaldo >= $count) )  { 				
			if (!$sv)
				return "keskusvarastossa"; 
			else 
				return "finns på centrallager";							
		} 
		elseif ($row->bestallt >= $neededStock) {
			return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($row->artnr, $count, $row->ordernr, false, true, $sv ) . "</font>";
		} else  { 
		//<a onMouseOver=\"return escape('')\" style=\"text-decoration: none\"> 
			if ($row->bestallningsgrans == 0)  {
			  
				// kolla om den är släppt ännu eller inte. 
				if ($row->release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($row->release) >= mktime()) {
						// TODO: få in att varan är en beställningsvara också. 
						if (!$sv) // todo "släpps på finska
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tulee markkinoille " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Släpps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
					} else { // då blir det vanlig beställningsvara. 
						if (!$sv) // TODO: finska nästa rad
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tilaustuote</font>";													
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">beställningsvara</font>";
					}
				} else {
					if (!$sv) // TODO: finska nästa rad
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tilaustuote</></font>";													
					else
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">beställningsvara</></font>";													
				}
								
			} else {
				 
				if ($row->release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($row->release) >= mktime()) {					
						if (!$sv) // TODO: släpps på finska
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tulee markkinoille " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Släpps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
					} else {
						if (!$sv) // TODO: finska på nästa rad
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillfälligt slut</font></a>" ;														
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillfälligt slut</font>" ;														
					}
				} else {
					if (!$sv) // TODO: finska på nästa rad
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillfälligt slut</font>" ;														
					else
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillfälligt slut</font>" ;														
				}
			}	
		}
	}         
	
}
function check_lager_bask_se($row) {
//	global	$count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue, 
//	$est_delivery, $fi, $sv, $release, $fi, $sv;
	global $count, $fi, $sv;
	$release = substr($row->release, 0, 10);
	//echo "här: " . $row->ordernr;

	if (ereg("^frakt", $row->artnr))
		{ return null; }
	
	if ($row->bokad ==1) {
			return "finns i lager";	
	} else {

		# Kolla hur många det finns på kö
		# antal på köp visas i $queue, nollställes först. 
		$queue = 0;
		
		$queue = $this->check_queue($row->artnr, false, $row->ordernr);
		
		$neededStock = $queue + $count;
		//echo "här: " . $row->lagersaldo . "." . $count . "";
		//echo $neededStock;
		if ($row->bestallt >= $neededStock )  { 			
			return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($row->artnr, $row->antal, $row->ordernr, false, $fi, $sv )	. "</font>";											
		} elseif ($row->bestallt >= $neededStock) {
			return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($row->artnr, $row->antal, $row->ordernr, false, $fi, $sv ) . "</font>";
		} else  { 
		//<a onMouseOver=\"return escape('')\" style=\"text-decoration: none\"> 
			if ($row->bestallningsgrans == 0)  {
			  
				// kolla om den är släppt ännu eller inte. 
				if ($row->release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($row->release) >= mktime()) {
						// TODO: få in att varan är en beställningsvara också. 
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Släpps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
					} else { // då blir det vanlig beställningsvara. 
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">beställningsvara</font>";
					}
				} else {
					return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">beställningsvara</></font>";													
				}
								
			} else {
				 
				if ($row->release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($row->release) >= mktime()) {					
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Släpps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
					} else {
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillfälligt slut</font>" ;														
					}
				} else {
					return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillfälligt slut</font>" ;														
				}
			}	
		}
	}         
	
}
function check_package_sv() {

	global $artnr, $count;
	$select = "SELECT lagersaldo, antal ";
	$select .= "FROM Artiklar, Paketpriser WHERE Artiklar.artnr=Paketpriser.artnr_del ";
	$select .= "AND Paketpriser.artnr_paket = '$artnr' ";
		
	$res = mysqli_query($this->conn_my, $select);
	
	unset ($check);

	while ($row = mysqli_fetch_array($res))
	{
	extract($row);

	$check = $antal*$count;



	  if ($check > $lagersaldo) {
	    return false;
	    break;
	  }
	  /*
	  else {
	    return false;
	  }
	  */
	}
	return true; 

}
function check_package_fi() {
	global $artnr, $count;

	$select = "SELECT lagersaldo_fi, antal ";
	$select .= "FROM Artiklar_fi, Paketpriser WHERE Artiklar_fi.artnr_fi=Paketpriser.artnr_del ";
	$select .= "AND Paketpriser.artnr_paket = '$artnr' ";

	$res = mysqli_query($this->conn_my, $select);
	
	unset ($check);

	while ($row = mysqli_fetch_array($res))
	{
	extract($row);

	$check = $antal*$count;



	  if ($check > $lagersaldo) {
	    return false;
	    break;
	  }
	  /*
	  else {
	    return false;
	  }
	  */
	}
	return true; 


}

function check_queue($artnr, $fi, $ordernr) {

	if (is_numeric($ordernr)) {
		if ($fi)
			$select = "SELECT antal FROM Orderposter_fi WHERE bokad = 0 && artnr = '$artnr' AND ordernr < " . $ordernr;
		else 	
			$select = "SELECT antal FROM Orderposter WHERE bokad = 0 && artnr = '$artnr' AND ordernr < " . $ordernr;		
	} else {
		if ($fi)
			$select = "SELECT antal FROM Orderposter_fi WHERE bokad = 0 && artnr = '$artnr' ";
		else 	
			$select = "SELECT antal FROM Orderposter WHERE bokad = 0 && artnr = '$artnr' ";		
	}
	
	
	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > 0) {	
		while ($row = mysqli_fetch_array($res) )   {
		extract ($row);
			$queue += $antal;

		}
	}
	else
		$queue = 0;
	
	return $queue;
}


function getArticleInfo2($artnr) {
	
	$select = "SELECT * FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query($this->conn_my, $select);
	$row = mysqli_fetch_object($res);
	return $row;
}

function getPrice($artnr) {
	
	$select = "SELECT utpris FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query($this->conn_my, $select);
	$row = mysqli_fetch_object($res);
	return $row->utpris;
}

function kollaLevtid2 ($artnr, $count, $ordernr, $fi)  { // artnr som skall kollas, antal samt inkommet datum på ordern
	global $fi;

	// kolla först inkommet för att få fram köplats. 
	// ange dagens datum om koll för kundvagnen 
	
	if ($ordernr == NULL) {
		$inkommetOur = date("Y-m-d H:i:s");
	}
	else {
		$select = "SELECT inkommet from Ordertabell WHERE ordernr = '$ordernr' ";

		$res = mysqli_query($this->conn_my, $select);

		if ($res)  {
			$row = mysqli_fetch_object($res);
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
	
	$res = mysqli_query($this->conn_my, $select);
	
	if (mysqli_num_rows($res) > '0') {
		
		while ($row = mysqli_fetch_array($res)):
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
	$res = mysqli_query($this->conn_my, $select);
	
	//echo $select;
	if (mysqli_num_rows($res) > '0') {
		
		while ($row = mysqli_fetch_array($res)): 
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

//-------------------------------------------------------------------------------------


function kollaLevtid ($artnr, $count, $ordernr, $table_fi, $fi, $sv)  { // artnr som skall kollas, antal samt inkommet datum på ordern
	//$fi avser i det här fallet om Orderposter_fi eller Orderposter skall användas. 
	
	// kolla först inkommet för att få fram köplats. 
	// ange dagens datum om koll för kundvagnen 	
	if ($ordernr == NULL) {
		$inkommetOur = date("Y-m-d H:i:s");
	}
	else {
		if ($fi)
			$select = "SELECT inkommet from Ordertabell_fi WHERE ordernr = '$ordernr' ";
		else 	
			$select = "SELECT inkommet from Ordertabell WHERE ordernr = '$ordernr' ";

		$res = mssql_query ($select, $this->conn_ms);

		if ($res)  {
			$row = mssql_fetch_object ($res);
			$inkommetOur = $row->inkommet;
		}
	}		
	//echo $inkommetOur;
	//$inkommetOur = strftime ("%m-%d-%Y %R", strtotime($inkommet));
	//echo "." . $inkommetOur;
	$totalNeed = 0;
	$ordernr = NULL; // tag bort värdet eftersom samma variable namn används nedan
	//echo $inkommetOur;	
	// räkna först ut hur många som står före
	if ($fi) {
		$select =  "SELECT Orderposter_fi.ordernr, Orderposter_fi.antal, Ordertabell_fi.inkommet FROM Orderposter_fi, Ordertabell_fi ";
		$select .= "WHERE Orderposter_fi.ordernr = Ordertabell_fi.ordernr AND ";
		$select .= "Orderposter_fi.artnr = '$artnr' AND Orderposter_fi.bokad = 0 ";
		$select .= "ORDER BY Ordertabell_fi.inkommet ASC";
	
	} else {
		$select =  "SELECT Orderposter.ordernr, Orderposter.antal, Ordertabell.inkommet FROM Orderposter, Ordertabell ";
		$select .= "WHERE Orderposter.ordernr = Ordertabell.ordernr AND ";
		$select .= "Orderposter.artnr = '$artnr' AND Orderposter.bokad = 0 AND inkommet < '" . date("Y-m-d H:i:s", strtotime($inkommetOur)) . "' ";
		$select .= "ORDER BY Ordertabell.inkommet ASC";	
	}
	
	//echo $select;
	//setlocale (LC_ALL, 'en_US');
	$res = mssql_query ($select, $this->conn_ms);
	
	if (mssql_num_rows ($res) > 0) {
		
		while ($row = mssql_fetch_array ($res)):
			extract ($row);
			//echo $inkommet . "<br>";
			//if (strtotime($inkommet) < strtotime($inkommetOur)) {
				//echo "<br>" . strtotime($inkommet) . ", " . strtotime($inkommetOur);
				$totalNeed += $antal; // antalet som står före.
				//echo "<br>$totalNeed, $ordernr<br>";
			//}
			//else
			//	break;
				
		endwhile;
		
	}
	
	$totalNeed += $count; // lägg på de vi behöver till totala behovet (för att det skall 
			      // nå fram till "vår" köplats)
	
	$antal_sum = 0;
	if ($table_fi) {
		$select = "SELECT inkopsnr, antal, levdatum, ber_lev_datum_fi as ber_lev_datum, prec_fi as prec, levererat FROM Inkopsposter_fi WHERE artnr = '$artnr' "; 
		$select .= "AND antal != levererat ";
		$select .= "ORDER BY inkopsnr ASC ";
		$res = mysqli_query($this->conn_my, $select);
			
	} else {
		$select = "SELECT inkopsnr, antal, levdatum, ber_lev_datum, prec, levererat FROM Inkopsposter WHERE artnr = '$artnr' "; 
		$select .= "AND antal != levererat ";
		$select .= "ORDER BY inkopsnr ASC ";
		$res = mysqli_query($this->conn_my, $select);
	}
	
	//echo $select;
	if (mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)): 
			extract ($row);
			$ber_lev_datum = substr($ber_lev_datum, 0, 10);
			$antal_sum += ($antal - $levererat);
			
			if ($antal_sum >= $totalNeed) {
				
				if ($ber_lev_datum == "" || $ber_lev_datum == "1900-01-01") {
						
					if ($levdatum != "" && $levdatum != "-") {
						
						if (!$sv) {
							$articleinfo = "<a onMouseOver=\"return escape('Datumet avser datum när varan <b>beräknas</b> komma in till vårt lager. Observera att detta är ungefärligt datum. ')\" style=\"text-decoration: none\">" . $levdatum . "</a>";					
						} else {
							$articleinfo = "<a onMouseOver=\"return escape('Datumet avser datum när varan <b>beräknas</b> komma in till vårt lager. Observera att detta är ungefärligt datum. ')\" style=\"text-decoration: none\">" . $levdatum . " </a>";											
						}						
					}
					
					elseif (!$sv)
						$articleinfo = "toimituspäivämäärä ei ole määritelty";
					else
						$articleinfo = "<a onMouseOver=\"return escape('Varan är beställd men leveranstiden är okänd. Vi har inte fått ett leveransbesked från vår leverantör. ')\" style=\"text-decoration: none\"> leveransdatum okänt</a>";
				} else {					
					//$prec = 1;
					//echo "här: " . $table_fi;
					if (!$table_fi && $prec == 1 && $fi) { // lägg på två dagar på leveranstiden om det är från finland men svenska levtiden kollas. 
						$ber_lev_datum = date("Y-m-d", (strtotime($ber_lev_datum . "+2 day")));					
					}
					if (!$sv) {
						$articleinfo = "<a onMouseOver=\"return escape('Leveranstiden avser när varan <b>beräknas</b> komma in till vårt lager. Observera att detta är en ungefärlig leveranstid. ')\" style=\"text-decoration: none\">" . $this->showDeliveryDate($ber_lev_datum, $prec, $fi, $sv) . "</a>";					
					} else {
						$articleinfo = "<a onMouseOver=\"return escape('Datumet avser datum när varan <b>beräknas</b> komma in till vårt lager. Observera att detta är ungefärligt datum. ')\" style=\"text-decoration: none\">" . $this->showDeliveryDate($ber_lev_datum, $prec, $fi, $sv) . " </a>";					
						
					}
				}
				
				break;
			}
		//echo "$antal_sum $totalNeed.$articleinfo<br>";
		$antal = $levererat = 0;
		endwhile;	
	
	}
	else
	
	{
	
		if (!$sv)
			$articleinfo = "toimituspäivämäärä ei ole määritelty";
		else
			$articleinfo = "<a onMouseOver=\"return escape('Varan är beställd men leveranstiden är okänd. Vi har inte fått ett leveransbesked från vår leverantör. ')\" style=\"text-decoration: none\"> leveransdatum okänt</a>";
	
	}


return $articleinfo;
}
function calcDate($date, $days) {
	$dat = strtotime($date) + 86400 * $days;
	return date("Y-m-d", $dat);
}

function showDeliveryDate($date, $prec, $fi, $sv) {
	//echo $date;
	$ret = "";
	if ($date != "")
		$date = substr($date, 0, 10);
		
	if ($date == "" || $date == "1900-01-01") {
		if ($fi && !$sv)
			return "<a onMouseOver=\"return escape('Varan är beställd men leveranstiden är okänd. Vi har inte fått ett leveransbesked från vår leverantör. ')\" style=\"text-decoration: none\"> leveransdatum okänt</a>";
		else 
			return "<a onMouseOver=\"return escape('Varan är beställd men leveranstiden är okänd. Vi har inte fått ett leveransbesked från vår leverantör. ')\" style=\"text-decoration: none\"> leveransdatum okänt</a>";
	}
	
	if ($prec == 1) { // exakt datum
		if ($fi) {
			return date("m-d-Y", strtotime($date));
		} else {
			return date("Y-m-d", strtotime($date));
		}
		return $date;
	} elseif ($prec == 2) {	// visas som vecka
		if ($fi && !$sv)
			return "Beräknas in vecka " .  strtolower (date("W", strtotime($date)));		
		else 	
			return "Beräknas in vecka " .  strtolower (date("W", strtotime($date)));		
			
	} elseif ($prec == 3) {	// del av månad
		$day = date("j", strtotime($date));
		$month = date("n", strtotime($date));

		if ($day > 0 && $day <= 10 ) {
			if ($fi && !$sv)
				return "Beräknas in i början av " . $this->getMonthFi($month);
			else 
				return "Beräknas in i början av " . $this->getMonthSv($month);
				
		} elseif ($day > 10 && $day <= 20) {
			if ($fi && !$sv)
				return "Beräknas in i mitten av " . $this->getMonthFi($month);			
			else 
				return "Beräknas in i mitten av " . $this->getMonthSv($month);
		} elseif ($day > 20) {
			if ($fi && !$sv)
				return "Beräknas in i slutet av " . $this->getMonthFi($month);
			else
				return "Beräknas in i slutet av " . $this->getMonthSv($month);
				
		} else { // tja, när är den något annat? Tomt blir nog bra
			return "";
		}
		
	} elseif ($prec == 4) { // månad
		if ($fi && !$sv)
			return "Beräknas in i " . strtolower ( date("F", strtotime($date)));		
		else 
			return "Beräknas in i " . strtolower ( date("F", strtotime($date)));		

	} else {
		return $date;
	}
	
}
function getMonthFi($month) {
	if ($month == 1)
		return "tammikuu";
	elseif ($month == 2)
		return "helmikuu";
	elseif ($month == 3)
		return "maaliskuu";
	elseif ($month == 4)
		return "huhtikuu";
	elseif ($month == 5)
		return "toukokuu";
	elseif ($month == 6)
		return "kesäkuu";
	elseif ($month == 7)
		return "heinäkuu";
	elseif ($month == 8)
		return "elokuu";
	elseif ($month == 9)
		return "syyskuu";
	elseif ($month == 10)
		return "lokakuu";
	elseif ($month == 11)
		return "marraskuu";
	elseif ($month == 12)
		return "joulukuu";
	else 
		return "";
	
	
	
}
function getMonthSv($month) {
	if ($month == 1)
		return "januari";
	elseif ($month == 2)
		return "februari";
	elseif ($month == 3)
		return "mars";
	elseif ($month == 4)
		return "april";
	elseif ($month == 5)
		return "maj";
	elseif ($month == 6)
		return "juni";
	elseif ($month == 7)
		return "juli";
	elseif ($month == 8)
		return "augusti";
	elseif ($month == 9)
		return "september";
	elseif ($month == 10)
		return "oktober";
	elseif ($month == 11)
		return "november";
	elseif ($month == 12)
		return "december";
	else 
		return "";
	
	
	
}
}
?>

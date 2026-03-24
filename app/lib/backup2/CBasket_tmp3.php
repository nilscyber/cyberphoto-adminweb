<?php

/*

PHP login object
author		Nils Kohlström
version		0.9 2006-07-14

*/

include("connections.php");

Class CBasket {
	var $conn_ms; var $conn_my; var $conn_fi;
	var $confirm; var $kundnrsave; var $ber_lev_datum; var $prec;
	var $completeOrder;
	var $includedArticles;
	
	var $freeFreight;
	var $basketValue;	
	
	var $mobilabonnemang;

function __construct() {
	//include ("CConnect_ms.php");
	//include ("CConnect.php");
	global $fi;
	/**
	if ($fi) {
		$this->conn_fi = @mssql_pconnect ("62.80.138.170", "apache", "aKatöms#1");
		@mssql_select_db ("cyberphoto", $this->conn_fi);
	}
	*/
        
	$this->conn_my = Db::getConnection();
	$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
	@mssql_select_db ("cyberphoto", $this->conn_ms);
	$this->conn_fi = $this->conn_ms;
	//echo "här: " . $this->conn_my;
	/**
	$this->conn_fi = $this->conn_fi;
	$this->conn_ms = $this->conn_ms;
	$this->conn_my = $this->conn_my;
	*/
	
	$this->mobilabonnemang = false;
}
function datebetweenInpris($datum) {
	$date = strtotime($datum); 
	$secs = time() - $date; 
	$days = $secs / 60 / 60 / 24; 
	$varde = ceil($days);
	echo $varde . " dagar";
	if ($varde > 182) { ?>
	<a onMouseOver="return escape('<b>Varningsklocka!</b><br><br>Inpriset på denna produkt är mer än ett halvår gammalt.<br><br>Bör kollas upp omgående!')">
	<?php
	echo "&nbsp;<b><font color='red'>** Varning! **</font></b></a>";
	}
} 
function checkSoldLastMonth($artnr) {
	
	
	$select = "SELECT antal FROM mostSoldArticlesLimited WHERE artnr = '" . $artnr . "' ";
	$res = mssql_query ($select, $this->conn_ms);
	$row = mssql_fetch_object($res);

	if (mssql_num_rows($res) > 0) {
		echo $row->antal . " st/mån";
	}
	else {
		echo "Inga sålda";
	}
}	
function addNyhetsbrev($name, $email, $fi) {
	global $fi, $sv;
	
	$email = trim($email);
	$s = "SELECT * FROM Nyhetsbrev WHERE email = '" . $email . "'";
	//echo $s;
	$res = @mssql_query ($s, $this->conn_ms);
	if ($fi && !$sv) {
		$lang = "fi";
		$cntry = "FI";
	} elseif ($fi && $sv) {
		$lang = "sv";
		$cntry = "FI";	
	} elseif ($sv) {
		$lang = "sv";
		$cntry = "SE";
	} else {
		$lang = "sv";
		$cntry = "SE";		
	}	
		
	if (@mssql_num_rows($res) == 0) {		
	    $s = "INSERT INTO Nyhetsbrev (email, name, dateAdded, confirmed, mailad, cntry, lang) values (" .
	            "'" . $email . "', '" . $name . "', '" . date("Y-m-d H:i:s")  . "', 0, 0, '" . $cntry . "', '" . $lang . "')";
	    //echo $s;	
	    @mssql_query($s);    
	}
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
	$freight, $pay, $fi, $sv, $old_villkor_id, $old_personnr, $old_firstName, $old_lastName, $old_firstNameDel, $old_lastNameDel, $old_foretag, $old_avisera, 
	$old_forsakringserbjudande, $old_splitOrder;
	

	$senasteLevsatt = 0; $senasteBetalsatt = 0;
	if ($confirm == 1) {

		// Plocka först fram kunduppgifterna

		$select  = "SELECT namn, co, adress, postnr, postadr, Kund.land_id, email, telnr, orgnr, mail_send, faktura, ";
		$select .= "lnamn, lco, ladress, lpostnr, lpostadr, lland_id, lemail, ltelnr, land, land_fi, erordernr, erref, faktlev, ";
		$select .= "savelogin, land_fi, foretag, userName, avtalskund, mobilnr, sms, nyhetsbrev, villkor_id, ";
		$select .= "firstName, lastName, firstNameDel, lastNameDel, electronic_invoice, forsakringserbjudande ";
		$select .= "FROM Kund LEFT JOIN Land ON Kund.land_id = Land.land_id ";
		$select .= "WHERE kundnr = $kundnrsave ";
			
		$res = mssql_query($select, $this->conn_ms);
		$row = mssql_fetch_array($res);

		// returns selected variabls prefixed $old_, e.g. $old_namn
		if ((mssql_num_rows($res)) > 0)
		extract($row, EXTR_PREFIX_ALL, "old");
		$old_orgnr = trim($old_orgnr);
		$old_personnr = $old_orgnr;
		//echo "." . strlen($old_electronic_invoice);
		//if ($old_electronic_invoice == 0 || $old_electronic_invoice == -1) {

		if (strlen($old_electronic_invoice) > 0) {
			$old_avisera = $old_electronic_invoice;			
			//echo "," . " " . $old_electronic_invoice;
		} else {
			$old_avisera = -1;	
			//echo ".";
		}
		//echo $kundnrsave;
		if ($old_forsakringserbjudande != 0 && $old_forsakringserbjudande != -1)
			$old_forsakringserbjudande = -1;
		
		if (!$sv)
			$old_land = $old_land_fi;
		// För gamla kunder som saknar leveransadress på sina ordrar.
		if (($kundrsave < 105138) AND ($old_lnamn == "" AND $old_lco == "" AND $old_ladress == "" AND $old_lpostnr == "" AND $old_lpostadr == "") )

		{
			$this->check_lev_adress($kundnrsave);

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
				//$pay = $this->getBets($senasteBetalsatt);
				//$freight = $this->getLevSatt($senasteLevsatt);
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
	$userName, $userTaken, $foretag, $new_userName, $new_nyhetsbrev, $new_mobilnr, $new_sms, $fi, $sv, $pay, $kreditor, 
	$new_firstName, $new_lastName, $new_firstNameDel, $new_lastNameDel, $new_personnr, $old_foretag, $new_salary, $old_salary, 
	$new_avisera, $new_forsakringserbjudande;
	
	$userTaken = "";
	//echo "här: " . $new_firstName;
		
	
	//echo "här företag: " . $old_foretag;
	//return;
	//if (($pay == "invoiceme" || eregi("avbetalning", $pay || $pay == "faktura_kampanj") || $this->mobilabonnemang) && isset($_SESSION['CKreditor']->cust_addresses) ) {
	if (($pay == "invoiceme" || eregi("avbetalning", $pay) || $this->mobilabonnemang || $pay == "faktura_kampanj") && isset($_SESSION['CKreditor']->cust_addresses) ) {
		$new_firstName = $_SESSION['CKreditor']->cust_addresses[0][0];
		$new_lastName = $_SESSION['CKreditor']->cust_addresses[0][1];
		$new_namn = $_SESSION['CKreditor']->cust_addresses[0][0] . " " . $_SESSION['CKreditor']->cust_addresses[0][1];
		$new_co = $_SESSION['CKreditor']->cust_addresses[0][2];
		//$new_adres = $_SESSION['CKreditor']->cust_addresses[0][1];
		$new_postnr = $_SESSION['CKreditor']->cust_addresses[0][3];			
		$new_postadr = $_SESSION['CKreditor']->cust_addresses[0][4];		 		 		
		
		
		$new_lco = $_SESSION['CKreditor']->cust_addresses[0][2];
		//$new_ladress = $_SESSION['CKreditor']->cust_addresses[0][1];
		$new_lpostnr = $_SESSION['CKreditor']->cust_addresses[0][3];			
		$new_lpostadr = $_SESSION['CKreditor']->cust_addresses[0][4];
		if ($fi) {
			$new_land = 'Finland';
			$new_lland = 'Finland';
		} else {
			$new_land = 'Sverige';
			$new_lland = 'Sverige';
		}		
		//$new_namn = $new_firstName . " " . $new_lastName;
		$new_firstNameDel = $new_firstName;
		$new_lastNameDel = $new_lastName;		
		$new_lnamn = $new_namn;
		if ($_SESSION['CKreditor']->cust_pno != "") {
				$new_personnr = $_SESSION['CKreditor']->cust_pno;
		}
	} elseif  (($pay == "invoiceme" || eregi("avbetalning", $pay) || $pay == "faktura_kampanj") && $fi)  {
		$new_namn = $new_firstName . " " . $new_lastName;
		/** Detta behövs inte, det läggs äändå ihop längre ner		
		$new_lco = $new_co;
		$new_lpostnr = $new_postnr;			
		$new_lpostadr = $new_postadr;
		$new_lland = $new_land;		
		*/
		$new_firstNameDel = $new_firstName;
		$new_lastNameDel = $new_lastName;	

	}

	if ($new_forsakringserbjudande == 'yes') {
		$new_forsakringserbjudande = -1;
	} else {
		$new_forsakringserbjudande = 0;
	}	
	$old_forsakringserbjudande = $new_forsakringserbjudande;
	
	// tag bort mellanslag före och efter på lösenord och användarnamn
	$userName = trim($userName);
	$new_userName = trim($new_userName);
	$new_passw = trim($new_passw);
	$change_passw = trim($change_passw);
	$new_namn = trim($new_namn);
	$new_lnamn = trim($new_lnamn);
	$new_mobilnr = trim($new_mobilnr);
	
	$new_salary = trim($new_salary);
	$old_salary = $new_salary;
	$new_personnr = trim($new_personnr);
	if ($old_foretag == 0)
		$new_orgnr = $new_personnr;
	
	if ($old_salary == "") $old_salary = 0;

	if ($fi && !$sv) {
		$lang = "fi";	$cntry = "FI";
		$currency = "EUR";
	} elseif ($fi && $sv) {
		$lang = "sv"; 	$cntry = "FI";	
		$currency = "EUR";
	} elseif ($sv) {
		$lang = "sv";	$cntry = "SE";
		$currency = "SEK";
	} else {
		$lang = "sv"; 	$cntry = "SE";		
		$currency = "SEK";
	}
	
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
			if ($foretag == "yes") {
				$foretaget = -1;
			} else {
				$foretaget = 0;
				$new_orgnr = trim($new_personnr);
			}
				
			if ($new_nyhetsbrev == "yes")
				$new_nyhetsbrev = -1;
			else
				$new_nyhetsbrev = 0;
			//temp
			//$new_nyhetsbrev = -1;
			
			if ($new_sms == "yes")
				$sms = -1;
			else
				$sms = 0;
			if ($new_passw == "")
				$new_passw = $this->generate_pass(5);
			//$select = "SELECT max(kundnr) as kundnr FROM Kund ";
			$select = "SELECT max(kundnr) as kundnr FROM Kundnr ";
			//echo $select;
			$row = (mssql_fetch_object(mssql_query($select, $this->conn_ms)));
			$newkundnr = $row->kundnr;
			$newkundnr++;
			$datumet = date("Y-m-d H:i:s");
			
			$insert = "INSERT INTO Kundnr (kundnr, usr, createdDate) values (" . $newkundnr . ", 'WO', '" . $datumet . "')";
			//echo $insert;
			$res2 = mssql_query ($insert, $this->conn_ms);

			$insert  = "INSERT INTO Kund (kundnr, namn, co, adress, postnr, postadr, land_id, email, telnr, orgnr, erref, erordernr, ";
			$insert .= "lnamn, lco, ladress, lpostadr, lpostnr, lland_id, kundid, lemail, ltelnr, faktlev, savelogin, userName, foretag, ";
			$insert .= " skapad, nyhetsbrev, mobilnr, sms, lang, firstName, lastName, firstNameDel, lastNameDel, forsakringserbjudande, ";
			if ($new_avisera != "") 
				$insert .= "electronic_invoice, ";			
			$insert .= " salary, currency) values ";
			$insert .= "";
			$insert .= " ('$newkundnr', '$new_namn', '$new_co', '$new_adress', ";
			$insert .= "'$new_postnr', '$new_postadr', '$land_id', '$new_email', '$new_telnr', '$new_orgnr', '$new_erref', '$new_erordernr', ";
			$insert .= "'$new_lnamn', '$new_lco', '$new_ladress', '$new_lpostadr', '$new_lpostnr', '$lland_id', '$new_passw', '$new_email', ";
			$insert .= "'$new_telnr', '$new_faktlev', '$spara', '$userName', $old_foretag, '$datumet', $new_nyhetsbrev, '$new_mobilnr', $sms, '$lang', ";
			$insert .= " '$new_firstName', '$new_lastName', '$new_firstNameDel', '$new_lastNameDel', $new_forsakringserbjudande, ";
			if ($new_avisera != "") 
				$insert .= "$new_avisera, ";

			$insert .= " $old_salary, '$currency')";

			//echo $insert;
			if ($res2)
				$res = mssql_query ($insert, $this->conn_ms);

			if ($res && $res2) {
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
			//temp
			//$new_nyhetsbrev = -1;
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
			$update .= "sms = $sms, " ;
			$update .= "lang = '$lang', ";
			//firstName, lastName, firstNameDel, lastNameDel
			$update .= "firstName = '$new_firstName', ";
			$update .= "lastName = '$new_lastName', ";
			$update .= "firstNameDel = '$new_firstNameDel', ";
			$update .= "forsakringserbjudande = " . $new_forsakringserbjudande . ", ";
			$update .= "lastNameDel = '$new_lastNameDel' ";			
			if ($old_salary > 0)
				$update .= ", salary = $old_salary ";
			if ($new_avisera != "") {
				$update .= ", electronic_invoice = " . $new_avisera ;
			}
			$update .= " WHERE kundnr = $kundnrsave";
			//echo "här: " . $new_avisera;
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
	if ($new_nyhetsbrev == -1)
		$this->addNyhetsbrev($new_namn, $new_email, $fi);

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
	      if ($betalsatt == 'kort') $betalsatt_id = 5; 
	      elseif ($betalsatt == 'netpay') $betalsatt_id = 7; 
	      elseif ($betalsatt == 'giro') $betalsatt_id = 1; 
	      elseif ($betalsatt == 'butik') $betalsatt_id = 8; 
	      elseif ($betalsatt == 'direktbetalning') $betalsatt_id = 10; 
	      elseif ( eregi("avbetalning", $betalsatt) || $betalsatt == "faktura_kampanj") $betalsatt_id = 7;
	      elseif ($betalsatt == 'invoiceme') $betalsatt_id = 13;
	      elseif ($betalsatt == 'foretagshyra') $betalsatt_id = 11;
	      elseif ($betalsatt == 'c') $betalsatt_id = 2;
	      else $betalsatt_id = 0; 
	      //foretagshyra
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
function getLeveranssatt_id ($fraktArtnr, $fi) {
	
	if ($fraktArtnr == "")
		return Null;
	$select = "SELECT * FROM Leveranssatt WHERE ";
	if (eregi("pf$", $fraktArtnr)) 		
		$select .= " artnrPf = '" . $fraktArtnr . "'";
	else 	
		$select .= " artnr = '" . $fraktArtnr . "'";
	$res = mysqli_query($this->conn_my, $select);
	$row = mysqli_fetch_object($res);	
	return $row->leveranssatt_id;		
	
	
	/**
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
	*/
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
		$pay = "avbetalning";
	elseif ($betalsatt_id == 1)
		$pay = "giro";
	elseif ($betalsatt_id == 8)
		$pay = "butik";
	elseif ($betalsatt_id == 13)
		$pay = "invoiceme";
	elseif ($betalsatt_id == 11)
		$pay = "foretagshyra";
	else
		$pay = "";
    
	return $pay;
	
}
function getBetalsattet ($betalsatt_id) {
	global $sv;
	
	$select = "SELECT betalsatt, betalsatt_fi FROM Betalsatt WHERE betalsatt_id = " . $betalsatt_id;
	$res = mssql_query ($select, $this->conn_ms);
	$row = mssql_fetch_object ($res);
	if (!$sv)
		return $row->betalsatt_fi;
	else
		return $row->betalsatt;
}

function getArticleInfo($artnr) {	
	
	$select = "SELECT * FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query($this->conn_my, $select);
	$row = mysqli_fetch_object($res);
	return $row;
}
function getArticleInfo_fi($artnr) {
	
	$select = "SELECT * FROM Artiklar, Artiklar_fi where Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.artnr = '$artnr' ";
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
	global $count, $lagersaldo, $lagersaldo_fi, $bestallt, $lev_datum, $lev_datum_norm, $lev_datum_norm_fi, $bestallningsgrans, $bestallningsgrans_fi, $fi, $sv, $bestallt_fi;		
	
	$select =  "SELECT Paketpriser.artnr_paket, Paketpriser.artnr_del, Paketpriser.antal, Artiklar.artnr, ";
	$select .= "Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar_fi.lagersaldo_fi, Artiklar.lagersaldo, Artiklar.beskrivning, ";
	$select .= "tillverkare, Artiklar.lagersaldo, Artiklar.bestallt, ";
	$select .= "lev_datum_fi, bestallningsgrans, bestallningsgrans_fi, lev_datum_norm, lev_datum_norm_fi, lagersaldo_fi, bestallt_fi, bestallt, ";
	$select .= "Artiklar.bestallningsgrans, Artiklar.lev_datum_normal ";
	$select .= "FROM Artiklar ";
	$select .= "INNER JOIN Paketpriser ON Artiklar.artnr = Paketpriser.artnr_del ";
	$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
	$select .= "WHERE Paketpriser.artnr_paket = '$artnr' ";

	/*
	$select =  "SELECT Paketpriser.artnr_paket, Paketpriser.artnr_del, Paketpriser.antal, Artiklar.artnr, ";
	$select .= "Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar_fi.lagersaldo_fi, Artiklar.lagersaldo, Artiklar.beskrivning, ";
	$select .= "tillverkare, Artiklar.lagersaldo, Artiklar.bestallt, Artiklar.lev_datum, ";
	$select .= "lev_datum_fi, bestallningsgrans, bestallningsgrans_fi, lev_datum_norm, lev_datum_norm_fi, lagersaldo_fi, bestallt_fi, bestallt, ";
	$select .= "Artiklar.bestallningsgrans, Artiklar.lev_datum_normal ";
	$select .= "FROM Artiklar, Paketpriser, Tillverkare LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi WHERE Artiklar.artnr = Paketpriser.artnr_del ";
	$select .= "AND Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "AND Paketpriser.artnr_paket = '$artnr' ";
	*/
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
//		echo $select;
	/* 	while ($row = mysqli_fetch_array($res) )   {
	extract ($row); */
	
	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)):
		$description = ""; $bestallt_fi = 0;
		extract($row);
//		echo $bestallt_fi;
		$count = $antal*$mangd;
		if ($tillverkare != '.')
			$description = $tillverkare . " ";

		if (!$sv && $beskrivning_fi != "")
			$description .= $beskrivning_fi;
		else 	
			$description .= $beskrivning;
		?>		
		<tr>
		  <td bgcolor="#F0F0F0"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php echo $description; ?></font></td>
		  <td bgcolor="#F0F0F0" align="center"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php echo $antal*$mangd; ?></font></td>
		  <td bgcolor="#F0F0F0"><font color="#2B2B2B" face="Verdana, Arial" size="1">
		<?php	
		//($artnr, $count, NULL);
		//echo $artnr;
		$count = $antal * $mangd;
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
function generate_pass_all ($pass_len)  {
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

	if (!eregi("\s", $c)) {
		$nps = $nps.$c;
		// förenkla för kunden genom att bara använda små bokstäver
		//$nps = strtolower($nps);
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
	$PHP_SELF, $bestallningsgrans, $pack, $fi, $sv, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $artnr2, $count, $select, $release;	
	
	$count = 1;
	/**
	if ($fi) {
		// Get currency for EURO
		$select_valuta = "SELECT kurs FROM ValutaNew WHERE valutakod = 'EUR'";
		$res_valuta = mysqli_query($select_valuta);
		$valuta = mysqli_fetch_object($res_valuta);
	}
	*/

	$select = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.utpris, Artiklar_fi.utpris_fi, Tillverkare.tillverkare, ";
	$select .= "Artiklar.no_buy, Artiklar.lagersaldo, Artiklar.releaseDate, Artiklar_fi.lagersaldo_fi, Artiklar.bestallt, Artiklar_fi.bestallt_fi, Artiklar.kommentar,  ";
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
	$select .= "AND Artiklar.ej_med=0 && Artiklar.artnr='$artnr' ";

        $select  = "SELECT Artiklar.artnr, Artiklar_fi.artnr_fi, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.utpris, Artiklar_fi.utpris_fi, Tillverkare.tillverkare, ";
        $select .= "Artiklar.datum_utpris, Artiklar.datum_inpris, Artiklar.kategori_id, Artiklar.kortinfo, ";
        $select .= "Artiklar.no_buy, Artiklar.lagersaldo, Artiklar_fi.lagersaldo_fi, Artiklar.bestallt, Artiklar_fi.bestallt_fi, Artiklar.kommentar,  ";
        $select .= "Artiklar_fi.kommentar_fi, Artiklar.lev_datum_norm, Artiklar_fi.lev_datum_norm_fi, Artiklar.releaseDate, ";                                              
        $select .= "Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.lev_datum_normal, Moms.momssats, Moms.momssats_fi, Artiklar.utgangen, Artiklar_fi.utgangen_fi ";
        $select .= "FROM Artiklar INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
        $select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
        $select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
        $select .= "WHERE Artiklar.tillverkar_id=Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id ";
        $select .= "AND Artiklar.artnr='$artnr' ";


	if ($fi) 
		//$select .= " AND Artiklar_fi.ej_med_fi=0 AND (utgangen_fi=0 OR lagersaldo_fi > 0 OR lagersaldo > 0 )  ";
		$select .= " AND Artiklar_fi.ej_med_fi=0 AND (utgangen=0 OR lagersaldo > 0 )  ";
	else
		$select .= " AND Artiklar.ej_med=0 AND (utgangen=0 OR lagersaldo > 0) ";

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
	$release = $rows->releaseDate;
	$artnr2 = $rows->artnr;
	return $rows;
	
}
function getDiscountText($rowDiscount, $momssats, $utpris, $showDecoration) {
	global $fi, $sv, $count;
	
	$txt = "";
	if ($fi) {
		if ($rowDiscount->discountPercent_fi != "") {		
			return round($rowDiscount->discountPercent_fi * 100, 0) .  "% rabatt avdragen";
		} elseif ($rowDiscount->discountAmount_fi != "") {
			$utpris = $utpris - $rowDiscount->discountAmount_fi;
		} elseif ($rowDiscount->discountOutprice_fi != "") {
			$utpris = $rowDiscount->discountOutprice_fi;
		}		
	} else {
		if ($rowDiscount->discountPercent != "") {
			$txt = round($rowDiscount->discountPercent * 100, 0) .  "% avdraget enligt rabattkod";
		} elseif ($rowDiscount->discountAmount != "") {
			$txt = round( ($rowDiscount->discountAmount + $rowDiscount->discountAmount * $momssats)*$count, 0) .  " kr avdraget enligt rabattkod";
		} elseif ($rowDiscount->discountOutprice != "") {
			//echo "här";
			$txt = "nytt utpris enligt rabattkod. Ordinarie pris: " . round($utpris + $utpris * $momssats, 0) . " kr";
			//$txt = "nytt utpris enligt kupong. Ordinarie pris: ";
			//$txt .= $utpris + $utpris * $momssats . " SEK";
			//echo $txt;
		}		
	}
	if ($txt != "") {
		if ($showDecoration)
			// $txt = "<br><b><font face=\"Verdana, Arial\" color=\"#85000D\" size=\"1\">(" . $txt . ")</font></b>";
			$txt = "<b><font face=\"Verdana, Arial\" color=\"#85000D\" size=\"1\">(" . $txt . ")</font></b>";
		else 	
			$txt = "(" . $txt . ")";
	}
	return $txt;		
}
function getDiscountPrice($rowDiscount, $artnr, $kategori_id, $tillverkar_id, $utpris) {
	//echo $utpris;'
	global $fi, $count;
	if ($rowDiscount->artnr != "") {
		if ($artnr != $rowDiscount->artnr) {
			return $utpris;
		}		
	} elseif ($rowDiscount->tillverkar_id != "") {
		if ($rowDiscount->kategori_id != "") {
			if ( ! ($rowDiscount->kategori_id == $kategori_id && $tillverkar_id == $rowDiscount->tillverkar_id)) {
				return $utpris;				
			}
			else 
				;
		} else {
			if ($tillverkar_id != $rowDiscount->tillverkar_id)
				return $utpris;
		}
	} elseif ($rowDiscount->kategori_id != "" ) {
		if ($rowDiscount->kategori_id != $kategori_id)
			return $utpris;
	} else {
		return $utpris;
	}
	if ($fi) {
		if ($rowDiscount->discountPercent_fi != "") {
			$utpris = $utpris - $utpris * $rowDiscount->discountPercent_fi;
		} elseif ($rowDiscount->discountAmount_fi != "") {
			$utpris = $utpris - $rowDiscount->discountAmount_fi;
		} elseif ($rowDiscount->discountOutprice_fi != "") {
			$utpris = $rowDiscount->discountOutprice_fi;
		}		
	} else {
		if ($rowDiscount->discountPercent != "") {
			$utpris = $utpris - $utpris * $rowDiscount->discountPercent;
		} elseif ($rowDiscount->discountAmount != "") {
			$utpris = $utpris - $rowDiscount->discountAmount;
		} elseif ($rowDiscount->discountOutprice != "") {
			$utpris = $rowDiscount->discountOutprice;
		}		
	}

	return $utpris;
}

function obsafe_print_r($var, $return = false, $html = false, $level = 0) {
    if (!is_array($var)) return false;
    $spaces = "";
    $space = $html ? "&nbsp;" : " ";
    $newline = $html ? "<br />" : "\n";
    for ($i = 1; $i <= 6; $i++) {
        $spaces .= $space;
    }
    $tabs = $spaces;
    for ($i = 1; $i <= $level; $i++) {
        $tabs .= $spaces;
    }
    if (is_array($var)) {
        $title = "Array";
    } elseif (is_object($var)) {
        $title = get_class($var)." Object";
    }
    $output = $title . $newline . $newline;
    foreach($var as $key => $value) {
        if (is_array($value) || is_object($value)) {
            $level++;
            $value = $this->obsafe_print_r($value, true, $html, $level);
            $level--;
        }
        $output .= $tabs . "[" . $key . "] => " . $value . $newline;
    }
    if ($return) return $output;
      else echo $output;
}
function array_search_r($needle, $haystack){
    foreach($haystack as $value){
        if(is_array($value))
            $match=$this->array_search_r($needle, $value);
        if($value==$needle)
            $match=1;
        if($match)
            return true;
    }
    return false;
} 
function array_search_recursive($needle, $haystack, $a=0, $nodes_temp=array()){
global $nodes_found;
  $a++;
  foreach ($haystack as $key1=>$value1) {
    $nodes_temp[$a] = $key1;
    if (is_array($value1)){   
      $this->array_search_recursive($needle, $value1, $a, $nodes_temp);
    }
    else if ($value1 === $needle){
      $nodes_found[] = $nodes_temp[$a];
    }
  }
  return $nodes_found;
}
/**
 * search for a value ($needle) in a two-dimensioned array ($haystack) in specified column
 * returns false if not found, returns the found array on success. 
 *
 * @param variable $needle
 * @param array $haystack
 * @param integer $column
 * @return false if not found array on success
 */
function search_array_n($needle, $haystack, $column) {
	if (count($haystack) == 0)
		return false;
	$i = 0;
	foreach ($haystack as $straw) {
		if ($straw[$column] == $needle) {
			return $straw;
		}
		$i += 1;
	}
}
function getDiscountArticles2($articles, $fi) {
	if (eregi("pac$", $artnr))  {
		$select = "SELECT artnr_del as artnr FROM Paketpriser WHERE artnr_paket = '" . $artnr . "'";
		//echo "<br>" . $select .  ": " . "" . "<br>";
		$res = mysqli_query($this->conn_my, $select);
		if (mysqli_num_rows($res) > 0) {
			//echo "jo!";
		}
		while ($row = mysqli_fetch_object($res)) {
			getDiscounts($articles, $fi);
		}
	} else {
	
	}

}
	/**
	 * get discount for combination of articles
	 * viewItemsInBasket must be run before this one! 
	 * 
	 * @param String $articles (array of articles to be tested)
	 * @param boolean $fi (
	 * @param double $discountPercent // total discount percent
	 * @param double $discountAmount // total of discount amount
	 * @param array $discounts // array of discounts // id | description | discount amount | discount percent | tax code | article to receive discount as boolean | article included for free as boolean | used as boolean
	 * @return void
	 */

function getDiscountArticles($articles, $fi) {
	global $fi, $sv, $freight;
	
	if (count($articles) < 1)
		return 0;

	$articles2 = $articles; // spara en kopia av articles för senare användning
	$testVar = true;
	$dscntIds = array();
	$minCnt = -1;
	/**
	$f = $this->search_array_n("ixus70", $articles2, 0);
	if ($f === false) {
		echo "falskt";
	} else {
	echo "visa: " . $f[0] . ":" . $f[1];
	}
	return;
	*/
	//$this->obsafe_print_r($articles, false, true);
	//exit;
	$i = 0;
	foreach ($articles as $article)	{
		$i++;
		//$this->obsafe_print_r($article, false, true);

		if (count($dscntIds) > 0) {
			$notSel = "";
			foreach ($dscntIds as $dscntId) {
				$notSel .= " AND dscntId != " . $dscntId;
			}
		}

		$select = "SELECT discountCodes.*, discountCodeArticle.artnr as artnrMatch, discountCodeArticle.articleDiscounted FROM discountCodes LEFT JOIN discountCodeArticle ON discountCodeArticle.dscntId = discountCodes.cnt " . 
		" WHERE  (" . 
		" (discountCodeArticle.artnr = '" . $article[0] . "' ) OR  (" .
		" (discountCodes.tillverkar_id =  " . $article[2] . " ) OR " .
		" (discountCodes.artnr =  '" . $article[0] . "' ) OR " .
		" (discountCodes.kategori_id =  " . $article[3] . " ) ";
		if ($this->basketValue > 0 && $tjosan == "hejsan") { // används inte nu då vi inte vet hur vi ska använda totalsumman. Däremot används siffran när det gäller fri frakt
			if ($fi) {
				$select .= "OR (discountCodes.totalSum_fi > " . $this->basketValue . " AND discountCodes.totalSum_fi is not null) ";
			} else {
				$select .= "OR (discountCodes.totalSum > " . $this->basketValue . " AND discountCodes.totalSum is not null ) ";
			}
		}
		if ($discountCode != "") {
			//$select .= " OR (discountCodes.discountCode = '" . $discountCode ."' )";
		}
		if ($this->basketValue > 0  && $tjosan == "hejsan") {
			if ($fi)
				$select .= " OR (discountCodes.freeFreight = -1 AND (discountCodes.totalSum_fi > " . $this->basketValue . " OR discountCodes.totalSum_fi is null OR  discountCodes.totalSum_fi = 0) )";
			else				
				$select .= " OR (discountCodes.freeFreight = -1 AND (discountCodes.totalSum > " . $this->basketValue . " OR discountCodes.totalSum is null OR  discountCodes.totalSum = 0) )";
		}
		$select .= ") ) "; 

		$select .= " AND validDate > now() AND validFrom < now() " . $notSel;
		if ($fi)
			$select .= " AND active_fi = -1";
		else
			$select .= " AND active_se = -1";

		//echo $i . "<p><br><BR><BR>" . $select . "<br>";// .  ": " . $article[0] . "<br>";
		//exit; 
		//EL-26385
		$res = mysqli_query($this->conn_my, $select);
		//echo "<p>ant rad: " . mysqli_num_rows($res) . "<p>";
		if (mysqli_num_rows($res) > 0) {
			
			
			//$row = mysqli_fetch_object($res);
			//print_r($row);
			while ($row = mysqli_fetch_object($res)) {
			
				if ( ($row->freeFreight == -1 && ($row->discountCodes.totalSum > $this->basketValue || $row->discountCodes.totalSum == 0 || $row->discountCodes.totalSum == "" ) && !$fi ) ||
					($row->freeFreight == -1 && ($row->discountCodes.totalSum > $this->basketValue || $row->discountCodes.totalSum_fi == 0 || $row->discountCodes.totalSum_fi == "" ) && $fi )) { // om det avser fri frakt så sätts den här och då fortsätter inte processen. 
					//echo "här: " . $freight;
					if (eregi("^fraktpost", $freight)) {
						$this->freeFreight = true;						
					}
					$testVar = false;
				} else {
					// tag fram tillhörande artiklar till den du söker information om 
					$select = "SELECT * FROM discountCodeArticle WHERE dscntId = " . $row->cnt;
					$res2 = mysqli_query($this->conn_my, $select);
					
					// kontrollera om det finns något att hämta
					if (mysqli_num_rows($res2) == 0 ) {
						$testVar = false;
						//echo "falskt!";
					}
					if ($fi && !$sv) {
						if ($row->descrptn_fi != "")
							$descrptn = $row->descrptn_fi;
						else
							$descrptn = $row->descrptn;
					} elseif ($fi && $sv) {
						if ($row->descrptn_fi_sv != "")
							$descrptn = $row->descrptn_fi_sv;
						elseif ($row->descprtn_fi != "")
							$descrptn = $row->descrptn_fi;
						else
							$descrptn = $row->descrptn;

					} else {
						$descrptn = $row->descrptn;
					}

					$articleDiscounted = ""; // ?
					$includedForFree = ""; // ?
					while ($rowDiscount = mysqli_fetch_object($res2)) {	
						
						//echo "<br>testartnr: "	. $rowDiscount->artnr . "<br>";
						// kontrollera om artikeln som kommer upp finns i arrayen av våra artiklar
						//$f = $this->search_array_n($rowDiscount->artnr, $articles2, 0); // varför matchar den inte sig själv här? (jo det gör den men det spelar ingen roll 
														// då det ändå testas nedanför om det är den artikel som skall rabatteras som kommit upp. 
														// Och det sparas också så inga dubletter skapas. 

						// ovanstående görs inte längre då det testas redan i första frågan
						$f = true;

						if ($f === false) {
							//echo "falskt!!";
							$testVar = false;
						} else { // 
							//echo "<p>testet: " . $articles[$f][1];
							//echo "<p>testet: " . $f[0];
							//echo "<p>härs: " .  $articles2[$f[0]][0] . ":" .  $articles2[$f[0]][1];
							
							
							if ($rowDiscount->articleDiscounted == -1) {      // om det är en rabatterad vara, märk upp vilken spara											     
								$articleDiscounted = $rowDiscount->artnr; // vilken i $articleDiscounted
								//echo "yes!" . $rowDiscount->artnr;
								// här, om det är en vara som skall vara med, spara det i en egen array
							} elseif ($rowDiscount->includedArticles_id != "") {  // annars, om det är en vara som skall ingå gratis, 
								//$includedForFree = $rowDiscount->artnr;   // spara det i $includedForFree
								// sparas i en array $includedArticles: artnr produkten | $artnr på det som skickas med | beskrivning som syns i kundvagnen
								//discountCodeIncludedArticles
								$sel =  "SELECT discountCodeIncludedArticles.*, Artiklar.lagersaldo, Artiklar.utgangen, Artiklar.ej_med FROM discountCodeIncludedArticles, Artiklar WHERE " . 
									"discountCodeIncludedArticles.includedArticles = Artiklar.artnr AND discountCodeIncludedArticles.includedArticles_id = " . $rowDiscount->includedArticles_id;
								//echo $sel;
								//exit;
								//$sel = "SELECT lagersaldo, utgangen FROM Artiklar WHERE artnr = '" . $rowDiscount->includedArticle . "'";
								//$rw = mysqli_fetch_object(mysqli_query($this->conn_my, $sel));
								//echo "<p>här: " . $rw->utgangen . "<br>" . $rw->lagersaldo . "<br>" . $rowDiscount->includedArticle;
								$rs = (mysqli_query($this->conn_my, $sel));
								unset($includedArtsTmp);
								if (mysqli_num_rows($rs) > 0 ) 
									$includedArtsTmp = array();
								$i = 0;
								while ($rw = mysqli_fetch_array($rs)) {
									array_push($includedArtsTmp, $rw);
									
									if (($includedArtsTmp[$i]['lagersaldo'] >= $includedArtsTmp[$i]['qty'] || $includedArtsTmp[$i]['utgangen'] == 0 ) && 
										 ($includedArtsTmp[$i]['lagersaldo'] > 0 || 
										$includedArtsTmp[$i]['allowNoStock'] == -1)) {
										//$includedArtsTmp[$i]['ej_med'] == 0 &&
										//echo "lagersaldo " . $includedArtsTmp[$i]['lagersaldo'];
									} else {
										unset($includedArtsTmp);
										break;
									}
									$i++;

								}
								//$this->obsafe_print_r($includedArtsTmp, false, true);
								if (is_array($includedArtsTmp)) {
									if (!is_array($this->includedArticles))
										$this->includedArticles = array();
									unset($arr);
									$arr = array();
									unset($arr2);
									$arr2 = array();
									array_push($arr, $article[0]);


									foreach ($includedArtsTmp as $includedArtsTm) {
										//echo "här: " . $includedArtsTm[0] . "<p>";´
										array_push($arr2, $includedArtsTm['includedArticles']);
										//array_push($arr2, $includedArtsTm['qty']);

										$testVar = false; // inget annat skall göras då så stoppar fortsättningen med denna variabel. 											
									}
									array_push($arr, $arr2);								
									array_push($arr, $descrptn);
									
									array_push($this->includedArticles, $arr);

									//echo "färdiga resultatet: <br>";

									//$this->obsafe_print_r($this->includedArticles, false, true);
									//echo "<p>slut<p>";


									$testVar = false;
								} else {
									$testVar = false;
								}
							} else {
								//$testVar = false;
							}
							/**	
							det här var tidigare, men det är borttaget nu. Sätt 100% i rabatten för att få samma funktionalitet. 
							} elseif ($rowDiscount->includedForFree == -1) {  // annars, om det är en vara som skall ingå gratis, 
								$includedForFree = $rowDiscount->artnr;   // spara det i $includedForFree
							}*/

							if ($minCnt > $f[1] || $minCnt < 0) // spara det minsta gemensamma antalet av rabatterade artiklar
								$minCnt = $f[1];						
							//echo "<p>härs: " . $minCnt;
						}
					}
				}
				
				if ($testVar) {
					//echo "kollar: " . $row->cnt;
					if ($row->discountAmount > 0) { // här sparas totala rabatterade summan i klassen
						if ($row->allowMultiple == -1 && $article[1] > 0) // om det tillåts fler än en så gångras det med minsta gemensamma nämnaren i antal
							$this->discountAmount += $row->discountAmount*$minCnt;
						else 
							$this->discountAmount += $row->discountAmount;
					}
					if ($row->discountPercent > 0) // här sparas procentuella rabatten. Den har nog ingen funktion egentligen. 
						$this->discountPercent = $row->discountPercent;
					// save array of successful ids to avoid duplicates
					unset($discountParts);
					$discountParts = array();
					array_push($discountParts, $row->cnt); // räknare
					array_push($discountParts, $descrptn); // beskrivningstext av rabatten (visas för kunden)
					array_push($discountParts, $articleDiscounted);	// artikelnr på den artikel som skall få rabatten
					if ($row->allowMultiple == -1 && $article[1] > 0)
						array_push($discountParts, $row->discountAmount*$minCnt); //sparar rabatterade summan
					else 
						array_push($discountParts, $row->discountAmount);																
					array_push($discountParts, $row->discountPercent); // rabatt i procent
					array_push($discountParts, $row->taxCode); 	// momskod på rabatten
					array_push($discountParts, $articleDiscounted); // samma, varfrö
					//array_push($discountParts, $includedForFree); 	// används ej, används istället i $this->includedArticles. Alternativt använd istället 100% i rabatt
					//						// men funktionen var tänkt att
					array_push($discountParts, false); 		// om raden är använd
					array_push($this->discounts, $discountParts); // (slå ihop)
					
					array_push($dscntIds, $row->cnt); // skapa en lista på de rader vi redan använt i tabellen discountCodes
					//$this->obsafe_print_r($discounts, false, true);
					// $this->discounts räknare | beskrivning | artikelnr (som får rabatten) | summa rabatt | procent rabatt | moms_id | samma artikelnr (vet ej varför) | om den är använd ännu
					//			0	  1		2		3				4		5		6			   7
				}
				$minCnt = -1;
				$testVar = true;
			}
		}
	}
	//echo "\n<br>procent: " . $discountPercent;
	//echo "\n<br>summma: " . &$discountAmount;	
//print_r($this->includedArticles);
}


// används ej längre
function getIncludedArticle($artnr) {
	
	//$f = $this->search_array_n($rowDiscount->artnr, $articles2, 0); 
	$f = $this->search_array_n($artnr, $this->includedArticles , 0);
	//echo $artnr;
	//print_r($this->includedArticles);
	if ($f === false) {
		
		return false;
		
	} else {
		//echo "nej" . $f[2];
		return $f; // ändra här till att ta fram artikelbeskrivning fårn $f[1] (=artnr) om det skulle önskas. 
	}

}
function getIncludedArticles($artnr) {
	
	//$f = $this->search_array_n($rowDiscount->artnr, $articles2, 0); 
	$f = $this->search_array_n($artnr, $this->includedArticles , 0);
	//echo $artnr;
	//print_r($this->includedArticles);
	if ($f === false) {
		
		return false;
		
	} else {
		//echo "nej" . $f[2];
		return $f[1]; // ändra här till att ta fram artikelbeskrivning fårn $f[1] (=artnr) om det skulle önskas. 
	}

}
// används ej
function getIncludedArticleOld($artnr) {
	return "";
	if (eregi("pac$", $artnr))  {
		$select = "SELECT artnr_del as artnr FROM Paketpriser WHERE artnr_paket = '" . $artnr . "'";
		//echo "<br>" . $select .  ": " . "" . "<br>";
		$res = mysqli_query($this->conn_my, $select);
		if (mysqli_num_rows($res) > 0) {
			//echo "jo!";
		}
		while ($row = mysqli_fetch_object($res)) {
			$select = "SELECT discountCodes.* FROM discountCodes  
			WHERE discountCodes.artnr = '" . $row->artnr . "' AND validDate > getdate() ";
			//echo "<br>" . $select .  ": " . $artnr . "<br>";
			$res2 = mssql_query($select, $this->conn_ms);

			if (mssql_num_rows($res2) > 0) {
				//echo "japp!";
				$row = mssql_fetch_object($res2);

				$arr = array();
				array_push($arr, $artnr);
				array_push($arr, $row->includedArticle);
				array_push($arr, $row->dscrptn);
				if (!is_array($this->includedArticles))
					$this->includedArticles = array();
				array_push($this->includedArticles, $arr);
				$beskrivning .= $row->dscrptn;
				//print_r($this->includedArticles);

			}			
		}
	} else {
	
		$select = "SELECT discountCodes.* FROM discountCodes  
		WHERE discountCodes.artnr = '" . $artnr . "' AND validDate > getdate() ";
		//echo "<br>" . $select .  ": " . $artnr . "<br>";
		$res = mssql_query($select, $this->conn_ms);

		if (mssql_num_rows($res) > 0) {
			//echo "japp!";
			$row = mssql_fetch_object($res);

			$arr = array();
			array_push($arr, $artnr);
			array_push($arr, $row->includedArticle);
			array_push($arr, $row->dscrptn);
			if (!is_array($this->includedArticles))
				$this->includedArticles = array();
			array_push($this->includedArticles, $arr);
			$beskrivning .= $row->dscrptn;
			//print_r($this->includedArticles);

		}
	}
	return $beskrivning;

	
	// här plockas fram om någon artikel har en kampanj där något skall skickas med på köpet
	// skiljer sig från det som finns i tabellen discountArticles där kunde måste lagt varan i kundvagnen
	// själv. Fältet i discountCodes skickas det alltid med. 
	// sparas i en array $includedArticles: artnr produkten | $artnr på det som skickas med | beskrivning som syns i kundvagnen
	/**
	if ($row->includedArticle != "") {
		$arr = array();
		array_push($article[0]);
		array_push($row->includedArticle);
		array_push($row->dscrptn);
		array_push($includedArticles, $arr);
	}*/

}
function viewItemsInBasket($firstbasket, $fi) {
	global $kundvagn, $goodsvalue, $utpris, $goodsvalueMoms, $moms, $artnr, $count, $lagersaldo, $lagersaldo_fi, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans, $betalsatt, 
	$PHP_SELF, $brev, $bestallningsgrans, $pack, $alltidBrev, $pallDelivery, $fi, $sv, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $release, $discountCode, $discountCodeStatus, $freight, $pay, $articles, $discountAmount;	
	
	$freight_check = NULL;
	$pallDelivery = false; // visar om någon produkt innehåller frakttillägg. Används bl.a. till att tyngre produkter inte skall skickas som hempkaet
	$brev = true;
	if ($firstbasket == 'nooutput') 
		$this->mobilabonnemang = false;
	$alltidBrev = false;	
	# Get the cookie kundvagn
	$answers = $kundvagn;	
	// plocka fram info om rabattkoder om det finns någon
		
	//echo "här" . $basketValue;
	if (!(eregi("fraktbutik", $artnr)) && $firstbasket != 'nooutput' && !(eregi("presentkort", $artnr)) ) 
		$this->completeOrder = true;
	
	if ($discountCode == "UJ07" && $this->basketValue < 801) {
			$select = "SELECT * FROM discountCodes WHERE discountCode = '" . $discountCode . "'";
			$res = mssql_query($select, $this->conn_ms);
			$rowDiscount = mssql_fetch_object($res);		
			if (mssql_num_rows($res) > 0) {			
				if (strtotime("now") < strtotime($rowDiscount->validDate) + 84600)				
					$discountCodeStatus = 1;
				else 
					$discountCodeStatus = 2;
			} else 	{
				
				$discountCodeStatus = 0;
			}	
			$rowDiscount = null;
	/**
	} elseif ($freight == "fraktpost" && $pay == "pf" && $discountCode == "UJ07") {
		$discountCode = "UJ07PF";
		if ($discountCode != "") {
			$select = "SELECT * FROM discountCodes WHERE discountCode = '" . $discountCode . "'";
			$res = mssql_query($select, $this->conn_ms);
			$rowDiscount = mssql_fetch_object($res);		
			if (mssql_num_rows($res) > 0) {			
				if (strtotime("now") < strtotime($rowDiscount->validDate) + 84600)				
					$discountCodeStatus = 1;
				else 
					$discountCodeStatus = 2;
			} else 	{
				
				$discountCodeStatus = 0;
			}		
		}
		$discountCode = "UJ07";		
	*/
	} else {
		
		if ($discountCode != "") {
			$select = "SELECT * FROM discountCodes WHERE discountCode = '" . $discountCode . "'";
			$res = mssql_query($select, $this->conn_ms);
			$rowDiscount = mssql_fetch_object($res);		
			if (mssql_num_rows($res) > 0) {			
				// if (strtotime("now") < strtotime($rowDiscount->validDate) + 84600)				
				if (strtotime("now") < strtotime($rowDiscount->validDate))				
					$discountCodeStatus = 1; // valid
				else 
					$discountCodeStatus = 2; // utgått
			} else 	{
				
				$discountCodeStatus = 0; // saknas
			}		
		}

	}
	
	if (ereg ("(grejor:)(.*)", $answers,$matches)) {
		# Split the number of items and article id s into a list
		$orderlista = $matches[2];
		$argument = split ("\|", $orderlista);
	}

	$goodscounter=0;
	$goodsvalue=0;
	$goodsvalueMoms=0;
	$moms = 0;
	
	$articles = array();
	$n = count($argument);
	$j = 0;
	for ($i=0; ($i < $n);  $i+=2) {

		$arg = $argument[$i];        # Article id
		$count = $argument[$i+1];    # Keeps track of the number of the same article
		
		if ($this->freeFreight && eregi("^frakt", $arg) ) {		
			$arg = "fraktfritt";
			$count = 1;
		}

		//if ($fi) {
			$select  = "SELECT artnr_fi as artnr, Artiklar.lagersaldo, Artiklar.beskrivning, Artiklar.bestallt, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, utpris, tillverkare, ";
			$select .= "frakt, lagersaldo_fi, bestallt_fi, Artiklar.releaseDate, Artiklar.kategori_id, Artiklar.tillverkar_id,  ";
			$select .= "lev_datum_fi, bestallningsgrans, lev_datum_norm, lev_datum_norm_fi, Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, ";
			$select .= "Moms.momssats_fi, Moms.momssats, Artiklar.link, link2_fi ";
			$select .= " FROM Artiklar, Tillverkare, Kategori, Moms LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
			$select .= "WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id ";
			$select .= "AND artnr='$arg'";

            $select  = "SELECT artnr_fi as artnr, Artiklar.lagersaldo, Artiklar.beskrivning, Artiklar.bestallt, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, utpris, tillverkare, ";
            $select .= "frakt, lagersaldo_fi, bestallt_fi, Artiklar.releaseDate, Artiklar.kategori_id, Artiklar.tillverkar_id,  ";
            $select .= "lev_datum_fi, bestallningsgrans, lev_datum_norm, lev_datum_norm_fi, Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, ";
            $select .= "Moms.momssats_fi, Moms.momssats, Artiklar.link, link2_fi ";
            $select .= " FROM Artiklar INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
            $select .= " INNER JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
            $select .= " INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
            $select .= " LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
            $select .= " WHERE artnr='$arg'";
                /**

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
		
		$row = mysqli_fetch_array(mysqli_query($this->conn_my, $select));
		if (is_array($row)) {
			extract($row);
		} else {
			?>
			
			<?php
			return;
		}
		//echo $kategori_id . " ";
		
		/**  Lagra alla artiklar i kundvagnen i en array som används för att plocka upp kampanjer för inkluderade artiklar på köpet. 
		Detta görs första omgången. */
		if ($firstbasket == 'nooutput') {
			// om mobilabonnemang, spara det i kundvagnen
			if ($kategori_id == 509) {
				$this->mobilabonnemang = true;
			} else {

			}
		
			
			if (eregi("pac$", $arg))  {
			
				$select = "SELECT Paketpriser.artnr_del as artnr, Paketpriser.antal, Artiklar.tillverkar_id, Artiklar.kategori_id FROM " . 
					  "Paketpriser, Artiklar WHERE Paketpriser.artnr_del = Artiklar.artnr AND artnr_paket = '" . $arg . "'";
					  
				//echo "<br>" . $select .  ": " . "" . "<br>";
				$res = mysqli_query($this->conn_my, $select);
				if (mysqli_num_rows($res) > 0) {
					//echo "jo!";
				}
				while ($row = mysqli_fetch_object($res)) { 
					$article = array();
					array_push($article, $row->artnr);
					array_push($article, $count*$row->antal);
					array_push($article, $row->tillverkar_id);
					array_push($article, $row->kategori_id);
					
					array_push($articles, $article);
					
					//print_r($article);

				}
			} else {	
				$article = array();
				array_push($article, $arg);
				array_push($article, $count);
				array_push($article, $tillverkar_id);
				array_push($article, $kategori_id);
				
				array_push($articles, $article);
			}	
			//$this->obsafe_print_r($articles, false, true);
		}		
		
		$release = $releaseDate;
		if ($fi) {
			$link = $link_fi;
			$utpris = $utpris_fi;
			$momssats = $momssats_fi;
		} else {
			$momssats = $momssats;
		}
		// räkna om priset här om det är någon form av rabattsats
		
		//TODO: fixa för fi också
	

		$discount = false;
		$standardUtpris = $utpris;
		$newUtpris = null;
		$extraBeskrivningText = "";
		if ($this->freeFreight && eregi("^frakt", $artnr) ) {
		
			$newUtpris = 0;
			if ($newUtpris != $utpris) {
				$extraBeskrivningText = "";				
				$discount = true;
				$utpris = $newUtpris;
				$count = 1;
			}
		} else {
			if ($discountCode != "" && $discountCodeStatus == 1 && $rowDiscount->totalSum == "" && !$fi) {							
				if ($rowDiscount->allowMultiple != -1)
					$count = 1;

				$newUtpris = $this->getDiscountPrice($rowDiscount, $artnr, $kategori_id, $tillverkar_id, $utpris);		
				if ($newUtpris != $utpris) {
					$extraBeskrivningText = $this->getDiscountText($rowDiscount, $momssats, $utpris, true);				
					$discount = true;
					$utpris = $newUtpris;
				}

			}		
		}
		// kollar om en artikel skall skickas med på köpet till artikeln och skriver in info om det då till kundvagnen
		
		if ($firstbasket != 'nooutput') {		
		//
			$tmpBeskr = "";
			if (eregi("pac$", $artnr))  {
			
				$select = "SELECT artnr_del as artnr, antal FROM Paketpriser WHERE artnr_paket = '" . $artnr . "'";
				//$select = "SELECT Paketpriser.artnr_del as artnr, Paketpriser.antal FROM Paketpriser, Artiklar WHERE Paketpriser.artnr_paket = Artiklar.artnr AND (Artiklar.utgangen = 0 OR Artiklar.lagersaldo > 0) AND Paketpriser.artnr_paket = '" . $artnr . "'";
				//echo "<br>" . $select .  ": " . "" . "<br>";
				$res = mysqli_query($this->conn_my, $select);
				if (mysqli_num_rows($res) > 0) {

				}
				while ($row = mysqli_fetch_object($res)) { 
					$var = $this->getIncludedArticle($row->artnr);
					$tmpBeskr .= $var[2];
				}
			} else {				
					//echo "hej";
					
					$var = $this->getIncludedArticle($artnr);
					
					//$this->obsafe_print_r($var, false, true);
					$tmpBeskr .= $var[2];
			}			
			

			$extraBeskrivningText .=  $tmpBeskr;

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
		if ($frakt > 0 && $freight != 'fraktbutik' && !$this->freeFreight )  {
		//$extraFrakt = true;
			if ($extra_freight < $frakt) {
				
				$extra_freight = $frakt; 
				
				}
		}
		if ($extra_freight == 3)
			$pallDelivery = true;	
		
		if (eregi("presentkort", $artnr)) {	
			if ($firstbasket == "nooutput")
				viewGiftCardInBasket($j, false);
			else
				viewGiftCardInBasket($j, true);
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
		// $description .= " " . $extraBeskrivningText;  Denna har jag bytt 2008-12-09
		$description .= "<br><br><b>" . $extraBeskrivningText;

			
		
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
			
			<?php if ($_SESSION['RememberMoms'] == 1) { ?>

			<td align="right" bgcolor="#ECECE6" width="79"><font size="1" face="Verdana">
			
			<?php 
			if ($fi)	
				echo number_format($utpris*$count, 0, ',', ' ') . " EUR";  
			else 	
				echo number_format($utpris*$count, 0, ',', ' ') . " kr";  
			?>
			</font></td>
			
			<?php } else { ?>
			
			<td align="right" bgcolor="#ECECE6" width="82"><font size="1" face="Verdana">
			
			<?php 
			if ($fi)
				echo number_format(($utpris + $utpris * $momssats) * $count, 0, ',', ' ') . " EUR"; 
			else 	
				echo number_format(($utpris + $utpris * $momssats) * $count, 0, ',', ' ') . " kr"; 
			?>
			
			</font></td>
			
			<?php } ?>
			
			</tr>

			<?php  	
			
		} 
	}

	
	if ($extra_freight && $firstbasket == 'yes' && $firstbasket != 'nooutput' && $extra_freight != 999 && $extra_freight != 3 && $freight != Null && !$this->freeFreight) {
	
	    if ($extra_freight == 1) $extra_freight_artnr = 'frakt+';
	    elseif ($extra_freight == 2) $extra_freight_artnr = 'frakt+2';
	    //elseif ($extra_freight == 3 && $old_foretag == 0) $extra_freight_artnr = 'frakthempall';	    
		//elseif ($extra_freight == 3 && $old_foretag == -1) $extra_freight_artnr = 'fraktpall';	    
	    else $extra_freight_artnr = 'frakt+'; // för säkerhets skull
	    $select  = "select Artiklar.beskrivning, Artiklar.kommentar, utpris, Artiklar_fi.beskrivning_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi from Artiklar, Artiklar_fi, Moms where Artiklar.momskod = Moms.moms_id AND Artiklar.artnr = Artiklar_fi.artnr_fi AND ";
	    $select .= " artnr='$extra_freight_artnr'";
	
	    $res = mysqli_query($this->conn_my, $select);
	    $row = mysqli_fetch_object($res);
		if ($fi && !$sv) {
	    	$name = $row->beskrivning_fi;
	    	$comment = $row->kommentar_fi;			
		} else {
	    	$name = $row->beskrivning;
	    	$comment = $row->kommentar;			
		}
		if ($fi) {
			$val = "EUR";
		    $outprice = $row->utpris_fi;
		    $momsts = $row->momssats_fi;			
		} else {
			$val = "kr";
		    $outprice = $row->utpris;
		    $momsts = $row->momssats;						
		}
	
	    $manufacturer = "";
	    $goodsvalue += $outprice;
	    $goodsvalueMoms += ($outprice + $outprice * $momsts);
?>	    

	<tr>
		<td colspan="5"><font size="1" face="Verdana">&nbsp;</font></td>
	</tr>
	<tr>
		<td colspan="5"><font size="1" face="Verdana"><b>Extra tillkommande avgifter</b></font></td>
	</tr>

	<tr>
		<td bgcolor="#ECECE6" width="262"><font size="1" face="Verdana"><i>
		<?php if ($fi && !$sv) { ?>
		<a onMouseOver="return escape('<b>Suuri lähetys lisämaksu</b><br>Kooltaan suurien tai painavien tuotteiden lähetyskuluihin tuleva lisämaksu (esim. tulostimet, scannerit, jalustat, taustakankaat, suuret laukut jne) Postin taulukon mukaisesti, tällä hetkellä 6,9 Eur (sis. alv 22%).')">
		<?php } else { ?>
		<a onMouseOver="return escape('<b>Avgift skrymmande</b><br>På stora varor som t ex skrivare, scanner, stativ, projektionsdukar eller större väskor tillkommer ett skrymmetillägg på 69kr - 295kr inklusive moms beroende på storlek.')">
		<?php } ?>		
		<?php echo $name; ?>
		</a></i>
		</font>
		</td>
		<td align="center" bgcolor="#ECECE6" width="30"><font size="1" face="Verdana">1</font></td>

		<td bgcolor="#ECECE6"><font size="1" face="Verdana">&nbsp;&nbsp;</font></td>
		<td bgcolor="#ECECE6"><font size="1" face="Verdana">&nbsp;&nbsp;</font></td>
		
		<?php if ($_SESSION['RememberMoms'] == 1) { ?>

		<td align="right" bgcolor="#ECECE6" width="79"><font size="1" face="Verdana">

		<?php echo number_format($outprice, 0, ',', ' ') . " " . $val;  ?>
		</font></td>
		
		<?php } else { ?>
		
		<td align="right" bgcolor="#ECECE6" width="82"><font size="1" face="Verdana">

		<?php echo number_format(($outprice + $outprice * $momsts) * $count, 0, ',', ' ') . " " . $val; ?>

		</font></td>
		
		<?php } ?>

	</tr>
	<?php  
	    
	}
	if ($firstbasket != 'nooutput' && count($this->discounts) > 0) {
		// $this->discounts räknare | beskrivning | summa rabatt | artikelnr | procent rabatt | moms_id | samma artikelnr? (på köpet?) | på köpet | falskt? (kanske att den är använd?
		// $this->discounts: id | description | discount amount | discount percent | tax code
// $this->discounts räknare | beskrivning | artikelnr (som får rabatten) | summa rabatt | procent rabatt | moms_id | samma artikelnr (vet ej varför) | om den är använd ännu
//			0	  1		2				3		4		5		6			   7

		$k = 0;
		foreach ($this->discounts as $discount) {
			//if ($discount[6] != $artnr && $discount[7] != $artnr && !$discount[7]) {
			if (!$discount[7]) {
			
				//$this->obsafe_print_r($discount, false, true);
				if ($discount[3] > 0) {
					$discountAmount = $discount[3]; 							
					$goodsvalue -= $discountAmount;
					$goodsvalueMoms -= $discountAmount * 1.25;
				} else if ($discount[4] > 0) {
					//$discountAmount = $discount[3];
					
					//$goodsvalue -= $discountAmount;
					//$goodsvalueMoms -= $discountAmount * 1.25;								
				}
			?>
				<tr>
					
				  <td bgcolor="#ECECE6" width="262"><font size="1" face="Verdana">
						<?php echo $discount[1]; ?>
		  			</td>
				  <td align="center" bgcolor="#ECECE6" width="30"><font size="1" face="Verdana">1</font></td>				  
				<td align="center" bgcolor="#ECECE6" width="66"><font size="1" face="Verdana">	
					&nbsp;&nbsp;
				</font></td>
				<td align="left" bgcolor="#ECECE6" width="100"><font size="1" color="#008000" face="Verdana">
							&nbsp;&nbsp;
				</font></td>
			 	<?php
	
			 	
			 	?>
				<?php if ($_SESSION['RememberMoms'] == 1) { ?>
	
				<td align="right" bgcolor="#ECECE6" width="79"><font size="1" face="Verdana">
				
				<?php 
				if ($fi)	
					echo number_format(-$discountAmount, 0, ',', ' ') . " EUR";  
				else 	
					echo number_format(-$discountAmount, 0, ',', ' ') . " kr";  
				?>
				</font></td>
				
				<?php } else { ?>
				
				<td align="right" bgcolor="#ECECE6" width="82"><font size="1" face="Verdana">
				
				<?php 
				if ($fi)
					echo number_format(-($discountAmount + $discountAmount * 0.25) * $count, 0, ',', ' ') . " EUR"; 
				else 	
					echo number_format(-($discountAmount + $discountAmount * 0.25) * $count, 0, ',', ' ') . " kr"; 
				?>
				
				</font></td>
				
				<?php } ?>
				
				</tr>
			
			<?php
			}
			$k++;				
			}
				
	}
	
	$this->basketValue = $goodsvalue;
}

/**
 * kontrollerar lagersaldo
 *
 * @param string $artnr 
 */
function check_lager($artnr) {
	
	global $fi, $sv;
	//echo $fi;
	//echo $sv;
	//if ($fi)
	//	$this->check_lager_fi($artnr);
	//else 	
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
	//echo $release;
	$release = substr($release, 0, 10);
	$package_stock_sv = false;

	# Make a check if freigt is already selected. 
	if (ereg("^frakt", $artnr))
		{ $freight_check = 1; }

	if (ereg("pac$", $artnr)) {    // kollar tillgången om det är ett paket
		$package_stock_sv = $this->check_package_sv($artnr); 	
	}
	//echo "här: " . $lagersaldo . $count;
	if ($lagersaldo >= $count || $package_stock_sv) {
		if (!$sv)
			print "<a onMouseOver=\"return escape('Tuote löytyy varastosta ja lähetetään normaalisti samana päivänä kuin teet tilauksesi')\" style=\"text-decoration: none\"> varastossa</a>"; 
		else 
			print "<a onMouseOver=\"return escape('Varan finns på lager och skickas normalt samma dag som ni beställer')\" style=\"text-decoration: none\"> finns i lager</a>";	
	} else {
		if (!(eregi("^frakt", $artnr))) {

			$this->completeOrder = false;
			echo "";
		} else {
			echo "";
		}
		# Kolla hur många det finns på kö
		# antal på köp visas i $queue, nollställes först. 
		$queue = 0;
		
		$queue = $this->check_queue($artnr, $fi, null);
		$neededStock = $queue + $count;
		//echo $bestallt;	
		//echo $neededStock;	
		if ($bestallt >= $neededStock )  { 	
				
			echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($artnr, $count, null, false, $fi, $sv )	;
			
		} 


		else  { 
		//<a onMouseOver=\"return escape('')\" style=\"text-decoration: none\"> 
			if (ereg("pac$",$artnr)) {				  				
				if (!$sv)
					print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \" onMouseOver=\"return escape('Tuote on paketti joka koostuu eri osista. Yksi tai useampi osa on loppunut varastosta. Klikkaa nähdääksesi joka tuotteen varastotilaa. Luonnollisesti voit tilata paketin kuitenkin.')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">lisää tietoja tästä</a>";
				else
					print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \" onMouseOver=\"return escape('Varan består av ett paket med flera delar. En eller flera av delarna är slut i lager. Klicka för att se lagerstatus på varje enskild vara. Naturligtvis går paketet bra att beställa ändå. ')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\" >klicka för info</a>";
					
			} elseif ($bestallningsgrans == 0)  {
			  
				// kolla om den är släppt ännu eller inte. 
				if ($release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($release) >= mktime()) {
						if (!$sv) 
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote ei ole vielä markkinoilla, arvioitu ensimmäinen toimitusaika " . $this->showDeliveryDate($release, 1, $fi, $sv) . "<br>Ainoastaan tilauksesta')\" style=\"text-decoration: none\">Tulee markkinoille " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har ännu inte kommit ut på marknaden. Beräknat första leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) ."<br>Produkten är en beställningsvara')\" style=\"text-decoration: none\">Släpps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						
					} else { // då blir det vanlig beställningsvara. 
						if (!$sv) // TODO: finska nästa rad
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm_fi </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> beställningsvara</></font></a>";													
					}
				} else {
					if (!$sv) // TODO: finska nästa rad
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm_fi </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
					else
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> beställningsvara</></font></a>";												
				}
				
				
			} else {
				 
				if ($release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($release) >= mktime()) {					
						if (!$sv)
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote ei ole vielä markkinoilla, arvioitu ensimmäinen toimitusaika " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Släpps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har ännu inte kommit ut på marknaden. Beräknat första leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Släpps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
					} else {
						if (!$sv) // TODO: finska på nästa rad
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilapäisesti lopussa. <br>Normaali toimitusaika on  <b>$lev_datum_norm_fi </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilapäisesti loppu</font></a>" ;														
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> tillfälligt slut</font></a>" ;															
					}
				} else {
					if (!$sv)
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilapäisesti lopussa. <br>Normaali toimitusaika on  <b>$lev_datum_norm_fi </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilapäisesti loppu</font></a>" ;														
					else
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> tillfälligt slut</font></a>" ;															
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
		if (!$package_stock_fi) $package_stock_sv = $this->check_package_sv($artnr);		
	}
	
	if ($lagersaldo_fi >= $count || $package_stock_fi) {
		if (!$sv)
			print "<a onMouseOver=\"return escape('Tuote löytyy varastosta ja lähetetään normaalisti samana päivänä kuin teet tilauksesi')\" style=\"text-decoration: none\"> varastossa</a>"; 
		else 
			print "<a onMouseOver=\"return escape('Varan finns på lager och skickas normalt samma dag som ni beställer')\" style=\"text-decoration: none\"> finns i lager</a>";	
	} else {

		# Kolla hur många det finns på kö
		# antal på köp visas i $queue, nollställes först. 
		$queue = 0;
		
		$queue = $this->check_queue($artnr, true, null);
		
		$neededStock = $queue + $count;
		
		if ($bestallt_fi >= $neededStock )  { 	
			
//			echo $bestallt_fi;
			echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($artnr, $count, null, true, $fi, $sv )	;			
		} 
		elseif ( ($package_stock_sv || $lagersaldo >= $count) )  { 				
			if (!$sv)
				print "<a onMouseOver=\"return escape('Tuote Ruotsin keskusvarastossa. Huom. pidempi toimitusaika')\" style=\"text-decoration: none\"> keskusvarastossa, toimitusaika noin 7vrk</a>"; 
			else 
				print "<a onMouseOver=\"return escape('Varan finns på centrallager, obs. längre leveranstid')\" style=\"text-decoration: none\"> finns på centrallager, leveranstid ca 7dgr</a>";							
		} 
		elseif ($bestallt >= $neededStock) {
			echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($artnr, $count, null, false, true, $sv );
		} else  { 
		//<a onMouseOver=\"return escape('')\" style=\"text-decoration: none\"> 
			if (ereg("pac$",$artnr)) {							
				if (!$sv)
					print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \"> onMouseOver=\"return escape('Tuote on paketti joka koostuu eri osista. Yksi tai useampi osa on loppunut varastosta. Klikkaa nähdääksesi joka tuotteen varastotilaa. Luonnollisesti voit tilata paketin kuitenkin.')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">lisää tietoja tästä</a>";
				else
					print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \"> onMouseOver=\"return escape('Varan består av ett paket med flera delar. En eller flera av delarna är slut i lager. Klicka för att se lagerstatus på varje enskild vara. Naturligtvis går paketet bra att beställa ändå. ')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\" >klicka för info</a>";
					
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
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm_fi </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> beställningsvara</></font></a>";													
					}
				} else {
					if (!$sv) // TODO: finska nästa rad
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm_fi </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
					else
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> beställningsvara</></font></a>";													
				}
				
				
			} else {
				 
				if ($release != "") {
					// kolla så att releasedatumet inte varit ännu. 
					if (strtotime($release) >= mktime()) {					
						if (!$sv)
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote ei ole vielä markkinoilla, arvioitu ensimmäinen toimitusaika " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Släpps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har ännu inte kommit ut på marknaden. Beräknat första leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Släpps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
					} else {
						if (!$sv) // TODO: finska på nästa rad
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilapäisesti lopussa. <br>Normaali toimitusaika on  <b>$lev_datum_norm_fi </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilapäisesti loppu</font></a>" ;														
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager. <br>Normal leveranstid är <b>$lev_datum_norm_fi </b> dagar till vårt lager<br>Denna leveranstid förutsätter att vår leverantör har varan på lager')\" style=\"text-decoration: none\"> tillfälligt slut</font></a>" ;														
					}
				} else {
					if (!$sv)
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilapäisesti lopussa. <br>Normaali toimitusaika on  <b>$lev_datum_norm_fi </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilapäisesti loppu</font></a>" ;														
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
			return "varastossa"; 
		else 
			return "finns i lager";	
	} else {

		# Kolla hur många det finns på kö
		# antal på köp visas i $queue, nollställes först. 
		$queue = 0;
		
		$queue = $this->check_queue($row->artnr, true, $row->ordernr);
		
		$neededStock = $queue + $count;
		//echo "här: " . $row->lagersaldo . "." . $count . "";
		//echo $row->bestallt_fi;
		if ($row->bestallt_fi >= $neededStock )  { 			
			return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($row->artnr, $count, $row->ordernr, true, $fi, $sv )	. "</font>";			
		} 
		elseif ( ($row->lagersaldo >= $count) )  { 				
			if (!$sv)
				return "keskusvarastossa, toimitusaika noin 7vrk"; 
			else 
				return "finns på centrallager, leveranstid ca 7dgr";							
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
						if (!$sv) 
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tulee markkinoille " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Släpps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
					} else { // då blir det vanlig beställningsvara. 
						if (!$sv) 
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
						if (!$sv) 
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tulee markkinoille " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Släpps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
					} else {
						if (!$sv) // TODO: finska på nästa rad
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tilapäisesti loppu</font></a>" ;														
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
		if (!$sv)
			return "varastossa"; 
		else 
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
						if (!$sv) 
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tulee markkinoille " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Släpps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
					} else { // då blir det vanlig beställningsvara. 
						if (!$sv) 
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
						if (!$sv) 
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tulee markkinoille " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Släpps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
					} else {
						if (!$sv) // TODO: finska på nästa rad
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tilapäisesti loppu</font></a>" ;														
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
function check_package_sv($artnr) {

	global $count;
	
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
	//echo $select;
	//echo $count;
	$res = mysqli_query($this->conn_my, $select);
	
	unset ($check);

	while ($row = mysqli_fetch_array($res))
	{
	extract($row);

	$check = $antal*$count;



	  if ($check > $lagersaldo_fi) {
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
		//if ($fi)
		//	$select = "SELECT sum(antal) as ant FROM Orderposter_fi WHERE bokad = 0 && artnr = '" . $artnr . "' AND ordernr < " . $ordernr;
		//else 	
			$select = "SELECT sum(antal) as ant FROM Orderposter WHERE bokad = 0 && artnr = '" . $artnr . "' AND ordernr < " . $ordernr;		
	} else {
		//if ($fi)
		//	$select = "SELECT sum(antal) as ant FROM Orderposter_fi WHERE bokad = 0 && artnr = '" . $artnr . "' ";
		//else 	
			$select = "SELECT sum(antal) as ant FROM Orderposter WHERE bokad = 0 && artnr = '$artnr' ";		
	}
		
	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > 0) {	
		$row = mysqli_fetch_array($res);
		$queue = $row['ant'];			
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
	$select .= "ORDER BY ber_lev_datum ASC ";
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
	
		if ($fi)
			$articleinfo = "toimituspäivämäärä ei ole määritelty";
		else
			$articleinfo = "leveransdatum okänt";
	
	}

if ($articleinfo == "" || $articleinfo == "-") {
	if ($fi)
		$articleinfo = "toimituspäivämäärä ei ole määritelty";
	else
		$articleinfo = "leveransdatum okänt";
}
return $articleinfo;
}

//-------------------------------------------------------------------------------------


function kollaLevtid ($artnr, $count, $ordernr, $table_fi, $fi, $sv)  { // artnr som skall kollas, antal samt inkommet datum på ordern, 
																		//$table_fi om vi ska titta efter leveranstid i finska Inkopsposter eller inte
																			
	//$fi avser i det här fallet om Orderposter_fi eller Orderposter skall användas. 
	
	// kolla först inkommet för att få fram köplats. 
	// ange dagens datum om koll för kundvagnen 	
	if ($ordernr == NULL) {
		$inkommetOur = date("Y-m-d H:i:s");
	}
	else {
		//if ($fi)
		//	$select = "SELECT inkommet from Ordertabell_fi WHERE ordernr = '$ordernr' ";
		//else 	
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
	/**
	if ($fi) {
		$select =  "SELECT Orderposter_fi.ordernr, Orderposter_fi.antal, Ordertabell_fi.inkommet FROM Orderposter_fi, Ordertabell_fi ";
		$select .= "WHERE Orderposter_fi.ordernr = Ordertabell_fi.ordernr AND ";
		$select .= "Orderposter_fi.artnr = '$artnr' AND Orderposter_fi.bokad = 0 AND inkommet < '" . date("Y-m-d H:i:s", strtotime($inkommetOur)) . "' ";
		$select .= "ORDER BY Ordertabell_fi.inkommet ASC";	
		
	} else {
	*/
		$select =  "SELECT Orderposter.ordernr, Orderposter.antal, Ordertabell.inkommet FROM Orderposter, Ordertabell ";
		$select .= "WHERE Orderposter.ordernr = Ordertabell.ordernr AND ";
		$select .= "Orderposter.artnr = '$artnr' AND Orderposter.bokad = 0 AND inkommet < '" . date("Y-m-d H:i:s", strtotime($inkommetOur)) . "' ";
		$select .= "ORDER BY Ordertabell.inkommet ASC";	
	//}
	
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
	/**
	if ($table_fi) {
		
		//echo ".";
		$select = "SELECT inkopsnr, antal, levdatum, ber_lev_datum_fi as ber_lev_datum, prec_fi as prec, levererat FROM Inkopsposter_fi WHERE artnr = '$artnr' "; 
		$select .= "AND antal != levererat ";		
		$select .= "ORDER BY ber_lev_datum_fi ASC ";
		$res = mysqli_query($this->conn_my, $select);
			
	} else {
	*/
		$select = "SELECT inkopsnr, antal, levdatum, ber_lev_datum, prec, levererat FROM Inkopsposter WHERE artnr = '$artnr' "; 
		$select .= "AND antal != levererat ";
		$select .= "ORDER BY ber_lev_datum ASC ";
		$res = mysqli_query($this->conn_my, $select);
		
	//}
	
	//echo $select;
	if (mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)): 
			extract ($row);
			$ber_lev_datum = substr($ber_lev_datum, 0, 10);
			
			$this->ber_lev_datum = $ber_lev_datum;
			$this->prec = $prec;
			$antal_sum += ($antal - $levererat);
			
			//echo "<br>här: , " . $artnr . " : " . $this->ber_lev_datum . ", " . $this->prec;
			if ($antal_sum >= $totalNeed) {
				
				if ($ber_lev_datum == "" || $ber_lev_datum == "1900-01-01" || $ber_lev_datum == "2100-01-01") {
					$this->ber_lev_datum = "2100-01-01";	
										
					if ($levdatum != "" && $levdatum != "-") {
						
						if (!$sv) {
							$articleinfo = "<a onMouseOver=\"return escape('Päivämäärä viittaa päivämäärään jolloin tuote arvioidaan saapuvan varastoon. Huomioi että tämä on arvioitu päivämäärä')\" style=\"text-decoration: none\">" . $levdatum . "</a>";					
						} else {
							$articleinfo = "<a onMouseOver=\"return escape('Datumet avser datum när varan <b>beräknas</b> komma in till vårt lager. Observera att detta är ungefärligt datum. ')\" style=\"text-decoration: none\">" . $levdatum . " </a>";											
						}						
					}
					
					elseif (!$sv)
						$articleinfo = "<a onMouseOver=\"return escape('Tuote on tilattu mutta toimitusaika ei ole tiedossa. Emme ole saaneet tilausvahvistusta toimittajalta. ')\" style=\"text-decoration: none\">toimituspäivämäärä ei ole määritelty</a>";
					else
						$articleinfo = "<a onMouseOver=\"return escape('Det betyder kort och gott att vi inte vet när varan finns i lager igen. Skulle vi få ett besked från vår leverantör kommer det att hamna här istället, men så länge det står leveransdatum okänt har vi alltså inget besked att ge. ')\" style=\"text-decoration: underline\"> leveransdatum okänt</a>";
				} else {					
					//$prec = 1;
					//echo "här: " . $table_fi;
					/**
					if (!$table_fi && $prec == 1 && $fi) { // lägg på två dagar på leveranstiden om det är från finland men svenska levtiden kollas. 
						$ber_lev_datum = date("Y-m-d", (strtotime($ber_lev_datum . "+2 day")));	
						//echo $ber_lev_datum;
					}*/
					$this->ber_lev_datum = $ber_lev_datum;
					if (!$sv) {						
						$articleinfo = "<a onMouseOver=\"return escape('Päivämäärä viittaa päivämäärään jolloin tuote arvioidaan saapuvan varastoon. Huomioi että tämä on arvioitu päivämäärä')\" style=\"text-decoration: none\">" . $this->showDeliveryDate($ber_lev_datum, $prec, $fi, $sv) . "</a>";					
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
		$this->ber_lev_datum = "2100-01-01";
		if (!$sv)
			$articleinfo = "<a onMouseOver=\"return escape('Tuote on tilattu mutta toimitusaika ei ole tiedossa. Emme ole saaneet tilausvahvistusta toimittajalta. ')\" style=\"text-decoration: none\">toimituspäivämäärä ei ole määritelty</a>";
		else
			$articleinfo = "<a onMouseOver=\"return escape('Det betyder kort och gott att vi inte vet när varan finns i lager igen. Skulle vi få ett besked från vår leverantör kommer det att hamna här istället, men så länge det står leveransdatum okänt har vi alltså inget besked att ge. ')\" style=\"text-decoration: underline\"> leveransdatum okänt</a>";
	
	}
//echo $articleinfo;


return $articleinfo;
}
function calcDate($date, $days) {
	$dat = strtotime($date) + 86400 * $days;
	return date("Y-m-d", $dat);
}

function showDeliveryDate($dat, $prec, $fi, $sv) {
	
	//echo $dat;
	setlocale (LC_ALL, 'en_US');
	
	$ret = "";

	if ($dat != "")
		$dat = substr($dat, 0, 10);
	
	$timestmp = strtotime($dat);		
	//echo $timestmp;
	if ($dat == "" || $dat == "1900-01-01" || $dat == "2100-01-01") {
		if ($fi && !$sv)
			return "<a onMouseOver=\"return escape('Tuote on tilattu mutta toimitusaika ei ole tiedossa. Emme ole saaneet tilausvahvistusta toimittajalta')\" style=\"text-decoration: none\"> toimituspäivämäärä ei ole määritelty</a>";
		else 
			return "<a onMouseOver=\"return escape('Det betyder kort och gott att vi inte vet när varan finns i lager igen. Skulle vi få ett besked från vår leverantör kommer det att hamna här istället, men så länge det står leveransdatum okänt har vi alltså inget besked att ge. ')\" style=\"text-decoration: underline\"> leveransdatum okänt</a>";
	}
	
	if ($prec == 1 || $prec == "") { // exakt datum
		if ($fi) {								
			return date("d-m-Y", $timestmp);
		} else {
			//echo ".";
			//echo strtotime($dat);
			return date("Y-m-d", $timestmp);
		}
		return $dat;
	} elseif ($prec == 2) {	// visas som vecka
		if ($fi && !$sv)
			return "Oletettu saapumisaika viikko " .  strtolower (date("W", strtotime($dat)));		
		else 	
			return "Beräknas in vecka " .  strtolower (date("W", strtotime($dat)));		
			
	} elseif ($prec == 3) {	// del av månad
		$day = date("j", strtotime($dat));
		$month = date("n", strtotime($dat));

		if ($day > 0 && $day <= 10 ) {
			if ($fi && !$sv)
				return "Oletettu saapumisaika " . $this->getMonthFi($month) . " alussa";
			else 
				return "Beräknas in i början av " . $this->getMonthSv($month);
				
		} elseif ($day > 10 && $day <= 20) {
			if ($fi && !$sv)
				return "Oletettu saapumisaika " . $this->getMonthFi($month) . " puolivälissä";		
			else 
				return "Beräknas in i mitten av " . $this->getMonthSv($month);
		} elseif ($day > 20) {
			if ($fi && !$sv)
				return "Oletettu saapumisaika " . $this->getMonthFi($month) . " loppupuolella";
			else
				return "Beräknas in i slutet av " . $this->getMonthSv($month);
				
		} else { // tja, när är den något annat? Tomt blir nog bra
			return "";
		}
		
	} elseif ($prec == 4) { // månad
		$month = date("n", strtotime($dat));
		if ($fi && !$sv)
			//return "Oletettu saapumisaika " . strtolower ( date("F", strtotime($dat)));		
			return "Oletettu saapumisaika " . $this->getMonthFi($month);		
		else 
			//return "Beräknas in i " . strtolower ( date("F", strtotime($dat)));		
			return "Beräknas in i " . $this->getMonthSv($month);

	} else {
		return $dat;
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

function includeCampaign($campaignNr) {
	global $campaignQuestionResult;
	
	$select = "SELECT * FROM campaignQuestions WHERE campaignId = " . $campaignNr . " order by ordering";
	$select2 = "SELECT * FROM campaign WHERE id = " . $campaignNr;
	$res = mysqli_query($this->conn_my, $select);
	$res2 = mysqli_query($this->conn_my, $select2);

	if (mysqli_num_rows($res) == 0)
		return;
	$row2 = mysqli_fetch_object($res2);
	?>	
	<input type="hidden" name="campaignNo" value="<?php echo $campaignNr ?>">
	  <tr>
	    <td bgcolor="#B90000">&nbsp;</td><td colspan="2" bgcolor="#B90000"><font face="Verdana" size="1" color="#FFFFFF"><b><% echo $row2->name; %></b></font></td>
	  </tr>
	  <?php 
		while ($row = mysqli_fetch_object($res)) {
		
			echo "<tr><td bgcolor=\"#B90000\">&nbsp;</td><td bgcolor=\"#B90000\"><font face=\"Verdana\" size=\"1\" color=\"#FFFFFF\">" . $row->question . "</font></td>";
			if ($row->typeTrueFalse) {
				echo "<td bgcolor=\"#B90000\"><input name=\"campaignRes[" . $row->questionNr . "]\" size=\"20\" type=\"checkbox\" value=yes";
				if ($campaignQuestionResult[$row->questionNr])
					echo " checked";					
				echo "></td></tr>";			
			} else {
				echo "<td bgcolor=\"#B90000\"><textarea cols=\"23\" rows=\"2\" style=\"font-family: Verdana; font-size: 8pt\" name=\"campaignRes[" . $row->questionNr . "]\" size=\"20\">" . $campaignQuestionResult[$row->questionNr] . "</textarea></td></tr>";
			}
		
		}
		
	  ?>	
	
<?php
}
function viewPacketDeliveryPicture($artnr, $mangd, $fi) {
	global $count, $lagersaldo, $lagersaldo_fi, $bestallt, $lev_datum, $lev_datum_norm, $lev_datum_norm_fi, $bestallningsgrans, $bestallningsgrans_fi, $fi, $sv, $bestallt_fi;		
	
	$select =  "SELECT Paketpriser.artnr_paket, Paketpriser.artnr_del, Paketpriser.antal, Artiklar.artnr, Artiklar.bild, ";
	$select .= "Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar_fi.lagersaldo_fi, Artiklar.lagersaldo, Artiklar.beskrivning, ";
	$select .= "tillverkare, Artiklar.lagersaldo, Artiklar.bestallt, ";
	$select .= "lev_datum_fi, bestallningsgrans, bestallningsgrans_fi, lev_datum_norm, lev_datum_norm_fi, lagersaldo_fi, bestallt_fi, bestallt, ";
	$select .= "Artiklar.bestallningsgrans, Artiklar.lev_datum_normal ";
	$select .= "FROM Artiklar ";
	$select .= "INNER JOIN Paketpriser ON Artiklar.artnr = Paketpriser.artnr_del ";
	$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
	$select .= "WHERE Paketpriser.artnr_paket = '$artnr' ";

	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)):
		$description = ""; $bestallt_fi = 0;
		extract($row);
//		echo $bestallt_fi;
		$count = $antal*$mangd;
		if ($tillverkare != '.')
			$description = $tillverkare . " ";

		if (!$sv && $beskrivning_fi != "")
			$description .= $beskrivning_fi;
		else 	
			$description .= $beskrivning;
		?>		
		<tr>
		  <td align="center" bgcolor="#FFFFFF"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php if ($bild != "") { ?><img src=/thumbs/medium/bilder/<?php echo $bild ?>><?php } else { ?><img src=/kopknapp/bildsaknas.jpg><?php } ?></font></td>
		  <td bgcolor="#FFFFFF"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php echo $description; ?></font></td>
		  <td bgcolor="#FFFFFF" align="center"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php echo $antal*$mangd; ?></font></td>
		  <td bgcolor="#FFFFFF"><font color="#2B2B2B" face="Verdana, Arial" size="1">
		<?php	
		//($artnr, $count, NULL);
		//echo $artnr;
		$count = $antal * $mangd;
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
		  <td bgcolor="#F0F0F0"><font face="Verdana, Arial" size="1">
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

function formatDiscountTxt($rs, $showstyle) {
	global $fi, $sv;
	
	if ($fi) {
		if ($sv) {		
			$explanationtext = $rs->descrptn_fi_sv;
			$campaign_link = $rs->campaign_link_fi_sv;
			$link = $rs->link_fi_sv;
			$link_ext = $rs->link_ext_fi_sv;
		} else {
			$explanationtext = $rs->descrptn_fi;
			$campaign_link = $rs->campaign_link_fi;
			$link = $rs->link_fi;
			$link_ext = $rs->link_ext_fi;
		}
	} else {
		$explanationtext = $rs->descrptn;
		$campaign_link = $rs->campaign_link;
		$link = $rs->link;
		$link_ext = $rs->link_ext;
	}

	if ($showstyle == 1) { // Denna visar texten på info sidan
		if ($explanationtext != "") {
	
			if ($fi && !$sv) {
				echo "<b><font color=\"#85000D\" face=\"Verdana\" size=\"1\">Kampanja!</font><font color=\"#000000\" face=\"Verdana\" size=\"1\">&nbsp;" . $explanationtext . "</b>";
			} elseif ($fi && $sv) {
				echo "<b><font color=\"#85000D\" face=\"Verdana\" size=\"1\">Kampanj!</font><font color=\"#000000\" face=\"Verdana\" size=\"1\">&nbsp;" . $explanationtext . "</b>";
			} else {
				echo "<b><font color=\"#85000D\" face=\"Verdana\" size=\"1\">Kampanj!</font><font color=\"#000000\" face=\"Verdana\" size=\"1\">&nbsp;" . $explanationtext . "</b>";
			}
		} else {

			echo "&nbsp;";

		}
	
	} elseif ($showstyle == 2) { // Denna visar bilden på infosidan.
		if ($campaign_link != "") {
		
			if ($link_ext != "") {
				echo "<a target=\"_blank\" href=\"$link_ext\"><img border=\"0\" src=\"/../../$campaign_link\"></a>";
				} else {
				echo "<img border=\"0\" src=\"../../$campaign_link\">";
				}
		} else {
		
			echo "&nbsp;";
		}
	
	/*	
	if ($campaign_link == "")
		return "";


	if ($link == "")
		$link == $link_ext;
		
	if (!eregi("^/", $campaign_link)) {
		
		$campaign_link = "/" . $campaign_link;
	}
	if (!eregi("http", $link)) {
		
		if (!eregi("/", $link)) {
		
			$link = "/" . $link;
		}
	}
	
	// om php/html sida: 
	if (eregi(".php$", $campaign_link) || eregi(".htm$", $campaign_link) || eregi(".html$", $campaign_link)) {
		ob_start();
		include($campaign_link);
		$cont = ob_get_contents();
		ob_end_clean();
		//$cont = "<a href=\"" . $rs->link . "\">" . $cont . "</a>"; // det här behövs nog inte
	} else { // annars, om bild

		$cont = "<a href=\"" . $link . "\"><img border=\"0\" src=\"" . $campaign_link . "\"></a><br>";
		
	}
	
	return $cont;
	
	*/
	
	} else {
	
		echo "&nbsp;";
	}
	
}

function getDiscountInfo($artnr, $showstyle, $tillverkar_id, $kategori_id, $discountCode="") {
	
	global $fi, $sv;

	$select = "SELECT discountCodes.*, discountCodeArticle.artnr as artnrMatch, discountCodeArticle.articleDiscounted FROM discountCodes LEFT JOIN discountCodeArticle ON discountCodeArticle.dscntId = discountCodes.cnt " . 
	" WHERE  (" . 
	" (discountCodeArticle.artnr = '" . $artnr . "' ) OR  (" .
	" (discountCodes.tillverkar_id =  " . $tillverkar_id . " ) OR " .
	" (discountCodes.artnr =  '" . $artnr . "' ) OR " .
	" (discountCodes.kategori_id =  " . $kategori_id . " ) ";
	if ($this->basketValue > 0 && $tjosan == "hejsan") { // används inte nu då vi inte vet hur vi ska använda totalsumman. Däremot används siffran när det gäller fri frakt
		if ($fi) {
			$select .= "OR (discountCodes.totalSum_fi > " . $this->basketValue . " AND discountCodes.totalSum_fi is not null) ";
		} else {
			$select .= "OR (discountCodes.totalSum > " . $this->basketValue . " AND discountCodes.totalSum is not null ) ";
		}
	}
	if ($discountCode != "") {
		//$select .= " OR (discountCodes.discountCode = '" . $discountCode ."' )";
	}
	if ($this->basketValue > 0  && $tjosan == "hejsan") {
		if ($fi)
			$select .= " OR (discountCodes.freeFreight = -1 AND (discountCodes.totalSum_fi > " . $this->basketValue . " OR discountCodes.totalSum_fi is null OR  discountCodes.totalSum_fi = 0) )";
		else				
			$select .= " OR (discountCodes.freeFreight = -1 AND (discountCodes.totalSum > " . $this->basketValue . " OR discountCodes.totalSum is null OR  discountCodes.totalSum = 0) )";
	}
	$select .= ") ) "; 

	$select .= " AND validDate > getdate() AND validFrom < getDate() " . $notSel;
	if ($fi)
		$select .= " AND active_fi = -1";
	else
		$select .= " AND active_se = -1";

	// echo $select;
	$res = mssql_query($select, $this->conn_ms);

	if (mssql_num_rows($res) > 0) {

		$row = mssql_fetch_object($res);



		if ( ($row->freeFreight == -1 && ($row->discountCodes.totalSum > $this->basketValue || $row->discountCodes.totalSum == 0 || $row->discountCodes.totalSum == "" ) && !$fi ) ||
			($row->freeFreight == -1 && ($row->discountCodes.totalSum > $this->basketValue || $row->discountCodes.totalSum_fi == 0 || $row->discountCodes.totalSum_fi == "" ) && $fi )) { // om det avser fri frakt så sätts den här och då fortsätter inte processen. 				
			return $this->formatDiscountTxt($row, $showstyle);				
			echo "här";
		} elseif ($row->artnrMatch != "") {
			// tag fram tillhörande artiklar till den du söker information om 
			$select = "SELECT * FROM discountCodeArticle WHERE dscntId = " . $row->cnt;
			$res2 = mssql_query($select, $this->conn_ms);

			if (mssql_num_rows($res2) == 0 ) {				
				//return $this->formatDiscountTxt($row);
				return "";
			}
			
			while ($rowDiscount = mssql_fetch_object($res2)) {	
				
				if ($rowDiscount->articleDiscounted == -1) {      // om det är en rabatterad vara										     				
					return formatDiscountTxt($row);
				} elseif ($rowDiscount->includedArticles_id != "") {  // annars, om det är en vara som skall ingå gratis, 				
					$sel =  "SELECT discountCodeIncludedArticles.*, Artiklar.lagersaldo, Artiklar.utgangen, Artiklar.ej_med FROM discountCodeIncludedArticles, Artiklar WHERE " . 
						"discountCodeIncludedArticles.includedArticles = Artiklar.artnr AND discountCodeIncludedArticles.includedArticles_id = " . $rowDiscount->includedArticles_id;
					
					$rs = (mssql_query($sel, $this->conn_ms));
					while ($rw = mssql_fetch_object($rs)) {
						
						if (($rw->lagersaldo >= $rw->qty || $rw->utgangen == 0 ) && 
							 ($rw->lagersaldo > 0 || $rw->allowNoStock == -1)) {

						} else { // då är den ogiltig
							unset($includedArtsTmp);
							return "";
							break;
						}
						$i++;

					}
					
					return $this->formatDiscountTxt($row, $showstyle);
					
				}
			}
		} else{
			return $this->formatDiscountTxt($row, $showstyle);
		}
	} else {
		return "";
	}
}

}
?>

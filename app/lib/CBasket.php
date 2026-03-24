<?php
/**
changelog: v44
�ndra s� att nyhetsbrev alltid �r f�rblockat oavsett tidigare, dvs old_newsletter ska alltid vara sant. 

*/
require_once("CCheckIpNumber.php");
require_once("Locs.php");
require_once("PasswordEncryption.php");
require_once("Db.php");
/*

PHP login object
author		Nils Kohlstr�m
version		0.9 2006-07-14

2013-08-30
En helgkampanj p� Cybairgun som beh�ver testat noggrant innan implementeringen.
*/

//include_once("connections.php");
	
require_once("CWebAdempiere.php");
$web_adempiere = new CWebAdempiere();

require_once("CMobile.php");
$mobile = new CMobile();
require_once("CCategories.php");
$sub = new CCategories();

Class CBasket {
	var $conn_ms;
	var $conn_my;
	var $conn_fi; 
    var $conn_master;
	var $confirm;
	var $kundnrsave;
	var $ber_lev_datum;
	var $prec;
	var $completeOrder;
	var $includedArticles;
	
	var $freeFreight;
	var $basketValue;	
	
	var $mobilabonnemang;
	var $aldersgrans;
	var $mobilabb;
	var $abbtelia;
	var $abbtele2;
	var $abbhalebop;
	var $check_extra_freight;
	var $accept_canon;
	var $isInsurable;
	var $isGiftcard;
	var $isCanon30Campaign;
	var $isCanon36Campaign;
	var $isMobileSvea;

function __construct() {
        
        /**
         * Connections not used anymore, use Db-class instead, e.g. Db::getConnection()
	$this->conn_my = Db::getConnection();
	//$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKat�ms#1");
	//@mssql_select_db ("cyberphoto", $this->conn_ms);
	$this->conn_fi = $this->conn_ms;	
        */
        // No database selected, use mysql_db_query or e.g: cyberorder.kund when querying 
        //$this->conn_master = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');

        
	$this->mobilabonnemang = false;
	$this->aldersgrans = false;
	$this->mobilabb = false;
	$this->abbtelia = false;
	$this->abbtele2 = false;
	$this->abbhalebop = false;
	$this->check_extra_freight = false;
	$this->accept_canon = false;
	$this->isInsurable = false;
	$this->isGiftcard = false;
	$this->isCanon30Campaign = false;
	$this->isCanon36Campaign = false;
	$this->isMobileSvea = false;
}
function datebetweenInpris($datum) {
	$date = strtotime($datum); 
	$secs = time() - $date; 
	$days = $secs / 60 / 60 / 24; 
	$varde = ceil($days);
	echo $varde . " dagar";
	if ($varde > 182) { ?>
	<a onMouseOver="return escape('<b>Varningsklocka!</b><br><br>Inpriset p� denna produkt �r mer �n ett halv�r gammalt.<br><br>B�r kollas upp omg�ende!')">
	<?php
	echo "&nbsp;<b><font color='red'>** Varning! **</font></b></a>";
	}
} 
function checkSoldLastMonth($artnr) {
	
	$select  = "SELECT antalManad1  ";
	$select .= "FROM cyberphoto.mostSoldArticles ";
	$select .= "WHERE artnr = '" . $artnr . "' ";
	
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);

	if (mysqli_num_rows($res) > 0) {
		echo $row->antalManad1 . " st/m�n";
	} else {
		echo "Inga s�lda";
	}
}
function soldLastMonthPricelist($artnr,$lagersaldo) {
	
	$select  = "SELECT antalManad1  ";
	$select .= "FROM cyberphoto.mostSoldArticles ";
	$select .= "WHERE artnr = '" . $artnr . "' ";
	
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);
	
	// echo $select;

	if (mysqli_num_rows($res) > 0) {
		// return $row->antalManad1 . " st/m�n";
		if ($row->antalManad1 > 0) {
			return "S: " . $row->antalManad1;
		} else {
			if ($lagersaldo > 0) {
				return "S: <span class=\"mark_red\">0</span>";
			} else {
				return "&nbsp;";
			}
		}
	} else {
		return "&nbsp;";
	}
}
function addNyhetsbrev($name, $email, $fi) {
	global $fi, $sv, $no;
	
	$email = trim($email);
	$email = Tools::sql_inject_clean($email);
	
	$query = "SELECT * FROM cyberorder.Nyhetsbrev WHERE email = '" . $email . "'";
	//echo $query;
	// $res = @mssql_query ($s, $this->conn_ms);
	$res = mysqli_query(Db::getConnection(false), $query);
    $lang = Locs::getLang();
    $cntry = Locs::getCountry();
		
	// if (@mssql_num_rows($res) == 0) {		
	if (@mysqli_num_rows($res) == 0) {		
	    $query = "INSERT INTO cyberorder.Nyhetsbrev (email, name, dateAdded, confirmed, mailad, cntry, lang) values (" .
	            "'" . $email . "', '" . $name . "', '" . date("Y-m-d H:i:s")  . "', 0, 0, '" . $cntry . "', '" . $lang . "')";
	    //echo $s;	
		// @mssql_query($s);
		$res = mysqli_query(Db::getConnection(true), $query);		
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

	global $confirm, $salt, $kundnrsave, $conn_master, $internal_pincode;
	
	$passwd = trim($passwd);
	// $passwd = $this->sql_inject_clean($passwd);
	$passwd = Tools::sql_inject_clean($passwd); // rensa bort tecken som vi inte godk�nner
	$kundnrtest = trim($kundnrtest);
	// $kundnrtest = $this->sql_inject_clean($kundnrtest);
	$kundnrtest = Tools::sql_inject_clean($kundnrtest); // rensa bort tecken som vi inte godk�nner
	$secure = new PasswordEncryption();
	if (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR']) && $kundnrtest == "kontant") {
		$confirm=1;
		$kundnrsave = 1004367; // detta �r kundnumret f�r kontantkunder i shopen
	// } elseif (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR']) && $internal_pincode != "") {
	} elseif ((CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) || CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) && $internal_pincode != "") {
		if ($internal_pincode == 4545) {
			$confirm=1;
			$kundnrsave = $kundnrtest;
		} else {
			$this->confirm = 2;
			$confirm = 2;
		}
	} elseif ($passwd == ""  || $kundnrtest == "") {
		$this->confirm = 2;
		$confirm = 2;
	} else {
		
		$select = "SELECT kundnr, kundid, kundid_encr, userName, trials from cyberorder.Kund ";
		$select .= "WHERE kundnr like '$kundnrtest' AND borttagen = 0";
                
		$res = mysqli_query(Db::getConnection(true), $select);
		if (mysqli_num_rows($res) > 0) { // @ s� att det inte syns om det blir fel. 
		
			extract(mysqli_fetch_array($res));
			
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo "Kundnummer: " . $kundnr . "<br>\n";
				echo "L�sen: " . $kundid . "<br>\n";
				echo "Inknappat: " . $passwd . "<br>\n";
				exit;
			}
						/**
						// Funktion som fanns f�r att kryptera l�senord automatiskt �t kunden s� kunden skulle slippa best�lla nytt l�senord
			            if ($kundid_encr == "" && $kundid != "" && $kundid != "nkN9RbBQ19sUs") {
                            $kundid_encr = $secure->encrypt_password($kundid);
                            $updt = "UPDATE cyberorder.Kund Set kundid_encr = '" . $kundid_encr . "' WHERE kundnr = " . $kundnr;
                            mysqli_query(Db::getConnection(true), $updt);
                            //echo $updt;
                            //exit;
                        }
						*/
			//if ($kundid_encr == "" && ($kundid == "" || $kundid == "nkN9RbBQ19sUs")) {
			if ($kundid_encr == "") { // Kunden har inget l�senord 
				$confirm = 5;
			} else {

				// kontrollera s� att kunden inte har missat sitt l�seord f�r m�nga g�nger.
				if ($trials <= 5)  {
                                    
				// kontrollera om l�senordet �r r�tt
                                
					//if ($passwd == $kundid ) {
                    if ($secure->validate_password($passwd, $kundid_encr)) {
                                            //echo "yes";
                                            //exit;
						if ($trials > 0) {
							$update = "update cyberorder.Kund set trials=0 where kundnr=$kundnr";
							mysqli_query(Db::getConnection(true), $update);
						}
						$confirm = 1;
						$kundnrsave = $kundnr; 
					} else {
						$newtrials = $trials+1;
						$update = "update cyberorder.Kund set trials=$newtrials where kundnr=$kundnr";
						mysqli_query(Db::getConnection(true), $update);
						$confirm='2';
					}
				} else {
					$confirm=3;
				}
			}
			
		} else {
		
			$select = "SELECT kundnr, kundid, kundid_encr, userName, trials FROM cyberorder.Kund ";
			$select .= "WHERE userName = '$kundnrtest' AND borttagen = 0";

			$res = mysqli_query(Db::getConnection(true), $select);
			if (mysqli_num_rows($res) > 0) {

				extract(mysqli_fetch_array($res));
                        if ($kundid_encr == "" && $kundid != "" && $kundid != "nkN9RbBQ19sUs") {
                            // create encrypted password if it doesn't exist
                            $kundid_encr = $secure->encrypt_password($kundid, $salt);                                                        
                            $updt = "UPDATE cyberorder.Kund Set kundid_encr = '" . $kundid_encr . "' WHERE kundnr = " . $kundnr;
                            mysqli_query(Db::getConnection(true), $updt);
                        }

				//if ($kundid_encr == "" && ($kundid == "" || $kundid == "nkN9RbBQ19sUs")) {
				if ($kundid_encr == "") {
					$confirm = 5; // l�senord saknas

				} else {

					// kontrollera s� att kunden inte har missat sitt l�senord f�r m�nga g�nger.
					if ($trials <= 5)  {

					// kontrollera om l�senordet �r r�tt
                                                
						if ($secure->validate_password($passwd, $kundid_encr)) {
                                                    //echo "yes";
                                                    //exit;
							if ($trials > 0) {
								$update = "update cyberorder.Kund set trials=0 where kundnr=$kundnr";
								mysqli_query(Db::getConnection(true), $update);
								}
							$confirm=1;
							$kundnrsave = $kundnr; 
                		
						} else {

                            $newtrials = $trials+1;
							$update = "update cyberorder.Kund set trials=$newtrials where kundnr=$kundnr";
							mysqli_query(Db::getConnection(true), $update);
							$confirm=2;
						}

					} else {
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
 * plockare fram kundinfo fr�n databasen
 *
 * @param boolean $lastInfo om senaste leverans och betals�tt skall plockas fram. 
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
	$freight, $pay, $fi, $sv, $no, $old_villkor_id, $old_personnr, $old_firstName, $old_lastName, $old_firstNameDel, $old_lastNameDel, $old_foretag, $old_avisera, 
	$old_forsakringserbjudande, $old_splitOrder, $conn_master;
	

	$senasteLevsatt = 0; $senasteBetalsatt = 0;
	if ($confirm == 1) {

		// Plocka f�rst fram kunduppgifterna

		$select  = "SELECT namn, co, adress, postnr, postadr, k.land_id, email, telnr, orgnr, mail_send, faktura, ";
		$select .= "lnamn, lco, ladress, lpostnr, lpostadr, lland_id, lemail, ltelnr, land, land_fi, erordernr, erref, faktlev, ";
		$select .= "savelogin, land_fi, foretag, userName, avtalskund, mobilnr, sms, nyhetsbrev, villkor_id, ";
		$select .= "firstName, lastName, firstNameDel, lastNameDel, electronic_invoice, forsakringserbjudande ";
		$select .= "FROM cyberorder.Kund k LEFT JOIN cyberorder.Land l ON k.land_id = l.land_id ";
		$select .= "WHERE k.kundnr = $kundnrsave ";

		$res = mysqli_query(Db::getConnection(true), $select);
		$row = mysqli_fetch_array($res);

		// returns selected variabls prefixed $old_, e.g. $old_namn
		if ((mysqli_num_rows($res)) > 0)
		extract($row, EXTR_PREFIX_ALL, "old");
		$old_orgnr = trim($old_orgnr);
		$old_personnr = $old_orgnr;
		$old_sms = $old_sms;
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
		// if ($old_forsakringserbjudande != 0 && $old_forsakringserbjudande != -1)
		if ($old_forsakringserbjudande != -1)
			$old_forsakringserbjudande = 0;
		
		// v44 - alltid p� �ven om kunden valt det tidigare
		$old_forsakringserbjudande = -1;
		if (!$sv && $fi)
			$old_land = $old_land_fi;
		// F�r gamla kunder som saknar leveransadress p� sina ordrar.
		if (($kundrsave < 105138) AND ($old_lnamn == "" AND $old_lco == "" AND $old_ladress == "" AND $old_lpostnr == "" AND $old_lpostadr == "") )

		{
			$this->check_lev_adress($kundnrsave);

		}

		// Vet inte riktigt hur man skriver en helt effektiv fr�ga, d�rf�r, detta extra
		$select2 = "SELECT land, land_fi from Land WHERE land_id = $old_lland_id";
		$res2 = @mysqli_query(Db::getConnection(), $select2);
		$row2 = @mysqli_fetch_object($res2);
		$old_lland = $row2->land;
		$old_lland_fi = $row2->land_fi;
		if (!$sv && $fi)
			$old_lland = $old_lland_fi;
		// leta reda p� eventuellt senaste orderuppgifterna
		$select = "SELECT betalsatt_id, leveranssatt_id, ordernr FROM Ordertabell WHERE kundnr = " . $kundnrsave . " ORDER BY ordernr DESC";
		
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
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
	$kundnr, $newcust, $kundnrsave, $one_stop,
	$wrongpassword, $change_passw, $new_faktlev, $new_erordernr, $new_erref, $new_kommentar, $new_lnamn,
	$userName, $userTaken, $foretag, $new_userName, $new_nyhetsbrev, $new_mobilnr, $new_sms, $fi, $sv, $no, $pay, $kreditor, 
	$new_firstName, $new_lastName, $new_firstNameDel, $new_lastNameDel, $new_personnr, $old_foretag, $new_salary, $old_salary, 
	$new_avisera, $new_forsakringserbjudande, $conn_master, $new_nyhetsbreverbjudande, $new_delivery_address, $new_invoice_address;
	
	$userTaken = "";
	$secure = new PasswordEncryption();
	// f�r att slippa skriva namnet hela tiden
	$k = $_SESSION['CKreditor'];
	if (($pay == "sveainvoice" || preg_match("/sveapayment/i", $pay) || $one_stop == "yes" || ($this->mobilabonnemang && $old_foretag != -1) || 
		$this->aldersgrans || $_SESSION['MOB_CONFIRMED']) && isset($k->cust_addresses)) {
		
		if (!isset($new_invoice_address) || $new_invoice_address == "noinvoice")
			$new_invoice_address = 0;		
		if (!isset($new_delivery_address) || $new_delivery_address == "nodelivery") 
			$new_delivery_address = $new_invoice_address;
		
		if ($old_foretag != -1) {
			$new_firstName = $k->sveaInvoiceAddress[$new_invoice_address]->FirstName;
			$new_lastName = $k->sveaInvoiceAddress[$new_invoice_address]->LastName;
		}  
		
		if ($old_foretag == -1) {
			$new_namn = $k->sveaInvoiceAddress[$new_invoice_address]->LegalName;
		} else {
			$new_namn = $k->sveaInvoiceAddress[$new_invoice_address]->FirstName . " " . $k->sveaInvoiceAddress[$new_invoice_address]->LastName;
		}
		
		if ($k->sveaInvoiceAddress[$new_invoice_address]->AddressLine1 != "") {
			$new_co = $k->sveaInvoiceAddress[$new_invoice_address]->AddressLine1;
			$new_adress = $k->sveaInvoiceAddress[$new_invoice_address]->AddressLine2;
		}	else {
			$new_co = $k->sveaInvoiceAddress[$new_invoice_address]->AddressLine2;
			$new_adress = "";
		}
		
		$new_postnr = $k->sveaInvoiceAddress[$new_invoice_address]->Postcode;
		$new_postadr = $k->sveaInvoiceAddress[$new_invoice_address]->Postarea;	 		 		

		if ($k->sveaInvoiceAddress[$new_delivery_address]->AddressLine1 != "") {
			$new_lco = $k->sveaInvoiceAddress[$new_delivery_address]->AddressLine1;
			$new_ladress = $k->sveaInvoiceAddress[$new_delivery_address]->AddressLine2;
		}	else {
			$new_lco = $k->sveaInvoiceAddress[$new_delivery_address]->AddressLine2;
			$new_ladress = "";
		}
		
		$new_lpostnr = $k->sveaInvoiceAddress[$new_delivery_address]->Postcode;
		$new_lpostadr = $k->sveaInvoiceAddress[$new_delivery_address]->Postarea;
		

		if ($fi) {
			$new_land = 'Finland';
			$new_lland = 'Finland';
        } else if ($no) {
			$new_land = 'Norge';
			$new_lland = 'Norge';
        } else {
			$new_land = 'Sverige';
			$new_lland = 'Sverige';
		}		
		//$new_namn = $new_firstName . " " . $new_lastName;
		$new_firstNameDel = $new_firstName;
		$new_lastNameDel = $new_lastName;		
		$new_lnamn = $new_namn;
		if ($k->cust_pno != "") {
				$new_personnr = $k->cust_pno;
		}
	} elseif  (($pay == "invoiceme" || $pay == "sveainvoice" || preg_match("/sveapayment/i", $pay) || eregi("avbetalning", $pay) || $pay == "faktura_kampanj") && $fi)  {
		$new_namn = $new_firstName . " " . $new_lastName;
		/** Detta beh�vs inte, det l�ggs ��nd� ihop l�ngre ner		
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
	
	// tag bort mellanslag f�re och efter p� l�senord och anv�ndarnamn
	$userName = trim($userName);
	$new_userName = trim($new_userName);
	$new_passw = trim($new_passw);
	$change_passw = trim($change_passw);
	if ($new_passw != "") {
		$kundid_encr = $secure->encrypt_password($new_passw);
	}
	if ($change_passw != "") {
		$kundid_encr = $secure->encrypt_password($change_passw);
	}
	$new_namn = trim($new_namn);
	$new_namn = str_replace("'", "", "$new_namn");
	$new_lnamn = trim($new_lnamn);
	$new_lnamn = str_replace("'", "", "$new_lnamn");
	$new_mobilnr = trim($new_mobilnr);
	
	$new_salary = trim($new_salary);
	$old_salary = $new_salary;
	$new_personnr = trim($new_personnr);
	if ($old_foretag == 0)
		$new_orgnr = $new_personnr;
	
	if ($old_salary == "") $old_salary = 0;
        /**
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
         *
         */
        $currency = Locs::getCurrency();
        $lang = Locs::getLang();
	
	$select = "SELECT land_id FROM Land WHERE land = '$new_land'";
	$row = mysqli_fetch_object(mysqli_query(Db::getConnection(), $select));
	$land_id = $row->land_id;

	$select = "SELECT land_id FROM Land WHERE land = '$new_lland'";
	$row = mysqli_fetch_object(mysqli_query(Db::getConnection(), $select));
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
				
			if ($new_nyhetsbrev == "yes" || $new_nyhetsbreverbjudande == "yes")
				$new_nyhetsbrev = -1;
			else
				$new_nyhetsbrev = 0;
			//temp
			//$new_nyhetsbrev = -1;
			
			if ($new_sms == "yes")
				$sms = -1;
			else
				$sms = 0;
			if ($new_passw == "") {
				$new_passw = $this->generate_pass(5);
				$kundid_encr = $secure->encrypt_password($new_passw);
			}
			//$select = "SELECT max(kundnr) as kundnr FROM Kund ";
			$select = "SELECT max(kundnr) as kundnr FROM cyberorder.Kundnr ";
			//echo $select;
			$row = (mysqli_fetch_object(mysqli_query(Db::getConnection(true), $select)));
			$newkundnr = $row->kundnr;
			$newkundnr++;
			$datumet = date("Y-m-d H:i:s");
			
			$insert = "INSERT INTO cyberorder.Kundnr (kundnr, usr, createdDate) values (" . $newkundnr . ", 'WO', '" . $datumet . "')";
			//echo $insert;
			$res2 = mysqli_query(Db::getConnection(true), $insert);

			$insert  = "INSERT INTO cyberorder.Kund (kundnr, namn, co, adress, postnr, postadr, land_id, email, telnr, orgnr, erref, erordernr, ";
			$insert .= "lnamn, lco, ladress, lpostadr, lpostnr, lland_id, lemail, ltelnr, faktlev, savelogin, userName, foretag, ";
			$insert .= " skapad, nyhetsbrev, mobilnr, sms, lang, firstName, lastName, firstNameDel, lastNameDel, forsakringserbjudande, ";
			if ($new_avisera != "") 
				$insert .= "electronic_invoice, ";			
			$insert .= " salary, currency, created, kundid_encr) values ";
			$insert .= "";
			$insert .= " ('$newkundnr', '$new_namn', '$new_co', '$new_adress', ";
			$insert .= "'$new_postnr', '$new_postadr', '$land_id', '$new_email', '$new_telnr', '$new_orgnr', '$new_erref', '$new_erordernr', ";
			$insert .= "'$new_lnamn', '$new_lco', '$new_ladress', '$new_lpostadr', '$new_lpostnr', '$lland_id', '$new_email', ";
			$insert .= "'$new_telnr', '$new_faktlev', '$spara', '$userName', $old_foretag, '$datumet', $new_nyhetsbrev, '$new_mobilnr', $sms, '$lang', ";
			$insert .= " '$new_firstName', '$new_lastName', '$new_firstNameDel', '$new_lastNameDel', $new_forsakringserbjudande, ";
			if ($new_avisera != "") 
				$insert .= "$new_avisera, ";

			$insert .= " $old_salary, '$currency', now(), '$kundid_encr')";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
				echo $insert;
				exit;
			}
			if ($res2)
				$res = mysqli_query(Db::getConnection(true), $insert);

			if ($res && $res2) {
				$kundnrsave = $newkundnr;
				$confirm = 1;

				//$old_erordernr = $new_erordernr;
				//$old_erref = $new_erref;
				//$old_kommentar = $new_kommentar;
				// 121128, tar bort nedan i tv� veckor och kollar reaktionerna
				// $this->maila_kundinfo($newkundnr, $new_passw, $mailpassw);
			}
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
				Tools::print_rw(get_defined_vars());
				exit;				
			}
		}
	
	} else { // Om uppdatera gamla uppgifter

		// kolla f�rst om inloggad, annars
		if ((ereg("[0-9]{4,}", $kundnrsave)) && ($confirm == 1)) {
			;
		} else {
			// Testa det angivna l�senordet

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

			$update  = "UPDATE cyberorder.Kund set namn = '$new_namn', co = '$new_co', adress = '$new_adress', postnr = '$new_postnr', ";
			$update .= "postadr = '$new_postadr', land_id = '$land_id', email = '$new_email', telnr = '$new_telnr', ";
			$update .= "orgnr = '$new_orgnr', faktlev = '$new_faktlev', ";
			$update .= "lnamn = '$new_lnamn', lco = '$new_lco', ladress = '$new_ladress', lpostadr = '$new_lpostadr', ";
			$update .= "lpostnr = '$new_lpostnr', lland_id = '$lland_id', lemail = '$new_email', ";
			$update .= "ltelnr = '$new_telnr', ";
			if ($change_passw != "" && $change_passw != " ") {
				//$update .= "kundid = '$change_passw', ";
				$update .= "kundid_encr = '$kundid_encr', ";
			}
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
			//echo "h�r: " . $new_avisera;
			//echo $update;
			
			$res = mysqli_query(Db::getConnection(true), $update);

			// maila ut den nya kundinfon

			//$this->maila_kundinfo($newkundnr, $passw, $mailpassw);

			$order_erordernr = $new_erordernr;
			$order_erref = $new_erref;
			$order_kommentar = $new_kommentar;

		} else {

			$confirm = 1;
			$wrongpassword = 'yes';
		}

	}
	// if ($new_nyhetsbrev == -1)
	if ($new_nyhetsbrev == -1 || ($new_email != "" && $one_stop == "yes")) // 110819 lagt till s� att alla personer som handlar via snabbkassan f�r nyhetsbrevet
		$this->addNyhetsbrev($new_namn, $new_email, $fi);

}

function maila_kundinfo($kundnr, $passw, $mailpassw) {
    global $conn_master;
    // TODO: �vers�ttningar? 
    $select = "SELECT * from cyberorder.Kund WHERE kundnr = '$kundnr'";
    $res = mysqli_query(Db::getConnection(true), $select);
    $row = mysqli_fetch_object($res);
    if ($row->email != ''): 	
	
    $selland1 = "SELECT land FROM Land where land_id = '$row->land_id'";
    $res1 = mysqli_query(Db::getConnection(), $selland1);
    $row1 = mysqli_fetch_object($res1);
    $land = $row1->land;

    $selland2 = "SELECT land FROM Land where land_id = '$row->land_id'";
    $res2 = mysqli_query(Db::getConnection(), $selland2);
    $row2 = mysqli_fetch_object($res2);
    $lland = $row2->land;

$message = <<<eof
Hej!

H�r kommer era kunduppgifter

Kundnummer: $kundnr
Anv�ndarnamn: $row->userName
eof;
/*
$mailpassw = true;
if ($mailpassw && $row->kundid != '')
	{ $message .= $row->kundid . "\n"; }
elseif ($row->kundid == '')
	{ $message .= "ej angett\n"; }
elseif ($mailpassw == 'no')
	{ $message .= "visas ej enl. �nskem�l\n"; }
*/
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

�vriga uppgifter:
$row->email
$row->telnr

eof;
}

$subject = "Kundinfo fr�n CyberPhoto";
// $extra = "From: order@cyberphoto.se \n ";
$extra = "From: kundtjanst@cyberphoto.se \n ";

mail($row->email, $subject, $message, $extra);
	endif; 
}

function check_lev_adress($kundnrsave) {
    
// Not used? 
global $old_lnamn, $old_lco, $old_lpostnr, $old_lpostadr, $old_lland_id, $old_ladress, $old_email, $old_telnr, 
$old_namn, $old_co, $old_adress, $old_postnr, $old_postadr, $old_land_id, $conn_master; 

// Kontrollera f�rst om det finns en order i "nya" ordertabellen 
$select = "SELECT max(ordernr) as ordernr from cyberorder.Ordertabell WHERE kundnr = '$kundnrsave'";
$res = mysqli_query(Db::getConnection(true), $select);
$row = mysqli_fetch_object($res);

if (($row->ordernr) > '0') {
	$ordernr = $row->ordernr;
   	$oldorders = 'yes'; # konstaterar att det finns gammal order
	}
// Annars se om det finns en order i "gamla" ordertabellen
else {

	$select = "SELECT max(ordernr) as ordernr from cyberorder.Ordertabell_gamla WHERE kundnr = '$kundnrsave'";
	$res = mysqli_query(Db::getConnection(true), $select);
    	$row = mysqli_fetch_object($res);
    	if (($row->ordernr) > '0') {
		$ordernr = $row->ordernr;
		$oldorders = 'yes'; // konstaterar att det finns gammal order
		$old = 'yes';	    // konstaterar att det �r en order ur "gamla" ordertabellen
		}

	}

if (($old) && ($oldorders)) { // om det finns order och det �r ur gamla tabellen

	$select = "SELECT lnamn, lco, ladress, lpostnr, lpostadr, land_id FROM cyberorder.Ordertabell_gamla WHERE ordernr = '$ordernr'";
	}

elseif ($oldorders) { // om senaste order �r ur nya listan
	$select = "SELECT lnamn, lco, ladress, lpostnr, lpostadr, land_id FROM cyberorder.Ordertabell WHERE ordernr = '$ordernr'";
	}

# Om inget av ovanst�ende g�ller s� finns det tydligen inga gamla ordrar
# d� anv�nder vi helt enkelt faktura adressen som leveransadress
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
	// Plocka fram data f�r ovanst�ende fr�ga 
	$res = mysqli_query(Db::getConnection(true), $select);
	#echo $select;
	$row = mysqli_fetch_object($res);

	$old_lnamn = $row->lnamn;
	$old_lco = $row->lco;
	$old_ladress = $row->ladress;
	$old_lpostnr = $row->lpostnr;
	$old_lpostadr = $row->lpostadr;
	$old_lland_id = $row->land_id;

endif; 

$update = "UPDATE cyberorder.Kund set lnamn = '$old_lnamn', lco = '$old_lco', ladress = '$old_ladress', ";
$update .= "lpostnr = '$old_lpostnr', lpostadr = '$old_lpostadr', lland_id = '$old_lland_id', ";
$update .= "ltelnr = '$old_telnr', lemail = '$old_email' ";
$update .= "WHERE kundnr = '$kundnrsave' ";

mysqli_query(Db::getConnection(true), $update);

}

function testUsername ($userName) {
	global $conn_master;
	
	// Testa om anv�ndarnamnet redan �r taget samt att anv�ndarnamnet inte �r samma som n�gon annans kundnummer
	
	if ($userName == "")
		$returnvalue = false;
	else {
	
		$test = "SELECT userName FROM cyberorder.Kund WHERE userName = '$userName' ";
		$resTest = mysqli_query(Db::getConnection(true), $test);

		if (mysqli_num_rows($resTest) > 0) {
			$test2 = "SELECT kundnr FROM cyberorder.Kund WHERE kundnr like '$userName' ";
			$resTest2 = mysqli_query(Db::getConnection(true), $test2);
			
			if (mysqli_num_rows($resTest2) > 0) 
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
	      elseif ($betalsatt == 'klarnakonto') $betalsatt_id = 14;
	      elseif ($betalsatt == 'foretagshyra') $betalsatt_id = 11;
	      elseif ($betalsatt == 'c') $betalsatt_id = 2;
	      elseif ($betalsatt == 'kontant') $betalsatt_id = 2;
	      elseif ($betalsatt == 'sveainvoice') $_SESSION['old_foretag'] == -1 ?  $betalsatt_id = 23 : $betalsatt_id = 20; 
	      elseif (preg_match("/sveapayment/i", $betalsatt)) $betalsatt_id = 21;
	      elseif ($betalsatt == 'paypal') $betalsatt_id = 22;
		  elseif ($betalsatt == 'swish') $betalsatt_id = 24;
	      else $betalsatt_id = 0; 
	      //foretagshyra
	    }
	}
	return $betalsatt_id;
}
function getLevSatt($leveranssatt_id) {
	// not used ? 
	$select = "SELECT artnr FROM Leveranssatt WHERE leveranssatt_id = " . $leveranssatt_id;
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);
	//echo $select;
	if (mysqli_num_rows($res) > 0)
		return $row->artnr;
	else
		return "";		
}
function getLeveranssatt_id ($fraktArtnr, $fi=null) {
        // 3 = default leveranssatt_id
	if ($fraktArtnr == "")
		return 3;
	$select = "SELECT * FROM Leveranssatt WHERE ";
	if (eregi("pf$", $fraktArtnr)) 		
		$select .= " artnrPf = '" . $fraktArtnr . "'";
	else 	
		$select .= " artnr = '" . $fraktArtnr . "'";
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);
        if ($row->leveranssatt_id!= "")
            return $row->leveranssatt_id;		
        else
            return 3;
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
	elseif ($betalsatt_id == 20)
		$pay = "sveainvoice";
	elseif ($betalsatt_id == 23)
		$pay = "sveainvoice";
	elseif ($betalsatt_id == 21)
		$pay = "sveapayment";
	elseif ($betalsatt_id == 22)
		$pay = "paypal";
	elseif ($betalsatt_id == 24)
		$pay = "swish";
	else
		$pay = "";
    
	return $pay;
	
}
function getBetalsattet ($betalsatt_id) {
	global $sv, $fi, $no;
	
	$select = "SELECT betalsatt, betalsatt_fi, betalsatt_no FROM cyberphoto.Betalsatt WHERE betalsatt_id = " . $betalsatt_id;
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);
	if ($sv)
            return $row->betalsatt;
	else if ($fi)
            return $row->betalsatt_fi;
        else if ($no)
            return $row->betalsatt_no;
}

function getArticleInfo($artnr) {	
	
	$select = "SELECT * FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);
	return $row;
}
function getArticleInfo_fi($artnr) {
	
	$select = "SELECT * FROM Artiklar, Artiklar_fi where Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.artnr = '$artnr' ";
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);
	return $row;
}

function getArticleSpec($artnr, $variable) {
	
	$select = "SELECT $variable FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);
	return round($row->$variable);
}

function session_clear() {
  	
	 unset($_SESSION['kundnrsave'],$_SESSION['confirm'],$_SESSION['old_namn'],$_SESSION['old_co'],$_SESSION['old_adress'],$_SESSION['old_postnr'],
	 $_SESSION['old_postadr'],$_SESSION['old_land_id'],$_SESSION['old_email'],$_SESSION['old_telnr'],$_SESSION['old_orgnr'],$_SESSION['old_lnamn'],
	 $_SESSION['old_lco'],$_SESSION['old_ladress'],$_SESSION['old_lpostnr'],$_SESSION['old_lpostadr'],$_SESSION['old_lland_id'],$_SESSION['old_ltelnr'],
	 $_SESSION['old_lemail'],$_SESSION['old_levadress'],$_SESSION['old_faktadress'],$_SESSION['old_land'],$_SESSION['old_land_fi'],$_SESSION['old_lland'],
	 $_SESSION['old_lland_fi'],$_SESSION['old_faktura'],$_SESSION['order_erref'],$_SESSION['order_erordernr'],$_SESSION['order_kommentar'],
	 $_SESSION['paketref'],$_SESSION['betalsatt'],$_SESSION['spara_uppgifter'],$_SESSION['old_faktlev'],$_SESSION['intern'],$_SESSION['old_foretag'],
	 $_SESSION['old_userName'],$_SESSION['SesCc'],$_SESSION['SesExpM'],$_SESSION['SesExpY'],$_SESSION['SesCcCode'],$_SESSION['kortfax'],
	 $_SESSION['old_nyhetsbrev'],$_SESSION['CcName'],$_SESSION['old_avtalskund'],$_SESSION['old_mobilnr'],$_SESSION['old_sms'],$_SESSION['old_nyhetsbreverbjudande'],
	 $_SESSION['old_forsakringserbjudande'],$_SESSION['senasteLevsatt'],$_SESSION['senasteBetalsatt'],$_SESSION['pay'],$_SESSION['freight'],
	 $_SESSION['pallDelivery'],$_SESSION['discountCode'],$_SESSION['discountCodeStatus'],$_SESSION['campaignNr'],$_SESSION['campaignQuestionResult'],
	 $_SESSION['basketValue'],$_SESSION['old_villkor_id'],$_SESSION['old_forsakring_new'],$_SESSION['old_choose_villkor'],$_SESSION['old_firstName'],
	 $_SESSION['old_lastName'],$_SESSION['old_firstNameDel'],$_SESSION['old_lastNameDel'],$_SESSION['old_email_test'],$_SESSION['old_personnr'],
	 $_SESSION['old_salary'],$_SESSION['old_avisera'],$_SESSION['old_splitOrder'],$_SESSION['articles'],$_SESSION['old_abbtype'],$_SESSION['old_abbnumber'],
	 $_SESSION['old_preskort'],$_SESSION['old_preskortdel1'],$_SESSION['old_preskortdel2'],$_SESSION['old_preskortdel3'],$_SESSION['old_preskortdel4'],$_SESSION['old_presenkort'],
	 $_SESSION['old_giftcardrebate'],$_SESSION['old_operator'],$_SESSION['old_one_stop'],$_SESSION['old_abbpersonnumber'],$_SESSION['old_invoice_address'],$_SESSION['old_delivery_address'],
	 $_SESSION['old_invoice_addresselector']);

	 // echo ($_SESSION['kundnrsave']) . "<br>";

	 // if session exists, unregister all variables that exist and destroy session 
	 
	 /*
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
	*/
} 


function viewPacketDelivery($artnr, $mangd, $fi) {
	global $count, $lagersaldo, $lagersaldo_fi, $bestallt, $lev_datum, $lev_datum_norm, $lev_datum_norm_fi, $bestallningsgrans, $bestallningsgrans_fi, $fi, $sv, $no, $bestallt_fi;		
	
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
	
	$res = mysqli_query(Db::getConnection(), $select);
	if (mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)):
		$description = ""; $bestallt_fi = 0;
		extract($row);
//		echo $bestallt_fi;
		$count = $antal*$mangd;
		if ($tillverkare != '.')
			$description = $tillverkare . " ";

		if (!$sv && $fi && $beskrivning_fi != "")
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
		  <a href="mailto:order@cyberphoto.se"><?php if ($fi == 'yes'): ?>Information saknas, kontakta cyberphoto f�r mer info<?php else: ?>Information saknas, kontakta cyberphoto f�r mer info<?php endif; ?></a>
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
	Input: $pass_len, L�ngd p� l�senord
	Output: $nps, l�senord best�ende av stora och sm� bokst�ver och siffror
	
	*/
$nps = ""; 

// Seed the random number generator
mt_srand ((double) microtime() * 1000000); 

while (strlen($nps)<$pass_len) {
	
	// Ge $c ett v�rde fr�n slumpm�ssigt valt ASCII v�rde
	// bara A-Z, a-z och siffror f�r att inte f� n�gra
	// skumma tecken
	$randvalue = mt_rand (48, 122);
	$c = chr($randvalue); 
	
	// L�gg till p� $nps om det �r i r�tt format

	if (eregi("[a-z0-9]", $c)) {
		$nps = $nps.$c;
		// f�renkla f�r kunden genom att bara anv�nda sm� bokst�ver
		$nps = strtolower($nps);
	}
	
}
 	return ($nps); 
}
function generate_pass_all ($pass_len)  {
	/*
	Input: $pass_len, L�ngd p� l�senord
	Output: $nps, l�senord best�ende av stora och sm� bokst�ver och siffror
	
	*/
$nps = ""; 

// Seed the random number generator
mt_srand ((double) microtime() * 1000000); 

while (strlen($nps)<$pass_len) {
	
	// Ge $c ett v�rde fr�n slumpm�ssigt valt ASCII v�rde
	// bara A-Z, a-z och siffror f�r att inte f� n�gra
	// skumma tecken
	$randvalue = mt_rand (48, 122);
	$c = chr($randvalue); 
	
	// L�gg till p� $nps om det �r i r�tt format

	if (!eregi("\s", $c)) {
		$nps = $nps.$c;
		// f�renkla f�r kunden genom att bara anv�nda sm� bokst�ver
		//$nps = strtolower($nps);
	}
	
}
 	return ($nps); 
}
function viewBasketShort($kundvagn) {
	global $fi, $sv, $no;
	
	$output = "";
	if (ereg ("(grejor:)(.*)", $kundvagn,$matches)) {
		# Split the number of items and article id s into a list
		$orderlista = $matches[2];
		$argument = split ("\|", $orderlista);
	}
        //reverse array to show last article first
        $argument = array_reverse($argument, true);
        
	$goodscounter=0;
	$goodsvalue=0;
	 
	$n = count($argument);
	//for ($i=0; ($i < $n);  $i+=2) {
	for ($i=$n-2; ($i > -1); $i+=-2) {
		$arg = $argument[$i];        # Article id
		$count = $argument[$i+1];    # Keeps track of the number of the same article
		$count = abs($count);
		$select  = "SELECT artnr, beskrivning, kommentar, utpris, utpris_no, tillverkare, frakt, lagersaldo, bestallt, utpris_fi,  ";
		$select .= "lev_datum, bestallningsgrans, lev_datum_normal, frakt FROM Artiklar ";
		$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
                $select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
		$select .= "WHERE artnr='$arg'";
		
		# Alla v�rden f�rsvinner inte i varje loop, s� d�rf�r m�ste vi g�ra enligt nedan
		$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = "";

		$row = mysqli_fetch_array(mysqli_query(Db::getConnection(), $select));
		extract($row);
		if ($fi)
                    $utpris = $utpris_fi;
                else if ($no)
                    $utpris = $utpris_no;                
		
		$goodscounter += 1;
		$goodsvalue += ($utpris*$count);
		
		if ($no) {
			$description = $count . "stk ";
		} else {
			$description = $count . "st ";
		}
		
		if ($tillverkare != '.')
			$description .= $tillverkare . " ";
		$description .= $beskrivning;
		
		if (strlen($description) >= 20)
			$description = substr ($description, 0, 20) . "..";

		if (!eregi("frakt", $artnr)) {
		$output .= "<option value=\"\">$description</option>\n";
	
	
		}
	}

	return $output;

}
function getArticleInfoBuy ($artnr) {
	global $kundvagn, $utpris, $moms, $lagersaldo, $lagersaldo_fi, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans, $betalsatt, 
	$PHP_SELF, $bestallningsgrans, $pack, $fi, $sv, $no, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $artnr2, $count, $select, $release;	
	
	$count = 1;


	$select  = "SELECT Artiklar.artnr, Artiklar_fi.artnr_fi, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.utpris, Artiklar_fi.utpris_fi, Tillverkare.tillverkare, ";
	$select .= "Artiklar.datum_utpris, Artiklar.datum_inpris, Artiklar.kategori_id, Artiklar.kortinfo, Artiklar.till_kund, ";
	$select .= "Artiklar.utpris_no, Artiklar.beskrivning_no, ";
	$select .= "Artiklar.no_buy, Artiklar.lagersaldo, Artiklar_fi.lagersaldo_fi, Artiklar.bestallt, Artiklar_fi.bestallt_fi, Artiklar.kommentar,  ";
	$select .= "Artiklar_fi.kommentar_fi, Artiklar.lev_datum_norm, Artiklar_fi.lev_datum_norm_fi, Artiklar.releaseDate, ";                                              
	$select .= "Artiklar.bestallningsgrans, Artiklar.lev_datum_normal, Moms.momssats, Moms.momssats_fi, Moms.momssats_no, Artiklar.utgangen, Artiklar_fi.utgangen_fi, ";
	$select .= "Artiklar.artnr_lev, Artiklar.grossist_id, Leverantor.grossist, Leverantor.bestallare, Leverantor.telefon, Leverantor.ordertel, Artiklar.m_product_id, ";
	$select .= "Artiklar.isSalesBundle, Artiklar.spec20, Artiklar.bild, Artiklar.no_buy_no, Artiklar_fi.no_buy_fi, ";
	$select .= "Artiklar.ej_med, Artiklar.ej_med_no, Artiklar_fi.ej_med_fi, Artiklar.isParent, Artiklar.artnr_parent, Artiklar.isTradeIn, ";
	$select .= "Artiklar.motljsk ";
	$select .= "FROM Artiklar ";
	$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Leverantor ON Artiklar.grossist_id = Leverantor.grossist_id ";
	$select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
	$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
	$select .= "WHERE Artiklar.tillverkar_id=Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id ";
	$select .= "AND Artiklar.artnr='$artnr' ";


	if (!CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
		if ($fi) {
			$select .= " AND Artiklar.ej_med=0 AND (demo=0 OR lagersaldo > 0) AND Artiklar_fi.ej_med_fi=0 AND (utgangen=0 OR lagersaldo > 0 )  ";
		} elseif ($no) {
			$select .= " AND Artiklar.ej_med=0 AND (demo=0 OR lagersaldo > 0) AND Artiklar.ej_med_no=0 AND (utgangen=0 OR lagersaldo > 0 )  ";
		} else {
			$select .= " AND Artiklar.ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
		}
	}

	if (!CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) && ($fi || $no)) {
		$select .= "AND Artiklar.artnr = 'nomore' ";
	}
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $select;
		
	}
	$res = mysqli_query(Db::getConnection(), $select);
	
	$rows = mysqli_fetch_object($res);
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $rows->momssats . "<br>";
		echo $rows->momssats_fi . "<br>";
		// exit;
	}
	$lev_datum_norm = $rows->lev_datum_norm;
	$lev_datum_norm_fi = $rows->lev_datum_norm_fi;
	$release = $rows->releaseDate;
	$artnr2 = $rows->artnr;
	return $rows;
	
}
function getDiscountText($rowDiscount, $momssats, $utpris, $showDecoration) {
	global $fi, $sv, $no, $count;
        $curr = Locs::getCurrency();
	
        // TODO: norska
	$txt = "";
	if ($fi) {
		if ($rowDiscount->discountPercent_fi != "") {		
			if ($sv) {
				$txt = round($rowDiscount->discountPercent_fi * 100, 0) .  "% avdraget enligt rabattkod";
			} else {
				$txt = round($rowDiscount->discountPercent_fi * 100, 0) .  "% alennus";
			}
		} elseif ($rowDiscount->discountAmount_fi != "") {
			if ($sv) {
				$txt = round( ($rowDiscount->discountAmount_fi + $rowDiscount->discountAmount_fi * $momssats)*$count, 0) .  " " . $curr . " avdraget enligt rabattkod";
			} else {
				$txt = round( ($rowDiscount->discountAmount_fi + $rowDiscount->discountAmount_fi * $momssats)*$count, 0) .  " " . $curr ." alennus";
			}
			// $utpris = $utpris - $rowDiscount->discountAmount_fi;
		} elseif ($rowDiscount->discountOutprice_fi != "") {
			$utpris = $rowDiscount->discountOutprice_fi;
		}
	} else if ($no) {
		if ($rowDiscount->discountPercent_no != "") {
			$txt = round($rowDiscount->discountPercent_no * 100, 0) .  "% trukket fra med rabattkode"; // Norska...
		} elseif ($rowDiscount->discountAmount_no != "") {
			$txt = round( ($rowDiscount->discountAmount_no + $rowDiscount->discountAmount_no * $momssats)*$count, 0) .  " " . $curr ." trukket fra med rabattkode";
		} elseif ($rowDiscount->discountOutprice_no != "") {
			//echo "h�r";
			$txt = "ny pris med rabattkode. Ordin�r pris: " . round($utpris + $utpris * $momssats, 0) . " " . $curr . "";
			//$txt = "nytt utpris enligt kupong. Ordinarie pris: ";
			//$txt .= $utpris + $utpris * $momssats . " SEK";
			//echo $txt;
		}			                
	} else {
		if ($rowDiscount->discountPercent != "") {
			$txt = round($rowDiscount->discountPercent * 100, 0) .  "% avdraget enligt rabattkod";
		} elseif ($rowDiscount->discountAmount != "") {
			$txt = round( ($rowDiscount->discountAmount + $rowDiscount->discountAmount * $momssats)*$count, 0) .  " " . $curr ." avdraget enligt rabattkod";
		} elseif ($rowDiscount->discountOutprice != "") {
			//echo "h�r";
			$txt = "nytt utpris enligt rabattkod. Ordinarie pris: " . round($utpris + $utpris * $momssats, 0) . " " . $curr . "";
			//$txt = "nytt utpris enligt kupong. Ordinarie pris: ";
			//$txt .= $utpris + $utpris * $momssats . " SEK";
			//echo $txt;
		}		
	}
	if ($txt != "") {
		if ($showDecoration)
			// $txt = "<br><b><font face=\"Verdana, Arial\" color=\"#85000D\" size=\"1\">(" . $txt . ")</font></b>";
			$txt = "<font face=\"Verdana, Arial\" color=\"#85000D\" size=\"1\">(" . $txt . ")</font>";
		else 	
			$txt = "(" . $txt . ")";
	}
	return $txt;		
}
function getDiscountPrice($rowDiscount, $artnr, $kategori_id, $tillverkar_id, $utpris) {
	//echo $utpris;'
	global $fi, $no,  $count;
	
	if (substr($artnr, -3) == "pac") {
		$artnr = preg_replace("/pac/i", "", $artnr);
	}

	if ($rowDiscount->discountCode == "AIRSOFTHELG") {
		if ($kategori_id == 533 || $kategori_id == 536 || $kategori_id == 534 || $kategori_id == 547 || $kategori_id == 549 || $kategori_id == 551
		|| $kategori_id == 1000219 || $kategori_id == 1000221 || $kategori_id == 539 || $kategori_id == 572 || $kategori_id == 573 || $kategori_id == 574
		|| $kategori_id == 535 || $kategori_id == 684 || $kategori_id == 686 || $kategori_id == 567 || $kategori_id == 688 || $kategori_id == 690 || $kategori_id == 691
		|| $kategori_id == 693 || $kategori_id == 694 || $kategori_id == 1000125 || $kategori_id == 561 || $kategori_id == 560 || $kategori_id == 528 || $kategori_id == 734
		|| $kategori_id == 731 || $kategori_id == 724 || $kategori_id == 744 || $kategori_id == 729 || $kategori_id == 730 || $kategori_id == 721 || $kategori_id == 728
		|| $kategori_id == 723 || $kategori_id == 1000235 || $kategori_id == 558 || $kategori_id == 733 || $kategori_id == 732 || $kategori_id == 722 || $kategori_id == 554
		|| $kategori_id == 726 || $kategori_id == 1000132 || $kategori_id == 1000129 || $kategori_id == 1000128 || $kategori_id == 1000131 || $kategori_id == 1000214 || $kategori_id == 727
		|| $kategori_id == 735 || $kategori_id == 542 || $kategori_id == 1000133 || $kategori_id == 634 || $kategori_id == 1000126 || $kategori_id == 1000222
		|| $kategori_id == 1000232 || $kategori_id == 1000225 || $kategori_id == 1000224 || $kategori_id == 1000230 || $kategori_id == 1000231 || $kategori_id == 1000228
		|| $kategori_id == 1000227 || $kategori_id == 1000226 || $kategori_id == 1000234) {
			$utpris = $utpris - $utpris * $rowDiscount->discountPercent;
		}
		return $utpris;
	}
	if ($rowDiscount->discountCode == "SAMORG25") {
		if ($tillverkar_id == $rowDiscount->tillverkar_id && ($kategori_id == 448 || $kategori_id == 344 || $kategori_id == 425 || $kategori_id == 427 || $kategori_id == 426 || $kategori_id == 424 || $kategori_id == 338 || $kategori_id == 238 || $kategori_id == 1000144 || $kategori_id == 1000170 || $kategori_id == 704 || $kategori_id == 423 || $kategori_id == 1000216)) {
			$utpris = $utpris - $utpris * $rowDiscount->discountPercent;
		}
		return $utpris;
	}
	if ($rowDiscount->discountCode == "SAMSKAL50") {
		if ($tillverkar_id == $rowDiscount->tillverkar_id && ($kategori_id == 344 || $kategori_id == 1000170)) {
			$utpris = $utpris - $utpris * $rowDiscount->discountPercent;
		}
		return $utpris;
	}
	if ($rowDiscount->discountCode == "C20V35") {
		if ($fi) {
			if ($utpris < 2700 && $tillverkar_id == $rowDiscount->tillverkar_id && ($kategori_id == 43 || $kategori_id == 50 || $kategori_id == 374 || $kategori_id == 376 || $kategori_id == 42 || $kategori_id == 373 || $kategori_id == 45 || $kategori_id == 375 || $kategori_id == 466)) {
				$utpris = $utpris - $utpris * $rowDiscount->discountPercent;
			}
		} else {
			if ($utpris < 20000 && $tillverkar_id == $rowDiscount->tillverkar_id && ($kategori_id == 43 || $kategori_id == 50 || $kategori_id == 374 || $kategori_id == 376 || $kategori_id == 42 || $kategori_id == 373 || $kategori_id == 45 || $kategori_id == 375 || $kategori_id == 466)) {
				$utpris = $utpris - $utpris * $rowDiscount->discountPercent;
			}
		}
		return $utpris;
	}
	if ($rowDiscount->discountCode == "SONYD15") {
		if ($fi) {
			if ($tillverkar_id == $rowDiscount->tillverkar_id && ($kategori_id == 395 || $kategori_id == 445)) {
				$utpris = $utpris - $utpris * $rowDiscount->discountPercent;
			}
		} else {
			if ($tillverkar_id == $rowDiscount->tillverkar_id && ($kategori_id == 395 || $kategori_id == 445)) {
				$utpris = $utpris - $utpris * $rowDiscount->discountPercent;
			}
		}
		return $utpris;
	}
	if ($rowDiscount->discountCode == "SONYL20") {
		if ($fi) {
			if ($tillverkar_id == $rowDiscount->tillverkar_id && ($kategori_id == 43 || $kategori_id == 50 || $kategori_id == 374 || $kategori_id == 376 || $kategori_id == 42 || $kategori_id == 373 || $kategori_id == 45 || $kategori_id == 375 || $kategori_id == 466)) {
				$utpris = $utpris - $utpris * $rowDiscount->discountPercent;
			}
		} else {
			if ($tillverkar_id == $rowDiscount->tillverkar_id && ($kategori_id == 43 || $kategori_id == 50 || $kategori_id == 374 || $kategori_id == 376 || $kategori_id == 42 || $kategori_id == 373 || $kategori_id == 45 || $kategori_id == 375 || $kategori_id == 466)) {
				$utpris = $utpris - $utpris * $rowDiscount->discountPercent;
			}
		}
		return $utpris;
	}
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
    } elseif ($no) {
		if ($rowDiscount->discountPercent_no != "") {
			$utpris = $utpris - $utpris * $rowDiscount->discountPercent_no;
		} elseif ($rowDiscount->discountAmount_no != "") {
			$utpris = $utpris - $rowDiscount->discountAmount_no;
		} elseif ($rowDiscount->discountOutprice_no != "") {
			$utpris = $rowDiscount->discountOutprice_no;
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
	if ($this->checkIfSalesBundle($artnr))  { // denna kan ju inte fungera men jag bytte ut den �nd�
		$select = "SELECT artnr_del as artnr FROM Paketpriser WHERE artnr_paket = '" . $artnr . "'";
		//echo "<br>" . $select .  ": " . "" . "<br>";
		$res = mysqli_query(Db::getConnection(), $select);
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

function getDiscountArticles($articles, $fi=null) {
	global $fi, $sv, $no, $freight;
        
        // s� slipper vi �ndra s� mycket
        Locs::setOldVars(); 
	if (count($articles) < 1)
		return 0;

	$articles2 = $articles; // spara en kopia av articles f�r senare anv�ndning
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
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {

		$select = "SELECT discountCodes.*, discountCodeArticle.artnr as artnrMatch, discountCodeArticle.articleDiscounted ";
		$select .= "FROM discountCodes ";
		$select .= "LEFT JOIN discountCodeArticle ON discountCodeArticle.dscntId = discountCodes.cnt ";
		$select .= "LEFT JOIN Tillverkare ON discountCodes.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "LEFT JOIN Artiklar ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE ";
		$select .= "
			CASE 
				WHEN discountCodes.tillverkar_id = 3 THEN Artiklar.tillverkar_id = 3 AND Artiklar.lagersaldo > 0 AND Artiklar.artnr = '" . $article[0] . "'
				ELSE
				CASE WHEN discountCodeArticle.artnr IS NOT NULL THEN
					discountCodeArticle.artnr = '" . $article[0] . "'
				ELSE
					CASE 
						WHEN discountCodes.artnr IS NOT NULL THEN discountCodes.artnr = '" . $article[0] . "'
						WHEN discountCodes.kategori_id IS NOT NULL AND discountCodes.tillverkar_id IS NOT NULL THEN (discountCodes.kategori_id = " . $article[3] . " AND discountCodes.tillverkar_id = " . $article[2] . ")
						WHEN discountCodes.kategori_id IS NULL THEN discountCodes.tillverkar_id = " . $article[2] . "
						ELSE discountCodes.kategori_id = " . $article[3] . "
					END
				END
			END
				";
		$select .= " AND validDate > now() AND validFrom < now() " . $notSel;
		if ($fi)
			$select .= " AND active_fi = -1";
                else if ($no)
                    $select .= " AND active_no = -1";
		else
			$select .= " AND active_se = -1";
		
            } else {
		
		$select = "SELECT discountCodes.*, discountCodeArticle.artnr as artnrMatch, discountCodeArticle.articleDiscounted ";
		$select .= "FROM discountCodes ";
		$select .= "LEFT JOIN discountCodeArticle ON discountCodeArticle.dscntId = discountCodes.cnt ";
		$select .= "WHERE ";
		$select .= "
				CASE WHEN discountCodeArticle.artnr IS NOT NULL THEN
					discountCodeArticle.artnr = '" . $article[0] . "'
				ELSE
					CASE 
						WHEN discountCodes.artnr IS NOT NULL THEN discountCodes.artnr = '" . $article[0] . "'
						WHEN discountCodes.kategori_id IS NOT NULL AND discountCodes.tillverkar_id IS NOT NULL THEN (discountCodes.kategori_id = " . $article[3] . " AND discountCodes.tillverkar_id = " . $article[2] . ")
						WHEN discountCodes.kategori_id IS NULL THEN discountCodes.tillverkar_id = " . $article[2] . "
						ELSE discountCodes.kategori_id = " . $article[3] . "
					END
				END
				";
		$select .= " AND validDate > now() AND validFrom < now() " . $notSel;
		if ($fi)
			$select .= " AND active_fi = -1";
                else if ($no)
                    $select .= " AND active_no = -1";
		else
                    $select .= " AND active_se = -1";
		
		}
		/*
		$select = "SELECT discountCodes.*, discountCodeArticle.artnr as artnrMatch, discountCodeArticle.articleDiscounted FROM discountCodes LEFT JOIN discountCodeArticle ON discountCodeArticle.dscntId = discountCodes.cnt " . 
		" WHERE  (" . 
		" (discountCodeArticle.artnr = '" . $article[0] . "' ) OR  (" .
		" (discountCodes.tillverkar_id =  " . $article[2] . " ) OR " .
		" (discountCodes.artnr =  '" . $article[0] . "' ) OR " .
		" (discountCodes.kategori_id =  " . $article[3] . " ) ";
		if ($this->basketValue > 0 && $tjosan == "hejsan") { // anv�nds inte nu d� vi inte vet hur vi ska anv�nda totalsumman. D�remot anv�nds siffran n�r det g�ller fri frakt
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
		*/

		//echo $i . "<p><br><BR><BR>" . $select . "<br>";// .  ": " . $article[0] . "<br>";
		//exit; 
		//EL-26385
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// exit;
		}
		$res = mysqli_query(Db::getConnection(), $select);
		//echo "<p>ant rad: " . mysqli_num_rows($res) . "<p>";
		if (mysqli_num_rows($res) > 0) {
			
			//$row = mysqli_fetch_object($res);
			//print_r($row);
			while ($row = mysqli_fetch_object($res)) {
			
				if ( ($row->freeFreight == -1 && ($row->discountCodes.totalSum > $this->basketValue || $row->discountCodes.totalSum == 0 || $row->discountCodes.totalSum == "" ) && !$fi ) ||
					($row->freeFreight == -1 && ($row->discountCodes.totalSum > $this->basketValue || $row->discountCodes.totalSum_fi == 0 || $row->discountCodes.totalSum_fi == "" ) && $fi )) { // om det avser fri frakt s� s�tts den h�r och d� forts�tter inte processen. 
					//echo "h�r: " . $freight;
					if (eregi("^fraktpost", $freight)) {
						$this->freeFreight = true;						
					}
					$testVar = false;
				} else {
					// tag fram tillh�rande artiklar till den du s�ker information om 
					$select = "SELECT * FROM discountCodeArticle WHERE dscntId = " . $row->cnt;
					$res2 = mysqli_query(Db::getConnection(), $select);
					
					// kontrollera om det finns n�got att h�mta
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
                                        } elseif ($no) {
						if ($row->descrptn_no != "")
							$descrptn = $row->descrptn_no;
						else
							$descrptn = $row->descrptn;
					} else {
						$descrptn = $row->descrptn;
					}

					$articleDiscounted = ""; // ?
					$includedForFree = ""; // ?
					while ($rowDiscount = mysqli_fetch_object($res2)) {	
						
						//echo "<br>testartnr: "	. $rowDiscount->artnr . "<br>";
						// kontrollera om artikeln som kommer upp finns i arrayen av v�ra artiklar
						//$f = $this->search_array_n($rowDiscount->artnr, $articles2, 0); // varf�r matchar den inte sig sj�lv h�r? (jo det g�r den men det spelar ingen roll 
														// d� det �nd� testas nedanf�r om det �r den artikel som skall rabatteras som kommit upp. 
														// Och det sparas ocks� s� inga dubletter skapas. 

						// ovanst�ende g�rs inte l�ngre d� det testas redan i f�rsta fr�gan
						$f = true;

						if ($f === false) {
							//echo "falskt!!";
							$testVar = false;
						} else { // 
							//echo "<p>testet: " . $articles[$f][1];
							//echo "<p>testet: " . $f[0];
							//echo "<p>h�rs: " .  $articles2[$f[0]][0] . ":" .  $articles2[$f[0]][1];
							
							
							if ($rowDiscount->articleDiscounted == -1) {      // om det �r en rabatterad vara, m�rk upp vilken spara											     
								$articleDiscounted = $rowDiscount->artnr; // vilken i $articleDiscounted
								//echo "yes!" . $rowDiscount->artnr;
								// h�r, om det �r en vara som skall vara med, spara det i en egen array
							} elseif ($rowDiscount->includedArticles_id != "") {  // annars, om det �r en vara som skall ing� gratis, 
								//$includedForFree = $rowDiscount->artnr;   // spara det i $includedForFree
								// sparas i en array $includedArticles: artnr produkten | $artnr p� det som skickas med | beskrivning som syns i kundvagnen
								//discountCodeIncludedArticles
								$sel =  "SELECT discountCodeIncludedArticles.*, Artiklar.lagersaldo, Artiklar.utgangen, Artiklar.ej_med FROM discountCodeIncludedArticles, Artiklar WHERE " . 
									"discountCodeIncludedArticles.includedArticles = Artiklar.artnr AND discountCodeIncludedArticles.includedArticles_id = " . $rowDiscount->includedArticles_id;
								//echo $sel;
								//exit;
								//$sel = "SELECT lagersaldo, utgangen FROM Artiklar WHERE artnr = '" . $rowDiscount->includedArticle . "'";
								//$rw = mysqli_fetch_object(mysqli_query(Db::getConnection(), $sel));
								//echo "<p>h�r: " . $rw->utgangen . "<br>" . $rw->lagersaldo . "<br>" . $rowDiscount->includedArticle;
								$rs = (mysqli_query(Db::getConnection(), $sel));
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
										//echo "h�r: " . $includedArtsTm[0] . "<p>";�
										array_push($arr2, $includedArtsTm['includedArticles']);
										// array_push($arr2, $includedArtsTm['qty']);

										$testVar = false; // inget annat skall g�ras d� s� stoppar forts�ttningen med denna variabel. 											
									}
									array_push($arr, $arr2);								
									array_push($arr, $descrptn);
									
									array_push($this->includedArticles, $arr);

									//echo "f�rdiga resultatet: <br>";

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
							det h�r var tidigare, men det �r borttaget nu. S�tt 100% i rabatten f�r att f� samma funktionalitet. 
							} elseif ($rowDiscount->includedForFree == -1) {  // annars, om det �r en vara som skall ing� gratis, 
								$includedForFree = $rowDiscount->artnr;   // spara det i $includedForFree
							}*/

							if ($minCnt > $f[1] || $minCnt < 0) // spara det minsta gemensamma antalet av rabatterade artiklar
								$minCnt = $f[1];						
							//echo "<p>h�rs: " . $minCnt;
						}
					}
				}
				
				if ($testVar) {
					//echo "kollar: " . $row->cnt;
					if ($row->discountAmount > 0) { // h�r sparas totala rabatterade summan i klassen
						if ($row->allowMultiple == -1 && $article[1] > 0) // om det till�ts fler �n en s� g�ngras det med minsta gemensamma n�mnaren i antal
							$this->discountAmount += $row->discountAmount*$minCnt;
						else 
							$this->discountAmount += $row->discountAmount;
					}
					if ($row->discountPercent > 0) // h�r sparas procentuella rabatten. Den har nog ingen funktion egentligen. 
						$this->discountPercent = $row->discountPercent;
					// save array of successful ids to avoid duplicates
					unset($discountParts);
					$discountParts = array();
					array_push($discountParts, $row->cnt); // r�knare
					array_push($discountParts, $descrptn); // beskrivningstext av rabatten (visas f�r kunden)
					array_push($discountParts, $articleDiscounted);	// artikelnr p� den artikel som skall f� rabatten
					if ($row->allowMultiple == -1 && $article[1] > 0)
						array_push($discountParts, $row->discountAmount*$minCnt); //sparar rabatterade summan
					else 
						array_push($discountParts, $row->discountAmount);																
					array_push($discountParts, $row->discountPercent); // rabatt i procent
					array_push($discountParts, $row->taxCode); 	// momskod p� rabatten
					array_push($discountParts, $articleDiscounted); // samma, varfr�
					//array_push($discountParts, $includedForFree); 	// anv�nds ej, anv�nds ist�llet i $this->includedArticles. Alternativt anv�nd ist�llet 100% i rabatt
					//						// men funktionen var t�nkt att
					array_push($discountParts, false); 		// om raden �r anv�nd
					array_push($this->discounts, $discountParts); // (sl� ihop)
					
					array_push($dscntIds, $row->cnt); // skapa en lista p� de rader vi redan anv�nt i tabellen discountCodes
					//$this->obsafe_print_r($discounts, false, true);
					// $this->discounts r�knare | beskrivning | artikelnr (som f�r rabatten) | summa rabatt | procent rabatt | moms_id | samma artikelnr (vet ej varf�r) | om den �r anv�nd �nnu
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


// anv�nds ej l�ngre
function getIncludedArticle($artnr) {
	
	//$f = $this->search_array_n($rowDiscount->artnr, $articles2, 0); 
	$f = $this->search_array_n($artnr, $this->includedArticles , 0);
	//echo $artnr;
	//print_r($this->includedArticles);
	if ($f === false) {
		
		return false;
		
	} else {
		//echo "nej" . $f[2];
		return $f; // �ndra h�r till att ta fram artikelbeskrivning f�rn $f[1] (=artnr) om det skulle �nskas. 
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
		return $f[1]; // �ndra h�r till att ta fram artikelbeskrivning f�rn $f[1] (=artnr) om det skulle �nskas. 
	}

}
// anv�nds ej
function getIncludedArticleOld($artnr) {
	return "";
	if ($this->checkIfSalesBundle($artnr))  {
		$select = "SELECT artnr_del as artnr FROM Paketpriser WHERE artnr_paket = '" . $artnr . "'";
		//echo "<br>" . $select .  ": " . "" . "<br>";
		$res = mysqli_query($this->conn_ms, $select);
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

	
	// h�r plockas fram om n�gon artikel har en kampanj d�r n�got skall skickas med p� k�pet
	// skiljer sig fr�n det som finns i tabellen discountArticles d�r kunde m�ste lagt varan i kundvagnen
	// sj�lv. F�ltet i discountCodes skickas det alltid med. 
	// sparas i en array $includedArticles: artnr produkten | $artnr p� det som skickas med | beskrivning som syns i kundvagnen
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
	global $kundvagn, $goodsvalue, $utpris, $goodsvalueMoms, $goodsvalueMomsGC, $moms, $artnr, $count, $lagersaldo, $lagersaldo_fi, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans, $betalsatt, 
	$PHP_SELF, $brev, $bestallningsgrans, $pack, $alltidBrev, $pallDelivery, $fi, $sv, $no, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $release, $discountCode, $discountCodeStatus, $freight, $pay, 
                $articles, $discountAmount, $mobile, $sub, $rowDiscount, $old_foretag;	
        //TODO: Norge och discounts
	$this->freeFreight = false;
	$currency = Locs::getCurrency();
        $lang = Locs::getLang();
        
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" && $firstbasket != 'nooutput') {
		//echo $select;
		//echo "h�r: " . $artnr;
		echo "Utanf�r: " . $kundvagn . "<br>";
	}
	
	// denna kampanj g�ller endast framtill 30 september d� Klarna st�r f�r alla frakter
	// if (($pay == "klarnakonto" || $pay == "kreditor") && $freight == "fraktpost" && strtotime('2009-09-30 23:59:59') > strtotime("now") && !$fi) {
	if ($pay == "faktura_kampanj" && $freight == "fraktpost" && strtotime('2010-04-30 23:59:59') > strtotime("now") && !$fi && !$no) {
		$this->freeFreight = true;
	} elseif (($pay == "avbetalning3" || $pay == "avbetalning6" || $pay == "avbetalning12" || $pay == "avbetalning24" || $pay == "avbetalning36") && $freight == "fraktpost" && strtotime('2009-09-13 23:59:59') < strtotime("now") && strtotime('2009-09-30 23:59:59') > strtotime("now") && !$fi) {
		$this->freeFreight = true;
	}
	
	// if ($discountCode == "FF09" && $freight == "fraktpost") {
	if ($discountCode == "FF09" && $freight == "fraktmypack") {
	// if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		$this->freeFreight = true;
	}

	if ($discountCode == "FF09" && $freight == "fraktmypack" && $sjabo == "ejnu") {
		$this->freeFreight = true;
	}

	if ($_SESSION['MOB_CONFIRMED'] && $freight == "fraktmypack" && $_SESSION['MOB_OP'] == 1) {
		$this->freeFreight = true;
	}

	if ($this->mobilabonnemang && $freight == "fraktmypack") {
		$this->freeFreight = true;
	}

	$freight_check = NULL;
	$pallDelivery = false; // visar om n�gon produkt inneh�ller frakttill�gg. Anv�nds bl.a. till att tyngre produkter inte skall skickas som hempkaet
	$brev = true;
	if ($firstbasket == 'nooutput') 
		$this->mobilabonnemang = false;
	$alltidBrev = false;	
	# Get the cookie kundvagn
	$answers = $kundvagn;	
	// plocka fram info om rabattkoder om det finns n�gon
		
	//echo "h�r" . $basketValue;
	if (!(eregi("fraktbutik", $artnr)) && $firstbasket != 'nooutput' && !(eregi("presentkort", $artnr)) ) 
		$this->completeOrder = true;
	
	if ($discountCode != "") {
		$select = "SELECT * FROM discountCodes WHERE discountCode = '" . $discountCode . "' ";
		if ($fi) {
			$select .= "AND active_fi = -1 ";
        } else if ($no) {
			$select .= "AND active_no = -1 ";
		} else {
			$select .= "AND active_se = -1 ";
		}
		$res = mysqli_query(Db::getConnection(), $select);
		$rowDiscount = mysqli_fetch_object($res);		
		if (mysqli_num_rows($res) > 0) {			
			// if (strtotime("now") < strtotime($rowDiscount->validDate) + 84600)				
			if (strtotime("now") > strtotime($rowDiscount->validFrom) && strtotime("now") < strtotime($rowDiscount->validDate))				
				$discountCodeStatus = 1; // valid
			else 
				$discountCodeStatus = 2; // utg�tt
		} else 	{
			
			$discountCodeStatus = 0; // saknas
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
	$goodsvalueMomsGC=0;
	$moms = 0;
	$products_cybairgun = 0;
	
	$articles = array();
	$n = count($argument);
	$j = 0;
	for ($i=0; ($i < $n);  $i+=2) {

		$arg = $argument[$i];        # Article id
		$count = $argument[$i+1];    # Keeps track of the number of the same article
		$count = abs($count);
		if ($this->freeFreight && eregi("^frakt", $arg) ) {		
			$arg = "fraktfritt";
			$count = 1;
		}

		//if ($fi) {
			$select  = "SELECT artnr_fi as artnr, Artiklar.lagersaldo, Artiklar.beskrivning, Artiklar.bestallt, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, utpris, tillverkare, ";
			$select .= "frakt, lagersaldo_fi, bestallt_fi, Artiklar.releaseDate, Artiklar.kategori_id, Artiklar.tillverkar_id,  ";
			$select .= "lev_datum_fi, bestallningsgrans, lev_datum_norm, lev_datum_norm_fi, Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, ";
			$select .= "Moms.momssats_fi, Moms.momssats, Artiklar.link, link2_fi, Kategori.kategori_id_parent ";
			$select .= " FROM Artiklar, Tillverkare, Kategori, Moms LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
			$select .= "WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id ";
			$select .= "AND artnr='$arg'";

            $select  = "SELECT Artiklar.artnr, Artiklar.lagersaldo, Artiklar.beskrivning, Artiklar.bestallt, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, utpris, utpris_no, tillverkare, ";
            $select .= "frakt, lagersaldo_fi, bestallt_fi, Artiklar.releaseDate, Artiklar.kategori_id, Artiklar.tillverkar_id,  ";
            $select .= "lev_datum_fi, bestallningsgrans, lev_datum_norm, lev_datum_norm_fi, Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, ";
            $select .= "Moms.momssats_fi, Moms.momssats, Moms.momssats_no, Artiklar.link, link2_fi, Kategori.kategori_id_parent, Kategori.isInsurable, ";
            $select .= "Artiklar.isSalesBundle ";
            $select .= "FROM Artiklar ";
            $select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
            $select .= "INNER JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
            $select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
            $select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
            $select .= "WHERE artnr='$arg'";
            // $select .= " WHERE NOT (Artiklar.kategori_id = 595) AND artnr='$arg'";
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
		# Alla v�rden f�rsvinner inte, s� d�rf�r m�ste vi g�ra enligt nedan
		$ej_brev = $artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $lagersaldo_fi = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = $lev_datum_norm_fi = $lev_datum_norm = "";
		$momssats = 0;
		
		
		$row = mysqli_fetch_array(mysqli_query(Db::getConnection(), $select));
		if (is_array($row)) {
			extract($row);
		} else {
			return;
		}
		if ($fi) {
			$link = $link_fi;
			$utpris = $utpris_fi;
			$momssats = $momssats_fi;
                } else if ($no) {                        
                    $utpris = $utpris_no;
                    $momssats = $momssats_no;
                    
		} else {
                    $momssats = $momssats;
		}                
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" && $firstbasket != 'nooutput') {
			//echo $select;
			//echo "h�r: " . $artnr;
			echo "Innanf�r: " . $kundvagn . "<br>";
		}

		//echo $kategori_id . " ";
		
		/**  Lagra alla artiklar i kundvagnen i en array som anv�nds f�r att plocka upp kampanjer f�r inkluderade artiklar p� k�pet. 
		Detta g�rs f�rsta omg�ngen. */
		if ($firstbasket == 'nooutput') {
			// om mobilabonnemang, spara det i kundvagnen
			if ($kategori_id == 509) {
				$this->mobilabonnemang = true;
			} elseif (!$no && ($kategori_id == 560 || $kategori_id == 561 || $kategori_id == 567 || $kategori_id == 535 || $kategori_id == 549 || $kategori_id == 536 || $kategori_id == 547 || $kategori_id == 533 || $kategori_id == 534 || $kategori_id == 551 || $kategori_id == 537 || $kategori_id == 552 || $kategori_id == 532 || $kategori_id == 530 || $kategori_id == 540 || $kategori_id == 541 || $kategori_id == 684 || $kategori_id == 685 || $kategori_id == 686 || $kategori_id == 687 || $kategori_id == 688 || $kategori_id == 689 || $kategori_id == 690 || $kategori_id == 691 || $kategori_id == 692 || $kategori_id == 693 || $kategori_id == 694 || $kategori_id == 1000125)) {
				$this->aldersgrans = true;
			} elseif ($kategori_id == 595) {
				$this->mobilabb = true;
			} elseif ($kategori_id == 1000010) {
				$this->abbtelia = true;
				$this->mobilabonnemang = true;
				$_SESSION['MOB_CONFIRMED'] = true;
			} elseif ($kategori_id == 1000011) {
				$this->abbtele2 = true;
				$_SESSION['MOB_CONFIRMED'] = true;
			} elseif ($kategori_id == 1000012) {
				$this->abbhalebop = true;
				$_SESSION['MOB_CONFIRMED'] = true;
			} elseif ($tillverkar_id == 3 && $artnr != "5ds" && $artnr != "5dsr" && $utpris > 6400 && $utpris < 40000 && time() > strtotime('2015-05-01 00:00:01') && time() < strtotime('2015-05-31 23:59:59') && !$fi && !$no) {
				$this->isCanon30Campaign = true;
			} elseif ($tillverkar_id == 3 && $artnr != "5ds" && $artnr != "5dsr" && $utpris > 6400 && $utpris < 40000 && time() > strtotime('2015-06-29 10:40:01') && time() < strtotime('2015-07-31 23:59:59') && !$fi && !$no) {
				$this->isCanon30Campaign = true;
			} elseif ($tillverkar_id == 3 && $utpris > 4800 && $utpris < 40000 && time() > strtotime('2015-09-01 00:00:01') && time() < strtotime('2015-12-31 23:59:59') && !$fi && !$no) {
				$this->isCanon36Campaign = true;
			} elseif ($kategori_id == 336 || $kategori_id == 462 || $kategori_id == 1000260 || $kategori_id == 748) {
				$this->isMobileSvea = true;
			} else {

			}
			
			if ($isInsurable != 0 && $utpris < 52000) {
				$this->isInsurable = true;
			}
		
			
			if ($isSalesBundle == -1)  {
			
				$select = "SELECT Paketpriser.artnr_del as artnr, Paketpriser.antal, Artiklar.tillverkar_id, Artiklar.kategori_id FROM " . 
					  "Paketpriser, Artiklar WHERE Paketpriser.artnr_del = Artiklar.artnr AND artnr_paket = '" . $arg . "'";
					  
				//echo "<br>" . $select .  ": " . "" . "<br>";
				$res = mysqli_query(Db::getConnection(), $select);
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

		// r�kna om priset h�r om det �r n�gon form av rabattsats
		
		//TODO: fixa f�r fi ocks�
	

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
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" && $lagersaldo > 0 && $discountCode == "CANON20XX" && $discountCodeStatus == 1 && ($lagersaldo >= $count)) {

				$newUtpris = $this->getDiscountPrice($rowDiscount, $artnr, $kategori_id, $tillverkar_id, $utpris);		
				if ($newUtpris != $utpris) {
					$extraBeskrivningText = $this->getDiscountText($rowDiscount, $momssats, $utpris, true);				
					$discount = true;
					$utpris = $newUtpris;
				}
			
			} elseif ($discountCode != "" && $discountCodeStatus == 1 && $rowDiscount->totalSum == "" && $discountCode != "CANON20XX") {
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
		// kollar om en artikel skall skickas med p� k�pet till artikeln och skriver in info om det d� till kundvagnen
		
		if ($firstbasket != 'nooutput') {		
		//
			$tmpBeskr = "";
			if ($isSalesBundle == -1)  {
			
				$select = "SELECT artnr_del as artnr, antal FROM Paketpriser WHERE artnr_paket = '" . $artnr . "'";
				//$select = "SELECT Paketpriser.artnr_del as artnr, Paketpriser.antal FROM Paketpriser, Artiklar WHERE Paketpriser.artnr_paket = Artiklar.artnr AND (Artiklar.utgangen = 0 OR Artiklar.lagersaldo > 0) AND Paketpriser.artnr_paket = '" . $artnr . "'";
				//echo "<br>" . $select .  ": " . "" . "<br>";
				$res = mysqli_query(Db::getConnection(), $select);
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
		// kolla om det �r ett paket
		if ($isSalesBundle == -1)  {
			$pack = true;
		}
		
		// kolla om det inte g�r som brev
		if (($ej_brev == -1 || $kat_ej_brev == -1)) {
			$brev = false;
			
		}
		if ((eregi("presentkort", $artnr)))
			$alltidBrev = true;
			
			
		// L�gg p� extra frakt om det beh�vs
		if ($frakt > 0 && $freight != 'fraktbutik' && !$this->freeFreight )  {
			$brev = false;
		//$extraFrakt = true;
			if ($extra_freight < $frakt) {
				$extra_freight = $frakt; 
				$this->check_extra_freight = true; // detta f�r att vi skall kunna blockera vissa frakts�tt i kundvagnen
			}
		}
		if ($extra_freight == 3)
			$pallDelivery = true;	
		//echo "h�r: " . $artnr;
		if (eregi("presentkort", $artnr)) {
                    require_once ("CGiftCard.php");
			if ($firstbasket == "nooutput")
				viewGiftCardInBasket($j, false);
			else
				viewGiftCardInBasket($j, true);
			$j +=1;
			$this->isGiftcard = true;
		}

		// ****************************************************************************************************
		if ($_SESSION['MOB_CONFIRMED'] && ($_SESSION['MOB_PHONE'] == $artnr) && $_SESSION['MOB_OP'] == 1 && $_SESSION['MOB_DELBETALA'] > 0) {
		    $utpris = 0;
		} elseif ($_SESSION['MOB_CONFIRMED'] && ($_SESSION['MOB_PHONE'] == $artnr) && $_SESSION['MOB_OP'] != 1) {
		    $utpris = ($_SESSION['MOB_PRICE'] * 0.8);
		}
		// ****************************************************************************************************

		
		$goodscounter += 1;
		$goodsvalue += ($utpris*$count);
		$goodsvalueMoms += ($utpris + $utpris * $momssats)*$count;
		$goodsvalueMomsGC += ($utpris + $utpris * $momssats)*$count;
		$moms += $utpris*$count*$momssats;
		
		if ($sub->getMainCategory($kategori_id_parent) == 1000147) {
		// if ($sub->controlCybairgun($kategori_id_parent)) {
			$products_cybairgun += ($utpris*$count);
		}
		
		if ($tillverkare != '.')
			$description = $tillverkare . " ";
		if (!$sv && $fi && $beskrivning_fi != "") {			
			$description .= $beskrivning_fi . " " . $kommentar_fi;
		} else {
			$description .= $beskrivning . " " . $kommentar;
		}
		if ($_SESSION['MOB_CONFIRMED'] && ($_SESSION['MOB_PHONE'] == $artnr)) {
			$description .= $extraBeskrivningText;
		} else {
			$description .= " <b>" . $extraBeskrivningText . "</b>";  // Denna har jag bytt 2008-12-09
		}
		// $description .= "<br><br><b>" . $extraBeskrivningText;

		
		// ****************************************************************************************************
		if ($_SESSION['MOB_CONFIRMED'] && ($_SESSION['MOB_PHONE'] == $artnr)) {
	    
		    $description .= " med " . $mobile->getDescriptionAbonnemang($_SESSION['MOB_AB']);
			if ($_SESSION['MOB_DATASIZE'] != "") {
				$description .= " + " . $mobile->getDescriptionAbonnemang($_SESSION['MOB_DATASIZE']);
			}
			if ($_SESSION['MOB_DELBETALA'] > 0) {
				if ($_SESSION['MOB_DELBETALA'] == 6) {
					$description .= " + " . $mobile->getDescriptionAbonnemang(333318);
				} elseif  ($_SESSION['MOB_DELBETALA'] == 12) {
					$description .= " + " . $mobile->getDescriptionAbonnemang(333319);
				} else {
					$description .= " + " . $mobile->getDescriptionAbonnemang(333320);
				}
			}
		    if ($_SESSION['MOB_FMA'] == 40) {
		    	$description .= " och f�rh�jd m�nadsavgift 50 SEK/m�n";
		    } elseif ($_SESSION['MOB_FMA'] == 80) {
		    	$description .= " och f�rh�jd m�nadsavgift 100 SEK/m�n";
		    } elseif ($_SESSION['MOB_FMA'] == 120) {
		    	$description .= " och f�rh�jd m�nadsavgift 150 SEK/m�n";
		    } elseif ($_SESSION['MOB_FMA'] == 160) {
		    	$description .= " och f�rh�jd m�nadsavgift 200 SEK/m�n";
		    } elseif ($_SESSION['MOB_FMA'] == 200) {
		    	$description .= " och f�rh�jd m�nadsavgift 250 SEK/m�n";
		    } elseif ($_SESSION['MOB_FMA'] == 240) {
		    	$description .= " och f�rh�jd m�nadsavgift 300 SEK/m�n";
		    } else {
		    	$description .= " ";
		    }
		    
	    		$_SESSION['MOB_BESKRIVNING'] = $description;
	    
	    		// echo $_SESSION['MOB_BESKRIVNING'];
		
		}
		// ****************************************************************************************************

		// h�r visar vi att produkten �r en k�plats
		if ($_SESSION['KOPLATS_CONFIRMED'] && ($_SESSION['KOPLATS_PRODUCT'] == $artnr)) {

			$description .= "<i><b> Bokas som k�plats!</b></i>";
		
		}
			
		
		// visa bara info om det inte �r kostnadsfri frakt
		if (!(eregi("fraktbutik", $artnr)) && $firstbasket != 'nooutput' && !(eregi("presentkort", $artnr)) ) {

?>

			<tr>
				
			  <td bgcolor="#ECECE6">
			  <?php 
	  			//if ((eregi("frakt", $artnr))) echo "<a name=\"beforeFreight\"></a>";
				//if ($i+4 == $n || $n < 3) echo "<a name=\"beforeFreight\"></a>"; // l�gg till bokm�rke s� att vi kan visa nedre delen av kundvagnen, men bara om kundvagnen �r l�ng, mer �n fyra artiklar. 
				if ($i+4 == $n && $n > 12) echo "<a name=\"beforeFreight\"></a>"; // l�gg till bokm�rke s� att vi kan visa nedre delen av kundvagnen, men bara om kundvagnen �r l�ng, mer �n fyra artiklar. 
				if ($link <> "")
					{
					 if (ereg("http:", $link))						
					 	print "<a href=\"".$link."\">";
					 else
					 	print "<a href=\"/".$link."\">";
		
					}
					
				 else
					{
						if ($artnr == $_SESSION['ERBJUDANDE_KUNDVAGN_PAC'] && $_SESSION['ERBJUDANDE_KUNDVAGN_CONFIRMED'])
							print "<a target=\"_parent\" href=\"http://" . $_SERVER["HTTP_HOST"] . "/info.php?article=" . $_SESSION['ERBJUDANDE_KUNDVAGN_SHOW'] . "\">";
						elseif (preg_match("/UNHCR/i", $artnr) || preg_match("/CFFN10/i", $artnr))
							print "<a target=\"_parent\" href=\"http://" . $_SERVER["HTTP_HOST"] . "/stod-jul\">";
						else {
							// tagit bort s� att kunden inte kommer in p� gamla sidor. 
							;//print "<a target=\"_parent\" href=\"http://" . $_SERVER["HTTP_HOST"] . "/info.php?article=".$artnr."\">";
							if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR']) || CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
								print "<a target=\"_parent\" href=\"http://" . $_SERVER["HTTP_HOST"] . "/info.php?article=".$artnr."\">";
							}
						}
					}
								
				
	  		if ($artnr == "fraktfritt") {
	  			echo "<font color=\"#FF0000\"><b>$description</b></font>"; 
	  		} else {
	  			echo $description; 
	  		}
	  		
	  		?>
	  		
	  		</font>
	  			
	  			</A>
	  			</td>
			  <td align="center" bgcolor="#ECECE6">
			  <?php 
			  if ($kategori_id != 629) {
			  	echo $count;
			  } else {
			  	echo "";
			  }
			  ?>
			  </td>				  
			<td align="center" bgcolor="#ECECE6"><span class="change_count">	
				<?php if ($_SESSION['MOB_CONFIRMED'] && $artnr == $_SESSION['MOB_PHONE']) { ?>
				
				<a onMouseOver="this.T_BGCOLOR='#FFFFFF';this.T_WIDTH=315;return escape('Klickar du h�r kommer abonnemanget raderas. �ven telefonen kommer tas.')" style="text-decoration: none" href="/confirm_abonnemang.php?ID=<?php echo session_id(); ?>&remove=true">
				ta bort</a>

				<?php } elseif ($artnr == $_SESSION['ERBJUDANDE_KUNDVAGN_PAC'] && $_SESSION['ERBJUDANDE_KUNDVAGN_CONFIRMED']) { ?>
				
				<a style="text-decoration: none" href="/confirm_erbjudande_kundvagn.php?ID=<?php echo session_id(); ?>&remove=true">
				<?php echo l('Remove'); ?></a>

				<?php } elseif (!(eregi("^frakt", $artnr)) && $kategori_id != 629) { ?>
		
                                <?php //TODO: hantera cookiesB.js och modifyItemsinbasket  ?>
				<A HREF="javascript:modifyItemsInBasket('<?php echo $artnr; ?>', '<?php echo $fi; ?>', '<?php echo $sv; ?>')">
				<?php echo l('Change count'); ?>
				<?php } elseif ($kategori_id == 629) { ?>
				
				<a onMouseOver="this.T_BGCOLOR='#FFFFFF';this.T_WIDTH=215;return escape('Tar bort produkten fr�n kundvagnen')" style="text-decoration: none" HREF="javascript:changeItem('<?php echo $artnr; ?>', 0, 0); kundvagnReload();">
				<!-- <img border="0" src="/kundvagn/trash.gif"></a> -->
				ta bort</a>
				
				<?php } else { ?>
				&nbsp;&nbsp;
				<?php }; ?>		
				</span>
			</td>
			<td align="left" bgcolor="#ECECE6"><font color="#008000">
				<?php
				if (preg_match("/UNHCR/i", $artnr) || preg_match("/CFFN10/i", $artnr)) { 
					echo "&nbsp;&nbsp";
				} elseif (!(eregi("^frakt", $artnr)) && $kategori_id != 629) { 
					$this->check_lager($artnr, $fi);
				} else {
					echo "&nbsp;&nbsp";
				}
				?>
		
			</font></td>
			
			<?php if ($_SESSION['RememberMoms'] == 1) { ?>

			<td align="right" bgcolor="#ECECE6">
			
			<?php 
			if ($fi) {
				echo number_format($utpris*$count, 0, ',', ' ') . " " . $currency;  
			} else {
				if ($kategori_id == 629) {
					echo "&nbsp;";
				} else {
					echo number_format($utpris*$count, 0, ',', ' ') . " " . $currency;
				}
			}
			?>
			</td>
			
			<?php } else { ?>
			
			<td align="right" bgcolor="#ECECE6">
			
			<?php 

                        if ($kategori_id == 629) {
                                echo "&nbsp;";
                        } else {
                                echo number_format(($utpris + $utpris * $momssats) * $count, 0, ',', ' ') . " " . $currency;
                        }

			?>
			
			</td>
			
			<?php } ?>

			<?php if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") { ?>
			<!--
			<td align="center" width="15"><font size="1" face="Verdana">	
				<?php if (!(eregi("^frakt", $artnr))) { ?>
				<a onMouseOver="this.T_BGCOLOR='#FFFF00';this.T_WIDTH=215;return escape('Tar bort produkten fr�n kundvagnen')" style="text-decoration: none" HREF="javascript:changeItem('<?php echo $artnr; ?>', 0, 0); kundvagnReload();">
				<img border="0" src="/kundvagn/trash.gif"></a>
				<?php } else { ?>							
				<a onMouseOver="this.T_BGCOLOR='#FFFFFF';this.T_WIDTH=350;return escape('Denna kan du inte ta bort, v�lj annat leveranss�tt ist�llet')" style="text-decoration: none">
				<img border="0" src="/kundvagn/trash_no.gif"></a>
				<?php } ?>							
			</font></td>
			-->
			<?php } ?>

			
			</tr>

			<?php  	
			
		} 

	}
	
	if (!$this->isGiftcard && ($_SESSION['psajt'] == 1 || $_SESSION['psajt'] == 2)) {
		unset($_SESSION['psajt']);
	}
	
	if ($_SESSION['CYBAIRGUN_XBOX'] && $products_cybairgun < 800) { // detta f�r att s�kra upp alla skurkar som f�rs�ker fippla
	    unset ($_SESSION['CYBAIRGUN_XBOX']);
	}
	
	if ($this->checkStoreStatus("15077") && $firstbasket != 'nooutput' && $products_cybairgun > 800 && strtotime('2014-08-29 00:00:01') < strtotime("now") && strtotime('2014-12-31 23:59:59') > strtotime("now")) {
	
	    $select  = "SELECT Artiklar.beskrivning, Artiklar.kommentar, utpris, utpris_no, Artiklar_fi.beskrivning_fi, Artiklar_fi.utpris_fi,  
                Moms.momssats, Moms.momssats_fi, Moms.momssats_no, Tillverkare.tillverkare ";
		$select .= "FROM Artiklar ";
		$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "INNER JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
		$select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
		$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
		$select .= "WHERE artnr='15077'";
		
	    $res = mysqli_query(Db::getConnection(), $select);
	    $row = mysqli_fetch_object($res);
		if ($fi && !$sv) {
                    $name = $row->beskrivning_fi;
                    $comment = $row->kommentar_fi;                    
		} else {
                    $name = $row->tillverkare . " " .$row->beskrivning . " <b><font color=#85000d>P� k�pet!</font></b>";
                    $comment = $row->kommentar;			
		}
		if ($fi) {

		    $outprice = $row->utpris_fi;
		    $momsts = $row->momssats_fi;
                } else if ($no) {
		    $outprice = $row->utpris_no;
		    $momsts = $row->momssats_no;                    
		} else {
		    $outprice = 0;
		    $momsts = $row->momssats;						
		}
	
	    $_SESSION['CYBAIRGUN_XBOX'] = true;
	
	?>
		<tr>
				
		  <td bgcolor="#ECECE6"><?php echo "<a target=\"_parent\" href=\"http://" . $_SERVER["HTTP_HOST"] . "/info.php?article=15077\">$name</a>"; ?></td>
		  <td align="center" bgcolor="#ECECE6">1</td>
		  <td bgcolor="#ECECE6">&nbsp;</td>
		  <td bgcolor="#ECECE6">&nbsp;</td>
		  <?php if ($_SESSION['RememberMoms'] == 1) { ?>
		  <td align="right" bgcolor="#ECECE6"><?php echo number_format($outprice, 0, ',', ' ') . " " . $currency;  ?></td>
		  <?php } else { ?>
		  <td align="right" bgcolor="#ECECE6"><?php echo number_format(($outprice + $outprice * $momsts) * $count, 0, ',', ' ') . " " . $currency; ?></td>
		  <?php } ?>
		  <?php if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") { ?>
		  <!--
		  <td align="center" width="15">
		  <a onMouseOver="this.T_BGCOLOR='#FFFFFF';this.T_WIDTH=350;return escape('Denna kan du inte ta bort, v�lj annat betalss�tt ist�llet')" style="text-decoration: none">
		  <img border="0" src="/kundvagn/trash_no.gif"></a>
		  </td>
		  -->
		  <?php } ?>
		  
		</tr>
	
	<?php
	
	}

	// h�r projecerar vi det som har med mobilabonnemang att g�ra

	/*
	if ($firstbasket != 'nooutput' && $_SESSION['MOB_CONFIRMED']) {

	if ($fi) {
		$val = "EUR";
	} else {
		$val = "kr";
	}
	    $abonnemangbeskrivning = $mobile->getDescriptionAbonnemang($_SESSION['MOB_AB']);
	    if ($_SESSION['MOB_FMA'] == 40) {
	    	$abonnemangbeskrivning .= " med f�rh�jd m�nadsavgift 50 kr/m�n";
	    } elseif ($_SESSION['MOB_FMA'] == 80) {
	    	$abonnemangbeskrivning .= " med f�rh�jd m�nadsavgift 100 kr/m�n";
	    } elseif ($_SESSION['MOB_FMA'] == 120) {
	    	$abonnemangbeskrivning .= " med f�rh�jd m�nadsavgift 150 kr/m�n";
	    } elseif ($_SESSION['MOB_FMA'] == 160) {
	    	$abonnemangbeskrivning .= " med f�rh�jd m�nadsavgift 200 kr/m�n";
	    } else {
	    	$abonnemangbeskrivning .= " ";
	    }
	    
	    $_SESSION['MOB_BESKRIVNING'] = $abonnemangbeskrivning;
	    
	    // echo $_SESSION['MOB_BESKRIVNING'];
	    
	    $goodsvalue += $outprice;
	    $goodsvalueMoms += ($_SESSION['MOB_DIFF']);
	
	?>
		<tr>
				
		  <td bgcolor="#ECECE6" colspan="2"><font size="1" face="Verdana"><?php echo $abonnemangbeskrivning; ?></td>
		  <td align="center" bgcolor="#ECECE6"><font size="1" face="Verdana">
		  
		<a onMouseOver="this.T_BGCOLOR='#FFFF00';this.T_WIDTH=215;return escape('Tar bort produkten fr�n kundvagnen')" style="text-decoration: none" href="/confirm_abonnemang.php?ID=<?php echo session_id(); ?>&remove=true">
		<!-- <img border="0" src="/kundvagn/trash.gif"></a> -->
		fimpa</a>

		  </td>
		  <td bgcolor="#ECECE6"><font size="1" face="Verdana"></td>
		  <?php if ($_SESSION['RememberMoms'] == 1) { ?>
		  <td align="right" bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo number_format($outprice, 0, ',', ' ') . " " . $val;  ?></td>
		  <?php } else { ?>
		  <td align="right" bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo number_format($_SESSION['MOB_DIFF'], 0, ',', ' ') . " " . $val; ?></td>
		  <?php } ?>
		  <?php if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") { ?>
		  <td align="center" width="15">
		  <a onMouseOver="this.T_BGCOLOR='#FFFFFF';this.T_WIDTH=350;return escape('Denna kan du inte ta bort, v�lj annat betalss�tt ist�llet')" style="text-decoration: none">
		  <img border="0" src="/kundvagn/trash_no.gif"></a>
		  </td>
		  <?php } ?>
		  
		</tr>
	
	<?php
	
	}
	*/


	// nedan tar vi fram aviavgift som skall l�ggas till f�r Klarna faktura (privat)
	
	if ($old_foretag != -1 && $pay == "sveainvoice" && $firstbasket != 'nooutput' && ((time() > strtotime('2019-12-31 23:59:59') && $sv && !$fi) || $fi)) {
	// if ($old_foretag != -1 && $pay == "sveainvoice" && $firstbasket != 'nooutput') {

	    $select  = "select Artiklar.beskrivning, Artiklar.kommentar, utpris, utpris_no, Artiklar_fi.beskrivning_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi, Moms.momssats_no from 
                Artiklar, Artiklar_fi, Moms where Artiklar.momskod = Moms.moms_id AND Artiklar.artnr = Artiklar_fi.artnr_fi AND ";
	    $select .= " artnr='invoicefee'";
	
	    $res = mysqli_query(Db::getConnection(), $select);
	    $row = mysqli_fetch_object($res);
		if ($fi && !$sv) {
                    $name = $row->beskrivning_fi;
                    $comment = $row->kommentar_fi;			
		} else {
                    $name = $row->beskrivning;
                    $comment = $row->kommentar;			
		}
		if ($fi) {
		    $outprice = $row->utpris_fi;
		    $momsts = $row->momssats_fi;
                } else if ($no) {
		    $outprice = $row->utpris_no;
		    $momsts = $row->momssats_no;                    
		} else {
		    $outprice = $row->utpris;
		    $momsts = $row->momssats;						
		}
	
	    $manufacturer = "";
	    $goodsvalue += $outprice;
	    $goodsvalueMoms += ($outprice + $outprice * $momsts);
	    $goodsvalueMomsGC += ($outprice + $outprice * $momsts);
	?>
		<tr>
				
		  <td bgcolor="#ECECE6"><?php echo $name; ?></td>
		  <td align="center" bgcolor="#ECECE6">1</td>
		  <td bgcolor="#ECECE6">&nbsp;</td>
		  <td bgcolor="#ECECE6">&nbsp;</td>
		  <?php if ($_SESSION['RememberMoms'] == 1) { ?>
		  <td align="right" bgcolor="#ECECE6"><?php echo number_format($outprice, 0, ',', ' ') . " " . $currency;  ?></td>
		  <?php } else { ?>
		  <td align="right" bgcolor="#ECECE6"><?php echo number_format(($outprice + $outprice * $momsts) * $count, 0, ',', ' ') . " " . $currency; ?></td>
		  <?php } ?>
		  <?php if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") { ?>
		  <!--
		  <td align="center" width="15">
		  <a onMouseOver="this.T_BGCOLOR='#FFFFFF';this.T_WIDTH=350;return escape('Denna kan du inte ta bort, v�lj annat betalss�tt ist�llet')" style="text-decoration: none">
		  <img border="0" src="/kundvagn/trash_no.gif"></a>
		  </td>
		  -->
		  <?php } ?>
		  
		</tr>
	
	<?php
	
	}
	

	if ($extra_freight && $firstbasket == 'yes' && $firstbasket != 'nooutput' && $extra_freight != 999 && $extra_freight != 3 && $freight != Null && !$this->freeFreight) {
	
	    if ($extra_freight == 1) $extra_freight_artnr = 'frakt+';
	    elseif ($extra_freight == 2) $extra_freight_artnr = 'frakt+2';
	    //elseif ($extra_freight == 3 && $old_foretag == 0) $extra_freight_artnr = 'frakthempall';	    
		//elseif ($extra_freight == 3 && $old_foretag == -1) $extra_freight_artnr = 'fraktpall';	    
	    else $extra_freight_artnr = 'frakt+'; // f�r s�kerhets skull
	    $select  = " SELECT Artiklar.beskrivning, Artiklar.kommentar, utpris, utpris_no, Artiklar_fi.beskrivning_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi, Moms.momssats_no   ";
	    $select .= " FROM Artiklar, Artiklar_fi, Moms where Artiklar.momskod = Moms.moms_id AND Artiklar.artnr = Artiklar_fi.artnr_fi AND ";
	    $select .= " artnr='$extra_freight_artnr'";
	
	    $res = mysqli_query(Db::getConnection(), $select);
	    $row = mysqli_fetch_object($res);
		if ($fi && !$sv) {
                    $name = $row->beskrivning_fi;
                    $comment = $row->kommentar_fi;			
		} else {
                    $name = $row->beskrivning;
                    $comment = $row->kommentar;			
		}
		if ($fi) {
		    $outprice = $row->utpris_fi;
		    $momsts = $row->momssats_fi;			
                } else if ($no) {
		    $outprice = $row->utpris_no;
		    $momsts = $row->momssats_no;			                    
		} else {
		    $outprice = $row->utpris;
		    $momsts = $row->momssats;						
		}
	
	    $manufacturer = "";
	    $goodsvalue += $outprice;
	    $goodsvalueMoms += ($outprice + $outprice * $momsts);
	    $goodsvalueMomsGC += ($outprice + $outprice * $momsts);
?>	    

	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="5"><b>Extra tillkommande avgifter</b></td>
	</tr>

	<tr>
		<td bgcolor="#ECECE6" width="262"><i>
		<?php if ($fi && !$sv) { ?>
                        <?php // TODO: �vers�ttning samt r�tt pris f�r Norge, ska det h�r vara h�rdkodat summan?   ?>
		<a onMouseOver="return escape('<b>Suuri l�hetys lis�maksu</b><br>Kooltaan suurien tai painavien tuotteiden l�hetyskuluihin tuleva lis�maksu (esim. tulostimet, scannerit, jalustat, taustakankaat, suuret laukut jne) Postin taulukon mukaisesti, t�ll� hetkell� 6,9 Eur (sis. alv 23%).')">
		<?php } else { ?>
		<a onMouseOver="return escape('<b>Avgift skrymmande</b><br>P� stora varor som t ex skrivare, scanner,�stativ, projektionsdukar eller st�rre v�skor tillkommer ett skrymmetill�gg p� 69kr - 295kr inklusive moms beroende p� storlek.')">
		<?php } ?>		
		<?php echo $name; ?>
		</a></i>
		</td>
		<td align="center" bgcolor="#ECECE6" width="30">1</td>

		<td bgcolor="#ECECE6">&nbsp;&nbsp;</td>
		<td bgcolor="#ECECE6">&nbsp;&nbsp;</td>
		
		<?php if ($_SESSION['RememberMoms'] == 1) { ?>

		<td align="right" bgcolor="#ECECE6" width="79"><?php echo number_format($outprice, 0, ',', ' ') . " " . $currency;  ?></td>
		
		<?php } else { ?>
		
		<td align="right" bgcolor="#ECECE6" width="82"><?php echo number_format(($outprice + $outprice * $momsts) * $count, 0, ',', ' ') . " " . $currency; ?></td>
		
		<?php } ?>

	</tr>
	<?php  
	    
	}
	if ($firstbasket != 'nooutput' && count($this->discounts) > 0) {
		// $this->discounts r�knare | beskrivning | summa rabatt | artikelnr | procent rabatt | moms_id | samma artikelnr? (p� k�pet?) | p� k�pet | falskt? (kanske att den �r anv�nd?
		// $this->discounts: id | description | discount amount | discount percent | tax code
// $this->discounts r�knare | beskrivning | artikelnr (som f�r rabatten) | summa rabatt | procent rabatt | moms_id | samma artikelnr (vet ej varf�r) | om den �r anv�nd �nnu
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
					$goodsvalueMomsGC -= $discountAmount * 1.25;
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
                                echo number_format(-$discountAmount, 0, ',', ' ') . " " . $currency;  
				?>
				</font></td>
				
				<?php } else { ?>
				
				<td align="right" bgcolor="#ECECE6" width="82"><font size="1" face="Verdana">
				
				<?php 

				echo number_format(-($discountAmount + $discountAmount * 0.25) * $count, 0, ',', ' ') . " " . $currency; 
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
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			$this->check_lager_se_new($artnr);
		} else {
			// $this->check_lager_se($artnr);
			$this->check_lager_se_new($artnr);
		}
}
/**
 * kontrollerar lagersaldo
 *
 * @param string $artnr
 */
function check_lager_se($artnr) {
	global $count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue, 
	$est_delivery, $fi, $sv, $no, $release, $web_adempiere;
	

		?>
		<font size="1" face="Verdana, Arial" color="#385F39">
		<?php
		
	//$ret = "";
	//echo $release;
	//echo $artnr;
	$release = substr($release, 0, 10);
	$package_stock_sv = false;

	# Make a check if freigt is already selected. 
	if (ereg("^frakt", $artnr))
		{ $freight_check = 1; }

	if ($this->checkIfSalesBundle($artnr)) {    // kollar tillg�ngen om det �r ett paket
		// echo "hej";
		$package_stock_sv = $this->check_package_sv($artnr); 	
	}
	//echo "h�r: " . $lagersaldo . $count;
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") { // endast test
		echo $package_stock_sv . ".";
		echo $lagersaldo;
		echo $count;
	}
	if ($lagersaldo >= $count || $package_stock_sv) {
		if (!$sv && $fi) {
			print "<a onMouseOver=\"return escape('Tuote l�ytyy varastosta ja l�hetet��n normaalisti samana p�iv�n� kuin teet tilauksesi')\" style=\"text-decoration: none\"> varastossa</a>"; 
		} else {
			print "<a onMouseOver=\"return escape('Varan finns p� lager och skickas normalt samma dag som ni best�ller')\" style=\"text-decoration: none\"> finns i lager </a>";
		}
	} else {
		if (!(eregi("^frakt", $artnr))) {

			$this->completeOrder = false;
			echo "";
		} else {
			echo "";
		}
		# Kolla hur m�nga det finns p� k�
		# antal p� k�p visas i $queue, nollst�lles f�rst. 
		$queue = 0;
		
		$queue = $this->check_queue($artnr, $fi, null);
		$neededStock = $queue + $count;
		//echo $bestallt;	
		//echo $neededStock;	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89xx") { // endast test
				echo "bestallt: " . $bestallt . "<br>";
				echo "neededStock: " . $neededStock . "<br>";
		}
		if ($bestallt >= $neededStock || $_SERVER['REMOTE_ADDR'] == "192.168.1.89xx")  { 	
				
			//if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				$web_adempiere->check_lager($artnr,1000000,$count);
			//} else {
			//	echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($artnr, $count, null, false, $fi, $sv )	;
			//}
			
		} 


		else  { 
		//<a onMouseOver=\"return escape('')\" style=\"text-decoration: none\"> 
			if ($this->checkIfSalesBundle($artnr)) {	
                                // TODO: hantera javascript
				if (!$sv && $fi)
					print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \" onMouseOver=\"return escape('Tuote on paketti joka koostuu eri osista. Yksi tai useampi osa on loppunut varastosta. Klikkaa n�hd��ksesi joka tuotteen varastotilaa. Luonnollisesti voit tilata paketin kuitenkin.')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">lis�� tietoja t�st�</a>";
				else
					print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \" onMouseOver=\"return escape('Varan best�r av ett paket med flera delar. En eller flera av delarna �r slut i lager. Klicka f�r att se lagerstatus p� varje enskild vara. Naturligtvis g�r paketet bra att best�lla �nd�. ')\" style=\"text-decoration: none\"><span class=\"mark_red\">klicka f�r info</span></a>";
					
			} elseif ($bestallningsgrans == 0)  {
			  
				// kolla om den �r sl�ppt �nnu eller inte. 
				if ($release != "") {
					// kolla s� att releasedatumet inte varit �nnu. 
					if (strtotime($release) >= mktime()) {
						if (!$sv && $fi) 
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote ei ole viel� markkinoilla, arvioitu ensimm�inen toimitusaika " . $this->showDeliveryDate($release, 1, $fi, $sv) . "<br>Ainoastaan tilauksesta')\" style=\"text-decoration: none\">Tulee markkinoille " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har �nnu inte kommit ut p� marknaden. Ber�knat f�rsta leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) ."<br>Produkten �r en best�llningsvara')\" style=\"text-decoration: none\">Sl�pps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						
					} else { // d� blir det vanlig best�llningsvara. 
						if (!$sv && $fi) // TODO: finska n�sta rad
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm </b> p�iv�� varastoomme<br>T�m� toimitusaika vaatii ett� toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem p� best�llning. <br>Normal leveranstid �r <b>$lev_datum_norm </b> dagar till v�rt lager<br>Denna leveranstid f�ruts�tter att v�r leverant�r har varan p� lager')\" style=\"text-decoration: none\"> best�llningsvara</></font></a>";													
					}
				} else {
					if (!$sv && $fi) // TODO: finska n�sta rad
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm </b> p�iv�� varastoomme<br>T�m� toimitusaika vaatii ett� toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
					else
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem p� best�llning. <br>Normal leveranstid �r <b>$lev_datum_norm </b> dagar till v�rt lager<br>Denna leveranstid f�ruts�tter att v�r leverant�r har varan p� lager')\" style=\"text-decoration: none\"> best�llningsvara</></font></a>";												
				}
				
				
			} else {
				 
				if ($release != "") {
					// kolla s� att releasedatumet inte varit �nnu. 
					if (strtotime($release) >= mktime()) {					
						if (!$sv && $fi)
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote ei ole viel� markkinoilla, arvioitu ensimm�inen toimitusaika " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Sl�pps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har �nnu inte kommit ut p� marknaden. Ber�knat f�rsta leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Sl�pps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
					} else {
						if (!$sv && $fi) // TODO: finska p� n�sta rad
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilap�isesti lopussa. <br>Normaali toimitusaika on  <b>$lev_datum_norm_fi </b> p�iv�� varastoomme<br>T�m� toimitusaika vaatii ett� toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilap�isesti loppu</font></a>" ;														
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten �r tillf�lligt slut i lager. <br>Normal leveranstid �r <b>$lev_datum_norm_fi </b> dagar till v�rt lager<br>Denna leveranstid f�ruts�tter att v�r leverant�r har varan p� lager')\" style=\"text-decoration: none\"> tillf�lligt slut</font></a>" ;															
					}
				} else {
					if (!$sv && $fi)
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilap�isesti lopussa. <br>Normaali toimitusaika on  <b>$lev_datum_norm_fi </b> p�iv�� varastoomme<br>T�m� toimitusaika vaatii ett� toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilap�isesti loppu</font></a>" ;														
					else
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten �r tillf�lligt slut i lager. <br>Normal leveranstid �r <b>$lev_datum_norm_fi </b> dagar till v�rt lager<br>Denna leveranstid f�ruts�tter att v�r leverant�r har varan p� lager')\" style=\"text-decoration: none\"> tillf�lligt slut</font></a>" ;															
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

function check_lager_se_new($artnr) {
	global $count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue, 
	$est_delivery, $fi, $sv, $no, $release, $web_adempiere, $mobilsite;
	
	$release = substr($release, 0, 10);
	$package_stock_sv = false;
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $artnr;
	}

	# Make a check if freigt is already selected. 
	if (ereg("^frakt", $artnr))
		{ $freight_check = 1; }

	if ($this->checkIfSalesBundle($artnr)) {    // kollar tillg�ngen om det �r ett paket
		$package_stock_sv = $this->check_package_sv($artnr); 	
	}
	if ($package_stock_sv) {
		
		if (!$sv && $fi) {
			print "<a onMouseOver=\"return escape('Tuote l�ytyy varastosta ja l�hetet��n normaalisti samana p�iv�n� kuin teet tilauksesi')\" style=\"text-decoration: none\">Varastossa</a>"; 
		} elseif ($no) {
			print "<a onMouseOver=\"return escape('Varan finns p� lager och skickas snarast efter best�llning.')\" style=\"text-decoration: none\">Finnes p� lager </a>";
		} else {
			if ($mobilsite) {
				print "<span class=\"instore\">Finns i lager</span>";
			} else {
				print "<a onMouseOver=\"return escape('Varan finns p� lager och skickas normalt samma dag som ni best�ller')\" style=\"text-decoration: none\">Finns i lager </a>";
			}
		}

	} elseif ($this->checkIfSalesBundle($artnr) && !$package_stock_sv) {
		
		if (preg_match("/pri_/i", $_SERVER['PHP_SELF']) || preg_match("/price_/i", $_SERVER['PHP_SELF']) || preg_match("/search\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<a href=\"/info.php?article=$artnr\">" . l('Click for info') . "</a>";
		} elseif (!$sv && $fi) {
                        // TODO: hantera javascript
			// print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \" onMouseOver=\"return escape('Tuote on paketti joka koostuu eri osista. Yksi tai useampi osa on loppunut varastosta. Klikkaa n�hd��ksesi joka tuotteen varastotilaa. Luonnollisesti voit tilata paketin kuitenkin.')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">lis�� tietoja t�st�</a>";
			print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \" style=\"text-decoration: none\"><span class=\"mark_cyber\">lis�� tietoja t�st�</span></a>";
		} elseif ($no) {
			print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \" style=\"text-decoration: none\"><span class=\"mark_cyber\">klikk for info</span></a>";
		} else {
			if ($mobilsite) {
				print "<span class=\"notinstore\">Tillf�lligt slut</span>";
			} else {
				// print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \" onMouseOver=\"return escape('Varan best�r av ett paket med flera delar. En eller flera av delarna �r slut i lager. Klicka f�r att se lagerstatus p� varje enskild vara. Naturligtvis g�r paketet bra att best�lla �nd�. ')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\" >klicka f�r info</a>";
				print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \" style=\"text-decoration: none\"><span class=\"mark_cyber\">klicka f�r info</span></a>";
				// print "<span onclick=\"show_hide('$artnr');\" style=\"cursor:pointer;\"><font face=\"Verdana, Arial\" size=\"1\" color=\"#85000D\">klicka f�r info</font></span>";
			}
		}

	} else {
		if (!(eregi("^frakt", $artnr))) {
			$this->completeOrder = false;
			echo "";
		} else {
			echo "";
		}
	
		$web_adempiere->check_lager($artnr,1000000,$count); // h�r kollar vi alla andra m�jligheter
	}         
}

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

	if ($this->checkIfSalesBundle($artnr)) {    // kollar tillg�ngen om det �r ett paket
		$package_stock_fi = $this->check_package_fi(); 
		if (!$package_stock_fi) $package_stock_sv = $this->check_package_sv($artnr);		
	}
	
	if ($lagersaldo_fi >= $count || $package_stock_fi) {
		if (!$sv)
			print "<a onMouseOver=\"return escape('Tuote l�ytyy varastosta ja l�hetet��n normaalisti samana p�iv�n� kuin teet tilauksesi')\" style=\"text-decoration: none\"> varastossa</a>"; 
		else 
			print "<a onMouseOver=\"return escape('Varan finns p� lager och skickas normalt samma dag som ni best�ller')\" style=\"text-decoration: none\"> finns i lager</a>";	
	} else {

		# Kolla hur m�nga det finns p� k�
		# antal p� k�p visas i $queue, nollst�lles f�rst. 
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
				print "<a onMouseOver=\"return escape('Varan finns p� centrallager, obs. l�ngre leveranstid')\" style=\"text-decoration: none\"> finns p� centrallager, leveranstid ca 7dgr</a>";							
		} 
		elseif ($bestallt >= $neededStock) {
			echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($artnr, $count, null, false, true, $sv );
		} else  { 
		//<a onMouseOver=\"return escape('')\" style=\"text-decoration: none\"> 
			if ($this->checkIfSalesBundle($artnr)) {							
				if (!$sv)
					print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \"> onMouseOver=\"return escape('Tuote on paketti joka koostuu eri osista. Yksi tai useampi osa on loppunut varastosta. Klikkaa n�hd��ksesi joka tuotteen varastotilaa. Luonnollisesti voit tilata paketin kuitenkin.')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">lis�� tietoja t�st�</a>";
				else
					print "<a href=\"javascript:levStatusPaket('$artnr', $count, '$fi', '$sv') \"> onMouseOver=\"return escape('Varan best�r av ett paket med flera delar. En eller flera av delarna �r slut i lager. Klicka f�r att se lagerstatus p� varje enskild vara. Naturligtvis g�r paketet bra att best�lla �nd�. ')\" style=\"text-decoration: none\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\" >klicka f�r info</a>";
					
			} elseif ($bestallningsgrans == 0)  {
			  
				// kolla om den �r sl�ppt �nnu eller inte. 
				if ($release != "") {
					// kolla s� att releasedatumet inte varit �nnu. 
					if (strtotime($release) >= mktime()) {
						// TODO: f� in att varan �r en best�llningsvara ocks�. 
						if (!$sv) 
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote ei ole viel� markkinoilla, arvioitu ensimm�inen toimitusaika " . $this->showDeliveryDate($release, 1, $fi, $sv) . "<br>Ainoastaan tilauksesta')\" style=\"text-decoration: none\">Tulee markkinoille " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har �nnu inte kommit ut p� marknaden. Ber�knat f�rsta leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) ."<br>Produkten �r en best�llningsvara')\" style=\"text-decoration: none\">Sl�pps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
					} else { // d� blir det vanlig best�llningsvara. 
						if (!$sv) // TODO: finska n�sta rad
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm_fi </b> p�iv�� varastoomme<br>T�m� toimitusaika vaatii ett� toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem p� best�llning. <br>Normal leveranstid �r <b>$lev_datum_norm_fi </b> dagar till v�rt lager<br>Denna leveranstid f�ruts�tter att v�r leverant�r har varan p� lager')\" style=\"text-decoration: none\"> best�llningsvara</></font></a>";													
					}
				} else {
					if (!$sv) // TODO: finska n�sta rad
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm_fi </b> p�iv�� varastoomme<br>T�m� toimitusaika vaatii ett� toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilaustuote</></font></a>";													
					else
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem p� best�llning. <br>Normal leveranstid �r <b>$lev_datum_norm_fi </b> dagar till v�rt lager<br>Denna leveranstid f�ruts�tter att v�r leverant�r har varan p� lager')\" style=\"text-decoration: none\"> best�llningsvara</></font></a>";													
				}
				
				
			} else {
				 
				if ($release != "") {
					// kolla s� att releasedatumet inte varit �nnu. 
					if (strtotime($release) >= mktime()) {					
						if (!$sv)
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote ei ole viel� markkinoilla, arvioitu ensimm�inen toimitusaika " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Sl�pps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Varan har �nnu inte kommit ut p� marknaden. Ber�knat f�rsta leverans " . $this->showDeliveryDate($release, 1, $fi, $sv) . "')\" style=\"text-decoration: none\">Sl�pps " . $this->showDeliveryDate($release, 1, $fi, $sv) . "</></font></a>";
					} else {
						if (!$sv) // TODO: finska p� n�sta rad
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilap�isesti lopussa. <br>Normaali toimitusaika on  <b>$lev_datum_norm_fi </b> p�iv�� varastoomme<br>T�m� toimitusaika vaatii ett� toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilap�isesti loppu</font></a>" ;														
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten �r tillf�lligt slut i lager. <br>Normal leveranstid �r <b>$lev_datum_norm_fi </b> dagar till v�rt lager<br>Denna leveranstid f�ruts�tter att v�r leverant�r har varan p� lager')\" style=\"text-decoration: none\"> tillf�lligt slut</font></a>" ;														
					}
				} else {
					if (!$sv)
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilap�isesti lopussa. <br>Normaali toimitusaika on  <b>$lev_datum_norm_fi </b> p�iv�� varastoomme<br>T�m� toimitusaika vaatii ett� toimittajalla on tuote varastossa')\" style=\"text-decoration: none\"> Tilap�isesti loppu</font></a>" ;														
					else
						print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten �r tillf�lligt slut i lager. <br>Normal leveranstid �r <b>$lev_datum_norm_fi </b> dagar till v�rt lager<br>Denna leveranstid f�ruts�tter att v�r leverant�r har varan p� lager')\" style=\"text-decoration: none\"> tillf�lligt slut</font></a>" ;														
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
		if (!$sv && $fi)
			return "varastossa"; 
		else 
			return "finns i lager";	
	} else {

		# Kolla hur m�nga det finns p� k�
		# antal p� k�p visas i $queue, nollst�lles f�rst. 
		$queue = 0;
		
		$queue = $this->check_queue($row->artnr, true, $row->ordernr);
		
		$neededStock = $queue + $count;
		//echo "h�r: " . $row->lagersaldo . "." . $count . "";
		//echo $row->bestallt_fi;
		if ($row->bestallt_fi >= $neededStock )  { 			
			return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($row->artnr, $count, $row->ordernr, true, $fi, $sv )	. "</font>";			
		} 
		elseif ( ($row->lagersaldo >= $count) )  { 				
			if (!$sv && $fi)
				return "keskusvarastossa, toimitusaika noin 7vrk"; 
			else 
				return "finns p� centrallager, leveranstid ca 7dgr";							
		} 
		elseif ($row->bestallt >= $neededStock) {
			return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($row->artnr, $count, $row->ordernr, false, true, $sv ) . "</font>";
		} else  { 
		//<a onMouseOver=\"return escape('')\" style=\"text-decoration: none\"> 
			if ($row->bestallningsgrans == 0)  {
			  
				// kolla om den �r sl�ppt �nnu eller inte. 
				if ($row->release != "") {
					// kolla s� att releasedatumet inte varit �nnu. 
					if (strtotime($row->release) >= mktime()) {
						// TODO: f� in att varan �r en best�llningsvara ocks�. 
						if (!$sv && $fi) 
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tulee markkinoille " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Sl�pps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
					} else { // d� blir det vanlig best�llningsvara. 
						if (!$sv && $fi) 
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tilaustuote</font>";													
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">best�llningsvara</font>";
					}
				} else {
					if (!$sv && $fi) // TODO: finska n�sta rad
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tilaustuote</></font>";													
					else
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">best�llningsvara</></font>";													
				}
								
			} else {
				 
				if ($row->release != "") {
					// kolla s� att releasedatumet inte varit �nnu. 
					if (strtotime($row->release) >= mktime()) {					
						if (!$sv && $fi) 
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tulee markkinoille " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Sl�pps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
					} else {
						if (!$sv && $fi) // TODO: finska p� n�sta rad
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tilap�isesti loppu</font></a>" ;														
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillf�lligt slut</font>" ;														
					}
				} else {
					if (!$sv && $fi) // TODO: finska p� n�sta rad
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillf�lligt slut</font>" ;														
					else
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillf�lligt slut</font>" ;														
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
	//echo "h�r: " . $row->ordernr;

	if (ereg("^frakt", $row->artnr))
		{ return null; }
	
	if ($row->bokad ==1) {
		if (!$sv && $fi)
			return "varastossa"; 
		else 
			return "finns i lager";	
	} else {

		# Kolla hur m�nga det finns p� k�
		# antal p� k�p visas i $queue, nollst�lles f�rst. 
		$queue = 0;
		
		$queue = $this->check_queue($row->artnr, false, $row->ordernr);
		
		$neededStock = $queue + $count;
		//echo "h�r: " . $row->lagersaldo . "." . $count . "";
		//echo $neededStock;
		if ($row->bestallt >= $neededStock )  { 			
			return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($row->artnr, $row->antal, $row->ordernr, false, $fi, $sv )	. "</font>";											
		} elseif ($row->bestallt >= $neededStock) {
			return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">". $this->kollaLevtid($row->artnr, $row->antal, $row->ordernr, false, $fi, $sv ) . "</font>";
		} else  { 
		//<a onMouseOver=\"return escape('')\" style=\"text-decoration: none\"> 
			if ($row->bestallningsgrans == 0)  {
			  	
				// kolla om den �r sl�ppt �nnu eller inte. 
				if ($row->release != "") {
					// kolla s� att releasedatumet inte varit �nnu. 
					if (strtotime($row->release) >= mktime()) {
						// TODO: f� in att varan �r en best�llningsvara ocks�. 
						if (!$sv && $fi) 
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tulee markkinoille " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Sl�pps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</font>";
					} else { // d� blir det vanlig best�llningsvara. 
						if (!$sv && $fi) 
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tilaustuote</font>";													
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">best�llningsvara</font>";
					}
				} else {
					if (!$sv) // TODO: finska n�sta rad
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tilaustuote</></font>";													
					else
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">best�llningsvara</></font>";													
				}
								
			} else {
				 
				if ($row->release != "") {
					// kolla s� att releasedatumet inte varit �nnu. 
					if (strtotime($row->release) >= mktime()) {					
						if (!$sv && $fi) 
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tulee markkinoille " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Sl�pps " . $this->showDeliveryDate($row->release, 1, $fi, $sv) . "</></font>";
					} else {
						if (!$sv && $fi) // TODO: finska p� n�sta rad
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">Tilap�isesti loppu</font></a>" ;														
						else
							return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillf�lligt slut</font>" ;													
					}
				} else {
					if (!$sv && $fi) // TODO: finska p� n�sta rad
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillf�lligt slut</font>" ;														
					else
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillf�lligt slut</font>" ;													
				}
			}	
		}
	}         
	
}
function check_package_sv($artnr = null) {

	global $count;
	
	$select = "SELECT lagersaldo, antal, kategori_id ";
	$select .= "FROM Artiklar, Paketpriser WHERE Artiklar.artnr=Paketpriser.artnr_del ";
	$select .= "AND Paketpriser.artnr_paket = '$artnr' ";
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $select . "count: " . $count;
	}
	
	if ($count == "") {
		$count = 1;
	}
		
	$res = mysqli_query(Db::getConnection(), $select);
	
	unset ($check);

	while ($row = mysqli_fetch_array($res)) {
	extract($row);

	$check = $antal*$count;

		if ($kategori_id != 595 && $kategori_id != 629 && $kategori_id != 630) {

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
	$res = mysqli_query(Db::getConnection(), $select);
	
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
		
	$res = mysqli_query(Db::getConnection(), $select);
	//if (mysqli_num_rows($res) > 0) {	
	//	$row = mysqli_fetch_array($res);
	//	$queue = $row['ant'];			
	//}
	//else
		$queue = 0;
	
	return $queue;
}


function getArticleInfo2($artnr) {
	
	$select = "SELECT * FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);
	return $row;
}

function getPrice($artnr) {
	
	$select = "SELECT utpris FROM Artiklar where artnr = '$artnr' ";
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);
	return $row->utpris;
}

function getPriceWithVat($artnr) {
	global $fi, $no;
	
	$select  = "SELECT utpris, utpris_fi, utpris_no, Moms.momssats, Moms.momssats_fi, Moms.momssats_no  ";
	$select .= "FROM Artiklar ";
	$select .= "JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
	$select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
	$select .= "WHERE artnr = '$artnr' ";
	
	$res = mysqli_query(Db::getConnection(), $select);
	$row = mysqli_fetch_object($res);
        $momssats = 0;
	if ($fi)
            $utpris_moms = $row->utpris_fi + $row->utpris_fi * $row->momssats_fi;
        else if ($no)
            $utpris_moms = $row->utpris_no + $row->utpris_no * $row->momssats_no;
        else
            $utpris_moms = $row->utpris + $row->utpris * $row->momssats;
	
	return number_format($utpris_moms, 0, ',', ' ') . " " . Locs::getCurrency();

}

function kollaLevtid2 ($artnr, $count, $ordernr, $fi)  { // artnr som skall kollas, antal samt inkommet datum p� ordern
	global $fi;
	

	// kolla f�rst inkommet f�r att f� fram k�plats. 
	// ange dagens datum om koll f�r kundvagnen 
	
	if ($ordernr == NULL) {
		$inkommetOur = date("Y-m-d H:i:s");
	}
	else {
		$select = "SELECT inkommet from Ordertabell WHERE ordernr = '$ordernr' ";

		$res = mysqli_query(Db::getConnection(), $select);

		if ($res)  {
			$row = mysqli_fetch_object($res);
			$inkommetOur = $row->inkommet;
		}
	}
	
	
	
	$totalNeed = 0;
	$ordernr = NULL; // tag bort v�rdet eftersom samma variable namn anv�nds nedan
	//echo $inkommetOur;	
	// r�kna f�rst ut hur m�nga som st�r f�re
	$select =  "SELECT Orderposter.ordernr, Orderposter.antal, Ordertabell.inkommet FROM Orderposter, Ordertabell ";
	$select .= "WHERE Orderposter.ordernr = Ordertabell.ordernr AND ";
	$select .= "Orderposter.artnr = '$artnr' AND Orderposter.bokad = 0 ";
	$select .= "ORDER BY Ordertabell.inkommet ASC";
	
	//echo $select;
	
	$res = mysqli_query(Db::getConnection(), $select);
	
	if (mysqli_num_rows($res) > '0') {
		
		while ($row = mysqli_fetch_array($res)):
			extract ($row);
			if ($inkommet < $inkommetOur) {
				
				$totalNeed += $antal; // antalet som st�r f�re.
				//echo "$totalNeed, $ordernr";
			}
			else
				break;
				
		endwhile;
		
	}
	
	$totalNeed += $count; // l�gg p� de vi beh�ver till totala behovet (f�r att det skall 
			      // n� fram till "v�r" k�plats)
	
	$antal_sum = 0;
	$select = "SELECT inkopsnr, antal, levdatum, levererat FROM Inkopsposter WHERE artnr = '$artnr' "; 
	$select .= "AND antal != levererat ";
	$select .= "ORDER BY ber_lev_datum ASC ";
	$res = mysqli_query(Db::getConnection(), $select);
	
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
			$articleinfo = "toimitusp�iv�m��r� ei ole m��ritelty";
		else
			$articleinfo = "leveransdatum ok�nt";
	
	}

if ($articleinfo == "" || $articleinfo == "-") {
	if ($fi)
		$articleinfo = "toimitusp�iv�m��r� ei ole m��ritelty";
	else
		$articleinfo = "leveransdatum ok�nt";
}
return $articleinfo;
}

//-------------------------------------------------------------------------------------


function kollaLevtid ($artnr, $count, $ordernr, $table_fi, $fi, $sv)  { // artnr som skall kollas, antal samt inkommet datum p� ordern, 
																		//$table_fi om vi ska titta efter leveranstid i finska Inkopsposter eller inte
	
	return "";
																		//echo ".";																	
	//$fi avser i det h�r fallet om Orderposter_fi eller Orderposter skall anv�ndas. 
	
	// kolla f�rst inkommet f�r att f� fram k�plats. 
	// ange dagens datum om koll f�r kundvagnen 
		//return "";
	if ($ordernr == NULL) {
		$inkommetOur = date("Y-m-d H:i:s");
	}
	else {
		//if ($fi)
		//	$select = "SELECT inkommet from Ordertabell_fi WHERE ordernr = '$ordernr' ";
		//else 	
			$select = "SELECT inkommet from Ordertabell WHERE ordernr = '$ordernr' ";

		$res = mysqli_query(Db::getConnection(), $select);

		if ($res)  {
			$row = mysqli_fetch_object($res);
			$inkommetOur = $row->inkommet;
		}
	}		
	//echo $inkommetOur;
	//$inkommetOur = strftime ("%m-%d-%Y %R", strtotime($inkommet));
	//echo "." . $inkommetOur;
	$totalNeed = 0;
	$ordernr = NULL; // tag bort v�rdet eftersom samma variable namn anv�nds nedan
	//echo $inkommetOur;	
	// r�kna f�rst ut hur m�nga som st�r f�re
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
	$res = mysqli_query(Db::getConnection(), $select);
if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98") {
	//echo $select;
}	
	if (mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)):
			extract ($row);
			//echo $inkommet . "<br>";
			//if (strtotime($inkommet) < strtotime($inkommetOur)) {
				//echo "<br>" . strtotime($inkommet) . ", " . strtotime($inkommetOur);
				$totalNeed += $antal; // antalet som st�r f�re.
				//echo "<br>$totalNeed, $ordernr<br>";
			//}
			//else
			//	break;
				
		endwhile;
		
	}
	
	$totalNeed += $count; // l�gg p� de vi beh�ver till totala behovet (f�r att det skall 
			      // n� fram till "v�r" k�plats)
	
	$antal_sum = 0;
	/**
	if ($table_fi) {
		
		//echo ".";
		$select = "SELECT inkopsnr, antal, levdatum, ber_lev_datum_fi as ber_lev_datum, prec_fi as prec, levererat FROM Inkopsposter_fi WHERE artnr = '$artnr' "; 
		$select .= "AND antal != levererat ";		
		$select .= "ORDER BY ber_lev_datum_fi ASC ";
		$res = mysqli_query(Db::getConnection(), $select);
			
	} else {
	*/
		$select = "SELECT inkopsnr, antal, levdatum, ber_lev_datum, prec, levererat FROM Inkopsposter WHERE artnr = '$artnr' "; 
		$select .= "AND antal != levererat ";
		$select .= "ORDER BY ber_lev_datum ASC ";
		$res = mysqli_query(Db::getConnection(), $select);
		
	//}
	
	//echo $select;
	if (mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)): 
			extract ($row);
			$ber_lev_datum = substr($ber_lev_datum, 0, 10);
			
			$this->ber_lev_datum = $ber_lev_datum;
			$this->prec = $prec;
			$antal_sum += ($antal - $levererat);
			
			//echo "<br>h�r: , " . $artnr . " : " . $this->ber_lev_datum . ", " . $this->prec;
			if ($antal_sum >= $totalNeed) {
				
				if ($ber_lev_datum == "" || $ber_lev_datum == "1900-01-01" || $ber_lev_datum == "2100-01-01") {
					$this->ber_lev_datum = "2100-01-01";	
										
					if ($levdatum != "" && $levdatum != "-") {
						
						if (!$sv && $fi) {
							$articleinfo = "<a onMouseOver=\"return escape('P�iv�m��r� viittaa p�iv�m��r��n jolloin tuote arvioidaan saapuvan varastoon. Huomioi ett� t�m� on arvioitu p�iv�m��r�')\" style=\"text-decoration: none\">" . $levdatum . "</a>";					
						} else {
							$articleinfo = "<a onMouseOver=\"return escape('Datumet avser datum n�r varan <b>ber�knas</b> komma in till v�rt lager. Observera att detta �r ungef�rligt datum. ')\" style=\"text-decoration: none\">" . $levdatum . " </a>";											
						}						
					}
					
					elseif (!$sv)
						$articleinfo = "<a onMouseOver=\"return escape('Tuote on tilattu mutta toimitusaika ei ole tiedossa. Emme ole saaneet tilausvahvistusta toimittajalta. ')\" style=\"text-decoration: none\">toimitusp�iv�m��r� ei ole m��ritelty</a>";
					else
						// $articleinfo = "<a onMouseOver=\"return escape('Det betyder kort och gott att vi inte vet n�r varan finns i lager igen. Skulle vi f� ett besked fr�n v�r leverant�r kommer det att hamna h�r ist�llet, men s� l�nge det st�r leveransdatum ok�nt har vi allts� inget besked att ge. ')\" style=\"text-decoration: underline\"> leveransdatum ok�nt</a>";
						$articleinfo = "<a onMouseOver=\"return escape('Leveransdatum ok�nt inneb�r att v�r leverant�r i dagsl�get inte har n�got ber�knat leveransdatum f�r n�r de f�r in varan till sig. S� fort vi f�r ett nytt leveransbesked uppdateras detta.')\" style=\"text-decoration: underline\"> leveransdatum ok�nt</a>";
				} else {					
					//$prec = 1;
					//echo "h�r: " . $table_fi;
					/**
					if (!$table_fi && $prec == 1 && $fi) { // l�gg p� tv� dagar p� leveranstiden om det �r fr�n finland men svenska levtiden kollas. 
						$ber_lev_datum = date("Y-m-d", (strtotime($ber_lev_datum . "+2 day")));	
						//echo $ber_lev_datum;
					}*/
					$this->ber_lev_datum = $ber_lev_datum;
					if (!$sv) {						
						$articleinfo = "<a onMouseOver=\"return escape('P�iv�m��r� viittaa p�iv�m��r��n jolloin tuote arvioidaan saapuvan varastoon. Huomioi ett� t�m� on arvioitu p�iv�m��r�')\" style=\"text-decoration: none\">" . $this->showDeliveryDate($ber_lev_datum, $prec, $fi, $sv) . "</a>";					
					} else {
						$articleinfo = "<a onMouseOver=\"return escape('Datumet avser datum n�r varan <b>ber�knas</b> komma in till v�rt lager. Observera att detta �r ungef�rligt datum. ')\" style=\"text-decoration: none\">" . $this->showDeliveryDate($ber_lev_datum, $prec, $fi, $sv) . " </a>";					
						
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
			$articleinfo = "<a onMouseOver=\"return escape('Tuote on tilattu mutta toimitusaika ei ole tiedossa. Emme ole saaneet tilausvahvistusta toimittajalta. ')\" style=\"text-decoration: none\">toimitusp�iv�m��r� ei ole m��ritelty</a>";
		else
			// $articleinfo = "<a onMouseOver=\"return escape('Det betyder kort och gott att vi inte vet n�r varan finns i lager igen. Skulle vi f� ett besked fr�n v�r leverant�r kommer det att hamna h�r ist�llet, men s� l�nge det st�r leveransdatum ok�nt har vi allts� inget besked att ge. ')\" style=\"text-decoration: underline\"> leveransdatum ok�nt</a>";
			$articleinfo = "<a onMouseOver=\"return escape('Leveransdatum ok�nt inneb�r att v�r leverant�r i dagsl�get inte har n�got ber�knat leveransdatum f�r n�r de f�r in varan till sig. S� fort vi f�r ett nytt leveransbesked uppdateras detta.')\" style=\"text-decoration: underline\"> leveransdatum ok�nt</a>";
	
	}
//echo $articleinfo;


return $articleinfo;
}
function calcDate($date, $days) {
	$dat = strtotime($date) + 86400 * $days;
	return date("Y-m-d", $dat);
}

function showDeliveryDate($dat, $prec, $fi, $sv) {
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		// echo $dat . "<br>";
		// echo $prec . "<br>";
	}
	setlocale (LC_ALL, 'en_US');
	
	$ret = "";

	if ($dat != "")
		$dat = substr($dat, 0, 10);
	
	$timestmp = strtotime($dat);		
	//echo $timestmp;
	/*
	if ($dat == "" || $dat == "1900-01-01" || $dat == "2100-01-01") {
		if ($fi && !$sv)
			return "<a onMouseOver=\"return escape('Tuote on tilattu mutta toimitusaika ei ole tiedossa. Emme ole saaneet tilausvahvistusta toimittajalta')\" style=\"text-decoration: none\"> toimitusp�iv�m��r� ei ole m��ritelty</a>";
		else 
			// return "<a onMouseOver=\"return escape('Det betyder kort och gott att vi inte vet n�r varan finns i lager igen. Skulle vi f� ett besked fr�n v�r leverant�r kommer det att hamna h�r ist�llet, men s� l�nge det st�r leveransdatum ok�nt har vi allts� inget besked att ge. ')\" style=\"text-decoration: underline\"> leveransdatum ok�nt</a>";
			return "<a onMouseOver=\"return escape('Leveransdatum ok�nt inneb�r att v�r leverant�r i dagsl�get inte har n�got ber�knat leveransdatum f�r n�r de f�r in varan till sig. S� fort vi f�r ett nytt leveransbesked uppdateras detta.')\" style=\"text-decoration: underline\"> leveransdatum ok�nt</a>";
	}
	*/
	if ($prec == "U") { // ok�nt datum
		if ($fi && !$sv)
			return "<a onMouseOver=\"return escape('Tuote on tilattu mutta toimitusaika ei ole tiedossa. Emme ole saaneet tilausvahvistusta toimittajalta')\" style=\"text-decoration: none\"> toimitusp�iv�m��r� ei ole m��ritelty</a>";
		else 
			return "<a onMouseOver=\"return escape('Leveransdatum ok�nt inneb�r att v�r leverant�r i dagsl�get inte har n�got ber�knat leveransdatum f�r n�r de f�r in varan till sig. S� fort vi f�r ett nytt leveransbesked uppdateras detta.')\" style=\"text-decoration: underline\"> leveransdatum ok�nt</a>";

	// } elseif ($prec == 1 || $prec == "") { // exakt datum
	} elseif ($prec == "D") { // exakt datum
		if ($fi) {								
			return date("d-m-Y", $timestmp);
		} else {
			//echo ".";
			//echo strtotime($dat);
			return date("Y-m-d", $timestmp);
		}
		return $dat;

	// } elseif ($prec == 2) {	// visas som vecka
	} elseif ($prec == "W") {	// visas som vecka
		if ($fi && !$sv)
			return "Oletettu saapumisaika viikko " .  strtolower (date("W", strtotime($dat)));		
		else 	
			return "Ber�knas in vecka " .  strtolower (date("W", strtotime($dat)));		
			

	// } elseif ($prec == 3) {	// del av m�nad
	} elseif ($prec == "P") {	// del av m�nad
		$day = date("j", strtotime($dat));
		$month = date("n", strtotime($dat));

		if ($day > 0 && $day <= 10 ) {
			if ($fi && !$sv)
				return "Oletettu saapumisaika " . $this->getMonthFi($month) . " alussa";
			else 
				return "Ber�knas in i b�rjan av " . $this->getMonthSv($month);
				
		} elseif ($day > 10 && $day <= 20) {
			if ($fi && !$sv)
				return "Oletettu saapumisaika " . $this->getMonthFi($month) . " puoliv�liss�";		
			else 
				return "Ber�knas in i mitten av " . $this->getMonthSv($month);
		} elseif ($day > 20) {
			if ($fi && !$sv)
				return "Oletettu saapumisaika " . $this->getMonthFi($month) . " loppupuolella";
			else
				return "Ber�knas in i slutet av " . $this->getMonthSv($month);
				
		} else { // tja, n�r �r den n�got annat? Tomt blir nog bra
			return "";
		}
		
	// } elseif ($prec == 4) { // m�nad
	} elseif ($prec == "M") { // m�nad
		$month = date("n", strtotime($dat));
		if ($fi && !$sv)
			//return "Oletettu saapumisaika " . strtolower ( date("F", strtotime($dat)));		
			return "Oletettu saapumisaika " . $this->getMonthFi($month);		
		else 
			//return "Ber�knas in i " . strtolower ( date("F", strtotime($dat)));		
			return "Ber�knas in i " . $this->getMonthSv($month);

	} else { // detta ska aldrig beh�va intr�ffa, men vi k�r den �nd�
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
		return "kes�kuu";
	elseif ($month == 7)
		return "hein�kuu";
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
	$res = mysqli_query(Db::getConnection(), $select);
	$res2 = mysqli_query(Db::getConnection(), $select2);

	if (mysqli_num_rows($res) == 0)
		return;
	$row2 = mysqli_fetch_object($res2);
	?>	
	<input type="hidden" name="campaignNo" value="<?php echo $campaignNr ?>">
	  <tr>
	    <td bgcolor="#B90000">&nbsp;</td><td colspan="2" bgcolor="#B90000"><font face="Verdana" size="1" color="#FFFFFF"><b><?php echo $row2->name; ?></b></font></td>
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

	$res = mysqli_query(Db::getConnection(), $select);
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
		// $this->check_lager($artnr, $fi); 
		$this->check_lager($artnr); 
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
		  <a href="mailto:order@cyberphoto.se"><?php if ($fi == 'yes'): ?>Information saknas, kontakta cyberphoto f�r mer info<?php else: ?>Information saknas, kontakta cyberphoto f�r mer info<?php endif; ?></a>
		  </font></td>
		</tr>
		<?php
	}
	?>
	  </font></td>
	</tr>
		


<?php
	
}

function viewPacketDeliveryPictureNew($artnr, $mangd, $fi) {
	global $count, $lagersaldo, $lagersaldo_fi, $bestallt, $lev_datum, $lev_datum_norm, $lev_datum_norm_fi, $bestallningsgrans, $bestallningsgrans_fi, $fi, $sv, $bestallt_fi, $jul;
	
	$select =  "SELECT Paketpriser.artnr_paket, Paketpriser.artnr_del, Paketpriser.antal, Artiklar.artnr, Artiklar.bild, ";
	$select .= "Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar_fi.lagersaldo_fi, Artiklar.lagersaldo, Artiklar.beskrivning, ";
	$select .= "tillverkare, Artiklar.lagersaldo, Artiklar.bestallt, ";
	$select .= "lev_datum_fi, bestallningsgrans, bestallningsgrans_fi, lev_datum_norm, lev_datum_norm_fi, lagersaldo_fi, bestallt_fi, bestallt, ";
	$select .= "Artiklar.bestallningsgrans, Artiklar.lev_datum_normal, Artiklar.ej_med, Artiklar.kategori_id ";
	$select .= "FROM Artiklar ";
	$select .= "INNER JOIN Paketpriser ON Artiklar.artnr = Paketpriser.artnr_del ";
	$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
	$select .= "WHERE Paketpriser.artnr_paket = '$artnr' ";
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $select;
	}

	$res = mysqli_query(Db::getConnection(), $select);
	if (mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)):
		$description = ""; $bestallt_fi = 0;
		extract($row);
//		echo $bestallt_fi;
		$count = $antal*$mangd;
		if ($tillverkare != '.')
			$description = $tillverkare . " ";

		if ($fi && !$sv && $beskrivning_fi != "")
			$description .= $beskrivning_fi;
		else 	
			$description .= $beskrivning;
		?>		
		<tr>
		  <td align="center" bgcolor="#FFFFFF">
		  <?php if($ej_med != -1 && $kategori_id != 486) { ?>
		  	<?php if ($jul) { ?>
		  		<a href="info.php?article=<?php echo $artnr; ?>">
		  	<?php } else { ?>
		  		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?article=<?php echo $artnr; ?>">
		  	<?php } ?>
		  <?php } else { ?>
		  	<a onMouseOver="return escape('Det finns ingen produktsida f�r denna artikel!')" style="text-decoration: none">
		  <?php } ?>
		  <?php if ($bild != "") { ?>
		  	<img src=/thumbs/medium/bilder/<?php echo $bild; ?>>
		  <?php } else { ?>
		  	<img src=/kopknapp/bildsaknas.jpg>
		  <?php } ?>
		  </a>
		  </td>
		  <td bgcolor="#FFFFFF"><font color="#2B2B2B" face="Verdana, Arial" size="1">
		  <?php if($ej_med != -1 && $kategori_id != 486) { ?>
		  	<?php if ($jul) { ?>
		  		<a href="info.php?article=<?php echo $artnr; ?>">
		  	<?php } else { ?>
		  		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?article=<?php echo $artnr; ?>">
		  	<?php } ?>
		  <?php } else { ?>
		  	<a onMouseOver="return escape('Det finns ingen produktsida f�r denna artikel!')" style="text-decoration: none">
		  <?php } ?>
		  <?php echo $description; ?></a></font></td>
		  <td bgcolor="#FFFFFF" align="center"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php echo $antal*$mangd; ?></font></td>
		  <td bgcolor="#FFFFFF"><font color="#2B2B2B" face="Verdana, Arial" size="1">
		<?php	
		if ($kategori_id == 595 || $kategori_id == 629 || $kategori_id == 630) {
			echo "<font size=\"1\" face=\"Verdana\" color=\"#385F39\">finns i lager</font>";
		} else {
			//($artnr, $count, NULL);
			//echo $artnr;
			$count = $antal * $mangd;
			// $this->check_lager($artnr, $fi); 
			$this->check_lager($artnr); 
		}
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
		  <a href="mailto:order@cyberphoto.se"><?php if ($fi == 'yes'): ?>Information saknas, kontakta cyberphoto f�r mer info<?php else: ?>Information saknas, kontakta cyberphoto f�r mer info<?php endif; ?></a>
		  </font></td>
		</tr>
		<?php
	}
	?>
	  </font></td>
	</tr>
		


<?php
	
}

function formatDiscountTxt($rs, $showstyle, $newpricelist) {
	global $fi, $sv, $no;
	
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
	} else if ($no) {
		$explanationtext = $rs->descrptn_no;
		$campaign_link = $rs->campaign_link_no;
		$link = $rs->link_no;
		$link_ext = $rs->link_ext_no;

	} else {
		$explanationtext = $rs->descrptn;
		$campaign_link = $rs->campaign_link;
		$link = $rs->link;
		$link_ext = $rs->link_ext;
		
	}
        
        $cnt = $rs->cnt;
        
	if ($showstyle == 1) { // Denna visar texten p� info sidan
		if ($explanationtext != "") {
	
			if ($fi && !$sv) {
				if ($newpricelist) {
					return "<span class=\"redmark\">Kampanja!</span> <span class=\"bold\">" . $explanationtext . "</span>";
				} else {
					return "<div class=\"campaign_area\"><span class=\"mark_cyber bold\">Kampanja!</span> <span class=\"bold\">" . $explanationtext . "</span></div>";
				}
			} elseif ($fi && $sv) {
				if ($newpricelist) {
					return "<span class=\"redmark\">Kampanj!</span> <span class=\"bold\">" . $explanationtext . "</span>";
				} else {
					return "<div class=\"campaign_area\"><span class=\"mark_cyber bold\">Kampanj!</span> <span class=\"bold\">" . $explanationtext . "</span></div>";
				}
			} elseif ($no) {
				if ($newpricelist) {
					return "<span class=\"redmark\">Kampanje!</span> <span class=\"bold\">" . $explanationtext . "</span>";
				} else {
					return "<div class=\"campaign_area\"><span class=\"mark_cyber bold\">Kampanje!</span> <span class=\"bold\">" . $explanationtext . "</span></div>";
				}
			} else {
				if ($newpricelist) {
					return "<span class=\"redmark\">Kampanj!</span> <span class=\"bold\">" . $explanationtext . "</span>";
				} else {
					return "<div class=\"campaign_area\"><span class=\"mark_cyber bold\">Kampanj!</span> <span class=\"bold\">" . $explanationtext . "</span></div>";
				}
			}
		} else {

			return "";

		}
	
	} elseif ($showstyle == 2) { // Denna visar bilden p� infosidan.
		if ($campaign_link != "") {
		
			if ($link_ext != "") {
				echo "<a target=\"_blank\" href=\"$link_ext\"><img border=\"0\" src=\"/../../$campaign_link\"></a>";
			} else {
				echo "<img border=\"0\" src=\"/../../$campaign_link\">";
				if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) || CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
					// echo "<br>- <a href=\"javascript:winPopupCenter(500, 700, '/order/campaign_detail.php?ID=$cnt');\" style=\"text-decoration: none\">Se detaljer";
					echo "<br>- <a target=\"_blank\" href=\"/order/admin/campaign.php?show=$cnt\" style=\"text-decoration: none\">Se detaljer<br>";
					echo "<hr noshade color=\"#C0C0C0\" align=\"left\" width=\"525\" size=\"1\">";
				}
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
		//$cont = "<a href=\"" . $rs->link . "\">" . $cont . "</a>"; // det h�r beh�vs nog inte
	} else { // annars, om bild

		$cont = "<a href=\"" . $link . "\"><img border=\"0\" src=\"" . $campaign_link . "\"></a><br>";
		
	}
	
	return $cont;
	
	*/
	} elseif ($showstyle == 3) { // Detta visar endast texten kampanj, till f�r prislistorna d�r utrymmet �r begr�nnsat

		if ($fi && !$sv) {
			echo "<b><font color=\"#85000D\" face=\"Verdana\" size=\"1\">Kampanja!</font></b>";
		} elseif ($fi && $sv) {
			echo "<b><font color=\"#85000D\" face=\"Verdana\" size=\"1\">Kampanj!</font></b>";
		} else {
			echo "<b><font color=\"#85000D\" face=\"Verdana\" size=\"1\">Kampanj!</font></b>";
		}
	
	} else {
	
		echo "&nbsp;";
	}
	
}

function getDiscountInfo($artnr, $showstyle, $tillverkar_id, $kategori_id, $discountCode="", $newpricelist = null) {
	
	global $fi, $sv, $no;

	/*
	$select = "SELECT discountCodes.*, discountCodeArticle.artnr as artnrMatch, discountCodeArticle.articleDiscounted FROM discountCodes LEFT JOIN discountCodeArticle ON discountCodeArticle.dscntId = discountCodes.cnt " . 
	" WHERE  (" . 
	" (discountCodeArticle.artnr = '" . $artnr . "' ) OR  (" .
	" (discountCodes.tillverkar_id =  " . $tillverkar_id . " ) OR " .
	" (discountCodes.artnr =  '" . $artnr . "' ) OR " .
	" (discountCodes.kategori_id =  " . $kategori_id . " ) ";
	*/
	
	$select = "SELECT discountCodes.*, discountCodeArticle.artnr as artnrMatch, discountCodeArticle.articleDiscounted ";
	$select .= "FROM discountCodes ";
	$select .= "LEFT JOIN discountCodeArticle ON discountCodeArticle.dscntId = discountCodes.cnt ";
	$select .= "WHERE ";
	$select .= "
			CASE WHEN discountCodeArticle.artnr IS NOT NULL THEN
				discountCodeArticle.artnr = '" . $artnr . "'
			ELSE
				CASE 
					WHEN discountCodes.artnr IS NOT NULL THEN discountCodes.artnr = '" . $artnr . "'
					WHEN discountCodes.kategori_id IS NOT NULL AND discountCodes.tillverkar_id IS NOT NULL THEN (discountCodes.kategori_id = " . $kategori_id . " AND discountCodes.tillverkar_id = " . $tillverkar_id . ")
					WHEN discountCodes.kategori_id IS NULL THEN discountCodes.tillverkar_id = " . $tillverkar_id . "
					ELSE discountCodes.kategori_id = " . $kategori_id . "
				END
			END
			";
	$select .= " AND validDate > now() AND validFrom < now() " . $notSel;
	if ($fi)
		$select .= " AND active_fi = -1";
    else if ($no)
		$select .= " AND active_no = -1";
	else
		$select .= " AND active_se = -1";

	$select .= " AND personal_discount = 0";
	$select .= " ORDER BY discountCodes.artnr DESC";
	/*
	$select = "SELECT discountCodes.*, discountCodeArticle.artnr as artnrMatch, discountCodeArticle.articleDiscounted FROM discountCodes LEFT JOIN discountCodeArticle ON discountCodeArticle.dscntId = discountCodes.cnt " . 
	$select .= "WHERE  ( ";
	$select .= "(discountCodeArticle.artnr = '" . $artnr . "' ) OR  ( ";
	$select .= "(discountCodes.artnr =  '" . $artnr . "' ) ";
	$select .= "OR (discountCodes.kategori_id =  " . $kategori_id . " ) "; 
	$select .= "OR (discountCodes.tillverkar_id =  " . $tillverkar_id . " ) ";
	// $select .= " "; 

	if ($this->basketValue > 0 && $tjosan == "hejsan") { // anv�nds inte nu d� vi inte vet hur vi ska anv�nda totalsumman. D�remot anv�nds siffran n�r det g�ller fri frakt
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

	// $select .= " AND validDate > getdate() AND validFrom < getDate() " . $notSel;
	$select .= " AND validDate > now() AND validFrom < now() " . $notSel;
	if ($fi)
		$select .= " AND active_fi = -1";
	else
		$select .= " AND active_se = -1";

	$select .= " ORDER BY discountCodes.artnr DESC";
	*/

	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $select;
		// exit;
	}
	$res = mysqli_query(Db::getConnection(), $select);

	if (mysqli_num_rows($res) > 0) {

		$row = mysqli_fetch_object($res);



		if ( ($row->freeFreight == -1 && ($row->discountCodes.totalSum > $this->basketValue || $row->discountCodes.totalSum == 0 || $row->discountCodes.totalSum == "" ) && !$fi ) ||
			($row->freeFreight == -1 && ($row->discountCodes.totalSum > $this->basketValue || $row->discountCodes.totalSum_fi == 0 || $row->discountCodes.totalSum_fi == "" ) && $fi )) { // om det avser fri frakt s� s�tts den h�r och d� forts�tter inte processen. 				
			return $this->formatDiscountTxt($row, $showstyle, $newpricelist);				
			echo "h�r";
		} elseif ($row->artnrMatch != "") {
			// tag fram tillh�rande artiklar till den du s�ker information om 
			$select = "SELECT * FROM discountCodeArticle WHERE dscntId = " . $row->cnt;
			$res2 = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res2) == 0 ) {				
				//return $this->formatDiscountTxt($row);
				return "";
			}
			
			while ($rowDiscount = mysqli_fetch_object($res2)) {	
				
				if ($rowDiscount->articleDiscounted == -1) {      // om det �r en rabatterad vara										     				
					return formatDiscountTxt($row);
				} elseif ($rowDiscount->includedArticles_id != "") {  // annars, om det �r en vara som skall ing� gratis, 				
					$sel =  "SELECT discountCodeIncludedArticles.*, Artiklar.lagersaldo, Artiklar.utgangen, Artiklar.ej_med FROM discountCodeIncludedArticles, Artiklar WHERE " . 
						"discountCodeIncludedArticles.includedArticles = Artiklar.artnr AND discountCodeIncludedArticles.includedArticles_id = " . $rowDiscount->includedArticles_id;
					
					$rs = (mysqli_query(Db::getConnection(), $sel));
					while ($rw = mysqli_fetch_object($rs)) {
						
						if (($rw->lagersaldo >= $rw->qty || $rw->utgangen == 0 ) && 
							 ($rw->lagersaldo > 0 || $rw->allowNoStock == -1)) {

						} else { // d� �r den ogiltig
							unset($includedArtsTmp);
							return "";
							break;
						}
						$i++;

					}
					
					return $this->formatDiscountTxt($row, $showstyle, $newpricelist);
					
				}
			}

		} else {
			
			if ($row->artnr != "" && $row->tillverkar_id == "" && $row->kategori_id == "") {
			
				return $this->formatDiscountTxt($row, $showstyle, $newpricelist);
			
			} elseif ($row->kategori_id == $kategori_id && $row->tillverkar_id == "") {
			
				return $this->formatDiscountTxt($row, $showstyle, $newpricelist);

			} elseif ($row->tillverkar_id == $tillverkar_id && $row->kategori_id == "") {
		
				return $this->formatDiscountTxt($row, $showstyle, $newpricelist);

			} elseif ($row->tillverkar_id == $tillverkar_id && $row->kategori_id == $kategori_id) {
		
				return $this->formatDiscountTxt($row, $showstyle, $newpricelist);
			
			} else {
			
			return "";
			
			}
		
		}
		
	} else {
	
		return "";
	
	}

}

function check_queue_web($artnr) {
	return true;
	$select = "SELECT artnr FROM Orderposter WHERE packad = 0 AND bokad = 0 AND  artnr = '" . $artnr . "' ";
	
	$res = mysqli_query(Db::getConnection(), $select);
	
	if (mysqli_num_rows($res) > 0) {
	
		return true;
		
	} else {
	
		return false;
	}

}

function display_queue_web($artnr) {

//$select = "SELECT SUM(antal) AS Antal FROM Orderposter WHERE packad = 0 AND bokad = 0 AND  artnr = '" . $artnr . "' ";
$select = "SELECT queue AS Antal FROM Artiklar WHERE artnr = '" . $artnr . "' ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
		
		echo $Antal . " st";
		
		endwhile;
		
	} else {
	
		echo "&nbsp;";
	
	}

}

function check_our_orderstock($artnr) {
	
	$select = "SELECT artnr FROM Inkopsposter WHERE levererat < antal AND antal > 0 AND artnr = '" . $artnr . "' ";
	
	$res = mysqli_query(Db::getConnection(), $select);
	
	if (mysqli_num_rows($res) > 0) {
	
		return true;
		
	} else {
	
		return false;
	}

}

function display_our_orderstock($artnr) {

$select = "SELECT SUM(antal-levererat) AS AntalBest FROM Inkopsposter WHERE levererat < antal AND antal > 0 AND artnr = '" . $artnr . "' ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
		
		echo $AntalBest . " st";
		
		endwhile;
		
	} else {
	
		echo "&nbsp;";
	
	}

}

function viewPacketDeliveryMorePackages($artnr, $mangd, $fi) {
	global $count, $lagersaldo, $lagersaldo_fi, $bestallt, $lev_datum, $lev_datum_norm, $lev_datum_norm_fi, $bestallningsgrans, $bestallningsgrans_fi, $fi, $sv, $bestallt_fi;		
	
	$select =  "SELECT Paketpriser.artnr_paket, Paketpriser.artnr_del, Paketpriser.antal, Artiklar.artnr, Artiklar.kategori_id, ";
	$select .= "Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar_fi.lagersaldo_fi, Artiklar.lagersaldo, Artiklar.beskrivning, Artiklar.bild, ";
	$select .= "tillverkare, Artiklar.lagersaldo, Artiklar.bestallt, ";
	$select .= "lev_datum_fi, bestallningsgrans, bestallningsgrans_fi, lev_datum_norm, lev_datum_norm_fi, lagersaldo_fi, bestallt_fi, bestallt, ";
	$select .= "Artiklar.bestallningsgrans, Artiklar.lev_datum_normal, Artiklar.kategori_id ";
	$select .= "FROM Artiklar ";
	$select .= "INNER JOIN Paketpriser ON Artiklar.artnr = Paketpriser.artnr_del ";
	$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
	$select .= "WHERE Paketpriser.artnr_paket = '$artnr' ";

	$res = mysqli_query(Db::getConnection(), $select);
	if (mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)):
		$description = ""; $bestallt_fi = 0;
		extract($row);
		
		$count = $antal*$mangd;
		if ($tillverkare != '.')
			$description = $tillverkare . " ";

		if (!$sv && $fi && $beskrivning_fi != "")
			$description .= $beskrivning_fi;
		else 	
			$description .= $beskrivning;
			
		$fulldescription = $description;

		if (strlen($description) >= 25)
			$description = substr ($description, 0, 25) . "...";
		
                //TODO: fixa l�nkarna till nya s�ttet
		if ($fi && !$sv) {
			$linkto = "info_fi.php?article=" . $artnr;
		} elseif ($fi && $sv) {
			$linkto = "info_fi_se.php?article=" . $artnr;
		} else {
			$linkto = "info.php?article=" . $artnr;
		}

		?>		
		<tr>
		  <td align="center" valign="top"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php echo $antal*$mangd; ?></font></td>
		  <td valign="top"><font color="#2B2B2B" face="Verdana, Arial" size="1"><a onMouseOver="this.T_WIDTH=350;return escape('<b><?php echo $fulldescription; ?></b><br><br><img border=\'0\' src=\'/thumbs/xlarge/bilder/<?php echo $bild; ?>\'>')" href="<?php echo $linkto; ?>"><?php echo $description; ?></a></font></td>
		  <td valign="top"><font color="#2B2B2B" face="Verdana, Arial" size="1">
		<?php	

		if ($kategori_id == 595 || $kategori_id == 629 || $kategori_id == 630) {
			echo "<font size=\"1\" face=\"Verdana\" color=\"#385F39\">finns i lager</font>";
		} else {
		$count = $antal * $mangd;
		$this->check_lager($artnr, $fi); 
		}
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
		  <td colspan="3"><font face="Verdana, Arial" size="1">
		  <a href="mailto:order@cyberphoto.se"><?php if ($fi == 'yes'): ?>Information saknas, kontakta cyberphoto f�r mer info<?php else: ?>Information saknas, kontakta cyberphoto f�r mer info<?php endif; ?></a>
		  </font></td>
		</tr>
		<?php
	}
	
}

function check_days_instock($artnr) {

// Used from CStock.php. 
$select = "SELECT DATEDIFF(dd, leveransTid, getdate()) AS DagarInStock FROM InkomnaArtiklar WHERE artnr = '" . $artnr . "' ORDER BY leveransTid DESC ";

$res = mssql_query ($select, $this->conn_ms);

$row = mssql_fetch_object($res);

	if (mssql_num_rows($res) > 0) {
	
		if ($row->DagarInStock > 180) {
		
			return true;
			
		} else {
		
			return false;
			
		}
		
	} else {
	
		return false;
	
	}

}

function display_days_instock($artnr) {
// CStock.php 
$select = "SELECT DATEDIFF(dd, leveransTid, getdate()) AS DagarInStock FROM InkomnaArtiklar WHERE artnr = '" . $artnr . "' ORDER BY leveransTid DESC ";

$res = mssql_query ($select, $this->conn_ms);

$row = mssql_fetch_object($res);

	if (mssql_num_rows($res) > 0) {
	
		echo "<font face=\"Verdana\" size=\"1\" color=\"#000000\">" . $row->DagarInStock . "&nbsp;dagar</font><font face=\"Verdana\" size=\"1\" color=\"#CC0000\">&nbsp;**&nbsp;Attention!&nbsp;**</font>";
		
		
	} else {
	
		echo "&nbsp;&nbsp;";
	
	}

}

function checkAge($age) {
	global $fi, $sv;

	if ($fi) {
		$getyear = substr($age,4,2);
		$getmonth = substr($age,2,2);
		$getday = substr($age,0,2);

		$age = $getyear . $getmonth . $getday;
	}
	
	if (substr($age,0,2) < 10) {

		$age = 20 . substr($age,0,6);
	
	} else {

		$age = 19 . substr($age,0,6);

	}
	
	$diff = time() - strtotime($age);

	if (($diff/60/60/24/365.25) > 18) {
	
		return true;
	
	} else {
	
		return false;
	
	}

}

function CheckIfEmailIsOK($epost) {
	if (preg_match('/^[-A-z0-9_.+]+[@][A-z0-9_-]+([.][A-z0-9_-]+)*[.][A-z]{2,8}$/', $epost)) {
		return true;
	} else {
		return false;
	}

}	

function getRentValue($levid, $basketvalue) {
	
	$select = "SELECT rate FROM tariffs where ids = '$levid' ";
	$res = mysqli_query(Db::getConnection(), $select);
	extract(mysqli_fetch_array($res));

	$calcMonthValue = $rate * $basketvalue;

	return round($calcMonthValue, 0);

}	
static function sql_inject_cleaner($str) {
    $str = str_ireplace("union", "", $str);
    $str = str_ireplace(";", "",  $str);
    $str = str_ireplace("--", "",  $str);
	$str = str_ireplace("\'", "", $str);
    $str = str_ireplace("'", "", $str);
    $str = str_ireplace(" select ", "", $str);
    $str = str_ireplace(" drop ", "", $str);
    $str = str_ireplace(" update ", "", $str);
    $str = str_ireplace("/*", "", $str);
	// allt nedan �r antagligen �verkurs - NK
    $str = str_ireplace(" HAVING ", "", $str);
    $str = str_ireplace(" CAST ", "", $str);
    $str = str_ireplace(" CONVERT ", "", $str);
    $str = str_ireplace(" INSERT ", "", $str);
    $str = str_ireplace(" WHERE ", "", $str);
    $str = str_ireplace(" CREATE ", "", $str);
    $str = str_ireplace(" PROCEDURE ", "", $str);
    $str = str_ireplace(" EXEC ", "", $str);
    $str = str_ireplace("_cmd", "", $str);
    return $str;
	
}
function sql_inject_clean($str) {
	return self::sql_inject_cleaner($str);
}
static function sql_inject_cleaner_light($str) {
    $str = str_ireplace("union", "", $str);
    $str = str_ireplace(";", "",  $str);
    $str = str_ireplace("--", "",  $str);
    //$str = str_ireplace("\'", "", $str);
	$str = str_ireplace("'", "", $str);	
    $str = str_ireplace(" select ", "", $str);
    $str = str_ireplace(" drop ", "", $str);
    $str = str_ireplace(" update ", "", $str);
    $str = str_ireplace("/*", "", $str);

    return $str;	
}
function sql_inject_clean_light($str) {
	return self::sql_inject_cleaner_light($str);
}

function checkStoreStatus($artnr) {
	
	$select = "SELECT lagersaldo FROM Artiklar WHERE artnr = '" . $artnr . "' ";

	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			extract(mysqli_fetch_array($res));

			if ($lagersaldo > 0) {

					return true;
			
				} else {

					return false;
			
				}
		
		} else {
		
			return false;
		
		}

}	

function checkIfSalesBundle($artnr) {
	
	$select = "SELECT * FROM Artiklar WHERE artnr = '" . $artnr . "' AND isSalesBundle = -1 ";
	$res = mysqli_query(Db::getConnection(), $select);
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $select;
	}
	
	if (mysqli_num_rows($res) > 0) {
		return true;
	} else {
		return false;
	}

}	

}
?>

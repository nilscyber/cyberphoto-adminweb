<?php
//require_once("CBasket_n.php");


/**
 * login info
 */
include_once("kreditor_shop_info.php");
	
include("connections.php");

Class CKreditor {
	
	/**
	 * constructor..
	 * @return CKreditor
	 */		

	function CKreditor() {
		/**
		if (!isset($_SESSION['CBasket']))
			$_SESSION['CBasket'] = new CBasket();
			
		$this->conn = $_SESSION['CBasket']->conn_ms;
		$this->conn_fi = $_SESSION['CBasket']->conn_fi;
		if ($fi)
			$this->conn_standard = $this->conn_fi;
		else 
			$this->conn_standard = $this->conn;		
		
		$this->conn = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
		@mssql_select_db ("cyberphoto", $this->conn);		
		$this->conn_standard = $this->conn;		
		*/
	}
	/**
	 * payment period in Kreditor language
	 *
	 * @var integer
	 */
	var $pclass;
	/**
	 * holds result from reserve_amount. 
	 * if $kreditor_status = 0 holds reservation id
	 * else holds error message
	 *
	 * @var array
	 */
	var $kreditor_result;	
	/**
	 * explanation (in Swedish) to present to customer when 
	 * status != 0
	 *
	 * @var string
	 */
	var $kreditor_result_text;	
	/**
	 * status for last reserve_amount
	 * case 0: success
	 * case -99: error
	 * default: other error
	 *
	 * @var integer
	 */	
	var $kreditor_status;
	/**
	 * payment period for periodic pay and invoice
	 *
	 * @var integer
	 */
	
	var $payment_period;	
	/**
	 * customers person no (social sec.)
	 *
	 * @var string
	 */
	var $cust_pno;
	/**
	 * Addresses retrieved from get_addresses
	 *
	 * @var string array
	 */
	var $cust_addresses;
	/**
	 * customers yearly salary, used when over goodsvalue > 10'000 SEK (incl. tax)
	 *
	 * @var integer
	 */
	var $cust_ysalary;
		
	/**
	 * true if entered pno uncorrect
	 *
	 * @var boolean
	 */
	var $incorrect_pno = false;
	/**
	 * error message, if something is wrong and should be printed on the page
	 *
	 * @var string
	 */
	var $err_message;
	/**
	 * Number of trials in session by customers. 
	 * Might be used in security controll of
	 * missuse
	 * @var int
	 */
	
	var $trials = 0;		
	/**
	 * contains status of credit from Kreditor. 
	 * 0 = not tested
	 * 1 = approved
	 * -1 = not approved
	 * @var int
	 */
	var $creditVerified = 0;
	/**
	 * if periodic payment type
	 * @var boolean
	 */
	var $periodic_pay = false;
	
	/**
	 * if invoice payment
	 * @var boolean
	 * 	 
	 */
	var $invoice_pay = false;	
	/**
	 * monthly rate when periodic payments
	 * @var double
	 */
	var $monthlyRate = 0;
	
	/**
	 * holds orderId, unique reference no for transaction to Kreditor
	 *
	 * @var long
	 */
	var $orderId = 0;
	/**
	 * holds connection variable for Sweden. 
	 * @var connection
	 */
	var $conn;
	/**
	 * holds connection variable for finland
	 * @var connection
	 */
	var $conn_fi;
	/**
	 * holds standard connection
	 *
	 * @var connection
	 */
	var $conn_standard;
	
	/**
	 * returns monthly rate based on number of months and basket size
	 *
	 * @param integer $months
	 * @param double $goodsvalue
	 * @return double
	 */
	function getSetMonthlyRate($months, $goodsvalue) {
		$monthlyRate = 250.35; // for testing
		// TODO: calculate monthly rate
		
		// TODO: set monthly rate
		$this->monthlyRate = $monthlyRate;
		return $this->monthlyRate;
	}
	function get_payment_period($pay) {
	
		if ($pay != "kreditor") {
			if ($pay == "avbetalning3")
				$this->payment_period = 3;
			elseif ($pay == "avbetalning6")
				$this->payment_period = 6;
			elseif ($pay == "avbetalning12")
				$this->payment_period = 12;
			elseif ($pay == "avbetalning24")
				$this->payment_period = 24;
			elseif ($pay == "avbetalning36")
				$this->payment_period = 36;
			else 	
				$this->payment_period = 0;
		}		
		return $this->payment_period;
				
	}
	/**
	 * uses function reserve_amount with CyberPhoto's values
	 * @return void
	 */
	function reserve_amount() {
		global $goodsvalueMoms, $pay, $KRED_SEND_BY_EMAIL, $KRED_FI_PNO, $KRED_ISO639_SV, $KRED_SE_PNO, $KRED_ISO3166_SE, $KRED_ISO3166_FI, $KRED_SEND_BY_MAIL; 
		global $KRED_SEK, $KRED_EUR;
		global $conn_standard;
		global $old_email, $eid, $secret;
		global $old_salary;
		if ($old_salary == "") $old_salary = 0;
		
		//echo $this->getSetOrderId();
		//$this->kreditor_status = -1;
		//$this->kreditor_result = "123456";
		//return;
		/**
		reserve_amount($pno, $amount, $reference, $referece_code, $orderid1, 
						$orderid2, $lev_addr, $f_addr, $email, $phone, $cell,
						$client_ip, $flags, $currency, $country, $language,
						$eid, $secret, $pno_encoding, $pclass, $ysalary,
						 &$result) {		
						 */
		
		if (!isset($this->cust_pno))
			return -1;
			
		if (!isset($this->orderId))
			$this->getSetOrderId();
		
		if ($fi)  {
			$country = $KRED_ISO3166_FI; 
			$pno_encoding = $KRED_FI_PNO;
			$currency = $KRED_EUR;
			$cntry = "fi";
		} else {
			$country = $KRED_ISO3166_SE;
			$language = $KRED_ISO639_SV;
			$pno_encoding = $KRED_SE_PNO;
			$currency = $KRED_SEK;
			$cntry = "se";
		}
		if ($fi && $sv)
			$language = $KRED_ISO639_SV;
		else 
			$language = $KRED_ISO639_SV;
		$fadr = mk_address($_SESSION['old_firstName'], $_SESSION['old_lastName'], $_SESSION['old_co'], $_SESSION['old_postnr'], $_SESSION['old_postadr'], $cntry);
		//$fadr = mk_address ("nils", "kohlström", "åkervägen 10", "91020", "HÖRNEFORS", "se");
		
		$this->pclass = $this->get_pclass($this->get_payment_period($pay));
		//echo "här: " .  $_SESSION['old_avisera'];
		//exit;
		// flags
		if ( (     ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$", $_SESSION['old_email'] )) && ($_SESSION['old_avisera'] == -1) ) {
			$flags = $KRED_SEND_BY_EMAIL;
			//echo "skickar som e-post!!";
		} else {
			$flags = $KRED_SEND_BY_MAIL;
			//echo "skickar som snigelpost!!";
		}
		//echo "flaggan: " . $flags;
		//echo "eposten: " . $_SESSION['old_email'];
		//exit;
		$status = reserve_amount($this->cust_pno, $goodsvalueMoms*100, "", $this->orderId, "", 
						"", $fadr, $fadr, $_SESSION['old_email'], $_SESSION['old_telnr'], $_SESSION['old_mobilnr'],
						$_SERVER['REMOTE_ADDR'], $flags, $currency, $country, $language,
						$eid, $secret, $pno_encoding, $this->pclass, $old_salary*100,
						 &$result);
		//echo "resultat: " . $status . " " . $result . "<br>"; 
		//print_r($fadr);
		/**
		$faddr = mk_address($ffname, $flname, $fstreet, $fpostno, $fcity, $fcountry);
		
		
		$status = reserve_amount($pno, $amount, $reference, $reference_code,
					 $orderid1, $orderid2, $laddr, $faddr,
					 $email, $phone, $cell, $clientip, $flags,
					 $currency, $country, $language, $eid, $secret, 
					 $pnoencoding, $pclass, $ysalary, $result);

						 */
		//print_r($fadr);
		//$this->pclass = 104;
		//echo "här: " . $this->pclass;
		
		//return;
		
		//$fadr = mk_address ("nils", "kohlström", "åkervägen 10", "91020", "HÖRNEFORS", "se");
		/**
		$status = reserve_amount("690627-8673", 700000, "nils", "123456", 
					"", "", $fadr, $fadr, 
					"nils@cyberphoto.se", "090-141141", "070-12345678", $_SERVER['REMOTE_ADDR'], $flags, 
					$currency, $country, $KRED_ISO639_SV, $eid, $secret, 
					$KRED_SE_PNO, $this->pclass, 40000000, &$result);
								
		*/
		$this->kreditor_status = $status;
		$this->kreditor_result = $result;
		if ($status != 0)
			$this->kreditor_result_text = strerror($result);
		else 
			$this->kreditor_result_text = "";
			
		$date = date("Y-m-d H:i:s");
		$updt = "INSERT INTO log_web (dat, webpage, comment, artnr) values ('" . $date . "', 'kreditor', 'resultat: " . $result . ". Expl: " . $this->kreditor_result_text . ".  Personnr:  " . $this->cust_pno . ". ordersumma: $goodsvalueMoms . pclass: $this->pclass', '" . $artnr . "')";
		//echo $updt . "<br>";
		@mssql_query($updt, $conn_standard);		
		
	} 
	
	/**
	 * sets pclass, requires paymentperiod to be set before
	 * on error returns -1
	 *
	 * @return array
	 */
	
	function get_pclass($pay) {
		global $conn_standard;

		$this->payment_period = $pay;
			//echo "här: " . $pay;
		/**	
		if (!isset($this->payment_period))  {
			$this->pclass = -1;
			return -1;
		} 
		$s = "select pclass from kreditor_pclass WHERE payment_period = " . $this->payment_period;
		echo s;
		$res = mssql_query($s, $conn_standard);
		if (mssql_num_rows($res) > 0) {
			$row = mssql_fetch_object($res);
			$pclass = $row->pclass;
			
		} else {
			$this->pclass = -1;
			return -1;
		}
		*/
		
		$pclass = -1;
		if ($pay == "invoiceme") {
			$pclass = -1;
		} else {
			switch ($this->payment_period) {
				case (3):
				$pclass = 105;
				break;
				case (6):
				$pclass = 106;
				break;
				case(12):
				$pclass = 107;
				break;
				case(24):
				$pclass = 103;
				break;
				case(36):
				$pclass = 104;
				break;
				default:				
				$pclass = -1;
				break;
								
			}
			
		}

		$this->pclass = $pclass;
			
		return $pclass;
	}
	/**
	 * sets and gets orderId in class
	 * @return long
	 */
	
	function getSetOrderId() {
		global $conn_standard;
		
		$str = "SELECT max(orderId) as maxId from orderIds";		
		$res = mssql_query($str, $conn_standard);
		if (mssql_num_rows($res)>0) {
			$row = mssql_fetch_object($res);
			$orderId = $row->maxId + 1;
			mssql_query("INSERT INTO orderIds (orderId) values (" . $orderId . ")"); 
			$this->orderId = $orderId;
			return $orderId;
		} else {		
			return 0;				
		}
	}
	
	
	function get_address($pno) {
		global $eid, $secret;
		$status = get_addresses($pno, $eid, $secret, 2, 1, $result);
		if ($status == 0) {
			$this->cust_addresses = $result;
			unset($this->err_message);
			unset($this->incorrect_pno);
			$this->cust_pno = $pno;
			
		} else  {
			$this->incorrect_pno = true;
			unset($this->cust_pno);
			unset($this->cust_addresses);
			$this->err_message = "<p><font face=\"Verdana\" size=\"2\" color=\"#85000D\"><b>Personnumret är felaktigt, vänligen pröva igen</b></font></p>";
		}
		//print_r($this->cust_addresses = $result);
	}
	function print_addresses() {
		$ret = "";
		
		if (!isset($this->cust_addresses))
			return;		
		//$this->cust_addresses[0][0] . " " . $this->cust_addresses[0][1]
		$ret =  "<br><input type='text' name='namn' size='24' value='" . $this->cust_addresses[0][0] . " " . $this->cust_addresses[0][1] . "' onFocus='this.blur()' style='font-family: Verdana; font-size: 8pt; background-color: #EBEBEB'><br>";
		$ret .=  "<input type='text' name='adress' size='24' value='" . $this->cust_addresses[0][2] . "' onFocus='this.blur()' style='font-family: Verdana; font-size: 8pt; background-color: #EBEBEB'><br>";
		$ret .=  "<input type='text' name='postnummer' size='5' value='" . $this->cust_addresses[0][3] . "' onFocus='this.blur()' style='font-family: Verdana; font-size: 8pt; background-color: #EBEBEB'> ";
		$ret .=  "<input type='text' name='ort' size='16' value='" . $this->cust_addresses[0][4] . "' onFocus='this.blur()' style='font-family: Verdana; font-size: 8pt; background-color: #EBEBEB'>";

		echo $ret;
		//return $ret;
	}
	/**
	 * transfers address from kreditor to our session address variables
	 * @return void
	 */
	function transfer_address() {	// not used	
		global $fi, $sv;
		if (!isset($this->cust_addresses))
			return;			
		//TODO: add name when available
		
		// TODO: c/o ? 
		$_SESSION['old_namn'] = $this->cust_addresses[0][0];
		//$_SESSION['old_co'] = $this->cust_addresses[0][1];
		$_SESSION['old_adress'] = $this->cust_addresses[0][1];
		$_SESSION['old_postnr'] = $this->cust_addresses[0][2];			
		$_SESSION['old_postadr'] = $this->cust_addresses[0][3];		 		 		
		
		// TODO: lc/o ? 
		$_SESSION['old_lnamn'] = $this->cust_addresses[0][0];
		//$_SESSION['old_lco'] = $this->cust_addresses[0][1];
		$_SESSION['old_ladress'] = $this->cust_addresses[0][1];
		$_SESSION['old_lpostnr'] = $this->cust_addresses[0][2];			
		$_SESSION['old_lpostadr'] = $this->cust_addresses[0][3];
		if ($fi) {
			$_SESSION['old_land_id'] = 358;
			$_SESSION['old_land_id'] = 358;
		} else {
			$_SESSION['old_lland_id'] = 46;
			$_SESSION['old_lland_id'] = 46;
		}	
		
	}
	/**
	 * returns and sets monthly cost for periodic payment
	 * on error returns -1
	 *
	 * @return double
	 */
	
	function periodic_cost($payment_period, $goodsValueMoms) {
		//global $goodsvalueMoms; // $eid and $secret is not necessary;
		//echo "här: " . $goodsValueMoms;
		$ret = 0;
		//$this->payment_period = $payment_period;
		//if (!isset($this->pclass))
		$pclass = $this->get_pclass_extra($payment_period);

		$flags = 0;
		
		//$country = "se";
		$currency = "SEK";
		//echo "här: " . $this->pclass;
		$status = periodic_cost($eid, $goodsValueMoms*100, $pclass, $currency, $flags, $secret, &$result);	
		if ($status == 0)		{
			//echo "här: " . $result . " ";
			$this->monthlyRate = round(doubleval($result) / 100, 0); 
			$ret = $this->monthlyRate;
			//echo "här: " . $ret;
		} elseif ($status == 99) {
			unset($this->monthlyRate);
			$ret = -1;
		} else {
			unset($this->monthlyRate);
			$ret = -1;
		}		
		return $ret;
	}
	function get_pclass_extra($pay) {
		global $conn_standard;
		
		//$this->payment_period = $pay;
			//echo "här: " . $pay;
		/**	
		if (!isset($this->payment_period))  {
			$this->pclass = -1;
			return -1;
		} 
		$s = "select pclass from kreditor_pclass WHERE payment_period = " . $this->payment_period;
		echo s;
		$res = mssql_query($s, $conn_standard);
		if (mssql_num_rows($res) > 0) {
			$row = mssql_fetch_object($res);
			$pclass = $row->pclass;
			
		} else {
			$this->pclass = -1;
			return -1;
		}
		*/
		
		$pclass = -1;
		if ($pay == "invoiceme") {
			$pclass = -1;
		} else {
			switch ($pay) {
				case (3):
				$pclass = 105;
				break;
				case (6):
				$pclass = 106;
				break;
				case(12):
				$pclass = 107;
				break;
				case(24):
				$pclass = 103;
				break;
				case(36):
				$pclass = 104;
				break;
				default:				
				$pclass = -1;
				break;
								
			}
			
		}

		//$this->pclass = $pclass;
			
		return $pclass;
	}	
}

?>
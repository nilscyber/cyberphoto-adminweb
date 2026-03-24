<?php
//CVerkkomaksut.php

class CVerkkomaksut {
	public $ordernr = "";
	public $amount = "";
	public $reference_number = "";
	public $order_description = "";
	
	public $return_address = "https://www.cyberphoto.fi/kundvagn/verkko/verkko_ok.php";
	public $cancel_address = "https://www.cyberphoto.fi/kundvagn/verkko/verkko_fail.php";
	public $notify_address = "https://www.cyberphoto.fi/kundvagn/verkko/verkko_notify.php";
	
	public $auth_md5_string;
    private $admin_pass = "d6ab09d2f5f9a985ca5802d32de9f20a"; // for manual login
	private $auth_code = "Ss8UF9qnNK4wfHE3WdZDjm7Be1kMy2";
	//private $auth_code = "6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ";
	private $merchant_id = 13772;
	private $currency = "EUR";	
	private $type = 4;
	public $culture = "fi_FI";
	public $CULTURE_FI = "fi_FI";
	public $CULTURE_SV = "sv_SE";
        public $test;
	
	
	function __construct($test = false) {
            $this->test = $test;
	}
	
	function encrypt(){
		$params = array(
			"MERCHANT_ID" => $this->merchant_id,
			"AMOUNT" => $this->amount,
			"ORDER_NUMBER" => $this->ordernr,
			"REFERENCE_NUMBER" => $this->reference_number,
			"ORDER_DESCRIPTION" => $this->order_description,
			"CURRENCY" => $this->currency,
			"RETURN_ADDRESS" => $this->return_address,
			"CANCEL_ADDRESS" => $this->cancel_address,
			"NOTIFY_ADDRESS" => $this->notify_address,
			"TYPE" => $this->type,
			"CULTURE" => $this->culture
		);

		$auth_array = array();
		foreach( $params as $key => $value )
		{
			$auth_array[] = $value;
		}
		$auth_string = $this->auth_code . "&" . implode( "&", $auth_array );
		$this->auth_md5_string = strtoupper( md5( $auth_string ) );
		return $this->auth_md5_string;
	}
	
	function htmlForm() {
		//echo $this->amount;
		$form  = "\n<form name=\"verkko\" action=\"https://ssl.verkkomaksut.fi/payment.svm\" method=\"post\" target=\"_parent\">\n";
		$form .= "<input name=\"MERCHANT_ID\" type=\"hidden\" value=$this->merchant_id>\n";
		$form .= "<input name=\"AMOUNT\" type=\"hidden\" value=\"$this->amount\">\n";
		$form .= "<input name=\"ORDER_NUMBER\" type=\"hidden\" value=\"$this->ordernr\">\n";
		$form .= "<input name=\"REFERENCE_NUMBER\" type=\"hidden\" value=\"$this->reference_number\">\n";
		$form .= "<input name=\"ORDER_DESCRIPTION\" type=\"hidden\" value=\"$this->order_description\">\n";
		$form .= "<input name=\"RETURN_ADDRESS\" type=\"hidden\" value=\"$this->return_address\">\n";
		$form .= "<input name=\"CANCEL_ADDRESS\" type=\"hidden\" value=\"$this->cancel_address\">\n";
		$form .= "<input name=\"NOTIFY_ADDRESS\" type=\"hidden\" value=\"$this->notify_address\">\n";
		$form .= "<input name=\"CURRENCY\" type=\"hidden\" value=\"$this->currency\">\n";
		$form .= "<input name=\"TYPE\" type=\"hidden\" value=$this->type>\n";
		$form .= "<input name=\"AUTHCODE\" type=\"hidden\" value=\"$this->auth_md5_string\">\n";
		$form .= "<input name=\"CULTURE\"type=\"hidden\" value=\"$this->culture\">";
		//$form .= "<input type=\"submit\" name=\"submit\" value=\"Siirry maksuun\">\n";
		//$form .= "<input type=\"image\" name=\"sv_button\" SRC=\"https://ssl.verkkomaksut.fi/logo/payhere_fin.jpg\">\n";
		$form .= "</form>\n";

		return $form;
	}
	function store_result() {
		//$_GET["ORDER_NUMBER"];$_GET["TIMESTAMP"];$_GET["PAID"];$_GET["RETURN_AUTHCODE"];
		//$test = true;
		//$_SERVER["REQUEST_URI"]
		//mail("admin@cyberphoto.se", "Log, store_result", "Sidan som hänvisade hit:: \n" . $_SERVER["REQUEST_URI"]);
		if ($_GET["PAID"] != "")  {
			//echo "här: <br>" . $this->get_md5_string_receipt() . "<br>" . $_GET["RETURN_AUTHCODE"];
			if ($this->get_md5_string_receipt() == $_GET["RETURN_AUTHCODE"] || $this->test) {
				if (!($_GET["ORDER_NUMBER"] > 999) || !(is_numeric($_GET["ORDER_NUMBER"])) ) {
					// TODO: logga konstigt försök? 
					//ej numeriskt 
				} else {
					
					$ordernr = $_GET["ORDER_NUMBER"];
					//$ordernr = 10000;
					$sel = "SELECT topsecret FROM cyberorder.Ordertabell WHERE ordernr = " . $ordernr;
					$row = (mysqli_fetch_object(mysqli_query(Db::getConnection(true), $sel)));
					if ($row->topsecret != "")
						$mess = date("j/n g:i") . " direktbetalning bekräftad /SYS\r\n" . ereg_replace("'", "''", $row->topsecret);
					else
						$mess = date("j/n g:i") . " direktbetalning bekräftad /SYS";
						
					$update = "UPDATE cyberorder.Ordertabell SET skickad_av = null, topsecret = '$mess'  WHERE ";
					$update .= "ordernr = " . $ordernr;
					///**
					if (!mysqli_query(Db::getConnection(true), $update)) {
						mail("admin@cyberphoto.se", "Obs!! Kunde ej uppdatera direktbetalning", "Order kunde ej uppdateras som skickad från verkkomaksut i filen CVerkkomaksut.php. Sql-frågan: \n" . $update);					
					} //*/					
						
					//echo "<p>" . $update;
				}
			} else {
				return false; // TODO: kanske logga hackningsförsök. 
			}				
		} else {		
			return false;
		}
	
	} 
	function check_ok() {
		if ($_GET["PAID"] != "")  {
			//echo "här: <br>" . $this->get_md5_string_receipt() . "<br>" . $_GET["RETURN_AUTHCODE"];
			if ($this->get_md5_string_receipt() == $_GET["RETURN_AUTHCODE"] || $this->test) {
				if (($_GET["ORDER_NUMBER"] < 999) || !(is_numeric($_GET["ORDER_NUMBER"])) ) {
					return false;
				} else {
					$sel = "SELECT * FROM cyberorder.Ordertabell WHERE ordernr = " . $_GET["ORDER_NUMBER"];
					//echo $sel;
					$row = (mysqli_fetch_object(mysqli_query(Db::getConnection(true), $sel)));
					return $row;
				}
			} else {
				return false; 
			}				
		} else {		
			return false;
		}	
	}
	function fail() {
		//echo "här: <br>" . $this->get_md5_string_receipt(true) . "<br>" . $_GET["RETURN_AUTHCODE"];
		if ($this->get_md5_string_receipt(true) == $_GET["RETURN_AUTHCODE"]) {
			if (!($_GET["ORDER_NUMBER"] > 999) || !(is_numeric($_GET["ORDER_NUMBER"])) ) {
				return false;
			} else {
				$sel = "SELECT * FROM cyberorder.Ordertabell WHERE ordernr = " . $_GET["ORDER_NUMBER"];
				//echo $sel;
				$row = (mysqli_fetch_object(mysqli_query(Db::getConnection(true), $sel)));
				return $row;
			}
		} else {
			return false; 
		}				
				
	}
	function get_md5_string_receipt($no_paid=false) {
		if ($no_paid) {
			$params = array(
				"ORDER_NUMBER" => $_GET["ORDER_NUMBER"],
				"TIMESTAMP" => $_GET["TIMESTAMP"]
			);
			
		} else {
			$params = array(
				"ORDER_NUMBER" => $_GET["ORDER_NUMBER"],
				"TIMESTAMP" => $_GET["TIMESTAMP"],
				"PAID" => $_GET["PAID"]
			);		
		}
		$auth_array = array();
		foreach( $params as $key => $value )
		{
			$auth_array[] = $value;
		}
		
		$auth_string = implode( "&", $auth_array ) . "&" . $this->auth_code ;
		$this->auth_md5_string = strtoupper( md5( $auth_string ) );
		return $this->auth_md5_string;	
	}

}



?>
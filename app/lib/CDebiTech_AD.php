<?php
require("connections.php");

Class CDebiTech {
	var $md5Key; var $sha1Key;
	function __construct() {
		$this->md5Key = "92A1C0372A4FBCF9D98086475A96E6AA9E636F93";
		$this->sha1Key = "D346F3325070D714EF5D61DC7CC1BAAE7A1E8CC1";
	}

	function getCountryKod($land_id) {
		global $conn_my;
		$select = "SELECT land_kod FROM Land WHERE land_id like '$land_id'";

		$res = mysqli_query($conn_my, $select);
		$row = mysqli_fetch_object($res);
	
		return $row->land_kod;
	
	}	
	function debiDeCrypt($strUrl) {
		return `/web/phplib/vedecrypt '$strUrl'`;
	}

	function debiCrypt($strUrl) {
		return `/web/phplib/veencrypt '$strUrl'`;
	}
	function getMacSend($orderrow, $fi = false) {
		if ($orderrow->GrandTotalSync > 0) {
			$ordersumma = number_format($orderrow->GrandTotalSync, 0, "", "");
		} elseif ($orderrow->GrandTotalRemain > 0) {
			$ordersumma = number_format($orderrow->GrandTotalRemain, 0, "", "");
		} else {
			$ordersumma = number_format($orderrow->totalsumma, 0, "", "");
		}
		// $ordersumma = number_format($orderrow->totalsumma, 0, "", "");
		$ordersumma = $ordersumma * 100;
		$datat = '1:vara:1:' . $ordersumma . ':';
		if ($fi)
			$datat .= '&' . "EUR";
		else
			$datat .= '&' . "SEK";
		$datat .= '&' . $orderrow->ordernr;
		$datat .= "&" . $this->sha1Key;
		$datat .= "&";

		return sha1($datat);
	}
	function verifyMacReturn($sum, $currency, $reply, $verifyId, $referenceData, $mac) {
		$calcMac = strtoupper($this->getMacReturn($sum, $currency, $reply, $verifyId, $referenceData));
		$mac = strtoupper($mac);
		//echo $calcMac . "<br>";
		//echo $mac;		
		if ($calcMac == $mac)
			return true;
		else
			return false;
	}
	function getMacReturn($sum, $currency, $reply, $verifyId, $referenceData) {
		//1250,00&SEK&A&12345678&ABC123&8CF47E1561ADAF8A07CFFF95099F823EDFADC18D&
		$datat = $sum;
		$datat .= '&' . $currency;
		$datat .= '&' . $reply;
		$datat .= '&' . $verifyId;
		$datat .= '&' . $referenceData; // refrenceData
		$datat .= '&' . $this->sha1Key;
		$datat .= "&";

		return sha1($datat);
	}
	function addLine($name, $value) {
		return "<input type=\"hidden\" name=\"" . $name . "\" value=\"" . $value . "\">\n";
	}
	function getFormLines($orderrow, $fi = false) {
		global $REMOTE_ADDR, $fi;
		
		if ($orderrow->GrandTotalSync > 0) {
			$ordersumma = number_format($orderrow->GrandTotalSync, 0, "", "");
		} elseif ($orderrow->GrandTotalRemain > 0) {
			$ordersumma = number_format($orderrow->GrandTotalRemain, 0, "", "");
		} else {
			$ordersumma = number_format($orderrow->totalsumma, 0, "", "");
		}
		$ordersumma = $ordersumma * 100;
		
		$form .= $this->addLine("data", '1:vara:1:' . $ordersumma . ':');
		if ($fi)
			$form .= $this->addLine("currency", "EUR");
		else
			$form .= $this->addLine("currency", "SEK");

		$form .= $this->addLine("shipment", "0");		

		$form .= $this->addLine("kundnr", $orderrow->kundnr);		

		$form .= $this->addLine("ordernr", $orderrow->ordernr);		
		if ( (     ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$", $orderrow->email ))) {		

			$form .= $this->addLine("eMail", trim($orderrow->email));
		}
		else {		

			$form .= $this->addLine("eMail", 'null@cyberphoto.se');
		}		


		$form .= $this->addLine("transID", $orderrow->ordernr);

		$form .= $this->addLine("namn", $orderrow->namn);		
		if (strlen($orderrow->co) == 0) {

			$form .= $this->addLine("billingAddress", $orderrow->ladress);		
		}
		else {

			$form .= $this->addLine("billingAddress", $orderrow->co);		
		}


		$form .= $this->addLine("billingCity", $orderrow->postadress);		

		$form .= $this->addLine("billingCountry", $this->getCountryKod($orderrow->fland_id));		

		$form .= $this->addLine("billingZipCode", $orderrow->postnr);		

		$form .= $this->addLine("billingFirstName", $orderrow->namn);		

		$form .= $this->addLine("billingLastName", $orderrow->namn);		

		$form .= $this->addLine("ip", $REMOTE_ADDR);		

		$form .= $this->addLine("uses3dsecure", "true");		

		$form .= $this->addLine("resetSession", "true");		

		$form .= $this->addLine("referenceNo", $orderrow->ordernr);		

		$form .= $this->addLine("metod", "login");	
	    $form .= $this->addLine("MAC", $this->getMacSend($orderrow, $fi) );

		return $form;

	}
	function cryptoData($orderrow) {
		global $REMOTE_ADDR, $fi;
		
		$ordersumma = number_format($orderrow->totalsumma, 0, "", "");
		$ordersumma = $ordersumma * 100;

		$datat = 'data=';
		$datat .= rawurlencode('1:vara:1:' . $ordersumma . ':');
		/**
		if ($fi)
			$datat .= '&currency=EUR';
		else 
			$datat .= '&currency=SEK';
		*/
		$datat .= '&currency=' . $orderrow->currency;
		$datat .= '&shipment=0';
		$datat .= "&kundnr=" . $orderrow->kundnr;
		$datat .= "&ordernr=" . $orderrow->ordernr;

		if ( (     ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$", $orderrow->email ))) {		
			$datat .= "&eMail=" . rawurlencode(trim($orderrow->email));
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

		//$datat .= "&eMail=" . rawurlencode($orderrow->email);
		//$datat .= "&billingAddress=" . rawurlencode ($orderrow->co);

		$datat .= "&billingCity=" . rawurlencode ($orderrow->postadress);
		$datat .= "&billingCountry=" . rawurlencode ($this->getCountryKod($orderrow->fland_id));
		$datat .= "&billingZipCode=" . rawurlencode ($orderrow->postnr);
		$datat .= "&billingFirstName=" . rawurlencode ($orderrow->namn);
		$datat .= "&billingLastName=" . rawurlencode ($orderrow->namn);
		$datat .= "&ip=". rawurlencode ($REMOTE_ADDR);
		$datat .= "&uses3dsecure=true";
		$datat .= "&resetSession=true";
		$datat .= "&referenceNo=" . $orderrow->ordernr;
		$datat .= "&metod=login";

		
		return rtrim($this->debiCrypt($datat) );		
	}
	function cryptoDataDirectPayment($orderrow) {
		global $REMOTE_ADDR, $fi, $sv;
		
		$ordersumma = number_format($orderrow->totalsumma, 0, "", "");
		$ordersumma = $ordersumma * 100;
	
		$datat = 'data=';
		$datat .= rawurlencode('1:vara:1:' . $ordersumma . ':');
	
		if ($fi)
			$datat .= '&currency=EUR';
		else 
		$datat .= '&currency=SEK';;
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
		$datat .= "&billingCountry=" . rawurlencode ($this->getCountryKod($orderrow->fland_id));
		$datat .= "&billingZipCode=" . rawurlencode ($orderrow->postnr);
		$datat .= "&billingFirstName=" . rawurlencode ($orderrow->namn);
		$datat .= "&billingLastName=" . rawurlencode ($orderrow->namn);
		$datat .= "&ip=". rawurlencode ($REMOTE_ADDR);		
		$datat .= "&referenceNo=" . $orderrow->ordernr;
		$datat .= "&metod=login";
		if ($fi && !$sv) {
			$datat .= "&lng=1";
		} elseif ($fi){
			$datat .= "&lng=2";			
		}
		//$datat .= "&uses3dsecure=true";
	
		return rtrim($this->debiCrypt($datat));		
	}

}
?>
<?php
require("connections.php");

Class CDebiTech {
	
	function CDebiTech() {
		
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
	
	function cryptoData($orderrow) {
		global $REMOTE_ADDR, $fi;
		
		$ordersumma = number_format($orderrow->totalsumma, 0, "", "");
		$ordersumma = $ordersumma * 100;

		$datat = 'data=';
		$datat .= rawurlencode('1:vara:1:' . $ordersumma . ':');

		$datat .= '&currency=SEK';
		$datat .= '&shipment=0';
		$datat .= "&kundnr=" . $orderrow->kundnr;
		$datat .= "&ordernr=" . $orderrow->ordernr;

		if ( (     ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$", $orderrow->email ))) {		
			$datat .= "&eMail=" . rawurlencode(trim($orderrow->email));
		}
		else {		
			$datat .= "&eMail=" . rawurlencode('null@cyberphoto.se');		
		}

		echo "hejsan: " . $fi . " <br>";
		
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
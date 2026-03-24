<?php

Class CDebiTech extends PaymentInterface {

    var $md5Key;
    var $sha1Key;
    //$sum, $currency, $reply, $verifyId, $referenceData

    var $sum;
    var $currency;
    var $reply;
    var $verifyId;
    var $referenceData;
    var $returnUrl =  array(      
                                "FI" => "https://www.cyberphoto.fi/kundvagn/check-out",                       
                                "SE" => "https://www2.cyberphoto.se/kundvagn/checka-ut",
                                "NO" => "https://www.cyberphoto.no/kundvagn/sjekk-ut", 
                                "DEFAULT" => "https://www2.cyberphoto.se/kundvagn/checka-ut"
                                        
                                );
    var $returnUrlRetry =  array(      
                                "FI" => "https://www.cyberphoto.fi/kundvagn/minun-sivu",                       
                                "SE" => "https://www2.cyberphoto.se/kundvagn/min-sida", 
                                "NO" => "https://www.cyberphoto.no/kundvagn/min-side", 
                                "DEFAULT" => "https://www2.cyberphoto.se/kundvagn/min-sida"
                                        
                                );    
    var $returnUrlDirect =  array(      
                                "FI" => "https://www.cyberphoto.fi/kundvagn/tilaukseni-tila",                       
                                "SE" => "https://www2.cyberphoto.se/kundvagn/min-orderstatus", 
                                "NO" => "https://www.cyberphoto.no/kundvagn/min-ordrestatus", 
                                "DEFAULT" => "https://www2.cyberphoto.se/kundvagn/min-orderstatus"
                                        
                                );      
   
    function __construct() {
        parent::__construct();
        $this->md5Key = "92A1C0372A4FBCF9D98086475A96E6AA9E636F93";
        $this->sha1Key = "D346F3325070D714EF5D61DC7CC1BAAE7A1E8CC1";
        
    }
    function getReturnUrl($retry, $directUrl = false) {
        $cntry = Locs::getCountry();
        if ($cntry == "")
            $cntry = "DEFAULT";
        if ($directUrl)        
            return $this->returnUrlRetry[$cntry];
        else if ($retry)
            return $this->returnUrlDirect[$cntry];
        else
            return $this->returnUrl[$cntry];
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

    function getMacSendRetry($orderrow) {
        // om GrandTotalSync finns så använder vi den. 
        if ($orderrow->GrandTotalSync > 0) {
            $ordersumma = number_format($orderrow->GrandTotalSync, 0, "", "");
        } elseif ($orderrow->GrandTotalRemain > 0) {
            $ordersumma = number_format($orderrow->GrandTotalRemain, 0, "", "");
        } else {
            $ordersumma = number_format($orderrow->totalsumma, 0, "", "");
        }

        $ordersumma = $ordersumma * 100;
        $datat = '1:vara:1:' . $ordersumma . ':';
        $datat .= '&' . $orderrow->currency;
        $datat .= '&' . $orderrow->ordernr;
        $datat .= "&" . $this->sha1Key;
        $datat .= "&";

        return sha1($datat);
    }

    function getMacSendNew() {
        $ordersumma = number_format($this->totalSum, 0, "", "");
        $ordersumma = $ordersumma * 100;
        $datat = '1:vara:1:' . $ordersumma . ':';
        $datat .= '&' . Locs::getCurrency();
        $datat .= '&' . $this->orderId; 
        $datat .= "&" . $this->sha1Key;
        $datat .= "&";

        return sha1($datat);
    }
    
    
    function verifyMacReturn($sum, $currency, $reply, $verifyId, $referenceData, $mac) {
        $mac = str_replace("MAC=", "", $mac);
        $calcMac = strtoupper($this->getMacReturn($sum, $currency, $reply, $verifyId, $referenceData));
        $mac = strtoupper($mac);
        //echo $calcMac . "<br>";
        //echo $mac;		
        if ($calcMac == $mac) {
            $this->verified = true;
            $this->sum = $sum;
            $this->currency = $currency;
            $this->reply = $reply;
            $this->verifyId = $verifyId;
            $this->referenceData = $referenceData;
            return true;
        } else {
            $this->verified = false;
            return false;
        }
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
	function getFormLinesNew($directUrl) {
        global $REMOTE_ADDR;
        $ordersumma = number_format($this->totalSum, 0, "", "");
        $ordersumma = $ordersumma * 100;

        $form .= $this->addLine("data", '1:vara:1:' . $ordersumma . ':');
        $form .= $this->addLine("currency", Locs::getCurrency() );

        $form .= $this->addLine("nettosum", round($this->total*100, 0));
        $form .= $this->addLine("moms", round($this->taxTotal * 100, 0));

        $form .= $this->addLine("shipment", "0");

        $form .= $this->addLine("kundnr", $_SESSION['kundnrsave']);

        $form .= $this->addLine("ordernr", $this->orderId);
        if (( ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$", trim($_SESSION['old_email'])))) {

            $form .= $this->addLine("eMail", trim($_SESSION['old_email']));
        } else {

            $form .= $this->addLine("eMail", 'null@cyberphoto.se');
        }


        $form .= $this->addLine("transID", $this->orderId);

        $form .= $this->addLine("namn", $_SESSION['old_namn']);
        if (strlen($_SESSION['old_co']) == 0) {

            $form .= $this->addLine("billingAddress", $_SESSION['old_adress']);
        } else {

            $form .= $this->addLine("billingAddress", $_SESSION['old_co']);
        }


        $form .= $this->addLine("billingCity", $_SESSION['old_postadr']);

        $form .= $this->addLine("billingCountry", $this->getCountryKod($_SESSION['old_land_id']));

        $form .= $this->addLine("billingZipCode", $_SESSION['old_postnr']);

        $form .= $this->addLine("billingFirstName", $_SESSION['old_namn']);

        $form .= $this->addLine("billingLastName", $_SESSION['old_namn']);

        $form .= $this->addLine("ip", $REMOTE_ADDR);

        $form .= $this->addLine("uses3dsecure", "true");

        $form .= $this->addLine("resetSession", "true");

        $form .= $this->addLine("referenceNo", $this->orderId);

        $form .= $this->addLine("metod", "login");
        $form .= $this->addLine("MAC", $this->getMacSendNew());
		// changed for version 2 of dibs
        $form .= $this->addLine("customReturnUrl", $this->getReturnUrl(false, $directUrl));
        $form .= $this->addLine("method", "cc.cekab");
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
			echo "session:\n";
			print_r($_SESSION);
			//exit;
		}
		$form .= $this->addLine("authOnly", "true");
		$form .= $this->addLine("language", $this->getLang());
        return $form;
        
    }
	function getLang() {
		//return "sv-FI";
		// Dibs can't handle finnish swedish
		if (strcasecmp(Locs::getCountry(),"NO") == 0 && strcasecmp(Locs::getLang(),"sv") == 0)
			return "sv-SE";
		else if (strcasecmp(Locs::getCountry(),"NO") == 0 && strcasecmp(Locs::getLang(),"no") == 0)
			return "nb-NO";
		else if (strcasecmp(Locs::getCountry(),"FI") == 0 && strcasecmp(Locs::getLang(),"sv") == 0)
			return "sv-SE";
		else
			return Locs::getLang() . "-" . Locs::getCountry();
	}
    function getFormLines($orderrow) {
        global $REMOTE_ADDR;
        
        // om GrandTotalSync finns så använder vi den.
        //print_r($orderrow);
        
        if ($orderrow->GrandTotalSync > 0) {
            $ordersumma = number_format($orderrow->GrandTotalSync, 0, "", "");
        } elseif ($orderrow->GrandTotalRemain > 0) {
            $ordersumma = number_format($orderrow->GrandTotalRemain, 0, "", "");
        } else {
            $ordersumma = number_format($orderrow->totalsumma, 0, "", "");
        }

        $ordersumma = $ordersumma * 100;

        $form .= $this->addLine("data", '1:vara:1:' . $ordersumma . ':');
        $form .= $this->addLine("currency", $orderrow->currency);

        $form .= $this->addLine("nettosum", round($orderrow->netto, 0));
        $form .= $this->addLine("moms", round($orderrow->moms, 0));

        $form .= $this->addLine("shipment", "0");

        $form .= $this->addLine("kundnr", $orderrow->kundnr);

        $form .= $this->addLine("ordernr", $orderrow->ordernr);
        if (( ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$", $orderrow->email))) {

            $form .= $this->addLine("eMail", trim($orderrow->email));
        } else {

            $form .= $this->addLine("eMail", 'null@cyberphoto.se');
        }


        $form .= $this->addLine("transID", $orderrow->ordernr);

        $form .= $this->addLine("namn", $orderrow->namn);
        if (strlen($orderrow->co) == 0) {

            $form .= $this->addLine("billingAddress", $orderrow->ladress);
        } else {

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
        $form .= $this->addLine("MAC", $this->getMacSendRetry($orderrow));
        $form .= $this->addLine("customReturnUrl", $this->getReturnUrl(true));
        $form .= $this->addLine("method", "cc.cekab");
		$form .= $this->addLine("authOnly", "true");	
		$form .= $this->addLine("language", $this->getLang());

        return $form;
    }

    function cryptoData($orderrow) {
        global $REMOTE_ADDR, $fi;

        $ordersumma = number_format($orderrow->totalsumma, 0, "", "");
        $ordersumma = $ordersumma * 100;

        $datat = 'data=';
        $datat .= rawurlencode('1:vara:1:' . $ordersumma . ':');

        $datat .= '&currency=' . $orderrow->currency;
        $datat .= '&shipment=0';
        $datat .= "&kundnr=" . $orderrow->kundnr;
        $datat .= "&ordernr=" . $orderrow->ordernr;

        if (( ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$", $orderrow->email))) {
            $datat .= "&eMail=" . rawurlencode(trim($orderrow->email));
        } else {
            $datat .= "&eMail=" . rawurlencode('null@cyberphoto.se');
        }


        $datat .= "&transID=" . $orderrow->ordernr;
        $datat .= "&namn=" . rawurlencode($orderrow->namn);
        if (strlen($orderrow->co) == 0) {
            $datat .= "&billingAddress=" . rawurlencode($orderrow->ladress);
        } else {
            $datat .= "&billingAddress=" . rawurlencode($orderrow->co);
        }

        //$datat .= "&eMail=" . rawurlencode($orderrow->email);
        //$datat .= "&billingAddress=" . rawurlencode ($orderrow->co);

        $datat .= "&billingCity=" . rawurlencode($orderrow->postadress);
        $datat .= "&billingCountry=" . rawurlencode($this->getCountryKod($orderrow->fland_id));
        $datat .= "&billingZipCode=" . rawurlencode($orderrow->postnr);
        $datat .= "&billingFirstName=" . rawurlencode($orderrow->namn);
        $datat .= "&billingLastName=" . rawurlencode($orderrow->namn);
        $datat .= "&ip=" . rawurlencode($REMOTE_ADDR);
        $datat .= "&uses3dsecure=true";
        $datat .= "&resetSession=true";
        $datat .= "&referenceNo=" . $orderrow->ordernr;
        $datat .= "&metod=login";


        return rtrim($this->debiCrypt($datat));
    }

    function cryptoDataDirectPayment($orderrow) {
        global $REMOTE_ADDR, $fi, $sv;

        $ordersumma = number_format($orderrow->totalsumma, 0, "", "");
        $ordersumma = $ordersumma * 100;

        $datat = 'data=';
        $datat .= rawurlencode('1:vara:1:' . $ordersumma . ':');

        $datat .= '&currency=' . $orderrow->currency;
            
        $datat .= '&shipment=0';
        $datat .= "&kundnr=" . $orderrow->kundnr;
        $datat .= "&ordernr=" . $orderrow->ordernr;


        if (strlen($orderrow->email == 0)) {
            $datat .= "&eMail=" . rawurlencode($orderrow->email);
        } else {
            $datat .= "&eMail=" . rawurlencode('null@cyberphoto.se');
        }
        $datat .= "&transID=" . $orderrow->ordernr;
        $datat .= "&namn=" . rawurlencode($orderrow->namn);
        if (strlen($orderrow->co) == 0) {
            $datat .= "&billingAddress=" . rawurlencode($orderrow->ladress);
        } else {
            $datat .= "&billingAddress=" . rawurlencode($orderrow->co);
        }

        $datat .= "&billingCity=" . rawurlencode($orderrow->postadress);
        $datat .= "&billingCountry=" . rawurlencode($this->getCountryKod($orderrow->fland_id));
        $datat .= "&billingZipCode=" . rawurlencode($orderrow->postnr);
        $datat .= "&billingFirstName=" . rawurlencode($orderrow->namn);
        $datat .= "&billingLastName=" . rawurlencode($orderrow->namn);
        $datat .= "&ip=" . rawurlencode($REMOTE_ADDR);
        $datat .= "&referenceNo=" . $orderrow->ordernr;
        $datat .= "&metod=login";
        if ($fi && !$sv) {
            $datat .= "&lng=1";
        } elseif ($fi) {
            $datat .= "&lng=2";
        }
        //$datat .= "&uses3dsecure=true";

        return rtrim($this->debiCrypt($datat));
    }
    function getPageSet($country, $lang) {  
		// always the same since 2016-03-30
		return "3ds_se_v31";
		
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.11xx") {
            echo $country . " : " . $lang;
            exit;
        }        
        if (strcasecmp($country,"NO") == 0) {
                return "3ds_no_v1";
        } else if (strcasecmp($country,"FI") == 0) {
            if (strcasecmp($lang,"sv") == 0)
                return "3ds_se_v2"; // swedish page is exactly the same
            else
                return "3ds_fi_v1";
        } else if (strcasecmp($country,"SE") == 0) {
            return "3ds_se_v3";
        } else {
            return "3ds_se_v3";
        }
    }
    /**
     * Prints form lines and pass customer to 
     * @param type $retry - true if later trial after login
     * @param type $orderrrows - if retry then supply orderrows as well
     * @param boolean $directUrl - if via direct url and no login
     */
    function sendToDibs($retry, $orderrow, $directUrl) {
        global $new_discountCode, $fraktartnr;
        if ($directUrl)
            $retry = true;
        
        if (!$retry) {
            $this->createInvoiceRows();
            $this->getSetOrderId();            
        }
        Log::addLog("Dibs - sending customer to dibs interface\n --- Dibs object --- \n" . print_r($this, true) . "\nDiscountCode: " . $new_discountCode . "\nFreight: " . $fraktartnr, Log::LEVEL_INFO);
		echo "<div id=\"centrering\">\n";
		
		echo "<form name=\"dibs\" action=\"https://securedt.dibspayment.com/verify/bin/cyber/index\" method=\"post\" target=\"_parent\">\n";
		echo "<p>" . l('kortsida_laddas') . "</p>\n";
		echo "<div><img border=\"0\" src=\"/images/load_bar.gif\"></div>\n";
		echo "<div class=\"top20\"><input type=\"submit\" value=\"" . l('ga_vidare') . "\"></div>\n";
                if ($retry)
                    echo $this->getFormLines($orderrow);                    
                else
                    echo $this->getFormLinesNew($directUrl);

		echo "<input type=\"hidden\" name=\"pageSet\" value=\"" . $this->getPageSet(Locs::getCountry(), Locs::getLang()) . "\">\n";
		echo "</form>\n";
		echo "<script language=\"JavaScript\">\n";
        echo "\tdocument.forms['dibs'].submit();\n";
        echo "function goTo() {\n";
		echo "\tdocument.forms['dibs'].submit();\n";
		echo "}\n";
		echo "window.setTimeout (\"goTo()\", 3500);\n";
        echo "</script>\n";
        
		
		echo "</div>\n";

	}
    

    /**
     * Saves dibs reference to ordertabell directly to avoid waiting time for customer until import/sync etc has occured
     * @param type $ordernr
     */
    public function saveToOrdertabell($ordernr) {
	$update = "UPDATE cyberphoto.Ordertabell set levklar = 3 WHERE ordernr = " . $ordernr;
        
        
	if ((mysqli_query(Db::getConnection(true), $update)) ) {
                // update to local database to avoid sync problems
                mysqli_query(Db::getConnection(), $update);

	} else {
            Log::addLog("Could not update Ordertabell after return from kortMan via dibs. All vars: " . print_r(get_defined_vars(), true), Log::LEVEL_WARN, null, null, true);
	}                  
    }
    
    public function triggerImport() {
		// inactivated since triggering here is too early because order has not been imported to adempiere yet
		if (false)
    		return true;
		    	
    	$xmlMess = "<ccAuthMsg>";
    	$xmlMess .= "   <OrderID>" . $this->finalOrdernr . "</OrderID>";
    	$xmlMess .= "      <AuthCode>" . $this->external_reference . "</AuthCode>";
    	$xmlMess .= "      <AuthAmount>" . $this->totalSum * 100 . "</AuthAmount>";
    	$xmlMess .= "      <AuthTime>" . date("Y-m-d H:i:s") . "</AuthTime>";
    	$xmlMess .= "	   <ccName>" . $this->payment_name . "</ccName>";
    	$xmlMess .= "	   <Currency>" . $this->currency . "</Currency>";
    	$xmlMess .= "   </ccAuthMsg>";
    	// $xmlMess is already UTF-8
    	
    	// include a library
    	//require_once ("Stomp.php");
    	
    	// make a connection
    	$con = new Stomp ( "tcp://devir.cyberphoto.se:61613" );
    	// connect
    	$con->connect ();
    	
    	// $con->send("/queue/ccAuthMsg", $xmlMess);
    	if ($con->send ( "ccAuthMsg", $xmlMess, array (
    			'persistent' => 'true'
    	) )) {
    		Log::addLog("Sent dibs xml to active mq. Dibs ref: " . $this->external_reference . " . XML: " . $xmlMess, Log::LEVEL_INFO);
    		return true;
    	} else {
    		Log::createTicketAndLog ( Log::RECIPIENT_IT, " Misslyckades med att skicka stomp (trigga import av dibs payment) : All variables: " . print_r ( get_defined_vars (), true ), Log::LEVEL_WARN );
			return false;
    	}
    	 
    }

}

?>
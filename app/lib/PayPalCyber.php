<?php

//require_once("Paypal_for_test.php");
require_once("Paypal.php");
require_once("CSvea.php");
require_once("Locs.php");
/**
 * Description of PayPalCyberPhoto
 *
 * @author nils
 */
class PayPalCyber extends Lionite_Paypal {
    var $products;
    var $orderId = 0;
    var $sveaObj;
    var $detailsRes;
    var $paymentId;
    var $payment_reference_id;
    var $connection;
    var $errorMess;
    var $token;

    const version = 1.2;
    
    public function __construct() {
        
        // set sandbox in parent when going live
        self::sandbox(false);
        $this->setLogin();
        $this->sveaObj = new CSvea();        
        $this->connectDb();
    }
    private function setLogin() {

        if (!parent::$_sandbox) {
            if (Locs::getCountry() == "FI") {
                $this->_settings['live']['username']="paypal_api1.cyberphoto.fi";
                $this->_settings['live']['password']="QCFCL6YSEASNZQF3";
                $this->_settings['live']['signature']="AWbepjhOCCkraGyNtyjbGZPQdehfA3A1w4qe.L4J6ZguOc8uX-4rLLR6";
            } else if (Locs::getCountry() == "NO") {
                $this->_settings['live']['username']="paypal_api1.cyberphoto.no";
                $this->_settings['live']['password']="FQT7QAL5S8BG5988";
                $this->_settings['live']['signature']="AHF7lSmFL0PayZSJnQhesmRYRwpWA-dgqwrxUn1UNYQZRbv2Zft2dgZ0";                       
                
            } else {
                $this->_settings['live']['username']="paypal_api1.cyberphoto.se";
                $this->_settings['live']['password']="AX79DCKEF2KF3GAJ";
                $this->_settings['live']['signature']="AiPC9BjkCyDFQXbSkoZcgqH3hpacA.5HfuFlPL9rXvoZr5j4Fn02pA0v";
            }
        } else {
            // if sandbox then already set in parent
        }        
    }
    public function connectDb() {
        if (!mysqli_ping($this->connection))
            $this->connection = Db::getConnection(true);
    }
    public function getOptions() {
        global $goodsvalueMoms, $fromMobile, $sv, $fi, $no;
        
        if ($this->orderId == 0)
            $this->orderId = $this->sveaObj->getSetOrderId();
        if ($this->orderId == 0)
            ; // TODO: ? 
        // sv_SE
        // fi_FI
        // fi_FI
        $curr = Locs::getCurrency();
        $locale = $_SESSION['currentLocale'];
        /**
        $curr = "SEK";
        if ($fi) $curr = "EUR";
        $cntry = $fi ? "FI" : "SE";
        $locale = "sv_SE";
        
        if ($fi && !$sv) {
            $cntryName = "Suomi";
            $locale = "fi_FI";
        } else if ($fi) {
            $cntryName = "Finland";
            $locale = "sv_FI";
        } else {
            $cntryName = "Sverige";
            $locale = "sv_SE";
        }
        */
        $phone = $_SESSION['old_mobilnr'] != "" ? $_SESSION['old_mobilnr'] : $_SESSION['old_telnr'];
        $_SESSION[''];
                 //number_format($ny_rabatt,      0, ',', ' ')
        $total = number_format($goodsvalueMoms, 0, '.', '');
        //dirname($_SERVER['REQUEST_URI'])
        // nuvarande sida (first_v53.php kanske just nu?)
        if ($fromMobile) {
			$base = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?site=mobile&step=1';
		} else {
			if ($fi && !$sv) {
				$base = 'https://www.cyberphoto.fi/kundvagn/check-out?site=desktop';
			} else if ($fi && $sv) {
				$base = 'https://www.cyberphoto.fi/kundvagn/checka-ut?site=desktop';
			} elseif ($no) {
				$base = 'https://www.cyberphoto.no/kundvagn/checka-ut?site=desktop';
			} else {
				$base = 'https://www.cyberphoto.se/kundvagn/checka-ut?site=desktop';
			}
		}
        // echo $base;
        // exit;
        //$_SERVER[''];
	$options = array(
            
            'cost' => $total, //Total cost of transaction
            'currency' => $curr, //Transaction currency, default is USD 
            //'currency' => "SEK", //Transaction currency, default is USD 
            //
            //'item_cost' => 'ITEMAMT', //Total cost of items in transaction, without shipping, handling or tax 
            //'shipping' => 'SHIPPINGAMT', //Shipping cost
            //'insurance' => 'INSURANCEAMT', //Insurance cost
            //'handling' => 'HANDLINGAMT', //Handling cost  
            //'tax' => 'TAXAMT', //Total tax amount
            //'desc' => 'DESC', //Transaction description
            //'custom' => 'CUSTOM', //Custom parameter - free-text
            'invoice' => $this->orderId, //Your own invoice or tracking number
            //'ipn_url' => 'NOTIFYURL', //IPN notification URL for this transaction ??? 
		
            // Shipping address fields
            'shipping_name' => $_SESSION['old_lnamn'],
            'shipping_address' => $_SESSION['old_lco'],
            'shipping_address_2' => $_SESSION['old_ladress'],
            'shipping_city' => $_SESSION['old_lpostadr'],
            //'shipping_state' => 'SHIPTOSTATE',
            'shipping_country_code' => $cntry,            
            //'shipping_country_name' => $cntryName, // deprecated
            'shipping_zipcode' => $_SESSION['old_lco'],
            'shipping_phone' => $phone, 
            'cancel' => $base . '&paypal_cancel=true', //URL to which customer is returned if he does not approve Paypal payment. - Required -
            'return' => $base . '&paypal_cancel=false', //URL to which customer is returned after approving Paypal payment. - Required -
            //'allowed_method' => 'PAYMENTREQUEST_0_ALLOWEDPAYMENTMETHOD', //Allowed payment method. Use 'InstantPaymentOnly' to force instant payments
            //'payment_action' => 'PAYMENTREQUEST_0_PAYMENTACTION', //Transaction payment action. Default is 'Sale', other values include 'Authorization' and 'Order'
            //'payment_id' => 'PAYMENTREQUEST_0_PAYMENTREQUESTID', //Unique identifier of a payment request - required for parallel payments
            //'no_shipping' => 'NOSHIPPING', // Shipping fields on checkout form: 0 - Show, 1 - Don't show, 2 - get address from Paypal account
            'locale' => $locale, // Set the locale of the Paypal checkout page (2 or 5 letter code)
            //'confirm_shipping' => 'REQCONFIRMSHIPPING', // Require Paypal confirmed address: 0 - no, 1 - yes
            //'allow_note' => 'ALLOWNOTE', // Allow buyer to leave a note for the merchant: 0 - no, 1 - yes (default)
            'email' => $_SESSION['old_lemail'], // Buyer Email - will be used to prefill the login field in the Paypal screen
            //'landing_page' => 'LANDINGPAGE', // 'Billing' | 'Login' - type of Paypal page to display            
            '_express-checkout-mobile' => $fromMobile,
	);
        $options = $this->sveaObj->utf8_encode_array($options);
        return $options;

    }
    /**
     * Get checkout url to forward customer to paypal
     * @return url
     */
    public function getCheckoutUrl() {

        $this->setLogin ();
        $items = $this->create_rows();
        //print_r($items);
        //echo "\n-----\n";
        $this->options = $this->getOptions();
        //print_r($this->options);
        //exit;
        //echo "\n-----\n";
        $url = parent::getCheckoutUrl($this->options, $items);
        if ($url == "")
            Log::addLog(print_r($this, true), Log::LEVEL_INFO, null, null);
        return $url;        
    }
    public function create_rows() {
        // use create_rows from CSvea
        require_once("CSvea.php");        
        $svea = new CSvea();
        
        $products = $svea->create_rows();
        //print_r($products);
        //exit;
	foreach($products as $product) {            
            $items[] = array(
                    'cost' => number_format(($product['PricePerUnit'] + ($product['PricePerUnit']*$product['VatPercent']/100) ), 0, '.', '') . ".00", //Item cost 
                    'name' => $product['Description'], //Item name
                    //'desc' => 'DESC', //Item description. Används inte, ihopsatt i 'Description' sen tidigare
                    'amount' => $product['NumberOfUnits'], //Amount of items of this type
                    //'tax' => 'TAXAMT', // Tax amount skippar, tror inte det behövs
                    'number' => $product['ArticleNumber'], // Item number
                    //'url' => 'ITEMURL', //Item URL
                    //'category' => 'ITEMCATEGORY', // Indicates type of goods: 'Digital' or 'Physical' (default)
                    //'weight' => 'ITEMWEIGHTVALUE', //Item weight
                    //'weight_unit' => 'ITEMWEIGHTUNIT' //Weight unit
            );
	}
        $this->products = $items;
        return $items;
        /**
         * Så här ser vår array ut som skapas i CPlaceOrder (via CSvea)
        $arrRows[] = array(
            "ArticleNumber" => $pacKey,
            "Description" => utf8_encode($beskrivning_alt),
            "PricePerUnit" => $visualPrice,
            "NumberOfUnits" => $count,
            "Unit" => "st",
            "VatPercent" => $momssats * 100,
            "DiscountPercent" => 0
        ); 
         * 
         */        
        

        
    }
    public function getResult($token) {
        $this->token = $token;
        // self::sandbox(true);
        // verify token and get details from paypal
        $this->detailsRes = $this->getCheckoutDetails($this->token);      
        // something is wrong
        if (!is_array($this->detailsRes)) {
            $this->errorMess .= " : checkout details is not array";
            return false;
        }
        // vet inte om den här raden behövs längre men den gör ingen skada i alla fall
        $options['token'] = $this->token; 
        
        // confirm payment and "use" it
        $this->paymentId = $this->confirmCheckoutPayment( $options );
        if(!is_string($this->paymentId)) {
            $this->errorMess .= " : confirm checkout failed. Details of result:  " . print_r($this->_confirmCheckoutPaymentResult,true) ;
            return false;
        }
        return $this->savePaymentReference();
        
    }    
    /**
     * Saves result from paypal to table payment_reference. Returns true on success
     * Called from getResult
     * 
     */
    public function savePaymentReference() {
        
        if (!is_array($this->detailsRes))
            return false;

        $insert = "INSERT INTO cyberorder.payment_reference(external_reference, totalSum, currency, orderId, payment_name, address1, city, 
            country_iso_code, country_name, postal, payment_phone, payment_email, payment_firstName, payment_lastName, 
            status, payment_date, comment, payment_address_confirmed) values (";
        
        $insert .= " '" . $this->paymentId . "' ,\n";
        $insert .= " " . $this->detailsRes['cost'] . ",\n";
        $insert .= " '" . $this->detailsRes['currency'] . "',\n";
        $insert .= " " . $this->detailsRes['invoice'] . ",\n";
        $insert .= " '" . $this->detailsRes['shipping_name'] . "',\n";
        $insert .= " '" . $this->detailsRes['shipping_address'] . "',\n";
        $insert .= " '" . $this->detailsRes['shipping_city'] . "',\n";
        $insert .= " '" . $this->detailsRes['shipping_country_code'] . "',\n";
        $insert .= " '" . $this->detailsRes['shipping_country_name'] . "',\n";
        $insert .= " '" . $this->detailsRes['shipping_zipcode'] . "',\n";
        $insert .= " '" . $this->detailsRes['shipping_phone'] . "',\n";
        $insert .= " '" . $this->detailsRes['email'] . "',\n";        
        $insert .= " '" . $this->detailsRes['first_name'] . "',\n";        
        $insert .= " '" . $this->detailsRes['last_name'] . "',\n";        
        $insert .= " '" . $this->detailsRes['status'] . "',\n";        
        $insert .= " '" . $this->detailsRes['timestamp'] . "',\n";        
        $insert .= " '" . str_replace ("'" , "\'", print_r($this, true)) . "',\n";
        $insert .= " " . ($this->detailsRes['address_status'] == "Confirmed" ? -1 : 0);        
        $insert .= ")\n";
        
        $this->connectDb();
        
        $res = mysqli_query($this->connection, $insert);
        if (!$res)
            $this->errorMess .= " : " . $insert;

        $this->payment_reference_id = mysqli_insert_id();

        return $res;
               
    }
    /**
     * Set ordernr when order is saved. Called in placeOrder.php
     * 
     * @param type $ordernr
     * @return boolean
     */
    public function finalize($ordernr) {
        if (!is_numeric($ordernr) || $ordernr < 1 || !is_numeric($this->payment_reference_id))
            return false;
        
        $update = " UPDATE payment_reference SET ordernr = " . $ordernr . " ";
        
        // checked earlier so check can be removed...
        if(is_string($this->paymentId)) {
            $update .= ", external_reference = '" . $this->paymentId . "' , status = 'PaymentActionCompleted' ";
        } else {
            $this->errorMess .= " : Could not confirm checkout payment " ;            
        }
        $update .= " WHERE payment_reference_id=" . $this->payment_reference_id;
        /**
        echo $update .  "\n";
        print_r($this);
        exit;        
        */
        $this->connectDb();
        
        if(!mysqli_query($this->connection, $update)) {
            $this->errorMess .= " : " . $update;
            return false;
        } else {
            return true;
        }
                
    }
}

?>

<?php
require_once("Paypal.php");
require_once("CSvea.php");
/**
 * Description of PayPalCyberPhoto
 *
 * @author nils
 */
class PayPalCyber extends Lionite_Paypal {
    var $products;
    var $orderId = 0;
    var $sveaObj;
    
    public function __construct() {
        // remove when going live, set to true for default for safety... 
        self::sandbox(true);
        $this->sveaObj = new CSvea();
    }
    public function getOptions() {
        global $goodsvalueMoms, $fi, $sv;
        
        if ($this->orderId == 0)
            $this->orderId = $this->sveaObj->getSetOrderId();
        if ($this->orderId == 0)
            ; // TODO: ? 
        
        $curr = "SEK";
        if ($fi) $curr = "EUR";
        $cntry = $fi ? "FI" : "SE";
        if ($fi && !$sv) {
            $cntryName = "Suomi";
        } else if ($fi) {
            $cntryName = "Finland";
        } else {
            $cntryName = "Sverige";
        }
        $phone = $_SESSION['old_mobilnr'] != "" ? $_SESSION['old_mobilnr'] : $_SESSION['old_telnr'];
        $_SESSION[''];
                 //number_format($ny_rabatt,      0, ',', ' ')
        $total = number_format($goodsvalueMoms, 2, '.', '');
        //dirname($_SERVER['REQUEST_URI'])
        // nuvarande sida (first_v53.php kanske just nu?)
        $base = 'http://' . $_SERVER['HTTP_HOST'] .  $_SERVER['PHP_SELF'];
        //echo $base;
        //exit;
        //$_SERVER[''];
	$options = array(
            
            'cost' => $total, //Total cost of transaction
            'currency' => $curr, //Transaction currency, default is USD 
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
            'cancel' => $base, //URL to which customer is returned if he does not approve Paypal payment. - Required -
            'return' => $base, //URL to which customer is returned after approving Paypal payment. - Required -
            //'allowed_method' => 'PAYMENTREQUEST_0_ALLOWEDPAYMENTMETHOD', //Allowed payment method. Use 'InstantPaymentOnly' to force instant payments
            //'payment_action' => 'PAYMENTREQUEST_0_PAYMENTACTION', //Transaction payment action. Default is 'Sale', other values include 'Authorization' and 'Order'
            //'payment_id' => 'PAYMENTREQUEST_0_PAYMENTREQUESTID', //Unique identifier of a payment request - required for parallel payments
            //'no_shipping' => 'NOSHIPPING', // Shipping fields on checkout form: 0 - Show, 1 - Don't show, 2 - get address from Paypal account
            //'locale' => 'LOCALECODE', // Set the locale of the Paypal checkout page (2 or 5 letter code)
            //'confirm_shipping' => 'REQCONFIRMSHIPPING', // Require Paypal confirmed address: 0 - no, 1 - yes
            //'allow_note' => 'ALLOWNOTE', // Allow buyer to leave a note for the merchant: 0 - no, 1 - yes (default)
            'email' => $_SESSION['old_lemail'], // Buyer Email - will be used to prefill the login field in the Paypal screen
            //'landing_page' => 'LANDINGPAGE', // 'Billing' | 'Login' - type of Paypal page to display            
	);    
        return $options;

    }
    /**
     * Get checkout url to forward customer to paypal
     * @return url
     */
    public function getCheckoutUrl() {
        $items = $this->create_rows();
        //print_r($items);
        //echo "\n-----\n";
        $options = $this->getOptions();
        //print_r($options);
        //echo "\n-----\n";
        return parent::getCheckoutUrl($options, $items);        
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
    
}

?>

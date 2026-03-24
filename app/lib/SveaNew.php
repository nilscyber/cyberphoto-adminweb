<?php
//error_reporting( E_ALL );
ini_set('display_errors', 'On');

// Include Svea PHP integration package.
$svea_directory = "sveawebpay/";
require_once( $svea_directory . "Includes.php" );


Class SveaNew extends PaymentInterface {
	var $config;
	var $svearows = array();
	var $sveacustomerinfo;
	var $sveaOrder;
	var $sveaAddress;
	
    function __construct($test = false) {
		$test = true; // TODO:
		if ($test) {
			$this->config = Svea\SveaConfig::getTestConfig();
		} else {	
			$this->config = Svea\SveaConfig::getProdConfig();
		}
    }	
	public function createAndSubmitForm() {
		//$this->createInvoiceRows();
        if ($this->orderId == 0)
            $this->getSetOrderId();
				
		$this->sveaOrder = WebPay::createOrder( $this->config );
		// You then add information to the order object by using the methods in the Svea\CreateOrderBuilder class.
		// For a Card order, the following methods are required:
		$cntry = Locs::getCountry();
        $lang = Locs::getLang();
		$curr = Locs::getCurrency();
		$this->sveaOrder->setCountryCode($cntry);                         // customer country, we recommend basing this on the customer billing address
		
		$this->sveaOrder->setCurrency(curr);                           // order currency
		$this->sveaOrder->setClientOrderNumber( $this->orderId );  // required - use a not previously sent client side order identifier, i.e. "order #20140519-371"
		$this->createInvoiceRows();
		// You may also chain fluent methods together:
		/**
		$this->sveaOrder
				->setCustomerReference("customer #123")         // optional - This should contain a customer reference, as in "customer #123".
				->setOrderDate("2014-05-28")                    // optional - or use an ISO8601 date as produced by i.e. date('c')
		;			
	    */
		// For card orders the ->addCustomerDetails() method is optional, but recommended, so we'll add what info we have
		$this->sveacustomerinfo = WebPayItem::individualCustomer(); // there's also a ::companyCustomer() method, used for non-person entities
        if (strlen($_SESSION['old_co']) > 1)
            $addr = $_SESSION['old_co'];
        if (strlen($_SESSION['old_ladress']) > 1) {
            if (strlen($addr) > 0)
                $addr .= " / ";
            $addr = $_SESSION['old_ladress'];
        }
        if (strlen($_SESSION['old_telnr']) > 5)
            $phone = $_SESSION['old_telnr'];
        else
            $phone = $_SESSION['old_mobilnr'];
		// Set customer information, using the methods from the IndividualCustomer class
		$parts = explode(" ", $_SESSION['old_namn']);
		$lastname = array_pop($parts);
		$firstname = implode(" ", $parts);	
			
		$this->sveacustomerinfo->setName( $firstname, $lastname);
		$this->sveaAddress = Svea\Helper::splitStreetAddress($addr); // Svea requires an address and a house number
		$this->sveacustomerinfo->setStreetAddress( $sveaAddress[0], $sveaAddress[1] );
		$this->sveacustomerinfo->setZipCode( $_SESSION['old_postnr'] )->setLocality( $_SESSION['old_postadr'] );

		$this->sveaOrder->addCustomerDetails( $this->sveacustomerinfo );

		// We have now completed specifying the order, and wish to send the payment request to Svea. To do so, we first select a payment method.
		// For card orders, we recommend using the ->usePaymentMethod(PaymentMethod::SVEACARDPAY).
		$myCardOrderRequest = $this->sveaOrder->usePaymentMethod(PaymentMethod::SVEACARDPAY);


		// Then set any additional required request attributes as detailed below. (See Svea\PaymentMethodPayment and Svea\HostedPayment classes for details.)
		$myCardOrderRequest
			->setCardPageLanguage($lang)                                     // ISO639 language code, i.e. "SV", "EN" etc. Defaults to English.
			//->setReturnUrl("http://localhost/".$this->getPath()."/landingpage.php"); // The return url where we receive and process the finished request response
			->setReturnUrl("https://www.cyberphoto.se/order/dibs/svea.php"); // The return url where we receive and process the finished request response
		// Get a payment form object which you can use to send the payment request to Svea
		$myCardOrderPaymentForm = $myCardOrderRequest->getPaymentForm();	
		//Tools::print_rw($this);
		echo "<pre>";
		print_r( "press submit to send the card payment request to Svea");
		print_r( $myCardOrderPaymentForm->completeHtmlFormWithSubmitButton );
		Tools::print_rw($this->sveaOrder);
		
	}
	public function createInvoiceRows() {
		echo "hej";
		$rows = parent::createInvoiceRows();

		foreach ($rows as $row) {
			//Tools::print_rw($row);
			$this->sveaOrder->addOrderRow(
						WebPayItem::orderRow()
							->setarticleNumber( $row['ArticleNumber'] )
							->setQuantity( (int)$row['NumberOfUnits'] )
							->setunit( $row['Unit'] )
							->setamountExVat( $row['PricePerUnit'])
							//->setAmountIncVat( $row['PricePerUnit'] * $row['NumberOfUnits'] )
							->setDescription( $row['Description'] )
							->setVatPercent( (int)$row['VatPercent'] )
							->setDiscountPercent( $row['DiscountPercent'])
							
							
							
							
			);	
		}
		//echo "<pre>".gettype($this->sveaOrder->orderRows[0]->vatPercent). " : " . gettype($this->sveaOrder->orderRows[1]->vatPercent)."</pre>";
		//Tools::print_rw($rows);
		//Tools::print_rw($this->sveaOrder->orderRows);
			
	}
	
	public function submitForm() {
		
	}
	public function triggerImport() {

		}
	public function getPath() {
		$myURL = $_SERVER['SCRIPT_NAME'];
		$myPath = explode('/', $myURL);
		unset( $myPath[count($myPath)-1]);
		$myPath = implode( '/', $myPath);

		return $myPath;
	}		
}

?>
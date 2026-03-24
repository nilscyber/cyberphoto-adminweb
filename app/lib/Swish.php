<?php

//require_once("PaymentInterface.php");
//https://portal.swish.nu/company/certificates
Class Swish extends PaymentInterface {
	/** Url to where we make the request */

	/** Change the three below to change from/to test/production */
	//public $url_payment_requests = 'https://mss.swicpc.bankgirot.se/swish-cpcapi/api/v1/paymentrequests'; // -test
	//public $url_payment_requests = 'https://swicpc.bankgirot.se/swish-cpcapi/api/v1/paymentrequests'; // -production//
	public $url_payment_requests = 'https://cpc.getswish.net/swish-cpcapi/api/v1/paymentrequests'; // -production
	
	//public $certificate_path = '/home/phplib/swish/swish_cert.p12'; // test certificate
	//public $certificate_path = '/home/phplib/swish/swish3.pfx'; // production	
	public $certificate_path = '/home/phplib/swish/swish20250609.pfx'; // production	
	
	/** Our swish account number */
	//public $payee = '1231181189'; // test-number
	public $payee = '1236562151'; // production (cyberphoto	
	
	
	
	public $url_waiting_page = 'https://www2.cyberphoto.se/kundvagn/hantera-din-swish-betalning';
	public $truststore_path = '/etc/pki/java/cacerts';
	public $truststore_pwd = 'changeit';

	public $certificate_pwd = 'swish'; // ygzOpq
	
	/** Buyers swish (mobile) number */
	public $payer;
	/** Message that's shown in customers swish app when making the payment */
	public $message = 'Avser betalning till CyberPhoto AB';
	/** Shell script (java) for making requests to swish */	
	public $shell_path = '/home/phplib/swish/swish_v3.sh';
	/** Url returned from swish for "manual" payment confirmation check */
	public $paymentConfirmationUrl;
	/** Response code for payment requests */
	public $response;
	/** Error code for payment requests */
	public $errorCode;
	/** Error in plain text for payment requests */
	public $errorMessage;
	
	public $swishPaymentId;
	public $returningToCart = false;
	
	/** Some error codes we might receive when making a payment request. For full list check swish docs */
	/** Payer alias invalid */
	const ERROR_REQUEST_BE18 = 'BE18';
	/** Another PaymentRequest already exists for this payerAlias. */
	const ERROR_REQUEST_RP06 = 'RP06';
	/** Payer not Enrolled */
	const ERROR_REQUEST_ACMT03 = 'ACMT03';
	/** Counterpart is not activated */
	const ERROR_REQUEST_ACMT01 = 'ACMT01';
	/** Amount value is too large */
	const ERROR_REQUEST_AM02 = 'AM02';
	/** Some of the error codes we might receive to callBackUrl from swish. For full list check swish docs */	
	/** Transaction declined */
	const ERROR_CONFIRMATION_RF07 = 'RF07'; // RF07 replaces AC07 AC15 AM14 AM21 AC05 AC06 AM04 DS0K
	/** Transaction declined */
	const ERROR_CONFIRMATION_AC05 = 'AC05';
	/** Transaction declined */
	const ERROR_CONFIRMATION_AC06 = 'AC06';
	/** Transaction declined */
	const ERROR_CONFIRMATION_AC07 = 'AC07';
	/** Transaction declined */
	const ERROR_CONFIRMATION_AC15 = 'AC15';
	/** Transaction declined */
	const ERROR_CONFIRMATION_AM04 = 'AM04';

	/** Transaction declined */
	const ERROR_CONFIRMATION_AM14 = 'AM14';
	/** Transaction declined */
	const ERROR_CONFIRMATION_AM21 = 'AM21';
	/** Transaction declined */
	const ERROR_CONFIRMATION_DS0K = 'DS0K';
	
	/** Payer cancelled BankId signing */
	const ERROR_CONFIRMATION_BANKIDCL = 'BANKIDCL';
	/** Bank system processing error */
	const ERROR_CONFIRMATION_FF10 = 'FF10';
	/** Swish timed out before the payment was started */
	const ERROR_CONFIRMATION_TM01 = 'TM01';
	/** Swish timed out waiting for an answer from the banks after payment was started. 
	 * In this case payment may have been successful  */
	const ERROR_CONFIRMATION_DS24 = 'DS24';
	/** Array of error messages to customer, depending of error code from  */
	public $CUSTOMER_MESSAGE = array('AC05' => 'Transaktionen blev nekad, vänligen använd ett annat swish-nummer eller välj ett annat betalsätt', 
									 'AC06' => 'Transaktionen blev nekad, vänligen använd ett annat swish-nummer eller välj ett annat betalsätt', 
									 'AC07' => 'Transaktionen blev nekad, vänligen använd ett annat swish-nummer eller välj ett annat betalsätt', 
									 'AC15' => 'Transaktionen blev nekad, vänligen använd ett annat swish-nummer eller välj ett annat betalsätt', 
									 'AM04' => 'Transaktionen blev nekad, vänligen använd ett annat swish-nummer eller välj ett annat betalsätt', 
									 'AM14' => 'Transaktionen blev nekad, vänligen använd ett annat swish-nummer eller välj ett annat betalsätt', 
									 'AM21' => 'Transaktionen blev nekad, vänligen använd ett annat swish-nummer eller välj ett annat betalsätt', 
									 'DS0K' => 'Transaktionen blev nekad, vänligen använd ett annat swish-nummer eller välj ett annat betalsätt', 
									 'RF07' => 'Transaktionen blev nekad, vänligen använd ett annat swish-nummer eller välj ett annat betalsätt',
									 'BANKIDCL' => 'Du har avbrutit betalningen, vänligen pröva igen eller välj ett annat betalsätt', 
									 'FF10' => 'Bankens system krånglar tyvärr, vänligen pröva igen eller välj ett annat betalsätt', 
									 'TM01' => 'Betalningen har avbrutits pga av att det tog för lång tid, vänligen pröva igen eller välj ett annat betalsätt', 
									 'DS24' => 'Något gick fel i kommunikationen med din bank. Vi kan inte veta säkert om betalningen är genomförd eller inte. Vänligen kontakta oss så löser vi det här manuellt. Kolla först om pengar dragits från ditt konto', 
									 'BE18' => 'Swish-numret du angav verkar inte vara korrekt, vänligen kontrollera numret och pröva igen', 
									 'AM02' => 'Beloppet är högre än vad din bank godkänner. Eventuellt kan du godkänna betalningen i förväg via din vanliga bankinloggning och på så sätt ändå kunna genomföra betalningen. Om inte så hoppas jag något annat betalsätt passar dina behov', 
									 'RP06' => 'Det pågår redan en betalning. Vänligen avbryt den i din swish-app, när det är gjort, klicka återigen på skicka beställning', 
									 'ACMT03' => 'Swish-numret du angav verkar inte vara aktiverat för Swish. Vänligen aktivera numret först eller byt till ett annat nummer', 									 
									 'ACMT01' => 'Swish-numret du angav verkar inte vara aktiverat för Swish. Vänligen aktivera numret först eller byt till ett annat nummer'								 
									 );	
	
	/** Status of payment, paid, i.e successful payment */
	const PAYMENT_STATUS_PAID = 'PAID';
	/** Status of payment, created, waiting for customer input and we're waiting for callbackUrl */
	const PAYMENT_STATUS_CREATED = 'CREATED';
	/** Status of payment, declined */
	const PAYMENT_STATUS_DECLINED = 'DECLINED';
	/** Status of payment, error */
	const PAYMENT_STATUS_ERROR = 'ERROR';
	
	const METHOD_PAYMENT_REQUEST = 1;
	const METHOD_PAYMENT_CONFIRMATION_CALLBACK = 3;
	const METHOD_PAYMENT_REFUND = 2;
	
	public $ERROR_TRANS_DECLINED = array(self::ERROR_CONFIRMATION_AC05, self::ERROR_CONFIRMATION_AC06, self::ERROR_CONFIRMATION_AC07, self::ERROR_CONFIRMATION_AC15, self::ERROR_CONFIRMATION_AM04, self::ERROR_CONFIRMATION_AM14, self::ERROR_CONFIRMATION_AM21, self::ERROR_CONFIRMATION_RF07);
	
	public $outside_first = false;
	
	public $retry;
	public $retry_url;
	
	public $cntr = 0;
	public $vars;
	function __construct($retry = false) {
		$this->return_url = 'https://www2.cyberphoto.se/kundvagn/swish_return.php';
		$this->currency = 'SEK';
		$this->retry = $retry;
		if ($retry) {
			$this->payment_method_s = 'swish';
			$this->payment_method = 24;
		}
	}
	function getVars($method) {
		$vars = $method . ' ' . $this->url_payment_requests . '  '  . $this->truststore_path . '  '  . 
				$this->truststore_pwd . '  '  . $this->certificate_path . '  '  . $this->certificate_pwd . 
				'  '  . $this->orderId . '  '  . $this->return_url . '  '  . $this->payer . '  '  .
				$this->payee . '  '  . $this->totalSum . '  '  . $this->currency . '  "'  . $this->message . '"' . '  "'  . $this->swishPaymentId . '"';
		error_log(print_r($vars, true));
		return $vars;

	}
	function doPaymentRequest($payer) {
		
		if (true || !$this->retry) {
			$this->getSetOrderId();
			$this->createInvoiceRows();	
		}
		error_log("0");
		if ($payer != '')
			$this->payer = $payer;
		else 
			$this->payer = $this->payment_mobile_phone;
		error_log("1");
		$this->vars = $this->getVars(self::METHOD_PAYMENT_REQUEST);
		error_log("2");
		//error_log($this->vars);
		// get payment request from java shell script
		error_log("Shell path: " . $this->shell_path);
		error_log("3");
		//$res = exec($this->shell_path . ' ' . $this->vars, $output );
		$res = exec('/home/phplib/swish/swish_v3.sh' . ' ' . $this->vars, $output );
		error_log("4");
		error_log("res: 	" . print_r($output, true));
		// result is separated with ';'. $output[0] means that it's the first line returned from shell function
		$output = preg_split('/;/', $output[0]);
		
		// response from swish, add to object
		$this->response = $output[0];
		
		if ($this->response == 201) { // i.e success
			// set status to 'in progress', i.e. waiting for payment to be completed
			$this->status = self::PAYMENT_STATUS_CREATED;
			
			// save payment confirmation url (for "manual" payment status update)
			$this->paymentConfirmationUrl = preg_replace("/\[|\]/", "", $output[1]) ;
			$this->external_reference = substr($this->paymentConfirmationUrl, (strrpos($this->paymentConfirmationUrl, "/"))+1);	
			$this->swishPaymentId = $this->external_reference;
			$this->savePaymentReferenceToDb();
			Log::addLog("Swish. Success response, object: " . print_r($this, true), Log::LEVEL_INFO);
			return true;
		} else {
			// we have an error, the error is an json encoded array
			$error = json_decode($output[1]);
			// save error code and error in plain text
			$this->status = $error[0]->errorCode; 
			$this->status = print_r($error, true);
			$this->status_text = $error[0]->errorMessage;
			$this->status_text = print_r($output, true);
			$this->errorCode = $error[0]->errorCode; 
			$this->errorMessage = $error[0]->errorMessage;
			return false;
		}			
	}
	/** Function for setting error codes for testing, e.g. setTestRequestError(Swish::ERROR_REQUEST_AC05) to simulate denied. 
	*   This error will then be returned when we receive calback from swish 
	*/
	function setTestRequestError($errorCode) {
		$this->message = $errorCode;
	}
	/** Function for "manually" checking swish payment status
		Returns one of: 
		PAYMENT_STATUS_PAID payment is done
		PAYMENT_STATUS_CREATED - we're still waiting
		PAYMENT_STATUS_DECLINED - customers payment was declined
		PAYMENT_STATUS_ERROR - some kind of error, check errorCode and errorMessage
		-1 if we got an http error
	*/
	function checkReturnURL() {
		$vars = $this->getVars(self::METHOD_PAYMENT_CONFIRMATION_CALLBACK);
		// get payment request from java shell script

		$res = exec($this->shell_path . ' ' . $vars, $output );
		
		// result is separated with ';'. $output[0] means that it's the first line returned from shell function
		$output = preg_split('/;/', $output[0]);
		
		// response from swish, add to object
		$this->response = $output[0];
		
		if ($this->response == 200) { // i.e success

			// decode response
			$postdata = json_decode($output[1]);
			// save info to 
			$postdata->fromcheckReturnURL='yes';
			Log::addLog("Output : " . print_r($postdata,true), Log::LEVEL_INFO);		
			$this->status = $postdata->status;
			$this->status_text = $postdata->errorMessage;
			
			$this->errorCode = $postdata->errorCode;
			$this->errorMessage = $postdata->errorMessage;			
			// if errorCode is empty but status is declined, set errorCode to standard declined error code
			// this shouldn't happen but, maybe an early bug... 
			if ($this->errorCode == '' && $this->status == self::PAYMENT_STATUS_DECLINED) {
				Log::addLog("Swish was missing errorCode but status is declined, manually set status to declined. For details see previous log post: ", Log::LEVEL_INFO);
				$this->errorCode = self::ERROR_CONFIRMATION_RF07;
				$postdata->errorCode = self::ERROR_CONFIRMATION_RF07;
			}			
			
			$this->swishPaymentId = $postdata->id;	
			if (!self::setStatus($postdata))
				; // TODO: ? 
			// if retry and status is paid we trigger import to ERP
			if ($this->retry && $this->status == Swish::PAYMENT_STATUS_PAID) {
				$this->triggerImport();
			}				
			return $this->status;					
		} else {
			//$this->errorCode = $this->response;
			Log::addLog("Swish. Error with check return url " .  print_r($res, true) . " : vars: " . $vars, Log::LEVEL_INFO);
			$this->errorCode = print_r($res, true) . " : vars: " . $vars;
			return -1;
		}			
	}

	/** 
	* Sets status when we receive request from swish to our callBackUrl. This is done in kundvagn/swish_return.php
	*/
	public static function setStatus($postdata) {
		// check if we have correct data by checking our payment reference we passed to swish
		if (!is_numeric($postdata->payeePaymentReference) || !(int)$postdata->payeePaymentReference > 0 ) {
			// log the error as severe, could be hacking attempt
			Log::addLog("Swish. Function setStatus, from checkUrl: " . $postdata->fromcheckReturnURL . ", postdata: " . print_r($postdata, true) . " \n --- all predefined variables --- \n" . print_r(get_defined_vars(), true), Log::LEVEL_CRIT);
			return false;
		}
		// error message in plain text if it exists
		$error = $postdata->errorMessage == 'null' ? '' : $postdata->errorMessage;
		$errorCode = $postdata->errorCode == 'null' ? '' : $postdata->errorCode;
		// status, hopefulley success, otherwise we save error code
		//$status = $postdata->status == Swish::PAYMENT_STATUS_PAID ? $postdata->status : $postdata->errorCode;
		$status = $postdata->status;
		$message = ''; // not used
		// add result to database, check is then done from checkStatus-function
        $update = " UPDATE cyberorder.payment_reference SET status = '" . $status . "', comment = '" . $message . "'"; 
		// , external_reference='" . $postdata->id . "' ";
		$update .= ", error_code = '" . $errorCode . "' ";
		$update .= ", error_message = '" . $error . "' ";
		
        $update .= " WHERE orderId =" . $postdata->payeePaymentReference;
		$update .= " AND external_reference = '". $postdata->id . "'" ;
        
		// Log sql for now, remove later when working fine
		Log::addLog("From swish,from checkUrl: " . $postdata->fromcheckReturnURL . ", update : " . $update . ", postdata: " . print_r($postdata, true), Log::LEVEL_INFO);
		
        if(!mysqli_query(Db::getConnection(true), $update)) {
            $this->errorMess .= " : Error while updating payment reference " . mysqli_error(Db::getConnection(true)) . " : " . $update . ", ";
			Log::addLog("Swish. Could not update status, query:  " . $this->errorMess . " , postdata: " . print_r($postdata, true) . " \n --- all predefined variables --- \n" . print_r(get_defined_vars(), true), Log::LEVEL_CRIT);
            return false;
        } else {
            return true;
        }					
	}
	// Used for retry
	public function triggerImport() {
		    	
    	$xmlMess = "<swishMsg>";
    	$xmlMess .= "\t<OrderID>" . $this->finalOrdernr . "</OrderID>\n";
    	$xmlMess .= "\t\t<Order_ID_ref>" . $this->external_reference . "</Order_ID_ref>\n";
		$xmlMess .= "\t\t<AuthCode>" . $this->external_reference . "</AuthCode>\n";
    	$xmlMess .= "\t\t<AuthAmount>" . $this->totalSum . "</AuthAmount>\n";
    	$xmlMess .= "\t\t<AuthTime>" . date("Y-m-d H:i:s") . "</AuthTime>\n";
    	$xmlMess .= "\t\t<ccName>" . "" . "</ccName>\n";
    	$xmlMess .= "\t\t<Currency>" . $this->currency . "</Currency>\n";
    	$xmlMess .= "\t</swishMsg>";
    	// $xmlMess is already UTF-8
    	
    	// include a library
    	//require_once ("Stomp.php");
    	
    	// make a connection
    	$con = new Stomp ( "tcp://cyber-erp.cyberphoto.se:61613" );
    	// connect
    	$con->connect ();
    	

    	if ($con->send ( "swishMsg", $xmlMess, array (
    			'persistent' => 'true'
    	) )) {
    		Log::addLog("Sent swish xml to active mq. Swish ref: " . $this->external_reference . " . XML: " . $xmlMess, Log::LEVEL_INFO);
    		return true;
    	} else {
    		Log::createTicketAndLog ( Log::RECIPIENT_IT, " Misslyckades med att skicka stomp (trigga import av swish payment) : All variables: " . print_r ( get_defined_vars (), true ), Log::LEVEL_WARN );
			return false;
    	}
	}
	/** 
	 * Check status of payment
	 *  Returns one of: 
		PAYMENT_STATUS_PAID payment is done
		PAYMENT_STATUS_CREATED - we're still waiting
		PAYMENT_STATUS_DECLINED - customers payment was declined
		PAYMENT_STATUS_ERROR - some kind of error, check errorCode and errorMessage
		-1 if sql error
	 * */
	
	public function checkStatus() {
		if ( (int)$this->payment_reference_id == 0 )
			return -1;		
		$select = 'SELECT * FROM cyberorder.payment_reference WHERE payment_reference_id=' . $this->payment_reference_id;
		
        $res = mysqli_query(Db::getConnection(true), $select);

        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_object($res);
			
			$this->status = $row->status;
			//$this->status_text = $row->comment;
			
			$this->errorCode = $row->error_code;
			$this->errorMessage = $row->error_message;		
			// if errorCode is empty but status is declined, set errorCode to standard declined error code
			// this shouldn't happen but, maybe an early bug... 
			if ($this->errorCode == '' && $this->status == self::PAYMENT_STATUS_DECLINED) {
				Log::addLog("Swish was missing errorCode but status is declined, manually set status to declined. For details see previous log post: ", Log::LEVEL_INFO);
				$this->errorCode = self::ERROR_CONFIRMATION_RF07;
				$postdata->errorCode = self::ERROR_CONFIRMATION_RF07;
			}
			// if retry and status is paid we trigger import to ERP
			if ($this->retry && $this->status == Swish::PAYMENT_STATUS_PAID) {
				$this->triggerImport();
			}
			return $this->status;

        } else {
            return -1;
        }		
		
	}	
    public function finalize($ordernr) {
        if (!is_numeric($ordernr) || $ordernr < 1 || !is_numeric($this->orderId))
            return false;
        
        $this->finalOrdernr = $ordernr;
        
        $update = " UPDATE cyberorder.payment_reference SET ordernr = " . $ordernr . " ";
        
        $update .= " WHERE orderId =" . $this->orderId;
		$update .= " AND status = 'PAID' ";
        /**
        echo $update .  "\n";
        print_r($this);
        exit;        
        */
        
        
        if(!mysqli_query(Db::getConnection(true), $update)) {
            $this->errorMess .= " : Error while updating payment reference " . mysqli_error(Db::getConnection(true)) . " : " . $update . ", ";
            return true;
        } else {
            return true;
        }
                
    }	
}

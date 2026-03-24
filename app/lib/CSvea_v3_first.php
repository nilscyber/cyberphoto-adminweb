<?php

include("connections.php");
require_once("CMobile.php");
require_once ('CPlaceOrder.php');

Class CSvea {

    /**
     * constructor..
     * @return CSvea
     */
    function __construct($test = false) {
        $this->test = $test;
        $this->conn_my2 = Db::getConnection(true);

        $this->conn_standard = Db::getConnection();

        if ($this->test)
            $this->wsdlLink = "https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL";
        else
            $this->wsdlLink = "https://webservices.sveaekonomi.se/webpay/sveawebpay.asmx?WSDL";
    }

    var $test = false;
    var $sveaUserName;
    var $sveaPassword;
    var $sveaAccountNo;
    var $wsdlLink;
    var $AddressSelector;
    var $BusinessType;
    var $sveaAddressObj;
    var $lowestMonthlyAnnuityFactor = 1000;
    var $SveaOrderId;
    var $SveaAccepted;
    var $SveaErrorMessage;
    var $SveaResultCode;
    var $SveaCampaignCode;
    var $sveaInvoiceAddress;
    var $sveaReservedAmount;
    var $sveaExpirationDate;
    var $sveaCustomerIdentity;
    var $sveaVerifiedAddress = true;

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
     * holds connection variable for finland
     * @var connection
     */
    var $conn_fi;
    var $conn_my2;

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

        if ($pay != "sveainvoice") {
            $this->payment_period = preg_replace("/[^0-9]/", "", $pay);
        }
        return $this->payment_period;
    }

    function create_rows() {
        global $kundvagn, $conn_my, $conn_master, $fi, $discountPercent, $discountAmount, $discounts, $bask,
        $moms, $moms1, $moms2, $moms3, $moms4, $goodsvalue, $fi, $sv, $pay, $goodsvalueMoms, $extra_frakt, $butiksfrakt, $old_foretag, $old_forsakring_new;

        $bask->getDiscountArticles($articles, $fi, $discountPercent, $discountAmount, $discounts);

        $invoiceRows = array();

        if (ereg("(grejor:)(.*)", $kundvagn, $matches)) {
            # Split the number of items and article id s into a list
            $orderlista = $matches[2];
            $argument = split("\|", $orderlista);
        }
        $n = count($argument);
        $goodscounter = 0;
        for ($i = 0; ($i < $n); $i+=2) {
            $arg = $argument[$i];        # Article id
            $count = $argument[$i + 1];    # Keeps track of the number of the same article

            if (eregi("^frakt", $arg))
                $fraktartnr = $arg;

            if ($bask->freeFreight && eregi("^frakt", $arg)) {
                $arg = "fraktfritt";
                $count = 1;
            }

            $goodscounter++;


            // Spara artnr för frakt för att kunna ange leveranssätt senare
            if ($arg != 'fraktbutik' && $arg != 'fraktfritt') {
                $invoiceRows = array_merge($invoiceRows, write_toOrderposter($arg, 0, $count, false, true));
            }

            //break;
        }
        if (count($articles) > 0) {
            //echo "<p>yes articles exist";
            foreach ($articles as $article) {
                //echo "<p>testade artikel: " . $article[0];
                $art = $bask->getIncludedArticles($article[0]);
                //echo "\n\n<p>Resultat, inluderad artikel: <p>";
                //print_r($art);
                //exit;
                if (is_array($art)) {
                    foreach ($art as $art2) {
                        //echo "<br>write to orderposter: " . $art2;
                        $invoiceRows = array_merge($invoiceRows, write_toOrderposter($art2, 0, $article[1], true, true));
                        //write_toOrderposter($art2, $newordernr, $article[1], true);
                    }
                }
            }
        }


        if ($goodscounter > 0) {

            $count = 1;
            if ($extra_frakt > 0 && $extra_frakt != 3 && !($butiksfrakt)) {

                if ($extra_frakt <> 1)
                    $extra_frakt = "frakt+" . $extra_frakt;
                else
                    $extra_frakt = "frakt+";

                $invoiceRows = array_merge($invoiceRows, write_toOrderposter($extra_frakt, $newordernr, 1, false, true));
            }
        }
        $moms = $moms1 + $moms2 + $moms3 + $moms4;
        if ($pay == "sveainvoice" && $old_foretag != -1)
            $invoiceRows = array_merge($invoiceRows, write_toOrderposter("invoicefee", $newordernr, 1, false, true));
			
		if ($old_forsakring_new == -1) {
            $invoiceRows = array_merge($invoiceRows, write_toOrderposter("friforsakring", $newordernr, 1, true, true));
		}

        $totalsumma = number_format($goodsvalueMoms, 0, "", "");
        $totalsumma2 = number_format($goodsvalue, 0, "", "");
        $öresutjämning = $totalsumma - ($goodsvalueMoms);
        $öresutjämning2 = $totalsumma2 - ($goodsvalue);
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
            echo "<br>moms: " . $moms;
            echo "<br>moms1: " . $moms1;
            echo "<br>moms2: " . $moms2;
            echo "<br>moms3: " . $moms3;
            echo "<br>moms4: " . $moms4;
            echo "<br>goodsvalue " . $goodsvalue;
            echo "<br>goodsvalueMoms " . $goodsvalueMoms;
            echo "<br>totalsumma " . $goodsvalueMoms;
            exit;
        }

        if ($öresutjämning2 == -0.5) {
            $öresutjämning2 = 0.5;
            $totalsumma2 += 1;
        }

        if ($öresutjämning == -0.5) {
            $öresutjämning = 0.5;
            $totalsumma += 1;
        }


        if ($öresutjämning != 0) {
            $invoiceRows = array_merge($invoiceRows, write_toOrderposter("avrund", $newordernr, 1, false, true));
            $invoiceRows[sizeof($invoiceRows) - 1]['PricePerUnit'] = $öresutjämning;
        }
        //print_r($invoiceRows); exit;
        return $invoiceRows;
        /**
          $clientInvoiceRows = array('OrderRow' => Array(
          "Description" => 'KitchenAid Stand Mixer (Red)',
          "PricePerUnit" => 500.00,
          "NumberOfUnits" => 1,
          "Unit" => "st",
          "VatPercent" => 25.00,
          "DiscountPercent" => 0
          ),
          ); */
    }

    function upatePaymentPlans($no_update = false) {
        global $fi, $no;
        
        Locs::setOldWay();
                
        $this->set_svea_login();
        $request = Array(
            "Auth" => Array(
                "Username" => $this->sveaUserName,
                "Password" => $this->sveaPassword,
                "ClientNumber" => $this->sveaAccountNo
            )
        );

        $data['request'] = $request;

        //print_r($data); exit;
        //Call Soap
        $client = new SoapClient($this->wsdlLink);

        //Make soap call to below method using above data
        $r = $client->GetPaymentPlanParamsEu($data);
        //print_r($r);
        if ($r->GetPaymentPlanParamsEuResult->Accepted != 1) {
            return $r;
        }

        if ($no_update)
            return $r;

        foreach ($r->GetPaymentPlanParamsEuResult->CampaignCodes->CampaignCodeInfo as $key => $plan) {
            // hardcode removal of 'personalköp' / 'employee-special' 
            if ($plan->CampaignCode == 310000)
                continue;
            $i = "REPLACE INTO SveaPaymPlans (CampaignCode, Description, PaymentPlanType, ContractLengthInMonths, MonthlyAnnuityFactor, InitialFee, NotificationFee, 
                InterestRatePercent, NumberOfInterestFreeMonths, NumberOfPaymentFreeMonths, FromAmount, ToAmount, isTestPlan, country) values (";
            $i .= $plan->CampaignCode . ",";
            $i .= "'" . $plan->Description . "',";
            $i .= "'" . $plan->PaymentPlanType . "',";
            $i .= $plan->ContractLengthInMonths . ",";
            $i .= $plan->MonthlyAnnuityFactor . ",";
            $i .= $plan->InitialFee . ",";
            $i .= $plan->NotificationFee . ",";
            $i .= $plan->InterestRatePercent . ",";
            $i .= $plan->NumberOfInterestFreeMonths . ",";
            $i .= $plan->NumberOfPaymentFreeMonths . ",";
            $i .= $plan->FromAmount . ",";
            $i .= $plan->ToAmount . ", ";
            $i .= $this->test ? -1 : 0;
            $i .= ",";
            $i .= $fi ? "'FI'" : ($no ? "'NO'" : "'SE'");
            $i .= ")";

            $i = $i;
            //echo $i;
            @mysqli_query($this->conn_my2, $i) . "\n";
            //TODO: if above fails, what then? 
        }
        return $r;
    }

    function set_svea_login($reload = false) {
        global $fi, $no, $old_foretag, $pay;
        Locs::setOldWay();
        if (!$reload && $this->sveaAccountNo != "")
            return;
        
        /**
          if ($this->test ) {
          if ($pay == "invoiceme" || $pay == "sveainvoice")
          $this->sveaAccountNo = '79021';
          else
          $this->sveaAccountNo = '59999';

          $this->sveaPassword = 'sverigetest';
          $this->sveaUserName = 'sverigetest';
          return;
          }
         */
        if ($fi) {
            if ($pay == "sveainvoice" && $old_foretag == -1) {
                $this->sveaAccountNo = '26018';
                // faktura företag
            } else if ($pay == "sveainvoice") {
                $this->sveaAccountNo = '26314';
                // faktura privatperson
            } else {
                $this->sveaAccountNo = '27314';
                // avbetalning privatperson
            }
        } else  if ($no) {
            if ($pay == "sveainvoice" && $old_foretag == -1) {
                $this->sveaAccountNo = '75794'; // TODO: vilket nummer? 
                // faktura företag
            } else if ($pay == "sveainvoice") {
                $this->sveaAccountNo = '76222'; // TODO: vilket nummer? 
                // faktura privatperson
            } else {
                $this->sveaAccountNo = '59671'; // TODO: vilket nummer? 
                // avbetalning privatperson
            }
        
        } else {
            if ($pay == "sveainvoice" && $old_foretag == -1) {
                $this->sveaAccountNo = '75794';
                // faktura företag
            } else if ($pay == "sveainvoice") {
                $this->sveaAccountNo = '76222';
                // faktura privatperson
            } else {
                $this->sveaAccountNo = '59671';
                // avbetalning privatperson
            }
        }
        $s = "SELECT * FROM svea_accounts WHERE accountno = " . $this->sveaAccountNo;
        $res = mysqli_query($s);
        $row = mysqli_fetch_assoc($res);

        $this->sveaAccountNo = $row['accountno'];
        $this->sveaPassword = $row['password'];
        $this->sveaUserName = $row['username'];
        //print_r($this);
    }

    function get_payment_plans() {
        global $goodsvalue;

        $this->cust_pno = $_SESSION['old_personnr'];
        ;

        if (!isset($this->cust_pno))
            return -1;


        $this->set_svea_login();
        $clientInvoiceRows = $this->create_rows();

        $request = Array(
            "Auth" => Array(
                "Username" => $this->sveaUserName,
                "Password" => $this->sveaPassword,
                "ClientNumber" => $this->sveaAccountNo
            ),
            "Amount" => $goodsvalue,
            "InvoiceRows" => $clientInvoiceRows,
        );

        $data['request'] = $request;

        //print_r($data); exit;
        $svea_server = $this->wsdlLink;

        //Print the request
        //Call Soap
        $client = new SoapClient($svea_server);


        //Make soap call to below method using above data
        $r = $client->GetPaymentPlanOptions($data);



        //Print the response
        // print_r($r);
    }

    /**
     * uses function reserve_amount with CyberPhoto's values
     * @return void
     */
    function reserve_amount() {
        global $goodsvalueMoms, $pay, $KRED_SEND_BY_EMAIL, $KRED_FI_PNO, $KRED_ISO639_SV, $KRED_ISO639_FI, $KRED_SE_PNO, $KRED_ISO3166_SE, $KRED_ISO3166_FI, $KRED_SEND_BY_MAIL;
        global $KRED_SEK, $KRED_EUR;
        global $conn_standard;
        global $old_email, $eid, $secret;
        global $old_salary;
        global $kundnrsave;
        global $kundvagn;
        global $old_personnr;
        global $old_foretag;
        if ($old_salary == "")
            $old_salary = 0;

        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			if ($pay == "sveainvoice") {
				$this->pclass = -2;
			} else {
				$this->pclass = preg_replace("/[^0-9]/", "", $pay);
			}
		} else {
			if ($pay == "sveainvoice") {
				$this->pclass = -2;
			} else {
				$this->pclass = preg_replace("/[^0-9]/", "", $pay);
			}
			// $this->pclass = $this->get_pclass_extra($this->get_payment_period($pay));
		}
        $this->cust_pno = $_SESSION['old_personnr'];

        if (!isset($this->cust_pno))
            return -1;
        $clientInvoiceRows = $this->create_rows();

        $this->set_svea_login(true);
        if ($this->orderId == 0)
            $this->getSetOrderId();

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
        $cntry = Locs::getCountry();
        $lang = Locs::getLang();
        $request = Array(
            "Auth" => Array(
                "Username" => $this->sveaUserName,
                "Password" => $this->sveaPassword,
                "ClientNumber" => $this->sveaAccountNo
            ),
            "CreateOrderInformation" => Array(
                "ClientOrderNumber" => $this->orderId,
                "OrderRows" => $clientInvoiceRows,
                "CustomerIdentity" => array(
                    "NationalIdNumber" => $this->cust_pno,
                    "Email" => $_SESSION['old_email'],
                    "PhoneNumber" => $phone,
                    "IpAddress" => $_SERVER['REMOTE_ADDR'],
                    "FullName" => $this->sveaInvoiceAddress->FirstName . " " . $this->sveaInvoiceAddress->LastName,
                    //"Street" => $this->sveaInvoiceAddress->AddressLine2,
                    //"CoAddress" => $this->sveaInvoiceAddress->AddressLine1,
                    //"ZipCode" => $this->sveaInvoiceAddress->Postcode,
                    //"Locality" => $this->sveaInvoiceAddress->Postarea ,
                    "CountryCode" => $cntry,
                    "CustomerType" => $old_foretag == -1 ? 'Company' : 'Individual',
                    "IndividualIdentity" => null
                ),
                "OrderDate" => date('c'),
                "AddressSelector" => $old_foretag == -1 ? $_SESSION['old_invoice_addresselector'] : null,
                "OrderType" => $pay == "sveainvoice" ? 'Invoice' : 'PaymentPlan',
                "PreApprovedCustomerId" => 0
            )
        );
        if ($pay != "sveainvoice") {
            $request['CreateOrderInformation']['CreatePaymentPlanDetails']['CampaignCode'] = (int) $this->pclass;
            $request['CreateOrderInformation']['CreatePaymentPlanDetails']['SendAutomaticGiroPaymentForm'] = true;

            //SendAutomaticGiroPaymentForm
        }
        //Put all the data in request tag
        $data['request'] = $request;
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
            echo $this->wsdlLink . "\n";
            print_r($data); //exit;
        }

        //Print the request
        //Call Soap
        $client = new SoapClient($this->wsdlLink);


        //Make soap call to below method using above data
        $r = $client->CreateOrderEu($data);
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
            print_r($r);
            
            print_r($this->sveaCustomerIdentity);
            echo "\n end ----\n";
            exit;
        }
        $this->sveaCustomerIdentity = $r->CreateOrderEuResult->CreateOrderResult->CustomerIdentity;
        $this->SveaAccepted = $r->CreateOrderEuResult->Accepted;
        $this->SveaResultCode = $r->CreateOrderEuResult->ResultCode;
        $this->SveaErrorMessage = $r->CreateOrderEuResult->ErrorMessage;
        $this->CampaignCode = $this->pclass;
        $this->SveaOrderId = $r->CreateOrderEuResult->CreateOrderResult->SveaOrderId;
        $this->sveaReservedAmount = $r->CreateOrderEuResult->CreateOrderResult->Amount;
        $this->sveaExpirationDate = substr($r->CreateOrderEuResult->CreateOrderResult->ExpirationDate, 0, 10);
        
        if ($r->CreateOrderEuResult->Accepted == 1 && Locs::getCountry() == "FI") {
            // if Finland try verify address
            $this->verifyAddress();
        }
        
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
            //exit;
        }
        // for backward compatibility 
        if ($r->CreateOrderEuResult->Accepted == 1) {
            $this->kreditor_status = 0;
        } else {
            $this->kreditor_status = 1;
        }
        $this->kreditor_result = $this->SveaOrderId;

        $this->conn_my2 = Db::getConnection(true);

        $logIP = $_SERVER['REMOTE_ADDR'];
        $kundvagn_local = preg_replace("/grejor:/", "", $kundvagn);
        
        if ($this->kreditor_status != 0) {
            //$this->kreditor_result_text = strerror($result);
            $SveaErrorResult = preg_replace("/\'/", "", $this->SveaErrorMessage);
            $insert = "INSERT INTO cyberphoto.logWeb (logDate, logPage, logComment, logBasket, logDeny, logReason, logIP, country, lang) values (now(), 'svea', 'Personnr:  " . $this->cust_pno .
                    ". Kundnummer: $kundnrsave. Ordersumma: $goodsvalueMoms. pclass: $this->pclass', '$kundvagn_local', '1', '$SveaErrorResult', '$logIP', '$cntry', '$lang')";

            // $extra = "From: " . "nils@cyberphoto.se";
            // mail("nils@cyberphoto.se", "Nekad klarna ", "insert: " . $insert, $extra);            
            if ($_SERVER['REMOTE_ADDR'] != "192.168.1.89") {
                // $this->sendMessToOTRS($kundnrsave,$this->cust_pno,$goodsvalueMoms,$result,$this->pclass,$kundvagn_local);
            }
        } else {
            $this->kreditor_result_text = "";
            $insert = "INSERT INTO cyberphoto.logWeb (logDate, logPage, logComment, logBasket, logIP, country, lang) values (now(), 'svea', 'resultat: " . $this->SveaOrderId . ". Personnr:  " . $this->cust_pno . ". kundnummer: $kundnrsave. ordersumma: $goodsvalueMoms. pclass: $this->pclass', '$kundvagn_local', '$logIP', $cntry', '$lang')";
			if ($this->pclass == 213061) { // sätt pclass för att maila ut till berörd
				$this->sendMessIfPclass($kundnrsave,$this->cust_pno,$goodsvalueMoms,$SveaErrorResult,$this->pclass,$kundvagn_local);
			}
        }
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {

            print_r($r);

            //echo "före vår\n";
            //print_r($this);
            //echo "efter vår\n";                        
            //exit;
        }
		if ($_SERVER['REMOTE_ADDR'] != "192.168.1.89") {
			// $this->sendMessToOTRS($kundnrsave,$this->cust_pno,$goodsvalueMoms,$SveaErrorResult,$this->pclass,$kundvagn_local);
		}
        mysqli_query($this->conn_my2, $insert);
    }

    function sendMessToOTRS($kundnrsave, $personnr, $goodsvalueMoms, $result, $pclass, $kundvagn) {

        $orderdatum = date("Y-m-d H:i:s", time());

        $addcreatedby = "noreply";

        $recipient .= " sjabo@cyberphoto.nu";
        // $recipient .= " deniedcredit@cyberphoto.se";

        $subj = $orderdatum . " Svea kreditgivning";

        $extra = "From: " . $addcreatedby;

        $text1 .= "Kund-nr: " . $kundnrsave . "\n\n";
        $text1 .= "Person-nr: " . $personnr . "\n\n";
        $text1 .= "Ordersumma: " . $goodsvalueMoms . "\n\n";
        $text1 .= "Orsak: " . $result . "\n\n";
        $text1 .= "Betalsätt: " . $pclass . "\n\n";
        $text1 .= "Kundvagn: " . $kundvagn . "\n\n";

        mail($recipient, $subj, $text1, $extra);
    }

    function sendMessIfPclass($kundnrsave, $personnr, $goodsvalueMoms, $result, $pclass, $kundvagn) {

        $orderdatum = date("Y-m-d H:i:s", time());

        $addcreatedby = "noreply";

        if ($pclass == 213060) {
			$recipient .= " sjabo@cyberphoto.nu";
		} else {
			$recipient .= " sjabo@cyberphoto.nu";
		}
        // $recipient .= " deniedcredit@cyberphoto.se";

        $subj = $orderdatum . " Svea kreditgivning";

        $extra = "From: " . $addcreatedby;

        $text1 .= "Kund-nr: " . $kundnrsave . "\n\n";
        $text1 .= "Person-nr: " . $personnr . "\n\n";
        $text1 .= "Ordersumma: " . $goodsvalueMoms . "\n\n";
        // $text1 .= "Orsak: " . $result . "\n\n";
        if ($pclass == 213060) {
			$text1 .= "Betalsätt: Köp nu betala om tre månader\n\n";
		} else {
			$text1 .= "Betalsätt: " . $pclass . "\n\n";
		}
        $text1 .= "Kundvagn: " . $kundvagn . "\n\n";

        mail($recipient, $subj, $text1, $extra);
    }
	
    /**
     * sets pclass, requires paymentperiod to be set before
     * on error returns -1
     *
     * @return array
     */
    function get_pclass($pay) {
        global $conn_standard;
        $country = Locs::getCountry();

        /*
          if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
          echo $pay;
          }
         */

        $this->payment_period = $pay;
        // echo "här: " . $pay;
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
        if ($pay == "sveainvoice") {
            $pclass = -1;
        } elseif ($pay == "klarnakonto") {
            if ($country == "FI") {
                $pclass = 518;
            } else {
                $pclass = 497;
            }
        } else {
            if ($country == "FI") {
                switch ($this->payment_period) {
                    case(15): // denna gäller vår 15-månaders kampanj i sep 2010
                        $pclass = 1584;
                        break;
                    // case(15): // denna gäller vår 15-månaders kampanj i maj 2010
                    // $pclass = 1228;
                    // break;
                    //case (3):
                    //$pclass = 105;
                    //break;
                    // case (98): // denna gäller fakturakampanj, midsommar
                    // $pclass = 1162;
                    // break;
                    case (98):
                        // $pclass = 2114; // denna gäller fakturakampanj, julen 2010
                        $pclass = 2836; // denna gäller faktura kampanj maj 2011
                        break;
                    case (88):
                        $pclass = 518;
                        break;
                    case (91):
                        $pclass = 4496; // detta gällerjulkampanj 2012 FI
                        break;
                    case (99):
                        $pclass = 262;
                        break;
                    case (6):
                        $pclass = 259;
                        break;
                    case(12):
                        $pclass = 260;
                        break;
                    case(24):// TODO: skall den verkligen vara med? 
                        $pclass = 258;
                        break;
                    case(36):
                        $pclass = 261;
                        break;
                    default:
                        $pclass = -1;
                        break;
                }
            } else {
                switch ($this->payment_period) {
                    case(15): // denna gäller vår 15-månaders kampanj i sep 2010
                        $pclass = 1583;
                        break;
                    // case(15): // denna gäller vår 15-månaders kampanj i maj 2010
                    // $pclass = 1226;
                    // break;
                    // case (98): // denna gäller fakturakampanj, midsommar
                    // $pclass = 1160;
                    // break;
                    case (88):
                        $pclass = 497;
                        break;
                    case (98):
                        // $pclass = 2112; // denna gäller fakturakampanj, julen 2010
                        $pclass = 2834; // denna gäller fakturakampanj maj 2011
                        break;
                    case (90):
                        $pclass = 4587; // denna gäller canon kampanj 2012 (julen)
                        break;
                    case (91):
                        $pclass = 4398; // detta gällerjulkampanj 2012
                        break;
                    case (3):
                        $pclass = 105;
                        break;
                    case (6):
                        $pclass = 106;
                        break;
                    case (98):
                        $pclass = 324;
                        break;
                    case (99):
                        $pclass = 264;
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
        }

        $this->pclass = $pclass;

        return $pclass;
    }

    /**
     * sets and gets orderId in class
     * @return long
     */
    function getSetOrderId() {
        global $conn_master;

        $str = "SELECT max(orderId) as maxId from cyberorder.orderIds";
        $res = mysqli_query($conn_master, $str);

        if (mysqli_num_rows($res) > 0) {

            $row = mysqli_fetch_object($res);
            $orderId = $row->maxId + 1;
            $ins = "INSERT INTO cyberorder.orderIds (orderId) values (" . $orderId . ")";
            //echo $ins;
            mysqli_query($conn_master, $ins);
            $this->orderId = $orderId;
            //echo $this->orderId;
            //exit;
            return $orderId;
        } else {
            return 0;
        }
    }

    function get_address($pno) {
        global $eid, $secret;

        $this->set_svea_login();

        $request = Array(
            "Auth" => Array(
                "Username" => $this->sveaUserName,
                "Password" => $this->sveaPassword,
                "ClientNumber" => $this->sveaAccountNo
            ),
            "IsCompany" => $_SESSION['old_foretag'] == -1 ? 1 : 0,
            "CountryCode" => 'SE',
            "SecurityNumber" => trim($pno)
        );
        
        $data['request'] = $request;

        //print_r($data); exit;
        //Call Soap
        $client = new SoapClient($this->wsdlLink);

        //Make soap call to below method using above data
        $svea_req = $client->GetAddresses($data);
        //print_r($svea_req);

        if ($svea_req->GetAddressesResult->Accepted == 1) {
            $a = $svea_req->GetAddressesResult->Addresses->CustomerAddress;
            if (count($a) == 1) {
                $a = array($a);
            }
			// sortera arrayen efter addressline1 först om den finns, annars adressline2
			function cmp($as, $bs)
			{
				$c1 = trim($as->AddressLine1);
				if ($c1 == "") 
					$c1 = $as->AddressLine2;
			    
				$c2 = trim($bs->AddressLine1);
				if (c2 == "") 
					$c2 = $bs->AddressLine2;
				
				return strcmp($c1, $c2);
			}
			usort($a, "cmp");
			
            // spara adresserna i detta svea-objekt för senare användning. 
            $this->sveaInvoiceAddress = $a;
            
            $a = $a[0]; // quick-fix så jag slapp ändra nedanstående rader
            
            $r[0][0] = $a->FirstName;
            $r[0][1] = $a->LastName;
            $r[0][2] = $a->AddressLine2; // standard address line
            if ($a->AddressLine1 != "") // c/o address
                $r[0][2] = $r[0][2] . " / " . $a->AddressLine1;
            $r[0][3] = $a->Postcode;
            $r[0][4] = $a->Postarea;
            $r[0][5] = $a->AddressLine1;
            $r[0][6] = $a->AddressLine2;
            /** 		
              $r[0][0] = ($a->FirstName);
              $r[0][1] = ($a->LastName);
              $r[0][2] = ($a->AddressLine2);
              if ($a->AddressLine1 != "")
              $r[0][2] = $r[0][2] . " / " . $a->AddressLine1;
              $r[0][3] = ($a->Postcode);
              $r[0][4] = ($a->Postarea);
              $r[0][5] = ($a->AddressLine1);
              $r[0][6] = ($a->AddressLine2);

              // */
            // land ?
            //print_r($r);


            $this->cust_addresses = $r;
            // print_r($this->cust_addresses);

            // as default first address in the address array is selected as the address to use
            $this->AddressSelector = $a->AddressSelector;
            
            // business type is set from first address in array. Should be the same in all            
            
            $this->BusinessType = $a->BusinessType;
            unset($this->err_message);
            unset($this->incorrect_pno);
            $this->cust_pno = $pno;
        } else {
            $this->incorrect_pno = true;
            unset($this->sveaAddressObj);
            unset($this->cust_pno);
            unset($this->cust_addresses);
            $this->err_message = "<p><font face=\"Verdana\" size=\"2\" color=\"#85000D\"><b>Personnumret är felaktigt, vänligen pröva igen</b></font></p>";
        }
        // $this->print_addresses_new();
    }
	
	function list_business_locations($new_invoice_address) {
		
		$ret = "";

        if (!isset($this->cust_addresses))
            return;
			
			$ret = "&nbsp;<span class=\"explfieldbold\">" . $this->sveaInvoiceAddress[0]->LegalName . "</span><br><br>\n";
			
			if ($new_invoice_address == "" || $new_invoice_address == "noinvoice") {
				$ret .= "\t\t<select class=\"selectaddresscolor\" name=\"new_invoice_address\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
				$ret .= "\t\t<option value=\"noinvoice\" selected>Välj fakturaadress</option>\n";
			} else {
				$ret .= "\t\t<select class=\"selectaddressnocolor\" name=\"new_invoice_address\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
				$ret .= "\t\t<option value=\"noinvoice\">Välj fakturaadress</option>\n";
			}
			
			for($i = 0; $i < count($this->sveaInvoiceAddress); ++$i) {
				
				$ret .= "\t\t<option value=\"" . $i . "\"";
				if ($new_invoice_address == $i && ($new_invoice_address != "" && $new_invoice_address != "noinvoice")) {
					$_SESSION['old_invoice_addresselector'] = $this->sveaInvoiceAddress[$i]->AddressSelector;
					$ret .= " selected";
				}
				$ret .= ">";
				if ($this->sveaInvoiceAddress[$i]->AddressLine1 != "") {
					$ret .= "" . $this->sveaInvoiceAddress[$i]->AddressLine1 . " / " . $this->sveaInvoiceAddress[$i]->AddressLine2 . ", " . $this->sveaInvoiceAddress[$i]->Postcode . ", " . $this->sveaInvoiceAddress[$i]->Postarea . "</option>\n";
				} else {
					$ret .= "" . $this->sveaInvoiceAddress[$i]->AddressLine2 . ", " . $this->sveaInvoiceAddress[$i]->Postcode . ", " . $this->sveaInvoiceAddress[$i]->Postarea . "</option>\n";
				}
				
			}

			$ret .= "\t\t</select>\n";
			
			if ($new_invoice_address != "" && $new_invoice_address != "noinvoice") {
			
				$ret .= "<hr style=\"border: none; background-color: #999999; color: #999999; height: 1px; width: 330px; float: left;\">";
			
				$ret .= "<input class='inputaddressgrey' type='text' name='new_namn' size='28' value='" . $this->sveaInvoiceAddress[0]->LegalName . "' onFocus='this.blur()'><br>\n";
				if ($this->sveaInvoiceAddress[$new_invoice_address]->AddressLine1 != "") {
					$ret .= "<input class='inputaddressgrey' type='text' name='new_co' size='28' value='" . $this->sveaInvoiceAddress[$new_invoice_address]->AddressLine1 . "' onFocus='this.blur()'><br>\n";
					$ret .= "<input class='inputaddressgrey' type='text' name='new_adress' size='28' value='" . $this->sveaInvoiceAddress[$new_invoice_address]->AddressLine2 . "' onFocus='this.blur()'><br>\n";
				} else {
					$ret .= "<input class='inputaddressgrey' type='text' name='new_co' size='28' value='" . $this->sveaInvoiceAddress[$new_invoice_address]->AddressLine2 . "' onFocus='this.blur()'><br>\n";
				}
				$ret .= "<input class='inputaddressgrey' type='text' name='new_postnr' size='4' value='" . $this->sveaInvoiceAddress[$new_invoice_address]->Postcode . "' onFocus='this.blur()'>\n";
				$ret .= "<input class='inputaddressgrey' type='text' name='new_postadr' size='17' value='" . $this->sveaInvoiceAddress[$new_invoice_address]->Postarea . "' onFocus='this.blur()'>\n";
				
			
			}
			
			echo $ret;
	
	}

	function list_business_delivery_locations($new_delivery_address) {
		
		$ret = "";

        if (!isset($this->cust_addresses))
            return;
			
			if ($new_delivery_address == "" || $new_delivery_address == "nodelivery") {
				$ret .= "\t\t<select class=\"selectaddresscolor\" name=\"new_delivery_address\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
				$ret .= "\t\t<option value=\"nodelivery\" selected>Välj leveransadress</option>\n";
			} else {
				$ret .= "\t\t<select class=\"selectaddressnocolor\" name=\"new_delivery_address\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
				$ret .= "\t\t<option value=\"nodelivery\">Välj leveransadress</option>\n";
			}
			
			for($i = 0; $i < count($this->sveaInvoiceAddress); ++$i) {
				
				$ret .= "\t\t<option value=\"" . $i . "\"";
				if ($new_delivery_address == $i && ($new_delivery_address != "" && $new_delivery_address != "nodelivery")) {
					$ret .= " selected";
				}
				$ret .= ">";
				if ($this->sveaInvoiceAddress[$i]->AddressLine1 != "") {
					$ret .= "" . $this->sveaInvoiceAddress[$i]->AddressLine1 . " / " . $this->sveaInvoiceAddress[$i]->AddressLine2 . ", " . $this->sveaInvoiceAddress[$i]->Postcode . ", " . $this->sveaInvoiceAddress[$i]->Postarea . "</option>\n";
				} else {
					$ret .= "" . $this->sveaInvoiceAddress[$i]->AddressLine2 . ", " . $this->sveaInvoiceAddress[$i]->Postcode . ", " . $this->sveaInvoiceAddress[$i]->Postarea . "</option>\n";
				}
				
			}

			$ret .= "\t\t</select>\n";
			
			if ($new_delivery_address != "" && $new_delivery_address != "nodelivery") {
			
				$ret .= "<hr style=\"border: none; background-color: #999999; color: #999999; height: 1px; width: 330px; float: left;\">";
			
				$ret .= "<input class='inputaddressgrey' type='text' name='new_lnamn' size='28' value='" . $this->sveaInvoiceAddress[0]->LegalName . "' onFocus='this.blur()'><br>\n";
				if ($this->sveaInvoiceAddress[$new_delivery_address]->AddressLine1 != "") {
					$ret .= "<input class='inputaddressgrey' type='text' name='new_lco' size='28' value='" . $this->sveaInvoiceAddress[$new_delivery_address]->AddressLine1 . "' onFocus='this.blur()'><br>\n";
					$ret .= "<input class='inputaddressgrey' type='text' name='new_ladress' size='28' value='" . $this->sveaInvoiceAddress[$new_delivery_address]->AddressLine2 . "' onFocus='this.blur()'><br>\n";
				} else {
					$ret .= "<input class='inputaddressgrey' type='text' name='new_lco' size='28' value='" . $this->sveaInvoiceAddress[$new_delivery_address]->AddressLine2 . "' onFocus='this.blur()'><br>\n";
				}
				$ret .= "<input class='inputaddressgrey' type='text' name='new_lpostnr' size='4' value='" . $this->sveaInvoiceAddress[$new_delivery_address]->Postcode . "' onFocus='this.blur()'>\n";
				$ret .= "<input class='inputaddressgrey' type='text' name='new_lpostadr' size='17' value='" . $this->sveaInvoiceAddress[$new_delivery_address]->Postarea . "' onFocus='this.blur()'>\n";
			
			}
			
			echo $ret;
	
	}
	
    function print_addresses() {
        $ret = "";

        if (!isset($this->cust_addresses))
            return;
        //$this->cust_addresses[0][0] . " " . $this->cust_addresses[0][1]
        $ret = "<br><input type='text' name='namn' size='24' value='" . $this->cust_addresses[0][0] . " " . $this->cust_addresses[0][1] . "' onFocus='this.blur()' style='font-family: Verdana; font-size: 8pt; background-color: #EBEBEB'><br>";
        $ret .= "<input type='text' name='adress' size='24' value='" . $this->cust_addresses[0][2] . "' onFocus='this.blur()' style='font-family: Verdana; font-size: 8pt; background-color: #EBEBEB'><br>";
        $ret .= "<input type='text' name='postnummer' size='5' value='" . $this->cust_addresses[0][3] . "' onFocus='this.blur()' style='font-family: Verdana; font-size: 8pt; background-color: #EBEBEB'> ";
        $ret .= "<input type='text' name='ort' size='16' value='" . $this->cust_addresses[0][4] . "' onFocus='this.blur()' style='font-family: Verdana; font-size: 8pt; background-color: #EBEBEB'>";

        echo $ret;
        //return $ret;
    }

    function print_addresses_new() {
        $ret = "";

        if (!isset($this->cust_addresses))
            return;
        //$this->cust_addresses[0][0] . " " . $this->cust_addresses[0][1]
        $ret = "<input class='inputaddressgrey' type='text' name='new_namn' size='28' value='" . $this->cust_addresses[0][0] . " " . $this->cust_addresses[0][1] . "' onFocus='this.blur()'><br>";
        $ret .= "<input class='inputaddressgrey' type='text' name='new_co' size='28' value='" . $this->cust_addresses[0][2] . "' onFocus='this.blur()'><br>";
        $ret .= "<input class='inputaddressgrey' type='text' name='new_postnr' size='5' value='" . $this->cust_addresses[0][3] . "' onFocus='this.blur()'>";
        $ret .= "<input class='inputaddressgrey' type='text' name='new_postadr' size='16' value='" . $this->cust_addresses[0][4] . "' onFocus='this.blur()'>";

        echo $ret;
        //return $ret;
    }

    function print_addresses_mobilesite() {
        $ret = "";

        if (!isset($this->cust_addresses))
            return;
        //$this->cust_addresses[0][0] . " " . $this->cust_addresses[0][1]
        $ret = "<input class='inputaddressgrey' type='text' name='new_namn' size='25' value='" . $this->cust_addresses[0][0] . " " . $this->cust_addresses[0][1] . "' onFocus='this.blur()'><br>";
        $ret .= "<input class='inputaddressgrey' type='text' name='new_co' size='25' value='" . $this->cust_addresses[0][2] . "' onFocus='this.blur()'><br>";
        $ret .= "<input class='inputaddressgrey' type='text' name='new_postnr' size='5' value='" . $this->cust_addresses[0][3] . "' onFocus='this.blur()'>";
        $ret .= "<input class='inputaddressgrey' type='text' name='new_postadr' size='14' value='" . $this->cust_addresses[0][4] . "' onFocus='this.blur()'>";

        echo $ret;
        // echo $ret;
        //return $ret;
    }

    /**
     * transfers address from kreditor to our session address variables
     * @return void
     */
    function transfer_address() { // not used	
        global $fi, $sv;
        if (!isset($this->cust_addresses))
            return;

        $_SESSION['old_firstName'] = $this->sveaAddressObj->FirstName;
        $_SESSION['old_lastName'] = $this->sveaAddressObj->LastName;
        $_SESSION['old_namn'] = $this->sveaAddressObj->FirstName . " " . $a->LastName;
        $_SESSION['old_co'] = $this->sveaAddressObj->AddressLine1;

        $_SESSION['old_adress'] = $this->sveaAddressObj->AddressLine2;
        $_SESSION['old_postnr'] = $this->sveaAddressObj->Postcode;
        $_SESSION['old_postadr'] = $this->sveaAddressObj->Postarea;

        $_SESSION['old_lnamn'] = $this->sveaAddressObj->FirstName . " " . $this->sveaAddressObj->LastName;
        $_SESSION['old_lco'] = $this->sveaAddressObj->AddressLine1;
        $_SESSION['old_ladress'] = $this->sveaAddressObj->AddressLine2;
        $_SESSION['old_lpostnr'] = $this->sveaAddressObj->Postcode;
        $_SESSION['old_lpostadr'] = $this->sveaAddressObj->Postarea;

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
    function periodic_cost($payment_period, $goodsValueMoms, $currency = "SEK") {


        $conn_standard = Db::getConnection();

        $pclass = $this->get_pclass_extra($payment_period);
        // echo "här: " . $pclass;

        $s = "SELECT * FROM SveaPaymPlans WHERE CampaignCode = " . $pclass;
        // echo $s;
        // exit;

        $res = mysqli_query($conn_standard, $s);
        $row = mysqli_fetch_object($res);
        return ( round($row->MonthlyAnnuityFactor * $goodsValueMoms, 0) );
    }

    function periodic_costNew($payment_code, $goodsValueMoms) {        

        $payment_code = preg_replace("/[^0-9]/", "", $payment_code);

        $conn_standard = Db::getConnection();

        $s = "SELECT * FROM SveaPaymPlans WHERE CampaignCode = " . $payment_code;

        $res = mysqli_query($conn_standard, $s);
        $row = mysqli_fetch_object($res);
        return ( round($row->MonthlyAnnuityFactor * $goodsValueMoms, 0) );
    }

    /**
     * returns and sets monthly cost for periodic payment
     * on error returns -1
     *
     * @return double
     */
    function get_fees($payment_period, $goodsValueMoms = 0, $currency = "SEK") {

        $conn_standard = Db::getConnection();

        $pclass = $this->get_pclass_extra($payment_period);
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo "här: " . $pclass;
		}

        $s = "SELECT * FROM SveaPaymPlans WHERE CampaignCode = " . $pclass;
        // echo $s;
        // exit;

        $res = mysqli_query($conn_standard, $s);
        $row = mysqli_fetch_object($res);
        return ( $row );
    }

    function get_feesNew($payment_code) {
		
        $payment_code = preg_replace("/[^0-9]/", "", $payment_code);
		
        $conn_standard = Db::getConnection();

        $s = "SELECT * FROM SveaPaymPlans WHERE CampaignCode = " . $payment_code;

        $res = mysqli_query($conn_standard, $s);
        $row = mysqli_fetch_object($res);
        return ( $row );
    }
	
    function updateLowestMonthlyAnnuityFactor($force = false) {
        global $conn_standard;
        if ($this->lowestMonthlyAnnuityFactor < 1000 && !force)
            return;

        $s = "SELECT * FROM SveaPaymPlans WHERE isTestPlan = ";
        $s .= $this->test ? -1 : 0 . "";
        $res = mysqli_query($conn_standard, $s);

        while ($row = mysqli_fetch_object($res)) {
            if ($row->MonthlyAnnuityFactor < $this->lowestMonthlyAnnuityFactor)
                $this->lowestMonthlyAnnuityFactor = $row->MonthlyAnnuityFactor;
        }
    }

    function get_monthly_cost($goodsValueMoms, $flags = null) {
        global $pay;
        $this->updateLowestMonthlyAnnuityFactor();
        return round($this->lowestMonthlyAnnuityFactor * $goodsValueMoms, 0, PHP_ROUND_HALF_UP);
    }

    function get_pclass_extra($period) {
        global $pay;

        $conn_standard = Db::getConnection();

        $pclass = -1;

        if ($pay == "sveainvoice") {
            $pclass = -2;
            return $pclass;
        }
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		
		}

        // $sel = "SELECT * FROM SveaPaymPlans WHERE ContractLengthInMonths = " . $pay . " AND country = '";
        $sel = "SELECT CampaignCode FROM SveaPaymPlans WHERE ContractLengthInMonths = " . $period . " AND country = '";
        $sel .= Locs::getCountry();
        $sel .= "' AND isTestPlan=";
        $sel .= $this->test ? -1 : 0 . "";
        //echo $sel;
        // echo "<br>här: " . $conn_standard . " till hit<br>";

        $res = mysqli_query($conn_standard, $sel);
        $row = mysqli_fetch_object($res);

        // echo "här:" . $row->CampaignCode;
        // return $row->CampaignCode;        
        return $row->CampaignCode;
    }

    function svea_example() {

        $clientInvoiceRows = array(Array(
                "Description" => 'KitchenAid Stand Mixer (Red)',
                "PricePerUnit" => 500.00,
                "NumberOfUnits" => 1,
                "Unit" => "st",
                "VatPercent" => 25.00,
                "DiscountPercent" => 0
            ),
        );


        $request = Array(
            "Auth" => Array(
                "Username" => 'sverigetest',
                "Password" => 'sverigetest',
                "ClientNumber" => 79021
            ),
            "CreateOrderInformation" => Array(
                "ClientOrderNumber" => '2-' . time(),
                "OrderRows" => $clientInvoiceRows,
                "CustomerIdentity" => array(
                    "NationalIdNumber" => 4605092222,
                    "Email" => 'test@sveaekonomi.se',
                    "PhoneNumber" => '07770007770',
                    "IpAddress" => $_SERVER['REMOTE_ADDR'],
                    "FullName" => 'test testsson',
                    "Street" => 'testgatan 2',
                    "ZipCode" => '16865',
                    "Locality" => 'Solna',
                    "CountryCode" => 'SE',
                    "CustomerType" => 'Individual',
                    "IndividualIdentity" => null
                ),
                "OrderDate" => date('c'),
                "AddressSelector" => '',
                "OrderType" => 'Invoice',
                "PreApprovedCustomerId" => 0
            )
        );


        //Put all the data in request tag
        $data['request'] = $request;

        //print_r($data);
        //exit;
        //Print the request
        //print_r($data);
        //Call Soap
        $client = new SoapClient($this->wsdlLink);


        //Make soap call to below method using above data
        $svea_req = $client->CreateOrderEu($data);


        //Print the response
        // print_r($svea_req);
        // print_r($data);
    }

    function verifyAddress() {

        // move first and last name to get the name right
        $name = trim($_SESSION['old_lastName']) . ", " . trim($_SESSION['old_firstName']);
        /**
         * Was attempting to make it more "smart" and compare only first of "all" first names. But I think it's good enough anyway...
        $sveaName[] = split(",", $this->sveaCustomerIdentity->FullName);
        $sveaFirstName = trim($sveaName[1]);
        $sveaLastName = trim($sveaName[0]);
        */
        if (!$this->addressCompare($name, $this->sveaCustomerIdentity->FullName)) {
            $this->sveaVerifiedAddress = false;
            return false;
        }
        if (!$this->addressCompare($_SESSION['old_lco'], $this->sveaCustomerIdentity->Street, "addressline")) {
            if (!$this->addressCompare($_SESSION['old_ladress'], $this->sveaCustomerIdentity->Street, "addressline")) {
                $this->sveaVerifiedAddress = false;
                return false;
            }
        }
        if (!$this->addressCompare($_SESSION['old_lpostnr'], $this->sveaCustomerIdentity->ZipCode, "postal")) {
            $this->sveaVerifiedAddress = false;
            return false;
        }
        if (!$this->addressCompare($_SESSION['old_lpostadr'], $this->sveaCustomerIdentity->Locality, "city")) {
            $this->sveaVerifiedAddress = false;
            return false;
        }
        return true;
    }
    
    function sveaCustomerIdentityToString() {

        $address = "";
        $address .= $this->sveaCustomerIdentity->FullName . "\n";
        $address .= $this->sveaCustomerIdentity->Street . "\n";
        $address .= $this->sveaCustomerIdentity->ZipCode . " " . $this->sveaCustomerIdentity->Locality .  "\n";
        
        return $address;
    }

    /**
     * 
     * @param type $addressTry
     * @param type $addressCorrect
     * @param type $type one of 'name', 'addressline', 'postal' or 'city'
     * @param type $lev_distance the levenshtein distance that is accepted
     * 
     * returns true if matching is ok false otherwise
     * 
     * Algorithm: 
     * 
     * 1. Remove whitespace
     * 2. All to lower to exclude case sensitivity
     * 
     * If address line {
     *  Get numbers from end of string and compare the numbers (for streetnumber)
     *  Compare rest of string for street name
     * 
     *  If customers address name are shorter. Compare only these 
     *  first characters with same amount of characters in sveas address
     * 
     * } else if postal {
     *  Remove all but numbers and compare. Should be EXACT match 
     * } else if city {
     *  Compare directly, should be EXACT match
     * } else  if name {
     *  diff max levenshtein diff
     * }
     * 
     */
    function addressCompare($addressTry, $addressCorrect, $type = "name", $lev_distance = 2) {

        $addressTry = strtolower($addressTry);
        $addressTry = preg_replace("/\s/", "", $addressTry);

        $addressCorrect = strtolower($addressCorrect);
        $addressCorrect = preg_replace("/\s/", "", $addressCorrect);

        if ($type == "addressline") {
            // Get street number and compare
            preg_match("/[0-9]$/", $addressTry, $res);
            $numTry = $res[0];
            preg_match("/[0-9]$/", $addressCorrect, $res);
            $numCorr = $res[0];
            if ($numTry != $numCorr) {
                return false;
            }
            // Get street name and compare
            $addressTry = preg_replace("/[0-9]$/", "", $addressTry);
            $addressCorrect = preg_replace("/[0-9]$/", "", $addressCorrect);

            $dist = levenshtein($addressTry, substr($addressCorrect, 0, strlen($addressTry)));
            if ($dist > $lev_distance)
                return false;
        } else if ($type == "name") {
            $dist = levenshtein($addressTry, substr($addressCorrect, 0, strlen($addressTry)));
            if ($dist > $lev_distance)
                return false;
        } else if ($type == "postal") {
            $addressTry = preg_replace("/[^0-9]/", "", $addressTry);
            $addressCorrect = preg_replace("/[^0-9]/", "", $addressCorrect);
            if ($addressTry != $addressCorrect)
                return false;
        } else if ($type == "city") {
            // exact match TODO: remove non-standard characters before? 
            if ($addressTry != $addressCorrect)
                return false;
        } else {
            return "error, wrong type";
        }


        return true;
    }

}

?>
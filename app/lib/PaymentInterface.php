<?php

/**
 * Common functions for payments. Extend this class when 
 * creating a new payment method. It's technically not
 * an interface but should be considered as one
 *
 * @author nils
 */
abstract class PaymentInterface {

    /**
     * holds orderId, temporary unique reference number to be used for transaction id. This will be
     * matched against the orders ordernumber when order is finally created
     *
     * @var long
     */
    public $orderId = 0;
	public $kundnr = 0;
    /**
     * Final ordernr after order has been created
     * @var long
     */
    public $finalOrdernr = 0;
    /** if payment is verfied */
    public $verified;
    /**  */
    public $invoiceRows;

    /**  */
    public $rounding;
    /** Payment reference from PSP */
    public $external_reference;
    /** Holds error message if anything goes wrong */
    public $errorMess;
    /** Table_id  */
    public $payment_reference_id; 
    
    /** PSP used  (id) */
    public $payment_method;
	/** PSP used (text) */
	public $payment_method_s;
	
	/** Method of delivery (id)  */
	public $delivery_method;

	/** Method of delivery (text)  */
	public $delivery_method_s;	
	
	/** Leveranssatt_id */
	public $leveranssatt_id;
    /** Total sum including tax */
    public $totalSum      ;
    /** Total sum excluding tax */
    public $total;
    /** Total tax */
    public $totalTax;
    public $currency;
    /** Buyers name  */
    public $payment_name;
    public $address1;
    public $city;
    public $country_iso_code;
    public $postal;
    public $shipping_zipcode;
    public $payment_phone;
	public $payment_mobile_phone;
    public $payment_email ;   
    public $payment_firstName    ;
    public $payment_lastName     ;
    /** Status from PSP  */
    public $status   ;
    
    public $payment_date;
    public $comment;
    /** If address is confirmed/verified */
    public $payment_address_confirmed;  
    
    public $return_url;
    
    public function __construct() {
    	global $kundnrsave;
        global $fromMobile;
        if ($fromMobile) {
            $this->return_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?site=mobile&step=1';
		} else {
            $this->return_url = 'https://' . $_SERVER['HTTP_HOST'] . '/?' .  $_SERVER['PHP_SELF'] . '?site=desktop';
		}
		if ($kundnrsave!='')
			$this->kundnr = $kundnrsave;
    }
      
    /**
     * sets and gets orderId in class
     * @return long
     */
    public function getSetOrderId() {
        
        if ($this->orderId != "" && is_numeric($this->orderId))
            return $this->orderId;
            
        $str = "SELECT max(orderId) as maxId from cyberorder.orderIds";
        $res = mysqli_query(Db::getConnection(true), $str);

        if (mysqli_num_rows($res) > 0) {

            $row = mysqli_fetch_object($res);
            $orderId = $row->maxId + 1;
            $ins = "INSERT INTO cyberorder.orderIds (orderId) values (" . $orderId . ")";
            //echo $ins;
            mysqli_query(Db::getConnection(true), $ins);
            $this->orderId = $orderId;
            //echo $this->orderId;
            //exit;
            return $orderId;
        } else {
            return 0;
        }
    }

    public function createInvoiceRows() {

        global $kundvagn, $fi, $discountPercent, $discountAmount, $discounts, $bask,
        $moms, $moms1, $moms2, $moms3, $moms4, $goodsvalue, $fi, $sv, $no, $pay, $goodsvalueMoms, $extra_frakt, $butiksfrakt, $old_foretag, $old_forsakring_new, $fraktartnr;
        
        if (!is_object($bask))
            $bask = new CBasket();
        $this->payment_method = $bask->getBetalsattId ( $pay );
		$this->payment_method_s = $pay;
		
        $bask->getDiscountArticles($articles, $fi, $discountPercent, $discountAmount, $discounts);
        $mobile = new CMobile();
        $invoiceRows = array();
		
		if (eregi ( "fraktbutik", $kundvagn )) {
			$butiksfrakt = true;
			// echo "fraktbutik";
		} else {
			$butiksfrakt = false;
		}
			
        if (ereg("(grejor:)(.*)", $kundvagn, $matches)) {
            # Split the number of items and article id s into a list
            $orderlista = $matches[2];
            $argument = split("\|", $orderlista);
        }
        $n = count($argument);

        for ($i = 0; ($i < $n); $i+=2) {
            $arg = $argument[$i];        # Article id
            $count = $argument[$i + 1];    # Keeps track of the number of the same article

            if (eregi("^frakt", $arg))
                $fraktartnr = $arg;
			$this->delivery_method = $bask->getLeveranssatt_id ( $fraktartnr );
			$this->delivery_method_s = $fraktartnr;
			
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
						if (($article[0] == "gt-i9105" || $article[0] == "gt-i9105w") && $art2 == "TFSDHC16GB") {
							$invoiceRows = array_merge($invoiceRows, write_toOrderposter($art2, 0, 2, true, true));
						} elseif (($article[0] == "gt-i9105" || $article[0] == "gt-i9105w") && $art2 == "MB-MSAGB") {
							$invoiceRows = array_merge($invoiceRows, write_toOrderposter($art2, 0, 2, true, true));
						} else {
							$invoiceRows = array_merge($invoiceRows, write_toOrderposter($art2, 0, $article[1], true, true));
						}
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
        if ($pay == "sveainvoice" && ((time() > strtotime('2019-12-31 23:59:59') && $sv && !$fi) || $fi))
        // if ($pay == "sveainvoice")
            $invoiceRows = array_merge($invoiceRows, write_toOrderposter("invoicefee", $newordernr, 1, false, true));

		if ($old_forsakring_new == -1) {
            $invoiceRows = array_merge($invoiceRows, write_toOrderposter("friforsakring", $newordernr, 1, true, true));
		}
		
		if ($_SESSION['CYBAIRGUN_XBOX']) {
            $invoiceRows = array_merge($invoiceRows, write_toOrderposter("15077", $newordernr, 1, true, true));
		}
		
		if ( $_SESSION['old_giftcardrebate'] > 0 ) {
			$invoiceRows = array_merge($invoiceRows,  write_toOrderposter("avdrag", $newordernr, 1, false, true) );
			$invoiceRows[sizeof($invoiceRows) - 1]['PricePerUnit'] = (0 - $_SESSION['old_giftcardrebate']);
			$goodsvalue -= $_SESSION['old_giftcardrebate'];
			// $moms påverkas inte
		}
	
        $momsTot = $moms1 + $moms2 + $moms3 + $moms4;
        
        $goodsvalueMoms = $goodsvalue + $momsTot;
        
        $totalsumma = number_format($goodsvalueMoms, 0, "", "");
        $totalsumma2 = number_format($goodsvalue, 0, "", "");
        $öresutjämning = $totalsumma - ($goodsvalueMoms);
        $öresutjämning2 = $totalsumma2 - ($goodsvalue);
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
            echo "\n<br>moms: " . $moms;
            echo "\n<br>moms1: " . $moms1;
            echo "\n<br>moms2: " . $moms2;
            echo "\n<br>moms3: " . $moms3;
            echo "\n<br>moms4: " . $moms4;
            echo "\n<br>goodsvalue " . $goodsvalue;
            echo "\n<br>goodsvalueMoms " . $goodsvalueMoms;
            echo "\n<br>totalsumma " . $totalsumma;
            //exit;
        }

        if ($öresutjämning2 == -0.5) {
            $öresutjämning2 = 0.5;
            $totalsumma2 += 1;
        }

        if ($öresutjämning == -0.5) {
            $öresutjämning = 0.5;
            $totalsumma += 1;
        }

        
        if (abs($öresutjämning)>0.01) {
            $invoiceRows = array_merge($invoiceRows, write_toOrderposter("avrund", $newordernr, 1, false, true));
            $invoiceRows[sizeof($invoiceRows) - 1]['PricePerUnit'] = $öresutjämning;
        }


        $this->invoiceRows = $invoiceRows;

        $this->totalSum = $totalsumma;
        $this->total = $goodsvalue;
        $this->taxTotal = $momsTot;
        $this->rounding = $öresutjämning;
        //print_r($this);
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
        	Tools::print_rw($invoiceRows);
        	//exit;
        }
        return $invoiceRows;

        
    }
    
    public function savePaymentReferenceToDb() {
        global $kundnrsave, $fraktartnr, $pay;
        if ($this->external_reference == "")
            return false;
        
        $insert = "INSERT INTO cyberorder.payment_reference(external_reference, payment_method, payment_method_s, delivery_method, delivery_method_s, totalSum, currency, orderId, payment_name, address1, city, 
            country_iso_code, country_name, postal, payment_phone, payment_email, payment_firstName, payment_lastName, 
            status, payment_date, comment, payment_address_confirmed, kundnr, order_object, ordernr) values (";
        
		if ($this->payment_date == '') {
			$this->payment_date = date("Y-m-d h:m:s");
			error_log("did set date to: " . $this->payment_date);
		} 
		$kundnr = (int)$this->kundnr > 0 ? $this->kundnr : $kundnrsave;
		$kundnr = (int)$kundnr;
		
        $insert .= " '" . $this->external_reference . "' ,\n";
        $insert .= " " . (is_numeric($this->payment_method) ? $this->payment_method : 0)  . ",\n";
		$insert .= " '" . $this->payment_method_s . "',\n";
        $insert .= " " . (is_numeric($this->delivery_method) ? $this->delivery_method : 0)  . ",\n";
		$insert .= " '" . $this->delivery_method_s . "',\n";
        $insert .= " " . (str_replace("," , "." , $this->totalSum )) . ",\n";        
        $insert .= " '" . $this->currency . "',\n";
        $insert .= " " . $this->orderId . ",\n";
        $insert .= " '" . $this->payment_name . "',\n";
        $insert .= " '" . $this->address1 . "',\n";
        $insert .= " '" . $this->city . "',\n";
        $insert .= " '" . $this->country_iso_code . "',\n";
        $insert .= " '" . $this->postal . "',\n";
        $insert .= " '" . $this->shipping_zipcode . "',\n";
        $insert .= " '" . $this->payment_phone . "',\n";
        $insert .= " '" . $this->payment_email . "',\n";        
        $insert .= " '" . $this->payment_firstName . "',\n";        
        $insert .= " '" . $this->payment_lastName . "',\n";        
        $insert .= " '" .  $this->status  . "',\n";        
        $insert .= " '" . $this->payment_date . "',\n";        
        $insert .= " '" . $this->comment . "',\n";  
        $insert .= " " . ($this->payment_address_confirmed ? -1 : 0) . ", ";		
		$insert .= " " . $kundnr . ", ";
		$insert .= " '" . print_r($this->invoiceRows, true) . "',\n";  
        $insert .= " " . (is_numeric($this->finalOrdernr) ? $this->finalOrdernr : 0)  . " \n";
        $insert .= ")\n";
		Log::addLog("Tmnp: " . $insert, Log::LEVEL_INFO);
        $res = mysqli_query(Db::getConnection(true), $insert);
        if (!$res)      {      
            
            $this->errorMess .=  " : " . mysqli_errno(Db::getConnection(true)) . " : " . $insert;
        }
		
        $this->payment_reference_id = mysqli_insert_id(Db::getConnection(true));

        return $res;
               
    }
    /**
     * Set ordernr when order is saved. Called in placeOrder.php
     * 
     * @param type $ordernr
     * @return boolean
     */
    public function finalize($ordernr) {
        if (!is_numeric($ordernr) || $ordernr < 1 || !is_numeric($this->orderId))
            return false;
        
        $this->finalOrdernr = $ordernr;
        
        $update = " UPDATE cyberorder.payment_reference SET ordernr = " . $ordernr . " ";
        
        $update .= " WHERE orderId =" . $this->orderId;
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
    /**
     * Method to trigger import of payment, i.e. e.g. send to active mq queue
     */
    abstract public function triggerImport();   
}

?>

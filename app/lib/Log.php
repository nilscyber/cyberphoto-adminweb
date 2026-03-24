<?php

/**
 * Class for logging of messages. Log by using one of the static functions 
 * addLog, createTicket or createTicketAndLog. 
 * 
 * @author nils
 */
require_once ("Locs.php");

Class Log {
    
    /** Emergency: system is unusable */
    const LEVEL_EMERG = 0;
    /** Alert: action must be taken immediately */
    const LEVEL_ALERT = 1;
    /** Critical: critical conditions */
    const LEVEL_CRIT = 2;
    /** Error: error conditions */
    const LEVEL_ERR = 3;
    /** Warning: warning conditions */
    const LEVEL_WARN = 4;
    /** Notice: normal but significant condition */
    const LEVEL_NOTICE = 5;
    /** Informational: informational messages */
    const LEVEL_INFO = 6;
    /** Debug: debug messages */
    const LEVEL_DEBUG = 7;
    /** Default recipient for tickets   */
    const RECIPIENT_IT = "it-support@cyberphoto.se";
    /** Standard recipient for ticket when not IT */
    const RECIPIENT_STANDARD = "kundtjanst@cyberphoto.se";
    /** Recipient for sales issues */
    const RECIPIENT_SALES = "sales@cyberphoto.se";
	
	const SMS_PASSWD = "sendSMSuser";
	
	const SMS_USER = "sendSMSuser";
	
	const SMS_URL = "http://192.168.1.45:81/sendmsg";

    //TODO: fill in all possible recipients

    private $conn;

    /**
     *  Creates log entry in table logWeb 
     * Example: <br>
     * Log::createTicket("Message to log", Log::LEVEL_ERR, null, null);
     *       
     * @param string $message - message for ticket 
     * @param int $level - level of importance 
     * @param system $source - file where it happend, set to null to use current php file
     * @param string $locale - current locale, set to nulll to use session variable "currentLocale"
     */
    public static function addLog($message, $level, $source, $locale) {
        $log = new Log();

        if (!isset($level))
            $level = self::LEVEL_NOTICE;

        if (!isset($locale) || $locale == "") {
            if (!isset($_SESSION['currentLocale'])) {
                Locs::setDefaultLocale();
            }
            $locale = $_SESSION['currentLocale'];
        }
        if (!isset($source) || $source == "")
        //$source = __FILE__; // 
            $source = $_SERVER['PHP_SELF'];

        $insert = "INSERT INTO cyberphoto.logWeb (logDate, logPage, logComment,logIP, locale, level, serverIP) values (";
        $insert .= "now(), ";
        $insert .= "'" . mysqli_real_escape_string($source) . "', ";
        //$insert .= "'" .  . "', "; 
        $insert .= "'" . mysqli_real_escape_string($message) . "', ";
        $insert .= "'" . $_SERVER['REMOTE_ADDR'] . "', ";
        $insert .= "'" . $locale . "', ";
        $insert .= "'" . $level . "', ";
        $insert .= "'" . $_SERVER['SERVER_ADDR'] . "' ";

        $insert .= ")";

        $log->connectDb();
        if (!(mysqli_query($log->conn, $insert))) {
            error_log("Gick inte att logga meddelande i weblog. Insertquery: " . $insert, 1, "admin@cyberphoto.se", "Subject: Problem med error log\nFrom: admin@cyberphoto.se\n");
        }
    }
	public static function sendSMS($phone, $message) {
		
         $urlStr = self::SMS_URL . "?user=" . self::SMS_USER . "&passwd=" . self::SMS_PASSWD . "&cat=1&to=" .  preg_replace("/\\+/", "%20", urlencode($phone)) . 
                "&text=" . preg_replace("/\\+/", "%20", urlencode($message));
		
		 echo $urlStr;
		 
		$r = new HttpRequest($urlStr, HttpRequest::METH_GET);
		$r->setOptions(array('lastmodified' => filemtime('local.rss')));
		$r->addQueryData(array(
			'category' => 3, 
			'user' => self::SMS_USER, 
			'passwd' => self::SMS_PASSWD, 
			
			
			));
		try {
			$r->send();
			if ($r->getResponseCode() == 200) {
				file_put_contents('local.rss', $r->getResponseBody());
			}
		} catch (HttpException $ex) {
			echo $ex;
		}		 
		 return;
		// Get cURL resource
		$ch = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $urlStr);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
													'Content-Type: application/xml',
													'Connection: Keep-Alive'
													));

		// Send the request & save response to $resp

		$resp = curl_exec($ch);
		echo "<br>resp:".$resp."<br>";
		// Close request to clear up some resources
		//curl_close($ch);
		$info = curl_getinfo($ch);
		print_r($info);

		
		if(!curl_errno($ch)) {
			echo '<br>Curl error: ' . curl_error($ch);
			return true;
		} else {
			echo '<br>Curl error: ' . curl_error($ch);
			return false;
		} 
	}
    /**
     * Create ticket 
     * 
     * Example: 
     * Log::createTicket(Log::RECIPIENT_IT, "Message to log", "Subject line for messsage", Log::LEVEL_ERR, null, null);
     *       
     * @param string $recipient - recipient 
     * @param string $message - message for ticket 
     * @param string $subject - subject in message
     * @param int $level - level of importance 
     * @param system $source - file where it happend, set to null to use current php file
     * @param string $locale - current locale, set to nulll to use session variable "currentLocale"
     * 
     
     */
    public static function createTicket($recipient, $message, $subject, $level, $source, $locale) {
        
        if (!isset($recipient) || $recipient == "")
            $recipient = self::RECIPIENT_IT;

        if (!isset($level))
            $level = self::LEVEL_NOTICE;

        if (!isset($locale) || $locale == "") {
            if (!isset($_SESSION['currentLocale'])) {
                Locs::setDefaultLocale();
            }
            $locale = $_SESSION['currentLocale'];
        }
        if (!isset($source) || $source == "")
            $source = __FILE__;
        if (!isset($subject) || $subject == "")
            $subject = "Ärende från webben, nivå " . $level;

        $extra = "From: admin@cyberphoto.se";

        $message .= "\n\n-------------\nÖvrigt: \n" .
                "\nLevel: " . $level .
                "\nSida: " . $source .
                "\nLocale: " . $locale
        ;


        if (!mail($recipient, $subject, $message, $extra))
            error_log("Gick inte att skapa ärende. Meddelande: " . $message, 1, "admin@cyberphoto.se", "Subject: Problem med error log\nFrom: admin@cyberphoto.se\n");
    }

    /**
     * Convenience function if you need to both log and create ticket.  
     * Example: 
     * Log::createTicketAndLog(Log::RECIPIENT_IT, "Message to log", "Subject line for messsage", Log::LEVEL_ERR, null, null);
     *       
     * @param string $recipient - recipient 
     * @param string $message - message for ticket 
     * @param string $subject - subject in message
     * @param int $level - level of importance 
     * @param system $source - file where it happend, set to null to use current php file
     * @param string $locale - current locale, set to nulll to use session variable "currentLocale"
     *  
     */
    public static function createTicketAndLog($recipient, $message, $subject, $level, $source, $locale) {
        self::createTicket($recipient, $message, $subject, $level, $source, $locale);
        self::addLog($message, $level, $source, $locale);
    }

    private function connectDb() {
        if (!mysqli_ping($this->conn)) {
            include("connections.php");
            $this->conn = $conn_master;
        }
    }

}

?>

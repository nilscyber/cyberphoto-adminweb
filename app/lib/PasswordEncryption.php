<?php

require_once("Locs.php");
require_once("Log.php");
require_once("CBasket.php");
require_once("Tools.php");
require_once("CCheckIpNumber.php");

class PasswordEncryption {

    var $conn_master;
    var $conn_standard;
    // var $executingPage = "/login/index.php";
    var $executingPage = "/login/";

    /**  */
    var $passwd_life_length = 1;
    var $max_trials = 5;
    var $verified_customer;
    var $error_mess;
    var $passwd_recovery_id;
    var $sourcePage;
    var $Cinputvalue; // värdet som kunden skrev in i fältet (kundnr, epost eller användarnamn)
    public function encrypt_password($password, &$salt = '', $stretch_cost = '07') {
        $salt = strlen($salt) != 22 ? $this->_create_salt() : $salt;
        $sec = '$2a$' . $stretch_cost . '$' . $salt . '$';
        if (function_exists('crypt') && defined('CRYPT_BLOWFISH')) {
            return crypt($password, $sec);
        } else {
            $this->error_mess = l('System error, try again later');
            return false;
        }
    }

    /**
     * @param string $pass The user submitted password
     * @param string $hashed_pass The hashed password pulled from the database
     * @param string $salt The salt used to generate the encrypted password
     */
    public function validate_password($password, $encrypted_password) {
        //echo $password . "<br>";
        //echo $encrypted_password . "<br>";
        //echo crypt($password, $encrypted_password) . "<br>";
        return strcmp($encrypted_password, crypt($password, $encrypted_password)) == 0;
    }

    /**
     * Create a new salt string which conforms to the requirements of CRYPT_BLOWFISH.
     *
     * @access  protected
     * @return  string
     */
    protected function _create_salt() {
        $salt = $this->_pseudo_rand(128);
        return substr(preg_replace('/[^A-Za-z0-9_]/is', '', base64_encode($salt)), 0, 22);
    }

    /**
     * Generates random sequence containing numbers and letters
     * @param type $length
     * @return String password
     */
    public function _generate_pass($length = 50) {

        $passwd = "";

        $i = 0;
        while (strlen($passwd) < $length) {

            $randvalue = $this->_pseudo_rand(1);
            if (preg_match("/[a-zA-Z0-9]/", $randvalue)) {
                $passwd .= $randvalue;
            }
            if ($i > 5000)
                break;
        }
        return ($passwd);
    }

    /**
     * Generates a secure, pseudo-random password with a safe fallback.
     *
     * @access  public
     * @param   int     $length
     */
    protected function _pseudo_rand($length) {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $is_strong = false;
            $rand = openssl_random_pseudo_bytes($length, $is_strong);
            if ($is_strong === true)
                return $rand;
        }
        $rand = '';
        $sha = '';
        for ($i = 0; $i < $length; $i++) {
            $sha = hash('sha256', $sha . mt_rand());
            $chr = mt_rand(0, 62);
            $rand .= chr(hexdec($sha[$chr] . $sha[$chr + 1]));
        }
        return $rand;
    }

    private function getConnectionDb($master = true) {
        if ($master) {
            if (!$this->conn_master || !mysqli_ping($this->conn_master)) {
                $this->conn_master = Db::getConnection(true);
            }
            return $this->conn_master;
        } else {
            if (!$this->conn_standard || !mysqli_ping($this->conn_standard)) {
                $this->conn_standard = Db::getConnection();
            }
            return $this->conn_standard;
        }
    }

    public function recovery_link_create($kundnr, $sourcePage="", $email) {
        if ($kundnr == null || strlen($kundnr) < 3)
            return null;
        $token = $this->_generate_pass();
        $insert = "INSERT INTO cyberorder.passwd_recovery(kundnr, passwd_temp, ip_adress, email, host, inputvalue) values (";
        $insert .= "'" . $kundnr . "', '" . $token . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $email . "', '" . $_SERVER["HTTP_HOST"] . "', '" . $this->Cinputvalue ."' ";
        $insert .= " )";

        //echo $insert;
        if (!mysqli_query($this->getConnectionDb(true), $insert)) {
            Log::addLog("Failed to insert temporary password: query: " . $insert . " . Other values: " . print_r(get_defined_vars(), true), Log::LEVEL_CRIT);
            return null;
        }
        // $url = "https://" . Locs::HTTP_HOST_From_Locale() . "?" . $this->executingPage . "?token=" . $token;
        $url = "https://" . Locs::HTTP_HOST_From_Locale() . $this->executingPage . l('reset-your-password') . "?token=" . $token;
        if ($sourcePage!="")
            $url .= "&sourcePage=" . $sourcePage;
        return $url;
    }

    public function verify_token($token, $sourcePage="") {
        
        global $conn_master; // to work with CBasket

        if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
	        sleep(3); // make it harder to crack
        }
        $sel = "SELECT * FROM cyberorder.passwd_recovery WHERE passwd_temp='" . $token . "' ";
        $sel .= " AND isUsed=0 ";
        $res = mysqli_query($this->getConnectionDb(true), $sel);
        if (mysqli_num_rows($res) == 0) {
            $this->error_mess = l('Link does not work. It can already be used');
            return false;
        }
        if (mysqli_num_rows($res) > 1) {
            $this->error_mess = l('System error, try again later');
            return false; // TODO, create new link? 
        }
        $row = mysqli_fetch_object($res);
        $created = strtotime($row->created);
        $live_to = $created + $this->passwd_life_length * 60 * 60; 
        if (time() > $live_to) {
            $this->error_mess = l('The link is no longer valid');
            return false;
        }

        if ($this->customer_exists($row->kundnr)) {
            $this->verified_customer = $row->kundnr;
            $this->passwd_recovery_id = $row->passwd_recovery_id;
            $this->sourcePage = $sourcePage;

            // set "old" variables that makes the customer logged in
            $_SESSION['kundnrsave'] = $this->verified_customer;
            $_SESSION['confirm'] = 1;

            // This is needed to load all customer_info-stuff
            $conn_master = $this->getConnectionDb(true);
            $b = new CBasket();
            $b->customer_info(true);
        } else {
            $this->error_mess = l('customerNo_missing');
            return false;
        }



        return $this->verified_customer;
    }

    public function customer_exists($kundnr) {
        $sel = "SELECT * FROM cyberorder.Kund WHERE kundnr = " . $kundnr;
        $res = mysqli_query($this->getConnectionDb(true), $sel);
        if (mysqli_num_rows($res) == 1)
            return true;
        else
            return false;
    }

    public function update_passwd($kundnr, $new_passwd) {
        if ($kundnr == null || strlen($kundnr) < 3 || $new_passwd == null || strlen($new_passwd) < 1)
            return false;
        
        $new_passwd = Tools::sql_inject_clean($new_passwd); // rensa bort tecken som vi inte godkänner
        
        $updt = "UPDATE cyberorder.Kund SET kundid_encr = '" . $this->encrypt_password($new_passwd) . "', trials=0 WHERE kundnr = " . $kundnr;
        // echo $updt;
		// exit;
        //echo " : " . $new_passwd;
        //return true;
        if (!mysqli_query($this->getConnectionDb(true), $updt)) {
            $this->error_mess = l('system_error_try_again_later');
            Log::addLog("Failed to update customer with new password, query: " . $updt . " . Other values: " . print_r(get_defined_vars(), true), Log::LEVEL_CRIT);
            return false;
        } else {
            Log::addLog("New password set for customer " . $this->verified_customer, Log::LEVEL_INFO);
            // Set recovery_id to used since it's...well, used.
            $updt = "UPDATE cyberorder.passwd_recovery SET isUsed = -1 WHERE passwd_recovery_id= " . $this->passwd_recovery_id;
            if (!mysqli_query($this->getConnectionDb(true), $updt))
                Log::addLog("Recovery table could not be reset, query used:  " . $updt . " .\nAll other values: " . print_r(get_defined_vars(), true), Log::LEVEL_ERR);
            return true;
        }
    }

    public function update_passwd_form() {
        if (!$this->verified_customer)
            return null;
        $form = "<form method=\"post\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\">";
        $form .= "<input type=\"hidden\" value=\"1\" name=\"updatePasswd\">";
        $form .= "<div style=\"margin-top: 5px;\"><label style=\"\" for=\"new_passwd\">" . l('Enter a new password') . "</label></div>";
        $form .= "<div style=\"margin-top: 1px;\"><input size=\"30\" style=\"text-align: center;\" type=\"password\" id=\"new_passwd\" name=\"new_passwd\"></input></div>";
        $form .= "<div style=\"margin-top: 5px;\"><label style=\"\" for=\"new_passwd_confirm\">" . l('Repeat password') . "</label></div>";
        $form .= "<div style=\"margin-top: 1px;\"><input size=\"30\" style=\"text-align: center;\" type=\"password\" id=\"new_passwd_confirm\" name=\"new_passwd_confirm\"></input></div>";
        // $form .= "<div style=\"margin-top: 5px;\">&nbsp;</div>";
        $form .= "<div style=\"margin-top: 5px;\"><input name=\"btn\" value=\"" . l('Reset') . "\" type=\"submit\"></div>";
        $form .= "</form>";
        return $form;
    }

    function getCustomer($inputvalue) {
		global $frameless, $currentUrl;

        $inputvalue = $this->sql_inject_clean($inputvalue);
		$this->Cinputvalue = $inputvalue;

        if (preg_match("/\@/", $inputvalue)) {

            // $select = "SELECT * FROM cyberorder.Kund WHERE email = '$inputvalue' AND borttagen = 0 order by kundnr DESC ";
            $select = "SELECT * FROM cyberorder.Kund WHERE email = '$inputvalue' AND borttagen = 0 order by skapad DESC ";
            $res = mysqli_query($this->getConnectionDb(false), $select);
            $check = mysqli_num_rows($res);
            // echo mysqli_num_rows($res);
        } else {

            // $select = "SELECT * FROM cyberorder.Kund WHERE kundnr like '$inputvalue' AND borttagen = 0 ";
            $select = "SELECT * FROM cyberorder.Kund WHERE kundnr = $inputvalue AND borttagen = 0 ";
            $res = mysqli_query($this->getConnectionDb(false), $select);
            $check = mysqli_num_rows($res);

            if (!$check > 0) {

                $select = "SELECT * FROM cyberorder.Kund WHERE userName = '$inputvalue' AND borttagen = 0 ";
                $res = mysqli_query($this->getConnectionDb(false), $select);
                $check = mysqli_num_rows($res);
            }
        }

        if ($check > 0) {

            while ($row = mysqli_fetch_object($res)) {

                $email = $row->email;
                $namn = $row->namn;
                $kundnr = $row->kundnr;
                $kundid = $row->kundid;
                $username = $row->userName;
				$beforedomain = $this->before_letter('@', $email);
				$star_beforedomain = $this->make_starred($beforedomain);
				$maildomain = $this->after_letter('@', $email);
                //$kundnr = sprintf("%-10d", $kundnr);
                $this->sendRecoveryLink($namn, $kundnr, $kundid, $username, $email);

                break; // stannar vid första kundnumret
            }
            echo "<h1>" . l('Confirmation') . "</h1>\n";
			echo "<div id=\"centrering\">\n";
            echo "<p>" . l('A message has been sent to your email address with instructions on how to reset your password') . ".</p>\n";
			if (!preg_match("/\@/", $this->Cinputvalue)) {
				echo "<p>" . l('Sent to email address') . ": <b><i>" . $star_beforedomain . "@" . $maildomain . "</i></b> (" . l('Note that the link is only valid for 60 minutes') . ")</p>\n";
			} else {
				echo "<p>" . l('Note that the link is only valid for 60 minutes') . ".</p>\n";
			}
			echo "</div>\n";
        } else {
            echo "<h1>" . l('Message') . "</h1>\n";
			echo "<div id=\"centrering\">\n";
            echo "<p>" . l('Sorry, we found no customer data based on') . " <b>" . $inputvalue . "</b>. " . l('Please try again') . ".</p>";
			if ($frameless) {
				echo "<a href=\"" . $currentUrl . "\"><p>" . l('Click here to try again') . "</p></a>";
			}
			echo "</div>\n";
        }
    }

	function before_letter($this_letter, $inthat) {
        return substr($inthat, 0, strpos($inthat, $this_letter));
    }

    function after_letter($this_letter, $inthat) {
        if (!is_bool(strpos($inthat, $this_letter)))
        return substr($inthat, strpos($inthat,$this_letter)+strlen($this_letter));
    }
	
	function make_starred($str){
		$len = strlen($str);
		// return substr($str, 0,1). str_repeat('*',$len - 2) . substr($str, $len - 1 ,1);
		return substr($str, 0,1). str_repeat('*',$len - 1);
	}

    function sql_inject_clean($str) {

        $str = str_ireplace("union", "", $str);
        $str = str_ireplace(";", "", $str);
        $str = str_ireplace("--", "", $str);
        $str = str_ireplace("'", "", $str);
        $str = str_ireplace("select", "", $str);
        $str = str_ireplace("drop", "", $str);
        $str = str_ireplace("update", "", $str);
        $str = str_ireplace("/*", "", $str);
        $str = str_ireplace("HAVING", "", $str);
        $str = str_ireplace("CAST", "", $str);
        $str = str_ireplace("CONVERT", "", $str);
        $str = str_ireplace("INSERT", "", $str);
        $str = str_ireplace(" AND ", "", $str);
        $str = str_ireplace("WHERE", "", $str);
        $str = str_ireplace("CREATE", "", $str);
        $str = str_ireplace("CREATE", "", $str);
        $str = str_ireplace("PROCEDURE", "", $str);
        $str = str_ireplace("EXEC", "", $str);
        $str = str_ireplace("_cmd", "", $str);
        $str = str_ireplace("version", "", $str);
        //$str = str_ireplace("'", "''", $str);
        return trim($str);
    }

    function sendRecoveryLink($namn, $kundnr, $passwd, $username, $email) {
        
        if (Locs::getLang() == "fi") {
            $addcreatedby = "info@cyberphoto.fi";
        } else if (Locs::getLang() == "no") {
            $addcreatedby = "info@cyberphoto.no";

        } else {
            $addcreatedby = "kundtjanst@cyberphoto.se";            
        }

        // $recipient .= " sjabo";
        $recipient .= $email;

        $subj = l('Reset your password');

        $extra = "From: " . $addcreatedby;
		//$extra = array("Content-type =>"text/plain: charset=\"UTF-8","From" => "abc@abc.com"); 
		$headers[] = 'Content-type: text/plain; charset=UTF-8';
		$headers[] = 'From: ' . $addcreatedby . '';
		//print_r($headers); exit;
        $text1 = l('Customer information') . ":\n\n";
        $text1 .= l('Name') . ": " . $namn . "\n";
        $text1 .= l('Customer number') . " : " . $kundnr . "\n";
        
        if ($username != "") {
            $text1 .= l('Username') . " : " . $username . "\n";
        }

        $text1 .= "\n\n" . l('Click the link below to reset your password') . "\n";
        $text1 .= $this->recovery_link_create($kundnr, $_SESSION['sourcePage'], $email) . "\n";
        //$text1 = str_replace("\n",  "<br>", $text1);
        SmtpMail::send($recipient, $subj, $text1, implode("\r\n", $headers));
    }
	
	// ***************************** NEDAN GÄLLER ADMIN *****************************
	
	function listAllRecovery($old) {
	
		$desiderow = true;

		$select  = "SELECT * FROM cyberorder.passwd_recovery ";
		if ($old) {
			$select .= "WHERE (created < now() - interval 60 minute) OR (created > now() - interval 60 minute AND isUsed = -1) ";
		} else {
			$select .= "WHERE created > now() - interval 60 minute AND isUsed = 0 ";
		}
		$select .= "ORDER BY created DESC ";
		$select .= "LIMIT 200 ";
		$res = mysqli_query($this->getConnectionDb(false), $select);
		$check = mysqli_num_rows($res);

		echo "<table cellspacing=\"1\" cellpading=\"2\">";
		echo "<tr>";
		echo "<td width=\"35\">&nbsp;</td>";
		echo "<td width=\"75\" class=\"bold align_left\">Kundnr</td>";
		echo "<td width=\"300\" class=\"bold align_left\">Skickad till e-post adress</td>";
		echo "<td width=\"130\" class=\"bold align_left\">IP-nummer</td>";
		echo "<td width=\"300\" class=\"bold align_left\">Inknappat värde</td>";
		if (!$old) {
			echo "<td width=\"160\" class=\"bold align_left\">Länken är gilltig till</td>";
			echo "<td width=\"70\" class=\"bold align_center\">Återstår</td>";
		} else {
			echo "<td width=\"160\" class=\"bold align_left\">Länken var gilltig till</td>";
			echo "<td width=\"70\" class=\"bold align_center\">&nbsp;</td>";
		}
		echo "<td width=\"35\">&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
		
		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
				
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
				
				$skapad = strtotime($created);
				$gilltig = strtotime(date('Y-m-d H:i:s', strtotime("$created +1 hour")));
				$aterstar = $gilltig - time();
				$aterstar2 = $aterstar / 60;
				
				echo "<tr>";
				echo "<td class=\"align_center\">";
				if ($host == "www.cyberphoto.fi") {
					echo "<img border=\"0\" src=\"fi_mini.jpg\">&nbsp;";
				} elseif ($host == "www.cyberphoto.no") {
					echo "<img border=\"0\" src=\"no_mini.jpg\">&nbsp;";
				} else {
					echo "<img border=\"0\" src=\"sv_mini.jpg\">&nbsp;";
				}
				echo "</td>";
				echo "<td class=\"$rowcolor\">" . $kundnr . "</td>";
				echo "<td class=\"$rowcolor\">" . $email . "</td>";
				echo "<td class=\"$rowcolor\">" . $ip_adress . "</td>";
				echo "<td class=\"$rowcolor\">" . $inputvalue . "</td>";
				echo "<td class=\"$rowcolor\">" . date('Y-m-d H:i:s', strtotime("$created +1 hour")) . "</td>";
				if ($isUsed == -1) {
					echo "<td class=\"$rowcolor\">&nbsp;</td>";
					echo "<td class=\"align_center\">&nbsp;<img src=\"status_green.jpg\"></td>";
				} else {
					if (!$old) {
						echo "<td class=\"$rowcolor align_right\">" . round($aterstar2,0) . " min&nbsp;</td>";
					} else {
						echo "<td class=\"$rowcolor\">&nbsp;</td>";
					}
					echo "<td class=\"align_center\">&nbsp;<img src=\"status_red.jpg\"></td>";
				}
				if ($ip_adress == $_SERVER['REMOTE_ADDR'] && !$old) {
					if ($host == "www.cyberphoto.fi") {
						echo "<td class=\"align_center\"><a target=\"_blank\" href=\"https://www.cyberphoto.se/login/index.php?token=" . $passwd_temp ."\">Återställningslänk</a></td>";
					} elseif ($host == "www.cyberphoto.no") {
						echo "<td class=\"align_center\"><a target=\"_blank\" href=\"https://www.cyberphoto.se/login/index.php?token=" . $passwd_temp ."\">Återställningslänk</a></td>";
					} else {
						echo "<td class=\"align_center\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/login/aterstall-ditt-losenord?token=" . $passwd_temp ."\">Återställningslänk</a></td>";
					}
				} else {
					echo "<td class=\"align_center\">&nbsp;</td>";
				}
				echo "</tr>";

				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
				
			}
		
		} else {
			echo "<tr>";
			echo "<td width=\"25\">&nbsp;</td>";
			echo "<td colspan=\"6\" class=\"firstrow bold italic\">Inga giltiga länkar finns just nu&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "</tr>";
		}

		echo "</table>";
		
	}

	function RecoveryByDay() {
	
		$desiderow = true;
	
		$select  = "SELECT DATE_FORMAT(created, '%Y-%m-%d') AS PubDate, COUNT(kundnr) AS Antal ";
		$select .= "FROM cyberorder.passwd_recovery ";
		$select .= "WHERE isUsed = -1 ";
		$select .= "GROUP BY PubDate ";
		$select .= "ORDER BY PubDate DESC ";
		$select .= "LIMIT 14 ";
		$res = mysqli_query($this->getConnectionDb(false), $select);
		$check = mysqli_num_rows($res);
	
		echo "<table cellspacing=\"1\" cellpading=\"2\">";
		echo "<tr>";
		echo "<td width=\"95\" class=\"bold align_center\">Datum</td>";
		echo "<td width=\"45\" class=\"bold align_center\">Antal</td>";
		// echo "<td>&nbsp;</td>";
		echo "</tr>";
	
		if (mysqli_num_rows($res) > 0) {
	
			while ($row = mysqli_fetch_object($res)) {
					
				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
	
	
				echo "<tr>";
				echo "<td class=\"$rowcolor bold align_center\">" . $row->PubDate . "</td>";
				echo "<td class=\"$rowcolor bold align_center\">" . $row->Antal . "</td>";
				// echo "<td class=\"$rowcolor\">&nbsp;</td>";
				echo "</tr>";
	
				if ($desiderow == true) {
				$desiderow = false;
				} else {
					$desiderow = true;
				}
	
			}
		
		} else {
			echo "<tr>";
			echo "<td width=\"25\">&nbsp;</td>";
			echo "<td colspan=\"6\" class=\"firstrow bold italic\">Inga giltiga länkar finns just nu&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "</tr>";
		}
		
		echo "</table>";
		
	}
	
}
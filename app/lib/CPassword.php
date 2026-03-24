<?php

Class CPassword {
	var $conn_my;

	function __construct() {
			
		$this->conn_my = Db::getConnection();
               
		$this->conn_order = Db::getConnectionDb('cyberorder');

	}
	
	function getCustomer($inputvalue) {
		global $sv,$fi;
	
		$inputvalue = $this->sql_inject_clean($inputvalue);
		
		if (preg_match("/\@/", $inputvalue)) {
			
			$select = "SELECT * FROM cyberorder.Kund WHERE email='$inputvalue' AND borttagen = 0 order by kundnr DESC ";
			$res = mysqli_query($this->conn_order, $select);
			$check = mysqli_num_rows($res);
			// echo mysqli_num_rows($res);
		
		} else {

			$select = "SELECT * FROM cyberorder.Kund WHERE kundnr like '$inputvalue' AND borttagen = 0 ";
			$res = mysqli_query($this->conn_order, $select);
			$check = mysqli_num_rows($res);
			
			if (!$check > 0) {

				$select = "SELECT * FROM cyberorder.Kund WHERE userName like '$inputvalue' AND borttagen = 0 ";
				$res = mysqli_query($this->conn_order, $select);
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
				$kundnr = sprintf("%-10d", $kundnr);
				
				if ( ($kundid == '') || ($kundid == 'nkN9RbBQ19sUs') || ($kundid == chr(32)) ) {
				
					$kundid = $this->generate_pass (5);
					$this->updateCustomerPassword($kundid,$kundnr);
				
				}
				
				$this->sendPassword($namn,$kundnr,$kundid,$username,$email);
		
                            break; // stannar vid första kundnumret
			}
		
			if ($fi && !$sv) {
				echo "<h1>Vahvistus</h1>\n";
				echo "<p>Asiakastietosi on nyt lähetetty.</p>\n<p><a href=\"/kundvagn/login_fi.php\">Takaisin kirjautumissivulle</a></p>\n";
			} elseif ($fi && $sv) {
				echo "<h1>Bekräftelse</h1>\n";
				echo "<p>Dina kunduppgifter har nu skickats.</p>\n<p><a href=\"/kundvagn/login_fi_se.php\">Tillbaka till loginsidan</a></p>\n";
			} else {
				echo "<h1>Bekräftelse</h1>\n";
				echo "<p>Dina kunduppgifter har nu skickats.</p>\n<p><a href=\"/kundvagn/login_se.php\">Tillbaka till loginsidan</a></p>\n";
			}
		
		} else {

			if ($fi && !$sv) {
				echo "<h1>Viesti</h1>\n";
				echo "<p>Etsintä ei antanut tulosta.</p>\n<p><a href=\"/kundvagn/login_fi.php\">Kokeile uudestaan</a></p>\n";
			} elseif ($fi && $sv) {
				echo "<h1>Meddelande</h1>\n";
				echo "<p>Tyvärr hittade vi inga kunduppgifter baserat på <b>$inputvalue</b></p><a href=\"/kundvagn/passw_fi_se.php\">Vänligen försök igen</a></p>\n";
			} else {
				echo "<h1>Meddelande</h1>\n";
				echo "<p>Tyvärr hittade vi inga kunduppgifter baserat på <b>$inputvalue</b></p><a href=\"/kundvagn/passw_se.php\">Vänligen försök igen</a></p>\n";
			}
		
		}
	
	}

	function updateCustomerPassword($kundid,$kundnr) {
		return;
		$conn_my2 = Db::getConnectionDb('cyberorder');
		
		$updt = "UPDATE cyberorder.Kund set kundid = '$kundid' WHERE kundnr = '$kundnr' ";

		$res = mysqli_query($conn_order, $updt);

	}
	
	function getCustomerInfo($kundnr) {

		$select  = "SELECT * FROM cyberorder.Kund WHERE kundnr = '$kundnr' ";

		$res = mysqli_query($this->conn_order, $select);

		$rows = mysqli_fetch_object($res);

		return $rows;

	}

	function sendPassword($namn,$kundnr,$passwd,$username,$email) {
		global $sv,$fi;

		if ($fi) {
			$addcreatedby = "info@cyberphoto.fi";
		} else {
			$addcreatedby = "kundtjanst@cyberphoto.se";
		}

		// $recipient .= " sjabo";
		$recipient .= $email;
		
		if ($fi && !$sv) {
			$subj = "Asiakastiedot CyberPhoto";

			$extra = "From: " . $addcreatedby;
			
			$text1 .= "Tiedot:\n\n";
			$text1 .= "Yritys / Nimi: " . $namn . "\n";
			$text1 .= "Asiakasnumero: " . $kundnr . "\n";
			$text1 .= "Salasana: " . $passwd . "\n";
			if ($username != "") {
				$text1 .= "Käyttäjätunnus: " . $username . "\n";
			}
			$text1 .= "\nhttps://www.cyberphoto.fi/index_fi.php?kundvagn/login_fi.php\n\n";
			$text1 .= "Asiakastiedot voit muuttaa seuraavan tilauksen yhtydessä.\n\n";
		} elseif ($fi && !$sv) {
			$subj = "Kunduppgifter hos CyberPhoto";

			$extra = "From: " . $addcreatedby;
			
			$text1 .= "Era uppgifter:\n\n";
			$text1 .= "Företag / Namn: " . $namn . "\n";
			$text1 .= "Kundnummer: " . $kundnr . "\n";
			$text1 .= "Lösenord: " . $passwd . "\n";
			if ($username != "") {
				$text1 .= "Användarnamn: " . $username . "\n";
			}
			$text1 .= "\nhttps://www.cyberphoto.fi/index_fi_se.php?kundvagn/login_fi_sv.php\n\n";
			$text1 .= "Dina inloggningsuppgifter ändrar du vid nästa orderläggning.\n\n";
		} else {
			$subj = "Kunduppgifter hos CyberPhoto";

			$extra = "From: " . $addcreatedby;
			
			$text1 .= "Era uppgifter:\n\n";
			$text1 .= "Företag / Namn: " . $namn . "\n";
			$text1 .= "Kundnummer: " . $kundnr . "\n";
			$text1 .= "Lösenord: " . $passwd . "\n";
			if ($username != "") {
				$text1 .= "Användarnamn: " . $username . "\n";
			}
			$text1 .= "\nhttp://www.cyberphoto.se/kundvagn/login.php\n\n";
			$text1 .= "Dina inloggningsuppgifter ändrar du vid nästa orderläggning.\n\n";
		}
		$extra = array("Content-type =>"text/plain: charset=\"UTF-8","From" => "$addcreatedby"); 
		mail($recipient, $subj, $text1, $extra);

	}
	
	function generate_pass ($pass_len)  {
		/*
		Input: $pass_len, Längd på lösenord
		Output: $nps, lösenord bestående av stora och små bokstäver och siffror
		
		*/
		$nps = ""; 

		// Seed the random number generator
		mt_srand ((double) microtime() * 1000000); 

		while (strlen($nps)<$pass_len) {
			
			// Ge $c ett värde från slumpmässigt valt ASCII värde
			// bara A-Z, a-z och siffror för att inte få några
			// skumma tecken
			$randvalue = mt_rand (48, 122);
			$c = chr($randvalue); 
			
			// Lägg till på $nps om det är i rätt format

			if (eregi("[a-z0-9]", $c)) {
				$nps = $nps.$c;
				// förenkla för kunden genom att bara använda små bokstäver
				$nps = strtolower($nps);
			}
			
		}

		return ($nps); 
	}
	
	
	function sql_inject_clean($str) {
		$str = str_ireplace("union", "", $str);
		$str = str_ireplace(";", "",  $str);
		$str = str_ireplace("--", "",  $str);
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
		return $str;
	}
	
}
?>

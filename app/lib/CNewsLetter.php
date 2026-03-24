<?php

include_once 'Db.php';

Class CNewsLetter {
	var $conn_ms; 
	var $conn_my; 
	var $conn_master;

	function __construct() {
		global $fi;
			
		$this->conn_my = Db::getConnection();
//		$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
//		@mssql_select_db ("cyberphoto", $this->conn_ms);
		
		// No database selected, use mysql_db_query or e.g: cyberorder.kund when querying 
		$this->conn_master = Db::getConnection(true);
		
	}

	function changeSubscription($email, $name, $boka) {
		global $fi, $sv, $mobilsite, $no;
		if ($fi && !$sv) {
			$lang = "fi";
			$cntry = "FI";
		} elseif ($fi && $sv) {
			$lang = "sv";
			$cntry = "FI";	
		} elseif ($no) {
			$lang = "no";
			$cntry = "NO";
		} elseif ($sv) {
			$lang = "sv";
			$cntry = "SE";
		} else {
			$lang = "sv";
			$cntry = "SE";		
		}	
		//echo "Här: <br>$email<br>$name<br>$boka";
		//return;	

		//exit;
		$retur = false;
		include ("CConnect_ms.php");
		
		$query = "";
		$datum = date("Y-m-d H:i:s");
		
		if ($boka == -1) {
			$query = "SELECT * FROM cyberorder.Nyhetsbrev WHERE email = '" . $email . "'";
			// $res = @mssql_query ($query, $this->conn_ms);
			$res = mysqli_query(Db::getConnection(false), $query);
			if (@mysqli_num_rows($res) > 0) {
				$query = "DELETE FROM cyberorder.Nyhetsbrev WHERE email = '" . $email . "'";
				// if (!@mssql_query ($query, $this->conn_ms))
					// return false;
				//echo $query;
				$res = mysqli_query(Db::getConnection(true), $query);
			}
			
			// $query = "INSERT INTO Nyhetsbrev (email, dateAdded, name) values ('" . $email . "', '" . $datum . "', '" . $name . "')";
			$query = "INSERT INTO cyberorder.Nyhetsbrev (email, name, dateAdded, cntry, lang) values (" .
						"'" . $email . "', '" . $name . "', '" . $datum  . "','" . $cntry . "', '" . $lang . "')";	
			// echo $query;
			
			$res = mysqli_query(Db::getConnection(true), $query);
			// if (!@mssql_query ($query, $this->conn_ms))
			// if (!@mysqli_query(Db::getConnection(true), $query))
				// return false;
			//echo $query;
			
			$this->updateCustomer($email,-1);
			// return true;
			
			$query = "UPDATE cyberorder.Kund SET nyhetsbrev = -1 WHERE email = '" . $email . "'";
			$res = mysqli_query(Db::getConnection(true), $query);
			// if (@mssql_query ($query, $this->conn_ms))
				return true;
			//echo $query;
		} else {
			$query = "DELETE FROM cyberorder.Nyhetsbrev WHERE email = '" . $email . "'";
			$res = mysqli_query(Db::getConnection(true), $query);
			
			// if (!@mssql_query ($query, $this->conn_ms))
				// return false;
			//echo $query;
			
			$this->updateCustomer($email,0);
			// return true;

			$query = "UPDATE cyberorder.Kund SET nyhetsbrev = 0 WHERE email = '" . $email . "'";
			$res = mysqli_query(Db::getConnection(true), $query);
			
			// if (@mssql_query ($query, $this->conn_ms))
				// return true;
			//echo $query;
			return true;
		}
	}
	
	function updateCustomer($email,$boka) {
	
		$update = "UPDATE cyberorder.Kund SET nyhetsbrev=$boka WHERE email= '" . $email . "'";
		// echo $sql;
		// exit;
		// mysqli_query($this->conn_master, $update);
		$res = mysqli_query(Db::getConnection(true), $update);
		
	}


function mailNyhetsbrevBekrNew($mailAdress) {
global $sv, $fi, $no, $frameless;

if ($fi && !$sv) {
	$headtitle = "Uutiskirjeen Tilaus - CyberPhoto";
	if ($frameless) {
		$linc = "<a href=\"http://www.cyberphoto.fi/varaa-uutiskirje?mail=$mailAdress&boka=0\">http://www.cyberphoto.fi/boka-nyhetsbrev</a>";
	} else {
		$linc = "<a href=\"http://www.cyberphoto.fi/index_fi.php?/faq/uutiskirje.php?mail=$mailAdress&boka=0&loc=FI_fi\">http://www.cyberphoto.fi/faq/uutiskirje.php</a>";
	}
	$text1 = "Tervetuloa CyberPhoto:n uutiskirjeen tilaajana!";
	$text2 = "Jos et ole tilannut tätä uutiskirjettä niin voit peruuttaa tilaus klikkaamalla linkkiä alla";
	$text3 = "Ystävällisin terveisin";
	$text4 = "CyberPhoto";
} elseif ($no) {
	$headtitle = "Bestilling nyhetsbrev - CyberPhoto";
	if ($frameless) {
		$linc = "<a href=\"http://www.cyberphoto.no/boka-nyhetsbrev?mail=$mailAdress&boka=0\">http://www.cyberphoto.no/boka-nyhetsbrev</a>";
	} else {
		$linc = "<a href=\"http://www.cyberphoto.no/faq/nyhetsbrev.php?mail=$mailAdress&boka=0\">http://www.cyberphoto.no/faq/nyhetsbrev.php</a>";
	}
	$text1 = "Velkommen som abonnent på CyberPhoto nyhetsbrev!";
	$text2 = "Om du ikke har gjort denne bestillingen kan du kansellere den ved å klikke på lenken under.";
	$text3 = "Med vennlig hilsen";
	$text4 = "CyberPhoto";
} else {
	$headtitle = "Beställning nyhetsbrev - CyberPhoto";
	if ($frameless) {
		$linc = "<a href=\"http://www.cyberphoto.se/boka-nyhetsbrev?mail=$mailAdress&boka=0\">http://www.cyberphoto.se/boka-nyhetsbrev</a>";
	} else {
		$linc = "<a href=\"http://www.cyberphoto.se/faq/nyhetsbrev.php?mail=$mailAdress&boka=0\">http://www.cyberphoto.se/faq/nyhetsbrev.php</a>";
	}
	$text1 = "Välkommen som prenumerant av CyberPhoto nyhetsbrev!";
	$text2 = "Om du inte har gjort denna beställning kan du avboka den genom att klicka på länken nedan.";
	$text3 = "Med vänliga hälsningar";
	$text4 = "CyberPhoto";
}

$text = <<<eof
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$headtitle</title>
</head>

<body>

<p>
<img border="0" src="http://www.cyberphoto.se/faq/logo_news_mobile.png" width="300" height="53"></p>
<p>$text1</p>
<p>$text2</p>
<p>$linc</p>
<p>$text3</p>
<p>$text4</p>

</body>

</html>
eof;


	require("class.phpmailer.php");

	$mail = new phpmailer();

	$mail->Host     = "81.8.240.27";
	//$mail->Mailer   = "smtp";

	$mail->AddAddress ($mailAdress, $mailNamn);
	$mail->Body = $text;
	//$mail->AltBody = $messageText;
	
	if ($fi && !$sv) {
		$mail->From     = "uutiskirje@cyberphoto.fi";
		$mail->FromName = "Uutiskirje CyberPhoto";
		$mail->Subject 	= "Uutiskirjeen Tilaus";
	} else {
		$mail->From     = "nyhetsbrev@cyberphoto.se";
		$mail->FromName = "Nyhetsbrev CyberPhoto";
		$mail->Subject 	= "Beställning av nyhetsbrev";
	}
	
	$mail->IsHTML(true);
	$mail->IsSMTP(true);



	// $mail->AddEmbeddedImage ("/home/www/faq/nyhetshuvud.jpg", "nyhetshuvud.jpg", "nyhetshuvud.jpg", "base64", "image/gif");
	
	// $mail->AddEmbeddedImage ("/home/www/faq/nyhetsavslut.jpg", "nyhetsavslut.jpg", "nyhetsavslut.jpg", "base64", "image/gif");
	if(!$mail->Send())
		echo "Orderbekräftelse kunden inte mailas iväg";
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}

function mailNyhetsbrevAvbNew($mailAdress) {
global $sv, $fi, $no, $frameless;

if ($fi && !$sv) {
	$headtitle = "Peruutus Uutiskirje - CybeerPhoto";
	if ($frameless) {
		$linc = "<a href=\"http://www.cyberphoto.fi/varaa-uutiskirje?mail=$mailAdress&boka=-1\">http://www.cyberphoto.fi/boka-nyhetsbrev</a>";
	} else {
		$linc = "<a href=\"http://www.cyberphoto.fi/index_fi.php?/faq/uutiskirje.php?mail=$mailAdress&boka=-1&loc=FI_fi\">http://www.cyberphoto.fi/faq/uutiskirje.php</a>";
	}
	$text1 = "Kiitos että olet ollut tilaajana CyberPhoto:n uutiskirjeeseen!";
	$text2 = "Jos halua ilmoittaa itsesi tilaajaksi taas voit tehdä sen alla olevalla linkillä.";
	$text3 = "Ystävällisin terveisin";
	$text4 = "CyberPhoto";
} elseif ($no) {
	$headtitle = "Avbestilling nyhetsbrev - CyberPhoto";
	if ($frameless) {
		$linc = "<a href=\"http://www.cyberphoto.no/boka-nyhetsbrev?mail=$mailAdress&boka=-1\">http://www.cyberphoto.no/boka-nyhetsbrev</a>";
	} else {
		$linc = "<a href=\"http://www.cyberphoto.no/faq/nyhetsbrev.php?mail=$mailAdress&boka=-1\">http://www.cyberphoto.no/faq/nyhetsbrev.php</a>";
	}
	$text1 = "Takk for at du har abonnert på CyberPhotos nyhetsbrev!";
	$text2 = "Om du vil melde deg på igjen kan du gjøre det på lenken under.";
	$text3 = "Med vennlig hilsen";
	$text4 = "CyberPhoto";
} else {
	$headtitle = "Avbeställning nyhetsbrev - CyberPhoto";
	if ($frameless) {
		$linc = "<a href=\"http://www.cyberphoto.se/boka-nyhetsbrev?mail=$mailAdress&boka=-1\">http://www.cyberphoto.se/boka-nyhetsbrev</a>";
	} else {
		$linc = "<a href=\"http://www.cyberphoto.se/faq/nyhetsbrev.php?mail=$mailAdress&boka=-1\">http://www.cyberphoto.se/faq/nyhetsbrev.php</a>";
	}
	$text1 = "Tack för att du varit prenumerant på CyberPhoto nyhetsbrev!";
	$text2 = "Om du vill anmäla dig igen kan du göra det på nedanstående länk.";
	$text3 = "Med vänliga hälsningar";
	$text4 = "CyberPhoto";
}

$text = <<<eof
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$headtitle</title>
</head>

<body>

<p>
<img border="0" src="http://www.cyberphoto.se/faq/logo_news_mobile.png" width="300" height="53"></p>
<p>$text1</p>
<p>$text2</p>
<p>$linc</p>
<p>$text3</p>
<p>$text4</p>

</body>

</html>
eof;


	require("class.phpmailer.php");

	$mail = new phpmailer();

	$mail->From     = "nyhetsbrev@cyberphoto.se";
	$mail->FromName = "Nyhetsbrev CyberPhoto";
	$mail->Host     = "81.8.240.27";
	//$mail->Mailer   = "smtp";

	$mail->AddAddress ($mailAdress, $mailNamn);
	$mail->Body = $text;
	//$mail->AltBody = $messageText;
	
	if ($fi && !$sv) {
		$mail->From     = "uutiskirje@cyberphoto.fi";
		$mail->FromName = "Uutiskirje CyberPhoto";
		$mail->Subject 	= "Peruutus Uutiskirje";
	} else {
		$mail->From     = "nyhetsbrev@cyberphoto.se";
		$mail->FromName = "Nyhetsbrev CyberPhoto";
		$mail->Subject 	= "Avbeställning av nyhetsbrev";
	}
	
	$mail->IsHTML(true);
	$mail->IsSMTP(true);



	// $mail->AddEmbeddedImage ("/home/www/faq/nyhetshuvud.jpg", "nyhetshuvud.jpg", "nyhetshuvud.jpg", "base64", "image/gif");
	
	// $mail->AddEmbeddedImage ("/home/www/faq/nyhetsavslut.jpg", "nyhetsavslut.jpg", "nyhetsavslut.jpg", "base64", "image/gif");
	if(!$mail->Send())
		echo "Orderbekräftelse kunden inte mailas iväg";
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}
	
}

?>

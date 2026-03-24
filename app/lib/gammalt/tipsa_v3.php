<?php
exit;
$artnr = $article;

$conn_my = Db::getConnection();
$conn_my2 = Db::getConnection(true);

$select = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris, tillverkare, no_buy, link, ";
$select .= "lagersaldo, bestallt, bestallningsgrans, lev_datum_normal, Moms.momssats, bild, betyg ";
$select .= "FROM Artiklar ";
$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
$select .= "INNER JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
if ($fi) {
$select .= "WHERE Artiklar_fi.ej_med_fi=0 && Artiklar.artnr='$artnr' ";
} else {
$select .= "WHERE Artiklar.ej_med=0 && Artiklar.artnr='$artnr' ";
}

$res = @mysqli_query($conn_my, $select);

$row = @mysqli_fetch_object($res);
if (!(mysqli_num_rows($res)>0))
	exit;
	
if ($row->betyg < 21 AND $row->betyg > 0 ) {
	$select = "SELECT link as betygLink FROM Betyg WHERE betygId = " . $row->betyg;
	$res = mysqli_query($conn_my, $select);
	if (mysqli_num_rows($res)>0)
		extract(mysqli_fetch_array($res));

}
if ($fi && !$sv) {
	$betygLink = ereg_replace(".gif$", "_fi.gif", $betygLink);
}

if ($row->tillverkare != ".") 
	if (!$sv) {
		$beskrivning = $row->tillverkare . " " . $row->beskrivning_fi . " " . $row->kommentar_fi;
	} else {
		$beskrivning = $row->tillverkare . " " . $row->beskrivning . "<br>" . $row->kommentar;
	}
$visabild = $row->bild;


if ($send) {
	// testa mailadresserna: 
	if ( !(     (ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$",$receiverMail ))))  {
		if ($fi && !$sv) {
			$extra = "<p><b><font face=\"Arial\" size=\"3\" color=\"#85000D\">Ystävällisesti tarkista sähköpostiosoite</font></b><p>\n";	
		} else {
			$extra = "<p><b><font face=\"Arial\" size=\"3\" color=\"#85000D\">Vänligen ange korrekt e-postadress</font></b><p>\n";	
		}
		$send = false;	
		
	}
	
	if ( !(     (ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$",$senderMail ))))  {
		if ($fi && !$sv) {
			$extra = "<p><b><font face=\"Arial\" size=\"3\" color=\"#85000D\">Ystävällisesti tarkista sähköpostiosoite</font></b><p>\n";	
		} else {
			$extra = "<p><b><font face=\"Arial\" size=\"3\" color=\"#85000D\">Vänligen ange korrekt e-postadress</font></b><p>\n";	
		}
		$send = false;	
		
	
	} 
	

}
if (!$send) { 

?>
<html>
<head>
<title><?php if ($fi && !$sv) : ?>Vihjaa!<?php else: ?>Tipsa!<?php endif; ?></title>
<?php include ("analyticsNew.php"); ?>
</head>
<body>
<?php echo $extra; ?>

<form method="get">
<input type="hidden" name="article" value="<?php echo $article; ?>">
<input type="hidden" name="send" value="true">
<table border="0" cellpadding="2" width="98%" style="border-collapse: collapse; background-image: url('/thumbs/xlarge/bilder/<?php echo $visabild; ?>'); background-repeat: no-repeat; background-position: right top">
  <tr>
    <td><b><font face="Arial"><?php if ($fi && !$sv) : ?>Lähetä vihje kaverille!<?php else: ?>Skicka tips till en vän!<?php endif; ?></font></b></td>
  </tr>
  <tr>
    <td><font face="Verdana" size="1">&nbsp;</font></td>
  </tr>
  <tr>
    <td><b><font face="Verdana" size="1"><?php echo $beskrivning; ?></font></b></td>
  </tr>
  <tr>
    <td><font face="Verdana" size="1">&nbsp;</font></td>
  </tr>
  <tr>
    <td><font face="Verdana" size="1"><?php if ($fi && !$sv) : ?>Sähköpostiosoiteesi<?php else: ?>Din e-postadress:<?php endif; ?></font></td>
  </tr>
  <tr>
    <td><font face="Verdana">
    <input type="text" name="senderMail" size="40" style="font-family: verdana; font-size: 10px" value="<?php echo $senderMail;  ?>"></font></td>
  </tr>
  <tr>
    <td><font face="Verdana" size="1"><?php if ($fi && !$sv) : ?>Vastaanottajan sähköposti<?php else: ?>Mottagarens e-postadress:<?php endif; ?></font></td>
  </tr>
  <tr>
    <td><font face="Verdana">
    <input type="text" name="receiverMail" size="40" style="font-family: verdana; font-size: 10px" value="<?php echo $receiverMail;  ?>"></font></td>
  </tr>
  <tr>
    <td><font face="Verdana" size="1"><?php if ($fi && !$sv) : ?>Viesti<?php else: ?>Meddelande<?php endif; ?></font></td>
  </tr>
  <tr>
    <td><font face="Verdana">
    <textarea rows="5" name="mess" cols="46" style="font-family: verdana; font-size: 10px"><?php echo $mess;  ?></textarea></font></td>
  </tr>
  <tr>
    <td>
    <p align="left"><font face="Verdana">
    <input type="submit" value="<?php if ($fi && !$sv) : ?>Lähetä vihje<?php else: ?>Skicka tips<?php endif; ?>" style="font-size: 8pt; font-weight: bold; font-family: Verdana"></font>
    </td>
  </tr>
  </table>

</form>
</body>
</html>
<?php } else { 

	$regcomment = $mess;
	
	//echo renderHtmlMail($row, $beskrivning, $betygLink, "nils@cyberphoto.se");
	$messageHtml = renderHtmlMail($row, $beskrivning, $betygLink, $senderMail, $mess);
	if (sendMail($row, $senderMail, $receiverMail, $messageHtml, "", $conn_ms, $artnr)) { 
	//exit;
	?>
	<script language="javascript">
		document.location.replace("sent.php?res=true&fi=<?php echo $fi; ?>&sv=<?php echo $sv; ?>");
	</script>

	<?php } else { ?>
	
	<script language="javascript">
		document.location.replace("sent.php?res=false&fi=<?php echo $fi; ?>&sv=<?php echo $sv; ?>");
	</script>
	<?php } 

 } 

function renderHtmlMail($row, $beskrivning, $betygLink, $tipSrc, $messSrc) {
	global $fi,$sv;
	ob_start();        
	if ($fi && !$sv) {
		include ("./base/tipsa_email_fi.htm"); 
	} elseif ($fi && $sv) {
		include ("./base/tipsa_email_fi_se.htm"); 
	} else {
		include ("./base/tipsa_email.htm"); 
	}
	$html = ob_get_contents();    
	ob_end_clean();
	$html = ereg_replace("beskrSrc", $beskrivning, $html);

	if ($row->link != "") {
		$link = "http://www.cyberphoto.se/?" . $row->link;
	} else {
		if ($fi && !$sv) {
			$link = "http://www.cyberphoto.fi/info_fi.php?article=" . $row->artnr;	
		} elseif ($fi && $sv) {
			$link = "http://www.cyberphoto.fi/info_fi_se.php?article=" . $row->artnr;	
		} else {
			$link = "http://www.cyberphoto.se/info.php?article=" . $row->artnr;	
		}
	}
	if ($row->bild != "") {
		$bild = ereg_replace("^../", "", $row->bild);

		if ($betygLink != "") {
			if ($fi && !$sv) {
				$html = ereg_replace("bildSrc", "<a href=\"" . $link . "\"><img src=\"http://www.cyberphoto.fi/" . $bild . "\" border=\"0\"></a>&nbsp;&nbsp;&nbsp;&nbsp;<img name=\"testbild\" border=\"0\" src=\"http://www.cyberphoto.fi" . $betygLink .  "\">", $html);
			} elseif ($sv && $fi) {
				$html = ereg_replace("bildSrc", "<a href=\"" . $link . "\"><img src=\"http://www.cyberphoto.fi/" . $bild . "\" border=\"0\"></a>&nbsp;&nbsp;&nbsp;&nbsp;<img name=\"testbild\" border=\"0\" src=\"http://www.cyberphoto.fi" . $betygLink .  "\">", $html);
			} else {
				$html = ereg_replace("bildSrc", "<a href=\"" . $link . "\"><img src=\"http://www.cyberphoto.se/" . $bild . "\" border=\"0\"></a>&nbsp;&nbsp;&nbsp;&nbsp;<img name=\"testbild\" border=\"0\" src=\"http://www.cyberphoto.se" . $betygLink .  "\">", $html);
			}
			
		} else {
			$html = ereg_replace("bildSrc", "<a href=\"" . $link . "\"><img src=\"http://www.cyberphoto.se/" . $bild . "\" border=\"0\"></a>", $html);	
			echo "här";
		}
        
       
		
	} else {
		$html = ereg_replace("bildSrc", "", $html);
		
	}

	
	$link = "<a href=\"" . $link . "\">" . $link . "</a>";
	$html = ereg_replace("linkSrc", $link, $html);
	$html = ereg_replace("tipSrc", $tipSrc, $html);
	$html = ereg_replace("messSrc", $messSrc, $html);

	return $html;
}

function sendMail($row, $from, $to, $messageHtml, $messageText, $conn_ms, $artnr) {
	global $regcomment,$fi,$sv,$conn_my2;
	require("class.phpmailer.php");

	$mail = new phpmailer();	
	
	$mail->From     = $from;
	$mail->FromName = "";
	$mail->Host     = "81.8.240.27";
	//$mail->Mailer   = "smtp";

	$mail->AddAddress ($to);
	$mail->Body = $messageHtml;
	$mail->AltBody = $messageText;

	if ($fi && !$sv) {
		$mail->Subject = $from . " lähetti vihjeen";	
	} else {
		$mail->Subject = "Produkttips från " . $from;	
	}

	$mail->IsHTML(true);
	$mail->IsSMTP(true);
	//$mail->AddEmbeddedImage ("/home/www/dotsv.gif", "dotsv.gif", "dotsv.gif", "base64", "image/gif");

	if($mail->Send()) {
		$date = date("Y-m-d H:i:s");
		$logIP = $_SERVER['REMOTE_ADDR'];
		//echo $date;
		if ($fi && !$sv) {
			// $updt = "INSERT INTO log_web (dat, webpage, comment, artnr) values ('" . $date . "', 'tips_fi', '" . $regcomment . "', '" . $artnr . "')";
			$updt = "INSERT INTO logWeb (logDate, logPage, logComment, logArtnr, logIP) values ('" . $date . "', 'tips_fi', '" . $regcomment . "', '" . $artnr . "', '$logIP')";
		} elseif ($fi && $sv) {
			// $updt = "INSERT INTO log_web (dat, webpage, comment, artnr) values ('" . $date . "', 'tips_fi_se', '" . $regcomment . "', '" . $artnr . "')";
			$updt = "INSERT INTO logWeb (logDate, logPage, logComment, logArtnr, logIP) values ('" . $date . "', 'tips_fi_se', '" . $regcomment . "', '" . $artnr . "', '$logIP')";
		} else {
			// $updt = "INSERT INTO log_web (dat, webpage, comment, artnr) values ('" . $date . "', 'tips_se', '" . $regcomment . "', '" . $artnr . "')";
			$updt = "INSERT INTO logWeb (logDate, logPage, logComment, logArtnr, logIP) values ('" . $date . "', 'tips_se', '" . $regcomment . "', '" . $artnr . "', '$logIP')";
		}
		//echo $updt . "<br>";
		@mysqli_query($conn_my2, $updt);
		return true;
	} else {
		return false;
	}	

}










?>

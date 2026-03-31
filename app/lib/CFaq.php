<?php

include("connections.php");

Class CFaq {

	var $conn_my; 
    var $conn_my2;

function __construct() {
	global $fi;
	
    $this->conn_my2 = Db::getConnection(true);
	$this->conn_my = Db::getConnection();

}

function getPrioFaqKat($prio,$lang,$kat) {
	
	$select = "SELECT * FROM (SELECT cntr, q, a, link FROM faq WHERE kategori_id IN ($kat) AND active = -1 AND prio = '" . $prio . "' AND  lang = '" . $lang . "' LIMIT 40) AS tmp ORDER BY RAND() LIMIT 7 ";
	// echo $select;
	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > 0) {
		// echo "jo!";
	}
	while ($row = mysqli_fetch_object($res)) {
	
	/**
	if ($row->link != "") {
		$row->link = $row->link;
		} else {
		$row->link = "contact.php?ID=" . $row->cntr;
		}
	*/
	$row->link = "service_support.php?ID=" . $row->cntr;

	if (strlen($row->a) >= 50)
		$row->a = substr ($row->a, 0, 50) . "...";
	
?>
	<hr noshade color="#999999" size="1">
	<a style="text-decoration: none" href="<?php echo $row->link ?>">
	<b><font face="Verdana" size="1" color="#000000"><?php echo $row->q; ?></b><br>
	<font face="Verdana" size="1" color="#999999"><font face="Verdana" size="1"><?php echo $row->a; ?><br><img border="0" src="pic/link_small.gif">&nbsp;<b><?php if ($fi && !$sv): ?>Lue lisää<?php else: ?>Läs mer<?php endif; ?></b></font></a>

	
<?php
	}
	
}

function getPrioFaq($prio) {
	global $sv, $fi, $frameless;

	if ($frameless) {
		if ($fi && !$sv) {
			$currentUrl = "asiakaspalvelu";
		} elseif ($no) {
			$currentUrl = "kundeservice";
		} else {
			$currentUrl = "kundservice";
		}
	}
	
	$select = "SELECT * FROM (SELECT cntr, q, a, link FROM faq WHERE active = -1 AND prio = '" . $prio . "' ";
	
	if ($fi && !$sv) {
	
		$select .= "AND  lang = 'fi' ";
	
	} elseif ($fi && $sv) {

		$select .= "AND  lang = 'sv' ";
		
	} else {
	
		$select .= "AND  lang = 'sv' ";
	
	}

	$select .= "LIMIT 40) AS tmp ORDER BY RAND() LIMIT 5 ";

	// echo $select;
	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > 0) {
		// echo "jo!";
	}
	while ($row = mysqli_fetch_object($res)) {
	
	/**
	if ($row->link != "") {
		$row->link = $row->link;
		} else {
		$row->link = "contact.php?ID=" . $row->cntr;
		}
	*/
	if ($currentUrl != "") {
		$row->link = $currentUrl . "?ID=" . $row->cntr;
	} elseif ($fi && !$sv) {
		// $row->link = "contact_fi.php?ID=" . $row->cntr;
		$row->link = $_SERVER['PHP_SELF'] . "?ID=" . $row->cntr;
	} elseif ($fi && $sv) {
		// $row->link = "contact_fi_se.php?ID=" . $row->cntr;
		$row->link = $_SERVER['PHP_SELF'] . "?ID=" . $row->cntr;
	} else {
		// $row->link = "contact.php?ID=" . $row->cntr;
		$row->link = $_SERVER['PHP_SELF'] . "?ID=" . $row->cntr;
	}

	if (strlen($row->a) >= 50)
		$row->a = substr ($row->a, 0, 50) . "...";
	
?>
	<hr noshade color="#999999" size="1">
	<a style="text-decoration: none" href="<?php echo $row->link ?>">
	<b><font face="Verdana" size="1" color="#000000"><?php echo $row->q; ?></b><br>
	<font face="Verdana" size="1" color="#999999"><font face="Verdana" size="1"><?php echo $row->a; ?><br><img border="0" src="pic/link_small.gif">&nbsp;<b><?php if ($fi && !$sv): ?>Lue lisää<?php else: ?>Läs mer<?php endif; ?></b></font></a>

	
<?php
	}
	
}

function showFaq($ID) {
	global $sv, $fi;
	
	$select = "SELECT cntr, q, a, link FROM faq WHERE active = -1 ";
	
	if ($fi && !$sv) {

		$select .= "AND  lang = 'fi' ";

	} elseif ($fi && $sv) {

		$select .= "AND  lang = 'sv' ";

	} else {

		$select .= "AND  lang = 'sv' ";

	}

	$select .= "AND cntr = '" . $ID . "' ";

	// echo $select;
	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > 0) {
		// echo "jo!";
	}
	while ($row = mysqli_fetch_object($res)) {

	$row->a = eregi_replace("\n", "<br>", $row->a);

	if ($row->link != "") {
		if (eregi("http", $row->link)) {
		$row->link = "<a style='text-decoration: none' target='_blank' href=$row->link>";
		} else {
		$row->link = "<a style='text-decoration: none' href=$row->link>";
		}
	}
	
?>

	<table border="0" cellpadding="5" cellspacing="0" width="95%">
	  <tr>
	    <td width="100%" style="border: 1px solid #008080; background-color: #FFFF99">
	    <b><font face="Verdana" size="1" color="#000000"><?php echo $row->q; ?></b><br><br>
	    <font face="Verdana" size="1" color="#000000"><?php echo $row->a; ?></font>
	    <?php
		if ($row->link != "") {
	    ?>

	    <br><br>
	    <table border="0" cellpadding="0" cellspacing="0">
	      <tr>
	        <td width="30"><?php echo $row->link ?><img border="0" src="pic/link.gif"></a></td>
	        <td width="75"><font face="Verdana" size="1" color="#000000"><b><?php echo $row->link ?><?php if ($fi): ?>Klicka här<?php else: ?>Klicka här<?php endif; ?></a></b></font></td>
	      </tr>
	    </table>

	    <?php } ?>
	    </td>
	  </tr>
	</table>

<?php
	}
	
}

function searchFaq($question) {
	global $sv, $fi;
	
	if ($question == "Sök här") { ?>
	
	<table border="0" cellpadding="5" cellspacing="0" width="95%">
	  <tr>
	    <td width="100%" style="border: 1px solid #008080; background-color: #EBEBEB">
	    <font face="Verdana" size="1" color="#000000">
	    <b>OBS! Du måste ange minst ett sökord!</b></td>
	  </tr>
	</table>
	
	<?php
	} elseif ($question != "") {
	?>

	<table border="0" cellpadding="5" cellspacing="0" width="95%">
	  <tr>
	    <td width="100%" style="border: 1px solid #008080; background-color: #EBEBEB">
	    <font face="Verdana" size="1" color="#000000">
	    <?php if ($fi && !$sv) { ?>
	    Tulos etsinnälle <b><?php echo $question; ?></b> näkyy oikealla.
	    <?php } else { ?>
	    Resultat av din sökning på <b><?php echo $question; ?></b> ser du ute till höger.
	    <?php } ?>
	    </td>
	  </tr>
	</table>

<?php
	}
}


function getResultFaq($question) {
	global $sv, $fi, $searchlogg, $frameless;
	
	
	if ($frameless) {
		if ($fi && !$sv) {
			$currentUrl = "asiakaspalvelu";
		} elseif ($no) {
			$currentUrl = "kundeservice";
		} else {
			$currentUrl = "kundservice";
		}
	}
	if ($question != "") {
		$searchwords = preg_split("/[\s]+/", $question);
		}

	$select = "SELECT cntr, q, a, link FROM faq WHERE active = -1 ";

	if ($fi && !$sv) {

		$select .= "AND lang = 'fi' ";

	} elseif ($fi && $sv) {

		$select .= "AND lang = 'sv' ";

	} else {

		$select .= "AND lang = 'sv' ";

	}

	$select .= "AND ( ";

	// $select .= "q like '%" . $question . "%' OR a like '%" . $question . "%' ";


	for ($i = 0; $i < count($searchwords);$i++) {
			if ($i == 0) {
			$select .= "q like '%" . $searchwords[$i] . "%' OR a like '%" . $searchwords[$i] . "%' ";
			} else {
			$select .= "OR q like '%" . $searchwords[$i] . "%' OR a like '%" . $searchwords[$i] . "%' ";
			}
	}

	$select .= ") ";

	// echo $select;
	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_object($res)) {
		
		if ($currentUrl != "") {
			$row->link = $currentUrl . "?ID=" . $row->cntr . "&question=$question";
		} elseif ($fi && !$sv) {
			// $row->link = "contact_fi.php?ID=" . $row->cntr . "&question=$question";
			$row->link = $_SERVER['PHP_SELF'] . "?ID=" . $row->cntr . "&question=$question";
		} elseif ($fi && $sv) {
			// $row->link = "contact_fi_se.php?ID=" . $row->cntr . "&question=$question";
			$row->link = $_SERVER['PHP_SELF'] . "?ID=" . $row->cntr . "&question=$question";
		} else {
			// $row->link = "contact.php?ID=" . $row->cntr . "&question=$question";
			$row->link = $_SERVER['PHP_SELF'] . "?ID=" . $row->cntr . "&question=$question";
		}
	
		if (strlen($row->a) >= 175)
			$row->a = substr ($row->a, 0, 175) . "...";
		
		for ($i = 0; $i < count($searchwords);$i++) {
		$row->q = eregi_replace("$searchwords[$i]", "<span style=\"background-color: #FFFF00\">$searchwords[$i]</span>", $row->q);
		$row->a = eregi_replace("$searchwords[$i]", "<span style=\"background-color: #FFFF00\">$searchwords[$i]</span>", $row->a);
		}

		// $row->q = eregi_replace("$question", "<span style=\"background-color: #FFFF00\">$question</span>", $row->q);
		// $row->a = eregi_replace("$question", "<span style=\"background-color: #FFFF00\">$question</span>", $row->a);
		
		?>	
		<hr noshade color="#999999" size="1">
		<a style="text-decoration: none" href="<?php echo $row->link ?>">
		<b><font face="Verdana" size="1" color="#000000"><?php echo $row->q; ?></b><br>
		<font face="Verdana" size="1" color="#999999"><font face="Verdana" size="1"><?php echo $row->a; ?><br><img border="0" src="pic/link_small.gif">&nbsp;<b><?php if ($fi && !$sv): ?>Lue lisää<?php else: ?>Läs mer<?php endif; ?></b></font></a>
	
	<?php
		}

	} else {
	
	?>

	<br><br><font face="Verdana" size="1" color="#000000">
	<?php if ($fi && !$sv) { ?>
	Valitettavasti kysymys ei ymmärretty<br><br><b>"<?php echo $question; ?>"</b><br><br>
	Kokeile toinen sana<br><br>
	<?php } else { ?>
	Tyvärr förstod jag inte frågan<br><br><b>"<?php echo $question; ?>"</b><br><br>
	Prova annan sökfras<br><br>
	<?php } ?>
	<hr noshade color="#999999" width="100%" size="1"><br>
	<a style="text-decoration: none" href="<?php if ($fi): ?>send_question_fi.php<?php else: ?>send_question.php<?php endif; ?>">
	<?php if ($fi && !$sv) { ?>
	Etkö löydä etsimääsi vastausta? Klikkaa tästä lähettääksesi kysymyksen meille.</a></font>
	<?php } else { ?>
	Hittar du ändå inte det du söker kan du skicka oss en fråga genom att klicka här.</a></font>
	<?php } ?>
	
	<?php
	
		if ($sv && !$fi) {
			// $searchlogg->loggSearch_v2($_SERVER['REMOTE_ADDR'],$question,1);
		}

	}
	
}

function sendMess($subj, $text, $senderMail, $namn, $recipient, $type, $telefon, $ordernr, $produkt) {
	global $fi, $sv, $no;

	if ($fi) {
		$recipient .= "@cyberphoto.fi";
	} elseif ($no) {
		$recipient .= "@cyberphoto.no";
	} else {
		$recipient .= "@cyberphoto.se";
	}
	
	if ($subj == "1") {
		if ($fi && !$sv) {
			$subj = "Yleiset kysymykset";
		} else {
			$subj = "Allmänna frågor";
		}
	} elseif ($subj == "2") {
		if ($fi && !$sv) {
			$subj = "Ekonomia / Laskutus / Osamaksut";
		} else {
			$subj = "Ekonomi / Faktura / Avbetalning";
		}
	} elseif ($subj == "3") {
		if ($fi && !$sv) {
			$subj = "Kotisivu (vika tai suositus)";
		} else {
			$subj = "Hemsida (fel eller förslag)";
		}
	} elseif ($subj == "4") {
		if ($fi && !$sv) {
			$subj = "Tilaus (jo laitettu tilaus)";
		} else {
			$subj = "Redan lagd order";
		}
	} elseif ($subj == "5") {
		if ($fi && !$sv) {
			$subj = "Tuote";
		} else {
			$subj = "Produktfråga";
		}
	} elseif ($subj == "6") {
		if ($fi && !$sv) {
			$subj = "Huolto / Palautus";
		} else {
			$subj = "Service / returärende";
		}
	} elseif ($subj == "7") {
		if ($fi && !$sv) {
			$subj = "Mobilabonnemang";
		} else {
			$subj = "Mobilabonnemang";
		}
	} elseif ($subj == "4680") {
		$subj = "Buggrapport";
	} else {
		if ($fi && !$sv) {
			$subj = "Muut";
		} else {
			$subj = "Annat";
		}
	}
	
	if ($produkt == "1") {
		if ($fi && !$sv) {
			$subj .= " - Digitaalikamera";
		} else {
			$subj .= " - Digitalkamera";
		}
	} elseif ($produkt == "2") {
		if ($fi && !$sv) {
			$subj .= " - Matkapuhelin";
		} else {
			$subj .= " - Mobiltelefon";
		}
	} elseif ($produkt == "3") {
		if ($fi && !$sv) {
			$subj .= " - Objektiivi";
		} else {
			$subj .= " - Objektiv";
		}
	} elseif ($produkt == "4") {
		if ($fi && !$sv) {
			$subj .= " - Skanneri";
		} else {
			$subj .= " - Skanner";
		}
	} elseif ($produkt == "5") {
		if ($fi && !$sv) {
			$subj .= " - Tulostin";
		} else {
			$subj .= " - Skrivare";
		}
	} elseif ($produkt == "6") {
		if ($fi && !$sv) {
			$subj .= " - Studio";
		} else {
			$subj .= " - Studio";
		}
	} elseif ($produkt == "7") {
		if ($fi && !$sv) {
			$subj .= " - Videokamera";
		} else {
			$subj .= " - Videokamera";
		}
	} elseif ($produkt == "8") {
		if ($fi && !$sv) {
			$subj .= " - Videoeditointi";
		} else {
			$subj .= " - Videoredigering";
		}
	} elseif ($produkt == "9") {
		if ($fi && !$sv) {
			$subj .= " - Muut tuotteet";
		} else {
			$subj .= " - Övriga produkter";
		}
	} else {
		$subj .= "";
	}

	$extra = "From: " . $namn . " <" . $senderMail . ">\n";
	//$extra .= "bcc:annons@cyberphoto.se\n";
	
	// lägg till namn och telefonnr till texten
	// $text1 = preg_replace("/\\\\/", "", $text) . "\n\nÖvrigt: \nNamn:\t\t$namn\nTelefon:\t$telefon\nRubrik:\t\t$type";
	if ($type != "") {
		$text1 .= "\n$type";
		$text1 .= "\n\n-------------------------------------------------";
	}
	$text1 .= "\n\n" . preg_replace("/\\\\/", "", $text);
	$text1 .= "\n\n-------------------------------------------------";
	if ($fi && !$sv) {
		$text1 .= "\n\nNimi:\t\t$namn\nPuhelin:\t$telefon";
	} else {
		$text1 .= "\n\nNamn:\t\t$namn\nTelefon:\t$telefon";
	}
	// $text1 .= preg_replace("/\\\\/", "", $text) . "\n\nNamn:\t\t$namn\nTelefon:\t$telefon\nRubrik:\t\t$type";
	
	if ($ordernr <> "") {
		if (eregi("ekonomi", $subj)) {
			if ($fi && !$sv) {
				$text1 .= "\nLaskunumero:\t$ordernr";
			} else {
				$text1 .= "\nFaktura nr:\t$ordernr";
			}
		} else {
			if ($fi && !$sv) {
				$text1 .= "\nTilausnumero:\t$ordernr";
			} else {
				$text1 .= "\nOrder nr:\t$ordernr";
			}
		}
	}
	
	// mail till oss
	SmtpMail::send($recipient, $subj, $text1, $extra);
	
	// spara meddelandet i en databas

	// kopia till kunden
	if ($fi) {
	$subjKopia = "autovastaus: " . $subj;
	} else {
	$subjKopia = "autosvar: " . $subj;
	}

if ($fi && !$sv) {
$textKopia = <<<eof
Kiitos kysymyksestäsi. Vastaamme normaalsiti kysymykseen yhden tai kahden vuorokauden sisällä.
 
Huom! Tämä on automaattinen vastausviesti ja et voi vastata tähän viestiin.
 
Kysymyksenne oli seuraava:

eof;
} else {
$textKopia = <<<eof
Tack för er förfrågan. Vi besvarar normalt er förfrågan inom ett till två dygn.
Vänligen skicka er förfrågan endast en gång. 

Obs! Detta är ett automatgenererat meddelande som du inte kan svara på. 

Er förfrågan ser ut som nedan: 

eof;
}
	$textKopia .= "\"" . preg_replace("/\\\\/", "", $text) . "\"";
	if ($fi) {
	$extraKopia = "From:autosvar@cyberphoto.fi\n";
	} else {
	$extraKopia = "From:autosvar@cyberphoto.se\n";
	}
	// $extraKopia .= "Reply-To: detta_meddelande_går_inte_att_svara_på@__.se";
	//if ($recipient == "ekonomi@cyberphoto.se")
	//	SmtpMail::send($senderMail, $subjKopia, $textKopia, $extraKopia);

	$this->saveMessage($subj, $text, $senderMail, $namn, $recipient, $type, $telefon, $ordernr);

}

function saveMessage($subj, $text, $sender, $namn, $recipient, $type, $telefon, $ordernr) {
	global $REMOTE_ADDR;
	sleep(3); // sleep 3 seconds to make it harder to hack
	$text = $this->cleanIt($text);
	$sender  = $this->cleanIt($sender);
	$namn   = $this->cleanIt($namn);

	$type	 = $this->cleanIt($type);
	$telefon	  = $this->cleanIt($telefon);
			
	$insert = "";
	$insert =  "INSERT INTO mail (subj, text, sender, namn, recipient, datum, type, ipnr, telefon, ordernr) values ";
	$insert .= "('". $subj . "', '" . $text . "', '" . $sender . "', '" . $namn . "', '" . $recipient;
	$insert .= "', '" . date("Y-m-d H:i:s") . "', '" . $type . "', '" . $REMOTE_ADDR . "', '" . $telefon . "', ";
	$insert .= "'" . $ordernr . "')";
	
	//@mysqli_query($this->conn_my2, $insert);
}

function cleanIt($str) {
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
	$str = str_ireplace("sleep", "", $str);
	
    //$str = str_ireplace("'", "''", $str);
    return $str;
}
function sendMessService($subj, $text, $senderMail, $namn, $recipient, $type, $telefon, $ordernr) {
	global $fi, $sv, $no, $new_adress, $sendtoemail;
	
	// $recipient = "sjabo";
	
	if ($fi) {
		$recipient .= "@cyberphoto.fi";
	} elseif ($no) {
		$recipient .= "@cyberphoto.no";
	} else {
		$recipient .= "@cyberphoto.se";
	}
	
	if ($subj == "1") {
		$subj = "Service ärende";
	} elseif ($subj == "2") {
		$subj = "Öppet köp (lämna tillbaka)";
	} elseif ($subj == "3") {
		$subj = "DOA (fungerade inte vid ankomst)";
	} else {
		$subj = "Annat";
	}
	

	$extra = "From: " . $namn . " <" . $senderMail . ">\n";
	//$extra .= "bcc:annons@cyberphoto.se\n";
	
	$text1 .= "\nProdukt:\t$type";
	// lägg till namn och telefonnr till texten
	// $text1 = preg_replace("/\\\\/", "", $text) . "\n\nÖvrigt: \nNamn:\t\t$namn\nTelefon:\t$telefon\nRubrik:\t\t$type";
	$text1 .= "\n\nBeskrivning:\t" . preg_replace("/\\\\/", "", $text);
	// $text1 .= "\n\nBeskrivning:\t" . preg_replace("/\\\\/", "", $text) . "\n\nNamn:\t\t$namn\nTelefon:\t$telefon";
	// $text1 .= preg_replace("/\\\\/", "", $text) . "\n\nNamn:\t\t$namn\nTelefon:\t$telefon\nVara:\t\t$type";
	
	$text1 .= "\n\n-------------------------------------------------";
	$text1 .= "\nOrder nr:\t$ordernr\n\nNamn:\t\t$namn\nTelefon:\t$telefon";
	if ($new_adress != "") {
		$text1 .= "\n\nNy adress:\t$new_adress";
	}
	if ($sendtoemail == "yes") {
		$text1 .= "\n\nJa tack, kunden vill ha handlingarna via e-post";
	}
	$text1 .= "\n-------------------------------------------------";
	
	// mail till oss
	SmtpMail::send($recipient, $subj, $text1, $extra);
	
	// spara meddelandet i en databas

	// kopia till kunden
	$subjKopia = "autosvar: " . $subj;

$textKopia = <<<eof
Tack för er anmälan!
Vi kommer behandla erat ärende snarast möjligt. Om vi har ytterligare frågor så kontaktar vi dig på angiven mailadress (alternativt på telefon).
Vänligen skicka er anmälan endast en gång. 

/ Serviceteamet

OBS! Detta är ett automatgenererat meddelande som du inte kan svara på. 

Er beskrivning ser ut som nedan: 

eof;

	$textKopia .= "\"" . preg_replace("/\\\\/", "", $text) . "\"";
	$extraKopia = "From:autosvar@cyberphoto.se\n";
	// $extraKopia .= "Reply-To: detta_meddelande_går_inte_att_svara_på@__.se";

	// SmtpMail::send($senderMail, $subjKopia, $textKopia, $extraKopia);  denna använder vi inte längre då vi kör via OTRS 101004

	$this->saveMessage($subj, $text, $senderMail, $namn, $recipient, $type, $telefon, $ordernr);

}

function showFaqList($kat,$prio) {
	global $sv, $fi;

	echo "<ul id=\"meny\">";
	
	$select = "SELECT cntr, q, prio, kategori_id FROM faq WHERE active = -1 ";
	
	if ($fi && !$sv) {
	
		$select .= "AND  lang = 'fi' ";
	
	} elseif ($fi && $sv) {

		$select .= "AND  lang = 'sv' ";
		
	} else {
	
		$select .= "AND  lang = 'sv' ";
	
	}

	if ($prio != "") {
		$select .= "AND prio = '" . $prio . "' ";
	}
	$select .= "AND kategori_id = '" . $kat . "' ";
	$select .= "ORDER BY prio ASC ";

	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > 0) {
	}
	while ($row = mysqli_fetch_object($res)) {
	
	if ($row->prio == 1111111) { // sätt ett när vi vill flagga prioriterade faq
		echo "<li><a class=\"toplistheading\" href=\"". $_SERVER['PHP_SELF'] . "?ID=" . $row->cntr ."&show=" . $row->kategori_id . "#area" . $row->kategori_id . "\">" . $row->q . "</a> <font color=\"red\">*</font></li>\n";
	} else {
		// echo "<li><a class=\"toplistheading\" href=\"". $_SERVER['PHP_SELF'] . "?ID=" . $row->cntr ."&show=" . $row->kategori_id . "#area" . $row->kategori_id . "\">" . $row->q . "</a></li>\n";
		echo "<li><a class=\"toplistheading\" href=\"". $_SERVER['PHP_SELF'] . "?ID=" . $row->cntr ."&show=" . $row->kategori_id . "&MF=vanliga#area" . $row->kategori_id . "\">" . $row->q . "</a></li>\n";
	}


	}
	
	echo "</ul>\n\n";
	
}

function showFaqDetail($ID) {
	global $sv, $fi;
	
	$select = "SELECT cntr, q, a, link FROM faq WHERE active = -1 ";
	
	if ($fi && !$sv) {

		$select .= "AND  lang = 'fi' ";

	} elseif ($fi && $sv) {

		$select .= "AND  lang = 'sv' ";

	} else {

		$select .= "AND  lang = 'sv' ";

	}

	$select .= "AND cntr = '" . $ID . "' ";

	// echo $select;
	$res = mysqli_query($this->conn_my, $select);
	if (mysqli_num_rows($res) > 0) {
		// echo "jo!";
	}
	while ($row = mysqli_fetch_object($res)) {

	$row->a = eregi_replace("\n", "<br>", $row->a);

	if ($row->link != "") {
		if (eregi("http", $row->link)) {
		$row->link = "<a style='text-decoration: none' target='_blank' href=$row->link>";
		} else {
		$row->link = "<a style='text-decoration: none' href=$row->link>";
		}
	}

        echo "<div id=\"toplistcontainer\">";
	echo "<div class=\"roundtop\">";
	echo "<div class=\"toplist1\"></div>";
	echo "<div class=\"toplist2\"></div>";
	echo "<div class=\"toplist3\"></div>";
	echo "<div class=\"toplist4\"></div>";
	echo "</div>";
	
	echo "<div class=\"contenttoplist\">";
	
	echo "<p class=\"abbrubrik\">" . $row->q . "</p>";
	echo "<p class=\"abbtext\">" . $row->a . "</p>";
	
	if ($row->link != "") {
		if ($fi && !$sv) {
			echo "<p class=\"abbtext\">" . $row->link . "<img border=\"0\" src=\"/pic/link.gif\">&nbsp;&nbsp;Klikkaa tästä saadaksesi lisätietoja</a></p>";
		} else {
			echo "<p class=\"abbtext\">" . $row->link . "<img border=\"0\" src=\"/pic/link.gif\">&nbsp;&nbsp;Klicka här för mer information</a></p>";
		}
	}

        echo "</div>";
	
	echo "<div class=\"roundbottom\">";
	echo "<div class=\"toplist4\"></div>";
	echo "<div class=\"toplist3\"></div>";
	echo "<div class=\"toplist2\"></div>";
	echo "<div class=\"toplist1\"></div>";
	echo "</div>";
	echo "</div>";
	
	}
	
}

function showFaqListSearch($q,$sv,$fi) {
	// global $sv, $fi;

        echo "<div id=\"systemkamcontainer\">";
	echo "<div class=\"roundtop\">";
	echo "<div class=\"sykam1\"></div>";
	echo "<div class=\"sykam2\"></div>";
	echo "<div class=\"sykam3\"></div>";
	echo "<div class=\"sykam4\"></div>";
	echo "</div>";
	
	echo "<div class=\"content22\">";

	
	$searchwords = preg_split("/[\s]+/", $q);

	echo "<ul id=\"meny_search\">";
	
	$select = "SELECT cntr, q, prio, kategori_id FROM faq WHERE ";

	$select .= "( ";

	for ($i = 0; $i < count($searchwords);$i++) {

			if ($i == 0) {
				// $select .= "tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
				$select .= "a LIKE '%" . $searchwords[$i] . "%' OR q LIKE '%" . $searchwords[$i] . "%') ";
			} else {
				// $select .= "AND (tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
				$select .= "AND (a LIKE '%" . $searchwords[$i] . "%' OR q LIKE '%" . $searchwords[$i] . "%') ";
			}
	}
	
	if ($fi && !$sv) {
	
		$select .= "AND  lang = 'fi' ";
		$link = "faq/faq_fi.php";
	
	} elseif ($fi && $sv) {

		$select .= "AND  lang = 'sv' ";
		$link = "faq/faq_fi_se.php";
		
	} else {
	
		$select .= "AND  lang = 'sv' ";
		// $link = "faq.php";
		$link = "kundservice.php";
	
	}

	$select .= "AND active = -1 ";
	$select .= "ORDER BY prio ASC ";
	
	// echo $select;

	$res = mysqli_query($this->conn_my, $select);
	
	$counterresult = 0;
	
	if (mysqli_num_rows($res) > 0) {
	}
	while ($row = mysqli_fetch_object($res)) {
	
	if ($row->prio == 1) {
		echo "<li><a class=\"toplistheading\" href=\"/$link?ID=" . $row->cntr ."&MF=vanliga&question=" . $q . "&show=" . $row->kategori_id . "#area" . $row->kategori_id . "\">" . $row->q . "</a></li>\n";
	} else {
		echo "<li><a class=\"toplistheading\" href=\"/$link?ID=" . $row->cntr ."&MF=vanliga&question=" . $q . "&show=" . $row->kategori_id . "#area" . $row->kategori_id . "\">" . $row->q . "</a></li>\n";
	}
	
	$counterresult++;

	}
	
	echo "</ul>\n\n";

	if ($fi && !$sv) {
		echo "<p class=\"searchheading\">Etsintä sanalla <b><font color=\"#85000D\">" . $q . "</font></b> antoi <b>" . $counterresult . " </b>kpl osumaa";
	} else {
		echo "<p class=\"searchheading\">Din sökning på <b><font color=\"#85000D\">" . $q . "</font></b> gav <b>" . $counterresult . " </b>träffar";
	}

        echo "</div>";
	
	echo "<div class=\"roundbottom\">";
	echo "<div class=\"sykam4\"></div>";
	echo "<div class=\"sykam3\"></div>";
	echo "<div class=\"sykam2\"></div>";
	echo "<div class=\"sykam1\"></div>";
	echo "</div>";
	echo "</div>";
	
}


}
?>

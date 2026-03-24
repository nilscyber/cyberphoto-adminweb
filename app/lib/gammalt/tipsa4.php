<%
$artnr = $article;
include("CConnect.php");
include("CConnect_ms.php");	

$select = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.kommentar, utpris, tillverkare, no_buy, link, ";
$select .= "lagersaldo, bestallt, lev_datum, bestallningsgrans, lev_datum_normal, Moms.momssats, bild, betyg ";
$select .= "FROM Artiklar, Tillverkare, Moms ";
$select .= "WHERE Artiklar.tillverkar_id=Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id ";
$select .= "AND Artiklar.ej_med=0 && Artiklar.artnr='$artnr' ";

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
if ($row->tillverkare != ".") 
	$beskrivning = $row->tillverkare . " " . $row->beskrivning . " " . $row->kommentar;
%>
<% 
if ($send) {
	// testa mailadresserna: 
	if ( !(     (ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$",$receiverMail ))))  {
		$extra = "<p><b><font face=\"Arial\" size=\"4\" color=\"#85000D\">Vänligen ange korrekta epostadresser</font></b><p>\n";	
		$send = false;	
		
	}
	
	if ( !(     (ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,5})$",$senderMail ))))  {
		$extra = "<p><b><font face=\"Arial\" size=\"4\" color=\"#85000D\">Vänligen ange korrekta epostadresser</font></b><p>\n";	
		$send = false;	
		
	
	} 
	

}
if (!$send) { 

%>
<html>
<head>
<title>Tipsa!</title>
</head>
<body>
<% echo $extra; %>
<b><font face="Arial">Skicka tips till en vän!</font></b><p><b>
<font face="Arial" size="2"> <% echo $beskrivning; %> </font></b></p>

<form method="get">
<input type="hidden" name="article" value="<% echo $article; %>">
<input type="hidden" name="send" value="true">
<table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber1">
  <tr>
    <td><font face="Verdana" size="1">Din e-postadress:</font></td>
  </tr>
  <tr>
    <td><font face="Verdana">
    <input type="text" name="senderMail" size="46" style="font-family: verdana; font-size: 10px" value="<% echo $senderMail;  %>"></font></td>
  </tr>
  <tr>
    <td><font face="Verdana" size="1">Mottagarens e-postadress:
    </font></td>
  </tr>
  <tr>
    <td><font face="Verdana">
    <input type="text" name="receiverMail" size="46" style="font-family: verdana; font-size: 10px" value="<% echo $receiverMail;  %>"></font></td>
  </tr>
  <tr>
    <td><font face="Verdana" size="1">Meddelande</font></td>
  </tr>
  <tr>
    <td><font face="Verdana">
    <textarea rows="5" name="mess" cols="46" style="font-family: verdana; font-size: 10px"><% echo $mess;  %></textarea></font></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
    <p align="center"><font face="Verdana">
    <input type="submit" value="skicka tips" style="font-weight: bold"></font></td>
  </tr>
  </table>

</form>
</body>
</html>
<% } else { 

	//echo renderHtmlMail($row, $beskrivning, $betygLink, "nils@cyberphoto.se");
	$messageHtml = renderHtmlMail($row, $beskrivning, $betygLink, $senderMail, $mess);
	if (sendMail($row, $senderMail, $receiverMail, $messageHtml, "", $conn_ms, $artnr)) { 
	//exit;
	%>
	<script language="javascript">
		document.location.replace("sent.php?res=true");
	</script>

	<% } else { %>
	
	<script language="javascript">
		document.location.replace("sent.php?res=false");
	</script>
	<% } 

 } 

function renderHtmlMail($row, $beskrivning, $betygLink, $tipSrc, $messSrc) {
	ob_start();        
	include ("./base/tipsa_email.htm"); 
	$html = ob_get_contents();    
	ob_end_clean();  	
	$html = ereg_replace("beskrSrc", $beskrivning, $html);

	if ($row->link != "") {
		$link = "http://www.cyberphoto.se/?" . $row->link;
	} else {
		$link = "http://www.cyberphoto.se/?info.php?article=" . $row->artnr;	
	}
	if ($row->bild != "") {
		$bild = ereg_replace("^../", "", $row->bild);

		if ($betygLink != "") {
			$html = ereg_replace("bildSrc", "<a href=\"" . $link . "\"><img src=\"http://www.cyberphoto.se/" . $bild . "\" border=\"0\"></a>&nbsp;&nbsp;&nbsp;&nbsp;<img name=\"testbild\" border=\"0\" src=\"http://www.cyberphoto.se" . $betygLink .  "\">", $html);
			
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
	require("class.phpmailer.php");

	$mail = new phpmailer();	
	
	$mail->From     = $from;
	$mail->FromName = "";
	$mail->Host     = "81.8.240.27";
	//$mail->Mailer   = "smtp";

	$mail->AddAddress ($to);
	$mail->Body = $messageHtml;
	$mail->AltBody = $messageText;

	$mail->Subject = "Produkttips från " . $from;	

	$mail->IsHTML(true);
	$mail->IsSMTP(true);
	//$mail->AddEmbeddedImage ("/home/www/dotsv.gif", "dotsv.gif", "dotsv.gif", "base64", "image/gif");

	if($mail->Send()) {
		$date = date("Y-m-d H:i:s");
		//echo $date;
		$updt = "INSERT INTO log_web (dat, webpage, artnr) values ('" . $date . "', 'tipsa.php', '" . $artnr . "')";
		//echo $updt . "<br>";
		@mssql_query($updt, $conn_ms);
		return true;
	} else {
		return false;
	}	

}










%>

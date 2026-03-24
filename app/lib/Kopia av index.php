<%
unset($user_agent);
unset($ip_address);
unset($referer);

$user_agent = $HTTP_USER_AGENT;
$ip_address = $REMOTE_ADDR;
if (isset($id))
$referer = "cyberborsen/add_del.php?id=$id";

elseif (isset($ordernr))
$referer = "kundvagn/backnp.php?ordernr=$ordernr&result=$result";

elseif (isset($kollinr)) {
	
	$referer = "http://server.logistik.posten.se/servlet/PacTrack?kolliid=$kollinr&xslURL=/xsl/pactrack/standard.xsl&lang=SE&cssURL=http://www.cyberphoto.se/css/pacsoft.css";
	#$referer = "kollinr.php?kollinr=$kollinr";
	}
	
elseif (isset($beskrivning))
$referer = "searchsimple.php?beskrivning=$beskrivning";

elseif (isset($show_article) || isset($showArticle))
$referer = "pri_show_article.php?show_article=$show_article$showArticle";

elseif (isset($kategori))
$referer = "pri_show_kategori.php?kategori_id=$kategori";

elseif ($lang == 'fi' && isset($link))
$referer = "$link?lang=fi&fi=yes";

elseif (isset($ref))
$referer = $ref;

elseif (!($lang == 'fi'))
$referer = $argv[0];

//echo $referer;
//exit;

if (isset($referer)) {
	if (eregi("http://", $referer) || eregi("https://", $referer))  {
		if (!(eregi("cyberphoto.se", $referer) || (eregi("secure.incab.se", $referer)) || (eregi("www.netpay.saljfinans.com", $referer)) || (eregi("server.logistik.posten.se", $referer)) || (eregi("secure.incab.se", $referer))  )) 
			$referer = "start.php";
	}
}

if (empty($referer)) {
	if ($lang == "fi")
	$referer = "start.php";
	else {
		// för intern startsida
		//if (eregi("81.8.240.", $ip_address) || ($ip_address == "81.8.144.102") || ($ip_address == "213.79.137.61") || (eregi("192.168.1.", $ip_address)) )
		// byt ut nedanstående om du vill ha något annat internt
		//if (eregi("81.8.240.100", $ip_address))		
			$referer = "start.php";
			
	}
}

%>
<% if ( eregi("$(Netscape6)",$user_agent) || eregi("Netscape6/6\.1", $user_agent) || eregi("Netscape6/6\.01", $user_agent)) : %>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd"> 
<html>
<head>
<link rel="shortcut icon" href="logga2.ico" />
<title>CyberPhoto AB. Vi kan digitalkameror och videokameror. Vi har även MP3 och Mobiltelefoner.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="digitalkamera,digitalkameror,kamera,kameror,videokamera,videokameror,skrivare,skrivarpapper,minneskort,minnesmedia,foto,film,mp3,mp-3,lcd-tv,bildskärmar,objektiv,fototillbehör,Fotoprodukter,fotoutrustning,fotoväska,fotoväskor,videoprojektor,videoprojektorerer,skanner,skannrar,skanners,Hörlurar,stativ,kikare,teleskop,canon,nikon,Minolta,konica,olympus,pentax,panasonic,sony,Sandisk,billiga digitalkameror,billig digitalkamera">
<meta name="description" content="CyberPhoto säljer digitalkameror, en digitalkamera från kamera och kameror. Videokameror och en videokamera, digitalt eller inte mp3.">
<meta name="expires" CONTENT="never">
<meta name="language" CONTENT="SV">
<meta name="distribution" CONTENT="global">
<meta name="robots" CONTENT="index,follow">
<SCRIPT language="Javascript" type="text/javascript"> document.title="CyberPhoto AB"; </SCRIPT>
</head>
<frameset framespacing="0" border="false" frameborder="0" cols="*">
  <frameset framespacing="0" border="false" frameborder="0" rows="*,45">
    <frameset cols="151,*">
      <frameset rows="*,83">
        <frame name="meny" 
        <% if ($lang == 'fi'): %>
	src="meny_ns6/huvud_fi.htm"
	<% else: %>
        src="meny_ns6/huvud.htm" 
        <% endif; %>
        scrolling="auto" target="main" noresize>
        <frame name="1" 
        <% if ($lang == 'fi'): %>
        src="meny_ns6/corner_fi.htm" 
        <% else: %>
        src="meny_ns6/corner_nn.htm" 
        <% endif; %>
        scrolling="no" noresize>
      </frameset>
      <frame name="main" 
      src="<% echo $referer; %>" scrolling="auto" noresize>
    </frameset>
   <frame 
   <% if ($lang == 'fi'): %>
   src="meny_ns6/list_fi.php" 
   <% else: %>
   src="meny_ns6/list.php" 
   <% endif; %>
   scrolling="no" target="main">
  </frameset>
<noframes>
<body bgcolor="#FFFFFF">
<h1><strong><b>CyberPhoto AB. Vi kan digitalkameror och videokameror. Vi har även MP3 och Mobiltelefoner.</b></strong></h1><br />
De billiga digitalkameror och videokameror vi säljer har vi provat. Detta så att vi vet vad som är videoprojektor och videokameror som är dåligt. 
Det mesta på marknaden är mycket bra men, det vi säljer Billiga digitalkameror och videoprojektorer eller videokameror. Man brukar ju säga "Allt är inte guld 
som glimmar" och det stämmer.<br /><br />

<b>Billig videokamera</b><br />
Vi säljer alla videokameror och gör ingen skillnad på vem som tillverkat videokameran. Vi är helt oberoende och inte märkesbundna på något 
sätt. Till många kunders glädje och till många leverantörers och kollegors förfäran talar vi dock om vad vi tycker och delar med oss av 
de erfarenheter vi fått av de olika digitalkameror vi säljer. Allt för videoprojektorer du som kund ska få en videoprojektorer med ditt val av 
digitalkamera.<br /><br />
<a href="start.php">Startsida</a><br />
<a href="http://www.altus.se/">Sökmotoroptimering, sökmotorregistrering sökmotorpositionering</a><br />
<a href="http://www.altus.fr">SEO Search Engine Optimization REFERENCEMENT Site Internet Professionnel par ALTUS</a><br />
<a href="http://www.altus.se/de/">Suchmaschinenoptimierer seo Suchmaschinen-Marketing Suchmaschinenoptimierung mit Altus</a><br />
</body>
</noframes>
</frameset>
</html>

<% exit; %>

<% elseif ( eregi("MSIE",$user_agent) || eregi("Netscape6/6\.",$user_agent) || eregi("Netscape/7",$user_agent) || (eregi("Mozilla/5", $user_agent) AND !( eregi("Netscape", $user_agent ) )) ) : %>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd"> 
<html>
<head>
<link rel="shortcut icon" href="/logga2.ico" />
<title>CyberPhoto AB. Vi kan digitalkameror och videokameror. Vi har även MP3 och Mobiltelefoner.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="digitalkamera,digitalkameror,kamera,kameror,videokamera,videokameror,skrivare,skrivarpapper,minneskort,minnesmedia,foto,film,mp3,mp-3,lcd-tv,bildskärmar,objektiv,fototillbehör,Fotoprodukter,fotoutrustning,fotoväska,fotoväskor,videoprojektor,videoprojektorerer,skanner,skannrar,skanners,Hörlurar,stativ,kikare,teleskop,canon,nikon,Minolta,konica,olympus,pentax,panasonic,sony,Sandisk,billiga digitalkameror,billig digitalkamera">
<meta name="description" content="CyberPhoto säljer digitalkameror, en digitalkamera från kamera och kameror. Videokameror och en videokamera, digitalt eller inte mp3.">
<meta name="expires" CONTENT="never">
<meta name="language" CONTENT="SV">
<meta name="distribution" CONTENT="global">
<meta name="robots" CONTENT="index,follow">
</head>
<frameset framespacing="0" border="false" frameborder="0" cols="*">
  <frameset framespacing="0" border="false" frameborder="0" rows="*,45">
    <frameset cols="149,*">
      <frameset rows="*,83">
        <frame name="meny" 
        <% if ($lang == 'fi'): %>
        src="meny_ie/huvud_fi.htm" 
        <% else: %>
        src="meny_ie/huvud.htm" 
        <% endif; %>
        scrolling="auto" target="main" noresize>
        <frame name="1" 
        <% if ($lang == 'fi'): %>
        src="meny_ie/corner_fi.htm" 
        <% else: %>
        src="meny_ie/corner_ie.htm" 
        <% endif; %>
        scrolling="no" noresize>
      </frameset>
      <frame name="main" src="<% echo $referer; %>">
    </frameset>
   <frame 
   <% if ($lang == 'fi'): %>
   src="meny_ie/list_fi.php"
   <% else: %>
   src="meny_ie/list.php"
   <% endif; %>   
   scrolling="no" target="main">
  </frameset>
<noframes>
<body bgcolor="#FFFFFF">
<h1><strong><b>CyberPhoto AB. Vi kan digitalkameror och videokameror. Vi har även MP3 och Mobiltelefoner.</b></strong></h1><br />
De billiga digitalkameror vi säljer har vi provat och videokameror. Så att vi vet vad som är videoprojektor och videokameror som är dåligt. 
Det mesta på marknaden är mycket bra men, det vi säljer Billiga digitalkameror och videoprojektorer eller videokameror. Man brukar ju säga "Allt är inte guld 
som glimmar" och det stämmer.<br /><br />

<b>Billig videokamera</b><br />
Vi säljer alla videokameror och gör ingen skillnad på vem som tillverkat videokameran. Vi är helt oberoende och inte märkesbundna på något 
sätt. Till många kunders glädje och till många leverantörers och kollegors förfäran talar vi dock om vad vi tycker och delar med oss av 
de erfarenheter vi fått av de olika digitalkameror vi säljer. Allt för videoprojektorer du som kund ska få en videoprojektorer med ditt val av 
digitalkamera.<br /><br />
<a href="start.php">Startsida</a><br />
<a href="http://www.altus.se/">Sökmotoroptimering, sökmotorregistrering sökmotorpositionering</a><br />
<a href="http://www.altus.fr">SEO Search Engine Optimization REFERENCEMENT Site Internet Professionnel par ALTUS</a><br />
<a href="http://www.altus.se/de/">Suchmaschinenoptimierer seo Suchmaschinen-Marketing Suchmaschinenoptimierung mit Altus</a><br />
</body>
</noframes>
</frameset>
</html>


<% exit; %>

<% else: %>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd"> 
<html>
<head>
<!--StartFragment -->
<link rel "shortcut icon" href="http://www.cyberphoto.se/logga2.ico" type="image/ico">
<title>CyberPhoto AB. Vi kan digitalkameror och videokameror. Vi har även MP3 och Mobiltelefoner.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="digitalkamera,digitalkameror,kamera,kameror,videokamera,videokameror,skrivare,skrivarpapper,minneskort,minnesmedia,foto,film,mp3,mp-3,lcd-tv,bildskärmar,objektiv,fototillbehör,Fotoprodukter,fotoutrustning,fotoväska,fotoväskor,videoprojektor,videoprojektorerer,skanner,skannrar,skanners,Hörlurar,stativ,kikare,teleskop,canon,nikon,Minolta,konica,olympus,pentax,panasonic,sony,Sandisk,billiga digitalkameror,billig digitalkamera">
<meta name="description" content="CyberPhoto säljer digitalkameror, en digitalkamera från kamera och kameror. Videokameror och en videokamera, digitalt eller inte mp3.">
<meta name="expires" CONTENT="never">
<meta name="language" CONTENT="SV">
<meta name="distribution" CONTENT="global">
<meta name="robots" CONTENT="index,follow">
</head>
<frameset framespacing="0" border="false" frameborder="0" cols="*">
  <frameset framespacing="0" border="false" frameborder="0" rows="*,45">
    <frameset cols="151,*">
      <frameset rows="*,83">
        <frame name="meny" 
        <% if ($lang == 'fi'): %>
        src="meny_nn/huvud_fi.htm" 
        <% else: %>
        src="meny_nn/huvud.htm" 
        <% endif; %>
        scrolling="auto" target="main" noresize>
        <frame name="1" 
        <% if ($lang == 'fi'): %>
        src="meny_nn/corner_fi.htm"
        <% else: %>
        src="meny_nn/corner_nn.htm"
        <% endif; %>
        scrolling="no" noresize>
      </frameset>
      <frame name="main" src="<% echo $referer; %>">
    </frameset>
   <frame 
   <% if ($lang == 'fi'): %>
   src="meny_nn/list_fi.php" 
   <% else: %>
   src="meny_nn/list.php" 
   <% endif; %>
   scrolling="no" target="main">
  </frameset>
<noframes>
<body bgcolor="#FFFFFF">
<h1><strong><b>CyberPhoto AB. Vi kan digitalkameror och videokameror. Vi har även MP3 och Mobiltelefoner.</b></strong></h1><br />
De billiga digitalkameror vi säljer har vi provat och videokameror. Så att vi vet vad som är videoprojektor och videokameror som är dåligt. 
Det mesta på marknaden är mycket bra men, det vi säljer Billiga digitalkameror och videoprojektorer eller videokameror. Man brukar ju säga "Allt är inte guld 
som glimmar" och det stämmer.<br /><br />

<b>Billig videokamera</b><br />
Vi säljer alla videokameror och gör ingen skillnad på vem som tillverkat videokameran. Vi är helt oberoende och inte märkesbundna på något 
sätt. Till många kunders glädje och till många leverantörers och kollegors förfäran talar vi dock om vad vi tycker och delar med oss av 
de erfarenheter vi fått av de olika digitalkameror vi säljer. Allt för videoprojektorer du som kund ska få en videoprojektorer med ditt val av 
digitalkamera.<br /><br />
<a href="start.php">Startsida</a><br />
<a href="http://www.altus.se/">Sökmotoroptimering, sökmotorregistrering sökmotorpositionering</a><br />
<a href="http://www.altus.fr">SEO Search Engine Optimization REFERENCEMENT Site Internet Professionnel par ALTUS</a><br />
<a href="http://www.altus.se/de/">Suchmaschinenoptimierer seo Suchmaschinen-Marketing Suchmaschinenoptimierung mit Altus</a><br />
</body>
</noframes>
</frameset>
</html>

<% exit; %><% endif; %>

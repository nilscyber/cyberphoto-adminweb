<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html>
<%
# connect
include_once ("CConnect.php");
# nödvändiga funktioner
include_once ("CTekniska_data.php");
if ($art == "") 
	exit;
%>
<head>
<STYLE type="text/css">A { text-decoration: none }</STYLE>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="description" content="CyberPhoto AB. Jämför videoprojektorer">
<meta name="keywords" content="Jämförelse">
<title>CyberPhoto AB. Jämför prdukter</title>
</head>

<%
	include("javascript/cookiemonster.js");
%>

<body bgcolor="#FFFFFF" text="#2B2B2B" link="#2B2B2B" vlink="#2B2B2B" alink="#2B2B2B"
topmargin="0" leftmargin="0">
<div align="center"><center>

<table border="0" width="85%" cellspacing="0" cellpadding="0">
  <tr>
    <td height="3"><small><small><font face="Verdana, Arial"><img src="10.gif" width="1"
    height="1"></font></small></small></td>
  </tr>
  <tr>
    <td bgcolor="#000000" width="90%" height="1"><small><small><font face="Verdana, Arial"><img
    src="10.gif" width="1" height="1"></font></small></small></td>
  </tr>
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><font face="Arial" color="#000000"><small><small><img src="pic/vertline.gif" width="4"
        height="15" alt="pic/vertline.gif (831 bytes)"></small></small><strong>Jämför sida vid sida</strong></font></td>
        <td align="right"></td>
      </tr>
    </table>
    </td>
  </tr>
</table>
</center></div>

<p align="center"><small><small><font face="Verdana, Arial"><img src="10.gif" width="10"
height="2"></font></small></small></p>
<div align="center"><center>

<table border="0" width="85%" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" align="left"><font face="Verdana, Arial" size="1">Här
      kan du se tekniska data på de modeller du vill jämföra.&nbsp;<p>Lägg till 
      produkter i listan genom att välja dem här nedanför<br><% 

	if ($art == "screenTV")
		show_screenTV('NULL', 'yes'); 		
	elseif ($art == "screen")
		show_screen('NULL', 'yes');
	elseif ($art == "mp3")
		show_comp('NULL', 'yes',  'Artiklar.kategori_id = 127', 'mp3')
      
      
      %><p>&nbsp;</p></font></td>
  </tr>
  <tr>
    <td valign="top" align="left"><font face="Verdana, Arial" size="1">
	<table border="0" width="85%" cellspacing="1" cellpadding="2">
		<%
		if (isset($tekniska_artnr)) {
			if ($art == "screenTV")
				include ("Tekn_screenTV.php");
			elseif ($art == "screen")
				include ("Tekn_screen.php");
			elseif ($art == "mp3")
				include ("Tekn_mp3.php");
			// osv ... 
		
		}
		%>
	</table>
    </td>
  </tr>
</table>
</center></div>

<p align="center"><small><small><font face="Verdana, Arial"><img src="10.gif" width="10"
height="2"></font></small></small></p>
<div align="center"><center>


<p align="center"><img src="10.gif" width="10" height="10"</p>

<p align="center"><small><small><font face="Verdana, Arial"><small>Med reservation för
prisförändringar eller eventuella fel</small></font></small></small></p>

<p align="center"><small><small><small><font face="Verdana, Arial">© <a
href="http://www.cyberphoto.se" target="top">CyberPhoto AB</a></font></small></small></small></p>
</body>
</html>



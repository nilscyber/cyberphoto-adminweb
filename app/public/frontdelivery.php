<%
session_start();
require_once ("CConnect.php");
require_once ("CCheckStart.php");
$start = new CCheckStart();

$datumto = trim($datumto);
$datumfrom = trim($datumfrom);
$leverantor = trim($leverantor);

%>
<html>

<head>
<title>CyberPhoto frontadministration - Fakturaunderlag</title>
<link rel="stylesheet" type="text/css" href="/css/frontstyle.css">
</head>

<body topmargin="10" leftmargin="10">

<p><font face="Verdana" size="1">Fakturaunderlag för perioden <b> <% echo $datumfrom; %></b> -
<b> <% echo $datumto; %></b></font></p>
<p><font face="Verdana" size="1">Leverantör: <b><% if ($leverantor != "") { echo $start->getLeverantorName($leverantor); } else { %>Alla<% } %></b></font></p>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <% if ($leverantor == "") { %>
    <td width="130"><b><font face="Verdana" size="1">Leverantör</font></b></td>
    <% } %>
    <td width="80"><b><font face="Verdana" size="1">Från</font></b></td>
    <td width="80"><b><font face="Verdana" size="1">Till</font></b></td>
    <td width="40"><b><font face="Verdana" size="1">Dagar</font></b></td>
    <td><b><font face="Verdana" size="1">Banner</font></b></td>
    <td width="50"><b><font face="Verdana" size="1">Sektion</font></b></td>
    <td width="50"><b><font face="Verdana" size="1">Ansvarig</font></b></td>
  </tr>
  <% $start->getFrontDelivery($datumfrom,$datumto,$_SESSION['adminsite'],$leverantor); %>
</table>

</body>
</html>
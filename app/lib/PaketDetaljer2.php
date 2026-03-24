<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="14"><img src="/kopknapp/1.gif" border=0 width="14" height="14"></td>
    <td	width="300" background="/kopknapp/2.gif" border=0></td>
    <td width="16"><img src="/kopknapp/3.gif" border=0 width="16" height="14"></td>
</tr>

<tr>
    <td valign="top" background="/kopknapp/4.gif">&nbsp;</td>
    <td background="/kopknapp/5.gif">

<%
require_once("CBasket_n.php");
$bask = new CBasket();
%>

<div align="center">
  <center>
	<table border="0" cellpadding="2" width="430" cellspacing="1">
	<tr>
	<td colspan="3"><b><font color="#2B2B2B" face="Arial" size="3"><% if ($fi && !$sv) { %>Paketin sisältö<% } else { %>Paketdetaljer<% } %></font></td>
	</tr>
	<tr>
		  <td><b><font face="Verdana, Arial" size="1"><% if ($fi && !$sv) { %>Tuotteen nimi<% } else { %>Varans namn<% } %></font></b></td>
		  <td><font color="#2B2B2B" face="Verdana, Arial" size="1"><b><% if ($fi && !$sv) { %>määrä<% } else { %>Antal<% } %></b></font></td>
		  <td><font color="#2B2B2B" face="Verdana, Arial" size="1"><b><% if ($fi && !$sv) { %>varaston&nbsp;tila<% } else { %>Lagerstatus<% } %></b></font></td>
	</tr>        
          
          <% 
          $bask->viewPacketDelivery($artnr, '1', $fi);
          %>
          
      </table>
  </center>
</div>

    </td>
    <td valign="top" background="/kopknapp/6.gif">&nbsp;</td>
</tr>

<tr>
    <td><img src="/kopknapp/7.gif" border=0 width="14" height="16"></td>
    <td background="/kopknapp/8.gif"></td>
    <td><img src="/kopknapp/9.gif" border=0 width="16" height="16"></td>
</tr>
</table>

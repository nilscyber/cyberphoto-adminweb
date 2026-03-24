<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="14"><img src="/kopknapp/11.gif" border=0 width="14" height="14"></td>
    <td	width="300" background="/kopknapp/22.gif" border=0></td>
    <td width="16"><img src="/kopknapp/33.gif" border=0 width="16" height="14"></td>
</tr>

<tr>
    <td valign="top" background="/kopknapp/44.gif">&nbsp;</td>
    <td background="/kopknapp/55.gif">

<%
require_once("CBasket.php");
$bask = new CBasket();
%>

<div align="center">
  <center>
	<table border="0" cellpadding="2" width="430" cellspacing="0">
	<tr>
	<td colspan="3"><b><font color="#2B2B2B" face="Arial" size="3"><% if ($fi && !$sv) { %>Paketin sisältö<% } else { %>Paketdetaljer<% } %></font></td>
	</tr>
	<tr>
		  <td>&nbsp;</td>
		  <td><b><font color="#FFFFFF" face="Verdana, Arial" size="1"><% if ($fi && !$sv) { %>Tuotteen nimi<% } else { %>Varans namn<% } %></font></b></td>
		  <td><font color="#2B2B2B" face="Verdana, Arial" size="1"><b><% if ($fi && !$sv) { %>määrä<% } else { %>Antal<% } %></b></font></td>
		  <td><font color="#2B2B2B" face="Verdana, Arial" size="1"><b><% if ($fi && !$sv) { %>varaston&nbsp;tila<% } else { %>Lagerstatus<% } %></b></font></td>
	</tr>        
          
          <% 
          if ($numberpac == "") {
	  
	  	if (eregi("pac$", $article)) {
	  
		$artnr = $article1;
	  
	  } else {
	  
	  	$artnr = $article . "pac";
	  
	  	}
	  }
	  
          $bask->viewPacketDeliveryPicture($artnr, '1', $fi);
          %>
          
      </table>
  </center>
</div>

    </td>
    <td valign="top" background="/kopknapp/66.gif">&nbsp;</td>
</tr>

<tr>
    <td><img src="/kopknapp/77.gif" border=0 width="14" height="16"></td>
    <td background="/kopknapp/88.gif"></td>
    <td><img src="/kopknapp/99.gif" border=0 width="16" height="16"></td>
</tr>
</table>

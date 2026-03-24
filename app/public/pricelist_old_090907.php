<%
require_once ("CConnect.php");
require_once ("CConnect_ms.php");
require_once ("CPriceSelected.php");
$price = new CPriceSelected();

if ($deletearticle != "") {
	$price->articleDelete($deletearticle,$show);
}

if ($alldeletearticle != "") {
	$price->AllArticleDelete($alldeletearticle);
}

if ($deletepricelist != "") {
	$price->pricelistDelete($deletepricelist);
}

if ($change != "") {

$rows = $price->getSpecPricelist($change);

$addid = $rows->priceID;
// $addfrom = substr ($rows->priceDateFrom, 0, 19);
$timefrom = strtotime($rows->priceDateFrom);
$addfrom = date("Y-m-d H:i:s", $timefrom);
// $addto = substr ($rows->priceDateTo, 0, 19);
$timeto = strtotime($rows->priceDateTo);
$addto = date("Y-m-d H:i:s", $timeto);
$addrubrik = $rows->priceHeader;
$addrubrik_fi = $rows->priceHeader_fi;
$addpayoff = $rows->priceUnderHeader;
$addpayoff_fi = $rows->priceUnderHeader_fi;
$addpicture = $rows->pricePicture;
$addtype = $rows->priceType;
$addcomment = $rows->priceComment;
$addactive = $rows->priceActive;
$addcreatedby = $rows->priceCreatedBy;

}

if ($subm) {
	
	$olright = true;
	
	if ($addactive == "yes") {
		$addactive = -1;
	} else {
		$addactive = 0;
	}
	
	if ($addfrom == "") {
		$olright = false;
		$wrongmess .= "<p>- Från datum får inte vara tomt!</p>";
	}
	if ($addfrom != "") {
		if (!($price->isValidDateTime($addfrom))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addto == "") {
		$olright = false;
		$wrongmess .= "<p>- Till datum får inte vara tomt!</p>";
	}
	if ($addto != "") {
		if (!($price->isValidDateTime($addto))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addcreatedby == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vem du är!</p>";
	}

	if ($olright) {
		$price->AddPriceList($addrubrik,$addrubrik_fi,$addpayoff,$addpayoff_fi,$addtype,$addcomment,$addcreatedby,$addfrom,$addto,$addpicture,$addactive);
	}

}

if ($submC) {
	
	$olright = true;
	
	if ($addactive == "yes") {
		$addactive = -1;
	} else {
		$addactive = 0;
	}
	
	if ($addfrom == "") {
		$olright = false;
		$wrongmess .= "<p>- Från datum får inte vara tomt!</p>";
	}
	if ($addfrom != "") {
		if (!($price->isValidDateTime($addfrom))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addto == "") {
		$olright = false;
		$wrongmess .= "<p>- Till datum får inte vara tomt!</p>";
	}
	if ($addto != "") {
		if (!($price->isValidDateTime($addto))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addcreatedby == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vem du är!</p>";
	}

	if ($olright) {
		$price->ChangePriceList($addid,$addrubrik,$addrubrik_fi,$addpayoff,$addpayoff_fi,$addtype,$addcomment,$addcreatedby,$addfrom,$addto,$addpicture,$addactive);
	}

}

if ($submArt) {
	
	$olright = true;
	
	if ($addartnr == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste fylla i ett artikel nr!</p>";
	}
	if ($addartnr != "") {
		if (!($price->check_artikel_status($addartnr))) {
			$olright = false;
			$wrongmess .= "<p>- Detta artikel nummer finns inte. Vänligen kolla upp detta!</p>";
		}
	}

	if ($olright) {
		$price->addPriceListArticle($addpricelist,$addartnr);
	}

}

%>
<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
<link rel="stylesheet" type="text/css" href="../../css/frontstyle.css">
<title>Administrera prislistor</title>
<SCRIPT LANGUAGE="javascript">
<!--

function sf(){document.log.addartnr.focus();}

//-->
</SCRIPT>
<style type="text/css">

<!--
#layerconfirm { height: 45px; width: 355px; left: 410px; top: 260px; position: absolute; visibility: visible; }
-->

</style>

</head>

<% if ($addart == "yes") { %>
<body topmargin="0" leftmargin="0" onLoad=sf()>
<% } else { %>
<body topmargin="0" leftmargin="0">
<% } %>
<table border="0" cellpadding="5" cellspacing="0" height="100%" width="100%">
  <tr>
    <td width="130" height="75" style="border-bottom: 1px solid #008080" align="center">
    <% include ("kugge.php"); %></td>
    <td height="75" style="background-image: url('logo.jpg'); background-repeat: no-repeat; background-position: right center; border-bottom: 1px solid #008080"><b><font face="Arial">Administrera delar av CyberPhoto webbshop</font></b></td>
  </tr>
  <tr>
    <td width="130" valign="top" style="border-right: 1px solid #008080">
    <% include ("meny.php"); %>
    </td>
    <td valign="top">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%">
      <tr>
        <td width="100%"><b><font face="Arial">Prislistor</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a href="pricelist.php?add=yes"><img border="0" src="plus.jpg">&nbsp;Lägg till prislista</font></a></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><hr noshade color="#CCCCCC" size="1"></font></td>
      </tr>
      <% if ($add == "yes" || $addid != "") { %>
    <% if ($wrongmess) { %>
    <tr>
      <td width="100%">
      <table border="0" cellpadding="5" cellspacing="0" style="border:1px solid #000000; border-collapse: collapse; background-color:#FFFF00" width="555">
        <tr>
          <td><b><font face="Verdana" size="1" color="#000000"><% echo $wrongmess; %></font></td>
        </tr>
      </table>
      </td>
    </tr>
    <tr>
      <td width="100%"><hr noshade color="#C0C0C0" size="1" align="left" width="555"></td>
    </tr>
    <% } %>
      <tr>
        <td width="100%">
<form>
  <% if ($addid !="") { %>
  <input type="hidden" value="<% echo $addid; %>" name="addid">
  <input type="hidden" value=true name="submC">
  <% } else { %>
  <input type="hidden" value=true name="subm">
  <input type="hidden" value="yes" name="add">
  <% } %>
  <table border="0" cellpadding="5" cellspacing="3" style="border-collapse: collapse; border: 1px solid #000000; background-color: #999966">
    <tr>
      <td><font face="Verdana" size="1">Gäller från <b><font color="#FF0000">*</font></b></font></td>
      <td><input type="text" name="addfrom" size="20" value="<% if ($addfrom == "") { echo date("Y-m-d H:i:s", time()); } else { echo $addfrom; }  %>" style="font-family: Verdana; font-size: 8pt"></td>
      <td>&nbsp;</td>
      <td><font face="Verdana" size="1">Gäller till <b><font color="#FF0000">*</font></b></font></td>
      <td><input type="text" name="addto" size="20" value="<% if ($addto == "") { echo date("Y-m-d H:i:s", time() + 157680000); } else { echo $addto; }  %>" style="font-family: Verdana; font-size: 8pt"></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Rubrik</font></td>
      <td colspan="4">
      <input type="text" name="addrubrik" size="30" style="font-family: Verdana; font-size: 8pt" value="<% echo $addrubrik; %>"></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Rubrik_fi</font></td>
      <td colspan="4">
      <input type="text" name="addrubrik_fi" size="30" style="font-family: Verdana; font-size: 8pt" value="<% echo $addrubrik_fi; %>"></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Ev. Payoff text</font></td>
      <td colspan="4">
      <input type="text" name="addpayoff" size="60" style="font-family: Verdana; font-size: 8pt" value="<% echo $addpayoff; %>"></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Ev. Payoff text_fi</font></td>
      <td colspan="4">
      <input type="text" name="addpayoff_fi" size="60" style="font-family: Verdana; font-size: 8pt" value="<% echo $addpayoff_fi; %>"></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Ev. Bild</font></td>
      <td><input type="text" name="addpicture" size="20" value="<% echo $addpicture; %>" style="font-family: Verdana; font-size: 8pt"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Aktiv</font></td>
      <td><input type="checkbox" name="addactive" value="yes" <% if ($addactive != "0") { %> checked <% } %>></td>
      <td>&nbsp;</td>
      <td><font face="Verdana" size="1">Typ</font></td>
      <td><select size="1" name="addtype" style="font-family: Verdana; font-size: 8pt">
      <option value="0"<% if ($addtype == "0") echo " selected";%>>Normal</option>
      <option value="1"<% if ($addtype == "1") echo " selected";%>>Pro</option>
      <option value="2"<% if ($addtype == "2") echo " selected";%>>Mobil</option>
      </select></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Ev. kommentar</font></td>
      <td><textarea rows="2" name="addcomment" cols="20" style="font-family: Verdana; font-size: 8pt"><% echo $addcomment; %></textarea></td>
      <td>&nbsp;</td>
      <td><font face="Verdana" size="1">Skapad av <b><font color="#FF0000">*</font></b></font></td>
      <td><select size="1" name="addcreatedby" style="font-family: Verdana; font-size: 8pt">
      		<option></option>
      		<% $price->getAnstallda(); %>
      		</select></td>
    </tr>
    </table>
  <p><input type="submit" value="<% if ($addid !="") { %>Uppdatera<% } elseif ($addidc !="") {%>Kopiera post<% } else { %>Lägg till<% } %>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #008080; font-weight: bold; background-color: #C0C0C0"></p>
</form>        
        </td>
      </tr>
      <% } %>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <% if ($show == "") { %>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Aktuella prislistor</font></b></td>
      </tr>
      <tr>
        <td width="100%">
        <table border="0" cellpadding="1" cellspacing="3">
          <tr>
            <td width="90"><b><font face="Verdana" size="1">Länkar</font></b></td>
            <td width="90"><b><font face="Verdana" size="1">Gäller till</font></b></td>
            <td width="90" align="center"><b><font face="Verdana" size="1">Återstår</font></b></td>
            <td width="350"><b><font face="Verdana" size="1">Rubrik</font></b></td>
            <td width="350"><b><font face="Verdana" size="1">Rubrik FI</font></b></td>
            <td width="50" align="center"><b><font face="Verdana" size="1">Aktiv</font></b></td>
            <td><b><font face="Verdana" size="1">&nbsp;</font></b></td>
          </tr>
          <% $price->getPriceListActual(); %>
        </table>
        </td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><hr noshade color="#CCCCCC" size="1"></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Planerade prislistor</font></b></td>
      </tr>
      <tr>
        <td width="100%">
        <table border="0" cellpadding="1" cellspacing="3">
          <tr>
            <td width="90"><b><font face="Verdana" size="1">Länkar</font></b></td>
            <td width="90"><b><font face="Verdana" size="1">Gäller från</font></b></td>
            <td width="90" align="center"><b><font face="Verdana" size="1">Publiceras om</font></b></td>
            <td width="350"><b><font face="Verdana" size="1">Rubrik</font></b></td>
            <td width="350"><b><font face="Verdana" size="1">Rubrik FI</font></b></td>
            <td width="50" align="center"><b><font face="Verdana" size="1">Aktiv</font></b></td>
            <td><b><font face="Verdana" size="1">&nbsp;</font></b></td>
          </tr>
          <% $price->getPriceListPlan(); %>
        </table>
        </td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><hr noshade color="#CCCCCC" size="1"></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <% } %>
      <% if ($show != "") { %>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Detaljer av vald prislista</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%">
        <table border="0" cellpadding="1" cellspacing="3">
          <tr>
            <td width="80"><b><font face="Verdana" size="1">Gäller från</font></b></td>
            <td width="80"><b><font face="Verdana" size="1">Gäller till</font></b></td>
            <td width="75" align="center"><b><font face="Verdana" size="1">Återstår</font></b></td>
            <td width="140"><b><font face="Verdana" size="1">Rubrik</font></b></td>
            <td width="140"><b><font face="Verdana" size="1">Rubrik FI</font></b></td>
            <td width="140"><b><font face="Verdana" size="1">Payoff text</font></b></td>
            <td width="140"><b><font face="Verdana" size="1">Payoff text FI</font></b></td>
            <td width="50" align="center"><b><font face="Verdana" size="1">Typ</font></b></td>
            <td width="100" align="center"><b><font face="Verdana" size="1">Kommentar</font></b></td>
            <td width="25" align="center"><b><font face="Verdana" size="1">Av</font></b></td>
            <td width="100" align="center"><b><font face="Verdana" size="1">Bild</font></b></td>
            <td width="50" align="center"><b><font face="Verdana" size="1">Aktiv</font></b></td>
            <td><b><font face="Verdana" size="1">&nbsp;</font></b></td>
            <td><b><font face="Verdana" size="1">&nbsp;</font></b></td>
          </tr>
          <% $price->getPriceListActualDetail($show); %>
        </table>
        </td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><hr noshade color="#CCCCCC" size="1"></font></td>
      </tr>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Länken till denna prislista blir</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%">
	<table border="0" cellpadding="2" cellspacing="0">
	  <tr>
	    <td width="25"><img border="0" src="sv_mini.jpg"></td>
	    <td><font face="Verdana" size="1"><a href="/pri_selected.php?ID=<% echo $show; %>" target="_blank">pri_selected.php?ID=<% echo $show; %></a></font></td>
	  </tr>
	  <tr>
	    <td width="25"><img border="0" src="fi_mini.jpg"></td>
	    <td><font face="Verdana" size="1"><a href="/pri_selected_fi.php?ID=<% echo $show; %>" target="_blank">pri_selected_fi.php?ID=<% echo $show; %></a></font></td>
	  </tr>
	  <tr>
	    <td width="25"><img border="0" src="fisv_mini.jpg"></td>
	    <td><font face="Verdana" size="1"><a href="/pri_selected_fi_se.php?ID=<% echo $show; %>" target="_blank">pri_selected_fi_se.php?ID=<% echo $show; %></a></font></td>
	  </tr>
	</table>
        </td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><hr noshade color="#CCCCCC" size="1"></font></td>
      </tr>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Produkter kopplade till ovantående prislista</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%">
        <table border="0" cellpadding="1" cellspacing="3">
          <tr>
            <td width="80"><b><font face="Verdana" size="1">Artikel nr</font></b></td>
            <td width="550"><b><font face="Verdana" size="1">Benämning</font></b></td>
            <td width="70" align="center"><b><font face="Verdana" size="1">Lagersaldo</font></b></td>
            <td width="75" align="center"><b><font face="Verdana" size="1"></font></b></td>
          </tr>
          <% $price->getPriceListArtnr($show); %>
        </table>
        </td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><hr noshade color="#CCCCCC" size="1"></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a href="pricelist.php?addart=yes&show=<% echo $show; %>"><img border="0" src="plus.jpg">&nbsp;Lägg till artikel till prislistan</font></a></td>
      </tr>
      <% } %>
      <% if ($addart == "yes") { %>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
    <% if ($wrongmess) { %>
    <tr>
      <td width="100%">
      <table border="0" cellpadding="5" cellspacing="0" style="border:1px solid #000000; border-collapse: collapse; background-color:#FFFF00" width="555">
        <tr>
          <td><b><font face="Verdana" size="1" color="#000000"><% echo $wrongmess; %></font></td>
        </tr>
      </table>
      </td>
    </tr>
    <tr>
      <td width="100%"><hr noshade color="#C0C0C0" size="1" align="left" width="555"></td>
    </tr>
    <% } %>
      <tr>
        <td width="100%">
<form name="log">
  <input type="hidden" value=true name="submArt">
  <input type="hidden" value="<% echo $show; %>" name="addpricelist">
  <input type="hidden" value="yes" name="addart">
  <input type="hidden" value="<% echo $show; %>" name="show">
  <table border="0" cellpadding="5" cellspacing="3" style="border-collapse: collapse; border: 1px solid #000000; background-color: #999966">
    <tr>
      <td><font face="Verdana" size="1">Artikel nr</font></td>
      <td>
      <input type="text" name="addartnr" size="30" style="font-family: Verdana; font-size: 8pt" value="<% echo $addartnr; %>"></td>
    </tr>
    </table>
  <p><input type="submit" value="<% if ($addid !="") { %>Uppdatera<% } elseif ($addidc !="") {%>Kopiera post<% } else { %>Lägg till<% } %>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #008080; font-weight: bold; background-color: #C0C0C0"></p>
</form>
	</td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <% } %>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
    </table>
    </td>
  </tr>
</table>

<% if ($confirmdelete != "") { %>
<div id="layerconfirm">

<% include ("confirm_pricedelete.php"); %>
          		
</div>
<% } %>

</body>
<script language="JavaScript" type="text/javascript" src="wz_tooltip_front.js"></script>
</html>
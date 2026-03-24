<?php
session_start();
require_once("COrderCheck.php");
$ordercheck = new COrderCheck();

if ($change != "") {

	$rows = $ordercheck->getSpecAlerts($change);

	$addID = $rows->checkID;
	$addFrom = substr ($rows->checkFrom, 0, 19);
	if ($now == "yes") {
		$addTo = date("Y-m-d H:i:s", time());
	} else {
		$addTo = substr ($rows->checkTo, 0, 19);
	}
	$addArtnr = $rows->checkArtnr;
	$addRecipient = $rows->checkRecipient;

}

if ($subm) {
	
	$olright = true;
	
	if ($addFrom == "") {
		$olright = false;
		$wrongmess .= "<p>- Datum för när den skall publiseras får inte vara tomt!</p>";
	}
	if ($addFrom != "") {
		if (!($ordercheck->isValidDateTime($addFrom))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addTo == "") {
		$olright = false;
		$wrongmess .= "<p>- Datum för hur länge den skall ligga får inte vara tomt!</p>";
	}
	if ($addTo != "") {
		if (!($ordercheck->isValidDateTime($addTo))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addArtnr == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange ett artikel nummer!</p>";
	}
	if ($addArtnr != "") {
		if (!($ordercheck->check_artikel_status($addArtnr) == $addArtnr)) {
			$olright = false;
			$wrongmess .= "<p>- Detta artikel nummer finns inte. Vänligen kolla upp detta! (måste skrivas exakt)</p>";
		}
	}
	if ($addRecipient == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vem som skall ha aviseringen!</p>";
	}
	if ($olright) {
		$ordercheck->doAlertAdd($addFrom,$addTo,$addArtnr,$addRecipient);
	}

}
if ($submC) {
	
	$olright = true;

	if ($addFrom == "") {
		$olright = false;
		$wrongmess .= "<p>- Datum för när den skall publiseras får inte vara tomt!</p>";
	}
	if ($addFrom != "") {
		if (!($ordercheck->isValidDateTime($addFrom))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addTo == "") {
		$olright = false;
		$wrongmess .= "<p>- Datum för hur länge den skall ligga får inte vara tomt!</p>";
	}
	if ($addTo != "") {
		if (!($ordercheck->isValidDateTime($addTo))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addArtnr == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange ett artikel nummer!</p>";
	}
	if ($addArtnr != "") {
		if (!($ordercheck->check_artikel_status($addArtnr) == $addArtnr)) {
			$olright = false;
			$wrongmess .= "<p>- Detta artikel nummer finns inte. Vänligen kolla upp detta! (måste skrivas exakt)</p>";
		}
	}
	if ($addRecipient == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vem som skall ha aviseringen!</p>";
	}
	if ($olright) {
		$ordercheck->doAlertChange($addID,$addFrom,$addTo,$addArtnr,$addRecipient);
	}
}

?>
<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
<link rel="stylesheet" type="text/css" href="/css/admin_current.css">
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<link rel="stylesheet" type="text/css" href="/css/frontstyle.css">
<title>Admin CyberPhoto</title>
</head>

<body topmargin="0" leftmargin="0">

<table border="0" cellpadding="5" cellspacing="0" height="100%" width="100%">
  <tr>
    <td width="130" height="75" style="border-bottom: 1px solid #008080" align="center">
    <?php include ("kugge.php"); ?></td>
    <td height="75" style="background-image: url('logo.jpg'); background-repeat: no-repeat; background-position: right center; border-bottom: 1px solid #008080"><b><font face="Arial" size="3">Administrera delar av CyberPhoto webbshop</font></b></td>
  </tr>
  <tr>
    <td width="130" valign="top" style="border-right: 1px solid #008080">
    <?php include ("meny.php"); ?>
    </td>
    <td valign="top">
    <div class="rubrik">Artiklar som skall bevakas (gäller endast webbordrar)</div>
    <p>&nbsp;</p>
	<?php
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	?>
	<?php if ($add == "yes" || $addID != "") { ?>
	<div>
	<form>
	<?php if ($addID !="") { ?>
		<input type="hidden" value="<?php echo $addID; ?>" name="addID">
		<input type="hidden" value=true name="submC">
	<?php } else { ?>
		<input type="hidden" value=true name="subm">
		<input type="hidden" value="yes" name="add">
	<?php } ?>
		<table border="0" cellpadding="5" cellspacing="0" style="border: 1px solid #999999; background-color: #C4E1FF">
		  <tr>
			<td align="left">Gäller från:</td>
			<td align="left">Gäller till:<?php if ($change != "") { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?change=<?php echo $addID; ?>&now=yes">&nbsp;&nbsp;(avsluta nu)</a><?php } ?></td>
		  </tr>
		  <tr>
			<td><input type="text" name="addFrom" size="20" value="<?php if ($addFrom == "") { echo date("Y-m-d H:i:s", time()); } else { echo $addFrom; } ?>" style="font-family: Verdana; font-size: 8pt"></td>
			<td><input type="text" name="addTo" size="20" value="<?php if ($addTo == "") { echo date("Y-m-d 23:59:59",mktime(0,0,0,date("n")+1,1-1,date("Y"))); } else { echo $addTo; } ?>" style="font-family: Verdana; font-size: 8pt"></td>
		  </tr>
		  <tr>
			<td align="left">Artikel nr:</td>
			<td><input type="text" name="addArtnr" size="20" value="<?php echo $addArtnr; ?>" style="font-family: Verdana; font-size: 8pt"></td>
		  </tr>
		  <tr>
			<td align="left">Skickas till:</td>
			<td><select size="1" name="addRecipient" style="font-family: Verdana; font-size: 8pt">
      		<option></option>
      		<?php $ordercheck->getAnstallda(); ?>
      		</select>
			</td>
		  </tr>
		</table>
		<p><input type="submit" value="<?php if ($addID !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #000000; font-weight: bold; background-color: #CCCCCC"></p>
	</form>
	</div>
	<?php } ?>
	<div><img border="0" src="/pic/help.gif">&nbsp;<b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?add=yes">Lägg till bevakning</b></a></div>
    <p>&nbsp;</p>
	<div><span class="dateheadline">&nbsp;Aktuella bevakningar</span></div>
	<div><?php $ordercheck->getActualAlerts(true); ?></div>
    <p>&nbsp;</p>
	<div><span class="dateheadline">&nbsp;Planerade bevakningar</span></div>
	<div><?php $ordercheck->getActualAlerts(false); ?></div>
    <p>&nbsp;</p>
	</td>
  </tr>
</table>

</body>

</html>
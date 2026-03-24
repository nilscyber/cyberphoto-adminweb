<?php
require_once ("CStoreStatus.php");
require_once ("stocktarget.php");
$store = new CStoreStatus();
// $diffstore = ($totalstore-$store->displayOverallStoreStatus());
$frandatum = date('Y-m-d', time());
$tilldatum = date('Y-m-d', strtotime("$frandatum +1 month"));
$tilldatumweek = date('Y-m-d', strtotime("$frandatum +2 week"));
if ($period == "") {
	$period = "twoweek";
}
?>
<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
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
	<div>
	<form>
	<table>
		<tr>
			<td>
			<input type="radio" name="period" value="today" onclick="submit();" <?php if ($period == "today") echo "checked"; ?>> Idag
			<input type="radio" name="period" value="twoweek" onclick="submit();" <?php if ($period == "twoweek") echo "checked"; ?>> Inom två veckor
			<input type="radio" name="period" value="month" onclick="submit();" <?php if ($period == "month") echo "checked"; ?>> Inom en månad
			<input type="radio" name="period" value="total" onclick="submit();" <?php if ($period == "total") echo "checked"; ?>> Totalt
			<input type="radio" name="period" value="late" onclick="submit();" <?php if ($period == "late") echo "checked"; ?>> Försenat
			<input type="radio" name="period" value="nolev" onclick="submit();" <?php if ($period == "nolev") echo "checked"; ?>> Ej leveranstid
			</td>
		<tr>
	</table>
	</form>
	</div>

    <?php if ($grossid != "") { ?>
		<?php if ($period == "total") { ?>
			<div class="rubrik">Beställda varor totalt</div>
		<?php } elseif ($period == "month") { ?>
			<div class="rubrik">Beräknas in i lager närmaste månaden</div>
		<?php } elseif ($period == "twoweek") { ?>
			<div class="rubrik">Beräknas in i lager närmaste två veckorna</div>
		<?php } elseif ($period == "late") { ?>
			<div class="rubrik">Beställda varor som är försenade till vårt lager</div>
		<?php } elseif ($period == "today") { ?>
			<div class="rubrik">Beställda varor som beräknas komma in idag</div>
		<?php } elseif ($period == "nodate") { ?>
			<div class="rubrik">Beställda varor som Ej har någon leveranstid!</div>
		<?php } ?>
			<p>&nbsp;</p>
			<div><?php $store->getIncomingFromSupplierValueDetail($grossid,$sortby); ?></div>
	<?php } else { ?>
		<?php if ($period == "total") { ?>
			<div class="rubrik">Beställda varor totalt</div>
			<p>&nbsp;</p>
			<div><?php $store->getIncomingFromSupplierValueNew($sortby,"all"); ?></div>
			<p>&nbsp;</p>
		<?php } elseif ($period == "today") { ?>
			<div class="rubrik">Beställda varor som beräknas komma in idag</div>
			<p>&nbsp;</p>
			<div><?php $store->getIncomingFromSupplierValueNew($sortby,"today"); ?></div>
			<p>&nbsp;</p>
		<?php } elseif ($period == "twoweek") { ?>
			<div class="rubrik">Beräknas in i lager närmaste två veckorna</div>
			<p>&nbsp;</p>
			<div><?php $store->getIncomingFromSupplierValueNew($sortby,"twoweek"); ?></div>
			<p>&nbsp;</p>
		<?php } elseif ($period == "month") { ?>
			<div class="rubrik">Beräknas in i lager närmaste månaden</div>
			<p>&nbsp;</p>
			<div><?php $store->getIncomingFromSupplierValueNew($sortby,"month"); ?></div>
			<p>&nbsp;</p>
		<?php } elseif ($period == "late") { ?>
			<div class="rubrik">Beställda varor som är försenade till vårt lager</div>
			<p>&nbsp;</p>
			<div><?php $store->getIncomingFromSupplierValueNew($sortby,"late"); ?></div>
			<p>&nbsp;</p>
		<?php } elseif ($period == "nolev") { ?>
			<div class="rubrik">Beställda varor som Ej har någon leveranstid!</div>
			<p>&nbsp;</p>
			<div><?php $store->getIncomingFromSupplierValueNew($sortby,"nodate"); ?></div>
			<p>&nbsp;</p>
		<?php } ?>
	<?php } ?>




	<?php if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") { ?>
	<?php } ?>
	</td>
  </tr>
</table>

</body>

</html>
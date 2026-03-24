<?php
require_once ("CDeparture.php");
$departure = new CDeparture();

if ($subm) {
	
	$olright = true;
	
	if ($dep_date == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste fylla i ett giltigt datum</p>";
	}
	if ($olright) {
		$departure->makeInsertDeparture("$dep_date");
		// $departure->departureChange($dep_time,$dep_active,$dep_changewhy,$dep_createdby,$dep_ip,$dep_id);
		$dep_ok = true;
	}
}

// echo $departure->getLastDeparture() . "<br>";
$getlastdate = strtotime($departure->getLastDeparture());
// echo $getlastdate . "<br>";
$estimate_lastdate = date("Y-m-d", mktime(0,0,0,date("m",$getlastdate)+2,date("d",$getlastdate),date("Y",$getlastdate)));
// echo $estimate_lastdate;
// exit;

?>

<html>

<head>
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<link rel="stylesheet" type="text/css" href="/css/mobil_abonnemang_v3.css" />
<script type="text/javascript" src="/order/admin/javascript/cal2.js"></script>
<script type="text/javascript" src="/order/admin/javascript/cal_conf2.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Ändra avgångstid</title>
</head>

<body bgcolor="#FFECB0">

<?php if (!$dep_ok) { ?>
<form name="sampleform">
  <?php if ($dep_id !="") { ?>
  <input type="hidden" value="<?php echo $dep_id; ?>" name="dep_id">
  <input type="hidden" value=true name="submC">
  <?php } else { ?>
  <input type="hidden" value=true name="subm">
  <input type="hidden" value="yes" name="add">
  <input type="hidden" value="<?php echo $addidc; ?>" name="addidc">
  <?php } ?>
<table border="0" cellpadding="3" cellspacing="0">
  <?php if ($wrongmess) { ?>
  <tr>
    <td colspan="4"><b><font face="Arial" size="2" color="#FF0000"><?php echo $wrongmess; ?></font></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td>Sista aktiva datumet:</td>
    <td><b>&nbsp;<?php echo date("Y-m-d", strtotime($departure->getLastDeparture())); ?></b></td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>Ange datum:</td>
    <td><input type="text" name="dep_date" size="10" value="<?php echo $estimate_lastdate; ?>" style="font-family: Verdana; font-size: 8pt"></td>
    <td colspan="2">&nbsp;<a href="javascript:showCal('Calendar1')">Välj datum</a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
  <p><input type="submit" value="Lägg till" name="B1"></p>
</form>
<?php } else { ?>
<p><b>Posten är nu uppdaterad!</b></p>
<span onclick="top.opener.location.reload(true);window.close()" style="cursor:pointer;">Stäng fönster</span>
<?php } ?>

</body>

</html>
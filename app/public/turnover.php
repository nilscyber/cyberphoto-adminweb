<?php
session_start();
require_once ("CTurnOver.php");
$turnover = new CTurnOver();
$ref_dagensdatum = date("Y-m-d");
if ($firstinput != "") {
	$dagensdatum = $firstinput;
} else {
	$dagensdatum = date("Y-m-d");
}
$dagensdatum_format = date("l j F Y", strtotime($dagensdatum));
$valuenow = $turnover->getTotalValueNew();


if ($firstinput == "") {

	$today = time();
	$today_day = date("w", $today);
	$last_year_day = date("w", strtotime("-1 year"));
	// $last_year_day = date("w", strtotime("$dagensdatum -1 year"));
	$last_year_day2 = date("Y-m-d", strtotime("-1 year"));
	// $last_year_day2 = date("Y-m-d", strtotime("$dagensdatum -1 year"));
	$last_year_day3 = strtotime($last_year_day2);
	$diff_day = $today_day - $last_year_day;
	$compare_date = date("Y-m-d", mktime(0,0,0,date("m",$last_year_day3),date("d",$last_year_day3)+$diff_day,date("Y",$last_year_day3)));
	// echo $compare_date;
	$compare_date_format = date("l j F Y", strtotime($compare_date));

	$valuethen = $turnover->getTotalValueNewOneYearBack();
	if ($valuethen == 0) {
		$valuediff = $valuenow - 1;
		$diffinprocent = $valuediff / 1;
	} else {
		$valuediff = $valuenow - $valuethen;
		$diffinprocent = $valuediff / $valuethen;
	}
	$diffinprocent = round(($diffinprocent * 100), 1);
	// echo $diffinprocent;
	if ($valuediff > 0) {
		$class = "rubrikgreen";
	} else {
		$class = "rubrikred";
	}
}
?>
<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
<link rel="stylesheet" type="text/css" href="/css/frontstyle.css">
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<script type="text/javascript" src="/order/javascript/cal2.js"></script>
<script type="text/javascript" src="/order/javascript/cal_conf2.js"></script>
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
    <div class="rubrik">Omsättning - <?php echo $dagensdatum_format; ?></div>
    <p>&nbsp;</p>
	<div><span class="lagerrubrik"><?php echo number_format($valuenow, 0, ',', ' '); ?> kr</span></div>
    <p>&nbsp;</p>
	<div>
	<table border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td><img border="0" src="sv.jpg"></td>
			<td class="lagerrubrik2" width="320" align="right"><?php echo number_format($turnover->getCountryValueNew(46), 0, ',', ' '); ?> kr</td>
		</tr>
		<tr>
			<td><img border="0" src="fi.jpg"></td>
			<td class="lagerrubrik2" width="320" align="right"><?php echo number_format($turnover->getCountryValueNew("358,999"), 0, ',', ' '); ?> kr</td>
		</tr>
		<tr>
			<td><img border="0" src="dk.jpg"></td>
			<td class="lagerrubrik2" width="320" align="right"><?php echo number_format($turnover->getCountryValueNew(45), 0, ',', ' '); ?> kr</td>
		</tr>
	</table>
	</div>
    <p>&nbsp;</p>
	<?php
	if ($firstinput == "") {
	?>
    <div class="rubrik">Omsättning motsvarande dag ifjol - <?php echo $compare_date_format; ?></div>
	<div><span class="lagerrubrik2"><?php echo number_format($valuethen, 0, ',', ' '); ?> kr</span></div>
    <p>&nbsp;</p>
    <div class="rubrik">Skillnad mellan idag och ifjol</div>
	<div><span class="<?php echo $class; ?>"><?php echo number_format($valuediff, 0, ',', ' '); ?> kr (<?php echo $diffinprocent; ?>%)</span></div>
    <p>&nbsp;</p>
	<?php
	}
	?>
	<div id="sjabotest">
	<form name="sampleform">
	<span class="abbrubrik">Annat datum:</span><br>
	<input type="text" name="firstinput" size=12 value="<?php echo $dagensdatum; ?>" style="font-family: Verdana; font-size: 10px"> <span class="abbrubrik"><a href="javascript:showCal('Calendar1')">Välj datum</a></span> <?php if ($ref_dagensdatum != $dagensdatum) { ?><span class="abbrubrik"><a href="turnover.php"> Idag</a></span><?php } ?><br>
	<hr noshade color="#C0C0C0" align="left" width="250" size="1">
	<input type="submit" value="Rapport" style="font-family: Verdana; font-size: 10px">
	</form>
	</div>

	</td>
  </tr>
</table>

</body>

</html>
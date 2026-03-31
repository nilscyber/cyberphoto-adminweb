<?php
spl_autoload_register(function ($class) {
	include $class . '.php';
});

session_start();
extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_SKIP);
extract($_COOKIE, EXTR_SKIP);

$tradein = new CTradeIn();

if ($edit == "yes") {
	$rows = $tradein->getTradeInInfo();

	$tiGREEN_TO = $rows->tiGREEN_TO;
	$tiGREEN_TB = $rows->tiGREEN_TB;
	$tiGREEN_TG = $rows->tiGREEN_TG;
	$tiRED_TO = $rows->tiRED_TO;
	$tiRED_TB = $rows->tiRED_TB;
	$tiRED_TG = $rows->tiRED_TG;

}

if ($subm) {
	
	$olright = true;
	
	if ($tiGREEN_TO != "" && $tiRED_TO != "") {
		$olright = false;
		$wrongmess .= "<p class=\"wrongmess\">- Ett av fälten för omsättning måste vara tomt!</p>";
	}
	if ($tiGREEN_TG != "" && $tiRED_TG != "") {
		$olright = false;
		$wrongmess .= "<p class=\"wrongmess\">- Ett av fälten för marginalen måste vara tomt!</p>";
	}
	
	/*
	if (!is_numeric($konumber)) {
		$olright = false;
		$wrongmess .= "<p class=\"wrongmess\">- Skall vara ett nummer mellan 1-99!</p>";
	}
	*/

	if ($olright) {
		$tradein->updateTradeInValue();
		$uppdate_ok = true;
	}

}

?>
<html>

<head>
<title>Uppdatera TradeIn value</title>
<link rel="icon" type="image/png" href="https://admin.cyberphoto.se/favicon.png">
<link rel="stylesheet" type="text/css" href="/css/tradein.css?v=g<?php echo date("ynjGi"); ?>" />
</head>

<?php if ($uppdate_ok) { ?>
	<!--
	<body>
	-->
	<body onload="top.opener.location.reload(true);window.close()">
<?php } else { ?>
	<body onLoad="document.update_form.konumber.focus();document.update_form.konumber.select();">
<?php } ?>

<?php
if ($wrongmess) {
	echo $wrongmess;
}
?>
<div>
<?php if (!$uppdate_ok) { ?>
<form name="update_form" method="post">
<input type="hidden" value=true name="subm">
<table id="update_box" border="0" cellpadding="2" cellspacing="0">
  <tr>
	<td align="center">Omsättning</td>
	<!-- <td align="center">TB</td> -->
	<td align="center">Marginal</td>
	<td align="center">Omsättning</td>
	<!-- <td align="center">TB</td> -->
	<td align="center">Marginal</td>
  </tr>
  <tr>
	<td><input onclick="select()" class="textbox_green" type="text" name="tiGREEN_TO" value="<?php echo $tiGREEN_TO; ?>">&nbsp;</td>
	<!-- <td><input onclick="select()" class="textbox_green" type="text" name="tiGREEN_TB" value="<?php echo $tiGREEN_TB; ?>">&nbsp;</td> -->
	<td><input onclick="select()" class="textbox_green" type="text" name="tiGREEN_TG" value="<?php echo $tiGREEN_TG; ?>">&nbsp;</td>
	<td><input onclick="select()" class="textbox_red" type="text" name="tiRED_TO" value="<?php echo $tiRED_TO; ?>">&nbsp;</td>
	<!-- <td><input onclick="select()" class="textbox_red" type="text" name="tiRED_TB" value="<?php echo $tiRED_TB; ?>">&nbsp;</td> -->
	<td><input onclick="select()" class="textbox_red" type="text" name="tiRED_TG" value="<?php echo $tiRED_TG; ?>">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6"><hr noshade color="#008080" size="1" width="100%" align="left"></td>
  </tr>
</table>
<input type="submit" value="Uppdatera" onclick="this.disabled=true;this.value='Behandlas...'; this.form.submit();">
</form>
<?php } else { ?>
	<h1>Uppdraget sparat!</h1>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
<?php } ?>

</div>
</body>

</html>
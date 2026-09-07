<?php
spl_autoload_register(function ($class) {
	include $class . '.php';
});

session_start();
extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_SKIP);
extract($_COOKIE, EXTR_SKIP);

$tradein = new CTradeIn();

/* ---- AJAX: inline-redigering från tradein_value.php ---- */
if (!empty($ajax)) {
	$norm = function ($v) {
		return str_replace(array(' ', "\xC2\xA0", ','), array('', '', '.'), trim((string)$v));
	};

	$tiGREEN_TO = $tiGREEN_TO ?? '';   // TB
	$tiRED_TO   = $tiRED_TO   ?? '';   // tvinga TB röd (0/1)
	$tiGREEN_TG = $tiGREEN_TG ?? '';   // täckningsgrad %

	$olright = true;
	if ($tiGREEN_TO !== "" && !is_numeric($norm($tiGREEN_TO))) $olright = false;
	if ($tiGREEN_TG !== "" && !is_numeric($norm($tiGREEN_TG))) $olright = false;

	if ($olright) {
		$tradein->updateTradeInValue();
		$tradein->getTradeInValue(true);
	} else {
		http_response_code(400);
		echo 'Ogiltigt värde';
	}
	exit;
}

/* ---- Fristående sida (fallback) ---- */
if ($edit == "yes" && !$subm) {
	$rows = $tradein->getTradeInInfo();

	$tiGREEN_TO = $rows->tiGREEN_TO;   // TB
	$tiRED_TO   = $rows->tiRED_TO;     // tvinga TB röd (0/1)
	$tiGREEN_TG = $rows->tiGREEN_TG;   // täckningsgrad %
}

if ($subm) {
	$tradein->updateTradeInValue();
	$uppdate_ok = true;
}

?>
<html>

<head>
<title>Uppdatera TradeIn value</title>
<link rel="icon" type="image/png" href="/favicon.png">
<link rel="stylesheet" type="text/css" href="/css/tradein.css?v=g<?php echo date("ynjGi"); ?>" />
</head>

<?php if ($uppdate_ok) { ?>
	<body onload="top.opener && top.opener.location.reload(true);window.close()">
<?php } else { ?>
	<body onLoad="document.update_form.tiGREEN_TO.focus();document.update_form.tiGREEN_TO.select();">
<?php } ?>

<div>
<?php if (!$uppdate_ok) { ?>
<form name="update_form" method="post">
<input type="hidden" value="true" name="subm">
<table id="update_box" border="0" cellpadding="4" cellspacing="0">
  <tr>
	<td align="center">TB</td>
	<td align="center">TG (%)</td>
	<td align="center">Tvinga TB röd</td>
  </tr>
  <tr>
	<td><input onclick="select()" type="text" name="tiGREEN_TO" value="<?php echo htmlspecialchars((string)($tiGREEN_TO ?? '')); ?>" style="font-size:28px;width:110px;text-align:center;"></td>
	<td><input onclick="select()" type="text" name="tiGREEN_TG" value="<?php echo htmlspecialchars((string)($tiGREEN_TG ?? '')); ?>" style="font-size:28px;width:110px;text-align:center;"></td>
	<td align="center"><input type="checkbox" name="tiRED_TO" value="1" <?php echo !empty($tiRED_TO) ? 'checked' : ''; ?>></td>
  </tr>
  <tr>
    <td colspan="3"><hr noshade color="#008080" size="1" width="100%" align="left"></td>
  </tr>
</table>
<input type="submit" value="Uppdatera" onclick="this.disabled=true;this.value='Behandlas...'; this.form.submit();">
</form>
<?php } else { ?>
	<h1>Uppdraget sparat!</h1>
<?php } ?>

</div>
</body>

</html>

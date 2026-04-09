<?php
spl_autoload_register(function ($class) {
	include $class . '.php';
});

$tradein = new CTradeIn();

$edit     = isset($_GET['edit'])     ? $_GET['edit']     : '';
$subm     = isset($_POST['subm'])    ? $_POST['subm']    : (isset($_GET['subm'])     ? $_GET['subm']     : '');
$konumber = isset($_POST['konumber'])? $_POST['konumber']: (isset($_GET['konumber']) ? $_GET['konumber'] : '');

if ($edit == "yes" && !$subm) {
	// $rows = $tradein->getKoSystemInfo($ID);
	// $konumber = substr($rows->koNR, -2);
	$konumber = 0;
}

if ($subm) {
	
	$olright = true;
	
	if ($konumber == "") {
		$olright = false;
		$wrongmess .= "<p class=\"wrongmess\">- Fältet får inte vara tomt!</p>";
	}

	if (!is_numeric($konumber)) {
		$olright = false;
		$wrongmess .= "<p class=\"wrongmess\">- Skall vara ett nummer mellan 1-99!</p>";
	}

	if ($olright) {
		$tradein->addPingNumber($konumber);
		$uppdate_ok = true;
	}

}

?>
<html>

<head>
<title>Uppdatera KÖ nummer</title>
<link rel="icon" type="image/png" href="https://admin.cyberphoto.se/ping_incomming.png">
<style>
.align_center { text-align: center }
.align_right { text-align: right }
.align_left { text-align: left }
.middle { vertical-align: middle }
.align_justify { text-align: justify }
.uppercase { text-transform: uppercase }
.hidden, .collapsed, .block_hidden_only_for_screen {display: none;}
.wrap { white-space: normal }
.bold { font-weight: bold }
.strike { text-decoration: line-through }
.none { text-decoration: none }
.underline { text-decoration: underline }
.italic { font-style: italic }
.top { vertical-align: top }
.span_link { cursor: pointer }
.span_link:hover { text-decoration: underline }
.clear { clear: both }
.floatleft { float: left; }
.floatright { float: right; }
.clear { clear: both; }
.top5 { margin-top: 5px; }
.bottom5 { margin-bottom: 5px; }
.top10 { margin-top: 10px; }
.bottom10 { margin-bottom: 10px; }
.top20 { margin-top: 20px; }
.bottom20 { margin-bottom: 20px; }
.left5 { margin-left: 5px; }
.left10 { margin-left: 10px; }
.left20 { margin-left: 20px; }
.right5 { margin-right: 5px; }
.right10 { margin-right: 10px; }
.right20 { margin-right: 20px; }
.mark_red { color: #FF0000; }
.mark_blue { color: #0000FF; }
.mark_green { color: #009933; }
.mark_cyber { color: #85000d; }

body {
	/* background-image: url(/order/logo.png); */
	/* background-repeat: no-repeat; */
	/* background-attachment:fixed; */
	/* background-position:top right; */
	background-color: #FFFFFF; 
	font-family: Verdana; font-size: 11px; 
	margin-top: 15; 
	margin-right: 15; 
	margin-bottom: 15; 
	margin-left: 15; 

}
h1 {
	font-family: Arial; 
	font-size: 14px; 
	color: #000000 ;
	max-width: 420px;
}
td           
{
 font-family: Verdana, Arial, Helvetica;
 font-size: 11px
}
.finnish_butt {
	background: #149018;
	border-color: #28992B;
	border-width: 1px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	margin: 3px 0px;
	padding: 10px 10px;
	text-transform: uppercase;
	font-family: Verdana, Arial, Helvetica;
	font-size: 12px;
	text-align: center;
	color: #ffffff;
	font-weight: bold;
	cursor: pointer;
	vertical-align: middle;
	float: left;
	box-shadow: 3px 3px 1px #888888;
}
.textbox_white {
	padding: 0px 3px;
	background: #FFFFFF;
	font-size: 60px;
	width: 100px;
	text-align: center;
}

.tbtgcolorgreen { color:#009933; font-weight:bold }
.tbtgcolorred { color:#CC0000; font-weight:bold }
.wrongmess { color:#CC0000; font-weight:bold }
.hr_grey {
	border: 0;
	color: #868686;
	background-color: #868686;
	height: 1px;
	width: 100%;
	text-align: center;
}
#calc td {
	padding: 0px 8px;
}
#calc2 td {
	padding: 0px 8px;
}
input:disabled { 
	background:#dddddd;
}
.tcenter {
	text-align: center;
}
</style>
</head>

<?php if ($uppdate_ok) { ?>
	<!--
	<body>
	-->
	<body onload="top.opener.location.reload(true);window.close()">
<?php } else { ?>
	<body onLoad="document.update_form.konumber.focus();document.update_form.konumber.select();">
<?php } ?>

<!-- <h1>Sätt KÖ-nr</h1> -->
<?php
if ($wrongmess) {
	echo $wrongmess;
}
?>
<div>
<?php if (!$uppdate_ok) { ?>
<form name="update_form" method="post">
<input type="hidden" value=true name="subm">
<input type="hidden" value="<?php echo $artnr; ?>" name="artnr">
<table border="0" cellpadding="2" cellspacing="0">
  <tr>
	<td colspan="3"><input onclick="select()" class="textbox_white" type="text" name="konumber" value="<?php echo $konumber; ?>">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><hr noshade color="#008080" size="1" width="275" align="left"></td>
  </tr>
</table>
<input type="submit" value="Skicka till inbyte" onclick="this.disabled=true;this.value='Behandlas...'; this.form.submit();">
</form>
<?php } else { ?>
	<h1>Uppdraget sparat!</h1>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
<?php } ?>

</div>
</body>

</html>
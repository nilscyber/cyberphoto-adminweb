<?php
require_once ("CMobile.php");
$mobile = new CMobile();
$abb_ok = false;

if ($ID != "") {
	
	$rows = $mobile->getSpecAbbNew($ID);
	
	$operator = $rows->operator;
	$name = $rows->name;
	$plan_length = $rows->plan_length;
}

if ($subm) {
	
	$olright = true;
	
	if ($count == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste välja hur många abonnemang du vill lägga till</p>";
	}
	if ($count > 25) {
		$olright = false;
		$wrongmess .= "<p>- Du inte kopiera fler än 25 poster åt gången.</p>";
	}
	if (!is_numeric($count))  {
		$olright = false;
		$wrongmess .= "<p>- Antalet måste anges i siffror.</p>";
	}
	if ($olright) {
		$mobile->AbbCopy($ID,$count);
		$abb_ok = true;
	}
}
?>

<html>

<head>
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<link rel="stylesheet" type="text/css" href="/css/mobil_abonnemang_v3.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>CyberPhoto - Kopiera abonnemang</title>
</head>

<body bgcolor="#ffffff">
<h1>Kopiera abonnemang</h1>
<?php if (!$abb_ok) { ?>
<form name="sampleform">
  <input type="hidden" value=true name="subm">
  <input type="hidden" value="<?php echo $ID; ?>" name="ID">
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
    <td colspan="4"><h3><?php echo $operator . " " . $name . " " . $plan_length . " mån"; ?></h3></td>
  </tr>
  <tr>
    <td>Antal</td>
    <td colspan="3"><input type="text" name="count" size="5" value="1"></td>
  </tr>
</table>
  <p><input type="submit" value="Lägg till" name="B1"></p>
</form>
<?php } else { ?>
<p><b>Abonnemangen är nu kopierade!</b></p>
<span onclick="top.opener.location.reload(true);window.close()" style="cursor:pointer;">Klicka här för att uppdatera!</span>
<?php } ?>

</body>

</html>
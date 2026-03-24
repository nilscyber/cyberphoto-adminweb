<?php
// require_once ("CConnect.php");
// require_once ("CConnect_ms.php");
require_once ("CPromotionCode.php");
$code = new CPromotionCode();
if ($promcode == "") {
	$promcodeshow = false;
} else {
	$promcodeshow = true;
}
?>
<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
<link rel="stylesheet" type="text/css" href="../../css/frontstyle.css">
<title>Admin CyberPhoto</title>
</head>

<body topmargin="0" leftmargin="0">

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
        <td width="100%"><b><font face="Arial">Rabattkoder</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><?php $code->getLatestCodes(); ?></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <?php if ($show == "yes") {?>
      <tr>
        <td width="100%"><?php $code->getCodesMonth($year,$month); ?></td>
      </tr>
      <?php } ?>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <?php if ($show == "yes" && $promcodeshow) {?>
      <tr>
        <td width="100%"><?php $code->getCodesDetail($year,$month,$promcode); ?></td>
      </tr>
      <?php } ?>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
    </table>
    </td>
  </tr>
</table>

</body>

</html>
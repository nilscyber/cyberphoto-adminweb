<?php
require_once ("CPoll.php");
$poll = new CPoll();
?>
<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
<link rel="stylesheet" type="text/css" href="/css/admin.css">
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
    <div class="rubrik">Webbundersökningar</div>
    <p>&nbsp;</p>
    <div class="underrubrik">Aktuella undersökningar</div>
    <div><?php $poll->pollListCurrent(); ?></div>
    <p>&nbsp;</p>
    <div class="underrubrik">Kommande undersökningar</div>
    <div><?php $poll->pollListUpcomming(); ?></div>
    <p>&nbsp;</p>
    <div class="underrubrik">Tidigare undersökningar</div>
    <div><?php $poll->pollListHistory(); ?></div>
	</td>
  </tr>
</table>

</body>

</html>
<?php
require_once ("CSearchLogg.php");
$searchlogg = new CSearchLogg();
?>
<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<link rel="stylesheet" type="text/css" href="/css/mobil_abonnemang_v3.css" />
<script type="text/javascript" src="/javascript/winpop.js"></script>
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
    <div class="rubrik">Vanliga frågor senaste 30 dagarna</div>
    <p>&nbsp;</p>
	<?php if ($show != "yes") { ?>
    <div><?php $searchlogg->getSearchWordsGroup_v1(1); ?></div>
	<?php } ?>
	<?php if ($show == "yes") { ?>
    <div><?php $searchlogg->getSearchWordsGroupDetail_v1($searchstring,1); ?></div>
	<?php } ?>
    <p>&nbsp;</p>
    </td>
  </tr>
</table>

</body>

</html>
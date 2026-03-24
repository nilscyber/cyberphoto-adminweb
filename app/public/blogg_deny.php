<?php
session_start();
require_once("CBlogg.php");
$blogg = new CBlogg();

if ($accept != "") {
	
	$blogg->AcceptComment($accept);

}

if ($deny != "") {
	
	$blogg->DenyComment($deny);

}

?>

<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
<link rel="stylesheet" type="text/css" href="/css/blogg.css" />
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
        <td width="100%"><b><font face="Arial">Borttagna bloggkommentarer</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a href="blogg.php">Ej behandlade</a>&nbsp;<?php echo "(" . $blogg->getComments(0) . ")"; ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="blogg_accept.php">Behandlade</a>&nbsp;<?php echo "(" . $blogg->getComments(1) . ")"; ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="blogg_deny.php">Borttagna</a>&nbsp;<?php echo "(" . $blogg->getComments(2) . ")"; ?></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><?php $blogg->showCommentsDenyed(); ?></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
    </table>
    </td>
  </tr>
</table>

</body>

</html>
<?php
$title = !empty($title) ? $title : 'CyberPhoto kategoristruktur';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
<meta http-equiv="Content-Type"
  content="text/html; charset=iso-8859-1" />
<title><?php echo $title ?></title>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
<link rel="stylesheet" type="text/css" href="/css/admin_current.css">
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<link rel="stylesheet" type="text/css" href="/css/frontstyle.css">
<link rel="stylesheet" type="text/css" href="stilmall.css" />
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
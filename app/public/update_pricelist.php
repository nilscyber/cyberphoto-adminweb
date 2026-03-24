<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
<link rel="stylesheet" type="text/css" href="../../css/frontstyle.css">
<title>Databasen uppdateras</title>
<script language="javascript">
<?php if ($show != "") { ?>
function goTo() {
	parent.location = "http://www.cyberphoto.se/order/admin/pricelist.php?show=<?php echo $show; ?>";
	}
window.setTimeout ("goTo()", 200);
<?php } else { ?>
function goTo() {
	parent.location = "http://www.cyberphoto.se/order/admin/pricelist.php";
	}
window.setTimeout ("goTo()", 200);
<?php } ?>
</script>
</head>

<body topmargin="0" leftmargin="0">

<table border="0" cellpadding="5" cellspacing="0" height="100%" width="100%">
  <tr>
    <td width="130" height="75" style="border-bottom: 1px solid #008080" align="center">
    <?php include ("kugge.php"); ?></td>
    <td height="75" style="background-image: url('logo.jpg'); background-repeat: no-repeat; background-position: right center; border-bottom: 1px solid #008080"><b><font face="Arial">Administrera delar av CyberPhoto webbshop</font></b></td>
  </tr>
  <tr>
    <td width="130" valign="top" style="border-right: 1px solid #008080">
    <?php include ("meny.php"); ?>
    </td>
    <td valign="top">
    
<div align="center">
  <center>
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td width="100%"><b><font face="Verdana" size="1">Databasen uppdateras, vänligen vänta......</font></b></td>
    </tr>
    <tr>
      <td width="100%"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
    </tr>
    <tr>
      <td width="100%"> <img border="0" src="loader.gif" width="31" height="31"></td>
    </tr>
  </table>
  </center>
</div>

    </td>
  </tr>
</table>

</body>

</html>
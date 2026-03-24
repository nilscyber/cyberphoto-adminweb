<?php
include_once("top.php");

?>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Lägg till produkten i specifik kampanj</title>
</head>
<?php if ($olright) { ?>
	<!--
	<body bgcolor="#FFFFFF">
	-->
	<body onload="top.opener.location.reload(true);window.close()">
<?php } else { ?>
	<body onLoad="document.sampleform.change.focus();document.sampleform.change.select();">
<?php } ?>

<?php if (!$olright) { ?>
<form name="sampleform">
  <input type="hidden" value=true name="submArt">
  <input type="hidden" value="<?php echo $article; ?>" name="addartnr">
  <input type="hidden" value="<?php echo $article; ?>" name="article">
<table border="0" cellpadding="3" cellspacing="0">
  <?php if ($wrongmess2) { ?>
  <tr>
    <td colspan="4"><b><font face="Arial" size="2" color="#FF0000"><?php echo $wrongmess2; ?></font></td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="4">Artikel som läggs till:&nbsp;<b><?php echo $article; ?></b></td>
  </tr>
  <tr>
    <td>Ange kampanjens ID-nr:</td>
    <td colspan="3"><input type="text" name="change" size="4" value="<?php echo $change; ?>"></td>
  </tr>
</table>
  <p><input type="submit" value="Lägg till" name="B1"></p>
</form>
<?php } ?>

</body>

</html>
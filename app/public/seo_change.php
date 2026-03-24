<?php
include("../../incl_class.php");

if ($change > 0) {

	// $rows = $seo->getSeoInfo($_GET['page']);
	$rows = $seo->getSeoInfoChange($change);

	$addid = $rows->seoID;
	$seoPage = $rows->seoPage;
	$seoReplace = $rows->seoReplace;
	$seoCanonical_SE = $rows->seoCanonical_SE;
	$seoCanonical_FI = $rows->seoCanonical_FI;
	$seoCanonical_NO = $rows->seoCanonical_NO;
	$seoPageLinc_SE = $rows->seoPageLinc_SE;
	$seoPageLinc_FI = $rows->seoPageLinc_FI;
	$seoPageLinc_NO = $rows->seoPageLinc_NO;
	$seoTitle_SE = $rows->seoTitle_SE;
	$seoTitle_FI = $rows->seoTitle_FI;
	$seoTitle_NO = $rows->seoTitle_NO;
	$seoMetaDescription_SE = $rows->seoMetaDescription_SE;
	$seoMetaDescription_FI = $rows->seoMetaDescription_FI;
	$seoMetaDescription_NO = $rows->seoMetaDescription_NO;
	$seoH1_SE = $rows->seoH1_SE;
	$seoH1_FI = $rows->seoH1_FI;
	$seoH1_NO = $rows->seoH1_NO;
	$seoH2_SE = $rows->seoH2_SE;
	$seoH2_FI = $rows->seoH2_FI;
	$seoH2_NO = $rows->seoH2_NO;
	$seoBody_SE = $rows->seoBody_SE;
	$seoBody_FI = $rows->seoBody_FI;
	$seoBody_NO = $rows->seoBody_NO;
	$seoBody2_SE = $rows->seoBody2_SE;
	$seoBody2_FI = $rows->seoBody2_FI;
	$seoBody2_NO = $rows->seoBody2_NO;
	// echo $addid;

}
if ($create == "yes") {
	// $seoPage = $_GET['name'];	
	$seoPage = preg_replace('/\s+/', '+', urldecode($_GET['name']));
	// $seoPageLinc_SE = $_GET['page'];
	$seoPageLinc_SE = preg_replace('/\s+/', '+', $_GET['page']);
}

if ($subm) {
	
	$olright = true;
	
	$who = $_COOKIE['login_mail'];
	
	if ($seoPage == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste döpa sidan till något!</p>";
	}
	if ($seoPageLinc_SE == "") {
		$olright = false;
		$wrongmess .= "<p>- Länken till sidan får inte vara tom på Svenska!</p>";
	}
	if ($who == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste vara inloggad för att utföra detta!</p>";
	}
	if ($olright) {
		$seo->addSeoData();
		$seo_ok = true;
	}

}

if ($submC) {
	
	$olright = true;
	
	$who = $_COOKIE['login_mail'];

	if ($seoPage == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste döpa sidan till något!</p>";
	}
	if ($seoPageLinc_SE == "") {
		$olright = false;
		$wrongmess .= "<p>- Länken till sidan får inte vara tom på Svenska!</p>";
	}
	if ($who == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste vara inloggad för att utföra detta!</p>";
	}
	if ($olright) {
		$seo->updateSeoData($addid);
		$seo_ok = true;
	}

}

?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<script type="text/javascript" src="/javascript/blogg.js"></script>
<link rel="stylesheet" type="text/css" href="/css/frontstyle.css">
<style>
.container_loggin {
	float: left;
	background: #FDF6BF;
	padding: 10px; 
	color: #000000; 
	border: #B8B8B8 solid 1px;
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	border-radius: 8px;
	margin: 5px 0px 8px 0px;
	max-width: 1250px;
}
.not_loggin {
	color: #000000; 
	font-size: 18px; 
	text-decoration: none; 
	font-weight: bold; 
	font-style: italic;
}
</style>
<?php if ($change != "") { ?>
<title>Ändra SEO data!</title>
<?php } else { ?>
<title>Skapa SEO data!</title>
<?php } ?>
</head>

<?php if ($seo_ok) { ?>
	<body onload="top.opener.location.reload(true);window.close()">
<?php } else { ?>
	<body onLoad="document.searchdok.seoPage.focus();document.searchdok.seoPage.select();">
<?php } ?>

<body bgcolor="#E4E4E4" topmargin="0" leftmargin="0" onLoad="document.searchdok.headline.focus();">
<?php
if ($_COOKIE['login_ok'] != "true") {
	echo "<div class=\"container_loggin\">\n";
	echo "<span class=\"not_loggin\">Du är Ej inloggad och kommer därför inte kunna utföra åtgärden!</span>\n";
	echo "</div>\n";
	echo "<div class=\"clear\"></div>\n";
}
?>

<?php
if ($add == "yes" || $addid != "") {
	if ($wrongmess) {
		echo "<b><font face=\"Arial\" size=\"1\" color=\"#FF0000\">" . $wrongmess . "</font></b>";
	}
}
?>
<?php if (!$seo_ok) { ?>
<form name="searchdok">
  <?php if ($addid !="") { ?>
  <input type="hidden" value="<?php echo $addid; ?>" name="addid">
  <input type="hidden" value=true name="submC">
  <?php } else { ?>
  <input type="hidden" value=true name="subm">
  <input type="hidden" value="yes" name="add">
  <?php } ?>
  <table width="100%">
   <tr>
    <td><b>Sida (eget namn)</b></td>
    <td><input type="text" name="seoPage" value="<?php echo $seoPage; ?>" size="30"></td>
   </tr>
   <tr>
    <td><b>Ersätter kategori ID</b></td>
    <td>
		<select size="1" name="seoReplace">
		<option></option>
		<?php $seo->getValidCategory(); ?>
		</select>
	</td>
   </tr>
   <tr>
	<td>&nbsp;</td>
	<td><hr noshade color="#0000FF" align="left" width="95%" size="1"></td>
   </tr>
   <tr>
    <td><b>Canonical</b> <img border="0" src="sv_mini.jpg"></td>
    <td><input type="text" name="seoCanonical_SE" value="<?php echo $seoCanonical_SE; ?>" size="65"></td>
   </tr>
   <tr>
    <td><b>Canonical</b> <img border="0" src="fi_mini.jpg"></td>
    <td><input type="text" name="seoCanonical_FI" value="<?php echo $seoCanonical_FI; ?>" size="65"></td>
   </tr>
   <tr>
    <td><b>Canonical</b> <img border="0" src="no_mini.jpg"></td>
    <td><input type="text" name="seoCanonical_NO" value="<?php echo $seoCanonical_NO; ?>" size="65"></td>
   </tr>
   <tr>
	<td>&nbsp;</td>
	<td><hr noshade color="#0000FF" align="left" width="95%" size="1"></td>
   </tr>
   <tr>
    <td><b>Sidlänk</b> <img border="0" src="sv_mini.jpg"></td>
    <td><input type="text" name="seoPageLinc_SE" value="<?php echo $seoPageLinc_SE; ?>" size="65"></td>
   </tr>
   <tr>
    <td><b>Sidlänk</b> <img border="0" src="fi_mini.jpg"></td>
    <td><input type="text" name="seoPageLinc_FI" value="<?php echo $seoPageLinc_FI; ?>" size="65"></td>
   </tr>
   <tr>
    <td><b>Sidlänk</b> <img border="0" src="no_mini.jpg"></td>
    <td><input type="text" name="seoPageLinc_NO" value="<?php echo $seoPageLinc_NO; ?>" size="65"></td>
   </tr>
   <tr>
	<td>&nbsp;</td>
	<td><hr noshade color="#0000FF" align="left" width="95%" size="1"></td>
   </tr>
   <tr>
    <td><b>Titel</b> <img border="0" src="sv_mini.jpg"></td>
    <td><input type="text" name="seoTitle_SE" value="<?php echo $seoTitle_SE; ?>" size="65"></td>
   </tr>
   <tr>
    <td><b>Titel</b> <img border="0" src="fi_mini.jpg"></td>
    <td><input type="text" name="seoTitle_FI" value="<?php echo $seoTitle_FI; ?>" size="65"></td>
   </tr>
   <tr>
    <td><b>Titel</b> <img border="0" src="no_mini.jpg"></td>
    <td><input type="text" name="seoTitle_NO" value="<?php echo $seoTitle_NO; ?>" size="65"></td>
   </tr>
   <tr>
	<td>&nbsp;</td>
	<td><hr noshade color="#0000FF" align="left" width="95%" size="1"></td>
   </tr>
   <tr>
    <td valign="top"><b>Meta</b> <img border="0" src="sv_mini.jpg"></td>
	<td><textarea rows="3" name="seoMetaDescription_SE" cols="100"><?php echo $seoMetaDescription_SE; ?></textarea></td>
   </tr>
   <tr>
    <td valign="top"><b>Meta</b> <img border="0" src="fi_mini.jpg"></td>
	<td><textarea rows="3" name="seoMetaDescription_FI" cols="100"><?php echo $seoMetaDescription_FI; ?></textarea></td>
   </tr>
   <tr>
    <td valign="top"><b>Meta</b> <img border="0" src="no_mini.jpg"></td>
	<td><textarea rows="3" name="seoMetaDescription_NO" cols="100"><?php echo $seoMetaDescription_NO; ?></textarea></td>
   </tr>
   <tr>
	<td>&nbsp;</td>
	<td><hr noshade color="#0000FF" align="left" width="95%" size="1"></td>
   </tr>
   <tr>
    <td><b>H1</b> <img border="0" src="sv_mini.jpg"></td>
    <td><input type="text" name="seoH1_SE" value="<?php echo $seoH1_SE; ?>" size="65"></td>
   </tr>
   <tr>
    <td><b>H1</b> <img border="0" src="fi_mini.jpg"></td>
    <td><input type="text" name="seoH1_FI" value="<?php echo $seoH1_FI; ?>" size="65"></td>
   </tr>
   <tr>
    <td><b>H1</b> <img border="0" src="no_mini.jpg"></td>
    <td><input type="text" name="seoH1_NO" value="<?php echo $seoH1_NO; ?>" size="65"></td>
   </tr>
   <tr>
	<td>&nbsp;</td>
	<td><hr noshade color="#0000FF" align="left" width="95%" size="1"></td>
   </tr>
   <tr>
    <td><b>H2</b> <img border="0" src="sv_mini.jpg"></td>
    <td><input type="text" name="seoH2_SE" value="<?php echo $seoH2_SE; ?>" size="65"></td>
   </tr>
   <tr>
    <td><b>H2</b> <img border="0" src="fi_mini.jpg"></td>
    <td><input type="text" name="seoH2_FI" value="<?php echo $seoH2_FI; ?>" size="65"></td>
   </tr>
   <tr>
    <td><b>H2</b> <img border="0" src="no_mini.jpg"></td>
    <td><input type="text" name="seoH2_NO" value="<?php echo $seoH2_NO; ?>" size="65"></td>
   </tr>
   <tr>
	<td>&nbsp;</td>
	<td><hr noshade color="#0000FF" align="left" width="95%" size="1"></td>
   </tr>
   <tr>
    <td valign="top"><b>Brödtext 1</b> <img border="0" src="sv_mini.jpg"></td>
	<td><textarea rows="5" name="seoBody_SE" cols="100"><?php echo $seoBody_SE; ?></textarea></td>
   </tr>
   <tr>
    <td valign="top"><b>Brödtext 1</b> <img border="0" src="fi_mini.jpg"></td>
	<td><textarea rows="5" name="seoBody_FI" cols="100"><?php echo $seoBody_FI; ?></textarea></td>
   </tr>
   <tr>
    <td valign="top"><b>Brödtext 1</b> <img border="0" src="no_mini.jpg"></td>
	<td><textarea rows="5" name="seoBody_NO" cols="100"><?php echo $seoBody_NO; ?></textarea></td>
   </tr>
   <tr>
	<td>&nbsp;</td>
	<td><hr noshade color="#0000FF" align="left" width="95%" size="1"></td>
   </tr>
   <tr>
    <td valign="top"><b>Brödtext 2</b> <img border="0" src="sv_mini.jpg"></td>
	<td><textarea rows="20" name="seoBody2_SE" cols="100"><?php echo $seoBody2_SE; ?></textarea></td>
   </tr>
   <tr>
    <td valign="top"><b>Brödtext 2</b> <img border="0" src="fi_mini.jpg"></td>
	<td><textarea rows="20" name="seoBody2_FI" cols="100"><?php echo $seoBody2_FI; ?></textarea></td>
   </tr>
   <tr>
    <td valign="top"><b>Brödtext 2</b> <img border="0" src="no_mini.jpg"></td>
	<td><textarea rows="20" name="seoBody2_NO" cols="100"><?php echo $seoBody2_NO; ?></textarea></td>
   </tr>
   <tr>
	<td>&nbsp;</td>
	<td><hr noshade color="#0000FF" align="left" width="95%" size="1"></td>
   </tr>
   <tr>
    <td></td>
    <td><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" onclick="this.disabled=true;this.value='Behandlas...'; this.form.submit();" style="font-family: Verdana; font-size: 8pt; color: #000000; font-weight: bold; background-color: #C0C0C0"></td>
   </tr>
  </table>
</form>
<?php } else { ?>
<p><b>SEO data förädlad</b></p>
<span onclick="top.opener.location.reload(true);window.close()" style="cursor:pointer;">Stäng fönster</span>
<?php } ?>
</body>

</html>
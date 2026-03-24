<?php
require_once("CBlogg.php");
$blogg = new CBlogg();
// include_once("top_no.php");
if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
	echo "SV: " . $sv . "<br>\n";
	echo "NO: " . $no . "<br>\n";
	echo "FI: " . $fi . "<br>\n";
}

$headline = $_REQUEST['headline'];
$who = $_REQUEST['who'];
$bloggtext = $_REQUEST['bloggtext'];
$picture = $_REQUEST['picture'];
$bloggtime = $_REQUEST['bloggtime'];
$blogstyle = $_REQUEST['blogstyle'];
$blogmobile = $_REQUEST['blogmobile'];
$not_fi = $_REQUEST['not_fi'];
$not_no = $_REQUEST['not_no'];
$land = $_REQUEST['land'];

$subm = $_REQUEST['subm'];
$submC = $_REQUEST['submC'];
$add = $_REQUEST['add'];
$addid = $_REQUEST['addid'];
/**
$ = $_REQUEST[''];
$ = $_REQUEST[''];
*/
if ($article != "") {

	$rows = $blogg->getSpecProduct($article);
	$tillverkare = $rows->tillverkare;
	$beskrivning = $rows->beskrivning;
	$headline2 = trim($tillverkare . " " . $beskrivning);
	// $productlink = "http://www.cyberphoto.se/info.php?article=" . $article;
	$bloggtext = "\n\n\n\n<a href=\"http://www.cyberphoto.se/info.php?article=$article\">$headline2</a> << Länk till produkten";

}

if ($blogstyle == "") {
	$blogstyle = 1;
}

if ($change != "") {
	
	$rows = $blogg->getSpecBlogg($change);

	$addid = $rows->cnt;
	$bloggtime = strtotime($rows->skapad);
	$bloggtime = date("Y-m-d H:i:s", $bloggtime);
	$headline = $rows->titel;
	$bloggtext = $rows->beskrivning;
	$who = $rows->inlagd_av;
	$picture = $rows->link_pic;
	$blogstyle = $rows->blog_style;
	$bloggtype = $rows->blogType;
	$not_fi = $rows->not_fi;
	$not_no = $rows->not_no;
	// echo $bloggtype;

}

if ($subm) {
	
	$olright = true;
	
	$who = $_COOKIE['login_mail'];
	
	if ($headline == "") {
		$olright = false;
		$wrongmess .= "<p>- Rubrik får inte vara tomt!</p>";
	}
	if ($bloggtext == "") {
		$olright = false;
		$wrongmess .= "<p>- Bloggtexten får inte vara tomt!</p>";
	}
	if ($picture == "") {
		$olright = false;
		$wrongmess .= "<p>- Det måste finnas bild angiven!</p>";
	}
	if ($who == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste vara inloggad för att utföra detta!</p>";
	}
	if ($bloggtime != "") {
		if (!($blogg->isValidDateTime($bloggtime))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($blogmobile == "yes") {
		$addblogmobile = 1;
	} else {
		$addblogmobile = 0;
	}
	if ($not_fi == "yes") {
		$not_fi = -1;
	} else {
		$not_fi = 0;
	}
	if ($not_no == "yes") {
		$not_no = -1;
	} else {
		$not_no = 0;
	}
	
	if ($olright) {
		
		if ($addblogmobile == 1) {
			$blogg->AddMobileBlogg_v1($headline,$bloggtext,$who,$picture,$bloggtime,$blogstyle);
		/*
		} elseif ($land == 1) {
			$blogg->AddBloggFi_v1($headline,$bloggtext,$who,$picture,$bloggtime,$blogstyle);
		*/
		} else {
			$blogg->AddBlogg_v3($headline,$bloggtext,$who,$picture,$bloggtime,$blogstyle,$bloggtype,$not_fi,$not_no);
		}
		$blogg_ok = true;
	}

}

if ($submC) {
	
	$olright = true;
	
	$who = $_COOKIE['login_mail'];

	if ($headline == "") {
		$olright = false;
		$wrongmess .= "<p>- Rubrik får inte vara tomt!</p>";
	}
	if ($bloggtext == "") {
		$olright = false;
		$wrongmess .= "<p>- Bloggtexten får inte vara tomt!</p>";
	}
	if ($picture == "") {
		$olright = false;
		$wrongmess .= "<p>- Det måste finnas bild angiven!</p>";
	}
	if ($who == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste vara inloggad för att utföra detta!</p>";
	}
	if ($bloggtime != "") {
		if (!($blogg->isValidDateTime($bloggtime))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($not_fi == "yes") {
		$not_fi = -1;
	} else {
		$not_fi = 0;
	}
	if ($not_no == "yes") {
		$not_no = -1;
	} else {
		$not_no = 0;
	}

	if ($olright) {
		$blogg->ChangeBlogg_v3($addid,$headline,$bloggtext,$who,$picture,$bloggtime,$blogstyle,$bloggtype,$not_fi,$not_no);
		$blogg_ok = true;
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
<title>Uppdatera blogg!</title>
<?php } else { ?>
<title>Lägg till ny blogg!</title>
<?php } ?>
</head>

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
<?php if (!$blogg_ok) { ?>
<form name="searchdok" method="post" action="newblogg.php">
  <?php if ($addid !="") { ?>
  <input type="hidden" value="<?php echo $addid; ?>" name="addid">
  <input type="hidden" value=true name="submC">
  <?php } else { ?>
  <input type="hidden" value=true name="subm">
  <input type="hidden" value="yes" name="add">
  <?php } ?>
  <table>
   <?php if ($change == "") { ?>
   <tr>
    <td><b>Land</b></td>
    <td><input type="radio" value="0"<?php if (!$fi) { echo " checked"; } ?> name="land">Sverige&nbsp;&nbsp;<input type="radio" value="1"<?php if ($fi) { echo " checked"; } ?> name="land">Finland</td>
   </tr>
   <?php } ?>
   <?php if ($fi) { ?>
	   <tr>
		<td><b>Typ</b></td>
		<td><input type="radio" value="28"<?php if ($bloggtype == "" || $bloggtype == 28) { echo " checked"; } ?> name="bloggtype">Blogg&nbsp;&nbsp;<input type="radio" value="29"<?php if ($bloggtype == 29) { echo " checked"; } ?> name="bloggtype">Test&nbsp;&nbsp;<input type="radio" value="30"<?php if ($bloggtype == 30) { echo " checked"; } ?> name="bloggtype">Nyhet</td>
	   </tr>
   <?php } else { ?>
	   <tr>
		<td><b>Typ</b></td>
		<td><input type="radio" value="19"<?php if ($bloggtype == "" || $bloggtype == 19) { echo " checked"; } ?> name="bloggtype">Blogg&nbsp;&nbsp;<input type="radio" value="1"<?php if ($bloggtype == 1) { echo " checked"; } ?> name="bloggtype">Test&nbsp;&nbsp;<input type="radio" value="2"<?php if ($bloggtype == 2) { echo " checked"; } ?> name="bloggtype">Nyhet&nbsp;&nbsp;<input type="radio" value="23"<?php if ($bloggtype == 23 || $mobile_news == "yes") { echo " checked"; } ?> name="bloggtype">Mobil</td>
	   </tr>
   <?php } ?>
   <tr>
    <td><b>Visa Ej i</b></td>
    <td><input type="checkbox" name="not_fi" value="yes" <?php if ($not_fi == "-1") { ?> checked <?php } ?>>Finland&nbsp;&nbsp;<input type="checkbox" name="not_no" value="yes" <?php if ($not_no == "-1") { ?> checked <?php } ?>>Norge</td>
   </tr>
   <tr>
    <td><b>Rubrik</b></td>
    <td><input type="text" name="headline" value="<?php echo $headline; ?>" size="45" style="font-family: Verdana; font-size: 10px"></td>
   </tr>
   <?php if ($change == "4545") { ?>
   <tr>
    <td><b>Mobil bloggen</b></td>
    <td><input type="checkbox" name="blogmobile" value="yes" <?php if ($blogmobile == "yes") { echo "checked"; } ?>></td>
   </tr>
   <?php } ?>
   <tr>
    <td valign="top"><b>Bloggtext</b></td>
    <td><textarea rows="21" name="bloggtext" cols="125" style="font-family: Verdana; font-size: 10px"><?php echo $bloggtext; ?></textarea></td>
   </tr>
   <tr>
    <td><b>Bildfil</b></td>
    <td><input type="text" name="picture" value="<?php echo $picture; ?>" size="45" style="font-family: Verdana; font-size: 10px"> (ska fysiskt ligga i mappen blogg)</td>
   </tr>
   <?php if ($change != "") { ?>
   <tr>
    <td valign="top"></td>
    <td colspan="2"><img border="0" width="300" src="/blogg/<?php echo $picture; ?>"></td>
   </tr>
   <?php } ?>
   <tr>
    <td><b>Bildtyp</b></td>
    <td><input type="radio" value="1"<?php if ($blogstyle == 1) { echo " checked"; } ?> name="blogstyle">Bred&nbsp;&nbsp;<input type="radio" value="0"<?php if ($blogstyle == 0) { echo " checked"; } ?> name="blogstyle">Liten (högerställd)</td>
   </tr>
   <tr>
    <td><b>Tidpunkt</b></td>
    <td><input type="text" name="bloggtime" size="20" value="<?php if ($bloggtime == "") { echo date("Y-m-d H:i:s", time()); } else { echo $bloggtime; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
   </tr>
   <tr>
    <td></td>
    <td><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" onclick="this.disabled=true;this.value='Behandlas...'; this.form.submit();" style="font-family: Verdana; font-size: 8pt; color: #000000; font-weight: bold; background-color: #C0C0C0"></td>
   </tr>
   <?php if ($article == "") { ?>
   <tr>
    <td><b>Sidlänk</b></td>
    <td><?php echo "&lt;a href=&quot;http://www.cyberphoto.se/info.php?article=niD3s&quot;&gt;Texten som beskriver länken&lt;/a&gt;"; ?></td>
   </tr>
   <tr>
    <td><b>Bildlänk</b></td>
    <td><?php echo "&lt;a target=&quot;_blank&quot; href=&quot;http://www.cyberphoto.se/blogg/bild.jpg&quot;&gt;Klicka här för större bild&lt;/a&gt;"; ?></td>
   </tr>
   <?php } ?>
  </table>
</form>
<?php } else { ?>
<p><b>Blogg inlagd!</b></p>
<span onclick="top.opener.location.reload(true);window.close()" style="cursor:pointer;">Stäng fönster</span>
<?php } ?>
</body>

</html>
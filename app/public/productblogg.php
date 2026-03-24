<?php
require_once("CBlogg.php");
$blogg = new CBlogg();

function escape_string_for_regex($str)
{
        
        $patterns = array('/\//', '/\^/', '/\./', '/\$/', '/\|/', '/\(/', '/\)/', '/\[/', '/\]/', '/\*/', '/\+/', '/\?/', '/\{/', '/\}/', '/\,/');
        $replace = array('\/', '\^', '\.', '\$', '\|', '\(', '\)', '\[', '\]', '\*', '\+', '\?', '\{', '\}', '\,');
        
        return preg_replace($patterns,$replace, $str);
}

if ($addblogg == "yes") {

	$rows = $blogg->getSpecProduct($article);
	$tillverkare = $rows->tillverkare;
	$beskrivning = $rows->beskrivning;
	$headline = $tillverkare . " " . $beskrivning;
	$bilden = $rows->bild;
	if (preg_match('/^\../', $bilden)) {
		$bilden = preg_replace("/\.\.\//", "", $bilden);
		$bilden = "http://www.cyberphoto.se/thumbs/large/" . $bilden;
	} else {
		$bilden = "http://www.cyberphoto.se/thumbs/large/bilder/" . $bilden;
	}
	$picture = $bilden;
	$productlink = "http://www.cyberphoto.se/info.php?article=" . $article;

}

if ($addblogg == "yes") {
	$addblogg = 1;
}

if ($change != "") {

	$rows = $blogg->getSpecBlogg($change);

	$addid = $rows->cnt;
	$bloggtime = strtotime($rows->skapad);
	$bloggtime = date("Y-m-d H:i:s", $bloggtime);
	$headline = $rows->titel;
	$headline_fi = $rows->titel_fi;
	$bloggtext = $rows->beskrivning;
	$bloggtext_fi = $rows->beskrivning_fi;
	$who = $rows->inlagd_av;
	$picture = $rows->link_pic;
	$productlink = $rows->link;
	$bloggtype = $rows->blogType;

	$headline = trim($headline);
	$bloggtext = trim($bloggtext);
	$headline_fi = trim($headline_fi);
	$bloggtext_fi = trim($bloggtext_fi);
	
}

if ($subm) {
	
	$olright = true;
	
	if ($bloggtype == "0" || $bloggtype == "4" || $bloggtype == "8" || $bloggtype == "12" || $bloggtype == "15" || $bloggtype == "18" || $bloggtype == "20") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vad bloggen avser!</p>";
	}
	if ($addblogg == "1") {
		if ($headline == "") {
			$olright = false;
			$wrongmess .= "<p>- Rubrik får inte vara tomt!</p>";
		}
	}
	if ($bloggtext == "") {
		$olright = false;
		$wrongmess .= "<p>- Bloggtexten får inte vara tomt!</p>";
	}
	if ($addblogg == "1") {
		if ($productlink == "") {
			$olright = false;
			$wrongmess .= "<p>- Det måste finnas en länk angiven!</p>";
		}
		if ($picture == "") {
			$olright = false;
			$wrongmess .= "<p>- Det måste finnas bild angiven!</p>";
		}
	}
	if ($who == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vem som bloggar!</p>";
	}
	if ($bloggtime != "") {
		if (!($blogg->isValidDateTime($bloggtime))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}

	if ($olright) {
		$headline = trim($headline);
		$bloggtext = trim($bloggtext);
		$headline_fi = trim($headline_fi);
		$bloggtext_fi = trim($bloggtext_fi);
		$bloggtext = preg_replace('/\'/', '´', $bloggtext);
		$bloggtext = preg_replace('/\\\/', '', $bloggtext);
		$bloggtext_fi = preg_replace('/\'/', '´', $bloggtext_fi);
		$bloggtext_fi = preg_replace('/\\\/', '', $bloggtext_fi);
		$blogg->AddProductBlogg($headline,$bloggtext,$who,$picture,$bloggtime,$bloggtype,$headline_fi,$bloggtext_fi,$productlink);
		$blogg_ok = true;
	}

}

if ($submC) {
	
	$olright = true;
	
	if ($bloggtype == "0" || $bloggtype == "4" || $bloggtype == "8" || $bloggtype == "12" || $bloggtype == "15" || $bloggtype == "18" || $bloggtype == "20") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vad bloggen avser!</p>";
	}
	if ($addblogg == "1") {
		if ($headline == "") {
			$olright = false;
			$wrongmess .= "<p>- Rubrik får inte vara tomt!</p>";
		}
	}
	if ($bloggtext == "") {
		$olright = false;
		$wrongmess .= "<p>- Bloggtexten får inte vara tomt!</p>";
	}
	if ($addblogg == "1") {
		if ($productlink == "") {
			$olright = false;
			$wrongmess .= "<p>- Det måste finnas en länk angiven!</p>";
		}
		if ($picture == "") {
			$olright = false;
			$wrongmess .= "<p>- Det måste finnas bild angiven!</p>";
		}
	}
	if ($who == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vem som bloggar!</p>";
	}
	if ($bloggtime != "") {
		if (!($blogg->isValidDateTime($bloggtime))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}

	if ($olright) {
		// $bloggtext = escape_string_for_regex($bloggtext);
		$bloggtext = preg_replace('/\'/', '´', $bloggtext);
		$bloggtext = preg_replace('/\\\/', '', $bloggtext);
		$bloggtext_fi = preg_replace('/\'/', '´', $bloggtext_fi);
		$bloggtext_fi = preg_replace('/\\\/', '', $bloggtext_fi);
		$blogg->ChangeProductBlogg($addid,$headline,$bloggtext,$who,$picture,$bloggtime,$bloggtype,$headline_fi,$bloggtext_fi,$productlink);
		$blogg_ok = true;
	}

}

?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<script type="text/javascript" src="/javascript/blogg.js"></script>
<link rel="stylesheet" type="text/css" href="/css/frontstyle.css">
<title>Lägg till ny blogg!</title>
</head>

<body bgcolor="#E4E4E4" topmargin="0" leftmargin="0" onLoad="document.searchdok.headline.focus();">
<?php
if ($add == "yes" || $addid != "") {
	if ($wrongmess) {
		echo "&nbsp;&nbsp;<b><font face=\"Arial\" size=\"2\" color=\"#FF0000\">" . $wrongmess . "</font></b>";
	}
}
?>
<?php if (!$blogg_ok) { ?>
<form name="searchdok">
  <?php if ($addid !="") { ?>
  <input type="hidden" value="<?php echo $addid; ?>" name="addid">
  <input type="hidden" value=true name="submC">
  <?php } else { ?>
  <input type="hidden" value=true name="subm">
  <input type="hidden" value="yes" name="add">
  <?php } ?>
  <input type="hidden" value="<?php echo $addsys; ?>" name="addsys">
  <input type="hidden" value="<?php echo $addblogg; ?>" name="addblogg">
  <input type="hidden" value="<?php echo $article; ?>" name="article">
  <table border="0" cellpadding="5" cellspacing="0">
   <tr>
    <td><b>Avser</b></td>
    <td><select size="1" name="bloggtype" style="font-family: Verdana; font-size: 10px">
		<?php if ($addsys == "yes") { ?>
        <?php $blogg->getBloggType("system"); ?>
		<?php } else { ?>
        <?php $blogg->getBloggType("product"); ?>
		<?php } ?>
        </select>
    </td>
   </tr>
	<?php if ($addsys != "yes") { ?>
   <tr>
    <td><b>Rubrik</b></td>
    <td><input type="text" name="headline" value="<?php echo $headline; ?>" size="45" style="font-family: Verdana; font-size: 10px"><font face="Verdana" size="1">&nbsp;(tänk på att den skall rymmas på fronten)</font></td>
   </tr>
	<?php } ?>
   <tr>
    <td valign="top"><b><?php if ($addsys == "yes") { echo "Information"; } else { echo "Bloggtext"; } ?></b></td>
    <td><textarea rows="6" name="bloggtext" cols="85" style="font-family: Verdana; font-size: 10px"><?php echo $bloggtext; ?></textarea></td>
   </tr>
	<?php if ($addsys != "yes") { ?>
   <tr>
    <td><b>Rubrik FI</b></td>
    <td><input type="text" name="headline_fi" value="<?php echo $headline_fi; ?>" size="45" style="font-family: Verdana; font-size: 10px; background-color: #99CCFF"></td>
   </tr>
	<?php } ?>
   <tr>
    <td valign="top"><b><?php if ($addsys == "yes") { echo "Information FI"; } else { echo "Bloggtext FI"; } ?></b></td>
    <td><textarea rows="6" name="bloggtext_fi" cols="85" style="font-family: Verdana; font-size: 10px; background-color: #99CCFF"><?php echo $bloggtext_fi; ?></textarea></td>
   </tr>
	<?php if ($addsys != "yes") { ?>
   <tr>
    <td><b>Länk</b></td>
    <td><input type="text" name="productlink" value="<?php echo $productlink; ?>" size="85" style="font-family: Verdana; font-size: 10px"></td>
   </tr>
   <tr>
    <td><b>Bild</b></td>
    <td><input type="text" name="picture" value="<?php echo $picture; ?>" size="85" style="font-family: Verdana; font-size: 10px"></td>
   </tr>
	<?php } ?>
   <tr>
    <td><b>Vem bloggar?</b></td>
    <td><select size="1" name="who" style="font-family: Verdana; font-size: 10px">
        <option></option>
        <?php $blogg->getAnstallda(); ?>
        </select>
    </td>
   </tr>
   <tr>
    <td><b><?php if ($addsys == "yes") { echo "Skall synas till"; } else { echo "Tidpunkt"; } ?></b></td>
    <td><input type="text" name="bloggtime" size="20" value="<?php if ($bloggtime == "" || $now == "yes") { echo date("Y-m-d H:i:s", time()); } else { echo $bloggtime; }  ?>" style="font-family: Verdana; font-size: 8pt"> <?php if ($addsys == "yes") { ?>(<a style="text-decoration: underline;" href="/order/admin/productblogg.php?change=<?php echo $addid; ?>&addsys=yes&now=yes">avsluta tiden som är nu, klicka här</a>)<?php } ?></td>
   </tr>
   <tr>
    <td></td>
    <td><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" onclick="this.disabled=true;this.value='Behandlas...'; this.form.submit();" style="font-family: Verdana; font-size: 8pt; color: #000000; font-weight: bold; background-color: #C0C0C0"></td>
   </tr>
  </table>
</form>
	<?php if ($addsys != "yes") { ?>
	<p align="center"><font face="Verdana" size="1">Ser du bilden nedan? Gör du inte det måste detta åtgärdas först!</p>
	<p align="center"><img border="1" src="<?php echo $picture; ?>"></font></p>
	<?php } ?>
<?php } else { ?>
<p><b>Blogg inlagd!</b></p>
<span onclick="top.opener.location.reload(true);window.close()" style="cursor:pointer;">Stäng fönster</span>
<?php } ?>
</body>

</html>
<?php
require_once ("CMobile.php");
$mobile = new CMobile();

if ($edit == "1") {

	$rows = $mobile->getSpecAbb($artid);

	$abb_id = $rows->mobile_plans_id;
	$abb_from = preg_replace('/:[0-9][0-9][0-9]/','', $rows->validfrom);
	$abb_from = strtotime($abb_from);
	$abb_from = date("Y-m-d H:i:s", $abb_from);
	if ($now == "yes") {
		$abb_to = date("Y-m-d H:i:s", time());
	} else {
		$abb_to = preg_replace('/:[0-9][0-9][0-9]/','', $rows->validto);
		$abb_to = strtotime($abb_to);
		$abb_to = date("Y-m-d H:i:s", $abb_to);
	}
	$abb_name = $rows->name;
	$abb_description = $rows->description;
	$abb_description_new = $rows->description_new;
	$abb_iswebstorefeatured = $rows->iswebstorefeatured;
	$abb_artnr = $rows->artnr;
	$abb_plan_length = $rows->plan_length;
	$abb_isactive = $rows->isactive;
	$abb_commissionAmt = $rows->commissionAmt;
	$abb_discountCustomerAmt = $rows->discountCustomerAmt;
	$abb_month_fee = $rows->month_fee;
	$abb_createdby = $rows->createdby;
	$abb_operator_id = $rows->operator_id;
	$abb_foretag = $rows->foretag;
		if ($abb_operator_id == 1) {
			$abb_operator_name = "Tele2";
		} elseif ($abb_operator_id == 2) {
			$abb_operator_name = "Telia";
		} elseif ($abb_operator_id == 3) {
			$abb_operator_name = "Halebop";
		}
}

if ($copypost != "") {

	$rows = $start->getSpecFrontBanner($copypost);
	
	$addidc = $rows->frontID;
	// $addfrom = substr ($rows->frontDateFrom, 0, 19);
	$addfrom = date("Y-m-d H:i:s", time());
	$addto = substr ($rows->frontDateTo, 0, 19);
	$section = $rows->frontSection;
	$addsection = $rows->frontSection;
	$addpicture = $rows->frontPicture;
	$addartnr = $rows->frontArtNr;
	$addlinc = $rows->frontLinc;
	$addstore = $rows->frontAllowNull;
	$addleverantor = $rows->frontLeverantor;
	$addprio = $rows->frontPrio;

}


if ($delete != "") {
	$start->BannerAdminDelete($delete,$section);
}
if ($subm) {
	
	$olright = true;
	$section = $addsection;
	
	if ($addsitecopy == 2 || $addsitecopy == 3) {
		$addsite = $addsitecopy;
	} else {
		$addsite = $_SESSION['adminsite'];
	}
	
	if ($addstore == "yes") {
		$addstore = -1;
	} else {
		$addstore = 0;
	}

	if ($addprio == "yes") {
		$addprio = -1;
	} else {
		$addprio = 0;
	}
	
	if ($addfrom == "") {
		$olright = false;
		$wrongmess .= "<p>- Datum för när den skall publiseras får inte vara tomt!</p>";
	}
	if ($addfrom != "") {
		if (!($start->isValidDateTime($addfrom))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addto == "") {
		$olright = false;
		$wrongmess .= "<p>- Datum för hur länge den skall ligga får inte vara tomt!</p>";
	}
	if ($addto != "") {
		if (!($start->isValidDateTime($addto))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addpicture == "") {
		$olright = false;
		$wrongmess .= "<p>- Bild måste anges. Skall ligga i start3 mappen!</p>";
	}
	if ($addartnr == "" && $addlinc == "" && (!eregi(".php$", $addpicture))) {
		$olright = false;
		$wrongmess .= "<p>- Antingen skall artikel nummer eller länk vara ifyllt!</p>";
	}
	if ($addlinc != "") {
		if (!eregi(".php", $addlinc)) {
		$olright = false;
		$wrongmess .= "<p>- Länken måste vara en php sida!</p>";
		}
	}
	if ($addartnr != "") {
		if (!($start->check_artikel_status($addartnr) == $addartnr)) {
		// if (!($start->check_artikel_status($addartnr))) {
			$olright = false;
			$wrongmess .= "<p>- Detta artikel nummer finns inte. Vänligen kolla upp detta! (måste skrivas exakt)</p>";
		}
	}
	if ($addstore == "0" && $addartnr == "") {
		$olright = false;
		$wrongmess .= "<p>- Om du inte tillåter slut i lager kan inte artikel nummer vara tomt!</p>";
	}
	if ($addcreatedby == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vem du är!</p>";
	}

	if ($olright) {
		$start->BannerAdminAdd($addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addsite,$addprio);
	}

}
if ($submC) {
	
	$olright = true;
	
	if ($abb_iswebstorefeatured == "yes") {
		$abb_iswebstorefeatured = -1;
	} else {
		$abb_iswebstorefeatured = 0;
	}
	
	if ($abb_isactive == "yes") {
		$abb_isactive = -1;
	} else {
		$abb_isactive = 0;
	}

	if ($abb_foretag == "yes") {
		$abb_foretag = 1;
	} else {
		$abb_foretag = 0;
	}
	
	if ($abb_from == "") {
		$olright = false;
		$wrongmess .= "<p>- Datum för när den skall publiseras får inte vara tomt!</p>";
	}
	if ($abb_from != "") {
		if (!($mobile->isValidDateTime($abb_from))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($abb_to == "") {
		$olright = false;
		$wrongmess .= "<p>- Datum för hur länge den skall ligga får inte vara tomt!</p>";
	}
	if ($abb_to != "") {
		if (!($mobile->isValidDateTime($abb_to))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}

	if ($abb_artnr == "") {
		$olright = false;
		$wrongmess .= "<p>- Artikel nummret får inte vara tomt!</p>";
	}

	if ($abb_artnr != "" && $abb_isactive == -1) {
		if ($mobile->check_artikel_status_mobile_plans($abb_artnr,$abb_id)) {
			$olright = false;
			$wrongmess .= "<p>- Detta artikel nummer finns redan på aktivt abonnemang. Välj ett annat!</p>";
		}
		if (!($mobile->check_artikel_status($abb_artnr))) {
			$olright = false;
			$wrongmess .= "<p>- Detta artikel finns inte i vårt affärssystem. Skapa detta först!</p>";
		}
	}

	if ($abb_name == "") {
		$olright = false;
		$wrongmess .= "<p>- Vad heter abonnemanget!</p>";
	}
	if ($abb_plan_length == "") {
		$olright = false;
		$wrongmess .= "<p>- Hur lång bindningstid har abonnemangen.</p>";
	}
	if ($abb_plan_length != "") {
		if (!is_numeric($abb_plan_length))  {
			$olright = false;
			$wrongmess .= "<p>- Bindningstid måste anges i siffror.</p>";
		}
	}
	if ($abb_commissionAmt == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vår provision. Även om den är noll.</p>";
	}
	if ($abb_commissionAmt != "") {
		if (!is_numeric($abb_commissionAmt))  {
			$olright = false;
			$wrongmess .= "<p>- Provisionen måste anges i siffror</p>";
		}
	}
	if ($abb_discountCustomerAmt == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange rabatt till kund. Även om den är noll.</p>";
	}
	if ($abb_discountCustomerAmt != "") {
		if (!is_numeric($abb_discountCustomerAmt))  {
			$olright = false;
			$wrongmess .= "<p>- Rabatten måste anges i siffror</p>";
		}
	}
	if ($abb_month_fee != "") {
		if (!is_numeric($abb_month_fee))  {
			$olright = false;
			$wrongmess .= "<p>- Månadsavgiften måste anges i siffror</p>";
		}
	}
	if ($abb_createdby == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vem du är!</p>";
	}
	if ($abb_operator_id == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste ange vilken operatör det avses!</p>";
	}
	if ($olright) {
		$mobile->AbbAdminChange($abb_id,$abb_name,$abb_iswebstorefeatured,$abb_isactive,$abb_from,$abb_to,$abb_artnr,$abb_plan_length,$abb_commissionAmt,$abb_discountCustomerAmt,$abb_createdby,$abb_operator_id,$abb_description,$abb_month_fee,$abb_foretag,$content);
		// echo "<span onclick=\"top.opener.location.reload(true);window.close()\" style=\"cursor:pointer;\">Klicka här för att uppdatera sidan</span>\n";
		// exit;
	}
}

?>

<html>

<head>
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<link rel="stylesheet" type="text/css" href="/css/mobil_abonnemang_v3.css" />
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Ändra abonnemanget</title>
<script language="javascript" type="text/javascript" src="/order/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	//plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
	plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	// theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,

	// Example content CSS (should be your site CSS)
	content_css : "../../css/product.css",

	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "lists/template_list.js",
	external_link_list_url : "lists/link_list.js",
	external_image_list_url : "lists/image_list.js",
	media_external_list_url : "lists/media_list.js",	
	relative_urls : false,
	document_base_url : "<?php echo $edit->rel_link; ?>",
	convert_urls : true, 
	// Replace values for the template plugin
	template_replace_values : {
		username : "Some User",
		staffid : "991234"
	}
});

</script>

</head>

<?php if ($olright) { ?>
	<body onload="top.opener.location.reload(true);window.close()" bgcolor="#FFECB0">
<?php } else { ?>
	<body bgcolor="#FFECB0">
<?php } ?>
<form>
  <?php if ($abb_id !="") { ?>
  <input type="hidden" value="<?php echo $abb_id; ?>" name="abb_id">
  <input type="hidden" value=true name="submC">
  <?php } else { ?>
  <input type="hidden" value=true name="subm">
  <input type="hidden" value="yes" name="add">
  <input type="hidden" value="<?php echo $addidc; ?>" name="addidc">
  <?php } ?>
 <div style="float: left; padding: 25 0 0 15;">
<table border="0" cellpadding="3" cellspacing="0">
  <?php if ($wrongmess) { ?>
  <tr>
    <td colspan="4"><b><font face="Arial" size="2" color="#FF0000"><?php echo $wrongmess; ?></font></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="4" class="abbheader"><?php echo $abb_operator_name . " " . $abb_name . " " . $abb_plan_length . " mån"; ?></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td>Gäller från:</td>
    <td><input type="text" name="abb_from" size="20" value="<?php if ($abb_from == "") { echo date("Y-m-d H:i:s", time()); } else { echo $abb_from; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
    <td>Gäller till:</td>
    <td><input type="text" name="abb_to" size="20" value="<?php if ($abb_to == "") { echo date("Y-m-d 23:59:59",mktime(0,0,0,date("n")+1,1-1,date("Y"))); } else { echo $abb_to; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
  </tr>
  <tr>
    <td>Operatör:</td>
    <td>&nbsp;<?php $mobile->getMobileOperatorInt($abb_operator_id); ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Abonnemang:</td>
    <td colspan="3"><input type="text" name="abb_name" size="55" value="<?php echo $abb_name; ?>" style="font-family: Verdana; font-size: 8pt"></td>
  </tr>
  <tr>
    <td>Aktiv:</td>
    <td><input type="checkbox" name="abb_isactive" value="yes" <?php if ($abb_isactive == -1) { ?> checked <?php } ?>></td>
    <td><?php if ($abb_isactive == -1) { ?><img border="0" src="/order/admin/status_green.gif"><?php } else { ?><img border="0" src="/order/admin/status_red.gif"><?php } ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Visas i webshopen:</td>
    <td><input type="checkbox" name="abb_iswebstorefeatured" value="yes" <?php if ($abb_iswebstorefeatured == -1) { ?> checked <?php } ?>></td>
    <td><?php if ($abb_iswebstorefeatured == -1) { ?><img border="0" src="/order/admin/status_green.gif"><?php } else { ?><img border="0" src="/order/admin/status_red.gif"><?php } ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Företagsabonnemang:</td>
    <td><input type="checkbox" name="abb_foretag" value="yes" <?php if ($abb_foretag == 1) { ?> checked <?php } ?>></td>
    <td><?php if ($abb_foretag == 1) { ?><img border="0" src="/order/admin/status_green.gif"><?php } else { ?><img border="0" src="/order/admin/status_red.gif"><?php } ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Bindningstid:</td>
    <td><input type="text" name="abb_plan_length" size="3" value="<?php echo $abb_plan_length; ?>" style="font-family: Verdana; font-size: 8pt"> mån</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Vårt artikelnummer:</td>
    <td><input type="text" name="abb_artnr" size="7" value="<?php echo $abb_artnr; ?>" style="font-family: Verdana; font-size: 8pt"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Provision:</td>
    <td><input type="text" name="abb_commissionAmt" size="7" value="<?php echo $abb_commissionAmt; ?>" style="font-family: Verdana; font-size: 8pt"> SEK</td>
    <td>Rabatt:</td>
    <td><input type="text" name="abb_discountCustomerAmt" size="7" value="<?php echo $abb_discountCustomerAmt; ?>" style="font-family: Verdana; font-size: 8pt"> SEK</td>
  </tr>
  <tr>
    <td><b>Behållning:</b></td>
    <td><input type="text" name="keep_calculate" size="6" value="<?php echo $abb_commissionAmt - $abb_discountCustomerAmt;  ?>" style="font-family: Verdana; font-size: 8pt; font-weight: bold; color:#000000" disabled> SEK</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>SEK / Månad:</td>
    <td><input type="text" name="abb_month_fee" size="6" value="<?php echo $abb_month_fee;  ?>" style="font-family: Verdana; font-size: 8pt; color:#000000"> SEK</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Skapad av:</td>
    <td>
    <select size="1" name="abb_createdby" style="font-family: Verdana; font-size: 8pt">
    <option></option>
    <?php $mobile->getAnstallda(); ?>
    </select>
    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
<div style="float: left; padding: 25 0 0 15;">
<textarea name="content" cols="85" rows="35"><?php echo $abb_description_new; ?></textarea>
</div>
<div style="clear: left;">
  <p><input type="submit" value="Utför ändring" name="B1"></p>
</form>

</body>

</html>
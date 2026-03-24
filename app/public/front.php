<?php
session_start();

if ($nollstall == "yes") {
	unset ($_SESSION['adminsite']);
}

if ($choose_site != "") {

	$_SESSION['adminsite'] = $choose_site;

	/*
	if ($choose_site == 1) {
		$_SESSION['adminsite'] = 1;
	} elseif ($choose_site == 2) {
		$_SESSION['adminsite'] = 2;
	} elseif ($choose_site == 3) {
		$_SESSION['adminsite'] = 3;
	} elseif ($choose_site == 5) {
		$_SESSION['adminsite'] = 5;
	} elseif ($choose_site == 6) {
		$_SESSION['adminsite'] = 6;
	} elseif ($choose_site == 7) {
		$_SESSION['adminsite'] = 7;
	} elseif ($choose_site == 8) {
		$_SESSION['adminsite'] = 8;
	} elseif ($choose_site == 9) {
		$_SESSION['adminsite'] = 9;
	} elseif ($choose_site == 10) {
		$_SESSION['adminsite'] = 10;
	} elseif ($choose_site == 11) {
		$_SESSION['adminsite'] = 11;
	} elseif ($choose_site == 12) {
		$_SESSION['adminsite'] = 12;
	} elseif ($choose_site == 13) {
		$_SESSION['adminsite'] = 13;
	} elseif ($choose_site == 14) {
		$_SESSION['adminsite'] = 14;
	} else {
		$_SESSION['adminsite'] = 99;
	}
	*/

}

// require_once ("CConnect.php");
// require_once ("CConnect_ms.php");
require_once ("CCheckStart.php");
$start = new CCheckStart();

if ($change != "") {

$rows = $start->getSpecFrontBanner($change);

$addid = $rows->frontID;
$addfrom = substr ($rows->frontDateFrom, 0, 19);
if ($now == "yes") {
$addto = date("Y-m-d H:i:s", time());
} else {
$addto = substr ($rows->frontDateTo, 0, 19);
}
$section = $rows->frontSection;
$addsection = $rows->frontSection;
$addpicture = $rows->frontPicture;
$addartnr = $rows->frontArtNr;
$addlinc = $rows->frontLinc;
$addstore = $rows->frontAllowNull;
$addcomment = $rows->frontComment;
$addleverantor = $rows->frontLeverantor;
$addcreatedby = $rows->frontCreatedBy;
$addprio = $rows->frontPrio;
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
	$start->FrontAdminDelete($delete,$section);
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
		$wrongmess .= "<p>- Datum f�r n�r den skall publiseras f�r inte vara tomt!</p>";
	}
	if ($addfrom != "") {
		if (!($start->isValidDateTime($addfrom))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt fr�n datum. Skall formateras s� h�r, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addto == "") {
		$olright = false;
		$wrongmess .= "<p>- Datum f�r hur l�nge den skall ligga f�r inte vara tomt!</p>";
	}
	if ($addto != "") {
		if (!($start->isValidDateTime($addto))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt till datum. Skall formateras s� h�r, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addpicture == "") {
		$olright = false;
		$wrongmess .= "<p>- Bild m�ste anges. Skall ligga i start3 mappen!</p>";
	}
	if ($addartnr == "" && $addlinc == "" && (!eregi(".php$", $addpicture))) {
		$olright = false;
		$wrongmess .= "<p>- Antingen skall artikel nummer eller l�nk vara ifyllt!</p>";
	}
	/*
	if ($addlinc != "") {
		if (!eregi(".php", $addlinc)) {
		$olright = false;
		$wrongmess .= "<p>- L�nken m�ste vara en php sida!</p>";
		}
	}
	*/
	if ($addartnr != "") {
		if (!($start->check_artikel_status($addartnr) == $addartnr)) {
		// if (!($start->check_artikel_status($addartnr))) {
			$olright = false;
			$wrongmess .= "<p>- Detta artikel nummer finns inte. V�nligen kolla upp detta! (m�ste skrivas exakt)</p>";
		}
	}
	if ($addstore == "0" && $addartnr == "") {
		$olright = false;
		$wrongmess .= "<p>- Om du inte till�ter slut i lager kan inte artikel nummer vara tomt!</p>";
	}
	if ($addcreatedby == "") {
		$olright = false;
		$wrongmess .= "<p>- Du m�ste ange vem du �r!</p>";
	}

	if ($olright) {
		$start->FrontAdminAdd($addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addsite,$addprio);
	}

}
if ($submC) {
	
	$olright = true;
	$section = $addsection;
	
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
		$wrongmess .= "<p>- Datum f�r n�r den skall publiseras f�r inte vara tomt!</p>";
	}
	if ($addfrom != "") {
		if (!($start->isValidDateTime($addfrom))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt fr�n datum. Skall formateras s� h�r, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addto == "") {
		$olright = false;
		$wrongmess .= "<p>- Datum f�r hur l�nge den skall ligga f�r inte vara tomt!</p>";
	}
	if ($addto != "") {
		if (!($start->isValidDateTime($addto))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt till datum. Skall formateras s� h�r, 2009-01-01 15:00:00</p>";
		}
	}
	if ($addpicture == "") {
		$olright = false;
		$wrongmess .= "<p>- Bild m�ste anges!</p>";
	}
	if ($addartnr == "" && $addlinc == "" && (!eregi(".php$", $addpicture))) {
		$olright = false;
		$wrongmess .= "<p>- Antingen skall artikel nummer eller l�nk vara ifyllt!</p>";
	}
	/*
	if ($addlinc != "") {
		if (!eregi(".php", $addlinc)) {
		$olright = false;
		$wrongmess .= "<p>- L�nken m�ste vara en php sida!</p>";
		}
	}
	*/
	if ($addartnr != "") {
		if (!($start->check_artikel_status($addartnr) == $addartnr)) {
		// if (!($start->check_artikel_status($addartnr))) {
			$olright = false;
			$wrongmess .= "<p>- Detta artikel nummer finns inte. V�nligen kolla upp detta! (m�ste skrivas exakt)</p>";
		}
	}
	if ($addstore == "0" && $addartnr == "") {
		$olright = false;
		$wrongmess .= "<p>- Om du inte till�ter slut i lager kan inte artikel nummer vara tomt!</p>";
	}
	if ($addcreatedby == "") {
		$olright = false;
		$wrongmess .= "<p>- Du m�ste ange vem du �r!</p>";
	}
	if ($olright) {
		$start->FrontAdminChange($addid,$addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addprio);
	}
}

?>
<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/admin.ico">
<title>Administrera frontsidan</title>
<link rel="stylesheet" type="text/css" href="/css/admin_current.css">
<link rel="stylesheet" type="text/css" href="/css/frontstyle.css">
<style type="text/css">

<!--
.flagshoose { 
	font-family: Verdana; 
	font-size: 11px; 
	color: #000000;
	text-align: center;
}

.text {  font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; text-decoration: none; clip:   rect(   ); color: #000000; line-height: 10pt}
.info { font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; text-decoration: none; clip:  rect(   ); color: #000000; line-height: 10pt}
#layerflag { height: 45px; width: 275px; left: 685px; top: 90px; position: absolute; visibility: visible; }
#layerflagchoose { height: 45px; width: 275px; left: 685px; top: 190px; position: absolute; visibility: visible; }
#layerconfirm { height: 45px; width: 355px; left: 685px; top: 360px; position: absolute; visibility: visible; }
#layermark1 { height: 25px; width: 25px; left: 223px; top: 162px; position: absolute; visibility: visible; }
#layer1 { height: 25px; width: 25px; left: 235px; top: 175px; position: absolute; visibility: visible; }
#layermark2 { height: 25px; width: 25px; left: 363px; top: 162px; position: absolute; visibility: visible; }
#layer2 { height: 25px; width: 25px; left: 375px; top: 175px; position: absolute; visibility: visible; }
#layermark5 { height: 25px; width: 25px; left: 178px; top: 242px; position: absolute; visibility: visible; }
#layer5 { height: 25px; width: 25px; left: 190px; top: 255px; position: absolute; visibility: visible; }
#layermark6 { height: 25px; width: 25px; left: 271px; top: 242px; position: absolute; visibility: visible; }
#layer6 { height: 25px; width: 25px; left: 283px; top: 255px; position: absolute; visibility: visible; }
#layermark7 { height: 25px; width: 25px; left: 363px; top: 242px; position: absolute; visibility: visible; }
#layer7 { height: 25px; width: 25px; left: 375px; top: 255px; position: absolute; visibility: visible; }
#layermark99 { height: 25px; width: 25px; left: 145px; top: 291px; position: absolute; visibility: visible; }
#layer99 { height: 10px; width: 500px; left: 157px; top: 301px; position: absolute; visibility: visible; }
#layermark10 { height: 25px; width: 25px; left: 163px; top: 304px; position: absolute; visibility: visible; }
#layer10 { height: 25px; width: 25px; left: 175px; top: 317px; position: absolute; visibility: visible; }
#layermark11 { height: 25px; width: 25px; left: 232px; top: 304px; position: absolute; visibility: visible; }
#layer11 { height: 25px; width: 25px; left: 244px; top: 317px; position: absolute; visibility: visible; }
#layermark12 { height: 25px; width: 25px; left: 298px; top: 304px; position: absolute; visibility: visible; }
#layer12 { height: 25px; width: 25px; left: 310px; top: 317px; position: absolute; visibility: visible; }
#layermark13 { height: 25px; width: 25px; left: 368px; top: 304px; position: absolute; visibility: visible; }
#layer13 { height: 25px; width: 25px; left: 380px; top: 317px; position: absolute; visibility: visible; }
#layermark14 { height: 25px; width: 25px; left: 433px; top: 304px; position: absolute; visibility: visible; }
#layer14 { height: 25px; width: 25px; left: 445px; top: 317px; position: absolute; visibility: visible; }
#layermark15 { height: 25px; width: 25px; left: 163px; top: 350px; position: absolute; visibility: visible; }
#layer15 { height: 25px; width: 25px; left: 175px; top: 363px; position: absolute; visibility: visible; }
#layermark16 { height: 25px; width: 25px; left: 232px; top: 350px; position: absolute; visibility: visible; }
#layer16 { height: 25px; width: 25px; left: 244px; top: 363px; position: absolute; visibility: visible; }
#layermark17 { height: 25px; width: 25px; left: 298px; top: 350px; position: absolute; visibility: visible; }
#layer17 { height: 25px; width: 25px; left: 310px; top: 363px; position: absolute; visibility: visible; }
#layermark18 { height: 25px; width: 25px; left: 368px; top: 350px; position: absolute; visibility: visible; }
#layer18 { height: 25px; width: 25px; left: 380px; top: 363px; position: absolute; visibility: visible; }
#layermark19 { height: 25px; width: 25px; left: 433px; top: 350px; position: absolute; visibility: visible; }
#layer19 { height: 25px; width: 25px; left: 445px; top: 363px; position: absolute; visibility: visible; }
#layermark20 { height: 25px; width: 25px; left: 163px; top: 396px; position: absolute; visibility: visible; }
#layer20 { height: 25px; width: 25px; left: 175px; top: 409px; position: absolute; visibility: visible; }
#layermark21 { height: 25px; width: 25px; left: 232px; top: 396px; position: absolute; visibility: visible; }
#layer21 { height: 25px; width: 25px; left: 244px; top: 409px; position: absolute; visibility: visible; }
#layermark22 { height: 25px; width: 25px; left: 298px; top: 396px; position: absolute; visibility: visible; }
#layer22 { height: 25px; width: 25px; left: 310px; top: 409px; position: absolute; visibility: visible; }
#layermark23 { height: 25px; width: 25px; left: 368px; top: 396px; position: absolute; visibility: visible; }
#layer23 { height: 25px; width: 25px; left: 380px; top: 409px; position: absolute; visibility: visible; }
#layermark24 { height: 25px; width: 25px; left: 433px; top: 396px; position: absolute; visibility: visible; }
#layer24 { height: 25px; width: 25px; left: 445px; top: 409px; position: absolute; visibility: visible; }
#layermark101 { height: 25px; width: 25px; left: 513px; top: 127px; position: absolute; visibility: visible; }
#layer101 { height: 25px; width: 25px; left: 525px; top: 140px; position: absolute; visibility: visible; }
#layermark102 { height: 25px; width: 25px; left: 513px; top: 227px; position: absolute; visibility: visible; }
#layer102 { height: 25px; width: 25px; left: 525px; top: 240px; position: absolute; visibility: visible; }
#layermark103 { height: 25px; width: 25px; left: 513px; top: 327px; position: absolute; visibility: visible; }
#layer103 { height: 25px; width: 25px; left: 525px; top: 340px; position: absolute; visibility: visible; }
-->

</style>
<script type="text/javascript">
function alertPrio() {
	if (document.addbannerform.addprio.checked)
		alert('OBS! Anv�nd denna funktion sparsamt');
}
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
      <td width="100%"><font face="Arial"><b>Frontsidan</b></font></td>
    </tr>
    <?php 
		// if ($_SESSION['adminsite'] == 1 || $_SESSION['adminsite'] == 2 || $_SESSION['adminsite'] == 3 || $_SESSION['adminsite'] == 6 || $_SESSION['adminsite'] == 7 || $_SESSION['adminsite'] == 8 || $_SESSION['adminsite'] == 9 || $_SESSION['adminsite'] == 10) { 
		if ($_SESSION['adminsite'] != "") { 
	?>
    <tr>
      <td width="100%">&nbsp;</td>
    </tr>
    <tr>
      <td width="100%"><b><font face="Verdana" size="1">Status fronten just nu<br></font></b><img border="0" src="bak.jpg"></td>
    </tr>
    <tr>
      <td width="100%"><hr noshade color="#C0C0C0" size="1"></td>
    </tr>
    <?php if ($section != "") { ?>
    <tr>
      <td width="100%"><b><font face="Verdana" size="2">Status sektion <?php echo $section; ?></font></b></td>
    </tr>
    <tr>
      <td width="100%">&nbsp;</td>
    </tr>
    <tr>
      <td width="100%">
      	<table border="0" cellpadding="1" cellspacing="3">
      	  <tr>
      	    <td colspan="11"><b><font face="Verdana" size="1" color="#85000D">Just nu sektion <?php echo $section; ?></font></b></td>
      	  </tr>
      	  <tr>
      	    <td width="120"><b><font face="Verdana" size="1">Visas fr�n</font></b></td>
      	    <td width="120"><b><font face="Verdana" size="1">Visas till</font></b></td>
      	    <td width="75" align="center"><b><font face="Verdana" size="1">�terst�r</font></b></td>
      	    <td width="140"><b><font face="Verdana" size="1">Bild</font></b></td>
      	    <td width="80"><b><font face="Verdana" size="1">Artikel nr</font></b></td>
      	    <td width="150"><b><font face="Verdana" size="1">Eventuell l�nk</font></b></td>
      	    <td width="95"><b><font face="Verdana" size="1">Till�t slut lager</font></b></td>
      	    <td width="15"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="15"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="80" align="center"><b><font face="Verdana" size="1">Leverant�r</font></b></td>
      	    <td width="130"><b><font face="Verdana" size="1">Ev. kommentar</font></b></td>
      	    <td width="25" align="center"><b><font face="Verdana" size="1">Av</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	  </tr>
      	    <?php $start->getBannerAdminNow($section,$_SESSION['adminsite']); ?>
      	</table>
      </td>
    </tr>
    <tr>
      <td width="100%">&nbsp;</td>
    </tr>
    <tr>
      <td width="100%">
      	<table border="0" cellpadding="1" cellspacing="3">
      	  <tr>
      	    <td colspan="11"><b><font face="Verdana" size="1" color="#85000D">Planerat sektion <?php echo $section; ?></font></b></td>
      	  </tr>
      	  <tr>
      	    <td width="120"><b><font face="Verdana" size="1">Visas fr�n</font></b></td>
      	    <td width="120"><b><font face="Verdana" size="1">Visas till</font></b></td>
      	    <td width="75" align="center"><b><font face="Verdana" size="1">Visas om</font></b></td>
      	    <td width="140"><b><font face="Verdana" size="1">Bild</font></b></td>
      	    <td width="80"><b><font face="Verdana" size="1">Artikel nr</font></b></td>
      	    <td width="150"><b><font face="Verdana" size="1">Eventuell l�nk</font></b></td>
      	    <td width="95"><b><font face="Verdana" size="1">Till�t slut lager</font></b></td>
      	    <td width="15"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="15"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="80" align="center"><b><font face="Verdana" size="1">Leverant�r</font></b></td>
      	    <td width="130"><b><font face="Verdana" size="1">Ev. kommentar</font></b></td>
      	    <td width="25" align="center"><b><font face="Verdana" size="1">Av</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	  </tr>
      	    <?php $start->getBannerAdminPlan($section,$_SESSION['adminsite']); ?>
      	</table>
      </td>
    </tr>
    <?php } ?>
    <tr>
      <td width="100%">&nbsp;</td>
    </tr>
    <?php if ($section != "") { ?>
    <tr>
      <td width="100%"><img border="0" src="../../pic/help.gif">&nbsp;<b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?add=yes&section=<?php echo $section; ?>">L�gg till post p� sektion <?php echo $section; ?></b></td>
    </tr>
    <tr>
      <td width="100%"><hr noshade color="#C0C0C0" size="1"></td>
    </tr>
    <?php } ?>
    <?php if ($add == "yes" || $addid != "" || $addidc != "") { ?>
    <?php if ($wrongmess) { ?>
    <tr>
      <td width="100%">
      <table border="0" cellpadding="5" cellspacing="0" style="border:1px solid #000000; border-collapse: collapse; background-color:#FFFF00" width="555">
        <tr>
          <td><b><font face="Verdana" size="1" color="#000000"><?php echo $wrongmess; ?></font></td>
        </tr>
      </table>
      </td>
    </tr>
    <tr>
      <td width="100%"><hr noshade color="#C0C0C0" size="1" align="left" width="555"></td>
    </tr>
    <?php } ?>
    <tr>
      <td width="100%">
<form name="addbannerform">
  <?php if ($addid !="") { ?>
  <input type="hidden" value="<?php echo $addid; ?>" name="addid">
  <input type="hidden" value=true name="submC">
  <?php } else { ?>
  <input type="hidden" value=true name="subm">
  <input type="hidden" value="yes" name="add">
  <input type="hidden" value="<?php echo $addidc; ?>" name="addidc">
  <?php } ?>
  <table border="0" cellpadding="5" cellspacing="3" style="border-collapse: collapse; border: 1px solid #000000; background-color: #999966">
    <?php if ($addidc != "" && ($_SESSION['adminsite'] == 1 || $_SESSION['adminsite'] == 2)) { ?>
    <tr>
      <td><font face="Verdana" size="1">Kopiera till</font></td>
      <td><font face="Verdana" size="1">
	  <select size="1" name="addsitecopy" style="font-family: Verdana; font-size: 8pt">
	  <option value=""></option>
	  <option value="1" <?php if ($addsitecopy == 1) { echo " selected"; } ?>>SE - Foto</option>
	  <option value="6" <?php if ($addsitecopy == 6) { echo " selected"; } ?>>SE - Mobil</option>
	  <option value="10" <?php if ($addsitecopy == 10) { echo " selected"; } ?>>SE - Cybairgun</option>
	  <option value="11" <?php if ($addsitecopy == 11) { echo " selected"; } ?>>SE - Hobby</option>
	  <option value="13" <?php if ($addsitecopy == 13) { echo " selected"; } ?>>SE - Hush�ll</option>
	  <option value=""></option>
	  <option value="2" <?php if ($addsitecopy == 2) { echo " selected"; } ?>>FI - Foto</option>
	  <option value="7" <?php if ($addsitecopy == 7) { echo " selected"; } ?>>FI - Mobil</option>
	  <option value="14" <?php if ($addsitecopy == 14) { echo " selected"; } ?>>FI - Cybairgun</option>
	  <option value=""></option>
	  <option value="3" <?php if ($addsitecopy == 3) { echo " selected"; } ?>>FI(SE) - Foto</option>
	  <option value=""></option>
	  <option value="30" <?php if ($addsitecopy == 30) { echo " selected"; } ?>>NO - Foto</option>
	  <option value="31" <?php if ($addsitecopy == 31) { echo " selected"; } ?>>NO - Mobil</option>
	  <option value="32" <?php if ($addsitecopy == 32) { echo " selected"; } ?>>NO - Cybairgun</option>
	  <option value="33" <?php if ($addsitecopy == 33) { echo " selected"; } ?>>NO - Hobby</option>
	  <option value="34" <?php if ($addsitecopy == 34) { echo " selected"; } ?>>NO - Hush�ll</option>
	  </select>
	  </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><font face="Verdana" size="1"><a onMouseOver="this.T_WIDTH=350;return escape('<b>Kopiera post till annan frontsida!</b><br><br>Om du kopiera post till annan frontsida och den �r aktiv s� visas den omg�ende.<br><br>Var d�rf�r mycket noggrann med att Allt �r korrekt. Framf�rallt bilder och bildl�nkar �r k�nsliga.')"><b>OBS! L�s detta!</b></a></font></td>
    </tr>
    <?php } ?>
    <tr>
      <td><font face="Verdana" size="1">G�ller fr�n <b><font color="#FF0000">*</font></b></font></td>
      <td><input type="text" name="addfrom" size="20" value="<?php if ($addfrom == "") { echo date("Y-m-d H:i:s", time()); } else { echo $addfrom; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
      <td>&nbsp;</td>
      <td><font face="Verdana" size="1">G�ller till <b><font color="#FF0000">*</font></b></font></td>
      <td><input type="text" name="addto" size="20" value="<?php if ($addto == "") { echo date("Y-m-d 23:59:59",mktime(0,0,0,date("n")+1,1-1,date("Y"))); } else { echo $addto; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Sektion <b><font color="#FF0000">*</font></b></font></td>
      <td><input type="text" name="addsection" value="<?php echo $section; ?>" size="2" style="font-family: Verdana; font-size: 8pt<?php if ($change != "") { ?>; background-color: #CCCCCC<?php } ?>"<?php if ($change != "") { ?> onFocus="this.blur()"<?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <?php if ($change != "") { ?>
      <td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?change=<?php echo $addid; ?>&now=yes">Avsluta tiden som �r nu</a></td>
      <?php } else { ?>
      <td>&nbsp;</td>
      <?php } ?>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Bild <b><font color="#FF0000">*</font></b></font></td>
      <td><input type="text" name="addpicture" size="20" value="<?php echo $addpicture; ?>" style="font-family: Verdana; font-size: 8pt"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Art nr</font></td>
      <td><input type="text" name="addartnr" size="20" value="<?php echo $addartnr; ?>" style="font-family: Verdana; font-size: 8pt"></td>
      <td>&nbsp;</td>
      <td><font face="Verdana" size="1">L�nk</font></td>
      <td><input type="text" name="addlinc" size="20" value="<?php echo $addlinc; ?>" style="font-family: Verdana; font-size: 8pt"></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Till�t slut i lager</font></td>
      <td><input type="checkbox" name="addstore" value="yes" <?php if ($addstore == -1) { ?> checked <?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Prioriterad</font></td>
      <td><input type="checkbox" name="addprio" onclick="javascript:alertPrio()" value="yes" <?php if ($addprio == -1) { ?> checked <?php } ?><?php if ($change != "") echo " disabled";?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Ev. kommentar</font></td>
      <td colspan="4"><textarea rows="1" name="addcomment" cols="58" style="font-family: Verdana; font-size: 8pt"><?php echo $addcomment; ?></textarea></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Ev. betalande leverant�r</font></td>
      <td><select size="1" name="addleverantor" style="font-family: Verdana; font-size: 8pt">
      		<option value=""></option>
      		<?php $start->getLeverantor(); ?>
      		</select></td>
      <td>&nbsp;</td>
      <td><font face="Verdana" size="1">Skapad av <b><font color="#FF0000">*</font></b></font></td>
      <td><select size="1" name="addcreatedby" style="font-family: Verdana; font-size: 8pt">
      		<option></option>
      		<?php $start->getAnstallda(); ?>
      		</select>
      </td>
    </tr>
  </table>
  <p><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>L�gg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #008080; font-weight: bold; background-color: #C0C0C0"></p>
</form>
     </td>
    </tr>
    <?php } ?>
    <tr>
      <td width="100%">&nbsp;</td>
    </tr>
    <?php } ?>
    <?php if ($start->getWarning($_SESSION['adminsite'])) { ?>
    <tr>
      <td width="100%"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
    </tr>
    <tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1" color="#FF0000"><b>UPPLYSNING!</font></b></td>
    </tr>
    <tr>
      <td width="100%"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
    </tr>
      <td width="100%">
	<table border="0" cellpadding="3" cellspacing="3" style="border-collapse: collapse; border: 1px solid #808000; background-color: #E6E6E6">
	 <tr>
          <td align="center"><font face="Verdana" size="1"><b>Sektion</b></font></td>
          <td align="center" width="100"><font face="Verdana" size="1"><b>Antal</b></font></td>
          <td align="center" width="50"><font face="Verdana" size="1">&nbsp;</font></td>
          <?php $start->displayWarning($_SESSION['adminsite']); ?>
         </tr>
	</table>
      </td>
    </tr>
    <?php } ?>
    <?php 
		// if ($_SESSION['adminsite'] == 1 || $_SESSION['adminsite'] == 2 || $_SESSION['adminsite'] == 3 || $_SESSION['adminsite'] == 6 || $_SESSION['adminsite'] == 7 || $_SESSION['adminsite'] == 8 || $_SESSION['adminsite'] == 9 || $_SESSION['adminsite'] == 10) { 
		if ($_SESSION['adminsite'] != "") { 
	?>
    <tr>
      <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
    </tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
    </tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1"><b>Se fronten annat datum</b> (f�reslaget datum �r en vecka bak�t)</font></td>
    </tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
    </tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1">
	  <form method="GET" target="_blank" action="/fronthistory.php">
	  <input type="text" name="datum" value="<?php echo date("Y-m-d", time() - 604800); ?>" size="10" style="font-family: Verdana; font-size: 8pt">
	  <input type="submit" value="Visa" style="font-family: Verdana; font-size: 8pt; color: #008080; background-color: #FFFFFF">
	  </form>
      </font></td>
    </tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
    </tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
    </tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1"><b>Ta fram fakturaunderlag f�r perioden</b> (f�reg�ende m�nad �r f�reslagen)</font></td>
    </tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
    </tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1">
	  <form method="GET" target="_blank" action="frontdelivery.php">
	  <input type="text" name="datumfrom" value="<?php echo date("Y-m-d",mktime(0,0,0,date("n")-1,1,date("Y"))); ?>" size="10" style="font-family: Verdana; font-size: 8pt">&nbsp;-&nbsp;
	  <input type="text" name="datumto" value="<?php echo date("Y-m-d",mktime(0,0,0-1,date("n"),1,date("Y"))); ?>" size="10" style="font-family: Verdana; font-size: 8pt">&nbsp;&nbsp;
      	  <select size="1" name="leverantor" style="font-family: Verdana; font-size: 8pt">
      	  <option value=""></option>
      	  <?php $start->getLeverantorLong(); ?>
      	  </select>
	  <input type="submit" value="Visa" style="font-family: Verdana; font-size: 8pt; color: #008080; background-color: #FFFFFF">
	  </form>
      </font></td>
    <tr>
      <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
    </tr>
    </tr>
    <?php } ?>
  </table>
  </center>
</div>
    
		<?php if ($confirmdelete != "") { ?>
		<div id="layerconfirm">

          	<?php include ("confirm.php"); ?>
          		
		</div>
		<?php } ?>

		<div id="layerflag">

          	<?php
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				include ("flag.php");
			} else {
				include ("flag.php");
			}
			?>
          		
		</div>

    <?php
		// if ($_SESSION['adminsite'] == 1 || $_SESSION['adminsite'] == 2 || $_SESSION['adminsite'] == 3 || $_SESSION['adminsite'] == 6 || $_SESSION['adminsite'] == 7 || $_SESSION['adminsite'] == 8 || $_SESSION['adminsite'] == 9 || $_SESSION['adminsite'] == 10) {
		if ($_SESSION['adminsite'] != "") {
	?>

		
		<div id="layer1">

          	<?php $start->getFrontBannerAdmin(1,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer2">

          	<?php $start->getFrontBannerAdmin(2,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer5">

          	<?php $start->getFrontBannerAdmin(5,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer6">

          	<?php $start->getFrontBannerAdmin(6,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer7">

          	<?php $start->getFrontBannerAdmin(7,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer99">

          	<?php $start->getFrontBannerAdmin(99,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer10">

          	<?php $start->getFrontBannerAdmin(10,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer11">

          	<?php $start->getFrontBannerAdmin(11,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer12">

          	<?php $start->getFrontBannerAdmin(12,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer13">

          	<?php $start->getFrontBannerAdmin(13,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer14">

          	<?php $start->getFrontBannerAdmin(14,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer15">

          	<?php $start->getFrontBannerAdmin(15,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer16">

          	<?php $start->getFrontBannerAdmin(16,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer17">

          	<?php $start->getFrontBannerAdmin(17,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer18">

          	<?php $start->getFrontBannerAdmin(18,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer19">

          	<?php $start->getFrontBannerAdmin(19,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer20">

          	<?php $start->getFrontBannerAdmin(20,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer21">

          	<?php $start->getFrontBannerAdmin(21,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer22">

          	<?php $start->getFrontBannerAdmin(22,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer23">

          	<?php $start->getFrontBannerAdmin(23,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer24">

          	<?php $start->getFrontBannerAdmin(24,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer101">

          	<?php $start->getFrontBannerAdmin(101,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer102">

          	<?php $start->getFrontBannerAdmin(102,$_SESSION['adminsite']); ?>
          		
		</div>
		<div id="layer103">

          	<?php $start->getFrontBannerAdmin(103,$_SESSION['adminsite']); ?>
          		
		</div>

		<?php if ($section == 1) { ?>
			<div id="layermark1">
		<?php } elseif ($section == 2) { ?>
			<div id="layermark2">
		<?php } elseif ($section == 5) { ?>
			<div id="layermark5">
		<?php } elseif ($section == 6) { ?>
			<div id="layermark6">
		<?php } elseif ($section == 7) { ?>
			<div id="layermark7">
		<?php } elseif ($section == 99) { ?>
			<div id="layermark99">
		<?php } elseif ($section == 10) { ?>
			<div id="layermark10">
		<?php } elseif ($section == 11) { ?>
			<div id="layermark11">
		<?php } elseif ($section == 12) { ?>
			<div id="layermark12">
		<?php } elseif ($section == 13) { ?>
			<div id="layermark13">
		<?php } elseif ($section == 14) { ?>
			<div id="layermark14">
		<?php } elseif ($section == 15) { ?>
			<div id="layermark15">
		<?php } elseif ($section == 16) { ?>
			<div id="layermark16">
		<?php } elseif ($section == 17) { ?>
			<div id="layermark17">
		<?php } elseif ($section == 18) { ?>
			<div id="layermark18">
		<?php } elseif ($section == 19) { ?>
			<div id="layermark19">
		<?php } elseif ($section == 20) { ?>
			<div id="layermark20">
		<?php } elseif ($section == 21) { ?>
			<div id="layermark21">
		<?php } elseif ($section == 22) { ?>
			<div id="layermark22">
		<?php } elseif ($section == 23) { ?>
			<div id="layermark23">
		<?php } elseif ($section == 24) { ?>
			<div id="layermark24">
		<?php } elseif ($section == 101) { ?>
			<div id="layermark101">
		<?php } elseif ($section == 102) { ?>
			<div id="layermark102">
		<?php } elseif ($section == 103) { ?>
			<div id="layermark103">
		<?php } ?>

          	<?php if ($section == 99) { ?>
          	<img src="mark_mitt.gif" border="0">
          	</div>
          	<?php } elseif ($section != "") { ?>
          	<img src="mark.gif" border="0">
          	</div>
          	<?php } ?>


    <?php } ?>
    
    </td>
  </tr>
</table>
<?php // echo $section;
?>
</body>
<script language="JavaScript" type="text/javascript" src="wz_tooltip_front.js"></script>
</html>
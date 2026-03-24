<?php
session_start();

if ($nollstall == "yes") {
	unset ($_SESSION['adminsite_banner']);
}

if ($choose_site != "") {

	$_SESSION['adminsite_banner'] = $choose_site;
	/*
	if ($choose_site == 500) {
		$_SESSION['adminsite_banner'] = 500;
	} elseif ($choose_site == 501) {
		$_SESSION['adminsite_banner'] = 501;
	} elseif ($choose_site == 502) {
		$_SESSION['adminsite_banner'] = 502;
	} elseif ($choose_site == 503) {
		$_SESSION['adminsite_banner'] = 503;
	} else {
		$_SESSION['adminsite_banner'] = 599;
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
	$start->BannerAdminDelete($delete,$section);
}
if ($subm) {
	
	$olright = true;
	$section = $addsection;
	
	if ($addsitecopy == 2 || $addsitecopy == 3) {
		$addsite = $addsitecopy;
	} else {
		$addsite = $_SESSION['adminsite_banner'];
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
		$wrongmess .= "<p>- Bild måste anges!</p>";
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
		$start->BannerAdminChange($addid,$addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addprio);
	}
}

?>
<html>

<head>
<link rel="shortcut icon" href="http://www.cyberphoto.se/order/admin/favicon.ico">
<title>Administrera övriga banners</title>
<link rel="stylesheet" type="text/css" href="/css/admin_current.css">
<link rel="stylesheet" type="text/css" href="/css/frontstyle.css">
<style type="text/css">

<!--
.text {  font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; text-decoration: none; clip:   rect(   ); color: #000000; line-height: 10pt}
.info { font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; text-decoration: none; clip:  rect(   ); color: #000000; line-height: 10pt}
#layerflag { height: 45px; width: 275px; left: 685px; top: 90px; position: absolute; visibility: visible; }
#layerflagchoose { height: 45px; width: 275px; left: 685px; top: 190px; position: absolute; visibility: visible; }
#layerconfirm { height: 45px; width: 355px; left: 685px; top: 360px; position: absolute; visibility: visible; }
#layermark1 { height: 25px; width: 25px; left: 223px; top: 162px; position: absolute; visibility: visible; }
#layermark99 { height: 25px; width: 25px; left: 145px; top: 271px; position: absolute; visibility: visible; }
#layer99 { height: 10px; width: 500px; left: 157px; top: 281px; position: absolute; visibility: visible; }
-->

</style>
<script type="text/javascript">
function alertPrio() {
	if (document.addbannerform.addprio.checked)
		alert('OBS! Använd denna funktion sparsamt');
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
      <td width="100%"><font face="Arial"><b>Övriga banners</b></font></td>
    </tr>
    <?php if ($_SESSION['adminsite_banner'] != "") { ?>
    <tr>
      <td width="100%">&nbsp;</td>
    </tr>
    <tr>
      <td width="100%"><b><font face="Verdana" size="1">Status just nu<br></font></b><img border="0" src="bak_banner.jpg"></td>
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
      	    <td width="120"><b><font face="Verdana" size="1">Visas från</font></b></td>
      	    <td width="120"><b><font face="Verdana" size="1">Visas till</font></b></td>
      	    <td width="75" align="center"><b><font face="Verdana" size="1">Återstår</font></b></td>
      	    <td width="140"><b><font face="Verdana" size="1">Bild</font></b></td>
      	    <td width="80"><b><font face="Verdana" size="1">Artikel nr</font></b></td>
      	    <td width="150"><b><font face="Verdana" size="1">Eventuell länk</font></b></td>
      	    <td width="95"><b><font face="Verdana" size="1">Tillåt slut lager</font></b></td>
      	    <td width="15"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="15"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="80" align="center"><b><font face="Verdana" size="1">Leverantör</font></b></td>
      	    <td width="130"><b><font face="Verdana" size="1">Ev. kommentar</font></b></td>
      	    <td width="25" align="center"><b><font face="Verdana" size="1">Av</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	  </tr>
      	    <?php $start->getBannerAdminNow($section,$_SESSION['adminsite_banner']); ?>
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
      	    <td width="120"><b><font face="Verdana" size="1">Visas från</font></b></td>
      	    <td width="120"><b><font face="Verdana" size="1">Visas till</font></b></td>
      	    <td width="75" align="center"><b><font face="Verdana" size="1">Visas om</font></b></td>
      	    <td width="140"><b><font face="Verdana" size="1">Bild</font></b></td>
      	    <td width="80"><b><font face="Verdana" size="1">Artikel nr</font></b></td>
      	    <td width="150"><b><font face="Verdana" size="1">Eventuell länk</font></b></td>
      	    <td width="95"><b><font face="Verdana" size="1">Tillåt slut lager</font></b></td>
      	    <td width="15"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="15"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="80" align="center"><b><font face="Verdana" size="1">Leverantör</font></b></td>
      	    <td width="130"><b><font face="Verdana" size="1">Ev. kommentar</font></b></td>
      	    <td width="25" align="center"><b><font face="Verdana" size="1">Av</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	    <td width="50"><b><font face="Verdana" size="1">&nbsp;</font></b></td>
      	  </tr>
      	    <?php $start->getBannerAdminPlan($section,$_SESSION['adminsite_banner']); ?>
      	</table>
      </td>
    </tr>
    <?php } ?>
    <tr>
      <td width="100%">&nbsp;</td>
    </tr>
    <?php if ($section != "") { ?>
    <tr>
      <td width="100%"><img border="0" src="../../pic/help.gif">&nbsp;<b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?add=yes&section=<?php echo $section; ?>">Lägg till post på sektion <?php echo $section; ?></b></td>
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
    <?php if ($addidc != "" && ($_SESSION['adminsite_banner'] == 1 || $_SESSION['adminsite_banner'] == 2)) { ?>
    <tr>
      <td><font face="Verdana" size="1">Kopiera till</font></td>
      <td><font face="Verdana" size="1"><?php if ($_SESSION['adminsite_banner'] == 1) { ?><input type="radio" value="2" name="addsitecopy"<?php if ($addsitecopy == 2) { echo " checked"; } ?>>&nbsp;<img src="fi_mini.jpg" border="0">&nbsp;&nbsp;<?php } ?><input type="radio" value="3" name="addsitecopy"<?php if ($addsitecopy == 3) { echo " checked"; } ?>>&nbsp;<img src="fisv_mini.jpg" border="0"></font></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><font face="Verdana" size="1"><a onMouseOver="this.T_WIDTH=350;return escape('<b>Kopiera post till annan frontsida!</b><br><br>Om du kopiera post till annan frontsida och den är aktiv så visas den omgående.<br><br>Var därför mycket noggrann med att Allt är korrekt. Framförallt bilder och bildlänkar är känsliga.')"><b>OBS! Läs detta!</b></a></font></td>
    </tr>
    <?php } ?>
    <tr>
      <td><font face="Verdana" size="1">Gäller från <b><font color="#FF0000">*</font></b></font></td>
      <td><input type="text" name="addfrom" size="20" value="<?php if ($addfrom == "") { echo date("Y-m-d H:i:s", time()); } else { echo $addfrom; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
      <td>&nbsp;</td>
      <td><font face="Verdana" size="1">Gäller till <b><font color="#FF0000">*</font></b></font></td>
      <td><input type="text" name="addto" size="20" value="<?php if ($addto == "") { echo date("Y-m-d 23:59:59",mktime(0,0,0,date("n")+1,1-1,date("Y"))); } else { echo $addto; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Sektion <b><font color="#FF0000">*</font></b></font></td>
      <td><input type="text" name="addsection" value="<?php echo $section; ?>" size="2" style="font-family: Verdana; font-size: 8pt<?php if ($change != "") { ?>; background-color: #CCCCCC<?php } ?>"<?php if ($change != "") { ?> onFocus="this.blur()"<?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <?php if ($change != "") { ?>
      <td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?change=<?php echo $addid; ?>&now=yes">Avsluta tiden som är nu</a></td>
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
      <td><font face="Verdana" size="1">Länk</font></td>
      <td><input type="text" name="addlinc" size="20" value="<?php echo $addlinc; ?>" style="font-family: Verdana; font-size: 8pt"></td>
    </tr>
    <tr>
      <td><font face="Verdana" size="1">Tillåt slut i lager</font></td>
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
      <td><font face="Verdana" size="1">Ev. betalande leverantör</font></td>
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
  <p><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #008080; font-weight: bold; background-color: #C0C0C0"></p>
</form>
     </td>
    </tr>
    <?php } ?>
    <tr>
      <td width="100%">&nbsp;</td>
    </tr>
    <?php } ?>
    <?php if ($start->getWarning($_SESSION['adminsite_banner'])) { ?>
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
          <?php $start->displayWarning($_SESSION['adminsite_banner']); ?>
         </tr>
	</table>
      </td>
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

          	<?php include ("flag_banner.php"); ?>
          		
		</div>

    <?php if ($_SESSION['adminsite_banner'] != "") { ?>

		<div id="layerflagchoose">

          	<?php include ("flagchoose_banner.php"); ?>
          		
		</div>
		
		<div id="layer99">

          	<?php $start->getFrontBannerAdmin(99,$_SESSION['adminsite_banner']); ?>
          		
		</div>

		<?php if ($section == 99) { ?>
			<div id="layermark99">
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
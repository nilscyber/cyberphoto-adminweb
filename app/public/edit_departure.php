<?php
require_once ("CDeparture.php");
$departure = new CDeparture();

if ($edit == "1") {

	$rows = $departure->getSpecDeparture($depid);
	// echo $rows->departure_OpenMorning_FI;
	
	$dep_id = $rows->departure_ID;
	$dep_time = preg_replace('/:[0-9][0-9][0-9]/','', $rows->departure_Time);
	$dep_time = strtotime($dep_time);
	$dep_time = date("Y-m-d H:i:s", $dep_time);
	$dep_OpenMorning = preg_replace('/:[0-9][0-9][0-9]/','', $rows->departure_OpenMorning);
	$dep_OpenMorning = strtotime($dep_OpenMorning);
	$dep_OpenMorning = date("Y-m-d H:i:s", $dep_OpenMorning);
	// $dep_OpenMorning_FI = preg_replace('/:[0-9][0-9][0-9]/','', $rows->departure_OpenMorning_FI);
	// $dep_OpenMorning_FI = strtotime($dep_OpenMorning_FI);
	$dep_OpenMorning_FI = date("Y-m-d H:i:s", strtotime($rows->departure_OpenMorning_FI));
	$dep_CloseLunch = preg_replace('/:[0-9][0-9][0-9]/','', $rows->departure_CloseLunch);
	$dep_CloseLunch = strtotime($dep_CloseLunch);
	$dep_CloseLunch = date("Y-m-d H:i:s", $dep_CloseLunch);
	$dep_CloseLunch_FI = date("Y-m-d H:i:s", strtotime($rows->departure_CloseLunch_FI));
	$dep_OpenLunch = preg_replace('/:[0-9][0-9][0-9]/','', $rows->departure_OpenLunch);
	$dep_OpenLunch = strtotime($dep_OpenLunch);
	$dep_OpenLunch = date("Y-m-d H:i:s", $dep_OpenLunch);
	$dep_OpenLunch_FI = date("Y-m-d H:i:s", strtotime($rows->departure_OpenLunch_FI));
	$dep_CloseEvening = preg_replace('/:[0-9][0-9][0-9]/','', $rows->departure_CloseEvening);
	$dep_CloseEvening = strtotime($dep_CloseEvening);
	$dep_CloseEvening = date("Y-m-d H:i:s", $dep_CloseEvening);
	$dep_CloseEvening_FI = date("Y-m-d H:i:s", strtotime($rows->departure_CloseEvening_FI));
	if ($now == "yes") {
		$abb_to = date("Y-m-d H:i:s", time());
	} else {
		$abb_to = preg_replace('/:[0-9][0-9][0-9]/','', $rows->validto);
		$abb_to = strtotime($abb_to);
		$abb_to = date("Y-m-d H:i:s", $abb_to);
	}
	$dep_active = $rows->departure_Active;
	$dep_phone_se = $rows->departure_Phone_SE;
	$dep_phone_fi = $rows->departure_Phone_FI;
	$dep_phone_no = $rows->departure_Phone_NO;
	$dep_recycle = $rows->departure_Recycle;
	$dep_changewhy = $rows->departure_ChangeWhy;
	$dep_createdby = $rows->departure_ChangeBy;
}

if ($subm) {

		// här händer inget just nu

}
if ($submC) {
	
	$olright = true;
	
	$dep_createdby = $_COOKIE['login_mail'];
	
	if ($dep_active == "yes") {
		$dep_active = 1;
	} else {
		$dep_active = 0;
	}

	if ($dep_phone_se == "yes") {
		$dep_phone_se = 1;
	} else {
		$dep_phone_se = 0;
	}

	if ($dep_phone_fi == "yes") {
		$dep_phone_fi = 1;
	} else {
		$dep_phone_fi = 0;
	}

	if ($dep_phone_no == "yes") {
		$dep_phone_no = 1;
	} else {
		$dep_phone_no = 0;
	}
	
	if ($dep_recycle == "yes") {
		$dep_recycle = 1;
	} else {
		$dep_recycle = 0;
	}
	
	if ($dep_changewhy == "" && $_SERVER['REMOTE_ADDR'] != "192.168.1.89") {
		$olright = false;
		$wrongmess .= "<p>- Du måste skriva en anledning varför du ändrar</p>";
	}
	if ($dep_time != "") {
		if (!($departure->isValidDateTime($dep_time))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($dep_OpenMorning != "") {
		if (!($departure->isValidDateTime($dep_OpenMorning))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($dep_CloseLunch != "") {
		if (!($departure->isValidDateTime($dep_CloseLunch))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($dep_OpenLunch != "") {
		if (!($departure->isValidDateTime($dep_OpenLunch))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($dep_CloseEvening != "") {
		if (!($departure->isValidDateTime($dep_CloseEvening))) {
			$olright = false;
			$wrongmess .= "<p>- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
		}
	}
	if ($dep_createdby == "") {
		$olright = false;
		$wrongmess .= "<p>- Du måste vara inloggad för att utföra detta!</p>";
	}
	if ($olright) {
		$dep_ip = $_SERVER['REMOTE_ADDR'];
		$departure->departureChange($dep_time,$dep_active,$dep_changewhy,$dep_createdby,$dep_ip,$dep_id,$dep_OpenMorning,$dep_CloseLunch,$dep_OpenLunch,$dep_CloseEvening,$dep_phone_se,$dep_phone_fi,$dep_phone_no,$dep_OpenMorning_FI,$dep_CloseLunch_FI,$dep_OpenLunch_FI,$dep_CloseEvening_FI,$dep_recycle);
		$dep_ok = true;
	}
}

?>

<html>

<head>
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<link rel="stylesheet" type="text/css" href="/css/mobil_abonnemang_v3.css" />
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Ändra avgångstid</title>
</head>

<body bgcolor="#FFECB0">

<?php if (!$dep_ok) { ?>
<form>
  <?php if ($dep_id !="") { ?>
  <input type="hidden" value="<?php echo $dep_id; ?>" name="dep_id">
  <input type="hidden" value=true name="submC">
  <?php } else { ?>
  <input type="hidden" value=true name="subm">
  <input type="hidden" value="yes" name="add">
  <input type="hidden" value="<?php echo $addidc; ?>" name="addidc">
  <?php } ?>
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
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td>Avgångstid:</td>
    <td><input type="text" name="dep_time" size="20" value="<?php if ($dep_time == "") { echo date("Y-m-d H:i:s", time()); } else { echo $dep_time; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
    <td colspan="2" align="left">&nbsp;<img border="0" src="/order/admin/fi_mini.jpg"></td>
  </tr>
  <tr>
    <td>Öppnar morgon:</td>
    <td><input type="text" name="dep_OpenMorning" size="20" value="<?php if ($dep_OpenMorning == "") { echo date("Y-m-d H:i:s", time()); } else { echo $dep_OpenMorning; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
    <td colspan="2"><input type="text" name="dep_OpenMorning_FI" size="20" value="<?php if ($dep_OpenMorning_FI == "") { echo date("Y-m-d H:i:s", time()); } else { echo $dep_OpenMorning_FI; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
  </tr>
  <tr>
    <td>Stänger lunch:</td>
    <td><input type="text" name="dep_CloseLunch" size="20" value="<?php if ($dep_CloseLunch == "") { echo date("Y-m-d H:i:s", time()); } else { echo $dep_CloseLunch; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
    <td colspan="2"><input type="text" name="dep_CloseLunch_FI" size="20" value="<?php if ($dep_CloseLunch_FI == "") { echo date("Y-m-d H:i:s", time()); } else { echo $dep_CloseLunch_FI; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
  </tr>
  <tr>
    <td>Öppnar lunch:</td>
    <td><input type="text" name="dep_OpenLunch" size="20" value="<?php if ($dep_OpenLunch == "") { echo date("Y-m-d H:i:s", time()); } else { echo $dep_OpenLunch; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
    <td colspan="2"><input type="text" name="dep_OpenLunch_FI" size="20" value="<?php if ($dep_OpenLunch_FI == "") { echo date("Y-m-d H:i:s", time()); } else { echo $dep_OpenLunch_FI; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
  </tr>
  <tr>
    <td>Stänger kväll:</td>
    <td><input type="text" name="dep_CloseEvening" size="20" value="<?php if ($dep_CloseEvening == "") { echo date("Y-m-d H:i:s", time()); } else { echo $dep_CloseEvening; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
    <td colspan="2"><input type="text" name="dep_CloseEvening_FI" size="20" value="<?php if ($dep_CloseEvening_FI == "") { echo date("Y-m-d H:i:s", time()); } else { echo $dep_CloseEvening_FI; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
  </tr>
  <tr>
    <td>Aktiv:</td>
    <td><input type="checkbox" name="dep_active" value="yes" <?php if ($dep_active == 1) { ?> checked <?php } ?>></td>
    <td><?php if ($dep_active == 1) { ?><img border="0" src="/order/admin/status_green.gif"><?php } else { ?><img border="0" src="/order/admin/status_red.gif"><?php } ?></td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td>Uppringande: <img border="0" src="/order/admin/sv_mini.jpg"></td>
    <td><input type="checkbox" name="dep_phone_se" value="yes" <?php if ($dep_phone_se == 1) { ?> checked <?php } ?>></td>
    <td><?php if ($dep_phone_se == 1) { ?><img border="0" src="/order/admin/status_green.gif"><?php } else { ?><img border="0" src="/order/admin/status_red.gif"><?php } ?></td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td>Uppringande: <img border="0" src="/order/admin/fi_mini.jpg"></td>
    <td><input type="checkbox" name="dep_phone_fi" value="yes" <?php if ($dep_phone_fi == 1) { ?> checked <?php } ?>></td>
    <td><?php if ($dep_phone_fi == 1) { ?><img border="0" src="/order/admin/status_green.gif"><?php } else { ?><img border="0" src="/order/admin/status_red.gif"><?php } ?></td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td>Uppringande: <img border="0" src="/order/admin/no_mini.jpg"></td>
    <td><input type="checkbox" name="dep_phone_no" value="yes" <?php if ($dep_phone_no == 1) { ?> checked <?php } ?>></td>
    <td><?php if ($dep_phone_no == 1) { ?><img border="0" src="/order/admin/status_green.gif"><?php } else { ?><img border="0" src="/order/admin/status_red.gif"><?php } ?></td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td>Visa inbyten: <img border="0" src="/order/admin/recycle.jpg"></td>
    <td><input type="checkbox" name="dep_recycle" value="yes" <?php if ($dep_recycle == 1) { ?> checked <?php } ?>></td>
    <td><?php if ($dep_recycle == 1) { ?><img border="0" src="/order/admin/status_green.gif"><?php } else { ?><img border="0" src="/order/admin/status_red.gif"><?php } ?></td>
    <td>&nbsp;</td>
  </tr>
    <tr>
    <td valign="top">Anledning till ändring:</td>
    <td colspan="3" valign="top"><textarea rows="2" name="dep_changewhy" cols="40"><?php echo $dep_changewhy; ?></textarea></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
  <p><input type="submit" value="Utför ändring" name="B1"></p>
</form>
<?php } else { ?>
<p><b>Posten är nu uppdaterad!</b></p>
<span onclick="top.opener.location.reload(true);window.close()" style="cursor:pointer;">Stäng fönster</span>
<?php } ?>

</body>

</html>
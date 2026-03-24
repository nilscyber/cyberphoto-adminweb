<?php

function getStoreStatus($artnr,$datefrom,$dateto) {
	
$select  = "SELECT lagersaldo, ej_med FROM Artiklar WHERE artnr = '" . $artnr . "' ";
$res = mysqli_query($select);
extract(mysqli_fetch_array($res));

if (strtotime($datefrom) < strtotime("now") && strtotime($dateto) > strtotime("now") && $lagersaldo > 0 && $ej_med != -1) {

	return true;

	} else {

	return false;

	}
}	

?>

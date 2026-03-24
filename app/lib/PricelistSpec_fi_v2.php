<?php
$räknare = 0;
include ("CConnect.php");
function getList($extraSelect) {
	global $conn_my;
	$select  = "SELECT tillverkare, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.artnr, Artiklar.artnr_lev, utpris,  ";
	$select .= "Artiklar_fi.utpris_fi, kategori, kategori_fi, link, link2_fi as link_fi, lagersaldo, lagersaldo_fi, bild,  ";
	$select .= "Moms.momssats, Moms.momssats_fi, Artiklar.kategori_id  ";
	$select .= "FROM ";
	$select .= "Artiklar, Kategori, Tillverkare, Moms, Artiklar_fi ";
	$select .= "WHERE Artiklar_fi.artnr_fi = Artiklar.artnr AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id AND ";
	$select .= " (utgangen_fi=0 OR lagersaldo_fi > 0 OR lagersaldo > 0 ) AND demo=0 AND Artiklar_fi.utpris_fi > 0 AND ej_med_fi=0 AND ej_med=0 AND isSalesBundle = 0 ";
	
	$select .= $extraSelect;
	include ("std_instore_special_fi.php"); // här tar vi bort alla specialprodukter samt kategorier som Inte skall visas/säljas i Finland
	$select .= $criteria;

	$select .= "ORDER BY kategori ASC, tillverkare ASC, Artiklar_fi.beskrivning_fi ASC ";
	
	$res = mysqli_query($conn_my, $select);

	return $res;

}
function extr($row) {
	global $lager, $linken, $bild, $priset;
	//http://www.cyberphoto.fi/?info_fi.php?article=
	// if ($row->lagersaldo > 0 || $row->lagersaldo_fi > 0)
	if ($row->lagersaldo > 0)
		$lager = "kyllä";
	else
		$lager = "ei";

	if ($row->link_fi)
		$linken = $row->link_fi;

	else
		// $linken = "info_fi.php?article=".$row->artnr;
		$linken = "info.php?article=".$row->artnr;
	if ($row->bild != "") {
		//$bild = $row->bild;
		$bild = "http://www.cyberphoto.fi" . eregi_replace("^(\.\.)", "",  $row->bild);
	}
	else {
		$bild = "";
	}

	$linken = "http://www.cyberphoto.fi/" . $linken . "&utm_source=hintaseuranta&utm_medium=cpc&utm_content=" . $row->artnr . "&utm_campaign=hintaseuranta_fi";

	//$priset = sprintf ("%10.0f", $row->utpris + $row->utpris * $row->momssats);
	$priset = round($row->utpris_fi + $row->utpris_fi * $row->momssats_fi, 0);

}
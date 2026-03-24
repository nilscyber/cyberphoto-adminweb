<?php
$räknare = 0;
include ("CConnect.php");

$select  = "SELECT tillverkare, Artiklar.beskrivning, Artiklar.artnr, Artiklar.artnr_lev, utpris, kategori, link, lagersaldo, bild, Moms.momssats FROM  ";
$select .= " Artiklar, Kategori, Tillverkare, Moms ";

$select .= "WHERE Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id AND ";

$select .= " ej_med=0 AND (demo=0 OR lagersaldo > 0)  AND (utgangen=0 OR lagersaldo > 0)  AND demo = 0 AND Artiklar.utpris > 0 "; //AND artnr not like '%pac' ";

$select .= " AND (  (Artiklar.kategori_id IN(7,51,52,57,203,213,329,346,402))  "; // videokameror
$select .= " OR (Artiklar.kategori_id IN(6,392,393,394,395))  "; // digitalkameror
$select .= " OR (Artiklar.kategori_id IN(329,129,28)) "; // bärbart lagringsmedia
$select .= " OR (Artiklar.kategori_id IN(126,217,218,219,220,221,222)) "; // kortläsare
$select .= " OR (Artiklar.kategori_id IN(109)) "; //compact flash
$select .= " OR (Artiklar.kategori_id IN(21)) "; // mmc
$select .= " OR (Artiklar.kategori_id IN(135)) "; //msd
$select .= " OR (Artiklar.kategori_id IN(115, 239, 240)) "; //ms
$select .= " OR (Artiklar.kategori_id IN(108)) "; // sm
$select .= " OR (Artiklar.kategori_id IN(191)) "; // tf
$select .= " OR (Artiklar.kategori_id IN(192)) "; // usb
$select .= " OR (Artiklar.kategori_id IN(208)) "; // xd
$select .= " OR (Artiklar.kategori_id IN(201)) "; // sd
$select .= " OR (Artiklar.kategori_id IN(336,339,340,341,238,337,338,371)) "; // telefoner och tillbehör
$select .= " OR (Artiklar.kategori_id IN(343,442,443)) "; // GPS navigering
$select .= " OR (Artiklar.kategori_id IN(127)) "; // mp spelare
$select .= " OR (Artiklar.kategori_id IN(173)) "; //
$select .= " OR (Artiklar.kategori_id IN(5,4)) "; // kompaktkameror, systemkameror
$select .= " OR (Artiklar.kategori_id IN(8,74)) "; // skrivare, filmskannrar, flatbäddsskannrar
$select .= " OR (Artiklar.kategori_id IN(24)) "; // bläckpatroner
$select .= " OR (Artiklar.kategori_id IN(171)) "; //  projektorer, hemmabio
$select .= " OR (Artiklar.kategori_id IN(372)) "; //  projektorer, data
$select .= " OR (Artiklar.kategori_id IN(10)) "; //  projektorer, dia
$select .= " OR (Artiklar.kategori_id IN(190,316,317,318,319)) "; //  projektionsdukar
$select .= " OR (Artiklar.kategori_id IN(206)) "; // videoprojektorstativ
$select .= " OR (Artiklar.kategori_id IN(321, 322, 323, 324)) "; // hörlurar
$select .= " OR (Artiklar.kategori_id IN(327, 347)) "; // skärmar TFT/LCD, plasmaTV
$select .= " OR (Artiklar.kategori_id IN(227)) "; // Digitalkamera stillbild/videokombination
$select .= " OR (Artiklar.kategori_id IN(389)) "; // DVD spelare
$select .= " OR (Artiklar.kategori_id IN(139)) "; // BluRay spelare
$select .= " OR (Artiklar.kategori_id IN(155)) "; // Micro-stereo
$select .= " OR (Artiklar.kategori_id IN(15,41,42,43,44,45,47,50,62,214,373,374,375,376,377,378,379,380,381,382,383,396,466)) "; // objektiv
$select .= " OR (Artiklar.kategori_id IN(2,23,414,415,449,450,451,465,215,236)) "; //

//$select .= " OR (Artiklar.kategori_id IN()) "; //


$select .= ")";

//$select .= " AND NOT (Artiklar.kategori_id = 486) ";

$select .= " order by kategori ASC, tillverkare ASC, beskrivning ASC ";


$res = mysqli_query($select);


function extr($row) {
	global $lager, $linken, $bild, $priset;

	if ($row->lagersaldo > 0)
		$lager = "ja";
	else
		$lager = "nej";

	//extract($row);
	if ($row->link)
		$linken = $row->link;

	else
		$linken = "info.php?article=".$row->artnr;
	if ($row->bild != "") {
		//$bild = $row->bild;
		$bild = "http://www.cyberphoto.se" . eregi_replace("^(\.\.)", "",  $row->bild);
	}
	else {
		$bild = "";
	}

	$linken = "http://www.cyberphoto.se/?" . $linken;

	//$priset = sprintf ("%10.0f", $row->utpris + $row->utpris * $row->momssats);
	$priset = round($row->utpris + $row->utpris * $row->momssats, 0);

}
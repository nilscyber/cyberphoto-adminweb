<?php
$teknTabell = "Tekn_objektiv";

include_once ("CTekniska_data.php");
include_once ("CConnect.php");

if ($comparepage != 'yes') {
	$num_of_articles = '1';
	if ($tekniska_data == "")
		$tekniska_artnr[1] = $artnr;
	else
		$tekniska_artnr[1] = $tekniska_data;

	$select = "SELECT artnr_tekniska_data FROM Info_page WHERE artnr = '" . $tekniska_artnr[1] . "'";
	$res = mysqli_query($select);
	$rs = mysqli_fetch_object($res);
	if (mysqli_num_rows($res) < 1 || $rs->artnr_tekniska_data == "" )
		$select2 = "SELECT * FROM " . $teknTabell . " WHERE artnr = '" . $tekniska_artnr[1] . "'";
	else
		$select2 = "SELECT * FROM " . $teknTabell . " WHERE artnr = '" . $rs->artnr_tekniska_data . "'";
	//echo $select2;
	$res = mysqli_query($select2);
	if (mysqli_num_rows($res) < 1 AND $infop)
		$empty = true;

	$row[1] = mysqli_fetch_object($res);
}
else  {
	for ($i = 1; $i <= $num_of_articles; $i++):
		if ($i > '5') break; // För säkerhets skull

		$select2 = "SELECT artnr_tekniska_data FROM Info_page WHERE artnr = '" . $tekniska_artnr[$i] . "'";
		$res2 = mysqli_query($select2);
		$rs = mysqli_fetch_object($res2);
		if (mysqli_num_rows($res2) < 1 || $rs->artnr_tekniska_data == "")
			$select[$i] = "SELECT * FROM " . $teknTabell . " WHERE artnr = '" . $tekniska_artnr[$i] . "'";
		else
			$select[$i] = "SELECT * FROM " . $teknTabell . " WHERE artnr = '" . $rs->artnr_tekniska_data . "'";
		$res[$i] = mysqli_query($select[$i]);
		$row[$i] = mysqli_fetch_object($res[$i]);
	endfor;
}
if (!$empty) {
?>

<?php if ($comparepage == 'yes'): ?>
<tr>
	<td valign="top" bgcolor="#FFFFFF"><font face="Verdana, Arial" size="1"></font></td>
<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial" size="1">
	<?php $artnr = $tekniska_artnr[$i]; include ("/home/www/cgi-bin/buyspot_tekn.php"); ?>
	</font></td>
<?php endfor; ?>
</tr>

<?php endif; ?>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Malli</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->typ); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Kiinnitys</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->fattning); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Rakenne</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->konstruktion); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Polttoväli</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->zoomomfång); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Valovoima</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_infoRound($row[$i]->ljusstyrka); ?> </font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Aukko</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->blandaromfang); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Tarkennusetäisyys</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->nargrans); ?> m</font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Suurennus</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->avbildningsskala); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Suodatinhalkaisija</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_infoRound($row[$i]->filterdiameter); ?> mm</font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Vastavalosuoja</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->motljusskydd); ?></font></td>
	<?php endfor; ?>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Mukana tulevat tarvikkeet</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->medfoljande_tillbehor); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Mitat</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->matt); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Paino</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info2($row[$i]->vikt); ?> g</font></td>
	<?php endfor; ?>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Muuta</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->kommentar); ?></font></td>
	<?php endfor; ?>
</tr>

<?php if ($comparepage == 'yes'): ?>


<?php endif; ?>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
	<?php if ($comparepage != 'yes'): ?>
	Vertaa toisiin objektiiveihin. Valitse listalta.
	<?php else: ?>
	Valitse toinen objektiivi listalta ja voit verrata sitä ylläolevaan.
	<?php endif; ?>
	</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php show_compNew($tekniska_artnr[$i], 'NULL', $kategori_id , 'Tekn_objektiv'); ?></font></td>
	<?php endfor; ?>
</tr>

<?php
} ?>

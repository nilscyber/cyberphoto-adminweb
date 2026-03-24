<?php
include_once ("CTekniska_data.php");
include_once ("CConnect.php");

$teknTabell = "Tekn_scanner";
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
	$res = mysqli_query($select2);
	if (mysqli_num_rows($res) < 1 AND $infop)
		$empty = true;

	$row[1] = mysqli_fetch_object($res);
}
else  {
	for ($i = 1; $i <= $num_of_articles; $i++):
		if ($i > '2') break; // För säkerhets skull

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
<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial" size="1">
	<?php $artnr = $tekniska_artnr[$i]; include ("/home/www/cgi-bin/buyspot_tekn_new.php"); ?>
	</font></td>
<?php endfor; ?>
</tr>

<?php endif; ?>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Optisk upplösning </font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->optisk_opplosning); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Optisk densitet</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->optisk_densitet); ?></font></td>
	<?php endfor; ?>
</tr
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Dokumentstorlek</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->dokumentstorlek); ?></font></td>
	<?php endfor; ?>
</tr>


<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Gränssnitt</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->granssnitt); ?></font></td>
	<?php endfor; ?>
</tr>


<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Hastighet</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->hastighet); ?> </font></td>
	<?php endfor; ?>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Mått</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->matt); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Vikt</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->vikt); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Systemkrav</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->systemkrav); ?></font></td>
	<?php endfor; ?>
</tr>

<?php if ($comparepage == 'yes'): ?>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Kommentar</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info3($row[$i]->kommentar); ?></font></td>
	<?php endfor; ?>
</tr>
<?php endif; ?>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
	<?php if ($comparepage != 'yes'): ?>
	Jämför med andra skannrar Välj ur listan.
	<?php else: ?>
	Välj en annan skanner ur listan för att jämföra med ovanstående.
	<?php endif; ?>
	</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php show_tek_scanner($tekniska_artnr[$i], 'NULL'); ?></font></td>
	<?php endfor; ?>
</tr>
<?php } ?>
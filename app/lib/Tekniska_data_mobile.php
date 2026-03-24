<?php
include_once ("CTekniska_data.php");

$teknTabell = "Tekn_mobile";
if ($comparepage != 'yes') {
	$num_of_articles = '1';
	if ($tekniska_data == "") {
		$tekniska_artnr[1] = $artnr; 
	} else {
		$tekniska_artnr[1] = $tekniska_data;
	}
	
	$select = "SELECT artnr_tekniska_data FROM cyberphoto.Info_page WHERE artnr = '" . $tekniska_artnr[1] . "'";
	$res = mysqli_query(Db::getConnection(), $select);
	$rs = mysqli_fetch_object($res);
	$tekniska_data_info = $rs->artnr_tekniska_data;
	
	if (mysqli_num_rows($res) < 1 || $rs->artnr_tekniska_data == "" ) {
		$select2 = "SELECT * FROM " . $teknTabell . " WHERE artnr = '" . $tekniska_artnr[1] . "'";			
	} else {
		$select2 = "SELECT * FROM " . $teknTabell . " WHERE artnr = '" . $rs->artnr_tekniska_data . "'";
	}
	$res = mysqli_query($select2);
	if (mysqli_num_rows($res) < 1 AND $infop) 
		$empty = true;

	$row[1] = mysqli_fetch_object($res);
	$tekniska_data_info = $row[1]->artnr;

} else {
	for ($i = 1; $i <= $num_of_articles; $i++): 		
		if ($i > '2') break; // För säkerhets skull
		
		$select2 = "SELECT artnr_tekniska_data FROM cyberphoto.Info_page WHERE artnr = '" . $tekniska_artnr[$i] . "'";
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

<?php if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR']) && $comparepage != 'yes') { ?>
<tr>
	<td colspan="2" valign="top" class="tek_white_row"><a target="_blank" href="/order/admin/Tekn_mobile.php?change=<?php echo $tekniska_data_info; ?>"><img border="0" src="/pic/editera.gif">&nbsp;Editera Tekniska data-tabellen</a></td>
</tr>
<?php } ?>
<?php if ($comparepage == 'yes'): ?>
<tr>
	<td valign="top" class="tek_white_row"></td>
<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" class="tek_white_row" align="right"><?php $artnr = $tekniska_artnr[$i]; include ("/home/www/cgi-bin/buyspot_tekn_new.php"); ?></td>
<?php endfor; ?>
</tr>

<?php endif; ?>

<tr>
	<td valign="top"><?php echo l('Battery'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params1); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('mob_filter10'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params22); ?></td>
	<?php endfor; ?>
</tr>
<!--
<tr>
	<td valign="top"><?php echo l('Battery time'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params2); ?></td>
	<?php endfor; ?>
</tr>
-->
<tr>
	<td valign="top"><?php echo l('Network'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params3); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Display'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params4); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Display size'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params5); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Resolution'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params6); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Memory card slot'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params7); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Internal memory for storage'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params8); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Processor'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params9); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('RAM'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params10); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Bluetooth'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params11); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Camera'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params12); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Video'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params13); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Operating system'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params14); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Built-in FM radio'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params15); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('GPS'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params16); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Type of SIM card'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params17); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Dimensions'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params18); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Weight'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params19); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('In the box'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info($row[$i]->params20); ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top"><?php echo l('Additional comment'); ?></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top"><?php show_info3($row[$i]->params21); ?></td>
	<?php endfor; ?>
</tr>

<tr>
	<td valign="top" class="tek_first_row">
	<?php if ($comparepage != 'yes'): ?>
	<?php echo l("Compare with another product"); ?>
	<?php else: ?>
	<?php echo l("Select another product from the list below to compare with the above"); ?> 
	<?php endif; ?>
	</td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" class="tek_first_row"><?php show_tek_mobile($tekniska_artnr[$i], 'NULL'); ?></td>
	<?php endfor; ?>
</tr>
<?php } ?>
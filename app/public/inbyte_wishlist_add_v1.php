	<?php
	if ($addStoreValue == "" && $addArtnr != "") {
		$addStoreValue = $monitor->getStoreValue($addArtnr);
	}
	?>
	<div>
	<form>
	<?php if ($addID !="") { ?>
		<input type="hidden" value="<?php echo $addID; ?>" name="addID">
		<input type="hidden" value=true name="submC">
	<?php } else { ?>
		<input type="hidden" value=true name="subm">
		<input type="hidden" value="yes" name="add">
	<?php } ?>
		<table border="0" cellpadding="5" cellspacing="0" style="border: 1px solid #999999; background-color: #C4E1FF">
		  <?php if ($addID !="") { ?>
		  <tr>
			<td align="left">Aktiv:</td>
			<td><input type="checkbox" name="addActive" value="yes" <?php if ($addActive == "1" || $addActive == "yes") { ?> checked <?php } ?>></td>
		  </tr>
		  <?php } ?>
		  <tr>
			<td align="left">Produkt: <b><font color="#FF0000">*</font></b></td>
			<td><input type="text" name="addProduct" size="60" value="<?php echo $addProduct; ?>" style="font-family: Verdana; font-size: 8pt"></td>
		  </tr>
		  <tr>
			<td align="left">Länk:</td>
			<td><input type="text" name="addLinc" size="60" value="<?php echo $addLinc; ?>" style="font-family: Verdana; font-size: 8pt"></td>
		  </tr>
		  <tr>
			<td align="left" valign="top">Egen notis: <b><font color="#FF0000">*</font></b></td>
			<td><textarea rows="3" name="addNote" cols="50"><?php echo $addNote; ?></textarea></td>
		  </tr>
		  <tr>
			<td align="left" valign="top"></td>
			<td align="left"><b><font color="#FF0000">*</font></b> Obligatoriskt</td>
		  </tr>
		</table>
		<p><input type="submit" value="<?php if ($addID !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #000000; font-weight: bold; background-color: #CCCCCC"></p>
	</form>
	</div>

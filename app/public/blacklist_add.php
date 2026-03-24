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
			<td align="left">IP-adress: <b><font color="#FF0000">*</font></b></td>
			<td><input type="text" name="addIP" size="20" value="<?php echo $addIP; ?>" style="font-family: Verdana; font-size: 8pt"></td>
		  </tr>
		  <tr>
			<td align="left">Upplagd av: <b><font color="#FF0000">*</font></b></td>
			<td><select size="1" name="addRecipient" style="font-family: Verdana; font-size: 8pt">
      		<option></option>
      		<?php $monitor->getAnstallda(); ?>
      		</select>
			</td>
		  </tr>
		  <tr>
			<td align="left" valign="top">Egen notis: *</td>
			<td><textarea rows="3" name="addComment" cols="30"><?php echo $addComment; ?></textarea></td>
		  </tr>
		  <tr>
			<td align="left" valign="top"></td>
			<td align="left"><b><font color="#FF0000">*</font></b> Obligatoriskt</td>
		  </tr>
		  <tr>
			<td align="left" valign="top"></td>
			<td align="left">* Om du lägger en egen notis kommer den med på aviseringen</td>
		  </tr>
		</table>
		<p><input type="submit" value="<?php if ($addID !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #000000; font-weight: bold; background-color: #CCCCCC"></p>
	</form>
	</div>

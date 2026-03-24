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
			<td align="left">Artikel nr: <b><font color="#FF0000">*</font></b></td>
			<td><input type="text" name="addArtnr" size="20" value="<?php echo $addArtnr; ?>" style="font-family: Verdana; font-size: 8pt"></td>
		  </tr>
		  <tr>
			<td align="left">Bevaka: <b><font color="#FF0000">*</font></b></td>
			<td>
				<select size="1" name="addType" style="font-size: 8pt; font-family: Verdana">
				<option value="0"<?php if ($addType == 0) { echo " selected"; } ?>>Lagersaldo</option>
				<option value="3"<?php if ($addType == 3) { echo " selected"; } ?>>Order nr</option>
				</select>
			</td>
		  </tr>
		  <tr>
			<td align="left">Uppfyll: <b><font color="#FF0000">*</font></b></td>
			<td>
				<select size="1" name="addMoreLess" style="font-size: 8pt; font-family: Verdana">
				<option value="0"<?php if ($addMoreLess == 0) { echo " selected"; } ?>>Mindre än</option>
				<option value="1"<?php if ($addMoreLess == 1) { echo " selected"; } ?>>Mer än</option>
				<option value="2"<?php if ($addMoreLess == 2) { echo " selected"; } ?>>Alla ändringar</option>
				</select>
				&nbsp;(spelar ingen roll om du bevakar en order)
			</td>
		  </tr>
		  <tr>
			<td align="left">Värde: <b><font color="#FF0000">*</font></b></td>
			<td><input type="text" name="addStoreValue" size="10" value="<?php echo $addStoreValue; ?>" style="font-family: Verdana; font-size: 8pt"> (endast heltal tillåtet)</td>
		  </tr>
		  <tr>
			<td align="left">Skickas till:</td>
			<td><select size="1" name="addRecipient" style="font-family: Verdana; font-size: 8pt">
      		<option></option>
      		<option value="ekonomi"<?php if ($addRecipient == "ekonomi") { echo " selected"; } ?>>OTRS ekonomikö</option>
      		<option value="inbyte"<?php if ($addRecipient == "inbyte") { echo " selected"; } ?>>OTRS inbyteskö</option>
      		<option value="kundtjanst"<?php if ($addRecipient == "kundtjanst") { echo " selected"; } ?>>OTRS kundtjänstkö</option>
      		<option value="produkt"<?php if ($addRecipient == "produkt") { echo " selected"; } ?>>OTRS säljkö</option>
      		<option value="service"<?php if ($addRecipient == "service") { echo " selected"; } ?>>OTRS servicekö</option>
      		<option value=""></option>
      		<option value="">Anger du inget val ovan skickas bevakningen till din e-post</option>
      		<option value=""></option>
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

	<?php
	if ($_COOKIE['login_ok'] != "true") {
		echo "<div class=\"container_loggin\">\n";
		echo "<span class=\"not_loggin\">Du är Ej inloggad och kommer därför inte kunna utföra åtgärden!</span>\n";
		echo "</div>\n";
		echo "<div class=\"clear\"></div>\n";
	}
	?>
	<div class="top10"></div>
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
			<td><input type="checkbox" name="addActive" value="yes" <?php if ($addActive == "-1" || $addActive == "yes") { ?> checked <?php } ?>></td>
		  </tr>
		  <?php } ?>
		  <tr>
			<td align="left">Filter: <b><font color="#FF0000">*</font></b></td>
			<td><input type="text" name="addWord" size="42" value="<?php echo $addWord; ?>" style="font-family: Verdana; font-size: 8pt"></td>
		  </tr>
		  <tr>
			<td align="left" valign="top">Egen notis:</td>
			<td><textarea rows="3" name="addComment" cols="35"><?php echo $addComment; ?></textarea></td>
		  </tr>
		  <tr>
			<td align="left" valign="top"></td>
			<td align="left"><b><font color="#FF0000">*</font></b> Obligatoriskt</td>
		  </tr>
		</table>
		<p><input type="submit" value="<?php if ($addID !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #000000; font-weight: bold; background-color: #CCCCCC"></p>
	</form>
	</div>

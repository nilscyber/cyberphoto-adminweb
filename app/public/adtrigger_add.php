<div class=top10>
	<?php 
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	?>
	<form>
	  <?php if ($addid !="") { ?>
	  <input type="hidden" value="<?php echo $addid; ?>" name="addid">
	  <input type="hidden" value=true name="submC">
	  <?php } else { ?>
	  <input type="hidden" value=true name="subm">
	  <input type="hidden" value="yes" name="add">
	  <?php } ?>
	  <table border="0" cellpadding="5" cellspacing="3" style="border-collapse: collapse; border: 1px solid #000000; background-color: #FFCC99">
		<tr>
		  <td><font face="Verdana" size="1">Gäller <b><font color="#FF0000">*</font></font></td>
		  <td><font face="Verdana" size="1"><input type="radio" value="0" name="addcountry"<?php if ($addcountry == 0) { echo " checked"; } ?>>&nbsp;<img src="sv_mini.jpg" border="0">&nbsp;&nbsp;<input type="radio" value="1" name="addcountry"<?php if ($addcountry == 1) { echo " checked"; } ?>>&nbsp;<img src="fi_mini.jpg" border="0"></font></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Gäller från <b><font color="#FF0000">*</font></b></font></td>
		  <td><input type="text" name="addfrom" size="20" value="<?php if ($addfrom == "") { echo date("Y-m-d H:i:s", time()); } else { echo $addfrom; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
		  <td>&nbsp;</td>
		  <td><font face="Verdana" size="1">Gäller till <b><font color="#FF0000">*</font></b></font></td>
		  <td><input type="text" name="addto" size="20" value="<?php if ($addto == "") { echo date("Y-m-d 23:59:59",mktime(0,0,0,date("n")+1,1-1,date("Y"))); } else { echo $addto; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Gruppering</font></td>
		  <td colspan="4">
		  <input type="text" name="addgroup" size="20" style="font-family: Verdana; font-size: 8pt" value="<?php echo $addgroup; ?>"></td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Namn</font></td>
		  <td colspan="4">
		  <input type="text" name="addrubrik" size="60" style="font-family: Verdana; font-size: 8pt" value="<?php echo $addrubrik; ?>"></td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Skall länkas till <b><font color="#FF0000">*</font></font></td>
		  <td colspan="4">
		  <input type="text" name="addlinc" size="60" style="font-family: Verdana; font-size: 8pt" value="<?php echo $addlinc; ?>"></td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Bild</font></td>
		  <td colspan="4">
		  <input type="text" name="addpicture" size="30" value="<?php echo $addpicture; ?>" style="font-family: Verdana; font-size: 8pt"></td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Kommentar</font></td>
		  <td colspan="4">
		  <textarea rows="2" name="addcomment" cols="55" style="font-family: Verdana; font-size: 8pt"><?php echo $addcomment; ?></textarea></td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Skapad av <b><font color="#FF0000">*</font></font></td>
		  <td colspan="4">
		  <select size="1" name="addcreatedby" style="font-family: Verdana; font-size: 8pt">
		  <option></option>
		  <?php $adtrigger->getAnstallda(); ?>
		  </select></td>
		</tr>
		</table>
	  <p><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #008080; font-weight: bold; background-color: #C0C0C0"></p>
	</form>        
</div>
<div class=top10>
<?php
if ($_COOKIE['login_ok'] != "true") {
	echo "<div class=\"container_loggin\">\n";
	echo "<span class=\"not_loggin\">Du är Ej inloggad och kommer därför inte kunna utföra åtgärden!</span>\n";
	echo "</div>\n";
	echo "<div class=\"clear\"></div>\n";
}
?>
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
		  <td><font face="Verdana" size="1">Gäller från <b><font color="#FF0000">*</font></b></font></td>
		  <td><input type="text" name="addfrom" size="20" value="<?php if ($addfrom == "") { echo date("Y-m-d H:i:s", time()); } else { echo $addfrom; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
		  <td>&nbsp;</td>
		  <td><font face="Verdana" size="1">Gäller till <b><font color="#FF0000">*</font></b></font></td>
		  <td><input type="text" name="addto" size="20" value="<?php if ($addto == "") { echo date("Y-m-d 23:59:59",mktime(0,0,0,date("n")+1,1-1,date("Y"))); } else { echo $addto; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Rubrik</font></td>
		  <td colspan="4">
		  <input type="text" name="addrubrik" size="60" style="font-family: Verdana; font-size: 8pt" value="<?php echo $addrubrik; ?>"></td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Rubrik_fi</font></td>
		  <td colspan="4">
		  <input type="text" name="addrubrik_fi" size="60" style="font-family: Verdana; font-size: 8pt" value="<?php echo $addrubrik_fi; ?>"></td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Rubrik_no</font></td>
		  <td colspan="4">
		  <input type="text" name="addrubrik_no" size="60" style="font-family: Verdana; font-size: 8pt" value="<?php echo $addrubrik_no; ?>"></td>
		</tr>
		<tr>
		  <td valign="top"><font face="Verdana" size="1">Ev. Payoff text</font></td>
		  <td colspan="4">
		  <textarea rows="6" name="addpayoff" cols="52"><?php echo $addpayoff; ?></textarea>
		</tr>
		<tr>
		  <td valign="top"><font face="Verdana" size="1">Ev. Payoff text_fi</font></td>
		  <td colspan="4">
		  <textarea rows="6" name="addpayoff_fi" cols="52"><?php echo $addpayoff_fi; ?></textarea>
		</tr>
		<tr>
		  <td valign="top"><font face="Verdana" size="1">Ev. Payoff text_no</font></td>
		  <td colspan="4">
		  <textarea rows="6" name="addpayoff_no" cols="52"><?php echo $addpayoff_no; ?></textarea>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Ev. Bild</font></td>
		  <td><input type="text" name="addpicture" size="20" value="<?php echo $addpicture; ?>" style="font-family: Verdana; font-size: 8pt"></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		</tr>
		<tr>
		  <td><font face="Verdana" size="1">Avser kategorier</font></td>
		  <td><input type="checkbox" name="addactive" value="yes" <?php if ($addactive != "0" && $addactive != "") { ?> checked <?php } ?>></td>
		  <td>&nbsp;</td>
		  <td><font face="Verdana" size="1">Ladda sida</font></td>
		  <td><select size="1" name="addtype">
		  <option value="0"<?php if ($addtype == "0") echo " selected";?>>Foto-video</option>
		  <option value="2"<?php if ($addtype == "2") echo " selected";?>>Mobiltelefoni</option>
		  <option value="4"<?php if ($addtype == "4") echo " selected";?>>Outdoor</option>
		  </select>
		  </td>
		</tr>
		<tr>
		  <td colspan="3">&nbsp;</td>
		  <td><font face="Verdana" size="1">Tvinga galleriläge</font></td>
		  <td><input type="checkbox" name="addgallerylist" value="yes" <?php if ($addgallerylist == -1) { ?> checked <?php } ?>></td>
		</tr>
		<tr>
		  <td valign="top"><font face="Verdana" size="1">Ev. kommentar</font></td>
		  <td colspan="4"><textarea rows="6" name="addcomment" cols="52"><?php echo $addcomment; ?></textarea></td>
		</tr>
		</table>
	  <p><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #008080; font-weight: bold; background-color: #C0C0C0"></p>
	</form>        
</div>
<div class=top10>
	<?php 
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	?>
	<form name="log">
	  <input type="hidden" value=true name="submArt">
	  <input type="hidden" value="<?php echo $show; ?>" name="addpricelist">
	  <input type="hidden" value="yes" name="addart">
	  <input type="hidden" value="<?php echo $show; ?>" name="show">
	  <table border="0" cellpadding="5" cellspacing="3" style="border-collapse: collapse; border: 1px solid #000000; background-color: #FFCC99">
		<tr>
		  <td><font face="Verdana" size="1"><?php if ($price->checkIfKat($show)) { ?>Kategori<?php } else { ?>Artikel nr<?php } ?></font></td>
		  <td>
		  <input type="text" name="addartnr" size="30" style="font-family: Verdana; font-size: 8pt" value="<?php echo $addartnr; ?>"></td>
		</tr>
		</table>
	  <p><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #008080; font-weight: bold; background-color: #C0C0C0"></p>
	</form>

</div>
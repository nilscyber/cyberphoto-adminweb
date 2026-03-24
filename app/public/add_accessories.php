<div class=top10>
	<?php 
	if ($wrongmess) {
		echo "<div class=\"wrongmess\">" . $wrongmess . "</div>";
	}
	if ($origin == "yes") {
		echo "<h2>Ändra produkt i alias " . $change . " (<span class=\"span_blue\">" . $product->getArticleName($change) . "</span>)</h2>\n";
	} else {
		echo "<h2>Lägg till produkt i alias " . $namealias . " (<span class=\"span_blue\">" . $product->getArticleName($namealias) . "</span>)</h2>\n";
	}
	/*
	if ($submArt && $addart != "") {
		echo "<h2><span class=\"span_red\">Visst vore det fint att kunna lägga till artikel $addartnr direkt här? Tyvärr måste Du fortfarande göra det via Adempiere.</span></h2>\n";
	}
	*/
	if ($addrecommended == "") {
		$addrecommended = 0;
	}
	?>
	<form name="log">
	<?php if ($origin == "yes") { ?>
	  <input type="hidden" value=true name="submC">
	<?php } else { ?>
	  <input type="hidden" value=true name="submArt">
	<?php } ?>
	  <input type="hidden" value="yes" name="alias">
	  <input type="hidden" value="<?php echo $namealias; ?>" name="change">
	  <input type="hidden" value="yes" name="addart">
	  <input type="hidden" value="<?php echo $show; ?>" name="show">
	  <table border="0" cellpadding="5" cellspacing="3" style="border-collapse: collapse; border: 1px solid #000000; background-color: #FFCC99">
		<tr>
		  <td>Artikel nr</td>
		  <td>
		  <input type="text" name="addartnr" size="20" value="<?php echo $addartnr; ?>"></td>
		</tr>
		<tr>
		  <td>Kommentar</td>
		  <td>
		  <input type="text" name="addcomment" size="40" value="<?php echo $addcomment; ?>"></td>
		</tr>
		<tr>
		  <td>Rekommenderat 0-99</td>
		  <td valign="top">
		  <input type="text" name="addrecommended" size="1" value="<?php echo $addrecommended; ?>"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td>
		  0 = Standard (visas endast i tillbehörsfliken)<br>
		  1 - 89 = Visas som rekommenderat tillbehör<br>
		  90 - 94 = Visas som liknande produkt<br>
		  95 - 98 = Visas som fler värdepaket<br>
		  99 = Ta bort tillbehöret som du lägger till<br>
		  </td>
		</tr>
		</table>
	  <p><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #008080; font-weight: bold; background-color: #C0C0C0"></p>
	</form>
</div>
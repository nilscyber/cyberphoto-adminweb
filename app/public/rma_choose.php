	<div class="bottom10">
		<form>
			<table cellpadding="2">
			 <tr>
				<td><b>Välj kategori:</b></td>
				<td>
					<select  style="font-family: Verdana; font-size: 11px" name="category" onchange="this.form.submit(this.options[this.selectedIndex].value)">
					<option></option>
					<?php $sold->getKategori(); ?>
					</select>
				</td>
				<td><input type="checkbox" name="discontinued" value="yes" onClick="submit()"<?php if ($discontinued == "yes") echo " checked";?>></td>
				<td>ta med utgångna produkter</td>
				<td><b>eller skriv in artikel nr:</b></td>
				<td><input type="text" name="article" size="30" value="<?php echo $article; ?>"></td>
				<td>&nbsp;</td>
			 </tr>
			</table>
		</form>
	</div>

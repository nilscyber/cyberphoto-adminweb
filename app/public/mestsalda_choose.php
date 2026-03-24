	<div>
		<form>
			<table>
			 <tr>
				<td>Välj kategori</td>
				<td>
					<select  style="font-family: Verdana; font-size: 11px" name="kategori_nr" onchange="this.form.submit(this.options[this.selectedIndex].value)">
					<option></option>
					<?php $sold->getKategori(); ?>
					</select>
				</td>
				<td>&nbsp;&nbsp;&nbsp;Visa "All Time High" istället</td>
				<td><input type="checkbox" name="alltimehigh" value="yes" onClick="submit()"<?php if ($alltimehigh == "yes") echo " checked";?>></td>
			 </tr>
			</table>
		</form>
	</div>

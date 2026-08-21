	<div>
		<form>
			<table>
			 <tr>
				<td>Välj kategori</td>
				<td>
					<select  style="font-family: Verdana; font-size: 11px" name="kategori_nr" onchange="this.form.submit(this.options[this.selectedIndex].value)">
					<option></option>
					<?php
						$catSql = "SELECT m_product_category_id, name FROM m_product_category ";
						$catSql .= "WHERE ad_client_id = 1000000 AND isactive = 'Y' ";
						$catSql .= "ORDER BY name ASC";
						$catRes = $pg ? @pg_query($pg, $catSql) : false;
						while ($catRes && $catRow = pg_fetch_assoc($catRes)) {
							$catId   = (int)$catRow['m_product_category_id'];
							$catName = htmlspecialchars($catRow['name'], ENT_QUOTES, 'UTF-8');
							echo "<option value=\"$catId\"";
							if ((int)$kategori_nr == $catId) {
								echo " selected";
							}
							echo ">$catName</option>\n";
						}
						if ($catRes) pg_free_result($catRes);
					?>
					</select>
				</td>
				<td>&nbsp;&nbsp;&nbsp;Visa "All Time High" istället</td>
				<td><input type="checkbox" name="alltimehigh" value="yes" onClick="submit()"<?php if ($alltimehigh == "yes") echo " checked";?>></td>
			 </tr>
			</table>
		</form>
	</div>

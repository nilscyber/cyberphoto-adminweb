<div class="container_white">

  <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
      <td width="20"><input type="checkbox" name="filter9" value="yes" onClick="submit()"<?php if ($filter9 == "yes") echo " checked";?>></font></td>
      <td width="148" align="left">
      <?php if ($filter9 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Täyskenno (24x36 mm)<?php else: ?>Fullformat (24x36 mm)<?php endif; ?></font></b></td>
      <td width="20"><input type="checkbox" name="filter7" value="yes" onClick="submit()"<?php if ($filter7 == "yes") echo " checked";?>></font></td>
      <td width="148" align="left">
      <?php if ($filter7 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Sisäinen etsin<?php else: ?>Inbyggd sökare<?php endif; ?></font></b></td>
      <td width="20"><input type="checkbox" name="filter8" value="yes" onClick="submit()"<?php if ($filter8 == "yes") echo " checked";?>></font></td>
      <td width="148" align="left">
      <?php if ($filter8 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Sääsuojattu<?php else: ?>Väderskyddad<?php endif; ?></font></b></td>
    </tr>
	
    <tr>
      <td width="20"><input type="checkbox" name="filter11" value="yes" onClick="submit()"<?php if ($filter11 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter11 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Peilitön<?php else: ?>Spegelfri<?php endif; ?></font></b></td>
      <td width="20"><input type="checkbox" name="filter10" value="yes" onClick="submit()"<?php if ($filter10 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter10 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Kääntyvä näyttö<?php else: ?>Vinklingsbar skärm<?php endif; ?></font></b></td>
      <td width="20"><input type="checkbox" name="filter6" value="yes" onClick="submit()"<?php if ($filter6 == "yes") echo " checked";?>></font></td>
      <td width="148" align="left">
      <?php if ($filter6 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>4K video<?php else: ?>Filmning i 4K<?php endif; ?></font></b></td>
    </tr>
	
    </table>

  <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
      <td align="left">
	
	<select name="marke" onchange="this.form.submit(this.options[this.selectedIndex].value)" size="1" <?php if ($marke > "0"): echo 'style="color: #85000D; font-weight: bold; font-family: Verdana; font-size: 11px"'; else: echo 'style="color: #000000; font-weight: normal; font-family: Verdana; font-size: 11px"'; endif;?>>
		<option value=""><?php if ($fi && !$sv): ?>Kaikki valmistajat<?php else: ?>Alla tillverkare<?php endif; ?></option>
		<option value="3"<?php if ($marke == "3") echo " selected";?>>Canon</option>
		<option value="14"<?php if ($marke == "14") echo " selected";?>>Fujifilm</option>
		<option value="7"<?php if ($marke == "7") echo " selected";?>>Nikon</option>
		<option value="9"<?php if ($marke == "9") echo " selected";?>>Olympus</option>
		<option value="24"<?php if ($marke == "24") echo " selected";?>>Panasonic</option>
		<option value="8"<?php if ($marke == "8") echo " selected";?>>Pentax</option>
		<option value="29"<?php if ($marke == "29") echo " selected";?>>Samsung</option>
		<option value="13"<?php if ($marke == "13") echo " selected";?>>Sony</option>
	</select>
	
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	<select name="varianter" onchange="this.form.submit(this.options[this.selectedIndex].value)" size="1" <?php if ($varianter > "0"): echo 'style="color: #85000D; font-weight: bold; font-family: Verdana; font-size: 11px"'; else: echo 'style="color: #000000; font-weight: normal; font-family: Verdana; font-size: 11px"'; endif;?>>
		<option value=""><?php if ($fi && !$sv): ?>Kaikki vaihtoehdot<?php else: ?>Alla kombinationer<?php endif; ?></option>
		<option value="hus"<?php if ($varianter == "hus") echo " selected";?>><?php if ($sv): ?>Endast kamerahus<?php else: ?>Kamerarunko<?php endif; ?></option>
		<option value="obj"<?php if ($varianter == "obj") echo " selected";?>><?php if ($sv): ?>Hus med objektiv<?php else: ?>Runko objektiivilla<?php endif; ?></option>
	</select>

	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

          <select size="1" name="prisMin" onchange="this.form.submit(this.options[this.selectedIndex].value)" <?php if ($prisMin > "0") echo 'style="color: #85000D; font-weight: bold; font-family: Verdana; font-size: 11px"';?>>
          <option value=""><?php if ($fi && !$sv): ?>Min. hinta<?php else: ?>Min. pris<?php endif; ?></option>
          <?php if ($fi) { ?>
			<option value="100" <?php if ($prisMin == "100") echo " selected";?>>100 </option>
			<option value="150" <?php if ($prisMin == "150") echo " selected";?>>150 </option>
			<option value="200" <?php if ($prisMin == "200") echo " selected";?>>200 </option>
			<option value="250" <?php if ($prisMin == "250") echo " selected";?>>250 </option>
			<option value="300" <?php if ($prisMin == "300") echo " selected";?>>300 </option>
			<option value="350" <?php if ($prisMin == "350") echo " selected";?>>350 </option>
			<option value="400" <?php if ($prisMin == "400") echo " selected";?>>400 </option>
			<option value="500" <?php if ($prisMin == "500") echo " selected";?>>500 </option>
			<option value="600" <?php if ($prisMin == "600") echo " selected";?>>600 </option>
			<option value="800" <?php if ($prisMin == "800") echo " selected";?>>800 </option>
			<option value="1000" <?php if ($prisMin == "1000") echo " selected";?>>1000 </option>
			<option value="1200" <?php if ($prisMin == "1200") echo " selected";?>>1200 </option>
			<option value="1500" <?php if ($prisMin == "1500") echo " selected";?>>1500 </option>
			<option value="2000" <?php if ($prisMin == "2000") echo " selected";?>>2000 </option>
			<option value="5000" <?php if ($prisMin == "5000") echo " selected";?>>5000 </option>
			<option value="10000" <?php if ($prisMin == "10000") echo " selected";?>>10000 </option>
        <?php } else { ?>
          <option value="4000" <?php if ($prisMin == "4000") echo " selected";?>>4000 kr</option>
          <?php if ($prisMax == "" || $prisMax > 5000) { ?>
          <option value="5000" <?php if ($prisMin == "5000") echo " selected";?>>5000 kr</option>
          <?php } ?>
          <?php if ($prisMax == "" || $prisMax > 6000) { ?>
          <option value="6000" <?php if ($prisMin == "6000") echo " selected";?>>6000 kr</option>
          <?php } ?>
          <?php if ($prisMax == "" || $prisMax > 7000) { ?>
          <option value="7000" <?php if ($prisMin == "7000") echo " selected";?>>7000 kr</option>
          <?php } ?>
          <?php if ($prisMax == "" || $prisMax > 8000) { ?>
          <option value="8000" <?php if ($prisMin == "8000") echo " selected";?>>8000 kr</option>
          <?php } ?>
          <?php if ($prisMax == "" || $prisMax > 9000) { ?>
          <option value="9000" <?php if ($prisMin == "9000") echo " selected";?>>9000 kr</option>
          <?php } ?>
          <?php if ($prisMax == "" || $prisMax > 10000) { ?>
          <option value="10000" <?php if ($prisMin == "10000") echo " selected";?>>10000 kr</option>
          <?php } ?>
          <?php if ($prisMax == "" || $prisMax > 15000) { ?>
          <option value="15000" <?php if ($prisMin == "15000") echo " selected";?>>15000 kr</option>
          <?php } ?>
          <?php if ($prisMax == "" || $prisMax > 20000) { ?>
          <option value="20000" <?php if ($prisMin == "20000") echo " selected";?>>20000 kr</option>
          <?php } ?>
        <?php } ?>
          </select>

	<?php if ($fi && !$sv) { ?>
	&nbsp;
	<?php } else { ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<?php } ?>

          <select size="1" name="prisMax" onchange="this.form.submit(this.options[this.selectedIndex].value)" <?php if ($prisMax > "0") echo 'style="color: #85000D; font-weight: bold; font-family: Verdana; font-size: 11px"';?>>
          <option value=""><?php if ($fi && !$sv): ?>Max. hinta<?php else: ?>Max. pris<?php endif; ?></option>
          <?php if ($fi) { ?>
			<option value="100" <?php if ($prisMax == "100") echo " selected";?>>100 </option>
			<option value="150" <?php if ($prisMax == "150") echo " selected";?>>150 </option>
			<option value="200" <?php if ($prisMax == "200") echo " selected";?>>200 </option>
			<option value="250" <?php if ($prisMax == "250") echo " selected";?>>250 </option>
			<option value="300" <?php if ($prisMax == "300") echo " selected";?>>300 </option>
			<option value="350" <?php if ($prisMax == "350") echo " selected";?>>350 </option>
			<option value="400" <?php if ($prisMax == "400") echo " selected";?>>400 </option>
			<option value="500" <?php if ($prisMax == "500") echo " selected";?>>500 </option>
			<option value="600" <?php if ($prisMax == "600") echo " selected";?>>600 </option>
			<option value="800" <?php if ($prisMax == "800") echo " selected";?>>800 </option>
			<option value="1000" <?php if ($prisMax == "1000") echo " selected";?>>1000 </option>
			<option value="1200" <?php if ($prisMax == "1200") echo " selected";?>>1200 </option>
			<option value="1500" <?php if ($prisMax == "1500") echo " selected";?>>1500 </option>
			<option value="2000" <?php if ($prisMax == "2000") echo " selected";?>>2000 </option>
			<option value="5000" <?php if ($prisMax == "5000") echo " selected";?>>5000 </option>
			<option value="10000" <?php if ($prisMax == "10000") echo " selected";?>>10000 </option>
        <?php } else { ?>
          <?php if ($prisMin == "" || $prisMin < 5000) { ?>
          <option value="5000" <?php if ($prisMax == "5000") echo " selected";?>>5000 kr</option>
          <?php } ?>
          <?php if ($prisMin == "" || $prisMin < 6000) { ?>
          <option value="6000" <?php if ($prisMax == "6000") echo " selected";?>>6000 kr</option>
          <?php } ?>
          <?php if ($prisMin == "" || $prisMin < 7000) { ?>
          <option value="7000" <?php if ($prisMax == "7000") echo " selected";?>>7000 kr</option>
          <?php } ?>
          <?php if ($prisMin == "" || $prisMin < 8000) { ?>
          <option value="8000" <?php if ($prisMax == "8000") echo " selected";?>>8000 kr</option>
          <?php } ?>
          <?php if ($prisMin == "" || $prisMin < 9000) { ?>
          <option value="9000" <?php if ($prisMax == "9000") echo " selected";?>>9000 kr</option>
          <?php } ?>
          <?php if ($prisMin == "" || $prisMin < 10000) { ?>
          <option value="10000" <?php if ($prisMax == "10000") echo " selected";?>>10000 kr</option>
          <?php } ?>
          <?php if ($prisMin == "" || $prisMin < 15000) { ?>
          <option value="15000" <?php if ($prisMax == "15000") echo " selected";?>>15000 kr</option>
          <?php } ?>
          <?php if ($prisMin == "" || $prisMin < 20000) { ?>
          <option value="20000" <?php if ($prisMax == "20000") echo " selected";?>>20000 kr</option>
          <?php } ?>
          <option value="100000" <?php if ($prisMax == "100000") echo " selected";?>>99000 kr</option>
        <?php } ?>
          </select>
	
      </td>
    </tr>
    </table>
    
</div>
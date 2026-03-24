<div class="container_white">

  <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
      <td width="20"><input type="checkbox" name="filter4" value="yes" onClick="submit()"<?php if ($filter4 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter4 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Ulkoinen laturi<?php else: ?>Extern laddare<?php endif; ?></font></b></td>
      <td width="20"><input type="checkbox" name="filter8" value="yes" onClick="submit()"<?php if ($filter8 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter8 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Sisäänrakennettu salama<?php else: ?>Inbyggd blixt<?php endif; ?></font></b></td>
      <td width="20"><input type="checkbox" name="filter2" value="yes" onClick="submit()"<?php if ($filter2 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter2 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Mikrofoniliitäntä<?php else: ?>Mikrofoningång<?php endif; ?></font></b></td>
    </tr>
    <tr>
      <td width="20"><input type="checkbox" name="filter5" value="yes" onClick="submit()"<?php if ($filter5 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter5 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Suodatinkierre<?php else: ?>Filtergänga<?php endif; ?></font></b></td>
      <td width="20"><input type="checkbox" name="filter10" value="yes" onClick="submit()"<?php if ($filter10 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter10 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Tarvikekenkä<?php else: ?>Har tillbehörssko<?php endif; ?></font></b></td>
      <td width="20"><input type="checkbox" name="filter3" value="yes" onClick="submit()"<?php if ($filter3 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter3 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Etsin<?php else: ?>Sökare<?php endif; ?></font></b></td>
    </tr>
    <tr>
      <td width="20"><input type="checkbox" name="filter6" value="yes" onClick="submit()"<?php if ($filter6 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter6 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Kaukosäädin<?php else: ?>Fjärrkontroll<?php endif; ?></font></b></td>
      <td width="20"><input type="checkbox" name="filter11" value="yes" onClick="submit()"<?php if ($filter11 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter11 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Kuulokeliitäntä<?php else: ?>Hörlursutgång<?php endif; ?></font></b></td>
      <td width="20"><input type="checkbox" name="filter12" value="yes" onClick="submit()"<?php if ($filter12 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($filter12 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Wi-Fi anslutning<?php else: ?>Wi-Fi liitäntä<?php endif; ?></font></b></td>
    </tr>
    <tr>
      <td colspan="6" align="left">
	<select name="marke" onchange="this.form.submit(this.options[this.selectedIndex].value)" size="1">
		<option value=""><?php if ($fi && !$sv): ?>Kaikki valmistajat<?php else: ?>Visa alla tillverkare<?php endif; ?></option>
		<option value="3"<?php if ($marke == "3") echo " selected";?>>Canon</option>
		<option value="1000032"<?php if ($marke == "1000032") echo " selected";?>>Contour</option>
		<option value="252"<?php if ($marke == "252") echo " selected";?>>Dörr</option>
		<option value="469"<?php if ($marke == "469") echo " selected";?>>GoPro</option>
		<option value="19"<?php if ($marke == "19") echo " selected";?>>JVC</option>
		<option value="24"<?php if ($marke == "24") echo " selected";?>>Panasonic</option>
		<option value="13"<?php if ($marke == "13") echo " selected";?>>Sony</option>
	</select>
      </td>
    </tr>
    </table>
    
</div>
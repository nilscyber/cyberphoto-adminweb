<div class="container_grey padding2">

	  <table border="0" cellpadding="3" cellspacing="0">
		<tr>
			<td colspan="6"><span class="obj_mount"><?php echo l("Lens mount"); ?></span></td>
		</tr>
		<tr>
		  <td><input type="radio" name="subMONTH" value="999" onClick="submit()"<?php if ($subMONTH == "" || $subMONTH == 999) echo " checked";?>></td>
		  <td width="70"><?php if ($subMONTH == "" || $subMONTH == 999) { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Kaikki<?php else: ?>Alla<?php endif; ?></b></td>
		  <?php if ($MF == 3) { ?>
			  <td><input type="radio" name="subMONTH" value="3" onClick="submit()"<?php if ($subMONTH == 3) echo " checked";?>></td>
			  <td width="70"><?php if ($subMONTH == 3) { ?><b><font color="#85000D"><?php } ?>Canon EF</b></td>
			  <td><input type="radio" name="subMONTH" value="33" onClick="submit()"<?php if ($subMONTH == 33) echo " checked";?>></td>
			  <td width="80"><?php if ($subMONTH == 33) { ?><b><font color="#85000D"><?php } ?>Canon EF-M</b></td>
		  <?php } ?>
		  <?php if ($MF == 7) { ?>
			  <td><input type="radio" name="subMONTH" value="7" onClick="submit()"<?php if ($subMONTH == 7) echo " checked";?>></td>
			  <td width="70"><?php if ($subMONTH == 7) { ?><b><font color="#85000D"><?php } ?>Nikon F</b></td>
			  <td><input type="radio" name="subMONTH" value="77" onClick="submit()"<?php if ($subMONTH == 77) echo " checked";?>></td>
			  <td width="70"><?php if ($subMONTH == 77) { ?><b><font color="#85000D"><?php } ?>Nikon 1</b></td>
		  <?php } ?>
		  <?php if ($MF == 13) { ?>
			  <td><input type="radio" name="subMONTH" value="13" onClick="submit()"<?php if ($subMONTH == 13) echo " checked";?>></td>
			  <td width="75"><?php if ($subMONTH == 13) { ?><b><font color="#85000D"><?php } ?>Sony A</b></td>
			  <td><input type="radio" name="subMONTH" value="133" onClick="submit()"<?php if ($subMONTH == 133) echo " checked";?>></td>
			  <td width="70"><?php if ($subMONTH == 133) { ?><b><font color="#85000D"><?php } ?>Sony E</b></td>
		  <?php } ?>
		  <!--
		  <?php if ($MF == 8) { ?>
			  <td><input type="radio" name="subMONTH" value="8" onClick="submit()"<?php if ($subMONTH == 8) echo " checked";?>></td>
			  <td width="70"><?php if ($subMONTH == 8) { ?><b><font color="#85000D"><?php } ?>Pentax K</b></td>
			  <td><input type="radio" name="subMONTH" value="88" onClick="submit()"<?php if ($subMONTH == 88) echo " checked";?>></td>
			  <td width="70"><?php if ($subMONTH == 88) { ?><b><font color="#85000D"><?php } ?>Pentax Q</b></td>
		  <?php } ?>
		  -->
		</tr>
	  </table>

</div>

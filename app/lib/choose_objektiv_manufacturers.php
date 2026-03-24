<div class="container_grey padding2">

	  <table border="0" cellpadding="3" cellspacing="0">
		<tr>
			<td colspan="16"><span class="obj_mount"><?php echo l("Manufacturer"); ?></span></td>
		</tr>
		<tr>
		  <td><input type="radio" name="subMF" value="999" onClick="submit()"<?php if ($subMF == "" || $subMF == 999) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == "" || $subMF == 999) { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Kaikki<?php else: ?>Alla<?php endif; ?></b></td>
		  <?php if ($MF == 3) { ?>
		  <td><input type="radio" name="subMF" value="3" onClick="submit()"<?php if ($subMF == 3) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 3) { ?><b><font color="#85000D"><?php } ?>Canon</b></td>
		  <?php } ?>
		  <?php if ($MF == 7) { ?>
		  <td><input type="radio" name="subMF" value="7" onClick="submit()"<?php if ($subMF == 7) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 7) { ?><b><font color="#85000D"><?php } ?>Nikon</b></td>
		  <?php } ?>
		  <?php if ($MF == 9 || $MF == 777) { ?>
		  <td><input type="radio" name="subMF" value="9" onClick="submit()"<?php if ($subMF == 9) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 9) { ?><b><font color="#85000D"><?php } ?>Olympus</b></td>
		  <?php } ?>
		  <?php if ($MF == 777 && $MF != 5) { ?>
		  <td><input type="radio" name="subMF" value="24" onClick="submit()"<?php if ($subMF == 24) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 24) { ?><b><font color="#85000D"><?php } ?>Panasonic</b></td>
		  <?php } ?>
		  <?php if ($MF == 8 && $MF != 777) { ?>
		  <td><input type="radio" name="subMF" value="8" onClick="submit()"<?php if ($subMF == 8) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 8) { ?><b><font color="#85000D"><?php } ?>Pentax</b></td>
		  <?php } ?>
		  <?php if ($MF == 13 && $MF != 777) { ?>
		  <td><input type="radio" name="subMF" value="13" onClick="submit()"<?php if ($subMF == 13) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 7) { ?><b><font color="#85000D"><?php } ?>Sony</b></td>
		  <?php } ?>
		  <?php if ($MF == 29 && $MF != 777) { ?>
		  <td><input type="radio" name="subMF" value="29" onClick="submit()"<?php if ($subMF == 29) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 29) { ?><b><font color="#85000D"><?php } ?>Samsung</b></td>
		  <?php } ?>
		  <?php if ($MF == 3 || $MF == 7 || $MF == 13 || $MF == 9) { ?>
		  <td><input type="radio" name="subMF" value="425" onClick="submit()"<?php if ($subMF == 425) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 425) { ?><b><font color="#85000D"><?php } ?>Lensbaby</b></td>
		  <?php } ?>
		  <?php if ($MF == 3 || $MF == 7 || $MF == 8 || $MF == 13 || $MF == 14 || $MF == 9 || $MF == 29 || $MF == 777) { ?>
		  <td><input type="radio" name="subMF" value="402" onClick="submit()"<?php if ($subMF == 402) echo " checked";?>></td>
		  <td width="85"><?php if ($subMF == 402) { ?><b><font color="#85000D"><?php } ?>Samyang</b></td>
		  <?php } ?>
		  <?php if ($MF != "" && $MF != 5 && $MF != 9 && $MF != 14 && $MF != 29) { ?>
		  <td><input type="radio" name="subMF" value="71" onClick="submit()"<?php if ($subMF == 71) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 71) { ?><b><font color="#85000D"><?php } ?>Sigma</b></td>
		  <?php } ?>
		  <?php if ($MF != "" && $MF != 9 && $MF != 5 && $MF != 14 && $MF != 29) { ?>
		  <td><input type="radio" name="subMF" value="42" onClick="submit()"<?php if ($subMF == 42) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 42) { ?><b><font color="#85000D"><?php } ?>Tamron</b></td>
		  <?php } ?>
		  <?php if ($MF != "" && $MF != 9 && $MF != 8 && $MF != 13 && $MF != 777 && $MF != 5 && $MF != 14 && $MF != 29) { ?>
		  <td><input type="radio" name="subMF" value="49" onClick="submit()"<?php if ($subMF == 49) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 49) { ?><b><font color="#85000D"><?php } ?>Tokina</b></td>
		  <?php } ?>
		  <?php if ($MF == 5) { ?>
		  <td><input type="radio" name="subMF" value="5" onClick="submit()"<?php if ($subMF == 5) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 5) { ?><b><font color="#85000D"><?php } ?>Leica</b></td>
		  <?php } ?>
		  <?php if ($MF == 14) { ?>
		  <td><input type="radio" name="subMF" value="14" onClick="submit()"<?php if ($subMF == 14) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 14) { ?><b><font color="#85000D"><?php } ?>Fujifilm</b></td>
		  <?php } ?>
		  <?php if ($MF != "" && $MF != 8 && $MF != 9 && $MF != 777 && $MF != 29) { ?>
		  <td><input type="radio" name="subMF" value="284" onClick="submit()"<?php if ($subMF == 284) echo " checked";?>></td>
		  <td width="50"><?php if ($subMF == 284) { ?><b><font color="#85000D"><?php } ?>Zeiss</b></td>
		  <?php } ?>
		  <?php if ($MF != 3 && $MF != 7 && ($MF == 777)) { ?>
		  <td><input type="radio" name="subMF" value="130" onClick="submit()"<?php if ($subMF == 130) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 130) { ?><b><font color="#85000D"><?php } ?>Voigtländer</b></td>
		  <?php } ?>
		</tr>
		<?php if ($MF == 3 || $MF == 7) { ?>
		<tr>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td><input type="radio" name="subMF" value="130" onClick="submit()"<?php if ($subMF == 130) echo " checked";?>></td>
		  <td width="70"><?php if ($subMF == 130) { ?><b><font color="#85000D"><?php } ?>Voigtländer</b></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		</tr>
		<?php } ?>
	  </table>

</div>

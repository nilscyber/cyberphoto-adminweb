<div id="systemkamcontainer">
<div class="roundtop">
<div class="sykam1"></div>
<div class="sykam2"></div>
<div class="sykam3"></div>
<div class="sykam4"></div>
</div>

<div class="content22">

              <table border="0" cellpadding="2" cellspacing="0">
                <tr>
                  <td><input type="radio" name="subMONTH" value="999" onClick="submit()"<?php if ($subMONTH == "" || $subMONTH == 999) echo " checked";?>></td>
                  <td width="70"><?php if ($subMONTH == "" || $subMONTH == 999) { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Alla<?php else: ?>Kaikki<?php endif; ?></b></td>
                  <?php if ($MF == 7) { ?>
					  <td><input type="radio" name="subMONTH" value="7" onClick="submit()"<?php if ($subMONTH == 7) echo " checked";?>></td>
					  <td width="70"><?php if ($subMONTH == 7) { ?><b><font color="#85000D"><?php } ?>Nikon F</b></td>
					  <td><input type="radio" name="subMONTH" value="77" onClick="submit()"<?php if ($subMONTH == 77) echo " checked";?>></td>
					  <td width="70"><?php if ($subMONTH == 77) { ?><b><font color="#85000D"><?php } ?>Nikon 1</b></td>
                  <?php } ?>
                  <?php if ($MF == 13) { ?>
					  <td><input type="radio" name="subMONTH" value="13" onClick="submit()"<?php if ($subMONTH == 13) echo " checked";?>></td>
					  <td width="75"><?php if ($subMONTH == 13) { ?><b><font color="#85000D"><?php } ?>Sony Alpha</b></td>
					  <td><input type="radio" name="subMONTH" value="133" onClick="submit()"<?php if ($subMONTH == 133) echo " checked";?>></td>
					  <td width="70"><?php if ($subMONTH == 133) { ?><b><font color="#85000D"><?php } ?>Sony NEX</b></td>
                  <?php } ?>
                  <?php if ($MF == 8) { ?>
					  <td><input type="radio" name="subMONTH" value="8" onClick="submit()"<?php if ($subMONTH == 8) echo " checked";?>></td>
					  <td width="70"><?php if ($subMONTH == 8) { ?><b><font color="#85000D"><?php } ?>Pentax K</b></td>
					  <td><input type="radio" name="subMONTH" value="88" onClick="submit()"<?php if ($subMONTH == 88) echo " checked";?>></td>
					  <td width="70"><?php if ($subMONTH == 88) { ?><b><font color="#85000D"><?php } ?>Pentax Q</b></td>
                  <?php } ?>
                </tr>
              </table>

</div>

<div class="roundbottom">
<div class="sykam4"></div>
<div class="sykam3"></div>
<div class="sykam2"></div>
<div class="sykam1"></div>
</div>
</div>

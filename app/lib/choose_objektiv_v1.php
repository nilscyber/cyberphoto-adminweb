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
                  <?php if ($MF == "" || $MF == 3) { ?>
                  <td><input type="radio" name="MF" value="3" onClick="submit()"<?php if ($MF == 3) echo " checked";?>></td>
                  <td width="70"><b><?php if ($MF == 3) { ?><font color="#85000D"><?php } ?>Canon</b></td>
                  <?php } ?>
                  <?php if ($MF== "" || $MF== 7) { ?>
                  <td><input type="radio" name="MF" value="7" onClick="submit()"<?php if ($MF== 7) echo " checked";?>></td>
                  <td width="70"><b><?php if ($MF== 7) { ?><font color="#85000D"><?php } ?>Nikon</b></td>
                  <?php } ?>
                  <?php if ($MF== "" || $MF== 8) { ?>
                  <td><input type="radio" name="MF" value="8" onClick="submit()"<?php if ($MF== 8) echo " checked";?>></td>
                  <td width="85"><b><?php if ($MF== 8) { ?><font color="#85000D"><?php } ?>Pentax</b></td>
                  <?php } ?>
                  <?php if ($MF== "" || $MF== 13) { ?>
                  <td><input type="radio" name="MF" value="13" onClick="submit()"<?php if ($MF== 13) echo " checked";?>></td>
                  <td width="70"><b><?php if ($MF== 13) { ?><font color="#85000D"><?php } ?>Sony</b></td>
            	  <?php } ?>
                  <?php if ($MF== "" || $MF== 777) { ?>
                  <td><input type="radio" name="MF" value="777" onClick="submit()"<?php if ($MF== 777) echo " checked";?>></td>
                  <td width="85"><b><?php if ($MF== 777) { ?><font color="#85000D"><?php } ?>Micro - 4/3</b></td>
                  <?php } ?>
                  <?php if ($MF == "" || $MF == 5) { ?>
                  <td><input type="radio" name="MF" value="5" onClick="submit()"<?php if ($MF == 5) echo " checked";?>></td>
                  <td width="70"><b><?php if ($MF == 5) { ?><font color="#85000D"><?php } ?>Leica M</b></td>
                  <?php } ?>
                  <?php if ($MF== "" || $MF== 9) { ?>
                  <td><input type="radio" name="MF" value="9" onClick="submit()"<?php if ($MF== 9) echo " checked";?>></td>
                  <td width="100"><b><?php if ($MF== 9) { ?><font color="#85000D"><?php } ?>Olympus - 4/3</b></td>
                  <?php } ?>
                  <?php if ($MF != "") { ?>
                  <td>
                  <a href="<?php echo $_SERVER['PHP_SELF']; ?>?MF=&subMF=">
                  <img src="/pricelist/arrow_right.gif" border="0"></a></td>
                  <td width="100"><a class="choose" href="<?php echo $_SERVER['PHP_SELF']; ?>?MF=&subMF="><?php if ($sv): ?>Ändra<?php else: ?>Vaihda valmistaja<?php endif; ?></a></td>
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
    
<div id="head_container">
<div class="container_white">

  <table border="0" cellpadding="3" cellspacing="0" width="100%">
    <tr>
      <td><?php if ($fi && !$sv): ?><b>Suodata vaihtoehtoja</b>&nbsp;(eri kombinaatiot voi antaa nolla vaihtoehtoja)<?php else: ?><b>Kombinera dina önskemål</b> (olika kombinationer kan ge noll resultat)<?php endif; ?></td>
    </tr>
  </table>

  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td width="25"><input type="checkbox" name="filter1" value="yes" onClick="submit()"<?php if ($filter1 == "yes") echo " checked";?><?php if ($filter2 == "yes" || $filter3 == "yes") echo " disabled";?>></font></td>
      <td width="200">
      <?php if ($filter1 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Android<?php else: ?>Android<?php endif; ?></font></b></td>
      <td width="25"><input type="checkbox" name="filter4" value="yes" onClick="submit()"<?php if ($filter4 == "yes") echo " checked";?>></font></td>
      <td width="200">
      <?php if ($filter4 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>3G (Eller 4G)<?php else: ?>3G (Eller 4G)<?php endif; ?></font></b></td>
      <td width="25"><input type="checkbox" name="filter7" value="yes" onClick="submit()"<?php if ($filter7 == "yes") echo " checked";?>></font></td>
      <td width="200">
      <?php if ($filter7 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Plats för minneskort<?php else: ?>Plats för minneskort<?php endif; ?></font></b></td>
    </tr>
    <tr>
      <td width="25"><input type="checkbox" name="filter2" value="yes" onClick="submit()"<?php if ($filter2 == "yes") echo " checked";?><?php if ($filter1 == "yes" || $filter3 == "yes") echo " disabled";?>></font></td>
      <td width="200">
      <?php if ($filter2 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Apple iOS<?php else: ?>Apple iOS<?php endif; ?></font></b></td>
      <td width="25"><input type="checkbox" name="filter5" value="yes" onClick="submit()"<?php if ($filter5 == "yes") echo " checked";?>></font></td>
      <td width="200">
      <?php if ($filter5 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Vattentålig<?php else: ?>Vattentålig<?php endif; ?></font></b></td>
      <td width="25"><input type="checkbox" name="filter8" value="yes" onClick="submit()"<?php if ($filter8 == "yes") echo " checked";?>></font></td>
      <td width="200">
      <?php if ($filter8 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Quad Core processor<?php else: ?>Quad Core processor<?php endif; ?></font></b></td>
    </tr>
    <tr>
      <td width="25"><input type="checkbox" name="filter3" value="yes" onClick="submit()"<?php if ($filter3 == "yes") echo " checked";?><?php if ($filter1 == "yes" || $filter2 == "yes") echo " disabled";?>></font></td>
      <td width="200">
      <?php if ($filter3 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Windows<?php else: ?>Windows<?php endif; ?></font></b></td>
      <td width="25"><input type="checkbox" name="filter6" value="yes" onClick="submit()"<?php if ($filter6 == "yes") echo " checked";?>></font></td>
      <td width="200">
      <?php if ($filter6 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Mindre än 10''<?php else: ?>Mindre än 10''<?php endif; ?></font></b></td>
      <td width="25"><input type="checkbox" name="filter9" value="yes" onClick="submit()"<?php if ($filter9 == "yes") echo " checked";?>></font></td>
      <td width="200">
      <?php if ($filter9 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Går att ringa med<?php else: ?>Går att ringa med<?php endif; ?></font></b></td>
    </tr>
   </table>

</div>
<div class="clear"></div>
</div>

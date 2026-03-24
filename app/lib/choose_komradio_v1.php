<div id="centrering">

<div id="stdlist">
<div class="roundtop">
<div class="infor1"></div>
<div class="infor2"></div>
<div class="infor3"></div>
<div class="infor4"></div>
</div>

<div class="content">


  <table border="0" cellpadding="3" cellspacing="0" width="100%">
    <tr>
      <td><font size="1" color="#000000" face="Verdana"><?php if ($sv): ?><b>Kombinera dina önskemål</b> (olika kombinationer kan ge noll resultat)<?php else: ?><b>Suodata vaihtoehtoja</b>&nbsp;(eri kombinaatiot voi antaa nolla vaihtoehtoja)<?php endif; ?></font></td>
    </tr>
  </table>

  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td width="25"><font face="Verdana" size="1"><input type="checkbox" name="filter1" value="yes" onClick="submit()"<?php if ($filter1 == "yes") echo " checked";?><?php if ($filter3 == "yes") echo " disabled";?>></font></td>
      <td width="200">
      <font face="Verdana" size="1"><?php if ($filter1 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>31 MHz<?php else: ?>31 MHz<?php endif; ?></font></b></td>
      <td width="25"><font face="Verdana" size="1"><input type="checkbox" name="filter4" value="yes" onClick="submit()"<?php if ($filter4 == "yes") echo " checked";?>></font></td>
      <td width="200">
      <font face="Verdana" size="1"><?php if ($filter4 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Bluetooth<?php else: ?>Bluetooth<?php endif; ?></font></b></td>
      <td width="25">&nbsp;</td>
      <td width="200">&nbsp;</td>
    </tr>
    <tr>
      <td width="25"><font face="Verdana" size="1"><input type="checkbox" name="filter2" value="yes" onClick="submit()"<?php if ($filter2 == "yes") echo " checked";?><?php if ($filter3 == "yes") echo " disabled";?>></font></td>
      <td width="200">
      <font face="Verdana" size="1"><?php if ($filter2 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>155 MHz<?php else: ?>155 MHz<?php endif; ?></font></b></td>
      <td width="25"><font face="Verdana" size="1"><input type="checkbox" name="filter5" value="yes" onClick="submit()"<?php if ($filter5 == "yes") echo " checked";?>></font></td>
      <td width="200">
      <font face="Verdana" size="1"><?php if ($filter5 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>GPS<?php else: ?>GPS<?php endif; ?></font></b></td>
      <td width="25">&nbsp;</td>
      <td width="200">&nbsp;</td>
    </tr>
    <tr>
      <td width="25"><font face="Verdana" size="1"><input type="checkbox" name="filter3" value="yes" onClick="submit()"<?php if ($filter3 == "yes") echo " checked";?><?php if ($filter1 == "yes" || $filter2 == "yes") echo " disabled";?>></font></td>
      <td width="200">
      <font face="Verdana" size="1"><?php if ($filter3 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>446 MHz<?php else: ?>446 MHz<?php endif; ?></font></b></td>
      <td width="25"><font face="Verdana" size="1"><input type="checkbox" name="filter6" value="yes" onClick="submit()"<?php if ($filter6 == "yes") echo " checked";?>></font></td>
      <td width="200">
      <font face="Verdana" size="1"><?php if ($filter6 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Fukt/damm-skyddad<?php else: ?>Fukt/damm-skyddad<?php endif; ?></font></b></td>
      <td width="25">&nbsp;</td>
      <td width="200">&nbsp;</td>
    </tr>
   </table>

</div>

<div class="roundbottom">
<div class="infor4"></div>
<div class="infor3"></div>
<div class="infor2"></div>
<div class="infor1"></div>
</div>
</div>


</div>
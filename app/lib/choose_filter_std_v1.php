<table border="0" cellpadding="2" cellspacing="0" width="100%">
  <tr>
      <td width="20"><input type="checkbox" name="pricefirst" value="yes" onclick="submit()"<?php if ($pricefirst == "yes") echo " checked";?>></td>
      <td align="left"><font face="Verdana" size="1"><?php if ($pricefirst == "yes") echo "<b>";?><?php if (!$sv): ?>Halvin ensin<?php else: ?>Visa billigast först<?php endif; ?></b></td>
      <td width="20"><input type="checkbox" name="filterstore" value="yes" onclick="submit()"<?php if ($filterstore == "yes") echo " checked";?>></td>
      <td align="left"><font face="Verdana" size="1"><?php if ($filterstore == "yes") echo "<b>";?><?php if (!$sv): ?>Kamerat varastossa<?php else: ?>Produkter i lager<?php endif; ?></b></td>
      <td width="20"><input type="checkbox" name="testresult" value="yes" onclick="submit()"<?php if ($testresult == "yes") echo " checked";?>></td>
      <td align="left"><font face="Verdana" size="1"><?php if ($testresult == "yes") echo "<b>";?><?php if (!$sv): ?>Testitulokset<?php else: ?>Visa testresultat<?php endif; ?></b></td>
  </tr>
</table>
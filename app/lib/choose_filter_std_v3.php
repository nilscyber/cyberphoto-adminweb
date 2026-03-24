<div id="stdlist">
<div class="roundtop">
<div class="infor1"></div>
<div class="infor2"></div>
<div class="infor3"></div>
<div class="infor4"></div>
</div>

<div class="content">

<table border="0" cellpadding="2" cellspacing="0">
  <tr>
      <td width="20"><input type="checkbox" name="filterstore" value="yes" onclick="submit()"<?php if ($filterstore == "yes") echo " checked";?>></td>
      <td width="100" align="left"><font face="Verdana" size="1"><?php if ($filterstore == "yes") echo "<b>";?><?php if ($fi && !$sv): ?>Kamerat varastossa<?php else: ?>Produkter i lager<?php endif; ?></b></td>
      <td width="20"><input type="checkbox" name="testresult" value="yes" onclick="submit()"<?php if ($testresult == "yes") echo " checked";?>></td>
      <td width="100" align="left"><font face="Verdana" size="1"><?php if ($testresult == "yes") echo "<b>";?><?php if ($fi && !$sv): ?>Testitulokset<?php else: ?>Visa testresultat<?php endif; ?></b></td>
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

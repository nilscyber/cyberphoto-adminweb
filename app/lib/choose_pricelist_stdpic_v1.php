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
    <td width="20">
    <input type="radio" value="pricelist" name="prislistan" onClick="submit()"<?php if ($prislistan == "pricelist") echo " checked"; ?>></td>
    <td width="65"><a onMouseOver="return escape('<?php if ($fi && !$sv): ?><b>Hintalista</b><br>Hintalista ilman kuvia.<?php else: ?><b>Prislista</b><br>Välj denna för prislista utan bilder.<?php endif; ?>')"><font size="1" face="Verdana"><?php if ($fi && !$sv): ?>Hintalista<?php else: ?>Prislista<?php endif; ?></font></a></td>
    <td width="20">
    <input type="radio" option value="" name="prislistan" onClick="submit()"<?php if ($prislistan == "") echo " checked"; ?>></font></td>
    <td><font size="1" face="Verdana"><a onMouseOver="return escape('<?php if ($fi && !$sv): ?><b>Kuvagalleria</b><br>Kuvallinen hintalista.<?php else: ?><b>Bildgalleri</b><br>Välj denna för att se prislistan med bilder för snabbt överblick.<?php endif; ?>')"><?php if ($fi && !$sv): ?>Kuvagalleria<?php else: ?>Bildgalleri<?php endif; ?></font></a></td>
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

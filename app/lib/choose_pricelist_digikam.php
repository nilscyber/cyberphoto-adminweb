<table border="0" cellpadding="2" cellspacing="0">
  <tr>
    <td width="20">
    <input type="radio" value="pricelist" name="prislistan" onClick="submit()"<?php if ($prislistan == "pricelist") echo " checked"; ?>></td>
    <td width="65"><a onMouseOver="return escape('<b>Prislista</b><br>Välj denna för prislista utan bilder.')"><font size="1" face="Verdana">Prislista</font></a></td>
    <td width="20">
    <input type="radio" option value="" name="prislistan" onClick="submit()"<?php if ($prislistan == "") echo " checked"; ?>></font></td>
    <td><font size="1" face="Verdana"><a onMouseOver="return escape('<b>Bildgalleri</b><br>Välj denna för att se prislistan med bilder för snabbt överblick.')">Bildgalleri</font></a></td>
  </tr>
</table>
<?php
if ($filter1 == "" && $filter2 == "" && $filter3 == "") {
	$filter1 = "yes";
	$filter2 = "yes";
	$filter3 = "yes";
}
?>
<div class="container_grey floatleft">

  <table border="0" cellpadding="2" cellspacing="0" width="400">
    <tr>
      <td width="25" align="right"><font face="Verdana" size="1"><input type="checkbox" name="filter1" value="yes" onclick="javascript:controldigikam('<?php echo $fi; ?>','<?php echo $sv; ?>')" <?php if ($filter1 == "yes") echo " checked";?>></font></td>
      <td width="105" align="left">
      <font face="Verdana" size="1"><?php if ($filter1 == "yes") { ?><b><font color="#000000"><?php } ?><?php if ($fi && !$sv): ?>Aloittelija<?php else: ?>Nybörjare<?php endif; ?></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter2" value="yes" onclick="javascript:controldigikam('<?php echo $fi; ?>','<?php echo $sv; ?>')"<?php if ($filter2 == "yes") echo " checked";?>></font></td>
      <td width="105" align="left">
      <font face="Verdana" size="1"><?php if ($filter2 == "yes") { ?><b><font color="#000000"><?php } ?><?php if ($fi && !$sv): ?>Harrastaja<?php else: ?>Entusiast<?php endif; ?></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter3" value="yes" onclick="javascript:controldigikam('<?php echo $fi; ?>','<?php echo $sv; ?>')"<?php if ($filter3 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <font face="Verdana" size="1"><?php if ($filter3 == "yes") { ?><b><font color="#000000"><?php } ?><?php if ($fi && !$sv): ?>Edistynyt<?php else: ?>Avancerade<?php endif; ?></font></b></td>
    </tr>
  </table>
    
</div>

<div class="container_white floatright">

  <table border="0" cellpadding="2" cellspacing="0" width="400">
    <tr>
      <td width="25" align="right"><font face="Verdana" size="1"><input type="checkbox" name="filter4" value="yes" onClick="submit()" <?php if ($filter4 == "yes") echo " checked";?>></font></td>
      <td width="105" align="left">
      <font face="Verdana" size="1"><?php if ($filter4 == "yes") { ?><b><font color="#000000"><?php } ?><?php if ($fi && !$sv): ?>Sopii valokuvaukseen<?php else: ?>Lämplig för foto<?php endif; ?></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter5" value="yes" onClick="submit()" <?php if ($filter5 == "yes") echo " checked";?>></font></td>
      <td width="105" align="left">
      <font face="Verdana" size="1"><?php if ($filter5 == "yes") { ?><b><font color="#000000"><?php } ?><?php if ($fi && !$sv): ?>Moottorisoitu<?php else: ?>Motordrivna<?php endif; ?></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter6" value="yes" onClick="submit()" <?php if ($filter6 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <font face="Verdana" size="1"><?php if ($filter6 == "yes") { ?><b><font color="#000000"><?php } ?><?php if ($fi && !$sv): ?>Tietokoneohjattu<?php else: ?>GoTo datorstyrda<?php endif; ?></font></b></td>
    </tr>
  </table>
    
</div>

<div class="clear"></div>
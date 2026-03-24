<?php
if ($filter1 == "" && $filter2 == "" && $filter3 == "") {
	$filter1 = "yes";
	$filter2 = "yes";
	$filter3 = "yes";
}
?>
<div id="systemkamcontainer">
<div class="roundtop">
<div class="sykam1"></div>
<div class="sykam2"></div>
<div class="sykam3"></div>
<div class="sykam4"></div>
</div>

<div class="content22">

  <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
      <td width="25" align="right"><font face="Verdana" size="1"><input type="checkbox" name="filter1" value="yes" onclick="javascript:controldigikam('<?php echo $fi; ?>','<?php echo $sv; ?>')" <?php if ($filter1 == "yes") echo " checked";?>></font></td>
      <td width="148" align="left">
      <?php if ($fi && !$sv) { ?>
      <a onMouseOver="return escape('<?php include ("explanation/systemkam/ny_fi.php"); ?>')">
      <?php } else { ?>
      <a onMouseOver="return escape('<?php include ("explanation/systemkam/ny.php"); ?>')">
      <?php } ?>
      <font face="Verdana" size="1"><?php if ($filter1 == "yes") { ?><b><font color="#000000"><?php } ?><?php if ($fi && !$sv): ?>Aloittelija<?php else: ?>Nybörjare<?php endif; ?></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter2" value="yes" onclick="javascript:controldigikam('<?php echo $fi; ?>','<?php echo $sv; ?>')"<?php if ($filter2 == "yes") echo " checked";?>></font></td>
      <td width="148" align="left">
      <?php if ($fi && !$sv) { ?>
      <a onMouseOver="return escape('<?php include ("explanation/systemkam/adv_fi.php"); ?>')">
      <?php } else { ?>
      <a onMouseOver="return escape('<?php include ("explanation/systemkam/adv.php"); ?>')">
      <?php } ?>
      <font face="Verdana" size="1"><?php if ($filter2 == "yes") { ?><b><font color="#000000"><?php } ?><?php if ($fi && !$sv): ?>Edistynyt<?php else: ?>Avancerad<?php endif; ?></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter3" value="yes" onclick="javascript:controldigikam('<?php echo $fi; ?>','<?php echo $sv; ?>')"<?php if ($filter3 == "yes") echo " checked";?>></font></td>
      <td align="left">
      <?php if ($fi && !$sv) { ?>
      <a onMouseOver="return escape('<?php include ("explanation/systemkam/pro_fi.php"); ?>')">
      <?php } else { ?>
      <a onMouseOver="return escape('<?php include ("explanation/systemkam/pro.php"); ?>')">
      <?php } ?>
      <font face="Verdana" size="1"><?php if ($filter3 == "yes") { ?><b><font color="#000000"><?php } ?><?php if ($fi && !$sv): ?>Ammattilainen<?php else: ?>Professionell<?php endif; ?></font></b></td>
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
    
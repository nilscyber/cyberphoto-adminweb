<div id="lenscontainer">
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <?php if ($MF == "kontakt" || $MF == "") { ?>
		<td><img border="0" src="/pricelist/kontakt1.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='kontakt'; document.lensform.submit();"><img border="0" src="/pricelist/kontakt.gif"></a></td>
	<?php } ?>
    <?php if ($MF == "om") { ?>
		<td><img border="0" src="/pricelist/om1.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='om'; document.lensform.submit();"><img border="0" src="/pricelist/om.gif"></a></td>
	<?php } ?>
    <?php if ($MF == "lev") { ?>
		<td><img border="0" src="/pricelist/lev.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='lev'; document.lensform.submit();"><img border="0" src="/pricelist/lev1.gif"></a></td>
	<?php } ?>
    <?php if ($MF == "vanliga") { ?>
		<td><img border="0" src="/pricelist/vanliga.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='vanliga'; document.lensform.submit();"><img border="0" src="/pricelist/vanliga1.gif"></a></td>
	<?php } ?>
    <?php if ($MF == "rma") { ?>
		<td><img border="0" src="/pricelist/serviceoretur1.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='rma'; document.lensform.submit();"><img border="0" src="/pricelist/serviceoretur.gif"></a></td>
	<?php } ?>
    <?php if ($MF == "copy") { ?>
		<td><img border="0" src="/pricelist/copyright.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='copy'; document.lensform.submit();"><img border="0" src="/pricelist/copyright1.gif"></a></td>
	<?php } ?>

  </tr>
</table>
</div>
<div id="lenscontainer">
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <?php if ($MF == 3) { ?>
		<td><img border="0" src="/pricelist/flik_2012_canon.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='3'; document.lensform.submit();"><img border="0" src="/pricelist/flik_2012_canon2.gif"></a></td>
	<?php } ?>
    <?php if ($MF == 7) { ?>
		<td><img border="0" src="/pricelist/flik_2012_nikon.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='7'; document.lensform.submit();"><img border="0" src="/pricelist/flik_2012_nikon2.gif"></a></td>
	<?php } ?>
    <?php if ($MF == 8) { ?>
		<td><img border="0" src="/pricelist/flik_2012_pentax.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='8'; document.lensform.submit();"><img border="0" src="/pricelist/flik_2012_pentax2.gif"></a></td>
	<?php } ?>
    <?php if ($MF == 13) { ?>
		<td><img border="0" src="/pricelist/flik_2012_sony.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='13'; document.lensform.submit();"><img border="0" src="/pricelist/flik_2012_sony2.gif"></a></td>
	<?php } ?>
    <?php if ($MF == 24) { ?>
		<td><img border="0" src="/pricelist/flik_2012_panasonic.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='24'; document.lensform.submit();"><img border="0" src="/pricelist/flik_2012_panasonic2.gif"></a></td>
	<?php } ?>
    <?php if ($MF == 9) { ?>
		<td><img border="0" src="/pricelist/flik_2012_olympus.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='9'; document.lensform.submit();"><img border="0" src="/pricelist/flik_2012_olympus2.gif"></a></td>
	<?php } ?>
    <?php if ($MF == 999) { ?>
		<td><img border="0" src="/pricelist/flik_2012_ovriga.gif"></td>
	<?php } else { ?>
		<td><a href="#" onclick="document.lensform.MF.value='999'; document.lensform.submit();"><img border="0" src="/pricelist/flik_2012_ovriga2.gif"></a></td>
	<?php } ?>
		<td><a href="/<?php if ($fi && !$sv) { ?>pri_studio_fi<?php } elseif ($fi && $sv) { ?>pri_studio_fi_se<?php } else { ?>pri_studio<?php } ?>.php"><img border="0" src="/pricelist/flik_2012_studio2.gif"></a></td>
  </tr>
</table>
</div>
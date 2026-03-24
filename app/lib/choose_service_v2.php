<div id="tab_bar">
<?php if ($MF == "kontakt" || $MF == "") { ?>
	<div class="top_red"><?php echo l('Contact us'); ?></div>
<?php } else { ?>
	<a href="#" onclick="document.lensform.MF.value='kontakt'; document.lensform.submit();">
	<div class="top_white"><?php echo l('Contact us'); ?></div></a>
<?php } ?>
<?php if ($MF == "om") { ?>
	<div class="top_red"><?php echo l('About the company'); ?></div>
<?php } else { ?>
	<a href="#" onclick="document.lensform.MF.value='om'; document.lensform.submit();">
	<div class="top_white"><?php echo l('About the company'); ?></div></a>
<?php } ?>
<?php if ($MF == "lev") { ?>
	<div class="top_red"><?php echo l('Terms of delivery'); ?></div>
<?php } else { ?>
	<a href="#" onclick="document.lensform.MF.value='lev'; document.lensform.submit();">
	<div class="top_white"><?php echo l('Terms of delivery'); ?></div></a>
<?php } ?>
<?php if (!$no) { ?>
	<?php if ($MF == "vanliga") { ?>
		<div class="top_red"><?php echo l('Frequently asked questions'); ?></div>
	<?php } else { ?>
		<a href="#" onclick="document.lensform.MF.value='vanliga'; document.lensform.submit();">
		<div class="top_white"><?php echo l('Frequently asked questions'); ?></div></a>
	<?php } ?>
<?php } ?>
<?php if ($MF == "rma") { ?>
	<div class="top_red"><?php echo l('Service and returns'); ?></div>
<?php } else { ?>
	<a href="#" onclick="document.lensform.MF.value='rma'; document.lensform.submit();">
	<div class="top_white"><?php echo l('Service and returns'); ?></div></a>
<?php } ?>
<?php if ($MF == "copy") { ?>
	<div class="top_red">&copy; Copyright</div>
<?php } else { ?>
	<a href="#" onclick="document.lensform.MF.value='copy'; document.lensform.submit();">
	<div class="top_white">&copy; Copyright</div></a>
<?php } ?>

<div class="clear"></div>
</div>
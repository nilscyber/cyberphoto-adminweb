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
                  <td align="center">
				  <a onMouseOver="return escape('<?php if ($sv): include ("explanation/lens/motljusskydd.php"); else: include ("explanation/lens/motljusskydd_fi.php"); endif; ?>')" href="<?php echo $_SERVER['PHP_SELF']; ?>?ArticleIDGroup=16&sortera=<?php echo $sortera; ?>&prislistan=<?php echo $prislistan; ?>&onshelf=<?php echo $onshelf; ?>">
				  <img border="0" src="/pricelist/obj_mtj.png"></a></td>
                  <td align="center">
				  <a onMouseOver="return escape('<?php if ($sv): include ("explanation/lens/objektivlock.php"); else: include ("explanation/lens/objektivlock_fi.php"); endif; ?>')" href="<?php echo $_SERVER['PHP_SELF']; ?>?ArticleIDGroup=174&sortera=<?php echo $sortera; ?>&prislistan=<?php echo $prislistan; ?>&onshelf=<?php echo $onshelf; ?>">
				  <img border="0" src="/pricelist/obj_lock.png"></a></td>
                  <td align="center">
				  <a onMouseOver="return escape('<?php if ($sv): include ("explanation/lens/other.php"); else: include ("explanation/lens/other_fi.php"); endif; ?>')" href="<?php echo $_SERVER['PHP_SELF']; ?>?ArticleIDGroup=33,516,1000070&sortera=<?php echo $sortera; ?>&prislistan=<?php echo $prislistan; ?>&onshelf=<?php echo $onshelf; ?>">
				  <img border="0" src="/pricelist/obj_tillb.png"></a>
				  </td>
                  <td align="center">
					<?php if (!$sv && $fi) { ?>
						<a href="pri_filter_fi.php">
					<?php } elseif ($sv && $fi) { ?>
						<a href="pri_filter_fi_se.php">
					<?php } else { ?>
						<a href="pri_filter.php">
					<?php } ?>
				  <img border="0" src="/pricelist/obj_t_filter.png"></a></td>
                  <td align="center">
				  <a href="faq/lenscomp.php">
				  <img border="0" src="/pricelist/obj_guide.png"></a></td>
                  <td align="center">
				  <a href="faq/objektivfrkrtn/objektivfrkrtn.php">
				  <img border="0" src="/pricelist/obj_fakta.png"></a></td>
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

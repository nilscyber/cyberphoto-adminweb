      <?php
	  if ($btype == "") {
		 $btype = 0;
	  }
	  if ($fi && !$sv) {
		$blogg_target = "blogi";
	  } else {
		$blogg_target = "bloggen";
	  }
	  ?>
	  <form method="GET" action="/<?php echo $blogg_target; ?>">
	  <div class="blogg_filtercontainer">
	  <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td width="120" class="filter" align="left"><input type="text" name="search" value="<?php echo $search; ?>" size="20"></td>
          <td width="25" class="filter"><input type="image" src="/blogg/<?php if ($fi && !$sv): ?>search_fi<?php else: ?>search2<?php endif; ?>.gif"></td>
          <td width="100" class="filterText" align="right"><?php echo l('Choose month'); ?>&nbsp;</td>
          <td width="150" class="filter"><?php $blogg->getBloggMonth_v2($month2,false); ?></td>
          <td width="25" class="filter"><input type="radio" value="0" name="btype" <?php if ($btype == 0) echo " checked"; ?> onchange="this.form.submit();"></td>
          <td width="40" class="filterText"><?php echo l('All'); ?></td>
          <td width="25" class="filter"><input type="radio" value="1" name="btype" <?php if ($btype == 1) echo " checked"; ?> onchange="this.form.submit();"></td>
          <td width="60" class="filterText"><?php echo l('Tests'); ?></td>
          <td width="25" class="filter"><input type="radio" value="2" name="btype" <?php if ($btype == 2) echo " checked"; ?> onchange="this.form.submit();"></td>
          <td width="60" class="filterText"><?php echo l('News'); ?></td>
		  <?php if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) { ?>
			  <td width="25" class="filter"><input type="checkbox" value="yes" name="show_upcomming" <?php if ($show_upcomming == "yes") echo " checked"; ?> onchange="this.form.submit();"></td>
			  <td width="60" class="filterText">Kommande</td>
		  <?php } ?>
		  <?php if ($fi && !$sv) { ?>
			<td class="filter" align="right">&nbsp;</td>
		  <?php } else { ?>
			<td class="filter" align="right"><a target="_blank" href="http://www.cyberphoto.se/rss/blogg.php"><img border="0" src="/blogg/rss.gif"></a></td>
		  <?php } ?>
        </tr>
	</table>
	</div>
	</form>

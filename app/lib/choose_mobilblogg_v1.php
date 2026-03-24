      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td width="120" class="filter" align="left"><input type="text" name="search" value="<?php echo $search; ?>" size="20"></td>
          <td width="25" class="filter"><input type="image" src="blogg/<?php if ($fi && !$sv): ?>search_fi<?php else: ?>search2<?php endif; ?>.gif"></td>
          <td width="95" class="filterText" align="center">Välj månad</td>
          <td width="150" class="filter"><?php $blogg->getBloggMonth_v2($month2,true); ?></td>
          <td class="filter" align="right"><a target="_blank" href="http://www.cyberphoto.se/rss/blogg.php"><img border="0" src="/blogg/rss.gif"></a></td>
        </tr>
	</table>

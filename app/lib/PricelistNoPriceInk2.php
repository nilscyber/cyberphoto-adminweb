<?php

# PHP Pricelist script
# author	Nils Kohlström
# version	2000-11-20


	# Read articles using criteria
        # if $criteria is not defined, all Articles will be selected therefore:
        if ($critiera = "")
         { $criteria = "WHERE Artiklar.kategori_id=0"; }

     	 $articles = readArticlesWithCriteria($criteria);
	$countertr = 0;
	while (list($key, $article) = each($articles)) :
?>
	<?php $countertr++; ?>
	<tr>
	<td>
	<?php
	if ($article->link) {
		print "<a class=\"mainlink\" href=\"".$article->link."\">";
		//print "<a class=\"mainlink\" href=\"info_ink.php?article=".$article->artnr."\">";
	}
	else {
		print "<a class=\"mainlink\" href=\"info_ink.php?article=".$article->artnr."\">";
	}

        if ($article->tillverkare <> ".") {
           print $article->tillverkare;
           print " ";
	}
	print $article->beskrivning;

        print "</a>";

        ?>
	</font></td>
	</tr>
	<?php if ($countertr > 40) {
	$countertr = 0;
	?>
	</table>
	</td>
	<td valign="top" width="300">
	<table border="0" cellpadding="0" cellspacing="1">
	<?php } ?>
	<?php endwhile; ?>
	<?php if ($countertr <= 40 && $ArticleIDGroup != 3) {
	?>
	</table>
	</td>
	<td valign="top" width="<% if ($ArticleIDGroup = 2): %>300<% else: %>600<% endif; %>">&nbsp;</td>
	</table>
	<?php } ?>
	</table>

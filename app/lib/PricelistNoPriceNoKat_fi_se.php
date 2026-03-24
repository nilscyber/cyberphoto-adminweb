<?php

# PHP Pricelist script
# author	Nils Kohlström
# version	2000-11-20


	# Read articles using criteria
        # if $criteria is not defined, all Articles will be selected therefore:
        if ($critiera = "")
         { $criteria = "WHERE Artiklar.kategori_id=0"; }

     	 $articles = readArticlesWithCriteria($criteria);
	
	$countheader = 1;

	while (list($key, $article) = each($articles)) :

	if ($countheader == 1) {
		if ($headline) {
	?>
		<tr>
		<td colspan="2"><font color="#2B2B2B" face="Verdana,Arial" size="1"><b>
        <?php              
                             echo "$headline - Utgångna modeller";
                             //print "<a name=\"$headline\">";
        ?>
                </td>
		</tr>
	<tr>
	    <td colspan="2" height="2"><img border="0" src="/pic/galleryline.jpg" width="600" height="2"></td>
  	</tr>
  	
	<?php
	$countheader = 0;
		}
	}
	?>


	<tr onmouseover="HiLite(this);">
	<td><font color="#2B2B2B" size="1" face="Verdana, Arial">
	<?php
	if ($article->link) {
		print "<A HREF=\"".$article->link."\">";
	}
	else {
		print "<A HREF=\"info_fi_se.php?article=".$article->artnr."\">";
	}

        if ($article->tillverkare <> ".") {
           print $article->tillverkare;
           print " ";
       }
       print $article->beskrivning;
        if ($article->kommentar) {
                print " ";
                print $article->kommentar;
        }

        //if ($article->link) {
                        print "</A>";
        //}

        ?>
	</font></td>
	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php
		if ($article->link <> "")
			{
			 print "<A href=\"".$article->link."\">";
			 if (eregi (".jpg$", $article->link) || eregi (".gif$", $article->link) || eregi (".jpeg$", $article->link))
			 	print "<IMG SRC=\"/pic/bild.gif\" border=0 >" ;
			 else
			 	print "<IMG SRC=\"/pic/02.gif\" border=0 >" ;

			}
			
		 else
			{
			print "<A HREF=\"info_fi_se.php?article=".$article->artnr."\">";
			print "<IMG SRC=\"/pic/02.gif\" border=0 >";
			}
		print "</A>";
	?>

	</font>&nbsp;</td>
	</tr>
<?php endwhile; ?>

<?php $headline = "" ?>

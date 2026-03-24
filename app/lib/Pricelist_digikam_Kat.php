<?php

# PHP Pricelist script
# author	Nils Kohlström
# version	2000-11-20


	# Read articles using criteria
        # if $criteria is not defined, all Articles will be selected therefore:
        if ($critiera = "")
         { $criteria = "WHERE Artiklar.kategori_id=0"; }

     	 $articles = readArticlesWithCriteria($criteria);

	# Reset category
	$current_category = "";
	$current_betyg = "";
	if ($headline) {
	?>
		<tr>
		<td bgcolor="#ECECE6" colspan="6"><font color="#2B2B2B" face="Verdana,Arial" size="1"><b>
        <?php              
                             echo "$headline";
                             //print "<a name=\"$headline\">";
        ?>
                </td>
		</tr>
	<?php		}
	
	while (list($key, $article) = each($articles)) :
    //if ( !(ereg("^beg", $article->artnr)) || (ereg("^beg", $article->artnr) && ($article->lagersaldo > '0') ) ): // överflödig iom att "utgången" används
?>


	
	<?php 	
	if ($sortera == "testres") {
			if ($article->betyg != $current_betyg) {
				$current_betyg = $article->betyg;
				
				$sel = "SELECT betygBeskrivning FROM Betyg WHERE betygId = " . $article->betyg;
				
				$res = mysqli_query($sel);
				$row = mysqli_fetch_object($res);
			
				?>

		<tr>
		<td bgcolor="#ECECE6" colspan="5"><font color="#85000D" face="Verdana,Arial" size="1"><b>
		<?php echo $row->betygBeskrivning; ?>

                </td>
		</tr>
	<?php	}
		}
	?>		

	<tr>
	<td bgcolor="#ECECE6"><font color="#2B2B2B" size="1" face="Verdana, Arial">
	<?php
	if ($article->link) {
		print "<A HREF=\"".$article->link."\">";
	}
	else {
		print "<A HREF=\"info.php?article=".$article->artnr."\">";
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

                print "</A>";
	?>
	</font></td>
	
	<?php if ($sortera == "ccd"): ?>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php  if ($article->ccd != NULL)
		{	
			$ccd = number_format ($article->ccd, 0, ",", " ");
			print $ccd;
			
					}
		else
		{
			print "uppgift saknas";
		}
	?>	
		
	</font></td>
	<?php elseif ($sortera == "zoom"): ?>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php  if ($article->zoom_digikam != NULL)
		{
			if ($article->zoom_digikam == 999)
				print "Beror på objektiv";
			//elseif ($article->zoom == "0")
			//	print "fast optik";
			else
				printf ("%.0fx", $article->zoom_digikam);
					}
		else
		{
			print "uppgift saknas";
		}
		
	?>	
		
	</font></td>
	<?php endif; ?>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<?php  if ($article->utpris>0)
		{
			printf ("%10.0f kr", $article->utpris);
					}
		else
		{
			print "&nbsp;&nbsp;\n";
		}
	?>
	</font></td>

	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<?php
		if ($article->utpris>0)
		{
			printf ("%10.0f kr", $article->utpris + $article->utpris * $article->momssats);
		}
		else
		{
			print "&nbsp;&nbsp;";
		}
		print "</font></td>\n";
	?>

	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php
	 
	 if ($article->no_buy == 0)
	 {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG SRC=\"/pic/01.gif\" border=0>";
			print "</A>";
		}
		else
		{
			print "&nbsp;&nbsp;";
		}
	?>
	</font></td>
	<td align="left" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
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
			print "<A HREF=\"info.php?article=".$article->artnr."\">";
			print "<IMG SRC=\"/pic/02.gif\" border=0 >";
			}
		print "</A>";
	?>

	</font></td>
	</tr>
<?php //endif; 
?>
<?php endwhile; ?>

<?php $headline = "" ?>
<?php
include ("CDateCheck.php");
require_once("CStyleCode.php");
require_once("CBasket.php");
$style = new CStyleCode();
$bask = new CBasket();

# PHP Pricelist script
# author	Nils Kohlström / Thomas Lövgren
# version	2006-09-01


	# Read articles using criteria
        # if $criteria is not defined, all Articles will be selected therefore:
        if ($critiera = "")
         { $criteria = "WHERE Artiklar.kategori_id=0"; }

     	 $articles = readArticlesWithCriteria($criteria);

	if (count($articles) < 1) {
		$headline = "Det finns inget objektiv som matchar dina önskemål."; // fast med röd text,, 
	}

	if ($headline) {
	?>
		<tr>
	<?php if (count($articles) < 1) { ?>
		<td><font color="#85000D" face="Verdana,Arial" size="1"><b>
	<?php } ?>
        <?php              
                             echo "$headline";
                             //print "<a name=\"$headline\">";
        ?>
                </td>
		<td><font face="Verdana,Arial" size="1">&nbsp;</td>
		<td><font face="Verdana,Arial" size="1">&nbsp;</td>
		<td><font face="Verdana,Arial" size="1">&nbsp;</td>
		<td><font face="Verdana,Arial" size="1">&nbsp;</td>
		<td><font face="Verdana,Arial" size="1">&nbsp;</td>
		</tr>
	<?php		}

	# Reset category
	$current_category = "";
	while (list($key, $article) = each($articles)) :
?>

	<?php	if ($article->kategori != $current_category) :
					 $current_category = $article->kategori;
	?>

		<?php if ($countheader == 1) { ?>
		<tr>
		<td colspan="<?php if ($tested == "yes"): ?>6<?php else: ?>5<?php endif; ?>" height="2"><img border="0" src="/pic/dotF9F9F7.jpg" width="600" height="2"></td>
		</tr>
		<?php $countheader = 0; ?>
		<?php } ?>

		<tr>
		<td colspan="<?php if ($tested == "yes"): ?>6<?php else: ?>5<?php endif; ?>"><font color="#2B2B2B" face="Verdana,Arial" size="1"><b>
        <?php
               if ($article->kategori <> ".") {
                        if ($headline) {
                             print "$headline";
                             print "<a name=\"$headline\">";
                        }
                        else {
               	             print $article->kategori;
                             print "<a name=\"$article->kategori\">";
                        }
               }
               else  {
                        print "&nbsp;&nbsp";
               }
        ?>
                </td>
		</tr>
	<tr>
	    <td colspan="<?php if ($tested == "yes"): ?>6<?php else: ?>5<?php endif; ?>" height="2"><img border="0" src="/pic/galleryline.jpg" width="600" height="2"></td>
  	</tr>
	<?php	$countheader = 1; ?>
	<?php		endif; ?>

	<tr onmouseover="HiLite(this);">
	<td><font color="#2B2B2B" size="1" face="Verdana, Arial">
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
       $style->StyleNewProduct($article->artnr,2,$article->demo);
	if ($article->kommentar) {
                print " ";
		$style->StyleText($article->kommentar);
	}

      	if ($article->campaignLink != "" && $article->campaignLink != "1" ) {
      		$showkampanjtexten = 2;
		include ("kampanj/$article->campaignLink");
      		$showkampanjtexten = 0;
	
	} elseif ($article->campaignLink == "1" ) {
	
		print "&nbsp;";
		$bask->getDiscountInfo($article->artnr,1,$article->tillverkar_id,$article->kategori_id); // artnr, showstyle (1=text, 2=bild), tillverkare, kategori
	
	} else {
	
	print "&nbsp;";
	
	}
	
        print "</A>";

	if ($tested == "yes") {
	
	echo "</td><td>";
	
		if ($article->betyg == 1) {
		echo " <font color='#85000D' size='1' face='Verdana, Arial'><b>Toppklass</b></font>";
		}
		if ($article->betyg == 20) {
		echo " <font color='#85000D' size='1' face='Verdana, Arial'><b>Bra köp</b></font>";
		}
		if ($article->betyg == 40) {
		echo " <font color='#85000D' size='1' face='Verdana, Arial'><b>Testad</b></font>";
		}
	}
	
        ?>
	</font></td>
        <td align="center"><font color="#2B2B2B" size="1" face="Verdana, Arial">
  <?php
	if ($article->filterd != NULL) {
			if ($article->filterd == "99") {
				print "--";  
			} else {
				print "$article->filterd";  
			}
    } else {
         	print "&nbsp;&nbsp;";
	}
   ?>
        </font></td>

         <td align="center"><font color="#2B2B2B" size="1" face="Verdana, Arial">
  <?php
         if ($article->motljsk != NULL)
             print "$article->motljsk"; 
         else
             print "&nbsp;&nbsp;";  
  ?>
         </font></td>

	<?php if ($_SESSION['RememberMoms'] == 1) { ?>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<?php  if ($article->utpris>0)
		{
			printf ("%10.0f kr", $article->utpris);
			//printf("%10.0f kr", $utpris*1.25);
		}
		else
		{
			// print "&nbsp;&nbsp;\n";
			print "ej&nbsp;prissatt";
		}
	?>
	</font></td>
	
	<?php } else { ?>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<?php if (!eregi("pac$", $article->artnr)) {
		datebetweenInprisMarkera($article->datum_inpris, $article->kategori_id);
		}
	?>

	<?php
		if ($article->utpris>0)
		{
			echo number_format(($article->utpris + $article->utpris * $article->momssats), 0, ',', ' ') ." kr</b></a></font>";
		}
		else
		{
			// print "&nbsp;&nbsp;";
			print "ej&nbsp;prissatt";
		}
		print "</font></td>\n";
	?>

	<?php } ?>

	<td width="35" align="center"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php
	 if ($article->no_buy == 0) {
	 	if ($article->lagersaldo > 0) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/01.gif\" border=0>";
			print "</A>";
			}
		elseif ($article->lagersaldo <= 0 && $article->bestallningsgrans > 0) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan är beställd\" SRC=\"/pic/06.gif\" border=0>";
			print "</A>";
			}
		elseif ($article->lagersaldo <= 0 && $article->bestallningsgrans == 0) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Beställningsvara\" SRC=\"/pic/09.gif\" border=0>";
			print "</A>";
			}
	}
	else
	{
		print "&nbsp;&nbsp;";
	}
	?>
	</font></td>
	</tr>
<?php endwhile; ?>

<?php $headline = "" ?>

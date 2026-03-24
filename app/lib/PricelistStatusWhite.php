<?php
include ("CDateCheck.php");
require_once("CBasket.php");
require_once("CStyleCode.php");
$style = new CStyleCode();
$bask = new CBasket();

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
	while (list($key, $article) = each($articles)) :
    if ( !(ereg("^beg", $article->artnr)) || (ereg("^beg", $article->artnr) && ($article->lagersaldo > '0') ) ):
?>

	<?php	if ($article->kategori != $current_category) :
					 $current_category = $article->kategori;
	?>

		<?php if ($countheader == 1) { ?>
		<tr>
		<td colspan="4" height="2"><img border="0" src="/pic/dotF9F9F7.jpg" width="600" height="2"></td>
		</tr>
		<?php $countheader = 0; ?>
		<?php } ?>

		<tr>
		<td colspan="4"><font color="#2B2B2B" face="Verdana,Arial" size="1"><b>
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
	    <td colspan="4" height="2"><img border="0" src="/pic/galleryline_white.jpg" width="600" height="2"></td>
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
		print "<A HREF=\"info_mobil.php?article=".$article->artnr."\">";
	}
         if ($article->tillverkare <> ".") {
           print $article->tillverkare;
           print " ";
       }
       print $article->beskrivning;
	if ($article->kommentar) {
                print " ";
		// print $article->kommentar;
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

	?>
	</font></td>

	<?php If ($_SESSION['RememberMoms'] == 1) { ?>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<?php  if ($article->utpris>0)
		{
			printf ("%10.0f kr", $article->utpris);
			//printf("%10.0f kr", $utpris*1.25);
		}
		else
		{
			print "&nbsp;&nbsp;\n";
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
			print "&nbsp;&nbsp;";
		}
		print "</font></td>\n";
	?>

	<?php } ?>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php
	 if ($article->no_buy == 0) {
	 	if (eregi("pac$", $article->artnr)) {
			print "<A onmouseover=\"return escape('<b>Paketlösning</b><br>Denna artikel innehåller flera artiklar. Klicka på produkten eller infoknappen för aktuell lagerstatus.')\" HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG SRC=\"/pic/parcel.gif\" border=0>";
			print "</A>";
			}
		elseif ($article->lagersaldo > 0) {
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
	<td align="left"><font color="#2B2B2B" size=1 face="Verdana, Arial">
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
			print "<A HREF=\"info_mobil.php?article=".$article->artnr."\">";
			print "<IMG SRC=\"/pic/02.gif\" border=0 >";
			}
		print "</A>";
	?>

	</font></td>
	</tr>
<?php endif; ?>
<?php endwhile; ?>

<?php $headline = "" ?>

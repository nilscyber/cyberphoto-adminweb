<?php
require_once("CBasket.php");
$bask = new CBasket();
require_once("CMobile.php");
$mobile = new CMobile();

# PHP Pricelist script
# author	Nils Kohlström / Thomas Lövgren
# version	2006-09-01


	# Read articles using criteria
        # if $criteria is not defined, all Articles will be selected therefore:
        if ($critiera = "")
         { $criteria = "WHERE Artiklar.kategori_id=0"; }

     	 $articles = readArticlesWithCriteria($criteria);

	# Reset category
	$current_category = "";
	while (list($key, $article) = each($articles)) :
?>


	<?php	if ($article->kategori != $current_category) :
					 $current_category = $article->kategori;
	?>

		<?php if ($countheader == 1) { ?>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<?php $countheader = 0; ?>
		<?php } ?>

	<tr>
		<td colspan="4" align="left"><font color="#2B2B2B"  face="Verdana" size="2"><b>

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
			</b>
                </td>
	</tr>

	<tr>
	    <td colspan="4" height="2" align="left"><img border="0" src="/pic/galleryline_mobil.jpg" width="600" height="2"></td>
  	</tr>

	<?php	$countkolumn = 1; ?>
	<?php	$countheader = 1; ?>

	<?php	endif; ?>


	<td width="25%" height="210">
	<table border="0" cellpadding="5" cellspacing="0" style="border-left: 1px solid #EEEEEE; border-right: 1px solid #CFCFCF; border-top: 1px solid #EEEEEE; border-bottom: 1px solid #CFCFCF" width="100%" height="210">
	  <tr>
	    <td colspan="2" align="center">
	    
	<b><font color="#2B2B2B" size="1" face="Verdana, Arial">
        
	<?php
	if ($article->link) {
		print "<A HREF=\"".$article->link."\">";
	}
	else {
		// print "<A HREF=\"info_mobil.php?article=".$article->artnr."\">";
		print "<A HREF=\"info.php?article=".$article->artnr."\">";
	}
	?>

        <?php if ($article->bild != "") { ?>
        	<img border ="0" name="bilden" src="<?php echo "/thumbs/large/bilder/".$article->bild; ?>"></a>
        	<?php } else { ?>
        	<img border ="0" name="bilden" src="/pic/noimage.jpg"></a>
        	<?php } ?>
                <?php if ($article->betyg == 20) { ?>
        	<img border ="0" name="bilden" src="/pic/stampbr_gallery.gif">
        	<?php } elseif ($article->betyg == 1) { ?>
        	<img border ="0" name="bilden" src="/pic/stampt_gallery.gif">
        	<?php } ?>
        	<br><br>
        
	<?php
	if ($article->link) {
		print "<A HREF=\"".$article->link."\">";
	}
	else {
		// print "<A HREF=\"info_mobil.php?article=".$article->artnr."\">";
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
  	
	</font>
	    
	    </td>
	  </tr>
	  <tr>

	<?php If ($_SESSION['RememberMoms'] == 1) { ?>
	    
	    <td align="left">
	    
	<font color="#85000D" size=2 face="Verdana, Arial"><b>
	&nbsp;
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
	</b>
	    </td>

	<?php } else { ?>

	    <td align="left">
	    
	<font color="#85000D" size=2 face="Verdana, Arial"><b>
	&nbsp;
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
	</b>
	    </td>

	<?php } ?>

	    <td align="right">
	    
	<font color="#2B2B2B" size=1 face="Verdana, Arial">
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

	
	</font>
	    
	    </td>
	  </tr>
	  <?php if ($article->kategori_id == 336 && $article->utpris > 0) { ?>
	  <tr>
	    <td colspan="2" align="center">
	    <a href="info.php?article=<?php echo $article->artnr; ?>#bookmark">
	    <font color="#000000" size=1 face="Verdana, Arial"><b>Från <font color="#85000D" size=1 face="Verdana, Arial"><?php echo $mobile->displayLowestPrice($article->artnr,1); ?> kr</font> med abonnemang</font>
	    </a>
	    </td>
	  </tr>
	  <?php } ?>
	</table>


	</font></td>
	
	<?php
	
	if ($countkolumn == 4) {
		print"<tr>";
		$countkolumn = 0;
		}
	$countkolumn++
	
	?>
		
	
	<?php endwhile; ?>

	<?php if ($countkolumn == 2) { ?>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
	<?php } elseif ($countkolumn == 3) { ?>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
	<?php } elseif ($countkolumn == 4) { ?>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
	<?php } ?>
	
	</tr>

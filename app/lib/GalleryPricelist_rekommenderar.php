<?php
require_once("CBasket.php");
$bask = new CBasket();

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
	$countkolumn = 1;
	while (list($key, $article) = each($articles)) :
?>
	<?php if ($countkolumn == 1) { ?>
	<tr>
		<td colspan="4" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	</tr>
	<tr>
		<td colspan="4"><font color="#2B2B2B"  face="Arial" size="2"><b>Vi rekommenderar</b></td>
	</tr>
	<tr>
	<?php } ?>

	<td width="25%" height="200" valign="top">
	<table border="0" cellpadding="5" cellspacing="0" style="border-left: 1px solid #EEEEEE; border-right: 1px solid #CFCFCF; border-top: 1px solid #EEEEEE; border-bottom: 1px solid #CFCFCF" width="100%" height="200">
	  <tr>
	    <td colspan="2" align="center">
	    
	<b><font color="#2B2B2B" size="1" face="Verdana, Arial">
        
	<?php
	if ($article->link) {
		print "<A HREF=\"".$article->link."\">";
	}
	else {
		print "<A HREF=\"info.php?article=".$article->artnr."\">";
	}
	?>

        <?php if ($article->bild != "") { ?>
        	<img border ="0" name="bilden" src="<?php echo "/thumbs/large/bilder/".$article->bild; ?>"></a>
        	<?php } else { ?>
        	<img border ="0" name="bilden" src="/pic/noimage.jpg"></a>
        	<?php } ?>
        	<br><br>
        
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
	
	</font>
	    
	    </td>
	  </tr>
	</table>


	</font></td>
	
	<?php
	
	$countkolumn++
	
	?>
		
	
	<?php endwhile; ?>

	<?php If ($countkolumn == 2) { ?>
		<td width="25%" height="170">&nbsp;</td>
		<td width="25%" height="170">&nbsp;</td>
		<td width="25%" height="170">&nbsp;</td>
		<?php }
		elseif ($countkolumn == 3) { ?>
		<td width="25%" height="170">&nbsp;</td>
		<td width="25%" height="170">&nbsp;</td>
		<?php }
		elseif ($countkolumn == 4) { ?>
		<td width="25%" height="170">&nbsp;</td>
		<?php } ?>
	
	</tr>
	<tr>
		<td colspan="4" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	</tr>
	<tr>
		<td colspan="4" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	</tr>

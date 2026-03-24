<?php
require_once("CCheckIpNumber.php");
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

	# Reset category
	$current_category = "";
	$current_betyg = "";
	if ($headline) {
	?>
		<tr>
		<td colspan="4" align="left"><font color="#2B2B2B" face="Verdana,Arial" size="2"><b>
        <?php              
                             echo "$headline";
                             //print "<a name=\"$headline\">";
        ?>
                </td>
		</tr>
	<tr>
	    <td colspan="4" height="2" align="left"><img border="0" src="/pic/galleryline.jpg" width="600" height="2"></td>
  	</tr>

	<?php if ($sortera != "testres") {
		$countkolumn = 1;
		}
	?>

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
	<?php $countkolumn = 1; ?>

		<tr>
		<td colspan="4"><font color="#85000D" face="Verdana,Arial" size="2"><b>
		<?php echo $row->betygBeskrivning; ?>

                </td>
		</tr>
	<?php	}
		}
	?>		


	<td width="25%" height="210">
	<table border="0" cellpadding="5" cellspacing="0" style="border-left: 1px solid #EEEEEE; border-right: 1px solid #CFCFCF; border-top: 1px solid #EEEEEE; border-bottom: 1px solid #CFCFCF" width="100%" height="100%">
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

	<?php if ($sortera == "testres" || $testresult == "yes") { ?>
                <?php if ($article->betyg == 20) { ?>
        	<img border ="0" name="bilden" src="/pic/stampbr_gallery.gif">
        	<?php } elseif ($article->betyg == 1) { ?>
        	<img border ="0" name="bilden" src="/pic/stampt_gallery.gif">
        	<?php } ?>
        	<br><br>
       	<?php } ?>

	<?php if ($sortera == "zoom") { ?>
	<font color="#85000D" size=1 face="Verdana, Arial"><br>
		<?php  if ($article->zoom_digikam != NULL)	{
			if ($article->zoom_digikam == 999)
				print "Beror på objektiv";
			else
				printf ("Optisk zoom: %.0fx", $article->zoom_digikam);
				}
		else
		{
			print "uppgift saknas";
		} ?>
	</font><br><br>
	<?php }
		
	?>	

	<?php if ($sortera == "ccd") { ?>
	<font color="#85000D" size=1 face="Verdana, Arial"><br>
		<?php  if ($article->ccd != NULL) {	
			$ccd = number_format ($article->ccd, 0, ",", " ");
			print "Upplösning: $ccd";
			}
			else
			{
			print "uppgift saknas";
			} ?>
	</font><br><br>
	<?php } ?>	


       	<?php If ($sortera != "testres" && $sortera !="zoom" && $sortera !="ccd") { ?><br><br><?php } ?>
        
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
    if ($article->beskrivningKort) {
		print $article->beskrivningKort;
	} else {
		print $article->beskrivning;
	}
       $style->StyleNewProduct($article->artnr,2,$article->demo);
        if ($article->kommentar) {
                // print " ";
                // $style->StyleText($article->kommentar);
        }

      	if ($article->campaignLink != "" && $article->campaignLink != "1" ) {
      		$showkampanjtexten = 2;
		include ("kampanj/$article->campaignLink");
      		$showkampanjtexten = 0;
	
	} elseif ($article->campaignLink == "1" ) {

	print "&nbsp;";
	$bask->getDiscountInfo($article->artnr,3,$article->tillverkar_id,$article->kategori_id); // artnr, showstyle (1=text, 2=bild), tillverkare, kategori

	} else {

	print "&nbsp;";

	}

        print "</A>";

        ?>
	</font>
	    
	    </td>
	  </tr>
	  <tr>
	    
	<?php if ($_SESSION['RememberMoms'] == 1) { ?>
	    
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
			// print "&nbsp;&nbsp;\n";
			print "ej&nbsp;prissatt";
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
			// print "&nbsp;&nbsp;";
			print "ej&nbsp;prissatt";
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
		/*
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
		*/
	?>
	
	</font>
	    
	    </td>
	  </tr>
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

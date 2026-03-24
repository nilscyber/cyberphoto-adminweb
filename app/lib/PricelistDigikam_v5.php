<?php
include ("CDateCheck.php");
require_once("CCheckIpNumber.php");
require_once("CStyleCode.php");
require_once("CBasket.php");
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

	if (count($articles) < 1) {
		$headline = "Ingen produkt matchar urvalet."; // fast med röd text,, 
	}

	# Reset category
	$current_category = "";
	$current_betyg = "";
	if ($headline) {
	?>
		<tr>
		<td colspan="<?php if ($testresult == "yes"): ?>4<?php else: ?>3<?php endif; ?>"><font color="#2B2B2B" face="Verdana,Arial" size="1"><b>
        <?php              
                             echo "$headline";
                             //print "<a name=\"$headline\">";
        ?>
                </td>
		</tr>
	<tr>
	    <td colspan="<?php if ($testresult == "yes"): ?>4<?php else: ?>3<?php endif; ?>" height="2"><img border="0" src="/pic/galleryline.jpg"></td>
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
		<td colspan="5"><font color="#85000D" face="Verdana,Arial" size="1"><b>
		<?php echo $row->betygBeskrivning; ?>

                </td>
		</tr>
	<?php	}
		}
	?>		

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

	?>
	</font>
	<?php

	if ($testresult == "yes") {
	
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
	</td>
	
	<?php if ($sortera == "ccd"): ?>
	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php  if ($article->ccd != NULL)
		{	
			$ccd = number_format($article->ccd / 1000000, 1);
			print $ccd;
			
					}
		else
		{
			print "uppgift saknas";
		}
	?>	
		
	</font></td>
	<?php elseif ($sortera == "zoom"): ?>
	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">
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

	<?php If ($_SESSION['RememberMoms'] == 1) { ?>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<?php  if ($article->utpris>0)
		{
			printf ("%10.0f kr", $article->utpris);
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

		// print "&nbsp;&nbsp;";
	}
	?>
	</font></td>
	</tr>
<?php //endif; 
?>
<?php endwhile; ?>

<?php $headline = "" ?>
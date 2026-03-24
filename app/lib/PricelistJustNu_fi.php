<?php
include ("CDateCheck.php");
require_once("CBasket.php");
$bask = new CBasket();

# PHP Pricelist script
# author	Nils Kohlström
# version	2000-11-20


	# Read articles using criteria
        # if $criteria is not defined, all Articles will be selected therefore:
        if ($critiera = "")
         { $criteria = "WHERE Artiklar.kategori_id=0"; }

     	 $articles = readArticlesWithCriteria($criteria);

	while (list($key, $article) = each($articles)) :
    if ( !(ereg("^beg", $article->artnr)) || (ereg("^beg", $article->artnr) && ($article->lagersaldo > '0') ) ):

	if ($article->link2_fi != "") {
		$link = "<a href=\"".$article->link2_fi . "?fi=1" . "\">";		
	} else {
		$link = "<a href=\"info_fi.php?article=".$article->artnr."\">";
	}
	
	if ($fi && !$sv && $article->beskrivning_fi != "")
		$beskrivning = $article->beskrivning_fi;
	else
		$beskrivning = $article->beskrivning;
	if ($article->tillverkare != ".")
		$beskrivning = $article->tillverkare . " " . $beskrivning;
?>

	<table border="0" cellpadding="5" cellspacing="0" style="border-left: 1px solid #EEEEEE; border-right: 1px solid #CFCFCF; border-top: 1px solid #EEEEEE; border-bottom: 1px solid #CFCFCF" width="100%">
	<tr>
	<td align="left" width="150" height="150"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	
	<?php echo $link; ?>

        <?php if ($article->bild != "") { ?>
        	<img border ="0" name="bilden" src="<?php echo "/thumbs/xxlarge/bilder/".$article->bild; ?>"></a>
        	<?php } else { ?>
        	<img border ="0" name="bilden" src="/pic/noimage.jpg"></a>
        	<?php } ?>

	</font></td>

	<?php // här splittar jag
	?>

	<td width="450" height="150"><font color="#2B2B2B" size="1" face="Verdana"><b>
	<?php echo $link; ?>
	
	<?php
       print $beskrivning;
       print "<br>";
       ?>
       <font color="#2B2B2B" size="1" face="Verdana"><b>
       <?php
	if ($article->kommentar_fi) {
		print $article->kommentar_fi;
	}

	?>
	<br>
	<?php
      	if ($article->campaignLink_fi != "" && $article->campaignLink_fi != "1" ) {
      		$showkampanjtexten = 2;
		include ("kampanj/$article->campaignLink_fi");
      		$showkampanjtexten = 0;
	
	} elseif ($article->campaignLink_fi == "1" ) {

	// print "&nbsp;";
	$bask->getDiscountInfo($article->artnr,1,$article->tillverkar_id,$article->kategori_id); // artnr, showstyle (1=text, 2=bild), tillverkare, kategori

	} else {

	print "&nbsp;";

	}

	if ($article->kortinfo_fi != "") {
        	echo "<br><br>";
		echo $article->kortinfo_fi;
	}
        // print "<br>";
        // print "Lisätietoja.....";

	print "</a>";

	?>
	</font></td>

	<td>
	<?php
	if (eregi("REA!", $article->kommentar)) {
    		echo "<img border=\"0\" src=\"/pic/rea_mini_fi.gif\">";
        } else {
        	echo "&nbsp;";
        }
        ?>
	</td>
	<td>
	<?php
       	if ($article->betyg == 20) {
       		echo "<img border=\"0\" src=\"/pic/stampbr_gallery_fi.gif\">";
       	} elseif ($article->betyg == 1) {
       		echo "<img border =\"0\" src=\"/pic/stampt_gallery_fi.gif\">";
        } else {
        	echo "&nbsp;";
        }
        ?>
	</td>
	<td>
	<?php
		print "<a style=\"text-decoration: none\" href=\"info_fi.php?article=".$article->artnr."\">";
	?>
	<img border="0" src="/pic/read_more_fi.jpg"></a>
	</td>

	<td align="center" width="100" height="150"><font color="#000000" size=2 face="Verdana, Arial"><b>

	<?php
		if ($article->utpris_fi>0)
		{
			printf ("%10.0f EUR", ($article->utpris_fi + $article->utpris_fi * $article->momssats));
		}
		else
		{
			print "&nbsp;&nbsp;";
		}
		print "</font></td>\n";
	?>

	<td align="center" width="100" height="150"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php
	 if ($article->no_buy == 0) {
	 	if ($article->lagersaldo > 0) {
	 	
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/11_fi.gif\" border=0>";
			print "</A>";
		
		} elseif ($article->lagersaldo == 0 && $article->bestallningsgrans > 0) {
		
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/66_fi.gif\" border=0>";
			print "</A>";
		
		} else {
		
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/99_fi.gif\" border=0>";
			print "</A>";
		
		}
	}
	else
	{
			print $link;
			print "<IMG SRC=\"/pic/14.gif\" border=0 >";
			print "</a>";
	}
	?>
	</font></td>
	</tr>
	</table><br>
<?php endif; ?>
<?php endwhile; ?>

<?php $headline = "" ?>

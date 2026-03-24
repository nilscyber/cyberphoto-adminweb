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
?>

	<table border="0" cellpadding="5" cellspacing="0" style="border-left: 1px solid #EEEEEE; border-right: 1px solid #CFCFCF; border-top: 1px solid #EEEEEE; border-bottom: 1px solid #CFCFCF" width="100%">
	<tr>
	<td align="left" width="150" height="150"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php
	if ($article->link) {
		print "<A HREF=\"".$article->link."\">";
	}
	else {
		if ($linctype == 1) {
		print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
		} elseif ($linctype == 2) {
		print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
		} else {
		print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
		}
	}
	?>

        <?php if ($article->bild != "") { ?>
        	<img border ="0" name="bilden" src="<?php echo "/thumbs/xxlarge/bilder/".$article->bild; ?>"></a>
        	<?php } else { ?>
        	<img border ="0" name="bilden" src="/pic/noimage.jpg"></a>
        	<?php } ?>

	</font></td>

	<?php // här splittar jag
	?>

	<td width="450" height="150"><font color="#2B2B2B" size="1" face="Verdana"><b>
	<?php
	if ($article->link) {
		print "<A style=\"text-decoration: none\" HREF=\"".$article->link."\">";
	}
	else {
		if ($linctype == 1) {
		print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
		} elseif ($linctype == 2) {
		print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
		} else {
		print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
		}
	}
         if ($article->tillverkare <> ".") {
           print $article->tillverkare;
           print " ";
       }
       print $article->beskrivning;
       print "<br>";

       ?>
       
       <?php
	if ($article->kommentar) {
                print " ";
    		if (eregi("REA!", $article->kommentar)) {
    			$article->kommentar = str_replace("REA!", "<b><font color='#85000D'>REA!</font></b>", $article->kommentar);
    		}
		print $article->kommentar;
	}
	?>
	<br>
	<?php

      	if ($article->campaignLink != "" && $article->campaignLink != "1" ) {
      		$showkampanjtexten = 2;
		include ("kampanj/$article->campaignLink");
      		$showkampanjtexten = 0;
	
	} elseif ($article->campaignLink == "1" ) {

	// print "&nbsp;";
	$bask->getDiscountInfo($article->artnr,1,$article->tillverkar_id,$article->kategori_id); // artnr, showstyle (1=text, 2=bild), tillverkare, kategori

	} else {

	print "&nbsp;";

	}

	if ($article->kortinfo != "") {
        	echo "<br><br>";
		echo $article->kortinfo;
	}
        // print "<br>";
        // print "Läs mer.....";

       	print "</a>";

	?>
	</font></td>
	
	<td>
	<?php
	if (eregi("REA!", $article->kommentar)) {
    		echo "<img border=\"0\" src=\"/pic/rea_mini.gif\">";
        } else {
        	echo "&nbsp;";
        }
        ?>
	</td>
	<td>
	<?php
       	if ($article->betyg == 20) {
       		echo "<img border=\"0\" src=\"/pic/stampbr_gallery.gif\">";
       	} elseif ($article->betyg == 1) {
       		echo "<img border =\"0\" src=\"/pic/stampt_gallery.gif\">";
        } else {
        	echo "&nbsp;";
        }
        ?>
	</td>
	<td>
	<?php
	if ($linctype == 1) {
		print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
	} elseif ($linctype == 2) {
		print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
	} else {
		print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
	}
	?>
	<img border="0" src="/pic/read_more.jpg"></a>
	</td>


	<td align="center" width="100" height="150"><font color="#000000" size=2 face="Verdana, Arial"><b>

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

	<td align="center" width="100" height="150"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php
	 if ($article->no_buy == 0) {
	 
	 	if ($article->lagersaldo > 0 || $article->kategori_id == 1000010 || $article->kategori_id == 1000011 || $article->kategori_id == 1000012) {
	 	
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/11.gif\" border=0>";
			print "</A>";
		
		} elseif ($article->lagersaldo <= 0 && $article->bestallningsgrans > 0) {
		
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/66.gif\" border=0>";
			print "</A>";
		
		} else {
		
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/99.gif\" border=0>";
			print "</A>";
		
		}
	} else {
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
				if ($linctype == 1) {
				print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
				} elseif ($linctype == 2) {
				print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
				} else {
				print "<A style=\"text-decoration: none\" HREF=\"info.php?article=".$article->artnr."\">";
				}
			print "<IMG SRC=\"/pic/14.gif\" border=0 >";
			}
		print "</A>";
	}
	?>
	</font></td>
	</tr>
	</table><br>
<?php endif; ?>
<?php endwhile; ?>

<?php $headline = "" ?>

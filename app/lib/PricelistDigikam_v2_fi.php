<?php
require_once("CBasket.php");
$bask = new CBasket();

# PHP Pricelist script
# author	Nils Kohlström
# version	2000-11-20
$fi = true;
$sv = false;

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
		<td colspan="5"><font color="#2B2B2B" face="Verdana,Arial" size="1"><b>
        <?php              
                             echo "$headline";
                             //print "<a name=\"$headline\">";
        ?>
                </td>
		</tr>
	<tr>
	    <td colspan="4" height="2"><img border="0" src="/pic/galleryline.jpg" width="600" height="2"></td>
  	</tr>
	<?php		}
	
	while (list($key, $article) = each($articles)) :
    if ( !(ereg("^beg", $article->artnr)) || (ereg("^beg", $article->artnr) && ($article->lagersaldo > '0') ) ):

	if ($article->link2_fi != "") {
		$link = "<A HREF=\"".$article->link2_fi . "?fi=1" . "\">";		
	} else {
		$link = "<A HREF=\"info_fi.php?article=".$article->artnr."\">";
	}
	if ($fi && !$sv && $article->beskrivning_fi != "")
		$beskrivning = $article->beskrivning_fi;
	else
		$beskrivning = $article->beskrivning;
	if ($article->tillverkare != ".")
		$beskrivning = $article->tillverkare . " " . $beskrivning;
		
	if ($sv)
		$beskrivning = $beskrivning . " " . $article->kommentar;
	else
		$beskrivning = $beskrivning . " " . $article->kommentar_fi;    
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
		<td colspan="5"><font color="#003E87" face="Verdana,Arial" size="1"><b>
		<?php echo $row->betygBeskrivning; ?>

                </td>
		</tr>
	<?php	}
		}
	?>		

	<tr onmouseover="HiLite(this);">
	<td><font color="#2B2B2B" size="1" face="Verdana, Arial">
	<%
	echo $link . $beskrivning;
       
      	if ($article->campaignLink_fi != "" && $article->campaignLink_fi != "1" ) {
      		$showkampanjtexten = 2;
		include ("kampanj/$article->campaignLink_fi");
      		$showkampanjtexten = 0;
	
	} elseif ($article->campaignLink_fi == "1" ) {

	print "&nbsp;";
	$bask->getDiscountInfo($article->artnr,1,$article->tillverkar_id,$article->kategori_id); // artnr, showstyle (1=text, 2=bild), tillverkare, kategori

	} else {

	print "&nbsp;";

	}
	
	echo "</a>";

	%>
	</font></td>
	
	<?php if ($sortera == "ccd"): ?>
	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">
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
	
	<% If ($_SESSION['RememberMoms'] == 1) { %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<%  if ($article->utpris_fi>0)
		{
			printf ("%10.0f EUR", $article->utpris_fi);
			//printf("%10.0f kr", $utpris*1.25);
		}
		else
		{
			print "&nbsp;&nbsp;\n";
		}
	%>
	</font></td>

	<% } else { %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<%
		if ($article->utpris_fi>0)
		{
			printf ("%10.0f EUR", ($article->utpris_fi + $article->utpris_fi * $article->momssats));
		}
		else
		{
			print "&nbsp;&nbsp;";
		}
		print "</font></td>\n";
	%>

	<% } %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%
	 
	 if ($article->no_buy == 0) {
	 	if (eregi("pac$", $article->artnr)) {
			print "<A onmouseover=\"return escape('<b>Pakettiratkaisu</b><br>Tähän tuotteeseen sisältyy useampia artikkeleita. Varastotilanteen näkee klikkaamalla itse tuotetta tai infopainiketta.')\" HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG SRC=\"/pic/parcel.gif\" border=0>";
			print "</A>";
			}
	 	elseif ($article->lagersaldo_fi > 0 || $article->lagersaldo > 0) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/01_fi.gif\" border=0>";
			print "</A>";
			// print "$article->lagersaldo";
			}
		elseif (($article->lagersaldo_fi == 0 || $article->lagersaldo == 0) && ($article->bestallningsgrans_fi > 0 || $article->bestallningsgrans > 0)) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan är beställd\" SRC=\"/pic/06_fi.gif\" border=0>";
			print "</A>";
			}
		elseif (($article->lagersaldo_fi == 0 || $article->lagersaldo == 0) && ($article->bestallningsgrans_fi == 0 || $article->bestallningsgrans == 0)) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Beställningsvara\" SRC=\"/pic/09_fi.gif\" border=0>";
			print "</A>";
			}
	}
	else
	{
		print "&nbsp;&nbsp;";
	}
	%>
	</font></td>
	</tr>
<% endif; %>
<% endwhile; %>

<% $headline = "" %>

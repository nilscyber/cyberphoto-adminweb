<?php
require_once("CBasket.php");
$bask = new CBasket();

# PHP Pricelist script
# author	Nils Kohlström / Thomas Lövgren
# version	2006-09-01
$fi = true;
$sv = false;

	# Read articles using criteria
        # if $criteria is not defined, all Articles will be selected therefore:
        if ($critiera = "")
         { $criteria = "WHERE Artiklar.kategori_id=0"; }

     	 $articles = readArticlesWithCriteria($criteria);

	# Reset category
	$current_category = "";
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

	<%	if ($article->kategori_fi != $current_category) :
					 $current_category = $article->kategori_fi;
	%>

		<% if ($countheader == 1) { %>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<% $countheader = 0; %>
		<% } %>

	<tr>
		<td colspan="4"><font color="#2B2B2B"  face="Verdana,Arial"  size="2"><b>

        <%
               if ($article->kategori_fi <> ".") {
                        if ($headline) {
                             print "$headline";
                             print "<a name=\"$headline\">";
                        }
                        else {
               	             print $article->kategori_fi;
                             print "<a name=\"$article->kategori_fi\">";
                        }
               }
               else  {
                        print "&nbsp;&nbsp";
               }
        %>
				</b>
	                </td>
		</tr>

	<tr>
	    <td colspan="4" height="2"><img border="0" src="/pic/galleryline.jpg" width="600" height="2"></td>
  	</tr>

	<%	$countkolumn = 1; %>
	<%	$countheader = 1; %>

	<%	endif; %>


	<td width="25%" height="210">
	<table border="0" cellpadding="5" cellspacing="0" style="border-left: 1px solid #EEEEEE; border-right: 1px solid #CFCFCF; border-top: 1px solid #EEEEEE; border-bottom: 1px solid #CFCFCF" width="100%" height="100%">
	  <tr>
	    <td colspan="2" align="center">
	    
	<b><font color="#2B2B2B" size="1" face="Verdana, Arial">

	<%
	if ($article->link) {
		print "<A HREF=\"".$article->link."\">";
	}
	else {
		print "<A HREF=\"info_fi.php?article=".$article->artnr."\">";
	}
	%>

        <% if ($article->bild != "") { %>
        	<img border ="0" name="bilden" src="<% echo "/thumbs/large/bilder/".$article->bild; %>"></a>
        	<% } else { %>
        	<img border ="0" name="bilden" src="/pic/noimage_fi.jpg"></a>
        	<% } %>
        	<br><br>
        
	<%
	echo $link . $beskrivning . "</a>";
       
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

	%>
	</font>
	    
	    </td>
	  </tr>
	  <tr>
	    
	<% if ($_SESSION['RememberMoms'] == 1) { %>

	    <td align="left">
	    
	<font color="#85000D" size=2 face="Verdana, Arial"><b>
	&nbsp;
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
	</b>
	    
	    </td>

	<% } else { %>

	    <td align="left">
	    
	<font color="#85000D" size=2 face="Verdana, Arial"><b>
	&nbsp;
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
	</b>
	    </td>

	<% } %>

	    <td align="right">
	    
	<font color="#2B2B2B" size=1 face="Verdana, Arial">
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
			}
		elseif (($article->lagersaldo_fi == 0 || $article->lagersaldo <= 0) && ($article->bestallningsgrans_fi > 0 || $article->bestallningsgrans > 0)) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan är beställd\" SRC=\"/pic/06_fi.gif\" border=0>";
			print "</A>";
			}
		elseif (($article->lagersaldo_fi == 0 || $article->lagersaldo <= 0) && ($article->bestallningsgrans_fi == 0 || $article->bestallningsgrans == 0)) {
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

	<%
	print $link;
	print "<IMG SRC=\"/pic/02.gif\" border=0 >";			
	print "</A>";
	%>
	
	</font>
	    
	    </td>
	  </tr>
	</table>


	</font></td>
	
	<%
	
	if ($countkolumn == 4) {
		print"<tr>";
		$countkolumn = 0;
		}
	$countkolumn++
	
	%>
		
	<% endif; %>

	<% endwhile; %>

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

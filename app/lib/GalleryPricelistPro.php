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
	while (list($key, $article) = each($articles)) :
?>


	<%	if ($article->kategori != $current_category) :
					 $current_category = $article->kategori;
	%>

		<% if ($countheader == 1) { %>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<% $countheader = 0; %>
		<% } %>

	<tr>
		<td colspan="4"><font color="#2B2B2B"  face="Arial" size="3"><b>

        <%
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
        %>
			</b>
                </td>
	</tr>

	<tr>
	    <td colspan="4" height="2"><img border="0" src="/pic/galleryline_pro.jpg" width="600" height="2"></td>
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
		print "<A HREF=\"info_pro.php?article=".$article->artnr."\">";
	}
	%>

        <% if ($article->bild != "") { %>
        	<img border ="0" name="bilden" src="<% echo "/thumbs/large/bilder/".$article->bild; %>"></a>
        	<% } else { %>
        	<img border ="0" name="bilden" src="/pic/noimage.jpg"></a>
        	<% } %>
        	<br><br>
        
	<%
	if ($article->link) {
		print "<A HREF=\"".$article->link."\">";
	}
	else {
		print "<A HREF=\"info_pro.php?article=".$article->artnr."\">";
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

        %>
	</font>
	    
	    </td>
	  </tr>
	  <tr>
	    
	<% If ($_SESSION['RememberMoms'] == 1) { %>
	    
	    <td align="left">
	    
	<font color="#85000D" size=2 face="Verdana, Arial"><b>
	&nbsp;
	<%  if ($article->utpris>0)
		{
			printf ("%10.0f kr", $article->utpris);
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
		if ($article->utpris>0)
		{
			printf ("%10.0f kr", $article->utpris + $article->utpris * $article->momssats);
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
			print "<a href=\"javascript:modifyItems('$article->artnr')\">";
			print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/buy_pro.gif\" border=0>";
			print "</a>";
	}
	else
	{
		print "&nbsp;&nbsp;";
	}
	%>

	<%
	 	if (eregi("pac$", $article->artnr)) {
			print "<a onmouseover=\"return escape('<b>Paketlösning</b><br>Denna artikel innehåller flera artiklar. Klicka på produkten för lagerstatus.')\"><img style=\"cursor:help\" src=\"/pic/parcel.gif\" border=0></a>";
			}
		elseif ($article->lagersaldo > 0) {
			print "<a onmouseover=\"return escape('<b>Finns i lager</b><br>Varan finns i vårt lager och är klart för omedelbar utleverans.')\"><img style=\"cursor:help\" src=\"/pic/status01.gif\" border=0></a>";
			}
		elseif ($article->lagersaldo <= 0 && $article->bestallningsgrans > 0) {
			print "<a onmouseover=\"return escape('<b>Varan är beställd</b><br>Varan är tillfälligt slut. Mer detaljerad information om leveransbesked hittar ni på produktsidan.')\"><img style=\"cursor:help\" src=\"/pic/status06.gif\" border=0></a>";
			}
		elseif ($article->lagersaldo <= 0 && $article->bestallningsgrans == 0) {
			print "<a onmouseover=\"return escape('<b>Beställningsvara</b><br>Denna vara tar vi endast hem efter beställning.')\"><IMG style=\"cursor:help\" src=\"/pic/status09.gif\" border=0></a>";
			}
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
		
	
	<% endwhile; %>

	<% If ($countkolumn == 2) { %>
		<td width="25%" height="210">&nbsp;</td>
		<td width="25%" height="210">&nbsp;</td>
		<td width="25%" height="210">&nbsp;</td>
		<% }
		elseif ($countkolumn == 3) { %>
		<td width="25%" height="210">&nbsp;</td>
		<td width="25%" height="210">&nbsp;</td>
		<% }
		elseif ($countkolumn == 4) { %>
		<td width="25%" height="210">&nbsp;</td>
		<% } %>
	
	</tr>

<?php
include ("CDateCheck.php");

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

	<%	if ($article->kategori != $current_category) :
					 $current_category = $article->kategori;
	%>

		<% if ($countheader == 1) { %>
		<tr>
		<td colspan="<% if ($_SESSION['RememberPicture'] == 1) { %>6<% } else { %>5<% } %>" height="2"><img border="0" src="/pic/dotF9F9F7.jpg" width="600" height="2"></td>
		</tr>
		<% $countheader = 0; %>
		<% } %>

		<tr>
		<td colspan="<% if ($_SESSION['RememberPicture'] == 1) { %>6<% } else { %>5<% } %>"><strong><font color="#2B2B2B" face="Arial,Verdana" size="3"><b>
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
                </td>
		</tr>
	<tr>
	    <td colspan="<% if ($_SESSION['RememberPicture'] == 1) { %>6<% } else { %>5<% } %>" height="2"><img border="0" src="/pic/galleryline_pro.jpg" width="600" height="2"></td>
  	</tr>
	<%	$countheader = 1; %>
	<%		endif; %>

	<tr onmouseover="HiLite(this);">
	
	<% if ($_SESSION['RememberPicture'] == 1) { %>
	<td align="center" bgcolor="#FFFFFF">
	<%
	if ($article->link) {
		print "<A HREF=\"".$article->link."\">";
	}
	else {
		print "<A HREF=\"info.php?article=".$article->artnr."\">";
	}
	%>
	<img src="/thumbs/small/bilder/<% echo $article->bild ; %>">
	
	<%
        print "</a>";
        %>
	
	</td>
	<% } %>
	
	<td><font color="#2B2B2B" size="1" face="Verdana, Arial">
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

       print "</A>";
       
	if ($article->campaignLink != "") {
	$showkampanjtexten = 2;
	include ("kampanj/$article->campaignLink");
	$showkampanjtexten = 0;
       	}

	%>
	<% if ($_SERVER['REMOTE_ADDR'] == "81.8.240.115") { %>
	<% // datebetweenInpris($article->datum_inpris);
	%>
	<% // echo $article->kategori_id. " "; %>
	<% } %>
	</font></td>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<% if ($article->utpris > 2500) { %>

	<a href="javascript:winPopupCenter(400, 650, '../faq/foretagshyra.php');" onMouseOver="return escape('<% include ("explanation/foretagshyra.php"); %>')" style="text-decoration: none">
	
	<% echo number_format(getRentValue(1,$article->utpris), 0, ',', ' ') ." kr/mån"; %>

	<img border="0" src="/pic/highlight_hyra.gif"></a>

	<% // echo getRentValue(1,$article->utpris) ." kr/mån"; %>
	
	<% } else { %>
	
		&nbsp;&nbsp;
	<% } %>
	
	</font></td>

	<% If ($_SESSION['RememberMoms'] == 1) { %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<%  if ($article->utpris>0)
		{
			echo number_format($article->utpris, 0, ',', ' ') ." kr</b></a>";
		}
		else
		{
			print "&nbsp;&nbsp;\n";
		}
	%>
	</font></td>
	
	<% } else { %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<% if (!eregi("pac$", $article->artnr)) {
		datebetweenInprisMarkera($article->datum_inpris);
		}
	%>

	<%
		if ($article->utpris>0)
		{
			echo number_format(($article->utpris + $article->utpris * $article->momssats), 0, ',', ' ') ." kr</b></a>";
		}
		else
		{
			print "&nbsp;&nbsp;";
		}
		print "</font></td>\n";
	%>
	
	<% } %>

	<td align="center"><font color="#2B2B2B" size=1 face="Verdana, Arial">
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
	</font></td>
	<td align="center">
	
	<%
	 	if (eregi("pac$", $article->artnr)) {
			print "<a onmouseover=\"return escape('<b>Paketlösning</b><br>Denna artikel innehåller flera artiklar. Klicka på produkten för lagerstatus.')\"><img style=\"cursor:help\" src=\"/pic/parcel.gif\" border=0></a>";
			}
		elseif ($article->lagersaldo > 0) {
			print "<a onmouseover=\"return escape('<b>Finns i lager</b><br>Varan finns i vårt lager och är klart för omedelbar utleverans.')\"><img style=\"cursor:help\" src=\"/pic/status01.gif\" border=0></a>";
			}
		elseif ($article->lagersaldo == 0 && $article->bestallningsgrans > 0) {
			print "<a onmouseover=\"return escape('<b>Varan är beställd</b><br>Varan är tillfälligt slut. Mer detaljerad information om leveransbesked hittar ni på produktsidan.')\"><img style=\"cursor:help\" src=\"/pic/status06.gif\" border=0></a>";
			}
		elseif ($article->lagersaldo == 0 && $article->bestallningsgrans == 0) {
			print "<a onmouseover=\"return escape('<b>Beställningsvara</b><br>Denna vara tar vi endast hem efter beställning.')\"><IMG style=\"cursor:help\" src=\"/pic/status09.gif\" border=0></a>";
			}
	%>
	
	</td>
	</tr>
<% endif; %>
<% endwhile; %>

<% $headline = "" %>

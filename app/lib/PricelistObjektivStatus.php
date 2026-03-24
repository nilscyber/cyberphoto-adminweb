<?php

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
		<td colspan="6" height="2"><img border="0" src="/pic/dotF9F9F7.jpg" width="600" height="2"></td>
		</tr>
		<% $countheader = 0; %>
		<% } %>

		<tr>
		<td colspan="6"><font color="#2B2B2B"  face="Verdana,Arial"><small><small><b>
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
	    <td colspan="6" height="2"><img border="0" src="/pic/galleryline.jpg" width="600" height="2"></td>
  	</tr>
	<%	$countheader = 1; %>
	<%		endif; %>

	<tr onmouseover="HiLite(this);">
	<td><font color="#2B2B2B" size="1" face="Verdana, Arial">
	<%
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

        print "</A>";

        %>
	</font></td>
        <td><font color="#2B2B2B" size="1" face="Verdana, Arial">
  <%
	if ($article->filterd != NULL) 
	 	print "$article->filterd";  
        else
         	print "&nbsp;&nbsp;"  
   %>
        </font></td>

         <td><font color="#2B2B2B" size="1" face="Verdana, Arial">
  <%
         if ($article->motljsk != NULL)
             print "$article->motljsk"; 
         else
             print "&nbsp;&nbsp;";  
  %>
         </font></td>

	<% If ($_SESSION['RememberMoms'] == 1) { %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

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
	</font></td>
	
	<% } else { %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

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
	
	<% } %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%
	 if ($article->no_buy == 0) {
	 	if ($article->lagersaldo > 0) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/01.gif\" border=0>";
			print "</A>";
			}
		elseif ($article->lagersaldo == 0 && $article->bestallningsgrans > 0) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan är beställd\" SRC=\"/pic/06.gif\" border=0>";
			print "</A>";
			}
		elseif ($article->lagersaldo == 0 && $article->bestallningsgrans == 0) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Beställningsvara\" SRC=\"/pic/09.gif\" border=0>";
			print "</A>";
			}
	}
	else
	{
		print "&nbsp;&nbsp;";
	}
	%>
	</font></td>
	<td align="left"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%
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
	%>

	</font></td>
	</tr>
<% endwhile; %>

<% $headline = "" %>

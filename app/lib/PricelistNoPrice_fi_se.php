<?php

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
?>

	<%	if ($article->kategori != $current_category) :
					 $current_category = $article->kategori;
	%>
		<% if ($countheader == 1) { %>
		<tr>
		<td colspan="4" height="2"><img border="0" src="/pic/dotF9F9F7.jpg" width="600" height="2"></td>
		</tr>
		<% $countheader = 0; %>
		<% } %>

		<tr>
		<td colspan="2"><font color="#2B2B2B"  face="Verdana,Arial"><small><small><b>
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
	    <td colspan="4" height="2"><img border="0" src="/pic/galleryline_fi.jpg" width="600" height="2"></td>
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
		print "<A HREF=\"info_fi_se.php?article=".$article->artnr."\">";
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

        //if ($article->link) {
                        print "</A>";
        //}

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
			print "<A HREF=\"info_fi_se.php?article=".$article->artnr."\">";
			print "<IMG SRC=\"/pic/02.gif\" border=0 >";
			}
		print "</A>";
	%>

	</font></td>
	</tr>
<% endwhile; %>

<% $headline = "" %>

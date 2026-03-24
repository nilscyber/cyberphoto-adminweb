	<tr>
<?php

$countkolumn = 1;

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

	<td width="33%" height="210">
	<table border="0" cellpadding="5" cellspacing="0" style="border-left: 1px solid #DBDBDB; border-right: 1px solid #999999; border-top: 1px solid #DBDBDB; border-bottom: 1px solid #999999" width="100%" height="100%">
	  <tr>
	    <td colspan="2" align="center">
	    
	<b><font color="#2B2B2B" size="1" face="Verdana, Arial">
        
        <% if ($article->bild != "") { %><% print "<a href=\"info.php?article=".$article->artnr."\">"; %><img border ="0" name="bilden" src="<% echo "/thumbs/large/bilder/".$article->bild; %>"></a><% } %><br><br>
        
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
	</font>
	    
	    </td>
	  </tr>
	  <tr>
	    
	    <td>
	    
	<font color="#2B2B2B" size=1 face="Verdana, Arial">
	Pris utan moms:&nbsp;

	<%  if ($article->utpris>0)
		{
			printf ("%10.0f kr", $article->utpris);
		}
		else
		{
			print "&nbsp;&nbsp;\n";
		}
	%>
	</font>
	
	<br>
	
	<font color="#85000D" size=1 face="Verdana, Arial"><b>
	Pris med moms:&nbsp;&nbsp;

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


	    <td align="center">
	    
	<font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%
	 if ($article->no_buy != "1")
	 {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG SRC=\"/pic/01.gif\" border=0>";
			print "</A>";
		}
		else
		{
			print "&nbsp;&nbsp;";
		}
	%>
	<br>
	<%
		if ($article->link <> "")
			{
			 print "<A href=\"".$article->link."\">";
			 if (eregi (".jpg$", $article->link) || eregi (".gif$", $article->link) || eregi (".jpeg$", $article->link))
			 	print "<IMG SRC=\"/pic/bild.gif\" border=0 >" ;
			 else
			 	print "<IMG SRC=\"/pic/02_ss.gif\" border=0 >" ;

			}

		 else
			{
			print "<A HREF=\"info.php?article=".$article->artnr."\">";
			print "<IMG SRC=\"/pic/02_ss.gif\" border=0 >";
			}
		print "</A>";
	%>
	
	</font>
	    
	    </td>
	  </tr>
	</table>


	</font></td>
	
	<%
	
	if ($countkolumn == 3) {
		print"<tr>";
		$countkolumn = 0;
		}
	$countkolumn++
	
	%>
		
	
	<% endwhile; %>
	
	</tr>

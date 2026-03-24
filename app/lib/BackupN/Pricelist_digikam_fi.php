<?php

# PHP Pricelist script
# author	Nils Kohlström
# version	2000-11-20

	// Get currency for EURO
	include ("CConnect.php");
	$select_valuta = "SELECT kurs FROM Valuta WHERE valutakod = 'EUR'";
	$res_valuta = mysqli_query($select_valuta);
	$valuta = mysqli_fetch_object($res_valuta);
	
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
		<tr>
		<td bgcolor="#ECECE6" colspan="<% if ($sortera == "zoom" || $sortera == "ccd") echo 7; else echo 6;%>"><font color="#2B2B2B"  face="Verdana,Arial"><small><small><b>
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
	<%		endif; %>

	<tr>
	<td bgcolor="#ECECE6"><font color="#2B2B2B" size="1" face="Verdana, Arial">
	<%

	if ($article->link_fi) {
		print "<A HREF=\"".$article->link_fi . "?fi=yes" . "\">";
	}
	elseif ($article->link) {
		print "<A HREF=\"".$article->link . "?fi=yes" . "\">";
	}
	else {
		print "<A HREF=\"info.php?article=".$article->artnr."&fi=yes\">";
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

	%>
	</font></td>
	
	<% if ($sortera == "ccd"): %>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%  if ($article->ccd != NULL)
		{	
			$ccd = number_format ($article->ccd, 0, ",", " ");
			print $ccd;
			
					}
		else
		{
			print "uppgift saknas";
		}
	%>	
		
	</font></td>
	<% elseif ($sortera == "zoom"): %>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%  if ($article->zoom != NULL)
		{
			if ($article->zoom == "999")
				print "Beror på objektiv";
			elseif ($article->zoom == "0")
				print "fast optik";
			else
				printf ("%.0fx", $article->zoom);
					}
		else
		{
			print "uppgift saknas";
		}
	%>	
		
	</font></td>
	<% endif; %>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<%  if ($article->utpris>0)
		{
			printf ("%10.0f kr", $article->utpris);
					}
		else
		{
			print "&nbsp;&nbsp;\n";
		}
	%>
	</font></td>

	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<%
		if ($article->utpris>0)
		{
			printf ("%10.0f kr", $article->utpris*1.25);
		}
		else
		{
			print "&nbsp;&nbsp;";
		}
		print "</font></td>\n";
	%>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<%
		if ($article->utpris>0)
		{
			printf ("%10.0f EUR", $article->utpris*1.25/$valuta->kurs);
		}
		else
		{
			print "&nbsp;&nbsp;";
		}
		print "</font></td>\n";
	%>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%
	 
	 if ($article->no_buy == 0)
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
	</font></td>
	<td align="left" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%

		if ($article->link_fi <> "")
			{
			 print "<A href=\"".$article->link_fi . "?fi=yes" . "\">";
			 print "<IMG SRC=\"/pic/02.gif\" border=0 >" ;
			}		
				
		elseif ($article->link <> "")
			{
			 print "<A href=\"".$article->link . "?fi=yes" . "\">";
			 if (eregi (".jpg$", $article->link) || eregi (".gif$", $article->link) || eregi (".jpeg$", $article->link))
			 	print "<IMG SRC=\"/pic/bild.gif\" border=0 >" ;
			 else
			 	print "<IMG SRC=\"/pic/02.gif\" border=0 >" ;
			}
		 else
			{
			print "<A HREF=\"info.php?article=".$article->artnr."&fi=yes\">";
			print "<IMG SRC=\"/pic/02.gif\" border=0 >";
			}
		print "</A>";
	%>

	</font></td>
	</tr>
<% endif; %>
<% endwhile; %>

<% $headline = "" %>

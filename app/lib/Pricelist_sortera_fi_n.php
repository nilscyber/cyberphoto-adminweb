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
    if ( !(ereg("^beg", $article->artnr)) || (ereg("^beg", $article->artnr) && ($article->lagersaldo > '0') ) ):
	if ($article->link2_fi != "")
		$link = "<A HREF=\"".$article->link2_fi."\">";
	elseif ($sv)
		$link = "<A HREF=\"info_fi_se.php?article=".$article->artnr."\">";
	else
		$link = "<A HREF=\"info_fi.php?article=".$article->artnr."\">";
		
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
	<?php
	echo $link;
	echo $beskrivning . "</A>";
	?>
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
			printf ("%10.0f kr", $article->utpris_fi);
					}
		else
		{
			print "&nbsp;&nbsp;\n";
		}
	%>
	</font></td>

	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<%
		if ($article->utpris_fi>0)
		{
			printf ("%10.0f kr", $article->utpris_fi + $article->utpris_fi * $article->momssats);
		}
		else
		{
			print "&nbsp;&nbsp;";
		}
		print "</font></td>\n";
	%>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">

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
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%
	 
	 if ($article->no_buy == 0)
	 {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG SRC=\"/pic/01_fi.gif\" border=0>";
			print "</A>";
		}
		else
		{
			print "&nbsp;&nbsp;";
		}
	%>
	</font></td>
	<td align="left" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<?php
	print $link;
	print "<IMG SRC=\"/pic/02.gif\" border=0 >";			
	print "</A>";
	?>

	</font></td>
	</tr>
<% endif; %>
<% endwhile; %>

<% $headline = "" %>

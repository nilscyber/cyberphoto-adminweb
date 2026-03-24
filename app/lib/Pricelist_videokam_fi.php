<?php

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
	
	while (list($key, $article) = each($articles)) :
    //if ( !(ereg("^beg", $article->artnr)) || (ereg("^beg", $article->artnr) && ($article->lagersaldo > '0') ) ): // överflödig iom att "utgången" används

	if ($link2_fi != "")
		$link = "<A HREF=\"".$article->link2_fi . "?fi=1" . "\">";
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
	<%	if ($article->kategori_fi != $current_category) :
					 $current_category = $article->kategori_fi;
	%>
		<tr>
		<td bgcolor="#ECECE6" colspan="<% if ($sortera == "zoom" || $sortera == "ccd") echo 6; else echo 5;%>"><font color="#2B2B2B" face="Verdana,Arial" size="1"><b>
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
                </td>
		</tr>
	<%		endif; %>
	
	<% 	
	if ($sortera == "testres") {
			if ($article->betyg != $current_betyg) {
				$current_betyg = $article->betyg;
				
				$sel = "SELECT betygBeskrivning FROM Betyg WHERE betygId = " . $article->betyg;
				
				$res = mysqli_query($sel);
				$row = mysqli_fetch_object($res);
			
				%>

		<tr>
		<td bgcolor="#ECECE6" colspan="5"><font color="#85000D" face="Verdana,Arial" size="1"><b>
		<% echo $row->betygBeskrivning; %>

                </td>
		</tr>
	<%	}
		}
	%>		

	<tr>
	<td bgcolor="#ECECE6"><font color="#2B2B2B" size="1" face="Verdana, Arial">
	<?php
	echo $link;
	echo $beskrivning . "</A>";
	?>
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
	<%  if ($article->zoom_digikam != NULL)
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
		
	%>	
		
	</font></td>
	<% endif; %>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<%  if ($article->utpris_fi>0)
		{
			printf ("%10.0f EUR", $article->utpris_fi);
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
<% //endif; 
%>
<% endwhile; %>

<% $headline = "" %>
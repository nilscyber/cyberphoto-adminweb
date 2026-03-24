<?php

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
	$current_betyg = "";
	$startkom = 1;
	

	while (list($key, $article) = each($articles)) :
    //if ( !(ereg("^beg", $article->artnr)) || (ereg("^beg", $article->artnr) && ($article->lagersaldo > '0') ) ): // överflödig iom att "utgången" används

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


	if ($startkom == 1) {
	print "<div align=\"center\"><center>\n" ;
	print "<table width=\"170\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\" >\n";
	print "<tr>\n";
	print "<td align=\"center\" style=\"background-image: url('/pricelist/aktuellakampanjer.png'); background-repeat: no-repeat\"><b>Ajankohtaiset kampanjat</b></td>\n";
	print "</tr>\n";
	}

?>


	<tr>
	<td height="180">
	<table border="0" cellpadding="3" cellspacing="0" style="border-left: 1px solid #EEEEEE; border-right: 1px solid #CFCFCF; border-top: 1px solid #EEEEEE; border-bottom: 1px solid #CFCFCF" width="100%" height="100%">
	  <tr>
	    <td colspan="2" align="center">
	    
	<b><font color="#2B2B2B" size="1" face="Verdana, Arial">
        
	<%
	echo $link;
	%>

        <% if ($article->bild != "") { %>
        	<img border ="0" name="bilden" src="<% echo "/thumbs/large/bilder/".$article->bild; %>"></a>
        	<% } else { %>
        	<img border ="0" name="bilden" src="/pic/noimage.jpg"></a>
        	<% } %>

	<% if ($sortera == "testres") { %>
                <% if ($article->betyg == 20) { %>
        	<img border ="0" name="bilden" src="/pic/stampbr_gallery.gif">
        	<% } elseif ($article->betyg == 1) { %>
        	<img border ="0" name="bilden" src="/pic/stampt_gallery.gif">
        	<% } %>
        	<br><br>
        	<% } %>

	<% if ($sortera == "zoom") { %>
	<font color="#85000D" size=1 face="Verdana, Arial"><br>
		<%  if ($article->zoom_digikam != NULL)	{
			if ($article->zoom_digikam == 999)
				print "Beror på objektiv";
			else
				printf ("Optisk zoom: %.0fx", $article->zoom_digikam);
				}
		else
		{
			print "uppgift saknas";
		} %>
	</font><br><br>
	<% }
		
	%>	

	<% if ($sortera == "ccd") { %>
	<font color="#85000D" size=1 face="Verdana, Arial"><br>
		<%  if ($article->ccd != NULL) {	
			$ccd = number_format($article->ccd / 1000000, 1);
			print "$ccd Mpix";
			}
			else
			{
			print "uppgift saknas";
			} %>
	</font><br><br>
	<% } %>	


       	<% If ($sortera != "testres" && $sortera !="zoom" && $sortera !="ccd") { %><br><br><% } %>
        
	<%
	echo $link . $beskrivning . "</a>";

        %>
	</font>
	    
	    </td>
	  </tr>
	  <tr>
	    
	<% If ($_SESSION['RememberMoms'] == 1) { %>

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
	</tr>
	<% $startkom++;	%>
	<% endwhile; %>


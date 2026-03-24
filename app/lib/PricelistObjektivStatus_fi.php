<?php

# PHP Pricelist script
# author	Nils Kohlström / Thomas Lövgren
# version	2006-09-01
$fi = true;
$sv = false;

	// include ("CConnect.php");
	// $select_valuta = "SELECT kurs FROM Valuta WHERE valutakod = 'EUR'";
	// $res_valuta = mysqli_query($select_valuta);
	// $valuta = mysqli_fetch_object($res_valuta);


	# Read articles using criteria
        # if $criteria is not defined, all Articles will be selected therefore:
        if ($critiera = "")
         { $criteria = "WHERE Artiklar.kategori_id=0"; }

     	 $articles = readArticlesWithCriteria($criteria);

	# Reset category
	$current_category = "";
	while (list($key, $article) = each($articles)) :
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
		<td colspan="6" height="2"><img border="0" src="/pic/dotF9F9F7.jpg" width="600" height="2"></td>
		</tr>
		<% $countheader = 0; %>
		<% } %>

		<tr>
		<td colspan="6"><font color="#2B2B2B"  face="Verdana,Arial"><small><small><b>
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
	<tr>
	    <td colspan="6" height="2"><img border="0" src="/pic/galleryline.jpg" width="600" height="2"></td>
  	</tr>
	<%	$countheader = 1; %>
	<%		endif; %>

	<tr onmouseover="HiLite(this);">
	<td><font color="#2B2B2B" size="1" face="Verdana, Arial">
	<%
	echo $link . $beskrivning . "</a>";
       

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

	 $article->motljsk = eregi_replace("\(ingår ej\)", "(ei sisälly)", $article->motljsk);
	 $article->motljsk = eregi_replace("\(ingår\)", "(sisältyy)", $article->motljsk);
	 $article->motljsk = eregi_replace("^ingår", "sisältyy", $article->motljsk);
	 $article->motljsk = eregi_replace("nej", "ei", $article->motljsk);
	 $article->motljsk = eregi_replace("fast", "kiinteä", $article->motljsk);
	 $article->motljsk = eregi_replace("utdragbart", "ulosvedettävä", $article->motljsk);
	 $article->motljsk = eregi_replace("inbyggt", "kiinteinen", $article->motljsk);
	 //$article->motljsk = eregi_replace("", "", $article->motljsk);
	 //$article->motljsk = eregi_replace("$Ingår", "(sisältyy)", $article->motljsk);

         if ($article->motljsk != NULL)
             print $article->motljsk;
         else
             print "&nbsp;&nbsp;";
  %>
         </font></td>

	<% If ($_SESSION['RememberMoms'] == 1) { %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

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
	</font></td>

	<% } else { %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">

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

	<% } %>

	<td align="right"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%
	 
	 if ($article->no_buy == 0) {
	 	if ($article->lagersaldo_fi > 0 || $article->lagersaldo > 0) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/01_fi.gif\" border=0>";
			print "</A>";
			}
		elseif (($article->lagersaldo_fi == 0 || $article->lagersaldo == 0) && ($article->bestallningsgrans_fi > 0 || $article->bestallningsgrans > 0)) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan är beställd\" SRC=\"/pic/06_fi.gif\" border=0>";
			print "</A>";
			}
		elseif (($article->lagersaldo_fi == 0 || $article->lagersaldo == 0) && ($article->bestallningsgrans_fi == 0 || $article->bestallningsgrans == 0)) {
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
	</font></td>
	<td align="left"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%
	print $link;
	print "<IMG SRC=\"/pic/02.gif\" border=0 >";			
	print "</A>";
	%>

	</font></td>
	</tr>
<% endwhile; %>

<% $headline = "" %>

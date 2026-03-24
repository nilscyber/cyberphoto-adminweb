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
	if ($headline) {
	?>
		<tr>
		<td colspan="4"><font color="#2B2B2B" face="Verdana,Arial" size="2"><b>
        <?php              
                             echo "$headline";
                             //print "<a name=\"$headline\">";
        ?>
                </td>
		</tr>
	<tr>
	    <td colspan="4" height="2"><img border="0" src="/pic/galleryline.jpg" width="600" height="2"></td>
  	</tr>

	<% if ($sortera != "testres") {
		$countkolumn = 1;
		}
	%>

	<?php		}
	
	while (list($key, $article) = each($articles)) :
    //if ( !(ereg("^beg", $article->artnr)) || (ereg("^beg", $article->artnr) && ($article->lagersaldo > '0') ) ): // överflödig iom att "utgången" används
?>
	<?php
	if ($article->link2_fi != "") {
		$link = "<A HREF=\"".$article->link2_fi . "?fi=1&sv=1" . "\">";		
	} else {
		$link = "<A HREF=\"info_fi_se.php?article=".$article->artnr."\">";
	}	

	$beskrivning = $article->beskrivning;
	if ($article->tillverkare != ".")
		$beskrivning = $article->tillverkare . " " . $beskrivning;
		
	$beskrivning = $beskrivning . " " . $article->kommentar;
	?>


	<?php 	
	if ($sortera == "testres") {
			if ($article->betyg != $current_betyg) {
				$current_betyg = $article->betyg;
				
				$sel = "SELECT betygBeskrivning FROM Betyg WHERE betygId = " . $article->betyg;
				
				$res = mysqli_query($sel);
				$row = mysqli_fetch_object($res);
			
				?>
	<% $countkolumn = 1; %>

		<tr>
		<td colspan="4"><font color="#85000D" face="Verdana,Arial" size="2"><b>
		<?php echo $row->betygBeskrivning; ?>

                </td>
		</tr>
	<?php	}
		}
	?>		


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
		print "<A HREF=\"info_fi_se.php?article=".$article->artnr."\">";
	}
	%>

        <% if ($article->bild != "") { %>
        	<img border ="0" name="bilden" src="<% echo "/thumbs/large/bilder/".$article->bild; %>"></a>
        	<% } else { %>
        	<img border ="0" name="bilden" src="/pic/noimage.jpg"></a>
        	<% } %>

	<% if ($sortera == "testres" || $testresult == "yes") { %>
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
			$ccd = number_format ($article->ccd, 0, ",", " ");
			print "Upplösning: $ccd";
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
			print "<A onmouseover=\"return escape('<b>Paketlösning</b><br>Denna artikel innehåller flera artiklar. Klicka på produkten eller infoknappen för aktuell lagerstatus.')\" HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG SRC=\"/pic/parcel.gif\" border=0>";
			print "</A>";
			}
	 	elseif ($article->lagersaldo_fi > 0 || $article->lagersaldo > 0) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan finns i vårt lager\" SRC=\"/pic/01.gif\" border=0>";
			print "</A>";
			}
		elseif (($article->lagersaldo_fi == 0 || $article->lagersaldo <= 0) && ($article->bestallningsgrans_fi > 0 || $article->bestallningsgrans > 0)) {
			print "<A HREF=\"javascript:modifyItems('$article->artnr')\">";
			print "<IMG ALT=\"Varan är beställd\" SRC=\"/pic/06.gif\" border=0>";
			print "</A>";
			}
		elseif (($article->lagersaldo_fi == 0 || $article->lagersaldo <= 0) && ($article->bestallningsgrans_fi == 0 || $article->bestallningsgrans == 0)) {
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
	
	<%
	
	if ($countkolumn == 4) {
		print"<tr>";
		$countkolumn = 0;
		}
	$countkolumn++
	
	%>
		
	<% endwhile; %>

	<% if ($countkolumn == 2) { %>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
	<% } elseif ($countkolumn == 3) { %>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
	<% } elseif ($countkolumn == 4) { %>
		<td width="25%" height="210"><img border="0" src="/pic/empty.gif"></td>
	<% } %>
	
	</tr>

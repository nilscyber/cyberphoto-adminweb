<?php

# PHP Pricelist script
# author	Nils Kohlström
# version	2000-11-20

        include("CCArticle.php");
$$env, typ

<%

	require("CArticle.php");
	# Read articles using criteria
	$criteria = "WHERE (Artiklar.kategori_id=5 or Artiklar.kategori_id=59) && ".
				"Artiklar.tillverkar_id=14 && ej_med=0 order by Artiklar.kategori_id ".
				"ASC, utpris ASC ";
	$articles = readArticlesWithCriteria($criteria);
	# Reset category
	$current_category = "";
	while (list($key, $article) = each($articles)) :
%>

	<%	if ($article->kategori != $current_category) :
					 $current_category = $article->kategori;
	%>
		<tr>
		<td bgcolor="#ECECE6" colspan="5"><font color="#2B2B2B"  face="Verdana,Arial"><small><small><b>
		<%=$article->kategori%><a name="<%=$article->kategori%>"></td>
		</tr>
	<%		endif; %>

	<tr>
	<td bgcolor="#ECECE6"><font color="#2B2B2B" size="1" face="Verdana, Arial">
	<%
	if ($article->link) {
		print "<A HREF=\"".$article->link."\">";
	}
	print $article->tillverkare." ".$article->beskrivning;
	if ($article->link) {
			print "</A>";
	}
	if ($article->kommentar) {
		print $article->kommentar;
	}
	%>
	</font></td>
	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<%  if ($article->utpris>0)
		{
			printf ("%.0f kr", $article->utpris);
		}
		else
		{
			print "<A HREF=\"email.htm\">Kontakta oss</A>\n";
		}
	%>
	</font></td>

	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">

	<%
		if ($article->utpris>0)
		{
			printf ("%.0f kr", $article->utpris*1.25);
		}
		else
		{
			print "<IMG SRC=\"/10.gif\">";
		}
		print "</font></td>\n";
	%>

	<td align="right" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%
	 if ($article->utpris>0)
	 {
			print "<A HREF=\"javascript:modifyItems('$artnr')\">";
			print "<IMG SRC=\"../pic/01.gif\" border=0>";
			print "</A>";
		}
		else
		{
			print "<IMG SRC=\"/10.gif\">";
		}
	%>
	</font></td>
	<td align="left" bgcolor="#ECECE6"><font color="#2B2B2B" size=1 face="Verdana, Arial">
	<%
		if ($article->link <> "")
			{
			 print "<A href=\"".$article->$link."\">"; 
			 if (eregi (".jpg$", $article->link) || eregi (".gif$", $article->link))
			 print "<IMG SRC=\"../pic/03.gif\" border=0 >" ;
			 else
			 print "<IMG SRC=\"../pic/02.gif\" border=0 >" ;
			}
		 else
			{
			 print "<IMG SRC=\"10.gif\">";
			}
		 if ($article->link <> "")
			{ 
				print "</A>";  
			}
	%> 

	</font></td>
	</tr>
<% endwhile; %>


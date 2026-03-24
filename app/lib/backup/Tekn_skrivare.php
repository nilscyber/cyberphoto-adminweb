<%
include_once ("CTekniska_data4.php");
include_once ("CConnect.php");

$empty = false;

if ($comparepage != 'yes') { 
	$num_of_articles = '1';
	if ($tekniska_data == "") 
		$tekniska_artnr[1] = $artnr; 
	else
		$tekniska_artnr[1] = $tekniska_data;
	
	// det blir något skumt fel, så detta måste göras för sig när det skall
	// vara med i en vanlig sida
	$select = "SELECT * FROM Tekn_skrivare WHERE artnr = '$tekniska_artnr[1]'";
	//echo $select;
	$res = mysqli_query($select);
	if (mysqli_num_rows($res) < 1 AND $infop) {
		$empty = true;
	}
	$row[1] = mysqli_fetch_object($res);

	}
else  {
	for ($i = 1; $i <= $num_of_articles; $i++): 
		if ($i > '5') break; # För säkerhets skull
		$select[$i] = "SELECT * FROM Tekn_skrivare WHERE artnr = '$tekniska_artnr[$i]'";
		$res[$i] = mysqli_query($select[$i]);
		$row[$i] = mysqli_fetch_object($res[$i]);
	endfor;

}

if (!$empty) {

%>

<% if ($comparepage == 'yes'): %>
<tr>
	<td valign="top" bgcolor="#FFFFFF"><font face="Verdana, Arial" size="1"></font></td>
<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial" size="1">
	<% $artnr = $tekniska_artnr[$i]; include ("/home/www/cgi-bin/buyspot_tekn.php"); %>
	</font></td>
<% endfor; %>
</tr>

<% endif; %>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Skrivarteknik </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->skrivarteknik); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Utskriftskvalitet</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->utskriftskvalitet); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Utskriftshastighet</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->utskriftshastighet); %></font></td>
	<% endfor; %>
</tr>
<% if ($tjosan == "hejsan"): %>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Gränssnitt</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->granssnitt); %> Hz</font></td>
	<% endfor; %>
</tr>
<% endif; %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Pappershantering</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->pappershantering); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Vikt papper</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vikt_papper); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Max utskriftsyta</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->max_utskriftsyta); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Mått</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->matt); %></font></td>
	<% endfor; %>
</tr>
<% if ($tjosan == "hejsan"): %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Vikt</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vikt); %>s</font></td>
	<% endfor; %>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljudnivå</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info2($row[$i]->ljudniva); %></font></td>
	<% endfor; %>
</tr>
<% endif; %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Strömförbrukning (cd/m2)</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->stromforbrukning); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bläckpatroner</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blackpatroner); %></font></td>
	<% endfor; %>
</tr>
<% if ($tjosan == "hejsan"): %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Systemkrav</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->systemkrav); %></font></td>
	<% endfor; %>
</tr>
<% endif; %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Rekomenderat systemkrav</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_infoRound($row[$i]->rek_systemkrav); %> kg</font></td>
	<% endfor; %>
</tr>

<% if ($comparepage == 'yes'): %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Kommentar</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info3($row[$i]->kommentar); %></font></td>
	<% endfor; %>
</tr>
<% endif; %>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
	<% if ($comparepage != 'yes'): %>
	Jämför med andra skrivare! Välj ur listan.
	<% else: %>
	Välj en annan skärm ur listan för att jämföra med ovanstående. 
	<% endif; %>
	</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_comp('NULL', 'yes', 'Artiklar.kategori_id = 2', 'skrivare'); %></font></td>
	<% endfor; %>
</tr>
<% } %>
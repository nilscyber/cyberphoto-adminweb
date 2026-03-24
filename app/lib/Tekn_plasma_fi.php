<%
include_once ("CTekniska_data.php");
include_once ("CConnect.php");

$teknTabell = "Tekn_screen";
if ($comparepage != 'yes') {
	$num_of_articles = '1';
	if ($tekniska_data == "")
		$tekniska_artnr[1] = $artnr;
	else
		$tekniska_artnr[1] = $tekniska_data;

	$select = "SELECT artnr_tekniska_data FROM Info_page WHERE artnr = '" . $tekniska_artnr[1] . "'";
	$res = mysqli_query($select);
	$rs = mysqli_fetch_object($res);
	if (mysqli_num_rows($res) < 1 || $rs->artnr_tekniska_data == "" )
		$select2 = "SELECT * FROM " . $teknTabell . " WHERE artnr = '" . $tekniska_artnr[1] . "'";
	else
		$select2 = "SELECT * FROM " . $teknTabell . " WHERE artnr = '" . $rs->artnr_tekniska_data . "'";
	$res = mysqli_query($select2);
	if (mysqli_num_rows($res) < 1 AND $infop)
		$empty = true;

	$row[1] = mysqli_fetch_object($res);
}
else  {
	for ($i = 1; $i <= $num_of_articles; $i++):
		if ($i > '5') break; // För säkerhets skull

		$select2 = "SELECT artnr_tekniska_data FROM Info_page WHERE artnr = '" . $tekniska_artnr[$i] . "'";
		$res2 = mysqli_query($select2);
		$rs = mysqli_fetch_object($res2);
		if (mysqli_num_rows($res2) < 1 || $rs->artnr_tekniska_data == "")
			$select[$i] = "SELECT * FROM " . $teknTabell . " WHERE artnr = '" . $tekniska_artnr[$i] . "'";
		else
			$select[$i] = "SELECT * FROM " . $teknTabell . " WHERE artnr = '" . $rs->artnr_tekniska_data . "'";
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
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Monitortyp </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->monitortyp); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Storlek</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->storlek); %>"</font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Rek upplösning</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->rek_upplosning); %></font></td>
	<% endfor; %>
</tr>
<% if ($tjosan == "hejsan"): %>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Max frekvens</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->max_frekvens); %> Hz</font></td>
	<% endfor; %>
</tr>
<% endif; %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Progressiv scan</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_trueFalse($row[$i]->progressive_scan); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljud</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljud); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Anslutningar</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->anslutningar); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Betraktningsvinkel H°/V°</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->betraktnings_vinkel); %></font></td>
	<% endfor; %>
</tr>
<% if ($tjosan == "hejsan"): %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Responstid</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->responstid); %>s</font></td>
	<% endfor; %>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Färger</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info2($row[$i]->färger); %></font></td>
	<% endfor; %>
</tr>
<% endif; %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljusstyrka (cd/m2)</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljusstyrka); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Kontrast</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->kontrast); %></font></td>
	<% endfor; %>
</tr>
<% if ($tjosan == "hejsan"): %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bildyta</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->bildyta); %></font></td>
	<% endfor; %>
</tr>
<% endif; %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Vikt</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_infoRound($row[$i]->vikt); %> kg</font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Storlek</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->dimension); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Övrigt</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->övrigt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bildförhållande</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->bildförhållande); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Text-TV</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->textTv); %></font></td>
	<% endfor; %>
</tr>
<% if ($tjosan == "hejsan"): %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Pitch</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_infoRound2($row[$i]->pitch); %></font></td>
	<% endfor; %>
</tr>
<% endif; %>

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
	Jämför med andra skärmar! Välj ur listan.
	<% else: %>
	Välj en annan skärm ur listan för att jämföra med ovanstående.
	<% endif; %>
	</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_plasma($tekniska_artnr[$i], 'NULL'); %></font></td>
	<% endfor; %>
</tr>
<% } %>
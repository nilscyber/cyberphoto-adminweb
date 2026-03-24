<%
include_once ("CTekniska_data.php");
include_once ("CConnect.php");
//include_once ("CTekn_screen.php");

if ($comparepage != 'yes') { 
	$num_of_articles = '1';
	if ($tekniska_data == "") 
		$tekniska_artnr[1] = $artnr; 
	else
		$tekniska_artnr[1] = $tekniska_data;
	
	// det blir något skumt fel, så detta måste göras för sig när det skall
	// vara med i en vanlig sida
	$select = "SELECT * FROM Tekn_mp3 WHERE artnr = '$tekniska_artnr[1]'";
	$res = mysqli_query($select);
	if (mysqli_num_rows($res) < 1 AND $infop) {
		$empty = true;
	}
	$row[1] = mysqli_fetch_object($res);

	}
else  {
	for ($i = 1; $i <= $num_of_articles; $i++): 
		if ($i > '5') break; # För säkerhets skull
		$select[$i] = "SELECT * FROM Tekn_mp3 WHERE artnr = '$tekniska_artnr[$i]'";
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
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Spelartyp</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->radio); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Minne</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minne); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Filformat</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->filformat); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Frekvensomfång</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->frekvensomfång); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Signal/brusförhållande</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->signal_brusförhållande); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bitrate</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->bitrate); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Display</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->display); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Strömkälla</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->strömkälla); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Batteritid</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->batteritid); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Anslutningar</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->anslutning); %></font></td>
	<% endfor; %>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Medföljande tillbehör</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->tillbehör); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Mått</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->mått); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Vikt</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vikt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Systemkrav</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->systemkrav); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Övrigt</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->övrigt); %></font></td>
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
	Jämför med andra MP3 spelare! Välj ur listan.
	<% else: %>
	Välj en annan MP3 spelare ur listan för att jämföra med ovanstående. 
	<% endif; %>
	</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_comp($tekniska_artnr[$i], 'NULL', 'Artiklar.kategori_id = 127', 'mp3'); %></font></td>
	<% endfor; %>
</tr>

<% } %>
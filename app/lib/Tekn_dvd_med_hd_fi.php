<%
include_once ("CTekniska_data.php");
include_once ("CConnect.php");

$teknTabell = "Tekn_dvd_med_hd";
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
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">HDD </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->hdd); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Tallennusaika HDD </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->rec_time); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">DVD-R </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_trueFalse($row[$i]->dvd_r); %></font></td>
	<% endfor; %>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">DVD-RW </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_trueFalse($row[$i]->dvd_rw); %></font></td>
	<% endfor; %>
</tr>


<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">CD-R </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_trueFalse($row[$i]->cd_r); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">CD-RW </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_trueFalse($row[$i]->cd_rw); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Äänifomaatti </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljudformat); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Kuvaformaatti </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->bildformat); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Time Slip </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_trueFalse($row[$i]->time_slip); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Showview </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_trueFalse($row[$i]->show_view); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Scart </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->scart); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">S-video in </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->s_video_in); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">S-video out </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->s_video_ut); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">RCA in </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->rca_in); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">RCA out </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->rca_ut); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">HDMI </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_trueFalse($row[$i]->hdmi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Komponentit </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_trueFalse($row[$i]->komponent); %></font></td>
	<% endfor; %>
</tr>


<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">DV in </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->dv_in); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Mitat (LxKxS) </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->storlek); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Paino </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vikt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Muuta </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ovrigt); %></font></td>
	<% endfor; %>
</tr>


	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
	<% if ($comparepage != 'yes'): %>
	Vertaa muihin soittimiin. Valitse listalta
	<% else: %>
	Valitse listalta soitin ja voit vertailla sitä ylläolevaan.
	<% endif; %>
	</font></td>

	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_comp('NULL', 'yes', 'Artiklar.kategori_id = 389', 'Tekn_dvd_med_hd'); %></font></td>

	<% endfor; %>
</tr>
<% } %>
<%
include_once ("CTekniska_data.php");

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
		$select2 = "SELECT * FROM Tekniska_data WHERE artnr = '" . $tekniska_artnr[1] . "'";			
	else 
		$select2 = "SELECT * FROM Tekniska_data WHERE artnr = '" . $rs->artnr_tekniska_data . "'";			
	$res = mysqli_query($select2);
	if (mysqli_num_rows($res) < 1 AND $infop) 
		$empty = true;

	$row[1] = mysqli_fetch_object($res);
}
else  {
	for ($i = 1; $i <= $num_of_articles; $i++): 		
		if ($i > '3') break; // För säkerhets skull
		
		$select2 = "SELECT artnr_tekniska_data FROM Info_page WHERE artnr = '" . $tekniska_artnr[$i] . "'";
		$res2 = mysqli_query($select2);
		$rs = mysqli_fetch_object($res2);
		if (mysqli_num_rows($res2) < 1 || $rs->artnr_tekniska_data == "") 
			$select[$i] = "SELECT * FROM Tekniska_data WHERE artnr = '" . $tekniska_artnr[$i] . "'";
		else
			$select[$i] = "SELECT * FROM Tekniska_data WHERE artnr = '" . $rs->artnr_tekniska_data . "'";		
		$res[$i] = mysqli_query($select[$i]);
		$row[$i] = mysqli_fetch_object($res[$i]);
	endfor;
}
if (!$empty) {

%>

<% if ($comparepage == 'yes'): %>
<tr>
	<td valign="top" bgcolor="#FFFFFF"><font face="Verdana, Arial" size="1"></font></td>
<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial" size="1">
	<% $artnr = $tekniska_artnr[$i]; include ("/home/www/cgi-bin/buyspot_tekn_new.php"); %>
	</font></td>
<% endfor; %>
</tr>

<% endif; %>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bildsensor storlek&nbsp;</font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->storlekccd)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Upplösning bildsensor</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info2($row[$i]->ccd); %> pixlar</font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Upplösning bild (effektiv)</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->upplosning); %></font></td>
	<% endfor; %>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Kompressions-<br>
	standard</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->kompression); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Objektiv</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->objektiv); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljusstyrka optik</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blandare); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Optisk
	  zoom</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->optisk_zoom); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Digital
	  zoom</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->dig_zoom); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Filterdiameter</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->filterdiam); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Fokusering</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->fokusering); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Närgräns</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->nargrans); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Minnestyp</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minnestyp); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Minne som ingår</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->inkl_minneskort); %></font></td>
	<% endfor; %>
</tr>
<!--
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Minneskapacitet</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minneskapacitet); %></font></td>
	<% endfor; %>
</tr>
-->
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Optisk sökare</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->optisk_sokare); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bildskärm</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->bildskarm)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljusmätning</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljusmatning); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Vitbalans</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vitbalans); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljuskänslighet</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljuskanslighet); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Slutartider</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->slutartider); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Exponering</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->exponering); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Exponerings-<br>
	kompensation</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->exponeringskompensation); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Självutlösare</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->sjalvutlosare); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Blixt</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Blixtinställningar
	</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixtinstalln); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Räckvidd blixt</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixt_rackvidd); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Sekvenstagning</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->sekvenstagning)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Film</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->film); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljudinspelning</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljudinspelning); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Anslutningar</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info($row[$i]->anslutningar); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Batteri</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->batteri); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Övrigt</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ovrigt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Mått</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->matt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Vikt</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vikt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Medföljande
	tillbehör</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->medfolj_tillb)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Medföljande
	  programvara</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->medfolj_program)); %></font></td>
	<% endfor; %>
<!--	
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Systemkrav</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->systemkrav)); %></font></td>
	<% endfor; %>
</tr>
-->
<!--
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bildexempel</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_pic($row[$i]->bildexempel); %></font></td>
	<% endfor; %>
</tr>
-->
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
	<% if ($comparepage != 'yes'): %>
	Jämför med andra digitala kameror! Välj
	kamera ur listan.
	<% else: %>
	Välj en annan kamera i ur listan för att jämföra med ovanstående kamera. 
	<% endif; %>
	</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_digikam($tekniska_artnr[$i], 'NULL'); %></font></td>
	<% endfor; %>
</tr>
<% } %>

<%
include_once ("CTekniska_data.php");
include_once ("CConnect.php");
$fi = true;
$sv = false;

$teknTabell = "Videokameror";
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
		if ($i > '3') break; // För säkerhets skull

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
<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial" size="1">
	<% $artnr = $tekniska_artnr[$i]; include ("/home/www/cgi-bin/buyspot_tekn_new_fi.php"); %>
	</font></td>
<% endfor; %>
</tr>

<% endif; %>

<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/system_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Järjestelmä </font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->system)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/ccd_size_fi.php"); %>')"><font face="Verdana, Arial" size="1"> CCD koko</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ccd); %></font></a></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/ccd_brutto_fi.php"); %>')"><font face="Verdana, Arial" size="1"> CCD brutto</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ccdBrutto); %></font></td>
	<% endfor; %>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/ccd_netto_film_fi.php"); %>')"><font face="Verdana, Arial" size="1"> CCD netto filmattaessa</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->cccdNettoFilm); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/ccd_netto_stillbild_fi.php"); %>')"><font face="Verdana, Arial" size="1"> CCD netto valokuvattaessa</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ccdNettoStillbild); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/stillbild_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Kuvausasetus</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->stillbildslage_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/objektiv_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Objektiivit</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->objektiv_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/zoom_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Zoom</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->zoom); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/digzoom_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Digitaalinen Zoom</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->digitalZoom); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/bildstab_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Kuvanvakaaja</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->bildstabilisering_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/min_belys_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Vähimmäisvalaistus</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minstaBelysning_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/man_fokus_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Manuaalinen tarkennusmahdollisuus</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->manuellFokus_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/filterdiameter_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Suodattimen halkaisija</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->filterdiam_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/vitbalans_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Valkobalanssi</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vitbalans_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/digi_effekt_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Digitaaliset tehosteet</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->digitalaEffekter_fi); %></font></a></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/16_9_fi.php"); %>')"><font face="Verdana, Arial" size="1"> 16:9 wide</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->wide_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/exponering_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Valotus</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->exponering_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/motivprogram_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Aiheohjelmat</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->motivprogram_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/sokare_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Etsin</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->sokare_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/skarm_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Näyttö</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->fargLcd); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/blixt_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Salama</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/minneskort_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Muistikortti</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minneskort_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"> Minneskapacitet</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minneskapacitet_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/hogtalare_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Kaiutin</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->hogtalare_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/rca_ut_fi.php"); %>')"><font face="Verdana, Arial" size="1"> RCA A/V ulos</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->RCAut_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/rca_in_fi.php"); %>')"><font face="Verdana, Arial" size="1"> RCA A/V sisään</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->RCAin_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/svideo_ut_fi.php"); %>')"><font face="Verdana, Arial" size="1"> S-video ulos</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->svideoUt_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/svideo_in_fi.php"); %>')"><font face="Verdana, Arial" size="1"> S-video sisään</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->svideoIn_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/dv_ut_fi.php"); %>')"><font face="Verdana, Arial" size="1"> DV ulos</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info($row[$i]->dvUt_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/dv_in_fi.php"); %>')"><font face="Verdana, Arial" size="1"> DV sisään</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->dvIn_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/mic_in_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Mic sisään</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->micIn_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/usb_fi.php"); %>')"><font face="Verdana, Arial" size="1"> USB</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->usb_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/horlursutgang_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Kuulokeliitäntä</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->horlursutgang_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/fjarrkontroll_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Kaukosäädin</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->fjarrkontroll_fi); %></font></a></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/storlek_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Koko</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->storlek); %></font></a></td>
	<% endfor; %>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/vikt_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Paino</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vikt_fi); %></font></a></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/bruks_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Käyttöohje</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->bruksanvisning_fi); %></font></a></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/medtillbehor_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Mukana tulevat tarvikkeet</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->medfoljandeTillbehor_fi); %></font></a></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/medprogram_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Mukana tuleva ohjelma</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->medfoljandeProgramvara_fi); %></font></a></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('<% include("explanation/tek/video/ovrigt_fi.php"); %>')"><font face="Verdana, Arial" size="1"> Muuta</font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->edit_fi); %></font></a></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
	<% if ($comparepage != 'yes'): %>
	Vertaa muihin digitaalisiin kameroihin! Valitse listalta.
	<% else: %>
	Valitse toinen kamera listalta ja voit verrata sitä ylläolevaan kameraan.
	<% endif; %>
	</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '3') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_videokamera($tekniska_artnr[$i], 'NULL'); %></font></td>
	<% endfor; %><% } %>
</tr>
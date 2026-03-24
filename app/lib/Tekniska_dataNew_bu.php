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
		if ($i > '5') break; // För säkerhets skull
		
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
<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial" size="1">
	<% $artnr = $tekniska_artnr[$i]; include ("/home/www/cgi-bin/buyspot_tekn.php"); %>
	</font></td>
<% endfor; %>
</tr>

<% endif; %>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bildsensor storlek&nbsp;</font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Den fysiska storleken på bildsensorn. Anges oftast på diagonalen och i tum.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->storlekccd)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Upplösning bildsensor </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Det är den totala upplösningen på sensorn. Den verkliga upplösningen, dvs den upplösning som går att utnyttja är vad som anges som Upplösning bild eller Effektiv upplösning. Numera anger de flesta tillverkare den verkliga (effektiva) upplösningen på sina kameror.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info2($row[$i]->ccd); %> pixlar</font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Upplösning bild (effektiv) </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Den verkliga upplösningen, dvs den upplösning som går att utnyttja. Numera anger de flesta tillverkare den verkliga (effektiva) upplösningen på sina kameror.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->upplosning); %></font></td>
	<% endfor; %>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Kompressions-<br>
	standard </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger den filtyp som bilderna sparas i. JPG är vanligast. RAW och TIFF är okomprimerade format. DNG är på kommande. Det är Adobes öppna RAW-format.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->kompression); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Objektiv </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges vilken brännvidd objektivet har och ofta även vad objektivet motsvarar på en 35mm kamera.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->objektiv); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljusstyrka optik </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges den maximala ljusstyrkan. På zoomobjektiv varierar oftast ljusstyrkan med zoomomfånget och därför anges ljusstyrkan till t ex 2,8-3,5 och där är den maximala ljusstyrkan 2,8 på vidvinkel och 3,5 på tele. Här anges inte minsta möjliga bländare.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blandare); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Optisk
	  zoom </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Det verkliga zoomomfånget. Helt enkelt det omfång objektivet har.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->optisk_zoom); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Digital
	  zoom </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Detta är egentligen ingen riktig zoom utan endast att kameran beskär i bilden. Ju mer du zoomar desto sämre upplösning.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->dig_zoom); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Filterdiameter </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Den storlek av filtergänga som passar kameran eller angiven filteradapter.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->filterdiam); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Fokusering </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Om kameran har autofokus, manuell fokusering eller båda.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->fokusering); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Närgräns </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Det närmaste avstånd kameran kan ta en skarp bild.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->nargrans); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Minnestyp </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger den typ av minneskort som passar kameran. Dessa hittar du under fliken tillbehör på denna sida.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minnestyp); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Minne som ingår </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger det minne som ingår i standardtillbehören eller är inbyggt i kameran.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->inkl_minneskort); %></font></td>
	<% endfor; %>
</tr>
<!--
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Minneskapacitet</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minneskapacitet); %></font></td>
	<% endfor; %>
</tr>
-->
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Optisk sökare </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges om kameran har en vanlig optisk sökare eller en EVF, dvs en elektronisk sökare, eller ingen alls.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->optisk_sokare); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bildskärm </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger storlek på bildskärmen som sitter på kamerans baksida. Anges oftast i tum och på diagonalen.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->bildskarm)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljusmätning </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger vilka typer av ljusmätning som finns att tillgå. Evaluerande=flerzon=matrix=ESP ljusmätning mäter ljuset i flera mätzoner och har programvara som talar om för kameran hur den ska reagera vid olika kombinationer. Centrumvägd ljusmätning mäter till största delen i motivets mitt och mindre utåt kanterna. Utsnittsmätning mäter endast i motivets centrum. Spotmätning mäter också endast i motivets centrum men betydligt snävare än vad utsnittsmätningen gör.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljusmatning); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Vitbalans </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger vad kameran har för inställningsmöjligheter angående färgåtergivningen i förhållande till motivets färgtemperatur. Ofta finns ett automatiskt läge, några förinställda lägen och ett manuellt läge där du kan låsa vitbalansen mot något vitt. Bilder som tas i RAW-format kan justeras i efterhand i ett bildbehandlingsprogram.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vitbalans); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljuskänslighet </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anges idag nästan uteslutande i ISO. ISO står för International Organization for Standardization. Tidigare angavs filmkänslighet i ASA och/eller DIN. Till exempel 100ASA motsvarar 21DIN och ska nu egentligen anges ISO 100/21° men på digitalkameror så anges endast ISO 100 som motsvarande ljuskänslighet. På film ger högre ISO-tal kornigare bilder. På en digitalkamera så fungerar det likadant fast det blir inte kornigare utan brusigare bilder, ungefär samma sak.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljuskanslighet); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Slutartider </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger vilka tider kamerans slutare kan arbeta inom. Det vill säga den tid kamerans bildsensor/film får ljus. Vanligt är från ca 1s upp till 1/2000s.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->slutartider); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Exponering </font>
	<img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges de olika exponeringslägena. Programautomatik där kameran ställer både tid och bländare. Tidsautomatik där du ställer bländaren och kameran ställer tiden. Bländarautomatik där du ställer tiden och kameran väljer bländare. Manuellt där du ställer både tid och bländare själv.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->exponering); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Exponerings-<br>
	kompensation </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('En funktion där du kan påverka exponeringen utan att behöva köra manuellt. Vanligast är att du kan över eller underexponera 2 bländarsteg.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->exponeringskompensation); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Självutlösare </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Kameran kan fördröja exponeringen oftast 10-12s efter avtryck.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->sjalvutlosare); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Blixt </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges om kameran har inbyggd blixt, och/eller om den har möjlighet till extern blixt.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Blixtinställningar 
	</font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges de inställningar som finns att ställa in på den inbyggda blixten om kameran har en sådan.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixtinstalln); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Räckvidd blixt </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges hur långt bort du kan fotografera med den inbyggda blixten om kameran har en sådan.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixt_rackvidd); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Sekvenstagning </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Kamerans möjlighet att ta flera bilder i en följd. Anges ofta hur många bilder per sekund (b/s alt fps) ibland även hur många bilder i följd.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->sekvenstagning)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Film </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Om kamerans möjligheter att spela in film. Oftast anges upplösning, bilder per sekund och hur långa sekvenser som kan spelas in.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->film); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Ljudinspelning </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger om kameran har inbyggd mikrofon.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljudinspelning); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Anslutningar </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges kameran olika anslutningar. Om anslutningar sitter på en medföljande docka så anges detta.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info($row[$i]->anslutningar); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Batteri </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges vad kameran använder för typ av batteri.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->batteri); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Övrigt </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ovrigt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Mått </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Kamerans mått som de uppges av tillverkaren.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->matt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Vikt </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Kamerans vikt som den uppges av tillverkaren. Tyvärr uppger de ibland med batterier och ibland utan.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vikt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Medföljande
	tillbehör </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('De tillbehör som tillverkaren uppger ska följa med.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->medfolj_tillb)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Medföljande
	  programvara </font><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Den programvara som tillverkaren uppger ska följa med.')"><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->medfolj_program)); %></font></td>
	<% endfor; %>
<!--	
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Systemkrav</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->systemkrav)); %></font></td>
	<% endfor; %>
</tr>
-->
<!--
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bildexempel</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
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
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_digikam($tekniska_artnr[$i], 'NULL'); %></font></td>
	<% endfor; %>
</tr>
<% } %>

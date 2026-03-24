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
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('DKuvakennon koko ilmoitetaan yleensä tuumissa vaakasuoraan mitattaessa.')"><font face="Verdana, Arial" size="1"> 
Kuvakennon koko&nbsp;.</font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->storlekccd)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Kyseessä on kuvakennon kokonaiserottelukyky. Todellinen erottelukyky, joka voidaan käyttää hyödyksi ilmoitetaan kuvanerottelukykynä tai tehollisena erottelukykynä. Nykyisin suurin osa valmistajista ilmoittaa kameroidensa todellisen (tehollisen) erottelukyvyn.')"><font face="Verdana, Arial" size="1"> Kuvakennon erottelukyky </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info2($row[$i]->ccd); %> 
pikseleitä</font>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Todellinen erottelukyky, eli se minkä voi käyttää hyödykseen. Kun haluat tietää megapixeleiden määrän kerrot ensin annetun pixelimäärän toisella (ilmoitetulla), niin saat esille tehollisen pixelimäärän. Nykyisin useimmat valmistajat ilmoittavat kameroidensa todellisen (tehollisen) erottelukyvyn.')"><font face="Verdana, Arial" size="1"> Kuvien erottelukyky (tehollinen) </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->upplosning); %></font></td>
	<% endfor; %>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Ilmoittaa kuvien tallennusmuodon. JPG on yleisin. RAW ja TIFF ovat pakkaamattomia. DNG on tulossa.Adoben avoin RAW-muoto.')"><font face="Verdana, Arial" size="1"> 
Tiedostomuoto </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->kompression); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Tässä ilmoitetaan objektiivin polttoväli ja mitä objektiivi vastaa 35mm kamerassa.')"><font face="Verdana, Arial" size="1"> Objektiivi </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->objektiv); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Tässä ilmoitetaan maksimi valonvoimakkuus. Zoomobjektiiveissa valovoimakkuus vaihtelee polttovälialueella siksi valovoimakkuus ilmoitetaan esim.2,8-3,5 jossa maksimi valovomakkuus on 2,8 laajakulmassa ja tele 3,5. Tässä ei ilmoiteta pienintä mahdollista aukkoa.')"><font face="Verdana, Arial" size="1"> Optiikan valovoimaisuus </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blandare); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Todellinen polttovälialue. Yksinkertaisesti objektiivin kattama alue.')"><font face="Verdana, Arial" size="1"> Optinen
	  zoom </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->optisk_zoom); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Tämä ei ole todellinen zoom, vaan tässä kamera rajoittaa kuvaa. Mitä enemmän zoomaat sen huonompi on kuvan tarkkuus.')"><font face="Verdana, Arial" size="1"> Digitaalinen
	  zoom </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->dig_zoom); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Kameraan sopivan suodattimen tai suodatinsovitteen koko.')"><font face="Verdana, Arial" size="1"> Suodattimen halkaisija </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->filterdiam); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Onko kamerassa on auto-/ käsitarkennus tai molemmat.')"><font face="Verdana, Arial" size="1"> Fokusointi</font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->fokusering); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Lähin etäisyys jolloin kamera vielä kuvaa terävästi.')"><font face="Verdana, Arial" size="1"> Lähietäisyys </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->nargrans); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('AIlmoittaa kameraan sopivan muistikortin. Löytyvät kohdasta tarvikkeet.')"><font face="Verdana, Arial" size="1"> Muisti </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minnestyp); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Ilmoittaa kameran vakiovarustukseen kuuluvan tai kamerassa olevan sisäisen muistin.')"><font face="Verdana, Arial" size="1"> Mukana tuleva muisti </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->inkl_minneskort); %></font></td>
	<% endfor; %>
</tr>
<!--
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"> Muistikapasiteetti</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minneskapacitet); %></font></td>
	<% endfor; %>
</tr>
-->
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges om kameran har en vanlig optisk sökare eller en EVF, dvs en elektronisk sökare, eller ingen alls.')"><font face="Verdana, Arial" size="1"> Optinen etsin </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->optisk_sokare); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger storlek på bildskärmen som sitter på kamerans baksida. Anges oftast i tum och på diagonalen.')"><font face="Verdana, Arial" size="1"> Näyttö </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->bildskarm)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger vilka typer av ljusmätning som finns att tillgå. Evaluerande=flerzon=matrix=ESP ljusmätning mäter ljuset i flera mätzoner och har programvara som talar om för kameran hur den ska reagera vid olika kombinationer. Centrumvägd ljusmätning mäter till största delen i motivets mitt och mindre utåt kanterna. Utsnittsmätning mäter endast i motivets centrum. Spotmätning mäter också endast i motivets centrum men betydligt snävare än vad utsnittsmätningen gör.')"><font face="Verdana, Arial" size="1"> Valonmittaus </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljusmatning); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger vad kameran har för inställningsmöjligheter angående färgåtergivningen i förhållande till motivets färgtemperatur. Ofta finns ett automatiskt läge, några förinställda lägen och ett manuellt läge där du kan låsa vitbalansen mot något vitt. Bilder som tas i RAW-format kan justeras i efterhand i ett bildbehandlingsprogram.')"><font face="Verdana, Arial" size="1"> Valkobalanssi </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vitbalans); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anges idag nästan uteslutande i ISO. ISO står för International Organization for Standardization. Tidigare angavs filmkänslighet i ASA och/eller DIN. Till exempel 100ASA motsvarar 21DIN och ska nu egentligen anges ISO 100/21° men på digitalkameror så anges endast ISO 100 som motsvarande ljuskänslighet. På film ger högre ISO-tal kornigare bilder. På en digitalkamera så fungerar det likadant fast det blir inte kornigare utan brusigare bilder, ungefär samma sak.')"><font face="Verdana, Arial" size="1"> Valoherkkyys </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljuskanslighet); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger vilka tider kamerans slutare kan arbeta inom. Det vill säga den tid kamerans bildsensor/film får ljus. Vanligt är från ca 1s upp till 1/2000s.')"><font face="Verdana, Arial" size="1"> Suljinajat </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->slutartider); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges de olika exponeringslägena. Programautomatik där kameran ställer både tid och bländare. Tidsautomatik där du ställer bländaren och kameran ställer tiden. Bländarautomatik där du ställer tiden och kameran väljer bländare. Manuellt där du ställer både tid och bländare själv.')"><font face="Verdana, Arial" size="1"> Valotus </font>
	<br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->exponering); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('En funktion där du kan påverka exponeringen utan att behöva köra manuellt. Vanligast är att du kan över eller underexponera 2 bländarsteg.')"><font face="Verdana, Arial" size="1"> Valotuksen kompensointi </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->exponeringskompensation); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Kameran kan fördröja exponeringen oftast 10-12s efter avtryck.')"><font face="Verdana, Arial" size="1"> Automaattilaukaisin </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->sjalvutlosare); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges om kameran har inbyggd blixt, och/eller om den har möjlighet till extern blixt.')"><font face="Verdana, Arial" size="1"> Salama </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges de inställningar som finns att ställa in på den inbyggda blixten om kameran har en sådan.')"><font face="Verdana, Arial" size="1"> Salama-asetukset
	</font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixtinstalln); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges hur långt bort du kan fotografera med den inbyggda blixten om kameran har en sådan.')"><font face="Verdana, Arial" size="1"> Salaman valaisukyky </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixt_rackvidd); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Kamerans möjlighet att ta flera bilder i en följd. Anges ofta hur många bilder per sekund (b/s alt fps) ibland även hur många bilder i följd.')"><font face="Verdana, Arial" size="1"> Sarjakuvaus </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->sekvenstagning)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Om kamerans möjligheter att spela in film. Oftast anges upplösning, bilder per sekund och hur långa sekvenser som kan spelas in.')"><font face="Verdana, Arial" size="1"> Filmaus </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->film); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Anger om kameran har inbyggd mikrofon.')"><font face="Verdana, Arial" size="1"> Äänentallennus </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljudinspelning); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges kameran olika anslutningar. Om anslutningar sitter på en medföljande docka så anges detta.')"><font face="Verdana, Arial" size="1"> Liitännät </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info($row[$i]->anslutningar); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges vad kameran använder för typ av batteri.')"><font face="Verdana, Arial" size="1"> Akku </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->batteri); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Här anges speciella egenskaper eller om bruksanvisning är på annat språk än svenska.')"><font face="Verdana, Arial" size="1"> Muuta </font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ovrigt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Kamerans mått som de uppges av tillverkaren.')"><font face="Verdana, Arial" size="1"> Mitat </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->matt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Kamerans vikt som den uppges av tillverkaren. Tyvärr uppger de ibland med batterier och ibland utan.')"><font face="Verdana, Arial" size="1"> Paino </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vikt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('De tillbehör som tillverkaren uppger ska följa med.')"><font face="Verdana, Arial" size="1"> Mukana tulevat tarvikkeet
	 </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->medfolj_tillb)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><img src="/css/images/fraga2.gif" border="0" onMouseOver="return escape('Den programvara som tillverkaren uppger ska följa med.')"><font face="Verdana, Arial" size="1"> Mukana tulevat ohjelmat
	  </font><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->medfolj_program)); %></font></td>
	<% endfor; %>
<!--
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Järjestelmävaatimukset</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->systemkrav)); %></font></td>
	<% endfor; %>
</tr>
-->
<!--
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Kuvaesimerkkejä</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_pic($row[$i]->bildexempel); %></font></td>
	<% endfor; %>
</tr>
-->
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
	<% if ($comparepage != 'yes'): %>
	Vertaa muihin digitaalisiin kameroihin! Valitse listalta.
	<% else: %>
	Valitse toinen kamera listalta ja voit verrata sitä ylläolevaan kameraan.
	<% endif; %>
	</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_digikam($tekniska_artnr[$i], 'NULL'); %></font></td>
	<% endfor; %></tr><% } %>
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
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kuvakennon koko ilmoitetaan yleensä tuumissa vaakasuoraan mitattaessa.')"><font face="Verdana, Arial" size="1"> Kuvakennon koko&nbsp;</font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->storlekccd)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kyseessä on kuvakennon kokonaiserottelukyky. Todellinen erottelukyky, joka voidaan käyttää hyödyksi ilmoitetaan kuvanerottelukykynä tai tehollisena erottelukykynä. Nykyisin suurin osa valmistajista ilmoittaa kameroidensa todellisen (tehollisen) erottelukyvyn.')"><font face="Verdana, Arial" size="1"> Kuvakennon erottelukyky </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info2($row[$i]->ccd); %> 
pikseleitä</font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Todellinen erottelukyky, eli se minkä voi käyttää hyödykseen. Kun haluat tietää megapixeleiden määrän kerrot ensin annetun pixelimäärän toisella (ilmoitetulla), niin saat esille tehollisen pixelimäärän. Nykyisin useimmat valmistajat ilmoittavat kameroidensa todellisen (tehollisen) erottelukyvyn.')"><font face="Verdana, Arial" size="1"> Kuvan erottelukyky (tehollinen)) </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->upplosning); %></font></td>
	<% endfor; %>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Ilmoittaa kuvien tallennusmuodon. JPG on yleisin. RAW ja TIFF ovat pakkaamattomia. DNG on tulossa.Adoben avoin RAW-muoto.')"><font face="Verdana, Arial" size="1"> Tiedostomuoto </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->kompression); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tässä ilmoitetaan objektiivin polttoväli ja mitä objektiivi vastaa 35mm kamerassa.')"><font face="Verdana, Arial" size="1"> Objektiivi </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->objektiv_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tässä ilmoitetaan maksimi valonvoimakkuus. Zoomobjektiiveissa valovoimakkuus vaihtelee polttovälialueella siksi valovoimakkuus ilmoitetaan esim.2,8-3,5 jossa maksimi valovomakkuus on 2,8 laajakulmassa ja tele 3,5. Tässä ei ilmoiteta pienintä mahdollista aukkoa.')"><font face="Verdana, Arial" size="1"> Optiikan valovoimaisuus </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blandare); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Todellinen polttovälialue. Yksinkertaisesti objektiivin kattama alue.')"><font face="Verdana, Arial" size="1"> Optinen zoom </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->optisk_zoom); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tämä ei ole todellinen zoom, vaan tässä kamera rajoittaa kuvaa. Mitä enemmän zoomaat sen huonompi on kuvan tarkkuus.')"><font face="Verdana, Arial" size="1"> Digitaalinen zoom </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->dig_zoom); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kameraan sopivan suodattimen tai suodatinsovitteen koko.')"><font face="Verdana, Arial" size="1"> Suodattimen halkaisija </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->filterdiam_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Onko kamerassa on auto-/ käsitarkennus tai molemmat.')"><font face="Verdana, Arial" size="1"> Fokusointi </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->fokusering_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Lähin etäisyys jolloin kamera ottaa vielä teräviä kuvia.')"><font face="Verdana, Arial" size="1"> Lähietäisyys </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->nargrans); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Ilmoittaa kameraan sopivan muistikortin. Löytyvät kohdasta tarvikkeet.')"><font face="Verdana, Arial" size="1"> Muisti </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->minnestyp); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Ilmoittaa kameran vakiovarustukseen kuuluvan tai kamerassa olevan sisäisen muistin.')"><font face="Verdana, Arial" size="1"> Mukana tuleva muisti </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->inkl_minneskort_fi); %></font></td>
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
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tässä ilmoitetaan, onko kamerassa tavallinen optinen etsin vaiko EVF, ts. elektroninen etsin tai ei ollenkaan.')"><font face="Verdana, Arial" size="1"> Optinen etsin </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->optisk_sokare_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Ilmoittaa kameran näyttöruudun koon. Ilmoitetaan yleensä tuumissa leveyssuuntaan.')"><font face="Verdana, Arial" size="1"> Näyttö </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->bildskarm)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Ilmoittaa, mitä valonmittauksia on käytettävissä. Arvioiva=monialue=matrix=ESP, valonmittaus mittaa valoa useilla alueilla ja ohjelma kertoo kameralle miten reagoida eri tilanteissa. Keskipainotteinen valonmittaus mittaa eniten kohteen keskiosassa ja vähemmän reunoilla. Osa-aluemittaus mittaa kohteen keskustaa. Pistemittaus mittaa myös kohteen keskustaa, mutta huomattavasti rajoitetummin kuin osa-aluemittaus.')"><font face="Verdana, Arial" size="1"> Valonmittaus </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljusmatning_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Ilmoittaa kameran säätömahdollisuudet värintoistossa suhteessa kohteen värilämpöön. Useinmiten on olemassa automaatiasento,muutama esivalittu ja käsisäätöinen asento, jossa voit lukita valkobalanssin vasten jotain valkeaa. RAW-muodossa otettuja kuvia voi korjata myös jäljestäpäin kuvankäsittelyohjelmassa.')"><font face="Verdana, Arial" size="1"> Valkobalanssi </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vitbalans_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Ilmoitetaan nykyään melkein poikkeuksetta ISO-arvoina. ISO tulee sanoista International Organization for Standardization. Aiemmin kuvaherkkyys ilmoitettiin ASA ja/tai DIN-arvoina. Esim. 100ASA vastaa 21DIN ja tulisi nykyään ilmoittaa ISO 100/21° mutta digitaalikameroissa ilmoitetaan ainoastaan ISO 100 joka vastaa valoherkkyyttä. Filmeillä suurempi ISO-luku aiheuttaa rakeisuutta kuvissa. Digitaalikameroissa se toimii melkein samoin, mutta kuviin ei tule rakeisuutta vaan kohinaa, suurin piirtein sama asia.')"><font face="Verdana, Arial" size="1"> Valoherkkyys </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljuskanslighet); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Ilmoittaa kameran sulkija toiminta-ajat. Toisin sanoen kuinka pitkän ajan kameran kuvakenno/filmi saa valoa. Tavallisimmin noin 1s jopa 1/2000sekuntiin.')"><font face="Verdana, Arial" size="1"> Suljinajat </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->slutartider); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tässä ilmoitetaan eri valotusasetukset. Ohjelma-automatiikka jossa kamera säätää sekä ajan että aukon. Ajan esivalinta-automatiikka, jolloin sinä säädät aukon ja kamera ajan. Aukon esivalinta-automatiikka sinä säädät ajan ja kamera valitsee aukon. Manuaali, jolloin sinä säädät sekä ajan että aukon itse.')"><font face="Verdana, Arial" size="1"> Valotus </font>
	<br></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->exponering_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Toiminto, jossa voit vaikuttaa valotukseen ilman että käytät käsisäätöä. Tavallisinta on kahden askeleen yli- tai alivaloitus.')"><font face="Verdana, Arial" size="1"> Valotuksen kompensointi </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->exponeringskompensation_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kamera voi viivyttää valotusta yleensä 10-12s laukaisun jälkeen.')"><font face="Verdana, Arial" size="1"> Automaattilaukaisin </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->sjalvutlosare_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tässä ilmoitetaan onko kamerassa sisäänrakennettu salama, ja/tai onko mahdollisuutta ulkopuoliseen salamaan.')"><font face="Verdana, Arial" size="1"> Salama </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixt_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tässä kerrotaan sisäänrakennetun salaman säätömahdollisuudet, mikäli sellaisia on.')"><font face="Verdana, Arial" size="1"> Salama-asetukset
	</font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixtinstalln_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tässä kerrotaan kuinka kaukaa voit kuvata sisäänrakennetulla salamalla mikäli kamerassa sellainen on.')"><font face="Verdana, Arial" size="1"> Salaman valaisu </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->blixt_rackvidd_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kameran mahdollisuudet ottaa kuvasarjoja. Ilmoitetaan usein kuinka monta kuvaa per sekunti (b/s tai fps) joskus jopa montako kuvaa peräkkäin.')"><font face="Verdana, Arial" size="1"> Sarjaotos </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->sekvenstagning_fi)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kameran mahdollisuudesta kuvata filmiä. Yleensä ilmoitetaan erottelukyky, kuvaa per sekunti ja kuinka pitkiä otoksia voidaan tallentaa.')"><font face="Verdana, Arial" size="1"> Filmaus </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->film_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Ilmoittaa onko kamerassa sisäänrakennettu mikrofoni.')"><font face="Verdana, Arial" size="1"> Äänentallennus </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ljudinspelning_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tässä ilmoitetaan kameran eri liitännät. Jos liitännät ovat mukana seuraavassa telakassa niin se ilmoitetaan.')"><font face="Verdana, Arial" size="1"> Liitännät </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info($row[$i]->anslutningar_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tässä ilmoitetaan kameran käyttämien paristojen tyyppi.')"><font face="Verdana, Arial" size="1"> Akku </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->batteri_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Tässä ilmoitetaan erityisominaisuuksista tai jos käyttöohje on muulla kuin kotimaisilla kielillä.')"><font face="Verdana, Arial" size="1"> 
Muuta </font></a></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->ovrigt_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kameran valmistajan ilmoittamat mitat.')"><font face="Verdana, Arial" size="1"> Mitat </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->matt); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Valmistajan ilmoittama kameran paino. Valitettavasti joskus paristojen kanssa, joskus ilman.')"><font face="Verdana, Arial" size="1"> Paino </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><% show_info($row[$i]->vikt_fi); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Valmistajan ilmoittamat mukana tulevat varusteet.')"><font face="Verdana, Arial" size="1"> Mukana tulevat varusteet </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->medfolj_tillb_fi)); %></font></td>
	<% endfor; %>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Ohjelma jonka valmistaja ilmoittaa tulevan mukana.')"><font face="Verdana, Arial" size="1"> Mukana tulevat ohjelmat </font></a><br></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_info(ereg_replace('`', '"', $row[$i]->medfolj_program_fi)); %></font></td>
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
	Valitse toinen kamera listalta ja vertaa sitä ylläolevaan kameraan.
	<% endif; %>
	</font></td>
	<% for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '5') break;  %>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><% show_digikam($tekniska_artnr[$i], 'NULL'); %></font></td>
	<% endfor; %></tr><% } %>
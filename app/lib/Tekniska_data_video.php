<?php
include_once ("CTekniska_data.php");
include_once ("CConnect.php");

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
		if ($i > '2') break; // För säkerhets skull

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

?>

<?php if ($comparepage == 'yes'): ?>
<tr>
	<td valign="top" bgcolor="#FFFFFF"><font face="Verdana, Arial" size="1"></font></td>
<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial" size="1">
	<?php $artnr = $tekniska_artnr[$i]; include ("/home/www/cgi-bin/buyspot_tekn_new.php"); ?>
	</font></a></td>
<?php endfor; ?>
</tr>

<?php endif; ?>

<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger vilken typ av lagringsmedia kameran använder. När man normalt talar om DV-kameror och DV-band så är det egentligen mini-DV som avses. Video8 finns knappt lägre. Hi8 är en bättre version av Video8. Digital8 är en kamera som lagrar digital film på Hi8-band. Finns inte många kvar på marknaden, då DV dominerar. Det som kommer mer och mer är kameror med minneskort och/eller hårddisk. Förmodligen ligger framtiden där. Däremot DVD-kameror har inte slagit så bra. De är i regel dyrare, klumpigare och ger sämre bild än DV')"><font face="Verdana, Arial" size="1"> System </font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->system); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Den fysiska storleken på bildsensorn. Anges oftast på diagonalen och i tum.')"><font face="Verdana, Arial" size="1"> CCD storlek</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->ccd); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Den upplösning bildsensorn har. Den teoretiska upplösningen kan man säga. I filmläge utnyttjas inte hela bildsensorn medan större delen utnyttjas vid stillbilder.')"><font face="Verdana, Arial" size="1"> CCD brutto</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->ccdBrutto); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Den verkliga upplösningen, dvs den upplösning som går att utnyttja. I filmläge utnyttjas inte hela bildsensorn medan större delen utnyttjas vid stillbilder.')"><font face="Verdana, Arial" size="1"> CCD netto vid film</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->cccdNettoFilm); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Den verkliga upplösningen, dvs den upplösning som går att utnyttja. I filmläge utnyttjas inte hela bildsensorn medan större delen utnyttjas vid stillbilder.')"><font face="Verdana, Arial" size="1"> CCD netto vid stillbild</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->ccdNettoStillbild); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Den verkliga upplösningen, dvs den upplösning som går att utnyttja. Vill du veta antal megapixel så tar du första pixelangivelsen multiplicerat med den andra så får du fram exakt antal effekiva pixlar. Numera anger de flesta tillverkare den verkliga (effektiva) upplösningen på sina kameror.')"><font face="Verdana, Arial" size="1"> Stillbildsläge</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->stillbildslage); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges objektivets brännviddsomfång och ljusstyrka.')"><font face="Verdana, Arial" size="1"> Objektiv</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->objektiv); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Det verkliga zoomomfånget. Helt enkelt det omfång objektivet har.')"><font face="Verdana, Arial" size="1"> Zoom</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->zoom); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Detta är egentligen ingen riktig zoom utan endast att kameran beskär i bilden. Ju mer du zoomar desto sämre upplösning.')"><font face="Verdana, Arial" size="1"> Digital Zoom</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->digitalZoom); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('I stort sett att videokameror har bildstabilisering idag. Det finns två tekniker. Digital bildstabilisering är billigare, men stjäl upplösning. Det är dock inget större problem om kameran har större upplösning på bildsensorn än vad en videobild kräver. Då kan den ge i stort sett full videoupplösning. Den andra tekniken är optisk bildstabilisering. Det är en mer avancerad konstruktion och fungerar så att linser styrs för att kompensera skakningar. Sitter ofta i mer avancerade kameror.')"><font face="Verdana, Arial" size="1"> Bildstabilisering</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->bildstabilisering); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger det ljusvärde som det är möjligt att filma i. Det är dock generellt först vid ca 100 lux som en videokamera ger en bra videobild. Tänk också på att ju sämre ljus desto sämre färger.')"><font face="Verdana, Arial" size="1"> Minsta belysning</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->minstaBelysning); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om du kan ställa in skärpan manuellt.')"><font face="Verdana, Arial" size="1"> Manuell fokuseringsmöjlighet</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->manuellFokus); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Den storlek av filtergänga som passar kameran eller angiven filteradapter.')"><font face="Verdana, Arial" size="1"> Filterdiameter</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->filterdiam); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger vad kameran har för inställningsmöjligheter angående färgåtergivningen i förhållande till motivets färgtemperatur. Ofta finns ett automatiskt läge, några förinställda lägen och ett manuellt läge där du kan låsa vitbalansen mot något vitt.')"><font face="Verdana, Arial" size="1"> Vitbalans</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->vitbalans); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges vilka digitala effekter kameran. Dessa är i regel mer för lek och experiment.')"><font face="Verdana, Arial" size="1"> Digitala effekter</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->digitalaEffekter); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om kameran har någon form av 16:9-läge. De två vanligaste är att kameran beskär upptill och nertill och ger 16:9-format som du sedan använder funktionen zoom på TVn. Det andra och mer seriösa 16:9-läget är att kameran trycker ihop bilden horisontellt till att bli så kalla anomorfisk och den filmen kan du visa upp i TVns äkta wide-läge.')"><font face="Verdana, Arial" size="1"> 16:9 wide</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->wide); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges de olika exponeringslägena. Programautomatik där kameran ställer både tid och bländare. Tidsautomatik där du ställer bländaren och kameran ställer tiden. Bländarautomatik där du ställer tiden och kameran väljer bländare. Manuellt där du ställer både tid och bländare själv.')"><font face="Verdana, Arial" size="1"> Exponering</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->exponering); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om kameran har motivprogram. Det är hjälpprogram som är anpassade efter olika filmsituationer. Avsett som en hjälp till den oerfarne filmaren.')"><font face="Verdana, Arial" size="1"> Motivprogram</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->motivprogram); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om sökaren är i färg eller svartvit. Färg har fördelen med att det ser naturligare ut eftersom verkligeheten är i färg, medan den svartvita sökaren har i regel en högre upplösning som gör det lättare att fokusera manuellt.')"><font face="Verdana, Arial" size="1"> Sökare</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->sokare); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger storlek på bildskärmen som sitter på kamerans baksida. Anges oftast i tum och på diagonalen.')"><font face="Verdana, Arial" size="1"> Bildskärm</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->fargLcd); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om kameran har inbyggd blixt.')"><font face="Verdana, Arial" size="1"> Blixt</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->blixt); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger den typ av minneskort som passar kameran. Dessa hittar du under fliken tillbehör på denna sida.')"><font face="Verdana, Arial" size="1"> Minneskort</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->minneskort); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"> Minneskapacitet</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->minneskapacitet); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om kameran har inbyggd högtalare.')"><font face="Verdana, Arial" size="1"> Högtalare</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->hogtalare); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('RCA kallas också Phonoplugg och är den vanligaste typen av kontakt som finns på t ex stereoprylar. Här avses möjligheten att sända ut vanlig videobild, kompositbild, till TV eller video.')"><font face="Verdana, Arial" size="1"> RCA A/V ut</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->RCAut); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('RCA kallas också Phonoplugg och är den vanligaste typen av kontakt som finns på t ex stereoprylar. Här avses möjligheten att spela in vanlig videobild, kompositbild, till TV eller video.')"><font face="Verdana, Arial" size="1"> RCA A/V in</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->RCAin); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här avses möjligheten att sända ut S-signal till TV eller video. Det krävs då att de har S-video anslutning.')"><font face="Verdana, Arial" size="1"> S-video ut</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->svideoUt); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här avses möjligheten att spela in S-signal från TV eller video. Det krävs då att de har S-video anslutning.')"><font face="Verdana, Arial" size="1"> S-video in</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->svideoIn); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('DV-kontakt, eller Firewire, IEE1394 eller I-Link som Sony kallar det. Kärt barn har många namn. Här avses möjligheten att via Firewire sända ut bild och ljud.')"><font face="Verdana, Arial" size="1"> DV ut</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->dvUt); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('DV-kontakt, eller Firewire, IEE1394 eller I-Link som Sony kallar det. Kärt barn har många namn. Här avses möjligheten att via Firewire spela in bild och ljud.')"><font face="Verdana, Arial" size="1"> DV in</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->dvIn); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om kameran har möjlighet att ansluta extern mikrofon.')"><font face="Verdana, Arial" size="1"> Mic in</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->micIn); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om kameran har USB-anslutning. USB står för Universal Serial Bus. Senare kameramodeller har USB2.0. I de flesta fall så kan du inte överföra film via denna port.')"><font face="Verdana, Arial" size="1"> USB</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->usb); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om kameran har möjlighet att ansluta hörlurar. Rätt praktiskt för den enusiastiske filmaren då du hör exakt vad som kommer på bandet. Speciellt vid användning av extern mikrofon är det bra att använda hörlur för att säkerställa att man får med sig ljud hem, dåden inbyggda mikrofonen bryts när en extern är inkopplad i mikrofoningången.Vi rekommenderar KOSS Porta Pro hörlurar.')"><font face="Verdana, Arial" size="1"> Hörlursutgång</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->horlursutgang); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om kameran har möjlighet till fjärrkontroll. Om den ingår ser du under medföljande tillbehör.')"><font face="Verdana, Arial" size="1"> Fjärrkontroll</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->fjarrkontroll); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kamerans mått som de uppges av tillverkaren.')"><font face="Verdana, Arial" size="1"> Storlek</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->storlek); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kamerans vikt som den uppges av tillverkaren. Tyvärr uppger de ibland med batterier och ibland utan.')"><font face="Verdana, Arial" size="1"> Vikt</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->vikt); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges språk på bruksanvisningen.')"><font face="Verdana, Arial" size="1"> Bruksanvisning</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->bruksanvisning); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('De tillbehör som tillverkaren uppger ska följa med.')"><font face="Verdana, Arial" size="1"> Medföljande tillbehör</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->medfoljandeTillbehor); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('De programvaror som tillverkaren uppger ska följa med.')"><font face="Verdana, Arial" size="1"> Medföljande programvara</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->medfoljandeProgramvara); ?></font></a></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges speciella egenskaper.')"><font face="Verdana, Arial" size="1"> Övrigt</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->edit); ?></font></a></td>
	<?php endfor; ?>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
	<?php if ($comparepage != 'yes'): ?>
	Jämför med andra videokameror! Välj ur listan.
	<?php else: ?>
	Välj en annan videokamera ur listan för att jämföra med ovanstående videokamera.
	<?php endif; ?>
	</font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php show_tek_video($tekniska_artnr[$i], 'NULL'); ?></font></td>
	<?php endfor; ?>
</tr>


<?php } ?>
<?php
include_once ("CTekniska_data.php");

if ($comparepage != 'yes') {
	$num_of_articles = 1;
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
} else {
	for ($i = 1; $i <= $num_of_articles; $i++):
		if ($i > 2) break; // För säkerhets skull

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

?>

<?php if ($comparepage == 'yes'): ?>
<tr>
	<td valign="top" bgcolor="#FFFFFF"><font face="Verdana, Arial" size="1"></font></td>
<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial" size="1">
	<?php $artnr = $tekniska_artnr[$i]; include ("/home/www/cgi-bin/buyspot_tekn_new.php"); ?>
	</font></td>
<?php endfor; ?>
</tr>

<?php endif; ?>

<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Den fysiska storleken på bildsensorn. Anges oftast på diagonalen och i tum.')"><font face="Verdana, Arial" size="1"> Bildsensor storlek&nbsp;</font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info(ereg_replace('`', '"', $row[$i]->storlekccd)); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Det är den totala upplösningen på sensorn. Den verkliga upplösningen, dvs den upplösning som går att utnyttja är vad som anges som Upplösning bild eller Effektiv upplösning. Numera anger de flesta tillverkare den verkliga (effektiva) upplösningen på sina kameror.')"><font face="Verdana, Arial" size="1"> Upplösning bildsensor </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info2($row[$i]->ccd); ?> pixlar</font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Den verkliga upplösningen, dvs den upplösning som går att utnyttja. Vill du veta antal megapixel så tar du första pixelangivelsen multiplicerat med den andra så får du fram exakt antal effekiva pixlar. Numera anger de flesta tillverkare den verkliga (effektiva) upplösningen på sina kameror.')"><font face="Verdana, Arial" size="1"> Upplösning bild (effektiv) </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->upplosning); ?></font></td>
	<?php endfor; ?>
</tr>

<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger den filtyp som bilderna sparas i. JPG är vanligast. RAW och TIFF är okomprimerade format. DNG är på kommande. Det är Adobes öppna RAW-format.')"><font face="Verdana, Arial" size="1"> Kompressionsstandard </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->kompression); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges vilken brännvidd objektivet har och ofta även vad objektivet motsvarar på en 35mm kamera.')"><font face="Verdana, Arial" size="1"> Objektiv </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->objektiv); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges den maximala ljusstyrkan. På zoomobjektiv varierar oftast ljusstyrkan med zoomomfånget och därför anges ljusstyrkan till t ex 2,8-3,5 och där är den maximala ljusstyrkan 2,8 på vidvinkel och 3,5 på tele. Här anges inte minsta möjliga bländare.')"><font face="Verdana, Arial" size="1"> Ljusstyrka optik </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->blandare); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Det verkliga zoomomfånget. Helt enkelt det omfång objektivet har.')"><font face="Verdana, Arial" size="1"> Optisk zoom </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->optisk_zoom); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Detta är egentligen ingen riktig zoom utan endast att kameran beskär i bilden. Ju mer du zoomar desto sämre upplösning.')"><font face="Verdana, Arial" size="1"> Digital zoom </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->dig_zoom); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Den storlek av filtergänga som passar kameran eller angiven filteradapter.')"><font face="Verdana, Arial" size="1"> Filterdiameter </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->filterdiam); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Om kameran har autofokus, manuell fokusering eller båda.')"><font face="Verdana, Arial" size="1"> Fokusering </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->fokusering); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Det närmaste avstånd kameran kan ta en skarp bild.')"><font face="Verdana, Arial" size="1"> Närgräns </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->nargrans); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger den typ av minneskort som passar kameran. Dessa hittar du under fliken tillbehör på denna sida.')"><font face="Verdana, Arial" size="1"> Minnestyp </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->minnestyp); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger det minne som ingår i standardtillbehören eller är inbyggt i kameran.')"><font face="Verdana, Arial" size="1"> Minne som ingår </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->inkl_minneskort); ?></font></td>
	<?php endfor; ?>
</tr>
<!--
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"> Minneskapacitet</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->minneskapacitet); ?></font></td>
	<?php endfor; ?>
</tr>
-->
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om kameran har en vanlig optisk sökare eller en EVF, dvs en elektronisk sökare, eller ingen alls.')"><font face="Verdana, Arial" size="1"> Optisk sökare </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->optisk_sokare); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger storlek på bildskärmen som sitter på kamerans baksida. Anges oftast i tum och på diagonalen.')"><font face="Verdana, Arial" size="1"> Bildskärm </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info(ereg_replace('`', '"', $row[$i]->bildskarm)); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger vilka typer av ljusmätning som finns att tillgå. Evaluerande=flerzon=matrix=ESP ljusmätning mäter ljuset i flera mätzoner och har programvara som talar om för kameran hur den ska reagera vid olika kombinationer. Centrumvägd ljusmätning mäter till största delen i motivets mitt och mindre utåt kanterna. Utsnittsmätning mäter endast i motivets centrum. Spotmätning mäter också endast i motivets centrum men betydligt snävare än vad utsnittsmätningen gör.')"><font face="Verdana, Arial" size="1"> Ljusmätning </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->ljusmatning); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger vad kameran har för inställningsmöjligheter angående färgåtergivningen i förhållande till motivets färgtemperatur. Ofta finns ett automatiskt läge, några förinställda lägen och ett manuellt läge där du kan låsa vitbalansen mot något vitt. Bilder som tas i RAW-format kan justeras i efterhand i ett bildbehandlingsprogram.')"><font face="Verdana, Arial" size="1"> Vitbalans </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->vitbalans); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anges idag nästan uteslutande i ISO. ISO står för International Organization for Standardization. Tidigare angavs filmkänslighet i ASA och/eller DIN. Till exempel 100ASA motsvarar 21DIN och ska nu egentligen anges ISO 100/21° men på digitalkameror så anges endast ISO 100 som motsvarande ljuskänslighet. På film ger högre ISO-tal kornigare bilder. På en digitalkamera så fungerar det likadant fast det blir inte kornigare utan brusigare bilder, ungefär samma sak.')"><font face="Verdana, Arial" size="1"> Ljuskänslighet </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->ljuskanslighet); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger vilka tider kamerans slutare kan arbeta inom. Det vill säga den tid kamerans bildsensor/film får ljus. Vanligt är från ca 1s upp till 1/2000s.')"><font face="Verdana, Arial" size="1"> Slutartider </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->slutartider); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges de olika exponeringslägena. Programautomatik där kameran ställer både tid och bländare. Tidsautomatik där du ställer bländaren och kameran ställer tiden. Bländarautomatik där du ställer tiden och kameran väljer bländare. Manuellt där du ställer både tid och bländare själv.')"><font face="Verdana, Arial" size="1"> Exponering </font>
	<br></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->exponering); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('En funktion där du kan påverka exponeringen utan att behöva köra manuellt. Vanligast är att du kan över eller underexponera 2 bländarsteg.')"><font face="Verdana, Arial" size="1"> Exponeringskompensation </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->exponeringskompensation); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kameran kan fördröja exponeringen oftast 10-12s efter avtryck.')"><font face="Verdana, Arial" size="1"> Självutlösare </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->sjalvutlosare); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges om kameran har inbyggd blixt, och/eller om den har möjlighet till extern blixt.')"><font face="Verdana, Arial" size="1"> Blixt </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->blixt); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges de inställningar som finns att ställa in på den inbyggda blixten om kameran har en sådan.')"><font face="Verdana, Arial" size="1"> Blixtinställningar
	</font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->blixtinstalln); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges hur långt bort du kan fotografera med den inbyggda blixten om kameran har en sådan.')"><font face="Verdana, Arial" size="1"> Räckvidd blixt </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->blixt_rackvidd); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kamerans möjlighet att ta flera bilder i en följd. Anges ofta hur många bilder per sekund (b/s alt fps) ibland även hur många bilder i följd.')"><font face="Verdana, Arial" size="1"> Sekvenstagning </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info(ereg_replace('`', '"', $row[$i]->sekvenstagning)); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Om kamerans möjligheter att spela in film. Oftast anges upplösning, bilder per sekund och hur långa sekvenser som kan spelas in.')"><font face="Verdana, Arial" size="1"> Film </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->film); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Anger om kameran har inbyggd mikrofon.')"><font face="Verdana, Arial" size="1"> Ljudinspelning </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->ljudinspelning); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges kameran olika anslutningar. Om anslutningar sitter på en medföljande docka så anges detta.')"><font face="Verdana, Arial" size="1"> Anslutningar </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php show_info($row[$i]->anslutningar); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges vad kameran använder för typ av batteri.')"><font face="Verdana, Arial" size="1"> Batteri </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->batteri); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Här anges speciella egenskaper eller om bruksanvisning är på annat språk än svenska.')"><font face="Verdana, Arial" size="1"> Övrigt </font></a></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->ovrigt); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kamerans mått som de uppges av tillverkaren.')"><font face="Verdana, Arial" size="1"> Mått </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->matt); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Kamerans vikt som den uppges av tillverkaren. Tyvärr uppger de ibland med batterier och ibland utan.')"><font face="Verdana, Arial" size="1"> Vikt </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php show_info($row[$i]->vikt); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('De tillbehör som tillverkaren uppger ska följa med.')"><font face="Verdana, Arial" size="1"> Medföljande
	tillbehör </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php show_info(ereg_replace('`', '"', $row[$i]->medfolj_tillb)); ?></font></td>
	<?php endfor; ?>
</tr>
<tr>
	<td valign="top" bgcolor="#ECECE6"><a onMouseOver="return escape('Den programvara som tillverkaren uppger ska följa med.')"><font face="Verdana, Arial" size="1"> Medföljande
	  programvara </font></a><br></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php show_info(ereg_replace('`', '"', $row[$i]->medfolj_program)); ?></font></td>
	<?php endfor; ?>
<!--
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Systemkrav</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php show_info(ereg_replace('`', '"', $row[$i]->systemkrav)); ?></font></td>
	<?php endfor; ?>
</tr>
-->
<!--
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Bildexempel</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php show_pic($row[$i]->bildexempel); ?></font></td>
	<?php endfor; ?>
</tr>
-->
<tr>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
	<?php if ($comparepage != 'yes'): ?>
	Jämför med andra digitala kameror! Välj
	kamera ur listan.
	<?php else: ?>
	Välj en annan kamera i ur listan för att jämföra med ovanstående kamera.
	<?php endif; ?>
	</font></td>
	<?php for ($i = 1; $i <= $num_of_articles; $i++): if ($i > '2') break;  ?>
	<td valign="top" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php show_digikam($tekniska_artnr[$i], 'NULL'); ?></font></td>
	<?php endfor; ?>
</tr>
<?php } ?>

<div class="top10"></div>
<div class="hr_gray"></div>
<div class="top10"></div>
<?php
if ($addid != "") {
	echo "<h2>Redigera post</h2>\n";
} elseif ($addidc != "") {
	echo "<h2>Kopiera post</h2>\n";
} else {
	echo "<h2>Lägg till post</h2>\n";
}
if ($addOrder == "") {
	$addOrder = 0;
}
?>
<div class="container_grey floatleft">
<form name="addbannerform">
  <?php if ($addid !="") { ?>
  <input type="hidden" value="<?php echo $addid; ?>" name="addid">
  <input type="hidden" value=true name="submC">
  <?php } else { ?>
  <input type="hidden" value=true name="subm">
  <input type="hidden" value="yes" name="add">
  <input type="hidden" value="<?php echo $addidc; ?>" name="addidc">
  <?php } ?>
  <table border="0" cellpadding="2" cellspacing="1">
	<?php if ($addidc != "" || $add == "yes") { ?>
    <tr>
      <td>Välj artikel</td>
      <td>
	  <select size="1" name="addArtnr">
      <option></option>
      <?php $tech->getActualProductsInCategory('395'); ?>
      </select>
      </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Visa även artiklar som är "EJ MED"</td>
      <td><input type="checkbox" name="show_ej_med" value="yes" <?php if ($show_ej_med == "yes"): echo "checked"; endif; ?> onclick="submit()"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<?php } ?>
    <tr>
      <td>Bildsensor storlek</td>
      <td><input type="text" name="params1" size="125" value="<?php echo $params1; ?>" placeholder="APS-C, Micro 4/3, Fullformat och mått i mm + ev tillverkarbenämning, dvs DX, CX eller liknande"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Upplösning bildsensor</td>
      <td><input type="text" name="params2" size="125" value="<?php echo $params2; ?>" placeholder="24000000"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Upplösning bild (effektiv)</td>
      <td><input type="text" name="params3" size="125" value="<?php echo $params3; ?>" placeholder="6000 x 4000 pixel"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Kompressionsstandard</td>
      <td><input type="text" name="params4" size="125" value="<?php echo $params4; ?>" placeholder="JPEG, RAW, MPEG4, H.264, MOV (vad gäller film anges helst container och kompressionsstandard)"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Objektiv</td>
      <td><input type="text" name="params5" size="125" value="<?php echo $params5; ?>" placeholder="Nikon F AF, Canon EF, Pentax K etc"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Ljusstyrka optik</td>
      <td><input type="text" name="params6" size="125" value="<?php echo $params6; ?>" placeholder="Beror på val av objektiv"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Optisk zoom</td>
      <td><input type="text" name="params7" size="125" value="<?php echo $params7; ?>" placeholder="Beror på val av objektiv"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Digital zoom</td>
      <td><input type="text" name="params8" size="125" value="<?php echo $params8; ?>" placeholder=""></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Filterdiameter</td>
      <td><input type="text" name="params9" size="125" value="<?php echo $params9; ?>" placeholder="Beror på val av objektiv"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Fokusering</td>
      <td><input type="text" name="params10" size="125" value="<?php echo $params10; ?>" placeholder="Autofokus 15 punkter varav 5 korslagda, manuell fokus"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Närgräns</td>
      <td><input type="text" name="params11" size="125" value="<?php echo $params11; ?>" placeholder="Beror på val av objektiv"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Minnestyp</td>
      <td><input type="text" name="params12" size="125" value="<?php echo $params12; ?>" placeholder="SD,SDHC, SDXC, CompactFlash etc"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Minne som ingår</td>
      <td><input type="text" name="params13" size="125" value="<?php echo $params13; ?>" placeholder=""></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Optisk sökare</td>
      <td><input type="text" name="params14" size="125" value="<?php echo $params14; ?>" placeholder="Pentaprismasökare, 95% visning, 0,82x förstoring"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Bildskärm</td>
      <td><input type="text" name="params15" size="125" value="<?php echo $params15; ?>" placeholder="3,0 tum touchskärm, 1037000 pixels upplösning"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Ljusmätning</td>
      <td><input type="text" name="params16" size="125" value="<?php echo $params16; ?>" placeholder="1024-fälts evaluerande mätning + centrumvägd + spotmätning"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Vitbalans</td>
      <td><input type="text" name="params17" size="125" value="<?php echo $params17; ?>" placeholder="Auto + 6 lägen + custom + färgtemperatur (K)"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Ljuskänslighet</td>
      <td><input type="text" name="params18" size="125" value="<?php echo $params18; ?>" placeholder="ISO 100-12800 (upp till 64-25600 i utökat läge)"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Slutartider</td>
      <td><input type="text" name="params19" size="125" value="<?php echo $params19; ?>" placeholder="30-1/8000s + bulb + time"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Exponering</td>
      <td><input type="text" name="params20" size="125" value="<?php echo $params20; ?>" placeholder="Programautomatik, bländarförval, slutartidsförval, manuell + motivprogram"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Exponeringskompensation</td>
      <td><input type="text" name="params21" size="125" value="<?php echo $params21; ?>" placeholder="+/- 5 steg i 1/3 steg"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Självutlösare</td>
      <td><input type="text" name="params22" size="125" value="<?php echo $params22; ?>" placeholder="2, 10 s + 3 bilder i rad efter 10 s"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Blixt</td>
      <td><input type="text" name="params23" size="125" value="<?php echo $params23; ?>" placeholder="Intern blixt, möjlighet till extern"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Blixtinställningar</td>
      <td><input type="text" name="params24" size="125" value="<?php echo $params24; ?>" placeholder="Balanserad upplättningsblixt, synk på långa slutartider, synk på bakre slutarridån, synk på långa slutartider och bakre slutarridån, "röda ögon"-reduktion, slavstyrning (som master/controller/commander)"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Räckvidd blixt</td>
      <td><input type="text" name="params25" size="125" value="<?php echo $params25; ?>" placeholder="Inbyggd blixt ledtal 12"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Sekvenstagning</td>
      <td><input type="text" name="params26" size="125" value="<?php echo $params26; ?>" placeholder="JPEG: Upp till 10 bilder/s så länge minnet räcker. RAW: Upp till 5 bilder/s, upp till 12 bilder i följd"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Film</td>
      <td><input type="text" name="params27" size="125" value="<?php echo $params27; ?>" placeholder="Full HD 1080/60p/50p/30p/25p, HD 720/30p/25p, 640x480/30 + ev bitrate/kvalitetslägen såsom 24Mbps"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Ljudinspelning</td>
      <td><input type="text" name="params28" size="125" value="<?php echo $params28; ?>" placeholder="Inbyggd stereomikrofon möjlighet till extern stereomikrofon"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Anslutningar</td>
      <td><input type="text" name="params29" size="125" value="<?php echo $params29; ?>" placeholder="USB High speed, HDMI mini C, nätdel (via batteriadapter), mikrofoningång 3,5mm stereoteleplugg, hörlursutgång 3,5mm stereoteleplugg, fjärrkontroll"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Batteri</td>
      <td><input type="text" name="params30" size="125" value="<?php echo $params30; ?>" placeholder="Laddbart litiumjonbatteri NP-60C"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Mått</td>
      <td><input type="text" name="params31" size="125" value="<?php echo $params31; ?>" placeholder="122 x 68 x 43 mm"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Vikt</td>
      <td><input type="text" name="params32" size="125" value="<?php echo $params32; ?>" placeholder="438 g utan batteri och minneskort"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Medföljande programvaror</td>
      <td><textarea rows="4" name="params33" cols="75" placeholder="CD-rom, Super camera utilities solutions, Mega Editor Plus"><?php echo $params33; ?></textarea></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Medföljande tillbehör</td>
      <td valign="top"><textarea rows="4" name="params34" cols="75" placeholder="Li-jonbatteri NP-60C, laddare NC-60Q, USB-kabel I-USB32a, nackrem AH-NS3, kamerahuslock BP-3, skydd för blixtsko FS-1, bruksanvisning"><?php echo $params34; ?></textarea></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Övriga kommentarer</td>
      <td><textarea rows="4" name="params35" cols="75" placeholder=""><?php echo $params35; ?></textarea></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Ditt namn</td>
      <td>
	  <select size="1" name="addcreatedby">
	  <option></option>
	  <?php $banners->getAnstallda(); ?>
	  </select>
      </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <p><input type="submit" value="Spara"></p>
</form>
</div>
<div class="clear"></div>
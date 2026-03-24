<?php
if ($_COOKIE['login_ok'] != "true") {
	echo "<div class=\"container_loggin\">\n";
	echo "<span class=\"not_loggin\">Du är Ej inloggad och kommer därför inte kunna utföra åtgärden!</span>\n";
	echo "</div>\n";
	echo "<div class=\"clear\"></div>\n";
}
?>
<div class="top10"></div>
<div class="hr_gray"></div>
<div class="top10"></div>
<?php
if ($addid != "") {
	echo "<h2>Redigera post - " . $product->getArticleName($addid) . "</h2>\n";
} elseif ($addidc != "") {
	echo "<h2>Kopiera post - " . $product->getArticleName($addidc) . "</h2>\n";
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
      <?php $tech->getActualProductsInCategory("336,1000260"); ?>
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
      <td>Batteri</td>
      <td><input type="text" name="params1" size="50" value="<?php echo $params1; ?>">XXXXmAh</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<!--
    <tr>
      <td>Batteritid</td>
      <td><input type="text" name="params2" size="50" value="<?php echo $params2; ?>">Taltid Upp till XXh Standby Upp till XXXh</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	-->
    <tr>
      <td>Trådlös laddning</td>
      <td><input type="text" name="params22" size="50" value="<?php echo $params22; ?>">Ja eller Nej (samt ev. standard)</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Nätverk</td>
      <td valign="top"><textarea rows="4" name="params3" cols="50"><?php echo $params3; ?></textarea>GSM XXX, XXX, XXX</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Displaytyp</td>
      <td><input type="text" name="params4" size="50" value="<?php echo $params4; ?>">LCD eller TFT</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Displaystorlek</td>
      <td><input type="text" name="params5" size="50" value="<?php echo $params5; ?>">4,7 tum</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Upplösning</td>
      <td><input type="text" name="params6" size="50" value="<?php echo $params6; ?>">1920 x 1080</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Plats för minneskort</td>
      <td><input type="text" name="params7" size="50" value="<?php echo $params7; ?>">Upp till XXGB</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Internt minne för lagring</td>
      <td><input type="text" name="params8" size="50" value="<?php echo $params8; ?>">Upp till XXGB</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Processor</td>
      <td><input type="text" name="params9" size="50" value="<?php echo $params9; ?>">X-Kärnig X,XGHz</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>RAM-minne</td>
      <td><input type="text" name="params10" size="50" value="<?php echo $params10; ?>">XGB</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Blåtand</td>
      <td><input type="text" name="params11" size="50" value="<?php echo $params11; ?>">Ja eller Nej</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Kamera</td>
      <td><input type="text" name="params12" size="50" value="<?php echo $params12; ?>">X Mpix med autofokus</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Video</td>
      <td><input type="text" name="params13" size="50" value="<?php echo $params13; ?>">HD-videoinspelning (1080)</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Operativsystem</td>
      <td><input type="text" name="params14" size="50" value="<?php echo $params14; ?>">Android, Ios, Windows</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Inbyggd FM-radio</td>
      <td><input type="text" name="params15" size="50" value="<?php echo $params15; ?>">Ja eller Nej</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>GPS</td>
      <td><input type="text" name="params16" size="50" value="<?php echo $params16; ?>">Ja eller Nej</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Typ av SIM-kort</td>
      <td><input type="text" name="params17" size="50" value="<?php echo $params17; ?>">Standard eller Micro eller Nano</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Mått</td>
      <td><input type="text" name="params18" size="50" value="<?php echo $params18; ?>">137,4 × 68,2 × 9,3 mm</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Vikt</td>
      <td><input type="text" name="params19" size="50" value="<?php echo $params19; ?>">xxx gram</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">I förpackningen</td>
      <td valign="top"><textarea rows="4" name="params20" cols="50"><?php echo $params20; ?></textarea>Telefon, laddare, stereoheadset,snabbguide</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Övriga kommentarer</td>
      <td valign="top"><textarea rows="4" name="params21" cols="50"><?php echo $params21; ?></textarea>NFC, HTC BoomSound</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <p><input type="submit" value="Spara"></p>
</form>
</div>
<div class="clear"></div>
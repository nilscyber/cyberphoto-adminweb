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
      <?php $tech->getActualProductsInCategory('7,51,52,57,203,213,329,346,402,517,1000236'); ?>
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
      <td>System</td>
      <td><input type="text" name="params1" size="50" value="<?php echo $params1; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>CMOS storlek</td>
      <td><input type="text" name="params2" size="50" value="<?php echo $params2; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>CMOS brutto</td>
      <td><input type="text" name="params3" size="50" value="<?php echo $params3; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Stillbildsläge</td>
      <td><input type="text" name="params4" size="50" value="<?php echo $params4; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Objektiv</td>
      <td><input type="text" name="params5" size="50" value="<?php echo $params5; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Optisk zoom</td>
      <td><input type="text" name="params6" size="50" value="<?php echo $params6; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Digital zoom</td>
      <td><input type="text" name="params7" size="50" value="<?php echo $params7; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Bildstabilisering</td>
      <td><input type="text" name="params8" size="50" value="<?php echo $params8; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Minsta belysning</td>
      <td><input type="text" name="params9" size="50" value="<?php echo $params9; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Fokusering</td>
      <td><input type="text" name="params10" size="50" value="<?php echo $params10; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Filterdiameter</td>
      <td><input type="text" name="params11" size="50" value="<?php echo $params11; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Vitbalans</td>
      <td><input type="text" name="params12" size="50" value="<?php echo $params12; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Digitala effekter</td>
      <td><input type="text" name="params13" size="50" value="<?php echo $params13; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Exponering</td>
      <td><input type="text" name="params14" size="50" value="<?php echo $params14; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Motivprogram</td>
      <td><input type="text" name="params15" size="50" value="<?php echo $params15; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Sökare</td>
      <td><input type="text" name="params16" size="50" value="<?php echo $params16; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Bildskärm</td>
      <td><input type="text" name="params17" size="50" value="<?php echo $params17; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Inbyggd blixt</td>
      <td><input type="text" name="params18" size="50" value="<?php echo $params18; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Minneskort</td>
      <td><input type="text" name="params19" size="50" value="<?php echo $params19; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Inbyggt minne</td>
      <td><input type="text" name="params20" size="50" value="<?php echo $params20; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Högtalare</td>
      <td><input type="text" name="params21" size="50" value="<?php echo $params21; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Tillbehörssko</td>
      <td><input type="text" name="params22" size="50" value="<?php echo $params22; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>AV-utgång</td>
      <td><input type="text" name="params23" size="50" value="<?php echo $params23; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>S-video utgång</td>
      <td><input type="text" name="params24" size="50" value="<?php echo $params24; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>LANC-ingång</td>
      <td><input type="text" name="params25" size="50" value="<?php echo $params25; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>WiFi</td>
      <td><input type="text" name="params26" size="50" value="<?php echo $params26; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>HDMI-utgång</td>
      <td><input type="text" name="params27" size="50" value="<?php echo $params27; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Mikrofoningång</td>
      <td><input type="text" name="params28" size="50" value="<?php echo $params28; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>USB-utgång</td>
      <td><input type="text" name="params29" size="50" value="<?php echo $params29; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Hörlursutgång</td>
      <td><input type="text" name="params30" size="50" value="<?php echo $params30; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Fjärrkontroll</td>
      <td><input type="text" name="params31" size="50" value="<?php echo $params31; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Bruksanvisning</td>
      <td><input type="text" name="params32" size="50" value="<?php echo $params32; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Mått</td>
      <td><input type="text" name="params33" size="50" value="<?php echo $params33; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Vikt</td>
      <td><input type="text" name="params34" size="50" value="<?php echo $params34; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Medföljande programvaror</td>
      <td><textarea rows="4" name="params35" cols="50"><?php echo $params35; ?></textarea></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Medföljande tillbehör</td>
      <td><textarea rows="4" name="params36" cols="50"><?php echo $params36; ?></textarea></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Övriga kommentarer</td>
      <td><textarea rows="4" name="params37" cols="50"><?php echo $params37; ?></textarea></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <p><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>"></p>
</form>
</div>
<div class="clear"></div>
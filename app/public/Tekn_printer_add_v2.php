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
      <?php $tech->getActualProductsInCategory('2,215,236,414,415,449,450,451,706'); ?>
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
      <td>Typ av skrivare</td>
      <td><input type="text" name="params1" size="50" value="<?php echo $params1; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Antal patroner</td>
      <td><input type="text" name="params2" size="50" value="<?php echo $params2; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Pappersstorlek</td>
      <td><input type="text" name="params3" size="50" value="<?php echo $params3; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Utskriftsupplösning</td>
      <td><input type="text" name="params4" size="50" value="<?php echo $params4; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Utskriftshastighet</td>
      <td><input type="text" name="params5" size="50" value="<?php echo $params5; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Wi-Fi</td>
      <td><input type="text" name="params6" size="50" value="<?php echo $params6; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Ljudnivå</td>
      <td><input type="text" name="params7" size="50" value="<?php echo $params7; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Strömförbrukning</td>
      <td><textarea rows="4" name="params8" cols="50"><?php echo $params8; ?></textarea></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Systemkrav</td>
      <td><textarea rows="4" name="params9" cols="50"><?php echo $params9; ?></textarea></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Mått</td>
      <td><input type="text" name="params10" size="50" value="<?php echo $params10; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Vikt</td>
      <td><input type="text" name="params11" size="50" value="<?php echo $params11; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Medföljande programvaror</td>
      <td><input type="text" name="params12" size="50" value="<?php echo $params12; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Medföljande tillbehör</td>
      <td><input type="text" name="params13" size="50" value="<?php echo $params13; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">Övriga kommentarer</td>
      <td><textarea rows="4" name="params14" cols="50"><?php echo $params14; ?></textarea></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <p><input type="submit" value="Spara"></p>
</form>
</div>
<div class="clear"></div>
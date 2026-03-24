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
      <?php $tech->getActualProductsInCategory('42,43,45,50,373,374,375,376,476'); ?>
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
      <td>Typ av objektiv</td>
      <td><input type="text" name="params1" size="50" value="<?php echo $params1; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Fattning</td>
      <td><input type="text" name="params2" size="50" value="<?php echo $params2; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Konstruktion</td>
      <td><input type="text" name="params3" size="50" value="<?php echo $params3; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Brännvidd</td>
      <td><input type="text" name="params4" size="50" value="<?php echo $params4; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Ljusstyrka</td>
      <td><input type="text" name="params5" size="50" value="<?php echo $params5; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Minsta bländare</td>
      <td><input type="text" name="params6" size="50" value="<?php echo $params6; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Närgräns</td>
      <td><input type="text" name="params7" size="50" value="<?php echo $params7; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Avbildningsskala</td>
      <td><input type="text" name="params8" size="50" value="<?php echo $params8; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Filterdiameter</td>
      <td><input type="text" name="params9" size="50" value="<?php echo $params9; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Motljusskydd</td>
      <td><input type="text" name="params10" size="50" value="<?php echo $params10; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Mått</td>
      <td><input type="text" name="params11" size="50" value="<?php echo $params11; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Vikt</td>
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
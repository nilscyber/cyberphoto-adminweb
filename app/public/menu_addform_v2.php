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
	echo "<h2>Redigera post</h2>\n";
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
    <tr>
      <td>Kategori</td>
      <td>
	  <select size="1" name="addByCat">
      <option></option>
      <?php $menu->getMenuKategori(); ?>
      </select>
      </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<tr>
      <td>&nbsp;</td>
      <td colspan="4"><hr noshade color="#999999" size="1"></td>
    </tr>
    <tr>
      <td><img border="0" src="sv_mini.jpg"></td>
      <td><input type="checkbox" name="addActiveSE" value="yes" <?php if ($addActiveSE == -1) { ?> checked <?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Namn</td>
      <td><input type="text" name="addNameSE" size="25" value="<?php echo $addNameSE; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Länk</td>
      <td><input type="text" name="addLincSE" size="50" value="<?php echo $addLincSE; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<tr>
      <td>&nbsp;</td>
      <td colspan="4"><hr noshade color="#999999" size="1"></td>
    </tr>
    <tr>
      <td><img border="0" src="no_mini.jpg"></td>
      <td><input type="checkbox" name="addActiveNO" value="yes" <?php if ($addActiveNO == -1) { ?> checked <?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Namn</td>
      <td><input type="text" name="addNameNO" size="25" value="<?php echo $addNameNO; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Länk</td>
      <td><input type="text" name="addLincNO" size="50" value="<?php echo $addLincNO; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<tr>
      <td>&nbsp;</td>
      <td colspan="4"><hr noshade color="#999999" size="1"></td>
    </tr>
    <tr>
      <td><img border="0" src="fi_mini.jpg"></td>
      <td><input type="checkbox" name="addActiveFI" value="yes" <?php if ($addActiveFI == -1) { ?> checked <?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Namn</td>
      <td><input type="text" name="addNameFI" size="25" value="<?php echo $addNameFI; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Länk</td>
      <td><input type="text" name="addLincFI" size="50" value="<?php echo $addLincFI; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<tr>
      <td>&nbsp;</td>
      <td colspan="4"><hr noshade color="#999999" size="1"></td>
    </tr>
	<tr>
      <td>Sortera</td>
      <td><input type="text" name="addOrder" size="2" value="<?php echo $addOrder; ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Är mellanrum</td>
      <td><input type="checkbox" name="addIsSpacing" value="yes" <?php if ($addIsSpacing == -1) { ?> checked <?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Är publik</td>
      <td><input type="checkbox" name="addShowPublic" value="yes" <?php if ($addShowPublic == -1) { ?> checked <?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Är överliggande</td>
      <td><input type="checkbox" name="addIsParent" value="yes" <?php if ($addIsParent == -1) { ?> checked <?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Lägg under</td>
      <td>
	  <select size="1" name="addParentMenu">
      <option></option>
      <?php $menu->getParentKategori(); ?>
      </select>
      </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <p><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>"></p>
</form>
</div>
<div class="clear"></div>
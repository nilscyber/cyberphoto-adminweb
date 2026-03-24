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
} elseif ($addidc != "") {
	echo "<h2>Kopiera post</h2>\n";
} else {
	if ($section == "") {
		$section = $_SESSION['bannersection'];
	}
	echo "<h2>Lägg till post</h2>\n";
}
if ($addsort == "") {
	$addsort = 0;
}
if ($addcategory == "") {
	$addcategory = 0;
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
  <table border="0" cellpadding="5" cellspacing="3">
    <?php if ($addidc != "") { ?>
    <tr>
      <td>Kopiera till</td>
      <td>
	  <select size="1" name="addsitecopy">
	  <option></option>
	  <option>*** Sverige ***</option>
	  <option value="1">Foto-Video</option>
	  <option value="2">Mobiltelefoni</option>
	  <option value="3">Batterier</option>
	  <option value="4">Outdoor</option>
	  <option></option>
	  <option>*** Norge ***</option>
	  <!--
	  <option value="101">Foto-Video</option>
	  -->
	  <option value="102">Mobiltelefoni</option>
	  <!--
	  <option value="103">Batterier</option>
	  <option value="104">Outdoor</option>
	  -->
	  <option></option>
	  <option>*** Finland ***</option>
	  <!--
	  <option value="201">Foto-Video</option>
	  -->
	  <option value="202">Mobiltelefoni</option>
	  <!--
	  <option value="203">Batterier</option>
	  <option value="204">Outdoor</option>
	  -->
	  <option></option>
	  <option>*** Finland (svenska) ***</option>
	  <!--
	  <option value="301">Foto-Video</option>
	  -->
	  <option value="302">Mobiltelefoni</option>
	  <!--
	  <option value="303">Batterier</option>
	  <option value="304">Outdoor</option>
	  -->
	  </select>
	  </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><a onMouseOver="this.T_WIDTH=350;return escape('<b>Kopiera post till annan frontsida!</b><br><br>Om du kopiera post till annan frontsida och den är aktiv så visas den omgående.<br><br>Var därför mycket noggrann med att Allt är korrekt. Framförallt bilder och bildlänkar är känsliga.')"><b>OBS! Läs detta!</b></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td>Gäller från <font color="#FF0000">*</font></td>
      <td><input type="text" name="addfrom" size="20" value="<?php if ($addfrom == "") { echo date("Y-m-d H:i:s", time()); } else { echo $addfrom; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
      <td>&nbsp;</td>
      <td>Gäller till <b><font color="#FF0000">*</font></b></td>
      <td><input type="text" name="addto" size="20" value="<?php if ($addto == "") { echo date("Y-m-d 23:59:59",mktime(0,0,0,date("n")+1,1-1,date("Y"))); } else { echo $addto; }  ?>" style="font-family: Verdana; font-size: 8pt"></td>
    </tr>
    <tr>
      <td>Sektion <b><font color="#FF0000">*</font></b></td>
      <td><input type="text" name="addsection" value="<?php echo $section; ?>" size="5" style="font-family: Verdana; font-size: 8pt<?php if ($change != "") { ?>; background-color: #CCCCCC<?php } ?>"<?php if ($change != "") { ?> onFocus="this.blur()"<?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <?php if ($change != "") { ?>
      <td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?change=<?php echo $addid; ?>&now=yes">Avsluta tiden som är nu</a></td>
      <?php } else { ?>
      <td>&nbsp;</td>
      <?php } ?>
    </tr>
    <tr>
      <td>Bild <b><font color="#FF0000">*</font></b></td>
      <td><input type="text" name="addpicture" size="20" value="<?php echo $addpicture; ?>" style="font-family: Verdana; font-size: 8pt"></td>
      <td>&nbsp;</td>
      <td>Art nr</td>
      <td><input type="text" name="addartnr" size="20" value="<?php echo $addartnr; ?>" style="font-family: Verdana; font-size: 8pt"></td>
    </tr>
    <tr>
      <td>Länk</td>
      <td colspan="4"><input type="text" name="addlinc" size="62" value="<?php echo $addlinc; ?>" style="font-family: Verdana; font-size: 8pt"></td>
    </tr>
    <tr>
      <td>Tillåt slut i lager</td>
      <td><input type="checkbox" name="addstore" value="yes" <?php if ($addstore == -1) { ?> checked <?php } ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Prioriterad</td>
      <td><input type="checkbox" name="addprio" onclick="javascript:alertPrio()" value="yes" <?php if ($addprio == -1) { ?> checked <?php } ?><?php if ($change != "") echo " disabled";?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<tr>
      <td>Sortera</td>
      <td colspan="4"><input type="text" name="addsort" size="2" value="<?php echo $addsort; ?>" style="font-family: Verdana; font-size: 8pt">&nbsp;gäller endast sektion vänster/höger, 0 är default</td>
    </tr>
	<tr>
      <td>Kategori</td>
      <td colspan="4"><input type="text" name="addcategory" size="2" value="<?php echo $addcategory; ?>" style="font-family: Verdana; font-size: 8pt">&nbsp;gäller endast banners i prislista, 0 är default</td>
    </tr>
  </table>
  <p><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till<?php } ?>" name="skicka" style="font-family: Verdana; font-size: 8pt; color: #000000; font-weight: bold; background-color: #C0C0C0"></p>
</form>
</div>
<div class="clear"></div>
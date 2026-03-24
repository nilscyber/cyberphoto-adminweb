<p><b><font face="Verdana" size="1">Välj vilken front du skall jobba med</font></b></p>
<table border="0" cellpadding="5" cellspacing="0" style="border: 1px solid #008080; background-color: #CCCCCC" width="480">
  <tr>
    <td class="flagshoose"><img border="0" src="sv.jpg"></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 1) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 1) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=1"><?php } ?>FOTO</a></b></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 6) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 6) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=6"><?php } ?>MOBIL</a></b></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 10) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 10) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=10"><?php } ?>CYBAIRGUN</a></b></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 11) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 11) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=11"><?php } ?>HOBBY</a></b></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 13) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 13) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=13"><?php } ?>HUSHÅLL</a></b></td>
  </tr>
  <tr>
    <td class="flagshoose"><img border="0" src="fi.jpg"></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 2) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 2) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=2"><?php } ?>FOTO</a></b></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 7) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 7) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=7"><?php } ?>MOBIL</a></b></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 14) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 14) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=14"><?php } ?>CYBAIRGUN</a></b></td>
    <td class="flagshoose"></td>
    <td class="flagshoose"></td>
  </tr>
  <tr>
    <td class="flagshoose"><img border="0" src="fisv.jpg"></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 3) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 3) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=3"><?php } ?>FOTO</a></b></td>
    <td class="flagshoose"></td>
    <td class="flagshoose"></td>
    <td class="flagshoose"></td>
    <td class="flagshoose"></td>
  </tr>
  <tr>
    <td class="flagshoose"><img border="0" src="no.jpg"></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 30) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 30) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=30"><?php } ?>FOTO</a></b></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 31) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 31) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=31"><?php } ?>MOBIL</a></b></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 32) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 32) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=32"><?php } ?>CYBAIRGUN</a></b></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 33) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 33) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=33"><?php } ?>HOBBY</a></b></td>
    <td class="flagshoose"><?php if ($_SESSION['adminsite'] == 34) { echo "<b>"; } ?><?php if ($_SESSION['adminsite'] != 34) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=34"><?php } ?>HUSHÅLL</a></b></td>
  </tr>
  <?php if ($_SESSION['adminsite'] != "") { ?>
  <tr>
    <td colspan="6" class="flagshoose"><b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?nollstall=yes">NOLLSTÄLL</a></b></td>
  </tr>
  <?php } ?>
</table>
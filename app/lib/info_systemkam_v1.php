<div id="stdlist">
<div class="roundtop">
<div class="infor1"></div>
<div class="infor2"></div>
<div class="infor3"></div>
<div class="infor4"></div>
</div>

<div class="content">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td align="center" width="33%">
    <a href="/produktarkivet?show=4000">
    <?php if ($fi && !$sv) { ?>- Poistuneet mallit<?php } else { ?>- Utgångna modeller<?php } ?></a></td>
    <td align="center" width="33%">
    <?php if ($fi && !$sv) { ?>
    <a href="/compare_fi.php">
    <?php } elseif ($fi && $sv) { ?>
    <a href="/compare_fi_se.php">
    <?php } else { ?>
    <a href="/compare.php">
    <?php } ?>
    <?php if ($fi && !$sv) { ?>- Vertaa kamerat<?php } else { ?>- Jämför kameror<?php } ?></a></td>
    <td align="center" width="33%">
    <?php if ($fi && !$sv) { ?>
    <a href="/faq/tester_fi.php">
    <?php } elseif ($fi && $sv) { ?>
    <a href="/faq/tester_fi_se.php">
    <?php } else { ?>
    <a href="/faq/tester.php">
    <?php } ?>
    <?php if ($fi && !$sv) { ?>- Testeistämme<?php } else { ?>- Om våra tester<?php } ?></a></td>
  </tr>
</table>

</div>

<div class="roundbottom">
<div class="infor4"></div>
<div class="infor3"></div>
<div class="infor2"></div>
<div class="infor1"></div>
</div>
</div>
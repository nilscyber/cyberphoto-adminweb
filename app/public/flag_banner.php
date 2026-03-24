<p><b><font face="Verdana" size="1">Välj vilken sida du skall jobba med med</font></b></p>
<table border="0" cellpadding="3" cellspacing="0" style="border-collapse: collapse; border: 1px solid #008080; background-color: #CCCCCC" bordercolor="#111111">
  <tr>
    <td><?php if ($_SESSION['adminsite_banner'] != 500) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=500"><?php } ?><img border="0" src="kamera.jpg"></a></td>
    <td><?php if ($_SESSION['adminsite_banner'] != 501) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=501"><?php } ?><img border="0" src="video.jpg"></a></td>
    <td><?php if ($_SESSION['adminsite_banner'] != 502) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=502"><?php } ?><img border="0" src="kompakt.jpg"></a></td>
    <td><?php if ($_SESSION['adminsite_banner'] != 503) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=503"><?php } ?><img border="0" src="battery.png"></a></td>
    <td><?php if ($_SESSION['adminsite_banner'] != 504) { ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose_site=504"><?php } ?><img border="0" src="filmjul.png"></a></td>
    <?php if ($_SESSION['adminsite_banner'] != "") { ?>
    <td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?nollstall=yes"><img border="0" src="nollstall.jpg"></a></td>
    <?php } ?>
  </tr>
</table>
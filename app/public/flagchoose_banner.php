<font face="Arial" size="3"><b>Aktiv sida</b></font><br>
<?php if ($_SESSION['adminsite_banner'] == 500) { ?>
<img border="0" src="kamera_full.jpg">
<?php } elseif ($_SESSION['adminsite_banner'] == 501) { ?>
<img border="0" src="video_full.jpg">
<?php } elseif ($_SESSION['adminsite_banner'] == 502) { ?>
<img border="0" src="kompakt_full.jpg">
<?php } elseif ($_SESSION['adminsite_banner'] == 503) { ?>
<img border="0" src="battery_full.png">
<?php } elseif ($_SESSION['adminsite_banner'] == 504) { ?>
<img border="0" src="filmjul_full.png">
<?php } else { ?>
Inget val
<?php } ?>


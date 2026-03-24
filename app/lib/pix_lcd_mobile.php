<table border="0" width="85" cellpadding="0" cellspacing="2">
  <tr>
    <td><img border="0" src="picinfo/onepix.jpg" width="1" height="1"></td>
  </tr>
  <?php if ($motljsk != "") { ?>
  <tr>
    <td style="border: 1px solid #C0C0C0; background-color: #F2F2F2" align="center"><font face="Verdana" size="1"><b>
    <?php if ($fi && !$sv): ?>Näyttö<?php else: ?>Skärm<?php endif; ?><br>
    </b><?php echo $motljsk; ?></font></td>
  </tr>
  <?php } ?>
  <?php if ($ccd != "") { ?>
  <tr>
    <td style="border: 1px solid #C0C0C0; background-color: #F2F2F2" align="center"><font face="Verdana" size="1"><b>Kamera<br>
    </b><?php echo number_format($ccd / 1000000, 1). " Mpix"; ?></font></td>
  </tr>
  <?php } ?>
</table>
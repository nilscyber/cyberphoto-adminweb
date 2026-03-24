<div align="center">
  <center>
<table border="0" cellpadding="5" cellspacing="1" width="93%">
  <?php if (count($articles) < 1) { ?>
  <?php if (!$sv && $fi) { ?>
  <tr>
    <td><font face="Arial" size="2"><b>Ei tuotteita näkyvissä.</b></font></td>
  </tr>
  <?php } else { ?>
  <tr>
    <td><font face="Arial" size="2"><b>Inga produkter visas just nu.</b></font></td>
  </tr>
  <?php } ?>
  <?php } else { ?>
  <tr>
    <td><font face="Verdana" size="1">
    <?php if (!$sv && $fi) { ?>
    Etsintäsi tuotti <b><?php echo count($articles); ?></b> osumaa
    <?php } else { ?>
    Antal produkter <b><?php echo count($articles); ?></b> st
    <?php } ?>    
    </font></td>
  </tr>
  <?php } ?>
</table>
  </center>
</div>
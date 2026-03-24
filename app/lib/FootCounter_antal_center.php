<div align="center">
  <center>
<table border="0" cellpadding="5" cellspacing="1" width="93%">
  <?php if (count($articles) < 1) { ?>
	  <?php if (!$sv && $fi) { ?>
	  <tr>
		<td style="border: 1px solid #85000D; background-color: #F7F7F4"><font face="Arial" size="2"><b>Oho, tämä yhdistelmä ei löydy.</b></font></td>
	  </tr>
	  <?php } else { ?>
	  <tr>
		<td style="border: 1px solid #3399FF; background-color: #F7F7F4"><font face="Arial" size="2"><b>Ooops, tyvärr finns inte denna kombination.</b></font></td>
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
<?php
          if ($numberpac == "") {
	  
	  	if (eregi("pac$", $article)) {
	  
		$artnr = $article1;
	  
	  } else {
	  
	  	$artnr = $article . "pac";
	  
	  	}
	  }
?>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="14"><img src="/kopknapp/11.gif" border=0 width="14" height="14"></td>
    <td	width="300" background="/kopknapp/22.gif" border=0></td>
    <td width="16"><img src="/kopknapp/33.gif" border=0 width="16" height="14"></td>
</tr>

<tr>
    <td valign="top" background="/kopknapp/44.gif">&nbsp;</td>
    <td background="/kopknapp/55.gif">

<?php
require_once("CBasket.php");
$bask = new CBasket();
?>

<div align="center">
  <center>
	<table border="0" cellpadding="2" width="430" cellspacing="0">
	<tr>
	<td colspan="3"><b><font color="#2B2B2B" face="Arial" size="3"><?php if ($fi && !$sv) { ?>Paketin sisältö<?php } else { ?>Paketdetaljer<?php } ?></font></td>
	<td align="right"><span onclick="show_hide('<?php echo $artnr; ?>');" style="cursor:pointer;"><img border="0" title="<?php if ($fi && !$sv): ?>Sulje ikkuna<?php else: ?>Dölj fönstret<?php endif; ?>" src="/pic/kryss_l.gif"></td>
	</tr>
	<tr>
		  <td>&nbsp;</td>
		  <td><b><font color="#FFFFFF" face="Verdana, Arial" size="1"><?php if ($fi && !$sv) { ?>Tuotteen nimi<?php } else { ?>Varans namn<?php } ?></font></b></td>
		  <td><font color="#2B2B2B" face="Verdana, Arial" size="1"><b><?php if ($fi && !$sv) { ?>määrä<?php } else { ?>Antal<?php } ?></b></font></td>
		  <td><font color="#2B2B2B" face="Verdana, Arial" size="1"><b><?php if ($fi && !$sv) { ?>varaston&nbsp;tila<?php } else { ?>Lagerstatus<?php } ?></b></font></td>
	</tr>        
          
          <?php $bask->viewPacketDeliveryPictureNew($artnr, '1', $fi); ?>
          
      </table>
  </center>
</div>
<!--
<p align="center"><span onclick="show_hide('<?php echo $artnr; ?>');" style="cursor:pointer;"><b><font color="black"><img border="0" src="/pic/kryss.gif"> Dölj fönstret</font></b></span></p>
-->
    </td>
    <td valign="top" background="/kopknapp/66.gif">&nbsp;</td>
</tr>

<tr>
    <td><img src="/kopknapp/77.gif" border=0 width="14" height="16"></td>
    <td background="/kopknapp/88.gif"></td>
    <td><img src="/kopknapp/99.gif" border=0 width="16" height="16"></td>
</tr>
</table>

<?php
session_start();
include_once(("CConnect.php"));
require_once("CBasket.php");

$bask = new CBasket();

$rows = $bask->getArticleInfoBuy($article);

$beskrivning = ereg_replace ('`', '"', $rows->beskrivning);
$utpris = $rows->utpris;
$utpris_moms = $rows->utpris + $rows->utpris * $rows->momssats;
$valuta = "kr";
$tillverkare = $rows->tillverkare;
?>

<table width="310" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="14"><img src="/kopknapp/1.gif" border=0 width="14" height="14"></td>
    <td	width="280" background="/kopknapp/2.gif" border=0></td>
    <td width="16"><img src="/kopknapp/3.gif" border=0 width="16" height="14"></td>
</tr>

<tr>
    <td valign="top" background="/kopknapp/4.gif">&nbsp;</td>
    <td background="/kopknapp/5.gif">
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td colspan="2"><span onclick="show_hide('koplatshantering');" style="cursor:pointer;"><font face="Verdana" size="2" color="#0000FF"><b>Boka en köplats!</b></font></span></td>
      </tr>
      <tr>
        <td colspan="2"><hr noshade color="#85000D" size="1"></td>
      </tr>
      <tr>
        <td><span onclick="show_hide('koplatshantering');" style="cursor:pointer;"><b><font face="Verdana, Arial" size="1"><?php if ($tillverkare != ".") echo $tillverkare . " "; echo $beskrivning; ?></b></(font></span></td>
        <td align="right"><span onclick="show_hide('koplatshantering');" style="cursor:pointer;">(klicka här för mer info)</span></td>
      </tr>
      <tr>
        <td colspan="2"><hr noshade color="#85000D" size="1"></td>
      </tr>
      <tr>
        <!-- <td><a href="javascript:modify	Items('<?php echo $artnr2; ?>')"><img src="/pic/11.gif" border="0" title="Klicka här för att lägga till i kundvagnen"></a></td> -->
        <td><a href="/confirm_koplats.php?ID=<? echo session_id(); ?>&article=<?php echo $article; ?>"><img src="/pic/11_boka.gif" border="0" title="Klicka här för att lägga till i kundvagnen"></a></td>
        <td align="right"><b><font face="Verdana, Arial" size="2" color="85000D"><?php echo number_format($utpris_moms, 0, ',', ' ') . " " . $valuta; ?></b></td>
      </tr>
    </table>

    </td>
    <td valign="top" background="/kopknapp/6.gif">&nbsp;</td>
</tr>

<tr>
    <td><img src="/kopknapp/7.gif" border=0 width="14" height="16"></td>
    <td background="/kopknapp/8.gif"></td>
    <td><img src="/kopknapp/9.gif" border=0 width="16" height="16"></td>
</tr>
</table>
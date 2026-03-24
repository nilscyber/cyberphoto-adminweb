<?php
include_once(("CConnect.php"));
require_once("CBasket.php");

$bask = new CBasket();

$rows = $bask->getArticleInfoBuy("forsakring");

$beskrivning = ereg_replace ('`', '"', $rows->beskrivning);
$tillverkare = $rows->tillverkare;
?>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="14"><img src="/kopknapp/1.gif" border=0 width="14" height="14"></td>
    <td	width="280" background="/kopknapp/2.gif" border=0></td>
    <td width="16"><img src="/kopknapp/3.gif" border=0 width="16" height="14"></td>
</tr>

<tr>
    <td valign="top" background="/kopknapp/4.gif">&nbsp;</td>
    <td background="/kopknapp/5.gif">
    
    <table border="0" cellpadding="0" cellspacing="0" width="430">
      <tr>
        <td><b><font face="Verdana, Arial" size="3"><?php if ($tillverkare != ".") echo $tillverkare . " "; echo $beskrivning; ?></b></(font></td>
        <td align="right"><span onclick="show_hide('mobilforsakring_info');" style="cursor:pointer;"><img border="0" title="Dölj fönstret" src="/pic/kryss_l.gif"></td>
      </tr>
      <tr>
        <td colspan="2">Här kommer info........</td>
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
<div align="center"><center>
<table onmouseout="ClearHiLite();" border="0" cellpadding="2" width="93%" style="border-collapse: collapse; border: 1px solid #CCCCCC" cellspacing="1" bgcolor="#F7F7F4">
<tr>

<% if ($_SESSION['RememberPicture'] == 1) { %>
<td valign="bottom" width="35"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;</font></td>
<% } %>

<td valign="bottom" width="500"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;</font></td>

<td align="right" valign="bottom" width="100"><font face="Verdana, Arial" size="1"><b>Företagshyra</b></font></td>

<% If ($_SESSION['RememberMoms'] == 1) { %>
<td align="right" valign="bottom" width="100"><font face="Verdana, Arial" color="#D90000" size="1"><b>Pris&nbsp;utan&nbsp;moms</b></font></td>
<% } else { %>
<td align="right" valign="bottom" width="100"><font face="Verdana, Arial" size="1"><b>Pris&nbsp;med&nbsp;moms</b></font></td>
<% } %>
<td align="center" valign="bottom" width="35">&nbsp;&nbsp;</td>
<td align="center" valign="bottom" width="20">&nbsp;&nbsp;</td>
</tr>

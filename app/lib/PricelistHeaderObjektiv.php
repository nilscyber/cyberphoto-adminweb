<div align="left"><left>
<table onmouseout="ClearHiLite();" border="0" cellpadding="2" width="99%" style="border-collapse: collapse; border: 1px solid #CCCCCC" cellspacing="1" bgcolor="#F7F7F4">
<tr>

<td valign="bottom"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;</font></td>
<% if ($tested == "yes") { %>
<td align="right" valign="bottom"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;</font></td>
<% } %>
<td align="left" valign="bottom"><font face="Verdana, Arial" size="1"><b><% if ($sv): %>Filter&nbsp;Ø<% else: %>Suodatinkoko<% endif; %></b></font></td>
<td align="left" valign="bottom"><font face="Verdana, Arial" size="1"><b><% if ($sv): %>Motljusskydd<% else: %>Vastavalosuoja<% endif; %></b></font></td>
<% If ($_SESSION['RememberMoms'] == 1) { %>
<td align="right" valign="bottom"><font face="Verdana, Arial" size="1"><b><% if ($sv): %>Pris&nbsp;utan&nbsp;moms<% else: %>Hinta&nbsp;alv&nbsp;0%<% endif; %></b></font></td>
<% } else { %>
<td align="right" valign="bottom"><font face="Verdana, Arial" size="1"><b><% if ($sv): %>Pris&nbsp;med&nbsp;moms<% else: %>Hinta&nbsp;alv&nbsp;24%<% endif; %></b></font></td>
<% } %>
<td width="35" align="center" valign="bottom">&nbsp;&nbsp;</td>
</tr>

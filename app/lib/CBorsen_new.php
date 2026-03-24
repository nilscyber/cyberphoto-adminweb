<%

$conn_borsen = Db::getConnectionDb('cyberborsen');

function countAdd($kategori_id) {

$select = "SELECT id FROM saljes WHERE kategori_id = '$kategori_id' ";
$res = mysqli_query($select);
if ($res)
return mysqli_num_rows($res);

}

function show_adds($type, $kategori_id, $search, $antal) {
if ($type == 'saljes') {
%>

<table border="0" cellpadding="2" width="100%" cellspacing="1">

  <tr>

    <td width="10%" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><b>Datum:</b></font></small>
    </td>
    <td width="40%" bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><b>Vara:</b></font></small>
    </td>
    <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><b>Pris:</b></font></small>
    </td>
    <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><b>Ägare:</b></font></small>
    </td>
    <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><b>Telefon:</b></font></small>
    </td>
  </tr>

<%

$select = "SELECT datum, vara, pris, agare, email, tel ";
$select .= "FROM saljes WHERE vara like '%$search%' && type='1' ";
$select .= "&& kategori_id=$kategori_id ";
$select .= "ORDER BY id DESC ";
$count = 0;
$res = mysqli_query($select);
while ($r = mysqli_fetch_array($res))
{
$count++;
if ($antal <> "alla") {
	if ($count >= $antal)
		break;
}
extract($r);
%>
<tr>

<td bgcolor="#ECECE6" valign="top">
<small><small><font face="Verdana, Arial">
<% echo $datum; %>
</small></small></font></td>

<td bgcolor="#ECECE6" valign="top">
<small><small><font face="Verdana, Arial">
<% echo $vara; %>
</small></small></font></td>

<td bgcolor="#ECECE6" valign="top">
<small><small><font face=\"Verdana, Arial\">
<% echo $pris; %> &nbsp;&nbsp;
</small></small></font></td>

<td bgcolor="#ECECE6" valign="top">
<small><small><font face="Verdana, Arial">
<%
  if ($email != "")
    { echo "<A HREF=\"mailto:$email\">"; }
    if ($agare != "" )
    {
    echo "$agare";
    }
    else
    { echo "&nbsp;&nbsp"; }

  if ($email != "")
    { echo "</A>"; }
%>
</small></small></font></td>

<td bgcolor="#ECECE6" valign="top">
<small><small><font face="Verdana, Arial"><% echo $tel; %> &nbsp;&nbsp;
</small></small></font></td>

</tr>
<%
}

%>
</table>



<%
}

else  {

%>

<p align="center"><img src="../pic/10.gif" width="10" height="48" alt="10.gif (93 bytes)"><a
name="kopes"><small><small><font face="Verdana, Arial"><b>KÖPES:</b></font></small></small></a></p>
<div align="center"><center>


<font color="#2B2B2B" size="1" face="Verdana, Arial">

<table border="0" cellpadding="2" width="85%" cellspacing="1">
  <tr>
    <td width="10%" bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><b>Datum:</b></font></small>
    </td>
    <td width="40%" bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><b>Intresserad av:</b></font></small>
    </td>
    <td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><b>Namn:</b></font></small>
    </td>
    <td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><b>Telefon:</b></font></small></small>
    </td>
  </tr>

<%

# Hämta namnet på tillverkaren
$select = "SELECT datum, vara, agare, email, tel ";
$select .= "FROM saljes WHERE vara like '%$search%' && kategori_id=$kategori_id && type='2' ";
$select .= "ORDER BY time DESC";

$res = mysqli_query($select);

while ($r = mysqli_fetch_array($res))

{

if ($antal <> "alla") {
	if ($count >= $antal)
		break;
}


extract($r);
%>
<tr>

<td bgcolor="#ECECE6" valign="top">
<small><small><font face="Verdana, Arial">
<% echo $datum; %> &nbsp;&nbsp;
</small></small></font></td>

<td bgcolor="#ECECE6" valign="top">
<small><small><font face="Verdana, Arial">
<% echo $vara; %> &nbsp;&nbsp;
</small></small></font></td>

<td bgcolor="#ECECE6" valign="top">
<small><small><font face="Verdana, Arial">

<%
  if ($email != "")
    { print "<A HREF=\"mailto:$email\">"; }

  print "$agare";

  if ($email != "")
    { print "</A>"; }
  print "&nbsp;&nbsp;";
  print "</small></small></font></td>\n";

  print "<td bgcolor=\"#ECECE6\" valign=\"top\">";
  print "<small><small><font face=\"Verdana, Arial\">";
  print "$tel &nbsp;&nbsp;";
  print "</small></small></font></td>\n";

  print "</tr>\n";
}

%>

</table>
</center></div>

<% } 
}

%>
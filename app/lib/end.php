<hr noshade color="#999999" width="93%" size="1">
<?php
include ("CConnect.php");

    $select  = "SELECT blogtype, beskrivning, skapad FROM blog where offentlig = -1 AND blogType IN(13,14) AND NOT (beskrivning IS NULL) AND skapad > now() ORDER BY skapad DESC";

	$res = mysqli_query($select);

	while ($row = mysqli_fetch_array($res)) {

		extract ($row);

		$beskrivning = eregi_replace("\n", "<br>", $beskrivning);

		if ($blogtype == 13) {
		echo "<font size='1' face='verdana' color='#000000'><b>" .$beskrivning. "</b><br><br></font>";
		}
		if ($blogtype == 14) {
		echo "<font size='1' face='verdana' color='#85000D'><b>" .$beskrivning. "</b><br><br></font>";
		}

	}

?>
<div align="center">
  <center>
  <table border="0" cellpadding="2" cellspacing="0" width="93%">
    <tr>
      <td width="100%" align="center">
      <font color="#666666" face="Verdana" size="1"><b>CyberPhoto AB</b>, Box 
      1226 | 901 22 Umeå | Tel 090-200 70 00</font></td>
    </tr>
    <tr>
      <td width="100%" align="center">
      <font face="Verdana" size="1" color="#666666">
      <a style="text-decoration: none" href="/contact.php"><font color="#666666">Kontakta oss</font>
      <img border="0" src="../mailto1.gif" width="12" height="8"></a> 
      | <a style="text-decoration: none" onMouseOver="return escape('<% include("oppet.php"); %>')"><font face="Verdana" size="1" color="#666666">Våra öppettider</font></a> 
      | <a style="text-decoration: none" href="../faq/cookies.php">
      <font color="#666666">Om cookies</font></a> | <a style="text-decoration: none" href="../copyright.php"><font color="#666666">Copyright</font></a></td>
    </tr>
  </table>
  </center>
</div>
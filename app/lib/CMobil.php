<?php

include("connections.php");

Class CMobil {
	var $conn_my;

function __construct() {
	global $fi;
	$this->conn_my = Db::getConnection();
}

function getProductMobil($block,$katID) {

	$select  = "SELECT * FROM ( ";
	$select .= "SELECT artnr, beskrivning, bild, kortinfo, tillverkare, Artiklar.tillverkar_id, Artiklar.kategori_id, Kategori.kategori, lagersaldo, utpris ";
	$select .= "FROM Artiklar LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
	$select .= "WHERE ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) AND (demo=0 OR lagersaldo > 0) ";
	if ($block == 1) {
		$select .= "AND Artiklar.spec15 = -1 ";
	} elseif ($block == 2) {
		$select .= "AND Artiklar.spec16 = -1 ";
	} elseif ($block == 3) {
		$select .= "AND Artiklar.spec17 = -1 ";
	} elseif ($block == 4) {
		$select .= "AND Artiklar.spec18 = -1 ";
	} elseif ($block == 5) {
		$select .= "AND Artiklar.spec19 = -1 ";
	} elseif ($block == 6) {
		$select .= "AND Artiklar.spec20 = -1 ";
	}
	
	$select .= "AND Artiklar.kategori_id IN($katID) ";
	$select .= "AND lagersaldo > 0 ";
	$select .= "ORDER BY lagersaldo DESC ";
	$select .= "LIMIT 10) ";
	$select .= "AS tmp ORDER BY RAND() LIMIT 1 ";
	
	$res = mysqli_query($select);
	
	$num_rows = mysqli_num_rows($res);
	
	if ($num_rows > 0) {

	extract(mysqli_fetch_array($res));

	$utprismoms = number_format(($utpris + $utpris * 0.25), 0, ',', ' ');

	if ($tillverkar_id == 29) {
		$loggo = "samsung.jpg";
	} elseif ($tillverkar_id == 112) {
		$loggo = "sandisk.jpg";
	} elseif ($tillverkar_id == 176) {
		$loggo = "jabra.jpg";
	} elseif ($tillverkar_id == 177) {
		$loggo = "nokia.jpg";
	} elseif ($tillverkar_id == 154) {
		$loggo = "lg.jpg";
	} elseif ($tillverkar_id == 201) {
		$loggo = "se.jpg";
	} elseif ($tillverkar_id == 272) {
		$loggo = "magellan.jpg";
	} elseif ($tillverkar_id == 242) {
		$loggo = "garmin.jpg";
	} elseif ($tillverkar_id == 244) {
		$loggo = "tomtom.jpg";
	} elseif ($tillverkar_id == 256) {
		$loggo = "htc.jpg";
	} elseif ($tillverkar_id == 324) {
		$loggo = "mio.jpg";
	} elseif ($tillverkar_id == 328) {
		$loggo = "igrip.jpg";
	} else {
		$loggo = "noimage";
	}
	
?>
	<table border="0" cellpadding="0" cellspacing="0" width="155" height="215">
	  <tr>
	    <td align="center">
	    <% if ($loggo != "noimage") { %>
	    <img border="0" src=logo/<% echo $loggo; %>>
	    <% } else { %>
		<b><font face="Arial" size="2"><% echo $tillverkare; %></font></b>
	    <% } %>
	    </td>
	  </tr>
	  <tr>
	    <td align="center" style="border-collapse: collapse; background-image: url('/thumbs/xxlarge/bilder/<% echo $bild; %>'); background-repeat: no-repeat; background-position: center">
	    <a onmouseover="return escape('<b><% echo $tillverkare ." " . $beskrivning; %></b><% if ($kortinfo != "") { %><br><br><% echo $kortinfo; %><% } %><br><br>Pris: <b><% echo $utprismoms; %> kr</b> inkl. moms')" href="../info_mobil.php?article=<% echo $artnr; %>">
	    <img border="0" src=link.gif></a>
	    </td>
	  </tr>
	  <tr>
	    <td valign="bottom">

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr>
	    <td align="left"><b>&nbsp;<% echo $utprismoms; %>&nbsp;kr</b></td>
	    <td align="right">

		<%
			print "<a href=\"javascript:modifyItems('$artnr')\">";
			print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/01.gif\" border=0>";
			print "</a>";
		%>
	    
	    </td>
	  </tr>
	</table>

	    </td>
	  </tr>
	</table>	

<?php
	} else {
?>
	
	<table border="0" cellpadding="0" cellspacing="0" width="140" height="109">
	  <tr>
	    <td><img border="0" src="ehandlare2009_empty.jpg"></td>
	  </tr>
	</table>

<?php

	}

}

function getMobilBlogg() {

    $select  = "SELECT cnt, blogType, titel, link FROM blog where offentlig = -1 AND blogType IN(9,10,11) OR cnt IN(2279,2281,2399,2422,2730,2927,3105) AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) ORDER BY skapad DESC LIMIT 8";	

	$res = mysqli_query($select);

	while ($row = mysqli_fetch_array($res)) {

		extract ($row);

	if ($row["blogType"] == 1 || $row["blogType"] == 2 || $row["blogType"] == 9 || $row["blogType"] == 10) {
	
	 	$link = eregi_replace("\?info", "info", $link);
	 	$link = eregi_replace("http://www.cyberphoto.se/", "", $link);

	  	if ($row["link"] != "") {
	  		$link = $link;
	  		} else {
	  		$link = "news.php?ID=" .$row["cnt"];
	  		}
	 } else {
	  	$link = "news.php?ID=" .$row["cnt"];
	  	}

		echo "<a href=\"../".$link."\">$titel</a><br>";
	}

}

}
?>

<?php

include("connections.php");

Class CiPhone {
	var $conn_my; 

function __construct() {
	global $fi;
        
	$this->conn_my = Db::getConnection();

}

function getProduct($block,$katID) {
	global $fi, $sv;

	$select  = "SELECT * FROM ( ";
	$select .= "SELECT artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, bild, kortinfo, tillverkare, Artiklar.tillverkar_id, Artiklar.kategori_id, Kategori.kategori, lagersaldo, Artiklar.utpris, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi ";
	$select .= "FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
        $select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
        $select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
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
	$select .= "AND lagersaldo > 0 AND NOT (demo = -1) ";
	$select .= "ORDER BY lagersaldo DESC ";
	$select .= "LIMIT 10) ";
	$select .= "AS tmp ORDER BY RAND() LIMIT 1 ";
	
	$res = mysqli_query($select);
	
	$num_rows = mysqli_num_rows($res);
	
	if ($num_rows > 0) {

	extract(mysqli_fetch_array($res));

	if ($fi && !$sv) {
		$link = "../info_fi.php?article=";
	} elseif ($fi && $sv) {
		$link = "../info_fi_se.php?article=";
	} else {
		$link = "../info.php?article=";
	}

	if ($tillverkare != ".") {
		if ($fi && !$sv) {
			$produktbeskrivning = $tillverkare ." " . $beskrivning_fi;
		} else {
			$produktbeskrivning = $tillverkare ." " . $beskrivning;
		}
	} else {
		if ($fi && !$sv) {
			$produktbeskrivning = $beskrivning_fi;
		} else {
			$produktbeskrivning = $beskrivning;
		}
	}

	if ($fi && !$sv) {
		$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
	} else {
		$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;kr";
	}

?>
	<table border="0" cellpadding="0" cellspacing="0" width="160" height="160">
	  <tr>
	    <td align="center" style="border-collapse: collapse; background-image: url('/thumbs/xlarge/bilder/<% echo $bild; %>'); background-repeat: no-repeat; background-position: center">
	    <a onmouseover="return escape('<b><% echo $produktbeskrivning; %></b><% if ($kortinfo != "") { %><br><br><% echo $kortinfo; %><% } %><br><br>Pris: <b><% echo $utprismoms; %></b>')" href="<% echo $link; %><% echo $artnr; %>">
	    <img border="0" src="/mobil/link.gif"></a>
	    </td>
	  </tr>
	  <tr>
	    <td valign="bottom">

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr>
	    <td align="left"><p class="pris">&nbsp;&nbsp;<% echo $utprismoms; %></p></td>
	    <td align="right">

		<%
			print "<a href=\"javascript:modifyItems('$artnr')\">";
			if ($fi && !$sv) {
				print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/11_fi.gif\" border=0>";
			} else {
				print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/11.gif\" border=0>";
			}
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
	    <td><img border="0" src="/mobil/ehandlare2009_empty_iphone.jpg"></td>
	  </tr>
	</table>

<?php

	}

}

function getProductIpad($block,$katID) {
	global $fi, $sv;
	
	$select  = "SELECT * FROM ( ";
	$select .= "SELECT artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, bild, kortinfo, tillverkare, Artiklar.tillverkar_id, Artiklar.kategori_id, Kategori.kategori, lagersaldo, Artiklar.utpris, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi ";
	$select .= "FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
        $select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
        $select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
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
	$select .= "AND lagersaldo > 0 AND NOT (demo = -1) ";
	$select .= "ORDER BY lagersaldo DESC ";
	$select .= "LIMIT 10) ";
	$select .= "AS tmp ORDER BY RAND() LIMIT 1 ";
	
	$res = mysqli_query($select);
	
	$num_rows = mysqli_num_rows($res);
	
	if ($num_rows > 0) {

	extract(mysqli_fetch_array($res));

	if ($fi && !$sv) {
		$link = "../info_fi.php?article=";
	} elseif ($fi && $sv) {
		$link = "../info_fi_se.php?article=";
	} else {
		$link = "../info.php?article=";
	}

	if ($tillverkare != ".") {
		if ($fi && !$sv) {
			$produktbeskrivning = $tillverkare ." " . $beskrivning_fi;
		} else {
			$produktbeskrivning = $tillverkare ." " . $beskrivning;
		}
	} else {
		if ($fi && !$sv) {
			$produktbeskrivning = $beskrivning_fi;
		} else {
			$produktbeskrivning = $beskrivning;
		}
	}

	if ($fi && !$sv) {
		$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
	} else {
		$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;kr";
	}

?>
	<table border="0" cellpadding="0" cellspacing="0" width="130" height="140">
	  <tr>
	    <td align="center" style="border-collapse: collapse; background-image: url('/thumbs/large/bilder/<% echo $bild; %>'); background-repeat: no-repeat; background-position: center">
	    <a onmouseover="return escape('<b><% echo $produktbeskrivning; %></b><% if ($kortinfo != "") { %><br><br><% echo $kortinfo; %><% } %><br><br>Pris: <b><% echo $utprismoms; %></b>')" href="<% echo $link; %><% echo $artnr; %>">
	    <img border="0" src="/mobil/link.gif"></a>
	    </td>
	  </tr>
	  <tr>
	    <td valign="bottom">

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr>
	    <td align="left"><p class="pris">&nbsp;&nbsp;<% echo $utprismoms; %></p></td>
	    <td align="right">

		<%
			print "<a href=\"javascript:modifyItems('$artnr')\">";
			if ($fi && !$sv) {
				print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/11_fi.gif\" border=0>";
			} else {
				print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/11.gif\" border=0>";
			}
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
	    <td><img border="0" src="/mobil/ehandlare2009_empty_iphone.jpg"></td>
	  </tr>
	</table>

<?php

	}

}

function mostSoldiPhone($kategorier_id, $limit) {
	global $fi, $sv;
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

        // $select  = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, ";
        $select  = "SELECT mostSoldArticles.artnr, mostSoldArticles.antalManad1, Artiklar.link, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, ";
        $select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi, Artiklar.utpris, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi ";
        $select .= "FROM Artiklar ";
        // $select .= "INNER JOIN mostSoldArticlesLimited ON mostSoldArticlesLimited.artnr = Artiklar.artnr ";
        $select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
        $select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
        $select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
        $select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
        $select .= " WHERE Artiklar.kategori_id IN($kategorier_id) ";

	if ($fi)
		$select .= "AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
	else
		$select .= "AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
	
	$select .= "ORDER BY antalManad1 DESC ";	
	
	$select .= "LIMIT " . $limit;	
	
	$res = mysqli_query($select);

	echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" width=\"230\">";

	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_array($res)):

		extract($row);

		if ($fi && !$sv) {
			$link = "../info_fi.php?article=" . $artnr;
		} elseif ($fi && $sv) {
			$link = "../info_fi_se.php?article=" . $artnr;
		} else {
			$link = "../info.php?article=" . $artnr;
		}

		if ($tillverkare != ".") {
			if ($fi && !$sv) {
				$produktbeskrivning = $tillverkare ." " . $beskrivning_fi;
			} else {
				$produktbeskrivning = $tillverkare ." " . $beskrivning;
			}
		} else {
			if ($fi && !$sv) {
				$produktbeskrivning = $beskrivning_fi;
			} else {
				$produktbeskrivning = $beskrivning;
			}
		}

		if ($fi && !$sv) {
			$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
		} else {
			$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;kr";
		}

		echo "<tr>";

		if ($kortinfo != "") {
			echo "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>$kortinfo<br><br>Pris: <b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		} else {
			echo "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>Pris: <b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		}

		if ($kortinfo != "") {
			echo "<td height=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>$kortinfo<br><br>Pris: <b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $produktbeskrivning . "</b></a></td>\n";
		} else {
			echo "<td height=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>Pris: <b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $produktbeskrivning . "</b></a></td>\n";
		}

		echo "</tr>";

		endwhile;

	} else {

		echo "<tr>";
		echo "<td></td>";
		echo "</tr>";
	}

	echo "</table>";
}


}
?>

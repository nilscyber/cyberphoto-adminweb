<?php

include("connections.php");

Class CAirgun {
	var $conn_my; var $conn_fi;

function __construct() {
	global $fi;
	$this->conn_my = Db::getConnection();
}

function getProductHobby($block,$katID) {

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
	// $select .= "AND lagersaldo > 0 ";
	$select .= "ORDER BY lagersaldo DESC ";
	$select .= "LIMIT 10) ";
	$select .= "AS tmp ORDER BY RAND() LIMIT 1 ";

	$res = mysqli_query($select);

	$num_rows = mysqli_num_rows($res);

	if ($num_rows > 0) {

	extract(mysqli_fetch_array($res));

	if ($tillverkare != ".") {
		$produktbeskrivning = $tillverkare ." " . $beskrivning;
	} else {
		$produktbeskrivning = $beskrivning;
	}

	$utprismoms = number_format(($utpris + $utpris * 0.25), 0, ',', ' ');

?>
	<table border="0" cellpadding="0" cellspacing="0" width="160" height="215">
	  <tr>
	    <td align="center"><p class="rubrikvara"><?php echo $produktbeskrivning; ?></p></td>
	  </tr>
	  <tr>
	    <td align="center" style="border-collapse: collapse; background-image: url('/thumbs/xxlarge/bilder/<?php echo $bild; ?>'); background-repeat: no-repeat; background-position: center">
	    <a onmouseover="return escape('<b><?php echo $produktbeskrivning; ?></b><?php if ($kortinfo != "") { ?><br><br><?php echo $kortinfo; ?><?php } ?><br><br>Pris: <b><?php echo $utprismoms; ?> kr</b> inkl. moms')" href="../info_hobby.php?article=<?php echo $artnr; ?>">
	    <img border="0" src=link.gif></a>
	    </td>
	  </tr>
	  <tr>
	    <td valign="bottom">

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr>
	    <td align="left"><p class="pris">&nbsp;&nbsp;<?php echo $utprismoms; ?>&nbsp;kr</p></td>
	    <td align="right">

		<?php
			print "<a href=\"javascript:modifyItems('$artnr')\">";
			print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/11_hobby.gif\" border=0>";
			print "</a>";
		?>

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
	    <td><img border="0" src="ehandlare2008_empty.jpg"></td>
	  </tr>
	</table>

<?php

	}

}

function getProductHobbyNew($block,$katID) {
	global $fi, $sv;

	$select  = "SELECT * FROM ( ";
	$select .= "SELECT artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, bild, kortinfo, kortinfo_fi, tillverkare, Artiklar.tillverkar_id, Artiklar.kategori_id, Kategori.kategori, lagersaldo, Artiklar.utpris, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi ";
	$select .= "FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
    $select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
    $select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
	// $select .= "WHERE ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) AND (demo=0 OR lagersaldo > 0) ";
	$select .= "WHERE ej_med=0 ";
	/*
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
	*/
	$select .= "AND Artiklar.kategori_id IN($katID) ";
	$select .= "AND lagersaldo > 0 ";
	$select .= "ORDER BY lagersaldo DESC ";
	$select .= "LIMIT 10) ";
	$select .= "AS tmp ORDER BY RAND() LIMIT 1 ";
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89xx") {
		echo $select;
		exit;
	}

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

	if ($fi && $kortinfo_fi != "") {
		$kortinfo2 = $kortinfo_fi;
	}
	if ($sv && $kortinfo != "") {
		$kortinfo2 = $kortinfo;
	}


	if ($fi) {
		$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
	} else {
		$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;kr";
	}

?>
	<table border="0" cellpadding="0" cellspacing="0" width="160" height="215">
	  <tr>
	    <td align="center"><p class="rubrikvara"><?php echo $produktbeskrivning; ?></p></td>
	  </tr>
	  <tr>
	    <td align="center" style="border-collapse: collapse; background-image: url('/thumbs/xxlarge/bilder/<?php echo $bild; ?>'); background-repeat: no-repeat; background-position: center">
	    <a onmouseover="return escape('<b><?php echo $produktbeskrivning; ?></b><?php if ($kortinfo2 != "") { ?><br><br><?php echo $kortinfo2; ?><?php } ?><br><br><?php if ($fi && !$sv): ?>Hinta<?php else: ?>Pris<?php endif; ?>: <b><?php echo $utprismoms; ?></b>')" href="<?php echo $link; ?><?php echo $artnr; ?>">
	    <img border="0" src=link.gif></a>
	    </td>
	  </tr>
	  <tr>
	    <td valign="bottom">

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr>
	    <td align="left"><p class="pris">&nbsp;&nbsp;<?php echo $utprismoms; ?></p></td>
	    <td align="right">

		<?php
			print "<a href=\"javascript:modifyItems('$artnr')\">";
			if ($fi && !$sv) {
				print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/11_fi.gif\" border=0>";
			} else {
				print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/11.gif\" border=0>";
			}
			print "</a>";
		?>

	    </td>
	  </tr>
	</table>

	    </td>
	  </tr>
	</table>

<?php
	} else {

		$this->getProductHobbyNewNotInStore($katID);
	}

}

function getProductHobbyNewNotInStore($katID) {
	global $fi, $sv;

	$select  = "SELECT * FROM ( ";
	$select .= "SELECT artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, bild, kortinfo, kortinfo_fi, tillverkare, Artiklar.tillverkar_id, Artiklar.kategori_id, Kategori.kategori, lagersaldo, Artiklar.utpris, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi ";
	$select .= "FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
    $select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
    $select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
	$select .= "WHERE ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) AND (demo=0 OR lagersaldo > 0) ";
	// $select .= "WHERE ej_med=0 ";
	$select .= "AND Artiklar.kategori_id IN($katID) ";
	// $select .= "AND lagersaldo > 0 ";
	$select .= "ORDER BY lagersaldo DESC ";
	$select .= "LIMIT 10) ";
	$select .= "AS tmp ORDER BY RAND() LIMIT 1 ";
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89xx") {
		echo $select;
		exit;
	}

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

	if ($fi && $kortinfo_fi != "") {
		$kortinfo2 = $kortinfo_fi;
	}
	if ($sv && $kortinfo != "") {
		$kortinfo2 = $kortinfo;
	}


	if ($fi) {
		$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
	} else {
		$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;kr";
	}

?>
	<table border="0" cellpadding="0" cellspacing="0" width="160" height="215">
	  <tr>
	    <td align="center"><p class="rubrikvara"><?php echo $produktbeskrivning; ?></p></td>
	  </tr>
	  <tr>
	    <td align="center" style="border-collapse: collapse; background-image: url('/thumbs/xxlarge/bilder/<?php echo $bild; ?>'); background-repeat: no-repeat; background-position: center">
	    <a onmouseover="return escape('<b><?php echo $produktbeskrivning; ?></b><?php if ($kortinfo2 != "") { ?><br><br><?php echo $kortinfo2; ?><?php } ?><br><br><?php if ($fi && !$sv): ?>Hinta<?php else: ?>Pris<?php endif; ?>: <b><?php echo $utprismoms; ?></b>')" href="<?php echo $link; ?><?php echo $artnr; ?>">
	    <img border="0" src=link.gif></a>
	    </td>
	  </tr>
	  <tr>
	    <td valign="bottom">

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr>
	    <td align="left"><p class="pris">&nbsp;&nbsp;<?php echo $utprismoms; ?></p></td>
	    <td align="right">

		<?php
			print "<a href=\"javascript:modifyItems('$artnr')\">";
			if ($fi && !$sv) {
				print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/11_fi.gif\" border=0>";
			} else {
				print "<img alt=\"Klicka här för att lägga varan i kundvagnen\" src=\"/pic/11.gif\" border=0>";
			}
			print "</a>";
		?>

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
	    <td>&nbsp;&nbsp;</td>
	  </tr>
	</table>

<?php

	}

}

function getHobbyBlogg() {
	global $fi, $sv;

    	$select  = "SELECT cnt, blogType, titel, titel_fi, link ";
    	$select  .= "FROM blog ";
    	$select  .= "WHERE offentlig = -1 AND blogType IN(21) ";
    	if ($fi && !$sv) {
    		$select  .= "AND blogType IN(21) AND NOT (beskrivning_fi IS NULL) AND NOT (link_pic IS NULL) ORDER BY skapad DESC LIMIT 14";
    	} else {
    		$select  .= "AND blogType IN(21) AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) ORDER BY skapad DESC LIMIT 14";
    	}

	$res = mysqli_query($select);

	while ($row = mysqli_fetch_array($res)) {

		extract ($row);

	if ($fi && !$sv) {
		$titel2 = $titel_fi;
	} else {
		$titel2 = $titel;
	}
	
	if ($row["blogType"] == 21) {

	 	if ($fi) {
	 		$link = eregi_replace("\?info", "info_fi", $link);
	 	} else {
	 		$link = eregi_replace("\?info", "info_hobby", $link);
	 	}
	 	$link = eregi_replace("http://www.cyberphoto.se/", "", $link);

	  	if ($row["link"] != "") {
	  		$link = $link;
	  		} else {
	  		$link = "news.php?ID=" .$row["cnt"];
	  		}
	 } else {
	  	$link = "news.php?ID=" .$row["cnt"];
	  	}

		echo "<a href=\"../".$link."\">$titel2</a><br>";
	}

}

function getFejkToplist($artnr) {
	global $fi, $sv;

	$select = "SELECT artnr, beskrivning, bild, kortinfo, tillverkare, Artiklar.tillverkar_id, Artiklar.kategori_id, Kategori.kategori, lagersaldo, utpris ";
	$select .= "FROM Artiklar LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
	$select .= "WHERE ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0)  ";
	$select .= "AND Artiklar.artnr IN($artnr) ";
	// $select .= "ORDER BY lagersaldo DESC ";
	$select .= "LIMIT 5 ";

	$res = mysqli_query($select);

	echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"235\">";

	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_array($res)):

		extract($row);

		if ($tillverkare != ".") {
			$produktbeskrivning = $tillverkare ." " . $beskrivning;
		} else {
			$produktbeskrivning = $beskrivning;
		}

		$utprismoms = number_format(($utpris + $utpris * 0.25), 0, ',', ' ');

		$link = "/info_hobby.php?article=" . $artnr;

		echo "<tr>";

		if ($kortinfo != "") {
			echo "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>$kortinfo<br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		} else {
			echo "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		}

		if ($kortinfo != "") {
			echo "<td height=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>$kortinfo<br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $produktbeskrivning . "</b></a></td>\n";
		} else {
			echo "<td height=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $produktbeskrivning . "</b></a></td>\n";
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

function getFejkToplistNew($artnr) {
	global $fi, $sv;

	$select = "SELECT artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, bild, kortinfo, kortinfo_fi, tillverkare, Artiklar.tillverkar_id, Artiklar.kategori_id, Kategori.kategori, lagersaldo, utpris, utpris_fi, Moms.momssats, Moms.momssats_fi  ";
	$select .= "FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
        $select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
        $select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
	$select .= "WHERE ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0)  ";
	$select .= "AND Artiklar.artnr IN($artnr) ";
	// $select .= "ORDER BY lagersaldo DESC ";
	$select .= "LIMIT 5 ";
	
	// echo $select;

	$res = mysqli_query($select);

	echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"235\">";

	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_array($res)):

		extract($row);

		if ($fi && !$sv) {
			$link = "../info_fi.php?article=" . $artnr;
		} elseif ($fi && $sv) {
			$link = "../info_fi_se.php?article=" . $artnr;
		} else {
			$link = "../info_hobby.php?article=" . $artnr;
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

		if ($fi && $kortinfo_fi != "") {
			$kortinfo2 = $kortinfo_fi;
		}
		if ($sv && $kortinfo != "") {
			$kortinfo2 = $kortinfo;
		}

		if ($fi) {
			if ($sv) {
				$utprismoms = "Pris: " . number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
			} else {
				$utprismoms = "Hinta: " . number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
			}
		} else {
			$utprismoms = "Pris: " . number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;kr";
		}

		echo "<tr>";

		if ($kortinfo != "") {
			echo "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>$kortinfo<br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		} else {
			echo "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		}

		if ($kortinfo != "") {
			echo "<td height=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>$kortinfo<br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $produktbeskrivning . "</b></a></td>\n";
		} else {
			echo "<td height=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $produktbeskrivning . "</b></a></td>\n";
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

function mostSoldHobby($kategorier_id, $limit) {
	global $fi, $sv;
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

        // $select  = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi, ";
        $select  = "SELECT mostSoldArticles.artnr, mostSoldArticles.antalManad1, Artiklar.link, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi, ";
        $select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi ";
        $select .= "FROM Artiklar ";
        // $select .= "INNER JOIN mostSoldArticlesLimited ON mostSoldArticlesLimited.artnr = Artiklar.artnr ";
        $select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
        $select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
        $select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
        $select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
        $select .= "WHERE Artiklar.kategori_id IN($kategorier_id) ";

	if ($fi)
		$select .= "AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
	else
		$select .= "AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
	
	$select .= "ORDER BY antalManad1 DESC ";	
	
	$select .= "LIMIT " . $limit;	
	
	$res = mysqli_query($select);
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		// echo $select;
	}

	echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"235\">";

	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_array($res)):

		extract($row);

		if ($fi && !$sv) {
			$link = "../info_fi.php?article=" . $artnr;
		} elseif ($fi && $sv) {
			$link = "../info_fi_se.php?article=" . $artnr;
		} else {
			$link = "../info_hobby.php?article=" . $artnr;
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

		if ($fi && $kortinfo_fi != "") {
			$kortinfo2 = $kortinfo_fi;
		}
		if ($sv && $kortinfo != "") {
			$kortinfo2 = $kortinfo;
		}

		if ($fi) {
			if ($sv) {
				$utprismoms = "Pris: " . number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
			} else {
				$utprismoms = "Hinta: " . number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
			}
		} else {
			$utprismoms = "Pris: " . number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;kr";
		}

		// $link = "/info_hobby.php?article=" . $artnr;

		echo "<tr>";

		if ($kortinfo2 != "") {
			echo "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>$kortinfo2<br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		} else {
			echo "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		}

		if ($kortinfo2 != "") {
			echo "<td height=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>$kortinfo2<br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $produktbeskrivning . "</b></a></td>\n";
		} else {
			echo "<td height=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $produktbeskrivning . "</b></a></td>\n";
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

function mostSoldAirgun($kategorier_id, $limit) {
	global $fi, $sv;
	/*
	Returnerar en lista på $limit sålda produkter på kategori $kategori_id
	(begränsad till 30 senaste dagarna)
	*/
	$int = 0;
	$select = "";
	$output = "";

        $select  = "SELECT mostSoldArticles.artnr, mostSoldArticles.antalManad1, Artiklar.link, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi, ";
        $select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi ";
        $select .= "FROM Artiklar ";
        $select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
        $select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
        $select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
        $select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
        $select .= "WHERE Artiklar.kategori_id IN($kategorier_id) ";

	if ($fi)
		$select .= "AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
	else
		$select .= "AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
	
	// $select .= "ORDER BY antal DESC ";	
	$select .= "ORDER BY mostSoldArticles.antalManad1 DESC, mostSoldArticles.antalManad2 DESC, mostSoldArticles.analManad3 DESC ";	
	
	$select .= "LIMIT " . $limit;	
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		// echo $select;
	}
	
	$res = mysqli_query($select);

	echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"235\">";

	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_array($res)):

		extract($row);

		if ($fi && !$sv) {
			$link = "../info_fi.php?article=" . $artnr;
		} elseif ($fi && $sv) {
			$link = "../info_fi_se.php?article=" . $artnr;
		} else {
			$link = "../info_hobby.php?article=" . $artnr;
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

		if ($fi && $kortinfo_fi != "") {
			$kortinfo2 = $kortinfo_fi;
		}
		if ($sv && $kortinfo != "") {
			$kortinfo2 = $kortinfo;
		}

		if ($fi) {
			if ($sv) {
				$utprismoms = "Pris: " . number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
			} else {
				$utprismoms = "Hinta: " . number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . "&nbsp;EUR";
			}
		} else {
			$utprismoms = "Pris: " . number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . "&nbsp;kr";
		}

		// $link = "/info_hobby.php?article=" . $artnr;

		echo "<tr>";

		if ($kortinfo2 != "") {
			echo "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>$kortinfo2<br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		} else {
			echo "<td height=\"64\" width=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
		}

		if ($kortinfo2 != "") {
			echo "<td height=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br>$kortinfo2<br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $produktbeskrivning . "</b></a></td>\n";
		} else {
			echo "<td height=\"64\"><a onmouseover=\"return escape('<b>$produktbeskrivning</b><br><br><b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"> "  . $produktbeskrivning . "</b></a></td>\n";
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

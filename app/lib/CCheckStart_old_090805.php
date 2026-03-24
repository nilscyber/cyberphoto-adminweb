<?php
require_once("CCheckIpNumber.php");

Class CCheckStart {

function isValidDateTime($dateTime)
{
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)) {
        if (checkdate($matches[2], $matches[3], $matches[1])) {
            return true;
        }
    }

    return false;
}

function getFrontBannerPrio($section,$site) {

unset ($lastOption);

// $select  = "SELECT * FROM ( ";
$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM frontAdmin ";
$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
$select .= "AND frontSection = '" . $section . "' AND frontSite = '" . $site . "' AND frontPrio = -1 ";
$select .= "ORDER BY frontID DESC ";
// $select .= " ) AS tmp ORDER BY RAND() ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
	
		if ($frontAllowNull == 0 && $frontArtNr != "") {
	
			if ($this->check_store_status($frontArtNr)) {
	
				if (eregi(".php$", $frontPicture)) {
			
					ob_start();
					include("$frontPicture");
					$cont = ob_get_contents();
					ob_end_clean();
			
					echo $cont;
			
				} else {


					if ($frontLinc != "") {
					
						if ($site == 2) {
						
						echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
						
						} elseif ($site == 3) {
						
						echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
						
						} else {
						
						echo "<a href=\"http://www.cyberphoto.se/$frontLinc\">";
						
						}
				
					} else {
					
						if ($site == 2) {
						
						echo "<a href=\"http://www.cyberphoto.fi/info_fi.php?article=$frontArtNr\">";
						
						} elseif ($site == 3) {
						
						echo "<a href=\"http://www.cyberphoto.fi/info_fi_se.php?article=$frontArtNr\">";
						
						} else {
						
						echo "<a href=\"http://www.cyberphoto.se/info.php?article=$frontArtNr\">";
						
						}
				
					}
				
					if (frontArtNr != "") {
					
						$beskrivning = $this->getbeskrivningArt($frontArtNr);
					
						echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
					} else {
					
						echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
					}
				
				}
			
			unset ($lastOption);
			
			break;
			
			} else {
			
			$lastOption = 1;
			
			}

		} else {
	
			if (eregi(".php$", $frontPicture)) {
			
				ob_start();
				include("$frontPicture");
				$cont = ob_get_contents();
				ob_end_clean();
			
				echo $cont;
			
			} else {

				if ($frontLinc != "") {
					
					if ($site == 2) {
					
					echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
					
					} elseif ($site == 3) {
					
					echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
					
					} else {
					
					echo "<a href=\"http://www.cyberphoto.se/$frontLinc\">";
					
					}
				
				} else {
				
					if ($site == 2) {
						
					echo "<a href=\"http://www.cyberphoto.fi/info_fi.php?article=$frontArtNr\">";
						
					} elseif ($site == 3) {
						
					echo "<a href=\"http://www.cyberphoto.fi/info_fi_se.php?article=$frontArtNr\">";
						
					} else {
						
					echo "<a href=\"http://www.cyberphoto.se/info.php?article=$frontArtNr\">";
						
					}
				
				}
				
				if (frontArtNr != "") {
					
					$beskrivning = $this->getbeskrivningArt($frontArtNr);
					
					echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
				} else {
					
					echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
				}
			
			}
	
		unset ($lastOption);
		
		break;
		
		}
	
		endwhile;
		
		if ($lastOption == 1) {
		
		// Här är alla alternativ slut. Då övergår den till ordinarie hämtning / Sjabo
		$this->getFrontBanner($section,$site);
		
		unset ($lastOption);
		
		}

	} else {
	
	// Här är alla alternativ slut. Då övergår den till ordinarie hämtning / Sjabo
	$this->getFrontBanner($section,$site);
	
	}

}	

function getFrontBanner($section,$site) {

unset ($lastOption);

$select  = "SELECT * FROM ( ";
$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM frontAdmin ";
$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
$select .= "AND frontSection = '" . $section . "' AND frontSite = '" . $site . "' ";
$select .= " ) AS tmp ORDER BY RAND() ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
	
		if ($frontAllowNull == 0 && $frontArtNr != "") {
	
			if ($this->check_store_status($frontArtNr)) {
	
				if (eregi(".php$", $frontPicture)) {
			
					ob_start();
					include("$frontPicture");
					$cont = ob_get_contents();
					ob_end_clean();
			
					echo $cont;
			
				} else {


					if ($frontLinc != "") {
					
						if ($site == 2) {
						
						echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
						
						} elseif ($site == 3) {
						
						echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
						
						} else {
						
						echo "<a href=\"http://www.cyberphoto.se/$frontLinc\">";
						
						}
				
					} else {
					
						if ($site == 2) {
						
						echo "<a href=\"http://www.cyberphoto.fi/info_fi.php?article=$frontArtNr\">";
						
						} elseif ($site == 3) {
						
						echo "<a href=\"http://www.cyberphoto.fi/info_fi_se.php?article=$frontArtNr\">";
						
						} else {
						
						echo "<a href=\"http://www.cyberphoto.se/info.php?article=$frontArtNr\">";
						
						}
				
					}
				
					if (frontArtNr != "") {
					
						$beskrivning = $this->getbeskrivningArt($frontArtNr);
					
						echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
					} else {
					
						echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
					}
				
				}
			
			unset ($lastOption);
			
			break;
			
			} else {
			
			$lastOption = 1;
			
			}

		} else {
	
			if (eregi(".php$", $frontPicture)) {
			
				ob_start();
				include("$frontPicture");
				$cont = ob_get_contents();
				ob_end_clean();
			
				echo $cont;
			
			} else {

				if ($frontLinc != "") {
					
					if ($site == 2) {
					
					echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
					
					} elseif ($site == 3) {
					
					echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
					
					} else {
					
					echo "<a href=\"http://www.cyberphoto.se/$frontLinc\">";
					
					}
				
				} else {
				
					if ($site == 2) {
						
					echo "<a href=\"http://www.cyberphoto.fi/info_fi.php?article=$frontArtNr\">";
						
					} elseif ($site == 3) {
						
					echo "<a href=\"http://www.cyberphoto.fi/info_fi_se.php?article=$frontArtNr\">";
						
					} else {
						
					echo "<a href=\"http://www.cyberphoto.se/info.php?article=$frontArtNr\">";
						
					}
				
				}
				
				if (frontArtNr != "") {
					
					$beskrivning = $this->getbeskrivningArt($frontArtNr);
					
					echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
				} else {
					
					echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
				}
			
			}
	
		unset ($lastOption);
		
		break;
		
		}
	
		endwhile;
		
		if ($lastOption == 1) {
		
			// Här är alla alternativ slut. Då visas "nödbilderna" / Sjabo
			if (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
				$this->getFrontBannerPrio($section,1);
			} else {
				$this->getFrontBannerEnd($section,$site);
			}
		
		unset ($lastOption);
		
		}

	} else {
	
		// Här är ingen fronbanner aktiverad. Då visas "nödbilderna" / Sjabo
		if (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
			$this->getFrontBannerPrio($section,1);
		} else {
			$this->getFrontBannerEnd($section,$site);
		}
	
	}

}	

function getMittBanner($site) {

unset ($showmittbanner);

// $select  = "SELECT * FROM ( ";
$select = "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM frontAdmin ";
$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
$select .= "AND frontSection = 99 AND frontSite = '" . $site . "' ";
// $select .= " ) AS tmp ORDER BY RAND() ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);

		if ($frontAllowNull == 0 && $frontArtNr != "") {
	
			if ($this->check_store_status($frontArtNr)) {
	
				return true;
			
			} else {
			
				$showmittbanner = 0;
			}

		} else {
	
			return true;
			
		}
	
		endwhile;
		
	} else {
	
		return false;
	
	}

}	

function check_store_status($frontArtNr) {
	
$select  = "SELECT lagersaldo, ej_med FROM Artiklar WHERE artnr = '" . $frontArtNr . "' ";
$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {

	extract(mysqli_fetch_array($res));

	// if ($lagersaldo > 0 && $ej_med != -1) {
	if ($lagersaldo > 0) {

		return true;
	
		} else {

		return false;
	
		}
	
	} else {
	
	return false;
	
	}

}	

function getbeskrivningArt($artnr) {
	
$select  = "SELECT beskrivning, Tillverkare.tillverkare FROM Artiklar LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id WHERE artnr = '" . $artnr . "' ";
$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {

	extract(mysqli_fetch_array($res));

	return $tillverkare . " ". $beskrivning;
	
	}

}	

function check_artikel_status($addartnr) {
	
$select  = "SELECT artnr FROM Artiklar WHERE artnr = '" . $addartnr . "' ";
$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {

	return true;
	
	} else {
	
	return false;
	
	}

}	

function getFrontBannerEnd($frontSection,$site) {

$select  = "SELECT * FROM ( ";
$select .= "SELECT frontID, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM frontAdmin ";
$select .= "WHERE NOT (frontPicture is Null) AND NOT (frontArtNr is Null AND frontLinc is Null) ";
if ($site == 2) {
	if ($frontSection == 1) {
	$select .= "AND frontPicture LIKE 'fi_last_large_%' ";
	} elseif ($frontSection == 2 || $frontSection == 7) {
	$select .= "AND frontPicture LIKE 'fi_last_mh_%' ";
	} elseif ($frontSection == 5 || $frontSection == 6) {
	$select .= "AND frontPicture LIKE 'fi_last_mv_%' ";
	} elseif ($frontSection > 100) {
	$select .= "AND frontPicture LIKE 'fi_last_right_%' ";
	} else {
	$select .= "AND frontPicture LIKE 'fi_last_small_%' ";
	}
} else {
	if ($frontSection == 1) {
	$select .= "AND frontPicture LIKE 'sv_last_large_%' ";
	} elseif ($frontSection == 2 || $frontSection == 7) {
	$select .= "AND frontPicture LIKE 'sv_last_mh_%' ";
	} elseif ($frontSection == 5 || $frontSection == 6) {
	$select .= "AND frontPicture LIKE 'sv_last_mv_%' ";
	} elseif ($frontSection > 100) {
	$select .= "AND frontPicture LIKE 'sv_last_right_%' ";
	} else {
	$select .= "AND frontPicture LIKE 'sv_last_small_%' ";
	}
}
// $select .= "AND frontSite = '" . $site . "' ";
$select .= " ) AS tmp ORDER BY RAND() ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
	
		if ($frontLinc != "") {
				
			if ($site == 2) {
					
			echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
					
			} elseif ($site == 3) {
					
			echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
					
			} else {
					
			echo "<a href=\"http://www.cyberphoto.se/$frontLinc\">";
					
			}
				
		} else {
				
			if ($site == 2) {
						
			echo "<a href=\"http://www.cyberphoto.fi/info_fi.php?article=$frontArtNr\">";
						
			} elseif ($site == 3) {
						
			echo "<a href=\"http://www.cyberphoto.fi/info_fi_se.php?article=$frontArtNr\">";
						
			} else {
						
			echo "<a href=\"http://www.cyberphoto.se/info.php?article=$frontArtNr\">";
						
			}
				
		}
				
		if (frontArtNr != "") {
					
			$beskrivning = $this->getbeskrivningArt($frontArtNr);
					
			echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
		} else {
					
			echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
		}

		break;
	
		endwhile;
		
	} else {
	
	// echo "<font face=\"Verdana\" size=\"1\">Ingen banner aktiv.<br>Dessutom är ingen &quot;nödbild&quot; producerad.</font>";
	echo "";
	
	}

}	

// ***** NEDAN BÖRJAR ALL ADMINISTRATIV KOD *****

function getFrontBannerAdmin($section,$site) {

unset ($lastOption);

$select  = "SELECT * FROM ( ";
$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM frontAdmin ";
$select .= "WHERE  frontDateFrom < getdate() AND frontDateTo > getdate() AND NOT (frontPicture is Null) AND NOT (frontArtNr is Null AND frontLinc is Null) ";
$select .= "AND frontSection = '" . $section . "' AND frontSite = '" . $site . "' ";
$select .= " ) AS tmp ORDER BY RAND() ";

$res = mssql_query ($select);

	if (mssql_num_rows($res) > 0) {
	
		while ($row = mssql_fetch_array($res)):
	
		extract($row);
	
		if ($frontAllowNull == 0 && $frontArtNr != "") {
	
			if ($this->check_store_status($frontArtNr)) {
			
			echo "<a href=\"front.php?section=$section\">";
			if ($section == 99) {
				echo "<img border=\"0\" src=\"green_mitt.jpg\"></a>";
			} else {
				echo "<img border=\"0\" src=\"green.jpg\"></a>";
			}
			
			unset ($lastOption);
			
			break;
			
			} else {
			
			$lastOption = 1;
			
			}

		} else {
	
			echo "<a href=\"front.php?section=$section\">";
			if ($section == 99) {
				echo "<img border=\"0\" src=\"green_mitt.jpg\"></a>";
			} else {
				echo "<img border=\"0\" src=\"green.jpg\"></a>";
			}
	
		unset ($lastOption);
		
		break;
		
		}
	
		endwhile;
		
		if ($lastOption == 1) {
		
		// Här är alla alternativ slut. Då visas röd status / Sjabo
		echo "<a href=\"front.php?section=$section\">";
		if ($section == 99) {
			echo "<img border=\"0\" src=\"red_mitt.jpg\"></a>";
		} else {
			echo "<img border=\"0\" src=\"red.jpg\"></a>";
		}
		
		unset ($lastOption);
		
		}

	} else {
	
	// Här är ingen fronbanner aktiverad. Då visas röd status / Sjabo
	echo "<a href=\"front.php?section=$section\">";
	if ($section == 99) {
		echo "<img border=\"0\" src=\"red_mitt.jpg\"></a>";
	} else {
		echo "<img border=\"0\" src=\"red.jpg\"></a>";
	}
	
	}

}	

function getBannerAdminNow($status,$site) {

$select  = "SELECT frontID, frontSection, frontDateFrom, frontDateTo, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontLeverantor, frontComment, frontCreatedBy, frontPrio FROM frontAdmin ";
// $select .= "LEFT JOIN Leverantor ON frontAdmin.frontLeverantor = Leverantor.grossist_id ";
$select .= "WHERE frontDateFrom < getdate() AND frontDateTo > getdate() AND frontSection = '" . $status . "' AND frontSite = '" . $site . "' ";
$select .= "ORDER BY frontDateTo ASC, frontArtNr ASC ";

$res = mssql_query ($select);

	if (mssql_num_rows($res) > 0) {
	
		while ($row = mssql_fetch_array($res)):
	
		extract($row);
		
		if ($frontAllowNull == -1 || ($frontAllowNull == 0 && $this->check_store_status($frontArtNr))) {
			$rowcolor = "#CCFFCC";
		} else {
			$rowcolor = "#C0C0C0";
		}
		
		if ($frontArtNr != "" && $this->artikel_status_utgangen($frontArtNr)) {
			$showutgangen = "<a onMouseOver=\"this.T_WIDTH=200;this.T_BGCOLOR='#FF0000';this.T_FONTCOLOR='#FFFFFF';return escape('<b>Detta är en utgången produkt!</b>')\"><font color='85000D'>&nbsp;utg</font></a>";
		} else {
			$showutgangen = "&nbsp;";
		}
		
		$beskrivning = $this->getbeskrivningArt($frontArtNr);
		$leverantor  = $this->getLeverantorName($frontLeverantor);
		$aterstar = $this->getDaysLeft($frontDateTo);
		$lagersaldo = $this->artikel_status_store($frontArtNr);

		echo "<tr>";
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i", strtotime($frontDateFrom)) . "</td>";
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i", strtotime($frontDateTo)) . "</td>";
		if ($aterstar < 4) {
		echo "<td bgcolor=\"$rowcolor\" align=\"right\"><font face=\"Verdana\" size=\"1\" color=\"red\"><b>" . $aterstar . " dagar&nbsp;&nbsp;</b></td>";
		} else {
		echo "<td bgcolor=\"$rowcolor\" align=\"right\"><font face=\"Verdana\" size=\"1\">" . $aterstar . " dagar&nbsp;&nbsp;</td>";
		}
		if (!eregi(".php$", $frontPicture)) {
			if ($frontSection == 1) {
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=500;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
			} elseif ($frontSection == 99) {
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=850;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
			} else {
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
			}
		} else {
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a href=\"/$frontPicture\" target=\_blank\">" . $frontPicture . "</a></td>";
		}
		if ($frontArtNr != "") {
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=450;return escape('<b>$beskrivning</b>')\" href=\"/?info.php?article=$frontArtNr\" target=\_blank\">" . $frontArtNr . "</a></td>";
		} else {
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
		}
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a href=\"/$frontLinc\" target=\_blank\">" . $frontLinc . "</a></td>";
		if ($frontAllowNull == -1) {
		echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">Ja" . $showutgangen . "</td>";
		} else {
			if ($lagersaldo > 0) {
				echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">Nej (" . $lagersaldo . ")" . $showutgangen . "</td>";
			} else {
				echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">Nej" . $showutgangen . "</td>";
			}
		}
		if ($frontAllowNull == -1 || ($frontAllowNull == 0 && $this->check_store_status($frontArtNr))) {
		echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=150;this.T_BGCOLOR='#FFFF00';return escape('<b>Denna visas just nu!</b>')\"><img border=\"0\" src=\"status_green.jpg\"></a></td>";
		} else {
		echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=150;this.T_BGCOLOR='#FFFF00';return escape('<b>Denna visas EJ nu!</b>')\"><img border=\"0\" src=\"status_red.jpg\"></a></td>";
		}
		if ($frontPrio == -1) {
		echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;this.T_BGCOLOR='#FFFF00';return escape('<b>Detta är en prioriterad frontbanner!</b>')\"><img border=\"0\" src=\"star.gif\"></a></td>";
		} else {
		echo "<td bgcolor=\"#FFFFFF\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
		}
		if ($frontLeverantor > 0) {
		echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;return escape('<b>$leverantor</b>')\">Ja</a></td>";
		} else {
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
		}
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . $frontComment . "</td>";
		echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">" . $frontCreatedBy . "</td>";
		echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"front.php?copypost=" . $frontID . "\">Kopiera</a></b></td>";
		echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"front.php?change=" . $frontID . "\">Ändra</a></b></td>";
		echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"front.php?confirmdelete=" . $frontID . "&section=" . $frontSection . "\">Ta bort</a></b></td>";
		echo "</tr>";
	
		endwhile;
		
	} else {
	
	echo "<tr>";
	echo "<td colspan=\"11\"><span style=\"background-color: #FF0000\"><font face=\"Verdana\" size=\"1\" color=\"#FFFFFF\"><b>Inga poster aktiva</b></span></td>";
	echo "</tr>";
	
	}

}

function getBannerAdminPlan($status,$site) {

$select  = "SELECT frontID, frontSection, frontDateFrom, frontDateTo, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontLeverantor, frontComment, frontCreatedBy, frontPrio FROM frontAdmin ";
// $select .= "LEFT JOIN Leverantor ON frontAdmin.frontLeverantor = Leverantor.grossist_id ";
$select .= "WHERE frontDateFrom > getdate() AND frontSection = '" . $status . "' AND frontSite = '" . $site . "' ";
$select .= "ORDER BY frontDateTo ASC, frontArtNr ASC ";

$res = mssql_query ($select);

	if (mssql_num_rows($res) > 0) {
	
		while ($row = mssql_fetch_array($res)):
	
		extract($row);
		
		$rowcolor = "#CCFFFF";

		if ($frontArtNr != "" && $this->artikel_status_utgangen($frontArtNr)) {
			$showutgangen = "<a onMouseOver=\"this.T_WIDTH=200;this.T_BGCOLOR='#FF0000';this.T_FONTCOLOR='#FFFFFF';return escape('<b>Detta är en utgången produkt!</b>')\"><font color='85000D'>&nbsp;utg</font></a>";
		} else {
			$showutgangen = "&nbsp;";
		}

		$beskrivning = $this->getbeskrivningArt($frontArtNr);
		$leverantor  = $this->getLeverantorName($frontLeverantor);

		echo "<tr>";
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i", strtotime($frontDateFrom)) . "</td>";
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i", strtotime($frontDateTo)) . "</td>";
		echo "<td bgcolor=\"$rowcolor\" align=\"right\"><font face=\"Verdana\" size=\"1\">" . $this->getDaysLeft($frontDateFrom) . " dagar&nbsp;&nbsp;</td>";
		if (!eregi(".php$", $frontPicture)) {
			if ($frontSection == 1) {
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=500;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
			} elseif ($frontSection == 99) {
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=850;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
			} else {
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
			}
		} else {
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a href=\"/$frontPicture\" target=\_blank\">" . $frontPicture . "</a></td>";
		}
		if ($frontArtNr != "") {
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=450;return escape('<b>$beskrivning</b>')\" href=\"/?info.php?article=$frontArtNr\" target=\_blank\">" . $frontArtNr . "</a></td>";
		} else {
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
		}
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a href=\"/$frontLinc\" target=\_blank\">" . $frontLinc . "</a></td>";
		if ($frontAllowNull == -1) {
		echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">Ja" . $showutgangen . "</td>";
		} else {
			if ($lagersaldo > 0) {
				echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">Nej (" . $lagersaldo . ")" . $showutgangen . "</td>";
			} else {
				echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">Nej" . $showutgangen . "</td>";
			}
		}
		echo "<td bgcolor=\"#FFFFFF\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
		if ($frontPrio == -1) {
		echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;this.T_BGCOLOR='#FFFF00';return escape('<b>Detta är en prioriterad frontbanner!</b>')\"><img border=\"0\" src=\"star.gif\"></a></td>";
		} else {
		echo "<td bgcolor=\"#FFFFFF\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
		}
		if ($frontLeverantor > 0) {
		echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;return escape('<b>$leverantor</b>')\">Ja</a></td>";
		} else {
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
		}
		echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . $frontComment . "</td>";
		echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">" . $frontCreatedBy . "</td>";
		echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"front.php?copypost=" . $frontID . "\">Kopiera</a></b></td>";
		echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"front.php?change=" . $frontID . "\">Ändra</a></b></td>";
		echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"front.php?confirmdelete=" . $frontID . "&section=" . $frontSection . "\">Ta bort</a></b></td>";
		echo "</tr>";
	
		endwhile;
		
	} else {
	
	echo "<tr>";
	echo "<td colspan=\"11\"><span style=\"background-color: #FF0000\"><font face=\"Verdana\" size=\"1\" color=\"#FFFFFF\"><b>Inga poster planerade</b></span></td>";
	echo "</tr>";
	
	}

}

function artikel_status_store($frontArtNr) {
	
$select  = "SELECT lagersaldo FROM Artiklar WHERE artnr = '" . $frontArtNr . "' ";
$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
		
		return $lagersaldo;
		
		endwhile;
	
	} else {
	
	return "";
	
	}

}	

function artikel_status_utgangen($frontArtNr) {
	
$select  = "SELECT utgangen FROM Artiklar WHERE utgangen = -1 AND artnr = '" . $frontArtNr . "' ";
$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {

	return true;
		
	} else {
	
	return false;
	
	}

}	

function getWarning($site) {

$select  = "SELECT * FROM frontAdmin WHERE DateDiff(d, getdate(), frontDateTo) < 4 AND frontDateFrom < getdate() AND frontDateTo > getdate() AND frontSite = '" . $site . "' ";

$res = mssql_query ($select);

	if (mssql_num_rows($res) > 0) {
	
		return true;
	
	} else {
	
		return false;
	}

}

function displayWarning($site) {

$rowcolor = true;

$select  = "SELECT frontSection, count(frontSection) AS Antal FROM frontAdmin WHERE DateDiff(d, getdate(), frontDateTo) < 4 AND frontDateFrom < getdate() AND frontDateTo > getdate() AND frontSite = '" . $site . "' GROUP BY frontSection ORDER BY frontSection ASC ";

$res = mssql_query ($select);

	if (mssql_num_rows($res) > 0) {
	
		while ($row = mssql_fetch_array($res)):
	
		extract($row);
		
		if ($rowcolor == true) {
			$backcolor = "#FFFF00";
		} else {
			$backcolor = "#FFFF99";
		}
		
		echo "<tr>";
		echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">" . $frontSection . "</td>";
		echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">" . $Antal . " st</td>";
		echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"front.php?section=" . $frontSection . "\">Visa</a></b></td>";
		echo "</tr>";
		
		if ($rowcolor == true) {
			$row = true;
			$rowcolor = false;
		} else {
			$row = false;
			$rowcolor = true;
		}
	
		endwhile;
		
	} else {
	
	echo "<tr>";
	echo "<td colspan=\"2\"><span style=\"background-color: #FF0000\"><font face=\"Verdana\" size=\"1\" color=\"#FFFFFF\"><b>Inga varningar finns</b></span></td>";
	echo "</tr>";
	
	}

}

function getLeverantor() {

global $addleverantor;

$select  = "SELECT grossist_id, grossist FROM Leverantor WHERE NOT (grossist is null) ORDER BY grossist ASC ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);

		if (strlen($grossist) >= 14)
			$grossist = substr ($grossist, 0, 14) . "...";
		
		echo "<option value=\"$grossist_id\"";
		
		if ($addleverantor == $grossist_id) {
			echo " selected";
		}
		
		echo ">" . $grossist . "</option>";
		
		// echo "<option value=\"$grossist_id\">" . $grossist . "</option>";
	
		endwhile;
		
	} else {
	
		echo "<option value=\"\">Tomt</option>";
	
	}

}

function getLeverantorLong() {

global $addleverantor;

$select  = "SELECT grossist_id, grossist FROM Leverantor WHERE NOT (grossist is null) ORDER BY grossist ASC ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);

		if (strlen($grossist) >= 55)
			$grossist = substr ($grossist, 0, 55) . "...";
		
		echo "<option value=\"$grossist_id\"";
		
		if ($addleverantor == $grossist_id) {
			echo " selected";
		}
		
		echo ">" . $grossist . "</option>";
		
		// echo "<option value=\"$grossist_id\">" . $grossist . "</option>";
	
		endwhile;
		
	} else {
	
		echo "<option value=\"\">Tomt</option>";
	
	}

}

function getLeverantorName($frontLeverantor) {

$select  = "SELECT grossist FROM Leverantor WHERE grossist_id = '" . $frontLeverantor . "' ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
		
		return $grossist;
		
		endwhile;
		
	} else {
	
		return "";
	
	}

}

function getAnstallda() {

global $addcreatedby;

$select  = "SELECT sign, namn FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";

$res = mssql_query ($select);

	while ($row = mssql_fetch_array($res)) {
	
	extract($row);

	echo "<option value=\"$sign\"";
		
	if ($addcreatedby == $sign) {
		echo " selected";
	}
		
	echo ">" . $namn . "</option>";
		
	
	// endwhile;

	}

}

function BannerAdminDelete($delete) {

mssql_query ("DELETE FROM frontAdmin WHERE frontID = '" . $delete . "'");

header("Location: update_front.php");

}

function BannerAdminAdd($addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addsite,$addprio) {

mssql_query ("INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontLeverantor,frontCreatedBy,frontSite,frontPrio) VALUES ('$addsection','$addfrom','$addto','$addpicture','$addartnr','$addlinc','$addstore','$addcomment','$addleverantor','$addcreatedby','$addsite','$addprio') ");

header("Location: update_front.php?section=$addsection");

}

function BannerAdminChange($addid,$addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addprio) {

mssql_query ("UPDATE frontAdmin  SET frontSection = '$addsection', frontDateFrom = '$addfrom',frontDateTo = '$addto',frontPicture = '$addpicture',frontArtNr = '$addartnr',frontLinc = '$addlinc',frontAllowNull = '$addstore',frontComment = '$addcomment',frontLeverantor = '$addleverantor',frontCreatedBy = '$addcreatedby',frontPrio = '$addprio' WHERE frontID = '$addid' ");
// mssql_query ("INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontCreatedBy) VALUES ('$addsection','$addfrom','$addto','$addpicture','$addartnr','$addlinc','$addstore','$addcomment','$addcreatedby') ");

header("Location: update_front.php?section=$addsection");

}

function getSpecFrontBanner($frontID) {

$select  = "SELECT * FROM frontAdmin WHERE frontID = '" . $frontID . "' ";

$res = mysqli_query($select);

$rows = mysqli_fetch_object($res);

return $rows;

}

function getDaysLeft($frontDateTo) {

	$now = time();
	$timeto = strtotime($frontDateTo);
	$diff = $timeto - $now;
	$sek = $diff % 60;
	$min = ($diff / 60) % 60;
	$hour = ($diff / 3600);
	$days = ($diff / 86400);
	$days = floor($days);
	$days = round($days, 0);
	return $days;
}

function getDaysBetween($frontDateFrom,$frontDateTo,$datumfrom,$datumto) {

	if (strtotime($frontDateFrom) > strtotime($datumfrom)) {
		$timefrom = strtotime($frontDateFrom);
	} else {
		$timefrom = strtotime($datumfrom);
	}

	if (strtotime($frontDateTo) < strtotime($datumto)) {
		$timeto = strtotime($frontDateTo);
	} else {
		$timeto = strtotime($datumto);
	}
	
	// $timefrom = strtotime($frontDateFrom);
	// $timeto = strtotime($frontDateTo);
	$diff = $timeto - $timefrom;
	$sek = $diff % 60;
	$min = ($diff / 60) % 60;
	$hour = ($diff / 3600);
	$days = ($diff / 86400);
	$days = floor($days) + 1;
	$days = round($days, 0);
	return $days;
}

function getKommaRunt() {

	return true;
	
}

function getFrontBannerPrioHistory($section,$site,$datum) {

unset ($lastOption);

// $select  = "SELECT * FROM ( ";
$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM frontAdmin ";
$select .= "WHERE  frontDateFrom < '$datum 12:00:00' AND frontDateTo > '$datum 12:00:00' AND NOT (frontPicture is Null) ";
$select .= "AND frontSection = '" . $section . "' AND frontSite = '" . $site . "' AND frontPrio = -1 ";
$select .= "ORDER BY frontID DESC ";
// $select .= " ) AS tmp ORDER BY RAND() ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
	
		if ($frontAllowNull == 0 && $frontArtNr != "") {
	
			if ($this->getKommaRunt()) {
	
				if (eregi(".php$", $frontPicture)) {
			
					ob_start();
					include("$frontPicture");
					$cont = ob_get_contents();
					ob_end_clean();
			
					echo $cont;
			
				} else {


					if ($frontLinc != "") {
					
						if ($site == 2) {
						
						echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
						
						} elseif ($site == 3) {
						
						echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
						
						} else {
						
						echo "<a href=\"http://www.cyberphoto.se/$frontLinc\">";
						
						}
				
					} else {
					
						if ($site == 2) {
						
						echo "<a href=\"http://www.cyberphoto.fi/info_fi.php?article=$frontArtNr\">";
						
						} elseif ($site == 3) {
						
						echo "<a href=\"http://www.cyberphoto.fi/info_fi_se.php?article=$frontArtNr\">";
						
						} else {
						
						echo "<a href=\"http://www.cyberphoto.se/info.php?article=$frontArtNr\">";
						
						}
				
					}
				
					echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
				}
			
			unset ($lastOption);
			
			break;
			
			} else {
			
			$lastOption = 1;
			
			}

		} else {
	
			if (eregi(".php$", $frontPicture)) {
			
				ob_start();
				include("$frontPicture");
				$cont = ob_get_contents();
				ob_end_clean();
			
				echo $cont;
			
			} else {

				if ($frontLinc != "") {
					
					if ($site == 2) {
					
					echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
					
					} elseif ($site == 3) {
					
					echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
					
					} else {
					
					echo "<a href=\"http://www.cyberphoto.se/$frontLinc\">";
					
					}
				
				} else {
				
					if ($site == 2) {
						
					echo "<a href=\"http://www.cyberphoto.fi/info_fi.php?article=$frontArtNr\">";
						
					} elseif ($site == 3) {
						
					echo "<a href=\"http://www.cyberphoto.fi/info_fi_se.php?article=$frontArtNr\">";
						
					} else {
						
					echo "<a href=\"http://www.cyberphoto.se/info.php?article=$frontArtNr\">";
						
					}
				
				}
				
				echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";
			
			}
	
		unset ($lastOption);
		
		break;
		
		}
	
		endwhile;
		
		if ($lastOption == 1) {
		
		// Här är alla alternativ slut. Då övergår den till ordinarie hämtning / Sjabo
		$this->getFrontBannerHistory($section,$site,$datum);
		
		unset ($lastOption);
		
		}

	} else {
	
	// Här är alla alternativ slut. Då övergår den till ordinarie hämtning / Sjabo
	$this->getFrontBannerHistory($section,$site,$datum);
	
	}

}	

function getFrontBannerHistory($section,$site,$datum) {

unset ($lastOption);

$select  = "SELECT * FROM ( ";
$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM frontAdmin ";
$select .= "WHERE  frontDateFrom < '$datum 12:00:00' AND frontDateTo > '$datum 12:00:00' AND NOT (frontPicture is Null) ";
$select .= "AND frontSection = '" . $section . "' AND frontSite = '" . $site . "' ";
$select .= " ) AS tmp ORDER BY RAND() ";

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
	
		if ($frontAllowNull == 0 && $frontArtNr != "") {
	
			if ($this->getKommaRunt()) {
	
				if (eregi(".php$", $frontPicture)) {
			
					ob_start();
					include("$frontPicture");
					$cont = ob_get_contents();
					ob_end_clean();
			
					echo $cont;
			
				} else {


					if ($frontLinc != "") {
					
						if ($site == 2) {
						
						echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
						
						} elseif ($site == 3) {
						
						echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
						
						} else {
						
						echo "<a href=\"http://www.cyberphoto.se/$frontLinc\">";
						
						}
				
					} else {
					
						if ($site == 2) {
						
						echo "<a href=\"http://www.cyberphoto.fi/info_fi.php?article=$frontArtNr\">";
						
						} elseif ($site == 3) {
						
						echo "<a href=\"http://www.cyberphoto.fi/info_fi_se.php?article=$frontArtNr\">";
						
						} else {
						
						echo "<a href=\"http://www.cyberphoto.se/info.php?article=$frontArtNr\">";
						
						}
				
					}
				
					echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";
				
				}
			
			unset ($lastOption);
			
			break;
			
			} else {
			
			$lastOption = 1;
			
			}

		} else {
	
			if (eregi(".php$", $frontPicture)) {
			
				ob_start();
				include("$frontPicture");
				$cont = ob_get_contents();
				ob_end_clean();
			
				echo $cont;
			
			} else {

				if ($frontLinc != "") {
					
					if ($site == 2) {
					
					echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
					
					} elseif ($site == 3) {
					
					echo "<a href=\"http://www.cyberphoto.fi/$frontLinc\">";
					
					} else {
					
					echo "<a href=\"http://www.cyberphoto.se/$frontLinc\">";
					
					}
				
				} else {
				
					if ($site == 2) {
						
					echo "<a href=\"http://www.cyberphoto.fi/info_fi.php?article=$frontArtNr\">";
						
					} elseif ($site == 3) {
						
					echo "<a href=\"http://www.cyberphoto.fi/info_fi_se.php?article=$frontArtNr\">";
						
					} else {
						
					echo "<a href=\"http://www.cyberphoto.se/info.php?article=$frontArtNr\">";
						
					}
				
				}
				
				echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";
			
			}
	
		unset ($lastOption);
		
		break;
		
		}
	
		endwhile;
		
		if ($lastOption == 1) {
		
			// Här är alla alternativ slut. Då visas "nödbilderna" / Sjabo
			if (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
				$this->getFrontBannerPrio($section,1);
			} else {
				$this->getFrontBannerEnd($section,$site);
			}
		
		unset ($lastOption);
		
		}

	} else {
	
		// Här är ingen fronbanner aktiverad. Då visas "nödbilderna" / Sjabo
		if (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
			$this->getFrontBannerPrio($section,1);
		} else {
			$this->getFrontBannerEnd($section,$site);
		}
	
	}

}	

function getFrontDelivery($datumfrom,$datumto,$site,$leverantor) {

$select  = "SELECT frontID, frontSection, frontDateFrom, frontDateTo, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontCreatedBy, frontLeverantor FROM frontAdmin ";
$select .= "WHERE NOT (frontLeverantor = 0) AND NOT (frontPicture is Null) ";
$select .= "AND (((frontDateFrom > '$datumfrom 12:00:00' AND frontDateFrom < '$datumto 12:00:00') OR (frontDateTo > '$datumfrom 12:00:00' AND frontDateTo < '$datumto 12:00:00')) OR (frontDateFrom < '$datumfrom 12:00:00' AND frontDateTo > '$datumto 12:00:00')) ";
$select .= "AND frontSite = '" . $site . "' ";
if ($leverantor != "") {
$select .= "AND frontLeverantor = '" . $leverantor . "' ";
}

// echo $select;

$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {
	
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
		
		if ($leverantor == "") {
		
		$frontLeverantor = $this->getLeverantorName($frontLeverantor);
		
		}
		
		$startdag = strtotime($frontDateFrom);
		$stoppdag = strtotime($frontDateTo);
		$antaldagar = (($startdag-$stoppdag)/86400);
		
		// echo $antaldagar;
		
		echo "<tr>";
		if ($leverantor == "") {
		echo "<td align=\"center\"><font face=\"Verdana\" size=\"1\"><b>$frontLeverantor</font></td>";
		}
		echo "<td><font face=\"Verdana\" size=\"1\"><b>" . date("j M Y", strtotime($frontDateFrom)) . "</font></td>";
		echo "<td><font face=\"Verdana\" size=\"1\"><b>" . date("j M Y", strtotime($frontDateTo)) . "</font></td>";
		echo "<td align=\"center\"><font face=\"Verdana\" size=\"1\"><b>" . $this->getDaysBetween($frontDateFrom,$frontDateTo,$datumfrom,$datumto) . "</font></td>";
		echo "<td><font face=\"Verdana\" size=\"1\"><img src=\"/start3/$frontPicture\"></font></td>";
		echo "<td align=\"center\"><font face=\"Verdana\" size=\"1\"><b>$frontSection</font></td>";
		echo "<td align=\"center\"><font face=\"Verdana\" size=\"1\"><b>$frontCreatedBy</font></td>";
		echo "</tr>";
		
		endwhile;
		
	} else {
	
	echo "<tr>";
	if ($leverantor == "") {
	echo "<td colspan=\"7\"><font face=\"Verdana\" size=\"1\"><b>Inga poster finns för aktuell period eller leverantör</font></td>";
	} else {
	echo "<td colspan=\"6\"><font face=\"Verdana\" size=\"1\"><b>Inga poster finns för aktuell period eller leverantör</font></td>";
	}
	echo "</tr>";
	
	}
	
}


}

?>

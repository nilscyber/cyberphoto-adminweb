<?php
// include("connections.php");
require_once("CCheckIpNumber.php");

Class CCheckStart {

	var $conn_my;
    var $conn_my2;

	function __construct() {

		$this->conn_my = Db::getConnection();
		
		$this->conn_my2 = Db::getConnection(true);
		
	}


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
	$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontSite FROM frontAdmin ";
	$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
	$select .= "AND frontSection = '" . $section . "' AND frontSite = '" . $site . "' AND frontPrio = -1 ";
	$select .= "ORDER BY frontID DESC ";
	
	// echo $select;
	// $select .= " ) AS tmp ORDER BY RAND() ";

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			$analyticsSite = $this->getSiteForAnalytics($frontSite);
		
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
						
							echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
					
						} else {
						
							if ($site == 2 || $site == 7 || $site == 14) {
							
								if (frontArtNr != "") {
								
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
									echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info_fi.php?article=$frontArtNr\">";
								
								} else {
								
									echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info_fi.php?article=$frontArtNr\">";

								}
							
							} elseif ($site == 3 || $site == 8) {
							
								if (frontArtNr != "") {
								
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
									echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info_fi_se.php?article=$frontArtNr\">";
								
								} else {
								
									echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info_fi_se.php?article=$frontArtNr\">";

								}
							
							} else {
							
								if (frontArtNr != "") {
								
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
									echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
								} else {
								
									echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";

								}
							
							}
					
						}
					
						if (frontArtNr != "") {
						
							$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
						
							if ($site == 7 || $site == 8) {
							
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start_mobiili/$frontPicture\"></a>";
								
							} else {
							
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start3/$frontPicture\"></a>";

							}
					
						} else {
						
							if ($site == 7 || $site == 8) {
							
								echo "<img border=\"0\" src=\"/start_mobiili/$frontPicture\"></a>";
								
							} else {
							
								echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";

							}
					
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
						
						echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
					
					} else {
					
						if ($site == 2 || $site == 7 || $site == 14) {
							
							if (frontArtNr != "") {
								
								$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
								echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info_fi.php?article=$frontArtNr\">";
								
							} else {
								
								echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info_fi.php?article=$frontArtNr\">";

							}
							
						} elseif ($site == 3 || $site == 8) {
							
							if (frontArtNr != "") {
								
								$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
								echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info_fi_se.php?article=$frontArtNr\">";
								
							} else {
								
								echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info_fi_se.php?article=$frontArtNr\">";

							}
							
						} else {
							
							if (frontArtNr != "") {
								
								$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
								echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
							} else {
								
								echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";

							}
							
						}
					
					}
					
					if (frontArtNr != "") {
						
						$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
						
						if ($site == 7 || $site == 8) {
							
							echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start_mobiili/$frontPicture\"></a>";
								
						} else {
							
							echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start3/$frontPicture\"></a>";

						}
					
					} else {
						
						if ($site == 7 || $site == 8) {
							
							echo "<img border=\"0\" src=\"/start_mobiili/$frontPicture\"></a>";
								
						} else {
							
							echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";

						}
					
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
		global $frameless;

	unset ($lastOption);

	$select  = "SELECT * FROM ( ";
	$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontSite FROM frontAdmin ";
	$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
	$select .= "AND frontSection = '" . $section . "' AND frontSite = '" . $site . "' ";
	$select .= " ) AS tmp ORDER BY RAND() ";
	// echo $select;
	// exit;

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
		
			$analyticsSite = $this->getSiteForAnalytics($frontSite);

			if ($frontAllowNull == 0 && $frontArtNr != "") {
		
				if ($this->check_store_status($frontArtNr)) {
		
					if (eregi(".php$", $frontPicture)) {
						
						if ($frontID == 7689 && $frameless) {
							ob_start();
							include("samsung_section12.php");
							$cont = ob_get_contents();
							ob_end_clean();
							
							$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
							
						} else {
				
							ob_start();
							include("$frontPicture");
							$cont = ob_get_contents();
							ob_end_clean();
						
						}
				
						echo $cont;
				
					} else {


						if ($frontLinc != "") {
						
							echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
					
						} else {
						
							if ($site == 2 || $site == 7 || $site == 14) {
							
								if (frontArtNr != "") {
								
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
									echo "<a title=\"$linctitle\" href=\"/info_fi.php?article=$frontArtNr\">";
								
								} else {
								
									echo "<a href=\"/info_fi.php?article=$frontArtNr\">";

								}
							
							} elseif ($site == 3 || $site == 8) {
							
								if (frontArtNr != "") {
								
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
									echo "<a title=\"$linctitle\" href=\"info_fi_se.php?article=$frontArtNr\">";
								
								} else {
								
									echo "<a href=\"/info_fi_se.php?article=$frontArtNr\">";

								}
							
							} else {
							
								if (frontArtNr != "") {
								
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
									echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
								} else {
								
									echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";

								}
							
							}
					
						}
					
						if (frontArtNr != "") {
						
							$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
						
							if ($site == 7 || $site == 8) {
							
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start_mobiili/$frontPicture\"></a>";
								
							} else {
							
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start3/$frontPicture\"></a>";

							}
					
						} else {
						
							if ($site == 7 || $site == 8) {
							
								echo "<img border=\"0\" src=\"/start_mobiili/$frontPicture\"></a>";
								
							} else {
							
								echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";

							}
					
						}
					
					}
				
				unset ($lastOption);
				
				break;
				
				} else {
				
				$lastOption = 1;
				
				}

			} else {
		
				if (eregi(".php$", $frontPicture)) {
				
					if ($frontID == 7689 && $frameless) {
						ob_start();
						include("samsung_section12.php");
						$cont = ob_get_contents();
						ob_end_clean();

						$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
						
					} else {
					
						ob_start();
						include("$frontPicture");
						$cont = ob_get_contents();
						ob_end_clean();
					
					}
				
					echo $cont;
				
				} else {

					if ($frontLinc != "") {
						
						echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
					
					} else {
					
						if ($site == 2 || $site == 7 || $site == 14) {
							
							if (frontArtNr != "") {
								
								$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
								echo "<a title=\"$linctitle\" href=\"/info_fi.php?article=$frontArtNr\">";
								
							} else {
								
								echo "<a href=\"/info_fi.php?article=$frontArtNr\">";

							}
							
						} elseif ($site == 3 || $site == 8) {
							
							if (frontArtNr != "") {
								
								$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
								echo "<a title=\"$linctitle\" href=\"/info_fi_se.php?article=$frontArtNr\">";
								
							} else {
								
								echo "<a href=\"/info_fi_se.php?article=$frontArtNr\">";

							}

						} else {
							
							if (frontArtNr != "") {
								
								$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
								echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
							} else {
								
								echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";

							}
							
						}
					
					}
					
					if (frontArtNr != "") {
						
						$beskrivning = $this->getbeskrivningArt($frontArtNr);
						
							if ($site == 7 || $site == 8) {
							
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start_mobiili/$frontPicture\"></a>";
								
							} else {
							
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/start3/$frontPicture\"></a>";

							}
					
					} else {
						
						if ($site == 7 || $site == 8) {
							
							echo "<img border=\"0\" src=\"/start_mobiili/$frontPicture\"></a>";
								
						} else {
							
							echo "<img border=\"0\" src=\"/start3/$frontPicture\"></a>";

						}
					
					}
				
				}
		
			unset ($lastOption);
			
			break;
			
			}
		
			endwhile;
			
			if ($lastOption == 1) {
			
				if ($site < 500) { // tar bort nödbilderna om det inte är fronten
				
					// Här är alla alternativ slut. Då visas "nödbilderna" / Sjabo
					if (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
						$this->getFrontBannerPrio($section,1);
					} else {
						$this->getFrontBannerEnd($section,$site);
					}
				}
			
			unset ($lastOption);
			
			}

		} else {
		
			if ($site < 500) { // tar bort nödbilderna om det inte är fronten
			
				// Här är ingen fronbanner aktiverad. Då visas "nödbilderna" / Sjabo
				if (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
					$this->getFrontBannerPrio($section,1);
				} else {
					$this->getFrontBannerEnd($section,$site);
				}
			}
		}

	}	

	function getMittBannerOld($site) {

	unset ($showmittbanner);

	// $select  = "SELECT * FROM ( ";
	$select = "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM frontAdmin ";
	$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
	$select .= "AND frontSection = 99 AND frontSite = '" . $site . "' ";
	// $select .= " ) AS tmp ORDER BY RAND() ";

	$res = mysqli_query($this->conn_my, $select);

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

	function getMittBanner($site) {

	$select  = "SELECT * FROM ( ";
	$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM frontAdmin ";
	$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
	$select .= "AND frontSection = 99 AND frontSite = '" . $site . "' ";
	$select .= " ) AS tmp ORDER BY RAND() ";

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

			if ($frontAllowNull == 0 && $frontArtNr != "") {
		
				if ($this->check_store_status($frontArtNr)) {
		
					return true;
				
				} else {
				
					return false;
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
		
	$select = "SELECT lagersaldo FROM Artiklar WHERE artnr = '" . $frontArtNr . "' ";

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

		extract(mysqli_fetch_array($res));

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
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

		extract(mysqli_fetch_array($res));

		return $tillverkare . " ". $beskrivning;
		
		}

	}	

	function getbeskrivningArtNew($artnr,$site = null) {
		
	$select  = "SELECT beskrivning, beskrivning_fi, Tillverkare.tillverkare FROM Artiklar LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id WHERE artnr = '" . $artnr . "' ";
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

		extract(mysqli_fetch_array($res));

		if ($site == 2 || $site == 7) {
		
			return $tillverkare . " ". $beskrivning_fi;

		} else {
		
			return $tillverkare . " ". $beskrivning;

		}
		
		}

	}	

	function check_artikel_status($addartnr) {
		
	$select  = "SELECT artnr FROM Artiklar WHERE artnr = '" . $addartnr . "' ";
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			extract(mysqli_fetch_array($res));
			
			return $artnr;
			// return true;
		
		} else {
		
			return false;
		
		}

	}	

	function getFrontBannerEnd($frontSection,$site) {

		if ($site == 2) {
			echo "";
		} elseif ($site == 3) {
			echo "";
		} elseif ($site == 7) {
			echo "";
		} elseif ($site == 8) {
			echo "";
		} else {
			if ($frontSection > 9 && $frontSection < 50) {
					echo "<a href=\"/blogg.php\"><img border=\"0\" src=\"/start3/last_rod_155x102.gif\"></a>";
			} elseif ($frontSection == 5 || $frontSection == 6) {
					echo "<a href=\"/blogg.php\"><img border=\"0\" src=\"/start3/last_rod_225x157.gif\"></a>";
			} elseif ($frontSection == 2 || $frontSection == 7) {
					echo "<a href=\"/blogg.php\"><img border=\"0\" src=\"/start3/last_rod_177x183.png\"></a>";
			}
		}
	}	

	function getSiteForAnalytics($frontSite) {

		if ($frontSite == 13) { // Hushåll
			$category = "FrontClickHouse";
		} elseif ($frontSite == 12) { // Pro
			$category = "FrontClickPro";
		} elseif ($frontSite == 11) { // Hobby
			$category = "FrontClickHobby";
		} elseif ($frontSite == 10) { // Cybairgun
			$category = "FrontClickCybairgun";
		} elseif ($frontSite == 9) { // Lagershop
			$category = "FrontClickLagershop";
		} elseif ($frontSite == 8) { // Mobil SV/FI
			$category = "FrontClickMobilFISE";
		} elseif ($frontSite == 7) { // Mobil SV/FI
			$category = "FrontClickMobilFI";
		} elseif ($frontSite == 6) { // Mobil
			$category = "FrontClickMobil";
		} elseif ($frontSite == 3) { // FISE
			$category = "FrontClickFISE";
		} elseif ($frontSite == 2) { // FI
			$category = "FrontClickFI";
		} elseif ($frontSite == 14) { // Cybairgun FI
			$category = "FrontClickCybairgunFI";
		} else { // Foto
			$category = "FrontClickFoto";
		}
		
		return $category;

	}

	function mostSoldMobil($kategorier_id, $limit, $sv, $fi) {

		$int = 0;
		$select = "";
		$output = "";
		$countrow = 0;

			$select  = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, ";
			$select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi ";
			$select .= "FROM Artiklar ";
			$select .= "INNER JOIN mostSoldArticlesLimited ON mostSoldArticlesLimited.artnr = Artiklar.artnr ";
			$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
			$select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
			$select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
			$select .= "WHERE Artiklar.kategori_id IN ($kategorier_id) ";

		if ($fi)
			$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
		else
			$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
		
		$select .= " ORDER BY antal DESC";	
		
		$select .= " LIMIT " . $limit;	
		
		$res = mysqli_query($select);

		while ($row = mysqli_fetch_array($res)) {
			$int += 1;
			extract ($row);
			if ($fi && !$sv) {
				
				if ($beskrivning_fi != "")
					$beskrivning = $beskrivning_fi;
				
				if ($link2_fi != "")
					$link = $link2_fi;
				else
					$link = "info_fi.php?article=".$artnr;
			} elseif ($fi && $sv) {
			
				if ($link2_fi != "")
					$link = $link2_fi;
				else
					$link = "info_fi_se.php?article=".$artnr;
				
			} else {

				if ($link != "")
					$link = $link;
				else
					$link = "../info_mobil.php?article=".$artnr;
				
			}

			if ($bild == "") {

				if ($fi && !$sv) {
					$bild = "../../../pic/noimage_fi.jpg";
				} else {
					$bild = "../../../pic/noimage.jpg";
				}
			}

			if ($fi) {
				$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ');
			} else {
				$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ');
			}

			print "<tr>";

			if ($countrow > 0) {
				if ($fi && !$sv) {
					print "<td height=\"64\" width=\"64\" style=\"border-top: 1px solid #85000D\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Hinta: <b>$utprismoms EUR</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
				} else {
					print "<td height=\"64\" width=\"64\" style=\"border-top: 1px solid #85000D\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
				}
			} else {
				if ($fi && !$sv) {
					print "<td height=\"64\" width=\"64\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Hinta: <b>$utprismoms EUR</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
				} else {
					print "<td height=\"64\" width=\"64\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
				}
			}

			print "</tr>";
			
			$countrow ++;

		}
		return $output;
	}

	function mostSoldMobilNew($kategorier_id, $limit, $sv, $fi) {

		$int = 0;
		$select = "";
		$output = "";
		$countrow = 0;

			$select  = "SELECT mostSoldArticlesLimited.artnr, mostSoldArticlesLimited.antal, Artiklar.link, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Tillverkare.tillverkare, ";
			$select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi ";
			$select .= "FROM Artiklar ";
			$select .= "INNER JOIN mostSoldArticlesLimited ON mostSoldArticlesLimited.artnr = Artiklar.artnr ";
			$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
			$select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
			$select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
			$select .= "WHERE Artiklar.kategori_id IN ($kategorier_id) ";

		if ($fi)
			$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
		else
			$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
		
		$select .= " ORDER BY antal DESC";	
		
		$select .= " LIMIT " . $limit;	
		
		$res = mysqli_query($select);

		while ($row = mysqli_fetch_array($res)) {
			$int += 1;
			extract ($row);
			if ($fi && !$sv) {
				
				if ($beskrivning_fi != "")
					$beskrivning = $beskrivning_fi;
				
				if ($link2_fi != "")
					$link = $link2_fi;
				else
					$link = "info_fi.php?article=".$artnr;
			} elseif ($fi && $sv) {
			
				if ($link2_fi != "")
					$link = $link2_fi;
				else
					$link = "info_fi_se.php?article=".$artnr;
				
			} else {

				if ($link != "")
					$link = $link;
				else
					$link = "../info_mobil.php?article=".$artnr;
				
			}

			if ($bild == "") {

				if ($fi && !$sv) {
					$bild = "../../../pic/noimage_fi.jpg";
				} else {
					$bild = "../../../pic/noimage.jpg";
				}
			}
			
			if ($fi) {
				$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ');
			} else {
				$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ');
			}

			print "<tr>";

			if ($countrow > 0) {
				if ($fi && !$sv) {
					print "<td height=\"64\" width=\"64\" style=\"border-top: 1px solid #85000D\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Hinta: <b>$utprismoms EUR</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
					print "<td height=\"64\" width=\"64\" style=\"border-top: 1px solid #85000D\" align=\"right\"><font face=\"Verdana\" size=\"1\"><b>$utprismoms EUR</b><p><a href=\"javascript:modifyItems('$artnr')\"><img border=\"0\" src=\"/pic/01_fi.gif\"></a></td>\n";
				} else {
					print "<td height=\"64\" width=\"64\" style=\"border-top: 1px solid #85000D\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
					print "<td height=\"64\" width=\"64\" style=\"border-top: 1px solid #85000D\" align=\"right\"><font face=\"Verdana\" size=\"1\"><b>$utprismoms kr</b><p><a href=\"javascript:modifyItems('$artnr')\"><img border=\"0\" src=\"/pic/01.gif\"></a></td>\n";
				}
			} else {
				if ($fi && !$sv) {
					print "<td height=\"64\" width=\"64\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Hinta: <b>$utprismoms EUR</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
					print "<td height=\"64\" width=\"64\" align=\"right\"><font face=\"Verdana\" size=\"1\"><b>$utprismoms EUR</b><p><a href=\"javascript:modifyItems('$artnr')\"><img border=\"0\" src=\"/pic/01_fi.gif\"></a></td>\n";
				} else {
					print "<td height=\"64\" width=\"64\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Pris: <b>$utprismoms kr</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
					print "<td height=\"64\" width=\"64\" align=\"right\"><font face=\"Verdana\" size=\"1\"><b>$utprismoms kr</b><p><a href=\"javascript:modifyItems('$artnr')\"><img border=\"0\" src=\"/pic/01.gif\"></a></td>\n";
				}
			}

			print "</tr>";
			
			$countrow ++;

		}
		return $output;
	}

	function mostSoldMobilNew2($kategorier_id, $limit, $sv, $fi) {
		global $no;

		$int = 0;
		$select = "";
		$output = "";
		$countrow = 0;

			$select  = "SELECT mostSoldArticles.artnr, mostSoldArticles.antalManad1, Artiklar.link, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, ";
			$select .= "Artiklar.lagersaldo, Artiklar.bestallningsgrans, Artiklar.utpris, Artiklar.utpris_no, Tillverkare.tillverkare, ";
			$select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi, Moms.momssats_no ";
			$select .= "FROM Artiklar ";
			$select .= "INNER JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
			$select .= "INNER JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
			$select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
			$select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
			$select .= "WHERE Artiklar.kategori_id IN ($kategorier_id) ";

		if ($fi)
			$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
		else
			$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
		
		$select .= " ORDER BY antalManad1 DESC";	
		
		$select .= " LIMIT " . $limit;	
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $select;
		}
		
		$res = mysqli_query($select);

		while ($row = mysqli_fetch_array($res)) {
			$int += 1;
			extract ($row);
			if ($fi && !$sv) {
				
				if ($beskrivning_fi != "")
					$beskrivning = $beskrivning_fi;
				
				if ($link2_fi != "")
					$link = $link2_fi;
				else
					$link = "info_fi.php?article=".$artnr;
			} elseif ($fi && $sv) {
			
				if ($link2_fi != "")
					$link = $link2_fi;
				else
					$link = "info_fi_se.php?article=".$artnr;
				
			} else {

				if ($link != "")
					$link = $link;
				else
					$link = "../info.php?article=".$artnr;
				
			}

			if ($bild == "") {

				if ($fi && !$sv) {
					$bild = "../../../pic/noimage_fi.jpg";
				} else {
					$bild = "../../../pic/noimage.jpg";
				}
			}
			
			if ($fi) {
				$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . " EUR";
			} elseif ($no) {
				$utprismoms = number_format(($utpris_no + $utpris_no * $momssats_no), 0, ',', ' ') . " NOK";
			} else {
				$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . " SEK";
			}

			print "<tr>";

			if ($countrow > 0) {
				if ($fi && !$sv) {
					print "<td height=\"64\" width=\"64\" style=\"border-top: 1px solid #85000D\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Hinta: <b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
					print "<td height=\"64\" width=\"64\" style=\"border-top: 1px solid #85000D\" align=\"right\"><b>$utprismoms</b><a href=\"javascript:modifyItems('$artnr')\"><div style=\"float: right; margin-top: 15px;\"><div class=\"buy_butt_green\">" . l('Buy') . "</div></div></a></td>\n";
				} else {
					print "<td height=\"64\" width=\"64\" style=\"border-top: 1px solid #85000D\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Pris: <b>$utprismoms</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
					print "<td height=\"64\" width=\"64\" style=\"border-top: 1px solid #85000D\" align=\"right\"><b>$utprismoms</b><a href=\"javascript:modifyItems('$artnr')\"><div style=\"float: right; margin-top: 15px;\"><div class=\"buy_butt_green\">" . l('Buy') . "</div></div></a></td>\n";
				}
			} else {
				if ($fi && !$sv) {
					print "<td height=\"64\" width=\"64\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Hinta: <b>$utprismoms</b>')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
					print "<td height=\"64\" width=\"64\" align=\"right\"><b>$utprismoms</b><a href=\"javascript:modifyItems('$artnr')\"><div style=\"float: right; margin-top: 15px;\"><div class=\"buy_butt_green\">" . l('Buy') . "</div></div></a></td>\n";
				} else {
					print "<td height=\"64\" width=\"64\" align=\"center\"><a onmouseover=\"return escape('<b>$tillverkare $beskrivning</b><br><br>Pris: <b>$utprismoms</b> inkl. moms')\" href=\"" . $link . "\"><font size=\"1\" face=\"Verdana\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></a></td>\n";
					print "<td height=\"64\" width=\"64\" align=\"right\"><b>$utprismoms</b><a href=\"javascript:modifyItems('$artnr')\"><div style=\"float: right; margin-top: 15px;\"><div class=\"buy_butt_green\">" . l('Buy') . "</div></div></a></td>\n";
				}
			}

			print "</tr>";
			
			$countrow ++;

		}
		return $output;
	}

		function mostHushallFront() {
			global $fi, $sv, $no;

			$countrow = 0;

			echo "<div align=\"center\">";
			echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" width=\"135\" height=\"358\">";
			
			$select  = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.utpris, Artiklar.utpris_no, Tillverkare.tillverkare, ";
			$select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi, Moms.momssats_no ";
			$select .= "FROM Artiklar ";
			$select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
			$select .= "JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
			$select .= "LEFT JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
			$select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
			$select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
			$select .= "WHERE Kategori.kategori_id_parent IN(1000082,1000083,1000088,1000100,1000101,1000116,1000118) ";
			/*
			$select .= "WHERE Artiklar.artnr = 'TKA8011' ";
			$select .= "OR Artiklar.artnr = '033561' ";
			$select .= "OR Artiklar.artnr = 'TAS4014' ";
			// $select .= "OR Artiklar.artnr = 'TAS2001' ";
			// $select .= "OR Artiklar.artnr = 'TWK8613' ";
			$select .= "OR Artiklar.artnr = 'E6040602' ";
			// $select .= "OR Artiklar.artnr = '657217' ";
			$select .= "OR Artiklar.artnr = 'AE-110' ";
			*/
			if ($fi)
				$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
			else
				$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
			$select .= " ORDER BY antalManad1 DESC, Tillverkare.tillverkare ASC";	
			$select .= " LIMIT 5 ";	

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}
			
			$res = mysqli_query($select);

			while ($row = mysqli_fetch_array($res)) {
				$int += 1;
				extract ($row);
				
				if ($fi && !$sv) {
					if ($tillverkare != ".") {
						$showtitle = $tillverkare . " " . $beskrivning;
					} else {
						$showtitle = $beskrivning;
					}
				} else {
					if ($tillverkare != ".") {
						$showtitle = $tillverkare . " " . $beskrivning;
					} else {
						$showtitle = $beskrivning;
					}
				}
				if ($fi) {
					$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . " EUR";
				} elseif ($no) {
					$utprismoms = number_format(($utpris_no + $utpris_no * $momssats_no), 0, ',', ' ') . " NOK";
				} else {
					$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . " SEK";
				}

				echo "<tr>";
				if ($countrow > 0) {
					if ($fi && !$sv) {
						echo "<td class=\"mostsoldtdleft\"><a title=\"$showtitle\" href=\"/info_fi.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
						echo "<td class=\"mostsoldtd\"><span class=\"mostsoldprice\">$utprismoms</td>\n";
					} else {
						echo "<td class=\"mostsoldtdleft\"><a title=\"$showtitle\" href=\"/info.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
						echo "<td class=\"mostsoldtd\"><span class=\"mostsoldprice\">$utprismoms</td>\n";
					}
				} else {
					if ($fi && !$sv) {
						echo "<td class=\"mostsoldtdleft_no\"><a title=\"$showtitle\" href=\"/info_fi.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
						echo "<td class=\"mostsoldtdright_no\"><span class=\"mostsoldprice\">$utprismoms</td>\n";
					} else {
						echo "<td class=\"mostsoldtdleft_no\"><a title=\"$showtitle\" href=\"/info.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
						echo "<td class=\"mostsoldtdright_no\"><span class=\"mostsoldprice\">$utprismoms</span></td>\n";
					}
				}
				echo "</tr>";
				
				$countrow ++;
			}
			echo "</table>";
			echo "</div>";
		}

		function mostSoldCybairgunFront() {
			global $fi, $sv, $no;

			$countrow = 0;

			echo "<div align=\"center\">";
			echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" width=\"135\" height=\"358\">";
			
			$select  = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.kortinfo, Artiklar.bild, Artiklar.utpris, Artiklar.utpris_no, Tillverkare.tillverkare, ";
			$select .= "Artiklar_fi.beskrivning_fi, Artiklar_fi.link2_fi, Artiklar_fi.utpris_fi, Moms.momssats, Moms.momssats_fi, Moms.momssats_no ";
			$select .= "FROM Artiklar ";
			$select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
			$select .= "JOIN Kategori ON Artiklar.kategori_id = Kategori.kategori_id ";
			$select .= "LEFT JOIN mostSoldArticles ON mostSoldArticles.artnr = Artiklar.artnr ";
			$select .= "LEFT JOIN Artiklar_fi ON Artiklar_fi.artnr_fi = Artiklar.artnr ";
			$select .= "LEFT JOIN Moms ON Moms.moms_id = Artiklar.momskod ";
			$select .= "WHERE Kategori.kategori_id IN(684,685,686,687,688,689,690,691,692,693,694,695,568,567,561,560,548,535,1000125) ";
			/*
			$select .= "WHERE Artiklar.artnr = 'TKA8011' ";
			$select .= "OR Artiklar.artnr = '033561' ";
			$select .= "OR Artiklar.artnr = 'TAS4014' ";
			// $select .= "OR Artiklar.artnr = 'TAS2001' ";
			// $select .= "OR Artiklar.artnr = 'TWK8613' ";
			$select .= "OR Artiklar.artnr = 'E6040602' ";
			// $select .= "OR Artiklar.artnr = '657217' ";
			$select .= "OR Artiklar.artnr = 'AE-110' ";
			*/
			if ($fi)
				$select .= " AND (Artiklar_fi.utgangen_fi=0 OR Artiklar_fi.lagersaldo_fi > 0) AND NOT (Artiklar.demo = -1 OR ej_med_fi = -1) ";
			else
				$select .= " AND (Artiklar.utgangen=0 OR Artiklar.lagersaldo > 0) AND NOT (Artiklar.demo = -1 OR ej_med = -1) ";
			$select .= " ORDER BY antalManad1 DESC";	
			$select .= " LIMIT 5 ";	

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}
			
			$res = mysqli_query($select);

			while ($row = mysqli_fetch_array($res)) {
				$int += 1;
				extract ($row);
				
				if ($fi && !$sv) {
					if ($tillverkare != ".") {
						$showtitle = $tillverkare . " " . $beskrivning_fi;
					} else {
						$showtitle = $beskrivning_fi;
					}
				} else {
					if ($tillverkare != ".") {
						$showtitle = $tillverkare . " " . $beskrivning;
					} else {
						$showtitle = $beskrivning;
					}
				}
				if ($fi) {
					$utprismoms = number_format(($utpris_fi + $utpris_fi * $momssats_fi), 0, ',', ' ') . " EUR";
				} elseif ($no) {
					$utprismoms = number_format(($utpris_no + $utpris_no * $momssats_no), 0, ',', ' ') . " NOK";
				} else {
					$utprismoms = number_format(($utpris + $utpris * $momssats), 0, ',', ' ') . " SEK";
				}

				echo "<tr>";
				if ($countrow > 0) {
					if ($fi && !$sv) {
						echo "<td class=\"mostsoldtdleft\"><a title=\"$showtitle\" href=\"/info_fi.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
						echo "<td class=\"mostsoldtd\"><span class=\"mostsoldprice\">$utprismoms</td>\n";
					} else {
						echo "<td class=\"mostsoldtdleft\"><a title=\"$showtitle\" href=\"/info.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
						echo "<td class=\"mostsoldtd\"><span class=\"mostsoldprice\">$utprismoms</td>\n";
					}
				} else {
					if ($fi && !$sv) {
						echo "<td class=\"mostsoldtdleft_no\"><a title=\"$showtitle\" href=\"/info_fi.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
						echo "<td class=\"mostsoldtdright_no\"><span class=\"mostsoldprice\">$utprismoms</td>\n";
					} else {
						echo "<td class=\"mostsoldtdleft_no\"><a title=\"$showtitle\" href=\"/info.php?article=$artnr\"><img border=\"0\" src=\"/thumbs/medium/bilder/". $bild . "\"></td>\n";
						echo "<td class=\"mostsoldtdright_no\"><span class=\"mostsoldprice\">$utprismoms</span></td>\n";
					}
				}
				echo "</tr>";
				
				$countrow ++;
			}
			echo "</table>";
			echo "</div>";
		}
		
	// *************************** NEDAN BÖRJAR ALL ADMINISTRATIV KOD ************************************

	function getFrontBannerAdmin($section,$site) {

	unset ($lastOption);

	$select  = "SELECT * FROM ( ";
	$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM frontAdmin ";
	// $select .= "WHERE  frontDateFrom < getdate() AND frontDateTo > getdate() AND NOT (frontPicture is Null) AND NOT (frontArtNr is Null AND frontLinc is Null) ";
	$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) AND NOT (frontArtNr is Null AND frontLinc is Null) ";
	$select .= "AND frontSection = '" . $section . "' AND frontSite = '" . $site . "' ";
	$select .= " ) AS tmp ORDER BY RAND() ";
	// echo $select;
	// exit;

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
		
			if ($frontAllowNull == 0 && $frontArtNr != "") {
		
				if ($this->check_store_status($frontArtNr)) {
				
				echo "<a href=\"" . $_SERVER['PHP_SELF'] ."?section=$section\">";
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
		
				echo "<a href=\"" . $_SERVER['PHP_SELF'] ."?section=$section\">";
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
			echo "<a href=\"" . $_SERVER['PHP_SELF'] ."?section=$section\">";
			if ($section == 99) {
				echo "<img border=\"0\" src=\"red_mitt.jpg\"></a>";
			} else {
				echo "<img border=\"0\" src=\"red.jpg\"></a>";
			}
			
			unset ($lastOption);
			
			}

		} else {
		
		// Här är ingen fronbanner aktiverad. Då visas röd status / Sjabo
		echo "<a href=\"" . $_SERVER['PHP_SELF'] ."?section=$section\">";
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
	// $select .= "WHERE frontDateFrom < getdate() AND frontDateTo > getdate() AND frontSection = '" . $status . "' AND frontSite = '" . $site . "' ";
	$select .= "WHERE frontDateFrom < now() AND frontDateTo > now() AND frontSection = '" . $status . "' AND frontSite = '" . $site . "' ";
	$select .= "ORDER BY frontDateTo ASC, frontArtNr ASC ";

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
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
			
			$frontDateFrom = preg_replace('/:[0-9][0-9][0-9]/','', $frontDateFrom);
			$frontDateTo = preg_replace('/:[0-9][0-9][0-9]/','', $frontDateTo);
			$beskrivning = $this->getbeskrivningArt($frontArtNr);
			$leverantor  = $this->getLeverantorName($frontLeverantor);
			$aterstar = $this->getDaysLeft($frontDateTo);
			$lagersaldo = $this->artikel_status_store($frontArtNr);

			echo "<tr>\n";
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i", strtotime($frontDateFrom)) . "</td>\n";
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i", strtotime($frontDateTo)) . "</td>\n";
			if ($aterstar < 4) {
			echo "<td bgcolor=\"$rowcolor\" align=\"right\"><font face=\"Verdana\" size=\"1\" color=\"red\"><b>" . $aterstar . " dagar&nbsp;&nbsp;</b></td>\n";
			} else {
			echo "<td bgcolor=\"$rowcolor\" align=\"right\"><font face=\"Verdana\" size=\"1\">" . $aterstar . " dagar&nbsp;&nbsp;</td>\n";
			}
			if (!eregi(".php$", $frontPicture)) {
				if ($frontSection == 1) {
					if ($site == 7 || $site == 8) {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=500;return escape('<img border=\'0\' src=\'/start_mobiili/$frontPicture\'>')\">" . $frontPicture . "</a></td>\n";
					} else {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=500;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>\n";
					}
				} elseif ($frontSection == 99) {
					if ($site == 7 || $site == 8) {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=850;return escape('<img border=\'0\' src=\'/start_mobiili/$frontPicture\'>')\">" . $frontPicture . "</a></td>\n";
					} else {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=850;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>\n";
					}
				} else {
					if ($site == 7 || $site == 8) {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;return escape('<img border=\'0\' src=\'/start_mobiili/$frontPicture\'>')\">" . $frontPicture . "</a></td>\n";
					} else {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>\n";
					}
				}
			} else {
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a href=\"/$frontPicture\" target=\"_blank\">" . $frontPicture . "</a></td>\n";
			}
			if ($frontArtNr != "") {
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=450;return escape('<b>$beskrivning</b>')\" href=\"/info.php?article=$frontArtNr\" target=\"_blank\">" . $frontArtNr . "</a></td>\n";
			} else {
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
			}
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a href=\"/$frontLinc\" target=\"_blank\">" . $frontLinc . "</a></td>\n";
			if ($frontAllowNull == -1) {
			echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">Ja" . $showutgangen . "</td>\n";
			} else {
				if ($lagersaldo > 0) {
					echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">Nej (" . $lagersaldo . ")" . $showutgangen . "</td>\n";
				} else {
					echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">Nej" . $showutgangen . "</td>\n";
				}
			}
			if ($frontAllowNull == -1 || ($frontAllowNull == 0 && $this->check_store_status($frontArtNr))) {
			echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=150;this.T_BGCOLOR='#FFFF00';return escape('<b>Denna visas just nu!</b>')\"><img border=\"0\" src=\"status_green.jpg\"></a></td>\n";
			} else {
			echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=150;this.T_BGCOLOR='#FFFF00';return escape('<b>Denna visas EJ nu!</b>')\"><img border=\"0\" src=\"status_red.jpg\"></a></td>\n";
			}
			if ($frontPrio == -1) {
			echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;this.T_BGCOLOR='#FFFF00';return escape('<b>Detta är en prioriterad frontbanner!</b>')\"><img border=\"0\" src=\"star.gif\"></a></td>\n";
			} else {
			echo "<td bgcolor=\"#FFFFFF\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>\n";
			}
			if ($frontLeverantor > 0) {
			echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;return escape('<b>$leverantor</b>')\">Ja</a></td>\n";
			} else {
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>\n";
			}
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . $frontComment . "</td>";
			echo "<td bgcolor=\"$rowcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">" . $frontCreatedBy . "</td>\n";
			echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"" . $_SERVER['PHP_SELF'] ."?copypost=" . $frontID . "\">Kopiera</a></b></td>\n";
			echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"" . $_SERVER['PHP_SELF'] ."?change=" . $frontID . "\">Ändra</a></b></td>\n";
			echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"" . $_SERVER['PHP_SELF'] ."?confirmdelete=" . $frontID . "&section=" . $frontSection . "\">Ta bort</a></b></td>\n";
			echo "</tr>";
		
			endwhile;
			
		} else {
		
		echo "<tr>\n";
		echo "<td colspan=\"11\"><span style=\"background-color: #FF0000\"><font face=\"Verdana\" size=\"1\" color=\"#FFFFFF\"><b>Inga poster aktiva</b></span></td>\n";
		echo "</tr>\n";
		
		}

	}

	function getBannerAdminPlan($status,$site) {

	$select  = "SELECT frontID, frontSection, frontDateFrom, frontDateTo, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontLeverantor, frontComment, frontCreatedBy, frontPrio FROM frontAdmin ";
	// $select .= "LEFT JOIN Leverantor ON frontAdmin.frontLeverantor = Leverantor.grossist_id ";
	// $select .= "WHERE frontDateFrom > getdate() AND frontSection = '" . $status . "' AND frontSite = '" . $site . "' ";
	$select .= "WHERE frontDateFrom > now() AND frontSection = '" . $status . "' AND frontSite = '" . $site . "' ";
	$select .= "ORDER BY frontDateTo ASC, frontArtNr ASC ";

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			$rowcolor = "#CCFFFF";

			if ($frontArtNr != "" && $this->artikel_status_utgangen($frontArtNr)) {
				$showutgangen = "<a onMouseOver=\"this.T_WIDTH=200;this.T_BGCOLOR='#FF0000';this.T_FONTCOLOR='#FFFFFF';return escape('<b>Detta är en utgången produkt!</b>')\"><font color='85000D'>&nbsp;utg</font></a>";
			} else {
				$showutgangen = "&nbsp;";
			}

			$frontDateFrom = preg_replace('/:[0-9][0-9][0-9]/','', $frontDateFrom);
			$frontDateTo = preg_replace('/:[0-9][0-9][0-9]/','', $frontDateTo);
			$beskrivning = $this->getbeskrivningArt($frontArtNr);
			$leverantor  = $this->getLeverantorName($frontLeverantor);

			echo "<tr>";
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i", strtotime($frontDateFrom)) . "</td>";
			echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i", strtotime($frontDateTo)) . "</td>";
			echo "<td bgcolor=\"$rowcolor\" align=\"right\"><font face=\"Verdana\" size=\"1\">" . $this->getDaysLeft($frontDateFrom) . " dagar&nbsp;&nbsp;</td>";
			if (!eregi(".php$", $frontPicture)) {
				if ($frontSection == 1) {
					if ($site == 7 || $site == 8) {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=500;return escape('<img border=\'0\' src=\'/start_mobiili/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
					} else {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=500;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
					}
				} elseif ($frontSection == 99) {
					if ($site == 7 || $site == 8) {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=850;return escape('<img border=\'0\' src=\'/start_mobiili/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
					} else {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=850;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
					}
				} else {
					if ($site == 7 || $site == 8) {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;return escape('<img border=\'0\' src=\'/start_mobiili/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
					} else {
						echo "<td bgcolor=\"$rowcolor\"><font face=\"Verdana\" size=\"1\"><a onMouseOver=\"this.T_WIDTH=250;return escape('<img border=\'0\' src=\'/start3/$frontPicture\'>')\">" . $frontPicture . "</a></td>";
					}
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
			echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"" . $_SERVER['PHP_SELF'] ."?copypost=" . $frontID . "\">Kopiera</a></b></td>";
			echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"" . $_SERVER['PHP_SELF'] ."?change=" . $frontID . "\">Ändra</a></b></td>";
			echo "<td bgcolor=\"#FFFFFF\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"" . $_SERVER['PHP_SELF'] ."?confirmdelete=" . $frontID . "&section=" . $frontSection . "\">Ta bort</a></b></td>";
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
	$res = mysqli_query($this->conn_my, $select);

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
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

		return true;
			
		} else {
		
		return false;
		
		}

	}	

	function getWarning($site) {

	// $select  = "SELECT * FROM frontAdmin WHERE DateDiff(d, getdate(), frontDateTo) < 4 AND frontDateFrom < getdate() AND frontDateTo > getdate() AND frontSite = '" . $site . "' ";
	$select  = "SELECT * ";
	$select  .= "FROM frontAdmin ";
	$select  .= "WHERE DateDiff(frontDateTo,now()) < 4 AND frontDateFrom < now() AND frontDateTo > now() AND frontSite = '" . $site . "' ";
	// echo $select;
	// exit;

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			return true;
		
		} else {
		
			return false;
		}

	}

	function displayWarning($site) {

	$rowcolor = true;

	$select  = "SELECT frontSection, count(frontSection) AS Antal ";
	$select  .= "FROM frontAdmin ";
	$select  .= "WHERE DateDiff(frontDateTo,now()) < 4 AND frontDateFrom < now() AND frontDateTo > now() AND frontSite = '" . $site . "' ";
	$select  .= "GROUP BY frontSection ";
	$select  .= "ORDER BY frontSection ASC ";

	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			if ($rowcolor == true) {
				$backcolor = "#FFFF00";
			} else {
				$backcolor = "#FFFF99";
			}
			
			echo "<tr>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">" . $frontSection . "</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">" . $Antal . " st</td>";
			echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b><a href=\"" . $_SERVER['PHP_SELF'] ."?section=" . $frontSection . "\">Visa</a></b></td>";
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

	$res = mysqli_query($this->conn_my, $select);

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

	$res = mysqli_query($this->conn_my, $select);

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

	$res = mysqli_query($this->conn_my, $select);

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

	$res = mysqli_query($this->conn_my2, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		echo "<option value=\"$sign\"";
			
		if ($addcreatedby == $sign) {
			echo " selected";
		}
			
		echo ">" . $namn . "</option>";
			
		
		// endwhile;

		}

	}

	function BannerAdminDelete($delete,$section) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("DELETE FROM frontAdmin WHERE frontID = '" . $delete . "'");

		$updt = "DELETE FROM frontAdmin WHERE frontID = '" . $delete . "'";

		$res = mysqli_query($conn_my, $updt);

		header("Location: update_banner.php?section=$section");

	}

	function FrontAdminDelete($delete,$section) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("DELETE FROM frontAdmin WHERE frontID = '" . $delete . "'");

		$updt = "DELETE FROM frontAdmin WHERE frontID = '" . $delete . "'";

		$res = mysqli_query($conn_my, $updt);

		header("Location: update_front.php?section=$section");

	}

	function BannerAdminAdd($addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addsite,$addprio) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontLeverantor,frontCreatedBy,frontSite,frontPrio) VALUES ('$addsection','$addfrom','$addto','$addpicture','$addartnr','$addlinc','$addstore','$addcomment','$addleverantor','$addcreatedby','$addsite','$addprio') ");

		$updt = "INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontLeverantor,frontCreatedBy,frontSite,frontPrio) VALUES ('$addsection','$addfrom','$addto','$addpicture','$addartnr','$addlinc','$addstore','$addcomment','$addleverantor','$addcreatedby','$addsite','$addprio')";

		$res = mysqli_query($conn_my, $updt);

		if ($addsite != 2 && $addsite != 3 && $addsite != 7 && $addsite != 14) { // om det inte är en finsk banner så skickar vi ut mail om det....
			$this->sendFrontMess($addcreatedby,$addsection,$addsite,$addpicture);
		}

		header("Location: update_banner.php?section=$addsection");

	}

	function FrontAdminAdd($addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addsite,$addprio) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontLeverantor,frontCreatedBy,frontSite,frontPrio) VALUES ('$addsection','$addfrom','$addto','$addpicture','$addartnr','$addlinc','$addstore','$addcomment','$addleverantor','$addcreatedby','$addsite','$addprio') ");

		$updt = "INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontLeverantor,frontCreatedBy,frontSite,frontPrio) VALUES ('$addsection','$addfrom','$addto','$addpicture','$addartnr','$addlinc','$addstore','$addcomment','$addleverantor','$addcreatedby','$addsite','$addprio')";

		$res = mysqli_query($conn_my, $updt);

		if ($addsite != 2 && $addsite != 3 && $addsite != 7 && $addsite != 14) { // om det inte är en finsk banner så skickar vi ut mail om det....
			$this->sendFrontMess($addcreatedby,$addsection,$addsite,$addpicture);
		}

		header("Location: update_front.php?section=$addsection");

	}

	function BannerAdminChange($addid,$addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addprio) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("UPDATE frontAdmin  SET frontSection = '$addsection', frontDateFrom = '$addfrom',frontDateTo = '$addto',frontPicture = '$addpicture',frontArtNr = '$addartnr',frontLinc = '$addlinc',frontAllowNull = '$addstore',frontComment = '$addcomment',frontLeverantor = '$addleverantor',frontCreatedBy = '$addcreatedby',frontPrio = '$addprio' WHERE frontID = '$addid' ");

		$updt = "UPDATE frontAdmin  SET frontSection = '$addsection', frontDateFrom = '$addfrom',frontDateTo = '$addto',frontPicture = '$addpicture',frontArtNr = '$addartnr',frontLinc = '$addlinc',frontAllowNull = '$addstore',frontComment = '$addcomment',frontLeverantor = '$addleverantor',frontCreatedBy = '$addcreatedby',frontPrio = '$addprio' WHERE frontID = '$addid'";

		$res = mysqli_query($conn_my, $updt);

		header("Location: update_banner.php?section=$addsection");

	}

	function FrontAdminChange($addid,$addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addprio) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("UPDATE frontAdmin  SET frontSection = '$addsection', frontDateFrom = '$addfrom',frontDateTo = '$addto',frontPicture = '$addpicture',frontArtNr = '$addartnr',frontLinc = '$addlinc',frontAllowNull = '$addstore',frontComment = '$addcomment',frontLeverantor = '$addleverantor',frontCreatedBy = '$addcreatedby',frontPrio = '$addprio' WHERE frontID = '$addid' ");

		$updt = "UPDATE frontAdmin  SET frontSection = '$addsection', frontDateFrom = '$addfrom',frontDateTo = '$addto',frontPicture = '$addpicture',frontArtNr = '$addartnr',frontLinc = '$addlinc',frontAllowNull = '$addstore',frontComment = '$addcomment',frontLeverantor = '$addleverantor',frontCreatedBy = '$addcreatedby',frontPrio = '$addprio' WHERE frontID = '$addid'";

		$res = mysqli_query($conn_my, $updt);

		header("Location: update_front.php?section=$addsection");

	}

	function sendFrontMess($addcreatedby,$addsection,$addsite,$addpicture) {

		if ($addsite == 2) {
			$country = "den finska";
		} elseif ($addsite == 3) {
			$country = "den finsk/svenska";
		} elseif ($addsite == 7) {
			$country = "finska mobil";
		} elseif ($addsite == 9) {
			$country = "lagershop";
		} else {
			$country = "svenska";
		}
		
		$addcreatedby = strtolower($addcreatedby);

		if ($addsite == 2 || $addsite == 3 || $addsite == 7) { // är det finland så skickar vi bara till börje
			$recipient = "bs";
		} else {
			$recipient = "front";
		}
		// $recipient .= " ss";
		
		$uppdatum = date("j/n-Y H:i:s", time());

		$subj = $uppdatum . " Ny fronbanner upplagd!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "Jag har lagt upp en ny banner på " . $country . " sajten, sektion " . $addsection . ". Vänligen kontrollera hur den ser ut.\n\n";
		
		// if (eregi(".gif", $addpicture)) {
		if (preg_match("/\.gif/i", $addpicture) || preg_match("/\.jpg/i", $addpicture) || preg_match("/\.png/i", $addpicture)) {
		
			if ($addsite == 7 || $addsite == 8) {
			
				$text1 .= "Se www.cyberphoto.se/start_mobiili/" . $addpicture . "";
			
			} else {
			
				$text1 .= "Se www.cyberphoto.se/start3/" . $addpicture . "";

			}
		}
		
		mail($recipient, $subj, $text1, $extra);

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

	$res = mysqli_query($this->conn_my, $select);

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

	$res = mysqli_query($this->conn_my, $select);

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

	$res = mysqli_query($this->conn_my, $select);

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

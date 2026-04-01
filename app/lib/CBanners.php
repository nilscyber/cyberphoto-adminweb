<?php

Class CBanners {

	function __construct() {

	}

	/**
	 * Returnerar korrekt src-sökväg för en banner-bild.
	 * - Nyuppladdade bilder lagras som "/banner_images/filnamn.jpg" (börjar med /) → används direkt.
	 * - Gamla bilder lagras som bara "filnamn.jpg" → serveras från /banners/ på admin-servern.
	 */
	private function buildImgSrc($frontPicture) {
		if (strncmp($frontPicture, '/', 1) === 0 || strncmp($frontPicture, 'http', 4) === 0) {
			return htmlspecialchars($frontPicture, ENT_QUOTES, 'UTF-8');
		}
		return '/banners/' . htmlspecialchars($frontPicture, ENT_QUOTES, 'UTF-8');
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
		$select  = "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontSite, frontSiteNew ";
		$select .= "FROM cyberphoto.frontAdmin ";
		$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
		$select .= "AND frontSection = '" . $section . "' AND frontSiteNew = '" . $site . "' AND frontPrio = -1 ";
		$select .= "ORDER BY frontID DESC ";
		// $select .= " ) AS tmp ORDER BY RAND() ";

		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)) {
			
					extract($row);
					
					$analyticsSite = $this->getSiteForAnalytics($frontSiteNew);
				
					if ($frontAllowNull == 0 && $frontArtNr != "") {
				
						if ($this->check_store_status($frontArtNr)) {

                        	if (preg_match("/\.php$/", $frontPicture)) {
						
								ob_start();
								include("/web/www/$frontPicture");
								$cont = ob_get_contents();
								ob_end_clean();
						
								echo $cont;
						
							} else {


								if ($frontLinc != "") {
								
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
							
								} else {
								
									if ($frontArtNr != "") {
									
										$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
										
										// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
										echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
									
									} else {
									
										// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
										echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

									}
									
								}
							
								if ($frontArtNr != "") {
								
									$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
								
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
							
								} else {
								
									echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
							
								}
							
							}
						
						unset ($lastOption);
						
						break;
						
						} else {
						
						$lastOption = 1;
						
						}

					} else {

						if (preg_match("/\.php$/", $frontPicture)) {
						
							ob_start();
							include("/web/www/$frontPicture");
							$cont = ob_get_contents();
							ob_end_clean();
						
							echo $cont;
						
						} else {

							if ($frontLinc != "") {
								
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
							
							} else {
							
								if ($frontArtNr != "") {
									
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
										
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
									
								} else {
									
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

								}
									
							}
							
							if ($frontArtNr != "") {
								
								$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
								
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
							
							} else {
								
								echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
							
							}
						
						}
				
					unset ($lastOption);
					
					break;
					
					}
			
				}
				
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
		$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontSite, frontSiteNew ";
		$select .= "FROM cyberphoto.frontAdmin ";
		$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
		$select .= "AND frontSection = '" . $section . "' AND frontSiteNew = '" . $site . "' ";
		$select .= " ) AS tmp ORDER BY RAND() ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)) {
			
					extract($row);
				
					$analyticsSite = $this->getSiteForAnalytics($frontSiteNew);

					if ($frontAllowNull == 0 && $frontArtNr != "") {
				
						if ($this->check_store_status($frontArtNr)) {
				
							if (preg_match("/\.php$/", $frontPicture)) {
						
								if ($frontID == 7689 && $frameless) {
									ob_start();
									include("samsung_section12.php");
									$cont = ob_get_contents();
									ob_end_clean();
									
									$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
									
								} else {
									
									ob_start();
									include("/web/www/$frontPicture");
									$cont = ob_get_contents();
									ob_end_clean();
								
								}
						
								echo $cont;
						
							} else {


								if ($frontLinc != "") {
								
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
							
								} else {
								
									if ($frontArtNr != "") {
									
										$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
										
										// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
										echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
									
									} else {
									
										// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
										echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

									}
									
								}
							
								if ($frontArtNr != "") {
								
									$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
								
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
							
								} else {
								
									echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
							
								}
							
							}
						
						unset ($lastOption);
						
						break;
						
						} else {
						
						$lastOption = 1;
						
						}

					} else {
				
						if (preg_match("/\.php$/", $frontPicture)) {
						
							if ($frontID == 7689 && $frameless) {
								ob_start();
								include("samsung_section12.php");
								$cont = ob_get_contents();
								ob_end_clean();

								$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
								
							} else {
							
								ob_start();
								include("/web/www/$frontPicture");
								$cont = ob_get_contents();
								ob_end_clean();
							
							}
						
							echo $cont;
						
						} else {

							if ($frontLinc != "") {
								
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
							
							} else {
							
								if ($frontArtNr != "") {
									
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
										
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
									
								} else {
									
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

								}
									
							}
							
							if ($frontArtNr != "") {
								
								$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
								
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
							
							} else {
								
								echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";

							}
						
						}
				
					unset ($lastOption);
					
					break;
					
					}
			
				}
				
				if ($lastOption == 1) {
				
					if ($site > 1500) { // tar bort nödbilderna om det inte är fronten
					
						// Här är alla alternativ slut. Då visas "nödbilderna" / Sjabo
						if (CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) {
							$this->getFrontBannerPrio($section,1);
						} else {
							$this->getFrontBannerEnd($section,$site);
						}
					}
				
				unset ($lastOption);
				
				}

			}

	}	

	function getBannerWithFramePrio($section,$site,$random,$floatleft,$limit) {
	
		unset($lastOption);
	
		$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontSite, frontSiteNew ";
		$select .= "FROM cyberphoto.frontAdmin ";
		$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
		$select .= "AND frontSection = '" . $section . "' AND frontSiteNew = '" . $site . "' AND frontPrio = -1 ";
		$select .= "ORDER BY frontID DESC ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
			
				$analyticsSite = $this->getSiteForAnalytics($frontSiteNew);

				if ($frontAllowNull == 0 && $frontArtNr != "") {
			
					if ($this->check_store_status($frontArtNr)) {
					
						if ($floatleft) {
							echo "<div class=\"banner_with_line floatleft\">\n";
						} else {
							echo "<div class=\"banner_with_line\">\n";
						}
			
						if (preg_match("/\.php$/", $frontPicture)) {
					
							if ($frontID == 7689 && $frameless) {
								ob_start();
								include("samsung_section12.php");
								$cont = ob_get_contents();
								ob_end_clean();
								
								$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
								
							} else {
								
								if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
									echo "jepp";
								}
								
								ob_start();
								include("/web/www/$frontPicture");
								$cont = ob_get_contents();
								ob_end_clean();
							
							}
					
							echo $cont;
					
						} else {


							if ($frontLinc != "") {
							
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
						
							} else {
							
								if ($frontArtNr != "") {
								
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
								} else {
								
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

								}
								
							}
						
							if ($frontArtNr != "") {
							
								$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
							
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
						
							} else {
							
								echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
						
							}
						
						}
						
						echo "</div>\n";
						
						unset($lastOption);
						break;
					
					} else {
						$lastOption = 1;
					}

				} else {
				
					if ($floatleft) {
						echo "<div class=\"banner_with_line floatleft\">\n";
					} else {
						echo "<div class=\"banner_with_line\">\n";
					}
       				
       				if (preg_match("/\.php$/", $frontPicture)) {
       					
						if ($frontID == 7689 && $frameless) {
							ob_start();
							include("samsung_section12.php");
							$cont = ob_get_contents();
							ob_end_clean();

							$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
							
						} else {
						
							if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
								echo $frontPicture;
							}
							
							ob_start();
							if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
								include("/web/www/$frontPicture");
							} else {
								include("/web/www/$frontPicture");
							}
							$cont = ob_get_contents();
							ob_end_clean();
						
						}
					
						echo $cont;
					
					} else {

						if ($frontLinc != "") {
							
							// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
							echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
						
						} else {
						
							if ($frontArtNr != "") {
								
								$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
							} else {
								
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

							}
								
						}
						
						if ($frontArtNr != "") {
							
							$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
							
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
						
						} else {
							
							echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";

						}
					
					}
			
					echo "</div>\n";
					
					unset($lastOption);
					break;
						
				}

			}
			
			if ($lastOption == 1) {
				$this->getBannerWithFrame($section,$site,$random,$floatleft,$limit);
				unset ($lastOption);
			}
			
		} else {
			$this->getBannerWithFrame($section,$site,$random,$floatleft,$limit);
		}

	}	
	
	function getBannerWithFrame($section,$site,$random,$floatleft,$limit) {
		global $frameless;
		
        $select = "";
        $count_banner = 1;
        
		if ($random) {
			$select  = "SELECT * FROM ( ";
		}
		$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontSite, frontSiteNew ";
		$select .= "FROM cyberphoto.frontAdmin ";
		$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
		$select .= "AND frontSection = '" . $section . "' AND frontSiteNew = '" . $site . "' ";
		if ($random) {
			$select .= " ) AS tmp ORDER BY RAND() ";
		} else {
			$select .= "ORDER BY frontSort DESC ";
		}
		/*
		if ($limit > 0) {
			$select .= "LIMIT $limit ";
		}
		*/
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
			
				$analyticsSite = $this->getSiteForAnalytics($frontSiteNew);

				if ($frontAllowNull == 0 && $frontArtNr != "") {
			
					if ($this->check_store_status($frontArtNr)) {
					
						if ($floatleft) {
							echo "<div class=\"banner_with_line floatleft\">\n";
						} else {
							echo "<div class=\"banner_with_line\">\n";
						}
			
						if (preg_match("/\.php$/", $frontPicture)) {
					
							if ($frontID == 7689 && $frameless) {
								ob_start();
								include("samsung_section12.php");
								$cont = ob_get_contents();
								ob_end_clean();
								
								$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
								
							} else {
								
								if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
									echo "jepp";
								}
								
								ob_start();
								include("/web/www/$frontPicture");
								$cont = ob_get_contents();
								ob_end_clean();
							
							}
					
							echo $cont;
					
						} else {


							if ($frontLinc != "") {
							
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
						
							} else {
							
								if ($frontArtNr != "") {
								
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
								} else {
								
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

								}
								
							}
						
							if ($frontArtNr != "") {
							
								$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
							
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
						
							} else {
							
								echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
						
							}
						
						}
						
						echo "</div>\n";
						
						if ($count_banner == $limit) {
							break;
						} else {
							$count_banner++;
						}
					
					}

				} else {
				
					if ($floatleft) {
						echo "<div class=\"banner_with_line floatleft\">\n";
					} else {
						echo "<div class=\"banner_with_line\">\n";
					}
       				
       				if (preg_match("/\.php$/", $frontPicture)) {
       					
						if ($frontID == 7689 && $frameless) {
							ob_start();
							include("samsung_section12.php");
							$cont = ob_get_contents();
							ob_end_clean();

							$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
							
						} else {
						
							if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
								echo $frontPicture;
							}
							
							ob_start();
							if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
								include("/web/www/$frontPicture");
							} else {
								include("/web/www/$frontPicture");
							}
							$cont = ob_get_contents();
							ob_end_clean();
						
						}
					
						echo $cont;
					
					} else {

						if ($site == 1440 && $section == 50) {
							echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"$frontLinc\">";
						} elseif ($frontLinc != "") {
							
							// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
							echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
						
						} else {
						
							if ($frontArtNr != "") {
								
								$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
							} else {
								
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

							}
								
						}
						
						if ($frontArtNr != "") {
							
							$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
							
								echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
						
						} else {
							
							echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";

						}
					
					}
			
					echo "</div>\n";
					
					if ($count_banner == $limit) {
						break;
					} else {
						$count_banner++;
					}
						
				}

			}
			
		}

	}	
	function getBannerNoFramePrio($section,$site,$random,$float,$limit) {
	
		unset($lastOption);
	
		$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontSite, frontSiteNew ";
		$select .= "FROM cyberphoto.frontAdmin ";
		$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
		$select .= "AND frontSection = '" . $section . "' AND frontSiteNew = '" . $site . "' AND frontPrio = -1 ";
		$select .= "ORDER BY frontID DESC ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
			
				$analyticsSite = $this->getSiteForAnalytics($frontSiteNew);

				if ($frontAllowNull == 0 && $frontArtNr != "") {
			
					if ($this->check_store_status($frontArtNr)) {
					
						if ($float == "left") {
							echo "<div class=\"banner_nostyle floatleft\">\n";
						} elseif ($float == "right") {
							echo "<div class=\"banner_nostyle floatright\">\n";
						} else {
							echo "<div class=\"banner_nostyle\">\n";
						}
			
						if (preg_match("/\.php$/", $frontPicture)) {
					
							if ($frontID == 7689 && $frameless) {
								ob_start();
								include("samsung_section12.php");
								$cont = ob_get_contents();
								ob_end_clean();
								
								$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
								
							} else {
								
								if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
									echo "jepp";
								}
								
								ob_start();
								include("/web/www/$frontPicture");
								$cont = ob_get_contents();
								ob_end_clean();
							
							}
					
							echo $cont;
					
						} else {


							if ($frontLinc != "") {
							
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
						
							} else {
							
								if ($frontArtNr != "") {
								
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
								} else {
								
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

								}
								
							}
						
							if ($frontArtNr != "") {
							
								$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
							
								if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/banners/$frontPicture\"></a>";
								} else {
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
								}
						
							} else {
							
								if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
									echo "<img border=\"0\" src=\"/banners/$frontPicture\"></a>";
								} else {
									echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
								}
						
							}
						
						}
						
						echo "</div>\n";
						
						unset($lastOption);
						break;
					
					} else {
						$lastOption = 1;
					}

				} else {
				
					if ($float == "left") {
						echo "<div class=\"banner_nostyle floatleft\">\n";
					} elseif ($float == "right") {
						echo "<div class=\"banner_nostyle floatright\">\n";
					} else {
						echo "<div class=\"banner_nostyle\">\n";
					}
       				
       				if (preg_match("/\.php$/", $frontPicture)) {
       					
						if ($frontID == 7689 && $frameless) {
							ob_start();
							include("samsung_section12.php");
							$cont = ob_get_contents();
							ob_end_clean();

							$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
							
						} else {
						
							if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
								echo $frontPicture;
							}
							
							ob_start();
							if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
								include("/web/www/$frontPicture");
							} else {
								include("/web/www/$frontPicture");
							}
							$cont = ob_get_contents();
							ob_end_clean();
						
						}
					
						echo $cont;
					
					} else {

						if ($frontLinc != "") {
							
							// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
							echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
						
						} else {
						
							if ($frontArtNr != "") {
								
								$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
							} else {
								
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

							}
								
						}
						
						if ($frontArtNr != "") {
							
							$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
							
								if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/banners/$frontPicture\"></a>";
								} else {
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
								}
						
						} else {
							
							if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
								echo "<img border=\"0\" src=\"/banners/$frontPicture\"></a>";
							} else {
								echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
							}

						}
					
					}
			
					echo "</div>\n";
					
					unset($lastOption);
					break;
						
				}

			}
			
			if ($lastOption == 1) {
				if ($section == 204) {
					$this->getBannerNoFrame(204,1,true,"no",1);
				} elseif ($section == 202) {
					$this->getBannerNoFrame(202,1,true,"no",1);
				} elseif ($section == 202 || $section == 203) {
					$this->getBannerNoFrame(201,1,true,"no",1);
				} else {
					$this->getBannerNoFrame($section,$site,$random,$float,$limit);
				}
				unset ($lastOption);
			}
			
		} else {
			if ($section == 204) {
				$this->getBannerNoFrame(204,1,true,"no",1);
			} elseif ($section == 202) {
				$this->getBannerNoFrame(202,1,true,"no",1);
			} elseif ($section == 202 || $section == 203) {
				$this->getBannerNoFrame(201,1,true,"no",1);
			} else {
				$this->getBannerNoFrame($section,$site,$random,$float,$limit);
			}
		}

	}	
	
	function getBannerNoFrame($section,$site,$random,$float,$limit) {
		global $frameless;
		
        $select = "";
        $count_banner = 1;
        
		if ($random) {
			$select  = "SELECT * FROM ( ";
		}
		$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontSite, frontSiteNew ";
		$select .= "FROM cyberphoto.frontAdmin ";
		$select .= "WHERE  frontDateFrom < now() AND frontDateTo > now() AND NOT (frontPicture is Null) ";
		$select .= "AND frontSection = '" . $section . "' AND frontSiteNew = '" . $site . "' ";
		if ($random) {
			$select .= " ) AS tmp ORDER BY RAND() ";
		} else {
			$select .= "ORDER BY frontSort DESC ";
		}
		/*
		if ($limit > 0) {
			$select .= "LIMIT $limit ";
		}
		*/
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
			
				$analyticsSite = $this->getSiteForAnalytics($frontSiteNew);

				if ($frontAllowNull == 0 && $frontArtNr != "") {
			
					if ($this->check_store_status($frontArtNr)) {
					
						if ($float == "left") {
							echo "<div class=\"banner_nostyle floatleft\">\n";
						} elseif ($float == "right") {
							echo "<div class=\"banner_nostyle floatright\">\n";
						} else {
							echo "<div class=\"banner_nostyle\">\n";
						}
			
						if (preg_match("/\.php$/", $frontPicture)) {
					
							if ($frontID == 7689 && $frameless) {
								ob_start();
								include("samsung_section12.php");
								$cont = ob_get_contents();
								ob_end_clean();
								
								$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
								
							} else {
								
								if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
									echo "jepp";
								}
								
								ob_start();
								include("/web/www/$frontPicture");
								$cont = ob_get_contents();
								ob_end_clean();
							
							}
					
							echo $cont;
					
						} else {


							if ($frontLinc != "") {
							
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
						
							} else {
							
								if ($frontArtNr != "") {
								
									$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
								} else {
								
									// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
									echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

								}
								
							}
						
							if ($frontArtNr != "") {
							
								$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
							
								if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/banners/$frontPicture\"></a>";
								} else {
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
								}
						
							} else {
							
								if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
									echo "<img border=\"0\" src=\"/banners/$frontPicture\"></a>";
								} else {
									echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
								}
						
							}
						
						}
						
						echo "</div>\n";
						
						if ($count_banner == $limit) {
							break;
						} else {
							$count_banner++;
						}
					
					}

				} else {
				
					if ($float == "left") {
						echo "<div class=\"banner_nostyle floatleft\">\n";
					} elseif ($float == "right") {
						echo "<div class=\"banner_nostyle floatright\">\n";
					} else {
						echo "<div class=\"banner_nostyle\">\n";
					}
       				
       				if (preg_match("/\.php$/", $frontPicture)) {
       					
						if ($frontID == 7689 && $frameless) {
							ob_start();
							include("samsung_section12.php");
							$cont = ob_get_contents();
							ob_end_clean();

							$cont = preg_replace("/mobil/", "mobiltelefoni", $cont);
							
						} else {
						
							if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
								echo $frontPicture;
							}
							
							ob_start();
							if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
								include("/web/www/$frontPicture");
							} else {
								include("/web/www/$frontPicture");
							}
							$cont = ob_get_contents();
							ob_end_clean();
						
						}
					
						echo $cont;
					
					} else {

						if ($frontLinc != "") {
							
							// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/$frontLinc\">";
							echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/$frontLinc\">";
						
						} else {
						
							if ($frontArtNr != "") {
								
								$linctitle = $this->getbeskrivningArtNew($frontArtNr,$site);
									
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" title=\"$linctitle\" href=\"/info.php?article=$frontArtNr\">";
								
							} else {
								
								// echo "<a onClick=\"_gaq.push(['_trackEvent', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture']);\" href=\"/info.php?article=$frontArtNr\">";
								echo "<a onClick=\"ga('send', 'event', '$analyticsSite', 'Sektion_$frontSection', '$frontPicture');\" href=\"/info.php?article=$frontArtNr\">";

							}
								
						}
						
						if ($frontArtNr != "") {
							
							$beskrivning = $this->getbeskrivningArtNew($frontArtNr,$site);
							
								if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"/banners/$frontPicture\"></a>";
								} else {
									echo "<img alt=\"$beskrivning\" border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
								}
						
						} else {
							
							if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
								echo "<img border=\"0\" src=\"/banners/$frontPicture\"></a>";
							} else {
								echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
							}

						}
					
					}
			
					echo "</div>\n";
					
					if ($count_banner == $limit) {
						break;
					} else {
						$count_banner++;
					}
						
				}

			}
			
		} elseif ($section == 202) {
			$this->getBannerNoFrame(201,1,true,"no",1);
		} elseif ($section == 203) {
			$this->getBannerNoFrame(201,1,true,"no",1);
		} elseif ($section == 204) {
			$this->getBannerNoFrame(201,1,true,"no",1);
		}

	}	
	
	
	function check_store_status($frontArtNr) {
		
		$select = "SELECT lagersaldo FROM Artiklar WHERE artnr = '" . $frontArtNr . "' ";

		$res = mysqli_query(Db::getConnection(), $select);

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
		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {

			extract(mysqli_fetch_array($res));

			return $tillverkare . " ". $beskrivning;
			
			}

	}	

	function getbeskrivningArtNew($artnr,$site = null) {
		global $fi, $sv, $no;
			
		$select  = "SELECT beskrivning, beskrivning_fi, Tillverkare.tillverkare ";
		$select .= "FROM cyberphoto.Artiklar ";
		$select .= "LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
		$select .= "LEFT JOIN Tillverkare on Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
		$select .= "WHERE artnr = '" . $artnr . "' ";
		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {

			extract(mysqli_fetch_array($res));

				if ($fi && !$sv) {
				
					return $tillverkare . " ". $beskrivning_fi;

				} else {
				
					return $tillverkare . " ". $beskrivning;

				}
			
			}

	}	

	function check_artikel_status($addartnr) {
		
	$select  = "SELECT artnr FROM cyberphoto.Artiklar WHERE artnr = '" . $addartnr . "' ";
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			extract(mysqli_fetch_array($res));
			
			return $artnr;
			// return true;
		
		} else {
		
			return false;
		
		}

	}	

	function getSiteForAnalytics($frontSite) {

		if ($frontSite == 305) { // Cybairgun FI SE
			$category = "FrontClickCybairgunFISE";
		} elseif ($frontSite == 304) { // Outdoor FI SE
			$category = "FrontClickOutdoorFISE";
		} elseif ($frontSite == 303) { // Batterier FI SE
			$category = "FrontClickBatterierFISE";
		} elseif ($frontSite == 302) { // Mobiltelefoni FI SE
			$category = "FrontClickMobilFISE";
		} elseif ($frontSite == 301) { // Foto FI SE
			$category = "FrontClickFotoFISE";
		} elseif ($frontSite == 205) { // Cybairgun FI
			$category = "FrontClickCybairgunFI";
		} elseif ($frontSite == 204) { // Outdoor FI
			$category = "FrontClickOutdoorFI";
		} elseif ($frontSite == 203) { // Batterier FI
			$category = "FrontClickBatterierFI";
		} elseif ($frontSite == 202) { // Mobiltelefoni FI
			$category = "FrontClickMobilFI";
		} elseif ($frontSite == 201) { // Foto FI
			$category = "FrontClickFotoFI";
		} elseif ($frontSite == 105) { // Cybairgun NO
			$category = "FrontClickCybairgunNO";
		} elseif ($frontSite == 104) { // Outdoor NO
			$category = "FrontClickOutdoorNO";
		} elseif ($frontSite == 103) { // Batterier NO
			$category = "FrontClickBatterierNO";
		} elseif ($frontSite == 102) { // Mobiltelefoni NO
			$category = "FrontClickMobilNO";
		} elseif ($frontSite == 101) { // Foto NO
			$category = "FrontClickFotoNO";
		} elseif ($frontSite == 5) { // Cybairgun
			$category = "FrontClickCybairgun";
		} elseif ($frontSite == 4) { // Outdoor
			$category = "FrontClickOutdoor";
		} elseif ($frontSite == 3) { // Batterier
			$category = "FrontClickBatterier";
		} elseif ($frontSite == 2) { // Mobiltelefoni
			$category = "FrontClickMobil";
		} elseif ($frontSite == 1) { // Foto
			$category = "FrontClickFoto";
		} else { // Foto
			$category = "FrontClickFoto";
		}
		
		return $category;

	}

	// *************************** NEDAN BÖRJAR ALL ADMINISTRATIV KOD ************************************

	function getBannerAdminNow($site,$section,$upcomming=false) {
		global $choose_site, $choose_department, $choose_section;

		$selfUrl = htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8');
		$heading = $upcomming ? 'Planerat' : 'Just nu';
		$dateCol = $upcomming ? 'Visas från' : 'Visas till';
		$showSort = ($section == 23 || $section == 24);

		echo "<div style=\"margin-top:20px;\">\n";
		echo "<h2 style=\"font-size:16px;margin-bottom:8px;\">$heading</h2>\n";
		echo "<table class=\"table-list\">\n";
		echo "<thead><tr>\n";
		echo "  <th>$dateCol</th>\n";
		echo "  <th>Återstår</th>\n";
		if ($showSort) echo "  <th>Sortering</th>\n";
		echo "  <th>Bild</th>\n";
		echo "  <th>Status</th>\n";
		echo "  <th>Av</th>\n";
		echo "  <th colspan=\"4\"></th>\n";
		echo "</tr></thead>\n";
		echo "<tbody>\n";

		$select  = "SELECT frontID, frontSection, frontDateFrom, frontDateTo, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontLeverantor, frontComment, frontCreatedBy, frontPrio, frontSort ";
		$select .= "FROM cyberphoto.frontAdmin ";
		if ($upcomming) {
			$select .= "WHERE frontDateFrom > now() ";
		} else {
			$select .= "WHERE frontDateFrom < now() AND frontDateTo > now() ";
		}
		$select .= "AND frontSection = '" . $section . "' AND frontSiteNew = '" . $site . "' ";
		$select .= "ORDER BY frontSort DESC, frontDateTo ASC, frontArtNr ASC ";

		$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)) {
				extract($row);

				$aterstar  = $this->getDaysLeft($frontDateTo);
				$isActive  = ($frontAllowNull == -1 || ($frontAllowNull == 0 && $this->check_store_status($frontArtNr)));
				$imgSrc    = $this->buildImgSrc($frontPicture);
				$imgLabel  = htmlspecialchars(basename($frontPicture), ENT_QUOTES, 'UTF-8');
				$createdBy = htmlspecialchars(strstr($frontCreatedBy, '@', true) ?: $frontCreatedBy, ENT_QUOTES, 'UTF-8');
				$dateVal   = $upcomming ? date("j M Y", strtotime($frontDateFrom)) : date("j M Y", strtotime($frontDateTo));
				$rowStyle  = '';

				echo "<tr$rowStyle>\n";

				// Datum
				echo "  <td style=\"white-space:nowrap;\">$dateVal</td>\n";

				// Återstår
				if ($aterstar < 4) {
					echo "  <td style=\"color:#dc2626;font-weight:700;text-align:right;white-space:nowrap;\">$aterstar d</td>\n";
				} else {
					echo "  <td style=\"text-align:right;white-space:nowrap;\">$aterstar d</td>\n";
				}

				// Sortering (valfri kolumn)
				if ($showSort) {
					echo "  <td style=\"text-align:center;\">$frontSort</td>\n";
				}

				// Bild — thumbnail med tooltip vid hover
				echo "  <td>\n";
				echo "    <div style=\"display:flex;align-items:center;gap:8px;\">\n";
				echo "      <img src=\"$imgSrc\" alt=\"\" style=\"height:40px;width:auto;border-radius:3px;border:1px solid #e5e7eb;object-fit:cover;cursor:pointer;\"\n";
				echo "           onclick=\"this.style.height=this.style.height==='40px'?'120px':'40px'\">\n";
				echo "      <span style=\"font-size:11px;color:#6b7280;word-break:break-all;\">$imgLabel</span>\n";
				echo "    </div>\n";
				echo "  </td>\n";

				// Status-indikator
				if ($isActive) {
					$statusDot = '<span title="Visas just nu" style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#10b981;"></span>';
				} else {
					$statusDot = '<span title="Visas EJ" style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#ef4444;"></span>';
				}
				echo "  <td style=\"text-align:center;\">$statusDot</td>\n";

				// Skapad av (bara förnamn/användarnamn)
				echo "  <td style=\"font-size:12px;color:#6b7280;\">$createdBy</td>\n";

				// Action-knappar
				$btnStyle = 'style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:600;text-decoration:none;border:1px solid #d1d5db;background:#f9fafb;color:#374151;"';
				echo "  <td><a href=\"{$selfUrl}?copypost={$frontID}\" $btnStyle>Kopiera</a></td>\n";
				echo "  <td><a href=\"{$selfUrl}?change={$frontID}\" $btnStyle>Ändra</a></td>\n";
				if ($upcomming) {
					echo "  <td></td><td></td>\n";
				} else {
					$extUrl = "{$selfUrl}?extend={$frontID}&choose_site={$choose_site}&choose_department={$choose_department}&choose_section={$choose_section}";
					$endUrl = "{$selfUrl}?endnow={$frontID}&choose_site={$choose_site}&choose_department={$choose_department}&choose_section={$choose_section}";
					echo "  <td><a href=\"$extUrl\" $btnStyle>Förläng</a></td>\n";
					echo "  <td><a href=\"$endUrl\" style=\"display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:600;text-decoration:none;border:1px solid #fca5a5;background:#fef2f2;color:#dc2626;\">Avsluta</a></td>\n";
				}

				echo "</tr>\n";
			}

		} else {
			$cols = $showSort ? 10 : 9;
			echo "<tr><td colspan=\"$cols\"><span class=\"noresult\">Inga poster finns upplagda</span></td></tr>\n";
		}

		echo "</tbody></table>\n";
		echo "</div>\n";
	}

	function artikel_status_store($frontArtNr) {
		
	$select  = "SELECT lagersaldo FROM Artiklar WHERE artnr = '" . $frontArtNr . "' ";
	$res = mysqli_query(Db::getConnection(), $select);

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
	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {

		return true;
			
		} else {
		
		return false;
		
		}

	}	

	function displayWarning($site) {

		$rowcolor = true;

		$select  = "SELECT frontSection, count(frontSection) AS Antal ";
		$select  .= "FROM cyberphoto.frontAdmin ";
		$select  .= "WHERE DateDiff(frontDateTo,now()) < 4 AND frontDateFrom < now() AND frontDateTo > now() AND frontSiteNew = '" . $site . "' ";
		$select  .= "GROUP BY frontSection ";
		$select  .= "ORDER BY frontSection ASC ";
		
		// echo $select;
		echo "<h3>Aktuella varningar för denna avdelning</h3>\n";

		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {

				echo "<div class=\"container_white\">\n";
				echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\">\n";
				echo "<tr>\n";
				echo "\t<td class=\"$backcolor bold align_center\" width=\"75\">Sektion</td>\n";
				echo "\t<td class=\"$backcolor bold align_center\" width=\"75\">Antal</td>\n";
				echo "\t<td class=\"$backcolor bold\" width=\"130\"></td>\n";
				echo "</tr>\n";
			
				while ($row = mysqli_fetch_array($res)) {
			
					extract($row);
					
					if ($rowcolor == true) {
						$backcolor = "firstrow";
					} else {
						$backcolor = "secondrow";
					}
					
					echo "<tr>\n";
					echo "\t<td class=\"$backcolor align_center\">" . $frontSection . "</td>\n";
					echo "\t<td class=\"$backcolor align_center\">" . $Antal . " st</td>\n";
					echo "<td class=\"$backcolor align_center\"><a href=\"" . $_SERVER['PHP_SELF'] ."?choose_site=" . $_SESSION['bannersite'] . "&choose_department=" . $_SESSION['bannerdepartment'] . "&choose_section=" . $frontSection . "\">Visa denna sektion</a></td>\n";
					echo "</tr>\n";
					
					if ($rowcolor == true) {
						$rowcolor = false;
					} else {
						$rowcolor = true;
					}
			
				}

				echo "</table>\n";
				echo "</div>\n";
				
			} else {
				echo "<p class=\"italic\">Inga varning finns just nu</p>\n";
			}

	}

	function getLeverantor() {

	global $addleverantor;

	$select  = "SELECT grossist_id, grossist FROM Leverantor WHERE NOT (grossist is null) ORDER BY grossist ASC ";

	$res = mysqli_query(Db::getConnection(), $select);

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

	$res = mysqli_query(Db::getConnection(), $select);

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

	$res = mysqli_query(Db::getConnection(), $select);

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

	$res = mysqli_query(Db::getConnection(), $select);

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

		// mssql_query ("DELETE FROM cyberphoto.frontAdmin WHERE frontID = '" . $delete . "'");

		$updt = "DELETE FROM cyberphoto.frontAdmin WHERE frontID = '" . $delete . "'";

		$res = mysqli_query($conn_my, $updt);

		header("Location: update_banner.php?section=$section");

	}

	function FrontAdminDelete($delete,$section) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("DELETE FROM cyberphoto.frontAdmin WHERE frontID = '" . $delete . "'");

		$updt = "DELETE FROM cyberphoto.frontAdmin WHERE frontID = '" . $delete . "'";

		$res = mysqli_query($conn_my, $updt);

		// header("Location: update_front.php?section=$section");
		header("Location: banners.php");

	}

	function FrontAdminExtend($extend) {
		global $choose_site, $choose_department, $choose_section;

		$conn_my = Db::getConnection(true);
		
		$addto = date("Y-m-d 23:59:59",mktime(0,0,0,date("n")+2,1-1,date("Y")));

		// mssql_query ("DELETE FROM cyberphoto.frontAdmin WHERE frontID = '" . $delete . "'");

		$updt = "UPDATE cyberphoto.frontAdmin SET frontDateTo = '$addto' WHERE frontID = '" . $extend . "'";

		$res = mysqli_query($conn_my, $updt);
		
		// echo $updt;
		// exit;

		// header("Location: update_front.php?section=$section");
		header("Location: banners.php?choose_site=$choose_site&choose_department=$choose_department&choose_section=$choose_section");

	}

	function FrontAdminEnd($extend) {
		global $choose_site, $choose_department, $choose_section;
	
		$conn_my = Db::getConnection(true);
	
		$updt = "UPDATE cyberphoto.frontAdmin SET frontDateTo = NOW() WHERE frontID = '" . $extend . "'";
	
		$res = mysqli_query($conn_my, $updt);
	
		// echo $updt;
		// exit;
	
		// header("Location: update_front.php?section=$section");
		header("Location: banners.php?choose_site=$choose_site&choose_department=$choose_department&choose_section=$choose_section");
	
	}
	
	function BannerAdminAdd($addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addsite,$addprio) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontLeverantor,frontCreatedBy,frontSite,frontPrio) VALUES ('$addsection','$addfrom','$addto','$addpicture','$addartnr','$addlinc','$addstore','$addcomment','$addleverantor','$addcreatedby','$addsite','$addprio') ");
	    $addprio = intval($addprio);
		$addsite = intval($addsite);
		$addleverantor = intval($addleverantor);
		$addstore = intval($addstore);
		$addsection = intval($addsection);
		$updt = "INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontLeverantor,frontCreatedBy,frontSite,frontPrio) VALUES ($addsection,'$addfrom','$addto','$addpicture','$addartnr','$addlinc',$addstore,'$addcomment',$addleverantor,'$addcreatedby',$addsite,$addprio)";
		
		error_log("1." . $updt);
		
		$res = mysqli_query($conn_my, $updt);

		$this->sendFrontMess($addcreatedby,$addsection,$addsite,$addpicture);

		header("Location: update_banner.php?section=$addsection");

	}

	function FrontAdminAdd($addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addsite,$addprio,$addsort = null,$addcategory = null) {

		/* För att fixa bakåtkompatibiliteten */
		if ($addsite == 1) {
			$addsiteOLD = 1;
		} elseif ($addsite == 2) {
			$addsiteOLD = 6;
		} elseif ($addsite == 4) {
			$addsiteOLD = 11;
		} elseif ($addsite == 5) {
			$addsiteOLD = 10;
		} elseif ($addsite == 101) {
			$addsiteOLD = 30;
		} elseif ($addsite == 102) {
			$addsiteOLD = 31;
		} elseif ($addsite == 104) {
			$addsiteOLD = 33;
		} elseif ($addsite == 201) {
			$addsiteOLD = 2;
		} elseif ($addsite == 202) {
			$addsiteOLD = 7;
		} elseif ($addsite == 205) {
			$addsiteOLD = 14;
		} elseif ($addsite == 301) {
			$addsiteOLD = 3;
		}

		$conn_my = Db::getConnection(true);

		// mssql_query ("INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontLeverantor,frontCreatedBy,frontSite,frontPrio) VALUES ('$addsection','$addfrom','$addto','$addpicture','$addartnr','$addlinc','$addstore','$addcomment','$addleverantor','$addcreatedby','$addsite','$addprio') ");
	    $addprio = intval($addprio);
		$addsite = intval($addsite);
		$addleverantor = intval($addleverantor);
		$addstore = intval($addstore);
		$addsection = intval($addsection);
		$addcategory = intval($addcategory);
		$addsort = intval($addsort);
		$addsiteOLD = intval($addsiteOLD);
		
		$updt = "INSERT INTO frontAdmin (frontSection,frontDateFrom,frontDateTo,frontPicture,frontArtNr,frontLinc,frontAllowNull,frontComment,frontLeverantor,frontCreatedBy,frontSite,frontPrio,frontSort,frontCategory,frontSiteNew) 
				VALUES ($addsection,'$addfrom','$addto','$addpicture','$addartnr','$addlinc',$addstore,'$addcomment',$addleverantor,'$addcreatedby',$addsiteOLD,$addprio,$addsort,$addcategory,$addsite)";
		// echo $updt;
		// exit;
		error_log("2. " .$updt);
		$res = mysqli_query($conn_my, $updt);

		if ($addsite < 100 || $addsite == 1440) { // om sverige lägger upp en banner skickas ett mail till ansvariga i Norge och Finland
			$this->sendFrontMess($addcreatedby,$addsection,$addsite,$addpicture);
		}

		// header("Location: update_front.php?section=$addsection");
		header("Location: banners.php?choose_site=" . $_SESSION['bannersite'] . "");

	}

	function BannerAdminChange($addid,$addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addprio) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("UPDATE frontAdmin  SET frontSection = '$addsection', frontDateFrom = '$addfrom',frontDateTo = '$addto',frontPicture = '$addpicture',frontArtNr = '$addartnr',frontLinc = '$addlinc',frontAllowNull = '$addstore',frontComment = '$addcomment',frontLeverantor = '$addleverantor',frontCreatedBy = '$addcreatedby',frontPrio = '$addprio' WHERE frontID = '$addid' ");

		$updt = "UPDATE frontAdmin  SET frontSection = '$addsection', frontDateFrom = '$addfrom',frontDateTo = '$addto',frontPicture = '$addpicture',frontArtNr = '$addartnr',frontLinc = '$addlinc',frontAllowNull = '$addstore',frontComment = '$addcomment',frontLeverantor = '$addleverantor',frontCreatedBy = '$addcreatedby',frontPrio = '$addprio' WHERE frontID = '$addid'";

		$res = mysqli_query($conn_my, $updt);

		header("Location: update_banner.php?section=$addsection");

	}

	function FrontAdminChange($addid,$addsection,$addfrom,$addto,$addpicture,$addartnr,$addlinc,$addstore,$addcomment,$addleverantor,$addcreatedby,$addprio,$addsort = null,$addcategory = null) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("UPDATE frontAdmin  SET frontSection = '$addsection', frontDateFrom = '$addfrom',frontDateTo = '$addto',frontPicture = '$addpicture',frontArtNr = '$addartnr',frontLinc = '$addlinc',frontAllowNull = '$addstore',frontComment = '$addcomment',frontLeverantor = '$addleverantor',frontCreatedBy = '$addcreatedby',frontPrio = '$addprio' WHERE frontID = '$addid' ");

		// $updt = "UPDATE frontAdmin SET frontSection = '$addsection', frontDateFrom = '$addfrom',frontDateTo = '$addto',frontPicture = '$addpicture',frontArtNr = '$addartnr',frontLinc = '$addlinc',frontAllowNull = '$addstore',frontComment = '$addcomment',frontLeverantor = '$addleverantor',frontCreatedBy = '$addcreatedby',frontPrio = '$addprio',frontSort = '$addsort', frontCategory = '$addcategory' WHERE frontID = '$addid'";
		$updt = "UPDATE frontAdmin SET frontSection = '$addsection', frontDateFrom = '$addfrom',frontDateTo = '$addto',frontPicture = '$addpicture',frontArtNr = '$addartnr',frontLinc = '$addlinc',frontAllowNull = '$addstore',frontComment = '$addcomment',frontLeverantor = '$addleverantor',frontCreatedBy = '$addcreatedby',frontSort = '$addsort', frontCategory = '$addcategory' WHERE frontID = '$addid'";

		// echo $updt;
		// exit;
		$res = mysqli_query($conn_my, $updt);

		// header("Location: update_front.php?section=$addsection");
		header("Location: banners.php?choose_site=" . $_SESSION['bannersite'] . "");

	}

	function sendFrontMess($addcreatedby,$addsection,$addsite,$addpicture) {

		if ($addsite == 1440) {
			$department = "development_environment_1440";
		} elseif ($addsite == 4) {
			$department = "outdoor";
		} elseif ($addsite == 3) {
			$department = "batterier";
		} elseif ($addsite == 2) {
			$department = "mobilsidan";
		} else {
			$department = "foto-video";
		}
		/*
		if ($addsite > 300) {
			$country = "den finska/svenska";
		} elseif ($addsite > 200) {
			$country = "den finska";
		} elseif ($addsite > 100) {
			$country = "den norska";
		} else {
			$country = "den svenska";
		}
		*/
		
		$recipient .= "sjabo@cyberphoto.nu ";
		if ($addsite != 1440) {
			$recipient .= "borje@cyberphoto.nu ";
		}
		if ($addsite == 2) {
			// $recipient .= "emilie@cyberphoto.nu ";
		}
		
		$uppdatum = date("Y-m-d H:i:s", time());

		$subj = $uppdatum . " Ny fronbanner upplagd!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "Banner på " . $department . " sajten, sektion " . $addsection . " är upplagd. Vänligen kontrollera hur den ser ut.\n\n";
		if ($addsite > 300) {
			$text1 .= "http://www.cyberphoto.fi/start3/" . $addpicture . " ";
		} elseif ($addsite > 200) {
			$text1 .= "http://www.cyberphoto.no/start3/" . $addpicture . " ";
		} else {
			$text1 .= "http://www.cyberphoto.se/start3/" . $addpicture . " ";
		}
	
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}

	function getSpecFrontBanner($frontID) {

	$select  = "SELECT * FROM cyberphoto.frontAdmin WHERE frontID = '" . $frontID . "' ";

	$res = mysqli_query(Db::getConnection(), $select);

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
	$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM cyberphoto.frontAdmin ";
	$select .= "WHERE  frontDateFrom < '$datum 12:00:00' AND frontDateTo > '$datum 12:00:00' AND NOT (frontPicture is Null) ";
	$select .= "AND frontSection = '" . $section . "' AND frontSite = '" . $site . "' AND frontPrio = -1 ";
	$select .= "ORDER BY frontID DESC ";
	// $select .= " ) AS tmp ORDER BY RAND() ";

	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
		
			if ($frontAllowNull == 0 && $frontArtNr != "") {
		
				if ($this->getKommaRunt()) {
		
					if (preg_match("/\.php$/", $frontPicture)) {
				
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
					
						echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
					
					}
				
				unset ($lastOption);
				
				break;
				
				} else {
				
				$lastOption = 1;
				
				}

			} else {
		
				if (preg_match("/\.php$/", $frontPicture)) {
				
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
					
					echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
				
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
	$select .= "SELECT frontID, frontSection, frontPicture, frontArtNr, frontLinc, frontAllowNull FROM cyberphoto.frontAdmin ";
	$select .= "WHERE  frontDateFrom < '$datum 12:00:00' AND frontDateTo > '$datum 12:00:00' AND NOT (frontPicture is Null) ";
	$select .= "AND frontSection = '" . $section . "' AND frontSite = '" . $site . "' ";
	$select .= " ) AS tmp ORDER BY RAND() ";

	$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
		
			if ($frontAllowNull == 0 && $frontArtNr != "") {
		
				if ($this->getKommaRunt()) {
		
					if (preg_match("/\.php$/", $frontPicture)) {
				
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
					
						echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
					
					}
				
				unset ($lastOption);
				
				break;
				
				} else {
				
				$lastOption = 1;
				
				}

			} else {
		
				if (preg_match("/\.php$/", $frontPicture)) {
				
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
					
					echo "<img border=\"0\" src=\"{$this->buildImgSrc($frontPicture)}\"></a>";
				
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

	$select  = "SELECT frontID, frontSection, frontDateFrom, frontDateTo, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontCreatedBy, frontLeverantor FROM cyberphoto.frontAdmin ";
	$select .= "WHERE NOT (frontLeverantor = 0) AND NOT (frontPicture is Null) ";
	$select .= "AND (((frontDateFrom > '$datumfrom 12:00:00' AND frontDateFrom < '$datumto 12:00:00') OR (frontDateTo > '$datumfrom 12:00:00' AND frontDateTo < '$datumto 12:00:00')) OR (frontDateFrom < '$datumfrom 12:00:00' AND frontDateTo > '$datumto 12:00:00')) ";
	$select .= "AND frontSite = '" . $site . "' ";
	if ($leverantor != "") {
	$select .= "AND frontLeverantor = '" . $leverantor . "' ";
	}

	// echo $select;

	$res = mysqli_query(Db::getConnection(), $select);

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
			echo "<td><font face=\"Verdana\" size=\"1\"><img src=\"{$this->buildImgSrc($frontPicture)}\"></font></td>";
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

	function getActiveSections($frontSiteNew) {
		global $choose_department;
		
		// $select  = "SELECT DISTINCT fa.frontSection, fs.sectionName ";
		$select  = "SELECT fs.sectionID, fs.sectionName ";
		// $select .= "FROM cyberphoto.frontAdmin fa ";
		$select .= "FROM frontSection fs ";
		// $select .= "LEFT JOIN frontSection fs ON fs.sectionID = fa.frontSection ";
		if ($_SESSION['bannerdepartment'] == 1440) {
			$select .= "WHERE fs.sectionDE1440 = -1 ";
		} elseif ($_SESSION['bannerdepartment'] == 5 || $_SESSION['bannerdepartment'] == 105 || $_SESSION['bannerdepartment'] == 205 || $_SESSION['bannerdepartment'] == 305) {
			$select .= "WHERE fs.sectionCybairgun = -1 ";
		} elseif ($_SESSION['bannerdepartment'] == 4 || $_SESSION['bannerdepartment'] == 104 || $_SESSION['bannerdepartment'] == 204 || $_SESSION['bannerdepartment'] == 304) {
			$select .= "WHERE fs.sectionHobby = -1 ";
		} elseif ($_SESSION['bannerdepartment'] == 3 || $_SESSION['bannerdepartment'] == 103 || $_SESSION['bannerdepartment'] == 203 || $_SESSION['bannerdepartment'] == 303) {
			$select .= "WHERE fs.sectionBatterier = -1 ";
		} elseif ($_SESSION['bannerdepartment'] == 2 || $_SESSION['bannerdepartment'] == 102 || $_SESSION['bannerdepartment'] == 202 || $_SESSION['bannerdepartment'] == 302) {
			$select .= "WHERE fs.sectionMobil = -1 ";
		} else {
			$select .= "WHERE fs.sectionFoto = -1 ";
		}
		$select .= "ORDER BY fs.sectionID ";
		
		// echo $select;

		$res = mysqli_query(Db::getConnection(), $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)) {
		
			extract($row);

			echo "<option value=\"$sectionID\"";
			
			if ($_SESSION['bannersection'] == $sectionID) {
				echo " selected";
			}
			
			if ($sectionName != "") {
				echo ">" . $sectionName . "</option>";
			} else {
				echo ">Sektion " . $sectionID . "</option>\n";
			}
			
		
			}
			
		}

	}

	function getBannerButiken() {

		$select  = "SELECT frontID, frontSection, frontDateFrom, frontDateTo, frontPicture, frontArtNr, frontLinc, frontAllowNull, frontLeverantor, frontComment, frontCreatedBy, frontPrio, frontSort ";
		$select .= "FROM cyberphoto.frontAdmin ";
		$select .= "WHERE frontDateFrom < now() AND frontDateTo > now() ";
		$select .= "AND frontSection = '201' ";
		$select .= "ORDER BY frontID DESC ";
		
		// echo $select;

		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)) {
			
					extract($row);
					
					if ($frontLinc != "") {
						echo "<a target=\"_blank\" href=\"/$frontLinc\">";
					} else {
						echo "<a target=\"_blank\" href=\"/info.php?article=$frontArtNr\">";
					}
					echo "<img border=\"1\" width=\"800\" src=\"/banners/$frontPicture\"><br><br>\n";
					// echo "<img border=\"1\" width=\"800\" src=\"http://www.cyberphoto.se/start3/$frontPicture\"><br><br>\n";
					echo "</a>";
					
				}
				
			}

	}
	
}

?>

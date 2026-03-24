<?php

Class CNews {

	function __construct() {
		
	}

	function getBloggRightFrame() {
		global $sv,$fi,$no;
	
		if ($fi && !$sv) {
			$bpage_linc = "/blogi";
		} else {
			$bpage_linc = "/bloggen";
		}

		echo "<a href=\"" . $bpage_linc . "\">";
		echo "<div class=\"menu_expand_foto\">" . l('Read the blog') . "</div>\n";
		echo "</a>\n";
		echo "<div class=\"menu_container_foto \">\n";

		$firstrow = true;
		$countrow = 1;
		
		$select  = "SELECT cnt, titel, link, skapad, blogType ";	
		$select .= "FROM cyberphoto.blog ";	
		$select .= "WHERE offentlig = -1 AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() ";	
		// $select .= "AND blogType IN(19) ";	
		// $select .= "AND (blogType IN(19,30) OR (blogType=1 AND cnt > 8674) OR (blogType=2 AND cnt > 8674) OR (cnt IN (8543,8659))) ";	
		if ($fi && !$sv) {
			$select .= "AND blogType IN(28,29,30) ";
		} else {
			$select .= "AND blogType IN(1,2,19) ";
		}
		if ($fi) {
			$select .= "AND not_fi = 0 ";
		}
		if ($no) {
			$select .= "AND not_no = 0 ";
		}
		$select .= "ORDER BY skapad DESC ";	
		$select .= "LIMIT 10";	
		
		// echo $select;

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_object($res)) {

			if ($firstrow) {
				echo "<a href=\"$bpage_linc\">";
			} else {
				echo "<a href=\"$bpage_linc#".$row->cnt."\">";
			}
			$titel = $row->titel;
			if (strlen($titel) > 27)
				$titel = substr ($titel, 0, 27) . "..";

			echo "<div class=\"bottom5 frontbloggheadline\">" . $titel . "</div></a>";
			
			$firstrow = false;
			$countrow++;
			
		}

		if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			echo "<div class=\"mpadding\"><img border=\"0\" src=\"/blogg/help.gif\"> <a href=\"javascript:winPopupCenter(520, 900, 'http://www.cyberphoto.se/order/admin/newblogg.php?fi=$fi');\">Lägg in ny blogg</a></div>";
		}
		echo "</div>\n";

	}
	
	function getNewsRightFrameMobile() {
		global $sv,$fi,$no;
	
		if ($fi && !$sv) {
			$bpage_linc = "/mobiili/news";
		} else {
			$bpage_linc = "/mobiltelefoni/news";
		}

		echo "<a href=\"" . $bpage_linc . "\">";
		echo "<div class=\"menu_expand_mobil\">" . l('Latest news') . "</div>\n";
		echo "</a>\n";
		echo "<div class=\"menu_container_mobil \">\n";

		$firstrow = true;
		$countrow = 1;
		
		$select  = "SELECT cnt, titel, link, skapad, blogType ";	
		$select .= "FROM cyberphoto.blog ";	
		$select .= "WHERE offentlig = -1 AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() ";	
		if ($fi && !$sv) {
			$select .= "AND blogType IN(28,29,30) ";
		} else {
			$select .= "AND blogType IN(23) AND skapad > DATE_SUB(NOW(), INTERVAL 1 MONTH) ";
		}
		if ($fi) {
			$select .= "AND not_fi = 0 ";
		}
		if ($no) {
			$select .= "AND not_no = 0 ";
		}
		$select .= "ORDER BY skapad DESC ";	
		$select .= "LIMIT 10";	
		
		// echo $select;

		$res = mysqli_query(Db::getConnection(), $select);
		
		echo "<form id=\"mobile_news\">\n";

		while ($row = mysqli_fetch_object($res)) {

			if (preg_match("/getmobilenews\.php/i", $_SERVER['PHP_SELF'])) {
				echo "<a href=\"/mobiltelefoni/#\" onclick=\"updateMobileNews(" . $row->cnt . ")\">";
			} else {
				echo "<a href=\"#\" onclick=\"updateMobileNews(" . $row->cnt . ")\">";
			}
			$titel = $row->titel;
			if (strlen($titel) > 27)
				$titel = substr ($titel, 0, 27) . "..";

			echo "<div class=\"bottom5 frontbloggheadline\">" . $titel . "</div></a>";
			
			$firstrow = false;
			$countrow++;
			
		}

		echo "</form>\n";

		if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			echo "<div class=\"mpadding\"><img border=\"0\" src=\"/blogg/help.gif\"> <a href=\"javascript:winPopupCenter(520, 900, 'http://www.cyberphoto.se/order/admin/newblogg.php?fi=$fi&mobile_news=yes');\">Lägg in ny blogg</a></div>";
		}
		echo "</div>\n";

	}

	function getMobileNews($ID) {
		global $fi, $sv, $no;
		
		if ($fi && !$sv) {
			$select = "SELECT cnt, titel, titel_fi, beskrivning, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style, blogType ";
		} else {
			$select = "SELECT cnt, titel, titel_fi, beskrivning, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style, blogType ";
		}
		$select .= "FROM cyberphoto.blog ";
		if ($fi && !$sv) {
			$select .= "WHERE offentlig = -1 AND blogType IN (23) ";
		} else {
			$select .= "WHERE offentlig = -1 AND blogType IN (23) ";
		}
		
		$select .= "AND skapad > DATE_SUB(NOW(), INTERVAL 1 MONTH) AND skapad < now() ";

		if ($fi && !$sv) {
			$select .= "AND NOT (titel IS Null) AND NOT (beskrivning IS NUll) AND NOT (link_pic IS NULL) ";
		} else {
			$select .= "AND NOT (titel IS Null) AND NOT (beskrivning IS NUll) AND NOT (link_pic IS NULL) ";
		}
		if ($fi) {
			$select .= "AND not_fi = 0 ";
		}
		if ($no) {
			$select .= "AND not_no = 0 ";
		}
		
		if ($ID > 0) {
			$select .= "AND cnt = $ID ";
		} else {
			$select .= "ORDER BY skapad DESC ";	
			$select .= "LIMIT 25 ";	
		}
		
		// echo $select;
		
		
		$res = mysqli_query(Db::getConnection(), $select);
			
		if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_object($res)) {
			
					$cnt = $row->cnt;

					if ($row->blogType == 1) {
						$titel .= "Test: ";
					} elseif ($row->blogType == 2) {
						$titel .= "Nyhet: ";
					}
					
					if ($fi && !$sv) {

						$titel .= $row->titel;
						$beskrivning = $row->beskrivning;
						
					} else {
						
						$titel .= $row->titel;
						$beskrivning = $row->beskrivning;
						
					}
					
					$pubdate = date("Y-m-d H:i",strtotime($row->PubDate));
				
					$beskrivning = eregi_replace("\n", "<br>", $beskrivning);
					$beskrivning = str_replace("\\", "", $beskrivning);
					
					if ($fi && $sv && !$frameless) {
						$beskrivning = eregi_replace("info.php", "info_fi_se.php", $beskrivning);
					}
					if ($fi) {
						$beskrivning = preg_replace("/cyberphoto\.se/", "cyberphoto.fi", $beskrivning);
					}
					if ($no) {
						$beskrivning = preg_replace("/cyberphoto\.se/", "cyberphoto.no", $beskrivning);
					}
					
					echo "<div class=\"news_mobile_container\">";
					
					echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
					
					if ($row->blog_style == 1) {
						echo "<tr>";
						echo "<td align=\"left\" width=\"100%\">";
						if ($ID > 0) {
							echo ("<div class=\"floatright\" name=\"$cnt\"><a href=\"/mobiltelefoni/#\" onclick=\"updateMobileNews(45698789545)\">Stäng</a></div>");
							echo ("<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>");
						} else {
							echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
						}
						echo "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=\"left\" width=\"100%\">";
						if (eregi(".gif", $row->link_pic) || eregi(".png", $row->link_pic)) {
							echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
						} elseif (eregi("_small.jpg", $row->link_pic)) {
							$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
							echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://" . $_SERVER["HTTP_HOST"] . "/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\"></a>";
						} else {
							echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
						}
						echo "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=\"left\" width=\"100%\">";
						if ($ID > 0) {
							echo ("<p class=\"news_mobile_text\">$beskrivning</p>");
						} else {
							echo "<p class=\"news_mobile_text\">$beskrivning</p>";
						}
						echo "</td>";
						echo "</tr>";
					
					} else {
						echo "<tr>";
						echo "<td align=\"left\" width=\"100%\">";
						if ($ID > 0) {
							echo ("<div class=\"floatright\" name=\"$cnt\"><a href=\"/mobiltelefoni/#\" onclick=\"updateMobileNews(45698789545)\">Stäng</a></div>");
							echo "<div class=\"clear\"></div>";
						}
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
						if ($ID > 0) {
							echo ("<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>");
							echo ("<p class=\"news_mobile_text\">$beskrivning</p>");
						} else {
							echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
							echo "<p class=\"news_mobile_text\">$beskrivning</p>";
						}
						echo "</td>";
						echo "</tr>";
					}
					echo "<tr>";
					echo "<td class=\"align_right pub\" width=\"100%\">";
					echo "Publicerad: $pubdate";
					echo "</td>";
					echo "</tr>";
					if (CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
						echo "<tr>";
						echo "<td class=\"align_right pub\" width=\"100%\">";
						echo "<br><a class=\"pub\" href=\"javascript:winPopupCenter(520, 900, 'http://" . $_SERVER["HTTP_HOST"] . "/order/admin/newblogg.php?change=$cnt');\">Uppdatera blogg</a>";
						echo "</td>";
						echo "</tr>";
					}
					echo "</table>";
					
					echo "</div>";
					
					unset($titel);
			
				}

		} else {

			echo "<div class=\"news_mobile_container\">";

			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
			echo "<tr>";
			echo "<td align=\"left\" width=\"100%\">";
			if ($fi && !$sv) {
				echo ("<p class=\"bloggtexten\">Blogissa ei ole merkintää</p>");
			} else {
				echo ("<p class=\"bloggtexten\"><b>Ooops!</b> Något gick snett. Detta blogginlägg finns inte.</p>");
			}
			echo "</td>";
			echo "</tr>";
			echo "</table>";
				
			echo "</div>";
			
		}
		

	}
	
	function getFrontBloggNewFramless($sv,$fi) {
		global $no;
	
		if ($fi && !$sv) {
			$bpage_linc = "/blogi";
		} else {
			$bpage_linc = "/bloggen";
		}

		echo "<div id=\"blogg_panel\">\n";
		echo "<a href=\"$bpage_linc\">";
		if ($fi && !$sv) {
			echo "<div id=\"blogg_header\"><div class=\"blogg_pic_fi\"></div></div>";
		} else {
			echo "<div id=\"blogg_header\"><div class=\"blogg_pic\"></div></div>";
		}
		echo "</a>\n";
		echo "<div id=\"blogg_container\">\n";
		$firstrow = true;
		$countrow = 1;
		
		$select  = "SELECT cnt, titel, link, skapad, blogType ";	
		$select .= "FROM cyberphoto.blog ";	
		$select .= "WHERE offentlig = -1 AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() ";	
		// $select .= "AND blogType IN(19) ";	
		// $select .= "AND (blogType IN(19,30) OR (blogType=1 AND cnt > 8674) OR (blogType=2 AND cnt > 8674) OR (cnt IN (8543,8659))) ";	
		if ($fi && !$sv) {
			$select .= "AND blogType IN(28,29,30) ";
		} else {
			$select .= "AND blogType IN(1,2,19) ";
		}
		if ($fi) {
			$select .= "AND not_fi = 0 ";
		}
		if ($no) {
			$select .= "AND not_no = 0 ";
		}
		$select .= "ORDER BY skapad DESC LIMIT 16";	

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);
			if ($firstrow) {
				echo "<a href=\"$bpage_linc\">";
			} else {
				echo "<a href=\"$bpage_linc#".$cnt."\">";
			}
			echo "<div style=\"height: 32px;\">\n";
			
			echo "<div class=\"top7\"></div>\n";
			
			$skapad = $this->formDate($skapad);
			$skapad = preg_replace('/may/','maj',$skapad);
			$skapad = preg_replace('/oct/','okt',$skapad);
			
			if (strlen($titel) > 19)
				$titel = substr ($titel, 0, 19) . "..";

			echo "<div class=\"frontbloggheadline\">$titel</div>";
			
			/*
			if ($firstrow) {
				echo "<a class=\"frontbloggheadline\" href=\"/blogg.php\">$titel</a>";
			} else {
				echo "<a class=\"frontbloggheadline\" href=\"/blogg.php#".$cnt."\">$titel</a>";
			}
			echo "</div>\n";
			*/
			
			echo "<div class=\"top5\"></div>\n";

			if ($blogType == 29) {
				echo "<div class=\"testcontainer\">Testi</div>\n";
			} elseif ($blogType == 30) {
				echo "<div class=\"newscontainer\">Uutuus</div>\n";
			} elseif ($blogType == 28) {
				echo "<div class=\"bloggcontainer\">Blogi</div>\n";
			} elseif ($blogType == 1) {
				echo "<div class=\"testcontainer\">Test</div>\n";
			} elseif ($blogType == 2) {
				echo "<div class=\"newscontainer\">Nyhet</div>\n";
			} else {
				echo "<div class=\"bloggcontainer\">Blogg</div>\n";
			}
			
			if ($countrow > 16) {
				echo "<div class=\"frontbloggseplast\">";
			} else {
				echo "<div class=\"frontbloggsep\">";
			}
			echo "<span class=\"frontbloggdateline\">$skapad</span>";
			/*
			if ($skapad != $current_skapad) {
				echo "<span class=\"frontbloggdateline\">$skapad</span>";
			} else {
				echo "<span class=\"frontbloggdateline\">&nbsp;</span>";
			}
			$current_skapad = $skapad;
			*/
			echo "</div>\n";
			
			echo "</div></a>\n";
			$firstrow = false;
			$countrow++;
			
		}

		echo "<div class=\"clear\"></div>";
		echo "<div class=\"blogg_readmore\">";
		echo "<a class=\"frontblogglinc\" href=\"$bpage_linc\">Läs mera >></a>";
		echo "</div>\n";

		echo "</div>\n";
		echo "</div>\n";

	}
	
	function formDate($bloggdate) {
	
		// echo $bloggdate;
		$bloggdate = strtotime($bloggdate);
	
		if (date('Y-m-d', $bloggdate) == date('Y-m-d', time())) {
			return "Idag " . date('H:i', $bloggdate);
		} elseif (date('Y-m-d', $bloggdate) == date('Y-m-d', strtotime("-1 day"))) {
			return "Igår " . date('H:i', $bloggdate);
		} else {
			// return date('Y-m-d', $bloggdate);
			return strtolower(date('j M', $bloggdate));
		}
	
	}

	function getFrontTest($sv,$fi) {

		$select  = "SELECT cnt, titel, link FROM cyberphoto.blog WHERE offentlig = -1 ";	
		$select  .= "AND blogType IN(1,5,9,21) AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() AND ((link_pic LIKE '%jpg') OR (link_pic LIKE '%jpeg')) ";	
		$select  .= "ORDER BY skapad DESC LIMIT 8";	

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);

			$link = eregi_replace("\?info", "info", $link);
			$link = eregi_replace("http://www.cyberphoto.se/", "", $link);

		  if ($row["link"] != "") {
			$link = $link;
			} else {
			$link = "news?ID=" .$row["cnt"];
			}

			echo "<a href=\"../".$link."\">$titel</a><br>";
		}

	}

	function getFrontNews($sv,$fi) {

		$select  = "SELECT cnt, blogType, titel, link FROM cyberphoto.blog WHERE offentlig = -1 ";	
		// $select  .= "AND blogType IN(2,3,6,7,10) AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() AND (link_pic LIKE '%jpg') ";	
		$select  .= "AND blogType IN(2,6,10,22) AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() AND ((link_pic LIKE '%jpg') OR (link_pic LIKE '%jpeg')) ";	
		$select  .= "ORDER BY skapad DESC LIMIT 8";	

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);
			
		if ($row["blogType"] == 2 || $row["blogType"] == 6 || $row["blogType"] == 10) {
		
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

	function getFrontBlogg($sv,$fi) {

		$firstrow = true;
		
		$select  = "SELECT cnt, titel, link FROM cyberphoto.blog WHERE offentlig = -1 ";	
		$select  .= "AND blogType IN(19) AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() ";	
		$select  .= "ORDER BY skapad DESC LIMIT 8";	

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);

			if (strlen($titel) >= 20)
				$titel = substr ($titel, 0, 20) . "..";

			if ($firstrow) {
				echo "<a href=\"/blogg.php\">$titel</a><br>";
			} else {
				echo "<a href=\"/blogg.php#".$cnt."\">$titel</a><br>";
			}
			
			$firstrow = false;
		}

	}

	function getLatestProducts() {
		global $sv, $fi;

		$firstrow = true;
		
		if ($fi && !$sv) {
			$select  = "SELECT titel_fi AS titel, beskrivning_fi AS beskrivning, link, link_pic, ";
		} else {
			$select  = "SELECT titel, beskrivning, link, link_pic, ";
		}
		if ($fi) {
			$select  .= "DATE_FORMAT(skapad, '%d-%m-%Y') AS PubDate ";
		} else {
			$select  .= "DATE_FORMAT(skapad, '%Y-%m-%d') AS PubDate ";
		}
		$select  .= "FROM cyberphoto.blog ";	
		$select  .= "WHERE  offentlig = -1 AND blogType IN(2,6,10) ";	
		if ($fi && !$sv) {
			$select  .= "AND NOT (beskrivning_fi IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() AND ((link_pic LIKE '%jpg') OR (link_pic LIKE '%jpeg')) ";
		} else {
			$select  .= "AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() AND ((link_pic LIKE '%jpg') OR (link_pic LIKE '%jpeg')) ";
		}
		$select  .= "ORDER BY skapad DESC LIMIT 50 ";	

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);

			if ($fi && !$sv) {
				$link = preg_replace("/\?info/", "info_fi", $link);
			} elseif ($fi && $sv) {
				$link = preg_replace("/\?info/", "info_fi_se", $link);
			} else {
				$link = preg_replace("/\?info/", "info", $link);
			}
			$link = eregi_replace("http://www.cyberphoto.se/", "", $link);

			echo "<div id=\"container\">\n";
			echo "<div class=\"roundtop\">\n";
			echo "<div class=\"r1\"></div>\n";
			echo "<div class=\"r2\"></div>\n";
			echo "<div class=\"r3\"></div>\n";
			echo "<div class=\"r4\"></div>\n";
			echo "</div>\n";
			echo "<div class=\"content\">\n";
			
			echo "<div class=\"pubtitel\">$titel</div>\n";
			echo "<div class=\"pubdate\">$PubDate</div>\n";
			echo "<div class=\"pubpicture\"><a href=\"/".$link."\"><img border='0' src='$link_pic'></a></div>";
			echo "<div class=\"pubtext\">$beskrivning</div>\n";

			echo "</div>\n";
			echo "<div class=\"roundbottom\">\n";
			echo "<div class=\"r4\"></div>\n";
			echo "<div class=\"r3\"></div>\n";
			echo "<div class=\"r2\"></div>\n";
			echo "<div class=\"r1\"></div>\n";
			echo "</div>\n";
			echo "</div>\n";
			
			echo "<br>";
			// echo "<p>&nbsp;</p>";


		}

	}

	function getLatestTests() {
		global $sv, $fi;

		$firstrow = true;
		
		if ($fi && !$sv) {
			$select  = "SELECT titel_fi AS titel, beskrivning_fi AS beskrivning, link, link_pic, ";
		} else {
			$select  = "SELECT titel, beskrivning, link, link_pic, ";
		}
		if ($fi) {
			$select  .= "DATE_FORMAT(skapad, '%d-%m-%Y') AS PubDate ";
		} else {
			$select  .= "DATE_FORMAT(skapad, '%Y-%m-%d') AS PubDate ";
		}
		$select  .= "FROM cyberphoto.blog ";	
		$select  .= "WHERE  offentlig = -1 AND blogType IN(1,5,9) ";	
		if ($fi && !$sv) {
			$select  .= "AND NOT (beskrivning_fi IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() AND ((link_pic LIKE '%jpg') OR (link_pic LIKE '%jpeg')) ";
		} else {
			$select  .= "AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() AND ((link_pic LIKE '%jpg') OR (link_pic LIKE '%jpeg')) ";
		}
		$select  .= "ORDER BY skapad DESC LIMIT 50 ";	

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);

			if ($fi && !$sv) {
				$link = preg_replace("/\?info/", "info_fi", $link);
			} elseif ($fi && $sv) {
				$link = preg_replace("/\?info/", "info_fi_se", $link);
			} else {
				$link = preg_replace("/\?info/", "info", $link);
			}
			$link = eregi_replace("http://www.cyberphoto.se/", "", $link);

			echo "<div id=\"container\">\n";
			echo "<div class=\"roundtop\">\n";
			echo "<div class=\"r1\"></div>\n";
			echo "<div class=\"r2\"></div>\n";
			echo "<div class=\"r3\"></div>\n";
			echo "<div class=\"r4\"></div>\n";
			echo "</div>\n";
			echo "<div class=\"content\">\n";
			
			echo "<div class=\"pubtitel\">$titel</div>\n";
			echo "<div class=\"pubdate\">$PubDate</div>\n";
			if ($fi && !$sv) {
				echo "<div class=\"pubpicturetest_fi\"><a href=\"/".$link."\"><img border='0' src='$link_pic'></a></div>";
			} else {
				echo "<div class=\"pubpicturetest\"><a href=\"/".$link."\"><img border='0' src='$link_pic'></a></div>";
			}
			echo "<div class=\"pubtext\">$beskrivning</div>\n";

			echo "</div>\n";
			echo "<div class=\"roundbottom\">\n";
			echo "<div class=\"r4\"></div>\n";
			echo "<div class=\"r3\"></div>\n";
			echo "<div class=\"r2\"></div>\n";
			echo "<div class=\"r1\"></div>\n";
			echo "</div>\n";
			echo "</div>\n";
			
			echo "<br>";
			// echo "<p>&nbsp;</p>";


		}

	}

	function getFrontBloggNew($sv,$fi) {

		echo "<a href=\"/blogg.php\"><div style=\"height: 35px;\"></div></a>\n";
		$firstrow = true;
		$countrow = 1;
		
		$select  = "SELECT cnt, titel, link, skapad, blogType ";	
		$select .= "FROM cyberphoto.blog ";	
		$select .= "WHERE offentlig = -1 AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) AND skapad < now() ";	
		// $select .= "AND blogType IN(19) ";	
		// $select .= "AND (blogType IN(19,30) OR (blogType=1 AND cnt > 8674) OR (blogType=2 AND cnt > 8674) OR (cnt IN (8543,8659))) ";	
		$select .= "AND blogType IN(1,2,19,30) ";
		$select .= "ORDER BY skapad DESC LIMIT 16";	

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_array($res)) {

			extract ($row);
			if ($firstrow) {
				echo "<a href=\"/blogg.php\">";
			} else {
				echo "<a href=\"/blogg.php#".$cnt."\">";
			}
			echo "<div style=\"height: 31px;\">\n";
			
			echo "<div class=\"top7\"></div>\n";
			
			$skapad = $this->formDate($skapad);
			$skapad = preg_replace('/may/','maj',$skapad);
			$skapad = preg_replace('/oct/','okt',$skapad);
			
			if (strlen($titel) > 19)
				$titel = substr ($titel, 0, 19) . "..";

			echo "<div class=\"frontbloggheadline\">$titel</div>";
			
			/*
			if ($firstrow) {
				echo "<a class=\"frontbloggheadline\" href=\"/blogg.php\">$titel</a>";
			} else {
				echo "<a class=\"frontbloggheadline\" href=\"/blogg.php#".$cnt."\">$titel</a>";
			}
			echo "</div>\n";
			*/
			
			echo "<div class=\"top5\"></div>\n";

			if ($blogType == 1) {
				echo "<div class=\"testcontainer\">Test</div>\n";
			} elseif ($blogType == 2) {
				echo "<div class=\"newscontainer\">Nyhet</div>\n";
			} else {
				echo "<div class=\"bloggcontainer\">Blogg</div>\n";
			}
			
			if ($countrow > 16) {
				echo "<div class=\"frontbloggseplast\">";
			} else {
				echo "<div class=\"frontbloggsep\">";
			}
			echo "<span class=\"frontbloggdateline\">$skapad</span>";
			/*
			if ($skapad != $current_skapad) {
				echo "<span class=\"frontbloggdateline\">$skapad</span>";
			} else {
				echo "<span class=\"frontbloggdateline\">&nbsp;</span>";
			}
			$current_skapad = $skapad;
			*/
			echo "</div>\n";
			
			echo "</div></a>\n";
			$firstrow = false;
			$countrow++;
			
		}

		echo "<div class=\"top7\">";
		echo "<a class=\"frontblogglinc\" href=\"/blogg.php\">Läs mera >></a>";
		echo "</div>\n";

	}
	
}

?>

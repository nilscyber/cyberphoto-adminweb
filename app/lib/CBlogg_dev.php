<?php

// include("connections.php");
require_once("CCheckIpNumber.php");


Class CBlogg {

	var $conn_my; 
	var $conn_my2; 
	var $conn_my3; 
	var $conn_fi;

	function __construct() {

		global $fi;

		$this->conn_my = Db::getConnection();
		$this->conn_fi = $this->conn_ms;
		$this->conn_my2 = Db::getConnectionDb('cyberadmin');
		$this->conn_my3 = Db::getConnectionDb('cyberadmin');

	}

	function getBloggNewPart110923($number,$search,$mobile) {

		global $fi, $sv, $comment, $month2;
		
		// echo $month2;

		$sida = $_SERVER['PHP_SELF'];

		if ($search != "") {
			$searchwords = preg_split("/[\s]+/", $search);
		}
		
		if ($period != "" && $period != 5) {
			if ($period == 1) {
				$showperiod = date("Y-m-d", (time() - (60*60*24*7)));
			} elseif ($period == 2) {
				$showperiod = date("Y-m-d", (time() - (60*60*24*14)));
			} elseif ($period == 3) {
				$showperiod = date("Y-m-d", (time() - (60*60*24*30)));
			} elseif ($period == 4) {
				$showperiod = date("Y-m-d", (time() - (60*60*24*60)));
			}
		}
		
		if ($fi && !$sv) {
			$select = "SELECT cnt, titel_fi, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style, blogType ";
		} else {
			$select = "SELECT cnt, titel, titel_fi, beskrivning, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style, blogType ";
		}
		$select .= "FROM blog ";
		if (!$mobile && ($_SERVER['REMOTE_ADDR'] == "192.168.1.65" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89")) {
			$select .= "WHERE offentlig = -1 AND blogType IN (19,23) ";
		} elseif ($mobile) {
			$select .= "WHERE offentlig = -1 AND blogType IN (23) ";
		} else {
			$select .= "WHERE offentlig = -1 AND blogType IN (19) ";
		}
		$select .= "AND skapad < now() ";

		if ($search != "") {

			$select .= "AND ( ";

			for ($i = 0; $i < count($searchwords);$i++) {
					if ($i == 0) {
						$select .= "(titel like '%" . $searchwords[$i] . "%' OR beskrivning like '%" . $searchwords[$i] . "%') ";
					} else {
						$select .= "OR (titel like '%" . $searchwords[$i] . "%' OR beskrivning like '%" . $searchwords[$i] . "%') ";
					}
			}

			$select .= ") ";
		}

		if ($fi && !$sv) {
			$select .= "AND NOT (titel_fi IS Null) AND NOT (beskrivning_fi IS NUll) AND NOT (link_pic IS NULL) ";
		} else {
			$select .= "AND NOT (titel IS Null) AND NOT (beskrivning IS NUll) AND NOT (link_pic IS NULL) ";
		}
		
		if ($month2 != "") {
			$select .= "AND DATE_FORMAT(skapad, '%Y-%m') = '" . $month2 . "' ";
		}
		
		$select .= "ORDER BY skapad DESC ";

		if ($month2 == "") {
			$select .= "LIMIT $number ";
		}
		
		// echo $select;
		
		
		$res = mysqli_query($this->conn_my, $select);
			
		if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_object($res)) {
			
				$cnt = $row->cnt;
				
				if ($fi && !$sv) {
					$titel = $row->titel_fi;
					$beskrivning = $row->beskrivning_fi;
				} else {
					$titel = $row->titel;
					$beskrivning = $row->beskrivning;
				}
				
				$pubdate = date("Y-m-d H:i",strtotime($row->PubDate));
				$isblogtype = $row->blogType;
			
				$beskrivning = eregi_replace("\n", "<br>", $beskrivning);
				$beskrivning = str_replace("\\", "", $beskrivning);
				
				if ($fi && $sv) {
					$beskrivning = eregi_replace("info.php", "info_fi_se.php", $beskrivning);
				}
				
				// echo "<a class=\"toplink\" name=\"$cnt\">";
				echo "<div id=\"container\">";
				echo "<div class=\"roundtop\">";
				echo "<div class=\"r1\"></div>";
				echo "<div class=\"r2\"></div>";
				echo "<div class=\"r3\"></div>";
				echo "<div class=\"r4\"></div>";
				echo "</div>";
				echo "<div class=\"content\">";

				echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
				
				if ($row->blog_style == 1) {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					if (eregi(".gif", $row->link_pic) || eregi(".png", $row->link_pic)) {
						echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
					} elseif (eregi("_small.jpg", $row->link_pic)) {
						$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
						echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://www.cyberphoto.se/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\"></a>";
					} else {
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
					}
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<p class=\"bloggtexten\">$beskrivning</p>";
					echo "</td>";
					echo "</tr>";
				
				} else {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					if (eregi(".gif", $row->link_pic) || eregi(".png", $row->link_pic)) {
						echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
					} elseif (eregi("_small.jpg", $row->link_pic)) {
						$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
						echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://www.cyberphoto.se/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\"></a>";
						// echo "<a target=\"_blank\" href=\"/blogg/$bildstor\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\"></a>";
					} else {
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
					}
					echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
					echo "<p class=\"bloggtexten\">$beskrivning</p>";
					echo "</td>";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td align=\"right\" width=\"100%\">";
				echo "<p class=\"pub\">Publicerad: $pubdate&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>";
				echo "</td>";
				echo "</tr>";
				// if ($_SERVER['REMOTE_ADDR'] == "81.8.240.115") {
				if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
				echo "<tr>";
				echo "<td align=\"right\" width=\"100%\">";
				echo "<span class=\"pub\"><a class=\"pub\" href=\"javascript:winPopupCenter(450, 900, 'http://www.cyberphoto.se/order/admin/newblogg.php?change=$cnt');\">Uppdatera blogg</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
				echo "</td>";
				echo "</tr>";
					if (!$mobile && $isblogtype == 23 && ($_SERVER['REMOTE_ADDR'] == "192.168.1.65" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89")) {
						echo "<tr>";
						echo "<td align=\"right\" width=\"100%\">";
						echo "<span class=\"ismobile\">** Mobilbloggen **&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
						echo "</td>";
						echo "</tr>";
					}
				}
				echo "<tr>";
				echo "<td align=\"left\" width=\"100%\">";
				if ($this->getTotalComments($cnt) < 1) {
					echo "<p class=\"commentheader\">Det finns inga kommentarer till detta inlägg.&nbsp;|&nbsp;<a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				} elseif ($this->getTotalComments($cnt) == 1) {
					echo "<p class=\"commentheader\">Det finns <b><a class=\"commentheader\" href=\"$sida?comment=$cnt&number=$number&search=$search&month=$month2#$cnt\">" . $this->getTotalComments($cnt) . "</a></b> kommentar. <a class=\"commentheader\" href=\"$sida?comment=$cnt&number=$number&search=$search&month=$month2#$cnt\">Visa kommentar</a>&nbsp;|&nbsp;<a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				} else {
					echo "<p class=\"commentheader\">Det finns <b><a class=\"commentheader\" href=\"$sida?comment=$cnt&number=$number&search=$search&month=$month2#$cnt\">" . $this->getTotalComments($cnt) . "</a></b> kommentarer. <a class=\"commentheader\" href=\"$sida?comment=$cnt&number=$number&search=$search&month=$month2#$cnt\">Visa kommentarer</a>&nbsp;|&nbsp;<a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				}
				echo "&nbsp;|&nbsp;URL: http://www.cyberphoto.se/blogg.php?ID=$cnt</p>";
				if ($number == 25 || $search != "") { // detta visas bara om resultaten inte är för många
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
						echo "<iframe src=\"http://www.facebook.com/plugins/like.php?href=http://www.cyberphoto.se/blogg.php?ID=$cnt&amp;layout=standard&amp;show_faces=false&amp;width=550&amp;action=like&amp;font=verdana&amp;colorscheme=light&amp;height=20\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:550px; height:20px;\" allowTransparency=\"true\"></iframe>";
						// echo "<fb:like href=\"http://www.cyberphoto.se/blogg.php?ID=$cnt\" send=\"false\" width=\"450\" show_faces=\"true\" font=\"verdana\"></fb:like>";
					} else {
						// echo "<fb:like href=\"http://www.cyberphoto.se/blogg.php?ID=$cnt\" send=\"false\" width=\"450\" show_faces=\"true\" font=\"verdana\"></fb:like>";
						echo "<iframe src=\"http://www.facebook.com/plugins/like.php?href=http://www.cyberphoto.se/blogg.php?ID=$cnt&amp;layout=standard&amp;show_faces=false&amp;width=550&amp;action=like&amp;font=verdana&amp;colorscheme=light&amp;height=20\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:550px; height:20px;\" allowTransparency=\"true\"></iframe>";
					}
					// echo "<iframe src=\"http://www.facebook.com/plugins/like.php?href=http://www.cyberphoto.se/blogg.php?ID=$cnt&amp;layout=button_count&amp;show_faces=false&amp;width=250&amp;action=like&amp;font=verdana&amp;colorscheme=light&amp;height=21\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:250px; height:21px;\" allowTransparency=\"true\"></iframe>";
					// echo "<iframe src=\"http://www.facebook.com/widgets/like.php?href=http://www.cyberphoto.se/blogg.php?ID=$cnt\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; width:450px; height:20px\"></iframe>";
				}
				echo "</td>";
				echo "</tr>";
				if ($comment == $cnt) {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<div align=\"right\">";
					echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"75%\">";
						
						echo "<tr>";
						echo "<td width=\"100%\">";
						$this->showComments($cnt);
						echo "</td>";
						echo "</tr>";
					echo "</table>";
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
				
				echo "</div>";
				echo "<div class=\"roundbottom\">";
				echo "<div class=\"r4\"></div>";
				echo "<div class=\"r3\"></div>";
				echo "<div class=\"r2\"></div>";
				echo "<div class=\"r1\"></div>";
				echo "</div>";
				echo "</div>";

				echo "&nbsp;<a class=\"toplink\" href=\"#top\">Till toppen</a>";
			
				}

		} else {
		

			echo "<div id=\"container\">";
			echo "<div class=\"roundtop\">";
			echo "<div class=\"r1\"></div>";
			echo "<div class=\"r2\"></div>";
			echo "<div class=\"r3\"></div>";
			echo "<div class=\"r4\"></div>";
			echo "</div>";
			echo "<div class=\"content\">";

			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
			echo "<tr>";
			echo "<td align=\"left\" width=\"100%\">";
			if ($fi && !$sv) {
				echo "<p class=\"bloggtexten\">Blogissa ei ole merkintää</p>";
			} else {
				echo "<p class=\"bloggtexten\">Sökning på <b>$search</b> gav inget inlägg i bloggen. <a href=\"$sida\">Rensa</a></p>";
			}
			echo "</td>";
			echo "</tr>";
			echo "</table>";
				
			echo "</div>";
			echo "<div class=\"roundbottom\">";
			echo "<div class=\"r4\"></div>";
			echo "<div class=\"r3\"></div>";
			echo "<div class=\"r2\"></div>";
			echo "<div class=\"r1\"></div>";
			echo "</div>";
			echo "</div>";

		}
		

	}

	function getBlogg_v2($number,$search,$mobile) {

		global $fi, $sv, $comment, $month2, $start;
		
		$sida = $_SERVER['PHP_SELF'];
		
		if ($search != "") {
			$searchwords = preg_split("/[\s]+/", $search);
			
			if(!($start > 0)) {                         // This variable is set to zero for the first page
				$start = 0;
			}
			$eu = ($start -0);                
			$limit = 25;                                 // No of records to be shown per page.
			$this1 = $eu + $limit; 
			$back = $eu - $limit; 
			$next = $eu + $limit; 		
		
			$select2 = "SELECT cnt ";
			$select2 .= "FROM blog ";
			if (!$mobile && ($_SERVER['REMOTE_ADDR'] == "192.168.1.65" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89")) {
				$select2 .= "WHERE offentlig = -1 AND blogType IN (19,23) ";
			} elseif ($mobile) {
				$select2 .= "WHERE offentlig = -1 AND blogType IN (23) ";
			} else {
				$select2 .= "WHERE offentlig = -1 AND blogType IN (19) ";
			}
			$select2 .= "AND skapad < now() ";

			if ($search != "") {

				$select2 .= "AND ( ";

				for ($i = 0; $i < count($searchwords);$i++) {
						if ($i == 0) {
							$select2 .= "(titel like '%" . $searchwords[$i] . "%' OR beskrivning like '%" . $searchwords[$i] . "%') ";
						} else {
							$select2 .= "AND (titel like '%" . $searchwords[$i] . "%' OR beskrivning like '%" . $searchwords[$i] . "%') ";
						}
				}

				$select2 .= ") ";
			}

			if ($fi && !$sv) {
				$select2 .= "AND NOT (titel_fi IS Null) AND NOT (beskrivning_fi IS NUll) AND NOT (link_pic IS NULL) ";
			} else {
				$select2 .= "AND NOT (titel IS Null) AND NOT (beskrivning IS NUll) AND NOT (link_pic IS NULL) ";
			}
		
			if ($month2 != "") {
				$select2 .= "AND DATE_FORMAT(skapad, '%Y-%m') = '" . $month2 . "' ";
			}
			
			$res2 = mysqli_query($this->conn_my, $select2);
			$nume = mysqli_num_rows($res2);
			
			// echo $tickets;
			// exit;
		
		}
		
		if ($fi && !$sv) {
			$select = "SELECT cnt, titel_fi, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style, blogType ";
		} else {
			$select = "SELECT cnt, titel, titel_fi, beskrivning, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style, blogType ";
		}
		$select .= "FROM blog ";
		if (!$mobile && ($_SERVER['REMOTE_ADDR'] == "192.168.1.65" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89")) {
			$select .= "WHERE offentlig = -1 AND blogType IN (19,23) ";
		} elseif ($mobile) {
			$select .= "WHERE offentlig = -1 AND blogType IN (23) ";
		} else {
			$select .= "WHERE offentlig = -1 AND blogType IN (19) ";
		}
		$select .= "AND skapad < now() ";

		if ($search != "") {

			$select .= "AND ( ";

			for ($i = 0; $i < count($searchwords);$i++) {
					if ($i == 0) {
						$select .= "(titel like '%" . $searchwords[$i] . "%' OR beskrivning like '%" . $searchwords[$i] . "%') ";
					} else {
						$select .= "AND (titel like '%" . $searchwords[$i] . "%' OR beskrivning like '%" . $searchwords[$i] . "%') ";
					}
			}

			$select .= ") ";
		}

		if ($fi && !$sv) {
			$select .= "AND NOT (titel_fi IS Null) AND NOT (beskrivning_fi IS NUll) AND NOT (link_pic IS NULL) ";
		} else {
			$select .= "AND NOT (titel IS Null) AND NOT (beskrivning IS NUll) AND NOT (link_pic IS NULL) ";
		}
		
		if ($month2 != "") {
			$select .= "AND DATE_FORMAT(skapad, '%Y-%m') = '" . $month2 . "' ";
		}
		
		$select .= "ORDER BY skapad DESC ";

		if ($search != "") {
			$select .= "LIMIT $eu, $limit ";
		} else {
			$select .= "LIMIT $number ";
		}
		
		// echo $select;
		// exit;
		
		
		$res = mysqli_query($this->conn_my, $select);
			
		if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_object($res)) {
			
				$cnt = $row->cnt;
				
				if ($fi && !$sv) {
					$titel = $row->titel_fi;
					$beskrivning = $row->beskrivning_fi;
				} else {
					$titel = $row->titel;
					$beskrivning = $row->beskrivning;
				}
				
				$pubdate = date("Y-m-d H:i",strtotime($row->PubDate));
				$isblogtype = $row->blogType;
			
				$beskrivning = eregi_replace("\n", "<br>", $beskrivning);
				$beskrivning = str_replace("\\", "", $beskrivning);
				
				if ($fi && $sv) {
					$beskrivning = eregi_replace("info.php", "info_fi_se.php", $beskrivning);
				}
				
				// echo "<a class=\"toplink\" name=\"$cnt\">";
				echo "<div id=\"container\">";
				echo "<div class=\"roundtop\">";
				echo "<div class=\"r1\"></div>";
				echo "<div class=\"r2\"></div>";
				echo "<div class=\"r3\"></div>";
				echo "<div class=\"r4\"></div>";
				echo "</div>";
				echo "<div class=\"content\">";

				echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
				
				if ($row->blog_style == 1) {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					if (eregi(".gif", $row->link_pic) || eregi(".png", $row->link_pic)) {
						echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
					} elseif (eregi("_small.jpg", $row->link_pic)) {
						$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
						echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://www.cyberphoto.se/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\"></a>";
					} else {
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
					}
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<p class=\"bloggtexten\">$beskrivning</p>";
					echo "</td>";
					echo "</tr>";
				
				} else {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					if (eregi(".gif", $row->link_pic) || eregi(".png", $row->link_pic)) {
						echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
					} elseif (eregi("_small.jpg", $row->link_pic)) {
						$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
						echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://www.cyberphoto.se/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\"></a>";
						// echo "<a target=\"_blank\" href=\"/blogg/$bildstor\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\"></a>";
					} else {
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
					}
					echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
					echo "<p class=\"bloggtexten\">$beskrivning</p>";
					echo "</td>";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td align=\"right\" width=\"100%\">";
				echo "<p class=\"pub\">Publicerad: $pubdate&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>";
				echo "</td>";
				echo "</tr>";
				// if ($_SERVER['REMOTE_ADDR'] == "81.8.240.115") {
				if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
				echo "<tr>";
				echo "<td align=\"right\" width=\"100%\">";
				echo "<span class=\"pub\"><a class=\"pub\" href=\"javascript:winPopupCenter(450, 900, 'http://www.cyberphoto.se/order/admin/newblogg.php?change=$cnt');\">Uppdatera blogg</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
				echo "</td>";
				echo "</tr>";
					if (!$mobile && $isblogtype == 23 && ($_SERVER['REMOTE_ADDR'] == "192.168.1.65" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89")) {
						echo "<tr>";
						echo "<td align=\"right\" width=\"100%\">";
						echo "<span class=\"ismobile\">** Mobilbloggen **&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
						echo "</td>";
						echo "</tr>";
					}
				}
				echo "<tr>";
				echo "<td align=\"left\" width=\"100%\">";
				if ($this->getTotalComments($cnt) < 1) {
					echo "<p class=\"commentheader\">Det finns inga kommentarer till detta inlägg.&nbsp;|&nbsp;<a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				} elseif ($this->getTotalComments($cnt) == 1) {
					echo "<p class=\"commentheader\">Det finns <b><a class=\"commentheader\" href=\"$sida?comment=$cnt&number=$number&search=$search&month=$month2#$cnt\">" . $this->getTotalComments($cnt) . "</a></b> kommentar. <a class=\"commentheader\" href=\"$sida?comment=$cnt&number=$number&search=$search&month=$month2#$cnt\">Visa kommentar</a>&nbsp;|&nbsp;<a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				} else {
					echo "<p class=\"commentheader\">Det finns <b><a class=\"commentheader\" href=\"$sida?comment=$cnt&number=$number&search=$search&month=$month2#$cnt\">" . $this->getTotalComments($cnt) . "</a></b> kommentarer. <a class=\"commentheader\" href=\"$sida?comment=$cnt&number=$number&search=$search&month=$month2#$cnt\">Visa kommentarer</a>&nbsp;|&nbsp;<a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				}
				echo "&nbsp;|&nbsp;URL: http://www.cyberphoto.se/blogg.php?ID=$cnt</p>";
				echo "<div class=\"blogg_fb\"><fb:like href=\"http://www.cyberphoto.se/blogg.php?ID=$cnt\" send=\"false\" width=\"90\" show_faces=\"false\" layout=\"button_count\"></fb:like></div>";
				echo "<div class=\"blogg_gplus\"><g:plusone size=\"medium\" href=\"http://www.cyberphoto.se/blogg.php?ID=$cnt\"></g:plusone></div>";
				echo "</td>";
				echo "</tr>";
				if ($comment == $cnt) {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<div align=\"right\">";
					echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"75%\">";
						
						echo "<tr>";
						echo "<td width=\"100%\">";
						$this->showComments($cnt);
						echo "</td>";
						echo "</tr>";
					echo "</table>";
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
				
				echo "</div>";
				echo "<div class=\"roundbottom\">";
				echo "<div class=\"r4\"></div>";
				echo "<div class=\"r3\"></div>";
				echo "<div class=\"r2\"></div>";
				echo "<div class=\"r1\"></div>";
				echo "</div>";
				echo "</div>";

				echo "&nbsp;<a class=\"toplink\" href=\"#top\">Till toppen</a>";

				}

		} else {
		

			echo "<div id=\"container\">";
			echo "<div class=\"roundtop\">";
			echo "<div class=\"r1\"></div>";
			echo "<div class=\"r2\"></div>";
			echo "<div class=\"r3\"></div>";
			echo "<div class=\"r4\"></div>";
			echo "</div>";
			echo "<div class=\"content\">";

			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
			echo "<tr>";
			echo "<td align=\"left\" width=\"100%\">";
			if ($fi && !$sv) {
				echo "<p class=\"bloggtexten\">Blogissa ei ole merkintää</p>";
			} else {
				echo "<p class=\"bloggtexten\">Sökning på <b>$search</b> gav inget inlägg i bloggen. <a href=\"$sida\">Rensa</a></p>";
			}
			echo "</td>";
			echo "</tr>";
			echo "</table>";
				
			echo "</div>";
			echo "<div class=\"roundbottom\">";
			echo "<div class=\"r4\"></div>";
			echo "<div class=\"r3\"></div>";
			echo "<div class=\"r2\"></div>";
			echo "<div class=\"r1\"></div>";
			echo "</div>";
			echo "</div>";

		}
		
				if ($search != "") {
					
					$p_limit=500; // This should be more than $limit and set to a value for whick links to be breaked

					if(!($p_f > 0)) {                         // This variable is set to zero for the first page
						$p_f = 0;
					}
					$p_fwd=$p_f+$p_limit;
					$p_back=$p_f-$p_limit;
				
					echo "<p class=\"mainlink\">&nbsp;Totalt $nume träffar på $search</p>";
					
					if ($nume > 25) {
						if($back >=0 and ($back >=$p_f)) {
							echo "<p>&nbsp;<a class=\"pagelink\" href='$page_name?start=$back&search=$search'><font face='Verdana' size='2'>Föregående sida</font></a>&nbsp;"; 
						} 
						for($i=$p_f;$i < $nume and $i<($p_f+$p_limit);$i=$i+$limit){
							if($i <> $eu){
								$i2=$i+$p_f;
								echo " <a class=\"pagelink\" href='$page_name?start=$i&search=$search'>$i</a> ";
							} else { 
								echo "<span class=\"pageredlink\">$i</span>";
							}        /// Current page is not displayed as link and given font color red

						}


						if($this1 < $nume) {
							echo "&nbsp;<a class=\"pagelink\" href='$page_name?start=$next&search=$search'><font face='Verdana' size='2'>Nästa sida</font></a></p>";
						} 
					}
					
				}

	}
	
	function getBloggSpec_v2($ID) {

		global $fi, $sv;
		
		$sida = $_SERVER['PHP_SELF'];

		if ($fi && !$sv) {
		
		$select = "SELECT cnt, titel_fi, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style ";
		
		} else {
		
		$select = "SELECT cnt, titel, titel_fi, beskrivning, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style ";
		
		}
		
		$select .= "FROM blog ";
		
		$select .= "WHERE offentlig = -1 AND blogType IN (19,23) ";
		
		$select .= "AND skapad < now() ";

		if ($fi && !$sv) {
		
		$select .= "AND NOT (titel_fi IS Null) AND NOT (beskrivning_fi IS NUll) AND NOT (link_pic IS NULL) ";
		
		} else {
		
		$select .= "AND NOT (titel IS Null) AND NOT (beskrivning IS NUll) AND NOT (link_pic IS NULL) ";
		
		}

		$select .= "AND cnt = $ID ";
		
		// echo $select;
		
		
		$res = mysqli_query($this->conn_my, $select);
			
		if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_object($res)) {
			
			
				$cnt = $row->cnt;
				
				if ($fi && !$sv) {

					$titel = $row->titel_fi;
					$beskrivning = $row->beskrivning_fi;
					
				} else {
					
					$titel = $row->titel;
					$beskrivning = $row->beskrivning;
					
				}
				
				$pubdate = date("Y-m-d H:i",strtotime($row->PubDate));
			
				$beskrivning = eregi_replace("\n", "<br>", $beskrivning);
				$beskrivning = str_replace("\\", "", $beskrivning);
				
				if ($fi && $sv) {
					$beskrivning = eregi_replace("info.php", "info_fi_se.php", $beskrivning);
				}
				
				// echo "<a class=\"toplink\" name=\"$cnt\">";
				echo "<div id=\"container\">";
				echo "<div class=\"roundtop\">";
				echo "<div class=\"r1\"></div>";
				echo "<div class=\"r2\"></div>";
				echo "<div class=\"r3\"></div>";
				echo "<div class=\"r4\"></div>";
				echo "</div>";
				echo "<div class=\"content\">";

				echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
				
				if ($row->blog_style == 1) {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					if (eregi(".gif", $row->link_pic) || eregi(".png", $row->link_pic)) {
						echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
					} elseif (eregi("_small.jpg", $row->link_pic)) {
						$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
						echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://www.cyberphoto.se/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\"></a>";
					} else {
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
					}
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<p class=\"bloggtexten\">$beskrivning</p>";
					echo "</td>";
					echo "</tr>";
				
				} else {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					if (eregi(".gif", $row->link_pic)) {
						echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
					} elseif (eregi("_small.jpg", $row->link_pic)) {
						$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
						echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://www.cyberphoto.se/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\"></a>";
						// echo "<a target=\"_blank\" href=\"/blogg/$bildstor\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\"></a>";
					} else {
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
					}
					echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
					echo "<p class=\"bloggtexten\">$beskrivning</p>";
					echo "</td>";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td align=\"right\" width=\"100%\">";
				echo "<p class=\"pub\">Publicerad: $pubdate&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>";
				echo "</td>";
				echo "</tr>";
				// if ($_SERVER['REMOTE_ADDR'] == "81.8.240.115") {
				if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
				echo "<tr>";
				echo "<td align=\"right\" width=\"100%\">";
				echo "<p class=\"pub\"><a class=\"pub\" href=\"javascript:winPopupCenter(450, 900, 'http://www.cyberphoto.se/order/admin/newblogg.php?change=$cnt');\">Uppdatera blogg</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>";
				echo "</td>";
				echo "</tr>";
				}
				echo "<tr>";
				echo "<td align=\"left\" width=\"100%\">";
				if ($this->getTotalComments($cnt) < 1) {
					echo "<p class=\"commentheader\">Det finns inga kommentarer till detta inlägg.&nbsp;|&nbsp;<a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				} else {
					echo "<p class=\"commentheader\"><a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				}
				echo "&nbsp;|&nbsp;URL: http://www.cyberphoto.se/blogg.php?ID=$cnt</p>";
				// echo "<fb:like href=\"http://www.cyberphoto.se/blogg.php?ID=$cnt\" send=\"false\" width=\"450\" show_faces=\"true\" font=\"verdana\"></fb:like>";
				echo "<iframe src=\"http://www.facebook.com/plugins/like.php?href=http://www.cyberphoto.se/blogg.php?ID=$cnt&amp;layout=standard&amp;show_faces=false&amp;width=550&amp;action=like&amp;font=verdana&amp;colorscheme=light&amp;height=20\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:550px; height:20px;\" allowTransparency=\"true\"></iframe>";
				echo "</td>";
				echo "</tr>";
				if (!$this->getTotalComments($cnt) < 1) {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<div align=\"right\">";
					echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"75%\">";
						
						echo "<tr>";
						echo "<td width=\"100%\">";
						$this->showComments($cnt);
						echo "</td>";
						echo "</tr>";
					echo "</table>";
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
				
				echo "</div>";
				echo "<div class=\"roundbottom\">";
				echo "<div class=\"r4\"></div>";
				echo "<div class=\"r3\"></div>";
				echo "<div class=\"r2\"></div>";
				echo "<div class=\"r1\"></div>";
				echo "</div>";
				echo "</div>";

				echo "&nbsp;<a class=\"toplink\" href=\"#top\">Till toppen</a>";
			
				}

		} else {
		

			echo "<div id=\"container\">";
			echo "<div class=\"roundtop\">";
			echo "<div class=\"r1\"></div>";
			echo "<div class=\"r2\"></div>";
			echo "<div class=\"r3\"></div>";
			echo "<div class=\"r4\"></div>";
			echo "</div>";
			echo "<div class=\"content\">";

			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
			echo "<tr>";
			echo "<td align=\"left\" width=\"100%\">";
			if ($fi && !$sv) {
				echo "<p class=\"bloggtexten\">Blogissa ei ole merkintää</p>";
			} else {
				echo "<p class=\"bloggtexten\"><b>Ooops!</b> Något gick snett. Detta blogginlägg finns inte.</p>";
			}
			echo "</td>";
			echo "</tr>";
			echo "</table>";
				
			echo "</div>";
			echo "<div class=\"roundbottom\">";
			echo "<div class=\"r4\"></div>";
			echo "<div class=\"r3\"></div>";
			echo "<div class=\"r2\"></div>";
			echo "<div class=\"r1\"></div>";
			echo "</div>";
			echo "</div>";

		}
		

	}

	function getBloggSpec_v3($ID) {

		global $fi, $sv;
		
		$sida = $_SERVER['PHP_SELF'];

		if ($fi && !$sv) {
		
		$select = "SELECT cnt, titel_fi, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style ";
		
		} else {
		
		$select = "SELECT cnt, titel, titel_fi, beskrivning, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style ";
		
		}
		
		$select .= "FROM blog ";
		
		$select .= "WHERE offentlig = -1 AND blogType IN (19,23) ";
		
		$select .= "AND skapad < now() ";

		if ($fi && !$sv) {
		
		$select .= "AND NOT (titel_fi IS Null) AND NOT (beskrivning_fi IS NUll) AND NOT (link_pic IS NULL) ";
		
		} else {
		
		$select .= "AND NOT (titel IS Null) AND NOT (beskrivning IS NUll) AND NOT (link_pic IS NULL) ";
		
		}

		$select .= "AND cnt = $ID ";
		
		// echo $select;
		
		
		$res = mysqli_query($this->conn_my, $select);
			
		if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_object($res)) {
			
			
				$cnt = $row->cnt;
				
				if ($fi && !$sv) {

					$titel = $row->titel_fi;
					$beskrivning = $row->beskrivning_fi;
					
				} else {
					
					$titel = $row->titel;
					$beskrivning = $row->beskrivning;
					
				}
				
				$pubdate = date("Y-m-d H:i",strtotime($row->PubDate));
			
				$beskrivning = eregi_replace("\n", "<br>", $beskrivning);
				$beskrivning = str_replace("\\", "", $beskrivning);
				
				if ($fi && $sv) {
					$beskrivning = eregi_replace("info.php", "info_fi_se.php", $beskrivning);
				}
				
				// echo "<a class=\"toplink\" name=\"$cnt\">";
				echo "<div id=\"container\">";
				echo "<div class=\"roundtop\">";
				echo "<div class=\"r1\"></div>";
				echo "<div class=\"r2\"></div>";
				echo "<div class=\"r3\"></div>";
				echo "<div class=\"r4\"></div>";
				echo "</div>";
				echo "<div class=\"content\">";

				echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
				
				if ($row->blog_style == 1) {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					if (eregi(".gif", $row->link_pic) || eregi(".png", $row->link_pic)) {
						echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
					} elseif (eregi("_small.jpg", $row->link_pic)) {
						$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
						echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://www.cyberphoto.se/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\"></a>";
					} else {
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
					}
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<p class=\"bloggtexten\">$beskrivning</p>";
					echo "</td>";
					echo "</tr>";
				
				} else {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					if (eregi(".gif", $row->link_pic)) {
						echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
					} elseif (eregi("_small.jpg", $row->link_pic)) {
						$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
						echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://www.cyberphoto.se/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\"></a>";
						// echo "<a target=\"_blank\" href=\"/blogg/$bildstor\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\"></a>";
					} else {
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
					}
					echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
					echo "<p class=\"bloggtexten\">$beskrivning</p>";
					echo "</td>";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td align=\"right\" width=\"100%\">";
				echo "<p class=\"pub\">Publicerad: $pubdate&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>";
				echo "</td>";
				echo "</tr>";
				// if ($_SERVER['REMOTE_ADDR'] == "81.8.240.115") {
				if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
				echo "<tr>";
				echo "<td align=\"right\" width=\"100%\">";
				echo "<p class=\"pub\"><a class=\"pub\" href=\"javascript:winPopupCenter(450, 900, 'http://www.cyberphoto.se/order/admin/newblogg.php?change=$cnt');\">Uppdatera blogg</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>";
				echo "</td>";
				echo "</tr>";
				}
				echo "<tr>";
				echo "<td align=\"left\" width=\"100%\">";
				if ($this->getTotalComments($cnt) < 1) {
					echo "<p class=\"commentheader\">Det finns inga kommentarer till detta inlägg.&nbsp;|&nbsp;<a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				} else {
					echo "<p class=\"commentheader\"><a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				}
				echo "&nbsp;|&nbsp;URL: http://www.cyberphoto.se/blogg.php?ID=$cnt</p>";
				// echo "<fb:like href=\"http://www.cyberphoto.se/blogg.php?ID=$cnt\" send=\"false\" width=\"450\" show_faces=\"true\" font=\"verdana\"></fb:like>";
				// echo "<iframe src=\"http://www.facebook.com/plugins/like.php?href=http://www.cyberphoto.se/blogg.php?ID=$cnt&amp;layout=standard&amp;show_faces=false&amp;width=550&amp;action=like&amp;font=verdana&amp;colorscheme=light&amp;height=20\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:550px; height:20px;\" allowTransparency=\"true\"></iframe>";
				echo "<div class=\"blogg_fb\"><fb:like href=\"http://www.cyberphoto.se/blogg.php?ID=$cnt\" send=\"false\" width=\"90\" show_faces=\"false\" layout=\"button_count\"></fb:like></div>";
				echo "<div class=\"blogg_gplus\"><g:plusone size=\"medium\" href=\"http://www.cyberphoto.se/blogg.php?ID=$cnt\"></g:plusone></div>";
				echo "</td>";
				echo "</tr>";
				if (!$this->getTotalComments($cnt) < 1) {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<div align=\"right\">";
					echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"75%\">";
						
						echo "<tr>";
						echo "<td width=\"100%\">";
						$this->showComments($cnt);
						echo "</td>";
						echo "</tr>";
					echo "</table>";
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
				
				echo "</div>";
				echo "<div class=\"roundbottom\">";
				echo "<div class=\"r4\"></div>";
				echo "<div class=\"r3\"></div>";
				echo "<div class=\"r2\"></div>";
				echo "<div class=\"r1\"></div>";
				echo "</div>";
				echo "</div>";

				echo "&nbsp;<a class=\"toplink\" href=\"#top\">Till toppen</a>";
				echo "<p>&nbsp;<a class=\"mainlink\" href=\"/blogg.php\">Läs mera i bloggen, klicka här!</a></p>";
			
				}

		} else {
		

			echo "<div id=\"container\">";
			echo "<div class=\"roundtop\">";
			echo "<div class=\"r1\"></div>";
			echo "<div class=\"r2\"></div>";
			echo "<div class=\"r3\"></div>";
			echo "<div class=\"r4\"></div>";
			echo "</div>";
			echo "<div class=\"content\">";

			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
			echo "<tr>";
			echo "<td align=\"left\" width=\"100%\">";
			if ($fi && !$sv) {
				echo "<p class=\"bloggtexten\">Blogissa ei ole merkintää</p>";
			} else {
				echo "<p class=\"bloggtexten\"><b>Ooops!</b> Något gick snett. Detta blogginlägg finns inte.</p>";
			}
			echo "</td>";
			echo "</tr>";
			echo "</table>";
				
			echo "</div>";
			echo "<div class=\"roundbottom\">";
			echo "<div class=\"r4\"></div>";
			echo "<div class=\"r3\"></div>";
			echo "<div class=\"r2\"></div>";
			echo "<div class=\"r1\"></div>";
			echo "</div>";
			echo "</div>";

		}
		

	}
	
	function getPhotoWorldBloggSpec_v1($ID) {

		global $fi, $sv;
		
		$sida = $_SERVER['PHP_SELF'];

		if ($fi && !$sv) {
		
		$select = "SELECT cnt, titel_fi, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style ";
		
		} else {
		
		$select = "SELECT cnt, titel, titel_fi, beskrivning, beskrivning_fi, DATE_FORMAT(skapad, '%Y-%m-%d %T') AS PubDate, link, link_pic, blog_style ";
		
		}
		
		$select .= "FROM blog ";
		
		$select .= "WHERE offentlig = -1 AND blogType IN (19) ";
		
		$select .= "AND skapad < now() ";

		if ($fi && !$sv) {
		
		$select .= "AND NOT (titel_fi IS Null) AND NOT (beskrivning_fi IS NUll) AND NOT (link_pic IS NULL) ";
		
		} else {
		
		$select .= "AND NOT (titel IS Null) AND NOT (beskrivning IS NUll) AND NOT (link_pic IS NULL) ";
		
		}

		$select .= "AND cnt = $ID ";
		
		// echo $select;
		
		
		$res = mysqli_query($this->conn_my, $select);
			
		if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_object($res)) {
			
			
				$cnt = $row->cnt;
				
				if ($fi && !$sv) {

					$titel = $row->titel_fi;
					$beskrivning = $row->beskrivning_fi;
					
				} else {
					
					$titel = $row->titel;
					$beskrivning = $row->beskrivning;
					
				}
				
				$pubdate = date("Y-m-d H:i",strtotime($row->PubDate));
			
				$beskrivning = eregi_replace("\n", "<br>", $beskrivning);
				$beskrivning = str_replace("\\", "", $beskrivning);
				
				if ($fi && $sv) {
					$beskrivning = eregi_replace("info.php", "info_fi_se.php", $beskrivning);
				}
				
				// echo "<a class=\"toplink\" name=\"$cnt\">";
				echo "<div id=\"container\">";
				echo "<div class=\"roundtop\">";
				echo "<div class=\"r1\"></div>";
				echo "<div class=\"r2\"></div>";
				echo "<div class=\"r3\"></div>";
				echo "<div class=\"r4\"></div>";
				echo "</div>";
				echo "<div class=\"content\">";

				echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
				
				if ($row->blog_style == 1) {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					if (eregi(".gif", $row->link_pic) || eregi(".png", $row->link_pic)) {
						echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
					} elseif (eregi("_small.jpg", $row->link_pic)) {
						$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
						echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://www.cyberphoto.se/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\"></a>";
					} else {
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\">";
					}
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<p class=\"bloggtexten\">$beskrivning</p>";
					echo "</td>";
					echo "</tr>";
				
				} else {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					if (eregi(".gif", $row->link_pic)) {
						echo "<img class=\"imgnoborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
					} elseif (eregi("_small.jpg", $row->link_pic)) {
						$bildstor = ereg_replace  ("_small.jpg","_big.jpg", $row->link_pic);
						echo "<a href=\"javascript:winPopupCenter(800, 1024, 'http://www.cyberphoto.se/blogg/$bildstor');\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\"></a>";
						// echo "<a target=\"_blank\" href=\"/blogg/$bildstor\"><img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\"></a>";
					} else {
						echo "<img class=\"imgborder\" border=\"0\" src=\"/blogg/$row->link_pic\" align=\"right\" hspace=\"15\" vspace=\"5\">";
					}
					echo "<a class=\"toplink\" name=\"$cnt\"><h1>$titel</h1></a>";
					echo "<p class=\"bloggtexten\">$beskrivning</p>";
					echo "</td>";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td align=\"right\" width=\"100%\">";
				echo "<p class=\"pub\">Publicerad: $pubdate&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>";
				echo "</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td align=\"left\" width=\"100%\">";
				if ($this->getTotalComments($cnt) < 1) {
					echo "<p class=\"commentheader\">Det finns inga kommentarer till detta inlägg.&nbsp;|&nbsp;<a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				} else {
					echo "<p class=\"commentheader\"><a class=\"commentheader\" href=\"javascript:winPopupCenter(330, 580, 'http://www.cyberphoto.se/blogg/newcomment.php?ID=$cnt');\">Lägg till kommentar</a>";
				}
				echo "&nbsp;|&nbsp;URL: http://www.cyberphoto.se/blogg.php?ID=$cnt</p>";
				// echo "<iframe src=\"http://www.facebook.com/plugins/like.php?href=http://www.cyberphoto.se/blogg.php?ID=$cnt&amp;layout=standard&amp;show_faces=false&amp;width=550&amp;action=like&amp;font=verdana&amp;colorscheme=light&amp;height=20\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:550px; height:20px;\" allowTransparency=\"true\"></iframe>";
				echo "</td>";
				echo "</tr>";
				if (!$this->getTotalComments($cnt) < 1) {
					echo "<tr>";
					echo "<td align=\"left\" width=\"100%\">";
					echo "<div align=\"right\">";
					echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"75%\">";
						
						echo "<tr>";
						echo "<td width=\"100%\">";
						$this->showComments($cnt);
						echo "</td>";
						echo "</tr>";
					echo "</table>";
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
				
				echo "</div>";
				echo "<div class=\"roundbottom\">";
				echo "<div class=\"r4\"></div>";
				echo "<div class=\"r3\"></div>";
				echo "<div class=\"r2\"></div>";
				echo "<div class=\"r1\"></div>";
				echo "</div>";
				echo "</div>";

				// echo "&nbsp;<a class=\"toplink\" href=\"#top\">Till toppen</a>";
			
				}

		} else {
		

			echo "<div id=\"container\">";
			echo "<div class=\"roundtop\">";
			echo "<div class=\"r1\"></div>";
			echo "<div class=\"r2\"></div>";
			echo "<div class=\"r3\"></div>";
			echo "<div class=\"r4\"></div>";
			echo "</div>";
			echo "<div class=\"content\">";

			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
			echo "<tr>";
			echo "<td align=\"left\" width=\"100%\">";
			if ($fi && !$sv) {
				echo "<p class=\"bloggtexten\">Blogissa ei ole merkintää</p>";
			} else {
				echo "<p class=\"bloggtexten\"><b>Ooops!</b> Något gick snett. Detta blogginlägg finns inte.</p>";
			}
			echo "</td>";
			echo "</tr>";
			echo "</table>";
				
			echo "</div>";
			echo "<div class=\"roundbottom\">";
			echo "<div class=\"r4\"></div>";
			echo "<div class=\"r3\"></div>";
			echo "<div class=\"r2\"></div>";
			echo "<div class=\"r1\"></div>";
			echo "</div>";
			echo "</div>";

		}
		

	}

	function getTotalComments($bloggID) {

	$select  = "SELECT COUNT(bcID) AS Antal FROM bloggComment WHERE bcActive = 1 AND bcBID = '" . $bloggID . "' ";

	$res = @mysqli_query($this->conn_my3, $select);

		while ($row = @mysqli_fetch_array($res)) {
		
		extract($row);
		
		return $Antal;

		}

	}

	function getAnstallda() {

	global $who;

	$select  = "SELECT sign, namn FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";

	$res = mysqli_query($this->conn_my2, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		echo "<option value=\"$sign\"";
			
		if ($who == $sign) {
			echo " selected";
		}
			
		echo ">" . $namn . "</option>\n";
			
		
		// endwhile;

		}

	}

	function getBloggType($type) {
		global $bloggtype;

		$conn_my = Db::getConnection(true);
		
		
		$select  = "SELECT blogType_id, blogType ";
		$select  .= "FROM blogType ";
		if ($type == "system") {
			$select  .= "WHERE blogType_id > 11 AND blogType_id < 18 ";
		} else {
			// $select  .= "WHERE (blogType_id < 12 OR blogType_id = 20 OR blogType_id = 21) ";
			$select  .= "WHERE blogType_id IN(0,1,2,4,5,6,8,9,10,20,21,22,24,25,25,26) ";
		}
		$select  .= "ORDER BY blogType_id ";

		// $res = mssql_query ($select);
		$res = mysqli_query($conn_my, $select);


			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

			echo "<option value=\"$blogType_id\"";
				
			if ($bloggtype == $blogType_id) {
				echo " selected";
			}
				
			echo ">" . $blogType . "</option>\n";
				
			
			// endwhile;

			}

	}

	function getSpecBlogg($bloggID) {

	$select  = "SELECT * FROM blog WHERE cnt = '" . $bloggID . "' ";

	$res = mysqli_query($this->conn_my, $select);

	$rows = mysqli_fetch_object($res);

	return $rows;

	}

	function getSpecProduct($article) {

	$select  = "SELECT Artiklar.beskrivning, Tillverkare.tillverkare, Artiklar.bild ";
	$select  .= "FROM Artiklar ";
	$select  .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
	$select  .= "WHERE Artiklar.artnr = '" . $article . "' ";

	$res = mysqli_query($this->conn_my, $select);

	$rows = mysqli_fetch_object($res);

	return $rows;

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

	function AddProductBlogg($headline,$bloggtext,$who,$picture,$bloggtime,$bloggtype,$headline_fi,$bloggtext_fi,$productlink) {

		$conn_my = Db::getConnection(true);

		/*
		if ($bloggtext_fi != "") {
			mssql_query ("INSERT INTO blog (titel,beskrivning,offentlig,inlagd_av,skapad,link_pic,blogType,titel_fi,beskrivning_fi,link) VALUES ('$headline','$bloggtext',-1,'$who','$bloggtime','$picture','$bloggtype','$headline_fi','$bloggtext_fi','$productlink') ");
		} else {
			mssql_query ("INSERT INTO blog (titel,beskrivning,offentlig,inlagd_av,skapad,link_pic,blogType,titel_fi,beskrivning_fi,link) VALUES ('$headline','$bloggtext',-1,'$who','$bloggtime','$picture','$bloggtype',NULL,NULL,'$productlink') ");
		}
		*/

		if ($bloggtext_fi != "") {
			$updt = "INSERT INTO blog (titel,beskrivning,offentlig,inlagd_av,skapad,link_pic,blogType,titel_fi,beskrivning_fi,link) VALUES ('$headline','$bloggtext',-1,'$who','$bloggtime','$picture','$bloggtype','$headline_fi','$bloggtext_fi','$productlink')";
		} else {
			$updt = "INSERT INTO blog (titel,beskrivning,offentlig,inlagd_av,skapad,link_pic,blogType,titel_fi,beskrivning_fi,link) VALUES ('$headline','$bloggtext',-1,'$who','$bloggtime','$picture','$bloggtype',NULL,NULL,'$productlink')";
		}
		
		// echo $updt;
		// exit;
		
		$res = mysqli_query($conn_my, $updt);
		
	}

	function ChangeProductBlogg($addid,$headline,$bloggtext,$who,$picture,$bloggtime,$bloggtype,$headline_fi,$bloggtext_fi,$productlink) {

		$conn_my = Db::getConnection(true);

		/*
		if ($bloggtext_fi != "") {
			mysqli_query("UPDATE blog SET titel = '$headline', beskrivning = '$bloggtext', inlagd_av = '$who', link_pic = '$picture', skapad = '$bloggtime', blogType = '$bloggtype', titel_fi = '$headline_fi', beskrivning_fi = '$bloggtext_fi', link = '$productlink' WHERE cnt = '$addid' ");
		} else {
			mysqli_query("UPDATE blog SET titel = '$headline', beskrivning = '$bloggtext', inlagd_av = '$who', link_pic = '$picture', skapad = '$bloggtime', blogType = '$bloggtype', titel_fi = NULL, beskrivning_fi = NULL, link = '$productlink' WHERE cnt = '$addid' ");
		}
		*/

		if ($bloggtext_fi != "") {
			$updt = "UPDATE blog SET titel = '$headline', beskrivning = '$bloggtext', inlagd_av = '$who', link_pic = '$picture', skapad = '$bloggtime', blogType = '$bloggtype', titel_fi = '$headline_fi', beskrivning_fi = '$bloggtext_fi', link = '$productlink' WHERE cnt = '$addid' ";
		} else {
			$updt = "UPDATE blog SET titel = '$headline', beskrivning = '$bloggtext', inlagd_av = '$who', link_pic = '$picture', skapad = '$bloggtime', blogType = '$bloggtype', titel_fi = NULL, beskrivning_fi = NULL, link = '$productlink' WHERE cnt = '$addid' ";
		}

		// echo $updt;
		// exit;
		$res = mysqli_query($conn_my, $updt);

	}

	function AddBlogg($rubrik,$bloggtext,$who,$picture,$bloggtime) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("INSERT INTO blog (titel,beskrivning,offentlig,inlagd_av,skapad,link_pic,blogType) VALUES ('$rubrik','$bloggtext',-1,'$who','$bloggtime','$picture',19) ");
		$updt = "INSERT INTO blog (titel,beskrivning,offentlig,inlagd_av,skapad,link_pic,blogType) VALUES ('$rubrik','$bloggtext',-1,'$who','$bloggtime','$picture',19)";

		// echo $updt;
		// exit;
		$res = mysqli_query($conn_my, $updt);

	}

	function AddBlogg_v2($rubrik,$bloggtext,$who,$picture,$bloggtime,$addblogstyle) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("INSERT INTO blog (titel,beskrivning,offentlig,inlagd_av,skapad,link_pic,blogType) VALUES ('$rubrik','$bloggtext',-1,'$who','$bloggtime','$picture',19) ");
		$updt = "INSERT INTO blog (titel,beskrivning,offentlig,inlagd_av,skapad,link_pic,blogType,blog_style) VALUES ('$rubrik','$bloggtext',-1,'$who','$bloggtime','$picture',19,'$addblogstyle')";
		// echo $updt;
		// exit;
		
		$res = mysqli_query($conn_my, $updt);

	}

	function AddMobileBlogg_v1($rubrik,$bloggtext,$who,$picture,$bloggtime,$addblogstyle) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("INSERT INTO blog (titel,beskrivning,offentlig,inlagd_av,skapad,link_pic,blogType) VALUES ('$rubrik','$bloggtext',-1,'$who','$bloggtime','$picture',19) ");
		$updt = "INSERT INTO blog (titel,beskrivning,offentlig,inlagd_av,skapad,link_pic,blogType,blog_style) VALUES ('$rubrik','$bloggtext',-1,'$who','$bloggtime','$picture',23,'$addblogstyle')";
		// echo $updt;
		// exit;
		
		$res = mysqli_query($conn_my, $updt);

	}

	function ChangeBlogg($addid,$rubrik,$bloggtext,$who,$picture,$bloggtime) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("UPDATE blog SET titel = '$rubrik', beskrivning = '$bloggtext', inlagd_av = '$who', link_pic = '$picture', skapad = '$bloggtime' WHERE cnt = '$addid' ");
		$updt = "UPDATE blog SET titel = '$rubrik', beskrivning = '$bloggtext', inlagd_av = '$who', link_pic = '$picture', skapad = '$bloggtime' WHERE cnt = '$addid'";

		$res = mysqli_query($conn_my, $updt);

	}

	function ChangeBlogg_v2($addid,$rubrik,$bloggtext,$who,$picture,$bloggtime,$addblogstyle) {

		$conn_my = Db::getConnection(true);

		// mssql_query ("UPDATE blog SET titel = '$rubrik', beskrivning = '$bloggtext', inlagd_av = '$who', link_pic = '$picture', skapad = '$bloggtime' WHERE cnt = '$addid' ");
		$updt = "UPDATE blog SET titel = '$rubrik', beskrivning = '$bloggtext', inlagd_av = '$who', link_pic = '$picture', skapad = '$bloggtime', blog_style = '$addblogstyle' WHERE cnt = '$addid'";
		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

	}

	function AddComment($namn,$epost,$kommentar,$bloggID,$mobile) {

		$conn_my = Db::getConnectionDb('cyberadmin');

		$ip = $_SERVER['REMOTE_ADDR'];

		// mysqli_query("INSERT INTO bloggComment (bcBID,bcName,bcMail,bcComment,bcIP,bcMobile) VALUES ('$bloggID','$namn','$epost','$kommentar','$ip','$mobile') ");
		$updt = "INSERT INTO bloggComment (bcBID,bcName,bcMail,bcComment,bcIP,bcMobile) VALUES ('$bloggID','$namn','$epost','$kommentar','$ip','$mobile') ";
		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

		$this->sendMessBlogg($namn,$epost,$kommentar,$bloggID,$ip);

	}

	function AcceptComment($ID) {

		$conn_my = Db::getConnectionDb('cyberadmin');

		// mysqli_query("UPDATE bloggComment SET bcActive = 1 WHERE bcID = '$ID' ");
		$updt = "UPDATE bloggComment SET bcActive = 1 WHERE bcID = '$ID' ";
		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

	}

	function DenyComment($ID) {

		$conn_my = Db::getConnectionDb('cyberadmin');

		// mysqli_query("UPDATE bloggComment SET bcActive = 2 WHERE bcID = '$ID' ");
		$updt = "UPDATE bloggComment SET bcActive = 2 WHERE bcID = '$ID' ";
		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

	}

	function sendMessBlogg($namn,$epost,$kommentar,$bloggID,$ip) {

		$getbloggID = $this->getCommentID($namn,$bloggID,$ip);
		$bloggHeader = $this->getBloggHaeder($bloggID);
		
		$recipient .= " blogg";
		// $recipient .= " sjabo";
		
		$subj = "Ny kommentar till bloggen!";

		if ($epost == "") {
			$extra = "From: anonym@cyberphoto.se";
		} else {
			$extra = "From: " . $epost;
		}
		
		$text1 = "Ny kommentar i bloggen:\n\n" . $kommentar . "\n\n";
		$text1 .= "Blogginlägg:\t" . $bloggHeader . "\n\n";
		$text1 .= "Godkänn inlägget genom att klicka nedan.\nhttp://www.cyberphoto.se/blogg/accept.php?ID=" . $getbloggID . "";	
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}

	function getCommentID($namn,$bloggID,$ip) {

	$select  = "SELECT MAX(bcID) AS idnummer FROM bloggComment WHERE bcName = '" . $namn . "' AND bcBID = '" . $bloggID . "' AND bcIP = '" . $ip . "' ";

	$res = mysqli_query($this->conn_my3, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);
		
		return $idnummer;

		}

	}

	function showComments($cnt) {

	$select  = "SELECT bcName, bcComment, bcTime, bcIP FROM bloggComment WHERE bcBID = '" . $cnt . "' AND bcActive = 1 ORDER BY bcTime DESC ";

	$res = mysqli_query($this->conn_my3, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		$bcComment = eregi_replace("\n", "<br>", $bcComment);
		
		echo "&nbsp;";
		echo "<div id=\"container_comment\">";
		echo "<div class=\"roundtop_comment\">";
		echo "<div class=\"r1_comment\"></div>";
		echo "<div class=\"r2_comment\"></div>";
		echo "<div class=\"r3_comment\"></div>";
		echo "<div class=\"r4_comment\"></div>";
		echo "</div>";
		echo "<div class=\"content_comment\">";
		if (CCheckIP::checkIpAdress($bcIP)) {
			echo "<img align=\"left\" border=\"0\" src=\"/blogg/cyberphoto_stamp.jpg\"><br>";
		} else {
			echo "<p align=\"left\" class=\"commentname\">$bcName</p>";
		}
		echo "<p align=\"left\" class=\"commenttexten\">$bcComment</p>";
		echo "<p align=\"left\" class=\"commentttiden\">$bcTime</p>";
		echo "</div>";
		echo "<div class=\"roundbottom_comment\">";
		echo "<div class=\"r4_comment\"></div>";
		echo "<div class=\"r3_comment\"></div>";
		echo "<div class=\"r2_comment\"></div>";
		echo "<div class=\"r1_comment\"></div>";
		echo "</div>";
		echo "</div>";

		}

	}

	function showCommentsNotPub() {

	$sida = $_SERVER['PHP_SELF'];

	$select  = "SELECT bcID, bcBID, bcName, bcMail, bcComment, bcTime, bcActive, bcIP FROM bloggComment WHERE bcActive = 0 ORDER BY bcTime DESC ";

	$res = mysqli_query($this->conn_my3, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		$bcComment = eregi_replace("\n", "<br>", $bcComment);
		$bloggHeader = $this->getBloggHaeder($bcBID);
		
		echo "&nbsp;";
		echo "<div id=\"container_comment\">";
		echo "<div class=\"roundtop_comment\">";
		echo "<div class=\"r1_comment\"></div>";
		echo "<div class=\"r2_comment\"></div>";
		echo "<div class=\"r3_comment\"></div>";
		echo "<div class=\"r4_comment\"></div>";
		echo "</div>";
		echo "<div class=\"content_comment\">";
		echo "<p align=\"left\" class=\"bloggheader\">$bloggHeader</p>";
		if ($bcActive == 1) {
			echo "<p align=\"left\" class=\"commentname\"><img border=\"0\" src=\"/order/admin/status_green.jpg\"></p>";
		} else {
			echo "<p align=\"left\" class=\"commentname\"><img border=\"0\" src=\"/order/admin/status_red.jpg\"></p>";
		}
		if (CCheckIP::checkIpAdress($bcIP)) {
			echo "<p><img border=\"0\" src=\"/blogg/cyberphoto_stamp.jpg\"></p>";
		} else {
			echo "<p align=\"left\" class=\"commentname\">$bcName</p>";
		}
		echo "<p align=\"left\" class=\"commenttexten\">$bcComment</p>";
		if ($bcMail != "") {
			echo "<p align=\"left\" class=\"commenttexten\"><a href=\"mailto:$bcMail\">$bcMail</a></p>";
		}
		echo "<p align=\"left\" class=\"commentttiden\">$bcTime</p>";
		if ($bcActive == 0) {
			echo "<a class=\"acceptlink\" href=\"$sida?accept=" . $bcID . "\">Godkänn detta inlägg!</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a class=\"denylink\" href=\"$sida?deny=" . $bcID . "\">Godkänn EJ inlägg!</a>";
		}
		echo "</div>";
		echo "<div class=\"roundbottom_comment\">";
		echo "<div class=\"r4_comment\"></div>";
		echo "<div class=\"r3_comment\"></div>";
		echo "<div class=\"r2_comment\"></div>";
		echo "<div class=\"r1_comment\"></div>";
		echo "</div>";
		echo "</div>";

		}

	}

	function showCommentsPub() {

	$sida = $_SERVER['PHP_SELF'];

	// $select  = "SELECT bcID, bcBID, bcName, bcMail, bcComment, bcTime, bcActive, bcIP FROM bloggComment WHERE bcActive = 1 ORDER BY bcTime DESC ";
	$select  = "SELECT bcID, bcBID, bcName, bcMail, bcComment, bcTime, bcActive, bcIP ";
	$select  .= "FROM bloggComment ";
	$select  .= "WHERE bcActive = 1 ";
	$select  .= "ORDER BY bcTime DESC ";
	$select  .= "LIMIT 25 ";

	$res = mysqli_query($this->conn_my3, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		$bcComment = eregi_replace("\n", "<br>", $bcComment);
		$bloggHeader = $this->getBloggHaeder($bcBID);
		
		echo "&nbsp;";
		echo "<div id=\"container_comment\">";
		echo "<div class=\"roundtop_comment\">";
		echo "<div class=\"r1_comment\"></div>";
		echo "<div class=\"r2_comment\"></div>";
		echo "<div class=\"r3_comment\"></div>";
		echo "<div class=\"r4_comment\"></div>";
		echo "</div>";
		echo "<div class=\"content_comment\">";
		echo "<p align=\"left\" class=\"bloggheader\">$bloggHeader</p>";
		if ($bcActive == 1) {
			echo "<p align=\"left\" class=\"commentname\"><img border=\"0\" src=\"/order/admin/status_green.jpg\"></p>";
		} else {
			echo "<p align=\"left\" class=\"commentname\"><img border=\"0\" src=\"/order/admin/status_red.jpg\"></p>";
		}
		if (CCheckIP::checkIpAdress($bcIP)) {
			echo "<p><img border=\"0\" src=\"/blogg/cyberphoto_stamp.jpg\"></p>";
		} else {
			echo "<p align=\"left\" class=\"commentname\">$bcName</p>";
		}
		echo "<p align=\"left\" class=\"commenttexten\">$bcComment</p>";
		if ($bcMail != "") {
			echo "<p align=\"left\" class=\"commenttexten\"><a href=\"mailto:$bcMail\">$bcMail</a></p>";
		}
		echo "<p align=\"left\" class=\"commentttiden\">$bcTime</p>";
		echo "<a class=\"denylink\" href=\"$sida?deny=" . $bcID . "\">Godkänn EJ inlägg!</a>";
		echo "</div>";
		echo "<div class=\"roundbottom_comment\">";
		echo "<div class=\"r4_comment\"></div>";
		echo "<div class=\"r3_comment\"></div>";
		echo "<div class=\"r2_comment\"></div>";
		echo "<div class=\"r1_comment\"></div>";
		echo "</div>";
		echo "</div>";

		}

	}

	function showCommentsDenyed() {

	$sida = $_SERVER['PHP_SELF'];

	$select  = "SELECT bcID, bcBID, bcName, bcMail, bcComment, bcTime, bcActive, bcIP ";
	$select  .= "FROM bloggComment ";
	$select  .= "WHERE bcActive = 2 ";
	$select  .= "ORDER BY bcTime DESC ";
	$select  .= "LIMIT 25 ";

	$res = mysqli_query($this->conn_my3, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);

		$bcComment = eregi_replace("\n", "<br>", $bcComment);
		$bloggHeader = $this->getBloggHaeder($bcBID);
		
		echo "&nbsp;";
		echo "<div id=\"container_comment\">";
		echo "<div class=\"roundtop_comment\">";
		echo "<div class=\"r1_comment\"></div>";
		echo "<div class=\"r2_comment\"></div>";
		echo "<div class=\"r3_comment\"></div>";
		echo "<div class=\"r4_comment\"></div>";
		echo "</div>";
		echo "<div class=\"content_comment\">";
		echo "<p align=\"left\" class=\"bloggheader\">$bloggHeader</p>";
		if ($bcActive == 1) {
			echo "<p align=\"left\" class=\"commentname\"><img border=\"0\" src=\"/order/admin/status_green.jpg\"></p>";
		} else {
			echo "<p align=\"left\" class=\"commentname\"><img border=\"0\" src=\"/order/admin/status_red.jpg\"></p>";
		}
		if (CCheckIP::checkIpAdress($bcIP)) {
			echo "<p><img border=\"0\" src=\"/blogg/cyberphoto_stamp.jpg\"></p>";
		} else {
			echo "<p align=\"left\" class=\"commentname\">$bcName</p>";
		}
		echo "<p align=\"left\" class=\"commenttexten\">$bcComment</p>";
		if ($bcMail != "") {
			echo "<p align=\"left\" class=\"commenttexten\"><a href=\"mailto:$bcMail\">$bcMail</a></p>";
		}
		echo "<p align=\"left\" class=\"commentttiden\">$bcTime</p>";
		echo "<a class=\"acceptlink\" href=\"$sida?accept=" . $bcID . "\">Godkänn detta inlägg!</a>";
		echo "</div>";
		echo "<div class=\"roundbottom_comment\">";
		echo "<div class=\"r4_comment\"></div>";
		echo "<div class=\"r3_comment\"></div>";
		echo "<div class=\"r2_comment\"></div>";
		echo "<div class=\"r1_comment\"></div>";
		echo "</div>";
		echo "</div>";

		}

	}

	function getBloggHaeder($bloggID) {

	$select  = "SELECT titel FROM blog WHERE cnt = '" . $bloggID . "'  ";

	$res = mysqli_query($this->conn_my, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);
		
		return $titel;

		}

	}

	function getComments($status) {

	$select  = "SELECT COUNT(bcID) AS Antal FROM bloggComment WHERE bcActive = '" . $status . "' ";

	$res = mysqli_query($this->conn_my3, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);
		
		return $Antal;

		}

	}

	// ************************ HÄR NEDAN TILLHÖR ADMIN ***********************

	function getLatestProductBlogg($bloggtype) {

		$desiderow = true;
		$current_bloggcount = 0;
		
		$select = "SELECT cnt, titel, titel_fi, inlagd_av, skapad, DATE_FORMAT(skapad, '%M %Y') AS PubDate ";
		$select .= "FROM blog ";
		$select .= "JOIN blogType ON blog.blogType = blogType.blogType_id ";
		$select .= "WHERE blog.blogType IN($bloggtype) AND offentlig = -1 ";
		$select .= "ORDER BY skapad DESC ";
		$select .= "LIMIT 500 ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			echo "<tr>\n";
			echo "<td width=\"120\"><b>&nbsp;</b></td>\n";
			echo "<td width=\"200\"><b>Rubrik</b></td>\n";
			echo "<td width=\"200\"><b>Rubrik FI</b></td>\n";
			echo "<td width=\"85\" align=\"center\"><b>Upplagd av</b></td>\n";
			echo "<td><b>&nbsp;</b></td>\n";
			echo "</tr>\n";

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
			
			if ($PubDate != $current_pubdate) {
				if ($current_bloggcount != 0) {
					echo "<tr>\n";
					echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">Totalt: $current_bloggcount st</td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">&nbsp;</td>\n";
					echo "</tr>\n";
					$current_bloggcount = 0;
				}
			echo "<tr>\n";
			echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline\">$PubDate</td>\n";
			echo "</tr>\n";
			}
			$current_pubdate = $PubDate;
			echo "<tr>\n";
			echo "<td class=\"$rowcolor\">" . date("Y-m-d H:i", strtotime($skapad)) . "</td>\n";
			echo "<td class=\"$rowcolor\">$titel</td>\n";
			echo "<td class=\"$rowcolor\">$titel_fi</td>\n";
			echo "<td class=\"$rowcolor\" align=\"center\">$inlagd_av</td>\n";
			echo "<td align=\"center\"><a href=\"javascript:winPopupCenter(550, 650, 'productblogg.php?change=$cnt');\">&nbsp;&nbsp;Ändra</a></td>\n";
			echo "</tr>\n";
			$current_bloggcount ++;

				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
			
			}
			
		}

		echo "<tr>\n";
		echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">Totalt: $current_bloggcount st</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}	

	function getUpCommingBlogg() {

		$desiderow = true;
		$current_bloggcount = 0;
		
		$select = "SELECT cnt, titel, inlagd_av, skapad, DATE_FORMAT(skapad, '%M %Y') AS PubDate ";
		$select .= "FROM blog ";
		$select .= "JOIN blogType ON blog.blogType = blogType.blogType_id ";
		$select .= "WHERE blog.blogType IN(19) AND offentlig = -1 AND skapad > now() ";
		$select .= "ORDER BY skapad ASC ";
		// $select .= "LIMIT 500 ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			echo "<tr>\n";
			echo "<td width=\"120\"><b>&nbsp;</b></td>\n";
			echo "<td width=\"200\"><b>Rubrik</b></td>\n";
			echo "<td width=\"85\" align=\"center\"><b>Upplagd av</b></td>\n";
			echo "<td width=\"65\"><b>&nbsp;</b></td>\n";
			echo "<td width=\"65\"><b>&nbsp;</b></td>\n";
			echo "</tr>\n";

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);

				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
			
			if ($PubDate != $current_pubdate) {
				if ($current_bloggcount != 0) {
					echo "<tr>\n";
					echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">Totalt: $current_bloggcount st</td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">&nbsp;</td>\n";
					echo "</tr>\n";
					$current_bloggcount = 0;
				}
			echo "<tr>\n";
			echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline\">$PubDate</td>\n";
			echo "</tr>\n";
			}
			$current_pubdate = $PubDate;
			echo "<tr>\n";
			echo "<td class=\"$rowcolor\">" . date("Y-m-d H:i", strtotime($skapad)) . "</td>\n";
			echo "<td class=\"$rowcolor\">$titel</td>\n";
			echo "<td class=\"$rowcolor\" align=\"center\">$inlagd_av</td>\n";
			echo "<td align=\"center\"><a href=\"javascript:winPopupCenter(450, 900, 'newblogg.php?change=$cnt');\">Ändra</a></td>\n";
			echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?delete=$cnt\">Ta bort</a></td>\n";
			echo "</tr>\n";
			$current_bloggcount ++;

				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
			
			}
			
		}

		echo "<tr>\n";
		echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">Totalt: $current_bloggcount st</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}	

	function getLatestSystemBlogg($bloggtype) {

		$desiderow = true;
		$current_bloggcount = 0;
		
		$select = "SELECT blog.blogType, cnt, titel, titel_fi, beskrivning, beskrivning_fi, inlagd_av, skapad, DATE_FORMAT(skapad, '%M %Y') AS PubDate ";
		$select .= "FROM blog ";
		$select .= "JOIN blogType ON blog.blogType = blogType.blogType_id ";
		$select .= "WHERE blog.blogType IN($bloggtype) AND offentlig = -1 ";
		$select .= "ORDER BY skapad DESC ";
		$select .= "LIMIT 500 ";
		// echo $select;
		$res = mysqli_query($this->conn_my, $select);

			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"800\">\n";
			echo "<tr>\n";
			echo "<td width=\"20\"><b>&nbsp;</b></td>\n";
			echo "<td width=\"120\"><b>&nbsp;</b></td>\n";
			echo "<td width=\"495\"><b>Meddelande</b></td>\n";
			// echo "<td width=\"200\"><b>Meddelande FI</b></td>\n";
			echo "<td width=\"85\" align=\"center\"><b>Upplagd av</b></td>\n";
			echo "<td><b>&nbsp;</b></td>\n";
			echo "</tr>\n";

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)) {
			
			extract($row);
			$beskrivning = eregi_replace("\n", "<br>", $beskrivning);

				if ($desiderow == true) {
					$rowcolor = "firstrow";
				} else {
					$rowcolor = "secondrow";
				}
			
			if ($PubDate != $current_pubdate) {
				if ($current_bloggcount != 0) {
					echo "<tr>\n";
					echo "<td width=\"20\"><b>&nbsp;</b></td>\n";
					echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">Totalt: $current_bloggcount st</td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "<td width=\"20\"><b>&nbsp;</b></td>\n";
					echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">&nbsp;</td>\n";
					echo "</tr>\n";
					$current_bloggcount = 0;
				}
			echo "<tr>\n";
			echo "<td width=\"20\"><b>&nbsp;</b></td>\n";
			echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline\">$PubDate</td>\n";
			echo "</tr>\n";
			}
			$current_pubdate = $PubDate;
			echo "<tr>\n";
			if ($blogType == 16 || $blogType == 17) {
				echo "<td width=\"20\"><img border=\"0\" src=\"/order/admin/fi_mini.jpg\"></td>\n";
			} else {
				echo "<td width=\"20\"><img border=\"0\" src=\"/order/admin/sv_mini.jpg\"></td>\n";
			}
			echo "<td class=\"$rowcolor\">" . date("Y-m-d H:i", strtotime($skapad)) . "</td>\n";
			echo "<td class=\"$rowcolor\">$beskrivning</td>\n";
			// echo "<td class=\"$rowcolor\">$beskrivning_fi</td>\n";
			echo "<td class=\"$rowcolor\" align=\"center\">$inlagd_av</td>\n";
			echo "<td align=\"center\"><a href=\"javascript:winPopupCenter(550, 650, 'productblogg.php?change=$cnt&addsys=yes');\">&nbsp;&nbsp;Ändra</a></td>\n";
			echo "</tr>\n";
			$current_bloggcount ++;

				if ($desiderow == true) {
					$desiderow = false;
				} else {
					$desiderow = true;
				}
			
			}
			
		}

		echo "<tr>\n";
		echo "<td width=\"20\"><b>&nbsp;</b></td>\n";
		echo "<td colspan=\"5\" align=\"left\" class=\"dateheadline_zero\">Totalt: $current_bloggcount st</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}	

	function getBloggMonth($month2) {
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			echo $artnr2;
		}
		// echo $artnr2;
		
		$select = "SELECT DISTINCT (DATE_FORMAT(skapad, '%Y-%m')) AS PubMonth, DATE_FORMAT(skapad, '%M') AS PubMonthName, DATE_FORMAT(skapad, '%Y') AS PubMonthYear ";
		$select .= "FROM blog ";
		$select .= "WHERE offentlig = -1 AND skapad < now() AND blogType IN(19) AND NOT (titel IS NULL) AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) ";
		$select .= "ORDER BY PubMonth DESC ";
		// echo $select;
		// exit;
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<select name=\"month\" onchange=\"this.form.submit();\">\n";
			echo "<option value=\"\">&nbsp;</option>\n";

			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
				
				$PubMonthName = preg_replace("/January/", "Januari", $PubMonthName);
				$PubMonthName = preg_replace("/February/", "Februari", $PubMonthName);
				$PubMonthName = preg_replace("/March/", "Mars", $PubMonthName);
				$PubMonthName = preg_replace("/May/", "Maj", $PubMonthName);
				$PubMonthName = preg_replace("/June/", "Juni", $PubMonthName);
				$PubMonthName = preg_replace("/July/", "Juli", $PubMonthName);
				$PubMonthName = preg_replace("/August/", "Augusti", $PubMonthName);
				$PubMonthName = preg_replace("/October/", "Oktober", $PubMonthName);
				
				echo "\t\t<option value=\"$PubMonth\"";
				
				if ($PubMonth == $month2) {
					echo " selected";
				}
				
				echo ">" . $PubMonthYear . " " . $PubMonthName . "&nbsp;&nbsp;</option> \n";
			
			}

			echo "</select>\n";
			
		}

	}	

	function getBloggMonth_v2($month2,$mobile) {
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			echo $artnr2;
		}
		// echo $artnr2;
		
		$select = "SELECT DISTINCT (DATE_FORMAT(skapad, '%Y-%m')) AS PubMonth, DATE_FORMAT(skapad, '%M') AS PubMonthName, DATE_FORMAT(skapad, '%Y') AS PubMonthYear ";
		$select .= "FROM blog ";
		if ($mobile) {
			$select .= "WHERE blogType IN(23) ";
		} else {
			$select .= "WHERE blogType IN(19) ";
		}
		$select .= "AND offentlig = -1 AND skapad < now() AND NOT (titel IS NULL) AND NOT (beskrivning IS NULL) AND NOT (link_pic IS NULL) ";
		$select .= "ORDER BY PubMonth DESC ";
		// echo $select;
		// exit;
		$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			echo "<select name=\"month\" onchange=\"this.form.submit();\">\n";
			echo "<option value=\"\">&nbsp;</option>\n";

			while ($row = mysqli_fetch_array($res)) {
			
				extract($row);
				
				$PubMonthName = preg_replace("/January/", "Januari", $PubMonthName);
				$PubMonthName = preg_replace("/February/", "Februari", $PubMonthName);
				$PubMonthName = preg_replace("/March/", "Mars", $PubMonthName);
				$PubMonthName = preg_replace("/May/", "Maj", $PubMonthName);
				$PubMonthName = preg_replace("/June/", "Juni", $PubMonthName);
				$PubMonthName = preg_replace("/July/", "Juli", $PubMonthName);
				$PubMonthName = preg_replace("/August/", "Augusti", $PubMonthName);
				$PubMonthName = preg_replace("/October/", "Oktober", $PubMonthName);
				
				echo "\t\t<option value=\"$PubMonth\"";
				
				if ($PubMonth == $month2) {
					echo " selected";
				}
				
				echo ">" . $PubMonthYear . " " . $PubMonthName . "&nbsp;&nbsp;</option> \n";
			
			}

			echo "</select>\n";
			
		}

	}	

	function deleteBlogg($ID) {

		$conn_my = Db::getConnection(true);

		$updt = "UPDATE blog SET offentlig = '0' WHERE cnt = '$ID'";
		// echo $updt;
		// exit;

		$res = mysqli_query($conn_my, $updt);

	}

}
?>

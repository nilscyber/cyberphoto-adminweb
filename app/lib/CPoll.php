<?php


Class CPoll {

	var $conn_my;

	function __construct() {

		$this->conn_my = Db::getConnectionDb('cyberadmin');

	}

	function checkIfPoll($site) {
		
	$select  = "SELECT poll_id FROM poll ";
	$select .= "WHERE  poll_from < now() AND poll_to > now() AND poll_site = '" . $site . "' ";
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			extract(mysqli_fetch_array($res));

				return $poll_id;
		
		} else {
		
				return 0;
		
		}

	}	

	function displayPoll($pollID) {

		$question = $this->displayQ($pollID);
		echo "<table class=\"ptable\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"177\" height=\"230\">\n";
		echo "\t<tr>\n";
		// echo "<td valign=\"top\">sdf</td>";
		echo "\t\t<td class=\"pquestion\" colspan=\"2\" height=\"40\" align=\"center\">$question</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\" height=\"3\"><img border=\"0\" src=\"1pix.gif\" height=\"3\"></td>\n";
		echo "\t</tr>\n";
		echo "\t<form>\n";
		// echo "\t<input type=\"hidden\" name=\"pollid\" value=\"yes\">\n";
		/*
		echo "<tr>";
		echo "<td width=\"25\"><input type=\"radio\" name=\"vote\" value=\"0\" onclick=\"getVote(this.value)\"></td>";
		echo "<td>Här kommer alternativen</td>";
		echo "</tr>";
		*/
		$this->displayAlt($pollID);
		echo "\t</form>\n";
		echo "\t<tr>\n";
		echo "\t\t<td class=\"plink\" valign=\"bottom\" colspan=\"2\" align=\"center\">&nbsp;</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\" height=\"3\"><img border=\"0\" src=\"1pix.gif\" height=\"3\"></td>\n";
		echo "\t</tr>\n";
		echo "\t</table>\n";

	}

	function displayPollLong($pollID) {

		$question = $this->displayQ($pollID);
		// echo "<table class=\"ptable\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"177\" height=\"230\">\n";
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"800\" height=\"230\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\" height=\"10\"><img border=\"0\" src=\"1pix.gif\" height=\"10\"></td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		// echo "<td valign=\"top\">sdf</td>";
		echo "\t\t<td class=\"pquestion\" colspan=\"2\" height=\"40\" align=\"center\">$question</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\" height=\"3\"><img border=\"0\" src=\"1pix.gif\" height=\"3\"></td>\n";
		echo "\t</tr>\n";
		echo "\t<form>\n";
		// echo "\t<input type=\"hidden\" name=\"pollid\" value=\"yes\">\n";
		/*
		echo "<tr>";
		echo "<td width=\"25\"><input type=\"radio\" name=\"vote\" value=\"0\" onclick=\"getVote(this.value)\"></td>";
		echo "<td>Här kommer alternativen</td>";
		echo "</tr>";
		*/
		$this->displayAltLong($pollID);
		echo "\t</form>\n";
		echo "\t<tr>\n";
		echo "\t\t<td class=\"plink\" valign=\"bottom\" colspan=\"2\" align=\"center\">&nbsp;</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\" height=\"3\"><img border=\"0\" src=\"1pix.gif\" height=\"3\"></td>\n";
		echo "\t</tr>\n";
		echo "\t</table>\n";

	}

	function displayQ($pollID) {
		
	$select  = "SELECT poll_q FROM poll ";
	$select .= "WHERE  poll_id = '" . $pollID . "' ";
	$res = mysqli_query($this->conn_my, $select);

	// echo $select;

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			return $poll_q;
			
			endwhile;
		
		} else {
		
		return "";
		
		}

	}	

	function displayAlt($pollID) {
		
	$select  = "SELECT poll_alt_id, poll_alt FROM poll_alt ";
	$select .= "WHERE  poll = '" . $pollID . "' ";
	$res = mysqli_query($this->conn_my, $select);

	// echo $select;

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			echo "\t<tr>\n";
			echo "\t\t<td width=\"22\" height=\"25\"><input type=\"radio\" name=\"vote\" value=\"$poll_alt_id\" onclick=\"getVote(this.value,$pollID);setCookie('webpoll','$pollID',365)\"></td>";
			echo "\t\t<td width=\"155\" class=\"palternativ\" height=\"25\" align=\"left\">$poll_alt</td>\n";
			echo "\t</tr>\n";
			
			endwhile;
		
		} else {
		
		return "";
		
		}

	}	

	function displayAltLong($pollID) {
		
	$select  = "SELECT poll_alt_id, poll_alt FROM poll_alt ";
	$select .= "WHERE  poll = '" . $pollID . "' ";
	$res = mysqli_query($this->conn_my, $select);

	// echo $select;

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			echo "\t<tr>\n";
			echo "\t\t<td width=\"15\" height=\"25\"><input type=\"radio\" name=\"vote\" value=\"$poll_alt_id\" onclick=\"getVote(this.value,$pollID);setCookie('webpoll','$pollID',365)\"></td>";
			echo "\t\t<td width=\"770\" class=\"palternativ\" height=\"25\" align=\"left\">$poll_alt</td>\n";
			echo "\t</tr>\n";
			
			endwhile;
		
		} else {
		
		return "";
		
		}

	}	

	function isValidDateTime($dateTime) {

		if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)) {
			if (checkdate($matches[2], $matches[3], $matches[1])) {
				return true;
			}
		}

		return false;
	}

	function addPoll($vote,$o,$ip) {

		$insrt = "INSERT INTO poll_answer (poll,poll_answer_ip,poll_answer) VALUES ('$o','$ip','$vote') ";

		mysqli_query($this->conn_my, $insrt);

	}

	function getTotalVotes($pollID) {
		
	$select  = "SELECT COUNT(poll_answer_id) AS Antal FROM poll_answer ";
	$select .= "WHERE  poll = '" . $pollID . "' ";
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):

			extract($row);
			return $Antal;
			
			endwhile;
		
		} else {
		
			return 0;
		
		}

	}	

	function displayResultTable($pollID,$totalpoll) {

		$question = $this->displayQ($pollID);
		$friseradtotal = number_format($totalpoll, 0, ',', ' ');
		echo "<table class=\"ptable\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"177\" height=\"230\">\n";
		echo "\t<tr>\n";
		// echo "<td valign=\"top\">sdf</td>";
		echo "\t\t<td class=\"pquestion\" colspan=\"2\" height=\"40\" align=\"center\">$question</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\" height=\"3\"><img border=\"0\" src=\"1pix.gif\" height=\"3\"></td>\n";
		echo "\t</tr>\n";
		$this->displayAltResult($pollID,$totalpoll);
		echo "\t<tr>\n";
		echo "\t\t<td class=\"votesnr\" valign=\"bottom\" colspan=\"2\" align=\"center\">$friseradtotal st har röstat</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\" height=\"3\"><img border=\"0\" src=\"1pix.gif\" height=\"3\"></td>\n";
		echo "\t</tr>\n";
		echo "\t</table>\n";

	}

	function displayResultTableLong($pollID,$totalpoll) {

		$question = $this->displayQ($pollID);
		$friseradtotal = number_format($totalpoll, 0, ',', ' ');
		// echo "<table class=\"ptable\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"177\" height=\"230\">\n";
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"800\" height=\"230\">\n";
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\" height=\"10\"><img border=\"0\" src=\"1pix.gif\" height=\"10\"></td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		// echo "<td valign=\"top\">sdf</td>";
		echo "\t\t<td class=\"pquestion\" colspan=\"2\" height=\"40\" align=\"center\">$question</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\" height=\"3\"><img border=\"0\" src=\"1pix.gif\" height=\"3\"></td>\n";
		echo "\t</tr>\n";
		$this->displayAltResultLong($pollID,$totalpoll);
		echo "\t<tr>\n";
		echo "\t\t<td class=\"votesnr\" valign=\"bottom\" colspan=\"2\" align=\"center\">$friseradtotal st har röstat</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td colspan=\"2\" height=\"3\"><img border=\"0\" src=\"1pix.gif\" height=\"3\"></td>\n";
		echo "\t</tr>\n";
		echo "\t</table>\n";

	}

	function displayAltResult($pollID,$totalpoll) {
		
	$select  = "SELECT poll_alt_id, poll_alt FROM poll_alt ";
	$select .= "WHERE  poll = '" . $pollID . "' ";
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			$totalVotes = $this->getTotalVotesPerAlt($pollID,$poll_alt_id);
			$procentvote = (100*round($totalVotes/$totalpoll,2));
			
			echo "\t<tr>\n";
			echo "\t\t<td class=\"palternativ\" width=\"22\" height=\"25\">&nbsp;$poll_alt</td>";
			echo "<td width=\"155\" class=\"palternativ\" height=\"25\"><img border=\"0\" src=\"/pic/voteline.png\" width=\"$procentvote%\" height=\"10\"> $procentvote%</td>\n";
			echo "\t</tr>\n";
			
			endwhile;
		
		} else {
		
		return "";
		
		}

	}	

	function displayAltResultLong($pollID,$totalpoll) {
		
	$select  = "SELECT poll_alt_id, poll_alt FROM poll_alt ";
	$select .= "WHERE  poll = '" . $pollID . "' ";
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);
			
			$totalVotes = $this->getTotalVotesPerAlt($pollID,$poll_alt_id);
			$procentvote = (100*round($totalVotes/$totalpoll,2));
			
			echo "\t<tr>\n";
			echo "\t\t<td class=\"palternativ\" width=\"75\" height=\"25\">&nbsp;$poll_alt</td>";
			echo "<td width=\"720\" class=\"palternativ\" height=\"25\"><img border=\"0\" src=\"/pic/voteline.png\" width=\"$procentvote%\" height=\"10\"> $procentvote%</td>\n";
			echo "\t</tr>\n";
			
			endwhile;
		
		} else {
		
		return "";
		
		}

	}	

	function getTotalVotesPerAlt($pollID,$poll_alt_id) {
		
	$select  = "SELECT COUNT(poll_answer_id) AS Antal FROM poll_answer ";
	$select .= "WHERE  poll = '" . $pollID . "' AND poll_answer = '" . $poll_alt_id . "' ";
	$res = mysqli_query($this->conn_my, $select);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):

			extract($row);
			return $Antal;
			
			endwhile;
		
		} else {
		
			return 0;
		
		}

	}	
	// ****************************** NEDAN ÄR FÖR ADMINISTRATION ********************************* //

	function getDaysLeft($dateto) {

		$now = time();
		$timeto = strtotime($dateto);
		$diff = $timeto - $now;
		$sek = $diff % 60;
		$min = ($diff / 60) % 60;
		$hour = ($diff / 3600);
		$days = ($diff / 86400);
		$days = floor($days);
		$days = round($days, 0);
		return $days;
	}

	function pollListCurrent() {
		
	$select  = "SELECT * FROM poll ";
	$select .= "WHERE  poll_from < now() AND poll_to > now() ";
	$res = mysqli_query($this->conn_my, $select);

	// echo $select;
		$desiderow = true;
		echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
		echo "<tr>";
		echo "<td width=\"300\"><b>Fråga</b></td>";
		echo "<td width=\"150\" align=\"center\"><b>Gäller från</b></td>";
		echo "<td width=\"150\" align=\"center\"><b>Gäller till</b></td>";
		echo "<td width=\"100\" align=\"center\"><b>Återstår</b></td>";
		echo "<td width=\"100\" align=\"center\"><b>Sida</b></td>";
		echo "<td width=\"25\"><b>&nbsp;</b></td>";

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

			if ($desiderow == true) {
				$rowcolor = "firstrow";
			} else {
				$rowcolor = "secondrow";
			}
			
			if ($poll_site == 0) {
				$poll_site = "Foto & Video";
			} elseif ($poll_site == 1) {
				$poll_site = "Mobil & Data";
			} elseif ($poll_site == 2) {
				$poll_site = "Professional";
			} elseif ($poll_site == 3) {
				$poll_site = "Hobby & Fiske";
			}
			$aterstar = $this->getDaysLeft($poll_to);
			
			echo "<tr>";
			echo "<td class=\"$rowcolor\">$poll_q</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$poll_from</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$poll_to</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$aterstar dagar</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$poll_site</td>";
			echo "<td align=\"center\"><img border=\"0\" src=\"status_green.gif\"></td>";
			echo "</tr>";

			if ($desiderow == true) {
				$desiderow = false;
			} else {
				$desiderow = true;
			}
			
			endwhile;
		
		} else {
		
			echo "<tr>";
			echo "<td colspan=\"6\" class=\"$rowcolor\"><b>Ingen undersökning ligger online just nu</b></td>";
			echo "</tr>";
		
		}

		echo "</table>";

	}

	function pollListUpcomming() {
		
	$select  = "SELECT * FROM poll ";
	$select .= "WHERE  poll_from > now() ";
	$res = mysqli_query($this->conn_my, $select);

	// echo $select;
		$desiderow = true;
		echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
		echo "<tr>";
		echo "<td width=\"300\"><b>Fråga</b></td>";
		echo "<td width=\"150\" align=\"center\"><b>Gäller från</b></td>";
		echo "<td width=\"150\" align=\"center\"><b>Gäller till</b></td>";
		echo "<td width=\"100\" align=\"center\"><b>Återstår</b></td>";
		echo "<td width=\"100\" align=\"center\"><b>Sida</b></td>";
		echo "<td width=\"25\"><b>&nbsp;</b></td>";

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

			if ($desiderow == true) {
				$rowcolor = "firstrow";
			} else {
				$rowcolor = "secondrow";
			}
			
			if ($poll_site == 0) {
				$poll_site = "Foto";
			} elseif ($poll_site == 1) {
				$poll_site = "Mobil & Data";
			} elseif ($poll_site == 2) {
				$poll_site = "Professional";
			} elseif ($poll_site == 3) {
				$poll_site = "Hobby & Fiske";
			}
			$aterstar = $this->getDaysLeft($poll_from);
			
			echo "<tr>";
			echo "<td class=\"$rowcolor\">$poll_q</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$poll_from</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$poll_to</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$aterstar dagar</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$poll_site</td>";
			echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>";
			echo "</tr>";
			
			if ($desiderow == true) {
				$desiderow = false;
			} else {
				$desiderow = true;
			}

			endwhile;
		
		} else {
		
			$rowcolor = "firstrow";
			echo "<tr>";
			echo "<td colspan=\"5\" class=\"$rowcolor\"><b>Ingen undersökning är planerad</b></td>";
			echo "<td><b>&nbsp;</b></td>";
			echo "</tr>";
		
		}

		echo "</table>";

	}

	function pollListHistory() {
		
	$select  = "SELECT * FROM poll ";
	$select .= "WHERE  poll_to < now() ";
	$res = mysqli_query($this->conn_my, $select);

	// echo $select;
		$desiderow = true;
		echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
		echo "<tr>";
		echo "<td width=\"300\"><b>Fråga</b></td>";
		echo "<td width=\"150\" align=\"center\"><b>Gällde från</b></td>";
		echo "<td width=\"150\" align=\"center\"><b>Gällde till</b></td>";
		echo "<td width=\"100\" align=\"center\"><b>Gällde</b></td>";
		echo "<td width=\"100\" align=\"center\"><b>Sida</b></td>";
		echo "<td width=\"25\"><b>&nbsp;</b></td>";

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

			if ($desiderow == true) {
				$rowcolor = "firstrow";
			} else {
				$rowcolor = "secondrow";
			}
			
			if ($poll_site == 0) {
				$poll_site = "Foto";
			} elseif ($poll_site == 1) {
				$poll_site = "Mobil & Data";
			} elseif ($poll_site == 2) {
				$poll_site = "Professional";
			} elseif ($poll_site == 3) {
				$poll_site = "Hobby & Fiske";
			}
			$aterstar = $this->getDaysLeft($poll_from);
			
			echo "<tr>";
			echo "<td class=\"$rowcolor\">$poll_q</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$poll_from</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$poll_to</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$aterstar dagar</td>";
			echo "<td class=\"$rowcolor\" align=\"center\">$poll_site</td>";
			echo "<td align=\"center\"><img border=\"0\" src=\"status_red.gif\"></td>";
			echo "</tr>";
			
			if ($desiderow == true) {
				$desiderow = false;
			} else {
				$desiderow = true;
			}

			endwhile;
		
		} else {
		
			$rowcolor = "firstrow";
			echo "<tr>";
			echo "<td colspan=\"5\" class=\"$rowcolor\"><b>Ingen tidigare undersökning finns i systemet</b></td>";
			echo "<td><b>&nbsp;</b></td>";
			echo "</tr>";
		
		}

		echo "</table>";

	}

}

?>

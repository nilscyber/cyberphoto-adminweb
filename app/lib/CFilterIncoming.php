<?php

Class CFilterIncoming {

	function getWordsToCheck() {
		
		$select  = "SELECT ci.checkWord, ci.checkID ";
		$select .= "FROM cyberadmin.checkincoming ci ";
		$select .= "WHERE ci.checkActive = -1 ";
		
		// echo $select;

		$res = mysqli_query(Db::getConnection(), $select);
		// $counts = mysqli_num_rows($res);
		
		while ($row = mysqli_fetch_object($res)) {
		
			$this->getOrdersThatFits($row->checkWord,$row->checkID);
				
		}

	}

	function getOrdersThatFits($words,$checkID) {
		
		$words = trim($words);
		$searchwords = preg_split("/[\s]+/", $words);
		
		$select  = "SELECT o.ordernr, o.order_url ";
		$select .= "FROM cyberphoto.Ordertabell o ";
		$select .= "WHERE o.inkommet > NOW() - INTERVAL 15 MINUTE ";
		$select .= "AND (";
		for ($i = 0; $i < count($searchwords);$i++) {

			if ($i == 0) {
				$select .= "o.lnamn like '%" . $searchwords[$i] . "%' OR o.lco like '%" . $searchwords[$i] . "%' OR o.ladress like '%" . $searchwords[$i] . "%' OR o.lpostadr like '%" . $searchwords[$i] . "%' OR o.namn like '%" . $searchwords[$i] . "%' OR o.co like '%" . $searchwords[$i] . "%' OR o.adress like '%" . $searchwords[$i] . "%' OR o.postadress like '%" . $searchwords[$i] . "%' OR o.email like '%" . $searchwords[$i] . "%' OR o.telefon like '%" . $searchwords[$i] . "%') ";
			} else {
				$select .= "AND (o.lnamn like '%" . $searchwords[$i] . "%' OR o.lco like '%" . $searchwords[$i] . "%' OR o.ladress like '%" . $searchwords[$i] . "%' OR o.lpostadr like '%" . $searchwords[$i] . "%' OR o.namn like '%" . $searchwords[$i] . "%' OR o.co like '%" . $searchwords[$i] . "%' OR o.adress like '%" . $searchwords[$i] . "%' OR o.postadress like '%" . $searchwords[$i] . "%' OR o.email like '%" . $searchwords[$i] . "%' OR o.telefon like '%" . $searchwords[$i] . "%') ";
			}
		
		}
		
		// echo $select;

		$res = mysqli_query(Db::getConnection(), $select);
		// $counts = mysqli_num_rows($res);
		
		if (mysqli_num_rows($res) > 0) {
			
			while ($row = mysqli_fetch_object($res)) {
			
				// echo $row->ordernr . "<br>";
				$this->sendMailForManuelCheck($row->ordernr,$words,$row->order_url);
				$this->doFilterCount($checkID);
					
			}
		
		}

	}

	function doFilterCount($checkID) {

		$aktuelltdatum = date("Y-m-d H:i:s");

		$updt  = "UPDATE cyberadmin.checkincoming ";
		$updt .= "SET ";
		$updt .= "checkCounter = checkCounter + 1, ";
		$updt .= "checkCounterTime = '$aktuelltdatum' ";
		$updt .= "WHERE checkID = '$checkID' ";
		
		$res = mysqli_query(Db::getConnection(true), $updt);


	}

	function sendMailForManuelCheck($ordernr,$words,$url) {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply@cyberphoto.se";

		// $recipient .= " stefan";
		$recipient .= " po@cyberphoto.se";
		$recipient .= " urgent_ticket@cyberphoto.se";
		
		$subj = $orderdatum . " Order som MÅSTE kontrolleras är upplagd!";

		$extra = "From: " . $addcreatedby;
		
		$text1  = "Denna order har fastnat i vårt inkommenade filterkontroll och måste kontrolleras manuellt.\n";
		$text1 .= "KAN vara så att en bedragare bakom denna order. Skall dock hanteras med stor respekt.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		$text1 .= "Filter som utlöste aviseringen: " . $words . "\n\n";
		$text1 .= "https://www2.cyberphoto.se/kundvagn/min-orderstatus?orderref=" . $url . "&order_check=" . $ordernr . "\n\n";
		$text1 .= "Mer information kan du hitta på sidan för filterhanteringen\n";
		$text1 .= "http://admin.cyberphoto.se/check_incoming.php\n\n";
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}
	
	function getActualFilters($deactivated = false) {

		$rowcolor = true;
		$startcount = 0;
		
		echo "<table cellpadding=\"2\" cellspacing=\"1\" width=\"100%\">";
		echo "<tr>";
		echo "<td width=\"200\" align=\"left\"><b>Filter</b></td>";
		echo "<td width=\"50\" align=\"center\"><b>Triggat</b></td>";
		echo "<td><b>Notering</b></td>";
		echo "<td width=\"200\" align=\"left\"><b>Upplagd av</b></td>";
		echo "<td width=\"90\" align=\"center\"><b>Datum</b></td>";
		echo "<td width=\"65\" align=\"center\"><b>&nbsp;</b></td>";
		echo "</tr>";

		$select  = "SELECT ci.* ";
		$select .= "FROM cyberadmin.checkincoming ci ";
		if ($deactivated) {
			$select .= "WHERE ci.checkActive = 0 ";
		} else {
			$select .= "WHERE ci.checkActive = -1 ";
		}

		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_object($res)) {
			
					if ($rowcolor == true) {
						$backcolor = "firstrow";
					} else {
						$backcolor = "secondrow";
					}

					echo "<tr>";
					echo "<td class=\"$backcolor\">" . $row->checkWord . "</td>";
					echo "<td class=\"$backcolor\" align=\"center\">" . $row->checkCounter . "</td>";
					echo "<td class=\"$backcolor\" align=\"left\">" . $row->checkNote . "</td>";
					echo "<td class=\"$backcolor\" align=\"left\">" . $row->checkBy . "</td>";
					echo "<td class=\"$backcolor\" align=\"center\">" . date("Y-m-d", strtotime($row->checkTime)) . "</td>";
					if ($deactivated) {
						echo "<td align=\"center\">&nbsp;</td>";
					} else {
						echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $row->checkID . "\">ändra</a></td>";
					}
					echo "</tr>";

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
					$startcount++;
			
				}
				
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"4\"><font color=\"#000000\"><b>Inga träffar</b></td>";
				echo "</tr>";
			
			}
			
		echo "<tr>";
		echo "<td colspan=\"4\"><b>Totalt: $startcount st</b></td>";
		echo "</tr>";
		echo "</table>";

	}

	function getFilterRow($ID) {

		$select  = "SELECT ci.* ";
		$select .= "FROM cyberadmin.checkincoming ci ";
		$select .= "WHERE checkID = '" . $ID . "' ";
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		return $rows;

	}

	function doFilterAdd($addWord,$addRecipient,$addComment) {

		$blackByIP = $_SERVER['REMOTE_ADDR'];
		$aktuelltdatum = date("Y-m-d H:i:s");

		$updt  = "INSERT INTO cyberadmin.checkincoming ";
		$updt .= "(checkWord,checkBy,checkTime,checkNote) ";
		$updt .= "VALUES ";
		$updt .= "('$addWord','$addRecipient','$aktuelltdatum','$addComment') ";

		$res = mysqli_query(Db::getConnection(true), $updt);

	}

	function doFilterChange($addID,$addWord,$addcreatedby,$addActive,$addComment) {

		$blackByIP = $_SERVER['REMOTE_ADDR'];
		$aktuelltdatum = date("Y-m-d H:i:s");

		$updt  = "UPDATE cyberadmin.checkincoming ";
		$updt .= "SET ";
		$updt .= "checkWord = '$addWord', ";
		if ($addActive == 0) {
			$updt .= "checkActive = '0', ";
			$updt .= "checkDeactivateBy = '$addcreatedby', ";
			$updt .= "checkDeactivateTime = '$aktuelltdatum', ";
		}
		if ($addComment != "") {
			$updt .= "checkNote = '$addComment' ";
		} else {
			$updt .= "checkNote = NULL ";
		}
		$updt .= "WHERE checkActive = -1 AND checkID = '$addID'";
		
		// echo $updt;

		$res = mysqli_query(Db::getConnection(true), $updt);

	}
	
	function getIncommingComment($page) {

		$rowcolor = true;
		$startcount = 0;
		
		echo "<table cellpadding=\"2\" cellspacing=\"1\" width=\"1200\">";
		echo "<tr>";
		echo "<td width=\"150\" align=\"center\"><b>Datum</b></td>";
		echo "<td align=\"left\"><b>Logg</b></td>";
		echo "</tr>";

		$select  = "SELECT logDate, logComment ";
		$select .= "FROM cyberphoto.logWeb ";
		// $select .= "WHERE logPage = '/kundvagn/placeOrder.php' ";
		$select .= "WHERE logPage = '" . $page . "' ";
		$select .= "AND logDate > DATE_SUB(now(), INTERVAL 5 day) ";
		$select .= "ORDER BY logDate DESC ";

		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_object($res)) {
			
					if ($rowcolor == true) {
						$backcolor = "firstrow";
					} else {
						$backcolor = "secondrow";
					}

					echo "<tr>";
					echo "<td class=\"$backcolor\" align=\"center\">" . date("Y-m-d H:i:s", strtotime($row->logDate)) . "</td>";
					echo "<td class=\"$backcolor\" align=\"left\">" . $row->logComment . "</td>";
					echo "</tr>";

					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}
					$startcount++;
			
				}
				
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"4\"><font color=\"#000000\"><b>Inga träffar</b></td>";
				echo "</tr>";
			
			}
			
		echo "<tr>";
		echo "<td colspan=\"4\"><b>Totalt: $startcount st</b></td>";
		echo "</tr>";
		echo "</table>";

	}

}
?>
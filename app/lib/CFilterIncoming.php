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

		$conn_ad = Db::getConnectionAD();

		$select  = "SELECT DISTINCT o.documentno, o.order_url ";
		$select .= "FROM c_order o ";
		$select .= "JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id ";
		$select .= "JOIN c_bpartner bp2 ON bp2.c_bpartner_id = o.bill_bpartner_id ";
		$select .= "JOIN c_bpartner_location bpl ON bpl.c_bpartner_location_id = o.c_bpartner_location_id ";
		$select .= "JOIN c_location loc ON loc.c_location_id = bpl.c_location_id ";
		$select .= "JOIN c_bpartner_location bpl2 ON bpl2.c_bpartner_location_id = o.bill_location_id ";
		$select .= "JOIN c_location loc2 ON loc2.c_location_id = bpl2.c_location_id ";
		$select .= "LEFT JOIN ad_user ad2 ON ad2.c_bpartner_id = o.c_bpartner_id ";
		$select .= "WHERE o.created > NOW() - INTERVAL '15 minutes' ";
		$select .= "AND (";

		for ($i = 0; $i < count($searchwords); $i++) {
			$w = pg_escape_string($conn_ad, $searchwords[$i]);
			$cond  = "bp.name ILIKE '%$w%' OR bp.name2 ILIKE '%$w%' ";
			$cond .= "OR loc.address1 ILIKE '%$w%' OR loc.address2 ILIKE '%$w%' OR loc.city ILIKE '%$w%' ";
			$cond .= "OR bp2.name ILIKE '%$w%' OR bp2.name2 ILIKE '%$w%' ";
			$cond .= "OR loc2.address1 ILIKE '%$w%' OR loc2.address2 ILIKE '%$w%' OR loc2.city ILIKE '%$w%' ";
			$cond .= "OR ad2.email ILIKE '%$w%' OR ad2.phone2 ILIKE '%$w%' ";
			$cond .= "OR bp.taxid ILIKE '%$w%' OR bp2.taxid ILIKE '%$w%'";
			if ($i == 0) {
				$select .= "($cond) ";
			} else {
				$select .= "AND ($cond) ";
			}
		}

		$select .= ")";

		// echo $select;

		$res = $conn_ad ? @pg_query($conn_ad, $select) : false;

		if ($res && pg_num_rows($res) > 0) {

			while ($row = pg_fetch_object($res)) {

				// echo $row->documentno . "<br>";
				$this->sendMailForManuelCheck($row->documentno, $words, $row->order_url);
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

		$recipient  = " stefan@cyberphoto.se";
		// $recipient .= " urgent_ticket@cyberphoto.se";
		
		$subj = $orderdatum . " Order som MÅSTE kontrolleras är upplagd!";

		$extra = "From: " . $addcreatedby;
		
		$text1  = "Denna order har fastnat i vår inkommande filterkontroll och måste kontrolleras manuellt.\n";
		$text1 .= "KAN vara så att en bedragare bakom denna order. Skall dock hanteras med stor respekt.\n\n";
		$text1 .= "Order nr: " . $ordernr . "\n\n";
		$text1 .= "Filter som utlöste aviseringen: " . $words . "\n\n";
		$text1 .= "https://admin.cyberphoto.se/search_dispatch.php?mode=order&page=1&q=" . $ordernr . "\n\n";
		$text1 .= "Mer information kan du hitta på sidan för filterhanteringen\n";
		$text1 .= "http://admin.cyberphoto.se/check_incoming.php\n\n";
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}
	
	function getActualFilters($deactivated = false) {

		$startcount = 0;

		$select  = "SELECT ci.* ";
		$select .= "FROM cyberadmin.checkincoming ci ";
		if ($deactivated) {
			$select .= "WHERE ci.checkActive = 0 ";
		} else {
			$select .= "WHERE ci.checkActive = -1 ";
		}

		$res = mysqli_query(Db::getConnection(), $select);

		echo "<table class=\"table-list\">\n";
		echo "<thead><tr>";
		echo "<th>Filter</th>";
		echo "<th style=\"width:70px;text-align:center;\">Triggat</th>";
		echo "<th>Notering</th>";
		echo "<th style=\"width:200px;\">Upplagd av</th>";
		echo "<th style=\"width:100px;text-align:center;\">Datum</th>";
		if (!$deactivated) {
			echo "<th style=\"width:70px;\"></th>";
		}
		echo "</tr></thead>\n";
		echo "<tbody>\n";

		if (mysqli_num_rows($res) > 0) {

			while ($row = mysqli_fetch_object($res)) {

				echo "<tr>";
				echo "<td>" . htmlspecialchars($row->checkWord) . "</td>";
				echo "<td style=\"text-align:center;\">" . (int)$row->checkCounter . "</td>";
				echo "<td>" . htmlspecialchars($row->checkNote) . "</td>";
				echo "<td>" . htmlspecialchars($row->checkBy) . "</td>";
				echo "<td style=\"text-align:center;\">" . date("Y-m-d", strtotime($row->checkTime)) . "</td>";
				if (!$deactivated) {
					echo "<td><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . (int)$row->checkID . "\" style=\"color:#0d9488;text-decoration:none;font-weight:600;\">ändra</a></td>";
				}
				echo "</tr>\n";

				$startcount++;

			}

		} else {

			$colspan = $deactivated ? 5 : 6;
			echo "<tr><td colspan=\"$colspan\" style=\"color:#6b7280;font-style:italic;\">Inga träffar</td></tr>\n";

		}

		echo "</tbody>\n";
		echo "<tfoot><tr><td colspan=\"" . ($deactivated ? 5 : 6) . "\" style=\"font-weight:600;\">Totalt: $startcount st</td></tr></tfoot>\n";
		echo "</table>\n";

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
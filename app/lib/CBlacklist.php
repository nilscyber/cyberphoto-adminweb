<?php

Class CBlacklist {
	var $conn_my;

	function __construct() {
			
		$this->conn_my = Db::getConnection();
		// $this->conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($this->conn_my, getenv('DB_NAME') ?: 'cyberphoto');
		
	}

	function checkIPnumber($IP) {
		
		$select = "SELECT blackIP, blackNote, blackBy ";
		$select .= "FROM blacklist ";
		$select .= "WHERE blackActive = 1 AND blackIP = '" . $IP . "' ";
		$res = mysqli_query($this->conn_my, $select);
		// $row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_array($res)):
		
			extract($row);

			$this->sendMessBlacklist_v1($blackIP,$blackNote,$blackBy);

			endwhile;
		
		} else {
		
			return;
		
		}

	}	

	function sendMessBlacklist_v1($ip,$note,$by) {
		global $newordernr,$old_lnamn;

		$senddate = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		$recipient .= " kundtjanst";
		// $recipient .= " sjabo";
		
		$subj = $senddate . " Svartlistad IP-adress!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "Vänligen kontrollera denna order i affärssystemet.\n\n";
		$text1 .= "Order nr: " . $newordernr . "\n\n";
		$text1 .= "Köpare: " . $old_lnamn . "\n\n";
		$text1 .= "IP-adress: " . $ip . "\n\n";
		$text1 .= "Bavakning upplagd av: " . $by . "\n\n";
		$text1 .= "Eventuell notering: " . $note . "\n\n";
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}
	
	// ************************************************************************

	function getActualBlacklist() {

		$rowcolor = true;
		$startcount = 0;
		
		echo "<table width=\"1200\">";
		echo "<tr>";
		echo "<td width=\"100\"><b>IP nummer</b></td>";
		echo "<td><b>Notering</b></td>";
		echo "<td width=\"110\" align=\"center\"><b>Upplagd av</b></td>";
		echo "<td width=\"65\" align=\"center\"><b>&nbsp;</b></td>";
		echo "</tr>";

		$select  = "SELECT * ";
		$select .= "FROM blacklist ";
		$select .= "WHERE blackActive = 1 ";
		// echo $select;
		// exit;

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)):
			
				extract($row);

				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}
				if (strlen($beskrivning) > 45) {
					$beskrivning = substr ($beskrivning, 0, 45) . "....";
				}
				if ($bestallningsgrans == 0 && $utgangen == -1 && $lagersaldo == 0) {
					$beskrivning .= "<font color=\"#FF0000\"><b><i>*** Felaktig</i></b></font>";
				}
				$monDays = round((time()-strtotime($monTime))/3600/24);


				echo "<tr>";
				echo "<td class=\"$backcolor\">$blackIP</td>";
				echo "<td class=\"$backcolor\" align=\"left\">$blackNote</td>";
				echo "<td class=\"$backcolor\" align=\"center\">$blackBy</td>";
				echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $blackID . "\">ändra</a></td>";
				echo "</tr>";

				if ($rowcolor == true) {
					$row = true;
					$rowcolor = false;
				} else {
					$row = false;
					$rowcolor = true;
				}
				$startcount++;
			
				endwhile;
				
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"4\"><font color=\"#000000\"><b>Inga IP-adresser bevakas just nu</b></td>";
				echo "</tr>";
			
			}
			
		echo "<tr>";
		echo "<td colspan=\"4\"><b>Totalt: $startcount st</b></td>";
		echo "</tr>";
		echo "</table>";

	}

	function getNotActualBlacklist() {

		$rowcolor = true;
		$startcount = 0;
		
		echo "<table width=\"1200\">";
		echo "<tr>";
		echo "<td width=\"100\"><b>IP nummer</b></td>";
		echo "<td><b>Notering</b></td>";
		echo "<td width=\"110\" align=\"center\"><b>Upplagd av</b></td>";
		echo "<td width=\"65\" align=\"center\"><b>&nbsp;</b></td>";
		echo "</tr>";

		$select  = "SELECT * ";
		$select .= "FROM blacklist ";
		$select .= "WHERE blackActive = 0 ";
		// echo $select;
		// exit;

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {

				while ($row = mysqli_fetch_array($res)):
			
				extract($row);

				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}
				if (strlen($beskrivning) > 45) {
					$beskrivning = substr ($beskrivning, 0, 45) . "....";
				}
				if ($bestallningsgrans == 0 && $utgangen == -1 && $lagersaldo == 0) {
					$beskrivning .= "<font color=\"#FF0000\"><b><i>*** Felaktig</i></b></font>";
				}
				$monDays = round((time()-strtotime($monTime))/3600/24);


				echo "<tr>";
				echo "<td class=\"$backcolor\">$blackIP</td>";
				echo "<td class=\"$backcolor\" align=\"left\">$blackNote</td>";
				echo "<td class=\"$backcolor\" align=\"center\">$blackBy</td>";
				echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $blackID . "\">ändra</a></td>";
				echo "</tr>";

				if ($rowcolor == true) {
					$row = true;
					$rowcolor = false;
				} else {
					$row = false;
					$rowcolor = true;
				}
				$startcount++;
			
				endwhile;
				
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"4\"><font color=\"#000000\"><b>Inga IP-adresser har bevakats</b></td>";
				echo "</tr>";
			
			}
			
		echo "<tr>";
		echo "<td colspan=\"4\"><b>Totalt: $startcount st</b></td>";
		echo "</tr>";
		echo "</table>";

	}
	
	function checkIfDuplicateIPnumber($IP) {
		
		$select = "SELECT blackIP ";
		$select .= "FROM blacklist ";
		$select .= "WHERE blackActive = 1 AND blackIP = '" . $IP . "' ";
		$res = mysqli_query($this->conn_my, $select);
		// $row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
		
			return true;
		
		} else {
		
			return false;
		
		}

	}	
	
	function getBlacklistRow($ID) {

	$select  = "SELECT * FROM blacklist WHERE blackID = '" . $ID . "' ";

	$res = mysqli_query($this->conn_my, $select);

	$rows = mysqli_fetch_object($res);

	return $rows;

	}

	function doBlacklistAdd($addIP,$addRecipient,$addComment) {

		$conn_my2 = Db::getConnection(true);
		
		$blackByIP = $_SERVER['REMOTE_ADDR'];
		$aktuelltdatum = date("Y-m-d H:i:s");

		$updt = "INSERT INTO blacklist (blackIP,blackTime,blackBy,blackNote,blackByIP) VALUES ('$addIP','$aktuelltdatum','$addRecipient','$addComment','$blackByIP')";

		$res = mysqli_query($conn_my2, $updt);

	}

	function doBlacklistChange($addID,$addIP,$addRecipient,$addActive,$addComment) {

		$conn_my2 = Db::getConnection(true);
		
		$blackByIP = $_SERVER['REMOTE_ADDR'];
		$aktuelltdatum = date("Y-m-d H:i:s");

		$updt = "UPDATE blacklist SET blackIP = '$addIP', blackBy = '$addRecipient', blackActive = '$addActive', blackNote = '$addComment' WHERE blackID = '$addID'";

		$res = mysqli_query($conn_my2, $updt);

	}
	
}
?>

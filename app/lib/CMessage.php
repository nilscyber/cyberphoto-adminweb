<?php

Class CMessage {

	function getSysMessage() {
		global $sv, $no, $fi;
	
		$select  = "SELECT cnt, blogtype, beskrivning, beskrivning_fi, skapad ";
		$select .= "FROM cyberphoto.blog ";
		if ($fi) {
			if ($sv) {
				$select .= "WHERE blogType IN(17,40) AND NOT (beskrivning IS NULL) AND skapad > now() ";
			} else {
				$select .= "WHERE blogType IN(17,40) AND NOT (beskrivning_fi IS NULL) AND skapad > now() ";
			}
		} elseif($no) {
			$select .= "WHERE blogType IN(14,41) AND NOT (beskrivning IS NULL) AND skapad > now() ";
		} else {
			$select .= "WHERE blogType IN(14) AND NOT (beskrivning IS NULL) AND skapad > now() ";
		}
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND offentlig = -1 ";
		}
		$select .= "ORDER BY skapad ASC ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
		$check = mysqli_num_rows($res);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_object($res)) {
			
				if ($fi && !$sv) {
					$beskrivning = eregi_replace("\n", "<br>", $row->beskrivning_fi);
				} else {
					$beskrivning = eregi_replace("\n", "<br>", $row->beskrivning);
				}
			
				echo "<div class=\"container_sys_info\">\n";
				echo "<div class=\"align_center\"><span class=\"bold\">" . $beskrivning . "</span></div>\n";
				if (CCheckIP::checkIpAdressWebAdmins($_SERVER['REMOTE_ADDR'])) {
					echo "<div class=\"align_right\"><span class=\"italic\"><a href=\"javascript:winPopupCenter(550, 650, '/order/admin/productblogg.php?change=" . $row->cnt . "&addsys=yes');\">Uppdatera</a></span></div>\n";
				}
				echo "</div>\n";
				
			}
		
		}

	}

	function getOpeningMessage() {
		global $sv, $no, $fi;
	
		$select  = "SELECT cnt, blogtype, beskrivning, beskrivning_fi, skapad ";
		$select .= "FROM cyberphoto.blog ";
		if ($fi) {
			if ($sv) {
				$select .= "WHERE blogType IN(16) AND NOT (beskrivning IS NULL) AND skapad > now() ";
			} else {
				$select .= "WHERE blogType IN(16) AND NOT (beskrivning_fi IS NULL) AND skapad > now() ";
			}
		} elseif ($no) {
			$select .= "WHERE blogType IN(32) AND NOT (beskrivning IS NULL) AND skapad > now() ";
		} else {
			$select .= "WHERE blogType IN(13) AND NOT (beskrivning IS NULL) AND skapad > now() ";
		}
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND offentlig = -1 ";
		}
		$select .= "ORDER BY skapad ASC ";
		// echo $select;
		$res = mysqli_query(Db::getConnection(), $select);
		$check = mysqli_num_rows($res);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_object($res)) {
			
				if ($fi && !$sv) {
					$beskrivning = eregi_replace("\n", "<br>", $row->beskrivning_fi);
				} else {
					$beskrivning = eregi_replace("\n", "<br>", $row->beskrivning);
				}
							
				echo "<div class=\"container_opening_info\">\n";
				echo "<div class=\"align_left\"><span class=\"\">" . $beskrivning . "</span></div>\n";
				if (CCheckIP::checkIpAdressWebAdmins($_SERVER['REMOTE_ADDR'])) {
					echo "<div class=\"align_right\"><span class=\"italic\"><a href=\"javascript:winPopupCenter(550, 650, '/order/admin/productblogg.php?change=" . $row->cnt . "&addsys=yes');\">Uppdatera</a></span></div>\n";
				}
				echo "</div>\n";
				
			}
		
		}

	}
	
}
?>
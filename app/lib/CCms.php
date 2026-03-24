<?php

Class CCms {

	function checkCMSActive($cms_ID) {
		global $sv, $fi, $no;
		
		if ($fi && !$sv) {
			$select = "SELECT cms_Active FROM cyberphoto.cms WHERE cms_ID = $cms_ID ";
		} elseif ($no) {
			$select = "SELECT cms_Active FROM cyberphoto.cms WHERE cms_ID = $cms_ID ";
		} else {
			$select = "SELECT cms_Active FROM cyberphoto.cms WHERE cms_ID = $cms_ID ";
		}
		
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			if ($row->cms_Active == -1) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function getCMSHeadline($cms_ID) {
		global $sv, $fi, $no;
		
		if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			$select = "SELECT cms_Headline FROM cyberphoto.cms WHERE cms_ID = $cms_ID ";
		} else {
			$select = "SELECT cms_Headline FROM cyberphoto.cms WHERE cms_Active = -1 AND cms_ID = $cms_ID ";
		}
		
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			return $row->cms_Headline;
		} else {
			return;
		}
	}

	function getCMSText($cms_ID) {
		global $sv, $fi, $no;
		
		if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) {
			$select = "SELECT cms_Text FROM cyberphoto.cms WHERE cms_ID = $cms_ID ";
		} else {
			$select = "SELECT cms_Text FROM cyberphoto.cms WHERE cms_Active = -1 AND cms_ID = $cms_ID ";
		}
		
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			return $row->cms_Text;
		} else {
			return;
		}
	}


	// ***************************** NEDAN ÄR FÖR ADMIN ******************************************
	
	function getCmsListAdmin($department) {
		global $sv;
		
		$rowcolor = true;
	
		$select  = "SELECT * ";
		$select .= "FROM cyberphoto.cms ";
		$select .= "WHERE cms_Department = '$department' AND cms_Arcive = 0 ";
		$select .= "ORDER BY cms_Headline ASC ";
		// echo $select;
		
		$res = mysqli_query(Db::getConnection(), $select);
		$check = mysqli_num_rows($res);

		if (mysqli_num_rows($res) > 0) {
		
			if ($department == "alla") {
				$department = "ej specificerat";
			}
		
			echo "<div class=\"top10\">\n";
			echo "<h2 class=\"span_blue\">$department</h2>\n";
			echo "<table cellspacing=\"1\" cellpadding=\"2\">\n";
			echo "<tr>\n";
			echo "<td width=\"400\"><b>Rubrik</b></td>\n";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			}
			echo "<td width=\"30\" align=\"center\"><b>&nbsp;</b></td>\n";
			echo "<td width=\"100\" align=\"left\"><b>&nbsp;</b></td>\n";
			echo "<td align=\"left\"><b>Länk till sidan</b></td>\n";
			echo "</tr>\n";
		
			while ($row = mysqli_fetch_object($res)) {

				if ($rowcolor == true) {
					$backcolor = "firstrow";
				} else {
					$backcolor = "secondrow";
				}
				echo "<tr>\n";
				echo "<td class=\"$backcolor\">$row->cms_Headline</td>\n";
				if ($row->cms_Active != -1) {
					echo "<td class=\"align_center\"><img border=\"0\" src=\"status_red.jpg\"></td>\n";
				} else {
					echo "<td class=\"align_center\"><img border=\"0\" src=\"status_green.jpg\"></td>\n";
				}
				echo "<td class=\"align_left\"><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $row->cms_ID . "\">Redigera</a></td>\n";
				if ($row->cms_Department == "alla") {
					echo "<td class=\"align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.se/cms/" . $row->cms_ID . "/" . strtolower(Tools::replace_special_char($row->cms_Headline)) . "\">/cms/" . $row->cms_ID . "/" . strtolower(Tools::replace_special_char($row->cms_Headline)) . "</a></td>\n";
				} else {
					echo "<td class=\"align_left\"><a target=\"_blank\" href=\"http://www.cyberphoto.se/" . $row->cms_Department . "/cms/" . $row->cms_ID . "/" . strtolower(Tools::replace_special_char($row->cms_Headline)) . "\">/" . $row->cms_Department . "/cms/" . $row->cms_ID . "/" . strtolower(Tools::replace_special_char($row->cms_Headline)) . "</a></td>\n";
				}
				echo "</tr>\n";
			
				if ($rowcolor == true) {
					$row = true;
					$rowcolor = false;
				} else {
					$row = false;
					$rowcolor = true;
				}
				
			}
			
			echo "</table>\n";
			echo "</div>\n";
		
		}

	}

	function getSpecCms($ID) {

		$select  = "SELECT * FROM cyberphoto.cms WHERE cms_ID = '" . $ID . "' ";
		// echo $select;
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		return $rows;

	}

	function doCmsChange($headline,$department,$area,$addBy,$addactive,$addid,$area_backup) {

		// $area = preg_replace("/\"/", "&#34;", $area);
		// $area = preg_replace("/\'/", "&#39;", $area);
		// $area = htmlentities($area);
		// $area = html_entity_decode($area);
		
		$updt_backup  = "INSERT INTO cyberphoto.cms ";
		$updt_backup .= "(cms_Headline,cms_Department,cms_Text,cms_Created,cms_CreatedBy,cms_CreatedByIP,cms_Active,cms_Arcive,cms_Arcive_ID,cms_Arcive_Text) ";
		$updt_backup .= "VALUES ";
		$updt_backup .= "('$headline','$department','$area',now(),'$addBy','" . $_SERVER['REMOTE_ADDR'] . "','0','-1','$addid','$area_backup') ";

		$res = mysqli_query(Db::getConnection(true), $updt_backup);

		$updt  = "UPDATE cyberphoto.cms ";
		$updt .= "SET ";
		$updt .= "cms_Headline = '$headline', cms_Department = '$department', cms_Text = '$area', ";
		$updt .= "cms_Updated = now(), cms_UpdatedBy = '$addBy', cms_UpdatedByIP = '" . $_SERVER['REMOTE_ADDR'] . "', cms_Active = '$addactive' ";
		$updt .= "WHERE cms_ID = '$addid'";

		// echo $updt_backup . "<br>TOTT<br>";
		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);

		header("Location: cms.php");

	}

	function doCmsAdd($headline,$department,$area,$addBy,$addactive) {

		// $area = preg_replace("/\"/", "&#34;", $area);
		// $area = preg_replace("/\'/", "&#39;", $area);
		
		$updt  = "INSERT INTO cyberphoto.cms ";
		$updt .= "(cms_Headline,cms_Department,cms_Text,cms_Created,cms_CreatedBy,cms_CreatedByIP,cms_Active) ";
		$updt .= "VALUES ";
		$updt .= "('$headline','$department','$area',now(),'$addBy','" . $_SERVER['REMOTE_ADDR'] . "','$addactive') ";

		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);

		header("Location: cms.php");

	}

	function replace_special_char($string) {
		$from = array("2");
		$to = array("4");
		$newstring = str_replace($from, $to, $string);
		// $newstring = preg_replace("/---/", "-", $newstring);
		// $newstring = preg_replace("/--/", "-", $newstring);
		return $newstring;
	}
	
}
?>
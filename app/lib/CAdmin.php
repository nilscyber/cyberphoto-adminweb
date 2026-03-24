<?php


Class CAdmin {

	var $conn_my;

	function __construct() {

		$this->conn_my = Db::getConnectionDb('cyberadmin');

		/*
		$this->conn_my = @mysqli_connect(getenv('DB_HOST') ?: 'db', getenv('DB_USER') ?: 'appuser', getenv('DB_PASS') ?: 'apppass');
		@mysqli_select_db($this->conn_my, "cyberadmin");
		*/

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

	function getAd() {

		$desiderow = true;

		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
		echo "<tr>\n";
		echo "<th width=\"20\">&nbsp;</th>\n";
		echo "<th width=\"150\">Gruppering</th>\n";
		echo "<th width=\"350\">Namn</th>\n";
		echo "<th width=\"90\" align=\"center\">Gäller till</th>\n";
		echo "<th width=\"350\">Länkas till</th>\n";
		// echo "<th width=\"150\">Kommentar</th>\n";
		// echo "<th width=\"30\">Bild</th>\n";
		echo "<th width=\"50\">&nbsp;</th>";
		echo "<th width=\"75\">&nbsp;</th>";
		echo "</tr>\n";

		$select = "SELECT * FROM adtrigger WHERE adFrom < now() AND adTo > now() ORDER BY adTo ASC";
		
		$res = mysqli_query($this->conn_my, $select);
		
			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
					if ($desiderow == true) {
						$rowcolor = "firstrow";
					} else {
						$rowcolor = "secondrow";
					}

					echo "<tr>";
					if ($adCountry == 1) {
						echo "<td align=\"center\"><img src=\"fi_mini.jpg\" border=\"0\"></td>";
					} else {
						echo "<td align=\"center\"><img src=\"sv_mini.jpg\" border=\"0\"></td>";
					}
					echo "<td class=\"$rowcolor\"><a href=\"adtrigger.php?group=" . $adGroup . "\">$adGroup</a></td>";
					echo "<td class=\"$rowcolor\">$adName</td>";
					echo "<td class=\"$rowcolor\">" . date("j M Y", strtotime($adTo)) . "</td>";
					echo "<td class=\"$rowcolor\"><a target=\"_blank\" href=\"/$adLinc\">$adLinc</a></td>";
					/// echo "<td class=\"$rowcolor\">$adComment</td>";
					/*
					if ($adPicture != "") {
						echo "<td align=\"center\"><a onMouseOver=\"this.T_WIDTH=850;return escape('<img border=\'0\' src=\'$adPicture\'>')\"><img src=\"status_blue.jpg\" border=\"0\"></a></td>";
					} else {
						echo "<td align=\"center\"><img src=\"status_white.jpg\" border=\"0\"></td>";
					}
					*/
					echo "<td align=\"right\"><a href=\"adtrigger.php?change=" . $adID . "\"><b>Ändra</b></a></td>";
					echo "<td align=\"center\"><a href=\"adtrigger.php?show=" . $adID . "\"><b>Statistik</b></a></td>";
					echo "</tr>";
			
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}

				endwhile;
			
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"8\"><font color=\"red\"><b>Det finns inga poster att visa</b></font></td>";
				echo "</tr>";
			}

		echo "</table>\n";
	}

	function getAdPlan() {

		$desiderow = true;

		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
		echo "<tr>\n";
		echo "<th width=\"20\">&nbsp;</th>\n";
		echo "<th width=\"150\">Gruppering</th>\n";
		echo "<th width=\"350\">Namn</th>\n";
		echo "<th width=\"90\" align=\"center\">Gäller från</th>\n";
		echo "<th width=\"350\">Länkas till</th>\n";
		// echo "<th width=\"150\">Kommentar</th>\n";
		// echo "<th width=\"30\">Bild</th>\n";
		echo "<th width=\"50\">&nbsp;</th>";
		echo "<th width=\"75\">&nbsp;</th>";
		echo "</tr>\n";

		$select = "SELECT * FROM adtrigger WHERE adFrom > now() ORDER BY adFrom";
		
		$res = mysqli_query($this->conn_my, $select);
		
			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
					if ($desiderow == true) {
						$rowcolor = "firstrow";
					} else {
						$rowcolor = "secondrow";
					}

					echo "<tr>";
					if ($adCountry == 1) {
						echo "<td align=\"center\"><img src=\"fi_mini.jpg\" border=\"0\"></td>";
					} else {
						echo "<td align=\"center\"><img src=\"sv_mini.jpg\" border=\"0\"></td>";
					}
					echo "<td class=\"$rowcolor\"><a href=\"adtrigger.php?group=" . $adGroup . "\">$adGroup</a></td>";
					echo "<td class=\"$rowcolor\">$adName</font></b></td>";
					echo "<td class=\"$rowcolor\">" . date("j M Y", strtotime($adFrom)) . "</td>";
					echo "<td class=\"$rowcolor\"><a target=\"_blank\" href=\"/$adLinc\">$adLinc</a></td>";
					// echo "<td class=\"$rowcolor\">$adComment</font></b></td>";
					/*
					if ($adPicture != "") {
						echo "<td align=\"center\"><a onMouseOver=\"this.T_WIDTH=850;return escape('<img border=\'0\' src=\'$adPicture\'>')\"><img src=\"status_blue.jpg\" border=\"0\"></a></td>";
					} else {
						echo "<td align=\"center\"><img src=\"status_white.jpg\" border=\"0\"></td>";
					}
					*/
					echo "<td align=\"right\"><a href=\"adtrigger.php?change=" . $adID . "\"><b>Ändra</b></a></td>";
					echo "</tr>";
			
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}

				endwhile;
			
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"8\"><font color=\"red\"><b>Det finns inga poster att visa</font></td>";
				echo "</tr>";
			}

		echo "</table>\n";

	}

	function getAdHistory() {

		$desiderow = true;

		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
		echo "<tr>\n";
		echo "<th width=\"20\">&nbsp;</th>\n";
		echo "<th width=\"150\">Gruppering</th>\n";
		echo "<th width=\"350\">Namn</th>\n";
		echo "<th width=\"90\" align=\"center\">Visades till</th>\n";
		echo "<th width=\"350\">Länkas till</th>\n";
		// echo "<th width=\"150\">Kommentar</th>\n";
		// echo "<th width=\"30\">Bild</th>\n";
		echo "<th width=\"50\">&nbsp;</th>";
		echo "<th width=\"75\">&nbsp;</th>";
		echo "</tr>\n";

		$select = "SELECT * FROM adtrigger WHERE adTo < now() ORDER BY adTo DESC";
		
		$res = mysqli_query($this->conn_my, $select);
		
			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
					if ($desiderow == true) {
						$rowcolor = "firstrow";
					} else {
						$rowcolor = "secondrow";
					}

					echo "<tr>";
					if ($adCountry == 1) {
						echo "<td align=\"center\"><img src=\"fi_mini.jpg\" border=\"0\"></td>";
					} else {
						echo "<td align=\"center\"><img src=\"sv_mini.jpg\" border=\"0\"></td>";
					}
					echo "<td class=\"$rowcolor\"><a href=\"adtrigger.php?group=" . $adGroup . "\">$adGroup</a></td>";
					echo "<td class=\"$rowcolor\">$adName</font></b></td>";
					echo "<td class=\"$rowcolor\">" . date("j M Y", strtotime($adTo)) . "</td>";
					echo "<td class=\"$rowcolor\"><a target=\"_blank\" href=\"/$adLinc\">$adLinc</a></td>";
					// echo "<td class=\"$rowcolor\">$adComment</font></b></td>";
					/*
					if ($adPicture != "") {
						echo "<td align=\"center\"><a onMouseOver=\"this.T_WIDTH=850;return escape('<img border=\'0\' src=\'$adPicture\'>')\"><img src=\"status_blue.jpg\" border=\"0\"></a></td>";
					} else {
						echo "<td align=\"center\"><img src=\"status_white.jpg\" border=\"0\"></td>";
					}
					*/
					echo "<td align=\"center\"><a href=\"adtrigger.php?show=" . $adID . "\"><b>Statistik</b></a></td>";
					// echo "<td align=\"right\"><font face=\"Verdana\" size=\"1\"><a href=\"adtrigger.php?change=" . $adID . "\"><b>Ändra</b></a></font></b></td>";
					echo "</tr>";
			
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}

				endwhile;
			
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"8\"><font color=\"red\"><b>Det finns inga poster att visa</b></font></td>";
				echo "</tr>";
			}

		echo "</table>\n";
	}

	function getAdDetail($show) {

		$desiderow = true;

		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
		echo "<tr>\n";
		echo "<th width=\"20\">&nbsp;</th>\n";
		echo "<th width=\"100\">Gruppering</th>\n";
		echo "<th width=\"250\">Namn</th>\n";
		echo "<th width=\"90\" align=\"center\">Gäller till</th>\n";
		echo "<th width=\"150\">Länk</th>\n";
		echo "<th width=\"150\">Kommentar</th>\n";
		echo "<th width=\"30\">Bild</th>\n";
		echo "<th width=\"50\">&nbsp;</th>";
		echo "<th width=\"75\">&nbsp;</th>";
		echo "</tr>\n";

		$select = "SELECT * FROM adtrigger WHERE adID = '" . $show . "' ";
		
		$res = mysqli_query($this->conn_my, $select);
		
			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
					if ($desiderow == true) {
						$rowcolor = "firstrow";
					} else {
						$rowcolor = "secondrow";
					}

					echo "<tr>";
					if ($adCountry == 1) {
						echo "<td align=\"center\"><img src=\"fi_mini.jpg\" border=\"0\"></td>";
					} else {
						echo "<td align=\"center\"><img src=\"sv_mini.jpg\" border=\"0\"></td>";
					}
					echo "<td class=\"$rowcolor\">$adGroup</td>";
					echo "<td class=\"$rowcolor\">$adName</td>";
					echo "<td class=\"$rowcolor\">" . date("j M Y", strtotime($adTo)) . "</td>";
					echo "<td class=\"$rowcolor\"><a target=\"_blank\" href=\"/$adLinc\">$adLinc</a></td>";
					echo "<td class=\"$rowcolor\">$adComment</td>";
					if ($adPicture != "") {
						echo "<td align=\"center\"><a onMouseOver=\"this.T_WIDTH=850;return escape('<img border=\'0\' src=\'$adPicture\'>')\"><img src=\"status_blue.jpg\" border=\"0\"></a></td>";
					} else {
						echo "<td align=\"center\"><img src=\"status_white.jpg\" border=\"0\"></td>";
					}
					echo "<td align=\"right\"><a href=\"adtrigger.php?change=" . $adID . "\"><b>Ändra</b></a></td>";
					echo "<td align=\"center\"><a href=\"adtrigger.php?show=" . $adID . "\"><b>Statistik</b></a></td>";
					if ($adCountry == 1) {
						echo "<td><a target=\"_blank\" href=\"http://www.cyberphoto.fi/ad_fi.php?ID=$adID\">http://www.cyberphoto.fi/ad.php?ID=$adID</font></a></td>";
					} else {
						echo "<td><a target=\"_blank\" href=\"http://www.cyberphoto.se/ad.php?ID=$adID\">http://www.cyberphoto.se/ad.php?ID=$adID</font></a></td>";
					}
					echo "</tr>";
			
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}

				endwhile;
			
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"9\"><font face=\"Verdana\" size=\"1\" color=\"red\"><b>Det finns inga poster att visa</b></font></td>";
				echo "</tr>";
			}

		echo "</table>\n";
	}

	function getLogg($show) {

		$desiderow = true;

		echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n";
		echo "<tr>\n";
		echo "<th width=\"140\">Datum</th>\n";
		echo "<th width=\"100\">Antal</th>\n";
		echo "</tr>\n";

		$select = "SELECT DATE_FORMAT(loggDate, '%Y-%m-%d') AS Datum, COUNT(loggID) AS Antal FROM loggtrigger WHERE loggAd = '" . $show . "' AND NOT (loggIP = 'internt') GROUP BY Datum DESC ";
		
		$res = mysqli_query($this->conn_my, $select);
		
			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
					if ($desiderow == true) {
						$rowcolor = "firstrow";
					} else {
						$rowcolor = "secondrow";
					}

					echo "<tr>";
					// echo "<td bgcolor=\"$backcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i:s", strtotime($loggDate)) . "</font></b></td>";
					echo "<td class=\"$rowcolor\"><a href=\"adtrigger.php?show=$show&detail=$Datum\">$Datum</td>";
					echo "<td class=\"$rowcolor\">$Antal</td>";
					echo "</tr>";
			
					if ($desiderow == true) {
						$desiderow = false;
					} else {
						$desiderow = true;
					}

				endwhile;
						echo "<tr>";
						echo "<td colspan=\"2\"><font face=\"Verdana\" size=\"1\">&nbsp;</font></td>";
						echo "</tr>";

			} else {
			
				echo "<tr>";
				echo "<td colspan=\"2\"><font face=\"Verdana\" size=\"1\" color=\"red\"><b>Inga klick är gjorda</b></font></td>";
				echo "</tr>";
			}

		echo "</table>\n";
	}

	function getLoggDetail($show,$detail) {

	$rowcolor = true;

		$select = "SELECT loggDate, loggIP FROM loggtrigger WHERE loggAd = '" . $show . "' AND loggDate LIKE '$detail%' ORDER BY loggDate DESC ";
		
		$res = mysqli_query($this->conn_my, $select);
		
			echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"3\">";
			echo "<tr>";
			echo "<td width=\"140\"><b><font face=\"Verdana\" size=\"1\">Tidpunkt</font></b></td>";
			echo "<td width=\"100\"><b><font face=\"Verdana\" size=\"1\">IP nummer</font></b></td>";
			echo "</tr>";
			
			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
					if ($rowcolor == true) {
						$backcolor = "#CCCCCC";
					} else {
						$backcolor = "#E8E8E8";
					}

					echo "<tr>";
					echo "<td bgcolor=\"$backcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i:s", strtotime($loggDate)) . "</font></b></td>";
					echo "<td bgcolor=\"$backcolor\"><font face=\"Verdana\" size=\"1\">$loggIP</font></b></td>";
					echo "</tr>";
			
					if ($rowcolor == true) {
						$row = true;
						$rowcolor = false;
					} else {
						$row = false;
						$rowcolor = true;
					}

				endwhile;
						echo "<tr>";
						echo "<td colspan=\"2\"><font face=\"Verdana\" size=\"1\">&nbsp;</font></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td colspan=\"2\"><font face=\"Verdana\" size=\"1\"><a href=\"adtrigger.php?deleteIPinternt=$show\"><img src=\"recycle.jpg\" border=\"0\">&nbsp;Rensa alla interna klick</font></td>";
						echo "</tr>";

			} else {
			
				echo "<tr>";
				echo "<td colspan=\"2\"><font face=\"Verdana\" size=\"1\" color=\"red\"><b>Inga klick är gjorda</b></font></td>";
				echo "</tr>";
			}
			
			echo "</table>";

	}

	function getLoggWiz($show) {

	?>
		<script type='text/javascript' src='http://www.google.com/jsapi'></script>
		<script type='text/javascript'>
		  google.load('visualization', '1', {'packages':['annotatedtimeline']});
		  google.setOnLoadCallback(drawChart);
		  function drawChart() {
			var data = new google.visualization.DataTable();
			data.addColumn('date', 'Date');
			data.addColumn('number', 'Antal klick');
			data.addRows([
	<?php

		$select = "SELECT DATE_FORMAT(loggDate, '%Y-%m-%d') AS Datum, COUNT(loggID) AS Antal FROM loggtrigger WHERE loggAd = '" . $show . "' AND NOT (loggIP = 'internt') GROUP BY Datum DESC ";
		
		$res = mysqli_query($this->conn_my, $select);
		
			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
					$pyear = date("Y", strtotime($Datum));
					$pmonth = date("n", strtotime($Datum))-1;
					$pday = date("j", strtotime($Datum));
					
					echo "\t[new Date($pyear, $pmonth ,$pday), $Antal],\n";

				endwhile;

			} else {
			
				echo "";
			}

	?>
			]);

			var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
			chart.draw(data, {displayAnnotations: true, thickness: 2, fill: 30});
		  }
		</script>
		<div id='chart_div' style='width: 850px; height: 240px;'></div>
	<?php

	}

	function getLoggTot($show) {

		$select = "SELECT COUNT(loggID) AS Antal FROM loggtrigger WHERE loggAd = '" . $show . "' AND NOT (loggIP = 'internt') ";
		
		$res = mysqli_query($this->conn_my, $select);
		
		$row = mysqli_fetch_object($res);

		return $row->Antal;
		

	}

	function getBuyTot($show) {

		$select = "SELECT COUNT(buyID) AS Antal FROM buytrigger WHERE buyAd = '" . $show . "' ";
		
		$res = mysqli_query($this->conn_my, $select);
		
		$row = mysqli_fetch_object($res);

		return $row->Antal;
		

	}

	function getLoggTotGroup($group) {

		$select = "SELECT COUNT(loggID) AS Antal ";
		$select .= "FROM loggtrigger ";
		$select .= "JOIN adtrigger ON loggtrigger.loggAd = adtrigger.adID ";
		$select .= "WHERE adGroup = '" . $group . "' AND NOT (loggIP = 'internt') ";
		
		$res = mysqli_query($this->conn_my, $select);
		
		$row = mysqli_fetch_object($res);

		return $row->Antal;
		

	}

	function getBuyTotGroup($group) {

		$select = "SELECT COUNT(buyID) AS Antal ";
		$select .= "FROM buytrigger ";
		$select .= "JOIN adtrigger ON buytrigger.buyAd = adtrigger.adID ";
		$select .= "WHERE adGroup = '" . $group . "' ";
		
		// echo $select;
		
		$res = mysqli_query($this->conn_my, $select);
		
		$row = mysqli_fetch_object($res);

		return $row->Antal;
		

	}


	function getAnstallda() {

	global $addcreatedby;

	$select  = "SELECT sign, namn FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY namn ";

	$res = mssql_query ($select);

		while ($row = mssql_fetch_array($res)) {
		
		extract($row);

		echo "<option value=\"$sign\"";
			
		if ($addcreatedby == $sign) {
			echo " selected";
		}
			
		echo ">" . $namn . "</option>";
			
		
		// endwhile;

		}

	}

	function getSpecPricelist($adID) {

	$select  = "SELECT * FROM adtrigger WHERE adID = '" . $adID . "' ";

	$res = mysqli_query($this->conn_my, $select);

	$rows = mysqli_fetch_object($res);

	return $rows;

	}

	function AddAd($addgroup,$addrubrik,$addlinc,$addcomment,$addcreatedby,$addfrom,$addto,$addpicture,$addcountry) {

	mysqli_query($this->conn_my, "INSERT INTO adtrigger (adGroup,adName,adLinc,adComment,adBy,adFrom,adTo,adPicture,adCountry) VALUES ('$addgroup','$addrubrik','$addlinc','$addcomment','$addcreatedby','$addfrom','$addto','$addpicture','$addcountry') ");

	header("Location: adtrigger.php");

	}

	function ChangeAd($addgroup,$addid,$addrubrik,$addlinc,$addcomment,$addcreatedby,$addfrom,$addto,$addpicture,$addcountry) {

	mysqli_query($this->conn_my, "UPDATE adtrigger SET adGroup = '$addgroup', adName = '$addrubrik', adLinc = '$addlinc', adComment = '$addcomment', adBy = '$addcreatedby', adFrom = '$addfrom', adTo = '$addto', adPicture = '$addpicture', adCountry = '$addcountry' WHERE adID = '$addid' ");

	header("Location: adtrigger.php?show=$addid");

	}

	function deleteIPinternt($deleteIPinternt) {

	mysqli_query($this->conn_my, "DELETE FROM loggtrigger WHERE loggAd = $deleteIPinternt AND loggIP = 'internt'");

	header("Location: adtrigger.php?show=$deleteIPinternt");

	}

	function getAdID($ID) {

		$select = "SELECT adID FROM adtrigger WHERE adFrom < now() AND adTo > now() AND adID = '" . $ID . "' ";
		
		$res = mysqli_query($this->conn_my, $select);
		
			if (mysqli_num_rows($res) > 0) {
			
				return true;
			
			} else {
			
				return false;
			
			}

	}

	function SendUser($ID) {

		$select = "SELECT adLinc FROM adtrigger WHERE adID = '" . $ID . "' ";
		
		$res = mysqli_query($this->conn_my, $select);

		while ($row = mysqli_fetch_array($res)) {
		
		extract($row);
		
		return $adLinc;
		
		}

	}

	function AddLogg($ID,$IP) {

	$insrt = "INSERT INTO loggtrigger (loggAd,loggIP) VALUES ('$ID','$IP') ";

	mysqli_query($this->conn_my, $insrt);

	}

	function AddBuyTrigger($adID,$IP,$ordernr) {

	$insrt = "INSERT INTO buytrigger (buyIP,buyAd,buyOrderNr) VALUES ('$IP','$adID','$ordernr') ";

	mysqli_query($this->conn_my, $insrt);

	}

}

?>

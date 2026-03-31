<?php

/*

PHP
author		Stefan Sjöberg
version		1.0 2012-01-05

*/

Class CParcelCheck {

var $conn_my;

	function __construct() {

		$this->conn_my = Db::getConnection();

	}

	function getProductsInPac($artnr) {

		$artnr2 = $artnr; // behöver den längre ned
		$countrow = 0;
		$totalsum = 0;

		$select  = "SELECT CONCAT(t.tillverkare,' ', a.beskrivning) AS produkt, a.utpris, m.momssats, pkt.antal, a.artnr ";
		$select .= "FROM Paketpriser pkt ";
		$select .= "JOIN Artiklar a ON a.artnr = pkt.artnr_del ";
		$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
		$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "WHERE pkt.artnr_paket = '$artnr' ";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

		$res = mysqli_query($this->conn_my, $select);

				echo "<table>";
				echo "<tr>";
				echo "<td width=\"40\" align=\"left\"><b>Antal</b></td>";
				echo "<td width=\"85\" align=\"left\"><b>Artikel nr</b></td>";
				echo "<td width=\"130\" align=\"left\"><b>Beskrivning</b></td>";
				echo "<td width=\"75\" align=\"center\"><b>Pris</b></td>";
				echo "</tr>";

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
				$utpriset = $utpris * $antal;
				$utpris_moms = $utpriset + ($utpriset * $momssats);
				

				echo "<tr>";
				echo "<td align=\"left\">$antal st</td>";
				echo "<td align=\"left\">$artnr</td>";
				echo "<td align=\"left\">$produkt</td>";
				echo "<td align=\"right\">$utpris_moms kr</td>";
				echo "</tr>";

				$countrow ++;
				$totalsum = $totalsum + $utpris_moms;
				endwhile;
				
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"4\">Detta kan inte vara möjligt</td>";
				echo "</tr>";
			
			}
				
				$pktpris = $this->getPricePac($artnr2); // hämtar vad vi tar för paketet
				$custdiscount = $totalsum - $this->getPricePac($artnr2); // kunden sparar i kronor
				$custdiscountpercent = round(($custdiscount / $totalsum)*100, 1); // kunden sparar i %
				
				echo "<tr>";
				echo "<td colspan=\"4\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td align=\"left\" colspan=\"3\"><b>Brutto:</b></td>";
				echo "<td align=\"right\"><b>$totalsum kr</b></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td align=\"left\" colspan=\"3\"><b>Paketpris:</b></td>";
				echo "<td align=\"right\"><b>$pktpris kr</b></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=\"4\"><hr noshade color=\"#85000D\" size=\"1\"></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td align=\"left\" colspan=\"3\"><b>Rabatt SEK:</b></td>";
				echo "<td align=\"right\"><b>$custdiscount kr</b></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td align=\"left\" colspan=\"3\"><b>Rabatt %:</b></td>";
				echo "<td align=\"right\"><b>$custdiscountpercent %</b></td>";
				echo "</tr>";
				echo "</table>";
	}

	function getPricePac($artnr) {

		$select  = "SELECT a.utpris, m.momssats ";
		$select .= "FROM Artiklar a ";
		$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
		$select .= "WHERE a.artnr = '$artnr' ";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
				$utpris_moms = $utpris + ($utpris * $momssats);
				return $utpris_moms;
				
				endwhile;
				
			} else {
			
				// detta skall bannemig inte behöva inträffa
				return "utpris saknas";
			
			
			}

	}

	function getActivePac($no = false) {

		$countrow = 0;
		$rowcolor = true;
		if ($no) {
			$valuta = "NOK";
		} else {
			$valuta = "SEK";
		}
		
		if ($no) {
			$select  = "SELECT a.artnr, a.beskrivning, k.kategori, SUM(a.utpris_no+(a.utpris_no*m.momssats_no)) as slutpris, a.m_product_id ";
			$select .= "FROM Artiklar a ";
			$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
			$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
			$select .= "WHERE a.IsSalesBundle = -1 AND a.ej_med = 0 AND a.utgangen = 0 AND NOT a.kategori_id IN (1000010,1000011,1000012) ";
			include ("std_instore_special_no.php");
			$criteria = preg_replace("/Artiklar./", "a.", $criteria);
			$criteria = preg_replace("/Kategori./", "k.", $criteria);
			$select .= $criteria;
			$select .= "GROUP BY a.artnr ";
			$select .= "ORDER BY k.kategori, a.beskrivning ";
		} else {
			$select  = "SELECT a.artnr, a.beskrivning, k.kategori, SUM(a.utpris+(a.utpris*m.momssats)) as slutpris, a.m_product_id ";
			$select .= "FROM Artiklar a ";
			$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
			$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
			$select .= "WHERE a.IsSalesBundle = -1 AND a.ej_med = 0 AND a.utgangen = 0 AND NOT a.kategori_id IN (1000010,1000011,1000012) ";
			$select .= "GROUP BY a.artnr ";
			$select .= "ORDER BY k.kategori, a.beskrivning ";
		}

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

		$res = mysqli_query($this->conn_my, $select);

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"200\" align=\"left\"><b>Kategori</b></td>";
			echo "<td width=\"150\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"600\" align=\"left\"><b>Beskrivning</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Utpris</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Lösa delar</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Differens</b></td>";
			if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_COOKIE['login_mail'] == 'mathias@cyberphoto.nu' || $_COOKIE['login_mail'] == 'borje@cyberphoto.nu') {
				echo "<td width=\"75\" align=\"center\"></td>";
			}
			echo "</tr>";

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					if ($this->getProductsInPacPrice($artnr,$slutpris,$no)) {
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
							$this->sendMess_v1();
							break;
						}
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						if ($no) {
							$linc = "http://www.cyberphoto.no/info.php?article=";
						} else {
							$linc = "http://www.cyberphoto.se/info.php?article=";
						}
						echo "<tr>";
						echo "<td class=\"$backcolor\" align=\"left\">$kategori</td>";
						echo "<td class=\"$backcolor\" align=\"left\">$artnr</td>";
						echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"$linc$artnr\">$beskrivning</a></td>";
						echo "<td class=\"$backcolor\" align=\"right\">$slutpris " . $valuta . "</td>";
						echo "<td class=\"$backcolor\" align=\"right\">" . round($this->displayPacPrice($artnr,$no)) . " " . $valuta . "</td>";
						echo "<td class=\"$backcolor\" align=\"right\">" . round($slutpris - $this->displayPacPrice($artnr,$no)) . " " . $valuta . "</td>";
						if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_COOKIE['login_mail'] == 'mathias@cyberphoto.nu' || $_COOKIE['login_mail'] == 'borje@cyberphoto.nu') {
							// echo "<td class=\"$backcolor\" align=\"right\">$lagersaldo st</td>";
							if ($no) {
								// echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, 'http://www.cyberphoto.no/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id');\">Åtgärda</a></td>";
								echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id&force_lang=no');\">Åtgärda</a></td>";
							} else {
								echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id&force_lang=sv');\">Åtgärda</a></td>";
							}
						}
						echo "</tr>";
					
						$countrow++;
						if ($rowcolor == true) {
							$rowcolor = false;
						} else {
							$rowcolor = true;
						}
					
					}
				
				endwhile;
				
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"3\">Detta kan inte vara möjligt??</td>";
				echo "</tr>";
			
			}
		if ($countrow == 0) {
			echo "<tr>";
			echo "<td colspan=\"7\"><font color=\"#00D900\"><b><i>Detta är lysande, alla paket är uppdaterade!</i></b></font></td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<p>Antal artiklar: <b>$countrow st</b></p>";

	}

	function getActivePacOnceADay() {

		$countrow = 0;
		$rowcolor = true;
		
		$select  = "SELECT a.artnr, k.kategori, SUM(a.utpris+(a.utpris*m.momssats)) as slutpris ";
		$select .= "FROM Artiklar a ";
		$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
		$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
		$select .= "WHERE a.IsSalesBundle = -1 AND a.ej_med = 0 AND a.utgangen = 0 AND NOT a.kategori_id IN (1000010,1000011,1000012) ";
		$select .= "GROUP BY a.artnr ";
		$select .= "ORDER BY k.kategori ";

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					if ($this->getProductsInPacPrice($artnr,$slutpris)) {
							$this->sendMess_v1();
							break;
					}
				
				endwhile;
				
			} else {
			
				return "";
		
			}

	}

	function getProductsInPacPrice($artnr,$price,$no = false) {

		$totalsum = 0;

		if ($no) {
			$select  = "SELECT a.utpris_no, m.momssats_no, pkt.antal ";
			$select .= "FROM Paketpriser pkt ";
			$select .= "JOIN Artiklar a ON a.artnr = pkt.artnr_del ";
			$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
			$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
			$select .= "WHERE pkt.artnr_paket = '$artnr' ";
		} else {
			$select  = "SELECT a.utpris, m.momssats, pkt.antal ";
			$select .= "FROM Paketpriser pkt ";
			$select .= "JOIN Artiklar a ON a.artnr = pkt.artnr_del ";
			$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
			$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
			$select .= "WHERE pkt.artnr_paket = '$artnr' ";
		}

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					if ($no) {
						$utpriset = $utpris_no * $antal;
						$utpris_moms = $utpriset + ($utpriset * $momssats_no);
					} else {
						$utpriset = $utpris * $antal;
						$utpris_moms = $utpriset + ($utpriset * $momssats);
					}

					$totalsum = $totalsum + $utpris_moms;
				
				endwhile;
				
			} else {
			
				return true; // detta skall inte behöva inträffa
			
			}
				
				if ($price > $totalsum) {
					return true;
				} else {
					return false;
				}
				
	}

	function displayPacPrice($artnr,$no) {

		$totalsum = 0;

		if ($no) {
			$select  = "SELECT a.utpris_no, m.momssats_no, pkt.antal ";
			$select .= "FROM Paketpriser pkt ";
			$select .= "JOIN Artiklar a ON a.artnr = pkt.artnr_del ";
			$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
			$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
			$select .= "WHERE pkt.artnr_paket = '$artnr' ";
		} else {
			$select  = "SELECT a.utpris, m.momssats, pkt.antal ";
			$select .= "FROM Paketpriser pkt ";
			$select .= "JOIN Artiklar a ON a.artnr = pkt.artnr_del ";
			$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
			$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
			$select .= "WHERE pkt.artnr_paket = '$artnr' ";
		}

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					if ($no) {
						$utpriset = $utpris_no * $antal;
						$utpris_moms = $utpriset + ($utpriset * $momssats_no);
					} else {
						$utpriset = $utpris * $antal;
						$utpris_moms = $utpriset + ($utpriset * $momssats);
					}

					$totalsum = $totalsum + $utpris_moms;
				
				endwhile;
				
			} else {
			
				return 0; // detta skall inte behöva inträffa
			
			}
				
			return $totalsum;
				
	}

	function sendMess_v1() {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		// $recipient .= " salj";
		$recipient .= " produkter";
		// $recipient .= " sjabo";
		// $recipient .= " rolf";
		// $recipient .= " tobias";
		
		$subj = $orderdatum . " Värdepaket måste åtgärdas!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "Vänligen kontrollera detta omgående.\n\n";
		$text1 .= "http://www.cyberphoto.se/order/admin/bad_parcel.php\n\n";
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}

	function getActivePacFI() {

		$countrow = 0;
		$rowcolor = true;
		
		$select  = "SELECT a.artnr, a.beskrivning, k.kategori, SUM(afi.utpris_fi+(afi.utpris_fi*m.momssats_fi)) as slutpris, a.m_product_id ";
		$select .= "FROM Artiklar a ";
		$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
		$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
		$select .= "JOIN Artiklar_fi afi ON a.artnr = afi.artnr_fi ";
		$select .= "WHERE a.IsSalesBundle = -1 AND a.ej_med = 0 AND afi.ej_med_fi = 0 AND a.utgangen = 0 AND NOT a.kategori_id IN (325,1000010,1000011,1000012) ";
		include ("std_instore_special_fi.php");
		$criteria = preg_replace("/Artiklar./", "a.", $criteria);
		$criteria = preg_replace("/Kategori./", "k.", $criteria);
		$select .= $criteria;
		$select .= "GROUP BY a.artnr ";
		$select .= "ORDER BY k.kategori, a.beskrivning ";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

		$res = mysqli_query($this->conn_my, $select);

			echo "<table>";
			echo "<tr>";
			echo "<td width=\"200\" align=\"left\"><b>Kategori</b></td>";
			echo "<td width=\"150\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"600\" align=\"left\"><b>Beskrivning</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Utpris</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Lösa delar</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Differens</b></td>";
			if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_COOKIE['login_mail'] == 'mathias@cyberphoto.nu' || $_COOKIE['login_mail'] == 'borje@cyberphoto.nu') {
				echo "<td width=\"75\" align=\"center\"></td>";
			}
			echo "</tr>";

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					if ($this->getProductsInPacPriceFI($artnr,$slutpris)) {
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						echo "<tr>";
						echo "<td class=\"$backcolor\" align=\"left\">$kategori</td>";
						echo "<td class=\"$backcolor\" align=\"left\">$artnr</td>";
						echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"http://www.cyberphoto.fi/info.php?article=$artnr\">$beskrivning</a></td>";
						echo "<td class=\"$backcolor\" align=\"right\">" . round($slutpris) . " EUR</td>";
						echo "<td class=\"$backcolor\" align=\"right\">" . round($this->displayPacPriceFI($artnr)) . " EUR</td>";
						echo "<td class=\"$backcolor\" align=\"right\">" . round($slutpris - $this->displayPacPriceFI($artnr)) . " EUR</td>";
						if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu' || $_COOKIE['login_mail'] == 'mathias@cyberphoto.nu' || $_COOKIE['login_mail'] == 'borje@cyberphoto.nu') {
							echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id&force_lang=fi');\">Åtgärda</a></td>";
						}
						echo "</tr>";
					
						$countrow++;
						if ($rowcolor == true) {
							$rowcolor = false;
						} else {
							$rowcolor = true;
						}
					
					}
				
				endwhile;
				
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"3\">Detta kan inte vara möjligt??</td>";
				echo "</tr>";
			
			}
		if ($countrow == 0) {
			echo "<tr>";
			echo "<td colspan=\"7\"><font color=\"#00D900\"><b><i>Detta är lysande, alla paket är uppdaterade!</i></b></font></td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<p>Antal artiklar: <b>$countrow st</b></p>";

	}

	function displayPacPriceFI($artnr) {

		$totalsum = 0;

		$select  = "SELECT afi.utpris_fi, m.momssats_fi, pkt.antal ";
		$select .= "FROM Paketpriser pkt ";
		$select .= "JOIN Artiklar a ON a.artnr = pkt.artnr_del ";
		$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
		$select .= "JOIN Artiklar_fi afi ON a.artnr = afi.artnr_fi ";
		$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "WHERE pkt.artnr_paket = '$artnr' ";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					$utpriset = $utpris_fi * $antal;
					$utpris_moms = $utpriset + ($utpriset * $momssats_fi);

					$totalsum = $totalsum + $utpris_moms;
				
				endwhile;
				
			} else {
			
				return 0; // detta skall inte behöva inträffa
			
			}
				
			return $totalsum;
				
	}

	function getProductsInPacPriceFI($artnr,$price) {

		$totalsum = 0;

		$select  = "SELECT afi.utpris_fi, m.momssats_fi, pkt.antal ";
		$select .= "FROM Paketpriser pkt ";
		$select .= "JOIN Artiklar a ON a.artnr = pkt.artnr_del ";
		$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
		$select .= "JOIN Artiklar_fi afi ON a.artnr = afi.artnr_fi ";
		$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "WHERE pkt.artnr_paket = '$artnr' ";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					$utpriset = $utpris_fi * $antal;
					$utpris_moms = $utpriset + ($utpriset * $momssats_fi);

					$totalsum = $totalsum + $utpris_moms;
				
				endwhile;
				
			} else {
			
				return true; // detta skall inte behöva inträffa
			
			}
				
				if ($price > $totalsum) {
					return true;
				} else {
					return false;
				}
				
	}

	function getActivePacOnceADayFI() {

		$countrow = 0;
		$rowcolor = true;
		
		$select  = "SELECT a.artnr, k.kategori, SUM(afi.utpris_fi+(afi.utpris_fi*m.momssats_fi)) as slutpris ";
		$select .= "FROM Artiklar a ";
		$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
		$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
		$select .= "JOIN Artiklar_fi afi ON a.artnr = afi.artnr_fi ";
		$select .= "WHERE a.IsSalesBundle = -1 AND afi.ej_med_fi = 0 AND afi.utgangen_fi = 0 AND NOT a.kategori_id IN (1000010,1000011,1000012) ";
		$select .= "GROUP BY a.artnr ";
		$select .= "ORDER BY k.kategori ";

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					if ($this->getProductsInPacPriceFI($artnr,$slutpris)) {
							$this->sendMessFI_v1();
							break;
					}
				
				endwhile;
				
			} else {
			
				return "";
		
			}

	}

	function sendMessFI_v1() {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		$recipient .= " borje";
		// $recipient .= " tobias";
		
		$subj = $orderdatum . " Värdepaket måste åtgärdas!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "Vänligen kontrollera detta omgående.\n\n";
		$text1 .= "http://www.cyberphoto.se/order/admin/bad_parcel.php\n\n";
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}

	function getArticleNotPriced($no = false) {

		$countrow = 0;
		$current_catcount = 0;
		$rowcolor = true;
		if ($no) {
			$valuta = "NOK";
		} else {
			$valuta = "SEK";
		}
		
		if ($no) {
			$select  = "SELECT a.artnr, t.tillverkare, a.beskrivning, a.utpris_no, a.lagersaldo, k.kategori, a.m_product_id ";
			$select .= "FROM Artiklar a ";
			$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
			$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
			// $select .= "WHERE a.utpris = 0 AND a.art_id > 0.1 ";
			// $select .= "WHERE ((a.utpris_no = 0 AND a.art_id_no > 0.1) OR (a.IsSalesBundle = -1 AND a.utpris_no < 0.8)) AND a.utpris > 1 ";
			$select .= "WHERE ((a.utpris_no = 0 AND a.utpris > 1) OR (a.IsSalesBundle = -1 AND a.utpris_no < 0.8)) AND a.utpris > 1 ";
			$select .= "AND a.ej_med = 0 AND a.ej_med_no = 0 AND (a.utgangen = 0 OR a.lagersaldo > 0) AND a.demo = 0 ";
			// $select .= "AND a.ej_med = 0 AND a.ej_med_no = 0 AND (a.demo = 0 OR a.lagersaldo > 0) AND (a.utgangen = 0 OR a.lagersaldo > 0) AND a.demo = 0 ";
			$select .= "AND NOT a.kategori_id = 486 ";
			include ("std_instore_special_no.php");
			$criteria = preg_replace("/Artiklar./", "a.", $criteria);
			$criteria = preg_replace("/Kategori./", "k.", $criteria);
			$select .= $criteria;
			$select .= "ORDER BY k.kategori ASC, t.tillverkare ASC, a.beskrivning ASC ";
		} else {
			$select  = "SELECT a.artnr, t.tillverkare, a.beskrivning, a.utpris, a.lagersaldo, k.kategori, a.m_product_id ";
			$select .= "FROM Artiklar a ";
			$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
			$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
			// $select .= "WHERE a.utpris = 0 AND a.art_id > 0.1 ";
			$select .= "WHERE ((a.utpris = 0 AND a.art_id > 0.1) OR (a.IsSalesBundle = -1 AND a.utpris < 0.8)) ";
			$select .= "AND a.ej_med = 0 AND (a.demo = 0 OR a.lagersaldo > 0) AND (a.utgangen = 0 OR a.lagersaldo > 0) ";
			$select .= "AND NOT a.kategori_id = 486 ";
			$select .= "ORDER BY k.kategori ASC, t.tillverkare ASC, a.beskrivning ASC ";
		}

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			// exit;
		}

		$res = mysqli_query($this->conn_my, $select);

			echo "<table width=\"1200\">";
			echo "<tr>";
			echo "<td width=\"150\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"900\" align=\"left\"><b>Artikel</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Utpris</b></td>";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.64" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				echo "<td width=\"75\" align=\"center\"></td>";
			}
			echo "</tr>";

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
						if ($kategori != $current_kategori) {
							if ($current_catcount != 0) {
								echo "<tr>\n";
								echo "<td colspan=\"3\" align=\"left\">&nbsp;</td>\n";
								echo "</tr>\n";
								$current_catcount = 0;
								$rowcolor = true;
							}
							echo "<tr>\n";
							echo "<td colspan=\"3\" align=\"left\"><b>$kategori</b></td>\n";
							echo "</tr>\n";
						}
						$current_kategori = $kategori;
						
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						$beskrivning = $tillverkare . " " . $beskrivning;
						if ($lagersaldo > 0) {
							$beskrivning .= "<b><font color=\"red\"> HÖG PRIORITET!</font></b>";
						}
						
						echo "<tr>";
						echo "<td class=\"$backcolor\" align=\"left\">$artnr</td>";
						if ($no) {
							echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"http://www.cyberphoto.no/info.php?article=$artnr\">$beskrivning</a></td>";
						} else {
							echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"/info.php?article=$artnr\">$beskrivning</a></td>";
						}
						echo "<td class=\"$backcolor\" align=\"right\">" . round($utpris,0) . " " . $valuta . "</td>";
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.64" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							// echo "<td class=\"$backcolor\" align=\"right\">$lagersaldo st</td>";
							if ($no) {
								// echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, 'http://www.cyberphoto.no/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id');\">Åtgärda</a></td>";
								echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id&force_lang=no');\">Åtgärda</a></td>";
							} else {
								echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id&force_lang=sv');\">Åtgärda</a></td>";
							}
						}
						echo "</tr>";
					
						$countrow++;
						$current_catcount++;
						if ($rowcolor == true) {
							$rowcolor = false;
						} else {
							$rowcolor = true;
						}
					
				
				endwhile;
				
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"4\"><font color=\"#00D900\"><b><i>Detta är lysande, alla produkter är prissatta!</i></b></font></td>";
				echo "</tr>";
			
			}
		if ($countrow == 0) {
		}
		echo "</table>";
		echo "<p>Antal artiklar: <b>$countrow st</b></p>";

	}

	function getArticleNotPricedOnceADay() {

		$select  = "SELECT a.artnr ";
		$select .= "FROM Artiklar a ";
		// $select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		// $select .= "WHERE a.utpris = 0 AND a.art_id > 0.1 ";
		$select .= "WHERE ((a.utpris = 0 AND a.art_id > 0.1) OR (a.IsSalesBundle = -1 AND a.utpris < 1)) ";
		$select .= "AND a.ej_med = 0 AND (a.demo = 0 OR a.lagersaldo > 0) AND (a.utgangen = 0 OR a.lagersaldo > 0) ";
		$select .= "AND NOT a.kategori_id = 486 ";
		// $select .= "ORDER BY t.tillverkare ASC, a.beskrivning ASC ";

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				$this->sendMessNotPriced_v1();
				
			} else {
			
				return;
		
			}

	}

	function sendMessNotPriced_v1() {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		// $recipient .= " salj";
		$recipient .= " produkter";
		// $recipient .= " sjabo";
		
		$subj = $orderdatum . " Det finns produkter som måste prissättas!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "Vänligen kontrollera detta i vårt affärssystem.\n\n";
		$text1 .= "http://www.cyberphoto.se/order/admin/not_priced.php\n\n";
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}

	function getArticleNotPricedFI() {

		$countrow = 0;
		$current_catcount = 0;
		$rowcolor = true;
		
		$select  = "SELECT a.artnr, t.tillverkare, afi.beskrivning_fi, afi.utpris_fi, a.lagersaldo, k.kategori, a.m_product_id ";
		$select .= "FROM Artiklar a ";
		$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "JOIN Artiklar_fi afi ON afi.artnr_fi = a.artnr ";
		$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
		// $select .= "WHERE afi.utpris_fi = 0 AND a.art_id > 0.1 ";
		// $select .= "WHERE afi.utpris_fi < 0.5 AND a.utpris > 1 ";
		// $select .= "WHERE (afi.utpris_fi < 0.5 OR afi.utpris_fi IS NULL) AND a.utpris > 1 ";
		$select .= "WHERE (((afi.utpris_fi = 0 || afi.utpris_fi IS NULL) AND a.utpris > 1) OR (a.IsSalesBundle = -1 AND afi.utpris_fi < 0.8)) AND a.utpris > 1 ";
		$select .= "AND a.ej_med = 0 AND afi.ej_med_fi = 0 AND (a.utgangen = 0 OR a.lagersaldo > 0) AND a.demo = 0 ";
		$select .= "AND NOT a.kategori_id = 486 ";
		include ("std_instore_special_fi.php");
		$criteria = preg_replace("/Artiklar./", "a.", $criteria);
		$select .= $criteria;
		$select .= "ORDER BY k.kategori ASC, t.tillverkare ASC, a.beskrivning ASC ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = mysqli_query($this->conn_my, $select);

			echo "<table width=\"1200\">";
			echo "<tr>";
			echo "<td width=\"150\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"900\" align=\"left\"><b>Artikel</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Utpris</b></td>";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.64" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				echo "<td width=\"75\" align=\"center\"></td>";
			}
			echo "</tr>";

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);

						if ($kategori != $current_kategori) {
							if ($current_catcount != 0) {
								echo "<tr>\n";
								echo "<td colspan=\"3\" align=\"left\">&nbsp;</td>\n";
								echo "</tr>\n";
								$current_catcount = 0;
								$rowcolor = true;
							}
							echo "<tr>\n";
							echo "<td colspan=\"3\" align=\"left\"><b>$kategori</b></td>\n";
							echo "</tr>\n";
						}
						$current_kategori = $kategori;
					
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						$beskrivning_fi = $tillverkare . " " . $beskrivning_fi;
						if ($lagersaldo > 0) {
							$beskrivning_fi .= "<b><font color=\"red\"> HÖG PRIORITET!</font></b>";
						}
						
						echo "<tr>";
						echo "<td class=\"$backcolor\" align=\"left\">$artnr</td>";
						echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"http://www.cyberphoto.fi/info.php?article=$artnr\">$beskrivning_fi</a></td>";
						echo "<td class=\"$backcolor\" align=\"right\">" . round($utpris,0) . " EUR</td>";
						if ($_SERVER['REMOTE_ADDR'] == "192.168.1.64" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							// echo "<td class=\"$backcolor\" align=\"right\">$lagersaldo st</td>";
							echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id&force_lang=fi');\">Åtgärda</a></td>";
						}
						echo "</tr>";
					
						$countrow++;
						$current_catcount++;
						if ($rowcolor == true) {
							$rowcolor = false;
						} else {
							$rowcolor = true;
						}
					
				
				endwhile;
				
			} else {
			
				echo "<tr>";
				echo "<td colspan=\"4\"><font color=\"#00D900\"><b><i>Detta är lysande, alla produkter är prissatta!</i></b></font></td>";
				echo "</tr>";
			
			}
		if ($countrow == 0) {
		}
		echo "</table>";
		echo "<p>Antal artiklar: <b>$countrow st</b></p>";

	}

	function getArticleNotPricedOnceADayFI() {

		$select  = "SELECT a.artnr ";
		$select .= "FROM Artiklar a ";
		// $select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "JOIN Artiklar_fi afi ON afi.artnr_fi = a.artnr ";
		// $select .= "WHERE afi.utpris_fi = 0 AND a.art_id > 0.1 ";
		// $select .= "WHERE afi.utpris_fi < 0.5 AND a.utpris > 1 ";
		$select .= "WHERE (afi.utpris_fi < 0.5 OR afi.utpris_fi IS NULL) AND a.utpris > 1 ";
		$select .= "AND afi.ej_med_fi = 0 AND (a.demo = 0 OR a.lagersaldo > 0) AND (a.utgangen = 0 OR a.lagersaldo > 0) ";
		$select .= "AND NOT a.kategori_id = 486 ";

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				$this->sendMessNotPricedFI_v1();
				
			} else {
			
				return;
		
			}

	}

	function sendMessNotPricedFI_v1() {

		$orderdatum = date("Y-m-d H:i:s", time());
		
		$addcreatedby = "noreply";

		$recipient .= " borje";
		// $recipient .= " sjabo";
		
		$subj = $orderdatum . " Det finns produkter som måste prissättas!";

		$extra = "From: " . $addcreatedby;
		
		$text1 = "Vänligen kontrollera detta i vårt affärssystem.\n\n";
		$text1 .= "http://www.cyberphoto.se/order/admin/not_priced.php\n\n";
		
		SmtpMail::send($recipient, $subj, $text1, $extra);

	}
	
	function getBestTG() {
		global $sortby;

		$countrow = 0;
		$rowcolor = true;
		
		$select  = "SELECT a.artnr, t.tillverkare, a.beskrivning, k.kategori, a.art_id, a.utpris, a.lagersaldo, SUM(utpris-art_id) AS TB, SUM(((utpris-art_id)/utpris)*100) AS TG ";
		$select .= "FROM Artiklar a ";
		$select .= "JOIN Tillverkare t ON a.tillverkar_id = t.tillverkar_id ";
		$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
		$select .= "WHERE a.lagersaldo > 0 AND art_id > 0 AND utpris > 0 AND NOT a.demo = -1 ";
		$select .= "GROUP BY a.artnr ";
		if ($sortby == "TB") {
			$select .= "ORDER BY TB DESC ";
		} else {
			$select .= "ORDER BY TG DESC ";
		}
		$select .= "LIMIT 200 ";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

		$res = mysqli_query($this->conn_my, $select);

			echo "<table width=\"1200\">\n";
			echo "\t<tr>\n";
			echo "\t\t<td width=\"130\" align=\"left\"><b>Artnr</b></td>\n";
			echo "\t\t<td align=\"left\"><b>Artikel</b></td>\n";
			echo "\t\t<td width=\"90\" align=\"center\"><b>Inpris</b></td>\n";
			echo "\t\t<td width=\"90\" align=\"center\"><b>Utpris</b></td>\n";
			echo "\t\t<td width=\"90\" align=\"center\"><b>Lagersaldo</b></td>\n";
			echo "\t\t<td width=\"90\" align=\"center\"><b><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=TB\">TB</a></b></td>\n";
			echo "\t\t<td width=\"90\" align=\"center\"><b><a href=\"" . $_SERVER['PHP_SELF'] . "?sortby=TG\">TG</a></b></td>\n";
			echo "\t</tr>\n";

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
						if ($rowcolor == true) {
							$backcolor = "firstrow";
						} else {
							$backcolor = "secondrow";
						}
						$beskrivning = $tillverkare . " " . $beskrivning;
						if ($lagersaldo > 1000000) {
							$beskrivning .= "<b><font color=\"red\"> HÖG PRIORITET!</font></b>";
						}
						
						echo "\t<tr>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"left\">$artnr</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"/info.php?article=$artnr\">$beskrivning</a></td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"right\">" . number_format(round($art_id, 2), 2, ',', ' ') . " SEK&nbsp;</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"right\">" . number_format(round($utpris, 2), 2, ',', ' ') . " SEK&nbsp;</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"right\">$lagersaldo st&nbsp;</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"right\">" . number_format(round($TB, 2), 2, ',', ' ') . " SEK&nbsp;</td>\n";
						echo "\t\t<td class=\"$backcolor\" align=\"right\"><b><span style=\"color: #228622;\">" . number_format(round($TG, 2), 2, ',', ' ') . " %&nbsp;</span></b></td>\n";
						echo "\t</tr>\n";
					
						$countrow++;
						if ($rowcolor == true) {
							$rowcolor = false;
						} else {
							$rowcolor = true;
						}
					
				
				endwhile;
				
			} else {
			
				echo "\t<tr>\n";
				echo "\t\t<td colspan=\"4\"><font color=\"#00D900\"><b><i>Detta är lysande, alla produkter är prissatta!</i></b></font></td>\n";
				echo "\t</tr>\n";
			
			}
		if ($countrow == 0) {
		}
		echo "</table>\n";
		echo "<p>Antal artiklar: <b>$countrow st</b></p>\n";

	}

	function getActivePacCheck($fi,$no) {
	
		$countrow = 0;
		$rowcolor = true;
		if ($fi) {
			$valuta = "EUR";
		} elseif ($no) {
			$valuta = "NOK";
		} else {
			$valuta = "SEK";
		}
	
		if ($fi) {
			$select  = "SELECT a.artnr, a.beskrivning, k.kategori, afi.utpris_fi as slutpris, a.m_product_id ";
			$select .= "FROM Artiklar a ";
			$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
			$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
			$select .= "JOIN Artiklar_fi afi ON a.artnr = afi.artnr_fi ";
			$select .= "WHERE a.IsSalesBundle = -1 AND a.ej_med = 0 AND afi.ej_med_fi = 0 AND a.utgangen = 0 AND NOT a.kategori_id IN (325,1000010,1000011,1000012) ";
			include ("std_instore_special_fi.php");
			$criteria = preg_replace("/Artiklar./", "a.", $criteria);
			$criteria = preg_replace("/Kategori./", "k.", $criteria);
			$select .= $criteria;
			$select .= "GROUP BY a.artnr ";
			$select .= "ORDER BY a.beskrivning, k.kategori ";
		} elseif ($no) {
			$select  = "SELECT a.artnr, a.beskrivning, k.kategori, a.utpris_no as slutpris, a.m_product_id ";
			$select .= "FROM Artiklar a ";
			$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
			$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
			$select .= "WHERE a.IsSalesBundle = -1 AND a.ej_med = 0 AND a.utgangen = 0 AND NOT a.kategori_id IN (1000010,1000011,1000012) ";
			include ("std_instore_special_no.php");
			$criteria = preg_replace("/Artiklar./", "a.", $criteria);
			$criteria = preg_replace("/Kategori./", "k.", $criteria);
			$select .= $criteria;
			$select .= "GROUP BY a.artnr ";
			$select .= "ORDER BY a.beskrivning, k.kategori ";
		} else {
			$select  = "SELECT a.artnr, a.beskrivning, k.kategori, a.utpris as slutpris, a.m_product_id ";
			$select .= "FROM Artiklar a ";
			$select .= "JOIN Kategori k ON a.kategori_id = k.kategori_id ";
			$select .= "JOIN Moms m ON a.momskod = m.moms_id ";
			$select .= "WHERE a.IsSalesBundle = -1 AND a.ej_med = 0 AND a.utgangen = 0 AND NOT a.kategori_id IN (1000010,1000011,1000012) ";
			$select .= "GROUP BY a.artnr ";
			$select .= "ORDER BY a.beskrivning, k.kategori ";
		}
	
		if ($no && $_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
	
		$res = mysqli_query($this->conn_my, $select);
	
		if (mysqli_num_rows($res) > 0) {

			echo "<h2>Paket som är billigare än huvudprodukten, bör kontrolleras...</b></h2>";
			echo "<table>";
			echo "<tr>";
			echo "<td width=\"200\" align=\"left\"><b>Kategori</b></td>";
			echo "<td width=\"150\" align=\"left\"><b>Artnr</b></td>";
			echo "<td width=\"600\" align=\"left\"><b>Beskrivning</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Utpris</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Ej paket</b></td>";
			echo "<td width=\"75\" align=\"center\"><b>Differens</b></td>";
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.53" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
				echo "<td width=\"75\" align=\"center\"></td>";
			}
			echo "</tr>";
			
			while ($row = mysqli_fetch_array($res)) {
				
			extract($row);
				
			if ($this->displayNotPacPrice($artnr,$fi,$no) > $slutpris) {
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
					$this->sendMess_v1();
					break;
				}
				if ($rowcolor == true) {
					$backcolor = "alt_firstrow";
				} else {
					$backcolor = "alt_secondrow";
				}
				if ($fi) {
					$linc = "http://www.cyberphoto.fi/info.php?article=";
				} elseif ($no) {
					$linc = "http://www.cyberphoto.no/info.php?article=";
				} else {
					$linc = "http://www.cyberphoto.se/info.php?article=";
				}
				echo "<tr>";
				echo "<td class=\"$backcolor\" align=\"left\">$kategori</td>";
				echo "<td class=\"$backcolor\" align=\"left\">$artnr</td>";
				echo "<td class=\"$backcolor\" align=\"left\"><a target=\"_blank\" href=\"$linc$artnr\">$beskrivning</a></td>";
				echo "<td class=\"$backcolor\" align=\"right\">$slutpris " . $valuta . "</td>";
				echo "<td class=\"$backcolor\" align=\"right\">" . round($this->displayNotPacPrice($artnr,$fi,$no)) . " " . $valuta . "</td>";
				echo "<td class=\"$backcolor\" align=\"right\">" . round($slutpris - $this->displayNotPacPrice($artnr,$fi,$no)) . " " . $valuta . "</td>";
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.53" || $_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
							// echo "<td class=\"$backcolor\" align=\"right\">$lagersaldo st</td>";
								if ($fi) {
								// echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, 'http://www.cyberphoto.no/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id');\">Åtgärda</a></td>";
									echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id&force_lang=fi');\">Åtgärda</a></td>";
								} elseif ($no) {
								// echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, 'http://www.cyberphoto.no/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id');\">Åtgärda</a></td>";
									echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id&force_lang=no');\">Åtgärda</a></td>";
								} else {
									echo "<td class=\"align_center\"><a href=\"javascript:winPopupCenter(900, 800, '/order/product_update.php?artnr=$artnr&m_product_id=$m_product_id&force_lang=sv');\">Åtgärda</a></td>";
									}
									}
									echo "</tr>";
										
												$countrow++;
												if ($rowcolor == true) {
												$rowcolor = false;
												} else {
													$rowcolor = true;
												}
		
				}
	
			}
	
			if ($countrow == 0) {
				echo "<tr>";
				echo "<td colspan=\"7\"><font color=\"#00D900\"><b><i>Detta är lysande, alla paket är uppdaterade!</i></b></font></td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "<p>Antal artiklar: <b>$countrow st</b></p>";
		}
		
	
	}

	function displayNotPacPrice($pac_artnr,$fi,$no) {
		
		$artnr = substr_replace($pac_artnr, "", -3);
	
		$select  = "SELECT a.utpris, afi.utpris_fi, a.utpris_no ";
		$select .= "FROM Artiklar a ";
		$select .= "JOIN Artiklar_fi afi ON a.artnr = afi.artnr_fi ";
		$select .= "WHERE a.artnr = '$artnr' ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
	
		$res = mysqli_query($this->conn_my, $select);
	
		if (mysqli_num_rows($res) > 0) {
				
			while ($row = mysqli_fetch_array($res)):
				
			extract($row);
				
			if ($fi) {
				$utpris = $utpris_fi;
			} elseif ($no) {
				$utpris = $utpris_no;
			} else {
				$utpris = $utpris;
			}
	
			endwhile;
	
		} else {
				
			return 0; // detta skall inte behöva inträffa
				
		}
	
		return $utpris;
	
	}
	
	
}

?>

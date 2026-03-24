<?php
require_once("CCheckIpNumber.php");

Class CPresentKort {
	var $conn_ms; 
	var $conn_my; 
	var $conn_fi;
    var $conn_my2;
	var $GiftCardOk;
    var $conn_master;
    var $GiftCardUsed;

	function __construct() {
		global $fi;
			
		$this->conn_my = Db::getConnection();
//		$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
//		@mssql_select_db ("cyberphoto", $this->conn_ms);
		$this->conn_my2 = Db::getConnection(true);
		$this->conn_fi = $this->conn_ms;
		$this->conn_master = Db::getConnection(true);
		$this->GiftCardOk = false;
		$this->GiftCardUsed = false;
	}

	function checkGiftCard($old_presentkort,$goodsvalueMoms) {
		global $GiftCardOk;
		$_SESSION['old_presenkort'] = $old_presentkort;
		$client_ip = $_SERVER['REMOTE_ADDR'];
		
		if (strlen($old_presentkort) <> 16 || !preg_match ("/^[0-9]/", $old_presentkort )) {
		// if (strlen($old_presentkort) <> 16 || !ctype_digit($old_presentkort)) {
		// if (strlen($old_presentkort) <> 16 || !is_int($old_presentkort)) {
		// if (strlen($old_presentkort) <> 16 || !is_numeric($old_presentkort)) {
		
			sleep(5); // om användaren anger fel format så vilar vi sidan i 5 sekunder

			// $insert = "INSERT INTO log_web (dat, webpage, comment) values (getdate(),'presentkort','kortnr: " . $old_presentkort . " IP: " . $client_ip ."')";
			$insert = "INSERT INTO logWeb (logDate, logPage, logComment, logIP) values (now(), 'presentkort', 'kortnr: " . $old_presentkort . "', '" . $client_ip ."')";
			mysqli_query($this->conn_my2, $insert);

			echo "<font color=\"red\">Ogiltigt presentkortsnummer!</font>";
		
		} else {
			
			$select = "SELECT cardCode ";
			$select .= "FROM cyberorder.Presentkort ";
			// $select .= "WHERE cardCode = '" . $old_presentkort . "' AND DATEDIFF(dd, datePurchased, getDate()) < 740 AND active = -1 AND cancelled = 0 ";
			$select .= "WHERE cardCode = '" . $old_presentkort . "' AND active = -1 AND cancelled = 0 AND expired_or_used = 0 ";
			
			$res = mysqli_query($this->conn_master, $select);
					if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
						echo $select;
						echo "<br>: " . $this->conn_master;
						exit;
						
					}
			
			if (mysqli_num_rows($res) > 0) {
			
				$this->GiftCardOk = true; // sätt status ok
				$this->getGiftCard($old_presentkort,$goodsvalueMoms); // om presentkortet är gilltigt. visa information om detta
			
			} else {
			
				sleep(5); // om användaren anger fel presentkortsnummer så vilar vi sidan i 5 sekunder
				
							$insert = "INSERT INTO logWeb (logDate, logPage, logComment, logIP) values (now(),'presentkort','kortnr: " . $old_presentkort . "', '" . $client_ip ."')";

				@mysqli_query($this->conn_my2, $insert);
				
				echo "<font color=\"red\">Ogiltigt presentkortsnummer!</font>";
			
			}
		
		}

	}

	function getGiftCard($old_presentkort,$goodsvalueMoms,$showlines=true) {
		global $new_giftcardrebate, $old_giftcardrebate;
		
			// echo $old_giftcardrebate;
			// echo $goodsvalueMoms;
			$select = "SELECT totalSum, usedSum, reservedSum, cardCode FROM cyberorder.Presentkort WHERE cardCode = '" . $old_presentkort . "'";
			$res = mysqli_query($this->conn_master, $select);
			$rows = mysqli_fetch_object($res);
				
				$disp_totalsum = $rows->totalSum;
				$disp_used = ($rows->usedSum+$rows->reservedSum);
				$disp_reserved = $rows->reservedSum;
				// $disp_left = ($disp_totalsum -($disp_used+$disp_reserved));
				$disp_left = ($disp_totalsum - $disp_used);
				// echo $disp_left;
				// echo $old_giftcardrebate;
				/*
				if ($disp_left >= $goodsvalueMoms) {
					$old_giftcardrebate = round($goodsvalueMoms);
				} else {
					$old_giftcardrebate = round($disp_left);
				}
				*/
				if ($new_giftcardrebate == 0) {
					if ($disp_left > $goodsvalueMoms) {
						$old_giftcardrebate = $goodsvalueMoms;
					} else {
						$old_giftcardrebate = $disp_left;
					}
				}
				
				// om kunden fyller i felaktikt belopp så åtgärdar vi detta och ger meddelande
				if ($old_giftcardrebate != "") {
				// if ($old_giftcardrebate > 0) {
				
					// if (!ctype_digit($old_giftcardrebateown)) {
					if (!is_numeric($old_giftcardrebate)) {
						$wrongmess .= "<p>Beloppet måste vara i sifrror, vänligen korrigera detta!</p>";
					}
					if ($old_giftcardrebate > $goodsvalueMoms) {
						$wrongmess .= "<p>Beloppet kan inte vara högre än vad som finns i varukorgen!</p>";
						$old_giftcardrebate = $goodsvalueMoms;
						// $old_giftcardrebate = 0;
					}
					if ($old_giftcardrebate < 0) {
						$wrongmess .= "<p>Beloppet kan inte vara ett negativt belopp!</p>";
						$old_giftcardrebate = 0;
						// $old_giftcardrebate = 0;
					}
					if ($old_giftcardrebate > $disp_left) {
						$wrongmess .= "<p>Beloppet kan inte vara högre än vad som finns tillgängligt på presentkortet.</p>";
						// $old_giftcardrebateown = $disp_left;
						$old_giftcardrebate = $disp_left;
					}
					if ($disp_left == 0) {
						$wrongmess .= "<p>Inga pengar återstår på detta presentkort.</p>";
						$this->GiftCardUsed = true;
					}
					if ($old_giftcardrebate == 0) {
						// $old_giftcardrebate = $goodsvalueMoms;
					}
					
				
				}
			
				echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
				echo "\t<tr>\n";
				echo "\t\t<td colspan=\"3\"><hr noshade color=\"#C0C0C0\" size=\"1\" width=\"98%\"></td>\n";
				echo "\t</tr>\n";
				echo "\t<tr>\n";
				echo "\t\t<td width=\"75\" align=\"left\">Totalsumma:</td>\n";
				echo "\t\t<td width=\"75\" align=\"right\">" . number_format($disp_totalsum, 0, ',', ' ') . " kr</td>\n";
				echo "\t\t<td align=\"left\">&nbsp;</td>\n";
				echo "\t</tr>\n";
				echo "\t<tr>\n";
				echo "\t\t<td width=\"75\" align=\"left\">Utnyttjat:</td>\n";
				echo "\t\t<td width=\"75\" align=\"right\">" . number_format($disp_used, 0, ',', ' ') . " kr</td>\n";
				echo "\t\t<td align=\"left\">&nbsp;</td>\n";
				echo "\t</tr>\n";
				echo "\t<tr>\n";
				echo "\t\t<td width=\"75\" align=\"left\">Återstår:</td>\n";
				echo "\t\t<td width=\"75\" align=\"right\"><b>" . number_format($disp_left, 0, ',', ' ') . " kr</b></td>\n";
				echo "\t\t<td align=\"left\">&nbsp;</td>\n";
				echo "\t</tr>\n";
				echo "\t<tr>\n";
				echo "\t\t<td colspan=\"3\"><hr noshade color=\"#C0C0C0\" size=\"1\" width=\"98%\"></td>\n";
				echo "\t</tr>\n";
				if ($wrongmess) {
					echo "\t<tr>\n";
					echo "\t\t<td colspan=\"3\"><font color=\"red\">$wrongmess</font></td>\n";
					echo "\t</tr>\n";
				}
				if (!$this->GiftCardUsed) {
					echo "\t<tr>\n";
					echo "\t\t<td colspan=\"2\" align=\"left\">Dra av för detta köp:</td>\n";
					echo "\t\t<td align=\"left\"><input type=\"text\" name=\"new_giftcardrebate\" size=\"5\" style=\"font-size: 12px\" value=\"" . ereg_replace ("[\]", "", $old_giftcardrebate) . "\">&nbsp;kr</td>\n";
					echo "\t</tr>\n";
					echo "\t\t<td colspan=\"3\"><input type=\"submit\" value=\"Uppdatera\" name=\"preschange\" style=\"font-family: Verdana; font-size: 10px\"></td>\n";
					echo "\t</tr>\n";
				}
				echo "</table>\n";
		$_SESSION['old_giftcardrebate'] = $old_giftcardrebate;
		
	}

}
?>

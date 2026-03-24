<?php

//require_once("CConnect_ms.php");
require_once("CCookies.php");
require_once("connections.php");
session_start();
session_register("giftCard", "giftCardReceiver", "giftCardClass", "NoCardTrials");

if (!(ereg ("^grejor", $kundvagn))) {
	//$i = count($giftCard);

	//$j = 0;
	//while 
	$giftCard = null;

	$giftCardReceiver = null;
	//unset($giftCard);
	//unset($giftCardReceiver);
}

//$giftCard = null;
//unset($giftCard);
//echo count($giftCard) . "<br>";

function getLastgiftCardArticleNumber () {
	/**
	if (ereg ("(grejor:)(.*)", $kundvagn,$matches)) {
		# Split the number of items and article id s into a list
		$orderlista = $matches[2];
		$argument = split ("\|", $orderlista);
	} */
	global $kundvagn;
	$argument = splitBasketToArray($kundvagn);
	$j = 0;
	
	$n = count($argument);
	for ($i=0; ($i < $n);  $i+=2) {
		$arg = $argument[$i];        # Article id
		$count = $argument[$i+1];    # Keeps track of the number of the same article	
		if (ereg ("(presentkort)([0-9]+)", $arg,$matches)) {
			$orderlista[$j] = $matches[2];
			$j += 1;
		} 		
	}
	if (is_array($orderlista)) {
		array_multisort($orderlista, SORT_DESC);
		return $orderlista[0];
	}
	else {
		return -1;
	}
}

function storeCardInformation($cardSum, $receiver) {
	
	//$i = count($GLOBALS['giftCard']);
	$i = 1 + getLastgiftCardArticleNumber ();
	$GLOBALS['giftCard'][$i] = $cardSum;
	$GLOBALS['giftCardReceiver'][$i] = $receiver;
	//storeCookie('presentkort' . $i, 1);
	modifyBasket('presentkort' . $i, 1, false);
}
//echo "här: " . $giftCardReceiver[0];
//echo "och: " . count($giftCardReceiver);

function storeCookie($articleNo, $ant) { // används inte
	modifyBasket($articleNo, $ant, false);

	/**
	?>
	<script language="JavaScript">
		addItemsNoPop('<?php echo $articleNo; ?>');
	</script>	
	<?php
	*/
}

function viewGiftCardInBasket($i, $output) {
	global $giftCard, $utpris, $kundvagn, $goodsvalue, $goodsvalueMoms, $moms, $artnr, $count, $lagersaldo, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans, $betalsatt;
	if ($GLOBALS['giftCard'][$i] == "") {
		//TODO: gå till remove.php i /presentkort/ och 
		//modifyBasket('presentkort'.$i, 0, false);
		
	?>			
		<script language="JavaScript">
			//document.location = document.location + "?removeArtnr=presentkort<% echo $i; %>";
			document.location = "viewBasket2.php?removeArtnr=presentkort<% echo $i; %>";
		</script>	
	<?php
		return false;
	}
		
	$utpris = $GLOBALS['giftCard'][$i]; 

	//$GLOBALS[utpris] = $GLOBALS['giftCard'][$i]; 
	//$utpris = $giftCard[$i];
	//echo "här: " .$giftCard[0];
	if ($output) {
?>
		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Presentkort (till <?php echo $GLOBALS['giftCardReceiver'][$i]; ?>)</font></td>
		  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1"><?php echo $count; ?></font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<?php		printf("%10.0f SEK", $utpris*$count);  ?>

		</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<?php		printf("%10.0f SEK", $utpris*$count);  ?>
		</font></td>
		<td bgcolor="#ECECE6" align="left">&nbsp;&nbsp;
		</font></td>
		<td bgcolor="#ECECE6" nowrap>
		<A HREF="javascript:modifyItemsInBasket('<?php echo $artnr; ?>', '1')">
		<font face="Verdana, Arial" size="1">
		<% if ($fi == 'yes'): %>
		<img src="antal_fi.gif" border=0 alt="muuta lukumäärä"></font></A>
		<% else: %>
		<img src="antal.gif" border=0 alt="klicka här för att ändra antal"></font></A>
		<% endif; %>
		</td>
		</tr>
		
<?php	}
	return true;
	
}

function verifyInformation ($cardSum) {
	if (!(isnumeric($cardSum))) {
		return false;
		}
	else {
		if ($cardSum >= 100 && $cardSum <= 30000) 
			return true;
		else
			return false;
	}
}
	

function newGiftCard($ordernr, $totalSum, $datePurchased, $artnr, $receiver) {
	global $REMOTE_ADDR;
	    
    $pass = gen_pass(16);
    //Kontrollera att ingen fått numret tidigare. Inte sannolikt men det kan ju faktiskt hända.
    $s = "SELECT cardCode FROM Presentkort WHERE cardCode = '" . $pass . "'";
    $res = mssql_query($s);
    if (mssql_num_rows($res) > 0) {
    	//echo "oh no!";
    	return;
    }
    $pass2 = gen_pass(16);
    $s2 = "INSERT INTO Presentkort (cardCode, totalSum, datePurchased, ordernr, ip, artnr, receiver, active, [key]) values ('" . $pass . "', " . $totalSum . ",'" . $datePurchased . "'," . $ordernr . ", '" . $REMOTE_ADDR . "', '" . $artnr . "', '" . $receiver . "', -1, '" . $pass2 . "')";
    $updt = $s2;
    mssql_query($updt);
    //echo $updt;

}
function gen_pass ($pass_len)  { 

	$nps = ""; 
	$c = "";

	// Seed the random number generator
	mt_srand ((double) microtime() * 1000000); 
	while (strlen($nps)<$pass_len) { 

		// Ge $c ett värde från slumpmässigt valt ASCII värde

		$c = chr(mt_rand (48,57)); 

		// Lägg till på $nps om det är i rätt format
		if (eregi("^[0-9]$", $c)) 
		$nps = $nps.$c; 
	}
		return ($nps);
}
Class CGiftCard {	//		       true/false
	var $regGiftCards; // [giftcardNumber][valid][active][$sumLeftTmp][$cardName]
	
	function __construct() {
		
	}
	
	function checkGiftCard($cardNo, $day, $month, $year) {
		//sleep(5);
		setlocale(LC_ALL, "sv_SE"); // har ingen funktion. Fungerar inte som vanligt. 
		global $conn_ms;
		$cardNo = ereg_replace("[^0-9]", "", $cardNo);
		
		
		
		if ( strlen($cardNo) != 16 || !is_numeric($day) || !is_numeric($month) || !is_numeric($year)  ) {
			return false;			
		} else {
		}
		
		$cardDate = mktime(0,0,0, $month, $day, $year);
		$cardDate1 = $cardDate - 86400;
		$cardDate2 = $cardDate + 86400;

		//$sel = "SELECT * FROM Presentkort WHERE datePurchased > '" . $cardDate1 . "' AND datePurchased < '" . $cardDate2 . "' AND cardCode = '" . $cardNo . "' and active = 0 and cancelled = 0";
		$sel = "SELECT * FROM Presentkort WHERE cardCode = '" . $cardNo . "'";

		$res = mssql_query($sel);
		if ( mssql_num_rows($res) > 0)  {
		
			$row = mssql_fetch_object($res);
			
			$datePurchased = strtotime($row->datePurchased);
			$formatDatePurchased = date("Y-m-d", $datePurchased);
			//echo "<p>" . date("Y-m-d", $datePurchased) . "<p>";
			//echo "<p>" . $row->cardCode;
			if (!is_array($this->regGiftCards)) 
				$this->regGiftCards = array();
			$arr = array();
			$arr[] = $cardNo;
			if ( $datePurchased > $cardDate1  && $datePurchased < $cardDate2 && $row->cancelled == 0) {
				$arr[] = true;
				//echo "<p> valid";
			} else {
				$arr[] = false;
			}
			if ( $row->active == -1) {
				$arr[] = true;
			} else {
				$arr[] = false;
				//echo "ej aktivt";
			}
			
			$arr[] = $row->totalSum - $row->usedSum - $row->reservedSum;
			$arr[] = $row->receiver;
			
			$this->regGiftCards[] = $arr;
			//print_r($this->regGiftCards);
			return $arr;
		} else {
			//echo "inget resultat";
			return false;
		}
		
		
	}
	
	function printGiftCards() {
		if (!is_array($this->regGiftCards))
			return false;
		if (count($this->regGiftCards) == 0)
			return false;
		$ret = "<table border=1><tr><td>Kortnummer</td><td>återstår på kortet</td><td>Namn på kortet</td><td>giltigt</td><td>aktivt</td>";
		foreach ($this->regGiftCards as $regGiftCard) {
			$ret .= "<tr><td>" . $regGiftCard[0] . "</td>";
			$ret .= "<td>" . $regGiftCard[3] . "</td>";
			$ret .= "<td>" . $regGiftCard[4] . "</td>";
			$ret .= "<td>";
			if ($regGiftCard[1])
				$ret .= "ja";
			else
				$ret .= "utgånget";
			$ret .= "</td>";
			$ret .= "<td>";
			if ($regGiftCard[2])
				$ret .= "ja";
			else
				$ret .= "ej aktivt";
			$ret .= "</td></tr>";
		
		
		}
		return $ret;	
	}

	/**
	 * search for a value ($needle) in a two-dimensioned array ($haystack) in specified column
	 * returns false if not found, returns the found array on success. 
	 *
	 * @param variable $needle
	 * @param array $haystack
	 * @param integer $column
	 * @return false if not found array on success
	 */
	function search_array_n($needle, $haystack, $column) {
		if (count($haystack) == 0)
			return false;
		$i = 0;
		foreach ($haystack as $straw) {
			if ($straw[$column] == $needle) {
				return $straw;
			}
			$i += 1;
		}
	}	
	
	
	
}
?>
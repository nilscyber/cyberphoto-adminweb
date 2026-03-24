<?php

//require_once("CConnect_ms.php");
require_once("CCookies.php");


//session_register("giftCard", "giftCardReceiver");
session_start();
$giftCard = &$_SESSION['giftCard'];
$giftCardReceiver = &$_SESSION['giftCardReceiver'];

include_once("connections.php");

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
	$_SESSION['giftCard'][$i] = $cardSum;
	$_SESSION['giftCardReceiver'][$i] = $receiver;
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
	global $giftCard, $utpris, $kundvagn, $goodsvalue, $goodsvalueMoms, $moms, $artnr, $count, $lagersaldo, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans, $betalsatt, $fi, $sv, $bask;
		//$bask->obsafe_print_r($GLOBALS['giftCard'], false, true);
		//$bask->obsafe_print_r(get_defined_vars(), false, true);
	if ($_SESSION['giftCard'][$i] == "") {
		//print_r($GLOBALS['giftCard']);
		//$bask->obsafe_print_r($_SESSION['giftCard'], false, true);
		//$bask->obsafe_print_r(get_defined_vars(), false, true);
		//
		exit;
		//TODO: gå till remove.php i /presentkort/ och 
		//modifyBasket('presentkort'.$i, 0, false);
		
	?>			
		<script language="JavaScript">
			//document.location = document.location + "?removeArtnr=presentkort<?php echo $i; ?>";
			document.location = "viewBasket2.php?removeArtnr=presentkort<?php echo $i; ?>";
		</script>	
	<?php
		return false;
	}
		
	$utpris = $_SESSION['giftCard'][$i]; 

	//$GLOBALS[utpris] = $GLOBALS['giftCard'][$i]; 
	//$utpris = $giftCard[$i];
	//echo "här: " .$giftCard[0];
	if ($output) {
?>
		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">Presentkort (<?php echo $_SESSION['giftCardReceiver'][$i]; ?>)</font></td>
		  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1"><?php echo $count; ?></font></td>
		<td bgcolor="#ECECE6" align="center">
		<a href="javascript:modifyItemsInBasket('<?php echo $artnr; ?>', '<?php echo $fi; ?>', '<?php echo $sv; ?>')">
		<font face="Verdana, Arial" size="1">
		<?php if ($fi == 'yes'): ?>
		Muuta lukumäärä</a></font>
		<?php else: ?>
		ändra antal</a></font>
		<?php endif; ?>
		</td>
		  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1">&nbsp;</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<?php		echo number_format($utpris * $count, 0, ',', ' ') . " kr";  ?>
		</font></td>
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
	global $REMOTE_ADDR, $conn_master;
        
    $pass = gen_pass(16);
    //Kontrollera att ingen fått numret tidigare. Inte sannolikt men det kan ju faktiskt hända.
    $s = "SELECT cardCode FROM cyberorder.Presentkort WHERE cardCode = '" . $pass . "'";
    $res = mysqli_query($conn_master, $s);
    if (mysqli_num_rows($res) > 0) {
    	echo "oh no!";
    }
    $pass2 = gen_pass(16);
    $s2 = "INSERT INTO cyberorder.Presentkort (cardCode, totalSum, datePurchased, ordernr, ip, artnr, receiver, active, keyss) values ('" . $pass . "', " . $totalSum . ",'" . $datePurchased . "'," . $ordernr . ", '" . $REMOTE_ADDR . "', '" . $artnr . "', '" . $receiver . "', -1, '" . $pass2 . "')";
    if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
        echo $s2;
        exit;
    }    
    $updt = $s2;
    mysqli_query($conn_master, $updt);
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

?>
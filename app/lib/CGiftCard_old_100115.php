<?php

//require_once("CConnect_ms.php");
require_once("CCookies.php");


session_register("giftCard", "giftCardReceiver");

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
    	echo "oh no!";
    }
    $pass2 = gen_pass(16);
    $s2 = "INSERT INTO Presentkort (cardCode, totalSum, datePurchased, ordernr, ip, artnr, receiver, active, [key]) values ('" . $pass . "', " . $totalSum . ",'" . $datePurchased . "'," . $ordernr . ", '" . $REMOTE_ADDR . "', '" . $artnr . "', '" . $receiver . "', 0, '" . $pass2 . "')";
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

?>
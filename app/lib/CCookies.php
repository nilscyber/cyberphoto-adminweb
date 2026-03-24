<?php

//echo  $kundvagn;
//exit;

//modifyBasket('cf256sd', 2, false);
//echo $kundvagn . "<p>";

function splitBasketToArray($kundvagn) {
	# Get the cookie kundvagn
	$answers = $kundvagn;

	if (ereg ("(grejor:)(.*)", $answers,$matches)) {
		# Split the number of items and article id s into a list
		$orderlista = $matches[2];
		$argument = split ("\|", $orderlista);
	}
	

	return $argument;
}
function modifyBasket($artnr, $count, $add) {
	global $kundvagn, $fi, $no; 

	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo "ja i modifyBasket funktionen";
		exit;
	}

	$argument = splitBasketToArray($kundvagn);	

	$kundvagnNew = modifyBasketString($artnr, $count, $add, $argument);
	if ( $kundvagnNew == "" ) {
		if ($fi) {
			setcookie ("kundvagn", "", time() - 3600, "/", ".cyberphoto.fi");
		} elseif ($no) {
			setcookie ("kundvagn", "", time() - 3600, "/", ".cyberphoto.no");
		} else {
			setcookie ("kundvagn", "", time() - 3600, "/", ".cyberphoto.se");
		}
	} else {
		if ($fi) {
			setcookie ("kundvagn", trim($kundvagnNew), time() + 2 * 31104000, "/", ".cyberphoto.fi");
		} elseif ($no) {
			setcookie ("kundvagn", trim($kundvagnNew), time() + 2 * 31104000, "/", ".cyberphoto.no");
		} else {
			setcookie ("kundvagn", trim($kundvagnNew), time() + 2 * 31104000, "/", ".cyberphoto.se");
		}
	}	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $kundvagnNew;;
	}
	//echo $kundvagnNew;
}	

function modifyBasketString($artnr, $count, $add, $argument) {
	//global $argument;
	$n = count($argument);	
		
	for ($i=0; ($i < $n);  $i+=2) {
		$arg = $argument[$i];        # Article id
		$count2 = $argument[$i+1];    # Keeps track of the number of the same article	
		
		if ($arg == $artnr) {
			if ($count > 0) {
				if ($add) { 
					$kundvagn .= $arg . "|" . ($count + $count2) . "|";
					//echo "<br>" . $kundvagn . "<br>";
					//$count = $count;
				}
				else {
					$kundvagn .= $arg . "|" . $count . "|";
					//$count += $count2;
				}
			}
			$exists = true;
		}
		else {
			$kundvagn .= $arg . "|" . $count2 . "|";
			//$exists = false;
		}
	}
	//echo $exists;
	if (!$exists && $count > 0) {
		$kundvagn .= $artnr . "|" . $count . "|";
		//echo "tjosan";
	}
	
	$kundvagn = "grejor:".$kundvagn;
	if ( !(ereg("\|", $kundvagn))) {
		return "";
	} else {
		$kundvagn = substr($kundvagn, 0, strlen($kundvagn) - 1);		
		return $kundvagn;	
	}	
}

?>
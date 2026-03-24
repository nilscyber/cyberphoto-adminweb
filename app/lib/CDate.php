<?php

Class CDate {

function getPickupTime() {

	$actualDay = date("Y-m-d");
	$weekDay = date("w");
	$stdPickUp = "17:00";
	
	if ($weekDay == 1) {
	
		if ((strtotime(date("Y-m-d $stdPickUp")) - time()) > 0) {
		
			$showdate = date("Y-m-d $stdPickUp");

			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";
			
				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

			
		} else {
			
			$showdate = date("Y-m-d $stdPickUp",strtotime("+1 days"));
			
			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";

				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

		}
	
	} elseif ($weekDay == 2) {
	
		if ((strtotime(date("Y-m-d $stdPickUp")) - time()) > 0) {
		
			$showdate = date("Y-m-d $stdPickUp");

			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";
			
				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

			
		} else {
			
			$showdate = date("Y-m-d $stdPickUp",strtotime("+1 days"));
			
			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";

				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

		}
	
	} elseif ($weekDay == 3) {
	
		if ((strtotime(date("Y-m-d $stdPickUp")) - time()) > 0) {
		
			$showdate = date("Y-m-d $stdPickUp");

			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";
			
				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

			
		} else {
			
			$showdate = date("Y-m-d $stdPickUp",strtotime("+1 days"));
			
			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";

				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

		}
	
	} elseif ($weekDay == 4) {
	
		if ((strtotime(date("Y-m-d $stdPickUp")) - time()) > 0) {
		
			$showdate = date("Y-m-d $stdPickUp");

			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";
			
				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

			
		} else {
			
			$showdate = date("Y-m-d $stdPickUp",strtotime("+1 days"));
			
			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";

				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

		}
	
	} elseif ($weekDay == 5) {
	
		if ((strtotime(date("Y-m-d $stdPickUp")) - time()) > 0) {
		
			$showdate = date("Y-m-d $stdPickUp");

			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";
			
				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

			
		} else {
			
			$showdate = date("Y-m-d $stdPickUp",strtotime("+1 days"));
			
			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";

				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

		}
	
	} elseif ($weekDay == 6) {
	
			$showdate = date("Y-m-d $stdPickUp",strtotime("+2 days"));
			
			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";

				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

	} elseif ($weekDay == 7) {
			
			$showdate = date("Y-m-d $stdPickUp",strtotime("+1 days"));
			
			echo "Nästa utleverans från vårt lager: <b>" . $showdate . "</b>";

				$now = time();
				$timeto = strtotime($showdate);
				$diff = $timeto - $now;
				$sek = $diff % 60;
				$min = ($diff / 60) % 60;
				$hour = ($diff / 3600);
				$hour = floor($hour);
				echo "&nbsp;&nbsp;Tid kvar: <b>" . $hour." h ".$min." min</b>";

	}
}

function getBlockDay() {
	
	$blockdate = date("Y-m-d");
	
	// Blockade datum
	if ($blockdate == "2008-11-12") {
		$block = true;
	} else {
		$block = false;
	}
	
	
	echo "<br>" . $block;
	echo "<br>" . $blockdate;
	echo "<br>" . date('Y-m-d', strtotime ('+3 day'));
}


}
?>

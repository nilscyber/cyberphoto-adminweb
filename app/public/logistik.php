<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Logistikflöden</h1>";

?>

	<div><?php echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?show=printed\">"; ?>Se detaljer för utskrivna plocksedlar</a></div>
	<div class="top5"><?php echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?show=notprinted\">"; ?>Se detaljer för EJ utskrivna plocksedlar</a></div>
	<div class="top5"><?php echo "<a href=\"" . $_SERVER['PHP_SELF'] . "\">"; ?>Se antal packade per person</a></div>
	<!--
	<div><?php echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?show=overall\">"; ?>Se detaljer för alla avgående ordrar</a></div>
	-->
	<?php
	if ($show == "printed") {
		$turnover->printPrintedOrdersFromAD();
	} elseif ($show == "notprinted") {
		$turnover->printNotPrintedOrdersFromAD();
	/*
	if ($show == "sent") {
		$turnover->printSentOrdersFromAD();
	}
	if ($show == "lagershop") {
		$turnover->printLagershopOrdersFromAD();
	}
	if ($show == "overall") {
		$turnover->printOverallDeliveriesFromAD();
	}
	*/
	} else {
		?>
		<p></p>
		<div>
		<form name="sampleform" method="POST">
		<span class="abbrubrik">Annat datum:</span><br>
		<input type="text" name="firstinput" size=12 value="<?php echo $dagensdatum; ?>" style="font-family: Verdana; font-size: 10px"> <span class="abbrubrik"><a href="javascript:showCal('Calendar1')">Välj datum</a></span> <?php if ($ref_dagensdatum != $dagensdatum) { ?><span class="abbrubrik"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=picked"> Idag</a></span><?php } ?>&nbsp;
		<br>
		<hr noshade color="#C0C0C0" align="left" width="150" size="1">
		<input type="submit" value="Rapport" style="font-family: Verdana; font-size: 10px">
		</form>
		</div>
		<?php
		if ($datum != "") {
			$dateFrom = date("Y-m-d", strtotime ($datum) ) . " 00:00:00";
			$dateTo = date("Y-m-d", strtotime ($datum) ) . " 24:00:00";
				// echo $dateFrom;
		} elseif ($firstinput != "") {
			$dateFrom = date("Y-m-d", strtotime ($firstinput) ) . " 00:00:00";
			$dateTo = date("Y-m-d", strtotime ($firstinput) ) . " 24:00:00";

		} else {
			$dateFrom = date("Y-m-d", time()) . " 00:00:00";
			$dateTo = date("Y-m-d", time() ) . " 24:00:00";

		}
		$turnover->printPicked($dateFrom, $dateTo);
	}
		
	include_once("footer.php");
?>
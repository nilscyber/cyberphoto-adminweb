<?php
	spl_autoload_register(function ($class) {
		include $class . '.php';
	});
	session_start();
	// require_once ("CTurnOver.php");
	$turnover = new CTurnOver();
	// require_once ("CStoreStatus.php");
	$store = new CStoreStatus();
	// require_once("CMonitorArticles.php");
	$monitor = new CMonitorArticles();
	// require_once("CWebADInternSuplier.php");
	$adintern = new CWebADInternSuplier();
	// require_once ("CParcelCheck.php");
	$parcel = new CParcelCheck();
	// require_once ("CAllocated.php");
	$allocated = new CAllocated();
	// require_once("CCampaignCheck.php");
	$campaign = new CCampaignCheck();
	$cpto = new CCPto();
	$sales = new CSales();
	$filter = new CFilterIncoming();
	$temp = new CTemp();
	
	$infopanel = new CInfopanel(); // Extends CTurnOver
	$infopanel->setNotPrintedByCountry();
	$infopanel->setPrintedByCountry();
	
	#$printed_DriveOut = $infopanel->getOrdersFromADDriveOut();

	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		// echo $_SERVER['REMOTE_ADDR'];
		// $turnover->sendMessOrderTotal(46);
		// echo date("Y-m-d H:i:s", time());
		// echo date('N');
		// $parcel->getArticleNotPricedOnceADay();
		// echo $turnover->getNotPrintedOrdersFromADNew();
		// exit;
		// echo $cpto->getOutgoingOrders(true);
		// $sales->salesAddValue($cpto->getOutgoingOrders(true),2);
		// $sales_value = $cpto->getOutgoingOrders(true);
		// $sales->salesAddValue($sales_value,1);
		// $sales->salesAddValue($cpto->getOutgoingOrders(true),1);
		// echo date('N');
	}
	// echo round($adintern->displayStoreValueSimple(),0);
	// $store->addStockValue(round($adintern->displayStoreValueSimple(),0));
	// $store->addStockAllocatedValue(round($adintern->displayStoreDiffSimple(),0));
	// echo round($adintern->displayStoreDiffSimple(),0);
	// exit;
	$dagensdatum = date("Y-m-d H:i");
	// $totpack = ($turnover->getPrintedOrdersLight_v2() + $turnover->getNotPrintedOrdersViewLight());
	
	// $sales->salesAddValue($cpto->getOutgoingOrders(true),2);
	// $sales_value = $cpto->getOutgoingOrders(true);
	// echo $sales_value;

	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.111" || true) {

		if (date('i') == 02 || date('i') == 17 || date('i') == 32 || date('i') == 47) {
			if ($_SESSION['maila_check_incoming'] != 1) {
				$_SESSION['maila_check_incoming'] = 1;
				// $filter->getWordsToCheck();
			}
		} else {
			unset ($_SESSION['maila_check_incoming']);
		}
	
		if (date('H') == 23 && date('i') > 58 && $nomore == "yes") {
		
			if ($_SESSION['maila'] != 1) {
				$_SESSION['maila'] = 1;
				// $turnover->sendMessOrderTotal(46);
				// $turnover->sendMessOrderTotal(358);
			}

		} else {
			unset ($_SESSION['maila']);
		}

		// if (date('H') == 19 && date('i') < 02 && date('w') != 6 && date('w') != 0) {
		if (date('H') == 19 && date('i') < 02) {
		
			if ($_SESSION['maila2'] != 1) {
				$_SESSION['maila2'] = 1;
				// $store->addStockValue(round($adintern->displayStoreValueSimple(),0));
				// $sales->salesAddValue($cpto->getOutgoingOrders(true),0);
				// fimpar denna funktion så länge 2013-01-09
				// $store->addStockAllocatedValue(round($adintern->displayStoreDiffSimple(),0));
			}

		} else {
			unset ($_SESSION['maila2']);
		}
		
		/**
		// inaktiverat 
		
		if (date('N') != 6 && date('N') != 7) { // denna kör vi inte på lördag & söndag
			if ((date('H') == 11 && date('i') < 02) || (date('H') == 16 && date('i') < 02)) {
			
				if ($_SESSION['maila3'] != 1) {
					$_SESSION['maila3'] = 1;
					$parcel->getActivePacOnceADay();
					$parcel->getActivePacOnceADayFI();
					$parcel->getArticleNotPricedOnceADay();
					$parcel->getArticleNotPricedOnceADayFI();
				}

			} else {
				unset ($_SESSION['maila3']);
			}
		}
		*/
		if ((date('H') == 10 && date('i') < 02) || (date('H') == 15 && date('i') < 02)) {
		
			if ($_SESSION['maila4'] != 1) {
				$_SESSION['maila4'] = 1;
				// $allocated->getActualMonitorAllocated();
			}

		} else {
			unset ($_SESSION['maila4']);
		}
		
		if (date('i') == 00 || date('i') == 15 || date('i') == 30 || date('i') == 45) {
			// echo date('i');
			// $monitor->checkArticlesLevel(0); // kollar bevakning på lagersaldon
			// $monitor->checkArticlesLevel(1); // kollar bevakning på inpriser
			// $monitor->checkArticlesLevel(2); // kollar bevakning på utpriser
			// $monitor->checkArticlesLevel(3); // kollar bevakning på orderposter
			// $campaign->checkNotifyCampaign(); // kollar bevakning på kampanjer som håller på att utgå
			// $monitor->checkArticlesLevelCorrect();
		}
		
		if (!(date('N') == 6 || date('N') == 7 || date('G') < 8 || date('G') > 18 || (date('G') == 8 && date('i') < 16))) { // denna kör vi inte på lördag & söndag samt kvällar och nätter
			if (date('i') == 59 || date('i') == 14 || date('i') == 29 || date('i') == 44) { // vi kör bara var 15:e minut
					// $store->addStockValueOngoing(round($adintern->displayStoreValueSimple(),0));
					// $sales->salesAddValue($cpto->getOutgoingOrders(true),1);
			}
		}
	}

?>
<?php
if (!(date('N') == 6 || date('N') == 7 || date('G') < 7 || date('G') > 18 || (date('G') == 7 && date('i') < 06))) { // denna kör vi inte på lördag & söndag samt kvällar och nätter
// if (date('N') != 6) { // om jag behöver köra test

	$utskrivna = $infopanel->getOrdersFromADNew();
	$ej_utskrivna = $infopanel->getNotPrintedOrdersFromADNew();

	$utskrivna_instabox = $infopanel->getOrdersFromADInstabox();
	$ej_utskrivna_instabox = $infopanel->getNotPrintedInstabox();
	
	#$utskrivna = $infopanel->printedByCountry['se']+$infopanel->printedByCountry['no']+$infopanel->printedByCountry['fi'];
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		#$ej_utskrivna = $infopanel->getNotPrintedOrdersFromADNew();
		// $ej_utskrivna = $infopanel->notPrintedByCountry['se']+$infopanel->notPrintedByCountry['no']+$infopanel->notPrintedByCountry['fi'];
		// $ej_utskrivna = 0;
	} else {
		// $ej_utskrivna = $infopanel->getNotPrintedOrdersFromADNew();
		#$ej_utskrivna = $infopanel->notPrintedByCountry['se']+$infopanel->notPrintedByCountry['no']+$infopanel->notPrintedByCountry['fi'];
	}
	$totpack = $utskrivna + $ej_utskrivna;
	$totpack_instabox = $utskrivna_instabox + $ej_utskrivna_instabox;
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.44") {
		if ($turnover->getIfWorkingday() && $totpack > 65 && (date('H') == 15 && date('i') >= 30)) {
			if ($_SESSION['maila_about_help'] != 1) {
				$_SESSION['maila_about_help'] = 1;
				$turnover->mailAboutHelp($totpack);
			}
		} else {
			unset ($_SESSION['maila_about_help']);
		}
	}
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		// echo "hej:" . $ej_utskrivna_instabox;
	}
	
?>
<!DOCTYPE html>
	<html>

	<head>
	<title>Aktuellt - <?php echo $dagensdatum; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/infopanel.css<?php echo "?v=g" . date("ynjGi") . "\">\n"; ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<style type="text/css">
	body {
	<?php if ($bus) { ?>
	background-image:url('red_bak2_blink.gif');
	<?php } elseif (($utskrivna-$totpack_instabox) > 20 && (date('H') == 16 && date('i') >= 45)) { ?>
	background-image:url('red_bak2.gif');
	<?php } elseif (($utskrivna-$totpack_instabox) > 15 && (date('H') == 16 && date('i') >= 50)) { ?>
	background-image:url('red_bak2.gif');
	<?php } elseif (($utskrivna-$totpack_instabox) > 5 && (date('H') == 16 && date('i') >= 55)) { ?>
	background-image:url('red_bak2.gif');
	<?php } elseif (($totpack-$totpack_instabox) > 100 && (date('H') == 15 && date('i') >= 00)) { ?>
	background-image:url('red_bak2.gif');
	<?php } elseif (($totpack-$totpack_instabox) > 70 && (date('H') == 16 && date('i') >= 00)) { ?>
	background-image:url('red_bak2.gif');
	<?php } elseif (($totpack-$totpack_instabox) > 35 && (date('H') == 16 && date('i') >= 30)) { ?>
	background-image:url('red_bak2.gif');
	<?php } elseif (($utskrivna-$totpack_instabox) > 10 && (date('H') == 16 && date('i') >= 45)) { ?>
	background-image:url('orange_bak2.gif');
	<?php } elseif (($utskrivna-$totpack_instabox) > 7 && (date('H') == 16 && date('i') >= 50)) { ?>
	background-image:url('orange_bak2.gif');
	<?php } elseif (($utskrivna-$totpack_instabox) > 3 && (date('H') == 16 && date('i') >= 55)) { ?>
	background-image:url('orange_bak2.gif');
	<?php } elseif (($totpack-$totpack_instabox) > 100 && (date('H') == 11)) { ?>
	background-image:url('orange_bak2.gif');
	<?php } elseif (($totpack-$totpack_instabox) > 80 && (date('H') == 15 && date('i') >= 00)) { ?>
	background-image:url('orange_bak2.gif');
	<?php } elseif (($totpack-$totpack_instabox) > 65 && (date('H') == 15 && date('i') >= 30)) { ?>
	background-image:url('orange_bak2.gif');
	<?php } elseif (($totpack-$totpack_instabox) > 50 && (date('H') == 16 && date('i') >= 00)) { ?>
	background-image:url('orange_bak2.gif');
	<?php } elseif (($totpack-$totpack_instabox) > 25 && (date('H') == 16 && date('i') >= 30)) { ?>
	background-image:url('orange_bak2.gif');
	<?php } else { ?>
	background-image:url('green_bak2.gif');
	<?php } ?>
	background-repeat:repeat-no;
	}
	.sidhuvuden {
		font-family: Verdana; 
		font-size: 28px; 
		color: #000000; 
		font-weight: bold;
		padding: 5px; 
		margin-bottom: -30px; 
	}
	.clear {
		clear: both;
	}
	.plugg_driveout {
		background: url('driveout.png') no-repeat top center;
		text-align: center;
		min-height: 350px;
		width: 350px;
	}
	.showpackbig { 
		font-family: Arial; 
		font-size: 300pt; 
		color:#000000; 
		margin-top: 0px; 
		text-align: center; 
	}
	.showprintbig { 
		font-family: Arial; 
		font-size: 300pt; 
		color:#595959; 
		margin-top: -90px; 
		text-align: center; 
	}
	.lightnen {
		color: #AEAEAE !important;
	}
	.flags {
		width: 1150px; 
		margin: -60px auto 0px auto;
		text-align: center;
	}
	.flagimage { 
		border: 1px solid #000000;
	}
	.flagnumber { 
		font-family: Arial; 
		font-size: 100pt; 
		color:#595959; 
		padding: 0px 30px 0px 15px; 
		text-align: center; 
	}
	.float_left {
		float: left;
	}
	.float_right {
		float: right;
	}
	.span_blue {
		color: blue;
	}
	.span_green {
		color: white;
	}
	</style>
	</head>

	<body>
	<div class="clear"></div>
	<?php /*if ($turnover->driveOutExists()) { ?>
		<div class="showpackbig"><?php echo $utskrivna; ?><img border="0" src="driveout.png"></div>
	<?php }*/ ?>
	<?php 
	
		#echo "<div class=\"flags\">" . $turnover->getNotPrintedOrdersInGroup($ej_utskrivna) . "</div>\n";	
		if ($ej_utskrivna > 0 && $_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo "<div class=\"\">" . $turnover->getNotPrintedOrdersInGroupOld() . "</div>\n";	
		}
		
	?>
	
	
	
	
<div class="container-fluid" >
	<div class="row" id="top-bar">
		<div class="col-md-6" id="clock"><?php echo $dagensdatum; ?></div>
		<div class="col-md-6"><div class="pull-right">Temperatur just nu: <?php echo $temp->showLastTemp(2); ?> &#8451;</div></div>
	</div>
	<div class="row" id="counters">
		<div class="col-md-2 col-md-offset-2">
		<?php
		function displayRandomPhotoArea() 
		{
			$imgs = glob('gif/*.{gif}', GLOB_BRACE);
			$image = $imgs[array_rand($imgs)];			
			echo "<img src=\"$image\" height=\"300\">";
		}

		displayRandomPhotoArea();
		?>		
		</div>
		<div class="col-md-2 col-md-offset-3" >
			<div class="big-number-instabox <?php echo ($utskrivna == 0) ? "black" : "blue-number-postnord" ?>"><?php echo $utskrivna; ?></div>
			<div class="big-number <?php echo ($ej_utskrivna == 0) ? "grey-number" : "blue-number-dark-postnord" ?>"><?php echo $ej_utskrivna; ?></div>
		</div>
		<div class="col-md-2">
			<div class="flagss">
				<div class="icon-container"><span class="glyphicon glyphicon-plane" aria-hidden="true"></div><?php echo $infopanel->getOrdersFromADExpress(); ?><br>
				<div class="icon-container"><img src="img/driveout.png"></div><?php echo $infopanel->getOrdersFromADDriveOut(); ?><br>
			</div>
			<div class="flagss grey-number">
				<div class="icon-container"><span class="glyphicon glyphicon-plane" aria-hidden="true"></div><?php echo $infopanel->getNotPrintedExpress(); ?><br>
				<div class="icon-container"><img src="img/driveout.png"></div><?php echo $infopanel->getNotPrintedDriveOut(); ?><br>
			</div>
		</div>
		<div class="col-md-2 col-md-offset-2">
		&nbsp;
		</div>
		<div class="col-md-2 col-md-offset-3" >
			<div class="big-number-instabox <?php echo ($utskrivna_instabox == 0) ? "black" : "red-number-instabox" ?>"><?php echo $utskrivna_instabox; ?></div>
			<div class="big-number-instabox <?php echo ($ej_utskrivna_instabox == 0) ? "grey-number" : "red-number-dark-instabox" ?>"><?php echo $ej_utskrivna_instabox; ?></div>
		</div>
		<div class="col-md-2">
			<div class="flagss">
				&nbsp;
			</div>
			<div class="flagss grey-number">
				<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ($totpack + $totpack_instabox); ?></div>
			</div>
		</div>
	</div>
	
	<div class="row" id="counters">
		<?php /*<div class="col-md-6"><SCRIPT src="js/axis.js"></script></div> */ ?>
		<div class="col-md-6 col-md-offset-6">
			<div class="info-text">
				<?php
				// Tidsstyrda påminnelser
				
				echo $infopanel->reminder(array(
								array('Driveout', '09:45', '10:00'),
								array('Driveout', '13:45', '14:00'),
							));
				?>
			</div>
		</div>
	</div>
</div>

	</body>

	</html>
<?php } else { ?>
	<html>

	<head>
	<title>Aktuellt - <?php echo $dagensdatum; ?></title>
	<style type="text/css">
	body {
		background-image:url('black_bak.gif');
		background-repeat:repeat-no;
	}
	</style>
	</head>

	<body>

	</body>
	</html>
<?php } ?>	

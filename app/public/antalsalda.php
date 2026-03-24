<?php
	// phpinfo();
	// exit;
	include 'Db.php';
	
	spl_autoload_register(function ($class) {
		include $class . '.php';
	});

	$adintern = new CWebADIntern();

?>
<html>

<head>
<link rel="shortcut icon" href="https://admin.cyberphoto.se/admin.ico">
<link rel="stylesheet" type="text/css" href="/css/pricelist.css" />
<link rel="stylesheet" type="text/css" href="/css/suplier.css" />
<meta http-equiv="Content-Language" content="sv">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Antalet sålda produkter för artikel nummer <?php echo $artnr; ?></title>
</head>

<body topmargin="0" leftmargin="0">
<?php if (!$show_salesbundle == "yes") { ?>

<div align="center">
  <center>
  <table border="0" cellpadding="5" cellspacing="0" width="100%">
    <tr>
      <td width="100%"><b><font face="Arial" size="3"><b>Antalet sålda produkter för artikel nummer <font color="#0000FF"><?php echo $artnr; ?></font></font></td>
    </tr>
    <tr>
      <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
    </tr>
    <tr>
      <td width="100%">
<table border="0" cellpadding="1" cellspacing="2" width="100%">
  <tr>
    <td align="center" width="30">&nbsp;</td>
    <td align="center" width="95"><b>1 v</b></td>
    <td align="center" width="95"><b>1 mån</b></td>
    <td align="center" width="95"><b>2 mån</b></td>
    <td align="center" width="95"><b>3 mån</b></td>
    <td align="center" width="95"><b>6 mån</b></td>
    <td align="center" width="95"><b>12 mån</b></td>
    <td align="center" width="95"><b>24 mån</b></td>
  </tr>

<?php

$setrowcolor = true;

$select  = "SELECT * FROM mostSoldArticles WHERE artnr = '" . $artnr . "'";	

if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
	echo $select;
}

$res = mysqli_query(Db::getConnection(), $select);

	if (mysqli_num_rows($res) > 0) {
	
		$showtrend = true;
		
		while ($row = mysqli_fetch_array($res)):
	
		extract($row);
		
		if ($setrowcolor) {
			$rowcolor = "#CCCCCC";
		} else {
			$rowcolor = "#E9E9E9";
		}
		
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td bgcolor='$rowcolor' align='center'>" . $antalVecka . " st</td>";
		echo "<td bgcolor='$rowcolor' align='center'>" . $antalManad1 . " st</td>";
		echo "<td bgcolor='$rowcolor' align='center'>" . $antalManad2 . " st</td>";
		echo "<td bgcolor='$rowcolor' align='center'>" . $analManad3 . " st</td>";
		echo "<td bgcolor='$rowcolor' align='center'>" . $antalManad6 . " st</td>";
		echo "<td bgcolor='$rowcolor' align='center'>" . $antalManad12 . " st</td>";
		echo "<td bgcolor='$rowcolor' align='center'>" . $antalmanad24 . " st</td>";
		echo "</tr>";

		if ($setrowcolor) {
			$setrowcolor = false;
		} else {
			$setrowcolor = true;
		}
		
		endwhile;

		echo "<tr>";
		echo "<td align='center' colspan='8'>&nbsp;</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align='center' colspan='8'>Antal sålda totalt: <b>" . $antal . " st</b></td>";
		echo "</tr>";

		
		$trendv = $antalVecka / 1;      // 1 vecka
		if ($antalManad1 == $antalVecka) {
			$trend1 = 0;
		} else {
			$trend1 = $antalManad1 / 4.345; // 1 månad  4.345 veckor
		}
		if ($antalManad2 == $antalManad1) {
			$trend2 = 0;
		} else {
			$trend2 = $antalManad2 / 8.69;
		}
		if ($analManad3 == $antalManad2) {
			$trend3 = 0;
		} else {
			$trend3 = $analManad3 / 13.04;
		}
		if ($antalManad6 == $analManad3) {
			$trend6 = 0;
		} else {
			$trend6 = $antalManad6 / 26.09;
		}
		if ($antalManad12 == $antalManad6) {
			$trend12 = 0;
		} else {
			$trend12 = $antalManad12 / 52.18;
		}
		if ($antalmanad24 == $antalManad12) {
			$trend24 = 0;
		} else {
			$trend24 = $antalmanad24 / 104.35;		
		}
		
		
	} else {
	
		$showtrend = false;
		
		echo "<tr>";
		echo "<td width='40'>&nbsp;</td>";
		echo "<td bgcolor='#CCCCCC' align='center'>0 st</td>";
		echo "<td bgcolor='#CCCCCC' align='center'>0 st</td>";
		echo "<td bgcolor='#CCCCCC' align='center'>0 st</td>";
		echo "<td bgcolor='#CCCCCC' align='center'>0 st</td>";
		echo "<td bgcolor='#CCCCCC' align='center'>0 st</td>";
		echo "<td bgcolor='#CCCCCC' align='center'>0 st</td>";
		echo "<td bgcolor='#CCCCCC' align='center'>0 st</td>";
		// echo "<td bgcolor='#CCCCCC' align='center'><b>0 st</b></td>";
		echo "</tr>";
	
	}
	
?>
</table>
      </td>
    </tr>
  </table>
  </center>
</div>



				<script type="text/javascript" src="https://www.google.com/jsapi"></script>
			<?php if ($showtrend) { ?>
				
				<script type="text/javascript">
				  google.load("visualization", "1", {packages:["linechart"]});
				  google.setOnLoadCallback(drawChart);
				  function drawChart() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Period');
					data.addColumn('number', 'Sålda per vecka');
					data.addRows(7);
					data.setValue(0, 0, '1 v');
					data.setValue(0, 1, <?php echo $trendv; ?>);
					data.setValue(1, 0, '1 mån');
					data.setValue(1, 1, <?php echo $trend1; ?>);
					data.setValue(2, 0, '2 mån');
					data.setValue(2, 1, <?php echo $trend2; ?>);
					data.setValue(3, 0, '3 mån');
					data.setValue(3, 1, <?php echo $trend3; ?>);
					data.setValue(4, 0, '6 mån');
					data.setValue(4, 1, <?php echo $trend6; ?>);
					data.setValue(5, 0, '12 mån');
					data.setValue(5, 1, <?php echo $trend12; ?>);
					data.setValue(6, 0, '24 mån');
					data.setValue(6, 1, <?php echo $trend24; ?>);

					var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
					chart.draw(data, {titleY: 'Per vecka', smoothLine: true, width: 970, height: 240, legend: 'none', title: 'Säljtrend för produkten'});
					// chart.draw(data, {titleY: 'Per dag', smoothLine: true, width: 970, height: 240, legend: 'none', title: 'Säljtrend för produkten'});
				  }
				</script>

				<div align="center" id="chart_div"></div>

			<?php } ?>

			<!-- prices -->



			<?php

			$setrowcolor = true;

			$select  = "select 
						op.utpris as utpris, o.inkommet as inkommet   

						from cyberorder.Orderposter op , cyberorder.Ordertabell o WHERE 
						op.ordernr = o.ordernr  AND o.land_id=46 AND 
						op.artnr = '" . $artnr . "'
						GROUP BY op.utpris
						ORDER BY inkommet DESC";

			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $select;
				exit;
			}

					$res = mysqli_query(Db::getConnection(), $select);

				if (mysqli_num_rows($res) > 0) {
						$data = "['Datum', 'Pris'],\n";
						
					$showprices = true;
					
					while ($row = mysqli_fetch_object($res)) {
								//print_r($row);
								$data .= "['" . substr($row->inkommet, 0, 10) . "', " . round($row->utpris,0) . "],\n";
								//echo $data;
							
							}


					
					
				} else {
				
					$showprices = false;
							
				
				}
				
			?>

			<?php if ($showprices) { ?>
				
				<script type="text/javascript">
				  google.load("visualization", "1", {packages:["corechart"]});
				  google.setOnLoadCallback(drawChart2);
				  function drawChart2() {
					var data2 = google.visualization.arrayToDataTable([
					  <?php echo $data; ?>
					]);          
					var options2 = {
					  title: 'Historik på försäljningspris', 
					  width: 970, 
					  legend: 'none', 
					  
					  titleY: 'Utpris (ex moms)'
					};
					
					var chart2 = new google.visualization.LineChart(document.getElementById('chart_div2'));
					chart2.draw(data2, options2);
						// Add our over/out handlers.

				  }
				
				</script>
				
				
				<div align="center" id="chart_div2"></div>

			<?php } ?>

<?php } else { ?>

<?php } ?>

<?php
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				// @ini_set('display_errors', 'on');
	
				//@error_reporting(E_ALL);
				@error_reporting(E_ALL & ~E_NOTICE);
				//echo $select;
				//exit;
			}

// require_once("CWebADInternSuplier.php");
$adintern = new CWebADInternSuplier();
?>

<h1>Uppgifter om skickade ordrar</h1>
<div>
<?php
// echo "..";
if ($show_salesbundle == "yes") {
	$adintern->displayDeliveryList($artnr,true);
} else {
	$adintern->displayDeliveryList($artnr,false);
}
?>

</div>

</body>

</html>
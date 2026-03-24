<?php
spl_autoload_register(function ($class) {
  include $class . '.php';
});
session_start();
$dashboard = new CDashboard();

// Dela på miljoner och runda till en decimal
$incommingValue = round($dashboard->getIncommaingValueMysql() / 1000000, 1);
$warehouseValue = round($dashboard->getStoreValueMysql() / 1000000, 1);

// Tidsstämpel
date_default_timezone_set('Europe/Stockholm');
$lastUpdated = date('Y-m-d H:i');
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Dashboard - Lageröversikt</title>
    <meta charset="UTF-8">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
      html, body {
        height: 100%;
      }
      body {
        font-family: sans-serif;
        margin: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }
      .gauge-wrapper {
        display: flex;
        gap: 40px;
        margin-bottom: 20px;
      }
      .updated-time {
        font-size: 0.9em;
        color: #555;
      }
    </style>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawCharts);

      function drawCharts() {
        var warehouseData = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Lager', <?php echo $warehouseValue; ?>]
        ]);

        var warehouseOptions = {
          width: 300, height: 240,
          redFrom: 20, redTo: 25,
          yellowFrom: 18, yellowTo: 20,
          greenFrom: 0, greenTo: 18,
          minorTicks: 5,
          max: 25
        };

        var incommingData = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Inbound', <?php echo $incommingValue; ?>]
        ]);

        var incommingOptions = {
          width: 300, height: 240,
          redFrom: 7, redTo: 10,
          yellowFrom: 6, yellowTo: 7,
          greenFrom: 0, greenTo: 6,
          minorTicks: 5,
          max: 10
        };

        var chart1 = new google.visualization.Gauge(document.getElementById('gauge_warehouse'));
        chart1.draw(warehouseData, warehouseOptions);

        var chart2 = new google.visualization.Gauge(document.getElementById('gauge_incomming'));
        chart2.draw(incommingData, incommingOptions);
      }

      // Automatisk uppdatering var 15:e minut (900000 ms)
      setInterval(() => {
        location.reload();
      }, 60000);
    </script>
  </head>
  <body>
    <div class="gauge-wrapper">
      <div id="gauge_warehouse"></div>
      <div id="gauge_incomming"></div>
    </div>
    <div class="updated-time">Senast uppdaterad: <?php echo $lastUpdated; ?></div>
  </body>
</html>

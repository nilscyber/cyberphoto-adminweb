<?php 
	include_once("top.php");
	$manual_pagetitle = "Antal ordrar samt snittvärde på dessa";
	include_once("header.php");
	
	echo "<h1>" . $manual_pagetitle . "</h1>\n";
	
	$this_year = date('Y');
	$x = $this_year; 
	
	if ($history == "") {
		$history = $this_year;
	}

	echo "<form>\n";
	echo "<div>\n";
	echo "<div style=\"float: left; width: 80px;\">\n";
	echo "<select name=\"history\" onchange=\"this.form.submit();\">\n";
	
	
	while($x >= 2012) {
		echo "<option value=\"" . $x . "\"";
		if ($x == $history) {
			echo " selected";
		}
		echo ">" . $x . "</option>\n";
		$x--;
	}
	
	echo "</select>\n";
	echo "</div>\n";
	
	echo "<div style=\"float: left; width: 300px;\">\n";
	if ($thismonth == "yes") {
		echo "Visa endast innevarande månad (" . date("Y-m",time()) . ")<input type=\"checkbox\" name=\"thismonth\" value=\"yes\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Visa endast innevarande månad (" . date("Y-m",time()) . ")<input type=\"checkbox\" name=\"thismonth\" value=\"yes\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	
	echo "</div>\n";
	echo "</form>\n";
	
	echo "<div class='top10 clear'>";
	?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
				['Månad', 'Antal', 'Snitt SEK'],
				<?php $statistics->getAverageValue(true,false,false); ?>
				]);
	
       var options = {
          title: 'Sverige',
          hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };
	
        var chart = new google.visualization.AreaChart(document.getElementById('chart_div_se'));
        chart.draw(data, options);
      }
    </script>
	<div id="chart_div_se" style="width: 1200px; height: 500px;"></div>
	<?php
	echo "</div>\n";
	
	if ($history < 2017) {

		echo "<div class='top10 clear'>";
		?>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
		  google.load("visualization", "1", {packages:["corechart"]});
		  google.setOnLoadCallback(drawChart);
		  function drawChart() {
			var data = google.visualization.arrayToDataTable([
					['Månad', 'Antal', 'Snitt EUR'],
					<?php $statistics->getAverageValue(false,true,false); ?>
					]);
		
		   var options = {
			  title: 'Finland',
			  hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
			  vAxis: {minValue: 0}
			};
		
			var chart = new google.visualization.AreaChart(document.getElementById('chart_div_fi'));
			chart.draw(data, options);
		  }
		</script>
		<div id="chart_div_fi" style="width: 1200px; height: 500px;"></div>
		<?php
		echo "</div>\n";

		echo "<div class='top10 clear'>";
		?>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
		  google.load("visualization", "1", {packages:["corechart"]});
		  google.setOnLoadCallback(drawChart);
		  function drawChart() {
			var data = google.visualization.arrayToDataTable([
					['Månad', 'Antal', 'Snitt NOK'],
					<?php $statistics->getAverageValue(false,false,true); ?>
					]);
		
		   var options = {
			  title: 'Norge',
			  hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
			  vAxis: {minValue: 0}
			};
		
			var chart = new google.visualization.AreaChart(document.getElementById('chart_div_no'));
			chart.draw(data, options);
		  }
		</script>
		<div id="chart_div_no" style="width: 1200px; height: 500px;"></div>
		<?php
		echo "</div>\n";
	}

	
	include_once("footer.php");
?>
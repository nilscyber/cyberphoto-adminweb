<html>

<head>
<link rel="shortcut icon" href="https://admin.cyberphoto.se/ms-icon-144x144.png">
<title>Temperaturlogg</title>
<STYLE>
/* Useful */
.align_center { text-align: center }
.align_right { text-align: right }
.align_left { text-align: left }
.middle { vertical-align: middle }
.align_justify { text-align: justify }
.uppercase { text-transform: uppercase }
.hidden, .collapsed, .block_hidden_only_for_screen {display: none;}
.wrap { white-space: normal }
.bold { font-weight: bold }
.strike { text-decoration: line-through }
.italic { font-style: italic }
.top { vertical-align: top }
.span_link { cursor: pointer }
.span_link:hover { text-decoration: underline }
.span_blue {color: blue;}
.span_green {color: #0AD20A;}
.span_red {color: red;}
.span_green2 {color: green;}

/* Bra att ha */

.floatleft {
	float: left;
}
.floatright {
	float: right;
}
.clear {
	clear: both;
}
.top5 {
	margin-top: 5px;
}
.bottom5 {
	margin-bottom: 5px;
}
.top10 {
	margin-top: 10px;
}
.bottom10 {
	margin-bottom: 10px;
}
.top20 {
	margin-top: 20px;
}
.bottom20 {
	margin-bottom: 20px;
}
.left5 {
	margin-left: 5px;
}
.left10 {
	margin-left: 10px;
}
.left20 {
	margin-left: 20px;
}
.right5 {
	margin-right: 5px;
}
.right10 {
	margin-right: 10px;
}
.right20 {
	margin-right: 20px;
}

body {
	/*background-image: url(/order/logo.jpg); */
	background-repeat: no-repeat; 
	background-attachment:fixed; 
	background-position:top right; 
	background-color: #FFFFFF; 
	font-family: Verdana; font-size: 11px; 
	margin-top: 5; 
	margin-right: 5; 
	margin-bottom: 5; 
	margin-left: 5; 
	text-align: center;

}
h1 {
	font-family: Verdana; 
	font-size: 16px; 
	color: #000000 ;
}
.bold { font-weight:bold }
table           
{
 background-color: #FFFFFF; 
 border: 1px solid green;
 margin-left:auto; 
 margin-right:auto;
 width: 480px;
 
}
th           
{
 padding: 2px;
 font-family: Verdana, Arial, Helvetica;
 font-size: 14px;
 text-align: center;
}
td           
{
 padding: 2px;
 font-family: Verdana, Arial, Helvetica;
 font-size: 11px;
 text-align: center;
}
.chat {
	background-image: url(/order/chat.png); 
	background-repeat: no-repeat; 
	background-position:top left; 
	padding-left: 20px;
	font-weight:bold;
	margin-top: 20px;
}
.tbtgcolorgreen { color:#009933; font: 12px Verdana; font-weight:bold }
.tbtgcolorred { color:#CC0000; font: 12px Verdana; font-weight:bold }
.search_line1 {
    background-color: #EDEDED;
}
.search_line2 {
    background-color: #FFFFFF;
}
.nav-bar {
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	justify-content: center;
	gap: 8px;
	padding: 14px 16px;
	background: #f0f4f8;
	border-bottom: 2px solid #d0d8e4;
	margin-bottom: 20px;
}
.nav-btn {
	display: inline-block;
	padding: 7px 16px;
	border-radius: 20px;
	border: 1px solid #b0bec5;
	background: #ffffff;
	color: #37474f;
	font-family: Verdana, sans-serif;
	font-size: 12px;
	cursor: pointer;
	text-decoration: none;
	transition: background 0.15s, color 0.15s, border-color 0.15s;
	white-space: nowrap;
}
.nav-btn:hover {
	background: #e3eaf0;
	border-color: #78909c;
}
.nav-btn.active {
	background: #1a3a6b;
	color: #ffffff;
	border-color: #1a3a6b;
	font-weight: bold;
}
.nav-btn.diagram {
	background: #2e7d32;
	color: #ffffff;
	border-color: #2e7d32;
	font-weight: bold;
}
.nav-btn.diagram:hover {
	background: #1b5e20;
	border-color: #1b5e20;
}
.nav-divider {
	width: 1px;
	height: 28px;
	background: #b0bec5;
	margin: 0 4px;
}
</style>
</head>

<body>
<?php
	$temperature = (isset($_GET['temperature']) ? $_GET['temperature'] : null);
	$humidity    = (isset($_GET['humidity'])    ? $_GET['humidity']    : null);
	$sensor      = (isset($_GET['sensor'])      ? $_GET['sensor']      : null);
	$showtemp    = (isset($_GET['showtemp'])    ? $_GET['showtemp']    : null);

	$dateFran = (isset($_GET['fran']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fran'])) ? $_GET['fran'] : null;
	$dateTill = (isset($_GET['till']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['till'])) ? $_GET['till'] : null;

	$sensorList = [
		1 => 'Server',
		2 => 'Ute',
		3 => 'Nya norra',
		4 => 'Nya södra',
		5 => 'Köket',
		6 => 'Inbyte',
		7 => 'Service',
		8 => 'Packsal',
		9 => 'Flyttbar',
	];

	$franVal = $dateFran ?: date('Y-m-d', strtotime('-30 days'));
	$tillVal = $dateTill ?: date('Y-m-d');
	$dateParams = ($dateFran || $dateTill) ? '&fran=' . urlencode($franVal) . '&till=' . urlencode($tillVal) : '';

	echo "<div class=\"nav-bar\">\n";
	echo "<a href=\"index.php\" class=\"nav-btn diagram\">&#9642; Visa diagram</a>\n";
	echo "<div class=\"nav-divider\"></div>\n";
	foreach ($sensorList as $id => $namn) {
		$active = ($sensor == $id) ? ' active' : '';
		echo "<a href=\"index.php?showtemp=yes&sensor=$id$dateParams\" class=\"nav-btn$active\">$namn</a>\n";
	}
	echo "<div class=\"nav-divider\"></div>\n";
	echo "<form method=\"GET\" style=\"display:flex;align-items:center;gap:6px;\">\n";
	echo "<label style=\"font-size:12px;color:#37474f;\">Från</label>\n";
	echo "<input type=\"date\" name=\"fran\" value=\"$franVal\" style=\"padding:5px 8px;border-radius:6px;border:1px solid #b0bec5;font-size:12px;\">\n";
	echo "<label style=\"font-size:12px;color:#37474f;\">Till</label>\n";
	echo "<input type=\"date\" name=\"till\" value=\"$tillVal\" style=\"padding:5px 8px;border-radius:6px;border:1px solid #b0bec5;font-size:12px;\">\n";
	if ($showtemp) {
		echo "<input type=\"hidden\" name=\"showtemp\" value=\"yes\">\n";
		echo "<input type=\"hidden\" name=\"sensor\" value=\"" . htmlspecialchars($sensor) . "\">\n";
	}
	echo "<button type=\"submit\" class=\"nav-btn diagram\" style=\"border-radius:6px;\">Visa</button>\n";
	echo "</form>\n";
	echo "</div>\n";
	
	spl_autoload_register(function ($class) {
		include $class . '.php';
	});
	$temp = new CTemp();
	
	// echo $temp->showLastTemp(2);
	
	// if (date('i') == 57 && $temp->getLatestTimeStamp() != date('i') && $showtemp != "yes") {
	/*
	if ((date('i') == 00 || date('i') == 15 || date('i') == 30 || date('i') == 45) && $temp->getLatestTimeStamp() != date('i') && $showtemp != "yes") {
		$temp->addTemp($temperature,$humidity);
	}
	*/
	if ($temperature != "") {
		$temp->addTemp($temperature,$humidity,$sensor);
	}
	
	if ($showtemp == "yes") {
		$temp->showTempList($sensor, $dateFran ? $franVal : null, $dateTill ? $tillVal : null);
	} else {

		$sensors = [
			1 => ['namn' => 'Serverrum', 'farg' => '#1a3a6b'],
			2 => ['namn' => 'Ute',       'farg' => '#2d6a4f'],
			3 => ['namn' => 'Nya norra', 'farg' => '#7b2d8b'],
			4 => ['namn' => 'Nya södra', 'farg' => '#0096c7'],
			5 => ['namn' => 'Köket',     'farg' => '#80b918'],
			6 => ['namn' => 'Inbyte',    'farg' => '#c1121f'],
			7 => ['namn' => 'Service',   'farg' => '#780000'],
			8 => ['namn' => 'Packsal',   'farg' => '#f77f00'],
			9 => ['namn' => 'Flyttbar',  'farg' => '#4361ee'],
		];

		$sqlFran = mysqli_real_escape_string(Db::getConnection(false), $franVal);
		$sqlTill = mysqli_real_escape_string(Db::getConnection(false), $tillVal);

		$select  = "SELECT DATE(tTime) AS dag, tSensor, ";
		$select .= "ROUND(AVG(tTemperature), 1) AS snitt_temp, ";
		$select .= "ROUND(AVG(tHumidity), 1) AS snitt_hum ";
		$select .= "FROM cyberadmin.temp ";
		$select .= "WHERE DATE(tTime) BETWEEN '$sqlFran' AND '$sqlTill' ";
		$select .= "AND HOUR(tTime) BETWEEN 7 AND 18 ";
		$select .= "GROUP BY dag, tSensor ";
		$select .= "ORDER BY dag ASC, tSensor ASC";

		$res = @mysqli_query(Db::getConnection(false), $select);

		// Bygg upp: $data[sensorId][datum] = värde
		$tempData = [];
		$humData  = [];
		$dates    = [];
		while ($row = mysqli_fetch_object($res)) {
			$tempData[$row->tSensor][$row->dag] = $row->snitt_temp;
			$humData[$row->tSensor][$row->dag]  = $row->snitt_hum;
			$dates[$row->dag] = true;
		}
		ksort($dates);
		$labels = array_keys($dates);

		// Formatera labels som "d-M" (t.ex. "14-Apr")
		$labelsFormatted = array_map(fn($d) => date('d-M', strtotime($d)), $labels);

		// Bygg datasets för temperatur och luftfuktighet
		$tempDatasets = [];
		$humDatasets  = [];
		foreach ($sensors as $id => $s) {
			if (empty($tempData[$id])) continue;
			$tempPoints = [];
			$humPoints  = [];
			foreach ($labels as $dag) {
				$tempPoints[] = isset($tempData[$id][$dag]) ? $tempData[$id][$dag] : 'null';
				$humPoints[]  = isset($humData[$id][$dag])  ? $humData[$id][$dag]  : 'null';
			}
			$tempDatasets[] = [
				'label'       => $s['namn'],
				'data'        => $tempPoints,
				'borderColor' => $s['farg'],
				'fill'        => false,
				'tension'     => 0.3,
				'pointRadius' => 2,
			];
			$humDatasets[] = [
				'label'       => $s['namn'],
				'data'        => $humPoints,
				'borderColor' => $s['farg'],
				'fill'        => false,
				'tension'     => 0.3,
				'pointRadius' => 2,
			];
		}

		$labelsJson    = json_encode($labelsFormatted);
		$tempDsJson    = json_encode($tempDatasets);
		$humDsJson     = json_encode($humDatasets);

		$periodRubrik = date('d-M-Y', strtotime($franVal)) . ' — ' . date('d-M-Y', strtotime($tillVal));

		echo "<script src=\"https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js\"></script>\n";
		echo "<div class=\"top20\">\n";
		echo "<h2>Medeltemperaturen 07:00–18:00 &nbsp;·&nbsp; $periodRubrik</h2>\n";
		echo "<div style=\"width:95%;margin:auto;\"><canvas id=\"chartTemp\"></canvas></div>\n";
		echo "</div>\n";
		echo "<div class=\"top20\">\n";
		echo "<h2>Medelluftfuktigheten 07:00–18:00 &nbsp;·&nbsp; $periodRubrik</h2>\n";
		echo "<div style=\"width:95%;margin:auto;\"><canvas id=\"chartHum\"></canvas></div>\n";
		echo "</div>\n";
		?>
		<script>
		(function(){
			var labels = <?= $labelsJson ?>;
			var commonOptions = {
				responsive: true,
				interaction: { mode: 'index', intersect: false },
				plugins: { legend: { position: 'right' } },
				scales: {
					x: { ticks: { maxRotation: 45, minRotation: 45 } },
					y: { ticks: { stepSize: 1 } }
				}
			};

			new Chart(document.getElementById('chartTemp'), {
				type: 'line',
				data: { labels: labels, datasets: <?= $tempDsJson ?> },
				options: Object.assign({}, commonOptions, {
					plugins: Object.assign({}, commonOptions.plugins, {
						tooltip: {
							callbacks: {
								label: function(ctx){ return ctx.dataset.label + ': ' + ctx.parsed.y + '°'; }
							}
						}
					})
				})
			});

			new Chart(document.getElementById('chartHum'), {
				type: 'line',
				data: { labels: labels, datasets: <?= $humDsJson ?> },
				options: Object.assign({}, commonOptions, {
					plugins: Object.assign({}, commonOptions.plugins, {
						tooltip: {
							callbacks: {
								label: function(ctx){ return ctx.dataset.label + ': ' + ctx.parsed.y + '%'; }
							}
						}
					})
				})
			});
		})();
		</script>
		<?php
	}

?>
</body>
</html>
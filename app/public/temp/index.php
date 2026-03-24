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
.chooser {
	margin-left:auto; 
	margin-right:auto;
	width: 920px;
}
</style>
</head>

<body>
<?php
	echo "<div class=\"chooser\">\n";
	echo "<form method=\"GET\">\n";
	echo "<input type=\"hidden\" name=\"showtemp\" value=\"yes\">\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($sensor == "1") {
		echo "Server <input type=\"radio\" name=\"sensor\" value=\"1\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Server <input type=\"radio\" name=\"sensor\" value=\"1\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($sensor == "2") {
		echo "Ute <input type=\"radio\" name=\"sensor\" value=\"2\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Ute <input type=\"radio\" name=\"sensor\" value=\"2\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($sensor == "3") {
		echo "Nya norra <input type=\"radio\" name=\"sensor\" value=\"3\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Nya norra <input type=\"radio\" name=\"sensor\" value=\"3\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($sensor == "4") {
		echo "Nya södra <input type=\"radio\" name=\"sensor\" value=\"4\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Nya södra <input type=\"radio\" name=\"sensor\" value=\"4\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($sensor == "5") {
		echo "Köket <input type=\"radio\" name=\"sensor\" value=\"5\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Köket <input type=\"radio\" name=\"sensor\" value=\"5\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($sensor == "6") {
		echo "Inbyte <input type=\"radio\" name=\"sensor\" value=\"6\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Inbyte <input type=\"radio\" name=\"sensor\" value=\"6\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($sensor == "7") {
		echo "Service <input type=\"radio\" name=\"sensor\" value=\"7\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Service <input type=\"radio\" name=\"sensor\" value=\"7\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($sensor == "8") {
		echo "Packsal <input type=\"radio\" name=\"sensor\" value=\"8\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Packsal <input type=\"radio\" name=\"sensor\" value=\"8\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	echo "<div style=\"float: left; width: 100px;\">\n";
	if ($sensor == "9") {
		echo "Flyttbar <input type=\"radio\" name=\"sensor\" value=\"9\" onClick=\"submit()\" checked>\n";
	} else {
		echo "Flyttbar <input type=\"radio\" name=\"sensor\" value=\"9\" onClick=\"submit()\">\n";
	}
	echo "</div>\n";
	
	echo "</form>\n";
	echo "</div>\n";
	echo "<div class=\"clear\"></div>\n";

	/* 2014-08-15 Logga temperaturen i serverrummet */

	$temperature = (isset($_GET['temperature']) ? $_GET['temperature'] : null);
	$humidity = (isset($_GET['humidity']) ? $_GET['humidity'] : null);
	$sensor = (isset($_GET['sensor']) ? $_GET['sensor'] : null);
	$showtemp = (isset($_GET['showtemp']) ? $_GET['showtemp'] : null);
	
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
		$temp->showTempList($sensor);
	} else {
		echo "<div class=\"top20\">\n";
		echo "<h2>Medeltemperaturen mellan 07:00 - 18:00 - 30 dagar bakåt</h2>\n";
		echo "<img border=\"0\" src=\"diagram_temperature.png?ver=" . date('Ymd') . "\" title=\"Genomsnitt senaste 30 dagarna\">";
		echo "</div>\n";
		echo "<div class=\"top20\">\n";
		echo "<h2>Medelluftfuktigheten mellan 07:00 - 18:00 - 30 dagar bakåt</h2>\n";
		echo "<img border=\"0\" src=\"diagram_humidity.png?ver=" . date('Ymd') . "\" title=\"Genomsnitt senaste 30 dagarna\">";
		echo "</div>\n";
	}

?>
</body>
</html>
<?php
spl_autoload_register(function ($class) {
	include $class . '.php';
});

$butiken = new CButiken();
$butiken->butikenHelp();
?>
<html>

<head>
<title>Hjälp kallad till butiken</title>
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<style>
.kosystem_container {
	background: #F1F1F1;
	color: #3A3A3A; 
	border: #E2E2E2 solid 1px;
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	border-radius: 8px;
	margin-bottom: 8px;
}
#kosystem_nr {
	color: #11800b; 
	font-size: 100px;
	text-align: center;
	font-weight: bold;
	padding: 0px 0px;
	margin: -10px 0px;
	font-family: Arial;
}
a { 
	text-decoration: none; 
}
</style>
</head>
<body>
<?php
	echo "<div class=\"kosystem_container\">\n";
	echo "<div ID=\"kosystem_nr\">CALLED</div>\n";
	echo "<div class=\"clear\"></div>\n";
	echo "</div>\n";
?>
</body>

</html>
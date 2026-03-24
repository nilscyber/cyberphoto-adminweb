<?php
spl_autoload_register(function ($class) {
	include $class . '.php';
});

$butiken = new CButiken();

if ($edit == "yes") {
	$rows = $butiken->getKoSystemInfo($ID);
	$konumber = substr($rows->koNR, -2);
}

if ($subm) {
	
	$olright = true;
	
	if ($konumber == "") {
		$olright = false;
		$wrongmess .= "<p class=\"wrongmess\">- Fältet får inte vara tomt!</p>";
	}

	if (!is_numeric($konumber)) {
		$olright = false;
		$wrongmess .= "<p class=\"wrongmess\">- Skall vara ett nummer mellan 1-99!</p>";
	}

	if ($olright) {
		$butiken->addKoNumber($konumber,null);
		$uppdate_ok = true;
	}

}

?>
<html>

<head>
<title>KÖ hantering</title>
<link rel="shortcut icon" href="https://admin.cyberphoto.se/kosystemet.ico">
<script type="text/javascript" src="https://admin.cyberphoto.se/javascript/winpop.js"></script>
<script type="text/javascript" src="/javascript/jquery/jquery.js"></script>
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
	color: #0f24c5; 
	font-size: 500px;
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
<?php
echo "<script>\n";
echo "\tfunction autoRefresh_div() {\n";
echo "\t\t$('#kosystem_nr').load('incl_konr.php');\n";
echo "\t}\n";
echo "\tsetInterval('autoRefresh_div()', 1000);\n";
echo "</script>\n";

/*
echo "<script>\n";
echo "function addkonumber () {\n";
echo "\t$.ajax({\n";
echo "\t\turl:'/order/incl_addkonr.php',\n";
echo "\t\ttype: 'POST',\n";
// echo "\t\tsuccess: function (response) {\n";
// echo "\t\t\talert(response);\n";
// echo "\t\t}\n";
echo "\t\t});\n";
echo "\t}\n";
echo "</script>\n";
*/
?>
</head>
<body>
<?php

	echo "<div class=\"kosystem_container\">\n";
	echo "<a href=\"javascript:winPopupCenter(190, 350, '/kosystem_update.php?edit=yes');\">\n";
	echo "<div ID=\"kosystem_nr\">" . $butiken->getLastKoInput() . "</div>\n";
	echo "</a>\n";
	// echo "<div class=\"kosystem_add\" onclick=\"addkonumber()\">next number</div>\n";
	echo "<div class=\"clear\"></div>\n";
	echo "</div>\n";

?>
</body>

</html>
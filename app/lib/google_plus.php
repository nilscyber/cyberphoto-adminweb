<?php
if ($fi) {
	echo "<script type=\"text/javascript\">\n";
	echo "\twindow.___gcfg = {lang: 'fi'};\n";
	echo "\t\t(function() {\n";
	echo "\t\tvar po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;\n";
	echo "\t\tpo.src = 'https://apis.google.com/js/platform.js';\n";
	echo "\t\tvar s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);\n";
	echo "\t})();\n";
	echo "</script>\n";
} elseif ($no) {
	echo "<script type=\"text/javascript\">\n";
	echo "\twindow.___gcfg = {lang: 'no'};\n";
	echo "\t\t(function() {\n";
	echo "\t\tvar po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;\n";
	echo "\t\tpo.src = 'https://apis.google.com/js/platform.js';\n";
	echo "\t\tvar s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);\n";
	echo "\t})();\n";
	echo "</script>\n";
} else {
	echo "<script type=\"text/javascript\">\n";
	echo "\twindow.___gcfg = {lang: 'sv'};\n";
	echo "\t\t(function() {\n";
	echo "\t\tvar po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;\n";
	echo "\t\tpo.src = 'https://apis.google.com/js/platform.js';\n";
	echo "\t\tvar s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);\n";
	echo "\t})();\n";
	echo "</script>\n";
}
?>
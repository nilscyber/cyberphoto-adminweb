<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	echo '<style>
	.col-icon{width:32px}
	.col-date{width:60px}
	.col-left{width:90px}
	.col-art{width:110px}
	.col-price{width:90px}
	.col-status{width:75px}
	.col-owner{width:70px}
	.bar-track{background:#eef2f5;border-radius:999px;height:10px;overflow:hidden}
	.bar-fill{background:#0d9488;height:100%;border-radius:999px}
	</style>';

	echo "<h1>Kommande produktuppdateringar</h1>\n";
	echo "<div class='top10'>";
	echo "<h2>Kommande</h2>\n";
	$product->listCommingUpdates();
	echo "<div class='top20'></div>\n";
	echo "<hr class=\"hr_blue\">\n";
	echo "<h2>Redan uppdaterade</h2>\n";
	$product->listCommingUpdates(true);
	echo "</div>\n";
	echo "<div class='top20'></div>\n";
	echo "<h2>Fördelning av utförda uppdateringar</h2>\n";
	$product->listProductUpdateBy();
	echo "</div>\n";
	
	include_once("footer.php");
?>
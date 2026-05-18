<?php
	include_once("top.php");
	include_once("header.php");

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<style>
.copy-art{cursor:pointer;user-select:none}
.text-right{text-align:right}
</style>\n";

	echo '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">'
	   . '<h1 style="margin:0">Produkter som har kö</h1>'
	   . '<a href="missing_products_export.php" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#0d9488;color:#fff;border-radius:6px;text-decoration:none;font-size:13px;font-weight:700">'
	   . '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>'
	   . 'Exportera till Excel</a>'
	   . '</div>' . "\n";
	$intern->printMissingProductsFromAD();

	include_once("footer.php");
?>
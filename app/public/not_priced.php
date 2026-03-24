<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Ej prissatta produkter</h1>\n";
	echo "<h2>Dessa artiklar måste åtgärdas (<span style=\"color:blue\">Sverige</span>)</h2>\n";
	$parcel->getArticleNotPriced();
	echo "<h2>Dessa artiklar måste åtgärdas (<span style=\"color:blue\">Finland</span>)</h2>\n";
	$parcel->getArticleNotPricedFI();
	echo "<h2>Dessa artiklar måste åtgärdas (<span style=\"color:blue\">Norge</span>)</h2>\n";
	$parcel->getArticleNotPriced(true);
	
	include_once("footer.php");
?>
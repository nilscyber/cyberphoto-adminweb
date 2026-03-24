<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Återställning av lösenord (kunder)</h1>\n";
	echo "<div class='top10'>";
	// $adintern->purschasedGoodsWithNoCustomers();
	echo "<h2>Gilltiga länkar just nu</h2>\n";
	$passwd->listAllRecovery(false);
	echo "<h2>Använda länkar eller länkar som gått ut</h2>\n";
	$passwd->listAllRecovery(true);
	echo "<h2>Bytta lösenord senaste 14 dagarna</h2>\n";
	$passwd->RecoveryByDay();
	echo "</div>\n";

	include_once("footer.php");
?>
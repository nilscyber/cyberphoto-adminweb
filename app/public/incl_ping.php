<?php
	// echo date(("Y-m-d H:i"), time());
	spl_autoload_register(function ($class) {
		include $class . '.php';
	});
	$tradein = new CTradeIn();
	echo $tradein->getLastPing(true);
	
?>
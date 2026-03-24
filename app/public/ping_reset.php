<?php
spl_autoload_register(function ($class) {
	include $class . '.php';
});
$tradein = new CTradeIn();
$tradein->pingIncommingReset();
?>
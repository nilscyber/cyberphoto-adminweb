<?php 
	spl_autoload_register(function ($class) {
		include $class . '.php';
	});
	session_start();
	$crontab = new CCrontab();
	$crontab->goodsExpectationValue();
?>
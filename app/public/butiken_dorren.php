<?php
	spl_autoload_register(function ($class) {
		include $class . '.php';
	});

	$add_logg = false;
	$butiken = new CButiken();

	if ($pling == "yes") {
		$add_logg = true;
	}

	if ($add_logg) {
		$butiken->addDorrPling();
	}
	exit;
?>
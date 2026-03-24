<?php
	if ($_GET['kassa'] != "") {
		$kassa = $_GET['kassa'];
	}
	// echo date(("Y-m-d H:i"), time());
	spl_autoload_register(function ($class) {
		include $class . '.php';
	});
	$butiken = new CButiken();
	$butiken->addKoNumber(null,$kassa);
	
?>
<?php
	// hanterar det som visas på skärmarna i butiken
	// echo date(("Y-m-d H:i"), time());
	spl_autoload_register(function ($class) {
		include $class . '.php';
	});
	$banners = new CBanners();
	$banners->getBannerNoFrame(203,1,true,"no",1);
	
?>
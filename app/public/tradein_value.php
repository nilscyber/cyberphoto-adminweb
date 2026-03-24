<?php
spl_autoload_register(function ($class) {
	include $class . '.php';
});

$tradein = new CTradeIn();

?>
<html>

<head>
<title>TradeIn Value</title>
<link rel="icon" type="image/png" href="https://admin.cyberphoto.se/favicon.png">
<script type="text/javascript" src="https://admin.cyberphoto.se/javascript/winpop.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="/css/tradein.css?v=g<?php echo date("ynjGi"); ?>" />
</head>
<body>
<?php

	$tradein->getTradeInValue(true);

?>
</body>

</html>
<?php
require_once("CADOrderInfo.php");
$orderi = new CADOrderInfo();
$order = isset($_GET['order']) ? $_GET['order'] : '';
?>
<html>

<head>
<link rel="shortcut icon" href="https://admin.cyberphoto.se/admin.ico">
<link rel="stylesheet" type="text/css" href="/css/order_info.css" />
<title>CyberPhoto - Uppgifter om säljorder <?php echo $order; ?></title>
<STYLE>
<!--
  tr { background-color: #FFFFFF}
  .normal { background-color: #FFFFFF }
  .highlight { background-color: #EAEAEA }
  .top20 { margin-top: 20px; }
//-->
</style>
</head>

<body>
<h1>Uppgifter om säljorder <?php echo $order; ?></h1>
<?php
if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
	$orderi->displayOrderInfo($order); 
} else {
	$orderi->displayOrderInfo($order); 
}
?>
</body>

</html>

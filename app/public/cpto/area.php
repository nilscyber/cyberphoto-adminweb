<?php include_once("top.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<title>CyberPhoto - CPTO</title> 
<?php include("css.php"); ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/> 
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
</head> 
 
<body>
<div id="maincontainer">

<div class="logopanel"><a onclick="showValues()"><div class="logo"></div></a></div>
<?php
$cpto->getIncommingOrders();
$cpto->getOutgoingOrders();
if (date(N) == 6 || date(N) == 7 || date(G) < 8 || date(G) > 18 || (date(G) == 8 && date(i) < 16)) {
	$cpto->getStoreValue(false);
} else {
	$cpto->getStoreValue(true);
}
$cpto->getPrintedOrders();
$cpto->getNotPrintedOrders();
?>
<div id="footcontainer">Copyright &copy; 1995 - <?php echo date(Y); ?></div>
</div>
</body>

</html>

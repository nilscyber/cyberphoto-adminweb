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

<div class="logopanel"><a href="/cpto/"><div class="logo"></div></a></div>
<div id="incontainer"><?php $cpto->getIncommingOrders(); ?></div>
<div id="outcontainer"><?php $cpto->getOutgoingOrders(); ?></div>
<div id="footcontainer">Copyright © 1995 - <?php echo date("Y", time()); ?></div>
</div>
</body>

</html>

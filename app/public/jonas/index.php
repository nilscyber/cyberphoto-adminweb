<?php session_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="/css/tradein.css?v=g<?php echo date("ynjGi"); ?>" />
<title><?php echo "Löpande data Jonas | CyberPhoto"; ?></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
 function autoRefresh_div()	 {
	  $("#upper_left").load("upper_left.php");
	  $("#result").load("incl_time.php");
	  $("#turn_around").load("turn_around.php");
	  $("#first_column").load("first_column.php");
	  $("#second_column").load("second_column.php");
	  $("#third_column").load("third_column.php");
  }
 setInterval('autoRefresh_div()', 30000);
</script>
</head>

<body>

<div class="first_col">
<div class="container_white align_center clock">
<div id="upper_left">&nbsp;</div>
</div>
<div id="first_column"></div>
</div>

<div class="second_col">
<div class="container_yellow align_center clock">
<div id="result"><?php echo date("H:i"); ?></div>
</div>
<div id="second_column"></div>
</div>

<div class="third_col">
<div class="container_white align_center clock">
<div id="upper_right">&nbsp;</div>
</div>
<div id="third_column"></div>
</div>

<div class="clear"></div>

<div id="footer"></div>

</body>

</html>
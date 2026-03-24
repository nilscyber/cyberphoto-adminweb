<?php
function __autoload($class_name) {
	include $class_name . '.php';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="sv" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link href="theme.css" rel="stylesheet">
	<script>
	 function autoRefresh_div()	 {
		 <?php if (preg_match("/tv4/i", $_SERVER['PHP_SELF'])) { ?>
		  $("#picture").load("incl_tv4.php");
		 <?php } elseif (preg_match("/tv3/i", $_SERVER['PHP_SELF'])) { ?>
		  $("#picture").load("incl_tv3.php");
		 <?php } elseif (preg_match("/tv2/i", $_SERVER['PHP_SELF'])) { ?>
		  $("#picture").load("incl_tv2.php");
		 <?php } else { ?>
		  $("#picture").load("incl_tv1.php");
		 <?php } ?>
	  }
	 setInterval('autoRefresh_div()', 25000);
	</script>
	<style>
		body {
			margin: 0px 0px 0px 0px ; 
			overflow:hidden;
		}
		img {
			max-width: 100%;
			height: auto;
			width: auto\9; /* ie8 */
		}
	</style>
  </head>
  <body>
	<div id="picture">Bilder laddas in....</div>
  </body>
</html>
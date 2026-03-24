<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" src="/javascript/jquery/jquery.js"></script>
<style>
body {
    background-color: #85000d;
}
#kosystem_nr {
	color: #ffffff; 
	font-size: 1000px;
	text-align: center;
	font-weight: bold;
	padding: 0px 0px;
	margin-top: 100px;
	font-family: Arial;
}
.ko_nr {
	color: #ffffff; 
	font-size: 1000px;
	text-align: center;
	font-weight: bold;
	padding: 0px 0px;
	margin-top: -150px;
	font-family: Arial;
}
.ko_kassa {
	color: #ffffff; 
	font-size: 200px;
	text-align: center;
	font-weight: bold;
	padding: 0px 0px;
	margin-top: -220px;
	font-family: Arial;
}
.blink_text_white {

    animation:0.5s blinker linear infinite;
    -webkit-animation:0.5s blinker linear infinite;
    -moz-animation:0.5s blinker linear infinite;
     color: white;
}

@-moz-keyframes blinker {  
     0% { opacity: 1.0; }
     50% { opacity: 0.0; }
     100% { opacity: 1.0; }
}

@-webkit-keyframes blinker {  
     0% { opacity: 1.0; }
     50% { opacity: 0.0; }
     100% { opacity: 1.0; }
}

@keyframes blinker {  
	 0% { opacity: 1.0; }
	 50% { opacity: 0.0; }
	 100% { opacity: 1.0; }
}
</style>
<script>
	function autoRefresh_div() {
		$('#kosystem_nr').load('incl_konr.php');
	}
	setInterval('autoRefresh_div()', 1000);
</script>
</head>
<body>
<div ID="kosystem_nr">&nbsp;</div>
</body>
</html>

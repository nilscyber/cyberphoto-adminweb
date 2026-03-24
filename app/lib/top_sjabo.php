<?php

	// se till att användaren kastas om till www
	if (!(preg_match("/www/i", $_SERVER["HTTP_HOST"]))) {

		$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
		$host     = $_SERVER['HTTP_HOST'];
		$script   = $_SERVER['SCRIPT_NAME'];
		$params   = $_SERVER['QUERY_STRING'];
		if ($params != null) {
			$currentUrl = $protocol . '://www.' . $host . $script . '?' . $params;
		} else {
			$currentUrl = $protocol . '://www.' . $host . $script;
		}

		header("Location: $currentUrl");
		exit;
	}
	
	// echo $_SERVER['HTTP_HOST'];
	// echo $_SERVER['QUERY_STRING'];
	
?>
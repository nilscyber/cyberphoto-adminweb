<?php
	// om Sverige
	if (preg_match("/cyberphoto\.se/i", $_SERVER["HTTP_HOST"])) {
		$sv = true;
	}
	// om Norge
	if (preg_match("/cyberphoto\.no/i", $_SERVER["HTTP_HOST"])) {
		$no = true;
	}
	// om Finland
	if (preg_match("/cyberphoto\.fi/i", $_SERVER["HTTP_HOST"])) {
		$fi = true;
	}

	// om Inte www, se till att användaren kastas om till www
	if (!(preg_match("/www/i", $_SERVER["HTTP_HOST"]))) {

		if ($HTTPS == "on") {
			$protocol = 'https';
		} else {
			$protocol = 'http';
		}
		$host     = $_SERVER['HTTP_HOST'];
		$script   = $_SERVER['SCRIPT_NAME'];
		$params   = $_SERVER['QUERY_STRING'];
		if ($params != null) {
			$currentUrl = $protocol . '://www.' . $host . $script . '?' . $params;
		} else {
			$currentUrl = $protocol . '://www.' . $host . $script;
		}

		header("Location: $currentUrl");
		
	}
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
		echo "SV: " . $sv . "<br>";
		echo "FI: " . $fi . "<br>";
		echo "NO: " . $no . "<br>";
		echo "cookie value: " . $_COOKIE['preferredLang'] . "<br>";
		echo "Current locale: " . $_SESSION['currentLocale'] . "<br>";
	}
	
?>
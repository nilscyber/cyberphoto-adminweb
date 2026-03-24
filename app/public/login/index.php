<?php
// include_once("../../../incl_class.php");
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
		/*
		if ($_SERVER['REQUEST_URI'] == "/foto-video/systemkameror") {
			echo "jepp";
		}
		*/
		// echo phpinfo();
		echo "sdsadf" . Locs::getDomainName();
		
		exit;
	}

if ($force_lang != "") {
	if ($force_lang == "fi") {
		$fi = true;
	} elseif ($force_lang == "no") {
		$no = true;
	} else {
		$sv = true;
	}
} else {
	if ($_SESSION['currentLocale'] == 'fi_FI' || $_SESSION['currentLocale'] == 'sv_FI') {
		$fi = true;
	} elseif ($_SESSION['currentLocale'] == 'no_NO') {
		$no = true;
	} else {
		$sv = true;
	}
}
if ($fi) {
	$google_client_id 		= '641591682336-abjgos8ljm0svvq7qbkrsgle6hb2q018.apps.googleusercontent.com';
	$google_client_secret 	= 'h9Jp1Gh05ePxLKvgHtsqRFGN';
	$google_redirect_url 	= 'https://www.cyberphoto.fi/order/admin/login'; //path to your script
	$google_developer_key 	= 'AIzaSyDwheMNHix_HPZhCKiuWFWf3Bfn9huN00k';
} elseif ($no) {
	$google_client_id 		= '641591682336-u1atn1873vct6i2h337dq990puuss401.apps.googleusercontent.com';
	$google_client_secret 	= '6nDhPT1jeGFJS54i4QLl27Op';
	$google_redirect_url 	= 'https://www.cyberphoto.no/order/admin/login'; //path to your script
	$google_developer_key 	= 'AIzaSyDwheMNHix_HPZhCKiuWFWf3Bfn9huN00k';
} elseif ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
	$google_client_id 		= '641591682336-jqcn7dkie5tt5s472oi6lpcusvhe79dc.apps.googleusercontent.com';
	$google_client_secret 	= 'WbnX_8hFAv-IY-AnzADIq2Lu';
	$google_redirect_url 	= 'https://admin.cyberphoto.se/login'; //path to your script
	$google_developer_key 	= 'AIzaSyDwheMNHix_HPZhCKiuWFWf3Bfn9huN00k';
} else {
	$google_client_id 		= '641591682336-tamcnl5v377ilj6ge41lq4fpeikm3li8.apps.googleusercontent.com';
	$google_client_secret 	= 'Jd6cdesSr9945bgew89-ZziA';
	$google_redirect_url 	= 'https://www.cyberphoto.se/order/admin/login'; //path to your script
	$google_developer_key 	= 'AIzaSyDwheMNHix_HPZhCKiuWFWf3Bfn9huN00k';
}

require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_Oauth2Service.php';

session_start();

if (isset($_REQUEST['from_page'])) {
	$_SESSION['admin_from_product'] = true;
	$_SESSION['admin_rem_page'] = $_REQUEST['from_page'];
}

$gClient = new Google_Client();
$gClient->setApplicationName('Login to CyberPhoto Administration Center');
$gClient->setClientId($google_client_id);
$gClient->setClientSecret($google_client_secret);
$gClient->setRedirectUri($google_redirect_url);
$gClient->setDeveloperKey($google_developer_key);

$google_oauthV2 = new Google_Oauth2Service($gClient);

if (isset($_REQUEST['reset'])) {
	unset($_SESSION['token']);
	// unset($_SESSION['admin_ok']);
	setcookie('login_ok', '', time() - 60, '/', 'https://www.cyberphoto.se');
	setcookie('login_name', '', time() - 60, '/', 'https://www.cyberphoto.se');
	setcookie('login_userid', '', time() - 60, '/', 'https://www.cyberphoto.se');
	setcookie('login_mail', '', time() - 60, '/', 'https://www.cyberphoto.se');
	// unset($_SESSION['admin_info']);
	// unset($_SESSION['admin_userid']);
	$gClient->revokeToken();
	header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL)); //redirect user back to page
}

if (isset($_GET['code'])) { 
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
	return;
}

if (isset($_SESSION['token'])) 
{ 
	$gClient->setAccessToken($_SESSION['token']);
}


if ($gClient->getAccessToken()) {
	  $user 				= $google_oauthV2->userinfo->get();
	  $user_id 				= $user['id'];
	  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
	  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
	  $_SESSION['token'] 	= $gClient->getAccessToken();
} else {
	$authUrl = $gClient->createAuthUrl();
}


if(isset($authUrl)) { 
	header('Location: ' . $authUrl . '');
} else {
	if ($user['hd'] == 'cyberphoto.nu') {
		// $_SESSION['admin_ok'] 	= true;
		// $_SESSION['admin_info'] = $user;
		require_once("CWebADIntern.php");
		$intern = new CWebADIntern();
		// $_SESSION['admin_userid'] 	= $intern->findUserId($email);
		setcookie('login_ok', 'true', time() + 36000, '/', 'https://www.cyberphoto.se');
		setcookie('login_name', $user_name, time() + 36000, '/', 'https://www.cyberphoto.se');
		setcookie('login_userid', $intern->findUserId($email), time() + 36000, '/', 'https://www.cyberphoto.se');
		setcookie('login_mail', $email, time() + 36000, '/', 'https://www.cyberphoto.se');
		if ($_SESSION['admin_from_product']) {
			unset($_SESSION['admin_from_product']);
			$currentUrl = $_SESSION['admin_rem_page'];
			unset($_SESSION['admin_rem_page']);
			header("Location: $currentUrl");
			/*
			echo '<!DOCTYPE HTML><html>';
			echo '<head>';
			// echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/order/admin/global.css?ver=ad" . date("ynjGi") . "\">\n";
			echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
			echo '<title>Login with Google</title>';
			echo '</head>';
			echo '<body>';
			echo "<h1>Välkommen <span class=\"user_name\">" . $_SESSION['admin_info']['name'] . "</span></h1>";
			echo "<div class=\"login_info\">Du kan nu nyttja webbfunktionerna på sidan fullt ut.</div>";
			echo "<br><br><div class=\"top20\"><button type=\"button\" onclick=\"javascript:window.close();\">Stäng detta fönster</button></div>\n";
			echo '</body></html>';
			*/
		} elseif ($fi) {
			header('Location: http://www.cyberphoto.fi/order/admin?login=yes');
		} elseif ($no) {
			header('Location: http://www.cyberphoto.no/order/admin?login=yes');
		} elseif ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
			header('Location: http://admin.cyberphoto.se/?login=yes');
		} else {
			header('Location: http://www.cyberphoto.se/order/admin?login=yes');
		}
	} else {
		echo '<!DOCTYPE HTML><html>';
		echo '<head>';
		echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
		echo '<title>Login with Google</title>';
		echo '</head>';
		echo '<body>';
		echo '<h1>Meddelande</h1>';
		echo 'Inloggad: '.$user_name;
		echo '<br /><br /><a href="'.$profile_url.'" target="_blank"><img src="'.$profile_image_url.'?sz=50" /></a>';
		echo '<br /><a class="logout" href="?reset=1">Logout</a>';
		echo '<br /><br />Detta är inte ett Google konto som godkänns av CyberPhoto.';
		echo '<br />Det <u>måste</u> vara ett cyberphoto.nu konto.';
		echo '<br /><br />Kontakta systemadministratören för mer information.';
		echo '</body></html>';
	}
}

?>


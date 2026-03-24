<?php
	$HTTPS=$_SERVER['HTTPS']; // behövs för php5.4, $HTTPS funkar inte längre
	$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
	if ($_SERVER['REMOTE_ADDR'] == "195.159.106.218") { // norrman som gäckar oss
		exit;
	}
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
		echo "Cookie mail: " . $_COOKIE['login_mail'];
		//error_reporting(E_WARNING );
	}
	$printview = false;
	if ($_GET['printview'] == "yes") {
		$printview = true;
	}
	$frameless = true;
	$can_not_order = false;
	require_once ("CCheckIpNumber.php");
	if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR'])) { // ****************** DENNA SKALL TA BORT NÄR VI GÅR LIVE. VIKTIGT!! **********************
		// echo "<div style=\"text-align: center; margin-top: 50px;\"><img border=\"0\" src=\"/info_140301.png\"></div>\n";
		// exit;
		// $host     = $_SERVER['HTTP_HOST'];
		// header("Location: http://$host");
	}

	if (preg_match("/mobil\.cyberphoto\.se/i", $_SERVER["HTTP_HOST"]) && !$no && !$fi) { // kasta besökaren vidare till mobilsidan om mobil.cyberphoto.se
		
		header("Location: https://www.cyberphoto.se/m/");
		exit;
	}
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
		/*
		if ($_SERVER['REQUEST_URI'] == "/foto-video/systemkameror") {
			echo "jepp";
		}
		*/
		// echo phpinfo();
		// echo Locs::getDomainName();
	}
	// om vi har en gammalmodig länk till produkten så fixar vi till det. Exempel: http://www.cyberphoto.se/?http://www.cyberphoto.se/info.php?article=nid4
	$oldparams   = $_SERVER['QUERY_STRING'];
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
		echo $oldparams;
	}
	if ($oldparams != null) {
		if (substr( $oldparams, 0, 42 ) === "https://www.cyberphoto.se/info.php?article=") {
			header("Location: $oldparams");
		}
		if (preg_match("/\/connected\/no_crm\.php\?mail\=/i", $oldparams)) {
			$oldparams = preg_replace('/\/connected\/no_crm\.php/i', 'avboka-crm', $oldparams);
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
				echo $oldparams;
			}
			header("Location: $oldparams");
		}
		if (preg_match("/kollinr\=/i", $oldparams)) {
			$oldparams = preg_replace('/kollinr\=/i', 'sok-kollinummer?parcel=', $oldparams);
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98x") {
				echo $oldparams;
			}
			header("Location: $oldparams");
		}
	}
	require_once ("CBasket.php");
	
	require_once('CArticleFunctions.php');
	spl_autoload_register(function ($class) {
		include $class . '.php';
	});
	
	session_start();
	include ("session_vars.php");
	
	include ("translate.php");
		
	$bask = new CBasket();
	$menu = new CMenu();
	$mostsold = new CMostSoldProducts();
	$departure = new CDeparture();
	$start = new CCheckStart();
	$front = new CMostSoldFront();
	$news = new CNews();
	$pricelist = new CPricelist();
	$csearch = new CSearchLogg();
	$price = new CPriceSelected();
	$order = new CStatus();
	$blogg = new CBlogg();
	$faq = new CFaq();
	$campaign = new CCampaignCheck();
	$style = new CStyleCode();
	$sub = new CCategories();
	$product = new CProduct();
	$passwd = new PasswordEncryption();
	$newsletter = new CNewsLetter();
	$webkolli = new CWebKollinr();
	$products = new CProduct();
	$banners = new CBanners();
	$samsung_products = new CGetProducts();
	$tech = new CTechnicalData();
	$posten = new CPosten();
	$message = new CMessage();
	$cms = new CCms();
	$seo = new CSeo();
	$adintern = new CWebADIntern();
	$orderi = new CADOrderInfo();
	$butiken = new CButiken();
	
	if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nuX') {
		echo Locs::getDomainName();
	}

	// här kastar vi om gamla info-sidan till den nya friendly URL länken
	if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nuX') {
		if (preg_match("/\/info\.php\?article\=/", $_SERVER['REQUEST_URI'])) {
			
			$MFU_artnr = $_GET['article'];
			$redirect_301_linc = CProduct::getProductFriendlyURL($MFU_artnr);
			$redirect_301_linc = "http://" . $_SERVER["HTTP_HOST"] . "" . $redirect_301_linc;
			// echo $redirect_301_linc;

			header("HTTP/1.1 301 Moved Permanently");
			header("Location: $redirect_301_linc");
			die();

		}
	}
	
	// tobias special
	if ($article == "legriaminisilver" && !$bask->checkStoreStatus("legriaminisilver")) {
			header("Location: https://www.cyberphoto.se/info.php?article=legriaminirod");
			die();
	}
	
	$to_launch_seconds = strtotime("2014-02-28 17:00:00") - time();

	$to_launch_days = floor($to_launch_seconds / 86400);
	$to_launch_seconds %= 86400;

	$to_launch_hours = floor($to_launch_seconds / 3600);
	$to_launch_seconds %= 3600;

	$to_launch_minutes = floor($to_launch_seconds / 60);
	$to_launch_seconds %= 60;

	if ($to_launch_seconds < 1) {
		$to_launch ="Nya sajten är lanserad";
	} elseif ($to_launch_days < 1 && $to_launch_hours < 1) {
		$to_launch = $to_launch_minutes . " min";
	} elseif ($to_launch_days < 1) {
		$to_launch = $to_launch_hours . " tim " . $to_launch_minutes . " min";
	} else {
		$to_launch = $to_launch_days . " dag " .  $to_launch_hours . " tim " . $to_launch_minutes . " min";
	}

	// plocka fram SEO informationen
	// $rows = $seo->getSeoInfo($_SERVER['REQUEST_URI']);
	// if (!preg_match("/seo_change\.php/i", $_SERVER['PHP_SELF']) && !preg_match("/info\.php/i", $_SERVER['PHP_SELF'])) {
	if (!preg_match("/seo_change\.php/i", $_SERVER['PHP_SELF'])) {
		if (preg_match("/info\.php/i", $_SERVER['PHP_SELF']) || preg_match("/search\.php/i", $_SERVER['PHP_SELF'])) {
			$rows = $seo->getSeoInfo($_SERVER['REQUEST_URI']);
		} else {
			$rows = $seo->getSeoInfo($_SERVER['REDIRECT_URL']);
		}
		if ($fi && !$sv) {
			$seo_title = $rows->seoTitle_FI;
			$seo_canonical = $rows->seoCanonical_FI;
			$seo_metaDescription = $rows->seoMetaDescription_FI;
			$seo_h1 = $rows->seoH1_FI;
			$seo_h2 = $rows->seoH2_FI;
			$seo_body = $rows->seoBody_FI;
			$seo_body2 = $rows->seoBody2_FI;
		} elseif ($no) {
			$seo_title = $rows->seoTitle_NO;
			$seo_canonical = $rows->seoCanonical_NO;
			$seo_metaDescription = $rows->seoMetaDescription_NO;
			$seo_h1 = $rows->seoH1_NO;
			$seo_h2 = $rows->seoH2_NO;
			$seo_body = $rows->seoBody_NO;
			$seo_body2 = $rows->seoBody2_NO;
		} else {
			$seo_title = $rows->seoTitle_SE;
			$seo_canonical = $rows->seoCanonical_SE;
			$seo_metaDescription = $rows->seoMetaDescription_SE;
			$seo_h1 = $rows->seoH1_SE;
			$seo_h2 = $rows->seoH2_SE;
			$seo_body = $rows->seoBody_SE;
			$seo_body2 = $rows->seoBody2_SE;
		}
	}
	
	if (preg_match("/send_question\.php/i", $_SERVER['PHP_SELF'])) {
		if (!CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR']) && preg_match("/cyberphoto\.se/i", $senderMail)) {
			header("Location: /");
			exit;
		}
		if (preg_match("/\[url\=/i", $text)) {
			header("Location: /");
			exit;
		}
		if ($subj == "6" && $sv && !$fi) {
			header("Location: kundservice?MF=rma");
		}
	}
	if (preg_match("/kundvagn\.php/i", $_SERVER['PHP_SELF'])) {
		if ($pre_fast_cash == "yes") {
			require_once("CCookies.php");
			if (!preg_match("/fraktpost/i", "splitBasketToArray($kundvagn)")) {
				modifyBasket(fraktpost, 1, true);
				// modifyBasket(invoicefee, 1, true);
				header("Location: /kundvagn/checka-ut?new_cust=priv&one_stop=yes");
				exit;
			}
		}
		/*
		if ($pay == "") {
			$pay = "sveainvoice";
		}
		*/
	}
	if (preg_match("/presentkort\.php/i", $_SERVER['PHP_SELF'])) {

		$preview = false;
		$confirm_giftcard = false;
		
		if ($_POST['confirm'] == 134) {
			$confirm_giftcard = true;
			$card = $_POST['card'];
			$name = $_POST['name'];
			$psajt = $_POST['psajt'];
			if ($psajt == 1) {
				$_SESSION['presentkortsajt'] = 2;
			} else {
				$_SESSION['presentkortsajt'] = 1;
			}
			require_once ("CGiftCard.php");
			storeCardInformation($card, $name);
		} else {
			$preview = false;
			if ($psajt == "") {
				$psajt = 0;
			}
			if ($name == "") {
				$name = "Mottagare";
			}
			
			if ($_POST['value1'] > 0 || $_POST['value2'] > 0) {
				if ($_POST['value2'] > 0) {
					$card = $_POST['value2'];
				} else {
					$card = $_POST['value1'];
				}

				$preview = true;

				if (!is_numeric($card)) {
					$card = 0;
					$extra .= "<p class=\"giftcard_redmark\">Du får bara ange siffror för värdet på presentkortet</p>\n";
					$preview = false;
				}
				if(!ereg("^[0-9]+$", $card)) {
					$card = 0;
					$extra .= "<p class=\"giftcard_redmark\">Värdet på presentkortet måste vara i heltal</p>\n";
					$preview = false;
				}
				if ($card < 100 || $card > 30000) {
					$extra .= "<p class=\"giftcard_redmark\">Värdet på presentkortet måste ligga mellan 100 och 30 000 SEK</p>\n";
					$preview = false;
				}
				
				if ($_POST['name'] != "Mottagare") {
					$name = $_POST['name'];
				} else {
					$name = "";
				}
				$psajt = $_POST['psajt'];
			}
			if ($card == "") {
				$card = 0;
			}
		}
	
	}
	
	// rada upp variabler som behövs för produktsidan
	if (preg_match("/info\.php/i", $_SERVER['PHP_SELF'])) {
		include ("product_variables.php");
	}
	
	// om man begär återställningslänk, logga ut användaren
	if (preg_match("/send_recoverylinc\.php/i", $_SERVER['PHP_SELF'])) {
		$logout = "yes";
	}
	
	if (preg_match("/mypage\.php/i", $_SERVER['PHP_SELF']) || preg_match("/placeOrder\.php/i", $_SERVER['PHP_SELF'])) {
		if ($fi) {
			$currency = "EUR";
		} elseif ($no) {
			$currency = "NOK";
		} else {
			$currency = "SEK";
		}
	}

	// ta fram en länk som kan återanvändas vars som helst på sidan
	if ($HTTPS == "on") {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
	$host     = $_SERVER['HTTP_HOST'];
	$script   = $_SERVER['REQUEST_URI'];
	if (preg_match("/\?/i", $script)) {
		// $currentUrl = $protocol . '://' . $host . $script . '?' . $params;
		$currentUrl = $protocol . '://' . $host . $script;
		$firstvariable = '&';
	} else {
		$currentUrl = $protocol . '://' . $host . $script;
		$currentUrl = preg_replace("/index\.php/", "", $currentUrl);
		$firstvariable = '?';
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

		// header("Location: $currentUrl");
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: $currentUrl");
		die();

	}
	// säkra upp att vissa sidor måste/bör vara https
	if ($HTTPS != "on" && (preg_match("/mypage\.php/i", $_SERVER['PHP_SELF']) || preg_match("/kundvagn\.php/i", $_SERVER['PHP_SELF']) || preg_match("/kortMan\.php/i", $_SERVER['PHP_SELF']))) {
		$host     = $_SERVER['HTTP_HOST'];
		$script   = $_SERVER['REQUEST_URI'];
		$currentUrl = 'https://' . $host . $script;

		header("Location: $currentUrl");
	}
	
	// säkra upp att vissa sidor inte bör vara https
	if ($HTTPS == "on" && (preg_match("/info\.php/i", $_SERVER['PHP_SELF'])) && no == "more") {
		$host     = $_SERVER['HTTP_HOST'];
		$script   = $_SERVER['REQUEST_URI'];
		$currentUrl = 'http://' . $host . $script;

		header("Location: $currentUrl");
	}
	
	// 2014-10-08 fasa bort cybaigun
	if (preg_match("/cybairgun/i", $_SERVER['REQUEST_URI'])) {
		
		if (substr($_SERVER['REQUEST_URI'], -9) === "cybairgun" || substr($_SERVER['REQUEST_URI'], -10) === "cybairgun/") {

			// $currentUrl = "http://www.cyberphoto.se/jakt-fritid/utforsaljning/airsoft";
			// $currentUrl = "http://www.cyberphoto.se/outdoor/utforsaljning/airsoft";
			$currentUrl = "https://www.cyberphoto.se/outdoor";
		
		} else {
		
			$host     = $_SERVER['HTTP_HOST'];
			$script   = $_SERVER['REQUEST_URI'];
			$currentUrl = 'https://' . $host . $script;
			// $currentUrl = (preg_replace("/cybairgun/", "jakt-fritid", $currentUrl));
			$currentUrl = (preg_replace("/cybairgun/", "outdoor", $currentUrl));
			
		}

		header("Location: $currentUrl");
	}
	
	if ($logout == 'yes' || $loggaut == 'yes') { // behåller loggaut som backup utifall att...
		$bask->session_clear();
		$kundnrsave = "";
		$confirm = "";
		$loggaut = "";
		unset($old_foretag);
		//echo ".";
	}


	if ($_GET['beskrivning'] != "") { // detta är bara övergående för den gamla sökningen
		$q = $_GET['beskrivning'];
	} elseif ($_GET['q'] == "öppettider") {
		header("Location: https://www.cyberphoto.se/kundservice");
	} elseif ($_GET['q'] == "") {
		$q = l('Search here');
	} else {
		$q = $_GET['q'];
	}

	$mobil = false;
	$hobby = false;
	$batterier = false;
	$cybairgun = false;
	$development_environment_1440 = false;
		// fix department if necessary
	if ($cybairgun && $_SERVER["REQUEST_URI"] == '/') {
		$cybairgun = false;		
	}
	if ($no && !CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
		if (preg_match("/jakt\-fritid/i", $currentUrl) || preg_match("/outdoor/i", $currentUrl) || $product_hobby) {
			$hobby = true;
			$setwebsection = "jakt-fritid";
			setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
		} else {
			$mobil = true;
			$setwebsection = "mobil";
			setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
		}
	} elseif ($fi && !CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
		$mobil = true;
		$setwebsection = "mobil";
		setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
	} elseif (preg_match("/development_environment_1440/i", $currentUrl)) {
		$development_environment_1440 = true;
		$setwebsection = "development_environment_1440";
		// setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
	} elseif (preg_match("/mobiltelefoni/i", $currentUrl) || preg_match("/mobiili/i", $currentUrl) || $product_mobil) {
		$mobil = true;
		$setwebsection = "mobil";
		setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
	} elseif ((preg_match("/cybairgun/i", $currentUrl) || $product_cybairgun) && !$no) {
		$cybairgun = true;
		$setwebsection = "cybairgun";
		setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
	/*
	} elseif (preg_match("/batterier/i", $currentUrl) || preg_match("/akut/i", $currentUrl) || $product_batterier) {
		$batterier = true;
		$setwebsection = "batterier";
		setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
	*/
	} elseif (preg_match("/jakt\-fritid/i", $currentUrl) || preg_match("/outdoor/i", $currentUrl) || $product_hobby) {
		$hobby = true;
		$setwebsection = "jakt-fritid";
		setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
	} elseif (preg_match("/foto\-video/i", $currentUrl) || $product_foto || $_SERVER['REQUEST_URI'] == "/canon/" || $_SERVER['REQUEST_URI'] == "/gopro/") {
		$setwebsection = "foto-video";
		setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
	} else {
		if (isset($_COOKIE["websection"])) {
			if ($_COOKIE['websection'] == "development_environment_1440") {
				$development_environment_1440 = true;
			} elseif ($_COOKIE['websection'] == "mobil") {
				$mobil = true;
			} elseif ($_COOKIE['websection'] == "batterier") {
				$batterier = true;
			} elseif ($_COOKIE['websection'] == "jakt-fritid") {
				$hobby = true;
			} elseif ($_COOKIE['websection'] == "cybairgun") {
				if ($_SERVER["REQUEST_URI"] != '/') {
					$cybairgun = true;
				} else {
					$setwebsection = "foto-video";
					setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
				}
			}
		} else {
			$setwebsection = "foto-video";
			setcookie("websection", $setwebsection, strtotime( '+30 days' ), "/", Locs::getDomainName());
			// setcookie("websection", "", time() - 3600, "/", Locs::getDomainName());
		}
	}
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98x" ) {
		echo "currentUrl: " . $currentUrl . "<br>";
		echo "websection: " . $_COOKIE['websection'] . "<br>";
		print_r($_COOKIE);
	}
	
	// ta hand om eventuella ringa upp kunder
	if (preg_match("/info\.php/i", $_SERVER['PHP_SELF'])) {
		if ($_POST['contact_phone'] != "") {
			if (strlen($_POST['contact_phone']) < 5) {
				$abb_contact_mess = l('This number is too short and does not seem reasonable.');
			} else {
				$abb_contact_mess = l('Thank you, your number is sent to us.') . "<br>" . l('We will call back shortly.');
				CMobile::sendAbbContactMeMess($_POST['contact_phone'],$_POST['article']);
				// echo $_POST['contact_phone'];
			}
		}
	}
	
	// hämtar in alla huvudlänkar i topppen på sidan
	include ("main_link.php");
	
?>
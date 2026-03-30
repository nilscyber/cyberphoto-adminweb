<?php
	// Emulate register_globals: extract GET/POST/COOKIE into local scope
	// Legacy code relies on $var being available directly from $_GET['var'] etc.
	extract($_GET, EXTR_SKIP);
	extract($_POST, EXTR_SKIP);
	extract($_COOKIE, EXTR_SKIP);

	// 2018-11-27, hänvisar allt från och med nu till nya adressen
	// ob_start(); // starta output buffering
	// header('Content-Type: text/html; charset=utf-8');


	use Liuch\DmarcSrg\Statistics;

	if ($_SERVER['HTTP_HOST'] == "www2.cyberphoto.se") {
			echo "Denna sida är permanent flyttad. Se till att uppdatera ditt bokmärke eller länk!<br><br>";
			echo "<a href=\"https://admin.cyberphoto.se\">https://admin.cyberphoto.se/</a>";
			exit;
	}
	$HTTPS=$_SERVER['HTTPS']; // behövs för php5.4, $HTTPS funkar inte längre
	// echo $HTTPS;

	if ($HTTPS != "on") {
		$host     = $_SERVER['HTTP_HOST'];
		$script   = $_SERVER['REQUEST_URI'];
		$currentUrl = 'https://' . $host . $script;

		header("Location: $currentUrl");
	}
	
	// echo phpinfo();
	spl_autoload_register(function ($class) {
		include $class . '.php';
	});

	session_start();
	include ("translate.php");

	/// ******** DETTA HAR MED M365 INLOGGNING ATT GÖRA
	$config = require_once 'config.php';

	// Generera state för säkerhet
	$state = md5(uniqid(mt_rand(), true));
	$_SESSION['oauth_state'] = $state;

	// Skapa inloggningslänk
	$auth_params = [
		'client_id' => $config['client_id'],
		'response_type' => 'code',
		'redirect_uri' => $config['redirect_uri'],
		'scope' => $config['scope'],
		'state' => $state,
		'response_mode' => 'query'
	];

	$auth_link = $config['auth_url'] . '?' . http_build_query($auth_params);

	// $_SESSION['return_to'] = $_SERVER['HTTP_REFERER'];
	$_SESSION['return_to'] = $currentUrl;
	
	/// ******** FRAM TILL HIT

	// if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu') {
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
			echo "nya admin";
		} elseif ($_SERVER['HTTP_HOST'] == "www2.cyberphoto.se") {
			echo "gamla admin";
		}
		// $sfsdfsdf = "2015-12-22 23:49:59";
		// $sfsdfsdf = "";
		// setcookie("testOR", $sfsdfsdf, strtotime( '+1 days' ), "/", "cyberphoto.se");
		// setcookie("testOR", $sfsdfsdf, time() + 36000, "/", "cyberphoto.se");
		// setcookie("testOR", $sfsdfsdf, time() - 3600, "/", "cyberphoto.se");
	}
	
	unset($_SESSION['admin_ok']);
	unset($_SESSION['admin_info']);
	unset($_SESSION['admin_userid']);

	$protocol = 'https';
	$host     = $_SERVER['HTTP_HOST'];
	$script   = $_SERVER['SCRIPT_NAME'];
	$params   = $_SERVER['QUERY_STRING'];
	if ($params != null) {
		$currentUrl = $protocol . '://' . $host . $script . '?' . $params;
	} else {
		$currentUrl = $protocol . '://' . $host . $script;
	}
	// header("Location: $currentUrl");
	// echo $currentUrl;
	$_SESSION['return_to'] = $currentUrl;
	// include_once("top_no.php");
	$cyberadmin = true;
	
	// require_once("CBlogg.php");
	$blogg = new CBlogg();
	// require_once("CCyberAdmin.php");
	$admin = new CCyberAdmin();
    require_once ("CTurnOver.php");
	// $turnoverold = new CTurnOver_v2();
	$turnover = new CTurnOverNew();
	// require_once ("CStoreStatus.php");
	$store = new CStoreStatus();
	// require_once("CWebADInternSuplier.php");
	$adintern = new CWebADInternSuplier();
	// require_once("CMonitorArticles.php");
	$monitor = new CMonitorArticles();
	// require_once("CWebADIntern.php");
	$intern = new CWebADIntern();
	// require_once ("CSoldArticles.php");
	$sold = new CSoldArticles();
	// require_once("CBlacklist.php");
	$blacklist = new CBlacklist();
	// require_once ("CParcelCheck.php");
	$parcel = new CParcelCheck();
	// require_once ("CBanners.php");
	$banners = new CBanners();
	// require_once ("CRma.php");
	$rma = new CRma();
	// require_once ("CAllocated.php");
	$allocated = new CAllocated();
	// require_once ("CMobile.php");
	$mobile = new CMobile();
	// require_once ("CDeparture.php");
	$departure = new CDeparture();
	// require_once ("CPriceSelected.php");
	$price = new CPriceSelected();
	// require_once ("CAdmin.php");
	$adtrigger = new CAdmin();
	// require_once ("CCheckArticles.php");
	$check = new CCheckArticles();
	// require_once("CCampaignCheck.php");
	$campaign = new CCampaignCheck();
	// require_once ("CCPto.php");
	$cpto = new CCPto();
	// require_once ("CEmployees.php");
	$employees = new CEmployees();
	// require_once ("CBasket.php");
	$bask = new CBasket();
	// require_once ("CStatus_newAD.php");
	$status = new CStatus();
	// require_once ("CWebKollinr.php");
	$webkolli = new CWebKollinr();
	// require_once("CADOrderInfo.php");
	$orderi = new CADOrderInfo();
	// require_once("CSearchLogg.php");
	$csearch = new CSearchLogg();
	// require_once ("PasswordEncryption.php");
	$passwd = new PasswordEncryption();
	$menu = new CMenu();
	$tech = new CTechnicalData();
	$statistics = new CStatistics();
	$product = new CProduct();
	$cms = new CCms();
	$sales = new CSales();
	$filter = new CFilterIncoming();
	require_once ("Tools.php");
	// require_once("COtrs.php");
	$otrs = new COtrs();
	require_once("CCheckIpNumber.php");
	$tradein = new CTradeIn();
	$butiken = new CButiken();
	$temp = new CTemp();
	$style = new CStyleCode();
	$pricelist = new CPricelist();
	// $adintern = new CWebADIntern();
	$search_bp = new CBusinessPartner();
	$adminstat = new CAdminStat();
	$crontab = new CCrontab();
	$tool = new CUsedProducts();
	
	// här kollar vi snabbfiltren som användaren kan lagra
	$hideTradeIn = (!empty($_COOKIE['pref_hide_tradein']) && $_COOKIE['pref_hide_tradein'] === '1');
	$hideDemo = (!empty($_COOKIE['pref_hide_demo']) && $_COOKIE['pref_hide_demo'] === '1');
	/*
	// Sätt sedan in det som en global i en funktion för att jobba med det på andra sidor
	function getSomething() {
		global $hideTradeIn;
		if ($hideTradeIn) {
			...
		}
	}
	*/
	if (preg_match("/dos_product\.php/i", $_SERVER['PHP_SELF'])) {

		$host     = $_SERVER['HTTP_HOST'];
		$script   = $_SERVER['REQUEST_URI'];
		$currentUrl = 'https://' . $host . $script;
		
		if ($_SERVER['REMOTE_ADDR'] == '192.168.1.89' || $_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu') {
			// phpinfo();
		}


		$beskrivning = (isset($_GET['q']) ? trim($_GET['q']) : null);
		$newsearch = (isset($_GET['s']) ? trim($_GET['s']) : null);

		$searchwords = preg_split("/[\s]+/", $beskrivning);

		// ...och denna rad
		$searchwords = str_replace("eosm", "eos m", $searchwords);

		$criteria .= "WHERE " ;

		if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR']) || CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) { // internt i huset ger vi möjlighet att få träff på vårt eget artikel nummer
			$criteria .= "( " ;
		}
		$criteria .= "( ";

		for ($i = 0; $i < count($searchwords);$i++) {

				if ($fi && !$sv) {
					if ($i == 0) {
						$criteria .= "kategori_fi like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar_fi.beskrivning_fi like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%' OR Artiklar_fi.kommentar_fi like '%" . $searchwords[$i] . "%') ";
					} else {
						$criteria .= "AND (kategori_fi like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar_fi.beskrivning_fi like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%' OR Artiklar_fi.kommentar_fi like '%" . $searchwords[$i] . "%') ";
						$morethenoneword = true;
					}
				} elseif ($fi && $sv) {
					if ($i == 0) {
						$criteria .= "kategori like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
					} else {
						$criteria .= "AND (kategori like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
						$morethenoneword = true;
					}
				} elseif ($no) {
					if ($i == 0) {
						$criteria .= "kategori_no like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
					} else {
						$criteria .= "AND (kategori_no like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
						$morethenoneword = true;
					}
				} else {
					if ($i == 0) {
						if ($sortera == "discontinued" || $sortera == "old_tradein") { // ta bort sökalias om utgångna eller inbytesprodukter
							$criteria .= "kategori like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
						} else {
							$criteria .= "kategori like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
						}
					} else {
						if ($sortera == "discontinued" || $sortera == "old_tradein") { // ta bort sökalias om utgångna eller inbytesprodukter
							$criteria .= "AND (kategori like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
						} else {
							$criteria .= "AND (kategori like '%" . $searchwords[$i] . "%' OR tillverkare like '%" . $searchwords[$i] . "%' OR Artiklar.beskrivning like '%" . $searchwords[$i] . "%' OR Artiklar.searchTerms like '%" . $searchwords[$i] . "%' OR Artiklar.kommentar like '%" . $searchwords[$i] . "%') ";
						}
						$morethenoneword = true;
					}
				}
		}

		if (CCheckIP::checkIpAdress($_SERVER['REMOTE_ADDR']) || CCheckIP::checkIpAdressLagershop($_SERVER['REMOTE_ADDR'])) { // internt i huset ger vi möjlighet att få träff på vårt eget artikel nummer
			$criteria .= "OR Artiklar.artnr = '" . $beskrivning . "') " ;
		}

		// denna ligger numera i std_instore.php, se nedan
		// $criteria .= "AND NOT (Artiklar.kategori_id IN (396,486,501,509,513)) " ;
		
		// $criteria .= "AND NOT (Artiklar.kategori_id IN (1000260,1000265)) " ;
		if (!CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			$criteria .= "AND NOT (Artiklar.kategori_id IN (1000260,1000267,1000270)) " ; // dolda kategorier som inte skall hittas via sökning
		}

		include ("std_instore.php");
		include ("std_sortby.php");

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			// echo strlen($beskrivning);
			// echo $criteria;
			echo $newsearch;
			// echo $num_of_rows;
			exit;
		}
		
		if ($beskrivning == "" || (strlen($beskrivning) < 2)) {
			$emptysearch = true;
		}
		if ($newsearch == "yes") {
			$clearsearch = true;
		}


	}

	if (preg_match("/dos_customer\.php/i", $_SERVER['PHP_SELF'])) {

		$host     = $_SERVER['HTTP_HOST'];
		$script   = $_SERVER['REQUEST_URI'];
		$currentUrl = 'https://' . $host . $script;
		
		if ($_SERVER['REMOTE_ADDR'] == '192.168.1.89' || $_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu') {
			// phpinfo();
		}


		$beskrivning = (isset($_GET['q']) ? trim($_GET['q']) : null);
		$newsearch = (isset($_GET['s']) ? trim($_GET['s']) : null);

		$searchwords = preg_split("/[\s]+/", $beskrivning);

		if ($beskrivning == "" || (strlen($beskrivning) < 2)) {
			$emptysearch = true;
		}
		if ($newsearch == "yes") {
			$clearsearch = true;
		}


	}

	if (preg_match("/dos_order\.php/i", $_SERVER['PHP_SELF'])) {

		$host     = $_SERVER['HTTP_HOST'];
		$script   = $_SERVER['REQUEST_URI'];
		$currentUrl = 'https://' . $host . $script;
		
		if ($_SERVER['REMOTE_ADDR'] == '192.168.1.89' || $_COOKIE['login_mail'] == 'sjabo@cyberphoto.nu') {
			// phpinfo();
		}


		$beskrivning = (isset($_GET['q']) ? trim($_GET['q']) : null);
		$newsearch = (isset($_GET['s']) ? trim($_GET['s']) : null);

		$searchwords = preg_split("/[\s]+/", $beskrivning);

		if ($beskrivning == "" || (strlen($beskrivning) < 2)) {
			$emptysearch = true;
		}
		if ($newsearch == "yes") {
			$clearsearch = true;
		}


	}
	
	if (preg_match("/check_external\.php/i", $_SERVER['PHP_SELF'])) { 
		if ($switch_external == "yes") {
			$_SESSION['EXTERNAL_SWITCH'] = 1;
			header("Location: https://www.cyberphoto.se/order/admin/check_external.php");
			exit;
		} elseif ($switch_external == "no") {
			unset ($_SESSION['EXTERNAL_SWITCH']);
			header("Location: https://www.cyberphoto.se/order/admin/check_external.php");
			exit;
		}
	}
	if (preg_match("/salesreport\.php/i", $_SERVER['PHP_SELF']) || preg_match("/logistik\.php/i", $_SERVER['PHP_SELF'])) { 
		$ref_dagensdatum = date("Y-m-d");
		if ($firstinput != "") {
			$dagensdatum = $firstinput;
		} else {
			$dagensdatum = date("Y-m-d");
		}
	}
	if (preg_match("/inbyte_wishlist\.php/i", $_SERVER['PHP_SELF'])) {

		if ($change != "") {

			$rows = $tradein->getWishlistSpec($change);

			$addID = $rows->tiID;
			$addActive = $rows->tiActive;
			$addProduct = $rows->tiProduct;
			$addNote = $rows->tiNote;
			$addLinc = $rows->tiLinc;
			$addBy = $rows->tiBy;

		}

		if ($subm) {
			
			$olright = true;
			
			if ($addProduct == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange en produkt</p>";
			}
			if ($addBy == "") {
				if ($_COOKIE['login_ok'] != "true") {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att lägga till en post</p>";
				} else {
					$addBy = $_COOKIE['login_mail'];
				}
			}
			if ($olright) {
				$tradein->doWishlistAdd($addProduct,$addNote,$addLinc,$addBy);
				header("Location: https://admin.cyberphoto.se/inbyte_wishlist.php");
				exit;
			}

		}
		if ($submC) {
			
			$olright = true;

			if ($addActive == "yes") {
				$addActive = 1;
			} else {
				$addActive = 0;
			}
			if ($addProduct == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange en produkt</p>";
			}
			if ($addNote == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange en kort förklaring</p>";
			}
			if ($olright) {
				$tradein->doWishlistChange($addID,$addActive,$addProduct,$addNote,$addLinc);
				header("Location: https://admin.cyberphoto.se/inbyte_wishlist.php");
				exit;
			}
		}

	}
	if (preg_match("/monitor_articles\.php/i", $_SERVER['PHP_SELF'])) {

		if ($change != "") {

			$rows = $monitor->getMonAlerts($change);

			$addID = $rows->monID;
			$addArtnr = $rows->monArtnr;
			$addMoreLess = $rows->monMoreLess;
			$addStoreValue = $rows->monStoreValue;
			$addRecipient = $rows->monUser;
			$addActive = $rows->monActive;
			$addComment = $rows->monComment;
			$addType = $rows->monType;

		}

		if ($subm) {
			
			$olright = true;
			
			if ($addArtnr == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange ett artikel nummer!</p>";
			}
			if ($addStoreValue == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange ett värde</p>";
			}
			if (!is_numeric($addStoreValue) && $addStoreValue != "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Värdet för endast vara numeriskt!</p>";
			}
			if ($addArtnr != "") {
				if (!($monitor->check_artikel_status($addArtnr) == $addArtnr)) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Detta artikel nummer finns inte. Vänligen kolla upp detta! (måste skrivas exakt)</p>";
				}
			}
			if ($addArtnr != "" && $addStoreValue != "" && $addType == 3) {
				if (!$monitor->check_artikel_on_order($addStoreValue,$addArtnr)) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Denna artikel finns inte på denna order. Vänligen kolla upp detta!</p>";
				}
			}
			if ($addRecipient == "") {
				if ($_COOKIE['login_ok'] != "true") {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem som skall ha aviseringen eller vara inloggad!</p>";
				} else {
					$addRecipient = $_COOKIE['login_mail'];
				}
			}
			if ($olright) {
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
					$monitor->doMonitorAdd_v3($addArtnr,$addRecipient,$addMoreLess,$addStoreValue,$addComment,$addType);
				} else {
					$monitor->doMonitorAdd_v3($addArtnr,$addRecipient,$addMoreLess,$addStoreValue,$addComment,$addType);
				}
				if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
					header("Location: https://admin.cyberphoto.se/monitor_articles.php");
				} else {
					header("Location: https://www.cyberphoto.se/order/admin/monitor_articles.php");
				}
				exit;
			}

		}
		if ($submC) {
			
			$olright = true;

			if ($addActive == "yes") {
				$addActive = 1;
			} else {
				$addActive = 0;
			}
			if ($addStoreValue == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange ett värde</p>";
			}
			if (!is_numeric($addStoreValue) && $addStoreValue != "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Värdet för endast vara numeriskt!</p>";
			}
			if ($addArtnr == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange ett artikel nummer!</p>";
			}
			if ($addArtnr != "") {
				if (!($monitor->check_artikel_status($addArtnr) == $addArtnr)) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Detta artikel nummer finns inte. Vänligen kolla upp detta! (måste skrivas exakt)</p>";
				}
			}
			if ($addArtnr != "" && $addStoreValue != "" && $addType == 3) {
				if (!$monitor->check_artikel_on_order($addStoreValue,$addArtnr)) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Denna artikel finns inte på denna order. Vänligen kolla upp detta!</p>";
				}
			}
			if ($addRecipient == "") {
				if ($_COOKIE['login_ok'] != "true") {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem som skall ha aviseringen eller vara inloggad!</p>";
				} else {
					$addRecipient = $_COOKIE['login_mail'];
				}
			}
			if ($olright) {
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
					$monitor->doMonitorChange_v3($addID,$addArtnr,$addRecipient,$addMoreLess,$addStoreValue,$addActive,$addComment,$addType);
				} else {
					$monitor->doMonitorChange_v3($addID,$addArtnr,$addRecipient,$addMoreLess,$addStoreValue,$addActive,$addComment,$addType);
				}
				if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
					header("Location: https://admin.cyberphoto.se/monitor_articles.php");
				} else {
					header("Location: https://www.cyberphoto.se/order/admin/monitor_articles.php");
				}
				exit;
			}
		}

	}
	if (preg_match("/bloggen\.php/i", $_SERVER['PHP_SELF'])) {
		if ($accept > 0) {
			$blogg->AcceptComment($accept);
			if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
				header("Location: https://admin.cyberphoto.se/blogg.php");
			} else {
				header("Location: https://www.cyberphoto.se/order/admin/blogg.php");
			}
			exit;
		}
		if ($deny > 0) {
			$blogg->DenyComment($deny);
			if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
				header("Location: https://admin.cyberphoto.se/blogg.php");
			} else {
				header("Location: https://www.cyberphoto.se/order/admin/blogg.php");
			}
			exit;
		}

	}
	if (preg_match("/kommande_bloggar\.php/i", $_SERVER['PHP_SELF'])) {
		if ($delete > 0) {
			$blogg->deleteBlogg($delete);
			if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
				header("Location: https://admin.cyberphoto.se/kommande_bloggar.php");
			} else {
				header("Location: https://www.cyberphoto.se/order/admin/kommande_bloggar.php");
			}
			exit;
		}

	}
	if (preg_match("/blacklist\.php/i", $_SERVER['PHP_SELF'])) {

		if ($change != "") {

			$rows = $blacklist->getBlacklistRow($change);

			$addID = $rows->blackID;
			$addIP = $rows->blackIP;
			$addRecipient = $rows->blackBy;
			$addActive = $rows->blackActive;
			$addComment = $rows->blackNote;

		}

		if ($subm) {
			
			$olright = true;
			
			if ($addIP == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange en IP-adress!</p>";
			}
			if ($addIP != "" && $blacklist->checkIfDuplicateIPnumber($addIP)) {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- IP-adress finns redan upplagd, kan ej lägga upp dubblett!</p>";
			}
			if ($addRecipient == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem som lägger upp denna bevakning!</p>";
			}
			if ($olright) {
				$blacklist->doBlacklistAdd($addIP,$addRecipient,$addComment);
				if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
					header("Location: https://admin.cyberphoto.se/blacklist.php");
				} else {
					header("Location: https://www.cyberphoto.se/order/admin/blacklist.php");
				}
				exit;
			}

		}
		if ($submC) {
			
			$olright = true;

			if ($addActive == "yes") {
				$addActive = 1;
			} else {
				$addActive = 0;
			}
			if ($addIP == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange en IP-adress!</p>";
			}
			if ($addIP != "" && $blacklist->checkIfDuplicateIPnumber($addIP)) {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- IP-adress finns redan upplagd, kan ej lägga upp dubblett!</p>";
			}
			if ($addRecipient == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem som lägger upp denna bevakning!</p>";
			}
			if ($olright) {
				$blacklist->doBlacklistChange($addID,$addIP,$addRecipient,$addActive,$addComment);
				if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
					header("Location: https://admin.cyberphoto.se/blacklist.php");
				} else {
					header("Location: https://www.cyberphoto.se/order/admin/blacklist.php");
				}
				exit;
			}
		}

	}
	if (preg_match("/lagerstatus\.php/i", $_SERVER['PHP_SELF'])) {
		

	}
	
	if (preg_match("/banners\.php/i", $_SERVER['PHP_SELF'])) {

		// Site och department är alltid Sverige (1) och Foto-video (1)
		$_SESSION['bannersite']       = 1;
		$_SESSION['bannerdepartment'] = 1;

		// Sektion: uppdatera från GET om valt, annars defaulta till 201
		if ($choose_section != "") {
			if ($choose_section > 0) {
				$_SESSION['bannersection'] = $choose_section;
			} else {
				$_SESSION['bannersection'] = 201;
			}
		}
		if (empty($_SESSION['bannersection'])) {
			$_SESSION['bannersection'] = 201;
		}

		if ($change != "") {

			$rows = $banners->getSpecFrontBanner($change);

			$addid = $rows->frontID;
			$addfrom = substr ($rows->frontDateFrom, 0, 19);
			if ($now == "yes") {
				$addto = date("Y-m-d H:i:s", time());
			} else {
				$addto = substr ($rows->frontDateTo, 0, 19);
			}
			$section = $rows->frontSection;
			$addsection = $rows->frontSection;
			$addpicture = $rows->frontPicture;
			$addartnr = $rows->frontArtNr;
			$addlinc = $rows->frontLinc;
			$addstore = $rows->frontAllowNull;
			$addcomment = $rows->frontComment;
			$addleverantor = $rows->frontLeverantor;
			$addcreatedby = $rows->frontCreatedBy;
			$addprio = $rows->frontPrio;
			$addsort = $rows->frontSort;
			$addcategory = $rows->frontCategory;
			
		}

		if ($copypost != "") {

			$rows = $banners->getSpecFrontBanner($copypost);

			$addidc = $rows->frontID;
			// $addfrom = substr ($rows->frontDateFrom, 0, 19);
			
			$timefrom = preg_replace('/:[0-9][0-9][0-9]/','', $rows->frontDateFrom);
			$timefrom = strtotime($timefrom);
			if ($timefrom > time()) {
				$addfrom = date("Y-m-d H:i:s", $timefrom);
			} else {
				$addfrom = date("Y-m-d H:i:s", time());
			}
				
			$timeto = preg_replace('/:[0-9][0-9][0-9]/','', $rows->frontDateTo);
			$timeto = strtotime($timeto);
			if ($timeto < time()) {
				unset($timeto);
			} else {
				$addto = date("Y-m-d H:i:s", $timeto);
			}
				
			// $addfrom = date("Y-m-d H:i:s", time());
			// $addto = substr ($rows->frontDateTo, 0, 19);
			$section = $rows->frontSection;
			$addsection = $rows->frontSection;
			$addpicture = $rows->frontPicture;
			$addartnr = $rows->frontArtNr;
			$addlinc = $rows->frontLinc;
			$addstore = $rows->frontAllowNull;
			$addleverantor = $rows->frontLeverantor;
			$addprio = $rows->frontPrio;
			$addsort = $rows->frontSort;
		}


		if ($delete != "") {
			$banners->FrontAdminDelete($delete,$section);
		}
		if ($extend > 7000) {
			$banners->FrontAdminExtend($extend);
		}
		if ($endnow > 7000) {
			$banners->FrontAdminEnd($endnow);
		}
		if ($subm) {

			$olright      = true;
			$addcreatedby = $_COOKIE['login_mail'];
			$section      = $addsection;
			$addsite      = $_SESSION['bannerdepartment'];
			$addstore     = -1; // alltid tillåt slut i lager
			$addprio      = 0;

			if ($addfrom == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Datum för när den skall publiseras får inte vara tomt!</p>";
			}
			if ($addfrom != "" && !$banners->isValidDateTime($addfrom)) {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
			}
			if ($addto == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Datum för hur länge den skall ligga får inte vara tomt!</p>";
			}
			if ($addto != "" && !$banners->isValidDateTime($addto)) {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
			}
			if ($addpicture == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Bild måste laddas upp!</p>";
			}
			if ($addcreatedby == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}

			if ($olright) {
				$banners->FrontAdminAdd($addsection,$addfrom,$addto,$addpicture,'','', $addstore,null,0,$addcreatedby,$addsite,$addprio,0,0);
				header('Location: ' . $_SERVER['PHP_SELF']);
				exit;
			}

		}
		if ($submC) {

			$olright      = true;
			$addcreatedby = $_COOKIE['login_mail'];
			$section      = $addsection;
			$addstore     = -1;
			$addprio      = 0;

			if ($addfrom == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Datum för när den skall publiseras får inte vara tomt!</p>";
			}
			if ($addfrom != "" && !$banners->isValidDateTime($addfrom)) {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
			}
			if ($addto == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Datum för hur länge den skall ligga får inte vara tomt!</p>";
			}
			if ($addto != "" && !$banners->isValidDateTime($addto)) {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
			}
			if ($addpicture == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Bild måste laddas upp!</p>";
			}
			if ($addcreatedby == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}
			if ($olright) {
				$banners->FrontAdminChange($addid,$addsection,$addfrom,$addto,$addpicture,'','', $addstore,null,0,$addcreatedby,$addprio,0,0);
				header('Location: ' . $_SERVER['PHP_SELF']);
				exit;
			}
		}
		
		
	}
	
	if (preg_match("/Tekn_/i", $_SERVER['PHP_SELF'])) {
	
		if ($change != "" || $copypost != "") {

			if ($copypost != "") {
				$rows = $tech->getSpecTech($copypost);
				$addidc = $rows->artnr;
			} else {
				$rows = $tech->getSpecTech($change);
				$addid = $rows->artnr;
			}
			
			$params1 = $rows->params1;
			$params2 = $rows->params2;
			$params3 = $rows->params3;
			$params4 = $rows->params4;
			$params5 = $rows->params5;
			$params6 = $rows->params6;
			$params7 = $rows->params7;
			$params8 = $rows->params8;
			$params9 = $rows->params9;
			$params10 = $rows->params10;
			$params11 = $rows->params11;
			$params12 = $rows->params12;
			$params13 = $rows->params13;
			$params14 = $rows->params14;
			$params15 = $rows->params15;
			$params16 = $rows->params16;
			$params17 = $rows->params17;
			$params18 = $rows->params18;
			$params19 = $rows->params19;
			$params20 = $rows->params20;
			$params21 = $rows->params21;
			$params22 = $rows->params22;
			$params23 = $rows->params23;
			$params24 = $rows->params24;
			$params25 = $rows->params25;
			$params26 = $rows->params26;
			$params27 = $rows->params27;
			$params28 = $rows->params28;
			$params29 = $rows->params29;
			$params30 = $rows->params30;
			$params31 = $rows->params31;
			$params32 = $rows->params32;
			$params33 = $rows->params33;
			$params34 = $rows->params34;
			$params35 = $rows->params35;
			$params36 = $rows->params36;
			$params37 = $rows->params37;
			
		}

		if ($subm) {
			
			$olright = true;
			
			if ($addArtnr == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste välja vilken artikel det avser</p>";
			}
			/*
			if ($addcreatedby == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem du är!</p>";
			}
			*/
			if ($olright) {
				$tech->techAdminAdd($addArtnr);
			}
		}

		if ($submC) {
			
			$olright = true;
			
			/*
			if ($addcreatedby == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem du är!</p>";
			}
			*/
			if ($olright) {
				$tech->techAdminChange($addid);
			}
		}
		
	}
	
	if (preg_match("/menu_web\.php/i", $_SERVER['PHP_SELF'])) {
		
		if ($choose_department != $_SESSION['menudepartment'] && $add != "yes" && $change == "" && $delete == "" && $subm == "" && $submC == "" && $copypost == "") {
			unset($_SESSION['menudepartment']);
		}
		if ($choose_department != "") {

			if ($choose_department > 0) {
				$_SESSION['menudepartment'] = $choose_department;
			} else {
				unset($_SESSION['menudepartment']);
			}

		}

		if ($change != "") {

			$rows = $menu->getSpecMenu($change);

			$addid = $rows->menuID;
			$addByCat = $rows->menuByCat;
			$addActiveSE = $rows->menuActiveSE;
			$addNameSE = $rows->menuNameSE;
			$addLincSE = $rows->menuLincSE;
			$addActiveNO = $rows->menuActiveNO;
			$addNameNO = $rows->menuNameNO;
			$addLincNO = $rows->menuLincNO;
			$addActiveFI = $rows->menuActiveFI;
			$addNameFI = $rows->menuNameFI;
			$addLincFI = $rows->menuLincFI;
			
			$addIsSpacing = $rows->menuIsSpacing;
			$addOrder = $rows->menuOrder;
			$addShowPublic = $rows->menuShowPublic;
			$addParentMenu = $rows->menuParentMenu;
			$addIsParent = $rows->menuIsParent;
			
		}

		if ($delete != "") {
			$menu->FrontAdminDelete($delete,$section);
		}
		if ($subm) {
			
			$olright = true;
			
			$addcreatedby = $_COOKIE['login_mail'];
			
			if ($addActiveSE == "yes") {
				$addActiveSE = -1;
			} else {
				$addActiveSE = 0;
			}
			if ($addActiveNO == "yes") {
				$addActiveNO = -1;
			} else {
				$addActiveNO = 0;
			}
			if ($addActiveFI == "yes") {
				$addActiveFI = -1;
			} else {
				$addActiveFI = 0;
			}
			if ($addIsSpacing == "yes") {
				$addIsSpacing = -1;
			} else {
				$addIsSpacing = 0;
			}
			if ($addShowPublic == "yes") {
				$addShowPublic = -1;
			} else {
				$addShowPublic = 0;
			}
			if ($addIsParent == "yes") {
				$addIsParent = -1;
			} else {
				$addIsParent = 0;
			}
			if ($addParentMenu == "") {
				$addParentMenu = 0;
			}
			if ($addIsParent == -1 && $addParentMenu != "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Det kan inte vara både överliggande samt underliggande</p>";
			}
	
			if ($addcreatedby == "") {
				$olright = false;
				// $wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem du är!</p>";
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}
			if ($olright) {
				$menu->menuAdminAdd($addByCat,$addActiveSE,$addActiveNO,$addActiveFI,$addNameSE,$addNameNO,$addNameFI,$addLincSE,$addLincNO,$addLincFI,$addOrder,$addIsSpacing,$addShowPublic,$addIsParent,$addParentMenu,$addcreatedby);
			}

		}
		if ($submC) {
			
			$addcreatedby = $_COOKIE['login_mail'];

			$olright = true;
			
			if ($addActiveSE == "yes") {
				$addActiveSE = -1;
			} else {
				$addActiveSE = 0;
			}
			if ($addActiveNO == "yes") {
				$addActiveNO = -1;
			} else {
				$addActiveNO = 0;
			}
			if ($addActiveFI == "yes") {
				$addActiveFI = -1;
			} else {
				$addActiveFI = 0;
			}
			if ($addIsSpacing == "yes") {
				$addIsSpacing = -1;
			} else {
				$addIsSpacing = 0;
			}
			if ($addShowPublic == "yes") {
				$addShowPublic = -1;
			} else {
				$addShowPublic = 0;
			}
			if ($addIsParent == "yes") {
				$addIsParent = -1;
			} else {
				$addIsParent = 0;
			}
			if ($addParentMenu == "") {
				$addParentMenu = 0;
			}
			if ($addIsParent == -1 && $addParentMenu != "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Det kan inte vara både överliggande samt underliggande</p>";
			}
	
			if ($addcreatedby == "") {
				$olright = false;
				// $wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem du är!</p>";
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}
			if ($olright) {
				$menu->menuAdminChange($addid,$addByCat,$addActiveSE,$addActiveNO,$addActiveFI,$addNameSE,$addNameNO,$addNameFI,$addLincSE,$addLincNO,$addLincFI,$addOrder,$addIsSpacing,$addShowPublic,$addIsParent,$addParentMenu,$addcreatedby);
			}
		}
		
		
	}

	if (preg_match("/rma\.php/i", $_SERVER['PHP_SELF'])) {
	
		if ($rma_year == "") {
			// $rma_year = 0;
			$rma_year = date("Y");
		}
		if ($rma_month == "") {
			$rma_month = 0;
		}
		if ($rma_days == "") {
			$rma_days = 0;
		}

	}
	if (preg_match("/doa\.php/i", $_SERVER['PHP_SELF'])) {
	
		if ($rma_year == "") {
			// $rma_year = 0;
			$rma_year = date("Y");
		}
		if ($rma_month == "") {
			$rma_month = 0;
		}
		if ($rma_days == "") {
			$rma_days = 0;
		}

	}
	if (preg_match("/return\.php/i", $_SERVER['PHP_SELF'])) {
	
		if ($rma_year == "") {
			// $rma_year = 0;
			$rma_year = date("Y");
		}
		if ($rma_month == "") {
			$rma_month = 0;
		}
		if ($rma_days == "") {
			$rma_days = 0;
		}

	}
	if (preg_match("/allokerat\.php/i", $_SERVER['PHP_SELF'])) {
	
		if ($subm) {
			
			$olright = true;
			
			if ($addArtnr == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange ett artikel nummer!</p>";
			}
			if ($addArtnr != "") {
				if (!($monitor->check_artikel_status($addArtnr) == $addArtnr)) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Detta artikel nummer finns inte. Vänligen kolla upp detta! (måste skrivas exakt)</p>";
				}
				if ($allocated->checkAllocatedDuplicate($addArtnr)) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Detta artikel bevakas redan. Välj annan artikel.</p>";
				}
			}
			if ($olright) {
				$allocated->doMonitorAllocatedAdd($addArtnr);
			}

		}
		if ($delete > 0) {
			$allocated->doMonitorAllocatedDelete($delete);
		}

	}
	if (preg_match("/abonnemang_mobil\.php/i", $_SERVER['PHP_SELF'])) {
	
		if ($copypost == "iamsure") {
			$mobile->AbbAdminCopy($ID);
		}

	}
	if (preg_match("/abonnemang_data\.php/i", $_SERVER['PHP_SELF'])) {
	
		if ($copypost == "iamsure") {
			$mobile->AbbDataAdminCopy($ID);
		}

	}
	if (preg_match("/pricelist\.php/i", $_SERVER['PHP_SELF'])) {

		if ($deletearticle != "") {
			$price->articleDelete($deletearticle,$show);
		}

		if ($alldeletearticle != "") {
			$price->AllArticleDelete($alldeletearticle);
		}

		if ($deletepricelist != "") {
			$price->pricelistDelete($deletepricelist);
		}

		if ($change != "") {

			$rows = $price->getSpecPricelist($change);

			$addid = $rows->priceID;
			// $addfrom = substr ($rows->priceDateFrom, 0, 19);
			$timefrom = preg_replace('/:[0-9][0-9][0-9]/','', $rows->priceDateFrom);
			$timefrom = strtotime($timefrom);
			$addfrom = date("Y-m-d H:i:s", $timefrom);
			// $addto = substr ($rows->priceDateTo, 0, 19);
			$timeto = preg_replace('/:[0-9][0-9][0-9]/','', $rows->priceDateTo);
			$timeto = strtotime($timeto);
			$addto = date("Y-m-d H:i:s", $timeto);
			$addrubrik = $rows->priceHeader;
			$addrubrik_fi = $rows->priceHeader_fi;
			$addrubrik_no = $rows->priceHeader_no;
			$addpayoff = $rows->priceUnderHeader;
			$addpayoff_fi = $rows->priceUnderHeader_fi;
			$addpayoff_no = $rows->priceUnderHeader_no;
			$addpicture = $rows->pricePicture;
			$addtype = $rows->priceType;
			$addcomment = $rows->priceComment;
			$addactive = $rows->priceActive;
			$addcreatedby = $rows->priceCreatedBy;
			$addgallerylist = $rows->priceListType;

		}

		if ($subm) {
			
			$olright = true;

			$addcreatedby = $_COOKIE['login_mail'];
			
			if ($addactive == "yes") {
				$addactive = -1;
			} else {
				$addactive = 0;
			}
			
			if ($addgallerylist == "yes") {
				$addgallerylist = -1;
			} else {
				$addgallerylist = 0;
			}
			
			if ($addfrom == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Från datum får inte vara tomt!</p>";
			}
			if ($addfrom != "") {
				if (!($price->isValidDateTime($addfrom))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
				}
			}
			if ($addto == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Till datum får inte vara tomt!</p>";
			}
			if ($addto != "") {
				if (!($price->isValidDateTime($addto))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
				}
			}
			if ($addcreatedby == "") {
				$olright = false;
				// $wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem du är!</p>";
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}

			if ($olright) {
				$price->AddPriceList($addrubrik,$addrubrik_fi,$addpayoff,$addpayoff_fi,$addtype,$addcomment,$addcreatedby,$addfrom,$addto,$addpicture,$addactive,$addrubrik_no,$addpayoff_no,$addgallerylist);
			}

		}

		if ($submC) {
			
			$olright = true;
			
			$addcreatedby = $_COOKIE['login_mail'];

			if ($addactive == "yes") {
				$addactive = -1;
			} else {
				$addactive = 0;
			}
			
			if ($addgallerylist == "yes") {
				$addgallerylist = -1;
			} else {
				$addgallerylist = 0;
			}
			
			if ($addfrom == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Från datum får inte vara tomt!</p>";
			}
			if ($addfrom != "") {
				if (!($price->isValidDateTime($addfrom))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
				}
			}
			if ($addto == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Till datum får inte vara tomt!</p>";
			}
			if ($addto != "") {
				if (!($price->isValidDateTime($addto))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
				}
			}
			if ($addcreatedby == "") {
				$olright = false;
				// $wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem du är!</p>";
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}

			if ($olright) {
				$price->ChangePriceList($addid,$addrubrik,$addrubrik_fi,$addpayoff,$addpayoff_fi,$addtype,$addcomment,$addcreatedby,$addfrom,$addto,$addpicture,$addactive,$addrubrik_no,$addpayoff_no,$addgallerylist);
			}

		}

		if ($submArt) {
			
			$olright = true;
			
			if ($addartnr == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste fylla i ett artikel nr!</p>";
			}
			if ($addartnr != "") {
				
				if (!$price->checkIfKat($addpricelist)) {
				// if (!eregi("-kat-", $addartnr)) {
					if (!($price->check_artikel_status($addartnr))) {
						$olright = false;
						$wrongmess .= "<p class=\"boldit_red\">- Detta artikel nummer finns inte. Vänligen kolla upp detta!</p>";
					}
				}
				if ($price->check_artikel_status_in_pricelist($addpricelist,$addartnr)) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Detta artikel nummer finns redan inlagt. Välj ett annat artikelnummer!</p>";
				}
			}

			if ($olright) {
				$price->addPriceListArticle($addpricelist,$addartnr);
			}

		}

	}

	if (preg_match("/adtrigger\.php/i", $_SERVER['PHP_SELF'])) {

		if ($deletearticle != "") {
			$adtrigger->articleDelete($deletearticle,$show);
		}

		if ($deleteIPinternt != "") {
			$adtrigger->deleteIPinternt($deleteIPinternt);
		}

		if ($deletepricelist != "") {
			$adtrigger->pricelistDelete($deletepricelist);
		}

		if ($change != "") {

		$rows = $adtrigger->getSpecPricelist($change);

		$addid = $rows->adID;
		// $addfrom = substr ($rows->priceDateFrom, 0, 19);
		$timefrom = strtotime($rows->adFrom);
		$addfrom = date("Y-m-d H:i:s", $timefrom);
		// $addto = substr ($rows->priceDateTo, 0, 19);
		$timeto = strtotime($rows->adTo);
		$addto = date("Y-m-d H:i:s", $timeto);
		$addgroup = $rows->adGroup;
		$addrubrik = $rows->adName;
		$addlinc = $rows->adLinc;
		$addpicture = $rows->adPicture;
		$addcomment = $rows->adComment;
		$addcreatedby = $rows->adBy;
		$addcountry = $rows->adCountry;

		}

		if ($subm) {
			
			$olright = true;
			
			if ($addfrom == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Från datum får inte vara tomt!</p>";
			}
			if ($addfrom != "") {
				if (!($adtrigger->isValidDateTime($addfrom))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
				}
			}
			if ($addto == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Till datum får inte vara tomt!</p>";
			}
			if ($addto != "") {
				if (!($adtrigger->isValidDateTime($addto))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
				}
			}
			if ($addlinc == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange en länk till vart man skickas!</p>";
			}
			if ($addcreatedby == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem du är!</p>";
			}

			if ($olright) {
				$adtrigger->AddAd($addgroup,$addrubrik,$addlinc,$addcomment,$addcreatedby,$addfrom,$addto,$addpicture,$addcountry);
			}

		}

		if ($submC) {
			
			$olright = true;
			
			if ($addfrom == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Från datum får inte vara tomt!</p>";
			}
			if ($addfrom != "") {
				if (!($adtrigger->isValidDateTime($addfrom))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
				}
			}
			if ($addto == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Till datum får inte vara tomt!</p>";
			}
			if ($addto != "") {
				if (!($adtrigger->isValidDateTime($addto))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
				}
			}
			if ($addlinc == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange en länk till vart man skickas!</p>";
			}
			if ($addcreatedby == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange vem du är!</p>";
			}

			if ($olright) {
				$adtrigger->ChangeAd($addgroup,$addid,$addrubrik,$addlinc,$addcomment,$addcreatedby,$addfrom,$addto,$addpicture,$addcountry);
			}

		}

		if ($submArt) {
			
			$olright = true;
			
			if ($addartnr == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste fylla i ett artikel nr!</p>";
			}
			if ($addartnr != "") {
				if (!($price->check_artikel_status($addartnr))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Detta artikel nummer finns inte. Vänligen kolla upp detta!</p>";
				}
			}

			if ($olright) {
				$adtrigger->addPriceListArticle($addpricelist,$addartnr);
			}

		}
	
	}
	if (preg_match("/fellogg_mobil\.php/i", $_SERVER['PHP_SELF'])) {
	
		$grupp = "mobil";

	}
	if (preg_match("/fellogg_surfplattor\.php/i", $_SERVER['PHP_SELF'])) {
	
		$grupp = "padda";

	}
	if (preg_match("/fellogg_systemkameror\.php/i", $_SERVER['PHP_SELF'])) {
	
		$grupp = "system";

	}
	if (preg_match("/fellogg_digitalkameror\.php/i", $_SERVER['PHP_SELF'])) {
	
		$grupp = "digikam";

	}
	if (preg_match("/fellogg_video\.php/i", $_SERVER['PHP_SELF'])) {
	
		$grupp = "video";

	}
	if (preg_match("/fellogg_objektiv\.php/i", $_SERVER['PHP_SELF'])) {
	
		$grupp = "objektiv";

	}
	if (preg_match("/goods_expectation\.php/i", $_SERVER['PHP_SELF'])) {
		$ref_dagensdatum = date("Y-m-d");
		if ($firstinput != "") {
			$dagensdatum = $firstinput;
		} else {
			$dagensdatum = date("Y-m-d");
		}
	}
	if (preg_match("/campaign\.php/i", $_SERVER['PHP_SELF']) || preg_match("/add_campaign_article\.php/i", $_SERVER['PHP_SELF'])) {
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.78x") {
			unset($discountCode);
		}
		
		if ($article != "") {
			
			if (!($price->check_artikel_status($article))) {
				$wrongmess .= "<p>Artikelnr finns inte.</p>";
			} else {
				$rows = $bask->getArticleInfo($article);
				$search_tillverkar_id = $rows->tillverkar_id;
				$search_kategori_id = $rows->kategori_id;
			}
			
		}

		if ($copy != "") {
			// $copypost = true;
			$rows = $campaign->getSpecDiscountCode($copy);
			$copyid = $rows->cnt;
			$addkampanjkod = $rows->discountCode;
			$addkampanjkod++;
			
			$timefrom = preg_replace('/:[0-9][0-9][0-9]/','', $rows->validFrom);
			$timefrom = strtotime($timefrom);
			if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
				echo $timefrom . "<br>";
				echo time() . "<br>";
			}
			if ($timefrom > time()) {
				$addfrom = date("Y-m-d H:i:s", $timefrom);
			} else {
				$addfrom = date("Y-m-d H:i:s", time());
			}
			
			$timeto = preg_replace('/:[0-9][0-9][0-9]/','', $rows->validDate);
			$timeto = strtotime($timeto);
			if ($timeto < time()) {
				unset($timeto);
			} else {
				$addto = date("Y-m-d H:i:s", $timeto);
			}
			
			// $addkampanjkod = $rows->discountCode;
			$addactive_se = $rows->active_se;
			$addactive_fi = $rows->active_fi;
			$addactive_no = $rows->active_no;
			$addpersonal_discount = $rows->personal_discount;
			$addartikelnr = $rows->artnr;
			$addkategori = $rows->kategori_id;
			$addmanufacturer = $rows->tillverkar_id;
			
			$adddiscountpercent = $rows->discountPercent;
			if ($adddiscountpercent != "") {
				$adddiscountpercent = $adddiscountpercent*100;
			}
			$adddiscountpercent_fi = $rows->discountPercent_fi;
			if ($adddiscountpercent_fi != "") {
				$adddiscountpercent_fi = $adddiscountpercent_fi*100;
			}
			$adddiscountpercent_no = $rows->discountPercent_no;
			if ($adddiscountpercent_no != "") {
				$adddiscountpercent_no = $adddiscountpercent_no*100;
			}
			
			$adddiscountamount = $rows->discountAmount;
			if ($adddiscountamount != "") {
				$adddiscountamount = round($adddiscountamount * 1.25, 0);
			}
			$adddiscountamount_fi = $rows->discountAmount_fi;
			if ($adddiscountamount_fi != "") {
				$adddiscountamount_fi = round($adddiscountamount_fi * 1.24, 0);
			}
			$adddiscountamount_no = $rows->discountAmount_no;
			if ($adddiscountamount_no != "") {
				$adddiscountamount_no = round($adddiscountamount_no * 1.25, 0);
			}
			
			$adddiscountoutprice = $rows->discountOutprice;
			if ($adddiscountoutprice != "") {
				$adddiscountoutprice = round($adddiscountoutprice * 1.25, 0);
			}
			$adddiscountoutprice_fi = $rows->discountOutprice_fi;
			if ($adddiscountoutprice_fi != "") {
				$adddiscountoutprice_fi = round($adddiscountoutprice_fi * 1.24, 0);
			}
			$adddiscountoutprice_no = $rows->discountOutprice_no;
			if ($adddiscountoutprice_no != "") {
				$adddiscountoutprice_no = round($adddiscountoutprice_no * 1.25, 0);
			}
			
			$addtitle_se = $rows->title_se;
			$addtitle_fi = $rows->title_fi;
			$addtitle_no = $rows->title_no;
			
			$addcampaigntext = $rows->descrptn;
			$addcampaigntext_fi = $rows->descrptn_fi;
			$addcampaigntext_fi_sv = $rows->descrptn_fi_sv;
			$addcampaigntext_no = $rows->descrptn_no;
			
			$addpicturelinc = $rows->campaign_link;
			$addpicturelinc_fi = $rows->campaign_link_fi;
			$addpicturelinc_fi_sv = $rows->campaign_link_fi_sv;
			$addpicturelinc_no = $rows->campaign_link_no;
			
			$addlinc = $rows->link;
			$addlinc_fi = $rows->link_fi;
			$addlinc_fi_sv = $rows->link_fi_sv;
			$addlinc_no = $rows->link_no;
			
			$addexternallinc = $rows->link_ext;
			$addexternallinc_fi = $rows->link_ext_fi;
			$addexternallinc_fi_sv = $rows->link_ext_fi_sv;
			$addexternallinc_no = $rows->link_ext_no;

			$addshowpicture = $rows->isPicture_show;
			$addcomment = $rows->notes;
			$addsite = $rows->site;
			// $addcreatedby = $rows->by_user;
		}
		if ($change != "") {

			if ($deletearticle != "") {
				$campaign->deleteCampaignArticle($deletearticle,$change);
			}
			
			if ($delArtIncl != "") {
				$campaign->deleteIncludedArticle($delArtIncl,$change);
			}
		
			$rows = $campaign->getSpecDiscountCode($change);

			$addid = $rows->cnt;
			$timefrom = preg_replace('/:[0-9][0-9][0-9]/','', $rows->validFrom);
			$timefrom = strtotime($timefrom);
			$addfrom = date("Y-m-d H:i:s", $timefrom);
			$timeto = preg_replace('/:[0-9][0-9][0-9]/','', $rows->validDate);
			$timeto = strtotime($timeto);
			if ($now == "yes") {
				$addto = date("Y-m-d H:i:s", time());
			} else {
				$addto = date("Y-m-d H:i:s", $timeto);
			}
			$addkampanjkod = $rows->discountCode;
			$addactive_se = $rows->active_se;
			$addactive_fi = $rows->active_fi;
			$addactive_no = $rows->active_no;
			$addpersonal_discount = $rows->personal_discount;
			$addartikelnr = $rows->artnr;
			$addkategori = $rows->kategori_id;
			$addmanufacturer = $rows->tillverkar_id;
			
			$adddiscountpercent = $rows->discountPercent;
			if ($adddiscountpercent != "") {
				$adddiscountpercent = $adddiscountpercent*100;
			}
			$adddiscountpercent_fi = $rows->discountPercent_fi;
			if ($adddiscountpercent_fi != "") {
				$adddiscountpercent_fi = $adddiscountpercent_fi*100;
			}
			$adddiscountpercent_no = $rows->discountPercent_no;
			if ($adddiscountpercent_no != "") {
				$adddiscountpercent_no = $adddiscountpercent_no*100;
			}
			
			$adddiscountamount = $rows->discountAmount;
			if ($adddiscountamount != "") {
				$adddiscountamount = round($adddiscountamount * 1.25, 0);
			}
			$adddiscountamount_fi = $rows->discountAmount_fi;
			if ($adddiscountamount_fi != "") {
				$adddiscountamount_fi = round($adddiscountamount_fi * 1.24, 0);
			}
			$adddiscountamount_no = $rows->discountAmount_no;
			if ($adddiscountamount_no != "") {
				$adddiscountamount_no = round($adddiscountamount_no * 1.25, 0);
			}
			
			$adddiscountoutprice = $rows->discountOutprice;
			if ($adddiscountoutprice != "") {
				$adddiscountoutprice = round($adddiscountoutprice * 1.25, 0);
			}
			$adddiscountoutprice_fi = $rows->discountOutprice_fi;
			if ($adddiscountoutprice_fi != "") {
				$adddiscountoutprice_fi = round($adddiscountoutprice_fi * 1.24, 0);
			}
			$adddiscountoutprice_no = $rows->discountOutprice_no;
			if ($adddiscountoutprice_no != "") {
				$adddiscountoutprice_no = round($adddiscountoutprice_no * 1.25, 0);
			}
			
			$addtitle_se = $rows->title_se;
			$addtitle_fi = $rows->title_fi;
			$addtitle_no = $rows->title_no;
			
			$addcampaigntext = $rows->descrptn;
			$addcampaigntext_fi = $rows->descrptn_fi;
			$addcampaigntext_fi_sv = $rows->descrptn_fi_sv;
			$addcampaigntext_no = $rows->descrptn_no;
			
			$addpicturelinc = $rows->campaign_link;
			$addpicturelinc_fi = $rows->campaign_link_fi;
			$addpicturelinc_fi_sv = $rows->campaign_link_fi_sv;
			$addpicturelinc_no = $rows->campaign_link_no;
			
			$addlinc = $rows->link;
			$addlinc_fi = $rows->link_fi;
			$addlinc_fi_sv = $rows->link_fi_sv;
			$addlinc_no = $rows->link_no;
			
			$addexternallinc = $rows->link_ext;
			$addexternallinc_fi = $rows->link_ext_fi;
			$addexternallinc_fi_sv = $rows->link_ext_fi_sv;
			$addexternallinc_no = $rows->link_ext_no;

			$addshowpicture = $rows->isPicture_show;
			$addcomment = $rows->notes;
			$addsite = $rows->site;
			$addcreatedby = $rows->by_user;
			$addnotify = $rows->notify;
			$addnotifyfrom = $rows->notifyTime;

		}

		if ($subm) {
			
			$olright = true;
			
			$addcreatedby = $_COOKIE['login_mail'];

			if ($addkampanjkod == "") {
				$olright = false;
				$wrongmess .= "<li>Vänligen ange en kampanjkod!</li>";
			}
			if ($addkampanjkod != "") {
				$addkampanjkod = strtoupper($addkampanjkod);
				if ($campaign->checkCampaignCode($addkampanjkod)) {
					$olright = false;
					$wrongmess .= "<li>Kampanjkod <b>$addkampanjkod</b> är redan förbrukad. Välj annan kampanjkod!</li>";
				}
			}
			if ($addfrom == "") {
				$olright = false;
				$wrongmess .= "<li>Från datum får inte vara tomt!</li>";
			}
			if ($addfrom != "") {
				if (!($price->isValidDateTime($addfrom))) {
					$olright = false;
					$wrongmess .= "<li>Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</li>";
				}
			}
			if ($addto == "") {
				$olright = false;
				$wrongmess .= "<li>Till datum får inte vara tomt!</li>";
			}
			if ($addto != "") {
				if (!($price->isValidDateTime($addto))) {
					$olright = false;
					$wrongmess .= "<li>Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</li>";
				}
			}
			if ($addfrom != "" && $addto != "") {
				if (strtotime($addfrom) >= strtotime($addto)) {
					$olright = false;
					$wrongmess .= "<li>Till-datumet kan inte vara mindre än från-datumet. Vänligen åtgärda detta!</li>";
				}
			}
			if ($addactive_se != "yes" && $addactive_fi != "yes" && $addactive_no != "yes") {
				$olright = false;
				$wrongmess .= "<li>Kampanjen måste vara aktiv i antingen Sverige, Norge eller i Finland</li>";
			}
			if ($addactive_se == "yes") {
				$addactive_se = -1;
			} else {
				$addactive_se = 0;
			}
			if ($addactive_fi == "yes") {
				$addactive_fi = -1;
			} else {
				$addactive_fi = 0;
			}
			if ($addactive_no == "yes") {
				$addactive_no = -1;
			} else {
				$addactive_no = 0;
			}
			if ($addpersonal_discount == "yes") {
				$addpersonal_discount = -1;
				if ($addsite == "") {
					$addsite = 0;
				}
			} else {
				$addpersonal_discount = 0;
			}
			if ($addartikelnr != "") {
				if (!($price->check_artikel_status($addartikelnr))) {
					$olright = false;
					$wrongmess .= "<li>Detta artikel nummer finns inte. Vänligen kolla upp detta!</li>";
				}
				/*
				// 2015-07-08, avaktiverar denna så länge då jag inte tror den behövs mer
				if ($campaign->checkCampaignCodeArticleInUse($addartikelnr,$addactive_se,$addactive_fi,$addactive_no)) {
					$olright = false;
					$wrongmess .= "<li>Detta artikel nummer finns redan i en annan aktiv kampanj eller kommande kampanj och krockar därmed. Vänligen kolla upp detta!</li>";
				}
				*/
			}
			if ($addshowpicture == "yes") {
				$addshowpicture = -1;
			} else {
				$addshowpicture = 0;
			}
			if ($addsite == "" && $addpersonal_discount == 0) {
				$olright = false;
				$wrongmess .= "<li>Ange vilken site den skall visas på.</li>";
			}
			if ($addcreatedby == "") {
				$olright = false;
				// $wrongmess .= "<li>Du måste ange vem du är!</li>";
				$wrongmess .= "<li>Du måste vara inloggad för att utföra detta!</li>";
			}
			if ($addnotify == "yes") {
				$addnotify = -1;
				if (!($price->isValidDateTime($addnotifyfrom))) {
					$olright = false;
					$wrongmess .= "<li>Ogiltigt aviseringsdatum. Skall formateras så här, 2009-01-01 15:00:00</li>";
				}
				if (strtotime($addnotifyfrom) >= strtotime($addto)) {
					$olright = false;
					$wrongmess .= "<li>Aviseringsdatumet inträffar efter att kampanjen avslutats, vänligen korrigera detta.</li>";
				}
			} else {
				$addnotify = 0;
				unset($addnotifyfrom);
			}
			
			if ($olright) {
				
				if ($adddiscountamount != "") {
					$adddiscountamount = round($adddiscountamount/1.25, 2);
				}
				if ($adddiscountamount_no != "") {
					$adddiscountamount_no = round($adddiscountamount_no/1.25, 2);
				}
				if ($adddiscountamount_fi != "") {
					$adddiscountamount_fi = round($adddiscountamount_fi/1.24, 4);
				}
				
				if ($adddiscountoutprice != "") {
					$adddiscountoutprice = round($adddiscountoutprice/1.25, 2);
				}
				if ($adddiscountoutprice_no != "") {
					$adddiscountoutprice_no = round($adddiscountoutprice_no/1.25, 2);
				}
				if ($adddiscountoutprice_fi != "") {
					$adddiscountoutprice_fi = round($adddiscountoutprice_fi/1.24, 4);
				}

				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
					$campaign->addCampaignNew($addkampanjkod,$addfrom,$addto,$addactive_se,$addactive_fi,$addactive_no,
											$addartikelnr,$addkategori,$addmanufacturer,$adddiscountpercent,$adddiscountpercent_fi,$adddiscountpercent_no,
											$adddiscountamount,$adddiscountamount_fi,$adddiscountamount_no,$adddiscountoutprice,$adddiscountoutprice_fi,$adddiscountoutprice_no,
											$addcampaigntext,$addcampaigntext_fi,$addcampaigntext_fi_sv,$addcampaigntext_no,
											$addlinc,$addlinc_fi,$addlinc_fi_sv,$addlinc_no,
											$addpicturelinc,$addpicturelinc_fi,$addpicturelinc_fi_sv,$addpicturelinc_no,
											$addexternallinc,$addexternallinc_fi,$addexternallinc_fi_sv,$addexternallinc_no,
											$addshowpicture,$addsite,$addcomment,$addcreatedby,$addnotify,$addnotifyfrom,$addpersonal_discount,
											$addtitle_se,$addtitle_fi,$addtitle_no);
				} else {
					$campaign->addCampaignNew($addkampanjkod,$addfrom,$addto,$addactive_se,$addactive_fi,$addactive_no,
											$addartikelnr,$addkategori,$addmanufacturer,$adddiscountpercent,$adddiscountpercent_fi,$adddiscountpercent_no,
											$adddiscountamount,$adddiscountamount_fi,$adddiscountamount_no,$adddiscountoutprice,$adddiscountoutprice_fi,$adddiscountoutprice_no,
											$addcampaigntext,$addcampaigntext_fi,$addcampaigntext_fi_sv,$addcampaigntext_no,
											$addlinc,$addlinc_fi,$addlinc_fi_sv,$addlinc_no,
											$addpicturelinc,$addpicturelinc_fi,$addpicturelinc_fi_sv,$addpicturelinc_no,
											$addexternallinc,$addexternallinc_fi,$addexternallinc_fi_sv,$addexternallinc_no,
											$addshowpicture,$addsite,$addcomment,$addcreatedby,$addnotify,$addnotifyfrom,$addpersonal_discount,
											$addtitle_se,$addtitle_fi,$addtitle_no);
				}
			}

		}

		if ($submC) {
			
			$olright = true;

			$addcreatedby = $_COOKIE['login_mail'];
			
			/*
			if ($addkampanjkod == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Vänligen ange en kampanjkod!</p>";
			}
			if ($addkampanjkod != "") {
				if ($campaign->checkCampaignCode($addkampanjkod)) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Kampanjkod <b>$addkampanjkod</b> är redan förbrukad. Välj annan kampanjkod!</p>";
				}
			}
			*/
			if ($addfrom == "") {
				$olright = false;
				$wrongmess .= "<li>Från datum får inte vara tomt!</li>";
			}
			if ($addfrom != "") {
				if (!($price->isValidDateTime($addfrom))) {
					$olright = false;
					$wrongmess .= "<li>Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</li>";
				}
			}
			if ($addto == "") {
				$olright = false;
				$wrongmess .= "<li>Till datum får inte vara tomt!</li>";
			}
			if ($addto != "") {
				if (!($price->isValidDateTime($addto))) {
					$olright = false;
					$wrongmess .= "<li>Ogiltigt till datum. Skall formateras så här, 2009-01-01 15:00:00</li>";
				}
			}
			if ($addfrom != "" && $addto != "") {
				if (strtotime($addfrom) >= strtotime($addto)) {
					$olright = false;
					$wrongmess .= "<li>Till-datumet kan inte vara mindre än från-datumet. Vänligen åtgärda detta!</li>";
				}
			}
			if ($addactive_se != "yes" && $addactive_fi != "yes" && $addactive_no != "yes") {
				$olright = false;
				$wrongmess .= "<li>Kampanjen måste vara aktiv i antingen Sverige, Norge eller i Finland</li>";
			}
			if ($addactive_se == "yes") {
				$addactive_se = -1;
			} else {
				$addactive_se = 0;
			}
			if ($addactive_fi == "yes") {
				$addactive_fi = -1;
			} else {
				$addactive_fi = 0;
			}
			if ($addactive_no == "yes") {
				$addactive_no = -1;
			} else {
				$addactive_no = 0;
			}
			if ($addpersonal_discount == "yes") {
				$addpersonal_discount = -1;
				if ($addsite == "") {
					$addsite = 0;
				}
			} else {
				$addpersonal_discount = 0;
			}
			if ($addartikelnr != "") {
				if (!($price->check_artikel_status($addartikelnr))) {
					$olright = false;
					$wrongmess .= "<li>Detta artikel nummer finns inte. Vänligen kolla upp detta!</li>";
				}
			}
			if ($addshowpicture == "yes") {
				$addshowpicture = -1;
			} else {
				$addshowpicture = 0;
			}
			if ($addsite == "" && $addpersonal_discount == 0) {
				$olright = false;
				$wrongmess .= "<li>Ange vilken site den skall visas på.</li>";
			}
			if ($addcreatedby == "") {
				$olright = false;
				// $wrongmess .= "<li>Du måste ange vem du är!</li>";
				$wrongmess .= "<li>Du måste vara inloggad för att utföra detta!</li>";
			}
			if ($addnotify == "yes") {
				$addnotify = -1;
				if (!($price->isValidDateTime($addnotifyfrom))) {
					$olright = false;
					$wrongmess .= "<li>Ogiltigt aviseringsdatum. Skall formateras så här, 2009-01-01 15:00:00</li>";
				}
			} else {
				$addnotify = 0;
				unset($addnotifyfrom);
			}

			if ($olright) {
				if ($adddiscountamount != "") {
					$adddiscountamount = round($adddiscountamount/1.25, 2);
				}
				if ($adddiscountamount_no != "") {
					$adddiscountamount_no = round($adddiscountamount_no/1.25, 2);
				}
				if ($adddiscountamount_fi != "") {
					$adddiscountamount_fi = round($adddiscountamount_fi/1.24, 4);
				}
				
				if ($adddiscountoutprice != "") {
					$adddiscountoutprice = round($adddiscountoutprice/1.25, 2);
				}
				if ($adddiscountoutprice_no != "") {
					$adddiscountoutprice_no = round($adddiscountoutprice_no/1.25, 2);
				}
				if ($adddiscountoutprice_fi != "") {
					$adddiscountoutprice_fi = round($adddiscountoutprice_fi/1.24, 4);
				}
				
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
					$campaign->updateCampaign($addkampanjkod,$addfrom,$addto,$addactive_se,$addactive_fi,$addactive_no,
											$addartikelnr,$addkategori,$addmanufacturer,$adddiscountpercent,$adddiscountpercent_fi,$adddiscountpercent_no,
											$adddiscountamount,$adddiscountamount_fi,$adddiscountamount_no,$adddiscountoutprice,$adddiscountoutprice_fi,$adddiscountoutprice_no,
											$addcampaigntext,$addcampaigntext_fi,$addcampaigntext_fi_sv,$addcampaigntext_no,
											$addlinc,$addlinc_fi,$addlinc_fi_sv,$addlinc_no,
											$addpicturelinc,$addpicturelinc_fi,$addpicturelinc_fi_sv,$addpicturelinc_no,
											$addexternallinc,$addexternallinc_fi,$addexternallinc_fi_sv,$addexternallinc_no,
											$addshowpicture,$addsite,$addcomment,$addcreatedby,$addnotify,$addnotifyfrom,$addpersonal_discount,
											$addtitle_se,$addtitle_fi,$addtitle_no,$addid);
				} else {
					$campaign->updateCampaign($addkampanjkod,$addfrom,$addto,$addactive_se,$addactive_fi,$addactive_no,
											$addartikelnr,$addkategori,$addmanufacturer,$adddiscountpercent,$adddiscountpercent_fi,$adddiscountpercent_no,
											$adddiscountamount,$adddiscountamount_fi,$adddiscountamount_no,$adddiscountoutprice,$adddiscountoutprice_fi,$adddiscountoutprice_no,
											$addcampaigntext,$addcampaigntext_fi,$addcampaigntext_fi_sv,$addcampaigntext_no,
											$addlinc,$addlinc_fi,$addlinc_fi_sv,$addlinc_no,
											$addpicturelinc,$addpicturelinc_fi,$addpicturelinc_fi_sv,$addpicturelinc_no,
											$addexternallinc,$addexternallinc_fi,$addexternallinc_fi_sv,$addexternallinc_no,
											$addshowpicture,$addsite,$addcomment,$addcreatedby,$addnotify,$addnotifyfrom,$addpersonal_discount,
											$addtitle_se,$addtitle_fi,$addtitle_no,$addid);
				}
			}

		}

		if ($submArt) {
			
			$olright = true;
			
			if ($addartnr == "") {
				$olright = false;
				$wrongmess2 .= "<li>Du måste fylla i ett artikel nr!</li>";
			}
			if ($change == "") {
				$olright = false;
				$wrongmess2 .= "<li>Du måste ange ett kampanj-ID!</li>";
			}
			if (preg_match("/add_campaign_article\.php/i", $_SERVER['PHP_SELF'])) {
				if (!$campaign->getValidCampaignPriceList($change)) {
					$olright = false;
					$wrongmess2 .= "<li>Detta kampanj-ID:et är inte gilltigt. Vänligen kontrollera!</li>";
				}
			}
			if ($addartnr != "") {
				if (!($price->check_artikel_status($addartnr))) {
					$olright = false;
					$wrongmess2 .= "<li>Detta artikel nummer finns inte. Vänligen kolla upp detta!</li>";
				}
			}

			if ($olright) {
				$campaign->addCampaignArticle($addartnr,$change,$oldIncID);
			}

		}

		if ($submArtIncluded) {
			
			$olright = true;

			if ($addno_store == "yes") {
				$addno_store = -1;
			} else {
				$addno_store = 0;
			}
			
			if ($addartnrincludedcount < 1) {
				$olright = false;
				$wrongmess3 .= "<li>Du måste fylla i antal som skall följa med!</li>";
			}
			if ($addartnrincluded == "") {
				$olright = false;
				$wrongmess3 .= "<li>Du måste fylla i ett artikel nr!</li>";
			}
			if ($addartnrincluded != "") {
				if (!($price->check_artikel_status($addartnrincluded))) {
					$olright = false;
					$wrongmess3 .= "<li>Detta artikel nummer finns inte. Vänligen kolla upp detta!</li>";
				}
			}

			if ($olright) {
				$campaign->addIncludedArticle($addartnrincludedcount,$addartnrincluded,$addno_store,$change,$oldIncID);
			}

		}
		
	}

	if (preg_match("/cms\.php/i", $_SERVER['PHP_SELF'])) {

		if ($change != "") {

			$rows = $cms->getSpecCms($change);

			$addid = $rows->cms_ID;
			$headline = $rows->cms_Headline;
			$department = $rows->cms_Department;
			$addactive = $rows->cms_Active;
			$area = $rows->cms_Text;
			
			// echo "sdfsf:" . $addid;

		}

		if ($subm) {
			
			$olright = true;
			
			$addBy = $_COOKIE['login_mail'];
			
			if ($headline == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange en rubrik. Den kommer även fungera som sidlänk</p>";
			}
			if ($department == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange vilka sida som skall laddas.</p>";
			}
			if ($addactive == "yes") {
				$addactive = -1;
			} else {
				$addactive = 0;
			}
			if ($area == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange något innehåll som skall visas på sidan</p>";
			}
			if ($addBy == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}
			if ($olright) {
				$cms->doCmsAdd($headline,$department,$area,$addBy,$addactive);
				// header("Location: https://www.cyberphoto.se/order/admin/cms.php");
				// exit;
			}

		}
		if ($submC) {
			
			$olright = true;

			$addBy = $_COOKIE['login_mail'];
			
			if ($headline == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange en rubrik. Den kommer även fungera som sidlänk</p>";
			}
			if ($department == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange vilka sida som skall laddas.</p>";
			}
			if ($addactive == "yes") {
				$addactive = -1;
			} else {
				$addactive = 0;
			}
			if ($area == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange något innehåll som skall visas på sidan</p>";
			}
			if ($addBy == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}
			if ($olright) {
				$cms->doCmsChange($headline,$department,$area,$addBy,$addactive,$addid,$area_backup);
				if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
					header("Location: https://admin.cyberphoto.se/cms.php");
				} else {
					header("Location: https://www.cyberphoto.se/order/admin/cms.php");
				}
				exit;
			}
		}

	}
	if (preg_match("/check_incoming\.php/i", $_SERVER['PHP_SELF'])) {

		if ($change != "") {

			$rows = $filter->getFilterRow($change);

			$addID = $rows->checkID;
			$addWord = $rows->checkWord;
			$addActive = $rows->checkActive;
			$addComment = $rows->checkNote;

		}

		if ($subm) {
			
			$olright = true;

			$addcreatedby = $_COOKIE['login_mail'];
			
			if ($addWord == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange ett filter (ord eller del av ord)!</p>";
			}
			if ($addcreatedby == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}
			if ($olright) {
				$filter->doFilterAdd($addWord,$addcreatedby,$addComment);
				header("Location: https://" . $_SERVER['HTTP_HOST'] . "/check_incoming.php");
				exit;
			}

		}
		if ($submC) {
			
			$olright = true;

			$addcreatedby = $_COOKIE['login_mail'];

			if ($addActive == "yes") {
				$addActive = -1;
			} else {
				$addActive = 0;
			}
			if ($addWord == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange ett filter (ord eller del av ord)!</p>";
			}
			if ($addcreatedby == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}
			if ($olright) {
				$filter->doFilterChange($addID,$addWord,$addcreatedby,$addActive,$addComment);
				header("Location: https://" . $_SERVER['HTTP_HOST'] . "/check_incoming.php");
				exit;
			}
		}

	}
	
	if (preg_match("/accessories\.php/i", $_SERVER['PHP_SELF']) || preg_match("/accessories_popup\.php/i", $_SERVER['PHP_SELF'])) {
		
		/*
		if ($change != "") {

			$rows = $filter->getFilterRow($change);

			$addID = $rows->checkID;
			$addWord = $rows->checkWord;
			$addActive = $rows->checkActive;
			$addComment = $rows->checkNote;

		}
		*/

		if ($submArt) {
			
			$olright = true;

			$addcreatedby = $_COOKIE['login_mail'];
			
			if ($change == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Nu blev det något allvarligt fel. Vänligen börja om från början.</p>";
			}
			if ($change != "") {
				if (!($price->check_artikel_status($change))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Nu blev det något allvarligt fel. Vänligen börja om från början.</p>";
				}
			}
			if ($addartnr == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste fylla i ett artikel nr!</p>";
			}
			if ($addartnr != "") {
				if (!($price->check_artikel_status($addartnr))) {
					$olright = false;
					$wrongmess .= "<p class=\"boldit_red\">- Detta artikel nummer finns inte. Vänligen kolla upp detta!</p>";
				}
			}
			if (!is_numeric($addrecommended) && $addrecommended != "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Värdet för rekommenderat får endast vara numeriskt!</p>";
			}
			
			if ($addcreatedby == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}
			if ($olright) {
				$product->addAccessories($addartnr,$change,$addcomment,$addrecommended,$addcreatedby);
				if (preg_match("/accessories_popup\.php/i", $_SERVER['PHP_SELF'])) {
					header("Location: https://www.cyberphoto.se/order/admin/accessories_popup.php?alias=yes&change=$change&addart=yes");
				} else {
					header("Location: https://www.cyberphoto.se/order/admin/accessories.php?alias=yes&change=$change&addart=yes");
				}
				exit;
			}

		}

		if ($delete != "" && $price->check_artikel_status($delete) && $price->check_artikel_status($change)) {
			
			$product->deleteAccessories($delete,$change);
			if (preg_match("/accessories_popup\.php/i", $_SERVER['PHP_SELF'])) {
				header("Location: https://www.cyberphoto.se/order/admin/accessories_popup.php?alias=yes&change=$change&addart=yes");
			} else {
				header("Location: https://www.cyberphoto.se/order/admin/accessories.php?alias=yes&change=$change&addart=yes");
			}
			exit;
			
		}

		if ($replace_article == "yes") {
			
			if (!($price->check_artikel_status($origin_article))) {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Nu blev det ett allvarligt fel. Vänligen börja om från början.</p>";
			}
			
			if (!($price->check_artikel_status($replace_artnr))) {
				$olright = false;
				$article = $origin_article;
				if (preg_match("/accessories_popup\.php/i", $_SERVER['PHP_SELF'])) {
					header("Location: https://www.cyberphoto.se/order/admin/accessories_popup.php?search=yes&article=$origin_article&replace_artnr=$replace_artnr&success=no");
				} else {
					header("Location: https://www.cyberphoto.se/order/admin/accessories.php?search=yes&article=$origin_article&replace_artnr=$replace_artnr&success=no");
				}
				exit;
			}
			
			$product->replaceAccessories($origin_article,$replace_artnr);
			if (preg_match("/accessories_popup\.php/i", $_SERVER['PHP_SELF'])) {
				header("Location: https://www.cyberphoto.se/order/admin/accessories_popup.php?search=yes&article=$replace_artnr&success=yes");
			} else {
				header("Location: https://www.cyberphoto.se/order/admin/accessories.php?search=yes&article=$replace_artnr&success=yes");
			}
			exit;
			
		}

		/*
		if ($submC) {
			
			$olright = true;

			$addcreatedby = $_COOKIE['login_mail'];

			if ($addActive == "yes") {
				$addActive = -1;
			} else {
				$addActive = 0;
			}
			if ($addWord == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange ett filter (ord eller del av ord)!</p>";
			}
			if ($addcreatedby == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste vara inloggad för att utföra detta!</p>";
			}
			if ($olright) {
				$filter->doFilterChange($addID,$addWord,$addcreatedby,$addActive,$addComment);
				header("Location: https://www.cyberphoto.se/order/admin/check_incoming.php");
				exit;
			}
		}
		*/

	}
	
	if ($submTime && preg_match("/set_weekend\.php/i", $_SERVER['PHP_SELF'])) {
		
		if ($_POST['weekendFromSE'] != "") {
			if (!($banners->isValidDateTime($_POST['weekendFromSE']))) {
				$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
			} else {
				setcookie("weekendFromSE", $weekendFromSE, time() + 36000, "/", "cyberphoto.se");
			}
		} else {
			setcookie("weekendFromSE", $weekendFromSE, time() - 3600, "/", "cyberphoto.se");
		}
	
		if ($_POST['weekendToSE'] != "") {
			if (!($banners->isValidDateTime($_POST['weekendToSE']))) {
				$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
			} else {
				setcookie("weekendToSE", $weekendToSE, time() + 36000, "/", "cyberphoto.se");
			}
		} else {
			setcookie("weekendToSE", $weekendToSE, time() - 3600, "/", "cyberphoto.se");
		}

		if ($_POST['weekendTextSE'] != "") {
			setcookie("weekendTextSE", $weekendTextSE, time() + 36000, "/", "cyberphoto.se");
		} else {
			setcookie("weekendTextSE", $weekendTextSE, time() - 3600, "/", "cyberphoto.se");
		}

		if ($_POST['weekendFromFI'] != "") {
			if (!($banners->isValidDateTime($_POST['weekendFromFI']))) {
				$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
			} else {
				setcookie("weekendFromFI", $weekendFromFI, time() + 36000, "/", "cyberphoto.fi");
			}
		} else {
			setcookie("weekendFromFI", $weekendFromFI, time() - 3600, "/", "cyberphoto.fi");
		}
	
		if ($_POST['weekendToFI'] != "") {
			if (!($banners->isValidDateTime($_POST['weekendToFI']))) {
				$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
			} else {
				setcookie("weekendToFI", $weekendToFI, time() + 36000, "/", "cyberphoto.fi");
			}
		} else {
			setcookie("weekendToFI", $weekendToFI, time() - 3600, "/", "cyberphoto.fi");
		}
	
		if ($_POST['weekendTextFI'] != "") {
			setcookie("weekendTextFI", $weekendTextFI, time() + 36000, "/", "cyberphoto.fi");
		} else {
			setcookie("weekendTextFI", $weekendTextFI, time() - 3600, "/", "cyberphoto.fi");
		}

		if ($_POST['weekendTextFISE'] != "") {
			setcookie("weekendTextFISE", $weekendTextFISE, time() + 36000, "/", "cyberphoto.fi");
		} else {
			setcookie("weekendTextFISE", $weekendTextFISE, time() - 3600, "/", "cyberphoto.fi");
		}

		if ($_POST['weekendFromNO'] != "") {
			if (!($banners->isValidDateTime($_POST['weekendFromNO']))) {
				$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
			} else {
				setcookie("weekendFromNO", $weekendFromNO, time() + 36000, "/", "cyberphoto.no");
			}
		} else {
			setcookie("weekendFromNO", $weekendFromNO, time() - 3600, "/", "cyberphoto.no");
		}
	
		if ($_POST['weekendToNO'] != "") {
			if (!($banners->isValidDateTime($_POST['weekendToNO']))) {
				$wrongmess .= "<p class=\"boldit_red\">- Ogiltigt datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
			} else {
				setcookie("weekendToNO", $weekendToNO, time() + 36000, "/", "cyberphoto.no");
			}
		} else {
			setcookie("weekendToNO", $weekendToNO, time() - 3600, "/", "cyberphoto.no");
		}
	
		if ($_POST['weekendTextNO'] != "") {
			setcookie("weekendTextNO", $weekendTextNO, time() + 36000, "/", "cyberphoto.no");
		} else {
			setcookie("weekendTextNO", $weekendTextNO, time() - 3600, "/", "cyberphoto.no");
		}

		if ($fi) {
			header("Location: https://www.cyberphoto.fi/order/admin/set_weekend.php");
		} elseif ($no) {
			header("Location: https://www.cyberphoto.no/order/admin/set_weekend.php");
		} else {
			if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") {
				header("Location: https://admin.cyberphoto.se/set_weekend.php");
			} else {
				header("Location: https://www.cyberphoto.se/order/admin/set_weekend.php");
			}
		}
		exit;
	
	}

	if (preg_match("/inbyte_incomming\.php/i", $_SERVER['PHP_SELF'])) {

		if ($change != "") {

			$rows = $tradein->getIncommingSpec($change);

			$addID = $rows->ping_ID;
			$addNumber = $rows->ping_Parcels;
			$addNumberBuy = $rows->ping_ParcelsBuy;
			$addActive = $rows->ping_deleted;

		}

		if ($submC) {
			
			$olright = true;
			$addBy = $_COOKIE['login_mail'];

			if ($addActive == "yes") {
				$addActive = 0;
			} else {
				$addActive = 1;
			}
			if ($addNumber == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange ett värde mellan 1-100</p>";
			}
			if ($addNumberBuy == "") {
				$olright = false;
				$wrongmess .= "<p class=\"boldit_red\">- Du måste ange ett värde mellan 0-100</p>";
			}
			if ($olright) {
				$tradein->doIncommingChange($addID,$addActive,$addNumber,$addBy,$addNumberBuy);
				header("Location: https://admin.cyberphoto.se/inbyte_incomming.php");
				exit;
			}
		}

	}

    // Funktion för att beräkna röda dagar i Sverige
    function beraknaRodaDagar($ar) {
        $rodaDagar = [];

        // Nyårsdagen
        $rodaDagar[] = "$ar-01-01";

        // Trettondedag jul
        $rodaDagar[] = "$ar-01-06";

        // Första maj
        $rodaDagar[] = "$ar-05-01";

        // Sveriges nationaldag
        $rodaDagar[] = "$ar-06-06";

        // Julafton och juldagarna
        $rodaDagar[] = "$ar-12-24";
        $rodaDagar[] = "$ar-12-25";
        $rodaDagar[] = "$ar-12-26";

        // Nyårsafton
        $rodaDagar[] = "$ar-12-31";

        // Påsken (beräknas dynamiskt)
        $paskeSondag = (new DateTime())->setDate($ar, 3, 21)->modify('+' . (easter_days($ar)) . ' days');
        $rodaDagar[] = $paskeSondag->modify('-2 days')->format('Y-m-d'); // Långfredagen
        $rodaDagar[] = $paskeSondag->modify('+1 day')->format('Y-m-d'); // Annandag påsk
        $rodaDagar[] = $paskeSondag->format('Y-m-d'); // Påskdagen

        // Kristi himmelsfärdsdag
        $rodaDagar[] = $paskeSondag->modify('+39 days')->format('Y-m-d');

        // Pingst (pingstdagen)
        $rodaDagar[] = $paskeSondag->modify('+10 days')->format('Y-m-d');

        // Midsommarafton och midsommardagen (fredagen och lördagen i midsommarveckan)
        $midsommar = (new DateTime("June 20 $ar"))->modify('next Friday');
        $rodaDagar[] = $midsommar->format('Y-m-d'); // Midsommarafton
        $rodaDagar[] = $midsommar->modify('+1 day')->format('Y-m-d'); // Midsommardagen

        return $rodaDagar;
    }

    // Funktion för att beräkna procent av arbetstid
    function procentArbetstid($year, $month) {
        $total_workdays = 0;
        $workdays_passed = 0;
        $today = new DateTime();
        $current_day = (int) $today->format('j'); // Dagens datum (t.ex. 28)
        $current_month = (int) $today->format('n'); // Nuvarande månad
        $current_hour = (int) $today->format('G'); // Aktuell timme (0-23)
    
        // Hämta röda dagar
        $red_days = beraknaRodaDagar($year);
    
        // Loopar igenom alla dagar i månaden
        for ($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++) {
            $date = new DateTime("$year-$month-$day");
            $weekday = $date->format('N'); // 1 = Måndag, 7 = Söndag
    
            // Om det är en arbetsdag (måndag-fredag) och INTE en röd dag
            if ($weekday <= 5 && !in_array($date->format('Y-m-d'), $red_days)) {
                $total_workdays++;
    
                // Räkna bara dagar som helt passerat
                if ($day < $current_day || $month < $current_month) {
                    $workdays_passed++;
                }
            }
        }
    
        // Beräkna timmar
        $total_hours = $total_workdays * 8;
        $worked_hours = $workdays_passed * 8;
    
        // Justera för pågående dag om vi är under arbetstid (08:00 - 17:00)
        if ($current_day <= cal_days_in_month(CAL_GREGORIAN, $month, $year)) {
            $date_today = new DateTime("$year-$month-$current_day");
            $weekday_today = $date_today->format('N');
    
            if ($weekday_today <= 5 && !in_array($date_today->format('Y-m-d'), $red_days)) {
                if ($current_hour >= 8 && $current_hour < 17) {
                    $worked_hours += ($current_hour - 8); // Lägg till timmar som gått idag
                } elseif ($current_hour >= 17) {
                    $worked_hours += 8; // Om arbetsdagen är slut, räkna hela dagen
                }
            }
        }
    
        // Säkerställ att worked_hours aldrig blir större än total_hours
        $worked_hours = min($worked_hours, $total_hours);
        $remaining_hours = max($total_hours - $worked_hours, 0);
    
        // Beräkna procentandel
        $arbetadProcent = ($total_hours > 0) ? ($worked_hours / $total_hours) * 100 : 0;
        $kvarvarandeProcent = 100 - $arbetadProcent;
    
        return [
            'arbetad' => round($arbetadProcent, 1),
            'kvarvarande' => round($kvarvarandeProcent, 1),
            'total_hours' => $total_hours,
            'worked_hours' => $worked_hours,
            'remaining_hours' => $remaining_hours
        ];
    }
        
    // Hämta resultat
    $work_stats = procentArbetstid(date('Y'), date('n'));

    $work_percentage = $work_stats['arbetad'];
    $remaining_percentage = $work_stats['kvarvarande'];
    $total_hours = $work_stats['total_hours'];
    $worked_hours = $work_stats['worked_hours'];
    $remaining_hours = $work_stats['remaining_hours'];
    
	if (preg_match("/salesreport\.php/i", $_SERVER['PHP_SELF'])) {

		$sales_this_year = $statistics->getSalesThisMonthNew();
		$sales_last_year = $statistics->getSalesThisMonthLastYear();

		$sales_percentage = $sales_last_year > 0 ? round(($sales_this_year / $sales_last_year) * 100, 1) : 0;

		$differens_between_years = $sales_this_year - $sales_last_year;

		$manader = [
			1 => "januari", 2 => "februari", 3 => "mars",
			4 => "april", 5 => "maj", 6 => "juni",
			7 => "juli", 8 => "augusti", 9 => "september",
			10 => "oktober", 11 => "november", 12 => "december"
		];

	}

	
?>
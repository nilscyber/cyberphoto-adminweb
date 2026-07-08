<?php

Class CCheckIP {

	private static $permCache = null;

	// Databasbackad behörighetskontroll - se cyberadmin.employees / permissions / employee_permissions
	static function hasPermission($permissionKey) {
		if (self::$permCache === null) {
			self::$permCache = self::loadPermissionsForCurrentUser();
		}
		return in_array($permissionKey, self::$permCache, true);
	}

	private static function loadPermissionsForCurrentUser() {
		$mail = isset($_COOKIE['login_mail']) ? $_COOKIE['login_mail'] : '';
		if ($mail === '') { return array(); }

		$db = Db::getConnection(false);
		$mailEsc = mysqli_real_escape_string($db, $mail);
		$sql = "SELECT ep.permission_key
				FROM cyberadmin.employees e
				JOIN cyberadmin.employee_permissions ep ON ep.employee_id = e.employee_id
				WHERE e.login_mail = '$mailEsc' AND e.active = 1";
		$res = mysqli_query($db, $sql);
		$out = array();
		while ($res && $row = mysqli_fetch_assoc($res)) { $out[] = $row['permission_key']; }
		return $out;
	}

	public static function getClientIP($ip_adress){
		$ip = $ip_adress;
		 if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];  
		 }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) { 
				$ip = $_SERVER["REMOTE_ADDR"]; 
		 }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
				$ip = $_SERVER["HTTP_CLIENT_IP"]; 
		 } 
			
		 return $ip;
	}
	public static function checkIpAdress($ip_adress) {
		$ip_adress = CCheckIP::getClientIP($ip_adress);		
		
		if (isset($_SESSION['EXTERNAL_SWITCH']) && $_SESSION['EXTERNAL_SWITCH'] == 1) { // Om man manuellt st�llt om till extern visning.
			return false;
		} elseif ($ip_adress == "192.168.1.240" || $ip_adress == "192.168.1.241" || $ip_adress == "192.168.1.242x") { // 140110, tillf�lligt tar vi bort datorerna i lagershopen fr�n internt visning
			return false;
		} elseif ($ip_adress == "192.168.1.89x") { // test att inte vara intern
			return false;
		} elseif ($ip_adress == "192.168.1.8" || $ip_adress == "192.168.1.7" ) { // lastbalanserare
			return false;			
		} elseif (preg_match("/192\.168\.1\./", $ip_adress)) { // V�ra interna IP adresser p� CyberPhoto
			return true;
		} elseif ($ip_adress == "85.30.13.164x" || $ip_adress == "83.219.209.231x") { // Special adresser. Sjabo hemma, etc.etc. Bara att fylla p�.
			return true;
		} else {
			return false;
		}
		
	}

	// Denna kollar IP nummren i lagershopen
	static function checkIpAdressLagershop($ip_adress) {
		$ip_adress = CCheckIP::getClientIP($ip_adress);
		if ($ip_adress == "192.168.1.89x" || $ip_adress == "192.168.1.240" || $ip_adress == "192.168.1.241" || $ip_adress == "192.168.1.242x") {
			return true;
		} else {
			return false;
		}
		
	}

	// Denna kollar IP nummren f�r webb-administrat�rer
	static function checkIpAdressWebAdmins($ip_adress) {
		$ip_adress = CCheckIP::getClientIP($ip_adress);
		if ($ip_adress == "192.168.1.89" || $ip_adress == "192.168.1.65x" || $ip_adress == "192.168.1.66x" || $ip_adress == "192.168.1.98" || 
			$ip_adress == "192.168.1.99") {
			return true;
		} else {
			return false;
		}
		
	}

	// Denna kollar IP nummren f�r personer med ut�kade privilegier
	static function checkIpAdressExtendedPrivileges($ip_adress) {
		$ip_adress = CCheckIP::getClientIP($ip_adress);	
		if ($_COOKIE['login_ok'] == "true") {
			return true;
		} else {
			return false;
		}
	
	}

	// Denna kollar om IP-nummret finns i v�rt n�tverk
	static function checkIpAdressInHouse($ip_adress) {
		$ip_adress = CCheckIP::getClientIP($ip_adress);	
		if (preg_match("/192\.168\.1\./", $ip_adress)) {
			return true;
		} else {
			return false;
		}
	
	}
	
	// Denna kollar om personen jobbar med inbyten
	static function checkIfLoginIsTradeIn() {
		return self::hasPermission('trade_in');
	}
	// Denna kollar om personen har prioritetsvisning
	static function checkIfLoginIsPriority() {
		return self::hasPermission('priority');
	}
	// Denna kollar om personen har inköpsbehörighet
	static function checkIfPurchaseValid() {
		return self::hasPermission('purchase_valid');
	}
	// För ett cleanare sök
	static function forCleanerSearch() {
		return self::hasPermission('clean_search');
	}
	// Denna kollar om personen får se ej lanserade produkter (kommande launchdate) på new_products.php
	static function checkIfCanSeeUpcomingProducts() {
		return self::hasPermission('product_permissions');
	}
	// Denna kollar om personen är inköpskollega
	static function checkIfPurchaseColleague() {
		return self::hasPermission('purchase_colleague');
	}

	// Denna kollar om personen får administrera behörigheter (denna sida)
	static function checkIfCanManagePermissions() {
		return self::hasPermission('manage_permissions');
	}

}

?>

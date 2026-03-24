<?php

Class CCheckIP {
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
		$ip_adress = CCheckIP::getClientIP($ip_adress);	
		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'borje@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'albin@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'marcus.johansson@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'albin.soderlind@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'andreas.almquist@cyberphoto.se') {
			return true;
		} else {
			return false;
		}
	
	}
	// Denna kollar om personen jobbar med inbyten
	static function checkIfLoginIsPriority() {
		$ip_adress = CCheckIP::getClientIP($ip_adress);	
		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'jonas@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'emil.lindberg@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'albin@cyberphoto.seX') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'emma@cyberphoto.seX') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'borje@cyberphoto.seX') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'johan.eriksson@cyberphoto.se') {
			return true;
		} else {
			return false;
		}
	
	}
	// Denna kollar om personen jobbar med inbyten
	static function checkIfPurchaseValid() {
		$ip_adress = CCheckIP::getClientIP($ip_adress);	
		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'borje@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'albin@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'marcus.johansson@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'ulrika@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'malin@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'nils@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'maria@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'erik@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'finbar@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'jonas@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'johan.eriksson@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'andreas.almquist@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'sandra.strandgren@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'jennie.hedstrom@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'anna@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'louise@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'kenneth.ly@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'emil.lindberg@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'robin@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'boyd@cyberphoto.se') {
			return true;
		} else {
			return false;
		}
	
	}
	// För ett cleanare sök
	static function forCleanerSearch() {
		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'robin@cyberphoto.seX') {
			return true;
		} else {
			return false;
		}
	
	}
	// Denna kollar om personen jobbar med inbyten
	static function checkIfPurchaseColleague() {
		$ip_adress = CCheckIP::getClientIP($ip_adress);	
		if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'boyd@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'emil.lindberg@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'malin@cyberphoto.se') {
			return true;
		} elseif ($_COOKIE['login_mail'] == 'jennie.hedstrom@cyberphoto.se') {
			return true;
		} else {
			return false;
		}
	
	}
	
}

?>

<?php
/*
 * Created on 2006-jan-31
 *
 */
 //http://www.devshed.com/c/a/PHP/Creating-a-Secure-PHP-Login-Script/
 
session_start();
  
class User { 
	var $db = null; // PEAR::DB pointer 
	var $failed = false; // failed login attempt 
	var $date; // current date GMT 
	var $kundnr = 0; // the current user's kundnr 

	function User($db, $date) { 
		$this->db = $db; 
		$this->date = $date; 
		$this->session_defaults();
		if ($_SESSION['logged']) { 
			$this->_checkSession(); 
		} elseif ( isset($_COOKIE['loginSave']) ) { 
			$this->_checkRemembered($_COOKIE['loginSave']); 
		} 
	}
	function session_defaults() { 
		$_SESSION['logged'] = false; 
		$_SESSION['uid'] = 0; 
		$_SESSION['userName'] = ''; 
		$_SESSION['cookie'] = 0; 
		$_SESSION['remember'] = false; 
	} 	
	function _checkLogin($userName, $password, $remember) { 
		$userName = $this->db->quote($userName); 
		//$password = $this->db->quote(md5($password)); 
		$sql = "SELECT * FROM Kund WHERE " . 
		"userName = $userName AND " . 
		"kundid = $password"; 
		//echo $sql . "<br>";
		$result = $this->db->getRow($sql); 
		if ( is_object($result) ) { 
			$this->_setSession($result, $remember); 
			return true; 
		} else { 
			$this->failed = true; 
			$this->_logout(); 
			return false; 
		} 
	} 
	
	function _setSession($values, $remember, $init = true) { 
		//echo $values->kundnr;
		$this->kundnr = $values->kundnr; 
		$_SESSION['uid'] = $this->kundnr; 
		$_SESSION['userName'] = htmlspecialchars($values->userName); 
		$_SESSION['cookie'] = $values->cookie; 
		$_SESSION['logged'] = true; 
		if ($remember) { 
			$this->updateCookie($values->cookie, true); 
		} 
		if ($init) { 
			
			$session = $this->db->quote(session_id()); 
			
			$ip = $this->db->quote($_SERVER['REMOTE_ADDR']); 

			$sql = "UPDATE Kund SET session = $session, ip = $ip WHERE " . 
			"kundnr = $this->kundnr"; 
			$this->db->query($sql); 
		} 
	} 
	function updateCookie($cookie, $save) { 
		$_SESSION['cookie'] = $cookie; 
		if ($save) { 
			$cookie = serialize(array($_SESSION['userName'], $cookie) ); 
			setcookie('loginSave', $cookie, time() + 31104000, '/', '.cyberphoto.se');
			//setcookie ("kundvagn", "", time() - 3600, "/", ".cyberphoto.se"); 
		} 
	}
	
	function _checkRemembered($cookie) { 
		list($userName, $cookie) = @unserialize($cookie); 
		if (!$userName or !$cookie) return; 
		$userName = $this->db->quote($userName); 
		$cookie = $this->db->quote($cookie); 
		$sql = "SELECT * FROM member WHERE " . 
		"(userName = $userName) AND (cookie = $cookie)"; 
		$result = $this->db->getRow($sql); 
		if (is_object($result) ) { 
			$this->_setSession($result, true); 			
		} 
	}

	function _checkSession() { 
		$userName = $this->db->quote($_SESSION['userName']); 
		$cookie = $this->db->quote($_SESSION['cookie']); 
		$session = $this->db->quote(session_id()); 
		$ip = $this->db->quote($_SERVER['REMOTE_ADDR']); 
		$sql = "SELECT * FROM member WHERE " . 
		"(userName = $userName) AND (cookie = $cookie) AND " . 
		"(session = $session) AND (ip = $ip)"; 
		$result = $this->db->getRow($sql); 
		if (is_object($result) ) { 
			$this->_setSession($result, false, false); 
		} else { 
			$this->_logout(); 
		} 
	} 	 
	function _logout() {
	}		
} 


?>

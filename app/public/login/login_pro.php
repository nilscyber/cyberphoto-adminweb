<?php
	session_start();

	// $_SESSION["id"] = $_POST["id"];
	// $_SESSION["name"] = $_POST["name"];
	// $_SESSION["email"] = $_POST["email"];


	function get_domain($email_address) {
		
		// Split the email address at the @ symbol
		$email_parts = explode( '@', $email_address );
		
		// Pop off everything after the @ symbol
		$domain = array_pop( $email_parts );
		
		return $domain;
	}

	$email_address = $_POST["email"];
	
	// Vi gör kontroll om användaren loggar in med en cyberphoto.nu adress - Om korrekt så sätter vi en kaka på detta.
	if (get_domain($email_address) == "cyberphoto.nu") {
		require_once("CWebADIntern.php");
		$intern = new CWebADIntern();

		setcookie('login_ok', 'true', time() + 36000, '/');
		setcookie('login_name', $_POST["name"], time() + 36000, '/');
		setcookie('login_userid', $intern->findUserId($_POST["email"]), time() + 36000, '/');
		setcookie('login_mail', $_POST["email"], time() + 36000, '/');
	}


?>
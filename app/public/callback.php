<?php
	session_start();
	$config = require_once 'config.php';

	function get_domain($email_address) {
		
		// Split the email address at the @ symbol
		$email_parts = explode( '@', $email_address );
		
		// Pop off everything after the @ symbol
		$domain = array_pop( $email_parts );
		
		return $domain;
	}

	if (isset($_GET['code'])) {
		// Byt ut authorization code mot access token
		$token_params = [
			'client_id' => $config['client_id'],
			'client_secret' => $config['client_secret'],
			'code' => $_GET['code'],
			'redirect_uri' => $config['redirect_uri'],
			'grant_type' => 'authorization_code'
		];

		// Hämta access token
		$ch = curl_init($config['token_url']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_params));
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		$token_data = json_decode($response, true);
		
		if (isset($token_data['access_token'])) {
			// Hämta användarinformation
			$graph_url = 'https://graph.microsoft.com/v1.0/me';
			$ch = curl_init($graph_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Authorization: Bearer ' . $token_data['access_token'],
				'Content-Type: application/json'
			]);
			
			$user_response = curl_exec($ch);
			curl_close($ch);
			
			$user_data = json_decode($user_response, true);
			
			if (isset($user_data['userPrincipalName'])) {
				
				$email_address = $user_data['userPrincipalName'];

				// Vi gör kontroll om användaren loggar in med en cyberphoto.nu adress - Om korrekt så sätter vi en kaka på detta.
				if (get_domain($email_address) == "cyberphoto.se") {
					require_once("CWebADIntern.php");
					$intern = new CWebADIntern();

					setcookie('login_ok', 'true', time() + 36000, '/');
					setcookie('login_name', $user_data['displayName'], time() + 36000, '/');
					setcookie('login_userid', $intern->findUserId($user_data['userPrincipalName']), time() + 36000, '/');
					setcookie('login_mail', $user_data['userPrincipalName'], time() + 36000, '/');
				} else {
					echo "Denna e-postadress är inte giltig. Måte vara en cyberphoto.se adress";
					exit;
				}
				
				$_SESSION['user_email'] = $user_data['userPrincipalName'];
				$_SESSION['user_name'] = $user_data['displayName'];
				
				// För att se vad som händer
				// echo "E-post: " . $_SESSION['user_email'] . "<br>";
				// echo "Namn: " . $_SESSION['user_name'] . "<br>";

				// Vänta 3 sekunder så vi hinner se datan
				// sleep(3);
				if (isset($_SESSION['return_to'])) {
					if ($_SESSION['return_to'] == "https://admin.cyberphoto.se/index.php") {
						header('Location: profile.php');
					} else {
						// header('Location: profile.php');
						header('Location: ' . $_SESSION['return_to']);
					}
					unset($_SESSION['return_to']);
				} else {
					header('Location: profile.php');
				}
				exit;
			}
		}
	}

	// Om något går fel, återgå till startsidan
	header('Location: index.php');
	exit;
<?php
// Använd access token för att få användarens information
$user_info_url = 'https://graph.microsoft.com/v1.0/me';
$headers = array(
    'Authorization: Bearer ' . $access_token
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $user_info_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$user_info_response = curl_exec($ch);
curl_close($ch);

$user_info = json_decode($user_info_response, true);
$name = $user_info['displayName'];
$email = $user_info['mail'];

// Nu har du användarens namn och e-postadress i PHP-variabler
echo 'Namn: ' . $name . '<br>';
echo 'E-post: ' . $email;
?>
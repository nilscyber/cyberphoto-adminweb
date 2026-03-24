<?php
// redirect.php
$client_id = '4afcf456-0e4d-4855-8661-4c3f74ad0466';
$client_secret = 'f3c1cc56-fba4-47b0-9712-0616f62c1ab0';
$redirect_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/redirect.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Byt ut koden mot en access token
    $token_url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    $token_data = array(
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'code' => $code,
        'grant_type' => 'authorization_code'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response, true);
    $access_token = $response_data['access_token'];

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
}
?>
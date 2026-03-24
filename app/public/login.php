<?php
// login.php
$client_id = '4afcf456-0e4d-4855-8661-4c3f74ad0466';
$redirect_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/redirect.php';
$scopes = 'openid email profile';

header('Location: https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=' . $client_id . '&response_type=code&redirect_uri=' . urlencode($redirect_uri) . '&scope=' . urlencode($scopes));
exit();
?>
<?php
// Konfigurations-fil för Microsoft OAuth
return [
    'client_id' => getenv('ENTRA_CLIENT_ID') ?: '',
    'client_secret' => getenv('ENTRA_CLIENT_SECRET') ?: '',
    'redirect_uri' => (
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            ? 'https' : 'http'
    ) . '://' . ($_SERVER['HTTP_HOST'] ?? 'admin.cyberphoto.se') . '/callback.php',
    'tenant' => 'common',
    'auth_url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
    'token_url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
    'scope' => 'https://graph.microsoft.com/User.Read'
];
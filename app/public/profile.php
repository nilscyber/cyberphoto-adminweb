<?php
// =========================================================
//  Inkluderingar (layout, klassfiler)
// =========================================================
include_once("top.php");
include_once("header.php");

// =========================================================
//  Hämta användardata (namn, e-post, ADempiere-ID)
// =========================================================

// Namn
if(isset($_COOKIE['login_name'])) {
    $userName = $_COOKIE['login_name'];
} elseif (!empty($_SESSION['user_name'])) {
    $userName = $_SESSION['user_name'];
} else {
    $userName = '';
}

// Fixa teckenkodning för namn (UTF-8 -> Latin1)
// $userName = $userName;

// E-post
if (isset($_COOKIE['login_mail'])) {
    $userEmail = $_COOKIE['login_mail'];
} elseif (!empty($_SESSION['user_email'])) {
    $userEmail = $_SESSION['user_email'];
} else {
    $userEmail = '';
}

// ADempiere user-id från kaka
$adUserIdCookie = isset($_COOKIE['login_userid']) ? (int)$_COOKIE['login_userid'] : 0;

// Debug-override via URL: profile.php?uid=1234567
$overrideUserId = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;

// Det user_id vi faktiskt använder i alla frågor/renderingar
$adUserId = ($overrideUserId > 0) ? $overrideUserId : $adUserIdCookie;


// =========================================================
//  Sid-specifik CSS (widget-boxar)
//  Flytta gärna till global CSS när ni är nöjda.
// =========================================================

echo '<style type="text/css">
    .profile-widget {
        border: 1px solid #dddddd;
        border-radius: 8px;
        background: #fafafa;
        padding: 12px 16px;
        margin: 16px 0;
    }
    .profile-widget h2,
    .profile-widget h3 {
        margin-top: 0;
        margin-bottom: 8px;
        font-size: 16px;
    }
</style>' . "\n";

// =========================================================
//  Render: huvudrubrik och grundinformation
// =========================================================

// Välkomst-rubrik
echo '<h1>Välkommen ' . $userName . '!</h1>' . "\n";

// Visning av e-post
if (!empty($userEmail)) {
    echo '<p>Du är inloggad med ditt Microsoft-konto: <b>' .
         htmlspecialchars($userEmail) .
         '</b></p>' . "\n";
}

// Info om debug-override (om vi visar någon annans user_id än den inloggades)
if ($overrideUserId > 0 && $overrideUserId != $adUserIdCookie) {
    echo '<p><i>Visar data för user_id ' . (int)$overrideUserId . ' (debug-override).</i></p>' . "\n";
}

// Länk tillbaka till startsidan
echo '<p><a href="index.php">Fortsätt till startsidan</a></p>' . "\n";

// =========================================================
//  Widget: Dina aktuella offerter
// =========================================================

// Hämta HTML från CAdminUser  kan vara tom sträng
$quotesHtml = CAdminUser::renderUserQuotations($adUserId, 50);

// Bara om vi faktiskt har något att visa renderar vi boxen
if ($quotesHtml !== '') {

    echo '<div class="profile-widget">' . "\n";
    echo '  <h3>Dina aktuella offerter</h3>' . "\n";
    echo $quotesHtml . "\n";
    echo '</div>' . "\n\n";
}

// =========================================================
//  Widget: Dina pågående ordrar (NYA SEKTIONEN)
// =========================================================

$ordersHtml = CAdminUser::renderUserActiveOrders($adUserId, 50);

if ($ordersHtml !== '') {
    echo '<div class="profile-widget">' . "\n";
    echo '  <h3>Dina pågående ordrar</h3>' . "\n";
    echo $ordersHtml . "\n";
    echo '</div>' . "\n\n";
}

// =========================================================
//  Widget: Alla säljare  pågående offerter
// =========================================================

$globalHtml = CAdminUser::renderGlobalQuotationsGrouped($adUserId, 50, 5000);

if ($globalHtml !== '') {
    echo '<div class="profile-widget">' . "\n";
    echo '  <h3>Alla säljare - pågående offerter</h3>' . "\n";
    echo $globalHtml . "\n";
    echo '</div>' . "\n\n";
}


// =========================================================
//  (Framtida widgets läggs här)
// =========================================================

include_once("footer.php");

// =========================================================
//  Footer / avslutning
// =========================================================
include_once("footer.php");

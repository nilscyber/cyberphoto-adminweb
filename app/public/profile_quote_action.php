<?php
include_once("top.php"); // ger session, Db, CAdminUser etc.

// Säkerställ att användaren är inloggad
$adUserIdCookie = isset($_COOKIE['login_userid']) ? (int)$_COOKIE['login_userid'] : 0;
if ($adUserIdCookie <= 0) {
    // ingen giltig user -> tillbaka till login eller index
    header("Location: index.php");
    exit;
}

$action  = isset($_GET['action'])   ? $_GET['action']   : '';
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Default 5 dagar, men kan göras parametriserbart senare
$days = 5;

if ($action === 'snooze' && $orderId > 0) {
    CAdminUser::snoozeQuotation($adUserIdCookie, $orderId, $days);
}

// Tillbaka till profilen
header("Location: profile.php");
exit;

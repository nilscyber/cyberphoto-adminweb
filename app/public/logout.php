<?php
session_start();
session_destroy();
unset($_SESSION['token']);
// unset($_SESSION['admin_ok']);
setcookie('login_ok', '', time() - 60, '/');
setcookie('login_name', '', time() - 60, '/');
setcookie('login_userid', '', time() - 60, '/');
setcookie('login_mail', '', time() - 60, '/');
header('Location: index.php');
exit;
<?php
/**
 * cronjob/check_incoming_words.php
 * Kör CFilterIncoming->getWordsToCheck() på schema (utan sessions).
 * PHP 5.6-kompatibel.
 */

date_default_timezone_set('Europe/Stockholm');
set_time_limit(0);

if (!headers_sent()) {
    header('Content-Type: text/plain; charset=utf-8');
}

ignore_user_abort(true);
if (function_exists('fastcgi_finish_request')) {
    echo '[' . date('Y-m-d H:i:s') . "] ACCEPTED\n";
    fastcgi_finish_request();
}

// Säkerställ att php-libben finns i include_path (skadar inte om den redan finns)
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/phplib');

// Autoload enligt ert standardmönster
spl_autoload_register(function ($class) {
    include $class . '.php';
});

// Låsning så att vi aldrig kör två samtidigt
$lockFile = sys_get_temp_dir() . '/cron_check_incoming_words.lock';
$lockFp = fopen($lockFile, 'c');

if (!$lockFp) {
    echo '[' . date('Y-m-d H:i:s') . "] ERROR: Could not open lock file\n";
    exit(2);
}

if (!flock($lockFp, LOCK_EX | LOCK_NB)) {
    echo '[' . date('Y-m-d H:i:s') . "] SKIP: Already running\n";
    fclose($lockFp);
    exit(0);
}

ftruncate($lockFp, 0);
fwrite($lockFp, "pid=" . getmypid() . " time=" . date('Y-m-d H:i:s') . "\n");
fflush($lockFp);

try {
    $filter = new CFilterIncoming();

    // Kör jobbet
    $filter->getWordsToCheck();

    echo '[' . date('Y-m-d H:i:s') . "] OK: getWordsToCheck executed\n";

} catch (Exception $e) {
    echo '[' . date('Y-m-d H:i:s') . '] ERROR: ' . $e->getMessage() . "\n";
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
    exit(2);
}

flock($lockFp, LOCK_UN);
fclose($lockFp);
exit(0);

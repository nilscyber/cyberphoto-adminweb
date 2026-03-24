<?php
/**
 * cronjob/article_monitor_and_campaign.php
 * Kör artikelbevakning (0..3) + kampanjbevakning var 15:e minut.
 * PHP 5.6-kompatibel.
 */

date_default_timezone_set('Europe/Stockholm');
set_time_limit(0);

if (!headers_sent()) {
    header('Content-Type: text/plain; charset=utf-8');
}

set_include_path(get_include_path() . PATH_SEPARATOR . '/home/phplib');

spl_autoload_register(function ($class) {
    include $class . '.php';
});

// Låsning för att undvika parallellkörning
$lockFile = sys_get_temp_dir() . '/cron_article_monitor_campaign.lock';
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
    $monitor  = new CMonitorArticles();

    $monitor->checkArticlesLevel(0); // lagersaldon
    // $monitor->checkArticlesLevel(1); // inpriser
    // $monitor->checkArticlesLevel(2); // utpriser
    $monitor->checkArticlesLevel(3); // orderposter

    echo '[' . date('Y-m-d H:i:s') . "] OK: monitor(0..3) + campaign executed\n";

} catch (Exception $e) {
    echo '[' . date('Y-m-d H:i:s') . '] ERROR: ' . $e->getMessage() . "\n";
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
    exit(2);
}

flock($lockFp, LOCK_UN);
fclose($lockFp);
exit(0);

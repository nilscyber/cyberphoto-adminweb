<?php
/**
 * cronjob/monitor_allocated.php
 * Kör CAllocated->getActualMonitorAllocated() på schema.
 * PHP 5.6-kompatibel.
 */

date_default_timezone_set('Europe/Stockholm');
set_time_limit(0);

if (!headers_sent()) {
    header('Content-Type: text/plain; charset=utf-8');
}

// Säkerställ php-lib i include_path
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/phplib');

// Autoload enligt ert standardmönster
spl_autoload_register(function ($class) {
    include $class . '.php';
});

// Låsning för att undvika parallellkörning
$lockFile = sys_get_temp_dir() . '/cron_monitor_allocated.lock';
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
    $allocated = new CAllocated();
    $allocated->getActualMonitorAllocated();

    echo '[' . date('Y-m-d H:i:s') . "] OK: getActualMonitorAllocated executed\n";

} catch (Exception $e) {
    echo '[' . date('Y-m-d H:i:s') . '] ERROR: ' . $e->getMessage() . "\n";
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
    exit(2);
}

flock($lockFp, LOCK_UN);
fclose($lockFp);
exit(0);

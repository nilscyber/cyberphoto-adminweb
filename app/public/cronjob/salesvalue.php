<?php
/**
 * cronjob/salesvalue_v1.php
 * PHP 5.6 compatible
 *
 * HTTP:
 *   /cronjob/salesvalue.php?mode=ongoing
 *   /cronjob/salesvalue.php?mode=final
 *
 * CLI:
 *   php salesvalue_v1.php --mode=ongoing
 *   php salesvalue_v1.php --mode=final
 */

date_default_timezone_set('Europe/Stockholm');
set_time_limit(0);

if (!headers_sent()) {
    header('Content-Type: text/plain; charset=utf-8');
}

// Säkerställ att php-libben finns i include_path (behöver inte skada även om den redan finns)
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/phplib');

// Autoload enligt ert standardmönster
spl_autoload_register(function ($class) {
    include $class . '.php';
});

// Hämta mode från CLI eller HTTP
$mode = null;

if (php_sapi_name() === 'cli') {
    $opts = getopt('', array('mode:'));
    if (isset($opts['mode']) && $opts['mode'] !== '') {
        $mode = $opts['mode'];
    }
} else {
    if (isset($_GET['mode']) && $_GET['mode'] !== '') {
        $mode = $_GET['mode'];
    }
}

if ($mode !== 'ongoing' && $mode !== 'final') {
    echo '[' . date('Y-m-d H:i:s') . "] ERROR: Invalid mode. Use ongoing|final\n";
    exit(1);
}

// Låsning: förhindra parallellkörning (t.ex. om servern är seg och cron triggar igen)
$lockFile = sys_get_temp_dir() . '/cron_salesvalue.lock';
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
    $store    = new CStoreStatus();
    $adintern = new CWebADInternSuplier();
    $cpto     = new CCPto();
    $sales    = new CSales();

    $storeValue = round($adintern->displayStoreValueSimple(), 0);
    $outgoing   = $cpto->getOutgoingOrders(true);

    if ($mode === 'ongoing') {
        // Flagga 1 = ongoing
        $store->addStockValueOngoing($storeValue);
        $sales->salesAddValue($outgoing, 1);

        echo '[' . date('Y-m-d H:i:s') . '] OK ongoing | store=' . $storeValue . ' | outgoing=' . $outgoing . "\n";
    } else {
        // Flagga 0 = final
        $store->addStockValue($storeValue);
        $sales->salesAddValue($outgoing, 0);

        echo '[' . date('Y-m-d H:i:s') . '] OK final | store=' . $storeValue . ' | outgoing=' . $outgoing . "\n";
    }

} catch (Exception $e) {
    echo '[' . date('Y-m-d H:i:s') . '] ERROR: ' . $e->getMessage() . "\n";
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
    exit(2);
}

flock($lockFp, LOCK_UN);
fclose($lockFp);
exit(0);

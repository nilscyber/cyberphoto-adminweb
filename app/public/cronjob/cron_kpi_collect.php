<?php
spl_autoload_register(function ($class) {
    include $class . '.php';
});

$kpi = new CKpi();

$salesToday   = $kpi->collectSalesToday(date('Y-m-d')); // dagsbunden
$printed      = $kpi->collectOrdersPrinted();           // backlog
$notPrinted   = $kpi->collectOrdersNotPrinted();        // backlog

// --- Product feed (lists) ---
$created = $kpi->collectProductsCreatedLast12h();
$kpi->upsertKpiJson('products.created', null, $created, 'pg');

$disc = $kpi->collectProductsDiscontinuedLast12h();
$kpi->upsertKpiJson('products.discontinued', null, $disc, 'pg');

echo json_encode(array(
  'ok' => true,
  'sales_today' => $out,
  'orders_printed' => $printed,
  'orders_not_printed' => $notPrinted,
  'products_created' => $created['count'],
  'products_discontinued' => $disc['count']
));


?>
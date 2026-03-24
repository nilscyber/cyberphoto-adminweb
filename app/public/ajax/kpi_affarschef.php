<?php
header('Content-Type: application/json; charset=utf-8');
require_once("Db.php");

$db = Db::getConnectionDb('cyberadmin');

function esc($db, $s) {
    $s = (string)$s;
    if (is_object($db) && method_exists($db, 'real_escape_string')) return $db->real_escape_string($s);
    if (is_resource($db) && function_exists('mysql_real_escape_string')) return mysqli_real_escape_string($db, $s);
    return addslashes($s);
}

$keys = array(
  'sales.today.inout',
  'orders.printed',
  'orders.not_printed',
  'products.created',
  'products.discontinued'
);
$in = array();
foreach ($keys as $k) $in[] = "'" . esc($db, $k) . "'";
$inSql = implode(',', $in);

$today = date('Y-m-d');

$sql = "
  SELECT kpi_key, kpi_date, count_value, sum_value, json_value, updated_at
  FROM kpi_cache
  WHERE kpi_key IN ($inSql)
  ORDER BY updated_at DESC
";

$rows = array();

if (is_object($db) && method_exists($db, 'query')) {
    $res = $db->query($sql);
    if ($res) while ($r = $res->fetch_assoc()) $rows[] = $r;
} else {
    $res = mysqli_query($db, $sql);
    if ($res) while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
}

$out = array();
foreach ($rows as $r) {
    // ta senaste per key
    if (!isset($out[$r['kpi_key']])) {
        $out[$r['kpi_key']] = array(
            'count' => (int)$r['count_value'],
            'sum'   => (float)$r['sum_value'],
			'json'       => isset($r['json_value']) ? (string)$r['json_value'] : null,
            'updated_at' => (string)$r['updated_at'],
            'kpi_date' => $r['kpi_date']
        );
    }
}

echo json_encode(array(
    'ok' => true,
    'today' => $today,
    'data' => $out
));

<?php
include_once("top.php");

$select  = "SELECT prod.value, ";
$select .= "manu.name AS manu_name, prod.name AS prod_name, ";
$select .= "cat.name AS cat_name, ";
$select .= "pstock.qtyavailable, ";
$select .= "cbp.name AS lev_name, ";
$select .= "COALESCE(pp.pricelimit, (SELECT mc.currentcostprice FROM m_cost mc WHERE mc.m_product_id = prod.m_product_id ORDER BY mc.updated DESC LIMIT 1), 0) AS net_price ";
$select .= "FROM m_product_cache pstock ";
$select .= "LEFT JOIN m_product prod ON pstock.m_product_id = prod.m_product_id ";
$select .= "LEFT JOIN xc_manufacturer manu ON prod.xc_manufacturer_id = manu.xc_manufacturer_id ";
$select .= "LEFT JOIN m_product_category cat ON prod.m_product_category_id = cat.m_product_category_id ";
$select .= "LEFT JOIN m_product_po prod_po ON pstock.m_product_id = prod_po.m_product_id ";
$select .= "LEFT JOIN c_bpartner cbp ON cbp.c_bpartner_id = prod_po.c_bpartner_id ";
$select .= "LEFT JOIN m_productprice pp ON pp.m_product_id = prod.m_product_id AND pp.m_pricelist_version_id = 1000000 ";
$select .= "WHERE pstock.m_warehouse_id = 1000000 AND pstock.qtyavailable < 0 ";
$select .= "AND NOT cat.value IN ('314','12','700','486','1000517') ";
$select .= "AND prod_po.iscurrentvendor = 'Y' ";
$select .= "ORDER BY pstock.qtyavailable ASC, manu.name ASC ";

$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

$filename = 'ko_produkter_' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');

// UTF-8 BOM så Excel förstår teckenkodningen
echo "\xEF\xBB\xBF";

$sep = ';';

// Rubrikrad
$headers = array('Artnr', 'Benämning', 'Kategori', 'Kö', 'Kö-värde (kr)', 'Leverantör', 'Nettopris');
echo implode($sep, $headers) . "\r\n";

$csvField = function($s) use ($sep) {
    $s = str_replace('"', '""', (string)$s);
    if (strpos($s, $sep) !== false || strpos($s, '"') !== false || strpos($s, "\n") !== false) {
        $s = '"' . $s . '"';
    }
    return $s;
};

while ($res && $row = pg_fetch_object($res)) {
    $product  = trim(($row->manu_name ?? '') . ' ' . ($row->prod_name ?? ''));
    $qty      = (int)$row->qtyavailable;
    $netPrice = (float)$row->net_price;
    $koValue  = abs($qty) * $netPrice;

    echo implode($sep, array(
        $csvField($row->value),
        $csvField($product),
        $csvField($row->cat_name ?? ''),
        $qty,
        number_format((int)round($koValue), 0, ',', ''),
        $csvField($row->lev_name ?? ''),
        number_format((int)round($netPrice), 0, ',', ''),
    )) . "\r\n";
}

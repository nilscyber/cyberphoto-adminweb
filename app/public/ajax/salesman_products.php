<?php
header('Content-Type: text/html; charset=UTF-8');
include_once 'CCheckIpNumber.php';
spl_autoload_register(function ($class) {
    include $class . '.php';
});

$h = function ($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
};

$seller = isset($_GET['seller']) ? trim($_GET['seller']) : '';
$start  = isset($_GET['start']) ? trim($_GET['start']) : '';
$end    = isset($_GET['end']) ? trim($_GET['end']) : '';

$validDate = function ($d) {
    return (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
};

if ($seller === '' || !$validDate($start) || !$validDate($end)) {
    echo '<p class="italic">Ogiltiga parametrar.</p>';
    exit;
}

$sales = new CSales();
$rows  = $sales->getSalesPerUserProductDetails($seller, $start, $end);

if (empty($rows)) {
    echo '<p class="italic">Inga produkter sålda under perioden.</p>';
    exit;
}

$totalAntal = 0;
$totalSumma = 0;

// Gruppera på ordernummer så varje order bara skriver ut ordercellen (med rowspan)
// på sin första rad. "Övrigt"-raden (tomt ordernummer) grupperas aldrig ihop med
// något annat, den ska alltid stå för sig själv.
$groupOf  = [];
$groupSize = [];
$prevOrder = null;
$groupIdx  = -1;
foreach ($rows as $i => $r) {
    $isUnallocated = ($r['order_no'] === '');
    if ($isUnallocated || $r['order_no'] !== $prevOrder) {
        $groupIdx++;
        $groupSize[$groupIdx] = 0;
    }
    $groupSize[$groupIdx]++;
    $groupOf[$i] = $groupIdx;
    $prevOrder = $isUnallocated ? null : $r['order_no'];
}

echo '<table class="table-list">';
echo '<thead><tr><th>Order</th><th>Produkt</th><th class="align_center">Antal</th><th class="align_right">Summa</th></tr></thead>';
echo '<tbody>';
$lastGroup = null;
foreach ($rows as $i => $r) {
    $isUnallocated = ($r['antal'] === null);
    $totalAntal += $isUnallocated ? 0 : $r['antal'];
    $totalSumma += $r['summa'];

    $isNewGroup = ($groupOf[$i] !== $lastGroup);
    $lastGroup = $groupOf[$i];

    $rowStyle = $isUnallocated ? 'font-style:italic; color:#666;' : '';
    if ($isNewGroup && $i > 0) {
        $rowStyle .= 'border-top:2px solid #e5e7eb;';
    }
    $styleAttr = $rowStyle !== '' ? ' style="' . $rowStyle . '"' : '';

    if ($isUnallocated || (int)$r['m_product_id'] <= 0) {
        $productCell = $h($r['label']);
    } else {
        $productUrl = '/search_dispatch.php?mode=product&q=' . rawurlencode($r['artnr']) . '&open=product&id=' . (int)$r['m_product_id'];
        $productCell = '<a href="' . $h($productUrl) . '" target="_blank" rel="noopener">' . $h($r['label']) . '</a>';
    }

    echo '<tr' . $styleAttr . '>';

    if ($isNewGroup) {
        if ($r['order_no'] === '') {
            $orderCell = '';
        } else {
            $orderUrl = '/search_dispatch.php?mode=order&page=1&q=' . rawurlencode($r['order_no']);
            $orderCell = '<a href="' . $h($orderUrl) . '" target="_blank" rel="noopener">' . $h($r['order_no']) . '</a>';
        }
        $rowspan = $groupSize[$groupOf[$i]];
        echo '<td' . ($rowspan > 1 ? ' rowspan="' . $rowspan . '"' : '') . ' style="vertical-align:top;">' . $orderCell . '</td>';
    }

    echo '<td>' . $productCell . '</td>'
        . '<td class="align_center">' . ($isUnallocated ? '&ndash;' : (int)$r['antal']) . '</td>'
        . '<td class="align_right">' . number_format($r['summa'], 0, ',', ' ') . ' SEK</td>'
        . '</tr>';
}
echo '</tbody>';
echo '<tfoot><tr style="font-weight:bold;">'
    . '<td colspan="2">Totalt</td>'
    . '<td class="align_center">' . $totalAntal . '</td>'
    . '<td class="align_right">' . number_format($totalSumma, 0, ',', ' ') . ' SEK</td>'
    . '</tr></tfoot>';
echo '</table>';

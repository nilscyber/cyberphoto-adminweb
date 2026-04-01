<?php
// /cronjob/cron_mail_used_products.php
// Skickar sammanfattning av begagnade produkter aldre an 90 dagar.

include_once("../top.php");

$from          = 'no-reply@cyberphoto.se';
$recipientsTO  = array('stefan@cyberphoto.se');
$to            = implode(', ', $recipientsTO);
$recipientsBCC = array('borje@cyberphoto.se','albin@cyberphoto.se','albin.soderlind@cyberphoto.se','andreas.almquist@cyberphoto.se');
$bcc           = implode(', ', $recipientsBCC);

$rows = $tool->getOldProducts('used', 90);

if (empty($rows)) { echo "OK (no rows)\n"; exit; }

$toUtf8 = function($s) {
    if ($s === null) return '';
    return (string)$s;
};
$h = function($s) use ($toUtf8) {
    return htmlspecialchars($toUtf8($s), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$fmt = function($n) {
    $s = (string)(int)round((float)$n);
    $out = ''; $len = strlen($s);
    for ($i = 0; $i < $len; $i++) {
        if ($i > 0 && ($len - $i) % 3 === 0) $out .= '&#160;';
        $out .= $s[$i];
    }
    return $out;
};

$sumNetto = 0.0;
foreach ($rows as $r) { $sumNetto += (float)$r['net_price']; }
$total = count($rows);

$dtStr       = date('Y-m-d');
$subjectText = "Begagnade produkter att \xc3\xa5tg\xc3\xa4rda - {$total} st ({$dtStr})";
$subject     = '=?UTF-8?B?' . base64_encode($subjectText) . '?=';

$html  = '<!doctype html><html><head><meta charset="UTF-8"></head>';
$html .= '<body style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#111;">';
$html .= '<p>Begagnade produkter (inbyten) med lager &gt; 0 vars s&auml;ljstart passerades f&ouml;r mer &auml;n <strong>90 dagar</strong> sedan.</p>';
$html .= '<p><strong>' . $total . ' st</strong> produkter kr&auml;ver &aring;tg&auml;rd.</p>';
$html .= '<table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;border:1px solid #ccc;">';
$html .= '<tr style="background:#d1f2f0;font-weight:bold;">';
$html .= '<th align="left">Artnr</th>';
$html .= '<th align="left">Produkt</th>';
$html .= '<th align="left">S&auml;ljstart</th>';
$html .= '<th align="right">&Aring;lder (d)</th>';
$html .= '<th align="right">Netto (kr)</th>';
$html .= '</tr>';

foreach ($rows as $r) {
    $artnr    = (string)$r['article'];
    $pid      = (int)$r['m_product_id'];
    $ageDays  = (int)$r['age_days'];
    $netPrice = (float)$r['net_price'];
    if ($ageDays > 365)     { $ageBg = '#fecaca'; }
    elseif ($ageDays > 180) { $ageBg = '#ffe4e6'; }
    else                    { $ageBg = '#fef3c7'; }
    $adminUrl = 'https://admin.cyberphoto.se/search_dispatch.php?q=' . rawurlencode($artnr)
              . '&open=product&id=' . $pid . '&mode=product';
    $html .= '<tr>';
    $html .= '<td>' . $h($artnr) . '</td>';
    $html .= '<td><a href="' . $h($adminUrl) . '">' . $h($toUtf8($r['product_full'])) . '</a></td>';
    $html .= '<td>' . $h($r['salestart']) . '</td>';
    $html .= '<td align="right" style="background:' . $ageBg . ';font-weight:bold;">' . $ageDays . '</td>';
    $html .= '<td align="right">' . $fmt($netPrice) . '</td>';
    $html .= '</tr>';
}

$html .= '<tr style="background:#f2f2f2;font-weight:bold;">';
$html .= '<td colspan="4" align="right">Summa netto</td>';
$html .= '<td align="right">' . $fmt($sumNetto) . '</td>';
$html .= '</tr>';
$html .= '</table>';
$html .= '<p style="margin-top:12px;"><a href="https://admin.cyberphoto.se/used_products.php">Visa i admin</a></p>';
$html .= '</body></html>';

$boundary = '=_CP_USED_' . md5(uniqid((string)mt_rand(), true));
$headers   = array();
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'From: ' . $from;
if (!empty($bcc)) { $headers[] = 'Bcc: ' . $bcc; }
$headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';
$htmlQP    = quoted_printable_encode($html);
$body  = "--{$boundary}\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
$body .= $htmlQP . "\r\n\r\n";
$body .= "--{$boundary}--\r\n";

$ok = SmtpMail::send($to, $subject, $body, implode("\r\n", $headers));
echo $ok ? "OK\n" : "FAILED\n";

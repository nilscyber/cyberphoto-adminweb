<?php
// /cronjob/cron_mail_missing_supplier_demo.php

include_once("../top.php");

// Inställningar
$warehouseId = 1000000;
$missingSupplierCode = '1004141'; // "Saknar leverantör" enligt din query

$from = 'no-reply@cyberphoto.se';

$to = 'service@cyberphoto.se';

$recipients = array(
  'stefan@cyberphoto.se',
  'karoline.juliusson@cyberphoto.se'
);
// Bygg BCC-strängen
$bcc = implode(', ', $recipients);

$rows = $sales->getDemoProductsMissingSupplier($missingSupplierCode, $warehouseId);

if (empty($rows)) {
    echo "OK (no rows)\n";
    exit;
}

// Datum + tid (utan sekunder)
$dtStr = date('Y-m-d H:i');

$subjectText = "Demo-produkter utan aktuell leverantör ({$dtStr})";

$toUtf8 = function($s){
    if ($s === null) return '';
    return (string)$s;
};

$h = function($s) use ($toUtf8){
    $s = $toUtf8($s);
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$subject = '=?UTF-8?B?' . base64_encode($subjectText) . '?=';

// HTML
$html  = '<!doctype html><html><head><meta charset="UTF-8"></head><body style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color:#111;">';
$html .= '<p>F&ouml;ljande DEMO-produkter har <b>Saknar leverant&ouml;r</b> som aktuell leverant&ouml;r och m&aring;ste &aring;tg&auml;rdas.</p>';
$html .= '<p>R&auml;tt leverant&ouml;r &auml;r: <b>4444 (CyberPhoto)</b></p>';

$html .= '<table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse; border:1px solid #ccc;">';
$html .= '<tr style="background:#f2f2f2; font-weight:bold;">';
$html .= '<th align="left">Artnr</th><th align="left">Beskrivning</th><th align="left">Leverant&ouml;r</th>';
$html .= '</tr>';

foreach ($rows as $r) {
    $artnr = (string)$r['artnr'];
    $pid   = (int)$r['m_product_id'];


    // Länk på beskrivning (admin drawer)
    $adminUrl = 'https://admin.cyberphoto.se/search_dispatch.php?mode=product&q='
              . rawurlencode($artnr)
              . '&open=product&id=' . $pid . '#';

    $descLabel = trim((string)$r['manufacturer'] . ' ' . (string)$r['description']);

	$artOut  = $h($artnr);
	$descOut = $h($descLabel);
	$supOut  = $h((string)$r['supplier_name']);
	$urlOut  = $h($adminUrl);

	$html .= '<tr>';
	$html .= '<td>' . $artOut . '</td>';
	$html .= '<td><a href="' . $urlOut . '">' . $descOut . '</a></td>';
	$html .= '<td>' . $supOut . '</td>';
	$html .= '</tr>';

}

$html .= '</table>';
$html .= '</body></html>';

// Mail headers
$boundary = "=_CP_MISS_SUP_DEMO_" . md5(uniqid((string)mt_rand(), true));

$headers = array();
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'From: ' . $from;
$headers[] = 'Bcc: ' . $bcc;
$headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

// HTML quoted-printable
$htmlQP = quoted_printable_encode($html);

$body  = "--{$boundary}\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
$body .= $htmlQP . "\r\n\r\n";
$body .= "--{$boundary}--\r\n";

$ok = SmtpMail::send($to, $subject, $body, implode("\r\n", $headers));
echo $ok ? "OK\n" : "FAILED\n";

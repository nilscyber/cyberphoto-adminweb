<?php
// /cronjob/cron_mail_new_products.php

include_once("../top.php");

// Inställningar
$hoursBack = 24;

$from = 'no-reply@cyberphoto.se';

$recipientsTO = array(
  'emil.lindberg@cyberphoto.se',
  'jennie.hedstrom@cyberphoto.se',
  'malin@cyberphoto.se',
  'boyd@cyberphoto.se'
);
$to = implode(', ', $recipientsTO);

$recipientsBCC = array(
  'stefan@cyberphoto.se'
);
$bcc = implode(', ', $recipientsBCC);

$rows = $sales->getNewProductsForMail($hoursBack);

if (empty($rows)) {
    echo "OK (no rows)\n";
    exit;
}

$toUtf8 = function($s){
    if ($s === null) return '';
    return (string)$s;
};

$h = function($s) use ($toUtf8){
    $s = $toUtf8($s);
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

// Datum för subject
$dtStr = date('Y-m-d H:i');

// Subject (RFC2047, alltid UTF-8)
$subjectText = "Nya produkter lanserade senaste {$hoursBack}h ({$dtStr})";
$subjectText = $toUtf8($subjectText);
$subject = '=?UTF-8?B?' . base64_encode($subjectText) . '?=';

// HTML
$html  = '<!doctype html><html><head><meta charset="UTF-8"></head>';
$html .= '<body style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color:#111;">';
$html .= '<p>Nya produkter som lanserats senaste ' . (int)$hoursBack . ' timmar.</p>';

$html .= '<table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse; border:1px solid #ccc;">';
$html .= '<tr style="background:#f2f2f2; font-weight:bold;">';
$html .= '<th align="left">Datum</th>';
$html .= '<th align="left">Artnr</th>';
$html .= '<th align="left">Produkt</th>';
$html .= '<th align="left">Leverant&ouml;r</th>';
$html .= '<th align="left">Ink&ouml;pare</th>';
$html .= '<th align="right">Min</th>';
$html .= '<th align="right">Max</th>';
$html .= '</tr>';

foreach ($rows as $r) {
    $artnrRaw = (string)$r['artnr'];
    $pid      = (int)$r['m_product_id'];

    // Länk till admin drawer_details (ny sida)
    $adminUrl = 'https://admin.cyberphoto.se/search_dispatch.php?q=' . rawurlencode($artnrRaw)
              . '&open=product&id=' . $pid . '#';

    $date = $h((string)$r['launch_date']);
    $art  = $h($artnrRaw);

    // Produktlabel
    $mfRaw   = (string)$r['manufacturer'];
    $descRaw = (string)$r['description'];
    $prodLabel = trim($mfRaw . ' ' . $descRaw);

    $prodOut = $h($prodLabel);
    $supOut  = $h((string)$r['supplier']);
    $buyOut  = $h((string)$r['buyer']);
    $urlOut  = $h($adminUrl);

    $min = (int)$r['min_stock'];
    $max = (int)$r['max_stock'];

    $html .= '<tr>';
    $html .= '<td>' . $date . '</td>';
    $html .= '<td>' . $art  . '</td>';
    $html .= '<td><a href="' . $urlOut . '">' . $prodOut . '</a></td>';
    $html .= '<td>' . $supOut . '</td>';
    $html .= '<td>' . $buyOut . '</td>';
    $html .= '<td align="right">' . $min . '</td>';
    $html .= '<td align="right">' . $max . '</td>';
    $html .= '</tr>';
}

$html .= '</table>';
$html .= '<p style="margin-top:12px;"><a href="https://admin.cyberphoto.se/new_products.php">Visa historik i admin</a></p>';
$html .= '</body></html>';

// CSV-bilaga (Latin-1, Excel-stabilt)
$csvFilename = 'nya_produkter_' . date('YmdHi') . '.csv';
$csvLatin1   = $sales->buildNewProductsCsvLatin1String($rows);

// Multipart mail
$boundary = "=_CP_NEWPROD_" . md5(uniqid((string)mt_rand(), true));

$headers = array();
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'From: ' . $from;
if (!empty($bcc)) {
  $headers[] = 'Bcc: ' . $bcc;
}
$headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

// HTML quoted-printable
$htmlQP = quoted_printable_encode($html);

$body  = "--{$boundary}\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
$body .= $htmlQP . "\r\n\r\n";

// Attachment
$body .= "--{$boundary}\r\n";
$body .= "Content-Type: text/csv; name=\"{$csvFilename}\"; charset=ISO-8859-1\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
$body .= "Content-Disposition: attachment; filename=\"{$csvFilename}\"\r\n\r\n";
$body .= chunk_split(base64_encode($csvLatin1)) . "\r\n";
$body .= "--{$boundary}--\r\n";

$ok = mail($to, $subject, $body, implode("\r\n", $headers));
echo $ok ? "OK\n" : "FAILED\n";

<?php
// /cronjob/cron_mail_focus_products.php

include_once("../top.php"); // cronjob ligger i /cronjob

// Inställningar
$days = 30;

$recipients = array(
  'stefan@cyberphoto.se',
  'emil.lindberg@cyberphoto.se',
  'jonas@cyberphoto.se',
  'victoria@cyberphoto.se',
  'boyd@cyberphoto.se',
  'thomas@cyberphoto.se'
);
$to = implode(', ', $recipients);
$cc = ''; // valfritt

// $to = 'stefan@cyberphoto.se'; // tillfällig override under testning

$rows = $sales->getFocusProductsSimple($days);

// Skippa mail om tom lista
if (empty($rows)) {
    echo "OK (no rows)\n";
    exit;
}

$dateStr = date('Y-m-d');
$weekStr = date('W');
$subject = "Fokusprodukter vecka {$weekStr} ({$dateStr})";

function to_utf8($s)
{
    return (string)$s;
}

// HTML-body (ASCII-safe via entiteter där vi har ÅÄÖ i statisk text)
$html  = '<!doctype html><html><head><meta charset="UTF-8"></head><body style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color:#111;">';
$html .= '<p>H&auml;r &auml;r aktuella fokusprodukter. S&aring;lda senaste ' . (int)$days . ' dagar.</p>';

$html .= '<table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse; border:1px solid #ccc;">';
$html .= '<tr style="background:#f2f2f2; font-weight:bold;">';
$html .= '<th align="left">Artnr</th><th align="left">Produkt</th><th align="left">Kategori</th><th align="right">Lagersaldo</th><th align="right">S&aring;lda 30d</th>';
$html .= '</tr>';

foreach ($rows as $r) {
    $artnrRaw = (string)$r['artnr'];
    $prodRaw  = (string)$r['product_label'];
    $catRaw   = (string)$r['category_name'];

    // Länka produkten i mailet till publika söken
    $url = 'https://www.cyberphoto.se/sok?q=' . rawurlencode($artnrRaw);

    $artnr = htmlspecialchars($artnrRaw, ENT_QUOTES, 'UTF-8');
    $prod  = htmlspecialchars(to_utf8($prodRaw), ENT_QUOTES, 'UTF-8');
    $cat   = htmlspecialchars(to_utf8($catRaw), ENT_QUOTES, 'UTF-8');

    $html .= '<tr>';
    $html .= '<td>' . $artnr . '</td>';
    $html .= '<td><a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . $prod . '</a></td>';
    $html .= '<td>' . $cat . '</td>';
    $html .= '<td align="right">' . (int)$r['onhand_qty'] . '</td>';
    $html .= '<td align="right">' . (int)$r['sold_30d'] . '</td>';
    $html .= '</tr>';
}

$html .= '</table>';
$html .= '<p style="margin-top:12px;"><a href="https://admin.cyberphoto.se/focus_products.php">&Ouml;ppna listan</a></p>';
$html .= '</body></html>';

function to_latin1($s)
{
    $s = (string)$s;

    // Om strängen är UTF-8: gör om till ISO-8859-1
    if (preg_match('//u', $s)) {
        return $s;
    }

    // Annars antar vi att den redan är "single byte" (latin1/ansi)
    return $s;
}

$csvFilename = 'fokusprodukter_' . date('YmdHi') . '.csv';

// Ingen BOM. Excel kommer ändå tolka som ANSI -> vi matchar det.
$csv  = "sep=;\r\n";
$csv .= "Artnr;Produkt;Kategori;Lagersaldo;Solda 30d\r\n";

foreach ($rows as $r) {
    $artnr = (string)$r['artnr'];
    $prod  = to_latin1((string)$r['product_label']);
    $cat   = to_latin1((string)$r['category_name']);

    $prod = str_replace(array("\r", "\n"), ' ', $prod);
    $cat  = str_replace(array("\r", "\n"), ' ', $cat);

    $csv .= $artnr . ';' . $prod . ';' . $cat . ';' . (int)$r['onhand_qty'] . ';' . (int)$r['sold_30d'] . "\r\n";
}


// --- Bygg multipart mail
$boundary = "=_CP_FOCUS_" . md5(uniqid((string)mt_rand(), true));
$from = 'no-reply@cyberphoto.se';

$headers = array();
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'From: ' . $from;
if ($cc !== '') { $headers[] = 'Cc: ' . $cc; }
$headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

// HTML som quoted-printable (stabilt för ÅÄÖ)
$htmlQP = quoted_printable_encode($html);

$body  = "--{$boundary}\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
$body .= $htmlQP . "\r\n\r\n";

// Attachment som base64
$body .= "--{$boundary}\r\n";
$body .= "Content-Type: text/csv; name=\"{$csvFilename}\"; charset=UTF-8\r\n";
// $body .= "Content-Type: text/csv; name=\"{$csvFilename}\"; charset=ISO-8859-1\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
$body .= "Content-Disposition: attachment; filename=\"{$csvFilename}\"\r\n\r\n";
$body .= chunk_split(base64_encode($csv)) . "\r\n";
$body .= "--{$boundary}--\r\n";

// Skicka
$ok = SmtpMail::send($to, $subject, $body, implode("\r\n", $headers));

echo $ok ? "OK\n" : "FAILED\n";

<?php
/**
 * Quick SMTP mail test — delete after verifying.
 * Access directly: https://admin.cyberphoto.se/test_mail.php
 */

require_once(__DIR__ . '/../lib/SmtpMail.php');

$to      = 'nils@kohlstrom.com';
$subject = 'SMTP test from adminweb ' . date('Y-m-d H:i:s');
$body    = "If you see this, SmtpMail::send() via SMTP is working.\n\nHost: " . (getenv('SMTP_HOST') ?: 'smtp.cyberphoto.se') . "\nPort: " . (getenv('SMTP_PORT') ?: 25);
$headers = 'From: nils@cyberphoto.se';

$result = SmtpMail::send($to, $subject, $body, $headers);

header('Content-Type: text/plain; charset=utf-8');
if ($result) {
    echo "OK — mail sent to $to\n";
} else {
    echo "FAILED — check error log for details\n";
}

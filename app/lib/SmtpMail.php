<?php
/**
 * Drop-in replacement for PHP's mail() that sends via SMTP sockets.
 * No external dependencies — works on PHP 8.x out of the box.
 * Autoloaded via spl_autoload_register in top.php.
 *
 * Configure via environment variables:
 *   SMTP_HOST  (default: smtp.cyberphoto.se)
 *   SMTP_PORT  (default: 25)
 *   SMTP_USER  (default: empty, no auth)
 *   SMTP_PASS  (default: empty, no auth)
 *
 * Usage: SmtpMail::send($to, $subject, $message, $headers)
 * Supports: HTML, multipart (attachments), BCC, CC, multiple recipients
 */
class SmtpMail {

    /**
     * Send email via SMTP.
     *
     * @param string $to       Recipient(s), comma-separated
     * @param string $subject  Email subject (may be MIME-encoded)
     * @param string $message  Email body (plain text, HTML, or pre-built multipart)
     * @param string $headers  Additional headers: From, Cc, Bcc, Content-Type, etc.
     * @return bool            True on success, false on failure
     */
    public static function send(string $to, string $subject, string $message, string $headers = ''): bool {
        $host = getenv('SMTP_HOST') ?: 'smtp.cyberphoto.se';
        $port = (int)(getenv('SMTP_PORT') ?: 25);

        // Parse From
        $fromEmail = 'no-reply@cyberphoto.se';
        $fromName  = '';
        if (preg_match('/^From:\s*(.+)$/im', $headers, $m)) {
            $fromValue = trim($m[1]);
            if (preg_match('/^(.+?)\s*<(.+?)>$/', $fromValue, $parts)) {
                $fromName  = trim($parts[1]);
                $fromEmail = trim($parts[2]);
            } else {
                $fromEmail = $fromValue;
            }
        }

        // Collect all envelope recipients (To + Cc + Bcc)
        $allRecipients = self::parseAddresses($to);
        if (preg_match('/^Cc:\s*(.+)$/im', $headers, $m)) {
            $allRecipients = array_merge($allRecipients, self::parseAddresses(trim($m[1])));
        }
        if (preg_match('/^Bcc:\s*(.+)$/im', $headers, $m)) {
            $allRecipients = array_merge($allRecipients, self::parseAddresses(trim($m[1])));
        }

        // Parse Content-Type from headers (if caller has pre-built MIME body)
        $contentType = '';
        if (preg_match('/^Content-Type:\s*(.+)$/im', $headers, $m)) {
            $contentType = trim($m[1]);
        }

        // SMTP connection
        $errno  = 0;
        $errstr = '';
        $smtp = @fsockopen($host, $port, $errno, $errstr, 10);
        if (!$smtp) {
            error_log("SmtpMail: could not connect to $host:$port — $errstr ($errno)");
            return false;
        }

        $response = function () use ($smtp): string {
            $data = '';
            while ($line = fgets($smtp, 512)) {
                $data .= $line;
                if (isset($line[3]) && $line[3] === ' ') break;
            }
            return $data;
        };

        $send = function (string $cmd) use ($smtp, $response): string {
            fwrite($smtp, $cmd . "\r\n");
            return $response();
        };

        // Greeting
        $response();

        // EHLO/HELO
        $ehloHost = gethostname() ?: 'localhost';
        $reply = $send("EHLO $ehloHost");
        if (str_starts_with($reply, '5')) {
            $send("HELO $ehloHost");
        }

        // AUTH if configured
        $smtpUser = getenv('SMTP_USER');
        if ($smtpUser) {
            $send("AUTH LOGIN");
            $send(base64_encode($smtpUser));
            $authReply = $send(base64_encode(getenv('SMTP_PASS') ?: ''));
            if (!str_starts_with($authReply, '235')) {
                error_log("SmtpMail: AUTH failed — $authReply");
                fclose($smtp);
                return false;
            }
        }

        // Envelope
        $reply = $send("MAIL FROM:<$fromEmail>");
        if (!str_starts_with($reply, '250')) {
            error_log("SmtpMail: MAIL FROM rejected — $reply");
            fclose($smtp);
            return false;
        }

        foreach ($allRecipients as $rcpt) {
            $reply = $send("RCPT TO:<$rcpt>");
            if (!str_starts_with($reply, '250')) {
                error_log("SmtpMail: RCPT TO <$rcpt> rejected — $reply");
                fclose($smtp);
                return false;
            }
        }

        $reply = $send("DATA");
        if (!str_starts_with($reply, '354')) {
            error_log("SmtpMail: DATA rejected — $reply");
            fclose($smtp);
            return false;
        }

        // Build DATA headers
        $fromHeader = $fromName ? "$fromName <$fromEmail>" : $fromEmail;
        $msg  = "Date: " . date('r') . "\r\n";
        $msg .= "From: $fromHeader\r\n";
        $msg .= "To: $to\r\n";
        $msg .= "Subject: $subject\r\n";
        $msg .= "MIME-Version: 1.0\r\n";

        if ($contentType !== '') {
            // Caller has pre-built MIME body — use their Content-Type and pass body as-is
            $msg .= "Content-Type: $contentType\r\n";
            $msg .= "\r\n";
            $msg .= self::dotStuff($message);
        } else {
            // Default: plain text
            $msg .= "Content-Type: text/plain; charset=utf-8\r\n";
            $msg .= "\r\n";
            $msg .= self::dotStuff($message);
        }
        $msg .= "\r\n.";

        $reply = $send($msg);
        if (!str_starts_with($reply, '250')) {
            error_log("SmtpMail: message rejected — $reply");
            fclose($smtp);
            return false;
        }

        $send("QUIT");
        fclose($smtp);
        return true;
    }

    private static function parseAddresses(string $addr): array {
        $result = [];
        foreach (array_map('trim', explode(',', $addr)) as $p) {
            if ($p === '') continue;
            if (preg_match('/<(.+?)>/', $p, $m)) {
                $result[] = trim($m[1]);
            } else {
                $result[] = $p;
            }
        }
        return $result;
    }

    private static function dotStuff(string $body): string {
        // Normalize to \n first (handles both \n and \r\n input), then convert to \r\n
        $body = str_replace("\r\n", "\n", $body);
        $body = str_replace("\r", "\n", $body);
        $body = str_replace("\n", "\r\n", $body);
        return str_replace("\r\n.", "\r\n..", $body);
    }
}

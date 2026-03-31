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
 */
class SmtpMail {

    /**
     * Send email via SMTP. Same signature as PHP's built-in mail().
     *
     * @param string $to           Recipient email address
     * @param string $subject      Email subject
     * @param string $message      Email body
     * @param string $headers      Additional headers (From, etc.)
     * @return bool                True on success, false on failure
     */
    public static function send(string $to, string $subject, string $message, string $headers = ''): bool {
        $host = getenv('SMTP_HOST') ?: 'smtp.cyberphoto.se';
        $port = (int)(getenv('SMTP_PORT') ?: 25);

        // Parse From header
        $fromEmail = 'no-reply@cyberphoto.se';
        $fromName  = '';
        if (preg_match('/From:\s*(.+?)(?:\r?\n|$)/i', $headers, $m)) {
            $fromValue = trim($m[1]);
            if (preg_match('/^(.+?)\s*<(.+?)>$/', $fromValue, $parts)) {
                $fromName  = trim($parts[1]);
                $fromEmail = trim($parts[2]);
            } else {
                $fromEmail = $fromValue;
            }
        }

        $to = trim($to);
        $errno = 0;
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

        // Read greeting
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

        $reply = $send("RCPT TO:<$to>");
        if (!str_starts_with($reply, '250')) {
            error_log("SmtpMail: RCPT TO rejected — $reply");
            fclose($smtp);
            return false;
        }

        $reply = $send("DATA");
        if (!str_starts_with($reply, '354')) {
            error_log("SmtpMail: DATA rejected — $reply");
            fclose($smtp);
            return false;
        }

        // Build message
        $date = date('r');
        $fromHeader = $fromName ? "$fromName <$fromEmail>" : $fromEmail;
        $msg  = "Date: $date\r\n";
        $msg .= "From: $fromHeader\r\n";
        $msg .= "To: $to\r\n";
        $msg .= "Subject: $subject\r\n";
        $msg .= "MIME-Version: 1.0\r\n";
        $msg .= "Content-Type: text/plain; charset=utf-8\r\n";
        $msg .= "\r\n";
        // Dot-stuff the body (lines starting with . get an extra .)
        $msg .= str_replace("\r\n.", "\r\n..", str_replace("\n", "\r\n", $message));
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
}

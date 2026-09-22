<?php

namespace App\Utils;

class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody, ?string $attachmentPath = null, ?string $attachmentName = null): bool
    {
        $user = getenv('MAIL_USERNAME');
        $pass = getenv('MAIL_PASSWORD');

        if ($attachmentPath && file_exists($attachmentPath)) {
            $boundary = 'b_' . md5(uniqid((string)mt_rand(), true));
            $fileName = $attachmentName ?: basename($attachmentPath);
            $mime = mime_content_type($attachmentPath) ?: 'application/octet-stream';
            $fileData = chunk_split(base64_encode(file_get_contents($attachmentPath)));
            $headers = "From: Automation Week IX <$user>\r\n"
                . "To: <$to>\r\n"
                . "Reply-To: $user\r\n"
                . "Subject: $subject\r\n"
                . "MIME-Version: 1.0\r\n"
                . "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";
            $body = "--$boundary\r\n"
                . "Content-Type: text/html; charset=UTF-8\r\n"
                . "Content-Transfer-Encoding: base64\r\n\r\n"
                . chunk_split(base64_encode($htmlBody)) . "\r\n"
                . "--$boundary\r\n"
                . "Content-Type: $mime; name=\"$fileName\"\r\n"
                . "Content-Disposition: attachment; filename=\"$fileName\"\r\n"
                . "Content-Transfer-Encoding: base64\r\n\r\n"
                . $fileData . "\r\n"
                . "--$boundary--";
            $message = $headers . $body;
        } else {
            $headers = "From: Automation Week IX <$user>\r\n"
                . "To: <$to>\r\n"
                . "Reply-To: $user\r\n"
                . "Subject: $subject\r\n"
                . "MIME-Version: 1.0\r\n"
                . "Content-Type: text/html; charset=UTF-8\r\n"
                . "Content-Transfer-Encoding: base64\r\n\r\n";
            $message = $headers . chunk_split(base64_encode($htmlBody));
        }

        $fp = stream_socket_client('tls://smtp.gmail.com:465', $errno, $errstr, 10);
        if (!$fp) {
            error_log("Mailer connect failed: $errstr ($errno)");
            return false;
        }
        $read = fn(): string => fgets($fp, 4096) ?: '';
        $readResponse = function () use ($fp, $read): string {
            $resp = $read();
            while (isset($resp[3]) && $resp[3] === '-') $resp = $read();
            return $resp;
        };
        $cmd = function (string $cmd, string $expect) use ($fp, $readResponse): bool {
            fwrite($fp, $cmd . "\r\n");
            $resp = $readResponse();
            if (str_starts_with($resp, $expect)) return true;
            error_log("Mailer [$cmd] got: " . trim($resp));
            return false;
        };
        $ok = $read()
            && $cmd('EHLO automationweek.local', '250')
            && $cmd('AUTH LOGIN', '334')
            && $cmd(base64_encode($user), '334')
            && $cmd(base64_encode($pass), '235')
            && $cmd("MAIL FROM:<$user>", '250')
            && $cmd("RCPT TO:<$to>", '250')
            && $cmd('DATA', '354')
            && (static function () use ($fp, $message, $read): bool {
                fwrite($fp, $message . "\r\n.\r\n");
                return str_starts_with($read(), '250');
            })();
        fwrite($fp, "QUIT\r\n");
        fclose($fp);
        return $ok;
    }
}

if (PHP_SAPI === 'cli' && ($_SERVER['argv'][1] ?? '') === '--check') {
    foreach ((array) @parse_ini_file(dirname(__DIR__, 2) . '/.env') as $key => $value) {
        putenv("$key=$value");
    }
    $ok = Mailer::send(getenv('MAIL_USERNAME'), 'Mailer check', '<p>Mailer works.</p>');
    echo $ok ? "OK - test mail sent\n" : "FAILED\n";
    exit($ok ? 0 : 1);
}

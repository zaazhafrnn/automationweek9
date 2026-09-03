<?php

namespace App\Components;

class EmailTemplates
{
    public static function welcome(string $name, string $loginUrl): string
    {
        return '<p>Halo ' . htmlspecialchars($name) . ',</p>'
            . '<p>Selamat! Akun AutomationWeek IX Anda berhasil dibuat.</p>'
            . '<p>Anda sekarang dapat masuk dan melanjutkan registrasi menggunakan email dan password yang terdaftar.</p>'
            . self::button($loginUrl, 'Masuk');
    }

    public static function resetPassword(string $name, string $resetLink): string
    {
        return '<p>Halo ' . htmlspecialchars($name) . ',</p>'
            . '<p>Kami menerima permintaan untuk mereset password akun AutomationWeek IX Anda. Jika Anda tidak melakukan permintaan ini, harap abaikan email ini.</p>'
            . '<p>Jika Anda yang meminta, silakan klik tombol di bawah ini.</p>'
            . self::button($resetLink, 'Reset Password')
            . '<p>atau klik link di bawah ini:</p>'
            . '<p><a href="' . htmlspecialchars($resetLink) . '" style="color:#b91c1c;">' . htmlspecialchars($resetLink) . '</a></p>'
            . '<p style="color:#6b7280;font-size:13px;">Permintaan ini valid selama 1 jam.</p>';
    }

    private static function button(string $url, string $label): string
    {
        return '<p style="margin:24px 0;text-align:center;">'
            . '<a href="' . htmlspecialchars($url) . '" style="display:inline-block;padding:12px 24px;background:#b91c1c;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">' . htmlspecialchars($label) . '</a>'
            . '</p>';
    }
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === '--check') {
    $reset = EmailTemplates::resetPassword('Dewi <script>', 'https://example.com/reset?token=abc');
    assert(str_contains($reset, '&lt;script&gt;'), 'name must be escaped');
    assert(str_contains($reset, 'Permintaan ini valid selama 1 jam'), 'expiry note present');
    assert(str_contains($reset, 'tombol di bawah ini'), 'button intro present');
    assert(str_contains($reset, 'href="https://example.com/reset?token=abc"'), 'reset link present');
    $welcome = EmailTemplates::welcome('Budi', 'https://example.com/login');
    assert(str_contains($welcome, 'Akun AutomationWeek IX'), 'welcome copy present');
    assert(str_contains($welcome, 'href="https://example.com/login"'), 'login link present');
    echo "OK\n";
}

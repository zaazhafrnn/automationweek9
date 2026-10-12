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

    public static function invoice(string $teamName, string $recipientName = '', ?string $waLink = null, ?string $dashboardUrl = null): string
    {
        $to = $recipientName !== '' ? $recipientName : $teamName;
        $html = '<p>Kepada, ' . htmlspecialchars($to) . '</p>'
            . '<p>Kami menginformasikan pembayaran untuk tim ' . htmlspecialchars($teamName) . ' telah berhasil terkonfirmasi oleh admin. Informasi detail mengenai invoice terlampir. Selamat bergabung di kompetisi AutomationWeek IX.</p>';
        if ($dashboardUrl) {
            $html .= '<p>Silahkan bergabung ke grup WhatsApp peserta yang tersedia di halaman dashboard anda: <a href="' . htmlspecialchars($dashboardUrl) . '" style="color:#b91c1c;">' . htmlspecialchars($dashboardUrl) . '</a></p>';
        }
        $html .= '<p>Demikian informasi dari kami mengenai invoice. Terima kasih sudah berpartisipasi dalam kompetisi AutomationWeek IX.</p>'
            . '<p>Semoga Sukses!</p>'
            . '<div style="margin:12px 0;padding:10px 10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;">'
            . '<p style="margin:0 0 6px;font-weight:bold;">Kontak Pembayaran & Bantuan:</p>'
            . '<p style="margin:4px 0;">Titis Nabila (085755184808)</p>'
            . '<p style="margin:4px 0;">Shafaatur R. (0895396129215)</p>'
            . '<p style="margin:4px 0;">Bantuan umum: Abel (+62 819-9828-2954)</p>'
            . '</div>';
        return $html;
    }

    public static function abstractResult(bool $passed, string $name, string $title, ?string $nextUrl = null): string
    {
        $mark = '<span style="background:#fde047;font-weight:bold;padding:0 6px;border-radius:4px;">LOLOS</span>';
        $html = '';
        if ($passed) {
            $html .= '<p>Hai, ' . htmlspecialchars($name) . '! 👋</p>'
                . '<p>Terima kasih telah menjadi bagian dari Automation Week IX.</p>'
                . '<p>Selamat🤩!!! Abstrak anda dengan judul <b>"' . htmlspecialchars($title) . '"</b> dinyatakan ' . $mark . ' pada tahap seleksi abstrak.✨</p>'
                . '<p>Silahkan melanjutkan ke tahap selanjutnya melalui tautan dibawah ini 👇</p>';
            if ($nextUrl) $html .= self::button($nextUrl, 'Lanjut ke Pembayaran');
            return $html;
        }
        return $html . '<p>Hai, ' . htmlspecialchars($name) . '! 👋</p>'
            . '<p>Terima kasih telah menjadi bagian dari Automation Week IX.</p>'
            . '<p>Abstrakmu yang berjudul <b>"' . htmlspecialchars($title) . '"</b> dinyatakan <b>tidak lolos</b> pada tahap seleksi abstrak kali ini.</p>'
            . '<p>Jangan berkecil hati, ya! Tetap semangat berkarya, kembangkan potensimu, dan terus ciptakan inovasi baru. ✨<br><br>Your Journey Doesn\'t End Here.🤗</p>';
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

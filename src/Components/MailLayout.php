<?php

namespace App\Components;

class MailLayout
{
    public static function render(string $body, string $logoUrl = ''): string
    {
        $logo = $logoUrl !== '' ? '<img src="' . htmlspecialchars($logoUrl) . '" alt="AutomationWeek" style="max-width:120px;height:auto;" />'
            : '<div style="font-size:20px;font-weight:bold;color:#b91c1c;">' . htmlspecialchars(APP_NAME) . '</div>';

        return '<div style="background:#f3f4f6;padding:24px 0;font-family:Arial,Helvetica,sans-serif;">'
            . '<div style="max-width:480px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;padding:32px;">'
            . '<div style="text-align:center;margin-bottom:24px;">' . $logo . '</div>'
            . $body
            . '<p style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e7eb;color:#9ca3af;font-size:12px;line-height:1.5;">'
            . 'Email ini dikirim otomatis oleh sistem ' . htmlspecialchars(APP_NAME) . '. Mohon tidak membalas email ini.</p>'
            . '</div></div>';
    }
}

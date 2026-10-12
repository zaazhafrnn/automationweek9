<?php

namespace App\Components;

class MailLayout
{
    public static function render(string $body, string $logoUrl = '', bool $showLogo = true, string $bannerHtml = ''): string
    {
        $logo = $logoUrl !== '' ? '<img src="' . htmlspecialchars($logoUrl) . '" alt="AutomationWeek" style="max-width:120px;height:auto;" />'
            : '<div style="font-size:20px;font-weight:bold;color:#b91c1c;">' . htmlspecialchars(APP_NAME) . '</div>';
        $hasBanner = $bannerHtml !== '';
        $banner = $hasBanner
            ? '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;width:100%;margin:0;padding:0;">'
            . '<tr><td height="48" style="height:48px;background-color:#ba1229;border-radius:0 0 40px 40px;padding:0;margin:0;line-height:0;font-size:0;">&nbsp;</td></tr>'
            . '</table>'
            : '';
        $cardRadius = '12px';
        $cardBorder = $hasBanner ? '0' : '1px solid #e5e7eb';
        $footer = '<p style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e7eb;color:#9ca3af;font-size:12px;line-height:1.5;">'
            . 'Email ini dikirim otomatis oleh sistem ' . htmlspecialchars(APP_NAME) . '. Mohon tidak membalas email ini.</p>';

        $outer = '<div style="background:#f3f4f6;padding:24px 0;font-family:Arial,Helvetica,sans-serif;">';
        $cardOpen = '<div style="max-width:480px;margin:0 auto;background:#ffffff;border:' . $cardBorder . ';border-radius:' . $cardRadius . ';overflow:hidden;">';

        if ($hasBanner) {
            $logoBlock = $showLogo
                ? '<div style="text-align:center;padding:32px 32px 0 32px;">' . $logo . '</div>'
                : '';
            $inner = $logoBlock . $banner . '<div style="padding:32px;">' . $body . $footer . '</div>';
            $cardClose = '</div>';
            return $outer . $cardOpen . $inner . $cardClose . '</div>';
        }

        $inner = ($showLogo ? '<div style="text-align:center;margin-bottom:0;">' . $logo . '</div>' : '')
            . $body
            . $footer;
        return $outer . $cardOpen . '<div style="padding:32px;">' . $inner . '</div></div></div>';
    }
}

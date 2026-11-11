<?php

namespace App\Components;

class MailLayout
{
    public static function render(string $body, string $logoUrl = '', bool $showLogo = true, string $bannerHtml = ''): string
    {
        $logo = $logoUrl !== '' ? '<img src="' . htmlspecialchars($logoUrl) . '" alt="AutomationWeek" style="max-width:120px;height:auto;" />'
            : '<div style="font-size:20px;font-weight:bold;color:#b91c1c;">' . htmlspecialchars(APP_NAME) . '</div>';
        $hasBanner = $bannerHtml !== '';
        $base = preg_replace('#/image/[^/]*$#', '', $logoUrl);
        $lkti = $base !== '' ? $base . '/image/lkti_icon.png' : '';
        $maskot = $base !== '' ? $base . '/image/maskot.png' : '';
        $logoBlock = $logo;
        $banner = $hasBanner
            ? '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;width:100%;margin:0;padding:0;">'
            . '<tr><td align="center" style="background-color:#ba1229;border-radius:0 0 52px 52px;padding:14px 16px;margin:0;color:#ffffff;font-family:Poppins,Arial,Helvetica,sans-serif;font-size:26px;font-weight:900;letter-spacing:2px;">' . htmlspecialchars($bannerHtml) . '</td></tr>'
            . '</table>'
            : '';
        $cardRadius = '12px';
        $cardBorder = $hasBanner ? '0' : '1px solid #e5e7eb';
        $footer = '<p style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e7eb;color:#9ca3af;font-size:12px;line-height:1.5;">'
            . 'Email ini dikirim otomatis oleh sistem ' . htmlspecialchars(APP_NAME) . '. Mohon tidak membalas email ini.</p>';

        $outer = '<div style="background:#f3f4f6;padding:24px 0;font-family:Arial,Helvetica,sans-serif;">';
        $cardOpen = '<div style="max-width:480px;margin:0 auto;background:#ffffff;border:' . $cardBorder . ';border-radius:' . $cardRadius . ';overflow:hidden;">';

        if ($hasBanner) {
            if ($showLogo) {
                $logoImgStyle = 'max-width:120px;height:auto;display:block;border:0;';
                $logoForRow = $logoUrl !== ''
                    ? '<img src="' . htmlspecialchars($logoUrl) . '" alt="AutomationWeek" style="' . $logoImgStyle . '" />'
                    : $logo;
                $logoCell = '<td valign="middle" style="padding:0;font-size:0;line-height:0;">' . $logoForRow . '</td>';
                $logoPairGap = '16px';
                $logoDivider = '<td valign="middle" style="padding:0 8px;font-size:0;line-height:0;width:1px;">'
                    . '<div style="width:1px;height:70px;background-color:#000000;margin:0 auto;font-size:0;line-height:0;">&#8203;</div>'
                    . '</td>';
                $pairRow = $lkti !== ''
                    ? '<td valign="middle" style="padding:0 ' . $logoPairGap . ' 0 0;font-size:0;line-height:0;">' . $logoForRow . '</td>'
                    . $logoDivider
                    . '<td valign="middle" style="padding:0 0 0 ' . $logoPairGap . ';font-size:0;line-height:0;">'
                    . '<img src="' . htmlspecialchars($lkti) . '" alt="" style="' . $logoImgStyle . '" />'
                    . '</td>'
                    : $logoCell;
                $logoBlock = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;width:100%;margin:0;padding:0;">'
                    . '<tr><td align="center" style="padding:0 32px;margin:0;">'
                    . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:collapse;margin:0 auto;">'
                    . '<tr>' . $pairRow . '</tr>'
                    . '</table></td></tr></table>';
            } else {
                $logoBlock = '';
            }

            $corners = $lkti !== '' && $maskot !== ''
                ? '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;width:100%;margin:16px 0 0;"><tr>'
                . '<td align="left" valign="bottom" style="padding:0;"><img src="' . htmlspecialchars($lkti) . '" alt="" style="max-width:92px;height:auto;display:block;" /></td>'
                . '<td></td>'
                . '<td align="right" valign="bottom" style="padding:0;"><img src="' . htmlspecialchars($maskot) . '" alt="" style="max-width:96px;height:auto;display:block;" /></td>'
                . '</tr></table>'
                : '';
            $inner = $logoBlock . $banner . '<div style="padding:32px;">' . $body . $corners . $footer . '</div>';
            $cardClose = '</div>';
            return $outer . $cardOpen . $inner . $cardClose . '</div>';
        }

        $inner = ($showLogo ? '<div style="text-align:center;margin-bottom:0;">' . $logo . '</div>' : '')
            . $body
            . $footer;
        return $outer . $cardOpen . '<div style="padding:32px;">' . $inner . '</div></div></div>';
    }
}

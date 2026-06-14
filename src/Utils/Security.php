<?php

namespace App\Utils;

class Security
{
    public static function generateCsrfToken()
    {
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function validateCsrfToken($token)
    {
        $sessionToken = Session::get('csrf_token');
        if (!$sessionToken || !$token) {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }

    public static function sanitizeString($string)
    {
        $string = trim($string);
        $string = stripslashes($string);
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

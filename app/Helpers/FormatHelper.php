<?php

namespace App\Helpers;

class FormatHelper
{
    public static function digits(?string $value): string
    {
        return preg_replace('/\D+/', '', $value ?? '') ?? '';
    }

    public static function cnpjCpf(?string $value): string
    {
        $v = self::digits($value);

        if (strlen($v) === 14) {
            return substr($v,0,2).'.'.substr($v,2,3).'.'.substr($v,5,3).'/'.substr($v,8,4).'-'.substr($v,12,2);
        }

        if (strlen($v) === 11) {
            return substr($v,0,3).'.'.substr($v,3,3).'.'.substr($v,6,3).'-'.substr($v,9,2);
        }

        return $value ?? '-';
    }

    public static function phone(?string $value): string
    {
        $v = self::digits($value);

        if (strlen($v) === 11) {
            return '('.substr($v,0,2).') '.substr($v,2,5).'-'.substr($v,7,4);
        }

        if (strlen($v) === 10) {
            return '('.substr($v,0,2).') '.substr($v,2,4).'-'.substr($v,6,4);
        }

        return $value ?? '-';
    }

    public static function cep(?string $value): string
    {
        $v = self::digits($value);
        return strlen($v) === 8 ? substr($v,0,5).'-'.substr($v,5,3) : ($value ?? '-');
    }
}
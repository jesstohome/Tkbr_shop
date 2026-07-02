<?php

namespace App\Helpers;

class Google2FA
{
    const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * 生成16位 base32 密钥
     */
    public static function generateSecret()
    {
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= self::ALPHABET[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * 获取当前6位TOTP验证码
     */
    public static function getCode($secret)
    {
        $timeSlice = floor(time() / 30);
        return self::oathHotp($secret, $timeSlice);
    }

    /**
     * 验证验证码（允许前后各1个窗口，即±30秒）
     */
    public static function verifyCode($secret, $code)
    {
        $timeSlice = floor(time() / 30);
        for ($i = -1; $i <= 1; $i++) {
            if (self::oathHotp($secret, $timeSlice + $i) === $code) {
                return true;
            }
        }
        return false;
    }

    /**
     * 生成 otpauth:// URL，用于扫码绑定
     */
    public static function getQRCodeUrl($accountName, $secret, $issuer = '')
    {
        $label = $accountName;
        $url = 'otpauth://totp/' . rawurlencode($label) . '?secret=' . $secret;
        if ($issuer) {
            $url .= '&issuer=' . rawurlencode($issuer);
        }
        return $url;
    }

    private static function oathHotp($secret, $counter)
    {
        $key = self::base32Decode($secret);
        $counter = pack('N*', 0) . pack('N*', $counter);
        $hash = hash_hmac('sha1', $counter, $key, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;
        $binary = (
            ((ord($hash[$offset + 0]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8)  |
            (ord($hash[$offset + 3]) & 0xFF)
        );
        return str_pad($binary % 1000000, 6, '0', STR_PAD_LEFT);
    }

    private static function base32Decode($input)
    {
        $input = strtoupper(rtrim($input, '='));
        $binary = '';
        foreach (str_split($input) as $char) {
            $val = strpos(self::ALPHABET, $char);
            if ($val === false) continue;
            $binary .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
        }
        $result = '';
        foreach (str_split($binary, 8) as $byte) {
            if (strlen($byte) < 8) break;
            $result .= chr(bindec($byte));
        }
        return $result;
    }
}

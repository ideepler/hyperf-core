<?php

namespace Ideepler\HyperfCore\Utils;

/**
 * 加密相关方法
 */
class CryptUtil
{
    /**
     * 数据加密
     * @param string $originStr 源字符串
     * @param string $key 秘钥
     * @return string chars 加密后的字符串
     */
    public static function encrypt(string $originStr, string $key): string
    {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($originStr, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * 数据解密
     * @param string $encryptStr 源字符串
     * @param string $key 秘钥
     * @return string chars 加密后的字符串
     */
    public static function decrypt(string $encryptStr, string $key): string
    {
        $decodedStr = base64_decode($encryptStr);
        $iv = substr($decodedStr, 0, 16);
        $decodedStrTrimIv = substr($decodedStr, 16);
        return openssl_decrypt($decodedStrTrimIv, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }
}
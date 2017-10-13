<?php

namespace common\helper;

class EncryptHelper
{
    /**
     * aes加密算法
     * @param string $input
     * @param string $key
     * @return string
     */
    public static function aes_encrypt($input, $key)
    {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = EncryptHelper::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);

        return $data;
    }

    private static function pkcs5_pad($text, $blockSize)
    {
        $pad = $blockSize - (strlen($text) % $blockSize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * aes解密算法
     * @param string $sStr
     * @param string $sKey
     * @return string
     */
    public static function aes_decrypt($sStr, $sKey)
    {
        if(base64_encode(base64_decode($sStr)) != $sStr)
            return $sStr;
        
        $decrypted = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $sKey,
            base64_decode($sStr),
            MCRYPT_MODE_ECB
        );

        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted ? $decrypted : $sStr;
    }

    function des_encrypt($string, $key)
    {
        // 加密方法
        $cipher_alg = MCRYPT_TRIPLEDES;
        // 初始化向量来增加安全性
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg, MCRYPT_MODE_ECB), MCRYPT_RAND);

        // 开始加密
        $encrypted_string = mcrypt_encrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv);
        return base64_encode($encrypted_string); // 转化成16进制
        //return $encrypted_string;
    }

    /**
     * 解密
     * @param string $string 密文
     * @param string $key
     * @return string
     */
    function des_decrypt($string, $key)
    {
        $string = base64_decode($string);

        // 加密方法
        $cipher_alg = MCRYPT_TRIPLEDES;
        // 初始化向量来增加安全性
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg, MCRYPT_MODE_ECB), MCRYPT_RAND);

        // 开始解密
        $decrypted_string = mcrypt_decrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv);
        return trim($decrypted_string);
    }
}

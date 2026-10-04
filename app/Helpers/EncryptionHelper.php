<?php

namespace App\Helpers;

class EncryptionHelper
{
    public static function encrypt($plaintext)
    {
        $key = config('messages.aes_key');

        $iv = openssl_random_pseudo_bytes(16);

        $encrypted = openssl_encrypt(
            $plaintext,
            'AES-256-CBC',
            $key,
            0,
            $iv
        );

        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($ciphertext)
    {
        if (!is_string($ciphertext) || $ciphertext === '') {
            return $ciphertext;
        }

        $key = config('messages.aes_key');

        $data = base64_decode($ciphertext);

        if ($data === false || strlen($data) <= 16) {
            return $ciphertext;
        }

        $iv = substr($data, 0, 16);

        $encrypted = substr($data, 16);

        $decrypted = openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            $key,
            0,
            $iv
        );

        return $decrypted === false ? $ciphertext : $decrypted;
    }
}

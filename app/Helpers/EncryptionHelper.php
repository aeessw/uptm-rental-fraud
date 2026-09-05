<?php

namespace App\Helpers;

class EncryptionHelper
{
    public static function encrypt($plaintext)
    {
        $key = env('AES_KEY');

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
        $key = env('AES_KEY');

        $data = base64_decode($ciphertext);

        $iv = substr($data, 0, 16);

        $encrypted = substr($data, 16);

        return openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            $key,
            0,
            $iv
        );
    }
}
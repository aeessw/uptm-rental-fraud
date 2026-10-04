<?php

namespace App\Helpers;

class EncryptionHelper
{
    public static function encrypt($plaintext)
    {
        $key = config('messages.aes_key');

        if (!is_string($key) || $key === '') {
            throw new \RuntimeException('Message encryption key is not configured.');
        }

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

        if ($decrypted !== false && mb_check_encoding($decrypted, 'UTF-8')) {
            return $decrypted;
        }

        // Earlier deployments read env() after configuration was cached and
        // encrypted messages with an empty key. Read those records only;
        // encrypt() always requires the configured key for new messages.
        $legacy = openssl_decrypt($encrypted, 'AES-256-CBC', '', 0, $iv);

        return $legacy !== false && mb_check_encoding($legacy, 'UTF-8')
            ? $legacy
            : 'This message could not be decrypted.';
    }
}

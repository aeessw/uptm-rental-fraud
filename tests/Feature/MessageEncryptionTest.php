<?php

namespace Tests\Feature;

use App\Helpers\EncryptionHelper;
use Tests\TestCase;

class MessageEncryptionTest extends TestCase
{
    public function test_legacy_empty_key_messages_remain_readable_without_changing_stored_data(): void
    {
        config(['messages.aes_key' => 'test-message-key-0123456789abcdef']);
        $iv = str_repeat('a', 16);
        $stored = base64_encode($iv.openssl_encrypt('Legacy conversation', 'AES-256-CBC', '', 0, $iv));

        $this->assertSame('Legacy conversation', EncryptionHelper::decrypt($stored));
    }

    public function test_new_messages_cannot_be_encrypted_without_a_key(): void
    {
        config(['messages.aes_key' => null]);
        $this->expectException(\RuntimeException::class);
        EncryptionHelper::encrypt('New conversation');
    }

    public function test_unknown_key_does_not_display_ciphertext(): void
    {
        config(['messages.aes_key' => 'test-message-key-0123456789abcdef']);
        $iv = str_repeat('b', 16);
        $stored = base64_encode($iv.openssl_encrypt('Other conversation', 'AES-256-CBC', 'different-key', 0, $iv));

        $this->assertSame('This message could not be decrypted.', EncryptionHelper::decrypt($stored));
    }

    public function test_existing_messages_decrypt_using_configuration_without_runtime_environment_lookup(): void
    {
        $key = 'test-message-key-0123456789abcdef';
        config(['messages.aes_key' => $key]);
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt('Existing conversation', 'AES-256-CBC', $key, 0, $iv);

        $this->assertSame('Existing conversation', EncryptionHelper::decrypt(base64_encode($iv.$encrypted)));

        $stored = base64_decode(EncryptionHelper::encrypt('New conversation'));
        $this->assertSame('New conversation', openssl_decrypt(substr($stored, 16), 'AES-256-CBC', $key, 0, substr($stored, 0, 16)));
    }
}

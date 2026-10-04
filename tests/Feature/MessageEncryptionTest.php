<?php

namespace Tests\Feature;

use App\Helpers\EncryptionHelper;
use Tests\TestCase;

class MessageEncryptionTest extends TestCase
{
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

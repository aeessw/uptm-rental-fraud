<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditLogger
{
    public static function log($userId, $action, $target = null)
    {
        $timestamp = now()->toDateTimeString();

        $data = $userId . $action . $target . $timestamp;

        $hmac = hash_hmac(
            'sha256',
            $data,
            env('HMAC_KEY')
        );

        return AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'target' => $target,
            'hmac' => $hmac,
        ]);
    }
}
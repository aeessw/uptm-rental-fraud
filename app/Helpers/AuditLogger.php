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
            config('audit.hmac_key')
        );

        $log = new AuditLog([
            'user_id' => $userId,
            'audit_action' => $action,
            'audit_target' => $target,
            'audit_hmac' => $hmac,
        ]);
        $log->audit_created_at = $timestamp;
        $log->audit_updated_at = $timestamp;
        $log->save();
        return $log;
    }
}
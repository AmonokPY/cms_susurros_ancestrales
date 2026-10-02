<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogger
{
    public static function record(string $action, string $description = '', string $result = 'ok', ?User $user = null): void
    {
        AuditLog::record($action, $description, $result, $user);
    }
}

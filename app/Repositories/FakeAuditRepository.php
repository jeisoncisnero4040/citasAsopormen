<?php 

namespace App\Repositories;

use App\Interfaces\AuditInterface;
use Illuminate\Support\Facades\Log;

class FakeAuditRepository implements AuditInterface
{
    public function saveAudit(string $audit, string $modulo,string $cedula):void
    {
        Log::info("Saving audit", [
            'audit' => $audit,
            'cedula' => $cedula,
            'modulo' => $modulo
        ]);
    }
}
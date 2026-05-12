<?php 

namespace App\Repositories;

use App\Dtos\GetAuditDto;
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
    public function getAudits(GetAuditDto $dto): array
    {
        return [
            [
                'id' => 1,
                'cedula' => '123456789',
                'modulo' => 'Test Module',
                'audit' => 'This is a test audit log.',
                'created_at' => now()->toDateTimeString(),
            ],
            [
                'id' => 2,
                'cedula' => '987654321',
                'modulo' => 'Another Module',
                'audit' => 'This is another test audit log.',
                'created_at' => now()->toDateTimeString(),
            ],
        ];
    }
}
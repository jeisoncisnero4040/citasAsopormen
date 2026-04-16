<?php 

namespace App\Interfaces;
use App\Dtos\GetAuditDto;

interface AuditInterface {
    public function saveAudit(string $audit, string $modulo,string $cedula):void;
    public function getAudits(GetAuditDto $dto):array;  
}
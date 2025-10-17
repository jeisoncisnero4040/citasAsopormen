<?php 

namespace App\Interfaces;

interface AuditInterface {
    public function saveAudit(string $audit,string $modulo):void;
}
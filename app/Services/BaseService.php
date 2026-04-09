<?php

namespace App\Services;

use App\Kafka\Domain\MessageQueue;
use App\Models\AuditMessageQueueBuilder;
use App\Models\UserRequesting;

class BaseService{
    protected QueueService $queueService;
    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }
    protected function dispatchToQueue(string $msm, UserRequesting $userRequesting): void
    {
        $messageQueue = $this->buildMsmAudit($msm, $userRequesting);
        $this->queueService->publish($messageQueue);
    }
    private function buildMsmAudit(string $action,UserRequesting $userRequesting,string $modulo = 'citas'):MessageQueue{
        return AuditMessageQueueBuilder::create()->withData([
            'audit'=>$action,
            'cedula'=>$userRequesting->getCedula(),
            'modulo'=>$modulo
        ])->build();
    }

    
    
}
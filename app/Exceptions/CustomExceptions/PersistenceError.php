<?php 

namespace App\Exceptions\CustomExceptions;

use App\Dtos\LogDto;
use App\Interfaces\Logable;
use App\Services\LogsService;
use Exception;

class PersistenceError extends Exception implements Logable
{
    private int $status;
    private string $action;
    private LogsService $logsService;

    public function __construct(
        string $message,
        string $action,
        LogsService $logsService,
        int $status = 500
    ) {
        parent::__construct($message);
        $this->status = $status;
        $this->action = $action;
        $this->logsService = $logsService;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function log(): void
    {
        $logDto = new LogDto(
            content: $this->message,
            action: $this->action
        );

        $this->logsService->saveLogs($logDto);
    }
}

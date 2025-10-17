<?php

namespace App\Services;

use App\Dtos\LogDto;
use App\Interfaces\LogsRepositoryInterface;
use Carbon\Carbon;

class LogsService{
    private LogsRepositoryInterface $logsRepository;

    public function __construct(LogsRepositoryInterface $logsRepository)
    {
        $this->logsRepository=$logsRepository;
    }

    public function saveLogs(LogDto $logDto){
        $logDto->setDate(Carbon::now()->format('Y-m-d H:i:s'));
        $this->logsRepository->save($logDto);
    }
}
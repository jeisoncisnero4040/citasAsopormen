<?php

namespace App\Repositories;

use App\Dtos\LogDto;
use App\Interfaces\LogsRepositoryInterface;

class LogsRepository extends BaseRepository implements LogsRepositoryInterface
{
    public function save(LogDto $log): void
    {
        $query = "INSERT INTO logs_clinico (error, fecha, lugar) VALUES (?, CONVERT(smalldatetime, ?, 120), ?)";
        
        self::sendQuery(
            query: $query,
            bindings: [
                $log->getContent(),
                $log->getDate(),
                $log->getAction()
            ],
            typeConsult: 'insert'
        );
    }
}

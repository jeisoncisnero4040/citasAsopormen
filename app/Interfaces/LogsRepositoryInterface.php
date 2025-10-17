<?php

namespace App\Interfaces;

use App\Dtos\LogDto;

interface LogsRepositoryInterface{
    public function save(LogDto $log):void;
}
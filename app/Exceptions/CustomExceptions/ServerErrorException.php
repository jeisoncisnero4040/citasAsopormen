<?php

namespace App\Exceptions\CustomExceptions;
use App\Exceptions\CustomExceptions\BadRequestException;
use Exception;

class ServerErrorException extends Exception {
    
    private $status;

    public function __construct(string $message, int $status=500)
    {
        parent::__construct($message);
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

}
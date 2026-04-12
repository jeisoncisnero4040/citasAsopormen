<?php

namespace App\Exceptions\CustomExceptions;
use Exception;

class QueueException extends Exception
{
    public function __construct(string $message = "An error occurred while processing the queue.", int $status = 0)
    {
        parent::__construct($message, $status);
    }
 
    public function getStatus(): int
    {
        return $this->code;
    }
}
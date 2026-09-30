<?php

namespace App\Exceptions\CustomExceptions;

use Exception;

class NotFoundException extends Exception
{
    private int  $status;

    public function __construct(string $message, int $status = 404)
    {
        parent::__construct($message);
        $this->status = $status;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}

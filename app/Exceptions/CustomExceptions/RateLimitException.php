<?php

namespace App\Exceptions\CustomExceptions;
use Exception;

class RateLimitException extends Exception {
    
    private $status;

    public function __construct($message="Error por rate limit", $status=429)
    {
        parent::__construct($message);
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

}
<?php
namespace App\Exceptions\CustomExceptions;

use Exception;

class BadRequestException extends Exception{
    private $status;

    public function __construct($message, $status)
    {
        parent::__construct($message);
        $this->status = $status;
    }
    public function getStatus()
    {   
        return $this->status;
    }
}

<?php
namespace App\Exceptions\CustomExceptions;

use Exception;
class QueuException extends Exception
{
    private $status;

    public function __construct($message, $status=500)
    {
        parent::__construct($message);
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
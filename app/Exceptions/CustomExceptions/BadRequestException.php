<?php
namespace App\Exceptions\CustomExceptions;
use App\Events\MessageSentEvent;
use Exception;

class BadRequestException extends Exception{
    private $status;
    private $statusChat;
    private $numCel;

    public function __construct($message, $status,$statusChat,$numCel)
    {
        parent::__construct($message);
        $this->status = $status;
        $this->statusChat=$statusChat;
        $this->numCel=$numCel;
    }
    public function getStatus()
    {   
        return $this->status;
    }
    public function SendMessageError(){
        event(new MessageSentEvent($this->numCel, $this->message, $this->statusChat));
        return;
    }
}

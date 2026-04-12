<?php

namespace App\Ports;

use App\Domain\Message;
use App\Domain\ExternalResponse;

interface ChannelMessagingPort
{
    public function sendMessage(Message $message): ExternalResponse;
}
<?php

namespace App\Services;

use App\Domain\WhatsappSenderProvider;
use App\Ports\ChannelMessagingPort;
use App\Domain\ExternalResponse;
use App\Domain\Message;


class MessagingService implements ChannelMessagingPort{

    private WhatsappSenderProvider $whatsappSenderProvider; 
    public function __construct(WhatsappSenderProvider $whatsappSenderProvider)
    {
        $this->whatsappSenderProvider = $whatsappSenderProvider;
    }
    public function sendMessage(Message $message): ExternalResponse
    {
        try {
            $result = $this->whatsappSenderProvider
                ->getClient()
                ->messages
                ->create(
                    $message->getReceiver(),
                    [
                        'from' => $message->getSender(),
                        'body' => $message->getContent()
                    ]
                );

            return ExternalResponse::fromSuccess(
                "Message sent successfully",
                [
                    'sid' => $result->sid ?? null,
                    'status' => $result->status ?? null,
                    'to' => $result->to ?? null
                ]
            );

        } catch (\Throwable $e) {
            return ExternalResponse::fromError(
                "Failed to send message",
                [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode()
                ]
            );
        }
    }
}
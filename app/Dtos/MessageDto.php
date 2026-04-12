<?php
namespace App\Dtos;

class MessageDto
{
    private string $content;
    private string $sender;
    private string $mediaUrl;   
    private string $messageType;
    private string $sid;
    private string $receiver;    

    public function __construct(string $content, string $sender, string $mediaUrl, string $messageType, string $sid, string $receiver)
    {
        $this->content = $content;
        $this->sender = $sender;
        $this->mediaUrl = $mediaUrl;
        $this->messageType = $messageType;
        $this->sid = $sid;
        $this->receiver = $receiver;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function getMediaUrl(): string
    {
        return $this->mediaUrl;
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function getSid(): string
    {
        return $this->sid;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            $data['Body'] ?? '',
            $data['From'] ?? '',
            $data['MediaUrl0'] ?? '',
            $data['MessageType'] ?? '',
            $data['MessageSid'] ?? '',
            $data['To'] ?? ''
        );
    }

    public function getReceiver(): string
    {
        return $this->receiver;
    }
}
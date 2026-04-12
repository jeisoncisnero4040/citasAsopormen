<?php
namespace App\Domain;
use App\Domain\MessageContent;
use App\Dtos\MessageDto;

class Message{
    private string $sender;
    private string $receiver;
    private MessageContent $content;
    private string $timestamp;
    private ?string $sid;
    private ?string $urlMedia;  

    public function __construct(string $sender, string $receiver, MessageContent   $content, string $timestamp, ?string $sid = null, ?string $urlMedia = null)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->content = $content;
        $this->timestamp = $timestamp;
        $this->sid = $sid;
        $this->urlMedia = $urlMedia;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            $data['sender'],
            $data['receiver'],
            MessageContent::fromContent($data['content']),
            $data['timestamp'],
            $data['sid'] ?? null,
            $data['urlMedia'] ?? null
        );
    }
    public static function fromDto(MessageDto $dto): self
    {
        return new self(
            $dto->getSender(),
            $dto->getReceiver(),
            MessageContent::fromContent($dto->getContent()),
            date('Y-m-d H:i:s'),
            $dto->getSid() ?: null,
            $dto->getMediaUrl() ?: null
        );
    }
    public static function fromApp(string $sender, string $receiver, MessageContent $content, ?string $sid = null, ?string $urlMedia = null): self
    {
        return new self(
            $sender,
            $receiver,
            $content,
            date('Y-m-d H:i:s'),
            $sid,
            $urlMedia
        );
    }
    public function getSender(): string
    {
        return $this->sender;
    }   
    public function getReceiver(): string
    {
        return $this->receiver;
    }
    public function getContent(): MessageContent    
    {
        return $this->content;  
    }
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }
    public function getSid(): string
    {        return $this->sid;
    }
    public function getUrlMedia(): ?string
    {        return $this->urlMedia;
    }   
    public function toArray(): array
    {
        return [
            'sender' => $this->sender,
            'receiver' => $this->receiver,
            'content' => $this->content->__toString(),
            'timestamp' => $this->timestamp,
            'sid' => $this->sid,
            'urlMedia' => $this->urlMedia,
        ];
    }
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
    public function setSid(string $sid): void
    {
        $this->sid = $sid;
    }
    public function setUrlMedia(string $urlMedia): void
    {
        $this->urlMedia = $urlMedia;    
    }


}


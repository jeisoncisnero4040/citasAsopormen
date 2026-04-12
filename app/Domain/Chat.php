<?php

namespace App\Domain;
use App\Enums\ChatStatusEnum;

class Chat
{
    private string $id;
    private string $telephoneNumber;
    private string $createdAt;
    private ChatStatusEnum $status;
    private ?string $clientName=null;
    private bool $identityVerified=false;
    private ?array $metadata=[];
    private ?array $errors=[];
    private ?string $resume=null;

    /** @var Message[] */
    private array $messages = [];

    public function __construct(
        string $id,
        string $telephoneNumber,
        string $createdAt,
        ChatStatusEnum $status,
        array $messages = [],
        ?string $clientName = null,
        bool $identityVerified = false,
        ?array $metadata = [],
        ?array $errors = [],
        ?string $resume = null
    ) {
        $this->id = $id;
        $this->telephoneNumber = $telephoneNumber;
        $this->createdAt = $createdAt;
        $this->status = $status;
        $this->messages = $messages;
        $this->clientName = $clientName;
        $this->identityVerified = $identityVerified;    
        $this->metadata = $metadata;
        $this->errors = $errors;
        $this->resume = $resume;
    }

    public function addMessage(Message $message): void
    {
        $this->messages[] = $message;
    }
    public function setClientName(string $name): void
    {
        $this->identityVerified = true;
        $this->clientName = $name;
    }
    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setIdentityVerified(bool $verified): void
    {
        $this->identityVerified = $verified;
    }
    public function setStatus(ChatStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function isIdentityVerified(): bool
    {
        return $this->identityVerified;
    }

    public function removeMessage(string $sid): void
    {
        $this->messages = array_filter(
            $this->messages,
            fn($m) => $m->getSid() !== $sid
        );
    }

    public function getMessages(): array
    {
        return $this->messages;
    }


    public function toContext(): array
    {
        return [
            'chat_id' => $this->id,
            'client_name' => $this->clientName,
            'identity_verified' => $this->identityVerified,
            'status' => $this->status->value,
            'resume' => $this->resume,
            'errors' => $this->errors,
            'metadata' => $this->metadata,
            'messages' => array_map(
                fn($m) => $m->toArray(),
                $this->messages
            )
        ];
    }
    public function toContextString(): string
    {
        return json_encode($this->toContext(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }
    public function getErrors(): array
    {        return $this->errors;
    }
    public function setResume(string $resume): void
    {   
        $this->resume = $resume;
        $this->messages = [];//para no hacer tan extensos los mensajes en el contexto, se limpia el array de mensajes al generar el resumen, ya que el resumen debe contener la información relevante de la conversación.; 
    }
    public  function setMessages(array $messages=[]): void
    {
        $this->messages = $messages;
    }
    
    public function getResume(): ?string
    {        return $this->resume;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'telephoneNumber' => $this->telephoneNumber,
            'createdAt' => $this->createdAt,
            'status' => $this->status->value,
            'clientName' => $this->clientName,
            'identityVerified' => $this->identityVerified,
            'metadata' => $this->metadata,
            'errors' => $this->errors,
            'resume' => $this->resume,
            'messages' => array_map(
                fn($m) => $m->toArray(),
                $this->messages
            )
        ];
    }
    public function toQueue(): array
    {
        return [
            'id' => $this->id,
            'telephoneNumber' => $this->telephoneNumber,
            'createdAt' => $this->createdAt
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTelephoneNumber(): string
    {
        return $this->telephoneNumber;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getStatus(): ChatStatusEnum
    {
        return $this->status;
    }
}
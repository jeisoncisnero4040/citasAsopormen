<?php

namespace App\Domain;

class IaResponse
{
    public function __construct(
        public  ?string $action,
        public  ?string $reason,
        public  ?string $message,
        public ?array $data = [],
        public  array $extra = []
    ) {}

    public static function fromString(string $response): self
    {
        $data = json_decode($response, true);


        if (!is_array($data)) {
            throw new \RuntimeException('Invalid AI response: not valid JSON');
        }
        return new self(
            action: $data['action'] ?? null,
            reason: $data['reason'] ?? null,
            message: $data['message'] ?? null,
            data: $data['data'] ?? null,
            extra: self::extractExtra($data)
        );
    }

    private static function extractExtra(array $data): array
    {
        $knownKeys = ['action', 'reason', 'message'];

        return array_diff_key($data, array_flip($knownKeys));
    }

    public function hasAction(): bool
    {
        return !empty($this->action);
    }

    public function isAction(string $action): bool
    {
        return $this->action === $action;
    }

    public function getIds(): array
    {
        return $this->extra['ids'] ?? [];
    }

    public function getDocumentNumber(): ?string
    {
        return $this->extra['document_number'] ?? null;
    }
    public function getData(): ?array
    {
        return $this->data ?? null;
    }

    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'reason' => $this->reason,
            'message' => $this->message,
            'data' => $this->data,
            ...$this->extra
        ];
    }
    public function message(): ?string
    {
        return $this->message ?? null;    
    }
    public function action(): ?string
    {
        return $this->action ?? null;    
    }
}
<?php

namespace App\Domain;

class MessageContent{
    private string $type; 
    private string|array $data; 

    public function __construct(string $type, string|array $data)
    {
        $this->type = $type;
        $this->data = $data;
    }
    public static function fromContent(string|array $content): self
    {
        if (is_string($content)) {
            return new self('text', $content);
        } elseif (is_array($content) && isset($content['type'], $content['data'])) {
            return new self($content['type'], $content['data']);
        }
        throw new \InvalidArgumentException('Invalid content format');
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getData(): string|array
    {
        return $this->data;
    }

    public function __toString(): string
    {
        if ($this->type === 'text') {
            return (string) $this->data;
        } elseif (in_array($this->type, ['image', 'video'])) {
            return "[{$this->type} content: " . json_encode($this->data) . "]";
        }
        return "[unknown content type]";
    }
}
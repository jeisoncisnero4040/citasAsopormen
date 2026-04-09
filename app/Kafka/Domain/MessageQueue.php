<?php

namespace App\Kafka\Domain;

use Illuminate\Support\Str;
use App\Kafka\Contracts\Idempotable;

class MessageQueue implements Idempotable
{
    private array $data = [];
    private array $meta = [];
    private array $headers = [];

    private string $topic;
    private string $event;
    private string $idempotencyKey;
    private string $timestamp;
    private int $version = 1;

    private ?string $key = null;

    public function __construct(string $topic, string $event)
    {
        $this->topic = $topic;
        $this->event = $event;
        $this->idempotencyKey = Str::uuid()->toString();
        $this->timestamp = now()->toISOString();

        $this->meta = [
            'service' => config('app.name'),
            'env' => config('app.env'),
        ];
    }
    public function getIdempotencyKey(): string
    {
        return $this->idempotencyKey;
    }

    public static function create(string $topic, string $event): self
    {
        return new self($topic, $event);
    }

    public function withData(array $data): self
    {
        $clone = clone $this;
        $clone->data = $data;
        return $clone;
    }

    public function withMeta(array $meta): self
    {
        $clone = clone $this;
        $clone->meta = array_merge($this->meta, $meta);
        return $clone;
    }

    public function withHeaders(array $headers): self
    {
        $clone = clone $this;
        $clone->headers = $headers;
        return $clone;
    }

    public function withKey(string $key): self
    {
        $clone = clone $this;
        $clone->key = $key;
        return $clone;
    }

    public function getKey(): string
    {
        return $this->key ?? $this->idempotencyKey;
    }

    public function getHeaders(): array
    {
        return array_merge([
            'event' => $this->event,
            'version' => $this->version,
            'idempotency_key' => $this->idempotencyKey,
        ], $this->headers);
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function toArray(): array
    {
        return [
            'event' => $this->event,
            'version' => $this->version,
            'idempotency_key' => $this->idempotencyKey,
            'timestamp' => $this->timestamp,
            'meta' => $this->meta,
            'headers' => $this->headers, 
            'data' => $this->data,
        ];
    }
}
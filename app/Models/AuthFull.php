<?php

namespace App\Models;

class AuthFull
{
    private ?array $authTrace = null;
    private ?array $ordersTrace = null;
    private ?array $apposTrace = null;

    private function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function withAuthTrace(array $trace): self
    {
        $this->authTrace = $trace;
        return $this;
    }

    public function withOrdersTrace(array $trace): self
    {
        $this->ordersTrace = $trace;
        return $this;
    }

    public function withApposTrace(array $trace): self
    {
        $this->apposTrace = $trace;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'auth'   => $this->authTrace,
            'orders' => $this->ordersTrace,
            'appos'  => $this->apposTrace,
        ];
    }
}
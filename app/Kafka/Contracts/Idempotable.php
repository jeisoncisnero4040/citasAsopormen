<?php

namespace App\Kafka\Contracts;

interface Idempotable
{
    public function getIdempotencyKey(): string;
}
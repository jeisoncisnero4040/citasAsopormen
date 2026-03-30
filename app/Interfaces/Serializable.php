<?php

namespace App\Interfaces;

interface Serializable
{
    public function toArray(): array;
}
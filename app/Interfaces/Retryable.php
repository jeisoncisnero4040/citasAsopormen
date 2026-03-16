<?php

namespace App\Interfaces;

interface Retryable{
    public function retryApply(callable $callback, int $retries = 3);
}
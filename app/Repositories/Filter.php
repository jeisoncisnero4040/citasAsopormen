<?php

namespace App\Repositories;

class Filter {
    private string $query;
    private array $bindings;
    public function __construct(
        string $query,
        array $bindings
    ) {
        $this->bindings=$bindings;
        $this->query= $query;
    }
    public function getQuery():string{
        return $this->query;
    }
    public function getBindings():array{
        return $this->bindings;
    }
}
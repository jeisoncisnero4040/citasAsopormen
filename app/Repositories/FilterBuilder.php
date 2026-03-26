<?php

namespace App\Repositories;

class FilterBuilder {
    private array $sentences = [];
    private array $bindings = [];

    public function __construct(array $sentences = [], array $bindings = [])
    {
        $this->sentences = $sentences;
        $this->bindings = $bindings;
    }
    public static function create(array $sentences = [], array $bindings = []):self{
        return new self($sentences ,$bindings );
    }
    

    public function add(string $sentence, mixed $value = null): self
    {
        $this->sentences[] = $sentence;

        if (!is_null($value)) {
            $this->bindings[] = $value;
        }

        return $this;
    }

    public function addRaw(string $sentence): self
    {
        $this->sentences[] = $sentence;
        return $this;
    }

    public function toFilter(): Filter
    {
        $sql = !empty($this->sentences)
            ? " AND " . implode(" AND ", $this->sentences)
            : "";

        return new Filter(
            query: $sql,
            bindings: $this->bindings
        );
    }
}
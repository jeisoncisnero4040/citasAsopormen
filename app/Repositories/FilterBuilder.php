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
        $sentence = $this->addPlaceholderSentence($sentence);
        $this->sentences[] = $sentence;

        if (!is_null($value)) {
            $this->bindings[] = $value;
        }

        return $this;
    }
    public function addComplexFilter(string $sentence, array $values): self
    {
        if(!str_starts_with($sentence,'(')){
            $sentence = '(' . $sentence;
        }
        if(!str_ends_with($sentence,')')){
            $sentence = $sentence . ')';   
        }
        $this->sentences[] = $sentence;
        $this->bindings = array_merge($this->bindings, $values);
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
    private function addPlaceholderSentence(string $sentence): string {
        $hasPlaceholder = str_contains($sentence, '?');
        if (!$hasPlaceholder) {
            $sentence .= ' = ?';
        }
        return $sentence;
    }
    public function addIn(string $field, array $values): self
    {
        if (empty($values)) {
            throw new \Exception('No se proporcionaron valores para el filtro IN.');
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->sentences[] = "($field IN ($placeholders))";
        $this->bindings    = array_merge($this->bindings, $values);

        return $this;
    }
}
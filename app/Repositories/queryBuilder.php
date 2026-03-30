<?php

namespace App\Repositories;

class QueryBuilder
{
    private string $baseQuery='';
    private string $filters = '';
    
    public function __construct(string $baseQuery= '',string $filters = '')
    {
        $this->baseQuery = $baseQuery;
        $this->filters = $filters;
    }
    public static function create(string $baseQuery = '',string $filters = ''): self
    {
        return new self($baseQuery, $filters);
    }
    public function withBaseQuery(string $baseQuery): self
    {
        $this->baseQuery = $baseQuery;
        return $this;
    }
    public function withFilters(string $filters): self
    {
        $this->filters = $filters;
        return $this;
    }
    public function toQuery(): string
    {
        if (empty($this->filters)) {
            return $this->baseQuery;
        }
        
        return str_replace('{{}}',$this->filters, $this->baseQuery);
    }
}
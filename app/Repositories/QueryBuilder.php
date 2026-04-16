<?php

namespace App\Repositories;
use App\Repositories\FilterBuilder;

class QueryBuilder
{
    private string $baseQuery='';
    private string $filters = '';
    private array $bindings = [];

    
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
    public function withBindings(array $bindings): self
    {
        $this->bindings = $bindings;
        return $this;
    }
    public function getBindings(): array
    {
        return $this->bindings;
    }
    public function withFilterBuilder(FilterBuilder $filtersbuilder): self
    {
        $this->filters= $filtersbuilder->toFilter()->getQuery();
        $this->bindings = $filtersbuilder->toFilter()->getBindings();
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
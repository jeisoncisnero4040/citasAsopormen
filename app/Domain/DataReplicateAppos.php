<?php

namespace App\Domain;

class DataReplicateAppos
{
    public function __construct(
        private ReplicateDisponibilityArray $replicateDisponibilityArray,
        private array $appos
    )
    {
    }

    public function getReplicateDisponibilityArray(): ReplicateDisponibilityArray
    {
        return $this->replicateDisponibilityArray;
    }

    public function getAppos(): array
    {
        return $this->appos;
    }
}
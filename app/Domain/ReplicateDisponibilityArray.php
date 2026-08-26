<?php

namespace App\Domain;
use App\Domain\ReplicateDispInfo;
use App\Domain\KeyAuthCup;

class ReplicateDisponibilityArray
{
    /**
     * @param ReplicateDispInfo[] $replicateDispInfos
     */
    public function __construct(private array $replicateDispInfos)
    {
    }
    public static function fromArray(array $data): self
    {
        $replicateDispInfos = array_map(fn($item) => ReplicateDispInfo::fromStdClass($item), $data);
        return new self($replicateDispInfos);
    }
    
    /**
     * @return ReplicateDispInfo[]
     */
    public function getReplicateDispInfos(): array
    {
        return $this->replicateDispInfos;
    }
    public function findByKey(KeyAuthCup $key): ?ReplicateDispInfo{
        $filtered = array_filter(
            $this->replicateDispInfos,
            fn(ReplicateDispInfo $item) => $item->getKeyAuthCup()->equals($key)
        );
        return $filtered ? array_values($filtered)[0] : null;
    }
    public function deleteByKey(KeyAuthCup $key): void
    {
        $this->replicateDispInfos = array_filter(
            $this->replicateDispInfos,
            fn(ReplicateDispInfo $item) => !$item->getKeyAuthCup()->equals($key)
        );
    }
    public function replace(ReplicateDispInfo $newItem): self{
        $this->replicateDispInfos = array_map(
            fn(ReplicateDispInfo $item) => $item->getKeyAuthCup()->equals($newItem->getKeyAuthCup()) ? $newItem : $item,
            $this->replicateDispInfos
        );
        return $this;
    }
}
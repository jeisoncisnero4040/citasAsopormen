<?php

namespace App\Domain;
use App\utils\ZerosPadder;


class Consecutive{   
    public function __construct(
        private string $consecutive,
        private  string $prefix)
    {}

    public function getConsecutive(): string
    {
        return $this->consecutive;
    }
    public function getPrefix(): string
    {
        return $this->prefix;
    }
    public function getNexConsecutive(): string
    {
        $nextConsecutive = ZerosPadder::increment($this->consecutive, 10);
        return $nextConsecutive;
    }
}
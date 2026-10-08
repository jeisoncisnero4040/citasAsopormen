<?php

namespace App\Files\Domain;


class SubRangeIndex{
    public function __construct(
        private int $firstPage,
        private int $lastPage,

    )
    {}
    public function getFirstPage():int{
        return $this->firstPage;
    }
    public function getLastPage():int{
        return $this->lastPage;
    }
}
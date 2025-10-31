<?php

namespace App\Models;

use stdClass;

class FeeModel{
    private float $value;

    public function __construct(stdClass $fee)
    {
        $this->value=$fee->valor;
    }
    public function getFee():float{
        return $this->value;
    }
}
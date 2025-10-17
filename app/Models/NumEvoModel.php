<?php

namespace App\Models;
use stdClass;

class NumEvoModel{
    private int $numEvo;

    public function __construct(stdClass $numEvo)
    {
        $this->numEvo=$numEvo->num_evo??0;
    }
    public function getNumEvo():int{
        return $this->numEvo;
    }
}
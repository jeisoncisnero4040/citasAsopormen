<?php

namespace App\Interfaces;

interface EvoRepositoryInterface{
    public function getPsicoEvoInRangeTime(string $historia,string $from ,string $to,string $procedipro):array;

}
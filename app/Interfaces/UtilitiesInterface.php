<?php

namespace App\Interfaces;

use App\Models\DxModel;

interface UtilitiesInterface {
    public function  getEvoUtilities():array;
    public function searchDx(string $param):array;
}
<?php 

namespace App\Interfaces;

interface Persistable{
    public function toPersistenceArray():array;
}
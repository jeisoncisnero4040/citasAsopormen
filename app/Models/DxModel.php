<?php 

namespace App\Models;

use stdClass;

class DxModel{
    private string $code;
    private string $disease;

    public function __construct(stdClass $dx)
    {
        $this->code=$dx->codigo;
        $this->disease=$dx->enfermedad;
    }
    public function getCode():string{
        return $this->code;
    }
    public function setCode(string $newCode):void{
        $this->code=$newCode;
    }
    public function getDisease():string{
        return $this->disease;
    }
    public function setDisease(string $newDisease):void{
        $this->disease=$newDisease;
    }
    public function toArray():array{
        return [
            'code'=>$this->code,
            'disease'=>$this->disease

        ];
    }
}
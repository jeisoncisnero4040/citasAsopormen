<?php

namespace App\Dtos;

use App\Requests\EvoRequest;


class GetEvoDto{
    private string $procedipro;
    private ?string $from;
    private ?string $to;
    private ?string $autoriz;
    private string $history;
    private string $profesional;
    private string $client;

    public function __construct(array $data)
    {
        self::valiadateDto(data:$data);
        $this->procedipro = $data['procedipro'];
        $this->from       = $data['from'] ?? null;
        $this->to         = $data['to'] ?? null;
        $this->autoriz    = $data['autoriz'] ?? null;
        $this->history    = $data['historia'];
        $this->client     = $data['client'];
        $this->profesional= $data['profesional'];



    }
    private function valiadateDto(array $data){
        EvoRequest::validateGetEvos(request:$data);
    }

    public function getAutoriz():?string{return $this->autoriz;}
    public function getFrom():?string{return $this->from;}
    public function getTo():?string{return $this->to;}
    public function getProcedipro():string{return $this->procedipro;}
    public function getHistory():string{return $this->history;}
    public function getProfesional():string{return $this->profesional;}
    public function getClient():string {return $this->client;}

    public function setFrom(string $from):void{$this->from=$from;}
    public function setTo(string $to):void{$this->to=$to;}

}
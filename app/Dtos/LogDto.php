<?php

namespace App\Dtos;

class LogDto
{
    private string $content;
    private string $date;
    private string $action;

    public function __construct(string $content = '', string $date = '', string $action ='evolucion')
    {
        $this->content = $content;
        $this->date = $date;
        $this->action = $action;;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
    public function getDate(): string
    {
        return $this->date;
    }
    public function setDate(string $date): void{
        $this->date = $date;
    }
    public function setAction(string $action):void{
        $this->action = $action;
    }
    public function getAction ():string{
        return $this->action;
    }


}

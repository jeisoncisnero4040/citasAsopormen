<?php

namespace App\Models;

class CapacitacionMediaViewModel{
    private int $id;
    private string $key;
    private ?string $urlSigned = null;
    private string $title;
    private string $description;

    public function __construct(
        int $id,
        string $key,

        string $title,
        string $description,
        ?string $urlSigned=null
    ) {
        $this->id = $id;
        $this->key = $key;

        $this->title = $title;
        $this->description = $description;
        $this->urlSigned = $urlSigned;
    }
    //getters
    public function getId(): int
    {
        return $this->id;   
    }
    public function getKey(): string
    {
        return $this->key;   
    }
    public function getUrlSigned(): ?string
    {
        return $this->urlSigned;
    }
    public function getTitle(): string
    {
        return $this->title;   
    }
    public function getDescription(): string
    {
        return $this->description;
    }


    public function setUrlSigned(?string $urlSigned): void
    {
        $this->urlSigned = $urlSigned;
    }

}
<?php

namespace App\Dtos;

class GetAuthsDto {
    public string $clientCode;
    public ?string $authCode;
    public bool $onlySchedulables;
    public bool $all;
    public ?string $from;
    public ?string $to;
    public bool $withFullInfo;
    public ?string $cupCode;
    
    
    public function __construct(
        string $clientCode,
        ?string $authCode = null,
        bool $onlySchedulables = false,
        bool $withFullInfo = false,
        ?string $from = null,
        ?string $to = null,
        ?string $cupCode=null,
    ){
        $this->clientCode = $clientCode;
        $this->authCode = $authCode;
        $this->onlySchedulables = $onlySchedulables;
        $this->from = $from;
        $this->to = $to;
        $this->withFullInfo = $withFullInfo;
        $this->cupCode=$cupCode;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            clientCode: $data['clientCode'] ?? '',
            authCode: $data['authCode'] ?? null,
            onlySchedulables: isset($data['onlySchedulables']) 
                ? filter_var($data['onlySchedulables'], FILTER_VALIDATE_BOOLEAN)
                : false,

            from: $data['from'] ?? null,
            to: $data['to'] ?? null,
            withFullInfo:isset($data['fullInfo']) 
                ? filter_var($data['fullInfo'], FILTER_VALIDATE_BOOLEAN)
                : false,
            cupCode:$data['cupCode']??null
        );
    }

    public function isOnlySchedulables(): bool
    {
        return $this->onlySchedulables;
    }

    public function hasDateRange(): bool
    {
        return !empty($this->from) || !empty($this->to);
    }
    public function hasFullInfo():bool{
        return $this->withFullInfo;
    }
    public function isByOrder(): bool
    {
        return !empty($this->authCode) && !empty($this->cupCode);
    }
    public function getClientCode(): string
    {
        return $this->clientCode;
    }

    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    public function getCupCode(): ?string
    {
        return $this->cupCode;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }
    public function hasUserCode():bool{
        return !empty($this->clientCode);
    }
    public function hasAuthCode(): bool
    {
        return !empty($this->authCode);
    }

}
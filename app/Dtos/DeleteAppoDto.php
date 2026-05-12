<?php

namespace App\Dtos;
use App\Models\UserRequesting;
class DeleteAppoDto
{
    private array $ids;
    private ?UserRequesting $userRequest;

    public function __construct(
        array $ids,
        ?UserRequesting $userRequest = null

    ) {
        $this->ids = $ids;
        $this->userRequest = $userRequest;
    }
    public static function fromRequest(array $query): self
    {
        return new self(
            ids: array_map('intval', explode(',', $query['ids']))
        );
    }
    public function setUserRequest(UserRequesting $userRequest): void
    {
        $this->userRequest = $userRequest;
    }
    public function getUserRequest(): ?UserRequesting
    {
        return $this->userRequest;
    }
    public function getIds(): array
    {
        return $this->ids;
    }




}

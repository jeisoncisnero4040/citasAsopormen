<?php

namespace App\Interfaces;

use App\Commands\AuthsDocumentsCommand;
use App\Dtos\GetAuthsDocumentsDto;
use App\Models\AuthsDocumentsViewModel;

interface AuthsDocumentsPort
{

    public function create(AuthsDocumentsCommand $item): int;
    /**
     * @param GetAuthsDocumentsDto $dto
     * @return AuthsDocumentsViewModel[]
     */
    public function get(GetAuthsDocumentsDto $dto): array;
    /**
     * @param GetAuthsDocumentsDto $dto
     * @return AuthsDocumentsCommand[]
     */
    public function getCommand(GetAuthsDocumentsDto $dto): array;

    /**
     * @return \StdClass[]
     */
    public function getUtility():array;

    public function delete(int $id): void;
}
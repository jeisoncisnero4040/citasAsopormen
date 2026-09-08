<?php

namespace App\Serializers;

use App\Commands\AgreementsCommand;
use App\Models\AgreementsViewModel;

class AgreementsSerializer
{
    public static function toPersistenceArray(AgreementsCommand $command): array
    {
        return [
            // ejemplo:
            // 'name' => $command->getName(),
            // 'sdt_created_at' => $command->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromArray(array $data): AgreementsCommand
    {
        return new AgreementsCommand(
            // mapear desde BD hacia el command
            // $data['name'] ?? null,
        );
    }

    public static function fromArrayToViewModel(array $data): AgreementsViewModel
    {
        return new AgreementsViewModel(
            $data['codigo'] ?? '',
            $data['eps_code'] ?? '',
            $data['nombre'] ?? '',
            $data['es_pbs'] ?? false,
        );
    }
    public static function fromPersistence(\stdClass $raw): AgreementsViewModel
    {
        return self::fromArrayToViewModel((array) $raw);
    }

    public static function serialize(AgreementsViewModel $model): array
    {
        return [
            'codigo' => $model->getCode(),
            'eps_code' => $model->getEpsCode(),
            'nombre' => $model->getName(),
            'es_pbs' => $model->isPbs(),
        ];
    }
}
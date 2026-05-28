<?php

namespace App\Serializers;

use App\Commands\CapacitacionMediaCommand;
use App\Models\CapacitacionMediaViewModel;

class CapacitacionMediaSerializer
{
    public static function toPersistenceArray(CapacitacionMediaCommand $command): array
    {
        return [
            // ejemplo:
            // 'name' => $command->getName(),
            // 'sdt_created_at' => $command->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromArray(array $data): CapacitacionMediaCommand
    {
        return new CapacitacionMediaCommand(
            // mapear desde BD hacia el command
            // $data['name'] ?? null,
        );
    }

    public static function fromArrayToViewModel(array $data): CapacitacionMediaViewModel
    {
        return new CapacitacionMediaViewModel(
            id: $data['id'] ?? 0,
            key: $data['url'] ?? '',
            title: $data['titulo'] ?? '',
            description: $data['descripcion'] ?? '',
            urlSigned: $data['url_signed'] ?? null
        );
    }

    public static function serialize(CapacitacionMediaViewModel $model): array
    {
        return [
            'id' => $model->getId(),
            'title' => $model->getTitle(),
            'description' => $model->getDescription(),
            'urlSigned' => $model->getUrlSigned(),
        ];
    }
}
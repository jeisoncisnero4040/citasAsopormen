<?php

namespace App\Mappers;

use App\Interfaces\ArrayToTreeMapperInterface;
use App\Exceptions\CustomExceptions\ServerErrorException;

class ReasonsPqrMapper implements ArrayToTreeMapperInterface
{
    public static function mapArray(mixed $data): array
    {
        if (!is_array($data)) {
            throw new ServerErrorException("Los datos proporcionados no son una lista válida", 500);
        }

        $items = [];
        $tree = [];

        // Inicializamos cada ítem con propiedad `children`
        foreach ($data as $item) {
            if (!isset($item->id_motivo)) {
                throw new ServerErrorException("Falta el campo 'id_motivo' en uno de los elementos", 500);
            }

            $item->children = [];
            $items[$item->id_motivo] = $item;
        }

        foreach ($data as $item) {
            if (!is_null($item->id_padre) && isset($items[$item->id_padre])) {
                $items[$item->id_padre]->children[] = $item;
            } else {
                $tree[] = $item;
            }
        }

        return $tree;
    }
}

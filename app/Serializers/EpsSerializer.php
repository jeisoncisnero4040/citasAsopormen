<?php
namespace App\Serializers;

use App\Domain\Eps;
use App\Domain\Code;

class EpsSerializer
{
    public static function serialize(Eps $eps): array
    {
        return [
            'nombre' => $eps->getName(),
            'codigo' => $eps->getCode()->getCode(),
            'nit_cli' => $eps->getNit()->getCode(),
        ];
    }
    public static function fromArray(array $data): Eps
    {
        return new Eps(
            name:$data['nombre'],
            code:Code::fromString($data['codigo']),
            nit:Code::fromString($data['nit_cli'])
        );
    }
    public static function fromPersistence(\StdClass $row): Eps
    {
        return new Eps(
            name:$row->nombre,
            code:Code::fromString($row->codigo),
            nit:Code::fromString($row->nit_cli)
        );
    }
}
<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Interfaces\TarifePort;
use App\Models\Tarife;
use App\Models\ClientView;


class TarifeService{
    private TarifePort $tarifeRepository;
    public function __construct(TarifePort $tarifeRepository)
    {
        $this->tarifeRepository = $tarifeRepository;
    }
    public function getTarifeByCode(string $code): ?Tarife
    {


        return null; // Retorna null si no se encuentra la tarifa
    }
    public function getTarifeByClient(ClientView $clientView): Tarife
    {
        $tarifes = $this->tarifeRepository->getTarifeByEpsAndCovenant($clientView->getEpsCode(), $clientView->getCovenantCode());
        if(empty($tarifes)){
            throw new NotFoundException(message: 'No se encontró una tarifa para la eps en la que el cliente está registrado', status: 404);
        }
        if (count($tarifes) > 1) {
            throw new BadRequestException(message: 'Se encontraron múltiples tarifas para la eps y convenio del cliente. Se esperaba solo una.', status: 400);
        }
        return $tarifes[0]; 
    }
}
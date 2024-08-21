<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Models\CentralOfficeModel;
use App\utils\ResponseManager;

class CentralOfficeService
{
    private $centralOfficeModel;
    private $responseManager;

    public function __construct(CentralOfficeModel $centralOfficeModel, ResponseManager $responseManager)
    {
        $this->centralOfficeModel = $centralOfficeModel;
        $this->responseManager = $responseManager;
    }

    public function getAllCentralsOffice()
    {
        $allOffices = $this->getCentralsOffice();
        return $this->responseManager->success($allOffices);
    }

    private function getCentralsOffice()
    {
        $offices = $this->centralOfficeModel::select('cod', 'nombre', 'direccion')->get();

        if ($offices->isEmpty()) {
            throw new NotFoundException('No se encontraron sedes', 404);
        }

        return $offices;
    }
}

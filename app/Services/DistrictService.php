<?php


namespace App\Services;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Repositories\DistrictRepository;

class DistrictService{
    private DistrictRepository $repo;
    public function __construct(DistrictRepository $repo)
    {
        $this->repo=$repo;
    }

    public function get(string $code){
        $district= $this->repo->getByCode(code:$code);
        if(empty($district)){
            throw new NotFoundException("No se han encontrado municipios validos con el codigo proporcionaldo",404);
        }
        return $district[0];
    }
}
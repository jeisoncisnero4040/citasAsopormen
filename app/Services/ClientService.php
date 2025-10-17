<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Interfaces\ClientRepositoryInterface;
use App\Utils\ResponseManager;


class ClientService{
    private ResponseManager $responseManager;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ResponseManager $responseManager,ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository=$clientRepository;
        $this->responseManager=$responseManager;
    }
    public function searchClientByBame(array $request){
        $param=$request['param'];
        $clients=$this->clientRepository->searchClient(params:strtoupper($request['param']));
        if(empty($clients)){
            throw new NotFoundException("No se han encontrado clientes que coincidan con {$param}",404);
        }
        return $this->responseManager->success(
            $clients
        );
    }
    public function findClient(string $code,array $request): array
    {
        $profesionalCedula=$request['cedula'];
        $client = $this->clientRepository->getClienByCode(codigo: $code,profesional:$profesionalCedula);

        $procedipros = $this->clientRepository->getProceduresClient(codigo: $code);

        $plainProcedipros = collect($procedipros)
            ->map(fn($pro) => $pro->procedipro)
            ->toArray();

        $client->setProcedipros(procedipros: $plainProcedipros);

        return $this->responseManager->success($client->toArray());
    }
}
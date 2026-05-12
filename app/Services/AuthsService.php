<?php

namespace App\Services;

use App\Dtos\GetAuthsDto;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Interfaces\AuthsInterface;
use App\Models\Auth;
use App\Models\AuthFull;
use App\Dtos\CreateAuthDto;
use App\Models\UserRequesting;
use App\Builders\AuthBuilder;
use App\Dtos\DeleteAuthsDto;
use App\Dtos\UpdateAuthsDto;
use App\Services\QueueService;
use App\Commands\AuthCommand;

class AuthsService extends BaseService{
    private AuthsInterface $authsRepository;
    private ClientService $clientService;
    private TarifeService $tarifeService;
    protected QueueService $queueService;   

    public  function __construct(AuthsInterface $authsRepository,
                                ClientService $clientService,
                                TarifeService $tarifeService,
                                QueueService $queueService

    ) {
        parent::__construct($queueService);
        $this->authsRepository = $authsRepository;
        $this->clientService = $clientService;  
        $this->tarifeService = $tarifeService;
        $this->queueService = $queueService;
    }
    /**
     * @return array<array<string, mixed>>
     */
    public function Create(CreateAuthDto $dto, UserRequesting $userRequesting): array{
        $client = $this->clientService->getByClientCode($dto->getClientCode());
        $tarife= $this->tarifeService->getTarifeByClient($client);
        $authsBuilder=AuthBuilder::create()
            ->withCreateAuthDto($dto)
            ->withClientView($client)
            ->withUserRequesting($userRequesting)
            ->withTarife($tarife);

        $authCommands = $authsBuilder->buildMany($dto->getProcedures());
        $exampleAuthCommand = $authCommands[0];
        $newIds = $this->authsRepository->saveMany($authCommands);
        $msm = $exampleAuthCommand->getMsmCreate(ids : $newIds);
        
        $newAuths = $this->authsRepository->getByIds($newIds);
        $this->dispatchToQueue($msm, $userRequesting);
        return $this->attachSpecialtiesWithoutCollapsing($newAuths);
    }
    public function get(GetAuthsDto $dto): array
    {
        $auths = $this->authsRepository->get(dto: $dto);
        if(empty($auths)){
            throw new NotFoundException("No se han encontrado Autorizaciones",404);
        }
        $isByAutCodeRequest = $dto->hasAuthCode();
        $user = null;
        if ($isByAutCodeRequest) {

            $usersCode = collect($auths)
                ->map(fn($auth) => $auth->getCodeClient())
                ->unique()
                ->values()
                ->toArray();

            if (count($usersCode) > 1) {
                throw new BadRequestException(
                    "No se ha podido completar esta accion porque se ha encontrado mas de un usuario inscrito a este numero de autorizacion",
                    400
                );
            }

            if (empty($usersCode)) {
                throw new NotFoundException(
                    "No se encontró usuario para esta autorización",
                    404
                );
            }

            $user = $this->clientService->getDataClientByHistoryId([
                'historyId' => $usersCode[0]
            ]);
        }

        $group = $this->attachSpecialtiesWithoutCollapsing(authorizations: $auths);

        return [
            'client' => $user,
            'auths' => $group
        ];
    }
    public function getAuthDetail(GetAuthsDto $dto): Array
    {
        if (empty($dto->getAuthCode()) || empty($dto->getClientCode())) {
            throw new BadRequestException(
                "El codigo de autorizacion y el cliente es obligatorio",
                400
            );
        }


        $authTrace = $this->authsRepository->getDetailAuth($dto);
        $ordersTrace = $this->authsRepository->getDetailsOrders($dto);
        $apposTrace = $this->authsRepository->getDetailApposAppos($dto);

        return AuthFull::create()
            ->withAuthTrace($authTrace)
            ->withOrdersTrace($ordersTrace)
            ->withApposTrace($apposTrace)
            ->toArray();
    }
    public function updateAuths(UpdateAuthsDto $dto, UserRequesting $userRequesting): array
    {   
        if(empty($dto->getNro())) {
            throw new BadRequestException("El número de autorización es obligatorio para actualizar",400);
        }
        $dtoGet = GetAuthsDto::fromArray(['nro' => $dto->getNro()]);
        $authsToUpdate = $this->authsRepository->getCommand($dtoGet);
        if(empty($authsToUpdate)){
            throw new NotFoundException("No se han encontrado Autorizaciones para actualizar",404);
        }
        $curentAuthCode = $authsToUpdate[0]->getAuthCode();

        logger()->info("Data to update auths", ['data' => $dto->getAuthCode(), 'from' => $dto->getFrom(), 'to' => $dto->getTo(), 'numberDays' => $dto->getNumberDays(), 'clientCode' => $dto->getClientCode(), 'senderCode' => $dto->getSenderCode()]);
        $authsUpdated = collect($authsToUpdate)
            ->map(function (AuthCommand $auth) use ($dto) {
                $auth->update($dto);
                return $auth;
            })
            ->toArray();
        logger()->info("Auths updated", ['auths' => array_map(fn($auth) =>  $auth->getAuthCode(), $authsUpdated)]);
        $this->authsRepository->updateMany($authsUpdated, $curentAuthCode);
        $msm = "El usuario {$userRequesting->getUsername()} ha actualizado la autorización con numero de autorizacion {$curentAuthCode} el dia ". date("Y-m-d H:i:s");
        $this->dispatchToQueue($msm, $userRequesting);
        return $this->get($dtoGet);
              
       
    }
    public function deleteAuths(DeleteAuthsDto $dto, UserRequesting $userRequesting): void
    {
        $getDto = GetAuthsDto::fromArray([
            'authCode' => $dto->getAuthCode(),
            'clientCode' => $dto->getClientCode(),
            'nro' => $dto->getNro(),
            'id' => $dto->getId()
        ]);
        $auths = $this->authsRepository->get(dto: $getDto);
        if(empty($auths)){
            throw new NotFoundException("No se han encontrado Autorizaciones para eliminar",404);
        }
        $idsApposDeleted =$this->authsRepository->delete($auths);
        $msm = $auths[0]->deleteLog($idsApposDeleted, $userRequesting);
        $this->dispatchToQueue($msm, $userRequesting);

    }

    /**
     * @param array<Auth> $authorizations
     */
    private function attachSpecialtiesWithoutCollapsing(array $authorizations): array
    {

        $specialtiesByAuth = collect($authorizations)
            ->groupBy(fn(Auth $item) => $item->getAutoriza())
            ->map(function ($items) {
                return $items
                    ->map(fn(Auth $item) => $item->getSpecialty())
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            });

        return collect($authorizations)
            ->map(function (Auth $item) use ($specialtiesByAuth) {
                $item->setSpecialties(
                    $specialtiesByAuth[$item->getAutoriza()] ?? []
                );
                return $item->toArray();
            })
            ->all();
    }

}
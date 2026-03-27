<?php

namespace App\Services;

use App\Dtos\GetAuthsDto;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Interfaces\AuthsInterface;
use App\Models\Auth;
use App\Models\AuthFull;
use GuzzleHttp\Client;

class AuthsService{
    private AuthsInterface $authsRepository;
    private ClientService $clientService;

    public  function __construct(AuthsInterface $authsRepository,
                                ClientService $clientService
    ) {
        $this->authsRepository = $authsRepository;
        $this->clientService = $clientService;
    }
    public function get(GetAuthsDto $dto): array
    {
        $auths = $this->authsRepository->get(dto: $dto);
        if(empty($auths)){
            throw new NotFoundException("No se han encontrado Autorizaciones",404);
        }

        $isByAutCodeRequest = !$dto->hasUserCode() && $dto->hasAuthCode();

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

    /**
     * @param autorizations array<Auth>
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
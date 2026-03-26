<?php

namespace App\Services;

use App\Dtos\GetAuthsDto;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Interfaces\AuthsInterface;
use App\Models\Auth;
use App\Models\AuthFull;

class AuthsService{
    private AuthsInterface $authsRepository;

    public  function __construct(AuthsInterface $authsRepository) {
        $this->authsRepository = $authsRepository;
    }
    public function get(GetAuthsDto $dto){


        $auths = $this->authsRepository->get(dto:$dto);
        return $this->attachSpecialtiesWithoutCollapsing(authorizations:$auths);
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
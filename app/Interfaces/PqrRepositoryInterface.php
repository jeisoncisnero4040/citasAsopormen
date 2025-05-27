<?php
namespace App\Interfaces;

interface PqrRepositoryInterface {
    public function create(array $newPqrData): mixed;
    public function find(int $pqrId,?string $estado=null):mixed;
    public function getPqrs(?string $estado=null):mixed;
    public function getActionsPqrs(int $idPqrs):mixed;
    public function remitPqrsToArea(int $idPqrs): mixed;
    public function saveAnswerAreaPqrs(array $request,array $actionsWithUrls,int $id):mixed;
    public function changueAreaPqrs(int $pqrsId,array $newAreaData):mixed;
    public function saveAnswerToUser(int $pqrsId,array $request):mixed;
    public function closePqrs(int $pqrsId):mixed;
}

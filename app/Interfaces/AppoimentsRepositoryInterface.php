<?php
namespace App\Interfaces;

use App\Dtos\ABAEvoDto;
use App\Dtos\BasicEvoDto;
use App\Dtos\PsicoEvoDto;
use App\Models\AppoimentInfoModel;
use App\Models\DisponilityEvoModel;
use App\Models\DxHistoryModel;
use App\Models\NumEvoModel;

interface AppoimentsRepositoryInterface{
    public function getDailyAppoimetsProfesionalByIdentity(string $identityNumber,bool $toModel=false):array;
    public function getScheduleProfesionalInRangeTime(string $identityNumber,bool $toModel,string $from,string $to):array;
    public function cancelAppoiment(string $ids, string $meanCancel, string $dateAppoiment, string $razon):array;
    public function getAppoimentsById(array $ids,bool $toModel=false ):array;
    public function getIdsAppoimentsByAutoriz(string $autoriz,string $cedulaProfesional,string $codEnt,string $history,string $procedure):array;
    public function getInfoFacAppoiment(int $id):AppoimentInfoModel;
    public function evoFono(BasicEvoDto $evo,DisponilityEvoModel $dispo,array $ids,string|null $idHistoricoDx):void;
    public function evoPsico(PsicoEvoDto $evo,DisponilityEvoModel $dispo,array $ids,string|null $idHistoricoDx):void;
    public function evoABA(ABAEvoDto $evo,DisponilityEvoModel $dispo,array $ids,string|null $idHistoricoDx):void;
    public function getDisponibilityAppo(int $id):DisponilityEvoModel;
    public function getDxHistoryByAppoId(int $id):DxHistoryModel|null;
    public function getNumEvoPsicologyByHistory(string $history):NumEvoModel;
    public function openPastApposByIds(array $ids):void;
    public function getAutorizAvailablesToChangeByAppoId(int $id):array;
    public function updateAppoById(int $id,array $data):int;
}
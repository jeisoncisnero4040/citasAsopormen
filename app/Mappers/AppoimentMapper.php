<?php

namespace App\Mappers;

use App\Dtos\ABAEvoDto;
use App\Dtos\BasicEvoDto;
use App\Dtos\PsicoEvoDto;
use App\Interfaces\Evolucionable;
use App\Models\AppoimentInfoModel;
use App\Models\NumEvoModel;
use App\Models\ProcediproModel;


class AppoimentMapper implements Evolucionable{
    public static function evoInDtoToEVO(BasicEvoDto $evoDto, 
                                        AppoimentInfoModel $appoInfo, 
                                        ProcediproModel $procedipro,
                                        string $now,
                                        ): BasicEvoDto
    {
        
        $evoDto->setCentral($appoInfo->getSede());
        $evoDto->setUserHistory($appoInfo->getHistory());
        $evoDto->setEpsCode($appoInfo->getCodEps());
        $evoDto->setAuthCode($appoInfo->getAutoriz());
        $evoDto->setCovenat($appoInfo->getConvenio());
        $evoDto->setProcedim($appoInfo->getTiempo());
        $evoDto->setRegistro($appoInfo->getUserRegistro());
        $evoDto->setRegistroCed($appoInfo->getCedulaRegistro());
        $evoDto->setRegistroDate($appoInfo->getSaveDate());
        $evoDto->setCostCenter($appoInfo->getCostCenter());
        $evoDto->setTarife($appoInfo->getTarifa());
        $evoDto->setValue($appoInfo->getPrecio());
        $evoDto->setSpecialty($appoInfo->getAccountBank());
        $evoDto->setClient($appoInfo->getClient());
        $evoDto->setClientCedula($appoInfo->getCedulaClient());
        $evoDto->setProcedimiento($appoInfo->getProcedimiento());
        

        $evoDto->setProcedipro($procedipro->getNombre());
        $evoDto->setTipoEvo($procedipro->getTypeEvo());
        $evoDto->setIsPelvic($procedipro->isPelvic());
        $evoDto->setIsHidric($procedipro->isHidric());
        $evoDto->setIsFisio($procedipro->isFisio());
        $evoDto->setTipoAppo($procedipro->getTypeAppo());

        $evoDto->setNow($now);
        return $evoDto;
    }
    public static function evoPsicoEvoInDtoToEvo(PsicoEvoDto $evoDto,
                                                 AppoimentInfoModel $appoInfo, 
                                                 ProcediproModel $procedipro,
                                                  NumEvoModel $numEvoModel, 
                                                  string $now): PsicoEvoDto
    {
        $evoDto->setCentral($appoInfo->getSede());
        $evoDto->setUserHistory($appoInfo->getHistory());
        $evoDto->setEpsCode($appoInfo->getCodEps());
        $evoDto->setAuthCode($appoInfo->getAutoriz());
        $evoDto->setCovenat($appoInfo->getConvenio());
        $evoDto->setProcedim($appoInfo->getTiempo());
        $evoDto->setRegistro($appoInfo->getUserRegistro());
        $evoDto->setRegistroCed($appoInfo->getCedulaRegistro());
        $evoDto->setRegistroDate($appoInfo->getSaveDate());
        $evoDto->setCostCenter($appoInfo->getCostCenter());
        $evoDto->setTarife($appoInfo->getTarifa());
        $evoDto->setValue($appoInfo->getPrecio());
        $evoDto->setSpecialty($appoInfo->getAccountBank());
        $evoDto->setClient($appoInfo->getClient());
        $evoDto->setClientCedula($appoInfo->getCedulaClient());
        $evoDto->setProcedimiento($appoInfo->getProcedimiento());
        

        $evoDto->setProcedipro($procedipro->getNombre());
        $evoDto->setTipoEvo($procedipro->getTypeEvo());
        $evoDto->setTipoAppo($procedipro->getTypeAppo());

        $evoDto->setNow($now);

        $numEvo=$numEvoModel->getNumEvo();
        $nexEvo=$numEvo+1;
        $evoDto->setNUmEvo($nexEvo);
        $evoDto->setAdmisionType($appoInfo->getTypeAdmision());
        return $evoDto;
    }
    public static function evoABAEvoInDtoToEvo(ABAEvoDto $evoDto,
                                                 AppoimentInfoModel $appoInfo, 
                                                 ProcediproModel $procedipro,
                                                  NumEvoModel $numEvoModel, 
                                                  string $now): ABAEvoDto
    {
        $evoDto->setCentral($appoInfo->getSede());
        $evoDto->setUserHistory($appoInfo->getHistory());
        $evoDto->setEpsCode($appoInfo->getCodEps());
        $evoDto->setAuthCode($appoInfo->getAutoriz());
        $evoDto->setCovenat($appoInfo->getConvenio());
        $evoDto->setProcedim($appoInfo->getTiempo());
        $evoDto->setRegistro($appoInfo->getUserRegistro());
        $evoDto->setRegistroCed($appoInfo->getCedulaRegistro());
        $evoDto->setRegistroDate($appoInfo->getSaveDate());
        $evoDto->setCostCenter($appoInfo->getCostCenter());
        $evoDto->setTarife($appoInfo->getTarifa());
        $evoDto->setValue($appoInfo->getPrecio());
        $evoDto->setSpecialty($appoInfo->getAccountBank());
        $evoDto->setClient($appoInfo->getClient());
        $evoDto->setClientCedula($appoInfo->getCedulaClient());
        $evoDto->setProcedimiento($appoInfo->getProcedimiento());
        

        $evoDto->setProcedipro($procedipro->getNombre());
        $evoDto->setTipoEvo($procedipro->getTypeEvo());
        $evoDto->setTipoAppo($procedipro->getTypeAppo());

        $evoDto->setNow($now);

        $numEvo=$numEvoModel->getNumEvo();
        $nexEvo=$numEvo+1;
        $evoDto->setNUmEvo($nexEvo);
        $evoDto->setAdmisionType($appoInfo->getTypeAdmision());
        return $evoDto;
    }
}